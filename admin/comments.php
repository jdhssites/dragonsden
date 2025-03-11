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

// Process comment approval/rejection
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approve_comment'])) {
    $comment_id = (int)$_POST['comment_id'];
    
    $stmt = $pdo->prepare("UPDATE comments SET status = 'approved' WHERE id = ?");
    
    if($stmt->execute([$comment_id])) {
        $success = 'Comment approved successfully!';
    } else {
        $errors[] = "Failed to approve comment. Please try again.";
    }
}

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reject_comment'])) {
    $comment_id = (int)$_POST['comment_id'];
    
    $stmt = $pdo->prepare("UPDATE comments SET status = 'rejected' WHERE id = ?");
    
    if($stmt->execute([$comment_id])) {
        $success = 'Comment rejected successfully!';
    } else {
        $errors[] = "Failed to reject comment. Please try again.";
    }
}

// Process comment deletion
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_comment'])) {
    $comment_id = (int)$_POST['comment_id'];
    
    $stmt = $pdo->prepare("DELETE FROM comments WHERE id = ?");
    
    if($stmt->execute([$comment_id])) {
        $success = 'Comment deleted successfully!';
    } else {
        $errors[] = "Failed to delete comment. Please try again.";
    }
}

// Get filter parameters
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$article_filter = isset($_GET['article']) ? (int)$_GET['article'] : 0;
$user_filter = isset($_GET['user']) ? (int)$_GET['user'] : 0;

// Build query based on filters
$query = "
    SELECT c.*, a.title as article_title, u.username as username
    FROM comments c
    JOIN articles a ON c.article_id = a.id
    JOIN users u ON c.user_id = u.id
    WHERE 1=1
";

$params = [];

if($status_filter !== 'all') {
    $query .= " AND c.status = ?";
    $params[] = $status_filter;
}

if($article_filter > 0) {
    $query .= " AND c.article_id = ?";
    $params[] = $article_filter;
}

if($user_filter > 0) {
    $query .= " AND c.user_id = ?";
    $params[] = $user_filter;
}

$query .= " ORDER BY c.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$comments = $stmt->fetchAll();

// Get articles for filter dropdown
$stmt = $pdo->query("SELECT id, title FROM articles ORDER BY title");
$articles = $stmt->fetchAll();

