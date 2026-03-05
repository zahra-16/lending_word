<!-- Footer -->
<footer>
    <div class="footer-container">
        <div class="footer-grid">
            <div class="footer-col">
                <h4><?= $getContent('footer', 'newsletter_title') ?></h4>
                <p><?= $getContent('footer', 'newsletter_desc') ?></p>
                <button class="footer-btn"><?= $getContent('footer', 'newsletter_button') ?></button>
            </div>
            
            <div class="footer-col">
                <h4><?= $getContent('footer', 'contact_title') ?></h4>
                <p><?= $getContent('footer', 'contact_desc') ?></p>
                <button class="footer-btn"><?= $getContent('footer', 'contact_button') ?></button>
            </div>
            
            <div class="footer-col">
                <h4><?= $getContent('footer', 'social_title') ?></h4>
                <p><?= $getContent('footer', 'social_desc') ?></p>
                <div class="social-links">
                    <?php foreach ($socialLinks as $social): ?>
                        <a href="<?= htmlspecialchars($social['url']) ?>" target="_blank" rel="noopener">
                            <i class="<?= htmlspecialchars($social['icon']) ?>"></i>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <div class="footer-sections">
            <?php foreach ($footerSections as $section): ?>
                <div class="footer-section">
                    <h5><?= htmlspecialchars($section['title']) ?></h5>
                    <ul>
                        <?php foreach ($section['links'] as $link): ?>
                            <li><a href="<?= htmlspecialchars($link['url']) ?>"><?= htmlspecialchars($link['label']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="footer-bottom">
            <p><?= $getContent('footer', 'copyright') ?></p>
            <p><?= $getContent('footer', 'bottom_text') ?></p>
        </div>
    </div>
</footer>
