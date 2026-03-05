<?php
$currentCondition = $_GET['condition'] ?? '';
$currentSeriesId  = $_GET['series_id']  ?? '';
$currentSortBy    = $_GET['sort_by']    ?? 'recommended';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Porsche Finder - New and Used Cars for Sale</title>
    <link rel="icon" type="image/png" href="/lending_word/public/assets/images/porsche-logo.png">
    <link rel="stylesheet" href="/lending_word/public/assets/css/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="/lending_word/public/assets/js/saved-vehicles.js"></script>
    <style>
        /* ================================================================
           PORSCHE NEXT FONT
        ================================================================ */
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

        /* ── Font Awesome override ── */
        .fa, .fas, .far, .fal, .fad, .fab,
        [class^="fa-"], [class*=" fa-"],
        .fa-solid, .fa-regular, .fa-light, .fa-brands,
        i[class*="fa"] {
            font-family: "Font Awesome 6 Free", "Font Awesome 6 Brands",
                         "Font Awesome 5 Free", "Font Awesome 5 Brands" !important;
            font-style: normal;
        }
        .fa-solid,   .fas { font-family: "Font Awesome 6 Free"   !important; font-weight: 900 !important; }
        .fa-regular, .far { font-family: "Font Awesome 6 Free"   !important; font-weight: 400 !important; }
        .fa-brands,  .fab { font-family: "Font Awesome 6 Brands" !important; font-weight: 400 !important; }

        *, *::before, *::after { box-sizing: border-box; }

        :root {
            --white:     #ffffff;
            --off:       #f6f6f3;
            --black:     #0a0a0a;
            --gray:      #888;
            --light:     #e6e6e0;
            --ease:      cubic-bezier(0.16, 1, 0.3, 1);
            --ease-back: cubic-bezier(0.34, 1.56, 0.64, 1);
            --font:      "Porsche Next", "Arial Narrow", Arial, "Helvetica Neue", Helvetica, sans-serif;
        }

        html { scroll-behavior: smooth; }

        body {
            background: var(--white);
            color: var(--black);
            font-family: var(--font);
            font-weight: 300;
            overflow-x: hidden;
            margin: 0;
        }

        /* ── CURSOR NONE SEMUA ── */
        * { cursor: none !important; }

        /* ── FIX KRITIS: Matikan page-transition-overlay dari style.css global ── */
        .page-transition-overlay {
            display: none !important;
            pointer-events: none !important;
            z-index: -9999 !important;
        }

        /* ── CUSTOM CURSOR ── */
        #cursor-dot, #cursor-ring {
            position: fixed;
            pointer-events: none;
            z-index: 9999;
            border-radius: 50%;
            top: 0; left: 0;
            transform: translate(-50%, -50%);
            will-change: left, top, transform;
            transition-property: width, height, opacity, transform;
            transition-timing-function: var(--ease);
            mix-blend-mode: difference;
        }
        #cursor-dot {
            width: 8px; height: 8px;
            background: #ffffff;
            transition-duration: .2s, .2s, .2s, .15s;
        }
        #cursor-ring {
            width: 38px; height: 38px;
            border: 1.5px solid #ffffff;
            background: transparent;
            transition-duration: .35s, .35s, .3s, .22s;
        }
        body.c-link #cursor-dot  { width: 5px; height: 5px; }
        body.c-link #cursor-ring { width: 54px; height: 54px; }
        body.c-card #cursor-dot  { width: 10px; height: 10px; }
        body.c-card #cursor-ring { width: 54px; height: 54px; }
        body.c-click #cursor-dot {
            transform: translate(-50%, -50%) scale(2.5);
            opacity: 0;
        }
        body.c-click #cursor-ring {
            transform: translate(-50%, -50%) scale(1.5);
            opacity: 0;
        }

        /* ── SCROLL PROGRESS ── */
        #progress {
            position: fixed; top: 0; left: 0;
            height: 2px; width: 0;
            background: var(--gray);
            z-index: 8000;
            transition: width .1s linear;
            pointer-events: none;
        }

        /* ── INTRO CURTAIN ── */
        #intro {
            position: fixed; inset: 0; z-index: 5000;
            display: flex; align-items: center; justify-content: center;
            background: var(--white);
            transition: opacity .5s ease .1s;
            pointer-events: all;
        }
        #intro.done {
            opacity: 0;
            pointer-events: none;
        }
        #intro[style*="display: none"] {
            pointer-events: none;
        }
        .c-panel {
            position: absolute; top: 0; bottom: 0; width: 50%;
            background: var(--white); z-index: 2;
            transition: transform 1.2s cubic-bezier(0.76, 0, 0.24, 1);
        }
        .c-panel.l { left: 0;  border-right: 1px solid rgba(0,0,0,0.08); }
        .c-panel.r { right: 0; border-left:  1px solid rgba(0,0,0,0.08); }
        #intro.open .c-panel.l { transform: translateX(-100%); }
        #intro.open .c-panel.r { transform: translateX(100%); }
        #intro-logo {
            position: relative; z-index: 1; opacity: 0;
            animation: wrdIn .6s .15s var(--ease) forwards;
            display: flex; align-items: center; justify-content: center;
        }
        #intro-logo img { width: clamp(80px, 10vw, 130px); height: auto; }
        @keyframes wrdIn {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── NAVBAR ── */
        .navbar {
            background: transparent !important;
            transition: background .4s ease, box-shadow .4s ease;
        }
        .navbar.scrolled {
            background: rgba(255,255,255,0.92) !important;
            backdrop-filter: blur(16px);
            box-shadow: 0 1px 0 rgba(0,0,0,.07);
        }
        .navbar .navbar-brand,
        .navbar .navbar-menu a {
            color: var(--black) !important;
            filter: none !important;
        }
        .navbar .navbar-brand img {
            filter: brightness(0) !important;
        }
        .navbar-menu a::after { background: var(--black) !important; }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ═══════════════════════════
           PAGE HEADER
        ═══════════════════════════ */
        .finder-header {
            padding: 180px 60px 60px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            border-bottom: 1px solid var(--light);
            opacity: 0;
            animation: fadeUp .8s 1.8s var(--ease) forwards;
        }
        .finder-eyebrow {
            font-family: var(--font);
            font-size: 10px;
            letter-spacing: .35em;
            text-transform: uppercase;
            color: var(--gray);
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .finder-eyebrow::before {
            content: '';
            display: block;
            width: 28px; height: 1px;
            background: var(--gray);
        }
        .finder-header h1 {
            font-family: var(--font);
            font-size: clamp(52px, 7vw, 96px);
            font-weight: 700;
            line-height: .95;
            letter-spacing: .02em;
            color: var(--black);
            margin: 0;
        }
        .finder-header-sub {
            font-family: var(--font);
            font-size: 1rem;
            color: var(--gray);
            font-weight: 300;
            margin: 14px 0 0;
            letter-spacing: .01em;
        }
        .saved-icon-link {
            position: relative;
            display: flex; align-items: center; justify-content: center;
            width: 48px; height: 48px;
            background: transparent;
            border: 1.5px solid var(--black);
            border-radius: 50%;
            color: var(--black);
            text-decoration: none;
            font-size: 1.2rem;
            transition: all 0.3s ease;
            flex-shrink: 0;
            pointer-events: all !important;
        }
        .saved-icon-link:hover { background: var(--black); color: var(--white); }
        .saved-count-badge {
            display: none;
            position: absolute; top: -6px; right: -6px;
            background: #d32f2f; color: #fff;
            border-radius: 50%; min-width: 22px; height: 22px;
            font-size: 0.7rem; font-weight: 700;
            align-items: center; justify-content: center;
            border: 2px solid #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        /* ═══════════════════════════
           LAYOUT
        ═══════════════════════════ */
        .finder-container {
            max-width: 1600px;
            margin: 0 auto;
            padding: 60px 60px 120px;
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 60px;
            align-items: start;
        }

        /* ═══════════════════════════
           SIDEBAR
        ═══════════════════════════ */
        .finder-sidebar {
            position: sticky;
            top: 100px;
            max-height: calc(100vh - 120px);
            overflow-y: auto;
            scrollbar-width: none;
            opacity: 0;
            animation: fadeUp .8s 2s var(--ease) forwards;
        }
        .finder-sidebar::-webkit-scrollbar { display: none; }
        .sidebar-inner {
            border: 1px solid var(--light);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 0 var(--light), 0 20px 50px rgba(0,0,0,.04);
        }
        .sidebar-title {
            font-family: var(--font);
            font-size: 10px;
            letter-spacing: .3em;
            text-transform: uppercase;
            color: var(--gray);
            padding: 22px 24px 10px;
        }
        .filter-group { border-top: 1px solid var(--light); }
        .filter-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: var(--font);
            font-size: 0.88rem;
            font-weight: 500;
            color: var(--black);
            padding: 16px 24px;
            letter-spacing: .02em;
            user-select: none;
            transition: background .2s ease;
        }
        .filter-header:hover { background: var(--off); }
        .filter-header i { font-size: 0.65rem; color: var(--gray); transition: transform 0.3s ease; }
        .filter-header.collapsed i { transform: rotate(180deg); }
        .filter-content {
            padding: 0 24px 18px;
            max-height: 1000px;
            overflow: hidden;
            transition: max-height 0.35s var(--ease), padding 0.35s ease;
        }
        .filter-content.hidden { max-height: 0; padding-top: 0; padding-bottom: 0; }
        .filter-option { display: flex; align-items: center; gap: 10px; padding: 6px 0; }
        .filter-option input[type="checkbox"],
        .filter-option input[type="radio"] {
            appearance: none;
            width: 15px; height: 15px;
            border: 1.5px solid var(--light);
            border-radius: 3px;
            flex-shrink: 0; position: relative;
            transition: border-color .2s, background .2s;
        }
        .filter-option input[type="radio"] { border-radius: 50%; }
        .filter-option input:checked { background: var(--black); border-color: var(--black); }
        .filter-option input[type="checkbox"]:checked::after {
            content: '';
            position: absolute; left: 3px; top: 1px;
            width: 5px; height: 8px;
            border: 1.5px solid #fff;
            border-top: none; border-left: none;
            transform: rotate(45deg);
        }
        .filter-option input[type="radio"]::after {
            content: '';
            position: absolute; inset: 3px;
            background: #fff; border-radius: 50%;
            transform: scale(0);
            transition: transform .25s var(--ease-back);
        }
        .filter-option input[type="radio"]:checked::after { transform: scale(1); }
        .filter-option label {
            flex: 1;
            font-family: var(--font);
            font-size: 0.85rem; color: rgba(0,0,0,.7);
            display: flex; justify-content: space-between; align-items: center;
            transition: color .2s;
        }
        .filter-option:hover label { color: var(--black); }
        .filter-option .count { color: var(--gray); font-size: 0.78rem; }
        .filter-option .info-icon { color: var(--gray); font-size: 0.78rem; margin-left: 5px; }
        .dropdown-select {
            width: 100%; padding: 9px 12px;
            border: 1.5px solid var(--light); border-radius: 6px;
            font-size: 0.85rem; font-family: var(--font);
            background: var(--white);
            color: var(--black); transition: border-color .2s; appearance: none;
        }
        .dropdown-select:focus { outline: none; border-color: var(--black); }
        .filter-label-sm {
            font-size: 0.75rem; font-family: var(--font);
            text-transform: uppercase; letter-spacing: .1em;
            color: var(--gray); margin-bottom: 6px; display: block;
        }
        .reset-btn {
            width: calc(100% - 48px); margin: 16px 24px 20px;
            padding: 11px; background: transparent;
            border: 1.5px solid var(--black); border-radius: 6px;
            font-family: var(--font); font-size: 11px;
            letter-spacing: .2em; text-transform: uppercase;
            color: var(--black);
            position: relative; overflow: hidden;
            transition: color .35s ease; display: block;
        }
        .reset-btn::before {
            content: ''; position: absolute; inset: 0;
            background: var(--black); transform: translateY(101%);
            transition: transform .35s var(--ease);
        }
        .reset-btn:hover { color: var(--white); }
        .reset-btn:hover::before { transform: translateY(0); }
        .reset-btn span { position: relative; z-index: 1; }

        /* ═══════════════════════════
           RESULTS
        ═══════════════════════════ */
        .finder-results { opacity: 0; animation: fadeUp .8s 2.1s var(--ease) forwards; }
        .results-header {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 36px; padding-bottom: 20px;
            border-bottom: 1px solid var(--light);
        }
        .results-count { font-family: var(--font); font-size: 0.88rem; color: var(--gray); }
        .results-count strong { color: var(--black); font-weight: 600; }
        .sort-controls { display: flex; align-items: center; gap: 12px; }
        .sort-controls label { font-family: var(--font); font-size: 0.85rem; color: var(--gray); }
        .sort-select {
            padding: 9px 36px 9px 12px;
            border: 1.5px solid var(--light); border-radius: 6px;
            font-size: 0.85rem; font-family: var(--font);
            background: var(--white) url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 12 12"><path d="M6 8L3 5h6z" fill="%23888"/></svg>') no-repeat right 12px center;
            appearance: none; min-width: 200px;
            color: var(--black); transition: border-color .2s;
        }
        .sort-select:focus { outline: none; border-color: var(--black); }
        .vehicle-grid { display: grid; gap: 20px; }

        /* ═══════════════════════════
           VEHICLE CARD
        ═══════════════════════════ */
        .vehicle-card {
            background: var(--white); border: 1px solid var(--light);
            border-radius: 16px; overflow: hidden;
            display: grid; grid-template-columns: 460px 1fr;
            min-height: 320px; position: relative;
            opacity: 0; transform: translateY(30px);
            transition: box-shadow .4s ease, transform .4s var(--ease), border-color .3s ease;
        }
        .vehicle-card.visible { opacity: 1; transform: translateY(0); }
        .vehicle-card::after {
            content: ''; position: absolute; inset: -1px; border-radius: 17px;
            background: linear-gradient(135deg, rgba(100,100,100,.2), rgba(0,0,0,.05)) border-box;
            border: 1px solid transparent;
            -webkit-mask: linear-gradient(#fff 0 0) padding-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: destination-out; mask-composite: exclude;
            opacity: 0; transition: opacity .4s ease; pointer-events: none;
        }
        .vehicle-card:hover {
            box-shadow: 0 18px 60px rgba(0,0,0,.09);
            transform: translateY(-4px);
            border-color: rgba(0,0,0,.08);
        }
        .vehicle-card:hover::after { opacity: 1; }
        .vehicle-image-wrapper { position: relative; background: var(--off); overflow: hidden; }
        .sound-badge {
            position: absolute; top: 18px; left: 18px;
            background: rgba(0,0,0,0.82); color: #fff;
            padding: 6px 16px; font-family: var(--font);
            font-size: 0.75rem; letter-spacing: .12em; text-transform: uppercase;
            border-radius: 999px; z-index: 2;
        }
        .vehicle-image {
            width: 100%; height: 100%; object-fit: cover;
            transition: transform 0.5s var(--ease);
        }
        .vehicle-card:hover .vehicle-image { transform: scale(1.04); }
        .vehicle-info { padding: 32px 36px; display: flex; flex-direction: column; }
        .vehicle-condition {
            font-family: var(--font); font-size: 10px; color: var(--gray);
            text-transform: uppercase; letter-spacing: .2em; margin-bottom: 10px;
        }
        .vehicle-title {
            font-family: var(--font);
            font-size: 1.75rem; font-weight: 600;
            margin-bottom: 16px; line-height: 1.2;
            color: var(--black); letter-spacing: .02em;
        }
        .vehicle-title a {
            color: inherit; text-decoration: none;
            transition: letter-spacing .3s ease;
        }
        .vehicle-card:hover .vehicle-title a { letter-spacing: .03em; }
        .vehicle-specs {
            display: flex; flex-wrap: wrap; gap: 4px 0; margin-bottom: 22px;
            font-family: var(--font); font-size: 0.85rem;
            color: var(--gray); line-height: 1.6;
        }
        .vehicle-specs span { display: flex; align-items: center; }
        .vehicle-specs span::after { content: "•"; margin: 0 10px; color: var(--light); }
        .vehicle-specs span:last-child::after { content: ""; margin: 0; }
        .vehicle-price {
            font-family: var(--font); font-size: 2rem; font-weight: 600;
            margin-bottom: 24px; margin-top: auto;
            color: var(--black); letter-spacing: .02em;
        }
        .vehicle-actions { display: flex; gap: 12px; }
        .btn-details {
            flex: 1; padding: 13px 22px;
            background: var(--black); color: var(--white);
            border: none; border-radius: 6px; font-family: var(--font);
            font-size: 0.78rem; font-weight: 500;
            letter-spacing: .15em; text-transform: uppercase;
            text-decoration: none; text-align: center;
            position: relative; overflow: hidden;
            transition: transform .2s ease, box-shadow .3s ease;
            pointer-events: all !important;
        }
        .btn-details::before {
            content: ''; position: absolute; inset: 0;
            background: rgba(255,255,255,.12);
            transform: scaleX(0); transform-origin: left;
            transition: transform .4s ease;
        }
        .btn-details:hover::before { transform: scaleX(1); }
        .btn-details:hover {
            box-shadow: 0 12px 28px rgba(0,0,0,.22);
            transform: translateY(-1px);
            color: var(--white);
        }
        .btn-save {
            padding: 13px 20px; background: var(--off);
            border: 1.5px solid var(--light); border-radius: 6px;
            display: flex; align-items: center; gap: 8px;
            font-family: var(--font); font-size: 0.78rem; font-weight: 500;
            letter-spacing: .1em; text-transform: uppercase; color: var(--black);
            position: relative; overflow: hidden;
            transition: border-color .2s ease, color .3s ease;
            pointer-events: all !important;
        }
        .btn-save::before {
            content: ''; position: absolute; inset: 0;
            background: var(--black); transform: scaleY(0);
            transform-origin: bottom; transition: transform .35s var(--ease);
        }
        .btn-save:hover { border-color: var(--black); color: var(--white); }
        .btn-save:hover::before { transform: scaleY(1); }
        .btn-save i, .btn-save span { position: relative; z-index: 1; }
        .vehicle-center {
            margin-top: 12px; font-family: var(--font);
            font-size: 0.78rem; color: var(--gray);
        }

        /* ═══════════════════════════
           NO RESULTS
        ═══════════════════════════ */
        .no-results {
            text-align: center; padding: 100px 20px;
            border: 1px solid var(--light); border-radius: 16px;
        }
        .no-results i { font-size: 3rem; color: var(--light); margin-bottom: 24px; display: block; }
        .no-results h3 {
            font-family: var(--font);
            font-size: 1.8rem; font-weight: 600;
            letter-spacing: .02em; margin-bottom: 10px;
        }
        .no-results p { color: var(--gray); font-family: var(--font); font-size: 0.9rem; }

        /* ═══════════════════════════
           FOOTER — pointer events tapi cursor none
        ═══════════════════════════ */
        footer {
            position: relative;
            z-index: 10;
            pointer-events: all !important;
        }
        footer * { pointer-events: all !important; }

        /* ═══════════════════════════
           RESPONSIVE
        ═══════════════════════════ */
        @media (max-width: 1200px) {
            .finder-container { grid-template-columns: 1fr; }
            .finder-sidebar { position: static; max-height: none; }
            .vehicle-card { grid-template-columns: 1fr; }
            .vehicle-image-wrapper { height: 280px; }
        }
        @media (max-width: 768px) {
            .finder-header { padding: 120px 24px 40px; flex-direction: column; align-items: flex-start; gap: 20px; }
            .finder-container { padding: 40px 24px 80px; }
            * { cursor: auto !important; }
            #cursor-dot, #cursor-ring { display: none !important; }
        }
    </style>
</head>
<body>

<!-- CURSOR -->
<div id="cursor-dot"></div>
<div id="cursor-ring"></div>

<!-- INTRO CURTAIN -->
<div id="intro">
    <div class="c-panel l"></div>
    <div id="intro-logo">
        <img src="/lending_word/public/assets/images/porsche-logo.png" alt="Porsche">
    </div>
    <div class="c-panel r"></div>
</div>

<!-- SCROLL PROGRESS -->
<div id="progress"></div>

<?php include __DIR__ . '/../partials/navbar.php'; ?>

<!-- PAGE HEADER -->
<div class="finder-header">
    <div>
        <p class="finder-eyebrow">Vehicle Finder</p>
        <h1>Porsche</h1>
        <p class="finder-header-sub">New and used cars for sale.</p>
    </div>
    <a href="/lending_word/saved_vehicles.php" class="saved-icon-link" title="Saved Vehicles">
        <i class="far fa-bookmark"></i>
        <span class="saved-count-badge">0</span>
    </a>
</div>

<div class="finder-container">

    <!-- SIDEBAR -->
    <aside class="finder-sidebar">
        <div class="sidebar-inner">
            <div class="sidebar-title">Filter</div>
            <form method="GET" action="" id="filterForm">

                <!-- Condition -->
                <div class="filter-group">
                    <div class="filter-header" onclick="toggleFilter(this)">
                        <span>Condition</span>
                        <i class="fas fa-chevron-up"></i>
                    </div>
                    <div class="filter-content">
                        <div class="filter-option">
                            <input type="checkbox" name="condition" value="New" id="condNew"
                                <?= $currentCondition === 'New' ? 'checked' : '' ?>>
                            <label for="condNew">
                                <span>New</span>
                                <span class="count"><?= count(array_filter($vehicles, fn($v) => $v['condition'] === 'New')) ?></span>
                            </label>
                        </div>
                        <div class="filter-option">
                            <input type="checkbox" name="condition" value="Used" id="condUsed"
                                <?= $currentCondition === 'Used' ? 'checked' : '' ?>>
                            <label for="condUsed">
                                <span>Used</span>
                                <span class="count"><?= count(array_filter($vehicles, fn($v) => $v['condition'] === 'Used')) ?></span>
                            </label>
                        </div>
                        <div class="filter-option">
                            <input type="checkbox" name="approved" value="1" id="condApproved">
                            <label for="condApproved">
                                <span>Porsche Approved Pre-Owned</span>
                                <i class="fas fa-info-circle info-icon" title="Certified pre-owned vehicles"></i>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Model Series -->
                <div class="filter-group">
                    <div class="filter-header" onclick="toggleFilter(this)">
                        <span>Model Series</span>
                        <i class="fas fa-chevron-up"></i>
                    </div>
                    <div class="filter-content">
                        <select name="series_id" class="dropdown-select" onchange="applyFilters()">
                            <option value="">All Models</option>
                            <?php foreach ($modelSeries as $series): ?>
                            <option value="<?= $series['id'] ?>" <?= $currentSeriesId == $series['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($series['name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Model Variants -->
                <div class="filter-group">
                    <div class="filter-header collapsed" onclick="toggleFilter(this)">
                        <span>Model Variants</span>
                        <i class="fas fa-chevron-up"></i>
                    </div>
                    <div class="filter-content hidden">
                        <p style="color: var(--gray); font-size: 0.82rem; font-family: var(--font);">Select a model series first</p>
                    </div>
                </div>

                <!-- Body Type -->
                <div class="filter-group">
                    <div class="filter-header collapsed" onclick="toggleFilter(this)">
                        <span>Body Type</span>
                        <i class="fas fa-chevron-up"></i>
                    </div>
                    <div class="filter-content hidden">
                        <?php foreach ($bodyTypes as $bodyType): ?>
                        <div class="filter-option">
                            <input type="checkbox" name="body_type_id[]"
                                value="<?= $bodyType['id'] ?>" id="body<?= $bodyType['id'] ?>">
                            <label for="body<?= $bodyType['id'] ?>"><?= htmlspecialchars($bodyType['name']) ?></label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Engine & Transmission -->
                <div class="filter-group">
                    <div class="filter-header collapsed" onclick="toggleFilter(this)">
                        <span>Engine &amp; Transmission</span>
                        <i class="fas fa-chevron-up"></i>
                    </div>
                    <div class="filter-content hidden">
                        <?php if (!empty($filterOptions['fuel_types'])): ?>
                        <span class="filter-label-sm">Fuel Type</span>
                        <?php foreach ($filterOptions['fuel_types'] as $fuelType): ?>
                        <div class="filter-option">
                            <input type="checkbox" name="fuel_type[]"
                                value="<?= htmlspecialchars($fuelType) ?>" id="fuel<?= md5($fuelType) ?>">
                            <label for="fuel<?= md5($fuelType) ?>"><?= htmlspecialchars($fuelType) ?></label>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Price -->
                <div class="filter-group">
                    <div class="filter-header collapsed" onclick="toggleFilter(this)">
                        <span>Price</span>
                        <i class="fas fa-chevron-up"></i>
                    </div>
                    <div class="filter-content hidden">
                        <div style="margin-bottom: 12px;">
                            <span class="filter-label-sm">Min Price</span>
                            <input type="number" name="min_price" class="dropdown-select"
                                placeholder="Rp 0" step="1000000">
                        </div>
                        <div>
                            <span class="filter-label-sm">Max Price</span>
                            <input type="number" name="max_price" class="dropdown-select"
                                placeholder="Any" step="1000000">
                        </div>
                    </div>
                </div>

                <!-- Porsche Center -->
                <div class="filter-group">
                    <div class="filter-header collapsed" onclick="toggleFilter(this)">
                        <span>Porsche Center</span>
                        <i class="fas fa-chevron-up"></i>
                    </div>
                    <div class="filter-content hidden">
                        <select name="center_id" class="dropdown-select" onchange="applyFilters()">
                            <option value="">All Centers</option>
                            <?php foreach ($porscheCenters as $center): ?>
                            <option value="<?= $center['id'] ?>">
                                <?= htmlspecialchars($center['name']) ?> – <?= htmlspecialchars($center['city']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <button type="button" class="reset-btn" onclick="resetFilters()">
                    <span>Reset Filters</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- RESULTS -->
    <main class="finder-results">
        <div class="results-header">
            <div class="results-count">
                <strong><?= count($vehicles) ?></strong> vehicles found
            </div>
            <div class="sort-controls">
                <label for="sortBy">Sort by</label>
                <select id="sortBy" class="sort-select" onchange="changeSort(this.value)">
                    <option value="recommended" <?= $currentSortBy === 'recommended' ? 'selected' : '' ?>>Recommended</option>
                    <option value="price_asc"   <?= $currentSortBy === 'price_asc'   ? 'selected' : '' ?>>Price: Low to High</option>
                    <option value="price_desc"  <?= $currentSortBy === 'price_desc'  ? 'selected' : '' ?>>Price: High to Low</option>
                    <option value="year_desc"   <?= $currentSortBy === 'year_desc'   ? 'selected' : '' ?>>Year: Newest First</option>
                    <option value="year_asc"    <?= $currentSortBy === 'year_asc'    ? 'selected' : '' ?>>Year: Oldest First</option>
                </select>
            </div>
        </div>

        <?php if (empty($vehicles)): ?>
        <div class="no-results">
            <i class="far fa-search"></i>
            <h3>No vehicles found</h3>
            <p>Try adjusting your filters to see more results.</p>
        </div>
        <?php else: ?>
        <div class="vehicle-grid">
            <?php foreach ($vehicles as $vehicle): ?>
            <div class="vehicle-card">
                <div class="vehicle-image-wrapper">
                    <?php if (!empty($vehicle['audio_url']) || !empty($vehicle['video_url'])): ?>
                    <div class="sound-badge">Sound</div>
                    <?php endif; ?>
                    <img src="<?= htmlspecialchars($vehicle['main_image_url']) ?>"
                         alt="<?= htmlspecialchars($vehicle['title']) ?>"
                         class="vehicle-image" loading="lazy">
                </div>
                <div class="vehicle-info">
                    <div class="vehicle-condition"><?= htmlspecialchars($vehicle['condition']) ?></div>
                    <h3 class="vehicle-title">
                        <a href="/lending_word/finder_detail.php?id=<?= $vehicle['id'] ?>">
                            <?= htmlspecialchars($vehicle['title']) ?>
                        </a>
                    </h3>
                    <div class="vehicle-specs">
                        <?php if ($vehicle['exterior_color']): ?><span><?= htmlspecialchars($vehicle['exterior_color']) ?></span><?php endif; ?>
                        <?php if ($vehicle['interior_color']): ?><span><?= htmlspecialchars($vehicle['interior_color']) ?></span><?php endif; ?>
                        <?php if ($vehicle['fuel_type']): ?><span><?= htmlspecialchars($vehicle['fuel_type']) ?></span><?php endif; ?>
                        <?php if ($vehicle['power_kw'] && $vehicle['power_hp']): ?><span><?= $vehicle['power_kw'] ?> kW / <?= $vehicle['power_hp'] ?> hp</span><?php endif; ?>
                        <?php if ($vehicle['drive_type']): ?><span><?= htmlspecialchars($vehicle['drive_type']) ?></span><?php endif; ?>
                        <?php if ($vehicle['transmission']): ?><span><?= htmlspecialchars($vehicle['transmission']) ?></span><?php endif; ?>
                    </div>
                    <div class="vehicle-price">
                        Rp <?= number_format($vehicle['price'], 0, ',', '.') ?>
                    </div>
                    <div class="vehicle-actions">
                        <a href="/lending_word/finder_detail.php?id=<?= $vehicle['id'] ?>" class="btn-details">Show details</a>
                        <button class="btn-save" data-save-vehicle="<?= $vehicle['id'] ?>" data-vehicle-id="<?= $vehicle['id'] ?>">
                            <i class="far fa-bookmark"></i>
                            <span>Save</span>
                        </button>
                    </div>
                    <?php if (!empty($vehicle['center_name'])): ?>
                    <div class="vehicle-center"><?= htmlspecialchars($vehicle['center_name']) ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </main>

</div>

<script>
(function () {
'use strict';

/* ─── 1. INTRO ─── */
const intro = document.getElementById('intro');
intro.style.display = 'flex';
intro.style.opacity = '1';

setTimeout(function () {
    intro.classList.add('open');
    setTimeout(function () {
        intro.classList.add('done');
        intro.style.pointerEvents = 'none';
        setTimeout(function () {
            intro.style.display = 'none';
            intro.style.pointerEvents = 'none';
            intro.style.zIndex = '-1';
        }, 600);
    }, 1150);
}, 900);

/* ─── 2. CURSOR ─── */
const dot  = document.getElementById('cursor-dot');
const ring = document.getElementById('cursor-ring');
let mx = 0, my = 0, rx = 0, ry = 0;

window.addEventListener('mousemove', function (e) {
    mx = e.clientX;
    my = e.clientY;
}, { passive: true });

document.addEventListener('mousedown', function () {
    document.body.classList.add('c-click');
    setTimeout(function () { document.body.classList.remove('c-click'); }, 280);
});

document.documentElement.addEventListener('mouseleave', function () {
    dot.style.opacity = '0'; ring.style.opacity = '0';
});
document.documentElement.addEventListener('mouseenter', function () {
    dot.style.opacity = ''; ring.style.opacity = '';
});

(function tick() {
    rx += (mx - rx) * 0.16;
    ry += (my - ry) * 0.16;
    dot.style.left  = mx + 'px';
    dot.style.top   = my + 'px';
    ring.style.left = rx + 'px';
    ring.style.top  = ry + 'px';
    requestAnimationFrame(tick);
})();

document.querySelectorAll('a, button, input, label, summary, select').forEach(function (el) {
    el.addEventListener('mouseenter', function () { document.body.classList.add('c-link'); });
    el.addEventListener('mouseleave', function () { document.body.classList.remove('c-link'); });
});

document.querySelectorAll('.vehicle-card').forEach(function (c) {
    c.addEventListener('mouseenter', function () {
        document.body.classList.remove('c-link');
        document.body.classList.add('c-card');
    });
    c.addEventListener('mouseleave', function () {
        document.body.classList.remove('c-card');
    });
});

/* ─── 3. SCROLL PROGRESS + NAVBAR ─── */
const progressEl = document.getElementById('progress');
const navbar = document.querySelector('.navbar');

window.addEventListener('scroll', function () {
    const maxY = document.body.scrollHeight - window.innerHeight;
    const pct  = maxY > 0 ? window.scrollY / maxY : 0;
    if (progressEl) progressEl.style.width = (pct * 100) + '%';
    if (navbar) navbar.classList.toggle('scrolled', window.scrollY > 50);
}, { passive: true });

/* ─── 4. CARD REVEAL ─── */
const revealObs = new IntersectionObserver(function (entries) {
    entries.forEach(function (e) {
        if (!e.isIntersecting) return;
        const allCards = Array.from(document.querySelectorAll('.vehicle-card'));
        const idx = allCards.indexOf(e.target);
        setTimeout(function () { e.target.classList.add('visible'); }, idx * 70);
        revealObs.unobserve(e.target);
    });
}, { threshold: 0.06, rootMargin: '0px 0px -40px 0px' });

document.querySelectorAll('.vehicle-card').forEach(function (c) { revealObs.observe(c); });

/* ─── 5. FILTER TOGGLE ─── */
window.toggleFilter = function (header) {
    header.classList.toggle('collapsed');
    header.nextElementSibling.classList.toggle('hidden');
};

/* ─── 6. APPLY / SORT / RESET ─── */
window.applyFilters = function () {
    document.getElementById('filterForm').submit();
};

window.changeSort = function (val) {
    const u = new URL(window.location.href);
    u.searchParams.set('sort_by', val);
    window.location.href = u.toString();
};

window.resetFilters = function () {
    window.location.href = window.location.pathname;
};

/* ─── 7. CONDITION SINGLE SELECT ─── */
document.querySelectorAll('input[name="condition"]').forEach(function (cb) {
    cb.addEventListener('change', function () {
        if (this.checked) {
            document.querySelectorAll('input[name="condition"]').forEach(function (c) {
                if (c !== cb) c.checked = false;
            });
        }
        window.applyFilters();
    });
});

/* ─── 8. FILTER INPUTS — submit on change ─── */
document.querySelectorAll(
    '#filterForm input[type="checkbox"]:not([name="condition"]), ' +
    '#filterForm input[type="number"]'
).forEach(function (el) {
    el.addEventListener('change', function () { window.applyFilters(); });
});

/* ─── 9. SCROLL RESTORE ─── */
window.addEventListener('beforeunload', function () {
    sessionStorage.setItem('finderScrollPos', window.scrollY);
});
window.addEventListener('load', function () {
    const y = sessionStorage.getItem('finderScrollPos');
    if (y) {
        window.scrollTo(0, parseInt(y));
        sessionStorage.removeItem('finderScrollPos');
    }
});

})();

/* ─── FIX FOOTER & OVERLAY ─── */
document.addEventListener('DOMContentLoaded', function () {

    /* 1. Bunuh overlay */
    document.querySelectorAll('.page-transition-overlay').forEach(function (el) {
        el.remove();
    });

    /* 2. Clone links untuk strip listener dari style.css global */
    function stripListeners(selector) {
        document.querySelectorAll(selector).forEach(function (el) {
            var clone = el.cloneNode(true);
            if (el.parentNode) el.parentNode.replaceChild(clone, el);
        });
    }
    stripListeners('.navbar a, footer a, .footer-section a, .social-links a, .footer-col a');

    /* 3. Pointer-events paksa ke footer */
    document.querySelectorAll('footer, footer *').forEach(function (el) {
        el.style.pointerEvents = 'auto';
    });
});

window.addEventListener('load', function () {
    document.querySelectorAll('.page-transition-overlay').forEach(function (el) {
        el.remove();
    });
    document.querySelectorAll('footer a, .footer-section a, .footer-col a, .social-links a').forEach(function (el) {
        var clone = el.cloneNode(true);
        if (el.parentNode) el.parentNode.replaceChild(clone, el);
    });
    document.querySelectorAll('footer, footer *').forEach(function (el) {
        el.style.pointerEvents = 'auto';
    });
});
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>

</body>
</html>