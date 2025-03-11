-- Create messages table
CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message TEXT NOT NULL,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    is_read TINYINT(1) DEFAULT 0,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create user_conversations table to track active conversations
CREATE TABLE IF NOT EXISTS user_conversations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user1_id INT NOT NULL,
    user2_id INT NOT NULL,
    last_message_time DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user1_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (user2_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_conversation (user1_id, user2_id)
);

