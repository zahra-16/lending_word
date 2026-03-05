<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Porsche Indonesia</title>
    <link rel="icon" type="image/png" href="/lending_word/public/assets/images/porsche-logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/lending_word/public/assets/css/style.css">
    
</head>

<body>
    <?php include 'porsche-chat-widget.php'; ?>


<!-- Custom Cursor -->
<div id="cursor-dot"></div>
<div id="cursor-ring"></div>

<!-- Particle Canvas -->
<canvas id="particle-canvas"></canvas>

<!-- Scroll Progress Ring -->
<div id="scroll-ring">
    <svg viewBox="0 0 48 48" width="48" height="48">
        <circle class="track" cx="24" cy="24" r="20"/>
        <circle class="prog" id="scroll-prog" cx="24" cy="24" r="20"/>
    </svg>
    <button id="scroll-ring-btn" onclick="window.scrollTo({top:0,behavior:'smooth'})">
        <i class="fas fa-arrow-up"></i>
    </button>
</div>

<!-- Page Transition Overlay -->
<div class="page-transition-overlay"></div>

<!-- Cinematic Intro Loader -->
<div id="intro">
    <div class="c-panel l"></div>
    <div id="intro-logo">
        <img src="/lending_word/public/assets/images/porsche-logo.png" alt="Porsche">
        <span id="intro-tagline">Porsche Indonesia</span>
    </div>
    <div class="c-panel r"></div>
    <span id="intro-counter">000</span>
    <span id="intro-label">Initializing</span>
    <div id="intro-progress"></div>
</div>

<!-- Progress Bar -->
<div id="progress"></div>

<!-- Navbar -->
<nav class="navbar" id="navbar">
    <div class="navbar-container">
        <a href="/lending_word/" class="navbar-brand">
            <img src="/lending_word/public/assets/images/porsche-logo2-png_seeklogo-314112-removebg-preview.png" style="height: 80px; filter: brightness(0) invert(1);">
        </a>
        <ul class="navbar-menu">
            <?php foreach ($navbarLinks as $item): ?>
                <li>
                    <a href="/lending_word/<?= htmlspecialchars($item['url']) ?>">
                        <?= htmlspecialchars($item['label']) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</nav>

<!-- Hero Section -->
<section class="hero" id="hero">
    <?php
    $heroImage  = $getContent('hero', 'image');
    $isVideo    = preg_match('/\.(mp4|webm|ogg)$/i', $heroImage);
    $isYouTube  = preg_match('/(youtube\.com|youtu\.be)/', $heroImage);

    if ($isYouTube):
        preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i', $heroImage, $match);
        $videoId = $match[1] ?? '';
    ?>
        <iframe class="hero-video youtube-embed" id="heroYoutubeIframe"
                src="https://www.youtube.com/embed/<?= $videoId ?>?autoplay=1&mute=1&loop=1&playlist=<?= $videoId ?>&controls=0&showinfo=0&rel=0&modestbranding=1&iv_load_policy=3&disablekb=1&fs=0&enablejsapi=1"
                frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
    <?php elseif ($isVideo): ?>
        <video class="hero-video" id="heroVideoEl" autoplay muted loop playsinline>
            <source src="<?= $heroImage ?>" type="video/<?= pathinfo($heroImage, PATHINFO_EXTENSION) ?>">
        </video>
    <?php else: ?>
        <div class="hero-bg-static" style="background-image: url('<?= $heroImage ?>');" id="heroBgStatic"></div>
    <?php endif; ?>

    <div class="hero-content">
        <?php $title = $getContent('hero', 'title'); ?>
        <h1>
            <span class="hero-line-wrap">
                <span class="hero-line"><?= trim($title) ?></span>
            </span>
        </h1>
        <p><?= $getContent('hero', 'subtitle') ?></p>
        <a href="http://localhost/lending_word/app/views/frontend/model-detail.php?id=66" class="btn btn-primary">
            <span><?= $getContent('hero', 'button_text') ?></span>
        </a>
    </div>

    <div class="hero-scroll-hint">
        <span class="hero-scroll-hint-text">Scroll</span>
        <div class="hero-scroll-hint-wheel"></div>
    </div>

    <button id="heroVideoBtn" class="hero-video-btn" aria-label="Pause video">
        <i class="fas fa-pause" id="heroVideoBtnIcon"></i>
    </button>
