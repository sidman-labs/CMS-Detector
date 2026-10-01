<?php
/**
 * index.php
 * ─────────────────────────────────────────────────────────────────────────────
 * Entry point for StackDetect.
 * Renders the full UI and loads recent scan history for display.
 * The actual detection happens via AJAX → detect.php
 * ─────────────────────────────────────────────────────────────────────────────
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';

$page_title = 'CMS Detector | Identify What CMS a Website Is Using Instantly';
$history    = ENABLE_HISTORY ? load_scan_history() : [];

include __DIR__ . '/templates/header.php';
?>

<!-- ══════════════════════════════════════════════════════
     NAV BAR
══════════════════════════════════════════════════════ -->
<nav class="topnav">
  <div class="topnav-brand">
    <span class="brand-icon">⬡</span>
    <span class="brand-name"><?= APP_NAME ?></span>
  </div>
  <div class="topnav-links">
    <a href="#how-it-works">How it works</a>
    <a href="#platforms">Platforms</a>
    <a href="#faq">FAQ</a>
    <a href="#history" id="nav-history-link">History</a>
  </div>
</nav>

<!-- ══════════════════════════════════════════════════════
     HERO
══════════════════════════════════════════════════════ -->
<header class="hero">
  <div class="hero-badge">🔍 Free Website Technology Lookup</div>
  <h1 class="hero-title">
    CMS Detector: Identify Any Website's<br>
    <span class="gradient-text">Tech Stack</span> Instantly
  </h1>
  <p class="hero-subtitle">
    Detect CMS, framework, programming language, and plugins<br class="br-hide">
    for any website — 100% free, no registration required.
  </p>

  <!-- ── SCAN FORM ── -->
  <div class="scan-box" id="scan-box">
    <div class="scan-input-wrap" id="scan-input-wrap">
      <span class="scan-icon">🌐</span>
      <input
        type="url"
        id="url-input"
        class="scan-input"
        placeholder="https://example.com"
        autocomplete="off"
        spellcheck="false"
        autofocus
      />
      <button id="scan-btn" class="scan-btn" type="button">
        <span class="scan-btn-label">Detect</span>
        <span class="scan-btn-arrow">→</span>
      </button>
    </div>
    <p class="scan-hint" id="scan-hint">Enter any public URL to scan its technology stack</p>
  </div>
</header>

<!-- ══════════════════════════════════════════════════════
     LOADING STATE
══════════════════════════════════════════════════════ -->
<div class="loader-wrap" id="loader-wrap" hidden>
  <div class="loader-spinner">
    <div class="spinner-ring"></div>
    <div class="spinner-ring spinner-ring--2"></div>
    <div class="spinner-core">⬡</div>
  </div>
  <p class="loader-text" id="loader-text">Initializing scan…</p>
  <div class="loader-url" id="loader-url"></div>
</div>

<!-- ══════════════════════════════════════════════════════
     RESULTS SECTION
══════════════════════════════════════════════════════ -->
<section class="results-section" id="results-section" hidden>

  <!-- Results header -->
  <div class="results-header">
    <div class="results-meta">
      <span class="results-meta-dot"></span>
      <span>Scan complete &mdash; <strong id="result-host"></strong></span>
    </div>
    <div class="results-actions">
      <button class="btn-ghost" id="copy-btn" title="Copy results">📋 Copy</button>
      <button class="btn-ghost" id="rescan-btn" title="Scan again">↺ Rescan</button>
    </div>
  </div>

  <!-- Result cards grid -->
  <div class="results-grid" id="results-grid">
    <!-- Filled dynamically by JS -->
  </div>

  <!-- "Nothing detected" fallback -->
  <div class="no-results" id="no-results" hidden>
    <div class="no-results-icon">🔭</div>
    <h3>Nothing detected</h3>
    <p>We couldn't identify any known technologies on this website.<br>
       It may use custom or obfuscated code.</p>
  </div>

</section>

<!-- ══════════════════════════════════════════════════════
     RECENT SCANS HISTORY (dynamic content – SEO priority)
══════════════════════════════════════════════════════ -->
<?php if (ENABLE_HISTORY && !empty($history)): ?>
<section class="history-section" id="history">
  <h2 class="section-title">Recent Website Detections</h2>
  <div class="history-list" id="history-list">
    <?php foreach (array_slice($history, 0, 8) as $scan): ?>
    <div class="history-item" data-url="<?= htmlspecialchars($scan['url']) ?>">
      <div class="history-item-left">
        <div class="history-favicon">
          <img
            src="https://www.google.com/s2/favicons?domain=<?= urlencode(parse_url($scan['url'], PHP_URL_HOST) ?? $scan['url']) ?>&sz=32"
            alt="<?= htmlspecialchars(parse_url($scan['url'], PHP_URL_HOST) ?? '') ?> favicon"
            loading="lazy"
            onerror="this.style.display='none'; this.nextElementSibling.style.display='block';"
          />
          <span class="history-favicon-fallback" style="display:none">🌐</span>
        </div>
        <div class="history-item-info">
          <span class="history-host"><?= htmlspecialchars(display_host($scan['url'])) ?></span>
          <div class="history-tags">
            <?php
              $tags = [];
              foreach ($scan['summary'] as $cat => $names) {
                  $tags = array_merge($tags, array_slice($names, 0, 2));
              }
              foreach (array_slice($tags, 0, 4) as $tag):
            ?>
            <span class="history-tag"><?= htmlspecialchars($tag) ?></span>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
      <div class="history-item-right">
        <span class="history-time"><?= time_ago($scan['timestamp']) ?></span>
        <button class="btn-rescan-history" data-url="<?= htmlspecialchars($scan['url']) ?>">
          Scan again →
        </button>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<!-- ══════════════════════════════════════════════════════
     HOW IT WORKS
══════════════════════════════════════════════════════ -->
<section class="how-section" id="how-it-works">
  <h2 class="section-title">How Our Technology Lookup Works</h2>
  <div class="how-grid">
    <div class="how-card">
      <div class="how-num">01</div>
      <h3>Fetch</h3>
      <p>We download the page HTML and capture all HTTP response headers in real time.</p>
    </div>
    <div class="how-card">
      <div class="how-num">02</div>
      <h3>Match</h3>
      <p>300+ signatures are checked: script paths, meta tags, cookies, and header values.</p>
    </div>
    <div class="how-card">
      <div class="how-num">03</div>
      <h3>Score</h3>
      <p>Each match is weighted and a confidence score is assigned before results are shown.</p>
    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════════════════
     PLATFORMS SECTION
══════════════════════════════════════════════════════ -->
<section class="platforms-section" id="platforms">

  <div class="section-header-wrap">
    <div class="section-eyebrow">⬡ Detection Coverage</div>
    <h2 class="section-heading">Supported <span class="gradient-text">CMS Platforms</span><br>and Frameworks</h2>
    <p class="section-subtext">Our detector recognises over <strong>100+ platforms</strong> and technology stacks across every major category.</p>
  </div>

  <div class="platforms-grid">

    <div class="platform-card reveal-card" style="--card-accent:#63b3ed">
      <div class="pc-icon">🔌</div>
      <div class="pc-number">01</div>
      <h3 class="pc-title">WordPress Plugin &amp; Theme Detection</h3>
      <p class="pc-desc">We inspect asset paths, script handles, and directory structures to pinpoint the exact WordPress theme and active plugins.</p>
      <div class="pc-tags">
        <span class="ptag ptag--wp">WordPress</span>
        <span class="ptag ptag--wp">WooCommerce</span>
        <span class="ptag ptag--wp">Elementor</span>
        <span class="ptag ptag--wp">Yoast SEO</span>
        <span class="ptag ptag--wp">Divi</span>
        <span class="ptag ptag--wp">WPBakery</span>
      </div>
    </div>

    <div class="platform-card reveal-card" style="--card-accent:#68d391">
      <div class="pc-icon">🛒</div>
      <div class="pc-number">02</div>
      <h3 class="pc-title">eCommerce Platforms</h3>
      <p class="pc-desc">Identify storefronts running on leading eCommerce solutions used by millions of online shops worldwide.</p>
      <div class="pc-tags">
        <span class="ptag ptag--green">Shopify</span>
        <span class="ptag ptag--green">Magento</span>
        <span class="ptag ptag--green">WooCommerce</span>
        <span class="ptag ptag--green">BigCommerce</span>
        <span class="ptag ptag--green">PrestaShop</span>
        <span class="ptag ptag--green">OpenCart</span>
      </div>
    </div>

    <div class="platform-card reveal-card" style="--card-accent:#f687b3">
      <div class="pc-icon">🧩</div>
      <div class="pc-number">03</div>
      <h3 class="pc-title">CMS &amp; Website Builders</h3>
      <p class="pc-desc">From enterprise CMS to drag-and-drop website builders, we detect them all with high accuracy.</p>
      <div class="pc-tags">
        <span class="ptag ptag--pink">Joomla</span>
        <span class="ptag ptag--pink">Drupal</span>
        <span class="ptag ptag--pink">Wix</span>
        <span class="ptag ptag--pink">Squarespace</span>
        <span class="ptag ptag--pink">Webflow</span>
        <span class="ptag ptag--pink">Ghost</span>
      </div>
    </div>

    <div class="platform-card reveal-card" style="--card-accent:#81e6d9">
      <div class="pc-icon">⚙️</div>
      <div class="pc-number">04</div>
      <h3 class="pc-title">Frameworks &amp; Languages</h3>
      <p class="pc-desc">Detect the underlying programming language and frontend or backend framework powering the site.</p>
      <div class="pc-tags">
        <span class="ptag ptag--teal">React</span>
        <span class="ptag ptag--teal">Next.js</span>
        <span class="ptag ptag--teal">Vue.js</span>
        <span class="ptag ptag--teal">Laravel</span>
        <span class="ptag ptag--teal">Django</span>
        <span class="ptag ptag--teal">Ruby on Rails</span>
      </div>
    </div>

  </div>
</section>

<!-- ══════════════════════════════════════════════════════
     FAQ
══════════════════════════════════════════════════════ -->
<section class="faq-section" id="faq">

  <div class="section-header-wrap">
    <div class="section-eyebrow">❓ Quick Answers</div>
    <h2 class="section-heading">Frequently Asked <span class="gradient-text">Questions</span></h2>
    <p class="section-subtext">Everything you need to know about our free CMS and technology detection tool.</p>
  </div>

  <div class="faq-list">

    <div class="faq-item" id="faq-1">
      <button class="faq-trigger" aria-expanded="false" aria-controls="faq-body-1">
        <span class="faq-q-num">01</span>
        <span class="faq-q-text">How do I find out what CMS a website is using for free?</span>
        <span class="faq-chevron" aria-hidden="true">
          <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M4.5 6.75L9 11.25L13.5 6.75" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </span>
      </button>
      <div class="faq-body" id="faq-body-1" role="region">
        <div class="faq-body-inner">
          <p>To identify a website's CMS, simply enter the URL into our search bar and click <strong>Detect</strong>. Our tool instantly scans the site's HTML, meta tags, and headers to reveal if it is built on WordPress, Shopify, Wix, or other major platforms — completely free with no account needed.</p>
        </div>
      </div>
    </div>

    <div class="faq-item" id="faq-2">
      <button class="faq-trigger" aria-expanded="false" aria-controls="faq-body-2">
        <span class="faq-q-num">02</span>
        <span class="faq-q-text">Is this CMS lookup tool 100% free to use?</span>
        <span class="faq-chevron" aria-hidden="true">
          <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M4.5 6.75L9 11.25L13.5 6.75" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </span>
      </button>
      <div class="faq-body" id="faq-body-2" role="region">
        <div class="faq-body-inner">
          <p>Yes, our CMS detector is completely free and supports <strong>unlimited scans</strong> with no registration required. You can perform deep technology lookups and stack analysis on any domain at zero cost.</p>
        </div>
      </div>
    </div>

    <div class="faq-item" id="faq-3">
      <button class="faq-trigger" aria-expanded="false" aria-controls="faq-body-3">
        <span class="faq-q-num">03</span>
        <span class="faq-q-text">How does the CMS detection algorithm work?</span>
        <span class="faq-chevron" aria-hidden="true">
          <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M4.5 6.75L9 11.25L13.5 6.75" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </span>
      </button>
      <div class="faq-body" id="faq-body-3" role="region">
        <div class="faq-body-inner">
          <p>The tool uses <strong>advanced fingerprinting</strong> to analyze technical signals such as <code>meta name="generator"</code> tags, HTTP headers, and unique directory paths like <code>/wp-content/</code>. It checks every page against thousands of specific artifacts to provide a high-confidence detection result.</p>
        </div>
      </div>
    </div>

    <div class="faq-item" id="faq-4">
      <button class="faq-trigger" aria-expanded="false" aria-controls="faq-body-4">
        <span class="faq-q-num">04</span>
        <span class="faq-q-text">Can your tool detect WordPress themes and plugins?</span>
        <span class="faq-chevron" aria-hidden="true">
          <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M4.5 6.75L9 11.25L13.5 6.75" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </span>
      </button>
      <div class="faq-body" id="faq-body-4" role="region">
        <div class="faq-body-inner">
          <p>Yes, our scanner identifies the specific WordPress themes and plugins powering a site by inspecting asset signatures and directory patterns. It can also detect eCommerce apps and JavaScript frameworks like React and Next.js.</p>
        </div>
      </div>
    </div>

    <div class="faq-item" id="faq-5">
      <button class="faq-trigger" aria-expanded="false" aria-controls="faq-body-5">
        <span class="faq-q-num">05</span>
        <span class="faq-q-text">Which CMS platforms and web technologies can you identify?</span>
        <span class="faq-chevron" aria-hidden="true">
          <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M4.5 6.75L9 11.25L13.5 6.75" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </span>
      </button>
      <div class="faq-body" id="faq-body-5" role="region">
        <div class="faq-body-inner">
          <p>We can detect over <strong>100+ platforms</strong>, including WordPress, Shopify, Joomla, Drupal, Squarespace, and Wix. Additionally, the tool identifies underlying technologies such as web servers, analytics trackers, and advertising pixels.</p>
        </div>
      </div>
    </div>

  </div>
</section>

<!-- ══════════════════════════════════════════════════════
     STYLES
══════════════════════════════════════════════════════ -->
<style>
/* ── NAV ─────────────────────────────────────────────── */
.topnav {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 24px 0 0;
}
.topnav-brand {
  display: flex;
  align-items: center;
  gap: 8px;
  font-weight: 700;
  font-size: 1.05rem;
  letter-spacing: -.01em;
}
.brand-icon {
  font-size: 1.5rem;
  color: var(--accent);
  line-height: 1;
}
.topnav-links {
  display: flex;
  gap: 24px;
  font-size: .875rem;
  color: var(--text-secondary);
}
.topnav-links a { color: inherit; transition: color var(--transition); }
.topnav-links a:hover { color: var(--text-primary); text-decoration: none; }

