<?php
$selectedMenu = 'login';
?>

<?php include './templates/header.php'; ?>

<div class="connexion">
    <div class="form-connexion">
        <form>
            <span class="error"></span>
            <span class="connec">Inscription</span>

            <label for="username">Pseudo</label>
            <input type="text" id="username" name="username" required><br><br>

            <label for="email"> Adresse email</label>
            <input type="email" id="email" name="email" required><br><br>

            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required><br><br>

            <button type="button" id="submit-creation">S'inscrire</button>

            <span class="register">Déjà inscrit ? <a href="index.php?page=login">Connectez-vous</a></span>
        </form>
    </div>
    <div class="mask-group"></div>
</div>

<script type="module">
    import {
        createAccount
    } from './services/createAccount.js';
    import {
        getConnectedUser
    } from './services/profile.js';

    const connectedUser = await getConnectedUser().catch(() => null);

    if (connectedUser) {
        window.location.href = "index.php?page=account";
    }

    document.getElementById('submit-creation').addEventListener('click', () => {
        const username = document.getElementById('username').value;
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        if (!username || !email || !password) {
            document.querySelector('.error').innerText = "Veuillez remplir tous les champs.";
            return;
        }

        createAccount(username, email, password)
            .then((data) => {
                console.log("Success", data);
                // Redirection vers la page d'accueil après la création du compte
                window.location.href = "index.php";
            })
            .catch((error) => {
                console.error("Error:", error);
                document.querySelector('.error').innerText = "Pseudo déjà prit. Veuillez en choisir un autre";
            });
    });
</script>

<?php include './templates/footer.php'; ?>