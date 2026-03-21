<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Career — Porsche Indonesia</title>
    <link rel="icon" type="image/png" href="/lending_word/public/assets/images/porsche-logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/lending_word/public/assets/css/style.css">
    <style>
        @font-face {
            font-family: "Porsche Next";
            src: url("/lending_word/public/assets/fonts/Porsche Next.ttf") format("truetype");
            font-weight: 100 900; font-style: normal; font-display: swap;
        }
        *, *::before, *::after { box-sizing: border-box; }
        :root {
            --white: #ffffff; --off: #f6f6f3; --black: #0a0a0a; --gray: #888;
            --light: #e6e6e0; --grey-100: #f5f5f5; --grey-200: #e8e8e8;
            --grey-300: #d0d0d0; --grey-500: #888888; --grey-700: #444444;
            --gold: #666666; --gold-accent: #c9a84c; --gold-light: rgba(201,168,76,0.12);
            --ease: cubic-bezier(0.16, 1, 0.3, 1);
            --ease-back: cubic-bezier(0.34, 1.56, 0.64, 1);
            --font-porsche: "Porsche Next", "Arial Narrow", Arial, sans-serif;
        }
        html { cursor: none; scroll-behavior: smooth; }
        body { background: #fff; color: #000; font-family: var(--font-porsche); font-weight: 300; overflow-x: hidden; }

        /* CURSOR */
        #cursor-dot, #cursor-ring {
            position: fixed; pointer-events: none; z-index: 9999; border-radius: 50%;
            top: 0; left: 0; transform: translate(-50%, -50%);
            will-change: left, top, transform;
            transition-property: width, height, opacity, transform;
            transition-timing-function: var(--ease); mix-blend-mode: difference;
        }
        #cursor-dot { width: 8px; height: 8px; background: #ffffff; transition-duration: .2s,.2s,.2s,.15s; }
        #cursor-ring { width: 38px; height: 38px; border: 1.5px solid #ffffff; background: transparent; transition-duration: .35s,.35s,.3s,.22s; }
        body.c-link #cursor-dot  { width: 5px; height: 5px; }
        body.c-link #cursor-ring { width: 54px; height: 54px; }
        body.c-card #cursor-dot  { width: 10px; height: 10px; }
        body.c-card #cursor-ring { width: 54px; height: 54px; }
        body.c-click #cursor-dot  { transform: translate(-50%,-50%) scale(2.5); opacity: 0; }
        body.c-click #cursor-ring { transform: translate(-50%,-50%) scale(1.5); opacity: 0; }

        /* PROGRESS */
        #progress { position: fixed; top: 0; left: 0; height: 2px; width: 0; background: var(--black); z-index: 8000; transition: width .1s linear; }

        /* INTRO */
        #intro { position: fixed; inset: 0; z-index: 5000; display: flex; align-items: center; justify-content: center; background: #ffffff; transition: opacity .5s ease .1s; }
        #intro.done { opacity: 0; pointer-events: none; }
        .c-panel { position: absolute; top: 0; bottom: 0; width: 50%; background: #ffffff; z-index: 2; transition: transform 1.2s cubic-bezier(0.76,0,0.24,1); }
        .c-panel.l { left: 0; border-right: 1px solid rgba(0,0,0,0.08); }
        .c-panel.r { right: 0; border-left: 1px solid rgba(0,0,0,0.08); }
        #intro.open .c-panel.l { transform: translateX(-100%); }
        #intro.open .c-panel.r { transform: translateX(100%); }
        #intro-logo { position: relative; z-index: 1; opacity: 0; animation: wrdIn .6s .15s var(--ease) forwards; display: flex; align-items: center; justify-content: center; }
        #intro-logo img { width: clamp(80px,10vw,130px); height: auto; }
        @keyframes wrdIn { from{opacity:0;transform:translateY(10px);} to{opacity:1;transform:translateY(0);} }

        /* NAVBAR */
        .navbar { background: transparent !important; transition: background .4s ease, box-shadow .4s ease; }
        .navbar.scrolled { background: rgba(255,255,255,0.92) !important; backdrop-filter: blur(24px); -webkit-backdrop-filter: blur(24px); box-shadow: 0 1px 0 rgba(0,0,0,.07); }
        .navbar.scrolled .navbar-brand img { filter: brightness(0) !important; }
        .navbar.scrolled .navbar-menu a { color: var(--black) !important; }

        /* EYEBROW */
        .section-eyebrow { font-family: var(--font-porsche); font-size: 10px; letter-spacing: .35em; text-transform: uppercase; color: var(--gray); margin-bottom: 12px; display: flex; align-items: center; gap: 12px; }
        .section-eyebrow::before { content: ''; display: block; width: 28px; height: 1px; background: var(--gray); }

        /* HERO */
        .career-hero { position: relative; height: 100vh; min-height: 600px; display: flex; align-items: flex-end; overflow: hidden; background: #000; }
        .career-hero-bg { position: absolute; inset: 0; background-size: cover; background-position: center 30%; filter: brightness(0.52); transform: scale(1.05); transition: transform 8s ease; }
        .career-hero-bg.loaded { transform: scale(1); }
        .career-hero-content { position: relative; z-index: 2; padding: 0 80px 80px; max-width: 760px; }
        .career-hero-eyebrow { font-family: var(--font-porsche); font-size: 0.65rem; font-weight: 400; letter-spacing: 0.35em; text-transform: uppercase; color: rgba(255,255,255,0.6); margin-bottom: 16px; display: flex; align-items: center; gap: 12px; }
        .career-hero-eyebrow::before { content: ''; display: block; width: 28px; height: 1px; background: rgba(255,255,255,0.5); }
        .career-hero h1 { font-family: var(--font-porsche); font-size: clamp(2.4rem,5vw,4.5rem); font-weight: 700; color: #fff; line-height: 1.06; letter-spacing: .02em; margin-bottom: 20px; overflow: hidden; }
        .career-hero-word { display: inline-block; opacity: 0; transform: translateY(100%); animation: wrdUp 0.8s var(--ease) forwards; }
        .career-hero-word:nth-child(1){animation-delay:2.3s;} .career-hero-word:nth-child(2){animation-delay:2.45s;} .career-hero-word:nth-child(3){animation-delay:2.6s;} .career-hero-word:nth-child(4){animation-delay:2.75s;} .career-hero-word:nth-child(5){animation-delay:2.9s;}
        @keyframes wrdUp { to{opacity:1;transform:translateY(0);} }
        .career-hero p { font-family: var(--font-porsche); font-size: 1rem; font-weight: 300; color: rgba(255,255,255,0.65); line-height: 1.75; margin-bottom: 36px; max-width: 520px; opacity: 0; animation: fadeUp .8s 3s var(--ease) forwards; }
        @keyframes fadeUp { from{opacity:0;transform:translateY(16px);} to{opacity:1;transform:translateY(0);} }
        .career-hero-actions { display: flex; gap: 14px; flex-wrap: wrap; opacity: 0; animation: fadeUp .8s 3.1s var(--ease) forwards; }
        .btn-career-primary { display: inline-flex; align-items: center; gap: 8px; padding: 14px 32px; background: #fff; color: var(--black); font-family: var(--font-porsche); font-size: 0.73rem; font-weight: 400; letter-spacing: .2em; text-transform: uppercase; text-decoration: none; position: relative; overflow: hidden; transition: transform 0.2s; }
        .btn-career-primary::before { content: ''; position: absolute; inset: 0; background: var(--gold-accent); transform: translateY(101%); transition: transform .35s var(--ease); }
        .btn-career-primary:hover { transform: translateY(-2px); color: #fff; }
        .btn-career-primary:hover::before { transform: translateY(0); }
        .btn-career-primary span, .btn-career-primary i { position: relative; z-index: 1; }
        .btn-career-outline { display: inline-flex; align-items: center; gap: 8px; padding: 14px 32px; border: 1px solid rgba(255,255,255,0.35); color: #fff; font-family: var(--font-porsche); font-size: 0.73rem; font-weight: 400; letter-spacing: .2em; text-transform: uppercase; text-decoration: none; transition: border-color 0.2s, background 0.2s; }
        .btn-career-outline:hover { border-color: #fff; background: rgba(255,255,255,0.08); }

        /* TABS */
        .career-tabs-nav { position: sticky; top: 0; z-index: 200; background: var(--black); display: flex; border-bottom: 1px solid rgba(255,255,255,0.08); overflow-x: auto; scrollbar-width: none; }
        .career-tabs-nav::-webkit-scrollbar { display: none; }
        .career-tab-item { flex-shrink: 0; padding: 18px 28px; background: none; border: none; border-bottom: 2px solid transparent; color: rgba(255,255,255,0.4); font-family: var(--font-porsche); font-size: 0.68rem; font-weight: 400; letter-spacing: .2em; text-transform: uppercase; cursor: none; transition: color 0.18s, border-color 0.18s; }
        .career-tab-item:hover { color: rgba(255,255,255,0.75); }
        .career-tab-item.active { color: #fff; border-bottom-color: rgba(255,255,255,0.6); }

        /* SEARCH */
        .career-search-section { padding: 72px 80px 40px; background: #fff; }
        .career-search-section h2 { font-family: var(--font-porsche); font-size: clamp(2rem,3.5vw,3rem); font-weight: 700; color: var(--black); letter-spacing: .02em; margin-bottom: 8px; line-height: 1; }
        .career-search-section > p { font-family: var(--font-porsche); font-size: 0.88rem; color: var(--grey-500); font-weight: 300; margin-bottom: 32px; }
        .career-search-box { display: flex; max-width: 580px; border: 1px solid var(--grey-200); }
        .career-search-input { flex: 1; padding: 14px 20px; border: none; font-family: var(--font-porsche); font-size: 0.88rem; font-weight: 300; color: var(--black); background: #fff; outline: none; letter-spacing: .02em; }
        .career-search-input::placeholder { color: var(--grey-300); }
        .career-search-btn { padding: 14px 22px; background: var(--black); color: #fff; border: none; font-family: var(--font-porsche); font-size: 0.68rem; font-weight: 400; letter-spacing: .2em; text-transform: uppercase; cursor: none; transition: background 0.18s; }
        .career-search-btn:hover { background: var(--grey-700); }

        /* JOB LISTINGS */
        .career-jobs-section { padding: 0 80px 80px; background: #fff; }
        .career-jobs-section-hd { display: flex; align-items: baseline; gap: 14px; padding-bottom: 20px; border-bottom: 1px solid var(--grey-200); margin-bottom: 20px; }
        .career-jobs-section-hd h2 { font-family: var(--font-porsche); font-size: 0.68rem; font-weight: 400; letter-spacing: .2em; text-transform: uppercase; color: var(--black); }
        .career-jobs-count { font-family: var(--font-porsche); font-size: 0.73rem; color: var(--grey-500); }
        .career-filter-pills { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 28px; }
        .career-pill { padding: 8px 18px; border: 1px solid var(--grey-200); background: #fff; color: var(--grey-700); font-family: var(--font-porsche); font-size: 0.65rem; font-weight: 400; letter-spacing: .15em; text-transform: uppercase; cursor: none; transition: all 0.15s; display: inline-flex; align-items: center; gap: 6px; }
        .career-pill:hover { border-color: var(--black); color: var(--black); }
        .career-pill.active { background: var(--black); color: #fff; border-color: var(--black); }
        .career-pill .pill-dot { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; }
        .career-job-list { display: flex; flex-direction: column; }
        .career-job-item { display: flex; align-items: center; justify-content: space-between; gap: 24px; padding: 24px 0; border-bottom: 1px solid var(--grey-200); text-decoration: none; cursor: none; transition: all 0.15s; }
        .career-job-item:hover { background: var(--grey-100); padding-left: 16px; padding-right: 16px; margin: 0 -16px; }
        .career-job-meta { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; flex-wrap: wrap; }
        .career-job-category { font-family: var(--font-porsche); font-size: 0.58rem; font-weight: 400; letter-spacing: .18em; text-transform: uppercase; color: var(--gold-accent); background: var(--gold-light); padding: 3px 10px; }
        .career-job-badge-urgent { font-family: var(--font-porsche); font-size: 0.58rem; font-weight: 400; letter-spacing: .1em; text-transform: uppercase; color: #c0392b; background: rgba(192,57,43,0.08); padding: 3px 10px; }
        .career-job-badge-featured { font-family: var(--font-porsche); font-size: 0.58rem; font-weight: 400; letter-spacing: .1em; text-transform: uppercase; color: #185fa5; background: rgba(24,95,165,0.08); padding: 3px 10px; }
        .career-job-location, .career-job-type { font-family: var(--font-porsche); font-size: 0.7rem; color: var(--grey-500); font-weight: 300; display: flex; align-items: center; gap: 5px; }
        .career-job-title { font-family: var(--font-porsche); font-size: 1.05rem; font-weight: 600; color: var(--black); margin-bottom: 5px; letter-spacing: .01em; }
        .career-job-desc { font-family: var(--font-porsche); font-size: 0.78rem; color: var(--grey-500); line-height: 1.6; font-weight: 300; }
        .career-job-right { display: flex; flex-direction: column; align-items: flex-end; gap: 8px; flex-shrink: 0; }
        .career-job-arrow { width: 40px; height: 40px; border: 1px solid var(--grey-200); display: flex; align-items: center; justify-content: center; color: var(--black); font-size: 11px; transition: all 0.2s var(--ease); }
        .career-job-item:hover .career-job-arrow { background: var(--black); color: #fff; border-color: var(--black); }
        .career-job-deadline { font-family: var(--font-porsche); font-size: 0.68rem; color: var(--grey-500); font-weight: 300; white-space: nowrap; }
        .career-job-salary { font-family: var(--font-porsche); font-size: 0.73rem; color: var(--grey-700); font-weight: 400; }
        .career-empty { text-align: center; padding: 80px 20px; color: var(--grey-500); }
        .career-empty i { font-size: 2rem; opacity: 0.2; display: block; margin-bottom: 14px; }
        .career-empty p { font-family: var(--font-porsche); font-size: 0.88rem; font-weight: 300; letter-spacing: .02em; }

        /* CATEGORIES */
        .career-cats-section { padding: 80px; background: var(--grey-100); }
        .career-cats-section > .section-eyebrow { margin-bottom: 8px; }
        .career-cats-section h2 { font-family: var(--font-porsche); font-size: clamp(1.8rem,3vw,2.8rem); font-weight: 700; color: var(--black); letter-spacing: .02em; line-height: 1; margin-bottom: 40px; }
        .career-cats-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 1px; background: var(--grey-200); }
        .career-cat-card { border: none; padding: 32px 28px; background: #fff; display: flex; flex-direction: column; gap: 10px; transition: all 0.2s var(--ease); cursor: none; text-decoration: none; }
        .career-cat-card:hover { background: var(--black); }
        .career-cat-card:hover h4, .career-cat-card:hover p, .career-cat-card:hover .career-cat-count { color: rgba(255,255,255,0.7); }
        .career-cat-card:hover h4 { color: #fff; }
        .career-cat-icon { width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; margin-bottom: 6px; transition: color 0.2s; }
        .career-cat-card:hover .career-cat-icon { color: #fff !important; }
        .career-cat-card h4 { font-family: var(--font-porsche); font-size: 0.95rem; font-weight: 600; color: var(--black); letter-spacing: .01em; transition: color 0.2s; }
        .career-cat-card p { font-family: var(--font-porsche); font-size: 0.78rem; color: var(--grey-500); line-height: 1.65; font-weight: 300; flex: 1; transition: color 0.2s; }
        .career-cat-count { font-family: var(--font-porsche); font-size: 0.62rem; font-weight: 400; letter-spacing: .15em; color: var(--grey-500); text-transform: uppercase; transition: color 0.2s; }

        /* ENTRY */
        .career-entry-section { padding: 80px; background: #fff; }
        .career-entry-section > .section-eyebrow { margin-bottom: 8px; }
        .career-entry-section > h2 { font-family: var(--font-porsche); font-size: clamp(1.8rem,3vw,2.8rem); font-weight: 700; color: var(--black); letter-spacing: .02em; line-height: 1; margin-bottom: 10px; }
        .career-entry-section > p { font-family: var(--font-porsche); font-size: 0.92rem; color: var(--grey-500); font-weight: 300; margin-bottom: 36px; letter-spacing: .01em; }
        .career-subtabs { display: flex; border-bottom: 1px solid var(--grey-200); margin-bottom: 36px; }
        .career-subtab { padding: 12px 0; margin-right: 32px; background: none; border: none; border-bottom: 1px solid transparent; font-family: var(--font-porsche); font-size: 0.7rem; font-weight: 400; letter-spacing: .15em; text-transform: uppercase; color: var(--grey-500); cursor: none; transition: color 0.15s, border-color 0.15s; }
        .career-subtab:hover { color: var(--black); }
        .career-subtab.active { color: var(--black); border-bottom-color: var(--black); }
        .career-cards-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 1px; background: var(--grey-200); }
        .career-card { background: #fff; text-decoration: none; display: flex; flex-direction: column; transition: transform 0.22s var(--ease); opacity: 0; transform: translateY(24px); }
        .career-card.reveal-done { opacity: 1; transform: translateY(0); }
        .career-card:hover { transform: translateY(-4px); }
        .career-card-img-wrap { overflow: hidden; aspect-ratio: 16/10; }
        .career-card-img { width: 100%; height: 100%; object-fit: cover; filter: grayscale(30%); transition: transform 0.5s var(--ease), filter 0.4s ease; }
        .career-card:hover .career-card-img { transform: scale(1.05); filter: grayscale(0%); }
        .career-card-body { padding: 28px 24px; flex: 1; display: flex; flex-direction: column; }
        .career-card-tag { font-family: var(--font-porsche); font-size: 0.58rem; font-weight: 400; letter-spacing: .2em; text-transform: uppercase; color: var(--grey-500); margin-bottom: 10px; }
        .career-card-body h3 { font-family: var(--font-porsche); font-size: 1.05rem; font-weight: 600; color: var(--black); margin-bottom: 10px; line-height: 1.25; letter-spacing: .01em; }
        .career-card-body p { font-family: var(--font-porsche); font-size: 0.82rem; color: var(--grey-500); line-height: 1.7; flex: 1; font-weight: 300; }
        .career-card-link { margin-top: 20px; font-family: var(--font-porsche); font-size: 0.68rem; font-weight: 400; letter-spacing: .15em; color: var(--black); display: inline-flex; align-items: center; gap: 8px; text-transform: uppercase; }

        /* DREAM */
        .career-dream-section { padding: 80px; background: var(--black); }
        .career-dream-section > .section-eyebrow { color: rgba(255,255,255,0.4); margin-bottom: 8px; }
        .career-dream-section > .section-eyebrow::before { background: rgba(255,255,255,0.3); }
        .career-dream-section h2 { font-family: var(--font-porsche); font-size: clamp(1.8rem,3vw,2.8rem); font-weight: 700; color: #fff; letter-spacing: .02em; line-height: 1; margin-bottom: 48px; }
        .career-dream-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 1px; background: rgba(255,255,255,0.06); }
        .career-dream-card { background: var(--black); padding: 36px 32px; text-decoration: none; display: flex; flex-direction: column; gap: 12px; transition: background 0.2s; }
        .career-dream-card:hover { background: #111; }
        .career-dream-icon { width: 44px; height: 44px; border: 1px solid rgba(255,255,255,0.12); display: flex; align-items: center; justify-content: center; font-size: 1rem; color: rgba(255,255,255,0.6); margin-bottom: 8px; transition: all 0.2s; }
        .career-dream-card:hover .career-dream-icon { border-color: rgba(255,255,255,0.3); color: #fff; }
        .career-dream-card h4 { font-family: var(--font-porsche); font-size: 1rem; font-weight: 600; color: #fff; letter-spacing: .01em; }
        .career-dream-card p { font-family: var(--font-porsche); font-size: 0.8rem; color: rgba(255,255,255,0.4); line-height: 1.7; flex: 1; font-weight: 300; }
        .career-dream-card-arrow { font-size: 0.7rem; color: rgba(255,255,255,0.35); margin-top: 8px; transition: color 0.2s, transform 0.2s; }
        .career-dream-card:hover .career-dream-card-arrow { color: rgba(255,255,255,0.7); transform: translateX(4px); }

        /* SUBSIDIARIES */
        .career-subs-section { padding: 80px; background: var(--grey-100); }
        .career-subs-section > .section-eyebrow { margin-bottom: 8px; }
        .career-subs-hd { display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 40px; }
        .career-subs-hd h2 { font-family: var(--font-porsche); font-size: clamp(1.6rem,2.5vw,2.4rem); font-weight: 700; color: var(--black); letter-spacing: .02em; line-height: 1.1; max-width: 480px; }
        .career-subs-nav { display: flex; gap: 8px; }
        .career-subs-nav-btn { width: 44px; height: 44px; border: 1px solid var(--grey-300); background: #fff; display: flex; align-items: center; justify-content: center; cursor: none; font-size: 11px; transition: all 0.2s var(--ease); }
        .career-subs-nav-btn:hover { background: var(--black); color: #fff; border-color: var(--black); }
        .career-subs-track-wrap { overflow: hidden; }
        .career-subs-track { display: flex; gap: 16px; transition: transform 0.4s cubic-bezier(0.25,0.46,0.45,0.94); }
        .career-sub-card { flex: 0 0 calc(25% - 12px); min-width: 250px; background: #fff; text-decoration: none; transition: transform 0.22s var(--ease), box-shadow 0.22s; }
        .career-sub-card:hover { transform: translateY(-4px); box-shadow: 0 12px 40px rgba(0,0,0,0.1); }
        .career-sub-card-img { aspect-ratio: 16/11; overflow: hidden; }
        .career-sub-card-img img { width: 100%; height: 100%; object-fit: cover; filter: grayscale(25%); transition: transform 0.5s var(--ease), filter 0.3s; }
        .career-sub-card:hover .career-sub-card-img img { transform: scale(1.05); filter: grayscale(0%); }
        .career-sub-card-body { padding: 22px; }
        .career-sub-card-body h4 { font-family: var(--font-porsche); font-size: 0.92rem; font-weight: 600; color: var(--black); margin-bottom: 8px; letter-spacing: .01em; }
        .career-sub-card-body p { font-family: var(--font-porsche); font-size: 0.78rem; color: var(--grey-500); line-height: 1.6; margin-bottom: 14px; font-weight: 300; }
        .career-sub-link { font-family: var(--font-porsche); font-size: 0.65rem; font-weight: 400; letter-spacing: .15em; color: var(--black); text-transform: uppercase; display: inline-flex; align-items: center; gap: 6px; }

        /* SOCIAL */
        .career-social-section { padding: 80px; background: #fff; border-top: 1px solid var(--grey-200); }
        .career-social-section > .section-eyebrow { margin-bottom: 8px; }
        .career-social-section h2 { font-family: var(--font-porsche); font-size: clamp(2rem,4vw,3.5rem); font-weight: 700; color: var(--black); letter-spacing: .02em; line-height: 1; margin-bottom: 32px; }
        .career-social-icons { display: flex; gap: 12px; flex-wrap: wrap; }
        .career-social-icon { width: 48px; height: 48px; border: 1px solid var(--grey-200); display: flex; align-items: center; justify-content: center; color: var(--black); font-size: 15px; text-decoration: none; transition: all 0.2s var(--ease); }
        .career-social-icon:hover { background: var(--black); color: #fff; border-color: var(--black); }
        .career-note-section { padding: 32px 80px; background: var(--grey-100); border-top: 1px solid var(--grey-200); }
        .career-note-section h3 { font-family: var(--font-porsche); font-size: 0.6rem; font-weight: 400; letter-spacing: .2em; text-transform: uppercase; color: var(--grey-500); margin-bottom: 8px; }
        .career-note-section p { font-family: var(--font-porsche); font-size: 0.78rem; color: var(--grey-500); line-height: 1.7; max-width: 720px; font-weight: 300; }

        /* JOB DETAIL MODAL */
        .job-modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.65); z-index: 1000; display: flex; align-items: center; justify-content: center; padding: 24px; opacity: 0; pointer-events: none; transition: opacity 0.25s; backdrop-filter: blur(6px); }
        .job-modal-overlay.open { opacity: 1; pointer-events: all; }
        .job-modal { background: #fff; width: 100%; max-width: 620px; max-height: 88vh; overflow-y: auto; box-shadow: 0 30px 80px rgba(0,0,0,0.25); transform: translateY(20px); transition: transform 0.3s var(--ease); }
        .job-modal-overlay.open .job-modal { transform: translateY(0); }
        .job-modal-hd { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; padding: 28px 32px 22px; border-bottom: 1px solid var(--grey-200); position: sticky; top: 0; background: #fff; z-index: 2; }
        .job-modal-close { width: 36px; height: 36px; border: 1px solid var(--grey-200); background: #fff; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-size: 12px; color: var(--grey-700); cursor: none; transition: all 0.18s; }
        .job-modal-close:hover { background: var(--black); color: #fff; border-color: var(--black); }
        .job-modal-body { padding: 28px 32px; }
        .job-modal-meta { display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; }
        .job-modal-tag { font-family: var(--font-porsche); font-size: 0.58rem; font-weight: 400; letter-spacing: .18em; text-transform: uppercase; color: var(--gold-accent); background: var(--gold-light); padding: 4px 10px; }
        .job-modal-meta-item { font-family: var(--font-porsche); font-size: 0.75rem; color: var(--grey-500); font-weight: 300; display: flex; align-items: center; gap: 5px; }
        .job-modal-desc { font-family: var(--font-porsche); font-size: 0.88rem; color: var(--grey-700); line-height: 1.8; margin-bottom: 24px; font-weight: 300; white-space: pre-line; }
        .job-modal-requirements h4 { font-family: var(--font-porsche); font-size: 0.62rem; font-weight: 400; letter-spacing: .2em; text-transform: uppercase; color: var(--black); margin-bottom: 14px; }
        .job-modal-requirements ul { list-style: none; display: flex; flex-direction: column; gap: 8px; margin-bottom: 28px; }
        .job-modal-requirements li { font-family: var(--font-porsche); font-size: 0.84rem; color: var(--grey-700); font-weight: 300; padding-left: 18px; position: relative; line-height: 1.6; }
        .job-modal-requirements li::before { content: '→'; position: absolute; left: 0; color: var(--grey-500); }
        .job-modal-apply { display: inline-flex; align-items: center; gap: 8px; padding: 14px 32px; background: var(--black); color: #fff; font-family: var(--font-porsche); font-size: 0.7rem; font-weight: 400; letter-spacing: .2em; text-transform: uppercase; text-decoration: none; border: none; cursor: none; position: relative; overflow: hidden; transition: transform 0.2s; }
        .job-modal-apply::before { content: ''; position: absolute; inset: 0; background: var(--gold-accent); transform: translateY(101%); transition: transform .35s var(--ease); }
        .job-modal-apply:hover { transform: translateY(-2px); }
        .job-modal-apply:hover::before { transform: translateY(0); }
        .job-modal-apply span, .job-modal-apply i { position: relative; z-index: 1; }

        /* APPLY MODAL */
        .apply-modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.72); z-index: 1100; display: flex; align-items: center; justify-content: center; padding: 24px; opacity: 0; pointer-events: none; transition: opacity 0.25s; backdrop-filter: blur(8px); }
        .apply-modal-overlay.open { opacity: 1; pointer-events: all; }
        .apply-modal { background: #fff; width: 100%; max-width: 580px; max-height: 92vh; overflow-y: auto; box-shadow: 0 32px 80px rgba(0,0,0,0.28); transform: translateY(24px) scale(0.98); transition: transform 0.32s cubic-bezier(0.16,1,0.3,1); }
        .apply-modal-overlay.open .apply-modal { transform: translateY(0) scale(1); }
        .apply-modal-hd { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; padding: 28px 32px 22px; border-bottom: 1px solid var(--grey-200); position: sticky; top: 0; background: #fff; z-index: 2; }
        .apply-modal-eyebrow { font-family: var(--font-porsche); font-size: 0.58rem; font-weight: 400; letter-spacing: .22em; text-transform: uppercase; color: var(--gold-accent); margin-bottom: 6px; }
        .apply-modal-title { font-family: var(--font-porsche); font-size: 1.25rem; font-weight: 700; color: var(--black); line-height: 1.2; letter-spacing: .01em; }
        .apply-modal-subtitle { font-family: var(--font-porsche); font-size: 0.75rem; color: var(--grey-500); font-weight: 300; margin-top: 4px; }
        .apply-modal-close-btn { width: 36px; height: 36px; border: 1px solid var(--grey-200); background: #fff; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-size: 12px; color: var(--grey-700); cursor: none; transition: all 0.18s; }
        .apply-modal-close-btn:hover { background: var(--black); color: #fff; border-color: var(--black); }
        .apply-modal-body { padding: 28px 32px 32px; }
        .apply-form-group { margin-bottom: 18px; }
        .apply-form-group label { display: block; font-family: var(--font-porsche); font-size: 0.6rem; font-weight: 400; letter-spacing: .22em; text-transform: uppercase; color: var(--grey-700); margin-bottom: 8px; }
        .apply-form-group label .req { color: #c0392b; margin-left: 2px; }
        .apply-form-group input, .apply-form-group textarea { width: 100%; padding: 12px 16px; border: 1px solid var(--grey-200); background: #fafafa; font-family: var(--font-porsche); font-size: 0.85rem; font-weight: 300; color: var(--black); outline: none; transition: border-color 0.18s, background 0.18s; border-radius: 0; -webkit-appearance: none; }
        .apply-form-group input:focus, .apply-form-group textarea:focus { border-color: var(--black); background: #fff; }
        .apply-form-group input::placeholder, .apply-form-group textarea::placeholder { color: var(--grey-300); }
        .apply-form-group textarea { min-height: 100px; resize: vertical; }
        .apply-form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .apply-form-note { font-family: var(--font-porsche); font-size: 0.72rem; color: var(--grey-500); font-weight: 300; margin-top: 5px; line-height: 1.5; }
        .apply-divider { height: 1px; background: var(--grey-200); margin: 22px 0; }
        .apply-section-label { font-family: var(--font-porsche); font-size: 0.6rem; font-weight: 400; letter-spacing: .22em; text-transform: uppercase; color: var(--grey-500); margin-bottom: 14px; display: flex; align-items: center; gap: 10px; }
        .apply-section-label::after { content: ''; flex: 1; height: 1px; background: var(--grey-200); }
        .apply-submit-btn { display: flex; align-items: center; justify-content: center; gap: 10px; width: 100%; padding: 16px 32px; background: var(--black); color: #fff; font-family: var(--font-porsche); font-size: 0.72rem; font-weight: 400; letter-spacing: .22em; text-transform: uppercase; border: none; cursor: none; position: relative; overflow: hidden; transition: transform 0.2s; margin-top: 24px; }
        .apply-submit-btn::before { content: ''; position: absolute; inset: 0; background: var(--gold-accent); transform: translateY(101%); transition: transform .35s var(--ease); }
        .apply-submit-btn:hover { transform: translateY(-2px); }
        .apply-submit-btn:hover::before { transform: translateY(0); }
        .apply-submit-btn span, .apply-submit-btn i { position: relative; z-index: 1; }
        .apply-submit-btn:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }
        .apply-submit-btn:disabled::before { display: none; }
        .apply-toast { display: none; align-items: center; gap: 10px; padding: 14px 18px; margin-top: 16px; font-family: var(--font-porsche); font-size: 0.82rem; font-weight: 300; line-height: 1.5; }
        .apply-toast.success { display: flex; background: rgba(0,168,90,0.07); border: 1px solid rgba(0,168,90,0.2); color: #007a40; }
        .apply-toast.error   { display: flex; background: rgba(192,57,43,0.07); border: 1px solid rgba(192,57,43,0.2); color: #c0392b; }
        .apply-toast i { font-size: 1rem; flex-shrink: 0; }
        .apply-success-state { display: none; flex-direction: column; align-items: center; text-align: center; padding: 48px 32px; gap: 16px; }
        .apply-success-state.show { display: flex; }
        .apply-success-icon { width: 64px; height: 64px; border: 1px solid rgba(0,168,90,0.3); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: #007a40; margin-bottom: 8px; }
        .apply-success-state h3 { font-family: var(--font-porsche); font-size: 1.2rem; font-weight: 700; color: var(--black); letter-spacing: .01em; }
        .apply-success-state p { font-family: var(--font-porsche); font-size: 0.85rem; color: var(--grey-500); font-weight: 300; line-height: 1.7; max-width: 380px; }
        .apply-back-btn { display: inline-flex; align-items: center; gap: 8px; padding: 12px 28px; border: 1px solid var(--grey-200); background: #fff; color: var(--black); font-family: var(--font-porsche); font-size: 0.68rem; font-weight: 400; letter-spacing: .18em; text-transform: uppercase; cursor: none; margin-top: 8px; transition: all 0.2s; }
        .apply-back-btn:hover { background: var(--black); color: #fff; border-color: var(--black); }

        /* REVEAL */
        .reveal-item { opacity: 0; transform: translateY(24px); transition: opacity 0.7s var(--ease), transform 0.7s var(--ease); }
        .reveal-item.reveal-done { opacity: 1; transform: translateY(0); }

        /* RESPONSIVE */
        @media (max-width: 1100px) {
            .career-hero-content, .career-search-section, .career-entry-section,
            .career-dream-section, .career-jobs-section, .career-subs-section,
            .career-social-section, .career-note-section, .career-cats-section { padding-left: 40px; padding-right: 40px; }
            .career-cards-grid, .career-cats-grid { grid-template-columns: repeat(2,1fr); }
            .career-dream-grid { grid-template-columns: repeat(2,1fr); }
        }
        @media (max-width: 768px) {
            .career-hero-content, .career-search-section, .career-entry-section,
            .career-dream-section, .career-jobs-section, .career-subs-section,
            .career-social-section, .career-note-section, .career-cats-section { padding-left: 20px; padding-right: 20px; }
            .career-cards-grid, .career-cats-grid { grid-template-columns: 1fr; }
            .career-dream-grid { grid-template-columns: 1fr; }
            .career-hero h1 { font-size: 2.2rem; }
            .career-sub-card { flex-basis: calc(80% - 12px); }
            .job-modal-hd, .job-modal-body { padding: 20px; }
            .apply-form-row { grid-template-columns: 1fr; }
            .apply-modal-hd, .apply-modal-body { padding: 20px; }
            html { cursor: auto; }
            #cursor-dot, #cursor-ring { display: none; }
            .career-tab-item, .career-pill, .career-search-btn, .career-subtab,
            .career-subs-nav-btn, .job-modal-close, .job-modal-apply,
            .apply-modal-close-btn, .apply-submit-btn, .apply-back-btn { cursor: pointer; }
        }
    </style>
</head>
<body>

<div id="cursor-dot"></div>
<div id="cursor-ring"></div>

<div id="intro">
    <div class="c-panel l"></div>
    <div id="intro-logo">
        <img src="/lending_word/public/assets/images/porsche-logo.png" alt="Porsche">
    </div>
    <div class="c-panel r"></div>
</div>

<div id="progress"></div>

<nav class="navbar" id="navbar" style="position:fixed;top:0;left:0;right:0;z-index:500;">
    <div class="navbar-container">
        <a href="/lending_word/" class="navbar-brand">
            <img src="/lending_word/public/assets/images/porsche-logo2-png_seeklogo-314112-removebg-preview.png"
                 style="height:70px;filter:brightness(0) invert(1);" id="navLogo">
        </a>
        <ul class="navbar-menu">
            <?php foreach ($navbarLinks as $item): ?>
            <li>
                <a href="/lending_word/<?= htmlspecialchars($item['url']) ?>" style="color:#fff;" class="nav-link">
                    <?= htmlspecialchars($item['label']) ?>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
</nav>

<!-- ══ HERO (selalu fixed di atas) ══════════════════════════════════════════ -->
<section class="career-hero">
    <div class="career-hero-bg" id="heroBg"
         style="background-image:url('<?= $careerModel->getContent('hero_image','https://files.porsche.com/filestore/image/multimedia/none/careers-jobs/image/38c40f0e-97e3-11ed-80b6-005056bbdc38;sN;twebp/porsche-image.webp') ?>')"></div>
    <div class="career-hero-content">
        <div class="career-hero-eyebrow">Career</div>
        <?php
        $heroTitle = $careerModel->getContent('hero_title', "Shape the future of mobility.");
        $heroWords = explode(' ', $heroTitle);
        ?>
        <h1>
            <?php foreach ($heroWords as $word): ?>
            <span class="career-hero-word"><?= htmlspecialchars($word) ?>&nbsp;</span>
            <?php endforeach; ?>
        </h1>
        <p><?= $careerModel->getContent('hero_subtitle', 'Join Porsche Indonesia and be part of a team that lives and breathes performance, precision, and passion every single day.') ?></p>
        <div class="career-hero-actions">
            <a href="#vacancies" class="btn-career-primary">
                <i class="fas fa-search"></i>
                <span><?= $careerModel->getContent('hero_btn_primary', 'Find Job Vacancies') ?></span>
            </a>
            <a href="#entry" class="btn-career-outline">
                <?= $careerModel->getContent('hero_btn_secondary', 'Entry Opportunities') ?>
            </a>
        </div>
    </div>
</section>

<!-- ══ TABS NAV (selalu fixed di bawah hero) ════════════════════════════════ -->
<div class="career-tabs-nav" id="careerTabsNav">
    <button class="career-tab-item active" data-target="vacancies">Find job vacancies</button>
    <button class="career-tab-item" data-target="entry">Entry opportunities</button>
    <button class="career-tab-item" data-target="subsidiaries">Subsidiaries</button>
</div>

<?php
// ── Ambil urutan section dari DB ─────────────────────────────────────────────
$defaultOrder = 'vacancies,categories,entry,dream,subsidiaries,social,note';
$savedOrder   = $careerModel->getRawContent('section_order', $defaultOrder);
$sectionOrder = array_filter(explode(',', $savedOrder));

// Pastikan semua section ada (jaga-jaga kalau ada yg belum tersimpan)
$allSectionKeys = ['vacancies', 'categories', 'entry', 'dream', 'subsidiaries', 'social', 'note'];
foreach ($allSectionKeys as $k) {
    if (!in_array($k, $sectionOrder)) $sectionOrder[] = $k;
}

// ── Buffer tiap section ───────────────────────────────────────────────────────
$sections = [];

// ── vacancies ─────────────────────────────────────────────────────────────────
ob_start(); ?>
<section class="career-search-section" id="vacancies">
    <p class="section-eyebrow">Open positions</p>
    <h2><?= $careerModel->getContent('search_title', 'Find job vacancies') ?></h2>
    <p>Explore opportunities across departments and locations.</p>
    <div class="career-search-box">
        <input type="text" class="career-search-input" id="jobSearchInput"
               placeholder="<?= $careerModel->getContent('search_placeholder', 'Job title, location...') ?>"
               autocomplete="off">
        <button class="career-search-btn" onclick="filterJobs()">
            <i class="fas fa-search"></i> Search
        </button>
    </div>
</section>
<section class="career-jobs-section">
    <div class="career-jobs-section-hd">
        <h2>Open Positions</h2>
        <span class="career-jobs-count" id="jobsCount">
            <?= count($jobs) ?> position<?= count($jobs) !== 1 ? 's' : '' ?>
        </span>
    </div>
    <div class="career-filter-pills">
        <button class="career-pill active" data-filter="all" onclick="setFilter('all',this)">All</button>
        <?php foreach ($categories as $cat): ?>
        <button class="career-pill"
                data-filter="<?= htmlspecialchars($cat['slug']) ?>"
                onclick="setFilter('<?= htmlspecialchars($cat['slug']) ?>',this)">
            <span class="pill-dot" style="background:<?= htmlspecialchars($cat['color'] ?? '#888') ?>;"></span>
            <?= htmlspecialchars($cat['name']) ?>
        </button>
        <?php endforeach; ?>
    </div>
    <div class="career-job-list" id="jobList">
        <?php if (empty($jobs)): ?>
        <div class="career-empty">
            <i class="fas fa-briefcase"></i>
            <p>No open positions at the moment. Please check back later.</p>
        </div>
        <?php else: ?>
        <?php foreach ($jobs as $job): ?>
        <a class="career-job-item" href="javascript:void(0)"
           data-category="<?= htmlspecialchars($job['category_slug'] ?? 'all') ?>"
           data-title="<?= htmlspecialchars(strtolower($job['title'])) ?>"
           data-location="<?= htmlspecialchars(strtolower($job['location'] ?? '')) ?>"
           onclick='openJobModal(<?= htmlspecialchars(json_encode($job), ENT_QUOTES) ?>)'>
            <div>
                <div class="career-job-meta">
                    <span class="career-job-category"
                          style="color:<?= htmlspecialchars($job['category_color'] ?? '#c9a84c') ?>;background:<?= htmlspecialchars($job['category_color'] ?? '#c9a84c') ?>18;">
                        <?php if (!empty($job['category_icon'])): ?>
                        <i class="<?= htmlspecialchars($job['category_icon']) ?>" style="margin-right:4px;font-size:0.65em;"></i>
                        <?php endif; ?>
                        <?= htmlspecialchars($job['category_name'] ?? 'General') ?>
                    </span>
                    <?php if (!empty($job['is_featured'])): ?><span class="career-job-badge-featured">Featured</span><?php endif; ?>
                    <?php if (!empty($job['is_urgent'])): ?><span class="career-job-badge-urgent">Urgent</span><?php endif; ?>
                    <?php if (!empty($job['location'])): ?>
                    <span class="career-job-location"><i class="fas fa-location-dot"></i><?= htmlspecialchars($job['location']) ?></span>
                    <?php endif; ?>
                    <?php if (!empty($job['employment_type'])): ?>
                    <span class="career-job-type"><i class="fas fa-clock"></i><?= htmlspecialchars($job['employment_type']) ?></span>
                    <?php endif; ?>
                </div>
                <div class="career-job-title"><?= htmlspecialchars($job['title']) ?></div>
                <?php if (!empty($job['short_desc'])): ?>
                <div class="career-job-desc"><?= htmlspecialchars($job['short_desc']) ?></div>
                <?php endif; ?>
            </div>
            <div class="career-job-right">
                <div class="career-job-arrow"><i class="fas fa-arrow-right"></i></div>
                <?php if (!empty($job['salary_range'])): ?><div class="career-job-salary"><?= htmlspecialchars($job['salary_range']) ?></div><?php endif; ?>
                <?php if (!empty($job['deadline'])): ?><div class="career-job-deadline">Until <?= date('d M Y', strtotime($job['deadline'])) ?></div><?php endif; ?>
            </div>
        </a>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>
<?php $sections['vacancies'] = ob_get_clean();

// ── categories ────────────────────────────────────────────────────────────────
ob_start(); ?>
<?php if (!empty($categories)): ?>
<section class="career-cats-section">
    <p class="section-eyebrow">Departments</p>
    <h2>Explore by department</h2>
    <div class="career-cats-grid">
        <?php foreach ($categories as $cat):
            $catJobCount = count(array_filter($jobs, fn($j) => ($j['category_slug'] ?? '') === $cat['slug']));
        ?>
        <div class="career-cat-card" onclick="setFilterAndScroll('<?= htmlspecialchars($cat['slug']) ?>')">
            <div class="career-cat-icon" style="color:<?= htmlspecialchars($cat['color'] ?? '#888') ?>;">
                <i class="<?= htmlspecialchars($cat['icon'] ?? 'fas fa-briefcase') ?>"></i>
            </div>
            <h4><?= htmlspecialchars($cat['name']) ?></h4>
            <?php if (!empty($cat['description'])): ?><p><?= htmlspecialchars($cat['description']) ?></p><?php endif; ?>
            <span class="career-cat-count"><?= $catJobCount ?> open position<?= $catJobCount !== 1 ? 's' : '' ?></span>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>
<?php $sections['categories'] = ob_get_clean();

// ── entry ─────────────────────────────────────────────────────────────────────
ob_start(); ?>
<section class="career-entry-section" id="entry">
    <p class="section-eyebrow">Early career</p>
    <h2><?= $careerModel->getContent('entry_title', 'Your first step into Porsche') ?></h2>
    <p><?= $careerModel->getContent('entry_subtitle', 'We offer numerous opportunities — no matter where you are in your career journey.') ?></p>
    <div class="career-subtabs">
        <button class="career-subtab active" data-subtab="students" onclick="setSubtab('students',this)">Students &amp; graduates</button>
        <button class="career-subtab" data-subtab="experienced" onclick="setSubtab('experienced',this)">Experienced professionals</button>
    </div>
    <div class="career-cards-grid" id="subtabStudents">
        <?php foreach ($entryStudents as $i => $card): ?>
        <a class="career-card reveal-item" href="<?= htmlspecialchars($card['link_url'] ?? '#') ?>" style="transition-delay:<?= $i*0.1 ?>s;">
            <div class="career-card-img-wrap">
                <img class="career-card-img" src="<?= htmlspecialchars($card['image'] ?? '') ?>" alt="<?= htmlspecialchars($card['title']) ?>" loading="lazy" onerror="this.src='https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=800&q=80';this.onerror=null;">
            </div>
            <div class="career-card-body">
                <?php if (!empty($card['tag'])): ?><div class="career-card-tag"><?= htmlspecialchars($card['tag']) ?></div><?php endif; ?>
                <h3><?= htmlspecialchars($card['title']) ?></h3>
                <?php if (!empty($card['description'])): ?><p><?= htmlspecialchars($card['description']) ?></p><?php endif; ?>
                <span class="career-card-link">Learn more <i class="fas fa-arrow-right"></i></span>
            </div>
        </a>
        <?php endforeach; ?>
        <?php if (empty($entryStudents)): ?><p style="color:var(--grey-500);grid-column:1/-1;padding:20px 0;font-family:var(--font-porsche);font-weight:300;">No student opportunities listed at this time.</p><?php endif; ?>
    </div>
    <div class="career-cards-grid" id="subtabExperienced" style="display:none;">
        <?php foreach ($entryExperienced as $i => $card): ?>
        <a class="career-card reveal-item" href="<?= htmlspecialchars($card['link_url'] ?? '#') ?>" style="transition-delay:<?= $i*0.1 ?>s;">
            <div class="career-card-img-wrap">
                <img class="career-card-img" src="<?= htmlspecialchars($card['image'] ?? '') ?>" alt="<?= htmlspecialchars($card['title']) ?>" loading="lazy" onerror="this.src='https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=800&q=80';this.onerror=null;">
            </div>
            <div class="career-card-body">
                <?php if (!empty($card['tag'])): ?><div class="career-card-tag"><?= htmlspecialchars($card['tag']) ?></div><?php endif; ?>
                <h3><?= htmlspecialchars($card['title']) ?></h3>
                <?php if (!empty($card['description'])): ?><p><?= htmlspecialchars($card['description']) ?></p><?php endif; ?>
                <span class="career-card-link">Learn more <i class="fas fa-arrow-right"></i></span>
            </div>
        </a>
        <?php endforeach; ?>
        <?php if (empty($entryExperienced)): ?><p style="color:var(--grey-500);grid-column:1/-1;padding:20px 0;font-family:var(--font-porsche);font-weight:300;">No experienced opportunities listed at this time.</p><?php endif; ?>
    </div>
</section>
<?php $sections['entry'] = ob_get_clean();

// ── dream ─────────────────────────────────────────────────────────────────────
ob_start(); ?>
<section class="career-dream-section">
    <p class="section-eyebrow">Navigate</p>
    <h2><?= $careerModel->getContent('dream_title', 'Find your dream job at Porsche') ?></h2>
    <div class="career-dream-grid">
        <a href="#vacancies" class="career-dream-card">
            <div class="career-dream-icon"><i class="fas fa-search"></i></div>
            <h4>Find job vacancies</h4>
            <p>Browse all open positions across departments and locations.</p>
            <span class="career-dream-card-arrow"><i class="fas fa-arrow-right"></i></span>
        </a>
        <a href="<?= $careerModel->getRawContent('faq_url','#') ?>" class="career-dream-card">
            <div class="career-dream-icon"><i class="fas fa-circle-question"></i></div>
            <h4>Frequently asked questions</h4>
            <p>Everything you need to know about the application process.</p>
            <span class="career-dream-card-arrow"><i class="fas fa-arrow-right"></i></span>
        </a>
        <a href="tel:<?= preg_replace('/[^0-9+]/', '', $careerModel->getRawContent('hotline_number','+62215551234')) ?>" class="career-dream-card">
            <div class="career-dream-icon"><i class="fas fa-phone"></i></div>
            <h4><?= $careerModel->getContent('hotline_label','Application hotline') ?></h4>
            <p><?= $careerModel->getContent('hotline_hours','Monday–Friday from 9 am to 5 pm') ?><br><?= $careerModel->getContent('hotline_number','+62 21 555 1234') ?></p>
            <span class="career-dream-card-arrow"><i class="fas fa-arrow-right"></i></span>
        </a>
    </div>
</section>
<?php $sections['dream'] = ob_get_clean();

// ── subsidiaries ──────────────────────────────────────────────────────────────
ob_start(); ?>
<section class="career-subs-section" id="subsidiaries">
    <p class="section-eyebrow">Network</p>
    <div class="career-subs-hd">
        <h2><?= $careerModel->getContent('subs_title','Entry into subsidiaries and dealer organisations') ?></h2>
        <?php if (count($subsidiaries) > 4): ?>
        <div class="career-subs-nav">
            <button class="career-subs-nav-btn" onclick="scrollSubs(-1)"><i class="fas fa-chevron-left"></i></button>
            <button class="career-subs-nav-btn" onclick="scrollSubs(1)"><i class="fas fa-chevron-right"></i></button>
        </div>
        <?php endif; ?>
    </div>
    <div class="career-subs-track-wrap">
        <div class="career-subs-track" id="subsTrack">
            <?php foreach ($subsidiaries as $sub): ?>
            <a class="career-sub-card" href="<?= htmlspecialchars($sub['link_url'] ?? '#') ?>" target="_blank" rel="noopener">
                <div class="career-sub-card-img">
                    <img src="<?= htmlspecialchars($sub['image'] ?? '') ?>" alt="<?= htmlspecialchars($sub['name']) ?>" loading="lazy" onerror="this.src='https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600&q=80';this.onerror=null;">
                </div>
                <div class="career-sub-card-body">
                    <h4><?= htmlspecialchars($sub['name']) ?></h4>
                    <?php if (!empty($sub['description'])): ?><p><?= htmlspecialchars($sub['description']) ?></p><?php endif; ?>
                    <span class="career-sub-link">Learn more <i class="fas fa-arrow-right"></i></span>
                </div>
            </a>
            <?php endforeach; ?>
            <?php if (empty($subsidiaries)): ?><p style="color:var(--grey-500);font-family:var(--font-porsche);font-weight:300;">No subsidiaries listed at this time.</p><?php endif; ?>
        </div>
    </div>
</section>
<?php $sections['subsidiaries'] = ob_get_clean();

// ── social ────────────────────────────────────────────────────────────────────
ob_start(); ?>
<section class="career-social-section">
    <p class="section-eyebrow">Connect</p>
    <h2><?= $careerModel->getContent('social_title','Follow us.') ?></h2>
    <div class="career-social-icons">
        <?php foreach ($socialLinks as $s): ?>
        <a href="<?= htmlspecialchars($s['url']) ?>" target="_blank" rel="noopener" class="career-social-icon">
            <i class="<?= htmlspecialchars($s['icon']) ?>"></i>
        </a>
        <?php endforeach; ?>
    </div>
</section>
<?php $sections['social'] = ob_get_clean();

// ── note ──────────────────────────────────────────────────────────────────────
ob_start(); ?>
<section class="career-note-section">
    <h3>Note</h3>
    <p><?= $careerModel->getContent('note_text','For reasons of better readability and without any intention of discrimination, only the masculine pronoun is used in this information. This includes all genders.') ?></p>
</section>
<?php $sections['note'] = ob_get_clean();

// ── Render sesuai urutan ──────────────────────────────────────────────────────
foreach ($sectionOrder as $key) {
    if (isset($sections[$key])) echo $sections[$key];
}
?>

<!-- JOB DETAIL MODAL -->
<div class="job-modal-overlay" id="jobModal" onclick="closeJobModal(event)">
    <div class="job-modal">
        <div class="job-modal-hd">
            <div>
                <div id="modalCategory" style="font-family:var(--font-porsche);font-size:0.58rem;font-weight:400;letter-spacing:0.18em;text-transform:uppercase;color:#c9a84c;margin-bottom:10px;"></div>
                <div id="modalTitle"    style="font-family:var(--font-porsche);font-size:1.4rem;font-weight:700;color:#0a0a0a;line-height:1.15;letter-spacing:.01em;">Job Title</div>
            </div>
            <button class="job-modal-close" onclick="document.getElementById('jobModal').classList.remove('open');document.body.style.overflow='';">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="job-modal-body">
            <div class="job-modal-meta"         id="modalMeta"></div>
            <div class="job-modal-desc"         id="modalDesc"></div>
            <div class="job-modal-requirements" id="modalReq"></div>
            <a href="javascript:void(0)" class="job-modal-apply" id="modalApply">
                <i class="fas fa-paper-plane"></i>
                <span>Apply Now</span>
            </a>
        </div>
    </div>
</div>

<!-- APPLY MODAL -->
<div class="apply-modal-overlay" id="applyModal">
    <div class="apply-modal">
        <div id="applyFormState">
            <div class="apply-modal-hd">
                <div>
                    <div class="apply-modal-eyebrow" id="applyModalCategory">Apply</div>
                    <div class="apply-modal-title"    id="applyModalTitle">Lamar Posisi</div>
                    <div class="apply-modal-subtitle" id="applyModalSubtitle">Isi form di bawah untuk mengirimkan lamaran Anda</div>
                </div>
                <button class="apply-modal-close-btn" id="applyModalCloseBtn" type="button" aria-label="Tutup">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="apply-modal-body">
                <form id="applyForm" novalidate>
                    <input type="hidden" id="applyJobId" name="job_id">
                    <div class="apply-section-label">Data Pribadi</div>
                    <div class="apply-form-row">
                        <div class="apply-form-group">
                            <label>Nama Lengkap <span class="req">*</span></label>
                            <input type="text" name="full_name" id="applyName" placeholder="John Doe" required autocomplete="name">
                        </div>
                        <div class="apply-form-group">
                            <label>Email <span class="req">*</span></label>
                            <input type="email" name="email" id="applyEmail" placeholder="john@email.com" required autocomplete="email">
                        </div>
                    </div>
                    <div class="apply-form-group">
                        <label>Nomor Telepon</label>
                        <input type="tel" name="phone" id="applyPhone" placeholder="+62 812 3456 7890" autocomplete="tel">
                    </div>
                    <div class="apply-divider"></div>
                    <div class="apply-section-label">Profil Online</div>
                    <div class="apply-form-group">
                        <label>LinkedIn URL</label>
                        <input type="url" name="linkedin_url" id="applyLinkedin" placeholder="https://linkedin.com/in/username">
                    </div>
                    <div class="apply-form-group">
                        <label>Portfolio / Website</label>
                        <input type="url" name="portfolio_url" id="applyPortfolio" placeholder="https://portofolio.com">
                        <div class="apply-form-note">Atau link Google Drive untuk CV / resume Anda.</div>
                    </div>
                    <div class="apply-divider"></div>
                    <div class="apply-section-label">Motivasi</div>
                    <div class="apply-form-group">
                        <label>Cover Letter</label>
                        <textarea name="cover_letter" id="applyCoverLetter" placeholder="Ceritakan mengapa Anda tertarik dengan posisi ini…"></textarea>
                    </div>
                    <div class="apply-toast" id="applyToast">
                        <i class="fas fa-circle-info"></i>
                        <span id="applyToastMsg"></span>
                    </div>
                    <button type="submit" class="apply-submit-btn" id="applySubmitBtn">
                        <i class="fas fa-paper-plane"></i>
                        <span>Kirim Lamaran</span>
                    </button>
                    <div style="margin-top:16px;text-align:center;">
                        <span style="font-family:var(--font-porsche);font-size:0.7rem;color:var(--grey-500);font-weight:300;">
                            Data Anda aman dan hanya digunakan untuk proses rekrutmen.
                        </span>
                    </div>
                </form>
            </div>
        </div>
        <div class="apply-success-state" id="applySuccessState">
            <div class="apply-success-icon"><i class="fas fa-check"></i></div>
            <h3>Lamaran Terkirim!</h3>
            <p id="applySuccessMsg">Terima kasih telah melamar. Tim HR kami akan meninjau lamaran Anda dan menghubungi Anda dalam waktu dekat.</p>
            <button class="apply-back-btn" id="applyBackBtn" type="button">
                <i class="fas fa-arrow-left"></i> Kembali ke Lowongan
            </button>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>

<script>
/* ── INTRO ── */
(function(){
    const intro=document.getElementById('intro');
    intro.style.display='flex'; intro.style.opacity='1';
    setTimeout(()=>{
        intro.classList.add('open');
        setTimeout(()=>{
            intro.classList.add('done');
            setTimeout(()=>{ intro.style.display='none'; },600);
        },1150);
    },900);
})();

/* ── CURSOR ── */
const dot=document.getElementById('cursor-dot'), ring=document.getElementById('cursor-ring');
let mx=0,my=0,rx=0,ry=0;
window.addEventListener('mousemove',e=>{mx=e.clientX;my=e.clientY;},{passive:true});
document.addEventListener('mousedown',()=>{ document.body.classList.add('c-click'); setTimeout(()=>document.body.classList.remove('c-click'),280); });
document.documentElement.addEventListener('mouseleave',()=>{dot.style.opacity='0';ring.style.opacity='0';});
document.documentElement.addEventListener('mouseenter',()=>{dot.style.opacity='';ring.style.opacity='';});
(function tick(){
    rx+=(mx-rx)*0.16; ry+=(my-ry)*0.16;
    dot.style.left=mx+'px'; dot.style.top=my+'px';
    ring.style.left=rx+'px'; ring.style.top=ry+'px';
    requestAnimationFrame(tick);
})();
function attachCursor(scope){
    (scope||document).querySelectorAll('a,button,input,label,textarea,select').forEach(el=>{
        el.addEventListener('mouseenter',()=>document.body.classList.add('c-link'));
        el.addEventListener('mouseleave',()=>document.body.classList.remove('c-link'));
    });
}
attachCursor();
document.querySelectorAll('.career-card,.career-cat-card,.career-sub-card,.career-dream-card').forEach(c=>{
    c.addEventListener('mouseenter',()=>{document.body.classList.remove('c-link');document.body.classList.add('c-card');});
    c.addEventListener('mouseleave',()=>document.body.classList.remove('c-card'));
});

/* ── PROGRESS ── */
const progressEl=document.getElementById('progress');
window.addEventListener('scroll',()=>{
    progressEl.style.width=(window.scrollY/(document.body.scrollHeight-window.innerHeight)*100)+'%';
},{passive:true});

/* ── NAVBAR ── */
const navbar=document.getElementById('navbar'),navLogo=document.getElementById('navLogo'),navLinks=document.querySelectorAll('.nav-link');
window.addEventListener('scroll',()=>{
    if(window.scrollY>60){navbar.classList.add('scrolled');navLogo.style.filter='brightness(0)';navLinks.forEach(l=>l.style.color='#0a0a0a');}
    else{navbar.classList.remove('scrolled');navLogo.style.filter='brightness(0) invert(1)';navLinks.forEach(l=>l.style.color='#fff');}
},{passive:true});

/* ── HERO BG ── */
setTimeout(()=>document.getElementById('heroBg').classList.add('loaded'),100);

/* ── TABS ── */
document.querySelectorAll('.career-tab-item').forEach(btn=>{
    btn.addEventListener('click',()=>{
        document.querySelectorAll('.career-tab-item').forEach(b=>b.classList.remove('active'));
        btn.classList.add('active');
        const t=document.getElementById(btn.dataset.target);
        if(t) t.scrollIntoView({behavior:'smooth',block:'start'});
    });
});

/* ── SUBTABS ── */
function setSubtab(tab,el){
    document.querySelectorAll('.career-subtab').forEach(b=>b.classList.remove('active'));
    el.classList.add('active');
    document.getElementById('subtabStudents').style.display    = tab==='students'    ?'':'none';
    document.getElementById('subtabExperienced').style.display = tab==='experienced' ?'':'none';
}

/* ── FILTER ── */
let currentFilter='all', currentSearch='';
function setFilter(filter,el){
    currentFilter=filter;
    document.querySelectorAll('.career-pill').forEach(p=>p.classList.remove('active'));
    el.classList.add('active');
    applyFilters();
}
function setFilterAndScroll(filter){
    currentFilter=filter;
    document.querySelectorAll('.career-pill').forEach(p=>p.classList.remove('active'));
    const btn=document.querySelector(`.career-pill[data-filter="${filter}"]`);
    if(btn) btn.classList.add('active');
    applyFilters();
    const vac=document.getElementById('vacancies');
    if(vac) vac.scrollIntoView({behavior:'smooth',block:'start'});
}
function filterJobs(){ currentSearch=document.getElementById('jobSearchInput').value.toLowerCase().trim(); applyFilters(); }
document.getElementById('jobSearchInput').addEventListener('input',()=>{ currentSearch=document.getElementById('jobSearchInput').value.toLowerCase().trim(); applyFilters(); });
document.getElementById('jobSearchInput').addEventListener('keydown',e=>{ if(e.key==='Enter') filterJobs(); });
function applyFilters(){
    const items=document.querySelectorAll('.career-job-item');
    let visible=0;
    items.forEach(item=>{
        const ok=(currentFilter==='all'||item.dataset.category===currentFilter)&&(!currentSearch||item.dataset.title.includes(currentSearch)||item.dataset.location.includes(currentSearch));
        item.style.display=ok?'':'none';
        if(ok) visible++;
    });
    document.getElementById('jobsCount').textContent=visible+' position'+(visible!==1?'s':'');
}

/* ── JOB DETAIL MODAL ── */
function openJobModal(job){
    document.getElementById('modalCategory').textContent=job.category_name||'General';
    document.getElementById('modalCategory').style.color=job.category_color||'#c9a84c';
    document.getElementById('modalTitle').textContent=job.title;
    document.getElementById('modalDesc').textContent=job.description||'No description available.';
    let metaHtml='';
    if(job.category_name) metaHtml+=`<span class="job-modal-tag" style="color:${job.category_color||'#c9a84c'};background:${job.category_color||'#c9a84c'}18;">${job.category_name}</span>`;
    if(job.location)         metaHtml+=`<span class="job-modal-meta-item"><i class="fas fa-location-dot"></i>${job.location}</span>`;
    if(job.employment_type)  metaHtml+=`<span class="job-modal-meta-item"><i class="fas fa-clock"></i>${job.employment_type}</span>`;
    if(job.experience_level) metaHtml+=`<span class="job-modal-meta-item"><i class="fas fa-layer-group"></i>${job.experience_level}</span>`;
    if(job.salary_range)     metaHtml+=`<span class="job-modal-meta-item"><i class="fas fa-money-bill-wave"></i>${job.salary_range}</span>`;
    if(job.deadline)         metaHtml+=`<span class="job-modal-meta-item"><i class="fas fa-calendar"></i>Until ${new Date(job.deadline).toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'})}</span>`;
    document.getElementById('modalMeta').innerHTML=metaHtml;
    let reqHtml='';
    if(job.requirements){
        const reqs=job.requirements.split('\n').filter(r=>r.trim());
        if(reqs.length) reqHtml=`<h4>Requirements</h4><ul>${reqs.map(r=>`<li>${r.replace(/^[-•]\s*/,'')}</li>`).join('')}</ul>`;
    }
    document.getElementById('modalReq').innerHTML=reqHtml;
    document.getElementById('modalApply').onclick=function(e){
        e.preventDefault();
        document.getElementById('jobModal').classList.remove('open');
        document.body.style.overflow='';
        openApplyModal(job);
    };
    document.getElementById('jobModal').classList.add('open');
    document.body.style.overflow='hidden';
}
function closeJobModal(e){
    if(e.target.id==='jobModal'){ document.getElementById('jobModal').classList.remove('open'); document.body.style.overflow=''; }
}

/* ── APPLY MODAL ── */
const applyOverlay=document.getElementById('applyModal');
const applyForm=document.getElementById('applyForm');
const applySubmitBtn=document.getElementById('applySubmitBtn');

function openApplyModal(job){
    document.getElementById('applyFormState').style.display='';
    document.getElementById('applySuccessState').classList.remove('show');
    document.getElementById('applyToast').style.display='none';
    applyForm.reset();
    applySubmitBtn.disabled=false;
    applySubmitBtn.querySelector('span').textContent='Kirim Lamaran';
    applySubmitBtn.querySelector('i').className='fas fa-paper-plane';
    document.getElementById('applyJobId').value=job.id||'';
    document.getElementById('applyModalCategory').textContent=job.category_name||'Lamar Posisi';
    document.getElementById('applyModalTitle').textContent=job.title||'Lamar Posisi';
    document.getElementById('applyModalSubtitle').textContent=[job.location,job.employment_type].filter(Boolean).join(' · ')||'Isi form untuk mengirimkan lamaran Anda';
    applyOverlay.classList.add('open');
    document.body.style.overflow='hidden';
    attachCursor(applyOverlay);
}
function closeApplyModal(){
    applyOverlay.classList.remove('open');
    document.body.style.overflow='';
}
applyOverlay.addEventListener('click',e=>{ if(e.target===applyOverlay) closeApplyModal(); });
document.getElementById('applyModalCloseBtn').addEventListener('click',closeApplyModal);
document.getElementById('applyBackBtn').addEventListener('click',closeApplyModal);
document.addEventListener('keydown',e=>{
    if(e.key==='Escape'){
        closeApplyModal();
        document.getElementById('jobModal').classList.remove('open');
        document.body.style.overflow='';
    }
});

applyForm.addEventListener('submit',async function(e){
    e.preventDefault();
    document.getElementById('applyToast').style.display='none';
    const name=document.getElementById('applyName').value.trim();
    const email=document.getElementById('applyEmail').value.trim();
    if(!name){ showApplyToast('Nama lengkap wajib diisi.','error'); document.getElementById('applyName').focus(); return; }
    if(!email||!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)){ showApplyToast('Masukkan alamat email yang valid.','error'); document.getElementById('applyEmail').focus(); return; }
    applySubmitBtn.disabled=true;
    applySubmitBtn.querySelector('span').textContent='Mengirim…';
    applySubmitBtn.querySelector('i').className='fas fa-spinner fa-spin';
    try{
        const res=await fetch('/lending_word/apply.php',{method:'POST',body:new FormData(applyForm)});
        const json=await res.json();
        if(json.success){
            document.getElementById('applyFormState').style.display='none';
            document.getElementById('applySuccessState').classList.add('show');
            document.getElementById('applySuccessMsg').textContent=json.message;
        } else {
            showApplyToast(json.message||'Terjadi kesalahan. Coba lagi.','error');
            applySubmitBtn.disabled=false;
            applySubmitBtn.querySelector('span').textContent='Kirim Lamaran';
            applySubmitBtn.querySelector('i').className='fas fa-paper-plane';
        }
    } catch(err){
        showApplyToast('Gagal terhubung ke server. Periksa koneksi Anda.','error');
        applySubmitBtn.disabled=false;
        applySubmitBtn.querySelector('span').textContent='Kirim Lamaran';
        applySubmitBtn.querySelector('i').className='fas fa-paper-plane';
    }
});

function showApplyToast(msg,type){
    const t=document.getElementById('applyToast');
    t.className='apply-toast '+(type||'error');
    document.getElementById('applyToastMsg').textContent=msg;
    t.querySelector('i').className=type==='success'?'fas fa-circle-check':'fas fa-circle-xmark';
    t.style.display='flex';
    t.scrollIntoView({behavior:'smooth',block:'nearest'});
}

/* ── SUBSIDIARIES ── */
let subsOffset=0;
function scrollSubs(dir){
    const track=document.getElementById('subsTrack');
    const card=track.querySelector('.career-sub-card');
    if(!card) return;
    const cardW=card.offsetWidth+16;
    const max=Math.max(0,(track.children.length-4)*cardW);
    subsOffset=Math.max(0,Math.min(subsOffset+dir*cardW,max));
    track.style.transform=`translateX(-${subsOffset}px)`;
}

/* ── SCROLL REVEAL ── */
const revealObs=new IntersectionObserver(entries=>{
    entries.forEach(e=>{ if(e.isIntersecting){ e.target.classList.add('reveal-done'); revealObs.unobserve(e.target); } });
},{threshold:0.08});
document.querySelectorAll('.reveal-item').forEach(el=>revealObs.observe(el));
</script>
</body>
</html>