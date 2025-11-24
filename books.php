<?php
    $selectedMenu = 'books';
?>

<?php include './templates/header.php'; ?>

Books

<script type="module">
  import { getAllBooks } from "./services/books.js";

  document.addEventListener("DOMContentLoaded", () => {
    getAllBooks().then((books) => {
      console.log(books);
    });
  });
</script>
<?php include './templates/footer.php'; ?>