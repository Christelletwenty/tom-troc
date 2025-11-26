<?php
    $selectedMenu = 'books';
?>

<?php include './templates/header.php'; ?>

<ul id="books-list">
  <li class="book">
    <img src="" alt="">
    <h3 class="title"></h3>
    <a href="">Voir détails</a>
  </li>
</ul>

<script type="module">
  import { getAllBooks } from "./services/books.js";

  document.addEventListener("DOMContentLoaded", () => {
    getAllBooks().then((books) => {
      console.log(books);

      books.forEach((book) => {
        const booksList = document.getElementById("books-list");
        const bookCloneTemplate = booksList.querySelector(".book").cloneNode(true);
  
        bookCloneTemplate.querySelector(".title").textContent = book.titre;
        bookCloneTemplate.querySelector("img").src = book.image;
        bookCloneTemplate.querySelector("a").href = `book.php?id=${book.id}`;

        booksList.appendChild(bookCloneTemplate);
      });
    });
  });
</script>
<?php include './templates/footer.php'; ?>