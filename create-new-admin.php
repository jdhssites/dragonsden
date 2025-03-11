<?php
require_once 'includes/db.php';
session_start();

// Set custom admin credentials - CHANGE THESE
$admin_username = 'newsadmin';
$admin_email = 'newsadmin@example.com';
$admin_password = 'Admin@123'; // More secure password

echo "<h1>New Admin Account Setup</h1>";

// Hash the password
$hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);

// Check if database has role column
$stmt = $pdo->prepare("SHOW COLUMNS FROM users LIKE 'role'");
$stmt->execute();
$column_exists = $stmt->fetch();

if (!$column_exists) {
    echo "<p>Error: The 'role' column doesn't exist in the users table.</p>";
    echo "<p>Please run the update-database.php script first:</p>";
    echo "<p><a href='update-database.php' style='color: blue; text-decoration: underline;'>Run Database Update Script</a></p>";
    exit;
}

// Create new admin user
try {
    // First check if user already exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$admin_email]);
    $existing_user = $stmt->fetch();
    
    if ($existing_user) {
        // Update existing user
        $stmt = $pdo->prepare("UPDATE users SET username = ?, role = 'admin', password = ? WHERE id = ?");
        $stmt->execute([$admin_username, $hashed_password, $existing_user['id']]);
        echo "<p>Existing user has been updated with admin privileges.</p>";
    } else {
        // Create new admin user
        $stmt = $pdo->prepare("INSERT INTO users (username, email, role, password, created_at) VALUES (?, ?, 'admin', ?, NOW())");
        $stmt->execute([$admin_username, $admin_email, $hashed_password]);
        echo "<p>New admin user has been created successfully!</p>";
    }
    
    echo "<div style='background-color: #f0f8ff; border: 1px solid #4682b4; padding: 15px; margin: 20px 0; border-radius: 5px;'>";
    echo "<h2>Admin Login Credentials</h2>";
    echo "<p>Username: <strong>{$admin_username}</strong></p>";
    echo "<p>Email: <strong>{$admin_email}</strong></p>";
    echo "<p>Password: <strong>{$admin_password}</strong></p>";
    echo "</div>";
    
    echo "<p><a href='login.php' style='display: inline-block; background-color: #4CAF50; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px;'>Go to Login Page</a></p>";
    
} catch (PDOException $e) {
    echo "<p>Error creating admin user: " . $e->getMessage() . "</p>";
    
    // More detailed error information
    echo "<h3>Database Error Details:</h3>";
    echo "<pre>";
    print_r($e);
    echo "</pre>";
}
?>

