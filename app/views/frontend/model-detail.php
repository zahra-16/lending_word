<?php
session_start();
require_once __DIR__ . '/../../models/ModelVariant.php';
require_once __DIR__ . '/../../models/FooterSection.php';
require_once __DIR__ . '/../../models/Content.php';
require_once __DIR__ . '/../../models/ModelGallery.php';
require_once __DIR__ . '/../../models/ModelSpecificationSection.php';
require_once __DIR__ . '/../../../config.php';

$id = $_GET['id'] ?? 0;

$modelVariant = new ModelVariant();
$variant = $modelVariant->getById($id);

if (!$variant) {
    header('Location: models.php');
    exit;
}

$subModels = $modelVariant->getVariantsByCategory($variant['category_slug']);

$modelGallery = new ModelGallery();
$galleryImages = $modelGallery->getByVariantId($id);

$footerSectionModel = new FooterSection();
$footerSections = $footerSectionModel->getAllWithLinks();
$socialLinks = $footerSectionModel->getSocialLinks();

$contentModel = new Content();
$getContent = function($section, $key) use ($contentModel) {
    return $contentModel->get($section, $key);
};

$modelSpec = new ModelSpecificationSection();

$db = new PDO(
    "pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME,
    DB_USER,
    DB_PASS
);
$stmt = $db->prepare("SELECT * FROM model_specification_sections WHERE variant_id = ? ORDER BY sort_order ASC");
$stmt->execute([$id]);
$specificationSections = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $db->prepare("SELECT * FROM model_sound WHERE variant_id = ?");
$stmt->execute([$id]);
$soundData = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Model Detail - Porsche</title>
    <link rel="stylesheet" href="/lending_word/public/assets/css/style.css?v=<?= time() ?>">
    <link rel="icon" type="image/png" href="/lending_word/public/assets/images/porsche-logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* ══════════════════════════════════════════
           PORSCHE NEXT FONT - Local File
        ══════════════════════════════════════════ */
        @font-face {
            font-family: "Porsche Next";
            src: url("/lending_word/public/assets/fonts/Porsche Next.ttf") format("truetype");
            font-weight: 100 900;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: "Porsche Next";
            src: url("/lending_word/public/assets/fonts/Porsche Next.ttf") format("truetype");
            font-weight: 100 900;
            font-style: italic;
            font-display: swap;
        }

        /* ══════════════════════════════════════════
           ROOT & BASE
        ══════════════════════════════════════════ */
        *, *::before, *::after { box-sizing: border-box; }

        :root {
            --white:      #ffffff;
            --off:        #f6f6f3;
            --black:      #0a0a0a;
            --gray:       #888;
            --light:      #e6e6e0;
            --gold:       #666666;
            --gold-light: #888888;
            --red:        #666666;
            --ease:       cubic-bezier(0.16, 1, 0.3, 1);
            --ease-back:  cubic-bezier(0.34, 1.56, 0.64, 1);
            --font-porsche: "Porsche Next", "Arial Narrow", Arial, sans-serif;
        }

        html { cursor: none; scroll-behavior: smooth; }

        body {
            background: #fff;
            color: #000;
            font-family: var(--font-porsche);
            font-weight: 300;
            overflow-x: hidden;
        }

 #cursor-dot, #cursor-ring {
    position: fixed;
    pointer-events: none;
    z-index: 9999;
    border-radius: 50%;
    top: 0; left: 0;
    transform: translate(-50%, -50%);
    will-change: left, top, transform;
    transition-property: width, height, opacity, transform;
    transition-timing-function: var(--ease);
    mix-blend-mode: difference; /* ← kunci utama */
}
#cursor-dot {
    width: 8px; height: 8px;
    background: #ffffff; /* putih = di bg putih jadi hitam, di bg gelap jadi putih */
    transition-duration: .2s, .2s, .2s, .15s;
}
#cursor-ring {
    width: 38px; height: 38px;
    border: 1.5px solid #ffffff;
    background: transparent;
    transition-duration: .35s, .35s, .3s, .22s;
}

body.c-link #cursor-dot  { width: 5px; height: 5px; }
body.c-link #cursor-ring { width: 54px; height: 54px; }

body.c-card #cursor-dot  { width: 10px; height: 10px; }
body.c-card #cursor-ring { width: 54px; height: 54px; }

body.c-gold #cursor-dot  { width: 10px; height: 10px; }
body.c-gold #cursor-ring { width: 54px; height: 54px; }

