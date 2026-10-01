<?php
/**
 * admin/includes/storage.php
 * ─────────────────────────────────────────────────────────────────────────────
 * Unified storage layer supporting both JSON and MySQL backends.
 * Switch the driver in config.php: STORAGE_DRIVER = 'json' | 'mysql'
 * ─────────────────────────────────────────────────────────────────────────────
 */

if (!defined('ADMIN_GUARD')) die('Direct access not permitted.');


// ════════════════════════════════════════════════════════════════════════════
// PUBLIC API — same interface regardless of backend
// ════════════════════════════════════════════════════════════════════════════

/**
 * Record a completed scan. Called from detect.php after successful analysis.
 *
 * @param string $url       Scanned URL
 * @param array  $results   Detected tech (grouped by category)
 * @param string $ip        Visitor IP
 */
function storage_save_scan(string $url, array $results, string $ip): void
{
    $geo  = geoip_lookup($ip);
    $flat = flatten_results($results);

    $record = [
        'url'          => $url,
        'cms'          => $flat['cms']       ?? '',
        'framework'    => $flat['framework'] ?? '',
        'language'     => $flat['language']  ?? '',
        'plugins'      => $flat['plugins']   ?? '',
        'server'       => $flat['server']    ?? '',
        'ip'           => $ip,
        'country'      => $geo['country']      ?? '',
        'country_code' => $geo['country_code'] ?? '',
        'city'         => $geo['city']         ?? '',
        'scanned_at'   => time(),
        'timestamp'    => time(),
    ];

    if (STORAGE_DRIVER === 'mysql') {
        mysql_save_scan($record);
    } else {
        json_save_scan($record);
    }

    storage_increment_stats($ip);
}

/**
 * Retrieve paginated scan records, newest first.
 *
 * @param int $page    1-based page number
 * @param int $limit   Records per page
 * @param string $search  Optional URL/CMS filter
 * @return array  ['records' => [...], 'total' => int]
 */
function storage_get_scans(int $page = 1, int $limit = RECORDS_PER_PAGE, string $search = ''): array
{
    if (STORAGE_DRIVER === 'mysql') {
        return mysql_get_scans($page, $limit, $search);
    }
    return json_get_scans($page, $limit, $search);
}

/**
 * Delete a scan record by ID.
 */
function storage_delete_scan(string $id): bool
{
    if (STORAGE_DRIVER === 'mysql') {
        return mysql_delete_scan($id);
    }
    return json_delete_scan($id);
}

/**
 * Get aggregated dashboard statistics.
 */
function storage_get_stats(): array
{
    if (STORAGE_DRIVER === 'mysql') {
        return mysql_get_stats();
    }
    return json_get_stats();
}


// ════════════════════════════════════════════════════════════════════════════
// JSON DRIVER
// ════════════════════════════════════════════════════════════════════════════

function json_load_all(): array
{
    if (!file_exists(SCANS_JSON_FILE)) return [];
    $data = @file_get_contents(SCANS_JSON_FILE);
    return $data ? (json_decode($data, true) ?? []) : [];
}