/* ── HERO ────────────────────────────────────────────── */
.hero {
  text-align: center;
  padding: 72px 0 48px;
}
.hero-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: rgba(99,179,237,.1);
  border: 1px solid rgba(99,179,237,.25);
  color: var(--accent);
  font-size: .78rem;
  font-weight: 600;
  letter-spacing: .06em;
  text-transform: uppercase;
  padding: 5px 14px;
  border-radius: 999px;
  margin-bottom: 24px;
}
.hero-title {
  font-size: clamp(2.2rem, 5vw, 3.4rem);
  font-weight: 700;
  line-height: 1.12;
  letter-spacing: -.03em;
  margin-bottom: 20px;
}
.gradient-text {
  background: linear-gradient(135deg, var(--accent) 0%, var(--accent-2) 60%, var(--accent-hot) 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}
.hero-subtitle {
  font-size: 1.05rem;
  color: var(--text-secondary);
  line-height: 1.65;
  margin-bottom: 40px;
}
.br-hide { display: none; }
@media (min-width: 600px) { .br-hide { display: block; } }

/* ── SCAN BOX ────────────────────────────────────────── */
.scan-box { max-width: 640px; margin: 0 auto; }

.scan-input-wrap {
  display: flex;
  align-items: center;
  background: var(--bg-input);
  border: 1px solid var(--border);
  border-radius: var(--radius-xl);
  padding: 6px 6px 6px 18px;
  gap: 10px;
  transition: border-color var(--transition), box-shadow var(--transition);
}
.scan-input-wrap:focus-within {
  border-color: var(--border-glow);
  box-shadow: var(--shadow-glow);
}
.scan-icon { font-size: 1.1rem; flex-shrink: 0; color: var(--text-muted); }

.scan-input {
  flex: 1;
  background: transparent;
  border: none;
  outline: none;
  font-family: var(--font-mono);
  font-size: .9rem;
  color: var(--text-primary);
  min-width: 0;
}
.scan-input::placeholder { color: var(--text-muted); }

.scan-btn {
  display: flex;
  align-items: center;
  gap: 6px;
  background: var(--accent);
  color: #0a0b0f;
  font-family: var(--font-ui);
  font-weight: 700;
  font-size: .9rem;
  border: none;
  border-radius: calc(var(--radius-xl) - 8px);
  padding: 11px 22px;
  cursor: pointer;
  transition: background var(--transition), transform var(--transition), opacity var(--transition);
  white-space: nowrap;
  flex-shrink: 0;
}
.scan-btn:hover  { background: #90cdf4; transform: translateY(-1px); }
.scan-btn:active { transform: translateY(0); }
.scan-btn:disabled { opacity: .5; cursor: not-allowed; transform: none; }
.scan-btn-arrow { font-size: 1rem; }

.scan-hint {
  margin-top: 12px;
  font-size: .8rem;
  color: var(--text-muted);
  transition: color var(--transition);
}
.scan-hint.error { color: #fc8181; }

/* ── LOADER ──────────────────────────────────────────── */
.loader-wrap {
  text-align: center;
  padding: 64px 0;
}
.loader-spinner {
  position: relative;
  width: 72px;
  height: 72px;
  margin: 0 auto 24px;
}
.spinner-ring {
  position: absolute;
  inset: 0;
  border: 2px solid transparent;
  border-top-color: var(--accent);
  border-radius: 50%;
  animation: spin 1.2s linear infinite;
}
.spinner-ring--2 {
  inset: 10px;
  border-top-color: var(--accent-2);
  animation-duration: .8s;
  animation-direction: reverse;
}
.spinner-core {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.4rem;
  color: var(--accent);
}
@keyframes spin { to { transform: rotate(360deg); } }

.loader-text {
  font-size: 1rem;
  color: var(--text-secondary);
  margin-bottom: 8px;
}
.loader-url {
  font-family: var(--font-mono);
  font-size: .8rem;
  color: var(--text-muted);
}

/* ── RESULTS ─────────────────────────────────────────── */
.results-section { margin-top: 48px; animation: fadeUp .4s ease; }

.results-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 12px;
  margin-bottom: 24px;
  padding-bottom: 20px;
  border-bottom: 1px solid var(--border);
}
.results-meta {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: .9rem;
  color: var(--text-secondary);
}
.results-meta-dot {
  width: 8px; height: 8px;
  background: var(--green);
  border-radius: 50%;
  box-shadow: 0 0 8px var(--green);
  animation: pulse 2s ease-in-out infinite;
}
@keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.4} }

