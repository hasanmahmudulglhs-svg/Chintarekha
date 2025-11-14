<?php
require_once 'config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = sanitize($_POST['title']);
    $content = sanitize($_POST['content']);
    
    if (empty($title) || empty($content)) {
        $error = 'Title and content are required.';
    } else {
        $image_url = '';
        
        // Handle file upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $max_size = 5 * 1024 * 1024; // 5MB
            
            if (in_array($_FILES['image']['type'], $allowed_types) && $_FILES['image']['size'] <= $max_size) {
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
        
        if (empty($error)) {
            $user_id = $_SESSION['user_id'];
            $insert_query = "INSERT INTO posts (user_id, title, content, image_url) 
                           VALUES ('$user_id', '$title', '$content', '$image_url')";
            
            if ($conn->query($insert_query)) {
                $success = 'Post created successfully!';
                // Clear form data
                unset($_POST);
            } else {
                $error = 'Error creating post. Please try again.';
            }
        }
    }
}

$page_title = 'Create Post - Chintarekha Blog';
include 'header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4><i class="bi bi-plus-circle"></i> Create New Post</h4>
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
                    <div class="text-center">
                        <a href="index.php" class="btn btn-primary">View All Posts</a>
                        <a href="profile.php" class="btn btn-outline-primary">Go to Profile</a>
                    </div>
                <?php else: ?>
                
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="title" class="form-label">Post Title</label>
                        <input type="text" class="form-control" id="title" name="title" 
                               placeholder="Enter an engaging title for your post"
                               value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="image" class="form-label">Featured Image (Optional)</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        <div class="form-text">Upload JPG, PNG, or GIF files (max 5MB)</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="content" class="form-label">Content</label>
                        <textarea class="form-control" id="content" name="content" rows="12" 
                                  placeholder="Write your post content here..."
                                  required><?php echo isset($_POST['content']) ? htmlspecialchars($_POST['content']) : ''; ?></textarea>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="index.php" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Publish Post
                        </button>
                    </div>
                </form>
                
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>