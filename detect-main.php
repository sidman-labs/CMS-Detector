<?php
/**
 * detect.php
 * ─────────────────────────────────────────────────────────────────────────────
 * Backend endpoint for CMS / technology detection.
 * Called via AJAX (POST, expects JSON response) or plain form POST.
 *
 * Request:  POST  { url: "https://example.com" }
 * Response: JSON  { success: bool, data: {...}, error: string|null }
 * ─────────────────────────────────────────────────────────────────────────────
 */

// ── Bootstrap ─────────────────────────────────────────────────────────────────
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';

// Load admin storage layer (for logging scans to admin panel)
define('ADMIN_GUARD', true);
require_once __DIR__ . '/admin/config.php';
require_once __DIR__ . '/admin/includes/storage.php';

// Patterns are loaded below after sanity checks
// to avoid loading a large array for invalid requests.

// ── CORS / Content-Type ───────────────────────────────────────────────────────
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

// ── Only accept POST ──────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond_error('Method Not Allowed', 405);
}

// ── Read & validate input ─────────────────────────────────────────────────────
// Accepts both application/json and application/x-www-form-urlencoded
$raw_url = '';
$content_type = $_SERVER['CONTENT_TYPE'] ?? '';

if (strpos($content_type, 'application/json') !== false) {
    $body    = file_get_contents('php://input');
    $payload = json_decode($body, true);
    $raw_url = $payload['url'] ?? '';
} else {
    $raw_url = $_POST['url'] ?? '';
}

if (empty(trim($raw_url))) {
    respond_error('Please enter a URL to scan.');
}

// ── Sanitize & validate URL ───────────────────────────────────────────────────
$url = sanitize_url($raw_url);

if (!validate_url($url)) {
    respond_error('The URL you entered appears to be invalid or targets a private network.');
}

// ── Rate limiting (optional, session-based) ───────────────────────────────────
if (RATE_LIMIT_ENABLED) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $now   = time();
    $scans = $_SESSION['rate_scans'] ?? [];

    // Remove scans outside the current window
    $scans = array_filter($scans, fn($t) => ($now - $t) < RATE_LIMIT_WINDOW);

    if (count($scans) >= RATE_LIMIT_MAX) {
        respond_error('You have reached the scan limit. Please wait before scanning again.');
    }

    $scans[] = $now;
    $_SESSION['rate_scans'] = array_values($scans);
}

// ── Fetch the target URL ──────────────────────────────────────────────────────
$fetch = fetch_url($url, FETCH_TIMEOUT);

if ($fetch['error']) {
    respond_error('Could not reach the website: ' . htmlspecialchars($fetch['error']));
}

if ($fetch['status'] === 0) {
    respond_error('The website did not respond. Please check the URL and try again.');
}

if ($fetch['status'] >= 400 && $fetch['status'] !== 403 && $fetch['status'] !== 401) {
    respond_error("The website returned HTTP {$fetch['status']}. It may be down or behind authentication.");
}

// ── Load detection patterns ───────────────────────────────────────────────────
$patterns = require __DIR__ . '/includes/cms_patterns.php';

$html    = $fetch['html'];
$headers = $fetch['headers'];
$cookies = $fetch['cookies'];

// ── Run detectors ─────────────────────────────────────────────────────────────
$results = [
    'cms'        => detect_category($patterns['cms'],        $html, $headers, $cookies, MIN_SCORE_CMS),
    'languages'  => detect_category($patterns['languages'],  $html, $headers, $cookies, MIN_SCORE_LANGUAGE),
    'frameworks' => detect_category($patterns['frameworks'], $html, $headers, $cookies, MIN_SCORE_FRAMEWORK),
    'plugins'    => detect_category($patterns['plugins'],    $html, $headers, $cookies, MIN_SCORE_PLUGIN),
    'servers'    => detect_category($patterns['servers'],    $html, $headers, $cookies, MIN_SCORE_SERVER),
];

// ── Strip internal pattern data before sending to client ─────────────────────
$clean_results = [];
foreach ($results as $category => $items) {
    $clean_results[$category] = array_map(function ($item) {
        return [
            'name'  => $item['name'],
            'icon'  => $item['icon'],
            'color' => $item['color'],
            'score' => $item['score'],
        ];
    }, $items);
}

// ── Persist public history + admin detailed log ───────────────────────────────
if (ENABLE_HISTORY) {
    save_scan_history($fetch['final_url'], $results);
}

// Save full record to admin storage (JSON or MySQL)
try {
    $visitor_ip = get_visitor_ip();
    storage_save_scan($fetch['final_url'], $results, $visitor_ip);
} catch (Throwable $e) {
    error_log('StackDetect admin storage error: ' . $e->getMessage());
}

// ── Build response ────────────────────────────────────────────────────────────
$response = [
    'success'   => true,
    'scanned'   => display_host($fetch['final_url']),
    'final_url' => $fetch['final_url'],
    'http_status' => $fetch['status'],
    'data'      => $clean_results,
    'meta'      => [
        'total_detected' => array_sum(array_map('count', $clean_results)),
        'scanned_at'     => date('c'),
    ],
];

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
exit;


// ── Helper: error response ────────────────────────────────────────────────────

/**
 * Send a JSON error response and terminate execution.
 *
 * @param string $message User-facing error message
 * @param int    $code    HTTP status code
 */
function respond_error(string $message, int $code = 200): never
{
    http_response_code($code);
    echo json_encode([
        'success' => false,
        'error'   => $message,
        'data'    => null,
    ]);
    exit;
}
