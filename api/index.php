<?php
require_once '../config/database.php';
require_once '../managers/livreManager.php';
require_once '../controllers/BooksController.php';

require_once '../managers/conversationManager.php';
require_once '../managers/messageManager.php';
require_once '../controllers/ConversationsController.php';

require_once '../managers/userManager.php';
require_once '../controllers/UserController.php';

$booksManager = new LivreManager($db);
$bookController = new BooksController($booksManager);

$userManager = new UserManager($db);
$userController = new UserController($userManager, $booksManager);

$conversationManager = new ConversationManager($db);
$messageManager = new MessageManager($db);
$conversationsController = new ConversationController($conversationManager, $messageManager);

// TODO aurait sa place dans une classe Util
function requireAuth()
{
    if (!isset($_SESSION['currentUserId'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Non connecté']);
        return;
    }
    return (int) $_SESSION['currentUserId'];
}

$page = $_GET['page'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

switch ($page) {
    case 'books':
        if ($method === 'POST') {
            $currentUserId = requireAuth();

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image']) && isset($_POST['book_id'])) {
                //Récupèration de l'id du livre et du fichier uploadé
                $bookId = (int) $_POST['book_id'];
                $file   = $_FILES['image'];

                $bookController->addImageForBookId($file, $bookId);
                return;
            } else if (
                isset($_POST['titre'])
                && isset($_POST['auteur'])
                && isset($_POST['description'])
                && !isset($_POST['id'])
            ) {
                $titre = $_POST['titre'];
                $auteur = $_POST['auteur'];
                $description = $_POST['description'];
                $dispo = isset($_POST['dispo']) ? $_POST['dispo'] : 1;
                $imagePath = ''; // chemin de l'image et par défaut : pas d'image
                $file = null;

                if (!empty($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $file = $_FILES;
                }

                echo $bookController->addBook($titre, $auteur, $description, $dispo, $currentUserId, $file);
                return;
            } else if (isset($_POST['id']) && isset($_POST['titre']) && isset($_POST['auteur']) && isset($_POST['image']) && isset($_POST['description']) && isset($_POST['dispo'])) {
                //Récupération des données du livre
                $id = $_POST['id'];
                $titre = $_POST['titre'];
                $auteur = $_POST['auteur'];
                $image = $_POST['image'];
                $description = $_POST['description'];
                $dispo = $_POST['dispo'];

                echo $bookController->updateBook($id, $titre, $auteur, $image, $description, $dispo, $currentUserId);
                return;
            } else if (isset($_POST['id'])) {
                $id = $_POST['id'];

                echo $bookController->deleteBook($id);
                return;
            }
        } else if ($method === 'GET') {
            if (isset($_GET['user_id'])) {
                echo $bookController->getBooksByUserId($_GET['user_id']);
                return;

                //récupérer livre par id
            } else if (isset($_GET['id'])) {
                echo $bookController->getBookById($_GET['id']);
                return;
            } else {
                echo $bookController->getAllBooks();
                return;
            }
        }
        break;

    case 'conversations':
        $currentUserId = requireAuth();

        if ($method === 'GET') {
            if (isset($_GET['conversation_id']) && isset($_GET['participants'])) {

                $conversationId = (int) $_GET['conversation_id'];
                echo $conversationsController->getParticipantsForConversationId($conversationId, $currentUserId);
                return;
            }

            if (isset($_GET['conversation_id'])) {
                $conversationId = (int) $_GET['conversation_id'];

                echo $conversationsController->getConversationById($conversationId, $currentUserId);
                return;
            }

            // GET par défaut sans params, on renvoie la liste des conv du user connecté avec le dernier message etc...
            echo $conversationsController->getConversationsForUserId($currentUserId);
            return;
        } else if ($method === 'POST') {
            if (isset($_POST['read_conversation_id'])) {
                $conversationId = (int) $_POST['read_conversation_id'];

                echo $conversationsController->markConversationAsRead($conversationId, $currentUserId);
                return;
            }

            $userId         = isset($_POST['user_id']) ? (int) $_POST['user_id'] : 0;
            $conversationId = isset($_POST['conversation_id']) ? (int) $_POST['conversation_id'] : 0;
            $content        = isset($_POST['content']) ? trim($_POST['content']) : '';

            //Création d'une nouvelle conversation
            //POST /api/conversations.php avec user_id=X
            if ($userId > 0 && $conversationId === 0 && $content === '') {
                echo $conversationsController->createConversation($userId, $currentUserId);
                return;
            }

            // Création d'un message dans une conversation
            // POST /api/conversations.php avec conversation_id=X & content=...
            if ($conversationId > 0 && $content !== '') {
                echo $conversationsController->createMessage($currentUserId, $conversationId, $content);
                return;
            }

            //Sinon : requête POST invalide
            http_response_code(400);
            echo json_encode(['error' => 'Paramètres POST invalides']);
            return;
        }
        break;

    case 'login':
        if ($method === 'POST') {
            //Connexion d'un user l'email et le password doivent être fournis
            if (isset($_POST['email']) && isset($_POST['password'])) {
                $email = $_POST['email'];
                $password = $_POST['password'];

                echo $userController->login($email, $password);
                return;
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Identifiants et mot de passe requis' . json_encode($_POST) . json_encode($_SESSION)]);
                return;
            }
        } else if ($method === 'GET') {
            echo $userController->getConnectedUser();
            return;
        }
        break;

    case 'messages':
        $currentUserId = requireAuth();

        if ($method === 'GET') {
            if (isset($_GET['unread_count'])) {
                echo $conversationsController->getUnreadForUserId($currentUserId);
                return;
            }
            return;
        }
        break;

    case 'profile':
        $currentUserId = requireAuth();
        echo $userController->getUserProfileById($currentUserId);
        break;

    case 'upload_avatar':
        $currentUserId = requireAuth();
        //Vérif que fichier avatar existe + pas d'erreur d'upload
        if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
            header('Location: ../account.php');
            exit;
        }
        //Récupération du fichier uploadé
        $file = $_FILES['avatar'];

        //Types autorisés d'uplaod et empêche les autres
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes, true)) {
            header('Location: ../account.php');
            exit;
        }

        //Taille limite + stockage 
        if ($file['size'] > 2 * 1024 * 1024) {
            header('Location: ../account.php');
            exit;
        }

        $userController->uploadAvatar($currentUserId, $file);

        break;

    case 'user':
        if ($method === 'POST') {
            $currentUserId = requireAuth();

            if (
                isset($_POST['username'])
                && isset($_POST['email'])
                && isset($_POST['password'])
            ) {

                $username = $_POST['username'];
                $email    = $_POST['email'];
                $password = $_POST['password'];
                echo $userController->createUser($username, $email, $password);
                return;
            } else if (isset($_POST['username'])) {

                $username = $_POST['username'];
                $email    = $_POST['email'] ?? null;
                $password = $_POST['password'] ?? null;

                echo $userController->updateUserProfile($currentUserId, $username, $email, $password);
                return;
            }
        } else if ($method === 'GET') {
            if (isset($_GET['id'])) {
                $userId = (int) $_GET['id'];
                echo $userController->getUserById($userId);
                return;
            } else {
                // Sinon : renvoyer tous les users
                echo $userController->getAllUsers();
                return;
            }
        }
        break;

    default:
        http_response_code(404);
        echo json_encode(["error" => "Page not found"]);
        break;
}
