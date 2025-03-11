<!-- Add this to your header.php file before the closing </header> tag -->

<?php if (isLoggedIn()): ?>
<div id="chat-notification" class="hidden fixed bottom-4 right-4 bg-dark-800 border border-dark-600 rounded-lg shadow-lg p-4 max-w-xs w-full z-50">
    <div class="flex items-start">
        <div class="flex-shrink-0 pt-0.5">
            <i class="fas fa-comment-dots text-primary-500 text-xl"></i>
        </div>
        <div class="ml-3 w-0 flex-1">
            <p class="text-sm font-medium text-white" id="notification-sender"></p>
            <p class="mt-1 text-sm text-dark-300" id="notification-message"></p>
            <div class="mt-2 flex">
                <a id="notification-link" href="#" class="text-sm font-medium text-primary-500 hover:text-primary-400">
                    View message
                </a>
            </div>
        </div>
        <div class="ml-4 flex-shrink-0 flex">
            <button id="close-notification" class="bg-dark-800 rounded-md inline-flex text-dark-400 hover:text-dark-300 focus:outline-none">
                <span class="sr-only">Close</span>
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Only run if user is logged in
    <?php if (isLoggedIn()): ?>
    
    const chatNotification = document.getElementById('chat-notification');
    const notificationSender = document.getElementById('notification-sender');
    const notificationMessage = document.getElementById('notification-message');
    const notificationLink = document.getElementById('notification-link');
    const closeNotification = document.getElementById('close-notification');
    
    // Close notification when clicked
    if (closeNotification) {
        closeNotification.addEventListener('click', function() {
            chatNotification.classList.add('hidden');
        });
    }
    
    // Check for new messages every 10 seconds
    function checkNewMessages() {
        fetch('chat-notification.php')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.has_new && data.latest) {
                    // Don't show notification if we're already on the chat page with this user
                    const currentPath = window.location.pathname;
                    const currentSearch = new URLSearchParams(window.location.search);
                    const currentUser = currentSearch.get('user');
                    
                    if (!(currentPath.endsWith('chat.php') && currentUser == data.latest.sender_id)) {
                        // Update notification content
                        notificationSender.textContent = data.latest.sender_name;
                        notificationMessage.textContent = data.latest.message_preview;
                        notificationLink.href = `chat.php?user=${data.latest.sender_id}`;
                        
                        // Show notification
                        chatNotification.classList.remove('hidden');
                        
                        // Hide after 10 seconds
                        setTimeout(() => {
                            chatNotification.classList.add('hidden');
                        }, 10000);
                    }
                }
            })
            .catch(error => {
                console.error('Error checking messages:', error);
            });
    }
    
    // Initial check
    checkNewMessages();
    
    // Set interval for checking
    setInterval(checkNewMessages, 10000);
    
    <?php endif; ?>
});
</script>
<?php endif; ?>