function json_save_all(array $records): void
{
    @file_put_contents(SCANS_JSON_FILE, json_encode($records, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
}

function json_save_scan(array $record): void
{
    $records    = json_load_all();
    $record['id'] = uniqid('s_', true);
    array_unshift($records, $record);

    // Keep max 500 records in JSON mode
    if (count($records) > 500) {
        $records = array_slice($records, 0, 500);
    }
    json_save_all($records);
}

function json_get_scans(int $page, int $limit, string $search): array
{
    $all = json_load_all();

    // Filter
    if ($search !== '') {
        $s   = strtolower($search);
        $all = array_filter($all, function ($r) use ($s) {
            return strpos(strtolower($r['url'] ?? ''), $s) !== false ||
                   strpos(strtolower($r['cms'] ?? ''), $s) !== false ||
                   strpos(strtolower($r['framework'] ?? ''), $s) !== false ||
                   strpos(strtolower($r['country'] ?? ''), $s) !== false;
        });
        $all = array_values($all);
    }

    $total   = count($all);
    $offset  = ($page - 1) * $limit;
    $records = array_slice($all, $offset, $limit);

    return ['records' => $records, 'total' => $total];
}

function json_delete_scan(string $id): bool
{
    $records = json_load_all();
    $before  = count($records);
    $records = array_values(array_filter($records, fn($r) => ($r['id'] ?? '') !== $id));
    json_save_all($records);
    return count($records) < $before;
}

function json_get_stats(): array
{
    $records = json_load_all();
    $stats   = load_stats_file();

    // CMS distribution
    $cms_dist = [];
    $fw_dist  = [];
    $daily    = [];
    $countries= [];

    foreach ($records as $r) {
        $cms = trim($r['cms'] ?? '');
        if ($cms) $cms_dist[$cms] = ($cms_dist[$cms] ?? 0) + 1;

        $fw  = trim($r['framework'] ?? '');
        if ($fw)  $fw_dist[$fw]  = ($fw_dist[$fw]  ?? 0) + 1;

        $day = date('Y-m-d', $r['timestamp'] ?? time());
        $daily[$day] = ($daily[$day] ?? 0) + 1;

        $cc  = $r['country'] ?? '';
        if ($cc) $countries[$cc] = ($countries[$cc] ?? 0) + 1;
    }

    arsort($cms_dist);
    arsort($fw_dist);
    arsort($countries);
    krsort($daily);

    return [
        'total_scans'    => count($records),
        'unique_ips'     => $stats['unique_ips'] ?? 0,
        'today_scans'    => $daily[date('Y-m-d')] ?? 0,
        'cms_dist'       => array_slice($cms_dist, 0, 8, true),
        'framework_dist' => array_slice($fw_dist,  0, 8, true),
        'daily_scans'    => array_slice($daily, 0, 14, true),
        'countries'      => array_slice($countries, 0, 5, true),
        'recent'         => array_slice($records, 0, 10),
    ];
}


// ════════════════════════════════════════════════════════════════════════════
// MYSQL DRIVER
// ════════════════════════════════════════════════════════════════════════════

/**
 * Returns a PDO connection or null if unavailable.
 * Never throws — all callers must handle null.
 */
function mysql_connect_pdo(): ?PDO
{
    static $pdo       = null;
    static $failed    = false;
    if ($failed)      return null;
    if ($pdo !== null) return $pdo;

    try {
        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s',
            DB_HOST, DB_PORT, DB_NAME, DB_CHARSET);
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_TIMEOUT            => 5,
        ]);
        return $pdo;
    } catch (Exception $e) {
        $failed = true;
        $pdo    = null;
        return null;
    }
}

/**
 * Return true if MySQL is available, false otherwise.
 */
function mysql_available(): bool
{
    return mysql_connect_pdo() !== null;
}

