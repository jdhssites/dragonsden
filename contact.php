<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
session_start();

$errors = [];
$success = false;

// Process contact form submission
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);
    
    // Validation
    if(empty($name)) {
        $errors[] = "Name is required";
    }
    
    if(empty($email)) {
        $errors[] = "Email is required";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    if(empty($subject)) {
        $errors[] = "Subject is required";
    }
    
    if(empty($message)) {
        $errors[] = "Message is required";
    }
    
    // If no errors, process the form
    if(empty($errors)) {
        // In a real application, you would send an email or store the message in a database
        // For this demo, we'll just set a success flag
        $success = true;
        
        // Clear form fields after successful submission
        $name = $email = $subject = $message = '';
    }
}
?>

<?php include 'includes/header.php'; ?>

<main class="flex-grow py-10 bg-dark-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-3xl font-serif font-bold text-white">Contact Us</h1>
            <p class="mt-2 text-dark-400">We'd love to hear from you. Get in touch with our team.</p>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <div class="bg-dark-900 rounded-xl shadow-md overflow-hidden">
                    <div class="p-8">
                        <?php if($success): ?>
                            <div class="mb-6 bg-green-900 border border-green-700 text-green-100 rounded-lg p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-check-circle text-green-400"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm">Thank you for your message! We'll get back to you as soon as possible.</p>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if(!empty($errors)): ?>
                            <div class="mb-6 bg-red-900 border border-red-700 text-red-100 rounded-lg p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-exclamation-circle text-red-400"></i>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-red-100">There were errors with your submission</h3>
                                        <div class="mt-2 text-sm text-red-200">
                                            <ul class="list-disc pl-5 space-y-1">
                                                <?php foreach($errors as $error): ?>
                                                    <li><?= $error ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <form action="contact.php" method="POST" class="space-y-6">
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-white">Your Name</label>
                                    <input type="text" name="name" id="name" value="<?= htmlspecialchars($name ?? '') ?>" required class="mt-1 block w-full rounded-md border-dark-700 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                </div>
                                
                                <div>
                                    <label for="email" class="block text-sm font-medium text-white">Email Address</label>
                                    <input type="email" name="email" id="email" value="<?= htmlspecialchars($email ?? '') ?>" required class="mt-1 block w-full rounded-md border-dark-700 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                </div>
                                
                                <div class="sm:col-span-2">
                                    <label for="subject" class="block text-sm font-medium text-white">Subject</label>
                                    <input type="text" name="subject" id="subject" value="<?= htmlspecialchars($subject ?? '') ?>" required class="mt-1 block w-full rounded-md border-dark-700 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                </div>
                                
                                <div class="sm:col-span-2">
                                    <label for="message" class="block text-sm font-medium text-white">Message</label>
                                    <textarea name="message" id="message" rows="6" required class="mt-1 block w-full rounded-md border-dark-700 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"><?= htmlspecialchars($message ?? '') ?></textarea>
                                </div>
                            </div>
                            
                            <div class="flex justify-end">
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 focus:ring-offset-dark-900 transition-colors">
                                    Send Message
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="lg:col-span-1">
                <div class="bg-dark-900 rounded-xl shadow-md overflow-hidden">
                    <div class="p-8">
                        <h2 class="text-xl font-bold text-white mb-4">Contact Information</h2>
                        
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 mt-1">
                                    <i class="fas fa-map-marker-alt text-primary-500"></i>
                                </div>
                                <div class="ml-3 text-dark-300">
                                    <p>123 News Street</p>
                                    <p>Media City, CA 90210</p>
                                    <p>United States</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-phone text-primary-500"></i>
                                </div>
                                <div class="ml-3 text-dark-300">
                                    <p>+1 (555) 123-4567</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-envelope text-primary-500"></i>
                                </div>
                                <div class="ml-3 text-dark-300">
                                    <p>info@dragonsden.com</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-8">
                            <h3 class="text-lg font-medium text-white mb-4">Follow Us</h3>
                            <div class="flex space-x-4">
                                <a href="#" class="text-dark-400 hover:text-primary-500 transition-colors">
                                    <i class="fab fa-facebook-f text-xl"></i>
                                </a>
                                <a href="#" class="text-dark-400 hover:text-primary-500 transition-colors">
                                    <i class="fab fa-twitter text-xl"></i>
                                </a>
                                <a href="#" class="text-dark-400 hover:text-primary-500 transition-colors">
                                    <i class="fab fa-instagram text-xl"></i>
                                </a>
                                <a href="#" class="text-dark-400 hover:text-primary-500 transition-colors">
                                    <i class="fab fa-linkedin-in text-xl"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6 bg-dark-900 rounded-xl shadow-md overflow-hidden">
                    <div class="p-8">
                        <h2 class="text-xl font-bold text-white mb-4">Business Hours</h2>
                        <div class="space-y-2 text-dark-300">
                            <div class="flex justify-between">
                                <span>Monday - Friday:</span>
                                <span>9:00 AM - 6:00 PM</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Saturday:</span>
                                <span>10:00 AM - 4:00 PM</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Sunday:</span>
                                <span>Closed</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

