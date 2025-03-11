<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
session_start();

// Get all categories
$stmt = $pdo->prepare("SELECT categories.*, COUNT(articles.id) as article_count 
                      FROM categories 
                      LEFT JOIN articles ON categories.id = articles.category_id 
                      GROUP BY categories.id 
                      ORDER BY categories.name");
$stmt->execute();
$categories = $stmt->fetchAll();
?>

<?php include 'includes/header.php'; ?>

<main class="flex-grow py-10 bg-dark-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-3xl font-serif font-bold text-white">Categories</h1>
            <p class="mt-2 text-dark-400">Browse all content categories</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach($categories as $category): ?>
                <a href="category.php?id=<?= $category['id'] ?>" class="bg-dark-900 rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow group">
                    <div class="p-6">
                        <h2 class="text-xl font-bold text-white group-hover:text-primary-400 transition-colors"><?= htmlspecialchars($category['name']) ?></h2>
                        <?php if($category['description']): ?>
                            <p class="mt-2 text-dark-300 line-clamp-2"><?= htmlspecialchars($category['description']) ?></p>
                        <?php endif; ?>
                        <div class="mt-4 flex items-center justify-between">
                            <span class="text-sm text-dark-400"><?= $category['article_count'] ?> articles</span>
                            <span class="text-primary-500 group-hover:translate-x-1 transition-transform">
                                <i class="fas fa-arrow-right"></i>
                            </span>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

