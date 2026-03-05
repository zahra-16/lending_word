<?php
session_start();
require_once __DIR__ . '/../../models/ModelVariant.php';
require_once __DIR__ . '/../../models/FooterSection.php';
require_once __DIR__ . '/../../models/Content.php';

$modelVariant = new ModelVariant();
$categories = $modelVariant->getCategories();
$selectedCategory = $_GET['category'] ?? 'all';
$variantsGrouped = $modelVariant->getVariantsGrouped($selectedCategory);
$filters = $modelVariant->getFilters();

if (empty($variantsGrouped)) {
    echo "<!-- DEBUG: No variants found for category: {$selectedCategory} -->";
    echo "<!-- Available categories: ";
    foreach ($categories as $cat) { echo $cat['slug'] . ' '; }
    echo "-->";
}

$footerSectionModel = new FooterSection();
$footerSections = $footerSectionModel->getAllWithLinks();
$socialLinks = $footerSectionModel->getSocialLinks();

$contentModel = new Content();
$getContent = function($section, $key) use ($contentModel) {
    return $contentModel->get($section, $key);
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Model Overview - Porsche</title>
    <link rel="icon" type="image/png" href="/lending_word/public/assets/images/porsche-logo.png">
    <link rel="stylesheet" href="/lending_word/public/assets/css/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>

/* ================================================================
   PORSCHE NEXT FONT - Local File
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

/* Override Font Awesome agar tidak terpengaruh */
.fa, .fas, .far, .fal, .fad, .fab,
[class^="fa-"], [class*=" fa-"],
.fa-solid, .fa-regular, .fa-light, .fa-brands,
i[class*="fa"] {
    font-family: "Font Awesome 6 Free", "Font Awesome 6 Brands", "Font Awesome 5 Free", "Font Awesome 5 Brands" !important;
    font-style: normal;
}
.fa-solid, .fas { font-family: "Font Awesome 6 Free" !important; font-weight: 900 !important; }
.fa-regular, .far { font-family: "Font Awesome 6 Free" !important; font-weight: 400 !important; }
.fa-brands, .fab { font-family: "Font Awesome 6 Brands" !important; font-weight: 400 !important; }

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
    --white:     #ffffff;
    --off:       #f6f6f3;
    --black:     #0a0a0a;
    --gray:      #888;
    --light:     #e6e6e0;
    --ease:      cubic-bezier(0.16, 1, 0.3, 1);
    --ease-back: cubic-bezier(0.34, 1.56, 0.64, 1);
    --ease-circ: cubic-bezier(0.85, 0, 0.15, 1);
    --font:      'Porsche Next', 'Arial Narrow', Arial, 'Helvetica Neue', Helvetica, sans-serif;
}

html { cursor: none; scroll-behavior: smooth; }

body {
    background: var(--white);
    color: var(--black);
    font-family: var(--font);
    font-weight: 600;
    overflow-x: hidden;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

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
    mix-blend-mode: difference; /* ← kunci utama */
}
#cursor-dot {
    width: 8px; height: 8px;
    background: #ffffff; /* putih = di bg putih jadi hitam, di bg gelap jadi putih */
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

body.c-gold #cursor-dot  { width: 10px; height: 10px; }
body.c-gold #cursor-ring { width: 54px; height: 54px; }

body.c-click #cursor-dot {
    transform: translate(-50%, -50%) scale(2.5);
    opacity: 0;
}
body.c-click #cursor-ring {
    transform: translate(-50%, -50%) scale(1.5);
    opacity: 0;
}

/* ===== PROGRESS BAR ===== */
#progress {
    position: fixed; top: 0; left: 0; height: 2px; width: 0;
    background: var(--gray); z-index: 8000; transition: width .1s linear;
}

/* ===== INTRO ===== */
#intro {
    position: fixed; inset: 0; z-index: 5000;
    display: flex; align-items: center; justify-content: center;
    background: #000;
    overflow: hidden;
    transition: opacity .5s ease .1s;
}
#intro.done { opacity: 0; pointer-events: none; }

#intro::after {
    content: '';
    position: absolute; left: 0; right: 0; height: 1px;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    top: 0;
    animation: scanLine 1.8s var(--ease-circ) forwards;
    pointer-events: none; z-index: 5;
}
@keyframes scanLine {
    0%   { top: 0; opacity: 0; }
    10%  { opacity: 1; }
    90%  { opacity: 1; }
    100% { top: 100%; opacity: 0; }
}

#intro-counter {
    position: absolute; bottom: 48px; right: 60px;
    font-size: 11px; letter-spacing: .35em;
    color: rgba(255,255,255,0.25);
    font-family: var(--font);
    font-weight: 200; z-index: 3;
    font-variant-numeric: tabular-nums;
}
#intro-label {
    position: absolute; bottom: 48px; left: 60px;
    font-size: 9px; letter-spacing: .5em;
    text-transform: uppercase; color: rgba(255,255,255,0.18); z-index: 3;
    font-family: var(--font);
}
#intro-progress {
    position: absolute; bottom: 0; left: 0;
    height: 1px; background: rgba(255,255,255,0.4); width: 0%;
    z-index: 4; box-shadow: 0 0 12px rgba(255,255,255,0.3);
    transition: width .05s linear;
}
#intro-progress::after {
    content: '';
    position: absolute; right: 0; top: -1px;
    width: 40px; height: 3px;
    background: rgba(255,255,255,0.8); filter: blur(2px);
}

