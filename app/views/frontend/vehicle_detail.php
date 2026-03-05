<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($vehicle['title']) ?> - Porsche Finder</title>
    <link rel="icon" type="image/png" href="/lending_word/public/assets/images/porsche-logo.png">
    <link rel="stylesheet" href="/lending_word/public/assets/css/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="/lending_word/public/assets/js/saved-vehicles.js"></script>
    <style>
        @font-face {
            font-family: "Porsche Next";
            src: url("/lending_word/public/assets/fonts/Porsche Next.ttf") format("truetype");
            font-weight: 100 900;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: "Porsche Next";
            src: url("/lending_word/public/assets/fonts/Porsche Next.ttf") format("truetype");
            font-weight: 100 900;
            font-style: italic;
            font-display: swap;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --white:     #ffffff;
            --off:       #f6f6f3;
            --black:     #0a0a0a;
            --gray:      #888;
            --light:     #e6e6e0;
            --ease:      cubic-bezier(0.16, 1, 0.3, 1);
            --ease-back: cubic-bezier(0.34, 1.56, 0.64, 1);
            --font-body: 'Porsche Next', Arial, sans-serif;
            --font-cond: 'Porsche Next', Arial, sans-serif;
        }

        html { cursor: none; scroll-behavior: smooth; }
        body { background: var(--white); color: var(--black); font-family: var(--font-body); font-weight: 300; overflow-x: hidden; }

        /* ── CURSOR ── */
        #cursor-dot, #cursor-ring {
            position: fixed;
            pointer-events: none;
            z-index: 9999;
            border-radius: 50%;
            top: 0; left: 0;
            transform: translate(-50%, -50%);
            will-change: left, top;
            transition: width .3s var(--ease), height .3s var(--ease),
                        opacity .25s ease, background .2s ease, border-color .2s ease;
        }
        #cursor-dot {
            width: 7px; height: 7px;
            background: var(--black);
        }
        #cursor-ring {
            width: 38px; height: 38px;
            border: 1.5px solid rgba(0,0,0,0.45);
            background: transparent;
        }

        /* Di area gelap (footer) */
        body.cursor-dark #cursor-dot  { background: #ffffff; }
        body.cursor-dark #cursor-ring { border-color: rgba(255,255,255,0.5); }

        /* Hover link */
        body.c-link #cursor-dot  { width: 52px; height: 52px; background: rgba(0,0,0,.07); }
        body.c-link #cursor-ring { opacity: 0; }
        body.cursor-dark.c-link #cursor-dot { background: rgba(255,255,255,.15); }

        /* Click */
        body.c-click #cursor-dot  { transform: translate(-50%,-50%) scale(2); opacity: 0; }
        body.c-click #cursor-ring { transform: translate(-50%,-50%) scale(1.4); opacity: 0; }

        /* ── CURSOR SAAT LIGHTBOX AKTIF ──
           Sembunyikan cursor custom, kembalikan cursor browser putih
           supaya tombol close/nav/thumb bisa diklik dengan jelas
        */
        body.lightbox-open #cursor-dot,
        body.lightbox-open #cursor-ring { opacity: 0 !important; }
        body.lightbox-open { cursor: default !important; }
        body.lightbox-open * { cursor: default !important; }
        body.lightbox-open .lightbox-close,
        body.lightbox-open .lightbox-nav,
        body.lightbox-open .lightbox-thumb { cursor: pointer !important; }

        /* ── PROGRESS ── */
        #progress { position: fixed; top: 0; left: 0; height: 2px; width: 0; background: var(--gray); z-index: 8000; transition: width .1s linear; }

        /* ── INTRO ── */
        #intro { position: fixed; inset: 0; z-index: 5000; display: flex; align-items: center; justify-content: center; background: var(--white); transition: opacity .5s ease .1s; }
        #intro.done { opacity: 0; pointer-events: none; }
        .c-panel { position: absolute; top: 0; bottom: 0; width: 50%; background: var(--white); z-index: 2; transition: transform 1.2s cubic-bezier(0.76, 0, 0.24, 1); }
        .c-panel.l { left: 0;  border-right: 1px solid rgba(0,0,0,0.08); }
        .c-panel.r { right: 0; border-left:  1px solid rgba(0,0,0,0.08); }
        #intro.open .c-panel.l { transform: translateX(-100%); }
        #intro.open .c-panel.r { transform: translateX(100%); }
        #intro-logo { position: relative; z-index: 1; opacity: 0; animation: wrdIn .6s .15s var(--ease) forwards; display: flex; align-items: center; justify-content: center; }
        #intro-logo img { width: clamp(80px,10vw,130px); height: auto; }
        @keyframes wrdIn  { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        /* ── NAVBAR ── */
        .navbar { background: transparent !important; transition: background .4s ease, box-shadow .4s ease; }
        .navbar.scrolled { background: rgba(255,255,255,0.92) !important; backdrop-filter: blur(16px); box-shadow: 0 1px 0 rgba(0,0,0,.07); }
        .navbar .navbar-brand, .navbar .navbar-menu a { color: var(--black) !important; filter: none !important; }
        .navbar-menu a::after { background: var(--black) !important; }

        /* ══ BACK ══ */
        .back-wrap { max-width: 1400px; margin: 0 auto; padding: 120px 60px 24px; opacity: 0; animation: fadeUp .7s 1.8s var(--ease) forwards; }
        .back-btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 11px 22px; background: var(--black); color: var(--white);
            text-decoration: none; font-family: var(--font-body);
            font-size: 0.75rem; font-weight: 500; letter-spacing: .15em;
            position: relative; overflow: hidden; transition: box-shadow .3s ease;
        }
        .back-btn::before { content: ''; position: absolute; inset: 0; background: rgba(255,255,255,.12); transform: scaleX(0); transform-origin: left; transition: transform .4s var(--ease); }
        .back-btn:hover::before { transform: scaleX(1); }
        .back-btn:hover { box-shadow: 0 8px 24px rgba(0,0,0,.22); color: var(--white); }
        .back-btn i, .back-btn span { position: relative; z-index: 1; }

        /* ══ GALLERY ══ */
        .gallery-hero { opacity: 0; animation: fadeUp .8s 2s var(--ease) forwards; }
        .gallery-container { max-width: 1400px; margin: 0 auto; padding: 0 60px; }

        .gallery-main { position: relative; margin-bottom: 12px; border-radius: 16px; overflow: hidden; }
        .gallery-badges { position: absolute; top: 20px; left: 20px; z-index: 10; display: flex; gap: 8px; }
        .badge { background: rgba(0,0,0,0.82); color: #fff; padding: 6px 16px; font-family: var(--font-body); font-size: 0.75rem; letter-spacing: .12em; border-radius: 999px; }

        .gallery-main-image { width: 100%; height: 600px; object-fit: cover; display: block; transition: transform .5s var(--ease); }
        .gallery-main:hover .gallery-main-image { transform: scale(1.015); }

        .gallery-open-btn {
            position: absolute; bottom: 20px; left: 20px;
            background: rgba(255,255,255,0.95); border: none;
            padding: 11px 22px;
            display: flex; align-items: center; gap: 8px;
            font-family: var(--font-body); font-size: 0.78rem; font-weight: 500; letter-spacing: .1em;
            transition: background .2s ease;
        }
        .gallery-open-btn:hover { background: #fff; }

        .gallery-thumbnails { display: grid; grid-template-columns: repeat(5,1fr); gap: 10px; }
        .gallery-thumb { height: 130px; width: 100%; object-fit: cover; border-radius: 10px; transition: all .35s var(--ease); border: 2px solid transparent; }
        .gallery-thumb:hover { opacity: .85; border-color: var(--black); transform: scale(1.03); }

        /* ══ DETAIL LAYOUT ══ */
        .detail-content { max-width: 1400px; margin: 0 auto; padding: 60px 60px 100px; display: grid; grid-template-columns: 1fr 380px; gap: 70px; opacity: 0; animation: fadeUp .8s 2.2s var(--ease) forwards; }

        .detail-eyebrow { font-family: var(--font-body); font-size: 10px; letter-spacing: .35em; color: var(--gray); margin-bottom: 14px; display: flex; align-items: center; gap: 12px; }
        .detail-eyebrow::before { content: ''; display: block; width: 28px; height: 1px; background: var(--gray); }

        .detail-main h1 { font-family: var(--font-cond); font-size: clamp(2rem,4vw,3.2rem); font-weight: 700; letter-spacing: .03em; line-height: 1.05; margin-bottom: 40px; }

        .color-specs { display: flex; gap: 28px; margin-bottom: 44px; flex-wrap: wrap; padding-bottom: 44px; border-bottom: 1px solid var(--light); }
        .color-spec { display: flex; align-items: center; gap: 14px; }
        .color-swatch { width: 44px; height: 44px; border-radius: 50%; border: 1px solid var(--light); flex-shrink: 0; box-shadow: 0 2px 8px rgba(0,0,0,.1); }
        .color-info h3 { font-family: var(--font-body); font-size: 9px; letter-spacing: .2em; color: var(--gray); margin-bottom: 5px; }
        .color-info p  { font-family: var(--font-body); font-size: 0.9rem; font-weight: 500; }
        .color-info small { font-family: var(--font-body); font-size: 0.82rem; color: var(--gray); }

        .specs-section { margin: 0 0 48px; }
        .specs-section-title { font-family: var(--font-body); font-size: 10px; letter-spacing: .3em; color: var(--gray); margin-bottom: 20px; }
        .specs-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 0; border: 1px solid var(--light); border-radius: 12px; overflow: hidden; }
        .spec-item { padding: 18px 22px; border-right: 1px solid var(--light); border-bottom: 1px solid var(--light); transition: background .3s ease; }
        .spec-item:hover { background: var(--off); }
        .spec-item:nth-child(3n) { border-right: none; }
        .spec-item:nth-last-child(-n+3) { border-bottom: none; }
        .spec-item h3 { font-family: var(--font-body); font-size: 9px; letter-spacing: .15em; color: var(--gray); margin-bottom: 7px; }
        .spec-item p  { font-family: var(--font-cond); font-size: 1.15rem; font-weight: 600; color: var(--black); letter-spacing: .02em; }

        .equipment-section { margin: 0 0 48px; padding-top: 44px; border-top: 1px solid var(--light); }
        .equipment-section h2 { font-family: var(--font-cond); font-size: 1.6rem; font-weight: 600; letter-spacing: .04em; margin-bottom: 24px; }
        .equipment-list { display: grid; gap: 0; }
        .equipment-item { display: flex; align-items: center; gap: 12px; font-family: var(--font-body); font-size: 0.88rem; color: rgba(0,0,0,.75); padding: 10px 0; border-bottom: 1px solid var(--light); }
        .equipment-item:last-child { border-bottom: none; }
        .equipment-item i { color: var(--black); font-size: 0.7rem; flex-shrink: 0; }

        .description-text { font-family: var(--font-body); font-size: 0.9rem; line-height: 1.8; color: rgba(0,0,0,.6); margin-top: 44px; padding-top: 44px; border-top: 1px solid var(--light); }

        /* ══ SIDEBAR ══ */
        .detail-sidebar { position: sticky; top: 100px; }

        .price-box { background: var(--white); border: 1px solid var(--light); border-radius: 16px; padding: 28px; margin-bottom: 16px; box-shadow: 0 2px 0 var(--light), 0 20px 50px rgba(0,0,0,.05); }
        .price { font-family: var(--font-cond); font-size: 2.2rem; font-weight: 600; letter-spacing: .02em; margin-bottom: 22px; color: var(--black); }

        .btn-contact {
            display: block; width: 100%; padding: 14px;
            background: var(--black); color: var(--white); border: none; border-radius: 6px;
            font-family: var(--font-body); font-size: 0.78rem; font-weight: 500; letter-spacing: .15em;
            text-align: center; text-decoration: none; margin-bottom: 10px;
            position: relative; overflow: hidden; transition: box-shadow .3s ease, transform .2s ease;
        }
        .btn-contact::before { content: ''; position: absolute; inset: 0; background: rgba(255,255,255,.12); transform: scaleX(0); transform-origin: left; transition: transform .4s var(--ease); }
        .btn-contact:hover::before { transform: scaleX(1); }
        .btn-contact:hover { box-shadow: 0 12px 28px rgba(0,0,0,.22); transform: translateY(-1px); color: var(--white); }

        .btn-save-detail {
            width: 100%; padding: 14px; background: var(--off); color: var(--black);
            border: 1.5px solid var(--light); border-radius: 6px;
            font-family: var(--font-body); font-size: 0.78rem; font-weight: 500; letter-spacing: .12em;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            position: relative; overflow: hidden; transition: border-color .2s ease, color .3s ease;
        }
        .btn-save-detail::before { content: ''; position: absolute; inset: 0; background: var(--black); transform: scaleY(0); transform-origin: bottom; transition: transform .35s var(--ease); }
        .btn-save-detail:hover { border-color: var(--black); color: var(--white); }
        .btn-save-detail:hover::before { transform: scaleY(1); }
        .btn-save-detail i, .btn-save-detail span { position: relative; z-index: 1; }
        .btn-save-detail.saved { background: #e8f5e9; border-color: #4caf50; color: #2e7d32; }

        .dealer-info { background: var(--off); border: 1px solid var(--light); border-radius: 16px; padding: 24px; }
        .dealer-info h3 { font-family: var(--font-cond); font-size: 1.1rem; font-weight: 600; letter-spacing: .03em; margin-bottom: 16px; }
        .dealer-detail { display: flex; flex-direction: column; gap: 10px; }
        .dealer-detail-item { display: flex; align-items: flex-start; gap: 10px; font-family: var(--font-body); font-size: 0.85rem; color: rgba(0,0,0,.65); }
        .dealer-detail-item i { color: var(--gray); margin-top: 2px; font-size: 0.8rem; flex-shrink: 0; }

        .stock-info { margin-top: 18px; padding-top: 18px; border-top: 1px solid var(--light); }
        .stock-item { display: flex; justify-content: space-between; margin-bottom: 8px; font-family: var(--font-body); font-size: 0.82rem; }
        .stock-item span:first-child { color: var(--gray); }
        .stock-item span:last-child { font-weight: 500; }

        /* ══ SIMILAR ══ */
        .similar-section { max-width: 1400px; margin: 0 auto 100px; padding: 60px 60px 0; border-top: 1px solid var(--light); }
        .similar-header { display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 36px; }
        .similar-eyebrow { font-family: var(--font-body); font-size: 10px; letter-spacing: .35em; color: var(--gray); margin-bottom: 10px; display: flex; align-items: center; gap: 12px; }
        .similar-eyebrow::before { content: ''; display: block; width: 28px; height: 1px; background: var(--gray); }
        .similar-section h2 { font-family: var(--font-cond); font-size: clamp(1.6rem,2.8vw,2.2rem); font-weight: 700; letter-spacing: .04em; margin: 0; }
        .similar-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 16px; }
        .similar-card {
            background: var(--white); border: 1px solid var(--light); border-radius: 14px;
            overflow: hidden; text-decoration: none; color: inherit;
            position: relative; display: flex; flex-direction: column;
            transition: box-shadow .4s ease, transform .4s var(--ease), border-color .3s ease;
        }
        .similar-card:hover { box-shadow: 0 20px 50px rgba(0,0,0,.1); transform: translateY(-5px); border-color: rgba(0,0,0,.1); }
        .similar-card-img-wrap { position: relative; overflow: hidden; }
        .similar-card-img-wrap img { width: 100%; height: 200px; object-fit: cover; display: block; transition: transform .55s var(--ease); }
        .similar-card:hover .similar-card-img-wrap img { transform: scale(1.06); }
        .similar-card-badge { position: absolute; top: 12px; left: 12px; background: rgba(0,0,0,0.72); backdrop-filter: blur(6px); color: #fff; font-family: var(--font-body); font-size: 0.62rem; letter-spacing: .15em; padding: 4px 10px; border-radius: 999px; }
        .similar-card-img-wrap::after { content: ''; position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.3) 0%, transparent 55%); opacity: 0; transition: opacity .4s ease; }
        .similar-card:hover .similar-card-img-wrap::after { opacity: 1; }
        .similar-card-info { padding: 16px 18px 18px; display: flex; flex-direction: column; gap: 6px; flex: 1; }
        .similar-card-meta { font-family: var(--font-body); font-size: 0.68rem; letter-spacing: .15em; color: var(--gray); }
        .similar-card h3 { font-family: var(--font-cond); font-size: 1.1rem; font-weight: 600; letter-spacing: .02em; line-height: 1.2; transition: letter-spacing .3s ease; }
        .similar-card:hover h3 { letter-spacing: .04em; }
        .similar-card-bottom { display: flex; align-items: center; justify-content: space-between; margin-top: auto; padding-top: 12px; border-top: 1px solid var(--light); }
        .similar-price { font-family: var(--font-cond); font-size: 1.1rem; font-weight: 600; letter-spacing: .01em; color: var(--black); }
        .similar-btn { display: inline-flex; align-items: center; gap: 5px; padding: 7px 14px; background: var(--black); color: var(--white); font-family: var(--font-body); font-size: 0.65rem; font-weight: 500; letter-spacing: .12em; border-radius: 4px; position: relative; overflow: hidden; transition: box-shadow .3s ease, transform .2s ease; white-space: nowrap; flex-shrink: 0; }
        .similar-btn::before { content: ''; position: absolute; inset: 0; background: rgba(255,255,255,.12); transform: scaleX(0); transform-origin: left; transition: transform .35s var(--ease); }
        .similar-card:hover .similar-btn::before { transform: scaleX(1); }
        .similar-card:hover .similar-btn { box-shadow: 0 6px 18px rgba(0,0,0,.25); transform: translateY(-1px); }

        /* ══ LIGHTBOX ══ */
        .lightbox {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.96); z-index: 10000;
            opacity: 0; transition: opacity .3s ease;
        }
        .lightbox.active { display: flex; opacity: 1; }
        .lightbox-content { position: relative; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; padding: 80px 90px 160px; }
        .lightbox-image { max-width: 100%; max-height: 100%; object-fit: contain; border-radius: 8px; }

        .lightbox-close {
            position: fixed; top: 20px; right: 20px; color: #fff;
            font-size: 28px; font-weight: 300;
            cursor: pointer !important;
            width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;
            background: rgba(255,255,255,.15); border-radius: 50%;
            transition: all .3s ease; z-index: 20000;
            border: 1px solid rgba(255,255,255,.2);
        }
        .lightbox-close:hover { background: rgba(255,255,255,.3); transform: rotate(90deg); }

        .lightbox-nav {
            position: fixed; top: 50%; transform: translateY(-50%);
            background: rgba(255,255,255,.15); color: #fff; border: 1px solid rgba(255,255,255,.2);
            width: 52px; height: 52px; display: flex; align-items: center; justify-content: center;
            font-size: 22px; cursor: pointer !important;
            border-radius: 50%; transition: background .3s ease;
            z-index: 20000;
        }
        .lightbox-nav:hover { background: rgba(255,255,255,.3); }
        .lightbox-prev { left: 20px; }
        .lightbox-next { right: 20px; }
        .lightbox-counter { position: absolute; bottom: 28px; left: 50%; transform: translateX(-50%); color: rgba(255,255,255,.7); font-family: var(--font-body); font-size: 0.8rem; letter-spacing: .1em; background: rgba(0,0,0,.5); padding: 8px 20px; border-radius: 999px; }
        .lightbox-thumbnails { position: absolute; bottom: 72px; left: 50%; transform: translateX(-50%); display: flex; gap: 8px; max-width: 80%; overflow-x: auto; padding: 8px 12px; background: rgba(0,0,0,.35); border-radius: 10px; scrollbar-width: thin; scrollbar-color: rgba(255,255,255,.2) transparent; }
        .lightbox-thumbnails::-webkit-scrollbar { height: 3px; }
        .lightbox-thumbnails::-webkit-scrollbar-thumb { background: rgba(255,255,255,.2); border-radius: 2px; }
        .lightbox-thumb { width: 72px; height: 54px; object-fit: cover; cursor: pointer !important; opacity: .5; border: 2px solid transparent; border-radius: 4px; flex-shrink: 0; transition: all .3s ease; }
        .lightbox-thumb:hover, .lightbox-thumb.active { opacity: 1; border-color: #fff; }

        @media (max-width: 1200px) {
            .detail-content { grid-template-columns: 1fr; }
            .detail-sidebar { position: static; }
            .specs-grid { grid-template-columns: repeat(2,1fr); }
            .similar-grid { grid-template-columns: repeat(2,1fr); }
        }
        @media (max-width: 768px) {
            .back-wrap, .gallery-container, .detail-content, .similar-section { padding-left: 24px; padding-right: 24px; }
            .gallery-thumbnails { grid-template-columns: repeat(3,1fr); }
            .similar-grid, .specs-grid { grid-template-columns: 1fr; }
            html { cursor: auto; }
            #cursor-dot, #cursor-ring { display: none; }
            .back-btn, .btn-contact, .btn-save-detail, .gallery-open-btn,
            .lightbox-close, .lightbox-nav, .gallery-thumb, .similar-card { cursor: pointer; }
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

<?php
require_once __DIR__ . '/../../helpers/pg_array_helper.php';
include __DIR__ . '/../partials/navbar.php';
?>

<div class="back-wrap">
    <a href="/lending_word/finder.php" class="back-btn">
        <i class="fas fa-arrow-left"></i>
        <span>Back to Vehicle Finder</span>
    </a>
</div>

<div class="gallery-hero">
    <div class="gallery-container">
        <div class="gallery-main">
            <div class="gallery-badges">
                <?php if (!empty($vehicle['audio_url']) || !empty($vehicle['video_url'])): ?><span class="badge">Sound</span><?php endif; ?>
                <span class="badge"><?= count($images) ?> Images</span>
            </div>
            <img src="<?= htmlspecialchars($vehicle['main_image_url']) ?>" alt="<?= htmlspecialchars($vehicle['title']) ?>" class="gallery-main-image">
            <?php if (!empty($images)): ?>
            <button class="gallery-open-btn"><i class="fas fa-th"></i> Open Gallery</button>
            <?php endif; ?>
        </div>
        <?php if (!empty($images)): ?>
        <div class="gallery-thumbnails">
            <?php foreach (array_slice($images, 0, 5) as $img): ?>
            <img src="<?= htmlspecialchars($img['image_url']) ?>" alt="<?= htmlspecialchars($img['caption'] ?? '') ?>" class="gallery-thumb">
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="detail-content">
    <div class="detail-main">
        <p class="detail-eyebrow"><?= htmlspecialchars($vehicle['condition']) ?></p>
        <h1><?= htmlspecialchars($vehicle['title']) ?></h1>

        <div class="color-specs">
            <?php if ($vehicle['exterior_color']): ?>
            <div class="color-spec">
                <?php $extHex = !empty($vehicle['exterior_color_hex']) ? htmlspecialchars($vehicle['exterior_color_hex']) : '#888888'; ?>
                <div class="color-swatch" style="background: <?= $extHex ?>; border-color: rgba(0,0,0,0.08);"></div>
                <div class="color-info"><h3>Exterior colour</h3><p><?= htmlspecialchars($vehicle['exterior_color']) ?></p></div>
            </div>
            <?php endif; ?>
            <?php if ($vehicle['interior_color']): ?>
            <div class="color-spec">
                <?php $intHex = !empty($vehicle['interior_color_hex']) ? htmlspecialchars($vehicle['interior_color_hex']) : '#111111'; ?>
                <div class="color-swatch" style="background: <?= $intHex ?>; border-color: rgba(0,0,0,0.08);"></div>
                <div class="color-info"><h3>Interior colour</h3><p><?= htmlspecialchars($vehicle['interior_color']) ?></p><?php if (!empty($vehicle['interior_material'])): ?><small><?= htmlspecialchars($vehicle['interior_material']) ?></small><?php endif; ?></div>
            </div>
            <?php endif; ?>
            <?php if (!empty($vehicle['roof_color'])): ?>
            <div class="color-spec">
                <div class="color-swatch black"></div>
                <div class="color-info"><h3>Roof colour</h3><p><?= htmlspecialchars($vehicle['roof_color']) ?></p></div>
            </div>
            <?php endif; ?>
        </div>

        <div class="specs-section">
            <p class="specs-section-title">Specifications</p>
            <div class="specs-grid">
                <?php if ($vehicle['model_year']): ?><div class="spec-item"><h3>Model Year</h3><p><?= htmlspecialchars($vehicle['model_year']) ?></p></div><?php endif; ?>
                <?php if ($vehicle['drive_type']): ?><div class="spec-item"><h3>Drivetrain</h3><p><?= htmlspecialchars($vehicle['drive_type']) ?></p></div><?php endif; ?>
                <?php if ($vehicle['fuel_type']): ?><div class="spec-item"><h3>Engine</h3><p><?= htmlspecialchars($vehicle['fuel_type']) ?></p></div><?php endif; ?>
                <?php if ($vehicle['transmission']): ?><div class="spec-item"><h3>Transmission</h3><p><?= htmlspecialchars($vehicle['transmission']) ?></p></div><?php endif; ?>
                <?php if ($vehicle['power_kw'] && $vehicle['power_hp']): ?><div class="spec-item"><h3>Maximum power</h3><p><?= $vehicle['power_kw'] ?> kW / <?= $vehicle['power_hp'] ?> hp</p></div><?php endif; ?>
                <?php if ($vehicle['acceleration_0_100']): ?><div class="spec-item"><h3>0–100 km/h</h3><p><?= $vehicle['acceleration_0_100'] ?> s</p></div><?php endif; ?>
                <?php if ($vehicle['top_speed']): ?><div class="spec-item"><h3>Top Speed</h3><p><?= $vehicle['top_speed'] ?> km/h</p></div><?php endif; ?>
                <?php if ($vehicle['mileage']): ?><div class="spec-item"><h3>Mileage</h3><p><?= number_format($vehicle['mileage'], 0, ',', '.') ?> km</p></div><?php endif; ?>
                <?php if ($vehicle['seats']): ?><div class="spec-item"><h3>Seats</h3><p><?= $vehicle['seats'] ?></p></div><?php endif; ?>
            </div>
        </div>

        <?php $equipmentArray = get_equipment_array($vehicle['equipment_highlights'] ?? null); ?>
        <?php if (!empty($equipmentArray)): ?>
        <div class="equipment-section">
            <h2>Vehicle Equipment</h2>
            <div class="equipment-list">
                <?php foreach ($equipmentArray as $feature): ?>
                <div class="equipment-item"><i class="fas fa-check"></i><span><?= htmlspecialchars($feature) ?></span></div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($vehicle['description'])): ?>
        <div class="description-text"><?= nl2br(htmlspecialchars($vehicle['description'])) ?></div>
        <?php endif; ?>
    </div>

    <div class="detail-sidebar">
        <div class="price-box">
            <div class="price">Rp <?= number_format($vehicle['price'], 0, ',', '.') ?></div>
            <a href="/lending_word/finder_contact.php?id=<?= $vehicle['id'] ?>" class="btn-contact">Contact Dealership</a>
            <button class="btn-save-detail btn-save" data-save-vehicle="<?= $vehicle['id'] ?>" data-vehicle-id="<?= $vehicle['id'] ?>">
                <i class="far fa-bookmark"></i><span>Save</span>
            </button>
        </div>
        <?php if (!empty($vehicle['center_name'])): ?>
        <div class="dealer-info">
            <h3><?= htmlspecialchars($vehicle['center_name']) ?></h3>
            <div class="dealer-detail">
                <?php if (!empty($vehicle['center_address'])): ?><div class="dealer-detail-item"><i class="fas fa-map-marker-alt"></i><span><?= nl2br(htmlspecialchars($vehicle['center_address'])) ?></span></div><?php endif; ?>
                <?php if (!empty($vehicle['center_phone'])): ?><div class="dealer-detail-item"><i class="fas fa-phone"></i><span><?= htmlspecialchars($vehicle['center_phone']) ?></span></div><?php endif; ?>
            </div>
            <?php if (!empty($vehicle['stock_number']) || !empty($vehicle['vin'])): ?>
            <div class="stock-info">
                <?php if (!empty($vehicle['stock_number'])): ?><div class="stock-item"><span>Stock Number</span><span><?= htmlspecialchars($vehicle['stock_number']) ?></span></div><?php endif; ?>
                <?php if (!empty($vehicle['vin'])): ?><div class="stock-item"><span>VIN</span><span><?= htmlspecialchars($vehicle['vin']) ?></span></div><?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($similarVehicles)): ?>
