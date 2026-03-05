<?php
/*
 * Admin — Inquiries, Center Contacts & Opening Hours
 */
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../app/models/VehicleInquiry.php';
require_once __DIR__ . '/../app/models/PorscheCenter.php';
require_once __DIR__ . '/../app/models/PorscheCenterHours.php';

$inquiryModel = new VehicleInquiry();
$centerModel  = new PorscheCenter();
$hoursModel   = new PorscheCenterHours();

$success = '';
$error   = '';
$tab     = $_GET['tab'] ?? 'inquiries';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'mark_read')    { $inquiryModel->markRead((int)$_POST['id']); $success = "Marked as read."; }
    if ($action === 'mark_replied') { $inquiryModel->markReplied((int)$_POST['id'], $_POST['reply_notes'] ?? ''); $success = "Marked as replied."; }
    if ($action === 'delete_inquiry') { $inquiryModel->delete((int)$_POST['id']); $success = "Inquiry deleted."; }
    if ($action === 'update_center') {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE porsche_centers SET phone=?,whatsapp=?,email=?,website=?,maps_url=? WHERE id=?");
        $stmt->execute([$_POST['phone']??null,$_POST['whatsapp']??null,$_POST['email']??null,$_POST['website']??null,$_POST['maps_url']??null,(int)$_POST['center_id']]);
        $success = "Center contact info updated."; $tab = 'centers';
    }
    if ($action === 'update_hours') {
        $centerId = (int)$_POST['center_id'];
        $days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
        $sortMap = array_flip($days);
        foreach ($days as $day) {
            $hoursModel->upsert($centerId, ['day_name'=>$day,'is_closed'=>isset($_POST['closed'][$day]),'open_time'=>$_POST['open_time'][$day]??null,'close_time'=>$_POST['close_time'][$day]??null,'lunch_start'=>$_POST['lunch_start'][$day]??null,'lunch_end'=>$_POST['lunch_end'][$day]??null,'sort_order'=>$sortMap[$day]+1]);
        }
        $success = "Opening hours updated."; $tab = 'hours';
    }
}

