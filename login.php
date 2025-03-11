<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
session_start();

// Redirect if already logged in
if(isLoggedIn()) {
  header('Location: index.php');
  exit;
}

$errors = [];
$email = '';

// Process form submission
if($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email']);
  $password = $_POST['password'];
  
  // Validation
  if(empty($email)) {
      $errors[] = "Email is required";
  }
  
  if(empty($password)) {
      $errors[] = "Password is required";
  }
  
  // If no validation errors, attempt login
  if(empty($errors)) {
      $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
      $stmt->execute([$email]);
      $user = $stmt->fetch();
      
      if($user && password_verify($password, $user['password'])) {
          // Set session and redirect
          $_SESSION['user_id'] = $user['id'];
          $_SESSION['username'] = $user['username'];
          $_SESSION['email'] = $user['email'];
          
          // Set role if it exists in the database
          if(isset($user['role'])) {
              $_SESSION['role'] = $user['role'];
          }
          
          // Remember me functionality
          if(isset($_POST['remember_me'])) {
              $token = bin2hex(random_bytes(32));
              $expires = time() + 60 * 60 * 24 * 30; // 30 days
              
              $stmt = $pdo->prepare("UPDATE users SET remember_token = ?, token_expires = ? WHERE id = ?");
              $stmt->execute([$token, date('Y-m-d H:i:s', $expires), $user['id']]);
              
              setcookie('remember_token', $token, $expires, '/', '', false, true);
              setcookie('user_id', $user['id'], $expires, '/', '', false, true);
          }
          
          // Redirect to intended page or home
          $redirect = isset($_SESSION['redirect_after_login']) ? $_SESSION['redirect_after_login'] : 'index.php';
          unset($_SESSION['redirect_after_login']);
          
          setFlashMessage('success', 'Login successful! Welcome back, ' . $user['username'] . '.');
          header('Location: ' . $redirect);
          exit;
      } else {
          $errors[] = "Invalid email or password";
      }
  }
}
?>

<?php include 'includes/header.php'; ?>

<main class="flex-grow py-12 bg-gray-50">
<div class="max-w-md mx-auto px-4 sm:px-6">
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-8 sm:p-10">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-gray-900">Welcome Back</h1>
                <p class="mt-2 text-sm text-gray-600">
                    Sign in to your account to continue
                </p>
            </div>
            
            <?php if(!empty($errors)): ?>
                <div class="mb-6 bg-red-50 border border-red-200 text-red-800 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">There were errors with your submission</h3>
                            <div class="mt-2 text-sm text-red-700">
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
            
            <form action="login.php" method="POST" class="space-y-6">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
                    <div class="mt-1">
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                    </div>
                </div>
                
                <div>
                    <div class="flex items-center justify-between">
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <a href="forgot-password.php" class="text-xs font-medium text-primary-600 hover:text-primary-500">
                            Forgot your password?
                        </a>
                    </div>
                    <div class="mt-1">
                        <input type="password" id="password" name="password" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                    </div>
                </div>
                
                <div class="flex items-center">
                    <input id="remember_me" name="remember_me" type="checkbox" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                    <label for="remember_me" class="ml-2 block text-sm text-gray-700">
                        Remember me
                    </label>
                </div>
                
                <div>
                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                        Sign in
                    </button>
                </div>
            </form>
            
            <div class="mt-6 text-center text-sm">
                <p>Having trouble logging in? <a href="login-debug.php" class="font-medium text-primary-600 hover:text-primary-500">Use the login debugging tool</a></p>
            </div>
        </div>
    </div>
    
    <div class="mt-6 text-center">
        <p class="text-sm text-gray-600">
            Don't have an account? 
            <a href="register.php" class="font-medium text-primary-600 hover:text-primary-500">
                Create an account
            </a>
        </p>
    </div>
</div>
</main>

<?php include 'includes/footer.php'; ?>

