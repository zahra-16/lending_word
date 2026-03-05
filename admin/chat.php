<?php
session_start();
require_once __DIR__ . '/../app/models/Admin.php';
if (!Admin::isLoggedIn()) {
    header('Location: /lending_word/admin/login.php');
    exit;
}
require_once __DIR__ . '/../app/models/ChatSession.php';
$sessionModel = new ChatSession();
$unread = $sessionModel->countUnread();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Chat — Admin Porsche Indonesia</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
    :root{
        --bg:#05050a;--bg2:#09090f;--bg3:#0e0e16;--bg4:#13131c;
        --b1:rgba(255,255,255,0.035);--b2:rgba(255,255,255,0.075);--b3:rgba(255,255,255,0.13);--b4:rgba(255,255,255,0.2);
        --t1:#eeeef4;--t2:#777790;--t3:#363648;--t4:#222230;
        --gold:#c9a84c;--gold2:#e8c97a;
        --green:#2dd4a0;--red:#ef6060;--amber:#f0b429;
        --r1:6px;--r2:10px;--r3:14px;--r4:100px;
    }
    *,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
    body{font-family:'DM Sans',sans-serif;background:var(--bg);color:var(--t1);height:100vh;display:flex;flex-direction:column;overflow:hidden;font-size:14px;-webkit-font-smoothing:antialiased;}
    ::-webkit-scrollbar{width:4px;}::-webkit-scrollbar-track{background:transparent;}::-webkit-scrollbar-thumb{background:var(--b3);border-radius:4px;}

    /* ── Topbar ── */
    .topbar{height:58px;padding:0 24px;display:flex;align-items:center;justify-content:space-between;background:rgba(5,5,10,0.9);border-bottom:1px solid var(--b2);flex-shrink:0;z-index:100;}
    .tb-left{display:flex;align-items:center;gap:14px;}
    .tb-back{display:flex;align-items:center;gap:7px;color:var(--t2);text-decoration:none;font-size:0.78rem;padding:6px 12px;border-radius:var(--r2);border:1px solid var(--b2);transition:all 0.15s;}
    .tb-back:hover{color:var(--t1);border-color:var(--b3);background:var(--bg3);}
    .tb-title{font-family:'Syne',sans-serif;font-size:0.9rem;font-weight:700;color:var(--t1);}
    .tb-badge{background:var(--red);color:#fff;font-size:0.6rem;font-weight:700;padding:2px 7px;border-radius:var(--r4);min-width:18px;text-align:center;display:none;}
    .tb-badge.show{display:inline-block;}

    /* ── Layout ── */
    .chat-layout{display:flex;flex:1;overflow:hidden;}

    /* ── Sessions Panel ── */
    .sessions-panel{width:320px;flex-shrink:0;border-right:1px solid var(--b1);display:flex;flex-direction:column;background:var(--bg2);}
    .sp-header{padding:16px 16px 10px;border-bottom:1px solid var(--b1);}
    .sp-title{font-family:'Syne',sans-serif;font-size:0.72rem;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:var(--t4);margin-bottom:10px;}
    .sp-filters{display:flex;gap:6px;flex-wrap:wrap;}
    .sp-filter{background:transparent;border:1px solid var(--b2);color:var(--t2);font-size:0.7rem;padding:4px 10px;border-radius:var(--r4);cursor:pointer;font-family:'DM Sans',sans-serif;transition:all 0.14s;}
    .sp-filter:hover{border-color:var(--b3);color:var(--t1);}
    .sp-filter.on{background:rgba(201,168,76,0.1);border-color:var(--gold);color:var(--gold);}
    .sessions-list{flex:1;overflow-y:auto;padding:8px;}

    /* ── Session Item ── */
    .session-item{display:flex;align-items:center;gap:10px;padding:10px 10px;border-radius:var(--r2);cursor:pointer;transition:background 0.12s;border:1px solid transparent;margin-bottom:4px;position:relative;}
    .session-item:hover{background:var(--bg3);}
    .session-item.active{background:var(--bg4);border-color:var(--b2);}
    .session-item.unread{border-left:2px solid var(--gold);}
    .si-avatar{width:36px;height:36px;border-radius:50%;background:var(--bg4);border:1px solid var(--b2);flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:600;color:var(--t2);}
    .si-info{flex:1;min-width:0;}
    .si-name{font-size:0.82rem;font-weight:600;color:var(--t1);margin-bottom:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
    .si-preview{font-size:0.73rem;color:var(--t3);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
    .si-meta{display:flex;flex-direction:column;align-items:flex-end;gap:4px;flex-shrink:0;}
    .si-time{font-size:0.65rem;color:var(--t4);}
    .si-dot{width:8px;height:8px;border-radius:50%;background:var(--gold);}
    .si-status{font-size:0.6rem;padding:2px 6px;border-radius:var(--r4);font-weight:600;}
    .si-status.open{background:rgba(45,212,160,0.1);color:var(--green);border:1px solid rgba(45,212,160,0.2);}
    .si-status.pending{background:rgba(240,180,41,0.1);color:var(--amber);border:1px solid rgba(240,180,41,0.2);}
    .si-status.closed{background:var(--bg4);color:var(--t4);border:1px solid var(--b1);}

    /* ── Session delete button (trash icon, muncul saat hover item) ── */
    .si-del-btn{
        width:24px;height:24px;border-radius:50%;flex-shrink:0;
        background:rgba(239,96,96,0.08);border:1px solid rgba(239,96,96,0.15);
        color:var(--red);font-size:10px;cursor:pointer;
        display:flex;align-items:center;justify-content:center;
        opacity:0;pointer-events:none;
        transition:opacity 0.15s,background 0.15s,transform 0.15s;
    }
    .session-item:hover .si-del-btn{opacity:1;pointer-events:all;}
    .si-del-btn:hover{background:rgba(239,96,96,0.22);border-color:rgba(239,96,96,0.4);transform:scale(1.12);}
    /* Animasi saat sesi dihapus */
    .session-item.removing{
        animation:siRemove 0.3s ease forwards;
        pointer-events:none;overflow:hidden;
    }
    @keyframes siRemove{
        0%{opacity:1;max-height:80px;transform:translateX(0);}
        100%{opacity:0;max-height:0;transform:translateX(-20px);padding:0;margin:0;}
    }

    .no-sessions{padding:40px 16px;text-align:center;color:var(--t4);font-size:0.82rem;}
    .no-sessions i{display:block;font-size:1.8rem;margin-bottom:10px;opacity:0.3;}

    /* ── Chat Main ── */
    .chat-main{flex:1;display:flex;flex-direction:column;background:var(--bg);overflow:hidden;}
    .chat-empty{flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;color:var(--t4);gap:12px;}
    .chat-empty i{font-size:2.5rem;opacity:0.2;}
    .chat-empty p{font-size:0.83rem;}
    .chat-header{padding:14px 20px;border-bottom:1px solid var(--b1);display:flex;align-items:center;justify-content:space-between;background:var(--bg2);flex-shrink:0;}
    .ch-info{display:flex;flex-direction:column;gap:2px;}
    .ch-name{font-size:0.88rem;font-weight:600;color:var(--t1);}
    .ch-sub{font-size:0.72rem;color:var(--t3);}
    .ch-actions{display:flex;gap:6px;}
    .ch-btn{padding:6px 14px;border-radius:var(--r2);border:1px solid var(--b2);background:transparent;color:var(--t2);font-size:0.72rem;font-family:'DM Sans',sans-serif;cursor:pointer;transition:all 0.13s;display:flex;align-items:center;gap:5px;}
    .ch-btn:hover{border-color:var(--b3);color:var(--t1);background:var(--bg3);}
    .ch-btn.danger:hover{border-color:rgba(239,96,96,0.3);color:var(--red);background:rgba(239,96,96,0.05);}
    .ch-btn.success:hover{border-color:rgba(45,212,160,0.3);color:var(--green);background:rgba(45,212,160,0.05);}

    /* ── Visitor Info Panel ── */
    .visitor-info{width:240px;flex-shrink:0;border-left:1px solid var(--b1);background:var(--bg2);padding:18px 16px;overflow-y:auto;display:none;}
    .visitor-info.show{display:block;}
    .vi-title{font-family:'Syne',sans-serif;font-size:0.6rem;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:var(--t4);margin-bottom:14px;}
    .vi-row{margin-bottom:12px;}
    .vi-label{font-size:0.65rem;letter-spacing:0.08em;text-transform:uppercase;color:var(--t4);margin-bottom:3px;}
    .vi-val{font-size:0.8rem;color:var(--t2);word-break:break-all;}
    .vi-val.highlight{color:var(--t1);}
    .vi-divider{height:1px;background:var(--b1);margin:14px 0;}
    .vi-badge{display:inline-block;padding:3px 8px;border-radius:var(--r4);font-size:0.65rem;font-weight:600;}

    /* ── Messages ── */
    .chat-messages{flex:1;overflow-y:auto;padding:20px 48px;display:flex;flex-direction:column;gap:12px;background:var(--bg);}
    .msg-wrap{display:flex;align-items:flex-end;gap:8px;}
    .msg-wrap.admin{flex-direction:row-reverse;}
    .msg-avatar{width:28px;height:28px;border-radius:50%;background:var(--bg4);border:1px solid var(--b2);flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:10px;color:var(--t3);}
    .msg-bubble-wrap{display:flex;flex-direction:column;gap:4px;max-width:65%;}
    .msg-wrap.admin .msg-bubble-wrap{align-items:flex-end;}
    .msg-bubble{padding:10px 14px;border-radius:12px;font-size:0.83rem;line-height:1.5;word-break:break-word;}
    .msg-wrap.visitor .msg-bubble{background:var(--bg3);border:1px solid var(--b2);color:var(--t1);border-radius:12px 12px 12px 3px;}
    .msg-wrap.admin .msg-bubble{background:linear-gradient(135deg,var(--gold),var(--gold2));color:#080500;border-radius:12px 12px 3px 12px;}
    .msg-time{font-size:0.63rem;color:var(--t4);padding:0 4px;}
    .msg-sender{font-size:0.65rem;color:var(--t3);padding:0 4px;margin-bottom:1px;}

    /* ── Hapus pesan — tombol di antara avatar & bubble ── */
    .msg-del-btn{
        width:26px;height:26px;border-radius:50%;flex-shrink:0;align-self:center;
        background:rgba(239,96,96,0.1);border:1px solid rgba(239,96,96,0.2);
        color:var(--red);font-size:11px;cursor:pointer;
        opacity:0;pointer-events:none;
        display:flex;align-items:center;justify-content:center;
        transition:opacity 0.15s,background 0.15s,transform 0.15s;
    }
    .msg-wrap:hover .msg-del-btn{opacity:1;pointer-events:all;}
    .msg-del-btn:hover{background:rgba(239,96,96,0.25);border-color:rgba(239,96,96,0.45);transform:scale(1.12);}
    .msg-deleted{font-size:0.75rem;color:var(--t4);font-style:italic;padding:6px 12px;border-radius:8px;border:1px dashed var(--t4);opacity:0.5;}

    /* ── Input area ── */
    .chat-input-area{padding:14px 20px;border-top:1px solid var(--b1);background:var(--bg2);flex-shrink:0;display:flex;gap:10px;align-items:flex-end;}
    .chat-input-area.disabled{opacity:0.45;pointer-events:none;}
    .chat-textarea{flex:1;background:var(--bg4);border:1px solid var(--b2);border-radius:var(--r2);padding:10px 14px;color:var(--t1);font-size:0.83rem;font-family:'DM Sans',sans-serif;resize:none;outline:none;min-height:40px;max-height:120px;line-height:1.5;transition:border-color 0.14s;}
    .chat-textarea:focus{border-color:var(--b4);}
    .chat-textarea::placeholder{color:var(--t4);}
    .send-btn{width:40px;height:40px;border-radius:var(--r2);background:linear-gradient(135deg,var(--gold),var(--gold2));border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#080500;font-size:15px;flex-shrink:0;transition:opacity 0.15s,transform 0.15s;}
    .send-btn:hover{opacity:0.88;transform:scale(1.05);}
    .closed-notice{flex:1;text-align:center;font-size:0.8rem;color:var(--t4);padding:8px;}

    /* ── Shared Modal styles ── */
    .modal-overlay{
        position:fixed;inset:0;background:rgba(0,0,0,0.65);
        z-index:9000;display:none;align-items:center;justify-content:center;
    }
    .modal-overlay.show{display:flex;}
    .modal-box{
        background:var(--bg3);border:1px solid var(--b2);border-radius:var(--r3);
        padding:28px 28px 22px;max-width:380px;width:90%;
        box-shadow:0 24px 64px rgba(0,0,0,0.5);
    }
    .modal-icon{font-size:2rem;color:var(--red);margin-bottom:12px;text-align:center;}
    .modal-title{font-family:'Syne',sans-serif;font-size:1rem;font-weight:700;color:var(--t1);text-align:center;margin-bottom:6px;}
    .modal-sub{font-size:0.8rem;color:var(--t3);text-align:center;margin-bottom:18px;line-height:1.55;}
    .modal-preview{
        background:var(--bg4);border:1px solid var(--b1);border-radius:var(--r1);
        padding:8px 12px;margin-bottom:18px;font-size:0.78rem;color:var(--t2);
        overflow:hidden;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;
    }
    .modal-actions{display:flex;gap:8px;justify-content:flex-end;}
    .modal-cancel{padding:8px 18px;border-radius:var(--r2);border:1px solid var(--b2);background:transparent;color:var(--t2);font-size:0.8rem;font-family:'DM Sans',sans-serif;cursor:pointer;transition:all 0.13s;}
    .modal-cancel:hover{border-color:var(--b3);color:var(--t1);}
    .modal-confirm{padding:8px 18px;border-radius:var(--r2);border:1px solid rgba(239,96,96,0.3);background:rgba(239,96,96,0.1);color:var(--red);font-size:0.8rem;font-family:'DM Sans',sans-serif;cursor:pointer;font-weight:600;transition:all 0.13s;}
    .modal-confirm:hover{background:rgba(239,96,96,0.2);border-color:rgba(239,96,96,0.5);}

    /* ── Toast ── */
    .toast{position:fixed;top:70px;right:20px;z-index:9999;padding:11px 16px;border-radius:var(--r2);font-size:0.8rem;font-weight:500;display:none;align-items:center;gap:8px;}
    .toast.show{display:flex;}
    .toast.ok{background:rgba(45,212,160,0.1);border:1px solid rgba(45,212,160,0.25);color:var(--green);}
    .toast.err{background:rgba(239,96,96,0.1);border:1px solid rgba(239,96,96,0.25);color:var(--red);}
    </style>
</head>
<body>

<div class="toast" id="toast"></div>

<!-- ══ Modal: Hapus Satu Pesan ══ -->
<div class="modal-overlay" id="modalDelMsg">
    <div class="modal-box">
        <div class="modal-icon"><i class="fas fa-trash-alt"></i></div>
        <div class="modal-title">Hapus Pesan?</div>
        <div class="modal-sub">Pesan ini akan dihapus permanen dan tidak bisa dikembalikan.</div>
        <div class="modal-preview" id="modalDelMsgPreview"></div>
        <div class="modal-actions">
            <button class="modal-cancel" id="modalDelMsgCancel">Batal</button>
            <button class="modal-confirm" id="modalDelMsgConfirm"><i class="fas fa-trash"></i> Hapus</button>
        </div>
    </div>
</div>

<!-- ══ Modal: Hapus Seluruh Percakapan ══ -->
<div class="modal-overlay" id="modalDelSession">
    <div class="modal-box">
        <div class="modal-icon"><i class="fas fa-comment-slash"></i></div>
        <div class="modal-title">Hapus Percakapan?</div>
        <div class="modal-sub">Seluruh pesan dalam percakapan <strong id="modalDelSessionName"></strong> akan dihapus permanen dari list.</div>
        <div class="modal-actions">
            <button class="modal-cancel" id="modalDelSessionCancel">Batal</button>
            <button class="modal-confirm" id="modalDelSessionConfirm"><i class="fas fa-trash"></i> Hapus Percakapan</button>
        </div>
    </div>
</div>

<header class="topbar">
    <div class="tb-left">
        <a href="/lending_word/admin/?tab=content" class="tb-back">
            <i class="fas fa-chevron-left"></i> Dashboard
        </a>
        <span class="tb-title"><i class="fas fa-comments" style="color:var(--gold);margin-right:6px;"></i>Live Chat</span>
        <span class="tb-badge <?= $unread > 0 ? 'show' : '' ?>" id="globalUnreadBadge"><?= $unread ?></span>
    </div>
    <div style="font-size:0.72rem;color:var(--t3);">Auto-refresh setiap 5 detik</div>
</header>

<div class="chat-layout">

    <!-- ── Sessions Panel ── -->
    <aside class="sessions-panel">
        <div class="sp-header">
            <div class="sp-title">Percakapan</div>
            <div class="sp-filters">
                <button class="sp-filter on" data-status="all">Semua</button>
                <button class="sp-filter" data-status="open">Aktif</button>
                <button class="sp-filter" data-status="pending">Pending</button>
                <button class="sp-filter" data-status="closed">Closed</button>
            </div>
        </div>
        <div class="sessions-list" id="sessionsList">
            <div class="no-sessions"><i class="fas fa-spinner fa-spin"></i>Memuat...</div>
        </div>
    </aside>

    <!-- ── Chat Main ── -->
    <main class="chat-main">
        <div class="chat-empty" id="chatEmpty">
            <i class="fas fa-comments"></i>
            <p>Pilih percakapan untuk mulai membalas</p>
        </div>

        <div id="chatArea" style="display:none;flex:1;flex-direction:column;overflow:hidden;">
            <div class="chat-header">
                <div class="ch-info">
                    <div class="ch-name" id="chatVisitorName">—</div>
                    <div class="ch-sub" id="chatVisitorSub">—</div>
                </div>
                <div class="ch-actions">
                    <button class="ch-btn" id="btnToggleInfo"><i class="fas fa-user"></i> Info</button>
                    <button class="ch-btn success" id="btnSetOpen"><i class="fas fa-circle-dot"></i> Buka</button>
                    <button class="ch-btn" id="btnSetPending"><i class="fas fa-clock"></i> Pending</button>
                    <button class="ch-btn danger" id="btnSetClosed"><i class="fas fa-ban"></i> Tutup</button>
                </div>
            </div>
            <div style="display:flex;flex:1;overflow:hidden;">
                <div class="chat-messages" id="chatMessages"></div>
                <div class="visitor-info" id="visitorInfo">
                    <div class="vi-title">Info Pengunjung</div>
                    <div class="vi-row"><div class="vi-label">Nama</div><div class="vi-val highlight" id="viName">—</div></div>
                    <div class="vi-row"><div class="vi-label">Email</div><div class="vi-val" id="viEmail">—</div></div>
                    <div class="vi-row"><div class="vi-label">Telepon</div><div class="vi-val" id="viPhone">—</div></div>
                    <div class="vi-divider"></div>
                    <div class="vi-row"><div class="vi-label">Status</div><div id="viStatus">—</div></div>
                    <div class="vi-row"><div class="vi-label">Mulai</div><div class="vi-val" id="viStarted">—</div></div>
                    <div class="vi-row"><div class="vi-label">Pesan</div><div class="vi-val" id="viMsgCount">—</div></div>
                    <div class="vi-row"><div class="vi-label">Session ID</div><div class="vi-val" id="viSessionId">—</div></div>
                </div>
            </div>
            <div class="chat-input-area" id="chatInputArea">
                <textarea class="chat-textarea" id="adminInput" rows="1" placeholder="Ketik balasan... (Enter kirim, Shift+Enter baris baru)"></textarea>
                <button class="send-btn" id="btnSend"><i class="fas fa-paper-plane"></i></button>
            </div>
        </div>
    </main>

</div><!-- .chat-layout -->

<script>
(function () {
'use strict';

const API = '/lending_word/admin/chat-admin-api.php';
let currentSessionId = null, lastMessageId = 0, pollTimer = null, currentFilter = 'all', infoVisible = false;

/* ════════════════════════════════
   HELPERS
════════════════════════════════ */
function toast(msg, type = 'ok') {
    const el = document.getElementById('toast');
    el.textContent = msg; el.className = 'toast show ' + type;
    clearTimeout(el._t); el._t = setTimeout(() => el.classList.remove('show'), 3000);
}

async function api(action, opts = {}) {
    const isPost = opts.body !== undefined;
    try {
        const res = await fetch(API + '?action=' + action + (opts.qs || ''), {
            method: isPost ? 'POST' : 'GET',
            headers: isPost ? { 'Content-Type': 'application/json' } : {},
            body: isPost ? JSON.stringify(opts.body) : undefined,
        });
        const text = await res.text();
        try { return JSON.parse(text); }
        catch (e) { console.error('[API parse]', action, text.substring(0, 200)); return { ok: false, error: 'Server error' }; }
    } catch (e) { return { ok: false, error: 'Network error' }; }
}

function fmtTime(dt) {
    const d = new Date(dt), now = new Date();
    if (d.toDateString() === now.toDateString())
        return d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
    return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short' }) + ' ' +
        d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
}

function esc(s) {
    return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;').replace(/'/g, '&#39;').replace(/\n/g, '<br>');
}

/* ════════════════════════════════
   MODAL: HAPUS PESAN
════════════════════════════════ */
let _delMsgId = null, _delMsgEl = null;
const modalDelMsg        = document.getElementById('modalDelMsg');
const modalDelMsgPreview = document.getElementById('modalDelMsgPreview');
const modalDelMsgCancel  = document.getElementById('modalDelMsgCancel');
const modalDelMsgConfirm = document.getElementById('modalDelMsgConfirm');

function openModalDelMsg(id, text, el) {
    _delMsgId = id; _delMsgEl = el;
    modalDelMsgPreview.textContent = text.substring(0, 140);
    modalDelMsg.classList.add('show');
}
function closeModalDelMsg() {
    _delMsgId = null; _delMsgEl = null;
    modalDelMsg.classList.remove('show');
}
modalDelMsgCancel.addEventListener('click', closeModalDelMsg);
modalDelMsg.addEventListener('click', e => { if (e.target === modalDelMsg) closeModalDelMsg(); });

modalDelMsgConfirm.addEventListener('click', async () => {
    if (!_delMsgId) return;
    modalDelMsgConfirm.disabled = true;
    modalDelMsgConfirm.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    const data = await api('delete_message', { body: { message_id: _delMsgId } });

    modalDelMsgConfirm.disabled = false;
    modalDelMsgConfirm.innerHTML = '<i class="fas fa-trash"></i> Hapus';

    if (data.ok) {
        toast('Pesan dihapus');
        if (_delMsgEl) {
            const isAdmin = _delMsgEl.classList.contains('admin');
            _delMsgEl.style.transition = 'opacity .25s, transform .25s';
            _delMsgEl.style.opacity = '0'; _delMsgEl.style.transform = 'scale(0.95)';
            setTimeout(() => {
                _delMsgEl.innerHTML = `<div style="display:flex;width:100%;justify-content:${isAdmin ? 'flex-end' : 'flex-start'};padding:0 40px;">
                    <span class="msg-deleted"><i class="fas fa-ban" style="margin-right:5px;font-size:0.65rem;"></i>Pesan dihapus</span>
                </div>`;
                _delMsgEl.classList.remove('admin', 'visitor');
                _delMsgEl.style.opacity = '1'; _delMsgEl.style.transform = '';
            }, 260);
        }
        closeModalDelMsg();
        loadSessions();
    } else {
        toast(data.error || 'Gagal menghapus pesan', 'err');
        closeModalDelMsg();
    }
});

/* ════════════════════════════════
   MODAL: HAPUS SESI
════════════════════════════════ */
let _delSessId = null, _delSessEl = null;
const modalDelSession        = document.getElementById('modalDelSession');
const modalDelSessionName    = document.getElementById('modalDelSessionName');
const modalDelSessionCancel  = document.getElementById('modalDelSessionCancel');
const modalDelSessionConfirm = document.getElementById('modalDelSessionConfirm');

function openModalDelSession(id, name, el) {
    _delSessId = id; _delSessEl = el;
    modalDelSessionName.textContent = name;
    modalDelSession.classList.add('show');
}
function closeModalDelSession() {
    _delSessId = null; _delSessEl = null;
    modalDelSession.classList.remove('show');
}
modalDelSessionCancel.addEventListener('click', closeModalDelSession);
modalDelSession.addEventListener('click', e => { if (e.target === modalDelSession) closeModalDelSession(); });

modalDelSessionConfirm.addEventListener('click', async () => {
    if (!_delSessId) return;
    modalDelSessionConfirm.disabled = true;
    modalDelSessionConfirm.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    const data = await api('delete_session', { body: { session_id: _delSessId } });

    modalDelSessionConfirm.disabled = false;
    modalDelSessionConfirm.innerHTML = '<i class="fas fa-trash"></i> Hapus Percakapan';

    if (data.ok) {
        toast('Percakapan dihapus');
        // Animasi remove item dari list
        if (_delSessEl) {
            _delSessEl.classList.add('removing');
            setTimeout(() => _delSessEl.remove(), 320);
        }
        // Jika sesi aktif yang dihapus, reset panel kanan
        if (_delSessId === currentSessionId) {
            currentSessionId = null; stopPolling();
            document.getElementById('chatArea').style.display = 'none';
            document.getElementById('chatEmpty').style.display = 'flex';
        }
        closeModalDelSession();
    } else {
        toast(data.error || 'Gagal menghapus percakapan', 'err');
        closeModalDelSession();
    }
});

/* ════════════════════════════════
   SESSIONS LIST
════════════════════════════════ */
async function loadSessions() {
    const data = await api('sessions', { qs: '&status=' + currentFilter });
    if (!data.ok) return;

    const badge = document.getElementById('globalUnreadBadge');
    data.unread_count > 0
        ? (badge.textContent = data.unread_count, badge.classList.add('show'))
        : badge.classList.remove('show');

    const list = document.getElementById('sessionsList');
    if (!data.sessions.length) {
        list.innerHTML = '<div class="no-sessions"><i class="fas fa-inbox"></i>Tidak ada percakapan</div>';
        return;
    }

    list.innerHTML = data.sessions.map(s => {
        const name    = s.visitor_name || ('Visitor #' + s.id);
        const preview = (s.last_message_preview || 'Belum ada pesan').substring(0, 48);
        const isUnread = s.is_read_admin == 0 && s.status !== 'closed';
        return `<div class="session-item ${s.id == currentSessionId ? 'active' : ''} ${isUnread ? 'unread' : ''}"
                     data-id="${s.id}" onclick="openSession(${s.id})">
            <div class="si-avatar">${name.charAt(0).toUpperCase()}</div>
            <div class="si-info">
                <div class="si-name">${esc(name)}</div>
                <div class="si-preview">${esc(preview)}</div>
            </div>
            <div class="si-meta">
                <div class="si-time">${fmtTime(s.last_message_at)}</div>
                ${isUnread ? '<div class="si-dot"></div>' : ''}
                <div class="si-status ${s.status}">${s.status}</div>
            </div>
            <button class="si-del-btn" title="Hapus percakapan"
                onclick="event.stopPropagation(); triggerDelSession(${s.id}, '${esc(name)}', this)">
                <i class="fas fa-trash"></i>
            </button>
        </div>`;
    }).join('');
}

window.triggerDelSession = function (sessionId, name, btn) {
    const itemEl = btn.closest('.session-item');
    openModalDelSession(sessionId, name, itemEl);
};

/* ════════════════════════════════
   OPEN SESSION
════════════════════════════════ */
window.openSession = async function (id) {
    currentSessionId = id; lastMessageId = 0; stopPolling();
    document.getElementById('chatEmpty').style.display = 'none';
    const area = document.getElementById('chatArea');
    area.style.display = 'flex';
    document.getElementById('chatMessages').innerHTML =
        '<div class="no-sessions"><i class="fas fa-spinner fa-spin"></i>Memuat pesan...</div>';

    const data = await api('messages', { qs: '&session_id=' + id });
    if (!data.ok) { toast('Gagal memuat pesan', 'err'); return; }

    updateSessionHeader(data.session);
    renderMessages(data.messages);
    lastMessageId = data.last_id || 0;

    const inputArea = document.getElementById('chatInputArea');
    if (data.session.status === 'closed') {
        inputArea.className = 'chat-input-area disabled';
        inputArea.innerHTML = '<div class="closed-notice"><i class="fas fa-lock"></i> Sesi ini sudah ditutup</div>';
    } else {
        inputArea.className = 'chat-input-area';
        inputArea.innerHTML = `<textarea class="chat-textarea" id="adminInput" rows="1" placeholder="Ketik balasan..."></textarea>
            <button class="send-btn" id="btnSend"><i class="fas fa-paper-plane"></i></button>`;
        document.getElementById('adminInput').addEventListener('keydown', e => {
            if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendReply(); }
        });
        document.getElementById('adminInput').addEventListener('input', () => {
            const ta = document.getElementById('adminInput');
            ta.style.height = 'auto'; ta.style.height = Math.min(ta.scrollHeight, 120) + 'px';
        });
        document.getElementById('btnSend').addEventListener('click', sendReply);
        document.getElementById('adminInput').focus();
    }
    loadSessions(); startPolling();
};

function updateSessionHeader(s) {
    const name = s.visitor_name || ('Visitor #' + s.id);
    document.getElementById('chatVisitorName').textContent = name;
    document.getElementById('chatVisitorSub').textContent =
        [s.visitor_email, s.visitor_phone].filter(Boolean).join(' · ') || 'Tidak ada kontak';
    document.getElementById('viName').textContent    = s.visitor_name || '—';
    document.getElementById('viEmail').textContent   = s.visitor_email || '—';
    document.getElementById('viPhone').textContent   = s.visitor_phone || '—';
    document.getElementById('viStarted').textContent = fmtTime(s.started_at);
    document.getElementById('viMsgCount').textContent = s.total_messages + ' pesan';
    document.getElementById('viSessionId').textContent = '#' + s.id;
    document.getElementById('viStatus').innerHTML =
        `<span class="vi-badge si-status ${s.status}">${s.status}</span>`;
}

/* ════════════════════════════════
   RENDER MESSAGES
════════════════════════════════ */
function renderMessages(messages) {
    const box = document.getElementById('chatMessages');
    box.innerHTML = '';
    messages.forEach(m => appendMessage(m));
    box.scrollTop = box.scrollHeight;
}

function appendMessage(m) {
    const box = document.getElementById('chatMessages');
    if (m.id && document.querySelector(`.msg-wrap[data-id="${m.id}"]`)) return; // dedupe

    const wrap = document.createElement('div');
    wrap.className  = 'msg-wrap ' + m.sender_type;
    wrap.dataset.id = m.id;

    const name = m.sender_name || (m.sender_type === 'admin' ? 'Admin' : 'Visitor');

    // Tombol hapus pesan — di antara avatar dan bubble-wrap
    wrap.innerHTML = `
        <div class="msg-avatar">${name.charAt(0).toUpperCase()}</div>
        <button class="msg-del-btn" title="Hapus pesan"><i class="fas fa-trash"></i></button>
        <div class="msg-bubble-wrap">
            <div class="msg-sender">${esc(name)}</div>
            <div class="msg-bubble">${esc(m.message)}</div>
            <div class="msg-time">${fmtTime(m.sent_at)}</div>
        </div>`;

    wrap.querySelector('.msg-del-btn').addEventListener('click', e => {
        e.stopPropagation();
        openModalDelMsg(m.id, m.message, wrap);
    });

    wrap.style.opacity = '0';
    wrap.style.transform = m.sender_type === 'admin' ? 'translateX(12px)' : 'translateX(-12px)';
    box.appendChild(wrap);
    requestAnimationFrame(() => requestAnimationFrame(() => {
        wrap.style.transition = 'opacity .3s ease, transform .35s cubic-bezier(0.16,1,0.3,1)';
        wrap.style.opacity = '1'; wrap.style.transform = 'translateX(0)';
    }));
    box.scrollTop = box.scrollHeight;
}

/* ════════════════════════════════
   SEND REPLY
════════════════════════════════ */
async function sendReply() {
    const input = document.getElementById('adminInput');
    if (!input) return;
    const text = input.value.trim();
    if (!text || !currentSessionId) return;
    const btn = document.getElementById('btnSend');
    btn.disabled = true;
    const data = await api('reply', { body: { session_id: currentSessionId, message: text } });
    btn.disabled = false;
    if (data.ok) {
        input.value = ''; input.style.height = 'auto';
        appendMessage(data.message); lastMessageId = data.message.id;
        loadSessions();
    } else { toast(data.error || 'Gagal mengirim', 'err'); }
}

/* ════════════════════════════════
   STATUS
════════════════════════════════ */
async function setStatus(status) {
    if (!currentSessionId) return;
    const data = await api('set_status', { body: { session_id: currentSessionId, status } });
    if (data.ok) { toast('Status: ' + status); openSession(currentSessionId); loadSessions(); }
    else { toast(data.error || 'Gagal ubah status', 'err'); }
}
document.getElementById('btnSetOpen').onclick    = () => setStatus('open');
document.getElementById('btnSetPending').onclick = () => setStatus('pending');
document.getElementById('btnSetClosed').onclick  = () => { if (confirm('Tutup sesi ini?')) setStatus('closed'); };

/* ════════════════════════════════
   POLLING
════════════════════════════════ */
function startPolling() {
    stopPolling();
    pollTimer = setInterval(async () => {
        if (!currentSessionId) return;
        const data = await api('poll_admin', { qs: `&session_id=${currentSessionId}&after_id=${lastMessageId}` });
        if (!data.ok) return;
        if (data.messages && data.messages.length) {
            data.messages.forEach(m => appendMessage(m));
            lastMessageId = data.last_id;
            loadSessions();
        }
        const badge = document.getElementById('globalUnreadBadge');
        data.unread_count > 0
            ? (badge.textContent = data.unread_count, badge.classList.add('show'))
            : badge.classList.remove('show');
    }, 4000);
}
function stopPolling() { clearInterval(pollTimer); }

/* ════════════════════════════════
   INIT
════════════════════════════════ */
setInterval(loadSessions, 8000);

document.querySelectorAll('.sp-filter').forEach(btn => btn.addEventListener('click', () => {
    document.querySelectorAll('.sp-filter').forEach(b => b.classList.remove('on'));
    btn.classList.add('on'); currentFilter = btn.dataset.status; loadSessions();
}));
document.getElementById('btnToggleInfo').addEventListener('click', () => {
    infoVisible = !infoVisible;
    document.getElementById('visitorInfo').classList.toggle('show', infoVisible);
});

loadSessions();
})();
</script>
</body>
</html>