<div class="similar-section">
    <div class="similar-header">
        <div class="similar-header-left">
            <p class="similar-eyebrow">You may also like</p>
            <h2>Similar Vehicles</h2>
        </div>
    </div>
    <div class="similar-grid">
        <?php foreach ($similarVehicles as $similar): ?>
        <a href="/lending_word/finder_detail.php?id=<?= $similar['id'] ?>" class="similar-card">
            <div class="similar-card-img-wrap">
                <img src="<?= htmlspecialchars($similar['main_image_url']) ?>" alt="<?= htmlspecialchars($similar['title']) ?>">
                <?php if (!empty($similar['condition'])): ?>
                <span class="similar-card-badge"><?= htmlspecialchars($similar['condition']) ?></span>
                <?php endif; ?>
            </div>
            <div class="similar-card-info">
                <?php if (!empty($similar['model_year'])): ?>
                <div class="similar-card-meta"><?= htmlspecialchars($similar['model_year']) ?></div>
                <?php endif; ?>
                <h3><?= htmlspecialchars($similar['title']) ?></h3>
                <div class="similar-card-bottom">
                    <div class="similar-price">Rp <?= number_format($similar['price'], 0, ',', '.') ?></div>
                    <span class="similar-btn"><i class="fas fa-arrow-right"></i> View</span>
                </div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<div class="lightbox" id="lightbox">
    <span class="lightbox-close" id="lightboxClose">&times;</span>
    <button class="lightbox-nav lightbox-prev" id="lightboxPrev">&#10094;</button>
    <button class="lightbox-nav lightbox-next" id="lightboxNext">&#10095;</button>
    <div class="lightbox-content"><img src="" alt="" class="lightbox-image" id="lightboxImage"></div>
    <div class="lightbox-counter" id="lightboxCounter"></div>
    <div class="lightbox-thumbnails" id="lightboxThumbnails"></div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>

