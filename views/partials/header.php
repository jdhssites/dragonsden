<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dragon's Den | Professional News & Insights</title>
    <meta name="description" content="Your trusted source for professional insights and in-depth analysis across business, technology, and more.">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        serif: ['Times New Roman', 'serif'],
                    },
                    colors: {
                        primary: {
                            DEFAULT: '#0f4c81',
                            foreground: '#ffffff',
                        },
                        secondary: {
                            DEFAULT: '#f0f2f5',
                            foreground: '#1f2937',
                        },
                        muted: {
                            DEFAULT: '#f0f2f5',
                            foreground: '#6b7280',
                        },
                        accent: {
                            DEFAULT: '#f0f2f5',
                            foreground: '#1f2937',
                        },
                        destructive: {
                            DEFAULT: '#ef4444',
                            foreground: '#ffffff',
                        },
                        border: '#e5e7eb',
                        background: '#ffffff',
                        foreground: '#1f2937',
                    },
                },
            },
        }
    </script>
    
    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Times New Roman', serif;
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Times New Roman', serif;
            font-weight: 700;
            letter-spacing: -0.025em;
        }
        .dark {
            --tw-bg-opacity: 1;
            background-color: rgb(17 24 39 / var(--tw-bg-opacity));
            --tw-text-opacity: 1;
            color: rgb(243 244 246 / var(--tw-text-opacity));
        }
    </style>
</head>
<body class="min-h-screen flex flex-col">
    <header class="border-b">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <a href="/" class="flex items-center">
                    <span class="text-2xl font-bold tracking-tight text-primary">Dragon's Den</span>
                </a>

                <div class="hidden md:flex items-center gap-6">
                    <nav class="flex items-center gap-8">
                        <a href="/" class="text-base font-medium text-muted-foreground hover:text-primary transition-colors">Home</a>
                        <a href="/articles" class="text-base font-medium text-muted-foreground hover:text-primary transition-colors">Articles</a>
                        <a href="/categories" class="text-base font-medium text-muted-foreground hover:text-primary transition-colors">Categories</a>
                        <a href="/about" class="text-base font-medium text-muted-foreground hover:text-primary transition-colors">About</a>
                    </nav>
                    <div class="flex items-center gap-4">
                        <!-- Theme Toggle Button -->
                        <button id="theme-toggle" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800">
                            <!-- Sun icon (shown in light mode) -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="sun hidden dark:block"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
                            <!-- Moon icon (shown in dark mode) -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="moon block dark:hidden"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
                        </button>
                        
                        <!-- User Menu -->
                        <?php if (isLoggedIn()): ?>
                            <div class="relative group">
                                <button class="flex items-center gap-2 text-sm font-medium">
                                    <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center">
                                        <?= substr($_SESSION['user_name'], 0, 1) ?>
                                    </div>
                                </button>
                                <div class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-md shadow-lg border border-gray-200 dark:border-gray-700 hidden group-hover:block z-10">
                                    <div class="p-3 border-b border-gray-200 dark:border-gray-700">
                                        <p class="font-medium"><?= escapeHtml($_SESSION['user_name']) ?></p>
                                        <p class="text-sm text-muted-foreground"><?= escapeHtml($_SESSION['user_email']) ?></p>
                                        <?php if ($_SESSION['is_admin']): ?>
                                            <p class="text-xs text-primary">Administrator</p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="py-1">
                                        <a href="/profile" class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">Profile</a>
                                        <?php if ($_SESSION['is_admin']): ?>
                                            <a href="/admin/articles" class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">Manage Articles</a>
                                        <?php endif; ?>
                                    </div>
                                    <div class="py-1 border-t border-gray-200 dark:border-gray-700">
                                        <a href="/logout" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100 dark:hover:bg-gray-700">Log out</a>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="flex items-center gap-2">
                                <a href="/login" class="flex items-center gap-2 px-3 py-1.5 text-sm font-medium rounded-md hover:bg-gray-100 dark:hover:bg-gray-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" y1="12" x2="3" y2="12"></line></svg>
                                    <span>Login</span>
                                </a>
                                <a href="/register" class="flex items-center gap-2 px-3 py-1.5 text-sm font-medium bg-primary text-white rounded-md hover:bg-primary/90">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
                                    <span>Register</span>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Mobile menu button -->
                <button id="mobile-menu-button" class="md:hidden p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
                </button>
            </div>
        </div>
        
        <!-- Mobile menu -->
        <div id="mobile-menu" class="md:hidden hidden">
            <div class="px-4 py-3 space-y-1">
                <a href="/" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-gray-100 dark:hover:bg-gray-800">Home</a>
                <a href="/articles" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-gray-100 dark:hover:bg-gray-800">Articles</a>
                <a href="/categories" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-gray-100 dark:hover:bg-gray-800">Categories</a>
                <a href="/about" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-gray-100 dark:hover:bg-gray-800">About</a>
                
                <?php if (isLoggedIn()): ?>
                    <a href="/profile" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-gray-100 dark:hover:bg-gray-800">Profile</a>
                    <?php if ($_SESSION['is_admin']): ?>
                        <a href="/admin/articles" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-gray-100 dark:hover:bg-gray-800">Manage Articles</a>
                    <?php endif; ?>
                    <a href="/logout" class="block px-3 py-2 rounded-md text-base font-medium text-red-600 hover:bg-gray-100 dark:hover:bg-gray-800">Log out</a>
                <?php else: ?>
                    <a href="/login" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-gray-100 dark:hover:bg-gray-800">Login</a>
                    <a href="/register" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-gray-100 dark:hover:bg-gray-800">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

