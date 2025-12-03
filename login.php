<?php
    session_start();
    $selectedMenu = 'login';
?>

<?php include './templates/header.php'; ?>

    <div class="connexion">
        <div class="form-connexion">
        <form>
            <span class="error"></span>
            <span class="connec">Connexion</span>
            <label for="email"> Adresse email</label>
            <input type="email" id="email" name="email" required><br><br>

            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required><br><br>

            <button type="button" id="submit-login">Se connecter</button>

            <span class="register">Pas de compte ? <a href="createAccount.php">Inscrivez-vous</a></span>
        </form>
        </div>
        <div class="mask-group"></div>
    </div>

    <script type="module">
        import { login } from './services/login.js';

        document.getElementById('submit-login').addEventListener('click', () => {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            if (!email || !password) {
                document.querySelector('.error').innerText = "Veuillez remplir tous les champs.";
                return;
            }

            login(email, password)
                .then((data) => {
                    console.log("Success:", data);
                    // Rediriger vers la page d'accueil après la connexion réussie
                    window.location.href = "index.php";
                })
                .catch((error) => {
                    console.error("Error:", error);
                    document.querySelector('.error').innerText = "Identifiant ou mot de passe incorrect";
                });
        });
    </script>

<?php include './templates/footer.php'; ?>