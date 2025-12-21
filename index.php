<?php
// index.php = routeur principal

// Page demandée dans l'URL, ex : index.php?page=page
$page = $_GET['page'] ?? 'home';

switch ($page) {
    case 'home':
        require __DIR__ . '/templates/home.php';
        break;

    case 'books':
        require __DIR__ . '/templates/books.php';
        break;

    case 'book':
        require __DIR__ . '/templates/book.php';
        break;

    case 'account':
        require __DIR__ . '/templates/account.php';
        break;

    case 'messages':
        require __DIR__ . '/templates/messages.php';
        break;

    case 'login':
        require __DIR__ . '/templates/login.php';
        break;

    case 'logout':
        require __DIR__ . '/logout.php';
        break;

    case 'register':
    case 'createAccount':
        require __DIR__ . '/templates/createAccount.php';
        break;

    case 'addBook':
        require __DIR__ . '/templates/addBook.php';
        break;

    case 'updateBook':
        require __DIR__ . '/templates/updateBook.php';
        break;

    case 'user':
        require __DIR__ . '/templates/user.php';
        break;

    default:
        // Définit le code HTTP 404 pour les navigateurs / SEO
        http_response_code(404);

        // Charge ta page d'erreur custom
        require 'templates/error.php';
        break;
}
