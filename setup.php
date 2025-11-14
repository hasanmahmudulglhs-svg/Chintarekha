<?php
require_once 'config.php';

echo "<h1>Chintarekha Blog - Database Setup</h1>";

// Check if admin user already exists
$admin_check = $conn->query("SELECT id FROM users WHERE username = 'admin'");

if ($admin_check->num_rows > 0) {
    echo "<p style='color: orange;'>⚠️ Admin user already exists. Setup has been run before.</p>";
} else {
    // Create admin user
    $admin_password = password_hash('password', PASSWORD_DEFAULT);
    $admin_query = "INSERT INTO users (username, name, email, password_hash, role, bio) 
                   VALUES ('admin', 'Administrator', 'admin@chintarekha.com', '$admin_password', 'admin', 'Site administrator and content moderator.')";
    
    if ($conn->query($admin_query)) {
        echo "<p style='color: green;'>✅ Admin user created successfully!</p>";
        echo "<p><strong>Admin Login:</strong> username: admin, password: password</p>";
    } else {
        echo "<p style='color: red;'>❌ Error creating admin user: " . $conn->error . "</p>";
    }
}

// Check if demo user already exists
$user_check = $conn->query("SELECT id FROM users WHERE username = 'demo_user'");

if ($user_check->num_rows > 0) {
    echo "<p style='color: orange;'>⚠️ Demo user already exists.</p>";
} else {
    // Create demo user
    $user_password = password_hash('password', PASSWORD_DEFAULT);
    $user_query = "INSERT INTO users (username, name, email, password_hash, role, bio) 
                  VALUES ('demo_user', 'Demo User', 'demo@chintarekha.com', '$user_password', 'user', 'I am a demo user for testing the blog functionality.')";
    
    if ($conn->query($user_query)) {
        echo "<p style='color: green;'>✅ Demo user created successfully!</p>";
        echo "<p><strong>Demo User Login:</strong> username: demo_user, password: password</p>";
        
        // Get the user ID for creating demo posts
        $user_id = $conn->insert_id;
        
        // Create demo posts
        $demo_posts = [
            [
                'title' => 'Welcome to Chintarekha Blog!',
                'content' => "Welcome to our beautiful blog platform! This is your first demo post.\n\nHere are some features you can explore:\n\n• Write and publish blog posts\n• Upload images for your posts\n• Comment on posts\n• Manage your profile\n• Edit and delete your own posts\n\nAdmins have additional powers like managing all posts and users. Try logging in with the admin account to see the admin panel!\n\nHappy blogging! 🎉"
            ],
            [
                'title' => 'Getting Started with Blogging',
                'content' => "Blogging is a wonderful way to share your thoughts and experiences with the world.\n\nHere are some tips for great blog posts:\n\n1. **Choose engaging titles** - Your title is the first thing readers see\n2. **Write quality content** - Focus on providing value to your readers\n3. **Use images wisely** - Visual content helps engage readers\n4. **Be consistent** - Regular posting helps build an audience\n5. **Interact with readers** - Respond to comments and build community\n\nRemember, every expert was once a beginner. Start writing and improve with each post!"
            ],
            [
                'title' => 'The Power of Community',
                'content' => "One of the best aspects of blogging platforms is the sense of community they create.\n\nWhen writers come together to share their stories, amazing things happen:\n\n• **Knowledge sharing** - Learn from others' experiences\n• **Inspiration** - Get motivated by others' journeys\n• **Feedback** - Improve your writing through constructive comments\n• **Networking** - Connect with like-minded individuals\n• **Support** - Find encouragement during challenging times\n\nSo don't just write - engage! Comment on others' posts, share your thoughts, and be part of this growing community.\n\nTogether, we can create something beautiful! ✨"
            ]
        ];
        
        foreach ($demo_posts as $post) {
            $title = $conn->real_escape_string($post['title']);
            $content = $conn->real_escape_string($post['content']);
            
            $post_query = "INSERT INTO posts (user_id, title, content) 
                          VALUES ($user_id, '$title', '$content')";
            
            if ($conn->query($post_query)) {
                echo "<p style='color: green;'>✅ Demo post created: " . htmlspecialchars($post['title']) . "</p>";
            }
        }
    }
}

// Create some demo categories
$categories = ['Technology', 'Lifestyle', 'Travel', 'Food', 'Health'];
foreach ($categories as $category) {
    $check_category = $conn->query("SELECT id FROM categories WHERE name = '$category'");
    if ($check_category->num_rows == 0) {
        $conn->query("INSERT INTO categories (name) VALUES ('$category')");
        echo "<p style='color: green;'>✅ Category created: $category</p>";
    }
}

echo "<hr>";
echo "<h3>Setup Complete! 🎉</h3>";
echo "<p><strong>Next Steps:</strong></p>";
echo "<ul>";
echo "<li><a href='index.php'>Visit the Blog Home Page</a></li>";
echo "<li><a href='login.php'>Login with Admin (admin/password) or Demo User (demo_user/password)</a></li>";
echo "<li><a href='signup.php'>Create your own account</a></li>";
echo "</ul>";
echo "<p><em>Note: Make sure your uploads/ folder has write permissions for image uploads to work properly.</em></p>";
?>

<style>
body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
h1 { color: #007bff; }
h3 { color: #28a745; }
</style>