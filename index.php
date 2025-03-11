<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
session_start();

// Get latest articles
$stmt = $pdo->prepare("SELECT articles.*, users.username 
                    FROM articles 
                    JOIN users ON articles.user_id = users.id 
                    ORDER BY created_at DESC 
                    LIMIT 12");
$stmt->execute();
$articles = $stmt->fetchAll();

// Get featured articles (top 4)
$featuredArticles = array_slice($articles, 0, 4);

// Get remaining articles for Latest News section
$latestArticles = array_slice($articles, 4);

// If we don't have enough articles for both sections, make sure Latest News has some
if (empty($latestArticles) && count($articles) > 0) {
    // Use all articles for Latest News if we don't have enough for both sections
    $latestArticles = $articles;
}

// Get categories
$stmt = $pdo->prepare("SELECT * FROM categories ORDER BY name");
$stmt->execute();
$categories = $stmt->fetchAll();

// Get popular articles
$popularArticles = getPopularArticles($pdo, 5);
?>

<?php include 'includes/header.php'; ?>

<main class="flex-grow">
    <!-- Featured Articles Grid -->
    <section class="bg-dark-900 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-serif font-bold text-white">Featured Stories</h2>
                <div class="hidden md:flex space-x-2">
                    <button id="prev-featured" class="p-2 rounded-full bg-dark-800 text-dark-400 hover:bg-dark-700 hover:text-white focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <button id="next-featured" class="p-2 rounded-full bg-dark-800 text-dark-400 hover:bg-dark-700 hover:text-white focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php if(!empty($featuredArticles)): ?>
                    <?php foreach($featuredArticles as $index => $article): ?>
                        <div class="relative overflow-hidden rounded-xl group">
                            <div class="aspect-w-16 aspect-h-9">
                                <?php if($article['image']): ?>
                                    <img src="uploads/<?= htmlspecialchars($article['image']) ?>" alt="<?= htmlspecialchars($article['title']) ?>" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                                <?php else: ?>
                                    <img src="assets/images/placeholder.jpg" alt="Placeholder" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                                <?php endif; ?>
                            </div>
                            <div class="absolute inset-0 bg-gradient-to-t from-dark-950 via-dark-950/80 to-transparent"></div>
                            
                            <?php
                            // Get category name
                            if($article['category_id']) {
                                $stmt = $pdo->prepare("SELECT name FROM categories WHERE id = ?");
                                $stmt->execute([$article['category_id']]);
                                $category = $stmt->fetch();
                                
                                if($category) {
                                    echo '<span class="absolute top-4 left-4 px-2 py-1 text-xs font-medium bg-primary-600 text-white rounded">' . htmlspecialchars($category['name']) . '</span>';
                                }
                            }
                            ?>
                            
                            <div class="absolute bottom-0 left-0 right-0 p-4">
                                <h3 class="text-lg font-bold text-white mb-2 line-clamp-2">
                                    <a href="article.php?id=<?= $article['id'] ?>" class="hover:text-primary-400 transition-colors">
                                        <?= htmlspecialchars($article['title']) ?>
                                    </a>
                                </h3>
                                <div class="flex items-center text-xs text-dark-300">
                                    <span><?= htmlspecialchars($article['username']) ?></span>
                                    <span class="mx-2">•</span>
                                    <span><?= formatDate($article['created_at']) ?></span>
                                </div>
                            </div>
                            <a href="article.php?id=<?= $article['id'] ?>" class="absolute inset-0" aria-label="<?= htmlspecialchars($article['title']) ?>"></a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>
    
    <!-- Main Content -->
    <section class="py-10 bg-dark-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Latest Articles -->
                <div class="lg:col-span-2">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-serif font-bold text-white">Latest News</h2>
                        <?php if(isLoggedIn()): ?>
                            <a href="create-article.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 focus:ring-offset-dark-950 transition-colors">
                                <i class="fas fa-plus mr-2"></i> Create Article
                            </a>
                        <?php endif; ?>
                    </div>
                    
                    <div class="space-y-8">
                        <?php if(!empty($latestArticles)): ?>
                            <?php foreach($latestArticles as $index => $article): ?>
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
                                                <?php
                                                // Get category name
                                                if($article['category_id']) {
                                                    $stmt = $pdo->prepare("SELECT name FROM categories WHERE id = ?");
                                                    $stmt->execute([$article['category_id']]);
                                                    $category = $stmt->fetch();
                                                    
                                                    if($category) {
                                                        echo '<a href="category.php?id=' . $article['category_id'] . '" class="inline-block px-2 py-1 text-xs font-medium bg-dark-800 text-primary-400 rounded mb-2">' . htmlspecialchars($category['name']) . '</a>';
                                                    }
                                                }
                                                ?>
                                                <h3 class="text-xl font-bold text-white mb-2">
                                                    <a href="article.php?id=<?= $article['id'] ?>" class="hover:text-primary-400 transition-colors">
                                                        <?= htmlspecialchars($article['title']) ?>
                                                    </a>
                                                </h3>
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
                        <?php else: ?>
                            <div class="bg-dark-900 rounded-xl p-6 text-center">
                                <p class="text-dark-300">No articles found. Be the first to publish!</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if(count($articles) > 10): ?>
                        <div class="mt-8 text-center">
                            <a href="all-articles.php" class="inline-flex items-center px-4 py-2 border border-dark-700 shadow-sm text-sm font-medium rounded-md text-dark-200 bg-dark-800 hover:bg-dark-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 focus:ring-offset-dark-950 transition-colors">
                                View All Articles
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <!-- Popular Articles -->
                    <div class="bg-dark-900 rounded-xl shadow-md p-6 mb-6">
                        <h3 class="text-xl font-serif font-bold text-white mb-4 pb-2 border-b border-dark-700">Trending Now</h3>
                        <div class="space-y-4">
                            <?php foreach($popularArticles as $index => $article): ?>
                                <article class="flex space-x-4">
                                    <div class="flex-shrink-0 w-16 h-16 rounded-md overflow-hidden">
                                        <?php if($article['image']): ?>
                                            <img src="uploads/<?= htmlspecialchars($article['image']) ?>" alt="<?= htmlspecialchars($article['title']) ?>" class="w-full h-full object-cover">
                                        <?php else: ?>
                                            <img src="assets/images/placeholder.jpg" alt="Placeholder" class="w-full h-full object-cover">
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-base font-medium text-white line-clamp-2">
                                            <a href="article.php?id=<?= $article['id'] ?>" class="hover:text-primary-400 transition-colors">
                                                <?= htmlspecialchars($article['title']) ?>
                                            </a>
                                        </h4>
                                        <div class="flex items-center mt-1 text-xs text-dark-400">
                                            <span><?= formatDate($article['created_at']) ?></span>
                                            <span class="mx-2">•</span>
                                            <span><i class="fas fa-eye mr-1"></i> <?= $article['views'] ?></span>
                                        </div>
                                    </div>
                                </article>
                                <?php if($index < count($popularArticles) - 1): ?>
                                    <div class="border-t border-dark-800"></div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Categories -->
                    <div class="bg-dark-900 rounded-xl shadow-md p-6 mb-6">
                        <h3 class="text-xl font-serif font-bold text-white mb-4 pb-2 border-b border-dark-700">Categories</h3>
                        <ul class="space-y-2">
                            <?php foreach($categories as $category): ?>
                                <li>
                                    <a href="category.php?id=<?= $category['id'] ?>" class="flex items-center justify-between p-2 rounded-lg hover:bg-dark-800 transition-colors group">
                                        <span class="text-dark-300 group-hover:text-white transition-colors"><?= htmlspecialchars($category['name']) ?></span>
                                        <?php
                                        // Get article count for this category
                                        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM articles WHERE category_id = ?");
                                        $stmt->execute([$category['id']]);
                                        $count = $stmt->fetch()['count'];
                                        ?>
                                        <span class="bg-dark-800 group-hover:bg-dark-700 text-dark-400 group-hover:text-dark-200 text-xs font-medium px-2.5 py-0.5 rounded-full transition-colors">
                                            <?= $count ?>
                                        </span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    
                    <!-- Newsletter -->
                    <div class="bg-primary-900 bg-opacity-50 rounded-xl shadow-md p-6 relative overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-br from-primary-600 to-primary-900 opacity-50"></div>
                        <div class="relative">
                            <h3 class="text-xl font-serif font-bold text-white mb-2">Subscribe to Our Newsletter</h3>
                            <p class="text-primary-100 mb-4">Get the latest news and updates delivered to your inbox.</p>
                            <form class="space-y-2">
                                <input type="email" placeholder="Your email address" class="w-full px-4 py-2 rounded-md bg-white bg-opacity-20 border border-primary-400 text-white placeholder-primary-200 focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent">
                                <button type="submit" class="w-full px-4 py-2 bg-white text-primary-700 font-medium rounded-md hover:bg-primary-50 transition-colors focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-primary-700">
                                    Subscribe
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <!-- User Actions -->
                    <?php if(!isLoggedIn()): ?>
                        <div class="bg-dark-900 rounded-xl shadow-md p-6 mt-6">
                            <h3 class="text-xl font-serif font-bold text-white mb-4">Join Our Community</h3>
                            <p class="text-dark-300 mb-4">Create an account to publish articles and join the conversation.</p>
                            <div class="space-y-2">
                                <a href="register.php" class="block w-full px-4 py-2 bg-primary-600 text-white text-center font-medium rounded-md hover:bg-primary-700 transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-dark-900">
                                    Register
                                </a>
                                <a href="login.php" class="block w-full px-4 py-2 bg-dark-800 text-dark-200 text-center font-medium rounded-md hover:bg-dark-700 hover:text-white transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-dark-900">
                                    Login
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Ad Banner -->
    <section class="py-8 bg-dark-900 border-t border-b border-dark-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gradient-to-r from-dark-800 to-dark-900 rounded-xl p-6 md:p-8 flex flex-col md:flex-row items-center justify-between">
                <div class="mb-4 md:mb-0 md:mr-8">
                    <p class="text-xs text-dark-400 uppercase tracking-wider mb-1">Advertisement</p>
                    <h3 class="text-xl md:text-2xl font-bold text-white mb-2">Premium Subscription</h3>
                    <p class="text-dark-300 mb-4 md:mb-0">Get unlimited access to exclusive content and features.</p>
                </div>
                <a href="#" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-dark-900 bg-primary-400 hover:bg-primary-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 focus:ring-offset-dark-900 transition-colors">
                    Learn More
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </a>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>