.results-actions { display: flex; gap: 8px; }
.btn-ghost {
  background: transparent;
  border: 1px solid var(--border);
  color: var(--text-secondary);
  font-family: var(--font-ui);
  font-size: .8rem;
  padding: 6px 12px;
  border-radius: var(--radius-sm);
  cursor: pointer;
  transition: border-color var(--transition), color var(--transition);
}
.btn-ghost:hover { border-color: var(--accent); color: var(--accent); }

.results-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
  gap: 16px;
}

/* Category card */
.result-card {
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: var(--radius-lg);
  padding: 24px;
  box-shadow: var(--shadow-card);
  transition: border-color var(--transition), transform var(--transition);
  animation: fadeUp .35s ease backwards;
}
.result-card:hover { border-color: rgba(99,179,237,.3); transform: translateY(-2px); }

.card-header {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 20px;
}
.card-category-icon {
  width: 36px; height: 36px;
  display: flex; align-items: center; justify-content: center;
  background: rgba(99,179,237,.1);
  border-radius: 10px;
  font-size: 1.1rem;
}
.card-category-label {
  font-size: .72rem;
  font-weight: 700;
  letter-spacing: .08em;
  text-transform: uppercase;
  color: var(--text-muted);
}

.tech-list { display: flex; flex-direction: column; gap: 10px; }

