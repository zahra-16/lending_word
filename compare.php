<?php
/**
 * Vehicle Comparison Page
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/app/models/Vehicle.php';
require_once __DIR__ . '/app/models/NavbarLink.php';
require_once __DIR__ . '/app/models/FooterSection.php';
require_once __DIR__ . '/app/models/Content.php';

$vehicleModel       = new Vehicle();
$navbarModel        = new NavbarLink();
$footerSectionModel = new FooterSection();
$contentModel       = new Content();

$socialLinks    = $footerSectionModel->getSocialLinks(); // ← sama kayak models.php

$vehicleIds = isset($_GET['vehicles']) ? explode(',', $_GET['vehicles']) : [];
$vehicleIds = array_filter(array_map('intval', $vehicleIds));
$vehicleIds = array_slice($vehicleIds, 0, 3);

$vehicles = [];
foreach ($vehicleIds as $id) {
    $v = $vehicleModel->getById($id);
    if ($v) $vehicles[] = $v;
}

$navbarLinks    = $navbarModel->getAll();
$footerSections = $footerSectionModel->getAllWithLinks();
$getContent = function($section, $key) use ($contentModel) { return $contentModel->get($section, $key); };
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Comparison - Porsche Finder</title>
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
            --ease-back: cubic-bezier(0.34, 1.56, 0.64, 1);
            --font:      "Porsche Next", "Arial Narrow", Arial, "Helvetica Neue", Helvetica, sans-serif;
        }

        html { cursor: none; scroll-behavior: smooth; }
        body { background: var(--white); color: var(--black); font-family: var(--font); font-weight: 300; overflow-x: hidden; }

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

        @keyframes fadeUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        /* ── NAVBAR ── */
        .navbar { background: transparent !important; transition: background .4s ease, box-shadow .4s ease; }
        .navbar.scrolled { background: rgba(255,255,255,0.92) !important; backdrop-filter: blur(16px); box-shadow: 0 1px 0 rgba(0,0,0,.07); }
        .navbar .navbar-brand, .navbar .navbar-menu a { color: var(--black) !important; filter: none !important; }
        .navbar-menu a::after { background: var(--black) !important; }

        .compare-header {
            padding: 160px 60px 60px;
            border-bottom: 1px solid var(--light);
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            opacity: 0;
            animation: fadeUp .8s .2s var(--ease) forwards;
        }
        .compare-eyebrow {
            font-family: var(--font);
            font-size: 10px; letter-spacing: .35em; text-transform: uppercase;
            color: var(--gray); margin-bottom: 14px;
            display: flex; align-items: center; gap: 12px;
        }
        .compare-eyebrow::before { content: ''; display: block; width: 28px; height: 1px; background: var(--gray); }
        .compare-header h1 { font-family: var(--font); font-size: clamp(2.5rem, 5vw, 5rem); font-weight: 700; letter-spacing: .02em; line-height: .95; margin: 0; }
        .compare-header p { font-family: var(--font); font-size: 1rem; color: var(--gray); margin-top: 14px; }

        .btn-back {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 11px 22px; background: var(--black); color: var(--white);
            text-decoration: none; font-family: var(--font);
            font-size: 0.75rem; font-weight: 500; letter-spacing: .15em; text-transform: uppercase;
            position: relative; overflow: hidden; transition: box-shadow .3s ease; flex-shrink: 0;
        }
        .btn-back::before { content: ''; position: absolute; inset: 0; background: rgba(255,255,255,.12); transform: scaleX(0); transform-origin: left; transition: transform .4s var(--ease); }
        .btn-back:hover::before { transform: scaleX(1); }
        .btn-back:hover { box-shadow: 0 8px 24px rgba(0,0,0,.22); color: var(--white); }
        .btn-back i, .btn-back span { position: relative; z-index: 1; }

        .compare-container {
            max-width: 1600px;
            margin: 0 auto;
            padding: 60px 60px 100px;
            opacity: 0;
            animation: fadeUp .8s .4s var(--ease) forwards;
        }

        .empty-state { text-align: center; padding: 120px 20px; border: 1px solid var(--light); border-radius: 16px; }
        .empty-state i { font-size: 3.5rem; color: var(--light); margin-bottom: 28px; display: block; }
        .empty-state h2 { font-family: var(--font); font-size: 2rem; font-weight: 700; letter-spacing: .02em; margin-bottom: 12px; }
        .empty-state p { font-family: var(--font); font-size: 0.95rem; color: var(--gray); margin-bottom: 40px; }
        .btn-browse {
            display: inline-block; padding: 14px 36px;
            background: var(--black); color: var(--white);
            font-family: var(--font); font-size: 0.78rem; font-weight: 500; letter-spacing: .15em; text-transform: uppercase;
            text-decoration: none; position: relative; overflow: hidden; transition: box-shadow .3s ease;
        }
        .btn-browse::before { content: ''; position: absolute; inset: 0; background: rgba(255,255,255,.12); transform: scaleX(0); transform-origin: left; transition: transform .4s var(--ease); }
        .btn-browse:hover::before { transform: scaleX(1); }
        .btn-browse:hover { box-shadow: 0 12px 28px rgba(0,0,0,.22); }

        .selection-bar { margin-bottom: 60px; }
        .selection-bar-title { font-family: var(--font); font-size: 10px; letter-spacing: .3em; text-transform: uppercase; color: var(--gray); margin-bottom: 20px; }
        .selection-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }

        .selection-slot { background: var(--white); border: 1px solid var(--light); border-radius: 16px; overflow: hidden; position: relative; transition: border-color .3s ease, box-shadow .4s ease; }
        .selection-slot.filled:hover { box-shadow: 0 16px 50px rgba(0,0,0,.08); border-color: rgba(0,0,0,.1); }

        .slot-empty { display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 320px; cursor: none; padding: 40px 20px; color: var(--gray); background: var(--off); transition: background .25s ease; }
        .slot-empty:hover { background: var(--light); }
        .slot-empty i { font-size: 2rem; color: var(--light); margin-bottom: 16px; }
        .slot-empty-title { font-family: var(--font); font-size: 0.88rem; font-weight: 500; color: var(--black); margin-bottom: 6px; }
        .slot-empty-sub { font-family: var(--font); font-size: 0.82rem; color: var(--gray); }

        .slot-vehicle { display: flex; flex-direction: column; width: 100%; }
        .slot-image { width: 100%; height: 220px; object-fit: cover; display: block; transition: transform .5s var(--ease); }
        .selection-slot:hover .slot-image { transform: scale(1.04); }
        .slot-info { padding: 20px 22px; flex: 1; display: flex; flex-direction: column; }
        .slot-title { font-family: var(--font); font-size: 1.2rem; font-weight: 600; letter-spacing: .02em; margin-bottom: 6px; }
        .slot-condition { font-family: var(--font); font-size: 10px; text-transform: uppercase; letter-spacing: .15em; color: var(--gray); margin-bottom: 12px; }
        .slot-price { font-family: var(--font); font-size: 1.4rem; font-weight: 600; letter-spacing: .02em; margin-bottom: 16px; margin-top: auto; }
        .slot-actions { display: flex; gap: 10px; margin-top: 4px; }

        .btn-replace { padding: 10px 16px; background: transparent; border: 1.5px solid var(--light); border-radius: 6px; font-family: var(--font); font-size: 0.75rem; font-weight: 500; letter-spacing: .1em; text-transform: uppercase; cursor: none; color: var(--black); display: flex; align-items: center; gap: 6px; transition: border-color .2s ease, background .2s ease, color .2s ease; }
        .btn-replace:hover { border-color: var(--black); background: var(--black); color: var(--white); }

        .btn-remove-x { position: absolute; top: 12px; right: 12px; width: 32px; height: 32px; background: rgba(255,255,255,0.9); border: 1px solid var(--light); border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: none; transition: all .3s ease; font-size: 1.1rem; color: var(--gray); z-index: 10; }
        .btn-remove-x:hover { background: var(--black); color: var(--white); border-color: var(--black); }

        .btn-view-details { flex: 1; padding: 10px 16px; background: var(--black); color: var(--white); border: none; font-family: var(--font); font-size: 0.75rem; font-weight: 500; letter-spacing: .1em; text-transform: uppercase; text-align: center; text-decoration: none; cursor: none; position: relative; overflow: hidden; transition: box-shadow .3s ease; }
        .btn-view-details::before { content: ''; position: absolute; inset: 0; background: rgba(255,255,255,.12); transform: scaleX(0); transform-origin: left; transition: transform .4s var(--ease); }
        .btn-view-details:hover::before { transform: scaleX(1); }
        .btn-view-details:hover { color: var(--white); }

        .comparison-table { border: 1px solid var(--light); border-radius: 16px; overflow: hidden; }
        .comparison-section { border-bottom: 1px solid var(--light); }
        .comparison-section:last-child { border-bottom: none; }
        .section-header { padding: 24px 32px; font-family: var(--font); font-size: 1.3rem; font-weight: 600; letter-spacing: .02em; color: var(--black); background: var(--off); border-bottom: 1px solid var(--light); }
        .comparison-row { display: grid; grid-template-columns: 200px repeat(3, 1fr); border-bottom: 1px solid var(--light); }
        .comparison-row:last-child { border-bottom: none; }
        .row-label { padding: 16px 24px; font-family: var(--font); font-size: 0.82rem; color: var(--gray); background: var(--off); border-right: 1px solid var(--light); display: flex; align-items: center; letter-spacing: .02em; }
        .row-value { padding: 16px 24px; font-family: var(--font); font-size: 0.88rem; font-weight: 400; color: var(--black); display: flex; align-items: center; border-right: 1px solid var(--light); transition: background .2s ease; }
        .row-value:last-child { border-right: none; }
        .row-value:hover { background: var(--off); }
        .row-value.empty { color: var(--light); }
        .row-value.highlight { font-family: var(--font); font-size: 1rem; font-weight: 600; letter-spacing: .02em; }

        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; padding: 20px; backdrop-filter: blur(4px); }
        .modal-overlay.active { display: flex; }
        .modal-content { background: var(--white); border-radius: 16px; max-width: 600px; width: 100%; max-height: 80vh; overflow: hidden; display: flex; flex-direction: column; box-shadow: 0 40px 100px rgba(0,0,0,.2); }
        .modal-header { padding: 28px 32px; border-bottom: 1px solid var(--light); position: relative; }
        .modal-header h2 { font-family: var(--font); font-size: 1.6rem; font-weight: 700; letter-spacing: .02em; margin-bottom: 6px; }
        .modal-header p { font-family: var(--font); font-size: 0.88rem; color: var(--gray); }
        .modal-close { position: absolute; top: 20px; right: 20px; background: var(--off); border: none; font-size: 1.3rem; color: var(--gray); cursor: none; width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border-radius: 50%; transition: all .3s ease; }
        .modal-close:hover { background: var(--black); color: var(--white); }
        .modal-body { padding: 24px 32px; overflow-y: auto; flex: 1; scrollbar-width: thin; scrollbar-color: var(--light) transparent; }
        .modal-vehicle-list { display: flex; flex-direction: column; gap: 12px; }
        .modal-vehicle-item { display: grid; grid-template-columns: 80px 1fr auto; gap: 16px; padding: 16px; border: 1.5px solid var(--light); border-radius: 12px; cursor: none; transition: border-color .25s ease, background .25s ease; }
        .modal-vehicle-item:not(.disabled):hover { border-color: var(--black); background: var(--off); }
        .modal-vehicle-item.disabled { opacity: 0.5; cursor: not-allowed; }
        .modal-vehicle-image { width: 80px; height: 60px; object-fit: cover; border-radius: 8px; }
        .modal-vehicle-info { display: flex; flex-direction: column; justify-content: center; }
        .modal-vehicle-title { font-family: var(--font); font-size: 1rem; font-weight: 600; letter-spacing: .02em; margin-bottom: 4px; }
        .modal-vehicle-meta { font-family: var(--font); font-size: 0.82rem; color: var(--gray); }
        .modal-vehicle-action { display: flex; align-items: center; }
        .btn-select { padding: 9px 18px; background: var(--black); color: var(--white); border: none; border-radius: 6px; font-family: var(--font); font-size: 0.75rem; font-weight: 500; letter-spacing: .1em; text-transform: uppercase; cursor: none; transition: box-shadow .3s ease; }
        .btn-select:hover { box-shadow: 0 6px 18px rgba(0,0,0,.2); }
        .badge-selected { padding: 7px 14px; background: #e8f5e9; color: #2e7d32; border: 1px solid #4caf50; border-radius: 6px; font-family: var(--font); font-size: 0.75rem; font-weight: 500; letter-spacing: .05em; }
        .modal-empty { text-align: center; padding: 50px 20px; }
        .modal-empty p { font-family: var(--font); color: var(--gray); margin-bottom: 20px; }

        @media (max-width: 1200px) {
            .selection-grid { grid-template-columns: 1fr; }
            .comparison-row { grid-template-columns: 1fr; }
            .row-label { background: var(--off); font-weight: 500; border-right: none; border-bottom: 1px solid var(--light); }
            .row-value { border-right: none; padding: 14px 24px; }
        }
        @media (max-width: 768px) {
            .compare-header { padding: 120px 24px 40px; flex-direction: column; align-items: flex-start; gap: 20px; }
            .compare-container { padding: 40px 24px 80px; }
            html { cursor: auto; }
            #cursor-dot, #cursor-ring { display: none; }
            .btn-back, .btn-browse, .btn-replace, .btn-remove-x, .btn-select, .btn-view-details, .modal-close, .slot-empty { cursor: pointer; }
        }
    </style>
</head>
<body>

<div id="cursor-dot"></div>
<div id="cursor-ring"></div>
<div id="progress"></div>

<?php include __DIR__ . '/app/views/partials/navbar.php'; ?>

<!-- Header -->
<div class="compare-header">
    <div>
        <p class="compare-eyebrow">Porsche Finder</p>
        <h1>Vehicle<br>Comparison</h1>
        <p>Compare vehicles side by side to find your perfect Porsche.</p>
    </div>
    <a href="/lending_word/finder.php" class="btn-back">
        <i class="fas fa-arrow-left"></i>
        <span>Back to Finder</span>
    </a>
</div>

<!-- Main -->
<div class="compare-container">
    <?php if (empty($vehicles)): ?>
    <div class="empty-state">
        <i class="fas fa-exchange-alt"></i>
        <h2>No vehicles selected</h2>
        <p>Browse our inventory and select vehicles to compare side by side.</p>
        <a href="/lending_word/finder.php" class="btn-browse">Browse Vehicles</a>
    </div>
    <?php else: ?>

    <!-- Selection -->
    <div class="selection-bar">
        <p class="selection-bar-title">Your selection</p>
        <div class="selection-grid">
            <?php for ($i = 0; $i < 3; $i++): ?>
            <div class="selection-slot <?= isset($vehicles[$i]) ? 'filled' : '' ?>">
                <?php if (isset($vehicles[$i])): ?>
                    <div class="slot-vehicle">
                        <button class="btn-remove-x" onclick="removeVehicle(<?= $vehicles[$i]['id'] ?>)" title="Remove">×</button>
                        <img src="<?= htmlspecialchars($vehicles[$i]['main_image_url']) ?>" alt="<?= htmlspecialchars($vehicles[$i]['title']) ?>" class="slot-image">
                        <div class="slot-info">
                            <div class="slot-title"><?= htmlspecialchars($vehicles[$i]['title']) ?></div>
                            <div class="slot-condition"><?= htmlspecialchars($vehicles[$i]['condition']) ?></div>
                            <div class="slot-price">Rp <?= number_format($vehicles[$i]['price'], 0, ',', '.') ?></div>
                            <div class="slot-actions">
                                <a href="/lending_word/finder_detail.php?id=<?= $vehicles[$i]['id'] ?>" class="btn-view-details">Details</a>
                                <button class="btn-replace" onclick="openModal(<?= $i ?>)"><i class="fas fa-sync-alt"></i> Replace</button>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="slot-empty" onclick="openModal(<?= $i ?>)">
                        <i class="fas fa-plus"></i>
                        <div class="slot-empty-title">Add vehicle</div>
                        <div class="slot-empty-sub">Select from saved</div>
                    </div>
                <?php endif; ?>
            </div>
            <?php endfor; ?>
        </div>
    </div>

    <!-- Comparison Table -->
    <div class="comparison-table">
        <div class="comparison-section">
            <div class="section-header">Overview</div>
            <?php
            $rows = [
                ['label' => 'Price',     'key' => null, 'format' => fn($v) => 'Rp ' . number_format($v['price'], 0, ',', '.'), 'highlight' => true],
                ['label' => 'Condition', 'key' => 'condition'],
                ['label' => 'Mileage',   'key' => null, 'format' => fn($v) => !empty($v['mileage']) ? number_format($v['mileage'],0,',','.') . ' km' : '—'],
            ];
            foreach ($rows as $row): $isHighlight = $row['highlight'] ?? false; ?>
            <div class="comparison-row">
                <div class="row-label"><?= $row['label'] ?></div>
                <?php foreach ($vehicles as $v): ?>
                <div class="row-value <?= $isHighlight ? 'highlight' : '' ?>">
                    <?php if (isset($row['format'])): echo ($row['format'])($v); else: echo htmlspecialchars($v[$row['key']] ?? '—'); endif; ?>
                </div>
                <?php endforeach; ?>
                <?php for ($i = count($vehicles); $i < 3; $i++): ?><div class="row-value empty">—</div><?php endfor; ?>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="comparison-section">
            <div class="section-header">Engine &amp; Performance</div>
            <?php
            $perfRows = [
                ['Fuel Type',    'fuel_type',    null, false],
                ['Power',        null,           fn($v) => !empty($v['power_kw']) && !empty($v['power_hp']) ? $v['power_kw'] . ' kW / ' . $v['power_hp'] . ' hp' : '—', true],
                ['Transmission', 'transmission', null, false],
                ['Drive Type',   'drive_type',   null, false],
                ['0–100 km/h',   null,           fn($v) => !empty($v['acceleration_0_100']) ? $v['acceleration_0_100'] . ' s' : '—', false],
                ['Top Speed',    null,           fn($v) => !empty($v['top_speed']) ? $v['top_speed'] . ' km/h' : '—', false],
            ];
            foreach ($perfRows as [$label, $key, $fmt, $hl]): ?>
            <div class="comparison-row">
                <div class="row-label"><?= $label ?></div>
                <?php foreach ($vehicles as $v): ?>
                <div class="row-value <?= $hl ? 'highlight' : '' ?>"><?php echo $fmt ? $fmt($v) : htmlspecialchars($v[$key] ?? '—'); ?></div>
                <?php endforeach; ?>
                <?php for ($i = count($vehicles); $i < 3; $i++): ?><div class="row-value empty">—</div><?php endfor; ?>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="comparison-section">
            <div class="section-header">Design &amp; Interior</div>
            <?php
            $designRows = [
                ['Exterior Color',    'exterior_color'],
                ['Interior Color',    'interior_color'],
                ['Interior Material', 'interior_material'],
                ['Seats',             'seats'],
            ];
            foreach ($designRows as [$label, $key]): ?>
            <div class="comparison-row">
                <div class="row-label"><?= $label ?></div>
                <?php foreach ($vehicles as $v): ?><div class="row-value"><?= htmlspecialchars($v[$key] ?? '—') ?></div><?php endforeach; ?>
                <?php for ($i = count($vehicles); $i < 3; $i++): ?><div class="row-value empty">—</div><?php endfor; ?>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="comparison-section">
            <div class="section-header">Location</div>
            <div class="comparison-row">
                <div class="row-label">Porsche Center</div>
                <?php foreach ($vehicles as $v): ?><div class="row-value"><?= htmlspecialchars($v['center_name'] ?? '—') ?></div><?php endforeach; ?>
                <?php for ($i = count($vehicles); $i < 3; $i++): ?><div class="row-value empty">—</div><?php endfor; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Modal -->
<div class="modal-overlay" id="vehicleModal">
    <div class="modal-content">
        <button class="modal-close" onclick="closeModal()">&times;</button>
        <div class="modal-header">
            <h2>Add Vehicle</h2>
            <p>Select from your saved vehicles to compare</p>
        </div>
        <div class="modal-body">
            <div id="modalVehicleList" class="modal-vehicle-list"></div>
        </div>
    </div>
</div>

<script>
/* ─── Cursor ─── */
const dot  = document.getElementById('cursor-dot');
const ring = document.getElementById('cursor-ring');
let mx = 0, my = 0, rx = 0, ry = 0;

window.addEventListener('mousemove', e => { mx = e.clientX; my = e.clientY; }, { passive: true });
(function tick() {
    rx += (mx-rx)*.16; ry += (my-ry)*.16;
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
const progressEl = document.getElementById('progress'), navbar = document.querySelector('.navbar');
window.addEventListener('scroll', () => {
    progressEl.style.width = (window.scrollY / (document.body.scrollHeight - window.innerHeight) * 100) + '%';
    navbar?.classList.toggle('scrolled', window.scrollY > 50);
}, { passive: true });

/* ─── Modal ─── */
let currentSlotIndex = null;
function openModal(slotIndex) { currentSlotIndex = slotIndex; document.getElementById('vehicleModal').classList.add('active'); loadSavedVehicles(); }
function closeModal() { document.getElementById('vehicleModal').classList.remove('active'); currentSlotIndex = null; }
document.getElementById('vehicleModal')?.addEventListener('click', e => { if (e.target.id === 'vehicleModal') closeModal(); });

function loadSavedVehicles() {
    const list = document.getElementById('modalVehicleList');
    list.innerHTML = '<div style="text-align:center;padding:30px;color:var(--gray);font-family:var(--font);">Loading…</div>';
    const params = new URLSearchParams(window.location.search);
    const current = params.get('vehicles') ? params.get('vehicles').split(',').map(Number) : [];
    fetch('/lending_word/saved_vehicles_api.php?action=list')
        .then(r => r.json())
        .then(data => {
            if (data.success && data.vehicles?.length) {
                list.innerHTML = data.vehicles.map(v => {
                    const isSel = current.includes(v.id);
                    return `<div class="modal-vehicle-item ${isSel ? 'disabled' : ''}" ${!isSel ? `onclick="selectVehicle(${v.id})"` : ''}>
                        <img src="${v.main_image_url}" alt="${v.title}" class="modal-vehicle-image">
                        <div class="modal-vehicle-info"><div class="modal-vehicle-title">${v.title}</div><div class="modal-vehicle-meta">${v.condition} · Rp ${parseInt(v.price).toLocaleString('id-ID')}</div></div>
                        <div class="modal-vehicle-action">${isSel ? '<span class="badge-selected">Selected</span>' : `<button class="btn-select" onclick="event.stopPropagation();selectVehicle(${v.id})">Add</button>`}</div>
                    </div>`;
                }).join('');
            } else {
                list.innerHTML = `<div class="modal-empty"><p>No saved vehicles yet.</p><a href="/lending_word/finder.php" style="font-family:var(--font);color:var(--black);text-decoration:underline;font-size:.88rem;">Browse vehicles →</a></div>`;
            }
        })
        .catch(() => {
            list.innerHTML = `<div class="modal-empty"><p>Could not load saved vehicles.</p><button onclick="loadSavedVehicles()" style="padding:8px 16px;background:var(--black);color:#fff;border:none;cursor:pointer;font-family:var(--font);font-size:.82rem;letter-spacing:.1em;text-transform:uppercase;">Retry</button></div>`;
        });
}

function selectVehicle(vehicleId) {
    const params = new URLSearchParams(window.location.search);
    let vehicles = params.get('vehicles') ? params.get('vehicles').split(',') : [];
    if (!vehicles.includes(vehicleId.toString())) vehicles.push(vehicleId.toString());
    vehicles = vehicles.slice(0, 3);
    window.location.href = '?vehicles=' + vehicles.join(',');
}
function removeVehicle(vehicleId) {
    const params = new URLSearchParams(window.location.search);
    const vehicles = params.get('vehicles') ? params.get('vehicles').split(',') : [];
    const updated = vehicles.filter(id => id !== vehicleId.toString());
    if (!updated.length) window.location.href = '/lending_word/finder.php';
    else window.location.href = '?vehicles=' + updated.join(',');
}
</script>

<?php include __DIR__ . '/app/views/partials/footer.php'; ?>
</body>
</html>