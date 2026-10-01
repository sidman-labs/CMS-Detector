<?php
/**
 * admin/includes/layout.php
 * ─────────────────────────────────────────────────────────────────────────────
 * Shared HTML shell for every admin page.
 * Call admin_header($title, $active_nav) at top, admin_footer() at bottom.
 * ─────────────────────────────────────────────────────────────────────────────
 */

if (!defined('ADMIN_GUARD')) die('Direct access not permitted.');

/**
 * Output the opening HTML, <head>, sidebar, and topbar.
 *
 * @param string $title      Page <title>
 * @param string $active_nav Which nav item to highlight: 'dashboard'|'scans'|'settings'
 */
function admin_header(string $title, string $active_nav = 'dashboard'): void
{
    $username = $_SESSION['admin_username'] ?? 'Admin';
    $csrf     = csrf_token();
    $base     = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    // Normalise base to admin directory (PHP 7.1+ compatible)
    $admin_base = (substr($base, -6) === '/admin') ? $base : $base . '/admin';

    $nav_items = [
        'dashboard' => ['icon' => '⬡', 'label' => 'Dashboard',   'href' => 'index.php'],
        'scans'     => ['icon' => '◈',  'label' => 'Scan Records', 'href' => 'scans.php'],
        'settings'  => ['icon' => '◎',  'label' => 'Settings',    'href' => 'settings.php'],
    ];
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <meta name="robots" content="noindex,nofollow"/>
  <title><?= htmlspecialchars($title) ?> — <?= ADMIN_APP_NAME ?> Admin</title>

  <!-- Fonts: JetBrains Mono + Syne -->
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@300;400;500;700&family=Syne:wght@400;600;700;800&display=swap" rel="stylesheet"/>

  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

  <style>
  /* ════════════════════════════════════════════════════
     TOKENS
  ════════════════════════════════════════════════════ */
  :root {
    --bg0:       #0d0e11;
    --bg1:       #12141a;
    --bg2:       #181b23;
    --bg3:       #1e2230;
    --border:    rgba(255,255,255,.07);
    --border-hi: rgba(251,191,36,.3);

    --text-1:  #e8eaf0;
    --text-2:  #8b92a8;
    --text-3:  #4a5068;

    --amber:   #fbbf24;
    --amber-d: #d97706;
    --green:   #34d399;
    --red:     #f87171;
    --blue:    #60a5fa;
    --violet:  #a78bfa;

    --sidebar-w: 230px;
    --topbar-h:  60px;

    --radius-sm: 6px;
    --radius-md: 10px;
    --radius-lg: 16px;

    --font-mono: 'JetBrains Mono', monospace;
    --font-ui:   'Syne', sans-serif;

    --t: .18s cubic-bezier(.4,0,.2,1);

    --shadow-card: 0 2px 16px rgba(0,0,0,.5), 0 1px 0 rgba(255,255,255,.04) inset;
  }

  /* ════════════════════════════════════════════════════
     RESET
  ════════════════════════════════════════════════════ */
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  html { -webkit-font-smoothing: antialiased; }
  body {
    font-family: var(--font-mono);
    background: var(--bg0);
    color: var(--text-1);
    min-height: 100vh;
    display: flex;
  }
  a { color: var(--amber); text-decoration: none; }
  button { font-family: inherit; cursor: pointer; }
  input, select { font-family: inherit; }

  /* ════════════════════════════════════════════════════
     SIDEBAR
  ════════════════════════════════════════════════════ */
  .sidebar {
    width: var(--sidebar-w);
    background: var(--bg1);
    border-right: 1px solid var(--border);
    display: flex;
    flex-direction: column;
    position: fixed;
    top: 0; left: 0; bottom: 0;
    z-index: 100;
    transition: transform var(--t);
  }

  .sidebar-logo {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 22px 20px 18px;
    border-bottom: 1px solid var(--border);
  }
  .sidebar-logo-icon {
    font-size: 1.6rem;
    color: var(--amber);
    line-height: 1;
  }
  .sidebar-logo-text {
    font-family: var(--font-ui);
    font-weight: 800;
    font-size: 1.05rem;
    letter-spacing: -.01em;
    color: var(--text-1);
  }
  .sidebar-logo-badge {
    font-size: .6rem;
    background: var(--amber);
    color: #000;
    padding: 1px 5px;
    border-radius: 3px;
    font-weight: 700;
    letter-spacing: .04em;
  }

  .sidebar-nav {
    flex: 1;
    padding: 16px 12px;
    display: flex;
    flex-direction: column;
    gap: 3px;
  }
  .sidebar-nav-label {
    font-size: .65rem;
    letter-spacing: .1em;
    text-transform: uppercase;
    color: var(--text-3);
    padding: 10px 8px 4px;
    font-weight: 500;
  }

  .nav-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 12px;
    border-radius: var(--radius-sm);
    color: var(--text-2);
    font-size: .82rem;
    font-weight: 400;
    transition: background var(--t), color var(--t);
    border: 1px solid transparent;
    white-space: nowrap;
    text-decoration: none;
  }
  .nav-item:hover { background: var(--bg2); color: var(--text-1); text-decoration: none; }
  .nav-item.active {
    background: rgba(251,191,36,.1);
    color: var(--amber);
    border-color: rgba(251,191,36,.2);
  }
  .nav-icon { font-size: 1rem; width: 18px; text-align: center; }

  .sidebar-footer {
    padding: 16px 12px;
    border-top: 1px solid var(--border);
  }
  .sidebar-user {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px;
  }
  .sidebar-avatar {
    width: 32px; height: 32px;
    background: linear-gradient(135deg, var(--amber) 0%, var(--amber-d) 100%);
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: .75rem;
    font-weight: 700;
    color: #000;
    flex-shrink: 0;
  }
  .sidebar-user-info { flex: 1; min-width: 0; }
  .sidebar-user-name {
    font-size: .8rem;
    font-weight: 500;
    color: var(--text-1);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .sidebar-user-role {
    font-size: .68rem;
    color: var(--text-3);
  }
  .sidebar-logout {
    background: transparent;
    border: none;
    color: var(--text-3);
    font-size: .8rem;
    padding: 4px;
    border-radius: 4px;
    transition: color var(--t);
    text-decoration: none;
  }
  .sidebar-logout:hover { color: var(--red); }

  /* ════════════════════════════════════════════════════
     MAIN AREA
  ════════════════════════════════════════════════════ */
  .main-area {
    margin-left: var(--sidebar-w);
    flex: 1;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
  }

  .topbar {
    height: var(--topbar-h);
    background: var(--bg1);
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 28px;
    position: sticky;
    top: 0;
    z-index: 50;
  }
  .topbar-left { display: flex; align-items: center; gap: 14px; }
  .topbar-breadcrumb {
    font-size: .8rem;
    color: var(--text-3);
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .topbar-breadcrumb span { color: var(--text-2); }
  .topbar-title {
    font-family: var(--font-ui);
    font-weight: 700;
    font-size: 1rem;
    color: var(--text-1);
  }
  .topbar-right { display: flex; align-items: center; gap: 12px; }
  .topbar-status {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: .72rem;
    color: var(--text-3);
  }
  .status-dot {
    width: 7px; height: 7px;
    background: var(--green);
    border-radius: 50%;
    box-shadow: 0 0 6px var(--green);
    animation: blink 3s ease-in-out infinite;
  }
  @keyframes blink { 0%,100%{opacity:1} 50%{opacity:.3} }

  .mobile-menu-btn {
    display: none;
    background: transparent;
    border: 1px solid var(--border);
    color: var(--text-2);
    padding: 6px 10px;
    border-radius: var(--radius-sm);
    font-size: .9rem;
    transition: border-color var(--t), color var(--t);
  }
  .mobile-menu-btn:hover { border-color: var(--amber); color: var(--amber); }

  /* ════════════════════════════════════════════════════
     PAGE CONTENT
  ════════════════════════════════════════════════════ */
  .page-content {
    flex: 1;
    padding: 28px 28px 60px;
  }
  .page-header {
    margin-bottom: 28px;
  }
  .page-header h1 {
    font-family: var(--font-ui);
    font-size: 1.5rem;
    font-weight: 700;
    letter-spacing: -.02em;
    margin-bottom: 4px;
  }
  .page-header p {
    font-size: .8rem;
    color: var(--text-3);
  }

  /* ════════════════════════════════════════════════════
     CARDS
  ════════════════════════════════════════════════════ */
  .card {
    background: var(--bg1);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-card);
  }
  .card-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 22px 0;
    margin-bottom: 16px;
  }
  .card-title {
    font-family: var(--font-ui);
    font-weight: 600;
    font-size: .9rem;
    color: var(--text-1);
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .card-title-icon { color: var(--amber); }
  .card-body { padding: 0 22px 22px; }

  /* ════════════════════════════════════════════════════
     STAT CARDS
  ════════════════════════════════════════════════════ */
  .stat-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 24px;
  }
  .stat-card {
    background: var(--bg1);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 22px;
    display: flex;
    flex-direction: column;
    gap: 14px;
    position: relative;
    overflow: hidden;
    transition: border-color var(--t), transform var(--t);
    box-shadow: var(--shadow-card);
  }
  .stat-card:hover { border-color: var(--border-hi); transform: translateY(-2px); }
  .stat-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 2px;
    background: linear-gradient(90deg, transparent, var(--amber), transparent);
    opacity: 0;
    transition: opacity var(--t);
  }
  .stat-card:hover::before { opacity: 1; }

  .stat-header { display: flex; align-items: flex-start; justify-content: space-between; }
  .stat-icon {
    width: 38px; height: 38px;
    border-radius: var(--radius-sm);
    display: flex; align-items: center; justify-content: center;
    font-size: 1rem;
  }
  .stat-icon.amber { background: rgba(251,191,36,.12); }
  .stat-icon.green { background: rgba(52,211,153,.12); }
  .stat-icon.blue  { background: rgba(96,165,250,.12); }
  .stat-icon.violet{ background: rgba(167,139,250,.12); }

  .stat-delta {
    font-size: .7rem;
    padding: 3px 8px;
    border-radius: 999px;
    font-weight: 500;
  }
  .stat-delta.up   { background: rgba(52,211,153,.15); color: var(--green); }
  .stat-delta.flat { background: rgba(139,146,168,.1);  color: var(--text-3); }

  .stat-value {
    font-family: var(--font-ui);
    font-size: 2rem;
    font-weight: 800;
    letter-spacing: -.04em;
    color: var(--text-1);
    line-height: 1;
  }
  .stat-label {
    font-size: .72rem;
    color: var(--text-3);
    text-transform: uppercase;
    letter-spacing: .06em;
  }

  /* ════════════════════════════════════════════════════
     CHARTS ROW
  ════════════════════════════════════════════════════ */
  .charts-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    margin-bottom: 24px;
  }
  .chart-wrap { padding: 22px; position: relative; }
  .chart-canvas-wrap { position: relative; height: 240px; }

  /* ════════════════════════════════════════════════════
     TABLE
  ════════════════════════════════════════════════════ */
  .data-table {
    width: 100%;
    border-collapse: collapse;
    font-size: .78rem;
  }
  .data-table th {
    text-align: left;
    padding: 10px 14px;
    font-size: .65rem;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: var(--text-3);
    border-bottom: 1px solid var(--border);
    font-weight: 500;
    white-space: nowrap;
  }
  .data-table td {
    padding: 11px 14px;
    border-bottom: 1px solid rgba(255,255,255,.03);
    color: var(--text-2);
    vertical-align: middle;
  }
  .data-table tr:last-child td { border-bottom: none; }
  .data-table tbody tr:hover td { background: rgba(255,255,255,.02); color: var(--text-1); }

  /* ════════════════════════════════════════════════════
     BADGES
  ════════════════════════════════════════════════════ */
  .badge {
    display: inline-block;
    font-size: .65rem;
    font-weight: 500;
    padding: 3px 8px;
    border-radius: 4px;
    white-space: nowrap;
    letter-spacing: .03em;
  }
  .badge-amber  { background: rgba(251,191,36,.15); color: var(--amber);  border: 1px solid rgba(251,191,36,.25); }
  .badge-green  { background: rgba(52,211,153,.12); color: var(--green);  border: 1px solid rgba(52,211,153,.2); }
  .badge-blue   { background: rgba(96,165,250,.12); color: var(--blue);   border: 1px solid rgba(96,165,250,.2); }
  .badge-violet { background: rgba(167,139,250,.12);color: var(--violet); border: 1px solid rgba(167,139,250,.2); }
  .badge-gray   { background: rgba(139,146,168,.1); color: var(--text-2); border: 1px solid var(--border); }

  /* ════════════════════════════════════════════════════
     BUTTONS
  ════════════════════════════════════════════════════ */
  .btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    border-radius: var(--radius-sm);
    font-family: var(--font-mono);
    font-size: .78rem;
    font-weight: 500;
    border: none;
    cursor: pointer;
    transition: opacity var(--t), transform var(--t), background var(--t);
    text-decoration: none;
  }
  .btn:hover { opacity: .85; transform: translateY(-1px); text-decoration: none; }
  .btn-primary { background: var(--amber); color: #000; }
  .btn-danger  { background: rgba(248,113,113,.15); color: var(--red); border: 1px solid rgba(248,113,113,.25); }
  .btn-danger:hover { background: rgba(248,113,113,.25); }
  .btn-ghost   { background: rgba(255,255,255,.05); color: var(--text-2); border: 1px solid var(--border); }
  .btn-ghost:hover { border-color: var(--amber); color: var(--amber); }
  .btn-sm { padding: 5px 10px; font-size: .72rem; }

  /* ════════════════════════════════════════════════════
     INPUTS
  ════════════════════════════════════════════════════ */
  .form-input {
    background: var(--bg2);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 9px 12px;
    color: var(--text-1);
    font-family: var(--font-mono);
    font-size: .82rem;
    outline: none;
    transition: border-color var(--t), box-shadow var(--t);
    width: 100%;
  }
  .form-input:focus {
    border-color: var(--amber);
    box-shadow: 0 0 0 3px rgba(251,191,36,.1);
  }
  .form-label {
    display: block;
    font-size: .72rem;
    letter-spacing: .05em;
    text-transform: uppercase;
    color: var(--text-3);
    margin-bottom: 6px;
    font-weight: 500;
  }
  .form-group { margin-bottom: 18px; }

  /* ════════════════════════════════════════════════════
     ALERTS
  ════════════════════════════════════════════════════ */
  .alert {
    padding: 12px 16px;
    border-radius: var(--radius-sm);
    font-size: .8rem;
    margin-bottom: 18px;
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .alert-error  { background: rgba(248,113,113,.1); border: 1px solid rgba(248,113,113,.25); color: var(--red); }
  .alert-success{ background: rgba(52,211,153,.1);  border: 1px solid rgba(52,211,153,.25);  color: var(--green); }
  .alert-info   { background: rgba(96,165,250,.1);  border: 1px solid rgba(96,165,250,.25);  color: var(--blue); }

  /* ════════════════════════════════════════════════════
     PAGINATION
  ════════════════════════════════════════════════════ */
  .pagination {
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 16px 22px;
    border-top: 1px solid var(--border);
    justify-content: space-between;
  }
  .pagination-info { font-size: .72rem; color: var(--text-3); }
  .pagination-btns { display: flex; gap: 4px; }
  .pg-btn {
    padding: 5px 10px;
    border-radius: 4px;
    font-size: .75rem;
    background: transparent;
    border: 1px solid var(--border);
    color: var(--text-2);
    cursor: pointer;
    transition: border-color var(--t), color var(--t), background var(--t);
    font-family: var(--font-mono);
    text-decoration: none;
    display: inline-block;
  }
  .pg-btn:hover, .pg-btn.active {
    border-color: var(--amber);
    color: var(--amber);
    background: rgba(251,191,36,.08);
    text-decoration: none;
  }
  .pg-btn.disabled { opacity: .3; pointer-events: none; }

  /* ════════════════════════════════════════════════════
     PROGRESS BARS
  ════════════════════════════════════════════════════ */
  .progress-list { display: flex; flex-direction: column; gap: 12px; }
  .progress-item { display: flex; flex-direction: column; gap: 6px; }
  .progress-header { display: flex; justify-content: space-between; font-size: .75rem; }
  .progress-label { color: var(--text-2); }
  .progress-pct   { color: var(--amber); font-weight: 500; }
  .progress-bar-bg {
    height: 6px;
    background: rgba(255,255,255,.05);
    border-radius: 3px;
    overflow: hidden;
  }
  .progress-bar-fill {
    height: 100%;
    border-radius: 3px;
    background: linear-gradient(90deg, var(--amber-d), var(--amber));
    transition: width 1.2s cubic-bezier(.4,0,.2,1);
  }

  /* ════════════════════════════════════════════════════
     MISC
  ════════════════════════════════════════════════════ */
  .url-cell {
    max-width: 280px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    font-family: var(--font-mono);
    font-size: .72rem;
    color: var(--blue);
  }
  .ip-cell {
    font-family: var(--font-mono);
    font-size: .72rem;
    color: var(--text-3);
  }
  .ts-cell {
    font-family: var(--font-mono);
    font-size: .7rem;
    color: var(--text-3);
    white-space: nowrap;
  }
  .flag { font-size: 1rem; }

  /* Sidebar overlay (mobile) */
  .sidebar-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.6);
    z-index: 99;
  }

  /* ════════════════════════════════════════════════════
     RESPONSIVE
  ════════════════════════════════════════════════════ */
  @media (max-width: 1100px) {
    .stat-grid { grid-template-columns: repeat(2, 1fr); }
    .charts-row { grid-template-columns: 1fr; }
  }
  @media (max-width: 768px) {
    .sidebar { transform: translateX(-100%); }
    .sidebar.open { transform: translateX(0); }
    .sidebar-overlay { display: block; }
    .main-area { margin-left: 0; }
    .mobile-menu-btn { display: flex; align-items: center; gap: 6px; }
    .stat-grid { grid-template-columns: 1fr 1fr; }
    .page-content { padding: 18px; }
    .topbar { padding: 0 18px; }
  }
  @media (max-width: 480px) {
    .stat-grid { grid-template-columns: 1fr; }
    .data-table th:nth-child(n+4),
    .data-table td:nth-child(n+4) { display: none; }
  }

  /* Animations */
  @keyframes fadeIn { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:none} }
  .anim { animation: fadeIn .3s ease backwards; }
  </style>
