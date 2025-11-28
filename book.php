<?php
    $selectedMenu = 'books';
?>

<?php include './templates/header.php'; ?>

<section class="book-detail">
  <div class="book-img"></div>

  <div class="book-info">
    <h3 class="titre"></h3>
    <h2 class="auteur"></h2>
    <hr>
    <h3 class="description">Description</h3>
    <h3 class="proprio">Propriétaire</h3>
    <span class="soldby">Vendu par:</span>
    <span class="register"><a href="message.php">Envoyer un message</a></span>
  </div>
        
</section>


<script type="module">
  import { getBookById } from "./services/books.js";

  document.addEventListener("DOMContentLoaded", () => {
    getBookById(new URLSearchParams(window.location.search).get('id')).then((book) => {
      document.querySelector(".book-img").style.backgroundImage = `url(${book.image})`;
    });

  });
</script>
<?php include './templates/footer.php'; ?>