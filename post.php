<?php
require_once 'config.php';

// Get post ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirect('index.php');
}

$post_id = (int)$_GET['id'];

// Update view count
$conn->query("UPDATE posts SET view_count = view_count + 1 WHERE id = $post_id");

// Get post with author information
$post_query = "SELECT p.*, u.username, u.name, u.profile_pic, u.role 
               FROM posts p 
               JOIN users u ON p.user_id = u.id 
               WHERE p.id = $post_id";
$post_result = $conn->query($post_query);

if ($post_result->num_rows == 0) {
    redirect('index.php');
}

$post = $post_result->fetch_assoc();

$error = '';
$success = '';

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isLoggedIn()) {
    if (isset($_POST['submit_comment'])) {
        $content = sanitize($_POST['content']);
        
        if (empty($content)) {
            $error = 'Comment content is required.';
        } else {
            $user_id = $_SESSION['user_id'];
            $insert_query = "INSERT INTO comments (post_id, user_id, content) 
                           VALUES ($post_id, $user_id, '$content')";
            
            if ($conn->query($insert_query)) {
                $success = 'Comment added successfully!';
                // Clear form
                unset($_POST);
            } else {
                $error = 'Error adding comment. Please try again.';
            }
        }
    }
}

// Get comments for this post
$comments_query = "SELECT c.*, u.username, u.name, u.profile_pic 
                   FROM comments c 
                   JOIN users u ON c.user_id = u.id 
                   WHERE c.post_id = $post_id 
                   ORDER BY c.created_at DESC";
$comments_result = $conn->query($comments_query);

$page_title = htmlspecialchars($post['title']) . ' - Chintarekha Blog';
include 'header.php';
?>