</head>
<body>

<!-- ── SIDEBAR OVERLAY (mobile) ── -->
<div class="sidebar-overlay" id="sidebar-overlay" onclick="closeSidebar()"></div>

<!-- ── SIDEBAR ── -->
<aside class="sidebar" id="sidebar">
  <div class="sidebar-logo">
    <div class="sidebar-logo-icon">⬡</div>
    <div>
      <div class="sidebar-logo-text"><?= ADMIN_APP_NAME ?></div>
    </div>
    <span class="sidebar-logo-badge">ADMIN</span>
  </div>

  <nav class="sidebar-nav">
    <div class="sidebar-nav-label">Navigation</div>
    <?php foreach ($nav_items as $key => $item): ?>
    <a href="<?= $admin_base . '/' . $item['href'] ?>"
       class="nav-item <?= $active_nav === $key ? 'active' : '' ?>">
      <span class="nav-icon"><?= $item['icon'] ?></span>
      <?= $item['label'] ?>
    </a>
    <?php endforeach; ?>

    <div class="sidebar-nav-label" style="margin-top:12px">External</div>
    <a href="../index.php" class="nav-item" target="_blank" rel="noopener">
      <span class="nav-icon">↗</span>
      View Site
    </a>
  </nav>

  <div class="sidebar-footer">
    <div class="sidebar-user">
      <div class="sidebar-avatar"><?= strtoupper(substr($username, 0, 1)) ?></div>
      <div class="sidebar-user-info">
        <div class="sidebar-user-name"><?= htmlspecialchars($username) ?></div>
        <div class="sidebar-user-role">Administrator</div>
      </div>
      <a href="logout.php" class="sidebar-logout" title="Logout">⏏</a>
    </div>
  </div>
