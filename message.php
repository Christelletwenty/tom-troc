<?php 
    $selectedMenu = 'home';
?>

<?php include 'templates/header.php'; ?>

<div class="my-account">
        <div class="infos-perso">

            <div class="profile">
                <form class="avatar-form" action="api/upload_avatar.php" method="post" enctype="multipart/form-data">
                    <label class="avatar-wrapper">
                        <img src="assets/default-avatar.png" alt="Avatar" class="avatar-img">
                    </label>
                </form>
                <hr/>
                <span class="proprio"></span>
                <span class="created-at">Membre depuis</span>
                <span class="library">Bibliothèque</span>
                <span class="my-books"></span>

                <button>Ecrire un message</button>
            </div>

 <div class="my-books">
            <table class="books-table">
                <thead>
                    <tr>
                        <th>Photo</th>
                        <th>Titre</th>
                        <th>Auteur</th>
                        <th>Description</th>
                    </tr>
                </thead>

                <tbody id="user-books-list">
                </tbody>

            </table>
        </div>
    </div>


<script type="module">
import { getUserBooks, getUserById } from './services/profile.js';

document.addEventListener("DOMContentLoaded", () => {
  const params = new URLSearchParams(window.location.search);
  const userId = params.get("user_id");

  // Références DOM
  const avatarImg   = document.querySelector(".avatar-img");
  const proprioSpan = document.querySelector(".proprio");
  const createdSpan = document.querySelector(".created-at");
  const myBooksSpan = document.querySelector(".my-books");
  const tbody       = document.getElementById("user-books-list");

  if (!userId) {
    console.warn("Aucun id de user dans l'URL");
    window.location.href = "books.php"
    return;
  }

  // 1) Infos du user
  getUserById(userId)
    .then((user) => {
      avatarImg.src = user.image || "assets/default-avatar.png";
      proprioSpan.textContent = user.username || "";

      if (user.created_at) {
        const date = new Date(user.created_at);
        const formatted = date.toLocaleDateString("fr-FR", {
          year: "numeric",
          month: "long",
        });
        createdSpan.textContent = `Membre depuis ${formatted}`;
      } else {
        createdSpan.textContent = "Membre depuis";
      }
    })
    .catch((error) => {
      console.error("Erreur lors de la récupération du user", error);
    });

  // 2) Livres du user
  getUserBooks(userId)
    .then((books) => {
      tbody.innerHTML = "";
      myBooksSpan.textContent = `${books.length} livre(s)`;

      books.forEach((book) => {
        const tr = document.createElement("tr");
        tr.classList.add("book-row");
        tr.dataset.id = book.id;

        tr.innerHTML = `
          <td class="col-photo">
              <img src="${book.image || ''}" alt="${book.titre || ''}" class="book-photo">
          </td>
          <td class="col-title">
              <span class="book-title">${book.titre || ''}</span>
          </td>
          <td class="col-author">
              <span class="book-author">${book.auteur || ''}</span>
          </td>
          <td class="col-description">
              <span class="book-description">${book.description || ''}</span>
          </td>
        `;

        tbody.appendChild(tr);
      });
    })
    .catch((error) => {
      console.error("Erreur lors de la récupération des livres", error);
    });
});
</script>


<?php include 'templates/footer.php'; ?>