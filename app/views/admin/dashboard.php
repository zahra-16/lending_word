<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — Porsche Indonesia</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /*
 * ============================================================
 * ADMIN PANEL — LIGHT GLASSMORPHISM THEME PATCH
 * Ganti seluruh blok <style> di admin.php dengan CSS ini
 * ============================================================
 */

:root{
    /* Backgrounds */
    --bg:  #dcdce8;           /* page background */
    --bg2: rgba(255,255,255,0.60); /* card / sidebar */
    --bg3: rgba(255,255,255,0.40); /* card header, hover */
    --bg4: rgba(255,255,255,0.28); /* input bg */
    --bg5: rgba(255,255,255,0.80); /* topbar */

    /* Borders */
    --b1: rgba(0,0,0,0.04);
    --b2: rgba(0,0,0,0.09);
    --b3: rgba(0,0,0,0.16);
    --b4: rgba(0,0,0,0.28);

    /* Text */
    --t1: #12121f;   /* headings */
    --t2: #4b4b6a;   /* body */
    --t3: #9090b0;   /* muted */
    --t4: #b8b8d0;   /* placeholder */

    /* Accents — dark ink (like iDraft's black buttons) */
    --gold:  #18181e;
    --gold2: #3a3a4a;
    --gold3: rgba(0,0,0,0.06);

    /* Status */
    --green: #00b894;
    --red:   #e17055;
    --blue:  #0984e3;
    --amber: #fdcb6e;

    /* Radii */
    --r1: 8px;
    --r2: 12px;
    --r3: 16px;
    --r4: 100px;
}

/* ── Page ─────────────────────────────────────────── */
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}

body{
    font-family:'DM Sans',sans-serif;
    /* Layered radial gradient mesh — matches reference soft grey-white blur */
    background:
        radial-gradient(ellipse at 15% 20%, rgba(200,200,230,0.55) 0%, transparent 55%),
        radial-gradient(ellipse at 85% 75%, rgba(210,205,235,0.50) 0%, transparent 55%),
        radial-gradient(ellipse at 50% 50%, rgba(230,228,240,0.40) 0%, transparent 70%),
        #d8d8e6;
    color:var(--t1);
    min-height:100vh;
    font-size:14px;
    line-height:1.6;
    -webkit-font-smoothing:antialiased;
    overflow-x:hidden;
}

/* Remove old dark glow pseudo */
body::before{ display:none; }

/* Scrollbar */
::-webkit-scrollbar{width:4px;height:4px;}
::-webkit-scrollbar-track{background:transparent;}
::-webkit-scrollbar-thumb{background:var(--b3);border-radius:4px;}

/* ── Topbar ───────────────────────────────────────── */
.topbar{
    position:sticky;top:0;z-index:300;
    height:62px;padding:0 36px;
    display:flex;align-items:center;justify-content:space-between;
    background:rgba(255,255,255,0.72);
    backdrop-filter:blur(28px) saturate(180%);
    border-bottom:1px solid var(--b2);
    box-shadow:0 1px 0 rgba(255,255,255,0.9) inset, 0 2px 12px rgba(0,0,0,0.06);
}
.topbar::after{
    content:'';position:absolute;bottom:-1px;left:0;right:0;height:1px;
    background:linear-gradient(90deg,transparent 5%,rgba(0,0,0,0.10) 40%,rgba(0,0,0,0.10) 60%,transparent 95%);
}

