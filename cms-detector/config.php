<?php
/**
 * config.php
 * ─────────────────────────────────────────────────────────────────────────────
 * Central configuration for CMS Detector (StackDetect).
 * All application-wide and admin panel settings live here.
 * Edit these values to tune behavior without touching core logic.
 *
 * IMPORTANT: Change ADMIN_USERNAME and ADMIN_PASSWORD_HASH before deploying.
 * Generate a new hash with:
 *   php -r "echo password_hash('yourpassword', PASSWORD_BCRYPT);"
 * ─────────────────────────────────────────────────────────────────────────────
 */

// ── App Meta ──────────────────────────────────────────────────────────────────
define('APP_NAME',       'CMS Detector');
define('APP_TAGLINE',    'Uncover the technology behind any website');
define('APP_VERSION',    '1.0.0');
define('ADMIN_APP_NAME', 'CMS Detector');
define('ADMIN_VERSION',  '1.0.0');

// ── Request Settings ──────────────────────────────────────────────────────────
define('FETCH_TIMEOUT',       15);   // Max seconds to wait for remote URL
define('FETCH_MAX_REDIRECTS',  5);   // Max HTTP redirects to follow

// ── Detection Thresholds ──────────────────────────────────────────────────────
define('MIN_SCORE_CMS',       15);   // Minimum confidence score for CMS detection
define('MIN_SCORE_LANGUAGE',  15);   // Minimum for programming language detection
define('MIN_SCORE_FRAMEWORK', 15);   // Minimum for framework detection
define('MIN_SCORE_PLUGIN',    15);   // Minimum for plugin detection
define('MIN_SCORE_SERVER',    30);   // Servers are header-only → require higher score

// ── History ───────────────────────────────────────────────────────────────────
define('ENABLE_HISTORY',    true);   // Enable/disable scan history feature
define('MAX_HISTORY_ITEMS',   20);   // Maximum number of scans to keep in history

// ── Rate Limiting (simple, session-based) ────────────────────────────────────
define('RATE_LIMIT_ENABLED',  false); // Set true to enable basic rate limiting
define('RATE_LIMIT_MAX',         10); // Max scans per window
define('RATE_LIMIT_WINDOW',    3600); // Window in seconds (1 hour)

// ── Admin Credentials ─────────────────────────────────────────────────────────
define('ADMIN_USERNAME',      'admin');

// Default password: admin123  — CHANGE THIS BEFORE GOING LIVE
define('ADMIN_PASSWORD_HASH', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

// ── Session ───────────────────────────────────────────────────────────────────
define('SESSION_NAME',        'stackdetect_admin');
define('SESSION_LIFETIME',    7200);   // 2 hours idle timeout (seconds)

// ── Storage: choose 'json' or 'mysql' ────────────────────────────────────────
define('STORAGE_DRIVER',      'mysql'); // 'json' | 'mysql'

// ── JSON Storage Paths ────────────────────────────────────────────────────────
define('SCANS_JSON_FILE',     __DIR__ . '/history/scans_full.json');
define('STATS_JSON_FILE',     __DIR__ . '/history/stats.json');

// ── MySQL (only used if STORAGE_DRIVER = 'mysql') ─────────────────────────────
// On shared hosting the database is pre-created by your host — use that name.
// On a local/root server you can name it anything (e.g. 'stackdetect').
define('DB_HOST',     'sql302.infinityfree.com');
define('DB_PORT',     '3306');
define('DB_NAME',     'if0_39102818_cms');
define('DB_USER',     'if0_39102818');
define('DB_PASS',     'SaieedRahman');
define('DB_CHARSET',  'utf8mb4');

// ── GeoIP ─────────────────────────────────────────────────────────────────────
// Uses ip-api.com free tier (45 req/min, no key needed).
// Set to false to disable geo lookup (faster, no external calls).
define('GEOIP_ENABLED',       true);
define('GEOIP_TIMEOUT',       3);      // seconds

// ── Pagination ────────────────────────────────────────────────────────────────
define('RECORDS_PER_PAGE',    20);

// ── Security ──────────────────────────────────────────────────────────────────
// ADMIN_GUARD is intentionally NOT defined here.
// Each admin entry-point (index.php, login.php, etc.) defines it BEFORE loading
// this config so that the include files can block direct URL access via:
//   if (!defined('ADMIN_GUARD')) die('Direct access not permitted.');