body.c-click #cursor-dot {
    transform: translate(-50%, -50%) scale(2.5);
    opacity: 0;
}
body.c-click #cursor-ring {
    transform: translate(-50%, -50%) scale(1.5);
    opacity: 0;
}

        /* ── SCROLL PROGRESS BAR ── */
        #progress {
            position: fixed;
            top: 0; left: 0;
            height: 2px;
            width: 0;
            background: var(--red);
            z-index: 8000;
            transition: width .1s linear;
        }

        /* ── INTRO CURTAIN ── */
        #intro {
            position: fixed; inset: 0;
            z-index: 5000;
            display: flex; align-items: center; justify-content: center;
            background: #ffffff;
            transition: opacity .5s ease .1s;
        }

        #intro.done { opacity: 0; pointer-events: none; }

        .c-panel {
            position: absolute;
            top: 0; bottom: 0;
            width: 50%;
            background: #ffffff;
            z-index: 2;
            transition: transform 1.2s cubic-bezier(0.76, 0, 0.24, 1);
        }

        .c-panel.l {
            left: 0;
            border-right: 1px solid rgba(0,0,0,0.08);
        }

        .c-panel.r {
            right: 0;
            border-left: 1px solid rgba(0,0,0,0.08);
        }

        #intro.open .c-panel.l { transform: translateX(-100%); }
        #intro.open .c-panel.r { transform: translateX(100%); }

        #intro-logo {
            position: relative;
            z-index: 1;
            opacity: 0;
            animation: wrdIn .6s .15s var(--ease) forwards;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #intro-logo img {
            width: clamp(80px, 10vw, 130px);
            height: auto;
        }

        @keyframes wrdIn {
            from { opacity:0; transform: translateY(10px); }
            to   { opacity:1; transform: translateY(0); }
        }

       /* ── NAVBAR ── */
        .navbar {
            background: rgba(255,255,255,0.6) !important;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        transition: background .4s ease, box-shadow .4s ease;
        }
        .navbar.scrolled {
            background: rgba(255,255,255,0.92) !important;
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            box-shadow: 0 1px 0 rgba(0,0,0,.07);
        }
        .navbar .navbar-brand,
        .navbar .navbar-menu a { color: var(--black) !important; filter: none !important; }
        .navbar-menu a::after { background: var(--black) !important; }
        .navbar-brand img { filter: brightness(0) !important; }

        /* ══════════════════════════════════════════
           HERO
        ══════════════════════════════════════════ */
        .detail-hero {
            min-height: 70vh;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            background-color: #fff;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            padding: 150px 60px 0;
            position: relative;
            overflow: visible;
        }

        .detail-hero-bg-clip {
            position: absolute;
            inset: 0;
            overflow: hidden;
            pointer-events: none;
            z-index: 0;
        }

        .detail-hero img {
            max-width: 900px;
            width: 100%;
            height: auto;
            position: relative;
            z-index: 2;
            opacity: 0;
            animation: heroCarIn 1.4s 2.1s var(--ease) forwards;
            filter: drop-shadow(0 40px 60px rgba(0,0,0,0.18));
            will-change: transform;
        }

        @keyframes heroCarIn {
            from {
                opacity: 0;
                transform: translateY(calc(50% + 80px)) scale(0.95);
                filter: drop-shadow(0 10px 20px rgba(0,0,0,0.06));
            }
            to {
                opacity: 1;
                transform: translateY(50%) scale(1);
                filter: drop-shadow(0 40px 60px rgba(0,0,0,0.18));
            }
        }

        /* ══════════════════════════════════════════
           DETAIL CONTENT
        ══════════════════════════════════════════ */
        .detail-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 300px 60px 80px;
        }

        /* back button */
        .back-btn {
            display: inline-block;
            padding: 12px 30px;
            background: #000;
            color: #fff;
            text-decoration: none;
            margin-bottom: 40px;
            text-transform: uppercase;
            letter-spacing: .15em;
            font-size: 0.75rem;
            font-family: var(--font-porsche);
            font-weight: 400;
            opacity: 0;
            transform: translateX(-20px);
            animation: slideInLeft 0.7s 2.3s var(--ease) forwards;
            position: relative;
            overflow: hidden;
        }

        .back-btn::before {
            content: '';
            position: absolute;
            inset: 0;
            background: var(--red);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.4s var(--ease);
        }

        .back-btn:hover::before { transform: scaleX(1); }
        .back-btn:hover { color: #fff; }
        .back-btn span { position: relative; z-index: 1; }

        @keyframes slideInLeft {
            to { opacity: 1; transform: translateX(0); }
        }

        /* Detail title */
        .detail-title {
            font-family: var(--font-porsche);
            font-size: clamp(2.5rem, 5vw, 4rem);
            font-weight: 700;
            margin-bottom: 40px;
            letter-spacing: .02em;
            line-height: 1;
            overflow: hidden;
        }

        .detail-title-word {
            display: inline-block;
            opacity: 0;
            transform: translateY(100%);
            animation: wrdUp 0.8s var(--ease) forwards;
        }
        .detail-title-word:nth-child(1) { animation-delay: 2.4s; }
        .detail-title-word:nth-child(2) { animation-delay: 2.55s; }
        .detail-title-word:nth-child(3) { animation-delay: 2.7s; }
        .detail-title-word:nth-child(4) { animation-delay: 2.85s; }

        @keyframes wrdUp {
            to { opacity: 1; transform: translateY(0); }
        }

        /* ══════════════════════════════════════════
           SPECS GRID
        ══════════════════════════════════════════ */
        .specs-grid {
            display: grid;
            grid-template-columns: 400px 1fr;
            gap: 80px;
            margin: 60px 0;
            align-items: center;
        }

        .specs-left {
            display: flex;
            flex-direction: column;
            gap: 50px;
        }

        .specs-right {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .specs-right img {
            width: 100%;
            max-width: 800px;
            height: auto;
            transition: transform 0.6s var(--ease), filter 0.4s ease;
            filter: drop-shadow(0 30px 50px rgba(0,0,0,0.12));
        }

        .specs-right:hover img {
            transform: scale(1.03) translateY(-8px);
            filter: drop-shadow(0 50px 70px rgba(0,0,0,0.2));
        }

        /* Spec boxes */
        .spec-box {
            padding: 0;
            background: transparent;
            border-radius: 0;
            margin-bottom: 50px;
            opacity: 0;
            transform: translateY(24px);
            transition: opacity 0.6s var(--ease), transform 0.6s var(--ease);
        }

        .spec-box.reveal-done {
            opacity: 1;
            transform: translateY(0);
        }

        .spec-box h3 {
            font-family: var(--font-porsche);
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .15em;
            color: var(--gray);
            margin-bottom: 8px;
            font-weight: 400;
        }

        .spec-box p {
            font-family: var(--font-porsche);
            font-size: 4rem;
            font-weight: 700;
            line-height: 1;
            margin-bottom: 5px;
            color: var(--black);
        }

        .spec-box p span {
            font-size: 2rem;
            font-weight: 400;
        }

        .counter {
            display: inline-block;
            font-size: inherit !important;
            font-family: var(--font-porsche);
        }

        /* spec bar */
        .spec-bar-track {
            width: 100%;
            height: 1px;
            background: var(--light);
            margin-top: 12px;
            position: relative;
            overflow: hidden;
        }

        .spec-bar-fill {
            position: absolute;
            inset: 0;
            background: linear-gradient(to right, rgba(100,100,100,.3), var(--gold));
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 1.4s var(--ease);
        }

        .spec-box.reveal-done .spec-bar-fill {
            transform: scaleX(var(--fill, 0.7));
        }

        .spec-box p.secondary-val {
            font-family: var(--font-porsche);
            font-size: 1.4rem;
            font-weight: 600;
            letter-spacing: .03em;
        }

        /* ══════════════════════════════════════════
           VIDEO SECTION
        ══════════════════════════════════════════ */
        .video-section {
            margin-top: 100px;
            padding-top: 80px;
            border-top: 1px solid #e5e5e5;
        }

        .video-title {
            font-family: var(--font-porsche);
            font-size: clamp(1.8rem, 3vw, 2.5rem);
            font-weight: 600;
            text-align: center;
            margin-bottom: 60px;
            letter-spacing: .02em;
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.7s var(--ease), transform 0.7s var(--ease);
        }

        .video-title.reveal-done { opacity: 1; transform: translateY(0); }

        .video-container {
            position: relative;
            width: 100%;
            border-radius: 12px;
            overflow: hidden;
            background: #000;
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.8s var(--ease), transform 0.8s var(--ease);
        }

        .video-container.reveal-done { opacity: 1; transform: translateY(0); }

        .video-container video {
            width: 100%;
            height: auto;
            display: block;
        }

        .video-toggle {
            position: absolute;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.8);
            border-radius: 8px;
            color: #fff;
            font-size: 1.2rem;
            cursor: none;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            font-family: var(--font-porsche);
        }

        .video-toggle:hover {
            background: rgba(255, 255, 255, 0.3);
            border-color: #fff;
            transform: scale(1.08);
        }

        /* ══════════════════════════════════════════
           SPEC HERO SECTION
        ══════════════════════════════════════════ */
        .spec-hero-section {
            position: relative;
            height: 100vh;
            width: 100vw;
            margin: 120px 0 0 0;
            margin-left: calc(-50vw + 50%);
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            overflow: hidden;
        }

        .spec-hero-bg-layer {
            position: absolute;
            inset: -10%;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            will-change: transform;
        }

        .spec-hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(
                to top,
                rgba(0,0,0,0.9) 0%,
                rgba(0,0,0,0.7) 30%,
                rgba(0,0,0,0.4) 55%,
                rgba(0,0,0,0.1) 80%,
                rgba(0,0,0,0) 100%
            );
            z-index: 1;
        }

        .spec-hero-content {
            position: relative;
            z-index: 2;
            text-align: center;
            color: #fff;
            max-width: 900px;
            padding: 100px 30px;
        }

        .spec-hero-content h2 {
            font-family: var(--font-porsche);
            font-size: clamp(3rem, 5vw, 4.8rem);
            font-weight: 700;
            letter-spacing: .02em;
            line-height: 1.1;
            margin-bottom: 32px;
            opacity: 0;
            transform: translateY(40px);
            animation: specFadeUp 1.4s cubic-bezier(.19,1,.22,1) forwards;
        }

        .spec-hero-content p {
            font-family: var(--font-porsche);
            font-size: clamp(.95rem, 1.3vw, 1.15rem);
            font-weight: 300;
            line-height: 1.75;
            letter-spacing: .01em;
            opacity: 0;
            transform: translateY(40px);
            animation: specFadeUp 1.4s cubic-bezier(.19,1,.22,1) 0.3s forwards;
        }

        @keyframes specFadeUp {
            to { opacity: 0.9; transform: translateY(0); }
        }

        /* ══════════════════════════════════════════
           SPEC CARDS COLLAGE
        ══════════════════════════════════════════ */
        .spec-cards-collage {
            position: relative;
            width: 100vw;
            height: 100vh;
            margin-left: calc(-50vw + 50%);
            margin-top: -50px;
            padding: 0 0 100px 0;
            background: linear-gradient(to bottom, #000 0%, #000 65%, #fff 65%, #fff 100%);
            color: #fff;
            z-index: 10;
        }

        .spec-cards-wrapper {
            max-width: 1400px;
            margin: 0 auto;
            padding: 100px 60px 80px;
            position: relative;
        }

        .spec-cards-grid {
            display: flex;
            gap: 20px;
            margin-bottom: 40px;
            overflow-x: auto;
            scroll-behavior: smooth;
            scrollbar-width: none;
            -ms-overflow-style: none;
            padding-bottom: 10px;
            margin-top: 0;
        }

        .spec-cards-grid::-webkit-scrollbar { display: none; }

        .spec-card-main,
        .spec-card-secondary {
            min-width: 600px;
            max-width: 600px;
            flex-shrink: 0;
        }

        .spec-card {
            position: relative;
            background: rgba(244, 244, 244, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(255, 255, 255, 0.1);
            height: 500px;
            opacity: 0;
            transform: translateY(40px) scale(0.97);
        }

        .spec-card.card-revealed {
            opacity: 1;
            transform: translateY(0) scale(1);
            transition: opacity 0.7s var(--ease), transform 0.7s var(--ease),
                        box-shadow 0.4s ease, border-color 0.3s ease;
        }

        .spec-card:hover {
            transform: translateY(-8px) scale(1.01);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            border-color: rgba(100,100,100,0.3);
        }

        .spec-card-spotlight {
            position: absolute;
            inset: 0;
            border-radius: 12px;
            pointer-events: none;
            opacity: 0;
            background: radial-gradient(circle 200px at var(--mx,50%) var(--my,50%),
                rgba(100,100,100,0.08) 0%, transparent 70%);
            transition: opacity .3s ease;
            z-index: 5;
        }

        .spec-card:hover .spec-card-spotlight { opacity: 1; }

        .spec-card-image {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        .spec-card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .spec-card:hover .spec-card-image img { transform: scale(1.08); }

        .spec-card-content {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 30px;
            z-index: 2;
            display: flex;
            flex-direction: column;
        }

        .spec-card-content::before {
            content: '';
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 180px;
            background: linear-gradient(to top, rgba(0,0,0,.85) 0%, rgba(0,0,0,.6) 40%, transparent 100%);
            z-index: -1;
            pointer-events: none;
            transition: height 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .spec-card-content.expanded::before {
            height: 100%;
            background: linear-gradient(to top, rgba(0,0,0,.9) 0%, rgba(0,0,0,.75) 50%, rgba(0,0,0,.5) 100%);
        }

        .spec-card-title {
            font-family: var(--font-porsche);
            font-size: 1.75rem;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 12px;
            color: #fff;
            letter-spacing: .02em;
        }

        .spec-card-description {
            max-height: 0;
            overflow: hidden;
            opacity: 0;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            margin-bottom: 0;
        }

        .spec-card-description.expanded {
            max-height: 350px;
            opacity: 1;
            margin-bottom: 16px;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,0.3) transparent;
        }

        .spec-card-description.expanded::-webkit-scrollbar { width: 4px; }
        .spec-card-description.expanded::-webkit-scrollbar-track { background: transparent; }
        .spec-card-description.expanded::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.3); border-radius: 2px; }

        .spec-card-text {
            font-family: var(--font-porsche);
            font-size: 0.9rem;
            font-weight: 300;
            line-height: 1.7;
            letter-spacing: .01em;
            color: rgba(255,255,255,.85);
            margin: 0;
        }

        .spec-card-toggle {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: none;
            border: 1px solid rgba(255,255,255,0.3);
            color: rgba(255,255,255,0.9);
            font-family: var(--font-porsche);
            font-size: 0.78rem;
            font-weight: 400;
            padding: 8px 16px;
            border-radius: 999px;
            cursor: none;
            transition: all 0.3s ease;
            position: relative;
            align-self: flex-start;
            letter-spacing: .1em;
            text-transform: uppercase;
        }

        .spec-card-toggle:hover {
            background: rgba(255,255,255,0.1);
            border-color: rgba(255,255,255,0.6);
        }

        .toggle-icon {
            transition: transform 0.3s ease;
            width: 12px; height: 12px;
        }

        .spec-card-description.expanded ~ .spec-card-toggle .toggle-icon { transform: rotate(180deg); }
        .spec-card-description.expanded ~ .spec-card-toggle { opacity: 0; pointer-events: none; }

        /* Nav arrows */
        .spec-cards-nav {
            position: absolute;
            top: 50%; left: 0; right: 0;
            transform: translateY(-50%);
            display: flex;
            justify-content: space-between;
            pointer-events: none;
            z-index: 10;
        }

        .spec-nav-btn {
            width: 52px; height: 52px;
            border-radius: 50%;
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.25);
            color: #fff;
            cursor: none;
            display: flex; align-items: center; justify-content: center;
            pointer-events: auto;
            transition: all 0.3s ease;
        }

        .spec-nav-btn:hover {
            background: rgba(255,255,255,0.25);
            transform: scale(1.12);
            box-shadow: 0 8px 24px rgba(0,0,0,.3);
            border-color: rgba(100,100,100,0.5);
        }

        .spec-nav-prev { margin-left: -26px; }
        .spec-nav-next { margin-right: -26px; }

        .spec-pagination {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-top: 70px;
        }

        .spec-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            background: rgba(255,255,255,.35);
            cursor: none;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .spec-dot.active {
            background: var(--gold);
            width: 36px;
            border-radius: 4px;
        }

        .spec-dot:hover { background: rgba(255,255,255,.7); }

        /* ══════════════════════════════════════════
           SOUND SECTIONS
        ══════════════════════════════════════════ */
        .sound-section-detail {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            margin: 100px 0;
            border-radius: 20px;
            opacity: 0;
            transform: translateY(40px);
            transition: opacity 0.8s var(--ease), transform 0.8s var(--ease);
        }

        .sound-section-detail.reveal-done { opacity: 1; transform: translateY(0); }

        .sound-bg-detail {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background-size: cover;
            background-position: center;
            filter: brightness(0.6);
            z-index: 0;
            transition: transform 0.8s var(--ease);
        }

        .sound-section-detail:hover .sound-bg-detail { transform: scale(1.02); }

        .sound-content-detail {
            position: relative;
            z-index: 2;
            text-align: center;
            color: #fff;
            padding: 40px;
            max-width: 1200px;
        }

        .sound-content-detail h2 {
            font-family: var(--font-porsche);
            font-size: clamp(2.5rem, 5vw, 4.5rem);
            font-weight: 700;
            letter-spacing: .02em;
            margin-bottom: 30px;
            line-height: 1.2;
        }

        .sound-content-detail p {
            font-family: var(--font-porsche);
            font-size: clamp(.9rem, 1.5vw, 1.15rem);
            font-weight: 300;
            line-height: 1.75;
            letter-spacing: .01em;
            margin-bottom: 50px;
            opacity: 0.9;
            max-width: 900px;
            margin-left: auto;
            margin-right: auto;
        }

        .sound-btn-detail {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            padding: 14px 32px;
            background: rgba(255,255,255,0.95);
            color: #000;
            border: none;
            border-radius: 4px;
            font-size: 0.78rem;
            font-weight: 500;
            cursor: none;
            transition: all 0.3s ease;
            font-family: var(--font-porsche);
            text-transform: uppercase;
            letter-spacing: .15em;
            position: relative;
            overflow: hidden;
        }

        .sound-btn-detail::before {
            content: '';
            position: absolute; inset: 0;
            background: var(--gold);
            transform: translateY(101%);
            transition: transform .35s var(--ease);
        }

        .sound-btn-detail:hover { color: #fff; transform: translateY(-2px); box-shadow: 0 10px 30px rgba(100,100,100,0.3); }
        .sound-btn-detail:hover::before { transform: translateY(0); }
        .sound-btn-detail span, .sound-btn-detail i { position: relative; z-index: 1; }

        /* Sound content section */
        .sound-section-content {
            position: relative;
            height: 600px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
            margin: 100px 0;
            border-radius: 24px;
            opacity: 0;
            transform: translateY(40px);
            transition: opacity 0.8s var(--ease), transform 0.8s var(--ease);
        }

        .sound-section-content.reveal-done { opacity: 1; transform: translateY(0); }

        .sound-bg-content {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background-size: cover;
            background-position: center;
            filter: brightness(0.7);
            z-index: 0;
            transition: transform 0.6s var(--ease);
        }

        .sound-section-content:hover .sound-bg-content { transform: scale(1.03); }

        .sound-content-wrapper {
            position: relative;
            z-index: 2;
            color: #fff;
            padding: 60px 60px 50px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
        }

        .sound-content-wrapper h2 {
            font-family: var(--font-porsche);
            font-size: clamp(2rem, 4vw, 3.5rem);
            font-weight: 700;
            letter-spacing: .02em;
            margin-bottom: 20px;
            line-height: 1.2;
            text-align: left;
        }

        .sound-content-wrapper p {
            font-family: var(--font-porsche);
            font-size: clamp(.85rem, 1.2vw, 1rem);
            font-weight: 300;
            letter-spacing: .01em;
            line-height: 1.7;
            margin-bottom: 0;
            opacity: 1;
            text-align: left;
        }

        .sound-btn-content {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 16px 28px;
            background: #fff;
            color: #000;
            border: none;
            border-radius: 6px;
            font-size: 0.78rem;
            font-weight: 500;
            cursor: none;
            transition: all 0.3s ease;
            font-family: var(--font-porsche);
            align-self: center;
            text-transform: uppercase;
            letter-spacing: .15em;
            position: relative;
            overflow: hidden;
        }

        .sound-btn-content::before {
            content: '';
            position: absolute; inset: 0;
            background: var(--gold);
            transform: scaleY(0);
            transform-origin: bottom;
            transition: transform .3s var(--ease);
        }

        .sound-btn-content:hover { color: #fff; transform: scale(1.04); }
        .sound-btn-content:hover::before { transform: scaleY(1); }
        .sound-btn-content span, .sound-btn-content i { position: relative; z-index: 1; }

        /* ══════════════════════════════════════════
           GALLERY
        ══════════════════════════════════════════ */
        .gallery-section {
            padding: 120px 0;
            background: #ffffff;
        }

        .gallery-layout {
            max-width: 1050px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .gallery-img-1 {
            width: 100%;
            margin-bottom: -120px;
            position: relative;
            z-index: 1;
            opacity: 0;
            transform: translateY(50px);
            transition: opacity 0.9s var(--ease), transform 0.9s var(--ease);
        }

        .gallery-img-1.reveal-done { opacity: 1; transform: translateY(0); }

        .gallery-img-1 img {
            width: 100%;
            border-radius: 20px;
            box-shadow: 0 30px 80px rgba(0,0,0,0.12);
            transition: transform 0.6s var(--ease), box-shadow 0.6s ease;
        }

        .gallery-img-1:hover img {
            transform: scale(1.01);
            box-shadow: 0 40px 100px rgba(0,0,0,0.18);
        }

        .gallery-content-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 40px;
            position: relative;
            z-index: 2;
        }

        .gallery-text {
            width: 42%;
            margin-top: 150px;
            padding: 45px;
            opacity: 0;
            transform: translateX(-30px);
            transition: opacity 0.8s var(--ease) 0.2s, transform 0.8s var(--ease) 0.2s;
        }

        .gallery-text.reveal-done { opacity: 1; transform: translateX(0); }

        .gallery-text h2 {
            font-family: var(--font-porsche);
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 20px;
            letter-spacing: .02em;
            line-height: 1.2;
        }

        .gallery-text p {
            font-family: var(--font-porsche);
            font-size: 0.9rem;
            line-height: 1.75;
            letter-spacing: .01em;
            color: #333;
            font-weight: 300;
        }

        .gallery-img-2 {
            width: 55%;
            opacity: 0;
            transform: translateX(30px);
            transition: opacity 0.8s var(--ease) 0.3s, transform 0.8s var(--ease) 0.3s;
        }

        .gallery-img-2.reveal-done { opacity: 1; transform: translateX(0); }

        .gallery-img-2 img {
            width: 500px;
            height: 600px;
            border-radius: 20px;
            box-shadow: 0 30px 80px rgba(0,0,0,0.12);
            transition: transform 0.6s var(--ease), box-shadow 0.6s ease;
        }

        .gallery-img-2:hover img {
            transform: scale(1.02) translateY(-6px);
            box-shadow: 0 50px 100px rgba(0,0,0,0.2);
        }

        .gallery-img-3 {
            width: 55%;
            margin-left: 250px;
            margin-top: -90px;
            position: relative;
            z-index: 3;
            opacity: 0;
            transform: translateY(40px);
            transition: opacity 0.8s var(--ease) 0.4s, transform 0.8s var(--ease) 0.4s;
        }

        .gallery-img-3.reveal-done { opacity: 1; transform: translateY(0); }

        .gallery-img-3 img {
            width: 100%;
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.12);
            transition: transform 0.6s var(--ease), box-shadow 0.6s ease;
        }

        .gallery-img-3:hover img {
            transform: scale(1.02) translateY(-4px);
            box-shadow: 0 40px 80px rgba(0,0,0,0.2);
        }

        /* ══════════════════════════════════════════
           SUB MODELS
        ══════════════════════════════════════════ */
        .sub-models-section {
            margin-top: 100px;
            padding-top: 80px;
            border-top: 1px solid #e5e5e5;
        }

        .sub-models-title {
            font-family: var(--font-porsche);
            font-size: clamp(1.8rem, 3vw, 2.5rem);
            font-weight: 700;
            text-align: center;
            margin-bottom: 60px;
            letter-spacing: .02em;
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.7s var(--ease), transform 0.7s var(--ease);
        }

        .sub-models-title.reveal-done { opacity: 1; transform: translateY(0); }

        .sub-models-slider {
            position: relative;
            overflow: hidden;
            padding: 0 60px;
        }

        .sub-models-container {
            display: flex;
            gap: 40px;
            transition: transform 0.4s var(--ease);
        }

        .sub-model-card {
            min-width: calc(33.333% - 27px);
            background: #fff;
            border: 1px solid var(--light);
            border-radius: 16px;
            padding: 30px;
            box-sizing: border-box;
            transition: all 0.4s var(--ease);
            opacity: 0;
            transform: translateY(40px) scale(0.97);
            box-shadow: 0 2px 6px rgba(0,0,0,.04);
            position: relative;
            overflow: hidden;
        }

        .sub-model-card.reveal-done {
            opacity: 1;
            transform: translateY(0) scale(1);
        }

        .sub-model-card::after {
            content: '';
            position: absolute; inset: -1px;
            border-radius: 17px;
            background: linear-gradient(135deg, rgba(100,100,100,.22), rgba(0,0,0,.06)) border-box;
            border: 1px solid transparent;
            -webkit-mask: linear-gradient(#fff 0 0) padding-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: destination-out;
            mask-composite: exclude;
            opacity: 0;
            transition: opacity .4s ease;
            pointer-events: none;
        }

        .sub-model-card:hover { transform: translateY(-6px) scale(1.01); box-shadow: 0 20px 60px rgba(0,0,0,.1); }
        .sub-model-card:hover::after { opacity: 1; }

        .sub-model-fuel {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: var(--font-porsche);
            font-size: 0.78rem;
            margin-bottom: 20px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: .1em;
        }

        .checkmark { font-size: 1.2rem; color: var(--gold); }

        .sub-model-card img {
            width: 100%;
            height: auto;
            margin-bottom: 20px;
            transition: transform 0.5s var(--ease), filter 0.4s ease;
            filter: drop-shadow(0 12px 24px rgba(0,0,0,.1));
        }

        .sub-model-card:hover img {
            transform: scale(1.04) translateY(-4px);
            filter: drop-shadow(0 24px 40px rgba(0,0,0,.18));
        }

        .sub-model-card h3 {
            font-family: var(--font-porsche);
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 30px;
            letter-spacing: .02em;
        }

        .sub-model-specs { margin-bottom: 30px; }

        .sub-model-specs .spec-item {
            margin-bottom: 16px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--light);
        }

        .sub-model-specs .spec-item:last-child { border-bottom: none; }

        .sub-model-specs .spec-value {
            font-family: var(--font-porsche);
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 4px;
            color: var(--black);
            letter-spacing: .03em;
        }

        .sub-model-specs .spec-label {
            font-size: 9px;
            color: #888;
            text-transform: uppercase;
            letter-spacing: .08em;
            font-family: var(--font-porsche);
        }

        .sub-model-actions { display: flex; gap: 10px; }

        .btn-configure {
            width: 100%;
            padding: 13px;
            font-family: var(--font-porsche);
            font-size: 0.72rem;
            border: none;
            cursor: none;
            text-transform: uppercase;
            letter-spacing: .2em;
            background: var(--black);
            color: #fff;
            text-decoration: none;
            display: block;
            text-align: center;
            border-radius: 6px;
            position: relative;
            overflow: hidden;
            transition: transform .2s ease, box-shadow .3s ease;
        }

        .btn-configure::before {
            content: '';
            position: absolute; inset: 0;
            background: var(--gold);
            transform: translateY(101%);
            transition: transform .4s var(--ease);
        }

        .btn-configure:hover { transform: translateY(-2px); box-shadow: 0 12px 28px rgba(0,0,0,.2); }
        .btn-configure:hover::before { transform: translateY(0); }
        .btn-configure span { position: relative; z-index: 1; }

        .slider-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: #fff;
            border: 1.5px solid var(--light);
            width: 50px; height: 50px;
            border-radius: 50%;
            cursor: none;
            font-size: 1.2rem;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s var(--ease);
            font-family: var(--font-porsche);
        }

        .slider-arrow.prev { left: 0; }
        .slider-arrow.next { right: 0; }
        .slider-arrow:hover { background: var(--black); color: #fff; border-color: var(--black); transform: translateY(-50%) scale(1.1); }

        .slider-dots {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 30px;
        }

        .dot {
            width: 8px; height: 8px;
            background: #ddd;
            border-radius: 50%;
            cursor: none;
            transition: all 0.4s var(--ease);
        }

        .dot.active {
            background: var(--gold);
            width: 28px;
            border-radius: 4px;
        }

        /* ══════════════════════════════════════════
           SECTION EYEBROW LABELS
        ══════════════════════════════════════════ */
        .section-eyebrow {
            font-family: var(--font-porsche);
            font-size: 10px;
            letter-spacing: .35em;
            text-transform: uppercase;
            color: var(--red);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .section-eyebrow::before {
            content: '';
            display: block;
            width: 28px; height: 1px;
            background: var(--red);
        }

        /* ══════════════════════════════════════════
           RESPONSIVE
        ══════════════════════════════════════════ */
        @media (max-width: 1024px) {
            .spec-card-main, .spec-card-secondary { min-width: 500px; max-width: 500px; }
            .spec-nav-prev, .spec-nav-next { margin: 0; }
            .spec-cards-wrapper { padding: 0 40px; }
        }

        @media (max-width: 768px) {
            .spec-hero-section { height: 80vh; margin: 80px 0 0 0; }
            .spec-hero-content { padding: 60px 20px; }
            .spec-cards-collage { margin-top: -50px; }
            .spec-cards-wrapper { padding: 0 20px; }
            .spec-card-main, .spec-card-secondary { min-width: 90vw; max-width: 90vw; }
            .spec-card-content { padding: 24px; }
            .spec-cards-nav { display: none; }
            .sub-model-card { min-width: 100%; }
            .sub-models-slider { padding: 0 40px; }
            .sound-section-content { height: 500px; }
            .sound-content-wrapper { padding: 40px 30px 40px; }
            html { cursor: auto; }
            #cursor-dot, #cursor-ring { display: none; }
            .btn-configure, .slider-arrow, .spec-card-toggle, .sound-btn-detail,
            .sound-btn-content, .spec-nav-btn { cursor: pointer; }
        }

        @media (max-width: 576px) {
            .gallery-content-wrapper { flex-direction: column; }
            .gallery-text, .gallery-img-2, .gallery-img-3 { width: 100%; margin-left: 0; }
        }
    </style>
</head>
<body>

<!-- CURSOR -->
<div id="cursor-dot"></div>
<div id="cursor-ring"></div>

<!-- INTRO CURTAIN -->
<div id="intro">
    <div class="c-panel l"></div>
    <div id="intro-logo">
        <img src="/lending_word/public/assets/images/porsche-logo.png" alt="Porsche">
    </div>
    <div class="c-panel r"></div>
</div>

<!-- SCROLL PROGRESS -->
<div id="progress"></div>

<?php include __DIR__ . '/../partials/navbar.php'; ?>

<!-- ══════════════ HERO ══════════════ -->
<div class="detail-hero" style="background-image: url('<?= htmlspecialchars($variant['hero_bg_image'] ?? '') ?>');">
    <div class="detail-hero-bg-clip"></div>
    <img src="<?= htmlspecialchars($variant['image']) ?>" alt="<?= htmlspecialchars($variant['name']) ?>" id="heroCarImg">
</div>

<!-- ══════════════ DETAIL CONTENT ══════════════ -->
<div class="detail-content">

    <a href="models.php" class="back-btn"><span>← Back to Models</span></a>

    <h1 class="detail-title">
        <?php
        $words = explode(' ', htmlspecialchars($variant['name']));
        foreach ($words as $word) {
            echo '<span class="detail-title-word">' . $word . '&nbsp;</span>';
        }
        ?>
    </h1>

    <!-- SPECS GRID -->
    <div class="specs-grid">
        <div class="specs-left">
            <?php if ($variant['acceleration']): ?>
            <div class="spec-box" data-fill="<?= max(0, 1 - (floatval($variant['acceleration']) / 14)) ?>">
                <p><span class="counter" data-target="<?= floatval($variant['acceleration']) ?>">0</span> <span>s</span></p>
                <h3>Acceleration 0 – 100 km/h</h3>
                <div class="spec-bar-track"><div class="spec-bar-fill"></div></div>
            </div>
            <?php endif; ?>

            <?php if ($variant['power_kw'] || $variant['power_ps']): ?>
            <div class="spec-box" data-fill="<?= min(1, floatval($variant['power_kw']) / 700) ?>">
                <p><span class="counter" data-target="<?= $variant['power_kw'] ?>">0</span> <span>kW</span> / <span class="counter" data-target="<?= $variant['power_ps'] ?>">0</span> <span>PS</span></p>
                <h3>Power (kW) / Power (PS)</h3>
                <div class="spec-bar-track"><div class="spec-bar-fill"></div></div>
            </div>
            <?php endif; ?>

            <?php if ($variant['top_speed']): ?>
            <div class="spec-box" data-fill="<?= min(1, floatval($variant['top_speed']) / 330) ?>">
                <p><span class="counter" data-target="<?= floatval($variant['top_speed']) ?>">0</span> <span>km/h</span></p>
                <h3>Top speed</h3>
                <div class="spec-bar-track"><div class="spec-bar-fill"></div></div>
            </div>
            <?php endif; ?>
        </div>

        <div class="specs-right">
            <img src="<?= htmlspecialchars($variant['image']) ?>" alt="<?= htmlspecialchars($variant['name']) ?>">
        </div>
    </div>

    <!-- SECONDARY SPECS -->
    <?php if ($variant['fuel_type'] || $variant['drive_type'] || $variant['transmission']): ?>
    <div class="specs-grid" style="grid-template-columns: repeat(3, 1fr); margin-top: 80px;">
        <?php if ($variant['fuel_type']): ?>
        <div class="spec-box">
            <p class="secondary-val"><?= htmlspecialchars($variant['fuel_type']) ?></p>
            <h3>Fuel Type</h3>
        </div>
        <?php endif; ?>

        <?php if ($variant['drive_type']): ?>
        <div class="spec-box">
            <p class="secondary-val"><?= htmlspecialchars($variant['drive_type']) ?></p>
            <h3>Drive Type</h3>
        </div>
        <?php endif; ?>

        <?php if ($variant['transmission']): ?>
        <div class="spec-box">
            <p class="secondary-val"><?= htmlspecialchars($variant['transmission']) ?></p>
            <h3>Transmission</h3>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- VIDEO -->
    <?php if (!empty($variant['model_video'])): ?>
    <div class="video-section">
        <p class="section-eyebrow">Performance</p>
        <h2 class="video-title">Experience the <?= htmlspecialchars($variant['name']) ?></h2>
        <div class="video-container">
            <video id="modelVideo" autoplay loop muted playsinline>
                <source src="<?= htmlspecialchars($variant['model_video']) ?>" type="video/mp4">
            </video>
            <button id="videoToggle" class="video-toggle" onclick="toggleVideo()">
                <span class="pause-icon">||</span>
                <span class="play-icon" style="display:none;">▶</span>
            </button>
        </div>
    </div>
    <?php endif; ?>

    <!-- SOUND DETAIL -->
    <?php if (!empty($variant['model_audio'])): ?>
    <div class="sound-section-detail">
        <div class="sound-bg-detail" style="background-image: url('<?= htmlspecialchars($variant['hero_bg_image'] ?? $variant['image']) ?>');"></div>
        <div class="sound-content-detail">
            <h2><?= htmlspecialchars($getContent('sound', 'title')) ?></h2>
            <p><?= htmlspecialchars($getContent('sound', 'caption')) ?></p>
            <button class="sound-btn-detail" onclick="toggleAudio()">
                <i class="fas fa-play" id="audioIcon"></i>
                <span id="audioText"><?= htmlspecialchars($getContent('sound', 'button_text')) ?></span>
            </button>
        </div>
        <audio id="modelAudio" src="<?= htmlspecialchars($variant['model_audio']) ?>" preload="auto"></audio>
    </div>
    <?php endif; ?>

    <!-- GALLERY -->
    <?php if (!empty($galleryImages)): ?>
    <div class="gallery-section">
        <div class="gallery-layout">
            <?php
            $image1 = $galleryImages[0] ?? null;
            $image2 = $galleryImages[1] ?? null;
            $image3 = $galleryImages[2] ?? null;
            ?>

            <?php if ($image1): ?>
            <div class="gallery-img-1">
                <img src="<?= htmlspecialchars($image1['image_url']) ?>" alt="<?= htmlspecialchars($image1['title']) ?>">
            </div>
            <?php endif; ?>

            <div class="gallery-content-wrapper">
                <div class="gallery-text">
                    <?php if ($image1 && $image1['title']): ?>
                    <p class="section-eyebrow">Gallery</p>
                    <h2><?= htmlspecialchars($image1['title']) ?></h2>
                    <?php endif; ?>
                    <?php if ($image1 && $image1['caption']): ?>
                    <p><?= htmlspecialchars($image1['caption']) ?></p>
                    <?php endif; ?>
                </div>

                <?php if ($image2): ?>
                <div class="gallery-img-2">
                    <img src="<?= htmlspecialchars($image2['image_url']) ?>" alt="<?= htmlspecialchars($image2['title']) ?>">
                </div>
                <?php endif; ?>
            </div>

            <?php if ($image3): ?>
            <div class="gallery-img-3">
                <img src="<?= htmlspecialchars($image3['image_url']) ?>" alt="<?= htmlspecialchars($image3['title']) ?>">
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- SPEC HERO + CARDS -->
    <?php if (!empty($specificationSections)): ?>
    <?php foreach ($specificationSections as $specSection): ?>

    <section class="spec-hero-section">
        <div class="spec-hero-bg-layer" style="background-image: url('<?= htmlspecialchars($specSection['background_image']) ?>');"></div>
        <div class="spec-hero-overlay"></div>
        <div class="spec-hero-content">
            <h2><?= htmlspecialchars($specSection['title']) ?></h2>
            <p><?= htmlspecialchars($specSection['description']) ?></p>
        </div>
    </section>

    <?php
    $heroCards = $modelSpec->getHeroCards($specSection['id']);
    if (!empty($heroCards)):
    ?>
    <section class="spec-cards-collage">
        <div class="spec-cards-wrapper">
            <div class="spec-cards-grid" id="specCardsGrid">
                <?php foreach ($heroCards as $index => $card): ?>
                <div class="spec-card spec-card-main" data-card-id="<?= $card['id'] ?>" style="transition-delay: <?= $index * 0.1 ?>s">
                    <div class="spec-card-spotlight"></div>
                    <div class="spec-card-image">
                        <img src="<?= htmlspecialchars($card['image_url']) ?>"
                             alt="<?= htmlspecialchars($card['title']) ?>">
                    </div>
                    <div class="spec-card-content">
                        <div>
                            <h3 class="spec-card-title"><?= htmlspecialchars($card['title']) ?></h3>
                            <div class="spec-card-description">
                                <p class="spec-card-text"><?= htmlspecialchars($card['description']) ?></p>
                            </div>
                        </div>
                        <button class="spec-card-toggle" onclick="toggleCardExpand(this)">
                            <span>Show more</span>
                            <svg class="toggle-icon" width="12" height="12" viewBox="0 0 12 12" fill="none">
                                <path d="M6 8L3 5L9 5L6 8Z" fill="currentColor"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="spec-cards-nav">
                <button class="spec-nav-btn spec-nav-prev" onclick="scrollSpecCards(-1)" aria-label="Previous">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2"/>
                    </svg>
                </button>
                <button class="spec-nav-btn spec-nav-next" onclick="scrollSpecCards(1)" aria-label="Next">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2"/>
                    </svg>
                </button>
            </div>

            <div class="spec-pagination">
                <?php for($i = 0; $i < count($heroCards); $i++): ?>
                <span class="spec-dot <?= $i === 0 ? 'active' : '' ?>" onclick="scrollToCard(<?= $i ?>)"></span>
                <?php endfor; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php endforeach; ?>
    <?php endif; ?>

    <!-- SOUND CONTENT -->
    <?php if ($soundData): ?>
    <div class="sound-section-content">
        <div class="sound-bg-content" style="background-image: url('<?= htmlspecialchars($soundData['background_image']) ?>');"></div>
        <div class="sound-content-wrapper">
            <div>
                <p class="section-eyebrow" style="color: rgba(255,255,255,0.7);">Sound</p>
                <h2><?= htmlspecialchars($soundData['title']) ?></h2>
                <p><?= htmlspecialchars($soundData['caption']) ?></p>
            </div>
            <button class="sound-btn-content"
                onmousedown="playContentAudio()" onmouseup="pauseContentAudio()" onmouseleave="pauseContentAudio()"
                ontouchstart="playContentAudio()" ontouchend="pauseContentAudio()">
                <i class="fas fa-play" id="contentAudioIcon"></i>
                <span id="contentAudioText"><?= htmlspecialchars($soundData['button_text']) ?></span>
            </button>
        </div>
        <audio id="contentAudio" src="<?= htmlspecialchars($soundData['audio_url']) ?>" preload="auto"></audio>
    </div>
    <?php endif; ?>

    <!-- SUB MODELS -->
    <?php if (!empty($subModels)): ?>
    <div class="sub-models-section">
        <p class="section-eyebrow" style="justify-content: center; margin-bottom: 8px;">Model family</p>
        <h2 class="sub-models-title">Which <?= htmlspecialchars($variant['category_name']) ?> is the right one for you?</h2>

        <div class="sub-models-slider">
            <button class="slider-arrow prev" onclick="slideSubModels(-1)">←</button>
            <div class="sub-models-container">
                <?php foreach ($subModels as $idx => $subModel): ?>
                <div class="sub-model-card" style="transition-delay: <?= $idx * 0.1 ?>s">
                    <div class="sub-model-fuel">
                        <?= htmlspecialchars($subModel['fuel_type'] ?? 'Gasoline') ?>
                        <span class="checkmark">✓</span>
                    </div>
                    <img src="<?= htmlspecialchars($subModel['image']) ?>" alt="<?= htmlspecialchars($subModel['name']) ?>">
                    <h3><?= htmlspecialchars($subModel['name']) ?></h3>

                    <div class="sub-model-specs">
                        <?php if ($subModel['acceleration']): ?>
                        <?php
                            $accel = htmlspecialchars($subModel['acceleration']);
                            // Tambah 's' jika belum ada satuan
                            if (!preg_match('/[a-zA-Z]/', $accel)) $accel .= ' s';
                        ?>
                        <div class="spec-item">
                            <div class="spec-value"><?= $accel ?></div>
                            <div class="spec-label">Acceleration 0 – 100 km/h</div>
                        </div>
                        <?php endif; ?>

                        <?php if ($subModel['power_kw'] && $subModel['power_ps']): ?>
                        <div class="spec-item">
                            <div class="spec-value"><?= $subModel['power_kw'] ?> kW / <?= $subModel['power_ps'] ?> PS</div>
                            <div class="spec-label">Power (kW) / Power (PS)</div>
                        </div>
                        <?php endif; ?>

                        <?php if ($subModel['top_speed']): ?>
                        <?php
                            $speed = htmlspecialchars($subModel['top_speed']);
                            // Tambah 'km/h' jika belum ada satuan
                            if (!preg_match('/[a-zA-Z]/', $speed)) $speed .= ' km/h';
                        ?>
                        <div class="spec-item">
                            <div class="spec-value"><?= $speed ?></div>
                            <div class="spec-label">Top speed</div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="sub-model-actions">
                        <a href="<?= !empty($subModel['configurator_url']) ? htmlspecialchars($subModel['configurator_url']) : 'https://configurator.porsche.com/en-ID/' ?>"
                           target="_blank" class="btn-configure"><span>Configure</span></a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <button class="slider-arrow next" onclick="slideSubModels(1)">→</button>
        </div>

        <div class="slider-dots">
            <?php for ($i = 0; $i < count($subModels); $i++): ?>
            <span class="dot" onclick="currentSlide(<?= $i ?>)"></span>
            <?php endfor; ?>
        </div>
    </div>
    <?php endif; ?>

</div><!-- /.detail-content -->

<script>
/* ════════════════════════════
   INTRO CURTAIN
════════════════════════════ */
(function() {
    const intro = document.getElementById('intro');
    intro.style.display = 'flex';
    intro.style.opacity = '1';
    setTimeout(function() {
        intro.classList.add('open');
        setTimeout(function() {
            intro.classList.add('done');
            setTimeout(function() { intro.style.display = 'none'; }, 600);
        }, 1150);
    }, 900);
})();

/* ════════════════════════════
   CUSTOM CURSOR
════════════════════════════ */
const dot  = document.getElementById('cursor-dot');
const ring = document.getElementById('cursor-ring');
let mx = 0, my = 0, rx = 0, ry = 0;

window.addEventListener('mousemove', e => { mx = e.clientX; my = e.clientY; }, { passive: true });

document.addEventListener('mousedown', () => {
    document.body.classList.add('c-click');
    setTimeout(() => document.body.classList.remove('c-click'), 280);
});
document.documentElement.addEventListener('mouseleave', () => {
    dot.style.opacity = '0'; ring.style.opacity = '0';
});
document.documentElement.addEventListener('mouseenter', () => {
    dot.style.opacity = ''; ring.style.opacity = '';
});

(function tick() {
    rx += (mx - rx) * 0.16;
    ry += (my - ry) * 0.16;
    dot.style.left  = mx + 'px';
    dot.style.top   = my + 'px';
    ring.style.left = rx + 'px';
    ring.style.top  = ry + 'px';
    requestAnimationFrame(tick);
})();

document.querySelectorAll('a, button, input, label').forEach(el => {
    el.addEventListener('mouseenter', () => document.body.classList.add('c-link'));
    el.addEventListener('mouseleave', () => document.body.classList.remove('c-link'));
});
document.querySelectorAll('.spec-card, .sub-model-card, .specs-right').forEach(c => {
    c.addEventListener('mouseenter', () => {
        document.body.classList.remove('c-link');
        document.body.classList.add('c-gold');
    });
    c.addEventListener('mouseleave', () => document.body.classList.remove('c-gold'));
});

/* ════════════════════════════
   SCROLL PROGRESS BAR
════════════════════════════ */
const progressEl = document.getElementById('progress');
window.addEventListener('scroll', () => {
    const pct = window.scrollY / (document.body.scrollHeight - window.innerHeight);
    progressEl.style.width = (pct * 100) + '%';
}, { passive: true });

/* ════════════════════════════
   SPEC CARD SPOTLIGHT
════════════════════════════ */
document.querySelectorAll('.spec-card').forEach(card => {
    const spot = card.querySelector('.spec-card-spotlight');
    card.addEventListener('mousemove', e => {
        const r = card.getBoundingClientRect();
        const px = ((e.clientX - r.left) / r.width)  * 100;
        const py = ((e.clientY - r.top)  / r.height) * 100;
        if (spot) { spot.style.setProperty('--mx', px + '%'); spot.style.setProperty('--my', py + '%'); }
    });
});

/* ════════════════════════════
   PARALLAX — spec hero bg layer
════════════════════════════ */
const specHeroBgs = document.querySelectorAll('.spec-hero-bg-layer');
window.addEventListener('scroll', () => {
    specHeroBgs.forEach(bg => {
        const section = bg.closest('.spec-hero-section');
        if (!section) return;
        const rect = section.getBoundingClientRect();
        const progress = -rect.top / window.innerHeight;
        bg.style.transform = `translateY(${progress * 15}%)`;
    });
}, { passive: true });

/* ════════════════════════════
   SCROLL REVEAL
════════════════════════════ */
const revealObs = new IntersectionObserver(entries => {
    entries.forEach(e => {
        if (e.isIntersecting) {
            e.target.classList.add('reveal-done');
            revealObs.unobserve(e.target);
        }
    });
}, { threshold: 0.1 });

document.querySelectorAll(
    '.spec-box, .video-title, .video-container, .sound-section-detail, ' +
    '.sound-section-content, .gallery-img-1, .gallery-text, .gallery-img-2, ' +
    '.gallery-img-3, .sub-models-title, .sub-model-card'
).forEach(el => revealObs.observe(el));

document.querySelectorAll('.specs-left .spec-box').forEach((box, i) => {
    box.style.transitionDelay = `${0.1 + i * 0.15}s`;
    revealObs.observe(box);
});

document.querySelectorAll('.spec-box[data-fill]').forEach(box => {
    const fill = parseFloat(box.getAttribute('data-fill')) || 0.7;
    const bar = box.querySelector('.spec-bar-fill');
    if (bar) bar.style.setProperty('--fill', fill);
});

const cardObs = new IntersectionObserver(entries => {
    entries.forEach(e => {
        if (e.isIntersecting) {
            e.target.classList.add('card-revealed');
            cardObs.unobserve(e.target);
        }
    });
}, { threshold: 0.05 });

document.querySelectorAll('.spec-card').forEach(c => cardObs.observe(c));

/* ════════════════════════════
   COUNTER ANIMATION
════════════════════════════ */
const counters = document.querySelectorAll('.counter');
let counterAnimated = false;

const counterObs = new IntersectionObserver(entries => {
    entries.forEach(e => {
        if (e.isIntersecting && !counterAnimated) {
            counterAnimated = true;
            counters.forEach(counter => {
                const target = parseFloat(counter.getAttribute('data-target'));
                const isDecimal = target % 1 !== 0;
                const duration = 2000;
                const steps = 80;
                let step = 0;
                const easeOut = t => 1 - Math.pow(1 - t, 3);
                const update = () => {
                    step++;
                    const progress = easeOut(step / steps);
                    const current = target * progress;
                    if (step < steps) {
                        counter.textContent = isDecimal ? current.toFixed(1) : Math.floor(current);
                        setTimeout(update, duration / steps);
                    } else {
                        counter.textContent = isDecimal ? target.toFixed(1) : Math.floor(target);
                    }
                };
                update();
            });
            counterObs.disconnect();
        }
    });
}, { threshold: 0.3 });

const specsGrid = document.querySelector('.specs-grid');
if (specsGrid) counterObs.observe(specsGrid);

/* ════════════════════════════
   SUB MODELS SLIDER
════════════════════════════ */
let currentIndex = 0;
const cards = document.querySelectorAll('.sub-model-card');
const dots  = document.querySelectorAll('.dot');
const container = document.querySelector('.sub-models-container');

function updateSlider() {
    if (!container || cards.length === 0) return;
    const cardWidth = cards[0].offsetWidth + 40;
    container.style.transform = `translateX(-${currentIndex * cardWidth}px)`;
    dots.forEach((dot, i) => dot.classList.toggle('active', i === currentIndex));
}

function slideSubModels(direction) {
    currentIndex = (currentIndex + direction + cards.length) % cards.length;
    updateSlider();
}

function currentSlide(index) {
    currentIndex = index;
    updateSlider();
}

if (dots.length > 0) dots[0].classList.add('active');

/* ════════════════════════════
   VIDEO TOGGLE
════════════════════════════ */
function toggleVideo() {
    const video = document.getElementById('modelVideo');
    const pauseIcon = document.querySelector('.pause-icon');
    const playIcon  = document.querySelector('.play-icon');
    if (video.paused) {
        video.play();
        pauseIcon.style.display = 'block';
        playIcon.style.display  = 'none';
    } else {
        video.pause();
        pauseIcon.style.display = 'none';
        playIcon.style.display  = 'block';
    }
}

/* ════════════════════════════
   AUDIO TOGGLE
════════════════════════════ */
function toggleAudio() {
    const audio = document.getElementById('modelAudio');
    const icon  = document.getElementById('audioIcon');
    const text  = document.getElementById('audioText');
    if (audio.paused) {
        audio.play();
        icon.className = 'fas fa-pause';
        text.textContent = 'Pause sound';
    } else {
        audio.pause();
        icon.className = 'fas fa-play';
        text.textContent = 'Listen to sound';
    }
}

/* ════════════════════════════
   CONTENT AUDIO (hold)
════════════════════════════ */
function playContentAudio() {
    const audio = document.getElementById('contentAudio');
    const icon  = document.getElementById('contentAudioIcon');
    if (audio) { audio.currentTime = 0; audio.play(); icon.className = 'fas fa-pause'; }
}

function pauseContentAudio() {
    const audio = document.getElementById('contentAudio');
    const icon  = document.getElementById('contentAudioIcon');
    if (audio) { audio.pause(); icon.className = 'fas fa-play'; }
}

/* ════════════════════════════
   SPEC CARDS HORIZONTAL
════════════════════════════ */
function toggleCardExpand(button) {
    const card        = button.closest('.spec-card');
    const description = card.querySelector('.spec-card-description');
    const content     = card.querySelector('.spec-card-content');
    description.classList.toggle('expanded');
    content.classList.toggle('expanded');
}

function scrollSpecCards(direction) {
    const grid = document.getElementById('specCardsGrid');
    if (!grid) return;
    grid.scrollBy({ left: 620 * direction, behavior: 'smooth' });
    setTimeout(updateActiveDot, 400);
}

function scrollToCard(index) {
    const grid = document.getElementById('specCardsGrid');
    if (!grid) return;
    grid.scrollTo({ left: 620 * index, behavior: 'smooth' });
    updateActiveDot(index);
}

function updateActiveDot(activeIndex = null) {
    const grid = document.getElementById('specCardsGrid');
    const sdots = document.querySelectorAll('.spec-dot');
    if (!grid || sdots.length === 0) return;
    if (activeIndex === null) activeIndex = Math.round(grid.scrollLeft / 620);
    sdots.forEach((d, i) => d.classList.toggle('active', i === activeIndex));
}

document.addEventListener('DOMContentLoaded', () => {
    const grid = document.getElementById('specCardsGrid');
    if (grid) {
        grid.addEventListener('scroll', () => updateActiveDot(), { passive: true });
        let touchStartX = 0;
        grid.addEventListener('touchstart', e => { touchStartX = e.changedTouches[0].screenX; });
        grid.addEventListener('touchend',   e => {
            const diff = touchStartX - e.changedTouches[0].screenX;
            if (Math.abs(diff) > 50) scrollSpecCards(diff >  0 ? 1 : -1);
        });
    }
});

document.addEventListener('keydown', e => {
    if (e.target.closest('.spec-cards-collage')) {
        if (e.key === 'ArrowLeft')  scrollSpecCards(-1);
        if (e.key === 'ArrowRight') scrollSpecCards(1);
    }
});
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>

</body>
</html>