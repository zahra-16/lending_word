<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../app/models/Admin.php';
require_once __DIR__ . '/../app/models/ModelVariant.php';

if (!Admin::isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$variantId = $_GET['variant_id'] ?? 0;
$modelVariant = new ModelVariant();
$variant = $modelVariant->getById($variantId);

if (!$variant) {
    header('Location: index.php?tab=variants');
    exit;
}

require_once __DIR__ . '/../config.php';
$db = new PDO(
    "pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME,
    DB_USER,
    DB_PASS
);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$success = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_sound'])) {
        $stmt = $db->prepare("
            INSERT INTO model_sound (variant_id, title, caption, background_image, button_text, audio_url)
            VALUES (?, ?, ?, ?, ?, ?)
            ON CONFLICT (variant_id) 
            DO UPDATE SET 
                title = EXCLUDED.title,
                caption = EXCLUDED.caption,
                background_image = EXCLUDED.background_image,
                button_text = EXCLUDED.button_text,
                audio_url = EXCLUDED.audio_url
        ");
        $stmt->execute([
            $variantId,
            $_POST['title'],
            $_POST['caption'],
            $_POST['background_image'],
            $_POST['button_text'],
            $_POST['audio_url']
        ]);
        $success = 'Sound content saved!';
    }
}