.c-panel {
    position: absolute; top: 0; bottom: 0; width: 50%;
    background: #0a0a0a; z-index: 2;
    transition: transform 1.3s cubic-bezier(0.76, 0, 0.24, 1);
}
.c-panel.l { left: 0;  border-right: 1px solid rgba(255,255,255,0.06); }
.c-panel.r { right: 0; border-left:  1px solid rgba(255,255,255,0.06); }
#intro.open .c-panel.l { transform: translateX(-102%); }
#intro.open .c-panel.r { transform: translateX(102%); }

#intro-logo {
    position: relative; z-index: 2; opacity: 0;
    animation: wrdIn .7s .2s var(--ease) forwards;
    display: flex; align-items: center; justify-content: center;
}
#intro-logo img { width: clamp(70px, 9vw, 110px); height: auto; filter: none; }

@keyframes wrdIn {
    from { opacity: 0; transform: scale(0.85) translateY(8px); filter: blur(8px); }
    to   { opacity: 1; transform: scale(1) translateY(0); filter: blur(0); }
}
@keyframes wrdUp  { to { opacity: 1; transform: translateY(0); } }
@keyframes lineUp { to { opacity: 1; transform: translateY(0); } }

/* ===== NAVBAR ===== */
.navbar { background: transparent !important; transition: background .4s ease, box-shadow .4s ease; }
.navbar.scrolled {
    background: rgba(255,255,255,0.92) !important;
    backdrop-filter: blur(16px);
    box-shadow: 0 1px 0 rgba(0,0,0,.07);
}
.navbar .navbar-brand,
.navbar .navbar-menu a { color: var(--black) !important; filter: none !important; }
.navbar.scrolled .navbar-brand,
.navbar.scrolled .navbar-menu a { color: var(--black) !important; }
.navbar-menu a::after { background: var(--black) !important; }
.navbar-brand img { filter: brightness(0) !important; }

/* ===== CONTAINER ===== */
.models-container { max-width: 1700px; margin: 0 auto; padding: 200px 60px 160px; }

/* ===== PAGE HEADER ===== */
.page-header { margin-bottom: 70px; }

.page-eyebrow {
    font-family: var(--font);
    font-size: 11px; letter-spacing: .35em; text-transform: uppercase;
    color: var(--gray); margin-bottom: 16px;
    display: flex; align-items: center; gap: 12px;
    opacity: 0; animation: lineUp .6s .7s var(--ease) forwards;
}
.page-eyebrow::before { content: ''; display: block; width: 28px; height: 1px; background: var(--gray); }

.page-title {
    font-family: var(--font);
    font-size: clamp(52px, 7vw, 100px); font-weight: 900;
    line-height: .95; letter-spacing: -.01em; overflow: hidden;
}

.tw {
    display: inline-block; opacity: 0; transform: translateY(100%);
    animation: wrdUp .75s var(--ease) forwards;
}
.tw:nth-child(1) { animation-delay: .8s; }
.tw:nth-child(2) { animation-delay: .95s; margin-left: .22em; }

/* ===== LAYOUT ===== */
.content-wrapper {
    display: flex; gap: 56px;
    opacity: 0; animation: lineUp .7s 1.2s var(--ease) forwards;
}

/* ===== SIDEBAR ===== */
.sidebar { width: 275px; flex-shrink: 0; position: sticky; top: 100px; align-self: flex-start; }

.sidebar-inner {
    background: var(--white); border: 1px solid var(--light); border-radius: 20px;
    padding: 30px; box-shadow: 0 2px 0 var(--light), 0 20px 50px rgba(0,0,0,.06);
}

.sdb-title {
    font-family: var(--font);
    font-size: 10px; letter-spacing: .3em; text-transform: uppercase; color: var(--gray); margin-bottom: 14px;
}

.model-filter { list-style: none; }
.model-filter li {
    display: flex; align-items: center; gap: 10px; padding: 7px 0;
    border-bottom: 1px solid transparent; transition: border-color .2s;
}
.model-filter li:hover { border-color: var(--light); }
.model-filter input[type="radio"] {
    appearance: none; width: 15px; height: 15px;
    border: 1.5px solid var(--light); border-radius: 50%;
    cursor: pointer; flex-shrink: 0; position: relative; transition: border-color .2s;
}
.model-filter input[type="radio"]::after {
    content: ''; position: absolute; inset: 3px;
    background: var(--black); border-radius: 50%;
    transform: scale(0); transition: transform .25s var(--ease-back);
}
.model-filter input[type="radio"]:checked { border-color: var(--black); }
.model-filter input[type="radio"]:checked::after { transform: scale(1); }
.model-filter label {
    font-family: var(--font);
    font-size: 14px; color: var(--black);
    display: flex; justify-content: space-between; width: 100%; cursor: pointer;
}
.model-filter .count { color: var(--gray); font-size: 12px; }

.filter-group { border-top: 1px solid var(--light); padding-top: 18px; margin-top: 18px; }
.filter-group summary {
    font-family: var(--font);
    font-size: 14px; font-weight: 500; color: var(--black);
    cursor: pointer; list-style: none;
    display: flex; justify-content: space-between; align-items: center;
    margin-bottom: 12px; user-select: none;
}
.filter-group summary::-webkit-details-marker { display: none; }
.sum-icon {
    width: 20px; height: 20px; border: 1px solid var(--light); border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 15px; color: var(--gray); line-height: 1;
    transition: transform .3s var(--ease), border-color .2s, color .2s;
}
.filter-group[open] .sum-icon { transform: rotate(45deg); border-color: var(--black); color: var(--black); }

