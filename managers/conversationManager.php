<?php
require_once '../models/conversationModel.php';

class ConversationManager
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }


    //Crée une conversation vide.
    //Retourne l'ID de la nouvelle conversation.
    public function createConversation(): int
    {
        $createConvRequest = "INSERT INTO conversation () VALUES ()";
        $createConvStmt = $this->db->prepare($createConvRequest);
        $createConvStmt->execute();

        return (int) $this->db->lastInsertId();
    }

    //Ajoute un utilisateur comme participant à une conversation
    public function addUserToConversation(int $conversationId, int $userId): void
    {
        $addUserToConvRequest = "INSERT INTO conversation_user (conversation_id, user_id)
                VALUES (:conversation_id, :user_id)";

        $addUserToConvStmt = $this->db->prepare($addUserToConvRequest);
        $addUserToConvStmt->execute([
            'conversation_id' => $conversationId,
            'user_id'         => $userId
        ]);
    }

    //Récupère toutes les conversations où participe un user
    public function getConversationsByUserId(int $userId): array
    {
        $getConvByUserIdRequest = "SELECT c.*
                FROM conversation c
                INNER JOIN conversation_user cu ON cu.conversation_id = c.id
                WHERE cu.user_id = :user_id
                ORDER BY c.created_at DESC";

        $getConvByUserIdStmt = $this->db->prepare($getConvByUserIdRequest);
        $getConvByUserIdStmt->setFetchMode(PDO::FETCH_CLASS, 'Conversation');
        $getConvByUserIdStmt->execute(['user_id' => $userId]);

        return $getConvByUserIdStmt->fetchAll();
    }

    //Récupère la liste des participants d'une conversation
    public function getParticipants(int $conversationId): array
    {
        $getParticipantRequest = "SELECT user_id
                FROM conversation_user
                WHERE conversation_id = :conversation_id";

        $getParticipantStmt = $this->db->prepare($getParticipantRequest);
        $getParticipantStmt->execute(['conversation_id' => $conversationId]);

        return $getParticipantStmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getParticipantNames(int $conversationId): array
    {
        $getParticipantNamesRequest = "SELECT u.username
                FROM conversation_user cu
                INNER JOIN user u ON u.id = cu.user_id
                WHERE cu.conversation_id = :conversation_id
                ORDER BY u.username ASC";

        $getParticipantNamesStmt = $this->db->prepare($getParticipantNamesRequest);
        $getParticipantNamesStmt->execute([
            'conversation_id' => $conversationId,
        ]);

        // Retourne simplement un tableau de strings (usernames)
        return $getParticipantNamesStmt->fetchAll(PDO::FETCH_COLUMN);
    }


    //Vérifie si une conversation existe
    public function conversationExists(int $conversationId): bool
    {
        $conversationExistsRequest = "SELECT id FROM conversation WHERE id = :id";
        $conversationExistsStmt = $this->db->prepare($conversationExistsRequest);
        $conversationExistsStmt->execute(['id' => $conversationId]);

        return (bool) $conversationExistsStmt->fetchColumn();
    }

    //Récupère le last message
    public function getConversationsSummaryByUserId(int $userId): array
    {
        $getConvSumByUserIdRequest = " SELECT c.id, c.created_at,
                -- date du dernier message
                (
                    SELECT m2.created_at
                    FROM message m2
                    WHERE m2.conversation_id = c.id
                    ORDER BY m2.created_at DESC
                    LIMIT 1
                ) AS last_message_at,
                
                -- contenu du dernier message
                (
                    SELECT m3.content
                    FROM message m3
                    WHERE m3.conversation_id = c.id
                    ORDER BY m3.created_at DESC
                    LIMIT 1
                ) AS last_message_content
            FROM conversation c
            INNER JOIN conversation_user cu 
                ON cu.conversation_id = c.id
            WHERE cu.user_id = :user_id
            ORDER BY 
                COALESCE(
                    (
                        SELECT m4.created_at
                        FROM message m4
                        WHERE m4.conversation_id = c.id
                        ORDER BY m4.created_at DESC
                        LIMIT 1
                    ),
                    c.created_at
                ) DESC
        ";

        $getConvSumByUserIdStmt = $this->db->prepare($getConvSumByUserIdRequest);
        $getConvSumByUserIdStmt->execute(['user_id' => $userId]);

        return $getConvSumByUserIdStmt->fetchAll(PDO::FETCH_ASSOC);
    }


    //Récupère les conversations entre deux users
    public function findConversationBetweenUsers(int $user1Id, int $user2Id): ?int
    {
        $findConvBetweenUserRequest = " SELECT c.id FROM conversation c INNER JOIN conversation_user cu1 ON cu1.conversation_id = c.id AND cu1.user_id = :u1 INNER JOIN conversation_user cu2 ON cu2.conversation_id = c.id AND cu2.user_id = :u2 LIMIT 1";

        $findConvBetweenUserStmt = $this->db->prepare($findConvBetweenUserRequest);
        $findConvBetweenUserStmt->execute([
            'u1' => $user1Id,
            'u2' => $user2Id,
        ]);

        $convId = $findConvBetweenUserStmt->fetchColumn();

        return $convId !== false ? (int) $convId : null;
    }

    public function getParticipantInfos(int $conversationId): array
    {
        $getParticipantsInfoRequest = "
            SELECT u.id, u.username, u.image
            FROM conversation_user cu
            JOIN user u ON u.id = cu.user_id
            WHERE cu.conversation_id = :conversation_id
        ";

        $getParticipantsInfoStmt = $this->db->prepare($getParticipantsInfoRequest);
        $getParticipantsInfoStmt->execute(['conversation_id' => $conversationId]);

        return $getParticipantsInfoStmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
