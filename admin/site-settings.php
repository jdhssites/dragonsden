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

// Get current settings
$settings = [];
$stmt = $pdo->query("SELECT * FROM settings");
if($stmt) {
    while($row = $stmt->fetch()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
}

// Process settings update
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_settings'])) {
    // Site Information
    $site_name = trim($_POST['site_name']);
    $site_description = trim($_POST['site_description']);
    $site_email = trim($_POST['site_email']);
    
    // Social Media
    $facebook_url = trim($_POST['facebook_url']);
    $twitter_url = trim($_POST['twitter_url']);
    $instagram_url = trim($_POST['instagram_url']);
    $linkedin_url = trim($_POST['linkedin_url']);
    
    // Content Settings
    $articles_per_page = (int)$_POST['articles_per_page'];
    $allow_comments = isset($_POST['allow_comments']) ? 1 : 0;
    $moderate_comments = isset($_POST['moderate_comments']) ? 1 : 0;
    
    // Validation
    if(empty($site_name)) {
        $errors[] = "Site name is required";
    }
    
    if(empty($site_email)) {
        $errors[] = "Site email is required";
    } elseif(!filter_var($site_email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address";
    }
    
    if($articles_per_page < 1) {
        $errors[] = "Articles per page must be at least 1";
    }
    
    // If no errors, update settings
    if(empty($errors)) {
        // Function to update or insert a setting
        function updateSetting($pdo, $key, $value) {
            $stmt = $pdo->prepare("SELECT * FROM settings WHERE setting_key = ?");
            $stmt->execute([$key]);
            
            if($stmt->rowCount() > 0) {
                $stmt = $pdo->prepare("UPDATE settings SET setting_value = ?, updated_at = NOW() WHERE setting_key = ?");
                return $stmt->execute([$value, $key]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
                return $stmt->execute([$key, $value]);
            }
        }
        
        // Update all settings
        $update_success = true;
        $update_success &= updateSetting($pdo, 'site_name', $site_name);
        $update_success &= updateSetting($pdo, 'site_description', $site_description);
        $update_success &= updateSetting($pdo, 'site_email', $site_email);
        $update_success &= updateSetting($pdo, 'facebook_url', $facebook_url);
        $update_success &= updateSetting($pdo, 'twitter_url', $twitter_url);
        $update_success &= updateSetting($pdo, 'instagram_url', $instagram_url);
        $update_success &= updateSetting($pdo, 'linkedin_url', $linkedin_url);
        $update_success &= updateSetting($pdo, 'articles_per_page', $articles_per_page);
        $update_success &= updateSetting($pdo, 'allow_comments', $allow_comments);
        $update_success &= updateSetting($pdo, 'moderate_comments', $moderate_comments);
        
        if($update_success) {
            $success = 'Settings updated successfully!';
            
            // Refresh settings
            $stmt = $pdo->query("SELECT * FROM settings");
            $settings = [];
            while($row = $stmt->fetch()) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }
        } else {
            $errors[] = "Failed to update settings. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Settings - Dragon's Den Admin</title>

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
                        <a href="site-settings.php" class="active-nav-link text-white group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                            <i class="fas fa-cog mr-3 text-primary-500"></i>
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
                    <h1 class="text-2xl font-bold text-white">Site Settings</h1>
                    <p class="mt-1 text-dark-400">Configure your website settings</p>
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
            
            <!-- Settings Form -->
            <div class="bg-dark-800 rounded-xl shadow-md overflow-hidden">
                <form action="site-settings.php" method="POST">
                    <div class="p-6 space-y-6">
                        <!-- Site Information -->
                        <div>
                            <h3 class="text-lg font-medium text-white">Site Information</h3>
                            <div class="mt-4 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                                <div class="sm:col-span-4">
                                    <label for="site_name" class="block text-sm font-medium text-white">Site Name</label>
                                    <input type="text" id="site_name" name="site_name" value="<?= htmlspecialchars($settings['site_name'] ?? 'Dragon\'s Den') ?>" required class="mt-1 block w-full rounded-md border-dark-700 bg-dark-700 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm text-white">
                                </div>
                                
                                <div class="sm:col-span-6">
                                    <label for="site_description" class="block text-sm font-medium text-white">Site Description</label>
                                    <textarea id="site_description" name="site_description" rows="3" class="mt-1 block w-full rounded-md border-dark-700 bg-dark-700 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm text-white"><?= htmlspecialchars($settings['site_description'] ?? 'Your Source for News') ?></textarea>
                                    <p class="mt-2 text-sm text-dark-400">Brief description of your website. This will be used in meta tags.</p>
                                </div>
                                
                                <div class="sm:col-span-4">
                                    <label for="site_email" class="block text-sm font-medium text-white">Contact Email</label>
                                    <input type="email" id="site_email" name="site_email" value="<?= htmlspecialchars($settings['site_email'] ?? 'contact@example.com') ?>" required class="mt-1 block w-full rounded-md border-dark-700 bg-dark-700 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm text-white">
                                </div>
                            </div>
                        </div>
                        
                        <div class="border-t border-dark-700 pt-6">
                            <h3 class="text-lg font-medium text-white">Social Media</h3>
                            <div class="mt-4 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                                <div class="sm:col-span-3">
                                    <label for="facebook_url" class="block text-sm font-medium text-white">Facebook URL</label>
                                    <div class="mt-1 flex rounded-md shadow-sm">
                                        <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-dark-700 bg-dark-800 text-dark-400 sm:text-sm">
                                            <i class="fab fa-facebook-f"></i>
                                        </span>
                                        <input type="url" id="facebook_url" name="facebook_url" value="<?= htmlspecialchars($settings['facebook_url'] ?? '') ?>" class="flex-1 min-w-0 block w-full rounded-none rounded-r-md border-dark-700 bg-dark-700 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm text-white">
                                    </div>
                                </div>
                                
                                <div class="sm:col-span-3">
                                    <label for="twitter_url" class="block text-sm font-medium text-white">Twitter URL</label>
                                    <div class="mt-1 flex rounded-md shadow-sm">
                                        <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-dark-700 bg-dark-800 text-dark-400 sm:text-sm">
                                            <i class="fab fa-twitter"></i>
                                        </span>
                                        <input type="url" id="twitter_url" name="twitter_url" value="<?= htmlspecialchars($settings['twitter_url'] ?? '') ?>" class="flex-1 min-w-0 block w-full rounded-none rounded-r-md border-dark-700 bg-dark-700 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm text-white">
                                    </div>
                                </div>
                                
                                <div class="sm:col-span-3">
                                    <label for="instagram_url" class="block text-sm font-medium text-white">Instagram URL</label>
                                    <div class="mt-1 flex rounded-md shadow-sm">
                                        <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-dark-700 bg-dark-800 text-dark-400 sm:text-sm">
                                            <i class="fab fa-instagram"></i>
                                        </span>
                                        <input type="url" id="instagram_url" name="instagram_url" value="<?= htmlspecialchars($settings['instagram_url'] ?? '') ?>" class="flex-1 min-w-0 block w-full rounded-none rounded-r-md border-dark-700 bg-dark-700 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm text-white">
                                    </div>
                                </div>
                                
                                <div class="sm:col-span-3">
                                    <label for="linkedin_url" class="block text-sm font-medium text-white">LinkedIn URL</label>
                                    <div class="mt-1 flex rounded-md shadow-sm">
                                        <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-dark-700 bg-dark-800 text-dark-400 sm:text-sm">
                                            <i class="fab fa-linkedin-in"></i>
                                        </span>
                                        <input type="url" id="linkedin_url" name="linkedin_url" value="<?= htmlspecialchars($settings['linkedin_url'] ?? '') ?>" class="flex-1 min-w-0 block w-full rounded-none rounded-r-md border-dark-700 bg-dark-700 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm text-white">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="border-t border-dark-700 pt-6">
                            <h3 class="text-lg font-medium text-white">Content Settings</h3>
                            <div class="mt-4 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                                <div class="sm:col-span-2">
                                    <label for="articles_per_page" class="block text-sm font-medium text-white">Articles Per Page</label>
                                    <input type="number" id="articles_per_page" name="articles_per_page" min="1" value="<?= (int)($settings['articles_per_page'] ?? 10) ?>" required class="mt-1 block w-full rounded-md border-dark-700 bg-dark-700 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm text-white">
                                </div>
                                
                                <div class="sm:col-span-6">
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input id="allow_comments" name="allow_comments" type="checkbox" <?= ($settings['allow_comments'] ?? 1) ? 'checked' : '' ?> class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-dark-700 rounded bg-dark-700">
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="allow_comments" class="font-medium text-white">Allow Comments</label>
                                            <p class="text-dark-400">Enable or disable commenting on articles.</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="sm:col-span-6">
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input id="moderate_comments" name="moderate_comments" type="checkbox" <?= ($settings['moderate_comments'] ?? 1) ? 'checked' : '' ?> class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-dark-700 rounded bg-dark-700">
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="moderate_comments" class="font-medium text-white">Moderate Comments</label>
                                            <p class="text-dark-400">If enabled, comments will require approval before being published.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="px-4 py-3 bg-dark-900 text-right sm:px-6">
                        <button type="submit" name="update_settings" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 focus:ring-offset-dark-950">
                            Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</body>
</html>

