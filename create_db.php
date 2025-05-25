<?php
$host = 'localhost';
$username = 'root';
$password = '';

try {
    // Create connection without database
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS community_db");
    
    echo "<h2>Database created successfully!</h2>";
    echo "<p>Now you can run <a href='setup.php'>setup.php</a> to create the tables.</p>";
    
} catch(PDOException $e) {
    die("Error creating database: " . $e->getMessage());
}
?> 