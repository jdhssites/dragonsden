<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
session_start();

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to view messages']);
    exit;
}

// Get the current user ID
$current_user_id = $_SESSION['user_id'];

// Get and validate the other user ID
$other_user_id = isset($_GET['user']) ? (int)$_GET['user'] : 0;

// Validate inputs
if ($other_user_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid user']);
    exit;
}

try {
    // Get messages between the two users
    $stmt = $pdo->prepare("
        SELECT 
            m.id,
            m.sender_id,
            m.receiver_id,
            m.message,
            DATE_FORMAT(m.timestamp, '%h:%i %p - %b %d') as formatted_time,
            m.is_read,
            (m.sender_id = ?) as is_sender
        FROM 
            messages m
        WHERE 
            (m.sender_id = ? AND m.receiver_id = ?) OR
            (m.sender_id = ? AND m.receiver_id = ?)
        ORDER BY 
            m.timestamp ASC
    ");
    
    $stmt->execute([
        $current_user_id,
        $current_user_id, $other_user_id,
        $other_user_id, $current_user_id
    ]);
    
    $messages = $stmt->fetchAll();
    
    // Mark messages as read
    $stmt = $pdo->prepare("
        UPDATE messages 
        SET is_read = 1 
        WHERE sender_id = ? AND receiver_id = ? AND is_read = 0
    ");
    $stmt->execute([$other_user_id, $current_user_id]);
    
    // Format messages for the response
    $formatted_messages = [];
    foreach ($messages as $msg) {
        $formatted_messages[] = [
            'id' => $msg['id'],
            'is_sender' => (bool)$msg['is_sender'],
            'message' => htmlspecialchars($msg['message']),
            'timestamp' => $msg['formatted_time'],
            'is_read' => (bool)$msg['is_read']
        ];
    }
    
    echo json_encode([
        'success' => true, 
        'messages' => $formatted_messages
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

