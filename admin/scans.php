<?php
/**
 * admin/scans.php — Scan Records
 * ─────────────────────────────────────────────────────────────────────────────
 * Full table of all scan records with search, pagination, and delete.
 * ─────────────────────────────────────────────────────────────────────────────
 */

define('ADMIN_GUARD', true);
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/storage.php';
require_once __DIR__ . '/includes/layout.php';

require_login();

$message = '';
$msg_type = 'success';

// ── Handle delete ─────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    if (!verify_csrf($_POST['csrf'] ?? '')) {
        $message  = 'CSRF verification failed.';
        $msg_type = 'error';
    } else {
        $id = $_POST['delete_id'];
        if (storage_delete_scan($id)) {
            $message = 'Record deleted successfully.';
        } else {
            $message  = 'Could not delete that record.';
            $msg_type = 'error';
        }
    }
}

// ── Pagination & search ───────────────────────────────────
$page   = max(1, (int) ($_GET['page'] ?? 1));
$search = trim($_GET['search'] ?? '');
$limit  = RECORDS_PER_PAGE;

$result  = storage_get_scans($page, $limit, $search);
$records = $result['records'];
$total   = $result['total'];
$pages   = max(1, (int) ceil($total / $limit));
$page    = min($page, $pages);

$csrf = csrf_token();

// Country flag helper
function flag_emoji_scans(string $code): string {
    if (strlen($code) !== 2) return '🌐';
    return mb_convert_encoding(
        '&#' . (0x1F1E0 + ord($code[0]) - ord('A')) . ';' .
        '&#' . (0x1F1E0 + ord($code[1]) - ord('A')) . ';',
        'UTF-8', 'HTML-ENTITIES'
    );
}

admin_header('Scan Records', 'scans');
?>

<div class="page-header anim">
  <h1>Scan Records</h1>
  <p><?= number_format($total) ?> total records<?= $search ? ' matching "<strong>' . htmlspecialchars($search) . '</strong>"' : '' ?></p>
</div>

<?php if ($message): ?>
<div class="alert alert-<?= $msg_type === 'error' ? 'error' : 'success' ?> anim" data-auto-dismiss="4000">
  <?= $msg_type === 'error' ? '⚠' : '✓' ?> <?= htmlspecialchars($message) ?>
</div>
<?php endif; ?>

<!-- ══ SEARCH BAR ══ -->
<div class="card anim" style="margin-bottom:16px;animation-delay:.05s">
  <div class="card-body" style="padding:16px 22px">
    <form method="GET" action="scans.php" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
      <input
        type="text"
        name="search"
        class="form-input"
        style="max-width:340px"
        placeholder="Search URL, CMS, framework, country…"
        value="<?= htmlspecialchars($search) ?>"
      />
      <button type="submit" class="btn btn-primary">Search</button>
      <?php if ($search): ?>
      <a href="scans.php" class="btn btn-ghost">✕ Clear</a>
      <?php endif; ?>
      <span style="font-size:.72rem;color:var(--text-3);margin-left:auto">
        Page <?= $page ?> of <?= $pages ?>
      </span>
    </form>
  </div>
</div>

