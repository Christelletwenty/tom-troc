<?php

class ConversationController
{
    private $conversationManager;
    private $messageManager;

    public function __construct($conversationManager, $messageManager)
    {
        $this->conversationManager = $conversationManager;
        $this->messageManager = $messageManager;
    }

    public function getConversationsForUserId($userId)
    {
        $conversations = $this->conversationManager->getConversationsSummaryByUserId($userId);
        return json_encode($conversations);
    }

    public function getParticipantsForConversationId($conversationId, $currentUserId)
    {
        if ($conversationId <= 0) {
            http_response_code(400);
            return json_encode(['error' => 'conversation_id invalide']);
        }

        // Vérifier que le user connecté participe à la conversation
        $participantsIds = $this->conversationManager->getParticipants($conversationId);
        if (!in_array($currentUserId, $participantsIds, true)) {
            http_response_code(403);
            return json_encode(['error' => 'Accès interdit à cette conversation']);
        }

        // On renvoie id + username + image
        $infos = $this->conversationManager->getParticipantInfos($conversationId);
        return json_encode($infos);
    }

    public function getConversationById($conversationId, $currentUserId)
    {
        if ($conversationId <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'conversation_id invalide']);
            return;
        }

        $participants = $this->conversationManager->getParticipants($conversationId);

        if (!in_array($currentUserId, $participants, true)) {
            http_response_code(403);
            echo json_encode([
                'error' => 'Vous ne participez pas à cette conversation'
            ]);
            return;
        }

        $messages = $this->messageManager->getMessagesByConversationId($conversationId);
        echo json_encode($messages);
        return;
    }

    public function markConversationAsRead($conversationId, $currentUserId)
    {
        $this->messageManager->readMessages($currentUserId, $conversationId);
        echo json_encode(['success' => true]);
    }

    public function createConversation($userId, $currentUserId)
    {
        if ($userId === $currentUserId) {
            http_response_code(400);
            return json_encode(['error' => 'Impossible de créer une conversation avec soi-même']);
        }

        $existingConvId = $this->conversationManager->findConversationBetweenUsers($currentUserId, $userId);

        if ($existingConvId !== null) {
            // On ne recrée pas, on renvoie juste l'ID existant
            http_response_code(200);
            return json_encode([
                'success'         => true,
                'conversation_id' => $existingConvId,
                'existing'        => true
            ]);
        }

        // On crée une conversation vide
        $newConversationId = $this->conversationManager->createConversation();

        // On ajoute le user connecté + l'autre user comme participants
        $this->conversationManager->addUserToConversation($newConversationId, $currentUserId);
        $this->conversationManager->addUserToConversation($newConversationId, $userId);

        http_response_code(201);
        return json_encode([
            'success'         => true,
            'conversation_id' => $newConversationId
        ]);
    }

    public function createMessage($currentUserId, $conversationId, $content)
    {
        $message = new Message();
        $message->setSenderId($currentUserId);
        $message->setConversationId($conversationId);
        $message->setContent($content);

        $this->messageManager->createMessage($message);

        http_response_code(201);
        return json_encode(['success' => true, 'message' => json_encode($message)]);
    }

    public function getUnreadForUserId($userUd)
    {
        $count = $this->messageManager->countUnreadMessages($userUd);

        return json_encode(['unread' => $count]);
    }
}
