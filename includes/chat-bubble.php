<div id="chat-bubble" class="fixed bottom-6 right-6 z-50 group">
    <a href="chat.php" class="flex items-center justify-center w-14 h-14 rounded-full bg-primary-600 text-white shadow-lg hover:bg-primary-700 transition-all transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500" aria-label="Open chat">
        <span id="chat-tooltip" class="absolute bottom-full right-0 mb-2 w-auto p-2 bg-dark-800 text-white text-sm rounded-md shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap">
            Open Chat
            <span id="chat-tooltip-count" class="hidden ml-1 font-bold"></span>
        </span>
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10v-3m0 0l4-4m-4 4h4m-4 7v3m0 0l4 4m-4-4h4m-5-8v2a3 3 0 003 3h4a3 3 0 003-3v-2M9 21h6a2 2 0 002-2V5a2 2 0 00-2-2H9a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
        </svg>
        <span id="chat-bubble-badge" class="absolute top-0 right-0 transform translate-x-1/2 -translate-y-1/2 bg-red-500 text-white text-xs font-semibold rounded-full px-2 py-0.5 hidden"></span>
    </a>
</div>

<script>
    // Function to fetch and update the chat bubble badge
    function updateChatBubble() {
        fetch('api/get_unread_messages.php')
            .then(response => response.json())
            .then(data => {
                const chatBubbleBadge = document.getElementById('chat-bubble-badge');
                if (data.success && data.unread_count > 0) {
                    chatBubbleBadge.textContent = data.unread_count > 99 ? '99+' : data.unread_count;
                    chatBubbleBadge.classList.remove('hidden');
                    
                    // Update tooltip
                    const chatTooltip = document.getElementById('chat-tooltip');
                    const chatTooltipCount = document.getElementById('chat-tooltip-count');
                    
                    if (data.unique_senders > 1) {
                        chatTooltip.firstChild.textContent = `Messages from ${data.unique_senders} people`;
                    } else {
                        chatTooltip.firstChild.textContent = 'New messages';
                    }
                    
                    chatTooltipCount.textContent = `(${data.unread_count})`;
                    chatTooltipCount.classList.remove('hidden');
                    
                    // Add pulse animation when new messages arrive
                    const chatBubble = document.getElementById('chat-bubble');
                    chatBubble.classList.add('animate-pulse');
                    setTimeout(() => {
                        chatBubble.classList.remove('animate-pulse');
                    }, 2000);
                } else {
                    chatBubbleBadge.classList.add('hidden');
                    
                    // Reset tooltip
                    const chatTooltip = document.getElementById('chat-tooltip');
                    const chatTooltipCount = document.getElementById('chat-tooltip-count');
                    chatTooltip.firstChild.textContent = 'Open Chat';
                    chatTooltipCount.classList.add('hidden');
                }
            })
            .catch(error => console.error('Error fetching unread messages:', error));
    }

    // Call the function initially and then every 10 seconds
    updateChatBubble();
    setInterval(updateChatBubble, 10000);
</script>

