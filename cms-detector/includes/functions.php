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
 * Fetch the HTML body and HTTP response headers of a URL using cURL.
 * Returns an array with 'html', 'headers', 'cookies', 'status', and 'error'.
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

    $ch = curl_init();

    // Container for raw headers
    $raw_headers = [];

    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,    // Follow redirects
        CURLOPT_MAXREDIRS      => 5,
        CURLOPT_TIMEOUT        => $timeout,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => false,   // Allow self-signed certs
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_USERAGENT      => 'Mozilla/5.0 (compatible; CMSDetector/1.0; +https://cmsdetect.app)',
        CURLOPT_ENCODING       => '',      // Accept all encodings (handles gzip etc.)
        CURLOPT_HEADERFUNCTION => function ($ch, $header) use (&$raw_headers) {
            $raw_headers[] = rtrim($header);
            return strlen($header);
        },
        CURLOPT_HTTPHEADER     => [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language: en-US,en;q=0.5',
            'Cache-Control: no-cache',
        ],
    ]);

    $html = curl_exec($ch);
    $result['status']    = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $result['final_url'] = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    $err                 = curl_error($ch);
    curl_close($ch);

    if ($err) {
        $result['error'] = $err;
        return $result;
    }

    $result['html']    = $html ?: '';
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
            'user_agent'       => 'Mozilla/5.0 (compatible; CMSDetector/1.0)',
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
    if (!empty($tech['meta'])) {
        foreach ($tech['meta'] as $metaName => $pattern) {
            $metaPattern = '/<meta[^>]+name=["\']' . preg_quote($metaName, '/') . '["\'][^>]+content=["\'](.*?)["\']/i';
            if (preg_match($metaPattern, $html, $m) && preg_match($pattern, $m[1])) {
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
