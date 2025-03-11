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
$username = '';
$email = '';

// Process form submission
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate input
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
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
    
    if(empty($password)) {
        $errors[] = "Password is required";
    } elseif(strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters";
    }
    
    if($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }
    
    // Check if username or email already exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    $user = $stmt->fetch();
    
    if($user) {
        if($user['username'] === $username) {
            $errors[] = "Username already taken";
        }
        if($user['email'] === $email) {
            $errors[] = "Email already registered";
        }
    }
    
    // If no errors, create user
    if(empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, created_at) VALUES (?, ?, ?, NOW())");
        if($stmt->execute([$username, $email, $hashed_password])) {
            // Set session and redirect
            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;
            
            setFlashMessage('success', 'Registration successful! Welcome to NewsHub.');
            header('Location: index.php');
            exit;
        } else {
            $errors[] = "Registration failed. Please try again.";
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
                    <h1 class="text-2xl font-bold text-gray-900">Create an Account</h1>
                    <p class="mt-2 text-sm text-gray-600">
                        Join our community and start sharing your stories
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
                
                <form action="register.php" method="POST" class="space-y-6">
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                        <div class="mt-1">
                            <input type="text" id="username" name="username" value="<?= htmlspecialchars($username) ?>" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                        </div>
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
                        <div class="mt-1">
                            <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                        </div>
                    </div>
                    
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <div class="mt-1">
                            <input type="password" id="password" name="password" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                        </div>
                    </div>
                    
                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                        <div class="mt-1">
                            <input type="password" id="confirm_password" name="confirm_password" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                        </div>
                    </div>
                    
                    <div>
                        <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                            Create Account
                        </button>
                    </div>
                </form>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 sm:px-10">
                <p class="text-xs text-center text-gray-600">
                    By signing up, you agree to our 
                    <a href="terms-of-service.php" class="font-medium text-primary-600 hover:text-primary-500">Terms of Service</a> and 
                    <a href="privacy-policy.php" class="font-medium text-primary-600 hover:text-primary-500">Privacy Policy</a>.
                </p>
            </div>
        </div>
        
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                Already have an account? 
                <a href="login.php" class="font-medium text-primary-600 hover:text-primary-500">
                    Sign in
                </a>
            </p>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

