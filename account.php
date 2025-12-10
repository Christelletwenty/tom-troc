<?php
$selectedMenu = 'home';
?>

<?php include 'templates/header.php'; ?>


<div class="my-account">
    <h1>Mon compte</h1>
    <div class="infos-perso">

        <div class="profile">
            <form class="avatar-form" action="api/upload_avatar.php" method="post" enctype="multipart/form-data">
                <label class="avatar-wrapper">
                    <img src="assets/default-avatar.png" alt="Avatar" class="avatar-img">
                    <span class="avatar-edit">modifier</span>
                    <input type="file" name="avatar" accept="image/*" class="avatar-input" onchange="this.form.submit()">
                </label>
            </form>
            <hr />
            <span class="proprio"></span>
            <span class="created-at">Membre depuis</span>
            <span class="library">Bibliothèque</span>
            <span class="my-books"></span>
        </div>
        <div class="edit-profile">
            <form class="form-edit">
                <h3 class="perso-info">Vos informations personnelles</h3>
                <label for="email">Adresse email</label>
                <input type="email" id="email" name="email"><br><br>

                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password"><br><br>

                <label for="username">Pseudo</label>
                <input type="username" id="username" name="username"><br><br>

                <button type="button" id="submit-creation">Enregister</button>
            </form>
        </div>
    </div>

    <div class="my-books">
        <button id="add-book-btn" class="add-book-btn">+ Ajouter un livre</button>
        <table class="books-table">
            <thead>
                <tr>
                    <th>Photo</th>
                    <th>Titre</th>
                    <th>Auteur</th>
                    <th>Description</th>
                    <th>Disponibilité</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody id="user-books-list">
                <!-- Les lignes seront générées en JS -->
            </tbody>

        </table>
    </div>
    <script type="module">
        import {
            getConnectedUser,
            updateUser,
            getUserBooks,
            updateBook,
            deleteBook
        } from './services/profile.js';

        document.addEventListener("DOMContentLoaded", () => {
            //Récupération de l'utilisateur connecté
            getConnectedUser()
                .then((user) => {
                    console.log(user);

                    document.querySelector(".avatar-img").src = user.image || "assets/default-avatar.png";
                    document.querySelector(".proprio").innerText = user.user_name;
                    document.querySelector(".created-at").innerText = `Membre depuis le ${user.created_at}`;
                    document.querySelector(".my-books").innerText = `${user.library} livre(s)`;

                    document.querySelector("#email").value = user.email;
                    document.querySelector("#password").value = "";
                    document.querySelector("#username").value = user.user_name;

                    // Une fois qu'on a le currentUser, on récupère ses lvres
                    //Récupération des livres de l'utilisateur
                    const tbody = document.getElementById("user-books-list");

                    getUserBooks(user.id)
                        .then((books) => {
                            console.log("Livres utilisateur :", books);
                            tbody.innerHTML = "";

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

                    <td class="col-dispo">
                        <span class="book-dispo" data-dispo="${book.dispo}">
                            ${String(book.dispo) === "1" ? "Disponible" : "Indisponible"}
                        </span>
                    </td>

                    <td class="col-action">
                        <button type="button" class="edit-book">Éditer</button>
                        <button type="button" class="delete-book">Supprimer</button>
                    </td>
                  `;

                                tbody.appendChild(tr);
                            });
                        })
                        .catch((error) => {
                            console.error("Erreur lors de la récupération des livres", error);
                        });

                    //Gestion de la suppression et update
                    tbody.addEventListener("click", (event) => {
                        const target = event.target;
                        const row = target.closest(".book-row");
                        if (!row) return;

                        const id = row.dataset.id;

                        //SUPPRESSION
                        if (target.classList.contains("delete-book")) {
                            if (!confirm("Voulez-vous vraiment supprimer ce livre ?")) {
                                return;
                            }

                            //suppression du livre
                            deleteBook(id)
                                .then((data) => {
                                    console.log("Livre supprimé", data);
                                    row.remove();
                                })
                                .catch((error) => {
                                    console.error("Erreur lors de la suppression du livre", error);
                                    alert("Erreur lors de la suppression du livre.");
                                });
                        }

                        if (target.classList.contains("edit-book")) {
                            window.location.href = `index.php?page=updateBook&id=${id}`;
                        }
                    });
                })
                .catch((error) => {
                    // Si pas de user connecté → on retourne vers la page de login
                    window.location.href = "index.php?page=login";
                });

            //Gestion du bouton d'édition du profil
            document.getElementById("submit-creation").addEventListener('click', () => {
                const email = document.getElementById('email').value;
                const password = document.getElementById('password').value;
                const username = document.getElementById('username').value;

                updateUser(email, password, username)
                    .then((data) => {
                        console.log("Profil édité avec succès", data);
                        window.location.href = "index.php?page=account";
                    })
                    .catch((error) => {
                        console.error("Erreur lors de la modification", error);
                    });
            });
        });

        document.getElementById("add-book-btn").addEventListener("click", () => {
            window.location.href = "index.php?page=addBook";
        });
    </script>

    <?php include 'templates/footer.php'; ?>