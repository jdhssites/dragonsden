<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
session_start();

// This is a debugging file to check all category links in the website

echo "<h1>Category Links Debugging</h1>";

// Get all categories
$stmt = $pdo->prepare("SELECT * FROM categories ORDER BY id");
$stmt->execute();
$categories = $stmt->fetchAll();

echo "<h2>All Categories and Their Links:</h2>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>ID</th><th>Name</th><th>Slug</th><th>Link in Header</th><th>Link in Footer</th><th>Link in Sidebar</th></tr>";

// Get the HTML content of key files
$header_content = file_get_contents('includes/header.php');
$footer_content = file_get_contents('includes/footer.php');
$sidebar_content = file_exists('includes/sidebar.php') ? file_get_contents('includes/sidebar.php') : "Sidebar file not found";

foreach($categories as $category) {
    echo "<tr>";
    echo "<td>" . $category['id'] . "</td>";
    echo "<td>" . htmlspecialchars($category['name']) . "</td>";
    echo "<td>" . htmlspecialchars($category['slug']) . "</td>";
    
    // Check header links
    $header_pattern = '/href=[\'"]category\.php\?id=' . $category['id'] . '[\'"]|href=[\'"]category\.php\?id=' . preg_quote($category['slug'], '/') . '[\'"]|href=[\'"]category\.php\?slug=' . preg_quote($category['slug'], '/') . '[\'"]|href=[\'"]category\/' . preg_quote($category['slug'], '/') . '[\'"]|href=[\'"]category\/' . $category['id'] . '[\'"]|href=[\'"]category\.php\?id=1[\'"].*?' . preg_quote($category['name'], '/') . '/i';
    preg_match($header_pattern, $header_content, $header_matches);
    echo "<td>" . (count($header_matches) > 0 ? htmlspecialchars($header_matches[0]) : "Not found") . "</td>";
    
    // Check footer links
    $footer_pattern = '/href=[\'"]category\.php\?id=' . $category['id'] . '[\'"]|href=[\'"]category\.php\?id=' . preg_quote($category['slug'], '/') . '[\'"]|href=[\'"]category\.php\?slug=' . preg_quote($category['slug'], '/') . '[\'"]|href=[\'"]category\/' . preg_quote($category['slug'], '/') . '[\'"]|href=[\'"]category\/' . $category['id'] . '[\'"]|href=[\'"]category\.php\?id=1[\'"].*?' . preg_quote($category['name'], '/') . '/i';
    preg_match($footer_pattern, $footer_content, $footer_matches);
    echo "<td>" . (count($footer_matches) > 0 ? htmlspecialchars($footer_matches[0]) : "Not found") . "</td>";
    
    // Check sidebar links
    $sidebar_pattern = '/href=[\'"]category\.php\?id=' . $category['id'] . '[\'"]|href=[\'"]category\.php\?id=' . preg_quote($category['slug'], '/') . '[\'"]|href=[\'"]category\.php\?slug=' . preg_quote($category['slug'], '/') . '[\'"]|href=[\'"]category\/' . preg_quote($category['slug'], '/') . '[\'"]|href=[\'"]category\/' . $category['id'] . '[\'"]|href=[\'"]category\.php\?id=1[\'"].*?' . preg_quote($category['name'], '/') . '/i';
    preg_match($sidebar_pattern, $sidebar_content, $sidebar_matches);
    echo "<td>" . (count($sidebar_matches) > 0 ? htmlspecialchars($sidebar_matches[0]) : "Not found") . "</td>";
    
    echo "</tr>";
}

echo "</table>";

// Check for hardcoded category IDs
echo "<h2>Files with Hardcoded Category IDs:</h2>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>File</th><th>Line</th><th>Content</th></tr>";

$files_to_check = [
    'includes/header.php',
    'includes/footer.php',
    'includes/sidebar.php',
    'index.php',
    'category.php',
    'categories.php',
    'article.php',
    'create-article.php',
    'edit-article.php'
];

foreach($files_to_check as $file) {
    if(file_exists($file)) {
        $lines = file($file);
        $line_number = 1;
        
        foreach($lines as $line) {
            // Look for category.php?id=1 or similar hardcoded IDs
            if(preg_match('/category\.php\?id=\d+/', $line)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($file) . "</td>";
                echo "<td>" . $line_number . "</td>";
                echo "<td>" . htmlspecialchars(trim($line)) . "</td>";
                echo "</tr>";
            }
            $line_number++;
        }
    }
}

echo "</table>";

// Provide a fix utility
echo "<h2>Fix Category Links:</h2>";

if(isset($_GET['fix']) && $_GET['fix'] == 'true') {
    echo "<p>Attempting to fix hardcoded category links...</p>";
    
    $files_fixed = 0;
    $links_fixed = 0;
    
    foreach($files_to_check as $file) {
        if(file_exists($file)) {
            $content = file_get_contents($file);
            $original_content = $content;
            $file_fixed = false;
            
            // For each category, replace hardcoded links with the correct ID
            foreach($categories as $category) {
                $pattern = '/category\.php\?id=1([\'"].*?' . preg_quote($category['name'], '/') . ')/i';
                $replacement = 'category.php?id=' . $category['id'] . '$1';
                
                $new_content = preg_replace($pattern, $replacement, $content, -1, $count);
                
                if($count > 0) {
                    $content = $new_content;
                    $links_fixed += $count;
                    $file_fixed = true;
                }
            }
            
            // If we made changes, save the file
            if($file_fixed) {
                file_put_contents($file, $content);
                $files_fixed++;
                echo "<p>Fixed file: " . htmlspecialchars($file) . "</p>";
            }
        }
    }
    
    echo "<p>Fixed $links_fixed category links in $files_fixed files.</p>";
    echo "<p><a href='debug-category-links.php'>Refresh to see results</a></p>";
} else {
    echo "<p>This utility will scan your website files and fix hardcoded category IDs.</p>";
    echo "<p>It will replace instances of 'category.php?id=1' with the correct category ID based on the category name.</p>";
    echo "<p><a href='debug-category-links.php?fix=true' style='background-color: #4CAF50; color: white; padding: 10px 15px; text-decoration: none; display: inline-block;'>Run Fix Utility</a></p>";
}

// Test all category links
echo "<h2>Test All Category Links:</h2>";
echo "<ul>";
foreach($categories as $category) {
    echo "<li><a href='category.php?id=" . $category['id'] . "'>" . htmlspecialchars($category['name']) . " (by ID: " . $category['id'] . ")</a></li>";
}
echo "</ul>";

echo "<h3>Category Links by Slug:</h3>";
echo "<ul>";
foreach($categories as $category) {
    echo "<li><a href='category.php?slug=" . $category['slug'] . "'>" . htmlspecialchars($category['name']) . " (by slug: " . $category['slug'] . ")</a></li>";
}
echo "</ul>";
?>

