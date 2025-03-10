<?php
require_once 'models/article.php';

// Get article slug from URL
$slug = isset($_GET['slug']) ? $_GET['slug'] : '';

if (empty($slug)) {
    http_response_code(404);
    require_once 'views/404.php';
    exit;
}

// Get article by slug
$article = getArticleBySlug($slug);

if (!$article) {
    http_response_code(404);
    require_once 'views/404.php';
    exit;
}

// Get related articles
$allArticles = getAllArticles();
$relatedArticles = array_filter($allArticles, function($a) use ($article) {
    return $a['category'] === $article['category'] && $a['id'] !== $article['id'];
});
$relatedArticles = array_slice($relatedArticles, 0, 3);

// Load the view
require_once 'views/article.php';

