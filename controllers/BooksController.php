<?php

class BooksController
{
    private $livreManager;

    public function __construct(LivreManager $manager)
    {
        $this->livreManager = $manager;
    }


    public function getAllBooks()
    {
        $books = $this->livreManager->findAllBooks();
        return json_encode($books);
    }

    public function getBookById($id)
    {
        $book = $this->livreManager->getBookById($id);
        return json_encode($book);
    }

    public function getBooksByUserId($userId)
    {
        $books = $this->livreManager->getBooksByUserId($userId);
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
        $this->livreManager->updateBookImage($bookId, $publicPath);

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
        $livre = new Livre();
        $livre->setTitre($titre);
        $livre->setAuteur($auteur);
        $livre->setImage($imagePath);
        $livre->setDescription($description);
        $livre->setDispo($dispo);
        $livre->setUserId($currentUserId);

        $this->livreManager->createBook($livre);
        return json_encode(['succès' => 'Livre crée']);
    }

    public function updateBook($id, $titre, $auteur, $image, $description, $dispo, $currentUserId)
    {
        $livre = new Livre();
        $livre->setId($id);
        $livre->setTitre($titre);
        $livre->setAuteur($auteur);
        $livre->setImage($image);
        $livre->setDescription($description);
        $livre->setDispo($dispo);
        $livre->setUserId($currentUserId);

        $this->livreManager->updateBook($livre);

        return json_encode(['succès' => 'Livre mis à jour']);
    }

    public function deleteBook($id)
    {
        $this->livreManager->deleteBook($id);
        return json_encode(['succès' => 'Livre supprimé']);
    }
}
