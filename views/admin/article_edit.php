<?php require_once 'views/partials/header.php'; ?>

<div class="container mx-auto px-4 py-8">
    <a href="/admin/articles" class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 mb-6">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
        Back to articles
    </a>

    <div class="max-w-4xl mx-auto overflow-hidden rounded-lg border border-border/60 bg-white dark:bg-gray-800">
        <div class="p-6 border-b border-border/60">
            <h2 class="text-2xl font-bold">Edit Article</h2>
        </div>
        
        <form method="POST" action="/admin/article/edit?id=<?= $articleId ?>">
            <div class="p-6 space-y-6">
                <?php if (isset($error)): ?>
                    <div class="bg-red-100 text-red-800 p-4 rounded-md">
                        <?= escapeHtml($error) ?>
                    </div>
                <?php endif; ?>

                <div class="space-y-2">
                    <label for="title" class="block text-sm font-medium">Title</label>
                    <input type="text" id="title" name="title" value="<?= isset($_POST['title']) ? escapeHtml($_POST['title']) : escapeHtml($article['title']) ?>" required class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary dark:bg-gray-700 dark:border-gray-600">
                </div>

                <div class="space-y-2">
                    <label for="excerpt" class="block text-sm font-medium">Excerpt</label>
                    <textarea id="excerpt" name="excerpt" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary h-20 dark:bg-gray-700 dark:border-gray-600" required><?= isset($_POST['excerpt']) ? escapeHtml($_POST['excerpt']) : escapeHtml($article['excerpt']) ?></textarea>
                </div>

                <div class="space-y-2">
                    <label for="content" class="block text-sm font-medium">Content</label>
                    <textarea id="content" name="content" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary h-64 dark:bg-gray-700 dark:border-gray-600" required><?= isset($_POST['content']) ? escapeHtml($_POST['content']) : escapeHtml(implode("\n\n", $article['content'])) ?></textarea>
                </div>

                <div class="space-y-4">
                    <label class="block text-sm font-medium">Image</label>
                    <div class="relative w-full h-48 mb-2 rounded-md overflow-hidden border">
                        <img id="image-preview" src="<?= isset($_POST['image']) ? escapeHtml($_POST['image']) : escapeHtml($article['image']) ?>" alt="Article preview" class="object-cover w-full h-full">
                    </div>

                    <div class="grid grid-cols-3 gap-2 mb-4">
                        <?php foreach ($placeholderImages as $img): ?>
                            <button type="button" class="relative h-20 rounded-md overflow-hidden border <?= (isset($_POST['image']) && $_POST['image'] === $img) || (!isset($_POST['image']) && $article['image'] === $img) ? 'ring-2 ring-primary' : '' ?>" onclick="selectImage('<?= $img ?>')">
                                <img src="<?= escapeHtml($img) ?>" alt="Placeholder" class="object-cover w-full h-full">
                            </button>
                        <?php endforeach; ?>
                    </div>

                    <div class="space-y-2">
                        <label for="image" class="block text-sm font-medium">Custom Image URL</label>
                        <input type="text" id="image" name="image" value="<?= isset($_POST['image']) ? escapeHtml($_POST['image']) : escapeHtml($article['image']) ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary dark:bg-gray-700 dark:border-gray-600" oninput="updateImagePreview(this.value)">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="readTime" class="block text-sm font-medium">Read Time (minutes)</label>
                        <input type="number" id="readTime" name="readTime" min="1" value="<?= isset($_POST['readTime']) ? intval($_POST['readTime']) : intval($article['readTime']) ?>" required class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary dark:bg-gray-700 dark:border-gray-600">
                    </div>

                    <div class="space-y-2">
                        <label for="category" class="block text-sm font-medium">Category</label>
                        <input type="text" id="category" name="category" value="<?= isset($_POST['category']) ? escapeHtml($_POST['category']) : escapeHtml($article['category']) ?>" required class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary dark:bg-gray-700 dark:border-gray-600">
                    </div>
                </div>
            </div>
            
            <div class="p-6 border-t border-border/60 flex justify-end gap-2">
                <a href="/admin/articles" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium hover:bg-gray-100 dark:border-gray-600 dark:hover:bg-gray-700">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-md text-sm font-medium hover:bg-primary/90">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function updateImagePreview(url) {
        document.getElementById('image-preview').src = url || '/placeholder.svg?height=600&width=800';
    }
    
    function selectImage(url) {
        document.getElementById('image').value = url;
        updateImagePreview(url);
        
        // Update selected state
        const buttons = document.querySelectorAll('[onclick^="selectImage"]');
        buttons.forEach(button => {
            if (button.getAttribute('onclick') === `selectImage('${url}')`) {
                button.classList.add('ring-2', 'ring-primary');
            } else {
                button.classList.remove('ring-2', 'ring-primary');
            }
        });
    }
</script>

<?php require_once 'views/partials/footer.php'; ?>

