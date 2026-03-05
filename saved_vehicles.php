<?php
/**
 * Saved Vehicles Page
 * Place this file in: /lending_word/saved_vehicles.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/app/models/SavedVehicle.php';
require_once __DIR__ . '/app/models/NavbarLink.php';
require_once __DIR__ . '/app/models/FooterSection.php';
require_once __DIR__ . '/app/models/Content.php';
require_once __DIR__ . '/app/helpers/pg_array_helper.php';

$savedVehicleModel  = new SavedVehicle();
$navbarModel        = new NavbarLink();
$footerSectionModel = new FooterSection();
$contentModel       = new Content();

$savedVehicles  = $savedVehicleModel->getAllSaved();
$savedCount     = count($savedVehicles);

$navbarLinks    = $navbarModel->getAll();
$footerSections = $footerSectionModel->getAllWithLinks();
$socialLinks    = $footerSectionModel->getSocialLinks(); // ← sama kayak models.php

$getContent = function($section, $key) use ($contentModel) {
    return $contentModel->get($section, $key);
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saved Vehicles – Porsche Finder</title>
    <link rel="icon" type="image/png" href="/lending_word/public/assets/images/porsche-logo.png">
    <link rel="stylesheet" href="/lending_word/public/assets/css/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --white:     #ffffff;
            --off:       #f6f6f3;
            --black:     #0a0a0a;
            --gray:      #888;
            --light:     #e6e6e0;
            --ease:      cubic-bezier(0.16, 1, 0.3, 1);
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
            transition: width .3s var(--ease), height .3s var(--ease), opacity .3s ease, background .2s ease, border-color .2s ease;
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
        body.cursor-dark #cursor-dot  { background: #ffffff; }
        body.cursor-dark #cursor-ring { border-color: rgba(255,255,255,0.5); }
        body.c-link #cursor-dot  { width: 52px; height: 52px; background: rgba(0,0,0,.07); }
        body.c-link #cursor-ring { opacity: 0; }
        body.cursor-dark.c-link #cursor-dot { background: rgba(255,255,255,.15); }
        body.c-click #cursor-dot  { transform: translate(-50%, -50%) scale(2); opacity: 0; }
        body.c-click #cursor-ring { transform: translate(-50%, -50%) scale(1.4); opacity: 0; }

        /* ── PROGRESS ── */
        #progress { position: fixed; top: 0; left: 0; height: 2px; width: 0; background: var(--gray); z-index: 8000; transition: width .1s linear; }

        /* ── NAVBAR ── */
        .navbar { background: transparent !important; transition: background .4s ease, box-shadow .4s ease; }
        .navbar.scrolled { background: rgba(255,255,255,0.92) !important; backdrop-filter: blur(16px); box-shadow: 0 1px 0 rgba(0,0,0,.07); }
        .navbar .navbar-brand, .navbar .navbar-menu a { color: var(--black) !important; filter: none !important; }
        .navbar-menu a::after { background: var(--black) !important; }

        @keyframes fadeUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        /* ── PAGE HEADER ── */
        .saved-header {
            padding: 160px 60px 60px;
            border-bottom: 1px solid var(--light);
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            opacity: 0;
            animation: fadeUp .8s .2s var(--ease) forwards;
        }
        .saved-eyebrow { font-family: var(--font-body); font-size: 10px; letter-spacing: .35em; color: var(--gray); margin-bottom: 14px; display: flex; align-items: center; gap: 12px; }
        .saved-eyebrow::before { content: ''; display: block; width: 28px; height: 1px; background: var(--gray); }
        .saved-header h1 { font-family: var(--font-cond); font-size: clamp(2.5rem,5vw,5rem); font-weight: 700; letter-spacing: .04em; line-height: .95; margin: 0; }
        .saved-header p { font-family: var(--font-body); font-size: 1rem; color: var(--gray); margin-top: 14px; }
        .header-count { font-family: var(--font-cond); font-size: 1rem; font-weight: 500; letter-spacing: .1em; color: var(--gray); flex-shrink: 0; }

        /* ── CONTAINER ── */
        .saved-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 60px 60px 100px;
            opacity: 0;
            animation: fadeUp .8s .4s var(--ease) forwards;
        }

        /* ── EMPTY STATE ── */
        .empty-state { text-align: center; padding: 120px 20px; border: 1px solid var(--light); border-radius: 16px; }
        .empty-state i { font-size: 3.5rem; color: var(--light); margin-bottom: 28px; display: block; }
        .empty-state h2 { font-family: var(--font-cond); font-size: 2rem; font-weight: 700; letter-spacing: .04em; margin-bottom: 12px; }
        .empty-state p { font-family: var(--font-body); font-size: 0.95rem; color: var(--gray); margin-bottom: 40px; }
        .btn-browse {
            display: inline-block; padding: 14px 36px;
            background: var(--black); color: var(--white);
            font-family: var(--font-body); font-size: 0.78rem; font-weight: 500; letter-spacing: .15em;
            text-decoration: none; position: relative; overflow: hidden; transition: box-shadow .3s ease;
        }
        .btn-browse::before { content: ''; position: absolute; inset: 0; background: rgba(255,255,255,.12); transform: scaleX(0); transform-origin: left; transition: transform .4s var(--ease); }
        .btn-browse:hover::before { transform: scaleX(1); }
        .btn-browse:hover { box-shadow: 0 12px 28px rgba(0,0,0,.22); color: var(--white); }

        /* ── VEHICLE LIST ── */
        .saved-vehicles-list { display: flex; flex-direction: column; gap: 1px; background: var(--light); border: 1px solid var(--light); border-radius: 16px; overflow: hidden; }
        .saved-vehicle-card { background: var(--white); transition: background .2s ease; }
        .saved-vehicle-card:hover { background: var(--off); }
        .vehicle-card-content { display: grid; grid-template-columns: 320px 1fr; min-height: 240px; }
        .vehicle-image-wrapper { position: relative; overflow: hidden; background: var(--off); }
        .vehicle-image { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform .6s var(--ease); }
        .saved-vehicle-card:hover .vehicle-image { transform: scale(1.04); }
        .vehicle-info-wrapper { padding: 32px 40px; display: flex; flex-direction: column; border-left: 1px solid var(--light); }
        .vehicle-eyebrow { font-family: var(--font-body); font-size: 10px; letter-spacing: .3em; color: var(--gray); margin-bottom: 10px; }
        .vehicle-title { font-family: var(--font-cond); font-size: 1.8rem; font-weight: 600; letter-spacing: .03em; line-height: 1; margin-bottom: 6px; }
        .vehicle-title a { color: var(--black); text-decoration: none; transition: opacity .2s ease; }
        .vehicle-title a:hover { opacity: .6; }
        .vehicle-specs { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 20px; }
        .spec-tag { padding: 5px 12px; background: var(--off); border: 1px solid var(--light); font-family: var(--font-body); font-size: 0.75rem; letter-spacing: .05em; color: var(--gray); }
        .vehicle-price-section { margin-top: auto; padding-top: 20px; border-top: 1px solid var(--light); display: flex; align-items: center; justify-content: space-between; gap: 20px; flex-wrap: wrap; }
        .vehicle-price { font-family: var(--font-cond); font-size: 1.8rem; font-weight: 600; letter-spacing: .02em; }
        .price-change { font-family: var(--font-body); font-size: 0.75rem; color: var(--gray); }
        .price-down { color: #2e7d32; }
        .price-up   { color: #c62828; }
        .vehicle-actions { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }

        .btn-details {
            padding: 11px 24px; background: var(--black); color: var(--white);
            font-family: var(--font-body); font-size: 0.75rem; font-weight: 500; letter-spacing: .15em;
            text-decoration: none; position: relative; overflow: hidden; transition: box-shadow .3s ease;
        }
        .btn-details::before { content: ''; position: absolute; inset: 0; background: rgba(255,255,255,.12); transform: scaleX(0); transform-origin: left; transition: transform .4s var(--ease); }
        .btn-details:hover::before { transform: scaleX(1); }
        .btn-details:hover { color: var(--white); box-shadow: 0 8px 24px rgba(0,0,0,.22); }

        .btn-action {
            padding: 10px 18px; background: transparent; border: 1.5px solid var(--light);
            font-family: var(--font-body); font-size: 0.75rem; font-weight: 500; letter-spacing: .1em;
            color: var(--black); cursor: none; display: flex; align-items: center; gap: 6px;
            transition: border-color .2s ease, background .2s ease, color .2s ease;
        }
        .btn-action:hover { border-color: var(--black); background: var(--black); color: var(--white); }
        .btn-action.compare-active { background: var(--black); color: var(--white); border-color: var(--black); }
        .btn-action.saved-btn { border-color: #4caf50; color: #2e7d32; background: #f1faf1; }
        .btn-action.saved-btn:hover { background: #c62828; border-color: #c62828; color: var(--white); }

        .vehicle-center { font-family: var(--font-body); font-size: 0.75rem; color: var(--gray); letter-spacing: .05em; margin-top: 10px; }

        /* ── COMPARISON BAR ── */
        .comparison-bar {
            position: fixed; bottom: -100px; left: 0; right: 0;
            background: var(--black); color: var(--white);
            padding: 22px 60px; z-index: 1000;
            transition: bottom .4s var(--ease);
            box-shadow: 0 -4px 40px rgba(0,0,0,.2);
            display: flex; justify-content: space-between; align-items: center;
        }
        .comparison-bar.active { bottom: 0; }
        .comparison-bar-left { display: flex; align-items: center; gap: 16px; }
        .comparison-bar-label { font-family: var(--font-body); font-size: 0.75rem; letter-spacing: .2em; color: rgba(255,255,255,.5); }
        .comparison-bar-count { font-family: var(--font-cond); font-size: 2rem; font-weight: 700; letter-spacing: .02em; }
        .comparison-bar-sub { font-family: var(--font-body); font-size: 0.8rem; color: rgba(255,255,255,.5); }
        .comparison-bar-actions { display: flex; gap: 12px; }

        .btn-compare-now {
            padding: 12px 32px; background: var(--white); color: var(--black);
            border: none; font-family: var(--font-body); font-size: 0.78rem; font-weight: 500; letter-spacing: .15em;
            cursor: none; position: relative; overflow: hidden; transition: box-shadow .3s ease;
        }
        .btn-compare-now::before { content: ''; position: absolute; inset: 0; background: rgba(0,0,0,.06); transform: scaleX(0); transform-origin: left; transition: transform .4s var(--ease); }
        .btn-compare-now:hover::before { transform: scaleX(1); }
        .btn-compare-now:hover { box-shadow: 0 8px 24px rgba(255,255,255,.15); }

        .btn-clear {
            padding: 12px 24px; background: transparent; color: rgba(255,255,255,.6);
            border: 1.5px solid rgba(255,255,255,.2); font-family: var(--font-body); font-size: 0.78rem; font-weight: 500; letter-spacing: .1em;
            cursor: none; transition: border-color .2s ease, color .2s ease;
        }
        .btn-clear:hover { border-color: rgba(255,255,255,.6); color: var(--white); }

        /* ── FOOTER CTA ── */
        .footer-cta { background: var(--off); border-top: 1px solid var(--light); padding: 80px 60px; text-align: center; }
        .footer-cta p { font-family: var(--font-body); font-size: 0.9rem; color: var(--gray); margin-bottom: 24px; }

        /* ── RESPONSIVE ── */
        @media (max-width: 1024px) {
            .vehicle-card-content { grid-template-columns: 260px 1fr; }
        }
        @media (max-width: 768px) {
            .saved-header { padding: 120px 24px 40px; flex-direction: column; align-items: flex-start; gap: 16px; }
            .saved-container { padding: 40px 24px 80px; }
            .vehicle-card-content { grid-template-columns: 1fr; }
            .vehicle-image-wrapper { height: 240px; }
            .vehicle-info-wrapper { border-left: none; border-top: 1px solid var(--light); }
            .comparison-bar { padding: 18px 24px; }
            html { cursor: auto; }
            #cursor-dot, #cursor-ring { display: none; }
            .btn-details, .btn-action, .btn-compare-now, .btn-clear, .btn-browse { cursor: pointer; }
        }
    </style>
</head>
<body>

<div id="cursor-dot"></div>
<div id="cursor-ring"></div>
<div id="progress"></div>

<?php include __DIR__ . '/app/views/partials/navbar.php'; ?>

<!-- Header -->
<div class="saved-header">
    <div>
        <p class="saved-eyebrow">Porsche Finder</p>
        <h1>Saved<br>Vehicles</h1>
        <p>Your personal shortlist of Porsche models.</p>
    </div>
    <?php if ($savedCount > 0): ?>
    <div class="header-count"><?= $savedCount ?> vehicle<?= $savedCount !== 1 ? 's' : '' ?> saved</div>
    <?php endif; ?>
</div>

<!-- Main -->
<div class="saved-container">
    <?php if (empty($savedVehicles)): ?>
    <div class="empty-state">
        <i class="far fa-bookmark"></i>
        <h2>Nothing saved yet</h2>
        <p>Browse our inventory and save your favourite Porsche models to compare and review later.</p>
        <a href="/lending_word/finder.php" class="btn-browse">Browse Vehicles</a>
    </div>
    <?php else: ?>

    <div class="saved-vehicles-list">
        <?php foreach ($savedVehicles as $vehicle): ?>
        <div class="saved-vehicle-card" data-vehicle-id="<?= $vehicle['id'] ?>">
            <div class="vehicle-card-content">
                <div class="vehicle-image-wrapper">
                    <img src="<?= htmlspecialchars($vehicle['main_image_url']) ?>"
                         alt="<?= htmlspecialchars($vehicle['title']) ?>"
                         class="vehicle-image">
                </div>
                <div class="vehicle-info-wrapper">
                    <div class="vehicle-eyebrow"><?= htmlspecialchars($vehicle['condition']) ?> · Saved <?= date('d M Y', strtotime($vehicle['saved_at'])) ?></div>
                    <h2 class="vehicle-title">
                        <a href="/lending_word/finder_detail.php?id=<?= $vehicle['id'] ?>">
                            <?= htmlspecialchars($vehicle['title']) ?>
                        </a>
                    </h2>

                    <div class="vehicle-specs">
                        <?php if ($vehicle['exterior_color']): ?><span class="spec-tag"><?= htmlspecialchars($vehicle['exterior_color']) ?></span><?php endif; ?>
                        <?php if ($vehicle['interior_color']): ?><span class="spec-tag"><?= htmlspecialchars($vehicle['interior_color']) ?></span><?php endif; ?>
                        <?php if ($vehicle['fuel_type']): ?><span class="spec-tag"><?= htmlspecialchars($vehicle['fuel_type']) ?></span><?php endif; ?>
                        <?php if ($vehicle['power_kw'] && $vehicle['power_hp']): ?><span class="spec-tag"><?= $vehicle['power_kw'] ?> kW / <?= $vehicle['power_hp'] ?> hp</span><?php endif; ?>
                        <?php if ($vehicle['drive_type']): ?><span class="spec-tag"><?= htmlspecialchars($vehicle['drive_type']) ?></span><?php endif; ?>
                        <?php if ($vehicle['transmission']): ?><span class="spec-tag"><?= htmlspecialchars($vehicle['transmission']) ?></span><?php endif; ?>
                    </div>

                    <div class="vehicle-price-section">
                        <div>
                            <div class="vehicle-price">Rp <?= number_format($vehicle['price'], 0, ',', '.') ?></div>
                            <?php if ($vehicle['saved_price'] && $vehicle['saved_price'] != $vehicle['price']): ?>
                            <div class="price-change">
                                At save: Rp <?= number_format($vehicle['saved_price'], 0, ',', '.') ?>
                                <?php if ($vehicle['price'] < $vehicle['saved_price']): ?>
                                <span class="price-down"> ↓ Rp <?= number_format($vehicle['saved_price'] - $vehicle['price'], 0, ',', '.') ?></span>
                                <?php else: ?>
                                <span class="price-up"> ↑ Rp <?= number_format($vehicle['price'] - $vehicle['saved_price'], 0, ',', '.') ?></span>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($vehicle['center_name'])): ?>
                            <div class="vehicle-center"><?= htmlspecialchars($vehicle['center_name']) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="vehicle-actions">
                            <a href="/lending_word/finder_detail.php?id=<?= $vehicle['id'] ?>" class="btn-details">Details</a>
                            <button class="btn-action compare-checkbox" data-vehicle-id="<?= $vehicle['id'] ?>">
                                <i class="fas fa-exchange-alt"></i> <span>Compare</span>
                            </button>
                            <button class="btn-action saved-btn" onclick="unsaveVehicle(<?= $vehicle['id'] ?>)">
                                <i class="fas fa-bookmark"></i> <span>Saved</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php endif; ?>
</div>

<?php if (!empty($savedVehicles)): ?>
<div class="footer-cta">
    <p>Want to add another Porsche to your list?</p>
    <a href="/lending_word/finder.php" class="btn-browse">Browse and save listings</a>
</div>
<?php endif; ?>

<!-- Comparison Bar -->
<div class="comparison-bar" id="comparisonBar">
    <div class="comparison-bar-left">
        <div class="comparison-bar-count" id="comparisonCount">0</div>
        <div>
            <div class="comparison-bar-label">Selected</div>
            <div class="comparison-bar-sub">up to 3 vehicles</div>
        </div>
    </div>
    <div class="comparison-bar-actions">
        <button class="btn-clear" onclick="clearComparison()">Clear</button>
        <button class="btn-compare-now" onclick="compareVehicles()">Compare now</button>
    </div>
</div>

<script>
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

window.addEventListener('mousemove', () => {
    const el = document.elementFromPoint(mx, my);
    const onDark = el && (el.closest('footer') || el.closest('.comparison-bar'));
    document.body.classList.toggle('cursor-dark', !!onDark);
}, { passive: true });

document.querySelectorAll('a, button, input, label').forEach(el => {
    el.addEventListener('mouseenter', () => document.body.classList.add('c-link'));
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

/* ─── Comparison ─── */
let selectedVehicles = [];
const MAX_COMPARISON = 3;

document.querySelectorAll('.compare-checkbox').forEach(btn => {
    btn.addEventListener('click', function() {
        const vehicleId = parseInt(this.dataset.vehicleId);
        if (selectedVehicles.includes(vehicleId)) {
            selectedVehicles = selectedVehicles.filter(id => id !== vehicleId);
            this.classList.remove('compare-active');
            this.innerHTML = '<i class="fas fa-exchange-alt"></i><span>Compare</span>';
        } else {
            if (selectedVehicles.length < MAX_COMPARISON) {
                selectedVehicles.push(vehicleId);
                this.classList.add('compare-active');
                this.innerHTML = '<i class="fas fa-check"></i><span>Selected</span>';
            } else {
                alert('You can compare up to 3 vehicles at a time');
            }
        }
        updateComparisonBar();
    });
});

function updateComparisonBar() {
    const bar   = document.getElementById('comparisonBar');
    const count = document.getElementById('comparisonCount');
    count.textContent = selectedVehicles.length;
    bar.classList.toggle('active', selectedVehicles.length > 0);
}
function compareVehicles() {
    if (selectedVehicles.length < 2) { alert('Select at least 2 vehicles to compare'); return; }
    window.location.href = '/lending_word/compare.php?vehicles=' + selectedVehicles.join(',');
}
function clearComparison() {
    selectedVehicles = [];
    document.querySelectorAll('.compare-checkbox').forEach(btn => {
        btn.classList.remove('compare-active');
        btn.innerHTML = '<i class="fas fa-exchange-alt"></i><span>Compare</span>';
    });
    updateComparisonBar();
}

function unsaveVehicle(vehicleId) {
    if (!confirm('Remove this vehicle from your saved list?')) return;
    fetch('/lending_word/saved_vehicles_api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=unsave&vehicle_id=' + vehicleId
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const card = document.querySelector(`[data-vehicle-id="${vehicleId}"]`);
            if (card) {
                card.style.transition = 'opacity .3s ease, transform .3s ease';
                card.style.opacity = '0'; card.style.transform = 'translateX(-16px)';
                setTimeout(() => {
                    card.remove();
                    if (!document.querySelectorAll('.saved-vehicle-card').length) location.reload();
                }, 320);
            }
        } else { alert('Failed to remove: ' + data.message); }
    })
    .catch(() => alert('An error occurred. Please try again.'));
}
</script>

<?php include __DIR__ . '/app/views/partials/footer.php'; ?>
</body>
</html>