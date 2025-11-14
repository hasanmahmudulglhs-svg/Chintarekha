<?php
require_once 'config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

$error = '';
$success = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_profile'])) {
        $name = sanitize($_POST['name']);
        $email = sanitize($_POST['email']);
        $bio = sanitize($_POST['bio']);
        
        if (empty($name) || empty($email)) {
            $error = 'Name and email are required.';
        } else {
            $profile_pic = $_SESSION['profile_pic'];
            
            // Handle profile picture upload
            if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
                $max_size = 2 * 1024 * 1024; // 2MB
                
                if (in_array($_FILES['profile_pic']['type'], $allowed_types) && $_FILES['profile_pic']['size'] <= $max_size) {
                    // Delete old profile pic if exists
                    if (!empty($_SESSION['profile_pic']) && file_exists('uploads/' . $_SESSION['profile_pic'])) {
                        unlink('uploads/' . $_SESSION['profile_pic']);
                    }
                    
                    $file_extension = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
                    $profile_pic = 'profile_' . $_SESSION['user_id'] . '_' . time() . '.' . $file_extension;
                    $upload_path = 'uploads/' . $profile_pic;
                    
                    if (!move_uploaded_file($_FILES['profile_pic']['tmp_name'], $upload_path)) {
                        $error = 'Error uploading profile picture.';
                    }
                } else {
                    $error = 'Invalid image file. Please upload JPG, PNG, or GIF files under 2MB.';
                }
            }
            
            // Handle profile picture removal
            if (isset($_POST['remove_profile_pic']) && $_POST['remove_profile_pic'] == '1') {
                if (!empty($_SESSION['profile_pic']) && file_exists('uploads/' . $_SESSION['profile_pic'])) {
                    unlink('uploads/' . $_SESSION['profile_pic']);
                }
                $profile_pic = '';
            }
            
            if (empty($error)) {
                $user_id = $_SESSION['user_id'];
                $update_query = "UPDATE users SET name = '$name', email = '$email', bio = '$bio', profile_pic = '$profile_pic' 
                               WHERE id = $user_id";
                
                if ($conn->query($update_query)) {
                    $_SESSION['name'] = $name;
                    $_SESSION['email'] = $email;
                    $_SESSION['profile_pic'] = $profile_pic;
                    $success = 'Profile updated successfully!';
                } else {
                    $error = 'Error updating profile. Please try again.';
                }
            }
        }
    }
    
    // Handle password change
    if (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $error = 'All password fields are required.';
        } elseif ($new_password !== $confirm_password) {
            $error = 'New passwords do not match.';
        } elseif (strlen($new_password) < 6) {
            $error = 'New password must be at least 6 characters long.';
        } else {
            // Verify current password
            $user_id = $_SESSION['user_id'];
            $user_query = "SELECT password_hash FROM users WHERE id = $user_id";
            $user_result = $conn->query($user_query);
            $user = $user_result->fetch_assoc();
            
            if (password_verify($current_password, $user['password_hash'])) {
                $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $update_query = "UPDATE users SET password_hash = '$new_password_hash' WHERE id = $user_id";
                
                if ($conn->query($update_query)) {
                    $success = 'Password changed successfully!';
                } else {
                    $error = 'Error changing password. Please try again.';
                }
            } else {
                $error = 'Current password is incorrect.';
            }
        }
    }
}

// Get user's posts
$user_id = $_SESSION['user_id'];
$posts_query = "SELECT * FROM posts WHERE user_id = $user_id ORDER BY created_at DESC";
$posts_result = $conn->query($posts_query);

// Get user info
$user_query = "SELECT * FROM users WHERE id = $user_id";
$user_result = $conn->query($user_query);
$user = $user_result->fetch_assoc();

$page_title = 'Profile - Chintarekha Blog';
include 'header.php';

// Display session messages
if (isset($_SESSION['message'])) {
    $message_type = isset($_SESSION['message_type']) ? $_SESSION['message_type'] : 'info';
    echo '<div class="alert alert-' . $message_type . ' alert-dismissible fade show" role="alert">';
    echo htmlspecialchars($_SESSION['message']);
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
    echo '</div>';
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}
?>