</section>

<div class="divider"></div>

<!-- Marquee Strip -->
<div class="marquee-strip">
    <div class="marquee-track">
        <?php
        $items = [
            'Porsche Indonesia', 'Performance', 'Precision Engineering',
            'Since 1948', 'Driven by Dreams', 'The Art of Speed'
        ];
        for ($set = 0; $set < 2; $set++):
            foreach ($items as $item): ?>
                <span class="marquee-item"><?= htmlspecialchars($item) ?><span class="marquee-diamond"></span></span>
            <?php endforeach;
        endfor; ?>
    </div>
</div>

<!-- About Section — Hero image + CTA card -->
<section class="about" id="about">
    <!-- Blok 1: Fullwidth image dengan judul overlay -->
    <div class="about-headline"
         style="--about-bg: url('<?= $getContent('about', 'image') ?>');">
        <h2 id="aboutHeadline"><?= $getContent('about', 'title') ?></h2>
    </div>

    <!-- Blok 2: CTA card dari database -->
    <div class="about-cta-section">
        <div class="about-cta-card" id="aboutBody">
            <h3><?= $getContent('cta', 'title') ?></h3>
            <p><?= $getContent('cta', 'description') ?></p>
            <a href="http://localhost/lending_word/finder.php?series_id=2" class="about-cta-card-btn">
                <?= $getContent('cta', 'button_text') ?>
            </a>
            <span class="about-cta-card-note">See detailed specifications, photos, and availability</span>
        </div>
    </div>
</section>

<!-- Featured Vehicles Section -->
<?php if (!empty($featuredVehicles)): ?>
<section class="featured-vehicles" id="featured">
    <div class="fv-inner">
        <!-- <h2 class="fv-heading reveal delay-1">Popular &amp; New Models</h2>
        <p class="fv-sub reveal delay-2">Discover our most sought-after and latest arrivals</p> -->

        <div class="fv-grid">
            <?php foreach ($featuredVehicles as $i => $v):
                if (!empty($v['link'])) {
                    $fvLink = htmlspecialchars($v['link']);
                } elseif (!empty($v['model_variant_id'])) {
                    $fvLink = '/lending_word/app/views/frontend/model-detail.php?id=' . (int)$v['model_variant_id'];
                } else {
                    $fvLink = '#';
                }
            ?>
            <a href="<?= $fvLink ?>" class="fv-card reveal delay-<?= min($i + 1, 5) ?>">
                <div class="fv-img">
                    <img src="<?= htmlspecialchars($v['image']) ?>"
                         alt="<?= htmlspecialchars($v['name']) ?>"
                         loading="eager"
                         onerror="this.src='https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=800';this.onerror=null;">
                </div>
                <div class="fv-info">
                    <h3 class="fv-name"><?= htmlspecialchars($v['name']) ?></h3>
                    <span class="fv-arrow"><i class="fas fa-arrow-right"></i></span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php endif; ?>



<!-- Models Section -->
<section class="section models" id="models">
    <div class="models-wrapper">
        <div class="models-image">
            <img id="modelImage" src="<?= $models[0]['image'] ?>" alt="<?= $models[0]['name'] ?>">
        </div>
        <div class="models-info">
            <h2 id="modelName"><?= $models[0]['name'] ?></h2>
            <div class="fuel-types" id="modelFuel">
                <?php foreach (explode(',', $models[0]['fuel_types']) as $fuel): ?>
                    <span class="fuel-tag"><?= trim($fuel) ?></span>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="models-tabs">
            <?php foreach ($models as $index => $model): ?>
                <button class="model-tab <?= $index === 0 ? 'active' : '' ?>"
                    onclick="changeModel('<?= $model['image'] ?>','<?= $model['name'] ?>','<?= $model['fuel_types'] ?>',this)">
                    <?= $model['name'] ?>
                </button>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Explore Models Section -->
