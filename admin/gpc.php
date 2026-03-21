<?php
/**
 * Admin GPC — /lending_word/admin/gpc.php
 */
session_start();
require_once __DIR__ . '/../app/models/Admin.php';
if (!Admin::isLoggedIn()) { header('Location: /lending_word/admin/login.php'); exit; }

require_once __DIR__ . '/../app/database.php';
require_once __DIR__ . '/../app/models/GpcModel.php';

$unreadInquiries = 0;
try { require_once __DIR__ . '/../app/models/VehicleInquiry.php'; $unreadInquiries = (new VehicleInquiry())->countUnread(); } catch(Exception $e){}
$chatUnread = 0;
try { if(file_exists(__DIR__.'/../app/models/ChatSession.php')){ require_once __DIR__.'/../app/models/ChatSession.php'; $chatUnread = (new ChatSession())->countUnread(); } } catch(Exception $e){}

$m   = new GpcModel();
$tab = $_GET['tab'] ?? 'contents';
$success = ''; $error = '';

// CONTENTS
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['update_contents'])) {
    foreach ($_POST['cc'] as $key => $value) $m->upsertContent($key, trim($value));
    $success = "Contents disimpan!";
}

// PARTNERS
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['add_partner'])) {
    try { $m->createPartner($_POST); $success="Partner ditambahkan!"; } catch(Exception $e){ $error=$e->getMessage(); }
}
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['update_partner'])) {
    $m->updatePartner((int)$_POST['id'], $_POST); $success="Partner diupdate!";
}
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['delete_partner'])) {
    $m->deletePartner((int)$_POST['id']); $success="Partner dihapus!";
}

// COOPERATIONS
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['add_coop'])) {
    try { $m->createCooperation($_POST); $success="Cooperation ditambahkan!"; } catch(Exception $e){ $error=$e->getMessage(); }
}
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['update_coop'])) {
    $m->updateCooperation((int)$_POST['id'], $_POST); $success="Cooperation diupdate!";
}
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['delete_coop'])) {
    $m->deleteCooperation((int)$_POST['id']); $success="Cooperation dihapus!";
}

