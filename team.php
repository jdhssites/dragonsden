<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
session_start();

// Set page title
$page_title = "Our Team - Dragon's Den";

// Check if role column exists in users table
$stmt = $pdo->prepare("SHOW COLUMNS FROM users LIKE 'role'");
$stmt->execute();
$column_exists = $stmt->fetch();

if(!$column_exists) {
  setFlashMessage('error', 'Database needs to be updated. Please run the update script first.');
  header('Location: update-database.php');
  exit;
}

// Get team members
$team_members = getTeamMembers($pdo);

// Group team members by role for organized display
$grouped_members = [];
foreach ($team_members as $member) {
    if (!isset($grouped_members[$member['role']])) {
        $grouped_members[$member['role']] = [];
    }
    $grouped_members[$member['role']][] = $member;
}

// Define role order and descriptions
$role_order = ['admin', 'editor', 'writer'];
$role_descriptions = [
    'admin' => 'Administrators have full control over the website, including user management, content moderation, and site configuration.',
    'editor' => 'Editors review, edit, and publish content from writers. They ensure all articles meet our quality standards.',
    'writer' => 'Writers create engaging content across various categories. They research topics and craft informative articles.'
];

// Get article counts for each team member
$member_article_counts = [];
foreach ($team_members as $member) {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM articles WHERE user_id = ?");
    $stmt->execute([$member['id']]);
    $result = $stmt->fetch();
    $member_article_counts[$member['id']] = $result['count'];
}
?>

<?php include 'includes/header.php'; ?>

<main class="flex-grow py-10 bg-dark-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8 text-center">
            <h1 class="text-4xl font-serif font-bold text-white">Meet Our Team</h1>
            <p class="mt-3 text-xl text-dark-400 max-w-3xl mx-auto">The talented individuals behind Dragon's Den who work together to bring you the latest news and insights.</p>
        </div>
        
        <?php if(empty($team_members)): ?>
            <div class="bg-dark-900 rounded-xl p-8 text-center">
                <p class="text-dark-300">No team members found. Team members will appear here when administrators assign special roles to users.</p>
                <?php if(isAdmin()): ?>
                    <div class="mt-4">
                        <a href="admin/manage-users.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 focus:ring-offset-dark-950 transition-colors">
                            <i class="fas fa-user-cog mr-2"></i> Manage Users
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <!-- Team sections by role -->
            <?php foreach ($role_order as $role): ?>
                <?php if (isset($grouped_members[$role])): ?>
                    <section class="mb-16">
                        <div class="flex items-center mb-6">
                            <div class="mr-4">
                                <span class="inline-block px-3 py-1 text-sm font-medium rounded-md <?= getRoleBadgeClass($role) ?>">
                                    <?= getRoleDisplayName($role) ?>s
                                </span>
                            </div>
                            <div class="flex-grow h-px bg-dark-800"></div>
                        </div>
                        
                        <p class="text-dark-300 mb-8"><?= $role_descriptions[$role] ?? '' ?></p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                            <?php foreach($grouped_members[$role] as $member): ?>
                                <div class="bg-dark-900 rounded-xl shadow-lg overflow-hidden transform transition duration-300 hover:scale-105 hover:shadow-accent-900/20">
                                    <div class="p-6">
                                        <div class="flex items-center mb-4">
                                            <img src="https://www.gravatar.com/avatar/<?= md5(strtolower(trim($member['email']))) ?>?s=200&d=mp" 
                                                alt="<?= htmlspecialchars($member['username']) ?>" 
                                                class="w-20 h-20 rounded-full mr-4 <?= $role === 'admin' ? 'admin-border admin-glow' : '' ?>">
                                            
                                            <div>
                                                <h2 class="text-xl font-bold text-white"><?= htmlspecialchars($member['username']) ?></h2>
                                                <span class="inline-block px-3 py-1 text-xs font-medium rounded-full <?= getRoleBadgeClass($member['role']) ?> mt-1">
                                                    <?= getRoleDisplayName($member['role']) ?>
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <?php if($member['bio']): ?>
                                            <p class="text-dark-300 mb-4"><?= htmlspecialchars($member['bio']) ?></p>
                                        <?php else: ?>
                                            <p class="text-dark-300 mb-4">Team member at Dragon's Den.</p>
                                        <?php endif; ?>
                                        
                                        <div class="mt-6 flex items-center justify-between">
                                            <div class="text-dark-400 text-sm">
                                                <i class="fas fa-newspaper mr-1"></i> 
                                                <?= $member_article_counts[$member['id']] ?> article<?= $member_article_counts[$member['id']] !== 1 ? 's' : '' ?>
                                            </div>
                                            
                                            <a href="author.php?id=<?= $member['id'] ?>" 
                                               class="inline-flex items-center px-4 py-2 border border-dark-700 shadow-sm text-sm font-medium rounded-md text-dark-300 bg-dark-800 hover:bg-dark-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 focus:ring-offset-dark-900 transition-colors">
                                                View Profile
                                                <i class="fas fa-arrow-right ml-2"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>
            <?php endforeach; ?>
            
            <!-- Join the team section -->
            <?php if(!isLoggedIn() || (isset($_SESSION['role']) && $_SESSION['role'] === 'user')): ?>
                <div class="mt-12 bg-gradient-to-r from-dark-900 to-dark-800 rounded-xl p-8 shadow-lg">
                    <div class="md:flex items-center justify-between">
                        <div class="md:w-2/3">
                            <h2 class="text-2xl font-bold text-white mb-4">Want to Join Our Team?</h2>
                            <p class="text-dark-300 mb-6">
                                We're always looking for talented writers, editors, and contributors to join our team. 
                                If you're passionate about journalism and want to share your voice with our audience, 
                                we'd love to hear from you.
                            </p>
                        </div>
                        <div class="md:w-1/3 text-center md:text-right">
                            <?php if(!isLoggedIn()): ?>
                                <a href="register.php" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 focus:ring-offset-dark-900 transition-colors">
                                    Register Now
                                    <i class="fas fa-user-plus ml-2"></i>
                                </a>
                            <?php else: ?>
                                <a href="contact.php" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 focus:ring-offset-dark-900 transition-colors">
                                    Apply Now
                                    <i class="fas fa-paper-plane ml-2"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