.filter-options { display: flex; flex-direction: column; gap: 2px; animation: dropIn .3s var(--ease); }
@keyframes dropIn {
    from { opacity: 0; transform: translateY(-8px); }
    to   { opacity: 1; transform: translateY(0); }
}
.filter-options label {
    font-family: var(--font);
    font-size: 13px; color: rgba(0,0,0,.7);
    display: flex; align-items: center; gap: 10px;
    padding: 5px 0; cursor: pointer; transition: color .2s;
}
.filter-options label:hover { color: var(--black); }
.filter-options input[type="checkbox"] {
    appearance: none; width: 14px; height: 14px;
    border: 1.5px solid var(--light); border-radius: 3px;
    cursor: pointer; flex-shrink: 0; position: relative;
    transition: border-color .2s, background .2s;
}
.filter-options input[type="checkbox"]:checked { background: var(--black); border-color: var(--black); }
.filter-options input[type="checkbox"]:checked::after {
    content: ''; position: absolute; left: 3px; top: 1px;
    width: 5px; height: 8px; border: 1.5px solid #fff;
    border-top: none; border-left: none; transform: rotate(45deg);
}

.reset-btn {
    width: 100%; padding: 11px; margin-top: 22px;
    background: transparent; border: 1.5px solid var(--black); border-radius: 6px;
    font-family: var(--font);
    font-size: 11px; letter-spacing: .2em; text-transform: uppercase;
    cursor: none; color: var(--black); position: relative; overflow: hidden; transition: color .35s ease;
}
.reset-btn::before {
    content: ''; position: absolute; inset: 0;
    background: var(--black); transform: translateY(101%); transition: transform .35s var(--ease);
}
.reset-btn:hover { color: var(--white); }
.reset-btn:hover::before { transform: translateY(0); }
.reset-btn span { position: relative; z-index: 1; }

/* ===== MAIN CONTENT ===== */
.main-content { flex: 1; min-width: 0; }

.variant-group { margin-bottom: 100px; }

.group-eyebrow {
    font-family: var(--font);
    font-size: 10px; letter-spacing: .3em; text-transform: uppercase;
    color: var(--gray); margin-bottom: 8px;
    opacity: 0; transform: translateX(-10px);
    transition: opacity .5s ease, transform .5s ease;
}

/* ✅ FIXED: group heading — no uppercase, mixed case */
.variant-group h2 {
    font-family: var(--font);
    font-size: clamp(34px, 4vw, 56px); font-weight: 600;
    color: var(--black); letter-spacing: .01em;
    line-height: 1; margin-bottom: 40px;
    opacity: 0; transform: translateY(18px);
    transition: opacity .6s ease, transform .6s ease .1s;
}
.variant-group.visible .group-eyebrow,
.variant-group.visible h2 { opacity: 1; transform: none; }

.variants-grid {
    display: grid; grid-template-columns: repeat(2, 1fr);
    gap: 70px 28px; padding-top: 90px;
}

