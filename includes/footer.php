<footer class="bg-dark-900 text-dark-300 mt-auto border-t border-dark-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div>
                    <a href="index.php" class="flex-shrink-0 flex items-center mb-4">
                        <span class="font-display text-2xl font-bold text-white">Dragon's <span class="text-primary-500">Den</span></span>
                    </a>
                    <p class="text-dark-400 mb-4">Your trusted source for breaking news, in-depth analysis, and global perspectives on the stories that matter.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-dark-400 hover:text-white transition-colors">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-dark-400 hover:text-white transition-colors">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-dark-400 hover:text-white transition-colors">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="text-dark-400 hover:text-white transition-colors">
                            <i class="fab fa-youtube"></i>
                        </a>
                        <a href="#" class="text-dark-400 hover:text-white transition-colors">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold text-white mb-4">Sections</h3>
                    <ul class="space-y-2">
                        <?php
                        // Get categories for footer
                        $stmt = $pdo->prepare("SELECT * FROM categories ORDER BY name LIMIT 5");
                        $stmt->execute();
                        $footer_categories = $stmt->fetchAll();
                        
                        foreach($footer_categories as $category):
                        ?>
                        <li>
                            <a href="category.php?id=<?= $category['id'] ?>" class="text-dark-400 hover:text-white transition-colors">
                                <?= htmlspecialchars($category['name']) ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold text-white mb-4">Company</h3>
                    <ul class="space-y-2">
                        <li><a href="about.php" class="text-dark-400 hover:text-white transition-colors">About Us</a></li>
                        <li><a href="contact.php" class="text-dark-400 hover:text-white transition-colors">Contact</a></li>
                        <li><a href="careers.php" class="text-dark-400 hover:text-white transition-colors">Careers</a></li>
                        <li><a href="advertise.php" class="text-dark-400 hover:text-white transition-colors">Advertise</a></li>
                        <li><a href="press.php" class="text-dark-400 hover:text-white transition-colors">Press Releases</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold text-white mb-4">Subscribe</h3>
                    <p class="text-dark-400 mb-4">Get the latest news delivered to your inbox daily.</p>
                    <form class="space-y-2">
                        <div>
                            <input type="email" placeholder="Your email address" class="newsletter-input w-full px-4 py-2 rounded-md bg-dark-800 border border-dark-700 text-white placeholder-dark-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        </div>
                        <button type="submit" class="w-full px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-dark-900">
                            Subscribe
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="border-t border-dark-700 mt-8 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-dark-400">&copy; <?= date('Y') ?> Dragon's Den. All rights reserved.</p>
                <div class="mt-4 md:mt-0 flex space-x-6">
                    <a href="privacy-policy.php" class="text-dark-400 hover:text-white transition-colors">Privacy Policy</a>
                    <a href="terms-of-service.php" class="text-dark-400 hover:text-white transition-colors">Terms of Service</a>
                    <a href="cookie-policy.php" class="text-dark-400 hover:text-white transition-colors">Cookie Policy</a>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    
    <!-- Custom JS -->
    <script>
        // Mobile menu toggle
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile menu
            const mobileMenuButton = document.querySelector('.mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            
            if (mobileMenuButton && mobileMenu) {
                mobileMenuButton.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');
                });
            }
            
            // Mobile submenu
            const mobileSubmenuButton = document.querySelector('.mobile-submenu-button');
            const mobileSubmenu = document.querySelector('.mobile-submenu');
            
            if (mobileSubmenuButton && mobileSubmenu) {
                mobileSubmenuButton.addEventListener('click', function() {
                    mobileSubmenu.classList.toggle('hidden');
                });
            }
            
            // Alert close
            const alertCloseButtons = document.querySelectorAll('.alert-close');
            
            alertCloseButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    const alert = this.closest('.alert-dismissible');
                    if (alert) {
                        alert.remove();
                    }
                });
            });
            
            // Auto-dismiss alerts after 5 seconds
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert-dismissible');
                alerts.forEach(function(alert) {
                    alert.remove();
                });
            }, 5000);
            
            // Image preview for upload
            const imageInput = document.getElementById('image');
            const imagePreview = document.getElementById('image-preview');
            
            if (imageInput && imagePreview) {
                imageInput.addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        const reader = new FileReader();
                        
                        reader.onload = function(e) {
                            imagePreview.src = e.target.result;
                            imagePreview.classList.remove('hidden');
                        };
                        
                        reader.readAsDataURL(this.files[0]);
                    }
                });
            }
            
            // Password strength meter
            const passwordInput = document.getElementById('new_password');
            const passwordStrength = document.getElementById('password-strength');
            
            if (passwordInput && passwordStrength) {
                passwordInput.addEventListener('input', function() {
                    const password = this.value;
                    let strength = 0;
                    
                    if (password.length >= 8) strength += 1;
                    if (password.match(/[a-z]+/)) strength += 1;
                    if (password.match(/[A-Z]+/)) strength += 1;
                    if (password.match(/[0-9]+/)) strength += 1;
                    if (password.match(/[^a-zA-Z0-9]+/)) strength += 1;
                    
                    // Update the strength meter
                    passwordStrength.style.width = (strength * 20) + '%';
                    
                    // Update the color and text based on strength
                    switch (strength) {
                        case 0:
                        case 1:
                            passwordStrength.className = 'bg-red-500 h-2 rounded-full';
                            document.getElementById('password-text').textContent = 'Very Weak';
                            document.getElementById('password-text').className = 'text-xs text-red-500';
                            break;
                        case 2:
                            passwordStrength.className = 'bg-yellow-500 h-2 rounded-full';
                            document.getElementById('password-text').textContent = 'Weak';
                            document.getElementById('password-text').className = 'text-xs text-yellow-500';
                            break;
                        case 3:
                            passwordStrength.className = 'bg-yellow-300 h-2 rounded-full';
                            document.getElementById('password-text').textContent = 'Medium';
                            document.getElementById('password-text').className = 'text-xs text-yellow-600';
                            break;
                        case 4:
                            passwordStrength.className = 'bg-green-400 h-2 rounded-full';
                            document.getElementById('password-text').textContent = 'Strong';
                            document.getElementById('password-text').className = 'text-xs text-green-500';
                            break;
                        case 5:
                            passwordStrength.className = 'bg-green-500 h-2 rounded-full';
                            document.getElementById('password-text').textContent = 'Very Strong';
                            document.getElementById('password-text').className = 'text-xs text-green-600';
                            break;
                    }
                });
            }
        });
    </script>
<?php include 'includes/chat-bubble.php'; ?>
</body>
</html>