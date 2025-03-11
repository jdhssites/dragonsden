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

// Handle file upload
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_media'])) {
    // Check if file was uploaded without errors
    if(isset($_FILES['media_file']) && $_FILES['media_file']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $filename = $_FILES['media_file']['name'];
        $filetype = $_FILES['media_file']['type'];
        $filesize = $_FILES['media_file']['size'];
        
        // Validate file extension
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if(!in_array(strtolower($ext), $allowed)) {
            $errors[] = "Error: Please select a valid file format (JPG, JPEG, PNG, GIF, WEBP).";
        }
        
        // Validate file size (max 5MB)
        if($filesize > 5242880) {
            $errors[] = "Error: File size is too large. Maximum file size is 5MB.";
        }
        
        // If no errors, process the upload
        if(empty($errors)) {
            // Create uploads directory if it doesn't exist
            $upload_dir = '../uploads/';
            if(!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            // Generate a unique filename
            $new_filename = uniqid() . '.' . $ext;
            $upload_path = $upload_dir . $new_filename;
            
            // Move the file to the uploads directory
            if(move_uploaded_file($_FILES['media_file']['tmp_name'], $upload_path)) {
                // Save file info to database
                $title = !empty($_POST['title']) ? $_POST['title'] : $filename;
                $description = $_POST['description'] ?? '';
                $file_url = 'uploads/' . $new_filename;
                
                $stmt = $pdo->prepare("INSERT INTO media (title, description, file_name, file_type, file_size, file_url, user_id, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
                
                if($stmt->execute([$title, $description, $new_filename, $filetype, $filesize, $file_url, $_SESSION['user_id']])) {
                    $success = 'File uploaded successfully!';
                } else {
                    $errors[] = "Error: Failed to save file information to database.";
                }
            } else {
                $errors[] = "Error: Failed to upload file.";
            }
        }
    } else {
        $errors[] = "Error: " . $_FILES['media_file']['error'];
    }
}

// Handle file deletion
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_media'])) {
    $media_id = (int)$_POST['media_id'];
    
    // Get file info
    $stmt = $pdo->prepare("SELECT * FROM media WHERE id = ?");
    $stmt->execute([$media_id]);
    $media = $stmt->fetch();
    
    if($media) {
        // Delete file from server
        $file_path = '../' . $media['file_url'];
        if(file_exists($file_path)) {
            unlink($file_path);
        }
        
        // Delete from database
        $stmt = $pdo->prepare("DELETE FROM media WHERE id = ?");
        
        if($stmt->execute([$media_id])) {
            $success = 'File deleted successfully!';
        } else {
            $errors[] = "Error: Failed to delete file from database.";
        }
    } else {
        $errors[] = "Error: File not found.";
    }
}

// Get all media files
$stmt = $pdo->query("SELECT m.*, u.username FROM media m JOIN users u ON m.user_id = u.id ORDER BY m.created_at DESC");
$media_files = $stmt->fetchAll();

// Calculate total storage used
$total_size = 0;
foreach($media_files as $file) {
    $total_size += $file['file_size'];
}
$total_size_mb = round($total_size / 1048576, 2); // Convert to MB
?>

