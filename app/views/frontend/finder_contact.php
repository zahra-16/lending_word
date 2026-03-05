<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Dealership – <?= htmlspecialchars($vehicle['title']) ?></title>
    <link rel="icon" type="image/png" href="/lending_word/public/assets/images/porsche-logo.png">
    <link rel="stylesheet" href="/lending_word/public/assets/css/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
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

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --white:     #ffffff;
            --off:       #f6f6f3;
            --black:     #0a0a0a;
            --gray:      #888;
            --light:     #e6e6e0;
            --ease:      cubic-bezier(0.16, 1, 0.3, 1);
            --font-body: 'Porsche Next', Arial, sans-serif;
            --font-cond: 'Porsche Next', Arial, sans-serif;
        }

        html { cursor: none; scroll-behavior: smooth; }
        body { background: var(--white); color: var(--black); font-family: var(--font-body); font-weight: 300; overflow-x: hidden; }

        /* ── CURSOR ── */
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

        /* ── PROGRESS ── */
        #progress { position: fixed; top: 0; left: 0; height: 2px; width: 0; background: var(--gray); z-index: 8000; transition: width .1s linear; }

        /* ── NAVBAR ── */
        .navbar { background: transparent !important; transition: background .4s ease, box-shadow .4s ease; }
        .navbar.scrolled { background: rgba(255,255,255,0.92) !important; backdrop-filter: blur(16px); box-shadow: 0 1px 0 rgba(0,0,0,.07); }
        .navbar .navbar-brand, .navbar .navbar-menu a { color: var(--black) !important; filter: none !important; }
        .navbar-menu a::after { background: var(--black) !important; }

        @keyframes fadeUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        /* ── BACK NAV ── */
        .back-nav {
            padding: 140px 60px 0;
            opacity: 0;
            animation: fadeUp .7s .15s var(--ease) forwards;
        }
        .btn-back {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 10px 20px; background: transparent; border: 1.5px solid var(--light);
            color: var(--black); text-decoration: none;
            font-family: var(--font-body); font-size: 0.75rem; font-weight: 500; letter-spacing: .15em;
            transition: border-color .2s ease, background .2s ease;
        }
        .btn-back:hover { border-color: var(--black); background: var(--off); }

        /* ── PAGE HEADER ── */
        .page-header {
            padding: 30px 60px 50px;
            border-bottom: 1px solid var(--light);
            opacity: 0;
            animation: fadeUp .7s .25s var(--ease) forwards;
        }
        .page-eyebrow { font-family: var(--font-body); font-size: 10px; letter-spacing: .35em; color: var(--gray); margin-bottom: 14px; display: flex; align-items: center; gap: 12px; }
        .page-eyebrow::before { content: ''; display: block; width: 28px; height: 1px; background: var(--gray); }
        .page-header h1 { font-family: var(--font-cond); font-size: clamp(2.2rem,4vw,4rem); font-weight: 700; letter-spacing: .04em; line-height: .95; }

        /* ── LAYOUT ── */
        .contact-layout {
            max-width: 1400px; margin: 0 auto;
            padding: 60px 60px 100px;
            display: grid; grid-template-columns: 1fr 380px; gap: 80px; align-items: start;
            opacity: 0;
            animation: fadeUp .7s .35s var(--ease) forwards;
        }

        /* ── TABS ── */
        .contact-tabs { display: flex; border-bottom: 1px solid var(--light); margin-bottom: 50px; gap: 0; }
        .contact-tab {
            padding: 14px 0; margin-right: 36px;
            background: transparent; border: none; border-bottom: 2px solid transparent; margin-bottom: -1px;
            font-family: var(--font-body); font-size: 0.82rem; font-weight: 500; letter-spacing: .1em;
            cursor: none; color: var(--gray); text-decoration: none; display: inline-block;
            transition: color .2s ease, border-color .2s ease;
        }
        .contact-tab:hover { color: var(--black); }
        .contact-tab.active { color: var(--black); border-bottom-color: var(--black); }

        /* ── SECTION TITLE ── */
        .section-title { font-family: var(--font-cond); font-size: 1.8rem; font-weight: 600; letter-spacing: .03em; margin-bottom: 32px; }
        .section-sub { font-family: var(--font-body); font-size: 0.9rem; color: var(--gray); line-height: 1.7; margin-bottom: 32px; }

        /* ── FORM ── */
        .form-group { margin-bottom: 24px; }
        .form-group label { display: block; margin-bottom: 8px; font-family: var(--font-body); font-size: 0.75rem; font-weight: 500; letter-spacing: .1em; color: var(--gray); }
        .form-group label .required { color: #c00; }

        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="tel"],
        .form-group select,
        .form-group textarea {
            width: 100%; padding: 13px 16px;
            border: 1.5px solid var(--light); background: var(--white); color: var(--black);
            font-family: var(--font-body); font-size: 0.9rem; font-weight: 300;
            transition: border-color .2s ease; appearance: none; outline: none;
        }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color: var(--black); }
        .form-group textarea { min-height: 160px; resize: vertical; }
        .form-group select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%23333' stroke-width='1.5' fill='none'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 16px center; padding-right: 40px; cursor: none;
        }

        .name-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .phone-row { display: grid; grid-template-columns: 160px 1fr; gap: 12px; }

        /* ── PRIVACY ── */
        .privacy-group { background: var(--off); border: 1px solid var(--light); padding: 20px; margin-bottom: 28px; }
        .privacy-group label { display: flex; align-items: flex-start; gap: 12px; font-family: var(--font-body); font-size: 0.85rem; font-weight: 300; letter-spacing: 0; color: var(--black); cursor: none; margin-bottom: 0; }
        .privacy-group input[type="checkbox"] { width: 16px; height: 16px; flex-shrink: 0; margin-top: 2px; cursor: none; accent-color: var(--black); }
        .privacy-link { color: var(--black); font-weight: 500; }
        .privacy-info { margin-top: 12px; font-size: 0.78rem; color: var(--gray); line-height: 1.6; padding-left: 28px; }
        .privacy-info a { color: var(--black); }

        /* ── SUBMIT ── */
        .btn-submit {
            padding: 14px 40px; background: var(--black); color: var(--white);
            border: none; font-family: var(--font-body); font-size: 0.78rem; font-weight: 500; letter-spacing: .15em;
            cursor: none; position: relative; overflow: hidden; transition: box-shadow .3s ease;
        }
        .btn-submit::before { content: ''; position: absolute; inset: 0; background: rgba(255,255,255,.12); transform: scaleX(0); transform-origin: left; transition: transform .4s var(--ease); }
        .btn-submit:hover::before { transform: scaleX(1); }
        .btn-submit:hover { box-shadow: 0 8px 24px rgba(0,0,0,.22); }
        .mandatory-note { margin-top: 16px; font-family: var(--font-body); font-size: 0.78rem; color: var(--gray); }

        /* ── TIME SLOTS ── */
        .time-slots { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 28px; }
        .time-slot {
            border: 1.5px solid var(--light); padding: 14px 16px;
            font-family: var(--font-body); font-size: 0.82rem; cursor: none;
            transition: border-color .2s ease, background .2s ease;
            display: flex; align-items: center; gap: 10px;
        }
        .time-slot:hover { border-color: var(--black); }
        .time-slot input[type="radio"] { accent-color: var(--black); cursor: none; }
        .time-slot.selected { border-color: var(--black); background: var(--off); }

        /* ── SUCCESS ── */
        .success-message {
            background: #f1faf1; border: 1px solid #a5d6a7;
            padding: 28px 32px; margin-bottom: 36px; display: flex; align-items: flex-start; gap: 16px;
        }
        .success-message i { font-size: 1.4rem; color: #2e7d32; flex-shrink: 0; margin-top: 2px; }
        .success-message h3 { font-family: var(--font-cond); font-size: 1.2rem; font-weight: 600; letter-spacing: .02em; margin-bottom: 6px; color: #1b5e20; }
        .success-message p { font-family: var(--font-body); font-size: 0.85rem; color: #2e7d32; }

        /* ── PHONE TAB ── */
        .phone-item {
            display: flex; align-items: center; gap: 20px;
            border: 1.5px solid var(--light); padding: 22px 28px; margin-bottom: 14px;
            transition: border-color .2s ease;
        }
        .phone-item:hover { border-color: var(--black); }
        .phone-item-icon { font-size: 1.3rem; color: var(--gray); flex-shrink: 0; width: 24px; text-align: center; }
        .phone-item-label { font-family: var(--font-body); font-size: 0.7rem; letter-spacing: .15em; color: var(--gray); margin-bottom: 4px; }
        .phone-item-value { font-family: var(--font-cond); font-size: 1.3rem; font-weight: 500; letter-spacing: .02em; }
        .phone-item-value a { color: var(--black); text-decoration: none; }
        .phone-item-value a:hover { text-decoration: underline; }
        .phone-item.whatsapp { border-color: rgba(37,211,102,.3); }
        .phone-item.whatsapp .phone-item-icon { color: #25d366; }
        .phone-item.whatsapp .phone-item-value a { color: #1a8f47; }

        .hours-table { border: 1px solid var(--light); margin-top: 32px; }
        .hours-head { padding: 14px 20px; background: var(--off); border-bottom: 1px solid var(--light); font-family: var(--font-body); font-size: 0.7rem; letter-spacing: .2em; color: var(--gray); }
        .hours-row { display: flex; justify-content: space-between; padding: 12px 20px; border-bottom: 1px solid var(--light); font-family: var(--font-body); font-size: 0.85rem; }
        .hours-row:last-child { border-bottom: none; }
        .hours-row.today { background: var(--off); }
        .hours-row.today .hours-day, .hours-row.today .hours-time { font-weight: 600; }
        .hours-day { color: var(--gray); }
        .hours-time { text-align: right; line-height: 1.6; }
        .closed-text { color: var(--light); }

        /* ── SIDEBAR ── */
        .contact-sidebar { position: sticky; top: 100px; }

        .vehicle-card { border: 1px solid var(--light); overflow: hidden; margin-bottom: 20px; }
        .vehicle-card img { width: 100%; height: 220px; object-fit: cover; display: block; }
        .vehicle-card-info { padding: 20px 22px; }
        .vehicle-card-eyebrow { font-family: var(--font-body); font-size: 0.7rem; letter-spacing: .15em; color: var(--gray); margin-bottom: 8px; }
        .vehicle-card-title { font-family: var(--font-cond); font-size: 1.3rem; font-weight: 600; letter-spacing: .03em; margin-bottom: 10px; }
        .vehicle-card-price { font-family: var(--font-cond); font-size: 1.4rem; font-weight: 600; }

        .dealer-card { border: 1px solid var(--light); padding: 24px; }
        .dealer-name { font-family: var(--font-cond); font-size: 1.1rem; font-weight: 600; letter-spacing: .03em; margin-bottom: 12px; }
        .dealer-address { font-family: var(--font-body); font-size: 0.85rem; color: var(--gray); line-height: 1.7; margin-bottom: 20px; }

        /* ── RESPONSIVE ── */
        @media (max-width: 1024px) {
            .contact-layout { grid-template-columns: 1fr; gap: 40px; }
            .contact-sidebar { position: static; }
        }
        @media (max-width: 768px) {
            .back-nav { padding: 110px 24px 0; }
            .page-header { padding: 20px 24px 40px; }
            .contact-layout { padding: 40px 24px 80px; }
            .name-row { grid-template-columns: 1fr; }
            .phone-row { grid-template-columns: 130px 1fr; }
            .time-slots { grid-template-columns: 1fr; }
            html { cursor: auto; }
            #cursor-dot, #cursor-ring { display: none; }
            .btn-back, .btn-submit, .contact-tab, .time-slot, .btn-action { cursor: pointer; }
            .form-group select, .form-group input, .privacy-group input[type="checkbox"] { cursor: pointer; }
        }
    </style>
</head>
<body>

<div id="cursor-dot"></div>
<div id="cursor-ring"></div>
<div id="progress"></div>

<?php include __DIR__ . '/../partials/navbar.php'; ?>

<div class="back-nav">
    <a href="/lending_word/finder_detail.php?id=<?= $vehicle['id'] ?>" class="btn-back">
        <i class="fas fa-arrow-left"></i> Back to Vehicle
    </a>
</div>

<div class="page-header">
    <p class="page-eyebrow">Porsche Finder</p>
    <h1>Contact<br>Dealership</h1>
</div>

<div class="contact-layout">

    <!-- ── LEFT: Forms ── -->
    <div class="contact-main">

        <?php if ($success): ?>
        <div class="success-message">
            <i class="fas fa-check-circle"></i>
            <div>
                <h3>Inquiry Sent</h3>
                <p>Thank you for your interest. The dealership will contact you shortly.</p>
            </div>
        </div>
        <?php endif; ?>

        <!-- Tabs -->
        <div class="contact-tabs">
            <a href="?id=<?= $vehicle['id'] ?>&tab=message"  class="contact-tab <?= $tab === 'message'  ? 'active' : '' ?>">Write message</a>
            <a href="?id=<?= $vehicle['id'] ?>&tab=callback" class="contact-tab <?= $tab === 'callback' ? 'active' : '' ?>">Arrange callback</a>
            <a href="?id=<?= $vehicle['id'] ?>&tab=phone"    class="contact-tab <?= $tab === 'phone'    ? 'active' : '' ?>">Contact by phone</a>
        </div>

        <?php
        $countryCodes = [
            'ID +62'=>'+62','US +1'=>'+1','GB +44'=>'+44','AU +61'=>'+61',
            'SG +65'=>'+65','MY +60'=>'+60','DE +49'=>'+49','FR +33'=>'+33',
            'JP +81'=>'+81','CN +86'=>'+86','KR +82'=>'+82','IN +91'=>'+91',
            'SA +966'=>'+966','AE +971'=>'+971','HK +852'=>'+852',
        ];
        ?>

        <!-- ═══ TAB: Write Message ═══ -->
        <?php if ($tab === 'message'): ?>
        <div class="contact-form-section">
            <h2 class="section-title">Your inquiry to<br><?= htmlspecialchars($center['name'] ?? 'Porsche Centre') ?></h2>
            <form method="POST">
                <input type="hidden" name="action"       value="send_inquiry">
                <input type="hidden" name="vehicle_id"   value="<?= $vehicle['id'] ?>">
                <input type="hidden" name="center_id"    value="<?= $vehicle['center_id'] ?? '' ?>">
                <input type="hidden" name="inquiry_type" value="message">

                <div class="form-group">
                    <label>Your message</label>
                    <textarea name="message"><?= htmlspecialchars($_POST['message'] ?? 'Dear Porsche Center, I am interested in this Porsche.') ?></textarea>
                </div>

                <h3 style="font-family:var(--font-cond);font-size:1.2rem;font-weight:600;letter-spacing:.04em;margin:32px 0 24px;">Your Contact Details</h3>

                <div class="form-group">
                    <label>Salutation <span class="required">*</span></label>
                    <select name="salutation" required>
                        <option value="">Select…</option>
                        <?php foreach (['Mr','Mrs','Ms','Dr'] as $s): ?>
                        <option value="<?= $s ?>" <?= ($_POST['salutation'] ?? '') === $s ? 'selected' : '' ?>><?= $s ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="name-row">
                    <div class="form-group">
                        <label>First name <span class="required">*</span></label>
                        <input type="text" name="first_name" value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Last name <span class="required">*</span></label>
                        <input type="text" name="last_name" value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Email <span class="required">*</span></label>
                    <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label>Phone Number</label>
                    <div class="phone-row">
                        <select name="phone_country_code">
                            <?php foreach ($countryCodes as $label => $code): ?>
                            <option value="<?= $code ?>" <?= ($_POST['phone_country_code'] ?? '+62') === $code ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="tel" name="phone_number" value="<?= htmlspecialchars($_POST['phone_number'] ?? '') ?>" placeholder="e.g. 0812 345 678">
                    </div>
                </div>

                <div class="privacy-group">
                    <label>
                        <input type="checkbox" name="privacy_agreed" value="1" required <?= !empty($_POST['privacy_agreed']) ? 'checked' : '' ?>>
                        <span>I agree that my details are sent to the dealership. <a href="#" class="privacy-link">Privacy Information</a></span>
                    </label>
                    <div class="privacy-info">Your personal data will be processed in accordance with our <a href="#">Privacy Policy</a> and only used to respond to your inquiry.</div>
                </div>

                <button type="submit" class="btn-submit">Send non-binding enquiry</button>
                <p class="mandatory-note">Fields marked with <span style="color:#c00">*</span> are mandatory</p>
            </form>
        </div>

        <!-- ═══ TAB: Callback ═══ -->
        <?php elseif ($tab === 'callback'): ?>
        <div class="callback-section">
            <h2 class="section-title">Arrange a callback</h2>
            <p class="section-sub">Let us know when you'd like us to call you back and we'll do our best to reach you at your preferred time.</p>

            <form method="POST">
                <input type="hidden" name="action"       value="send_inquiry">
                <input type="hidden" name="vehicle_id"   value="<?= $vehicle['id'] ?>">
                <input type="hidden" name="center_id"    value="<?= $vehicle['center_id'] ?? '' ?>">
                <input type="hidden" name="inquiry_type" value="callback">

                <div class="form-group">
                    <label>Preferred callback time</label>
                    <div class="time-slots">
                        <?php foreach (['Morning (08:00–12:00)','Afternoon (12:00–15:00)','Late afternoon (15:00–18:00)','Any time during opening hours'] as $slot): ?>
                        <label class="time-slot">
                            <input type="radio" name="callback_time" value="<?= $slot ?>"> <?= $slot ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label>Your message (optional)</label>
                    <textarea name="message" placeholder="Add any additional information here…"></textarea>
                </div>

                <h3 style="font-family:var(--font-cond);font-size:1.2rem;font-weight:600;letter-spacing:.04em;margin:32px 0 24px;">Your Contact Details</h3>

                <div class="form-group">
                    <label>Salutation <span class="required">*</span></label>
                    <select name="salutation" required>
                        <option value="">Select…</option>
                        <?php foreach (['Mr','Mrs','Ms','Dr'] as $s): ?><option value="<?= $s ?>"><?= $s ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="name-row">
                    <div class="form-group">
                        <label>First name <span class="required">*</span></label>
                        <input type="text" name="first_name" required>
                    </div>
                    <div class="form-group">
                        <label>Last name <span class="required">*</span></label>
                        <input type="text" name="last_name" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Phone Number <span class="required">*</span></label>
                    <div class="phone-row">
                        <select name="phone_country_code">
                            <?php foreach ($countryCodes as $label => $code): ?>
                            <option value="<?= $code ?>" <?= $code === '+62' ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="tel" name="phone_number" required placeholder="e.g. 0812 345 678">
                    </div>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email">
                </div>
                <div class="privacy-group">
                    <label>
                        <input type="checkbox" name="privacy_agreed" value="1" required>
                        <span>I agree that my details are sent to the dealership. <a href="#" class="privacy-link">Privacy Information</a></span>
                    </label>
                </div>
                <button type="submit" class="btn-submit">Request callback</button>
                <p class="mandatory-note">Fields marked with <span style="color:#c00">*</span> are mandatory</p>
            </form>
        </div>

        <!-- ═══ TAB: Phone ═══ -->
        <?php elseif ($tab === 'phone'): ?>
        <div class="phone-contact-section">
            <h2 class="section-title">Contact by phone</h2>
            <p class="section-sub">Our team at <?= htmlspecialchars($center['name'] ?? 'the dealership') ?> is happy to answer your questions directly.</p>

            <?php if (!empty($center['phone'])): ?>
            <div class="phone-item">
                <div class="phone-item-icon"><i class="fas fa-phone"></i></div>
                <div>
                    <div class="phone-item-label">Phone</div>
                    <div class="phone-item-value"><a href="tel:<?= htmlspecialchars($center['phone']) ?>"><?= htmlspecialchars($center['phone']) ?></a></div>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($center['whatsapp'])): ?>
            <div class="phone-item whatsapp">
                <div class="phone-item-icon"><i class="fab fa-whatsapp"></i></div>
                <div>
                    <div class="phone-item-label">WhatsApp</div>
                    <div class="phone-item-value">
                        <a href="https://wa.me/<?= preg_replace('/[^0-9]/','',$center['whatsapp']) ?>" target="_blank">
                            <?= htmlspecialchars($center['whatsapp']) ?>
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($openingHours)): ?>
            <div class="hours-table">
                <div class="hours-head">Opening Hours</div>
                <?php $todayName = date('l'); foreach ($openingHours as $hour): $isToday = $hour['day_name'] === $todayName; ?>
                <div class="hours-row <?= $isToday ? 'today' : '' ?>">
                    <span class="hours-day"><?= htmlspecialchars($hour['day_name']) ?></span>
                    <span class="hours-time <?= $hour['is_closed'] ? 'closed-text' : '' ?>">
                        <?php if ($hour['is_closed']): ?>Closed
                        <?php else: ?>
                            <?= htmlspecialchars($hour['open_time']) ?> – <?= !empty($hour['lunch_start']) ? htmlspecialchars($hour['lunch_start']) : htmlspecialchars($hour['close_time']) ?>
                            <?php if (!empty($hour['lunch_end'])): ?><br><?= htmlspecialchars($hour['lunch_end']) ?> – <?= htmlspecialchars($hour['close_time']) ?><?php endif; ?>
                        <?php endif; ?>
                    </span>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

    </div>

    <!-- ── RIGHT: Sidebar ── -->
    <div class="contact-sidebar">
        <div class="vehicle-card">
            <img src="<?= htmlspecialchars($vehicle['main_image_url']) ?>" alt="<?= htmlspecialchars($vehicle['title']) ?>">
            <div class="vehicle-card-info">
                <div class="vehicle-card-eyebrow"><?= htmlspecialchars($vehicle['condition'] ?? '') ?></div>
                <div class="vehicle-card-title"><?= htmlspecialchars($vehicle['title']) ?></div>
                <div class="vehicle-card-price">Rp <?= number_format($vehicle['price'], 0, ',', '.') ?></div>
            </div>
        </div>

        <?php if (!empty($center)): ?>
        <div class="dealer-card">
            <div class="dealer-name"><?= htmlspecialchars($center['name']) ?></div>
            <div class="dealer-address">
                <?= nl2br(htmlspecialchars($center['address'] ?? '')) ?>
                <?php if (!empty($center['city'])): ?><br><?= htmlspecialchars($center['city']) ?><?php endif; ?>
            </div>

            <?php if (!empty($openingHours)): ?>
            <div class="hours-table">
                <div class="hours-head">Opening Hours</div>
                <?php $todayName = date('l'); foreach ($openingHours as $hour): $isToday = $hour['day_name'] === $todayName; ?>
                <div class="hours-row <?= $isToday ? 'today' : '' ?>">
                    <span class="hours-day"><?= htmlspecialchars($hour['day_name']) ?></span>
                    <span class="hours-time <?= $hour['is_closed'] ? 'closed-text' : '' ?>">
                        <?php if ($hour['is_closed']): ?>Closed
                        <?php else: ?>
                            <?= htmlspecialchars($hour['open_time']) ?> – <?= !empty($hour['lunch_start']) ? htmlspecialchars($hour['lunch_start']) : htmlspecialchars($hour['close_time']) ?>
                            <?php if (!empty($hour['lunch_end'])): ?><br><?= htmlspecialchars($hour['lunch_end']) ?> – <?= htmlspecialchars($hour['close_time']) ?><?php endif; ?>
                        <?php endif; ?>
                    </span>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>

<script>
/* ─── Cursor ─── */
const dot = document.getElementById('cursor-dot'), ring = document.getElementById('cursor-ring');
let mx = 0, my = 0, rx = 0, ry = 0;
window.addEventListener('mousemove', e => { mx = e.clientX; my = e.clientY; }, { passive: true });
(function tick() { rx += (mx-rx)*.16; ry += (my-ry)*.16; dot.style.cssText += `left:${mx}px;top:${my}px`; ring.style.cssText += `left:${rx}px;top:${ry}px`; requestAnimationFrame(tick); })();
document.querySelectorAll('a, button, input, label, select, textarea').forEach(el => {
    el.addEventListener('mouseenter', () => document.body.classList.add('c-link'));
    el.addEventListener('mouseleave', () => document.body.classList.remove('c-link'));
});

/* ─── Progress + navbar ─── */
const progressEl = document.getElementById('progress'), navbar = document.querySelector('.navbar');
window.addEventListener('scroll', () => {
    progressEl.style.width = (window.scrollY / (document.body.scrollHeight - window.innerHeight) * 100) + '%';
    navbar?.classList.toggle('scrolled', window.scrollY > 50);
}, { passive: true });

/* ─── Time slot selection ─── */
document.querySelectorAll('.time-slot input[type="radio"]').forEach(radio => {
    radio.addEventListener('change', () => {
        document.querySelectorAll('.time-slot').forEach(s => s.classList.remove('selected'));
        radio.closest('.time-slot').classList.add('selected');
    });
});
</script>
</body>
</html>