$contents     = $m->getAllContents();
$partners     = $m->getAllPartners();
$cooperations = $m->getAllCooperations();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>GPC Admin — Porsche Indonesia</title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
:root{--b1:rgba(0,0,0,0.04);--b2:rgba(0,0,0,0.09);--b3:rgba(0,0,0,0.16);--b4:rgba(0,0,0,0.28);--t1:#12121f;--t2:#4b4b6a;--t3:#9090b0;--t4:#b8b8d0;--gold:#18181e;--gold2:#3a3a4a;--green:#00b894;--red:#e17055;--blue:#0984e3;--r1:8px;--r2:12px;--r3:16px;--r4:100px;}
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'DM Sans',sans-serif;background:radial-gradient(ellipse at 15% 20%,rgba(200,200,230,.55) 0%,transparent 55%),radial-gradient(ellipse at 85% 75%,rgba(210,205,235,.50) 0%,transparent 55%),#d8d8e6;color:var(--t1);min-height:100vh;font-size:14px;line-height:1.6;-webkit-font-smoothing:antialiased;overflow-x:hidden;}
::-webkit-scrollbar{width:4px;height:4px;}::-webkit-scrollbar-thumb{background:var(--b3);border-radius:4px;}
.topbar{position:sticky;top:0;z-index:300;height:62px;padding:0 36px;display:flex;align-items:center;justify-content:space-between;background:rgba(255,255,255,.72);backdrop-filter:blur(28px) saturate(180%);border-bottom:1px solid var(--b2);box-shadow:0 1px 0 rgba(255,255,255,.9) inset,0 2px 12px rgba(0,0,0,.06);}
.brand{display:flex;align-items:center;gap:12px;text-decoration:none;}.brand-mark{width:32px;height:32px;background:linear-gradient(140deg,#18181e,#3a3a4a);border-radius:var(--r2);display:flex;align-items:center;justify-content:center;font-size:12px;color:#fff;box-shadow:0 4px 12px rgba(0,0,0,.22);}
.brand-name{font-family:'Syne',sans-serif;font-size:.95rem;font-weight:800;letter-spacing:.1em;text-transform:uppercase;color:var(--t1);}.brand-div{width:1px;height:18px;background:var(--b3);}.brand-role{font-size:.68rem;color:var(--t3);letter-spacing:.07em;text-transform:uppercase;}
.topbar-actions{display:flex;align-items:center;gap:8px;}
.tpill{display:flex;align-items:center;gap:6px;padding:6px 14px;border-radius:var(--r4);font-size:.73rem;font-weight:500;border:1px solid var(--b2);color:var(--t2);text-decoration:none;background:rgba(255,255,255,.60);transition:all .18s;letter-spacing:.02em;}
.tpill:hover{border-color:var(--b3);color:var(--t1);background:rgba(255,255,255,.85);}.tpill.logout:hover{border-color:rgba(225,112,85,.4);color:var(--red);background:rgba(225,112,85,.06);}
.layout{display:flex;min-height:calc(100vh - 62px);}
.sidebar{width:210px;flex-shrink:0;background:rgba(255,255,255,.52);backdrop-filter:blur(20px);border-right:1px solid var(--b2);padding:18px 8px;position:sticky;top:62px;height:calc(100vh - 62px);overflow-y:auto;}
.sidebar::-webkit-scrollbar{display:none;}
.sg{margin-bottom:24px;}.sg-label{font-family:'Syne',sans-serif;font-size:.56rem;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:var(--t4);padding:0 8px;margin-bottom:6px;display:flex;align-items:center;gap:6px;}.sg-label::after{content:'';flex:1;height:1px;background:var(--b2);}
.nav-it{display:flex;align-items:center;gap:8px;padding:7px 9px;border-radius:var(--r2);font-size:.77rem;font-weight:500;color:var(--t2);text-decoration:none;cursor:pointer;border:none;background:transparent;font-family:'DM Sans',sans-serif;width:100%;text-align:left;transition:all .14s;white-space:nowrap;position:relative;}
.nav-it i{font-size:10.5px;width:14px;text-align:center;flex-shrink:0;color:var(--t3);}
.nav-it:hover{color:var(--t1);background:rgba(255,255,255,.65);}
.nav-it.on{color:var(--t1);background:rgba(255,255,255,.80);border:1px solid var(--b2);box-shadow:0 2px 8px rgba(0,0,0,.07);}
.nav-it.on i{color:var(--gold);}.nav-it.on::before{content:'';position:absolute;left:0;top:50%;transform:translateY(-50%);width:2px;height:13px;background:linear-gradient(180deg,var(--gold),var(--gold2));border-radius:2px;}
.sdiv{height:1px;background:var(--b2);margin:8px 0;}
.main{flex:1;padding:30px 38px 80px;min-width:0;}
.panel{display:none;}.panel.on{display:block;animation:panelIn .22s cubic-bezier(.22,1,.36,1);}
@keyframes panelIn{from{opacity:0;transform:translateY(6px);}to{opacity:1;transform:translateY(0);}}
.toast{display:flex;align-items:center;gap:10px;padding:12px 16px;background:rgba(0,184,148,.08);border:1px solid rgba(0,184,148,.22);border-radius:var(--r2);color:var(--green);font-size:.81rem;margin-bottom:22px;}
.toast.err{background:rgba(225,112,85,.08);border-color:rgba(225,112,85,.22);color:var(--red);}
.t-dot{width:6px;height:6px;border-radius:50%;background:var(--green);flex-shrink:0;}.toast.err .t-dot{background:var(--red);}
.pg-hd{margin-bottom:26px;}.pg-hd h1{font-family:'Syne',sans-serif;font-size:1.55rem;font-weight:700;color:var(--t1);letter-spacing:-.02em;}.pg-hd p{font-size:.78rem;color:var(--t3);margin-top:4px;}
.card{background:rgba(255,255,255,.62);backdrop-filter:blur(16px);border:1px solid rgba(255,255,255,.85);border-radius:var(--r3);overflow:hidden;margin-bottom:16px;box-shadow:0 8px 32px rgba(0,0,0,.07);}
.card-hd{display:flex;align-items:center;gap:10px;padding:15px 20px;background:rgba(255,255,255,.45);border-bottom:1px solid var(--b1);}
.c-ico{width:28px;height:28px;border-radius:var(--r2);display:flex;align-items:center;justify-content:center;font-size:11px;flex-shrink:0;background:rgba(0,0,0,.08);border:1px solid rgba(0,0,0,.10);color:var(--gold);}
.card-hd h3{font-family:'Syne',sans-serif;font-size:.8rem;font-weight:700;color:var(--t1);}.card-body{padding:20px;}
.fg3{display:grid;grid-template-columns:repeat(3,1fr);gap:13px;}.fg2{display:grid;grid-template-columns:repeat(2,1fr);gap:13px;}
.f{display:flex;flex-direction:column;gap:5px;margin-bottom:13px;}.f:last-child{margin-bottom:0;}
.f>label{font-family:'Syne',sans-serif;font-size:.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--t3);}
.f input,.f select,.f textarea{padding:8px 11px;background:rgba(255,255,255,.60);border:1px solid var(--b2);border-radius:var(--r1);color:var(--t1);font-size:.83rem;font-family:'DM Sans',sans-serif;outline:none;transition:border-color .14s;width:100%;}
.f input:focus,.f select:focus,.f textarea:focus{border-color:var(--b4);box-shadow:0 0 0 3px rgba(0,0,0,.05);background:rgba(255,255,255,.85);}
.f textarea{min-height:85px;resize:vertical;}.f input::placeholder,.f textarea::placeholder{color:var(--t4);}
.ck{display:flex;align-items:center;gap:8px;cursor:pointer;margin-bottom:11px;}
.ck input{appearance:none;-webkit-appearance:none;width:15px;height:15px;background:rgba(255,255,255,.60);border:1px solid var(--b3);border-radius:4px;cursor:pointer;flex-shrink:0;position:relative;transition:all .14s;}
.ck input:checked{background:var(--gold);border-color:var(--gold);}
.ck input:checked::after{content:'';position:absolute;left:3.5px;top:1px;width:5px;height:9px;border:2px solid #fff;border-left:none;border-top:none;transform:rotate(45deg);}
.ck span{font-size:.8rem;color:var(--t2);}
.factions{display:flex;align-items:center;gap:10px;margin-top:18px;padding-top:16px;border-top:1px solid var(--b1);}
.add-card{background:rgba(255,255,255,.50);backdrop-filter:blur(12px);border:1px solid rgba(255,255,255,.80);border-radius:var(--r3);padding:18px 20px;margin-bottom:14px;box-shadow:0 4px 20px rgba(0,0,0,.06);}
.add-hd{font-family:'Syne',sans-serif;font-size:.64rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--t2);margin-bottom:14px;display:flex;align-items:center;gap:7px;}
.btn-p{display:inline-flex;align-items:center;gap:7px;padding:9px 20px;background:linear-gradient(135deg,#1a1a24,#2e2e3c);color:#fff;border:none;border-radius:var(--r2);font-size:.78rem;font-weight:700;font-family:'DM Sans',sans-serif;cursor:pointer;letter-spacing:.04em;transition:all .18s;box-shadow:0 3px 16px rgba(0,0,0,.18);text-decoration:none;}
.btn-p:hover{box-shadow:0 5px 26px rgba(0,0,0,.28);transform:translateY(-1px);}
.btn-g{display:inline-flex;align-items:center;gap:7px;padding:9px 18px;background:rgba(255,255,255,.60);color:var(--t2);border:1px solid var(--b2);border-radius:var(--r2);font-size:.78rem;font-weight:500;font-family:'DM Sans',sans-serif;cursor:pointer;text-decoration:none;transition:all .15s;}
.btn-g:hover{border-color:var(--b3);color:var(--t1);background:rgba(255,255,255,.85);}
.btn-xs{padding:6px 16px!important;font-size:.73rem!important;}
.tbox{background:rgba(255,255,255,.60);backdrop-filter:blur(16px);border:1px solid rgba(255,255,255,.85);border-radius:var(--r3);overflow:hidden;margin-bottom:16px;box-shadow:0 8px 32px rgba(0,0,0,.07);}
table{width:100%;border-collapse:collapse;}
thead th{padding:10px 13px;text-align:left;font-family:'Syne',sans-serif;font-size:.58rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--t3);background:rgba(255,255,255,.40);border-bottom:1px solid var(--b2);white-space:nowrap;}
tbody td{padding:11px 13px;border-bottom:1px solid var(--b1);vertical-align:middle;font-size:.81rem;color:var(--t1);}
tbody tr:last-child td{border-bottom:none;}tbody tr:hover td{background:rgba(255,255,255,.25);}
.timg{width:88px;height:52px;object-fit:contain;background:#f8f8f8;border-radius:var(--r1);border:1px solid var(--b2);display:block;padding:4px;}
.timg-cover{width:88px;height:58px;object-fit:cover;border-radius:var(--r1);border:1px solid var(--b2);display:block;}
.ti{padding:6px 9px;background:rgba(255,255,255,.55);border:1px solid var(--b2);border-radius:var(--r1);color:var(--t1);font-size:.8rem;font-family:'DM Sans',sans-serif;outline:none;transition:border-color .13s;width:100%;}
.ti:focus{border-color:var(--b4);background:rgba(255,255,255,.85);}
.ti-sm{width:78px!important;}
.ta{padding:6px 9px;background:rgba(255,255,255,.55);border:1px solid var(--b2);border-radius:var(--r1);color:var(--t1);font-size:.8rem;font-family:'DM Sans',sans-serif;outline:none;transition:border-color .13s;width:100%;resize:vertical;min-height:70px;}
.ta:focus{border-color:var(--b4);background:rgba(255,255,255,.85);}
.ts{padding:6px 9px;background:rgba(255,255,255,.55);border:1px solid var(--b2);border-radius:var(--r1);color:var(--t1);font-size:.8rem;font-family:'DM Sans',sans-serif;outline:none;cursor:pointer;width:100%;}
.acts{display:flex;flex-wrap:wrap;gap:4px;}
.ab{display:inline-flex;align-items:center;gap:4px;padding:5px 10px;border-radius:var(--r1);font-size:.68rem;font-weight:600;border:1px solid transparent;cursor:pointer;font-family:'DM Sans',sans-serif;transition:all .12s;background:transparent;text-decoration:none;white-space:nowrap;}
.ab-u{border-color:rgba(0,184,148,.22);color:var(--green);background:rgba(0,184,148,.06);}.ab-u:hover{background:rgba(0,184,148,.14);border-color:var(--green);}
.ab-d{border-color:rgba(225,112,85,.22);color:var(--red);background:rgba(225,112,85,.06);}.ab-d:hover{background:rgba(225,112,85,.14);border-color:var(--red);}
.save-bar{position:sticky;bottom:20px;z-index:100;display:flex;align-items:center;justify-content:center;gap:12px;padding:14px 26px;background:rgba(255,255,255,.75);border:1px solid rgba(255,255,255,.90);border-radius:var(--r3);box-shadow:0 8px 40px rgba(0,0,0,.12);margin-top:32px;backdrop-filter:blur(20px);}
.tscroll{overflow-x:auto;}
.img-preview{max-width:180px;height:90px;object-fit:contain;background:#f5f5f5;border-radius:var(--r2);border:1px solid var(--b2);margin-top:6px;opacity:.9;display:block;}
.img-preview-cover{max-width:180px;height:100px;object-fit:cover;border-radius:var(--r2);border:1px solid var(--b2);margin-top:6px;opacity:.9;display:block;}
@media(max-width:840px){.sidebar{display:none;}.main{padding:18px 14px 60px;}.fg3,.fg2{grid-template-columns:1fr;}}
</style>
</head>
<body>
<header class="topbar">
    <a class="brand" href="/lending_word/admin/">
        <div class="brand-mark"><i class="fas fa-handshake"></i></div>
        <span class="brand-name">Porsche</span><div class="brand-div"></div><span class="brand-role">GPC Admin</span>
    </a>
    <div class="topbar-actions">
        <a href="/lending_word/admin/" class="tpill"><i class="fas fa-arrow-left"></i> Dashboard</a>
        <a href="/lending_word/globalpartnershipcouncil.php" target="_blank" class="tpill"><i class="fas fa-up-right-from-square"></i> GPC Page</a>
        <a href="/lending_word/admin/logout.php" class="tpill logout"><i class="fas fa-arrow-right-from-bracket"></i> Logout</a>
    </div>
</header>
<div class="layout">
<aside class="sidebar">
    <div class="sg">
        <div class="sg-label">GPC</div>
        <?php foreach(['contents'=>['fas fa-pen-nib','Page Content'],'partners'=>['fas fa-handshake','Partners'],'cooperations'=>['fas fa-grid-2','Cooperations']] as $k=>[$ic,$lbl]): ?>
        <button class="nav-it <?= $tab===$k?'on':'' ?>" onclick="location.href='?tab=<?= $k ?>'">
            <i class="<?= $ic ?>"></i><?= $lbl ?>
        </button>
        <?php endforeach; ?>
    </div>
    <div class="sdiv"></div>
    <div class="sg">
        <div class="sg-label">Main Admin</div>
        <a class="nav-it" href="/lending_word/admin/"><i class="fas fa-gauge"></i>Dashboard</a>
        <a class="nav-it" href="/lending_word/admin/career.php"><i class="fas fa-briefcase"></i>Career</a>
        <a class="nav-it" href="/lending_word/admin/inquiries.php"><i class="fas fa-inbox"></i>Inquiries<?php if($unreadInquiries>0): ?><span style="margin-left:auto;background:#e17055;color:#fff;font-size:.57rem;font-weight:700;padding:2px 5px;border-radius:10px;"><?= $unreadInquiries ?></span><?php endif; ?></a>
    </div>
</aside>
<main class="main">
<?php if($success): ?><div class="toast"><span class="t-dot"></span><?= htmlspecialchars($success) ?></div><?php endif; ?>
<?php if($error):   ?><div class="toast err"><span class="t-dot"></span><?= htmlspecialchars($error) ?></div><?php endif; ?>

<!-- ══ PAGE CONTENT ══════════════════════════════════════════════════════════ -->
<div class="panel <?= $tab==='contents'?'on':'' ?>">
<div class="pg-hd"><h1>Page Content</h1><p>Teks &amp; konfigurasi halaman Global Partnership Council</p></div>
<form method="POST">
<?php
$grouped = [];
foreach ($contents as $row) $grouped[$row['section']][] = $row;
$sectionMeta = [
    'hero'     => ['fas fa-image',         'Hero Section'],
    'intro'    => ['fas fa-align-left',     'Intro Section'],
    'partners' => ['fas fa-handshake',      'Partners Section'],
    'coop'     => ['fas fa-grid-2',         'Cooperation Section'],
    'misc'     => ['fas fa-ellipsis',       'Miscellaneous'],
];
foreach ($grouped as $section => $items):
    [$ico, $title] = $sectionMeta[$section] ?? ['fas fa-pen', ucfirst($section)];
?>
<div class="card">
    <div class="card-hd"><div class="c-ico"><i class="<?= $ico ?>"></i></div><h3><?= $title ?></h3></div>
    <div class="card-body">
    <?php foreach ($items as $item):
        $colType = $item['type'] ?? 'text';
        $isTA  = $colType === 'textarea';
        $isImg = $colType === 'image';
    ?>
    <div class="f">
        <label>
            <?= ucwords(str_replace('_',' ', $item['label'] ?? $item['key_name'])) ?>
            <span style="font-family:monospace;font-size:.55rem;color:var(--t4);font-weight:400;text-transform:none;letter-spacing:0;margin-left:4px;"><?= htmlspecialchars($item['key_name']) ?></span>
        </label>
        <?php if($isTA): ?>
        <textarea name="cc[<?= htmlspecialchars($item['key_name']) ?>]"><?= htmlspecialchars($item['value']??'') ?></textarea>
        <?php else: ?>
        <input type="text" name="cc[<?= htmlspecialchars($item['key_name']) ?>]" value="<?= htmlspecialchars($item['value']??'') ?>" placeholder="<?= $isImg?'https://...':'' ?>">
        <?php endif; ?>
        <?php if($isImg && !empty($item['value'])): ?>
        <img src="<?= htmlspecialchars($item['value']) ?>" class="img-preview-cover">
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
    </div>
</div>
<?php endforeach; ?>
<div class="save-bar">
    <button type="submit" name="update_contents" class="btn-p"><i class="fas fa-floppy-disk"></i>Save All Contents</button>
    <a href="/lending_word/globalpartnershipcouncil.php" target="_blank" class="btn-g"><i class="fas fa-up-right-from-square"></i>Preview GPC Page</a>
</div>
</form>
</div>

<!-- ══ PARTNERS ══════════════════════════════════════════════════════════════ -->
<div class="panel <?= $tab==='partners'?'on':'' ?>">
<div class="pg-hd"><h1>Partners</h1><p>Logo partner yang tampil di carousel halaman GPC</p></div>
<div class="add-card">
    <div class="add-hd"><i class="fas fa-plus"></i>Tambah Partner</div>
    <form method="POST">
        <div class="fg3" style="margin-bottom:11px;">
            <div class="f" style="margin:0;"><label>Nama *</label><input type="text" name="name" required placeholder="Michelin"></div>
            <div class="f" style="margin:0;"><label>Link URL</label><input type="text" name="link_url" placeholder="https://..."></div>
            <div class="f" style="margin:0;"><label>Sort Order</label><input type="number" name="sort_order" value="0"></div>
        </div>
        <div class="f"><label>Logo URL *</label><input type="text" name="logo_url" placeholder="https://...logo.png"></div>
        <div class="f"><label>Deskripsi (opsional)</label><input type="text" name="description" placeholder="Partner premium..."></div>
        <label class="ck"><input type="checkbox" name="is_active" value="1" checked><span>Aktif</span></label>
        <div class="factions"><button type="submit" name="add_partner" class="btn-p btn-xs"><i class="fas fa-plus"></i>Tambah</button></div>
    </form>
</div>
<div class="tbox"><div class="tscroll"><table>
    <thead><tr><th>Logo</th><th>Nama</th><th>Logo URL</th><th>Link URL</th><th>Deskripsi</th><th>Aktif</th><th>Order</th><th>Aksi</th></tr></thead>
    <tbody>
    <?php foreach ($partners as $p): ?>
    <tr><form method="POST"><input type="hidden" name="id" value="<?= $p['id'] ?>">
        <td><?php if($p['logo_url']): ?><img src="<?= htmlspecialchars($p['logo_url']) ?>" class="timg" onerror="this.style.display='none'"><?php endif; ?></td>
        <td><input class="ti" type="text" name="name" value="<?= htmlspecialchars($p['name']) ?>" style="min-width:110px;"></td>
        <td><input class="ti" type="text" name="logo_url" value="<?= htmlspecialchars($p['logo_url']??'') ?>" style="min-width:160px;"></td>
        <td><input class="ti" type="text" name="link_url" value="<?= htmlspecialchars($p['link_url']??'#') ?>" style="min-width:140px;"></td>
        <td><input class="ti" type="text" name="description" value="<?= htmlspecialchars($p['description']??'') ?>" style="min-width:130px;"></td>
        <td style="text-align:center;"><label class="ck" style="justify-content:center;margin:0;"><input type="checkbox" name="is_active" value="1" <?= !empty($p['is_active'])?'checked':'' ?>><span></span></label></td>
        <td><input class="ti" type="number" name="sort_order" value="<?= $p['sort_order'] ?>" style="width:60px;"></td>
        <td><div class="acts">
            <button type="submit" name="update_partner" class="ab ab-u"><i class="fas fa-check"></i>Update</button>
            <button type="submit" name="delete_partner" class="ab ab-d" onclick="return confirm('Hapus partner ini?')"><i class="fas fa-trash-alt"></i></button>
        </div></td>
    </form></tr>
    <?php endforeach; ?>
    <?php if (empty($partners)): ?><tr><td colspan="8" style="text-align:center;color:var(--t4);padding:40px;">Belum ada partner.</td></tr><?php endif; ?>
    </tbody>
</table></div></div>
</div>

<!-- ══ COOPERATIONS ══════════════════════════════════════════════════════════ -->
<div class="panel <?= $tab==='cooperations'?'on':'' ?>">
<div class="pg-hd"><h1>Cooperation Cards</h1><p>Kartu "Your cooperation opportunities" di halaman GPC — klik kartu akan membuka modal dengan gambar &amp; deskripsi lengkap</p></div>
<div class="add-card">
    <div class="add-hd"><i class="fas fa-plus"></i>Tambah Cooperation</div>
    <form method="POST">
        <div class="fg3" style="margin-bottom:11px;">
            <div class="f" style="margin:0;"><label>Judul *</label><input type="text" name="title" required placeholder="Porsche Motorsport"></div>
            <div class="f" style="margin:0;"><label>Link URL</label><input type="text" name="link_url" placeholder="https://..."></div>
            <div class="f" style="margin:0;"><label>Sort Order</label><input type="number" name="sort_order" value="0"></div>
        </div>
        <div class="f"><label>Image URL *</label><input type="text" name="image_url" placeholder="https://...gambar.jpg"></div>
        <div class="f">
            <label>Deskripsi (muncul di modal)</label>
            <textarea name="description" rows="4" placeholder="Deskripsi lengkap yang muncul ketika user mengklik kartu ini..."></textarea>
        </div>
        <label class="ck"><input type="checkbox" name="is_active" value="1" checked><span>Aktif</span></label>
        <div class="factions"><button type="submit" name="add_coop" class="btn-p btn-xs"><i class="fas fa-plus"></i>Tambah</button></div>
    </form>
</div>
<div class="tbox"><div class="tscroll"><table>
    <thead>
        <tr>
            <th>Preview</th>
            <th>Judul</th>
            <th>Deskripsi</th>
            <th>Image URL</th>
            <th>Link URL</th>
            <th>Aktif</th>
            <th>Order</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($cooperations as $c): ?>
    <tr><form method="POST"><input type="hidden" name="id" value="<?= $c['id'] ?>">
        <td><?php if($c['image_url']): ?><img src="<?= htmlspecialchars($c['image_url']) ?>" class="timg-cover" onerror="this.style.display='none'"><?php endif; ?></td>
        <td><input class="ti" type="text" name="title" value="<?= htmlspecialchars($c['title']) ?>" style="min-width:140px;"></td>
        <td><textarea class="ta" name="description" rows="3" style="min-width:200px;"><?= htmlspecialchars($c['description']??'') ?></textarea></td>
        <td><input class="ti" type="text" name="image_url" value="<?= htmlspecialchars($c['image_url']??'') ?>" style="min-width:170px;"></td>
        <td><input class="ti" type="text" name="link_url"  value="<?= htmlspecialchars($c['link_url']??'#') ?>"  style="min-width:155px;"></td>
        <td style="text-align:center;"><label class="ck" style="justify-content:center;margin:0;"><input type="checkbox" name="is_active" value="1" <?= !empty($c['is_active'])?'checked':'' ?>><span></span></label></td>
        <td><input class="ti" type="number" name="sort_order" value="<?= $c['sort_order'] ?>" style="width:60px;"></td>
        <td><div class="acts">
            <button type="submit" name="update_coop" class="ab ab-u"><i class="fas fa-check"></i>Update</button>
            <button type="submit" name="delete_coop" class="ab ab-d" onclick="return confirm('Hapus?')"><i class="fas fa-trash-alt"></i></button>
        </div></td>
    </form></tr>
    <?php endforeach; ?>
    <?php if (empty($cooperations)): ?><tr><td colspan="8" style="text-align:center;color:var(--t4);padding:40px;">Belum ada cooperation cards.</td></tr><?php endif; ?>
    </tbody>
</table></div></div>
</div>

</main>
</div>
</body>
</html>