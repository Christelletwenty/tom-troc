<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TomTroc</title>
    <link rel="stylesheet" href="styles.css" />
    <script type="module">
        import { getConnectedUser } from './services/profile.js';

        document.addEventListener("DOMContentLoaded", () => {
            //Récupération de l'utilisateur connecté
            getConnectedUser()
            .then((user) => {
                console.log(user);
                if(user) {
                    document.getElementById("logout-button").style.display = 'flex';
                } else {
                    document.getElementById("login-button").style.display = 'flex';
                }
            })
            .catch(() => {
                document.getElementById("login-button").style.display = 'flex';
            });
        });
    </script>
</head>
<body>
    <header>
        <nav>
            <ul>
                <li>
                    <a href="./">
                        <img src="assets/logo.png" alt="Logo Tom Troc" />
                    </a>
                </li>
                <li><a href="./" <?php echo isset($selectedMenu) && $selectedMenu === 'home' ? 'class="active"' : ''; ?>>Accueil</a></li>
                <li><a href="books.php" <?php echo isset($selectedMenu) && $selectedMenu === 'book' ? 'class="active"' : ''; ?>>Nos livres à l'échange</a></li>
            </ul>
            <ul>
                <li><a href="messagerie.php" <?php echo isset($selectedMenu) && $selectedMenu === 'message' ? 'class="active"' : ''; ?>>Messagerie</a></li>
                <li><a href="account.php" <?php echo isset($selectedMenu) && $selectedMenu === 'account' ? 'class="active"' : ''; ?>>Mon compte</a></li>

                <li id="logout-button"><a href="logout.php" class="logout">Déconnexion</a></li>
                <li id="login-button"><a href="login.php" <?php echo isset($selectedMenu) && $selectedMenu === 'login' ? 'class="active"' : ''; ?>>Connexion</a></li>
            </ul>
        </nav>
    </header>
    <main>