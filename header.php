<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Chintarekha Blog'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Post images for index page previews */
        .post-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px 8px 0 0;
        }
        
        /* Post images for full post view */
        .post-full-image {
            width: 100%;
            height: 300px;
            object-fit: contain;
            border-radius: 12px;
            background-color: #f8f9fa;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .post-image {
                height: 150px;
            }
            .post-full-image {
                height: 250px;
            }
        }
        
        @media (max-width: 480px) {
            .post-image {
                height: 120px;
            }
            .post-full-image {
                height: 200px;
            }
        }
        
        /* Image loading and hover effects */
        .post-image, .post-full-image {
            transition: transform 0.3s ease;
            background-color: #f8f9fa;
        }
        
        .post-image:hover {
            transform: scale(1.02);
        }
        
        /* Ensure images don't break layout */
        .card-img-top {
            border-bottom: 1px solid rgba(0,0,0,.125);
        }
        
        /* Active navigation tab styling */
        .navbar-nav .nav-link.active {
            background-color: rgba(0,0,0,0.2) !important;
            border-radius: 6px;
            font-weight: 600;
        }
        
        .navbar-nav .nav-link:hover {
            background-color: rgba(0,0,0,0.1);
            border-radius: 6px;
        }
        .profile-pic {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
        }
        .profile-pic-lg {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
        }
        .post-preview .post-content {
            max-height: 150px;
            overflow: hidden;
        }
        .post-full .post-content {
            line-height: 1.6;
        }
        .comment-box {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-4">