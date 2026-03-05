<nav class="navbar">
    <div class="navbar-container">
        <a href="/lending_word/" class="navbar-brand">
            <img src="/lending_word/public/assets/images/porsche-logo2-png_seeklogo-314112-removebg-preview.png" alt="Porsche" style="height: 80px; filter: invert(1);">
        </a>
        <ul class="navbar-menu">
            <?php foreach ($navbarLinks as $link): ?>
                <li><a href="<?= htmlspecialchars($link['url']) ?>"><?= htmlspecialchars($link['label']) ?></a></li>
            <?php endforeach; ?>
        </ul>
    </div>
</nav>