// Get users for filter dropdown
$stmt = $pdo->query("SELECT id, username FROM users ORDER BY username");
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Comments - Dragon's Den Admin</title>

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
                    <a href="dashboard.php" class="text-dark-300 hover:bg-dark-800 hover:text-white group flex items-center px-3 py-2 text-sm font-medium rounded-md">
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
                    <a href="manage-categories.php" class="text-dark-300 hover:bg-dark-800 hover:text-white group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                        <i class="fas fa-folder mr-3 text-dark-400 group-hover:text-white"></i>
                        Categories
                    </a>
                    <a href="comments.php" class="active-nav-link text-white group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                        <i class="fas fa-comments mr-3 text-primary-500"></i>
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
                    <h1 class="text-2xl font-bold text-white">Manage Comments</h1>
                    <p class="mt-1 text-dark-400">Review, approve, and moderate user comments</p>
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
            
            <!-- Filters -->
            <div class="bg-dark-800 rounded-xl p-4 mb-6">
                <form action="comments.php" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="status" class="block text-sm font-medium text-white mb-1">Status</label>
                        <select id="status" name="status" class="w-full rounded-md border-dark-700 bg-dark-700 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm text-white">
                            <option value="all" <?= $status_filter === 'all' ? 'selected' : '' ?>>All Comments</option>
                            <option value="pending" <?= $status_filter === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="approved" <?= $status_filter === 'approved' ? 'selected' : '' ?>>Approved</option>
                            <option value="rejected" <?= $status_filter === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="article" class="block text-sm font-medium text-white mb-1">Article</label>
                        <select id="article" name="article" class="w-full rounded-md border-dark-700 bg-dark-700 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm text-white">
                            <option value="0">All Articles</option>
                            <?php foreach($articles as $article): ?>
                                <option value="<?= $article['id'] ?>" <?= $article_filter === $article['id'] ? 'selected' : '' ?>><?= htmlspecialchars($article['title']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label for="user" class="block text-sm font-medium text-white mb-1">User</label>
                        <select id="user" name="user" class="w-full rounded-md border-dark-700 bg-dark-700 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm text-white">
                            <option value="0">All Users</option>
                            <?php foreach($users as $user): ?>
                                <option value="<?= $user['id'] ?>" <?= $user_filter === $user['id'] ? 'selected' : '' ?>><?= htmlspecialchars($user['username']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="md:col-span-3 flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 focus:ring-offset-dark-950 transition-colors">
                            <i class="fas fa-filter mr-2"></i> Apply Filters
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Comments Table -->
            <div class="bg-dark-800 rounded-xl shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <?php if(empty($comments)): ?>
                        <div class="p-8 text-center">
                            <p class="text-dark-300 mb-4">No comments found matching your filters.</p>
                        </div>
                    <?php else: ?>
                        <table class="min-w-full divide-y divide-dark-700">
                            <thead class="bg-dark-900">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-dark-400 uppercase tracking-wider">Comment</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-dark-400 uppercase tracking-wider">Article</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-dark-400 uppercase tracking-wider">User</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-dark-400 uppercase tracking-wider">Date</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-dark-400 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-dark-400 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-dark-800 divide-y divide-dark-700">
                                <?php foreach($comments as $comment): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-normal">
                                            <div class="text-sm text-white max-w-md break-words">
                                                <?= htmlspecialchars(substr($comment['content'], 0, 100)) ?><?= strlen($comment['content']) > 100 ? '...' : '' ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-normal">
                                            <div class="text-sm text-white">
                                                <a href="../article.php?id=<?= $comment['article_id'] ?>" class="hover:text-primary-400 transition-colors">
                                                    <?= htmlspecialchars(substr($comment['article_title'], 0, 30)) ?><?= strlen($comment['article_title']) > 30 ? '...' : '' ?>
                                                </a>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-white">
                                                <a href="../author.php?id=<?= $comment['user_id'] ?>" class="hover:text-primary-400 transition-colors">
                                                    <?= htmlspecialchars($comment['username']) ?>
                                                </a>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-dark-300">
                                                <?= date('M j, Y g:i a', strtotime($comment['created_at'])) ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?php if($comment['status'] === 'approved'): ?>
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-900 text-green-200">
                                                    Approved
                                                </span>
                                            <?php elseif($comment['status'] === 'rejected'): ?>
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-900 text-red-200">
                                                    Rejected
                                                </span>
                                            <?php else: ?>
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-900 text-yellow-200">
                                                    Pending
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end space-x-2">
                                                <button type="button" data-modal-toggle="viewCommentModal<?= $comment['id'] ?>" class="text-primary-400 hover:text-primary-300 transition-colors">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                
                                                <?php if($comment['status'] !== 'approved'): ?>
                                                    <form action="comments.php" method="POST" class="inline">
                                                        <input type="hidden" name="comment_id" value="<?= $comment['id'] ?>">
                                                        <button type="submit" name="approve_comment" class="text-green-400 hover:text-green-300 transition-colors" title="Approve">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                                
                                                <?php if($comment['status'] !== 'rejected'): ?>
                                                    <form action="comments.php" method="POST" class="inline">
                                                        <input type="hidden" name="comment_id" value="<?= $comment['id'] ?>">
                                                        <button type="submit" name="reject_comment" class="text-yellow-400 hover:text-yellow-300 transition-colors" title="Reject">
                                                            <i class="fas fa-ban"></i>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                                
                                                <button type="button" data-modal-toggle="deleteCommentModal<?= $comment['id'] ?>" class="text-accent-500 hover:text-accent-400 transition-colors">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                            
                                            <!-- View Comment Modal -->
                                            <div id="viewCommentModal<?= $comment['id'] ?>" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 w-full md:inset-0 h-modal md:h-full justify-center items-center">
                                                <div class="relative p-4 w-full max-w-2xl h-full md:h-auto">
                                                    <div class="relative bg-dark-800 rounded-lg shadow">
                                                        <div class="flex justify-between items-center p-5 rounded-t border-b border-dark-700">
                                                            <h3 class="text-xl font-medium text-white">
                                                                Comment Details
                                                            </h3>
                                                            <button type="button" class="text-dark-400 bg-transparent hover:bg-dark-700 hover:text-white rounded-lg text-sm p-1.5 ml-auto inline-flex items-center" data-modal-toggle="viewCommentModal<?= $comment['id'] ?>">
                                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                                            </button>
                                                        </div>
                                                        <div class="p-6 space-y-6">
                                                            <div>
                                                                <h4 class="text-sm font-medium text-dark-400">Comment Content</h4>
                                                                <p class="mt-1 text-white"><?= nl2br(htmlspecialchars($comment['content'])) ?></p>
                                                            </div>
                                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                                <div>
                                                                    <h4 class="text-sm font-medium text-dark-400">Article</h4>
                                                                    <p class="mt-1 text-white">
                                                                        <a href="../article.php?id=<?= $comment['article_id'] ?>" class="hover:text-primary-400 transition-colors">
                                                                            <?= htmlspecialchars($comment['article_title']) ?>
                                                                        </a>
                                                                    </p>
                                                                </div>
                                                                <div>
                                                                    <h4 class="text-sm font-medium text-dark-400">User</h4>
                                                                    <p class="mt-1 text-white">
                                                                        <a href="../author.php?id=<?= $comment['user_id'] ?>" class="hover:text-primary-400 transition-colors">
                                                                            <?= htmlspecialchars($comment['username']) ?>
                                                                        </a>
                                                                    </p>
                                                                </div>
                                                                <div>
                                                                    <h4 class="text-sm font-medium text-dark-400">Date</h4>
                                                                    <p class="mt-1 text-white"><?= date('F j, Y g:i a', strtotime($comment['created_at'])) ?></p>
                                                                </div>
                                                                <div>
                                                                    <h4 class="text-sm font-medium text-dark-400">Status</h4>
                                                                    <p class="mt-1">
                                                                        <?php if($comment['status'] === 'approved'): ?>
                                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-900 text-green-200">
                                                                                Approved
                                                                            </span>
                                                                        <?php elseif($comment['status'] === 'rejected'): ?>
                                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-900 text-red-200">
                                                                                Rejected
                                                                            </span>
                                                                        <?php else: ?>
                                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-900 text-yellow-200">
                                                                                Pending
                                                                            </span>
                                                                        <?php endif; ?>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="flex items-center p-6 space-x-2 rounded-b border-t border-dark-700">
                                                            <?php if($comment['status'] !== 'approved'): ?>
                                                                <form action="comments.php" method="POST">
                                                                    <input type="hidden" name="comment_id" value="<?= $comment['id'] ?>">
                                                                    <button type="submit" name="approve_comment" class="text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Approve</button>
                                                                </form>
                                                            <?php endif; ?>
                                                            
                                                            <?php if($comment['status'] !== 'rejected'): ?>
                                                                <form action="comments.php" method="POST">
                                                                    <input type="hidden" name="comment_id" value="<?= $comment['id'] ?>">
                                                                    <button type="submit" name="reject_comment" class="text-white bg-yellow-600 hover:bg-yellow-700 focus:ring-4 focus:outline-none focus:ring-yellow-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Reject</button>
                                                                </form>
                                                            <?php endif; ?>
                                                            
                                                            <form action="comments.php" method="POST">
                                                                <input type="hidden" name="comment_id" value="<?= $comment['id'] ?>">
                                                                <button type="submit" name="delete_comment" class="text-white bg-accent-600 hover:bg-accent-700 focus:ring-4 focus:outline-none focus:ring-accent-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Delete</button>
                                                            </form>
                                                            
                                                            <button type="button" data-modal-toggle="viewCommentModal<?= $comment['id'] ?>" class="text-dark-300 bg-dark-700 hover:bg-dark-600 focus:ring-4 focus:outline-none focus:ring-dark-600 rounded-lg border border-dark-600 text-sm font-medium px-5 py-2.5 hover:text-white focus:z-10">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Delete Comment Modal -->
                                            <div id="deleteCommentModal<?= $comment['id'] ?>" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 w-full md:inset-0 h-modal md:h-full justify-center items-center">
                                                <div class="relative p-4 w-full max-w-md h-full md:h-auto">
                                                    <div class="relative bg-dark-800 rounded-lg shadow">
                                                        <div class="flex justify-between items-center p-5 rounded-t border-b border-dark-700">
                                                            <h3 class="text-xl font-medium text-white">
                                                                Confirm Deletion
                                                            </h3>
                                                            <button type="button" class="text-dark-400 bg-transparent hover:bg-dark-700 hover:text-white rounded-lg text-sm p-1.5 ml-auto inline-flex items-center" data-modal-toggle="deleteCommentModal<?= $comment['id'] ?>">
                                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                                            </button>
                                                        </div>
                                                        <div class="p-6 text-center">
                                                            <svg class="mx-auto mb-4 w-14 h-14 text-accent-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                            <h3 class="mb-5 text-lg font-normal text-dark-300">
                                                                Are you sure you want to delete this comment?
                                                            </h3>
                                                            <form action="comments.php" method="POST">
                                                                <input type="hidden" name="comment_id" value="<?= $comment['id'] ?>">
                                                                <button type="submit" name="delete_comment" class="text-white bg-accent-600 hover:bg-accent-800 focus:ring-4 focus:outline-none focus:ring-accent-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
                                                                    Yes, delete comment
                                                                </button>
                                                                <button type="button" data-modal-toggle="deleteCommentModal<?= $comment['id'] ?>" class="text-dark-300 bg-dark-700 hover:bg-dark-600 focus:ring-4 focus:outline-none focus:ring-dark-600 rounded-lg border border-dark-600 text-sm font-medium px-5 py-2.5 hover:text-white focus:z-10">
                                                                    Cancel
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
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

