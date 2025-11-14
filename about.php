<?php
require_once 'config.php';

$page_title = 'About Us - Chintarekha Blog';
include 'header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <!-- Hero Section -->
        <div class="text-center mb-5">
            <h1 class="display-4 mb-4"><i class="bi bi-journal-text text-primary"></i> About Chintarekha</h1>
            <p class="lead text-muted">Where stories come to life and voices find their home</p>
        </div>

        <!-- About Content -->
        <div class="card shadow-sm mb-5">
            <div class="card-body p-5">
                <div class="row">
                    <div class="col-md-4 text-center mb-4">
                        <i class="bi bi-lightbulb display-1 text-warning mb-3"></i>
                        <h4>Our Vision</h4>
                        <p class="text-muted">To create a platform where every voice matters and every story finds its audience.</p>
                    </div>
                    <div class="col-md-4 text-center mb-4">
                        <i class="bi bi-heart display-1 text-danger mb-3"></i>
                        <h4>Our Mission</h4>
                        <p class="text-muted">Empowering writers to share their thoughts, experiences, and creativity with the world.</p>
                    </div>
                    <div class="col-md-4 text-center mb-4">
                        <i class="bi bi-people display-1 text-success mb-3"></i>
                        <h4>Our Community</h4>
                        <p class="text-muted">Building connections through authentic storytelling and meaningful conversations.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Story Section -->
        <div class="card shadow-sm mb-5">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0"><i class="bi bi-book"></i> Our Story</h3>
            </div>
            <div class="card-body p-4">
                <p class="mb-4">
                    <strong>Chintarekha</strong> was born from a simple belief: everyone has a story worth telling. 
                    In a world filled with noise, we wanted to create a space where authentic voices could rise above the clutter.
                </p>
                
                <p class="mb-4">
                    Our platform combines the simplicity of traditional blogging with modern, responsive design. 
                    Whether you're a seasoned writer or someone who's never published a word, Chintarekha welcomes you 
                    to share your thoughts, experiences, and creativity.
                </p>
                
                <div class="row mt-4">
                    <div class="col-md-6">
                        <h5><i class="bi bi-check-circle text-success"></i> What We Offer</h5>
                        <ul class="list-unstyled ms-3">
                            <li><i class="bi bi-arrow-right text-primary"></i> Easy-to-use writing interface</li>
                            <li><i class="bi bi-arrow-right text-primary"></i> Image upload capabilities</li>
                            <li><i class="bi bi-arrow-right text-primary"></i> Interactive comment system</li>
                            <li><i class="bi bi-arrow-right text-primary"></i> Personal profile management</li>
                            <li><i class="bi bi-arrow-right text-primary"></i> Mobile-responsive design</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5><i class="bi bi-star text-warning"></i> Why Choose Us</h5>
                        <ul class="list-unstyled ms-3">
                            <li><i class="bi bi-arrow-right text-primary"></i> Clean, distraction-free reading</li>
                            <li><i class="bi bi-arrow-right text-primary"></i> Supportive community environment</li>
                            <li><i class="bi bi-arrow-right text-primary"></i> No ads or commercial clutter</li>
                            <li><i class="bi bi-arrow-right text-primary"></i> Focus on content quality</li>
                            <li><i class="bi bi-arrow-right text-primary"></i> Regular updates and improvements</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Team Section -->
        <div class="card shadow-sm mb-5">
            <div class="card-header bg-success text-white">
                <h3 class="mb-0"><i class="bi bi-people-fill"></i> Our Values</h3>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="d-flex">
                            <i class="bi bi-shield-check text-primary fs-3 me-3"></i>
                            <div>
                                <h5>Authenticity</h5>
                                <p class="text-muted mb-0">We believe in genuine, authentic storytelling that connects hearts and minds.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex">
                            <i class="bi bi-hand-thumbs-up text-success fs-3 me-3"></i>
                            <div>
                                <h5>Respect</h5>
                                <p class="text-muted mb-0">Every voice deserves respect, and every opinion matters in our community.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex">
                            <i class="bi bi-lightbulb text-warning fs-3 me-3"></i>
                            <div>
                                <h5>Innovation</h5>
                                <p class="text-muted mb-0">We continuously evolve to provide the best writing and reading experience.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex">
                            <i class="bi bi-globe text-info fs-3 me-3"></i>
                            <div>
                                <h5>Inclusivity</h5>
                                <p class="text-muted mb-0">Our platform welcomes writers and readers from all backgrounds and perspectives.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <?php
        $total_posts = $conn->query("SELECT COUNT(*) as count FROM posts")->fetch_assoc()['count'];
        $total_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
        $total_comments = $conn->query("SELECT COUNT(*) as count FROM comments")->fetch_assoc()['count'];
        ?>
        
        <div class="card shadow-sm mb-5">
            <div class="card-body text-center p-5">
                <h3 class="mb-4">Chintarekha by the Numbers</h3>
                <div class="row">
                    <div class="col-md-4">
                        <div class="border-end border-2">
                            <h2 class="text-primary"><?php echo $total_posts; ?></h2>
                            <p class="text-muted mb-0">Stories Published</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border-end border-2">
                            <h2 class="text-success"><?php echo $total_users; ?></h2>
                            <p class="text-muted mb-0">Active Writers</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <h2 class="text-warning"><?php echo $total_comments; ?></h2>
                        <p class="text-muted mb-0">Conversations Started</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="text-center mb-5">
            <h3 class="mb-4">Ready to Share Your Story?</h3>
            <?php if (isLoggedIn()): ?>
                <a href="create_post.php" class="btn btn-primary btn-lg me-3">
                    <i class="bi bi-plus-circle"></i> Write Your First Post
                </a>
                <a href="profile.php" class="btn btn-outline-secondary btn-lg">
                    <i class="bi bi-person"></i> Manage Your Profile
                </a>
            <?php else: ?>
                <a href="signup.php" class="btn btn-primary btn-lg me-3">
                    <i class="bi bi-person-plus"></i> Join Our Community
                </a>
                <a href="login.php" class="btn btn-outline-secondary btn-lg">
                    <i class="bi bi-box-arrow-in-right"></i> Sign In
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>