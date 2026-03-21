<?php
/**
 * globalpartnershipcouncil.php — View
 * Letakkan di: /lending_word/app/views/frontend/globalpartnershipcouncil.php
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Global Partnership Council — Porsche Indonesia</title>
    <link rel="icon" type="image/png" href="/lending_word/public/assets/images/porsche-logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/lending_word/public/assets/css/style.css">
    <style>
        @font-face {
            font-family: "Porsche Next";
            src: url("/lending_word/public/assets/fonts/Porsche Next.ttf") format("truetype");
            font-weight: 100 900; font-style: normal; font-display: swap;
        }
        *, *::before, *::after { box-sizing: border-box; }
        :root {
            --black: #0a0a0a; --white: #ffffff;
            --grey-100: #f5f5f5; --grey-200: #e8e8e8;
            --grey-300: #d0d0d0; --grey-500: #888888; --grey-700: #444444;
            --gold-accent: #c9a84c; --gold-light: rgba(201,168,76,0.12);
            --ease: cubic-bezier(0.16, 1, 0.3, 1);
            --font-p: "Porsche Next", "Arial Narrow", Arial, sans-serif;
        }
        html { cursor: none; scroll-behavior: smooth; }
        body { background: #fff; color: #000; font-family: var(--font-p); font-weight: 300; overflow-x: hidden; margin: 0; }

        /* CURSOR */
        #cursor-dot, #cursor-ring {
            position: fixed; pointer-events: none; z-index: 9999; border-radius: 50%;
            top: 0; left: 0; transform: translate(-50%,-50%);
            transition-property: width, height, opacity, transform;
            transition-timing-function: var(--ease); mix-blend-mode: difference;
        }
        #cursor-dot { width: 8px; height: 8px; background: #fff; transition-duration: .2s,.2s,.2s,.15s; }
        #cursor-ring { width: 38px; height: 38px; border: 1.5px solid #fff; background: transparent; transition-duration: .35s,.35s,.3s,.22s; }
        body.c-link #cursor-dot { width: 5px; height: 5px; }
        body.c-link #cursor-ring { width: 54px; height: 54px; }
        body.c-click #cursor-dot { transform: translate(-50%,-50%) scale(2.5); opacity: 0; }
        body.c-click #cursor-ring { transform: translate(-50%,-50%) scale(1.5); opacity: 0; }

        /* SCROLL PROGRESS */
        #progress { position: fixed; top: 0; left: 0; height: 2px; width: 0; background: var(--black); z-index: 8000; transition: width .1s linear; }

        /* INTRO */
        #intro { position: fixed; inset: 0; z-index: 5000; display: flex; align-items: center; justify-content: center; background: #fff; transition: opacity .5s ease .1s; }
        #intro.done { opacity: 0; pointer-events: none; }
        .c-panel { position: absolute; top: 0; bottom: 0; width: 50%; background: #fff; z-index: 2; transition: transform 1.2s cubic-bezier(0.76,0,0.24,1); }
        .c-panel.l { left: 0; border-right: 1px solid rgba(0,0,0,0.08); }
        .c-panel.r { right: 0; border-left: 1px solid rgba(0,0,0,0.08); }
        #intro.open .c-panel.l { transform: translateX(-100%); }
        #intro.open .c-panel.r { transform: translateX(100%); }
        #intro-logo { position: relative; z-index: 1; opacity: 0; animation: fadeIn .6s .15s var(--ease) forwards; display: flex; align-items: center; justify-content: center; }
        #intro-logo img { width: clamp(80px,10vw,130px); height: auto; }
        @keyframes fadeIn { from{opacity:0;transform:translateY(10px);} to{opacity:1;transform:translateY(0);} }

        /* NAVBAR */
        .navbar { background: transparent !important; transition: background .4s ease, box-shadow .4s ease; }
        .navbar.scrolled { background: rgba(255,255,255,0.92) !important; backdrop-filter: blur(24px); -webkit-backdrop-filter: blur(24px); box-shadow: 0 1px 0 rgba(0,0,0,.07); }
        .navbar.scrolled .navbar-brand img { filter: brightness(0) !important; }
        .navbar.scrolled .navbar-menu a { color: var(--black) !important; }

        /* HERO */
        .gpc-hero { position: relative; height: 70vh; min-height: 480px; display: flex; align-items: flex-end; overflow: hidden; background: #000; }
        .gpc-hero-bg { position: absolute; inset: 0; background-size: cover; background-position: center 40%; filter: brightness(0.48); transform: scale(1.05); transition: transform 8s ease; }
        .gpc-hero-bg.loaded { transform: scale(1); }
        .gpc-hero-content { position: relative; z-index: 2; padding: 0 80px 72px; max-width: 800px; }
        .gpc-hero-eyebrow { font-family: var(--font-p); font-size: 0.62rem; font-weight: 400; letter-spacing: 0.35em; text-transform: uppercase; color: rgba(255,255,255,0.55); margin-bottom: 14px; display: flex; align-items: center; gap: 12px; opacity: 0; animation: fadeUp .8s 2.3s var(--ease) forwards; }
        .gpc-hero-eyebrow::before { content: ''; display: block; width: 28px; height: 1px; background: rgba(255,255,255,0.4); }
        .gpc-hero h1 { font-family: var(--font-p); font-size: clamp(2rem,4.5vw,4rem); font-weight: 700; color: #fff; line-height: 1.08; letter-spacing: .02em; margin: 0 0 16px; opacity: 0; animation: fadeUp .8s 2.5s var(--ease) forwards; }
        .gpc-hero-sub { font-family: var(--font-p); font-size: 0.9rem; font-weight: 300; color: rgba(255,255,255,0.55); letter-spacing: .02em; opacity: 0; animation: fadeUp .8s 2.7s var(--ease) forwards; border-top: 1px solid rgba(255,255,255,0.15); padding-top: 14px; margin-top: 14px; display: inline-block; }
        @keyframes fadeUp { from{opacity:0;transform:translateY(18px);} to{opacity:1;transform:translateY(0);} }

        /* BREADCRUMB */
        .gpc-breadcrumb { padding: 14px 80px; background: #fff; border-bottom: 1px solid var(--grey-200); }
        .gpc-breadcrumb span { font-family: var(--font-p); font-size: 0.72rem; color: var(--grey-500); font-weight: 300; letter-spacing: .02em; }
        .gpc-breadcrumb a { color: var(--grey-500); text-decoration: none; }
        .gpc-breadcrumb a:hover { color: var(--black); }

        /* INTRO SECTION */
        .gpc-intro { padding: 80px; display: flex; justify-content: center; }
        .gpc-intro-inner { max-width: 620px; text-align: center; }
        .gpc-intro h2 { font-family: var(--font-p); font-size: clamp(1.4rem,2.5vw,1.9rem); font-weight: 700; color: var(--black); letter-spacing: .01em; line-height: 1.25; margin-bottom: 20px; }
        .gpc-intro p { font-family: var(--font-p); font-size: 0.88rem; color: var(--grey-700); line-height: 1.85; font-weight: 300; }

        /* PARTNERS SECTION */
        .gpc-partners { padding: 72px 80px; background: var(--grey-100); }
        .gpc-partners-hd { display: flex; align-items: center; justify-content: space-between; margin-bottom: 36px; }
        .gpc-partners-hd h2 { font-family: var(--font-p); font-size: clamp(1.6rem,2.5vw,2.2rem); font-weight: 700; color: var(--black); letter-spacing: .02em; }
        .gpc-partners-nav { display: flex; gap: 8px; }
        .gpc-partners-nav-btn { width: 44px; height: 44px; border: 1px solid var(--grey-300); background: #fff; display: flex; align-items: center; justify-content: center; cursor: none; font-size: 11px; transition: all 0.2s var(--ease); }
        .gpc-partners-nav-btn:hover { background: var(--black); color: #fff; border-color: var(--black); }
        .gpc-partners-track { display: grid; grid-template-columns: repeat(5,1fr); gap: 1px; background: var(--grey-200); }
        .gpc-partner-card { background: #fff; padding: 24px 20px; display: flex; flex-direction: column; align-items: center; gap: 10px; text-decoration: none; transition: all 0.2s var(--ease); cursor: none; }
        .gpc-partner-card:hover { background: var(--grey-100); }
        .gpc-partner-logo { width: 100%; height: 56px; object-fit: contain; filter: grayscale(100%) opacity(0.7); transition: filter 0.3s; }
        .gpc-partner-card:hover .gpc-partner-logo { filter: grayscale(0%) opacity(1); }
        .gpc-partner-name { font-family: var(--font-p); font-size: 0.68rem; color: var(--grey-700); font-weight: 400; letter-spacing: .05em; text-align: center; }
        .gpc-partner-readmore { font-family: var(--font-p); font-size: 0.65rem; color: var(--black); font-weight: 400; letter-spacing: .1em; display: flex; align-items: center; gap: 5px; }
        .gpc-partner-readmore::before { content: '→'; }

        /* DOTS */
        .gpc-dots { display: flex; justify-content: center; gap: 6px; margin-top: 20px; }
        .gpc-dot { width: 7px; height: 7px; border-radius: 50%; background: var(--grey-300); cursor: none; transition: background 0.2s; }
        .gpc-dot.active { background: var(--black); }

        /* COOPERATION SECTION */
        .gpc-coop { padding: 80px; background: #fff; }
        .gpc-coop h2 { font-family: var(--font-p); font-size: clamp(1.6rem,2.5vw,2.2rem); font-weight: 700; color: var(--black); letter-spacing: .02em; text-align: center; margin-bottom: 44px; }
        .gpc-coop-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 16px; }
        .gpc-coop-card { position: relative; aspect-ratio: 16/11; overflow: hidden; text-decoration: none; display: block; cursor: none; }
        .gpc-coop-card img { width: 100%; height: 100%; object-fit: cover; filter: brightness(0.6) grayscale(20%); transition: transform 0.5s var(--ease), filter 0.4s; }
        .gpc-coop-card:hover img { transform: scale(1.06); filter: brightness(0.5) grayscale(0%); }
        .gpc-coop-label { position: absolute; bottom: 0; left: 0; right: 0; padding: 14px 16px 16px; background: linear-gradient(0deg, rgba(0,0,0,0.75) 0%, transparent 100%); }
        .gpc-coop-label-text { font-family: var(--font-p); font-size: 0.75rem; color: rgba(255,255,255,0.8); font-weight: 300; letter-spacing: .06em; text-transform: uppercase; display: block; margin-bottom: 6px; }
        .gpc-coop-btn { display: inline-block; padding: 7px 16px; border: 1px solid rgba(255,255,255,0.5); color: #fff; font-family: var(--font-p); font-size: 0.65rem; font-weight: 400; letter-spacing: .12em; text-transform: uppercase; transition: background 0.2s, border-color 0.2s; }
        .gpc-coop-card:hover .gpc-coop-btn { background: rgba(255,255,255,0.15); border-color: #fff; }

        /* ── COOPERATION MODAL ─────────────────────────────────────────────── */
        #coopModal {
            position: fixed; inset: 0; z-index: 6000;
            display: none; align-items: center; justify-content: center;
            padding: 20px;
        }
        #coopModalOverlay {
            position: absolute; inset: 0;
            background: rgba(0,0,0,0.75);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            cursor: none;
        }
        #coopModalBox {
            position: relative; z-index: 1;
            background: #fff;
            width: min(880px, 92vw);
            max-height: 88vh;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            animation: modalIn .4s cubic-bezier(0.16,1,0.3,1);
        }
        #coopModalBox::-webkit-scrollbar { width: 3px; }
        #coopModalBox::-webkit-scrollbar-thumb { background: var(--grey-300); }
        @keyframes modalIn {
            from { opacity: 0; transform: translateY(32px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0)   scale(1); }
        }
        #coopModalClose {
            position: absolute; top: 18px; right: 18px; z-index: 2;
            width: 38px; height: 38px; border-radius: 50%;
            background: rgba(0,0,0,0.55); color: #fff;
            border: none; font-size: 14px; line-height: 1;
            display: flex; align-items: center; justify-content: center;
            cursor: none; transition: background .2s var(--ease), transform .2s var(--ease);
        }
        #coopModalClose:hover { background: rgba(0,0,0,0.90); transform: scale(1.08); }
        #coopModalImgWrap { width: 100%; overflow: hidden; max-height: 440px; flex-shrink: 0; }
        #coopModalImgWrap img {
            width: 100%; height: 100%; max-height: 440px;
            object-fit: cover; display: block;
            transition: opacity .35s;
        }
        #coopModalBody { padding: 40px 48px 52px; }
        #coopModalTitle {
            font-family: var(--font-p);
            font-size: clamp(1.5rem, 2.8vw, 2.1rem);
            font-weight: 700; color: var(--black);
            letter-spacing: .01em; line-height: 1.1;
            margin-bottom: 20px;
        }
        #coopModalDivider {
            width: 40px; height: 2px;
            background: var(--black);
            margin-bottom: 22px;
        }
        #coopModalDesc {
            font-family: var(--font-p);
            font-size: 0.9rem; font-weight: 300;
            color: var(--grey-700);
            line-height: 1.95;
            margin-bottom: 32px;
            white-space: pre-line;
        }
        #coopModalLink {
            display: inline-flex; align-items: center; gap: 10px;
            padding: 13px 30px;
            background: var(--black); color: #fff;
            font-family: var(--font-p); font-size: 0.72rem;
            font-weight: 400; letter-spacing: .12em;
            text-transform: uppercase; text-decoration: none;
            transition: background .2s var(--ease), letter-spacing .25s var(--ease);
            cursor: none;
        }
        #coopModalLink:hover { background: #333; letter-spacing: .18em; }
        #coopModalLink .modal-arrow { font-size: 1rem; transition: transform .22s var(--ease); }
        #coopModalLink:hover .modal-arrow { transform: translateX(4px); }

        /* NOTE */
        .gpc-note { padding: 32px 80px; background: var(--grey-100); border-top: 1px solid var(--grey-200); }
        .gpc-note h3 { font-family: var(--font-p); font-size: 0.6rem; font-weight: 400; letter-spacing: .2em; text-transform: uppercase; color: var(--grey-500); margin-bottom: 8px; }
        .gpc-note p { font-family: var(--font-p); font-size: 0.78rem; color: var(--grey-500); line-height: 1.7; max-width: 720px; font-weight: 300; }

        /* REVEAL */
        .reveal-item { opacity: 0; transform: translateY(20px); transition: opacity 0.6s var(--ease), transform 0.6s var(--ease); }
        .reveal-item.reveal-done { opacity: 1; transform: translateY(0); }

        /* RESPONSIVE */
        @media (max-width: 1100px) {
            .gpc-hero-content, .gpc-breadcrumb, .gpc-intro,
            .gpc-partners, .gpc-coop, .gpc-note { padding-left: 40px; padding-right: 40px; }
            .gpc-partners-track { grid-template-columns: repeat(4,1fr); }
            .gpc-coop-grid { grid-template-columns: repeat(2,1fr); }
        }
        @media (max-width: 768px) {
            .gpc-hero-content, .gpc-breadcrumb, .gpc-intro,
            .gpc-partners, .gpc-coop, .gpc-note { padding-left: 20px; padding-right: 20px; }
            .gpc-hero h1 { font-size: 1.9rem; }
            .gpc-partners-track { grid-template-columns: repeat(2,1fr); }
            .gpc-coop-grid { grid-template-columns: 1fr; }
            html { cursor: auto; }
            #cursor-dot, #cursor-ring { display: none; }
            .gpc-partners-nav-btn, .gpc-coop-card,
            #coopModalClose, #coopModalOverlay,
            #coopModalLink { cursor: pointer !important; }
            #coopModalBody { padding: 28px 24px 36px; }
        }
        @media (max-width: 480px) {
            #coopModalBox { max-height: 94vh; }
            #coopModalImgWrap { max-height: 240px; }
        }
    </style>
