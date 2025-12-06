<?php 
    $selectedMenu = 'home';
?>

<?php include 'templates/header.php'; ?>

<div class="container grey">
    <section class="content">
        <article>
            <h1>Rejoignez nos lecteurs passionnés</h1>
            <p>Donnez une nouvelle vie à vos livres en les échangeant avec d'autres amoureux de la lecture. Nous croyons en la magie du partage de connaissances et d'histoires à travers les livres. </p>
            <button onclick="location.href='createAccount.php'">Découvrir</button>
        </article>
        <aside class="section-1">
            <img src="assets/section-1.jpg" alt="" />
        </aside>
        
    </section>
</div>

<div class="container">
    <h1>Les derniers livres ajoutés</h1>
    <section class="book-content">
        <ul id="books-list">
            <li class="book">
                <a href="">
                    <img src="" alt="">
                    <h3 class="titre"></h3>
                    <h2 class="auteur"></h2>
                    <span class="soldby">Vendu par:</span>
                </a>
            </li>
        </ul>
    </section>
    <button onclick="location.href='books.php'">Voir tous les livres</button>
</div>

    <div class="container grey">
        <h1>Comment ça marche?</h1>
        <p class="how-subtitle">Échanger des livres avec TomTroc c’est simple et amusant ! Suivez ces étapes pour commencer :</p>
        <div class="container-infos">
            <div class="info-card">
                <p>Inscrivez-vous gratuitement sur notre plateforme.</p>
            </div>
            <div class="info-card">
                 <p>Ajoutez les livres que vous souhaitez échanger à votre profil.</p>
            </div>
            <div class="info-card">
                 <p>Parcourez les livres disponibles chez d'autres membres.</p>
            </div>
            <div class="info-card">
                 <p>Proposez un échange et discutez avec d'autres passionnés de lecture.</p>
            </div>
        </div>
        <button onclick="location.href='books.php'" class="variant">Voir tous les livres</button>
    </div>

   <div class="container-bandeau">
    <img src="assets/bandeau-img.png">
   </div>


    <div class="values-section">
    <div class="values-content">
        <h1>Nos valeurs</h1>

        <p class="values-text">
        Chez Tom Troc, nous mettons l'accent sur le partage, la découverte et la communauté.
        Nos valeurs sont ancrées dans notre passion pour les livres et notre désir de créer
        des liens entre les lecteurs. Nous croyons en la puissance des histoires pour
        rassembler les gens et inspirer des conversations enrichissantes.
        </p>

        <p class="values-text">
        Notre association a été fondée avec une conviction profonde : chaque livre mérite
        d'être lu et partagé.
        </p>

        <p class="values-text">
        Nous sommes passionnés par la création d'une plateforme conviviale qui permet aux
        lecteurs de se connecter, de partager leurs découvertes littéraires et d'échanger
        des livres qui attendent patiemment sur les étagères.
        </p>

        <p class="values-signature">L'équipe Tom Troc</p>

        <img src="assets/vector.png" alt="Illustration" class="values-illustration">
    </div>
</div>



<script type="module">
    import { getAllBooks } from "./services/books.js";

    document.addEventListener("DOMContentLoaded", () => {
        getAllBooks().then((books) => {

            books.slice(0, 4).forEach((book) => {
                const booksList = document.getElementById("books-list");
                const bookCloneTemplate = booksList.querySelector(".book").cloneNode(true);
        
                bookCloneTemplate.querySelector("img").src = book.image;
                bookCloneTemplate.querySelector(".titre").textContent = book.titre;
                bookCloneTemplate.querySelector(".auteur").textContent = book.auteur;
                bookCloneTemplate.querySelector(".soldby").innerText = "Vendu par : " + book.user_name;
                bookCloneTemplate.querySelector("a").href = `book.php?id=${book.id}`;

                booksList.appendChild(bookCloneTemplate);
            });
        });
    });
</script>


<?php include 'templates/footer.php'; ?>