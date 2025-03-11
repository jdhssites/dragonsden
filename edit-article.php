<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
session_start();

// Check if user is logged in
if(!isLoggedIn()) {
    setFlashMessage('error', 'Please login to edit articles.');
    header('Location: login.php');
    exit;
}

// Get article ID from URL
$article_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if(!$article_id) {
    setFlashMessage('error', 'Invalid article ID.');
    header('Location: index.php');
    exit;
}

// Get article details
$stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
$stmt->execute([$article_id]);
$article = $stmt->fetch();

if(!$article) {
    setFlashMessage('error', 'Article not found.');
    header('Location: index.php');
    exit;
}

// Check if user is the author
if($article['user_id'] != $_SESSION['user_id']) {
    setFlashMessage('error', 'You can only edit your own articles.');
    header('Location: article.php?id=' . $article_id);
    exit;
}

// Get categories for dropdown
$stmt = $pdo->prepare("SELECT * FROM categories ORDER BY name");
$stmt->execute();
$categories = $stmt->fetchAll();

$errors = [];
$title = $article['title'];
$content = $article['content'];
$category_id = $article['category_id'];
$current_image = $article['image'];

// Process form submission
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate input
    $title = trim($_POST['title']);
    $content = $_POST['content'];
    $category_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
    
    // Validation
    if(empty($title)) {
        $errors[] = "Title is required";
    } elseif(strlen($title) < 5 || strlen($title) > 200) {
        $errors[] = "Title must be between 5 and 200 characters";
    }
    
    if(empty($content)) {
        $errors[] = "Content is required";
    }
    
    // Handle image upload
    $image_name = $current_image;
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 2 * 1024 * 1024; // 2MB
        
        if(!in_array($_FILES['image']['type'], $allowed_types)) {
            $errors[] = "Only JPG, PNG and GIF images are allowed";
        } elseif($_FILES['image']['size'] > $max_size) {
            $errors[] = "Image size should be less than 2MB";
        } else {
            $image_name = time() . '_' . $_FILES['image']['name'];
            $upload_dir = 'uploads/';
            
            // Create directory if it doesn't exist
            if(!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            if(!move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image_name)) {
                $errors[] = "Failed to upload image";
                $image_name = $current_image;
            } else {
                // Delete old image if exists
                if($current_image && file_exists('uploads/' . $current_image)) {
                    unlink('uploads/' . $current_image);
                }
            }
        }
    }
    
    // Handle image deletion
    if(isset($_POST['delete_image']) && $_POST['delete_image'] == 1) {
        if($current_image && file_exists('uploads/' . $current_image)) {
            unlink('uploads/' . $current_image);
        }
        $image_name = null;
    }
    
    // If no errors, update article
    if(empty($errors)) {
        $stmt = $pdo->prepare("UPDATE articles SET title = ?, content = ?, category_id = ?, image = ?, updated_at = NOW() WHERE id = ?");
        
        if($stmt->execute([$title, $content, $category_id ?: null, $image_name, $article_id])) {
            setFlashMessage('success', 'Article updated successfully!');
            header('Location: article.php?id=' . $article_id);
            exit;
        } else {
            $errors[] = "Failed to update article. Please try again.";
            
            // Delete newly uploaded image if article update failed
            if($image_name != $current_image && $image_name && file_exists('uploads/' . $image_name)) {
                unlink('uploads/' . $image_name);
            }
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<main class="flex-grow py-10 bg-dark-950">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-dark-900 rounded-xl shadow-md overflow-hidden">
            <div class="px-6 py-8 sm:p-10">
                <div class="mb-8">
                    <h1 class="text-2xl font-bold text-white">Edit Article</h1>
                    <p class="mt-2 text-sm text-dark-400">
                        Update your article content and settings
                    </p>
                </div>
                
                <?php if(!empty($errors)): ?>
                    <div class="mb-6 bg-red-900 border border-red-700 text-red-100 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-red-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-100">There were errors with your submission</h3>
                                <div class="mt-2 text-sm text-red-200">
                                    <ul class="list-disc pl-5 space-y-1">
                                        <?php foreach($errors as $error): ?>
                                            <li><?= $error ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <style>
                    /* Fix text color in form inputs */
                    .dark input[type="text"],
                    .dark input[type="email"],
                    .dark input[type="password"],
                    .dark textarea,
                    .dark select {
                        color: #1e293b; /* Dark text color */
                        background-color: #f8fafc; /* Light background */
                    }
                    
                    .dark input[type="text"]::placeholder,
                    .dark input[type="email"]::placeholder,
                    .dark input[type="password"]::placeholder,
                    .dark textarea::placeholder,
                    .dark select::placeholder {
                        color: #64748b; /* Placeholder color */
                    }
                </style>

                <form action="edit-article.php?id=<?= $article_id ?>" method="POST" enctype="multipart/form-data" class="space-y-6 dark">
                    <div>
                        <label for="title" class="block text-sm font-medium text-white">Title</label>
                        <div class="mt-1">
                            <input type="text" id="title" name="title" value="<?= htmlspecialchars($title) ?>" required class="appearance-none block w-full px-3 py-2 border border-dark-700 rounded-md shadow-sm placeholder-dark-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                        </div>
                    </div>
                    
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-white">Category (Optional)</label>
                        <div class="mt-1">
                            <select id="category_id" name="category_id" class="appearance-none block w-full px-3 py-2 border border-dark-700 rounded-md shadow-sm text-dark-900 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                                <option value="">Select Category</option>
                                <?php foreach($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>" <?= $category_id == $category['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($category['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div>
                        <?php if($current_image): ?>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-white mb-2">Current Image</label>
                                <div class="relative">
                                    <img src="uploads/<?= htmlspecialchars($current_image) ?>" alt="Current Image" class="max-h-48 rounded-md">
                                    <div class="mt-2 flex items-center">
                                        <input id="delete_image" name="delete_image" type="checkbox" value="1" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-dark-700 rounded">
                                        <label for="delete_image" class="ml-2 block text-sm text-dark-300">
                                            Delete current image
                                        </label>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <label for="image" class="block text-sm font-medium text-white">New Featured Image (Optional)</label>
                        <div class="mt-1 flex items-center">
                            <div class="w-full">
                                <label class="flex justify-center px-6 pt-5 pb-6 border-2 border-dark-700 border-dashed rounded-md cursor-pointer hover:bg-dark-800">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-dark-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-dark-400">
                                            <span class="relative bg-dark-900 rounded-md font-medium text-primary-400 hover:text-primary-300 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary-500">
                                                Upload a file
                                            </span>
                                            <input id="image" name="image" type="file" class="sr-only" accept="image/jpeg,image/png,image/gif">
                                            <p class="pl-1">or drag and drop</p>
                                        </div>
                                        <p class="text-xs text-dark-400">
                                            PNG, JPG, GIF up to 2MB
                                        </p>
                                    </div>
                                </label>
                            </div>
                            <img id="image-preview" class="ml-4 h-32 w-32 object-cover rounded-md hidden" src="#" alt="Image preview">
                        </div>
                    </div>
                    
                    <div>
                        <label for="content" class="block text-sm font-medium text-white">Content</label>
                        <div class="mt-1">
                            <textarea id="content" name="content" rows="15" required class="appearance-none block w-full px-3 py-2 border border-dark-700 rounded-md shadow-sm placeholder-dark-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"><?= htmlspecialchars($content) ?></textarea>
                        </div>
                    </div>
                    
                    <div class="flex justify-between">
                        <a href="article.php?id=<?= $article_id ?>" class="inline-flex items-center px-4 py-2 border border-dark-700 shadow-sm text-sm font-medium rounded-md text-dark-300 bg-dark-800 hover:bg-dark-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 focus:ring-offset-dark-900 transition-colors">
                            Cancel
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 focus:ring-offset-dark-900 transition-colors">
                            Update Article
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Image preview for upload
        const imageInput = document.getElementById('image');
        const imagePreview = document.getElementById('image-preview');
        
        if (imageInput && imagePreview) {
            imageInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                        imagePreview.classList.remove('hidden');
                    };
                    
                    reader.readAsDataURL(this.files[0]);
                }
            });
        }
    });
</script>

<?php include 'includes/footer.php'; ?>