</aside>

<!-- ── MAIN AREA ── -->
<div class="main-area">

  <!-- Topbar -->
  <div class="topbar">
    <div class="topbar-left">
      <button class="mobile-menu-btn" onclick="openSidebar()">☰ Menu</button>
      <div class="topbar-title"><?= htmlspecialchars($title) ?></div>
    </div>
    <div class="topbar-right">
      <div class="topbar-status">
        <div class="status-dot"></div>
        System Online
      </div>
      <span style="font-size:.72rem;color:var(--text-3)"><?= date('M j, Y') ?></span>
    </div>
  </div>

  <!-- Page content starts here -->
  <div class="page-content">
<?php
} // end admin_header()


/**
 * Output closing HTML for the admin shell.
 */
function admin_footer(): void
{
?>
  </div><!-- /.page-content -->
</div><!-- /.main-area -->

<script>
function openSidebar()  {
  document.getElementById('sidebar').classList.add('open');
  document.getElementById('sidebar-overlay').style.display = 'block';
}
function closeSidebar() {
  document.getElementById('sidebar').classList.remove('open');
  document.getElementById('sidebar-overlay').style.display = 'none';
}

// Auto-dismiss alerts
document.querySelectorAll('.alert[data-auto-dismiss]').forEach(el => {
  setTimeout(() => el.style.display = 'none', parseInt(el.dataset.autoDismiss) || 4000);
});

// Animate progress bars on load
window.addEventListener('load', () => {
  document.querySelectorAll('.progress-bar-fill[data-width]').forEach(bar => {
    setTimeout(() => { bar.style.width = bar.dataset.width; }, 300);
  });
});

// Confirm before delete
document.querySelectorAll('[data-confirm]').forEach(el => {
  el.addEventListener('click', e => {
    if (!confirm(el.dataset.confirm)) e.preventDefault();
  });
});
</script>

</body>
</html>
<?php
} // end admin_footer()
