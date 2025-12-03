<?php
require_once '../config/database.php';
require_once '../managers/commentaireManager.php';

$commentaireManager = new CommentaireManager($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_SESSION['currentUserId'])) {
    http_response_code(401);
    echo json_encode(['erreur' => 'Utilisateur non authentifié']);
    return;
} 
//Création d'un commentaire pour un livre
else if(isset($_POST['contenu']) && isset($_POST['created_at']) && isset($_POST['livre_id'])) {
    $contenu = $_POST['contenu'];
    $created_at = $_POST['created_at'];
    $user_id = $_SESSION['currentUserId'];
    $livre_id = $_POST['livre_id'];

    $commentaire = new Commentaire();
    $commentaire->setContenu($contenu);
    $commentaire->setCreatedAt($created_at);
    $commentaire->setUserId($user_id);
    $commentaire->setLivreId($livre_id);
    //Si les champs ne sont pas tous remplis
} else if(isset($_POST['contenu']) || isset($_POST['created_at']) || isset($_POST['livre_id'])) {
    //Erreur : remplir tous les champs est obligatoire
    http_response_code(400);
    echo json_encode(['error' => 'Tous les champs sont requis pour créer un commentaire']);
    return;
    //Récupération des commentaires par livre
} else if(isset($_GET['livre_id'])) { 
    $comment = $commentaireManager->getCommentsByBookId($_GET['livre_id']);
    //Si les commentaires existent on les renvoie
    if($comment) {
        echo json_encode($comment);
        return;
        //Si ils existent pas, ils ne sont pas trouvés donc 404
    } else { 
        http_response_code(404);
        echo json_encode(['error' => 'Commentaires non trouvées']);
        return;
    }
} else if (isset($_POST['delete_id'])) {
    //Suppression d'un commentaire pour un livre donné seulement si c'est le user qui l'a crée
    $commentaireManager->deleteCommentBook($_POST['delete_id']);
}

?>