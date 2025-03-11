<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
session_start();

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to send messages']);
    exit;
}

// Check if it's an AJAX request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get the current user ID
$sender_id = $_SESSION['user_id'];

// Get and validate the receiver ID and message
$receiver_id = isset($_POST['receiver_id']) ? (int)$_POST['receiver_id'] : 0;
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

// Validate inputs
if ($receiver_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid receiver']);
    exit;
}

if (empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Message cannot be empty']);
    exit;
}

// Check if receiver exists
$stmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");
$stmt->execute([$receiver_id]);
if (!$stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Receiver does not exist']);
    exit;
}

try {
    // Begin transaction
    $pdo->beginTransaction();
    
    // Insert the message
    $stmt = $pdo->prepare("
        INSERT INTO messages (sender_id, receiver_id, message) 
        VALUES (?, ?, ?)
    ");
    $stmt->execute([$sender_id, $receiver_id, $message]);
    
    // Update or create conversation record
    $stmt = $pdo->prepare("
        INSERT INTO user_conversations (user1_id, user2_id, last_message_time)
        VALUES (?, ?, NOW())
        ON DUPLICATE KEY UPDATE last_message_time = NOW()
    ");
    
    // Ensure user1_id is always the smaller ID to maintain uniqueness
    $user1 = min($sender_id, $receiver_id);
    $user2 = max($sender_id, $receiver_id);
    
    $stmt->execute([$user1, $user2]);
    
    // Commit transaction
    $pdo->commit();
    
    echo json_encode(['success' => true, 'message' => 'Message sent successfully']);
} catch (PDOException $e) {
    // Rollback transaction on error
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