<script>
/* ─── Intro ─── */
(function() {
    const intro = document.getElementById('intro');
    intro.style.display = 'flex'; intro.style.opacity = '1';
    setTimeout(function() {
        intro.classList.add('open');
        setTimeout(function() {
            intro.classList.add('done');
            setTimeout(function() { intro.style.display = 'none'; }, 600);
        }, 1150);
    }, 900);
})();

/* ─── Cursor ─── */
const dot  = document.getElementById('cursor-dot');
const ring = document.getElementById('cursor-ring');
let mx = 0, my = 0, rx = 0, ry = 0;

window.addEventListener('mousemove', e => { mx = e.clientX; my = e.clientY; }, { passive: true });

(function tick() {
    rx += (mx - rx) * .16; ry += (my - ry) * .16;
    dot.style.left  = mx + 'px'; dot.style.top   = my + 'px';
    ring.style.left = rx + 'px'; ring.style.top  = ry + 'px';
    requestAnimationFrame(tick);
})();

/* Deteksi area gelap */
window.addEventListener('mousemove', () => {
    if (document.body.classList.contains('lightbox-open')) return;
    const el = document.elementFromPoint(mx, my);
    const onDark = el && (el.closest('footer') || el.closest('.lightbox'));
    document.body.classList.toggle('cursor-dark', !!onDark);
}, { passive: true });

