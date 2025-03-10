<?php require_once 'views/partials/header.php'; ?>

<div class="container mx-auto px-4 py-12">
    <section class="mb-16">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-3xl font-bold text-primary">Featured Article</h2>
            <a href="/articles" class="flex items-center gap-2 text-primary hover:underline">
                <span>View all</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
            </a>
        </div>
        
        <?php if ($featuredArticle): ?>
            <!-- Featured Article -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 bg-secondary/30 rounded-lg overflow-hidden border">
                <div class="relative h-[300px] lg:h-full">
                    <img src="<?= escapeHtml($featuredArticle['image']) ?>" alt="<?= escapeHtml($featuredArticle['title']) ?>" class="object-cover w-full h-full" />
                </div>
                <div class="p-6 flex flex-col justify-center">
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-medium bg-primary text-white mb-4"><?= escapeHtml($featuredArticle['category']) ?></span>
                    <a href="/article/<?= escapeHtml($featuredArticle['slug']) ?>">
                        <h3 class="text-3xl font-bold mb-4 hover:text-primary transition-colors leading-tight">
                            <?= escapeHtml($featuredArticle['title']) ?>
                        </h3>
                    </a>
                    <p class="text-muted-foreground mb-6"><?= escapeHtml($featuredArticle['excerpt']) ?></p>
                    <div class="flex items-center gap-4 text-sm text-muted-foreground mb-6">
                        <div class="flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                            <span><?= escapeHtml($featuredArticle['date']) ?></span>
                        </div>
                        <div class="flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            <span><?= escapeHtml($featuredArticle['readTime']) ?> min read</span>
                        </div>
                    </div>
                    <a href="/article/<?= escapeHtml($featuredArticle['slug']) ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-md hover:bg-primary/90 transition-colors">
                        <span>Read Article</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="text-center py-12 bg-secondary/30 rounded-lg border">
                <h3 class="text-xl font-medium">No featured article available</h3>
                <p class="text-muted-foreground mt-2">Check back soon for new content</p>
            </div>
        <?php endif; ?>
    </section>

    <section>
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-3xl font-bold text-primary">Recent Articles</h2>
            <div class="flex items-center gap-2 text-muted-foreground">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                <span>Updated daily</span>
            </div>
        </div>
        
        <?php if (!empty($recentArticles)): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($recentArticles as $article): ?>
                    <?php require 'views/partials/article_card.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-12 bg-secondary/30 rounded-lg border">
                <h3 class="text-xl font-medium">No recent articles available</h3>
                <p class="text-muted-foreground mt-2">Check back soon for new content</p>
            </div>
        <?php endif; ?>
    </section>
</div>

<?php require_once 'views/partials/footer.php'; ?>

