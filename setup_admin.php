<?php
/**
 * setup_admin.php
 * ─────────────────────────────────────────────────────────────────────────────
 * Standalone admin-user manager.
 * Protected by a one-time setup token stored in admin/.setup_token
 * Writes to MySQL admin_users table when STORAGE_DRIVER = 'mysql',
 * otherwise falls back to admin/users.json.
 * ─────────────────────────────────────────────────────────────────────────────
 */

define('ADMIN_GUARD', true);
require_once __DIR__ . '/config.php';

// Load storage functions so we can use mysql_* helpers
require_once __DIR__ . '/admin/includes/storage.php';

define('USERS_FILE', __DIR__ . '/admin/users.json');
define('TOKEN_FILE', __DIR__ . '/admin/.setup_token');
session_start();

// ── Determine storage mode ────────────────────────────────────────────────────
$use_mysql = (defined('STORAGE_DRIVER') && STORAGE_DRIVER === 'mysql' && mysql_available());

// ── Token bootstrap ───────────────────────────────────────────────────────────
if (!file_exists(TOKEN_FILE)) {
    $token = bin2hex(random_bytes(16));
    file_put_contents(TOKEN_FILE, $token);
} else {
    $token = trim(file_get_contents(TOKEN_FILE));
}

// ── JSON fallback helpers ─────────────────────────────────────────────────────
function json_load_users(): array {
    if (!file_exists(USERS_FILE)) return [];
    $raw = json_decode(file_get_contents(USERS_FILE), true);
    return is_array($raw) ? $raw : [];
}

function json_save_users(array $users): void {
    file_put_contents(USERS_FILE, json_encode(array_values($users), JSON_PRETTY_PRINT));
}

// ── Auth check ────────────────────────────────────────────────────────────────
$authed = !empty($_SESSION['setup_authed']);
$msg    = '';
$error  = '';

