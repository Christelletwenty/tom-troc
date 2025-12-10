<?php
require_once '../config/database.php';
require_once '../managers/userManager.php';

if (!isset($_SESSION['currentUserId'])) {
    header('Location: ../login.php');
    exit;
}

$userId = $_SESSION['currentUserId'];

//Vérif fichier
if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
    header('Location: ../account.php');
    exit;
}

$file = $_FILES['avatar'];

//Types autorisés
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
if (!in_array($file['type'], $allowedTypes, true)) {
    header('Location: ../account.php');
    exit;
}

//Taille max 2 Mo
if ($file['size'] > 2 * 1024 * 1024) {
    header('Location: ../account.php');
    exit;
}

//Dossier de destination = assets/
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
