<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../app/models/Vehicle.php';
require_once __DIR__ . '/../app/models/ModelSeries.php';
require_once __DIR__ . '/../app/models/BodyType.php';
require_once __DIR__ . '/../app/models/PorscheCenter.php';

$vehicleModel       = new Vehicle();
$modelSeriesModel   = new ModelSeries();
$bodyTypeModel      = new BodyType();
$porscheCenterModel = new PorscheCenter();

$success     = '';
$editVehicle = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add_vehicle') {
        $vehicleId = $vehicleModel->create($_POST);
        if (!empty($_POST['additional_images'])) {
            $imageUrls = array_filter(array_map('trim', explode("\n", $_POST['additional_images'])));
            $sortOrder = 1;
            foreach ($imageUrls as $imageUrl) {
                if (filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                    $stmt = Database::getInstance()->getConnection()->prepare("
                        INSERT INTO vehicle_images (vehicle_id, image_url, sort_order) VALUES (?, ?, ?)
                    ");
                    $stmt->execute([$vehicleId, $imageUrl, $sortOrder++]);
                }
            }
        }
        $success = "Vehicle added successfully!";
    }

    if ($action === 'update_vehicle') {
        $vehicleModel->update($_POST['id'], $_POST);
        if (isset($_POST['update_images'])) {
            $stmt = Database::getInstance()->getConnection()->prepare("DELETE FROM vehicle_images WHERE vehicle_id = ?");
            $stmt->execute([$_POST['id']]);
            if (!empty($_POST['additional_images'])) {
                $imageUrls = array_filter(array_map('trim', explode("\n", $_POST['additional_images'])));
                $sortOrder = 1;
                foreach ($imageUrls as $imageUrl) {
                    if (filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                        $stmt = Database::getInstance()->getConnection()->prepare("
                            INSERT INTO vehicle_images (vehicle_id, image_url, sort_order) VALUES (?, ?, ?)
                        ");
                        $stmt->execute([$_POST['id'], $imageUrl, $sortOrder++]);
                    }
                }
            }
        }
        $success = "Vehicle updated successfully!";
    }

    if ($action === 'delete_vehicle') {
        $vehicleModel->delete($_POST['id']);
        $success = "Vehicle deleted successfully!";
    }
}

if (isset($_GET['edit'])) {
    $editVehicle = $vehicleModel->getById($_GET['edit']);
    if ($editVehicle) {
        $existingImages = $vehicleModel->getImages($_GET['edit']);
        $editVehicle['additional_images'] = implode("\n", array_column($existingImages, 'image_url'));
    }
}

