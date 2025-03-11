<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
session_start();

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Get total articles count
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM articles");
$stmt->execute();
$total_articles = $stmt->fetch()['count'];
$total_pages = ceil($total_articles / $per_page);

// Get articles with pagination
$stmt = $pdo->prepare("SELECT articles.*, users.username, categories.name as category_name 
                      FROM articles 
                      JOIN users ON articles.user_id = users.id 
                      LEFT JOIN categories ON articles.category_id = categories.id
                      ORDER BY articles.created_at DESC
                      LIMIT ? OFFSET ?");
$stmt->execute([$per_page, $offset]);
$articles = $stmt->fetchAll();
?>

<?php include 'includes/header.php'; ?>

<main class="flex-grow py-10 bg-dark-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-3xl font-serif font-bold text-white">All Articles</h1>
            <p class="mt-2 text-dark-400">Browse all our published content</p>
        </div>
        
        <?php if(empty($articles)): ?>
            <div class="bg-dark-900 rounded-xl p-8 text-center">
                <p class="text-dark-300">No articles found.</p>
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
                                    <div class="flex items-center">
                                        <img src="https://www.gravatar.com/avatar/<?= md5(strtolower(trim($article['username']))) ?>?s=80&d=mp" alt="Author" class="w-8 h-8 rounded-full mr-2">
                                        <span class="text-sm text-dark-400"><?= htmlspecialchars($article['username']) ?></span>
                                    </div>
                                    <div class="flex items-center text-sm text-dark-400">
                                        <span><?= formatDate($article['created_at']) ?></span>
                                        <span class="mx-2">•</span>
                                        <span><i class="fas fa-eye mr-1"></i> <?= $article['views'] ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            
            <!-- Pagination -->
            <?php if($total_pages > 1): ?>
                <div class="mt-8 flex justify-center">
                    <nav class="inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        <?php if($page > 1): ?>
                            <a href="?page=<?= $page - 1 ?>" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-dark-700 bg-dark-800 text-sm font-medium text-dark-300 hover:bg-dark-700 hover:text-white">
                                <span class="sr-only">Previous</span>
                                <i class="fas fa-chevron-left h-5 w-5"></i>
                            </a>
                        <?php else: ?>
                            <span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-dark-700 bg-dark-900 text-sm font-medium text-dark-500 cursor-not-allowed">
                                <span class="sr-only">Previous</span>
                                <i class="fas fa-chevron-left h-5 w-5"></i>
                            </span>
                        <?php endif; ?>
                        
                        <?php for($i = 1; $i <= $total_pages; $i++): ?>
                            <?php if($i == $page): ?>
                                <span class="relative inline-flex items-center px-4 py-2 border border-primary-600 bg-primary-900 text-sm font-medium text-white">
                                    <?= $i ?>
                                </span>
                            <?php else: ?>
                                <a href="?page=<?= $i ?>" class="relative inline-flex items-center px-4 py-2 border border-dark-700 bg-dark-800 text-sm font-medium text-dark-300 hover:bg-dark-700 hover:text-white">
                                    <?= $i ?>
                                </a>
                            <?php endif; ?>
                        <?php endfor; ?>
                        
                        <?php if($page < $total_pages): ?>
                            <a href="?page=<?= $page + 1 ?>" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-dark-700 bg-dark-800 text-sm font-medium text-dark-300 hover:bg-dark-700 hover:text-white">
                                <span class="sr-only">Next</span>
                                <i class="fas fa-chevron-right h-5 w-5"></i>
                            </a>
                        <?php else: ?>
                            <span class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-dark-700 bg-dark-900 text-sm font-medium text-dark-500 cursor-not-allowed">
                                <span class="sr-only">Next</span>
                                <i class="fas fa-chevron-right h-5 w-5"></i>
                            </span>
                        <?php endif; ?>
                    </nav>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

