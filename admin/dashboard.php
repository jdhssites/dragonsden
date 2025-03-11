<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
session_start();

// Check if user is logged in
if(!isLoggedIn()) {
    setFlashMessage('error', 'Please login to access the admin dashboard.');
    header('Location: ../login.php');
    exit;
}

// Check if role column exists in users table
$stmt = $pdo->prepare("SHOW COLUMNS FROM users LIKE 'role'");
$stmt->execute();
$column_exists = $stmt->fetch();

if(!$column_exists) {
    setFlashMessage('error', 'Database needs to be updated. Please run the update script first.');
    header('Location: ../update-database.php');
    exit;
}

// Check if user is admin
if(!isAdmin()) {
    setFlashMessage('error', 'You do not have permission to access this page.');
    header('Location: ../index.php');
    exit;
}

// Get site statistics
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM users");
$stmt->execute();
$user_count = $stmt->fetch()['count'];

$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM articles");
$stmt->execute();
$article_count = $stmt->fetch()['count'];

$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM categories");
$stmt->execute();
$category_count = $stmt->fetch()['count'];

$stmt = $pdo->prepare("SELECT SUM(views) as total FROM articles");
$stmt->execute();
$total_views = $stmt->fetch()['total'] ?: 0;

// Get recent users
$stmt = $pdo->prepare("SELECT * FROM users ORDER BY created_at DESC LIMIT 5");
$stmt->execute();
$recent_users = $stmt->fetchAll();

// Get team members
$team_members = getTeamMembers($pdo);

