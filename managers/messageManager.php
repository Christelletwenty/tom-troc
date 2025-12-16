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

    //Récupérer les messages non lu
    public function countUnreadMessages(int $userId): int
    {
        $countUnreadMsgRequest = " SELECT COUNT(m.id) FROM message m JOIN conversation_user cu ON cu.conversation_id = m.conversation_id WHERE cu.user_id = :userId AND m.user_id != :userId AND m.read_at IS NULL ";

        $countUnreadMsgStmt = $this->db->prepare($countUnreadMsgRequest);
        $countUnreadMsgStmt->execute(['userId' => $userId]);

        return (int) $countUnreadMsgStmt->fetchColumn();
    }

    // Marquer les messages comme lu
    public function readMessages($currentUserId, $conversationId)
    {
        $readMessagesRequest = "UPDATE message
                SET read_at = NOW()
                WHERE conversation_id = :conversation_id AND user_id != :user_id";

        $readMessagesStmt = $this->db->prepare($readMessagesRequest);
        $readMessagesStmt->execute([
            'conversation_id' => $conversationId,
            'user_id'         => $currentUserId
        ]);
    }
}
