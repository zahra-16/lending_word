<?php
session_start();
require_once __DIR__ . '/../../models/ModelVariant.php';
require_once __DIR__ . '/../../models/FooterSection.php';
require_once __DIR__ . '/../../models/Content.php';

$modelVariant = new ModelVariant();
$categories   = $modelVariant->getCategories();
$allVariants  = $modelVariant->getVariantsByCategory('all');

$ids = [];
for ($i = 1; $i <= 3; $i++) {
    if (!empty($_GET["model$i"])) $ids[$i] = (int) $_GET["model$i"];
}

$selected = [];
foreach ($ids as $slot => $id) {
    foreach ($allVariants as $v) {
        if ((int)$v['id'] === $id) { $selected[$slot] = $v; break; }
    }
}

$footerSectionModel = new FooterSection();
$footerSections     = $footerSectionModel->getAllWithLinks();
$socialLinks        = $footerSectionModel->getSocialLinks();

$contentModel = new Content();
$getContent   = fn($s, $k) => $contentModel->get($s, $k);

$specRows = [
    ['label' => 'Fuel Type',    'key' => 'fuel_type'],
    ['label' => 'Drive Type',   'key' => 'drive_type'],
    ['label' => 'Transmission', 'key' => 'transmission'],
    ['label' => 'Acceleration', 'key' => 'acceleration'],
    ['label' => 'Power',        'key' => '_power'],
    ['label' => 'Top Speed',    'key' => 'top_speed'],
    ['label' => 'Body Design',  'key' => 'body_design'],
    ['label' => 'Seats',        'key' => 'seats'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Model Comparison – Porsche</title>
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

/* Override Font Awesome */
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

*,*::before,*::after { box-sizing: border-box; margin: 0; padding: 0; }

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

html { cursor: none; scroll-behavior: smooth; }

body {
    background: var(--white);
    color: var(--black);
    font-family: var(--font);
    font-weight: 300;
    overflow-x: hidden;
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

/* Navbar */
.navbar { background: transparent !important; transition: background .4s ease; }
.navbar.scrolled { background: rgba(255,255,255,.92) !important; backdrop-filter: blur(16px); box-shadow: 0 1px 0 rgba(0,0,0,.07); }
.navbar-brand, .navbar-menu a { color: var(--black) !important; }
.navbar-menu a::after { display: none !important; }

/* Page */
.compare-page { max-width: 1600px; margin: 0 auto; padding: 140px 60px 80px; }

/* Header */
.compare-header {
    margin-bottom: 60px;
    opacity: 0; transform: translateY(20px);
    animation: fadeUp .7s .3s var(--ease) forwards;
}

/* back link — tetap uppercase, label kecil */
.back-link {
    display: inline-flex; align-items: center; gap: 8px;
    font-size: 11px; letter-spacing: .2em; text-transform: uppercase;
    color: var(--gray); text-decoration: none; margin-bottom: 20px;
    transition: color .2s; cursor: none;
}
.back-link:hover { color: var(--black); }

/* eyebrow — tetap uppercase, label kecil */
.page-eyebrow {
    font-size: 11px; letter-spacing: .35em; text-transform: uppercase;
    color: var(--gray); margin-bottom: 14px;
    display: flex; align-items: center; gap: 12px;
}
.page-eyebrow::before { content: ''; display: block; width: 28px; height: 1px; background: var(--gray); }

/* ✅ FIXED: page title — mixed case, no uppercase */
.page-title {
    font-family: var(--font);
    font-size: clamp(48px, 6vw, 90px); font-weight: 700;
    line-height: .95; letter-spacing: .02em;
}

/* Compare grid */
.compare-grid {
    display: grid; grid-template-columns: 200px repeat(3, 1fr);
    background: var(--white); border: 1px solid var(--light);
    border-radius: 24px; box-shadow: 0 20px 60px rgba(0,0,0,.07);
    overflow: hidden;
    opacity: 0; transform: translateY(24px);
    animation: fadeUp .7s .5s var(--ease) forwards;
}

/* Labels col */
.col-labels { background: var(--off); border-right: 1px solid var(--light); }
.col-labels .col-header { height: 360px; display: flex; align-items: flex-end; padding: 28px 24px; border-bottom: 1px solid var(--light); }

/* ✅ FIXED: "Specifications" label — mixed case */
.col-labels .col-header span {
    font-size: 10px; font-weight: 600;
    text-transform: uppercase; /* label kolom kecil, oke uppercase */
    letter-spacing: .25em; color: var(--gray);
}

/* ✅ FIXED: spec row labels — tetap uppercase karena label kecil */
.spec-label-cell {
    padding: 18px 22px; border-bottom: 1px solid var(--light);
    font-size: 10px; font-weight: 500;
    text-transform: uppercase; letter-spacing: .12em;
    color: var(--gray); display: flex; align-items: center; min-height: 62px;
}

/* Vehicle col */
.col-vehicle { border-right: 1px solid var(--light); transition: background .3s; }
.col-vehicle:last-child { border-right: none; }
.col-vehicle:hover { background: var(--off); }

/* Vehicle header */
.col-header {
    position: relative; display: flex; flex-direction: column;
    align-items: center; justify-content: flex-end;
    padding: 28px 20px 24px; border-bottom: 1px solid var(--light);
    min-height: 360px;
    background: linear-gradient(180deg, var(--off) 0%, var(--white) 100%);
}
.col-header.empty { cursor: none; justify-content: center; background: var(--off); transition: background .25s; }
.col-header.empty:hover { background: var(--light); }

/* "Select model" — tetap uppercase, label kecil */
.add-btn { display: flex; flex-direction: column; align-items: center; gap: 14px; color: var(--gray); font-size: 11px; letter-spacing: .2em; text-transform: uppercase; transition: color .25s; }
.col-header.empty:hover .add-btn { color: var(--black); }

.add-btn-icon {
    width: 48px; height: 48px; border: 1.5px solid var(--light); border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.3rem; color: var(--gray);
    transition: border-color .25s, color .25s, transform .3s var(--ease);
}
.col-header.empty:hover .add-btn-icon { border-color: var(--black); color: var(--black); transform: rotate(90deg); }

.remove-btn {
    position: absolute; top: 14px; right: 14px; width: 28px; height: 28px;
    border-radius: 50%; background: rgba(0,0,0,.06); border: none; cursor: none;
    display: flex; align-items: center; justify-content: center;
    font-size: .8rem; color: var(--gray); transition: background .2s, color .2s; z-index: 5;
}
.remove-btn:hover { background: var(--black); color: var(--white); }

.new-badge {
    position: absolute; top: 14px; left: 14px; background: var(--black); color: #fff;
    font-size: 10px; padding: 4px 12px; border-radius: 999px;
    letter-spacing: .15em; text-transform: uppercase; font-weight: 500;
    animation: badgePulse 2.8s ease-in-out infinite;
}
@keyframes badgePulse { 0%,100% { box-shadow: 0 0 0 0 rgba(0,0,0,.2); } 50% { box-shadow: 0 0 0 10px rgba(0,0,0,0); } }

.car-image-wrap { width: 100%; max-width: 280px; height: 160px; display: flex; align-items: center; justify-content: center; margin-bottom: 16px; }
.car-image-wrap img { max-width: 100%; max-height: 100%; object-fit: contain; filter: drop-shadow(0 14px 28px rgba(0,0,0,.14)); transition: transform .4s var(--ease-back); }
.col-vehicle:hover .car-image-wrap img { transform: translateY(-8px) scale(1.05); }

/* ✅ FIXED: car name — mixed case, no uppercase */
.car-name {
    font-family: var(--font);
    font-size: 1.2rem; font-weight: 700;
    text-align: center; margin-bottom: 8px;
    line-height: 1.2; letter-spacing: .02em;
}

.car-tags { display: flex; flex-wrap: wrap; gap: 6px; justify-content: center; margin-bottom: 18px; }
.car-tag { background: var(--off); font-size: 11px; padding: 4px 10px; border-radius: 999px; color: rgba(0,0,0,.6); border: 1px solid var(--light); }

/* CTA */
.col-cta { display: flex; flex-direction: column; gap: 8px; width: 100%; padding: 0 20px 24px; }
.btn-configure {
    background: var(--black); color: var(--white); border: none;
    padding: 13px; font-family: var(--font);
    font-size: 11px; letter-spacing: .2em; text-transform: uppercase;
    cursor: none; width: 100%; border-radius: 6px;
    transition: background .2s; position: relative; overflow: hidden;
    text-align: center; text-decoration: none; display: block;
}
.btn-configure::before { content: ''; position: absolute; inset: 0; background: rgba(255,255,255,.12); transform: scaleX(0); transform-origin: left; transition: transform .35s ease; }
.btn-configure:hover::before { transform: scaleX(1); }
.btn-configure:hover { background: #222; }

/* "Change model" — tetap uppercase, label kecil */
.change-label { display: flex; align-items: center; justify-content: center; gap: 6px; font-size: 11px; letter-spacing: .12em; text-transform: uppercase; color: var(--gray); cursor: none; text-decoration: none; transition: color .2s; padding: 4px 0; }
.change-label:hover { color: var(--black); }

/* Spec value */
.spec-value-cell { padding: 18px 20px; border-bottom: 1px solid var(--light); font-size: .9rem; color: var(--black); display: flex; align-items: center; min-height: 62px; font-weight: 400; transition: background .2s; }
.col-vehicle:hover .spec-value-cell { background: rgba(0,0,0,.012); }
.spec-value-cell.empty-cell { color: rgba(0,0,0,.2); }

/* Picker overlay */
.picker-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.55); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(6px); }
.picker-overlay.open { display: flex; animation: fadeIn .25s ease; }
.picker-modal { background: var(--white); border-radius: 20px; width: min(780px, 92vw); max-height: 82vh; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 30px 80px rgba(0,0,0,.25); animation: slideUp .3s var(--ease); }
.picker-top { padding: 26px 30px 18px; border-bottom: 1px solid var(--light); display: flex; align-items: center; justify-content: space-between; }

/* ✅ FIXED: picker modal title — mixed case */
.picker-top h2 {
    font-family: var(--font);
    font-size: 1.3rem; font-weight: 700;
    letter-spacing: .02em;
}

.picker-close { background: none; border: none; font-size: 1.2rem; cursor: none; color: var(--gray); padding: 6px; border-radius: 8px; transition: background .2s, color .2s; }
.picker-close:hover { background: var(--off); color: var(--black); }
.picker-search { padding: 14px 30px; border-bottom: 1px solid var(--light); }
.picker-search input { width: 100%; padding: 11px 16px; border: 1px solid var(--light); border-radius: 8px; font-size: .9rem; font-family: var(--font); outline: none; transition: border .2s; background: var(--off); }
.picker-search input:focus { border-color: var(--black); background: var(--white); }
.picker-cats { display: flex; gap: 6px; padding: 12px 30px; overflow-x: auto; border-bottom: 1px solid var(--light); scrollbar-width: none; }
.picker-cats::-webkit-scrollbar { display: none; }
/* cat pills — tetap uppercase, label kecil */
.cat-pill { background: none; border: 1px solid var(--light); border-radius: 999px; padding: 6px 16px; font-size: 11px; letter-spacing: .1em; text-transform: uppercase; cursor: none; white-space: nowrap; font-family: var(--font); transition: background .2s, border-color .2s, color .2s; }
.cat-pill.active, .cat-pill:hover { background: var(--black); color: #fff; border-color: var(--black); }
.picker-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 12px; padding: 22px 30px; overflow-y: auto; flex: 1; }
.picker-card { border: 1px solid var(--light); border-radius: 14px; padding: 16px 12px 14px; cursor: none; transition: border-color .2s, box-shadow .2s, transform .2s; display: flex; flex-direction: column; align-items: center; gap: 8px; text-align: center; background: var(--white); }
.picker-card:hover { border-color: var(--black); box-shadow: 0 8px 24px rgba(0,0,0,.1); transform: translateY(-3px); }
.picker-card img { width: 100%; max-height: 76px; object-fit: contain; filter: drop-shadow(0 4px 10px rgba(0,0,0,.1)); }

/* ✅ FIXED: picker card name — mixed case */
.picker-card-name {
    font-family: var(--font);
    font-size: .82rem; font-weight: 700;
    line-height: 1.3; color: var(--black);
    letter-spacing: .02em;
}
.picker-card-tag { font-size: .68rem; color: var(--gray); }

@keyframes fadeUp  { from { opacity: 0; transform: translateY(24px); } to { opacity: 1; transform: translateY(0); } }
@keyframes fadeIn  { from { opacity: 0; } to { opacity: 1; } }
@keyframes slideUp { from { opacity: 0; transform: translateY(40px); } to { opacity: 1; transform: translateY(0); } }

@media (max-width: 900px) {
    .compare-page { padding: 100px 20px 50px; }
    .compare-grid { grid-template-columns: 130px repeat(3, 1fr); }
    .col-header, .col-labels .col-header { min-height: 260px; }
}
@media (max-width: 640px) {
    .compare-grid { grid-template-columns: 100px repeat(2, 1fr); overflow-x: auto; }
    .col-vehicle:last-child { display: none; }
    html { cursor: auto; }
    #cursor-dot, #cursor-ring { display: none; }
}
</style>
</head>
<body>

<div id="cursor-dot"></div>
<div id="cursor-ring"></div>

<?php include __DIR__ . '/../partials/navbar.php'; ?>

<div class="compare-page">

    <div class="compare-header">
        <a href="models.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Model overview</a>
        <p class="page-eyebrow">Side by side</p>
        <h1 class="page-title">Model Comparison</h1>
    </div>

    <div class="compare-grid" id="compareGrid">

        <div class="col-labels">
            <div class="col-header"><span>Specifications</span></div>
            <?php foreach ($specRows as $row): ?>
            <div class="spec-label-cell"><?= htmlspecialchars($row['label']) ?></div>
            <?php endforeach; ?>
        </div>

        <?php for ($slot = 1; $slot <= 3; $slot++):
            $v = $selected[$slot] ?? null;
        ?>
        <div class="col-vehicle" id="col-<?= $slot ?>">

            <?php if ($v): ?>
            <div class="col-header">
                <?php if (!empty($v['is_new'])): ?><span class="new-badge">New</span><?php endif; ?>
                <button class="remove-btn" onclick="removeSlot(<?= $slot ?>)" title="Remove"><i class="fas fa-times"></i></button>
                <div class="car-image-wrap">
                    <img src="<?= htmlspecialchars($v['image']) ?>" alt="<?= htmlspecialchars($v['name']) ?>">
                </div>
                <div class="car-name"><?= htmlspecialchars($v['name']) ?></div>
                <div class="car-tags">
                    <?php if (!empty($v['fuel_type'])): ?><span class="car-tag"><?= htmlspecialchars($v['fuel_type']) ?></span><?php endif; ?>
                    <?php if (!empty($v['drive_type'])): ?><span class="car-tag"><?= htmlspecialchars($v['drive_type']) ?></span><?php endif; ?>
                </div>
                <div class="col-cta">
                    <?php if (!empty($v['configurator_url'])): ?>
                    <a href="<?= htmlspecialchars($v['configurator_url']) ?>" target="_blank" class="btn-configure">Configure</a>
                    <?php else: ?>
                    <a href="model-detail.php?id=<?= $v['id'] ?>" class="btn-configure">View Model</a>
                    <?php endif; ?>
                    <a href="#" class="change-label" onclick="openPicker(<?= $slot ?>); return false;">
                        <i class="fas fa-right-left" style="font-size:.7rem"></i> Change model
                    </a>
                </div>
            </div>
            <?php else: ?>
            <div class="col-header empty" onclick="openPicker(<?= $slot ?>)">
                <div class="add-btn">
                    <div class="add-btn-icon"><i class="fas fa-plus"></i></div>
                    <span>Select model</span>
                </div>
            </div>
            <?php endif; ?>

            <?php foreach ($specRows as $row):
                $val = '';
                if ($v) {
                    if ($row['key'] === '_power') {
                        $val = ($v['power_kw'] || $v['power_ps'])
                            ? ($v['power_kw'] . ' kW / ' . $v['power_ps'] . ' PS')
                            : '';
                    } elseif ($row['key'] === 'top_speed') {
                        $raw = $v['top_speed'] ?? '';
                        // Tambah km/h jika belum ada satuan
                        $val = $raw && !preg_match('/[a-zA-Z]/', $raw) ? $raw . ' km/h' : $raw;
                    } elseif ($row['key'] === 'acceleration') {
                        $raw = $v['acceleration'] ?? '';
                        // Tambah s jika belum ada satuan
                        $val = $raw && !preg_match('/[a-zA-Z]/', $raw) ? $raw . ' s' : $raw;
                    } else {
                        $val = $v[$row['key']] ?? '';
                    }
                }
            ?>
            <div class="spec-value-cell <?= !$v ? 'empty-cell' : '' ?>">
                <?= $val ? htmlspecialchars($val) : ($v ? '—' : '') ?>
            </div>
            <?php endforeach; ?>

        </div>
        <?php endfor; ?>

    </div>
</div>

<!-- Picker Modal -->
<div class="picker-overlay" id="pickerOverlay" onclick="closePicker(event)">
    <div class="picker-modal" onclick="event.stopPropagation()">
        <div class="picker-top">
            <h2>Select a model</h2>
            <button class="picker-close" onclick="closePicker()"><i class="fas fa-times"></i></button>
        </div>
        <div class="picker-search">
            <input type="text" id="pickerSearch" placeholder="Search model…" oninput="filterPicker()">
        </div>
        <div class="picker-cats" id="pickerCats">
            <?php foreach ($categories as $cat): ?>
            <button class="cat-pill <?= $cat['slug'] === 'all' ? 'active' : '' ?>"
                    data-slug="<?= $cat['slug'] ?>" onclick="setCat(this)">
                <?= htmlspecialchars($cat['name']) ?>
            </button>
            <?php endforeach; ?>
        </div>
        <div class="picker-grid" id="pickerGrid">
            <?php foreach ($allVariants as $pv): ?>
            <div class="picker-card"
                 data-id="<?= $pv['id'] ?>"
                 data-cat="<?= $pv['category_id'] ?>"
                 data-name="<?= strtolower(htmlspecialchars($pv['name'])) ?>"
                 onclick="selectModel(<?= $pv['id'] ?>)">
                <img src="<?= htmlspecialchars($pv['image']) ?>" alt="<?= htmlspecialchars($pv['name']) ?>" loading="lazy">
                <div class="picker-card-name"><?= htmlspecialchars($pv['name']) ?></div>
                <?php if (!empty($pv['fuel_type'])): ?>
                <div class="picker-card-tag"><?= htmlspecialchars($pv['fuel_type']) ?></div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>

<script>
/* Custom cursor */
const dot  = document.getElementById('cursor-dot');
const ring = document.getElementById('cursor-ring');
let mx = 0, my = 0, rx = 0, ry = 0;
window.addEventListener('mousemove', e => { mx = e.clientX; my = e.clientY; }, { passive: true });
(function tick() {
    rx += (mx - rx) * 0.16; ry += (my - ry) * 0.16;
    if (dot)  dot.style.cssText  += `left:${mx}px;top:${my}px`;
    if (ring) ring.style.cssText += `left:${rx}px;top:${ry}px`;
    requestAnimationFrame(tick);
})();
document.querySelectorAll('a,button,input,label,.cat-pill').forEach(el => {
    el.addEventListener('mouseenter', () => document.body.classList.add('c-link'));
    el.addEventListener('mouseleave', () => document.body.classList.remove('c-link'));
});

/* Navbar scroll */
window.addEventListener('scroll', () => {
    document.querySelector('.navbar')?.classList.toggle('scrolled', window.scrollY > 50);
}, { passive: true });

/* Picker */
let activeSlot = 1;
let activeCat  = 'all';

<?php $catMapJson = json_encode(array_column($categories, 'id', 'slug')); ?>
const catMap = <?= $catMapJson ?>;

function getParams() { return new URLSearchParams(window.location.search); }
function buildUrl(p)  { const s = p.toString(); return window.location.pathname + (s ? '?' + s : ''); }

function openPicker(slot) {
    activeSlot = slot;
    document.getElementById('pickerOverlay').classList.add('open');
    document.getElementById('pickerSearch').value = '';
    filterPicker();
}
function closePicker(e) {
    if (!e || e.target === document.getElementById('pickerOverlay'))
        document.getElementById('pickerOverlay').classList.remove('open');
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closePicker(); });

function selectModel(id) { const p = getParams(); p.set('model' + activeSlot, id); window.location.href = buildUrl(p); }
function removeSlot(slot) { const p = getParams(); p.delete('model' + slot); window.location.href = buildUrl(p); }

function setCat(pill) {
    document.querySelectorAll('.cat-pill').forEach(p => p.classList.remove('active'));
    pill.classList.add('active');
    activeCat = pill.dataset.slug;
    filterPicker();
}

function filterPicker() {
    const q     = document.getElementById('pickerSearch').value.toLowerCase().trim();
    const catId = catMap[activeCat];
    document.querySelectorAll('.picker-card').forEach(card => {
        const nm = card.dataset.name.includes(q);
        const cm = activeCat === 'all' || parseInt(card.dataset.cat) === catId;
        card.style.display = (nm && cm) ? '' : 'none';
    });
}
</script>
</body>
</html>