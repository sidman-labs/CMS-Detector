<?php
/**
 * home.php
 * ─────────────────────────────────────────────────────────────────────────────
 * StackDetect — Redesigned Homepage
 * Modern, editorial-grade CMS detector landing page.
 * ─────────────────────────────────────────────────────────────────────────────
 */

// Uncomment when integrating with your app:
// require_once __DIR__ . '/config.php';
// require_once __DIR__ . '/includes/functions.php';

$page_title = 'StackDetect — Instantly Reveal Any Website\'s Tech Stack';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($page_title) ?></title>
  <meta name="description" content="Instantly detect any website's CMS, framework, plugins, and tech stack. Free, fast, no registration required." />

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,300&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet" />

  <style>
    /* ════════════════════════════════════════════════════
       TOKENS & RESET
    ════════════════════════════════════════════════════ */
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      /* Palette */
      --ink:        #0c0d10;
      --ink-2:      #16181f;
      --ink-3:      #1e2029;
      --border:     rgba(255,255,255,.07);
      --border-hi:  rgba(255,255,255,.13);
      --text-1:     #f0f1f5;
      --text-2:     #9ba0b0;
      --text-3:     #5a5f72;

      /* Brand colors */
      --lime:       #c8f135;
      --lime-dim:   rgba(200,241,53,.08);
      --lime-glow:  rgba(200,241,53,.18);
      --cyan:       #38d9c0;
      --cyan-dim:   rgba(56,217,192,.07);
      --violet:     #a78bfa;
      --orange:     #f97316;

      /* Radii */
      --r-sm: 8px;
      --r-md: 14px;
      --r-lg: 20px;
      --r-xl: 32px;

      /* Typography */
      --font-display: 'Syne', sans-serif;
      --font-body:    'DM Sans', sans-serif;
      --font-mono:    'DM Mono', monospace;

      /* Transitions */
      --ease: cubic-bezier(.25,.46,.45,.94);
    }

    html { scroll-behavior: smooth; }

    body {
      background: var(--ink);
      color: var(--text-1);
      font-family: var(--font-body);
      font-size: 16px;
      line-height: 1.6;
      -webkit-font-smoothing: antialiased;
      overflow-x: hidden;
    }

    a { color: inherit; text-decoration: none; }
    button { font-family: var(--font-body); cursor: pointer; }

    /* ════════════════════════════════════════════════════
       BACKGROUND CANVAS
    ════════════════════════════════════════════════════ */
    .bg-canvas {
      position: fixed;
      inset: 0;
      z-index: 0;
      pointer-events: none;
      overflow: hidden;
    }
    .bg-orb {
      position: absolute;
      border-radius: 50%;
      filter: blur(120px);
      opacity: .55;
    }
    .bg-orb--1 {
      width: 600px; height: 600px;
      top: -200px; left: -150px;
      background: radial-gradient(circle, rgba(200,241,53,.25) 0%, transparent 70%);
      animation: orb-drift-1 18s ease-in-out infinite alternate;
    }
    .bg-orb--2 {
      width: 500px; height: 500px;
      top: 30%; right: -180px;
      background: radial-gradient(circle, rgba(56,217,192,.18) 0%, transparent 70%);
      animation: orb-drift-2 22s ease-in-out infinite alternate;
    }
    .bg-orb--3 {
      width: 400px; height: 400px;
      bottom: 10%; left: 30%;
      background: radial-gradient(circle, rgba(167,139,250,.12) 0%, transparent 70%);
      animation: orb-drift-3 26s ease-in-out infinite alternate;
    }

    /* Subtle grid overlay */
    .bg-grid {
      position: absolute;
      inset: 0;
      background-image:
        linear-gradient(rgba(255,255,255,.025) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,.025) 1px, transparent 1px);
      background-size: 60px 60px;
    }

    @keyframes orb-drift-1 {
      from { transform: translate(0,0) scale(1); }
      to   { transform: translate(60px, 80px) scale(1.15); }
    }
    @keyframes orb-drift-2 {
      from { transform: translate(0,0) scale(1); }
      to   { transform: translate(-40px, 60px) scale(1.1); }
    }
    @keyframes orb-drift-3 {
      from { transform: translate(0,0) scale(1); }
      to   { transform: translate(30px, -50px) scale(1.08); }
    }

    /* ════════════════════════════════════════════════════
       LAYOUT WRAPPER
    ════════════════════════════════════════════════════ */
    .wrapper {
      position: relative;
      z-index: 1;
      max-width: 1100px;
      margin: 0 auto;
      padding: 0 24px;
    }

    /* ════════════════════════════════════════════════════
       NAVIGATION
    ════════════════════════════════════════════════════ */
    .nav {
      position: sticky;
      top: 0;
      z-index: 100;
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      background: rgba(12,13,16,.7);
      border-bottom: 1px solid var(--border);
    }
    .nav-inner {
      display: flex;
      align-items: center;
      justify-content: space-between;
      height: 64px;
      gap: 24px;
    }
    .nav-logo {
      display: flex;
      align-items: center;
      gap: 10px;
      font-family: var(--font-display);
      font-size: 1.15rem;
      font-weight: 800;
      letter-spacing: -.02em;
    }
    .nav-logo-icon {
      width: 32px; height: 32px;
      border-radius: 8px;
      background: var(--lime);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: .9rem;
    }
    .nav-links {
      display: flex;
      align-items: center;
      gap: 4px;
    }
    .nav-links a {
      font-size: .875rem;
      font-weight: 500;
      color: var(--text-2);
      padding: 6px 14px;
      border-radius: var(--r-sm);
      transition: color .2s, background .2s;
    }
    .nav-links a:hover { color: var(--text-1); background: rgba(255,255,255,.05); }
    .nav-cta {
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .btn-nav {
      font-size: .82rem;
      font-weight: 600;
      padding: 8px 18px;
      border-radius: var(--r-sm);
      border: 1px solid var(--border-hi);
      background: transparent;
      color: var(--text-1);
      transition: border-color .2s, background .2s;
    }
    .btn-nav:hover { background: rgba(255,255,255,.06); border-color: rgba(255,255,255,.2); }
    .btn-nav-primary {
      background: var(--lime);
      color: var(--ink);
      border-color: transparent;
    }
    .btn-nav-primary:hover { background: #d4f55a; transform: translateY(-1px); }

    @media (max-width: 640px) {
      .nav-links { display: none; }
      .btn-nav:not(.btn-nav-primary) { display: none; }
    }

    /* ════════════════════════════════════════════════════
       HERO
    ════════════════════════════════════════════════════ */
    .hero {
      padding: 96px 0 64px;
      text-align: center;
    }

    .hero-chip {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 6px 16px;
      border-radius: 999px;
      border: 1px solid rgba(200,241,53,.25);
      background: rgba(200,241,53,.06);
      font-size: .75rem;
      font-weight: 600;
      letter-spacing: .08em;
      text-transform: uppercase;
      color: var(--lime);
      margin-bottom: 32px;
      animation: fade-up .6s var(--ease) both;
    }
    .chip-dot {
      width: 6px; height: 6px;
      border-radius: 50%;
      background: var(--lime);
      animation: blink 2s ease infinite;
    }
    @keyframes blink { 0%,100%{opacity:1} 50%{opacity:.3} }

    .hero-headline {
      font-family: var(--font-display);
      font-size: clamp(2.8rem, 7vw, 5.2rem);
      font-weight: 800;
      line-height: 1.04;
      letter-spacing: -.04em;
      margin-bottom: 28px;
      animation: fade-up .65s .08s var(--ease) both;
    }
    .hero-headline em {
      font-style: normal;
      background: linear-gradient(110deg, var(--lime) 10%, var(--cyan) 55%, var(--violet) 90%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .hero-sub {
      font-size: 1.1rem;
      color: var(--text-2);
      line-height: 1.7;
      max-width: 540px;
      margin: 0 auto 44px;
      font-weight: 300;
      animation: fade-up .65s .14s var(--ease) both;
    }

    /* ── Scan bar ── */
    .scanbar-wrap {
      max-width: 660px;
      margin: 0 auto 20px;
      animation: fade-up .65s .2s var(--ease) both;
    }
    .scanbar {
      position: relative;
      display: flex;
      align-items: center;
      gap: 0;
      background: var(--ink-3);
      border: 1.5px solid var(--border-hi);
      border-radius: var(--r-xl);
      padding: 6px 6px 6px 22px;
      transition: border-color .25s, box-shadow .25s;
    }
    .scanbar:focus-within {
      border-color: var(--lime);
      box-shadow: 0 0 0 4px rgba(200,241,53,.1), 0 20px 60px rgba(0,0,0,.5);
    }
    .scanbar-prefix {
      font-family: var(--font-mono);
      font-size: .78rem;
      color: var(--text-3);
      white-space: nowrap;
      flex-shrink: 0;
      margin-right: 2px;
    }
    .scanbar-input {
      flex: 1;
      background: transparent;
      border: none;
      outline: none;
      font-family: var(--font-mono);
      font-size: .9rem;
      color: var(--text-1);
      min-width: 0;
      padding: 8px 0;
    }
    .scanbar-input::placeholder { color: var(--text-3); }
    .scanbar-btn {
      flex-shrink: 0;
      display: flex;
      align-items: center;
      gap: 8px;
      background: var(--lime);
      color: var(--ink);
      font-weight: 700;
      font-size: .9rem;
      border: none;
      border-radius: calc(var(--r-xl) - 8px);
      padding: 12px 26px;
      transition: background .2s, transform .2s, box-shadow .2s;
      white-space: nowrap;
    }
    .scanbar-btn:hover {
      background: #d4f55a;
      transform: translateY(-1px);
      box-shadow: 0 8px 24px rgba(200,241,53,.25);
    }
    .scanbar-btn:active { transform: none; }
    .scanbar-btn:disabled { opacity: .45; cursor: not-allowed; transform: none !important; }
    .scanbar-btn svg { width: 16px; height: 16px; transition: transform .2s; }
    .scanbar-btn:hover svg { transform: translateX(2px); }

    .scanbar-note {
      font-size: .78rem;
      color: var(--text-3);
      margin-top: 14px;
      animation: fade-up .65s .26s var(--ease) both;
    }
    .scanbar-note.error { color: #fb7185; }
    .scanbar-note span { color: var(--text-2); }

    /* ── Stat pills ── */
    .hero-stats {
      display: flex;
      justify-content: center;
      flex-wrap: wrap;
      gap: 12px;
      margin-top: 52px;
      animation: fade-up .65s .32s var(--ease) both;
    }
    .stat-pill {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 10px 20px;
      background: var(--ink-2);
      border: 1px solid var(--border);
      border-radius: 999px;
      font-size: .82rem;
    }
    .stat-pill-num {
      font-family: var(--font-display);
      font-size: 1rem;
      font-weight: 700;
      color: var(--lime);
    }
    .stat-pill-label { color: var(--text-2); }

    /* ════════════════════════════════════════════════════
       LOADER
    ════════════════════════════════════════════════════ */
    .loader-section {
      padding: 64px 0;
      text-align: center;
    }
    .loader-ring-wrap {
      position: relative;
      width: 80px;
      height: 80px;
      margin: 0 auto 28px;
    }
    .ring {
      position: absolute;
      inset: 0;
      border-radius: 50%;
      border: 2px solid transparent;
    }
    .ring--1 { border-top-color: var(--lime); animation: spin 1s linear infinite; }
    .ring--2 { inset: 10px; border-top-color: var(--cyan); animation: spin .7s linear infinite reverse; }
    .ring--3 { inset: 20px; border-top-color: var(--violet); animation: spin .5s linear infinite; }
    @keyframes spin { to { transform: rotate(360deg); } }
    .ring-core {
      position: absolute; inset: 0;
      display: flex; align-items: center; justify-content: center;
      font-size: 1.4rem;
    }

    .loader-label {
      font-family: var(--font-mono);
      font-size: .85rem;
      color: var(--text-2);
      margin-bottom: 8px;
    }
    .loader-url {
      font-family: var(--font-mono);
      font-size: .75rem;
      color: var(--text-3);
    }
    .loader-steps {
      display: flex;
      justify-content: center;
      gap: 8px;
      margin-top: 20px;
      flex-wrap: wrap;
    }
    .loader-step {
      font-size: .72rem;
      padding: 4px 12px;
      border-radius: 999px;
      border: 1px solid var(--border);
      color: var(--text-3);
      font-family: var(--font-mono);
      transition: all .3s;
    }
    .loader-step.active {
      border-color: var(--lime);
      color: var(--lime);
      background: var(--lime-dim);
    }

    /* ════════════════════════════════════════════════════
       RESULTS
    ════════════════════════════════════════════════════ */
    .results-section { padding-bottom: 48px; }

    .results-bar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 12px;
      padding: 16px 20px;
      background: var(--ink-2);
      border: 1px solid var(--border);
      border-radius: var(--r-md);
      margin-bottom: 24px;
    }
    .results-bar-left {
      display: flex;
      align-items: center;
      gap: 12px;
      font-size: .875rem;
    }
    .status-dot {
      width: 8px; height: 8px;
      border-radius: 50%;
      background: var(--lime);
      box-shadow: 0 0 10px var(--lime);
      animation: blink 2s infinite;
    }
    .results-bar-url {
      font-family: var(--font-mono);
      font-size: .8rem;
      color: var(--text-2);
    }
    .results-bar-right { display: flex; gap: 8px; }
    .btn-action {
      display: flex;
      align-items: center;
      gap: 6px;
      padding: 7px 14px;
      border-radius: var(--r-sm);
      border: 1px solid var(--border-hi);
      background: transparent;
      color: var(--text-2);
      font-size: .78rem;
      font-weight: 500;
      transition: all .2s;
    }
    .btn-action:hover { border-color: var(--lime); color: var(--lime); background: var(--lime-dim); }

    .results-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 14px;
    }

    /* Result card */
    .rcard {
      background: var(--ink-2);
      border: 1px solid var(--border);
      border-radius: var(--r-lg);
      padding: 24px;
      transition: border-color .2s, transform .2s, box-shadow .2s;
      animation: fade-up .4s var(--ease) backwards;
      position: relative;
      overflow: hidden;
    }
    .rcard::before {
      content: '';
      position: absolute;
      top: 0; left: 0; right: 0;
      height: 1px;
      background: linear-gradient(90deg, transparent, var(--card-color, var(--lime)), transparent);
      opacity: 0;
      transition: opacity .3s;
    }
    .rcard:hover { border-color: var(--border-hi); transform: translateY(-3px); box-shadow: 0 16px 48px rgba(0,0,0,.4); }
    .rcard:hover::before { opacity: 1; }

    .rcard-head {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 18px;
    }
    .rcard-icon-wrap {
      width: 38px; height: 38px;
      border-radius: 10px;
      display: flex; align-items: center; justify-content: center;
      font-size: 1.15rem;
      background: rgba(255,255,255,.05);
    }
    .rcard-cat {
      font-size: .68rem;
      font-weight: 700;
      letter-spacing: .1em;
      text-transform: uppercase;
      color: var(--text-3);
    }
    .rcard-count {
      margin-left: auto;
      font-family: var(--font-mono);
      font-size: .7rem;
      color: var(--text-3);
      background: rgba(255,255,255,.04);
      border: 1px solid var(--border);
      padding: 2px 8px;
      border-radius: 999px;
    }

    .tech-list { display: flex; flex-direction: column; gap: 8px; }
    .tech-row {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 10px 12px;
      border-radius: var(--r-sm);
      background: rgba(255,255,255,.025);
      border: 1px solid transparent;
      transition: border-color .2s, background .2s;
    }
    .tech-row:hover { background: rgba(255,255,255,.045); border-color: var(--border); }
    .tech-emoji { font-size: 1.1rem; line-height: 1; flex-shrink: 0; }
    .tech-name {
      flex: 1;
      font-size: .85rem;
      font-weight: 600;
      color: var(--text-1);
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      min-width: 0;
    }
    .tech-conf {
      flex-shrink: 0;
      display: flex;
      flex-direction: column;
      align-items: flex-end;
      gap: 4px;
    }
    .tech-conf-num {
      font-family: var(--font-mono);
      font-size: .68rem;
      color: var(--text-3);
    }
    .conf-bar {
      width: 52px;
      height: 3px;
      border-radius: 2px;
      background: rgba(255,255,255,.06);
      overflow: hidden;
    }
    .conf-bar-fill {
      height: 100%;
      border-radius: 2px;
      width: 0;
      transition: width 1.2s cubic-bezier(.34,1.56,.64,1);
    }

    /* No results */
    .no-results {
      text-align: center;
      padding: 56px 24px;
      border: 1px dashed var(--border-hi);
      border-radius: var(--r-lg);
    }
    .no-results-icon { font-size: 3rem; margin-bottom: 16px; }
    .no-results h3 { font-size: 1.1rem; font-weight: 600; margin-bottom: 8px; }
    .no-results p { font-size: .875rem; color: var(--text-2); line-height: 1.65; }

    /* ════════════════════════════════════════════════════
       TRUST STRIP
    ════════════════════════════════════════════════════ */
    .trust-strip {
      border-top: 1px solid var(--border);
      border-bottom: 1px solid var(--border);
      padding: 32px 0;
      overflow: hidden;
    }
    .trust-label {
      text-align: center;
      font-size: .72rem;
      letter-spacing: .1em;
      text-transform: uppercase;
      color: var(--text-3);
      margin-bottom: 24px;
    }
    .trust-logos {
      display: flex;
      justify-content: center;
      flex-wrap: wrap;
      gap: 0;
    }
    .trust-tech {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 8px 24px;
      border-right: 1px solid var(--border);
      font-size: .85rem;
      font-weight: 500;
      color: var(--text-2);
      transition: color .2s;
    }
    .trust-tech:last-child { border-right: none; }
    .trust-tech:hover { color: var(--text-1); }
    .trust-tech-icon { font-size: 1.1rem; }

    /* ════════════════════════════════════════════════════
       HOW IT WORKS
    ════════════════════════════════════════════════════ */
    .how-section {
      padding: 96px 0;
    }
    .section-eyebrow {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-family: var(--font-mono);
      font-size: .72rem;
      letter-spacing: .12em;
      text-transform: uppercase;
      color: var(--cyan);
      background: var(--cyan-dim);
      border: 1px solid rgba(56,217,192,.18);
      padding: 5px 14px;
      border-radius: 999px;
      margin-bottom: 20px;
    }
    .section-title {
      font-family: var(--font-display);
      font-size: clamp(1.8rem, 4vw, 2.8rem);
      font-weight: 800;
      letter-spacing: -.035em;
      line-height: 1.1;
      margin-bottom: 14px;
    }
    .section-sub {
      font-size: .95rem;
      color: var(--text-2);
      line-height: 1.7;
      max-width: 480px;
      font-weight: 300;
    }
    .section-header { margin-bottom: 56px; }

    .steps-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 2px;
      background: var(--border);
      border-radius: var(--r-lg);
      overflow: hidden;
    }
    @media (max-width: 640px) {
      .steps-grid { grid-template-columns: 1fr; }
    }
    .step-card {
      background: var(--ink-2);
      padding: 36px 30px;
      position: relative;
      transition: background .2s;
    }
    .step-card:hover { background: var(--ink-3); }
    .step-num {
      font-family: var(--font-display);
      font-size: 3rem;
      font-weight: 800;
      color: rgba(255,255,255,.04);
      line-height: 1;
      margin-bottom: 20px;
      letter-spacing: -.05em;
    }
    .step-icon {
      width: 44px; height: 44px;
      border-radius: 12px;
      display: flex; align-items: center; justify-content: center;
      font-size: 1.3rem;
      margin-bottom: 16px;
    }
    .step-icon--1 { background: rgba(200,241,53,.1); }
    .step-icon--2 { background: rgba(56,217,192,.1); }
    .step-icon--3 { background: rgba(167,139,250,.1); }
    .step-title {
      font-family: var(--font-display);
      font-size: 1.1rem;
      font-weight: 700;
      margin-bottom: 10px;
      letter-spacing: -.01em;
    }
    .step-desc { font-size: .85rem; color: var(--text-2); line-height: 1.65; }

    /* ════════════════════════════════════════════════════
       CAPABILITIES
    ════════════════════════════════════════════════════ */
    .caps-section { padding-bottom: 96px; }
    .caps-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 14px;
    }
    @media (max-width: 600px) { .caps-grid { grid-template-columns: 1fr; } }

    .cap-card {
      position: relative;
      padding: 32px 28px;
      border-radius: var(--r-lg);
      border: 1px solid var(--border);
      background: var(--ink-2);
      overflow: hidden;
      transition: border-color .25s, transform .25s;
    }
    .cap-card:hover { border-color: var(--border-hi); transform: translateY(-2px); }
    .cap-card-glow {
      position: absolute;
      inset: 0;
      opacity: 0;
      transition: opacity .3s;
      pointer-events: none;
    }
    .cap-card:hover .cap-card-glow { opacity: 1; }

    .cap-accent {
      width: 40px; height: 40px;
      border-radius: 10px;
      display: flex; align-items: center; justify-content: center;
      font-size: 1.25rem;
      margin-bottom: 18px;
    }
    .cap-num {
      position: absolute;
      top: 24px; right: 24px;
      font-family: var(--font-mono);
      font-size: .65rem;
      color: var(--text-3);
      letter-spacing: .08em;
    }
    .cap-title {
      font-family: var(--font-display);
      font-size: 1.05rem;
      font-weight: 700;
      margin-bottom: 10px;
      letter-spacing: -.01em;
    }
    .cap-desc {
      font-size: .82rem;
      color: var(--text-2);
      line-height: 1.7;
      margin-bottom: 20px;
    }
    .cap-tags {
      display: flex;
      flex-wrap: wrap;
      gap: 6px;
    }
    .cap-tag {
      font-size: .7rem;
      font-weight: 600;
      padding: 3px 10px;
      border-radius: 999px;
      border: 1px solid;
    }

    /* ════════════════════════════════════════════════════
       FAQ
    ════════════════════════════════════════════════════ */
    .faq-section { padding-bottom: 96px; }
    .faq-cols {
      display: grid;
      grid-template-columns: 280px 1fr;
      gap: 48px;
    }
    @media (max-width: 720px) { .faq-cols { grid-template-columns: 1fr; } }
    .faq-sidebar { padding-top: 4px; }
    .faq-sidebar-desc {
      font-size: .875rem;
      color: var(--text-2);
      line-height: 1.7;
      margin-top: 16px;
      margin-bottom: 24px;
    }
    .btn-contact {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 10px 20px;
      border: 1px solid var(--border-hi);
      border-radius: var(--r-sm);
      font-size: .82rem;
      font-weight: 600;
      color: var(--text-1);
      background: transparent;
      transition: all .2s;
    }
    .btn-contact:hover { border-color: var(--lime); color: var(--lime); background: var(--lime-dim); }

    .faq-list { display: flex; flex-direction: column; gap: 8px; }
    .faq-item {
      border: 1px solid var(--border);
      border-radius: var(--r-md);
      background: var(--ink-2);
      overflow: hidden;
      transition: border-color .2s;
    }
    .faq-item:hover { border-color: var(--border-hi); }
    .faq-item.open { border-color: rgba(200,241,53,.25); }

    .faq-btn {
      width: 100%;
      display: flex;
      align-items: center;
      gap: 14px;
      padding: 18px 20px;
      background: transparent;
      border: none;
      text-align: left;
      color: var(--text-1);
      font-size: .875rem;
      font-weight: 600;
      transition: background .15s;
    }
    .faq-btn:hover { background: rgba(255,255,255,.02); }
    .faq-q { flex: 1; line-height: 1.45; }
    .faq-arr {
      flex-shrink: 0;
      width: 22px; height: 22px;
      border-radius: 50%;
      border: 1px solid var(--border-hi);
      display: flex; align-items: center; justify-content: center;
      transition: transform .3s, border-color .2s, background .2s;
      font-size: .75rem;
      color: var(--text-3);
    }
    .faq-item.open .faq-arr { transform: rotate(45deg); border-color: var(--lime); background: var(--lime-dim); color: var(--lime); }

    .faq-body {
      max-height: 0;
      overflow: hidden;
      transition: max-height .35s cubic-bezier(.4,0,.2,1);
    }
    .faq-body-inner {
      padding: 0 20px 20px;
      font-size: .85rem;
      color: var(--text-2);
      line-height: 1.75;
      border-top: 1px solid var(--border);
      padding-top: 16px;
    }
    .faq-body-inner code {
      font-family: var(--font-mono);
      font-size: .8em;
      color: var(--lime);
      background: var(--lime-dim);
      padding: 2px 6px;
      border-radius: 4px;
    }
    .faq-body-inner strong { color: var(--text-1); }

    /* ════════════════════════════════════════════════════
       CTA BAND
    ════════════════════════════════════════════════════ */
    .cta-band {
      margin-bottom: 96px;
      border-radius: var(--r-xl);
      background: linear-gradient(135deg, var(--ink-3) 0%, #1a1c24 100%);
      border: 1px solid var(--border-hi);
      padding: 56px 40px;
      text-align: center;
      position: relative;
      overflow: hidden;
    }
    .cta-band::before {
      content: '';
      position: absolute;
      top: -50%;
      left: 50%;
      transform: translateX(-50%);
      width: 600px;
      height: 300px;
      background: radial-gradient(ellipse, rgba(200,241,53,.08) 0%, transparent 70%);
      pointer-events: none;
    }
    .cta-band-title {
      font-family: var(--font-display);
      font-size: clamp(1.8rem, 4vw, 2.8rem);
      font-weight: 800;
      letter-spacing: -.035em;
      margin-bottom: 14px;
      line-height: 1.1;
    }
    .cta-band-sub {
      font-size: .95rem;
      color: var(--text-2);
      margin-bottom: 36px;
      font-weight: 300;
    }
    .btn-cta-primary {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      padding: 16px 36px;
      border-radius: var(--r-xl);
      background: var(--lime);
      color: var(--ink);
      font-weight: 700;
      font-size: 1rem;
      border: none;
      transition: background .2s, transform .2s, box-shadow .2s;
    }
    .btn-cta-primary:hover {
      background: #d4f55a;
      transform: translateY(-2px);
      box-shadow: 0 16px 48px rgba(200,241,53,.3);
    }
    .btn-cta-primary svg { width: 18px; height: 18px; }

    /* ════════════════════════════════════════════════════
       FOOTER
    ════════════════════════════════════════════════════ */
    .footer {
      border-top: 1px solid var(--border);
      padding: 40px 0;
    }
    .footer-inner {
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 16px;
    }
    .footer-logo {
      display: flex;
      align-items: center;
      gap: 8px;
      font-family: var(--font-display);
      font-size: 1rem;
      font-weight: 800;
    }
    .footer-logo-dot {
      width: 8px; height: 8px;
      border-radius: 50%;
      background: var(--lime);
    }
    .footer-copy {
      font-size: .78rem;
      color: var(--text-3);
    }
    .footer-links {
      display: flex;
      gap: 20px;
      font-size: .78rem;
      color: var(--text-2);
    }
    .footer-links a:hover { color: var(--text-1); }

    /* ════════════════════════════════════════════════════
       ANIMATIONS
    ════════════════════════════════════════════════════ */
    @keyframes fade-up {
      from { opacity: 0; transform: translateY(20px); }
      to   { opacity: 1; transform: translateY(0); }
    }
    .reveal {
      opacity: 0;
      transform: translateY(24px);
      transition: opacity .6s var(--ease), transform .6s var(--ease);
    }
    .reveal.is-visible { opacity: 1; transform: translateY(0); }

    /* ════════════════════════════════════════════════════
       SCROLLBAR
    ════════════════════════════════════════════════════ */
    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-track { background: var(--ink); }
    ::-webkit-scrollbar-thumb { background: var(--ink-3); border-radius: 3px; }
    ::-webkit-scrollbar-thumb:hover { background: var(--border-hi); }

    /* ════════════════════════════════════════════════════
       HISTORY SECTION
    ════════════════════════════════════════════════════ */
    .history-section { padding-bottom: 64px; }
    .history-grid {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }
    .h-item {
      display: flex;
      align-items: center;
      gap: 14px;
      padding: 14px 18px;
      background: var(--ink-2);
      border: 1px solid var(--border);
      border-radius: var(--r-md);
      cursor: pointer;
      transition: border-color .2s, background .2s;
    }
    .h-item:hover { border-color: var(--border-hi); background: var(--ink-3); }
    .h-fav {
      width: 28px; height: 28px;
      flex-shrink: 0;
      display: flex; align-items: center; justify-content: center;
      font-size: .95rem;
    }
    .h-fav img { width: 20px; height: 20px; border-radius: 4px; }
    .h-host {
      font-size: .875rem;
      font-weight: 600;
      color: var(--text-1);
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      flex: 1;
      min-width: 0;
    }
    .h-tags {
      display: flex; flex-wrap: wrap; gap: 4px;
      flex-shrink: 0;
    }
    .h-tag {
      font-size: .68rem;
      padding: 2px 9px;
      border-radius: 999px;
      background: rgba(200,241,53,.06);
      border: 1px solid rgba(200,241,53,.18);
      color: var(--lime);
      white-space: nowrap;
    }
    .h-time {
      font-size: .7rem;
      color: var(--text-3);
      font-family: var(--font-mono);
      flex-shrink: 0;
    }
    @media (max-width: 480px) {
      .h-tags, .h-time { display: none; }
    }
  </style>
</head>
<body>

<!-- Background canvas -->
<div class="bg-canvas" aria-hidden="true">
  <div class="bg-orb bg-orb--1"></div>
  <div class="bg-orb bg-orb--2"></div>
  <div class="bg-orb bg-orb--3"></div>
  <div class="bg-grid"></div>
</div>

<!-- ═══════════════════════════════════════════════════════
     NAV
═══════════════════════════════════════════════════════ -->
<nav class="nav">
  <div class="wrapper nav-inner">
    <a href="/" class="nav-logo">
      <div class="nav-logo-icon">⬡</div>
      StackDetect
    </a>

    <div class="nav-links">
      <a href="#how-it-works">How it works</a>
      <a href="#capabilities">Coverage</a>
      <a href="#faq">FAQ</a>
    </div>

    <div class="nav-cta">
      <button class="btn-nav" onclick="document.getElementById('url-input').focus(); window.scrollTo({top:0, behavior:'smooth'})">Try for Free</button>
    </div>
  </div>
</nav>

<!-- ═══════════════════════════════════════════════════════
     HERO
═══════════════════════════════════════════════════════ -->
<header class="hero">
  <div class="wrapper">
    <div class="hero-chip">
      <div class="chip-dot"></div>
      Free · No account needed · Instant results
    </div>

    <h1 class="hero-headline">
      Reveal Any Website's<br>
      <em>Tech Stack</em> in Seconds
    </h1>

    <p class="hero-sub">
      Detect CMS, frameworks, plugins, servers and more — from a single URL.
      Powered by 300+ technology fingerprints.
    </p>

    <!-- Scan bar -->
    <div class="scanbar-wrap">
      <div class="scanbar" id="scanbar">
        <span class="scanbar-prefix">https://</span>
        <input
          type="text"
          id="url-input"
          class="scanbar-input"
          placeholder="example.com"
          autocomplete="off"
          spellcheck="false"
          autofocus
        />
        <button class="scanbar-btn" id="scan-btn" type="button">
          Analyze
          <svg viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M3.33 8h9.34M8 3.33 12.67 8 8 12.67" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </button>
      </div>
      <p class="scanbar-note" id="scan-note">
        Enter any website URL — <span>results appear in under 5 seconds</span>
      </p>
    </div>

    <!-- Stats -->
    <div class="hero-stats">
      <div class="stat-pill">
        <span class="stat-pill-num">300+</span>
        <span class="stat-pill-label">Tech signatures</span>
      </div>
      <div class="stat-pill">
        <span class="stat-pill-num">100%</span>
        <span class="stat-pill-label">Free forever</span>
      </div>
      <div class="stat-pill">
        <span class="stat-pill-num">&lt;5s</span>
        <span class="stat-pill-label">Scan time</span>
      </div>
      <div class="stat-pill">
        <span class="stat-pill-num">0</span>
        <span class="stat-pill-label">Registration needed</span>
      </div>
    </div>
  </div>
</header>

<!-- ═══════════════════════════════════════════════════════
     LOADER
═══════════════════════════════════════════════════════ -->
<div class="loader-section" id="loader-wrap" hidden>
  <div class="wrapper">
    <div class="loader-ring-wrap">
      <div class="ring ring--1"></div>
      <div class="ring ring--2"></div>
      <div class="ring ring--3"></div>
      <div class="ring-core">⬡</div>
    </div>
    <p class="loader-label" id="loader-text">Initializing scan…</p>
    <div class="loader-url" id="loader-url"></div>
    <div class="loader-steps">
      <span class="loader-step" id="lstep-0">Fetching page</span>
      <span class="loader-step" id="lstep-1">Reading headers</span>
      <span class="loader-step" id="lstep-2">Matching signatures</span>
      <span class="loader-step" id="lstep-3">Scoring results</span>
    </div>
  </div>
</div>

<!-- ═══════════════════════════════════════════════════════
     RESULTS
═══════════════════════════════════════════════════════ -->
<section class="results-section" id="results-section" hidden>
  <div class="wrapper">
    <div class="results-bar">
      <div class="results-bar-left">
        <div class="status-dot"></div>
        <span>Scan complete —</span>
        <code class="results-bar-url" id="result-host"></code>
      </div>
      <div class="results-bar-right">
        <button class="btn-action" id="copy-btn">
          <svg width="13" height="13" viewBox="0 0 16 16" fill="none"><rect x="5" y="5" width="9" height="9" rx="2" stroke="currentColor" stroke-width="1.5"/><path d="M11 5V4a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h1" stroke="currentColor" stroke-width="1.5"/></svg>
          Copy report
        </button>
        <button class="btn-action" id="rescan-btn">
          <svg width="13" height="13" viewBox="0 0 16 16" fill="none"><path d="M14 8A6 6 0 1 1 2.34 5M2 2v3h3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
          New scan
        </button>
      </div>
    </div>

    <div class="results-grid" id="results-grid"></div>

    <div class="no-results" id="no-results" hidden>
      <div class="no-results-icon">🔭</div>
      <h3>Nothing detected</h3>
      <p>We couldn't identify known technologies on this site.<br>It may use custom or obfuscated code.</p>
    </div>
  </div>
</section>

<!-- ═══════════════════════════════════════════════════════
     TRUST STRIP
═══════════════════════════════════════════════════════ -->
<div class="trust-strip">
  <div class="wrapper">
    <p class="trust-label">Detects technologies including</p>
    <div class="trust-logos">
      <div class="trust-tech"><span class="trust-tech-icon">🟦</span> WordPress</div>
      <div class="trust-tech"><span class="trust-tech-icon">🟢</span> Shopify</div>
      <div class="trust-tech"><span class="trust-tech-icon">⚛️</span> React</div>
      <div class="trust-tech"><span class="trust-tech-icon">▲</span> Next.js</div>
      <div class="trust-tech"><span class="trust-tech-icon">🌐</span> Wix</div>
      <div class="trust-tech"><span class="trust-tech-icon">🔷</span> Drupal</div>
      <div class="trust-tech"><span class="trust-tech-icon">🟠</span> Laravel</div>
      <div class="trust-tech"><span class="trust-tech-icon">🐍</span> Django</div>
      <div class="trust-tech"><span class="trust-tech-icon">💎</span> Ruby on Rails</div>
      <div class="trust-tech"><span class="trust-tech-icon">🌊</span> Webflow</div>
    </div>
  </div>
</div>

<!-- ═══════════════════════════════════════════════════════
     HOW IT WORKS
═══════════════════════════════════════════════════════ -->
<section class="how-section" id="how-it-works">
  <div class="wrapper">
    <div class="section-header reveal">
      <div class="section-eyebrow">⬡ Process</div>
      <h2 class="section-title">How detection works</h2>
      <p class="section-sub">Three precise steps, completed in seconds — no guessing, no waiting.</p>
    </div>

    <div class="steps-grid reveal">
      <div class="step-card">
        <div class="step-num">01</div>
        <div class="step-icon step-icon--1">⬇️</div>
        <div class="step-title">Fetch</div>
        <p class="step-desc">We download the full page HTML, capture HTTP response headers, cookies, and inline scripts in real time from the live server.</p>
      </div>
      <div class="step-card">
        <div class="step-num">02</div>
        <div class="step-icon step-icon--2">🔍</div>
        <div class="step-title">Match</div>
        <p class="step-desc">300+ curated fingerprints are checked: script paths, <code style="font-family:var(--font-mono);font-size:.8em;color:var(--lime);background:var(--lime-dim);padding:1px 5px;border-radius:4px">meta</code> generators, unique directory structures, and HTTP header patterns.</p>
      </div>
      <div class="step-card">
        <div class="step-num">03</div>
        <div class="step-icon step-icon--3">📊</div>
        <div class="step-title">Score</div>
        <p class="step-desc">Each match is weighted for reliability. A confidence score is calculated and results are grouped by category for clarity.</p>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════════════════════════════════════════════════
     CAPABILITIES
═══════════════════════════════════════════════════════ -->
<section class="caps-section" id="capabilities">
  <div class="wrapper">
    <div class="section-header reveal">
      <div class="section-eyebrow" style="color:var(--violet);background:rgba(167,139,250,.07);border-color:rgba(167,139,250,.2)">⬡ Coverage</div>
      <h2 class="section-title">What we can detect</h2>
      <p class="section-sub">From enterprise CMS platforms to micro frontend frameworks — comprehensive coverage across every major category.</p>
    </div>

    <div class="caps-grid">

      <!-- WordPress -->
      <div class="cap-card reveal" style="--cap-c:#3b82f6">
        <div class="cap-card-glow" style="background:radial-gradient(ellipse at top left, rgba(59,130,246,.05), transparent 60%)"></div>
        <div class="cap-num">01</div>
        <div class="cap-accent" style="background:rgba(59,130,246,.1)">🔌</div>
        <h3 class="cap-title">WordPress Ecosystem</h3>
        <p class="cap-desc">Deep inspection of asset paths, script handles, and directory structures reveals the active theme and plugins with high precision.</p>
        <div class="cap-tags">
          <span class="cap-tag" style="color:#60a5fa;background:rgba(59,130,246,.08);border-color:rgba(59,130,246,.2)">WordPress</span>
          <span class="cap-tag" style="color:#60a5fa;background:rgba(59,130,246,.08);border-color:rgba(59,130,246,.2)">WooCommerce</span>
          <span class="cap-tag" style="color:#60a5fa;background:rgba(59,130,246,.08);border-color:rgba(59,130,246,.2)">Elementor</span>
          <span class="cap-tag" style="color:#60a5fa;background:rgba(59,130,246,.08);border-color:rgba(59,130,246,.2)">Divi</span>
          <span class="cap-tag" style="color:#60a5fa;background:rgba(59,130,246,.08);border-color:rgba(59,130,246,.2)">Yoast SEO</span>
        </div>
      </div>

      <!-- eCommerce -->
      <div class="cap-card reveal" style="--cap-c:#10b981">
        <div class="cap-card-glow" style="background:radial-gradient(ellipse at top left, rgba(16,185,129,.05), transparent 60%)"></div>
        <div class="cap-num">02</div>
        <div class="cap-accent" style="background:rgba(16,185,129,.1)">🛒</div>
        <h3 class="cap-title">eCommerce Platforms</h3>
        <p class="cap-desc">Identify the storefront engine behind any online shop — from Shopify's checkout flows to Magento's catalog architecture.</p>
        <div class="cap-tags">
          <span class="cap-tag" style="color:#34d399;background:rgba(16,185,129,.08);border-color:rgba(16,185,129,.2)">Shopify</span>
          <span class="cap-tag" style="color:#34d399;background:rgba(16,185,129,.08);border-color:rgba(16,185,129,.2)">Magento</span>
          <span class="cap-tag" style="color:#34d399;background:rgba(16,185,129,.08);border-color:rgba(16,185,129,.2)">BigCommerce</span>
          <span class="cap-tag" style="color:#34d399;background:rgba(16,185,129,.08);border-color:rgba(16,185,129,.2)">PrestaShop</span>
          <span class="cap-tag" style="color:#34d399;background:rgba(16,185,129,.08);border-color:rgba(16,185,129,.2)">OpenCart</span>
        </div>
      </div>

      <!-- CMS & Builders -->
      <div class="cap-card reveal" style="--cap-c:#ec4899">
        <div class="cap-card-glow" style="background:radial-gradient(ellipse at top left, rgba(236,72,153,.05), transparent 60%)"></div>
        <div class="cap-num">03</div>
        <div class="cap-accent" style="background:rgba(236,72,153,.1)">🧩</div>
        <h3 class="cap-title">CMS &amp; Website Builders</h3>
        <p class="cap-desc">From enterprise-grade CMS platforms to modern drag-and-drop builders — comprehensive coverage across the full spectrum.</p>
        <div class="cap-tags">
          <span class="cap-tag" style="color:#f472b6;background:rgba(236,72,153,.08);border-color:rgba(236,72,153,.2)">Joomla</span>
          <span class="cap-tag" style="color:#f472b6;background:rgba(236,72,153,.08);border-color:rgba(236,72,153,.2)">Drupal</span>
          <span class="cap-tag" style="color:#f472b6;background:rgba(236,72,153,.08);border-color:rgba(236,72,153,.2)">Wix</span>
          <span class="cap-tag" style="color:#f472b6;background:rgba(236,72,153,.08);border-color:rgba(236,72,153,.2)">Squarespace</span>
          <span class="cap-tag" style="color:#f472b6;background:rgba(236,72,153,.08);border-color:rgba(236,72,153,.2)">Webflow</span>
          <span class="cap-tag" style="color:#f472b6;background:rgba(236,72,153,.08);border-color:rgba(236,72,153,.2)">Ghost</span>
        </div>
      </div>

      <!-- Frameworks -->
      <div class="cap-card reveal" style="--cap-c:#f59e0b">
        <div class="cap-card-glow" style="background:radial-gradient(ellipse at top left, rgba(245,158,11,.05), transparent 60%)"></div>
        <div class="cap-num">04</div>
        <div class="cap-accent" style="background:rgba(245,158,11,.1)">⚙️</div>
        <h3 class="cap-title">Frameworks &amp; Languages</h3>
        <p class="cap-desc">Detect the underlying programming language and frontend or backend framework powering the site's architecture.</p>
        <div class="cap-tags">
          <span class="cap-tag" style="color:#fbbf24;background:rgba(245,158,11,.08);border-color:rgba(245,158,11,.2)">React</span>
          <span class="cap-tag" style="color:#fbbf24;background:rgba(245,158,11,.08);border-color:rgba(245,158,11,.2)">Next.js</span>
          <span class="cap-tag" style="color:#fbbf24;background:rgba(245,158,11,.08);border-color:rgba(245,158,11,.2)">Vue.js</span>
          <span class="cap-tag" style="color:#fbbf24;background:rgba(245,158,11,.08);border-color:rgba(245,158,11,.2)">Laravel</span>
          <span class="cap-tag" style="color:#fbbf24;background:rgba(245,158,11,.08);border-color:rgba(245,158,11,.2)">Django</span>
          <span class="cap-tag" style="color:#fbbf24;background:rgba(245,158,11,.08);border-color:rgba(245,158,11,.2)">Rails</span>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ═══════════════════════════════════════════════════════
     FAQ
═══════════════════════════════════════════════════════ -->
<section class="faq-section" id="faq">
  <div class="wrapper">
    <div class="faq-cols">

      <div class="faq-sidebar reveal">
        <div class="section-eyebrow" style="color:var(--orange);background:rgba(249,115,22,.07);border-color:rgba(249,115,22,.18)">❓ FAQ</div>
        <h2 class="section-title" style="font-size:1.9rem;margin-top:16px">Common questions</h2>
        <p class="faq-sidebar-desc">Everything you need to know about our free CMS and technology detection tool. Can't find an answer? Reach out.</p>
        <a href="mailto:hello@stackdetect.io" class="btn-contact">
          <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><rect x="1" y="3" width="14" height="10" rx="2" stroke="currentColor" stroke-width="1.5"/><path d="m1 5 7 4 7-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
          Contact support
        </a>
      </div>

      <div class="faq-list reveal">

        <div class="faq-item" data-faq="0">
          <button class="faq-btn" aria-expanded="false">
            <span class="faq-q">How do I find out what CMS a website is using for free?</span>
            <span class="faq-arr">+</span>
          </button>
          <div class="faq-body">
            <div class="faq-body-inner">
              Enter the full URL into the scan bar above and click <strong>Analyze</strong>. Our engine instantly inspects the site's HTML, headers, and scripts to identify the platform — completely free, no account needed.
            </div>
          </div>
        </div>

        <div class="faq-item" data-faq="1">
          <button class="faq-btn" aria-expanded="false">
            <span class="faq-q">Is this tool really 100% free with no limits?</span>
            <span class="faq-arr">+</span>
          </button>
          <div class="faq-body">
            <div class="faq-body-inner">
              Yes. StackDetect is completely free with <strong>unlimited scans</strong> and no registration required. There are no hidden plans or rate limits for standard use.
            </div>
          </div>
        </div>

        <div class="faq-item" data-faq="2">
          <button class="faq-btn" aria-expanded="false">
            <span class="faq-q">How does the detection algorithm work?</span>
            <span class="faq-arr">+</span>
          </button>
          <div class="faq-body">
            <div class="faq-body-inner">
              We use multi-signal <strong>fingerprinting</strong> — checking <code>meta name="generator"</code> tags, HTTP response headers, unique asset paths like <code>/wp-content/</code>, cookie names, and inline script patterns. Each signal is weighted to produce a confidence score.
            </div>
          </div>
        </div>

        <div class="faq-item" data-faq="3">
          <button class="faq-btn" aria-expanded="false">
            <span class="faq-q">Can it detect WordPress themes and plugins?</span>
            <span class="faq-arr">+</span>
          </button>
          <div class="faq-body">
            <div class="faq-body-inner">
              Yes. By inspecting asset directories and script handles, we can identify the active WordPress <strong>theme</strong> and many installed <strong>plugins</strong> — including popular ones like Elementor, WooCommerce, and Yoast SEO.
            </div>
          </div>
        </div>

        <div class="faq-item" data-faq="4">
          <button class="faq-btn" aria-expanded="false">
            <span class="faq-q">What if a website hides its technology stack?</span>
            <span class="faq-arr">+</span>
          </button>
          <div class="faq-body">
            <div class="faq-body-inner">
              Some sites actively remove or obscure identifying headers and paths. In those cases, detection accuracy is reduced and we'll show you only what signals we could confirm. We'll never show a false positive — if we're not confident, we won't guess.
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</section>

<!-- ═══════════════════════════════════════════════════════
     CTA BAND
═══════════════════════════════════════════════════════ -->
<div class="wrapper">
  <div class="cta-band reveal">
    <h2 class="cta-band-title">Ready to reveal any site's stack?</h2>
    <p class="cta-band-sub">It's free. It's instant. No account required.</p>
    <button class="btn-cta-primary" onclick="document.getElementById('url-input').focus(); window.scrollTo({top:0, behavior:'smooth'})">
      Start scanning now
      <svg viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M3.75 9h10.5M9 3.75 14.25 9 9 14.25" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </button>
  </div>
</div>

<!-- ═══════════════════════════════════════════════════════
     FOOTER
═══════════════════════════════════════════════════════ -->
<footer class="footer">
  <div class="wrapper footer-inner">
    <div class="footer-logo">
      <div class="footer-logo-dot"></div>
      StackDetect
    </div>
    <div class="footer-links">
      <a href="#how-it-works">How it works</a>
      <a href="#faq">FAQ</a>
      <a href="mailto:hello@stackdetect.io">Contact</a>
    </div>
    <p class="footer-copy">© <?= date('Y') ?> StackDetect. Free for everyone.</p>
  </div>
</footer>

<!-- ═══════════════════════════════════════════════════════
     JAVASCRIPT
═══════════════════════════════════════════════════════ -->
<script>
(function () {
  'use strict';

  /* ── Refs ── */
  const urlInput      = document.getElementById('url-input');
  const scanBtn       = document.getElementById('scan-btn');
  const scanNote      = document.getElementById('scan-note');
  const loaderWrap    = document.getElementById('loader-wrap');
  const loaderText    = document.getElementById('loader-text');
  const loaderUrl     = document.getElementById('loader-url');
  const resultsSection= document.getElementById('results-section');
  const resultsGrid   = document.getElementById('results-grid');
  const resultHost    = document.getElementById('result-host');
  const noResults     = document.getElementById('no-results');
  const copyBtn       = document.getElementById('copy-btn');
  const rescanBtn     = document.getElementById('rescan-btn');

  /* ── Category config ── */
  const CATS = {
    cms:        { label: 'CMS',                  icon: '🏗️', color: '#3b82f6' },
    languages:  { label: 'Programming Language',  icon: '💻', color: '#10b981' },
    frameworks: { label: 'Framework / Library',   icon: '⚙️', color: '#f59e0b' },
    plugins:    { label: 'Plugins & Services',    icon: '🔌', color: '#a78bfa' },
    servers:    { label: 'Web Server',            icon: '🖥️', color: '#38d9c0' },
  };

  /* ── Loader steps ── */
  const STEPS = [
    { el: 'lstep-0', msg: 'Fetching page source…'          },
    { el: 'lstep-1', msg: 'Scanning HTTP headers…'          },
    { el: 'lstep-2', msg: 'Matching 300+ signatures…'       },
    { el: 'lstep-3', msg: 'Calculating confidence scores…'  },
  ];

  let loadTimer = null;
  let lastData  = null;
  let currentUrl = '';

  /* ── Scan trigger ── */
  function startScan(targetUrl) {
    let url = (targetUrl || 'https://' + urlInput.value).trim();
    if (!url || url === 'https://') {
      setNote('Please enter a URL first.', true);
      urlInput.focus();
      return;
    }
    if (!/^https?:\/\//i.test(url)) url = 'https://' + url;

    try { new URL(url); } catch {
      setNote('That doesn\'t look like a valid URL — try: example.com', true);
      return;
    }

    currentUrl = url;
    setState('loading');
    doScan(url);
  }

  /* ── AJAX ── */
  async function doScan(url) {
    try {
      const res = await fetch('detect.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ url }),
      });
      if (!res.ok) throw new Error('HTTP ' + res.status);
      const json = await res.json();
      if (!json.success) { setState('error', json.error || 'Unknown error'); return; }
      lastData = json;
      renderResults(json);
      setState('results');
    } catch (e) {
      setState('error', 'Network error: ' + e.message);
    }
  }

  /* ── Render ── */
  function renderResults(json) {
    const data = json.data || {};
    resultsGrid.innerHTML = '';
    resultHost.textContent = json.scanned || '';

    let total = 0;
    let delay = 0;

    for (const [key, meta] of Object.entries(CATS)) {
      const items = data[key] || [];
      if (!items.length) continue;
      total += items.length;

      const card = document.createElement('div');
      card.className = 'rcard';
      card.style.cssText = `animation-delay:${delay}ms; --card-color:${meta.color}`;
      delay += 80;

      card.innerHTML = `
        <div class="rcard-head">
          <div class="rcard-icon-wrap">${meta.icon}</div>
          <span class="rcard-cat">${meta.label}</span>
          <span class="rcard-count">${items.length}</span>
        </div>
        <div class="tech-list">
          ${items.map(t => `
            <div class="tech-row">
              <span class="tech-emoji">${t.icon || '🔧'}</span>
              <span class="tech-name">${esc(t.name)}</span>
              <div class="tech-conf">
                <span class="tech-conf-num">${Math.round(t.score)}%</span>
                <div class="conf-bar">
                  <div class="conf-bar-fill" data-w="${Math.round(t.score)}%" style="background:${meta.color};width:0"></div>
                </div>
              </div>
            </div>
          `).join('')}
        </div>
      `;
      resultsGrid.appendChild(card);
    }

    noResults.hidden = total > 0;

    requestAnimationFrame(() => {
      document.querySelectorAll('.conf-bar-fill').forEach(bar => {
        setTimeout(() => { bar.style.width = bar.dataset.w; }, 120);
      });
    });
  }

  /* ── State machine ── */
  function setState(state, errMsg) {
    clearInterval(loadTimer);
    STEPS.forEach((s, i) => {
      const el = document.getElementById(s.el);
      if (el) el.classList.remove('active');
    });

    loaderWrap.hidden     = true;
    resultsSection.hidden = true;
    scanBtn.disabled      = false;
    setNote('Enter any website URL — results appear in under 5 seconds', false);

    if (state === 'loading') {
      loaderWrap.hidden = false;
      loaderUrl.textContent = currentUrl;
      scanBtn.disabled = true;

      let step = 0;
      activateStep(step);

      loadTimer = setInterval(() => {
        step = (step + 1) % STEPS.length;
        activateStep(step);
      }, 1500);

      loaderWrap.scrollIntoView({ behavior: 'smooth', block: 'center' });

    } else if (state === 'results') {
      resultsSection.hidden = false;
      setTimeout(() => resultsSection.scrollIntoView({ behavior: 'smooth', block: 'start' }), 80);

    } else if (state === 'error') {
      setNote(errMsg || 'Something went wrong.', true);
      urlInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
      urlInput.focus();
    }
  }

  function activateStep(idx) {
    STEPS.forEach((s, i) => {
      const el = document.getElementById(s.el);
      if (!el) return;
      el.classList.toggle('active', i === idx);
    });
    if (STEPS[idx]) loaderText.textContent = STEPS[idx].msg;
  }

  function setNote(msg, isError) {
    scanNote.innerHTML = isError
      ? msg
      : 'Enter any website URL — <span>results appear in under 5 seconds</span>';
    scanNote.classList.toggle('error', isError);
    if (!isError) scanNote.querySelector('span').style.color = 'var(--text-2)';
  }

  /* ── Copy ── */
  copyBtn.addEventListener('click', () => {
    if (!lastData) return;
    const lines = [`StackDetect report: ${lastData.final_url || currentUrl}`, ''];
    for (const [cat, items] of Object.entries(lastData.data || {})) {
      if (items.length) {
        lines.push(`[${cat.toUpperCase()}]`);
        items.forEach(t => lines.push(`  • ${t.name} (${t.score}%)`));
        lines.push('');
      }
    }
    navigator.clipboard.writeText(lines.join('\n')).then(() => {
      copyBtn.innerHTML = `<svg width="13" height="13" viewBox="0 0 16 16" fill="none"><path d="M3 8l4 4 6-7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg> Copied!`;
      setTimeout(() => {
        copyBtn.innerHTML = `<svg width="13" height="13" viewBox="0 0 16 16" fill="none"><rect x="5" y="5" width="9" height="9" rx="2" stroke="currentColor" stroke-width="1.5"/><path d="M11 5V4a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h1" stroke="currentColor" stroke-width="1.5"/></svg> Copy report`;
      }, 2200);
    });
  });

  /* ── Rescan ── */
  rescanBtn.addEventListener('click', () => {
    resultsSection.hidden = true;
    window.scrollTo({ top: 0, behavior: 'smooth' });
    setTimeout(() => urlInput.focus(), 400);
  });

  /* ── Keyboard & click ── */
  urlInput.addEventListener('keydown', e => { if (e.key === 'Enter') startScan(); });
  scanBtn.addEventListener('click', () => startScan());

  /* ── Util ── */
  function esc(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }

})();

/* ── FAQ Accordion ── */
(function () {
  document.querySelectorAll('.faq-item').forEach(function (item) {
    const btn  = item.querySelector('.faq-btn');
    const body = item.querySelector('.faq-body');
    if (!btn || !body) return;

    btn.addEventListener('click', function () {
      const isOpen = item.classList.contains('open');

      document.querySelectorAll('.faq-item.open').forEach(function (other) {
        if (other !== item) {
          other.classList.remove('open');
          other.querySelector('.faq-btn').setAttribute('aria-expanded', 'false');
          other.querySelector('.faq-body').style.maxHeight = '0';
        }
      });

      if (isOpen) {
        item.classList.remove('open');
        btn.setAttribute('aria-expanded', 'false');
        body.style.maxHeight = '0';
      } else {
        item.classList.add('open');
        btn.setAttribute('aria-expanded', 'true');
        body.style.maxHeight = body.scrollHeight + 'px';
      }
    });
  });
})();

/* ── Scroll reveal ── */
(function () {
  if (!('IntersectionObserver' in window)) {
    document.querySelectorAll('.reveal').forEach(function (el) { el.classList.add('is-visible'); });
    return;
  }
  var io = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (entry.isIntersecting) {
        entry.target.classList.add('is-visible');
        io.unobserve(entry.target);
      }
    });
  }, { threshold: 0.1 });
  document.querySelectorAll('.reveal').forEach(function (el) { io.observe(el); });
})();
</script>

</body>
</html>