<!-- ══ RECORDS TABLE ══ -->
<div class="card anim" style="animation-delay:.1s">
  <div style="overflow-x:auto">
    <?php if (empty($records)): ?>
    <div style="text-align:center;padding:60px;color:var(--text-3)">
      <div style="font-size:2.5rem;margin-bottom:16px">◈</div>
      <div style="font-size:.9rem;margin-bottom:6px;color:var(--text-2)">No records found</div>
      <div style="font-size:.78rem">
        <?= $search ? 'Try a different search term.' : 'Scan some websites to see records here.' ?>
      </div>
    </div>
    <?php else: ?>
    <table class="data-table">
      <thead>
        <tr>
          <th>#</th>
          <th>URL</th>
          <th>CMS</th>
          <th>Framework</th>
          <th>Language</th>
          <th>Plugins</th>
          <th>IP Address</th>
          <th>Location</th>
          <th>Timestamp</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $row_num = ($page - 1) * $limit + 1;
        foreach ($records as $row):
          $id    = $row['id']  ?? '';
          $ts    = $row['scanned_at'] ?? $row['timestamp'] ?? time();
          $cc    = $row['country_code'] ?? '';
          $city  = trim(($row['city'] ?? '') . ($row['country'] ? ', ' . $row['country'] : ''));
        ?>
        <tr>
          <!-- Row number -->
          <td style="color:var(--text-3);font-size:.68rem"><?= $row_num++ ?></td>

          <!-- URL -->
          <td>
            <a href="<?= htmlspecialchars($row['url']) ?>"
               target="_blank" rel="noopener"
               class="url-cell"
               title="<?= htmlspecialchars($row['url']) ?>"
               style="display:block;max-width:240px">
              <?= htmlspecialchars($row['url']) ?>
            </a>
          </td>

          <!-- CMS -->
          <td>
            <?php if (!empty($row['cms'])): ?>
            <span class="badge badge-amber"><?= htmlspecialchars($row['cms']) ?></span>
            <?php else: ?>
            <span style="color:var(--text-3)">—</span>
            <?php endif; ?>
          </td>

          <!-- Framework -->
          <td>
            <?php if (!empty($row['framework'])): ?>
            <span class="badge badge-blue"><?= htmlspecialchars($row['framework']) ?></span>
            <?php else: ?>
            <span style="color:var(--text-3)">—</span>
            <?php endif; ?>
          </td>

          <!-- Language -->
          <td>
            <?php if (!empty($row['language'])): ?>
            <span class="badge badge-green"><?= htmlspecialchars($row['language']) ?></span>
            <?php else: ?>
            <span style="color:var(--text-3)">—</span>
            <?php endif; ?>
          </td>

          <!-- Plugins -->
          <td style="max-width:180px">
            <?php if (!empty($row['plugins'])): ?>
            <div style="font-size:.7rem;color:var(--text-2);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:180px"
                 title="<?= htmlspecialchars($row['plugins']) ?>">
              <?= htmlspecialchars($row['plugins']) ?>
            </div>
            <?php else: ?>
            <span style="color:var(--text-3)">—</span>
            <?php endif; ?>
          </td>

          <!-- IP -->
          <td class="ip-cell">
            <?= htmlspecialchars($row['ip'] ?? '—') ?>
          </td>

          <!-- Location -->
          <td>
            <?php if ($city): ?>
            <span style="font-size:.75rem;color:var(--text-2)">
              <?= $cc ? flag_emoji_scans($cc) . ' ' : '' ?><?= htmlspecialchars($city) ?>
            </span>
            <?php else: ?>
            <span style="color:var(--text-3);font-size:.72rem">Unknown</span>
            <?php endif; ?>
          </td>

          <!-- Timestamp -->
          <td class="ts-cell">
            <div><?= date('M d, Y', $ts) ?></div>
            <div style="color:var(--text-3)"><?= date('H:i:s', $ts) ?></div>
          </td>

          <!-- Delete -->
          <td>
            <?php if ($id): ?>
            <form method="POST" action="scans.php" style="display:inline">
              <input type="hidden" name="csrf"      value="<?= htmlspecialchars($csrf) ?>"/>
              <input type="hidden" name="delete_id" value="<?= htmlspecialchars($id) ?>"/>
              <?php if ($search): ?>
              <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>"/>
              <?php endif; ?>
              <button
                type="submit"
                class="btn btn-danger btn-sm"
                data-confirm="Delete this scan record? This cannot be undone."
              >✕</button>
            </form>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>
  </div>

  <!-- ── PAGINATION ── -->
  <?php if ($pages > 1): ?>
  <div class="pagination">
    <div class="pagination-info">
      Showing <?= number_format(($page - 1) * $limit + 1) ?>–<?= number_format(min($page * $limit, $total)) ?>
      of <?= number_format($total) ?> records
    </div>
    <div class="pagination-btns">
      <?php
      $qs = $search ? '&search=' . urlencode($search) : '';

      // Previous
      if ($page > 1): ?>
      <a class="pg-btn" href="?page=<?= $page - 1 ?><?= $qs ?>">← Prev</a>
      <?php else: ?>
      <span class="pg-btn disabled">← Prev</span>
      <?php endif;

      // Page numbers (window of 5)
      $start = max(1, $page - 2);
      $end   = min($pages, $start + 4);
      $start = max(1, $end - 4);

      if ($start > 1): ?>
      <a class="pg-btn" href="?page=1<?= $qs ?>">1</a>
      <?php if ($start > 2): ?>
      <span class="pg-btn disabled">…</span>
      <?php endif;
      endif;

      for ($p = $start; $p <= $end; $p++): ?>
      <a class="pg-btn <?= $p === $page ? 'active' : '' ?>"
         href="?page=<?= $p ?><?= $qs ?>"><?= $p ?></a>
      <?php endfor;

      if ($end < $pages): ?>
      <?php if ($end < $pages - 1): ?>
      <span class="pg-btn disabled">…</span>
      <?php endif; ?>
      <a class="pg-btn" href="?page=<?= $pages ?><?= $qs ?>"><?= $pages ?></a>
      <?php endif;

      // Next
      if ($page < $pages): ?>
      <a class="pg-btn" href="?page=<?= $page + 1 ?><?= $qs ?>">Next →</a>
      <?php else: ?>
      <span class="pg-btn disabled">Next →</span>
      <?php endif; ?>
    </div>
  </div>
  <?php endif; ?>

</div><!-- /.card -->

<?php admin_footer(); ?>
