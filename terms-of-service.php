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
                <h1 class="text-3xl font-serif font-bold text-white mb-6">Terms of Service</h1>
                <p class="text-dark-300 mb-4">Last Updated: March 1, 2023</p>
                
                <div class="prose prose-lg max-w-none text-dark-300">
                    <p>Welcome to Dragon's Den. These Terms of Service ("Terms") govern your access to and use of our website, services, and content. By accessing or using our services, you agree to be bound by these Terms.</p>
                    
                    <h2 class="text-2xl font-serif font-bold text-white mt-8 mb-4">1. Acceptance of Terms</h2>
                    <p>By accessing or using our website, you acknowledge that you have read, understood, and agree to be bound by these Terms. If you do not agree to these Terms, you must not access or use our services.</p>
                    
                    <h2 class="text-2xl font-serif font-bold text-white mt-8 mb-4">2. Changes to Terms</h2>
                    <p>We reserve the right to modify these Terms at any time. We will provide notice of significant changes by posting the updated Terms on our website and updating the "Last Updated" date. Your continued use of our services after such changes constitutes your acceptance of the new Terms.</p>
                    
                    <h2 class="text-2xl font-serif font-bold text-white mt-8 mb-4">3. Account Registration</h2>
                    <p>To access certain features of our website, you may need to register for an account. When you register, you agree to provide accurate, current, and complete information and to update this information to maintain its accuracy. You are responsible for maintaining the confidentiality of your account credentials and for all activities that occur under your account.</p>
                    
                    <h2 class="text-2xl font-serif font-bold text-white mt-8 mb-4">4. User Content</h2>
                    <p>Our services may allow you to post, submit, or publish content, including comments, articles, and other materials. By submitting content to our website, you grant us a worldwide, non-exclusive, royalty-free license to use, reproduce, modify, adapt, publish, translate, distribute, and display such content in any media.</p>
                    <p>You represent and warrant that:</p>
                    <ul class="list-disc pl-5 space-y-2">
                        <li>You own or have the necessary rights to the content you submit</li>
                        <li>Your content does not violate the privacy rights, publicity rights, copyright, contractual rights, or any other rights of any person or entity</li>
                        <li>Your content does not contain material that is false, intentionally misleading, defamatory, obscene, harassing, threatening, or otherwise unlawful</li>
                    </ul>
                    
                    <h2 class="text-2xl font-serif font-bold text-white mt-8 mb-4">5. Prohibited Conduct</h2>
                    <p>You agree not to:</p>
                    <ul class="list-disc pl-5 space-y-2">
                        <li>Use our services in any way that violates any applicable law or regulation</li>
                        <li>Impersonate any person or entity or falsely state or misrepresent your affiliation with a person or entity</li>
                        <li>Engage in any conduct that restricts or inhibits anyone's use or enjoyment of our services</li>
                        <li>Attempt to gain unauthorized access to our systems or user accounts</li>
                        <li>Use our services to transmit any malware, viruses, or other malicious code</li>
                        <li>Harvest or collect email addresses or other contact information of users</li>
                        <li>Use our services for any commercial solicitation purposes without our consent</li>
                    </ul>
                    
                    <h2 class="text-2xl font-serif font-bold text-white mt-8 mb-4">6. Intellectual Property</h2>
                    <p>Our website and its original content, features, and functionality are owned by Dragon's Den and are protected by international copyright, trademark, patent, trade secret, and other intellectual property laws. You may not copy, modify, create derivative works, publicly display, publicly perform, republish, or transmit any material from our website without our prior written consent.</p>
                    
                    <h2 class="text-2xl font-serif font-bold text-white mt-8 mb-4">7. Third-Party Links</h2>
                    <p>Our website may contain links to third-party websites or services that are not owned or controlled by Dragon's Den. We have no control over, and assume no responsibility for, the content, privacy policies, or practices of any third-party websites or services. You acknowledge and agree that we shall not be responsible or liable for any damage or loss caused by your use of any such website or service.</p>
                    
                    <h2 class="text-2xl font-serif font-bold text-white mt-8 mb-4">8. Termination</h2>
                    <p>We may terminate or suspend your account and access to our services immediately, without prior notice or liability, for any reason, including if you breach these Terms. Upon termination, your right to use our services will immediately cease.</p>
                    
                    <h2 class="text-2xl font-serif font-bold text-white mt-8 mb-4">9. Disclaimer of Warranties</h2>
                    <p>Our services are provided on an "as is" and "as available" basis. Dragon's Den makes no warranties, expressed or implied, regarding the operation of our services or the information, content, or materials included therein.</p>
                    
                    <h2 class="text-2xl font-serif font-bold text-white mt-8 mb-4">10. Limitation of Liability</h2>
                    <p>In no event shall Dragon's Den, its directors, employees, partners, agents, suppliers, or affiliates be liable for any indirect, incidental, special, consequential, or punitive damages, including without limitation, loss of profits, data, use, goodwill, or other intangible losses, resulting from your access to or use of or inability to access or use our services.</p>
                    
                    <h2 class="text-2xl font-serif font-bold text-white mt-8 mb-4">11. Governing Law</h2>
                    <p>These Terms shall be governed by and construed in accordance with the laws of the State of California, without regard to its conflict of law provisions. Any dispute arising from these Terms shall be resolved exclusively in the courts located in Los Angeles County, California.</p>
                    
                    <h2 class="text-2xl font-serif font-bold text-white mt-8 mb-4">12. Contact Us</h2>
                    <p>If you have any questions about these Terms, please contact us at:</p>
                    <p>Email: legal@dragonsden.com</p>
                    <p>Address: 123 News Street, Media City, CA 90210, United States</p>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