<section class="explore-models" id="explore">
    <h2 class="reveal delay-1"><?= $getContent('explore_models', 'title') ?></h2>

    <?php
    $chunks = array_chunk($exploreModels, 2);
    foreach ($chunks as $rowIdx => $row): ?>
        <div class="explore-row">
            <?php foreach ($row as $colIdx => $model): ?>
                <div class="explore-card reveal delay-<?= ($rowIdx + $colIdx + 1) ?>">
                    <div class="explore-card-inner">
                        <div class="explore-card-image">
                            <img src="<?= $model['image'] ?>" alt="<?= $model['name'] ?>"
                                onerror="this.src='https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=1200'; this.onerror=null;">
                        </div>
                        <div class="explore-card-content">
                            <?php
                            $logoMap = [
                                '911'      => '911',
                                '718'      => '718',
                                'cayenne'  => 'cayenne',
                                'macan'    => 'macan',
                                'panamera' => 'panamera',
                                'taycan'   => 'taycan',
                            ];
                            $logoSlug = $logoMap[strtolower($model['name'])] ?? strtolower($model['name']);
                            ?>
                            <div class="explore-card-title">
                                <img src="/lending_word/public/assets/images/model-logos/<?= $logoSlug ?>.svg"
                                     alt="<?= $model['name'] ?>"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                <span style="display:none; font-size:clamp(2.5rem,4vw,3.8rem); font-weight:800; font-style:italic; color:#fff; font-family:var(--font);"><?= $model['name'] ?></span>
                            </div>
                            <div class="explore-card-bottom">
                                <div>
                                    <div class="explore-card-badges">
                                        <?php foreach (explode(',', $model['fuel_types']) as $fuel): ?>
                                            <span class="explore-badge"><?= trim($fuel) ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                    <p class="explore-card-desc"><?= $model['description'] ?> <?= $model['doors'] ?> • <?= $model['seats'] ?></p>
                                </div>
                                <?php $categorySlug = strtolower($model['name']); ?>
                                <a href="/lending_word/models.php?category=<?= $categorySlug ?>" class="explore-card-btn">Explore →</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
</section>

<!-- Inventory Section -->
<section class="section inventory" id="inventory">
    <div class="inventory-card">
        <div class="inventory-text reveal-left">
            <h2><?= $getContent('inventory', 'title') ?></h2>
            <p><?= $getContent('inventory', 'description') ?></p>
            <a href="/lending_word/finder.php" class="btn btn-primary">
                <span><?= $getContent('inventory', 'button_text') ?></span>
            </a>
        </div>
        <div class="inventory-image reveal-right">
            <img src="<?= $getContent('inventory', 'image') ?>" alt="Porsche Inventory">
        </div>
    </div>
</section>

<!-- Discover Section -->
<section class="section discover" id="features">
    <h2 class="discover-title-main reveal delay-1">Discover</h2>
    <div class="discover-grid">
        <?php foreach ($discoverFeatures as $idx => $feature): ?>
            <a href="/lending_word/discover-detail.php?slug=<?= urlencode($feature['slug'] ?? '') ?>"
   class="discover-card reveal delay-<?= min($idx+1,5) ?>">
                <div class="discover-image">
                    <img src="<?= htmlspecialchars($feature['image']) ?>"
                         alt="<?= htmlspecialchars($feature['title']) ?>">
                </div>
                <div class="discover-content">
                    <?php if (!empty($feature['category'])): ?>
                    <span class="discover-desc"><?= htmlspecialchars($feature['category']) ?></span>
                    <?php endif; ?>
                    <h3 class="discover-title"><?= htmlspecialchars($feature['title']) ?></h3>
                    <span class="discover-arrow">Explore <i class="fas fa-arrow-right"></i></span>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<div class="divider"></div>

