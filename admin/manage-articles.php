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

// Process article deletion
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_article'])) {
    $article_id = (int)$_POST['article_id'];
    
    // Get article details to check if image needs to be deleted
    $stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
    $stmt->execute([$article_id]);
    $article = $stmt->fetch();
    
    if($article) {
        // Delete the article image if exists
        if($article['image'] && file_exists('../uploads/' . $article['image'])) {
            unlink('../uploads/' . $article['image']);
        }
        
        // Delete the article
        $stmt = $pdo->prepare("DELETE FROM articles WHERE id = ?");
        if($stmt->execute([$article_id])) {
            $success = 'Article deleted successfully.';
        } else {
            $errors[] = "Failed to delete article.";
        }
    } else {
        $errors[] = "Article not found.";
    }
}

// Get articles with pagination, filtering and sorting
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category_filter = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$sort_by = isset($_GET['sort']) ? trim($_GET['sort']) : 'newest';

// Build the query
$query = "SELECT articles.*, users.username, categories.name as category_name 
          FROM articles 
          JOIN users ON articles.user_id = users.id 
          LEFT JOIN categories ON articles.category_id = categories.id";
$count_query = "SELECT COUNT(*) as total FROM articles 
               JOIN users ON articles.user_id = users.id 
               LEFT JOIN categories ON articles.category_id = categories.id";
$params = [];
$where_clauses = [];

if (!empty($search)) {
    $where_clauses[] = "(articles.title LIKE ? OR articles.content LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($category_filter > 0) {
    $where_clauses[] = "articles.category_id = ?";
    $params[] = $category_filter;
}

if (!empty($where_clauses)) {
    $query .= " WHERE " . implode(' AND ', $where_clauses);
    $count_query .= " WHERE " . implode(' AND ', $where_clauses);
}

// Add ordering
switch ($sort_by) {
    case 'oldest':
        $query .= " ORDER BY articles.created_at ASC";
        break;
    case 'views':
        $query .= " ORDER BY articles.views DESC";
        break;
    case 'title':
        $query .= " ORDER BY articles.title ASC";
        break;
    case 'newest':
    default:
        $query .= " ORDER BY articles.created_at DESC";
        break;
}

// Add pagination
$query .= " LIMIT ? OFFSET ?";
$params[] = $per_page;
$params[] = $offset;

// Get total articles count
$stmt = $pdo->prepare($count_query);
$stmt->execute(empty($params) ? [] : array_slice($params, 0, count($params) - 2));
$total_articles = $stmt->fetch()['total'];
$total_pages = ceil($total_articles / $per_page);

// Get articles
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$articles = $stmt->fetchAll();