.brand{display:flex;align-items:center;gap:12px;text-decoration:none;}
.brand-mark{
    width:32px;height:32px;
    background:linear-gradient(140deg,#18181e,#3a3a4a);
    border:none;border-radius:var(--r2);
    display:flex;align-items:center;justify-content:center;
    font-size:12px;color:#fff;
    box-shadow:0 4px 12px rgba(0,0,0,0.22);
}
.brand-mark::before{ display:none; }
.brand-name{font-family:'Syne',sans-serif;font-size:0.95rem;font-weight:800;letter-spacing:0.1em;text-transform:uppercase;color:var(--t1);}
.brand-div{width:1px;height:18px;background:var(--b3);}
.brand-role{font-size:0.68rem;color:var(--t3);letter-spacing:0.07em;text-transform:uppercase;}

.topbar-actions{display:flex;align-items:center;gap:8px;}
.tpill{
    display:flex;align-items:center;gap:6px;
    padding:6px 14px;border-radius:var(--r4);
    font-size:0.73rem;font-weight:500;
    border:1px solid var(--b2);
    color:var(--t2);text-decoration:none;
    background:rgba(255,255,255,0.60);
    backdrop-filter:blur(8px);
    transition:all 0.18s;letter-spacing:0.02em;
}
.tpill:hover{border-color:var(--b3);color:var(--t1);background:rgba(255,255,255,0.85);}
.tpill.logout:hover{border-color:rgba(225,112,85,0.4);color:var(--red);background:rgba(225,112,85,0.06);}

/* ── Layout ───────────────────────────────────────── */
.layout{display:flex;min-height:calc(100vh - 62px);}

/* ── Sidebar ──────────────────────────────────────── */
.sidebar{
    width:210px;flex-shrink:0;
    background:rgba(255,255,255,0.52);
    backdrop-filter:blur(20px);
    border-right:1px solid var(--b2);
    padding:18px 8px;
    position:sticky;top:62px;
    height:calc(100vh - 62px);
    overflow-y:auto;
    box-shadow:2px 0 16px rgba(0,0,0,0.04);
}
.sidebar::-webkit-scrollbar{display:none;}

.sg{margin-bottom:24px;}
.sg-label{
    font-family:'Syne',sans-serif;
    font-size:0.56rem;font-weight:700;letter-spacing:0.14em;text-transform:uppercase;
    color:var(--t4);padding:0 8px;margin-bottom:6px;
    display:flex;align-items:center;gap:6px;
}
.sg-label::after{content:'';flex:1;height:1px;background:var(--b2);}

.nav-it{
    display:flex;align-items:center;gap:8px;
    padding:7px 9px;border-radius:var(--r2);
    font-size:0.77rem;font-weight:500;
    color:var(--t2);text-decoration:none;cursor:pointer;
    border:none;background:transparent;
    font-family:'DM Sans',sans-serif;width:100%;text-align:left;
    transition:all 0.14s;white-space:nowrap;position:relative;
}
.nav-it i{font-size:10.5px;width:14px;text-align:center;flex-shrink:0;color:var(--t3);transition:color 0.14s;}
.nav-it:hover{color:var(--t1);background:rgba(255,255,255,0.65);}
.nav-it:hover i{color:var(--t2);}
.nav-it.on{
    color:var(--t1);
    background:rgba(255,255,255,0.80);
    border:1px solid var(--b2);
    box-shadow:0 2px 8px rgba(0,0,0,0.07);
}
.nav-it.on i{color:var(--gold);}
.nav-it.on::before{
    content:'';position:absolute;left:0;top:50%;transform:translateY(-50%);
    width:2px;height:13px;
    background:linear-gradient(180deg,var(--gold),var(--gold2));
    border-radius:2px;
}
.nav-badge{
    margin-left:auto;
    background:var(--red);color:#fff;
    font-size:0.57rem;font-weight:700;
    padding:2px 5px;border-radius:10px;line-height:1.4;
}
.sdiv{height:1px;background:var(--b2);margin:8px 0;}

/* ── Main ─────────────────────────────────────────── */
.main{flex:1;padding:30px 38px 80px;min-width:0;position:relative;z-index:1;}
.panel{display:none;}
.panel.on{display:block;animation:panelIn 0.22s cubic-bezier(0.22,1,0.36,1);}
@keyframes panelIn{from{opacity:0;transform:translateY(6px);}to{opacity:1;transform:translateY(0);}}

/* ── Toast ────────────────────────────────────────── */
.toast{
    display:flex;align-items:center;gap:10px;
    padding:12px 16px;
    background:rgba(0,184,148,0.08);
    border:1px solid rgba(0,184,148,0.22);
    border-radius:var(--r2);color:var(--green);
    font-size:0.81rem;margin-bottom:22px;
    animation:toastIn 0.3s cubic-bezier(0.34,1.56,0.64,1);
    backdrop-filter:blur(8px);
}
@keyframes toastIn{from{opacity:0;transform:translateY(-8px) scale(0.97);}to{opacity:1;transform:translateY(0) scale(1);}}
.t-dot{width:6px;height:6px;border-radius:50%;background:var(--green);flex-shrink:0;animation:blink 2s ease infinite;}
@keyframes blink{0%,100%{opacity:1;}50%{opacity:0.4;}}

/* ── Page Heading ─────────────────────────────────── */
.pg-hd{margin-bottom:26px;}
.pg-hd h1{font-family:'Syne',sans-serif;font-size:1.55rem;font-weight:700;color:var(--t1);letter-spacing:-0.02em;line-height:1.1;}
.pg-hd p{font-size:0.78rem;color:var(--t3);margin-top:4px;}

/* ── Cards ────────────────────────────────────────── */
.card{
    background:rgba(255,255,255,0.62);
    backdrop-filter:blur(16px);
    border:1px solid rgba(255,255,255,0.85);
    border-radius:var(--r3);
    overflow:hidden;margin-bottom:16px;
    transition:border-color 0.18s,box-shadow 0.18s;
    box-shadow:0 2px 0 rgba(255,255,255,0.9) inset, 0 8px 32px rgba(0,0,0,0.07);
}
.card:hover{
    border-color:rgba(255,255,255,1);
    box-shadow:0 2px 0 rgba(255,255,255,0.9) inset, 0 12px 40px rgba(0,0,0,0.10);
}

.card-hd{
    display:flex;align-items:center;gap:10px;
    padding:15px 20px;
    background:rgba(255,255,255,0.45);
    border-bottom:1px solid var(--b1);
}
.c-ico{width:28px;height:28px;border-radius:var(--r2);display:flex;align-items:center;justify-content:center;font-size:11px;flex-shrink:0;}
.ico-g{background:rgba(0,0,0,0.08);border:1px solid rgba(0,0,0,0.10);color:var(--gold);}
.ico-d{background:rgba(0,0,0,0.05);border:1px solid var(--b2);color:var(--t3);}
.card-hd h3{font-family:'Syne',sans-serif;font-size:0.8rem;font-weight:700;color:var(--t1);}
.card-body{padding:20px;}

/* ── Form Grids ───────────────────────────────────── */
.fg3{display:grid;grid-template-columns:repeat(3,1fr);gap:13px;}
.fg2{display:grid;grid-template-columns:repeat(2,1fr);gap:13px;}
.f{display:flex;flex-direction:column;gap:5px;margin-bottom:13px;}
.f:last-child{margin-bottom:0;}
.f>label{
    font-family:'Syne',sans-serif;
    font-size:0.6rem;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;
    color:var(--t3);
}
.f input,.f select,.f textarea{
    padding:8px 11px;
    background:rgba(255,255,255,0.60);
    border:1px solid var(--b2);
    border-radius:var(--r1);
    color:var(--t1);
    font-size:0.83rem;font-family:'DM Sans',sans-serif;
    outline:none;
    transition:border-color 0.14s,box-shadow 0.14s;
    width:100%;
    backdrop-filter:blur(4px);
}
.f input:focus,.f select:focus,.f textarea:focus{
    border-color:var(--b4);
    box-shadow:0 0 0 3px rgba(0,0,0,0.05);
    background:rgba(255,255,255,0.85);
}
.f input::placeholder,.f textarea::placeholder{color:var(--t4);}
.f textarea{min-height:85px;resize:vertical;}
.f select option{background:#fff;color:var(--t1);}

.img-prev{display:block;max-width:260px;height:130px;object-fit:cover;border-radius:var(--r2);border:1px solid var(--b2);margin-top:8px;opacity:0.9;}
.fdiv{height:1px;background:linear-gradient(90deg,transparent,var(--b2) 15%,var(--b2) 85%,transparent);margin:17px 0;}
.slbl{font-family:'Syne',sans-serif;font-size:0.58rem;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:var(--t4);margin-bottom:11px;}

/* ── Checkbox ─────────────────────────────────────── */
.ck{display:flex;align-items:center;gap:8px;cursor:pointer;margin-bottom:11px;}
.ck input{
    appearance:none;-webkit-appearance:none;
    width:15px;height:15px;
    background:rgba(255,255,255,0.60);
    border:1px solid var(--b3);border-radius:4px;
    cursor:pointer;flex-shrink:0;position:relative;transition:all 0.14s;
}
.ck input:checked{background:var(--gold);border-color:var(--gold);}
.ck input:checked::after{
    content:'';position:absolute;left:3.5px;top:1px;
    width:5px;height:9px;
    border:2px solid #fff;border-left:none;border-top:none;
    transform:rotate(45deg);
}
.ck span{font-size:0.8rem;color:var(--t2);}
.factions{display:flex;align-items:center;gap:10px;margin-top:18px;padding-top:16px;border-top:1px solid var(--b1);}

/* ── Add Card ─────────────────────────────────────── */
.add-card{
    background:rgba(255,255,255,0.50);
    backdrop-filter:blur(12px);
    border:1px solid rgba(255,255,255,0.80);
    border-radius:var(--r3);
    padding:18px 20px;margin-bottom:14px;
    position:relative;overflow:hidden;
    box-shadow:0 2px 0 rgba(255,255,255,0.95) inset, 0 4px 20px rgba(0,0,0,0.06);
}
.add-card::before{
    content:'';position:absolute;top:0;left:0;right:0;height:1px;
    background:rgba(255,255,255,0.9);
}
.add-hd{
    font-family:'Syne',sans-serif;
    font-size:0.64rem;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;
    color:var(--t2);margin-bottom:14px;
    display:flex;align-items:center;gap:7px;
}

/* ── Save Bar ─────────────────────────────────────── */
.save-bar{
    position:sticky;bottom:20px;z-index:100;
    display:flex;align-items:center;justify-content:center;gap:12px;
    padding:14px 26px;
    background:rgba(255,255,255,0.75);
    border:1px solid rgba(255,255,255,0.90);
    border-radius:var(--r3);
    box-shadow:0 8px 40px rgba(0,0,0,0.12),0 0 0 1px rgba(255,255,255,0.8) inset;
    margin-top:32px;
    backdrop-filter:blur(20px);
}

/* ── Buttons ──────────────────────────────────────── */
.btn-p{
    display:inline-flex;align-items:center;gap:7px;
    padding:9px 20px;
    background:linear-gradient(135deg,#1a1a24,#2e2e3c);
    color:#fff;border:none;border-radius:var(--r2);
    font-size:0.78rem;font-weight:700;font-family:'DM Sans',sans-serif;
    cursor:pointer;letter-spacing:0.04em;
    transition:all 0.18s;
    box-shadow:0 3px 16px rgba(0,0,0,0.18);
    text-decoration:none;
}
.btn-p:hover{box-shadow:0 5px 26px rgba(0,0,0,0.28);transform:translateY(-1px);}
.btn-p:active{transform:translateY(0);}

.btn-g{
    display:inline-flex;align-items:center;gap:7px;
    padding:9px 18px;
    background:rgba(255,255,255,0.60);
    color:var(--t2);
    border:1px solid var(--b2);border-radius:var(--r2);
    font-size:0.78rem;font-weight:500;font-family:'DM Sans',sans-serif;
    cursor:pointer;text-decoration:none;
    transition:all 0.15s;
    backdrop-filter:blur(4px);
}
.btn-g:hover{border-color:var(--b3);color:var(--t1);background:rgba(255,255,255,0.85);}
.btn-xs{padding:6px 16px !important;font-size:0.73rem !important;}

/* ── Table Box ────────────────────────────────────── */
.tbox{
    background:rgba(255,255,255,0.60);
    backdrop-filter:blur(16px);
    border:1px solid rgba(255,255,255,0.85);
    border-radius:var(--r3);
    overflow:hidden;margin-bottom:16px;
    box-shadow:0 2px 0 rgba(255,255,255,0.9) inset,0 8px 32px rgba(0,0,0,0.07);
}
table{width:100%;border-collapse:collapse;}
thead th{
    padding:10px 13px;text-align:left;
    font-family:'Syne',sans-serif;font-size:0.58rem;font-weight:700;
    letter-spacing:0.1em;text-transform:uppercase;
    color:var(--t3);
    background:rgba(255,255,255,0.40);
    border-bottom:1px solid var(--b2);white-space:nowrap;
}
tbody td{padding:11px 13px;border-bottom:1px solid var(--b1);vertical-align:middle;font-size:0.81rem;color:var(--t1);}
tbody tr:last-child td{border-bottom:none;}
tbody tr{transition:background 0.1s;}
tbody tr:hover td{background:rgba(255,255,255,0.25);}

.timg{width:88px;height:58px;object-fit:cover;border-radius:var(--r1);border:1px solid var(--b2);display:block;background:rgba(0,0,0,0.04);}

/* Inline table inputs */
.ti{
    padding:6px 9px;
    background:rgba(255,255,255,0.55);
    border:1px solid var(--b2);border-radius:var(--r1);
    color:var(--t1);font-size:0.8rem;font-family:'DM Sans',sans-serif;
    outline:none;transition:border-color 0.13s;width:100%;
    backdrop-filter:blur(4px);
}
.ti:focus{border-color:var(--b4);background:rgba(255,255,255,0.85);}
.ti::placeholder{color:var(--t4);}
.ti-sm{width:78px!important;}
.ts{
    padding:6px 9px;
    background:rgba(255,255,255,0.55);
    border:1px solid var(--b2);border-radius:var(--r1);
    color:var(--t1);font-size:0.8rem;font-family:'DM Sans',sans-serif;
    outline:none;cursor:pointer;width:100%;
}
.ts option{background:#fff;color:var(--t1);}
.ta{
    padding:6px 9px;
    background:rgba(255,255,255,0.55);
    border:1px solid var(--b2);border-radius:var(--r1);
    color:var(--t1);font-size:0.8rem;font-family:'DM Sans',sans-serif;
    outline:none;resize:vertical;min-height:68px;width:100%;
    backdrop-filter:blur(4px);
}
.ta:focus{border-color:var(--b4);}

/* ── Action Buttons ───────────────────────────────── */
.acts{display:flex;flex-wrap:wrap;gap:4px;}
.ab{
    display:inline-flex;align-items:center;gap:4px;
    padding:5px 10px;border-radius:var(--r1);
    font-size:0.68rem;font-weight:600;
    border:1px solid transparent;cursor:pointer;
    font-family:'DM Sans',sans-serif;transition:all 0.12s;
    background:transparent;text-decoration:none;white-space:nowrap;
}
.ab-u{border-color:rgba(0,184,148,0.22);color:var(--green);background:rgba(0,184,148,0.06);}
.ab-u:hover{background:rgba(0,184,148,0.14);border-color:var(--green);}
.ab-d{border-color:rgba(225,112,85,0.22);color:var(--red);background:rgba(225,112,85,0.06);}
.ab-d:hover{background:rgba(225,112,85,0.14);border-color:var(--red);}
.ab-l{border-color:var(--b2);color:var(--t2);background:rgba(255,255,255,0.40);}
.ab-l:hover{border-color:var(--b3);color:var(--t1);background:rgba(255,255,255,0.75);}

/* ── Edit Notice ──────────────────────────────────── */
.edit-notice{
    display:flex;align-items:center;justify-content:space-between;
    padding:11px 16px;
    background:rgba(0,0,0,0.04);
    border:1px solid rgba(0,0,0,0.08);
    border-radius:var(--r2);color:var(--t2);
    font-size:0.78rem;margin-bottom:14px;
}
.edit-notice a{color:var(--t1);opacity:0.65;font-size:0.73rem;text-decoration:underline;}
.edit-notice a:hover{opacity:1;}

/* ── Footer Section ───────────────────────────────── */
.ft-sec{
    background:rgba(255,255,255,0.58);
    backdrop-filter:blur(14px);
    border:1px solid rgba(255,255,255,0.82);
    border-radius:var(--r3);
    overflow:hidden;margin-bottom:14px;
    box-shadow:0 2px 0 rgba(255,255,255,0.9) inset,0 6px 24px rgba(0,0,0,0.06);
}
.ft-sec-hd{
    display:flex;align-items:center;
    padding:12px 17px;
    background:rgba(255,255,255,0.42);
    border-bottom:1px solid var(--b1);
}
.ft-sec-hd h3{font-family:'Syne',sans-serif;font-size:0.78rem;font-weight:700;color:var(--t1);}
.ft-body{padding:16px;}

/* ── Section Heading ──────────────────────────────── */
.sec-hd{display:flex;align-items:baseline;gap:10px;margin-bottom:14px;padding-bottom:12px;border-bottom:1px solid var(--b1);}
.sec-hd h2{font-family:'Syne',sans-serif;font-size:0.9rem;font-weight:700;color:var(--t1);}
.sec-cnt{font-size:0.68rem;color:var(--t3);background:rgba(255,255,255,0.60);border:1px solid var(--b2);padding:2px 7px;border-radius:var(--r4);}

/* ── Responsive ───────────────────────────────────── */
@media(max-width:1100px){.sidebar{width:178px;}.fg3{grid-template-columns:repeat(2,1fr);}}
@media(max-width:840px){.sidebar{display:none;}.main{padding:18px 14px 60px;}.fg3,.fg2{grid-template-columns:1fr;}}
    </style>
</head>
<body>

<?php

$unreadInquiries = 0;
if (file_exists(__DIR__ . '/../app/models/VehicleInquiry.php')) {
    require_once __DIR__ . '/../app/models/VehicleInquiry.php';
    try { $inquiryBadgeModel = new VehicleInquiry(); $unreadInquiries = $inquiryBadgeModel->countUnread(); }
    catch (Exception $e) { $unreadInquiries = 0; }
}

$chatUnread = 0;
if (file_exists(__DIR__ . '/../../app/models/ChatSession.php')) {
    require_once __DIR__ . '/../../app/models/ChatSession.php';
    try { $chatSessionBadge = new ChatSession(); $chatUnread = $chatSessionBadge->countUnread(); }
    catch (Exception $e) { $chatUnread = 0; }
}

$tab = $_GET['tab'] ?? 'content';


?>

<header class="topbar">
    <a class="brand" href="?tab=content">
        <div class="brand-mark"><i class="fas fa-shield-halved"></i></div>
        <span class="brand-name">Porsche</span>
        <div class="brand-div"></div>
        <span class="brand-role">Admin Panel</span>
    </a>
    <div class="topbar-actions">
        <a href="/lending_word/" target="_blank" class="tpill"><i class="fas fa-up-right-from-square"></i> Preview</a>
        <a href="/lending_word/admin/logout.php" class="tpill logout"><i class="fas fa-arrow-right-from-bracket"></i> Logout</a>
    </div>
</header>

<div class="layout">
    <aside class="sidebar">
        <div class="sg">
            <div class="sg-label">Content</div>
            <?php foreach([
                'content'  => ['fas fa-file-lines',   'Content'],
                'sound'    => ['fas fa-music',         'Sound'],
                'featured' => ['fas fa-star',          'Featured Vehicles'],
                'models'   => ['fas fa-car-side',      'Models'],
                'explore'  => ['fas fa-compass',       'Explore Models'],
                'discover' => ['fas fa-wand-magic-sparkles',      'Discover'],
                'navbar'   => ['fas fa-bars',          'Navbar'],
                'footer'   => ['fas fa-table-columns', 'Footer'],
                'variants' => ['fas fa-layer-group',   'Variants'],
            ] as $k => [$ic, $lbl]): ?>
            <button class="nav-it <?= $tab === $k ? 'on' : '' ?>" onclick="location.href='?tab=<?= $k ?>'">
                <i class="<?= $ic ?>"></i><?= $lbl ?>
            </button>
            <?php endforeach; ?>
        </div>
        <div class="sdiv"></div>
        <div class="sg">
            <div class="sg-label">Tools</div>
            <a class="nav-it" href="/lending_word/admin/vehicles.php"><i class="fas fa-car"></i>Finder Vehicles</a>
            <a class="nav-it" href="/lending_word/admin/saved_vehicles.php"><i class="fas fa-bookmark"></i>Saved Vehicles</a>
            <a class="nav-it" href="/lending_word/admin/inquiries.php">
                <i class="fas fa-inbox"></i>Inquiries
                <?php if ($unreadInquiries > 0):?><span class="nav-badge"><?= $unreadInquiries ?></span><?php endif;?>
            </a>
            <a class="nav-it" href="/lending_word/admin/chat.php">
                <i class="fas fa-comments"></i>Live Chat
                <?php if ($chatUnread > 0):?><span class="nav-badge"><?= $chatUnread ?></span><?php endif;?>
            </a>
            <a class="nav-it" href="/lending_word/admin/career.php">
                <i class="fas fa-briefcase"></i>Career
                <?php if ($chatUnread > 0):?><span class="nav-badge"><?= $chatUnread ?></span><?php endif;?>
            </a>
            <a class="nav-it" href="/lending_word/admin/gpc.php">
                <i class="fas fa-file-contract"></i>GPC
                
            </a>
        </div>
    </aside>

    <main class="main">
        <?php if (isset($success) && $success):?>
        <div class="toast"><span class="t-dot"></span><?= htmlspecialchars($success) ?></div>
        <?php endif;?>

        <!-- CONTENT TAB -->
        <div class="panel <?= $tab === 'content' ? 'on' : '' ?>">
            <div class="pg-hd"><h1>Site Content</h1><p>Edit sections, headlines and copy text</p></div>
            <form method="POST">
                <?php foreach ($grouped as $section => $items):?>
                <div class="card">
                    <div class="card-hd"><div class="c-ico ico-g"><i class="fas fa-pen-nib"></i></div><h3><?= ucfirst($section) ?> Section</h3></div>
                    <div class="card-body">
                        <?php foreach ($items as $item):?>
                        <div class="f">
                            <label><?= ucwords(str_replace('_', ' ', $item['key_name'])) ?></label>
                            <?php if ($item['type'] === 'textarea'):?>
                                <textarea name="content[<?= $item['id'] ?>]"><?= htmlspecialchars($item['value']) ?></textarea>
                            <?php else:?>
                                <input type="text" name="content[<?= $item['id'] ?>]" value="<?= htmlspecialchars($item['value']) ?>">
                            <?php endif;?>
                            <?php if ($item['type'] === 'image' && $item['value']):?>
                                <img src="<?= htmlspecialchars($item['value']) ?>" class="img-prev">
                            <?php endif;?>
                        </div>
                        <?php endforeach;?>
                    </div>
                </div>
                <?php endforeach;?>
                <div class="save-bar">
                    <button type="submit" name="update" class="btn-p"><i class="fas fa-floppy-disk"></i>Save All Changes</button>
                    <a href="/lending_word/" target="_blank" class="btn-g"><i class="fas fa-up-right-from-square"></i>Preview Site</a>
                </div>
            </form>
        </div>

        <!-- SOUND TAB -->
        <div class="panel <?= $tab === 'sound' ? 'on' : '' ?>">
            <div class="pg-hd"><h1>Sound Section</h1><p>Audio, background image and button config</p></div>
            <form method="POST">
                <div class="card">
                    <div class="card-hd"><div class="c-ico ico-g"><i class="fas fa-waveform-lines"></i></div><h3>Sound Content</h3></div>
                    <div class="card-body">
                        <?php
                        $sc = [
                            ['id' => null,'key_name' => 'title',           'value' => '','type' => 'text'],
                            ['id' => null,'key_name' => 'caption',         'value' => '','type' => 'textarea'],
                            ['id' => null,'key_name' => 'background_image','value' => '','type' => 'image'],
                            ['id' => null,'key_name' => 'button_text',     'value' => '','type' => 'text'],
                            ['id' => null,'key_name' => 'audio_url',       'value' => '','type' => 'text'],
                        ];
                        foreach ($grouped['sound'] ?? $sc as $item) {
                            if (!isset($item['id'])) { $item['id'] = 'new_' . $item['key_name']; $item['value'] = ''; }
                        ?>
                        <div class="f">
                            <label><?= ucwords(str_replace('_', ' ', $item['key_name'])) ?></label>
                            <?php if ($item['type'] === 'textarea'):?>
                                <textarea name="content[<?= $item['id'] ?>]"><?= htmlspecialchars($item['value']) ?></textarea>
                            <?php else:?>
                                <input type="text" name="content[<?= $item['id'] ?>]" value="<?= htmlspecialchars($item['value']) ?>">
                            <?php endif;?>
                            <?php if ($item['type'] === 'image' && $item['value']):?><img src="<?= htmlspecialchars($item['value']) ?>" class="img-prev"><?php endif;?>
                        </div>
                        <?php }?>
                        <div class="factions"><button type="submit" name="update_sound" class="btn-p"><i class="fas fa-floppy-disk"></i>Save Sound</button></div>
                    </div>
                </div>
            </form>
        </div>

        <!-- FEATURED VEHICLES TAB -->
        <div class="panel <?= $tab === 'featured' ? 'on' : '' ?>">
            <div class="pg-hd">
                <h1>Featured Vehicles</h1>
                <p>Kartu Popular &amp; New yang muncul di homepage setelah section About</p>
            </div>
            <div class="add-card">
                <div class="add-hd"><i class="fas fa-plus"></i>Tambah Featured Vehicle</div>
                <form method="POST">
                    <div class="fg3" style="margin-bottom:11px;">
                        <div class="f" style="margin:0;"><label>Nama Model <span style="color:var(--gold)">*</span></label><input type="text" name="name" required placeholder="911 Carrera T."></div>
                        <div class="f" style="margin:0;"><label>Subtitle / Tipe</label><input type="text" name="subtitle" placeholder="Sports Car"></div>
                        <div class="f" style="margin:0;"><label>Badge</label><input type="text" name="badge" placeholder="New · Popular · Limited"></div>
                    </div>
                    <div class="f"><label>Image URL <span style="color:var(--gold)">*</span></label><input type="text" name="image" required placeholder="https://..."></div>
                    <div class="fg3" style="margin-bottom:11px;">
                        <div class="f" style="margin:0;"><label>Link URL Custom</label><input type="text" name="link" placeholder="/lending_word/app/views/frontend/model-detail.php?id=87"><small style="color:var(--t4);font-size:.67rem;margin-top:3px;">Kosongkan jika pakai Variant ID di bawah</small></div>
                        <div class="f" style="margin:0;"><label>Model Variant ID</label><input type="number" name="model_variant_id" placeholder="87"><small style="color:var(--t4);font-size:.67rem;margin-top:3px;">ID dari tabel model_variants (lihat tab Variants)</small></div>
                        <div class="f" style="margin:0;"><label>Sort Order</label><input type="number" name="sort_order" value="0"></div>
                    </div>
                    <label class="ck"><input type="checkbox" name="is_active" value="1" checked><span>Aktif (tampil di homepage)</span></label>
                    <div class="factions" style="margin-top:12px;padding-top:12px;"><button type="submit" name="add_featured" class="btn-p btn-xs"><i class="fas fa-plus"></i>Tambah Vehicle</button></div>
                </form>
            </div>
            <div class="edit-notice">
                <span><i class="fas fa-info-circle" style="margin-right:6px;"></i>Card akan link ke <strong>model-detail.php?id={variant_id}</strong> atau URL custom.</span>
                <a href="/lending_word/" target="_blank">Preview Homepage →</a>
            </div>
            <div class="tbox"><table>
                <thead><tr><th>Preview</th><th>Nama Model</th><th>Subtitle</th><th>Badge</th><th>Image URL</th><th>Link / Variant ID</th><th>Aktif</th><th>Order</th><th>Aksi</th></tr></thead>
                <tbody>
                <?php foreach ($featuredVehicles as $v):?>
                <tr><form method="POST">
                    <input type="hidden" name="id" value="<?= $v['id'] ?>">
                    <td><?php if ($v['image']):?><img src="<?= htmlspecialchars($v['image']) ?>" class="timg"><?php endif;?></td>
                    <td><input class="ti" type="text" name="name" value="<?= htmlspecialchars($v['name']) ?>"></td>
                    <td><input class="ti" type="text" name="subtitle" value="<?= htmlspecialchars($v['subtitle'] ?? '') ?>" placeholder="Sports Car" style="min-width:100px;"></td>
                    <td><input class="ti" type="text" name="badge" value="<?= htmlspecialchars($v['badge'] ?? '') ?>" placeholder="New" style="min-width:80px;"></td>
                    <td><input class="ti" type="text" name="image" value="<?= htmlspecialchars($v['image']) ?>" style="min-width:170px;"></td>
                    <td style="min-width:185px;"><input class="ti" type="text" name="link" value="<?= htmlspecialchars($v['link'] ?? '') ?>" placeholder="Custom Link URL" style="margin-bottom:4px;"><input class="ti" type="number" name="model_variant_id" value="<?= htmlspecialchars($v['model_variant_id'] ?? '') ?>" placeholder="Variant ID"></td>
                    <td style="text-align:center;"><label class="ck" style="justify-content:center;margin:0;"><input type="checkbox" name="is_active" value="1" <?= !empty($v['is_active']) ? 'checked' : '' ?>><span></span></label></td>
                    <td><input class="ti ti-sm" type="number" name="sort_order" value="<?= $v['sort_order'] ?>"></td>
                    <td><div class="acts"><button type="submit" name="update_featured" class="ab ab-u"><i class="fas fa-check"></i>Update</button><button type="submit" name="delete_featured" class="ab ab-d" onclick="return confirm('Hapus featured vehicle ini?')"><i class="fas fa-trash-alt"></i></button></div></td>
                </form></tr>
                <?php endforeach;?>
                <?php if (empty($featuredVehicles)):?>
                <tr><td colspan="9" style="text-align:center;color:var(--t4);padding:40px;"><i class="fas fa-star" style="display:block;font-size:1.4rem;margin-bottom:8px;opacity:0.3;"></i>Belum ada featured vehicles. Tambahkan di atas.</td></tr>
                <?php endif;?>
                </tbody>
            </table></div>
        </div>

        <!-- MODELS TAB -->
        <div class="panel <?= $tab === 'models' ? 'on' : '' ?>">
            <div class="pg-hd"><h1>Models</h1><p>Homepage model lineup</p></div>
            <div class="add-card">
                <div class="add-hd"><i class="fas fa-plus"></i>Add Model</div>
                <form method="POST">
                    <div class="fg3" style="margin-bottom:12px;">
                        <div class="f" style="margin:0;"><label>Name <span style="color:var(--gold)">*</span></label><input type="text" name="name" required placeholder="e.g. 911 Carrera"></div>
                        <div class="f" style="margin:0;"><label>Fuel Types <span style="color:var(--gold)">*</span></label><input type="text" name="fuel_types" required placeholder="Gasoline, Electric"></div>
                        <div class="f" style="margin:0;"><label>Sort Order</label><input type="number" name="sort_order" value="0"></div>
                    </div>
                    <div class="f"><label>Image URL</label><input type="text" name="image" placeholder="https://..."></div>
                    <div class="factions" style="margin-top:10px;padding-top:10px;"><button type="submit" name="add_model" class="btn-p btn-xs"><i class="fas fa-plus"></i>Add</button></div>
                </form>
            </div>
            <div class="tbox"><table>
                <thead><tr><th>Image</th><th>Name</th><th>Fuel Types</th><th>Image URL</th><th>Order</th><th>Actions</th></tr></thead>
                <tbody><?php foreach ($models as $m):?><tr><form method="POST">
                    <input type="hidden" name="id" value="<?= $m['id'] ?>">
                    <td><?php if ($m['image']):?><img src="<?= htmlspecialchars($m['image']) ?>" class="timg"><?php endif;?></td>
                    <td><input class="ti" type="text" name="name" value="<?= htmlspecialchars($m['name']) ?>"></td>
                    <td><input class="ti" type="text" name="fuel_types" value="<?= htmlspecialchars($m['fuel_types']) ?>"></td>
                    <td><input class="ti" type="text" name="image" value="<?= htmlspecialchars($m['image']) ?>"></td>
                    <td><input class="ti ti-sm" type="number" name="sort_order" value="<?= $m['sort_order'] ?>"></td>
                    <td><div class="acts"><button type="submit" name="update_model" class="ab ab-u"><i class="fas fa-check"></i>Update</button><button type="submit" name="delete_model" class="ab ab-d" onclick="return confirm('Delete?')"><i class="fas fa-trash-alt"></i></button></div></td>
                </form></tr><?php endforeach;?></tbody>
            </table></div>
        </div>

        <!-- EXPLORE TAB -->
        <div class="panel <?= $tab === 'explore' ? 'on' : '' ?>">
            <div class="pg-hd"><h1>Explore Models</h1><p>Explore section cards</p></div>
            <div class="add-card">
                <div class="add-hd"><i class="fas fa-plus"></i>Add Explore Model</div>
                <form method="POST">
                    <div class="fg3" style="margin-bottom:11px;">
                        <div class="f" style="margin:0;"><label>Name</label><input type="text" name="name" required></div>
                        <div class="f" style="margin:0;"><label>Fuel Types</label><input type="text" name="fuel_types" required></div>
                        <div class="f" style="margin:0;"><label>Sort Order</label><input type="number" name="sort_order" value="0"></div>
                    </div>
                    <div class="f"><label>Description</label><textarea name="description" style="min-height:60px;"></textarea></div>
                    <div class="fg3">
                        <div class="f" style="margin:0;"><label>Doors</label><input type="text" name="doors" placeholder="2 pintu"></div>
                        <div class="f" style="margin:0;"><label>Seats</label><input type="text" name="seats" placeholder="2+2 kursi"></div>
                        <div class="f" style="margin:0;"><label>Image URL</label><input type="text" name="image" required></div>
                    </div>
                    <div class="factions" style="margin-top:10px;padding-top:10px;"><button type="submit" name="add_explore" class="btn-p btn-xs"><i class="fas fa-plus"></i>Add</button></div>
                </form>
            </div>
            <div class="tbox"><table>
                <thead><tr><th>Image</th><th>Name</th><th>Description</th><th>Fuel</th><th>Doors</th><th>Seats</th><th>Image URL</th><th>Order</th><th>Actions</th></tr></thead>
                <tbody><?php foreach ($exploreModels as $m):?><tr><form method="POST">
                    <input type="hidden" name="id" value="<?= $m['id'] ?>">
                    <td><?php if ($m['image']):?><img src="<?= htmlspecialchars($m['image']) ?>" class="timg"><?php endif;?></td>
                    <td><input class="ti" type="text" name="name" value="<?= htmlspecialchars($m['name']) ?>"></td>
                    <td><textarea class="ta" name="description"><?= htmlspecialchars($m['description']) ?></textarea></td>
                    <td><input class="ti" type="text" name="fuel_types" value="<?= htmlspecialchars($m['fuel_types']) ?>"></td>
                    <td><input class="ti" type="text" name="doors" value="<?= htmlspecialchars($m['doors']) ?>"></td>
                    <td><input class="ti" type="text" name="seats" value="<?= htmlspecialchars($m['seats']) ?>"></td>
                    <td><input class="ti" type="text" name="image" value="<?= htmlspecialchars($m['image']) ?>" style="min-width:200px;"></td>
                    <td><input class="ti ti-sm" type="number" name="sort_order" value="<?= $m['sort_order'] ?>"></td>
                    <td><div class="acts"><button type="submit" name="update_explore" class="ab ab-u"><i class="fas fa-check"></i>Update</button><button type="submit" name="delete_explore" class="ab ab-d" onclick="return confirm('Delete?')"><i class="fas fa-trash-alt"></i></button></div></td>
                </form></tr><?php endforeach;?></tbody>
            </table></div>
        </div>

        <!-- DISCOVER TAB -->
        <div class="panel <?= $tab === 'discover' ? 'on' : '' ?>">
            <div class="pg-hd"><h1>Discover Features</h1><p>Landing page highlight cards</p></div>
            <div class="add-card">
                <div class="add-hd"><i class="fas fa-plus"></i>Tambah Feature</div>
                <form method="POST">
                    <div class="fg3" style="margin-bottom:11px;">
                        <div class="f" style="margin:0;"><label>Title <span style="color:var(--gold)">*</span></label><input type="text" name="title" required placeholder="E-Performance"></div>
                        <div class="f" style="margin:0;"><label>Category</label><input type="text" name="category" placeholder="Teknologi, Racing, Heritage..."></div>
                        <div class="f" style="margin:0;justify-content:flex-end;"><label>&nbsp;</label><label class="ck" style="margin:0;"><input type="checkbox" name="is_featured" value="1"><span>Featured (tampil besar)</span></label></div>
                    </div>
                    <div class="f"><label>Description</label><textarea name="description" style="min-height:70px;" placeholder="Deskripsi singkat yang menarik..."></textarea></div>
                    <div class="fg3" style="margin-bottom:11px;">
                        <div class="f" style="margin:0;"><label>Image URL <span style="color:var(--gold)">*</span></label><input type="text" name="image" required placeholder="https://..."></div>
                        <div class="f" style="margin:0;"><label>Link URL</label><input type="text" name="link_url" placeholder="/lending_word/discover/e-performance"></div>
                        <div class="f" style="margin:0;"><label>Link Label</label><input type="text" name="link_label" placeholder="Pelajari Lebih Lanjut" value="Pelajari Lebih Lanjut"></div>
                    </div>
                    <div class="fg2">
                        <div class="f" style="margin:0;"><label>Stats JSON <small style="color:var(--t4);font-weight:300;text-transform:none;letter-spacing:0;">&nbsp;— opsional</small></label><input type="text" name="stats" placeholder='[{"val":"408","lbl":"kW"},{"val":"4.1","lbl":"detik 0–100"}]'><small style="color:var(--t4);font-size:.68rem;margin-top:3px;">Format array JSON: val = angka, lbl = satuan</small></div>
                        <div class="f" style="margin:0;"><label>Sort Order</label><input type="number" name="sort_order" value="0"></div>
                    </div>
                    <div class="factions" style="margin-top:12px;padding-top:12px;"><button type="submit" name="add_discover" class="btn-p btn-xs"><i class="fas fa-plus"></i>Tambah Feature</button></div>
                </form>
            </div>
            <div class="edit-notice">
                <span><i class="fas fa-eye" style="margin-right:6px;"></i>Halaman discover detail tersedia di <strong>/lending_word/discover-detail.php</strong></span>
                <a href="/lending_word/discover-detail.php" target="_blank">Preview →</a>
            </div>
            <div class="tbox"><table>
                <thead><tr><th>Image</th><th>Title</th><th>Category</th><th>Description</th><th>Stats JSON</th><th>Link</th><th>Featured</th><th>Image URL</th><th>Order</th><th>Actions</th></tr></thead>
                <tbody><?php foreach ($discoverFeatures as $f):?><tr><form method="POST">
                    <input type="hidden" name="id" value="<?= $f['id'] ?>">
                    <td><?php if ($f['image']):?><img src="<?= htmlspecialchars($f['image']) ?>" class="timg"><?php endif;?></td>
                    <td><input class="ti" type="text" name="title" value="<?= htmlspecialchars($f['title']) ?>"></td>
                    <td><input class="ti" type="text" name="category" value="<?= htmlspecialchars($f['category'] ?? '') ?>" placeholder="Teknologi" style="min-width:90px;"></td>
                    <td><textarea class="ta" name="description"><?= htmlspecialchars($f['description']) ?></textarea></td>
                    <td><input class="ti" type="text" name="stats" value="<?= htmlspecialchars($f['stats'] ?? '') ?>" placeholder='[{"val":"408","lbl":"kW"}]' style="min-width:150px;"></td>
                    <td style="min-width:160px;"><input class="ti" type="text" name="link_url" value="<?= htmlspecialchars($f['link_url'] ?? '') ?>" placeholder="/halaman" style="margin-bottom:4px;"><input class="ti" type="text" name="link_label" value="<?= htmlspecialchars($f['link_label'] ?? 'Pelajari Lebih Lanjut') ?>" placeholder="Teks tombol"></td>
                    <td style="text-align:center;"><label class="ck" style="justify-content:center;margin:0;"><input type="checkbox" name="is_featured" value="1" <?= !empty($f['is_featured']) ? 'checked' : '' ?>><span></span></label></td>
                    <td><input class="ti" type="text" name="image" value="<?= htmlspecialchars($f['image']) ?>" style="min-width:190px;"></td>
                    <td><input class="ti ti-sm" type="number" name="sort_order" value="<?= $f['sort_order'] ?>"></td>
                    <td><div class="acts">
                        <button type="submit" name="update_discover" class="ab ab-u"><i class="fas fa-check"></i>Update</button>
                        <a href="/lending_word/admin/discover-sections.php?id=<?= $f['id'] ?>" class="ab" style="border-color:rgba(91,156,246,0.22);color:#5b9cf6;background:rgba(91,156,246,0.05);"><i class="fas fa-layer-group"></i>Edit Sections</a>
                        <a href="/lending_word/admin/discover-gallery.php?id=<?= $f['id'] ?>" class="ab" style="border-color:rgba(160,90,220,0.22);color:#a05adc;background:rgba(160,90,220,0.05);"><i class="fas fa-images"></i>Gallery</a>
                        <button type="submit" name="delete_discover" class="ab ab-d" onclick="return confirm('Hapus?')"><i class="fas fa-trash-alt"></i></button>
                    </div></td>
                </form></tr><?php endforeach;?></tbody>
            </table></div>
        </div>

        <!-- NAVBAR TAB -->
        <div class="panel <?= $tab === 'navbar' ? 'on' : '' ?>">
            <div class="pg-hd"><h1>Navbar</h1><p>Navigation links and order</p></div>
            <div class="add-card">
                <div class="add-hd"><i class="fas fa-plus"></i>Add Link</div>
                <form method="POST">
                    <div class="fg3">
                        <div class="f" style="margin:0;"><label>Label</label><input type="text" name="label" placeholder="e.g. Home" required></div>
                        <div class="f" style="margin:0;"><label>URL</label><input type="text" name="url" placeholder="#hero" required></div>
                        <div class="f" style="margin:0;"><label>Sort Order</label><input type="number" name="sort_order" value="0"></div>
                    </div>
                    <div class="factions" style="margin-top:10px;padding-top:10px;"><button type="submit" name="add_navbar" class="btn-p btn-xs"><i class="fas fa-plus"></i>Add</button></div>
                </form>
            </div>
            <div class="tbox"><table>
                <thead><tr><th>Label</th><th>URL</th><th>Order</th><th>Actions</th></tr></thead>
                <tbody><?php foreach ($navbarLinks as $l):?><tr><form method="POST">
                    <input type="hidden" name="id" value="<?= $l['id'] ?>">
                    <td><input class="ti" type="text" name="label" value="<?= htmlspecialchars($l['label']) ?>"></td>
                    <td><input class="ti" type="text" name="url" value="<?= htmlspecialchars($l['url']) ?>"></td>
                    <td><input class="ti ti-sm" type="number" name="sort_order" value="<?= $l['sort_order'] ?>"></td>
                    <td><div class="acts"><button type="submit" name="update_navbar" class="ab ab-u"><i class="fas fa-check"></i>Update</button><button type="submit" name="delete_navbar" class="ab ab-d" onclick="return confirm('Delete?')"><i class="fas fa-trash-alt"></i></button></div></td>
                </form></tr><?php endforeach;?></tbody>
            </table></div>
        </div>

        <!-- FOOTER TAB -->
        <div class="panel <?= $tab === 'footer' ? 'on' : '' ?>">
            <div class="pg-hd"><h1>Footer</h1><p>Newsletter, contact, social text, links and sections</p></div>
            <form method="POST">
                <div class="card" style="margin-bottom:14px;">
                    <div class="card-hd"><div class="c-ico ico-g"><i class="fas fa-newspaper"></i></div><h3>Newsletter Column</h3></div>
                    <div class="card-body"><div class="fg3">
                        <?php foreach ($grouped['footer'] ?? [] as $item): if (!in_array($item['key_name'], ['newsletter_title','newsletter_desc','newsletter_button','newsletter_link'])) continue; ?>
                        <div class="f" style="margin:0;"><label><?= ucwords(str_replace('_',' ',$item['key_name'])) ?></label><input type="text" name="content[<?= $item['id'] ?>]" value="<?= htmlspecialchars($item['value']) ?>"></div>
                        <?php endforeach; ?>
                    </div></div>
                </div>
                <div class="card" style="margin-bottom:14px;">
                    <div class="card-hd"><div class="c-ico ico-g"><i class="fas fa-location-dot"></i></div><h3>Locations &amp; Contacts Column</h3></div>
                    <div class="card-body"><div class="fg3">
                        <?php foreach ($grouped['footer'] ?? [] as $item): if (!in_array($item['key_name'], ['contact_title','contact_desc','contact_button','contact_link'])) continue; ?>
                        <div class="f" style="margin:0;"><label><?= ucwords(str_replace('_',' ',$item['key_name'])) ?></label><input type="text" name="content[<?= $item['id'] ?>]" value="<?= htmlspecialchars($item['value']) ?>"></div>
                        <?php endforeach; ?>
                    </div></div>
                </div>
                <div class="card" style="margin-bottom:14px;">
                    <div class="card-hd"><div class="c-ico ico-g"><i class="fas fa-share-nodes"></i></div><h3>Social Media Column Text</h3></div>
                    <div class="card-body"><div class="fg2">
                        <?php foreach ($grouped['footer'] ?? [] as $item): if (!in_array($item['key_name'], ['social_title','social_desc'])) continue; ?>
                        <div class="f" style="margin:0;"><label><?= ucwords(str_replace('_',' ',$item['key_name'])) ?></label><input type="text" name="content[<?= $item['id'] ?>]" value="<?= htmlspecialchars($item['value']) ?>"></div>
                        <?php endforeach; ?>
                    </div></div>
                </div>
                <div class="card" style="margin-bottom:20px;">
                    <div class="card-hd"><div class="c-ico ico-d"><i class="fas fa-copyright"></i></div><h3>Footer Bottom</h3></div>
                    <div class="card-body"><div class="fg2">
                        <?php foreach ($grouped['footer'] ?? [] as $item): if (!in_array($item['key_name'], ['copyright','bottom_text'])) continue; ?>
                        <div class="f" style="margin:0;"><label><?= ucwords(str_replace('_',' ',$item['key_name'])) ?></label><input type="text" name="content[<?= $item['id'] ?>]" value="<?= htmlspecialchars($item['value']) ?>"></div>
                        <?php endforeach; ?>
                    </div></div>
                </div>
                <div style="display:flex;justify-content:flex-end;margin-bottom:28px;"><button type="submit" name="update" class="btn-p"><i class="fas fa-floppy-disk"></i>Save Footer Text</button></div>
            </form>

            <!-- ══ FOOTER SECTION MANAGER — TAMBAHAN BARU ══════════════
                 Tambah / rename / hapus group section (Legal, Company, dll)
                 Disisipkan di sini, tidak ada yang lain diubah.
            ════════════════════════════════════════════════════════════ -->
            <div class="ft-sec" style="margin-bottom:20px;">
                <div class="ft-sec-hd" style="justify-content:space-between;">
                    <h3><i class="fas fa-layer-group" style="margin-right:7px;opacity:0.45;font-size:0.72rem;"></i>Footer Section Groups</h3>
                    <span class="sec-cnt"><?= count($footerSections) ?> section<?= count($footerSections) !== 1 ? 's' : '' ?></span>
                </div>
                <div class="ft-body">
                    <div class="add-card" style="margin-bottom:14px;">
                        <div class="add-hd"><i class="fas fa-plus"></i>Tambah Section Baru</div>
                        <form method="POST">
                            <div class="fg3" style="margin-bottom:10px;">
                                <div class="f" style="margin:0;">
                                    <label>Nama Section <span style="color:var(--gold)">*</span></label>
                                    <input type="text" name="section_title" required placeholder="contoh: Legal, Company, Services">
                                </div>
                                <div class="f" style="margin:0;">
                                    <label>Sort Order</label>
                                    <input type="number" name="section_sort_order" value="0" style="max-width:110px;">
                                </div>
                                <div class="f" style="margin:0;justify-content:flex-end;">
                                    <label>&nbsp;</label>
                                    <button type="submit" name="add_footer_section" class="btn-p btn-xs">
                                        <i class="fas fa-plus"></i>Tambah Section
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <?php if (!empty($footerSections)): ?>
                    <div class="tbox" style="margin-bottom:0;">
                        <table>
                            <thead>
                                <tr>
                                    <th style="width:36px;">#</th>
                                    <th>Nama Section</th>
                                    <th style="width:120px;">Sort Order</th>
                                    <th style="width:76px;text-align:center;">Links</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($footerSections as $sec): ?>
                                <tr>
                                    <form method="POST">
                                        <input type="hidden" name="id" value="<?= $sec['id'] ?>">
                                        <td style="color:var(--t4);font-size:0.7rem;font-family:'Syne',sans-serif;font-weight:700;"><?= $sec['id'] ?></td>
                                        <td><input class="ti" type="text" name="section_title" value="<?= htmlspecialchars($sec['title']) ?>" style="min-width:160px;"></td>
                                        <td><input class="ti ti-sm" type="number" name="section_sort_order" value="<?= $sec['sort_order'] ?? 0 ?>"></td>
                                        <td style="text-align:center;">
                                            <span style="font-size:0.72rem;color:var(--t3);background:rgba(255,255,255,0.60);border:1px solid var(--b2);padding:2px 9px;border-radius:var(--r4);">
                                                <?= count($sec['links']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="acts">
                                                <button type="submit" name="update_footer_section" class="ab ab-u">
                                                    <i class="fas fa-check"></i>Rename
                                                </button>
                                                <button type="submit" name="delete_footer_section" class="ab ab-d"
                                                    onclick="return confirm('Hapus section \'<?= htmlspecialchars(addslashes($sec['title'])) ?>\' beserta <?= count($sec['links']) ?> link-nya?\nTindakan ini tidak bisa dibatalkan.')">
                                                    <i class="fas fa-trash-alt"></i>Hapus
                                                </button>
                                            </div>
                                        </td>
                                    </form>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div style="text-align:center;padding:28px 16px;color:var(--t4);">
                        <i class="fas fa-layer-group" style="display:block;font-size:1.5rem;margin-bottom:10px;opacity:0.25;"></i>
                        Belum ada footer sections. Tambahkan di atas.
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <!-- ══ END FOOTER SECTION MANAGER ══ -->

            <?php foreach ($footerSections as $sec):?>
            <div class="ft-sec">
                <div class="ft-sec-hd"><h3><?= htmlspecialchars($sec['title']) ?></h3></div>
                <div class="ft-body">
                    <div class="add-card" style="margin-bottom:12px;">
                        <div class="add-hd"><i class="fas fa-plus"></i>Add Link</div>
                        <form method="POST">
                            <input type="hidden" name="section_id" value="<?= $sec['id'] ?>">
                            <div class="fg3" style="margin-bottom:10px;">
                                <div class="f" style="margin:0;"><label>Label</label><input type="text" name="label" required></div>
                                <div class="f" style="margin:0;"><label>URL</label><input type="text" name="url" required></div>
                                <div class="f" style="margin:0;"><label>Order</label><input type="number" name="sort_order" value="0"></div>
                            </div>
                            <button type="submit" name="add_footer_link" class="btn-p btn-xs"><i class="fas fa-plus"></i>Add</button>
                        </form>
                    </div>
                    <table><thead><tr><th>Label</th><th>URL</th><th>Order</th><th>Actions</th></tr></thead>
                    <tbody><?php foreach ($sec['links'] as $l):?><tr><form method="POST">
                        <input type="hidden" name="id" value="<?= $l['id'] ?>">
                        <td><input class="ti" type="text" name="label" value="<?= htmlspecialchars($l['label']) ?>"></td>
                        <td><input class="ti" type="text" name="url" value="<?= htmlspecialchars($l['url']) ?>"></td>
                        <td><input class="ti ti-sm" type="number" name="sort_order" value="<?= $l['sort_order'] ?>"></td>
                        <td><div class="acts"><button type="submit" name="update_footer_link" class="ab ab-u"><i class="fas fa-check"></i>Update</button><button type="submit" name="delete_footer_link" class="ab ab-d" onclick="return confirm('Delete?')"><i class="fas fa-trash-alt"></i></button></div></td>
                    </form></tr><?php endforeach;?></tbody></table>
                </div>
            </div>
            <?php endforeach;?>
            <div class="ft-sec">
                <div class="ft-sec-hd"><h3>Social Media Icons</h3></div>
                <div class="ft-body">
                    <div class="add-card" style="margin-bottom:12px;">
                        <div class="add-hd"><i class="fas fa-plus"></i>Add Social</div>
                        <form method="POST">
                            <div class="fg3" style="margin-bottom:10px;">
                                <div class="f" style="margin:0;"><label>Platform</label><input type="text" name="platform" placeholder="Instagram" required></div>
                                <div class="f" style="margin:0;"><label>URL</label><input type="text" name="url" required></div>
                                <div class="f" style="margin:0;"><label>Icon Class</label><input type="text" name="icon" placeholder="fab fa-instagram" required></div>
                            </div>
                            <div class="f"><label>Sort Order</label><input type="number" name="sort_order" value="0" style="max-width:110px;"></div>
                            <button type="submit" name="add_social" class="btn-p btn-xs"><i class="fas fa-plus"></i>Add Social</button>
                        </form>
                    </div>
                    <table><thead><tr><th>Platform</th><th>URL</th><th>Icon</th><th>Order</th><th>Actions</th></tr></thead>
                    <tbody><?php foreach ($socialLinks as $s):?><tr><form method="POST">
                        <input type="hidden" name="id" value="<?= $s['id'] ?>">
                        <td><input class="ti" type="text" name="platform" value="<?= htmlspecialchars($s['platform']) ?>"></td>
                        <td><input class="ti" type="text" name="url" value="<?= htmlspecialchars($s['url']) ?>"></td>
                        <td><input class="ti" type="text" name="icon" value="<?= htmlspecialchars($s['icon']) ?>"></td>
                        <td><input class="ti ti-sm" type="number" name="sort_order" value="<?= $s['sort_order'] ?>"></td>
                        <td><div class="acts"><button type="submit" name="update_social" class="ab ab-u"><i class="fas fa-check"></i>Update</button><button type="submit" name="delete_social" class="ab ab-d" onclick="return confirm('Delete?')"><i class="fas fa-trash-alt"></i></button></div></td>
                    </form></tr><?php endforeach;?></tbody></table>
                </div>
            </div>
        </div>

        <!-- VARIANTS TAB -->
        <div class="panel <?= $tab === 'variants' ? 'on' : '' ?>">
            <div class="pg-hd"><h1>Model Variants</h1><p>Specs, media and configurator links</p></div>
            <div class="add-card">
                <div class="add-hd"><i class="fas fa-plus"></i>Add Variant</div>
                <form method="POST">
                    <div class="fg3" style="margin-bottom:11px;">
                        <div class="f" style="margin:0;"><label>Category</label><select name="category_id" required><?php foreach ($categories as $c):?><?php if ($c['slug'] !== 'all'):?><option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option><?php endif;?><?php endforeach;?></select></div>
                        <div class="f" style="margin:0;"><label>Model Name</label><input type="text" name="name" required></div>
                        <div class="f" style="margin:0;"><label>Variant Group</label><input type="text" name="variant_group"></div>
                    </div>
                    <div class="f"><label>Image URL</label><input type="text" name="image" required></div>
                    <div class="fdiv"></div>
                    <div class="fg3" style="margin-bottom:11px;">
                        <div class="f" style="margin:0;"><label>Fuel Type</label><input type="text" name="fuel_type"></div>
                        <div class="f" style="margin:0;"><label>Drive Type</label><input type="text" name="drive_type"></div>
                        <div class="f" style="margin:0;"><label>Transmission</label><input type="text" name="transmission"></div>
                    </div>
                    <div class="fg3" style="margin-bottom:11px;">
                        <div class="f" style="margin:0;"><label>Acceleration</label><input type="text" name="acceleration" placeholder="4.1 s"></div>
                        <div class="f" style="margin:0;"><label>Power kW</label><input type="text" name="power_kw"></div>
                        <div class="f" style="margin:0;"><label>Power PS</label><input type="text" name="power_ps"></div>
                    </div>
                    <div class="fg3" style="margin-bottom:11px;">
                        <div class="f" style="margin:0;"><label>Top Speed</label><input type="text" name="top_speed"></div>
                        <div class="f" style="margin:0;"><label>Body Design</label><input type="text" name="body_design"></div>
                        <div class="f" style="margin:0;"><label>Seats</label><input type="text" name="seats"></div>
                    </div>
                    <div class="fdiv"></div>
                    <div class="f"><label>Configurator URL</label><input type="text" name="configurator_url"></div>
                    <div class="f"><label>Hero BG Image URL</label><input type="text" name="hero_bg_image"></div>
                    <div class="fg3" style="margin-bottom:11px;">
                        <div class="f" style="margin:0;"><label>Video URL</label><input type="text" name="model_video"></div>
                        <div class="f" style="margin:0;"><label>Audio URL</label><input type="text" name="model_audio"></div>
                        <div class="f" style="margin:0;"><label>Sort Order</label><input type="number" name="sort_order" value="0"></div>
                    </div>
                    <label class="ck"><input type="checkbox" name="is_new"><span>Mark as New</span></label>
                    <div class="factions" style="margin-top:10px;padding-top:10px;"><button type="submit" name="add_variant" class="btn-p btn-xs"><i class="fas fa-plus"></i>Add Variant</button></div>
                </form>
            </div>
            <div class="tbox"><table>
                <thead><tr><th>Image</th><th>Name</th><th>Category</th><th>Fuel/Drive/Trans</th><th>Specs</th><th>Media & URLs</th><th>Actions</th></tr></thead>
                <tbody><?php foreach ($modelVariants as $v):?><tr><form method="POST">
                    <input type="hidden" name="id" value="<?= $v['id'] ?>">
                    <td><?php if ($v['image']):?><img src="<?= htmlspecialchars($v['image']) ?>" class="timg" style="margin-bottom:5px;"><?php endif;?><input class="ti" type="text" name="image" value="<?= htmlspecialchars($v['image']) ?>" placeholder="Image URL"></td>
                    <td><input class="ti" type="text" name="name" value="<?= htmlspecialchars($v['name']) ?>"></td>
                    <td><select class="ts" name="category_id"><?php foreach ($categories as $c):?><?php if ($c['slug'] !== 'all'):?><option value="<?= $c['id'] ?>" <?= $v['category_id'] == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option><?php endif;?><?php endforeach;?></select></td>
                    <td style="min-width:145px;"><input class="ti" type="text" name="fuel_type" value="<?= htmlspecialchars($v['fuel_type'] ?? '') ?>" placeholder="Fuel" style="margin-bottom:4px;"><input class="ti" type="text" name="drive_type" value="<?= htmlspecialchars($v['drive_type'] ?? '') ?>" placeholder="Drive" style="margin-bottom:4px;"><input class="ti" type="text" name="transmission" value="<?= htmlspecialchars($v['transmission'] ?? '') ?>" placeholder="Trans"></td>
                    <td style="min-width:155px;"><input class="ti" type="text" name="acceleration" value="<?= htmlspecialchars($v['acceleration'] ?? '') ?>" placeholder="Accel" style="margin-bottom:4px;"><input class="ti" type="text" name="power_kw" value="<?= htmlspecialchars($v['power_kw'] ?? '') ?>" placeholder="kW" style="margin-bottom:4px;"><input class="ti" type="text" name="power_ps" value="<?= htmlspecialchars($v['power_ps'] ?? '') ?>" placeholder="PS" style="margin-bottom:4px;"><input class="ti" type="text" name="top_speed" value="<?= htmlspecialchars($v['top_speed'] ?? '') ?>" placeholder="Top Speed"></td>
                    <td style="min-width:210px;"><input class="ti" type="text" name="configurator_url" value="<?= htmlspecialchars($v['configurator_url'] ?? '') ?>" placeholder="Configurator" style="margin-bottom:4px;"><input class="ti" type="text" name="hero_bg_image" value="<?= htmlspecialchars($v['hero_bg_image'] ?? '') ?>" placeholder="Hero BG" style="margin-bottom:4px;"><input class="ti" type="text" name="model_video" value="<?= htmlspecialchars($v['model_video'] ?? '') ?>" placeholder="Video" style="margin-bottom:4px;"><input class="ti" type="text" name="model_audio" value="<?= htmlspecialchars($v['model_audio'] ?? '') ?>" placeholder="Audio"></td>
                    <td><div class="acts"><button type="submit" name="update_variant" class="ab ab-u"><i class="fas fa-check"></i>Save</button><button type="submit" name="delete_variant" class="ab ab-d" onclick="return confirm('Delete?')"><i class="fas fa-trash-alt"></i></button><a href="/lending_word/admin/gallery.php?variant_id=<?= $v['id'] ?>" class="ab ab-l"><i class="fas fa-images"></i>Gallery</a><a href="/lending_word/admin/sound.php?variant_id=<?= $v['id'] ?>" class="ab ab-l"><i class="fas fa-music"></i>Sound</a><a href="/lending_word/admin/specification.php?variant_id=<?= $v['id'] ?>" class="ab ab-l"><i class="fas fa-list-ul"></i>Spec</a></div></td>
                </form></tr><?php endforeach;?></tbody>
            </table></div>
        </div>

    </main>
</div>
</body>
</html>