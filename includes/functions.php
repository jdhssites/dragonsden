<?php
// Check if user is logged in
function isLoggedIn() {
  return isset($_SESSION['user_id']);
}

// Format date
function formatDate($date) {
  return date('F j, Y', strtotime($date));
}

// Set flash message
function setFlashMessage($type, $message) {
  $_SESSION['flash_messages'][$type] = $message;
}

// Get flash message
function getFlashMessage($type = null) {
    if ($type !== null) {
        if (isset($_SESSION['flash_messages'][$type])) {
            $message = $_SESSION['flash_messages'][$type];
            unset($_SESSION['flash_messages'][$type]);
            return $message;
        }
        return '';
    } else {
        // Original behavior for backward compatibility
        if (isset($_SESSION['flash_message'])) {
            $message = $_SESSION['flash_message'];
            unset($_SESSION['flash_message']);
            return $message;
        }
        return '';
    }
}

// Check if flash message exists
function hasFlashMessage($type) {
    return isset($_SESSION['flash_messages'][$type]);
}

// Truncate text to a specific length
function truncateText($text, $length = 150) {
  $text = strip_tags($text);
  if(strlen($text) <= $length) {
      return $text;
  }
  
  $text = substr($text, 0, $length);
  $text = substr($text, 0, strrpos($text, ' '));
  return $text . '...';
}

// Get user by ID
function getUserById($pdo, $user_id) {
  $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
  $stmt->execute([$user_id]);
  return $stmt->fetch();
}

// Check if remember me token is valid
function checkRememberMe($pdo) {
  if(isset($_COOKIE['remember_token']) && isset($_COOKIE['user_id'])) {
      $token = $_COOKIE['remember_token'];
      $user_id = $_COOKIE['user_id'];
      
      $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND remember_token = ? AND token_expires > NOW()");
      $stmt->execute([$user_id, $token]);
      $user = $stmt->fetch();
      
      if($user) {
          // Set session
          $_SESSION['user_id'] = $user['id'];
          $_SESSION['username'] = $user['username'];
          $_SESSION['email'] = $user['email'];
          
          // Set role if column exists
          $stmt = $pdo->prepare("SHOW COLUMNS FROM users LIKE 'role'");
          $stmt->execute();
          if($stmt->fetch() && isset($user['role'])) {
              $_SESSION['role'] = $user['role'];
          }
          
          // Renew token
          $expires = time() + 60 * 60 * 24 * 30; // 30 days
          
          $stmt = $pdo->prepare("UPDATE users SET token_expires = ? WHERE id = ?");
          $stmt->execute([date('Y-m-d H:i:s', $expires), $user['id']]);
          
          setcookie('remember_token', $token, $expires, '/', '', false, true);
          setcookie('user_id', $user['id'], $expires, '/', '', false, true);
          
          return true;
      }
  }
  
  return false;
}

// Get popular articles
function getPopularArticles($pdo, $limit = 5) {
  $stmt = $pdo->prepare("SELECT articles.*, users.username 
                        FROM articles 
                        JOIN users ON articles.user_id = users.id 
                        ORDER BY views DESC 
                        LIMIT ?");
  $stmt->execute([$limit]);
  return $stmt->fetchAll();
}

// Get recent articles
function getRecentArticles($pdo, $limit = 5) {
  $stmt = $pdo->prepare("SELECT articles.*, users.username 
                        FROM articles 
                        JOIN users ON articles.user_id = users.id 
                        ORDER BY created_at DESC 
                        LIMIT ?");
  $stmt->execute([$limit]);
  return $stmt->fetchAll();
}

// Get articles by category
function getArticlesByCategory($pdo, $category_id, $limit = 10) {
  $stmt = $pdo->prepare("SELECT articles.*, users.username 
                        FROM articles 
                        JOIN users ON articles.user_id = users.id 
                        WHERE articles.category_id = ? 
                        ORDER BY created_at DESC 
                        LIMIT ?");
  $stmt->execute([$category_id, $limit]);
  return $stmt->fetchAll();
}

// Get articles by user
function getArticlesByUser($pdo, $user_id, $limit = 10) {
  $stmt = $pdo->prepare("SELECT articles.*, users.username 
                        FROM articles 
                        JOIN users ON articles.user_id = users.id 
                        WHERE articles.user_id = ? 
                        ORDER BY created_at DESC 
                        LIMIT ?");
  $stmt->execute([$user_id, $limit]);
  return $stmt->fetchAll();
}

// Search articles
function searchArticles($pdo, $query, $limit = 10) {
  $search = "%$query%";
  $stmt = $pdo->prepare("SELECT articles.*, users.username 
                        FROM articles 
                        JOIN users ON articles.user_id = users.id 
                        WHERE articles.title LIKE ? OR articles.content LIKE ? 
                        ORDER BY created_at DESC 
                        LIMIT ?");
  $stmt->execute([$search, $search, $limit]);
  return $stmt->fetchAll();
}

// Check if user is admin
function isAdmin() {
  // Check if role column exists in session
  if(!isset($_SESSION['role'])) {
    return false;
  }
  
  return $_SESSION['role'] === 'admin';
}

// Check if user has permission to publish articles
function canPublishArticles() {
  if (!isLoggedIn()) return false;
  
  // Check if role column exists in session
  if(!isset($_SESSION['role'])) {
    return false;
  }
  
  $allowedRoles = ['admin', 'editor', 'writer'];
  return in_array($_SESSION['role'], $allowedRoles);
}

// Check if user can edit any article (not just their own)
function canEditAnyArticle() {
  if (!isLoggedIn()) return false;
  
  // Check if role column exists in session
  if(!isset($_SESSION['role'])) {
    return false;
  }
  
  $allowedRoles = ['admin', 'editor'];
  return in_array($_SESSION['role'], $allowedRoles);
}

// Check if user can delete any article (not just their own)
function canDeleteAnyArticle() {
  if (!isLoggedIn()) return false;
  
  // Check if role column exists in session
  if(!isset($_SESSION['role'])) {
    return false;
  }
  
  $allowedRoles = ['admin', 'editor'];
  return in_array($_SESSION['role'], $allowedRoles);
}

// Get all team members (users with special roles)
function getTeamMembers($pdo) {
  // Check if role column exists
  $stmt = $pdo->prepare("SHOW COLUMNS FROM users LIKE 'role'");
  $stmt->execute();
  if(!$stmt->fetch()) {
    return [];
  }
  
  $stmt = $pdo->prepare("SELECT * FROM users WHERE role IN ('admin', 'editor', 'writer') ORDER BY role, username");
  $stmt->execute();
  return $stmt->fetchAll();
}

// Get role display name
function getRoleDisplayName($role) {
  $roles = [
    'admin' => 'Administrator',
    'editor' => 'Editor',
    'writer' => 'Writer',
    'user' => 'Member'
  ];
  
  return isset($roles[$role]) ? $roles[$role] : 'Member';
}

// Get role badge class
function getRoleBadgeClass($role) {
  $classes = [
    'admin' => 'bg-accent-600 text-white',
    'editor' => 'bg-primary-600 text-white',
    'writer' => 'bg-primary-400 text-dark-900',
    'user' => 'bg-dark-700 text-dark-300'
  ];
  
  return isset($classes[$role]) ? $classes[$role] : 'bg-dark-700 text-dark-300';
}

