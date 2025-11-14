<?php
require_once 'config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

// Get post ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirect('index.php');
}

$post_id = (int)$_GET['id'];

// Get post and check ownership or admin rights
$post_query = "SELECT * FROM posts WHERE id = $post_id";
$post_result = $conn->query($post_query);

if ($post_result->num_rows == 0) {
    redirect('index.php');
}

$post = $post_result->fetch_assoc();

// Check if user can delete this post
if ($post['user_id'] != $_SESSION['user_id'] && !isAdmin()) {
    redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Delete image file if exists
    if (!empty($post['image_url']) && file_exists('uploads/' . $post['image_url'])) {
        unlink('uploads/' . $post['image_url']);
    }
    
    // Delete post (comments will be deleted automatically due to foreign key cascade)
    $delete_query = "DELETE FROM posts WHERE id = $post_id";
    
    if ($conn->query($delete_query)) {
        $_SESSION['message'] = 'Post deleted successfully!';
        $_SESSION['message_type'] = 'success';
        redirect('profile.php');
    } else {
        $_SESSION['message'] = 'Error deleting post. Please try again.';
        $_SESSION['message_type'] = 'danger';
        redirect('post.php?id=' . $post_id);
    }
}

$page_title = 'Delete Post - Chintarekha Blog';
include 'header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow border-danger">
            <div class="card-header bg-danger text-white">
                <h4><i class="bi bi-exclamation-triangle"></i> Delete Post</h4>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i>
                    <strong>Warning:</strong> This action cannot be undone. All comments on this post will also be deleted.
                </div>
                
                <h5>Are you sure you want to delete this post?</h5>
                <div class="card mt-3">
                    <div class="card-body">
                        <h6 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h6>
                        <p class="card-text text-muted">
                            <?php echo htmlspecialchars(substr($post['content'], 0, 150)); ?>
                            <?php echo strlen($post['content']) > 150 ? '...' : ''; ?>
                        </p>
                        <small class="text-muted">
                            Created: <?php echo date('F j, Y', strtotime($post['created_at'])); ?>
                        </small>
                    </div>
                </div>
                
                <form method="POST" class="mt-4">
                    <div class="d-flex justify-content-between">
                        <a href="post.php?id=<?php echo $post['id']; ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash"></i> Yes, Delete Post
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>