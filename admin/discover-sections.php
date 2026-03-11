<?php
/**
 * Admin: Edit Discover Feature Sections
 * Path: /lending_word/admin/discover-sections.php?id=X
 */

require_once __DIR__ . '/../app/database.php';

$pdo = Database::getInstance()->getConnection();
$id  = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: index.php?tab=discover'); exit; }

$stmt = $pdo->prepare("SELECT * FROM discover_features WHERE id = ?");
$stmt->execute([$id]);
$feature = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$feature) { header('Location: index.php?tab=discover'); exit; }

$success = '';
$error   = '';

function safeJson($val, $default = []) {
    if (empty($val)) return $default;
    $d = json_decode($val, true);
    return is_array($d) ? $d : $default;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_sections'])) {
        $raw = $_POST['sections_json'] ?? '[]';
        $decoded = json_decode($raw, true);
        $clean = (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) ? $raw : '[]';
        try {
            $upd = $pdo->prepare("UPDATE discover_features SET sections = :s::jsonb WHERE id = :id");
            $upd->execute(['s' => $clean, 'id' => $id]);
        } catch (Exception $e) {
            $upd = $pdo->prepare("UPDATE discover_features SET sections = :s WHERE id = :id");
            $upd->execute(['s' => $clean, 'id' => $id]);
        }
        header("Location: discover-sections.php?id=$id&saved=1");
        exit;
    }
}

if (isset($_GET['saved'])) {
    $stmt->execute([$id]);
    $feature = $stmt->fetch(PDO::FETCH_ASSOC);
    $success = 'Sections berhasil disimpan!';
}