// ── Handle POST actions ───────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Login
    if ($action === 'login') {
        if (hash_equals($token, trim($_POST['setup_token'] ?? ''))) {
            $_SESSION['setup_authed'] = true;
            $authed = true;
            $msg = 'Access granted.';
        } else {
            $error = 'Invalid setup token.';
        }
    }

    // Logout
    elseif ($action === 'logout' && $authed) {
        $_SESSION['setup_authed'] = false;
        $authed = false;
        $msg = 'Signed out.';
    }

    // Add user
    elseif ($action === 'add' && $authed) {
        $u     = trim($_POST['username'] ?? '');
        $p     = $_POST['password'] ?? '';
        $email = trim($_POST['email'] ?? '');
        $role  = in_array($_POST['role'] ?? '', ['admin','viewer']) ? $_POST['role'] : 'admin';

        if ($u === '' || $p === '') {
            $error = 'Username and password are required.';
        } elseif (strlen($p) < 6) {
            $error = 'Password must be at least 6 characters.';
        } else {
            if ($use_mysql) {
                $existing = mysql_list_admin_users();
                $exists = false;
                foreach ($existing as $row) {
                    if (strtolower($row['username']) === strtolower($u)) { $exists = true; break; }
                }
                if ($exists) {
                    $error = "Username \"$u\" already exists in the database.";
                } elseif (mysql_add_admin_user($u, $p, $email, $role)) {
                    $msg = "User \"$u\" added to MySQL admin_users table.";
                } else {
                    $error = "Failed to insert user into database. Check DB connection.";
                }
            } else {
                $users = json_load_users();
                $exists = false;
                foreach ($users as $usr) {
                    if (strtolower($usr['username']) === strtolower($u)) { $exists = true; break; }
                }
                if ($exists) {
                    $error = "Username \"$u\" already exists.";
                } else {
                    $users[] = ['username' => $u, 'hash' => password_hash($p, PASSWORD_BCRYPT)];
                    json_save_users($users);
                    $msg = "User \"$u\" added to users.json (JSON fallback mode).";
                }
            }
        }
    }

    // Update password
    elseif ($action === 'update' && $authed) {
        $u  = trim($_POST['username'] ?? '');
        $p  = $_POST['password'] ?? '';
        $p2 = $_POST['password2'] ?? '';
        if ($p !== $p2) {
            $error = 'Passwords do not match.';
        } elseif (strlen($p) < 6) {
            $error = 'Password must be at least 6 characters.';
        } else {
            if ($use_mysql) {
                if (mysql_update_admin_password($u, $p)) {
                    $msg = "Password for \"$u\" updated in database.";
                } else {
                    $error = "User \"$u\" not found or DB error.";
                }
            } else {
                $users = json_load_users();
                $found = false;
                foreach ($users as &$usr) {
                    if ($usr['username'] === $u) {
                        $usr['hash'] = password_hash($p, PASSWORD_BCRYPT);
                        $found = true; break;
                    }
                }
                unset($usr);
                if ($found) {
                    json_save_users($users);
                    $msg = "Password for \"$u\" updated.";
                } else {
                    $error = "User \"$u\" not found.";
                }
            }
        }
    }

    // Toggle active/inactive
    elseif ($action === 'toggle' && $authed) {
        $id        = (int) ($_POST['user_id'] ?? 0);
        $is_active = (int) ($_POST['is_active'] ?? 0);
        if ($use_mysql && $id) {
            mysql_toggle_admin_user($id, $is_active);
            $msg = 'User status updated.';
        }
    }

    // Delete user
    elseif ($action === 'delete' && $authed) {
        if ($use_mysql) {
            $id = (int) ($_POST['user_id'] ?? 0);
            $all = mysql_list_admin_users();
            if (count($all) <= 1) {
                $error = 'Cannot delete the last admin user.';
            } elseif ($id && mysql_delete_admin_user($id)) {
                $msg = 'User deleted from database.';
            } else {
                $error = 'Delete failed.';
            }
        } else {
            $u = trim($_POST['username'] ?? '');
            $users = json_load_users();
            if (count($users) <= 1) {
                $error = 'Cannot delete the last admin user.';
            } else {
                $filtered = [];
                foreach ($users as $usr) {
                    if ($usr['username'] !== $u) $filtered[] = $usr;
                }
                json_save_users($filtered);
                $msg = "User \"$u\" deleted.";
            }
        }
    }

    // Sync config.php admin → storage
    elseif ($action === 'import_config' && $authed) {
        if ($use_mysql) {
            $existing = mysql_list_admin_users();
            $exists = false;
            foreach ($existing as $row) {
                if ($row['username'] === ADMIN_USERNAME) { $exists = true; break; }
            }
            if ($exists) {
                $msg = 'Config.php admin already exists in the database.';
            } else {
                // Insert with the existing bcrypt hash from config.php
                $pdo_import = mysql_connect_pdo();
                if ($pdo_import) {
                    try {
                        $stmt = $pdo_import->prepare(
                            "INSERT INTO `admin_users` (username, password, email, role, created_at, is_active) VALUES (?, ?, '', 'admin', ?, 1)"
                        );
                        $stmt->execute([ADMIN_USERNAME, ADMIN_PASSWORD_HASH, time()]);
                        $msg = 'Config.php admin imported into database.';
                    } catch (Exception $e) {
                        $error = 'DB insert failed: ' . $e->getMessage();
                    }
                } else {
                    $error = 'Cannot connect to database.';
                }
            }
        } else {
            $users = json_load_users();
            $exists = false;
            foreach ($users as $usr) {
                if ($usr['username'] === ADMIN_USERNAME) { $exists = true; break; }
            }
            if (!$exists) {
                $users[] = ['username' => ADMIN_USERNAME, 'hash' => ADMIN_PASSWORD_HASH];
                json_save_users($users);
                $msg = 'Config.php admin imported into users.json.';
            } else {
                $msg = 'Config.php admin already exists in users.json.';
            }
        }
    }
}

