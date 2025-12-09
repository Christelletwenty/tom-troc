<?php
require_once '../models/messageModel.php';

class MessageManager
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Envoie un message dans une conversation
     */
    public function createMessage(Message $message): void
    {
        $sql = "INSERT INTO message (conversation_id, user_id, content)
                VALUES (:conversation_id, :user_id, :content)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'conversation_id' => $message->getConversationId(),
            'user_id'         => $message->getSenderId(),
            'content'         => $message->getContent(),
        ]);
    }

    /**
     * Récupère tous les messages d'une conversation
     */
    public function getMessagesByConversationId(int $conversationId): array
    {
        $sql = "SELECT *, user_id as sender_id
                FROM message
                WHERE conversation_id = :conversation_id
                ORDER BY created_at ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Message');
        $stmt->execute(['conversation_id' => $conversationId]);

        return $stmt->fetchAll();
    }

    /**
     * Marquer un message comme lu
     */
    public function markAsRead(int $messageId): void
    {
        $sql = "UPDATE message
                SET read_at = NOW()
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $messageId]);
    }
}
?>
