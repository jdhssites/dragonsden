<?php require_once 'views/partials/header.php'; ?>

<div class="container mx-auto px-4 py-8">
    <a href="/articles" class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 mb-6">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
        Back to articles
    </a>

    <article class="max-w-3xl mx-auto">
        <span class="inline-block px-3 py-1 rounded-full text-xs font-medium bg-primary text-white mb-4">
            <?= escapeHtml($article['category']) ?>
        </span>
        <h1 class="text-4xl font-bold mb-4 leading-tight"><?= escapeHtml($article['title']) ?></h1>

        <div class="flex items-center gap-4 text-muted-foreground mb-8">
            <div class="flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                <span><?= escapeHtml($article['date']) ?></span>
            </div>
            <div class="flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                <span><?= escapeHtml($article['readTime']) ?> min read</span>
            </div>
            <div class="flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>
                <span><?= escapeHtml($article['category']) ?></span>
            </div>
        </div>

        <div class="relative w-full h-[400px] mb-8 rounded-lg overflow-hidden">
            <img src="<?= escapeHtml($article['image']) ?>" alt="<?= escapeHtml($article['title']) ?>" class="object-cover w-full h-full" />
        </div>

        <div class="prose prose-lg max-w-none prose-headings:font-serif prose-headings:font-bold prose-p:text-base prose-p:leading-relaxed">
            <?php foreach ($article['content'] as $paragraph): ?>
                <p class="mb-4"><?= escapeHtml($paragraph) ?></p>
            <?php endforeach; ?>
        </div>
    </article>

    <?php if (!empty($relatedArticles)): ?>
    <div class="max-w-3xl mx-auto mt-16">
        <h2 class="text-2xl font-bold mb-6">Related Articles</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <?php foreach ($relatedArticles as $article): ?>
                <div class="group">
                    <div class="relative h-40 mb-3 overflow-hidden rounded-md">
                        <img src="<?= escapeHtml($article['image']) ?>" alt="<?= escapeHtml($article['title']) ?>" class="object-cover w-full h-full transition-transform group-hover:scale-105" />
                    </div>
                    <a href="/article/<?= escapeHtml($article['slug']) ?>">
                        <h3 class="font-medium group-hover:text-primary transition-colors line-clamp-2"><?= escapeHtml($article['title']) ?></h3>
                    </a>
                    <div class="mt-2 flex items-center text-sm text-muted-foreground">
                        <span><?= escapeHtml($article['date']) ?></span>
                        <span class="mx-2">•</span>
                        <span><?= escapeHtml($article['readTime']) ?> min read</span>
                    </div>
                    <a href="/article/<?= escapeHtml($article['slug']) ?>" class="mt-2 inline-flex items-center gap-1 text-sm text-primary hover:underline">
                        Read more 
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php require_once 'views/partials/footer.php'; ?>

