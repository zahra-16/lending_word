<?php
// public/porsche-chat-widget.php
// Include file ini di halaman utama dengan: <?php include 'porsche-chat-widget.php'; ?>

<style>
:root {
    --chat-bg:       #5a5f5e;
    --chat-bg-dark:  #3e4443;
    --chat-white:    #ffffff;
    --chat-light:    #f4f4f4;
    --chat-border:   #e8e8e8;
    --chat-text:     #1a1a1a;
    --chat-sub:      #888;
    --chat-btn:      #5a5f5e;
    --chat-radius:   18px;
    --chat-w:        390px;
    --chat-h:        620px;
    --chat-ease:     cubic-bezier(0.16, 1, 0.3, 1);
}

/* ══════════════════════════════════════
   CUSTOM CURSOR — identik dengan model-detail.php
══════════════════════════════════════ */
#chat-window, 
#chat-window *,
#chat-toggle {
    cursor: none !important;
}

#chat-cur-dot,
#chat-cur-ring {
    position: fixed;
    pointer-events: none;
    z-index: 99999;
    border-radius: 50%;
    top: 0; left: 0;
    transform: translate(-50%, -50%);
    will-change: left, top, transform;
    transition-property: width, height, opacity, transform;
    transition-timing-function: cubic-bezier(0.16, 1, 0.3, 1);
    mix-blend-mode: difference;
    opacity: 0;
    visibility: hidden;
}

#chat-cur-dot {
    width: 8px; height: 8px;
    background: #ffffff;
    transition-duration: .2s, .2s, .2s, .15s;
}

#chat-cur-ring {
    width: 38px; height: 38px;
    border: 1.5px solid #ffffff;
    background: transparent;
    transition-duration: .35s, .35s, .3s, .22s;
}

/* State: link/button */
body.chat-c-link #chat-cur-dot  { width: 5px;  height: 5px; }
body.chat-c-link #chat-cur-ring { width: 54px; height: 54px; }

/* State: card hover */
body.chat-c-card #chat-cur-dot  { width: 10px; height: 10px; }
body.chat-c-card #chat-cur-ring { width: 54px; height: 54px; }

/* State: click/press */
body.chat-c-click #chat-cur-dot {
    transform: translate(-50%, -50%) scale(2.5);
    opacity: 0;
}
body.chat-c-click #chat-cur-ring {
    transform: translate(-50%, -50%) scale(1.5);
    opacity: 0;
}

/* Cursor aktif */
#chat-cur-dot.chat-cur-on,
#chat-cur-ring.chat-cur-on {
    opacity: 1;
    visibility: visible;
}

/* Mobile: fallback ke cursor biasa */
@media (max-width: 768px) {
    #chat-cur-dot, #chat-cur-ring { display: none !important; }
    #chat-window *, #chat-toggle  { cursor: auto !important; }
}

/* ── Toggle Button ── */
#chat-toggle {
    position: fixed; bottom: 28px; left: 28px;
    width: 58px; height: 58px; background: var(--chat-btn);
    border-radius: 50%; border: none; z-index: 9990;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 4px 24px rgba(0,0,0,0.32);
    transition: transform .35s var(--chat-ease), box-shadow .3s ease, opacity .4s ease, visibility .4s ease;
    opacity: 0; visibility: hidden; transform: scale(0.8);
}
#chat-toggle.chat-visible { opacity: 1; visibility: visible; transform: scale(1); }
#chat-toggle:hover { transform: scale(1.09) !important; box-shadow: 0 8px 30px rgba(0,0,0,0.42); }
#chat-toggle svg { width: 26px; height: 26px; fill: #fff; }
#chat-toggle .icon-close { display: none; }
#chat-toggle.open .icon-chat  { display: none; }
#chat-toggle.open .icon-close { display: block; }
#chat-toggle::before {
    content: ''; position: absolute; inset: -4px; border-radius: 50%;
    border: 2px solid rgba(255,255,255,0.22); animation: none; opacity: 0;
}
#chat-toggle.chat-visible::before { animation: chatPulse 2.8s ease-in-out infinite; }
@keyframes chatPulse { 0%,100%{transform:scale(1);opacity:.6;}50%{transform:scale(1.2);opacity:0;} }

#chat-unread-badge {
    position: absolute; top: 2px; right: 2px;
    min-width: 18px; height: 18px; background: #e53e3e; border-radius: 9px;
    border: 2px solid #fff; display: none; align-items: center; justify-content: center;
    font-size: 10px; font-weight: 700; color: #fff; padding: 0 4px;
}
#chat-unread-badge.show { display: flex; }

/* ══════════════════════════════════════
   TOOLTIP / BUBBLE NOTIFIKASI
══════════════════════════════════════ */
#chat-tooltip {
    position: fixed;
    bottom: 100px;
    left: 28px;
    width: 280px;
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.16), 0 0 0 1px rgba(0,0,0,0.06);
    padding: 16px;
    z-index: 9988;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    opacity: 0;
    visibility: hidden;
    transform: translateY(12px) scale(0.96);
    transform-origin: bottom left;
    transition: opacity .38s var(--chat-ease), transform .4s var(--chat-ease), visibility .38s ease;
    pointer-events: none;
    cursor: auto !important;
}
#chat-tooltip.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0) scale(1);
    pointer-events: all;
}
/* Sembunyikan tooltip saat chat terbuka */
#chat-tooltip.hidden {
    opacity: 0 !important;
    visibility: hidden !important;
    pointer-events: none !important;
}

/* Ekor / arrow bawah tooltip */
#chat-tooltip::after {
    content: '';
    position: absolute;
    bottom: -8px;
    left: 28px;
    width: 16px;
    height: 16px;
    background: #fff;
    box-shadow: 3px 3px 6px rgba(0,0,0,0.07);
    transform: rotate(45deg);
    border-radius: 2px;
    clip-path: polygon(0 0, 100% 100%, 0 100%);
}

/* Avatar logo di tooltip */
#chat-tooltip .tt-avatar {
    width: 46px;
    height: 46px;
    border-radius: 50%;
    background: #f5f5f5;
    border: 1.5px solid #e8e8e8;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}
#chat-tooltip .tt-avatar img {
    width: 30px;
    height: 30px;
    object-fit: contain;
}

/* Teks tooltip */
#chat-tooltip .tt-body {
    flex: 1;
    min-width: 0;
}
#chat-tooltip .tt-title {
    font-size: 14px;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 4px;
    letter-spacing: -.01em;
}
#chat-tooltip .tt-text {
    font-size: 13px;
    color: #555;
    line-height: 1.45;
}

/* Tombol close tooltip */
#chat-tooltip-close {
    position: absolute;
    top: 10px;
    right: 10px;
    width: 24px;
    height: 24px;
    border: 1.5px solid #e0e0e0;
    border-radius: 50%;
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    color: #888;
    line-height: 1;
    flex-shrink: 0;
    cursor: pointer !important;
    transition: background .18s, color .18s, border-color .18s;
    z-index: 2;
}
#chat-tooltip-close:hover {
    background: #f0f0f0;
    color: #333;
    border-color: #ccc;
}

@media (max-width: 480px) {
    #chat-tooltip {
        left: 12px;
        right: 12px;
        width: auto;
        bottom: 90px;
    }
    #chat-tooltip::after {
        left: 32px;
    }
}

/* ── Chat Window ── */
#chat-window {
    position: fixed; bottom: 100px; left: 28px;
    width: var(--chat-w); height: var(--chat-h);
    background: var(--chat-white); border-radius: var(--chat-radius);
    overflow: hidden; z-index: 9989;
    box-shadow: 0 24px 64px rgba(0,0,0,0.22), 0 0 0 1px rgba(0,0,0,0.05);
    display: flex; flex-direction: column;
    opacity: 0; transform: translateY(24px) scale(0.95);
    pointer-events: none; visibility: hidden;
    transition: opacity .4s var(--chat-ease), transform .4s var(--chat-ease), visibility .4s ease;
    transform-origin: bottom left;
}
#chat-window.open {
    opacity: 1; transform: translateY(0) scale(1);
    pointer-events: all; visibility: visible;
}

