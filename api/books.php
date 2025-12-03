<?php 
require_once '../config/database.php';
require_once '../managers/livreManager.php';

$booksManager = new LivreManager($db);

//Ajout d'un livre
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_SESSION['currentUserId'])) {
    http_response_code(401);
    echo json_encode(['erreur' => 'Utilisateur non authentifié']);
    return;
} else if (isset($_POST['titre']) && isset($_POST['auteur']) && isset($_POST['image']) && isset($_POST['description']) && isset($_POST['dispo'])) {
    $titre = $_POST['titre'];
    $auteur = $_POST['auteur'];
    $image = $_POST['image'];
    $description = $_POST['description'];
    $dispo = $_POST['dispo'];
    $user_id = $_SESSION['currentUserId'];

    //Récupération de l'id de l'utilisateur qui est authentifié
    $currentUserId = $_SESSION['currentUserId'];

    $livre = new Livre();
    $livre->setTitre($titre);
    $livre->setAuteur($auteur);
    $livre->setImage($image);
    $livre->setDescription($description);
    $livre->setDispo($dispo);
    $livre->setUserId($currentUserId);

    $booksManager->createBook($livre);
    echo json_encode(['succès' => 'Livre crée']);
    return;
    //Update d'un book
    //Vérification si l'utilisateur est bien authentifié
} else if (isset($_POST['id']) && isset($_POST['titre']) && isset($_POST['auteur']) && isset($_POST['image']) && isset($_POST['description']) && isset($_POST['dispo'])) {
    $id = $_POST['id'];
    $titre = $_POST['titre'];
    $auteur = $_POST['auteur'];
    $image = $_POST['image'];
    $description = $_POST['description'];
    $dispo = $_POST['dispo'];

    //Récupération de l'id de l'utilisateur qui est authentifié
    $currentUserId = $_SESSION['currentUserId'];

    $livre = new Livre();
    $livre->setId($id);
    $livre->setTitre($titre);
    $livre->setAuteur($auteur);
    $livre->setImage($image);
    $livre->setDescription($description);
    $livre->setDispo($dispo);
    $livre->setUserId($currentUserId);

    $booksManager->updateBook($livre);

    echo json_encode(['succès' => 'Livre mis à jour']);
    
    //Suppresssion d'un livre
    //Vérification que l'utilisateur soit bien authentifié
} else if (isset($_POST['id'])) {

    //Récupération de l'id de l'utilisateur qui est authentifié
    $currentUserId = $_SESSION['currentUserId'];
    $id = $_POST['id'];

    $booksManager->deleteBook($id);
    echo json_encode(['succès' => 'Livre supprimé']);
    return;
    //Récupération de tous les livres par userId
} else if (isset($_GET['user_id'])) {
    $books = $booksManager->getBooksByUserId($_GET['user_id']);
    echo json_encode($books);
    return;

} else if (isset($_GET['id'])) {
    $book = $booksManager->getBookById($_GET['id']);
    echo json_encode($book);
    return;

} else if (isset($_SESSION['currentUserId'])) {
    // cas "par défaut" pour un utilisateur connecté (Mon compte)
    $books = $booksManager->getBooksByUserId($_SESSION['currentUserId']);
    echo json_encode($books);
    return;

} else {
    // fallback : tous les livres si pas connecté
    $books = $booksManager->findAllBooks();
    echo json_encode($books);
    return;
}
?>