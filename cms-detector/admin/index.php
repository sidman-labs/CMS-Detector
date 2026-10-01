<?php
/**
 * admin/index.php — Dashboard
 * ─────────────────────────────────────────────────────────────────────────────
 * Main dashboard: stat cards, scan/day chart, CMS distribution, recent scans.
 * ─────────────────────────────────────────────────────────────────────────────
 */

define('ADMIN_GUARD', true);
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/storage.php';
require_once __DIR__ . '/includes/layout.php';

require_login();

$stats   = storage_get_stats();
$total   = $stats['total_scans']    ?? 0;
$unique  = $stats['unique_ips']     ?? 0;
$today   = $stats['today_scans']    ?? 0;
$cms_d   = $stats['cms_dist']       ?? [];
$fw_d    = $stats['framework_dist'] ?? [];
$daily   = $stats['daily_scans']    ?? [];
$recent  = $stats['recent']         ?? [];
$countries = $stats['countries']    ?? [];

// Build last-14-day series for chart (fill gaps with 0)
$chart_days   = [];
$chart_counts = [];
for ($i = 13; $i >= 0; $i--) {
    $day = date('Y-m-d', strtotime("-$i days"));
    $chart_days[]   = date('M d', strtotime($day));
    $chart_counts[] = $daily[$day] ?? 0;
}

// Percentage helper
$cms_total = array_sum($cms_d) ?: 1;
$fw_total  = array_sum($fw_d)  ?: 1;

// Country flag emoji from 2-letter code
function flag_emoji(string $code): string {
    if (strlen($code) !== 2) return '🌐';
    return mb_convert_encoding(
        '&#' . (0x1F1E0 + ord($code[0]) - ord('A')) . ';' .
        '&#' . (0x1F1E0 + ord($code[1]) - ord('A')) . ';',
        'UTF-8', 'HTML-ENTITIES'
    );
}

admin_header('Dashboard', 'dashboard');
?>

<div class="page-header anim">
  <h1>Dashboard</h1>
  <p>Overview of all scans and detected technologies</p>
</div>

<!-- ══ STAT CARDS ══════════════════════════════════════════ -->
<div class="stat-grid">

  <div class="stat-card anim" style="animation-delay:.05s">
    <div class="stat-header">
      <div class="stat-icon amber">📊</div>
      <span class="stat-delta up">↑ Live</span>
    </div>
    <div class="stat-value"><?= number_format($total) ?></div>
    <div class="stat-label">Total Scans</div>
  </div>

  <div class="stat-card anim" style="animation-delay:.1s">
    <div class="stat-header">
      <div class="stat-icon green">👤</div>
      <span class="stat-delta flat">Unique</span>
    </div>
    <div class="stat-value"><?= number_format($unique) ?></div>
    <div class="stat-label">Unique Visitors</div>
  </div>

  <div class="stat-card anim" style="animation-delay:.15s">
    <div class="stat-header">
      <div class="stat-icon blue">📅</div>
      <span class="stat-delta up">Today</span>
    </div>
    <div class="stat-value"><?= number_format($today) ?></div>
    <div class="stat-label">Scans Today</div>
  </div>

  <div class="stat-card anim" style="animation-delay:.2s">
    <div class="stat-header">
      <div class="stat-icon violet">🏗️</div>
      <span class="stat-delta flat">Distinct</span>
    </div>
    <div class="stat-value"><?= count($cms_d) ?></div>
    <div class="stat-label">CMS Types Found</div>
  </div>

</div>