// Get recent articles
$stmt = $pdo->prepare("SELECT articles.*, users.username 
                      FROM articles 
                      JOIN users ON articles.user_id = users.id 
                      ORDER BY created_at DESC 
                      LIMIT 5");
$stmt->execute();
$recent_articles = $stmt->fetchAll();

// Get popular articles
$stmt = $pdo->prepare("SELECT articles.*, users.username 
                      FROM articles 
                      JOIN users ON articles.user_id = users.id 
                      ORDER BY views DESC 
                      LIMIT 5");
$stmt->execute();
$popular_articles = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Dragon's Den</title>

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

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
                    <a href="dashboard.php" class="flex-shrink-0 flex items-center">
                        <span class="font-display text-2xl font-bold text-white">Dragon's <span class="text-primary-500">Den</span></span>
                        <span class="ml-3 text-sm bg-accent-600 text-white px-2 py-1 rounded">Admin</span>
                    </a>
                </div>
                
                <div class="flex items-center">
                    <!-- Search -->
                    <div class="hidden md:block mr-4">
                        <div class="relative">
                            <input type="search" placeholder="Search..." class="w-64 rounded-full border border-dark-700 bg-dark-800 py-2 pl-4 pr-10 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 text-white placeholder-dark-400">
                            <button type="submit" class="absolute inset-y-0 right-0 flex items-center pr-3">
                                <i class="fas fa-search text-dark-400"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Notifications -->
                    <button class="mr-4 p-2 rounded-full text-dark-400 hover:text-white hover:bg-dark-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-500">
                        <span class="sr-only">View notifications</span>
                        <i class="fas fa-bell"></i>
                    </button>
                    
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
                                <?php if(isset($_SESSION['role'])): ?>
                                    <div class="mt-1">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full <?= getRoleBadgeClass($_SESSION['role']) ?>">
                                            <?= getRoleDisplayName($_SESSION['role']) ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <a href="../profile.php" class="block px-4 py-2 text-sm text-dark-300 hover:bg-dark-700 hover:text-white">
                                <i class="fas fa-user mr-2"></i> Profile
                            </a>
                            <a href="../index.php" class="block px-4 py-2 text-sm text-dark-300 hover:bg-dark-700 hover:text-white">
                                <i class="fas fa-home mr-2"></i> Back to Site
                            </a>
                            <div class="border-t border-dark-700"></div>
                            <a href="../logout.php" class="block px-4 py-2 text-sm text-dark-300 hover:bg-dark-700 hover:text-white">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
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
                    <a href="dashboard.php" class="active-nav-link text-white group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                        <i class="fas fa-tachometer-alt mr-3 text-primary-500"></i>
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
            <!-- Welcome Banner -->
            <div class="rounded-xl bg-gradient-to-r from-primary-700 to-primary-900 shadow-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-white">Welcome back, <?= htmlspecialchars($_SESSION['username']) ?>!</h1>
                            <p class="text-primary-100 mt-1">Here's what's happening with your site today.</p>
                        </div>
                        <div class="hidden md:block">
                            <div class="text-right">
                                <p class="text-primary-100">Today's Date</p>
                                <p class="text-white font-medium"><?= date('F j, Y') ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div class="bg-dark-800 rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-primary-900 bg-opacity-50 text-primary-400">
                            <i class="fas fa-users text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-sm font-medium text-dark-400">Total Users</h2>
                            <p class="text-2xl font-semibold text-white"><?= number_format($user_count) ?></p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="manage-users.php" class="text-xs text-primary-400 hover:text-primary-300 transition-colors">View all users <i class="fas fa-arrow-right ml-1"></i></a>
                    </div>
                </div>
                
                <div class="bg-dark-800 rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-primary-900 bg-opacity-50 text-primary-400">
                            <i class="fas fa-newspaper text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-sm font-medium text-dark-400">Articles</h2>
                            <p class="text-2xl font-semibold text-white"><?= number_format($article_count) ?></p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="manage-articles.php" class="text-xs text-primary-400 hover:text-primary-300 transition-colors">Manage articles <i class="fas fa-arrow-right ml-1"></i></a>
                    </div>
                </div>
                
                <div class="bg-dark-800 rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-primary-900 bg-opacity-50 text-primary-400">
                            <i class="fas fa-folder text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-sm font-medium text-dark-400">Categories</h2>
                            <p class="text-2xl font-semibold text-white"><?= number_format($category_count) ?></p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="manage-categories.php" class="text-xs text-primary-400 hover:text-primary-300 transition-colors">Manage categories <i class="fas fa-arrow-right ml-1"></i></a>
                    </div>
                </div>
                
                <div class="bg-dark-800 rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-primary-900 bg-opacity-50 text-primary-400">
                            <i class="fas fa-eye text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-sm font-medium text-dark-400">Total Views</h2>
                            <p class="text-2xl font-semibold text-white"><?= number_format($total_views) ?></p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="analytics.php" class="text-xs text-primary-400 hover:text-primary-300 transition-colors">View analytics <i class="fas fa-arrow-right ml-1"></i></a>
                    </div>
                </div>
            </div>
            
            <!-- Charts & Insights -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <div class="bg-dark-800 rounded-xl shadow-md p-6">
                    <h2 class="text-lg font-medium text-white mb-4">User Growth</h2>
                    <div>
                        <canvas id="userChart" height="200"></canvas>
                    </div>
                </div>
                
                <div class="bg-dark-800 rounded-xl shadow-md p-6">
                    <h2 class="text-lg font-medium text-white mb-4">Article Views</h2>
                    <div>
                        <canvas id="viewsChart" height="200"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Recent Activity -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Recent Users -->
                <div class="bg-dark-800 rounded-xl shadow-md overflow-hidden">
                    <div class="px-6 py-4 border-b border-dark-700 flex justify-between items-center">
                        <h2 class="text-lg font-medium text-white">Recent Users</h2>
                        <a href="manage-users.php" class="text-xs text-primary-400 hover:text-primary-300 transition-colors">View all</a>
                    </div>
                    <div class="p-6">
                        <ul class="divide-y divide-dark-700">
                            <?php foreach($recent_users as $user): ?>
                                <li class="py-3 flex items-center">
                                    <img class="h-10 w-10 rounded-full" src="https://www.gravatar.com/avatar/<?= md5(strtolower(trim($user['email']))) ?>?s=100&d=mp" alt="<?= htmlspecialchars($user['username']) ?>">
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-white"><?= htmlspecialchars($user['username']) ?></p>
                                        <p class="text-xs text-dark-400"><?= formatDate($user['created_at']) ?></p>
                                    </div>
                                    <div class="ml-auto">
                                        <span class="px-2 py-1 text-xs rounded-full <?= getRoleBadgeClass($user['role'] ?? 'user') ?>">
                                            <?= getRoleDisplayName($user['role'] ?? 'user') ?>
                                        </span>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                
                <!-- Recent Articles -->
                <div class="bg-dark-800 rounded-xl shadow-md overflow-hidden">
                    <div class="px-6 py-4 border-b border-dark-700 flex justify-between items-center">
                        <h2 class="text-lg font-medium text-white">Recent Articles</h2>
                        <a href="manage-articles.php" class="text-xs text-primary-400 hover:text-primary-300 transition-colors">View all</a>
                    </div>
                    <div class="p-6">
                        <ul class="divide-y divide-dark-700">
                            <?php foreach($recent_articles as $article): ?>
                                <li class="py-3">
                                    <a href="../article.php?id=<?= $article['id'] ?>" class="block hover:bg-dark-700 -mx-2 px-2 py-1 rounded-md transition-colors">
                                        <p class="text-sm font-medium text-white line-clamp-1"><?= htmlspecialchars($article['title']) ?></p>
                                        <div class="flex items-center mt-1">
                                            <p class="text-xs text-dark-400"><?= htmlspecialchars($article['username']) ?></p>
                                            <span class="mx-1 text-dark-500">•</span>
                                            <p class="text-xs text-dark-400"><?= formatDate($article['created_at']) ?></p>
                                        </div>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                
                <!-- Popular Articles -->
                <div class="bg-dark-800 rounded-xl shadow-md overflow-hidden">
                    <div class="px-6 py-4 border-b border-dark-700 flex justify-between items-center">
                        <h2 class="text-lg font-medium text-white">Popular Articles</h2>
                        <a href="analytics.php" class="text-xs text-primary-400 hover:text-primary-300 transition-colors">View analytics</a>
                    </div>
                    <div class="p-6">
                        <ul class="divide-y divide-dark-700">
                            <?php foreach($popular_articles as $article): ?>
                                <li class="py-3">
                                    <a href="../article.php?id=<?= $article['id'] ?>" class="block hover:bg-dark-700 -mx-2 px-2 py-1 rounded-md transition-colors">
                                        <p class="text-sm font-medium text-white line-clamp-1"><?= htmlspecialchars($article['title']) ?></p>
                                        <div class="flex justify-between items-center mt-1">
                                            <p class="text-xs text-dark-400"><?= htmlspecialchars($article['username']) ?></p>
                                            <p class="text-xs font-medium bg-dark-700 text-primary-400 px-2 py-0.5 rounded-full">
                                                <?= number_format($article['views']) ?> views
                                            </p>
                                        </div>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    
    <!-- Chart.js Initialization -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // User Growth Chart
            const userCtx = document.getElementById('userChart').getContext('2d');
            const userChart = new Chart(userCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'New Users',
                        data: [12, 19, 15, 25, 22, 30],
                        fill: true,
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        tension: 0.4,
                        pointBackgroundColor: 'rgba(59, 130, 246, 1)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(75, 85, 99, 0.1)'
                            },
                            ticks: {
                                color: 'rgba(255, 255, 255, 0.7)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: 'rgba(255, 255, 255, 0.7)'
                            }
                        }
                    }
                }
            });
            
            // Article Views Chart
            const viewsCtx = document.getElementById('viewsChart').getContext('2d');
            const viewsChart = new Chart(viewsCtx, {
                type: 'bar',
                data: {
                    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                    datasets: [{
                        label: 'Views',
                        data: [650, 590, 800, 810, 760, 550, 490],
                        backgroundColor: 'rgba(59, 130, 246, 0.8)',
                        borderRadius: 4,
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(75, 85, 99, 0.1)'
                            },
                            ticks: {
                                color: 'rgba(255, 255, 255, 0.7)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: 'rgba(255, 255, 255, 0.7)'
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>

