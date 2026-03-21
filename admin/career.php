<?php
/**
 * Admin Career — /lending_word/admin/career.php
 */
session_start();
require_once __DIR__ . '/../app/models/Admin.php';
if (!Admin::isLoggedIn()) { header('Location: /lending_word/admin/login.php'); exit; }

require_once __DIR__ . '/../app/database.php';
require_once __DIR__ . '/../app/models/CareerModel.php';

$unreadInquiries = 0;
try { require_once __DIR__ . '/../app/models/VehicleInquiry.php'; $unreadInquiries = (new VehicleInquiry())->countUnread(); } catch(Exception $e){}
$chatUnread = 0;
try { if(file_exists(__DIR__.'/../app/models/ChatSession.php')){ require_once __DIR__.'/../app/models/ChatSession.php'; $chatUnread = (new ChatSession())->countUnread(); } } catch(Exception $e){}

$m   = new CareerModel();
$tab = $_GET['tab'] ?? 'jobs';
$success = ''; $error = '';

// ── JOBS ──────────────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['add_job'])) {
    try { $m->createJob($_POST); $success="Job ditambahkan!"; } catch(Exception $e){ $error=$e->getMessage(); }
}
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['update_job'])) {
    try {
        $m->updateJob((int)$_POST['id'], $_POST);
        if (isset($_POST['tag_ids'])) $m->setJobTags((int)$_POST['id'], array_map('intval', $_POST['tag_ids']));
        else $m->setJobTags((int)$_POST['id'], []);
        $success="Job diupdate!";
    } catch(Exception $e){ $error=$e->getMessage(); }
}
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['delete_job'])) {
    $m->deleteJob((int)$_POST['id']); $success="Job dihapus!";
}

// ── CATEGORIES ────────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['add_category'])) {
    try { $m->createCategory($_POST); $success="Kategori ditambahkan!"; } catch(Exception $e){ $error=$e->getMessage(); }
}
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['update_category'])) {
    $m->updateCategory((int)$_POST['id'], $_POST); $success="Kategori diupdate!";
}
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['delete_category'])) {
    $m->deleteCategory((int)$_POST['id']); $success="Kategori dihapus!";
}

// ── APPLICATIONS ──────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['update_app_status'])) {
    $m->updateApplicationStatus((int)$_POST['id'], $_POST['status'], $_POST['notes']??''); $success="Status diupdate!";
}
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['delete_application'])) {
    $m->deleteApplication((int)$_POST['id']); $success="Lamaran dihapus!";
}

// ── ENTRY CARDS ───────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['add_entry_card'])) {
    try { $m->createEntryCard($_POST); $success="Entry card ditambahkan!"; } catch(Exception $e){ $error=$e->getMessage(); }
}
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['update_entry_card'])) {
    $m->updateEntryCard((int)$_POST['id'], $_POST); $success="Entry card diupdate!";
}
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['delete_entry_card'])) {
    $m->deleteEntryCard((int)$_POST['id']); $success="Entry card dihapus!";
}

// ── SUBSIDIARIES ──────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['add_subsidiary'])) {
    try { $m->createSubsidiary($_POST); $success="Subsidiary ditambahkan!"; } catch(Exception $e){ $error=$e->getMessage(); }
}
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['update_subsidiary'])) {
    $m->updateSubsidiary((int)$_POST['id'], $_POST); $success="Subsidiary diupdate!";
}
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['delete_subsidiary'])) {
    $m->deleteSubsidiary((int)$_POST['id']); $success="Subsidiary dihapus!";
}

// ── TAGS ──────────────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['add_tag'])) {
    try { $m->createTag(trim($_POST['name']??'')); $success="Tag ditambahkan!"; } catch(Exception $e){ $error=$e->getMessage(); }
}
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['delete_tag'])) {
    $m->deleteTag((int)$_POST['id']); $success="Tag dihapus!";
}

// ── CONTENTS ──────────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['update_contents'])) {
    foreach ($_POST['cc'] as $key => $value) $m->upsertContent($key, trim($value));
    $success = "Contents disimpan!";
}

// ── SECTION ORDER ─────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['save_section_order'])) {
    $allowed = ['vacancies','categories','entry','dream','subsidiaries','social','note'];
    $order   = array_filter($_POST['section_order'] ?? [], fn($s) => in_array($s, $allowed));
    // Pastikan semua section ada
    foreach ($allowed as $s) { if (!in_array($s, $order)) $order[] = $s; }
    $m->upsertContent('section_order', implode(',', $order));
    $success = "Urutan section disimpan!";
}

// ── FETCH DATA ────────────────────────────────────────────────────────────────
$allCategories = $m->getAllCategories();
$allJobs       = $m->getJobsWithTags();
$allTags       = $m->getAllTags();
$applications  = $m->getApplications($_GET['status'] ?? '');
$entryCards    = $m->getAllEntryCards();
$subsidiaries  = $m->getAllSubsidiaries();
$contents      = $m->getAllContents();

$totalJobs  = count($allJobs);
$activeJobs = count(array_filter($allJobs, fn($j) => !empty($j['is_active'])));
$totalApps  = count($m->getApplications());
$newApps    = count(array_filter($m->getApplications(), fn($a) => $a['status']==='new'));

// Current section order
$defaultOrder   = 'vacancies,categories,entry,dream,subsidiaries,social,note';
$savedOrder     = $m->getRawContent('section_order', $defaultOrder);
$currentOrder   = array_filter(explode(',', $savedOrder));

