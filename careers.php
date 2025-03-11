<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
session_start();
?>

<?php include 'includes/header.php'; ?>

<main class="flex-grow py-10 bg-dark-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Hero Section -->
        <div class="bg-dark-900 rounded-xl overflow-hidden shadow-xl mb-12">
            <div class="md:flex">
                <div class="md:flex-1 p-8 md:p-12 flex flex-col justify-center">
                    <h1 class="text-3xl md:text-4xl font-serif font-bold text-white mb-4">Join Our Team</h1>
                    <p class="text-lg text-dark-300 mb-6">Be part of a dynamic team that's shaping the future of digital journalism.</p>
                    <a href="#open-positions" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 focus:ring-offset-dark-900 transition-colors">
                        View Open Positions
                        <i class="fas fa-arrow-down ml-2"></i>
                    </a>
                </div>
                <div class="md:flex-1">
                    <img src="/placeholder.svg?height=400&width=600" alt="Dragon's Den Team" class="w-full h-full object-cover">
                </div>
            </div>
        </div>
        
        <!-- Why Join Us -->
        <div class="mb-12">
            <h2 class="text-2xl font-serif font-bold text-white mb-6">Why Join Dragon's Den?</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-dark-900 rounded-xl shadow-md p-6">
                    <div class="w-12 h-12 rounded-full bg-primary-900 flex items-center justify-center mb-4">
                        <i class="fas fa-lightbulb text-primary-400 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">Innovation</h3>
                    <p class="text-dark-300">We're at the forefront of digital journalism, constantly exploring new technologies and storytelling formats.</p>
                </div>
                
                <div class="bg-dark-900 rounded-xl shadow-md p-6">
                    <div class="w-12 h-12 rounded-full bg-primary-900 flex items-center justify-center mb-4">
                        <i class="fas fa-users text-primary-400 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">Inclusive Culture</h3>
                    <p class="text-dark-300">We celebrate diversity and create an environment where everyone's voice is heard and valued.</p>
                </div>
                
                <div class="bg-dark-900 rounded-xl shadow-md p-6">
                    <div class="w-12 h-12 rounded-full bg-primary-900 flex items-center justify-center mb-4">
                        <i class="fas fa-chart-line text-primary-400 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">Growth Opportunities</h3>
                    <p class="text-dark-300">We invest in our team's professional development with mentorship, training, and advancement paths.</p>
                </div>
            </div>
        </div>
        
        <!-- Benefits -->
        <div class="mb-12">
            <h2 class="text-2xl font-serif font-bold text-white mb-6">Benefits & Perks</h2>
            
            <div class="bg-dark-900 rounded-xl shadow-md overflow-hidden">
                <div class="p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <ul class="space-y-4">
                                <li class="flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-check-circle text-primary-500 mt-1 mr-3"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-medium text-white">Competitive Compensation</h4>
                                        <p class="text-dark-300">Salary packages that recognize your skills and experience</p>
                                    </div>
                                </li>
                                <li class="flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-check-circle text-primary-500 mt-1 mr-3"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-medium text-white">Health & Wellness</h4>
                                        <p class="text-dark-300">Comprehensive medical, dental, and vision coverage</p>
                                    </div>
                                </li>
                                <li class="flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-check-circle text-primary-500 mt-1 mr-3"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-medium text-white">Flexible Work</h4>
                                        <p class="text-dark-300">Remote and hybrid options with flexible scheduling</p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        
                        <div>
                            <ul class="space-y-4">
                                <li class="flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-check-circle text-primary-500 mt-1 mr-3"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-medium text-white">Professional Development</h4>
                                        <p class="text-dark-300">Learning stipends and career advancement opportunities</p>
                                    </div>
                                </li>
                                <li class="flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-check-circle text-primary-500 mt-1 mr-3"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-medium text-white">Paid Time Off</h4>
                                        <p class="text-dark-300">Generous vacation policy and paid holidays</p>
                                    </div>
                                </li>
                                <li class="flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-check-circle text-primary-500 mt-1 mr-3"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-medium text-white">Retirement Plans</h4>
                                        <p class="text-dark-300">401(k) with company matching contributions</p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Open Positions -->
        <div id="open-positions" class="mb-12">
            <h2 class="text-2xl font-serif font-bold text-white mb-6">Open Positions</h2>
            
            <div class="space-y-6">
                <div class="bg-dark-900 rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                    <div class="p-6">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                            <div>
                                <h3 class="text-xl font-bold text-white">Senior Journalist - Technology</h3>
                                <div class="flex flex-wrap gap-2 mt-2">
                                    <span class="px-2 py-1 bg-dark-800 text-dark-300 rounded-full text-xs">Full-time</span>
                                    <span class="px-2 py-1 bg-dark-800 text-dark-300 rounded-full text-xs">Remote</span>
                                </div>
                            </div>
                            <a href="#" class="mt-4 md:mt-0 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 focus:ring-offset-dark-900 transition-colors">
                                Apply Now
                            </a>
                        </div>
                        <p class="mt-4 text-dark-300">We're looking for an experienced technology journalist to cover breaking news, trends, and in-depth features in the tech industry.</p>
                    </div>
                </div>
                
                <div class="bg-dark-900 rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                    <div class="p-6">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                            <div>
                                <h3 class="text-xl font-bold text-white">Digital Marketing Manager</h3>
                                <div class="flex flex-wrap gap-2 mt-2">
                                    <span class="px-2 py-1 bg-dark-800 text-dark-300 rounded-full text-xs">Full-time</span>
                                    <span class="px-2 py-1 bg-dark-800 text-dark-300 rounded-full text-xs">Hybrid</span>
                                </div>
                            </div>
                            <a href="#" class="mt-4 md:mt-0 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 focus:ring-offset-dark-900 transition-colors">
                                Apply Now
                            </a>
                        </div>
                        <p class="mt-4 text-dark-300">Join our marketing team to develop and implement digital strategies that grow our audience and enhance our brand presence.</p>
                    </div>
                </div>
                
                <div class="bg-dark-900 rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                    <div class="p-6">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                            <div>
                                <h3 class="text-xl font-bold text-white">Full Stack Developer</h3>
                                <div class="flex flex-wrap gap-2 mt-2">
                                    <span class="px-2 py-1 bg-dark-800 text-dark-300 rounded-full text-xs">Full-time</span>
                                    <span class="px-2 py-1 bg-dark-800 text-dark-300 rounded-full text-xs">On-site</span>
                                </div>
                            </div>
                            <a href="#" class="mt-4 md:mt-0 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 focus:ring-offset-dark-900 transition-colors">
                                Apply Now
                            </a>
                        </div>
                        <p class="mt-4 text-dark-300">We're seeking a talented developer to help build and maintain our digital platforms, with experience in PHP, JavaScript, and modern web frameworks.</p>
                    </div>
                </div>
            </div>
            
            <div class="mt-8 text-center">
                <p class="text-dark-300 mb-4">Don't see a position that matches your skills?</p>
                <a href="#" class="inline-flex items-center px-4 py-2 border border-dark-700 shadow-sm text-sm font-medium rounded-md text-dark-300 bg-dark-800 hover:bg-dark-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 focus:ring-offset-dark-900 transition-colors">
                    Send us your resume
                </a>
            </div>
        </div>
        
        <!-- Application Process -->
        <div class="mb-12">
            <h2 class="text-2xl font-serif font-bold text-white mb-6">Our Application Process</h2>
            
            <div class="bg-dark-900 rounded-xl shadow-md overflow-hidden">
                <div class="p-8">
                    <ol class="relative border-l border-dark-700">
                        <li class="mb-10 ml-6">
                            <span class="absolute flex items-center justify-center w-8 h-8 bg-primary-900 rounded-full -left-4 ring-4 ring-dark-900">
                                <span class="text-primary-300 font-bold">1</span>
                            </span>
                            <h3 class="text-lg font-bold text-white mb-1">Application Review</h3>
                            <p class="text-dark-300">Our team reviews your application and resume to assess your qualifications and experience.</p>
                        </li>
                        <li class="mb-10 ml-6">
                            <span class="absolute flex items-center justify-center w-8 h-8 bg-primary-900 rounded-full -left-4 ring-4 ring-dark-900">
                                <span class="text-primary-300 font-bold">2</span>
                            </span>
                            <h3 class="text-lg font-bold text-white mb-1">Initial Interview</h3>
                            <p class="text-dark-300">A video call with our HR team to discuss your background, skills, and interest in the role.</p>
                        </li>
                        <li class="mb-10 ml-6">
                            <span class="absolute flex items-center justify-center w-8 h-8 bg-primary-900 rounded-full -left-4 ring-4 ring-dark-900">
                                <span class="text-primary-300 font-bold">3</span>
                            </span>
                            <h3 class="text-lg font-bold text-white mb-1">Skills Assessment</h3>
                            <p class="text-dark-300">Depending on the role, you may be asked to complete a skills test or assignment relevant to the position.</p>
                        </li>
                        <li class="mb-10 ml-6">
                            <span class="absolute flex items-center justify-center w-8 h-8 bg-primary-900 rounded-full -left-4 ring-4 ring-dark-900">
                                <span class="text-primary-300 font-bold">4</span>
                            </span>
                            <h3 class="text-lg font-bold text-white mb-1">Team Interview</h3>
                            <p class="text-dark-300">Meet with potential team members and managers to discuss the role in more detail and assess team fit.</p>
                        </li>
                        <li class="ml-6">
                            <span class="absolute flex items-center justify-center w-8 h-8 bg-primary-900 rounded-full -left-4 ring-4 ring-dark-900">
                                <span class="text-primary-300 font-bold">5</span>
                            </span>
                            <h3 class="text-lg font-bold text-white mb-1">Offer & Onboarding</h3>
                            <p class="text-dark-300">If selected, you'll receive an offer and begin our comprehensive onboarding process.</p>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

