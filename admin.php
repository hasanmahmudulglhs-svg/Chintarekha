<?php
require_once 'config.php';

// Check if user is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('index.php');
}

$message = '';
$message_type = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_post'])) {
        $post_id = (int)$_POST['post_id'];
        
        // Get post image for deletion
        $post_query = "SELECT image_url FROM posts WHERE id = $post_id";
        $post_result = $conn->query($post_query);
        
        if ($post_result->num_rows > 0) {
            $post = $post_result->fetch_assoc();
            
            // Delete image file if exists
            if (!empty($post['image_url']) && file_exists('uploads/' . $post['image_url'])) {
                unlink('uploads/' . $post['image_url']);
            }
            
            // Delete post
            if ($conn->query("DELETE FROM posts WHERE id = $post_id")) {
                $message = 'Post deleted successfully!';
                $message_type = 'success';
            } else {
                $message = 'Error deleting post.';
                $message_type = 'danger';
            }
        }
    }
    
    if (isset($_POST['delete_user'])) {
        $user_id = (int)$_POST['user_id'];
        
        // Don't allow admin to delete themselves
        if ($user_id != $_SESSION['user_id']) {
            // Get user's profile pic for deletion
            $user_query = "SELECT profile_pic FROM users WHERE id = $user_id";
            $user_result = $conn->query($user_query);
            
            if ($user_result->num_rows > 0) {
                $user = $user_result->fetch_assoc();
                
                // Delete profile pic if exists
                if (!empty($user['profile_pic']) && file_exists('uploads/' . $user['profile_pic'])) {
                    unlink('uploads/' . $user['profile_pic']);
                }
                
                // Delete user (posts and comments will be deleted automatically due to foreign key cascade)
                if ($conn->query("DELETE FROM users WHERE id = $user_id")) {
                    $message = 'User deleted successfully!';
                    $message_type = 'success';
                } else {
                    $message = 'Error deleting user.';
                    $message_type = 'danger';
                }
            }
        } else {
            $message = 'You cannot delete your own admin account.';
            $message_type = 'warning';
        }
    }
    
    if (isset($_POST['toggle_role'])) {
        $user_id = (int)$_POST['user_id'];
        $new_role = $_POST['new_role'];
        
        // Don't allow admin to change their own role
        if ($user_id != $_SESSION['user_id']) {
            if ($conn->query("UPDATE users SET role = '$new_role' WHERE id = $user_id")) {
                $message = 'User role updated successfully!';
                $message_type = 'success';
            } else {
                $message = 'Error updating user role.';
                $message_type = 'danger';
            }
        } else {
            $message = 'You cannot change your own admin role.';
            $message_type = 'warning';
        }
    }
}

// Get statistics
$total_posts = $conn->query("SELECT COUNT(*) as count FROM posts")->fetch_assoc()['count'];
$total_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$total_comments = $conn->query("SELECT COUNT(*) as count FROM comments")->fetch_assoc()['count'];
$total_admins = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'admin'")->fetch_assoc()['count'];

// Get all posts
$posts_query = "SELECT p.*, u.username, u.name 
                FROM posts p 
                JOIN users u ON p.user_id = u.id 
                ORDER BY p.created_at DESC";
$posts_result = $conn->query($posts_query);

// Get all users
$users_query = "SELECT * FROM users ORDER BY created_at DESC";
$users_result = $conn->query($users_query);

// Get recent comments
$comments_query = "SELECT c.*, p.title as post_title, u.username, u.name 
                   FROM comments c 
                   JOIN posts p ON c.post_id = p.id 
                   JOIN users u ON c.user_id = u.id 
                   ORDER BY c.created_at DESC 
                   LIMIT 10";
$comments_result = $conn->query($comments_query);

$page_title = 'Admin Panel - Chintarekha Blog';
include 'header.php';
?>

<?php if ($message): ?>
    <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
        <?php echo $message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Admin Dashboard Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-gear"></i> Admin Dashboard</h2>
    <div class="btn-group" role="group">
        <a href="create_post.php" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> New Post
        </a>
        <a href="index.php" class="btn btn-outline-secondary">
            <i class="bi bi-house"></i> Back to Site
        </a>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4><?php echo $total_posts; ?></h4>
                        <p class="mb-0">Total Posts</p>
                    </div>
                    <i class="bi bi-journal-text display-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4><?php echo $total_users; ?></h4>
                        <p class="mb-0">Total Users</p>
                    </div>
                    <i class="bi bi-people display-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4><?php echo $total_comments; ?></h4>
                        <p class="mb-0">Total Comments</p>
                    </div>
                    <i class="bi bi-chat-dots display-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4><?php echo $total_admins; ?></h4>
                        <p class="mb-0">Administrators</p>
                    </div>
                    <i class="bi bi-shield-check display-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabs -->
<ul class="nav nav-tabs mb-4" id="adminTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="posts-tab" data-bs-toggle="tab" data-bs-target="#posts" type="button" role="tab">
            <i class="bi bi-journal-text"></i> Manage Posts
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab">
            <i class="bi bi-people"></i> Manage Users
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="comments-tab" data-bs-toggle="tab" data-bs-target="#comments" type="button" role="tab">
            <i class="bi bi-chat-dots"></i> Recent Comments
        </button>
    </li>
</ul>