// ── Load current user list for display ───────────────────────────────────────
$users = $use_mysql ? mysql_list_admin_users() : json_load_users();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <meta name="robots" content="noindex,nofollow"/>
  <title>Admin Setup — CMS Detector</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;700&family=Syne:wght@700;800&display=swap" rel="stylesheet"/>
  <style>
  :root {
    --bg0:    #0d0e11;
    --bg1:    #12141a;
    --bg2:    #181b23;
    --bg3:    #1e2130;
    --border: rgba(255,255,255,.08);
    --text-1: #e8eaf0;
    --text-2: #8b92a8;
    --text-3: #4a5068;
    --amber:  #fbbf24;
    --green:  #34d399;
    --red:    #f87171;
    --blue:   #60a5fa;
    --t: .18s cubic-bezier(.4,0,.2,1);
  }
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  html { -webkit-font-smoothing: antialiased; }
  body {
    font-family: 'JetBrains Mono', monospace;
    background: var(--bg0);
    color: var(--text-1);
    min-height: 100vh;
    padding: 40px 24px 80px;
    position: relative;
  }
  body::before {
    content: '';
    position: fixed; inset: 0;
    background-image:
      linear-gradient(rgba(251,191,36,.025) 1px, transparent 1px),
      linear-gradient(90deg, rgba(251,191,36,.025) 1px, transparent 1px);
    background-size: 40px 40px;
    pointer-events: none;
  }
  .wrap { position: relative; z-index: 1; max-width: 800px; margin: 0 auto; }

  .page-header {
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 12px;
    margin-bottom: 36px; padding-bottom: 24px;
    border-bottom: 1px solid var(--border);
  }
  .page-logo { display: flex; align-items: center; gap: 12px; }
  .page-logo-icon { font-size: 2rem; color: var(--amber); }
  .page-logo-name { font-family: 'Syne', sans-serif; font-size: 1.4rem; font-weight: 800; letter-spacing: -.02em; }
  .page-logo-sub { font-size: .65rem; color: var(--text-3); letter-spacing: .1em; text-transform: uppercase; margin-top: 2px; }
  .page-actions { display: flex; gap: 8px; align-items: center; }

  .card { background: var(--bg1); border: 1px solid var(--border); border-radius: 14px; padding: 28px; box-shadow: 0 4px 32px rgba(0,0,0,.5); margin-bottom: 24px; }
  .card-title { font-family: 'Syne', sans-serif; font-size: 1rem; font-weight: 700; margin-bottom: 20px; display: flex; align-items: center; gap: 8px; }
  .card-title-icon { color: var(--amber); }

  .db-badge {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 4px 12px; border-radius: 999px; font-size: .7rem; font-weight: 700;
    letter-spacing: .05em; text-transform: uppercase;
  }
  .db-badge.mysql  { background: rgba(52,211,153,.12); color: var(--green); border: 1px solid rgba(52,211,153,.3); }
  .db-badge.json   { background: rgba(251,191,36,.12); color: var(--amber); border: 1px solid rgba(251,191,36,.3); }

  .token-box {
    background: var(--bg2); border: 1px solid rgba(251,191,36,.25); border-radius: 8px;
    padding: 14px 18px; margin-bottom: 20px; font-size: .8rem; color: var(--text-2); line-height: 1.7;
  }
  .token-box code { display: block; margin-top: 6px; color: var(--amber); font-size: .9rem; letter-spacing: .05em; word-break: break-all; }

  .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px; }
  .form-row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 14px; margin-bottom: 14px; }
  @media (max-width: 560px) { .form-row, .form-row-3 { grid-template-columns: 1fr; } }
  .form-group { margin-bottom: 14px; }
  .form-label { display: block; font-size: .65rem; letter-spacing: .09em; text-transform: uppercase; color: var(--text-3); margin-bottom: 6px; font-weight: 500; }
  .form-input, .form-select {
    width: 100%; background: var(--bg2); border: 1px solid var(--border); border-radius: 8px;
    padding: 10px 14px; color: var(--text-1); font-family: 'JetBrains Mono', monospace;
    font-size: .85rem; outline: none; transition: border-color var(--t), box-shadow var(--t);
  }
  .form-input:focus, .form-select:focus { border-color: var(--amber); box-shadow: 0 0 0 3px rgba(251,191,36,.1); }
  .form-input::placeholder { color: var(--text-3); }
  .form-select option { background: var(--bg2); }

  .btn { display: inline-flex; align-items: center; gap: 6px; font-family: 'JetBrains Mono', monospace; font-size: .8rem; font-weight: 700; border: none; border-radius: 7px; padding: 9px 18px; cursor: pointer; transition: opacity var(--t), transform var(--t); text-decoration: none; white-space: nowrap; }
  .btn:hover  { opacity: .85; transform: translateY(-1px); }
  .btn:active { transform: none; opacity: 1; }
  .btn-primary { background: var(--amber); color: #000; }
  .btn-danger  { background: rgba(248,113,113,.15); color: var(--red);   border: 1px solid rgba(248,113,113,.3); }
  .btn-ghost   { background: transparent; color: var(--text-2); border: 1px solid var(--border); }
  .btn-blue    { background: rgba(96,165,250,.15); color: var(--blue);  border: 1px solid rgba(96,165,250,.3); }
  .btn-green   { background: rgba(52,211,153,.15); color: var(--green); border: 1px solid rgba(52,211,153,.3); }
  .btn-sm      { padding: 6px 12px; font-size: .72rem; }

  .alert { display: flex; align-items: flex-start; gap: 10px; padding: 12px 16px; border-radius: 8px; font-size: .8rem; margin-bottom: 20px; line-height: 1.5; }
  .alert-success { background: rgba(52,211,153,.08);  border: 1px solid rgba(52,211,153,.25); color: var(--green); }
  .alert-error   { background: rgba(248,113,113,.08); border: 1px solid rgba(248,113,113,.25); color: var(--red); }
  .alert-info    { background: rgba(96,165,250,.08);  border: 1px solid rgba(96,165,250,.25); color: var(--blue); }
  .alert-warn    { background: rgba(251,191,36,.08);  border: 1px solid rgba(251,191,36,.25); color: var(--amber); }

  .user-table { width: 100%; border-collapse: collapse; }
  .user-table th { font-size: .65rem; letter-spacing: .09em; text-transform: uppercase; color: var(--text-3); padding: 0 0 12px 0; text-align: left; border-bottom: 1px solid var(--border); }
  .user-table td { padding: 13px 0; border-bottom: 1px solid var(--border); vertical-align: middle; font-size: .85rem; }
  .user-table tr:last-child td { border-bottom: none; }

  .badge { display: inline-block; padding: 2px 10px; border-radius: 999px; font-size: .62rem; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; }
  .badge-amber  { background: rgba(251,191,36,.15); color: var(--amber); border: 1px solid rgba(251,191,36,.25); }
  .badge-green  { background: rgba(52,211,153,.15); color: var(--green); border: 1px solid rgba(52,211,153,.25); }
  .badge-gray   { background: rgba(255,255,255,.05); color: var(--text-3); border: 1px solid var(--border); }

  .td-actions { display: flex; gap: 6px; justify-content: flex-end; flex-wrap: wrap; }

  .edit-panel { background: var(--bg2); border: 1px solid rgba(251,191,36,.2); border-radius: 10px; padding: 20px; margin-top: 8px; display: none; }
  .edit-panel.open { display: block; animation: fadeIn .2s ease; }
  @keyframes fadeIn { from { opacity:0; transform:translateY(-6px); } to { opacity:1; transform:none; } }
  .edit-panel-title { font-size: .75rem; color: var(--amber); letter-spacing: .06em; text-transform: uppercase; margin-bottom: 14px; font-weight: 700; }

  .login-wrap { max-width: 440px; margin: 80px auto 0; }
  .login-hint { font-size: .75rem; color: var(--text-3); margin-top: 10px; line-height: 1.6; }

  .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
  @media (max-width: 480px) { .info-grid { grid-template-columns: 1fr; } }
  .info-item { background: var(--bg2); border: 1px solid var(--border); border-radius: 8px; padding: 12px 16px; font-size: .78rem; }
  .info-item-label { color: var(--text-3); font-size: .65rem; letter-spacing: .08em; text-transform: uppercase; margin-bottom: 4px; }
  .info-item-value { color: var(--amber); word-break: break-all; }
  </style>
</head>
<body>
<div class="wrap">

  <div class="page-header">
    <div class="page-logo">
      <span class="page-logo-icon">⬡</span>
      <div>
        <div class="page-logo-name">CMS Detector</div>
        <div class="page-logo-sub">Admin User Setup</div>
      </div>
    </div>
    <?php if ($authed): ?>
    <div class="page-actions">
      <?php if ($use_mysql): ?>
      <span class="db-badge mysql">● MySQL</span>
      <?php else: ?>
      <span class="db-badge json">● JSON Fallback</span>
      <?php endif; ?>
      <a href="admin/" class="btn btn-ghost btn-sm">→ Admin Panel</a>
      <form method="POST" style="display:inline">
        <input type="hidden" name="action" value="logout"/>
        <button type="submit" class="btn btn-ghost btn-sm">Sign Out</button>
      </form>
    </div>
    <?php endif; ?>
  </div>

  <?php if ($msg): ?>
  <div class="alert alert-success">✓ <?= htmlspecialchars($msg) ?></div>
  <?php endif; ?>
  <?php if ($error): ?>
  <div class="alert alert-error">⚠ <?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <?php if (!$authed): ?>
  <!-- ── TOKEN LOGIN ── -->
  <div class="login-wrap">
    <div class="card">
      <div class="card-title"><span class="card-title-icon">🔑</span> Enter Setup Token</div>
      <div class="token-box">
        Your setup token is stored in:<br>
        <code>cms-detector/admin/.setup_token</code>
        Open that file, copy the token, and paste it below.
      </div>
      <form method="POST" autocomplete="off">
        <input type="hidden" name="action" value="login"/>
        <div class="form-group">
          <label class="form-label" for="setup_token">Setup Token</label>
          <input type="text" id="setup_token" name="setup_token" class="form-input"
                 placeholder="Paste token here" required autocomplete="off"/>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%">Unlock Setup →</button>
      </form>
      <p class="login-hint">This page manages admin accounts directly in the database.<br>
      The token never changes unless you delete <code>admin/.setup_token</code>.</p>
    </div>
  </div>

  <?php else: ?>

  <!-- ── DB STATUS ── -->
  <?php if (!$use_mysql): ?>
  <div class="alert alert-warn">
    ⚠ <strong>MySQL unavailable</strong> — operating in JSON fallback mode.
    Users saved here will be stored in <code>admin/users.json</code> and only
    used if the database is still unreachable at login time.
    Check your DB credentials in <code>config.php</code>.
  </div>
  <?php endif; ?>

  <!-- ── CURRENT USERS TABLE ── -->
  <div class="card">
    <div class="card-title">
      <span class="card-title-icon">👥</span>
      Admin Users
      <?php if ($use_mysql): ?>
      <small style="font-family:monospace;font-size:.65rem;color:var(--text-3);font-weight:400">(MySQL · admin_users table)</small>
      <?php else: ?>
      <small style="font-family:monospace;font-size:.65rem;color:var(--text-3);font-weight:400">(JSON fallback · admin/users.json)</small>
      <?php endif; ?>
    </div>

    <?php if (empty($users)): ?>
    <div class="alert alert-info">
      ℹ No users found.
      <form method="POST" style="display:inline; margin-left:12px;">
        <input type="hidden" name="action" value="import_config"/>
        <button type="submit" class="btn btn-blue btn-sm">Import admin from config.php</button>
      </form>
    </div>
    <?php else: ?>

    <table class="user-table">
      <thead>
        <tr>
          <th>Username</th>
          <?php if ($use_mysql): ?><th>Email</th><th>Role</th><th>Status</th><?php endif; ?>
          <th style="text-align:right">Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($users as $i => $usr):
        // Normalise: mysql rows use 'username'; json uses 'username'
        $uname = $usr['username'] ?? '';
        $uid   = isset($usr['id']) ? (int)$usr['id'] : 0;
        $role  = $usr['role'] ?? 'admin';
        $active= isset($usr['is_active']) ? (int)$usr['is_active'] : 1;
        $email = $usr['email'] ?? '';
      ?>
        <tr>
          <td>
            <strong><?= htmlspecialchars($uname) ?></strong>
            <?php if ($i === 0): ?>
            <span class="badge badge-amber" style="margin-left:8px">primary</span>
            <?php endif; ?>
          </td>
          <?php if ($use_mysql): ?>
          <td style="color:var(--text-2);font-size:.8rem"><?= htmlspecialchars($email ?: '—') ?></td>
          <td>
            <span class="badge <?= $role === 'admin' ? 'badge-amber' : 'badge-gray' ?>">
              <?= htmlspecialchars($role) ?>
            </span>
          </td>
          <td>
            <span class="badge <?= $active ? 'badge-green' : 'badge-gray' ?>">
              <?= $active ? 'Active' : 'Disabled' ?>
            </span>
          </td>
          <?php endif; ?>
          <td>
            <div class="td-actions">
              <button class="btn btn-blue btn-sm"
                onclick="togglePanel('edit-<?= $i ?>')">✏ Password</button>
              <?php if ($use_mysql && count($users) > 1): ?>
              <form method="POST" onsubmit="return confirm('Toggle status for \'<?= htmlspecialchars($uname) ?>\'?')">
                <input type="hidden" name="action" value="toggle"/>
                <input type="hidden" name="user_id" value="<?= $uid ?>"/>
                <input type="hidden" name="is_active" value="<?= $active ? 0 : 1 ?>"/>
                <button type="submit" class="btn btn-ghost btn-sm"><?= $active ? '⊘ Disable' : '✓ Enable' ?></button>
              </form>
              <?php endif; ?>
              <?php if (count($users) > 1): ?>
              <form method="POST" onsubmit="return confirm('Permanently delete \'<?= htmlspecialchars($uname) ?>\'?')">
                <input type="hidden" name="action" value="delete"/>
                <?php if ($use_mysql): ?>
                <input type="hidden" name="user_id" value="<?= $uid ?>"/>
                <?php else: ?>
                <input type="hidden" name="username" value="<?= htmlspecialchars($uname) ?>"/>
                <?php endif; ?>
                <button type="submit" class="btn btn-danger btn-sm">✕ Delete</button>
              </form>
              <?php endif; ?>
            </div>

            <!-- Inline change password -->
            <div class="edit-panel" id="edit-<?= $i ?>">
              <div class="edit-panel-title">Change Password — <?= htmlspecialchars($uname) ?></div>
              <form method="POST">
                <input type="hidden" name="action" value="update"/>
                <input type="hidden" name="username" value="<?= htmlspecialchars($uname) ?>"/>
                <div class="form-row">
                  <div class="form-group">
                    <label class="form-label">New Password</label>
                    <input type="password" name="password" class="form-input" placeholder="min. 6 characters" required/>
                  </div>
                  <div class="form-group">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" name="password2" class="form-input" placeholder="repeat password" required/>
                  </div>
                </div>
                <div style="display:flex;gap:8px">
                  <button type="submit" class="btn btn-primary btn-sm">Save Password</button>
                  <button type="button" class="btn btn-ghost btn-sm"
                    onclick="togglePanel('edit-<?= $i ?>')">Cancel</button>
                </div>
              </form>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>

    <div style="margin-top:16px">
      <form method="POST" style="display:inline">
        <input type="hidden" name="action" value="import_config"/>
        <button type="submit" class="btn btn-ghost btn-sm">↓ Import config.php admin</button>
      </form>
    </div>
    <?php endif; ?>
  </div>

  <!-- ── ADD NEW USER ── -->
  <div class="card">
    <div class="card-title"><span class="card-title-icon">➕</span> Add New Admin User</div>
    <form method="POST" autocomplete="off">
      <input type="hidden" name="action" value="add"/>
      <?php if ($use_mysql): ?>
      <div class="form-row-3">
        <div class="form-group">
          <label class="form-label" for="new_user">Username *</label>
          <input type="text" id="new_user" name="username" class="form-input"
                 placeholder="e.g. john" autocomplete="off" required/>
        </div>
        <div class="form-group">
          <label class="form-label" for="new_pass">Password *</label>
          <input type="password" id="new_pass" name="password" class="form-input"
                 placeholder="min. 6 chars" autocomplete="new-password" required/>
        </div>
        <div class="form-group">
          <label class="form-label" for="new_role">Role</label>
          <select id="new_role" name="role" class="form-select">
            <option value="admin">admin</option>
            <option value="viewer">viewer</option>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label" for="new_email">Email (optional)</label>
        <input type="email" id="new_email" name="email" class="form-input"
               placeholder="user@example.com" autocomplete="off"/>
      </div>
      <?php else: ?>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label" for="new_user">Username *</label>
          <input type="text" id="new_user" name="username" class="form-input"
                 placeholder="e.g. john" autocomplete="off" required/>
        </div>
        <div class="form-group">
          <label class="form-label" for="new_pass">Password *</label>
          <input type="password" id="new_pass" name="password" class="form-input"
                 placeholder="min. 6 chars" autocomplete="new-password" required/>
        </div>
      </div>
      <?php endif; ?>
      <button type="submit" class="btn btn-green">+ Add User</button>
    </form>
  </div>

  <!-- ── DB INFO ── -->
  <div class="card">
    <div class="card-title"><span class="card-title-icon">ℹ</span> Connection Info</div>
    <div class="info-grid">
      <div class="info-item">
        <div class="info-item-label">Storage Driver</div>
        <div class="info-item-value"><?= STORAGE_DRIVER ?></div>
      </div>
      <div class="info-item">
        <div class="info-item-label">MySQL Available</div>
        <div class="info-item-value" style="color:<?= $use_mysql ? 'var(--green)' : 'var(--red)' ?>">
          <?= $use_mysql ? 'YES — connected' : 'NO — check credentials' ?>
        </div>
      </div>
      <?php if (defined('DB_HOST')): ?>
      <div class="info-item">
        <div class="info-item-label">DB Host</div>
        <div class="info-item-value"><?= htmlspecialchars(DB_HOST) ?></div>
      </div>
      <div class="info-item">
        <div class="info-item-label">DB Name</div>
        <div class="info-item-value"><?= htmlspecialchars(DB_NAME) ?></div>
      </div>
      <?php endif; ?>
    </div>
    <div style="margin-top:16px; font-size:.78rem; color:var(--text-2); line-height:1.8;">
      <strong style="color:var(--text-1)">Login priority:</strong>
      MySQL <code>admin_users</code> table → <code>admin/users.json</code> → <code>config.php</code> constants
    </div>
  </div>

  <?php endif; ?>

</div>

<script>
function togglePanel(id) {
  var el = document.getElementById(id);
  if (!el) return;
  var isOpen = el.classList.contains('open');
  document.querySelectorAll('.edit-panel.open').forEach(function(p) { p.classList.remove('open'); });
  if (!isOpen) el.classList.add('open');
}
</script>
</body>
</html>
