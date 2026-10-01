<?php
/**
 * admin/includes/auth.php
 * ─────────────────────────────────────────────────────────────────────────────
 * Authentication helpers: session init, login, logout, guard.
 * ─────────────────────────────────────────────────────────────────────────────
 */

if (!defined('ADMIN_GUARD')) die('Direct access not permitted.');

/**
 * Initialise the admin session with secure settings.
 */
function admin_session_start(): void
{
    if (session_status() !== PHP_SESSION_NONE) return;

    session_name(SESSION_NAME);

    // session_set_cookie_params() array form requires PHP 7.3+
    // Use the individual-argument form for maximum compatibility (PHP 5.2+)
    $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    if (PHP_VERSION_ID >= 70300) {
        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'secure'   => $secure,
            'httponly' => true,
            'samesite' => 'Strict',
        ]);
    } else {
        session_set_cookie_params(0, '/', '', $secure, true);
    }

    session_start();

    // Rotate session ID every 30 minutes to mitigate fixation
    if (!isset($_SESSION['_created'])) {
        $_SESSION['_created'] = time();
    } elseif (time() - $_SESSION['_created'] > 1800) {
        session_regenerate_id(true);
        $_SESSION['_created'] = time();
    }
}

/**
 * Check if the current user is authenticated.
 */
function is_logged_in(): bool
{
    admin_session_start();

    if (empty($_SESSION['admin_logged_in'])) return false;

    // Idle timeout check
    if (isset($_SESSION['admin_last_active'])
        && (time() - $_SESSION['admin_last_active']) > SESSION_LIFETIME) {
        admin_logout();
        return false;
    }

    $_SESSION['admin_last_active'] = time();
    return true;
}

/**
 * Redirect to login page if not authenticated.
 * Call at the top of every protected page.
 */
function require_login(): void
{
    if (!is_logged_in()) {
        header('Location: ' . admin_url('login.php'));
        exit;
    }
}

/**
 * Try to verify credentials against the MySQL admin_users table.
 * Returns: true  = valid credentials
 *          false = wrong username or password (definitive)
 *          null  = MySQL unavailable (fall through to next method)
 */
function mysql_check_login(string $username, string $password)
{
    if (!defined('STORAGE_DRIVER') || STORAGE_DRIVER !== 'mysql') return null;
    if (!defined('DB_HOST')) return null;

    try {
        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s',
            DB_HOST, DB_PORT, DB_NAME, DB_CHARSET);
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT            => 5,
        ]);
        $stmt = $pdo->prepare(
            "SELECT `password` FROM `admin_users` WHERE `username` = ? AND `is_active` = 1 LIMIT 1"
        );
        $stmt->execute([$username]);
        $row = $stmt->fetch();
        if (!$row) return false;
        if (!password_verify($password, $row['password'])) return false;

        // Update last_login timestamp
        $upd = $pdo->prepare("UPDATE `admin_users` SET `last_login` = ? WHERE `username` = ?");
        $upd->execute([time(), $username]);

        return true;
    } catch (Exception $e) {
        return null; // DB unavailable — fall through to next auth method
    }
}

/**
 * Load users from users.json fallback file.
 */
function load_admin_users(): array
{
    $file = dirname(__DIR__) . '/users.json';
    if (!file_exists($file)) return [];
    $raw = json_decode(file_get_contents($file), true);
    return is_array($raw) && !empty($raw) ? $raw : [];
}

/**
 * Attempt login with given credentials.
 * Priority: 1) MySQL admin_users table  2) users.json  3) config.php constants
 */
function attempt_login(string $username, string $password): bool
{
    // 1. Try MySQL
    $mysql_result = mysql_check_login($username, $password);
    if ($mysql_result === true) {
        // Success via MySQL
    } elseif ($mysql_result === false) {
        return false; // Wrong credentials — no point checking further
    } else {
        // MySQL unavailable — try JSON file
        $users = load_admin_users();
        if (!empty($users)) {
            $ok = false;
            foreach ($users as $user) {
                if (isset($user['username'], $user['hash'])
                    && $user['username'] === $username
                    && password_verify($password, $user['hash'])) {
                    $ok = true;
                    break;
                }
            }
            if (!$ok) return false;
        } else {
            // Final fallback: config.php constants
            if ($username !== ADMIN_USERNAME) return false;
            if (!password_verify($password, ADMIN_PASSWORD_HASH)) return false;
        }
    }

    admin_session_start();
    session_regenerate_id(true);

    $_SESSION['admin_logged_in']   = true;
    $_SESSION['admin_username']    = $username;
    $_SESSION['admin_last_active'] = time();
    $_SESSION['_created']          = time();

    return true;
}

/**
 * Destroy the admin session and redirect to login.
 */
function admin_logout(): void
{
    admin_session_start();
    $_SESSION = [];
    session_destroy();
    header('Location: ' . admin_url('login.php') . '?logged_out=1');
    exit;
}

/**
 * Build a URL relative to the admin directory.
 * Compatible with PHP 7.1+
 */
function admin_url(string $page = ''): string
{
    $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    // Normalize: if we're already in /admin, don't double it
    if (substr($base, -6) !== '/admin') {
        $base .= '/admin';
    }
    return $base . ($page ? '/' . ltrim($page, '/') : '');
}

/**
 * Generate a CSRF token and store in session.
 */
function csrf_token(): string
{
    admin_session_start();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify submitted CSRF token.
 */
function verify_csrf(string $token): bool
{
    admin_session_start();
    return isset($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}