</head>
<body>

<div id="cursor-dot"></div>
<div id="cursor-ring"></div>

<div id="intro">
    <div class="c-panel l"></div>
    <div id="intro-logo"><img src="/lending_word/public/assets/images/porsche-logo.png" alt="Porsche"></div>
    <div class="c-panel r"></div>
</div>

<div id="progress"></div>

<!-- NAVBAR -->
<nav class="navbar" id="navbar" style="position:fixed;top:0;left:0;right:0;z-index:500;">
    <div class="navbar-container">
        <a href="/lending_word/" class="navbar-brand">
            <img src="/lending_word/public/assets/images/porsche-logo2-png_seeklogo-314112-removebg-preview.png"
                 style="height:70px;filter:brightness(0) invert(1);" id="navLogo">
        </a>
        <ul class="navbar-menu">
            <?php foreach ($navbarLinks as $item): ?>
            <li>
                <a href="/lending_word/<?= htmlspecialchars($item['url']) ?>" style="color:#fff;" class="nav-link">
                    <?= htmlspecialchars($item['label']) ?>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
</nav>

<!-- HERO -->
<section class="gpc-hero">
    <div class="gpc-hero-bg" id="heroBg"
         style="background-image:url('<?= $gpc->getRawContent('hero_image','https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=1920&q=80') ?>')"></div>
    <div class="gpc-hero-content">
        <div class="gpc-hero-eyebrow">Porsche Indonesia</div>
        <h1><?= $gpc->getContent('hero_title', 'Global Partnership Council') ?></h1>
        <span class="gpc-hero-sub"><?= $gpc->getContent('hero_subtitle', 'Your cooperation opportunities') ?></span>
    </div>
