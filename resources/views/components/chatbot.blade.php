{{-- resources/views/components/chatbot.blade.php --}}
{{-- Include di layouts/app.blade.php sebelum </body> --}}

<style>
    /* ── Chatbot Variables ─────────────────────────────────────── */
    :root {
        --chat-primary: #1a4ff5;
        --chat-primary-d: #1340e1;
        --chat-accent: #0ff4c6;
        --chat-bubble-w: 380px;
        --chat-bubble-h: 560px;
        --chat-radius: 24px;
        --chat-z: 9999;
    }

    /* ── Bubble trigger button ─────────────────────────────────── */
    #chatbot-trigger {
        position: fixed;
        bottom: 32px;
        right: 32px;
        z-index: var(--chat-z);
        width: 60px;
        height: 60px;
        border-radius: 20px;
        background: linear-gradient(135deg, var(--chat-primary), #3671ff);
        border: none;
        cursor: pointer;
        box-shadow: 0 8px 32px rgba(26, 79, 245, .45), 0 0 0 0 rgba(26, 79, 245, .3);
        transition: transform .25s cubic-bezier(.34, 1.56, .64, 1),
            box-shadow .25s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: bubblePulse 3s ease-in-out infinite;
    }

    #chatbot-trigger:hover {
        transform: scale(1.1) translateY(-3px);
        box-shadow: 0 14px 40px rgba(26, 79, 245, .55);
    }

    @keyframes bubblePulse {

        0%,
        100% {
            box-shadow: 0 8px 32px rgba(26, 79, 245, .45), 0 0 0 0 rgba(26, 79, 245, .3);
        }

        50% {
            box-shadow: 0 8px 32px rgba(26, 79, 245, .45), 0 0 0 12px rgba(26, 79, 245, 0);
        }
    }

    /* ── Badge notifikasi ──────────────────────────────────────── */
    #chatbot-badge {
        position: absolute;
        top: -6px;
        right: -6px;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #ef4444;
        color: #fff;
        font-size: 10px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2.5px solid #fff;
        opacity: 0;
        transform: scale(0);
        transition: opacity .2s, transform .3s cubic-bezier(.34, 1.56, .64, 1);
    }

    #chatbot-badge.show {
        opacity: 1;
        transform: scale(1);
    }

    /* ── Icon toggle ───────────────────────────────────────────── */
    .trigger-icon {
        transition: all .3s cubic-bezier(.34, 1.56, .64, 1);
        position: absolute;
    }

    .trigger-icon.hidden-icon {
        opacity: 0;
        transform: rotate(90deg) scale(0.5);
    }

    /* ── Chatbot panel ─────────────────────────────────────────── */
    #chatbot-panel {
        position: fixed;
        bottom: 108px;
        right: 32px;
        z-index: var(--chat-z);
        width: var(--chat-bubble-w);
        height: var(--chat-bubble-h);
        border-radius: var(--chat-radius);
        background: #fff;
        box-shadow: 0 24px 80px rgba(15, 20, 60, .18), 0 4px 24px rgba(26, 79, 245, .08);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        transform-origin: bottom right;
        transform: scale(0.85) translateY(20px);
        opacity: 0;
        pointer-events: none;
        transition: transform .35s cubic-bezier(.34, 1.56, .64, 1),
            opacity .25s ease;
        border: 1px solid rgba(26, 79, 245, .1);
    }

    #chatbot-panel.open {
        transform: scale(1) translateY(0);
        opacity: 1;
        pointer-events: all;
    }

    /* Mobile: full screen */
    @media (max-width: 480px) {
        #chatbot-panel {
            bottom: 0;
            right: 0;
            left: 0;
            width: 100%;
            height: 90dvh;
            border-radius: var(--chat-radius) var(--chat-radius) 0 0;
        }

        #chatbot-trigger {
            bottom: 20px;
            right: 20px;
        }
    }

    /* ── Header ────────────────────────────────────────────────── */
    #chat-header {
        background: linear-gradient(135deg, var(--chat-primary) 0%, #1340e1 100%);
        padding: 18px 20px 16px;
        flex-shrink: 0;
        position: relative;
        overflow: hidden;
    }

    #chat-header::after {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(ellipse 120% 80% at 100% 0%, rgba(15, 244, 198, .15), transparent);
        pointer-events: none;
    }

    .chat-avatar {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        background: rgba(255, 255, 255, .2);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 18px;
        position: relative;
    }

    .chat-status-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: var(--chat-accent);
        border: 2px solid var(--chat-primary);
        position: absolute;
        bottom: -2px;
        right: -2px;
        animation: statusPulse 2s ease-in-out infinite;
    }

    @keyframes statusPulse {

        0%,
        100% {
            box-shadow: 0 0 0 0 rgba(15, 244, 198, .5);
        }

        50% {
            box-shadow: 0 0 0 5px rgba(15, 244, 198, 0);
        }
    }

    #chat-close {
        width: 32px;
        height: 32px;
        border-radius: 10px;
        background: rgba(255, 255, 255, .15);
        border: none;
        cursor: pointer;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background .2s;
        flex-shrink: 0;
    }

    #chat-close:hover {
        background: rgba(255, 255, 255, .25);
    }

    /* ── Suggested chips ───────────────────────────────────────── */
    #chat-suggestions {
        padding: 14px 16px 6px;
        flex-shrink: 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .suggestion-chip {
        display: inline-block;
        background: #f0f4ff;
        color: var(--chat-primary);
        border: 1px solid rgba(26, 79, 245, .15);
        border-radius: 20px;
        padding: 5px 12px;
        font-size: 11.5px;
        font-weight: 600;
        cursor: pointer;
        margin: 3px 3px 3px 0;
        transition: background .15s, transform .15s;
        white-space: nowrap;
        font-family: 'DM Sans', sans-serif;
    }

    .suggestion-chip:hover {
        background: var(--chat-primary);
        color: #fff;
        transform: translateY(-1px);
    }

    /* ── Messages area ─────────────────────────────────────────── */
    #chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 16px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        scroll-behavior: smooth;
    }

    #chat-messages::-webkit-scrollbar {
        width: 4px;
    }

    #chat-messages::-webkit-scrollbar-thumb {
        background: #e2e8f0;
        border-radius: 2px;
    }

    /* ── Message bubbles ───────────────────────────────────────── */
    .msg {
        display: flex;
        gap: 8px;
        max-width: 85%;
        animation: msgIn .3s ease;
    }

    @keyframes msgIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .msg.user {
        align-self: flex-end;
        flex-direction: row-reverse;
    }

    .msg.bot {
        align-self: flex-start;
    }

    .msg-avatar {
        width: 28px;
        height: 28px;
        border-radius: 9px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        margin-top: 2px;
    }

    .msg.bot .msg-avatar {
        background: #f0f4ff;
    }

    .msg.user .msg-avatar {
        background: var(--chat-primary);
        font-size: 11px;
        color: #fff;
        font-weight: 700;
        font-family: 'Syne', sans-serif;
    }

    .msg-bubble {
        padding: 10px 14px;
        border-radius: 16px;
        font-size: 13.5px;
        line-height: 1.55;
        font-family: 'DM Sans', sans-serif;
    }

    .msg.bot .msg-bubble {
        background: #f8faff;
        color: #1e293b;
        border: 1px solid #e8efff;
        border-bottom-left-radius: 4px;
    }

    .msg.user .msg-bubble {
        background: var(--chat-primary);
        color: #fff;
        border-bottom-right-radius: 4px;
    }

    /* Markdown dalam bubble bot */
    .msg.bot .msg-bubble strong {
        font-weight: 700;
        color: #1340e1;
    }

    .msg.bot .msg-bubble em {
        font-style: italic;
        color: #475569;
    }

    .msg.bot .msg-bubble ul,
    .msg.bot .msg-bubble ol {
        margin: 6px 0 6px 16px;
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .msg.bot .msg-bubble li::marker {
        color: var(--chat-primary);
    }

    .msg.bot .msg-bubble code {
        background: #e8efff;
        color: #1340e1;
        padding: 1px 5px;
        border-radius: 5px;
        font-size: 12px;
        font-family: monospace;
    }

    /* ── Typing indicator ──────────────────────────────────────── */
    .typing-indicator {
        display: flex;
        gap: 4px;
        align-items: center;
        padding: 12px 16px;
    }

    .typing-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #94a3b8;
        animation: typingBounce 1.2s infinite ease-in-out;
    }

    .typing-dot:nth-child(2) {
        animation-delay: .15s;
    }

    .typing-dot:nth-child(3) {
        animation-delay: .30s;
    }

    @keyframes typingBounce {

        0%,
        80%,
        100% {
            transform: scale(0.8);
            opacity: .5;
        }

        40% {
            transform: scale(1.2);
            opacity: 1;
        }
    }

    /* ── Input area ────────────────────────────────────────────── */
    #chat-input-area {
        padding: 12px 14px 14px;
        border-top: 1px solid #f1f5f9;
        background: #fff;
        flex-shrink: 0;
    }

    .chat-input-row {
        display: flex;
        align-items: flex-end;
        gap: 8px;
        background: #f8faff;
        border: 1.5px solid #e8efff;
        border-radius: 16px;
        padding: 8px 8px 8px 14px;
        transition: border-color .2s;
    }

    .chat-input-row:focus-within {
        border-color: var(--chat-primary);
        box-shadow: 0 0 0 3px rgba(26, 79, 245, .1);
    }

    #chat-input {
        flex: 1;
        border: none;
        background: transparent;
        font-family: 'DM Sans', sans-serif;
        font-size: 13.5px;
        color: #1e293b;
        resize: none;
        outline: none;
        max-height: 90px;
        min-height: 22px;
        line-height: 1.5;
    }

    #chat-input::placeholder {
        color: #94a3b8;
    }

    #chat-send {
        width: 36px;
        height: 36px;
        border-radius: 11px;
        background: var(--chat-primary);
        border: none;
        cursor: pointer;
        color: #fff;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background .2s, transform .15s;
    }

    #chat-send:hover:not(:disabled) {
        background: var(--chat-primary-d);
        transform: scale(1.07);
    }

    #chat-send:disabled {
        background: #94a3b8;
        cursor: not-allowed;
    }

    .chat-footer-note {
        text-align: center;
        font-size: 10.5px;
        color: #94a3b8;
        margin-top: 8px;
        font-family: 'DM Sans', sans-serif;
    }

    /* ── Welcome message ───────────────────────────────────────── */
    .welcome-card {
        background: linear-gradient(135deg, #f0f4ff, #f8faff);
        border: 1px solid #dbeafe;
        border-radius: 16px;
        padding: 16px;
        text-align: center;
        margin-bottom: 4px;
    }
</style>

{{-- ── TRIGGER BUTTON ──────────────────────────────────────── --}}
<button id="chatbot-trigger" aria-label="Buka Chatbot MCI" title="Tanya MCI AI">
    <div id="chatbot-badge">1</div>
    {{-- Icon chat --}}
    <svg id="icon-chat" class="trigger-icon w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-4 4-1-4z" />
    </svg>
    {{-- Icon close --}}
    <svg id="icon-close" class="trigger-icon hidden-icon w-6 h-6 text-white" fill="none" stroke="currentColor"
        viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
    </svg>
</button>

{{-- ── CHATBOT PANEL ────────────────────────────────────────── --}}
<div id="chatbot-panel" role="dialog" aria-label="MCI AI Chatbot" aria-modal="true">

    {{-- Header --}}
    <div id="chat-header">
        <div style="display:flex;align-items:center;gap:12px;position:relative;z-index:1;">
            <div class="chat-avatar">
                🤖
                <div class="chat-status-dot"></div>
            </div>
            <div style="flex:1;">
                <div style="color:#fff;font-family:'Syne',sans-serif;font-weight:700;font-size:15px;line-height:1.2;">
                    MCI AI Assistant
                </div>
                <div
                    style="color:rgba(255,255,255,.7);font-size:11.5px;font-family:'DM Sans',sans-serif;margin-top:1px;">
                    Powered by Claude · Siap membantu 😊
                </div>
            </div>
            <button id="chat-clear" title="Hapus percakapan"
                style="width:32px;height:32px;border-radius:10px;background:rgba(255,255,255,.15);border:none;cursor:pointer;color:#fff;display:flex;align-items:center;justify-content:center;transition:background .2s;">
                <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
            <button id="chat-close" aria-label="Tutup chatbot">
                <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
        </div>
    </div>

    {{-- Suggested questions --}}
    <div id="chat-suggestions"></div>

    {{-- Messages --}}
    <div id="chat-messages"></div>

    {{-- Input --}}
    <div id="chat-input-area">
        <div class="chat-input-row">
            <textarea id="chat-input" placeholder="Tanya seputar UKM MCI…" rows="1" maxlength="500"
                aria-label="Pesan ke MCI AI"></textarea>
            <button id="chat-send" disabled aria-label="Kirim pesan">
                <svg style="width:17px;height:17px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                </svg>
            </button>
        </div>
        <div class="chat-footer-note">🔒 Percakapan bersifat rahasia &nbsp;·&nbsp; MCI AI dapat membuat kesalahan</div>
    </div>
</div>

@push('scripts')
    <script>
        (() => {
            'use strict';

            function generateUUID() {
                return Math.random().toString(36).substring(2, 9) + '-' + Date.now().toString(36);
            }

            // ── State ──────────────────────────────────────────────────────
            const SESSION_KEY = 'mci_chat_session';
            const HISTORY_KEY = 'mci_chat_history';
            let isOpen = false;
            let isStreaming = false;
            // let sessionId = localStorage.getItem(SESSION_KEY) || crypto.randomUUID();
            let sessionId = localStorage.getItem(SESSION_KEY) || generateUUID();
            localStorage.setItem(SESSION_KEY, sessionId);

            // ── DOM refs ───────────────────────────────────────────────────
            const trigger = document.getElementById('chatbot-trigger');
            const panel = document.getElementById('chatbot-panel');
            const iconChat = document.getElementById('icon-chat');
            const iconClose = document.getElementById('icon-close');
            const badge = document.getElementById('chatbot-badge');
            const messagesEl = document.getElementById('chat-messages');
            const inputEl = document.getElementById('chat-input');
            const sendBtn = document.getElementById('chat-send');
            const clearBtn = document.getElementById('chat-clear');
            const suggestEl = document.getElementById('chat-suggestions');
            const closePanelBtn = document.getElementById('chat-close');

            // ── CSRF token ─────────────────────────────────────────────────
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

            // ── Toggle panel ───────────────────────────────────────────────
            function togglePanel() {
                isOpen = !isOpen;
                panel.classList.toggle('open', isOpen);
                iconChat.classList.toggle('hidden-icon', isOpen);
                iconClose.classList.toggle('hidden-icon', !isOpen);
                if (isOpen) {
                    badge.classList.remove('show');
                    inputEl.focus();
                    scrollToBottom();
                }
            }
            trigger.addEventListener('click', togglePanel);
            closePanelBtn.addEventListener('click', togglePanel);

            // Tutup saat klik luar (desktop)
            document.addEventListener('click', e => {
                if (isOpen && !panel.contains(e.target) && !trigger.contains(e.target)) {
                    togglePanel();
                }
            });

            // ── Simple markdown parser ─────────────────────────────────────
            function parseMarkdown(text) {
                return text
                    .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
                    .replace(/\*(.+?)\*/g, '<em>$1</em>')
                    .replace(/`(.+?)`/g, '<code>$1</code>')
                    .replace(/^[-•]\s(.+)/gm, '<li>$1</li>')
                    .replace(/(<li>.*<\/li>\n?)+/g, s => `<ul>${s}</ul>`)
                    .replace(/\n/g, '<br>');
            }

            // ── Render message ─────────────────────────────────────────────
            function addMessage(role, content, id = null) {
                const wrap = document.createElement('div');
                wrap.className = `msg ${role}`;
                if (id) wrap.id = id;

                const avatar = document.createElement('div');
                avatar.className = 'msg-avatar';
                avatar.textContent = role === 'bot' ? '🤖' : 'A';

                const bubble = document.createElement('div');
                bubble.className = 'msg-bubble';
                bubble.innerHTML = role === 'bot' ? parseMarkdown(content) : escapeHtml(content);

                wrap.appendChild(avatar);
                wrap.appendChild(bubble);
                messagesEl.appendChild(wrap);
                scrollToBottom();
                return bubble;
            }

            function escapeHtml(t) {
                return t.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            }

            // ── Typing indicator ───────────────────────────────────────────
            function showTyping() {
                const el = document.createElement('div');
                el.id = 'typing-indicator';
                el.className = 'msg bot';
                el.innerHTML = `
        <div class="msg-avatar">🤖</div>
        <div class="msg-bubble" style="padding:0;">
            <div class="typing-indicator">
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
            </div>
        </div>`;
                messagesEl.appendChild(el);
                scrollToBottom();
            }

            function hideTyping() {
                document.getElementById('typing-indicator')?.remove();
            }

            function scrollToBottom() {
                requestAnimationFrame(() => {
                    messagesEl.scrollTop = messagesEl.scrollHeight;
                });
            }

            // ── Selamat datang ─────────────────────────────────────────────
            function showWelcome() {
                const el = document.createElement('div');
                el.className = 'welcome-card';
                el.innerHTML = `
        <div style="font-size:32px;margin-bottom:8px;">👋</div>
        <div style="font-family:'Syne',sans-serif;font-weight:700;color:#1e293b;font-size:15px;margin-bottom:6px;">
            Halo! Saya MCI AI
        </div>
        <div style="font-size:13px;color:#64748b;line-height:1.6;">
            Asisten virtual UKM Mahasiswa Creative & Innovation.<br>
            Tanya apa saja seputar MCI — saya siap membantu! 😊
        </div>`;
                messagesEl.appendChild(el);
            }

            // ── Load suggested questions ───────────────────────────────────
            async function loadSuggestions() {
                try {
                    const res = await fetch('/chatbot/suggested');
                    const data = await res.json();
                    suggestEl.innerHTML =
                        '<div style="font-size:11px;color:#94a3b8;font-family:\'DM Sans\',sans-serif;font-weight:600;margin-bottom:4px;padding:0 2px;">Pertanyaan populer:</div>';
                    const wrap = document.createElement('div');
                    data.suggestions.slice(0, 4).forEach(q => {
                        const chip = document.createElement('button');
                        chip.className = 'suggestion-chip';
                        chip.textContent = q;
                        chip.addEventListener('click', () => {
                            inputEl.value = q;
                            updateSendBtn();
                            sendMessage();
                        });
                        wrap.appendChild(chip);
                    });
                    suggestEl.appendChild(wrap);
                } catch (e) {
                    suggestEl.style.display = 'none';
                }
            }

            // ── Send message ───────────────────────────────────────────────
            async function sendMessage() {
                const text = inputEl.value.trim();
                if (!text || isStreaming) return;

                inputEl.value = '';
                updateSendBtn();
                autoResize();

                // Sembunyikan suggestions setelah pertama kirim
                suggestEl.style.display = 'none';

                addMessage('user', text);
                isStreaming = true;
                sendBtn.disabled = true;

                showTyping();

                try {
                    const response = await fetch('/chatbot/chat', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                        },
                        body: JSON.stringify({
                            message: text,
                            session_id: sessionId
                        }),
                    });

                    hideTyping();

                    if (!response.ok) {
                        const err = await response.json().catch(() => ({}));
                        addMessage('bot', err.message || 'Maaf, terjadi kesalahan. Coba lagi ya! 🙏');
                        return;
                    }

                    // Streaming SSE
                    const botBubble = addMessage('bot', '');
                    const reader = response.body.getReader();
                    const decoder = new TextDecoder();
                    let buffer = '';
                    let fullText = '';

                    while (true) {
                        const {
                            done,
                            value
                        } = await reader.read();
                        if (done) break;

                        buffer += decoder.decode(value, {
                            stream: true
                        });
                        const lines = buffer.split('\n');
                        buffer = lines.pop() ?? '';

                        for (const line of lines) {
                            const trimmed = line.trim();
                            if (!trimmed.startsWith('data: ')) continue;
                            const payload = trimmed.slice(6);
                            if (payload === '[DONE]') break;

                            try {
                                const chunk = JSON.parse(payload);
                                if (chunk.text) {
                                    fullText += chunk.text;
                                    botBubble.innerHTML = parseMarkdown(fullText);
                                    scrollToBottom();
                                }
                            } catch {}
                        }
                    }

                    saveHistory();

                    // Badge saat panel tertutup
                    if (!isOpen) {
                        badge.classList.add('show');
                    }

                } catch (e) {
                    hideTyping();
                    addMessage('bot', 'Koneksi terputus. Periksa internet Anda dan coba lagi 🌐');
                } finally {
                    isStreaming = false;
                    updateSendBtn();
                    inputEl.focus();
                }
            }

            // ── Input handlers ─────────────────────────────────────────────
            inputEl.addEventListener('input', () => {
                updateSendBtn();
                autoResize();
            });
            inputEl.addEventListener('keydown', e => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendMessage();
                }
            });
            sendBtn.addEventListener('click', sendMessage);

            function updateSendBtn() {
                sendBtn.disabled = !inputEl.value.trim() || isStreaming;
            }

            function autoResize() {
                inputEl.style.height = 'auto';
                inputEl.style.height = Math.min(inputEl.scrollHeight, 90) + 'px';
            }

            // ── Clear chat ──────────────────────────────────────────────────
            clearBtn.addEventListener('click', () => {
                if (!confirm('Hapus seluruh percakapan?')) return;
                messagesEl.innerHTML = '';
                // sessionId = crypto.randomUUID();
                sessionId = generateUUID();
                localStorage.setItem(SESSION_KEY, sessionId);
                localStorage.removeItem(HISTORY_KEY);
                showWelcome();
                suggestEl.style.display = 'block';
                loadSuggestions();
            });

            // ── Persist history (opsional, lightweight) ────────────────────
            function saveHistory() {
                // Simpan max 20 messages di localStorage
                const msgs = [...messagesEl.querySelectorAll('.msg')].slice(-20).map(el => ({
                    role: el.classList.contains('user') ? 'user' : 'bot',
                    content: el.querySelector('.msg-bubble')?.innerHTML ?? '',
                }));
                localStorage.setItem(HISTORY_KEY, JSON.stringify(msgs));
            }

            function loadHistory() {
                try {
                    const saved = JSON.parse(localStorage.getItem(HISTORY_KEY) ?? '[]');
                    if (saved.length === 0) return false;
                    saved.forEach(({
                        role,
                        content
                    }) => {
                        const wrap = document.createElement('div');
                        wrap.className = `msg ${role}`;
                        const avatar = document.createElement('div');
                        avatar.className = 'msg-avatar';
                        avatar.textContent = role === 'bot' ? '🤖' : 'A';
                        const bubble = document.createElement('div');
                        bubble.className = 'msg-bubble';
                        bubble.innerHTML = content;
                        wrap.appendChild(avatar);
                        wrap.appendChild(bubble);
                        messagesEl.appendChild(wrap);
                    });
                    return true;
                } catch {
                    return false;
                }
            }

            // ── Init ───────────────────────────────────────────────────────
            (function init() {
                const hadHistory = loadHistory();
                if (!hadHistory) showWelcome();

                loadSuggestions();

                // Tampilkan badge setelah 3 detik untuk attract attention
                setTimeout(() => {
                    if (!isOpen) badge.classList.add('show');
                }, 3000);

                // Update CSRF meta tag jika belum ada
                if (!document.querySelector('meta[name="csrf-token"]')) {
                    const meta = document.createElement('meta');
                    meta.name = 'csrf-token';
                    meta.content = '{{ csrf_token() }}';
                    document.head.appendChild(meta);
                }
            })();

        })();
    </script>
@endpush
