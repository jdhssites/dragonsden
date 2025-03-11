<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
session_start();

// Get search query
$query = isset($_GET['q']) ? trim($_GET['q']) : '';

$articles = [];
$total_results = 0;

// Process search if query is provided
if(!empty($query)) {
    // Search articles
    $stmt = $pdo->prepare("SELECT articles.*, users.username, categories.name as category_name 
                          FROM articles 
                          JOIN users ON articles.user_id = users.id 
                          LEFT JOIN categories ON articles.category_id = categories.id
                          WHERE articles.title LIKE ? OR articles.content LIKE ? 
                          ORDER BY articles.created_at DESC");
    $search_term = "%$query%";
    $stmt->execute([$search_term, $search_term]);
    $articles = $stmt->fetchAll();
    
    $total_results = count($articles);
}
?>

<?php include 'includes/header.php'; ?>

<main class="flex-grow py-10 bg-dark-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-3xl font-serif font-bold text-white">Search Results</h1>
            <?php if(!empty($query)): ?>
                <p class="mt-2 text-dark-400">
                    <?= $total_results ?> result<?= $total_results != 1 ? 's' : '' ?> for "<?= htmlspecialchars($query) ?>"
                </p>
            <?php endif; ?>
        </div>
        
        <!-- Search Form -->
        <div class="bg-dark-900 rounded-xl shadow-md p-6 mb-8">
            <form action="search.php" method="GET" class="flex flex-col sm:flex-row gap-4">
                <div class="flex-grow">
                    <input type="search" name="q" value="<?= htmlspecialchars($query) ?>" placeholder="Search articles..." class="w-full rounded-md border border-dark-700 bg-dark-800 py-2 px-4 text-white placeholder-dark-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                </div>
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 focus:ring-offset-dark-900 transition-colors">
                    <i class="fas fa-search mr-2"></i> Search
                </button>
            </form>
        </div>
        
        <?php if(empty($query)): ?>
            <div class="bg-dark-900 rounded-xl p-8 text-center">
                <div class="text-5xl text-dark-700 mb-4">
                    <i class="fas fa-search"></i>
                </div>
                <h2 class="text-xl font-medium text-white mb-2">Enter a search term</h2>
                <p class="text-dark-400">Type in the search box above to find articles.</p>
            </div>
        <?php elseif(empty($articles)): ?>
            <div class="bg-dark-900 rounded-xl p-8 text-center">
                <div class="text-5xl text-dark-700 mb-4">
                    <i class="fas fa-search-minus"></i>
                </div>
                <h2 class="text-xl font-medium text-white mb-2">No results found</h2>
                <p class="text-dark-400">We couldn't find any articles matching "<?= htmlspecialchars($query) ?>".</p>
                <div class="mt-6">
                    <a href="index.php" class="inline-flex items-center px-4 py-2 border border-dark-700 shadow-sm text-sm font-medium rounded-md text-dark-300 bg-dark-800 hover:bg-dark-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 focus:ring-offset-dark-900 transition-colors">
                        <i class="fas fa-home mr-2"></i> Back to Home
                    </a>
                </div>
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
        <?php endif; ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