/* ===== CARD ===== */
.variant-card {
    background: var(--white); border-radius: 20px;
    padding-top: 60px; padding-bottom: 28px;
    border: 1px solid var(--light);
    box-shadow: 0 1px 0 rgba(255,255,255,.9) inset, 0 18px 55px rgba(0,0,0,.07), 0 2px 6px rgba(0,0,0,.04);
    position: relative; overflow: visible; cursor: none;
    transform-style: preserve-3d; will-change: transform;
    opacity: 0; transform: translateY(50px) scale(.97);
    transition: opacity .7s var(--ease), transform .7s var(--ease), box-shadow .4s ease;
}
.variant-card.visible { opacity: 1; transform: translateY(0) scale(1); }
.variant-card.in-compare {
    box-shadow: 0 0 0 2.5px var(--black), 0 18px 55px rgba(0,0,0,.07), 0 2px 6px rgba(0,0,0,.04) !important;
}
.variant-card::after {
    content: ''; position: absolute; inset: -1px; border-radius: 25px;
    background: linear-gradient(135deg, rgba(100,100,100,.22), rgba(0,0,0,.06)) border-box;
    border: 1px solid transparent;
    -webkit-mask: linear-gradient(#fff 0 0) padding-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: destination-out; mask-composite: exclude;
    opacity: 0; transition: opacity .4s ease; pointer-events: none;
}
.variant-card:hover::after { opacity: 1; }

.spotlight {
    position: absolute; inset: 0; border-radius: 24px; pointer-events: none; opacity: 0;
    background: radial-gradient(circle 200px at var(--mx,50%) var(--my,50%), rgba(0,0,0,.04) 0%, transparent 70%);
    transition: opacity .3s ease;
}
.variant-card:hover .spotlight { opacity: 1; }

.badge-new {
    position: absolute; top: 22px; right: 22px;
    background: var(--black); color: #fff;
    font-family: var(--font);
    font-size: 10px; padding: 5px 14px; border-radius: 999px;
    letter-spacing: .15em; text-transform: uppercase; z-index: 10;
    animation: badgePulse 2.8s ease-in-out infinite;
}
@keyframes badgePulse {
    0%,100% { box-shadow: 0 0 0 0 rgba(0,0,0,.2); }
    50%      { box-shadow: 0 0 0 10px rgba(0,0,0,0); }
}

.img-wrap {
    position: relative; z-index: 5; margin: -120px -6% 0;
    height: 150px; display: flex; align-items: flex-end; justify-content: center;
}
.variant-image {
    width: 105%; height: 100%; object-fit: contain; display: block;
    filter: drop-shadow(0 16px 28px rgba(0,0,0,.13));
    transition: transform .5s var(--ease), filter .4s ease;
    transform-origin: bottom center; will-change: transform;
}
.variant-card:hover .variant-image { filter: drop-shadow(0 34px 52px rgba(0,0,0,.2)); }
.img-wrap::after {
    content: ''; position: absolute; bottom: -8px; left: 10%; right: 10%; height: 16px;
    background: radial-gradient(ellipse, rgba(0,0,0,.18) 0%, transparent 70%);
    border-radius: 50%; transition: transform .4s ease, opacity .4s ease;
}
.variant-card:hover .img-wrap::after { transform: scaleX(.86); opacity: .7; }

.card-body { padding: 16px 20px 0; }

/* ✅ FIXED: variant name — no uppercase, mixed case */
.variant-name {
    font-family: var(--font);
    font-size: 24px; font-weight: 700;
    letter-spacing: .02em;
    margin-bottom: 10px; line-height: 1.1;
    transition: letter-spacing .3s ease;
}
.variant-card:hover .variant-name { letter-spacing: .04em; }

.variant-tags { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 16px; }
.variant-tag {
    font-family: var(--font);
    background: var(--off); color: rgba(0,0,0,.65);
    padding: 4px 12px; font-size: 11px; border-radius: 999px;
    border: 1px solid var(--light); transition: background .25s, border-color .25s;
}
.variant-card:hover .variant-tag { background: var(--white); border-color: rgba(0,0,0,.12); }

.variant-specs {
    display: grid; grid-template-columns: repeat(3, 1fr);
    border: 1px solid var(--light); border-radius: 10px;
    overflow: hidden; margin-bottom: 14px;
}
.spec-cell {
    padding: 10px 10px; border-right: 1px solid var(--light);
    position: relative; overflow: hidden;
}
.spec-cell:last-child { border-right: none; }
.spec-cell::before {
    content: ''; position: absolute; inset: 0;
    background: var(--off); transform: translateY(100%); transition: transform .4s var(--ease);
}
.variant-card:hover .spec-cell::before { transform: translateY(0); }
.spec-val {
    font-family: var(--font);
    font-size: 17px; font-weight: 600;
    display: block; position: relative; z-index: 1; line-height: 1.1; letter-spacing: .03em;
}
.spec-lbl {
    font-family: var(--font);
    font-size: 9px; color: var(--gray); text-transform: uppercase;
    letter-spacing: .08em; display: block; margin-top: 3px; position: relative; z-index: 1;
}

.bars { margin-bottom: 18px; display: flex; flex-direction: column; gap: 8px; }
.bar-row { display: grid; grid-template-columns: 72px 1fr 44px; align-items: center; gap: 10px; }
.bar-lbl {
    font-family: var(--font);
    font-size: 10px; text-transform: uppercase; letter-spacing: .08em; color: var(--gray);
}
.bar-track { height: 1px; background: var(--light); border-radius: 1px; position: relative; overflow: hidden; }
.bar-fill {
    position: absolute; inset: 0;
    background: linear-gradient(to right, rgba(0,0,0,.15), var(--black));
    transform: scaleX(0); transform-origin: left;
    border-radius: 1px; transition: transform 1.1s var(--ease);
}
.bar-row.is-accel .bar-fill { background: linear-gradient(to right, rgba(100,100,100,.3), #555); }
.variant-card.visible .bar-fill { transform: scaleX(var(--fill)); }
.bar-val {
    font-family: var(--font);
    font-size: 11px; color: var(--black); text-align: right; font-variant-numeric: tabular-nums;
}

/* ===== BUTTONS ===== */
.variant-actions { display: flex; gap: 10px; padding: 0 20px; }

.btn {
    padding: 13px 22px;
    font-family: var(--font);
    font-size: 12px; text-transform: uppercase; letter-spacing: .15em;
    border-radius: 6px; cursor: none; text-decoration: none;
    display: inline-block; text-align: center;
    position: relative; overflow: hidden; border: none;
    transition: transform .2s ease, box-shadow .3s ease, background .3s ease, color .3s ease;
}
.btn::before {
    content: ''; position: absolute; inset: 0;
    background: rgba(255,255,255,.14);
    transform: scaleX(0); transform-origin: left; transition: transform .4s ease;
}
.btn:hover::before { transform: scaleX(1); }
.btn:active { transform: scale(.97); }
.btn-primary { background: var(--black); color: var(--white); flex: 1; }
.btn-primary:hover { box-shadow: 0 12px 28px rgba(0,0,0,.22); color: var(--white); }
.btn-secondary { background: var(--off); color: var(--black); border: 1px solid var(--light); }
.btn-secondary:hover { background: var(--black); color: var(--white); }
.btn-compare.active { background: var(--black) !important; color: var(--white) !important; border-color: var(--black) !important; }

/* ===== COMPARE BAR ===== */
.compare-bar {
    position: fixed; bottom: 0; left: 0; right: 0; z-index: 500;
    background: rgba(255,255,255,0.96); backdrop-filter: blur(16px);
    border-top: 1px solid rgba(0,0,0,0.10); box-shadow: 0 -8px 40px rgba(0,0,0,0.10);
    padding: 18px 60px; display: flex; align-items: center; gap: 20px;
    transform: translateY(100%); transition: transform 0.38s cubic-bezier(0.16, 1, 0.3, 1);
}
.compare-bar.show { transform: translateY(0); }
.compare-slots { display: flex; gap: 14px; flex: 1; }
.compare-slot {
    width: 120px; height: 72px; border-radius: 12px;
    border: 1.5px dashed rgba(0,0,0,0.15);
    display: flex; align-items: center; justify-content: center;
    font-family: var(--font);
    font-size: 0.72rem; color: rgba(0,0,0,0.35); letter-spacing: 0.5px;
    position: relative; overflow: hidden; background: var(--off); transition: border-color .2s;
}
.compare-slot.filled { border-color: var(--black); border-style: solid; background: var(--white); }
.compare-slot img {
    max-width: 100%; max-height: 100%; object-fit: contain;
    padding: 4px; filter: drop-shadow(0 4px 8px rgba(0,0,0,.15));
}
.compare-slot .slot-remove {
    position: absolute; top: 4px; right: 4px;
    background: rgba(0,0,0,0.7); color: #fff;
    border: none; border-radius: 50%;
    width: 18px; height: 18px; font-size: 0.6rem; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    opacity: 0; transition: opacity .2s;
}
.compare-slot.filled:hover .slot-remove { opacity: 1; }
.compare-bar-info {
    font-family: var(--font);
    font-size: 0.82rem; color: rgba(0,0,0,.55); white-space: nowrap;
}
.compare-bar-info strong { color: var(--black); }
.btn-go-compare {
    background: var(--black); color: var(--white);
    border: none; padding: 13px 30px;
    font-family: var(--font);
    font-size: 11px; letter-spacing: .2em; text-transform: uppercase;
    border-radius: 6px; cursor: pointer;
    white-space: nowrap; transition: background .2s; position: relative; overflow: hidden;
}
.btn-go-compare::before {
    content: ''; position: absolute; inset: 0;
    background: rgba(255,255,255,.12);
    transform: scaleX(0); transform-origin: left; transition: transform .35s var(--ease);
}
.btn-go-compare:hover::before { transform: scaleX(1); }
.btn-go-compare:hover { background: #222; }
.btn-go-compare:disabled { background: #aaa; cursor: not-allowed; }
.btn-clear-compare {
    background: none; border: 1.5px solid var(--black); color: var(--black);
    padding: 13px 22px;
    font-family: var(--font);
    font-size: 11px; letter-spacing: .2em; text-transform: uppercase;
    border-radius: 6px; cursor: pointer;
    white-space: nowrap; position: relative; overflow: hidden; transition: color .35s ease;
}
.btn-clear-compare::before {
    content: ''; position: absolute; inset: 0;
    background: var(--black); transform: translateY(101%); transition: transform .35s var(--ease);
}
.btn-clear-compare:hover { color: var(--white); }
.btn-clear-compare:hover::before { transform: translateY(0); }
.btn-clear-compare span { position: relative; z-index: 1; }

/* ===== RESPONSIVE ===== */
@media (max-width: 1100px) {
    .content-wrapper { flex-direction: column; }
    .sidebar { width: 100%; position: static; }
    .variants-grid { grid-template-columns: 1fr 1fr; gap: 80px 28px; }
}
@media (max-width: 720px) {
    .models-container { padding: 100px 24px 160px; }
    .variants-grid { grid-template-columns: 1fr; }
    html { cursor: auto; }
    #cursor-dot, #cursor-ring { display: none; }
    .btn, .reset-btn, .variant-card { cursor: pointer; }
    .compare-bar { padding: 14px 20px; gap: 12px; }
    .compare-slot { width: 80px; height: 52px; }
    .compare-bar-info { display: none; }
}
</style>
</head>
<body>

<div id="cursor-dot"></div>
<div id="cursor-ring"></div>

<div id="intro">
    <div class="c-panel l"></div>
    <div id="intro-logo">
        <img src="/lending_word/public/assets/images/porsche-logo.png" alt="Porsche">
    </div>
    <div class="c-panel r"></div>
    <span id="intro-counter">000</span>
    <span id="intro-label">Initializing</span>
    <div id="intro-progress"></div>
</div>

<div id="progress"></div>

<?php include __DIR__ . '/../partials/navbar.php'; ?>

<div class="models-container">

    <header class="page-header">
        <p class="page-eyebrow">Model lineup</p>
        <h1 class="page-title">
    <span class="tw">Model</span>
    <span class="tw">Overview</span>
</h1>
    </header>

    <div class="content-wrapper">

        <aside class="sidebar">
            <div class="sidebar-inner">
                <div class="sdb-title">Models</div>
                <ul class="model-filter">
                    <?php foreach ($categories as $cat): ?>
                    <li>
                        <input type="radio" name="model" id="cat-<?= $cat['slug'] ?>"
                               value="<?= $cat['slug'] ?>"
                               <?= $selectedCategory === $cat['slug'] ? 'checked' : '' ?>
                               onchange="saveScrollAndNavigate('?category=<?= $cat['slug'] ?>')">
                        <label for="cat-<?= $cat['slug'] ?>">
                            <?= htmlspecialchars($cat['name']) ?>
                            <span class="count"><?= $cat['count'] ?></span>
                        </label>
                    </li>
                    <?php endforeach; ?>
                </ul>

                <details class="filter-group" open>
                    <summary>Body Design <span class="sum-icon">+</span></summary>
                    <div class="filter-options">
                        <?php foreach ($filters['body_design'] as $design): ?>
                        <label><input type="checkbox" name="body_design" value="<?= $design ?>"> <?= htmlspecialchars($design) ?></label>
                        <?php endforeach; ?>
                    </div>
                </details>
                <details class="filter-group">
                    <summary>Seats <span class="sum-icon">+</span></summary>
                    <div class="filter-options">
                        <?php foreach ($filters['seats'] as $seat): ?>
                        <label><input type="checkbox" name="seats" value="<?= $seat ?>"> <?= $seat ?> seats</label>
                        <?php endforeach; ?>
                    </div>
                </details>
                <details class="filter-group">
                    <summary>Drive <span class="sum-icon">+</span></summary>
                    <div class="filter-options">
                        <?php foreach ($filters['drive_type'] as $drive): ?>
                        <label><input type="checkbox" name="drive" value="<?= $drive ?>"> <?= htmlspecialchars($drive) ?></label>
                        <?php endforeach; ?>
                    </div>
                </details>
                <details class="filter-group">
                    <summary>Fuel Type <span class="sum-icon">+</span></summary>
                    <div class="filter-options">
                        <?php foreach ($filters['fuel_type'] as $fuel): ?>
                        <label><input type="checkbox" name="fuel" value="<?= $fuel ?>"> <?= htmlspecialchars($fuel) ?></label>
                        <?php endforeach; ?>
                    </div>
                </details>

                <button class="reset-btn" onclick="resetFilters()"><span>Reset Filter</span></button>
            </div>
        </aside>

        <main class="main-content">
            <?php foreach ($variantsGrouped as $groupName => $variants): ?>
            <div class="variant-group">
                <div class="group-eyebrow">Model family</div>
                <h2><?= htmlspecialchars($groupName) ?></h2>
                <div class="variants-grid">
                    <?php foreach ($variants as $variant): ?>
                    <?php
                        $accel = $variant['acceleration'] ?? null;
                        $kw    = $variant['power_kw']     ?? null;
                        $ps    = $variant['power_ps']     ?? null;
                        $speed = $variant['top_speed']    ?? null;
                        $aFill = $accel ? max(0, 1 - ((float)$accel / 14)) : 0;
                        $sFill = $speed ? min(1, (float)$speed / 330)       : 0;
                        $pFill = $kw    ? min(1, (float)$kw / 700)          : 0;
                    ?>
                    <div class="variant-card <?= !empty($variant['is_new']) ? 'is-new' : '' ?>" data-id="<?= $variant['id'] ?>">
                        <div class="spotlight"></div>
                        <?php if (!empty($variant['is_new'])): ?>
                        <span class="badge-new">New</span>
                        <?php endif; ?>
                        <div class="img-wrap">
                            <img src="<?= htmlspecialchars($variant['image']) ?>"
                                 alt="<?= htmlspecialchars($variant['name']) ?>"
                                 class="variant-image" loading="lazy">
                        </div>
                        <div class="card-body">
                            <h3 class="variant-name"><?= htmlspecialchars($variant['name']) ?></h3>
                            <div class="variant-tags">
                                <?php if (!empty($variant['fuel_type'])): ?><span class="variant-tag"><?= htmlspecialchars($variant['fuel_type']) ?></span><?php endif; ?>
                                <?php if (!empty($variant['drive_type'])): ?><span class="variant-tag"><?= htmlspecialchars($variant['drive_type']) ?></span><?php endif; ?>
                                <?php if (!empty($variant['transmission'])): ?><span class="variant-tag"><?= htmlspecialchars($variant['transmission']) ?></span><?php endif; ?>
                            </div>
                            <div class="variant-specs">
                                <?php if ($accel): ?><div class="spec-cell accel"><span class="spec-val"><?= htmlspecialchars($accel) ?></span><span class="spec-lbl">0–100 km/h</span></div><?php endif; ?>
                                <?php if ($kw || $ps): ?><div class="spec-cell"><span class="spec-val"><?= $kw ?> / <?= $ps ?></span><span class="spec-lbl">kW / PS</span></div><?php endif; ?>
                                <?php if ($speed): ?><div class="spec-cell"><span class="spec-val"><?= htmlspecialchars($speed) ?></span><span class="spec-lbl">Top speed</span></div><?php endif; ?>
                            </div>
                            <div class="bars">
                                <?php if ($accel): ?><div class="bar-row is-accel"><span class="bar-lbl">Accel.</span><div class="bar-track"><div class="bar-fill" style="--fill:<?= $aFill ?>"></div></div><span class="bar-val"><?= $accel ?>s</span></div><?php endif; ?>
                                <?php if ($speed): ?><div class="bar-row"><span class="bar-lbl">Top spd.</span><div class="bar-track"><div class="bar-fill" style="--fill:<?= $sFill ?>"></div></div><span class="bar-val"><?= $speed ?></span></div><?php endif; ?>
                                <?php if ($kw): ?><div class="bar-row"><span class="bar-lbl">Power</span><div class="bar-track"><div class="bar-fill" style="--fill:<?= $pFill ?>"></div></div><span class="bar-val"><?= $kw ?>kW</span></div><?php endif; ?>
                            </div>
                        </div>
                        <div class="variant-actions">
                            <a href="model-detail.php?id=<?= $variant['id'] ?>" class="btn btn-primary">Select model</a>
                            <button class="btn btn-secondary btn-compare"
                                    data-id="<?= $variant['id'] ?>"
                                    data-name="<?= htmlspecialchars($variant['name']) ?>">Compare</button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </main>

    </div>
</div>

<div class="compare-bar" id="compareBar">
    <div class="compare-slots" id="compareSlots">
        <div class="compare-slot" id="slot-0"><span>+ Select</span></div>
        <div class="compare-slot" id="slot-1"><span>+ Select</span></div>
        <div class="compare-slot" id="slot-2"><span>+ Select</span></div>
    </div>
    <div class="compare-bar-info"><strong id="compareCount">0</strong> of 3 models selected</div>
    <button class="btn-clear-compare" onclick="clearCompare()"><span>Clear</span></button>
    <button class="btn-go-compare" id="btnGoCompare" disabled onclick="goCompare()">Compare now</button>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>

<script>
/* ─── Intro ─── */
(function() {
    const intro        = document.getElementById('intro');
    const introCounter = document.getElementById('intro-counter');
    const introProg    = document.getElementById('intro-progress');

    const dur   = 1400;
    const start = performance.now();
    (function tick(now) {
        const p    = Math.min((now - start) / dur, 1);
        const ease = 1 - Math.pow(1 - p, 3);
        const pct  = Math.round(ease * 100);
        if (introCounter) introCounter.textContent = String(pct).padStart(3, '0');
        if (introProg)    introProg.style.width = pct + '%';
        if (p < 1) requestAnimationFrame(tick);
        else openIntro();
    })(start);

    function openIntro() {
        intro.classList.add('open');
        setTimeout(function() {
            intro.classList.add('done');
            setTimeout(function() { intro.style.display = 'none'; }, 600);
        }, 1300);
    }
})();

/* ─── Cursor ─── */
const dot = document.getElementById('cursor-dot');
const ring = document.getElementById('cursor-ring');
let mx = 0, my = 0, rx = 0, ry = 0;

window.addEventListener('mousemove', e => { mx = e.clientX; my = e.clientY; }, { passive: true });

// ← TAMBAHKAN INI
document.addEventListener('mousedown', () => {
    document.body.classList.add('c-click');
    setTimeout(() => document.body.classList.remove('c-click'), 280);
});
document.documentElement.addEventListener('mouseleave', () => {
    dot.style.opacity = '0'; ring.style.opacity = '0';
});
document.documentElement.addEventListener('mouseenter', () => {
    dot.style.opacity = ''; ring.style.opacity = '';
});

(function tick() {
    rx += (mx - rx) * 0.16; ry += (my - ry) * 0.16;
    dot.style.left  = mx + 'px'; dot.style.top  = my + 'px';
    ring.style.left = rx + 'px'; ring.style.top = ry + 'px';
    requestAnimationFrame(tick);
})();

document.querySelectorAll('a, button, .reset-btn, input, label, summary').forEach(el => {
    el.addEventListener('mouseenter', () => document.body.classList.add('c-link'));
    el.addEventListener('mouseleave', () => document.body.classList.remove('c-link'));
});
document.querySelectorAll('.variant-card').forEach(c => {
    c.addEventListener('mouseenter', () => { document.body.classList.remove('c-link'); document.body.classList.add('c-card'); });
    c.addEventListener('mouseleave', () => document.body.classList.remove('c-card'));
});

/* ─── Progress + navbar ─── */
const progressEl = document.getElementById('progress');
const navbar = document.querySelector('.navbar');
window.addEventListener('scroll', () => {
    const pct = window.scrollY / (document.body.scrollHeight - window.innerHeight);
    progressEl.style.width = (pct * 100) + '%';
    navbar?.classList.toggle('scrolled', window.scrollY > 50);
}, { passive: true });

/* ─── Scroll restore ─── */
window.addEventListener('load', function() {
    const y = sessionStorage.getItem('scrollPos');
    if (y) { window.scrollTo(0, parseInt(y)); sessionStorage.removeItem('scrollPos'); }
});
function saveScrollAndNavigate(url) {
    sessionStorage.setItem('scrollPos', window.scrollY);
    window.location.href = url;
}

/* ─── Scroll reveal ─── */
const revealObs = new IntersectionObserver(entries => {
    entries.forEach(e => {
        if (!e.isIntersecting) return;
        const el = e.target; revealObs.unobserve(el);
        if (el.classList.contains('variant-card')) {
            const siblings = Array.from(el.closest('.variants-grid')?.children || [el]);
            setTimeout(() => el.classList.add('visible'), siblings.indexOf(el) * 120);
        } else { el.classList.add('visible'); }
    });
}, { threshold: 0.08, rootMargin: '0px 0px -60px 0px' });
document.querySelectorAll('.variant-card, .variant-group').forEach(el => revealObs.observe(el));

/* ─── 3D tilt ─── */
document.querySelectorAll('.variant-card').forEach(card => {
    const img = card.querySelector('.variant-image');
    const spot = card.querySelector('.spotlight');
    card.addEventListener('mousemove', e => {
        const r = card.getBoundingClientRect();
        const cx = (e.clientX - r.left) / r.width  - 0.5;
        const cy = (e.clientY - r.top)  / r.height - 0.5;
        card.style.transform = `perspective(900px) rotateY(${cx*11}deg) rotateX(${-cy*7}deg) translateY(-6px) scale(1.013)`;
        card.style.boxShadow = `${-cx*22}px ${-cy*16+32}px 70px rgba(0,0,0,.13), 0 2px 6px rgba(0,0,0,.04)`;
        if (img) { img.style.transform = `translateY(-12px) translateX(${cx*16}px) rotateY(${cx*5}deg) scale(1.06)`; img.style.transition = 'transform 0.1s ease, filter 0.4s ease'; }
        if (spot) { spot.style.setProperty('--mx', ((e.clientX - r.left) / r.width * 100) + '%'); spot.style.setProperty('--my', ((e.clientY - r.top) / r.height * 100) + '%'); }
    });
    card.addEventListener('mouseleave', () => {
        if (card.classList.contains('visible')) { card.style.transform = ''; card.style.boxShadow = ''; }
        if (img) { img.style.transform = ''; img.style.transition = 'transform 0.6s cubic-bezier(0.34,1.56,0.64,1), filter 0.4s ease'; }
    });
});

/* ─── Filters ─── */
function filterCards() {
    const bodyDesign = getChecked('body_design'), drive = getChecked('drive'), fuel = getChecked('fuel');
    document.querySelectorAll('.variant-card').forEach(card => {
        const tags = Array.from(card.querySelectorAll('.variant-tag')).map(t => t.textContent.trim());
        let show = true;
        if (bodyDesign.length && !bodyDesign.some(v => tags.includes(v))) show = false;
        if (drive.length      && !drive.some(v => tags.includes(v)))      show = false;
        if (fuel.length       && !fuel.some(v => tags.includes(v)))       show = false;
        if (show) {
            card.style.display = '';
            const vis = Array.from(card.closest('.variants-grid')?.children || [card]).filter(s => s.style.display !== 'none');
            card.classList.remove('visible');
            setTimeout(() => { card.style.opacity = ''; card.style.transform = ''; card.classList.add('visible'); }, 50 + vis.indexOf(card) * 80);
        } else {
            card.classList.remove('visible'); card.style.opacity = '0'; card.style.transform = 'scale(0.95) translateY(10px)';
            setTimeout(() => { if (card.style.opacity === '0') card.style.display = 'none'; }, 350);
        }
    });
}
function getChecked(name) { return Array.from(document.querySelectorAll(`input[name="${name}"]:checked`)).map(cb => cb.value); }
function resetFilters() {
    document.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
    document.querySelectorAll('.variant-card').forEach(card => {
        card.style.display = ''; card.classList.remove('visible'); card.style.opacity = ''; card.style.transform = '';
        const siblings = Array.from(card.closest('.variants-grid')?.children || [card]);
        setTimeout(() => card.classList.add('visible'), siblings.indexOf(card) * 100);
    });
}
document.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.addEventListener('change', filterCards));

/* ─── Compare ─── */
const MAX_COMPARE = 3;
let compareList = JSON.parse(sessionStorage.getItem('compareModels') || '[]');
function renderBar() {
    const bar = document.getElementById('compareBar');
    bar.classList.toggle('show', compareList.length > 0);
    for (let i = 0; i < MAX_COMPARE; i++) {
        const slot = document.getElementById('slot-' + i), item = compareList[i];
        if (item) { slot.classList.add('filled'); slot.innerHTML = `<img src="${item.image}" alt="${item.name}"><button class="slot-remove" onclick="removeFromCompare(${item.id}, event)" title="Remove">✕</button>`; }
        else { slot.classList.remove('filled'); slot.innerHTML = '<span>+ Select</span>'; }
    }
    document.getElementById('compareCount').textContent = compareList.length;
    document.getElementById('btnGoCompare').disabled = compareList.length < 2;
    document.querySelectorAll('.variant-card').forEach(card => {
        const id = parseInt(card.dataset.id), inList = compareList.some(c => c.id === id);
        card.classList.toggle('in-compare', inList);
        const btn = card.querySelector('.btn-compare');
        if (btn) { btn.classList.toggle('active', inList); btn.textContent = inList ? 'Remove' : 'Compare'; }
    });
}
function toggleCompare(id, name, image) {
    const idx = compareList.findIndex(c => c.id === id);
    if (idx > -1) { compareList.splice(idx, 1); } else { if (compareList.length >= MAX_COMPARE) compareList.shift(); compareList.push({ id, name, image }); }
    sessionStorage.setItem('compareModels', JSON.stringify(compareList)); renderBar();
}
function removeFromCompare(id, e) { e.stopPropagation(); compareList = compareList.filter(c => c.id !== id); sessionStorage.setItem('compareModels', JSON.stringify(compareList)); renderBar(); }
function clearCompare() { compareList = []; sessionStorage.removeItem('compareModels'); renderBar(); }
function goCompare() { window.location.href = 'model-compare.php?' + compareList.map((c, i) => `model${i+1}=${c.id}`).join('&'); }
document.querySelectorAll('.btn-compare').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault(); e.stopPropagation();
        const card = this.closest('.variant-card');
        toggleCompare(parseInt(this.dataset.id), this.dataset.name, card?.querySelector('.variant-image')?.src || '');
    });
});
renderBar();
</script>
</body>
</html>