<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
session_start();

// Check if user is logged in
if(!isLoggedIn()) {
    $_SESSION['redirect_after_login'] = 'my-articles.php';
    setFlashMessage('error', 'Please login to view your articles.');
    header('Location: login.php');
    exit;
}

// Get user's articles
$stmt = $pdo->prepare("SELECT articles.*, categories.name as category_name 
                      FROM articles 
                      LEFT JOIN categories ON articles.category_id = categories.id
                      WHERE articles.user_id = ? 
                      ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$articles = $stmt->fetchAll();
?>

<?php include 'includes/header.php'; ?>

<main class="flex-grow py-10 bg-dark-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
            <h1 class="text-3xl font-serif font-bold text-white mb-4 md:mb-0">My Articles</h1>
            <a href="create-article.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 focus:ring-offset-dark-950 transition-colors">
                <i class="fas fa-plus mr-2"></i> Create New Article
            </a>
        </div>
        
        <?php if(empty($articles)): ?>
            <div class="bg-dark-900 rounded-xl p-8 text-center">
                <p class="text-dark-300 mb-4">You haven't published any articles yet.</p>
                <a href="create-article.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 focus:ring-offset-dark-950 transition-colors">
                    Create Your First Article
                </a>
            </div>
        <?php else: ?>
            <div class="bg-dark-900 rounded-xl shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-dark-700">
                        <thead class="bg-dark-800">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-dark-300 uppercase tracking-wider">Title</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-dark-300 uppercase tracking-wider">Category</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-dark-300 uppercase tracking-wider">Published</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-dark-300 uppercase tracking-wider">Views</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-dark-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-dark-900 divide-y divide-dark-800">
                            <?php foreach($articles as $article): ?>
                                <tr class="hover:bg-dark-800 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="article.php?id=<?= $article['id'] ?>" class="text-white hover:text-primary-400 transition-colors font-medium">
                                            <?= htmlspecialchars($article['title']) ?>
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if($article['category_name']): ?>
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-medium rounded-full bg-dark-800 text-primary-400">
                                                <?= htmlspecialchars($article['category_name']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-dark-400">Uncategorized</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-dark-300">
                                        <?= formatDate($article['created_at']) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-dark-300">
                                        <?= $article['views'] ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="article.php?id=<?= $article['id'] ?>" class="text-primary-400 hover:text-primary-300 transition-colors">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="edit-article.php?id=<?= $article['id'] ?>" class="text-dark-300 hover:text-white transition-colors">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="text-accent-500 hover:text-accent-400 transition-colors" 
                                                    data-modal-toggle="deleteModal<?= $article['id'] ?>">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                        
                                        <!-- Delete Modal -->
                                        <div id="deleteModal<?= $article['id'] ?>" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 w-full md:inset-0 h-modal md:h-full justify-center items-center">
                                            <div class="relative p-4 w-full max-w-md h-full md:h-auto">
                                                <div class="relative bg-dark-800 rounded-lg shadow">
                                                    <div class="flex justify-between items-center p-5 rounded-t border-b border-dark-700">
                                                        <h3 class="text-xl font-medium text-white">
                                                            Confirm Deletion
                                                        </h3>
                                                        <button type="button" class="text-dark-400 bg-transparent hover:bg-dark-700 hover:text-white rounded-lg text-sm p-1.5 ml-auto inline-flex items-center" data-modal-toggle="deleteModal<?= $article['id'] ?>">
                                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                                        </button>
                                                    </div>
                                                    <div class="p-6 text-center">
                                                        <svg class="mx-auto mb-4 w-14 h-14 text-dark-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                        <h3 class="mb-5 text-lg font-normal text-dark-300">
                                                            Are you sure you want to delete the article "<?= htmlspecialchars($article['title']) ?>"?
                                                            <br>This action cannot be undone.
                                                        </h3>
                                                        <form action="delete-article.php" method="POST" class="inline-block">
                                                            <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
                                                            <button type="submit" class="text-white bg-accent-600 hover:bg-accent-800 focus:ring-4 focus:outline-none focus:ring-accent-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
                                                                Yes, delete it
                                                            </button>
                                                        </form>
                                                        <button data-modal-toggle="deleteModal<?= $article['id'] ?>" type="button" class="text-dark-300 bg-dark-700 hover:bg-dark-600 focus:ring-4 focus:outline-none focus:ring-dark-600 rounded-lg border border-dark-600 text-sm font-medium px-5 py-2.5 hover:text-white focus:z-10">
                                                            Cancel
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<!-- Modal Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modalToggles = document.querySelectorAll('[data-modal-toggle]');
        
        modalToggles.forEach(function(toggle) {
            const modalId = toggle.getAttribute('data-modal-toggle');
            const modal = document.getElementById(modalId);
            
            if (modal) {
                toggle.addEventListener('click', function() {
                    modal.classList.toggle('hidden');
                    modal.classList.toggle('flex');
                });
            }
        });
    });
</script>

<?php include 'includes/footer.php'; ?>

