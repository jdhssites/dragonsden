<?php
// Get all categories for navigation
$categories_query = $pdo->query("SELECT * FROM categories ORDER BY name");
$nav_categories = $categories_query->fetchAll();

// Get current page for active nav highlighting
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/chat-bubble.css">
    <title><?= isset($page_title) ? $page_title : "Dragon's Den - Your Source for News" ?></title>
    
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
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        /* Admin UI Enhancements */
        .admin-badge {
            background: linear-gradient(135deg, #e11d48 0%, #be123c 100%);
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1), 0 1px 2px rgba(0, 0, 0, 0.06);
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.8;
            }
        }
        
        .admin-border {
            border: 2px solid #e11d48;
            box-shadow: 0 0 0 2px rgba(225, 29, 72, 0.3);
        }
        
        .admin-glow {
            box-shadow: 0 0 8px rgba(225, 29, 72, 0.6);
        }
        
        /* Fix for form inputs */
        .dark input[type="text"],
        .dark input[type="email"],
        .dark input[type="password"],
        .dark textarea,
        .dark select {
            color: #1e293b;
            background-color: #f8fafc;
        }
        
        .dark input[type="text"]::placeholder,
        .dark input[type="email"]::placeholder,
        .dark input[type="password"]::placeholder,
        .dark textarea::placeholder,
        .dark select::placeholder {
            color: #64748b;
        }
    </style>
