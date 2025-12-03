<?php

require_once '../config/database.php';
require_once '../managers/userManager.php';
require_once '../managers/livreManager.php';

if (!isset($_SESSION['currentUserId'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Utilisateur non authentifié']);
    exit;
}

$userManager = new UserManager($db);
$livreManager = new LivreManager($db);

$user = $userManager->getUserById($_SESSION['currentUserId']);

if (!$user) {
    http_response_code(404);
    echo json_encode(['error' => 'Utilisateur non trouvé']);
    exit;
}

// base : on prend ce que renvoie jsonSerialize()
$data = $user->jsonSerialize();

// on ajoute le nombre de livres
$data['library'] = $livreManager->countBooksByUserId($user->getId());

echo json_encode($data);
