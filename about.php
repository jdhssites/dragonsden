<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
session_start();
?>

<?php include 'includes/header.php'; ?>

<main class="flex-grow py-10 bg-dark-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-dark-900 rounded-xl shadow-md overflow-hidden">
            <div class="md:flex">
                <div class="md:flex-shrink-0 md:w-1/3">
                    <img src="/placeholder.svg?height=600&width=800" alt="About Dragon's Den" class="h-full w-full object-cover">
                </div>
                <div class="p-8 md:p-10">
                    <h1 class="text-3xl font-serif font-bold text-white mb-6">About Dragon's Den</h1>
                    
                    <div class="prose prose-lg max-w-none text-dark-300">
                        <p>Dragon's Den is a premier news platform dedicated to delivering accurate, timely, and insightful news coverage across a wide range of topics. Founded in 2023, we've quickly established ourselves as a trusted source for breaking news, in-depth analysis, and thought-provoking commentary.</p>
                        
                        <h2 class="text-2xl font-serif font-bold text-white mt-8 mb-4">Our Mission</h2>
                        <p>At Dragon's Den, our mission is to empower readers with knowledge through quality journalism. We believe in the power of well-researched, balanced reporting to help people make informed decisions about the world around them.</p>
                        
                        <h2 class="text-2xl font-serif font-bold text-white mt-8 mb-4">Our Values</h2>
                        <ul class="list-disc pl-5 space-y-2">
                            <li><strong class="text-white">Accuracy:</strong> We verify facts and present information with precision and clarity.</li>
                            <li><strong class="text-white">Independence:</strong> We maintain editorial independence and report without fear or favor.</li>
                            <li><strong class="text-white">Fairness:</strong> We present multiple perspectives and treat all subjects with respect.</li>
                            <li><strong class="text-white">Transparency:</strong> We are open about our methods and correct our mistakes promptly.</li>
                            <li><strong class="text-white">Innovation:</strong> We embrace new technologies and storytelling formats to better serve our audience.</li>
                        </ul>
                        
                        <h2 class="text-2xl font-serif font-bold text-white mt-8 mb-4">Our Team</h2>
                        <p>Dragon's Den is powered by a diverse team of experienced journalists, editors, and digital media professionals. Our contributors bring expertise from various fields, ensuring comprehensive coverage across all our news categories.</p>
                        
                        <h2 class="text-2xl font-serif font-bold text-white mt-8 mb-4">Contact Us</h2>
                        <p>We value your feedback and are always open to story ideas, tips, and suggestions. Please visit our <a href="contact.php" class="text-primary-400 hover:text-primary-300">Contact page</a> to get in touch with our team.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Team Section -->
        <div class="mt-12">
            <h2 class="text-2xl font-serif font-bold text-white mb-6">Leadership Team</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-dark-900 rounded-xl shadow-md overflow-hidden">
                    <img src="/placeholder.svg?height=300&width=300" alt="Team Member" class="w-full h-64 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-white">Jane Doe</h3>
                        <p class="text-primary-400 mb-2">Editor-in-Chief</p>
                        <p class="text-dark-300">With over 20 years of journalism experience, Jane leads our editorial vision and strategy.</p>
                    </div>
                </div>
                
                <div class="bg-dark-900 rounded-xl shadow-md overflow-hidden">
                    <img src="/placeholder.svg?height=300&width=300" alt="Team Member" class="w-full h-64 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-white">John Smith</h3>
                        <p class="text-primary-400 mb-2">Managing Editor</p>
                        <p class="text-dark-300">John oversees our day-to-day operations and ensures the highest quality standards across all content.</p>
                    </div>
                </div>
                
                <div class="bg-dark-900 rounded-xl shadow-md overflow-hidden">
                    <img src="/placeholder.svg?height=300&width=300" alt="Team Member" class="w-full h-64 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-white">Sarah Johnson</h3>
                        <p class="text-primary-400 mb-2">Technology Director</p>
                        <p class="text-dark-300">Sarah leads our digital strategy and technological innovation to enhance user experience.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

