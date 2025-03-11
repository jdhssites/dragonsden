<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
session_start();

// Check if user is logged in
if(!isLoggedIn()) {
  $_SESSION['redirect_after_login'] = 'profile.php';
  setFlashMessage('error', 'Please login to view your profile.');
  header('Location: login.php');
  exit;
}

// Get user details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if(!$user) {
  setFlashMessage('error', 'User not found.');
  header('Location: index.php');
  exit;
}

// Get user stats
$stmt = $pdo->prepare("SELECT COUNT(*) as article_count FROM articles WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$article_count = $stmt->fetch()['article_count'];

$stmt = $pdo->prepare("SELECT SUM(views) as total_views FROM articles WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$total_views = $stmt->fetch()['total_views'] ?: 0;

$errors = [];
$success = false;

// Process profile update
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
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
  if($username !== $user['username'] || $email !== $user['email']) {
      $stmt = $pdo->prepare("SELECT * FROM users WHERE (username = ? OR email = ?) AND id != ?");
      $stmt->execute([$username, $email, $_SESSION['user_id']]);
      $existing_user = $stmt->fetch();
      
      if($existing_user) {
          if($existing_user['username'] === $username) {
              $errors[] = "Username already taken";
          }
          if($existing_user['email'] === $email) {
              $errors[] = "Email already registered";
          }
      }
  }
  
  // If no errors, update profile
  if(empty($errors)) {
      $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, bio = ? WHERE id = ?");
      
      if($stmt->execute([$username, $email, $bio, $_SESSION['user_id']])) {
          $_SESSION['username'] = $username;
          $user['username'] = $username;
          $user['email'] = $email;
          $user['bio'] = $bio;
          $success = true;
      } else {
          $errors[] = "Failed to update profile. Please try again.";
      }
  }
}

// Process password change
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
  $current_password = $_POST['current_password'];
  $new_password = $_POST['new_password'];
  $confirm_password = $_POST['confirm_password'];
  
  // Validation
  if(empty($current_password)) {
      $errors[] = "Current password is required";
  } elseif(!password_verify($current_password, $user['password'])) {
      $errors[] = "Current password is incorrect";
  }
  
  if(empty($new_password)) {
      $errors[] = "New password is required";
  } elseif(strlen($new_password) < 6) {
      $errors[] = "New password must be at least 6 characters";
  }
  
  if($new_password !== $confirm_password) {
      $errors[] = "New passwords do not match";
  }
  
  // If no errors, update password
  if(empty($errors)) {
      $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
      $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
      
      if($stmt->execute([$hashed_password, $_SESSION['user_id']])) {
          $success = true;
      } else {
          $errors[] = "Failed to update password. Please try again.";
      }
  }
}
?>

<?php include 'includes/header.php'; ?>

