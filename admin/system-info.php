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

// Get system information
$php_version = phpversion();
$mysql_version = $pdo->query('SELECT VERSION() as version')->fetch()['version'];
$server_software = $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown';
$server_os = PHP_OS;
$max_upload_size = ini_get('upload_max_filesize');
$post_max_size = ini_get('post_max_size');
$memory_limit = ini_get('memory_limit');
$max_execution_time = ini_get('max_execution_time');

// Get database statistics
$db_stats = [
    'users' => $pdo->query("SELECT COUNT(*) as count FROM users")->fetch()['count'],
    'articles' => $pdo->query("SELECT COUNT(*) as count FROM articles")->fetch()['count'],
    'categories' => $pdo->query("SELECT COUNT(*) as count FROM categories")->fetch()['count'],
    'comments' => $pdo->query("SELECT COUNT(*) as count FROM comments")->fetch()['count'],
];

// Get database size
$db_size_query = $pdo->query("
    SELECT 
        SUM(data_length + index_length) / 1024 / 1024 AS size_mb 
    FROM 
        information_schema.TABLES 
    WHERE 
        table_schema = '{$db_name}'
");
$db_size = $db_size_query ? round($db_size_query->fetch()['size_mb'], 2) : 'Unknown';

// Get PHP extensions
$loaded_extensions = get_loaded_extensions();
sort($loaded_extensions);

// Get PHP configuration
$php_config = [
    'display_errors' => ini_get('display_errors'),
    'error_reporting' => ini_get('error_reporting'),
    'date.timezone' => ini_get('date.timezone'),
    'session.gc_maxlifetime' => ini_get('session.gc_maxlifetime'),
    'allow_url_fopen' => ini_get('allow_url_fopen'),
    'file_uploads' => ini_get('file_uploads'),
    'max_input_time' => ini_get('max_input_time'),
    'default_charset' => ini_get('default_charset'),
];

// Get server information
$server_info = [
    'HTTP_HOST' => $_SERVER['HTTP_HOST'] ?? 'Unknown',
    'DOCUMENT_ROOT' => $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown',
    'SERVER_ADDR' => $_SERVER['SERVER_ADDR'] ?? 'Unknown',
    'SERVER_PORT' => $_SERVER['SERVER_PORT'] ?? 'Unknown',
    'REMOTE_ADDR' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown',
    'REQUEST_TIME' => date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME'] ?? time()),
];
?>

<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Information - Dragon's Den Admin</title>

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

