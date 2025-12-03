<?php
session_start();

require_once '../config/database.php';
require_once '../managers/userManager.php';

// 1. Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['currentUserId'])) {
    header('Location: ../login.php');
    exit;
}

// 2. Vérifier si un fichier a été envoyé
if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
    // Tu peux gérer un message d'erreur plus tard si tu veux
    header('Location: ../account.php');
    exit;
}

$file = $_FILES['avatar'];

// 3. Vérifications basiques
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
if (!in_array($file['type'], $allowedTypes, true)) {
    header('Location: ../account.php');
    exit;
}

if ($file['size'] > 2 * 1024 * 1024) { // 2 Mo max par exemple
    header('Location: ../account.php');
    exit;
}

// 4. Déterminer le dossier de destination
$uploadDir = '../uploads/avatars/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// 5. Générer un nom de fichier unique
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$userId = $_SESSION['currentUserId'];
$filename = 'avatar_' . $userId . '_' . time() . '.' . $extension;
$destinationPath = $uploadDir . $filename;

// 6. Déplacer le fichier
if (!move_uploaded_file($file['tmp_name'], $destinationPath)) {
    header('Location: ../account.php');
    exit;
}

// 7. Chemin à stocker en BDD (chemin accessible depuis le navigateur)
$imagePathForDb = 'uploads/avatars/' . $filename;

// 8. Mettre à jour le user en BDD
$userManager = new UserManager($db);
$userManager->updateUserImage($userId, $imagePathForDb);

// 9. Redirection vers le compte
header('Location: ../account.php');
exit;
