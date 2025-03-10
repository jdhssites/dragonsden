<?php
require_once 'models/article.php';

// Check if user is admin
if (!isAdmin()) {
    header('Location: /login');
    exit;
}

// Get article ID from URL
$articleId = isset($_GET['id']) ? $_GET['id'] : '';

if (empty($articleId)) {
    header('Location: /admin/articles');
    exit;
}

// Get article by ID
$article = getArticleById($articleId);

if (!$article) {
    header('Location: /admin/articles');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $excerpt = $_POST['excerpt'] ?? '';
    $content = $_POST['content'] ?? '';
    $image = $_POST['image'] ?? '/placeholder.svg?height=600&width=800';
    $readTime = intval($_POST['readTime'] ?? 5);
    $category = $_POST['category'] ?? '';
    
    // Validate required fields
    if (empty($title) || empty($excerpt) || empty($content) || empty($category)) {
        $error = 'All required fields must be filled out';
    } else {
        // Update article
        $result = updateArticle($articleId, $title, $excerpt, $content, $image, $readTime, $category);
        
        if ($result['success']) {
            header('Location: /admin/articles?message=Article updated successfully');
            exit;
        } else {
            $error = $result['message'];
        }
    }
}

// Common placeholder images
$placeholderImages = [
    '/placeholder.svg?height=600&width=800',
    '/placeholder.svg?height=600&width=800&text=Technology',
    '/placeholder.svg?height=600&width=800&text=Health',
    '/placeholder.svg?height=600&width=800&text=Business',
    '/placeholder.svg?height=600&width=800&text=Science',
    '/placeholder.svg?height=600&width=800&text=Lifestyle',
];

// Load the view
require_once 'views/admin/article_edit.php';

