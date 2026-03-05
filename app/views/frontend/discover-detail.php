<?php
require_once __DIR__ . '/../../../app/database.php';
$pdo = Database::getInstance()->getConnection();

$slug = $_GET['slug'] ?? null;
$id   = $_GET['id']   ?? null;

try {
    if ($slug) {
        $stmt = $pdo->prepare("SELECT * FROM discover_features WHERE slug = ? LIMIT 1");
        $stmt->execute([$slug]);
    } elseif ($id) {
        $stmt = $pdo->prepare("SELECT * FROM discover_features WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
    } else {
        $stmt = $pdo->query("SELECT * FROM discover_features ORDER BY sort_order ASC, id ASC LIMIT 1");
    }
    $feature = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) { $feature = null; }

if (!$feature) {
    try {
        $stmt = $pdo->query("SELECT * FROM discover_features ORDER BY sort_order ASC, id ASC");
        $allFeatures = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) { $allFeatures = []; }
}

function parseJson($val, $default = []) {
    if (empty($val)) return $default;
    $d = json_decode($val, true);
    return is_array($d) ? $d : $default;
}

$sections      = parseJson($feature['sections']       ?? null);
$highlights    = parseJson($feature['highlights']     ?? null);
$gallery       = parseJson($feature['gallery']        ?? null);
$relatedModels = parseJson($feature['related_models'] ?? null);
$stats         = parseJson($feature['stats']          ?? null);

$heroImage    = $feature['hero_image']    ?? $feature['image']       ?? '';
$heroTitle    = $feature['hero_title']    ?? $feature['title']       ?? 'Discover';
$heroSubtitle = $feature['hero_subtitle'] ?? $feature['description'] ?? '';
$metaTitle    = $feature['meta_title']    ?? ($heroTitle . ' — Porsche Indonesia');

try {
    $navStmt = $pdo->query("SELECT id, title, slug, image, category FROM discover_features ORDER BY sort_order ASC, id ASC");
    $allNav  = $navStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) { $allNav = []; }

$gallerySections = [];
if ($feature) {
    try {
        $phStmt = $pdo->prepare("SELECT * FROM discover_gallery WHERE feature_id = ? ORDER BY sort_order ASC, id ASC");
        $phStmt->execute([$feature['id']]);
        $gallerySections = $phStmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) { $gallerySections = []; }
}

foreach ($sections as $k => &$_s) {
    if (!isset($_s['sort_order'])) $_s['sort_order'] = $k * 10;
}
unset($_s);

foreach ($gallerySections as $_gs) {
    $_gs['type'] = 'gallery-layout';
    $sections[] = $_gs;
}

usort($sections, function($a, $b) {
    return (int)($a['sort_order'] ?? 999) <=> (int)($b['sort_order'] ?? 999);
});

$currentIdx = -1;
foreach ($allNav as $i => $n) {
    if ($feature && $n['id'] == $feature['id']) { $currentIdx = $i; break; }
}
$prevFeature = $currentIdx > 0 ? $allNav[$currentIdx - 1] : null;
$nextFeature = $currentIdx >= 0 && $currentIdx < count($allNav) - 1 ? $allNav[$currentIdx + 1] : null;

function fUrl($f) {
    if (!empty($f['slug'])) return '/lending_word/discover-detail.php?slug=' . urlencode($f['slug']);
    return '/lending_word/discover-detail.php?id=' . (int)$f['id'];
}

function buildTabs($sections) {
    $tabs = [];
    foreach ($sections as $sec) {
        if (!empty($sec['tab_label']) && !empty($sec['tab_id'])) {
            $tabs[] = ['label' => $sec['tab_label'], 'id' => $sec['tab_id']];
        }
    }
    return $tabs;
}
?><!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($metaTitle) ?></title>
<meta name="description" content="<?= htmlspecialchars(mb_substr(strip_tags($heroSubtitle), 0, 160)) ?>">
<link rel="icon" type="image/png" href="/lending_word/public/assets/images/porsche-logo.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
/* ── RESET ── */
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
html{scroll-behavior:smooth;-webkit-text-size-adjust:100%}
body{
  font-family:'DM Sans',Helvetica,Arial,sans-serif;
  background:#fff;color:#1a1a1a;
  overflow-x:hidden;
  -webkit-font-smoothing:antialiased;
}
a{color:inherit;text-decoration:none}
img{display:block;max-width:100%;height:auto}
button{font-family:inherit;cursor:pointer}
::-webkit-scrollbar{width:3px;height:3px}
::-webkit-scrollbar-thumb{background:#ccc}

/* ── TOPBAR ── */
.topbar{
  position:fixed;top:0;left:0;right:0;z-index:500;
  height:56px;
  display:flex;align-items:center;justify-content:space-between;
  padding:0 32px;
  background:rgba(255,255,255,0.95);
  backdrop-filter:blur(20px);
  border-bottom:1px solid rgba(0,0,0,0.1);
  transition:background .3s,border-color .3s;
}
.topbar.over-hero{background:transparent;border-color:transparent}
.topbar.over-hero .topbar-logo img{filter:brightness(0) invert(1)}
.topbar.over-hero .topbar-back{color:rgba(255,255,255,.7);border-color:rgba(255,255,255,.3)}
.topbar.over-hero .topbar-back:hover{color:#fff;border-color:#fff}
.topbar-logo img{height:40px;transition:filter .3s}
.topbar-back{
  display:inline-flex;align-items:center;gap:7px;
  font-size:11px;letter-spacing:.18em;text-transform:uppercase;font-weight:500;
  color:rgba(0,0,0,.55);
  border:1px solid rgba(0,0,0,.2);
  padding:8px 18px;
  transition:color .2s,border-color .2s,background .2s;
  background:transparent;
}
.topbar-back:hover{color:#000;border-color:#000}

/* ── HERO ── */
.hero{
  position:relative;
  height:100svh;min-height:500px;
  overflow:hidden;
  display:flex;align-items:flex-end;
}
.hero-bg{
  position:absolute;inset:0;
  background-size:cover;background-position:center;
  transform:scale(1.08);
  transition:transform 6s ease-out;
}
.hero-bg.loaded{transform:scale(1.08)}
.hero-vignette{
  position:absolute;inset:0;
  background:linear-gradient(to top,rgba(0,0,0,.75) 0%,rgba(0,0,0,.15) 45%,rgba(0,0,0,.05) 100%);
}
.hero-content{
  position:relative;z-index:2;
  padding:0 72px 80px;
  color:#fff;
  max-width:960px;
}
.hero-eyebrow{
  font-size:11px;letter-spacing:.35em;text-transform:uppercase;
  color:rgba(255,255,255,.5);margin-bottom:16px;
  display:flex;align-items:center;gap:12px;
  opacity:0;animation:fuA .4s .1s ease forwards;
}
.hero-eyebrow-line{width:32px;height:1px;background:rgba(255,255,255,.3)}
.hero h1{
  font-size:clamp(3rem,6vw,6rem);
  font-weight:300;line-height:1.02;letter-spacing:-.03em;
  color:#fff;
  opacity:0;animation:fuB .5s .2s cubic-bezier(.16,1,.3,1) forwards;
}
.hero-scroll{
  position:absolute;bottom:36px;left:50%;transform:translateX(-50%);
  z-index:2;display:flex;flex-direction:column;align-items:center;gap:6px;
  opacity:0;animation:fuA .4s .6s ease forwards;
}
.hs-word{font-size:9px;letter-spacing:.4em;text-transform:uppercase;color:rgba(255,255,255,.35)}
.hs-line{width:1px;height:40px;background:rgba(255,255,255,.2);overflow:hidden;position:relative}
.hs-line::after{
  content:'';position:absolute;top:-40px;left:0;right:0;height:100%;
  background:#fff;animation:drop 2.4s ease-in-out infinite;
}
@keyframes drop{0%{top:-40px;opacity:1}100%{top:40px;opacity:0}}

/* ── TAB STRIP ── */
.tab-strip{
  background:#fff;
  border-bottom:1px solid #e0e0e0;
  position:sticky;top:56px;z-index:200;
}
.tab-strip-inner{
  max-width:1400px;margin:0 auto;
  padding:0 72px;
  display:flex;overflow-x:auto;
  scrollbar-width:none;gap:0;
}
.tab-strip-inner::-webkit-scrollbar{display:none}
.tab-btn{
  display:inline-flex;align-items:center;
  padding:0 24px;height:50px;
  font-size:12px;letter-spacing:.08em;font-weight:400;
  color:rgba(0,0,0,.38);
  position:relative;white-space:nowrap;
  background:none;border:none;cursor:pointer;
  transition:color .22s;
  text-decoration:none;
}
.tab-btn::after{
  content:'';position:absolute;bottom:0;left:0;right:0;height:2px;
  background:#000;transform:scaleX(0);transform-origin:left;
  transition:transform .3s cubic-bezier(.16,1,.3,1);
}
.tab-btn.active,
.tab-btn:hover{color:#000}
.tab-btn.active::after{transform:scaleX(1)}

/* ── PAGE ── */
.page{background:#fff}

/* ── INTRO TEXT ── */
.intro-centered{
  padding:80px 72px 60px;
  text-align:center;
  max-width:900px;margin:0 auto;
}
.intro-centered p{
  font-size:clamp(1.25rem,2.2vw,1.7rem);
  line-height:1.55;font-weight:400;
  color:#1a1a1a;
  letter-spacing:-.01em;
}

/* ── CARD SLIDER ── */
.card-slider{
  padding:40px 0 56px;
}
.cs-head{
  max-width:1400px;margin:0 auto;
  padding:0 72px 28px;
  display:flex;align-items:center;justify-content:space-between;
}
.cs-head-title{
  font-size:clamp(1.8rem,3vw,2.6rem);
  font-weight:300;letter-spacing:-.025em;line-height:1.15;
}
.cs-nav{display:flex;gap:0;border:1px solid #d0d0d0;}
.cs-nav-btn{
  width:40px;height:40px;
  display:flex;align-items:center;justify-content:center;
  background:#fff;color:#1a1a1a;font-size:13px;
  border:none;cursor:pointer;
  transition:background .18s;
}
.cs-nav-btn:first-child{border-right:1px solid #d0d0d0}
.cs-nav-btn:hover{background:#f5f5f5}
.cs-rail{overflow:hidden;padding:0}
.cs-track{
  display:flex;gap:16px;
  padding:0 72px;
  transition:transform .55s cubic-bezier(.16,1,.3,1);
}

/* Card A: overlay */
.cs-card-overlay{
  flex:0 0 calc(50% - 8px);
  border-radius:8px;overflow:hidden;
  position:relative;background:#f0f0f0;
  cursor:pointer;
}
.cs-card-overlay img{
  width:100%;aspect-ratio:16/10;object-fit:cover;display:block;
  transition:transform .9s cubic-bezier(.16,1,.3,1);
  filter:brightness(.75);
}
.cs-card-overlay:hover img{transform:scale(1.04)}
.cs-card-overlay-body{
  position:absolute;bottom:0;left:0;right:0;
  padding:24px 28px;
  display:flex;align-items:flex-end;justify-content:space-between;
}
.cs-card-overlay-title{
  font-size:1.1rem;font-weight:400;color:#fff;
  letter-spacing:-.01em;
}
.cs-card-overlay-arrow{
  width:32px;height:32px;border-radius:50%;
  border:1.5px solid rgba(255,255,255,.6);
  display:flex;align-items:center;justify-content:center;
  color:#fff;font-size:11px;flex-shrink:0;
  transition:border-color .2s,background .2s;
}
.cs-card-overlay:hover .cs-card-overlay-arrow{border-color:#fff;background:rgba(255,255,255,.15)}

/* Card B: split */
.cs-card-split{
  flex:0 0 calc(60% - 8px);
  display:flex;gap:0;background:#fff;
  cursor:pointer;
}
.cs-card-split-img{
  flex:0 0 50%;border-radius:8px;overflow:hidden;background:#f0f0f0;
}
.cs-card-split-img img{
  width:100%;height:100%;object-fit:cover;
  transition:transform .9s cubic-bezier(.16,1,.3,1);
  min-height:300px;
}
.cs-card-split:hover .cs-card-split-img img{transform:scale(1.04)}
.cs-card-split-body{
  flex:1;padding:32px 36px;
  display:flex;flex-direction:column;justify-content:center;
}
.cs-card-split-title{
  font-size:1.3rem;font-weight:700;letter-spacing:-.02em;
  margin-bottom:14px;line-height:1.2;
}
.cs-card-split-desc{
  font-size:.9rem;line-height:1.78;font-weight:400;
  color:#3a3a3a;
}

/* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
   Card C: History style — UPDATED per referensi
   ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
.cs-card-history{
  flex:0 0 calc(50% - 8px);
  border-radius:12px;            /* lebih rounded */
  overflow:hidden;
  position:relative;background:#111;
  cursor:pointer;
}
.cs-card-history img{
  width:100%;
  aspect-ratio:16/11;            /* landscape 16/11 */
  object-fit:cover;display:block;
  transition:transform .9s cubic-bezier(.16,1,.3,1);
  filter:brightness(.7);
}
.cs-card-history:hover img{transform:scale(1.04);filter:brightness(.6)}
/* gradient lebih kuat dari bawah supaya teks terbaca */
.cs-card-history::after{
  content:'';position:absolute;inset:0;
  background:linear-gradient(
    to top,
    rgba(0,0,0,.88) 0%,
    rgba(0,0,0,.55) 35%,
    rgba(0,0,0,.15) 60%,
    transparent 100%
  );
}
.cs-card-history-body{
  position:absolute;bottom:0;left:0;right:0;z-index:2;
  padding:28px 28px 28px 28px;
}
.cs-card-history-text{flex:1;}
.cs-card-history-title{
  font-size:1.45rem;font-weight:700;color:#fff;
  letter-spacing:-.02em;margin-bottom:10px;line-height:1.15;
}
.cs-card-history-desc{
  font-size:.875rem;line-height:1.7;color:rgba(255,255,255,.85);font-weight:400;
  max-width:380px;
  /* deskripsi muncul di bawah judul, max 200 char — truncate via PHP */
}
/* Arrow bulat di pojok kanan bawah */
.cs-card-history-arrow{
  position:absolute;
  bottom:24px;
  right:24px;
  z-index:3;
  width:36px;height:36px;border-radius:50%;flex-shrink:0;
  border:1.5px solid rgba(255,255,255,.6);
  display:flex;align-items:center;justify-content:center;
  color:#fff;font-size:11px;
  transition:border-color .2s,background .2s,transform .2s;
  background:rgba(0,0,0,.2);
}
.cs-card-history:hover .cs-card-history-arrow{
  border-color:#fff;
  background:rgba(255,255,255,.18);
  transform:translate(2px,-2px);
}

.cs-dots{
  max-width:1400px;margin:20px auto 0;
  padding:0 72px;display:flex;gap:6px;align-items:center;
}
.cs-dot{
  width:28px;height:3px;background:#d0d0d0;
  border:none;padding:0;cursor:pointer;
  transition:all .28s;border-radius:2px;
}
.cs-dot.on{background:#000;width:40px}

/* ── SECTION DIVIDER ── */
.sec-divider{height:1px;background:#e8e8e8;margin:0}

/* ── QUOTE BLOCK ── */
.quote-block{
  padding:88px 72px;
  text-align:center;
  background:#fff;
}
.quote-block-text{
  font-size:clamp(1.6rem,3vw,2.8rem);
  font-weight:700;line-height:1.3;letter-spacing:-.03em;
  color:#1a1a1a;
  max-width:820px;margin:0 auto;
}
.quote-block-author{
  display:block;margin-top:24px;
  font-size:11px;letter-spacing:.28em;text-transform:uppercase;
  color:#888;
}

/* ── IMAGE FULL WIDTH ── */
.img-full{position:relative;overflow:hidden;background:#000;}
.img-full img{
  width:100%;aspect-ratio:21/9;object-fit:cover;display:block;
}
.img-full-caption{
  max-width:1400px;margin:10px auto 0;
  padding:0 72px;
  font-size:.75rem;color:#999;
}

/* ── CENTERED TITLE ── */
.centered-title{
  padding:80px 72px 56px;
  text-align:center;
}
.centered-title h2{
  font-size:clamp(2rem,4vw,3.4rem);
  font-weight:700;line-height:1.15;letter-spacing:-.03em;
  max-width:780px;margin:0 auto;
}
.centered-title p{
  margin-top:20px;
  font-size:clamp(.95rem,1.2vw,1.05rem);
  font-weight:400;color:#3a3a3a;line-height:1.75;
  max-width:620px;margin-left:auto;margin-right:auto;
}

/* ── SPLIT ── */
.split-block{
  display:grid;grid-template-columns:1fr 1fr;
  min-height:580px;
  overflow:hidden;
}
.split-block.img-left{direction:rtl}
.split-block.img-left>*{direction:ltr}
.split-text{
  padding:72px 80px;
  display:flex;flex-direction:column;justify-content:center;
  background:#fff;
}
.split-eyebrow{
  font-size:11px;letter-spacing:.3em;text-transform:uppercase;
  color:#888;margin-bottom:20px;font-weight:500;
}
.split-text h2{
  font-size:clamp(1.8rem,2.8vw,2.8rem);
  font-weight:300;letter-spacing:-.025em;line-height:1.12;
  margin-bottom:20px;
}
.split-text p{
  font-size:.97rem;line-height:1.82;font-weight:300;color:#444;
}
.split-img-wrap{
  position:relative;overflow:visible;background:#f4f4f2;
}
.split-img-wrap img{
  width:100%;height:100%;object-fit:cover;display:block;
  transition:transform .9s cubic-bezier(.16,1,.3,1);
}
.split-block:hover .split-img-wrap img{transform:scale(1.03)}

/* Gallery overlap style */
.split-block.disc-gallery-style{
  position:relative;
  align-items:stretch;
  overflow:visible;
}
.split-block.disc-gallery-style .split-text{
  padding:80px 72px 80px 72px;
  z-index:2;
}
.split-block.disc-gallery-style .split-img-primary{
  position:relative;
  background:#f0f0f0;
  overflow:hidden;
}
.split-block.disc-gallery-style .split-img-primary img{
  width:100%;height:100%;object-fit:cover;
}
.split-block.disc-gallery-style .split-img-secondary{
  position:absolute;
  right:-40px;
  top:50%;
  transform:translateY(-50%);
  width:55%;
  aspect-ratio:3/4;
  overflow:hidden;
  border-radius:4px;
  box-shadow:0 20px 60px rgba(0,0,0,.25);
  z-index:3;
}
.split-block.disc-gallery-style .split-img-secondary img{
  width:100%;height:100%;object-fit:cover;
}

/* ── STAT GRID ── */
.stat-block{padding:64px 72px;background:#fff;}
.stat-block-title{
  font-size:clamp(1.5rem,2.4vw,2.2rem);font-weight:300;letter-spacing:-.02em;
  margin-bottom:32px;
}
.stat-grid{
  display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));
  border:1px solid #e8e8e8;
}
.stat-item{
  padding:36px 28px;border-right:1px solid #e8e8e8;
  transition:background .25s;
}
.stat-item:last-child{border-right:none}
.stat-item:hover{background:#fafafa}
.stat-val{font-size:2.8rem;font-weight:300;letter-spacing:-.04em;line-height:1;margin-bottom:6px}
.stat-lbl{font-size:9px;letter-spacing:.34em;text-transform:uppercase;color:#999;margin-bottom:10px}
.stat-desc{font-size:.8rem;color:#666;line-height:1.72;font-weight:300}

/* ── PHILOSOPHY / GALLERY STYLE ── */
.disc-gallery-block{
  padding:120px 0;
  background:#fff;
  overflow:visible;
}
.disc-gallery-layout{
  max-width:1050px;
  margin:0 auto;
  padding:0 20px;
  position:relative;
}
.disc-gallery-img-top{
  width:100%;
  margin-bottom:-120px;
  position:relative;
  z-index:1;
}
.disc-gallery-img-top img{
  width:100%;
  height:340px;
  object-fit:cover;
  border-radius:20px;
  box-shadow:0 30px 80px rgba(0,0,0,.1);
  display:block;
  transition:transform .6s cubic-bezier(.16,1,.3,1);
}
.disc-gallery-img-top:hover img{transform:scale(1.01);}
.disc-gallery-content-row{
  display:flex;
  justify-content:space-between;
  align-items:flex-start;
  position:relative;
  z-index:2;
}
.disc-gallery-text{
  width:42%;
  margin-top:150px;
  padding:40px 40px 40px 0;
  flex-shrink:0;
}
.disc-gallery-text h2{
  font-size:clamp(2rem,3.5vw,3rem);
  font-weight:300;letter-spacing:-.03em;line-height:1.08;
  margin-bottom:24px;color:#1a1a1a;
}
.disc-gallery-text p{
  font-size:.95rem;line-height:1.82;
  color:#444;font-weight:300;
}
.disc-gallery-img-right{
  width:55%;
  margin-right:-200px;
  flex-shrink:0;
}
.disc-gallery-img-right img{
  width:100%;
  height:600px;
  object-fit:cover;
  border-radius:16px;
  box-shadow:0 30px 80px rgba(0,0,0,.15);
  display:block;
  transition:transform .6s cubic-bezier(.16,1,.3,1),box-shadow .6s ease;
}
.disc-gallery-img-right:hover img{
  transform:scale(1.02) translateY(-6px);
  box-shadow:0 50px 100px rgba(0,0,0,.2);
}
.disc-gallery-img-bottom{
  width:50%;
  margin-left:20%;
  margin-top:-90px;
  position:relative;
  z-index:3;
}
.disc-gallery-img-bottom img{
  width:100%;
  border-radius:16px;
  box-shadow:0 20px 50px rgba(0,0,0,.12);
  display:block;
  transition:transform .6s cubic-bezier(.16,1,.3,1),box-shadow .6s ease;
}
.disc-gallery-img-bottom:hover img{
  transform:scale(1.02) translateY(-4px);
  box-shadow:0 40px 80px rgba(0,0,0,.18);
}

@media(max-width:900px){
  .disc-gallery-block{padding:60px 0}
  .disc-gallery-layout{padding:0 20px}
  .disc-gallery-content-row{flex-direction:column}
  .disc-gallery-text{width:100%;margin-top:60px;padding:24px 0;margin-right:0}
  .disc-gallery-img-right{width:100%;margin-right:0}
  .disc-gallery-img-right img{height:300px}
  .disc-gallery-img-top{margin-bottom:-40px}
  .disc-gallery-img-top img{height:220px}
  .disc-gallery-img-bottom{width:80%;margin-left:0;margin-top:-40px}
}

/* ── TWO COL TEXT ── */
.twocol-block{
  padding:64px 72px;
  display:grid;grid-template-columns:1fr 1fr;gap:64px;
  border-top:1px solid #e8e8e8;
}
.twocol-col h3{font-size:1.3rem;font-weight:300;letter-spacing:-.015em;margin-bottom:14px}
.twocol-col p{font-size:.93rem;line-height:1.82;color:#555;font-weight:300}

/* ── CTA DARK ── */
.cta-dark{
  background:#1a1a1a;padding:80px 72px;
  display:grid;grid-template-columns:1fr 1fr;gap:80px;align-items:center;
}
.cta-eyebrow{font-size:10px;letter-spacing:.35em;text-transform:uppercase;color:rgba(255,255,255,.35);margin-bottom:16px}
.cta-dark h2{font-size:clamp(1.6rem,2.8vw,2.6rem);font-weight:300;letter-spacing:-.02em;color:#fff;margin-bottom:18px}
.cta-dark p{font-size:.93rem;line-height:1.82;font-weight:300;color:rgba(255,255,255,.55)}
.cta-img{overflow:hidden;aspect-ratio:4/3;background:#111;border-radius:4px}
.cta-img img{width:100%;height:100%;object-fit:cover;filter:brightness(.75);transition:transform .9s cubic-bezier(.16,1,.3,1)}
.cta-dark:hover .cta-img img{transform:scale(1.03)}
.cta-btn{
  margin-top:28px;display:inline-flex;align-items:center;gap:10px;
  font-size:11px;letter-spacing:.2em;text-transform:uppercase;font-weight:500;
  color:#fff;border-bottom:1px solid rgba(255,255,255,.4);padding-bottom:4px;
  transition:border-color .22s;
}
.cta-btn:hover{border-color:#fff}

/* ── PREV/NEXT ── */
.pn{display:grid;grid-template-columns:1fr 1fr;border-top:1px solid #e8e8e8}
.pn-btn{
  position:relative;height:220px;overflow:hidden;
  display:flex;flex-direction:column;justify-content:flex-end;
  padding:36px 48px;cursor:pointer;
  border-right:1px solid #e8e8e8;
  background:#fafafa;
  transition:background .3s;
}
.pn-btn:last-child{border-right:none;text-align:right}
.pn-bg{
  position:absolute;inset:0;background-size:cover;background-position:center;
  filter:brightness(.32);opacity:0;
  transition:opacity .5s,transform .8s cubic-bezier(.16,1,.3,1);
}
.pn-btn:hover .pn-bg{opacity:1;transform:scale(1.04)}
.pn-btn::after{content:'';position:absolute;inset:0;background:rgba(0,0,0,0);transition:background .5s}
.pn-btn:hover::after{background:rgba(0,0,0,.1)}
.pn-body{position:relative;z-index:2;transition:color .4s}
.pn-btn:hover .pn-body{color:#fff}
.pn-dir{
  font-size:10px;letter-spacing:.3em;text-transform:uppercase;
  color:#888;margin-bottom:12px;font-weight:500;
  display:flex;align-items:center;gap:8px;
  transition:color .4s;
}
.pn-btn:last-child .pn-dir{justify-content:flex-end}
.pn-btn:hover .pn-dir{color:rgba(255,255,255,.55)}
.pn-title{font-size:1.5rem;font-weight:300;letter-spacing:-.015em;color:#1a1a1a;transition:color .4s}
.pn-btn:hover .pn-title{color:#fff}

/* ── LISTING ── */
.listing-page{max-width:1400px;margin:0 auto;padding:100px 72px 80px}
.listing-page h1{font-size:clamp(2.5rem,5vw,4.5rem);font-weight:300;letter-spacing:-.025em;margin-bottom:10px}
.listing-sub{font-size:1rem;color:#888;margin-bottom:52px}
.listing-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:2px}
.lst-card{position:relative;height:500px;overflow:hidden;background:#ddd;display:block}
.lst-card img{width:100%;height:100%;object-fit:cover;filter:brightness(.72);transition:transform .9s cubic-bezier(.16,1,.3,1),filter .6s}
.lst-card:hover img{transform:scale(1.06);filter:brightness(.85)}
.lst-card::after{content:'';position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.82) 0%,rgba(0,0,0,.12) 55%,transparent 100%)}
.lst-body{position:absolute;bottom:0;left:0;right:0;z-index:2;padding:28px 24px;color:#fff}
.lst-cat{font-size:9px;letter-spacing:.4em;text-transform:uppercase;color:rgba(255,255,255,.45);margin-bottom:8px}
.lst-title{font-size:1.3rem;font-weight:300;letter-spacing:-.01em}

/* ── REVEAL ANIMATION ── */
.rv{opacity:0;transform:translateY(22px);transition:opacity .8s cubic-bezier(.16,1,.3,1),transform .8s cubic-bezier(.16,1,.3,1)}
.rv.in{opacity:1;transform:translateY(0)}
.d1{transition-delay:.08s}.d2{transition-delay:.18s}.d3{transition-delay:.3s}

@keyframes fuA{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}
@keyframes fuB{from{opacity:0;transform:translateY(24px)}to{opacity:1;transform:translateY(0)}}

/* ── RESPONSIVE ── */
@media(max-width:900px){
  .hero-content{padding:0 24px 56px}
  .tab-strip-inner{padding:0 20px}
  .intro-centered{padding:56px 24px 40px}
  .cs-head{padding:0 20px 20px}
  .cs-track{padding:0 20px;gap:10px}
  .cs-card-overlay{flex:0 0 80%}
  .cs-card-history{flex:0 0 78%}
  .cs-card-split{flex:0 0 85%;flex-direction:column}
  .cs-dots{padding:0 20px}
  .quote-block{padding:56px 24px}
  .img-full img{aspect-ratio:4/3}
  .img-full-caption{padding:0 24px}
  .centered-title{padding:56px 24px 40px}
  .split-block{grid-template-columns:1fr}
  .split-block.img-left{direction:ltr}
  .split-text{padding:48px 24px}
  .stat-block{padding:40px 24px}
  .stat-grid{grid-template-columns:1fr 1fr}
  .stat-item{border-bottom:1px solid #e8e8e8;border-right:none}
  .twocol-block{grid-template-columns:1fr;padding:40px 24px;gap:32px}
  .cta-dark{grid-template-columns:1fr;padding:48px 24px;gap:32px}
  .pn{grid-template-columns:1fr}
  .pn-btn{height:160px;padding:24px 20px}
  .listing-page{padding:80px 20px 60px}
  .listing-grid{grid-template-columns:1fr}
  .split-block.disc-gallery-style .split-img-secondary{display:none}
}
</style>
</head>
<body>

<!-- TOPBAR -->
<header class="topbar" id="topbar">
    <a href="/lending_word/" class="topbar-logo">
        <img src="/lending_word/public/assets/images/porsche-logo2-png_seeklogo-314112-removebg-preview.png" alt="Porsche">
    </a>
    <a href="/lending_word/#features" class="topbar-back">
        <i class="fas fa-chevron-left" style="font-size:9px"></i> Discover
    </a>
</header>

<?php if ($feature): ?>

<!-- HERO -->
<section class="hero">
    <div class="hero-bg" id="heroBg" style="background-image:url('<?= htmlspecialchars($heroImage) ?>')"></div>
    <div class="hero-vignette"></div>
    <div class="hero-content">
        <div class="hero-eyebrow">
            <span class="hero-eyebrow-line"></span>
            <?= htmlspecialchars($feature['category'] ?? 'Discover') ?>
        </div>
        <h1><?= htmlspecialchars($heroTitle) ?></h1>
    </div>
    <div class="hero-scroll">
        <span class="hs-word">Scroll</span>
        <div class="hs-line"></div>
    </div>
</section>

<!-- TAB STRIP -->
<div class="tab-strip" id="tabStrip">
    <div class="tab-strip-inner" id="tabInner">
    </div>
</div>

<!-- PAGE CONTENT -->
<div class="page" id="pageContent">

<?php
if (!empty($sections)):
    $i = 0;
    $sliderCounter = 0;

    while ($i < count($sections)):
        $sec  = $sections[$i];
        $type = $sec['type'] ?? 'intro';

        if (in_array($type, ['intro', 'text-image'])):
            $group = [];
            $j = $i;
            while ($j < count($sections) && in_array($sections[$j]['type'] ?? '', ['intro','text-image'])):
                $group[] = $sections[$j];
                $j++;
            endwhile;

            $slId = 'csl' . $sliderCounter++;
            $cardStyle = $sec['card_style'] ?? 'overlay';
            $hasTitle = !empty($sec['section_title']) || !empty($sec['tab_label']);
            $sectionId = !empty($sec['tab_id']) ? ' id="' . htmlspecialchars($sec['tab_id']) . '"' : '';
?>
<!-- CARD SLIDER -->
<div class="card-slider rv"<?= $sectionId ?>>
    <?php if ($hasTitle || count($group) > 1): ?>
    <div class="cs-head">
        <h2 class="cs-head-title"><?= htmlspecialchars($sec['section_title'] ?? $sec['tab_label'] ?? '') ?></h2>
        <?php if (count($group) > 1): ?>
        <div class="cs-nav">
            <button class="cs-nav-btn" data-sl="<?= $slId ?>" data-dir="-1"><i class="fas fa-arrow-left"></i></button>
            <button class="cs-nav-btn" data-sl="<?= $slId ?>" data-dir="1"><i class="fas fa-arrow-right"></i></button>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    <div class="cs-rail">
        <div class="cs-track" id="<?= $slId ?>">
            <?php foreach ($group as $gc):
                // Auto: jika card_style tidak di-set tapi ada body → pakai history
                $cs = $gc['card_style'] ?? '';
                if (empty($cs) || $cs === 'overlay') {
                    $cs = !empty($gc['body']) ? 'history' : 'overlay';
                }
            ?>
            <?php if ($cs === 'history'): ?>
            <div class="cs-card-history">
                <?php if (!empty($gc['image'])): ?><img src="<?= htmlspecialchars($gc['image']) ?>" alt="<?= htmlspecialchars($gc['title'] ?? '') ?>" loading="lazy"><?php endif; ?>
                <div class="cs-card-history-body">
                    <div class="cs-card-history-text">
                        <?php if (!empty($gc['title'])): ?><div class="cs-card-history-title"><?= htmlspecialchars($gc['title']) ?></div><?php endif; ?>
                        <?php if (!empty($gc['body'])): ?><div class="cs-card-history-desc"><?= htmlspecialchars(mb_substr($gc['body'], 0, 200)) ?></div><?php endif; ?>
                    </div>
                </div>

            </div>
            <?php elseif ($cs === 'split'): ?>
            <div class="cs-card-split">
                <?php if (!empty($gc['image'])): ?>
                <div class="cs-card-split-img"><img src="<?= htmlspecialchars($gc['image']) ?>" alt="" loading="lazy"></div>
                <?php endif; ?>
                <div class="cs-card-split-body">
                    <?php if (!empty($gc['title'])): ?><div class="cs-card-split-title"><?= htmlspecialchars($gc['title']) ?></div><?php endif; ?>
                    <?php if (!empty($gc['body'])): ?><div class="cs-card-split-desc"><?= htmlspecialchars($gc['body']) ?></div><?php endif; ?>
                </div>
            </div>
            <?php else: /* overlay (default) */ ?>
            <div class="cs-card-overlay">
                <?php if (!empty($gc['image'])): ?><img src="<?= htmlspecialchars($gc['image']) ?>" alt="<?= htmlspecialchars($gc['title'] ?? '') ?>" loading="lazy"><?php endif; ?>
                <div class="cs-card-overlay-body">
                    <div class="cs-card-overlay-title"><?= htmlspecialchars($gc['title'] ?? '') ?></div>
                    <div class="cs-card-overlay-arrow"><i class="fas fa-arrow-right" style="font-size:10px"></i></div>
                </div>
            </div>
            <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
    <?php if (count($group) > 2): ?>
    <div class="cs-dots" id="<?= $slId ?>-dots">
        <?php $pages = ceil(count($group)/2); for ($p=0;$p<$pages;$p++): ?>
        <button class="cs-dot <?= $p===0?'on':'' ?>" data-sl="<?= $slId ?>" data-pg="<?= $p ?>"></button>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</div>
<div class="sec-divider"></div>
<?php
            $i = $j;

        elseif ($type === 'quote'):
            $sectionId = !empty($sec['tab_id']) ? ' id="'.htmlspecialchars($sec['tab_id']).'"' : '';
?>
<!-- QUOTE -->
<div class="quote-block rv"<?= $sectionId ?>>
    <div class="quote-block-text"><?= htmlspecialchars($sec['text'] ?? '') ?></div>
    <?php if (!empty($sec['author'])): ?><span class="quote-block-author"><?= htmlspecialchars($sec['author']) ?></span><?php endif; ?>
</div>
<div class="sec-divider"></div>
<?php
            $i++;

        elseif ($type === 'image-full'):
            $sectionId = !empty($sec['tab_id']) ? ' id="'.htmlspecialchars($sec['tab_id']).'"' : '';
?>
<!-- IMAGE FULL -->
<div class="img-full rv"<?= $sectionId ?>>
    <?php if (!empty($sec['image'])): ?><img src="<?= htmlspecialchars($sec['image']) ?>" alt="" loading="lazy"><?php endif; ?>
</div>
<?php if (!empty($sec['caption'])): ?><p class="img-full-caption"><?= htmlspecialchars($sec['caption']) ?></p><?php endif; ?>
<div class="sec-divider"></div>
<?php
            $i++;

        elseif ($type === 'centered-title'):
            $sectionId = !empty($sec['tab_id']) ? ' id="'.htmlspecialchars($sec['tab_id']).'"' : '';
?>
<!-- CENTERED TITLE -->
<div class="centered-title rv"<?= $sectionId ?>>
    <?php if (!empty($sec['title'])): ?><h2><?= htmlspecialchars($sec['title']) ?></h2><?php endif; ?>
    <?php if (!empty($sec['body'])): ?><p><?= htmlspecialchars($sec['body']) ?></p><?php endif; ?>
</div>
<div class="sec-divider"></div>
<?php
            $i++;

        elseif ($type === 'stat-grid'):
            $sectionId = !empty($sec['tab_id']) ? ' id="'.htmlspecialchars($sec['tab_id']).'"' : '';
?>
<!-- STAT GRID -->
<div class="stat-block rv"<?= $sectionId ?>>
    <?php if (!empty($sec['title'])): ?><h2 class="stat-block-title"><?= htmlspecialchars($sec['title']) ?></h2><?php endif; ?>
    <div class="stat-grid">
        <?php foreach ($sec['items'] ?? [] as $s): ?>
        <div class="stat-item">
            <div class="stat-val"><?= htmlspecialchars($s['val'] ?? '') ?></div>
            <div class="stat-lbl"><?= htmlspecialchars($s['lbl'] ?? '') ?></div>
            <?php if (!empty($s['desc'])): ?><div class="stat-desc"><?= htmlspecialchars($s['desc']) ?></div><?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<div class="sec-divider"></div>
<?php
            $i++;

        elseif ($type === 'gallery-layout'):
            $sectionId = !empty($sec['tab_id']) ? ' id="'.htmlspecialchars($sec['tab_id']).'"' : '';
?>
<!-- PHILOSOPHY / GALLERY STYLE -->
<div class="disc-gallery-block rv"<?= $sectionId ?>>
    <div class="disc-gallery-layout">
        <?php if (!empty($sec['image_top'])): ?>
        <div class="disc-gallery-img-top">
            <img src="<?= htmlspecialchars($sec['image_top']) ?>" alt="" loading="lazy">
        </div>
        <?php endif; ?>
        <div class="disc-gallery-content-row">
            <div class="disc-gallery-text">
                <?php if (!empty($sec['eyebrow'])): ?>
                <div style="font-size:10px;letter-spacing:.3em;text-transform:uppercase;color:#888;margin-bottom:16px;font-weight:500;"><?= htmlspecialchars($sec['eyebrow']) ?></div>
                <?php endif; ?>
                <?php if (!empty($sec['title'])): ?><h2><?= htmlspecialchars($sec['title']) ?></h2><?php endif; ?>
                <?php if (!empty($sec['body'])): ?><p><?= nl2br(htmlspecialchars($sec['body'])) ?></p><?php endif; ?>
            </div>
            <?php if (!empty($sec['image_right'])): ?>
            <div class="disc-gallery-img-right">
                <img src="<?= htmlspecialchars($sec['image_right']) ?>" alt="" loading="lazy">
            </div>
            <?php endif; ?>
        </div>
        <?php if (!empty($sec['image_bottom'])): ?>
        <div class="disc-gallery-img-bottom">
            <img src="<?= htmlspecialchars($sec['image_bottom']) ?>" alt="" loading="lazy">
        </div>
        <?php endif; ?>
    </div>
</div>
<div class="sec-divider"></div>
<?php
            $i++;

        elseif ($type === 'two-col'):
            $sectionId = !empty($sec['tab_id']) ? ' id="'.htmlspecialchars($sec['tab_id']).'"' : '';
?>
<!-- TWO COL TEXT -->
<div class="twocol-block rv"<?= $sectionId ?>>
    <div class="twocol-col">
        <?php if (!empty($sec['left_title'])): ?><h3><?= htmlspecialchars($sec['left_title']) ?></h3><?php endif; ?>
        <?php if (!empty($sec['left_body'])): ?><p><?= nl2br(htmlspecialchars($sec['left_body'])) ?></p><?php endif; ?>
    </div>
    <div class="twocol-col">
        <?php if (!empty($sec['right_title'])): ?><h3><?= htmlspecialchars($sec['right_title']) ?></h3><?php endif; ?>
        <?php if (!empty($sec['right_body'])): ?><p><?= nl2br(htmlspecialchars($sec['right_body'])) ?></p><?php endif; ?>
    </div>
</div>
<div class="sec-divider"></div>
<?php
            $i++;

        else:
            $i++;
        endif;
    endwhile;

elseif (!empty($feature['description'])):
?>
<!-- FALLBACK -->
<div class="split-block rv">
    <div class="split-text">
        <div class="split-eyebrow"><?= htmlspecialchars($feature['category'] ?? 'Discover') ?></div>
        <h2><?= htmlspecialchars($heroTitle) ?></h2>
        <p><?= nl2br(htmlspecialchars($feature['description'])) ?></p>
    </div>
    <?php if (!empty($feature['image'])): ?>
    <div class="split-img-wrap"><img src="<?= htmlspecialchars($feature['image']) ?>" alt="" loading="lazy"></div>
    <?php endif; ?>
</div>
<?php endif; ?>

</div><!-- /page -->

<!-- PREV / NEXT -->
<?php if ($prevFeature || $nextFeature): ?>
<div class="pn">
    <?php if ($prevFeature): ?>
    <a href="<?= fUrl($prevFeature) ?>" class="pn-btn">
        <div class="pn-bg" style="background-image:url('<?= htmlspecialchars($prevFeature['image']??'') ?>')"></div>
        <div class="pn-body">
            <div class="pn-dir"><i class="fas fa-arrow-left" style="font-size:9px"></i> Sebelumnya</div>
            <div class="pn-title"><?= htmlspecialchars($prevFeature['title']??'') ?></div>
        </div>
    </a>
    <?php else: ?><div style="border-right:1px solid #e8e8e8"></div><?php endif; ?>
    <?php if ($nextFeature): ?>
    <a href="<?= fUrl($nextFeature) ?>" class="pn-btn" style="text-align:right">
        <div class="pn-bg" style="background-image:url('<?= htmlspecialchars($nextFeature['image']??'') ?>')"></div>
        <div class="pn-body">
            <div class="pn-dir" style="justify-content:flex-end">Selanjutnya <i class="fas fa-arrow-right" style="font-size:9px"></i></div>
            <div class="pn-title"><?= htmlspecialchars($nextFeature['title']??'') ?></div>
        </div>
    </a>
    <?php else: ?><div></div><?php endif; ?>
</div>
<?php endif; ?>

<?php else: ?>
<!-- LISTING MODE -->
<div class="listing-page">
    <h1>Discover</h1>
    <p class="listing-sub">Eksplorasi dunia Porsche.</p>
    <div class="listing-grid">
        <?php foreach ($allFeatures ?? [] as $f): ?>
        <a href="<?= fUrl($f) ?>" class="lst-card">
            <img src="<?= htmlspecialchars($f['image']??'') ?>" alt="<?= htmlspecialchars($f['title']??'') ?>" loading="lazy">
            <div class="lst-body">
                <div class="lst-cat"><?= htmlspecialchars($f['category']??'Discover') ?></div>
                <div class="lst-title"><?= htmlspecialchars($f['title']??'') ?></div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<script>
(function(){
'use strict';

/* ── TOPBAR transparent over hero ── */
const bar = document.getElementById('topbar');
function updateBar(){
    const heroH = document.querySelector('.hero')?.offsetHeight || 400;
    bar.classList.toggle('over-hero', window.scrollY < heroH - 70);
}
window.addEventListener('scroll', updateBar, {passive:true});
updateBar();

/* ── HERO BG parallax ── */
const heroBg = document.getElementById('heroBg');
if(heroBg){
    const tmp = new Image();
    tmp.onload = () => heroBg.classList.add('loaded');
    tmp.src = heroBg.style.backgroundImage.replace(/url\(['"]?(.*?)['"]?\)/,'$1');
    const hero = document.querySelector('.hero');
    let raf = false;
    function doParallax(){
        if(!hero) return;
        const heroH = hero.offsetHeight;
        const sy = window.scrollY;
        if(sy > heroH) return;
        // Parallax halus: scale sedikit + geser ke atas max ~8% hero height
        const shift = sy * 0.08;
        heroBg.style.transform = `scale(1.08) translateY(${shift}px)`;
    }
    window.addEventListener('scroll',()=>{
        if(raf) return; raf=true;
        requestAnimationFrame(()=>{ doParallax(); raf=false; });
    },{passive:true});
    doParallax();
}

/* ── BUILD TAB STRIP ── */
(function buildTabs(){
    const tabInner = document.getElementById('tabInner');
    if(!tabInner) return;

    const tabData = <?= json_encode(array_values(array_unique(array_merge(
        array_filter(array_map(function($s){
            if(!empty($s['tab_id']) && !empty($s['tab_label'])) return ['id'=>$s['tab_id'],'label'=>$s['tab_label']];
            return null;
        }, $sections ?? [])),
        array_filter(array_map(function($p){
            if(!empty($p['tab_id']) && !empty($p['tab_label'])) return ['id'=>$p['tab_id'],'label'=>$p['tab_label']];
            return null;
        }, $gallerySections ?? []))
    ), SORT_REGULAR))) ?>;

    tabData.forEach(t => {
        const btn = document.createElement('button');
        btn.className = 'tab-btn';
        btn.textContent = t.label;
        btn.onclick = () => {
            const el = document.getElementById(t.id);
            if(el){
                const offset = 56 + 50;
                const y = el.getBoundingClientRect().top + window.scrollY - offset;
                window.scrollTo({top: y, behavior:'smooth'});
            }
        };
        tabInner.appendChild(btn);
    });

    if(tabData.length){
        function updateActive(){
            const offset = 56+50+40;
            let active = null;
            tabData.forEach(t => {
                const el = document.getElementById(t.id);
                if(el && el.getBoundingClientRect().top <= offset) active = t.id;
            });
            tabInner.querySelectorAll('.tab-btn').forEach((btn,i) => {
                btn.classList.toggle('active', active === tabData[i]?.id);
            });
        }
        window.addEventListener('scroll', updateActive, {passive:true});
    }
})();

/* ── SLIDER ENGINE ── */
const sliders = {};

function initSlider(id){
    if(sliders[id]) return sliders[id];
    const track = document.getElementById(id);
    if(!track) return null;
    sliders[id] = { track, offset:0 };
    return sliders[id];
}

function getCardWidth(track){
    const card = track.querySelector('[class^="cs-card"]');
    if(!card) return 0;
    return card.offsetWidth + parseInt(getComputedStyle(track).gap||'16');
}

function goTo(id, offset){
    const s = initSlider(id);
    if(!s) return;
    const cards = s.track.querySelectorAll('[class^="cs-card"]');
    if(!cards.length) return;
    const cw = getCardWidth(s.track);
    const railW = s.track.parentElement.offsetWidth;
    const maxOffset = Math.max(0, cw * cards.length - railW + 72*2);
    offset = Math.max(0, Math.min(offset, maxOffset));
    s.offset = offset;
    s.track.style.transform = `translateX(-${offset}px)`;
    const dotsEl = document.getElementById(id+'-dots');
    if(dotsEl){
        const pg = Math.round(offset / (cw * Math.max(1, Math.floor(railW/cw))));
        dotsEl.querySelectorAll('.cs-dot').forEach((d,i)=>d.classList.toggle('on',i===pg));
    }
}

document.querySelectorAll('.cs-nav-btn').forEach(btn=>{
    btn.addEventListener('click',()=>{
        const id = btn.dataset.sl;
        const s = initSlider(id);
        if(!s) return;
        const cw = getCardWidth(s.track);
        goTo(id, s.offset + parseInt(btn.dataset.dir) * cw);
    });
});

document.querySelectorAll('.cs-dot').forEach(dot=>{
    dot.addEventListener('click',()=>{
        const id = dot.dataset.sl;
        const s = initSlider(id);
        if(!s) return;
        const cards = s.track.querySelectorAll('[class^="cs-card"]');
        const cw = getCardWidth(s.track);
        const railW = s.track.parentElement.offsetWidth;
        const perPage = Math.max(1, Math.floor(railW/cw));
        goTo(id, parseInt(dot.dataset.pg) * perPage * cw);
    });
});

/* ── REVEAL ── */
const ro = new IntersectionObserver(entries=>{
    entries.forEach(e=>{
        if(e.isIntersecting){ e.target.classList.add('in'); ro.unobserve(e.target); }
    });
},{threshold:.06, rootMargin:'0px 0px -30px 0px'});
document.querySelectorAll('.rv').forEach(el=>ro.observe(el));

})();
</script>
</body>
</html>