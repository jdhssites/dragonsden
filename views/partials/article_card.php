<a href="/article/<?= escapeHtml($article['slug']) ?>" class="block h-full">
    <div class="overflow-hidden h-full flex flex-col transition-all hover:shadow-md border border-border/60 rounded-lg">
        <div class="relative h-48 w-full">
            <img src="<?= escapeHtml($article['image']) ?>" alt="<?= escapeHtml($article['title']) ?>" class="object-cover w-full h-full" />
            <span class="absolute top-3 left-3 inline-block px-3 py-1 rounded-full text-xs font-medium bg-primary text-white">
                <?= escapeHtml($article['category']) ?>
            </span>
        </div>
        <div class="p-6 flex-1">
            <h3 class="text-xl font-bold mb-2 hover:text-primary transition-colors leading-tight">
                <?= escapeHtml($article['title']) ?>
            </h3>
            <p class="text-muted-foreground line-clamp-3">
                <?= escapeHtml($article['excerpt']) ?>
            </p>
        </div>
        <div class="px-6 pb-6 text-sm text-muted-foreground flex justify-between border-t pt-4">
            <div class="flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                <span><?= escapeHtml($article['date']) ?></span>
            </div>
            <div class="flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                <span><?= escapeHtml($article['readTime']) ?> min read</span>
            </div>
        </div>
    </div>
</a>

