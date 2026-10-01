<?php
/**
 * functions.php
 * ─────────────────────────────────────────────────────────────────────────────
 * Helper functions: URL validation, HTTP fetching via cURL, pattern matching,
 * history persistence, and result formatting.
 * ─────────────────────────────────────────────────────────────────────────────
 */

// ── 1. URL Utilities ──────────────────────────────────────────────────────────

/**
 * Sanitize and normalize an incoming URL string.
 * Adds https:// if no scheme is present.
 *
 * @param  string $url Raw user input
 * @return string      Cleaned URL string
 */
function sanitize_url(string $url): string
{
    $url = trim($url);
    $url = filter_var($url, FILTER_SANITIZE_URL);

    // Prepend https:// if no scheme found
    if (!preg_match('#^https?://#i', $url)) {
        $url = 'https://' . $url;
    }

    return $url;
}

/**
 * Validate that a URL is well-formed and has a resolvable host.
 *
 * @param  string $url URL to validate
 * @return bool
 */
function validate_url(string $url): bool
{
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return false;
    }

    $host = parse_url($url, PHP_URL_HOST);
    if (empty($host)) {
        return false;
    }

    // Block localhost and private IP ranges
    $blocked = ['/^localhost$/i', '/^127\./', '/^10\./', '/^192\.168\./', '/^172\.(1[6-9]|2\d|3[01])\./'];
    foreach ($blocked as $pattern) {
        if (preg_match($pattern, $host)) {
            return false;
        }
    }

    return true;
}


// ── 2. HTTP Fetching ──────────────────────────────────────────────────────────

/**
 * Browser profiles used for outbound requests.
 *
 * A declared bot User-Agent — the classic "Mozilla/5.0 (compatible; Name/1.0;
 * +https://…)" form — is rate-limited or blocked outright by most modern
 * platforms. Shopify's edge in particular answers it with HTTP 429, which used
 * to abort the whole scan. Presenting an ordinary desktop browser keeps this
 * passive read-only scan working exactly like a normal visit would.
 *
 * @return array<int, array{ua: string, ch_ua: string|null, platform: string|null}>
 */
function request_profiles(): array
{
    return [
        [
            'ua'       => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36',
            'ch_ua'    => '"Google Chrome";v="125", "Chromium";v="125", "Not.A/Brand";v="24"',
            'platform' => 'Windows',
        ],
        [
            'ua'       => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
            'ch_ua'    => '"Google Chrome";v="124", "Chromium";v="124", "Not.A/Brand";v="24"',
            'platform' => 'macOS',
        ],
        [
            'ua'       => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:126.0) Gecko/20100101 Firefox/126.0',
            'ch_ua'    => null,
            'platform' => null,
        ],
    ];
}

/**
 * Headers a real browser sends on a top-level navigation.
 *
 * @param  array $profile One entry from request_profiles()
 * @return array          Header lines in "Name: value" form
 */
function browser_request_headers(array $profile): array
{
    $headers = [
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
        'Accept-Language: en-US,en;q=0.9',
        'Upgrade-Insecure-Requests: 1',
        'Sec-Fetch-Dest: document',
        'Sec-Fetch-Mode: navigate',
        'Sec-Fetch-Site: none',
        'Sec-Fetch-User: ?1',
        'Cache-Control: max-age=0',
        'Connection: keep-alive',
    ];

    if (!empty($profile['ch_ua'])) {
        $headers[] = 'sec-ch-ua: ' . $profile['ch_ua'];
        $headers[] = 'sec-ch-ua-mobile: ?0';
        $headers[] = 'sec-ch-ua-platform: "' . $profile['platform'] . '"';
    }

    return $headers;
}

/**
 * Statuses that usually mean "throttled", not "this site has no CMS".
 */
function is_throttled_status(int $status): bool
{
    return in_array($status, [403, 429, 503], true);
}

