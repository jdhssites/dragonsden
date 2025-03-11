<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
session_start();

// Check if user is logged in and is admin
if(!isLoggedIn() || !isAdmin()) {
    setFlashMessage('error', 'You do not have permission to access this page.');
    header('Location: ../index.php');
    exit;
}

$errors = [];
$success = '';

// Process category creation
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_category'])) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    
    // Generate slug from name
    $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $name));
    
    // Validation
    if(empty($name)) {
        $errors[] = "Category name is required";
    } else {
        // Check if category name or slug already exists
        $stmt = $pdo->prepare("SELECT * FROM categories WHERE name = ? OR slug = ?");
        $stmt->execute([$name, $slug]);
        $existing_category = $stmt->fetch();
        
        if($existing_category) {
            $errors[] = "A category with this name already exists";
        }
    }
    
    // If no errors, create category
    if(empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO categories (name, slug, description, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
        
        if($stmt->execute([$name, $slug, $description])) {
            $success = 'Category created successfully!';
        } else {
            $errors[] = "Failed to create category. Please try again.";
        }
    }
}

// Process category update
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_category'])) {
    $category_id = (int)$_POST['category_id'];
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    
    // Generate slug from name
    $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $name));
    
    // Validation
    if(empty($name)) {
        $errors[] = "Category name is required";
    } else {
        // Check if category name or slug already exists (excluding current category)
        $stmt = $pdo->prepare("SELECT * FROM categories WHERE (name = ? OR slug = ?) AND id != ?");
        $stmt->execute([$name, $slug, $category_id]);
        $existing_category = $stmt->fetch();
        
        if($existing_category) {
            $errors[] = "A category with this name already exists";
        }
    }
    
    // If no errors, update category
    if(empty($errors)) {
        $stmt = $pdo->prepare("UPDATE categories SET name = ?, slug = ?, description = ?, updated_at = NOW() WHERE id = ?");
        
        if($stmt->execute([$name, $slug, $description, $category_id])) {
            $success = 'Category updated successfully!';
        } else {
            $errors[] = "Failed to update category. Please try again.";
        }
    }
}

// Process category deletion
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_category'])) {
    $category_id = (int)$_POST['category_id'];
    
    // Check if category has articles
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM articles WHERE category_id = ?");
    $stmt->execute([$category_id]);
    $article_count = $stmt->fetch()['count'];
    
    if($article_count > 0) {
        $errors[] = "This category has $article_count articles associated with it. Please reassign or delete these articles before deleting the category.";
    } else {
        // Delete the category
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        
        if($stmt->execute([$category_id])) {
            $success = 'Category deleted successfully!';
        } else {
            $errors[] = "Failed to delete category. Please try again.";
        }
    }
}

// Get all categories
$stmt = $pdo->prepare("SELECT categories.*, COUNT(articles.id) as article_count 
                    FROM categories 
                    LEFT JOIN articles ON categories.id = articles.category_id 
                    GROUP BY categories.id 
                    ORDER BY categories.name");
$stmt->execute();
$categories = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories - Dragon's Den Admin</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        dark: {
                            50: '#f8fafc',
                            100: '#f1f5f9',
                            200: '#e2e8f0',
                            300: '#cbd5e1',
                            400: '#94a3b8',
                            500: '#64748b',
                            600: '#475569',
                            700: '#334155',
                            800: '#1e293b',
                            900: '#0f172a',
                            950: '#020617',
                        },
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                            950: '#172554',
                        },
                        accent: {
                            50: '#fff1f2',
                            100: '#ffe4e6',
                            200: '#fecdd3',
                            300: '#fda4af',
                            400: '#fb7185',
                            500: '#f43f5e',
                            600: '#e11d48',
                            700: '#be123c',
                            800: '#9f1239',
                            900: '#881337',
                            950: '#4c0519',
                        },
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                        serif: ['Playfair Display', 'Georgia', 'serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                        display: ['Montserrat', 'sans-serif'],
                    },
                },
            },
        }
    </script>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&family=Montserrat:wght@600;700;800&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        .admin-sidebar {
            height: calc(100vh - 4rem);
            position: sticky;
            top: 4rem;
        }
        
        .active-nav-link {
            border-left: 4px solid #3b82f6;
            background-color: rgba(59, 130, 246, 0.1);
        }
    </style>
