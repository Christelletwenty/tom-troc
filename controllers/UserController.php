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
            $user = new User();
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
            header('Location: ../account.php');
            exit;
        }

        $this->userManager->updateUserImage($userId, $imagePathForDb);

        //Retour à la page profil
        header('Location: ../account.php');
        return;
    }
}
