<?php
// Database setup script
require_once 'config/database.php';
require_once 'helpers/utils.php';

// Create database tables
function setupDatabase() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Create database if it doesn't exist
    $sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
    if ($conn->query($sql) === FALSE) {
        die("Error creating database: " . $conn->error);
    }
    
    // Close connection and reconnect to the new database
    $conn->close();
    $conn = getDbConnection();
    
    // Create users table
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT(11) NOT NULL AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        is_admin TINYINT(1) NOT NULL DEFAULT 0,
        avatar VARCHAR(255) DEFAULT '/placeholder.svg?height=100&width=100',
        bio TEXT,
        theme VARCHAR(50) DEFAULT 'system',
        email_notifications TINYINT(1) DEFAULT 1,
        dark_mode TINYINT(1) DEFAULT 0,
        language VARCHAR(10) DEFAULT 'en',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    )";
    
    if ($conn->query($sql) === FALSE) {
        die("Error creating users table: " . $conn->error);
    }
    
    // Create articles table
    $sql = "CREATE TABLE IF NOT EXISTS articles (
        id INT(11) NOT NULL AUTO_INCREMENT,
        title VARCHAR(255) NOT NULL,
        slug VARCHAR(255) NOT NULL UNIQUE,
        excerpt TEXT NOT NULL,
        content TEXT NOT NULL,
        image VARCHAR(255) DEFAULT '/placeholder.svg?height=600&width=800',
        date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        read_time INT(11) DEFAULT 5,
        category VARCHAR(100) NOT NULL,
        author_id INT(11) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        FOREIGN KEY (author_id) REFERENCES users(id)
    )";
    
    if ($conn->query($sql) === FALSE) {
        die("Error creating articles table: " . $conn->error);
    }
    
    // Create admin user if it doesn't exist
    $adminEmail = 'admin@example.com';
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $adminEmail);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $adminName = 'Admin';
        $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $isAdmin = 1;
        $adminAvatar = '/placeholder.svg?height=100&width=100';
        
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, is_admin, avatar) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssis", $adminName, $adminEmail, $adminPassword, $isAdmin, $adminAvatar);
        
        if ($stmt->execute() === FALSE) {
            die("Error creating admin user: " . $stmt->error);
        }
        
        $adminId = $conn->insert_id;
        
        // Create sample articles
        $articles = [
            [
                'title' => 'The Future of Artificial Intelligence in Healthcare',
                'excerpt' => 'Exploring how AI is revolutionizing medical diagnostics, treatment plans, and patient care.',
                'content' => "Artificial Intelligence (AI) is rapidly transforming the healthcare industry, offering unprecedented opportunities to improve patient outcomes, reduce costs, and enhance the efficiency of medical services.\n\nOne of the most promising applications of AI in healthcare is in medical diagnostics. Machine learning algorithms can analyze medical images such as X-rays, MRIs, and CT scans with remarkable accuracy, often detecting subtle abnormalities that might be missed by human radiologists. For example, AI systems have demonstrated the ability to identify early signs of diseases like cancer, allowing for earlier intervention and potentially saving lives.\n\nAI is also making significant contributions to personalized medicine. By analyzing vast amounts of patient data, including genetic information, medical history, and lifestyle factors, AI can help physicians develop tailored treatment plans that are optimized for individual patients. This approach not only improves treatment efficacy but also minimizes adverse effects by avoiding unnecessary medications or procedures.\n\nIn addition to diagnostics and treatment planning, AI is enhancing patient care through virtual health assistants and monitoring systems. AI-powered chatbots can provide immediate responses to patient inquiries, schedule appointments, and even offer basic medical advice. Meanwhile, remote monitoring devices equipped with AI can track patients' vital signs and alert healthcare providers to potential issues before they become serious problems.\n\nDespite these promising developments, the integration of AI into healthcare faces several challenges. Concerns about data privacy, algorithm bias, and the potential for over-reliance on technology must be addressed. Additionally, healthcare professionals need proper training to effectively collaborate with AI systems rather than viewing them as replacements.\n\nAs we look to the future, the role of AI in healthcare will likely continue to expand. Researchers are exploring applications in drug discovery, surgical robotics, and predictive analytics for population health management. While AI will never replace the human touch in healthcare, it has the potential to be a powerful tool that enhances the capabilities of medical professionals and improves outcomes for patients worldwide.",
                'image' => '/placeholder.svg?height=600&width=800',
                'category' => 'technology',
                'read_time' => 8
            ],
            [
                'title' => 'Sustainable Living: Small Changes with Big Impact',
                'excerpt' => 'Practical tips for reducing your carbon footprint and living more sustainably in everyday life.',
                'content' => "As climate change continues to pose significant challenges to our planet, many individuals are seeking ways to reduce their environmental impact through sustainable living practices. The good news is that even small changes in our daily habits can collectively make a substantial difference.\n\nOne of the most effective ways to live more sustainably is to reduce energy consumption at home. Simple actions like switching to LED light bulbs, unplugging electronics when not in use, and properly insulating your home can significantly decrease your energy usage. Installing a programmable thermostat can also help by automatically adjusting temperatures when you're away or asleep.\n\nTransportation is another area where sustainable choices can have a major impact. Whenever possible, opt for walking, cycling, or public transportation instead of driving. If you must use a car, consider carpooling or investing in an electric or hybrid vehicle. For unavoidable air travel, look into carbon offset programs to mitigate your flight's emissions.\n\nOur food choices also play a crucial role in sustainability. Reducing meat consumption, especially beef, can dramatically lower your carbon footprint, as livestock production is a significant source of greenhouse gases. Choosing locally grown, seasonal produce reduces the emissions associated with food transportation and supports local farmers. Additionally, planning meals to minimize food waste helps conserve the resources used in food production.\n\nWater conservation is equally important for sustainable living. Installing low-flow faucets and showerheads, fixing leaks promptly, and collecting rainwater for garden irrigation are effective ways to reduce water usage. Even simple habits like turning off the tap while brushing your teeth or taking shorter showers can save thousands of gallons annually.

Finally, embracing the principles of reduce, reuse, and recycle can minimize waste. Before purchasing new items, consider whether you truly need them. When shopping, choose products with minimal packaging and bring your own reusable bags. For items you no longer need, explore options for donating, repurposing, or recycling before discarding them.

By incorporating these sustainable practices into our daily lives, we can collectively work toward a healthier planet for current and future generations. Remember, the journey toward sustainability is not about perfection but progress—every positive change, no matter how small, is a step in the right direction.",
                'image' => '/placeholder.svg?height=600&width=800',
                'category' => 'lifestyle',
                'read_time' => 6
            ],
            [
                'title' => 'The Science of Sleep: Why Quality Rest Matters',
                'excerpt' => 'Understanding the crucial role of sleep in physical health, cognitive function, and emotional wellbeing.',
                'content' => "Sleep is a fundamental biological process that affects virtually every aspect of our health and wellbeing. Despite spending roughly one-third of our lives asleep, many people underestimate the importance of quality rest and the profound impact it has on our bodies and minds.\n\nDuring sleep, the body engages in essential maintenance and repair processes. Growth hormone is released, facilitating tissue repair and muscle growth. The immune system produces cytokines, proteins that help fight inflammation and infection. This explains why inadequate sleep is associated with increased susceptibility to illnesses and slower recovery times.\n\nPerhaps even more remarkable is what happens in the brain during sleep. Rather than shutting down, the brain remains highly active, cycling through different sleep stages characterized by distinct patterns of neural activity. During deep sleep, the brain consolidates memories, transferring information from short-term to long-term storage. This process is crucial for learning and skill development.\n\nRapid Eye Movement (REM) sleep, the stage associated with dreaming, plays a vital role in emotional processing and regulation. During REM sleep, the brain processes emotional experiences and memories, helping us make sense of complex emotions and stressful events. This explains why sleep deprivation often leads to mood disturbances, irritability, and impaired emotional resilience.\n\nThe quality of our sleep is influenced by numerous factors, including our sleep environment, daily habits, and overall health. Maintaining a consistent sleep schedule, creating a cool, dark, and quiet sleeping environment, and limiting exposure to screens before bedtime can significantly improve sleep quality. Regular physical activity and mindful stress management also contribute to better sleep.\n\nDespite the clear importance of sleep, modern society often glorifies busyness and productivity at the expense of rest. Many people view sleep as a luxury rather than a necessity, leading to chronic sleep deprivation with serious consequences. Research has linked insufficient sleep to a range of health problems, including cardiovascular disease, diabetes, obesity, and neurodegenerative disorders.\n\nAs our understanding of sleep science continues to evolve, one thing remains clear: quality sleep is not indulgent—it's essential. By prioritizing sleep and adopting habits that promote restful nights, we can enhance our physical health, cognitive performance, emotional wellbeing, and overall quality of life.",
                'image' => '/placeholder.svg?height=600&width=800',
                'category' => 'health',
                'read_time' => 7
            ]
        ];
        
        foreach ($articles as $article) {
            $title = $article['title'];
            $slug = slugify($title);
            $excerpt = $article['excerpt'];
            $content = $article['content'];
            $image = $article['image'];
            $category = $article['category'];
            $readTime = $article['read_time'];
            
            $stmt = $conn->prepare("INSERT INTO articles (title, slug, excerpt, content, image, read_time, category, author_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssisis", $title, $slug, $excerpt, $content, $image, $readTime, $category, $adminId);
            
            if ($stmt->execute() === FALSE) {
                echo "Error creating article: " . $stmt->error . "<br>";
            }
        }
        
        echo "Admin user and sample articles created successfully!<br>";
    } else {
        echo "Admin user already exists.<br>";
    }
    
    closeDbConnection($conn);
    
    echo "Database setup completed successfully!";
}

// Run the setup
setupDatabase();

