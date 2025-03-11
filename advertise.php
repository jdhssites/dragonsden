<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
session_start();
?>

<?php include 'includes/header.php'; ?>

<main class="flex-grow py-10 bg-dark-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Hero Section -->
        <div class="bg-gradient-to-r from-primary-900 to-primary-800 rounded-xl overflow-hidden shadow-xl mb-12">
            <div class="px-8 py-12 md:p-12">
                <h1 class="text-3xl md:text-4xl font-serif font-bold text-white mb-4">Advertise with Dragon's Den</h1>
                <p class="text-primary-100 text-lg mb-6 max-w-3xl">Connect with our engaged audience and boost your brand's visibility through strategic advertising partnerships.</p>
                <a href="#contact-form" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-primary-900 bg-white hover:bg-primary-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white focus:ring-offset-primary-800 transition-colors">
                    Get Started
                    <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
        
        <!-- Stats Section -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <div class="bg-dark-900 rounded-xl shadow-md p-8 text-center">
                <div class="text-4xl font-bold text-primary-500 mb-2">500K+</div>
                <div class="text-lg font-medium text-white mb-1">Monthly Visitors</div>
                <p class="text-dark-400">Unique readers visiting our platform each month</p>
            </div>
            
            <div class="bg-dark-900 rounded-xl shadow-md p-8 text-center">
                <div class="text-4xl font-bold text-primary-500 mb-2">2.5M+</div>
                <div class="text-lg font-medium text-white mb-1">Page Views</div>
                <p class="text-dark-400">Monthly page impressions across our website</p>
            </div>
            
            <div class="bg-dark-900 rounded-xl shadow-md p-8 text-center">
                <div class="text-4xl font-bold text-primary-500 mb-2">15 min</div>
                <div class="text-lg font-medium text-white mb-1">Avg. Time on Site</div>
                <p class="text-dark-400">Engaged audience spending quality time with content</p>
            </div>
        </div>
        
        <!-- Advertising Options -->
        <div class="mb-12">
            <h2 class="text-2xl font-serif font-bold text-white mb-6">Advertising Options</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="bg-dark-900 rounded-xl shadow-md overflow-hidden">
                    <div class="p-6">
                        <div class="w-12 h-12 rounded-full bg-primary-900 flex items-center justify-center mb-4">
                            <i class="fas fa-desktop text-primary-400 text-xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Display Advertising</h3>
                        <p class="text-dark-300 mb-4">Strategic banner placements throughout our website, targeting specific sections or the entire site.</p>
                        <ul class="space-y-2 text-dark-400">
                            <li class="flex items-start">
                                <i class="fas fa-check text-primary-500 mt-1 mr-2"></i>
                                <span>Premium banner positions</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-primary-500 mt-1 mr-2"></i>
                                <span>Category-specific targeting</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-primary-500 mt-1 mr-2"></i>
                                <span>Responsive designs for all devices</span>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="bg-dark-900 rounded-xl shadow-md overflow-hidden">
                    <div class="p-6">
                        <div class="w-12 h-12 rounded-full bg-primary-900 flex items-center justify-center mb-4">
                            <i class="fas fa-pen-fancy text-primary-400 text-xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Sponsored Content</h3>
                        <p class="text-dark-300 mb-4">Engaging branded articles and features that align with our editorial standards and your marketing goals.</p>
                        <ul class="space-y-2 text-dark-400">
                            <li class="flex items-start">
                                <i class="fas fa-check text-primary-500 mt-1 mr-2"></i>
                                <span>Native content integration</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-primary-500 mt-1 mr-2"></i>
                                <span>Professional content creation</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-primary-500 mt-1 mr-2"></i>
                                <span>Social media amplification</span>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="bg-dark-900 rounded-xl shadow-md overflow-hidden">
                    <div class="p-6">
                        <div class="w-12 h-12 rounded-full bg-primary-900 flex items-center justify-center mb-4">
                            <i class="fas fa-envelope-open-text text-primary-400 text-xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Newsletter Sponsorship</h3>
                        <p class="text-dark-300 mb-4">Reach our dedicated subscribers directly in their inbox with exclusive sponsorships of our popular newsletters.</p>
                        <ul class="space-y-2 text-dark-400">
                            <li class="flex items-start">
                                <i class="fas fa-check text-primary-500 mt-1 mr-2"></i>
                                <span>High open and click-through rates</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-primary-500 mt-1 mr-2"></i>
                                <span>Targeted audience segments</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-primary-500 mt-1 mr-2"></i>
                                <span>Performance analytics included</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Audience Section -->
        <div class="mb-12">
            <h2 class="text-2xl font-serif font-bold text-white mb-6">Our Audience</h2>
            
            <div class="bg-dark-900 rounded-xl shadow-md overflow-hidden">
                <div class="p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <h3 class="text-xl font-bold text-white mb-4">Demographics</h3>
                            <div class="space-y-4">
                                <div>
                                    <p class="text-dark-300 mb-1">Age Distribution</p>
                                    <div class="w-full bg-dark-800 rounded-full h-2.5">
                                        <div class="bg-primary-500 h-2.5 rounded-full" style="width: 35%"></div>
                                    </div>
                                    <div class="flex justify-between text-xs text-dark-400 mt-1">
                                        <span>18-24: 15%</span>
                                        <span>25-34: 35%</span>
                                        <span>35-44: 25%</span>
                                        <span>45+: 25%</span>
                                    </div>
                                </div>
                                
                                <div>
                                    <p class="text-dark-300 mb-1">Gender</p>
                                    <div class="flex">
                                        <div class="w-1/2 pr-2">
                                            <div class="w-full bg-dark-800 rounded-full h-2.5">
                                                <div class="bg-primary-500 h-2.5 rounded-full" style="width: 52%"></div>
                                            </div>
                                            <p class="text-xs text-dark-400 mt-1">Male: 52%</p>
                                        </div>
                                        <div class="w-1/2 pl-2">
                                            <div class="w-full bg-dark-800 rounded-full h-2.5">
                                                <div class="bg-accent-500 h-2.5 rounded-full" style="width: 48%"></div>
                                            </div>
                                            <p class="text-xs text-dark-400 mt-1">Female: 48%</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div>
                                    <p class="text-dark-300 mb-1">Education</p>
                                    <div class="w-full bg-dark-800 rounded-full h-2.5">
                                        <div class="bg-primary-500 h-2.5 rounded-full" style="width: 75%"></div>
                                    </div>
                                    <p class="text-xs text-dark-400 mt-1">75% have a bachelor's degree or higher</p>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-xl font-bold text-white mb-4">Interests</h3>
                            <div class="flex flex-wrap gap-2">
                                <span class="px-3 py-1 bg-dark-800 text-dark-300 rounded-full text-sm">Technology</span>
                                <span class="px-3 py-1 bg-dark-800 text-dark-300 rounded-full text-sm">Business</span>
                                <span class="px-3 py-1 bg-dark-800 text-dark-300 rounded-full text-sm">Politics</span>
                                <span class="px-3 py-1 bg-dark-800 text-dark-300 rounded-full text-sm">Science</span>
                                <span class="px-3 py-1 bg-dark-800 text-dark-300 rounded-full text-sm">Health</span>
                                <span class="px-3 py-1 bg-dark-800 text-dark-300 rounded-full text-sm">Travel</span>
                                <span class="px-3 py-1 bg-dark-800 text-dark-300 rounded-full text-sm">Entertainment</span>
                                <span class="px-3 py-1 bg-dark-800 text-dark-300 rounded-full text-sm">Sports</span>
                                <span class="px-3 py-1 bg-dark-800 text-dark-300 rounded-full text-sm">Finance</span>
                                <span class="px-3 py-1 bg-dark-800 text-dark-300 rounded-full text-sm">Education</span>
                            </div>
                            
                            <h3 class="text-xl font-bold text-white mt-6 mb-4">Device Usage</h3>
                            <div class="space-y-4">
                                <div>
                                    <div class="flex justify-between text-dark-300 mb-1">
                                        <span>Mobile</span>
                                        <span>65%</span>
                                    </div>
                                    <div class="w-full bg-dark-800 rounded-full h-2.5">
                                        <div class="bg-primary-500 h-2.5 rounded-full" style="width: 65%"></div>
                                    </div>
                                </div>
                                
                                <div>
                                    <div class="flex justify-between text-dark-300 mb-1">
                                        <span>Desktop</span>
                                        <span>30%</span>
                                    </div>
                                    <div class="w-full bg-dark-800 rounded-full h-2.5">
                                        <div class="bg-primary-500 h-2.5 rounded-full" style="width: 30%"></div>
                                    </div>
                                </div>
                                
                                <div>
                                    <div class="flex justify-between text-dark-300 mb-1">
                                        <span>Tablet</span>
                                        <span>5%</span>
                                    </div>
                                    <div class="w-full bg-dark-800 rounded-full h-2.5">
                                        <div class="bg-primary-500 h-2.5 rounded-full" style="width: 5%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Contact Form -->
        <div id="contact-form" class="bg-dark-900 rounded-xl shadow-md overflow-hidden">
            <div class="p-8">
                <h2 class="text-2xl font-serif font-bold text-white mb-6">Get in Touch</h2>
                <p class="text-dark-300 mb-6">Ready to advertise with us? Fill out the form below and our advertising team will contact you with more information.</p>
                
                <form class="space-y-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label for="ad-name" class="block text-sm font-medium text-white">Your Name</label>
                            <input type="text" name="ad-name" id="ad-name" required class="mt-1 block w-full rounded-md border-dark-700 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        </div>
                        
                        <div>
                            <label for="ad-company" class="block text-sm font-medium text-white">Company</label>
                            <input type="text" name="ad-company" id="ad-company" required class="mt-1 block w-full rounded-md border-dark-700 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        </div>
                        
                        <div>
                            <label for="ad-email" class="block text-sm font-medium text-white">Email Address</label>
                            <input type="email" name="ad-email" id="ad-email" required class="mt-1 block w-full rounded-md border-dark-700 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        </div>
                        
                        <div>
                            <label for="ad-phone" class="block text-sm font-medium text-white">Phone Number</label>
                            <input type="tel" name="ad-phone" id="ad-phone" class="mt-1 block w-full rounded-md border-dark-700 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        </div>
                        
                        <div class="sm:col-span-2">
                            <label for="ad-interest" class="block text-sm font-medium text-white">Interested In</label>
                            <select id="ad-interest" name="ad-interest" class="mt-1 block w-full rounded-md border-dark-700 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm text-dark-900">
                                <option value="">Select an option</option>
                                <option value="display">Display Advertising</option>
                                <option value="sponsored">Sponsored Content</option>
                                <option value="newsletter">Newsletter Sponsorship</option>
                                <option value="custom">Custom Partnership</option>
                            </select>
                        </div>
                        
                        <div class="sm:col-span-2">
                            <label for="ad-message" class="block text-sm font-medium text-white">Additional Information</label>
                            <textarea id="ad-message" name="ad-message" rows="4" class="mt-1 block w-full rounded-md border-dark-700 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"></textarea>
                        </div>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 focus:ring-offset-dark-900 transition-colors">
                            Submit Inquiry
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

