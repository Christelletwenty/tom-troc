<?php 

require_once '../config/database.php';
require_once '../managers/userManager.php';

$userManager = new UserManager($db);

//Création d'un user
if(isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    //Vérification si le username écrit existe déjà ?
    $isExistingUser = $userManager->getUserByUsername($username);
    if($isExistingUser) {
        http_response_code(409);
        echo json_encode(['erreur' => 'Nom dutilisateur déjà pris']);
        return;
        //Si non on crée le nouveau user
    } else {
        $user = new User();
        $user->setUsername($username);
        $user-setPassword($password);
        $userManager->createUser($user);
        echo json_encode(['succès' => 'Utilisateur crée avec succès']);
        return;
    }
    //On va chercher le user par son id
} else if(isset($_GET['id'])) {
    $user = $userManager->getUserById($_GET['id']);
    //Si le user existe on le renvoie
    if ($user) {
        echo json_encode($user);
        return;
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Utilisateur non trouvé']);
        return;
    }
    //Si on récupère pas le user par son id on renvoie tous les users
} else {
    //on renvoie la liste
    $users = $userManager->findAll();
    echo json_encode($users);
    return;
}

?>