<?php include __DIR__ . '/../partials/footer.php'; ?>


<script>
(function () {
'use strict';

/* ─── 1. CINEMATIC LOADER ─── */
const intro        = document.getElementById('intro');
const introCounter = document.getElementById('intro-counter');
const introProg    = document.getElementById('intro-progress');
const navbar       = document.getElementById('navbar');

function animateLoader() {
    const dur = 1400, start = performance.now();
    (function tick(now) {
        const p    = Math.min((now - start) / dur, 1);
        const ease = 1 - Math.pow(1 - p, 3);
        const pct  = Math.round(ease * 100);
        if (introCounter) introCounter.textContent = String(pct).padStart(3, '0');
        if (introProg)    introProg.style.width = pct + '%';
        if (p < 1) requestAnimationFrame(tick);
        else openIntro();
    })(start);
}

function openIntro() {
    intro.classList.add('open');
    setTimeout(() => {
        intro.classList.add('done');
        navbar.classList.add('visible');
        const heroBg = document.getElementById('heroBgStatic');
        if (heroBg) setTimeout(() => heroBg.classList.add('loaded'), 100);
        setTimeout(() => {
            document.querySelectorAll('.hero-line').forEach(el => el.classList.add('revealed'));
        }, 200);
        setTimeout(() => { intro.style.display = 'none'; }, 600);
    }, 1300);
}
animateLoader();

/* ─── 2. PRECISION CURSOR ─── */
(function () {
    const dot  = document.getElementById('cursor-dot');
    const ring = document.getElementById('cursor-ring');
    if (!dot || !ring) return;

    let mx = 0, my = 0, rx = 0, ry = 0;
    let started = false;

    dot.classList.add('hidden');
    ring.classList.add('hidden');

    /* Continuous RAF loop — sama persis seperti models.php */
    (function tick() {
        rx += (mx - rx) * 0.16;
        ry += (my - ry) * 0.16;
        dot.style.left  = mx + 'px';
        dot.style.top   = my + 'px';
        ring.style.left = rx + 'px';
        ring.style.top  = ry + 'px';
        requestAnimationFrame(tick);
    })();

    window.addEventListener('mousemove', (e) => {
        mx = e.clientX;
        my = e.clientY;
        if (!started) {
            started = true;
            rx = mx; ry = my;
            dot.classList.remove('hidden');
            ring.classList.remove('hidden');
        }
    }, { passive: true });

    document.addEventListener('mousedown', () => {
        document.body.classList.add('c-click');
        setTimeout(() => document.body.classList.remove('c-click'), 280);
    });

    document.documentElement.addEventListener('mouseleave', () => {
        dot.classList.add('hidden');
        ring.classList.add('hidden');
    });
    document.documentElement.addEventListener('mouseenter', () => {
        dot.classList.remove('hidden');
        ring.classList.remove('hidden');
    });

    document.querySelectorAll(
        'a, button, .model-tab, .footer-btn, .explore-card, .discover-card, .fv-card, .about-cta-card-btn'
    ).forEach(el => {
        el.addEventListener('mouseenter', () => document.body.classList.add('c-link'));
        el.addEventListener('mouseleave', () => document.body.classList.remove('c-link'));
    });

    document.querySelectorAll(
        '.models, .featured-vehicles, .about-cta-section, .discover, .inventory'
    ).forEach(el => {
        el.addEventListener('mouseenter', () => document.body.classList.add('c-dark'));
        el.addEventListener('mouseleave', () => document.body.classList.remove('c-dark'));
    });
})();

/* ─── 3. PROGRESS BAR + NAVBAR + SCROLL RING ─── */
const progressEl = document.getElementById('progress');
const scrollRing = document.getElementById('scroll-ring');
const scrollProg = document.getElementById('scroll-prog');
const CIRCUMF    = 2 * Math.PI * 20;
window.addEventListener('scroll', () => {
    const maxY = document.body.scrollHeight - window.innerHeight;
    const pct  = maxY > 0 ? window.scrollY / maxY : 0;
    if (progressEl) progressEl.style.width = (pct * 100) + '%';
    navbar.classList.toggle('scrolled', window.scrollY > 60);
    if (scrollRing) {
        scrollRing.classList.toggle('visible', window.scrollY > 400);
        if (scrollProg) scrollProg.style.strokeDashoffset = CIRCUMF * (1 - pct);
    }
}, { passive: true });

/* ─── 4. SCROLL REVEAL ─── */
const revealObs = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('revealed');
            revealObs.unobserve(entry.target);
        }
    });
}, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });
document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-scale, .section-tag, .about-stat, .explore-models h2, .explore-models-subtitle').forEach(el => revealObs.observe(el));

