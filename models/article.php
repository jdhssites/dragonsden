<?php
// Article model

// Get all articles
function getAllArticles() {
    $conn = getDbConnection();
    
    $sql = "SELECT a.id, a.title, a.slug, a.excerpt, a.content, a.image, a.date, a.read_time, a.category, 
                   u.name as author_name, u.avatar as author_avatar 
            FROM articles a 
            JOIN users u ON a.author_id = u.id 
            ORDER BY a.date DESC";
    
    $result = $conn->query($sql);
    $articles = [];
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $articles[] = [
                'id' => $row['id'],
                'title' => $row['title'],
                'slug' => $row['slug'],
                'excerpt' => $row['excerpt'],
                'content' => explode("\n\n", $row['content']),
                'image' => $row['image'] ?: "/placeholder.svg?height=600&width=800",
                'date' => formatDate($row['date']),
                'readTime' => $row['read_time'],
                'category' => $row['category'],
                'author' => [
                    'name' => $row['author_name'],
                    'avatar' => $row['author_avatar'] ?: "/placeholder.svg?height=100&width=100"
                ]
            ];
        }
    }
    
    closeDbConnection($conn);
    return $articles;
}

// Get article by ID
function getArticleById($id) {
    $conn = getDbConnection();
    
    $stmt = $conn->prepare("SELECT a.id, a.title, a.slug, a.excerpt, a.content, a.image, a.date, a.read_time, a.category, 
                                   u.name as author_name, u.avatar as author_avatar 
                            FROM articles a 
                            JOIN users u ON a.author_id = u.id 
                            WHERE a.id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        closeDbConnection($conn);
        return null;
    }
    
    $row = $result->fetch_assoc();
    $article = [
        'id' => $row['id'],
        'title' => $row['title'],
        'slug' => $row['slug'],
        'excerpt' => $row['excerpt'],
        'content' => explode("\n\n", $row['content']),
        'image' => $row['image'] ?: "/placeholder.svg?height=600&width=800",
        'date' => formatDate($row['date']),
        'readTime' => $row['read_time'],
        'category' => $row['category'],
        'author' => [
            'name' => $row['author_name'],
            'avatar' => $row['author_avatar'] ?: "/placeholder.svg?height=100&width=100"
        ]
    ];
    
    closeDbConnection($conn);
    return $article;
}

// Get article by slug
function getArticleBySlug($slug) {
    $conn = getDbConnection();
    
    $stmt = $conn->prepare("SELECT a.id, a.title, a.slug, a.excerpt, a.content, a.image, a.date, a.read_time, a.category, 
                                   u.name as author_name, u.avatar as author_avatar 
                            FROM articles a 
                            JOIN users u ON a.author_id = u.id 
                            WHERE a.slug = ?");
    $stmt->bind_param("s", $slug);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        closeDbConnection($conn);
        return null;
    }
    
    $row = $result->fetch_assoc();
    $article = [
        'id' => $row['id'],
        'title' => $row['title'],
        'slug' => $row['slug'],
        'excerpt' => $row['excerpt'],
        'content' => explode("\n\n", $row['content']),
        'image' => $row['image'] ?: "/placeholder.svg?height=600&width=800",
        'date' => formatDate($row['date']),
        'readTime' => $row['read_time'],
        'category' => $row['category'],
        'author' => [
            'name' => $row['author_name'],
            'avatar' => $row['author_avatar'] ?: "/placeholder.svg?height=100&width=100"
        ]
    ];
    
    closeDbConnection($conn);
    return $article;
}

// Create article
function createArticle($title, $excerpt, $content, $image, $readTime, $category, $authorId) {
    $conn = getDbConnection();
    
    $slug = slugify($title);
    
    // Check if slug already exists
    $stmt = $conn->prepare("SELECT id FROM articles WHERE slug = ?");
    $stmt->bind_param("s", $slug);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        closeDbConnection($conn);
        return [
            'success' => false,
            'message' => 'An article with a similar title already exists'
        ];
    }
    
    // Insert article
    $stmt = $conn->prepare("INSERT INTO articles (title, slug, excerpt, content, image, read_time, category, author_id) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssisis", $title, $slug, $excerpt, $content, $image, $readTime, $category, $authorId);
    
    if (!$stmt->execute()) {
        closeDbConnection($conn);
        return [
            'success' => false,
            'message' => 'Failed to create article: ' . $conn->error
        ];
    }
    
    $articleId = $conn->insert_id;
    closeDbConnection($conn);
    
    return [
        'success' => true,
        'message' => 'Article created successfully',
        'articleId' => $articleId
    ];
}

// Update article
function updateArticle($id, $title, $excerpt, $content, $image, $readTime, $category) {
    $conn = getDbConnection();
    
    $slug = slugify($title);
    
    // Check if slug already exists for another article
    $stmt = $conn->prepare("SELECT id FROM articles WHERE slug = ? AND id != ?");
    $stmt->bind_param("si", $slug, $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        closeDbConnection($conn);
        return [
            'success' => false,
            'message' => 'An article with a similar title already exists'
        ];
    }
    
    // Update article
    $stmt = $conn->prepare("UPDATE articles SET title = ?, slug = ?, excerpt = ?, content = ?, 
                                  image = ?, read_time = ?, category = ? 
                           WHERE id = ?");
    $stmt->bind_param("sssssisi", $title, $slug, $excerpt, $content, $image, $readTime, $category, $id);
    
    if (!$stmt->execute()) {
        closeDbConnection($conn);
        return [
            'success' => false,
            'message' => 'Failed to update article: ' . $conn->error
        ];
    }
    
    closeDbConnection($conn);
    
    return [
        'success' => true,
        'message' => 'Article updated successfully'
    ];
}

// Delete article
function deleteArticle($id) {
    $conn = getDbConnection();
    
    $stmt = $conn->prepare("DELETE FROM articles WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if (!$stmt->execute()) {
        closeDbConnection($conn);
        return [
            'success' => false,
            'message' => 'Failed to delete article: ' . $conn->error
        ];
    }
    
    closeDbConnection($conn);
    
    return [
        'success' => true,
        'message' => 'Article deleted successfully'
    ];
}

// Get articles by category
function getArticlesByCategory($category) {
    $conn = getDbConnection();
    
    $stmt = $conn->prepare("SELECT a.id, a.title, a.slug, a.excerpt, a.content, a.image, a.date, a.read_time, a.category, 
                                   u.name as author_name, u.avatar as author_avatar 
                            FROM articles a 
                            JOIN users u ON a.author_id = u.id 
                            WHERE a.category = ?
                            ORDER BY a.date DESC");
    $stmt->bind_param("s", $category);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $articles = [];
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $articles[] = [
                'id' => $row['id'],
                'title' => $row['title'],
                'slug' => $row['slug'],
                'excerpt' => $row['excerpt'],
                'content' => explode("\n\n", $row['content']),
                'image' => $row['image'] ?: "/placeholder.svg?height=600&width=800",
                'date' => formatDate($row['date']),
                'readTime' => $row['read_time'],
                'category' => $row['category'],
                'author' => [
                    'name' => $row['author_name'],
                    'avatar' => $row['author_avatar'] ?: "/placeholder.svg?height=100&width=100"
                ]
            ];
        }
    }
    
    closeDbConnection($conn);
    return $articles;
}

// Get unique categories
function getUniqueCategories() {
    $conn = getDbConnection();
    
    $result = $conn->query("SELECT DISTINCT category FROM articles ORDER BY category");
    $categories = [];
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row['category'];
        }
    }
    
    closeDbConnection($conn);
    return $categories;
}

