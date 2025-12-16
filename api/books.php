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
    //Récupèration de l'id du livre et du fichier uploadé
    $bookId = (int) $_POST['book_id'];
    $file   = $_FILES['image'];

    //Vérifie qu'il n'y a pas d'erreur lors de l'upload
    if ($file['error'] !== UPLOAD_ERR_OK) {
        header('Location: ../updateBook.php?id=' . $bookId);
        return;
    }

    // Dossier de destination (par ex. assets/uploads/books/) ou sont stocké les images
    $uploadDir = '../assets/uploads/books/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0775, true);
    }

    //Récupèration et nettoyage de l'extension du fichier
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $extension = strtolower($extension) ?: 'jpg';

    //Génération d'un nom de fichier unique
    $filename   = 'book_' . $bookId . '_' . time() . '.' . $extension;
    $destPath   = $uploadDir . $filename;
    $publicPath = 'assets/uploads/books/' . $filename; // stocké en BDD

    //Déplacement du fichier depuis le dossier temporaire
    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        header('Location: ../updateBook.php?id=' . $bookId);
        return;
    }

    // Mise à jour du chemin de l'image en BDD
    $booksManager->updateBookImage($bookId, $publicPath);

    // Retour sur la page d'édition du livre
    header('Location: ../updateBook.php?id=' . $bookId);
    return;
}

// création d'un livre
if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['titre'])
    && isset($_POST['auteur'])
    && isset($_POST['description'])
    && !isset($_POST['id'])
) {

    //Récupèration des données du formulaire
    $titre = $_POST['titre'];
    $auteur = $_POST['auteur'];
    $description = $_POST['description'];
    $dispo = isset($_POST['dispo']) ? $_POST['dispo'] : 1;
    //L'utilisateur connecté est le proprio du livre
    $currentUserId = $_SESSION['currentUserId'];

    $imagePath = ''; // chemin de l'image et par défaut : pas d'image

    // Si un fichier "image" a été envoyé, on tente l'upload
    if (!empty($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['image'];

        $uploadDir = '../assets/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        //Extension du fichier
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $extension = strtolower($extension ?: 'jpg');
        //Nom du fichier unique
        $filename   = 'book_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
        $destPath   = $uploadDir . $filename;
        $publicPath = 'assets/' . $filename;
        //Déplacement du fichier
        if (move_uploaded_file($file['tmp_name'], $destPath)) {
            $imagePath = $publicPath;
        }
    }
    //Création de l'objet Livre
    $livre = new Livre();
    $livre->setTitre($titre);
    $livre->setAuteur($auteur);
    $livre->setImage($imagePath);
    $livre->setDescription($description);
    $livre->setDispo($dispo);
    $livre->setUserId($currentUserId);

    $booksManager->createBook($livre);
    echo json_encode(['succès' => 'Livre crée']);
    return;

    //update d'un livre
} else if (isset($_POST['id']) && isset($_POST['titre']) && isset($_POST['auteur']) && isset($_POST['image']) && isset($_POST['description']) && isset($_POST['dispo'])) {
    //Récupération des données du livre
    $id = $_POST['id'];
    $titre = $_POST['titre'];
    $auteur = $_POST['auteur'];
    $image = $_POST['image'];
    $description = $_POST['description'];
    $dispo = $_POST['dispo'];

    $currentUserId = $_SESSION['currentUserId'];
    //Création du livre
    $livre = new Livre();
    $livre->setId($id);
    $livre->setTitre($titre);
    $livre->setAuteur($auteur);
    $livre->setImage($image);
    $livre->setDescription($description);
    $livre->setDispo($dispo);
    $livre->setUserId($currentUserId);
    //Mise à jour en base
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
} else {
    $books = $booksManager->findAllBooks();
    echo json_encode($books);
    return;
}
