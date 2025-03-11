<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
session_start();
?>

<?php include 'includes/header.php'; ?>

<main class="flex-grow py-10 bg-dark-950">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-dark-900 rounded-xl shadow-md overflow-hidden">
            <div class="p-8">
                <h1 class="text-3xl font-serif font-bold text-white mb-6">Privacy Policy</h1>
                <p class="text-dark-300 mb-4">Last Updated: March 1, 2023</p>
                
                <div class="prose prose-lg max-w-none text-dark-300">
                    <p>At Dragon's Den, we take your privacy seriously. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our website or use our services.</p>
                    
                    <h2 class="text-2xl font-serif font-bold text-white mt-8 mb-4">Information We Collect</h2>
                    
                    <h3 class="text-xl font-bold text-white mt-6 mb-3">Personal Information</h3>
                    <p>We may collect personal information that you voluntarily provide to us when you register on our website, express interest in obtaining information about us or our products and services, participate in activities on our website, or otherwise contact us. The personal information we collect may include:</p>
                    <ul class="list-disc pl-5 space-y-2">
                        <li>Name and contact information (email address, phone number)</li>
                        <li>Account credentials (username, password)</li>
                        <li>Profile information (profile picture, bio)</li>
                        <li>Content you publish on our platform</li>
                        <li>Payment information for subscriptions or purchases</li>
                    </ul>
                    
                    <h3 class="text-xl font-bold text-white mt-6 mb-3">Automatically Collected Information</h3>
                    <p>When you visit our website, we may automatically collect certain information about your device and usage patterns. This information may include:</p>
                    <ul class="list-disc pl-5 space-y-2">
                        <li>IP address and device identifiers</li>
                        <li>Browser type and version</li>
                        <li>Operating system</li>
                        <li>Pages visited and time spent on pages</li>
                        <li>Referral sources</li>
                        <li>Geographic location (country, city)</li>
                    </ul>
                    
                    <h2 class="text-2xl font-serif font-bold text-white mt-8 mb-4">How We Use Your Information</h2>
                    <p>We may use the information we collect for various purposes, including:</p>
                    <ul class="list-disc pl-5 space-y-2">
                        <li>Providing, maintaining, and improving our services</li>
                        <li>Processing transactions and sending related information</li>
                        <li>Responding to inquiries and providing customer support</li>
                        <li>Sending administrative information and updates</li>
                        <li>Sending marketing and promotional communications (with your consent)</li>
                        <li>Personalizing your experience on our website</li>
                        <li>Analyzing usage patterns to improve our website and services</li>
                        <li>Protecting our website and services from unauthorized access</li>
                    </ul>
                    
                    <h2 class="text-2xl font-serif font-bold text-white mt-8 mb-4">Cookies and Tracking Technologies</h2>
                    <p>We use cookies and similar tracking technologies to collect information about your browsing activities. Cookies are small text files stored on your device that help us provide a better user experience. You can set your browser to refuse all or some browser cookies, but this may limit your ability to use certain features of our website.</p>
                    <p>We use the following types of cookies:</p>
                    <ul class="list-disc pl-5 space-y-2">
                        <li><strong class="text-white">Essential cookies:</strong> Necessary for the website to function properly</li>
                        <li><strong class="text-white">Preference cookies:</strong> Enable the website to remember your preferences</li>
                        <li><strong class="text-white">Analytics cookies:</strong> Help us understand how visitors interact with our website</li>
                        <li><strong class="text-white">Marketing cookies:</strong> Used to track visitors across websites for advertising purposes</li>
                    </ul>
                    
                    <h2 class="text-2xl font-serif font-bold text-white mt-8 mb-4">Disclosure of Your Information</h2>
                    <p>We may share your information in the following situations:</p>
                    <ul class="list-disc pl-5 space-y-2">
                        <li><strong class="text-white">With service providers:</strong> We may share your information with third-party vendors who provide services on our behalf.</li>
                        <li><strong class="text-white">Business transfers:</strong> If we are involved in a merger, acquisition, or sale of assets, your information may be transferred as part of that transaction.</li>
                        <li><strong class="text-white">Legal requirements:</strong> We may disclose your information if required to do so by law or in response to valid requests by public authorities.</li>
                        <li><strong class="text-white">With your consent:</strong> We may share your information with third parties when you have given us your consent to do so.</li>
                    </ul>
                    
                    <h2 class="text-2xl font-serif font-bold text-white mt-8 mb-4">Data Security</h2>
                    <p>We implement appropriate technical and organizational measures to protect your personal information from unauthorized access, disclosure, alteration, or destruction. However, no method of transmission over the Internet or electronic storage is 100% secure, and we cannot guarantee absolute security.</p>
                    
                    <h2 class="text-2xl font-serif font-bold text-white mt-8 mb-4">Your Privacy Rights</h2>
                    <p>Depending on your location, you may have certain rights regarding your personal information, including:</p>
                    <ul class="list-disc pl-5 space-y-2">
                        <li>The right to access your personal information</li>
                        <li>The right to correct inaccurate or incomplete information</li>
                        <li>The right to request deletion of your personal information</li>
                        <li>The right to restrict or object to processing of your information</li>
                        <li>The right to data portability</li>
                        <li>The right to withdraw consent</li>
                    </ul>
                    <p>To exercise these rights, please contact us using the information provided in the "Contact Us" section below.</p>
                    
                    <h2 class="text-2xl font-serif font-bold text-white mt-8 mb-4">Changes to This Privacy Policy</h2>
                    <p>We may update this Privacy Policy from time to time to reflect changes in our practices or for other operational, legal, or regulatory reasons. We will notify you of any material changes by posting the new Privacy Policy on this page and updating the "Last Updated" date.</p>
                    
                    <h2 class="text-2xl font-serif font-bold text-white mt-8 mb-4">Contact Us</h2>
                    <p>If you have questions or concerns about this Privacy Policy or our privacy practices, please contact us at:</p>
                    <p>Email: privacy@dragonsden.com</p>
                    <p>Address: 123 News Street, Media City, CA 90210, United States</p>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

