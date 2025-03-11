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

// Process user role update
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_role'])) {
  $user_id = (int)$_POST['user_id'];
  $new_role = $_POST['role'];
  
  // Validate role
  $valid_roles = ['admin', 'editor', 'writer', 'user'];
  if(!in_array($new_role, $valid_roles)) {
      $errors[] = "Invalid role selected.";
  }
  
  // Don't allow changing own role (to prevent admin from demoting themselves)
  if($user_id === (int)$_SESSION['user_id']) {
      $errors[] = "You cannot change your own role.";
  }
  
  if(empty($errors)) {
      $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
      if($stmt->execute([$new_role, $user_id])) {
          $success = 'User role updated successfully.';
      } else {
          $errors[] = "Failed to update user role.";
      }
  }
}

// Process user update
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
  $user_id = (int)$_POST['user_id'];
  $username = trim($_POST['username']);
  $email = trim($_POST['email']);
  $bio = trim($_POST['bio']);
  
  // Validation
  if(empty($username)) {
      $errors[] = "Username is required";
  } elseif(strlen($username) < 3 || strlen($username) > 20) {
      $errors[] = "Username must be between 3 and 20 characters";
  }
  
  if(empty($email)) {
      $errors[] = "Email is required";
  } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errors[] = "Invalid email format";
  }
  
  // Check if username or email already exists (excluding current user)
  $stmt = $pdo->prepare("SELECT * FROM users WHERE (username = ? OR email = ?) AND id != ?");
  $stmt->execute([$username, $email, $user_id]);
  $existing_user = $stmt->fetch();
  
  if($existing_user) {
      if($existing_user['username'] === $username) {
          $errors[] = "Username already taken";
      }
      if($existing_user['email'] === $email) {
          $errors[] = "Email already registered";
      }
  }
  
  if(empty($errors)) {
      $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, bio = ? WHERE id = ?");
      if($stmt->execute([$username, $email, $bio, $user_id])) {
          $success = 'User details updated successfully.';
      } else {
          $errors[] = "Failed to update user details.";
      }
  }
}

// Process user password reset
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_password'])) {
  $user_id = (int)$_POST['user_id'];
  $new_password = $_POST['new_password'];
  
  // Validation
  if(empty($new_password)) {
      $errors[] = "New password is required";
  } elseif(strlen($new_password) < 6) {
      $errors[] = "New password must be at least 6 characters";
  }
  
  if(empty($errors)) {
      $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
      $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
      if($stmt->execute([$hashed_password, $user_id])) {
          $success = 'User password reset successfully.';
      } else {
          $errors[] = "Failed to reset user password.";
      }
  }
}

// Process user deletion
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
  $user_id = (int)$_POST['user_id'];
  
  // Don't allow deleting own account
  if($user_id === (int)$_SESSION['user_id']) {
      $errors[] = "You cannot delete your own account.";
  } else {
      // Begin transaction
      $pdo->beginTransaction();
      
      try {
          // Delete user's articles
          $stmt = $pdo->prepare("DELETE FROM articles WHERE user_id = ?");
          $stmt->execute([$user_id]);
          
          // Delete user
          $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
          $stmt->execute([$user_id]);
          
          // Commit transaction
          $pdo->commit();
          $success = 'User and all their content deleted successfully.';
      } catch (Exception $e) {
          // Rollback transaction on error
          $pdo->rollBack();
          $errors[] = "Failed to delete user: " . $e->getMessage();
      }
  }
}

// Get all users with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$role_filter = isset($_GET['role']) ? trim($_GET['role']) : '';

// Build the query
$query = "SELECT * FROM users";
$count_query = "SELECT COUNT(*) as total FROM users";
$params = [];

if (!empty($search)) {
    $query .= " WHERE (username LIKE ? OR email LIKE ?)";
    $count_query .= " WHERE (username LIKE ? OR email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    
    if (!empty($role_filter)) {
        $query .= " AND role = ?";
        $count_query .= " AND role = ?";
        $params[] = $role_filter;
    }
} elseif (!empty($role_filter)) {
    $query .= " WHERE role = ?";
    $count_query .= " WHERE role = ?";
    $params[] = $role_filter;
}

// Add ordering
$query .= " ORDER BY role, username";

