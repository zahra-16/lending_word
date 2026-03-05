<?php
session_start();
require_once __DIR__ . '/../../models/ModelVariant.php';
require_once __DIR__ . '/../../models/ConfiguratorColor.php';
require_once __DIR__ . '/../../models/ConfiguratorWheel.php';

$id = $_GET['id'] ?? 0;

$modelVariant = new ModelVariant();
$variant = $modelVariant->getById($id);

if (!$variant) {
    header('Location: models.php');
    exit;
}

$colorModel = new ConfiguratorColor();
$wheelModel = new ConfiguratorWheel();

$colors = $colorModel->getAll();
$wheels = $wheelModel->getAll();

// Debug
if (empty($colors)) {
    echo "<!-- DEBUG: No colors found. Please import configurator_setup.sql -->";
}

$basePrice = 2000000000; // 2 Miliar base price
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configure <?= htmlspecialchars($variant['name']) ?> - Porsche</title>
    <link rel="stylesheet" href="/lending_word/public/assets/css/style.css">
    <style>
        body { background: #fff; color: #000; font-family: 'Porsche Next', sans-serif; margin: 0; }
        
        .navbar { background: rgba(0,0,0,0.9) !important; }
        .navbar-brand { color: #fff !important; filter: invert(1) !important; }
        .navbar-menu a { color: #fff !important; }
        
        .config-container { display: flex; min-height: 100vh; padding-top: 80px; }
        
        .config-preview { flex: 1; background: #f5f5f5; display: flex; align-items: center; justify-content: center; padding: 40px; position: sticky; top: 80px; height: calc(100vh - 80px); }
        .car-wrapper { position: relative; max-width: 100%; }
        .config-preview img { max-width: 100%; height: auto; display: block; }
        .color-overlay { position: absolute; top: 0; left: 0; right: 0; bottom: 0; pointer-events: none; mix-blend-mode: multiply; opacity: 0; transition: all 0.5s ease; }
        
        .config-options { width: 450px; background: #fff; padding: 40px; overflow-y: auto; }
        
        .config-header { margin-bottom: 40px; }
        .config-header h1 { font-size: 2rem; font-weight: 400; margin-bottom: 10px; }
        .config-header .year { color: #666; font-size: 0.9rem; }
        
        .option-section { margin-bottom: 50px; }
        .option-section h2 { font-size: 1.2rem; font-weight: 500; margin-bottom: 20px; text-transform: uppercase; letter-spacing: 1px; }
        
        .color-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; }
        .color-option { width: 60px; height: 60px; border-radius: 50%; cursor: pointer; border: 3px solid transparent; transition: 0.3s; position: relative; }
        .color-option:hover { transform: scale(1.1); }
        .color-option.selected { border-color: #000; box-shadow: 0 0 0 2px #fff, 0 0 0 4px #000; }
        .color-option .price { position: absolute; bottom: -25px; left: 50%; transform: translateX(-50%); font-size: 0.7rem; white-space: nowrap; color: #666; }
        
        .wheel-list { display: flex; flex-direction: column; gap: 15px; }
        .wheel-option { border: 2px solid #e5e5e5; padding: 15px; border-radius: 8px; cursor: pointer; transition: 0.3s; display: flex; justify-content: space-between; align-items: center; }
        .wheel-option:hover { border-color: #000; }
        .wheel-option.selected { border-color: #000; background: #f5f5f5; }
        .wheel-option .name { font-weight: 500; }
        .wheel-option .size { font-size: 0.85rem; color: #666; }
        .wheel-option .price { font-weight: 500; }
        
        .price-summary { position: sticky; bottom: 0; background: #000; color: #fff; padding: 30px; margin: -40px; margin-top: 40px; }
        .price-summary .total { font-size: 1.5rem; font-weight: 500; margin-bottom: 20px; }
        .price-summary .breakdown { font-size: 0.9rem; margin-bottom: 20px; opacity: 0.8; }
        .price-summary .breakdown div { display: flex; justify-content: space-between; margin-bottom: 5px; }
        .btn-save { width: 100%; padding: 15px; background: #fff; color: #000; border: none; font-size: 1rem; text-transform: uppercase; letter-spacing: 1px; cursor: pointer; font-weight: 500; }
        .btn-save:hover { background: #f5f5f5; }
    </style>
</head>
<body>

<?php include __DIR__ . '/../partials/navbar.php'; ?>

<div class="config-container">
    <div class="config-preview">
        <div class="car-wrapper">
            <img id="carImage" src="<?= htmlspecialchars($variant['image']) ?>" alt="<?= htmlspecialchars($variant['name']) ?>">
            <div class="color-overlay" id="colorOverlay"></div>
        </div>
    </div>
    
    <div class="config-options">
        <div class="config-header">
            <h1><?= htmlspecialchars($variant['name']) ?></h1>
            <div class="year">2025</div>
        </div>
        
        <div class="option-section">
            <h2>Exterior Colours</h2>
            <div class="color-grid">
                <?php foreach ($colors as $color): ?>
                <div class="color-option" 
                     data-id="<?= $color['id'] ?>" 
                     data-price="<?= $color['price'] ?>"
                     data-hex="<?= $color['hex_code'] ?>"
                     style="background: <?= $color['hex_code'] ?>; <?= $color['hex_code'] === '#FFFFFF' ? 'border: 1px solid #ddd;' : '' ?>"
                     onclick="selectColor(this)">
                    <?php if ($color['price'] > 0): ?>
                    <div class="price">+Rp <?= number_format($color['price'], 0, ',', '.') ?></div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="option-section">
            <h2>Wheels</h2>
            <div class="wheel-list">
                <?php foreach ($wheels as $wheel): ?>
                <div class="wheel-option" 
                     data-id="<?= $wheel['id'] ?>" 
                     data-price="<?= $wheel['price'] ?>"
                     onclick="selectWheel(this)">
                    <div>
                        <div class="name"><?= htmlspecialchars($wheel['name']) ?></div>
                        <div class="size"><?= htmlspecialchars($wheel['size']) ?></div>
                    </div>
                    <div class="price">
                        <?= $wheel['price'] > 0 ? '+Rp ' . number_format($wheel['price'], 0, ',', '.') : 'Standard' ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="price-summary">
            <div class="breakdown">
                <div><span>Base Price</span><span id="basePrice">Rp <?= number_format($basePrice, 0, ',', '.') ?></span></div>
                <div><span>Color</span><span id="colorPrice">Rp 0</span></div>
                <div><span>Wheels</span><span id="wheelPrice">Rp 0</span></div>
            </div>
            <div class="total">
                Total: <span id="totalPrice">Rp <?= number_format($basePrice, 0, ',', '.') ?></span>
            </div>
            <button class="btn-save" onclick="saveConfiguration()">Save Configuration</button>
        </div>
    </div>
</div>

<script>
const basePrice = <?= $basePrice ?>;
let selectedColor = null;
let selectedWheel = null;

function selectColor(el) {
    document.querySelectorAll('.color-option').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    selectedColor = {
        id: el.dataset.id,
        price: parseFloat(el.dataset.price),
        hex: el.dataset.hex
    };
    applyColorOverlay(selectedColor.hex);
    updatePrice();
}

function applyColorOverlay(hexColor) {
    const overlay = document.getElementById('colorOverlay');
    
    // Special handling for white and light colors
    if (hexColor === '#FFFFFF' || hexColor === '#F0F0F0') {
        overlay.style.background = 'transparent';
        overlay.style.opacity = '0';
    } else {
        overlay.style.background = hexColor;
        overlay.style.opacity = '0.4';
    }
}

function selectWheel(el) {
    document.querySelectorAll('.wheel-option').forEach(w => w.classList.remove('selected'));
    el.classList.add('selected');
    selectedWheel = {
        id: el.dataset.id,
        price: parseFloat(el.dataset.price)
    };
    updatePrice();
}

function updatePrice() {
    const colorPrice = selectedColor ? selectedColor.price : 0;
    const wheelPrice = selectedWheel ? selectedWheel.price : 0;
    const total = basePrice + colorPrice + wheelPrice;
    
    document.getElementById('colorPrice').textContent = 'Rp ' + colorPrice.toLocaleString('id-ID');
    document.getElementById('wheelPrice').textContent = 'Rp ' + wheelPrice.toLocaleString('id-ID');
    document.getElementById('totalPrice').textContent = 'Rp ' + total.toLocaleString('id-ID');
}

function saveConfiguration() {
    if (!selectedColor || !selectedWheel) {
        alert('Please select color and wheels');
        return;
    }
    alert('Configuration saved! Total: Rp ' + (basePrice + selectedColor.price + selectedWheel.price).toLocaleString('id-ID'));
}

// Auto select first options
document.querySelector('.color-option').click();
document.querySelector('.wheel-option').click();
</script>

</body>
</html>
