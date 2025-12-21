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
        $message = new MessageModel();
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

    public function messagesRoute()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if (!isset($_SESSION['currentUserId'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Non connecté']);
            return;
        }
        $currentUserId = (int) $_SESSION['currentUserId'];

        if ($method === 'GET') {
            if (isset($_GET['unread_count'])) {
                echo $this->getUnreadForUserId($currentUserId);
                return;
            }
            return;
        }
    }

    public function conversationsRoute()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if (!isset($_SESSION['currentUserId'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Non connecté']);
            return;
        }
        $currentUserId = (int) $_SESSION['currentUserId'];

        if ($method === 'GET') {
            if (isset($_GET['conversation_id']) && isset($_GET['participants'])) {

                $conversationId = (int) $_GET['conversation_id'];
                echo $this->getParticipantsForConversationId($conversationId, $currentUserId);
                return;
            }

            if (isset($_GET['conversation_id'])) {
                $conversationId = (int) $_GET['conversation_id'];

                echo $this->getConversationById($conversationId, $currentUserId);
                return;
            }

            // GET par défaut sans params, on renvoie la liste des conv du user connecté avec le dernier message etc...
            echo $this->getConversationsForUserId($currentUserId);
            return;
        } else if ($method === 'POST') {
            if (isset($_POST['read_conversation_id'])) {
                $conversationId = (int) $_POST['read_conversation_id'];

                echo $this->markConversationAsRead($conversationId, $currentUserId);
                return;
            }

            $userId         = isset($_POST['user_id']) ? (int) $_POST['user_id'] : 0;
            $conversationId = isset($_POST['conversation_id']) ? (int) $_POST['conversation_id'] : 0;
            $content        = isset($_POST['content']) ? trim($_POST['content']) : '';

            //Création d'une nouvelle conversation
            //POST /api/conversations.php avec user_id=X
            if ($userId > 0 && $conversationId === 0 && $content === '') {
                echo $this->createConversation($userId, $currentUserId);
                return;
            }

            // Création d'un message dans une conversation
            // POST /api/conversations.php avec conversation_id=X & content=...
            if ($conversationId > 0 && $content !== '') {
                echo $this->createMessage($currentUserId, $conversationId, $content);
                return;
            }

            //Sinon : requête POST invalide
            http_response_code(400);
            echo json_encode(['error' => 'Paramètres POST invalides']);
            return;
        }
    }
}
