-- ═══════════════════════════════════════════════════════════════════════════
-- StackDetect — MySQL / MariaDB Schema  (universal import)
-- ═══════════════════════════════════════════════════════════════════════════
--
-- Works on ANY server or hosting panel without modification.
-- All statements use IF NOT EXISTS / INSERT IGNORE so re-importing is safe.
--
-- ┌─────────────────────────────────────────────────────────────────────────┐
-- │  Shared hosting / cPanel / phpMyAdmin                                   │
-- │    1. Select your database in the left panel                            │
-- │    2. Open the SQL tab                                                  │
-- │    3. Paste this file and click Go — nothing to change                  │
-- ├─────────────────────────────────────────────────────────────────────────┤
-- │  VPS / dedicated / local (full root access)                             │
-- │    Option A — import into an existing database:                         │
-- │      mysql -u root -p my_database < database.sql                        │
-- │    Option B — let MySQL create the database too:                        │
-- │      Uncomment the CREATE DATABASE / USE block below, then run:         │
-- │      mysql -u root -p < database.sql                                    │
-- └─────────────────────────────────────────────────────────────────────────┘
--
-- Tables created:
--   scans           — one row per URL scan result
--   admin_users     — admin panel accounts
--   admin_sessions  — optional persistent session store
--   stats           — cached aggregate counters for the dashboard
-- ═══════════════════════════════════════════════════════════════════════════


-- ── (Optional) Create & select database ──────────────────────────────────────
-- Uncomment ONLY if you have root/super privileges and want MySQL to create
-- the database for you. Leave commented on shared hosting.

-- CREATE DATABASE IF NOT EXISTS `stackdetect`
--   CHARACTER SET utf8mb4
--   COLLATE utf8mb4_unicode_ci;
-- USE `stackdetect`;


-- ── scans ─────────────────────────────────────────────────────────────────────
-- One row per CMS / technology detection scan.

CREATE TABLE IF NOT EXISTS `scans` (
  `id`           INT UNSIGNED   NOT NULL AUTO_INCREMENT  COMMENT 'Auto-increment primary key',
  `url`          VARCHAR(2048)  NOT NULL                 COMMENT 'Full URL that was scanned',
  `cms`          VARCHAR(100)   NOT NULL DEFAULT ''      COMMENT 'Top detected CMS (e.g. WordPress)',
  `framework`    VARCHAR(100)   NOT NULL DEFAULT ''      COMMENT 'Top detected framework (e.g. Laravel)',
  `language`     VARCHAR(100)   NOT NULL DEFAULT ''      COMMENT 'Top detected language (e.g. PHP)',
  `server`       VARCHAR(100)   NOT NULL DEFAULT ''      COMMENT 'Web server software (e.g. Nginx)',
  `plugins`      VARCHAR(500)   NOT NULL DEFAULT ''      COMMENT 'Comma-separated list of detected plugins',
  `ip`           VARCHAR(45)    NOT NULL DEFAULT ''      COMMENT 'Visitor IP address (IPv4 or IPv6)',
  `country`      VARCHAR(100)   NOT NULL DEFAULT ''      COMMENT 'Country name from GeoIP',
  `country_code` VARCHAR(5)     NOT NULL DEFAULT ''      COMMENT 'ISO 3166-1 alpha-2 country code',
  `city`         VARCHAR(100)   NOT NULL DEFAULT ''      COMMENT 'City name from GeoIP',
  `scanned_at`   INT UNSIGNED   NOT NULL                 COMMENT 'Unix timestamp of the scan',

  PRIMARY KEY (`id`),
  INDEX `idx_scanned_at`   (`scanned_at`),
  INDEX `idx_ip`           (`ip`),
  INDEX `idx_cms`          (`cms`),
  INDEX `idx_framework`    (`framework`),
  INDEX `idx_country_code` (`country_code`)

) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Stores one record per CMS/technology detection scan';


-- ── admin_users ───────────────────────────────────────────────────────────────
-- Admin panel user accounts.

