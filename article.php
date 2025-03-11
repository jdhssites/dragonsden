<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
session_start();

// Get article ID from URL
$article_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if(!$article_id) {
    setFlashMessage('error', 'Invalid article ID.');
    header('Location: index.php');
    exit;
}

// Get article details
$stmt = $pdo->prepare("SELECT articles.*, users.username, categories.name as category_name 
                      FROM articles 
                      JOIN users ON articles.user_id = users.id 
                      LEFT JOIN categories ON articles.category_id = categories.id
                      WHERE articles.id = ?");
$stmt->execute([$article_id]);
$article = $stmt->fetch();

if(!$article) {
    setFlashMessage('error', 'Article not found.');
    header('Location: index.php');
    exit;
}

// Get related articles
$stmt = $pdo->prepare("SELECT articles.*, users.username 
                    FROM articles 
                    JOIN users ON articles.user_id = users.id 
                    WHERE articles.category_id = ? AND articles.id != ? 
                    ORDER BY created_at DESC 
                    LIMIT 3");
$stmt->execute([$article['category_id'], $article_id]);
$relatedArticles = $stmt->fetchAll();

// Increment view count
$stmt = $pdo->prepare("UPDATE articles SET views = views + 1 WHERE id = ?");
$stmt->execute([$article_id]);
?>

<?php include 'includes/header.php'; ?>

<main class="flex-grow py-10 bg-dark-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <article>
                    <!-- Article Header -->
                    <header class="mb-8">
                        <?php if($article['category_name']): ?>
                            <a href="category.php?id=<?= $article['category_id'] ?>" class="inline-block px-3 py-1 text-xs font-medium rounded-full bg-primary-900 text-primary-300 mb-4">
                                <?= htmlspecialchars($article['category_name']) ?>
                            </a>
                        <?php endif; ?>
                        
                        <h1 class="text-3xl md:text-4xl font-serif font-bold text-white mb-4">
                            <?= htmlspecialchars($article['title']) ?>
                        </h1>
                        
                        <div class="flex items-center space-x-4 text-sm text-dark-400 mb-6">
                            <div class="flex items-center">
                                <img src="https://www.gravatar.com/avatar/<?= md5(strtolower(trim($article['username']))) ?>?s=80&d=mp" alt="Author" class="w-10 h-10 rounded-full mr-3">
                                <span><?= htmlspecialchars($article['username']) ?></span>
                            </div>
                            <span>•</span>
                            <time datetime="<?= date('c', strtotime($article['created_at'])) ?>"><?= formatDate($article['created_at']) ?></time>
                            <span>•</span>
                            <span><i class="fas fa-eye mr-1"></i> <?= $article['views'] ?> views</span>
                        </div>
                    </header>
                    
                    <!-- Featured Image -->
                    <?php if($article['image']): ?>
                        <div class="mb-8">
                            <img src="uploads/<?= htmlspecialchars($article['image']) ?>" alt="<?= htmlspecialchars($article['title']) ?>" class="w-full h-auto rounded-xl shadow-md">
                        </div>
                    <?php endif; ?>
                    
                    <!-- Article Content -->
                    <div class="article-content prose prose-lg max-w-none mb-8">
                        <?= $article['content'] ?>
                    </div>
                    
                    <!-- Article Footer -->
                    <footer class="border-t border-dark-700 pt-6 mt-8">
                        <div class="flex flex-wrap items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <span class="text-sm text-dark-400">Share this article:</span>
                                <div class="flex space-x-3">
                                    <a href="#" class="text-dark-400 hover:text-blue-500 transition-colors">
                                        <i class="fab fa-facebook-f"></i>
                                    </a>
                                    <a href="#" class="text-dark-400 hover:text-blue-400 transition-colors">
                                        <i class="fab fa-twitter"></i>
                                    </a>
                                    <a href="#" class="text-dark-400 hover:text-blue-600 transition-colors">
                                        <i class="fab fa-linkedin-in"></i>
                                    </a>
                                    <a href="#" class="text-dark-400 hover:text-red-500 transition-colors">
                                        <i class="fas fa-envelope"></i>
                                    </a>
                                </div>
                            </div>
                            
                            <?php if(isLoggedIn() && $_SESSION['user_id'] == $article['user_id']): ?>
                                <div class="flex space-x-3 mt-4 sm:mt-0">
                                    <a href="edit-article.php?id=<?= $article['id'] ?>" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 focus:ring-offset-dark-950 transition-colors">
                                        <i class="fas fa-edit mr-2"></i> Edit
                                    </a>
                                    <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-accent-600 hover:bg-accent-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent-500 focus:ring-offset-dark-950 transition-colors" data-modal-toggle="deleteModal">
                                        <i class="fas fa-trash-alt mr-2"></i> Delete
                                    </button>
                                </div>
                                
                                <!-- Delete Modal -->
                                <div id="deleteModal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 w-full md:inset-0 h-modal md:h-full justify-center items-center">
                                    <div class="relative p-4 w-full max-w-md h-full md:h-auto">
                                        <div class="relative bg-dark-800 rounded-lg shadow">
                                            <div class="flex justify-between items-center p-5 rounded-t border-b border-dark-700">
                                                <h3 class="text-xl font-medium text-white">
                                                    Confirm Deletion
                                                </h3>
                                                <button type="button" class="text-dark-400 bg-transparent hover:bg-dark-700 hover:text-white rounded-lg text-sm p-1.5 ml-auto inline-flex items-center" data-modal-toggle="deleteModal">
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                                </button>
                                            </div>
                                            <div class="p-6 text-center">
                                                <svg class="mx-auto mb-4 w-14 h-14 text-dark-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                <h3 class="mb-5 text-lg font-normal text-dark-300">
                                                    Are you sure you want to delete this article? This action cannot be undone.
                                                </h3>
                                                <form action="delete-article.php" method="POST" class="inline-block">
                                                    <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
                                                    <button type="submit" class="text-white bg-accent-600 hover:bg-accent-800 focus:ring-4 focus:outline-none focus:ring-accent-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
                                                        Yes, delete it
                                                    </button>
                                                </form>
                                                <button data-modal-toggle="deleteModal" type="button" class="text-dark-300 bg-dark-700 hover:bg-dark-600 focus:ring-4 focus:outline-none focus:ring-dark-600 rounded-lg border border-dark-600 text-sm font-medium px-5 py-2.5 hover:text-white focus:z-10">
                                                    Cancel
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </footer>
                </article>
            </div>
            
            <div class="lg:col-span-1">
                <!-- Author Info -->
                <div class="bg-dark-900 rounded-xl shadow-md p-6 mb-6">
                    <h3 class="text-lg font-serif font-semibold text-white mb-4">About the Author</h3>
                    <div class="flex items-center mb-4">
                        <img src="https://www.gravatar.com/avatar/<?= md5(strtolower(trim($article['username']))) ?>?s=80&d=mp" alt="Author" class="w-16 h-16 rounded-full mr-4">
                        <div>
                            <h4 class="text-base font-medium text-white"><?= htmlspecialchars($article['username']) ?></h4>
                            <?php
                            // Get article count for this author
                            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM articles WHERE user_id = ?");
                            $stmt->execute([$article['user_id']]);
                            $count = $stmt->fetch()['count'];
                            ?>
                            <p class="text-sm text-dark-400"><?= $count ?> articles</p>
                        </div>
                    </div>
                    <a href="author.php?id=<?= $article['user_id'] ?>" class="inline-flex items-center justify-center w-full px-4 py-2 border border-dark-700 shadow-sm text-sm font-medium rounded-md text-dark-200 bg-dark-800 hover:bg-dark-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 focus:ring-offset-dark-900 transition-colors">
                        View all articles
                    </a>
                </div>
                
                <!-- Related Articles -->
                <?php if(!empty($relatedArticles)): ?>
                    <div class="bg-dark-900 rounded-xl shadow-md p-6">
                        <h3 class="text-lg font-serif font-semibold text-white mb-4">Related Articles</h3>
                        <div class="space-y-4">
                            <?php foreach($relatedArticles as $relatedArticle): ?>
                                <article class="flex space-x-3">
                                    <div class="flex-shrink-0 w-16 h-16 rounded-md overflow-hidden">
                                        <?php if($relatedArticle['image']): ?>
                                            <img src="uploads/<?= htmlspecialchars($relatedArticle['image']) ?>" alt="<?= htmlspecialchars($relatedArticle['title']) ?>" class="w-full h-full object-cover">
                                        <?php else: ?>
                                            <img src="assets/images/placeholder.jpg" alt="Placeholder" class="w-full h-full object-cover">
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-medium text-white line-clamp-2">
                                            <a href="article.php?id=<?= $relatedArticle['id'] ?>" class="hover:text-primary-400 transition-colors">
                                                <?= htmlspecialchars($relatedArticle['title']) ?>
                                            </a>
                                        </h4>
                                        <div class="flex items-center mt-1">
                                            <span class="text-xs text-dark-400"><?= formatDate($relatedArticle['created_at']) ?></span>
                                        </div>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<!-- Modal Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modalToggles = document.querySelectorAll('[data-modal-toggle]');
        const modal = document.getElementById('deleteModal');
        
        if (modal) {
            modalToggles.forEach(function(toggle) {
                toggle.addEventListener('click', function() {
                    modal.classList.toggle('hidden');
                    modal.classList.toggle('flex');
                });
            });
        }
    });
</script>

<?php include 'includes/footer.php'; ?>