/* ─── 5. COUNT-UP ─── */
const countObs = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (!entry.isIntersecting) return;
        const el = entry.target;
        const target = parseInt(el.dataset.target || '0', 10);
        const dur = 2000, start = performance.now();
        (function update(now) {
            const t = Math.min((now - start) / dur, 1);
            const ease = 1 - Math.pow(1 - t, 4);
            el.textContent = Math.round(ease * target);
            if (t < 1) { requestAnimationFrame(update); }
            else { el.textContent = target; el.classList.add('pop'); setTimeout(() => el.classList.remove('pop'), 300); }
        })(start);
        countObs.unobserve(el);
    });
}, { threshold: 0.6 });
document.querySelectorAll('.count-up').forEach(el => countObs.observe(el));

/* ─── 6. MAGNETIC BUTTONS ─── */
document.querySelectorAll('.btn, .model-tab').forEach(el => {
    el.addEventListener('mousemove', e => {
        const r  = el.getBoundingClientRect();
        const dx = (e.clientX - (r.left + r.width  / 2)) * 0.28;
        const dy = (e.clientY - (r.top  + r.height / 2)) * 0.28;
        el.style.transform = `translate(${dx}px, ${dy}px)`;
    });
    el.addEventListener('mouseleave', () => {
        el.style.transition = 'transform .5s cubic-bezier(0.34,1.56,0.64,1)';
        el.style.transform  = '';
        setTimeout(() => el.style.transition = '', 500);
    });
});

/* ─── 7. HERO SPOTLIGHT ─── */
const hero = document.querySelector('.hero');
if (hero) {
    window.addEventListener('mousemove', e => {
        const r = hero.getBoundingClientRect();
        if (e.clientY > r.top && e.clientY < r.bottom) {
            hero.style.setProperty('--mx', ((e.clientX - r.left) / r.width  * 100).toFixed(1) + '%');
            hero.style.setProperty('--my', ((e.clientY - r.top)  / r.height * 100).toFixed(1) + '%');
        }
    }, { passive: true });
}

/* ─── 8. MODEL TAB SWITCH ─── */
window.changeModel = function(image, name, fuels, tabEl) {
    const img      = document.getElementById('modelImage');
    const nameEl   = document.getElementById('modelName');
    const fuelWrap = document.getElementById('modelFuel');
    if (img) {
        img.classList.add('switching');
        setTimeout(() => {
            img.src = image;
            img.onload = () => {
                img.classList.remove('switching');
                img.classList.add('entering');
                setTimeout(() => img.classList.remove('entering'), 600);
            };
        }, 250);
    }
    if (nameEl) {
        nameEl.classList.add('switching');
        setTimeout(() => { nameEl.textContent = name; nameEl.classList.remove('switching'); }, 250);
    }
    if (fuelWrap) {
        fuelWrap.style.opacity = '0'; fuelWrap.style.transform = 'translateY(-6px)';
        setTimeout(() => {
            fuelWrap.innerHTML = '';
            fuels.split(',').forEach((fuel, i) => {
                const s = document.createElement('span');
                s.className = 'fuel-tag'; s.textContent = fuel.trim();
                s.style.animationDelay = (i * 0.07) + 's';
                fuelWrap.appendChild(s);
            });
            fuelWrap.style.transition = 'opacity .4s ease, transform .4s cubic-bezier(0.16,1,0.3,1)';
            fuelWrap.style.opacity = '1'; fuelWrap.style.transform = 'translateY(0)';
        }, 220);
    }
    document.querySelectorAll('.model-tab').forEach(t => t.classList.remove('active'));
    tabEl.classList.add('active');
};

