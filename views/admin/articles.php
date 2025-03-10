<?php require_once 'views/partials/header.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-4xl font-bold text-primary">Manage Articles</h1>
        <a href="/admin/article/new" class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-md hover:bg-primary/90 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            <span>New Article</span>
        </a>
    </div>

    <?php if (isset($_GET['message'])): ?>
        <div class="bg-green-100 text-green-800 p-4 rounded-md mb-6">
            <?= escapeHtml($_GET['message']) ?>
        </div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="bg-red-100 text-red-800 p-4 rounded-md mb-6">
            <?= escapeHtml($error) ?>
        </div>
    <?php endif; ?>

    <div class="grid gap-6">
        <?php if (!empty($articles)): ?>
            <?php foreach ($articles as $article): ?>
                <div class="overflow-hidden rounded-lg border border-border/60 bg-white dark:bg-gray-800">
                    <div class="p-6 pb-3">
                        <div class="flex items-start justify-between">
                            <div>
                                <h2 class="text-xl font-bold"><?= escapeHtml($article['title']) ?></h2>
                                <div class="flex items-center gap-2 mt-2">
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-medium bg-primary text-white">
                                        <?= escapeHtml($article['category']) ?>
                                    </span>
                                    <span class="text-sm text-muted-foreground"><?= escapeHtml($article['date']) ?></span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="/admin/article/edit?id=<?= $article['id'] ?>" class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium border border-gray-300 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 dark:border-gray-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                    <span>Edit</span>
                                </a>
                                <form method="POST" action="/admin/articles" onsubmit="return confirm('Are you sure you want to delete this article?');">
                                    <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
                                    <button type="submit" name="delete_article" class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium bg-red-600 text-white rounded-md hover:bg-red-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                        <span>Delete</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 pt-3">
                        <p class="text-muted-foreground line-clamp-2"><?= escapeHtml($article['excerpt']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="text-center py-12">
                <h3 class="text-xl font-medium">No articles found</h3>
                <p class="text-muted-foreground mt-2">Create your first article to get started</p>
                <a href="/admin/article/new" class="inline-flex items-center gap-2 px-4 py-2 mt-4 bg-primary text-white rounded-md hover:bg-primary/90 transition-colors">
                    Create Article
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'views/partials/footer.php'; ?>

