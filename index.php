<?php
require_once 'config.php';

// Get all posts with user information
$posts_query = "SELECT p.*, u.username, u.name, u.profile_pic 
                FROM posts p 
                JOIN users u ON p.user_id = u.id 
                ORDER BY p.created_at DESC";
$posts_result = $conn->query($posts_query);

$page_title = 'Home - Chintarekha Blog';
include 'header.php';
?>

<!-- Hero Section -->
<div class="jumbotron bg-primary text-white rounded mb-4 p-5">
    <div class="container">
        <h1 class="display-4"><i class="bi bi-journal-text"></i> Welcome to Chintarekha Blog</h1>
        <p class="lead">Discover amazing stories, share your thoughts, and connect with fellow writers.</p>
        <?php if (!isLoggedIn()): ?>
            <a class="btn btn-light btn-lg" href="signup.php" role="button">
                <i class="bi bi-person-plus"></i> Join Our Community
            </a>
        <?php else: ?>
            <a class="btn btn-light btn-lg" href="create_post.php" role="button">
                <i class="bi bi-plus-circle"></i> Write Your Story
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Posts Section -->
<div class="row">
    <div class="col-md-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-newspaper"></i> Latest Posts</h2>
            <?php if (isLoggedIn()): ?>
                <a href="create_post.php" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> New Post
                </a>
            <?php endif; ?>
        </div>
        
        <?php if ($posts_result->num_rows > 0): ?>
            <?php while ($post = $posts_result->fetch_assoc()): ?>
                <div class="card mb-4 shadow-sm">
                    <?php if ($post['image_url']): ?>
                        <img src="uploads/<?php echo htmlspecialchars($post['image_url']); ?>" 
                             class="card-img-top post-image" alt="<?php echo htmlspecialchars($post['title']); ?>">
                    <?php endif; ?>
                    
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <?php if ($post['profile_pic']): ?>
                                <img src="uploads/<?php echo htmlspecialchars($post['profile_pic']); ?>" 
                                     alt="<?php echo htmlspecialchars($post['name']); ?>" class="profile-pic me-2">
                            <?php else: ?>
                                <div class="profile-pic me-2 bg-secondary d-flex align-items-center justify-content-center text-white">
                                    <i class="bi bi-person"></i>
                                </div>
                            <?php endif; ?>
                            <div>
                                <small class="text-muted">By <?php echo htmlspecialchars($post['name']); ?></small><br>
                                <small class="text-muted"><?php echo date('F j, Y', strtotime($post['created_at'])); ?></small>
                            </div>
                            <div class="ms-auto">
                                <small class="text-muted">
                                    <i class="bi bi-eye"></i> <?php echo $post['view_count']; ?> views
                                </small>
                            </div>
                        </div>
                        
                        <h5 class="card-title">
                            <a href="post.php?id=<?php echo $post['id']; ?>" class="text-decoration-none">
                                <?php echo htmlspecialchars($post['title']); ?>
                            </a>
                        </h5>
                        
                        <div class="post-preview">
                            <div class="post-content">
                                <p class="card-text"><?php echo nl2br(htmlspecialchars(substr($post['content'], 0, 300))); ?></p>
                                <?php if (strlen($post['content']) > 300): ?>
                                    <p class="text-muted">...</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="post.php?id=<?php echo $post['id']; ?>" class="btn btn-primary btn-sm">
                                <i class="bi bi-book-open"></i> Read More
                            </a>
                            
                            <?php if (isLoggedIn() && ($_SESSION['user_id'] == $post['user_id'] || isAdmin())): ?>
                                <div class="btn-group" role="group">
                                    <a href="edit_post.php?id=<?php echo $post['id']; ?>" class="btn btn-outline-secondary btn-sm">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <a href="delete_post.php?id=<?php echo $post['id']; ?>" 
                                       class="btn btn-outline-danger btn-sm"
                                       onclick="return confirm('Are you sure you want to delete this post?')">
                                        <i class="bi bi-trash"></i> Delete
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="card text-center">
                <div class="card-body py-5">
                    <i class="bi bi-journal-x display-1 text-muted"></i>
                    <h4 class="mt-3">No Posts Yet</h4>
                    <p class="text-muted">Be the first to share your story!</p>
                    <?php if (isLoggedIn()): ?>
                        <a href="create_post.php" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Write First Post
                        </a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-primary">
                            <i class="bi bi-box-arrow-in-right"></i> Login to Write
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Sidebar -->
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5><i class="bi bi-info-circle"></i> About Chintarekha</h5>
            </div>
            <div class="card-body">
                <p class="card-text">
                    A simple and elegant blog platform where writers can share their thoughts, 
                    experiences, and stories with the world. Join our community today!
                </p>
            </div>
        </div>
        
        <?php
        // Get recent posts for sidebar
        $recent_posts_query = "SELECT id, title, created_at FROM posts ORDER BY created_at DESC LIMIT 5";
        $recent_posts_result = $conn->query($recent_posts_query);
        ?>
        
        <?php if ($recent_posts_result->num_rows > 0): ?>
        <div class="card mb-4">
            <div class="card-header">
                <h5><i class="bi bi-clock-history"></i> Recent Posts</h5>
            </div>
            <div class="list-group list-group-flush">
                <?php while ($recent_post = $recent_posts_result->fetch_assoc()): ?>
                    <a href="post.php?id=<?php echo $recent_post['id']; ?>" 
                       class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <small class="fw-bold"><?php echo htmlspecialchars(substr($recent_post['title'], 0, 30)); ?><?php echo strlen($recent_post['title']) > 30 ? '...' : ''; ?></small>
                            <small><?php echo date('M j', strtotime($recent_post['created_at'])); ?></small>
                        </div>
                    </a>
                <?php endwhile; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <?php
        // Get user stats for sidebar
        $total_posts = $conn->query("SELECT COUNT(*) as count FROM posts")->fetch_assoc()['count'];
        $total_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
        $total_comments = $conn->query("SELECT COUNT(*) as count FROM comments")->fetch_assoc()['count'];
        ?>
        
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-bar-chart"></i> Community Stats</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-4">
                        <i class="bi bi-journal-text display-6 text-primary"></i>
                        <h6 class="mt-2"><?php echo $total_posts; ?></h6>
                        <small class="text-muted">Posts</small>
                    </div>
                    <div class="col-4">
                        <i class="bi bi-people display-6 text-success"></i>
                        <h6 class="mt-2"><?php echo $total_users; ?></h6>
                        <small class="text-muted">Writers</small>
                    </div>
                    <div class="col-4">
                        <i class="bi bi-chat-dots display-6 text-warning"></i>
                        <h6 class="mt-2"><?php echo $total_comments; ?></h6>
                        <small class="text-muted">Comments</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>