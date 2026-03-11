<?php
/**
 * Admin: Edit Discover Gallery (Gallery-style sections)
 * Path: /lending_word/admin/discover-gallery.php?id=X
 */
require_once __DIR__ . '/../app/database.php';

$pdo = Database::getInstance()->getConnection();
$featureId = (int)($_GET['id'] ?? 0);
if (!$featureId) { header('Location: index.php?tab=discover'); exit; }

$stmt = $pdo->prepare("SELECT * FROM discover_features WHERE id = ?");
$stmt->execute([$featureId]);
$feature = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$feature) { header('Location: index.php?tab=discover'); exit; }

$success = '';
$error   = '';

// ── Handle POST ───────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // DELETE
    if (!empty($_POST['delete_id'])) {
        $del = $pdo->prepare("DELETE FROM discover_gallery WHERE id = ? AND feature_id = ?");
        $del->execute([(int)$_POST['delete_id'], $featureId]);
        header("Location: discover-gallery.php?id=$featureId&saved=1");
        exit;
    }

    // UPDATE existing
    if (!empty($_POST['update_id'])) {
        $uid = (int)$_POST['update_id'];
        $upd = $pdo->prepare("
            UPDATE discover_gallery SET
                eyebrow=:eyebrow, title=:title, body=:body,
                tab_id=:tab_id, tab_label=:tab_label,
                image_top=:image_top, image_right=:image_right,
                image_bottom=:image_bottom, sort_order=:sort_order
            WHERE id=:id AND feature_id=:fid
        ");
        $upd->execute([
            'id'          => $uid,
            'fid'         => $featureId,
            'eyebrow'     => trim($_POST['eyebrow']     ?? ''),
            'title'       => trim($_POST['title']       ?? ''),
            'body'        => trim($_POST['body']         ?? ''),
            'tab_id'      => trim($_POST['tab_id']      ?? ''),
            'tab_label'   => trim($_POST['tab_label']   ?? ''),
            'image_top'   => trim($_POST['image_top']   ?? ''),
            'image_right' => trim($_POST['image_right'] ?? ''),
            'image_bottom'=> trim($_POST['image_bottom']?? ''),
            'sort_order'  => (int)($_POST['sort_order'] ?? 0),
        ]);
        header("Location: discover-gallery.php?id=$featureId&saved=1");
        exit;
    }

    // CREATE new
    if (!empty($_POST['create_new'])) {
        $ins = $pdo->prepare("
            INSERT INTO discover_gallery
                (feature_id, eyebrow, title, body, tab_id, tab_label,
                 image_top, image_right, image_bottom, sort_order)
            VALUES (?,?,?,?,?,?,?,?,?,?)
        ");
        $ins->execute([
            $featureId,
            trim($_POST['eyebrow']     ?? ''),
            trim($_POST['title']       ?? ''),
            trim($_POST['body']         ?? ''),
            trim($_POST['tab_id']      ?? ''),
            trim($_POST['tab_label']   ?? ''),
            trim($_POST['image_top']   ?? ''),
            trim($_POST['image_right'] ?? ''),
            trim($_POST['image_bottom']?? ''),
            (int)($_POST['sort_order'] ?? 0),
        ]);
        header("Location: discover-gallery.php?id=$featureId&saved=1");
        exit;
    }
}

if (isset($_GET['saved'])) $success = 'Perubahan berhasil disimpan!';

// Fetch all gallery rows for this feature
$rows = $pdo->prepare("SELECT * FROM discover_gallery WHERE feature_id = ? ORDER BY sort_order ASC, id ASC");
$rows->execute([$featureId]);
$items = $rows->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Gallery Sections — <?= htmlspecialchars($feature['title']) ?> — Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
/*
 * ============================================================
 * DISCOVER GALLERY ADMIN — LIGHT GLASSMORPHISM THEME PATCH
 * Ganti seluruh blok <style> di discover-gallery.php dengan CSS ini
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
    --r1: 8px; --r2: 12px; --r3: 16px; --r4: 100px;
}

*,*::before,*::after{margin:0;padding:0;box-sizing:border-box}

body{
    font-family:'DM Sans',sans-serif;
    background:
        radial-gradient(ellipse at 15% 20%, rgba(200,200,230,0.55) 0%, transparent 55%),
        radial-gradient(ellipse at 85% 75%, rgba(210,205,235,0.50) 0%, transparent 55%),
        #d8d8e6;
    color:var(--t1);
    min-height:100vh;font-size:14px;
    -webkit-font-smoothing:antialiased;
}

::-webkit-scrollbar{width:4px}
::-webkit-scrollbar-track{background:transparent}
::-webkit-scrollbar-thumb{background:var(--b3);border-radius:4px}

/* ── TOPBAR ── */
.topbar{
    position:sticky;top:0;z-index:300;
    height:60px;padding:0 32px;
    display:flex;align-items:center;justify-content:space-between;
    background:rgba(255,255,255,0.72);
    backdrop-filter:blur(24px) saturate(180%);
    border-bottom:1px solid var(--b2);
    box-shadow:0 1px 0 rgba(255,255,255,0.9) inset, 0 2px 10px rgba(0,0,0,0.05);
}
.brand{display:flex;align-items:center;gap:10px;text-decoration:none;color:var(--t1);}
.brand i{color:var(--t1);font-size:13px;}
.brand-name{font-family:'Syne',sans-serif;font-size:.85rem;font-weight:700;letter-spacing:.08em;color:var(--t1);}
.breadcrumb{display:flex;align-items:center;gap:6px;font-size:.75rem;color:var(--t3);}
.breadcrumb a{color:var(--t2);text-decoration:none;transition:color .15s}
.breadcrumb a:hover{color:var(--t1)}
.breadcrumb i{font-size:9px}
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

/* ── WRAP ── */
.wrap{max-width:900px;margin:0 auto;padding:32px 24px 100px;}

/* ── PAGE HEADING ── */
.pg-hd{margin-bottom:28px;}
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

/* ── ITEM CARDS ── */
.item-card{
    background:rgba(255,255,255,0.62);
    backdrop-filter:blur(14px);
    border:1px solid rgba(255,255,255,0.85);
    border-radius:var(--r3);overflow:hidden;margin-bottom:16px;
    box-shadow:0 2px 0 rgba(255,255,255,0.9) inset, 0 4px 20px rgba(0,0,0,0.06);
    transition:border-color .2s, box-shadow .2s;
}
.item-card:hover{
    border-color:rgba(255,255,255,1);
    box-shadow:0 2px 0 rgba(255,255,255,0.9) inset, 0 8px 28px rgba(0,0,0,0.09);
}
.item-card-hd{
    display:flex;align-items:center;gap:10px;
    padding:12px 16px;
    background:rgba(255,255,255,0.45);
    border-bottom:1px solid var(--b1);
    cursor:pointer;
}
.item-order{
    width:22px;height:22px;
    background:rgba(255,255,255,0.60);border:1px solid var(--b2);
    border-radius:var(--r4);
    display:flex;align-items:center;justify-content:center;
    font-family:'Syne',sans-serif;font-size:.6rem;font-weight:700;
    color:var(--t2);flex-shrink:0;
}
.item-title-preview{flex:1;font-size:.78rem;color:var(--t2);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.item-actions{display:flex;gap:4px;margin-left:auto;}
.ia-btn{
    display:inline-flex;align-items:center;gap:4px;
    padding:5px 9px;border-radius:var(--r1);
    font-size:.67rem;font-weight:600;
    border:1px solid transparent;cursor:pointer;
    font-family:'DM Sans',sans-serif;transition:all .12s;background:transparent;
}
.ia-del{border-color:rgba(225,112,85,.22);color:var(--red);background:rgba(225,112,85,.06);}
.ia-del:hover{background:rgba(225,112,85,.16);border-color:var(--red);}
.ia-toggle{border-color:var(--b2);color:var(--t2);background:rgba(255,255,255,0.40);}
.ia-toggle:hover{border-color:var(--b3);color:var(--t1);background:rgba(255,255,255,0.75);}

.item-body{padding:20px;}
.item-body.collapsed{display:none}

/* ── FIELDS ── */
.fg{display:grid;gap:12px;margin-bottom:14px;}
.fg-2{grid-template-columns:1fr 1fr}
.f{display:flex;flex-direction:column;gap:4px;}
.f label{
    font-family:'Syne',sans-serif;font-size:.58rem;font-weight:700;
    letter-spacing:.1em;text-transform:uppercase;color:var(--t3);
}
.f input,.f textarea{
    padding:7px 10px;
    background:rgba(255,255,255,0.55);
    border:1px solid var(--b2);border-radius:var(--r1);
    color:var(--t1);font-size:.82rem;font-family:'DM Sans',sans-serif;
    outline:none;transition:border-color .14s, background .14s;width:100%;
    backdrop-filter:blur(4px);
}
.f input:focus,.f textarea:focus{border-color:var(--b4);background:rgba(255,255,255,0.88);}
.f input::placeholder,.f textarea::placeholder{color:var(--t4)}
.f textarea{min-height:75px;resize:vertical}
.f small{font-size:.67rem;color:var(--t4)}

.img-prev{
    display:block;width:100%;max-height:90px;object-fit:cover;
    border-radius:var(--r1);border:1px solid var(--b2);
    margin-top:6px;opacity:.85;
}
.img-prev[src=""]{display:none}

.fdiv{height:1px;background:var(--b2);margin:14px 0}
.sub-lbl{
    font-family:'Syne',sans-serif;font-size:.6rem;font-weight:700;
    letter-spacing:.1em;text-transform:uppercase;
    color:var(--t3);margin-bottom:10px;
    display:flex;align-items:center;gap:8px;
}
.sub-lbl::after{content:'';flex:1;height:1px;background:var(--b2)}

/* ── BUTTONS ── */
.btn-save{
    display:inline-flex;align-items:center;gap:7px;
    padding:9px 20px;
    background:linear-gradient(135deg,#1a1a24,#2e2e3c);
    color:#fff;border:none;border-radius:var(--r2);
    font-size:.78rem;font-weight:700;font-family:'DM Sans',sans-serif;
    cursor:pointer;transition:all .18s;
    box-shadow:0 3px 14px rgba(0,0,0,0.18);
}
.btn-save:hover{box-shadow:0 5px 22px rgba(0,0,0,0.26);transform:translateY(-1px);}
.btn-save:active{transform:translateY(0);}

.btn-del{
    display:inline-flex;align-items:center;gap:7px;
    padding:9px 16px;
    background:rgba(225,112,85,.06);
    border:1px solid rgba(225,112,85,.22);
    border-radius:var(--r2);color:var(--red);
    font-size:.78rem;font-family:'DM Sans',sans-serif;
    cursor:pointer;transition:all .15s;
}
.btn-del:hover{background:rgba(225,112,85,.14);border-color:var(--red);}

/* ── ADD CARD ── */
.add-card{
    background:rgba(255,255,255,0.52);
    backdrop-filter:blur(12px);
    border:2px dashed var(--b3);
    border-radius:var(--r3);
    padding:24px;margin-top:24px;
    transition:border-color .2s;
}
.add-card:hover{border-color:var(--b4);}
.add-card-hd{
    font-family:'Syne',sans-serif;font-size:.75rem;font-weight:700;
    letter-spacing:.08em;color:var(--t2);
    margin-bottom:18px;
    display:flex;align-items:center;gap:8px;
}

/* ── SAVE BAR ── */
.save-bar{
    position:fixed;bottom:0;left:0;right:0;z-index:200;
    padding:14px 24px;
    background:rgba(255,255,255,0.75);
    backdrop-filter:blur(20px);
    border-top:1px solid var(--b2);
    display:flex;align-items:center;justify-content:space-between;
    box-shadow:0 -2px 16px rgba(0,0,0,0.06);
}
.save-info{font-size:.76rem;color:var(--t3);}
.save-info strong{color:var(--t1);}

.btn-g{
    display:inline-flex;align-items:center;gap:7px;
    padding:9px 16px;
    background:rgba(255,255,255,0.60);
    color:var(--t2);border:1px solid var(--b2);border-radius:var(--r2);
    font-size:.78rem;font-family:'DM Sans',sans-serif;
    cursor:pointer;text-decoration:none;transition:all .15s;
    backdrop-filter:blur(4px);
}
.btn-g:hover{border-color:var(--b3);color:var(--t1);background:rgba(255,255,255,0.85);}

/* ── PREVIEW PANE ── */
.preview-grid{display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;margin-top:10px;}
.preview-img{
    width:100%;aspect-ratio:16/9;object-fit:cover;
    border-radius:var(--r1);border:1px solid var(--b2);opacity:.85;
}
.preview-img[src=""]{display:none}
.preview-label{font-size:.6rem;color:var(--t3);letter-spacing:.06em;text-transform:uppercase;margin-bottom:4px;}

/* ── EMPTY STATE ── */
[style*="border: 1px dashed"]{
    background:rgba(255,255,255,0.40);
    backdrop-filter:blur(8px);
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
            <span style="color:var(--t1)">Gallery</span>
        </nav>
    </div>
    <div style="display:flex;gap:8px;">
        <?php
        $prevUrl = !empty($feature['slug'])
            ? "/lending_word/discover-detail.php?slug={$feature['slug']}"
            : "/lending_word/discover-detail.php?id={$featureId}";
        ?>
        <a href="<?= $prevUrl ?>" target="_blank" class="tpill"><i class="fas fa-up-right-from-square"></i>Preview</a>
        <a href="index.php?tab=discover" class="tpill"><i class="fas fa-arrow-left"></i>Back</a>
    </div>
</header>

<div class="wrap">
    <?php if ($success): ?>
    <div class="toast"><span style="width:6px;height:6px;border-radius:50%;background:var(--green);flex-shrink:0;"></span><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <div class="pg-hd">
        <h1>Gallery / Gallery — <span style="color:var(--gold)"><?= htmlspecialchars($feature['title']) ?></span></h1>
        <p>Kelola layout gallery-style (gambar atas full-width + teks kiri + gambar kanan overlap + gambar bawah)</p>
    </div>

    <!-- ── EXISTING ITEMS ── -->
    <?php foreach ($items as $idx => $item): ?>
    <div class="item-card">
        <div class="item-card-hd" onclick="toggleItem(<?= $item['id'] ?>)">
            <span class="item-order"><?= $idx + 1 ?></span>
            <span class="item-title-preview">
                <?= htmlspecialchars($item['tab_label'] ?: ($item['title'] ?: '(no title)')) ?>
            </span>
            <div class="item-actions" onclick="event.stopPropagation()">
                <button type="button" class="ia-btn ia-toggle" onclick="toggleItem(<?= $item['id'] ?>)"><i class="fas fa-minus"></i></button>
            </div>
        </div>
        <div class="item-body" id="item-<?= $item['id'] ?>">
            <form method="POST">
                <input type="hidden" name="update_id" value="<?= $item['id'] ?>">

                <!-- Tab info -->
                <div class="sub-lbl">Tab Strip</div>
                <div class="fg fg-2" style="margin-bottom:14px">
                    <div class="f">
                        <label>Tab Label <small>(e.g. Gallery)</small></label>
                        <input type="text" name="tab_label" value="<?= htmlspecialchars($item['tab_label'] ?? '') ?>" placeholder="Gallery">
                    </div>
                    <div class="f">
                        <label>Tab ID <small>(anchor, e.g. gallery)</small></label>
                        <input type="text" name="tab_id" value="<?= htmlspecialchars($item['tab_id'] ?? '') ?>" placeholder="gallery">
                    </div>
                </div>

                <!-- Teks -->
                <div class="fdiv"></div>
                <div class="sub-lbl">Konten Teks</div>
                <div class="fg" style="margin-bottom:14px">
                    <div class="f">
                        <label>Eyebrow <small>(label kecil atas judul)</small></label>
                        <input type="text" name="eyebrow" value="<?= htmlspecialchars($item['eyebrow'] ?? '') ?>" placeholder="Company gallery.">
                    </div>
                    <div class="f">
                        <label>Judul</label>
                        <input type="text" name="title" value="<?= htmlspecialchars($item['title'] ?? '') ?>" placeholder="Company gallery.">
                    </div>
                    <div class="f">
                        <label>Paragraf / Deskripsi</label>
                        <textarea name="body" placeholder="We are committed to exclusive quality..."><?= htmlspecialchars($item['body'] ?? '') ?></textarea>
                    </div>
                </div>

                <!-- Gambar -->
                <div class="fdiv"></div>
                <div class="sub-lbl">Gambar</div>

                <!-- Preview grid -->
                <div class="preview-grid" style="margin-bottom:16px;">
                    <div>
                        <div class="preview-label">Atas (full width)</div>
                        <img class="preview-img" src="<?= htmlspecialchars($item['image_top'] ?? '') ?>" id="prev-top-<?= $item['id'] ?>">
                    </div>
                    <div>
                        <div class="preview-label">Kanan (tinggi, overlap)</div>
                        <img class="preview-img" src="<?= htmlspecialchars($item['image_right'] ?? '') ?>" id="prev-right-<?= $item['id'] ?>">
                    </div>
                    <div>
                        <div class="preview-label">Bawah (overlap ke atas)</div>
                        <img class="preview-img" src="<?= htmlspecialchars($item['image_bottom'] ?? '') ?>" id="prev-bot-<?= $item['id'] ?>">
                    </div>
                </div>

                <div class="fg" style="margin-bottom:14px">
                    <div class="f">
                        <label>Image Atas <small>— Full width, memanjang (pensil/crest)</small></label>
                        <input type="text" name="image_top" value="<?= htmlspecialchars($item['image_top'] ?? '') ?>"
                            placeholder="https://..."
                            oninput="document.getElementById('prev-top-<?= $item['id'] ?>').src=this.value">
                    </div>
                    <div class="f">
                        <label>Image Kanan <small>— Tinggi, menonjol ke kanan (interior/detail)</small></label>
                        <input type="text" name="image_right" value="<?= htmlspecialchars($item['image_right'] ?? '') ?>"
                            placeholder="https://..."
                            oninput="document.getElementById('prev-right-<?= $item['id'] ?>').src=this.value">
                    </div>
                    <div class="f">
                        <label>Image Bawah <small>— Overlap ke atas dari bawah (eksterior/detail)</small></label>
                        <input type="text" name="image_bottom" value="<?= htmlspecialchars($item['image_bottom'] ?? '') ?>"
                            placeholder="https://..."
                            oninput="document.getElementById('prev-bot-<?= $item['id'] ?>').src=this.value">
                    </div>
                </div>

                <div class="fg fg-2">
                    <div class="f">
                        <label>Sort Order</label>
                        <input type="number" name="sort_order" value="<?= (int)$item['sort_order'] ?>">
                    </div>
                </div>

                <div style="display:flex;gap:10px;margin-top:18px;align-items:center">
                    <button type="submit" class="btn-save"><i class="fas fa-floppy-disk"></i>Simpan</button>
                    <button type="button" class="btn-del" onclick="deleteItem(<?= $item['id'] ?>)">
                        <i class="fas fa-trash-alt"></i>Hapus
                    </button>
                </div>
            </form>
        </div>
    </div>
    <?php endforeach; ?>

    <?php if (empty($items)): ?>
    <div style="text-align:center;padding:48px;color:var(--t4);border:1px dashed var(--b2);border-radius:var(--r3);">
        <i class="fas fa-images" style="font-size:2rem;opacity:.3;display:block;margin-bottom:12px;"></i>
        <p style="font-size:.82rem;">Belum ada gallery section. Tambahkan di bawah.</p>
    </div>
    <?php endif; ?>

    <!-- ── ADD NEW ── -->
    <div class="add-card">
        <div class="add-card-hd"><i class="fas fa-plus"></i>Tambah Gallery Section Baru</div>
        <form method="POST">
            <input type="hidden" name="create_new" value="1">

            <div class="fg fg-2" style="margin-bottom:14px">
                <div class="f">
                    <label>Tab Label</label>
                    <input type="text" name="tab_label" placeholder="Gallery">
                </div>
                <div class="f">
                    <label>Tab ID</label>
                    <input type="text" name="tab_id" placeholder="gallery">
                </div>
            </div>

            <div class="fg" style="margin-bottom:14px">
                <div class="f">
                    <label>Eyebrow</label>
                    <input type="text" name="eyebrow" placeholder="Company gallery.">
                </div>
                <div class="f">
                    <label>Judul</label>
                    <input type="text" name="title" placeholder="Company gallery.">
                </div>
                <div class="f">
                    <label>Paragraf</label>
                    <textarea name="body" placeholder="We are committed to exclusive quality..."></textarea>
                </div>
            </div>

            <div class="fdiv"></div>
            <div class="sub-lbl">Gambar</div>
            <div class="fg" style="margin-bottom:14px">
                <div class="f">
                    <label>Image Atas <small>(full width — pensil, crest, etc)</small></label>
                    <input type="text" name="image_top" placeholder="https://...">
                </div>
                <div class="f">
                    <label>Image Kanan <small>(tinggi, overlap kanan — interior, detail)</small></label>
                    <input type="text" name="image_right" placeholder="https://...">
                </div>
                <div class="f">
                    <label>Image Bawah <small>(overlap ke atas — eksterior, close-up)</small></label>
                    <input type="text" name="image_bottom" placeholder="https://...">
                </div>
            </div>

            <div class="fg fg-2" style="margin-bottom:14px">
                <div class="f">
                    <label>Sort Order</label>
                    <input type="number" name="sort_order" value="0">
                </div>
            </div>

            <button type="submit" class="btn-save"><i class="fas fa-plus"></i>Tambah Section</button>
        </form>
    </div>
</div>

<!-- Delete form (hidden) -->
<form method="POST" id="deleteForm">
    <input type="hidden" name="delete_id" id="deleteId">
</form>

<div class="save-bar">
    <div class="save-info">Kelola gallery/gallery sections untuk <strong><?= htmlspecialchars($feature['title']) ?></strong></div>
    <a href="index.php?tab=discover" class="btn-g"><i class="fas fa-arrow-left"></i>Kembali</a>
</div>

<script>
function toggleItem(id) {
    const body = document.getElementById('item-' + id);
    const collapsed = body.classList.toggle('collapsed');
    const btn = body.closest('.item-card').querySelector('.ia-toggle i');
    btn.className = collapsed ? 'fas fa-plus' : 'fas fa-minus';
}

function deleteItem(id) {
    if (!confirm('Hapus section ini?')) return;
    document.getElementById('deleteId').value = id;
    document.getElementById('deleteForm').submit();
}
</script>
</body>
</html>