<div class="row">
    <!-- Main Content -->
    <div class="col-md-8">
        <!-- Post Content -->
        <article class="card shadow-sm mb-4">            
            <div class="card-body">
                <!-- Post Header -->
                <div class="d-flex align-items-center mb-4">
                    <?php if ($post['profile_pic']): ?>
                        <img src="uploads/<?php echo htmlspecialchars($post['profile_pic']); ?>" 
                             alt="<?php echo htmlspecialchars($post['name']); ?>" class="profile-pic me-3">
                    <?php else: ?>
                        <div class="profile-pic me-3 bg-secondary d-flex align-items-center justify-content-center text-white">
                            <i class="bi bi-person"></i>
                        </div>
                    <?php endif; ?>
                    <div class="flex-grow-1">
                        <h6 class="mb-0">
                            <?php echo htmlspecialchars($post['name']); ?>
                            <?php if ($post['role'] === 'admin'): ?>
                                <span class="badge bg-danger ms-1">Admin</span>
                            <?php endif; ?>
                        </h6>
                        <small class="text-muted">@<?php echo htmlspecialchars($post['username']); ?></small>
                        <br>
                        <small class="text-muted">
                            <i class="bi bi-calendar"></i> <?php echo date('F j, Y \a\t g:i A', strtotime($post['created_at'])); ?>
                        </small>
                    </div>
                    <div class="text-end">
                        <small class="text-muted">
                            <i class="bi bi-eye"></i> <?php echo $post['view_count']; ?> views
                        </small>
                    </div>
                </div>
                
                <!-- Post Title -->
                <h1 class="mb-4"><?php echo htmlspecialchars($post['title']); ?></h1>
                
                <!-- Post Image -->
                <?php if ($post['image_url']): ?>
                    <div class="mb-4">
                        <img src="uploads/<?php echo htmlspecialchars($post['image_url']); ?>" 
                             class="img-fluid w-100 post-full-image rounded shadow-sm" 
                             alt="<?php echo htmlspecialchars($post['title']); ?>">
                    </div>
                <?php endif; ?>
                
                <!-- Post Content -->
                <div class="post-full">
                    <div class="post-content">
                        <?php echo nl2br(htmlspecialchars($post['content'])); ?>
                    </div>
                </div>
                
                <!-- Post Actions -->
                <?php if (isLoggedIn() && ($_SESSION['user_id'] == $post['user_id'] || isAdmin())): ?>
                    <hr>
                    <div class="d-flex justify-content-end">
                        <div class="btn-group" role="group">
                            <a href="edit_post.php?id=<?php echo $post['id']; ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <a href="delete_post.php?id=<?php echo $post['id']; ?>" 
                               class="btn btn-outline-danger"
                               onclick="return confirm('Are you sure you want to delete this post?')">
                                <i class="bi bi-trash"></i> Delete
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </article>
        
        <!-- Comments Section -->
        <div class="card shadow-sm">
            <div class="card-header">
                <h5><i class="bi bi-chat-dots"></i> Comments (<?php echo $comments_result->num_rows; ?>)</h5>
            </div>
            <div class="card-body">
                <!-- Comment Form -->
                <?php if (isLoggedIn()): ?>
                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle"></i> <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle"></i> <?php echo $success; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" class="mb-4">
                        <div class="d-flex">
                            <?php if (!empty($_SESSION['profile_pic'])): ?>
                                <img src="uploads/<?php echo htmlspecialchars($_SESSION['profile_pic']); ?>" 
                                     alt="Your Profile" class="profile-pic me-3">
                            <?php else: ?>
                                <div class="profile-pic me-3 bg-secondary d-flex align-items-center justify-content-center text-white">
                                    <i class="bi bi-person"></i>
                                </div>
                            <?php endif; ?>
                            <div class="flex-grow-1">
                                <textarea class="form-control" name="content" rows="3" 
                                          placeholder="Write a comment..."
                                          required><?php echo isset($_POST['content']) ? htmlspecialchars($_POST['content']) : ''; ?></textarea>
                                <div class="d-flex justify-content-end mt-2">
                                    <button type="submit" name="submit_comment" class="btn btn-primary btn-sm">
                                        <i class="bi bi-send"></i> Post Comment
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <hr>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> 
                        <a href="login.php" class="alert-link">Login</a> to post a comment.
                    </div>
                <?php endif; ?>
                
                <!-- Comments List -->
                <?php if ($comments_result->num_rows > 0): ?>
                    <?php while ($comment = $comments_result->fetch_assoc()): ?>
                        <div class="comment-box">
                            <div class="d-flex">
                                <?php if ($comment['profile_pic']): ?>
                                    <img src="uploads/<?php echo htmlspecialchars($comment['profile_pic']); ?>" 
                                         alt="<?php echo htmlspecialchars($comment['name']); ?>" class="profile-pic me-3">
                                <?php else: ?>
                                    <div class="profile-pic me-3 bg-secondary d-flex align-items-center justify-content-center text-white">
                                        <i class="bi bi-person"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($comment['name']); ?></h6>
                                        <small class="text-muted">
                                            <?php echo date('F j, Y \a\t g:i A', strtotime($comment['created_at'])); ?>
                                        </small>
                                    </div>
                                    <p class="mb-0"><?php echo nl2br(htmlspecialchars($comment['content'])); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-chat-dots display-4 text-muted"></i>
                        <h5 class="mt-3 text-muted">No comments yet</h5>
                        <p class="text-muted">Be the first to comment on this post!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Sidebar -->
    <div class="col-md-4">
        <!-- Author Info -->
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5><i class="bi bi-person-circle"></i> About the Author</h5>
            </div>
            <div class="card-body text-center">
                <?php if ($post['profile_pic']): ?>
                    <img src="uploads/<?php echo htmlspecialchars($post['profile_pic']); ?>" 
                         alt="<?php echo htmlspecialchars($post['name']); ?>" class="profile-pic-lg mb-3">
                <?php else: ?>
                    <div class="profile-pic-lg mx-auto mb-3 bg-secondary d-flex align-items-center justify-content-center text-white">
                        <i class="bi bi-person display-4"></i>
                    </div>
                <?php endif; ?>
                <h6><?php echo htmlspecialchars($post['name']); ?></h6>
                <p class="text-muted">@<?php echo htmlspecialchars($post['username']); ?></p>
                
                <?php
                // Get author's bio and stats
                $author_query = "SELECT bio FROM users WHERE id = " . $post['user_id'];
                $author_result = $conn->query($author_query);
                $author = $author_result->fetch_assoc();
                
                if (!empty($author['bio'])):
                ?>
                    <p class="card-text"><?php echo nl2br(htmlspecialchars($author['bio'])); ?></p>
                <?php endif; ?>
                
                <?php
                // Get author's post count
                $author_posts_count = $conn->query("SELECT COUNT(*) as count FROM posts WHERE user_id = " . $post['user_id'])->fetch_assoc()['count'];
                ?>
                <small class="text-muted"><?php echo $author_posts_count; ?> posts published</small>
            </div>
        </div>
        
        <!-- More Posts by Author -->
        <?php
        $more_posts_query = "SELECT id, title, created_at FROM posts 
                           WHERE user_id = " . $post['user_id'] . " AND id != $post_id 
                           ORDER BY created_at DESC LIMIT 5";
        $more_posts_result = $conn->query($more_posts_query);
        ?>
        
        <?php if ($more_posts_result->num_rows > 0): ?>
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5><i class="bi bi-journal-text"></i> More by <?php echo htmlspecialchars($post['name']); ?></h5>
            </div>
            <div class="list-group list-group-flush">
                <?php while ($more_post = $more_posts_result->fetch_assoc()): ?>
                    <a href="post.php?id=<?php echo $more_post['id']; ?>" 
                       class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <small class="fw-bold"><?php echo htmlspecialchars(substr($more_post['title'], 0, 30)); ?><?php echo strlen($more_post['title']) > 30 ? '...' : ''; ?></small>
                            <small><?php echo date('M j', strtotime($more_post['created_at'])); ?></small>
                        </div>
                    </a>
                <?php endwhile; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Navigation -->
        <div class="card shadow-sm">
            <div class="card-body">
                <a href="index.php" class="btn btn-outline-primary w-100 mb-2">
                    <i class="bi bi-arrow-left"></i> Back to All Posts
                </a>
                <?php if (isLoggedIn()): ?>
                    <a href="create_post.php" class="btn btn-primary w-100">
                        <i class="bi bi-plus-circle"></i> Write a Post
                    </a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-primary w-100">
                        <i class="bi bi-box-arrow-in-right"></i> Login to Write
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>