/**
 * Fetch the HTML body and HTTP response headers of a URL using cURL.
 * Returns an array with 'html', 'headers', 'cookies', 'status', and 'error'.
 *
 * Retries once (after honouring Retry-After, capped at 3s) when the server
 * throttles the first attempt, because WAFs commonly serve the retry normally.
 *
 * @param  string $url     Target URL
 * @param  int    $timeout Maximum seconds to wait (default: 15)
 * @return array
 */
function fetch_url(string $url, int $timeout = 15): array
{
    $result = [
        'html'       => '',
        'headers'    => [],
        'cookies'    => [],
        'status'     => 0,
        'final_url'  => $url,
        'error'      => null,
    ];

    if (!function_exists('curl_init')) {
        // Fallback to file_get_contents if cURL is unavailable
        return fetch_url_fallback($url, $timeout, $result);
    }

    $profiles = request_profiles();
    $attempts = min(2, count($profiles));

    for ($i = 0; $i < $attempts; $i++) {
        $result = curl_fetch_url($url, $timeout, $profiles[$i]);

        // Keep a response we can actually fingerprint, throttled or not.
        if (!is_throttled_status($result['status']) || $result['html'] !== '') {
            break;
        }

        // Last attempt — nothing left to try.
        if ($i === $attempts - 1) {
            break;
        }

        $retry_after = trim($result['headers']['retry-after'] ?? '');
        sleep(ctype_digit($retry_after) ? min((int) $retry_after, 3) : 1);
    }

    return $result;
}

/**
 * Perform a single cURL request with a browser-like profile.
 *
 * @internal
 */
function curl_fetch_url(string $url, int $timeout, array $profile): array
{
    $result = [
        'html'      => '',
        'headers'   => [],
        'cookies'   => [],
        'status'    => 0,
        'final_url' => $url,
        'error'     => null,
    ];

    $raw_headers = [];
    $cookie_jar  = tempnam(sys_get_temp_dir(), 'cmsd_ck_');

    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,    // Follow redirects
        CURLOPT_MAXREDIRS      => 5,
        CURLOPT_TIMEOUT        => $timeout,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => false,   // Allow self-signed certs
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_USERAGENT      => $profile['ua'],
        CURLOPT_ENCODING       => '',      // Accept all encodings (handles gzip etc.)
        CURLOPT_HEADERFUNCTION => function ($ch, $header) use (&$raw_headers) {
            $raw_headers[] = rtrim($header);
            return strlen($header);
        },
        CURLOPT_HTTPHEADER     => browser_request_headers($profile),
    ]);

    // Negotiate HTTP/2 when the local libcurl supports it. Skipped entirely on
    // builds that predate the constant rather than risking a fatal error.
    if (defined('CURL_HTTP_VERSION_2TLS')) {
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2TLS);
    }

    // Carry cookies set on the first hop (Cloudflare's __cf_bm, Shopify's
    // session cookies) through the redirect chain, the way a browser does.
    if ($cookie_jar !== false) {
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_jar);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_jar);
    }

    $html = curl_exec($ch);
    $result['status']    = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $result['final_url'] = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL) ?: $url;
    $err                 = curl_error($ch);
    curl_close($ch);

    if ($cookie_jar !== false) {
        @unlink($cookie_jar);
    }

    if ($err && $html === false) {
        $result['error'] = $err;
        return $result;
    }

    $result['html']    = is_string($html) ? $html : '';
    $result['headers'] = parse_raw_headers($raw_headers);
    $result['cookies'] = extract_cookies($raw_headers);

    return $result;
}

/**
 * Fallback fetch using file_get_contents when cURL is unavailable.
 *
 * @internal
 */