$sectionDefs = [
    'vacancies'    => ['fas fa-briefcase',   'Job Vacancies',       'Search box + daftar lowongan terbuka'],
    'categories'   => ['fas fa-tag',         'Categories',          'Grid explore by department'],
    'entry'        => ['fas fa-door-open',   'Entry Opportunities', 'Kartu students & experienced professionals'],
    'dream'        => ['fas fa-star',        'Dream Job',           'Kartu navigasi — FAQ, hotline, dll'],
    'subsidiaries' => ['fas fa-building',    'Subsidiaries',        'Carousel kartu anak perusahaan'],
    'social'       => ['fas fa-share-nodes', 'Social',              'Ikon media sosial'],
    'note'         => ['fas fa-pen-nib',     'Note',                'Teks catatan legal di bawah halaman'],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Career Admin — Porsche Indonesia</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
:root{--bg:#dcdce8;--b1:rgba(0,0,0,0.04);--b2:rgba(0,0,0,0.09);--b3:rgba(0,0,0,0.16);--b4:rgba(0,0,0,0.28);--t1:#12121f;--t2:#4b4b6a;--t3:#9090b0;--t4:#b8b8d0;--gold:#18181e;--gold2:#3a3a4a;--green:#00b894;--red:#e17055;--blue:#0984e3;--r1:8px;--r2:12px;--r3:16px;--r4:100px;}
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
.nav-badge{margin-left:auto;background:var(--red);color:#fff;font-size:.57rem;font-weight:700;padding:2px 5px;border-radius:10px;line-height:1.4;}
.sdiv{height:1px;background:var(--b2);margin:8px 0;}
.main{flex:1;padding:30px 38px 80px;min-width:0;}
.panel{display:none;}.panel.on{display:block;animation:panelIn .22s cubic-bezier(.22,1,.36,1);}
@keyframes panelIn{from{opacity:0;transform:translateY(6px);}to{opacity:1;transform:translateY(0);}}
.toast{display:flex;align-items:center;gap:10px;padding:12px 16px;background:rgba(0,184,148,.08);border:1px solid rgba(0,184,148,.22);border-radius:var(--r2);color:var(--green);font-size:.81rem;margin-bottom:22px;}
.toast.err{background:rgba(225,112,85,.08);border-color:rgba(225,112,85,.22);color:var(--red);}
.t-dot{width:6px;height:6px;border-radius:50%;background:var(--green);flex-shrink:0;}.toast.err .t-dot{background:var(--red);}
.pg-hd{margin-bottom:26px;}.pg-hd h1{font-family:'Syne',sans-serif;font-size:1.55rem;font-weight:700;color:var(--t1);letter-spacing:-.02em;}.pg-hd p{font-size:.78rem;color:var(--t3);margin-top:4px;}
.stats-row{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:24px;}
.stat-card{background:rgba(255,255,255,.62);backdrop-filter:blur(16px);border:1px solid rgba(255,255,255,.85);border-radius:var(--r3);padding:18px 20px;box-shadow:0 8px 32px rgba(0,0,0,.07);}
.stat-card .val{font-family:'Syne',sans-serif;font-size:1.8rem;font-weight:800;color:var(--t1);line-height:1;}.stat-card .lbl{font-size:.72rem;color:var(--t3);margin-top:5px;}.stat-card .sub{font-size:.68rem;color:var(--green);margin-top:4px;}
.card{background:rgba(255,255,255,.62);backdrop-filter:blur(16px);border:1px solid rgba(255,255,255,.85);border-radius:var(--r3);overflow:hidden;margin-bottom:16px;box-shadow:0 8px 32px rgba(0,0,0,.07);}
.card-hd{display:flex;align-items:center;gap:10px;padding:15px 20px;background:rgba(255,255,255,.45);border-bottom:1px solid var(--b1);}
.c-ico{width:28px;height:28px;border-radius:var(--r2);display:flex;align-items:center;justify-content:center;font-size:11px;flex-shrink:0;}.ico-g{background:rgba(0,0,0,.08);border:1px solid rgba(0,0,0,.10);color:var(--gold);}
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
.timg{width:88px;height:58px;object-fit:cover;border-radius:var(--r1);border:1px solid var(--b2);display:block;}
.ti{padding:6px 9px;background:rgba(255,255,255,.55);border:1px solid var(--b2);border-radius:var(--r1);color:var(--t1);font-size:.8rem;font-family:'DM Sans',sans-serif;outline:none;transition:border-color .13s;width:100%;}
.ti:focus{border-color:var(--b4);background:rgba(255,255,255,.85);}
.ti-sm{width:78px!important;}
.ts{padding:6px 9px;background:rgba(255,255,255,.55);border:1px solid var(--b2);border-radius:var(--r1);color:var(--t1);font-size:.8rem;font-family:'DM Sans',sans-serif;outline:none;cursor:pointer;width:100%;}
.ta{padding:6px 9px;background:rgba(255,255,255,.55);border:1px solid var(--b2);border-radius:var(--r1);color:var(--t1);font-size:.8rem;font-family:'DM Sans',sans-serif;outline:none;resize:vertical;min-height:68px;width:100%;}
.acts{display:flex;flex-wrap:wrap;gap:4px;}
.ab{display:inline-flex;align-items:center;gap:4px;padding:5px 10px;border-radius:var(--r1);font-size:.68rem;font-weight:600;border:1px solid transparent;cursor:pointer;font-family:'DM Sans',sans-serif;transition:all .12s;background:transparent;text-decoration:none;white-space:nowrap;}
.ab-u{border-color:rgba(0,184,148,.22);color:var(--green);background:rgba(0,184,148,.06);}.ab-u:hover{background:rgba(0,184,148,.14);border-color:var(--green);}
.ab-d{border-color:rgba(225,112,85,.22);color:var(--red);background:rgba(225,112,85,.06);}.ab-d:hover{background:rgba(225,112,85,.14);border-color:var(--red);}
.ab-l{border-color:var(--b2);color:var(--t2);background:rgba(255,255,255,.40);}.ab-l:hover{border-color:var(--b3);color:var(--t1);}
.ab-info{border-color:rgba(9,132,227,.22);color:var(--blue);background:rgba(9,132,227,.06);}.ab-info:hover{background:rgba(9,132,227,.14);border-color:var(--blue);}
.save-bar{position:sticky;bottom:20px;z-index:100;display:flex;align-items:center;justify-content:center;gap:12px;padding:14px 26px;background:rgba(255,255,255,.75);border:1px solid rgba(255,255,255,.90);border-radius:var(--r3);box-shadow:0 8px 40px rgba(0,0,0,.12);margin-top:32px;backdrop-filter:blur(20px);}
.tscroll{overflow-x:auto;}
.pill-filter{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:20px;}
.pill-filter a{padding:6px 14px;border-radius:var(--r4);font-size:.7rem;font-weight:600;font-family:'Syne',sans-serif;letter-spacing:.08em;text-transform:uppercase;text-decoration:none;border:1px solid var(--b2);background:rgba(255,255,255,.60);color:var(--t2);transition:all .15s;}
.pill-filter a.on{background:#18181e;color:#fff;border-color:#18181e;}
.tag-pill{display:inline-flex;align-items:center;gap:4px;padding:3px 8px;background:rgba(0,0,0,.06);border-radius:var(--r4);font-size:.68rem;color:var(--t2);font-weight:500;}
.tags-checkboxes{display:flex;flex-wrap:wrap;gap:8px;margin-top:6px;}
.tags-checkboxes label{display:flex;align-items:center;gap:5px;padding:4px 10px;border:1px solid var(--b2);border-radius:var(--r4);font-size:.72rem;cursor:pointer;background:rgba(255,255,255,.55);transition:all .14px;}
.tags-checkboxes label:has(input:checked){background:#18181e;color:#fff;border-color:#18181e;}
.tags-checkboxes input{accent-color:#fff;width:12px;height:12px;}

/* SECTION ORDER */
.sortable-list{display:flex;flex-direction:column;gap:10px;max-width:560px;}
.sort-item{display:flex;align-items:center;gap:14px;padding:14px 16px;background:rgba(255,255,255,.70);border:1px solid rgba(255,255,255,.90);border-radius:var(--r2);cursor:grab;user-select:none;transition:box-shadow .15s,transform .15s;box-shadow:0 2px 8px rgba(0,0,0,.06);}
.sort-item:active{cursor:grabbing;}
.sort-item.sortable-ghost{opacity:.35;transform:scale(.98);}
.sort-item.sortable-chosen{box-shadow:0 8px 32px rgba(0,0,0,.14);transform:scale(1.01);background:rgba(255,255,255,.95);}
.sort-item.sortable-drag{box-shadow:0 16px 48px rgba(0,0,0,.18);}
.sort-handle{color:var(--t4);font-size:12px;flex-shrink:0;cursor:grab;}
.sort-num{font-family:'Syne',sans-serif;font-size:.65rem;font-weight:700;color:var(--t4);width:18px;text-align:center;flex-shrink:0;}
.sort-ico{width:32px;height:32px;border-radius:var(--r1);display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.06);border:1px solid rgba(0,0,0,.08);color:var(--t2);font-size:11px;flex-shrink:0;}
.sort-info{flex:1;}
.sort-info strong{font-family:'Syne',sans-serif;font-size:.8rem;font-weight:700;color:var(--t1);display:block;}
.sort-info span{font-size:.72rem;color:var(--t3);}
.sort-fixed-badge{font-family:'Syne',sans-serif;font-size:.58rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--t4);background:rgba(0,0,0,.05);border:1px solid var(--b2);padding:3px 8px;border-radius:var(--r4);}
.sort-fixed-item{display:flex;align-items:center;gap:14px;padding:12px 16px;background:rgba(255,255,255,.35);border:1px dashed var(--b2);border-radius:var(--r2);opacity:.6;}
.sort-fixed-item .sort-ico{background:rgba(0,0,0,.03);}

/* Detail lamaran modal */
.app-modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:500;display:flex;align-items:center;justify-content:center;padding:20px;opacity:0;pointer-events:none;transition:opacity .2s;backdrop-filter:blur(6px);}
.app-modal-overlay.open{opacity:1;pointer-events:all;}
.app-modal{background:#fff;width:100%;max-width:560px;max-height:88vh;overflow-y:auto;border-radius:var(--r3);box-shadow:0 24px 64px rgba(0,0,0,.22);transform:translateY(16px);transition:transform .25s cubic-bezier(.22,1,.36,1);}
.app-modal-overlay.open .app-modal{transform:translateY(0);}
.app-modal-hd{display:flex;align-items:flex-start;justify-content:space-between;gap:12px;padding:22px 24px 18px;border-bottom:1px solid var(--b2);position:sticky;top:0;background:#fff;z-index:2;border-radius:var(--r3) var(--r3) 0 0;}
.app-modal-hd h3{font-family:'Syne',sans-serif;font-size:1rem;font-weight:700;color:var(--t1);}
.app-modal-hd p{font-size:.75rem;color:var(--t3);margin-top:3px;}
.app-modal-close{width:30px;height:30px;border:1px solid var(--b2);background:transparent;border-radius:var(--r1);display:flex;align-items:center;justify-content:center;font-size:11px;color:var(--t3);cursor:pointer;flex-shrink:0;transition:all .15s;}
.app-modal-close:hover{background:var(--t1);color:#fff;border-color:var(--t1);}
.app-modal-body{padding:22px 24px;}
.app-row{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px;}
.app-field{display:flex;flex-direction:column;gap:4px;}
.app-field label{font-family:'Syne',sans-serif;font-size:.58rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--t3);}
.app-field span{font-size:.83rem;color:var(--t1);word-break:break-all;}
.app-field a{font-size:.83rem;color:var(--blue);text-decoration:none;word-break:break-all;}
.app-field a:hover{text-decoration:underline;}
.app-divider{height:1px;background:var(--b2);margin:16px 0;}
.app-cover{background:rgba(0,0,0,.03);border:1px solid var(--b1);border-radius:var(--r1);padding:12px 14px;font-size:.82rem;color:var(--t2);line-height:1.7;white-space:pre-wrap;max-height:180px;overflow-y:auto;}

@media(max-width:840px){.sidebar{display:none;}.main{padding:18px 14px 60px;}.fg3,.fg2{grid-template-columns:1fr;}.stats-row{grid-template-columns:repeat(2,1fr);}.app-row{grid-template-columns:1fr;}}
</style>
</head>
<body>
<header class="topbar">
    <a class="brand" href="/lending_word/admin/">
        <div class="brand-mark"><i class="fas fa-shield-halved"></i></div>
        <span class="brand-name">Porsche</span><div class="brand-div"></div><span class="brand-role">Admin</span>
    </a>
    <div class="topbar-actions">
        <a href="/lending_word/admin/" class="tpill"><i class="fas fa-arrow-left"></i> Dashboard</a>
        <a href="/lending_word/career.php" target="_blank" class="tpill"><i class="fas fa-up-right-from-square"></i> Career Page</a>
        <a href="/lending_word/admin/logout.php" class="tpill logout"><i class="fas fa-arrow-right-from-bracket"></i> Logout</a>
    </div>
</header>
<div class="layout">
<aside class="sidebar">
    <div class="sg">
        <div class="sg-label">Career</div>
        <?php foreach([
            'jobs'         => ['fas fa-briefcase',   'Job Vacancies'],
            'categories'   => ['fas fa-tag',         'Categories'],
            'tags'         => ['fas fa-hashtag',      'Tags'],
            'applications' => ['fas fa-inbox',        'Applications'],
            'entry'        => ['fas fa-door-open',    'Entry Cards'],
            'subsidiaries' => ['fas fa-building',     'Subsidiaries'],
            'contents'     => ['fas fa-pen-nib',      'Page Content'],
            'order'        => ['fas fa-grip-lines',   'Section Order'],
        ] as $k=>[$ic,$lbl]): ?>
        <button class="nav-it <?= $tab===$k?'on':'' ?>" onclick="location.href='?tab=<?= $k ?>'">
            <i class="<?= $ic ?>"></i><?= $lbl ?>
            <?php if($k==='applications' && $newApps>0): ?><span class="nav-badge"><?= $newApps ?></span><?php endif; ?>
        </button>
        <?php endforeach; ?>
    </div>
    <div class="sdiv"></div>
    <div class="sg">
        <div class="sg-label">Main Admin</div>
        <a class="nav-it" href="/lending_word/admin/"><i class="fas fa-gauge"></i>Dashboard</a>
        <a class="nav-it" href="/lending_word/admin/inquiries.php"><i class="fas fa-inbox"></i>Inquiries<?php if($unreadInquiries>0): ?><span class="nav-badge"><?= $unreadInquiries ?></span><?php endif; ?></a>
        <a class="nav-it" href="/lending_word/admin/chat.php"><i class="fas fa-comments"></i>Live Chat<?php if($chatUnread>0): ?><span class="nav-badge"><?= $chatUnread ?></span><?php endif; ?></a>
    </div>
</aside>
<main class="main">
<?php if($success): ?><div class="toast"><span class="t-dot"></span><?= htmlspecialchars($success) ?></div><?php endif; ?>
<?php if($error):   ?><div class="toast err"><span class="t-dot"></span><?= htmlspecialchars($error) ?></div><?php endif; ?>

<!-- ══ TAB: JOBS ══════════════════════════════════════════════════════════════ -->
<div class="panel <?= $tab==='jobs'?'on':'' ?>">
<div class="pg-hd"><h1>Job Vacancies</h1><p>Kelola lowongan yang tampil di halaman career</p></div>
<div class="stats-row">
    <div class="stat-card"><div class="val"><?= $totalJobs ?></div><div class="lbl">Total Jobs</div></div>
    <div class="stat-card"><div class="val"><?= $activeJobs ?></div><div class="lbl">Active Jobs</div><div class="sub">tampil frontend</div></div>
    <div class="stat-card"><div class="val"><?= $totalApps ?></div><div class="lbl">Total Lamaran</div></div>
    <div class="stat-card"><div class="val"><?= $newApps ?></div><div class="lbl">New Applications</div></div>
</div>
<div class="add-card">
    <div class="add-hd"><i class="fas fa-plus"></i>Tambah Lowongan</div>
    <form method="POST">
        <div class="fg3" style="margin-bottom:11px;">
            <div class="f" style="margin:0;"><label>Judul *</label><input type="text" name="title" required placeholder="Sales Consultant"></div>
            <div class="f" style="margin:0;"><label>Kategori</label>
                <select name="category_id"><option value="">— Pilih —</option>
                <?php foreach($allCategories as $c): ?><option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="f" style="margin:0;"><label>Lokasi</label><input type="text" name="location" placeholder="Jakarta Selatan"></div>
        </div>
        <div class="f"><label>Deskripsi Singkat</label><input type="text" name="short_desc" placeholder="Kalimat pembuka…"></div>
        <div class="f"><label>Deskripsi Lengkap</label><textarea name="description" style="min-height:90px;"></textarea></div>
        <div class="f"><label>Requirements (tiap baris = 1 poin)</label><textarea name="requirements" style="min-height:75px;" placeholder="Min 2 tahun pengalaman&#10;Kemampuan komunikasi"></textarea></div>
        <div class="fg3" style="margin-bottom:11px;">
            <div class="f" style="margin:0;"><label>Tipe</label>
                <select name="employment_type"><?php foreach(['Full-time','Part-time','Contract','Internship','Freelance'] as $et): ?><option><?= $et ?></option><?php endforeach; ?></select>
            </div>
            <div class="f" style="margin:0;"><label>Level</label>
                <select name="experience_level"><option value="">— Any —</option><?php foreach(['Entry Level','Mid Level','Senior Level','Manager','Director'] as $el): ?><option><?= $el ?></option><?php endforeach; ?></select>
            </div>
            <div class="f" style="margin:0;"><label>Salary Range</label><input type="text" name="salary_range" placeholder="Rp 8–12 juta/bln"></div>
        </div>
        <div class="fg3" style="margin-bottom:11px;">
            <div class="f" style="margin:0;"><label>Apply URL</label><input type="text" name="apply_url"></div>
            <div class="f" style="margin:0;"><label>Apply Email</label><input type="email" name="apply_email"></div>
            <div class="f" style="margin:0;"><label>Deadline</label><input type="date" name="deadline"></div>
        </div>
        <div class="fg3" style="margin-bottom:11px;">
            <div class="f" style="margin:0;"><label>Sort Order</label><input type="number" name="sort_order" value="0"></div>
            <div style="display:flex;flex-direction:column;gap:6px;padding-top:22px;">
                <label class="ck"><input type="checkbox" name="is_active"   value="1" checked><span>Aktif</span></label>
                <label class="ck"><input type="checkbox" name="is_featured" value="1"><span>Featured</span></label>
                <label class="ck"><input type="checkbox" name="is_urgent"   value="1"><span>Urgent</span></label>
            </div>
        </div>
        <?php if (!empty($allTags)): ?>
        <div class="f" style="margin-bottom:13px;">
            <label>Tags</label>
            <div class="tags-checkboxes">
                <?php foreach($allTags as $t): ?>
                <label><input type="checkbox" name="tag_ids[]" value="<?= $t['id'] ?>"><?= htmlspecialchars($t['name']) ?></label>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        <div class="factions" style="margin-top:12px;padding-top:12px;">
            <button type="submit" name="add_job" class="btn-p btn-xs"><i class="fas fa-plus"></i>Tambah</button>
        </div>
    </form>
</div>
<div class="tbox"><div class="tscroll"><table>
    <thead><tr><th>Judul</th><th>Kategori</th><th>Lokasi</th><th>Tipe</th><th>Salary</th><th>Apply</th><th>Deadline</th><th>Tags</th><th>Flag</th><th>Aktif</th><th>Order</th><th>Aksi</th></tr></thead>
    <tbody>
    <?php foreach($allJobs as $j): ?>
    <tr><form method="POST">
        <input type="hidden" name="id" value="<?= $j['id'] ?>">
        <td><input class="ti" type="text" name="title" value="<?= htmlspecialchars($j['title']) ?>" style="min-width:150px;"></td>
        <td><select class="ts" name="category_id" style="min-width:120px;"><option value="">—</option>
            <?php foreach($allCategories as $c): ?><option value="<?= $c['id'] ?>" <?= $j['category_id']==$c['id']?'selected':'' ?>><?= htmlspecialchars($c['name']) ?></option><?php endforeach; ?></select></td>
        <td><input class="ti" type="text" name="location" value="<?= htmlspecialchars($j['location']??'') ?>" style="min-width:100px;"></td>
        <td><select class="ts" name="employment_type" style="min-width:95px;"><?php foreach(['Full-time','Part-time','Contract','Internship','Freelance'] as $et): ?><option <?= ($j['employment_type']??'')===$et?'selected':'' ?>><?= $et ?></option><?php endforeach; ?></select></td>
        <td><input class="ti" type="text" name="salary_range" value="<?= htmlspecialchars($j['salary_range']??'') ?>" style="min-width:105px;"></td>
        <td style="min-width:145px;">
            <input class="ti" type="text" name="apply_url"   value="<?= htmlspecialchars($j['apply_url']??'') ?>" placeholder="URL" style="margin-bottom:4px;">
            <input class="ti" type="text" name="apply_email" value="<?= htmlspecialchars($j['apply_email']??'') ?>" placeholder="Email">
        </td>
        <td><input class="ti" type="date" name="deadline" value="<?= htmlspecialchars($j['deadline']??'') ?>" style="min-width:120px;"></td>
        <td style="min-width:130px;">
            <div class="tags-checkboxes" style="flex-direction:column;gap:4px;">
                <?php foreach($allTags as $t):
                    $checked = in_array($t['name'], array_column($j['tags']??[], 'name'));
                ?>
                <label style="font-size:.68rem;"><input type="checkbox" name="tag_ids[]" value="<?= $t['id'] ?>" <?= $checked?'checked':'' ?>><?= htmlspecialchars($t['name']) ?></label>
                <?php endforeach; ?>
            </div>
        </td>
        <td>
            <label class="ck" style="margin:0 0 4px;"><input type="checkbox" name="is_featured" value="1" <?= !empty($j['is_featured'])?'checked':'' ?>><span style="font-size:.72rem;">Featured</span></label>
            <label class="ck" style="margin:0;"><input type="checkbox" name="is_urgent" value="1" <?= !empty($j['is_urgent'])?'checked':'' ?>><span style="font-size:.72rem;">Urgent</span></label>
        </td>
        <td style="text-align:center;"><label class="ck" style="justify-content:center;margin:0;"><input type="checkbox" name="is_active" value="1" <?= !empty($j['is_active'])?'checked':'' ?>><span></span></label></td>
        <td><input class="ti ti-sm" type="number" name="sort_order" value="<?= $j['sort_order'] ?>"></td>
        <td><div class="acts">
            <button type="submit" name="update_job" class="ab ab-u"><i class="fas fa-check"></i>Update</button>
            <button type="submit" name="delete_job" class="ab ab-d" onclick="return confirm('Hapus?')"><i class="fas fa-trash-alt"></i></button>
        </div></td>
    </form></tr>
    <?php endforeach; ?>
    <?php if(empty($allJobs)): ?><tr><td colspan="12" style="text-align:center;color:var(--t4);padding:40px;">Belum ada lowongan.</td></tr><?php endif; ?>
    </tbody>
</table></div></div>
</div>

<!-- ══ TAB: CATEGORIES ════════════════════════════════════════════════════════ -->
<div class="panel <?= $tab==='categories'?'on':'' ?>">
<div class="pg-hd"><h1>Job Categories</h1><p>Kategori dengan warna &amp; ikon untuk filter</p></div>
<div class="add-card"><div class="add-hd"><i class="fas fa-plus"></i>Tambah Kategori</div>
    <form method="POST">
        <div class="fg3" style="margin-bottom:11px;">
            <div class="f" style="margin:0;"><label>Nama *</label><input type="text" name="name" required placeholder="Sales &amp; Marketing"></div>
            <div class="f" style="margin:0;"><label>Icon (FA class)</label><input type="text" name="icon" placeholder="fas fa-chart-line" value="fas fa-briefcase"></div>
            <div class="f" style="margin:0;"><label>Warna</label><input type="color" name="color" value="#c9a84c"></div>
        </div>
        <div class="f"><label>Deskripsi</label><input type="text" name="description" placeholder="Deskripsi singkat kategori"></div>
        <div class="fg2" style="margin-bottom:11px;">
            <div class="f" style="margin:0;"><label>Sort Order</label><input type="number" name="sort_order" value="0"></div>
            <div style="padding-top:22px;"><label class="ck"><input type="checkbox" name="is_active" value="1" checked><span>Aktif</span></label></div>
        </div>
        <div class="factions"><button type="submit" name="add_category" class="btn-p btn-xs"><i class="fas fa-plus"></i>Tambah</button></div>
    </form>
</div>
<div class="tbox"><table>
    <thead><tr><th>Icon</th><th>Nama</th><th>Slug</th><th>Deskripsi</th><th>Warna</th><th>Jobs</th><th>Aktif</th><th>Order</th><th>Aksi</th></tr></thead>
    <tbody>
    <?php foreach($allCategories as $c): ?>
    <?php $jc=count(array_filter($allJobs,fn($j)=>$j['category_id']==$c['id'])); ?>
    <tr><form method="POST"><input type="hidden" name="id" value="<?= $c['id'] ?>">
        <td>
            <input class="ti" type="text" name="icon" value="<?= htmlspecialchars($c['icon']??'fas fa-briefcase') ?>" style="min-width:130px;">
            <div style="margin-top:4px;color:<?= htmlspecialchars($c['color']??'#c9a84c') ?>;font-size:1.1rem;"><i class="<?= htmlspecialchars($c['icon']??'fas fa-briefcase') ?>"></i></div>
        </td>
        <td><input class="ti" type="text" name="name" value="<?= htmlspecialchars($c['name']) ?>" style="min-width:140px;"></td>
        <td><span style="font-size:.75rem;color:var(--t3);font-family:monospace;"><?= htmlspecialchars($c['slug']) ?></span></td>
        <td><input class="ti" type="text" name="description" value="<?= htmlspecialchars($c['description']??'') ?>" style="min-width:160px;"></td>
        <td><input type="color" name="color" value="<?= htmlspecialchars($c['color']??'#c9a84c') ?>" style="width:44px;height:32px;padding:2px;cursor:pointer;border:1px solid var(--b2);border-radius:var(--r1);"></td>
        <td style="font-size:.8rem;color:var(--t3);"><?= $jc ?></td>
        <td style="text-align:center;"><label class="ck" style="justify-content:center;margin:0;"><input type="checkbox" name="is_active" value="1" <?= !empty($c['is_active'])?'checked':'' ?>><span></span></label></td>
        <td><input class="ti ti-sm" type="number" name="sort_order" value="<?= $c['sort_order'] ?>"></td>
        <td><div class="acts">
            <button type="submit" name="update_category" class="ab ab-u"><i class="fas fa-check"></i>Update</button>
            <button type="submit" name="delete_category" class="ab ab-d" onclick="return confirm('Hapus?')"><i class="fas fa-trash-alt"></i></button>
        </div></td>
    </form></tr>
    <?php endforeach; ?>
    <?php if(empty($allCategories)): ?><tr><td colspan="9" style="text-align:center;color:var(--t4);padding:28px;">Belum ada kategori.</td></tr><?php endif; ?>
    </tbody>
</table></div>
</div>

<!-- ══ TAB: TAGS ═════════════════════════════════════════════════════════════ -->
<div class="panel <?= $tab==='tags'?'on':'' ?>">
<div class="pg-hd"><h1>Job Tags</h1><p>Label tambahan untuk lowongan</p></div>
<div class="add-card"><div class="add-hd"><i class="fas fa-plus"></i>Tambah Tag Baru</div>
    <form method="POST" style="display:flex;gap:10px;align-items:flex-end;">
        <div class="f" style="margin:0;flex:1;"><label>Nama Tag *</label><input type="text" name="name" required placeholder="Remote Friendly"></div>
        <button type="submit" name="add_tag" class="btn-p btn-xs" style="flex-shrink:0;"><i class="fas fa-plus"></i>Tambah</button>
    </form>
</div>
<div class="tbox"><table>
    <thead><tr><th>Nama</th><th>Slug</th><th>Dibuat</th><th>Aksi</th></tr></thead>
    <tbody>
    <?php foreach($allTags as $t): ?>
    <tr>
        <td><span class="tag-pill"><?= htmlspecialchars($t['name']) ?></span></td>
        <td><span style="font-size:.75rem;color:var(--t3);font-family:monospace;"><?= htmlspecialchars($t['slug']) ?></span></td>
        <td style="font-size:.75rem;color:var(--t3);"><?= date('d M Y', strtotime($t['created_at'] ?? 'now')) ?></td>
        <td><form method="POST" style="display:inline;">
            <input type="hidden" name="id" value="<?= $t['id'] ?>">
            <button type="submit" name="delete_tag" class="ab ab-d" onclick="return confirm('Hapus tag ini?')"><i class="fas fa-trash-alt"></i>Hapus</button>
        </form></td>
    </tr>
    <?php endforeach; ?>
    <?php if(empty($allTags)): ?><tr><td colspan="4" style="text-align:center;color:var(--t4);padding:28px;">Belum ada tags.</td></tr><?php endif; ?>
    </tbody>
</table></div>
</div>

<!-- ══ TAB: APPLICATIONS ════════════════════════════════════════════════════ -->
<div class="panel <?= $tab==='applications'?'on':'' ?>">
<div class="pg-hd"><h1>Lamaran Masuk</h1><p>Pipeline rekrutmen career page</p></div>
<?php if($newApps>0): ?><div class="toast" style="margin-bottom:20px;"><span class="t-dot"></span>Ada <strong><?= $newApps ?> lamaran baru</strong> belum diproses.</div><?php endif; ?>
<?php $filterStatus=$_GET['status']??''; ?>
<div class="pill-filter">
    <?php foreach([''=> 'All','new'=>'New','reviewing'=>'Reviewing','shortlisted'=>'Shortlisted','interview'=>'Interview','offered'=>'Offered','hired'=>'Hired','rejected'=>'Rejected'] as $s=>$lbl): ?>
    <a href="?tab=applications&status=<?= $s ?>" class="<?= $filterStatus===$s?'on':'' ?>"><?= $lbl ?></a>
    <?php endforeach; ?>
</div>
<div class="tbox"><div class="tscroll"><table>
    <thead><tr>
        <th>Nama</th><th>Posisi</th><th>Email</th><th>Phone</th>
        <th>LinkedIn / Portfolio</th><th>Status</th><th>Tanggal</th><th>Aksi</th>
    </tr></thead>
    <tbody>
    <?php foreach($applications as $a): ?>
    <tr><form method="POST"><input type="hidden" name="id" value="<?= $a['id'] ?>">
        <td>
            <strong><?= htmlspecialchars($a['full_name'] ?? '—') ?></strong>
            <?php if(!empty($a['source'])): ?><div style="font-size:.68rem;color:var(--t4);margin-top:2px;"><?= htmlspecialchars($a['source']) ?></div><?php endif; ?>
        </td>
        <td style="font-size:.78rem;color:var(--t3);"><?= htmlspecialchars($a['job_title']??'—') ?></td>
        <td><?php if(!empty($a['email'])): ?><a href="mailto:<?= htmlspecialchars($a['email']) ?>" style="color:var(--blue);font-size:.8rem;text-decoration:none;"><?= htmlspecialchars($a['email']) ?></a><?php else: ?>—<?php endif; ?></td>
        <td style="font-size:.8rem;"><?= htmlspecialchars($a['phone']??'—') ?></td>
        <td style="font-size:.78rem;">
            <?php if(!empty($a['linkedin_url'])): ?><a href="<?= htmlspecialchars($a['linkedin_url']) ?>" target="_blank" style="color:var(--blue);text-decoration:none;display:flex;align-items:center;gap:4px;margin-bottom:3px;"><i class="fab fa-linkedin"></i> LinkedIn</a><?php endif; ?>
            <?php if(!empty($a['portfolio_url'])): ?><a href="<?= htmlspecialchars($a['portfolio_url']) ?>" target="_blank" style="color:var(--blue);text-decoration:none;display:flex;align-items:center;gap:4px;"><i class="fas fa-link"></i> Portfolio/CV</a><?php endif; ?>
            <?php if(empty($a['linkedin_url']) && empty($a['portfolio_url'])): ?>—<?php endif; ?>
        </td>
        <td><select class="ts" name="status" style="min-width:105px;">
            <?php foreach(['new','reviewing','shortlisted','interview','offered','hired','rejected'] as $s): ?>
            <option value="<?= $s ?>" <?= $a['status']===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
            <?php endforeach; ?>
        </select></td>
        <td style="font-size:.75rem;color:var(--t3);white-space:nowrap;"><?= date('d M Y',strtotime($a['created_at'])) ?></td>
        <td><div class="acts">
            <button type="submit" name="update_app_status" class="ab ab-u"><i class="fas fa-check"></i>Update</button>
            <button type="button" class="ab ab-info" onclick='openAppDetail(<?= htmlspecialchars(json_encode($a), ENT_QUOTES) ?>)'><i class="fas fa-eye"></i>Detail</button>
            <button type="submit" name="delete_application" class="ab ab-d" onclick="return confirm('Hapus lamaran ini?')"><i class="fas fa-trash-alt"></i></button>
        </div></td>
    </form></tr>
    <?php endforeach; ?>
    <?php if(empty($applications)): ?><tr><td colspan="8" style="text-align:center;color:var(--t4);padding:40px;">Belum ada lamaran.</td></tr><?php endif; ?>
    </tbody>
</table></div></div>
</div>

<!-- ══ TAB: ENTRY CARDS ══════════════════════════════════════════════════════ -->
<div class="panel <?= $tab==='entry'?'on':'' ?>">
<div class="pg-hd"><h1>Entry Opportunity Cards</h1><p>Section "Your first step into Porsche"</p></div>
<div class="add-card"><div class="add-hd"><i class="fas fa-plus"></i>Tambah Entry Card</div>
    <form method="POST">
        <div class="fg3" style="margin-bottom:11px;">
            <div class="f" style="margin:0;"><label>Tab Group *</label><select name="tab_group"><option value="students">Students &amp; Graduates</option><option value="experienced">Experienced</option></select></div>
            <div class="f" style="margin:0;"><label>Judul *</label><input type="text" name="title" required></div>
            <div class="f" style="margin:0;"><label>Tag / Badge</label><input type="text" name="tag" placeholder="Students · Graduate"></div>
        </div>
        <div class="f"><label>Deskripsi</label><textarea name="description" style="min-height:70px;"></textarea></div>
        <div class="fg3" style="margin-bottom:11px;">
            <div class="f" style="margin:0;"><label>Image URL *</label><input type="text" name="image" placeholder="https://…"></div>
            <div class="f" style="margin:0;"><label>Link URL</label><input type="text" name="link_url" placeholder="#vacancies"></div>
            <div class="f" style="margin:0;"><label>Sort Order</label><input type="number" name="sort_order" value="0"></div>
        </div>
        <label class="ck"><input type="checkbox" name="is_active" value="1" checked><span>Aktif</span></label>
        <div class="factions"><button type="submit" name="add_entry_card" class="btn-p btn-xs"><i class="fas fa-plus"></i>Tambah</button></div>
    </form>
</div>
<div class="tbox"><div class="tscroll"><table>
    <thead><tr><th>Preview</th><th>Tab</th><th>Judul</th><th>Tag</th><th>Deskripsi</th><th>Image URL</th><th>Link</th><th>Aktif</th><th>Order</th><th>Aksi</th></tr></thead>
    <tbody>
    <?php foreach($entryCards as $ec): ?>
    <tr><form method="POST"><input type="hidden" name="id" value="<?= $ec['id'] ?>">
        <td><?php if($ec['image']): ?><img src="<?= htmlspecialchars($ec['image']) ?>" class="timg" onerror="this.style.display='none'"><?php endif; ?></td>
        <td><select class="ts" name="tab_group" style="min-width:95px;"><option value="students" <?= $ec['tab_group']==='students'?'selected':'' ?>>Students</option><option value="experienced" <?= $ec['tab_group']==='experienced'?'selected':'' ?>>Experienced</option></select></td>
        <td><input class="ti" type="text" name="title" value="<?= htmlspecialchars($ec['title']) ?>" style="min-width:115px;"></td>
        <td><input class="ti" type="text" name="tag"   value="<?= htmlspecialchars($ec['tag']??'') ?>" style="min-width:75px;"></td>
        <td><textarea class="ta" name="description" style="min-height:55px;"><?= htmlspecialchars($ec['description']??'') ?></textarea></td>
        <td><input class="ti" type="text" name="image"    value="<?= htmlspecialchars($ec['image']??'') ?>" style="min-width:145px;"></td>
        <td><input class="ti" type="text" name="link_url" value="<?= htmlspecialchars($ec['link_url']??'') ?>" style="min-width:125px;"></td>
        <td style="text-align:center;"><label class="ck" style="justify-content:center;margin:0;"><input type="checkbox" name="is_active" value="1" <?= !empty($ec['is_active'])?'checked':'' ?>><span></span></label></td>
        <td><input class="ti ti-sm" type="number" name="sort_order" value="<?= $ec['sort_order'] ?>"></td>
        <td><div class="acts">
            <button type="submit" name="update_entry_card" class="ab ab-u"><i class="fas fa-check"></i>Update</button>
            <button type="submit" name="delete_entry_card" class="ab ab-d" onclick="return confirm('Hapus?')"><i class="fas fa-trash-alt"></i></button>
        </div></td>
    </form></tr>
    <?php endforeach; ?>
    <?php if(empty($entryCards)): ?><tr><td colspan="10" style="text-align:center;color:var(--t4);padding:28px;">Belum ada entry cards.</td></tr><?php endif; ?>
    </tbody>
</table></div></div>
</div>

<!-- ══ TAB: SUBSIDIARIES ════════════════════════════════════════════════════ -->
<div class="panel <?= $tab==='subsidiaries'?'on':'' ?>">
<div class="pg-hd"><h1>Subsidiaries</h1><p>Kartu anak perusahaan di career page</p></div>
<div class="add-card"><div class="add-hd"><i class="fas fa-plus"></i>Tambah Subsidiary</div>
    <form method="POST">
        <div class="fg3" style="margin-bottom:11px;">
            <div class="f" style="margin:0;"><label>Nama *</label><input type="text" name="name" required placeholder="Porsche Deutschland"></div>
            <div class="f" style="margin:0;"><label>Link URL</label><input type="text" name="link_url" placeholder="https://…"></div>
            <div class="f" style="margin:0;"><label>Sort Order</label><input type="number" name="sort_order" value="0"></div>
        </div>
        <div class="f"><label>Deskripsi</label><textarea name="description" style="min-height:70px;"></textarea></div>
        <div class="f"><label>Image URL *</label><input type="text" name="image" required placeholder="https://…"></div>
        <label class="ck"><input type="checkbox" name="is_active" value="1" checked><span>Aktif</span></label>
        <div class="factions"><button type="submit" name="add_subsidiary" class="btn-p btn-xs"><i class="fas fa-plus"></i>Tambah</button></div>
    </form>
</div>
<div class="tbox"><div class="tscroll"><table>
    <thead><tr><th>Preview</th><th>Nama</th><th>Deskripsi</th><th>Image URL</th><th>Link URL</th><th>Aktif</th><th>Order</th><th>Aksi</th></tr></thead>
    <tbody>
    <?php foreach($subsidiaries as $s): ?>
    <tr><form method="POST"><input type="hidden" name="id" value="<?= $s['id'] ?>">
        <td><?php if($s['image']): ?><img src="<?= htmlspecialchars($s['image']) ?>" class="timg" onerror="this.style.display='none'"><?php endif; ?></td>
        <td><input class="ti" type="text" name="name"     value="<?= htmlspecialchars($s['name']) ?>" style="min-width:135px;"></td>
        <td><textarea class="ta" name="description" style="min-height:55px;"><?= htmlspecialchars($s['description']??'') ?></textarea></td>
        <td><input class="ti" type="text" name="image"    value="<?= htmlspecialchars($s['image']??'') ?>" style="min-width:155px;"></td>
        <td><input class="ti" type="text" name="link_url" value="<?= htmlspecialchars($s['link_url']??'') ?>" style="min-width:155px;"></td>
        <td style="text-align:center;"><label class="ck" style="justify-content:center;margin:0;"><input type="checkbox" name="is_active" value="1" <?= !empty($s['is_active'])?'checked':'' ?>><span></span></label></td>
        <td><input class="ti ti-sm" type="number" name="sort_order" value="<?= $s['sort_order'] ?>"></td>
        <td><div class="acts">
            <button type="submit" name="update_subsidiary" class="ab ab-u"><i class="fas fa-check"></i>Update</button>
            <button type="submit" name="delete_subsidiary" class="ab ab-d" onclick="return confirm('Hapus?')"><i class="fas fa-trash-alt"></i></button>
        </div></td>
    </form></tr>
    <?php endforeach; ?>
    <?php if(empty($subsidiaries)): ?><tr><td colspan="8" style="text-align:center;color:var(--t4);padding:28px;">Belum ada subsidiaries.</td></tr><?php endif; ?>
    </tbody>
</table></div></div>
</div>

<!-- ══ TAB: CONTENTS ════════════════════════════════════════════════════════ -->
<div class="panel <?= $tab==='contents'?'on':'' ?>">
<div class="pg-hd"><h1>Page Content</h1><p>Teks &amp; konfigurasi halaman — tabel <code style="font-size:.78rem;background:rgba(0,0,0,.06);padding:2px 6px;border-radius:4px;">career_contents</code></p></div>
<form method="POST">
<?php
$grouped=[];
foreach($contents as $row) $grouped[$row['section']][]=$row;
$sectionMeta=['hero'=>['fas fa-image','Hero Section'],'search'=>['fas fa-magnifying-glass','Search Section'],'entry'=>['fas fa-door-open','Entry Opportunities'],'dream'=>['fas fa-star','Dream Job Section'],'subs'=>['fas fa-building','Subsidiaries'],'social'=>['fas fa-share-nodes','Social Section'],'hotline'=>['fas fa-phone','Hotline CTA'],'misc'=>['fas fa-ellipsis','Miscellaneous']];
foreach($grouped as $section=>$items):
    if($section==='section_order') continue; // skip internal key
    [$ico,$title]=$sectionMeta[$section]??['fas fa-pen',ucfirst($section)];
?>
<div class="card">
    <div class="card-hd"><div class="c-ico ico-g"><i class="<?= $ico ?>"></i></div><h3><?= $title ?></h3></div>
    <div class="card-body">
    <?php foreach($items as $item):
        $colType = $item['type'] ?? $item['content_type'] ?? 'text';
        $isTA    = $colType === 'textarea';
        $isImg   = $colType === 'image';
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
        <img src="<?= htmlspecialchars($item['value']) ?>" style="max-width:200px;height:100px;object-fit:cover;border-radius:var(--r2);border:1px solid var(--b2);margin-top:6px;opacity:.85;">
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
    </div>
</div>
<?php endforeach; ?>
<div class="save-bar">
    <button type="submit" name="update_contents" class="btn-p"><i class="fas fa-floppy-disk"></i>Save All Contents</button>
    <a href="/lending_word/career.php" target="_blank" class="btn-g"><i class="fas fa-up-right-from-square"></i>Preview Career Page</a>
</div>
</form>
</div>

<!-- ══ TAB: SECTION ORDER ═════════════════════════════════════════════════════ -->
<div class="panel <?= $tab==='order'?'on':'' ?>">
<div class="pg-hd">
    <h1>Section Order</h1>
    <p>Drag &amp; drop untuk mengatur urutan section di halaman career — perubahan langsung tampil di frontend</p>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;align-items:start;">

    <!-- SORTABLE COLUMN -->
    <div>
        <div style="font-family:'Syne',sans-serif;font-size:.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--t3);margin-bottom:14px;display:flex;align-items:center;gap:8px;">
            <i class="fas fa-grip-lines"></i> Urutan Section (drag untuk ubah)
        </div>

        <!-- Fixed top: Hero -->
        <div class="sort-fixed-item" style="margin-bottom:6px;">
            <i class="fas fa-lock" style="font-size:9px;color:var(--t4);flex-shrink:0;"></i>
            <div class="sort-ico"><i class="fas fa-image"></i></div>
            <div class="sort-info"><strong>Hero</strong><span>Fixed di paling atas</span></div>
            <span class="sort-fixed-badge">Fixed</span>
        </div>

        <div class="sort-fixed-item" style="margin-bottom:10px;">
            <i class="fas fa-lock" style="font-size:9px;color:var(--t4);flex-shrink:0;"></i>
            <div class="sort-ico"><i class="fas fa-bars"></i></div>
            <div class="sort-info"><strong>Tab Navigation</strong><span>Fixed di bawah hero</span></div>
            <span class="sort-fixed-badge">Fixed</span>
        </div>

        <form method="POST" id="orderForm">
            <div class="sortable-list" id="sortableList">
                <?php
                // Merge: tampilkan section sesuai urutan tersimpan, pastikan semua ada
                $allKeys = array_keys($sectionDefs);
                $ordered = array_unique(array_merge($currentOrder, $allKeys));
                $ordered = array_filter($ordered, fn($k) => isset($sectionDefs[$k]));
                foreach ($ordered as $key):
                    [$ico, $label, $desc] = $sectionDefs[$key];
                ?>
                <div class="sort-item" data-key="<?= $key ?>">
                    <i class="fas fa-grip-vertical sort-handle"></i>
                    <div class="sort-ico"><i class="<?= $ico ?>"></i></div>
                    <div class="sort-info">
                        <strong><?= $label ?></strong>
                        <span><?= $desc ?></span>
                    </div>
                    <input type="hidden" name="section_order[]" value="<?= $key ?>" class="sort-input">
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Fixed bottom: Footer -->
            <div class="sort-fixed-item" style="margin-top:6px;">
                <i class="fas fa-lock" style="font-size:9px;color:var(--t4);flex-shrink:0;"></i>
                <div class="sort-ico"><i class="fas fa-shoe-prints"></i></div>
                <div class="sort-info"><strong>Footer</strong><span>Fixed di paling bawah</span></div>
                <span class="sort-fixed-badge">Fixed</span>
            </div>

            <div style="margin-top:20px;display:flex;gap:10px;align-items:center;">
                <button type="submit" name="save_section_order" class="btn-p">
                    <i class="fas fa-floppy-disk"></i>Simpan Urutan
                </button>
                <button type="button" class="btn-g" onclick="resetOrder()">
                    <i class="fas fa-rotate-left"></i>Reset Default
                </button>
            </div>
        </form>
    </div>

    <!-- PREVIEW COLUMN -->
    <div>
        <div style="font-family:'Syne',sans-serif;font-size:.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--t3);margin-bottom:14px;display:flex;align-items:center;gap:8px;">
            <i class="fas fa-eye"></i> Preview Urutan
        </div>
        <div style="background:rgba(255,255,255,.55);border:1px solid rgba(255,255,255,.85);border-radius:var(--r3);padding:16px;box-shadow:0 4px 20px rgba(0,0,0,.06);">
            <div style="display:flex;flex-direction:column;gap:4px;" id="orderPreview">
                <!-- Diisi JS -->
            </div>
            <div style="margin-top:12px;padding-top:12px;border-top:1px solid var(--b1);">
                <a href="/lending_word/career.php" target="_blank" class="btn-g" style="width:100%;justify-content:center;font-size:.72rem;">
                    <i class="fas fa-up-right-from-square"></i>Buka Career Page
                </a>
            </div>
        </div>

        <div style="margin-top:20px;padding:16px;background:rgba(0,132,227,.05);border:1px solid rgba(0,132,227,.15);border-radius:var(--r2);">
            <div style="font-family:'Syne',sans-serif;font-size:.62rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--blue);margin-bottom:8px;"><i class="fas fa-circle-info"></i> Info</div>
            <p style="font-size:.77rem;color:var(--t2);line-height:1.65;">
                Urutan ini mengatur tampilan di halaman <strong>career.php</strong>. Section <strong>Hero</strong>, <strong>Tab Navigation</strong>, dan <strong>Footer</strong> selalu berada di posisi tetap dan tidak bisa dipindah.
            </p>
        </div>
    </div>

</div>
</div>

</main>
</div>

<!-- ══ DETAIL LAMARAN MODAL ══════════════════════════════════════════════════ -->
<div class="app-modal-overlay" id="appDetailModal" onclick="if(event.target===this)closeAppDetail()">
    <div class="app-modal">
        <div class="app-modal-hd">
            <div><h3 id="appDetailName">—</h3><p id="appDetailPosition">—</p></div>
            <button class="app-modal-close" onclick="closeAppDetail()"><i class="fas fa-times"></i></button>
        </div>
        <div class="app-modal-body">
            <div class="app-row">
                <div class="app-field"><label>Email</label><a id="appDetailEmail" href="#">—</a></div>
                <div class="app-field"><label>Telepon</label><span id="appDetailPhone">—</span></div>
            </div>
            <div class="app-row">
                <div class="app-field"><label>LinkedIn</label><a id="appDetailLinkedin" href="#" target="_blank">—</a></div>
                <div class="app-field"><label>Portfolio / CV Link</label><a id="appDetailPortfolio" href="#" target="_blank">—</a></div>
            </div>
            <div class="app-row">
                <div class="app-field"><label>Status</label><span id="appDetailStatus">—</span></div>
                <div class="app-field"><label>Tanggal Melamar</label><span id="appDetailDate">—</span></div>
            </div>
            <div class="app-row">
                <div class="app-field"><label>Sumber</label><span id="appDetailSource">—</span></div>
                <div class="app-field"><label>IP Address</label><span id="appDetailIp">—</span></div>
            </div>
            <div class="app-divider"></div>
            <div class="app-field" style="margin-bottom:0;">
                <label>Cover Letter</label>
                <div class="app-cover" id="appDetailCover">—</div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.2/Sortable.min.js"></script>
<script>
/* ── SECTION ORDER DRAG & DROP ── */
const sectionLabels = <?= json_encode(array_map(fn($v) => $v[1], $sectionDefs)) ?>;
const sectionIcons  = <?= json_encode(array_map(fn($v) => $v[0], $sectionDefs)) ?>;

const sortableList = document.getElementById('sortableList');
const orderPreview = document.getElementById('orderPreview');

if (sortableList) {
    Sortable.create(sortableList, {
        animation: 180,
        ghostClass: 'sortable-ghost',
        chosenClass: 'sortable-chosen',
        dragClass: 'sortable-drag',
        handle: '.sort-handle',
        onEnd: updatePreview,
    });
    updatePreview();
}

function updatePreview() {
    if (!orderPreview) return;
    const items = sortableList.querySelectorAll('.sort-item');
    let html = '';
    // Fixed top
    html += previewRow('fas fa-image', 'Hero', true, 1);
    html += previewRow('fas fa-bars', 'Tab Navigation', true, 2);
    let n = 3;
    items.forEach(item => {
        const key   = item.dataset.key;
        const label = sectionLabels[key] || key;
        const icon  = sectionIcons[key]  || 'fas fa-layer-group';
        html += previewRow(icon, label, false, n++);
        // sync hidden inputs order
        item.querySelector('.sort-input').value = key;
    });
    html += previewRow('fas fa-shoe-prints', 'Footer', true, n);
    orderPreview.innerHTML = html;
}

function previewRow(icon, label, fixed, n) {
    return `<div style="display:flex;align-items:center;gap:10px;padding:8px 10px;border-radius:var(--r1);background:${fixed?'rgba(0,0,0,.03)':'rgba(255,255,255,.65)'};border:1px solid ${fixed?'var(--b1)':'var(--b2)'};opacity:${fixed?'.55':'1'};">
        <span style="font-family:'Syne',sans-serif;font-size:.6rem;font-weight:700;color:var(--t4);width:16px;text-align:center;">${n}</span>
        <i class="${icon}" style="font-size:10px;width:12px;color:var(--t3);"></i>
        <span style="font-size:.78rem;font-family:'Syne',sans-serif;font-weight:600;color:${fixed?'var(--t4)':'var(--t1)'};">${label}</span>
        ${fixed?'<span style="margin-left:auto;font-size:.58rem;color:var(--t4);">fixed</span>':''}
    </div>`;
}

const defaultOrder = ['vacancies','categories','entry','dream','subsidiaries','social','note'];
function resetOrder() {
    if (!confirm('Reset ke urutan default?')) return;
    defaultOrder.forEach(key => {
        const item = sortableList.querySelector(`[data-key="${key}"]`);
        if (item) sortableList.appendChild(item);
    });
    updatePreview();
}

/* ── APP DETAIL MODAL ── */
function openAppDetail(a) {
    document.getElementById('appDetailName').textContent     = a.full_name     || '—';
    document.getElementById('appDetailPosition').textContent = a.job_title     || 'Posisi tidak diketahui';
    document.getElementById('appDetailPhone').textContent    = a.phone         || '—';
    document.getElementById('appDetailStatus').textContent   = a.status        || '—';
    document.getElementById('appDetailSource').textContent   = a.source        || '—';
    document.getElementById('appDetailIp').textContent       = a.ip_address    || '—';
    document.getElementById('appDetailCover').textContent    = a.cover_letter  || 'Tidak ada cover letter.';
    document.getElementById('appDetailDate').textContent = a.created_at
        ? new Date(a.created_at).toLocaleDateString('id-ID', {day:'2-digit',month:'long',year:'numeric'})
        : '—';
    const emailEl = document.getElementById('appDetailEmail');
    if (a.email) { emailEl.href = 'mailto:' + a.email; emailEl.textContent = a.email; }
    else { emailEl.removeAttribute('href'); emailEl.textContent = '—'; }
    const liEl = document.getElementById('appDetailLinkedin');
    if (a.linkedin_url) { liEl.href = a.linkedin_url; liEl.textContent = 'Buka LinkedIn'; }
    else { liEl.removeAttribute('href'); liEl.textContent = '—'; }
    const pfEl = document.getElementById('appDetailPortfolio');
    if (a.portfolio_url) { pfEl.href = a.portfolio_url; pfEl.textContent = 'Buka Link'; }
    else { pfEl.removeAttribute('href'); pfEl.textContent = '—'; }
    document.getElementById('appDetailModal').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeAppDetail() {
    document.getElementById('appDetailModal').classList.remove('open');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeAppDetail(); });
</script>
</body>
</html>