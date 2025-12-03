<?php 

require_once '../config/database.php';
require_once '../managers/livreManager.php';

$booksManager = new LivreManager($db);

// Si POST mais pas connecté → refus
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_SESSION['currentUserId'])) {
    http_response_code(401);
    echo json_encode(['erreur' => 'Utilisateur non authentifié']);
    return;
}

//Upload d'une nouvelle image
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image']) && isset($_POST['book_id'])) {

    $bookId = (int) $_POST['book_id'];
    $file   = $_FILES['image'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        header('Location: ../updateBook.php?id=' . $bookId);
        return;
    }

    // Dossier de destination (par ex. assets/uploads/books/)
    $uploadDir = '../assets/uploads/books/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0775, true);
    }

    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $extension = strtolower($extension) ?: 'jpg';

    $filename   = 'book_' . $bookId . '_' . time() . '.' . $extension;
    $destPath   = $uploadDir . $filename;
    $publicPath = 'assets/uploads/books/' . $filename; // stocké en BDD

    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        header('Location: ../updateBook.php?id=' . $bookId);
        return;
    }

    // Mise à jour de l'image en BDD
    $booksManager->updateBookImage($bookId, $publicPath);

    // Retour sur la page d'édition du livre
    header('Location: ../updateBook.php?id=' . $bookId);
    return;
}

//création d'un livre
if (isset($_POST['titre']) && isset($_POST['auteur']) && isset($_POST['image']) && isset($_POST['description']) && isset($_POST['dispo']) && !isset($_POST['id'])) {
    $titre = $_POST['titre'];
    $auteur = $_POST['auteur'];
    $image = $_POST['image'];
    $description = $_POST['description'];
    $dispo = $_POST['dispo'];
    $user_id = $_SESSION['currentUserId'];

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

   //update d'un livre
} else if (isset($_POST['id']) && isset($_POST['titre']) && isset($_POST['auteur']) && isset($_POST['image']) && isset($_POST['description']) && isset($_POST['dispo'])) {
    $id = $_POST['id'];
    $titre = $_POST['titre'];
    $auteur = $_POST['auteur'];
    $image = $_POST['image'];
    $description = $_POST['description'];
    $dispo = $_POST['dispo'];

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
    return;

   //suppression d'un livre
} else if (isset($_POST['id'])) {

    $currentUserId = $_SESSION['currentUserId'];
    $id = $_POST['id'];

    $booksManager->deleteBook($id);
    echo json_encode(['succès' => 'Livre supprimé']);
    return;

    //récup livre par user id
} else if (isset($_GET['user_id'])) {
    $books = $booksManager->getBooksByUserId($_GET['user_id']);
    echo json_encode($books);
    return;

    //récupérer livre par id
} else if (isset($_GET['id'])) {
    $book = $booksManager->getBookById($_GET['id']);
    echo json_encode($book);
    return;

    //récupérer les livres du user connecté
} else if (isset($_SESSION['currentUserId'])) {
    $books = $booksManager->getBooksByUserId($_SESSION['currentUserId']);
    echo json_encode($books);
    return;

    //récupérer tous les livres si pas connecté
} else {
    $books = $booksManager->findAllBooks();
    echo json_encode($books);
    return;
}
?>