/* Hover interaktif */
document.querySelectorAll('a, button, input, label').forEach(el => {
    el.addEventListener('mouseenter', () => { if (!document.body.classList.contains('lightbox-open')) document.body.classList.add('c-link'); });
    el.addEventListener('mouseleave', () => document.body.classList.remove('c-link'));
});

document.addEventListener('mousedown', () => document.body.classList.add('c-click'));
document.addEventListener('mouseup',   () => document.body.classList.remove('c-click'));

/* ─── Progress + navbar ─── */
const progressEl = document.getElementById('progress');
const navbar     = document.querySelector('.navbar');
window.addEventListener('scroll', () => {
    progressEl.style.width = (window.scrollY / (document.body.scrollHeight - window.innerHeight) * 100) + '%';
    navbar?.classList.toggle('scrolled', window.scrollY > 50);
}, { passive: true });

/* ─── Gallery data ─── */
const galleryImages = [
    { url: '<?= htmlspecialchars($vehicle['main_image_url']) ?>', alt: '<?= htmlspecialchars($vehicle['title']) ?>' }
    <?php if (!empty($images)): ?><?php foreach ($images as $img): ?>
    ,{ url: '<?= htmlspecialchars($img['image_url']) ?>', alt: '<?= htmlspecialchars($img['caption'] ?? $vehicle['title']) ?>' }
    <?php endforeach; ?><?php endif; ?>
];

