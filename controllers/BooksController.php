<?php

class BooksController
{
    private $booksManager;

    public function __construct(BooksManager $manager)
    {
        $this->booksManager = $manager;
    }


    public function getAllBooks()
    {
        $books = $this->booksManager->findAllBooks();
        return json_encode($books);
    }

    public function getBookById($id)
    {
        $book = $this->booksManager->getBookById($id);
        return json_encode($book);
    }

    public function getBooksByUserId($userId)
    {
        $books = $this->booksManager->getBooksByUserId($userId);
        return json_encode($books);
    }

    public function addImageForBookId($file, $bookId)
    {
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
        $this->booksManager->updateBookImage($bookId, $publicPath);

        // Retour sur la page d'édition du livre
        header('Location: ../updateBook.php?id=' . $bookId);
        return;
    }

    public function addBook($titre, $auteur, $description, $dispo, $currentUserId, $file_content)
    {
        if (!empty($file_content['image']) && $file_content['image']['error'] === UPLOAD_ERR_OK) {
            $file = $file_content['image'];

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
        $livre = new BookModel();
        $livre->setTitre($titre);
        $livre->setAuteur($auteur);
        $livre->setImage($imagePath);
        $livre->setDescription($description);
        $livre->setDispo($dispo);
        $livre->setUserId($currentUserId);

        $this->booksManager->createBook($livre);
        return json_encode(['succès' => 'Livre crée']);
    }

    public function updateBook($id, $titre, $auteur, $image, $description, $dispo, $currentUserId)
    {
        $livre = new BookModel();
        $livre->setId($id);
        $livre->setTitre($titre);
        $livre->setAuteur($auteur);
        $livre->setImage($image);
        $livre->setDescription($description);
        $livre->setDispo($dispo);
        $livre->setUserId($currentUserId);

        $this->booksManager->updateBook($livre);

        return json_encode(['succès' => 'Livre mis à jour']);
    }

    public function deleteBook($id)
    {
        $this->booksManager->deleteBook($id);
        return json_encode(['succès' => 'Livre supprimé']);
    }

    public function booksRoute(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];

        // ===== GET =====
        if ($method === 'GET') {

            if (isset($_GET['user_id'])) {
                echo $this->getBooksByUserId($_GET['user_id']);
                return;
            }

            if (isset($_GET['id'])) {
                echo $this->getBookById($_GET['id']);
                return;
            }

            echo $this->getAllBooks();
            return;
        }

        // ===== POST =====
        if ($method === 'POST') {

            if (!isset($_SESSION['currentUserId'])) {
                http_response_code(401);
                echo json_encode(['error' => 'Non connecté']);
                return;
            }
            $currentUserId = (int) $_SESSION['currentUserId'];

            // Upload image (book existant)
            if (isset($_FILES['image']) && isset($_POST['book_id'])) {
                $bookId = (int) $_POST['book_id'];
                $this->addImageForBookId($_FILES['image'], $bookId);
                return;
            }

            // Update
            if (isset($_POST['id'], $_POST['titre'], $_POST['auteur'], $_POST['image'], $_POST['description'], $_POST['dispo'])) {
                echo $this->updateBook(
                    $_POST['id'],
                    $_POST['titre'],
                    $_POST['auteur'],
                    $_POST['image'],
                    $_POST['description'],
                    $_POST['dispo'],
                    $currentUserId
                );
                return;
            }

            // Delete
            if (isset($_POST['id']) && !isset($_POST['titre'])) {
                echo $this->deleteBook($_POST['id']);
                return;
            }

            // Add
            if (isset($_POST['titre'], $_POST['auteur'], $_POST['description'])) {
                $titre = $_POST['titre'];
                $auteur = $_POST['auteur'];
                $description = $_POST['description'];
                $dispo = isset($_POST['dispo']) ? $_POST['dispo'] : 1;

                $file = null;
                if (!empty($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $file = $_FILES;
                }

                echo $this->addBook($titre, $auteur, $description, $dispo, $currentUserId, $file);
                return;
            }

            http_response_code(400);
            echo json_encode(['error' => 'Paramètres POST invalides']);
            return;
        }

        http_response_code(405);
        echo json_encode(['error' => 'Méthode non autorisée']);
    }
}
