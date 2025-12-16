<?php
require_once '../config/database.php';
require_once '../managers/messageManager.php';

$messageManager = new MessageManager($db);

// Affichage du badge de notification (nombres de messages non lus)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['unread_count'])) {

    // Vérifie que l'utilisateur est connecté
    if (!isset($_SESSION['currentUserId'])) {
        // Si non connecté -> 404 Unauthorized
        http_response_code(401);
        echo json_encode(['error' => 'Non connecté']);
        return;
    }
    //Récupère l'id de l'utilisateur connecté depuis la session
    $currentUserId = (int) $_SESSION['currentUserId'];
    //Appelle le manager pour compter les messages non lus
    $count = $messageManager->countUnreadMessages($currentUserId);

    echo json_encode(['unread' => $count]);
    return;
    //Envoi d'un message
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    //Vérifie que l'utilisateur est connecté
    if (!isset($_SESSION['currentUserId'])) {
        //Si non connecté -> 404 Unauthorized
        http_response_code(401);
        echo json_encode(['error' => 'Non connecté']);
        return;
    }

    //L'expéditeur du message est l'utilisateur connecté
    $senderId   = $_SESSION['currentUserId'];
    //Récupère le contenu du message
    $content    = trim($_POST['content']);

    //Vérifie que le message n'est pas vide
    if ($content === '') {
        http_response_code(400);
        echo json_encode(['error' => 'Message vide']);
        return;
    }

    //Création d'un nouvel obejt Message en définissant l'expéditeur et le contenu
    $message = new Message();
    $message->setSenderId($senderId);
    $message->setContent($content);

    $messageManager->createMessage($message);

    echo json_encode(['success' => 'Message envoyé']);
    return;
}
