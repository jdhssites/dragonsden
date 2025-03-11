<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
session_start();

// Get author ID from URL
$author_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if(!$author_id) {
    setFlashMessage('error', 'Invalid author ID.');
    header('Location: index.php');
    exit;
}

// Get author details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$author_id]);
$author = $stmt->fetch();

if(!$author) {
    setFlashMessage('error', 'Author not found.');
    header('Location: index.php');
    exit;
}

// Get author's articles
$stmt = $pdo->prepare("SELECT articles.*, categories.name as category_name 
                      FROM articles 
                      LEFT JOIN categories ON articles.category_id = categories.id
                      WHERE articles.user_id = ? 
                      ORDER BY articles.created_at DESC");
$stmt->execute([$author_id]);
$articles = $stmt->fetchAll();

// Get author stats
$stmt = $pdo->prepare("SELECT COUNT(*) as article_count FROM articles WHERE user_id = ?");
$stmt->execute([$author_id]);
$article_count = $stmt->fetch()['article_count'];

$stmt = $pdo->prepare("SELECT SUM(views) as total_views FROM articles WHERE user_id = ?");
$stmt->execute([$author_id]);
$total_views = $stmt->fetch()['total_views'] ?: 0;
?>

<?php include 'includes/header.php'; ?>

<main class="flex-grow py-10 bg-dark-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Author Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-dark-900 rounded-xl shadow-md overflow-hidden sticky top-8">
                    <div class="p-6 text-center">
                        <img src="https://www.gravatar.com/avatar/<?= md5(strtolower(trim($author['email']))) ?>?s=150&d=mp" 
                             class="rounded-full mx-auto h-32 w-32 object-cover mb-4" alt="<?= htmlspecialchars($author['username']) ?>">
                        <h1 class="text-2xl font-bold text-white"><?= htmlspecialchars($author['username']) ?></h1>
                        <p class="text-dark-400 mt-1">Member since <?= date('M Y', strtotime($author['created_at'])) ?></p>
                        
                        <?php if($author['bio']): ?>
                            <div class="mt-4 text-dark-300 text-sm">
                                <?= htmlspecialchars($author['bio']) ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="mt-6 grid grid-cols-2 gap-4 text-center">
                            <div class="bg-dark-800 rounded-lg p-3">
                                <span class="block text-2xl font-bold text-white"><?= $article_count ?></span>
                                <span class="text-sm text-dark-400">Articles</span>
                            </div>
                            <div class="bg-dark-800 rounded-lg p-3">
                                <span class="block text-2xl font-bold text-white"><?= $total_views ?></span>
                                <span class="text-sm text-dark-400">Views</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Articles -->
            <div class="lg:col-span-3">
                <div class="mb-8">
                    <h2 class="text-2xl font-serif font-bold text-white">Articles by <?= htmlspecialchars($author['username']) ?></h2>
                </div>
                
                <?php if(empty($articles)): ?>
                    <div class="bg-dark-900 rounded-xl p-8 text-center">
                        <p class="text-dark-300">This author hasn't published any articles yet.</p>
                    </div>
                <?php else: ?>
                    <div class="space-y-8">
                        <?php foreach($articles as $article): ?>
                            <article class="bg-dark-900 rounded-xl overflow-hidden shadow-md hover:shadow-lg transition-shadow">
                                <div class="md:flex">
                                    <div class="md:flex-shrink-0 md:w-48 h-48">
                                        <?php if($article['image']): ?>
                                            <img src="uploads/<?= htmlspecialchars($article['image']) ?>" alt="<?= htmlspecialchars($article['title']) ?>" class="w-full h-full object-cover">
                                        <?php else: ?>
                                            <img src="assets/images/placeholder.jpg" alt="Placeholder" class="w-full h-full object-cover">
                                        <?php endif; ?>
                                    </div>
                                    <div class="p-6 flex flex-col justify-between">
                                        <div>
                                            <?php if($article['category_name']): ?>
                                                <a href="category.php?id=<?= $article['category_id'] ?>" class="inline-block px-2 py-1 text-xs font-medium bg-dark-800 text-primary-400 rounded mb-2">
                                                    <?= htmlspecialchars($article['category_name']) ?>
                                                </a>
                                            <?php endif; ?>
                                            <h2 class="text-xl font-bold text-white mb-2">
                                                <a href="article.php?id=<?= $article['id'] ?>" class="hover:text-primary-400 transition-colors">
                                                    <?= htmlspecialchars($article['title']) ?>
                                                </a>
                                            </h2>
                                            <p class="text-dark-300 mb-4 line-clamp-2">
                                                <?= substr(strip_tags($article['content']), 0, 150) ?>...
                                            </p>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <div class="text-sm text-dark-400">
                                                <span><?= formatDate($article['created_at']) ?></span>
                                            </div>
                                            <div class="flex items-center text-sm text-dark-400">
                                                <span><i class="fas fa-eye mr-1"></i> <?= $article['views'] ?> views</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

