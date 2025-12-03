<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TomTroc</title>
    <link rel="stylesheet" href="styles.css" />
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
                <li><a href="messages.php" <?php echo isset($selectedMenu) && $selectedMenu === 'message' ? 'class="active"' : ''; ?>>Messagerie</a></li>
                <li><a href="account.php" <?php echo isset($selectedMenu) && $selectedMenu === 'account' ? 'class="active"' : ''; ?>>Mon compte</a></li>

            <?php if(isset($_SESSION['currentUserId'])):?>
                <li><a href="logout.php" class="logout">Déconnexion</a></li>
                <?php else: ?>
                <li><a href="login.php" <?php echo isset($selectedMenu) && $selectedMenu === 'login' ? 'class="active"' : ''; ?>>Connexion</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <main>