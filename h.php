<?php
/**
 * home.php — StackDetect
 * Clean white modern SaaS-style CMS detector homepage
 */
$page_title = 'StackDetect — Detect Any Website\'s CMS & Tech Stack Instantly';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($page_title) ?></title>
  <meta name="description" content="Instantly detect any website's CMS, framework, plugins, and tech stack. Free, fast, no registration required." />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet" />
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
    .rcard{background:#fff;border:1.5px solid var(--border);border-radius:var(--r-lg);padding:22px;box-shadow:var(--sh-sm);transition:border-color .2s,box-shadow .2s,transform .2s;animation:fadeUp .35s ease backwards}
    .rcard:hover{border-color:var(--border-2);box-shadow:var(--sh-md);transform:translateY(-2px)}
    .rcard-head{display:flex;align-items:center;gap:10px;padding-bottom:14px;border-bottom:1px solid var(--border);margin-bottom:14px}
    .rcard-ico{width:34px;height:34px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0}
    .rcard-lbl{font-size:.68rem;font-weight:700;letter-spacing:.09em;text-transform:uppercase;color:var(--ink-3);flex:1}
    .rcard-bdg{font-size:.68rem;font-weight:700;padding:2px 8px;border-radius:999px;background:var(--blue-lt);color:var(--blue);border:1px solid #bfdbfe}
    .tech-rows{display:flex;flex-direction:column;gap:8px}
    .tech-row{display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:var(--r-sm);background:var(--bg);border:1px solid var(--border);transition:background .15s,border-color .15s}
    .tech-row:hover{background:var(--blue-lt);border-color:#bfdbfe}
    .tech-emoji{font-size:1rem;flex-shrink:0}
    .tech-name{flex:1;font-size:.85rem;font-weight:600;color:var(--ink);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;min-width:0}
    .tech-sc{flex-shrink:0;text-align:right}
    .tech-pct{font-family:var(--mono);font-size:.67rem;color:var(--ink-4);display:block;margin-bottom:3px}
    .pbar{width:56px;height:3px;border-radius:2px;background:var(--border-2);overflow:hidden}
    .pfill{height:100%;border-radius:2px;width:0;transition:width 1.1s cubic-bezier(.34,1.2,.64,1)}
    .no-detect{text-align:center;padding:56px 24px;border:2px dashed var(--border-2);border-radius:var(--r-xl)}
    .no-detect-ico{font-size:2.5rem;margin-bottom:14px}
    .no-detect h3{font-size:1rem;font-weight:700;margin-bottom:8px}
    .no-detect p{font-size:.875rem;color:var(--ink-3)}
    /* LOGOS */
    .logos-sec{background:var(--bg);border-top:1px solid var(--border);border-bottom:1px solid var(--border);padding:60px 24px}
    .logos-in{max-width:1000px;margin:0 auto}
    .logos-h{text-align:center;font-size:1.3rem;font-weight:800;letter-spacing:-.02em;margin-bottom:8px}
    .logos-sub{text-align:center;font-size:.875rem;color:var(--ink-3);margin-bottom:40px}
    .logos-grid{display:grid;grid-template-columns:repeat(6,1fr);gap:10px}
    @media(max-width:768px){.logos-grid{grid-template-columns:repeat(4,1fr)}}
    @media(max-width:480px){.logos-grid{grid-template-columns:repeat(3,1fr)}}
    .lchip{display:flex;flex-direction:column;align-items:center;gap:7px;padding:16px 8px;border-radius:var(--r-md);background:#fff;border:1.5px solid var(--border);font-size:.71rem;font-weight:600;color:var(--ink-3);text-align:center;transition:border-color .2s,color .2s,box-shadow .2s,transform .2s}
    .lchip:hover{border-color:var(--blue);color:var(--blue);box-shadow:var(--sh-md);transform:translateY(-2px)}
    .lchip-ico{font-size:1.4rem;line-height:1}
    /* NUMBERS STRIP */
    .nums-strip{background:var(--blue);padding:48px 24px}
    .nums-in{max-width:1000px;margin:0 auto;display:grid;grid-template-columns:repeat(4,1fr);gap:2px}
    @media(max-width:640px){.nums-in{grid-template-columns:repeat(2,1fr)}}
    .num-item{padding:24px 20px;text-align:center;border-right:1px solid rgba(255,255,255,.15)}
    .num-item:last-child{border-right:none}
    .num-val{font-size:2.1rem;font-weight:800;color:#fff;letter-spacing:-.03em;line-height:1;margin-bottom:6px}
    .num-lbl{font-size:.78rem;color:rgba(255,255,255,.7);font-weight:500}
    /* HOW */
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
    /* EDITORIAL 4-BLOCK */
    .editorial-sec{background:var(--bg);border-top:1px solid var(--border);border-bottom:1px solid var(--border);padding:56px 24px}
    .editorial-in{max-width:1000px;margin:0 auto;display:grid;grid-template-columns:1fr 1fr;gap:20px;align-items:start}
    @media(max-width:760px){.editorial-in{grid-template-columns:1fr}}
    .ed-col{display:flex;flex-direction:column;gap:20px}
    .ed-card{background:#fff;border:1.5px solid var(--border);border-radius:var(--r-lg);padding:28px 26px;box-shadow:var(--sh-sm);transition:border-color .2s,box-shadow .2s}
    .ed-card:hover{border-color:var(--border-2);box-shadow:var(--sh-md)}
    .ed-card--blue{background:linear-gradient(135deg,#2563eb 0%,#4f46e5 100%);border-color:transparent}
    .ed-card--blue:hover{border-color:transparent;box-shadow:0 8px 32px rgba(37,99,235,.35)}
    .ed-card-icon{width:36px;height:36px;border-radius:9px;display:flex;align-items:center;justify-content:center;margin-bottom:14px;flex-shrink:0}
    .ed-title{font-size:1rem;font-weight:800;letter-spacing:-.015em;color:var(--ink);margin-bottom:12px;line-height:1.3}
    .ed-body{display:flex;flex-direction:column;gap:10px}
    .ed-body p{font-size:.83rem;color:var(--ink-3);line-height:1.75}
    .ed-body strong{color:var(--ink-2);font-weight:700}
    .ed-body code{font-family:var(--mono);font-size:.78em;color:var(--rose);background:rgba(225,29,72,.06);padding:1px 6px;border-radius:4px;font-weight:500}
    .ed-code{font-family:var(--mono);font-size:.75rem;color:var(--rose);background:#fff5f5;border:1px solid #fecdd3;border-radius:var(--r-sm);padding:10px 14px;line-height:1.6;overflow-x:auto;white-space:nowrap}
    .ed-chrome-btn{display:flex;align-items:center;justify-content:center;gap:9px;width:100%;margin-top:20px;padding:13px 20px;border-radius:var(--r-md);background:#fff;color:var(--blue);font-weight:700;font-size:.875rem;border:none;cursor:pointer;box-shadow:0 4px 16px rgba(0,0,0,.18);transition:transform .2s,box-shadow .2s}
    .ed-chrome-btn:hover{transform:translateY(-1px);box-shadow:0 8px 24px rgba(0,0,0,.22)}
    .ed-stats{display:flex;align-items:center;background:#fff;border:1.5px solid var(--border);border-radius:var(--r-lg);padding:24px 28px;gap:0;box-shadow:var(--sh-sm)}
    .ed-stat{flex:1;text-align:center}
    .ed-stat-num{font-size:1.6rem;font-weight:800;color:var(--ink);letter-spacing:-.03em;line-height:1;margin-bottom:5px}
    .ed-stat-lbl{font-size:.75rem;color:var(--ink-3);font-weight:500}
    .ed-stat-div{width:1px;height:48px;background:var(--border-2);flex-shrink:0}
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
<nav class="nav">
  <div class="nav-in">
    <a href="/" class="logo">
      <div class="logo-mark">S</div>
      StackDetect
    </a>
    <div class="nav-menu">
      <a href="#how-it-works">How it works</a>
      <a href="#technologies">Technologies</a>
      <a href="#faq">FAQ</a>
    </div>
    <button class="btn-primary-sm" onclick="document.getElementById('url-input').focus();scrollTo({top:0,behavior:'smooth'})">Try Free →</button>
  </div>
</nav>

<!-- HERO -->
<div class="hero-wrap">
  <div class="blob blob-1"></div>
  <div class="blob blob-2"></div>
  <div class="blob blob-3"></div>
  <div class="hero">
    <div class="hero-badge"><span class="bdot"></span> Instant CMS Analysis Engine</div>
    <h1 class="hero-h1">What CMS is That?<br>Use <span class="hl">CMS Detector</span> and Find Out</h1>
    <p class="hero-p">Curious about what powers your favorite sites? Instantly detect CMS, frameworks, and technologies used on any URL with our advanced detection algorithm.</p>

    <div class="scan-wrap">
      <div class="scan-box" id="scan-box">
        <div class="scan-ico">
          <svg viewBox="0 0 20 20" fill="none"><path d="M10 2C5.58 2 2 5.58 2 10s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8z" stroke="currentColor" stroke-width="1.5"/><path d="M2 10h16M10 2a12.5 12.5 0 0 1 0 16M10 2a12.5 12.5 0 0 0 0 16" stroke="currentColor" stroke-width="1.5"/></svg>
        </div>
        <input type="text" id="url-input" class="scan-input" placeholder="Enter a URL (e.g., example.com)" autocomplete="off" spellcheck="false" autofocus />
        <button class="scan-btn" id="scan-btn" type="button">
          Detect CMS
          <svg viewBox="0 0 16 16" fill="none"><path d="M3 8h10M8.5 3.5 13 8l-4.5 4.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
      </div>
      <p class="scan-note" id="scan-note">By using this tool, you agree to our <a href="#">terms of service</a></p>
    </div>

    <div class="trust-row">
      <div class="ti"><svg viewBox="0 0 16 16" fill="none"><path d="M13.5 4L6 11.5 2.5 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>Free forever</div>
      <div class="tsep"></div>
      <div class="ti"><svg viewBox="0 0 16 16" fill="none"><path d="M13.5 4L6 11.5 2.5 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>No signup required</div>
      <div class="tsep"></div>
      <div class="ti"><svg viewBox="0 0 16 16" fill="none"><path d="M13.5 4L6 11.5 2.5 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>Results in under 5 seconds</div>
      <div class="tsep"></div>
      <div class="ti"><svg viewBox="0 0 16 16" fill="none"><path d="M13.5 4L6 11.5 2.5 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>300+ technologies</div>
    </div>
  </div>
</div>

<!-- LOADER -->
<div id="loader-wrap" hidden>
  <div class="loader-sec">
    <div class="loader-vis">
      <div class="lr lr-1"></div>
      <div class="lr lr-2"></div>
      <div class="lr-ico">🔍</div>
    </div>
    <p class="loader-msg" id="loader-text">Initializing scan…</p>
    <div class="loader-url" id="loader-url"></div>
    <div class="loader-pills">
      <span class="lpill" id="lpill-0">Fetching page</span>
      <span class="lpill" id="lpill-1">Reading headers</span>
      <span class="lpill" id="lpill-2">Matching signatures</span>
      <span class="lpill" id="lpill-3">Scoring results</span>
    </div>
  </div>
</div>

<!-- RESULTS -->
<section id="results-section" hidden>
  <div class="results-outer">
    <div class="rtopbar">
      <div class="rtb-left">
        <div class="rtb-dot"></div>
        Scan complete —
        <span class="rtb-url" id="result-host"></span>
      </div>
      <div class="rtb-right">
        <button class="btn-act" id="copy-btn">
          <svg viewBox="0 0 16 16" fill="none"><rect x="5" y="5" width="9" height="9" rx="1.5" stroke="currentColor" stroke-width="1.4"/><path d="M11 5V3.5A1.5 1.5 0 0 0 9.5 2h-6A1.5 1.5 0 0 0 2 3.5v6A1.5 1.5 0 0 0 3.5 11H5" stroke="currentColor" stroke-width="1.4"/></svg>
          Copy
        </button>
        <button class="btn-act" id="rescan-btn">
          <svg viewBox="0 0 16 16" fill="none"><path d="M13.5 8A5.5 5.5 0 1 1 8 2.5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/><path d="M13.5 2.5v3h-3" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
          New scan
        </button>
      </div>
    </div>
    <div class="res-grid" id="results-grid"></div>
    <div class="no-detect" id="no-results" hidden>
      <div class="no-detect-ico">🔭</div>
      <h3>Nothing detected</h3>
      <p>We couldn't identify known technologies. This site may use custom or obfuscated code.</p>
    </div>
  </div>
</section>

<!-- TECH LOGOS GRID -->
<div class="logos-sec" id="technologies">
  <div class="logos-in">
    <h2 class="logos-h appear">Detects Hundreds of Technologies</h2>
    <p class="logos-sub appear">Our algorithm recognizes over 400+ CMS types, frameworks, and e-commerce platforms instantly.</p>
    <div class="logos-grid appear">
      <div class="lchip"><span class="lchip-ico">🟦</span>WordPress</div>
      <div class="lchip"><span class="lchip-ico">🟢</span>Shopify</div>
      <div class="lchip"><span class="lchip-ico">🔷</span>Joomla</div>
      <div class="lchip"><span class="lchip-ico">🫐</span>Drupal</div>
      <div class="lchip"><span class="lchip-ico">⬛</span>Squarespace</div>
      <div class="lchip"><span class="lchip-ico">🌐</span>Wix</div>
      <div class="lchip"><span class="lchip-ico">⚛️</span>React</div>
      <div class="lchip"><span class="lchip-ico">▲</span>Next.js</div>
      <div class="lchip"><span class="lchip-ico">💚</span>Vue.js</div>
      <div class="lchip"><span class="lchip-ico">🔴</span>Angular</div>
      <div class="lchip"><span class="lchip-ico">🌊</span>Webflow</div>
      <div class="lchip"><span class="lchip-ico">👻</span>Ghost</div>
      <div class="lchip"><span class="lchip-ico">🛒</span>Magento</div>
      <div class="lchip"><span class="lchip-ico">🏪</span>WooCommerce</div>
      <div class="lchip"><span class="lchip-ico">🔶</span>Laravel</div>
      <div class="lchip"><span class="lchip-ico">🐍</span>Django</div>
      <div class="lchip"><span class="lchip-ico">💎</span>Ruby on Rails</div>
      <div class="lchip"><span class="lchip-ico">🐘</span>PrestaShop</div>
      <div class="lchip"><span class="lchip-ico">🏗️</span>Gatsby</div>
      <div class="lchip"><span class="lchip-ico">📦</span>BigCommerce</div>
      <div class="lchip"><span class="lchip-ico">🎨</span>Elementor</div>
      <div class="lchip"><span class="lchip-ico">🔌</span>Divi</div>
      <div class="lchip"><span class="lchip-ico">📊</span>HubSpot</div>
      <div class="lchip"><span class="lchip-ico">☁️</span>Cloudflare</div>
    </div>
  </div>
</div>

<!-- 4 EDITORIAL BLOCKS + STATS -->
<div class="editorial-sec">
  <div class="editorial-in">

    <!-- LEFT COLUMN -->
    <div class="ed-col">

      <!-- Block 1: What CMS is this? -->
      <div class="ed-card appear">
        <div class="ed-card-icon" style="background:#eff6ff;color:#2563eb">
          <svg width="18" height="18" viewBox="0 0 20 20" fill="none"><circle cx="10" cy="10" r="8" stroke="currentColor" stroke-width="1.6"/><path d="M10 9v5M10 7v.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
        </div>
        <h2 class="ed-title">What CMS is this?</h2>
        <div class="ed-body">
          <p>With so many CMS and website builders out there, it's only natural that you will encounter lots of websites on a daily basis and wonder if they were custom built or built with a known CMS. The amount of times people in the digital arena ask themselves <strong>"What CMS is this?"</strong> or <strong>"How did they build this site?"</strong> is not a small number. Now with our algorithm CMS detector, we can recognise (some would say guess) hundreds of CMS, frameworks and website builders. Now when you find a cool site that you want to know how it was built, just come here and add the URL to the search bar and we will do the rest.</p>
        </div>
      </div>

      <!-- Block 2: CMS Checker -->
      <div class="ed-card appear">
        <div class="ed-card-icon" style="background:#f0fdf4;color:#16a34a">
          <svg width="18" height="18" viewBox="0 0 20 20" fill="none"><rect x="3" y="3" width="14" height="14" rx="2" stroke="currentColor" stroke-width="1.6"/><path d="M7 10l2.5 2.5L13 7" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </div>
        <h2 class="ed-title">CMS Checker — how do we do it?</h2>
        <div class="ed-body">
          <p>Detecting what CMS is being used on a given website can be simple and sometimes it can be daunting. A lot of website builders use a "generator meta tag" which makes it really easy to detect which CMS is being used. Here is an example of such a tag:</p>
          <div class="ed-code">&lt;meta name="generator" content="WordPress 6.4.1" /&gt;</div>
          <p>Another option is to detect code patterns that are used on every website built by a given CMS. A lot of "non open source" systems call JavaScript or CSS files from the same place which also makes the task of detection pretty easy. Detecting such a CMS is pretty easy. Another option would be to check the "headers" being sent. There is usually a giveaway hidden in there somewhere.</p>
          <p style="margin-top:12px">There are a few other options that can be used but these are the main giveaways that help us run the CMS Detector.</p>
        </div>
      </div>

    </div><!-- /ed-col left -->

    <!-- RIGHT COLUMN -->
    <div class="ed-col">

      <!-- Block 3: Framework Detector -->
      <div class="ed-card appear">
        <div class="ed-card-icon" style="background:#fef9c3;color:#d97706">
          <svg width="18" height="18" viewBox="0 0 20 20" fill="none"><path d="M4 6l8-3 4 1.5v9L12 17 4 14V6z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M12 4v13M4 6l8 3 4-1.5" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/></svg>
        </div>
        <h2 class="ed-title">Framework Detector — What is the CMS?</h2>
        <div class="ed-body">
          <p>Not only can we detect a large amount of CMS and website builders, we can also detect a few frameworks such as <code>Laravel</code> and <code>CodeIgniter</code>. So now when you use our CMS detector tool, not only will you get the best CMS website builder results, we take it a step further with our framework detector abilities. A framework is much harder to detect, since it is only the platform on which developers choose to build their sites. The developer has full control over the entire HTML, so you will never see a meta tag in a <code>Laravel</code> site, telling us know that the site is built with <code>Laravel</code>. For that reason, it's much harder to detect frameworks, but nevertheless we can accurately detect them and will show you them in the results if you search for a site built with such a framework.</p>
          <p style="margin-top:12px">The truth is that sometimes it can be very easy to detect the popular CMS such as <code>WordPress</code> &amp; <code>Shopify</code>. But there are various other platforms that you need to take into account — and we do just that!</p>
          <p style="margin-top:12px">We have also built a new and improved CMS Detector on our sister site which you can use to not only detect what CMS a site is using but also get some additional information in the process.</p>
        </div>
      </div>

      <!-- Block 4: Chrome Extension CTA card -->
      <div class="ed-card ed-card--blue appear">
        <div class="ed-card-icon" style="background:rgba(255,255,255,.15);color:#fff">
          <svg width="18" height="18" viewBox="0 0 20 20" fill="none"><circle cx="10" cy="10" r="7" stroke="currentColor" stroke-width="1.6"/><circle cx="10" cy="10" r="3" stroke="currentColor" stroke-width="1.6"/><path d="M10 3v4M10 13v4M3 10h4M13 10h4" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg>
        </div>
        <h2 class="ed-title" style="color:#fff">CMS Detection Using a Chrome Extension</h2>
        <div class="ed-body" style="color:rgba(255,255,255,.82)">
          <p>By using our Chrome extension, you can detect any website's CMS on the go. Install it and the next time you are on a site that you want to detect its CMS, just hit the "CMS Detect" logo in your Chrome browser and we will do the rest. You will get the name of the CMS just like you do on the actual site.</p>
        </div>
        <button class="ed-chrome-btn" onclick="window.open('#','_blank')">
          <svg width="16" height="16" viewBox="0 0 20 20" fill="none"><circle cx="10" cy="10" r="8" stroke="currentColor" stroke-width="1.5"/><circle cx="10" cy="10" r="3.5" stroke="currentColor" stroke-width="1.5"/><path d="M10 2.5l6.06 10.5H3.94L10 2.5z" stroke="currentColor" stroke-width="1.3" stroke-linejoin="round"/></svg>
          Add to Chrome — It's Free
        </button>
      </div>

      <!-- Stats row -->
      <div class="ed-stats appear">
        <div class="ed-stat">
          <div class="ed-stat-num">46+</div>
          <div class="ed-stat-lbl">Systems Detected</div>
        </div>
        <div class="ed-stat-div"></div>
        <div class="ed-stat">
          <div class="ed-stat-num">4,962,700+</div>
          <div class="ed-stat-lbl">Sites Analysed</div>
        </div>
      </div>

    </div><!-- /ed-col right -->

  </div>
</div>

<!-- NUMBERS -->
<div class="nums-strip">
  <div class="nums-in">
    <div class="num-item"><div class="num-val">400+</div><div class="num-lbl">Technologies detected</div></div>
    <div class="num-item"><div class="num-val">&lt;5s</div><div class="num-lbl">Average scan time</div></div>
    <div class="num-item"><div class="num-val">100%</div><div class="num-lbl">Free forever</div></div>
    <div class="num-item"><div class="num-val">0</div><div class="num-lbl">Registration required</div></div>
  </div>
</div>

<!-- HOW IT WORKS -->
<section class="how-sec" id="how-it-works">
  <div class="how-in">
    <div class="center appear">
      <div class="sec-label">How it works</div>
      <h2 class="sec-h2">Three steps to reveal any tech stack</h2>
      <p class="sec-p" style="margin:0 auto">Fast, accurate detection using multi-signal fingerprinting — no guessing involved.</p>
    </div>
    <div class="how-grid">
      <div class="how-card appear">
        <div class="how-num">1</div>
        <div class="how-ico" style="background:#eff6ff">⬇️</div>
        <div class="how-t">Fetch the Page</div>
        <p class="how-d">We download the full page HTML, HTTP response headers, cookies, and inline scripts from the live server in real time.</p>
      </div>
      <div class="how-card appear">
        <div class="how-num">2</div>
        <div class="how-ico" style="background:#f0fdf4">🔍</div>
        <div class="how-t">Match Signatures</div>
        <p class="how-d">300+ fingerprints are tested: <code style="font-family:var(--mono);font-size:.8em;color:var(--blue);background:var(--blue-lt);padding:1px 5px;border-radius:4px">meta</code> generators, unique paths like <code style="font-family:var(--mono);font-size:.8em;color:var(--blue);background:var(--blue-lt);padding:1px 5px;border-radius:4px">/wp-content/</code>, and header patterns.</p>
      </div>
      <div class="how-card appear">
        <div class="how-num">3</div>
        <div class="how-ico" style="background:#fef9c3">📊</div>
        <div class="how-t">Score &amp; Report</div>
        <p class="how-d">Every match is weighted for reliability. A confidence score is calculated and displayed with results grouped by category.</p>
      </div>
    </div>
  </div>
</section>

<!-- FAQ -->
<section class="faq-sec" id="faq">
  <div class="faq-in">
    <div class="faq-hdr appear">
      <div class="sec-label">FAQ</div>
      <h2 class="sec-h2">Frequently asked questions</h2>
      <p class="sec-p" style="margin:10px auto 0">Everything you need to know about StackDetect.</p>
    </div>
    <div class="faq-list appear">
      <div class="faq-item">
        <button class="faq-btn"><span class="faq-q">How do I detect what CMS a website is using?</span><span class="faq-tog">+</span></button>
        <div class="faq-body"><div class="faq-body-in">Simply paste any URL into the scan bar and click <strong>Detect CMS</strong>. Our engine scans the site's HTML, headers, and scripts and shows you results in seconds — completely free, no account needed.</div></div>
      </div>
      <div class="faq-item">
        <button class="faq-btn"><span class="faq-q">Is StackDetect really free with no limits?</span><span class="faq-tog">+</span></button>
        <div class="faq-body"><div class="faq-body-in">Yes, <strong>100% free</strong> with unlimited scans and zero registration. There are no rate limits or hidden plans for standard lookups.</div></div>
      </div>
      <div class="faq-item">
        <button class="faq-btn"><span class="faq-q">How does the detection algorithm work?</span><span class="faq-tog">+</span></button>
        <div class="faq-body"><div class="faq-body-in">We use multi-signal <strong>fingerprinting</strong> — checking <code>meta name="generator"</code> tags, HTTP headers, unique paths like <code>/wp-content/</code>, cookie names, and inline script patterns, each weighted to produce a confidence score.</div></div>
      </div>
      <div class="faq-item">
        <button class="faq-btn"><span class="faq-q">Can it detect WordPress plugins and themes?</span><span class="faq-tog">+</span></button>
        <div class="faq-body"><div class="faq-body-in">Yes. By inspecting asset directories and script handles we can identify the active WordPress <strong>theme</strong> and many installed plugins including Elementor, WooCommerce, Yoast SEO, and Divi.</div></div>
      </div>
      <div class="faq-item">
        <button class="faq-btn"><span class="faq-q">What if a site hides its technology stack?</span><span class="faq-tog">+</span></button>
        <div class="faq-body"><div class="faq-body-in">Some sites deliberately remove headers or obfuscate paths. In those cases detection accuracy is lower — we only show results we can confirm with high confidence. We never show false positives.</div></div>
      </div>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="cta-sec">
  <div class="cta-box appear">
    <h2>Ready to detect any tech stack?</h2>
    <p>Free, instant, no account required.</p>
    <button class="btn-cta" onclick="document.getElementById('url-input').focus();scrollTo({top:0,behavior:'smooth'})">
      Start scanning free
      <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M8.5 3.5 13 8l-4.5 4.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </button>
  </div>
</section>

<!-- FOOTER -->
<footer class="footer">
  <div class="footer-in">
    <div class="logo"><div class="logo-mark">S</div>StackDetect</div>
    <div class="footer-links">
      <a href="#how-it-works">How it works</a>
      <a href="#technologies">Technologies</a>
      <a href="#faq">FAQ</a>
      <a href="mailto:hello@stackdetect.io">Contact</a>
    </div>
    <p class="footer-copy">© <?= date('Y') ?> StackDetect. Free for everyone.</p>
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
    cms:{label:'CMS',icon:'🏗️',bg:'#eff6ff',color:'#2563eb'},
    languages:{label:'Programming Language',icon:'💻',bg:'#f0fdf4',color:'#16a34a'},
    frameworks:{label:'Framework / Library',icon:'⚙️',bg:'#fef9c3',color:'#d97706'},
    plugins:{label:'Plugins & Services',icon:'🔌',bg:'#fdf4ff',color:'#9333ea'},
    servers:{label:'Web Server',icon:'🖥️',bg:'#f0fdfa',color:'#0d9488'},
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
      card.style.animationDelay=delay+'ms';
      delay+=80;
      card.innerHTML='<div class="rcard-head"><div class="rcard-ico" style="background:'+meta.bg+'">'+meta.icon+'</div><span class="rcard-lbl">'+meta.label+'</span><span class="rcard-bdg">'+items.length+'</span></div><div class="tech-rows">'+items.map(function(t){return '<div class="tech-row"><span class="tech-emoji">'+(t.icon||'🔧')+'</span><span class="tech-name">'+esc(t.name)+'</span><div class="tech-sc"><span class="tech-pct">'+Math.round(t.score)+'%</span><div class="pbar"><div class="pfill" data-w="'+Math.round(t.score)+'%" style="background:'+meta.color+';width:0"></div></div></div></div>';}).join('')+'</div>';
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
    var lines=['StackDetect — '+(lastData.final_url||currentUrl),''];
    Object.entries(lastData.data||{}).forEach(function(e){
      if(e[1].length){lines.push('['+e[0].toUpperCase()+']');e[1].forEach(function(t){lines.push('  • '+t.name+' ('+t.score+'%)');});lines.push('');}
    });
    navigator.clipboard.writeText(lines.join('\n')).then(function(){
      copyBtn.innerHTML='<svg viewBox="0 0 16 16" fill="none" width="13" height="13"><path d="M3 8l4 4 6-7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg> Copied!';
      setTimeout(function(){copyBtn.innerHTML='<svg viewBox="0 0 16 16" fill="none" width="13" height="13"><rect x="5" y="5" width="9" height="9" rx="1.5" stroke="currentColor" stroke-width="1.4"/><path d="M11 5V3.5A1.5 1.5 0 0 0 9.5 2h-6A1.5 1.5 0 0 0 2 3.5v6A1.5 1.5 0 0 0 3.5 11H5" stroke="currentColor" stroke-width="1.4"/></svg> Copy';},2200);
    });
  });

  rescanBtn.addEventListener('click',function(){resultSec.hidden=true;scrollTo({top:0,behavior:'smooth'});setTimeout(function(){urlInput.focus();},400);});
  urlInput.addEventListener('keydown',function(e){if(e.key==='Enter')startScan();});
  scanBtn.addEventListener('click',function(){startScan();});
  function esc(s){return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');}
})();

/* FAQ */
(function(){
  document.querySelectorAll('.faq-item').forEach(function(item){
    var btn=item.querySelector('.faq-btn'),body=item.querySelector('.faq-body');
    btn.addEventListener('click',function(){
      var open=item.classList.contains('open');
      document.querySelectorAll('.faq-item.open').forEach(function(o){
        o.classList.remove('open');o.querySelector('.faq-body').style.maxHeight='0';
      });
      if(!open){item.classList.add('open');body.style.maxHeight=body.scrollHeight+'px';}
    });
  });
})();

/* Scroll reveal */
(function(){
  if(!('IntersectionObserver' in window)){document.querySelectorAll('.appear').forEach(function(el){el.classList.add('in');});return;}
  var io=new IntersectionObserver(function(entries){entries.forEach(function(e){if(e.isIntersecting){e.target.classList.add('in');io.unobserve(e.target);}});},{threshold:0.1});
  document.querySelectorAll('.appear').forEach(function(el){io.observe(el);});
})();
</script>
</body>
</html>