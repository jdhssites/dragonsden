<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
session_start();

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$current_user_id = $_SESSION['user_id'];

// Get unread message count
$stmt = $pdo->prepare("
    SELECT COUNT(*) as count 
    FROM messages 
    WHERE receiver_id = ? AND is_read = 0
");
$stmt->execute([$current_user_id]);
$result = $stmt->fetch();

// Get sender information for the latest unread message
$stmt = $pdo->prepare("
    SELECT 
        m.sender_id,
        u.username as sender_name,
        m.message,
        m.timestamp
    FROM 
        messages m
    JOIN 
        users u ON m.sender_id = u.id
    WHERE 
        m.receiver_id = ? AND m.is_read = 0
    ORDER BY 
        m.timestamp DESC
    LIMIT 1
");
$stmt->execute([$current_user_id]);
$latest = $stmt->fetch();

$response = [
    'success' => true,
    'unread_count' => (int)$result['count'],
    'has_new' => (int)$result['count'] > 0,
    'last_checked' => date('Y-m-d H:i:s')
];

if ($latest) {
    $response['latest'] = [
        'sender_id' => $latest['sender_id'],
        'sender_name' => $latest['sender_name'],
        'message_preview' => substr(htmlspecialchars($latest['message']), 0, 30) . (strlen($latest['message']) > 30 ? '...' : ''),
        'timestamp' => date('h:i A', strtotime($latest['timestamp']))
    ];
}

// Get count of unique senders
if ($result['count'] > 0) {
    $stmt = $pdo->prepare("
        SELECT COUNT(DISTINCT sender_id) as unique_senders
        FROM messages
        WHERE receiver_id = ? AND is_read = 0
    ");
    $stmt->execute([$current_user_id]);
    $unique = $stmt->fetch();
    $response['unique_senders'] = (int)$unique['unique_senders'];
}

echo json_encode($response);

