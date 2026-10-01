<?php
/**
 * admin/logout.php
 * ─────────────────────────────────────────────────────────────────────────────
 * Destroys the admin session and redirects to the login page.
 * ─────────────────────────────────────────────────────────────────────────────
 */

define('ADMIN_GUARD', true);
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';

admin_logout(); // Terminates with redirect
