<?php
if (!isset($_SESSION['user_id'])) { 
    return; 
}
if (!isset($appConfig) || !is_array($appConfig)) {
    if(file_exists(__DIR__ . '/../config/app.php')) {
        $appConfig = require __DIR__ . '/../config/app.php';
    } else {
        return; 
    }
}

$base_url_chat = $appConfig['BASE_URL'] ?? '/'; 
$admin_chat_recipient_id = $appConfig['ADMIN_CHAT_RECIPIENT_ID'] ?? 0; 
$current_user_id_js = (int)($_SESSION['user_id'] ?? 0);
$current_user_nama_js = $_SESSION['user_nama'] ?? 'Pengguna';
$is_admin_js_php = ($_SESSION['is_admin'] ?? false); 
?>
<div id="chat-bubble-container" title="Buka Chat">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="30px" height="30px"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M21.99 4c0-1.1-.89-2-1.99-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h14l4 4-.01-18zM17.17 14H6v-2h11.17v2zm-1.17-3H6V9h10v2zm0-3H6V6h10v2z"/></svg>
    <span id="chat-notification-dot"></span>
</div>
<div id="chat-window-container">
    <div class="chat-header-ui">
        <button id="chat-back-to-list-button" title="Kembali ke Daftar Pengguna" style="display: none;">&larr;</button>
        <div class="chat-title-status-wrapper">
            <span id="chat-window-title-text">Chat</span>
            <small id="chat-partner-status-text"></small>
        </div>
        <button id="chat-close-button" title="Tutup Chat">&times;</button>
    </div>
    <div id="chat-messages-area"></div>
    <div class="chat-input-form-area">
        <textarea id="chat-text-input" placeholder="Ketik pesan Anda..." rows="1"></textarea>
        <button id="chat-send-button" title="Kirim Pesan">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="20px" height="20px"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2 .01 7z"/></svg>
        </button>
    </div>