</section>

<!-- BREADCRUMB -->
<div class="gpc-breadcrumb">
    <span>
        <a href="/lending_word/">Home</a>
        &nbsp;/&nbsp;
        <?= $gpc->getContent('hero_title', 'Global Partnership Council') ?>
    </span>
</div>

<!-- INTRO -->
<section class="gpc-intro">
    <div class="gpc-intro-inner reveal-item">
        <h2><?= $gpc->getContent('intro_title', 'The best way to write success stories? As a team.') ?></h2>
        <p><?= $gpc->getContent('intro_body', '') ?></p>
    </div>
</section>

<!-- PARTNERS -->
<?php if (!empty($partners)): ?>
<section class="gpc-partners">
    <div class="gpc-partners-hd reveal-item">
        <h2><?= $gpc->getContent('partners_title', 'Partners') ?></h2>
        <?php if (count($partners) > 5): ?>
        <div class="gpc-partners-nav">
            <button class="gpc-partners-nav-btn" id="partnersPrev"><i class="fas fa-chevron-left"></i></button>
            <button class="gpc-partners-nav-btn" id="partnersNext"><i class="fas fa-chevron-right"></i></button>
        </div>
        <?php endif; ?>
    </div>

    <?php
    $perPage    = 10;
    $pages      = array_chunk($partners, $perPage);
    $totalPages = count($pages);
    ?>

    <?php foreach ($pages as $pi => $pagePartners): ?>
    <div class="gpc-partners-track reveal-item" id="partnersPage<?= $pi ?>"
         style="<?= $pi > 0 ? 'display:none;' : '' ?>">
        <?php foreach ($pagePartners as $p): ?>
        <a class="gpc-partner-card" href="<?= htmlspecialchars($p['link_url'] ?? '#') ?>" target="_blank" rel="noopener">
            <?php if (!empty($p['logo_url'])): ?>
            <img class="gpc-partner-logo"
                 src="<?= htmlspecialchars($p['logo_url']) ?>"
                 alt="<?= htmlspecialchars($p['name']) ?>"
                 onerror="this.style.display='none'">
            <?php endif; ?>
            <div class="gpc-partner-name"><?= htmlspecialchars($p['name']) ?></div>
            <span class="gpc-partner-readmore">Read more</span>
        </a>
        <?php endforeach; ?>
    </div>
    <?php endforeach; ?>

    <?php if ($totalPages > 1): ?>
    <div class="gpc-dots" id="partnersDots">
        <?php for ($i = 0; $i < $totalPages; $i++): ?>
        <div class="gpc-dot <?= $i===0?'active':'' ?>" data-page="<?= $i ?>"></div>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</section>
