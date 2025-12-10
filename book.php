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
    <h3>Description</h3>
    <p class="description"></p>
    <h3>Propriétaire</h3>
    <div class="owner">
      <img src="assets/default-avatar.png" alt="Avatar du propriétaire" class="owner-avatar">
      <p class="proprio"></p>
    </div>
    <button type="button" id="send-msg">Envoyer un message</button>
  </div>

</section>


<script type="module">
  import {
    getUserById
  } from './services/profile.js';
  import {
    getBookById
  } from "./services/books.js";
  import {
    createConversationWithUserId
  } from "./services/conversations.js";

  document.addEventListener("DOMContentLoaded", () => {
    getBookById(new URLSearchParams(window.location.search).get('id')).then((book) => {
      document.querySelector(".book-img").style.backgroundImage = `url(${book.image})`;
      document.querySelector(".titre").innerText = book.titre;
      document.querySelector(".auteur").innerText = "par" + " " + book.auteur;
      document.querySelector(".description").innerText = book.description;
      document.querySelector(".proprio").innerText = book.user_name;
      getUserById(book.user_id).then((user) => {
        document.querySelector(".owner-avatar").src = user.image;
      });

      document.querySelector("#send-msg").addEventListener("click", () => {

        createConversationWithUserId(book.user_id).then((conv) => {
          console.log(conv)
          window.location.href = `index.php?page=messages&conversation_id=${conv.conversation_id}`;
        }).catch((err) => alert(err));
      })
    });

  });
</script>
<?php include './templates/footer.php'; ?>