<div class="tab-content" id="adminTabContent">
    <!-- Posts Tab -->
    <div class="tab-pane fade show active" id="posts" role="tabpanel">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5><i class="bi bi-journal-text"></i> All Posts (<?php echo $total_posts; ?>)</h5>
            </div>
            <div class="card-body">
                <?php if ($posts_result->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Author</th>
                                    <th>Views</th>
                                    <th>Comments</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($post = $posts_result->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <a href="post.php?id=<?php echo $post['id']; ?>" class="text-decoration-none">
                                                <?php echo htmlspecialchars(substr($post['title'], 0, 50)); ?>
                                                <?php echo strlen($post['title']) > 50 ? '...' : ''; ?>
                                            </a>
                                        </td>
                                        <td><?php echo htmlspecialchars($post['name']); ?></td>
                                        <td><?php echo $post['view_count']; ?></td>
                                        <td>
                                            <?php
                                            $comment_count = $conn->query("SELECT COUNT(*) as count FROM comments WHERE post_id = " . $post['id'])->fetch_assoc()['count'];
                                            echo $comment_count;
                                            ?>
                                        </td>
                                        <td><?php echo date('M j, Y', strtotime($post['created_at'])); ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="edit_post.php?id=<?php echo $post['id']; ?>" class="btn btn-outline-secondary">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form method="POST" style="display: inline;" 
                                                      onsubmit="return confirm('Are you sure you want to delete this post?')">
                                                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                                    <button type="submit" name="delete_post" class="btn btn-outline-danger">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-journal-x display-1 text-muted"></i>
                        <h4 class="mt-3">No Posts Found</h4>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Users Tab -->
    <div class="tab-pane fade" id="users" role="tabpanel">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5><i class="bi bi-people"></i> All Users (<?php echo $total_users; ?>)</h5>
            </div>
            <div class="card-body">
                <?php if ($users_result->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Posts</th>
                                    <th>Joined</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($user = $users_result->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php if ($user['profile_pic']): ?>
                                                    <img src="uploads/<?php echo htmlspecialchars($user['profile_pic']); ?>" 
                                                         alt="Profile" class="profile-pic me-2">
                                                <?php else: ?>
                                                    <div class="profile-pic me-2 bg-secondary d-flex align-items-center justify-content-center text-white">
                                                        <i class="bi bi-person"></i>
                                                    </div>
                                                <?php endif; ?>
                                                <div>
                                                    <strong><?php echo htmlspecialchars($user['name']); ?></strong><br>
                                                    <small class="text-muted">@<?php echo htmlspecialchars($user['username']); ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $user['role'] === 'admin' ? 'danger' : 'primary'; ?>">
                                                <?php echo ucfirst($user['role']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php
                                            $user_posts = $conn->query("SELECT COUNT(*) as count FROM posts WHERE user_id = " . $user['id'])->fetch_assoc()['count'];
                                            echo $user_posts;
                                            ?>
                                        </td>
                                        <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                                        <td>
                                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <!-- Toggle Role -->
                                                    <form method="POST" style="display: inline;">
                                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                        <input type="hidden" name="new_role" value="<?php echo $user['role'] === 'admin' ? 'user' : 'admin'; ?>">
                                                        <button type="submit" name="toggle_role" 
                                                                class="btn btn-outline-<?php echo $user['role'] === 'admin' ? 'warning' : 'success'; ?>"
                                                                onclick="return confirm('Are you sure you want to change this user\'s role?')">
                                                            <i class="bi bi-<?php echo $user['role'] === 'admin' ? 'person-dash' : 'person-plus'; ?>"></i>
                                                        </button>
                                                    </form>
                                                    
                                                    <!-- Delete User -->
                                                    <form method="POST" style="display: inline;">
                                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                        <button type="submit" name="delete_user" class="btn btn-outline-danger"
                                                                onclick="return confirm('Are you sure you want to delete this user? All their posts and comments will be deleted too.')">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            <?php else: ?>
                                                <small class="text-muted">Current User</small>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-people display-1 text-muted"></i>
                        <h4 class="mt-3">No Users Found</h4>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Comments Tab -->
    <div class="tab-pane fade" id="comments" role="tabpanel">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5><i class="bi bi-chat-dots"></i> Recent Comments</h5>
            </div>
            <div class="card-body">
                <?php if ($comments_result->num_rows > 0): ?>
                    <?php while ($comment = $comments_result->fetch_assoc()): ?>
                        <div class="comment-box">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        <?php echo htmlspecialchars($comment['name']); ?>
                                        <small class="text-muted">commented on</small>
                                        <a href="post.php?id=<?php echo $comment['post_id']; ?>" class="text-decoration-none">
                                            <?php echo htmlspecialchars(substr($comment['post_title'], 0, 30)); ?>
                                            <?php echo strlen($comment['post_title']) > 30 ? '...' : ''; ?>
                                        </a>
                                    </h6>
                                    <p class="mb-1"><?php echo nl2br(htmlspecialchars($comment['content'])); ?></p>
                                    <small class="text-muted">
                                        <?php echo date('F j, Y \a\t g:i A', strtotime($comment['created_at'])); ?>
                                    </small>
                                </div>
                                <a href="post.php?id=<?php echo $comment['post_id']; ?>" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-chat-dots display-1 text-muted"></i>
                        <h4 class="mt-3">No Comments Found</h4>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>