<!-- ══ CHARTS ROW ══════════════════════════════════════════ -->
<div class="charts-row">

  <!-- Daily scans bar chart -->
  <div class="card anim" style="animation-delay:.25s">
    <div class="card-head">
      <div class="card-title">
        <span class="card-title-icon">◈</span> Scans — Last 14 Days
      </div>
    </div>
    <div class="card-body chart-wrap">
      <div class="chart-canvas-wrap">
        <canvas id="dailyChart"></canvas>
      </div>
    </div>
  </div>

  <!-- CMS doughnut -->
  <div class="card anim" style="animation-delay:.3s">
    <div class="card-head">
      <div class="card-title">
        <span class="card-title-icon">⬡</span> CMS Distribution
      </div>
    </div>
    <div class="card-body chart-wrap">
      <?php if (empty($cms_d)): ?>
        <p style="color:var(--text-3);font-size:.8rem;text-align:center;padding:40px 0">No CMS data yet</p>
      <?php else: ?>
      <div style="display:grid;grid-template-columns:1fr 140px;gap:20px;align-items:center;height:240px">
        <div class="chart-canvas-wrap" style="height:100%">
          <canvas id="cmsChart"></canvas>
        </div>
        <div class="progress-list" style="gap:8px">
          <?php foreach (array_slice($cms_d, 0, 6, true) as $name => $count): ?>
          <?php $pct = round($count / $cms_total * 100); ?>
          <div style="font-size:.68rem">
            <div style="display:flex;justify-content:space-between;color:var(--text-2);margin-bottom:3px">
              <span><?= htmlspecialchars($name) ?></span>
              <span style="color:var(--amber)"><?= $pct ?>%</span>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </div>

</div>

<!-- ══ BOTTOM ROW ══════════════════════════════════════════ -->
<div style="display:grid;grid-template-columns:1fr 280px;gap:16px;align-items:start">

  <!-- Recent scans table -->
  <div class="card anim" style="animation-delay:.35s">
    <div class="card-head">
      <div class="card-title">
        <span class="card-title-icon">◎</span> Recent Scans
      </div>
      <a href="scans.php" class="btn btn-ghost btn-sm">View All →</a>
    </div>
    <div class="card-body" style="padding-top:0">
      <?php if (empty($recent)): ?>
        <p style="color:var(--text-3);font-size:.8rem;padding:24px 0;text-align:center">No scans recorded yet.</p>
      <?php else: ?>
      <table class="data-table">
        <thead>
          <tr>
            <th>URL</th>
            <th>CMS</th>
            <th>Framework</th>
            <th>Country</th>
            <th>Time</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($recent as $row): ?>
          <tr>
            <td>
              <div class="url-cell" title="<?= htmlspecialchars($row['url']) ?>">
                <?= htmlspecialchars($row['url']) ?>
              </div>
            </td>
            <td>
              <?php if ($row['cms']): ?>
              <span class="badge badge-amber"><?= htmlspecialchars($row['cms']) ?></span>
              <?php else: ?>
              <span style="color:var(--text-3);font-size:.72rem">—</span>
              <?php endif; ?>
            </td>
            <td>
              <?php if ($row['framework']): ?>
              <span class="badge badge-blue"><?= htmlspecialchars($row['framework']) ?></span>
              <?php else: ?>
              <span style="color:var(--text-3);font-size:.72rem">—</span>
              <?php endif; ?>
            </td>
            <td>
              <?php
              $cc = $row['country_code'] ?? '';
              echo $cc ? flag_emoji($cc) . ' ' : '🌐 ';
              echo htmlspecialchars($row['country'] ?: 'Unknown');
              ?>
            </td>
            <td class="ts-cell"><?= date('M d, H:i', $row['scanned_at'] ?? $row['timestamp'] ?? time()) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <?php endif; ?>
    </div>
  </div>

  <!-- Side column -->
  <div style="display:flex;flex-direction:column;gap:16px">

    <!-- Framework distribution -->
    <div class="card anim" style="animation-delay:.4s">
      <div class="card-head">
        <div class="card-title"><span class="card-title-icon">⚙️</span> Frameworks</div>
      </div>
      <div class="card-body">
        <?php if (empty($fw_d)): ?>
          <p style="color:var(--text-3);font-size:.75rem;text-align:center;padding:12px 0">No data yet</p>
        <?php else: ?>
        <div class="progress-list">
          <?php foreach (array_slice($fw_d, 0, 6, true) as $name => $count): ?>
          <?php $pct = round($count / $fw_total * 100); ?>
          <div class="progress-item">
            <div class="progress-header">
              <span class="progress-label"><?= htmlspecialchars($name) ?></span>
              <span class="progress-pct"><?= $pct ?>%</span>
            </div>
            <div class="progress-bar-bg">
              <div class="progress-bar-fill" data-width="<?= $pct ?>%" style="width:0"></div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Top countries -->
    <?php if (!empty($countries)): ?>
    <div class="card anim" style="animation-delay:.45s">
      <div class="card-head">
        <div class="card-title"><span class="card-title-icon">🌍</span> Top Countries</div>
      </div>
      <div class="card-body">
        <?php
        $c_total = array_sum($countries) ?: 1;
        foreach ($countries as $country => $count):
        $pct = round($count / $c_total * 100);
        ?>
        <div style="display:flex;align-items:center;justify-content:space-between;
                    padding:7px 0;border-bottom:1px solid var(--border);font-size:.75rem;
                    gap:10px">
          <span style="color:var(--text-2)"><?= htmlspecialchars($country) ?></span>
          <div style="display:flex;align-items:center;gap:8px">
            <div style="width:50px;height:4px;background:rgba(255,255,255,.06);border-radius:2px;overflow:hidden">
              <div style="width:<?= $pct ?>%;height:100%;background:var(--amber);border-radius:2px"></div>
            </div>
            <span style="color:var(--text-3);font-family:var(--font-mono)"><?= $count ?></span>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

  </div><!-- /.side-col -->

