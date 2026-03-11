<?php
    session_start();
    if (!isset($_SESSION['admin_logged_in'])) {
        header('Location: login.php');
        exit;
    }

    require_once __DIR__ . '/../app/models/ModelVariant.php';
    require_once __DIR__ . '/../app/models/ModelSpecificationSection.php';

    $modelVariant = new ModelVariant();
    $modelSpec = new ModelSpecificationSection();

    $variantId = $_GET['variant_id'] ?? 0;
    $variant = $modelVariant->getById($variantId);

    if (!$variant) {
        header('Location: manage_models.php');
        exit;
    }

    // Handle actions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';
        if ($action === 'add_section') {
            $modelSpec->create($variantId,$_POST['background_image'],$_POST['title'],$_POST['description'],$_POST['sort_order'] ?? 0);
            header('Location: specification.php?variant_id=' . $variantId); exit;
        }
        if ($action === 'update_section') {
            $modelSpec->update($_POST['section_id'],$_POST['background_image'],$_POST['title'],$_POST['description'],$_POST['sort_order'] ?? 0);
            header('Location: specification.php?variant_id=' . $variantId); exit;
        }
        if ($action === 'delete_section') {
            $modelSpec->delete($_POST['section_id']);
            header('Location: specification.php?variant_id=' . $variantId); exit;
        }
        if ($action === 'add_hero_card') {
            $modelSpec->addHeroCard($_POST['section_id'],$_POST['image_url'],$_POST['title'],$_POST['description'],$_POST['sort_order'] ?? 0);
            header('Location: specification.php?variant_id=' . $variantId); exit;
        }
        if ($action === 'update_hero_card') {
            $modelSpec->updateHeroCard($_POST['card_id'],$_POST['image_url'],$_POST['title'],$_POST['description'],$_POST['sort_order'] ?? 0);
            header('Location: specification.php?variant_id=' . $variantId); exit;
        }
        if ($action === 'delete_hero_card') {
            $modelSpec->deleteHeroCard($_POST['card_id']);
            header('Location: specification.php?variant_id=' . $variantId); exit;
        }
        if ($action === 'add_image') {
            $modelSpec->addImage($_POST['section_id'],$_POST['image_url'],$_POST['title'],$_POST['description'],$_POST['sort_order'] ?? 0);
            header('Location: specification.php?variant_id=' . $variantId); exit;
        }
        if ($action === 'update_image') {
            $modelSpec->updateImage($_POST['image_id'],$_POST['image_url'],$_POST['title'],$_POST['description'],$_POST['sort_order'] ?? 0);
            header('Location: specification.php?variant_id=' . $variantId); exit;
        }
        if ($action === 'delete_image') {
            $modelSpec->deleteImage($_POST['image_id']);
            header('Location: specification.php?variant_id=' . $variantId); exit;
        }
    }

    // Handle AJAX requests
    if (isset($_GET['action'])) {
        header('Content-Type: application/json');
        $sectionId = $_GET['section_id'] ?? 0;
        if ($_GET['action'] === 'get_images') { echo json_encode($modelSpec->getSectionImages($sectionId)); exit; }
        if ($_GET['action'] === 'get_hero_cards') { echo json_encode($modelSpec->getHeroCards($sectionId)); exit; }
    }

    $sections = $modelSpec->getByVariantId($variantId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Specification — <?= htmlspecialchars($variant['name']) ?> — Admin</title>
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
        --blue:  #0984e3;
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
        color:var(--t1);min-height:100vh;font-size:14px;line-height:1.6;
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
    .container{max-width:1400px;margin:32px auto;padding:0 40px 80px;}

    /* ── PAGE HEADER ── */
    .page-hd{display:flex;justify-content:space-between;align-items:center;margin-bottom:28px;}
    .page-hd h2{font-family:'Syne',sans-serif;font-size:1.1rem;font-weight:700;letter-spacing:-.01em;color:var(--t1);}

    /* ── BUTTONS ── */
    .btn{
        display:inline-flex;align-items:center;gap:6px;
        padding:8px 18px;border:none;border-radius:var(--r2);
        cursor:pointer;font-size:.76rem;font-weight:600;
        font-family:'DM Sans',sans-serif;text-decoration:none;
        transition:all .18s;text-transform:uppercase;letter-spacing:.05em;
    }
    .btn-primary{background:linear-gradient(135deg,#1a1a24,#2e2e3c);color:#fff;box-shadow:0 2px 10px rgba(0,0,0,0.16);}
    .btn-primary:hover{box-shadow:0 4px 18px rgba(0,0,0,0.24);transform:translateY(-1px);}
    .btn-success{background:linear-gradient(135deg,#00b894,#00a381);color:#fff;box-shadow:0 2px 10px rgba(0,184,148,0.22);}
    .btn-success:hover{box-shadow:0 4px 18px rgba(0,184,148,0.32);transform:translateY(-1px);}
    .btn-danger{background:rgba(225,112,85,0.10);color:var(--red);border:1px solid rgba(225,112,85,0.26);}
    .btn-danger:hover{background:rgba(225,112,85,0.20);border-color:var(--red);}
    .btn-secondary{
        background:rgba(255,255,255,0.60);color:var(--t2);
        border:1px solid var(--b2);backdrop-filter:blur(4px);
    }
    .btn-secondary:hover{background:rgba(255,255,255,0.88);border-color:var(--b3);color:var(--t1);}
    .btn-sm{padding:5px 10px;font-size:.68rem;}

    /* ── SECTIONS GRID ── */
    .sections-grid{display:grid;gap:28px;margin-bottom:60px;}

    /* ── SECTION CARD ── */
    .section-card{
        background:var(--bg2);backdrop-filter:blur(14px);
        border:1px solid rgba(255,255,255,0.85);
        border-radius:var(--r3);overflow:hidden;
        box-shadow:0 2px 0 rgba(255,255,255,0.9) inset, 0 4px 20px rgba(0,0,0,0.06);
    }
    .section-preview{
        position:relative;height:440px;
        background-size:cover;background-position:center;
    }
    .section-preview::before{content:'';position:absolute;inset:0;background:rgba(0,0,0,0.38);}
    .section-info{position:absolute;bottom:24px;left:24px;right:24px;color:#fff;z-index:2;}
    .section-info h3{font-family:'Syne',sans-serif;font-size:1.6rem;margin-bottom:8px;font-weight:700;}
    .section-info p{font-size:.88rem;opacity:.88;line-height:1.55;}

    /* ── SUB SECTION LABELS ── */
    .sub-hd{
        font-family:'Syne',sans-serif;font-size:.6rem;font-weight:700;
        letter-spacing:.1em;text-transform:uppercase;
        color:var(--t3);margin-bottom:12px;
        display:flex;align-items:center;gap:8px;
        padding:0 20px;
    }
    .sub-hd::after{content:'';flex:1;height:1px;background:var(--b2);}
    .sub-wrap{padding:16px 20px 0;}

    /* ── CAROUSEL PREVIEW ── */
    .carousel-preview{
        display:flex;gap:12px;
        overflow-x:auto;padding-bottom:8px;
        scrollbar-width:thin;
    }
    .carousel-card{
        min-width:220px;
        background:rgba(255,255,255,0.70);
        backdrop-filter:blur(8px);
        border:1px solid rgba(255,255,255,0.9);
        border-radius:var(--r2);overflow:hidden;
        position:relative;flex-shrink:0;
        box-shadow:0 2px 12px rgba(0,0,0,0.06);
    }
    .carousel-card img{width:100%;height:130px;object-fit:cover;display:block;border-bottom:1px solid var(--b1);}
    .carousel-card-content{padding:12px;}
    .carousel-card-content h4{font-family:'Syne',sans-serif;font-size:.78rem;font-weight:700;margin-bottom:5px;color:var(--t1);}
    .carousel-card-content p{font-size:.72rem;color:var(--t3);line-height:1.4;}
    .carousel-card-content small{font-size:.62rem;color:var(--t4);}
    .card-actions{position:absolute;top:8px;right:8px;display:flex;gap:4px;}

    /* ── SECTION ACTIONS BAR ── */
    .section-actions{
        display:flex;justify-content:space-between;align-items:center;
        padding:14px 20px;
        border-top:1px solid var(--b1);
        background:rgba(255,255,255,0.35);
    }
    .section-meta{font-size:.72rem;color:var(--t3);}
    .section-actions-btns{display:flex;gap:6px;flex-wrap:wrap;}

    /* ── EMPTY STATE ── */
    .empty-state{
        text-align:center;padding:80px 20px;
        color:var(--t4);
        border:1px dashed var(--b3);border-radius:var(--r3);
        background:rgba(255,255,255,0.35);backdrop-filter:blur(8px);
    }
    .empty-state i{font-size:2.5rem;margin-bottom:16px;opacity:.2;display:block;}
    .empty-state p{font-size:.88rem;margin-bottom:20px;}

    /* ── MODAL ── */
    .modal{display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(10,10,20,0.55);z-index:1000;overflow-y:auto;backdrop-filter:blur(6px);}
    .modal.active{display:flex;align-items:center;justify-content:center;padding:40px;}
    .modal-content{
        background:rgba(255,255,255,0.88);
        backdrop-filter:blur(24px);
        border:1px solid rgba(255,255,255,0.95);
        max-width:680px;width:100%;
        padding:36px;border-radius:var(--r3);
        max-height:90vh;overflow-y:auto;
        box-shadow:0 20px 60px rgba(0,0,0,0.18);
    }
    .modal-content.wide{max-width:960px;}
    .modal-content h2{
        font-family:'Syne',sans-serif;font-size:1rem;font-weight:700;
        letter-spacing:.04em;margin-bottom:24px;color:var(--t1);
        display:flex;align-items:center;gap:8px;
    }
    .modal-content h2::after{content:'';flex:1;height:1px;background:var(--b2);}

    /* ── MODAL FORM FIELDS ── */
    .form-group{margin-bottom:18px;}
    .form-group label{
        display:block;margin-bottom:5px;
        font-family:'Syne',sans-serif;
        color:var(--t3);font-weight:700;
        font-size:.58rem;text-transform:uppercase;letter-spacing:.1em;
    }
    .form-group input,
    .form-group textarea{
        width:100%;padding:8px 11px;
        background:rgba(255,255,255,0.60);
        border:1px solid var(--b2);border-radius:var(--r1);
        color:var(--t1);font-size:.83rem;font-family:'DM Sans',sans-serif;
        outline:none;transition:border-color .14s,background .14s;
        backdrop-filter:blur(4px);
    }
    .form-group input:focus,
    .form-group textarea:focus{border-color:var(--b4);background:rgba(255,255,255,0.92);}
    .form-group input::placeholder,
    .form-group textarea::placeholder{color:var(--t4);}
    .form-group textarea{min-height:100px;resize:vertical;}
    .form-actions{display:flex;gap:8px;justify-content:flex-end;margin-top:24px;}

    /* ── MODAL LIST ITEMS ── */
    .modal-list-item{
        display:flex;gap:14px;
        padding:14px;
        background:rgba(255,255,255,0.65);
        border:1px solid var(--b2);border-radius:var(--r2);
        margin-bottom:12px;align-items:center;
        backdrop-filter:blur(4px);
    }
    .modal-list-item img{width:90px;height:68px;object-fit:cover;border-radius:var(--r1);border:1px solid var(--b1);flex-shrink:0;}
    .modal-list-item-info{flex:1;min-width:0;}
    .modal-list-item-info h4{font-family:'Syne',sans-serif;font-size:.8rem;font-weight:700;margin-bottom:4px;color:var(--t1);}
    .modal-list-item-info p{font-size:.74rem;color:var(--t3);line-height:1.4;}
    .modal-list-item-info small{font-size:.64rem;color:var(--t4);}
    .modal-list-item-actions{display:flex;flex-direction:column;gap:5px;flex-shrink:0;}

    @media(max-width:768px){
        .topbar{padding:0 16px;}
        .breadcrumb{display:none;}
        .container{padding:0 16px 60px;}
        .section-actions-btns{gap:4px;}
        .btn{padding:7px 12px;font-size:.68rem;}
    }
    </style>
</head>
<body>

<!-- ── TOPBAR ── -->
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
            <span style="color:var(--t1)">Specification</span>
        </nav>
    </div>
    <div class="topbar-r">
        <a href="/lending_word/admin/?tab=variants" class="tpill">
            <i class="fas fa-arrow-left"></i>Back to Variants
        </a>
    </div>
</header>

<div class="container">
    <div class="page-hd">
        <h2>Specification Sections</h2>
        <button class="btn btn-success" onclick="showModal('addSectionModal')">
            <i class="fas fa-plus"></i>Add Section
        </button>
    </div>

    <?php if (empty($sections)): ?>
    <div class="empty-state">
        <i class="fas fa-image"></i>
        <p>No specification sections yet.</p>
        <button class="btn btn-primary" onclick="showModal('addSectionModal')">Create First Section</button>
    </div>
    <?php else: ?>
    <div class="sections-grid">
        <?php foreach ($sections as $section):
            $heroCards = $modelSpec->getHeroCards($section['id']);
            $carouselImages = $modelSpec->getSectionImages($section['id']);
        ?>
        <div class="section-card">
            <!-- Preview -->
            <div class="section-preview" style="background-image:url('<?= htmlspecialchars($section['background_image']) ?>');">
                <div class="section-info">
                    <h3><?= htmlspecialchars($section['title']) ?></h3>
                    <p><?= htmlspecialchars($section['description']) ?></p>
                </div>
            </div>

            <!-- Hero Cards -->
            <?php if (!empty($heroCards)): ?>
            <div class="sub-wrap" style="margin-top:16px;">
                <div class="sub-hd"><i class="fas fa-th-large" style="font-size:.55rem;"></i>Hero Cards (<?= count($heroCards) ?>)</div>
                <div class="carousel-preview">
                    <?php foreach ($heroCards as $card): ?>
                    <div class="carousel-card">
                        <img src="<?= htmlspecialchars($card['image_url']) ?>" alt="<?= htmlspecialchars($card['title']) ?>">
                        <div class="carousel-card-content">
                            <h4><?= htmlspecialchars($card['title']) ?></h4>
                            <p><?= htmlspecialchars(substr($card['description'], 0, 70)) ?>…</p>
                            <small>Sort: <?= $card['sort_order'] ?></small>
                        </div>
                        <div class="card-actions">
                            <button type="button" class="btn btn-sm btn-primary" onclick='editHeroCardDirect(<?= json_encode($card) ?>)'>
                                <i class="fas fa-edit"></i>
                            </button>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="delete_hero_card">
                                <input type="hidden" name="card_id" value="<?= $card['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this card?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Carousel Images -->
            <?php if (!empty($carouselImages)): ?>
            <div class="sub-wrap" style="margin-top:16px;">
                <div class="sub-hd"><i class="fas fa-images" style="font-size:.55rem;"></i>Carousel Images (<?= count($carouselImages) ?>)</div>
                <div class="carousel-preview">
                    <?php foreach ($carouselImages as $img): ?>
                    <div class="carousel-card">
                        <img src="<?= htmlspecialchars($img['image_url']) ?>" alt="<?= htmlspecialchars($img['title']) ?>">
                        <div class="carousel-card-content">
                            <h4><?= htmlspecialchars($img['title']) ?></h4>
                            <p><?= htmlspecialchars(substr($img['description'], 0, 60)) ?>…</p>
                            <small>Sort: <?= $img['sort_order'] ?></small>
                        </div>
                        <div class="card-actions">
                            <button type="button" class="btn btn-sm btn-primary" onclick='editImageDirect(<?= json_encode($img) ?>)'>
                                <i class="fas fa-edit"></i>
                            </button>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="delete_image">
                                <input type="hidden" name="image_id" value="<?= $img['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this image?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Actions Bar -->
            <div class="section-actions">
                <div class="section-meta">
                    Sort: <?= $section['sort_order'] ?> &nbsp;·&nbsp;
                    <?= count($heroCards) ?> cards &nbsp;·&nbsp;
                    <?= count($carouselImages) ?> carousel
                </div>
                <div class="section-actions-btns">
                    <button class="btn btn-secondary btn-sm" onclick="manageHeroCards(<?= $section['id'] ?>)">
                        <i class="fas fa-th-large"></i>Cards
                    </button>
                    <button class="btn btn-secondary btn-sm" onclick="manageImages(<?= $section['id'] ?>)">
                        <i class="fas fa-images"></i>Carousel
                    </button>
                    <button class="btn btn-primary btn-sm" onclick="editSection(<?= $section['id'] ?>)">
                        <i class="fas fa-edit"></i>Edit
                    </button>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="action" value="delete_section">
                        <input type="hidden" name="section_id" value="<?= $section['id'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this section?')">
                            <i class="fas fa-trash"></i>Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<!-- ========================= MODALS ========================= -->

<!-- Add Section -->
<div id="addSectionModal" class="modal">
    <div class="modal-content">
        <h2>Add New Section</h2>
        <form method="POST">
            <input type="hidden" name="action" value="add_section">
            <div class="form-group"><label>Background Image URL</label><input type="url" name="background_image" required placeholder="https://..."></div>
            <div class="form-group"><label>Title</label><input type="text" name="title" required></div>
            <div class="form-group"><label>Description</label><textarea name="description" required></textarea></div>
            <div class="form-group"><label>Sort Order</label><input type="number" name="sort_order" value="0" style="width:120px;"></div>
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="hideModal('addSectionModal')">Cancel</button>
                <button type="submit" class="btn btn-success"><i class="fas fa-plus"></i>Add Section</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Section -->
<div id="editSectionModal" class="modal">
    <div class="modal-content">
        <h2>Edit Section</h2>
        <form method="POST">
            <input type="hidden" name="action" value="update_section">
            <input type="hidden" name="section_id" id="edit_section_id">
            <div class="form-group"><label>Background Image URL</label><input type="url" name="background_image" id="edit_background_image" required></div>
            <div class="form-group"><label>Title</label><input type="text" name="title" id="edit_title" required></div>
            <div class="form-group"><label>Description</label><textarea name="description" id="edit_description" required></textarea></div>
            <div class="form-group"><label>Sort Order</label><input type="number" name="sort_order" id="edit_sort_order" style="width:120px;"></div>
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="hideModal('editSectionModal')">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-floppy-disk"></i>Update</button>
            </div>
        </form>
    </div>
</div>

<!-- Manage Hero Cards -->
<div id="manageHeroCardsModal" class="modal">
    <div class="modal-content wide">
        <h2>Manage Hero Cards</h2>
        <div style="display:flex;justify-content:space-between;margin-bottom:16px;">
            <button class="btn btn-success" onclick="showModal('addHeroCardModal')"><i class="fas fa-plus"></i>Add Card</button>
            <button class="btn btn-secondary" onclick="hideModal('manageHeroCardsModal')"><i class="fas fa-times"></i>Close</button>
        </div>
        <div id="heroCardsList"></div>
    </div>
</div>

<!-- Manage Carousel Images -->
<div id="manageImagesModal" class="modal">
    <div class="modal-content wide">
        <h2>Manage Carousel Images</h2>
        <div style="display:flex;justify-content:space-between;margin-bottom:16px;">
            <button class="btn btn-success" onclick="showModal('addImageModal')"><i class="fas fa-plus"></i>Add Image</button>
            <button class="btn btn-secondary" onclick="hideModal('manageImagesModal')"><i class="fas fa-times"></i>Close</button>
        </div>
        <div id="imagesList"></div>
    </div>
</div>

<!-- Add Image -->
<div id="addImageModal" class="modal">
    <div class="modal-content">
        <h2>Add Carousel Image</h2>
        <form method="POST">
            <input type="hidden" name="action" value="add_image">
            <input type="hidden" name="section_id" id="add_image_section_id">
            <div class="form-group"><label>Image URL</label><input type="url" name="image_url" required placeholder="https://..."></div>
            <div class="form-group"><label>Title</label><input type="text" name="title" required></div>
            <div class="form-group"><label>Description</label><textarea name="description" required></textarea></div>
            <div class="form-group"><label>Sort Order</label><input type="number" name="sort_order" value="0" style="width:120px;"></div>
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="hideModal('addImageModal')">Cancel</button>
                <button type="submit" class="btn btn-success"><i class="fas fa-plus"></i>Add Image</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Image -->
<div id="editImageModal" class="modal">
    <div class="modal-content">
        <h2>Edit Carousel Image</h2>
        <form method="POST">
            <input type="hidden" name="action" value="update_image">
            <input type="hidden" name="image_id" id="edit_image_id">
            <div class="form-group"><label>Image URL</label><input type="url" name="image_url" id="edit_image_url" required></div>
            <div class="form-group"><label>Title</label><input type="text" name="title" id="edit_image_title" required></div>
            <div class="form-group"><label>Description</label><textarea name="description" id="edit_image_description" required></textarea></div>
            <div class="form-group"><label>Sort Order</label><input type="number" name="sort_order" id="edit_image_sort_order" style="width:120px;"></div>
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="hideModal('editImageModal')">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-floppy-disk"></i>Update</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Hero Card -->
<div id="addHeroCardModal" class="modal">
    <div class="modal-content">
        <h2>Add Hero Card</h2>
        <form method="POST">
            <input type="hidden" name="action" value="add_hero_card">
            <input type="hidden" name="section_id" id="add_card_section_id">
            <div class="form-group"><label>Image URL</label><input type="url" name="image_url" required placeholder="https://..."></div>
            <div class="form-group"><label>Title</label><input type="text" name="title" required placeholder="Engine Power"></div>
            <div class="form-group"><label>Description</label><textarea name="description" required></textarea></div>
            <div class="form-group"><label>Sort Order</label><input type="number" name="sort_order" value="0" style="width:120px;"></div>
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="hideModal('addHeroCardModal')">Cancel</button>
                <button type="submit" class="btn btn-success"><i class="fas fa-plus"></i>Add Card</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Hero Card -->
<div id="editHeroCardModal" class="modal">
    <div class="modal-content">
        <h2>Edit Hero Card</h2>
        <form method="POST">
            <input type="hidden" name="action" value="update_hero_card">
            <input type="hidden" name="card_id" id="edit_card_id">
            <div class="form-group"><label>Image URL</label><input type="url" name="image_url" id="edit_card_url" required></div>
            <div class="form-group"><label>Title</label><input type="text" name="title" id="edit_card_title" required></div>
            <div class="form-group"><label>Description</label><textarea name="description" id="edit_card_description" required></textarea></div>
            <div class="form-group"><label>Sort Order</label><input type="number" name="sort_order" id="edit_card_sort_order" style="width:120px;"></div>
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="hideModal('editHeroCardModal')">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-floppy-disk"></i>Update</button>
            </div>
        </form>
    </div>
</div>

<!-- ========================= JAVASCRIPT ========================= -->
<script>
const sections = <?= json_encode($sections) ?>;
let currentSectionId = null;

function showModal(modalId) { document.getElementById(modalId).classList.add('active'); }
function hideModal(modalId) { document.getElementById(modalId).classList.remove('active'); }

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal')) e.target.classList.remove('active');
});

function editSection(sectionId) {
    const section = sections.find(s => s.id == sectionId);
    if (!section) return;
    document.getElementById('edit_section_id').value = section.id;
    document.getElementById('edit_background_image').value = section.background_image;
    document.getElementById('edit_title').value = section.title;
    document.getElementById('edit_description').value = section.description;
    document.getElementById('edit_sort_order').value = section.sort_order;
    showModal('editSectionModal');
}

function manageHeroCards(sectionId) {
    currentSectionId = sectionId;
    document.getElementById('add_card_section_id').value = sectionId;
    loadHeroCards(sectionId);
    showModal('manageHeroCardsModal');
}

function loadHeroCards(sectionId) {
    fetch(`?action=get_hero_cards&section_id=${sectionId}`)
        .then(r => r.json())
        .then(cards => {
            const el = document.getElementById('heroCardsList');
            if (!cards.length) { el.innerHTML = '<p style="text-align:center;color:var(--t4);padding:40px;">No hero cards yet</p>'; return; }
            el.innerHTML = cards.map(card => `
                <div class="modal-list-item">
                    <img src="${card.image_url}" alt="${card.title}">
                    <div class="modal-list-item-info">
                        <h4>${card.title}</h4>
                        <p>${card.description}</p>
                        <small>Sort: ${card.sort_order}</small>
                    </div>
                    <div class="modal-list-item-actions">
                        <button class="btn btn-primary btn-sm" onclick='editHeroCard(${JSON.stringify(card)})'><i class="fas fa-edit"></i> Edit</button>
                        <form method="POST">
                            <input type="hidden" name="action" value="delete_hero_card">
                            <input type="hidden" name="card_id" value="${card.id}">
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete?')"><i class="fas fa-trash"></i> Delete</button>
                        </form>
                    </div>
                </div>`).join('');
        });
}

function editHeroCardDirect(card) { editHeroCard(card); }
function editHeroCard(card) {
    if (typeof card !== 'object' || !card) return;
    document.getElementById('edit_card_id').value = card.id;
    document.getElementById('edit_card_url').value = card.image_url;
    document.getElementById('edit_card_title').value = card.title;
    document.getElementById('edit_card_description').value = card.description;
    document.getElementById('edit_card_sort_order').value = card.sort_order;
    showModal('editHeroCardModal');
}

function manageImages(sectionId) {
    currentSectionId = sectionId;
    document.getElementById('add_image_section_id').value = sectionId;
    loadImages(sectionId);
    showModal('manageImagesModal');
}

function loadImages(sectionId) {
    fetch(`?action=get_images&section_id=${sectionId}`)
        .then(r => r.json())
        .then(images => {
            const el = document.getElementById('imagesList');
            if (!images.length) { el.innerHTML = '<p style="text-align:center;color:var(--t4);padding:40px;">No carousel images yet</p>'; return; }
            el.innerHTML = images.map(img => `
                <div class="modal-list-item">
                    <img src="${img.image_url}" alt="${img.title}">
                    <div class="modal-list-item-info">
                        <h4>${img.title}</h4>
                        <p>${img.description}</p>
                        <small>Sort: ${img.sort_order}</small>
                    </div>
                    <div class="modal-list-item-actions">
                        <button class="btn btn-primary btn-sm" onclick='editImage(${JSON.stringify(img)})'><i class="fas fa-edit"></i> Edit</button>
                        <form method="POST">
                            <input type="hidden" name="action" value="delete_image">
                            <input type="hidden" name="image_id" value="${img.id}">
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete?')"><i class="fas fa-trash"></i> Delete</button>
                        </form>
                    </div>
                </div>`).join('');
        });
}

function editImageDirect(image) { editImage(image); }
function editImage(image) {
    if (typeof image !== 'object' || !image) return;
    document.getElementById('edit_image_id').value = image.id;
    document.getElementById('edit_image_url').value = image.image_url;
    document.getElementById('edit_image_title').value = image.title;
    document.getElementById('edit_image_description').value = image.description;
    document.getElementById('edit_image_sort_order').value = image.sort_order;
    showModal('editImageModal');
}
</script>
</body>
</html>