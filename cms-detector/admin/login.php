<?php
/**
 * admin/login.php
 * ─────────────────────────────────────────────────────────────────────────────
 * Admin login page. Handles form submission and session creation.
 * ─────────────────────────────────────────────────────────────────────────────
 */

define('ADMIN_GUARD', true);
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';

// Redirect already-logged-in users
if (is_logged_in()) {
    header('Location: index.php');
    exit;
}

$error        = '';
$logged_out   = isset($_GET['logged_out']);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Basic brute-force: small sleep on every attempt
    usleep(300_000); // 300ms

    if (attempt_login($username, $password)) {
        header('Location: index.php');
        exit;
    }

    $error = 'Invalid username or password. Please try again.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <meta name="robots" content="noindex,nofollow"/>
  <title>Admin Login — <?= ADMIN_APP_NAME ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@300;400;500;700&family=Syne:wght@700;800&display=swap" rel="stylesheet"/>
  <style>
  :root {
    --bg0:    #0d0e11;
    --bg1:    #12141a;
    --bg2:    #181b23;
    --border: rgba(255,255,255,.07);
    --text-1: #e8eaf0;
    --text-2: #8b92a8;
    --text-3: #4a5068;
    --amber:  #fbbf24;
    --amber-d:#d97706;
    --green:  #34d399;
    --red:    #f87171;
    --t: .18s cubic-bezier(.4,0,.2,1);
  }
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  html { -webkit-font-smoothing: antialiased; }

  body {
    font-family: 'JetBrains Mono', monospace;
    background: var(--bg0);
    color: var(--text-1);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 24px;
    position: relative;
    overflow: hidden;
  }

  /* Grid background */
  body::before {
    content: '';
    position: fixed;
    inset: 0;
    background-image:
      linear-gradient(rgba(251,191,36,.03) 1px, transparent 1px),
      linear-gradient(90deg, rgba(251,191,36,.03) 1px, transparent 1px);
    background-size: 40px 40px;
    pointer-events: none;
  }

  /* Glow */
  body::after {
    content: '';
    position: fixed;
    bottom: -200px; left: 50%;
    transform: translateX(-50%);
    width: 600px; height: 400px;
    background: radial-gradient(ellipse, rgba(251,191,36,.08) 0%, transparent 70%);
    pointer-events: none;
  }

  /* Scanlines */
  .scanlines {
    position: fixed;
    inset: 0;
    background: repeating-linear-gradient(
      0deg,
      transparent,
      transparent 2px,
      rgba(0,0,0,.08) 2px,
      rgba(0,0,0,.08) 4px
    );
    pointer-events: none;
    z-index: 0;
  }

  .login-wrap {
    position: relative;
    z-index: 1;
    width: 100%;
    max-width: 420px;
  }

  /* Logo */
  .login-logo {
    text-align: center;
    margin-bottom: 36px;
  }
  .login-logo-icon {
    font-size: 3rem;
    color: var(--amber);
    display: block;
    margin-bottom: 8px;
    animation: float 4s ease-in-out infinite;
  }
  @keyframes float {
    0%,100% { transform: translateY(0); }
    50%      { transform: translateY(-6px); }
  }
  .login-logo-name {
    font-family: 'Syne', sans-serif;
    font-size: 1.6rem;
    font-weight: 800;
    letter-spacing: -.02em;
  }
  .login-logo-sub {
    font-size: .72rem;
    color: var(--text-3);
    letter-spacing: .1em;
    text-transform: uppercase;
    margin-top: 4px;
  }

  /* Card */
  .login-card {
    background: var(--bg1);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 36px;
    box-shadow: 0 4px 40px rgba(0,0,0,.6), 0 1px 0 rgba(255,255,255,.04) inset;
    animation: fadeUp .4s ease backwards;
  }
  @keyframes fadeUp {
    from { opacity:0; transform: translateY(20px); }
    to   { opacity:1; transform: none; }
  }

  .login-card-header {
    margin-bottom: 28px;
  }
  .login-card-title {
    font-family: 'Syne', sans-serif;
    font-weight: 700;
    font-size: 1.1rem;
    margin-bottom: 4px;
  }
  .login-card-sub {
    font-size: .75rem;
    color: var(--text-3);
  }

  .form-group { margin-bottom: 18px; }
  .form-label {
    display: block;
    font-size: .68rem;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: var(--text-3);
    margin-bottom: 8px;
    font-weight: 500;
  }
  .input-wrap { position: relative; }
  .input-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-3);
    font-size: .9rem;
    pointer-events: none;
  }
  .form-input {
    width: 100%;
    background: var(--bg2);
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 11px 12px 11px 36px;
    color: var(--text-1);
    font-family: 'JetBrains Mono', monospace;
    font-size: .85rem;
    outline: none;
    transition: border-color var(--t), box-shadow var(--t);
  }
  .form-input:focus {
    border-color: var(--amber);
    box-shadow: 0 0 0 3px rgba(251,191,36,.12);
  }
  .form-input::placeholder { color: var(--text-3); }

  .login-btn {
    width: 100%;
    background: var(--amber);
    color: #000;
    font-family: 'JetBrains Mono', monospace;
    font-weight: 700;
    font-size: .88rem;
    border: none;
    border-radius: 8px;
    padding: 12px;
    cursor: pointer;
    transition: opacity var(--t), transform var(--t);
    margin-top: 8px;
    letter-spacing: .02em;
  }
  .login-btn:hover  { opacity: .88; transform: translateY(-1px); }
  .login-btn:active { transform: none; opacity: 1; }

  .alert-error {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 14px;
    border-radius: 8px;
    font-size: .78rem;
    margin-bottom: 20px;
    background: rgba(248,113,113,.1);
    border: 1px solid rgba(248,113,113,.25);
    color: var(--red);
    animation: shake .35s ease;
  }
  @keyframes shake {
    0%,100%{transform:translateX(0)}
    20%,60%{transform:translateX(-6px)}
    40%,80%{transform:translateX(6px)}
  }

  .alert-success {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 14px;
    border-radius: 8px;
    font-size: .78rem;
    margin-bottom: 20px;
    background: rgba(52,211,153,.1);
    border: 1px solid rgba(52,211,153,.25);
    color: var(--green);
  }

  .login-footer {
    text-align: center;
    margin-top: 20px;
    font-size: .72rem;
    color: var(--text-3);
  }
  .login-footer a { color: var(--text-3); }
  .login-footer a:hover { color: var(--amber); }
  </style>
