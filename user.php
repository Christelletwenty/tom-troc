<?php
$selectedMenu = 'home';
?>

<?php include 'templates/header.php'; ?>

<div class="user-profile-page">

  <div class="user-profile-layout">

    <div class="user-profile-card">

      <form class="user-profile-avatar-form" action="api/upload_avatar.php" method="post" enctype="multipart/form-data">
        <label class="user-profile-avatar-wrapper">
          <img src="assets/default-avatar.png" alt="Avatar" class="user-profile-avatar">
        </label>
      </form>

      <hr class="user-profile-separator" />

      <div>
        <span class="user-profile-username"></span>
        <span class="user-profile-created">Membre depuis</span>

        <span class="user-profile-library-label">Bibliothèque</span>
        <span class="user-profile-books-count"></span>

        <button class="user-profile-message-btn">Écrire un message</button>
      </div>

    </div>

    <div class="user-books-container">
      <table class="user-books-table">
        <thead>
          <tr>
            <th>Photo</th>
            <th>Titre</th>
            <th>Auteur</th>
            <th>Description</th>
          </tr>
        </thead>

        <tbody id="user-books-list"></tbody>

      </table>
    </div>

  </div>
</div>


<script type="module">
  import {
    getUserBooks,
    getUserById
  } from './services/profile.js';

  document.addEventListener("DOMContentLoaded", () => {
    const params = new URLSearchParams(window.location.search);
    //Récupération de l'id de l'utilisateur
    const userId = params.get("user_id");

    // Références DOM avec les nouvelles classes
    const avatarImg = document.querySelector(".user-profile-avatar");
    const usernameEl = document.querySelector(".user-profile-username");
    const createdEl = document.querySelector(".user-profile-created");
    const booksCount = document.querySelector(".user-profile-books-count");
    const tbody = document.getElementById("user-books-list");

    //On redirige vers la page des livres si y a pas d'utilisateur id
    if (!userId) {
      window.location.href = "index.php?page=books";
      return;
    }

    // Profil
    getUserById(userId)
      .then((user) => {
        //avatar + nom de l'utilisateur
        avatarImg.src = user.image || "assets/default-avatar.png";
        usernameEl.textContent = user.username || "";
        //Date d'inscription de l'utilisateur
        if (user.created_at) {
          const date = new Date(user.created_at);
          createdEl.textContent = "Membre depuis " + date.toLocaleDateString("fr-FR", {
            year: "numeric",
            month: "long",
          });
        }
      });

    // Livres
    getUserBooks(userId)
      .then((books) => {
        //Affichage du nombre total de livres
        booksCount.textContent = `${books.length} livres`;
        tbody.innerHTML = "";

        books.forEach((book, i) => {
          //Alternance de style pour les lignes
          const rowClass = i % 2 === 0 ? "user-book-row even" : "user-book-row odd";

          const tr = document.createElement("tr");
          tr.className = rowClass;

          tr.innerHTML = `
          <td><img src="${book.image}" class="user-books-photo"></td>
          <td>${book.titre}</td>
          <td>${book.auteur}</td>
          <td class="user-books-desc">${book.description}</td>
        `;

          tbody.appendChild(tr);
        });

      });
  });
</script>

<?php include 'templates/footer.php'; ?>