</head>
<body class="bg-dark-950 text-dark-100 min-h-screen flex flex-col">
    <!-- Admin Header -->
    <header class="bg-dark-800 border-b border-dark-700 sticky top-0 z-10">
        <div class="max-w-full mx-auto px-4 sm:px-6">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="index.php" class="flex-shrink-0 flex items-center">
                        <span class="font-display text-2xl font-bold text-white">Dragon's <span class="text-primary-500">Den</span></span>
                        <span class="ml-3 text-sm bg-accent-600 text-white px-2 py-1 rounded">Admin</span>
                    </a>
                </div>
                
                <div class="flex items-center">
                    <!-- User Menu -->
                    <div class="ml-3 relative" x-data="{ open: false }">
                        <div>
                            <button @click="open = !open" @click.away="open = false" type="button" class="flex text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 focus:ring-offset-dark-800" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                                <span class="sr-only">Open user menu</span>
                                <img class="h-8 w-8 rounded-full" src="https://www.gravatar.com/avatar/<?= md5(strtolower(trim($_SESSION['email'] ?? ''))) ?>?s=80&d=mp" alt="User avatar">
                            </button>
                        </div>
                        <div x-show="open" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-dark-800 ring-1 ring-black ring-opacity-5 focus:outline-none z-10" style="display: none;">
                            <div class="px-4 py-2 text-xs text-dark-400 border-b border-dark-700">
                                Signed in as <span class="font-medium text-white"><?= htmlspecialchars($_SESSION['username']) ?></span>
                            </div>
                            <a href="../profile.php" class="block px-4 py-2 text-sm text-dark-300 hover:bg-dark-700 hover:text-white">
                                Profile
                            </a>
                            <a href="../index.php" class="block px-4 py-2 text-sm text-dark-300 hover:bg-dark-700 hover:text-white">
                                Back to Site
                            </a>
                            <div class="border-t border-dark-700"></div>
                            <a href="../logout.php" class="block px-4 py-2 text-sm text-dark-300 hover:bg-dark-700 hover:text-white">
                                Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="flex flex-col md:flex-row">
        <!-- Sidebar Navigation -->
        <aside class="bg-dark-900 border-r border-dark-700 w-full md:w-64 flex-shrink-0 admin-sidebar overflow-y-auto">
            <nav class="mt-5 px-2">
                <div class="space-y-1">
                    <a href="index.php" class="text-dark-300 hover:bg-dark-800 hover:text-white group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                        <i class="fas fa-tachometer-alt mr-3 text-dark-400 group-hover:text-white"></i>
                        Dashboard
                    </a>
                    <a href="manage-users.php" class="text-dark-300 hover:bg-dark-800 hover:text-white group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                        <i class="fas fa-users-cog mr-3 text-dark-400 group-hover:text-white"></i>
                        User Management
                    </a>
                    <a href="manage-articles.php" class="text-dark-300 hover:bg-dark-800 hover:text-white group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                        <i class="fas fa-newspaper mr-3 text-dark-400 group-hover:text-white"></i>
                        Article Management
                    </a>
                    <a href="manage-categories.php" class="active-nav-link text-white group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                        <i class="fas fa-folder mr-3 text-primary-500"></i>
                        Categories
                    </a>
                    <a href="comments.php" class="text-dark-300 hover:bg-dark-800 hover:text-white group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                        <i class="fas fa-comments mr-3 text-dark-400 group-hover:text-white"></i>
                        Comments
                    </a>
                </div>
                
                <div class="pt-6 mt-6 border-t border-dark-700">
                    <h3 class="px-3 text-xs font-semibold text-dark-400 uppercase tracking-wider">Content</h3>
                    <div class="mt-3 space-y-1">
                        <a href="../create-article.php" class="text-dark-300 hover:bg-dark-800 hover:text-white group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                            <i class="fas fa-plus-circle mr-3 text-dark-400 group-hover:text-white"></i>
                            Create Article
                        </a>
                        <a href="media-library.php" class="text-dark-300 hover:bg-dark-800 hover:text-white group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                            <i class="fas fa-images mr-3 text-dark-400 group-hover:text-white"></i>
                            Media Library
                        </a>
                    </div>
                </div>
                
                <div class="pt-6 mt-6 border-t border-dark-700">
                    <h3 class="px-3 text-xs font-semibold text-dark-400 uppercase tracking-wider">System</h3>
                    <div class="mt-3 space-y-1">
                        <a href="site-settings.php" class="text-dark-300 hover:bg-dark-800 hover:text-white group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                            <i class="fas fa-cog mr-3 text-dark-400 group-hover:text-white"></i>
                            Site Settings
                        </a>
                        <a href="system-info.php" class="text-dark-300 hover:bg-dark-800 hover:text-white group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                            <i class="fas fa-info-circle mr-3 text-dark-400 group-hover:text-white"></i>
                            System Info
                        </a>
                    </div>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-6 overflow-hidden">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-white">Manage Categories</h1>
                    <p class="mt-1 text-dark-400">Create, edit and manage content categories</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <button type="button" data-modal-toggle="createCategoryModal" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 focus:ring-offset-dark-950 transition-colors">
                        <i class="fas fa-plus mr-2"></i> Create New Category
                    </button>
                </div>
            </div>
            
            <?php if(!empty($success)): ?>
                <div class="mb-6 bg-green-900 border border-green-700 text-green-100 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm"><?= $success ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if(!empty($errors)): ?>
                <div class="mb-6 bg-red-900 border border-red-700 text-red-100 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-100">There were errors with your request</h3>
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
            
            <!-- Categories Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php if(empty($categories)): ?>
                    <div class="col-span-3 bg-dark-800 rounded-xl p-8 text-center">
                        <p class="text-dark-300 mb-4">No categories found.</p>
                        <button type="button" data-modal-toggle="createCategoryModal" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 focus:ring-offset-dark-950 transition-colors">
                            <i class="fas fa-plus mr-2"></i> Create First Category
                        </button>
                    </div>
                <?php else: ?>
                    <?php foreach($categories as $category): ?>
                        <div class="bg-dark-800 rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                            <div class="p-6">
                                <div class="flex justify-between items-start">
                                    <h2 class="text-xl font-bold text-white"><?= htmlspecialchars($category['name']) ?></h2>
                                    <div class="flex space-x-2">
                                        <button type="button" data-modal-toggle="editCategoryModal<?= $category['id'] ?>" class="text-primary-400 hover:text-primary-300 transition-colors">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" data-modal-toggle="deleteCategoryModal<?= $category['id'] ?>" class="text-accent-500 hover:text-accent-400 transition-colors">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <p class="text-sm text-dark-400 mt-1">Slug: <?= htmlspecialchars($category['slug']) ?></p>
                                
                                <?php if($category['description']): ?>
                                    <p class="mt-3 text-dark-300 line-clamp-2"><?= htmlspecialchars($category['description']) ?></p>
                                <?php else: ?>
                                    <p class="mt-3 text-dark-500 italic">No description</p>
                                <?php endif; ?>
                                
                                <div class="mt-4 flex items-center justify-between">
                                    <span class="text-sm bg-dark-700 text-primary-400 px-2 py-1 rounded-full">
                                        <?= $category['article_count'] ?> article<?= $category['article_count'] != 1 ? 's' : '' ?>
                                    </span>
                                    <a href="../category.php?id=<?= $category['id'] ?>" class="text-xs text-primary-400 hover:text-primary-300 transition-colors">
                                        View category <i class="fas fa-arrow-right ml-1"></i>
                                    </a>
                                </div>
                            </div>
                            
                            <!-- Edit Category Modal -->
                            <div id="editCategoryModal<?= $category['id'] ?>" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 w-full md:inset-0 h-modal md:h-full justify-center items-center">
                                <div class="relative p-4 w-full max-w-md h-full md:h-auto">
                                    <div class="relative bg-dark-800 rounded-lg shadow">
                                        <div class="flex justify-between items-center p-5 rounded-t border-b border-dark-700">
                                            <h3 class="text-xl font-medium text-white">
                                                Edit Category
                                            </h3>
                                            <button type="button" class="text-dark-400 bg-transparent hover:bg-dark-700 hover:text-white rounded-lg text-sm p-1.5 ml-auto inline-flex items-center" data-modal-toggle="editCategoryModal<?= $category['id'] ?>">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                            </button>
                                        </div>
                                        <form action="manage-categories.php" method="POST">
                                            <input type="hidden" name="category_id" value="<?= $category['id'] ?>">
                                            <div class="p-6 space-y-6">
                                                <div>
                                                    <label for="name<?= $category['id'] ?>" class="block text-sm font-medium text-white">Category Name</label>
                                                    <input type="text" id="name<?= $category['id'] ?>" name="name" value="<?= htmlspecialchars($category['name']) ?>" required class="mt-1 block w-full rounded-md border-dark-700 bg-dark-700 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm text-white">
                                                </div>
                                                <div>
                                                    <label for="description<?= $category['id'] ?>" class="block text-sm font-medium text-white">Description (Optional)</label>
                                                    <textarea id="description<?= $category['id'] ?>" name="description" rows="3" class="mt-1 block w-full rounded-md border-dark-700 bg-dark-700 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm text-white"><?= htmlspecialchars($category['description']) ?></textarea>
                                                </div>
                                            </div>
                                            <div class="flex items-center p-6 space-x-2 rounded-b border-t border-dark-700">
                                                <button type="submit" name="update_category" class="text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Save Changes</button>
                                                <button type="button" data-modal-toggle="editCategoryModal<?= $category['id'] ?>" class="text-dark-300 bg-dark-700 hover:bg-dark-600 focus:ring-4 focus:outline-none focus:ring-dark-600 rounded-lg border border-dark-600 text-sm font-medium px-5 py-2.5 hover:text-white focus:z-10">Cancel</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Delete Category Modal -->
                            <div id="deleteCategoryModal<?= $category['id'] ?>" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 w-full md:inset-0 h-modal md:h-full justify-center items-center">
                                <div class="relative p-4 w-full max-w-md h-full md:h-auto">
                                    <div class="relative bg-dark-800 rounded-lg shadow">
                                        <div class="flex justify-between items-center p-5 rounded-t border-b border-dark-700">
                                            <h3 class="text-xl font-medium text-white">
                                                Confirm Deletion
                                            </h3>
                                            <button type="button" class="text-dark-400 bg-transparent hover:bg-dark-700 hover:text-white rounded-lg text-sm p-1.5 ml-auto inline-flex items-center" data-modal-toggle="deleteCategoryModal<?= $category['id'] ?>">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                            </button>
                                        </div>
                                        <div class="p-6 text-center">
                                            <svg class="mx-auto mb-4 w-14 h-14 text-accent-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <h3 class="mb-5 text-lg font-normal text-dark-300">
                                                Are you sure you want to delete the category "<?= htmlspecialchars($category['name']) ?>"?
                                            </h3>
                                            <?php if($category['article_count'] > 0): ?>
                                                <p class="mb-5 text-sm text-accent-400">
                                                    <i class="fas fa-exclamation-triangle mr-1"></i> This category has <?= $category['article_count'] ?> articles. These articles will become uncategorized if you delete this category.
                                                </p>
                                            <?php endif; ?>
                                            <form action="manage-categories.php" method="POST">
                                                <input type="hidden" name="category_id" value="<?= $category['id'] ?>">
                                                <button type="submit" name="delete_category" class="text-white bg-accent-600 hover:bg-accent-800 focus:ring-4 focus:outline-none focus:ring-accent-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
                                                    Yes, delete category
                                                </button>
                                                <button type="button" data-modal-toggle="deleteCategoryModal<?= $category['id'] ?>" class="text-dark-300 bg-dark-700 hover:bg-dark-600 focus:ring-4 focus:outline-none focus:ring-dark-600 rounded-lg border border-dark-600 text-sm font-medium px-5 py-2.5 hover:text-white focus:z-10">
                                                    Cancel
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <!-- Create Category Modal -->
            <div id="createCategoryModal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 w-full md:inset-0 h-modal md:h-full justify-center items-center">
                <div class="relative p-4 w-full max-w-md h-full md:h-auto">
                    <div class="relative bg-dark-800 rounded-lg shadow">
                        <div class="flex justify-between items-center p-5 rounded-t border-b border-dark-700">
                            <h3 class="text-xl font-medium text-white">
                                Create New Category
                            </h3>
                            <button type="button" class="text-dark-400 bg-transparent hover:bg-dark-700 hover:text-white rounded-lg text-sm p-1.5 ml-auto inline-flex items-center" data-modal-toggle="createCategoryModal">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                            </button>
                        </div>
                        <form action="manage-categories.php" method="POST">
                            <div class="p-6 space-y-6">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-white">Category Name</label>
                                    <input type="text" id="name" name="name" required class="mt-1 block w-full rounded-md border-dark-700 bg-dark-700 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm text-white">
                                    <p class="mt-1 text-xs text-dark-400">A slug will be automatically generated from the name.</p>
                                </div>
                                <div>
                                    <label for="description" class="block text-sm font-medium text-white">Description (Optional)</label>
                                    <textarea id="description" name="description" rows="3" class="mt-1 block w-full rounded-md border-dark-700 bg-dark-700 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm text-white"></textarea>
                                </div>
                            </div>
                            <div class="flex items-center p-6 space-x-2 rounded-b border-t border-dark-700">
                                <button type="submit" name="create_category" class="text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Create Category</button>
                                <button type="button" data-modal-toggle="createCategoryModal" class="text-dark-300 bg-dark-700 hover:bg-dark-600 focus:ring-4 focus:outline-none focus:ring-dark-600 rounded-lg border border-dark-600 text-sm font-medium px-5 py-2.5 hover:text-white focus:z-10">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    
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
</body>
</html>

