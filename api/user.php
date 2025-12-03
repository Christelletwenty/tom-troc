<?php 
require_once '../config/database.php';
require_once '../managers/userManager.php';

$userManager = new UserManager($db);

//Création d'un user (s'il n'est pas connecté)
if (!isset($_SESSION['currentUserId'])
    && isset($_POST['username']) 
    && isset($_POST['email']) 
    && isset($_POST['password'])) {

    $username = $_POST['username'];
    $email    = $_POST['email'];
    $password = $_POST['password'];

    // Vérification si le username écrit existe déjà ?
    $isExistingUser = $userManager->getUserByUsername($username);
    if($isExistingUser) {
        http_response_code(409);
        echo json_encode(['erreur' => 'Nom dutilisateur déjà pris']);
        return;
    } else {
        // On crée le nouveau user
        $user = new User();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPassword($password);

        $userManager->createUser($user);

        // On récupère le user nouvellement créé pour récupérer son id
        $created = $userManager->getUserByEmail($email);
        if ($created) {
            $_SESSION['currentUserId'] = $created->getId();
        }

        echo json_encode(['succès' => 'Utilisateur crée avec succès']);
        return;
    }

    //Update d'un user existant
} else if (isset($_SESSION['currentUserId']) && isset($_POST['username'])) {

    // vérification authentification
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_SESSION['currentUserId'])) {
        http_response_code(401);
        echo json_encode(['erreur' => 'Utilisateur non authentifié']);
        return;
    }

    $userId   = $_SESSION['currentUserId'];
    $username = $_POST['username'];
    $email    = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;

    // On récupère le user depuis la DB
    $user = $userManager->getUserById($userId);

    if (!$user) {
        http_response_code(404);
        echo json_encode(['erreur' => 'Utilisateur non trouvé']);
        return;
    }

    $user->setUsername($username);

    if ($email !== null) {
        $user->setEmail($email);
    }

    // 👉 ICI : on décide clairement ce qu'on fait du password
    if ($password !== null && trim($password) !== "") {
        // Nouveau mot de passe en clair → sera hashé dans updateUser()
        $user->setPassword($password);
    } else {
        // Aucun nouveau mot de passe → on indique au manager de NE PAS le toucher
        $user->setPassword(null);
    }

    $userManager->updateUser($user);
    echo json_encode(['succès' => 'Profil mis à jour']);
    return;

    // Récupérer un user par id
} else if (isset($_GET['id'])) {
    $user = $userManager->getUserById($_GET['id']);
    if ($user) {
        echo json_encode($user);
        return;
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Utilisateur non trouvé']);
        return;
    }

    // Sinon : renvoyer tous les users
} else {
    $users = $userManager->findAll();
    echo json_encode($users);
    return;
}

?>