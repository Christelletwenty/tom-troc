<?php
    $selectedMenu = 'books';
?>

<?php include './templates/header.php'; ?>

<section class="book-content">

  <div class="search-bar">
    <span class="exchange-books">Nos livres à l'échange</span>
    <input type="text" placeholder=" 🔍 Rechercher un livre" id="search-book" >
  </div>

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


<script type="module">
  import { getAllBooks } from "./services/books.js";
  import { getConnectedUser } from "./services/profile.js";

  document.addEventListener("DOMContentLoaded", () => {
    Promise.all([getAllBooks(), getConnectedUser().catch(() => null)])
      .then(([books, user]) => {
        const booksList = document.getElementById("books-list");
        const template = booksList.querySelector(".book");

        books.filter(book => book.user_id !== user?.id).forEach((book) => {

        const bookCloneTemplate = template.cloneNode(true);
  
        bookCloneTemplate.querySelector("img").src = book.image;
        bookCloneTemplate.querySelector(".titre").textContent = book.titre;
        bookCloneTemplate.querySelector(".auteur").textContent = book.auteur;
        bookCloneTemplate.querySelector(".soldby").innerText = "Vendu par : " + book.user_name;
        bookCloneTemplate.querySelector("a").href = `index.php?page=book&id=${book.id}`;

        booksList.appendChild(bookCloneTemplate);
      });
    });

    document.getElementById("search-book").addEventListener("input", (e) => {
      const searchText = e.target.value.toLowerCase();

      document.querySelectorAll("#books-list .book:not(:first-child)").forEach((li) => {
        const title = li.querySelector(".titre")?.textContent.toLowerCase() || "";

        if (title.includes(searchText)) {
          li.style.display = "block";
        } else {
          li.style.display = "none";
        }
      });
    });

  });
</script>
<?php include './templates/footer.php'; ?>