.tech-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 12px;
  background: rgba(255,255,255,.03);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  transition: background var(--transition), border-color var(--transition);
}
.tech-item:hover { background: rgba(255,255,255,.05); border-color: rgba(255,255,255,.12); }

.tech-icon {
  font-size: 1.2rem;
  line-height: 1;
  flex-shrink: 0;
}
.tech-info { flex: 1; min-width: 0; }
.tech-name {
  font-size: .88rem;
  font-weight: 600;
  color: var(--text-primary);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.tech-score-wrap {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 4px;
  flex-shrink: 0;
}
.tech-score-label {
  font-family: var(--font-mono);
  font-size: .7rem;
  color: var(--text-muted);
}
.tech-bar-bg {
  width: 60px; height: 4px;
  background: rgba(255,255,255,.08);
  border-radius: 2px;
  overflow: hidden;
}
.tech-bar-fill {
  height: 100%;
  border-radius: 2px;
  transition: width 1s ease;
}

.no-results {
  text-align: center;
  padding: 48px;
  color: var(--text-muted);
}
.no-results-icon { font-size: 3rem; margin-bottom: 16px; }
.no-results h3 { font-size: 1.1rem; margin-bottom: 8px; color: var(--text-secondary); }
.no-results p  { font-size: .875rem; line-height: 1.6; }

/* ── HOW IT WORKS ────────────────────────────────────── */
.how-section {
  margin-top: 80px;
  padding-top: 48px;
  border-top: 1px solid var(--border);
}
.section-title {
  font-size: 1.5rem;
  font-weight: 700;
  letter-spacing: -.02em;
  margin-bottom: 32px;
}
.how-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 16px;
}
.how-card {
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: var(--radius-lg);
  padding: 28px 24px;
  transition: border-color var(--transition);
}
.how-card:hover { border-color: rgba(99,179,237,.3); }
.how-num {
  font-family: var(--font-mono);
  font-size: .78rem;
  color: var(--accent);
  margin-bottom: 12px;
  letter-spacing: .05em;
}
.how-card h3 { font-size: 1rem; margin-bottom: 8px; }
.how-card p  { font-size: .85rem; color: var(--text-secondary); line-height: 1.6; }

