<?php
require_once 'models/article.php';

// Get all articles
$articles = getAllArticles();

// Featured article is the first one
$featuredArticle = !empty($articles) ? $articles[0] : null;

// Recent articles are the next 6
$recentArticles = array_slice($articles, 1, 6);

// Load the view
require_once 'views/home.php';

