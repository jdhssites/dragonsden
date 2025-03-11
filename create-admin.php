<?php
require_once 'includes/db.php';
session_start();

echo "<h1>Admin Account Setup</h1>";

// Define admin credentials
$admin_username = 'admin';
$admin_email = 'admin@example.com';
$admin_password = 'admin123'; // You should change this after logging in
$hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);

// Check if admin user already exists
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
$stmt->execute([$admin_username, $admin_email]);
$existing_user = $stmt->fetch();

if ($existing_user) {
    // Update existing user
    try {
        $stmt = $pdo->prepare("UPDATE users SET role = 'admin', password = ? WHERE id = ?");
        $stmt->execute([$hashed_password, $existing_user['id']]);
        echo "<p>Existing user '{$existing_user['username']}' has been updated with admin privileges.</p>";
        echo "<p>The password has been reset to: <strong>admin123</strong></p>";
    } catch (PDOException $e) {
        echo "<p>Error updating user: " . $e->getMessage() . "</p>";
    }
} else {
    // Create new admin user
    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, role, password, created_at) VALUES (?, ?, 'admin', ?, NOW())");
        $stmt->execute([$admin_username, $admin_email, $hashed_password]);
        echo "<p>New admin user has been created:</p>";
        echo "<p>Username: <strong>{$admin_username}</strong></p>";
        echo "<p>Email: <strong>{$admin_email}</strong></p>";
        echo "<p>Password: <strong>admin123</strong></p>";
    } catch (PDOException $e) {
        echo "<p>Error creating admin user: " . $e->getMessage() . "</p>";
    }
}

// Check if role column exists
$stmt = $pdo->prepare("SHOW COLUMNS FROM users LIKE 'role'");
$stmt->execute();
$column_exists = $stmt->fetch();

if (!$column_exists) {
    echo "<p>Warning: The 'role' column doesn't exist in the users table. Please run the update-database.php script first.</p>";
    echo "<p><a href='update-database.php' style='color: blue; text-decoration: underline;'>Run Database Update Script</a></p>";
} else {
    echo "<p>The 'role' column exists in the users table.</p>";
}

echo "<h2>Login Instructions</h2>";
echo "<p>You can now log in with the following credentials:</p>";
echo "<ul>";
echo "<li>Username: <strong>{$admin_username}</strong></li>";
echo "<li>Password: <strong>admin123</strong></li>";
echo "</ul>";
echo "<p><strong>Important:</strong> Please change your password after logging in for security reasons.</p>";
echo "<p><a href='login.php' style='color: blue; text-decoration: underline;'>Go to Login Page</a></p>";
?>

