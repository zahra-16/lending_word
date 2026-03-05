<?php
$isIndex  = (basename($_SERVER['PHP_SELF']) === 'index.php');
$isDetail = (basename($_SERVER['PHP_SELF']) === 'model-detail.php');

$isDarkBg    = $isIndex || $isDetail;
$navbarClass = $isIndex ? 'navbar' : 'navbar visible no-anim';
$logoFilter  = $isDarkBg ? 'brightness(0) invert(1)' : 'brightness(0)';

// Ambil navbar dari DB langsung
if (!isset($navbarLinks)) {
    try {
        $db = \Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT * FROM navbar_links ORDER BY sort_order ASC");
        $navbarLinks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // Fallback hardcode jika DB gagal
        $navbarLinks = [
            ['label' => 'Home',     'url' => '#hero'],
            ['label' => 'About',    'url' => '#about'],
            ['label' => 'Models',   'url' => '#models'],
            ['label' => 'Discover', 'url' => '#features'],
        ];
    }
}
?>
<nav class="<?= $navbarClass ?>" id="navbar">
    <div class="navbar-container">
        <a href="/lending_word/index.php" class="navbar-brand">
            <img src="/lending_word/public/assets/images/porsche-logo2-png_seeklogo-314112-removebg-preview.png" 
                 style="height: 80px; filter: <?= $logoFilter ?>;">
        </a>
        <ul class="navbar-menu <?= !$isIndex ? 'no-anim' : '' ?>">
            <?php foreach ($navbarLinks as $item): ?>
                <li>
                    <a href="/lending_word/index.php<?= htmlspecialchars($item['url']) ?>">
                        <?= htmlspecialchars($item['label']) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</nav>