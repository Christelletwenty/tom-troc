<?php
    $selectedMenu = 'login';
?>

<?php include './templates/header.php'; ?>

<!DOCTYPE html>
<html lang="en">
    <div class="connexion">
        <div class="form-connexion">
        <form action="" method="post">
            <span class="connec">Connexion</span>
            <label for="email"> Adresse email</label>
            <input type="email" id="email" name="email" required><br><br>

            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required><br><br>

            <button type="submit">S'inscrire</button>

            <span class="register">Pas de compte ? Inscrivez-vous</span>
        </form>
        </div>
        <div class="mask-group"></div>
    </div>

<?php include './templates/footer.php'; ?>