$centers          = $centerModel->getAll();
$filter           = ['is_read' => $_GET['filter'] ?? ''];
$inquiries        = $inquiryModel->getAll($filter);
$unread           = $inquiryModel->countUnread();
$selectedCenterId = (int)($_GET['center_id'] ?? ($centers[0]['id'] ?? 0));
$selectedHours    = $selectedCenterId ? $hoursModel->getByCenter($selectedCenterId) : [];
$selectedCenter   = null;
foreach ($centers as $c) { if ($c['id'] == $selectedCenterId) { $selectedCenter = $c; break; } }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inquiries & Centers — Porsche Admin</title>
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

        .back-btn {
            display: flex; align-items: center; gap: 6px;
            padding: 6px 14px; border-radius: var(--r4);
            font-size: 0.73rem; font-weight: 500;
            border: 1px solid var(--b2); color: var(--t2);
            text-decoration: none; transition: all 0.18s; background: var(--bg3);
        }
        .back-btn:hover { border-color: var(--b3); color: var(--t1); background: var(--bg4); }

        /* ── SUBNAV ── */
        .subnav {
            height: 48px; padding: 0 36px;
            display: flex; align-items: center; gap: 2px;
            background: var(--bg2); border-bottom: 1px solid var(--b1);
            position: sticky; top: 62px; z-index: 200;
        }

        .subnav a {
            display: flex; align-items: center; gap: 7px;
            padding: 0 16px; height: 48px;
            text-decoration: none; color: var(--t2);
            font-size: 0.78rem; font-weight: 500;
            border-bottom: 2px solid transparent;
            transition: all 0.15s; position: relative;
        }
        .subnav a i { font-size: 11px; }
        .subnav a:hover { color: var(--t1); }
        .subnav a.active { color: var(--t1); border-bottom-color: var(--gold); }
        .subnav a.active i { color: var(--gold); }

        .nbadge {
            background: var(--red); color: #fff;
            font-size: 0.62rem; font-weight: 700;
            padding: 1.5px 6px; border-radius: 20px; line-height: 1.5;
        }

        /* ── WRAP ── */
        .wrap { max-width: 1560px; margin: 0 auto; padding: 28px 36px 80px; position: relative; z-index: 1; }

        .panel { display: none; }
        .panel.active { display: block; animation: panelIn 0.2s ease; }
        @keyframes panelIn { from { opacity:0; transform:translateY(5px); } to { opacity:1; transform:translateY(0); } }

        /* ── TOAST ── */
        .toast { display: flex; align-items: center; gap: 10px; padding: 12px 16px; background: rgba(45,212,160,0.05); border: 1px solid rgba(45,212,160,0.16); border-radius: var(--r2); color: var(--green); font-size: 0.81rem; margin-bottom: 22px; animation: toastIn 0.3s cubic-bezier(0.34,1.56,0.64,1); }
        @keyframes toastIn { from { opacity:0; transform:translateY(-8px); } to { opacity:1; transform:translateY(0); } }
        .t-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--green); flex-shrink: 0; animation: blink 2s ease infinite; }
        @keyframes blink { 0%,100%{opacity:1;} 50%{opacity:0.4;} }

        /* ── STAT CARDS ── */
        .stats { display: grid; grid-template-columns: repeat(4,1fr); gap: 14px; margin-bottom: 24px; }

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
        .ic-rd { background: rgba(239,96,96,0.1);   border: 1px solid rgba(239,96,96,0.2);  color: var(--red); }
        .ic-bl { background: rgba(91,156,246,0.1);  border: 1px solid rgba(91,156,246,0.2); color: var(--blue); }
        .ic-gr { background: rgba(45,212,160,0.1);  border: 1px solid rgba(45,212,160,0.2); color: var(--green); }

        .sc-body label { display: block; font-family: 'Syne', sans-serif; font-size: 0.6rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: var(--t4); margin-bottom: 4px; }
        .sc-body strong { font-family: 'Syne', sans-serif; font-size: 2rem; font-weight: 800; line-height: 1; color: var(--t1); letter-spacing: -0.04em; display: block; }

        /* ── FILTER BAR ── */
        .fbar { display: flex; align-items: center; gap: 6px; margin-bottom: 16px; }
        .fbar-lbl { font-family: 'Syne', sans-serif; font-size: 0.6rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: var(--t4); margin-right: 4px; }
        .pill { padding: 5px 14px; border-radius: var(--r4); font-size: 0.76rem; font-weight: 500; border: 1px solid var(--b2); color: var(--t2); text-decoration: none; transition: all 0.15s; }
        .pill:hover { border-color: var(--b3); color: var(--t1); }
        .pill.on { background: linear-gradient(135deg, var(--gold), var(--gold2)); border-color: var(--gold); color: #080500; font-weight: 700; }

        /* ── TABLE ── */
        .tbox { background: var(--bg2); border: 1px solid var(--b2); border-radius: var(--r3); overflow: hidden; box-shadow: 0 1px 0 rgba(255,255,255,0.025) inset, 0 16px 48px rgba(0,0,0,0.4); }

        table { width: 100%; border-collapse: collapse; }
        thead th { padding: 11px 14px; text-align: left; font-family: 'Syne', sans-serif; font-size: 0.58rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: var(--t4); background: linear-gradient(180deg, var(--bg3), var(--bg2)); border-bottom: 1px solid var(--b1); white-space: nowrap; }
        tbody td { padding: 13px 14px; border-bottom: 1px solid var(--b1); vertical-align: top; font-size: 0.83rem; color: var(--t1); }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr { transition: background 0.1s; }
        tbody tr:hover td { background: rgba(255,255,255,0.012); }
        tbody tr.unread td { background: rgba(255,255,255,0.022); }
        tbody tr.unread:hover td { background: rgba(255,255,255,0.032); }

        .unread-dot { width: 7px; height: 7px; border-radius: 50%; background: var(--t1); display: inline-block; }

        /* ── BADGES ── */
        .badge { display: inline-flex; align-items: center; gap: 5px; padding: 3px 9px; border-radius: var(--r1); font-size: 0.68rem; font-weight: 600; letter-spacing: 0.02em; }
        .badge-msg { background: rgba(91,156,246,0.1);  color: var(--blue);  border: 1px solid rgba(91,156,246,0.18); }
        .badge-cb  { background: rgba(240,180,41,0.1);  color: var(--amber); border: 1px solid rgba(240,180,41,0.18); }
        .badge-ph  { background: rgba(45,212,160,0.1);  color: var(--green); border: 1px solid rgba(45,212,160,0.18); }

        .spill { display: inline-flex; align-items: center; gap: 5px; font-size: 0.74rem; font-weight: 500; }
        .s-new     { color: var(--red); }
        .s-read    { color: var(--t3); }
        .s-replied { color: var(--green); }

        /* ── TIMESTAMP ── */
        .ts-main { font-size: 0.79rem; color: var(--t2); }
        .ts-sub  { font-size: 0.73rem; color: var(--t4); margin-top: 1px; }

        /* ── VEHICLE CELL ── */
        .inq-veh { display: flex; align-items: center; gap: 10px; text-decoration: none; }
        .inq-veh-img { width: 72px; height: 48px; flex-shrink: 0; border-radius: var(--r1); overflow: hidden; background: var(--bg4); border: 1px solid var(--b2); display: flex; align-items: center; justify-content: center; color: var(--t4); font-size: 16px; }
        .inq-veh-img img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .inq-veh-title { font-size: 0.79rem; color: var(--blue); line-height: 1.4; margin-bottom: 3px; }
        .inq-veh-price { font-size: 0.74rem; color: var(--t2); }

        /* ── ACTION BUTTONS ── */
        .acts { display: flex; flex-wrap: wrap; gap: 4px; }
        .abtn { display: inline-flex; align-items: center; gap: 4px; padding: 5px 11px; border-radius: var(--r1); font-size: 0.69rem; font-weight: 600; border: 1px solid transparent; cursor: pointer; font-family: 'DM Sans', sans-serif; transition: all 0.12s; background: transparent; white-space: nowrap; }
        .ab-read  { border-color: var(--b2); color: var(--t2); }
        .ab-read:hover  { border-color: var(--b3); color: var(--t1); background: var(--bg3); }
        .ab-reply { border-color: rgba(45,212,160,0.2); color: var(--green); background: rgba(45,212,160,0.04); }
        .ab-reply:hover { background: rgba(45,212,160,0.12); border-color: var(--green); }
        .ab-del   { border-color: rgba(239,96,96,0.18); color: var(--red); background: rgba(239,96,96,0.04); }
        .ab-del:hover   { background: rgba(239,96,96,0.12); border-color: var(--red); }

        /* ── REPLY PANEL ── */
        .rpanel { display: none; margin-top: 8px; padding: 12px; background: var(--bg4); border: 1px solid var(--b2); border-radius: var(--r2); animation: popIn 0.18s ease; }
        @keyframes popIn { from { opacity:0; transform:scale(0.98) translateY(-4px); } to { opacity:1; transform:scale(1) translateY(0); } }
        .rpanel textarea { width: 100%; padding: 8px 11px; background: var(--bg5); border: 1px solid var(--b2); border-radius: var(--r1); color: var(--t1); font-size: 0.81rem; font-family: 'DM Sans', sans-serif; resize: vertical; min-height: 65px; outline: none; transition: border-color 0.14s; }
        .rpanel textarea:focus { border-color: var(--b4); }
        .rpanel textarea::placeholder { color: var(--t4); }
        .rpanel-submit { margin-top: 7px; padding: 7px 16px; background: linear-gradient(135deg, var(--gold), var(--gold2)); color: #080500; border: none; border-radius: var(--r1); font-size: 0.76rem; font-weight: 700; font-family: 'DM Sans', sans-serif; cursor: pointer; transition: opacity 0.15s; }
        .rpanel-submit:hover { opacity: 0.88; }

        /* ── SECTION HEADER ── */
        .sec-hd { display: flex; align-items: baseline; gap: 10px; margin-bottom: 18px; padding-bottom: 14px; border-bottom: 1px solid var(--b1); }
        .sec-hd h2 { font-family: 'Syne', sans-serif; font-size: 1rem; font-weight: 700; color: var(--t1); }
        .sec-cnt { font-size: 0.68rem; color: var(--t4); background: var(--bg3); border: 1px solid var(--b2); padding: 2px 8px; border-radius: var(--r4); }

        /* ── CENTER CARDS ── */
        .card {
            background: var(--bg2); border: 1px solid var(--b2);
            border-radius: var(--r3); overflow: hidden;
            margin-bottom: 18px;
            box-shadow: 0 1px 0 rgba(255,255,255,0.03) inset, 0 16px 48px rgba(0,0,0,0.42);
            transition: border-color 0.2s;
        }
        .card:hover { border-color: var(--b3); }
        .card-hd {
            display: flex; align-items: center; gap: 12px;
            padding: 16px 22px;
            background: linear-gradient(180deg, var(--bg3), var(--bg2));
            border-bottom: 1px solid var(--b1);
            position: relative;
        }
        .card-hd::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 1px; background: linear-gradient(90deg, transparent, rgba(201,168,76,0.2), transparent); }
        .card-ico { width: 28px; height: 28px; border-radius: var(--r2); display: flex; align-items: center; justify-content: center; font-size: 11px; flex-shrink: 0; background: var(--gold3); border: 1px solid rgba(201,168,76,0.18); color: var(--gold); }
        .card-hd h3 { font-family: 'Syne', sans-serif; font-size: 0.85rem; font-weight: 700; color: var(--t1); }
        .card-hd p { font-size: 0.72rem; color: var(--t3); margin-top: 1px; }
        .card-body { padding: 22px; }

        /* ── FORMS ── */
        .fg3 { display: grid; grid-template-columns: repeat(3,1fr); gap: 14px; margin-bottom: 14px; }
        .fg2 { display: grid; grid-template-columns: repeat(2,1fr); gap: 14px; margin-bottom: 14px; }

        .field { display: flex; flex-direction: column; gap: 5px; }
        .field label { font-family: 'Syne', sans-serif; font-size: 0.6rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: var(--t3); }
        .field input, .field textarea, .field select { padding: 8px 11px; background: var(--bg4); border: 1px solid var(--b2); border-radius: var(--r1); color: var(--t1); font-size: 0.83rem; font-family: 'DM Sans', sans-serif; outline: none; transition: border-color 0.14s, box-shadow 0.14s; width: 100%; }
        .field input:focus, .field textarea:focus, .field select:focus { border-color: var(--b4); box-shadow: 0 0 0 3px rgba(255,255,255,0.025); }
        .field input::placeholder, .field textarea::placeholder { color: var(--t4); }

        /* ── BUTTONS ── */
        .btn-p { display: inline-flex; align-items: center; gap: 8px; padding: 10px 22px; background: linear-gradient(135deg, var(--gold), var(--gold2)); color: #080500; border: none; border-radius: var(--r2); font-size: 0.79rem; font-weight: 700; font-family: 'DM Sans', sans-serif; cursor: pointer; letter-spacing: 0.04em; transition: all 0.18s; box-shadow: 0 3px 18px rgba(201,168,76,0.22); }
        .btn-p:hover { box-shadow: 0 5px 26px rgba(201,168,76,0.38); transform: translateY(-1px); }

        /* ── CENTER TABS ── */
        .ctabs { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 18px; }
        .ctab { padding: 6px 16px; border-radius: var(--r4); font-size: 0.76rem; font-weight: 500; border: 1px solid var(--b2); background: transparent; color: var(--t2); text-decoration: none; transition: all 0.15s; }
        .ctab:hover { border-color: var(--b3); color: var(--t1); }
        .ctab.on { background: linear-gradient(135deg, var(--gold), var(--gold2)); border-color: var(--gold); color: #080500; font-weight: 700; }

        /* ── HOURS TABLE ── */
        .htable { width: 100%; border-collapse: collapse; }
        .htable thead th { padding: 10px 12px; text-align: left; font-family: 'Syne', sans-serif; font-size: 0.58rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: var(--t4); background: linear-gradient(180deg, var(--bg3), var(--bg2)); border-bottom: 1px solid var(--b1); }
        .htable tbody td { padding: 10px 12px; border-bottom: 1px solid var(--b1); vertical-align: middle; }
        .htable tbody tr:last-child td { border-bottom: none; }
        .htable tbody tr:hover td { background: rgba(255,255,255,0.01); }

        .htable input[type="text"] { width: 86px; padding: 6px 9px; background: var(--bg4); border: 1px solid var(--b2); border-radius: var(--r1); color: var(--t1); font-size: 0.81rem; font-family: 'DM Sans', sans-serif; outline: none; transition: border-color 0.14s; }
        .htable input[type="text"]:focus { border-color: var(--b4); }
        .htable input[type="text"]::placeholder { color: var(--t4); }

        .day-name { font-size: 0.82rem; font-weight: 600; color: var(--t1); }

        /* Toggle */
        .tog { position: relative; width: 34px; height: 18px; display: inline-block; }
        .tog input { opacity: 0; width: 0; height: 0; }
        .tog-track { position: absolute; inset: 0; background: var(--bg4); border: 1px solid var(--b2); border-radius: 20px; cursor: pointer; transition: 0.18s; }
        .tog-track::before { content: ''; position: absolute; width: 12px; height: 12px; background: var(--t3); border-radius: 50%; left: 2px; top: 50%; transform: translateY(-50%); transition: 0.18s; }
        .tog input:checked + .tog-track { background: rgba(239,96,96,0.12); border-color: rgba(239,96,96,0.35); }
        .tog input:checked + .tog-track::before { background: var(--red); left: calc(100% - 14px); }

        .day-row.closed .time-input { opacity: 0.2; pointer-events: none; }

        .hint { font-size: 0.76rem; color: var(--t3); margin-bottom: 16px; display: flex; align-items: center; gap: 7px; }
        .hint code { background: var(--bg4); padding: 2px 7px; border-radius: 5px; font-family: 'Courier New', monospace; font-size: 0.73rem; color: var(--t2); border: 1px solid var(--b2); }

        /* ── EMPTY ── */
        .empty { text-align: center; padding: 56px 20px; color: var(--t4); }
        .empty i { font-size: 1.8rem; display: block; margin-bottom: 12px; opacity: 0.2; }
        .empty p { font-size: 0.82rem; }

        @media (max-width: 900px) {
            .topbar, .subnav, .wrap { padding-left: 14px; padding-right: 14px; }
            .stats { grid-template-columns: repeat(2,1fr); }
            .fg3, .fg2 { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<header class="topbar">
    <div style="display:flex; align-items:center; gap:12px;">
        <a class="brand" href="/lending_word/admin/">
            <div class="brand-mark"><i class="fas fa-shield-halved"></i></div>
            <span class="brand-name">Porsche</span>
            <div class="brand-div"></div>
            <span class="brand-sub">Admin</span>
        </a>
    </div>
    <a href="/lending_word/admin/" class="back-btn"><i class="fas fa-arrow-left"></i> Dashboard</a>
</header>

<nav class="subnav">
    <a href="?tab=inquiries" class="<?= $tab==='inquiries'?'active':'' ?>">
        <i class="fas fa-inbox"></i> Inquiries
        <?php if($unread>0): ?><span class="nbadge"><?= $unread ?></span><?php endif; ?>
    </a>
    <a href="?tab=centers" class="<?= $tab==='centers'?'active':'' ?>">
        <i class="fas fa-building"></i> Center Contacts
    </a>
    <a href="?tab=hours&center_id=<?= $selectedCenterId ?>" class="<?= $tab==='hours'?'active':'' ?>">
        <i class="fas fa-clock"></i> Opening Hours
    </a>
</nav>

<div class="wrap">

    <?php if($success): ?>
    <div class="toast"><span class="t-dot"></span><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <!-- ═══ INQUIRIES ═══ -->
    <div class="panel <?= $tab==='inquiries'?'active':'' ?>">
        <?php
        $total   = count($inquiries);
        $replied = count(array_filter($inquiries, fn($i) => $i['is_replied']));
        $readCnt = count(array_filter($inquiries, fn($i) => $i['is_read']));
        ?>
        <div class="stats">
            <div class="scard"><div class="sc-ico ic-bk"><i class="fas fa-inbox"></i></div><div class="sc-body"><label>Total</label><strong><?= $total ?></strong></div></div>
            <div class="scard"><div class="sc-ico ic-rd"><i class="fas fa-circle-dot"></i></div><div class="sc-body"><label>Unread</label><strong><?= $unread ?></strong></div></div>
            <div class="scard"><div class="sc-ico ic-bl"><i class="fas fa-eye"></i></div><div class="sc-body"><label>Read</label><strong><?= $readCnt ?></strong></div></div>
            <div class="scard"><div class="sc-ico ic-gr"><i class="fas fa-check-double"></i></div><div class="sc-body"><label>Replied</label><strong><?= $replied ?></strong></div></div>
        </div>

        <div class="fbar">
            <span class="fbar-lbl">Filter</span>
            <a href="?tab=inquiries"               class="pill <?= empty($_GET['filter'])?'on':'' ?>">All</a>
            <a href="?tab=inquiries&filter=unread" class="pill <?= ($_GET['filter']??'')==='unread'?'on':'' ?>">Unread</a>
            <a href="?tab=inquiries&filter=read"   class="pill <?= ($_GET['filter']??'')==='read'?'on':'' ?>">Read</a>
        </div>

        <div class="tbox">
            <table>
                <thead>
                    <tr>
                        <th style="width:14px;"></th>
                        <th>Date</th>
                        <th>Client</th>
                        <th>Contact</th>
                        <th>Vehicle</th>
                        <th>Center</th>
                        <th>Type</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if(empty($inquiries)): ?>
                <tr><td colspan="10"><div class="empty"><i class="fas fa-inbox"></i><p>No inquiries found.</p></div></td></tr>
                <?php endif; ?>
                <?php foreach($inquiries as $inq): ?>
                <tr class="<?= !$inq['is_read']?'unread':'' ?>">
                    <td style="vertical-align:middle;">
                        <?php if(!$inq['is_read']): ?><span class="unread-dot"></span><?php endif; ?>
                    </td>
                    <td style="white-space:nowrap;">
                        <div class="ts-main"><?= date('d M Y', strtotime($inq['created_at'])) ?></div>
                        <div class="ts-sub"><?= date('H:i', strtotime($inq['created_at'])) ?></div>
                    </td>
                    <td>
                        <div style="font-weight:600; font-size:0.85rem;"><?= htmlspecialchars($inq['full_name']) ?></div>
                        <?php if($inq['salutation']??''): ?><div style="font-size:0.72rem; color:var(--t3); margin-top:1px;"><?= htmlspecialchars($inq['salutation']) ?></div><?php endif; ?>
                    </td>
                    <td style="min-width:148px;">
                        <?php if($inq['email']): ?><a href="mailto:<?= htmlspecialchars($inq['email']) ?>" style="color:var(--blue); text-decoration:none; font-size:0.79rem; display:block; margin-bottom:2px;"><?= htmlspecialchars($inq['email']) ?></a><?php endif; ?>
                        <?php if($inq['phone_number']): ?><span style="font-size:0.77rem; color:var(--t2);"><?= htmlspecialchars($inq['phone_country_code']??'') ?> <?= htmlspecialchars($inq['phone_number']) ?></span><?php endif; ?>
                    </td>
                    <td style="min-width:200px;">
                        <?php if($inq['vehicle_title']): ?>
                        <a class="inq-veh" href="/lending_word/finder_detail.php?id=<?= $inq['vehicle_id'] ?>" target="_blank">
                            <div class="inq-veh-img">
                                <?php if(!empty($inq['main_image_url'])): ?>
                                <img src="<?= htmlspecialchars($inq['main_image_url']) ?>" alt="" loading="lazy" onerror="this.parentElement.innerHTML='<i class=\'fas fa-car\'></i>'">
                                <?php else: ?><i class="fas fa-car"></i><?php endif; ?>
                            </div>
                            <div>
                                <div class="inq-veh-title"><?= htmlspecialchars($inq['vehicle_title']) ?></div>
                                <div class="inq-veh-price">Rp <?= number_format($inq['vehicle_price'],0,',','.') ?></div>
                            </div>
                        </a>
                        <?php else: ?><span style="color:var(--t4);">–</span><?php endif; ?>
                    </td>
                    <td style="font-size:0.79rem; color:var(--t2); white-space:nowrap;"><?= htmlspecialchars($inq['center_name']??'–') ?></td>
                    <td>
                        <?php
                        $bclass = ['message'=>'badge-msg','callback'=>'badge-cb','phone'=>'badge-ph'];
                        $bico   = ['message'=>'fa-comment','callback'=>'fa-phone','phone'=>'fa-mobile-screen'];
                        $t = $inq['inquiry_type'];
                        ?>
                        <span class="badge <?= $bclass[$t]??'' ?>">
                            <i class="fas <?= $bico[$t]??'fa-question' ?>"></i>
                            <?= ucfirst($t) ?>
                        </span>
                    </td>
                    <td style="max-width:185px; font-size:0.79rem; color:var(--t2);">
                        <?php if($inq['inquiry_type']==='callback'&&$inq['callback_time']): ?>
                        <div style="color:var(--amber); font-size:0.73rem; margin-bottom:3px;"><i class="fas fa-clock"></i> <?= htmlspecialchars($inq['callback_time']) ?></div>
                        <?php endif; ?>
                        <?= htmlspecialchars(mb_substr($inq['message']??'–',0,100)) ?><?= strlen($inq['message']??'')>100?'…':'' ?>
                    </td>
                    <td>
                        <?php if($inq['is_replied']): ?><span class="spill s-replied"><i class="fas fa-check-double" style="font-size:9px;"></i> Replied</span>
                        <?php elseif($inq['is_read']): ?><span class="spill s-read"><i class="fas fa-eye" style="font-size:9px;"></i> Read</span>
                        <?php else: ?><span class="spill s-new"><i class="fas fa-circle" style="font-size:6px;"></i> New</span><?php endif; ?>
                    </td>
                    <td style="min-width:120px;">
                        <div class="acts">
                            <?php if(!$inq['is_read']): ?>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="mark_read">
                                <input type="hidden" name="id" value="<?= $inq['id'] ?>">
                                <button type="submit" class="abtn ab-read"><i class="fas fa-check"></i>Read</button>
                            </form>
                            <?php endif; ?>
                            <button type="button" class="abtn ab-reply" onclick="toggleReply(<?= $inq['id'] ?>)">
                                <i class="fas fa-reply"></i>Reply
                            </button>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="delete_inquiry">
                                <input type="hidden" name="id" value="<?= $inq['id'] ?>">
                                <button type="submit" class="abtn ab-del" onclick="return confirm('Delete this inquiry?')"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </div>
                        <div class="rpanel" id="reply-<?= $inq['id'] ?>">
                            <form method="POST">
                                <input type="hidden" name="action" value="mark_replied">
                                <input type="hidden" name="id" value="<?= $inq['id'] ?>">
                                <textarea name="reply_notes" placeholder="Reply notes…"><?= htmlspecialchars($inq['reply_notes']??'') ?></textarea>
                                <button type="submit" class="rpanel-submit"><i class="fas fa-check"></i> Mark as Replied</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ═══ CENTERS ═══ -->
    <div class="panel <?= $tab==='centers'?'active':'' ?>">
        <div class="sec-hd">
            <h2>Center Contacts</h2>
            <span class="sec-cnt"><?= count($centers) ?> location<?= count($centers)!==1?'s':'' ?></span>
        </div>
        <?php foreach($centers as $center): ?>
        <div class="card">
            <div class="card-hd">
                <div class="card-ico"><i class="fas fa-location-dot"></i></div>
                <div>
                    <h3><?= htmlspecialchars($center['name']) ?></h3>
                    <p><?= htmlspecialchars($center['city']??'') ?></p>
                </div>
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="action" value="update_center">
                    <input type="hidden" name="center_id" value="<?= $center['id'] ?>">
                    <div class="fg3">
                        <div class="field"><label>Phone</label><input type="text" name="phone" value="<?= htmlspecialchars($center['phone']??'') ?>" placeholder="+62 21 123 4567"></div>
                        <div class="field"><label>WhatsApp</label><input type="text" name="whatsapp" value="<?= htmlspecialchars($center['whatsapp']??'') ?>" placeholder="+62 812 345 678"></div>
                        <div class="field"><label>Email</label><input type="email" name="email" value="<?= htmlspecialchars($center['email']??'') ?>" placeholder="info@porsche.id"></div>
                    </div>
                    <div class="fg2">
                        <div class="field"><label>Website URL</label><input type="text" name="website" value="<?= htmlspecialchars($center['website']??'') ?>" placeholder="https://..."></div>
                        <div class="field"><label>Google Maps URL</label><input type="text" name="maps_url" value="<?= htmlspecialchars($center['maps_url']??'') ?>" placeholder="https://maps.google.com/..."></div>
                    </div>
                    <button type="submit" class="btn-p"><i class="fas fa-floppy-disk"></i> Save Contact</button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- ═══ HOURS ═══ -->
    <div class="panel <?= $tab==='hours'?'active':'' ?>">
        <div class="sec-hd"><h2>Opening Hours</h2></div>

        <div class="ctabs">
            <?php foreach($centers as $c): ?>
            <a href="?tab=hours&center_id=<?= $c['id'] ?>" class="ctab <?= $c['id']==$selectedCenterId?'on':'' ?>">
                <?= htmlspecialchars($c['name']) ?>
            </a>
            <?php endforeach; ?>
        </div>

        <?php if($selectedCenter): ?>
        <div class="card">
            <div class="card-hd">
                <div class="card-ico"><i class="fas fa-clock"></i></div>
                <div><h3><?= htmlspecialchars($selectedCenter['name']) ?></h3><p>Weekly schedule</p></div>
            </div>
            <div class="card-body">
                <p class="hint">
                    <i class="fas fa-circle-info"></i>
                    Format <code>HH.MM</code> — contoh <code>08.30</code> atau <code>18.00</code>. Kolom lunch boleh dikosongkan.
                </p>
                <form method="POST">
                    <input type="hidden" name="action" value="update_hours">
                    <input type="hidden" name="center_id" value="<?= $selectedCenterId ?>">
                    <table class="htable">
                        <thead>
                            <tr>
                                <th style="width:120px;">Day</th>
                                <th style="width:68px; text-align:center;">Closed</th>
                                <th>Opens</th>
                                <th>Lunch Start</th>
                                <th>Lunch End</th>
                                <th>Closes</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
                        $hmap = [];
                        foreach($selectedHours as $h) { $hmap[$h['day_name']] = $h; }
                        foreach($days as $day):
                            $h = $hmap[$day] ?? [];
                            $closed = !empty($h['is_closed']);
                        ?>
                        <tr class="day-row <?= $closed?'closed':'' ?>" id="row-<?= $day ?>">
                            <td><span class="day-name"><?= $day ?></span></td>
                            <td style="text-align:center;">
                                <label class="tog">
                                    <input type="checkbox" name="closed[<?= $day ?>]" value="1" <?= $closed?'checked':'' ?> onchange="toggleDay('<?= $day ?>',this.checked)">
                                    <span class="tog-track"></span>
                                </label>
                            </td>
                            <td class="time-input"><input type="text" name="open_time[<?= $day ?>]" value="<?= htmlspecialchars($h['open_time']??'') ?>" placeholder="08.30"></td>
                            <td class="time-input"><input type="text" name="lunch_start[<?= $day ?>]" value="<?= htmlspecialchars($h['lunch_start']??'') ?>" placeholder="12.00"></td>
                            <td class="time-input"><input type="text" name="lunch_end[<?= $day ?>]" value="<?= htmlspecialchars($h['lunch_end']??'') ?>" placeholder="13.00"></td>
                            <td class="time-input"><input type="text" name="close_time[<?= $day ?>]" value="<?= htmlspecialchars($h['close_time']??'') ?>" placeholder="18.00"></td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div style="margin-top:22px;">
                        <button type="submit" class="btn-p"><i class="fas fa-floppy-disk"></i> Save Opening Hours</button>
                    </div>
                </form>
            </div>
        </div>
        <?php else: ?>
        <div class="card"><div class="card-body"><div class="empty"><i class="fas fa-building"></i><p>No centers found.</p></div></div></div>
        <?php endif; ?>
    </div>

</div>

<script>
function toggleReply(id) {
    const el = document.getElementById('reply-' + id);
    el.style.display = el.style.display === 'block' ? 'none' : 'block';
}
function toggleDay(day, closed) {
    const row = document.getElementById('row-' + day);
    closed ? row.classList.add('closed') : row.classList.remove('closed');
}
</script>
</body>
</html>