<main class="flex-grow py-12 bg-dark-950">
  <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="mb-8">
          <h1 class="text-3xl font-serif font-bold text-white">Your Profile</h1>
          <p class="mt-2 text-dark-400">Manage your account settings and view your stats</p>
      </div>
      
      <?php if($success): ?>
          <div class="mb-6 bg-green-900 border border-green-700 text-green-100 rounded-lg p-4">
              <div class="flex">
                  <div class="flex-shrink-0">
                      <i class="fas fa-check-circle text-green-400"></i>
                  </div>
                  <div class="ml-3">
                      <p class="text-sm">Profile updated successfully!</p>
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
      
      <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
          <!-- Sidebar -->
          <div class="lg:col-span-1">
              <div class="bg-dark-900 rounded-xl shadow-md overflow-hidden">
                  <div class="p-6 text-center">
                      <img src="https://www.gravatar.com/avatar/<?= md5(strtolower(trim($user['email']))) ?>?s=150&d=mp" 
                           class="rounded-full mx-auto h-32 w-32 object-cover mb-4" alt="Profile Picture">
                      <h2 class="text-xl font-bold text-white"><?= htmlspecialchars($user['username']) ?></h2>
                      <p class="text-dark-400 mt-1">Member since <?= date('M Y', strtotime($user['created_at'])) ?></p>
                      
                      <div class="mt-6 grid grid-cols-2 gap-4 text-center">
                          <div class="bg-dark-800 rounded-lg p-3">
                              <span class="block text-2xl font-bold text-white"><?= $article_count ?></span>
                              <span class="text-sm text-dark-400">Articles</span>
                          </div>
                          <div class="bg-dark-800 rounded-lg p-3">
                              <span class="block text-2xl font-bold text-white"><?= $total_views ?></span>
                              <span class="text-sm text-dark-400">Views</span>
                          </div>
                      </div>
                      
                      <div class="mt-6">
                          <a href="my-articles.php" class="inline-flex items-center justify-center w-full px-4 py-2 border border-dark-700 shadow-sm text-sm font-medium rounded-md text-dark-300 bg-dark-800 hover:bg-dark-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 focus:ring-offset-dark-900 transition-colors">
                              <i class="fas fa-file-alt mr-2"></i> My Articles
                          </a>
                      </div>
                  </div>
              </div>
          </div>
          
          <!-- Main Content -->
          <div class="lg:col-span-3 space-y-8">
              <!-- Profile Information -->
              <div class="bg-dark-900 rounded-xl shadow-md overflow-hidden">
                  <div class="px-6 py-5 border-b border-dark-700">
                      <h3 class="text-lg font-medium text-white">Profile Information</h3>
                      <p class="mt-1 text-sm text-dark-400">Update your account's profile information</p>
                  </div>
                  <div class="px-6 py-6">
                      <form action="profile.php" method="POST">
                          <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                              <div>
                                  <label for="username" class="block text-sm font-medium text-white">Username</label>
                                  <input type="text" name="username" id="username" value="<?= htmlspecialchars($user['username']) ?>" required class="mt-1 block w-full rounded-md border-dark-700 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                              </div>
                              
                              <div>
                                  <label for="email" class="block text-sm font-medium text-white">Email address</label>
                                  <input type="email" name="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" required class="mt-1 block w-full rounded-md border-dark-700 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                              </div>
                              
                              <div class="sm:col-span-2">
                                  <label for="bio" class="block text-sm font-medium text-white">Bio (Optional)</label>
                                  <textarea name="bio" id="bio" rows="4" class="mt-1 block w-full rounded-md border-dark-700 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
                              </div>
                          </div>
                          
                          <div class="mt-6 flex justify-end">
                              <button type="submit" name="update_profile" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 focus:ring-offset-dark-900 transition-colors">
                                  Update Profile
                              </button>
                          </div>
                      </form>
                  </div>
              </div>
              
              <!-- Change Password -->
              <div class="bg-dark-900 rounded-xl shadow-md overflow-hidden">
                  <div class="px-6 py-5 border-b border-dark-700">
                      <h3 class="text-lg font-medium text-white">Change Password</h3>
                      <p class="mt-1 text-sm text-dark-400">Ensure your account is using a secure password</p>
                  </div>
                  <div class="px-6 py-6">
                      <form action="profile.php" method="POST">
                          <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                              <div class="sm:col-span-2">
                                  <label for="current_password" class="block text-sm font-medium text-white">Current Password</label>
                                  <input type="password" name="current_password" id="current_password" required class="mt-1 block w-full rounded-md border-dark-700 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                              </div>
                              
                              <div>
                                  <label for="new_password" class="block text-sm font-medium text-white">New Password</label>
                                  <input type="password" name="new_password" id="new_password" required class="mt-1 block w-full rounded-md border-dark-700 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                  
                                  <div class="mt-2">
                                      <div class="h-2 bg-dark-700 rounded-full overflow-hidden">
                                          <div id="password-strength" class="h-full bg-red-500 rounded-full" style="width: 0%"></div>
                                      </div>
                                      <p id="password-text" class="text-xs text-red-500 mt-1">Password strength</p>
                                  </div>
                              </div>
                              
                              <div>
                                  <label for="confirm_password" class="block text-sm font-medium text-white">Confirm New Password</label>
                                  <input type="password" name="confirm_password" id="confirm_password" required class="mt-1 block w-full rounded-md border-dark-700 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                              </div>
                          </div>
                          
                          <div class="mt-6 flex justify-end">
                              <button type="submit" name="change_password" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 focus:ring-offset-dark-900 transition-colors">
                                  Change Password
                              </button>
                          </div>
                      </form>
                  </div>
              </div>
          </div>
      </div>
  </div>
</main>

<?php include 'includes/footer.php'; ?>