let currentImageIndex = 0;

function openLightbox(i) {
    currentImageIndex = i;
    updateLightboxImage();
    document.getElementById('lightbox').classList.add('active');
    document.body.style.overflow = 'hidden';
    // Tambah class lightbox-open → CSS hide cursor custom, kembalikan cursor browser
    document.body.classList.add('lightbox-open');
    document.body.classList.remove('c-link', 'c-click', 'cursor-dark');
}

function closeLightbox() {
    document.getElementById('lightbox').classList.remove('active');
    document.body.style.overflow = 'auto';
    // Hapus class → cursor custom aktif kembali
    document.body.classList.remove('lightbox-open');
}

function changeImage(d) {
    currentImageIndex = (currentImageIndex + d + galleryImages.length) % galleryImages.length;
    updateLightboxImage();
}

function updateLightboxImage() {
    document.getElementById('lightboxImage').src = galleryImages[currentImageIndex].url;
    document.getElementById('lightboxImage').alt = galleryImages[currentImageIndex].alt;
    document.getElementById('lightboxCounter').textContent = `${currentImageIndex + 1} / ${galleryImages.length}`;
    const tc = document.getElementById('lightboxThumbnails');
    tc.innerHTML = '';
    galleryImages.forEach((img, i) => {
        const t = document.createElement('img');
        t.src = img.url; t.alt = img.alt;
        t.className = 'lightbox-thumb' + (i === currentImageIndex ? ' active' : '');
        t.onclick = () => { currentImageIndex = i; updateLightboxImage(); };
        tc.appendChild(t);
    });
}

