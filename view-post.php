<?php
session_start();
require_once 'config/database.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$post_id = $_GET['id'];

// Get post details
$stmt = $pdo->prepare("SELECT posts.*, users.username 
                      FROM posts 
                      JOIN users ON posts.user_id = users.id 
                      WHERE posts.id = ?");
$stmt->execute([$post_id]);
$post = $stmt->fetch();

if (!$post) {
    header("Location: index.php");
    exit();
}

// Record view
if (!isset($_SESSION['viewed_posts'])) {
    $_SESSION['viewed_posts'] = [];
}

if (!in_array($post_id, $_SESSION['viewed_posts'])) {
    $stmt = $pdo->prepare("INSERT INTO views (post_id, user_id) VALUES (?, ?)");
    $stmt->execute([$post_id, $_SESSION['user_id'] ?? null]);
    $_SESSION['viewed_posts'][] = $post_id;
}

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $comment = trim($_POST['comment']);
    if (!empty($comment)) {
        $stmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)");
        $stmt->execute([$post_id, $_SESSION['user_id'], $comment]);
        header("Location: view-post.php?id=" . $post_id);
        exit();
    }
}

// Get comments
$stmt = $pdo->prepare("SELECT comments.*, users.username 
                      FROM comments 
                      JOIN users ON comments.user_id = users.id 
                      WHERE comments.post_id = ? 
                      ORDER BY comments.created_at DESC");
$stmt->execute([$post_id]);
$comments = $stmt->fetchAll();

// Get view count
$stmt = $pdo->prepare("SELECT COUNT(*) as view_count FROM views WHERE post_id = ?");
$stmt->execute([$post_id]);
$view_count = $stmt->fetch()['view_count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?> - Community Platform</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="logo">Community</div>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="profile.php">My Profile</a>
                <a href="create-post.php">Create Post</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </div>
    </nav>

    <main class="container">
        <div class="post-card">
            <div class="post-header">
                <h2><?php echo htmlspecialchars($post['title']); ?></h2>
                <span class="author">Posted by <?php echo htmlspecialchars($post['username']); ?></span>
            </div>
            <div class="post-content">
                <?php echo nl2br(htmlspecialchars($post['content'])); ?>
            </div>
            <div class="post-stats">
                <span>👁️ <?php echo $view_count; ?> views</span>
                <span>💬 <?php echo count($comments); ?> comments</span>
            </div>
        </div>

        <div class="comments-section">
            <h3>Comments</h3>
            
            <?php if(isset($_SESSION['user_id'])): ?>
                <form method="POST" action="" class="comment-form">
                    <div class="form-group">
                        <textarea name="comment" placeholder="Write a comment..." required></textarea>
                    </div>
                    <button type="submit" class="btn">Post Comment</button>
                </form>
            <?php else: ?>
                <p>Please <a href="login.php">login</a> to comment.</p>
            <?php endif; ?>

            <?php foreach($comments as $comment): ?>
                <div class="comment">
                    <div class="comment-header">
                        <span class="author"><?php echo htmlspecialchars($comment['username']); ?></span>
                        <span class="date"><?php echo date('M j, Y g:i a', strtotime($comment['created_at'])); ?></span>
                    </div>
                    <div class="comment-content">
                        <?php echo nl2br(htmlspecialchars($comment['content'])); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
</body>
</html> 