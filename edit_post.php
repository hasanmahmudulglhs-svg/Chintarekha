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

// Check if user can edit this post
if ($post['user_id'] != $_SESSION['user_id'] && !isAdmin()) {
    redirect('index.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = sanitize($_POST['title']);
    $content = sanitize($_POST['content']);
    
    if (empty($title) || empty($content)) {
        $error = 'Title and content are required.';
    } else {
        $image_url = $post['image_url']; // Keep existing image by default
        
        // Handle file upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $max_size = 5 * 1024 * 1024; // 5MB
            
            if (in_array($_FILES['image']['type'], $allowed_types) && $_FILES['image']['size'] <= $max_size) {
                // Delete old image if exists
                if (!empty($post['image_url']) && file_exists('uploads/' . $post['image_url'])) {
                    unlink('uploads/' . $post['image_url']);
                }
                
                $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $image_url = 'post_' . time() . '_' . uniqid() . '.' . $file_extension;
                $upload_path = 'uploads/' . $image_url;
                
                if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    $error = 'Error uploading image.';
                }
            } else {
                $error = 'Invalid image file. Please upload JPG, PNG, or GIF files under 5MB.';
            }
        }
        
        // Handle image removal
        if (isset($_POST['remove_image']) && $_POST['remove_image'] == '1') {
            if (!empty($post['image_url']) && file_exists('uploads/' . $post['image_url'])) {
                unlink('uploads/' . $post['image_url']);
            }
            $image_url = '';
        }
        
        if (empty($error)) {
            $update_query = "UPDATE posts SET title = '$title', content = '$content', image_url = '$image_url' 
                           WHERE id = $post_id";
            
            if ($conn->query($update_query)) {
                $success = 'Post updated successfully!';
                // Refresh post data
                $post_result = $conn->query($post_query);
                $post = $post_result->fetch_assoc();
            } else {
                $error = 'Error updating post. Please try again.';
            }
        }
    }
}

$page_title = 'Edit Post - Chintarekha Blog';
include 'header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-warning text-dark">
                <h4><i class="bi bi-pencil"></i> Edit Post</h4>
            </div>
            <div class="card-body">
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
                
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="title" class="form-label">Post Title</label>
                        <input type="text" class="form-control" id="title" name="title" 
                               value="<?php echo htmlspecialchars($post['title']); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Current Image</label>
                        <?php if (!empty($post['image_url'])): ?>
                            <div class="mb-2">
                                <img src="uploads/<?php echo htmlspecialchars($post['image_url']); ?>" 
                                     alt="Current image" style="max-width: 300px; height: auto;" class="img-thumbnail">
                                <div class="form-check mt-2">
                                    <input type="checkbox" class="form-check-input" id="remove_image" name="remove_image" value="1">
                                    <label class="form-check-label" for="remove_image">
                                        Remove current image
                                    </label>
                                </div>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No image uploaded</p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="image" class="form-label">Upload New Image (Optional)</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        <div class="form-text">Upload JPG, PNG, or GIF files (max 5MB). This will replace the current image.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="content" class="form-label">Content</label>
                        <textarea class="form-control" id="content" name="content" rows="12" 
                                  required><?php echo htmlspecialchars($post['content']); ?></textarea>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="post.php?id=<?php echo $post['id']; ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Post
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Update Post
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>