<?php endif; ?>

<!-- COOPERATION -->
<?php if (!empty($cooperations)): ?>
<section class="gpc-coop">
    <h2 class="reveal-item"><?= $gpc->getContent('coop_title', 'Your cooperation opportunities') ?></h2>
    <div class="gpc-coop-grid">
        <?php foreach ($cooperations as $i => $c): ?>
        <a class="gpc-coop-card reveal-item"
           href="#"
           data-title="<?= htmlspecialchars($c['title']) ?>"
           data-desc="<?= htmlspecialchars($c['description'] ?? '') ?>"
           data-img="<?= htmlspecialchars($c['image_url'] ?? '') ?>"
           data-link="<?= htmlspecialchars($c['link_url'] ?? '#') ?>"
           style="transition-delay:<?= ($i % 3) * 0.08 ?>s;">
            <img src="<?= htmlspecialchars($c['image_url'] ?? '') ?>"
                 alt="<?= htmlspecialchars($c['title']) ?>"
                 loading="lazy"
                 onerror="this.src='https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600&q=80';this.onerror=null;">
            <div class="gpc-coop-label">
                <span class="gpc-coop-label-text"><?= htmlspecialchars($c['title']) ?></span>
                <span class="gpc-coop-btn">Read more</span>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
</section>

<!-- COOPERATION MODAL -->
<div id="coopModal" role="dialog" aria-modal="true" aria-labelledby="coopModalTitle">
    <div id="coopModalOverlay"></div>
    <div id="coopModalBox">
        <button id="coopModalClose" aria-label="Close modal">&#x2715;</button>
        <div id="coopModalImgWrap"></div>
        <div id="coopModalBody">
            <h2 id="coopModalTitle"></h2>
            <div id="coopModalDivider"></div>
            <p id="coopModalDesc"></p>
            <a id="coopModalLink" href="#" target="_blank" rel="noopener">
                Learn more <span class="modal-arrow">→</span>
            </a>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- NOTE -->
