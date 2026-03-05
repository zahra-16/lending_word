<?php
require_once __DIR__ . '/../../../app/database.php';
$pdo = Database::getInstance()->getConnection();
try {
    $stmt = $pdo->query("SELECT * FROM discover_features ORDER BY sort_order ASC, id ASC");
    $discoverFeatures = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) { $discoverFeatures = []; }
$features_json = json_encode($discoverFeatures, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT);
?><!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Discover — Porsche Indonesia</title>
<link rel="icon" type="image/png" href="/lending_word/public/assets/images/porsche-logo.png">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
:root{--white:#fff;--black:#080808;--ease:cubic-bezier(0.16,1,0.3,1)}
html{scroll-behavior:smooth}
body{font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;background:var(--black);color:var(--white);overflow-x:hidden;-webkit-font-smoothing:antialiased;cursor:none}
#cur-d,#cur-r{position:fixed;pointer-events:none;z-index:9999;border-radius:50%;top:0;left:0;transform:translate(-50%,-50%)}
#cur-d{width:6px;height:6px;background:#fff;transition:width .2s,height .2s}
#cur-r{width:34px;height:34px;border:1.5px solid rgba(255,255,255,0.5);transition:width .4s var(--ease),height .4s var(--ease)}
body.c-link #cur-r{width:60px;height:60px;border-color:rgba(255,255,255,0.9)}
body.c-link #cur-d{width:3px;height:3px}
body.c-img #cur-r{width:80px;height:80px;background:rgba(255,255,255,0.04)}
#prog{position:fixed;top:0;left:0;height:1px;width:0;z-index:8000;background:linear-gradient(90deg,transparent,rgba(255,255,255,0.7),#fff);transition:width .08s linear}

/* ── INTRO CURTAIN (finder.php style) ── */
#intro{position:fixed;inset:0;z-index:9500;display:flex;align-items:center;justify-content:center;background:#fff;transition:opacity .5s ease .05s}
#intro.done{opacity:0;pointer-events:none}
.c-panel{position:absolute;top:0;bottom:0;width:50%;background:#fff;z-index:2;transition:transform 1.2s cubic-bezier(0.76,0,0.24,1)}
.c-panel.l{left:0;border-right:1px solid rgba(0,0,0,0.08)}
.c-panel.r{right:0;border-left:1px solid rgba(0,0,0,0.08)}
#intro.open .c-panel.l{transform:translateX(-102%)}
#intro.open .c-panel.r{transform:translateX(102%)}
#intro-logo{position:relative;z-index:1;opacity:0;animation:introLogoReveal .6s .15s var(--ease) forwards;display:flex;align-items:center;justify-content:center}
#intro-logo img{width:clamp(70px,9vw,110px);filter:none}
@keyframes introLogoReveal{from{opacity:0;transform:scale(0.85) translateY(8px)}to{opacity:1;transform:scale(1) translateY(0)}}

.nav{position:fixed;top:0;width:100%;z-index:1000;height:68px;display:flex;align-items:center;transition:background .5s ease}
.nav.solid{background:rgba(4,4,4,0.92);backdrop-filter:blur(24px);box-shadow:0 1px 0 rgba(255,255,255,0.07)}
.nav-inner{max-width:1500px;margin:0 auto;padding:0 56px;display:flex;align-items:center;justify-content:space-between;width:100%}
.nav-logo img{height:56px;filter:brightness(0) invert(1)}
.nav-back{display:flex;align-items:center;gap:8px;color:rgba(255,255,255,0.65);text-decoration:none;font-size:11px;letter-spacing:.2em;text-transform:uppercase;cursor:none;border:1px solid rgba(255,255,255,0.18);padding:8px 18px;border-radius:2px;transition:color .25s,border-color .25s}
.nav-back:hover{color:#fff;border-color:rgba(255,255,255,0.5)}
.hero{height:100vh;position:relative;display:flex;align-items:flex-end;overflow:hidden}
.hero-bg{position:absolute;inset:0;background-size:cover;background-position:center;transform:scale(1.08);filter:brightness(0.45) contrast(1.15);transition:transform 12s ease-out,filter 2s ease}
.hero-bg.loaded{transform:scale(1);filter:brightness(0.5) contrast(1.1)}
.hero::after{content:'';position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,0.92) 0%,rgba(0,0,0,0.3) 50%,rgba(0,0,0,0.1) 100%),linear-gradient(to right,rgba(0,0,0,0.5) 0%,transparent 55%);z-index:1}
.hero-content{position:relative;z-index:2;padding:0 80px 80px;max-width:900px}
.hero-tag{display:inline-flex;align-items:center;gap:10px;font-size:9px;letter-spacing:.45em;text-transform:uppercase;color:rgba(255,255,255,0.45);margin-bottom:20px;opacity:0;animation:fadeUp .8s 1s var(--ease) forwards}
.hero-tag-line{height:1px;width:40px;background:rgba(255,255,255,0.35)}
.hero h1{font-size:clamp(3rem,6vw,5.5rem);font-weight:300;line-height:1.06;letter-spacing:-.025em;opacity:0;animation:fadeUpBig .9s 1.15s var(--ease) forwards}
.hero-subtitle{margin-top:18px;font-size:1.05rem;font-weight:300;color:rgba(255,255,255,0.68);line-height:1.7;max-width:560px;opacity:0;animation:fadeUp .8s 1.35s var(--ease) forwards}
.hero-scroll{position:absolute;bottom:36px;left:50%;transform:translateX(-50%);z-index:2;display:flex;flex-direction:column;align-items:center;gap:8px;opacity:0;animation:fadeUp .7s 1.8s ease forwards}
.hero-scroll-text{font-size:8px;letter-spacing:.4em;text-transform:uppercase;color:rgba(255,255,255,0.3)}
.scroll-wheel{width:20px;height:32px;border:1px solid rgba(255,255,255,0.25);border-radius:10px;position:relative}
.scroll-wheel::after{content:'';position:absolute;top:5px;left:50%;transform:translateX(-50%);width:2px;height:7px;background:rgba(255,255,255,0.7);border-radius:2px;animation:wheelAnim 2.2s ease-in-out infinite}
@keyframes wheelAnim{0%{top:5px;opacity:1}80%{top:18px;opacity:0}100%{top:5px;opacity:0}}
.cat-strip{background:#0a0a0a;border-bottom:1px solid rgba(255,255,255,0.07);position:sticky;top:68px;z-index:100}
.cat-inner{max-width:1500px;margin:0 auto;padding:0 56px;display:flex;overflow-x:auto}
.cat-inner::-webkit-scrollbar{display:none}
.cat-btn{background:none;border:none;color:rgba(255,255,255,0.45);font-size:11px;letter-spacing:.18em;text-transform:uppercase;padding:18px 28px;cursor:none;position:relative;white-space:nowrap;transition:color .25s;font-family:inherit}
.cat-btn::after{content:'';position:absolute;bottom:0;left:0;right:0;height:2px;background:#fff;transform:scaleX(0);transition:transform .35s var(--ease)}
.cat-btn.active{color:#fff}
.cat-btn.active::after{transform:scaleX(1)}
.discover-main{max-width:1500px;margin:0 auto;padding:0 56px}
.feature-hero{display:grid;grid-template-columns:1fr 1fr;min-height:88vh;margin-bottom:2px;overflow:hidden}
.feature-hero.reverse{direction:rtl}
.feature-hero.reverse>*{direction:ltr}
.feature-hero-img{position:relative;overflow:hidden}
.feature-hero-img img{width:100%;height:100%;object-fit:cover;transition:transform 1.2s cubic-bezier(.19,1,.22,1),filter .8s ease;transform:scale(1.04);filter:brightness(0.75) contrast(1.1)}
.feature-hero:hover .feature-hero-img img{transform:scale(1.09);filter:brightness(0.85) contrast(1.12)}
.feature-hero-img-overlay{position:absolute;inset:0;background:linear-gradient(to right,transparent 60%,rgba(0,0,0,0.7) 100%);z-index:1}
.feature-hero.reverse .feature-hero-img-overlay{background:linear-gradient(to left,transparent 60%,rgba(0,0,0,0.7) 100%)}
.feature-hero-body{background:#0c0c0c;display:flex;flex-direction:column;justify-content:center;padding:80px 72px;position:relative;overflow:hidden}
.feature-hero-body::before{content:'';position:absolute;top:-1px;bottom:-1px;width:1px;background:rgba(255,255,255,0.07);left:0}
.feature-hero.reverse .feature-hero-body::before{left:auto;right:0}
.fh-num{font-size:10px;letter-spacing:.4em;text-transform:uppercase;color:rgba(255,255,255,0.2);margin-bottom:24px;display:flex;align-items:center;gap:12px}
.fh-num-line{height:1px;width:32px;background:rgba(255,255,255,0.15)}
.feature-hero-body h2{font-size:clamp(2rem,3.2vw,3.2rem);font-weight:300;line-height:1.1;letter-spacing:-.02em;margin-bottom:20px}
.feature-hero-body p{font-size:.98rem;line-height:1.8;font-weight:300;color:rgba(255,255,255,0.62);margin-bottom:36px;max-width:420px}
.fh-stats{display:flex;gap:32px;margin-bottom:40px;padding:24px 0;border-top:1px solid rgba(255,255,255,0.07);border-bottom:1px solid rgba(255,255,255,0.07)}
.fh-stat-val{font-size:1.6rem;font-weight:300;letter-spacing:-.02em;color:#fff;margin-bottom:4px}
.fh-stat-lbl{font-size:9px;letter-spacing:.3em;text-transform:uppercase;color:rgba(255,255,255,0.35)}
.fh-cta{display:inline-flex;align-items:center;gap:10px;color:#fff;text-decoration:none;cursor:none;font-size:11px;letter-spacing:.2em;text-transform:uppercase;border-bottom:1px solid rgba(255,255,255,0.35);padding-bottom:4px;transition:border-color .3s,gap .3s}
.fh-cta:hover{border-color:#fff;gap:16px}
.features-section{padding:80px 0 40px}
.features-section-tag{display:flex;align-items:center;gap:14px;margin-bottom:14px}
.features-section-tag span{font-size:9px;letter-spacing:.4em;text-transform:uppercase;color:rgba(255,255,255,0.25)}
.features-section-tag-line{height:1px;flex:1;max-width:60px;background:rgba(255,255,255,0.12)}
.features-section h3{font-size:clamp(1.8rem,2.8vw,2.6rem);font-weight:300;letter-spacing:-.02em;margin-bottom:52px}
.features-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:2px}
.feat-card{position:relative;height:520px;overflow:hidden;cursor:none;background:#111}
.feat-card-img{position:absolute;inset:0}
.feat-card-img img{width:100%;height:100%;object-fit:cover;transition:transform .8s cubic-bezier(.19,1,.22,1),filter .6s ease;transform:scale(1.03);filter:brightness(0.55) contrast(1.1) saturate(0.9)}
.feat-card:hover .feat-card-img img{transform:scale(1.1);filter:brightness(0.7) contrast(1.15) saturate(1.05)}
.feat-card::after{content:'';position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,0.88) 0%,rgba(0,0,0,0.2) 50%,transparent 100%);z-index:1}
.feat-card-body{position:absolute;bottom:0;left:0;right:0;z-index:2;padding:32px 28px;transition:transform .5s var(--ease)}
.feat-card:hover .feat-card-body{transform:translateY(-6px)}
.feat-card-label{font-size:8px;letter-spacing:.45em;text-transform:uppercase;color:rgba(255,255,255,0.4);margin-bottom:10px;display:flex;align-items:center;gap:8px}
.feat-card-label::before{content:'';width:20px;height:1px;background:rgba(255,255,255,0.3)}
.feat-card-title{font-size:1.3rem;font-weight:400;letter-spacing:-.01em;margin-bottom:10px}
.feat-card-desc{font-size:.82rem;color:rgba(255,255,255,0.55);line-height:1.7;font-weight:300;max-height:0;overflow:hidden;transition:max-height .5s var(--ease),opacity .4s ease;opacity:0}
.feat-card:hover .feat-card-desc{max-height:80px;opacity:1}
.feat-card-arrow{display:inline-flex;align-items:center;gap:6px;margin-top:14px;font-size:9px;letter-spacing:.25em;text-transform:uppercase;color:rgba(255,255,255,0.5);transform:translateX(-4px);transition:transform .4s var(--ease),color .3s,opacity .3s;opacity:0}
.feat-card:hover .feat-card-arrow{transform:translateX(0);opacity:1;color:#fff}
.compare-section{padding:80px 0;border-top:1px solid rgba(255,255,255,0.07)}
.compare-hd{text-align:center;margin-bottom:56px}
.compare-hd h3{font-size:clamp(1.8rem,2.6vw,2.4rem);font-weight:300;letter-spacing:-.02em;margin-bottom:12px}
.compare-hd p{font-size:.9rem;color:rgba(255,255,255,0.45)}
.compare-grid{display:grid;grid-template-columns:repeat(4,1fr);border:1px solid rgba(255,255,255,0.07)}
.compare-col{border-right:1px solid rgba(255,255,255,0.07);padding:36px 32px;transition:background .3s}
.compare-col:last-child{border-right:none}
.compare-col:hover{background:rgba(255,255,255,0.025)}
.compare-col-head{display:flex;flex-direction:column;gap:6px;margin-bottom:28px;padding-bottom:20px;border-bottom:1px solid rgba(255,255,255,0.07)}
.compare-model{font-size:.85rem;color:rgba(255,255,255,0.45);font-weight:300}
.compare-name{font-size:1.1rem;font-weight:400}
.compare-row{padding:12px 0;border-bottom:1px solid rgba(255,255,255,0.04)}
.compare-row:last-child{border-bottom:none}
.compare-row-label{font-size:9px;letter-spacing:.25em;text-transform:uppercase;color:rgba(255,255,255,0.28);margin-bottom:5px}
.compare-row-val{font-size:.95rem;font-weight:300}
.compare-row-val span{color:rgba(255,255,255,0.6);font-size:.78rem;margin-left:4px}
.footer-cta{border-top:1px solid rgba(255,255,255,0.07);padding:80px 0;text-align:center}
.footer-cta h2{font-size:clamp(2.2rem,4vw,3.8rem);font-weight:300;letter-spacing:-.025em;margin-bottom:18px}
.footer-cta p{font-size:.98rem;color:rgba(255,255,255,0.5);max-width:460px;margin:0 auto 40px;line-height:1.75;font-weight:300}
.footer-cta-btns{display:flex;gap:14px;justify-content:center;flex-wrap:wrap}
.btn-outline{display:inline-flex;align-items:center;gap:8px;padding:13px 32px;border:1.5px solid rgba(255,255,255,0.4);color:#fff;text-decoration:none;cursor:none;font-size:11px;letter-spacing:.2em;text-transform:uppercase;position:relative;overflow:hidden;transition:color .35s,border-color .3s}
.btn-outline::before{content:'';position:absolute;inset:0;background:#fff;transform:translateX(-101%);transition:transform .5s var(--ease)}
.btn-outline:hover{color:#000;border-color:#fff}
.btn-outline:hover::before{transform:translateX(0)}
.btn-outline span{position:relative;z-index:1}
.btn-solid{display:inline-flex;align-items:center;gap:8px;padding:13px 32px;background:#fff;border:1.5px solid #fff;color:#000;text-decoration:none;cursor:none;font-size:11px;letter-spacing:.2em;text-transform:uppercase;transition:background .3s,color .3s}
.btn-solid:hover{background:transparent;color:#fff}
@keyframes fadeUp{from{opacity:0;transform:translateY(18px)}to{opacity:1;transform:translateY(0)}}
@keyframes fadeUpBig{from{opacity:0;transform:translateY(32px) skewY(2deg)}to{opacity:1;transform:translateY(0) skewY(0deg)}}
.reveal{opacity:0;transform:translateY(36px);transition:opacity .9s var(--ease),transform .9s var(--ease)}
.reveal.in{opacity:1;transform:translateY(0)}
.d1{transition-delay:.08s}.d2{transition-delay:.18s}.d3{transition-delay:.3s}.d4{transition-delay:.44s}
@media(max-width:1100px){.feature-hero{grid-template-columns:1fr;min-height:auto}.feature-hero-img{height:55vw}.features-grid{grid-template-columns:repeat(2,1fr)}.compare-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:768px){.nav-inner,.discover-main,.cat-inner{padding-left:20px;padding-right:20px}.hero-content{padding:0 32px 64px}.features-grid,.compare-grid{grid-template-columns:1fr}.feature-hero-body{padding:44px 32px}body{cursor:auto}#cur-d,#cur-r{display:none}.cat-btn,.nav-back,.btn-outline,.btn-solid{cursor:pointer}}
</style>
</head>
<body>
<div id="cur-d"></div><div id="cur-r"></div><div id="prog"></div>

<!-- INTRO CURTAIN (finder.php style) -->
<div id="intro">
    <div class="c-panel l"></div>
    <div id="intro-logo">
        <img src="/lending_word/public/assets/images/porsche-logo.png" alt="Porsche">
    </div>
    <div class="c-panel r"></div>
</div>

<nav class="nav" id="nav">
    <div class="nav-inner">
        <a href="/lending_word/" class="nav-logo">
            <img src="/lending_word/public/assets/images/porsche-logo2-png_seeklogo-314112-removebg-preview.png" alt="Porsche">
        </a>
        <a href="/lending_word/#features" class="nav-back">
            <i class="fas fa-chevron-left"></i><span>Kembali</span>
        </a>
    </div>
</nav>
<section class="hero" id="hero">
    <div class="hero-bg" id="heroBg" style="background-image:url('<?php echo !empty($discoverFeatures[0]['image']) ? htmlspecialchars($discoverFeatures[0]['image']) : 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=1600&q=80'; ?>')"></div>
    <div class="hero-content">
        <div class="hero-tag"><span class="hero-tag-line"></span><span>Discover</span></div>
        <h1>Temukan<br>Dunia Porsche</h1>
        <p class="hero-subtitle">Dari inovasi e-performance hingga aksesori eksklusif, setiap detail dirancang untuk memperkaya pengalaman berkendara Anda.</p>
    </div>
    <div class="hero-scroll"><span class="hero-scroll-text">Scroll</span><div class="scroll-wheel"></div></div>
</section>
<div class="cat-strip"><div class="cat-inner" id="catStrip"><button class="cat-btn active" data-cat="all">Semua</button></div></div>
<div class="discover-main">
    <div id="featureHeros"></div>
    <div class="features-section reveal">
        <div class="features-section-tag"><span>Highlights</span><div class="features-section-tag-line"></div></div>
        <h3>Eksplorasi Lebih Dalam</h3>
        <div class="features-grid" id="featuresGrid"></div>
    </div>
    <div class="compare-section reveal">
        <div class="compare-hd"><h3>Perbandingan Model</h3><p>Temukan spesifikasi lengkap untuk setiap model Porsche</p></div>
        <div class="compare-grid">
            <div class="compare-col"><div class="compare-col-head"><span class="compare-model">Sports Car</span><span class="compare-name">911 Carrera</span></div><div class="compare-row"><div class="compare-row-label">Akselerasi</div><div class="compare-row-val">4.0 <span>s 0–100 km/h</span></div></div><div class="compare-row"><div class="compare-row-label">Tenaga</div><div class="compare-row-val">283 <span>kW / 385 PS</span></div></div><div class="compare-row"><div class="compare-row-label">Top Speed</div><div class="compare-row-val">293 <span>km/h</span></div></div><div class="compare-row"><div class="compare-row-label">Drive</div><div class="compare-row-val">RWD</div></div></div>
            <div class="compare-col"><div class="compare-col-head"><span class="compare-model">Electric SUV</span><span class="compare-name">Macan 4 Electric</span></div><div class="compare-row"><div class="compare-row-label">Akselerasi</div><div class="compare-row-val">5.2 <span>s 0–100 km/h</span></div></div><div class="compare-row"><div class="compare-row-label">Tenaga</div><div class="compare-row-val">300 <span>kW / 408 PS</span></div></div><div class="compare-row"><div class="compare-row-label">Range</div><div class="compare-row-val">613 <span>km (WLTP)</span></div></div><div class="compare-row"><div class="compare-row-label">Drive</div><div class="compare-row-val">AWD</div></div></div>
            <div class="compare-col"><div class="compare-col-head"><span class="compare-model">Sports Sedan</span><span class="compare-name">Panamera Turbo E-Hybrid</span></div><div class="compare-row"><div class="compare-row-label">Akselerasi</div><div class="compare-row-val">3.2 <span>s 0–100 km/h</span></div></div><div class="compare-row"><div class="compare-row-label">Tenaga</div><div class="compare-row-val">515 <span>kW / 700 PS</span></div></div><div class="compare-row"><div class="compare-row-label">Top Speed</div><div class="compare-row-val">315 <span>km/h</span></div></div><div class="compare-row"><div class="compare-row-label">Drive</div><div class="compare-row-val">AWD</div></div></div>
            <div class="compare-col"><div class="compare-col-head"><span class="compare-model">SUV</span><span class="compare-name">Cayenne Turbo GT</span></div><div class="compare-row"><div class="compare-row-label">Akselerasi</div><div class="compare-row-val">3.3 <span>s 0–100 km/h</span></div></div><div class="compare-row"><div class="compare-row-label">Tenaga</div><div class="compare-row-val">471 <span>kW / 640 PS</span></div></div><div class="compare-row"><div class="compare-row-label">Top Speed</div><div class="compare-row-val">300 <span>km/h</span></div></div><div class="compare-row"><div class="compare-row-label">Drive</div><div class="compare-row-val">AWD</div></div></div>
        </div>
    </div>
    <div class="footer-cta reveal">
        <h2>Siap Merasakan<br>Perbedaannya?</h2>
        <p>Kunjungi dealer Porsche terdekat atau hubungi konsultan kami untuk pengalaman Porsche yang personal.</p>
        <div class="footer-cta-btns">
            <a href="/lending_word/finder.php" class="btn-solid"><span>Temukan Kendaraan</span></a>
            <a href="#" class="btn-outline"><span>Hubungi Kami</span></a>
        </div>
    </div>
</div>
<script>
(function(){
'use strict';

/* ── INTRO CURTAIN ── */
(function(){
    var intro = document.getElementById('intro');
    setTimeout(function(){
        intro.classList.add('open');
        setTimeout(function(){
            intro.classList.add('done');
            document.getElementById('heroBg').classList.add('loaded');
            setTimeout(function(){ intro.style.display = 'none'; }, 500);
        }, 1200);
    }, 900);
})();

const cd=document.getElementById('cur-d'),cr=document.getElementById('cur-r');
if(cd&&cr){
    let mx=0,my=0,rx=0,ry=0;
    window.addEventListener('mousemove',e=>{mx=e.clientX;my=e.clientY;cd.style.left=mx+'px';cd.style.top=my+'px';},{passive:true});
    (function tick(){rx+=(mx-rx)*.12;ry+=(my-ry)*.12;cr.style.left=rx+'px';cr.style.top=ry+'px';requestAnimationFrame(tick);})();
    document.querySelectorAll('a,button').forEach(el=>{
        el.addEventListener('mouseenter',()=>document.body.classList.add('c-link'));
        el.addEventListener('mouseleave',()=>document.body.classList.remove('c-link'));
    });
}
const prog=document.getElementById('prog'),nav=document.getElementById('nav');
window.addEventListener('scroll',()=>{
    const maxY=document.body.scrollHeight-window.innerHeight;
    if(prog)prog.style.width=(maxY>0?(window.scrollY/maxY*100):0)+'%';
    nav.classList.toggle('solid',window.scrollY>60);
},{passive:true});
const ro=new IntersectionObserver(entries=>{
    entries.forEach(e=>{if(e.isIntersecting){e.target.classList.add('in');ro.unobserve(e.target);}});
},{threshold:.08,rootMargin:'0px 0px -40px 0px'});
document.querySelectorAll('.reveal').forEach(el=>ro.observe(el));

// Schema: id, title, description, image, sort_order, created_at,
//         category, stats (JSON text), link_url, link_label, is_featured
const features = <?php echo $features_json; ?>;

function buildCategories(){
    const cats=[...new Set(features.map(f=>f.category).filter(Boolean))];
    const strip=document.getElementById('catStrip');
    cats.forEach(cat=>{
        const btn=document.createElement('button');
        btn.className='cat-btn';btn.dataset.cat=cat;btn.textContent=cat;
        btn.onclick=()=>filterFeatures(cat);strip.appendChild(btn);
    });
    strip.querySelector('[data-cat="all"]').onclick=()=>filterFeatures('all');
}
function filterFeatures(cat){
    document.querySelectorAll('.cat-btn').forEach(b=>b.classList.toggle('active',b.dataset.cat===cat));
    renderHeros(cat);renderGrid(cat);
}
function renderHeros(cat){
    const container=document.getElementById('featureHeros');
    const list=cat==='all'?features.slice(0,4):features.filter(f=>f.category===cat).slice(0,4);
    container.innerHTML='';
    list.forEach((f,i)=>{
        const reverse=i%2!==0;
        let stats=[];
        if(f.stats){try{stats=JSON.parse(f.stats);}catch(e){}}
        const linkUrl=f.link_url||'';
        const linkLabel=f.link_label||'Pelajari Lebih Lanjut';
        const el=document.createElement('div');
        el.className='feature-hero reveal'+(reverse?' reverse':'');
        el.innerHTML=`
            <div class="feature-hero-img">
                <img src="${esc(f.image||'')}" alt="${esc(f.title||'')}" loading="lazy"
                     onerror="this.src='https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=1400&q=80'">
                <div class="feature-hero-img-overlay"></div>
            </div>
            <div class="feature-hero-body">
                <div class="fh-num"><span class="fh-num-line"></span><span>0${i+1} — ${esc(f.category||'Discover')}</span></div>
                <h2>${esc(f.title||'')}</h2>
                <p>${esc(f.description||'')}</p>
                ${stats.length?`<div class="fh-stats">${stats.map(s=>`<div><div class="fh-stat-val">${esc(String(s.val||''))}</div><div class="fh-stat-lbl">${esc(String(s.lbl||''))}</div></div>`).join('')}</div>`:''}
                ${linkUrl?`<a href="${esc(linkUrl)}" class="fh-cta">${esc(linkLabel)} <i class="fas fa-arrow-right"></i></a>`:''}
            </div>`;
        container.appendChild(el);
        requestAnimationFrame(()=>requestAnimationFrame(()=>el.classList.add('in')));
        el.querySelector('.feature-hero-img').addEventListener('mouseenter',()=>document.body.classList.add('c-img'));
        el.querySelector('.feature-hero-img').addEventListener('mouseleave',()=>document.body.classList.remove('c-img'));
    });
}
function renderGrid(cat){
    const container=document.getElementById('featuresGrid');
    const list=cat==='all'?features:features.filter(f=>f.category===cat);
    container.innerHTML='';
    list.forEach((f,i)=>{
        const el=document.createElement('div');
        el.className=`feat-card reveal d${Math.min(i%3+1,4)}`;
        const desc=(f.description||'').substring(0,120);
        const linkUrl=f.link_url||'';
        const linkLabel=f.link_label||'Selengkapnya';
        el.innerHTML=`
            <div class="feat-card-img">
                <img src="${esc(f.image||'')}" alt="${esc(f.title||'')}" loading="lazy"
                     onerror="this.src='https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=800&q=80'">
            </div>
            <div class="feat-card-body">
                <div class="feat-card-label">${esc(f.category||'Discover')}</div>
                <div class="feat-card-title">${esc(f.title||'')}</div>
                <div class="feat-card-desc">${esc(desc)}${desc.length>=120?'...':''}</div>
                <div class="feat-card-arrow">${esc(linkLabel)} <i class="fas fa-arrow-right" style="font-size:8px"></i></div>
            </div>`;
        if(linkUrl){el.style.cursor='pointer';el.onclick=()=>window.location.href=linkUrl;}
        container.appendChild(el);
        el.addEventListener('mouseenter',()=>document.body.classList.add('c-img'));
        el.addEventListener('mouseleave',()=>document.body.classList.remove('c-img'));
        requestAnimationFrame(()=>requestAnimationFrame(()=>ro.observe(el)));
    });
}
function esc(str){return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');}
buildCategories();renderHeros('all');renderGrid('all');
let raf=false;
window.addEventListener('scroll',()=>{
    if(raf)return;raf=true;
    requestAnimationFrame(()=>{
        const bg=document.getElementById('heroBg');
        if(bg)bg.style.transform=`scale(1) translateY(${window.scrollY*0.25}px)`;
        raf=false;
    });
},{passive:true});
})();
</script>
</body>
</html>