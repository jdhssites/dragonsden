<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
session_start();

// Redirect if not logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$current_user_id = $_SESSION['user_id'];

// Get all users for the chat list
$stmt = $pdo->prepare("
    SELECT id, username, role 
    FROM users 
    WHERE id != ? 
    ORDER BY username ASC
");
$stmt->execute([$current_user_id]);
$users = $stmt->fetchAll();

// Get active conversations
$stmt = $pdo->prepare("
    SELECT uc.*, 
           u.username as other_username,
           u.id as other_user_id,
           (SELECT COUNT(*) FROM messages 
            WHERE sender_id = uc.user2_id 
            AND receiver_id = uc.user1_id 
            AND is_read = 0) as unread_count
    FROM user_conversations uc
    JOIN users u ON (uc.user1_id = ? AND uc.user2_id = u.id) OR (uc.user2_id = ? AND uc.user1_id = u.id)
    ORDER BY last_message_time DESC
");
$stmt->execute([$current_user_id, $current_user_id]);
$conversations = $stmt->fetchAll();

// Get selected user if any
$selected_user_id = isset($_GET['user']) ? (int)$_GET['user'] : null;
$selected_username = '';

if ($selected_user_id) {
    $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->execute([$selected_user_id]);
    $user = $stmt->fetch();
    if ($user) {
        $selected_username = $user['username'];
    }
}

// Page title
$page_title = "Chat - Dragon's Den";
?>

<?php include 'includes/header.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row">
        <!-- Left sidebar - User list -->
        <div class="w-full md:w-1/4 bg-dark-800 rounded-lg shadow-lg p-4 mb-4 md:mb-0 md:mr-4">
            <h2 class="text-xl font-bold text-white mb-4">Conversations</h2>
            
            <!-- Search box -->
            <div class="mb-4">
                <input type="text" id="user-search" placeholder="Search users..." 
                       class="w-full px-3 py-2 bg-dark-700 text-white rounded-md border border-dark-600 focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            
            <!-- Active conversations -->
            <?php if (count($conversations) > 0): ?>
                <div class="mb-4">
                    <h3 class="text-lg font-semibold text-white mb-2">Recent</h3>
                    <ul class="space-y-2 max-h-60 overflow-y-auto" id="conversations-list">
                        <?php foreach ($conversations as $convo): ?>
                            <?php 
                            $other_id = $convo['other_user_id'];
                            $is_active = $selected_user_id == $other_id ? 'bg-primary-700' : 'bg-dark-700 hover:bg-dark-600';
                            ?>
                            <li>
                                <a href="chat.php?user=<?= $other_id ?>" 
                                   class="flex items-center justify-between p-2 rounded-md <?= $is_active ?> transition-colors">
                                    <span class="text-white"><?= htmlspecialchars($convo['other_username']) ?></span>
                                    <?php if ($convo['unread_count'] > 0): ?>
                                        <span class="bg-primary-500 text-white text-xs px-2 py-1 rounded-full">
                                            <?= $convo['unread_count'] ?>
                                        </span>
                                    <?php endif; ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <!-- All users -->
            <div>
                <h3 class="text-lg font-semibold text-white mb-2">All Users</h3>
                <ul class="space-y-2 max-h-96 overflow-y-auto" id="users-list">
                    <?php foreach ($users as $user): ?>
                        <?php $is_active = $selected_user_id == $user['id'] ? 'bg-primary-700' : 'bg-dark-700 hover:bg-dark-600'; ?>
                        <li class="user-item">
                            <a href="chat.php?user=<?= $user['id'] ?>" 
                               class="flex items-center p-2 rounded-md <?= $is_active ?> transition-colors">
                                <span class="text-white"><?= htmlspecialchars($user['username']) ?></span>
                                <span class="ml-2 text-xs text-dark-300">(<?= $user['role'] ?>)</span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        
        <!-- Right side - Chat area -->
        <div class="w-full md:w-3/4 bg-dark-800 rounded-lg shadow-lg p-4 flex flex-col">
            <?php if ($selected_user_id): ?>
                <!-- Chat header -->
                <div class="border-b border-dark-600 pb-3 mb-4">
                    <h2 class="text-xl font-bold text-white">
                        Chat with <?= htmlspecialchars($selected_username) ?>
                    </h2>
                </div>
                
                <!-- Messages container -->
                <div id="messages-container" class="flex-grow overflow-y-auto mb-4 p-2" style="height: 400px;">
                    <div class="flex justify-center items-center h-full text-dark-400">
                        <p>Loading messages...</p>
                    </div>
                </div>
                
                <!-- Message input -->
                <div class="mt-auto">
                    <form id="message-form" class="flex">
                        <input type="hidden" id="receiver-id" value="<?= $selected_user_id ?>">
                        <input type="text" id="message-input" placeholder="Type your message..." 
                               class="flex-grow px-4 py-2 bg-dark-700 text-white rounded-l-md border border-dark-600 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <button type="submit" 
                                class="px-4 py-2 bg-primary-600 text-white rounded-r-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            Send
                        </button>
                    </form>
                </div>
            <?php else: ?>
                <!-- No user selected -->
                <div class="flex flex-col items-center justify-center h-full text-center">
                    <div class="text-dark-300 mb-4">
                        <i class="fas fa-comments text-6xl"></i>
                    </div>
                    <h2 class="text-xl font-bold text-white mb-2">Select a user to start chatting</h2>
                    <p class="text-dark-400">Choose a user from the list on the left to begin a conversation.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Chat JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const messagesContainer = document.getElementById('messages-container');
    const messageForm = document.getElementById('message-form');
    const messageInput = document.getElementById('message-input');
    const receiverId = document.getElementById('receiver-id');
    const userSearch = document.getElementById('user-search');
    
    // Only initialize chat if a user is selected
    if (receiverId) {
        // Load initial messages
        loadMessages();
        
        // Set up polling for new messages (every 3 seconds)
        const messageInterval = setInterval(loadMessages, 3000);
        
        // Form submission
        messageForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const message = messageInput.value.trim();
            if (message === '') return;
            
            sendMessage(message);
            messageInput.value = '';
        });
    }
    
    // User search functionality
    if (userSearch) {
        userSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const userItems = document.querySelectorAll('.user-item');
            
            userItems.forEach(item => {
                const username = item.textContent.toLowerCase();
                if (username.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
    
    // Function to load messages
    function loadMessages() {
        if (!receiverId) return;
        
        fetch(`get-messages.php?user=${receiverId.value}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayMessages(data.messages);
                } else {
                    console.error('Error loading messages:', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }
    
    // Function to send a message
    function sendMessage(message) {
        const formData = new FormData();
        formData.append('receiver_id', receiverId.value);
        formData.append('message', message);
        
        fetch('send-message.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Add the new message to the UI
                loadMessages();
            } else {
                console.error('Error sending message:', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
    
    // Function to display messages
    function displayMessages(messages) {
        if (!messagesContainer) return;
        
        if (messages.length === 0) {
            messagesContainer.innerHTML = `
                <div class="flex justify-center items-center h-full text-dark-400">
                    <p>No messages yet. Start the conversation!</p>
                </div>
            `;
            return;
        }
        
        let html = '';
        messages.forEach(msg => {
            const isCurrentUser = msg.is_sender;
            const messageClass = isCurrentUser 
                ? 'bg-primary-600 text-white self-end' 
                : 'bg-dark-700 text-white self-start';
                
            html += `
                <div class="flex flex-col mb-4 max-w-3/4 ${isCurrentUser ? 'items-end' : 'items-start'}">
                    <div class="px-4 py-2 rounded-lg ${messageClass}">
                        ${msg.message}
                    </div>
                    <div class="text-xs text-dark-400 mt-1">
                        ${msg.timestamp}
                    </div>
                </div>
            `;
        });
        
        messagesContainer.innerHTML = html;
        
        // Scroll to bottom
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
});
</script>

<?php include 'includes/footer.php'; ?>

