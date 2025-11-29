<?php 
    $selectedMenu = 'home';
?>

<?php include 'templates/header.php'; ?>


    MON COMPTE WORKS

<script type="module">
    import { getConnectedUser } from './services/login.js';

    document.addEventListener("DOMContentLoaded", () => {

        getConnectedUser()
            .then((data) => {
                console.log("Success:", data);
            })
            .catch((error) => {
                // Si on pas de user connecté, on redirige sur la page de login
                window.location.href = "login.php";
            });
    });
</script>

<?php include 'templates/footer.php'; ?>