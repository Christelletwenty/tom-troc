<?php
require_once '../models/messageModel.php';

class MessageManager
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    //Envoie un message dans une conversation
    public function createMessage(Message $message): void
    {
        $createMsgRequest = "INSERT INTO message (conversation_id, user_id, content)
                VALUES (:conversation_id, :user_id, :content)";

        $createMsgStmt = $this->db->prepare($createMsgRequest);
        $createMsgStmt->execute([
            'conversation_id' => $message->getConversationId(),
            'user_id'         => $message->getSenderId(),
            'content'         => $message->getContent(),
        ]);
    }

    //Récupère tous les messages d'une conversation
    public function getMessagesByConversationId(int $conversationId): array
    {
        $getMessagesByConvIdRequest = "SELECT *, user_id as sender_id
                FROM message
                WHERE conversation_id = :conversation_id
                ORDER BY created_at ASC";

        $getMessagesByConvIdStmt = $this->db->prepare($getMessagesByConvIdRequest);
        $getMessagesByConvIdStmt->setFetchMode(PDO::FETCH_CLASS, 'Message');
        $getMessagesByConvIdStmt->execute(['conversation_id' => $conversationId]);

        return $getMessagesByConvIdStmt->fetchAll();
    }

    //Marquer un message comme lu
    public function markAsRead(int $messageId): void
    {
        $markAsReadRequest = "UPDATE message
                SET read_at = NOW()
                WHERE id = :id";

        $markAsReadStmt = $this->db->prepare($markAsReadRequest);
        $markAsReadStmt->execute(['id' => $messageId]);
    }
}
?>
