<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
session_start();

// This script will fix any issues with categories in the database

echo "<h1>Category Fix Utility</h1>";

// Check if we need to run the fix
$run_fix = isset($_GET['run']) && $_GET['run'] == 'true';

if ($run_fix) {
    // 1. Make sure all categories have unique IDs
    echo "<h2>Fixing category IDs...</h2>";
    
    // Get all categories
    $stmt = $pdo->prepare("SELECT * FROM categories ORDER BY id");
    $stmt->execute();
    $categories = $stmt->fetchAll();
    
    // Check for duplicate IDs
    $ids = [];
    $duplicates = [];
    
    foreach($categories as $category) {
        if (in_array($category['id'], $ids)) {
            $duplicates[] = $category['id'];
        } else {
            $ids[] = $category['id'];
        }
    }
    
    if (!empty($duplicates)) {
        echo "<p>Found duplicate category IDs: " . implode(", ", $duplicates) . "</p>";
        echo "<p>This requires manual database intervention. Please contact your database administrator.</p>";
    } else {
        echo "<p>No duplicate category IDs found.</p>";
    }
    
    // 2. Fix any articles with invalid category IDs
    echo "<h2>Fixing articles with invalid category IDs...</h2>";
    
    // Find articles with invalid category IDs
    $stmt = $pdo->prepare("SELECT a.id, a.title, a.category_id FROM articles a LEFT JOIN categories c ON a.category_id = c.id WHERE a.category_id IS NOT NULL AND c.id IS NULL");
    $stmt->execute();
    $invalid_articles = $stmt->fetchAll();
    
    if (!empty($invalid_articles)) {
        echo "<p>Found " . count($invalid_articles) . " articles with invalid category IDs.</p>";
        
        // Set these articles to NULL category
        $stmt = $pdo->prepare("UPDATE articles SET category_id = NULL WHERE id = ?");
        
        foreach($invalid_articles as $article) {
            $stmt->execute([$article['id']]);
            echo "<p>Fixed article ID " . $article['id'] . ": " . htmlspecialchars($article['title']) . " (removed invalid category ID " . $article['category_id'] . ")</p>";
        }
        
        echo "<p>All invalid category references fixed.</p>";
    } else {
        echo "<p>No articles with invalid category IDs found.</p>";
    }
    
    // 3. Make sure all categories have a slug
    echo "<h2>Fixing categories without slugs...</h2>";
    
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE slug IS NULL OR slug = ''");
    $stmt->execute();
    $no_slug_categories = $stmt->fetchAll();
    
    if (!empty($no_slug_categories)) {
        echo "<p>Found " . count($no_slug_categories) . " categories without slugs.</p>";
        
        $stmt = $pdo->prepare("UPDATE categories SET slug = ? WHERE id = ?");
        
        foreach($no_slug_categories as $category) {
            // Create a slug from the name
            $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $category['name']));
            $stmt->execute([$slug, $category['id']]);
            echo "<p>Added slug '" . $slug . "' to category: " . htmlspecialchars($category['name']) . "</p>";
        }
        
        echo "<p>All categories now have slugs.</p>";
    } else {
        echo "<p>All categories already have slugs.</p>";
    }
    
    echo "<p>Category fixes completed.</p>";
    echo "<p><a href='debug-categories.php'>View updated category information</a></p>";
} else {
    // Show warning and confirmation button
    echo "<div style='background-color: #ffdddd; padding: 15px; border: 1px solid #ff0000; margin-bottom: 20px;'>";
    echo "<p><strong>Warning:</strong> This utility will modify your database to fix category-related issues.</p>";
    echo "<p>It will:</p>";
    echo "<ul>";
    echo "<li>Check for and report duplicate category IDs</li>";
    echo "<li>Fix articles with invalid category IDs by setting them to NULL</li>";
    echo "<li>Add slugs to categories that don't have them</li>";
    echo "</ul>";
    echo "<p>It's recommended to backup your database before proceeding.</p>";
    echo "</div>";
    
    echo "<p><a href='fix-categories.php?run=true' style='background-color: #4CAF50; color: white; padding: 10px 15px; text-decoration: none; display: inline-block;'>Run Category Fix</a></p>";
    echo "<p><a href='debug-categories.php'>View current category information first</a></p>";
}
?>

