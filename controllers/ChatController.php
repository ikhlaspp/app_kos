<?php
// File: nama_proyek_kos/controllers/ChatController.php

class ChatController extends BaseController {
    private ChatModel $chatModel;
    private UserModel $userModel; // Pastikan ini dideklarasikan

    public function __construct(PDO $pdo, array $appConfig) {
        parent::__construct($pdo, $appConfig);
        $this->chatModel = new ChatModel($this->pdo);
        $this->userModel = new UserModel($this->pdo); // Inisialisasi UserModel
    }

     private function _updateUserActivity(): void {
        if (isset($_SESSION['user_id'])) {
            $this->userModel->updateUserLastActive((int)$_SESSION['user_id']);
        }
    }

    private function _getConversationId(int $userId1, int $userId2): string {
        $userKecil = min($userId1, $userId2);
        $userBesar = max($userId1, $userId2);
        return "{$userKecil}_{$userBesar}";
    }

    public function sendMessage(): void {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Anda harus login untuk mengirim pesan.']);
            return;
        }
        $this->_updateUserActivity(); 
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Metode tidak diizinkan.']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $messageText = trim($input['message_text'] ?? '');
        $receiverIdInput = filter_var($input['receiver_id'] ?? 0, FILTER_VALIDATE_INT);
        
        $receiverId = $receiverIdInput;
        if (empty($receiverId) && !($_SESSION['is_admin'] ?? false)) { // Jika user biasa dan receiver_id tidak ada, target admin default
            $receiverId = (int)($this->appConfig['ADMIN_CHAT_RECIPIENT_ID'] ?? 0);
        }

        if (empty($messageText)) {
            echo json_encode(['success' => false, 'message' => 'Pesan tidak boleh kosong.']);
            return;
        }
        if (empty($receiverId)) {
            echo json_encode(['success' => false, 'message' => 'Penerima tidak valid.']);
            return;
        }

        $senderId = (int)$_SESSION['user_id'];
        if ($senderId == $receiverId) {
             echo json_encode(['success' => false, 'message' => 'Tidak bisa mengirim pesan ke diri sendiri.']);
             return;
        }

        $conversationId = $this->_getConversationId($senderId, $receiverId);
        $messageSavedId = $this->chatModel->saveMessage($conversationId, $senderId, $receiverId, $messageText);

        if ($messageSavedId) {
            echo json_encode([
                'success' => true, 
                'message' => 'Pesan terkirim.',
                'sentMessage' => [
                    'id' => $messageSavedId,
                    'conversation_id' => $conversationId,
                    'sender_id' => $senderId,
                    'receiver_id' => $receiverId,
                    'message_text' => htmlspecialchars($messageText),
                    'sent_at' => date('Y-m-d H:i:s'), 
                    'sender_nama' => $_SESSION['user_nama'] ?? 'Saya',
                    'is_read' => false
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menyimpan pesan ke database.']);
        }
    }

    public function fetchMessages(): void {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'messages' => [], 'message' => 'Sesi tidak valid.']);
            return;
        }
        $this->_updateUserActivity();

        $partnerId = filter_input(INPUT_GET, 'partner_id', FILTER_VALIDATE_INT);
        $currentUserId = (int)$_SESSION['user_id'];

        if (empty($partnerId)) {
            if (!($_SESSION['is_admin'] ?? false)) { // Jika user biasa, partnernya adalah admin default
                $partnerId = (int)($this->appConfig['ADMIN_CHAT_RECIPIENT_ID'] ?? 0);
            }
            if (empty($partnerId)) {
                echo json_encode(['success' => false, 'messages' => [], 'message' => 'Partner chat tidak ditentukan.']);
                return;
            }
        }
        
        if ($partnerId == $currentUserId && !($_SESSION['is_admin'] ?? false)) { // User biasa tidak bisa fetch chat dengan diri sendiri via partner_id admin
            echo json_encode(['success' => false, 'messages' => [], 'message' => 'Tidak valid mengambil pesan diri sendiri.']);
            return;
        }

        $conversationId = $this->_getConversationId($currentUserId, $partnerId);
        $this->chatModel->markMessagesAsRead($conversationId, $currentUserId);
        $messages = $this->chatModel->getMessagesForConversation($conversationId);
        
        foreach ($messages as &$msg) { // Reference to modify original array
            $msg['message_text'] = htmlspecialchars($msg['message_text']);
            $msg['sender_nama'] = htmlspecialchars($msg['sender_nama']);
            $dt = new DateTime($msg['sent_at']);
            $msg['sent_at_formatted'] = $dt->format('H:i');
        }

