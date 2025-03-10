<?php
session_start();
require_once 'config/database.php';
require_once 'helpers/auth.php';
require_once 'helpers/utils.php';

// Define routes
$routes = [
    '/' => 'controllers/home.php',
    '/articles' => 'controllers/articles.php',
    '/article' => 'controllers/article.php',
    '/login' => 'controllers/login.php',
    '/register' => 'controllers/register.php',
    '/logout' => 'controllers/logout.php',
    '/admin/articles' => 'controllers/admin/articles.php',
    '/admin/article/new' => 'controllers/admin/article_new.php',
    '/admin/article/edit' => 'controllers/admin/article_edit.php',
    '/admin/article/delete' => 'controllers/admin/article_delete.php',
    '/profile' => 'controllers/profile.php',
    '/about' => 'controllers/about.php',
    '/categories' => 'controllers/categories.php',
];

// Get the current URI
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Route to the appropriate controller
if (array_key_exists($uri, $routes)) {
    require_once $routes[$uri];
} else {
    // Check if it's an article detail page
    if (preg_match('/^\/article\/([a-zA-Z0-9-]+)$/', $uri, $matches)) {
        $_GET['slug'] = $matches[1];
        require_once 'controllers/article.php';
    } else {
        // 404 page
        http_response_code(404);
        require_once 'views/404.php';
    }
}

