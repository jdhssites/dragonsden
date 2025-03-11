-- Add role column to users table
ALTER TABLE users ADD COLUMN role VARCHAR(20) DEFAULT 'user' AFTER email;

-- Update existing admin user or create one if not exists
-- Replace 'admin_username' and 'admin_email' with your desired values
-- The password is hashed version of 'admin123' - you should change this
INSERT INTO users (username, email, role, password, created_at) 
VALUES ('admin', 'admin@example.com', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW())
ON DUPLICATE KEY UPDATE role = 'admin';

-- Add some example roles for team members
UPDATE users SET role = 'editor' WHERE id = 2;
UPDATE users SET role = 'writer' WHERE id = 3;

-- Create a table for role permissions (optional, for future expansion)
CREATE TABLE IF NOT EXISTS role_permissions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  role VARCHAR(20) NOT NULL,
  permission VARCHAR(50) NOT NULL,
  UNIQUE KEY role_permission (role, permission)
);

-- Insert default permissions
INSERT INTO role_permissions (role, permission) VALUES
('admin', 'manage_users'),
('admin', 'publish_article'),
('admin', 'edit_any_article'),
('admin', 'delete_any_article'),
('editor', 'publish_article'),
('editor', 'edit_any_article'),
('writer', 'publish_article'),
('user', 'create_draft');

