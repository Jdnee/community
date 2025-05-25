<?php
require_once 'config/database.php';

try {
    // Insert sample users
    $users = [
        ['username' => 'alex_dev', 'email' => 'alex@example.com', 'password' => 'dev123'],
        ['username' => 'sarah_tech', 'email' => 'sarah@example.com', 'password' => 'tech123'],
        ['username' => 'mike_coder', 'email' => 'mike@example.com', 'password' => 'code123'],
        ['username' => 'lisa_design', 'email' => 'lisa@example.com', 'password' => 'design123']
    ];

    $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    foreach ($users as $user) {
        $password = password_hash($user['password'], PASSWORD_DEFAULT);
        $stmt->execute([$user['username'], $user['email'], $password]);
    }

    // Get user IDs
    $userIds = $pdo->query("SELECT id FROM users")->fetchAll(PDO::FETCH_COLUMN);

    // Insert sample posts
    $posts = [
        [
            'user_id' => $userIds[0],
            'title' => 'Getting Started with Web Development',
            'content' => "Here's a comprehensive guide to start your web development journey:\n\n1. Learn HTML & CSS\n2. Master JavaScript\n3. Choose a backend language\n4. Learn about databases\n5. Practice with projects!"
        ],
        [
            'user_id' => $userIds[1],
            'title' => 'Best Practices for Clean Code',
            'content' => "Writing clean code is essential for maintainable projects. Here are some tips:\n\n- Use meaningful variable names\n- Keep functions small and focused\n- Write comments when necessary\n- Follow the DRY principle\n- Test your code thoroughly"
        ],
        [
            'user_id' => $userIds[2],
            'title' => 'My Journey as a Developer',
            'content' => "Started coding 5 years ago, and here's what I've learned:\n\n- Consistency is key\n- Build projects you're passionate about\n- Join coding communities\n- Never stop learning\n- Share your knowledge"
        ],
        [
            'user_id' => $userIds[3],
            'title' => 'UI/UX Design Tips',
            'content' => "Design principles that make a difference:\n\n- Keep it simple\n- Use consistent spacing\n- Choose colors wisely\n- Make it accessible\n- Test with real users"
        ],
        [
            'user_id' => $userIds[0],
            'title' => 'Debugging Techniques',
            'content' => "Effective debugging strategies:\n\n1. Use console.log strategically\n2. Break down the problem\n3. Check your assumptions\n4. Use debugging tools\n5. Take breaks when stuck"
        ]
    ];

    $stmt = $pdo->prepare("INSERT INTO posts (user_id, title, content) VALUES (?, ?, ?)");
    foreach ($posts as $post) {
        $stmt->execute([$post['user_id'], $post['title'], $post['content']]);
    }

    // Insert some sample comments
    $comments = [
        ['post_id' => 1, 'user_id' => $userIds[1], 'content' => 'Great guide! This helped me a lot.'],
        ['post_id' => 1, 'user_id' => $userIds[2], 'content' => 'Would you recommend any specific resources for beginners?'],
        ['post_id' => 2, 'user_id' => $userIds[0], 'content' => 'These are excellent tips. I especially agree with the DRY principle.'],
        ['post_id' => 3, 'user_id' => $userIds[3], 'content' => 'Your journey is inspiring! Thanks for sharing.']
    ];

    $stmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)");
    foreach ($comments as $comment) {
        $stmt->execute([$comment['post_id'], $comment['user_id'], $comment['content']]);
    }

    // Insert some sample views
    $views = [];
    foreach ($userIds as $userId) {
        foreach (range(1, 5) as $postId) {
            $views[] = ['post_id' => $postId, 'user_id' => $userId];
        }
    }

    $stmt = $pdo->prepare("INSERT INTO views (post_id, user_id) VALUES (?, ?)");
    foreach ($views as $view) {
        $stmt->execute([$view['post_id'], $view['user_id']]);
    }

    echo "Sample data inserted successfully!";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 