</head>
<body class="bg-dark-950 text-dark-100 min-h-screen flex flex-col <?= isAdmin() ? 'admin-mode' : '' ?>">
    <!-- Admin Top Bar - Only visible to admins -->
    <?php if (isLoggedIn() && isAdmin()): ?>
    <div class="bg-accent-600 text-white py-1 px-4">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <i class="fas fa-shield-alt"></i>
                <span class="text-sm font-medium">Admin Mode Active</span>
            </div>
            <div class="flex items-center space-x-4">
                <a href="admin/dashboard.php" class="text-sm hover:underline flex items-center">
                    <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
                </a>
                <a href="admin/manage-users.php" class="text-sm hover:underline flex items-center">
                    <i class="fas fa-users mr-1"></i> Users
                </a>
                <a href="admin/manage-articles.php" class="text-sm hover:underline flex items-center">
                    <i class="fas fa-newspaper mr-1"></i> Articles
                </a>
                <a href="admin/manage-categories.php" class="text-sm hover:underline flex items-center">
                    <i class="fas fa-tags mr-1"></i> Categories
                </a>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Header -->
    <header class="bg-dark-900 border-b border-dark-800 sticky top-0 z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <!-- Logo -->
                    <a href="index.php" class="flex-shrink-0 flex items-center">
                        <span class="font-display text-2xl font-bold text-white">Dragon's <span class="text-primary-500">Den</span></span>
                    </a>
                    
                    <!-- Desktop Navigation -->
                    <nav class="hidden md:ml-6 md:flex md:space-x-4">
                        <a href="index.php" class="<?= $current_page == 'index.php' ? 'text-white bg-dark-800' : 'text-dark-300 hover:text-white hover:bg-dark-800' ?> px-3 py-2 rounded-md text-sm font-medium">Home</a>
                        
                        <!-- Categories Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" @click.away="open = false" type="button" class="<?= $current_page == 'categories.php' || $current_page == 'category.php' ? 'text-white bg-dark-800' : 'text-dark-300 hover:text-white hover:bg-dark-800' ?> group px-3 py-2 rounded-md inline-flex items-center text-sm font-medium focus:outline-none" aria-expanded="false">
                                <span>Categories</span>
                                <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            
                            <div x-show="open" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-dark-800 ring-1 ring-black ring-opacity-5 focus:outline-none z-10" style="display: none;">
                                <a href="categories.php" class="block px-4 py-2 text-sm text-dark-300 hover:bg-dark-700 hover:text-white">All Categories</a>
                                <div class="border-t border-dark-700 my-1"></div>
                                <?php foreach ($nav_categories as $cat): ?>
                                <a href="category.php?id=<?= $cat['id'] ?>" class="block px-4 py-2 text-sm text-dark-300 hover:bg-dark-700 hover:text-white"><?= htmlspecialchars($cat['name']) ?></a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <a href="all-articles.php" class="<?= $current_page == 'all-articles.php' ? 'text-white bg-dark-800' : 'text-dark-300 hover:text-white hover:bg-dark-800' ?> px-3 py-2 rounded-md text-sm font-medium">Articles</a>
                        <a href="about.php" class="<?= $current_page == 'about.php' ? 'text-white bg-dark-800' : 'text-dark-300 hover:text-white hover:bg-dark-800' ?> px-3 py-2 rounded-md text-sm font-medium">About</a>
                        <a href="team.php" class="<?= $current_page == 'team.php' ? 'text-white bg-dark-800' : 'text-dark-300 hover:text-white hover:bg-dark-800' ?> px-3 py-2 rounded-md text-sm font-medium">Our Team</a>
                        <a href="contact.php" class="<?= $current_page == 'contact.php' ? 'text-white bg-dark-800' : 'text-dark-300 hover:text-white hover:bg-dark-800' ?> px-3 py-2 rounded-md text-sm font-medium">Contact</a>
                    </nav>
                </div>
                
                <div class="flex items-center">
                    <!-- Search -->
                    <div class="hidden md:block">
                        <form action="search.php" method="GET" class="relative">
                            <input type="text" name="q" placeholder="Search..." class="w-48 bg-dark-800 border border-dark-700 rounded-md py-1.5 pl-3 pr-10 text-sm text-white placeholder-dark-400 focus:outline-none focus:ring-1 focus:ring-primary-500 focus:border-primary-500">
                            <button type="submit" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <i class="fas fa-search text-dark-400"></i>
                            </button>
                        </form>
                    </div>
                    
                    <!-- User Menu -->
                    <?php if (isLoggedIn()): ?>
                    <div class="ml-3 relative" x-data="{ open: false }">
                        <div>
                            <button @click="open = !open" @click.away="open = false" type="button" class="flex text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 focus:ring-offset-dark-900 <?= isAdmin() ? 'admin-border admin-glow' : '' ?>" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                                <span class="sr-only">Open user menu</span>
                                <img class="h-8 w-8 rounded-full" src="https://www.gravatar.com/avatar/<?= md5(strtolower(trim($_SESSION['email']))) ?>?s=80&d=mp" alt="User avatar">
                                <?php if (isAdmin()): ?>
                                <span class="absolute -top-2 -right-2 admin-badge">Admin</span>
                                <?php endif; ?>
                            </button>
                        </div>
                        <div x-show="open" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-dark-800 ring-1 ring-black ring-opacity-5 focus:outline-none z-10" style="display: none;">
                            <div class="px-4 py-2 text-xs text-dark-400 border-b border-dark-700">
                                <?php if (isAdmin()): ?>
                                <div class="flex items-center space-x-2 mb-1">
                                    <span class="admin-badge">Admin</span>
                                    <span class="font-medium text-white"><?= htmlspecialchars($_SESSION['username']) ?></span>
                                </div>
                                <div class="text-dark-400 text-xs">Full administrative access</div>
                                <?php else: ?>
                                Signed in as <span class="font-medium text-white"><?= htmlspecialchars($_SESSION['username']) ?></span>
                                <?php endif; ?>
                            </div>
                            <a href="profile.php" class="block px-4 py-2 text-sm text-dark-300 hover:bg-dark-700 hover:text-white">
                                <i class="fas fa-user mr-2"></i> Profile
                            </a>
                            <a href="my-articles.php" class="block px-4 py-2 text-sm text-dark-300 hover:bg-dark-700 hover:text-white">
                                <i class="fas fa-newspaper mr-2"></i> My Articles
                            </a>
                            <?php if (isAdmin()): ?>
                            <div class="border-t border-dark-700 my-1"></div>
                            <a href="admin/dashboard.php" class="block px-4 py-2 text-sm text-accent-400 hover:bg-dark-700 hover:text-accent-300">
                                <i class="fas fa-tachometer-alt mr-2"></i> Admin Dashboard
                            </a>
                            <a href="admin/manage-users.php" class="block px-4 py-2 text-sm text-accent-400 hover:bg-dark-700 hover:text-accent-300">
                                <i class="fas fa-users mr-2"></i> Manage Users
                            </a>
                            <a href="admin/manage-articles.php" class="block px-4 py-2 text-sm text-accent-400 hover:bg-dark-700 hover:text-accent-300">
                                <i class="fas fa-newspaper mr-2"></i> Manage Articles
                            </a>
                            <a href="admin/manage-categories.php" class="block px-4 py-2 text-sm text-accent-400 hover:bg-dark-700 hover:text-accent-300">
                                <i class="fas fa-tags mr-2"></i> Manage Categories
                            </a>
                            <?php endif; ?>
                            <div class="border-t border-dark-700 my-1"></div>
                            <a href="logout.php" class="block px-4 py-2 text-sm text-dark-300 hover:bg-dark-700 hover:text-white">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </a>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="ml-3 flex items-center space-x-2">
                        <a href="login.php" class="text-dark-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Login</a>
                        <a href="register.php" class="bg-primary-600 hover:bg-primary-700 text-white px-3 py-2 rounded-md text-sm font-medium transition-colors">Register</a>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Mobile menu button -->
                    <div class="md:hidden ml-3">
                        <button type="button" @click="mobileMenuOpen = !mobileMenuOpen" class="inline-flex items-center justify-center p-2 rounded-md text-dark-400 hover:text-white hover:bg-dark-800 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-500" aria-expanded="false">
                            <span class="sr-only">Open main menu</span>
                            <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Mobile menu, show/hide based on menu state -->
        <div x-data="{ mobileMenuOpen: false }" x-show="mobileMenuOpen" class="md:hidden bg-dark-800 border-t border-dark-700" style="display: none;">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="index.php" class="<?= $current_page == 'index.php' ? 'bg-dark-700 text-white' : 'text-dark-300 hover:bg-dark-700 hover:text-white' ?> block px-3 py-2 rounded-md text-base font-medium">Home</a>
                <a href="categories.php" class="<?= $current_page == 'categories.php' ? 'bg-dark-700 text-white' : 'text-dark-300 hover:bg-dark-700 hover:text-white' ?> block px-3 py-2 rounded-md text-base font-medium">Categories</a>
                <a href="all-articles.php" class="<?= $current_page == 'all-articles.php' ? 'bg-dark-700 text-white' : 'text-dark-300 hover:bg-dark-700 hover:text-white' ?> block px-3 py-2 rounded-md text-base font-medium">Articles</a>
                <a href="about.php" class="<?= $current_page == 'about.php' ? 'bg-dark-700 text-white' : 'text-dark-300 hover:bg-dark-700 hover:text-white' ?> block px-3 py-2 rounded-md text-base font-medium">About</a>
                <a href="team.php" class="<?= $current_page == 'team.php' ? 'bg-dark-700 text-white' : 'text-dark-300 hover:bg-dark-700 hover:text-white' ?> block px-3 py-2 rounded-md text-base font-medium">Our Team</a>
                <a href="contact.php" class="<?= $current_page == 'contact.php' ? 'bg-dark-700 text-white' : 'text-dark-300 hover:bg-dark-700 hover:text-white' ?> block px-3 py-2 rounded-md text-base font-medium">Contact</a>
                
                <!-- Mobile Search -->
                <div class="mt-3 px-3">
                    <form action="search.php" method="GET" class="relative">
                        <input type="text" name="q" placeholder="Search..." class="w-full bg-dark-800 border border-dark-700 rounded-md py-1.5 pl-3 pr-10 text-sm text-white placeholder-dark-400 focus:outline-none focus:ring-1 focus:ring-primary-500 focus:border-primary-500">
                        <button type="submit" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <i class="fas fa-search text-dark-400"></i>
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Mobile Categories -->
            <div class="px-5 pt-2 pb-3">
                <div class="text-dark-400 text-xs font-semibold uppercase tracking-wider mb-2">Categories</div>
                <div class="grid grid-cols-2 gap-2">
                    <?php foreach ($nav_categories as $cat): ?>
                    <a href="category.php?id=<?= $cat['id'] ?>" class="text-dark-300 hover:bg-dark-700 hover:text-white block px-3 py-2 rounded-md text-sm"><?= htmlspecialchars($cat['name']) ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Mobile Admin Links (Only for admins) -->
            <?php if (isLoggedIn() && isAdmin()): ?>
            <div class="px-5 pt-2 pb-3 border-t border-dark-700">
                <div class="flex items-center space-x-2 mb-2">
                    <span class="admin-badge">Admin</span>
                    <span class="text-dark-400 text-xs font-semibold uppercase tracking-wider">Admin Controls</span>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <a href="admin/dashboard.php" class="text-accent-400 hover:bg-dark-700 hover:text-accent-300 block px-3 py-2 rounded-md text-sm">
                        <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
                    </a>
                    <a href="admin/manage-users.php" class="text-accent-400 hover:bg-dark-700 hover:text-accent-300 block px-3 py-2 rounded-md text-sm">
                        <i class="fas fa-users mr-1"></i> Users
                    </a>
                    <a href="admin/manage-articles.php" class="text-accent-400 hover:bg-dark-700 hover:text-accent-300 block px-3 py-2 rounded-md text-sm">
                        <i class="fas fa-newspaper mr-1"></i> Articles
                    </a>
                    <a href="admin/manage-categories.php" class="text-accent-400 hover:bg-dark-700 hover:text-accent-300 block px-3 py-2 rounded-md text-sm">
                        <i class="fas fa-tags mr-1"></i> Categories
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </header>
    
    <!-- Flash Messages -->
    <?php if (isset($_SESSION['flash_messages']['success'])): ?>
    <div class="bg-green-900 border-l-4 border-green-500 text-white p-4 mb-4 mx-auto max-w-7xl mt-4" role="alert">
        <p><?= $_SESSION['flash_messages']['success']; unset($_SESSION['flash_messages']['success']); ?></p>
    </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['flash_messages']['error'])): ?>
    <div class="bg-red-900 border-l-4 border-red-500 text-white p-4 mb-4 mx-auto max-w-7xl mt-4" role="alert">
        <p><?= $_SESSION['flash_messages']['error']; unset($_SESSION['flash_messages']['error']); ?></p>
    </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['flash_messages']['info'])): ?>
    <div class="bg-blue-900 border-l-4 border-blue-500 text-white p-4 mb-4 mx-auto max-w-7xl mt-4" role="alert">
        <p><?= $_SESSION['flash_messages']['info']; unset($_SESSION['flash_messages']['info']); ?></p>
    </div>
    <?php endif; ?>