<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Media Library - Dragon's Den Admin</title>

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
        
        .media-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
        }
        
        .media-item {
            aspect-ratio: 1 / 1;
            position: relative;
            overflow: hidden;
            border-radius: 0.5rem;
        }
        
        .media-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .media-item:hover img {
            transform: scale(1.05);
        }
        
        .media-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(15, 23, 42, 0.8);
            padding: 0.5rem;
            transform: translateY(100%);
            transition: transform 0.3s ease;
        }
        
        .media-item:hover .media-overlay {
            transform: translateY(0);
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
                        <a href="media-library.php" class="active-nav-link text-white group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                            <i class="fas fa-images mr-3 text-primary-500"></i>
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
                    <h1 class="text-2xl font-bold text-white">Media Library</h1>
                    <p class="mt-1 text-dark-400">Manage your images and media files</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <button type="button" data-modal-toggle="uploadMediaModal" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 focus:ring-offset-dark-950 transition-colors">
                        <i class="fas fa-upload mr-2"></i> Upload New Media
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
            
            <!-- Media Stats -->
            <div class="bg-dark-800 rounded-xl p-4 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="flex items-center">
                        <div class="bg-primary-900 p-3 rounded-lg mr-4">
                            <i class="fas fa-image text-primary-400 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-dark-400 text-sm">Total Files</p>
                            <p class="text-white text-lg font-semibold"><?= count($media_files) ?></p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="bg-primary-900 p-3 rounded-lg mr-4">
                            <i class="fas fa-database text-primary-400 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-dark-400 text-sm">Storage Used</p>
                            <p class="text-white text-lg font-semibold"><?= $total_size_mb ?> MB</p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="bg-primary-900 p-3 rounded-lg mr-4">
                            <i class="fas fa-calendar-alt text-primary-400 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-dark-400 text-sm">Last Upload</p>
                            <p class="text-white text-lg font-semibold">
                                <?= !empty($media_files) ? date('M j, Y', strtotime($media_files[0]['created_at'])) : 'No uploads yet' ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Media Grid -->
            <div class="bg-dark-800 rounded-xl p-6">
                <?php if(empty($media_files)): ?>
                    <div class="text-center py-8">
                        <div class="text-dark-400 text-6xl mb-4">
                            <i class="fas fa-images"></i>
                        </div>
                        <h3 class="text-white text-lg font-medium mb-2">No media files found</h3>
                        <p class="text-dark-300 mb-4">Upload your first image to get started</p>
                        <button type="button" data-modal-toggle="uploadMediaModal" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 focus:ring-offset-dark-950 transition-colors">
                            <i class="fas fa-upload mr-2"></i> Upload New Media
                        </button>
                    </div>
                <?php else: ?>
                    <div class="media-grid">
                        <?php foreach($media_files as $file): ?>
                            <div class="media-item bg-dark-700">
                                <?php if(in_array(strtolower(pathinfo($file['file_name'], PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp'])): ?>
                                    <img src="../<?= $file['file_url'] ?>" alt="<?= htmlspecialchars($file['title']) ?>" loading="lazy">
                                <?php else: ?>
                                    <div class="flex items-center justify-center h-full bg-dark-700">
                                        <i class="fas fa-file-alt text-4xl text-dark-500"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="media-overlay">
                                    <h3 class="text-white text-sm font-medium truncate"><?= htmlspecialchars($file['title']) ?></h3>
                                    <div class="flex justify-between items-center mt-2">
                                        <span class="text-dark-300 text-xs"><?= round($file['file_size'] / 1024, 2) ?> KB</span>
                                        <div class="flex space-x-2">
                                            <button type="button" data-modal-toggle="viewMediaModal<?= $file['id'] ?>" class="text-primary-400 hover:text-primary-300 transition-colors">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" data-modal-toggle="deleteMediaModal<?= $file['id'] ?>" class="text-accent-500 hover:text-accent-400 transition-colors">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- View Media Modal -->
                                <div id="viewMediaModal<?= $file['id'] ?>" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 w-full md:inset-0 h-modal md:h-full justify-center items-center">
                                    <div class="relative p-4 w-full max-w-2xl h-full md:h-auto">
                                        <div class="relative bg-dark-800 rounded-lg shadow">
                                            <div class="flex justify-between items-center p-5 rounded-t border-b border-dark-700">
                                                <h3 class="text-xl font-medium text-white">
                                                    <?= htmlspecialchars($file['title']) ?>
                                                </h3>
                                                <button type="button" class="text-dark-400 bg-transparent hover:bg-dark-700 hover:text-white rounded-lg text-sm p-1.5 ml-auto inline-flex items-center" data-modal-toggle="viewMediaModal<?= $file['id'] ?>">
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                                </button>
                                            </div>
                                            <div class="p-6 space-y-6">
                                                <div class="flex justify-center">
                                                    <?php if(in_array(strtolower(pathinfo($file['file_name'], PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp'])): ?>
                                                        <img src="../<?= $file['file_url'] ?>" alt="<?= htmlspecialchars($file['title']) ?>" class="max-h-96 max-w-full">
                                                    <?php else: ?>
                                                        <div class="flex items-center justify-center h-64 w-64 bg-dark-700 rounded-lg">
                                                            <i class="fas fa-file-alt text-6xl text-dark-500"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                                    <div>
                                                        <h4 class="text-sm font-medium text-dark-400">File Details</h4>
                                                        <div class="mt-2 space-y-2">
                                                            <p class="text-sm text-white">
                                                                <span class="text-dark-400">Name:</span> <?= htmlspecialchars($file['file_name']) ?>
                                                            </p>
                                                            <p class="text-sm text-white">
                                                                <span class="text-dark-400">Type:</span> <?= htmlspecialchars($file['file_type']) ?>
                                                            </p>
                                                            <p class="text-sm text-white">
                                                                <span class="text-dark-400">Size:</span> <?= round($file['file_size'] / 1024, 2) ?> KB
                                                            </p>
                                                            <p class="text-sm text-white">
                                                                <span class="text-dark-400">Uploaded by:</span> <?= htmlspecialchars($file['username']) ?>
                                                            </p>
                                                            <p class="text-sm text-white">
                                                                <span class="text-dark-400">Date:</span> <?= date('F j, Y g:i a', strtotime($file['created_at'])) ?>
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <h4 class="text-sm font-medium text-dark-400">Description</h4>
                                                        <p class="mt-2 text-sm text-white">
                                                            <?= !empty($file['description']) ? nl2br(htmlspecialchars($file['description'])) : 'No description provided.' ?>
                                                        </p>
                                                        
                                                        <h4 class="text-sm font-medium text-dark-400 mt-4">URL</h4>
                                                        <div class="mt-2">
                                                            <input type="text" value="<?= htmlspecialchars($_SERVER['HTTP_HOST'] . '/' . $file['file_url']) ?>" class="w-full rounded-md border-dark-700 bg-dark-700 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm text-white" readonly>
                                                            <p class="mt-1 text-xs text-dark-400">Click to copy the URL</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex items-center p-6 space-x-2 rounded-b border-t border-dark-700">
                                                <button type="button" data-modal-toggle="deleteMediaModal<?= $file['id'] ?>" class="text-white bg-accent-600 hover:bg-accent-700 focus:ring-4 focus:outline-none focus:ring-accent-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Delete</button>
                                                <button type="button" data-modal-toggle="viewMediaModal<?= $file['id'] ?>" class="text-dark-300 bg-dark-700 hover:bg-dark-600 focus:ring-4 focus:outline-none focus:ring-dark-600 rounded-lg border border-dark-600 text-sm font-medium px-5 py-2.5 hover:text-white focus:z-10">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Delete Media Modal -->
                                <div id="deleteMediaModal<?= $file['id'] ?>" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 w-full md:inset-0 h-modal md:h-full justify-center items-center">
                                    <div class="relative p-4 w-full max-w-md h-full md:h-auto">
                                        <div class="relative bg-dark-800 rounded-lg shadow">
                                            <div class="flex justify-between items-center p-5 rounded-t border-b border-dark-700">
                                                <h3 class="text-xl font-medium text-white">
                                                    Confirm Deletion
                                                </h3>
                                                <button type="button" class="text-dark-400 bg-transparent hover:bg-dark-700 hover:text-white rounded-lg text-sm p-1.5 ml-auto inline-flex items-center" data-modal-toggle="deleteMediaModal<?= $file['id'] ?>">
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                                </button>
                                            </div>
                                            <div class="p-6 text-center">
                                                <svg class="mx-auto mb-4 w-14 h-14 text-accent-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                <h3 class="mb-5 text-lg font-normal text-dark-300">
                                                    Are you sure you want to delete this file?
                                                </h3>
                                                <p class="mb-5 text-sm text-accent-400">
                                                    <i class="fas fa-exclamation-triangle mr-1"></i> This action cannot be undone. This will permanently delete the file from the server.
                                                </p>
                                                <form action="media-library.php" method="POST">
                                                    <input type="hidden" name="media_id" value="<?= $file['id'] ?>">
                                                    <button type="submit" name="delete_media" class="text-white bg-accent-600 hover:bg-accent-800 focus:ring-4 focus:outline-none focus:ring-accent-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
                                                        Yes, delete file
                                                    </button>
                                                    <button type="button" data-modal-toggle="deleteMediaModal<?= $file['id'] ?>" class="text-dark-300 bg-dark-700 hover:bg-dark-600 focus:ring-4 focus:outline-none focus:ring-dark-600 rounded-lg border border-dark-600 text-sm font-medium px-5 py-2.5 hover:text-white focus:z-10">
                                                        Cancel
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Upload Media Modal -->
            <div id="uploadMediaModal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 w-full md:inset-0 h-modal md:h-full justify-center items-center">
                <div class="relative p-4 w-full max-w-md h-full md:h-auto">
                    <div class="relative bg-dark-800 rounded-lg shadow">
                        <div class="flex justify-between items-center p-5 rounded-t border-b border-dark-700">
                            <h3 class="text-xl font-medium text-white">
                                Upload New Media
                            </h3>
                            <button type="button" class="text-dark-400 bg-transparent hover:bg-dark-700 hover:text-white rounded-lg text-sm p-1.5 ml-auto inline-flex items-center" data-modal-toggle="uploadMediaModal">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                            </button>
                        </div>
                        <form action="media-library.php" method="POST" enctype="multipart/form-data">
                            <div class="p-6 space-y-6">
                                <div>
                                    <label for="media_file" class="block text-sm font-medium text-white">Select File</label>
                                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-dark-700 border-dashed rounded-md">
                                        <div class="space-y-1 text-center">
                                            <svg class="mx-auto h-12 w-12 text-dark-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <div class="flex text-sm text-dark-400">
                                                <label for="media_file" class="relative cursor-pointer bg-dark-700 rounded-md font-medium text-primary-400 hover:text-primary-300 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary-500 focus-within:ring-offset-dark-800">
                                                    <span>Upload a file</span>
                                                    <input id="media_file" name="media_file" type="file" class="sr-only" required>
                                                </label>
                                                <p class="pl-1">or drag and drop</p>
                                            </div>
                                            <p class="text-xs text-dark-400">
                                                PNG, JPG, GIF, WEBP up to 5MB
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label for="title" class="block text-sm font-medium text-white">Title (Optional)</label>
                                    <input type="text" id="title" name="title" class="mt-1 block w-full rounded-md border-dark-700 bg-dark-700 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm text-white">
                                    <p class="mt-1 text-xs text-dark-400">If left blank, the file name will be used.</p>
                                </div>
                                <div>
                                    <label for="description" class="block text-sm font-medium text-white">Description (Optional)</label>
                                    <textarea id="description" name="description" rows="3" class="mt-1 block w-full rounded-md border-dark-700 bg-dark-700 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm text-white"></textarea>
                                </div>
                            </div>
                            <div class="flex items-center p-6 space-x-2 rounded-b border-t border-dark-700">
                                <button type="submit" name="upload_media" class="text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Upload</button>
                                <button type="button" data-modal-toggle="uploadMediaModal" class="text-dark-300 bg-dark-700 hover:bg-dark-600 focus:ring-4 focus:outline-none focus:ring-dark-600 rounded-lg border border-dark-600 text-sm font-medium px-5 py-2.5 hover:text-white focus:z-10">Cancel</button>
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
            
            // Copy URL to clipboard
            const urlInputs = document.querySelectorAll('input[readonly]');
            urlInputs.forEach(function(input) {
                input.addEventListener('click', function() {
                    this.select();
                    document.execCommand('copy');
                    alert('URL copied to clipboard!');
                });
            });
        });
    </script>
</body>
</html>

