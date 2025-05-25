<?php
session_start();
require_once 'config/database.php';

// Get the current tab, default to 'all'
$current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'all';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community Platform</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="logo">Community</div>
        <div class="nav-links">
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
        <?php if(isset($_SESSION['user_id'])): ?>
            <div class="create-post-form">
                <h2>Create a New Post</h2>
                <form action="create-post.php" method="POST">
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" id="title" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="content">Content</label>
                        <textarea id="content" name="content" required></textarea>
                    </div>
                    <button type="submit" class="btn">Post</button>
                </form>
            </div>
        <?php else: ?>
            <div class="login-required">
                <p>Please login or register to create posts</p>
                <a href="login.php" class="btn">Login</a>
                <a href="register.php" class="btn">Register</a>
            </div>
        <?php endif; ?>

        <div class="tabs">
            <a href="?tab=all" class="tab <?php echo $current_tab === 'all' ? 'active' : ''; ?>">All Posts</a>
            <a href="?tab=popular" class="tab <?php echo $current_tab === 'popular' ? 'active' : ''; ?>">Popular</a>
            <a href="?tab=recent" class="tab <?php echo $current_tab === 'recent' ? 'active' : ''; ?>">Recent</a>
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="?tab=my-posts" class="tab <?php echo $current_tab === 'my-posts' ? 'active' : ''; ?>">My Posts</a>
            <?php endif; ?>
        </div>

        <div class="posts-container">
            <?php
            $query = "SELECT posts.*, users.username, 
                     (SELECT COUNT(*) FROM comments WHERE post_id = posts.id) as comment_count,
                     (SELECT COUNT(*) FROM views WHERE post_id = posts.id) as view_count
                     FROM posts 
                     JOIN users ON posts.user_id = users.id";

            // Modify query based on selected tab
            switch($current_tab) {
                case 'popular':
                    $query .= " ORDER BY view_count DESC, comment_count DESC";
                    break;
                case 'recent':
                    $query .= " ORDER BY posts.created_at DESC";
                    break;
                case 'my-posts':
                    if(isset($_SESSION['user_id'])) {
                        $query .= " WHERE posts.user_id = ? ORDER BY posts.created_at DESC";
                    }
                    break;
                default: // 'all'
                    $query .= " ORDER BY posts.created_at DESC";
            }

            $stmt = $pdo->prepare($query);
            if($current_tab === 'my-posts' && isset($_SESSION['user_id'])) {
                $stmt->execute([$_SESSION['user_id']]);
            } else {
                $stmt->execute();
            }

            while($post = $stmt->fetch(PDO::FETCH_ASSOC)):
            ?>
            <div class="post-card">
                <div class="post-header">
                    <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                    <span class="author">Posted by <?php echo htmlspecialchars($post['username']); ?></span>
                </div>
                <div class="post-content">
                    <?php echo nl2br(htmlspecialchars($post['content'])); ?>
                </div>
                <div class="post-stats">
                    <span>👁️ <?php echo $post['view_count']; ?> views</span>
                    <span>💬 <?php echo $post['comment_count']; ?> comments</span>
                    <span>⏰ <?php echo date('M d, Y', strtotime($post['created_at'])); ?></span>
                </div>
                <div class="post-actions">
                    <a href="view-post.php?id=<?php echo $post['id']; ?>" class="btn">View Post</a>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </main>

    <script src="js/main.js"></script>
</body>
</html> 