</div><!-- /.bottom-row -->

<script>
(function () {
  const AMBER   = '#fbbf24';
  const AMBER_D = '#d97706';
  const BG3     = '#1e2230';
  const TEXT2   = '#8b92a8';

  Chart.defaults.color         = TEXT2;
  Chart.defaults.font.family   = "'JetBrains Mono', monospace";
  Chart.defaults.font.size     = 11;
  Chart.defaults.borderColor   = 'rgba(255,255,255,.06)';

  const gridCfg = {
    color: 'rgba(255,255,255,.05)',
    drawBorder: false,
  };

  // ── Daily bar chart ──
  const dailyCtx = document.getElementById('dailyChart');
  if (dailyCtx) {
    new Chart(dailyCtx, {
      type: 'bar',
      data: {
        labels: <?= json_encode($chart_days) ?>,
        datasets: [{
          label: 'Scans',
          data: <?= json_encode($chart_counts) ?>,
          backgroundColor: 'rgba(251,191,36,.2)',
          borderColor: AMBER,
          borderWidth: 1.5,
          borderRadius: 4,
          hoverBackgroundColor: 'rgba(251,191,36,.35)',
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: '#181b23',
            borderColor: 'rgba(251,191,36,.3)',
            borderWidth: 1,
            padding: 10,
            callbacks: {
              label: ctx => ` ${ctx.raw} scans`,
            }
          }
        },
        scales: {
          x: { grid: gridCfg, ticks: { maxRotation: 45 } },
          y: { grid: gridCfg, beginAtZero: true, ticks: { precision: 0 } }
        }
      }
    });
  }

  // ── CMS doughnut ──
  const cmsCtx = document.getElementById('cmsChart');
  if (cmsCtx) {
    const cmsLabels = <?= json_encode(array_keys($cms_d)) ?>;
    const cmsCounts = <?= json_encode(array_values($cms_d)) ?>;
    const palette   = ['#fbbf24','#34d399','#60a5fa','#a78bfa','#f87171','#fb923c','#a3e635','#22d3ee'];

    new Chart(cmsCtx, {
      type: 'doughnut',
      data: {
        labels: cmsLabels,
        datasets: [{
          data: cmsCounts,
          backgroundColor: palette.map(c => c + '33'),
          borderColor: palette,
          borderWidth: 1.5,
          hoverOffset: 6,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '68%',
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: '#181b23',
            borderColor: 'rgba(251,191,36,.3)',
            borderWidth: 1,
            padding: 10,
          }
        }
      }
    });
  }
})();
</script>

<script>
// Real-time auto-refresh every 30 seconds
(function () {
  let countdown = 30;
  const indicator = document.createElement('div');
  indicator.style.cssText = 'position:fixed;bottom:18px;right:18px;background:rgba(251,191,36,.12);border:1px solid rgba(251,191,36,.25);color:#fbbf24;font-family:var(--font-mono);font-size:.68rem;padding:6px 12px;border-radius:20px;z-index:999;cursor:pointer';
  indicator.title = 'Click to refresh now';
  indicator.addEventListener('click', () => location.reload());
  document.body.appendChild(indicator);

  function tick() {
    indicator.textContent = '⟳ Refreshing in ' + countdown + 's';
    if (countdown <= 0) {
      location.reload();
    }
    countdown--;
    setTimeout(tick, 1000);
  }
  tick();
})();
</script>

<?php admin_footer(); ?>