// Get existing sound data
$stmt = $db->prepare("SELECT * FROM model_sound WHERE variant_id = ?");
$stmt->execute([$variantId]);
$soundData = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sound Content — <?= htmlspecialchars($variant['name']) ?> — Admin</title>
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
    .container{max-width:800px;margin:32px auto;padding:0 40px 80px;}

    /* ── PAGE HEADING ── */
    .pg-hd{margin-bottom:24px;}
    .pg-hd h1{font-family:'Syne',sans-serif;font-size:1.4rem;font-weight:700;letter-spacing:-.02em;color:var(--t1);}
    .pg-hd p{font-size:.76rem;color:var(--t3);margin-top:4px;}

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

    /* ── FORM CARD ── */
    .form-card{
        background:var(--bg2);
        backdrop-filter:blur(16px);
        border:1px solid rgba(255,255,255,0.85);
        border-radius:var(--r3);
        padding:32px;
        box-shadow:0 2px 0 rgba(255,255,255,0.9) inset, 0 4px 20px rgba(0,0,0,0.06);
    }

    /* ── FORM FIELDS ── */
    .form-group{margin-bottom:20px;}
    .form-group label{
        display:block;margin-bottom:6px;
        font-family:'Syne',sans-serif;
        color:var(--t3);font-weight:700;
        font-size:.58rem;text-transform:uppercase;letter-spacing:.1em;
    }
    .form-group input,
    .form-group textarea{
        width:100%;padding:8px 12px;
        background:rgba(255,255,255,0.55);
        border:1px solid var(--b2);border-radius:var(--r1);
        color:var(--t1);font-family:'DM Sans',sans-serif;font-size:.84rem;
        outline:none;transition:border-color .14s, background .14s;
        backdrop-filter:blur(4px);
    }
    .form-group input:focus,
    .form-group textarea:focus{border-color:var(--b4);background:rgba(255,255,255,0.88);}
    .form-group input::placeholder,
    .form-group textarea::placeholder{color:var(--t4);}
    .form-group textarea{min-height:100px;resize:vertical;}

    /* ── IMAGE PREVIEW ── */
    .image-preview{
        display:block;margin-top:10px;
        max-width:100%;height:180px;object-fit:cover;
        border-radius:var(--r2);
        border:1px solid var(--b2);
        opacity:.85;
    }

    /* ── SAVE BUTTON ── */
    .btn-save{
        display:inline-flex;align-items:center;gap:8px;
        padding:10px 28px;
        background:linear-gradient(135deg,#1a1a24,#2e2e3c);
        color:#fff;border:none;border-radius:var(--r2);
        font-size:.8rem;font-weight:700;
        font-family:'DM Sans',sans-serif;
        cursor:pointer;transition:all .18s;
        box-shadow:0 3px 14px rgba(0,0,0,0.18);
        text-transform:uppercase;letter-spacing:.06em;
        margin-top:8px;
    }
    .btn-save:hover{box-shadow:0 5px 22px rgba(0,0,0,0.26);transform:translateY(-1px);}
    .btn-save:active{transform:translateY(0);}

    /* ── DIVIDER ── */
    .fdiv{height:1px;background:var(--b2);margin:20px 0;}

    @media(max-width:768px){
        .topbar{padding:0 16px;}
        .breadcrumb{display:none;}
        .container{padding:0 16px 60px;}
        .form-card{padding:20px;}
    }
    </style>
</head>
<body>

<header class="topbar">
    <div style="display:flex;align-items:center;gap:16px;">
        <a class="brand" href="index.php?tab=variants">
            <i class="fas fa-shield-halved"></i>
            <span class="brand-name">Porsche Admin</span>
        </a>
        <nav class="breadcrumb">
            <i class="fas fa-chevron-right"></i>
            <a href="index.php?tab=variants">Variants</a>
            <i class="fas fa-chevron-right"></i>
            <span><?= htmlspecialchars($variant['name']) ?></span>
            <i class="fas fa-chevron-right"></i>
            <span style="color:var(--t1)">Sound</span>
        </nav>
    </div>
    <div class="topbar-r">
        <a href="index.php?tab=variants" class="tpill">
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

    <div class="pg-hd">
        <h1>Sound Content</h1>
        <p>Configure the sound section for <strong><?= htmlspecialchars($variant['name']) ?></strong></p>
    </div>

    <div class="form-card">
        <form method="POST">
            <div class="form-group">
                <label>Title</label>
                <input type="text" name="title"
                    value="<?= htmlspecialchars($soundData['title'] ?? 'Set the pace: 9,000 revolutions per minute.') ?>"
                    required>
            </div>

            <div class="form-group">
                <label>Caption</label>
                <textarea name="caption" required><?= htmlspecialchars($soundData['caption'] ?? 'The naturally aspirated engine and sport exhaust system ensure an unfiltered sound experience.') ?></textarea>
            </div>

            <div class="fdiv"></div>

            <div class="form-group">
                <label>Background Image URL</label>
                <input type="text" name="background_image"
                    value="<?= htmlspecialchars($soundData['background_image'] ?? $variant['hero_bg_image'] ?? $variant['image'] ?? '') ?>"
                    placeholder="https://..."
                    oninput="updateBgPreview(this)">
                <?php
                $bgImg = $soundData['background_image'] ?? $variant['hero_bg_image'] ?? '';
                ?>
                <img id="bgPreview"
                     src="<?= htmlspecialchars($bgImg) ?>"
                     alt="Preview"
                     class="image-preview"
                     style="<?= $bgImg ? '' : 'display:none;' ?>">
            </div>

            <div class="form-group">
                <label>Button Text</label>
                <input type="text" name="button_text"
                    value="<?= htmlspecialchars($soundData['button_text'] ?? 'Hold for sound') ?>"
                    required>
            </div>

            <div class="form-group">
                <label>Audio URL</label>
                <input type="text" name="audio_url"
                    value="<?= htmlspecialchars($soundData['audio_url'] ?? $variant['model_audio'] ?? '') ?>"
                    placeholder="/lending_word/public/assets/audio/sound.mp3">
            </div>

            <button type="submit" name="save_sound" class="btn-save">
                <i class="fas fa-floppy-disk"></i>Save Sound Content
            </button>
        </form>
    </div>
</div>

<script>
function updateBgPreview(input) {
    const img = document.getElementById('bgPreview');
    if (input.value) {
        img.src = input.value;
        img.style.display = 'block';
    } else {
        img.style.display = 'none';
    }
}
</script>

</body>
</html>