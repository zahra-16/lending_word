<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../app/models/ModelVariant.php';
require_once __DIR__ . '/../app/models/ModelGallery.php';

$variantId = $_GET['variant_id'] ?? 0;

$modelVariant = new ModelVariant();
$variant = $modelVariant->getById($variantId);

if (!$variant) {
    header('Location: /lending_word/admin/?tab=variants');
    exit;
}

$modelGallery = new ModelGallery();
$galleryImages = $modelGallery->getByVariantId($variantId);

$success = '';

// Add gallery image
if (isset($_POST['add_gallery'])) {
    $imageUrls = $_POST['image_url'] ?? [];
    $titles = $_POST['title'] ?? [];
    $sections = $_POST['section'] ?? [];
    $captions = $_POST['caption'] ?? [];
    $sortOrders = $_POST['sort_order'] ?? [];
    $count = 0;
    foreach ($imageUrls as $index => $imageUrl) {
        if (!empty($imageUrl)) {
            $title = $titles[$index] ?? '';
            $section = $sections[$index] ?? '';
            $caption = $captions[$index] ?? '';
            $sortOrder = $sortOrders[$index] ?? 0;
            $modelGallery->create($variantId, $imageUrl, $title, $section, $caption, $sortOrder);
            $count++;
        }
    }
    $success = "$count gallery image(s) added successfully!";
    header("Location: gallery.php?variant_id=$variantId&success=" . urlencode($success));
    exit;
}

// Update gallery image
if (isset($_POST['update_gallery'])) {
    $id = $_POST['id'] ?? 0;
    $imageUrl = $_POST['image_url'] ?? '';
    $title = $_POST['title'] ?? '';
    $section = $_POST['section'] ?? '';
    $caption = $_POST['caption'] ?? '';
    $sortOrder = $_POST['sort_order'] ?? 0;
    if ($id && $imageUrl) {
        $modelGallery->update($id, $imageUrl, $title, $section, $caption, $sortOrder);
        $success = 'Gallery image updated successfully!';
        header("Location: gallery.php?variant_id=$variantId&success=" . urlencode($success));
        exit;
    }
}

// Delete gallery image
if (isset($_POST['delete_gallery'])) {
    $id = $_POST['id'] ?? 0;
    $modelGallery->delete($id);
    $success = 'Gallery image deleted successfully!';
    header("Location: gallery.php?variant_id=$variantId&success=" . urlencode($success));
    exit;
}