/* ── HISTORY ─────────────────────────────────────────── */
.history-section {
  margin-top: 64px;
  padding-top: 48px;
  border-top: 1px solid var(--border);
}
.history-list { display: flex; flex-direction: column; gap: 10px; }

.history-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  padding: 14px 18px;
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  cursor: pointer;
  transition: border-color var(--transition), background var(--transition);
}
.history-item:hover { border-color: rgba(99,179,237,.3); background: #13151a; }
.history-item-left { display: flex; align-items: center; gap: 12px; flex: 1; min-width: 0; }

.history-favicon {
  width: 28px; height: 28px;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
  font-size: 1rem;
}
.history-favicon img { width: 20px; height: 20px; border-radius: 4px; }

.history-item-info { min-width: 0; }
.history-host {
  display: block;
  font-size: .875rem;
  font-weight: 600;
  color: var(--text-primary);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.history-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 4px;
  margin-top: 4px;
}
.history-tag {
  font-size: .7rem;
  padding: 2px 8px;
  background: rgba(99,179,237,.1);
  border: 1px solid rgba(99,179,237,.2);
  border-radius: 999px;
  color: var(--accent);
  white-space: nowrap;
}

.history-item-right {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 6px;
  flex-shrink: 0;
}
.history-time {
  font-size: .72rem;
  color: var(--text-muted);
  font-family: var(--font-mono);
}
.btn-rescan-history {
  background: transparent;
  border: none;
  color: var(--accent);
  font-size: .75rem;
  font-family: var(--font-ui);
  cursor: pointer;
  padding: 0;
  transition: opacity var(--transition);
}
.btn-rescan-history:hover { opacity: .7; text-decoration: underline; }

/* ── ANIMATIONS ──────────────────────────────────────── */
@keyframes fadeUp {
  from { opacity: 0; transform: translateY(16px); }
  to   { opacity: 1; transform: translateY(0); }
}

/* ══════════════════════════════════════════════════════
   SHARED SECTION HEADER
══════════════════════════════════════════════════════ */
.section-header-wrap {
  text-align: center;
  margin-bottom: 48px;
}
.section-eyebrow {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-family: var(--font-mono);
  font-size: .72rem;
  letter-spacing: .12em;
  text-transform: uppercase;
  color: var(--accent);
  background: rgba(99,179,237,.08);
  border: 1px solid rgba(99,179,237,.2);
  padding: 5px 14px;
  border-radius: 999px;
  margin-bottom: 18px;
}
.section-heading {
  font-size: clamp(1.6rem, 3.5vw, 2.2rem);
  font-weight: 700;
  letter-spacing: -.03em;
  line-height: 1.18;
  margin-bottom: 14px;
}
.section-subtext {
  font-size: .95rem;
  color: var(--text-secondary);
  line-height: 1.65;
  max-width: 520px;
  margin: 0 auto;
}

/* ══════════════════════════════════════════════════════
   PLATFORMS SECTION
══════════════════════════════════════════════════════ */
.platforms-section {
  margin-top: 88px;
  padding-top: 56px;
  border-top: 1px solid var(--border);
}

.platforms-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 16px;
}

@media (max-width: 580px) {
  .platforms-grid { grid-template-columns: 1fr; }
}

