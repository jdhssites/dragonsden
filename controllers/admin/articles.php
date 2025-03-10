<?php
require_once 'models/article.php';

// Check if user is admin
if (!isAdmin()) {
    header('Location: /login');
    exit;
}

// Get all articles
$articles = getAllArticles();

// Handle delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_article'])) {
    $articleId = $_POST['article_id'];
    $result = deleteArticle($articleId);
    
    if ($result['success']) {
        header('Location: /admin/articles?message=Article deleted successfully');
        exit;
    } else {
        $error = $result['message'];
    }
}

// Load the view
require_once 'views/admin/articles.php';

