<?php
require_once '../config/database.php';
require_once '../managers/BooksManager.php';
require_once '../controllers/BooksController.php';

require_once '../managers/ConversationManager.php';
require_once '../managers/MessageManager.php';
require_once '../controllers/ConversationsController.php';

require_once '../managers/UserManager.php';
require_once '../controllers/UserController.php';

$booksManager = new BooksManager($db);
$bookController = new BooksController($booksManager);

$userManager = new UserManager($db);
$userController = new UserController($userManager, $booksManager);

$conversationManager = new ConversationManager($db);
$messageManager = new MessageManager($db);
$conversationsController = new ConversationController($conversationManager, $messageManager);

$page = $_GET['page'] ?? '';

switch ($page) {
    case 'books':
        $bookController->booksRoute();
        break;

    case 'conversations':
        $conversationsController->conversationsRoute();
        break;

    case 'login':
        $userController->loginRoute();
        break;

    case 'messages':
        $conversationsController->messagesRoute();
        break;

    case 'profile':
        $userController->profileRoute();
        break;

    case 'upload_avatar':
        $userController->uploadAvatarRoute();
        break;

    case 'user':
        $userController->userRoute();
        break;

    default:
        http_response_code(404);
        echo json_encode(["error" => "Page not found"]);
        break;
}