/* Platform Card */
.platform-card {
  position: relative;
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: var(--radius-lg);
  padding: 28px 24px 24px;
  overflow: hidden;
  transition: border-color .25s ease, transform .25s ease, box-shadow .25s ease;
  opacity: 0;
  transform: translateY(24px);
}
.platform-card.is-visible {
  opacity: 1;
  transform: translateY(0);
  transition: opacity .55s ease, transform .55s ease, border-color .25s ease, box-shadow .25s ease;
}
.platform-card::before {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, rgba(99,179,237,.06), transparent 60%);
  pointer-events: none;
  transition: background .3s ease;
}
.platform-card[style*="#68d391"]::before { background: linear-gradient(135deg, rgba(104,211,145,.07), transparent 60%); }
.platform-card[style*="#f687b3"]::before { background: linear-gradient(135deg, rgba(246,135,179,.07), transparent 60%); }
.platform-card[style*="#81e6d9"]::before { background: linear-gradient(135deg, rgba(129,230,217,.07), transparent 60%); }

.platform-card::after {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 2px;
  background: linear-gradient(90deg, transparent, var(--card-accent), transparent);
  opacity: 0;
  transition: opacity .3s ease;
}
.platform-card:hover {
  border-color: rgba(99,179,237,.35);
  transform: translateY(-3px);
  box-shadow: 0 12px 40px rgba(0,0,0,.35);
}
.platform-card[style*="#68d391"]:hover { border-color: rgba(104,211,145,.35); }
.platform-card[style*="#f687b3"]:hover { border-color: rgba(246,135,179,.35); }
.platform-card[style*="#81e6d9"]:hover { border-color: rgba(129,230,217,.35); }
.platform-card:hover::after { opacity: 1; }

.pc-icon {
  font-size: 1.8rem;
  line-height: 1;
  margin-bottom: 10px;
  display: block;
}
.pc-number {
  font-family: var(--font-mono);
  font-size: .65rem;
  letter-spacing: .12em;
  color: var(--card-accent);
  opacity: .7;
  margin-bottom: 10px;
}
.pc-title {
  font-size: 1rem;
  font-weight: 700;
  letter-spacing: -.01em;
  margin-bottom: 10px;
  color: var(--text-primary);
  line-height: 1.3;
}
.pc-desc {
  font-size: .8rem;
  color: var(--text-secondary);
  line-height: 1.65;
  margin-bottom: 18px;
}
.pc-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
}

