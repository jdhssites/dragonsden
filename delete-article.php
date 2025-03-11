<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
session_start();

// Check if user is logged in
if(!isLoggedIn()) {
    setFlashMessage('error', 'Please login to delete articles.');
    header('Location: login.php');
    exit;
}

// Check if form was submitted
if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Get article ID from form
$article_id = isset($_POST['article_id']) ? (int)$_POST['article_id'] : 0;

if(!$article_id) {
    setFlashMessage('error', 'Invalid article ID.');
    header('Location: index.php');
    exit;
}

// Get article details
$stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
$stmt->execute([$article_id]);
$article = $stmt->fetch();

if(!$article) {
    setFlashMessage('error', 'Article not found.');
    header('Location: index.php');
    exit;
}

// Check if user is the author
if($article['user_id'] != $_SESSION['user_id']) {
    setFlashMessage('error', 'You can only delete your own articles.');
    header('Location: article.php?id=' . $article_id);
    exit;
}

// Delete the article image if exists
if($article['image'] && file_exists('uploads/' . $article['image'])) {
    unlink('uploads/' . $article['image']);
}

// Delete the article
$stmt = $pdo->prepare("DELETE FROM articles WHERE id = ?");
if($stmt->execute([$article_id])) {
    setFlashMessage('success', 'Article deleted successfully!');
    header('Location: my-articles.php');
} else {
    setFlashMessage('error', 'Failed to delete article. Please try again.');
    header('Location: article.php?id=' . $article_id);
}
exit;

