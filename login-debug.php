<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
session_start();

echo "<h1>Login Debugging Tool</h1>";

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    echo "<h2>Attempting Login</h2>";
    echo "<p>Email: " . htmlspecialchars($email) . "</p>";
    
    // Check if user exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user) {
        echo "<p>User found in database:</p>";
        echo "<ul>";
        echo "<li>ID: " . $user['id'] . "</li>";
        echo "<li>Username: " . htmlspecialchars($user['username']) . "</li>";
        echo "<li>Email: " . htmlspecialchars($user['email']) . "</li>";
        echo "<li>Role: " . (isset($user['role']) ? htmlspecialchars($user['role']) : "Not set") . "</li>";
        echo "</ul>";
        
        // Check password
        $password_match = password_verify($password, $user['password']);
        echo "<p>Password verification: " . ($password_match ? "SUCCESS" : "FAILED") . "</p>";
        
        if ($password_match) {
            echo "<p style='color: green;'>Login successful! You would be redirected to the homepage in a normal login.</p>";
            
            // Show what would be stored in session
            echo "<p>The following would be stored in your session:</p>";
            echo "<ul>";
            echo "<li>user_id: " . $user['id'] . "</li>";
            echo "<li>username: " . htmlspecialchars($user['username']) . "</li>";
            echo "<li>email: " . htmlspecialchars($user['email']) . "</li>";
            if (isset($user['role'])) {
                echo "<li>role: " . htmlspecialchars($user['role']) . "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p style='color: red;'>Password does not match our records.</p>";
            echo "<p>Stored password hash: " . $user['password'] . "</p>";
            echo "<p>Note: If you're using a pre-hashed password from SQL, it might not work with PHP's password_verify function.</p>";
        }
    } else {
        echo "<p style='color: red;'>No user found with this email address.</p>";
    }
}
?>

<h2>Test Login Form</h2>
<form method="POST" action="login-debug.php" style="max-width: 400px; margin: 20px 0; padding: 20px; border: 1px solid #ccc; border-radius: 5px;">
    <div style="margin-bottom: 15px;">
        <label for="email" style="display: block; margin-bottom: 5px;">Email:</label>
        <input type="email" id="email" name="email" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
    </div>
    
    <div style="margin-bottom: 15px;">
        <label for="password" style="display: block; margin-bottom: 5px;">Password:</label>
        <input type="password" id="password" name="password" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
    </div>
    
    <button type="submit" style="background-color: #3b82f6; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer;">Test Login</button>
</form>

<p><a href="login.php" style="color: blue; text-decoration: underline;">Go to Regular Login Page</a></p>
<p><a href="create-admin.php" style="color: blue; text-decoration: underline;">Create/Reset Admin Account</a></p>