/* ─── 9. EXPLORE CARD HOVER ─── */
document.querySelectorAll('.explore-row').forEach(row => {
    const cards = row.querySelectorAll('.explore-card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', () => {
            cards.forEach(c => {
                if (c === card) { c.style.transition = 'flex 0.5s cubic-bezier(0.25,0.46,0.45,0.94),opacity 0.4s ease'; c.style.flex = '1.15'; c.style.opacity = '1'; }
                else { c.style.transition = 'flex 0.5s cubic-bezier(0.25,0.46,0.45,0.94),opacity 0.4s ease'; c.style.flex = '0.9'; c.style.opacity = '0.75'; }
            });
        });
        card.addEventListener('mouseleave', () => {
            cards.forEach(c => { c.style.transition = 'flex 0.45s cubic-bezier(0.25,0.46,0.45,0.94),opacity 0.35s ease'; c.style.flex = '1'; c.style.opacity = '1'; });
        });
    });
});

/* ─── RIPPLE pada fv-card ─── */
document.querySelectorAll('.fv-card').forEach(card => {
    const rc = document.createElement('div');
    rc.className = 'ripple-container';
    card.appendChild(rc);
    card.addEventListener('mousedown', e => {
        const r = card.getBoundingClientRect();
        const x = e.clientX - r.left, y = e.clientY - r.top;
        const size = Math.max(r.width, r.height) * 0.55;
        const el = document.createElement('div');
        el.className = 'ripple-circle';
        el.style.cssText = `width:${size}px;height:${size}px;left:${x-size/2}px;top:${y-size/2}px;`;
        rc.appendChild(el);
        setTimeout(() => el.remove(), 900);
    });
});

/* ─── SPOTLIGHT pada discover-card ─── */
document.querySelectorAll('.discover-card').forEach(card => {
    card.addEventListener('mousemove', e => {
        const r = card.getBoundingClientRect();
        card.style.setProperty('--dx', ((e.clientX - r.left) / r.width  * 100) + '%');
        card.style.setProperty('--dy', ((e.clientY - r.top)  / r.height * 100) + '%');
    });
});

/* ─── 3D TILT pada fv-card ─── */
document.querySelectorAll('.fv-card').forEach(card => {
    card.addEventListener('mouseenter', () => { card.style.willChange = 'transform'; });
    card.addEventListener('mousemove', e => {
        const r = card.getBoundingClientRect();
        const x = (e.clientX - r.left) / r.width  - .5;
        const y = (e.clientY - r.top)  / r.height - .5;
        card.style.transform = `translateY(-10px) scale(1.015) rotateX(${-y*7}deg) rotateY(${x*7}deg)`;
        card.style.transformOrigin = 'center center';
    });
    card.addEventListener('mouseleave', () => { card.style.transform = ''; card.style.transformOrigin = ''; card.style.willChange = 'auto'; });
});

