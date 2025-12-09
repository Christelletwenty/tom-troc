<?php
require_once '../models/conversationModel.php';

class ConversationManager
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Crée une conversation vide.
     * Retourne l'ID de la nouvelle conversation.
     */
    public function createConversation(): int
    {
        $sql = "INSERT INTO conversation () VALUES ()";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return (int) $this->db->lastInsertId();
    }

    /**
     * Ajoute un utilisateur comme participant à une conversation
     */
    public function addUserToConversation(int $conversationId, int $userId): void
    {
        $sql = "INSERT INTO conversation_user (conversation_id, user_id)
                VALUES (:conversation_id, :user_id)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'conversation_id' => $conversationId,
            'user_id'         => $userId
        ]);
    }

    /**
     * Récupère toutes les conversations où participe un user
     */
    public function getConversationsByUserId(int $userId): array
    {
        $sql = "SELECT c.*
                FROM conversation c
                INNER JOIN conversation_user cu ON cu.conversation_id = c.id
                WHERE cu.user_id = :user_id
                ORDER BY c.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Conversation');
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll();
    }

    /**
     * Récupère la liste des participants d'une conversation
     */
    public function getParticipants(int $conversationId): array
    {
        $sql = "SELECT user_id
                FROM conversation_user
                WHERE conversation_id = :conversation_id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['conversation_id' => $conversationId]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getParticipantNames(int $conversationId): array
    {
        $sql = "SELECT u.username
                FROM conversation_user cu
                INNER JOIN user u ON u.id = cu.user_id
                WHERE cu.conversation_id = :conversation_id
                ORDER BY u.username ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'conversation_id' => $conversationId,
        ]);

        // Retourne simplement un tableau de strings (usernames)
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Vérifie si une conversation existe
     */
    public function conversationExists(int $conversationId): bool
    {
        $sql = "SELECT id FROM conversation WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $conversationId]);

        return (bool) $stmt->fetchColumn();
    }
}
?>