function mysql_ensure_table(): bool
{
    $pdo = mysql_connect_pdo();
    if ($pdo === null) return false;
    try {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `scans` (
              `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
              `url`          VARCHAR(2048) NOT NULL,
              `cms`          VARCHAR(100)  NOT NULL DEFAULT '',
              `framework`    VARCHAR(100)  NOT NULL DEFAULT '',
              `language`     VARCHAR(100)  NOT NULL DEFAULT '',
              `plugins`      VARCHAR(500)  NOT NULL DEFAULT '',
              `server`       VARCHAR(100)  NOT NULL DEFAULT '',
              `ip`           VARCHAR(45)   NOT NULL DEFAULT '',
              `country`      VARCHAR(100)  NOT NULL DEFAULT '',
              `country_code` VARCHAR(5)    NOT NULL DEFAULT '',
              `city`         VARCHAR(100)  NOT NULL DEFAULT '',
              `scanned_at`   INT UNSIGNED  NOT NULL,
              INDEX (`scanned_at`),
              INDEX (`ip`),
              INDEX (`cms`),
              INDEX (`framework`),
              INDEX (`country_code`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
        return true;
    } catch (Exception $e) {
        error_log('StackDetect mysql_ensure_table error: ' . $e->getMessage());
        return false;
    }
}

function mysql_save_scan(array $record): bool
{
    if (!mysql_ensure_table()) {
        error_log('StackDetect: mysql_ensure_table() failed — check DB credentials in config.php');
        return false;
    }
    $pdo = mysql_connect_pdo();
    if ($pdo === null) {
        error_log('StackDetect: mysql_connect_pdo() returned null — DB unreachable');
        return false;
    }
    try {
        $stmt = $pdo->prepare("
            INSERT INTO `scans`
              (url, cms, framework, language, plugins, server, ip, country, country_code, city, scanned_at)
            VALUES
              (:url,:cms,:framework,:language,:plugins,:server,:ip,:country,:country_code,:city,:scanned_at)
        ");
        $params = [
            ':url'          => $record['url'],
            ':cms'          => $record['cms'],
            ':framework'    => $record['framework'],
            ':language'     => $record['language'],
            ':plugins'      => $record['plugins'],
            ':server'       => $record['server'],
            ':ip'           => $record['ip'],
            ':country'      => $record['country'],
            ':country_code' => $record['country_code'],
            ':city'         => $record['city'],
            ':scanned_at'   => $record['scanned_at'],
        ];
        $stmt->execute($params);
        return true;
    } catch (Exception $e) {
        error_log('StackDetect mysql_save_scan error: ' . $e->getMessage());
        return false;
    }
}

function mysql_get_scans(int $page, int $limit, string $search): array
{
    if (!mysql_ensure_table()) return ['records' => [], 'total' => 0];
    $pdo = mysql_connect_pdo();
    if ($pdo === null) return ['records' => [], 'total' => 0];

    $offset = ($page - 1) * $limit;
    try {
        if ($search !== '') {
            $like  = '%' . $search . '%';
            $total = $pdo->prepare("SELECT COUNT(*) FROM `scans` WHERE url LIKE ? OR cms LIKE ? OR framework LIKE ? OR country LIKE ?");
            $total->execute([$like, $like, $like, $like]);
            $count = (int) $total->fetchColumn();

            $stmt = $pdo->prepare("SELECT * FROM `scans` WHERE url LIKE ? OR cms LIKE ? OR framework LIKE ? OR country LIKE ? ORDER BY scanned_at DESC LIMIT ? OFFSET ?");
            $stmt->execute([$like, $like, $like, $like, $limit, $offset]);
        } else {
            $count = (int) $pdo->query("SELECT COUNT(*) FROM `scans`")->fetchColumn();
            $stmt  = $pdo->prepare("SELECT * FROM `scans` ORDER BY scanned_at DESC LIMIT ? OFFSET ?");
            $stmt->execute([$limit, $offset]);
        }
        return ['records' => $stmt->fetchAll(), 'total' => $count];
    } catch (Exception $e) {
        return ['records' => [], 'total' => 0];
    }
}

function mysql_delete_scan(string $id): bool
{
    if (!mysql_ensure_table()) return false;
    $pdo = mysql_connect_pdo();
    if ($pdo === null) return false;
    try {
        $stmt = $pdo->prepare("DELETE FROM `scans` WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    } catch (Exception $e) {
        return false;
    }
}

function mysql_get_stats(): array
{
    $empty = [
        'total_scans'    => 0,
        'unique_ips'     => 0,
        'today_scans'    => 0,
        'cms_dist'       => [],
        'fw_dist'        => [],
        'daily'          => [],
        'countries'      => [],
        'recent'         => [],
    ];

    if (!mysql_ensure_table()) return $empty;
    $pdo = mysql_connect_pdo();
    if ($pdo === null) return $empty;

    try {
        $total = (int) $pdo->query("SELECT COUNT(*) FROM `scans`")->fetchColumn();
        $uniq  = (int) $pdo->query("SELECT COUNT(DISTINCT ip) FROM `scans`")->fetchColumn();

        $today_s    = (int) mktime(0, 0, 0);
        $today_stmt = $pdo->prepare("SELECT COUNT(*) FROM `scans` WHERE `scanned_at` >= ?");
        $today_stmt->execute([$today_s]);
        $today_c = (int) $today_stmt->fetchColumn();

        // CMS distribution
        $cms_rows = $pdo->query("SELECT cms, COUNT(*) as cnt FROM `scans` WHERE cms != '' GROUP BY cms ORDER BY cnt DESC LIMIT 8")->fetchAll();
        $cms_dist = array_column($cms_rows, 'cnt', 'cms');

        // Framework distribution
        $fw_rows = $pdo->query("SELECT framework, COUNT(*) as cnt FROM `scans` WHERE framework != '' GROUP BY framework ORDER BY cnt DESC LIMIT 8")->fetchAll();
        $fw_dist = array_column($fw_rows, 'cnt', 'framework');

        // Daily (last 14 days)
        $since = time() - 86400 * 14;
        $rows  = $pdo->prepare("SELECT DATE(FROM_UNIXTIME(`scanned_at`)) as day, COUNT(*) as cnt FROM `scans` WHERE `scanned_at` >= ? GROUP BY day ORDER BY day DESC");
        $rows->execute([$since]);
        $daily = array_column($rows->fetchAll(), 'cnt', 'day');

        // Countries
        $cc_rows   = $pdo->query("SELECT country, COUNT(*) as cnt FROM `scans` WHERE country != '' GROUP BY country ORDER BY cnt DESC LIMIT 5")->fetchAll();
        $countries = array_column($cc_rows, 'cnt', 'country');

        // Recent
        $recent = $pdo->query("SELECT * FROM `scans` ORDER BY `scanned_at` DESC LIMIT 10")->fetchAll();

        return [
            'total_scans'    => $total,
            'unique_ips'     => $uniq,
            'today_scans'    => $today_c,
            'cms_dist'       => $cms_dist,
            'framework_dist' => $fw_dist,
            'daily_scans'    => $daily,
            'countries'      => $countries,
            'recent'         => $recent,
        ];
    } catch (Exception $e) {
        return $empty;
    }
}


// ════════════════════════════════════════════════════════════════════════════
// MYSQL ADMIN USER MANAGEMENT
// ════════════════════════════════════════════════════════════════════════════

function mysql_list_admin_users(): array
{
    $pdo = mysql_connect_pdo();
    if ($pdo === null) return [];
    try {
        return $pdo->query(
            "SELECT id, username, email, role, last_login, created_at, is_active FROM `admin_users` ORDER BY id ASC"
        )->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

function mysql_add_admin_user(string $username, string $password, string $email, string $role): bool
{
    $pdo = mysql_connect_pdo();
    if ($pdo === null) return false;
    try {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare(
            "INSERT INTO `admin_users` (username, password, email, role, created_at, is_active) VALUES (?, ?, ?, ?, ?, 1)"
        );
        $stmt->execute([$username, $hash, $email, $role, time()]);
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function mysql_update_admin_password(string $username, string $new_password): bool
{
    $pdo = mysql_connect_pdo();
    if ($pdo === null) return false;
    try {
        $hash = password_hash($new_password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("UPDATE `admin_users` SET `password` = ? WHERE `username` = ?");
        $stmt->execute([$hash, $username]);
        return $stmt->rowCount() > 0;
    } catch (Exception $e) {
        return false;
    }
}

function mysql_toggle_admin_user(int $id, int $is_active): bool
{
    $pdo = mysql_connect_pdo();
    if ($pdo === null) return false;
    try {
        $stmt = $pdo->prepare("UPDATE `admin_users` SET `is_active` = ? WHERE `id` = ?");
        $stmt->execute([$is_active, $id]);
        return $stmt->rowCount() > 0;
    } catch (Exception $e) {
        return false;
    }
}

function mysql_delete_admin_user(int $id): bool
{
    $pdo = mysql_connect_pdo();
    if ($pdo === null) return false;
    try {
        $stmt = $pdo->prepare("DELETE FROM `admin_users` WHERE `id` = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    } catch (Exception $e) {
        return false;
    }
}

function mysql_get_admin_user_by_id(int $id): ?array
{
    $pdo = mysql_connect_pdo();
    if ($pdo === null) return null;
    try {
        $stmt = $pdo->prepare("SELECT id, username, email, role, is_active FROM `admin_users` WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    } catch (Exception $e) {
        return null;
    }
}


// ════════════════════════════════════════════════════════════════════════════
// STATS SIDECAR (JSON mode unique IP tracking)
// ════════════════════════════════════════════════════════════════════════════

function load_stats_file(): array
{
    if (!file_exists(STATS_JSON_FILE)) return [];
    $d = @file_get_contents(STATS_JSON_FILE);
    return $d ? (json_decode($d, true) ?? []) : [];
}

function storage_increment_stats(string $ip): void
{
    if (STORAGE_DRIVER !== 'json') return;

    $stats = load_stats_file();
    $ips   = $stats['ip_set'] ?? [];

    $hash  = hash('sha256', $ip); // Store hash not raw IP for privacy
    if (!in_array($hash, $ips, true)) {
        $ips[] = $hash;
    }

    $stats['ip_set']    = $ips;
    $stats['unique_ips']= count($ips);

    @file_put_contents(STATS_JSON_FILE, json_encode($stats), LOCK_EX);
}


// ════════════════════════════════════════════════════════════════════════════
// HELPERS
// ════════════════════════════════════════════════════════════════════════════

/**
 * Flatten the multi-category detect.php result into simple string fields.
 */
function flatten_results(array $results): array
{
    $out = [];

    $first = fn(array $cat) => !empty($cat) ? ($cat[0]['name'] ?? '') : '';

    $out['cms']       = $first($results['cms']       ?? []);
    $out['framework'] = $first($results['frameworks'] ?? []);
    $out['language']  = $first($results['languages']  ?? []);
    $out['server']    = $first($results['servers']    ?? []);

    $plugins = array_slice($results['plugins'] ?? [], 0, 4);
    $out['plugins'] = implode(', ', array_column($plugins, 'name'));

    return $out;
}

/**
 * Look up country/city from an IP using ip-api.com (free, no key).
 */
function geoip_lookup(string $ip): array
{
    if (!GEOIP_ENABLED || !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE)) {
        return ['country' => '', 'country_code' => '', 'city' => ''];
    }

    $url = "http://ip-api.com/json/{$ip}?fields=country,countryCode,city,status";
    $ctx = stream_context_create(['http' => ['timeout' => GEOIP_TIMEOUT]]);
    $raw = @file_get_contents($url, false, $ctx);
    if (!$raw) return ['country' => '', 'country_code' => '', 'city' => ''];

    $data = json_decode($raw, true);
    if (($data['status'] ?? '') !== 'success') {
        return ['country' => '', 'country_code' => '', 'city' => ''];
    }

    return [
        'country'      => $data['country']     ?? '',
        'country_code' => $data['countryCode'] ?? '',
        'city'         => $data['city']        ?? '',
    ];
}

/**
 * Get real visitor IP (handles proxies/CloudFlare).
 */
function get_visitor_ip(): string
{
    foreach (['HTTP_CF_CONNECTING_IP','HTTP_X_FORWARDED_FOR','HTTP_X_REAL_IP','REMOTE_ADDR'] as $key) {
        $ip = $_SERVER[$key] ?? '';
        if ($ip) {
            // X-Forwarded-For can be a comma-separated list
            $ip = trim(explode(',', $ip)[0]);
            if (filter_var($ip, FILTER_VALIDATE_IP)) return $ip;
        }
    }
    return '0.0.0.0';
}
