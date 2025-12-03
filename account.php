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
                <hr/>
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
          <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Accusamus praesentium alias eum magnam animi, dolor voluptates ex? Id quasi sint quas, similique doloribus, voluptates libero blanditiis maiores molestias, eaque illum!</p>
    </div>

<script type="module">
    import { getConnectedUser, updateUser } from './services/profile.js';
    
    document.addEventListener("DOMContentLoaded", () => {
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
          })
          .catch((error) => {
            // Si pas de user connecté → on retourne vers la page de login
            window.location.href = "login.php";
          });
    });

    document.getElementById("submit-creation").addEventListener('click', () => {
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const username = document.getElementById('username').value;
        
        updateUser(email, password, username)
          .then((data) => {
            console.log("Profil édité avec succès", data);
            window.location.href = "account.php";
          })
          .catch((error) => {
            console.error("Erreur lors de la modification", error);
          });
    });
</script>

<?php include 'templates/footer.php'; ?>