/* ─── 10. PARALLAX ON SCROLL ─── */
let rafPending = false;
window.addEventListener('scroll', () => {
    if (rafPending) return;
    rafPending = true;
    requestAnimationFrame(() => {
        const scrollY = window.scrollY;
        const hc = document.querySelector('.hero-content');
        if (hc) hc.style.transform = `translateY(${scrollY * 0.14}px)`;
        const ai = document.querySelector('.about-image-inner img');
        if (ai) {
            const sec = document.querySelector('.about');
            if (sec) { const off = -sec.getBoundingClientRect().top * 0.07; ai.style.transform = `scale(1.05) translateY(${off}px)`; }
        }
        document.querySelectorAll('.discover-card').forEach((card, i) => {
            if (card.matches(':hover')) return;
            const mid  = card.getBoundingClientRect().top + card.offsetHeight / 2 - window.innerHeight / 2;
            const tilt = Math.min(Math.max(mid * 0.007 * (i % 2 === 0 ? 1 : -1), -5), 5);
            card.style.transform = `translateY(${tilt}px)`;
        });
        rafPending = false;
    });
}, { passive: true });

/* ─── 11. SMOOTH SCROLL ANCHOR ─── */
document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
        const id = a.getAttribute('href');
        if (id === '#') return;
        const target = document.querySelector(id);
        if (!target) return;
        e.preventDefault();
        const top = target.getBoundingClientRect().top + window.scrollY - 80;
        window.scrollTo({ top, behavior: 'smooth' });
    });
});

/* ─── 12. PAGE TRANSITION ─── */
const overlay = document.querySelector('.page-transition-overlay');
if (overlay) {
    document.querySelectorAll('a[href]').forEach(link => {
        const href = link.getAttribute('href') || '';
        if (href.startsWith('#') || link.target === '_blank' || href.startsWith('mailto') || href.startsWith('tel') || href.startsWith('javascript') || href.includes('#')) return;
        link.addEventListener('click', e => {
            e.preventDefault();
            overlay.classList.add('entering');
            setTimeout(() => { window.location.href = href; }, 480);
        });
    });
    window.addEventListener('pageshow', () => {
        overlay.classList.remove('entering');
        overlay.classList.add('leaving');
        setTimeout(() => overlay.classList.remove('leaving'), 450);
    });
}

/* ─── 13. HERO VIDEO PAUSE/PLAY ─── */
const videoBtn     = document.getElementById('heroVideoBtn');
const videoBtnIcon = document.getElementById('heroVideoBtnIcon');
const heroVideoEl  = document.getElementById('heroVideoEl');
if (videoBtn) {
    let isPaused = false;
    videoBtn.addEventListener('click', () => {
        isPaused = !isPaused;
        if (heroVideoEl) { isPaused ? heroVideoEl.pause() : heroVideoEl.play(); }
        const ytIframe = document.getElementById('heroYoutubeIframe');
        if (ytIframe) {
            const msg = isPaused ? '{"event":"command","func":"pauseVideo","args":""}' : '{"event":"command","func":"playVideo","args":""}';
            ytIframe.contentWindow.postMessage(msg, '*');
        }
        videoBtnIcon.className = isPaused ? 'fas fa-play' : 'fas fa-pause';
        videoBtn.setAttribute('aria-label', isPaused ? 'Play video' : 'Pause video');
    });
}

})();

/* ─── ABOUT REVEAL ─── */
(function() {
    const sec = document.querySelector('.about');
    if (!sec) return;
    const obs = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            document.getElementById('aboutHeadline')?.classList.add('revealed');
            setTimeout(() => document.getElementById('aboutBody')?.classList.add('revealed'), 320);
            setTimeout(() => document.getElementById('aboutDivider')?.classList.add('revealed'), 480);
            setTimeout(() => document.getElementById('aboutCta')?.classList.add('revealed'), 580);
            setTimeout(() => document.getElementById('aboutRight')?.classList.add('revealed'), 220);
            obs.unobserve(entry.target);
        });
    }, { threshold: 0.1 });
    obs.observe(sec);
})();