</div>
<style>
    :root{--chat-color-white:#FFFFFF;--chat-color-bg-light:#E9F1F7;--chat-color-border:#D6E0EA;--chat-color-primary:#4A90E2;--chat-color-primary-hover:#357ABD;--chat-color-text-dark:#1A3A5B;--chat-color-text-light:#FFFFFF;--chat-color-text-muted:#6A89B9;--chat-color-received-bg:#FFFFFF;--chat-color-received-text:var(--chat-color-text-dark);--chat-color-online:#a8e6cf;--chat-color-offline:#adb5bd;}
    #chat-bubble-container{position:fixed;bottom:30px;right:30px;width:60px;height:60px;background-color:var(--chat-color-primary);color:var(--chat-color-text-light);border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0px 4px 12px rgba(0,0,0,0.25);z-index:10000;transition:transform .2s ease-in-out,background-color .2s}
    #chat-bubble-container:hover{transform:scale(1.1);background-color:var(--chat-color-primary-hover)}
    #chat-notification-dot{position:absolute;top:-3px;right:-3px;min-width:20px;height:20px;padding:0 3px;background-color:red;color:white;border-radius:50%;border:2px solid white;display:none;font-size:10px;font-weight:bold;line-height:14px;text-align:center;box-sizing:border-box;z-index:1}
    #chat-notification-dot.active{display:flex;align-items:center;justify-content:center;}
    #chat-window-container{position:fixed;bottom:100px;right:30px;width:350px;max-height:75vh;background-color:var(--chat-color-white);border-radius:10px;box-shadow:0px 5px 25px rgba(0,0,0,0.2);display:none;flex-direction:column;z-index:10001;border:1px solid var(--chat-color-border);overflow:hidden}
    #chat-window-container.open{display:flex}
    .chat-header-ui{background-color:var(--chat-color-primary);color:var(--chat-color-text-light);padding:10px 15px;font-weight:bold;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--chat-color-primary-hover)}
    .chat-title-status-wrapper{flex-grow:1;text-align:center;overflow:hidden;}
    #chat-window-title-text{display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:1em;line-height:1.2;}
    #chat-partner-status-text{display:block;font-size:0.7em;font-weight:normal;line-height:1.2;opacity:0.9;}
    #chat-back-to-list-button{font-size:22px;padding-right:10px;background:none;border:none;color:var(--chat-color-text-light);font-weight:bold;cursor:pointer;padding-top:0;padding-bottom:0;line-height:1}
    #chat-close-button{font-size:28px;padding-left:10px;background:none;border:none;color:var(--chat-color-text-light);font-weight:bold;cursor:pointer;padding-top:0;padding-bottom:0;line-height:1}
    #chat-close-button:hover,#chat-back-to-list-button:hover{opacity:.8}
    #chat-messages-area{flex-grow:1;padding:15px;overflow-y:auto;background-color:var(--chat-color-bg-light);display:flex;flex-direction:column}
    .message-item{max-width:80%;margin-bottom:10px;padding:10px 14px;border-radius:18px;word-wrap:break-word;font-size:.9rem;line-height:1.45;clear:both}
    .message-item.sent{background-color:var(--chat-color-primary);color:var(--chat-color-text-light);align-self:flex-end;border-bottom-right-radius:5px;margin-left:auto}
    .message-item.received{background-color:var(--chat-color-received-bg);color:var(--chat-color-received-text);align-self:flex-start;border-bottom-left-radius:5px;margin-right:auto;border:1px solid var(--chat-color-border)}
    .message-sender-name{font-size:.75em;color:var(--chat-color-text-muted);margin-bottom:3px}
    .message-timestamp{font-size:.7rem;margin-top:5px;text-align:right;display:block}
    .message-item.sent .message-timestamp{color:#e0f0ff;}
    .message-item.received .message-timestamp{color:var(--chat-color-text-muted)}
    .message-timestamp .read-status{margin-left:4px;font-size:0.9em;font-weight:bold;}
    .chat-input-form-area{display:flex;padding:10px 12px;border-top:1px solid var(--chat-color-border);background-color:var(--chat-color-white);align-items:flex-end}
    #chat-text-input{flex-grow:1;border:1px solid var(--chat-color-border);border-radius:18px;padding:10px 15px;font-size:.9rem;margin-right:8px;resize:none;min-height:22px;max-height:100px;overflow-y:auto;line-height:1.4;background-color:var(--chat-color-white);color:var(--chat-color-text-dark)}
    #chat-send-button{background-color:var(--chat-color-primary);color:var(--chat-color-text-light);border:none;border-radius:50%;width:42px;height:42px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background-color .2s;flex-shrink:0}
    #chat-send-button:hover{background-color:var(--chat-color-primary-hover)}
    #chat-send-button svg{margin-left:2px}
    .chat-user-list{list-style:none;padding:0;margin:0; width:100%;}
    .chat-user-list li{padding:12px 15px;border-bottom:1px solid var(--chat-color-border);cursor:pointer;transition:background-color .2s;font-size:.9em;color:var(--chat-color-text-dark)}
    .chat-user-list li:hover{background-color:var(--chat-color-bg-light)}
    .chat-user-list li .user-email{font-size:.8em;color:var(--chat-color-text-muted);display:block}
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatBubble = document.getElementById('chat-bubble-container');
    const chatWindow = document.getElementById('chat-window-container');
    const closeChatBtn = document.getElementById('chat-close-button');
    const messageInput = document.getElementById('chat-text-input');
    const sendMessageBtn = document.getElementById('chat-send-button');
    const messagesArea = document.getElementById('chat-messages-area');
    const chatHeaderTitleEl = document.getElementById('chat-window-title-text');
    const partnerStatusTextEl = document.getElementById('chat-partner-status-text');
    const backToListBtn = document.getElementById('chat-back-to-list-button');
    const chatNotificationDot = document.getElementById('chat-notification-dot');

    if (!chatBubble || !chatWindow || !closeChatBtn || !messageInput || !sendMessageBtn || !messagesArea || !chatHeaderTitleEl || !partnerStatusTextEl || !backToListBtn || !chatNotificationDot) {
        console.error("Chat UI Error: Elemen HTML utama chat tidak ditemukan."); return; 
    }

    const baseUrl = '<?php echo $base_url_chat; ?>';
    const defaultAdminId = parseInt('<?php echo $admin_chat_recipient_id; ?>');
    const currentUserId = parseInt('<?php echo $current_user_id_js; ?>');
    const currentUserName = '<?php echo addslashes($current_user_nama_js); ?>';
    const isAdmin = <?php echo $is_admin_js_php ? 'true' : 'false'; ?>;

    let currentPartnerId = 0;
    let currentPartnerName = '';
    const defaultChatWindowTitleForUser = `Chat dengan Admin`;
    const defaultChatWindowTitleForAdmin = 'Pilih Pengguna';
    let lastDisplayedMessageId = 0; 
    let partnerStatusPollingIntervalId = null; 
    const PARTNER_STATUS_POLLING_MS = 15000;
    let notificationPollingIntervalId = null;
    const NOTIFICATION_POLLING_MS = 15000;

    function initializeChatState() {
        if (isAdmin) {
            currentPartnerId = 0; currentPartnerName = '';
            if(chatHeaderTitleEl) chatHeaderTitleEl.textContent = defaultChatWindowTitleForAdmin;
            if(backToListBtn) backToListBtn.style.display = 'none';
            if(partnerStatusTextEl) partnerStatusTextEl.textContent = '';
        } else {
            currentPartnerId = defaultAdminId; currentPartnerName = 'Admin'; 
            if(chatHeaderTitleEl) chatHeaderTitleEl.textContent = defaultChatWindowTitleForUser;
            if(backToListBtn) backToListBtn.style.display = 'none';
        }
        lastDisplayedMessageId = 0; 
    }
    initializeChatState(); 

    function htmlspecialcharsJS(str) {
        if (typeof str !== 'string') return '';
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return str.replace(/[&<>"']/g, (match) => map[match]);
    }

    function displayMessage(msg) {
        const messageDiv = document.createElement('div');
        messageDiv.classList.add('message-item');
        if (msg.id && msg.id.toString().startsWith('temp_')) { messageDiv.id = msg.id; }
        
        const contentDiv = document.createElement('div');
        contentDiv.classList.add('message-content');
        contentDiv.textContent = msg.message_text; 
        
        const timestampDiv = document.createElement('div');
        timestampDiv.classList.add('message-timestamp');
        let timeText = msg.sent_at_formatted || new Date(msg.sent_at || Date.now()).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false });
        timestampDiv.textContent = timeText + ' '; 

        if (parseInt(msg.sender_id) === currentUserId) { 
            messageDiv.classList.add('sent');
            const readStatusSpan = document.createElement('span');
            readStatusSpan.className = 'read-status';
            if (msg.id && msg.id.toString().startsWith('temp_')) {
                readStatusSpan.innerHTML = '&#x2713;'; readStatusSpan.style.color = '#A0A0A0'; readStatusSpan.title = 'Mengirim...';
            } else if (msg.is_read === true || msg.is_read === 1 || msg.is_read === '1') {
                readStatusSpan.innerHTML = '&#x2713;&#x2713;'; readStatusSpan.style.color = '#34B7F1'; readStatusSpan.title = 'Dibaca';
            } else {
                readStatusSpan.innerHTML = '&#x2713;&#x2713;'; readStatusSpan.style.color = '#A0A0A0'; readStatusSpan.title = 'Terkirim';
            }
            timestampDiv.appendChild(readStatusSpan); 
        } else { 
            messageDiv.classList.add('received'); 
        }
        
        messageDiv.appendChild(contentDiv); messageDiv.appendChild(timestampDiv);
        messagesArea.appendChild(messageDiv); 
        
        if (msg.id && !msg.id.toString().startsWith('temp_')) {
            const numericId = parseInt(msg.id);
            if (numericId > lastDisplayedMessageId) { lastDisplayedMessageId = numericId; }
        }
        if((messagesArea.scrollHeight - messagesArea.scrollTop - messagesArea.clientHeight < 150) || parseInt(msg.sender_id) === currentUserId ) {
             messagesArea.scrollTop = messagesArea.scrollHeight; 
        }
    }
    
    async function fetchChatMessages(partnerIdToFetch) {
        if (partnerIdToFetch === 0 || (partnerIdToFetch === currentUserId && !isAdmin && partnerIdToFetch !== defaultAdminId) ) {
             if(messagesArea) messagesArea.innerHTML = '<p style="text-align:center; color:#aaa;">Partner chat tidak valid.</p>';
             stopPollingPartnerStatus(); return; 
        }
        if(messagesArea) messagesArea.innerHTML = '<p style="text-align:center; color:#aaa;">Memuat pesan...</p>';
        try {
            const response = await fetch(`${baseUrl}chat/fetchMessages?partner_id=${partnerIdToFetch}`);
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status} - ${await response.text()}`);
            const data = await response.json();
            if(messagesArea) messagesArea.innerHTML = ''; 
            if (data.success && data.messages) {
                let maxIdInBatch = 0;
                if (data.messages.length > 0) {
                    data.messages.forEach(msg => {
                        displayMessage(msg);
                        if (msg.id && !msg.id.toString().startsWith('temp_')) {
                            if(parseInt(msg.id) > maxIdInBatch) maxIdInBatch = parseInt(msg.id);
                        }
                    });
                    if (maxIdInBatch > lastDisplayedMessageId) lastDisplayedMessageId = maxIdInBatch;
                } else { 
                    lastDisplayedMessageId = 0; 
                    if(messagesArea) messagesArea.innerHTML = '<p style="text-align:center; color:#aaa;">Belum ada pesan.</p>';
                }
                if(messagesArea) messagesArea.scrollTop = messagesArea.scrollHeight;
                startPollingPartnerStatus(partnerIdToFetch);
            } else { 
                if(messagesArea) messagesArea.innerHTML = `<p style="text-align:center; color:red;">${htmlspecialcharsJS(data.message) || 'Gagal memuat pesan.'}</p>`;
                stopPollingPartnerStatus();
            }
        } catch (error) { 
            if(messagesArea) messagesArea.innerHTML = '<p style="text-align:center; color:red;">Kesalahan memuat riwayat.</p>'; 
            console.error('Error fetching messages:', error); 
            stopPollingPartnerStatus();
        }
    }

    async function loadAdminChatUserList() {
        if (!isAdmin || !messagesArea || !chatHeaderTitleEl || !backToListBtn) return; 
        stopPollingPartnerStatus(); 
        messagesArea.innerHTML = '<p style="text-align:center; color:#aaa;">Memuat daftar pengguna...</p>';
        chatHeaderTitleEl.textContent = defaultChatWindowTitleForAdmin;
        if(partnerStatusTextEl) partnerStatusTextEl.textContent = '';
        backToListBtn.style.display = 'none'; 
        currentPartnerId = 0; currentPartnerName = ''; lastDisplayedMessageId = 0;
        try {
            const response = await fetch(`${baseUrl}chat/getAdminChatList`);
            if (!response.ok)  throw new Error(`HTTP error! status: ${response.status} - ${await response.text()}`);
            const data = await response.json();
            if(messagesArea) messagesArea.innerHTML = ''; 
            if (data.success && data.users) {
                if (data.users.length === 0) { if(messagesArea) messagesArea.innerHTML = '<p style="text-align:center; color:#aaa;">Tidak ada pengguna.</p>'; return; }
                const userListUl = document.createElement('ul'); userListUl.className = 'chat-user-list';
                data.users.forEach(user => {
                    if (parseInt(user.id) === currentUserId) return; 
                    const listItem = document.createElement('li');
                    listItem.innerHTML = `${htmlspecialcharsJS(user.nama)} <span class="user-email">(${htmlspecialcharsJS(user.email)})</span>`;
                    listItem.dataset.userId = user.id; listItem.dataset.userName = user.nama;
                    listItem.addEventListener('click', () => {
                        currentPartnerId = parseInt(listItem.dataset.userId); currentPartnerName = listItem.dataset.userName;
                        if(chatHeaderTitleEl) chatHeaderTitleEl.textContent = `Chat dengan ${htmlspecialcharsJS(currentPartnerName)}`;
                        if(backToListBtn) backToListBtn.style.display = 'inline-block'; 
                        fetchChatMessages(currentPartnerId); 
                    });
                    userListUl.appendChild(listItem);
                });
                if(messagesArea) messagesArea.appendChild(userListUl);
            } else { if(messagesArea) messagesArea.innerHTML = `<p style="text-align:center; color:red;">${htmlspecialcharsJS(data.message) || 'Gagal memuat pengguna.'}</p>`;}
        } catch (error) { if(messagesArea) messagesArea.innerHTML = '<p style="text-align:center; color:red;">Kesalahan jaringan.</p>'; console.error('Error fetching admin chat list:', error);}
    }
    
    async function fetchPartnerStatus(partnerIdToFetch) {
        if (!partnerIdToFetch || partnerIdToFetch === 0 || !partnerStatusTextEl) return;
        if (partnerIdToFetch === currentUserId && !isAdmin) { partnerStatusTextEl.textContent = ''; return; }
        try {
            const response = await fetch(`${baseUrl}chat/getUserOnlineStatus?partner_id=${partnerIdToFetch}`);
            if (!response.ok) { partnerStatusTextEl.textContent = 'Status tidak diketahui'; return; }
            const data = await response.json();
            if (data.success) {
                partnerStatusTextEl.textContent = data.last_active_text;
                partnerStatusTextEl.style.color = data.online ? 'var(--chat-color-online, #c8e6c9)' : 'var(--chat-color-offline, #e0e0e0)';
            }
        } catch (error) { /* console.warn('Error fetching partner status:', error); */ }
    }

    function startPollingPartnerStatus(partnerIdForPolling) {
        stopPollingPartnerStatus(); 
        if (partnerIdForPolling === 0 || (partnerIdForPolling === currentUserId && !isAdmin)) return;
        fetchPartnerStatus(partnerIdForPolling); 
        partnerStatusPollingIntervalId = setInterval(() => { 
            if (chatWindow.classList.contains('open') && currentPartnerId === partnerIdForPolling) {
                fetchPartnerStatus(partnerIdForPolling);
            } else { stopPollingPartnerStatus(); }
        }, PARTNER_STATUS_POLLING_MS);
    }

    function stopPollingPartnerStatus() {
        if (partnerStatusPollingIntervalId) { clearInterval(partnerStatusPollingIntervalId); partnerStatusPollingIntervalId = null; } 
        if(partnerStatusTextEl) partnerStatusTextEl.textContent = '';
    }

    async function pollForNotifications() {
        if (!currentUserId || !chatNotificationDot) return;
        try {
            const response = await fetch(`${baseUrl}chat/checkUnreadSummary`);
            if (!response.ok) return; 
            const data = await response.json();
            if (data.success) {
                if (data.unread_count > 0) { 
                    chatNotificationDot.classList.add('active');
                    chatNotificationDot.textContent = data.unread_count > 9 ? '9+' : data.unread_count.toString();
                } else { 
                    chatNotificationDot.classList.remove('active');
                    chatNotificationDot.textContent = '';
                }
            }
        } catch (error) { console.warn('Notification polling error:', error); }
    }

    function startNotificationPolling() {
        if (notificationPollingIntervalId) clearInterval(notificationPollingIntervalId); 
        if (!currentUserId) return; 
        pollForNotifications(); 
        notificationPollingIntervalId = setInterval(pollForNotifications, NOTIFICATION_POLLING_MS);
    }

    window.openChatWithPartner = function(partnerId, partnerName) {
        if (!chatWindow || !chatHeaderTitleEl || !messagesArea || !messageInput || !backToListBtn) { return; }
        partnerId = parseInt(partnerId);
        if (isNaN(partnerId) || partnerId === 0 || partnerId === currentUserId) { alert("Partner chat tidak valid."); return; }
        stopPollingPartnerStatus(); 
        currentPartnerId = partnerId; currentPartnerName = partnerName;
        chatHeaderTitleEl.textContent = `Chat dengan ${htmlspecialcharsJS(currentPartnerName)}`;
        if (isAdmin) { backToListBtn.style.display = 'inline-block'; } 
        else { backToListBtn.style.display = 'none';  }
        messagesArea.innerHTML = ''; fetchChatMessages(currentPartnerId); 
        chatWindow.classList.add('open'); messageInput.focus();
        pollForNotifications(); 
    };

    chatBubble.addEventListener('click', function() {
        chatWindow.classList.toggle('open');
        if (chatWindow.classList.contains('open')) {
            messageInput.focus();
            if (chatNotificationDot) chatNotificationDot.classList.remove('active'); 
            
            if (isAdmin) {
                if (currentPartnerId === 0) { stopPollingPartnerStatus(); loadAdminChatUserList().finally(() => pollForNotifications()); }
                else { fetchChatMessages(currentPartnerId).finally(() => pollForNotifications()); chatHeaderTitleEl.textContent = `Chat dengan ${htmlspecialcharsJS(currentPartnerName)}`; backToListBtn.style.display = 'inline-block'; }
            } else { 
                if (currentPartnerId !== 0 && currentPartnerId !== currentUserId) {
                    chatHeaderTitleEl.textContent = defaultChatWindowTitleForUser; backToListBtn.style.display = 'none';
                    fetchChatMessages(currentPartnerId).finally(() => pollForNotifications());
                } else {
                    if(messagesArea) messagesArea.innerHTML = '<p style="text-align:center; color:orange;">Admin tidak terkonfigurasi.</p>';
                    if(chatHeaderTitleEl) chatHeaderTitleEl.textContent = 'Chat Error'; backToListBtn.style.display = 'none'; stopPollingPartnerStatus();
                    pollForNotifications(); 
                }
            }
        } else { stopPollingPartnerStatus(); }
    });

    closeChatBtn.addEventListener('click', () => { chatWindow.classList.remove('open'); stopPollingPartnerStatus(); });
    if (isAdmin) { backToListBtn.addEventListener('click', () => { stopPollingPartnerStatus(); loadAdminChatUserList(); }); }
    
    async function processSendMessage() {
        const messageText = messageInput.value.trim();
        if (messageText === '') return;
        let effectivePartnerId = currentPartnerId;
        if(!isAdmin && currentPartnerId === currentUserId && defaultAdminId !== 0 && defaultAdminId !== currentUserId) {
            effectivePartnerId = defaultAdminId;
        }
        if (effectivePartnerId === 0 || (effectivePartnerId === currentUserId && !isAdmin)) {
            alert(isAdmin && effectivePartnerId === 0 ? 'Pilih pengguna.' : 'Partner chat tidak valid.'); return;
        }
        const tempId = 'temp_' + Date.now();
        const optimisticMessage = { id: tempId, message_text: messageText, sender_id: currentUserId, sent_at: new Date().toISOString(), sent_at_formatted: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false }), sender_nama: currentUserName, is_read: false };
        if (messagesArea.querySelector('.chat-user-list')) { messagesArea.innerHTML = ''; }
        displayMessage(optimisticMessage); 
        const originalMessageText = messageInput.value; 
        messageInput.value = ''; messageInput.style.height = 'auto'; messageInput.focus();
        try {
            const response = await fetch(`${baseUrl}chat/sendMessage`, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ receiver_id: effectivePartnerId, message_text: originalMessageText }) });
            const data = await response.json(); 
            if (!response.ok) { throw new Error(data.message || `Server error: ${response.status}`);}
            if (data.success && data.sentMessage) { 
                const tempMsgEl = document.getElementById(optimisticMessage.id);
                if(tempMsgEl) tempMsgEl.remove(); 
                displayMessage(data.sentMessage); 
                if (data.sentMessage.id && parseInt(data.sentMessage.id) > lastDisplayedMessageId) { lastDisplayedMessageId = parseInt(data.sentMessage.id); }
            } else { alert('Pesan gagal terkirim: ' + (data.message || 'Kesalahan server.'));}
        } catch (error) { alert('Gagal mengirim pesan: ' + error.message); console.error('Error mengirim pesan (fetch):', error); }
    }
    sendMessageBtn.addEventListener('click', processSendMessage);
    messageInput.addEventListener('keypress', (e) => { if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); processSendMessage(); }});
    messageInput.addEventListener('input', function () { this.style.height = 'auto'; this.style.height = (this.scrollHeight) + 'px'; });

    if (currentUserId > 0) {
        startNotificationPolling();
    }
});
</script>