function fetch_url_fallback(string $url, int $timeout, array $result): array
{
    $ctx = stream_context_create([
        'http' => [
            'timeout'          => $timeout,
            'follow_location'  => 1,
            'max_redirects'    => 5,
            'user_agent'       => request_profiles()[0]['ua'],
            'ignore_errors'    => true,
        ],
        'ssl'  => [
            'verify_peer'      => false,
            'verify_peer_name' => false,
        ],
    ]);

    $html = @file_get_contents($url, false, $ctx);
    if ($html === false) {
        $result['error'] = 'Could not connect to the target URL.';
        return $result;
    }

    $result['html'] = $html;

    // Parse headers from $http_response_header superglobal (set by file_get_contents)
    if (!empty($http_response_header)) {
        $result['headers'] = parse_raw_headers($http_response_header);
        $result['cookies'] = extract_cookies($http_response_header);
    }

    return $result;
}


// ── 3. Header Parsing ─────────────────────────────────────────────────────────

/**
 * Parse an array of raw HTTP header strings into a key→value map.
 * Header names are lowercased for consistent matching.
 *
 * @param  array $raw_headers  e.g. ['HTTP/1.1 200 OK', 'Content-Type: text/html', ...]
 * @return array               e.g. ['content-type' => 'text/html', ...]
 */
function parse_raw_headers(array $raw_headers): array
{
    $headers = [];
    foreach ($raw_headers as $line) {
        if (strpos($line, ':') !== false) {
            [$name, $value] = explode(':', $line, 2);
            $key = strtolower(trim($name));
            // Multiple values for same header → append
            if (isset($headers[$key])) {
                $headers[$key] .= '; ' . trim($value);
            } else {
                $headers[$key] = trim($value);
            }
        }
    }
    return $headers;
}

/**
 * Extract all cookie names from Set-Cookie headers.
 *
 * @param  array $raw_headers
 * @return array  Flat list of cookie name strings
 */
function extract_cookies(array $raw_headers): array
{
    $cookies = [];
    foreach ($raw_headers as $line) {
        if (preg_match('/^set-cookie:\s*([^=;]+)/i', $line, $m)) {
            $cookies[] = trim($m[1]);
        }
    }
    return $cookies;
}


// ── 4. Pattern Matching ───────────────────────────────────────────────────────

/**
 * Run a single technology's patterns against the fetched page data.
 * Returns a confidence score (0–100). Score > 0 means detected.
 *
 * @param  array  $tech   A technology entry from cms_patterns.php
 * @param  string $html   Full HTML body
 * @param  array  $headers Parsed HTTP headers
 * @param  array  $cookies Cookie name list
 * @return int    Confidence score
 */
function match_technology(array $tech, string $html, array $headers, array $cookies): int
{
    $score = 0;

    // ── Headers ──
    foreach ($tech['headers'] ?? [] as $headerName => $pattern) {
        $val = $headers[strtolower($headerName)] ?? '';
        if ($val !== '' && preg_match($pattern, $val)) {
            $score += 30;
        }
    }

    // ── HTML body ──
    foreach ($tech['html'] ?? [] as $pattern) {
        if (preg_match($pattern, $html)) {
            $score += 20;
        }
    }

    // ── Cookies ──
    foreach ($tech['cookies'] ?? [] as $pattern) {
        foreach ($cookies as $cookie) {
            if (preg_match($pattern, $cookie)) {
                $score += 25;
                break;
            }
        }
    }

    // ── Meta tags ──
    // Supports both attribute orders:
    //   <meta name="generator" content="Drupal 7" />   ← most common
    //   <meta content="Drupal 7" name="generator" />   ← also valid HTML
    if (!empty($tech['meta'])) {
        foreach ($tech['meta'] as $metaName => $pattern) {
            $quotedName = preg_quote($metaName, '/');
            $matched    = false;
            $content    = '';

            // Order 1: name comes before content
            $p1 = '/<meta[^>]+name=["\']' . $quotedName . '["\'][^>]+content=["\'](.*?)["\']/i';
            if (preg_match($p1, $html, $m)) {
                $content = $m[1];
                $matched = true;
            }

            // Order 2: content comes before name
            if (!$matched) {
                $p2 = '/<meta[^>]+content=["\'](.*?)["\'][^>]+name=["\']' . $quotedName . '["\'][^>]*\/?>/i';
                if (preg_match($p2, $html, $m)) {
                    $content = $m[1];
                    $matched = true;
                }
            }

            if ($matched && preg_match($pattern, $content)) {
                $score += 25;
            }
        }
    }

    // ── Script src ──
    if (preg_match_all('/<script[^>]+src=["\'](.*?)["\']/i', $html, $scriptMatches)) {
        foreach ($tech['scripts'] ?? [] as $pattern) {
            foreach ($scriptMatches[1] as $src) {
                if (preg_match($pattern, $src)) {
                    $score += 15;
                    break;
                }
            }
        }
    }

    // ── Link href ──
    if (preg_match_all('/<link[^>]+href=["\'](.*?)["\']/i', $html, $linkMatches)) {
        foreach ($tech['links'] ?? [] as $pattern) {
            foreach ($linkMatches[1] as $href) {
                if (preg_match($pattern, $href)) {
                    $score += 10;
                    break;
                }
            }
        }
    }

    return min($score, 100); // Cap at 100
}

