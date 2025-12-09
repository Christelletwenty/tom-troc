<?php
require_once '../config/database.php';
require_once '../managers/conversationManager.php';
require_once '../managers/messageManager.php';
require_once '../models/messageModel.php';

header('Content-Type: application/json; charset=utf-8');

$conversationManager = new ConversationManager($db);
$messageManager      = new MessageManager($db);

/**
 * Helper : vérifier que l'user est connecté
 */
function requireAuth(): int {
    if (!isset($_SESSION['currentUserId'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Non connecté']);
        exit;
    }
    return (int) $_SESSION['currentUserId'];
}

/**
 * ============================
 *  GET /api/conversations.php
 * ============================
 * - Sans paramètre : liste des conversations du user connecté
 * - Avec ?conversation_id=X : liste des messages de cette conversation
 */
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $currentUserId = requireAuth();

    if (isset($_GET['conversation_id']) && isset($_GET['participants'])) {
        $conversationId = (int) $_GET['conversation_id'];

        if ($conversationId <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'conversation_id invalide']);
            exit;
        }

        // (Optionnel) vérifier que le user connecté participe à cette conversation
        // pour éviter qu'il voie les participants d'une conversation où il n'est pas
        $participantsIds = $conversationManager->getParticipants($conversationId);
        if (!in_array($currentUserId, $participantsIds, true)) {
            http_response_code(403);
            echo json_encode(['error' => 'Accès interdit à cette conversation']);
            exit;
        }

        $names = $conversationManager->getParticipantNames($conversationId);
        echo json_encode($names);
        exit;
    }

    // 1) GET /api/conversations.php?conversation_id=123
    if (isset($_GET['conversation_id'])) {
        $conversationId = (int) $_GET['conversation_id'];

        if ($conversationId <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'conversation_id invalide']);
            exit;
        }

        $participants = $conversationManager->getParticipants($conversationId);

        if (!in_array($currentUserId, $participants, true)) {
            http_response_code(403);
            echo json_encode([
                'error' => 'Vous ne participez pas à cette conversation'
            ]);
            exit;
        }

        $messages = $messageManager->getMessagesByConversationId($conversationId);
        echo json_encode($messages);
        exit;
    }

    // 2) GET /api/conversations.php
    //    → Liste des conversations du user connecté
    $conversations = $conversationManager->getConversationsByUserId($currentUserId);
    echo json_encode($conversations);
    exit;
}

/**
 * ============================
 *  POST /api/conversations.php
 * ============================
 * - Avec user_id : créer une conversation entre moi et ce user
 * - Avec conversation_id + content : créer un message dans la conversation
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $currentUserId = requireAuth();

    // On récupère les données POST de façon safe
    $userId         = isset($_POST['user_id']) ? (int) $_POST['user_id'] : 0;
    $conversationId = isset($_POST['conversation_id']) ? (int) $_POST['conversation_id'] : 0;
    $content        = isset($_POST['content']) ? trim($_POST['content']) : '';

    // 1) Cas : création d'une nouvelle conversation
    //    POST /api/conversations.php avec user_id=X
    if ($userId > 0 && $conversationId === 0 && $content === '') {

        if ($userId === $currentUserId) {
            http_response_code(400);
            echo json_encode(['error' => 'Impossible de créer une conversation avec soi-même']);
            exit;
        }

        // TODO : Si une conversation existe deja entre ces 2 users, on en créé pas une autre et on renvoie juste l'ID

        // On crée une conversation vide
        $newConversationId = $conversationManager->createConversation();

        // On ajoute le user connecté + l'autre user comme participants
        $conversationManager->addUserToConversation($newConversationId, $currentUserId);
        $conversationManager->addUserToConversation($newConversationId, $userId);

        http_response_code(201);
        echo json_encode([
            'success'         => true,
            'conversation_id' => $newConversationId
        ]);
        exit;
    }

    // 2) Cas : création d'un message dans une conversation
    //    POST /api/conversations.php avec conversation_id=X & content=...
    if ($conversationId > 0 && $content !== '') {

        // TODO (optionnel) : vérifier que $currentUserId est bien participant de cette conversation

        $message = new Message();
        $message->setSenderId($currentUserId);
        $message->setConversationId($conversationId);
        $message->setContent($content);

        $messageManager->createMessage($message);

        http_response_code(201);
        echo json_encode(['success' => true, 'message' => json_encode($message)]);
        exit;
    }

    // 3) Sinon : requête POST invalide
    http_response_code(400);
    echo json_encode(['error' => 'Paramètres POST invalides']);
    exit;
}

// Méthode HTTP non gérée
http_response_code(405);
echo json_encode(['error' => 'Méthode non autorisée']);
exit;
