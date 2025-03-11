<!-- Add this to your navigation menu in header.php -->
<?php if (isLoggedIn()): ?>
    <li class="relative group">
        <a href="chat.php" class="block py-2 px-4 text-white hover:text-primary-400 transition-colors">
            <span>Chat</span>
            <span id="unread-badge" class="hidden ml-1 px-1.5 py-0.5 text-xs bg-primary-500 text-white rounded-full"></span>
        </a>
    </li>
<?php endif; ?>

<!-- Add this script to update the unread badge -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if (isLoggedIn()): ?>
    const unreadBadge = document.getElementById('unread-badge');
    
    function updateUnreadBadge() {
        fetch('chat-notification.php')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.unread_count > 0) {
                    unreadBadge.textContent = data.unread_count > 99 ? '99+' : data.unread_count;
                    unreadBadge.classList.remove('hidden');
                } else {
                    unreadBadge.classList.add('hidden');
                }
            })
            .catch(error => {
                console.error('Error updating badge:', error);
            });
    }
    
    // Initial update
    updateUnreadBadge();
    
    // Update every 30 seconds
    setInterval(updateUnreadBadge, 30000);
    <?php endif; ?>
});
</script>