/**
 * Detect all technologies in a given category from the pattern set.
 * Returns matched items sorted by confidence score (highest first).
 *
 * @param  array  $category_patterns  Array of tech entries for one category
 * @param  string $html
 * @param  array  $headers
 * @param  array  $cookies
 * @param  int    $min_score          Minimum score to include (default: 15)
 * @return array                      Matched items with 'score' added
 */
function detect_category(
    array $category_patterns,
    string $html,
    array $headers,
    array $cookies,
    int $min_score = 15
): array {
    $detected = [];

    foreach ($category_patterns as $tech) {
        $score = match_technology($tech, $html, $headers, $cookies);
        if ($score >= $min_score) {
            $detected[] = array_merge($tech, ['score' => $score]);
        }
    }

    // Sort by confidence descending
    usort($detected, fn($a, $b) => $b['score'] <=> $a['score']);

    return $detected;
}


// ── 4b. WordPress Deep Scan (theme + plugin enumeration) ─────────────────────

/**
 * When WordPress is detected, extract the active theme name and a list of
 * all plugins referenced in the page's HTML (via wp-content/plugins/ and
 * wp-content/themes/ asset paths found in <script>, <link>, and inline URLs).
 *
 * This is a best-effort static scan: only plugins/themes whose asset files
 * (css/js) are loaded on the scanned page will be found. Server-side-only
 * plugins with no front-end assets won't appear.
 *
 * @param  string $html      Full HTML body
 * @param  string $page_url  Final page URL, used to resolve theme stylesheets
 * @return array             Theme metadata and plugins exposed by page assets
 */
