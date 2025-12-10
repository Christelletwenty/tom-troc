<?php
    $selectedMenu = 'books';
?>

<?php include './templates/header.php'; ?>

<div class="update-book">
    <h1>Ajouter un livre</h1>
    <div class="img">
        <div class="book-image-form">
            <label class="book-image-wrapper">
                <img id="book-image" src="assets/placeholder-book.png" alt="Couverture du livre" />
                <span class="book-image-edit">Ajouter une photo</span>
                <input
                    type="file"
                    name="image"
                    id="image-input"
                    accept="image/*"
                    class="book-image-input">
            </label>
        </div>
    </div>

    <div class="form-update">
        <form id="create-book-form">
            <label for="titre">Titre</label>
            <input type="text" id="titre" name="titre">

            <label for="auteur">Auteur</label>
            <input type="text" id="auteur" name="auteur">

            <label for="description">Commentaire</label>
            <textarea id="description" name="description" rows="4"></textarea>

            <p class="error" style="color:#c44; font-size:13px; margin:4px 0 0;"></p>

            <button type="button" id="submit-book">Valider</button>
        </form>
    </div>
</div>

<script type="module">
    import { createBook } from './services/profile.js';

    const errorEl = document.querySelector('.error');
    const imageInput = document.getElementById('image-input');
    const bookImage  = document.getElementById('book-image');

    imageInput.addEventListener('change', () => {
        const file = imageInput.files[0];
        if (file) {
            bookImage.src = URL.createObjectURL(file);
        } else {
            bookImage.src = 'assets/placeholder-book.png';
        }
    });

    document.getElementById("submit-book").addEventListener('click', () => {
        const titre = document.getElementById('titre').value.trim();
        const auteur = document.getElementById('auteur').value.trim();
        const description = document.getElementById('description').value.trim();
        const dispo = "1";
        const imageFile = imageInput.files[0] || null;

        errorEl.innerText = '';

        if (!titre || !auteur || !description) {
            errorEl.innerText = "Veuillez remplir le titre, l'auteur et le commentaire.";
            return;
        }

        createBook(titre, auteur, description, "1", imageFile)
            .then((data) => {
                console.log("Livre créé avec succès", data);
                window.location.href = "index.php?page=account";
            })
            .catch((error) => {
                console.error("Erreur lors de la création", error);
                errorEl.innerText = "Une erreur est survenue lors de la création.";
            });
    });
</script>

<?php include './templates/footer.php'; ?>
