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
        :root {
            --bg:   #05050a; --bg2: #09090f; --bg3: #0e0e16; --bg4: #13131c; --bg5: #181825;
            --b1: rgba(255,255,255,0.035); --b2: rgba(255,255,255,0.075);
            --b3: rgba(255,255,255,0.13);  --b4: rgba(255,255,255,0.2);
            --t1: #eeeef4; --t2: #777790; --t3: #363648; --t4: #1e1e2c;
            --gold: #c9a84c; --gold2: #e8c97a; --gold3: rgba(201,168,76,0.14);
            --green: #2dd4a0; --red: #ef6060; --blue: #5b9cf6; --amber: #f0b429;
            --r1: 6px; --r2: 10px; --r3: 14px; --r4: 100px;
        }

        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg); color: var(--t1);
            min-height: 100vh; font-size: 14px; line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        body::before {
            content: ''; position: fixed;
            top: -280px; left: 50%; transform: translateX(-50%);
            width: 1000px; height: 500px;
            background: radial-gradient(ellipse, rgba(201,168,76,0.032) 0%, transparent 65%);
            pointer-events: none; z-index: 0;
        }

        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--b3); border-radius: 4px; }

        /* ── TOPBAR ── */
        .topbar {
            position: sticky; top: 0; z-index: 300;
            height: 62px; padding: 0 36px;
            display: flex; align-items: center; justify-content: space-between;
            background: rgba(5,5,10,0.84);
            backdrop-filter: blur(28px) saturate(180%);
            border-bottom: 1px solid var(--b2);
        }
        .topbar::after {
            content: ''; position: absolute;
            bottom: -1px; left: 0; right: 0; height: 1px;
            background: linear-gradient(90deg, transparent 5%, rgba(201,168,76,0.28) 35%, rgba(201,168,76,0.28) 65%, transparent 95%);
        }

        .brand { display: flex; align-items: center; gap: 12px; text-decoration: none; }
        .brand-mark {
            width: 32px; height: 32px;
            background: linear-gradient(140deg, var(--bg4), var(--bg5));
            border: 1px solid var(--b3); border-radius: var(--r2);
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; color: var(--gold);
            position: relative; overflow: hidden;
            box-shadow: 0 0 24px rgba(201,168,76,0.07);
        }
        .brand-mark::before { content: ''; position: absolute; inset: 0; background: linear-gradient(140deg, rgba(201,168,76,0.1), transparent); }
        .brand-name { font-family: 'Syne', sans-serif; font-size: 0.95rem; font-weight: 800; letter-spacing: 0.1em; text-transform: uppercase; color: var(--t1); }
        .brand-div  { width: 1px; height: 18px; background: var(--b3); }
        .brand-sub  { font-size: 0.68rem; color: var(--t4); letter-spacing: 0.07em; text-transform: uppercase; }

        .subnav-item { display: flex; align-items: center; gap: 7px; font-size: 0.74rem; font-weight: 500; color: var(--t2); }
        .subnav-item i { font-size: 11px; color: var(--gold); }
        .subnav-item span { font-family: 'Syne', sans-serif; font-size: 0.72rem; font-weight: 700; color: var(--t1); }

        .back-btn {
            display: flex; align-items: center; gap: 6px;
            padding: 6px 14px; border-radius: var(--r4);
            font-size: 0.73rem; font-weight: 500;
            border: 1px solid var(--b2); color: var(--t2);
            text-decoration: none; transition: all 0.18s; background: var(--bg3);
        }
        .back-btn:hover { border-color: var(--b3); color: var(--t1); background: var(--bg4); }

        /* ── WRAP ── */
        .wrap { max-width: 1560px; margin: 0 auto; padding: 30px 36px 80px; position: relative; z-index: 1; }

        /* ── STAT CARDS ── */
        .stats { display: grid; grid-template-columns: repeat(4,1fr); gap: 14px; margin-bottom: 30px; }

        .scard {
            background: var(--bg2); border: 1px solid var(--b2);
            border-radius: var(--r3); padding: 20px 22px;
            display: flex; align-items: flex-start; gap: 14px;
            transition: border-color 0.2s, transform 0.15s;
            position: relative; overflow: hidden;
            box-shadow: 0 1px 0 rgba(255,255,255,0.03) inset, 0 16px 48px rgba(0,0,0,0.42);
        }
        .scard::after { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 1px; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.06), transparent); }
        .scard:hover { border-color: var(--b3); transform: translateY(-2px); }

        .sc-ico { width: 38px; height: 38px; border-radius: var(--r2); display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; }
        .ic-bk { background: rgba(255,255,255,0.05); border: 1px solid var(--b2); color: var(--t2); }
        .ic-gl { background: var(--gold3); border: 1px solid rgba(201,168,76,0.2); color: var(--gold); }
        .ic-gr { background: rgba(45,212,160,0.1); border: 1px solid rgba(45,212,160,0.2); color: var(--green); }
        .ic-bl { background: rgba(91,156,246,0.1); border: 1px solid rgba(91,156,246,0.2); color: var(--blue); }

        .sc-body label { display: block; font-family: 'Syne', sans-serif; font-size: 0.6rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: var(--t4); margin-bottom: 4px; }
        .sc-body strong { font-family: 'Syne', sans-serif; font-size: 2rem; font-weight: 800; line-height: 1; color: var(--t1); letter-spacing: -0.04em; display: block; }
        .sc-body span { font-size: 0.7rem; color: var(--t4); margin-top: 4px; display: block; }

        /* ── SECTION HEADER ── */
        .sec-hd { display: flex; align-items: baseline; gap: 10px; margin-bottom: 14px; padding-bottom: 12px; border-bottom: 1px solid var(--b1); }
        .sec-hd h2 { font-family: 'Syne', sans-serif; font-size: 0.95rem; font-weight: 700; color: var(--t1); }
        .sec-cnt { font-size: 0.68rem; color: var(--t4); background: var(--bg3); border: 1px solid var(--b2); padding: 2px 8px; border-radius: var(--r4); }

        /* ── TABLE ── */
        .tbox { background: var(--bg2); border: 1px solid var(--b2); border-radius: var(--r3); overflow: hidden; margin-bottom: 26px; box-shadow: 0 1px 0 rgba(255,255,255,0.025) inset, 0 16px 48px rgba(0,0,0,0.4); }

        table { width: 100%; border-collapse: collapse; }
        thead th { padding: 11px 14px; text-align: left; font-family: 'Syne', sans-serif; font-size: 0.58rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: var(--t4); background: linear-gradient(180deg, var(--bg3), var(--bg2)); border-bottom: 1px solid var(--b1); white-space: nowrap; }
        tbody td { padding: 13px 14px; border-bottom: 1px solid var(--b1); vertical-align: middle; font-size: 0.83rem; color: var(--t1); }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr { transition: background 0.1s; }
        tbody tr:hover td { background: rgba(255,255,255,0.012); }

        /* ── RANK BADGE ── */
        .rank {
            display: inline-flex; align-items: center; justify-content: center;
            width: 28px; height: 28px; border-radius: var(--r2);
            font-family: 'Syne', sans-serif; font-size: 0.72rem; font-weight: 700;
            background: var(--bg4); border: 1px solid var(--b2); color: var(--t3);
        }
        .rank.gold   { background: rgba(201,168,76,0.12); border-color: rgba(201,168,76,0.28); color: var(--gold); }
        .rank.silver { background: rgba(180,188,200,0.1); border-color: rgba(180,188,200,0.22); color: #aab4c4; }
        .rank.bronze { background: rgba(175,110,70,0.1);  border-color: rgba(175,110,70,0.22);  color: #c4845a; }

        /* ── BADGES ── */
        .save-badge { display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: var(--r4); font-size: 0.7rem; font-weight: 700; background: rgba(45,212,160,0.08); color: var(--green); border: 1px solid rgba(45,212,160,0.18); }
        .users-badge { display: inline-flex; align-items: center; gap: 5px; font-size: 0.78rem; color: var(--t2); }

        /* ── TIMESTAMP ── */
        .ts-main { font-size: 0.79rem; color: var(--t2); }
        .ts-sub  { font-size: 0.73rem; color: var(--t4); margin-top: 1px; }

        /* ── VEHICLE CELLS ── */
        .vname { font-weight: 600; font-size: 0.85rem; color: var(--t1); }
        .vmeta { font-size: 0.74rem; color: var(--t3); margin-top: 2px; }
        .price-val { font-family: 'Syne', sans-serif; font-size: 0.83rem; font-weight: 700; color: var(--t1); }
        .sess-id { font-family: 'Courier New', monospace; font-size: 0.76rem; color: var(--t3); background: var(--bg4); padding: 3px 8px; border-radius: 5px; border: 1px solid var(--b1); }

        /* ── INSIGHTS ── */
        .insights {
            background: var(--bg2); border: 1px solid var(--b2);
            border-radius: var(--r3); overflow: hidden;
            box-shadow: 0 1px 0 rgba(255,255,255,0.025) inset, 0 16px 48px rgba(0,0,0,0.4);
        }
        .insights-hd {
            display: flex; align-items: center; gap: 10px;
            padding: 16px 20px;
            background: linear-gradient(180deg, var(--bg3), var(--bg2));
            border-bottom: 1px solid var(--b1);
        }
        .ins-ico { width: 28px; height: 28px; border-radius: var(--r2); display: flex; align-items: center; justify-content: center; font-size: 11px; background: var(--gold3); border: 1px solid rgba(201,168,76,0.18); color: var(--gold); }
        .insights-hd h2 { font-family: 'Syne', sans-serif; font-size: 0.82rem; font-weight: 700; color: var(--t1); }

        .insights-body { display: grid; grid-template-columns: repeat(3,1fr); }

        .insight-item {
            padding: 20px 22px;
            border-right: 1px solid var(--b1);
            transition: background 0.15s;
        }
        .insight-item:last-child { border-right: none; }
        .insight-item:hover { background: rgba(255,255,255,0.01); }
        .insight-item label { font-family: 'Syne', sans-serif; display: block; font-size: 0.58rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: var(--t4); margin-bottom: 8px; }
        .insight-item p { font-size: 0.82rem; color: var(--t2); line-height: 1.6; }
        .insight-item p strong { color: var(--t1); font-weight: 600; }

        /* ── EMPTY ── */
        .empty { text-align: center; padding: 56px 20px; color: var(--t4); }
        .empty i { font-size: 1.8rem; display: block; margin-bottom: 12px; opacity: 0.2; }
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