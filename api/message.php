<?php
require_once '../config/database.php';
require_once '../managers/messageManager.php';

$messageManager = new MessageManager($db);

//Récupération la conversation avec un user
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['user_id'])) {

    if (!isset($_SESSION['currentUserId'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Non connecté']);
        return;
    }

    $currentUserId = $_SESSION['currentUserId'];
    $otherUserId   = (int) $_GET['user_id'];

    $messages = $messageManager->getConversation($currentUserId, $otherUserId);

    echo json_encode($messages);
    return;
    //Récupération de tous MES messages
} else if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['mine'])) {

    if (!isset($_SESSION['currentUserId'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Non connecté']);
        return;
    }

    $currentUserId = $_SESSION['currentUserId'];

    $messages = $messageManager->getMessagesByUserId($currentUserId);

    echo json_encode($messages);
    return;
    //Envoyer un message
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_SESSION['currentUserId'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Non connecté']);
        return;
    }

    $senderId   = $_SESSION['currentUserId'];
    $receiverId = (int) $_POST['receiver_id'];
    $bookId     = isset($_POST['book_id']) ? (int) $_POST['book_id'] : null;
    $content    = trim($_POST['content']);

    if ($content === '') {
        http_response_code(400);
        echo json_encode(['error' => 'Message vide']);
        return;
    }

    $message = new Message();
    $message->setSenderId($senderId);
    $message->setReceiverId($receiverId);
    $message->setBookId($bookId);
    $message->setContent($content);

    $messageManager->createMessage($message);

    echo json_encode(['success' => 'Message envoyé']);
    return;
}
?>