<section class="gpc-note">
    <h3>Note</h3>
    <p><?= $gpc->getContent('note_text', 'For reasons of better readability and without any intention of discrimination, only the masculine pronoun is used in this information. This includes all genders.') ?></p>
</section>

<?php include __DIR__ . '/footer.php'; ?>

<script>
/* ── INTRO ─────────────────────────────────────────────────────────────────── */
(function(){
    const intro = document.getElementById('intro');
    intro.style.display = 'flex';
    setTimeout(() => {
        intro.classList.add('open');
        setTimeout(() => {
            intro.classList.add('done');
            setTimeout(() => { intro.style.display = 'none'; }, 600);
        }, 1150);
    }, 900);
})();

/* ── CURSOR ─────────────────────────────────────────────────────────────────── */
const dot = document.getElementById('cursor-dot'), ring = document.getElementById('cursor-ring');
let mx=0,my=0,rx=0,ry=0;
window.addEventListener('mousemove',e=>{mx=e.clientX;my=e.clientY;},{passive:true});
document.addEventListener('mousedown',()=>{ document.body.classList.add('c-click'); setTimeout(()=>document.body.classList.remove('c-click'),280); });
document.documentElement.addEventListener('mouseleave',()=>{dot.style.opacity='0';ring.style.opacity='0';});
document.documentElement.addEventListener('mouseenter',()=>{dot.style.opacity='';ring.style.opacity='';});
(function tick(){ rx+=(mx-rx)*.16; ry+=(my-ry)*.16; dot.style.left=mx+'px'; dot.style.top=my+'px'; ring.style.left=rx+'px'; ring.style.top=ry+'px'; requestAnimationFrame(tick); })();
function registerCursorHover(el) {
    el.addEventListener('mouseenter',()=>document.body.classList.add('c-link'));
    el.addEventListener('mouseleave',()=>document.body.classList.remove('c-link'));
}
document.querySelectorAll('a,button,input,label').forEach(registerCursorHover);

