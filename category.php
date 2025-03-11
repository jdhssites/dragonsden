<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
session_start();

// Get category by ID or slug
$category = null;
$articles = [];

if (isset($_GET['id'])) {
    $category_id = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$category_id]);
    $category = $stmt->fetch();
} elseif (isset($_GET['slug'])) {
    $category_slug = $_GET['slug'];
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE slug = ?");
    $stmt->execute([$category_slug]);
    $category = $stmt->fetch();
}

// If category found, get articles
if ($category) {
    $stmt = $pdo->prepare("
        SELECT a.*, u.username as author_name 
        FROM articles a
        JOIN users u ON a.user_id = u.id
        WHERE a.category_id = ?
        ORDER BY a.created_at DESC
    ");
    $stmt->execute([$category['id']]);
    $articles = $stmt->fetchAll();
} else {
    // Category not found
    setFlashMessage('error', 'Category not found.');
    header('Location: categories.php');
    exit;
}

// Get total article count
$article_count = count($articles);

// Page title
$page_title = htmlspecialchars($category['name']) . ' - Dragon\'s Den';

include 'includes/header.php';
?>

<main class="container mx-auto px-4 py-8">
    <div class="max-w-5xl mx-auto">
        <!-- Category Header -->
        <div class="mb-8 text-center">
            <h1 class="text-4xl font-bold text-white mb-2"><?= htmlspecialchars($category['name']) ?></h1>
            <?php if (!empty($category['description'])): ?>
                <p class="text-dark-300 text-lg"><?= htmlspecialchars($category['description']) ?></p>
            <?php endif; ?>
            <div class="mt-4 text-dark-400">
                <span class="inline-flex items-center">
                    <i class="fas fa-newspaper mr-2"></i>
                    <?= $article_count ?> article<?= $article_count != 1 ? 's' : '' ?>
                </span>
            </div>
        </div>

        <?php if (empty($articles)): ?>
            <div class="bg-dark-800 rounded-xl p-8 text-center">
                <p class="text-dark-300 mb-4">No articles found in this category.</p>
                <?php if (isLoggedIn()): ?>
                    <a href="create-article.php?category=<?= $category['id'] ?>" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 focus:ring-offset-dark-950 transition-colors">
                        <i class="fas fa-plus mr-2"></i> Create First Article
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <!-- Articles Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($articles as $article): ?>
                    <div class="bg-dark-800 rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                        <?php if (!empty($article['image'])): ?>
                            <a href="article.php?id=<?= $article['id'] ?>">
                                <img src="uploads/<?= htmlspecialchars($article['image']) ?>" alt="<?= htmlspecialchars($article['title']) ?>" class="w-full h-48 object-cover">
                            </a>
                        <?php else: ?>
                            <a href="article.php?id=<?= $article['id'] ?>">
                                <div class="w-full h-48 bg-dark-700 flex items-center justify-center">
                                    <i class="fas fa-newspaper text-4xl text-dark-500"></i>
                                </div>
                            </a>
                        <?php endif; ?>
                        
                        <div class="p-6">
                            <a href="article.php?id=<?= $article['id'] ?>" class="block">
                                <h2 class="text-xl font-bold text-white hover:text-primary-400 transition-colors"><?= htmlspecialchars($article['title']) ?></h2>
                            </a>
                            
                            <div class="mt-2 text-dark-400 text-sm flex items-center">
                                <i class="fas fa-user-edit mr-1"></i>
                                <a href="author.php?id=<?= $article['user_id'] ?>" class="hover:text-primary-400 transition-colors">
                                    <?= htmlspecialchars($article['author_name']) ?>
                                </a>
                                <span class="mx-2">•</span>
                                <i class="far fa-calendar-alt mr-1"></i>
                                <?= date('M j, Y', strtotime($article['created_at'])) ?>
                            </div>
                            
                            <p class="mt-3 text-dark-300 line-clamp-3">
                                <?= htmlspecialchars(substr(strip_tags($article['content']), 0, 150)) ?>...
                            </p>
                            
                            <div class="mt-4 flex items-center justify-between">
                                <div class="flex items-center text-dark-400 text-sm">
                                    <i class="far fa-eye mr-1"></i>
                                    <?= $article['views'] ?> view<?= $article['views'] != 1 ? 's' : '' ?>
                                </div>
                                <a href="article.php?id=<?= $article['id'] ?>" class="text-primary-400 hover:text-primary-300 transition-colors text-sm font-medium">
                                    Read more <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

