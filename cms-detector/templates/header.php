<?php
/**
 * templates/header.php
 * Outputs the <head> block and opening page structure.
 * Variables expected: $page_title (string)
 */
$site_url    = 'https://cms-detector.rf.gd';
$og_image    = $site_url . '/assets/uploads/CMS-Detector.png';
$favicon_url = $site_url . '/assets/uploads/CMS-Detector-Favicon.png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <!-- ── Primary SEO ────────────────────────────────────────────────────── -->
  <title>CMS Detector | Identify What CMS a Website Is Using Instantly</title>
  <meta name="description" content="Detect the CMS, themes, and plugins of any website instantly. Identify WordPress, Shopify, and 100+ tech stacks for free with our accurate lookup tool. Try now!" />
  <meta name="keywords" content="CMS detector, what CMS, website technology lookup, WordPress theme detector, Shopify detector, builtwith alternative, what CMS is this site using, web stack checker" />
  <link rel="canonical" href="<?= $site_url ?>/" />
  <link rel="sitemap" type="application/xml" title="Sitemap" href="<?= $site_url ?>/sitemap.xml" />
  <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1" />

  <!-- ── Favicon ────────────────────────────────────────────────────────── -->
  <link rel="icon" type="image/png" href="<?= $favicon_url ?>" />
  <link rel="shortcut icon" href="<?= $favicon_url ?>" />
  <link rel="apple-touch-icon" href="<?= $og_image ?>" />

  <!-- ── Author & Theme ────────────────────────────────────────────────── -->
  <meta name="author" content="CMS Detector" />
  <meta name="theme-color" content="#63b3ed" />
  <meta name="msapplication-TileColor" content="#0a0b0f" />
  <meta name="application-name" content="CMS Detector" />

  <!-- ── Google Verification ────────────────────────────────────────────── -->
  <meta name="google-site-verification" content="bH04NM4cw7LASX9CseL1EdjRaHDElALJVXHvJgg0GPI" />

  <!-- ── Open Graph (Facebook & LinkedIn) ──────────────────────────────── -->
  <meta property="og:type"        content="website" />
  <meta property="og:site_name"   content="CMS Detector" />
  <meta property="og:url"         content="<?= $site_url ?>/" />
  <meta property="og:title"       content="CMS Detector | Identify What CMS a Website Is Using Instantly" />
  <meta property="og:description" content="Detect the CMS, themes, and plugins of any website instantly. Identify WordPress, Shopify, and 100+ tech stacks for free with our accurate lookup tool. Try now!" />
  <meta property="og:image"       content="<?= $og_image ?>" />
  <meta property="og:image:width"  content="1200" />
  <meta property="og:image:height" content="630" />
  <meta property="og:image:alt"   content="CMS Detector – Website Technology Lookup Tool" />
  <meta property="og:locale"      content="en_US" />

  <!-- ── Twitter / X Card ──────────────────────────────────────────────── -->
  <meta name="twitter:card"        content="summary_large_image" />
  <meta name="twitter:title"       content="CMS Detector | Identify What CMS a Website Is Using Instantly" />
  <meta name="twitter:description" content="Detect the CMS, themes, and plugins of any website instantly. Identify WordPress, Shopify, and 100+ tech stacks for free with our accurate lookup tool. Try now!" />
  <meta name="twitter:image"       content="<?= $og_image ?>" />
  <meta name="twitter:image:alt"   content="CMS Detector – Website Technology Lookup Tool" />

  <!-- ── JSON-LD: SoftwareApplication ──────────────────────────────────── -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "SoftwareApplication",
    "name": "CMS Detector",
    "url": "<?= $site_url ?>/",
    "logo": "<?= $og_image ?>",
    "operatingSystem": "Web",
    "applicationCategory": "BusinessApplication",
    "description": "Free website technology lookup tool to instantly identify Content Management Systems, themes, plugins, frameworks, and programming languages used by any website.",
    "featureList": [
      "CMS Detection (WordPress, Shopify, Joomla, Drupal, Wix, Squarespace)",
      "Framework Detection (Laravel, Django, React, Vue.js, Next.js)",
      "Plugin and Theme Detection",
      "Programming Language Detection",
      "Web Server Identification",
      "Unlimited Free Scans – No Registration Required"
    ],
    "offers": {
      "@type": "Offer",
      "price": "0",
      "priceCurrency": "USD"
    },
    "aggregateRating": {
      "@type": "AggregateRating",
      "ratingValue": "4.9",
      "ratingCount": "120"
    },
    "publisher": {
      "@type": "Organization",
      "name": "CMS Detector",
      "url": "<?= $site_url ?>/"
    }
  }
  </script>

  <!-- ── JSON-LD: FAQPage ───────────────────────────────────────────────── -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [
      {
        "@type": "Question",
        "name": "How do I find out what CMS a website is using for free?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "To identify a website's CMS, simply enter the URL into our search bar and click 'Analyze.' Our tool instantly scans the site's HTML, meta tags, and headers to reveal if it is built on WordPress, Shopify, Wix, or other major platforms."
        }
      },
      {
        "@type": "Question",
        "name": "Is this CMS lookup tool 100% free to use?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "Yes, our CMS detector is completely free and supports unlimited scans with no registration required. You can perform deep technology lookups and stack analysis on any domain at zero cost."
        }
      },
      {
        "@type": "Question",
        "name": "How does the CMS detection algorithm work?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "The tool uses advanced fingerprinting to analyze technical signals such as meta name='generator' tags, HTTP headers, and unique directory paths like /wp-content/. It checks every page for thousands of specific artifacts to provide a high-confidence detection result."
        }
      },
      {
        "@type": "Question",
        "name": "Can your tool detect WordPress themes and plugins?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "Yes, our scanner identifies the specific WordPress themes and plugins powering a site by inspecting asset signatures and directory patterns. It can also detect eCommerce apps and JavaScript frameworks like React and Next.js."
        }
      },
      {
        "@type": "Question",
        "name": "Which CMS platforms and web technologies can you identify?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "We can detect over 100+ platforms, including WordPress, Shopify, Joomla, Drupal, Squarespace, and Wix. Additionally, the tool identifies underlying technologies such as web servers, analytics trackers, and advertising pixels."
        }
      }
    ]
  }
  </script>

  <!-- ── JSON-LD: WebSite + SearchAction ───────────────────────────────── -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "WebSite",
    "name": "CMS Detector",
    "url": "<?= $site_url ?>/",
    "description": "Free website technology lookup tool to identify the CMS, themes, plugins, and full tech stack of any website instantly.",
    "potentialAction": {
      "@type": "SearchAction",
      "target": {
        "@type": "EntryPoint",
        "urlTemplate": "<?= $site_url ?>/?url={search_term_string}"
      },
      "query-input": "required name=search_term_string"
    }
  }
  </script>

  <!-- ── JSON-LD: Organization ─────────────────────────────────────────── -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "Organization",
    "name": "CMS Detector",
    "url": "<?= $site_url ?>/",
    "logo": {
      "@type": "ImageObject",
      "url": "<?= $og_image ?>",
      "width": 1200,
      "height": 630
    },
    "description": "Free website technology lookup tool to instantly identify CMS, themes, plugins, and full tech stacks.",
    "sameAs": []
  }
  </script>

  <!-- ── JSON-LD: BreadcrumbList ────────────────────────────────────────── -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [
      {
        "@type": "ListItem",
        "position": 1,
        "name": "Home",
        "item": "<?= $site_url ?>/"
      }
    ]
  }
  </script>

  <!-- Fonts: DM Sans + Space Mono -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet" />

  <style>
    /* ════════════════════════════════════════════════
       DESIGN TOKENS
    ════════════════════════════════════════════════ */
    :root {
      --bg:          #0a0b0f;
      --bg-card:     #111318;
      --bg-input:    #161820;
      --border:      rgba(255,255,255,.08);
      --border-glow: rgba(99,179,237,.35);

      --text-primary:   #f0f2f7;
      --text-secondary: #8b92a8;
      --text-muted:     #4a5068;

      --accent:      #63b3ed;
      --accent-2:    #81e6d9;
      --accent-hot:  #f687b3;

      --green:  #68d391;
      --yellow: #f6e05e;
      --orange: #fc8181;

      --radius-sm:  6px;
      --radius-md:  12px;
      --radius-lg:  20px;
      --radius-xl:  28px;

      --shadow-card: 0 4px 24px rgba(0,0,0,.4), 0 1px 0 rgba(255,255,255,.04) inset;
      --shadow-glow: 0 0 0 1px var(--border-glow), 0 0 32px rgba(99,179,237,.12);

      --font-ui:   'DM Sans', system-ui, sans-serif;
      --font-mono: 'Space Mono', monospace;

      --transition: .2s cubic-bezier(.4,0,.2,1);
    }

    /* ════════════════════════════════════════════════
       RESET & BASE
    ════════════════════════════════════════════════ */
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    html { scroll-behavior: smooth; -webkit-font-smoothing: antialiased; }

    body {
      font-family: var(--font-ui);
      background: var(--bg);
      color: var(--text-primary);
      min-height: 100vh;
      overflow-x: hidden;
    }

    a { color: var(--accent); text-decoration: none; }
    a:hover { text-decoration: underline; }

    /* ════════════════════════════════════════════════
       GRID BACKGROUND
    ════════════════════════════════════════════════ */
    body::before {
      content: '';
      position: fixed;
      inset: 0;
      background-image:
        linear-gradient(rgba(99,179,237,.04) 1px, transparent 1px),
        linear-gradient(90deg, rgba(99,179,237,.04) 1px, transparent 1px);
      background-size: 48px 48px;
      pointer-events: none;
      z-index: 0;
    }

    /* Radial glow behind hero */
    body::after {
      content: '';
      position: fixed;
      top: -20%;
      left: 50%;
      transform: translateX(-50%);
      width: 900px;
      height: 600px;
      background: radial-gradient(ellipse at center,
        rgba(99,179,237,.12) 0%,
        rgba(129,230,217,.06) 40%,
        transparent 70%);
      pointer-events: none;
      z-index: 0;
    }

    /* ════════════════════════════════════════════════
       LAYOUT
    ════════════════════════════════════════════════ */
    .site-wrapper {
      position: relative;
      z-index: 1;
      max-width: 860px;
      margin: 0 auto;
      padding: 0 24px 80px;
    }
  </style>
</head>
<body>
<div class="site-wrapper">