/* ── SCROLL PROGRESS ────────────────────────────────────────────────────────── */
const progressEl = document.getElementById('progress');
window.addEventListener('scroll',()=>{ progressEl.style.width=(window.scrollY/(document.body.scrollHeight-window.innerHeight)*100)+'%'; },{passive:true});

/* ── NAVBAR ─────────────────────────────────────────────────────────────────── */
const navbar    = document.getElementById('navbar');
const navLogo   = document.getElementById('navLogo');
const navLinks  = document.querySelectorAll('.nav-link');
window.addEventListener('scroll',()=>{
    if(window.scrollY>60){
        navbar.classList.add('scrolled');
        navLogo.style.filter='brightness(0)';
        navLinks.forEach(l=>l.style.color='#0a0a0a');
    } else {
        navbar.classList.remove('scrolled');
        navLogo.style.filter='brightness(0) invert(1)';
        navLinks.forEach(l=>l.style.color='#fff');
    }
},{passive:true});

/* ── HERO BG ─────────────────────────────────────────────────────────────────── */
setTimeout(()=>document.getElementById('heroBg').classList.add('loaded'),100);

/* ── PARTNERS PAGINATION ────────────────────────────────────────────────────── */
let currentPartnersPage = 0;
const partnerPages = document.querySelectorAll('[id^="partnersPage"]');
const dots = document.querySelectorAll('.gpc-dot');