</head>
<body>
<div class="scanlines"></div>

<div class="login-wrap">
  <div class="login-logo">
    <span class="login-logo-icon">⬡</span>
    <div class="login-logo-name"><?= ADMIN_APP_NAME ?></div>
    <div class="login-logo-sub">Admin Control Panel</div>
  </div>

  <div class="login-card">
    <div class="login-card-header">
      <div class="login-card-title">Sign in</div>
      <div class="login-card-sub">Enter your credentials to access the admin panel</div>
    </div>

    <?php if ($error): ?>
    <div class="alert-error">⚠ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($logged_out): ?>
    <div class="alert-success">✓ You have been logged out successfully.</div>
    <?php endif; ?>

    <form method="POST" action="login.php" autocomplete="off">
      <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>"/>

      <div class="form-group">
        <label class="form-label" for="username">Username</label>
        <div class="input-wrap">
          <span class="input-icon">◈</span>
          <input
            type="text"
            id="username"
            name="username"
            class="form-input"
            placeholder="admin"
            value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
            autocomplete="username"
            required
          />
        </div>
      </div>

      <div class="form-group">
        <label class="form-label" for="password">Password</label>
        <div class="input-wrap">
          <span class="input-icon">◎</span>
          <input
            type="password"
            id="password"
            name="password"
            class="form-input"
            placeholder="••••••••"
            autocomplete="current-password"
            required
          />
        </div>
      </div>

      <button type="submit" class="login-btn">Sign In →</button>
    </form>
  </div>

  <div class="login-footer">
    <a href="../index.php">← Back to StackDetect</a>
    &nbsp;·&nbsp; v<?= ADMIN_VERSION ?>
  </div>
</div>
</body>
</html>
