<?php
require_once 'includes/db.php';

// Configuration - you can modify these values
$admin_username = 'admin';
$admin_email = 'admin@example.com';
$admin_password = 'admin123';
$admin_role = 'admin';

// Output buffer to collect messages
$messages = [];
$errors = [];

// Function to add message
function addMessage($message) {
    global $messages;
    $messages[] = $message;
}

// Function to add error
function addError($message) {
    global $errors;
    $errors[] = $message;
}

// Check database connection
try {
    $pdo->query("SELECT 1");
    addMessage("✅ Database connection successful");
} catch (PDOException $e) {
    addError("❌ Database connection failed: " . $e->getMessage());
    goto output;
}

// Check if users table exists
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() > 0) {
        addMessage("✅ Users table exists");
    } else {
        addError("❌ Users table does not exist. Please run the database setup script first.");
        goto output;
    }
} catch (PDOException $e) {
    addError("❌ Error checking users table: " . $e->getMessage());
    goto output;
}

// Check if role column exists in users table
try {
    $stmt = $pdo->prepare("SHOW COLUMNS FROM users LIKE 'role'");
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        addMessage("✅ Role column exists in users table");
    } else {
        addMessage("⚠️ Role column does not exist in users table. Adding it now...");
        
        // Add role column
        try {
            $pdo->exec("ALTER TABLE users ADD COLUMN role VARCHAR(20) DEFAULT 'user' AFTER email");
            addMessage("✅ Role column added successfully");
        } catch (PDOException $e) {
            addError("❌ Failed to add role column: " . $e->getMessage());
            goto output;
        }
    }
} catch (PDOException $e) {
    addError("❌ Error checking role column: " . $e->getMessage());
    goto output;
}

// Check if admin user exists
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$admin_email]);
    $user = $stmt->fetch();
    
    if ($user) {
        addMessage("✅ User with email {$admin_email} already exists. Updating to admin role...");
        
        // Update existing user to admin
        try {
            $stmt = $pdo->prepare("UPDATE users SET role = ?, username = ? WHERE id = ?");
            $stmt->execute([$admin_role, $admin_username, $user['id']]);
            
            // Update password if needed
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);
            $stmt->execute([$hashed_password, $user['id']]);
            
            addMessage("✅ User updated to admin role with username: {$admin_username}");
        } catch (PDOException $e) {
            addError("❌ Failed to update user: " . $e->getMessage());
            goto output;
        }
    } else {
        addMessage("⚠️ No user found with email {$admin_email}. Creating admin user...");
        
        // Create new admin user
        try {
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())");
            $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);
            $stmt->execute([$admin_username, $admin_email, $hashed_password, $admin_role]);
            
            addMessage("✅ Admin user created successfully");
        } catch (PDOException $e) {
            addError("❌ Failed to create admin user: " . $e->getMessage());
            goto output;
        }
    }
} catch (PDOException $e) {
    addError("❌ Error checking admin user: " . $e->getMessage());
    goto output;
}

// Verify admin user exists and has correct role
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role = ?");
    $stmt->execute([$admin_email, $admin_role]);
    $admin_user = $stmt->fetch();
    
    if ($admin_user) {
        addMessage("✅ Admin user verified with ID: {$admin_user['id']}");
    } else {
        addError("❌ Admin user verification failed. Please check the database manually.");
    }
} catch (PDOException $e) {
    addError("❌ Error verifying admin user: " . $e->getMessage());
}

output:
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Account Setup</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background-color: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #2563eb;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 10px;
        }
        .message {
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 4px;
        }
        .success {
            background-color: #d1fae5;
            border-left: 4px solid #10b981;
        }
        .error {
            background-color: #fee2e2;
            border-left: 4px solid #ef4444;
        }
        .warning {
            background-color: #fff7ed;
            border-left: 4px solid #f97316;
        }
        .credentials {
            background-color: #eff6ff;
            border: 1px solid #93c5fd;
            border-radius: 4px;
            padding: 15px;
            margin-top: 20px;
        }
        .credentials h3 {
            margin-top: 0;
            color: #2563eb;
        }
        code {
            background-color: #f1f5f9;
            padding: 2px 4px;
            border-radius: 4px;
            font-family: monospace;
        }
        .btn {
            display: inline-block;
            background-color: #2563eb;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 15px;
        }
        .btn:hover {
            background-color: #1d4ed8;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Admin Account Setup</h1>
        
        <?php if (!empty($errors)): ?>
            <div class="message error">
                <h3>Errors:</h3>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($messages)): ?>
            <div class="message success">
                <h3>Process Log:</h3>
                <ul>
                    <?php foreach ($messages as $message): ?>
                        <li><?= $message ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <?php if (empty($errors)): ?>
            <div class="credentials">
                <h3>Admin Login Credentials</h3>
                <p><strong>Username:</strong> <?= htmlspecialchars($admin_username) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($admin_email) ?></p>
                <p><strong>Password:</strong> <?= htmlspecialchars($admin_password) ?></p>
                <p><strong>Role:</strong> <?= htmlspecialchars($admin_role) ?></p>
                
                <p class="warning">⚠️ For security reasons, please change this password after logging in.</p>
                
                <a href="login.php" class="btn">Go to Login Page</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