function showPartnersPage(n) {
    if (!partnerPages.length) return;
    partnerPages.forEach((p,i) => p.style.display = i === n ? '' : 'none');
    dots.forEach((d,i) => d.classList.toggle('active', i === n));
    currentPartnersPage = n;
}

const prevBtn = document.getElementById('partnersPrev');
const nextBtn = document.getElementById('partnersNext');
if (prevBtn) prevBtn.addEventListener('click', () => {
    showPartnersPage((currentPartnersPage - 1 + partnerPages.length) % partnerPages.length);
});
if (nextBtn) nextBtn.addEventListener('click', () => {
    showPartnersPage((currentPartnersPage + 1) % partnerPages.length);
});
dots.forEach(d => d.addEventListener('click', () => showPartnersPage(+d.dataset.page)));

/* ── COOPERATION MODAL ──────────────────────────────────────────────────────── */
const modal         = document.getElementById('coopModal');
const modalOverlay  = document.getElementById('coopModalOverlay');
const modalClose    = document.getElementById('coopModalClose');
const modalImgWrap  = document.getElementById('coopModalImgWrap');
const modalTitle    = document.getElementById('coopModalTitle');
const modalDesc     = document.getElementById('coopModalDesc');
const modalLink     = document.getElementById('coopModalLink');

function openCoopModal(card) {
    const title = card.dataset.title || '';
    const desc  = card.dataset.desc  || '';
    const img   = card.dataset.img   || '';
    const link  = card.dataset.link  || '#';

    modalTitle.textContent = title;
    modalDesc.textContent  = desc;

    if (img) {
        modalImgWrap.innerHTML = `<img src="${img}" alt="${title.replace(/"/g,'&quot;')}" onerror="this.parentElement.style.display='none'">`;
        modalImgWrap.style.display = '';
    } else {
        modalImgWrap.innerHTML = '';
        modalImgWrap.style.display = 'none';
    }

    if (link && link !== '#') {
        modalLink.href = link;
        modalLink.style.display = '';
    } else {
        modalLink.style.display = 'none';
    }

    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    // Scroll modal to top on reopen
    const box = document.getElementById('coopModalBox');
    if (box) box.scrollTop = 0;
}

function closeCoopModal() {
    modal.style.display = 'none';
    document.body.style.overflow = '';
}

document.querySelectorAll('.gpc-coop-card').forEach(card => {
    card.addEventListener('click', e => {
        e.preventDefault();
        openCoopModal(card);
    });
});

if (modalOverlay) modalOverlay.addEventListener('click', closeCoopModal);
if (modalClose)   modalClose.addEventListener('click',   closeCoopModal);
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeCoopModal(); });

// Register cursor on modal interactive elements
[modalOverlay, modalClose, modalLink].forEach(el => { if(el) registerCursorHover(el); });

/* ── SCROLL REVEAL ──────────────────────────────────────────────────────────── */
const obs = new IntersectionObserver(entries => {
    entries.forEach(e => {
        if(e.isIntersecting){ e.target.classList.add('reveal-done'); obs.unobserve(e.target); }
    });
}, {threshold: 0.08});
document.querySelectorAll('.reveal-item').forEach(el => obs.observe(el));
</script>   
</body>
</html>