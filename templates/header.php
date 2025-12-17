<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TomTroc</title>
    <link rel="stylesheet" href="styles.css" />
    <script type="module">
        import {
            getConnectedUser
        } from './services/profile.js';

        import {
            getNotificationsCount
        } from './services/conversations.js';

        document.addEventListener("DOMContentLoaded", () => {
            //Récupération de l'utilisateur connecté
            getConnectedUser()
                .then((user) => {
                    console.log(user);
                    if (user) {
                        document.getElementById("logout-button").style.display = 'flex';
                    } else {
                        document.getElementById("login-button").style.display = 'flex';
                        document.getElementById("unread-badge").style.display = 'none';
                    }
                })
                .catch(() => {
                    document.getElementById("login-button").style.display = 'flex';
                    document.getElementById("unread-badge").style.display = 'none';
                });

            getNotificationsCount().then((data) => {
                const badge = document.getElementById("unread-badge");
                if (data.unread > 0) {
                    badge.textContent = data.unread;
                    badge.classList.remove("hidden");
                }
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
                        <img src="assets/logo.png" id="logo" alt="Logo Tom Troc" />
                    </a>
                </li>
                <li><a href="index.php?page=home" <?php echo isset($selectedMenu) && $selectedMenu === 'home' ? 'class="active"' : ''; ?>>Accueil</a></li>
                <li><a href="index.php?page=books" <?php echo isset($selectedMenu) && $selectedMenu === 'book' ? 'class="active"' : ''; ?>>Nos livres à l'échange</a></li>
            </ul>
            <ul>
                <li><a href="index.php?page=messages" <?php echo isset($selectedMenu) && $selectedMenu === 'message' ? 'class="active"' : ''; ?>><img src="assets/icon_messagerie.png">Messagerie <span id="unread-badge" class="badge hidden">0</span></a></li>
                <li><a href="index.php?page=account" <?php echo isset($selectedMenu) && $selectedMenu === 'account' ? 'class="active"' : ''; ?>><img src="assets/icon_mon_compte.png">Mon compte</a></li>

                <li id="logout-button"><a href="index.php?page=logout" class="logout">Déconnexion</a></li>
                <li id="login-button"><a href="index.php?page=login" <?php echo isset($selectedMenu) && $selectedMenu === 'login' ? 'class="active"' : ''; ?>>Connexion</a></li>
            </ul>
        </nav>
    </header>
    <main>