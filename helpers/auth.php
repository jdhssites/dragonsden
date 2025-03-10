<?php
// Authentication helper functions

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check if user is admin
function isAdmin() {
    return isset($_SESSION['user_id']) && isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
}

// Get current user
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    $conn = getDbConnection();
    $userId = $_SESSION['user_id'];
    
    $stmt = $conn->prepare("SELECT id, name, email, is_admin, avatar, bio, theme FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        closeDbConnection($conn);
        return null;
    }
    
    $user = $result->fetch_assoc();
    closeDbConnection($conn);
    
    return $user;
}

// Login user
function loginUser($email, $password) {
    $conn = getDbConnection();
    
    $stmt = $conn->prepare("SELECT id, name, email, password, is_admin FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        closeDbConnection($conn);
        return [
            'success' => false,
            'message' => 'Invalid email or password'
        ];
    }
    
    $user = $result->fetch_assoc();
    
    if (!password_verify($password, $user['password'])) {
        closeDbConnection($conn);
        return [
            'success' => false,
            'message' => 'Invalid email or password'
        ];
    }
    
    // Set session variables
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['is_admin'] = $user['is_admin'] == 1;
    
    closeDbConnection($conn);
    
    return [
        'success' => true,
        'message' => 'Login successful',
        'user' => [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'isAdmin' => $user['is_admin'] == 1
        ]
    ];
}

// Register user
function registerUser($name, $email, $password) {
    $conn = getDbConnection();
    
    // Check if user already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        closeDbConnection($conn);
        return [
            'success' => false,
            'message' => 'User with this email already exists'
        ];
    }
    
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, is_admin, avatar) VALUES (?, ?, ?, 0, '/placeholder.svg?height=100&width=100')");
    $stmt->bind_param("sss", $name, $email, $hashedPassword);
    
    if (!$stmt->execute()) {
        closeDbConnection($conn);
        return [
            'success' => false,
            'message' => 'Registration failed: ' . $conn->error
        ];
    }
    
    $userId = $conn->insert_id;
    
    // Set session variables
    $_SESSION['user_id'] = $userId;
    $_SESSION['user_name'] = $name;
    $_SESSION['user_email'] = $email;
    $_SESSION['is_admin'] = false;
    
    closeDbConnection($conn);
    
    return [
        'success' => true,
        'message' => 'Registration successful',
        'user' => [
            'id' => $userId,
            'name' => $name,
            'email' => $email,
            'isAdmin' => false
        ]
    ];
}

// Logout user
function logoutUser() {
    session_unset();
    session_destroy();
}

