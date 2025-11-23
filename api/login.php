<?php 
require_once '../config/database.php';
require_once '../managers/userManager.php';

$userManager = new UserManager($db);

//Connexion d'un user le username et le password doivent être fournis
if(isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $user = $userManager->getUserByUsername($username);

    //Si le password correspond à celui qui est en base de données
    if($user && password_verify($password, $user->getPassword())) {
        //Connexion réussie
        echo json_encode($user);
        $_SESSION['currentUserId'] = $user->getId();
        return;
    } else {
        //Si le password ne correspond pas : Échec de la connexion
        http_response_code(401);
        echo json_encode(['error' => 'Identifiant ou mot de passe incorrect']);
        return;
    }
    //Si les champs username et password ne sont pas fournis
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Identifiants et mot de passe requis']);
    return;
}

?>