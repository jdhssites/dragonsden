<?php
require_once 'includes/db.php';
session_start();

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    die("You must be logged in to run this update script.");
}

echo "<h1>Database Update Script</h1>";

// Check if the role column already exists
$stmt = $pdo->prepare("SHOW COLUMNS FROM users LIKE 'role'");
$stmt->execute();
$column_exists = $stmt->fetch();

if($column_exists) {
    echo "<p>The 'role' column already exists in the users table. No update needed.</p>";
} else {
    // Add role column to users table
    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN role VARCHAR(20) DEFAULT 'user' AFTER email");
        echo "<p>Successfully added 'role' column to users table.</p>";
    } catch (PDOException $e) {
        echo "<p>Error adding 'role' column: " . $e->getMessage() . "</p>";
        die();
    }
}

// Set the current logged-in user as admin
try {
    $stmt = $pdo->prepare("UPDATE users SET role = 'admin' WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    echo "<p>Successfully set your account as an administrator.</p>";
    
    // Update session to include role
    $_SESSION['role'] = 'admin';
} catch (PDOException $e) {
    echo "<p>Error setting admin role: " . $e->getMessage() . "</p>";
}

// Check if role_permissions table exists
$stmt = $pdo->prepare("SHOW TABLES LIKE 'role_permissions'");
$stmt->execute();
$table_exists = $stmt->fetch();

if(!$table_exists) {
    // Create role_permissions table
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS role_permissions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            role VARCHAR(20) NOT NULL,
            permission VARCHAR(50) NOT NULL,
            UNIQUE KEY role_permission (role, permission)
        )");
        echo "<p>Successfully created role_permissions table.</p>";
        
        // Insert default permissions
        $permissions = [
            ['admin', 'manage_users'],
            ['admin', 'publish_article'],
            ['admin', 'edit_any_article'],
            ['admin', 'delete_any_article'],
            ['editor', 'publish_article'],
            ['editor', 'edit_any_article'],
            ['writer', 'publish_article'],
            ['user', 'create_draft']
        ];
        
        $stmt = $pdo->prepare("INSERT INTO role_permissions (role, permission) VALUES (?, ?)");
        foreach($permissions as $permission) {
            try {
                $stmt->execute($permission);
            } catch (PDOException $e) {
                // Ignore duplicate key errors
                if($e->getCode() != 23000) {
                    throw $e;
                }
            }
        }
        echo "<p>Successfully added default permissions.</p>";
    } catch (PDOException $e) {
        echo "<p>Error creating role_permissions table: " . $e->getMessage() . "</p>";
    }
}

echo "<h2>Database Update Complete</h2>";
echo "<p>Your database has been updated with the new user role system.</p>";
echo "<p><a href='index.php' style='color: blue; text-decoration: underline;'>Return to homepage</a></p>";
echo "<p><a href='admin/index.php' style='color: blue; text-decoration: underline;'>Go to Admin Dashboard</a></p>";
?>