function detect_wordpress_extras(string $html, string $page_url = ''): array
{
    $theme_slug = null;
    $theme_name = null;
    $theme_version = null;
    $theme_author = null;
    $plugins = [];
    $theme_stylesheets = [];

    // Decode entities first: WordPress and CDNs often HTML-encode query strings.
    $source = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');

    // Prefer an actual stylesheet URL because its header contains the canonical
    // "Theme Name", not merely the directory slug.
    if (preg_match_all('/<(?:link|script)[^>]+(?:href|src)=["\']([^"\']+)["\']/i', $source, $asset_matches)) {
        foreach ($asset_matches[1] as $asset_url) {
            if (preg_match('#(?:wp[\-_]?content|content)/themes/([a-zA-Z0-9_-]+)/(?:[^"\']*?/)?style(?:\.min)?\.css(?:[?#][^"\']*)?$#i', $asset_url, $m)) {
                $theme_slug = $theme_slug ?: $m[1];
                $theme_stylesheets[] = $asset_url;
            }
        }
    }

    // Standard theme asset path, including installations that rewrite or move
    // wp-content. The first candidate is normally the active theme.
    if (preg_match_all('#(?:/|\\\\)(?:wp[\-_]?content|content)/themes/([a-zA-Z0-9_-]+)(?:/|\\\\)#i', $source, $theme_matches)) {
        foreach ($theme_matches[1] as $candidate) {
            $theme_slug = $theme_slug ?: $candidate;
        }
    }
    if (!$theme_slug && preg_match('#(?:^|\s)theme-([a-zA-Z0-9_-]+)(?:\s|["\'])#i', $source, $m)) {
        $theme_slug = $m[1];
    }

    // Read the theme header when the page exposed a stylesheet. This adds the
    // real display name (for example "Hello Elementor") and optional metadata.
    foreach (array_slice(array_unique($theme_stylesheets), 0, 2) as $stylesheet) {
        $stylesheet_url = resolve_asset_url($stylesheet, $page_url);
        if (!$stylesheet_url || !validate_url($stylesheet_url)) {
            continue;
        }
        $stylesheet_fetch = fetch_url($stylesheet_url, 4);
        if (!empty($stylesheet_fetch['html'])) {
            $header = substr($stylesheet_fetch['html'], 0, 12000);
            if (preg_match('/^\s*\*\s*Theme Name:\s*(.+?)\s*$/mi', $header, $m)) {
                $theme_name = trim($m[1]);
            }
            if (preg_match('/^\s*\*\s*Version:\s*(.+?)\s*$/mi', $header, $m)) {
                $theme_version = trim($m[1]);
            }
            if (preg_match('/^\s*\*\s*Author:\s*(.+?)\s*$/mi', $header, $m)) {
                $theme_author = trim(strip_tags($m[1]));
            }
        }
        if ($theme_name) {
            break;
        }
    }

    // Plugins whose public assets are present on the page. This is intentionally
    // passive: it does not crawl admin/private directories or guess hidden ones.
    if (preg_match_all('#(?:/|\\\\)(?:wp[\-_]?content|content)/plugins/([a-zA-Z0-9_-]+)(?:/|\\\\)#i', $source, $plugin_matches)) {
        $plugins = array_values(array_unique($plugin_matches[1], SORT_STRING));
    }

    $plugins = array_filter($plugins, fn($p) => strlen($p) >= 2 && strlen($p) <= 60);
    sort($plugins, SORT_STRING | SORT_FLAG_CASE);

    $theme_slug = ($theme_slug && strlen($theme_slug) >= 2 && strlen($theme_slug) <= 60)
        ? $theme_slug
        : null;

    return [
        'theme' => $theme_name ?: ($theme_slug ? format_slug($theme_slug) : null),
        'theme_slug' => $theme_slug,
        'theme_version' => $theme_version,
        'theme_author' => $theme_author,
        'plugins' => array_map('format_plugin_name', array_values($plugins)),
        'plugin_slugs' => array_values($plugins),
        'note' => 'Only themes and plugins that expose public page assets can be detected.',
    ];
}

/**
 * Resolve an asset URL against the scanned page URL.
 */
