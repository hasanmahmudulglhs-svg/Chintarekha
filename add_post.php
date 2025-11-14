<?php
require_once 'config.php';

// Require login
requireLogin();

$message = '';
$error = '';
$currentUser = getCurrentUser();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = sanitize($_POST['title']);
    $content = sanitize($_POST['content']);
    $image_url = '';
    
    if (empty($title) || empty($content)) {
        $error = 'Title and content are required.';
    } else {
        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $upload_result = uploadImage($_FILES['image']);
            if ($upload_result['success']) {
                $image_url = $upload_result['filepath'];
            } else {
                $error = $upload_result['message'];
            }
        }
        
        if (empty($error)) {
            $user_id = $_SESSION['user_id'];
            $sql = "INSERT INTO posts (user_id, title, content, image_url) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isss", $user_id, $title, $content, $image_url);
            
            if ($stmt->execute()) {
                $message = 'Post published successfully!';
                header('refresh:2;url=index.php');
            } else {
                $error = 'Error publishing post: ' . $conn->error;
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Write Post - Chintarekha Blog</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <h4 class="mb-0">Chintarekha</h4>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="add_post.php">Write Post</a>
                    </li>
                    <?php if ($currentUser['role'] === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="admin.php">Admin</a>
                        </li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            Welcome, <?php echo htmlspecialchars($currentUser['name']); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main>
        <div class="container mt-4">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow">
                        <div class="card-header">
                            <h2 class="card-title mb-0">Write a New Post</h2>
                        </div>
                        <div class="card-body">
                            <?php if ($message): ?>
                                <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
                            <?php endif; ?>
                            
                            <?php if ($error): ?>
                                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                            <?php endif; ?>
                            
                            <form method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Post Title *</label>
                                    <input type="text" class="form-control" id="title" name="title" required 
                                           value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>"
                                           placeholder="Enter an engaging title for your post">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="image" class="form-label">Post Image (optional)</label>
                                    <input type="file" class="form-control" id="image" name="image" 
                                           accept="image/jpeg,image/png,image/gif,image/webp">
                                    <div class="form-text">Upload an image for your post. Supported formats: JPG, PNG, GIF, WebP. Maximum size: 5MB.</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="content" class="form-label">Post Content *</label>
                                    <textarea class="form-control" id="content" name="content" rows="12" required 
                                              placeholder="Write your blog post content here..."><?php echo isset($_POST['content']) ? htmlspecialchars($_POST['content']) : ''; ?></textarea>
                                </div>
                                
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane"></i> Publish Post
                                    </button>
                                    <a href="index.php" class="btn btn-secondary">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container text-center">
            <p class="mb-0">&copy; 2025 Chintarekha Blog. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Write Post - Chintarekha Blog</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <h4 class="mb-0">Chintarekha</h4>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="add_post.php">Write Post</a>
                    </li>
                    <?php if ($currentUser['role'] === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="admin.php">Admin</a>
                        </li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            Welcome, <?php echo htmlspecialchars($currentUser['name']); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main>
        <div class="container mt-4">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow">
                        <div class="card-header">
                            <h2 class="card-title mb-0">Write a New Post</h2>
                        </div>
                        <div class="card-body">
                            <?php if ($message): ?>
                                <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
                            <?php endif; ?>
                            
                            <?php if ($error): ?>
                                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                            <?php endif; ?>
                            
                            <form method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Post Title *</label>
                                    <input type="text" class="form-control" id="title" name="title" required 
                                           value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>"
                                           placeholder="Enter an engaging title for your post">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="image" class="form-label">Post Image (optional)</label>
                                    <input type="file" class="form-control" id="image" name="image" 
                                           accept="image/jpeg,image/png,image/gif,image/webp">
                                    <div class="form-text">Upload an image for your post. Supported formats: JPG, PNG, GIF, WebP. Maximum size: 5MB.</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="content" class="form-label">Post Content *</label>
                                    <textarea class="form-control" id="content" name="content" rows="12" required 
                                              placeholder="Write your blog post content here..."><?php echo isset($_POST['content']) ? htmlspecialchars($_POST['content']) : ''; ?></textarea>
                                </div>
                                
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane"></i> Publish Post
                                    </button>
                                    <a href="index.php" class="btn btn-secondary">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container text-center">
            <p class="mb-0">&copy; 2025 Chintarekha Blog. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>