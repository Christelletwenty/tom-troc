<?php 

require_once '../config/database.php';
require_once '../managers/livreManager.php';

$booksManager = new LivreManager($db);

//Création d'un user
if(isset($_POST['book'])) {
    //ici on créé
} else if(isset($_GET['id'])) {
    $book = $booksManager->getBooksByUserId($_GET['id']);
    //Si le user existe on le renvoie
    if ($book) {
        echo json_encode($book);
        return;
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Utilisateur non trouvé']);
        return;
    }
    //Si on récupère pas le user par son id on renvoie tous les users
} else {
    //on renvoie la liste
    $books = $booksManager->findAllBooks();
    echo json_encode($books);
    return;
}

?>