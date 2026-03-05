<?php
session_start();
require_once __DIR__ . '/../app/models/ModelVariant.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$modelVariant = new ModelVariant();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $db = Database::getInstance()->getConnection();
        
        switch ($_POST['action']) {
            case 'add_variant':
                $stmt = $db->prepare("
                    INSERT INTO model_variants 
                    (category_id, name, variant_group, image, fuel_type, drive_type, transmission, 
                     acceleration, power_kw, power_ps, top_speed, body_design, seats, is_new, sort_order)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $_POST['category_id'], $_POST['name'], $_POST['variant_group'], $_POST['image'],
                    $_POST['fuel_type'], $_POST['drive_type'], $_POST['transmission'], $_POST['acceleration'],
                    $_POST['power_kw'], $_POST['power_ps'], $_POST['top_speed'], $_POST['body_design'],
                    $_POST['seats'], isset($_POST['is_new']) ? 1 : 0, $_POST['sort_order']
                ]);
                $success = "Model variant added successfully!";
                break;
                
            case 'delete_variant':
                $stmt = $db->prepare("DELETE FROM model_variants WHERE id = ?");
                $stmt->execute([$_POST['id']]);
                $success = "Model variant deleted successfully!";
                break;
        }
    }
}

$categories = $modelVariant->getCategories();
$allVariants = $modelVariant->getVariantsByCategory('all');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Model Variants - Admin</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 1400px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px; }
        h1 { margin-bottom: 30px; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, select, textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .btn { padding: 12px 24px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; }
        .btn-primary { background: #007bff; color: #fff; }
        .btn-danger { background: #dc3545; color: #fff; }
        .btn:hover { opacity: 0.9; }
        table { width: 100%; border-collapse: collapse; margin-top: 30px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; font-weight: bold; }
        .variant-img { width: 100px; height: auto; }
        .back-link { display: inline-block; margin-bottom: 20px; color: #007bff; text-decoration: none; }
        .back-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-link">← Back to Dashboard</a>
        
        <h1>Manage Model Variants</h1>
        
        <?php if (isset($success)): ?>
            <div class="success"><?= $success ?></div>
        <?php endif; ?>
        
        <h2>Add New Model Variant</h2>
        <form method="POST">
            <input type="hidden" name="action" value="add_variant">
            
            <div class="form-row">
                <div class="form-group">
                    <label>Category</label>
                    <select name="category_id" required>
                        <?php foreach ($categories as $cat): ?>
                            <?php if ($cat['slug'] !== 'all'): ?>
                                <option value="<?= $cat['id'] ?>"><?= $cat['name'] ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Model Name</label>
                    <input type="text" name="name" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Variant Group</label>
                    <input type="text" name="variant_group" placeholder="e.g., 911 Carrera Model variants">
                </div>
                
                <div class="form-group">
                    <label>Image URL</label>
                    <input type="text" name="image" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Fuel Type</label>
                    <input type="text" name="fuel_type" placeholder="e.g., Gasoline">
                </div>
                
                <div class="form-group">
                    <label>Drive Type</label>
                    <input type="text" name="drive_type" placeholder="e.g., Rear-Wheel Drive">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Transmission</label>
                    <input type="text" name="transmission" placeholder="e.g., Automatic">
                </div>
                
                <div class="form-group">
                    <label>Acceleration (0-100 km/h)</label>
                    <input type="text" name="acceleration" placeholder="e.g., 4.1 s">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Power (kW)</label>
                    <input type="number" name="power_kw" placeholder="e.g., 290">
                </div>
                
                <div class="form-group">
                    <label>Power (PS)</label>
                    <input type="number" name="power_ps" placeholder="e.g., 394">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Top Speed</label>
                    <input type="text" name="top_speed" placeholder="e.g., 294 km/h">
                </div>
                
                <div class="form-group">
                    <label>Body Design</label>
                    <input type="text" name="body_design" placeholder="e.g., Coupe">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Seats</label>
                    <input type="number" name="seats" placeholder="e.g., 4">
                </div>
                
                <div class="form-group">
                    <label>Sort Order</label>
                    <input type="number" name="sort_order" value="0">
                </div>
            </div>
            
            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_new" style="width: auto;">
                    Mark as New
                </label>
            </div>
            
            <button type="submit" class="btn btn-primary">Add Model Variant</button>
        </form>
        
        <h2 style="margin-top: 40px;">Existing Model Variants</h2>
        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Fuel</th>
                    <th>Drive</th>
                    <th>Power</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($allVariants as $variant): ?>
                <tr>
                    <td><img src="<?= $variant['image'] ?>" class="variant-img"></td>
                    <td><?= htmlspecialchars($variant['name']) ?></td>
                    <td><?= $variant['category_id'] ?></td>
                    <td><?= htmlspecialchars($variant['fuel_type']) ?></td>
                    <td><?= htmlspecialchars($variant['drive_type']) ?></td>
                    <td><?= $variant['power_kw'] ?> kW / <?= $variant['power_ps'] ?> PS</td>
                    <td>
                        <a href="specification.php?variant_id=<?= $variant['id'] ?>" class="btn btn-primary" style="text-decoration: none; display: inline-block; margin-right: 5px;">Specification</a>
                        <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this variant?')">
                            <input type="hidden" name="action" value="delete_variant">
                            <input type="hidden" name="id" value="<?= $variant['id'] ?>">
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