/* ─── EXPLORE MODELS — seluruh halaman dark saat scroll ke explore ─── */
/* ─── EXPLORE MODELS — seluruh halaman dark saat scroll ke explore ─── */
(function() {
    const exploreSection = document.querySelector('.explore-models');
    if (!exploreSection) return;

    const style = document.createElement('style');
    style.textContent = `
        .featured-vehicles, .models, .about, .about-cta-section,
        .about-headline, .marquee-strip, .inventory, .discover,
        .explore-models {
            transition: background 1s ease, color 1s ease, border-color 1s ease;
        }
        body.site-dark .featured-vehicles { background: #000000 !important; }
        body.site-dark .models            { background: #000000 !important; }
        body.site-dark .about             { background: #000000 !important; }
        body.site-dark .about-cta-section { background: #000000 !important; }
        body.site-dark .about-cta-card    { background: #000000 !important; }
        body.site-dark .about-headline    { background: #000000 !important; }
        body.site-dark .marquee-strip     { background: #000000 !important; }
        body.site-dark .inventory         { background: #000000 !important; }
        body.site-dark .inventory-text    { background: linear-gradient(135deg,#1a1a1a,#111) !important; }
        body.site-dark .discover          { background: #000000 !important; color: #fff !important; }
        body.site-dark .explore-models    { background: #000 !important; }
        body.site-dark footer             { background: #000 !important; }
        body.site-dark .divider           { background: rgba(0, 0, 0, 0.08) !important; }
        body.site-dark .fv-heading,
        body.site-dark .discover-title-main { color: #fff !important; }
        body.site-dark .fv-sub            { color: rgba(255,255,255,0.45) !important; }
        body.site-dark .models-info h2    { color: #fff !important; }
        body.site-dark .model-tab         { color: rgba(255,255,255,0.4) !important; }
        body.site-dark .model-tab.active  { color: #fff !important; }
        body.site-dark .fuel-tag {
            background: #222 !important;
            color: rgba(255,255,255,0.6) !important;
            border-color: #333 !important;
        }
        body.site-dark .marquee-item      { color: rgba(255,255,255,0.13) !important; }
        body.site-dark .marquee-diamond   { background: rgba(255,255,255,0.13) !important; }
        body.site-dark .marquee-strip::before { background: linear-gradient(to right, #0a0a0a, transparent) !important; }
        body.site-dark .marquee-strip::after  { background: linear-gradient(to left,  #0a0a0a, transparent) !important; }
        .explore-models.is-dark { background: #000 !important; color: #fff !important; }
        .explore-models.is-dark h2 { color: #fff !important; }
        .explore-models.is-dark .section-tag-num { color: rgba(255,255,255,0.28) !important; }
        .explore-models.is-dark .section-tag-line { background: rgba(255,255,255,0.18) !important; }
        .explore-models.is-dark .explore-card-title img {
            filter: brightness(0) invert(1) drop-shadow(0 2px 20px rgba(0,0,0,0.5)) !important;
        }
        .explore-models.is-dark .explore-badge {
            background: rgba(255,255,255,0.12) !important;
            border: 1px solid rgba(255,255,255,0.25) !important;
            color: #fff !important;
        }
    `;
    document.head.appendChild(style);

    function checkDark() {
        const rect = exploreSection.getBoundingClientRect();
        // Dark hanya aktif saat explore section masih overlap dengan viewport
        // rect.bottom > 100 → belum lewat sepenuhnya
        // rect.top < window.innerHeight - 100 → sudah masuk viewport
        // SESUDAH
        const inView = rect.top < (window.innerHeight * 0.15) && rect.bottom > (window.innerHeight * 0.80);
        if (inView) {
            document.body.classList.add('site-dark');
            exploreSection.classList.add('is-dark');
        } else {
            document.body.classList.remove('site-dark');
            exploreSection.classList.remove('is-dark');
        }
    }

    window.addEventListener('scroll', checkDark, { passive: true });
    checkDark(); // cek langsung saat load
})();
</script>
</body>
</html>