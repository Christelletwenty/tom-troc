<?php

class UserController
{
    private $userManager;
    private $bookManager;

    public function __construct($userManager, $bookManager)
    {
        $this->userManager = $userManager;
        $this->bookManager = $bookManager;
    }

    public function login($email, $password)
    {
        $user = $this->userManager->getUserByEmail($email);

        //Si le password correspond à celui qui est en base de données
        if ($user && password_verify($password, $user->getPassword())) {
            $_SESSION['currentUserId'] = $user->getId();
            return json_encode($user);
        } else {
            //Si le password ne correspond pas : Échec de la connexion
            http_response_code(401);
            return json_encode(['error' => 'Identifiant ou mot de passe incorrect']);
        }
    }

    public function logout()
    {
        session_destroy();
        header("Location: index.php?page=login");
    }

    public function getAllUsers()
    {
        $users = $this->userManager->getAllUsers();
        return json_encode($users);
    }

    public function getUserById($userId)
    {
        $user = $this->userManager->getUserById($userId);
        if ($user) {
            return json_encode($user);
        } else {
            http_response_code(404);
            return json_encode(['error' => 'Utilisateur non trouvé']);
        }
    }

    public function getUserProfileById($userId)
    {
        $user = $this->userManager->getUserById($userId);

        if (!$user) {
            http_response_code(404);
            return json_encode(['error' => 'Utilisateur non trouvé']);
        }

        // base : on prend ce que renvoie jsonSerialize()
        $data = $user->jsonSerialize();

        // on ajoute le nombre de livres
        $data['library'] = $this->bookManager->countBooksByUserId($user->getId());

        return json_encode($data);
    }

    public function getConnectedUser()
    {
        if (isset($_SESSION['currentUserId'])) {
            $user = $this->userManager->getUserById($_SESSION['currentUserId']);

            if ($user) {
                return json_encode($user);
            } else {
                http_response_code(500);
                return json_encode(['error' => 'Erreur lors de la récupération du user connecté']);
            }
        }
    }

    public function createUser($username, $email, $password)
    {
        // Vérification si le username écrit existe déjà ?
        $isExistingUser = $this->userManager->getUserByUsername($username);
        if ($isExistingUser) {
            http_response_code(409);
            return json_encode(['erreur' => 'Nom dutilisateur déjà pris']);
        } else {
            // On crée le nouveau user
            $user = new UserModel();
            $user->setUsername($username);
            $user->setEmail($email);
            $user->setPassword($password);

            $this->userManager->createUser($user);

            // On récupère le user nouvellement créé pour récupérer son id
            $created = $this->userManager->getUserByEmail($email);
            if ($created) {
                $_SESSION['currentUserId'] = $created->getId();
            }

            return json_encode(['succès' => 'Utilisateur crée avec succès']);
        }
    }

    public function updateUserProfile($userId, $username, $email = null, $password = null)
    {
        $user = $this->userManager->getUserById($userId);

        if (!$user) {
            http_response_code(404);
            return json_encode(['erreur' => 'Utilisateur non trouvé']);
        }

        $user->setUsername($username);

        if ($email !== null) {
            $user->setEmail($email);
        }

        if ($password !== null && trim($password) !== "") {
            $user->setPassword($password);
        } else {
            $user->setPassword(null);
        }

        $this->userManager->updateUser($user);
        return json_encode(['succès' => 'Profil mis à jour']);
    }

    public function uploadAvatar($userId, $file)
    {
        $uploadDir = '../assets/';

        // On s'assure qu'il existe
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        //Nom unique du fichier
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION) ?: 'jpg');
        $filename = 'avatar_' . $userId . '_' . time() . '.' . $extension;

        //Chemins
        $destinationPath = $uploadDir . $filename;      // Sur le disque
        $imagePathForDb  = 'assets/' . $filename;       // En BDD et visible via <img>

        //Déplacement du fichier
        if (!move_uploaded_file($file['tmp_name'], $destinationPath)) {
            header('Location: ../index.php?page=account');
            exit;
        }

        $this->userManager->updateUserImage($userId, $imagePathForDb);

        //Retour à la page profil
        header('Location: ../index.php?page=account');
        return;
    }

    public function loginRoute(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];

        // GET: récupérer l'utilisateur connecté (si tu le fais déjà)
        if ($method === 'GET') {
            echo $this->getConnectedUser();
            return;
        }

        // POST: login avec email + password
        if ($method === 'POST') {
            if (!isset($_POST['email'], $_POST['password'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Email et mot de passe requis']);
                return;
            }

            echo $this->login($_POST['email'], $_POST['password']);
            return;
        }

        http_response_code(405);
        echo json_encode(['error' => 'Méthode non autorisée']);
    }

    public function uploadAvatarRoute(): void
    {
        if (!isset($_SESSION['currentUserId'])) {
            header('Location: ../index.php?page=login');
            exit;
        }
        $currentUserId = (int) $_SESSION['currentUserId'];

        if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
            header('Location: ../index.php?page=account');
            exit;
        }

        $file = $_FILES['avatar'];

        // Types autorisés (comme dans ton api/index.php actuel) :contentReference[oaicite:1]{index=1}
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes, true)) {
            header('Location: ../index.php?page=account');
            exit;
        }

        // Taille max 2MB (comme ton code actuel) :contentReference[oaicite:2]{index=2}
        if ($file['size'] > 2 * 1024 * 1024) {
            header('Location: ../index.php?page=account');
            exit;
        }

        $this->uploadAvatar($currentUserId, $file);
    }

    public function profileRoute()
    {
        if (!isset($_SESSION['currentUserId'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Non connecté']);
            return;
        }
        $currentUserId = (int) $_SESSION['currentUserId'];

        echo $this->getUserProfileById($currentUserId);
    }

    public function userRoute()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if (!isset($_SESSION['currentUserId'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Non connecté']);
            return;
        }
        $currentUserId = (int) $_SESSION['currentUserId'];

        if ($method === 'POST') {
            if (!isset($_SESSION['currentUserId'])) {
                http_response_code(401);
                echo json_encode(['error' => 'Non connecté']);
                return;
            }
            $currentUserId = (int) $_SESSION['currentUserId'];

            if (
                isset($_POST['username'])
                && isset($_POST['email'])
                && isset($_POST['password'])
            ) {

                $username = $_POST['username'];
                $email    = $_POST['email'];
                $password = $_POST['password'];
                echo $this->createUser($username, $email, $password);
                return;
            } else if (isset($_POST['username'])) {

                $username = $_POST['username'];
                $email    = $_POST['email'] ?? null;
                $password = $_POST['password'] ?? null;

                echo $this->updateUserProfile($currentUserId, $username, $email, $password);
                return;
            }
        } else if ($method === 'GET') {
            if (isset($_GET['id'])) {
                $userId = (int) $_GET['id'];
                echo $this->getUserById($userId);
                return;
            } else {
                // Sinon : renvoyer tous les users
                echo $this->getAllUsers();
                return;
            }
        }
    }
}
