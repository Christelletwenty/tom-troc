<?php
require_once '../config/database.php';
require_once '../managers/messageManager.php';

$messageManager = new MessageManager($db);

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['unread_count'])) {

    if (!isset($_SESSION['currentUserId'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Non connecté']);
        return;
    }

    $currentUserId = (int) $_SESSION['currentUserId'];

    $count = $messageManager->countUnreadMessages($currentUserId);

    echo json_encode(['unread' => $count]);
    return;
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_SESSION['currentUserId'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Non connecté']);
        return;
    }

    $senderId   = $_SESSION['currentUserId'];
    $content    = trim($_POST['content']);

    if ($content === '') {
        http_response_code(400);
        echo json_encode(['error' => 'Message vide']);
        return;
    }

    $message = new Message();
    $message->setSenderId($senderId);
    $message->setContent($content);

    $messageManager->createMessage($message);

    echo json_encode(['success' => 'Message envoyé']);
    return;
}