/* Platform Tags */
.ptag {
  display: inline-flex;
  align-items: center;
  font-size: .72rem;
  font-weight: 600;
  padding: 4px 11px;
  border-radius: 999px;
  border: 1px solid;
  transition: transform .15s ease, box-shadow .15s ease;
  cursor: default;
  white-space: nowrap;
}
.ptag:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,.3);
}
.ptag--wp    { color: #63b3ed; background: rgba(99,179,237,.1);  border-color: rgba(99,179,237,.25); }
.ptag--green { color: #68d391; background: rgba(104,211,145,.1); border-color: rgba(104,211,145,.25); }
.ptag--pink  { color: #f687b3; background: rgba(246,135,179,.1); border-color: rgba(246,135,179,.25); }
.ptag--teal  { color: #81e6d9; background: rgba(129,230,217,.1); border-color: rgba(129,230,217,.25); }

/* ══════════════════════════════════════════════════════
   FAQ SECTION
══════════════════════════════════════════════════════ */
.faq-section {
  margin-top: 88px;
  padding-top: 56px;
  border-top: 1px solid var(--border);
}

.faq-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
  max-width: 780px;
  margin: 0 auto;
}

/* FAQ Item */
.faq-item {
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  overflow: hidden;
  transition: border-color .2s ease;
}
.faq-item:hover {
  border-color: rgba(99,179,237,.2);
}
.faq-item.faq-open {
  border-color: rgba(99,179,237,.35);
  box-shadow: 0 0 0 1px rgba(99,179,237,.1), 0 8px 32px rgba(0,0,0,.25);
}

/* FAQ Trigger (button) */
.faq-trigger {
  width: 100%;
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 20px 22px;
  background: transparent;
  border: none;
  cursor: pointer;
  text-align: left;
  color: var(--text-primary);
  font-family: var(--font-ui);
  font-size: .93rem;
  font-weight: 600;
  letter-spacing: -.01em;
  line-height: 1.4;
  transition: background .15s ease;
}
.faq-trigger:hover { background: rgba(255,255,255,.02); }

.faq-q-num {
  flex-shrink: 0;
  font-family: var(--font-mono);
  font-size: .65rem;
  color: var(--accent);
  background: rgba(99,179,237,.1);
  border: 1px solid rgba(99,179,237,.2);
  padding: 3px 8px;
  border-radius: 6px;
  letter-spacing: .06em;
}
.faq-q-text { flex: 1; }

.faq-chevron {
  flex-shrink: 0;
  color: var(--text-muted);
  transition: transform .3s cubic-bezier(.4,0,.2,1), color .2s ease;
  display: flex;
  align-items: center;
}
.faq-open .faq-chevron {
  transform: rotate(180deg);
  color: var(--accent);
}

/* FAQ Body (accordion) */
.faq-body {
  max-height: 0;
  overflow: hidden;
  transition: max-height .35s cubic-bezier(.4,0,.2,1);
}
.faq-body-inner {
  padding: 0 22px 22px 60px;
  font-size: .875rem;
  color: var(--text-secondary);
  line-height: 1.75;
  border-top: 1px solid var(--border);
  padding-top: 18px;
  margin-left: 0;
}
.faq-body-inner p { margin: 0; }
.faq-body-inner strong { color: var(--text-primary); }
.faq-body-inner code {
  font-family: var(--font-mono);
  font-size: .8em;
  color: var(--accent);
  background: rgba(99,179,237,.08);
  padding: 1px 6px;
  border-radius: 4px;
}

/* ── RESPONSIVE ──────────────────────────────────────── */
@media (max-width: 520px) {
  .hero-title { font-size: 2rem; }
  .scan-btn-label { display: none; }
  .scan-btn { padding: 11px 16px; }
  .results-header { flex-direction: column; align-items: flex-start; }
  .history-item-right { display: none; }
  .topnav-links { display: none; }
  .faq-body-inner { padding-left: 22px; }
  .faq-trigger { padding: 16px 16px; }
}
</style>

<!-- ══════════════════════════════════════════════════════
     JAVASCRIPT
══════════════════════════════════════════════════════ -->
<script>
(function () {
  'use strict';

  // ── Element refs ──────────────────────────────────────
  const urlInput      = document.getElementById('url-input');
  const scanBtn       = document.getElementById('scan-btn');
  const scanHint      = document.getElementById('scan-hint');
  const loaderWrap    = document.getElementById('loader-wrap');
  const loaderText    = document.getElementById('loader-text');
  const loaderUrl     = document.getElementById('loader-url');
  const resultsSection= document.getElementById('results-section');
  const resultsGrid   = document.getElementById('results-grid');
  const resultHost    = document.getElementById('result-host');
  const noResults     = document.getElementById('no-results');
  const copyBtn       = document.getElementById('copy-btn');
  const rescanBtn     = document.getElementById('rescan-btn');

  // ── Category meta for display ─────────────────────────
  const CATEGORIES = {
    cms:        { label: 'CMS',                 icon: '🏗️' },
    languages:  { label: 'Programming Language', icon: '💻' },
    frameworks: { label: 'Framework / Library',  icon: '⚙️' },
    plugins:    { label: 'Plugins & Services',   icon: '🔌' },
    servers:    { label: 'Web Server',           icon: '🖥️' },
  };

  // ── Loading messages (rotated during scan) ────────────
  const LOADING_MSGS = [
    'Fetching page source…',
    'Scanning HTTP headers…',
    'Matching technology signatures…',
    'Analyzing cookies and scripts…',
    'Calculating confidence scores…',
    'Almost there…',
  ];

  // ── State ─────────────────────────────────────────────
  let loadingTimer  = null;
  let lastResults   = null;
  let currentUrl    = '';

  // ── Trigger scan ──────────────────────────────────────
  function startScan(targetUrl) {
    const url = (targetUrl || urlInput.value).trim();

    if (!url) {
      showHint('Please enter a URL first.', true);
      urlInput.focus();
      return;
    }

    // Basic client-side format check
    let normalized = url;
    if (!/^https?:\/\//i.test(normalized)) normalized = 'https://' + normalized;

    try { new URL(normalized); } catch {
      showHint('That doesn\'t look like a valid URL. Try: https://example.com', true);
      return;
    }

    currentUrl = normalized;
    if (targetUrl) urlInput.value = normalized;

    setState('loading');
    doScan(normalized);
  }

  // ── AJAX scan ─────────────────────────────────────────
  async function doScan(url) {
    try {
      const res  = await fetch('detect.php', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json' },
        body:    JSON.stringify({ url }),
      });

      if (!res.ok) throw new Error(`HTTP ${res.status}`);

      const json = await res.json();

      if (!json.success) {
        setState('error', json.error || 'An unknown error occurred.');
        return;
      }

      lastResults = json;
      renderResults(json);
      setState('results');

    } catch (err) {
      setState('error', 'Network error: ' + err.message);
    }
  }

  // ── Render results ─────────────────────────────────────
  function renderResults(json) {
    const data = json.data || {};
    resultsGrid.innerHTML = '';
    resultHost.textContent = json.scanned || '';

    let totalItems = 0;
    let delay = 0;

    for (const [catKey, meta] of Object.entries(CATEGORIES)) {
      const items = data[catKey] || [];
      if (!items.length) continue;

      totalItems += items.length;

      const card = document.createElement('div');
      card.className = 'result-card';
      card.style.animationDelay = delay + 'ms';
      delay += 70;

      card.innerHTML = `
        <div class="card-header">
          <div class="card-category-icon">${meta.icon}</div>
          <span class="card-category-label">${meta.label}</span>
        </div>
        <div class="tech-list">
          ${items.map(tech => renderTechItem(tech)).join('')}
        </div>
      `;

      resultsGrid.appendChild(card);
    }

    noResults.hidden = totalItems > 0;

    // Animate bars after paint
    requestAnimationFrame(() => {
      document.querySelectorAll('.tech-bar-fill').forEach(bar => {
        const width = bar.dataset.width;
        setTimeout(() => { bar.style.width = width; }, 100);
      });
    });
  }

  function renderTechItem(tech) {
    const pct   = Math.round(tech.score);
    const color = tech.color || '#63b3ed';
    return `
      <div class="tech-item">
        <div class="tech-icon">${tech.icon || '🔧'}</div>
        <div class="tech-info">
          <div class="tech-name">${escHtml(tech.name)}</div>
        </div>
        <div class="tech-score-wrap">
          <span class="tech-score-label">${pct}%</span>
          <div class="tech-bar-bg">
            <div class="tech-bar-fill" data-width="${pct}%" style="width:0%;background:${color}"></div>
          </div>
        </div>
      </div>
    `;
  }

  // ── UI state machine ──────────────────────────────────
  function setState(state, errorMsg) {
    // Reset everything first
    loaderWrap.hidden     = true;
    resultsSection.hidden = true;
    scanBtn.disabled      = false;
    clearInterval(loadingTimer);
    showHint('Enter any public URL to scan its technology stack', false);

    if (state === 'loading') {
      loaderWrap.hidden = false;
      loaderUrl.textContent = currentUrl;
      scanBtn.disabled = true;

      // Rotate loading messages
      let msgIdx = 0;
      loaderText.textContent = LOADING_MSGS[0];
      loadingTimer = setInterval(() => {
        msgIdx = (msgIdx + 1) % LOADING_MSGS.length;
        loaderText.textContent = LOADING_MSGS[msgIdx];
      }, 1800);

      // Scroll into view
      loaderWrap.scrollIntoView({ behavior: 'smooth', block: 'center' });

    } else if (state === 'results') {
      resultsSection.hidden = false;
      setTimeout(() => {
        resultsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }, 100);

    } else if (state === 'error') {
      showHint(errorMsg || 'Something went wrong.', true);
      urlInput.focus();
      urlInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
  }

  function showHint(msg, isError) {
    scanHint.textContent = msg;
    scanHint.classList.toggle('error', !!isError);
  }

  // ── Copy results ──────────────────────────────────────
  copyBtn.addEventListener('click', () => {
    if (!lastResults) return;
    const lines = [`StackDetect report for: ${lastResults.final_url}`, ''];
    const data  = lastResults.data || {};
    for (const [cat, items] of Object.entries(data)) {
      if (items.length) {
        lines.push(`[${cat.toUpperCase()}]`);
        items.forEach(t => lines.push(`  • ${t.name} (${t.score}%)`));
        lines.push('');
      }
    }
    lines.push(`Scanned at: ${lastResults.meta?.scanned_at || new Date().toISOString()}`);

    navigator.clipboard.writeText(lines.join('\n')).then(() => {
      copyBtn.textContent = '✓ Copied!';
      setTimeout(() => { copyBtn.textContent = '📋 Copy'; }, 2000);
    });
  });

  // ── Rescan button ─────────────────────────────────────
  rescanBtn.addEventListener('click', () => {
    resultsSection.hidden = true;
    urlInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
    urlInput.focus();
  });

  // ── History rescan buttons ────────────────────────────
  document.querySelectorAll('.btn-rescan-history').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      startScan(btn.dataset.url);
    });
  });
  document.querySelectorAll('.history-item').forEach(item => {
    item.addEventListener('click', () => startScan(item.dataset.url));
  });

  // ── Keyboard: Enter to scan ───────────────────────────
  urlInput.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') startScan();
  });

  // ── Click scan button ─────────────────────────────────
  scanBtn.addEventListener('click', () => startScan());

  // ── Paste auto-scan option ────────────────────────────
  urlInput.addEventListener('paste', () => {
    showHint('Press Enter or click Detect to scan', false);
  });

  // ── Util: escape HTML ─────────────────────────────────
  function escHtml(str) {
    return String(str)
      .replace(/&/g,'&amp;').replace(/</g,'&lt;')
      .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }

})();

