<?php

// Hanya proses jika pengguna sudah login dan $appConfig tersedia
if (!isset($_SESSION['user_id'])) { 
    // Jika pengguna belum login, jangan tampilkan apa pun dari file ini.
    return; 
}

// $appConfig diharapkan sudah tersedia dari file yang meng-include footer.php 
// (yang kemudian meng-include file ini, dan footer.php di-include oleh BaseController).
// Jika $appConfig tidak ada, fitur chat tidak akan bisa mendapatkan URL atau ID admin dengan benar.
if (!isset($appConfig) || !is_array($appConfig)) {
    error_log("KRITIS: Variabel \$appConfig tidak tersedia atau bukan array di includes/chat_ui.php. Chat UI tidak akan dimuat dengan benar.");
    // Tampilkan pesan placeholder atau jangan tampilkan apa-apa jika config tidak ada.
    // Untuk development, ini bisa membantu:
    // echo "<p style='color:red; background:white; padding:10px; position:fixed; bottom:0; left:0; z-index:10000;'>Error: Konfigurasi Aplikasi untuk Chat tidak termuat!</p>";
    return; 
}

// Variabel PHP yang akan di-passing ke JavaScript
$base_url_chat = $appConfig['BASE_URL'] ?? '/'; 
$admin_chat_recipient_id = $appConfig['ADMIN_CHAT_RECIPIENT_ID'] ?? 0; 
$current_user_id_js = (int)($_SESSION['user_id'] ?? 0);
$current_user_nama_js = $_SESSION['user_nama'] ?? 'Pengguna'; // Nama pengguna saat ini untuk ditampilkan sebagai pengirim
$is_admin_js_php = ($_SESSION['is_admin'] ?? false); // Status admin pengguna saat ini

?>

<div id="chat-bubble-container" title="Buka Chat">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="30px" height="30px">
        <path d="M0 0h24v24H0V0z" fill="none"/>
        <path d="M21.99 4c0-1.1-.89-2-1.99-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h14l4 4-.01-18zM17.17 14H6v-2h11.17v2zm-1.17-3H6V9h10v2zm0-3H6V6h10v2z"/>
    </svg>
</div>

<div id="chat-window-container">
    <div class="chat-header-ui">
        <button id="chat-back-to-list-button" title="Kembali ke Daftar Pengguna" style="display: none; background: none; border: none; color: white; font-size: 22px; cursor: pointer; padding: 0 10px 0 0;">&larr;</button>
        <span id="chat-window-title-text">Chat</span>
        <button id="chat-close-button" title="Tutup Chat">&times;</button>
    </div>
    <div id="chat-messages-area">
        </div>
    <div class="chat-input-form-area">
        <textarea id="chat-text-input" placeholder="Ketik pesan Anda..." rows="1"></textarea>
        <button id="chat-send-button" title="Kirim Pesan">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="20px" height="20px">
                <path d="M0 0h24v24H0V0z" fill="none"/><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2 .01 7z"/>
            </svg>
        </button>
    </div>
</div>

