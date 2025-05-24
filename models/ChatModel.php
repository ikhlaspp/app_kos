<?php
// File: nama_proyek_kos/models/ChatModel.php

class ChatModel {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function saveMessage(string $conversationId, int $senderId, int $receiverId, string $messageText): ?string {
        $sql = "INSERT INTO chat_messages (conversation_id, sender_id, receiver_id, message_text)
                VALUES (:conversation_id, :sender_id, :receiver_id, :message_text)";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':conversation_id', $conversationId);
            $stmt->bindParam(':sender_id', $senderId, PDO::PARAM_INT);
            $stmt->bindParam(':receiver_id', $receiverId, PDO::PARAM_INT);
            $stmt->bindParam(':message_text', $messageText);

            if ($stmt->execute()) {
                return $this->pdo->lastInsertId();
            }
            error_log("ChatModel::saveMessage execute() returned false. Info: " . implode(", ", $stmt->errorInfo()));
            return null; // Ubah ke null agar lebih jelas bedanya dengan string ID
        } catch (PDOException $e) {
            $logMessage = "ChatModel::saveMessage PDOException: " . $e->getMessage() . "\n";
            $logMessage .= "SQL Query: " . $sql . "\n";
            $logMessage .= "Params: convID={$conversationId}, sender={$senderId}, receiver={$receiverId}\n";
            error_log($logMessage);
            return null;
        }
    }

    public function getMessagesForConversation(string $conversationId, int $limit = 50, int $offset = 0): array {
        $sql = "SELECT cm.id, cm.sender_id, cm.receiver_id, cm.message_text, cm.sent_at, cm.is_read, 
                       u_sender.nama as sender_nama 
                FROM chat_messages cm
                JOIN users u_sender ON cm.sender_id = u_sender.id
                WHERE cm.conversation_id = :conversation_id
                ORDER BY cm.sent_at ASC 
                LIMIT :limit OFFSET :offset"; 
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':conversation_id', $conversationId);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("ChatModel::getMessagesForConversation Error: " . $e->getMessage());
            return [];
        }
    }

    public function markMessagesAsRead(string $conversationId, int $receiverId): bool {
        $sql = "UPDATE chat_messages 
                SET is_read = TRUE 
                WHERE conversation_id = :conversation_id 
                  AND receiver_id = :receiver_id 
                  AND is_read = FALSE";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':conversation_id', $conversationId);
            $stmt->bindParam(':receiver_id', $receiverId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("ChatModel::markMessagesAsRead Error: " . $e->getMessage());
            return false;
        }
    }

    
}
?>