// Add pagination
$query .= " LIMIT ? OFFSET ?";
$params[] = $per_page;
$params[] = $offset;

// Get total users count
$stmt = $pdo->prepare($count_query);
$stmt->execute(empty($params) ? [] : array_slice($params, 0, count($params) - 2));
$total_users = $stmt->fetch()['total'];
$total_pages = ceil($total_users / $per_page);

// Get users
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$users = $stmt->fetchAll();
?>

<?php include '../includes/header.php'; ?>

<main class="flex-grow py-10 bg-dark-950">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
          <h1 class="text-3xl font-serif font-bold text-white mb-4 md:mb-0">Manage Users</h1>
          <div class="flex flex-col sm:flex-row gap-4">
              <a href="../admin/index.php" class="inline-flex items-center px-4 py-2 border border-dark-700 shadow-sm text-sm font-medium rounded-md text-dark-300 bg-dark-800 hover:bg-dark-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 focus:ring-offset-dark-950 transition-colors">
                  <i class="fas fa-arrow-left mr-2"></i> Back to Admin Dashboard
              </a>
              <button type="button" data-modal-toggle="createUserModal" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 focus:ring-offset-dark-950 transition-colors">
                  <i class="fas fa-user-plus mr-2"></i> Create New User
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
                      <h3 class="text-sm font-medium text-red-100">There were errors with your submission</h3>
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
      <div class="bg-dark-900 rounded-xl shadow-md overflow-hidden mb-6">
          <div class="p-6">
              <form action="manage-users.php" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div>
                      <label for="search" class="block text-sm font-medium text-white mb-1">Search Users</label>
                      <input type="text" id="search" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search by username or email" class="w-full rounded-md border-dark-700 bg-dark-800 py-2 px-3 text-white placeholder-dark-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                  </div>
                  
                  <div>
                      <label for="role" class="block text-sm font-medium text-white mb-1">Filter by Role</label>
                      <select id="role" name="role" class="w-full rounded-md border-dark-700 bg-dark-800 py-2 px-3 text-white focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                          <option value="">All Roles</option>
                          <option value="admin" <?= $role_filter === 'admin' ? 'selected' : '' ?>>Administrators</option>
                          <option value="editor" <?= $role_filter === 'editor' ? 'selected' : '' ?>>Editors</option>
                          <option value="writer" <?= $role_filter === 'writer' ? 'selected' : '' ?>>Writers</option>
                          <option value="user" <?= $role_filter === 'user' ? 'selected' : '' ?>>Regular Users</option>
                      </select>
                  </div>
                  
                  <div class="flex items-end gap-2">
                      <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 focus:ring-offset-dark-900 transition-colors">
                          <i class="fas fa-search mr-2"></i> Search
                      </button>
                      
                      <a href="manage-users.php" class="inline-flex items-center px-4 py-2 border border-dark-700 shadow-sm text-sm font-medium rounded-md text-dark-300 bg-dark-800 hover:bg-dark-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 focus:ring-offset-dark-950 transition-colors">
                          <i class="fas fa-times mr-2"></i> Clear
                      </a>
                  </div>
              </form>
          </div>
      </div>
      
      <!-- Users Table -->
      <div class="bg-dark-900 rounded-xl shadow-md overflow-hidden">
          <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-dark-700">
                  <thead class="bg-dark-800">
                      <tr>
                          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-dark-300 uppercase tracking-wider">User</th>
                          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-dark-300 uppercase tracking-wider">Email</th>
                          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-dark-300 uppercase tracking-wider">Role</th>
                          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-dark-300 uppercase tracking-wider">Joined</th>
                          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-dark-300 uppercase tracking-wider">Actions</th>
                      </tr>
                  </thead>
                  <tbody class="bg-dark-900 divide-y divide-dark-800">
                      <?php if(empty($users)): ?>
                          <tr>
                              <td colspan="5" class="px-6 py-4 text-center text-dark-300">
                                  No users found. <?= !empty($search) || !empty($role_filter) ? 'Try adjusting your search filters.' : '' ?>
                              </td>
                          </tr>
                      <?php else: ?>
                          <?php foreach($users as $user): ?>
                              <tr class="hover:bg-dark-800 transition-colors">
                                  <td class="px-6 py-4 whitespace-nowrap">
                                      <div class="flex items-center">
                                          <div class="flex-shrink-0 h-10 w-10">
                                              <img class="h-10 w-10 rounded-full" src="https://www.gravatar.com/avatar/<?= md5(strtolower(trim($user['email']))) ?>?s=100&d=mp" alt="<?= htmlspecialchars($user['username']) ?>">
                                          </div>
                                          <div class="ml-4">
                                              <div class="text-sm font-medium text-white"><?= htmlspecialchars($user['username']) ?></div>
                                              <?php if($user['id'] == $_SESSION['user_id']): ?>
                                                  <div class="text-xs text-primary-400">(You)</div>
                                              <?php endif; ?>
                                          </div>
                                      </div>
                                  </td>
                                  <td class="px-6 py-4 whitespace-nowrap text-sm text-dark-300">
                                      <?= htmlspecialchars($user['email']) ?>
                                  </td>
                                  <td class="px-6 py-4 whitespace-nowrap">
                                      <span class="px-2 py-1 inline-flex text-xs leading-5 font-medium rounded-full <?= getRoleBadgeClass($user['role']) ?>">
                                          <?= getRoleDisplayName($user['role']) ?>
                                      </span>
                                  </td>
                                  <td class="px-6 py-4 whitespace-nowrap text-sm text-dark-300">
                                      <?= formatDate($user['created_at']) ?>
                                  </td>
                                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                      <div class="flex space-x-2">
                                          <button type="button" class="text-primary-400 hover:text-primary-300 transition-colors" data-modal-toggle="viewUserModal<?= $user['id'] ?>" title="View User">
                                              <i class="fas fa-eye"></i>
                                          </button>
                                          
                                          <button type="button" class="text-primary-400 hover:text-primary-300 transition-colors" data-modal-toggle="editUserModal<?= $user['id'] ?>" title="Edit User">
                                              <i class="fas fa-edit"></i>
                                          </button>
                                          
                                          <?php if($user['id'] != $_SESSION['user_id']): ?>
                                              <button type="button" class="text-primary-400 hover:text-primary-300 transition-colors" data-modal-toggle="editRoleModal<?= $user['id'] ?>" title="Change Role">
                                                  <i class="fas fa-user-tag"></i>
                                              </button>
                                              
                                              <button type="button" class="text-primary-400 hover:text-primary-300 transition-colors" data-modal-toggle="resetPasswordModal<?= $user['id'] ?>" title="Reset Password">
                                                  <i class="fas fa-key"></i>
                                              </button>
                                              
                                              <button type="button" class="text-accent-500 hover:text-accent-400 transition-colors" data-modal-toggle="deleteUserModal<?= $user['id'] ?>" title="Delete User">
                                                  <i class="fas fa-trash-alt"></i>
                                              </button>
                                          <?php endif; ?>
                                      </div>
                                      
                                      <!-- View User Modal -->
                                      <div id="viewUserModal<?= $user['id'] ?>" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 w-full md:inset-0 h-modal md:h-full justify-center items-center">
                                          <div class="relative p-4 w-full max-w-2xl h-full md:h-auto">
                                              <div class="relative bg-dark-800 rounded-lg shadow">
                                                  <div class="flex justify-between items-center p-5 rounded-t border-b border-dark-700">
                                                      <h3 class="text-xl font-medium text-white">
                                                          User Profile: <?= htmlspecialchars($user['username']) ?>
                                                      </h3>
                                                      <button type="button" class="text-dark-400 bg-transparent hover:bg-dark-700 hover:text-white rounded-lg text-sm p-1.5 ml-auto inline-flex items-center" data-modal-toggle="viewUserModal<?= $user['id'] ?>">
                                                          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                                      </button>
                                                  </div>
                                                  <div class="p-6 space-y-6">
                                                      <div class="flex flex-col sm:flex-row">
                                                          <div class="sm:w-1/3 flex justify-center mb-4 sm:mb-0">
                                                              <img class="h-32 w-32 rounded-full" src="https://www.gravatar.com/avatar/<?= md5(strtolower(trim($user['email']))) ?>?s=256&d=mp" alt="<?= htmlspecialchars($user['username']) ?>">
                                                          </div>
                                                          <div class="sm:w-2/3">
                                                              <h4 class="text-lg font-medium text-white mb-2"><?= htmlspecialchars($user['username']) ?></h4>
                                                              <p class="text-dark-300 mb-2">
                                                                  <span class="font-medium text-dark-200">Email:</span> <?= htmlspecialchars($user['email']) ?>
                                                              </p>
                                                              <p class="text-dark-300 mb-2">
                                                                  <span class="font-medium text-dark-200">Role:</span> 
                                                                  <span class="px-2 py-1 inline-flex text-xs leading-5 font-medium rounded-full <?= getRoleBadgeClass($user['role']) ?>">
                                                                      <?= getRoleDisplayName($user['role']) ?>
                                                                  </span>
                                                              </p>
                                                              <p class="text-dark-300 mb-2">
                                                                  <span class="font-medium text-dark-200">Joined:</span> <?= formatDate($user['created_at']) ?>
                                                              </p>
                                                              
                                                              <?php
                                                              // Get article count for this user
                                                              $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM articles WHERE user_id = ?");
                                                              $stmt->execute([$user['id']]);
                                                              $article_count = $stmt->fetch()['count'];
                                                              
                                                              // Get total views for this user's articles
                                                              $stmt = $pdo->prepare("SELECT SUM(views) as total_views FROM articles WHERE user_id = ?");
                                                              $stmt->execute([$user['id']]);
                                                              $total_views = $stmt->fetch()['total_views'] ?: 0;
                                                              ?>
                                                              
                                                              <p class="text-dark-300 mb-2">
                                                                  <span class="font-medium text-dark-200">Articles:</span> <?= $article_count ?>
                                                              </p>
                                                              <p class="text-dark-300 mb-2">
                                                                  <span class="font-medium text-dark-200">Total Views:</span> <?= $total_views ?>
                                                              </p>
                                                          </div>
                                                      </div>
                                                      
                                                      <?php if(!empty($user['bio'])): ?>
                                                          <div class="mt-4">
                                                              <h4 class="text-lg font-medium text-white mb-2">Bio</h4>
                                                              <p class="text-dark-300"><?= nl2br(htmlspecialchars($user['bio'])) ?></p>
                                                          </div>
                                                      <?php endif; ?>
                                                  </div>
                                                  <div class="flex items-center p-6 space-x-2 rounded-b border-t border-dark-700">
                                                      <a href="../author.php?id=<?= $user['id'] ?>" class="text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                                                          View Public Profile
                                                      </a>
                                                      <button type="button" data-modal-toggle="viewUserModal<?= $user['id'] ?>" class="text-dark-300 bg-dark-700 hover:bg-dark-600 focus:ring-4 focus:outline-none focus:ring-dark-600 rounded-lg border border-dark-600 text-sm font-medium px-5 py-2.5 hover:text-white focus:z-10">
                                                          Close
                                                      </button>
                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                      
                                      <!-- Edit User Modal -->
                                      <div id="editUserModal<?= $user['id'] ?>" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 w-full md:inset-0 h-modal md:h-full justify-center items-center">
                                          <div class="relative p-4 w-full max-w-2xl h-full md:h-auto">
                                              <div class="relative bg-dark-800 rounded-lg shadow">
                                                  <div class="flex justify-between items-center p-5 rounded-t border-b border-dark-700">
                                                      <h3 class="text-xl font-medium text-white">
                                                          Edit User: <?= htmlspecialchars($user['username']) ?>
                                                      </h3>
                                                      <button type="button" class="text-dark-400 bg-transparent hover:bg-dark-700 hover:text-white rounded-lg text-sm p-1.5 ml-auto inline-flex items-center" data-modal-toggle="editUserModal<?= $user['id'] ?>">
                                                          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                                      </button>
                                                  </div>
                                                  <form action="manage-users.php" method="POST">
                                                      <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                      <div class="p-6 space-y-6">
                                                          <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                                              <div>
                                                                  <label for="username<?= $user['id'] ?>" class="block text-sm font-medium text-white">Username</label>
                                                                  <input type="text" id="username<?= $user['id'] ?>" name="username" value="<?= htmlspecialchars($user['username']) ?>" required class="mt-1 block w-full rounded-md border-dark-700 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm bg-dark-700 text-white">
                                                              </div>
                                                              
                                                              <div>
                                                                  <label for="email<?= $user['id'] ?>" class="block text-sm font-medium text-white">Email address</label>
                                                                  <input type="email" id="email<?= $user['id'] ?>" name="email" value="<?= htmlspecialchars($user['email']) ?>" required class="mt-1 block w-full rounded-md border-dark-700 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm bg-dark-700 text-white">
                                                              </div>
                                                              
                                                              <div class="sm:col-span-2">
                                                                  <label for="bio<?= $user['id'] ?>" class="block text-sm font-medium text-white">Bio (Optional)</label>
                                                                  <textarea id="bio<?= $user['id'] ?>" name="bio" rows="4" class="mt-1 block w-full rounded-md border-dark-700 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm bg-dark-700 text-white"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
                                                              </div>
                                                          </div>
                                                      </div>
                                                      <div class="flex items-center p-6 space-x-2 rounded-b border-t border-dark-700">
                                                          <button type="submit" name="update_user" class="text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                                                              Save Changes
                                                          </button>
                                                          <button type="button" data-modal-toggle="editUserModal<?= $user['id'] ?>" class="text-dark-300 bg-dark-700 hover:bg-dark-600 focus:ring-4 focus:outline-none focus:ring-dark-600 rounded-lg border border-dark-600 text-sm font-medium px-5 py-2.5 hover:text-white focus:z-10">
                                                              Cancel
                                                          </button>
                                                      </div>
                                                  </form>
                                              </div>
                                          </div>
                                      </div>
                                      
                                      <!-- Edit Role Modal -->
                                      <div id="editRoleModal<?= $user['id'] ?>" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 w-full md:inset-0 h-modal md:h-full justify-center items-center">
                                          <div class="relative p-4 w-full max-w-md h-full md:h-auto">
                                              <div class="relative bg-dark-800 rounded-lg shadow">
                                                  <div class="flex justify-between items-center p-5 rounded-t border-b border-dark-700">
                                                      <h3 class="text-xl font-medium text-white">
                                                          Change Role for <?= htmlspecialchars($user['username']) ?>
                                                      </h3>
                                                      <button type="button" class="text-dark-400 bg-transparent hover:bg-dark-700 hover:text-white rounded-lg text-sm p-1.5 ml-auto inline-flex items-center" data-modal-toggle="editRoleModal<?= $user['id'] ?>">
                                                          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                                      </button>
                                                  </div>
                                                  <form action="manage-users.php" method="POST">
                                                      <div class="p-6 space-y-6">
                                                          <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                          <div>
                                                              <label for="role<?= $user['id'] ?>" class="block text-sm font-medium text-white mb-2">Select Role</label>
                                                              <select id="role<?= $user['id'] ?>" name="role" class="bg-dark-700 border border-dark-600 text-white text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                                                                  <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>Member</option>
                                                                  <option value="writer" <?= $user['role'] === 'writer' ? 'selected' : '' ?>>Writer</option>
                                                                  <option value="editor" <?= $user['role'] === 'editor' ? 'selected' : '' ?>>Editor</option>
                                                                  <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Administrator</option>
                                                              </select>
                                                          </div>
                                                          <div class="text-sm text-dark-300">
                                                              <p><strong class="text-white">Role Permissions:</strong></p>
                                                              <ul class="list-disc pl-5 mt-2 space-y-1">
                                                                  <li><strong>Member:</strong> Can comment on articles</li>
                                                                  <li><strong>Writer:</strong> Can publish articles</li>
                                                                  <li><strong>Editor:</strong> Can publish, edit, and delete any articles</li>
                                                                  <li><strong>Administrator:</strong> Full access to all features including user management</li>
                                                              </ul>
                                                          </div>
                                                      </div>
                                                      <div class="flex items-center p-6 space-x-2 rounded-b border-t border-dark-700">
                                                          <button type="submit" name="update_role" class="text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Save Changes</button>
                                                          <button type="button" data-modal-toggle="editRoleModal<?= $user['id'] ?>" class="text-dark-300 bg-dark-700 hover:bg-dark-600 focus:ring-4 focus:outline-none focus:ring-dark-600 rounded-lg border border-dark-600 text-sm font-medium px-5 py-2.5 hover:text-white focus:z-10">Cancel</button>
                                                      </div>
                                                  </form>
                                              </div>
                                          </div>
                                      </div>
                                      
                                      <!-- Reset Password Modal -->
                                      <div id="resetPasswordModal<?= $user['id'] ?>" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 w-full md:inset-0 h-modal md:h-full justify-center items-center">
                                          <div class="relative p-4 w-full max-w-md h-full md:h-auto">
                                              <div class="relative bg-dark-800 rounded-lg shadow">
                                                  <div class="flex justify-between items-center p-5 rounded-t border-b border-dark-700">
                                                      <h3 class="text-xl font-medium text-white">
                                                          Reset Password for <?= htmlspecialchars($user['username']) ?>
                                                      </h3>
                                                      <button type="button" class="text-dark-400 bg-transparent hover:bg-dark-700 hover:text-white rounded-lg text-sm p-1.5 ml-auto inline-flex items-center" data-modal-toggle="resetPasswordModal<?= $user['id'] ?>">
                                                          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                                      </button>
                                                  </div>
                                                  <form action="manage-users.php" method="POST">
                                                      <div class="p-6 space-y-6">
                                                          <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                          <div>
                                                              <label for="new_password<?= $user['id'] ?>" class="block text-sm font-medium text-white mb-2">New Password</label>
                                                              <input type="password" id="new_password<?= $user['id'] ?>" name="new_password" required class="bg-dark-700 border border-dark-600 text-white text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                                                          </div>
                                                          <div class="text-sm text-dark-300">
                                                              <p class="text-accent-400"><i class="fas fa-exclamation-triangle mr-2"></i> This action will reset the user's password. They will need to use this new password to log in.</p>
                                                          </div>
                                                      </div>
                                                      <div class="flex items-center p-6 space-x-2 rounded-b border-t border-dark-700">
                                                          <button type="submit" name="reset_password" class="text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Reset Password</button>
                                                          <button type="button" data-modal-toggle="resetPasswordModal<?= $user['id'] ?>" class="text-dark-300 bg-dark-700 hover:bg-dark-600 focus:ring-4 focus:outline-none focus:ring-dark-600 rounded-lg border border-dark-600 text-sm font-medium px-5 py-2.5 hover:text-white focus:z-10">Cancel</button>
                                                      </div>
                                                  </form>
                                              </div>
                                          </div>
                                      </div>
                                      
                                      <!-- Delete User Modal -->
                                      <div id="deleteUserModal<?= $user['id'] ?>" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 w-full md:inset-0 h-modal md:h-full justify-center items-center">
                                          <div class="relative p-4 w-full max-w-md h-full md:h-auto">
                                              <div class="relative bg-dark-800 rounded-lg shadow">
                                                  <div class="flex justify-between items-center p-5 rounded-t border-b border-dark-700">
                                                      <h3 class="text-xl font-medium text-white">
                                                          Delete User
                                                      </h3>
                                                      <button type="button" class="text-dark-400 bg-transparent hover:bg-dark-700 hover:text-white rounded-lg text-sm p-1.5 ml-auto inline-flex items-center" data-modal-toggle="deleteUserModal<?= $user['id'] ?>">
                                                          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                                      </button>
                                                  </div>
                                                  <div class="p-6 text-center">
                                                      <svg class="mx-auto mb-4 w-14 h-14 text-accent-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                      <h3 class="mb-5 text-lg font-normal text-dark-300">
                                                          Are you sure you want to delete <span class="font-semibold text-white"><?= htmlspecialchars($user['username']) ?></span>?
                                                      </h3>
                                                      <p class="mb-5 text-sm text-dark-400">
                                                          This will permanently delete the user account and all associated content. This action cannot be undone.
                                                      </p>
                                                      <form action="manage-users.php" method="POST">
                                                          <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                          <button type="submit" name="delete_user" class="text-white bg-accent-600 hover:bg-accent-800 focus:ring-4 focus:outline-none focus:ring-accent-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
                                                              Yes, delete user
                                                          </button>
                                                          <button type="button" data-modal-toggle="deleteUserModal<?= $user['id'] ?>" class="text-dark-300 bg-dark-700 hover:bg-dark-600 focus:ring-4 focus:outline-none focus:ring-dark-600 rounded-lg border border-dark-600 text-sm font-medium px-5 py-2.5 hover:text-white focus:z-10">
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
                      <?php endif; ?>
                  </tbody>
              </table>
          </div>
      </div>
      
      <!-- Pagination -->
      <?php if($total_pages > 1): ?>
          <div class="mt-8 flex justify-center">
              <nav class="inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                  <?php if($page > 1): ?>
                      <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&role=<?= urlencode($role_filter) ?>" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-dark-700 bg-dark-800 text-sm font-medium text-dark-300 hover:bg-dark-700 hover:text-white">
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
                          <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&role=<?= urlencode($role_filter) ?>" class="relative inline-flex items-center px-4 py-2 border border-dark-700 bg-dark-800 text-sm font-medium text-dark-300 hover:bg-dark-700 hover:text-white">
                              <?= $i ?>
                          </a>
                      <?php endif; ?>
                  <?php endfor; ?>
                  
                  <?php if($page < $total_pages): ?>
                      <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&role=<?= urlencode($role_filter) ?>" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-dark-700 bg-dark-800 text-sm font-medium text-dark-300 hover:bg-dark-700 hover:text-white">
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
      
      <!-- Create User Modal -->
      <div id="createUserModal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 w-full md:inset-0 h-modal md:h-full justify-center items-center">
          <div class="relative p-4 w-full max-w-2xl h-full md:h-auto">
              <div class="relative bg-dark-800 rounded-lg shadow">
                  <div class="flex justify-between items-center p-5 rounded-t border-b border-dark-700">
                      <h3 class="text-xl font-medium text-white">
                          Create New User
                      </h3>
                      <button type="button" class="text-dark-400 bg-transparent hover:bg-dark-700 hover:text-white rounded-lg text-sm p-1.5 ml-auto inline-flex items-center" data-modal-toggle="createUserModal">
                          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                      </button>
                  </div>
                  <form action="manage-users.php" method="POST">
                      <div class="p-6 space-y-6">
                          <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                              <div>
                                  <label for="new_username" class="block text-sm font-medium text-white">Username</label>
                                  <input type="text" id="new_username" name="username" required class="mt-1 block w-full rounded-md border-dark-700 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm bg-dark-700 text-white">
                              </div>
                              
                              <div>
                                  <label for="new_email" class="block text-sm font-medium text-white">Email address</label>
                                  <input type="email" id="new_email" name="email" required class="mt-1 block w-full rounded-md border-dark-700 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm bg-dark-700 text-white">
                              </div>
                              
                              <div>
                                  <label for="new_password" class="block text-sm font-medium text-white">Password</label>
                                  <input type="password" id="new_password" name="new_password" required class="mt-1 block w-full rounded-md border-dark-700 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm bg-dark-700 text-white">
                              </div>
                              
                              <div>
                                  <label for="new_role" class="block text-sm font-medium text-white">Role</label>
                                  <select id="new_role" name="role" class="bg-dark-700 border border-dark-600 text-white text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                                      <option value="user">Member</option>
                                      <option value="writer">Writer</option>
                                      <option value="editor">Editor</option>
                                      <option value="admin">Administrator</option>
                                  </select>
                              </div>
                              
                              <div class="sm:col-span-2">
                                  <label for="new_bio" class="block text-sm font-medium text-white">Bio (Optional)</label>
                                  <textarea id="new_bio" name="bio" rows="4" class="mt-1 block w-full rounded-md border-dark-700 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm bg-dark-700 text-white"></textarea>
                              </div>
                          </div>
                      </div>
                      <div class="flex items-center p-6 space-x-2 rounded-b border-t border-dark-700">
                          <button type="submit" name="create_user" class="text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                              Create User
                          </button>
                          <button type="button" data-modal-toggle="createUserModal" class="text-dark-300 bg-dark-700 hover:bg-dark-600 focus:ring-4 focus:outline-none focus:ring-dark-600 rounded-lg border border-dark-600 text-sm font-medium px-5 py-2.5 hover:text-white focus:z-10">
                              Cancel
                          </button>
                      </div>
                  </form>
              </div>
          </div>
      </div>
  </div>
</main>

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

<?php include '../includes/footer.php'; ?>