        echo json_encode(['success' => true, 'messages' => $messages]);
    }

    public function fetchNewMessages(): void {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'messages' => [], 'message' => 'Sesi tidak valid.']);
            return;
        }
        $this->_updateUserActivity();
        
        $partnerId = filter_input(INPUT_GET, 'partner_id', FILTER_VALIDATE_INT);
        $lastMessageIdClientHas = filter_input(INPUT_GET, 'last_message_id', FILTER_VALIDATE_INT);
        $currentUserId = (int)$_SESSION['user_id'];
        // ... (logika untuk menentukan $partnerId jika kosong) ...
        if (empty($partnerId) || $lastMessageIdClientHas === null || $lastMessageIdClientHas < 0 || ($partnerId == $currentUserId && !($_SESSION['is_admin'] ?? false))) { /* ... error ... */ return; }


        $conversationId = $this->_getConversationId($currentUserId, $partnerId);
        // Model getNewMessagesForConversation sudah menghandle mark as read untuk pesan yang diambil
        $newMessages = $this->chatModel->getNewMessagesForConversation($conversationId, $currentUserId, $lastMessageIdClientHas);

        $formattedMessages = [];
        // ... (loop untuk format pesan dan htmlspecialchars seperti sebelumnya) ...
        foreach ($newMessages as $msg) {
            $dt = new DateTime($msg['sent_at']);
            $formattedMsg = $msg;
            $formattedMsg['message_text'] = htmlspecialchars($msg['message_text'] ?? '');
            $formattedMsg['sender_nama'] = htmlspecialchars($msg['sender_nama'] ?? 'Unknown');
            $formattedMsg['sent_at_formatted'] = $dt->format('H:i');
            $formattedMessages[] = $formattedMsg;
        }
        echo json_encode(['success' => true, 'messages' => $formattedMessages]);
    }

    public function getAdminChatList(): void {
        header('Content-Type: application/json');
        try {
            if (!isset($_SESSION['user_id']) || !($_SESSION['is_admin'] ?? false)) {
                echo json_encode(['success' => false, 'message' => 'Akses ditolak.', 'users' => []]);
                return;
            }
            if (!isset($this->userModel) || !($this->userModel instanceof UserModel) ) {
                 error_log("ChatController Fatal Error: Properti \$userModel tidak diinisialisasi!");
                 http_response_code(500);
                 echo json_encode(['success' => false, 'message' => 'Kesalahan server: Komponen pengguna tidak siap.', 'users' => []]);
                 return;
            }
            $users = $this->userModel->getAllNonAdminUsers();
            // Nanti bisa ditambahkan info jumlah pesan belum dibaca dari setiap user
            echo json_encode(['success' => true, 'users' => $users]);
        } catch (Throwable $e) { 
            error_log("ChatController::getAdminChatList Exception/Error: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            http_response_code(500); 
            echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan internal.', 'users' => []]);
        }
    }

    public function getUserOnlineStatus(): void {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'online' => false, 'last_active_text' => 'N/A']);
            return;
        }
        $this->_updateUserActivity(); // Update aktivitas diri sendiri dulu

        $partnerId = filter_input(INPUT_GET, 'partner_id', FILTER_VALIDATE_INT);
        if (empty($partnerId)) {
            echo json_encode(['success' => false, 'online' => false, 'last_active_text' => 'Partner tidak valid']);
            return;
        }

        $partnerData = $this->userModel->getUserActivityInfo($partnerId);
        if (!$partnerData) {
            echo json_encode(['success' => false, 'online' => false, 'last_active_text' => 'User tidak ditemukan']);
            return;
        }

        $online = false;
        $lastActiveText = 'Terakhir aktif: lama'; // Default

        if ($partnerData['last_active_at']) {
            $lastActiveTimestamp = strtotime($partnerData['last_active_at']);
            $currentTime = time();
            $minutesAgo = round(($currentTime - $lastActiveTimestamp) / 60);

            if ($minutesAgo < 5) { // Anggap online jika aktif dalam 5 menit terakhir
                $online = true;
                $lastActiveText = 'Online';
            } elseif ($minutesAgo < 60) {
                $lastActiveText = "Aktif {$minutesAgo} menit lalu";
            } elseif ($minutesAgo < (60 * 24)) {
                $lastActiveText = "Aktif " . round($minutesAgo/60) . " jam lalu";
            } else {
                $lastActiveText = "Aktif pada " . date('d M Y', $lastActiveTimestamp);
            }
        } else {
            $lastActiveText = 'Aktivitas tidak diketahui';
        }

        echo json_encode([
            'success' => true, 
            'user_id' => $partnerId,
            'online' => $online, 
            'last_active_text' => $lastActiveText,
            'partner_nama' => htmlspecialchars($partnerData['nama'] ?? 'User')
        ]);
    }

    public function checkUnreadSummary(): void {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'unread_count' => 0, 'message' => 'Sesi tidak valid.']);
            return;
        }
        $currentUserId = (int)$_SESSION['user_id'];

        error_log("DEBUG checkUnreadSummary: Called for user ID " . $currentUserId); // DEBUG PHP LOG

        $unreadCount = $this->chatModel->getUnreadMessageCount($currentUserId);
        error_log("DEBUG checkUnreadSummary: Unread count from model: " . $unreadCount); // DEBUG PHP LOG

        echo json_encode(['success' => true, 'unread_count' => $unreadCount]);
    }
    
}
?>