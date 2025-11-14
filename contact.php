<?php
require_once 'config.php';

$message = '';
$message_type = '';

// Handle contact form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $subject = sanitize($_POST['subject']);
    $contact_message = sanitize($_POST['message']);
    
    if (empty($name) || empty($email) || empty($subject) || empty($contact_message)) {
        $message = 'All fields are required.';
        $message_type = 'danger';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Please enter a valid email address.';
        $message_type = 'danger';
    } else {
        // In a real application, you would send an email here
        // For demo purposes, we'll just show a success message
        $message = 'Thank you for your message! We\'ll get back to you soon.';
        $message_type = 'success';
        
        // Clear form data on success
        unset($_POST);
    }
}

$page_title = 'Contact Us - Chintarekha Blog';
include 'header.php';
?>

<div class="row">
    <!-- Contact Form -->
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0"><i class="bi bi-envelope"></i> Get in Touch</h3>
            </div>
            <div class="card-body p-4">
                <p class="mb-4">
                    We'd love to hear from you! Whether you have questions, suggestions, or just want to say hello, 
                    feel free to reach out to us using the form below.
                </p>
                
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                        <?php if ($message_type == 'success'): ?>
                            <i class="bi bi-check-circle"></i>
                        <?php else: ?>
                            <i class="bi bi-exclamation-triangle"></i>
                        <?php endif; ?>
                        <?php echo $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Your Name *</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address *</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="subject" class="form-label">Subject *</label>
                        <select class="form-select" id="subject" name="subject" required>
                            <option value="">Choose a subject...</option>
                            <option value="General Inquiry" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'General Inquiry') ? 'selected' : ''; ?>>General Inquiry</option>
                            <option value="Technical Support" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Technical Support') ? 'selected' : ''; ?>>Technical Support</option>
                            <option value="Feature Request" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Feature Request') ? 'selected' : ''; ?>>Feature Request</option>
                            <option value="Bug Report" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Bug Report') ? 'selected' : ''; ?>>Bug Report</option>
                            <option value="Partnership" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Partnership') ? 'selected' : ''; ?>>Partnership Opportunity</option>
                            <option value="Other" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label for="message" class="form-label">Your Message *</label>
                        <textarea class="form-control" id="message" name="message" rows="6" 
                                  placeholder="Please share your thoughts, questions, or feedback..."
                                  required><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-send"></i> Send Message
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Contact Information -->
    <div class="col-lg-4">
        <!-- Contact Info Card -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-info-circle"></i> Contact Information</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6><i class="bi bi-envelope text-primary"></i> Email</h6>
                    <p class="text-muted mb-0">hasanmahmudulglhs@gmail.com</p>
                </div>
                
                <div class="mb-3">
                    <h6><i class="bi bi-telephone text-success"></i> Phone</h6>
                    <p class="text-muted mb-0">+880 1234 567890</p>
                </div>
                
                <div class="mb-3">
                    <h6><i class="bi bi-geo-alt text-danger"></i> Address</h6>
                    <p class="text-muted mb-0">
                        123 Narinda<br>
                        Dhaka South<br>
                        Dhaka-1200
                    </p>
                </div>
                
                <div>
                    <h6><i class="bi bi-clock text-warning"></i> Response Time</h6>
                    <p class="text-muted mb-0">We typically respond within 24-48 hours during business days.</p>
                </div>
            </div>
        </div>
        
        <!-- FAQ Card -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="bi bi-question-circle"></i> Quick Help</h5>
            </div>
            <div class="card-body">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                How do I create an account?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Click on "Sign Up" in the navigation bar and fill out the registration form with your details.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                How do I write a post?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                After logging in, click "Write Post" in the navigation bar to access the post editor.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                Can I edit my posts?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Yes! You can edit your posts anytime from your profile page or directly from the post view.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Social Links -->
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="bi bi-share"></i> Connect With Us</h5>
            </div>
            <div class="card-body text-center">
                <p class="text-muted mb-3">Follow us on social media for updates and community highlights!</p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="#" class="btn btn-outline-primary btn-lg">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <a href="#" class="btn btn-outline-info btn-lg">
                        <i class="bi bi-twitter"></i>
                    </a>
                    <a href="#" class="btn btn-outline-danger btn-lg">
                        <i class="bi bi-instagram"></i>
                    </a>
                    <a href="#" class="btn btn-outline-dark btn-lg">
                        <i class="bi bi-github"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>