// ── FAQ Accordion ──────────────────────────────────────
(function () {
  const triggers = document.querySelectorAll('.faq-trigger');

  triggers.forEach(function (btn) {
    btn.addEventListener('click', function () {
      const item    = btn.closest('.faq-item');
      const bodyId  = btn.getAttribute('aria-controls');
      const body    = document.getElementById(bodyId);
      const isOpen  = item.classList.contains('faq-open');

      // Close all other items
      document.querySelectorAll('.faq-item.faq-open').forEach(function (openItem) {
        if (openItem !== item) {
          openItem.classList.remove('faq-open');
          openItem.querySelector('.faq-trigger').setAttribute('aria-expanded', 'false');
          var ob = openItem.querySelector('.faq-body');
          ob.style.maxHeight = '0';
        }
      });

      if (isOpen) {
        item.classList.remove('faq-open');
        btn.setAttribute('aria-expanded', 'false');
        body.style.maxHeight = '0';
      } else {
        item.classList.add('faq-open');
        btn.setAttribute('aria-expanded', 'true');
        body.style.maxHeight = body.scrollHeight + 'px';
      }
    });
  });
})();

// ── Platform Card Scroll-Reveal ────────────────────────
(function () {
  if (!('IntersectionObserver' in window)) {
    document.querySelectorAll('.reveal-card').forEach(function (el) {
      el.classList.add('is-visible');
    });
    return;
  }

  var observer = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry, i) {
      if (entry.isIntersecting) {
        var el = entry.target;
        var idx = Array.from(document.querySelectorAll('.reveal-card')).indexOf(el);
        setTimeout(function () {
          el.classList.add('is-visible');
        }, idx * 90);
        observer.unobserve(el);
      }
    });
  }, { threshold: 0.12 });

  document.querySelectorAll('.reveal-card').forEach(function (el) {
    observer.observe(el);
  });
})();
</script>

<?php include __DIR__ . '/templates/footer.php'; ?>
