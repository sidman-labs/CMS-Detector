<?php
/**
 * admin/settings.php — Admin Settings
 * ─────────────────────────────────────────────────────────────────────────────
 * Change admin password, view system info, flush history.
 * ─────────────────────────────────────────────────────────────────────────────
 */

define('ADMIN_GUARD', true);
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/storage.php';
require_once __DIR__ . '/includes/layout.php';

require_login();

$msg      = '';
$msg_type = 'success';
$csrf     = csrf_token();

// ── Handle POST ───────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf'] ?? '')) {
        $msg      = 'CSRF verification failed. Please refresh and try again.';
        $msg_type = 'error';
    } else {
        $action = $_POST['action'] ?? '';

        // Generate new password hash
        if ($action === 'gen_hash') {
            $pw = $_POST['new_password'] ?? '';
            if (strlen($pw) < 8) {
                $msg      = 'Password must be at least 8 characters.';
                $msg_type = 'error';
            } else {
                $hash     = password_hash($pw, PASSWORD_BCRYPT, ['cost' => 12]);
                $msg      = 'New hash generated (copy it into config.php):';
                $msg_type = 'hash';
                $new_hash = $hash;
            }
        }

        // Flush scan history
        if ($action === 'flush_history') {
            $files = [SCANS_JSON_FILE, STATS_JSON_FILE];
            foreach ($files as $f) {
                if (file_exists($f)) {
                    file_put_contents($f, $action === 'flush_history' && strpos($f, 'scans_full') !== false ? '[]' : '{}');
                }
            }
            // Also flush the public history
            $pub = __DIR__ . '/../history/scans.json';
            if (file_exists($pub)) file_put_contents($pub, '[]');

            $msg = 'All scan history has been flushed.';
        }
    }
}

// System info
$php_ver   = phpversion();
$curl_ok   = function_exists('curl_init');
$json_ok   = function_exists('json_encode');
$scans_sz  = file_exists(SCANS_JSON_FILE) ? round(filesize(SCANS_JSON_FILE) / 1024, 1) : 0;
$history_writable = is_writable(dirname(SCANS_JSON_FILE));

// MySQL connection test
$db_status = 'N/A';
$db_ok     = true;
if (STORAGE_DRIVER === 'mysql') {
    $db_pdo = mysql_connect_pdo();
    if ($db_pdo === null) {
        $db_status = 'Connection FAILED — check DB_HOST, DB_USER, DB_PASS in config.php';
        $db_ok     = false;
    } else {
        $db_count  = 0;
        try { $db_count = (int) $db_pdo->query("SELECT COUNT(*) FROM `scans`")->fetchColumn(); } catch (Exception $e) {}
        $db_status = 'Connected ✓  (' . number_format($db_count) . ' scan records in DB)';
    }
}

admin_header('Settings', 'settings');
?>

<div class="page-header anim">
  <h1>Settings</h1>
  <p>System configuration and maintenance tools</p>
</div>

<?php if ($msg && $msg_type !== 'hash'): ?>
<div class="alert alert-<?= $msg_type === 'error' ? 'error' : 'success' ?> anim" data-auto-dismiss="5000">
  <?= $msg_type === 'error' ? '⚠' : '✓' ?> <?= htmlspecialchars($msg) ?>