<div class="row">
    <!-- Profile Info -->
    <div class="col-md-4">
        <div class="card shadow mb-4">
            <div class="card-body text-center">
                <?php if (!empty($user['profile_pic'])): ?>
                    <img src="uploads/<?php echo htmlspecialchars($user['profile_pic']); ?>" 
                         alt="Profile Picture" class="profile-pic-lg mb-3">
                <?php else: ?>
                    <div class="profile-pic-lg mx-auto mb-3 bg-secondary d-flex align-items-center justify-content-center text-white">
                        <i class="bi bi-person display-4"></i>
                    </div>
                <?php endif; ?>
                
                <h4><?php echo htmlspecialchars($user['name']); ?></h4>
                <p class="text-muted">@<?php echo htmlspecialchars($user['username']); ?></p>
                
                <?php if (!empty($user['bio'])): ?>
                    <p class="card-text"><?php echo nl2br(htmlspecialchars($user['bio'])); ?></p>
                <?php endif; ?>
                
                <hr>
                <div class="row text-center">
                    <div class="col-6">
                        <h5><?php echo $posts_result->num_rows; ?></h5>
                        <small class="text-muted">Posts</small>
                    </div>
                    <div class="col-6">
                        <?php
                        $comments_count = $conn->query("SELECT COUNT(*) as count FROM comments WHERE user_id = $user_id")->fetch_assoc()['count'];
                        ?>
                        <h5><?php echo $comments_count; ?></h5>
                        <small class="text-muted">Comments</small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Profile Settings -->
        <div class="card shadow">
            <div class="card-header">
                <h5><i class="bi bi-gear"></i> Profile Settings</h5>
            </div>
            <div class="card-body">
                <button class="btn btn-primary btn-sm w-100 mb-2" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                    <i class="bi bi-pencil"></i> Edit Profile
                </button>
                <button class="btn btn-warning btn-sm w-100" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                    <i class="bi bi-lock"></i> Change Password
                </button>
            </div>
        </div>
    </div>
    
    <!-- User Posts -->
    <div class="col-md-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3><i class="bi bi-journal-text"></i> My Posts</h3>
            <a href="create_post.php" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> New Post
            </a>
        </div>
        
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
        
        <?php if ($posts_result->num_rows > 0): ?>
            <?php while ($post = $posts_result->fetch_assoc()): ?>
                <div class="card mb-3 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h5 class="card-title">
                                    <a href="post.php?id=<?php echo $post['id']; ?>" class="text-decoration-none">
                                        <?php echo htmlspecialchars($post['title']); ?>
                                    </a>
                                </h5>
                                <p class="card-text text-muted">
                                    <?php echo htmlspecialchars(substr($post['content'], 0, 150)); ?>
                                    <?php echo strlen($post['content']) > 150 ? '...' : ''; ?>
                                </p>
                                <small class="text-muted">
                                    <i class="bi bi-calendar"></i> <?php echo date('F j, Y', strtotime($post['created_at'])); ?>
                                    <i class="bi bi-eye ms-3"></i> <?php echo $post['view_count']; ?> views
                                    <?php
                                    $comment_count = $conn->query("SELECT COUNT(*) as count FROM comments WHERE post_id = " . $post['id'])->fetch_assoc()['count'];
                                    ?>
                                    <i class="bi bi-chat-dots ms-3"></i> <?php echo $comment_count; ?> comments
                                </small>
                            </div>
                            <div class="btn-group ms-3" role="group">
                                <a href="edit_post.php?id=<?php echo $post['id']; ?>" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="delete_post.php?id=<?php echo $post['id']; ?>" 
                                   class="btn btn-outline-danger btn-sm"
                                   onclick="return confirm('Are you sure you want to delete this post?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="card text-center">
                <div class="card-body py-5">
                    <i class="bi bi-journal-x display-1 text-muted"></i>
                    <h4 class="mt-3">No Posts Yet</h4>
                    <p class="text-muted">Start sharing your thoughts with the world!</p>
                    <a href="create_post.php" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Write Your First Post
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil"></i> Edit Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?php echo htmlspecialchars($user['name']); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="bio" class="form-label">Bio</label>
                        <textarea class="form-control" id="bio" name="bio" rows="3" 
                                  placeholder="Tell us about yourself..."><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Current Profile Picture</label>
                        <?php if (!empty($user['profile_pic'])): ?>
                            <div class="mb-2">
                                <img src="uploads/<?php echo htmlspecialchars($user['profile_pic']); ?>" 
                                     alt="Profile" style="width: 100px; height: 100px; object-fit: cover;" class="img-thumbnail">
                                <div class="form-check mt-2">
                                    <input type="checkbox" class="form-check-input" id="remove_profile_pic" name="remove_profile_pic" value="1">
                                    <label class="form-check-label" for="remove_profile_pic">
                                        Remove current picture
                                    </label>
                                </div>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No profile picture uploaded</p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="profile_pic" class="form-label">Upload New Profile Picture</label>
                        <input type="file" class="form-control" id="profile_pic" name="profile_pic" accept="image/*">
                        <div class="form-text">Upload JPG, PNG, or GIF files (max 2MB)</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-lock"></i> Change Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                        <div class="form-text">Must be at least 6 characters long</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="change_password" class="btn btn-warning">Change Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>