if (isset($_GET['success'])) {
    $success = $_GET['success'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery — <?= htmlspecialchars($variant['name']) ?> — Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
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
        --topbar-h: 60px;
    }
    *,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
    body{
        font-family:'DM Sans',sans-serif;
        background:
            radial-gradient(ellipse at 15% 20%, rgba(200,200,230,0.55) 0%, transparent 55%),
            radial-gradient(ellipse at 85% 75%, rgba(210,205,235,0.50) 0%, transparent 55%),
            #d8d8e6;
        color:var(--t1);
        min-height:100vh;font-size:14px;line-height:1.6;
        -webkit-font-smoothing:antialiased;
    }
    ::-webkit-scrollbar{width:4px}
    ::-webkit-scrollbar-track{background:transparent}
    ::-webkit-scrollbar-thumb{background:var(--b3);border-radius:4px}

    /* ── TOPBAR ── */
    .topbar{
        position:sticky;top:0;z-index:300;
        height:var(--topbar-h);padding:0 32px;
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

    /* ── CONTAINER ── */
    .container{max-width:1200px;margin:32px auto;padding:0 40px 80px;}

    /* ── TOAST ── */
    .success{
        display:flex;align-items:center;gap:10px;
        padding:11px 15px;
        background:rgba(0,184,148,0.08);
        border:1px solid rgba(0,184,148,0.22);
        border-radius:var(--r2);color:var(--green);
        font-size:.8rem;margin-bottom:20px;
        backdrop-filter:blur(8px);
    }

    /* ── ADD FORM CARD ── */
    .add-form{
        background:var(--bg2);
        backdrop-filter:blur(16px);
        border:1px solid rgba(255,255,255,0.85);
        border-radius:var(--r3);
        padding:28px 32px;margin-bottom:32px;
        box-shadow:0 2px 0 rgba(255,255,255,0.9) inset, 0 4px 20px rgba(0,0,0,0.06);
    }
    .add-form h2{
        font-family:'Syne',sans-serif;
        font-size:.68rem;font-weight:700;
        letter-spacing:.12em;text-transform:uppercase;
        color:var(--t2);margin-bottom:22px;
        display:flex;align-items:center;gap:8px;
    }
    .add-form h2::after{content:'';flex:1;height:1px;background:var(--b2);}

    /* ── FORM FIELDS ── */
    .form-group{margin-bottom:14px;}
    .form-group label{
        display:block;margin-bottom:5px;
        font-family:'Syne',sans-serif;
        color:var(--t3);font-weight:700;
        font-size:.58rem;text-transform:uppercase;letter-spacing:.1em;
    }
    .form-group input{
        width:100%;padding:7px 10px;
        border:1px solid var(--b2);
        background:rgba(255,255,255,0.55);
        color:var(--t1);font-size:.82rem;
        font-family:'DM Sans',sans-serif;
        border-radius:var(--r1);outline:none;
        transition:border-color .14s, background .14s;
        backdrop-filter:blur(4px);
    }
    .form-group input:focus{border-color:var(--b4);background:rgba(255,255,255,0.88);}
    .form-group input::placeholder{color:var(--t4);}

    /* ── BUTTONS ── */
    .btn-save{
        display:inline-flex;align-items:center;gap:7px;
        padding:9px 22px;
        background:linear-gradient(135deg,#1a1a24,#2e2e3c);
        color:#fff;border:none;border-radius:var(--r2);
        font-size:.78rem;font-weight:700;
        font-family:'DM Sans',sans-serif;
        cursor:pointer;transition:all .18s;
        box-shadow:0 3px 14px rgba(0,0,0,0.18);
        text-transform:uppercase;letter-spacing:.06em;
    }
    .btn-save:hover{box-shadow:0 5px 22px rgba(0,0,0,0.26);transform:translateY(-1px);}
    .btn-save:active{transform:translateY(0);}

    .btn-add-more{
        display:inline-flex;align-items:center;gap:7px;
        padding:9px 18px;
        background:rgba(255,255,255,0.60);color:var(--t2);
        border:1px solid var(--b2);border-radius:var(--r2);
        font-size:.78rem;font-weight:600;
        font-family:'DM Sans',sans-serif;
        cursor:pointer;transition:all .18s;
        backdrop-filter:blur(4px);
        text-transform:uppercase;letter-spacing:.06em;
        margin-right:10px;
    }
    .btn-add-more:hover{border-color:var(--b3);color:var(--t1);background:rgba(255,255,255,0.85);}

    /* ── GALLERY GRID ── */
    .gallery-grid{
        display:grid;
        grid-template-columns:repeat(auto-fill, minmax(340px, 1fr));
        gap:20px;
    }

    /* ── GALLERY ITEM CARD ── */
    .gallery-item{
        background:var(--bg2);backdrop-filter:blur(14px);
        border:1px solid rgba(255,255,255,0.85);
        border-radius:var(--r3);overflow:hidden;
        box-shadow:0 2px 0 rgba(255,255,255,0.9) inset, 0 4px 20px rgba(0,0,0,0.06);
        transition:border-color .2s, box-shadow .2s;
    }
    .gallery-item:hover{
        border-color:rgba(255,255,255,1);
        box-shadow:0 2px 0 rgba(255,255,255,0.9) inset, 0 8px 28px rgba(0,0,0,0.09);
    }
    .gallery-item img{width:100%;height:220px;object-fit:cover;display:block;border-bottom:1px solid var(--b1);}
    .gallery-item-body{padding:18px;}
    .gallery-item .form-group input{
        padding:7px 10px;font-size:.82rem;
        background:rgba(255,255,255,0.55);
        border:1px solid var(--b2);border-radius:var(--r1);
        color:var(--t1);font-family:'DM Sans',sans-serif;width:100%;
    }
    .gallery-item .form-group input:focus{border-color:var(--b4);background:rgba(255,255,255,0.88);}

    .btn-update{
        display:inline-flex;align-items:center;justify-content:center;gap:5px;
        padding:8px 14px;
        background:rgba(255,255,255,0.60);color:var(--t2);
        border:1px solid var(--b2);border-radius:var(--r1);
        font-size:.72rem;font-weight:600;
        cursor:pointer;font-family:'DM Sans',sans-serif;
        text-transform:uppercase;letter-spacing:.05em;
        backdrop-filter:blur(4px);transition:all .15s;
        width:48%;margin-right:4%;
    }
    .btn-update:hover{background:rgba(255,255,255,0.90);border-color:var(--b3);color:var(--t1);transform:translateY(-1px);}

    .btn-delete{
        display:inline-flex;align-items:center;justify-content:center;gap:5px;
        padding:8px 14px;
        background:rgba(225,112,85,0.06);color:var(--red);
        border:1px solid rgba(225,112,85,0.22);border-radius:var(--r1);
        font-size:.72rem;font-weight:600;
        cursor:pointer;font-family:'DM Sans',sans-serif;
        text-transform:uppercase;letter-spacing:.05em;
        transition:all .15s;width:48%;
    }
    .btn-delete:hover{background:rgba(225,112,85,0.16);border-color:var(--red);}

    /* ── EMPTY STATE ── */
    .empty-state{
        text-align:center;padding:60px 20px;color:var(--t4);
        border:1px dashed var(--b3);border-radius:var(--r3);
        background:rgba(255,255,255,0.35);backdrop-filter:blur(8px);
    }
    .empty-state i{font-size:1.8rem;margin-bottom:12px;opacity:.25;display:block;}
    .empty-state p{font-size:.82rem;}

    @media(max-width:768px){
        .topbar{padding:0 16px;}
        .breadcrumb{display:none;}
        .container{padding:0 16px 60px;}
        .add-form{padding:20px;}
        .gallery-grid{grid-template-columns:1fr;}
    }
    </style>
</head>
<body>

<header class="topbar">
    <div style="display:flex;align-items:center;gap:16px;">
        <a class="brand" href="/lending_word/admin/?tab=variants">
            <i class="fas fa-shield-halved"></i>
            <span class="brand-name">Porsche Admin</span>
        </a>
        <nav class="breadcrumb">
            <i class="fas fa-chevron-right"></i>
            <a href="/lending_word/admin/?tab=variants">Variants</a>
            <i class="fas fa-chevron-right"></i>
            <span><?= htmlspecialchars($variant['name']) ?></span>
            <i class="fas fa-chevron-right"></i>
            <span style="color:var(--t1)">Gallery</span>
        </nav>
    </div>
    <div class="topbar-r">
        <a href="/lending_word/admin/?tab=variants" class="tpill">
            <i class="fas fa-arrow-left"></i>Back to Variants
        </a>
    </div>
</header>

<div class="container">
    <?php if ($success): ?>
        <div class="success">
            <span style="width:6px;height:6px;border-radius:50%;background:var(--green);flex-shrink:0;display:inline-block;"></span>
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <div class="add-form">
        <h2>Add Gallery Images</h2>
        <form method="POST" id="galleryForm">
            <div id="imageInputs">
                <div class="image-input-group">
                    <div class="form-group">
                        <label>Image URL 1</label>
                        <input type="text" name="image_url[]" placeholder="https://example.com/image.jpg" required>
                    </div>
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title[]" placeholder="e.g. Thrill of the 911">
                    </div>
                    <div class="form-group">
                        <label>Section</label>
                        <input type="text" name="section[]" placeholder="e.g. Performance, Design, Interior">
                    </div>
                    <div class="form-group">
                        <label>Caption (Optional)</label>
                        <input type="text" name="caption[]" placeholder="Image description">
                    </div>
                    <div class="form-group">
                        <label>Sort Order</label>
                        <input type="number" name="sort_order[]" value="0" style="width:120px;">
                    </div>
                </div>
            </div>
            <div style="display:flex;align-items:center;margin-top:8px;">
                <button type="button" onclick="addImageInput()" class="btn-add-more">
                    <i class="fas fa-plus"></i>Add More
                </button>
                <button type="submit" name="add_gallery" class="btn-save">
                    <i class="fas fa-floppy-disk"></i>Save All Images
                </button>
            </div>
        </form>
    </div>

    <script>
    let imageCount = 1;
    function addImageInput() {
        imageCount++;
        const div = document.createElement('div');
        div.className = 'image-input-group';
        div.style.marginTop = '24px';
        div.style.paddingTop = '24px';
        div.style.borderTop = '1px solid rgba(0,0,0,0.08)';
        div.innerHTML = `
            <div class="form-group">
                <label>Image URL ${imageCount}</label>
                <input type="text" name="image_url[]" placeholder="https://example.com/image.jpg" required>
            </div>
            <div class="form-group">
                <label>Title</label>
                <input type="text" name="title[]" placeholder="e.g. Thrill of the 911">
            </div>
            <div class="form-group">
                <label>Section</label>
                <input type="text" name="section[]" placeholder="e.g. Performance, Design, Interior">
            </div>
            <div class="form-group">
                <label>Caption (Optional)</label>
                <input type="text" name="caption[]" placeholder="Image description">
            </div>
            <div class="form-group">
                <label>Sort Order</label>
                <input type="number" name="sort_order[]" value="${imageCount - 1}" style="width:120px;">
            </div>
        `;
        document.getElementById('imageInputs').appendChild(div);
    }
    </script>

    <div class="gallery-grid">
        <?php foreach ($galleryImages as $image): ?>
        <div class="gallery-item">
            <form method="POST">
                <input type="hidden" name="id" value="<?= $image['id'] ?>">
                <img src="<?= htmlspecialchars($image['image_url']) ?>" alt="<?= htmlspecialchars($image['caption'] ?? '') ?>">
                <div class="gallery-item-body">
                    <div class="form-group">
                        <label>Image URL</label>
                        <input type="text" name="image_url" value="<?= htmlspecialchars($image['image_url']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" value="<?= htmlspecialchars($image['title'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Section</label>
                        <input type="text" name="section" value="<?= htmlspecialchars($image['section'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Caption</label>
                        <input type="text" name="caption" value="<?= htmlspecialchars($image['caption'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Sort Order</label>
                        <input type="number" name="sort_order" value="<?= $image['sort_order'] ?>" style="width:120px;">
                    </div>
                    <div style="display:flex;margin-top:14px;">
                        <button type="submit" name="update_gallery" class="btn-update">
                            <i class="fas fa-floppy-disk"></i>Update
                        </button>
                        <button type="submit" name="delete_gallery" class="btn-delete" onclick="return confirm('Delete this image?')">
                            <i class="fas fa-trash-alt"></i>Delete
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <?php endforeach; ?>
    </div>

    <?php if (empty($galleryImages)): ?>
    <div class="empty-state">
        <i class="fas fa-images"></i>
        <p>No gallery images yet. Add your first image above.</p>
    </div>
    <?php endif; ?>
</div>

</body>
</html>