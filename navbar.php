<?php
require_once 'config.php';

// Get current page name
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">
            <i class="bi bi-journal-text"></i> Chintarekha Blog
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>" href="index.php"><i class="bi bi-house"></i> Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'about.php') ? 'active' : ''; ?>" href="about.php"><i class="bi bi-info-circle"></i> About Us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'contact.php') ? 'active' : ''; ?>" href="contact.php"><i class="bi bi-envelope"></i> Contact Us</a>
                </li>
                <?php if (isLoggedIn()): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'create_post.php') ? 'active' : ''; ?>" href="create_post.php"><i class="bi bi-plus-circle"></i> Write Post</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'profile.php') ? 'active' : ''; ?>" href="profile.php"><i class="bi bi-person"></i> Profile</a>
                </li>
                <?php if (isAdmin()): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'admin.php') ? 'active' : ''; ?>" href="admin.php"><i class="bi bi-gear"></i> Admin</a>
                </li>
                <?php endif; ?>
                <?php endif; ?>
            </ul>
            
            <ul class="navbar-nav">
                <?php if (isLoggedIn()): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <?php if (!empty($_SESSION['profile_pic'])): ?>
                                <img src="uploads/<?php echo htmlspecialchars($_SESSION['profile_pic']); ?>" 
                                     alt="Profile" class="profile-pic me-1">
                            <?php endif; ?>
                            <?php echo htmlspecialchars($_SESSION['username']); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person"></i> Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php"><i class="bi bi-box-arrow-in-right"></i> Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="signup.php"><i class="bi bi-person-plus"></i> Sign Up</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>