CREATE TABLE IF NOT EXISTS `admin_users` (
  `id`           INT UNSIGNED           NOT NULL AUTO_INCREMENT  COMMENT 'Auto-increment primary key',
  `username`     VARCHAR(64)            NOT NULL                 COMMENT 'Unique login username',
  `password`     VARCHAR(255)           NOT NULL                 COMMENT 'bcrypt hashed password',
  `email`        VARCHAR(255)           NOT NULL DEFAULT ''      COMMENT 'Optional contact email',
  `role`         ENUM('admin','viewer') NOT NULL DEFAULT 'admin' COMMENT 'Access role',
  `last_login`   INT UNSIGNED                    DEFAULT NULL    COMMENT 'Unix timestamp of last successful login',
  `created_at`   INT UNSIGNED           NOT NULL                 COMMENT 'Unix timestamp of account creation',
  `is_active`    TINYINT(1)             NOT NULL DEFAULT 1       COMMENT '1 = active, 0 = disabled',

  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_username` (`username`)

) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Admin panel user accounts';

-- Default admin user — safe to re-import (INSERT IGNORE skips if already exists)
-- Username : admin
-- Password : admin123
-- ⚠  Change this password immediately after first login (Admin → Settings).
INSERT IGNORE INTO `admin_users`
  (`username`, `password`, `email`, `role`, `created_at`, `is_active`)
VALUES (
  'admin',
  '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
  '',
  'admin',
  UNIX_TIMESTAMP(),
  1
);


-- ── admin_sessions ────────────────────────────────────────────────────────────
-- Optional persistent session store.
-- Not required when using PHP's default file-based sessions.

CREATE TABLE IF NOT EXISTS `admin_sessions` (
  `id`         VARCHAR(128) NOT NULL  COMMENT 'Session ID (PHP session_id())',
  `data`       TEXT         NOT NULL  COMMENT 'Serialized session payload',
  `created_at` INT UNSIGNED NOT NULL  COMMENT 'Unix timestamp of session creation',
  `updated_at` INT UNSIGNED NOT NULL  COMMENT 'Unix timestamp of last activity',

  PRIMARY KEY (`id`),
  INDEX `idx_updated_at` (`updated_at`)

) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Optional persistent admin session storage';


-- ── stats ─────────────────────────────────────────────────────────────────────
-- Pre-computed aggregate counters so the dashboard avoids full-table scans.
-- Updated automatically by the PHP storage layer.

CREATE TABLE IF NOT EXISTS `stats` (
  `key`        VARCHAR(64)  NOT NULL           COMMENT 'Counter name (e.g. total_scans)',
  `value`      BIGINT       NOT NULL DEFAULT 0 COMMENT 'Current counter value',
  `updated_at` INT UNSIGNED NOT NULL           COMMENT 'Unix timestamp of last update',

  PRIMARY KEY (`key`)

) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Cached aggregate statistics for the admin dashboard';

-- Seed default counters — INSERT IGNORE skips rows that already exist
INSERT IGNORE INTO `stats` (`key`, `value`, `updated_at`) VALUES
  ('total_scans', 0, UNIX_TIMESTAMP()),
  ('unique_ips',  0, UNIX_TIMESTAMP());


-- ── Demo data (optional) ──────────────────────────────────────────────────────
-- Uncomment the block below to pre-populate the dashboard with sample scans.

/*
INSERT INTO `scans`
  (`url`, `cms`, `framework`, `language`, `server`, `plugins`, `ip`, `country`, `country_code`, `city`, `scanned_at`)
VALUES
  ('https://wordpress.org',      'WordPress', 'jQuery',    'PHP',     'Nginx',      'WooCommerce, Yoast SEO',      '185.125.190.17',  'United States', 'US', 'San Francisco', UNIX_TIMESTAMP() -  3600),
  ('https://drupal.org',         'Drupal',    '',          'PHP',     'Nginx',      'Cloudflare',                  '23.185.0.1',      'United States', 'US', 'Portland',      UNIX_TIMESTAMP() -  7200),
  ('https://laravel.com',        '',          'Laravel',   'PHP',     'Nginx',      'Cloudflare',                  '104.21.80.1',     'United States', 'US', 'San Jose',      UNIX_TIMESTAMP() - 10800),
  ('https://vuejs.org',          '',          'Vue.js',    'Node.js', 'Cloudflare', 'Google Analytics',            '151.101.1.195',   'United States', 'US', 'New York',      UNIX_TIMESTAMP() - 14400),
  ('https://getbootstrap.com',   '',          'Bootstrap', 'Node.js', 'GitHub',     '',                            '185.199.108.153', 'United States', 'US', 'San Francisco', UNIX_TIMESTAMP() - 18000),
  ('https://django-project.com', '',          'Django',    'Python',  'Nginx',      '',                            '34.100.182.96',   'Germany',       'DE', 'Frankfurt',     UNIX_TIMESTAMP() - 21600),
  ('https://shopify.com',        'Shopify',   '',          'Ruby',    'Cloudflare', 'Google Analytics, GTM',       '23.227.38.74',    'Canada',        'CA', 'Ottawa',        UNIX_TIMESTAMP() - 25200),
  ('https://joomla.org',         'Joomla',    '',          'PHP',     'Apache',     '',                            '217.160.0.218',   'Germany',       'DE', 'Berlin',        UNIX_TIMESTAMP() - 28800),
  ('https://ghost.org',          'Ghost',     'Node.js',   'Node.js', 'Nginx',      '',                            '104.18.11.1',     'United Kingdom','GB', 'London',        UNIX_TIMESTAMP() - 32400),
  ('https://magento.com',        'Magento',   '',          'PHP',     'Nginx',      'Cloudflare, Google Analytics','52.92.36.1',      'Australia',     'AU', 'Sydney',        UNIX_TIMESTAMP() - 36000);
*/


-- ═══════════════════════════════════════════════════════════════════════════
-- Verify installation — run this after import to confirm all tables exist:
--   SHOW TABLES;
--
-- Expected output:
--   admin_sessions
--   admin_users
--   scans
--   stats
-- ═══════════════════════════════════════════════════════════════════════════