$vehicles    = $vehicleModel->getAll();
$modelSeries = $modelSeriesModel->getAll();
$bodyTypes   = $bodyTypeModel->getAll();
$centers     = $porscheCenterModel->getAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finder Vehicles — Porsche Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --bg:   #05050a; --bg2: #09090f; --bg3: #0e0e16; --bg4: #13131c; --bg5: #181825;
            --b1: rgba(255,255,255,0.035); --b2: rgba(255,255,255,0.075);
            --b3: rgba(255,255,255,0.13);  --b4: rgba(255,255,255,0.2);
            --t1: #eeeef4; --t2: #777790; --t3: #363648; --t4: #1e1e2c;
            --gold: #c9a84c; --gold2: #e8c97a; --gold3: rgba(201,168,76,0.14);
            --green: #2dd4a0; --red: #ef6060; --blue: #5b9cf6; --amber: #f0b429;
            --r1: 6px; --r2: 10px; --r3: 14px; --r4: 100px;
        }

        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--t1);
            min-height: 100vh;
            font-size: 14px;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        body::before {
            content: '';
            position: fixed;
            top: -280px; left: 50%;
            transform: translateX(-50%);
            width: 1000px; height: 500px;
            background: radial-gradient(ellipse, rgba(201,168,76,0.032) 0%, transparent 65%);
            pointer-events: none; z-index: 0;
        }

        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--b3); border-radius: 4px; }

        /* ── TOPBAR ── */
        .topbar {
            position: sticky; top: 0; z-index: 300;
            height: 62px; padding: 0 36px;
            display: flex; align-items: center; justify-content: space-between;
            background: rgba(5,5,10,0.84);
            backdrop-filter: blur(28px) saturate(180%);
            border-bottom: 1px solid var(--b2);
        }

        .topbar::after {
            content: ''; position: absolute;
            bottom: -1px; left: 0; right: 0; height: 1px;
            background: linear-gradient(90deg, transparent 5%, rgba(201,168,76,0.28) 35%, rgba(201,168,76,0.28) 65%, transparent 95%);
        }

        .brand { display: flex; align-items: center; gap: 12px; text-decoration: none; }

        .brand-mark {
            width: 32px; height: 32px;
            background: linear-gradient(140deg, var(--bg4), var(--bg5));
            border: 1px solid var(--b3); border-radius: var(--r2);
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; color: var(--gold);
            position: relative; overflow: hidden;
            box-shadow: 0 0 24px rgba(201,168,76,0.07);
        }
        .brand-mark::before {
            content: ''; position: absolute; inset: 0;
            background: linear-gradient(140deg, rgba(201,168,76,0.1), transparent);
        }

        .brand-name {
            font-family: 'Syne', sans-serif;
            font-size: 0.95rem; font-weight: 800;
            letter-spacing: 0.1em; text-transform: uppercase; color: var(--t1);
        }

        .brand-div { width: 1px; height: 18px; background: var(--b3); }

        .brand-sub {
            font-size: 0.68rem; color: var(--t4);
            letter-spacing: 0.07em; text-transform: uppercase;
        }

        .subnav-item {
            display: flex; align-items: center; gap: 7px;
            font-size: 0.74rem; font-weight: 500; color: var(--t2);
        }
        .subnav-item i { font-size: 11px; color: var(--gold); }
        .subnav-item span {
            font-family: 'Syne', sans-serif;
            font-size: 0.72rem; font-weight: 700;
            color: var(--t1); letter-spacing: 0.02em;
        }

        .back-btn {
            display: flex; align-items: center; gap: 6px;
            padding: 6px 14px; border-radius: var(--r4);
            font-size: 0.73rem; font-weight: 500;
            border: 1px solid var(--b2); color: var(--t2);
            text-decoration: none; transition: all 0.18s; background: var(--bg3);
        }
        .back-btn:hover { border-color: var(--b3); color: var(--t1); background: var(--bg4); }

        /* ── WRAP ── */
        .wrap {
            max-width: 1560px; margin: 0 auto;
            padding: 30px 36px 80px;
            position: relative; z-index: 1;
        }

        /* ── TOAST ── */
        .toast {
            display: flex; align-items: center; gap: 10px;
            padding: 12px 16px;
            background: rgba(45,212,160,0.05);
            border: 1px solid rgba(45,212,160,0.16);
            border-radius: var(--r2); color: var(--green);
            font-size: 0.81rem; margin-bottom: 22px;
            animation: toastIn 0.3s cubic-bezier(0.34,1.56,0.64,1);
        }
        @keyframes toastIn { from { opacity:0; transform:translateY(-8px); } to { opacity:1; transform:translateY(0); } }
        .t-dot {
            width: 6px; height: 6px; border-radius: 50%;
            background: var(--green); flex-shrink: 0;
            animation: blink 2s ease infinite;
        }
        @keyframes blink { 0%,100%{opacity:1;} 50%{opacity:0.4;} }

        /* ── EDIT NOTICE ── */
        .edit-notice {
            display: flex; align-items: center; justify-content: space-between;
            padding: 11px 16px;
            background: rgba(201,168,76,0.04);
            border: 1px solid rgba(201,168,76,0.16);
            border-radius: var(--r2); color: var(--gold);
            font-size: 0.78rem; margin-bottom: 16px;
        }
        .edit-notice a { color: var(--gold); opacity: 0.65; font-size: 0.73rem; text-decoration: underline; }
        .edit-notice a:hover { opacity: 1; }

        /* ── FORM CARD ── */
        .form-card {
            background: var(--bg2); border: 1px solid var(--b2);
            border-radius: var(--r3); overflow: hidden;
            margin-bottom: 26px;
            box-shadow: 0 1px 0 rgba(255,255,255,0.03) inset, 0 20px 60px rgba(0,0,0,0.45);
            transition: border-color 0.2s;
        }
        .form-card:hover { border-color: var(--b3); }

        .form-card-hd {
            display: flex; align-items: center; gap: 12px;
            padding: 18px 24px;
            background: linear-gradient(180deg, var(--bg3), var(--bg2));
            border-bottom: 1px solid var(--b1);
            position: relative;
        }
        .form-card-hd::before {
            content: ''; position: absolute;
            top: 0; left: 0; right: 0; height: 1px;
            background: linear-gradient(90deg, transparent, rgba(201,168,76,0.24), transparent);
        }

        .form-ico {
            width: 30px; height: 30px; border-radius: var(--r2);
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; flex-shrink: 0;
            background: var(--gold3); border: 1px solid rgba(201,168,76,0.2);
            color: var(--gold);
        }

        .form-card-hd h2 {
            font-family: 'Syne', sans-serif;
            font-size: 0.85rem; font-weight: 700;
            color: var(--t1); letter-spacing: 0.01em;
        }

        .form-card-body { padding: 24px; }

        /* ── FORM LAYOUT ── */
        .fg3 { display: grid; grid-template-columns: repeat(3,1fr); gap: 14px; margin-bottom: 14px; }
        .fg2 { display: grid; grid-template-columns: repeat(2,1fr); gap: 14px; margin-bottom: 14px; }
        .fg1 { margin-bottom: 14px; }

        .field { display: flex; flex-direction: column; gap: 5px; }

        .field label {
            font-family: 'Syne', sans-serif;
            font-size: 0.6rem; font-weight: 700;
            letter-spacing: 0.1em; text-transform: uppercase;
            color: var(--t3);
        }

        .field input, .field select, .field textarea {
            padding: 8px 11px;
            background: var(--bg4); border: 1px solid var(--b2);
            border-radius: var(--r1); color: var(--t1);
            font-size: 0.83rem; font-family: 'DM Sans', sans-serif;
            outline: none;
            transition: border-color 0.14s, box-shadow 0.14s, background 0.14s;
            width: 100%;
        }
        .field input:focus, .field select:focus, .field textarea:focus {
            border-color: var(--b4); background: var(--bg5);
            box-shadow: 0 0 0 3px rgba(255,255,255,0.025);
        }
        .field input::placeholder, .field textarea::placeholder { color: var(--t4); }
        .field textarea { min-height: 100px; resize: vertical; }
        .field select option { background: var(--bg4); }
        .field small { font-size: 0.7rem; color: var(--t4); }

        /* ── COLOR PICKER FIELD ── */
        .color-pick-wrap {
            display: flex; align-items: center; gap: 8px;
        }
        .color-pick-wrap input[type="text"] {
            flex: 1;
            font-family: 'DM Mono', 'Courier New', monospace;
            font-size: 0.82rem;
            letter-spacing: 0.05em;
        }
        /* Native color input styled as swatch button */
        .color-native {
            width: 38px; height: 34px;
            border-radius: var(--r1);
            border: 1px solid var(--b3);
            padding: 2px;
            cursor: pointer;
            background: var(--bg4);
            flex-shrink: 0;
            overflow: hidden;
            position: relative;
        }
        .color-native input[type="color"] {
            position: absolute; inset: -4px;
            width: calc(100% + 8px); height: calc(100% + 8px);
            border: none; padding: 0; margin: 0;
            background: none; cursor: pointer;
            opacity: 0;
        }
        .color-native-preview {
            width: 100%; height: 100%;
            border-radius: 4px;
            pointer-events: none;
        }

        /* Swatch preview bar */
        .color-preview-row {
            display: flex; gap: 10px; margin-top: 8px;
        }
        .color-chip {
            display: flex; align-items: center; gap: 8px;
            padding: 6px 12px;
            border-radius: var(--r2);
            border: 1px solid var(--b2);
            background: var(--bg4);
            font-size: 0.74rem; color: var(--t2);
        }
        .color-chip-dot {
            width: 22px; height: 22px; border-radius: 50%;
            border: 1px solid rgba(255,255,255,0.1);
            flex-shrink: 0;
            transition: background 0.2s;
        }

        /* Checkbox */
        .ck {
            display: flex; align-items: center; gap: 9px;
            cursor: pointer; margin-bottom: 10px;
        }
        .ck input[type="checkbox"] {
            appearance: none; -webkit-appearance: none;
            width: 16px; height: 16px;
            background: var(--bg4); border: 1px solid var(--b3);
            border-radius: 4px; cursor: pointer; flex-shrink: 0;
            transition: all 0.14s; position: relative;
        }
        .ck input:checked { background: var(--gold3); border-color: var(--gold); }
        .ck input:checked::after {
            content: ''; position: absolute;
            left: 4px; top: 1px; width: 5px; height: 9px;
            border: 2px solid var(--gold2); border-left: none; border-top: none;
            transform: rotate(45deg);
        }
        .ck span { font-size: 0.82rem; color: var(--t2); }

        /* Divider + Section label */
        .fdiv {
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--b2) 15%, var(--b2) 85%, transparent);
            margin: 20px 0;
        }
        .slbl {
            font-family: 'Syne', sans-serif;
            font-size: 0.6rem; font-weight: 700;
            letter-spacing: 0.12em; text-transform: uppercase;
            color: var(--t4); margin-bottom: 13px;
            display: flex; align-items: center; gap: 8px;
        }
        .slbl::after { content: ''; flex: 1; height: 1px; background: var(--b1); }

        /* Form actions */
        .factions {
            display: flex; align-items: center; gap: 10px;
            margin-top: 20px; padding-top: 18px; border-top: 1px solid var(--b1);
        }

        /* ── BUTTONS ── */
        .btn-p {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 10px 22px;
            background: linear-gradient(135deg, var(--gold), var(--gold2));
            color: #080500; border: none; border-radius: var(--r2);
            font-size: 0.79rem; font-weight: 700; font-family: 'DM Sans', sans-serif;
            cursor: pointer; letter-spacing: 0.04em;
            transition: all 0.18s;
            box-shadow: 0 3px 18px rgba(201,168,76,0.24);
        }
        .btn-p:hover { box-shadow: 0 5px 26px rgba(201,168,76,0.38); transform: translateY(-1px); }
        .btn-p:active { transform: translateY(0); }

        .btn-g {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 10px 18px; background: transparent;
            color: var(--t2); border: 1px solid var(--b2); border-radius: var(--r2);
            font-size: 0.79rem; font-weight: 500; font-family: 'DM Sans', sans-serif;
            cursor: pointer; text-decoration: none; transition: all 0.15s;
        }
        .btn-g:hover { border-color: var(--b3); color: var(--t1); background: var(--bg3); }

        /* ── SECTION HEADER ── */
        .sec-hd {
            display: flex; align-items: baseline; gap: 10px;
            margin-bottom: 14px; padding-bottom: 12px;
            border-bottom: 1px solid var(--b1);
        }
        .sec-hd h2 {
            font-family: 'Syne', sans-serif;
            font-size: 0.95rem; font-weight: 700; color: var(--t1);
        }
        .sec-cnt {
            font-size: 0.68rem; color: var(--t4);
            background: var(--bg3); border: 1px solid var(--b2);
            padding: 2px 8px; border-radius: var(--r4);
        }

        /* ── TABLE ── */
        .tbox {
            background: var(--bg2); border: 1px solid var(--b2);
            border-radius: var(--r3); overflow: hidden;
            box-shadow: 0 1px 0 rgba(255,255,255,0.025) inset, 0 16px 48px rgba(0,0,0,0.4);
        }

        table { width: 100%; border-collapse: collapse; }

        thead th {
            padding: 11px 14px; text-align: left;
            font-family: 'Syne', sans-serif;
            font-size: 0.58rem; font-weight: 700;
            letter-spacing: 0.1em; text-transform: uppercase;
            color: var(--t4);
            background: linear-gradient(180deg, var(--bg3), var(--bg2));
            border-bottom: 1px solid var(--b1); white-space: nowrap;
        }

        tbody td {
            padding: 13px 14px; border-bottom: 1px solid var(--b1);
            vertical-align: middle; font-size: 0.83rem; color: var(--t1);
        }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr { transition: background 0.1s; }
        tbody tr:hover td { background: rgba(255,255,255,0.012); }
        tbody tr.editing td { background: rgba(201,168,76,0.03); }

        /* ── VEHICLE CELLS ── */
        .vimg {
            width: 98px; height: 64px; object-fit: cover;
            border-radius: var(--r1); border: 1px solid var(--b2);
            display: block; background: var(--bg4);
        }
        .vimg-ph {
            width: 98px; height: 64px;
            border-radius: var(--r1); border: 1px solid var(--b2);
            background: var(--bg4);
            display: flex; align-items: center; justify-content: center;
            color: var(--t4); font-size: 18px;
        }
        .vname { font-weight: 600; font-size: 0.85rem; color: var(--t1); margin-bottom: 3px; }
        .vmeta { font-size: 0.74rem; color: var(--t3); }
        .spec-row { font-size: 0.78rem; color: var(--t2); line-height: 1.75; }
        .price-val {
            font-family: 'Syne', sans-serif;
            font-size: 0.85rem; font-weight: 700; color: var(--t1);
        }

        /* Color swatches in table */
        .tbl-colors { display: flex; gap: 5px; align-items: center; margin-top: 4px; }
        .tbl-swatch {
            width: 16px; height: 16px; border-radius: 50%;
            border: 1px solid rgba(255,255,255,0.1);
            display: inline-block; flex-shrink: 0;
        }
        .tbl-color-name { font-size: 0.7rem; color: var(--t3); }

        .pill-feat {
            display: inline-flex; align-items: center; gap: 4px;
            font-size: 0.64rem; font-weight: 700; color: var(--gold);
            background: rgba(201,168,76,0.1); border: 1px solid rgba(201,168,76,0.18);
            padding: 2px 7px; border-radius: var(--r4); margin-left: 6px;
        }

        .status-on  { display:inline-flex; align-items:center; gap:5px; font-size:0.74rem; font-weight:600; color:var(--green); }
        .status-off { display:inline-flex; align-items:center; gap:5px; font-size:0.74rem; font-weight:600; color:var(--t3); }
        .sdot { width: 5px; height: 5px; border-radius: 50%; flex-shrink: 0; }
        .dot-g { background: var(--green); }
        .dot-x { background: var(--t3); }

        /* ── ACTION BUTTONS ── */
        .acts { display: flex; flex-wrap: wrap; gap: 4px; }

        .abtn {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 5px 11px; border-radius: var(--r1);
            font-size: 0.69rem; font-weight: 600;
            border: 1px solid transparent; cursor: pointer;
            font-family: 'DM Sans', sans-serif; transition: all 0.12s;
            background: transparent; text-decoration: none; white-space: nowrap;
        }

        .ab-edit { border-color: var(--b2); color: var(--t2); }
        .ab-edit:hover { border-color: var(--b3); color: var(--t1); background: var(--bg3); }

        .ab-del { border-color: rgba(239,96,96,0.18); color: var(--red); background: rgba(239,96,96,0.04); }
        .ab-del:hover { background: rgba(239,96,96,0.12); border-color: var(--red); }

        @media (max-width: 1100px) { .fg3 { grid-template-columns: repeat(2,1fr); } }
        @media (max-width: 860px) { .wrap { padding: 18px 14px 60px; } .fg3, .fg2 { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

<header class="topbar">
    <div style="display:flex; align-items:center; gap:16px;">
        <a class="brand" href="/lending_word/admin/">
            <div class="brand-mark"><i class="fas fa-shield-halved"></i></div>
            <span class="brand-name">Porsche</span>
            <div class="brand-div"></div>
            <span class="brand-sub">Admin</span>
        </a>
        <div class="subnav-item">
            <i class="fas fa-chevron-right" style="font-size:9px; color:var(--t4);"></i>
            <i class="fas fa-car"></i>
            <span>Finder Vehicles</span>
        </div>
    </div>
    <a href="/lending_word/admin/" class="back-btn"><i class="fas fa-arrow-left"></i> Dashboard</a>
</header>

<div class="wrap">

    <?php if ($success): ?>
    <div class="toast"><span class="t-dot"></span><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if ($editVehicle): ?>
    <div class="edit-notice">
        <span><i class="fas fa-pen-to-square" style="margin-right:6px;"></i>Editing: <strong><?= htmlspecialchars($editVehicle['title']) ?></strong></span>
        <a href="vehicles.php">Cancel Edit</a>
    </div>
    <?php endif; ?>

    <!-- ═══ FORM ═══ -->
    <div class="form-card">
        <div class="form-card-hd">
            <div class="form-ico"><i class="fas <?= $editVehicle ? 'fa-pen' : 'fa-plus' ?>"></i></div>
            <h2><?= $editVehicle ? 'Edit Vehicle' : 'Add New Vehicle' ?></h2>
        </div>
        <div class="form-card-body">
            <form method="POST">
                <input type="hidden" name="action" value="<?= $editVehicle ? 'update_vehicle' : 'add_vehicle' ?>">
                <?php if ($editVehicle): ?>
                    <input type="hidden" name="id" value="<?= $editVehicle['id'] ?>">
                    <input type="hidden" name="update_images" value="1">
                <?php endif; ?>

                <div class="slbl">Basic Info</div>
                <div class="fg3">
                    <div class="field"><label>Title <span style="color:var(--gold)">*</span></label><input type="text" name="title" value="<?= htmlspecialchars($editVehicle['title'] ?? '') ?>" required></div>
                    <div class="field"><label>Condition <span style="color:var(--gold)">*</span></label>
                        <select name="condition" required>
                            <option value="New"  <?= ($editVehicle['condition'] ?? '') === 'New'  ? 'selected' : '' ?>>New</option>
                            <option value="Used" <?= ($editVehicle['condition'] ?? '') === 'Used' ? 'selected' : '' ?>>Used</option>
                        </select>
                    </div>
                    <div class="field"><label>Model Year <span style="color:var(--gold)">*</span></label><input type="number" name="model_year" value="<?= $editVehicle['model_year'] ?? '' ?>" required min="1950" max="2030"></div>
                </div>
                <div class="fg3">
                    <div class="field"><label>Model Series</label>
                        <select name="series_id"><option value="">Select Series</option>
                            <?php foreach ($modelSeries as $s): ?><option value="<?= $s['id'] ?>" <?= ($editVehicle['series_id'] ?? '') == $s['id'] ? 'selected' : '' ?>><?= htmlspecialchars($s['name']) ?></option><?php endforeach; ?>
                        </select>
                    </div>
                    <div class="field"><label>Body Type</label>
                        <select name="body_type_id"><option value="">Select Body Type</option>
                            <?php foreach ($bodyTypes as $t): ?><option value="<?= $t['id'] ?>" <?= ($editVehicle['body_type_id'] ?? '') == $t['id'] ? 'selected' : '' ?>><?= htmlspecialchars($t['name']) ?></option><?php endforeach; ?>
                        </select>
                    </div>
                    <div class="field"><label>Porsche Center</label>
                        <select name="center_id"><option value="">Select Center</option>
                            <?php foreach ($centers as $c): ?><option value="<?= $c['id'] ?>" <?= ($editVehicle['center_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?> — <?= $c['city'] ?></option><?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="fdiv"></div>
                <div class="slbl">Technical Specs</div>
                <div class="fg3">
                    <div class="field"><label>Fuel Type</label><input type="text" name="fuel_type" value="<?= htmlspecialchars($editVehicle['fuel_type'] ?? '') ?>" placeholder="e.g., Petrol, Electric"></div>
                    <div class="field"><label>Transmission</label><input type="text" name="transmission" value="<?= htmlspecialchars($editVehicle['transmission'] ?? '') ?>" placeholder="e.g., PDK (Automatic)"></div>
                    <div class="field"><label>Drive Type</label><input type="text" name="drive_type" value="<?= htmlspecialchars($editVehicle['drive_type'] ?? '') ?>" placeholder="e.g., Rear-wheel-drive"></div>
                </div>
                <div class="fg3">
                    <div class="field"><label>Power (kW)</label><input type="number" name="power_kw" value="<?= htmlspecialchars($editVehicle['power_kw'] ?? '') ?>" step="0.01"></div>
                    <div class="field"><label>Power (hp)</label><input type="number" name="power_hp" value="<?= htmlspecialchars($editVehicle['power_hp'] ?? '') ?>" step="0.01"></div>
                    <div class="field"><label>Seats</label><input type="number" name="seats" value="<?= htmlspecialchars($editVehicle['seats'] ?? '') ?>" min="2" max="9"></div>
                </div>
                <div class="fg3">
                    <div class="field"><label>Mileage (km)</label><input type="number" name="mileage" value="<?= htmlspecialchars($editVehicle['mileage'] ?? '') ?>" min="0"><small>Leave empty for new vehicles</small></div>
                    <div class="field"><label>Approved Pre-Owned</label>
                        <select name="is_approved_preowned">
                            <option value="0" <?= empty($editVehicle['is_approved_preowned']) ? 'selected' : '' ?>>No</option>
                            <option value="1" <?= !empty($editVehicle['is_approved_preowned']) ? 'selected' : '' ?>>Yes</option>
                        </select>
                        <small>For certified pre-owned vehicles</small>
                    </div>
                    <div class="field"><label>Variant ID</label><input type="number" name="variant_id" value="<?= htmlspecialchars($editVehicle['variant_id'] ?? '') ?>"><small>Link to model variant</small></div>
                </div>

                <div class="fdiv"></div>
                <div class="slbl">Color</div>

                <!-- Exterior Color -->
                <div class="fg2" style="margin-bottom:8px;">
                    <div class="field">
                        <label>Exterior Color Name</label>
                        <input type="text" name="exterior_color" id="ext_color_name"
                               value="<?= htmlspecialchars($editVehicle['exterior_color'] ?? '') ?>"
                               placeholder="e.g., Provence Purple">
                    </div>
                    <div class="field">
                        <label>Exterior Color Hex</label>
                        <div class="color-pick-wrap">
                            <input type="text" name="exterior_color_hex" id="ext_color_hex"
                                   value="<?= htmlspecialchars($editVehicle['exterior_color_hex'] ?? '#888888') ?>"
                                   placeholder="#a08bbe" maxlength="7">
                            <div class="color-native" id="ext_color_native_wrap">
                                <div class="color-native-preview" id="ext_color_preview"
                                     style="background: <?= htmlspecialchars($editVehicle['exterior_color_hex'] ?? '#888888') ?>;"></div>
                                <input type="color" id="ext_color_picker"
                                       value="<?= htmlspecialchars($editVehicle['exterior_color_hex'] ?? '#888888') ?>">
                            </div>
                        </div>
                        <small>Klik kotak warna untuk pilih, atau ketik hex manual</small>
                    </div>
                </div>

                <!-- Interior Color -->
                <div class="fg2" style="margin-bottom:8px;">
                    <div class="field">
                        <label>Interior Color Name</label>
                        <input type="text" name="interior_color" id="int_color_name"
                               value="<?= htmlspecialchars($editVehicle['interior_color'] ?? '') ?>"
                               placeholder="e.g., Leather package Black">
                    </div>
                    <div class="field">
                        <label>Interior Color Hex</label>
                        <div class="color-pick-wrap">
                            <input type="text" name="interior_color_hex" id="int_color_hex"
                                   value="<?= htmlspecialchars($editVehicle['interior_color_hex'] ?? '#111111') ?>"
                                   placeholder="#111111" maxlength="7">
                            <div class="color-native" id="int_color_native_wrap">
                                <div class="color-native-preview" id="int_color_preview"
                                     style="background: <?= htmlspecialchars($editVehicle['interior_color_hex'] ?? '#111111') ?>;"></div>
                                <input type="color" id="int_color_picker"
                                       value="<?= htmlspecialchars($editVehicle['interior_color_hex'] ?? '#111111') ?>">
                            </div>
                        </div>
                        <small>Klik kotak warna untuk pilih, atau ketik hex manual</small>
                    </div>
                </div>

                <!-- Live Preview -->
                <div class="color-preview-row" id="colorPreviewRow">
                    <div class="color-chip">
                        <div class="color-chip-dot" id="ext_chip"></div>
                        <span id="ext_chip_label">Exterior</span>
                    </div>
                    <div class="color-chip">
                        <div class="color-chip-dot" id="int_chip"></div>
                        <span id="int_chip_label">Interior</span>
                    </div>
                </div>

                <div class="fdiv"></div>
                <div class="slbl">Pricing & Media</div>
                <div class="fg2">
                    <div class="field"><label>Price (IDR) <span style="color:var(--gold)">*</span></label><input type="number" name="price" value="<?= $editVehicle['price'] ?? '' ?>" required min="0" step="1000000"></div>
                    <div class="field"><label>Main Image URL <span style="color:var(--gold)">*</span></label><input type="url" name="main_image_url" value="<?= htmlspecialchars($editVehicle['main_image_url'] ?? '') ?>" required></div>
                </div>
                <div class="fg1">
                    <div class="field">
                        <label>Additional Images <span style="font-weight:400;text-transform:none;letter-spacing:0;font-family:'DM Sans',sans-serif;">(one per line)</span></label>
                        <textarea name="additional_images" rows="4" placeholder="https://example.com/image1.jpg&#10;https://example.com/image2.jpg"><?= htmlspecialchars($editVehicle['additional_images'] ?? '') ?></textarea>
                        <small>Each URL on a new line. These will appear in the vehicle gallery.</small>
                    </div>
                </div>

                <div class="fdiv"></div>
                <div class="slbl">Visibility</div>
                <label class="ck"><input type="checkbox" name="is_featured" value="1" <?= ($editVehicle['is_featured'] ?? false) ? 'checked' : '' ?>><span>Featured Vehicle</span></label>
                <label class="ck"><input type="checkbox" name="is_active" value="1" <?= ($editVehicle['is_active'] ?? true) ? 'checked' : '' ?>><span>Active</span></label>

                <div class="factions">
                    <button type="submit" class="btn-p">
                        <i class="fas fa-floppy-disk"></i>
                        <?= $editVehicle ? 'Update Vehicle' : 'Add Vehicle' ?>
                    </button>
                    <?php if ($editVehicle): ?>
                    <a href="vehicles.php" class="btn-g"><i class="fas fa-xmark"></i> Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- ═══ TABLE ═══ -->
    <div class="sec-hd">
        <h2>All Vehicles</h2>
        <span class="sec-cnt"><?= count($vehicles) ?> total</span>
    </div>

    <div class="tbox">
        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Vehicle</th>
                    <th>Specs</th>
                    <th>Price</th>
                    <th>Center</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($vehicles as $vehicle): ?>
                <tr class="<?= ($editVehicle && $editVehicle['id'] == $vehicle['id']) ? 'editing' : '' ?>">
                    <td>
                        <?php if (!empty($vehicle['main_image_url'])): ?>
                        <img class="vimg" src="<?= htmlspecialchars($vehicle['main_image_url']) ?>" alt="<?= htmlspecialchars($vehicle['title']) ?>" loading="lazy" onerror="this.outerHTML='<div class=\'vimg-ph\'><i class=\'fas fa-car\'></i></div>'">
                        <?php else: ?><div class="vimg-ph"><i class="fas fa-car"></i></div><?php endif; ?>
                    </td>
                    <td>
                        <div class="vname"><?= htmlspecialchars($vehicle['title']) ?><?php if ($vehicle['is_featured']): ?><span class="pill-feat"><i class="fas fa-star" style="font-size:8px;"></i> Featured</span><?php endif; ?></div>
                        <div class="vmeta"><?= htmlspecialchars($vehicle['condition']) ?> &bull; <?= $vehicle['model_year'] ?></div>
                        <!-- Color swatches in table -->
                        <div class="tbl-colors">
                            <?php if (!empty($vehicle['exterior_color_hex'])): ?>
                            <span class="tbl-swatch" style="background:<?= htmlspecialchars($vehicle['exterior_color_hex']) ?>;" title="Exterior: <?= htmlspecialchars($vehicle['exterior_color'] ?? '') ?>"></span>
                            <?php endif; ?>
                            <?php if (!empty($vehicle['interior_color_hex'])): ?>
                            <span class="tbl-swatch" style="background:<?= htmlspecialchars($vehicle['interior_color_hex']) ?>;" title="Interior: <?= htmlspecialchars($vehicle['interior_color'] ?? '') ?>"></span>
                            <?php endif; ?>
                            <?php if (!empty($vehicle['exterior_color'])): ?>
                            <span class="tbl-color-name"><?= htmlspecialchars($vehicle['exterior_color']) ?></span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td>
                        <div class="spec-row">
                            <?php if ($vehicle['fuel_type']): ?><div><?= htmlspecialchars($vehicle['fuel_type']) ?></div><?php endif; ?>
                            <?php if ($vehicle['power_kw']): ?><div><?= $vehicle['power_kw'] ?> kW / <?= $vehicle['power_hp'] ?> hp</div><?php endif; ?>
                            <?php if ($vehicle['transmission']): ?><div><?= htmlspecialchars($vehicle['transmission']) ?></div><?php endif; ?>
                        </div>
                    </td>
                    <td><span class="price-val">Rp <?= number_format($vehicle['price'], 0, ',', '.') ?></span></td>
                    <td>
                        <div class="spec-row">
                            <div><?= htmlspecialchars($vehicle['center_name'] ?? '—') ?></div>
                            <?php if (!empty($vehicle['center_city'])): ?><div><?= htmlspecialchars($vehicle['center_city']) ?></div><?php endif; ?>
                        </div>
                    </td>
                    <td>
                        <?php if ($vehicle['is_active']): ?>
                        <span class="status-on"><span class="sdot dot-g"></span>Active</span>
                        <?php else: ?>
                        <span class="status-off"><span class="sdot dot-x"></span>Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="acts">
                            <a href="vehicles.php?edit=<?= $vehicle['id'] ?>" class="abtn ab-edit"><i class="fas fa-pen"></i> Edit</a>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="delete_vehicle">
                                <input type="hidden" name="id" value="<?= $vehicle['id'] ?>">
                                <button type="submit" class="abtn ab-del" onclick="return confirm('Delete this vehicle?')"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>

<script>
// ── COLOR PICKER LOGIC ──
function initColorPicker(hexInputId, pickerId, previewId, chipId, chipLabelId, nameInputId) {
    const hexInput  = document.getElementById(hexInputId);
    const picker    = document.getElementById(pickerId);
    const preview   = document.getElementById(previewId);
    const chip      = document.getElementById(chipId);
    const chipLabel = document.getElementById(chipLabelId);
    const nameInput = document.getElementById(nameInputId);

    function isValidHex(v) { return /^#[0-9A-Fa-f]{6}$/.test(v); }

    function applyColor(hex) {
        if (!isValidHex(hex)) return;
        preview.style.background = hex;
        chip.style.background    = hex;
        picker.value             = hex;
        hexInput.value           = hex;
    }

    function updateLabel() {
        const name = nameInput ? nameInput.value.trim() : '';
        if (chipLabel) chipLabel.textContent = name || chipLabel.dataset.default;
    }

    // Color picker → update hex input + preview
    picker.addEventListener('input', e => {
        applyColor(e.target.value);
    });

    // Hex text input → update picker + preview
    hexInput.addEventListener('input', e => {
        let v = e.target.value.trim();
        if (!v.startsWith('#')) v = '#' + v;
        hexInput.value = v;
        if (isValidHex(v)) applyColor(v);
    });

    hexInput.addEventListener('blur', e => {
        let v = hexInput.value.trim();
        if (!v.startsWith('#')) v = '#' + v;
        if (isValidHex(v)) applyColor(v);
        else applyColor(picker.value); // revert to last valid
    });

    // Name input → update chip label
    if (nameInput) {
        nameInput.addEventListener('input', updateLabel);
        chipLabel.dataset.default = chipLabel.textContent;
        updateLabel();
    }

    // Init on load
    applyColor(hexInput.value || picker.value);
    updateLabel();
}

document.addEventListener('DOMContentLoaded', () => {
    initColorPicker('ext_color_hex', 'ext_color_picker', 'ext_color_preview', 'ext_chip', 'ext_chip_label', 'ext_color_name');
    initColorPicker('int_color_hex', 'int_color_picker', 'int_color_preview', 'int_chip', 'int_chip_label', 'int_color_name');
});
</script>

</body>
</html>