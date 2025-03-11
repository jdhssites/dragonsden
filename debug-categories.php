<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
session_start();

// This is a debugging file to check all categories in the database

echo "<h1>Category Debugging</h1>";

// Get all categories
$stmt = $pdo->prepare("SELECT * FROM categories ORDER BY id");
$stmt->execute();
$categories = $stmt->fetchAll();

echo "<h2>All Categories in Database:</h2>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>ID</th><th>Name</th><th>Slug</th><th>Description</th><th>Created At</th><th>Article Count</th></tr>";

foreach($categories as $category) {
    // Get article count for this category
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM articles WHERE category_id = ?");
    $stmt->execute([$category['id']]);
    $count = $stmt->fetch()['count'];
    
    echo "<tr>";
    echo "<td>" . $category['id'] . "</td>";
    echo "<td>" . htmlspecialchars($category['name']) . "</td>";
    echo "<td>" . htmlspecialchars($category['slug']) . "</td>";
    echo "<td>" . htmlspecialchars($category['description'] ?? '') . "</td>";
    echo "<td>" . $category['created_at'] . "</td>";
    echo "<td>" . $count . "</td>";
    echo "</tr>";
}

echo "</table>";

// Check for articles with no category
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM articles WHERE category_id IS NULL");
$stmt->execute();
$null_count = $stmt->fetch()['count'];

echo "<p>Articles with no category: " . $null_count . "</p>";

// Check for articles with invalid category
$stmt = $pdo->prepare("SELECT a.id, a.title, a.category_id FROM articles a LEFT JOIN categories c ON a.category_id = c.id WHERE a.category_id IS NOT NULL AND c.id IS NULL");
$stmt->execute();
$invalid_categories = $stmt->fetchAll();

if (!empty($invalid_categories)) {
    echo "<h2>Articles with Invalid Category IDs:</h2>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Article ID</th><th>Title</th><th>Invalid Category ID</th></tr>";
    
    foreach($invalid_categories as $article) {
        echo "<tr>";
        echo "<td>" . $article['id'] . "</td>";
        echo "<td>" . htmlspecialchars($article['title']) . "</td>";
        echo "<td>" . $article['category_id'] . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p>No articles with invalid category IDs found.</p>";
}

// Show all articles with their categories
$stmt = $pdo->prepare("SELECT a.id, a.title, a.category_id, c.name as category_name 
                      FROM articles a 
                      LEFT JOIN categories c ON a.category_id = c.id 
                      ORDER BY a.id");
$stmt->execute();
$all_articles = $stmt->fetchAll();

echo "<h2>All Articles with Their Categories:</h2>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Article ID</th><th>Title</th><th>Category ID</th><th>Category Name</th></tr>";

foreach($all_articles as $article) {
    echo "<tr>";
    echo "<td>" . $article['id'] . "</td>";
    echo "<td>" . htmlspecialchars($article['title']) . "</td>";
    echo "<td>" . ($article['category_id'] ?? 'NULL') . "</td>";
    echo "<td>" . htmlspecialchars($article['category_name'] ?? 'Uncategorized') . "</td>";
    echo "</tr>";
}

echo "</table>";

// Test category links
echo "<h2>Test Category Links:</h2>";
echo "<ul>";
foreach($categories as $category) {
    echo "<li><a href='category.php?id=" . $category['id'] . "'>" . htmlspecialchars($category['name']) . " (ID: " . $category['id'] . ")</a></li>";
}
echo "</ul>";
?>

