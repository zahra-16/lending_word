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
    <title>Sound Content - <?= htmlspecialchars($variant['name']) ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Porsche Next', sans-serif; background: #000; color: #fff; }
        .header { background: #0a0a0a; padding: 25px 60px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .header h1 { font-size: 1.5rem; font-weight: 300; margin-bottom: 10px; }
        .header p { color: rgba(255,255,255,0.6); font-size: 0.9rem; }
        .container { max-width: 1200px; margin: 40px auto; padding: 0 60px; }
        .back-btn { display: inline-block; padding: 10px 25px; background: transparent; color: #fff; text-decoration: none; border: 1px solid rgba(255,255,255,0.3); margin-bottom: 30px; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px; }
        .back-btn:hover { background: #fff; color: #000; }
        .success { background: rgba(0,255,0,0.1); color: #4ade80; padding: 15px 20px; margin-bottom: 30px; border: 1px solid rgba(0,255,0,0.2); }
        .form-card { background: #0a0a0a; padding: 40px; border: 1px solid rgba(255,255,255,0.1); }
        .form-group { margin-bottom: 25px; }
        .form-group label { display: block; margin-bottom: 10px; color: rgba(255,255,255,0.8); font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px; }
        .form-group input, .form-group textarea { width: 100%; padding: 15px; background: transparent; border: 1px solid rgba(255,255,255,0.2); color: #fff; font-family: inherit; font-size: 0.95rem; }
        .form-group textarea { min-height: 100px; resize: vertical; }
        .form-group input:focus, .form-group textarea:focus { outline: none; border-color: #fff; }
        .image-preview { margin-top: 15px; max-width: 400px; border-radius: 8px; }
        .btn-save { background: #fff; color: #000; padding: 15px 50px; border: none; cursor: pointer; text-transform: uppercase; letter-spacing: 2px; font-size: 0.9rem; font-family: inherit; }
        .btn-save:hover { background: rgba(255,255,255,0.9); }
    </style>
</head>
<body>
    <div class="header">
        <h1>Sound Content</h1>
        <p><?= htmlspecialchars($variant['name']) ?></p>
    </div>

    <div class="container">
        <a href="index.php?tab=variants" class="back-btn">← Back to Variants</a>

        <?php if ($success): ?>
            <div class="success">✓ <?= $success ?></div>
        <?php endif; ?>

        <div class="form-card">
            <form method="POST">
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="title" value="<?= htmlspecialchars($soundData['title'] ?? 'Set the pace: 9,000 revolutions per minute.') ?>" required>
                </div>

                <div class="form-group">
                    <label>Caption</label>
                    <textarea name="caption" required><?= htmlspecialchars($soundData['caption'] ?? 'The naturally aspirated engine and sport exhaust system ensure an unfiltered sound experience.') ?></textarea>
                </div>

                <div class="form-group">
                    <label>Background Image URL</label>
                    <input type="text" name="background_image" value="<?= htmlspecialchars($soundData['background_image'] ?? $variant['hero_bg_image'] ?? $variant['image']) ?>">
                    <?php if (!empty($soundData['background_image']) || !empty($variant['hero_bg_image'])): ?>
                        <img src="<?= htmlspecialchars($soundData['background_image'] ?? $variant['hero_bg_image']) ?>" alt="Preview" class="image-preview">
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label>Button Text</label>
                    <input type="text" name="button_text" value="<?= htmlspecialchars($soundData['button_text'] ?? 'Hold for sound') ?>" required>
                </div>

                <div class="form-group">
                    <label>Audio URL</label>
                    <input type="text" name="audio_url" value="<?= htmlspecialchars($soundData['audio_url'] ?? $variant['model_audio'] ?? '') ?>" placeholder="/lending_word/public/assets/audio/sound.mp3">
                </div>

                <button type="submit" name="save_sound" class="btn-save">Save Sound Content</button>
            </form>
        </div>
    </div>
</body>
</html>
