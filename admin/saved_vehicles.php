<?php
/**
 * Admin - Saved Vehicles Management
 */
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../app/models/SavedVehicle.php';

$savedVehicleModel = new SavedVehicle();

$statistics  = $savedVehicleModel->getAdminStatistics();
$recentSaves = $savedVehicleModel->getRecentSaves(50);

$stmt = Database::getInstance()->getConnection()->query("
    SELECT
        COUNT(*) as total_saves,
        COUNT(DISTINCT session_id) as unique_users,
        COUNT(DISTINCT vehicle_id) as unique_vehicles
    FROM saved_vehicles
");
$totals = $stmt->fetch(PDO::FETCH_ASSOC);

$recentCount = count(array_filter($recentSaves, function($save) {
    return strtotime($save['saved_at']) > strtotime('-7 days');
}));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saved Vehicles Analytics — Porsche Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /*
 * ============================================================
 * SAVED VEHICLES ADMIN — LIGHT GLASSMORPHISM THEME PATCH
 * Ganti seluruh blok <style> di saved_vehicles.php dengan CSS ini
 * ============================================================
 */

:root {
    --bg:  #dcdce8;
    --bg2: rgba(255,255,255,0.60);
    --bg3: rgba(255,255,255,0.40);
    --bg4: rgba(255,255,255,0.28);
    --bg5: rgba(255,255,255,0.80);
    --b1: rgba(0,0,0,0.04);
    --b2: rgba(0,0,0,0.09);
    --b3: rgba(0,0,0,0.16);
    --b4: rgba(0,0,0,0.28);
    --t1: #12121f;
    --t2: #4b4b6a;
    --t3: #9090b0;
    --t4: #b8b8d0;
    --gold:  #18181e;
    --gold2: #3a3a4a;
    --gold3: rgba(0,0,0,0.06);
    --green: #00b894;
    --red:   #e17055;
    --blue:  #0984e3;
    --amber: #fdcb6e;
    --r1: 8px; --r2: 12px; --r3: 16px; --r4: 100px;
}

*, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
html { scroll-behavior: smooth; }

body {
    font-family: 'DM Sans', sans-serif;
    background:
        radial-gradient(ellipse at 15% 20%, rgba(200,200,230,0.55) 0%, transparent 55%),
        radial-gradient(ellipse at 85% 75%, rgba(210,205,235,0.50) 0%, transparent 55%),
        radial-gradient(ellipse at 50% 50%, rgba(230,228,240,0.40) 0%, transparent 70%),
        #d8d8e6;
    color: var(--t1);
    min-height: 100vh;
    font-size: 14px; line-height: 1.6;
    -webkit-font-smoothing: antialiased;
}

body::before { display: none; }

::-webkit-scrollbar { width: 4px; height: 4px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: var(--b3); border-radius: 4px; }

/* ── TOPBAR ── */
.topbar {
    position: sticky; top: 0; z-index: 300;
    height: 62px; padding: 0 36px;
    display: flex; align-items: center; justify-content: space-between;
    background: rgba(255,255,255,0.72);
    backdrop-filter: blur(28px) saturate(180%);
    border-bottom: 1px solid var(--b2);
    box-shadow: 0 1px 0 rgba(255,255,255,0.9) inset, 0 2px 12px rgba(0,0,0,0.06);
}
.topbar::after {
    content: ''; position: absolute;
    bottom: -1px; left: 0; right: 0; height: 1px;
    background: linear-gradient(90deg, transparent 5%, rgba(0,0,0,0.10) 35%, rgba(0,0,0,0.10) 65%, transparent 95%);
}

.brand { display: flex; align-items: center; gap: 12px; text-decoration: none; }
.brand-mark {
    width: 32px; height: 32px;
    background: linear-gradient(140deg, #18181e, #3a3a4a);
    border: none; border-radius: var(--r2);
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; color: #fff;
    box-shadow: 0 4px 12px rgba(0,0,0,0.22);
}
.brand-mark::before { display: none; }
.brand-name { font-family: 'Syne', sans-serif; font-size: 0.95rem; font-weight: 800; letter-spacing: 0.1em; text-transform: uppercase; color: var(--t1); }
.brand-div  { width: 1px; height: 18px; background: var(--b3); }
.brand-sub  { font-size: 0.68rem; color: var(--t3); letter-spacing: 0.07em; text-transform: uppercase; }

.subnav-item { display: flex; align-items: center; gap: 7px; font-size: 0.74rem; font-weight: 500; color: var(--t2); }
.subnav-item i { font-size: 11px; color: var(--t1); }
.subnav-item span { font-family: 'Syne', sans-serif; font-size: 0.72rem; font-weight: 700; color: var(--t1); }

.back-btn {
    display: flex; align-items: center; gap: 6px;
    padding: 6px 14px; border-radius: var(--r4);
    font-size: 0.73rem; font-weight: 500;
    border: 1px solid var(--b2); color: var(--t2);
    text-decoration: none; transition: all 0.18s;
    background: rgba(255,255,255,0.60);
    backdrop-filter: blur(8px);
}
.back-btn:hover { border-color: var(--b3); color: var(--t1); background: rgba(255,255,255,0.85); }

/* ── WRAP ── */
.wrap { max-width: 1560px; margin: 0 auto; padding: 30px 36px 80px; position: relative; z-index: 1; }

/* ── STAT CARDS ── */
.stats { display: grid; grid-template-columns: repeat(4,1fr); gap: 14px; margin-bottom: 30px; }

.scard {
    background: rgba(255,255,255,0.62);
    backdrop-filter: blur(16px);
    border: 1px solid rgba(255,255,255,0.85);
    border-radius: var(--r3); padding: 20px 22px;
    display: flex; align-items: flex-start; gap: 14px;
    transition: border-color 0.2s, transform 0.15s, box-shadow 0.2s;
    position: relative; overflow: hidden;
    box-shadow: 0 2px 0 rgba(255,255,255,0.9) inset, 0 8px 32px rgba(0,0,0,0.07);
}
.scard::after {
    content: ''; position: absolute;
    top: 0; left: 0; right: 0; height: 1px;
    background: rgba(255,255,255,0.90);
}
.scard:hover {
    border-color: rgba(255,255,255,1);
    transform: translateY(-2px);
    box-shadow: 0 2px 0 rgba(255,255,255,0.9) inset, 0 14px 40px rgba(0,0,0,0.10);
}

.sc-ico { width: 38px; height: 38px; border-radius: var(--r2); display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; }
.ic-bk { background: rgba(0,0,0,0.06); border: 1px solid var(--b2); color: var(--t2); }
.ic-gl { background: rgba(0,0,0,0.07); border: 1px solid rgba(0,0,0,0.10); color: var(--t1); }
.ic-gr { background: rgba(0,184,148,0.10); border: 1px solid rgba(0,184,148,0.20); color: var(--green); }
.ic-bl { background: rgba(9,132,227,0.10); border: 1px solid rgba(9,132,227,0.20); color: var(--blue); }

.sc-body label { display: block; font-family: 'Syne', sans-serif; font-size: 0.6rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: var(--t3); margin-bottom: 4px; }
.sc-body strong { font-family: 'Syne', sans-serif; font-size: 2rem; font-weight: 800; line-height: 1; color: var(--t1); letter-spacing: -0.04em; display: block; }
.sc-body span { font-size: 0.7rem; color: var(--t3); margin-top: 4px; display: block; }

/* ── SECTION HEADER ── */
.sec-hd { display: flex; align-items: baseline; gap: 10px; margin-bottom: 14px; padding-bottom: 12px; border-bottom: 1px solid var(--b2); }
.sec-hd h2 { font-family: 'Syne', sans-serif; font-size: 0.95rem; font-weight: 700; color: var(--t1); }
.sec-cnt { font-size: 0.68rem; color: var(--t3); background: rgba(255,255,255,0.60); border: 1px solid var(--b2); padding: 2px 8px; border-radius: var(--r4); }

/* ── TABLE ── */
.tbox {
    background: rgba(255,255,255,0.60);
    backdrop-filter: blur(16px);
    border: 1px solid rgba(255,255,255,0.85);
    border-radius: var(--r3); overflow: hidden; margin-bottom: 26px;
    box-shadow: 0 2px 0 rgba(255,255,255,0.9) inset, 0 8px 32px rgba(0,0,0,0.07);
}

table { width: 100%; border-collapse: collapse; }
thead th { padding: 11px 14px; text-align: left; font-family: 'Syne', sans-serif; font-size: 0.58rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: var(--t3); background: rgba(255,255,255,0.40); border-bottom: 1px solid var(--b2); white-space: nowrap; }
tbody td { padding: 13px 14px; border-bottom: 1px solid var(--b1); vertical-align: middle; font-size: 0.83rem; color: var(--t1); }
tbody tr:last-child td { border-bottom: none; }
tbody tr { transition: background 0.1s; }
tbody tr:hover td { background: rgba(255,255,255,0.30); }

/* ── RANK BADGE ── */
.rank {
    display: inline-flex; align-items: center; justify-content: center;
    width: 28px; height: 28px; border-radius: var(--r2);
    font-family: 'Syne', sans-serif; font-size: 0.72rem; font-weight: 700;
    background: rgba(255,255,255,0.55); border: 1px solid var(--b2); color: var(--t3);
}
.rank.gold   { background: rgba(212,175,55,0.12);  border-color: rgba(212,175,55,0.30);  color: #9a7c1a; }
.rank.silver { background: rgba(160,168,180,0.12); border-color: rgba(160,168,180,0.28); color: #6a7280; }
.rank.bronze { background: rgba(175,110,70,0.10);  border-color: rgba(175,110,70,0.24);  color: #8a5030; }

/* ── BADGES ── */
.save-badge { display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: var(--r4); font-size: 0.7rem; font-weight: 700; background: rgba(0,184,148,0.08); color: var(--green); border: 1px solid rgba(0,184,148,0.20); }
.users-badge { display: inline-flex; align-items: center; gap: 5px; font-size: 0.78rem; color: var(--t2); }

/* ── TIMESTAMP ── */
.ts-main { font-size: 0.79rem; color: var(--t2); }
.ts-sub  { font-size: 0.73rem; color: var(--t3); margin-top: 1px; }

/* ── VEHICLE CELLS ── */
.vname { font-weight: 600; font-size: 0.85rem; color: var(--t1); }
.vmeta { font-size: 0.74rem; color: var(--t3); margin-top: 2px; }
.price-val { font-family: 'Syne', sans-serif; font-size: 0.83rem; font-weight: 700; color: var(--t1); }
.sess-id {
    font-family: 'Courier New', monospace; font-size: 0.76rem; color: var(--t3);
    background: rgba(255,255,255,0.55); padding: 3px 8px; border-radius: 5px;
    border: 1px solid var(--b2); backdrop-filter: blur(4px);
}

/* ── INSIGHTS ── */
.insights {
    background: rgba(255,255,255,0.60);
    backdrop-filter: blur(16px);
    border: 1px solid rgba(255,255,255,0.85);
    border-radius: var(--r3); overflow: hidden;
    box-shadow: 0 2px 0 rgba(255,255,255,0.9) inset, 0 8px 32px rgba(0,0,0,0.07);
}
.insights-hd {
    display: flex; align-items: center; gap: 10px;
    padding: 16px 20px;
    background: rgba(255,255,255,0.45);
    border-bottom: 1px solid var(--b1);
}
.ins-ico { width: 28px; height: 28px; border-radius: var(--r2); display: flex; align-items: center; justify-content: center; font-size: 11px; background: rgba(0,0,0,0.07); border: 1px solid rgba(0,0,0,0.10); color: var(--t1); }
.insights-hd h2 { font-family: 'Syne', sans-serif; font-size: 0.82rem; font-weight: 700; color: var(--t1); }

.insights-body { display: grid; grid-template-columns: repeat(3,1fr); }

.insight-item {
    padding: 20px 22px;
    border-right: 1px solid var(--b1);
    transition: background 0.15s;
}
.insight-item:last-child { border-right: none; }
.insight-item:hover { background: rgba(255,255,255,0.25); }
.insight-item label { font-family: 'Syne', sans-serif; display: block; font-size: 0.58rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: var(--t3); margin-bottom: 8px; }
.insight-item p { font-size: 0.82rem; color: var(--t2); line-height: 1.6; }
.insight-item p strong { color: var(--t1); font-weight: 600; }

/* ── EMPTY ── */
.empty { text-align: center; padding: 56px 20px; color: var(--t4); }
.empty i { font-size: 1.8rem; display: block; margin-bottom: 12px; opacity: 0.25; }
.empty p { font-size: 0.82rem; }

@media (max-width: 900px) {
    .stats { grid-template-columns: repeat(2,1fr); }
    .insights-body { grid-template-columns: 1fr; }
    .wrap { padding: 18px 14px 60px; }
}
    </style>
</head>
<body>

<header class="topbar">
    <div style="display:flex; align-items:center; gap:16px;">
        <a class="brand" href="/lending_word/admin/">
            <div class="brand-mark"><i class="fas fa-shield-halved"></i></div>
            <span class="brand-name">Porsche</span>
            <div class="brand-div"></div>
            <span class="brand-sub">Admin</span>
        </a>
        <div class="subnav-item">
            <i class="fas fa-chevron-right" style="font-size:9px; color:var(--t4);"></i>
            <i class="fas fa-bookmark"></i>
            <span>Saved Vehicles Analytics</span>
        </div>
    </div>
    <a href="/lending_word/admin/" class="back-btn"><i class="fas fa-arrow-left"></i> Dashboard</a>
</header>

<div class="wrap">

    <!-- ── STATS ── -->
    <div class="stats">
        <div class="scard">
            <div class="sc-ico ic-bk"><i class="fas fa-bookmark"></i></div>
            <div class="sc-body"><label>Total Saves</label><strong><?= number_format($totals['total_saves']) ?></strong><span>All time</span></div>
        </div>
        <div class="scard">
            <div class="sc-ico ic-bl"><i class="fas fa-users"></i></div>
            <div class="sc-body"><label>Unique Users</label><strong><?= number_format($totals['unique_users']) ?></strong><span>With saved vehicles</span></div>
        </div>
        <div class="scard">
            <div class="sc-ico ic-gr"><i class="fas fa-car"></i></div>
            <div class="sc-body"><label>Popular Vehicles</label><strong><?= number_format($totals['unique_vehicles']) ?></strong><span>Have been saved</span></div>
        </div>
        <div class="scard">
            <div class="sc-ico ic-gl"><i class="fas fa-chart-simple"></i></div>
            <div class="sc-body"><label>Avg per User</label><strong><?= $totals['unique_users'] > 0 ? number_format($totals['total_saves'] / $totals['unique_users'], 1) : '0' ?></strong><span>Vehicles saved</span></div>
        </div>
    </div>

    <!-- ── MOST SAVED ── -->
    <div class="sec-hd">
        <h2>Most Saved Vehicles</h2>
        <span class="sec-cnt"><?= count($statistics) ?> vehicle<?= count($statistics) !== 1 ? 's' : '' ?></span>
    </div>

    <div class="tbox">
        <?php if (empty($statistics)): ?>
        <div class="empty"><i class="far fa-bookmark"></i><p>No vehicles have been saved yet</p></div>
        <?php else: ?>
        <table>
            <thead><tr><th>Rank</th><th>Vehicle</th><th>Save Count</th><th>Unique Users</th><th>Last Saved</th></tr></thead>
            <tbody>
                <?php $rank = 1; foreach ($statistics as $stat):
                    $cls = $rank === 1 ? 'gold' : ($rank === 2 ? 'silver' : ($rank === 3 ? 'bronze' : ''));
                ?>
                <tr>
                    <td><span class="rank <?= $cls ?>"><?= $rank++ ?></span></td>
                    <td><div class="vname"><?= htmlspecialchars($stat['title']) ?></div></td>
                    <td><span class="save-badge"><i class="fas fa-bookmark" style="font-size:9px;"></i><?= $stat['save_count'] ?> saves</span></td>
                    <td><span class="users-badge"><i class="fas fa-user" style="font-size:10px; color:var(--t3);"></i><?= $stat['unique_savers'] ?> users</span></td>
                    <td>
                        <?php if ($stat['last_saved_at']): ?>
                        <div class="ts-main"><?= date('d M Y', strtotime($stat['last_saved_at'])) ?></div>
                        <div class="ts-sub"><?= date('H:i', strtotime($stat['last_saved_at'])) ?></div>
                        <?php else: ?><span style="color:var(--t4);">—</span><?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <!-- ── RECENT ACTIVITY ── -->
    <div class="sec-hd">
        <h2>Recent Save Activity</h2>
        <span class="sec-cnt">Last 50 saves</span>
    </div>

    <div class="tbox">
        <?php if (empty($recentSaves)): ?>
        <div class="empty"><i class="far fa-clock"></i><p>No recent activity</p></div>
        <?php else: ?>
        <table>
            <thead><tr><th>Timestamp</th><th>Vehicle</th><th>Session ID</th><th>Saved Price</th></tr></thead>
            <tbody>
                <?php foreach ($recentSaves as $save): ?>
                <tr>
                    <td>
                        <div class="ts-main"><?= date('d M Y', strtotime($save['saved_at'])) ?></div>
                        <div class="ts-sub"><?= date('H:i:s', strtotime($save['saved_at'])) ?></div>
                    </td>
                    <td>
                        <div class="vname"><?= htmlspecialchars($save['vehicle_title']) ?></div>
                        <div class="vmeta"><?= htmlspecialchars($save['condition']) ?> &bull; <?= $save['model_year'] ?></div>
                    </td>
                    <td><span class="sess-id"><?= substr($save['session_id'], 0, 16) ?>…</span></td>
                    <td>
                        <?php if ($save['saved_price']): ?>
                        <span class="price-val">Rp <?= number_format($save['saved_price'], 0, ',', '.') ?></span>
                        <?php else: ?><span style="color:var(--t4);">—</span><?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <!-- ── INSIGHTS ── -->
    <div class="insights">
        <div class="insights-hd">
            <div class="ins-ico"><i class="fas fa-lightbulb"></i></div>
            <h2>Insights</h2>
        </div>
        <div class="insights-body">
            <?php if (!empty($statistics)): ?>
            <div class="insight-item">
                <label>Top Vehicle</label>
                <p><strong>"<?= htmlspecialchars($statistics[0]['title']) ?>"</strong> has been saved <strong><?= $statistics[0]['save_count'] ?> times</strong> by <strong><?= $statistics[0]['unique_savers'] ?></strong> different users.</p>
            </div>
            <div class="insight-item">
                <label>Recent Activity</label>
                <p><strong><?= $recentCount ?></strong> vehicles were saved in the <strong>last 7 days</strong>.</p>
            </div>
            <div class="insight-item">
                <label>User Engagement</label>
                <p>On average, users save <strong><?= $totals['unique_users'] > 0 ? round($totals['total_saves'] / $totals['unique_users'], 1) : '0' ?></strong> vehicles to compare and review later.</p>
            </div>
            <?php else: ?>
            <div class="insight-item" style="grid-column:1/-1;">
                <label>No Data</label>
                <p>No data available yet. Start promoting the saved vehicles feature to users!</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

</div>
</body>
</html>