$sections = safeJson($feature['sections'] ?? null);
$sectionsJson = json_encode($sections, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Sections — <?= htmlspecialchars($feature['title']) ?> — Admin</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
/*
 * ============================================================
 * DISCOVER SECTIONS ADMIN — LIGHT GLASSMORPHISM THEME PATCH
 * Ganti seluruh blok <style> di discover-sections.php dengan CSS ini
 * ============================================================
 */

:root{
    --bg:  #dcdce8;
    --bg2: rgba(255,255,255,0.60);
    --bg3: rgba(255,255,255,0.42);
    --bg4: rgba(255,255,255,0.28);
    --bg5: rgba(255,255,255,0.80);
    --b1: rgba(0,0,0,0.04);
    --b2: rgba(0,0,0,0.09);
    --b3: rgba(0,0,0,0.16);
    --b4: rgba(0,0,0,0.28);
    --t1: #12121f;
    --t2: #4b4b6a;
    --t3: #9090b0;
    --t4: #b8b8d0;
    --gold:  #18181e;
    --gold2: #3a3a4a;
    --gold3: rgba(0,0,0,0.06);
    --green: #00b894;
    --red:   #e17055;
    --blue:  #0984e3;
    --r1: 8px; --r2: 12px; --r3: 16px; --r4: 100px;
    --topbar-h: 60px;
    --savebar-h: 64px;
}

*,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
html,body{height:100%;}

body{
    font-family:'DM Sans',sans-serif;
    background:
        radial-gradient(ellipse at 15% 20%, rgba(200,200,230,0.55) 0%, transparent 55%),
        radial-gradient(ellipse at 85% 75%, rgba(210,205,235,0.50) 0%, transparent 55%),
        #d8d8e6;
    color:var(--t1);
    min-height:100vh;font-size:14px;line-height:1.6;
    -webkit-font-smoothing:antialiased;overflow:hidden;
}

::-webkit-scrollbar{width:4px}
::-webkit-scrollbar-track{background:transparent}
::-webkit-scrollbar-thumb{background:var(--b3);border-radius:4px}

/* ── TOPBAR ── */
.topbar{
    position:fixed;top:0;left:0;right:0;z-index:300;
    height:var(--topbar-h);
    padding:0 32px;
    display:flex;align-items:center;justify-content:space-between;
    background:rgba(255,255,255,0.72);
    backdrop-filter:blur(24px) saturate(180%);
    border-bottom:1px solid var(--b2);
    flex-shrink:0;
    box-shadow:0 1px 0 rgba(255,255,255,0.9) inset, 0 2px 10px rgba(0,0,0,0.05);
}
.brand{display:flex;align-items:center;gap:10px;text-decoration:none;color:var(--t1);}
.brand i{color:var(--t1);font-size:13px;}
.brand-name{font-family:'Syne',sans-serif;font-size:.85rem;font-weight:700;letter-spacing:.08em;color:var(--t1);}
.breadcrumb{display:flex;align-items:center;gap:6px;font-size:.75rem;color:var(--t3);}
.breadcrumb a{color:var(--t2);text-decoration:none;transition:color .15s;}
.breadcrumb a:hover{color:var(--t1);}
.breadcrumb i{font-size:9px;}
.topbar-r{display:flex;align-items:center;gap:8px;}
.tpill{
    display:flex;align-items:center;gap:6px;
    padding:6px 14px;border-radius:var(--r4);
    font-size:.72rem;font-weight:500;
    border:1px solid var(--b2);color:var(--t2);
    text-decoration:none;transition:all .18s;
    background:rgba(255,255,255,0.55);
    backdrop-filter:blur(6px);
}
.tpill:hover{border-color:var(--b3);color:var(--t1);background:rgba(255,255,255,0.85);}
.tpill.primary{
    background:linear-gradient(135deg,#1a1a24,#2e2e3c);
    color:#fff;border-color:transparent;font-weight:700;
    box-shadow:0 3px 12px rgba(0,0,0,0.18);
}
.tpill.primary:hover{box-shadow:0 5px 20px rgba(0,0,0,0.26);}

/* ── LAYOUT ── */
.layout-wrap{
    position:fixed;
    top:var(--topbar-h);
    bottom:var(--savebar-h);
    left:0;right:0;
    display:grid;
    grid-template-columns:300px 1fr;
    overflow:hidden;
}

/* ── SIDEBAR ── */
.side{
    background:rgba(255,255,255,0.52);
    backdrop-filter:blur(18px);
    border-right:1px solid var(--b2);
    height:100%;
    overflow-y:auto;
    padding:20px;
    display:flex;
    flex-direction:column;
    gap:14px;
}

/* ── MAIN ── */
.main{
    height:100%;
    overflow-y:auto;
    padding:28px 36px 40px;
    min-width:0;
}

/* ── SIDEBAR CARDS ── */
.side-card{
    background:rgba(255,255,255,0.62);
    backdrop-filter:blur(12px);
    border:1px solid rgba(255,255,255,0.85);
    border-radius:var(--r2);overflow:hidden;flex-shrink:0;
    box-shadow:0 2px 0 rgba(255,255,255,0.9) inset, 0 4px 16px rgba(0,0,0,0.06);
}
.side-card-hd{
    padding:10px 13px;
    background:rgba(255,255,255,0.45);
    border-bottom:1px solid var(--b1);
    font-family:'Syne',sans-serif;font-size:.62rem;font-weight:700;
    letter-spacing:.1em;text-transform:uppercase;color:var(--t2);
    display:flex;align-items:center;gap:7px;
}
.side-card-hd i{color:var(--t1);}
.side-card-body{padding:13px;}
.feat-thumb{width:100%;aspect-ratio:16/9;object-fit:cover;border-radius:var(--r1);border:1px solid var(--b2);display:block;background:rgba(0,0,0,0.05);margin-bottom:10px;}
.feat-title{font-family:'Syne',sans-serif;font-size:.9rem;font-weight:700;margin-bottom:3px;color:var(--t1);}
.feat-cat{font-size:.72rem;color:var(--t3);}

.type-btn{
    display:flex;align-items:center;gap:8px;width:100%;
    padding:8px 10px;
    background:rgba(255,255,255,0.55);
    border:1px solid var(--b2);border-radius:var(--r1);
    color:var(--t2);font-size:.76rem;
    font-family:'DM Sans',sans-serif;cursor:pointer;
    transition:all .15s;text-align:left;margin-bottom:5px;
    backdrop-filter:blur(4px);
}
.type-btn:last-child{margin-bottom:0;}
.type-btn:hover{border-color:var(--b3);color:var(--t1);background:rgba(255,255,255,0.85);}
.type-btn i{font-size:10px;width:14px;color:var(--t1);}
.type-btn .type-desc{font-size:.67rem;color:var(--t4);display:block;margin-top:1px;}

/* ── PAGE HEADING ── */
.pg-hd{margin-bottom:22px;}
.pg-hd h1{font-family:'Syne',sans-serif;font-size:1.4rem;font-weight:700;letter-spacing:-.02em;color:var(--t1);}
.pg-hd p{font-size:.76rem;color:var(--t3);margin-top:4px;}

/* ── TOAST ── */
.toast{
    display:flex;align-items:center;gap:10px;
    padding:11px 15px;
    background:rgba(0,184,148,0.08);
    border:1px solid rgba(0,184,148,0.22);
    border-radius:var(--r2);color:var(--green);
    font-size:.8rem;margin-bottom:18px;
    backdrop-filter:blur(8px);
}
.t-dot{width:6px;height:6px;border-radius:50%;background:var(--green);flex-shrink:0;}

/* ── SECTION LIST ── */
.section-list{display:flex;flex-direction:column;gap:10px;}
.sec-card{
    background:rgba(255,255,255,0.62);
    backdrop-filter:blur(14px);
    border:1px solid rgba(255,255,255,0.85);
    border-radius:var(--r3);overflow:hidden;
    transition:border-color .2s,box-shadow .2s;
    box-shadow:0 2px 0 rgba(255,255,255,0.9) inset, 0 4px 20px rgba(0,0,0,0.06);
}
.sec-card:hover{border-color:rgba(255,255,255,1);box-shadow:0 2px 0 rgba(255,255,255,0.9) inset, 0 8px 28px rgba(0,0,0,0.09);}
.sec-card.dragging{opacity:.5;border-color:var(--b3);box-shadow:0 0 0 2px rgba(0,0,0,0.10);}
.sec-card.drag-over{border-color:var(--b4);box-shadow:0 4px 30px rgba(0,0,0,0.12);}

.sec-hd{
    display:flex;align-items:center;gap:10px;
    padding:12px 16px;
    background:rgba(255,255,255,0.45);
    border-bottom:1px solid var(--b1);
    cursor:grab;user-select:none;
}
.sec-hd:active{cursor:grabbing;}
.sec-drag{color:var(--t4);font-size:11px;flex-shrink:0;}
.sec-order{
    width:22px;height:22px;
    background:rgba(255,255,255,0.60);border:1px solid var(--b2);
    border-radius:var(--r4);
    display:flex;align-items:center;justify-content:center;
    font-family:'Syne',sans-serif;font-size:.6rem;font-weight:700;
    color:var(--t2);flex-shrink:0;
}
.sec-type-badge{
    display:inline-flex;align-items:center;gap:5px;
    padding:3px 8px;border-radius:var(--r4);
    font-size:.62rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;
    flex-shrink:0;
}
/* Badge colors adjusted for light bg */
.badge-intro     {background:rgba(9,132,227,0.10); border:1px solid rgba(9,132,227,0.22); color:#0868b8;}
.badge-stat-grid {background:rgba(0,184,148,0.10); border:1px solid rgba(0,184,148,0.22); color:#007a60;}
.badge-quote     {background:rgba(0,0,0,0.07);     border:1px solid rgba(0,0,0,0.14);     color:#333;}
.badge-image-full{background:rgba(130,60,190,0.08);border:1px solid rgba(130,60,190,0.18);color:#6b30a0;}
.badge-two-col   {background:rgba(200,140,0,0.10); border:1px solid rgba(200,140,0,0.20); color:#8a5c00;}

.sec-preview-text{flex:1;font-size:.76rem;color:var(--t2);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;min-width:0;}
.sec-actions{display:flex;align-items:center;gap:4px;flex-shrink:0;margin-left:auto;}
.sa-btn{
    display:inline-flex;align-items:center;gap:4px;
    padding:5px 9px;border-radius:var(--r1);
    font-size:.67rem;font-weight:600;border:1px solid transparent;
    cursor:pointer;font-family:'DM Sans',sans-serif;transition:all .12s;background:transparent;
}
.sa-toggle{border-color:var(--b2);color:var(--t2);}
.sa-toggle:hover{border-color:var(--b3);color:var(--t1);background:rgba(255,255,255,0.70);}
.sa-del{border-color:rgba(225,112,85,0.22);color:var(--red);background:rgba(225,112,85,0.06);}
.sa-del:hover{background:rgba(225,112,85,0.16);border-color:var(--red);}
.sa-move{border-color:var(--b2);color:var(--t3);padding:5px 7px;}
.sa-move:hover{color:var(--t2);border-color:var(--b3);background:rgba(255,255,255,0.70);}

.sec-body{padding:18px;}
.sec-body.collapsed{display:none;}

/* ── FORM GRID ── */
.fg{display:grid;gap:12px;margin-bottom:12px;}
.fg-2{grid-template-columns:1fr 1fr;}
.fg-3{grid-template-columns:1fr 1fr 1fr;}
.fg-4{grid-template-columns:1fr 1fr 1fr 1fr;}
.fg-5{grid-template-columns:1fr 1fr 1fr 1fr 1fr;}
.f{display:flex;flex-direction:column;gap:4px;}
.f label{font-family:'Syne',sans-serif;font-size:.58rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--t3);}
.f input,.f select,.f textarea{
    padding:7px 10px;
    background:rgba(255,255,255,0.55);
    border:1px solid var(--b2);border-radius:var(--r1);
    color:var(--t1);font-size:.82rem;font-family:'DM Sans',sans-serif;
    outline:none;transition:border-color .14s, background .14s;width:100%;
    backdrop-filter:blur(4px);
}
.f input:focus,.f select:focus,.f textarea:focus{
    border-color:var(--b4);background:rgba(255,255,255,0.88);
}
.f input::placeholder,.f textarea::placeholder{color:var(--t4);}
.f textarea{min-height:75px;resize:vertical;}
.f select option{background:#fff;color:var(--t1);}
.f small{font-size:.67rem;color:var(--t4);}

.fdiv{height:1px;background:var(--b2);margin:14px 0;}
.sub-label{
    font-family:'Syne',sans-serif;font-size:.6rem;font-weight:700;
    letter-spacing:.1em;text-transform:uppercase;
    color:var(--t3);margin-bottom:10px;
    display:flex;align-items:center;gap:8px;
}
.sub-label::after{content:'';flex:1;height:1px;background:var(--b2);}

/* ── STAT ITEMS ── */
.stat-items{display:flex;flex-direction:column;gap:8px;}
.stat-item{
    display:grid;grid-template-columns:1fr 1fr 2fr auto;
    gap:8px;align-items:center;
    background:rgba(255,255,255,0.50);
    border:1px solid var(--b2);border-radius:var(--r1);
    padding:10px 12px;backdrop-filter:blur(4px);
}
.stat-item input{
    padding:6px 8px;
    background:rgba(255,255,255,0.65);
    border:1px solid var(--b2);border-radius:6px;
    color:var(--t1);font-size:.8rem;font-family:'DM Sans',sans-serif;
    outline:none;width:100%;
    transition:border-color .14s, background .14s;
}
.stat-item input:focus{border-color:var(--b4);background:rgba(255,255,255,0.90);}
.stat-item input::placeholder{color:var(--t4);}
.del-stat{
    background:none;border:none;color:var(--red);
    cursor:pointer;opacity:.5;font-size:11px;padding:4px;
    transition:opacity .15s;flex-shrink:0;
}
.del-stat:hover{opacity:1;}

.add-item-btn{
    display:inline-flex;align-items:center;gap:6px;
    padding:6px 14px;
    background:transparent;
    border:1px dashed var(--b3);border-radius:var(--r1);
    color:var(--t3);font-size:.73rem;font-family:'DM Sans',sans-serif;
    cursor:pointer;margin-top:6px;transition:all .15s;
}
.add-item-btn:hover{border-color:var(--b4);color:var(--t1);background:rgba(255,255,255,0.60);}

.img-prev{
    display:block;width:100%;max-height:100px;object-fit:cover;
    border-radius:var(--r1);border:1px solid var(--b2);
    margin-top:6px;opacity:.85;
}
.img-prev[src=""]{display:none;}

/* ── SAVE BAR ── */
.save-bar{
    position:fixed;bottom:0;left:0;right:0;z-index:200;
    height:var(--savebar-h);
    padding:0 36px;
    background:rgba(255,255,255,0.75);
    backdrop-filter:blur(20px);
    border-top:1px solid var(--b2);
    display:flex;align-items:center;justify-content:space-between;gap:12px;
    box-shadow:0 -2px 16px rgba(0,0,0,0.06);
}
.save-info{font-size:.76rem;color:var(--t3);}
.save-info strong{color:var(--t1);}

.btn-p{
    display:inline-flex;align-items:center;gap:7px;
    padding:10px 22px;
    background:linear-gradient(135deg,#1a1a24,#2e2e3c);
    color:#fff;border:none;border-radius:var(--r2);
    font-size:.8rem;font-weight:700;font-family:'DM Sans',sans-serif;
    cursor:pointer;transition:all .18s;
    box-shadow:0 3px 16px rgba(0,0,0,0.18);
}
.btn-p:hover{box-shadow:0 5px 26px rgba(0,0,0,0.28);transform:translateY(-1px);}
.btn-p:active{transform:translateY(0);}

.btn-g{
    display:inline-flex;align-items:center;gap:7px;
    padding:10px 18px;
    background:rgba(255,255,255,0.60);
    color:var(--t2);border:1px solid var(--b2);border-radius:var(--r2);
    font-size:.8rem;font-family:'DM Sans',sans-serif;
    cursor:pointer;text-decoration:none;transition:all .15s;
    backdrop-filter:blur(4px);
}
.btn-g:hover{border-color:var(--b3);color:var(--t1);background:rgba(255,255,255,0.85);}

/* ── EMPTY ── */
.empty{text-align:center;padding:60px 20px;color:var(--t4);}
.empty i{font-size:1.8rem;margin-bottom:12px;opacity:.25;display:block;}
.empty p{font-size:.82rem;}

@media(max-width:900px){
    .layout-wrap{grid-template-columns:1fr;}
    .side{display:none;}
    .main{padding:18px 16px 40px;}
    .fg-3{grid-template-columns:1fr 1fr;}
    .fg-4{grid-template-columns:1fr 1fr;}
    .fg-5{grid-template-columns:1fr 1fr;}
}
</style>
</head>
<body>

<header class="topbar">
    <div style="display:flex;align-items:center;gap:16px;">
        <a class="brand" href="index.php?tab=discover"><i class="fas fa-shield-halved"></i><span class="brand-name">Porsche Admin</span></a>
        <nav class="breadcrumb">
            <i class="fas fa-chevron-right"></i>
            <a href="index.php?tab=discover">Discover</a>
            <i class="fas fa-chevron-right"></i>
            <span><?= htmlspecialchars($feature['title']) ?></span>
            <i class="fas fa-chevron-right"></i>
            <span style="color:var(--t1)">Sections</span>
        </nav>
    </div>
    <div class="topbar-r">
        <?php
        $prevUrl = '';
        if (!empty($feature['slug'])) $prevUrl = "/lending_word/discover-detail.php?slug={$feature['slug']}";
        elseif ($id) $prevUrl = "/lending_word/discover-detail.php?id={$id}";
        ?>
        <?php if ($prevUrl): ?>
        <a href="<?= $prevUrl ?>" target="_blank" class="tpill"><i class="fas fa-up-right-from-square"></i>Preview</a>
        <?php endif; ?>
        <a href="index.php?tab=discover" class="tpill"><i class="fas fa-arrow-left"></i>Back</a>
        <button class="tpill primary" onclick="saveAll()"><i class="fas fa-floppy-disk"></i>Save Sections</button>
    </div>
</header>

<!-- Full-height layout (topbar → layout → savebar) -->
<div class="layout-wrap">

    <!-- SIDEBAR — scrolls independently -->
    <aside class="side">
        <div class="side-card">
            <div class="side-card-hd"><i class="fas fa-sparkles"></i>Feature Info</div>
            <div class="side-card-body">
                <?php if ($feature['image']): ?>
                <img src="<?= htmlspecialchars($feature['image']) ?>" class="feat-thumb" onerror="this.style.display='none'">
                <?php endif; ?>
                <div class="feat-title"><?= htmlspecialchars($feature['title']) ?></div>
                <?php if ($feature['category']): ?>
                <div class="feat-cat"><?= htmlspecialchars($feature['category']) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="side-card">
            <div class="side-card-hd"><i class="fas fa-plus"></i>Tambah Section</div>
            <div class="side-card-body">
                <button class="type-btn" onclick="addSection('intro')">
                    <i class="fas fa-align-left"></i>
                    <span>Text + Image<span class="type-desc">Teks di kiri/kanan + gambar</span></span>
                </button>
                <button class="type-btn" onclick="addSection('stat-grid')">
                    <i class="fas fa-chart-bar"></i>
                    <span>Stat Grid<span class="type-desc">Grid angka/statistik</span></span>
                </button>
                <button class="type-btn" onclick="addSection('quote')">
                    <i class="fas fa-quote-left"></i>
                    <span>Quote<span class="type-desc">Kutipan teks besar</span></span>
                </button>
                <button class="type-btn" onclick="addSection('image-full')">
                    <i class="fas fa-image"></i>
                    <span>Image Full<span class="type-desc">Gambar lebar penuh</span></span>
                </button>
                <button class="type-btn" onclick="addSection('two-col')">
                    <i class="fas fa-columns"></i>
                    <span>Two Column<span class="type-desc">Dua kolom teks</span></span>
                </button>
            </div>
        </div>

        <div class="side-card">
            <div class="side-card-hd"><i class="fas fa-circle-info"></i>Tips</div>
            <div class="side-card-body" style="font-size:.74rem;color:var(--t3);line-height:1.7;">
                <p>• Drag header section untuk reorder</p>
                <p style="margin-top:5px;">• Klik <strong style="color:var(--t1)">▲▼</strong> untuk pindah section</p>
                <p style="margin-top:5px;">• Klik <strong style="color:var(--t1)">−</strong> untuk collapse/expand</p>
                <p style="margin-top:5px;">• Klik <strong style="color:var(--t1)">Save</strong> untuk menyimpan semua</p>
                <div style="margin-top:12px;padding-top:10px;border-top:1px solid var(--b1);">
                    <p style="color:var(--t4);margin-bottom:6px;">Card Style (Text+Image):</p>
                    <p>• <span style="color:#5b9cf6">Overlay</span> — gambar + judul overlay</p>
                    <p style="margin-top:3px;">• <span style="color:#2dd4a0">History</span> — gambar landscape + deskripsi bawah</p>
                    <p style="margin-top:3px;">• <span style="color:#f0b429">Split</span> — gambar kiri, teks kanan</p>
                </div>
            </div>
        </div>
    </aside>

    <!-- MAIN — scrolls independently -->
    <main class="main">
        <?php if ($success): ?>
        <div class="toast"><span class="t-dot"></span><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <div class="pg-hd">
            <h1>Edit Sections — <span style="color:var(--gold)"><?= htmlspecialchars($feature['title']) ?></span></h1>
            <p>Edit tiap section satu per satu. Drag untuk reorder. Klik Save setelah selesai.</p>
        </div>

        <form method="POST" id="saveForm">
            <input type="hidden" name="save_sections" value="1">
            <input type="hidden" name="sections_json" id="sectionsJsonInput">
        </form>

        <div class="section-list" id="sectionList"></div>

        <div class="empty" id="emptyState" style="display:none;">
            <i class="fas fa-layer-group"></i>
            <p>Belum ada sections. Klik tipe section di sidebar kiri untuk menambahkan.</p>
        </div>
    </main>

</div><!-- /layout-wrap -->

<!-- SAVE BAR — fixed bottom -->
<div class="save-bar">
    <div class="save-info">
        <strong id="sectionCountLabel">0 sections</strong> — perubahan belum tersimpan sampai klik Save
    </div>
    <div style="display:flex;gap:8px;align-items:center;">
        <a href="index.php?tab=discover" class="btn-g"><i class="fas fa-arrow-left"></i>Kembali</a>
        <button class="btn-p" onclick="saveAll()"><i class="fas fa-floppy-disk"></i>Simpan Semua Sections</button>
    </div>
</div>

<script>
let sections = <?= json_encode($sections, JSON_UNESCAPED_UNICODE) ?>;

const TYPE_META = {
    'intro':      { label:'Text + Image',  icon:'fas fa-align-left',   badgeClass:'badge-intro' },
    'stat-grid':  { label:'Stat Grid',     icon:'fas fa-chart-bar',    badgeClass:'badge-stat-grid' },
    'quote':      { label:'Quote',         icon:'fas fa-quote-left',   badgeClass:'badge-quote' },
    'image-full': { label:'Image Full',    icon:'fas fa-image',        badgeClass:'badge-image-full' },
    'two-col':    { label:'Two Column',    icon:'fas fa-columns',      badgeClass:'badge-two-col' },
};

function addSection(type) {
    const defaults = {
        'intro':      { type:'intro', tag:'', title:'', body:'', image:'', image_position:'right', card_style:'overlay', caption:'', tab_id:'', tab_label:'' },
        'stat-grid':  { type:'stat-grid',  title:'', items:[{val:'',lbl:'',desc:''}], tab_id:'', tab_label:'' },
        'quote':      { type:'quote',      text:'', author:'', tab_id:'', tab_label:'' },
        'image-full': { type:'image-full', image:'', caption:'', tab_id:'', tab_label:'' },
        'two-col':    { type:'two-col',    left_title:'', left_body:'', right_title:'', right_body:'', tab_id:'', tab_label:'' },
    };
    sections.push(Object.assign({}, defaults[type]));
    renderAll();
    setTimeout(() => {
        const cards = document.querySelectorAll('.sec-card');
        if (cards.length) cards[cards.length - 1].scrollIntoView({ behavior:'smooth', block:'center' });
    }, 100);
}

function removeSection(idx) {
    if (!confirm('Hapus section ini?')) return;
    sections.splice(idx, 1);
    renderAll();
}

function moveSection(idx, dir) {
    const newIdx = idx + dir;
    if (newIdx < 0 || newIdx >= sections.length) return;
    [sections[idx], sections[newIdx]] = [sections[newIdx], sections[idx]];
    renderAll();
}

function collectSections() {
    const cards = document.querySelectorAll('.sec-card');
    const result = [];
    cards.forEach((card) => {
        const type = card.dataset.type;
        const sec = { type };
        const g = (name) => {
            const el = card.querySelector(`[data-field="${name}"]`);
            return el ? el.value : '';
        };
        if (type === 'intro') {
            sec.tag            = g('tag');
            sec.title          = g('title');
            sec.body           = g('body');
            sec.image          = g('image');
            sec.image_position = g('image_position');
            sec.card_style     = g('card_style');
            sec.caption        = g('caption');
            sec.tab_id         = g('tab_id');
            sec.tab_label      = g('tab_label');
        } else if (type === 'stat-grid') {
            sec.title     = g('title');
            sec.tab_id    = g('tab_id');
            sec.tab_label = g('tab_label');
            sec.items = [];
            card.querySelectorAll('.stat-item').forEach(row => {
                sec.items.push({
                    val:  row.querySelector('[data-item="val"]').value,
                    lbl:  row.querySelector('[data-item="lbl"]').value,
                    desc: row.querySelector('[data-item="desc"]').value,
                });
            });
        } else if (type === 'quote') {
            sec.text      = g('text');
            sec.author    = g('author');
            sec.tab_id    = g('tab_id');
            sec.tab_label = g('tab_label');
        } else if (type === 'image-full') {
            sec.image     = g('image');
            sec.caption   = g('caption');
            sec.tab_id    = g('tab_id');
            sec.tab_label = g('tab_label');
        } else if (type === 'two-col') {
            sec.left_title  = g('left_title');
            sec.left_body   = g('left_body');
            sec.right_title = g('right_title');
            sec.right_body  = g('right_body');
            sec.tab_id      = g('tab_id');
            sec.tab_label   = g('tab_label');
        }
        result.push(sec);
    });
    return result;
}

function saveAll() {
    sections = collectSections();
    document.getElementById('sectionsJsonInput').value = JSON.stringify(sections);
    document.getElementById('saveForm').submit();
}

function previewText(sec) {
    if (sec.type === 'intro') {
        const style = sec.card_style ? ` [${sec.card_style}]` : '';
        return (sec.title || sec.body || '(belum ada judul)') + style;
    }
    if (sec.type === 'stat-grid')  return sec.title || `${(sec.items||[]).length} stat items`;
    if (sec.type === 'quote')      return sec.text ? `"${sec.text.substring(0,60)}..."` : '(belum ada kutipan)';
    if (sec.type === 'image-full') return sec.caption || sec.image || '(gambar full width)';
    if (sec.type === 'two-col')    return sec.left_title || sec.right_title || '(two column)';
    return '';
}

function renderFields(sec, idx) {
    const v = (k) => {
        const val = sec[k] ?? '';
        return String(val).replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    };

    if (sec.type === 'intro') {
        const cardStyleOpts = [
            { val:'overlay', label:'Overlay — gambar + judul overlay' },
            { val:'history', label:'History — landscape + deskripsi bawah' },
            { val:'split',   label:'Split — gambar kiri, teks kanan' },
        ];
        const csOptions = cardStyleOpts.map(o =>
            `<option value="${o.val}" ${(sec.card_style||'overlay')===o.val?'selected':''}>${o.label}</option>`
        ).join('');

        return `
        <div class="fg fg-2" style="margin-bottom:12px;">
            <div class="f">
                <label>Tab ID <small>(anchor link)</small></label>
                <input type="text" data-field="tab_id" value="${v('tab_id')}" placeholder="uniqueness">
            </div>
            <div class="f">
                <label>Tab Label <small>(nama di tab strip)</small></label>
                <input type="text" data-field="tab_label" value="${v('tab_label')}" placeholder="Uniqueness">
            </div>
        </div>
        <div class="fdiv"></div>
        <div class="fg fg-3">
            <div class="f">
                <label>Tag Kecil</label>
                <input type="text" data-field="tag" value="${v('tag')}" placeholder="e.g. Inovasi">
            </div>
            <div class="f" style="grid-column:span 2">
                <label>Judul Section</label>
                <input type="text" data-field="title" value="${v('title')}" placeholder="Judul section ini">
            </div>
        </div>
        <div class="fg" style="margin-bottom:12px;">
            <div class="f">
                <label>Isi / Paragraf <small>(muncul di card History, max ~200 karakter)</small></label>
                <textarea data-field="body" placeholder="Deskripsi atau paragraf panjang...">${sec.body ?? ''}</textarea>
            </div>
        </div>
        <div class="fdiv"></div>
        <div class="sub-label">Gambar & Tampilan</div>
        <div class="fg fg-3" style="margin-bottom:12px;">
            <div class="f" style="grid-column:span 2">
                <label>Image URL</label>
                <input type="text" data-field="image" value="${v('image')}" placeholder="https://..." oninput="updatePreview(this)">
                <img class="img-prev" src="${v('image')}" data-preview>
            </div>
            <div class="f">
                <label>Posisi Gambar</label>
                <select data-field="image_position">
                    <option value="right" ${(sec.image_position||'right')==='right'?'selected':''}>Kanan</option>
                    <option value="left"  ${sec.image_position==='left'?'selected':''}>Kiri</option>
                    <option value="full"  ${sec.image_position==='full'?'selected':''}>Full width</option>
                </select>
            </div>
        </div>
        <div class="fg fg-2" style="margin-bottom:12px;">
            <div class="f">
                <label>Card Style <small>— tampilan di slider</small></label>
                <select data-field="card_style">
                    ${csOptions}
                </select>
                <small>
                    <strong style="color:#5b9cf6">Overlay</strong> = gambar gelap + judul &nbsp;|&nbsp;
                    <strong style="color:#2dd4a0">History</strong> = landscape 16/11 + deskripsi &nbsp;|&nbsp;
                    <strong style="color:#f0b429">Split</strong> = gambar + teks samping
                </small>
            </div>
            <div class="f">
                <label>Caption <small>(opsional)</small></label>
                <input type="text" data-field="caption" value="${v('caption')}" placeholder="Keterangan gambar...">
            </div>
        </div>`;
    }

    if (sec.type === 'stat-grid') {
        const items = Array.isArray(sec.items) ? sec.items : [];
        const itemsHtml = items.map((it) => `
        <div class="stat-item">
            <input data-item="val"  placeholder="408"    value="${String(it.val||'').replace(/"/g,'&quot;')}">
            <input data-item="lbl"  placeholder="kW"     value="${String(it.lbl||'').replace(/"/g,'&quot;')}">
            <input data-item="desc" placeholder="Deskripsi singkat..." value="${String(it.desc||'').replace(/"/g,'&quot;')}">
            <button type="button" class="del-stat" onclick="this.closest('.stat-item').remove()"><i class="fas fa-times"></i></button>
        </div>`).join('');
        return `
        <div class="fg fg-2" style="margin-bottom:12px;">
            <div class="f">
                <label>Tab ID</label>
                <input type="text" data-field="tab_id" value="${v('tab_id')}" placeholder="stats">
            </div>
            <div class="f">
                <label>Tab Label</label>
                <input type="text" data-field="tab_label" value="${v('tab_label')}" placeholder="Statistik">
            </div>
        </div>
        <div class="fdiv"></div>
        <div class="f" style="margin-bottom:14px;">
            <label>Judul Grid <small>(opsional)</small></label>
            <input type="text" data-field="title" value="${v('title')}" placeholder="Performa">
        </div>
        <div class="fdiv"></div>
        <div class="sub-label">Stat Items</div>
        <div style="display:grid;grid-template-columns:1fr 1fr 2fr auto;gap:8px;padding:0 0 6px;margin-bottom:4px;">
            <span style="font-size:.6rem;letter-spacing:.1em;text-transform:uppercase;color:var(--t4);">Nilai</span>
            <span style="font-size:.6rem;letter-spacing:.1em;text-transform:uppercase;color:var(--t4);">Satuan</span>
            <span style="font-size:.6rem;letter-spacing:.1em;text-transform:uppercase;color:var(--t4);">Deskripsi</span>
            <span></span>
        </div>
        <div class="stat-items">${itemsHtml}</div>
        <button type="button" class="add-item-btn" onclick="addStatItem(this)">
            <i class="fas fa-plus"></i>Tambah Item
        </button>`;
    }

    if (sec.type === 'quote') {
        return `
        <div class="fg fg-2" style="margin-bottom:12px;">
            <div class="f">
                <label>Tab ID</label>
                <input type="text" data-field="tab_id" value="${v('tab_id')}" placeholder="quote">
            </div>
            <div class="f">
                <label>Tab Label</label>
                <input type="text" data-field="tab_label" value="${v('tab_label')}" placeholder="Quote">
            </div>
        </div>
        <div class="fdiv"></div>
        <div class="f" style="margin-bottom:12px;">
            <label>Kutipan <span style="color:var(--gold)">*</span></label>
            <textarea data-field="text" style="min-height:100px;" placeholder="Tulis kutipan yang berkesan...">${sec.text ?? ''}</textarea>
        </div>
        <div class="f">
            <label>Atribusi / Penulis <small>(opsional)</small></label>
            <input type="text" data-field="author" value="${v('author')}" placeholder="Dr. Ferry Porsche, 1948">
        </div>`;
    }

    if (sec.type === 'image-full') {
        return `
        <div class="fg fg-2" style="margin-bottom:12px;">
            <div class="f">
                <label>Tab ID</label>
                <input type="text" data-field="tab_id" value="${v('tab_id')}" placeholder="gallery">
            </div>
            <div class="f">
                <label>Tab Label</label>
                <input type="text" data-field="tab_label" value="${v('tab_label')}" placeholder="Gallery">
            </div>
        </div>
        <div class="fdiv"></div>
        <div class="f" style="margin-bottom:12px;">
            <label>Image URL <span style="color:var(--gold)">*</span></label>
            <input type="text" data-field="image" value="${v('image')}" placeholder="https://..." oninput="updatePreview(this)">
            <img class="img-prev" src="${v('image')}" data-preview>
        </div>
        <div class="f">
            <label>Caption <small>(opsional)</small></label>
            <input type="text" data-field="caption" value="${v('caption')}" placeholder="Keterangan gambar...">
        </div>`;
    }

    if (sec.type === 'two-col') {
        return `
        <div class="fg fg-2" style="margin-bottom:12px;">
            <div class="f">
                <label>Tab ID</label>
                <input type="text" data-field="tab_id" value="${v('tab_id')}" placeholder="detail">
            </div>
            <div class="f">
                <label>Tab Label</label>
                <input type="text" data-field="tab_label" value="${v('tab_label')}" placeholder="Detail">
            </div>
        </div>
        <div class="fdiv"></div>
        <div class="sub-label">Kolom Kiri</div>
        <div class="fg fg-2" style="margin-bottom:14px;">
            <div class="f">
                <label>Judul Kiri</label>
                <input type="text" data-field="left_title" value="${v('left_title')}" placeholder="Judul kolom kiri">
            </div>
            <div class="f">
                <label>Isi Kiri</label>
                <textarea data-field="left_body" placeholder="Paragraf kolom kiri...">${sec.left_body ?? ''}</textarea>
            </div>
        </div>
        <div class="fdiv"></div>
        <div class="sub-label">Kolom Kanan</div>
        <div class="fg fg-2">
            <div class="f">
                <label>Judul Kanan</label>
                <input type="text" data-field="right_title" value="${v('right_title')}" placeholder="Judul kolom kanan">
            </div>
            <div class="f">
                <label>Isi Kanan</label>
                <textarea data-field="right_body" placeholder="Paragraf kolom kanan...">${sec.right_body ?? ''}</textarea>
            </div>
        </div>`;
    }

    return '<p style="color:var(--t4);font-size:.8rem;">Unknown section type.</p>';
}

function renderCard(sec, idx) {
    const meta = TYPE_META[sec.type] || { label: sec.type, icon:'fas fa-layer-group', badgeClass:'badge-intro' };
    const preview = previewText(sec);
    return `
    <div class="sec-card" data-idx="${idx}" data-type="${sec.type}" draggable="true">
        <div class="sec-hd">
            <span class="sec-drag"><i class="fas fa-grip-vertical"></i></span>
            <span class="sec-order">${idx + 1}</span>
            <span class="sec-type-badge ${meta.badgeClass}"><i class="${meta.icon}"></i> ${meta.label}</span>
            <span class="sec-preview-text">${escHtml(preview)}</span>
            <div class="sec-actions">
                <button type="button" class="sa-btn sa-move" title="Naik" onclick="moveSection(${idx},-1)"><i class="fas fa-chevron-up"></i></button>
                <button type="button" class="sa-btn sa-move" title="Turun" onclick="moveSection(${idx},1)"><i class="fas fa-chevron-down"></i></button>
                <button type="button" class="sa-btn sa-toggle" onclick="toggleSection(this)"><i class="fas fa-minus"></i></button>
                <button type="button" class="sa-btn sa-del" onclick="removeSection(${idx})"><i class="fas fa-trash-alt"></i></button>
            </div>
        </div>
        <div class="sec-body">
            ${renderFields(sec, idx)}
        </div>
    </div>`;
}

function escHtml(s) {
    return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

function renderAll() {
    const list  = document.getElementById('sectionList');
    const empty = document.getElementById('emptyState');
    const label = document.getElementById('sectionCountLabel');
    if (!sections.length) {
        list.innerHTML = '';
        empty.style.display = 'block';
        label.textContent = '0 sections';
        return;
    }
    empty.style.display = 'none';
    list.innerHTML = sections.map((s,i) => renderCard(s,i)).join('');
    label.textContent = `${sections.length} section${sections.length !== 1 ? 's' : ''}`;
    initDrag();
}

function toggleSection(btn) {
    const body = btn.closest('.sec-card').querySelector('.sec-body');
    const icon = btn.querySelector('i');
    body.classList.toggle('collapsed');
    icon.className = body.classList.contains('collapsed') ? 'fas fa-plus' : 'fas fa-minus';
}

function updatePreview(input) {
    const prev = input.parentElement.querySelector('[data-preview]');
    if (prev) { prev.src = input.value; prev.style.display = input.value ? '' : 'none'; }
}

function addStatItem(btn) {
    const list = btn.previousElementSibling;
    const item = document.createElement('div');
    item.className = 'stat-item';
    item.innerHTML = `
        <input data-item="val"  placeholder="408">
        <input data-item="lbl"  placeholder="kW">
        <input data-item="desc" placeholder="Deskripsi singkat...">
        <button type="button" class="del-stat" onclick="this.closest('.stat-item').remove()"><i class="fas fa-times"></i></button>`;
    list.appendChild(item);
}

function initDrag() {
    const cards = document.querySelectorAll('.sec-card');
    let dragSrc = null;
    cards.forEach(card => {
        card.addEventListener('dragstart', e => { dragSrc = card; card.classList.add('dragging'); e.dataTransfer.effectAllowed = 'move'; });
        card.addEventListener('dragend', () => { card.classList.remove('dragging'); document.querySelectorAll('.sec-card').forEach(c => c.classList.remove('drag-over')); });
        card.addEventListener('dragover', e => { e.preventDefault(); if (card !== dragSrc) { document.querySelectorAll('.sec-card').forEach(c => c.classList.remove('drag-over')); card.classList.add('drag-over'); } });
        card.addEventListener('drop', e => {
            e.preventDefault();
            if (!dragSrc || dragSrc === card) return;
            card.classList.remove('drag-over');
            sections = collectSections();
            const srcIdx  = parseInt(dragSrc.dataset.idx);
            const destIdx = parseInt(card.dataset.idx);
            const moved   = sections.splice(srcIdx, 1)[0];
            sections.splice(destIdx, 0, moved);
            renderAll();
        });
    });
}

renderAll();
</script>
</body>
</html>