<style>
    #chat-bubble-container{position:fixed;bottom:30px;right:30px;width:60px;height:60px;background-color:#007bff;color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0px 4px 12px rgba(0,0,0,0.25);z-index:10000;transition:transform .2s ease-in-out,background-color .2s}
    #chat-bubble-container:hover{transform:scale(1.1);background-color:#0056b3}
    #chat-window-container{position:fixed;bottom:100px;right:30px;width:350px;max-height:75vh;background-color:#fff;border-radius:10px;box-shadow:0px 5px 25px rgba(0,0,0,0.2);display:none;flex-direction:column;z-index:10001;border:1px solid #dee2e6;overflow:hidden}
    #chat-window-container.open{display:flex}
    .chat-header-ui{background-color:#007bff;color:white;padding:10px 15px;font-weight:bold;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid #0056b3}
    #chat-window-title-text{flex-grow:1;text-align:center;margin-left:5px;margin-right:5px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    #chat-back-to-list-button{font-size:22px; padding-right:10px;}
    #chat-back-to-list-button,#chat-close-button{background:none;border:none;color:white;font-weight:bold;cursor:pointer;padding-top:0; padding-bottom:0; line-height:1}
    #chat-close-button{font-size:28px; padding-left: 10px;}
    #chat-close-button:hover,#chat-back-to-list-button:hover{opacity:.8}
    #chat-messages-area{flex-grow:1;padding:15px;overflow-y:auto;background-color:#f8f9fa;display:flex;flex-direction:column}
    .message-item{max-width:80%;margin-bottom:10px;padding:10px 14px;border-radius:18px;word-wrap:break-word;font-size:.9rem;line-height:1.45;clear:both}
    .message-item.sent{background-color:#007bff;color:white;align-self:flex-end;border-bottom-right-radius:5px;margin-left:auto}
    .message-item.received{background-color:#e9ecef;color:#212529;align-self:flex-start;border-bottom-left-radius:5px;margin-right:auto}
    .message-sender-name{font-size:.75em;color:#6c757d;margin-bottom:3px}
    .message-timestamp{font-size:.7rem;margin-top:5px;text-align:right;display:block}
    .message-item.sent .message-timestamp{color:#dee2e6}
    .message-item.received .message-timestamp{color:#6c757d}
    .chat-input-form-area{display:flex;padding:10px 12px;border-top:1px solid #e0e0e0;background-color:#fff;align-items:flex-end}
    #chat-text-input{flex-grow:1;border:1px solid #ced4da;border-radius:18px;padding:10px 15px;font-size:.9rem;margin-right:8px;resize:none;min-height:22px;max-height:100px;overflow-y:auto;line-height:1.4}
    #chat-send-button{background-color:#007bff;color:white;border:none;border-radius:50%;width:42px;height:42px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background-color .2s;flex-shrink:0}
    #chat-send-button:hover{background-color:#0056b3}
    #chat-send-button svg{margin-left:2px}
    .chat-user-list{list-style:none;padding:0;margin:0; width:100%;}
    .chat-user-list li{padding:12px 15px;border-bottom:1px solid #f0f0f0;cursor:pointer;transition:background-color .2s;font-size:.9em}
    .chat-user-list li:hover{background-color:#f5f5f5}
    .chat-user-list li .user-email{font-size:.8em;color:#6c757d;display:block}
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
    const backToListBtn = document.getElementById('chat-back-to-list-button');

    if (!chatBubble || !chatWindow || !closeChatBtn || !messageInput || !sendMessageBtn || !messagesArea || !chatHeaderTitleEl || !backToListBtn) {
        console.error("Chat UI Error: Satu atau lebih elemen HTML utama untuk chat tidak ditemukan. Periksa semua ID elemen (chat-bubble-container, chat-window-container, chat-close-button, chat-text-input, chat-send-button, chat-messages-area, chat-window-title-text, chat-back-to-list-button).");
        return; 
    }
    console.log('Chat UI Script Loaded and DOM Ready! All main elements found.');

    const baseUrl = '<?php echo $base_url_chat; ?>';
    const defaultAdminId = parseInt('<?php echo $admin_chat_recipient_id; ?>');
    const currentUserId = parseInt('<?php echo $current_user_id_js; ?>');
    const currentUserName = '<?php echo addslashes($current_user_nama_js); ?>';
    const isAdmin = <?php echo $is_admin_js_php ? 'true' : 'false'; ?>;

    let currentPartnerId = 0;
    let currentPartnerName = '';
    const defaultChatWindowTitleForUser = `Chat dengan Admin`;
    const defaultChatWindowTitleForAdmin = 'Pilih Pengguna';

    function initializeChatState() {
        if (isAdmin) {
            currentPartnerId = 0; 
            currentPartnerName = '';
            chatHeaderTitleEl.textContent = defaultChatWindowTitleForAdmin;
            backToListBtn.style.display = 'none';
        } else {
            currentPartnerId = defaultAdminId;
            currentPartnerName = 'Admin'; 
            chatHeaderTitleEl.textContent = defaultChatWindowTitleForUser;
            backToListBtn.style.display = 'none';
        }
        console.log(`Init/Reset: isAdmin=${isAdmin}, currentPartnerId=${currentPartnerId}, PartnerName=${currentPartnerName}, DefaultAdminId=${defaultAdminId}, CurrentUserId=${currentUserId}`);
    }
    initializeChatState(); 

    function htmlspecialcharsJS(str) { // JS version of htmlspecialchars
        if (typeof str !== 'string') return '';
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return str.replace(/[&<>"']/g, (match) => map[match]);
    }

    function displayMessage(msg, isNewMessage = true) {
        const messageDiv = document.createElement('div');
        messageDiv.classList.add('message-item');
        if (msg.id && msg.id.toString().startsWith('temp_')) { // Beri ID untuk pesan optimis
            messageDiv.id = msg.id;
        }
        
        const contentDiv = document.createElement('div');
        contentDiv.classList.add('message-content');
        contentDiv.textContent = msg.message_text; // Teks dari server sudah di-htmlspecialchars jika perlu
                                                  // Teks dari input pengguna (optimis) juga harus aman

        const timestampDiv = document.createElement('div');
        timestampDiv.classList.add('message-timestamp');
        timestampDiv.textContent = msg.sent_at_formatted || new Date(msg.sent_at || Date.now()).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false });

        if (parseInt(msg.sender_id) === currentUserId) { 
            messageDiv.classList.add('sent'); 
        } else { 
            messageDiv.classList.add('received'); 
            // Opsional: Tampilkan nama pengirim jika bukan dari diri sendiri dan jika tersedia
            // if (msg.sender_nama && msg.sender_nama !== currentUserName) {
            //     const senderNameDiv = document.createElement('div');
            //     senderNameDiv.classList.add('message-sender-name');
            //     senderNameDiv.textContent = htmlspecialcharsJS(msg.sender_nama);
            //     messageDiv.insertBefore(senderNameDiv, contentDiv);
            // }
        }
        
        messageDiv.appendChild(contentDiv);
        messageDiv.appendChild(timestampDiv);

        messagesArea.appendChild(messageDiv); // Pesan baru selalu di akhir
        
        // Auto-scroll ke bawah jika pesan baru atau area sedang di-scroll ke bawah
        if (isNewMessage || (messagesArea.scrollHeight - messagesArea.scrollTop - messagesArea.clientHeight < 150) ) {
             messagesArea.scrollTop = messagesArea.scrollHeight; 
        }
    }
    
    async function fetchChatMessages(partnerIdToFetch) {
        if (partnerIdToFetch === 0 || (partnerIdToFetch === currentUserId && !isAdmin && partnerIdToFetch !== defaultAdminId) ) {
             messagesArea.innerHTML = '<p style="text-align:center; color:#aaa;">Partner chat tidak valid atau belum dipilih.</p>';
             return; 
        }
        messagesArea.innerHTML = '<p style="text-align:center; color:#aaa;">Memuat pesan...</p>';
        try {
            const response = await fetch(`${baseUrl}chat/fetchMessages?partner_id=${partnerIdToFetch}`);
            if (!response.ok) {
                 const errorText = await response.text();
                 throw new Error(`HTTP error! status: ${response.status} - ${errorText}`);
            }
            const data = await response.json();
            messagesArea.innerHTML = ''; 
            if (data.success && data.messages) {
                if (data.messages.length > 0) {
                    data.messages.forEach(msg => displayMessage(msg, false)); // Tampilkan semua sebagai riwayat
                } else { messagesArea.innerHTML = '<p style="text-align:center; color:#aaa;">Belum ada pesan. Mulai percakapan!</p>';}
                messagesArea.scrollTop = messagesArea.scrollHeight; // Scroll ke paling bawah setelah semua pesan dimuat
            } else { messagesArea.innerHTML = `<p style="text-align:center; color:red;">${htmlspecialcharsJS(data.message) || 'Gagal memuat pesan.'}</p>`;}
        } catch (error) { 
            messagesArea.innerHTML = '<p style="text-align:center; color:red;">Kesalahan memuat riwayat pesan.</p>'; 
            console.error('Error fetching messages:', error); 
        }
    }

    async function loadAdminChatUserList() {
        if (!isAdmin) return; 
        messagesArea.innerHTML = '<p style="text-align:center; color:#aaa;">Memuat daftar pengguna...</p>';
        chatHeaderTitleEl.textContent = defaultChatWindowTitleForAdmin;
        backToListBtn.style.display = 'none'; 
        currentPartnerId = 0; 
        currentPartnerName = '';
        try {
            const response = await fetch(`${baseUrl}chat/getAdminChatList`);
            if (!response.ok)  throw new Error(`HTTP error! status: ${response.status} - ${await response.text()}`);
            const data = await response.json();
            messagesArea.innerHTML = ''; 
            if (data.success && data.users) {
                if (data.users.length === 0) { messagesArea.innerHTML = '<p style="text-align:center; color:#aaa;">Tidak ada pengguna untuk diajak chat.</p>'; return; }
                const userListUl = document.createElement('ul'); userListUl.className = 'chat-user-list';
                data.users.forEach(user => {
                    if (parseInt(user.id) === currentUserId) return; // Admin tidak bisa chat dengan dirinya sendiri dari daftar ini
                    const listItem = document.createElement('li');
                    listItem.innerHTML = `${htmlspecialcharsJS(user.nama)} <span class="user-email">(${htmlspecialcharsJS(user.email)})</span>`;
                    listItem.dataset.userId = user.id; 
                    listItem.dataset.userName = user.nama;

                    listItem.addEventListener('click', () => {
                        currentPartnerId = parseInt(listItem.dataset.userId); 
                        currentPartnerName = listItem.dataset.userName;
                        chatHeaderTitleEl.textContent = `Chat dengan ${htmlspecialcharsJS(currentPartnerName)}`;
                        backToListBtn.style.display = 'inline-block'; 
                        fetchChatMessages(currentPartnerId); 
                    });
                    userListUl.appendChild(listItem);
                });
                messagesArea.appendChild(userListUl);
            } else { messagesArea.innerHTML = `<p style="text-align:center; color:red;">${htmlspecialcharsJS(data.message) || 'Gagal memuat daftar pengguna.'}</p>`;}
        } catch (error) { messagesArea.innerHTML = '<p style="text-align:center; color:red;">Kesalahan jaringan saat memuat daftar pengguna.</p>'; console.error('Error fetching admin chat list:', error);}
    }
    
    chatBubble.addEventListener('click', function() {
        chatWindow.classList.toggle('open');
        if (chatWindow.classList.contains('open')) {
            messageInput.focus();
            if (isAdmin) {
                if (currentPartnerId === 0) { loadAdminChatUserList(); }
                else { fetchChatMessages(currentPartnerId); chatHeaderTitleEl.textContent = `Chat dengan ${htmlspecialcharsJS(currentPartnerName)}`; backToListBtn.style.display = 'inline-block'; }
            } else { 
                if (currentPartnerId !== 0 && currentPartnerId !== currentUserId) { // User biasa chat dengan admin default
                    chatHeaderTitleEl.textContent = defaultChatWindowTitleForUser; backToListBtn.style.display = 'none';
                    fetchChatMessages(currentPartnerId);
                } else { // Admin ID tidak terkonfigurasi atau user mencoba chat dengan diri sendiri via admin ID
                    messagesArea.innerHTML = '<p style="text-align:center; color:orange;">Tidak dapat memulai chat. Admin tidak terkonfigurasi.</p>';
                    chatHeaderTitleEl.textContent = 'Chat Error'; backToListBtn.style.display = 'none';
                    console.warn(`Chat gagal dimulai untuk user biasa: currentPartnerId=${currentPartnerId}, currentUserId=${currentUserId}, defaultAdminId=${defaultAdminId}`);
                }
            }
        } else { 
            // Tidak melakukan reset title saat ditutup agar saat dibuka kembali, konteksnya tetap
        }
    });

    closeChatBtn.addEventListener('click', () => {
        chatWindow.classList.remove('open');
    });

    if (isAdmin) {
        backToListBtn.addEventListener('click', function() {
            loadAdminChatUserList(); 
        });
    }
    
    async function processSendMessage() {
        const messageText = messageInput.value.trim();
        if (messageText === '') return;

        let effectivePartnerId = currentPartnerId;
        if (!isAdmin && currentPartnerId === currentUserId) { // Jika user biasa dan partnerID adalah diri sendiri (karena admin ID = user ID)
            effectivePartnerId = defaultAdminId; // Pastikan user biasa selalu kirim ke admin default jika ada konflik
        }
        if (effectivePartnerId === 0 || (effectivePartnerId === currentUserId && !isAdmin)) {
            alert(isAdmin && effectivePartnerId === 0 ? 'Pilih pengguna untuk diajak chat terlebih dahulu.' : 'Tidak bisa mengirim pesan. Partner chat belum ditentukan atau tidak valid.');
            return;
        }

        const optimisticMessage = { 
            id: 'temp_' + Date.now(), 
            message_text: messageText, 
            sender_id: currentUserId, 
            sent_at: new Date().toISOString(), 
            sent_at_formatted: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false }), 
            sender_nama: currentUserName 
        };
        if (messagesArea.querySelector('.chat-user-list')) { // Jika sedang menampilkan daftar user, bersihkan dulu
            messagesArea.innerHTML = '';
        }
        displayMessage(optimisticMessage, false); 
        
        const originalMessageText = messageInput.value; 
        messageInput.value = ''; 
        messageInput.style.height = 'auto'; // Reset tinggi textarea
        messageInput.focus();
        
        try {
            const response = await fetch(`${baseUrl}chat/sendMessage`, { 
                method: 'POST', 
                headers: { 'Content-Type': 'application/json' }, 
                body: JSON.stringify({ receiver_id: effectivePartnerId, message_text: originalMessageText }) 
            });
            const data = await response.json(); 
            
            if (!response.ok) { 
                throw new Error(data.message || `Server error: ${response.status}`);
            }

            if (data.success && data.sentMessage) { 
                console.log('Server ACK:', data.sentMessage);
                // Hapus pesan optimis jika ada (memerlukan ID pada elemen HTML pesan optimis)
                // const tempMsgElement = document.getElementById(optimisticMessage.id);
                // if(tempMsgElement) tempMsgElement.remove();
                // displayMessage(data.sentMessage, false); // Tampilkan pesan dari server
                // Untuk sementara, pesan optimis sudah cukup, server hanya konfirmasi.
                // Jika ingin update ID, bisa modifikasi pesan optimis.
            } else { 
                alert('Pesan gagal terkirim: ' + (data.message || 'Kesalahan server.'));
                // messageInput.value = originalMessageText; // Kembalikan teks jika gagal
            }
        } catch (error) { 
            alert('Gagal mengirim pesan: ' + error.message); 
            console.error('Error mengirim pesan (fetch):', error); 
            // messageInput.value = originalMessageText; 
        }
    }
    sendMessageBtn.addEventListener('click', processSendMessage);
    messageInput.addEventListener('keypress', (e) => { if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); processSendMessage(); }});
    messageInput.addEventListener('input', function () { this.style.height = 'auto'; this.style.height = (this.scrollHeight) + 'px'; });

});
</script>