document.addEventListener('keydown', e => {
    if (!document.getElementById('lightbox').classList.contains('active')) return;
    if (e.key === 'ArrowLeft')  changeImage(-1);
    if (e.key === 'ArrowRight') changeImage(1);
    if (e.key === 'Escape')     closeLightbox();
});

document.addEventListener('DOMContentLoaded', () => {
    const mi = document.querySelector('.gallery-main-image');
    const ob = document.querySelector('.gallery-open-btn');
    const lb = document.getElementById('lightbox');

    if (mi) mi.addEventListener('click', () => openLightbox(0));
    if (ob) ob.addEventListener('click', e => { e.stopPropagation(); openLightbox(0); });
    document.querySelectorAll('.gallery-thumb').forEach((t, i) => { t.addEventListener('click', () => openLightbox(i + 1)); });

    document.getElementById('lightboxClose')?.addEventListener('click', e => { e.stopPropagation(); closeLightbox(); });
    document.getElementById('lightboxPrev')?.addEventListener('click',  e => { e.stopPropagation(); changeImage(-1); });
    document.getElementById('lightboxNext')?.addEventListener('click',  e => { e.stopPropagation(); changeImage(1);  });
    if (lb) lb.addEventListener('click', e => { if (e.target === lb) closeLightbox(); });
});
</script>
</body>
</html>