/* ── Screens ── */
.chat-screen {
    position: absolute; inset: 0; display: flex; flex-direction: column;
    background: var(--chat-white);
    transition: opacity .28s ease, transform .32s var(--chat-ease);
    opacity: 0; pointer-events: none; transform: translateX(32px); z-index: 1;
    overflow: hidden;
}
.chat-screen.active { opacity: 1; pointer-events: all; transform: translateX(0); z-index: 2; }

/* ── Header ── */
.chat-header {
    background: var(--chat-bg); padding: 14px 16px;
    display: flex; align-items: center; justify-content: space-between;
    flex-shrink: 0; z-index: 10;
}
.chat-header-left  { display: flex; align-items: center; gap: 10px; flex: 1; min-width: 0; }
.chat-header-right { display: flex; gap: 6px; }
.chat-avatar {
    width: 38px; height: 38px; background: #fff; border-radius: 50%;
    display: flex; align-items: center; justify-content: center; overflow: hidden; flex-shrink: 0;
}
.chat-avatar img { width: 26px; height: 26px; object-fit: contain; }
.chat-header-title { color: #fff; font-size: 14px; font-weight: 600; letter-spacing: .01em; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.chat-header-sub   { color: rgba(255,255,255,0.62); font-size: 11px; margin-top: 1px; }
.chat-icon-btn {
    width: 34px; height: 34px; background: rgba(255,255,255,0.13); border: none;
    border-radius: 50%; display: flex; align-items: center; justify-content: center;
    color: rgba(255,255,255,0.88); font-size: 13px; flex-shrink: 0;
    transition: background .2s, transform .15s; -webkit-tap-highlight-color: transparent;
}
.chat-icon-btn:hover  { background: rgba(255,255,255,0.24); }
.chat-icon-btn:active { background: rgba(255,255,255,0.36); transform: scale(0.9); }

/* ── Status Dot ── */
.status-dot {
    width: 7px; height: 7px; border-radius: 50%; background: #2dd4a0;
    display: inline-block; margin-right: 4px; vertical-align: middle;
    animation: statusBlink 2s ease-in-out infinite;
}
@keyframes statusBlink { 0%,100%{opacity:1;}50%{opacity:.35;} }

/* ══════════════════════════════════════
   SCREEN: WELCOME
══════════════════════════════════════ */
#screen-welcome .welcome-body {
    flex: 1; padding: 28px 22px 0; overflow-y: auto;
}
#screen-welcome .welcome-body h2 {
    font-size: 24px; font-weight: 700; color: var(--chat-text); margin-bottom: 8px;
}
#screen-welcome .welcome-body p {
    font-size: 13.5px; color: var(--chat-sub); line-height: 1.55; margin-bottom: 20px;
}

