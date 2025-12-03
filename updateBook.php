<?php
    $selectedMenu = 'books';

    // Récupérer l'id du livre depuis l'URL
    $bookId = $_GET['id'] ?? null;
    if (!$bookId) {
        // pas d'id → on retourne à la liste
        header('Location: account.php');
        exit;
    }
?>

<?php include './templates/header.php'; ?>

<div class="update-book">
    <h1>Modifier les informations</h1>
        <div class="img">
    <form class="book-image-form" action="api/upload_book_image.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="book_id" value="<?= htmlspecialchars($bookId, ENT_QUOTES) ?>">

        <label class="book-image-wrapper">
            <img id="book-image" alt="Couverture du livre" />
            <button class="book-image-edit">Modifier la photo</button>
            <input type="file"
                   name="image"
                   accept="image/*"
                   class="book-image-input"
                   onchange="this.form.submit()">
        </label>
    </form>
</div>

    <div class="form-update">
        <form>
            <label for="titre">Titre</label>
            <input type="text" id="titre" name="titre"><br><br>

            <label for="auteur">Auteur</label>
            <input type="text" id="auteur" name="auteur"><br><br>

            <label for="description">Commentaire</label>
            <textarea id="description" name="description" rows="4"></textarea><br><br>

            <label for="dispo">Disponibilité</label>
            <select name="dispo" id="dispo">
                <option value="1">Disponible</option>
                <option value="0">Indisponible</option>
            </select>

            <button type="button" id="submit-update">Valider</button>
        </form>
    </div>
</div>


<script type="module">
 import { updateBook, getBookById } from './services/profile.js';

    const bookId = <?= json_encode($bookId) ?>;

    document.addEventListener("DOMContentLoaded", () => {
        const titreInput = document.getElementById('titre');
        const auteurInput = document.getElementById('auteur');
        const descriptionInput = document.getElementById('description');
        const dispoSelect = document.getElementById('dispo');
        const imageEl = document.getElementById('book-image');

        let currentImage = '';

        // Charger les infos du livre à partir de l'API
        getBookById(bookId)
            .then((book) => {
                titreInput.value = book.titre || '';
                auteurInput.value = book.auteur || '';
                descriptionInput.value = book.description || '';
                dispoSelect.value = String(book.dispo ?? '1');

                currentImage = book.image || '';
                if (currentImage) {
                    imageEl.src = currentImage;
                    imageEl.alt = book.titre || '';
                } else {
                    imageEl.style.display = 'none';
                }
            })
            .catch((error) => {
                console.error("Erreur lors du chargement du livre", error);
                alert("Impossible de charger ce livre.");
                window.location.href = "account.php";
            });

        // Click sur "Valider"
        document.getElementById("submit-update").addEventListener('click', () => {
            const titre = titreInput.value.trim();
            const auteur = auteurInput.value.trim();
            const description = descriptionInput.value.trim();
            const dispo = dispoSelect.value;

            if (!titre || !auteur) {
                alert("Titre et auteur sont obligatoires.");
                return;
            }

            updateBook(bookId, titre, auteur, currentImage, description, dispo)
                .then((data) => {
                    console.log("Livre édité avec succès", data);
                    window.location.href = "account.php";
                })
                .catch((error) => {
                    console.error("Erreur lors de la modification", error);
                    alert("Erreur lors de la modification du livre.");
                });
        });
    });
</script>
<?php include './templates/footer.php'; ?>