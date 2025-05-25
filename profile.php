<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user's posts
$stmt = $pdo->prepare("SELECT posts.*, 
                      (SELECT COUNT(*) FROM comments WHERE post_id = posts.id) as comment_count,
                      (SELECT COUNT(*) FROM views WHERE post_id = posts.id) as view_count
                      FROM posts 
                      WHERE user_id = ? 
                      ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$posts = $stmt->fetchAll();

// Get user's comments
$stmt = $pdo->prepare("SELECT comments.*, posts.title as post_title, posts.id as post_id
                      FROM comments 
                      JOIN posts ON comments.post_id = posts.id
                      WHERE comments.user_id = ? 
                      ORDER BY comments.created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$comments = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Community Platform</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="logo">Community</div>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="create-post.php">Create Post</a>
            <a href="logout.php">Logout</a>
        </div>
    </nav>

    <main class="container">
        <div class="profile-header">
            <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
            <a href="create-post.php" class="btn">Create New Post</a>
        </div>

        <div class="profile-section">
            <h3>My Posts</h3>
            <?php if (empty($posts)): ?>
                <p>You haven't created any posts yet.</p>
            <?php else: ?>
                <?php foreach($posts as $post): ?>
                    <div class="post-card">
                        <div class="post-header">
                            <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                            <span class="date"><?php echo date('M j, Y', strtotime($post['created_at'])); ?></span>
                        </div>
                        <div class="post-content">
                            <?php echo nl2br(htmlspecialchars(substr($post['content'], 0, 200))); ?>...
                        </div>
                        <div class="post-stats">
                            <span>👁️ <?php echo $post['view_count']; ?> views</span>
                            <span>💬 <?php echo $post['comment_count']; ?> comments</span>
                        </div>
                        <div class="post-actions">
                            <a href="view-post.php?id=<?php echo $post['id']; ?>" class="btn">View Post</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="profile-section">
            <h3>My Comments</h3>
            <?php if (empty($comments)): ?>
                <p>You haven't made any comments yet.</p>
            <?php else: ?>
                <?php foreach($comments as $comment): ?>
                    <div class="comment">
                        <div class="comment-header">
                            <span class="post-title">On: <a href="view-post.php?id=<?php echo $comment['post_id']; ?>"><?php echo htmlspecialchars($comment['post_title']); ?></a></span>
                            <span class="date"><?php echo date('M j, Y g:i a', strtotime($comment['created_at'])); ?></span>
                        </div>
                        <div class="comment-content">
                            <?php echo nl2br(htmlspecialchars($comment['content'])); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
</body>
</html> 