.conv-preview-card {
    background: #fff; border: 1px solid var(--chat-border); border-radius: 14px;
    overflow: hidden;
    transition: box-shadow .22s, transform .18s;
    margin-bottom: 10px;
}
.conv-preview-card:hover { box-shadow: 0 5px 20px rgba(0,0,0,0.09); transform: translateY(-1px); }
.conv-preview-item {
    display: flex; align-items: center; gap: 12px; padding: 14px 16px;
    border-bottom: 1px solid var(--chat-border);
}
.conv-item-avatar {
    width: 38px; height: 38px; border-radius: 50%; background: #f0f0f0;
    flex-shrink: 0; display: flex; align-items: center; justify-content: center; overflow: hidden;
}
.conv-item-avatar img { width: 26px; height: 26px; object-fit: contain; }
.conv-item-info { flex: 1; min-width: 0; }
.conv-item-name    { font-size: 13px; font-weight: 600; color: var(--chat-text); }
.conv-item-preview {
    font-size: 12px; color: var(--chat-sub);
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: 2px;
}
.conv-item-time  { font-size: 11px; color: var(--chat-sub); white-space: nowrap; flex-shrink: 0; }
.conv-item-ticks { font-size: 11px; color: #2dd4a0; }
.conv-preview-footer {
    display: flex; align-items: center; justify-content: space-between;
    padding: 12px 16px; font-size: 13px; font-weight: 600; color: var(--chat-text);
    transition: background .18s;
}
.conv-preview-footer:hover { background: #fafafa; }
.conv-preview-footer .badge {
    background: var(--chat-btn); color: #fff; font-size: 11px; font-weight: 700;
    min-width: 20px; height: 20px; border-radius: 10px; padding: 0 6px;
    display: inline-flex; align-items: center; justify-content: center;
}

.welcome-new-btn {
    width: calc(100% - 44px); margin: 0 22px 20px;
    background: var(--chat-btn); color: #fff;
    font-size: 14px; font-weight: 600; padding: 15px;
    border: none; border-radius: 12px;
    transition: background .2s, transform .15s;
    flex-shrink: 0;
}
.welcome-new-btn:hover  { background: var(--chat-bg-dark); }
.welcome-new-btn:active { transform: scale(0.98); }

.privacy-link-btn {
    display: inline-block; background: transparent; color: var(--chat-sub);
    font-size: 11.5px; padding: 0; border: none;
    text-decoration: underline; margin-bottom: 22px;
    transition: color .18s;
}
.privacy-link-btn:hover { color: var(--chat-text); }

/* ══════════════════════════════════════
   SCREEN: CONVERSATIONS LIST
══════════════════════════════════════ */
#screen-conversations .conv-list-body {
    flex: 1; overflow-y: auto; padding: 12px 0;
}
.conv-list-item {
    display: flex; align-items: center; gap: 12px;
    padding: 14px 18px; border-bottom: 1px solid #f2f2f2;
    transition: background .18s;
}
.conv-list-item:hover { background: #fafafa; }
.conv-list-item.unread .conv-list-preview { color: var(--chat-text); font-weight: 500; }
.conv-list-item.unread .conv-list-name    { font-weight: 700; }
.conv-list-avatar {
    width: 42px; height: 42px; border-radius: 50%; background: #f0f0f0;
    flex-shrink: 0; display: flex; align-items: center; justify-content: center; overflow: hidden;
}
.conv-list-avatar img { width: 28px; height: 28px; object-fit: contain; }
.conv-list-info { flex: 1; min-width: 0; }
.conv-list-name    { font-size: 13.5px; font-weight: 600; color: var(--chat-text); }
.conv-list-preview {
    font-size: 12px; color: var(--chat-sub); margin-top: 3px;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.conv-list-meta { text-align: right; flex-shrink: 0; }
.conv-list-time  { font-size: 11px; color: var(--chat-sub); }
.conv-list-dot   {
    width: 8px; height: 8px; background: var(--chat-btn); border-radius: 50%;
    margin: 4px 0 0 auto; display: none;
}
.conv-list-item.unread .conv-list-dot { display: block; }

.conv-list-empty {
    text-align: center; padding: 50px 30px; color: var(--chat-sub); font-size: 13px;
}
.conv-list-empty svg { width: 48px; height: 48px; fill: #ddd; margin-bottom: 14px; }

.conv-list-new-btn {
    width: calc(100% - 36px); margin: 0 18px 18px;
    background: var(--chat-btn); color: #fff;
    font-size: 14px; font-weight: 600; padding: 14px;
    border: none; border-radius: 12px;
    transition: background .2s; flex-shrink: 0;
}
.conv-list-new-btn:hover { background: var(--chat-bg-dark); }

/* ══════════════════════════════════════
   SCREEN: PRIVACY
══════════════════════════════════════ */
#screen-privacy .privacy-body {
    flex: 1; padding: 32px 24px; overflow-y: auto;
}
#screen-privacy h3 { font-size: 22px; font-weight: 700; color: var(--chat-text); margin-bottom: 16px; }
#screen-privacy p  { font-size: 13px; color: var(--chat-sub); line-height: 1.65; margin-bottom: 12px; }
.privacy-accept-btn {
    background: var(--chat-btn); color: #fff; padding: 13px 36px;
    border: none; border-radius: 10px; font-size: 14px; font-weight: 600;
    float: right; margin-top: 10px; transition: background .2s;
}
.privacy-accept-btn:hover { background: var(--chat-bg-dark); }

/* ══════════════════════════════════════
   SCREEN: CHAT
══════════════════════════════════════ */
#screen-chat .chat-messages-area {
    flex: 1; overflow-y: auto; padding: 18px 14px;
    display: flex; flex-direction: column; gap: 14px; background: #f7f7f7;
}

/* Bubbles */
.msg-bubble { max-width: 82%; display: flex; align-items: flex-end; gap: 7px; }
.msg-bubble.agent { align-self: flex-start; }
.msg-bubble.user  { align-self: flex-end; flex-direction: row-reverse; }
.msg-agent-avatar {
    width: 30px; height: 30px; border-radius: 50%; background: #e8e8e8;
    flex-shrink: 0; display: flex; align-items: center; justify-content: center; overflow: hidden;
}
.msg-agent-avatar img { width: 20px; height: 20px; object-fit: contain; }
.msg-content {
    background: var(--chat-white); border-radius: 16px 16px 16px 4px;
    padding: 11px 14px; font-size: 13.5px; color: var(--chat-text); line-height: 1.5;
    box-shadow: 0 1px 4px rgba(0,0,0,0.07);
}
.msg-bubble.user .msg-content {
    background: var(--chat-btn); color: #fff;
    border-radius: 16px 16px 4px 16px;
}
.msg-time {
    font-size: 10px; color: var(--chat-sub); margin-top: 4px; padding: 0 4px;
}
.msg-bubble.user .msg-time { text-align: right; }
.msg-sender-name {
    font-size: 9px; color: var(--chat-sub); padding: 0 4px 2px;
    text-transform: uppercase; letter-spacing: .06em;
}

/* Action bubble */
.msg-action-card {
    background: #f0f0f0; border-radius: 12px; padding: 14px 16px; margin-top: 6px;
}
.msg-action-card p { font-size: 13px; color: var(--chat-text); line-height: 1.5; margin-bottom: 12px; }
.msg-action-btn {
    width: 100%; background: var(--chat-btn); color: #fff;
    border: none; border-radius: 8px; padding: 11px;
    font-size: 13px; font-weight: 600; transition: background .2s;
}
.msg-action-btn:hover { background: var(--chat-bg-dark); }

/* Typing indicator */
.typing-indicator {
    display: flex; align-items: center; gap: 4px; padding: 10px 14px;
    background: var(--chat-white); border-radius: 16px 16px 16px 4px;
    width: fit-content; box-shadow: 0 1px 4px rgba(0,0,0,0.07);
}
.typing-dot {
    width: 7px; height: 7px; border-radius: 50%; background: #bbb;
    animation: typingBounce 1.2s ease-in-out infinite;
}
.typing-dot:nth-child(2){animation-delay:.2s;}
.typing-dot:nth-child(3){animation-delay:.4s;}
@keyframes typingBounce{0%,60%,100%{transform:translateY(0);}30%{transform:translateY(-6px);}}

/* Closed notice */
.chat-closed-banner {
    padding: 12px 16px; background: #fff8e1; border-top: 1px solid #ffe082;
    font-size: 12px; color: #7c5700; text-align: center; flex-shrink: 0;
}
.chat-closed-new-btn {
    width: calc(100% - 24px); margin: 8px 12px 12px;
    background: var(--chat-btn); color: #fff; border: none;
    border-radius: 10px; padding: 12px; font-size: 13px; font-weight: 600;
    transition: background .2s;
}
.chat-closed-new-btn:hover { background: var(--chat-bg-dark); }

/* Input bar */
.chat-input-bar {
    display: flex; align-items: center; gap: 8px; padding: 10px 12px;
    border-top: 1px solid var(--chat-border); background: #fff; flex-shrink: 0;
}
.chat-textarea {
    flex: 1; border: 1px solid var(--chat-border); border-radius: 22px;
    padding: 9px 14px; font-size: 13px; color: var(--chat-text);
    outline: none; resize: none; font-family: inherit; line-height: 1.4;
    background: #fafafa; transition: border-color .2s; max-height: 80px;
}
.chat-textarea:focus { border-color: var(--chat-btn); background: #fff; }
.chat-plus-btn {
    width: 34px; height: 34px; background: #f0f0f0; border: none;
    border-radius: 50%; display: flex; align-items: center;
    justify-content: center; color: var(--chat-sub); font-size: 16px; flex-shrink: 0;
    transition: background .2s;
}
.chat-plus-btn:hover { background: #e4e4e4; }
.chat-send-btn {
    width: 36px; height: 36px; background: var(--chat-btn); border: none;
    border-radius: 50%; display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; transition: background .2s, transform .2s;
}
.chat-send-btn:hover  { background: var(--chat-bg-dark); transform: scale(1.08); }
.chat-send-btn:active { transform: scale(0.95); }
.chat-send-btn svg { width: 15px; height: 15px; fill: #fff; }

/* ══════════════════════════════════════
   SCREEN: CONTACT DETAILS
══════════════════════════════════════ */
#screen-contact .contact-body {
    flex: 1; padding: 32px 24px; overflow-y: auto;
}
#screen-contact h3 { font-size: 20px; font-weight: 700; color: var(--chat-text); margin-bottom: 6px; }
#screen-contact .contact-subtitle { font-size: 13px; color: var(--chat-sub); margin-bottom: 28px; line-height: 1.5; }
.contact-field { margin-bottom: 20px; }
.contact-field label { display: block; font-size: 13px; font-weight: 600; color: var(--chat-text); margin-bottom: 6px; }
.contact-field input {
    width: 100%; border: none; border-bottom: 1.5px solid var(--chat-border);
    padding: 8px 0; font-size: 14px; color: var(--chat-text);
    background: transparent; outline: none; font-family: inherit;
    transition: border-color .2s; box-sizing: border-box;
}
.contact-field input:focus { border-color: var(--chat-btn); }
.contact-field input::placeholder { color: #bbb; }
.contact-send-btn {
    float: right; background: var(--chat-btn); color: #fff;
    border: none; border-radius: 10px; padding: 12px 32px;
    font-size: 14px; font-weight: 600;
    margin-top: 16px; transition: background .2s;
}
.contact-send-btn:hover { background: var(--chat-bg-dark); }
.contact-send-btn:disabled { background: #bbb; }
.contact-success {
    text-align: center; padding: 40px 20px;
}
.contact-success svg { width: 52px; height: 52px; fill: #2dd4a0; margin-bottom: 14px; }
.contact-success h4 { font-size: 18px; font-weight: 700; color: var(--chat-text); margin-bottom: 8px; }
.contact-success p { font-size: 13px; color: var(--chat-sub); }

/* ── Inline Contact Card ── */
.inline-contact-card {
    background: #f3f4f6; border-radius: 12px; padding: 14px 16px;
    margin-top: 4px; max-width: 260px;
}
.icc-title {
    font-size: 13px; font-weight: 700; color: var(--chat-text); margin-bottom: 12px;
}
.icc-input {
    width: 100%; border: none; border-bottom: 1.5px solid #d1d5db;
    padding: 8px 0; font-size: 13px; color: var(--chat-text);
    background: transparent; outline: none; font-family: inherit;
    margin-bottom: 10px; box-sizing: border-box; display: block;
    transition: border-color .2s;
}
.icc-input:focus { border-color: var(--chat-btn); }
.icc-input::placeholder { color: #bbb; font-size: 12.5px; }
.icc-btn {
    width: 100%; background: var(--chat-btn); color: #fff;
    border: none; border-radius: 8px; padding: 10px;
    font-size: 13px; font-weight: 600; margin-top: 4px;
    transition: background .2s;
}
.icc-btn:hover { background: var(--chat-bg-dark); }
.icc-btn:disabled { background: #bbb; }

/* ── Mobile ── */
@media(max-width: 480px) {
    #chat-window {
        bottom: 0; left: 0; right: 0; width: 100%; height: 93vh;
        border-radius: var(--chat-radius) var(--chat-radius) 0 0;
    }
    #chat-toggle { bottom: 20px; left: 20px; }
}
</style>

<!-- ── Custom Cursor Elements ── -->
<div id="chat-cur-dot"></div>
<div id="chat-cur-ring"></div>

<!-- ══════════════════════════════════════
     TOOLTIP / BUBBLE NOTIFIKASI
══════════════════════════════════════ -->
<div id="chat-tooltip" role="dialog" aria-label="Customer Care">
    <button id="chat-tooltip-close" aria-label="Tutup notifikasi">✕</button>
    <div class="tt-avatar">
        <img src="/lending_word/public/assets/images/porsche-logo.png"
             onerror="this.src='https://upload.wikimedia.org/wikipedia/commons/thumb/5/54/Porsche_logo.svg/80px-Porsche_logo.svg.png'"
             alt="Porsche">
    </div>
    <div class="tt-body">
        <div class="tt-title">Customer Care</div>
        <div class="tt-text">Feel free to chat with us if you have any questions.</div>
    </div>
</div>

<!-- ── Toggle Button ── -->
<button id="chat-toggle" aria-label="Buka chat">
    <svg class="icon-chat" viewBox="0 0 24 24"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-3 11H7v-2h10v2zm0-3H7V8h10v2zm0-3H7V5h10v2z"/></svg>
    <svg class="icon-close" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
    <span id="chat-unread-badge"></span>
</button>

<!-- ── Chat Window ── -->
<div id="chat-window">

    <!-- ══ SCREEN: WELCOME ══ -->
    <div class="chat-screen active" id="screen-welcome">
        <div class="chat-header">
            <div class="chat-header-left">
                <div class="chat-avatar">
                    <img id="logo-welcome" src="/lending_word/public/assets/images/porsche-logo.png"
                         onerror="this.src='https://upload.wikimedia.org/wikipedia/commons/thumb/5/54/Porsche_logo.svg/80px-Porsche_logo.svg.png'" alt="Porsche">
                </div>
                <div>
                    <div class="chat-header-title">Porsche Indonesia</div>
                    <div class="chat-header-sub"><span class="status-dot"></span>Online</div>
                </div>
            </div>
            <div class="chat-header-right">
                <button class="chat-icon-btn js-minimize" aria-label="Tutup">
                    <i class="fas fa-chevron-down"></i>
                </button>
            </div>
        </div>
        <div class="welcome-body">
            <h2>Welcome to Porsche 👋</h2>
            <p>Feel free to chat with us if you need assistance. We are here to help with your Porsche journey.</p>

            <button class="privacy-link-btn js-goto-privacy">Privacy Policy</button>

            <!-- Conversation Preview Card -->
            <div class="conv-preview-card js-goto-conv-list">
                <div class="conv-preview-item">
                    <div class="conv-item-avatar">
                        <img src="/lending_word/public/assets/images/porsche-logo.png"
                             onerror="this.src='https://upload.wikimedia.org/wikipedia/commons/thumb/5/54/Porsche_logo.svg/80px-Porsche_logo.svg.png'" alt="">
                    </div>
                    <div class="conv-item-info">
                        <div class="conv-item-name">Customer Care</div>
                        <div class="conv-item-preview" id="welcome-preview-text">Hubungi kami untuk info lebih lanjut ...</div>
                    </div>
                    <div style="text-align:right;flex-shrink:0;">
                        <div class="conv-item-time" id="welcome-preview-time">Sekarang</div>
                        <div class="conv-item-ticks" id="welcome-preview-ticks">✓✓</div>
                    </div>
                </div>
                <div class="conv-preview-footer">
                    <span>Conversations <span id="welcome-conv-count" class="badge" style="display:none"></span></span>
                    <i class="fas fa-chevron-right" style="color:#ccc;font-size:11px;"></i>
                </div>
            </div>
        </div>
        <button class="welcome-new-btn js-start-new-welcome">Start new conversation</button>
    </div>

    <!-- ══ SCREEN: CONVERSATIONS LIST ══ -->
    <div class="chat-screen" id="screen-conversations">
        <div class="chat-header">
            <div class="chat-header-left">
                <button class="chat-icon-btn js-back-from-conv" aria-label="Kembali">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <div>
                    <div class="chat-header-title">Conversations</div>
                </div>
            </div>
            <div class="chat-header-right">
                <button class="chat-icon-btn" id="btn-conv-more" aria-label="Menu">
                    <i class="fas fa-ellipsis-h"></i>
                </button>
                <button class="chat-icon-btn js-minimize" aria-label="Tutup">
                    <i class="fas fa-chevron-down"></i>
                </button>
            </div>
        </div>
        <div class="conv-list-body" id="conv-list-body">
            <div class="conv-list-empty">
                <svg viewBox="0 0 24 24"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>
                <div>Belum ada percakapan</div>
            </div>
        </div>
        <button class="conv-list-new-btn js-start-new-conv">Start new conversation</button>
    </div>

    <!-- ══ SCREEN: PRIVACY ══ -->
    <div class="chat-screen" id="screen-privacy">
        <div class="chat-header">
            <div class="chat-header-left">
                <button class="chat-icon-btn js-back-from-privacy" aria-label="Kembali">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <div class="chat-header-title">Privacy notice</div>
            </div>
            <div class="chat-header-right">
                <button class="chat-icon-btn js-minimize" aria-label="Tutup">
                    <i class="fas fa-chevron-down"></i>
                </button>
            </div>
        </div>
        <div class="privacy-body">
            <h3>Privacy notice</h3>
            <p>Please accept to start your conversation.</p>
            <p>This website uses cookies to provide you with a great user experience. Please be assured that your data is safe and will not be used to identify you personally. Details can be found in our Privacy Policy.</p>
            <button class="privacy-accept-btn js-accept-privacy">Accept</button>
        </div>
    </div>

    <!-- ══ SCREEN: CHAT ══ -->
    <div class="chat-screen" id="screen-chat">
        <div class="chat-header">
            <div class="chat-header-left">
                <button class="chat-icon-btn js-back-from-chat" aria-label="Kembali">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <div class="chat-avatar">
                    <img src="/lending_word/public/assets/images/porsche-logo.png"
                         onerror="this.src='https://upload.wikimedia.org/wikipedia/commons/thumb/5/54/Porsche_logo.svg/80px-Porsche_logo.svg.png'" alt="">
                </div>
                <div>
                    <div class="chat-header-title">Customer Care</div>
                    <div class="chat-header-sub" id="chat-status-sub">We will reply as soon as possible.</div>
                </div>
            </div>
            <div class="chat-header-right">
                <button class="chat-icon-btn js-minimize" aria-label="Tutup">
                    <i class="fas fa-chevron-down"></i>
                </button>
            </div>
        </div>

        <div class="chat-messages-area" id="chat-messages-area"></div>

        <div class="chat-input-bar" id="chat-input-bar">
            <button class="chat-plus-btn" id="btn-plus" aria-label="Lampiran">+</button>
            <textarea class="chat-textarea" id="chat-input" rows="1" placeholder="Your message"></textarea>
            <button class="chat-send-btn" id="btn-send" aria-label="Kirim">
                <svg viewBox="0 0 24 24"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
            </button>
        </div>
    </div>

    <!-- ══ SCREEN: CONTACT DETAILS ══ -->
    <div class="chat-screen" id="screen-contact">
        <div class="chat-header">
            <div class="chat-header-left">
                <button class="chat-icon-btn js-back-from-contact" aria-label="Kembali">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <div class="chat-header-title">Contact details</div>
            </div>
            <div class="chat-header-right">
                <button class="chat-icon-btn js-minimize" aria-label="Tutup">
                    <i class="fas fa-chevron-down"></i>
                </button>
            </div>
        </div>
        <div class="contact-body">
            <h3>Porsche Support Team</h3>
            <p class="contact-subtitle">Please share your contact details and we will contact you as soon as possible.</p>
            <div class="contact-field">
                <label for="contact-name">Name</label>
                <input type="text" id="contact-name" placeholder="Enter your name" autocomplete="name">
            </div>
            <div class="contact-field">
                <label for="contact-email">Email</label>
                <input type="email" id="contact-email" placeholder="Enter your email address" autocomplete="email">
            </div>
            <div class="contact-field">
                <label for="contact-phone">Phone <span style="font-weight:400;color:#bbb;">(optional)</span></label>
                <input type="tel" id="contact-phone" placeholder="Enter your phone number" autocomplete="tel">
            </div>
            <button class="contact-send-btn" id="btn-contact-send">Send</button>
        </div>
    </div>

</div><!-- #chat-window -->


<script>
(function () {
'use strict';

/* ═══════════════════════════════════════════════
   CONFIG
═══════════════════════════════════════════════ */
const VISITOR_API = '/lending_word/admin/chat-api.php';
const LOGO        = '/lending_word/public/assets/images/porsche-logo.png';
const LOGO_FB     = 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/54/Porsche_logo.svg/80px-Porsche_logo.svg.png';
const POLL_MS     = 3000;

/* ═══════════════════════════════════════════════
   TOOLTIP CONFIG — ubah sesuai kebutuhan
═══════════════════════════════════════════════ */
const TOOLTIP_DELAY_MS   = 4000;   // Delay sebelum bubble muncul (ms)
const TOOLTIP_AUTOHIDE_MS = 0;     // 0 = tidak auto-hide; isi angka ms jika mau auto-hide
const TOOLTIP_SESSION_KEY = 'porsche_tooltip_dismissed';

/* ═══════════════════════════════════════════════
   STATE
═══════════════════════════════════════════════ */
let sessionId        = null;
let lastMsgId        = 0;
let pollTimer        = null;
let privacyAccepted  = localStorage.getItem('porsche_privacy') === '1';
let currentScreen    = 'welcome';
let cachedMessages   = [];
let allSessions      = [];
let activeSessionId  = null;
let pendingNewSession = false;
let tokenHistory     = JSON.parse(localStorage.getItem('porsche_tokens') || '[]');

/* ═══════════════════════════════════════════════
   ELEMENTS
═══════════════════════════════════════════════ */
const toggle     = document.getElementById('chat-toggle');
const chatWindow = document.getElementById('chat-window');
const badge      = document.getElementById('chat-unread-badge');
const tooltip    = document.getElementById('chat-tooltip');
const tooltipClose = document.getElementById('chat-tooltip-close');
const screens    = {
    welcome       : document.getElementById('screen-welcome'),
    conversations : document.getElementById('screen-conversations'),
    privacy       : document.getElementById('screen-privacy'),
    chat          : document.getElementById('screen-chat'),
    contact       : document.getElementById('screen-contact'),
};

/* ═══════════════════════════════════════════════
   TOOLTIP LOGIC
═══════════════════════════════════════════════ */
(function initTooltip() {
    if (!tooltip || !tooltipClose) return;

    // Jangan tampilkan lagi jika sudah pernah di-dismiss di sesi ini
    const dismissed = sessionStorage.getItem(TOOLTIP_SESSION_KEY);
    if (dismissed) return;

    let autoHideTimer = null;

    function showTooltip() {
        tooltip.classList.add('show');
        if (TOOLTIP_AUTOHIDE_MS > 0) {
            autoHideTimer = setTimeout(hideTooltip, TOOLTIP_AUTOHIDE_MS);
        }
    }

    function hideTooltip() {
        clearTimeout(autoHideTimer);
        tooltip.classList.remove('show');
        tooltip.classList.add('hidden');
        sessionStorage.setItem(TOOLTIP_SESSION_KEY, '1');
    }

    // Klik tombol × untuk tutup
    tooltipClose.addEventListener('click', function(e) {
        e.stopPropagation();
        hideTooltip();
    });

    // Klik di area tooltip untuk langsung buka chat
    tooltip.addEventListener('click', function(e) {
        if (e.target === tooltipClose) return;
        hideTooltip();
        openWidget();
    });

    // Tampilkan setelah toggle button terlihat + delay
    function tryShowTooltip() {
        if (!toggle.classList.contains('chat-visible')) return;
        if (chatWindow.classList.contains('open')) return;
        if (sessionStorage.getItem(TOOLTIP_SESSION_KEY)) return;
        setTimeout(showTooltip, TOOLTIP_DELAY_MS);
    }

    // Observer: tunggu toggle jadi visible
    const obs = new MutationObserver(function(mutations) {
        mutations.forEach(function(m) {
            if (m.type === 'attributes' && m.attributeName === 'class') {
                if (toggle.classList.contains('chat-visible')) {
                    obs.disconnect();
                    tryShowTooltip();
                }
            }
        });
    });
    obs.observe(toggle, { attributes: true });

    // Sembunyikan tooltip saat chat dibuka
    chatWindow.addEventListener('transitionend', function() {
        if (chatWindow.classList.contains('open')) {
            tooltip.classList.remove('show');
            tooltip.classList.add('hidden');
        }
    });
})();

/* ═══════════════════════════════════════════════
   CUSTOM CURSOR — identik dengan model-detail.php
   mix-blend-mode: difference + lerp ring + state classes
═══════════════════════════════════════════════ */
(function initChatCursor() {
    const cDot  = document.getElementById('chat-cur-dot');
    const cRing = document.getElementById('chat-cur-ring');
    if (!cDot || !cRing) return;

    let cmx = 0, cmy = 0;
    let crx = 0, cry = 0;
    let curActive = false;
    let raf = null;

    document.addEventListener('mousemove', function(e) {
        cmx = e.clientX;
        cmy = e.clientY;
    }, { passive: true });

    function tick() {
        crx += (cmx - crx) * 0.16;
        cry += (cmy - cry) * 0.16;
        cDot.style.left  = cmx + 'px';
        cDot.style.top   = cmy + 'px';
        cRing.style.left = crx + 'px';
        cRing.style.top  = cry + 'px';
        raf = requestAnimationFrame(tick);
    }

    function activate() {
        if (curActive) return;
        curActive = true;
        cDot.classList.add('chat-cur-on');
        cRing.classList.add('chat-cur-on');
        if (!raf) tick();
    }

    function deactivate() {
        curActive = false;
        cDot.classList.remove('chat-cur-on');
        cRing.classList.remove('chat-cur-on');
        document.body.classList.remove('chat-c-link', 'chat-c-card', 'chat-c-click');
        if (raf) { cancelAnimationFrame(raf); raf = null; }
    }

    chatWindow.addEventListener('mouseenter', activate);
    chatWindow.addEventListener('mouseleave', deactivate);
    toggle.addEventListener('mouseenter', activate);
    toggle.addEventListener('mouseleave', function() {
        if (!chatWindow.matches(':hover')) deactivate();
    });

    [chatWindow, toggle].forEach(function(el) {
        el.addEventListener('mousedown', function() {
            document.body.classList.add('chat-c-click');
            setTimeout(function() { document.body.classList.remove('chat-c-click'); }, 280);
        });
    });

    var LINK_SEL = 'a, button, input, textarea, label, select, [role="button"]';
    var CARD_SEL = '.conv-preview-card, .conv-list-item, .msg-bubble';

    function bindLinkHover(el) {
        if (el.dataset.chl) return;
        el.dataset.chl = '1';
        el.addEventListener('mouseenter', function() {
            document.body.classList.add('chat-c-link');
            document.body.classList.remove('chat-c-card');
        });
        el.addEventListener('mouseleave', function() {
            document.body.classList.remove('chat-c-link');
        });
    }

    function bindCardHover(el) {
        if (el.dataset.chc) return;
        el.dataset.chc = '1';
        el.addEventListener('mouseenter', function() {
            document.body.classList.remove('chat-c-link');
            document.body.classList.add('chat-c-card');
        });
        el.addEventListener('mouseleave', function() {
            document.body.classList.remove('chat-c-card');
        });
    }

    function bindAll(root) {
        root.querySelectorAll(LINK_SEL).forEach(bindLinkHover);
        root.querySelectorAll(CARD_SEL).forEach(bindCardHover);
    }

    bindAll(chatWindow);
    bindLinkHover(toggle);

    new MutationObserver(function(mutations) {
        mutations.forEach(function(m) {
            m.addedNodes.forEach(function(node) {
                if (node.nodeType !== 1) return;
                bindAll(node);
                if (node.matches && node.matches(LINK_SEL)) bindLinkHover(node);
                if (node.matches && node.matches(CARD_SEL)) bindCardHover(node);
            });
        });
    }).observe(chatWindow, { childList: true, subtree: true });

    document.documentElement.addEventListener('mouseleave', function() {
        if (curActive) deactivate();
    });
})();

/* ═══════════════════════════════════════════════
   VISIBILITY — tampil setelah lewat hero
═══════════════════════════════════════════════ */
(function initVisibility() {
    let done = false;
    function tryShow() {
        if (done) return;
        const hero = document.getElementById('hero');
        if (!hero || hero.getBoundingClientRect().bottom < 0) {
            done = true;
            toggle.classList.add('chat-visible');
        }
    }
    setTimeout(() => {
        tryShow();
        window.addEventListener('scroll', tryShow, { passive: true });
    }, 3000);
})();

/* ═══════════════════════════════════════════════
   TOGGLE WIDGET
═══════════════════════════════════════════════ */
toggle.addEventListener('click', () => {
    const isOpen = chatWindow.classList.contains('open');
    if (isOpen) {
        closeWidget();
    } else {
        openWidget();
    }
});

function openWidget() {
    chatWindow.classList.add('open');
    toggle.classList.add('open');
    badge.classList.remove('show');
    // Sembunyikan tooltip saat chat dibuka
    if (tooltip) {
        tooltip.classList.remove('show');
        tooltip.classList.add('hidden');
    }
    if (privacyAccepted && !sessionId) bgInit();
}

function closeWidget() {
    chatWindow.classList.remove('open');
    toggle.classList.remove('open');
}

/* ═══════════════════════════════════════════════
   SCREEN NAVIGATION
═══════════════════════════════════════════════ */
function goTo(name, dir = 'forward') {
    if (currentScreen === name) return;
    const prev = screens[currentScreen];
    const next = screens[name];
    if (!prev || !next) return;

    prev.classList.remove('active');
    prev.style.transition = 'opacity .25s ease, transform .28s cubic-bezier(0.16,1,0.3,1)';
    prev.style.transform  = dir === 'back' ? 'translateX(40px)' : 'translateX(-40px)';
    prev.style.opacity    = '0';

    next.style.transition = '';
    next.style.transform  = dir === 'back' ? 'translateX(-40px)' : 'translateX(40px)';
    next.style.opacity    = '0';
    next.classList.add('active');

    requestAnimationFrame(() => requestAnimationFrame(() => {
        next.style.transition = 'opacity .3s ease, transform .35s cubic-bezier(0.16,1,0.3,1)';
        next.style.transform  = 'translateX(0)';
        next.style.opacity    = '1';
        setTimeout(() => {
            prev.style.cssText = '';
            next.style.cssText = '';
        }, 380);
    }));

    currentScreen = name;
}

/* ═══════════════════════════════════════════════
   BUTTON BINDINGS
═══════════════════════════════════════════════ */
document.querySelectorAll('.js-minimize').forEach(el =>
    el.addEventListener('click', closeWidget)
);

document.querySelector('.js-back-from-conv')?.addEventListener('click', () => goTo('welcome', 'back'));
document.querySelector('.js-back-from-privacy')?.addEventListener('click', () => goTo('welcome', 'back'));
document.querySelector('.js-back-from-chat')?.addEventListener('click', () => goTo('conversations', 'back'));
document.querySelector('.js-back-from-contact')?.addEventListener('click', () => goTo('chat', 'back'));

document.querySelector('.js-goto-privacy')?.addEventListener('click', () => {
    pendingNewSession = false;
    goTo('privacy');
});

document.querySelector('.js-accept-privacy')?.addEventListener('click', () => {
    privacyAccepted = true;
    localStorage.setItem('porsche_privacy', '1');
    if (pendingNewSession) {
        pendingNewSession = false;
        startFreshSession();
    } else {
        openExistingOrNew();
    }
});

document.querySelector('.js-goto-conv-list')?.addEventListener('click', () => {
    goTo('conversations');
    renderConvList();
});

document.querySelector('.js-start-new-welcome')?.addEventListener('click', () => {
    if (!privacyAccepted) { pendingNewSession = true; goTo('privacy'); }
    else startFreshSession();
});

document.querySelector('.js-start-new-conv')?.addEventListener('click', () => {
    if (!privacyAccepted) { pendingNewSession = true; goTo('privacy'); }
    else startFreshSession();
});

document.getElementById('btn-send')?.addEventListener('click', sendMessage);
document.getElementById('chat-input')?.addEventListener('keydown', e => {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
});
document.getElementById('chat-input')?.addEventListener('input', autoResizeTextarea);
document.getElementById('btn-plus')?.addEventListener('click', () => goTo('contact'));
document.getElementById('btn-contact-send')?.addEventListener('click', submitContact);

/* ═══════════════════════════════════════════════
   API HELPERS
═══════════════════════════════════════════════ */
async function api(action, body = null) {
    const opts = { method: body ? 'POST' : 'GET', headers: {} };
    if (body) {
        opts.headers['Content-Type'] = 'application/json';
        opts.body = JSON.stringify(body);
    }
    const [act, ...qparts] = action.split('&');
    let url = VISITOR_API + '?action=' + act;
    if (qparts.length) url += '&' + qparts.join('&');
    const res  = await fetch(url, opts);
    const text = await res.text();
    try {
        return JSON.parse(text);
    } catch(e) {
        console.error('[Chat API parse error]', text.substring(0, 200));
        return { ok: false, error: 'Server response error' };
    }
}

/* ═══════════════════════════════════════════════
   BACKGROUND INIT
═══════════════════════════════════════════════ */
async function bgInit() {
    try {
        const data = await api('init');
        if (!data.ok) return;
        sessionId      = data.session_id;
        activeSessionId = data.session_id;
        lastMsgId      = data.last_id || 0;
        cachedMessages = data.messages || [];
        if (data.token) saveToken(data.token);
        updateWelcomePreview();
        checkUnreadBadge(data.messages || []);
        startPolling();
    } catch(e) {
        console.error('[bgInit]', e);
    }
}

/* ═══════════════════════════════════════════════
   OPEN EXISTING CHAT
═══════════════════════════════════════════════ */
async function openSessionInChat(sid) {
    stopPolling();
    activeSessionId = sid;
    sessionId       = sid;
    lastMsgId       = 0;
    cachedMessages  = [];

    goTo('chat');
    const area = document.getElementById('chat-messages-area');
    area.innerHTML = '';
    showTyping();

    try {
        const data = await api('init');
        if (!data.ok) { hideTyping(); return; }
        sessionId      = data.session_id;
        lastMsgId      = data.last_id || 0;
        cachedMessages = data.messages || [];
        hideTyping();
        area.innerHTML = '';
        if (cachedMessages.length) cachedMessages.forEach(m => appendMsg(m));
        else appendWelcomeMsg();
        updateChatStatus(data.status);
        startPolling();
    } catch(e) {
        hideTyping();
        console.error('[openSessionInChat]', e);
    }
}

/* ═══════════════════════════════════════════════
   OPEN EXISTING OR NEW
═══════════════════════════════════════════════ */
async function openExistingOrNew() {
    goTo('chat');
    if (!sessionId) {
        const area = document.getElementById('chat-messages-area');
        area.innerHTML = '';
        showTyping();
        await bgInit();
        hideTyping();
        if (cachedMessages.length) cachedMessages.forEach(m => appendMsg(m));
        else appendWelcomeMsg();
    } else {
        const area = document.getElementById('chat-messages-area');
        area.innerHTML = '';
        cachedMessages.forEach(m => appendMsg(m));
        if (!cachedMessages.length) appendWelcomeMsg();
        startPolling();
    }
}

/* ═══════════════════════════════════════════════
   START FRESH SESSION
═══════════════════════════════════════════════ */
async function startFreshSession() {
    stopPolling();
    sessionId       = null;
    activeSessionId = null;
    lastMsgId       = 0;
    cachedMessages  = [];

    goTo('chat');
    const area = document.getElementById('chat-messages-area');
    area.innerHTML = '';
    restoreInputBar();
    showTyping();

    try {
        const data = await api('new_session');
        if (!data.ok) {
            hideTyping();
            appendBotMsg('Gagal membuat sesi baru. Silakan coba lagi.');
            return;
        }
        sessionId       = data.session_id;
        activeSessionId = data.session_id;
        lastMsgId       = data.last_id || 0;
        cachedMessages  = data.messages || [];
        if (data.token) saveToken(data.token);
        hideTyping();
        if (cachedMessages.length) cachedMessages.forEach(m => appendMsg(m));
        else appendWelcomeMsg();
        if (data.is_new && !data.visitor_name) appendInlineContactForm();
        updateChatStatus(data.status);
        startPolling();
    } catch(e) {
        hideTyping();
        console.error('[startFreshSession]', e);
        appendBotMsg('Terjadi kesalahan. Silakan coba lagi.<br><small style="color:rgba(255,255,255,0.7)">Pastikan koneksi internet Anda stabil.</small>');
    }
}

/* ═══════════════════════════════════════════════
   POLLING
═══════════════════════════════════════════════ */
function startPolling() {
    stopPolling();
    pollTimer = setInterval(async () => {
        if (!sessionId) return;
        try {
            const data = await api(`poll&after_id=${lastMsgId}`);
            if (!data.ok) return;
            if (data.messages && data.messages.length) {
                data.messages.forEach(m => {
                    if (m.id > lastMsgId) {
                        cachedMessages.push(m);
                        if (currentScreen === 'chat' && chatWindow.classList.contains('open')) appendMsg(m);
                        else showBadge(1);
                        lastMsgId = m.id;
                    }
                });
                if (data.last_id > lastMsgId) lastMsgId = data.last_id;
                updateWelcomePreview();
            }
            updateChatStatus(data.status);
        } catch(e) { /* silent */ }
    }, POLL_MS);
}

function stopPolling() { clearInterval(pollTimer); pollTimer = null; }

/* ═══════════════════════════════════════════════
   SEND MESSAGE
═══════════════════════════════════════════════ */
async function sendMessage() {
    const input = document.getElementById('chat-input');
    const text  = input?.value.trim();
    if (!text || !sessionId) return;

    input.value = '';
    input.style.height = 'auto';

    const fake = { id: 'tmp_' + Date.now(), sender_type: 'visitor', sender_name: 'Saya', message: text, sent_at: new Date().toISOString() };
    appendMsg(fake);

    try {
        const data = await api('send', { message: text });
        if (data.ok && data.message) {
            const tmpEl = document.querySelector(`.msg-bubble[data-id="${fake.id}"]`);
            if (tmpEl) tmpEl.dataset.id = data.message.id;
            if (data.message.id > lastMsgId) lastMsgId = data.message.id;
            const idx = cachedMessages.findIndex(m => m.id === fake.id);
            if (idx >= 0) cachedMessages[idx] = data.message;
            else cachedMessages.push(data.message);
        }
    } catch(e) {
        console.error('[sendMessage]', e);
    }
}

/* ═══════════════════════════════════════════════
   CONTACT FORM SUBMIT
═══════════════════════════════════════════════ */
async function submitContact() {
    const name  = document.getElementById('contact-name')?.value.trim();
    const email = document.getElementById('contact-email')?.value.trim();
    const phone = document.getElementById('contact-phone')?.value.trim();

    if (!name || !email) { alert('Nama dan email wajib diisi.'); return; }

    const btn = document.getElementById('btn-contact-send');
    btn.disabled = true;
    btn.textContent = 'Mengirim...';

    try {
        const data = await api('contact', { name, email, phone });
        if (data.ok) {
            document.querySelector('#screen-contact .contact-body').innerHTML = `
                <div class="contact-success">
                    <svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/></svg>
                    <h4>Terima kasih!</h4>
                    <p>Tim kami akan menghubungi Anda segera.</p>
                </div>`;
            setTimeout(() => goTo('chat', 'back'), 2200);
        }
    } catch(e) {
        btn.disabled = false;
        btn.textContent = 'Send';
        console.error('[submitContact]', e);
    }
}

/* ═══════════════════════════════════════════════
   TOKEN HISTORY
═══════════════════════════════════════════════ */
function saveToken(token) {
    if (!token || !/^[a-f0-9]{48}$/.test(token)) return;
    tokenHistory = tokenHistory.filter(t => t !== token);
    tokenHistory.unshift(token);
    tokenHistory = tokenHistory.slice(0, 20);
    localStorage.setItem('porsche_tokens', JSON.stringify(tokenHistory));
}

/* ═══════════════════════════════════════════════
   RENDER: CONVERSATIONS LIST
═══════════════════════════════════════════════ */
async function renderConvList() {
    const body = document.getElementById('conv-list-body');
    if (!body) return;

    if (!tokenHistory.length) {
        body.innerHTML = `<div class="conv-list-empty">
            <svg viewBox="0 0 24 24"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>
            <div>Belum ada percakapan</div></div>`;
        return;
    }

    body.innerHTML = '<div style="padding:20px;text-align:center;color:#bbb;font-size:13px;">Memuat...</div>';

    try {
        const data = await api('sessions_by_tokens', { tokens: tokenHistory });
        if (!data.ok || !data.sessions.length) {
            body.innerHTML = `<div class="conv-list-empty">
                <svg viewBox="0 0 24 24"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>
                <div>Belum ada percakapan</div></div>`;
            return;
        }

        body.innerHTML = data.sessions.map(s => {
            const preview   = (s.last_message || 'Mulai percakapan...').substring(0, 55);
            const timeStr   = s.last_message_at ? formatTime(s.last_message_at) : '';
            const hasUnread = parseInt(s.unread_count) > 0;
            return `<div class="conv-list-item${hasUnread ? ' unread' : ''}" data-token="${esc(s.session_token)}">
                <div class="conv-list-avatar">
                    <img src="${LOGO}" onerror="this.src='${LOGO_FB}'" alt="">
                </div>
                <div class="conv-list-info">
                    <div class="conv-list-name">Customer Care</div>
                    <div class="conv-list-preview">${esc(preview)}</div>
                </div>
                <div class="conv-list-meta">
                    <div class="conv-list-time">${timeStr}</div>
                    <div class="conv-list-dot"></div>
                </div>
            </div>`;
        }).join('');

        body.querySelectorAll('.conv-list-item').forEach(item => {
            item.addEventListener('click', () => openSessionByToken(item.dataset.token));
        });
    } catch(e) {
        console.error('[renderConvList]', e);
    }
}

/* ── Buka sesi berdasarkan token ── */
async function openSessionByToken(token) {
    stopPolling();
    cachedMessages = [];
    lastMsgId = 0;

    goTo('chat');
    const area = document.getElementById('chat-messages-area');
    area.innerHTML = '';
    restoreInputBar();
    showTyping();

    try {
        const data = await api('switch_session', { token });
        if (!data.ok) { hideTyping(); appendBotMsg('Gagal memuat percakapan.'); return; }
        sessionId       = data.session_id;
        activeSessionId = data.session_id;
        lastMsgId       = data.last_id || 0;
        cachedMessages  = data.messages || [];
        saveToken(token);
        hideTyping();
        area.innerHTML = '';
        cachedMessages.forEach(m => appendMsg(m));
        updateChatStatus(data.status);
        startPolling();
    } catch(e) {
        hideTyping();
        console.error('[openSessionByToken]', e);
    }
}

/* ═══════════════════════════════════════════════
   UPDATE WELCOME PREVIEW
═══════════════════════════════════════════════ */
function updateWelcomePreview() {
    if (!cachedMessages.length) return;
    const last      = cachedMessages[cachedMessages.length - 1];
    const previewEl = document.getElementById('welcome-preview-text');
    const timeEl    = document.getElementById('welcome-preview-time');
    const countEl   = document.getElementById('welcome-conv-count');
    if (previewEl) previewEl.textContent = last.message.substring(0, 55);
    if (timeEl)    timeEl.textContent    = formatTime(last.sent_at);
    if (countEl) { countEl.textContent = '1'; countEl.style.display = 'inline-flex'; }
}

/* ═══════════════════════════════════════════════
   CHAT STATUS UI
═══════════════════════════════════════════════ */
function updateChatStatus(status) {
    const sub = document.getElementById('chat-status-sub');
    const bar = document.getElementById('chat-input-bar');

    if (status === 'closed') {
        if (sub) sub.innerHTML = '❌ Sesi ditutup';
        if (bar) {
            bar.innerHTML = `
                <div style="width:100%;padding:10px 12px 12px;">
                    <div class="chat-closed-banner" style="margin:0 0 8px;border-radius:8px;">Sesi ini sudah ditutup oleh admin.</div>
                    <button class="chat-closed-new-btn js-closed-new">💬 Mulai Percakapan Baru</button>
                </div>`;
            bar.querySelector('.js-closed-new')?.addEventListener('click', startFreshSession);
        }
        stopPolling();
    } else {
        if (sub) sub.innerHTML = 'We will reply as soon as possible.';
        restoreInputBar();
    }
}

function restoreInputBar() {
    const bar = document.getElementById('chat-input-bar');
    if (!bar || bar.querySelector('#chat-input')) return;
    bar.innerHTML = `
        <button class="chat-plus-btn" id="btn-plus" aria-label="Lampiran">+</button>
        <textarea class="chat-textarea" id="chat-input" rows="1" placeholder="Your message"></textarea>
        <button class="chat-send-btn" id="btn-send" aria-label="Kirim">
            <svg viewBox="0 0 24 24"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
        </button>`;
    bar.querySelector('#btn-send')?.addEventListener('click', sendMessage);
    bar.querySelector('#chat-input')?.addEventListener('keydown', e => {
        if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
    });
    bar.querySelector('#chat-input')?.addEventListener('input', autoResizeTextarea);
    bar.querySelector('#btn-plus')?.addEventListener('click', () => goTo('contact'));
}

/* ═══════════════════════════════════════════════
   RENDER: MESSAGES
═══════════════════════════════════════════════ */
function appendMsg(m) {
    const area    = document.getElementById('chat-messages-area');
    if (!area) return;
    const isAgent = m.sender_type === 'admin';

    if (m.id && document.querySelector(`.msg-bubble[data-id="${m.id}"]`)) return;

    const wrap = document.createElement('div');
    wrap.className  = 'msg-bubble ' + (isAgent ? 'agent' : 'user');
    wrap.dataset.id = m.id || 'tmp_' + Date.now();

    const time       = formatTime(m.sent_at);
    const avatarHtml = isAgent ? `<div class="msg-agent-avatar"><img src="${LOGO}" onerror="this.src='${LOGO_FB}'" alt=""></div>` : '';

    wrap.innerHTML = `${avatarHtml}<div>
        <div class="msg-content">${esc(m.message).replace(/\n/g,'<br>')}</div>
        <div class="msg-time">${time}</div>
    </div>`;

    wrap.style.opacity   = '0';
    wrap.style.transform = isAgent ? 'translateX(-10px)' : 'translateX(10px)';
    area.appendChild(wrap);

    requestAnimationFrame(() => requestAnimationFrame(() => {
        wrap.style.transition = 'opacity .28s ease, transform .32s cubic-bezier(0.16,1,0.3,1)';
        wrap.style.opacity    = '1';
        wrap.style.transform  = 'translateX(0)';
    }));

    area.scrollTop = area.scrollHeight;
}

function appendBotMsg(html) {
    const area = document.getElementById('chat-messages-area');
    if (!area) return;
    const wrap = document.createElement('div');
    wrap.className = 'msg-bubble agent';
    wrap.innerHTML = `
        <div class="msg-agent-avatar"><img src="${LOGO}" onerror="this.src='${LOGO_FB}'" alt=""></div>
        <div>
            <div class="msg-content">${html}</div>
            <div class="msg-time">${formatTime(new Date().toISOString())}</div>
        </div>`;
    area.appendChild(wrap);
    area.scrollTop = area.scrollHeight;
}

function appendWelcomeMsg() {
    const area = document.getElementById('chat-messages-area');
    if (!area) return;
    const greet = document.createElement('div');
    greet.className = 'msg-bubble agent';
    greet.innerHTML = `
        <div class="msg-agent-avatar"><img src="${LOGO}" onerror="this.src='${LOGO_FB}'" alt=""></div>
        <div>
            <div class="msg-content">Thanks for reaching out to us. We'll be right with you. Alternatively, leave us your contact number and our sales consultants will reach out to you directly.</div>
            <div class="msg-time">${formatTime(new Date().toISOString())}</div>
        </div>`;
    area.appendChild(greet);
    area.scrollTop = area.scrollHeight;
}

function appendInlineContactForm() {
    const area = document.getElementById('chat-messages-area');
    if (!area || document.getElementById('inline-contact-card')) return;

    const card = document.createElement('div');
    card.className = 'msg-bubble agent';
    card.id = 'inline-contact-card';
    card.innerHTML = `
        <div class="msg-agent-avatar"><img src="${LOGO}" onerror="this.src='${LOGO_FB}'" alt=""></div>
        <div style="flex:1;">
            <div class="inline-contact-card">
                <div class="icc-title">Leave contact details</div>
                <input class="icc-input" id="icc-name"  type="text"  placeholder="Name"  autocomplete="name">
                <input class="icc-input" id="icc-email" type="email" placeholder="Email" autocomplete="email">
                <input class="icc-input" id="icc-phone" type="tel"   placeholder="Phone (optional)" autocomplete="tel">
                <button class="icc-btn" id="icc-send">Send</button>
            </div>
            <div class="msg-time">${formatTime(new Date().toISOString())}</div>
        </div>`;
    area.appendChild(card);
    area.scrollTop = area.scrollHeight;
    card.querySelector('#icc-send')?.addEventListener('click', submitInlineContact);
}

async function submitInlineContact() {
    const name  = document.getElementById('icc-name')?.value.trim();
    const email = document.getElementById('icc-email')?.value.trim();
    const phone = document.getElementById('icc-phone')?.value.trim();

    if (!name)  { const el = document.getElementById('icc-name');  if(el){el.style.borderColor='#e53e3e';el.focus();} return; }
    if (!email) { const el = document.getElementById('icc-email'); if(el){el.style.borderColor='#e53e3e';el.focus();} return; }

    const btn = document.getElementById('icc-send');
    if (btn) { btn.disabled = true; btn.textContent = 'Sending...'; }

    try {
        const data = await api('contact', { name, email, phone });
        if (data.ok) {
            const card = document.getElementById('inline-contact-card');
            const icc  = card?.querySelector('.inline-contact-card');
            if (icc) icc.innerHTML = `
                <div style="text-align:center;padding:10px 0;">
                    <div style="font-size:22px;margin-bottom:6px;">✅</div>
                    <div style="font-weight:600;font-size:13px;color:#1a1a1a;margin-bottom:4px;">Terima kasih, ${esc(name)}!</div>
                    <div style="font-size:12px;color:#888;">Tim kami akan menghubungi Anda segera.</div>
                </div>`;
        } else {
            if (btn) { btn.disabled = false; btn.textContent = 'Send'; }
        }
    } catch(e) {
        if (btn) { btn.disabled = false; btn.textContent = 'Send'; }
        console.error('[submitInlineContact]', e);
    }
}

function showTyping() {
    const area = document.getElementById('chat-messages-area');
    if (!area) return;
    const wrap = document.createElement('div');
    wrap.className = 'msg-bubble agent';
    wrap.id = 'typing-indicator';
    wrap.innerHTML = `
        <div class="msg-agent-avatar"><img src="${LOGO}" onerror="this.src='${LOGO_FB}'" alt=""></div>
        <div class="typing-indicator">
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
        </div>`;
    area.appendChild(wrap);
    area.scrollTop = area.scrollHeight;
}

function hideTyping() {
    document.getElementById('typing-indicator')?.remove();
}

/* ═══════════════════════════════════════════════
   BADGE
═══════════════════════════════════════════════ */
function showBadge(count) {
    badge.textContent = count > 9 ? '9+' : count;
    badge.classList.add('show');
}

function checkUnreadBadge(messages) {
    const n = messages.filter(m => m.sender_type === 'admin' && m.is_read == 0).length;
    if (n > 0) showBadge(n);
}

/* ═══════════════════════════════════════════════
   UTILS
═══════════════════════════════════════════════ */
function autoResizeTextarea() {
    const ta = document.getElementById('chat-input');
    if (!ta) return;
    ta.style.height = 'auto';
    ta.style.height = Math.min(ta.scrollHeight, 80) + 'px';
}

function formatTime(iso) {
    if (!iso) return '';
    const d   = new Date(iso);
    const now  = new Date();
    const isToday     = d.toDateString() === now.toDateString();
    const yesterday   = new Date(now); yesterday.setDate(now.getDate() - 1);
    const isYesterday = d.toDateString() === yesterday.toDateString();
    if (isToday)     return d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
    if (isYesterday) return 'yesterday';
    return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'numeric', year: 'numeric' });
}

function esc(s) {
    return String(s)
        .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
        .replace(/"/g,'&quot;').replace(/'/g,'&#39;');
}

/* ═══════════════════════════════════════════════
   AUTO BACKGROUND INIT
═══════════════════════════════════════════════ */
if (privacyAccepted) bgInit();

})();
</script>