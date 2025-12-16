<?php
require_once '../config/database.php';
require_once '../managers/userManager.php';


//Véroification que l'utilisateur est bien connecté
if (!isset($_SESSION['currentUserId'])) {
    //Redirection vers la page de login si pas connecté
    header('Location: ../login.php');
    exit;
}
//Récupération de l'id de l'utilisateur connecté
$userId = $_SESSION['currentUserId'];

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

//Dossier de destination = assets/ ou sont stocké les images uploadées
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

//Mise à jour du user
$userManager = new UserManager($db);
$userManager->updateUserImage($userId, $imagePathForDb);

//Retour à la page profil
header('Location: ../account.php');
exit;
