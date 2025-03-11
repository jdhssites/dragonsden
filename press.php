<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
session_start();
?>

<?php include 'includes/header.php'; ?>

<main class="flex-grow py-10 bg-dark-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-3xl font-serif font-bold text-white">Press Releases</h1>
            <p class="mt-2 text-dark-400">Latest news and announcements from Dragon's Den</p>
        </div>
        
        <div class="space-y-8">
            <!-- Press Release 1 -->
            <div class="bg-dark-900 rounded-xl shadow-md overflow-hidden">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row md:items-start">
                        <div class="md:flex-shrink-0 md:mr-6 mb-4 md:mb-0">
                            <span class="inline-block bg-dark-800 rounded-md px-3 py-1 text-sm text-dark-300">March 15, 2023</span>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-white mb-2">Dragon's Den Launches Redesigned Website with Enhanced User Experience</h2>
                            <p class="text-dark-300 mb-4">
                                Dragon's Den, a leading digital news platform, today announced the launch of its completely redesigned website, featuring a modern dark-themed interface, improved navigation, and enhanced content discovery tools.
                            </p>
                            <div class="flex items-center">
                                <a href="#" class="text-primary-400 hover:text-primary-300 transition-colors">
                                    Read full press release <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Press Release 2 -->
            <div class="bg-dark-900 rounded-xl shadow-md overflow-hidden">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row md:items-start">
                        <div class="md:flex-shrink-0 md:mr-6 mb-4 md:mb-0">
                            <span class="inline-block bg-dark-800 rounded-md px-3 py-1 text-sm text-dark-300">February 8, 2023</span>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-white mb-2">Dragon's Den Expands Editorial Team with Industry-Leading Journalists</h2>
                            <p class="text-dark-300 mb-4">
                                Dragon's Den today announced the expansion of its editorial team with the addition of five award-winning journalists, strengthening its coverage across technology, business, and international news sectors.
                            </p>
                            <div class="flex items-center">
                                <a href="#" class="text-primary-400 hover:text-primary-300 transition-colors">
                                    Read full press release <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Press Release 3 -->
            <div class="bg-dark-900 rounded-xl shadow-md overflow-hidden">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row md:items-start">
                        <div class="md:flex-shrink-0 md:mr-6 mb-4 md:mb-0">
                            <span class="inline-block bg-dark-800 rounded-md px-3 py-1 text-sm text-dark-300">January 20, 2023</span>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-white mb-2">Dragon's Den Announces Strategic Partnership with Global Analytics Firm</h2>
                            <p class="text-dark-300 mb-4">
                                Dragon's Den has entered into a strategic partnership with DataInsight Analytics to enhance its data-driven reporting capabilities and provide readers with deeper insights into complex global issues.
                            </p>
                            <div class="flex items-center">
                                <a href="#" class="text-primary-400 hover:text-primary-300 transition-colors">
                                    Read full press release <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Press Release 4 -->
            <div class="bg-dark-900 rounded-xl shadow-md overflow-hidden">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row md:items-start">
                        <div class="md:flex-shrink-0 md:mr-6 mb-4 md:mb-0">
                            <span class="inline-block bg-dark-800 rounded-md px-3 py-1 text-sm text-dark-300">December 5, 2022</span>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-white mb-2">Dragon's Den Wins Excellence in Digital Journalism Award</h2>
                            <p class="text-dark-300 mb-4">
                                Dragon's Den has been recognized with the prestigious Excellence in Digital Journalism Award for its innovative approach to multimedia storytelling and commitment to factual reporting.
                            </p>
                            <div class="flex items-center">
                                <a href="#" class="text-primary-400 hover:text-primary-300 transition-colors">
                                    Read full press release <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Media Contact -->
        <div class="mt-12 bg-dark-900 rounded-xl shadow-md overflow-hidden">
            <div class="p-8">
                <h2 class="text-2xl font-serif font-bold text-white mb-4">Media Contact</h2>
                <div class="flex flex-col md:flex-row md:items-center">
                    <div class="md:w-1/2 mb-6 md:mb-0">
                        <p class="text-dark-300 mb-4">For press inquiries, interview requests, or additional information, please contact our media relations team.</p>
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <i class="fas fa-user text-primary-500 w-6"></i>
                                <span class="text-white ml-2">Sarah Johnson</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-briefcase text-primary-500 w-6"></i>
                                <span class="text-white ml-2">Director of Communications</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-envelope text-primary-500 w-6"></i>
                                <span class="text-white ml-2">press@dragonsden.com</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-phone text-primary-500 w-6"></i>
                                <span class="text-white ml-2">+1 (555) 123-4567</span>
                            </div>
                        </div>
                    </div>
                    <div class="md:w-1/2 md:pl-8 md:border-l md:border-dark-700">
                        <h3 class="text-lg font-bold text-white mb-4">Download Media Kit</h3>
                        <p class="text-dark-300 mb-4">Access our media kit containing logos, brand guidelines, executive bios, and high-resolution images.</p>
                        <a href="#" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 focus:ring-offset-dark-900 transition-colors">
                            <i class="fas fa-download mr-2"></i> Download Media Kit
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