</div>
<?php endif; ?>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;align-items:start">

  <!-- ── Password / Hash Generator ── -->
  <div class="card anim" style="animation-delay:.05s">
    <div class="card-head">
      <div class="card-title"><span class="card-title-icon">◎</span> Password Hash Generator</div>
    </div>
    <div class="card-body">
      <p style="font-size:.78rem;color:var(--text-3);margin-bottom:18px;line-height:1.6">
        Passwords are stored as bcrypt hashes. Enter your desired password below to generate a new hash,
        then paste it into <code style="color:var(--amber);background:rgba(251,191,36,.08);padding:1px 5px;border-radius:3px">config.php</code>.
      </p>

      <form method="POST" action="settings.php">
        <input type="hidden" name="csrf"   value="<?= htmlspecialchars($csrf) ?>"/>
        <input type="hidden" name="action" value="gen_hash"/>

        <div class="form-group">
          <label class="form-label" for="new_password">New Password</label>
          <input type="password" id="new_password" name="new_password"
                 class="form-input" placeholder="Minimum 8 characters" autocomplete="new-password"/>
        </div>
        <div class="form-group">
          <label class="form-label" for="new_password2">Confirm Password</label>
          <input type="password" id="new_password2"
                 class="form-input" placeholder="Repeat password" autocomplete="new-password"/>
        </div>

        <button type="submit" class="btn btn-primary" id="gen-btn">Generate Hash</button>
      </form>

      <?php if (($msg_type ?? '') === 'hash' && !empty($new_hash)): ?>
      <div class="alert alert-info" style="margin-top:18px;flex-direction:column;align-items:flex-start;gap:8px">
        <div style="font-size:.75rem;color:var(--blue)">✓ <?= htmlspecialchars($msg) ?></div>
        <div style="display:flex;gap:8px;width:100%;align-items:center">
          <code id="hash-output" style="font-size:.7rem;word-break:break-all;flex:1;
                background:var(--bg2);padding:8px;border-radius:4px;color:var(--amber)">
            <?= htmlspecialchars($new_hash) ?>
          </code>
          <button onclick="copyHash()" class="btn btn-ghost btn-sm" id="copy-hash-btn">Copy</button>
        </div>
        <div style="font-size:.7rem;color:var(--text-3)">
          Replace the value of <strong>ADMIN_PASSWORD_HASH</strong> in <code>config.php</code>.
        </div>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- ── System Info ── -->
  <div style="display:flex;flex-direction:column;gap:16px">

    <div class="card anim" style="animation-delay:.1s">
      <div class="card-head">
        <div class="card-title"><span class="card-title-icon">⬡</span> System Status</div>
      </div>
      <div class="card-body">
        <?php
        $checks = [
          ['PHP Version',        $php_ver,               true],
          ['cURL Extension',     $curl_ok ? 'Available' : 'Missing (using fallback)', $curl_ok],
          ['JSON Extension',     $json_ok ? 'Available' : 'Missing', $json_ok],
          ['Storage Driver',     STORAGE_DRIVER,         true],
          ['MySQL Database',     $db_status,             $db_ok],
          ['History Writable',   $history_writable ? 'Yes' : 'No — check permissions', $history_writable],
          ['GeoIP Enabled',      GEOIP_ENABLED ? 'Yes' : 'No', true],
          ['Scans File Size',    $scans_sz . ' KB',      true],
          ['Session Name',       SESSION_NAME,           true],
        ];
        ?>
        <table style="width:100%;font-size:.75rem;border-collapse:collapse">
          <?php foreach ($checks as [$label, $value, $ok]): ?>
          <tr>
            <td style="padding:8px 0;color:var(--text-3);border-bottom:1px solid var(--border);
                       width:50%"><?= $label ?></td>
            <td style="padding:8px 0;border-bottom:1px solid var(--border);
                       color:<?= $ok ? 'var(--text-2)' : 'var(--red)' ?>">
              <?= htmlspecialchars((string)$value) ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </table>
      </div>
    </div>

    <!-- Danger zone -->
    <div class="card anim" style="animation-delay:.15s;border-color:rgba(248,113,113,.2)">
      <div class="card-head">
        <div class="card-title" style="color:var(--red)"><span>⚠</span> Danger Zone</div>
      </div>
      <div class="card-body">
        <p style="font-size:.78rem;color:var(--text-3);margin-bottom:16px;line-height:1.6">
          These actions are irreversible. Proceed with caution.
        </p>
        <form method="POST" action="settings.php">
          <input type="hidden" name="csrf"   value="<?= htmlspecialchars($csrf) ?>"/>
          <input type="hidden" name="action" value="flush_history"/>
          <button
            type="submit"
            class="btn btn-danger"
            data-confirm="This will permanently delete ALL scan history. Are you absolutely sure?"
          >
            🗑 Flush All Scan History
          </button>
        </form>
      </div>
    </div>

  </div><!-- /.right col -->

</div><!-- /.grid -->

<!-- ── Quick Reference ── -->
<div class="card anim" style="margin-top:16px;animation-delay:.2s">
  <div class="card-head">
    <div class="card-title"><span class="card-title-icon">◈</span> Quick Reference — config.php</div>
  </div>
  <div class="card-body">
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:20px">
      <?php
      $cfg_items = [
        ['ADMIN_USERNAME',    ADMIN_USERNAME],
        ['STORAGE_DRIVER',    STORAGE_DRIVER],
        ['GEOIP_ENABLED',     GEOIP_ENABLED ? 'true' : 'false'],
        ['FETCH_TIMEOUT',     defined('FETCH_TIMEOUT') ? FETCH_TIMEOUT . 's' : 'N/A'],
        ['RECORDS_PER_PAGE',  RECORDS_PER_PAGE],
        ['SESSION_LIFETIME',  SESSION_LIFETIME . 's'],
      ];
      foreach ($cfg_items as [$k, $v]):
      ?>
      <div style="background:var(--bg2);border:1px solid var(--border);border-radius:8px;padding:14px">
        <div style="font-size:.68rem;color:var(--text-3);letter-spacing:.06em;text-transform:uppercase;margin-bottom:6px"><?= $k ?></div>
        <div style="font-family:var(--font-mono);font-size:.85rem;color:var(--amber)"><?= htmlspecialchars((string)$v) ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<script>
// Confirm passwords match before submit
document.getElementById('gen-btn')?.addEventListener('click', function(e) {
  const p1 = document.getElementById('new_password').value;
  const p2 = document.getElementById('new_password2').value;
  if (p1 !== p2) {
    e.preventDefault();
    alert('Passwords do not match.');
  }
});

function copyHash() {
  const text = document.getElementById('hash-output')?.textContent?.trim();
  if (!text) return;
  navigator.clipboard.writeText(text).then(() => {
    const btn = document.getElementById('copy-hash-btn');
    btn.textContent = '✓ Copied';
    setTimeout(() => btn.textContent = 'Copy', 2000);
  });
}
</script>

<?php admin_footer(); ?>