// Get categories for filter dropdown
$stmt = $pdo->prepare("SELECT * FROM categories ORDER BY name");
$stmt->execute();
$categories = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Articles - Dragon's Den Admin</title>

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
                    <a href="manage-articles.php" class="active-nav-link text-white group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                        <i class="fas fa-newspaper mr-3 text-primary-500"></i>
                        Article Management
                    </a>
                    <a href="manage-categories.php" class="text-dark-300 hover:bg-dark-800 hover:text-white group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                        <i class="fas fa-folder mr-3 text-dark-400 group-hover:text-white"></i>
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
                    <h1 class="text-2xl font-bold text-white">Manage Articles</h1>
                    <p class="mt-1 text-dark-400">Manage, edit and delete articles</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <a href="../create-article.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 focus:ring-offset-dark-950 transition-colors">
                        <i class="fas fa-plus mr-2"></i> Create New Article
                    </a>
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
            
            <!-- Search and Filter -->
            <div class="bg-dark-800 rounded-xl shadow-md overflow-hidden mb-6">
                <div class="p-6">
                    <form action="manage-articles.php" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="md:col-span-2">
                            <label for="search" class="block text-sm font-medium text-white mb-1">Search Articles</label>
                            <input type="text" id="search" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search by title or content" class="w-full rounded-md border-dark-700 bg-dark-700 py-2 px-3 text-white placeholder-dark-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                        </div>
                        
                        <div>
                            <label for="category" class="block text-sm font-medium text-white mb-1">Filter by Category</label>
                            <select id="category" name="category" class="w-full rounded-md border-dark-700 bg-dark-700 py-2 px-3 text-white focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                                <option value="0">All Categories</option>
                                <?php foreach($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>" <?= $category_filter === $category['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($category['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label for="sort" class="block text-sm font-medium text-white mb-1">Sort By</label>
                            <select id="sort" name="sort" class="w-full rounded-md border-dark-700 bg-dark-700 py-2 px-3 text-white focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                                <option value="newest" <?= $sort_by === 'newest' ? 'selected' : '' ?>>Newest First</option>
                                <option value="oldest" <?= $sort_by === 'oldest' ? 'selected' : '' ?>>Oldest First</option>
                                <option value="views" <?= $sort_by === 'views' ? 'selected' : '' ?>>Most Views</option>
                                <option value="title" <?= $sort_by === 'title' ? 'selected' : '' ?>>Title (A-Z)</option>
                            </select>
                        </div>
                        
                        <div class="md:col-span-4 flex gap-2">
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 focus:ring-offset-dark-950 transition-colors">
                                <i class="fas fa-search mr-2"></i> Apply Filters
                            </button>
                            
                            <a href="manage-articles.php" class="inline-flex items-center px-4 py-2 border border-dark-700 shadow-sm text-sm font-medium rounded-md text-dark-300 bg-dark-700 hover:bg-dark-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 focus:ring-offset-dark-950 transition-colors">
                                <i class="fas fa-times mr-2"></i> Clear Filters
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Articles Table -->
            <div class="bg-dark-800 rounded-xl shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-dark-700">
                        <thead class="bg-dark-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-dark-300 uppercase tracking-wider">Title</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-dark-300 uppercase tracking-wider">Author</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-dark-300 uppercase tracking-wider">Category</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-dark-300 uppercase tracking-wider">Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-dark-300 uppercase tracking-wider">Views</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-dark-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-dark-800 divide-y divide-dark-700">
                            <?php if(empty($articles)): ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-dark-300">
                                        No articles found. <?= !empty($search) || $category_filter > 0 ? 'Try adjusting your search filters.' : '' ?>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach($articles as $article): ?>
                                    <tr class="hover:bg-dark-700 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <?php if($article['image']): ?>
                                                        <img class="h-10 w-10 rounded object-cover" src="../uploads/<?= htmlspecialchars($article['image']) ?>" alt="<?= htmlspecialchars($article['title']) ?>">
                                                    <?php else: ?>
                                                        <div class="h-10 w-10 rounded bg-dark-700 flex items-center justify-center">
                                                            <i class="fas fa-file-alt text-dark-400"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-white line-clamp-1"><?= htmlspecialchars($article['title']) ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-white"><?= htmlspecialchars($article['username']) ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?php if($article['category_name']): ?>
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-medium rounded-full bg-dark-700 text-primary-400">
                                                    <?= htmlspecialchars($article['category_name']) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-dark-400">Uncategorized</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-dark-300">
                                            <?= formatDate($article['created_at']) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-dark-300">
                                            <?= number_format($article['views']) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <a href="../article.php?id=<?= $article['id'] ?>" class="text-dark-300 hover:text-white transition-colors" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="../edit-article.php?id=<?= $article['id'] ?>" class="text-primary-400 hover:text-primary-300 transition-colors" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="text-accent-500 hover:text-accent-400 transition-colors" data-modal-toggle="deleteModal<?= $article['id'] ?>" title="Delete">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                                
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
                                                                <svg class="mx-auto mb-4 w-14 h-14 text-accent-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                                <h3 class="mb-5 text-lg font-normal text-dark-300">
                                                                    Are you sure you want to delete the article "<?= htmlspecialchars($article['title']) ?>"?
                                                                </h3>
                                                                <p class="mb-5 text-sm text-dark-400">
                                                                    This will permanently delete the article and its associated image. This action cannot be undone.
                                                                </p>
                                                                <form action="manage-articles.php" method="POST">
                                                                    <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
                                                                    <button type="submit" name="delete_article" class="text-white bg-accent-600 hover:bg-accent-800 focus:ring-4 focus:outline-none focus:ring-accent-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
                                                                        Yes, delete article
                                                                    </button>
                                                                    <button type="button" data-modal-toggle="deleteModal<?= $article['id'] ?>" class="text-dark-300 bg-dark-700 hover:bg-dark-600 focus:ring-4 focus:outline-none focus:ring-dark-600 rounded-lg border border-dark-600 text-sm font-medium px-5 py-2.5 hover:text-white focus:z-10">
                                                                        Cancel
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Pagination -->
            <?php if($total_pages > 1): ?>
                <div class="mt-6 flex justify-center">
                    <nav class="inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        <?php if($page > 1): ?>
                            <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&category=<?= $category_filter ?>&sort=<?= $sort_by ?>" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-dark-700 bg-dark-800 text-sm font-medium text-dark-300 hover:bg-dark-700 hover:text-white">
                                <span class="sr-only">Previous</span>
                                <i class="fas fa-chevron-left h-5 w-5"></i>
                            </a>
                        <?php else: ?>
                            <span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-dark-700 bg-dark-900 text-sm font-medium text-dark-500 cursor-not-allowed">
                                <span class="sr-only">Previous</span>
                                <i class="fas fa-chevron-left h-5 w-5"></i>
                            </span>
                        <?php endif; ?>
                        
                        <?php for($i = 1; $i <= $total_pages; $i++): ?>
                            <?php if($i == $page): ?>
                                <span class="relative inline-flex items-center px-4 py-2 border border-primary-600 bg-primary-900 text-sm font-medium text-white">
                                    <?= $i ?>
                                </span>
                            <?php else: ?>
                                <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&category=<?= $category_filter ?>&sort=<?= $sort_by ?>" class="relative inline-flex items-center px-4 py-2 border border-dark-700 bg-dark-800 text-sm font-medium text-dark-300 hover:bg-dark-700 hover:text-white">
                                    <?= $i ?>
                                </a>
                            <?php endif; ?>
                        <?php endfor; ?>
                        
                        <?php if($page < $total_pages): ?>
                            <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&category=<?= $category_filter ?>&sort=<?= $sort_by ?>" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-dark-700 bg-dark-800 text-sm font-medium text-dark-300 hover:bg-dark-700 hover:text-white">
                                <span class="sr-only">Next</span>
                                <i class="fas fa-chevron-right h-5 w-5"></i>
                            </a>
                        <?php else: ?>
                            <span class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-dark-700 bg-dark-900 text-sm font-medium text-dark-500 cursor-not-allowed">
                                <span class="sr-only">Next</span>
                                <i class="fas fa-chevron-right h-5 w-5"></i>
                            </span>
                        <?php endif; ?>
                    </nav>
                </div>
            <?php endif; ?>
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

