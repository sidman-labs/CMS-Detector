<?php
/**
 * home.php — CMS Detector
 * Clean white modern SaaS-style CMS detector homepage with full SEO
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';

$page_title       = 'CMS Detector — Check What CMS Any Website Uses (Free, Instant)';
$page_description = 'Free CMS detector tool. Paste any URL to instantly check what CMS, framework, or plugins a website uses. Detects WordPress, Shopify, Joomla, Drupal, Wix, and 400+ technologies in seconds — no signup needed.';
$canonical_url    = 'https://cms-detector.rf.gd/';
$og_image         = 'https://cms-detector.rf.gd/assets/images/thumbnail.png';
$favicon_url      = 'https://cms-detector.rf.gd/assets/images/icon.png';
$history          = ENABLE_HISTORY ? load_scan_history() : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <!-- Primary SEO -->
  <title><?= htmlspecialchars($page_title) ?></title>
  <meta name="description" content="<?= htmlspecialchars($page_description) ?>" />
  <meta name="keywords" content="cms detector, check cms, what cms is this, cms checker, which cms, detect cms, what cms is this website using, cms finder, website cms checker" />
  <meta name="robots" content="index, follow" />
  <meta name="author" content="CMS Detector" />
  <link rel="canonical" href="<?= htmlspecialchars($canonical_url) ?>" />
  <link rel="icon" type="image/png" href="<?= htmlspecialchars($favicon_url) ?>" />
  <link rel="shortcut icon" href="<?= htmlspecialchars($favicon_url) ?>" />

  <!-- Open Graph -->
  <meta property="og:type" content="website" />
  <meta property="og:url" content="<?= htmlspecialchars($canonical_url) ?>" />
  <meta property="og:title" content="<?= htmlspecialchars($page_title) ?>" />
  <meta property="og:description" content="<?= htmlspecialchars($page_description) ?>" />
  <meta property="og:image" content="<?= htmlspecialchars($og_image) ?>" />
  <meta property="og:site_name" content="CMS Detector" />
  <meta property="og:locale" content="en_US" />

  <!-- Twitter Card -->
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="<?= htmlspecialchars($page_title) ?>" />
  <meta name="twitter:description" content="<?= htmlspecialchars($page_description) ?>" />
  <meta name="twitter:image" content="<?= htmlspecialchars($og_image) ?>" />

  <!-- Schema.org JSON-LD -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "WebApplication",
    "name": "CMS Detector",
    "url": "<?= htmlspecialchars($canonical_url) ?>",
    "description": "<?= htmlspecialchars($page_description) ?>",
    "applicationCategory": "DeveloperApplication",
    "operatingSystem": "Any",
    "offers": {
      "@type": "Offer",
      "price": "0",
      "priceCurrency": "USD"
    },
    "featureList": [
      "CMS Detection",
      "Framework Detection",
      "Plugin Detection",
      "Tech Stack Analysis",
      "400+ Technologies"
    ],
    "creator": {
      "@type": "Organization",
      "name": "CMS Detector",
      "url": "<?= htmlspecialchars($canonical_url) ?>"
    }
  }
  </script>

  <!-- FAQPage Schema -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [
      {
        "@type": "Question",
        "name": "How do I detect what CMS a website is using?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "Simply paste any URL into the scan bar and click Detect CMS. Our engine scans the site's HTML, headers, and scripts and shows you results in seconds — completely free, no account needed."
        }
      },
      {
        "@type": "Question",
        "name": "Is CMS Detector really free with no limits?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "Yes, 100% free with unlimited scans and zero registration. There are no rate limits or hidden plans for standard lookups."
        }
      },
      {
        "@type": "Question",
        "name": "How does the CMS detection algorithm work?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "We use multi-signal fingerprinting — checking meta name=generator tags, HTTP headers, unique paths like /wp-content/, cookie names, and inline script patterns, each weighted to produce a confidence score."
        }
      },
      {
        "@type": "Question",
        "name": "Can it detect WordPress plugins and themes?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "Yes. By inspecting asset directories and script handles we can identify the active WordPress theme and many installed plugins including Elementor, WooCommerce, Yoast SEO, and Divi."
        }
      },
      {
        "@type": "Question",
        "name": "What if a site hides its technology stack?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "Some sites deliberately remove headers or obfuscate paths. In those cases detection accuracy is lower — we only show results we can confirm with high confidence. We never show false positives."
        }
      }
    ]
  }
  </script>

  <!-- Fonts: Premium typography for scan results -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet" />
  
  <!-- Font Awesome 6 Free Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <style>
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
    :root{
      --white:#fff;--bg:#f7f8fc;--border:#e4e7f0;--border-2:#c8cde0;
      --ink:#0f1117;--ink-2:#374151;--ink-3:#6b7280;--ink-4:#9ca3af;
      --blue:#2563eb;--blue-dk:#1d4ed8;--blue-lt:#eff6ff;--blue-mid:#dbeafe;
      --green:#16a34a;--rose:#e11d48;--indigo:#4f46e5;
      --sh-sm:0 1px 3px rgba(0,0,0,.06),0 1px 2px rgba(0,0,0,.04);
      --sh-md:0 4px 16px rgba(0,0,0,.07),0 2px 6px rgba(0,0,0,.04);
      --sh-lg:0 12px 40px rgba(0,0,0,.10),0 4px 12px rgba(0,0,0,.05);
      --r-sm:6px;--r-md:10px;--r-lg:16px;--r-xl:24px;--r-2xl:40px;
      --font:'Plus Jakarta Sans',sans-serif;--mono:'JetBrains Mono',monospace;
      --poppins:'Poppins',sans-serif;--inter:'Inter',sans-serif;
    }
    html{scroll-behavior:smooth}
    body{background:var(--white);color:var(--ink);font-family:var(--font);font-size:16px;line-height:1.6;-webkit-font-smoothing:antialiased;overflow-x:hidden}
    a{color:inherit;text-decoration:none}
    button{font-family:var(--font);cursor:pointer;border:none}

    /* NAV */
    .nav{position:sticky;top:0;z-index:200;background:rgba(255,255,255,.93);backdrop-filter:blur(16px);border-bottom:1px solid var(--border)}
    .nav-in{max-width:1160px;margin:0 auto;padding:0 24px;height:62px;display:flex;align-items:center;gap:28px}
    .logo{display:flex;align-items:center;gap:9px;font-weight:800;font-size:1.05rem;flex-shrink:0}
    .logo-mark{width:30px;height:30px;border-radius:8px;background:var(--blue);display:flex;align-items:center;justify-content:center;color:#fff;font-size:.85rem;font-weight:800}
    .nav-menu{display:flex;align-items:center;gap:2px;flex:1}
    .nav-menu a{font-size:.85rem;font-weight:500;color:var(--ink-3);padding:6px 12px;border-radius:var(--r-sm);transition:color .15s,background .15s}
    .nav-menu a:hover{color:var(--ink);background:var(--bg)}
    .btn-primary-sm{font-size:.83rem;font-weight:700;padding:8px 18px;border-radius:var(--r-sm);background:var(--blue);color:#fff;transition:background .15s,box-shadow .15s}
    .btn-primary-sm:hover{background:var(--blue-dk);box-shadow:0 4px 12px rgba(37,99,235,.3)}
    @media(max-width:640px){.nav-menu{display:none}}

    /* HERO */
    .hero-wrap{position:relative;overflow:hidden;background:#fff}
    .blob{position:absolute;border-radius:50%;pointer-events:none;filter:blur(72px);opacity:.6}
    .blob-1{width:680px;height:480px;top:-140px;left:-200px;background:radial-gradient(ellipse,#c7d9ff 0%,#e0f2fe 50%,transparent 75%)}
    .blob-2{width:520px;height:400px;top:-100px;right:-160px;background:radial-gradient(ellipse,#ddd6fe 0%,#ede9fe 50%,transparent 75%)}
    .blob-3{width:380px;height:280px;bottom:-60px;left:42%;background:radial-gradient(ellipse,#bbf7d0 0%,#d1fae5 60%,transparent 80%);opacity:.35}
    .hero{position:relative;z-index:1;max-width:860px;margin:0 auto;padding:84px 24px 76px;text-align:center}
    .hero-badge{display:inline-flex;align-items:center;gap:7px;padding:5px 14px;border-radius:999px;background:#eff6ff;border:1px solid #bfdbfe;font-size:.74rem;font-weight:600;color:var(--blue);letter-spacing:.05em;margin-bottom:28px}
    .bdot{width:6px;height:6px;border-radius:50%;background:var(--blue);animation:pulse 2s ease infinite}
    @keyframes pulse{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.35;transform:scale(.8)}}
    .hero-h1{font-size:clamp(2.3rem,5.5vw,3.8rem);font-weight:800;line-height:1.1;letter-spacing:-.03em;margin-bottom:20px}
    .hero-h1 .hl{color:var(--blue)}
    .hero-p{font-size:1.05rem;color:var(--ink-3);line-height:1.72;max-width:520px;margin:0 auto 40px;font-weight:400}

    /* SCAN */
    .scan-wrap{max-width:640px;margin:0 auto}
    .scan-box{background:#fff;border:1.5px solid var(--border-2);border-radius:var(--r-xl);padding:7px 7px 7px 20px;display:flex;align-items:center;gap:12px;box-shadow:var(--sh-lg);transition:border-color .2s,box-shadow .2s}
    .scan-box:focus-within{border-color:var(--blue);box-shadow:0 0 0 4px rgba(37,99,235,.1),var(--sh-lg)}
    .scan-ico{flex-shrink:0;color:var(--ink-4);display:flex;align-items:center}
    .scan-ico svg{width:18px;height:18px}
    .scan-input{flex:1;border:none;outline:none;font-family:var(--mono);font-size:.875rem;color:var(--ink);background:transparent;min-width:0;padding:10px 0}
    .scan-input::placeholder{color:var(--ink-4)}
    .scan-btn{flex-shrink:0;display:flex;align-items:center;gap:8px;background:var(--blue);color:#fff;font-weight:700;font-size:.9rem;padding:12px 24px;border-radius:var(--r-lg);border:none;transition:background .2s,box-shadow .2s,transform .15s;white-space:nowrap}
    .scan-btn:hover{background:var(--blue-dk);box-shadow:0 6px 20px rgba(37,99,235,.35);transform:translateY(-1px)}
    .scan-btn:active{transform:none}
    .scan-btn:disabled{opacity:.5;cursor:not-allowed;transform:none!important}
    .scan-btn svg{width:16px;height:16px}
    .scan-note{margin-top:13px;font-size:.78rem;color:var(--ink-4)}
    .scan-note a{color:var(--blue)}
    .scan-note.err{color:var(--rose)}


    /* TRUST */
    .trust-row{display:flex;align-items:center;justify-content:center;gap:22px;margin-top:42px;flex-wrap:wrap}
    .ti{display:flex;align-items:center;gap:7px;font-size:.8rem;color:var(--ink-3);font-weight:500}
    .ti svg{width:15px;height:15px;color:var(--green)}
    .tsep{width:4px;height:4px;border-radius:50%;background:var(--border-2)}

    /* LOADER */
    .loader-sec{text-align:center;padding:64px 24px}
    .loader-vis{position:relative;width:64px;height:64px;margin:0 auto 24px}
    .lr{position:absolute;inset:0;border-radius:50%;border:2.5px solid transparent}
    .lr-1{border-top-color:var(--blue);animation:spin 1s linear infinite}
    .lr-2{inset:10px;border-top-color:var(--indigo);animation:spin .7s linear infinite reverse}
    @keyframes spin{to{transform:rotate(360deg)}}
    .lr-ico{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-size:1.2rem}
    .loader-msg{font-size:.9rem;font-weight:600;color:var(--ink-2);margin-bottom:6px}
    .loader-url{font-family:var(--mono);font-size:.74rem;color:var(--ink-4);margin-bottom:22px}
    .loader-pills{display:flex;justify-content:center;gap:8px;flex-wrap:wrap}
    .lpill{font-size:.71rem;font-family:var(--mono);padding:4px 13px;border-radius:999px;border:1.5px solid var(--border-2);color:var(--ink-4);background:#fff;transition:all .25s}
    .lpill.on{border-color:var(--blue);color:var(--blue);background:var(--blue-lt)}

    /* RESULTS */
    .results-outer{max-width:1160px;margin:0 auto;padding:0 24px 64px}
    .rtopbar{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;padding:14px 20px;background:#f0fdf4;border:1.5px solid #bbf7d0;border-radius:var(--r-lg);margin-bottom:22px}
    .rtb-left{display:flex;align-items:center;gap:10px;font-size:.875rem;font-weight:600;color:var(--green)}
    .rtb-dot{width:8px;height:8px;border-radius:50%;background:var(--green);box-shadow:0 0 8px rgba(22,163,74,.5);animation:pulse 2s infinite}
    .rtb-url{font-family:var(--mono);font-size:.78rem;color:var(--ink-2);font-weight:400}
    .rtb-right{display:flex;gap:8px}
    .btn-act{display:flex;align-items:center;gap:6px;padding:6px 14px;border-radius:var(--r-sm);border:1.5px solid var(--border-2);background:#fff;font-size:.78rem;font-weight:600;color:var(--ink-2);transition:border-color .15s,color .15s}
    .btn-act:hover{border-color:var(--blue);color:var(--blue)}
    .btn-act svg{width:13px;height:13px}
    .res-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(290px,1fr));gap:16px}
    .rcard{background:#fff;border:1.5px solid var(--border);border-radius:var(--r-lg);padding:24px;box-shadow:0 2px 8px rgba(0,0,0,.08);transition:border-color .2s,box-shadow .2s,transform .2s;animation:fadeUp .35s ease backwards}
    .rcard:hover{border-color:var(--blue);box-shadow:0 8px 24px rgba(37,99,235,.12);transform:translateY(-3px)}
    .rcard-head{display:flex;align-items:center;gap:12px;padding-bottom:16px;border-bottom:2px solid var(--border);margin-bottom:18px}
    .rcard-ico{width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;flex-shrink:0;font-weight:600;color:inherit}
    .rcard-lbl{font-size:.72rem;font-weight:800;letter-spacing:.12em;text-transform:uppercase;color:var(--ink-2);flex:1;font-family:var(--poppins)}
    .rcard-bdg{font-size:.7rem;font-weight:700;padding:4px 12px;border-radius:8px;background:var(--blue-lt);color:var(--blue);border:1.5px solid #bfdbfe;font-family:var(--poppins)}
    .tech-rows{display:flex;flex-direction:column;gap:10px}
    .tech-row{display:flex;align-items:center;gap:11px;padding:12px 14px;border-radius:var(--r-md);background:rgba(37,99,235,.02);border:1px solid rgba(37,99,235,.1);transition:all .2s}
    .tech-row:hover{background:var(--blue-lt);border-color:#bfdbfe}
    .tech-ico{width:28px;height:28px;display:flex;align-items:center;justify-content:center;flex-shrink:0;color:var(--blue);font-size:1rem}
    .tech-ico img{width:22px;height:22px;border-radius:4px;object-fit:contain}
    .tech-emoji{font-size:1rem;flex-shrink:0}
    .tech-name{flex:1;font-size:.875rem;font-weight:600;color:var(--ink);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;min-width:0;font-family:var(--poppins)}
    .tech-sc{flex-shrink:0;text-align:right}
    .tech-pct{font-family:var(--mono);font-size:.7rem;color:var(--blue);display:block;margin-bottom:4px;font-weight:600}
    .pbar{width:60px;height:4px;border-radius:2px;background:var(--border-2);overflow:hidden}
    .pfill{height:100%;border-radius:2px;width:0;transition:width 1.1s cubic-bezier(.34,1.2,.64,1)}
    .no-detect{text-align:center;padding:56px 24px;border:2px dashed var(--border-2);border-radius:var(--r-xl)}
    .no-detect-ico{font-size:2.5rem;margin-bottom:14px}
    .no-detect h3{font-size:1rem;font-weight:700;margin-bottom:8px}
    .no-detect p{font-size:.875rem;color:var(--ink-3)}

    /* LOGOS / TECH GRID */
    .logos-sec{background:var(--bg);border-top:1px solid var(--border);border-bottom:1px solid var(--border);padding:60px 24px}
    .logos-in{max-width:1100px;margin:0 auto}
    .logos-h{text-align:center;font-size:1.3rem;font-weight:800;letter-spacing:-.02em;margin-bottom:8px}
    .logos-sub{text-align:center;font-size:.875rem;color:var(--ink-3);margin-bottom:40px}
    .logos-grid{display:grid;grid-template-columns:repeat(6,1fr);gap:10px}
    @media(max-width:900px){.logos-grid{grid-template-columns:repeat(5,1fr)}}
    @media(max-width:640px){.logos-grid{grid-template-columns:repeat(4,1fr)}}
    @media(max-width:420px){.logos-grid{grid-template-columns:repeat(3,1fr)}}
    .lchip{display:flex;flex-direction:column;align-items:center;gap:7px;padding:16px 8px;border-radius:var(--r-md);background:#fff;border:1.5px solid var(--border);font-size:.71rem;font-weight:600;color:var(--ink-3);text-align:center;transition:border-color .2s,color .2s,box-shadow .2s,transform .2s;cursor:default}
    .lchip:hover{border-color:var(--blue);color:var(--blue);box-shadow:var(--sh-md);transform:translateY(-2px)}
    .lchip-ico{width:36px;height:36px;display:flex;align-items:center;justify-content:center;line-height:1;font-size:1.5rem}
    .lchip-ico img{width:36px;height:36px;object-fit:contain;border-radius:6px;display:block}
    .lchip-ico.emoji{font-size:1.5rem}

    /* NUMBERS STRIP */
    .nums-strip{background:var(--blue);padding:48px 24px}
    .nums-in{max-width:1000px;margin:0 auto;display:grid;grid-template-columns:repeat(4,1fr);gap:2px}
    @media(max-width:640px){.nums-in{grid-template-columns:repeat(2,1fr)}}
    .num-item{padding:24px 20px;text-align:center;border-right:1px solid rgba(255,255,255,.15)}
    .num-item:last-child{border-right:none}
    .num-val{font-size:2.1rem;font-weight:800;color:#fff;letter-spacing:-.03em;line-height:1;margin-bottom:6px}
    .num-lbl{font-size:.78rem;color:rgba(255,255,255,.7);font-weight:500}

    /* HOW IT WORKS */
    .how-sec{padding:88px 24px}
    .how-in{max-width:1000px;margin:0 auto}
    .sec-label{display:inline-block;font-size:.71rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--blue);background:var(--blue-lt);border:1px solid #bfdbfe;padding:4px 12px;border-radius:999px;margin-bottom:16px}
    .sec-h2{font-size:clamp(1.6rem,3.5vw,2.4rem);font-weight:800;letter-spacing:-.03em;line-height:1.15;margin-bottom:12px}
    .sec-p{font-size:.95rem;color:var(--ink-3);max-width:460px;line-height:1.7}
    .how-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:22px;margin-top:48px}
    @media(max-width:640px){.how-grid{grid-template-columns:1fr}}
    .how-card{padding:28px 24px;border-radius:var(--r-lg);border:1.5px solid var(--border);background:#fff;box-shadow:var(--sh-sm);transition:border-color .2s,box-shadow .2s,transform .2s}
    .how-card:hover{border-color:var(--border-2);box-shadow:var(--sh-md);transform:translateY(-3px)}
    .how-num{display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:50%;background:var(--blue);color:#fff;font-size:.8rem;font-weight:800;margin-bottom:16px}
    .how-ico{width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;margin-bottom:14px}
    .how-t{font-size:.95rem;font-weight:700;margin-bottom:8px;letter-spacing:-.01em}
    .how-d{font-size:.82rem;color:var(--ink-3);line-height:1.7}
    .how-d code{font-family:var(--mono);font-size:.8em;color:var(--blue);background:var(--blue-lt);padding:1px 5px;border-radius:4px}

    /* HISTORY */
    .history-sec{padding:88px 24px;background:var(--bg)}
    .history-in{max-width:820px;margin:0 auto}
    .history-list{display:flex;flex-direction:column;gap:10px;margin-top:44px}
    .history-item{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:16px 20px;background:#fff;border:1.5px solid var(--border);border-radius:var(--r-lg);cursor:pointer;box-shadow:var(--sh-sm);transition:border-color .2s,box-shadow .2s,transform .2s}
    .history-item:hover{border-color:var(--border-2);box-shadow:var(--sh-md);transform:translateY(-2px)}
    .history-item-left{display:flex;align-items:center;gap:12px;flex:1;min-width:0}
    .history-favicon{width:32px;height:32px;border-radius:8px;background:var(--bg);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;flex-shrink:0}
    .history-favicon img{width:18px;height:18px;border-radius:4px}
    .history-favicon-fallback{font-size:1rem}
    .history-item-info{min-width:0}
    .history-host{display:block;font-size:.875rem;font-weight:700;color:var(--ink);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;font-family:var(--mono)}
    .history-tags{display:flex;flex-wrap:wrap;gap:5px;margin-top:6px}
    .history-tag{font-size:.68rem;font-weight:600;padding:2px 9px;background:var(--blue-lt);border:1px solid #bfdbfe;border-radius:999px;color:var(--blue);white-space:nowrap}
    .history-item-right{display:flex;flex-direction:column;align-items:flex-end;gap:6px;flex-shrink:0}
    .history-time{font-size:.72rem;color:var(--ink-4);font-family:var(--mono)}
    .btn-rescan-history{background:transparent;border:none;color:var(--blue);font-size:.78rem;font-weight:700;cursor:pointer;padding:0;transition:opacity .15s}
    .btn-rescan-history:hover{opacity:.65;text-decoration:underline}
    @media(max-width:560px){.history-item-right{display:none}}

    /* FAQ */
    .faq-sec{padding:88px 24px;background:var(--bg)}
    .faq-in{max-width:760px;margin:0 auto}
    .faq-hdr{text-align:center;margin-bottom:44px}
    .faq-list{display:flex;flex-direction:column;gap:10px}
    .faq-item{border:1.5px solid var(--border);border-radius:var(--r-md);background:#fff;overflow:hidden;transition:border-color .2s,box-shadow .2s}
    .faq-item.open{border-color:#bfdbfe;box-shadow:0 0 0 3px rgba(37,99,235,.06)}
    .faq-btn{width:100%;display:flex;align-items:center;gap:14px;padding:18px 20px;background:transparent;border:none;text-align:left;cursor:pointer;transition:background .15s}
    .faq-btn:hover{background:var(--bg)}
    .faq-q{flex:1;font-size:.9rem;font-weight:700;color:var(--ink);line-height:1.45}
    .faq-tog{flex-shrink:0;width:26px;height:26px;border-radius:50%;border:1.5px solid var(--border-2);background:var(--bg);display:flex;align-items:center;justify-content:center;color:var(--ink-3);font-size:.8rem;font-weight:700;transition:all .25s}
    .faq-item.open .faq-tog{background:var(--blue);border-color:var(--blue);color:#fff;transform:rotate(45deg)}
    .faq-body{max-height:0;overflow:hidden;transition:max-height .32s cubic-bezier(.4,0,.2,1)}
    .faq-body-in{padding:0 20px 20px;font-size:.875rem;color:var(--ink-3);line-height:1.75;border-top:1px solid var(--border);padding-top:16px}
    .faq-body-in strong{color:var(--ink-2)}
    .faq-body-in code{font-family:var(--mono);font-size:.8em;color:var(--blue);background:var(--blue-lt);padding:1px 6px;border-radius:4px}

    /* CTA */
    .cta-sec{padding:80px 24px}
    .cta-box{max-width:720px;margin:0 auto;background:linear-gradient(135deg,#1d4ed8 0%,#4f46e5 100%);border-radius:var(--r-2xl);padding:56px 40px;text-align:center;position:relative;overflow:hidden}
    .cta-box::before{content:'';position:absolute;top:-40%;left:50%;transform:translateX(-50%);width:400px;height:300px;background:radial-gradient(ellipse,rgba(255,255,255,.12) 0%,transparent 70%);pointer-events:none}
    .cta-box h2{font-size:clamp(1.5rem,4vw,2.2rem);font-weight:800;color:#fff;letter-spacing:-.03em;margin-bottom:12px}
    .cta-box p{font-size:.95rem;color:rgba(255,255,255,.75);margin-bottom:30px}
    .btn-cta{display:inline-flex;align-items:center;gap:10px;padding:14px 32px;border-radius:var(--r-xl);background:#fff;color:var(--blue);font-weight:800;font-size:.95rem;border:none;box-shadow:0 8px 32px rgba(0,0,0,.2);transition:transform .2s,box-shadow .2s}
    .btn-cta:hover{transform:translateY(-2px);box-shadow:0 16px 40px rgba(0,0,0,.25)}

    /* FOOTER */
    .footer{background:var(--bg);border-top:1px solid var(--border);padding:36px 24px}
    .footer-in{max-width:1160px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px}
    .footer-copy{font-size:.78rem;color:var(--ink-4)}
    .footer-links{display:flex;gap:20px}
    .footer-links a{font-size:.78rem;color:var(--ink-3);transition:color .15s}
    .footer-links a:hover{color:var(--blue)}

    /* UTILS */
    @keyframes fadeUp{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)}}
    .appear{opacity:0;transform:translateY(20px);transition:opacity .55s ease,transform .55s ease}
    .appear.in{opacity:1;transform:none}
    .center{text-align:center}
    ::-webkit-scrollbar{width:5px}
    ::-webkit-scrollbar-track{background:var(--bg)}
    ::-webkit-scrollbar-thumb{background:var(--border-2);border-radius:3px}
  </style>
</head>
<body>

<!-- NAV -->
<nav class="nav" role="navigation" aria-label="Main navigation">
  <div class="nav-in">
    <a href="/" class="logo" aria-label="CMS Detector home">
      <div class="logo-mark" aria-hidden="true">C</div>
      CMS Detector
    </a>
    <div class="nav-menu">
      <a href="#how-it-works">How it works</a>
      <a href="#technologies">Technologies</a>
      <a href="#faq">FAQ</a>
      <?php if (ENABLE_HISTORY && !empty($history)): ?><a href="#history">History</a><?php endif; ?>
    </div>
    <button class="btn-primary-sm" onclick="document.getElementById('url-input').focus();scrollTo({top:0,behavior:'smooth'})">Try Free →</button>
  </div>
</nav>

<!-- HERO -->
<main>
<div class="hero-wrap">
  <div class="blob blob-1" aria-hidden="true"></div>
  <div class="blob blob-2" aria-hidden="true"></div>
  <div class="blob blob-3" aria-hidden="true"></div>
  <div class="hero">
    <div class="hero-badge" aria-hidden="true"><span class="bdot"></span> Instant CMS Analysis Engine</div>
    <h1 class="hero-h1">What CMS is That?<br>Use <span class="hl">CMS Detector</span> and Find Out</h1>
    <p class="hero-p">Curious about what powers your favorite sites? Instantly detect CMS, frameworks, and technologies used on any URL with our advanced detection algorithm.</p>

    <div class="scan-wrap">
      <div class="scan-box" id="scan-box" role="search">
        <div class="scan-ico" aria-hidden="true">
          <svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M10 2C5.58 2 2 5.58 2 10s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8z" stroke="currentColor" stroke-width="1.5"/><path d="M2 10h16M10 2a12.5 12.5 0 0 1 0 16M10 2a12.5 12.5 0 0 0 0 16" stroke="currentColor" stroke-width="1.5"/></svg>
        </div>
        <input type="text" id="url-input" class="scan-input" placeholder="Enter a URL (e.g., example.com)" autocomplete="off" spellcheck="false" autofocus aria-label="Website URL to detect" />
        <button class="scan-btn" id="scan-btn" type="button" aria-label="Detect CMS">
          Detect CMS
          <svg viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8h10M8.5 3.5 13 8l-4.5 4.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
      </div>
      <p class="scan-note" id="scan-note">By using this tool, you agree to our <a href="#">terms of service</a></p>
    </div>

    <div class="trust-row" aria-label="Key features">
      <div class="ti"><svg viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M13.5 4L6 11.5 2.5 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>Free forever</div>
      <div class="tsep" aria-hidden="true"></div>
      <div class="ti"><svg viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M13.5 4L6 11.5 2.5 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>No signup required</div>
      <div class="tsep" aria-hidden="true"></div>
      <div class="ti"><svg viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M13.5 4L6 11.5 2.5 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>Results in under 5 seconds</div>
      <div class="tsep" aria-hidden="true"></div>
      <div class="ti"><svg viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M13.5 4L6 11.5 2.5 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>400+ technologies</div>
    </div>
  </div>
</div>

<!-- LOADER -->
<div id="loader-wrap" hidden aria-live="polite" aria-label="Scanning in progress">
  <div class="loader-sec">
    <div class="loader-vis" aria-hidden="true">
      <div class="lr lr-1"></div>
      <div class="lr lr-2"></div>
      <div class="lr-ico">🔍</div>
    </div>
    <p class="loader-msg" id="loader-text">Initializing scan…</p>
    <div class="loader-url" id="loader-url"></div>
    <div class="loader-pills" aria-hidden="true">
      <span class="lpill" id="lpill-0">Fetching page</span>
      <span class="lpill" id="lpill-1">Reading headers</span>
      <span class="lpill" id="lpill-2">Matching signatures</span>
      <span class="lpill" id="lpill-3">Scoring results</span>
    </div>
  </div>
</div>

<!-- RESULTS -->
<section id="results-section" hidden aria-label="Detection results">
  <div class="results-outer">
    <div class="rtopbar">
      <div class="rtb-left">
        <div class="rtb-dot" aria-hidden="true"></div>
        Scan complete —
        <span class="rtb-url" id="result-host"></span>
      </div>
      <div class="rtb-right">
        <button class="btn-act" id="copy-btn" aria-label="Copy results to clipboard">
          <svg viewBox="0 0 16 16" fill="none" aria-hidden="true"><rect x="5" y="5" width="9" height="9" rx="1.5" stroke="currentColor" stroke-width="1.4"/><path d="M11 5V3.5A1.5 1.5 0 0 0 9.5 2h-6A1.5 1.5 0 0 0 2 3.5v6A1.5 1.5 0 0 0 3.5 11H5" stroke="currentColor" stroke-width="1.4"/></svg>
          Copy
        </button>
        <button class="btn-act" id="rescan-btn" aria-label="Start a new scan">
          <svg viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M13.5 8A5.5 5.5 0 1 1 8 2.5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/><path d="M13.5 2.5v3h-3" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
          New scan
        </button>
      </div>
    </div>
    <div class="res-grid" id="results-grid" role="list"></div>
    <div class="no-detect" id="no-results" hidden>
      <div class="no-detect-ico" aria-hidden="true">🔭</div>
      <h3>Nothing detected</h3>
      <p>We couldn't identify known technologies. This site may use custom or obfuscated code.</p>
    </div>
  </div>
</section>
</main>

<!-- ══════════════════════════════════════════════════════
     RECENT SCANS HISTORY (dynamic content – SEO priority)
══════════════════════════════════════════════════════ -->
<?php if (ENABLE_HISTORY && !empty($history)): ?>
<section class="history-sec" id="history" aria-label="Recently detected websites">
  <div class="history-in">
    <div class="center appear">
      <div class="sec-label">Live Activity</div>
      <h2 class="sec-h2">Recent Website Detections</h2>
      <p class="sec-p" style="margin:0 auto">See what other people just scanned with CMS Detector.</p>
    </div>
    <div class="history-list appear" id="history-list" role="list">
      <?php foreach (array_slice($history, 0, 8) as $scan): ?>
      <div class="history-item" role="listitem" data-url="<?= htmlspecialchars($scan['url']) ?>">
        <div class="history-item-left">
          <div class="history-favicon">
            <img
              src="https://www.google.com/s2/favicons?domain=<?= urlencode(parse_url($scan['url'], PHP_URL_HOST) ?? $scan['url']) ?>&sz=64"
              alt="<?= htmlspecialchars(parse_url($scan['url'], PHP_URL_HOST) ?? '') ?> favicon"
              loading="lazy"
              onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-block';"
            />
            <span class="history-favicon-fallback" aria-hidden="true" style="display:none">🌐</span>
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
          <button class="btn-rescan-history" data-url="<?= htmlspecialchars($scan['url']) ?>" type="button">
            Scan again →
          </button>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- TECH LOGOS GRID -->
<section class="logos-sec" id="technologies" aria-label="Supported technologies">
  <div class="logos-in">
    <h2 class="logos-h appear">Detects Hundreds of Technologies</h2>
    <p class="logos-sub appear">Our algorithm recognizes over 400+ CMS types, frameworks, and e-commerce platforms instantly.</p>
    <div class="logos-grid appear" role="list">

      <div class="lchip" role="listitem">
        <span class="lchip-ico"><img src="https://www.google.com/s2/favicons?domain=wordpress.org&sz=128" alt="WordPress logo" loading="lazy" /></span>
        WordPress
      </div>
      <div class="lchip" role="listitem">
        <span class="lchip-ico"><img src="https://www.google.com/s2/favicons?domain=shopify.com&sz=128" alt="Shopify logo" loading="lazy" /></span>
        Shopify
      </div>
      <div class="lchip" role="listitem">
        <span class="lchip-ico"><img src="https://www.google.com/s2/favicons?domain=joomla.org&sz=128" alt="Joomla logo" loading="lazy" /></span>
        Joomla
      </div>
      <div class="lchip" role="listitem">
        <span class="lchip-ico"><img src="https://www.google.com/s2/favicons?domain=drupal.org&sz=128" alt="Drupal logo" loading="lazy" /></span>
        Drupal
      </div>
      <div class="lchip" role="listitem">
        <span class="lchip-ico"><img src="https://www.google.com/s2/favicons?domain=squarespace.com&sz=128" alt="Squarespace logo" loading="lazy" /></span>
        Squarespace
      </div>
      <div class="lchip" role="listitem">
        <span class="lchip-ico"><img src="https://www.google.com/s2/favicons?domain=wix.com&sz=128" alt="Wix logo" loading="lazy" /></span>
        Wix
      </div>
      <div class="lchip" role="listitem">
        <span class="lchip-ico"><img src="https://www.google.com/s2/favicons?domain=ghost.org&sz=128" alt="Ghost logo" loading="lazy" /></span>
        Ghost
      </div>
      <div class="lchip" role="listitem">
        <span class="lchip-ico"><img src="https://www.google.com/s2/favicons?domain=prestashop.com&sz=128" alt="PrestaShop logo" loading="lazy" /></span>
        PrestaShop
      </div>
      <div class="lchip" role="listitem">
        <span class="lchip-ico"><img src="https://www.google.com/s2/favicons?domain=bigcommerce.com&sz=128" alt="BigCommerce logo" loading="lazy" /></span>
        BigCommerce
      </div>
      <div class="lchip" role="listitem">
        <span class="lchip-ico"><img src="https://www.google.com/s2/favicons?domain=laravel.com&sz=128" alt="Laravel logo" loading="lazy" /></span>
        Laravel
      </div>
      <div class="lchip" role="listitem">
        <span class="lchip-ico"><img src="https://www.google.com/s2/favicons?domain=hubspot.com&sz=128" alt="HubSpot logo" loading="lazy" /></span>
        HubSpot
      </div>
      <div class="lchip" role="listitem">
        <span class="lchip-ico"><img src="https://www.google.com/s2/favicons?domain=opencart.com&sz=128" alt="OpenCart logo" loading="lazy" /></span>
        OpenCart
      </div>
      <div class="lchip" role="listitem">
        <span class="lchip-ico"><img src="https://www.google.com/s2/favicons?domain=weebly.com&sz=128" alt="Weebly logo" loading="lazy" /></span>
        Weebly
      </div>
      <div class="lchip" role="listitem">
        <span class="lchip-ico"><img src="https://www.google.com/s2/favicons?domain=blogger.com&sz=128" alt="Blogger logo" loading="lazy" /></span>
        Blogger
      </div>
      <div class="lchip" role="listitem">
        <span class="lchip-ico"><img src="https://www.google.com/s2/favicons?domain=tumblr.com&sz=128" alt="Tumblr logo" loading="lazy" /></span>
        Tumblr
      </div>
      <div class="lchip" role="listitem">
        <span class="lchip-ico"><img src="https://www.google.com/s2/favicons?domain=umbraco.com&sz=128" alt="Umbraco logo" loading="lazy" /></span>
        Umbraco
      </div>
      <div class="lchip" role="listitem">
        <span class="lchip-ico"><img src="https://www.google.com/s2/favicons?domain=codeigniter.com&sz=128" alt="CodeIgniter logo" loading="lazy" /></span>
        CodeIgniter
      </div>
      <div class="lchip" role="listitem">
        <span class="lchip-ico"><img src="https://www.google.com/s2/favicons?domain=zen-cart.com&sz=128" alt="Zen Cart logo" loading="lazy" /></span>
        Zen Cart
      </div>
      <div class="lchip" role="listitem">
        <span class="lchip-ico"><img src="https://www.google.com/s2/favicons?domain=vbulletin.com&sz=128" alt="vBulletin logo" loading="lazy" /></span>
        vBulletin
      </div>
      <div class="lchip" role="listitem">
        <span class="lchip-ico"><img src="https://www.google.com/s2/favicons?domain=oscommerce.com&sz=128" alt="osCommerce logo" loading="lazy" /></span>
        osCommerce
      </div>
      <div class="lchip" role="listitem">
        <span class="lchip-ico"><img src="https://www.google.com/s2/favicons?domain=jimdo.com&sz=128" alt="Jimdo logo" loading="lazy" /></span>
        Jimdo
      </div>
      <div class="lchip" role="listitem">
        <span class="lchip-ico"><img src="https://www.google.com/s2/favicons?domain=strikingly.com&sz=128" alt="Strikingly logo" loading="lazy" /></span>
        Strikingly
      </div>
      <div class="lchip" role="listitem">
        <span class="lchip-ico"><img src="https://www.google.com/s2/favicons?domain=react.dev&sz=128" alt="React logo" loading="lazy" /></span>
        React
      </div>
      <div class="lchip" role="listitem">
        <span class="lchip-ico"><img src="https://www.google.com/s2/favicons?domain=nextjs.org&sz=128" alt="Next.js logo" loading="lazy" /></span>
        Next.js
      </div>
      <div class="lchip" role="listitem">
        <span class="lchip-ico"><img src="https://www.google.com/s2/favicons?domain=vuejs.org&sz=128" alt="Vue.js logo" loading="lazy" /></span>
        Vue.js
      </div>
      <div class="lchip" role="listitem">
        <span class="lchip-ico"><img src="https://www.google.com/s2/favicons?domain=angular.io&sz=128" alt="Angular logo" loading="lazy" /></span>
        Angular
      </div>
      <div class="lchip" role="listitem">
        <span class="lchip-ico"><img src="https://www.google.com/s2/favicons?domain=webflow.com&sz=128" alt="Webflow logo" loading="lazy" /></span>
        Webflow
      </div>
      <div class="lchip" role="listitem">
        <span class="lchip-ico"><img src="https://www.google.com/s2/favicons?domain=magento.com&sz=128" alt="Magento logo" loading="lazy" /></span>
        Magento
      </div>
      <div class="lchip" role="listitem">
        <span class="lchip-ico"><img src="https://www.google.com/s2/favicons?domain=djangoproject.com&sz=128" alt="Django logo" loading="lazy" /></span>
        Django
      </div>
      <div class="lchip" role="listitem">
        <span class="lchip-ico"><img src="https://www.google.com/s2/favicons?domain=cloudflare.com&sz=128" alt="Cloudflare logo" loading="lazy" /></span>
        Cloudflare
      </div>

    </div>
  </div>
</section>

<!-- NUMBERS STRIP -->
<div class="nums-strip" aria-label="Key statistics">
  <div class="nums-in">
    <div class="num-item"><div class="num-val">400+</div><div class="num-lbl">Technologies detected</div></div>
    <div class="num-item"><div class="num-val">&lt;5s</div><div class="num-lbl">Average scan time</div></div>
    <div class="num-item"><div class="num-val">100%</div><div class="num-lbl">Free forever</div></div>
    <div class="num-item"><div class="num-val">0</div><div class="num-lbl">Registration required</div></div>
  </div>
</div>

<!-- HOW IT WORKS -->
<section class="how-sec" id="how-it-works" aria-label="How it works">
  <div class="how-in">
    <div class="center appear">
      <div class="sec-label">How it works</div>
      <h2 class="sec-h2">Three steps to reveal any tech stack</h2>
      <p class="sec-p" style="margin:0 auto">Fast, accurate detection using multi-signal fingerprinting — no guessing involved.</p>
    </div>
    <div class="how-grid">
      <div class="how-card appear">
        <div class="how-num" aria-hidden="true">1</div>
        <div class="how-ico" style="background:#eff6ff" aria-hidden="true">⬇️</div>
        <h3 class="how-t">Fetch the Page</h3>
        <p class="how-d">We download the full page HTML, HTTP response headers, cookies, and inline scripts from the live server in real time.</p>
      </div>
      <div class="how-card appear">
        <div class="how-num" aria-hidden="true">2</div>
        <div class="how-ico" style="background:#f0fdf4" aria-hidden="true">🔍</div>
        <h3 class="how-t">Match Signatures</h3>
        <p class="how-d">300+ fingerprints are tested: <code>meta</code> generators, unique paths like <code>/wp-content/</code>, and header patterns.</p>
      </div>
      <div class="how-card appear">
        <div class="how-num" aria-hidden="true">3</div>
        <div class="how-ico" style="background:#fef9c3" aria-hidden="true">📊</div>
        <h3 class="how-t">Score &amp; Report</h3>
        <p class="how-d">Every match is weighted for reliability. A confidence score is calculated and displayed with results grouped by category.</p>
      </div>
    </div>
  </div>
</section>

<!-- FAQ -->
<section class="faq-sec" id="faq" aria-label="Frequently asked questions">
  <div class="faq-in">
    <div class="faq-hdr appear">
      <div class="sec-label">FAQ</div>
      <h2 class="sec-h2">Frequently asked questions</h2>
      <p class="sec-p" style="margin:10px auto 0">Everything you need to know about CMS Detector.</p>
    </div>
    <div class="faq-list appear">

      <div class="faq-item">
        <button class="faq-btn" aria-expanded="false">
          <span class="faq-q">How do I detect what CMS a website is using?</span>
          <span class="faq-tog" aria-hidden="true">+</span>
        </button>
        <div class="faq-body" role="region">
          <div class="faq-body-in">Simply paste any URL into the scan bar and click <strong>Detect CMS</strong>. Our engine scans the site's HTML, headers, and scripts and shows you results in seconds — completely free, no account needed.</div>
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-btn" aria-expanded="false">
          <span class="faq-q">Is CMS Detector really free with no limits?</span>
          <span class="faq-tog" aria-hidden="true">+</span>
        </button>
        <div class="faq-body" role="region">
          <div class="faq-body-in">Yes, <strong>100% free</strong> with unlimited scans and zero registration. There are no rate limits or hidden plans for standard lookups.</div>
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-btn" aria-expanded="false">
          <span class="faq-q">How does the detection algorithm work?</span>
          <span class="faq-tog" aria-hidden="true">+</span>
        </button>
        <div class="faq-body" role="region">
          <div class="faq-body-in">We use multi-signal <strong>fingerprinting</strong> — checking <code>meta name="generator"</code> tags, HTTP headers, unique paths like <code>/wp-content/</code>, cookie names, and inline script patterns, each weighted to produce a confidence score.</div>
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-btn" aria-expanded="false">
          <span class="faq-q">Can it detect WordPress plugins and themes?</span>
          <span class="faq-tog" aria-hidden="true">+</span>
        </button>
        <div class="faq-body" role="region">
          <div class="faq-body-in">Yes. By inspecting asset directories and script handles we can identify the active WordPress <strong>theme</strong> and many installed plugins including Elementor, WooCommerce, Yoast SEO, and Divi.</div>
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-btn" aria-expanded="false">
          <span class="faq-q">What if a site hides its technology stack?</span>
          <span class="faq-tog" aria-hidden="true">+</span>
        </button>
        <div class="faq-body" role="region">
          <div class="faq-body-in">Some sites deliberately remove headers or obfuscate paths. In those cases detection accuracy is lower — we only show results we can confirm with high confidence. We never show false positives.</div>
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-btn" aria-expanded="false">
          <span class="faq-q">What types of technologies can CMS Detector identify?</span>
          <span class="faq-tog" aria-hidden="true">+</span>
        </button>
        <div class="faq-body" role="region">
          <div class="faq-body-in">CMS Detector identifies <strong>CMS platforms</strong> (WordPress, Drupal, Joomla), <strong>e-commerce systems</strong> (Shopify, Magento, WooCommerce), <strong>JavaScript frameworks</strong> (React, Vue, Angular, Next.js), <strong>web servers</strong> (Nginx, Apache, Cloudflare), and <strong>programming languages</strong> (PHP, Python, Ruby).</div>
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-btn" aria-expanded="false">
          <span class="faq-q">Is the scan safe and does it affect the target website?</span>
          <span class="faq-tog" aria-hidden="true">+</span>
        </button>
        <div class="faq-body" role="region">
          <div class="faq-body-in">Completely safe. We perform a <strong>passive read-only analysis</strong> — exactly like a normal browser visit. We never modify, inject, or stress-test the target website in any way.</div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- CTA -->
<section class="cta-sec" aria-label="Call to action">
  <div class="cta-box appear">
    <h2>Ready to detect any tech stack?</h2>
    <p>Free, instant, no account required.</p>
    <button class="btn-cta" onclick="document.getElementById('url-input').focus();scrollTo({top:0,behavior:'smooth'})">
      Start scanning free
      <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8h10M8.5 3.5 13 8l-4.5 4.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </button>
  </div>
</section>

<!-- FOOTER -->
<footer class="footer">
  <div class="footer-in">
    <div class="logo" aria-label="CMS Detector">
      <div class="logo-mark" aria-hidden="true">C</div>CMS Detector
    </div>
    <nav class="footer-links" aria-label="Footer navigation">
      <a href="#how-it-works">How it works</a>
      <a href="#technologies">Technologies</a>
      <a href="#faq">FAQ</a>
      <a href="mailto:hello@cms-detector.rf.gd">Contact</a>
    </nav>
    <p class="footer-copy">&copy; <?= date('Y') ?> CMS Detector. Free for everyone.</p>
  </div>
</footer>

<script>
(function(){
  'use strict';
  var urlInput=document.getElementById('url-input'),
      scanBtn=document.getElementById('scan-btn'),
      scanNote=document.getElementById('scan-note'),
      loaderW=document.getElementById('loader-wrap'),
      loaderTxt=document.getElementById('loader-text'),
      loaderUrl=document.getElementById('loader-url'),
      resultSec=document.getElementById('results-section'),
      resultGrid=document.getElementById('results-grid'),
      resultHost=document.getElementById('result-host'),
      noResults=document.getElementById('no-results'),
      copyBtn=document.getElementById('copy-btn'),
      rescanBtn=document.getElementById('rescan-btn');

  var CATS={
    cms:{label:'CMS',icon:'fas fa-layer-group',bg:'#eff6ff',color:'#2563eb'},
    languages:{label:'Programming Language',icon:'fas fa-code',bg:'#f0fdf4',color:'#16a34a'},
    frameworks:{label:'Framework / Library',icon:'fas fa-gears',bg:'#fef9c3',color:'#d97706'},
    plugins:{label:'Plugins & Services',icon:'fas fa-puzzle-piece',bg:'#fdf4ff',color:'#9333ea'},
    servers:{label:'Web Server',icon:'fas fa-server',bg:'#f0fdfa',color:'#0d9488'},
  };
  var STEPS=[
    {id:'lpill-0',msg:'Fetching page source…'},
    {id:'lpill-1',msg:'Scanning HTTP headers…'},
    {id:'lpill-2',msg:'Matching 300+ signatures…'},
    {id:'lpill-3',msg:'Calculating confidence scores…'},
  ];
  var timer=null,lastData=null,currentUrl='';

  function startScan(target){
    var url=(target||urlInput.value).trim();
    if(!url){note('Please enter a URL.',true);urlInput.focus();return;}
    if(!/^https?:\/\//i.test(url))url='https://'+url;
    try{new URL(url);}catch(e){note("That doesn't look like a valid URL — try: example.com",true);return;}
    currentUrl=url;
    if(target)urlInput.value=url.replace(/^https?:\/\//,'');
    setState('loading');
    doScan(url);
  }

  async function doScan(url){
    try{
      var r=await fetch('detect.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({url:url})});
      if(!r.ok)throw new Error('HTTP '+r.status);
      var j=await r.json();
      if(!j.success){setState('error',j.error||'Unknown error');return;}
      lastData=j;renderResults(j);setState('results');
    }catch(e){setState('error','Network error: '+e.message);}
  }

  function renderResults(json){
    var data=json.data||{};
    resultGrid.innerHTML='';
    resultHost.textContent=json.scanned||'';
    var total=0,delay=0;
    Object.entries(CATS).forEach(function(entry){
      var key=entry[0],meta=entry[1],items=data[key]||[];
      if(!items.length)return;
      total+=items.length;
      var card=document.createElement('div');
      card.className='rcard';
      card.setAttribute('role','listitem');
      card.style.animationDelay=delay+'ms';
      delay+=80;
      var iconHtml='<i class="'+meta.icon+'" aria-hidden="true"></i>';
      card.innerHTML='<div class="rcard-head"><div class="rcard-ico" style="background:'+meta.bg+';color:'+meta.color+'">'+iconHtml+'</div><span class="rcard-lbl">'+meta.label+'</span><span class="rcard-bdg">'+items.length+'</span></div><div class="tech-rows">'+items.map(function(t){
        var techDomain=t.domain||t.name.toLowerCase().replace(/\s+/g,'');
        var faviconUrl='https://www.google.com/s2/favicons?domain='+encodeURIComponent(techDomain)+'&sz=48';
        var techIcon='<img src="'+faviconUrl+'" alt="" loading="lazy" onerror="this.style.display=\'none\'" />';
        if(!t.domain){techIcon='<i class="fas fa-cube" style="color:'+meta.color+'" aria-hidden="true"></i>';}
        return '<div class="tech-row"><span class="tech-ico">'+techIcon+'</span><span class="tech-name">'+esc(t.name)+'</span><div class="tech-sc"><span class="tech-pct">'+Math.round(t.score)+'%</span><div class="pbar" aria-label="Confidence '+Math.round(t.score)+'%"><div class="pfill" data-w="'+Math.round(t.score)+'%" style="background:'+meta.color+';width:0"></div></div></div></div>';
      }).join('')+'</div>';
      resultGrid.appendChild(card);
    });
    noResults.hidden=total>0;
    requestAnimationFrame(function(){
      document.querySelectorAll('.pfill').forEach(function(el){setTimeout(function(){el.style.width=el.dataset.w;},150);});
    });
  }

  function setState(state,errMsg){
    clearInterval(timer);
    STEPS.forEach(function(s){var el=document.getElementById(s.id);if(el)el.classList.remove('on');});
    loaderW.hidden=true;resultSec.hidden=true;scanBtn.disabled=false;
    note('By using this tool, you agree to our <a href="#">terms of service</a>',false);
    if(state==='loading'){
      loaderW.hidden=false;loaderUrl.textContent=currentUrl;scanBtn.disabled=true;
      var step=0;setStep(step);
      timer=setInterval(function(){step=(step+1)%STEPS.length;setStep(step);},1500);
      loaderW.scrollIntoView({behavior:'smooth',block:'center'});
    }else if(state==='results'){
      resultSec.hidden=false;
      setTimeout(function(){resultSec.scrollIntoView({behavior:'smooth',block:'start'});},80);
    }else if(state==='error'){
      note(errMsg||'Something went wrong.',true);
      urlInput.scrollIntoView({behavior:'smooth',block:'center'});urlInput.focus();
    }
  }

  function setStep(idx){
    STEPS.forEach(function(s,i){var el=document.getElementById(s.id);if(el)el.classList.toggle('on',i===idx);});
    if(STEPS[idx])loaderTxt.textContent=STEPS[idx].msg;
  }

  function note(html,isError){scanNote.innerHTML=html;scanNote.className='scan-note'+(isError?' err':'');}

  copyBtn.addEventListener('click',function(){
    if(!lastData)return;
    var lines=['CMS Detector — '+(lastData.final_url||currentUrl),''];
    Object.entries(lastData.data||{}).forEach(function(e){
      if(e[1].length){lines.push('['+e[0].toUpperCase()+']');e[1].forEach(function(t){lines.push('  \u2022 '+t.name+' ('+t.score+'%)');});lines.push('');}
    });
    navigator.clipboard.writeText(lines.join('\n')).then(function(){
      copyBtn.innerHTML='<svg viewBox="0 0 16 16" fill="none" width="13" height="13"><path d="M3 8l4 4 6-7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg> Copied!';
      setTimeout(function(){copyBtn.innerHTML='<svg viewBox="0 0 16 16" fill="none" width="13" height="13"><rect x="5" y="5" width="9" height="9" rx="1.5" stroke="currentColor" stroke-width="1.4"/><path d="M11 5V3.5A1.5 1.5 0 0 0 9.5 2h-6A1.5 1.5 0 0 0 2 3.5v6A1.5 1.5 0 0 0 3.5 11H5" stroke="currentColor" stroke-width="1.4"/></svg> Copy';},2200);
    });
  });

  rescanBtn.addEventListener('click',function(){resultSec.hidden=true;scrollTo({top:0,behavior:'smooth'});setTimeout(function(){urlInput.focus();},400);});
  urlInput.addEventListener('keydown',function(e){if(e.key==='Enter')startScan();});
  scanBtn.addEventListener('click',function(){startScan();});

  document.querySelectorAll('.btn-rescan-history').forEach(function(btn){
    btn.addEventListener('click',function(e){e.stopPropagation();startScan(btn.dataset.url);});
  });
  document.querySelectorAll('.history-item').forEach(function(item){
    item.addEventListener('click',function(){startScan(item.dataset.url);});
  });

  function esc(s){return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');}
})();

/* FAQ accordion */
(function(){
  document.querySelectorAll('.faq-item').forEach(function(item){
    var btn=item.querySelector('.faq-btn'),body=item.querySelector('.faq-body');
    btn.addEventListener('click',function(){
      var open=item.classList.contains('open');
      document.querySelectorAll('.faq-item.open').forEach(function(o){
        o.classList.remove('open');
        o.querySelector('.faq-body').style.maxHeight='0';
        o.querySelector('.faq-btn').setAttribute('aria-expanded','false');
      });
      if(!open){
        item.classList.add('open');
        body.style.maxHeight=body.scrollHeight+'px';
        btn.setAttribute('aria-expanded','true');
      }
    });
  });
})();

/* Scroll reveal */
(function(){
  if(!('IntersectionObserver' in window)){
    document.querySelectorAll('.appear').forEach(function(el){el.classList.add('in');});
    return;
  }
  var io=new IntersectionObserver(function(entries){
    entries.forEach(function(e){
      if(e.isIntersecting){e.target.classList.add('in');io.unobserve(e.target);}
    });
  },{threshold:0.1});
  document.querySelectorAll('.appear').forEach(function(el){io.observe(el);});
})();
</script>
</body>
</html>