function resolve_asset_url(string $asset, string $page_url): ?string
{
    $asset = trim(html_entity_decode($asset, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    if ($asset === '' || str_starts_with($asset, 'data:') || str_starts_with($asset, '//')) {
        if (str_starts_with($asset, '//')) {
            $scheme = parse_url($page_url, PHP_URL_SCHEME) ?: 'https';
            return $scheme . ':' . $asset;
        }
        return null;
    }
    if (preg_match('#^https?://#i', $asset)) {
        return $asset;
    }

    $parts = parse_url($page_url);
    if (empty($parts['scheme']) || empty($parts['host'])) {
        return null;
    }
    $origin = $parts['scheme'] . '://' . $parts['host'] . (isset($parts['port']) ? ':' . $parts['port'] : '');
    if (str_starts_with($asset, '/')) {
        return $origin . $asset;
    }

    $path = $parts['path'] ?? '/';
    $directory = rtrim(str_replace('\\', '/', dirname($path)), '/');
    return $origin . ($directory ? $directory . '/' : '/') . $asset;
}

/**
 * Convert a slug into a readable display name and preserve common brand names.
 * e.g. "yoast-seo" → "Yoast SEO", "woocommerce" → "WooCommerce".
 *
 * @param  string $slug
 * @return string
 */
function format_plugin_name(string $slug): string
{
    static $known_names = [
        'akismet' => 'Akismet',
        'contact-form-7' => 'Contact Form 7',
        'elementor' => 'Elementor',
        'elementor-pro' => 'Elementor Pro',
        'google-site-kit' => 'Site Kit by Google',
        'jetpack' => 'Jetpack',
        'litespeed-cache' => 'LiteSpeed Cache',
        'mailchimp-for-wp' => 'Mailchimp for WordPress',
        'query-monitor' => 'Query Monitor',
        'rank-math' => 'Rank Math SEO',
        'redirection' => 'Redirection',
        'revslider' => 'Slider Revolution',
        'updraftplus' => 'UpdraftPlus',
        'wordfence' => 'Wordfence',
        'woocommerce' => 'WooCommerce',
        'wordpress-seo' => 'Yoast SEO',
        'wpforms-lite' => 'WPForms Lite',
        'wp-rocket' => 'WP Rocket',
    ];
    $key = strtolower($slug);
    if (isset($known_names[$key])) {
        return $known_names[$key];
    }

    $words = preg_split('/[-_]+/', $slug);
    return implode(' ', array_map('ucfirst', $words));
}

/**
 * Backwards-compatible formatter used by older admin/history code.
 */
function format_slug(string $slug): string
{
    return format_plugin_name($slug);
}


// ── 5. Scan History ───────────────────────────────────────────────────────────

define('HISTORY_FILE', __DIR__ . '/../history/scans.json');
define('MAX_HISTORY',  20);

/**
 * Append a completed scan to the JSON history file.
 * Keeps only the most recent MAX_HISTORY entries.
 *
 * @param  string $url     Scanned URL
 * @param  array  $results Detected technologies grouped by category
 */
function save_scan_history(string $url, array $results): void
{
    $history = load_scan_history();

    $summary = [];
    foreach ($results as $category => $items) {
        if (!empty($items)) {
            $summary[$category] = array_column(array_slice($items, 0, 3), 'name');
        }
    }

    array_unshift($history, [
        'url'       => $url,
        'summary'   => $summary,
        'timestamp' => time(),
    ]);

    // Trim to max entries
    $history = array_slice($history, 0, MAX_HISTORY);

    @file_put_contents(HISTORY_FILE, json_encode($history, JSON_PRETTY_PRINT));
}

/**
 * Load existing scan history from the JSON file.
 *
 * @return array
 */
function load_scan_history(): array
{
    if (!file_exists(HISTORY_FILE)) {
        return [];
    }
    $data = @file_get_contents(HISTORY_FILE);
    return $data ? (json_decode($data, true) ?? []) : [];
}


// ── 6. Misc Utilities ────────────────────────────────────────────────────────

/**
 * Convert a Unix timestamp to a human-readable relative time string.
 * e.g.  "3 minutes ago", "2 hours ago", "yesterday"
 *
 * @param  int $timestamp
 * @return string
 */
function time_ago(int $timestamp): string
{
    $diff = time() - $timestamp;

    if ($diff < 60)          return 'just now';
    if ($diff < 3600)        return floor($diff / 60) . 'm ago';
    if ($diff < 86400)       return floor($diff / 3600) . 'h ago';
    if ($diff < 172800)      return 'yesterday';
    return date('M j, Y', $timestamp);
}

/**
 * Get a short display hostname from a full URL.
 * e.g.  "https://www.example.com/path" → "example.com"
 *
 * @param  string $url
 * @return string
 */
function display_host(string $url): string
{
    $host = parse_url($url, PHP_URL_HOST) ?? $url;
    return preg_replace('/^www\./i', '', $host);
}