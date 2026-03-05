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
    <title>Gallery - <?= htmlspecialchars($variant['name']) ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Porsche Next', -apple-system, sans-serif; background: #000; color: #fff; }
        .header { background: #0a0a0a; border-bottom: 1px solid rgba(255,255,255,0.1); padding: 25px 60px; display: flex; justify-content: space-between; align-items: center; }
        .header h1 { font-size: 1.5rem; font-weight: 300; letter-spacing: 1px; text-transform: uppercase; }
        .back-btn { background: transparent; color: white; padding: 10px 25px; text-decoration: none; border: 1px solid rgba(255,255,255,0.3); transition: 0.3s; font-weight: 300; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; }
        .back-btn:hover { background: white; color: #000; border-color: white; }
        .container { max-width: 1400px; margin: 40px auto; padding: 0 60px; }
        .success { background: rgba(0,255,0,0.1); color: #4ade80; padding: 15px 20px; margin-bottom: 30px; border: 1px solid rgba(0,255,0,0.2); }
        .add-form { background: #0a0a0a; padding: 40px; margin-bottom: 40px; border: 1px solid rgba(255,255,255,0.1); }
        .add-form h2 { font-size: 1.3rem; font-weight: 300; margin-bottom: 30px; text-transform: uppercase; letter-spacing: 2px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 10px; color: rgba(255,255,255,0.8); font-weight: 300; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px; }
        .form-group input { width: 100%; padding: 15px; border: 1px solid rgba(255,255,255,0.2); background: transparent; color: #fff; font-size: 0.95rem; font-family: inherit; }
        .form-group input:focus { outline: none; border-color: #fff; }
        .btn-save { background: #fff; color: #000; padding: 15px 50px; border: 1px solid #fff; font-size: 0.9rem; cursor: pointer; font-weight: 400; transition: 0.3s; text-transform: uppercase; letter-spacing: 2px; font-family: inherit; }
        .btn-save:hover { background: transparent; color: #fff; }
        .gallery-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 30px; }
        .gallery-item { background: #0a0a0a; border: 1px solid rgba(255,255,255,0.1); position: relative; }
        .gallery-item img { width: 100%; height: 250px; object-fit: cover; display: block; }
        .gallery-item p { color: rgba(255,255,255,0.6); font-size: 0.9rem; margin-bottom: 15px; }
        .btn-delete { background: transparent; color: #ff4444; border: 1px solid #ff4444; padding: 8px 20px; cursor: pointer; text-transform: uppercase; letter-spacing: 1px; font-size: 0.8rem; transition: 0.3s; font-family: inherit; }
        .btn-delete:hover { background: #ff4444; color: #fff; }
        .btn-update:hover { background: #4ade80; color: #000; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Gallery - <?= htmlspecialchars($variant['name']) ?></h1>
        <a href="/lending_word/admin/?tab=variants" class="back-btn">← Back to Variants</a>
    </div>

    <div class="container">
        <?php if ($success): ?>
            <div class="success">✓ <?= htmlspecialchars($success) ?></div>
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
                            <input type="number" name="sort_order[]" value="0">
                        </div>
                    </div>
                </div>
                <button type="button" onclick="addImageInput()" class="btn-save" style="background: transparent; color: #4ade80; border-color: #4ade80; margin-right: 10px;">+ Add More</button>
                <button type="submit" name="add_gallery" class="btn-save">Save All Images</button>
            </form>
        </div>

        <script>
        let imageCount = 1;
        function addImageInput() {
            imageCount++;
            const div = document.createElement('div');
            div.className = 'image-input-group';
            div.style.marginTop = '30px';
            div.style.paddingTop = '30px';
            div.style.borderTop = '1px solid rgba(255,255,255,0.1)';
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
                    <input type="number" name="sort_order[]" value="${imageCount - 1}">
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
                    <img src="<?= htmlspecialchars($image['image_url']) ?>" alt="<?= htmlspecialchars($image['caption']) ?>">
                    <div style="padding: 20px;">
                        <div class="form-group" style="margin-bottom: 15px;">
                            <label style="font-size: 0.8rem;">Image URL</label>
                            <input type="text" name="image_url" value="<?= htmlspecialchars($image['image_url']) ?>" required style="padding: 10px; font-size: 0.85rem;">
                        </div>
                        <div class="form-group" style="margin-bottom: 15px;">
                            <label style="font-size: 0.8rem;">Title</label>
                            <input type="text" name="title" value="<?= htmlspecialchars($image['title'] ?? '') ?>" style="padding: 10px; font-size: 0.85rem;">
                        </div>
                        <div class="form-group" style="margin-bottom: 15px;">
                            <label style="font-size: 0.8rem;">Section</label>
                            <input type="text" name="section" value="<?= htmlspecialchars($image['section'] ?? '') ?>" style="padding: 10px; font-size: 0.85rem;">
                        </div>
                        <div class="form-group" style="margin-bottom: 15px;">
                            <label style="font-size: 0.8rem;">Caption</label>
                            <input type="text" name="caption" value="<?= htmlspecialchars($image['caption']) ?>" style="padding: 10px; font-size: 0.85rem;">
                        </div>
                        <div class="form-group" style="margin-bottom: 15px;">
                            <label style="font-size: 0.8rem;">Sort Order</label>
                            <input type="number" name="sort_order" value="<?= $image['sort_order'] ?>" style="padding: 10px; font-size: 0.85rem; width: 100px;">
                        </div>
                        <button type="submit" name="update_gallery" class="btn-update" style="background: transparent; color: #4ade80; border: 1px solid #4ade80; padding: 8px 20px; cursor: pointer; text-transform: uppercase; letter-spacing: 1px; font-size: 0.8rem; transition: 0.3s; font-family: inherit; width: 48%; margin-right: 4%;">Update</button>
                        <button type="submit" name="delete_gallery" class="btn-delete" onclick="return confirm('Delete this image?')" style="width: 48%;">Delete</button>
                    </div>
                </form>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if (empty($galleryImages)): ?>
            <p style="text-align: center; color: rgba(255,255,255,0.5); padding: 60px 0;">No gallery images yet. Add your first image above.</p>
        <?php endif; ?>
    </div>
</body>
</html>
