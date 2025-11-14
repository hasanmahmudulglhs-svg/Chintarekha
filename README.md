# Chintarekha Blog

A simple, elegant blog platform built with PHP, MySQL, and Bootstrap. Perfect for personal blogging, community writing, and content management.

## 🌟 Features

- **User Authentication**: Secure login and signup system
- **Post Management**: Create, edit, and delete blog posts
- **Image Uploads**: Upload profile pictures and post images  
- **Comment System**: Interactive commenting on posts
- **Admin Panel**: Complete administrative control
- **Responsive Design**: Beautiful Bootstrap-powered UI
- **Role-based Access**: User and Admin roles with different permissions

## 📋 Requirements

- **XAMPP** (or any web server with PHP and MySQL)
- **PHP 7.4+**
- **MySQL 5.7+**
- **Web Browser** with JavaScript enabled

## 🚀 Installation & Setup

### 1. Clone/Download Files
```bash
# Place all files in your XAMPP htdocs folder
C:\xampp\htdocs\Chintarekha\
```

### 2. Database Setup
1. Start XAMPP (Apache and MySQL)
2. Open phpMyAdmin (http://localhost/phpmyadmin)
3. Create a new database named `blog`
4. Import the `blog.sql` file into the database

### 3. Configure Database Connection
The `config.php` file is already configured for XAMPP defaults:
- Host: localhost
- Username: root  
- Password: (empty)
- Database: blog

### 4. Set Folder Permissions
Make sure the `uploads/` folder has write permissions for image uploads.

### 5. Run Setup Script
Visit: `http://localhost/Chintarekha/setup.php`

### 6. Start Blogging!
Visit: `http://localhost/Chintarekha/`


## 📁 Project Structure

```
Chintarekha/
├── config.php          # Database configuration
├── index.php           # Homepage with post listings
├── login.php           # User login page
├── signup.php          # User registration page
├── logout.php          # Logout handler
├── create_post.php     # Create new blog post
├── edit_post.php       # Edit existing post
├── delete_post.php     # Delete post confirmation
├── post.php            # Individual post view with comments
├── profile.php         # User profile management
├── admin.php           # Admin dashboard
├── setup.php           # Initial setup script
├── header.php          # Common HTML header
├── footer.php          # Common HTML footer
├── navbar.php          # Navigation bar
├── blog.sql            # Database structure
├── uploads/            # Image uploads directory
└── README.md           # This file
```

## 👤 User Features

### Regular Users Can:
- ✅ Create account and login
- ✅ Write, edit, and delete their own posts
- ✅ Upload profile pictures and post images
- ✅ Comment on any post
- ✅ Update their profile information
- ✅ Change their password

### Administrators Can:
- ✅ All regular user features
- ✅ Edit and delete ANY post
- ✅ Manage all users (promote/demote, delete)
- ✅ View comprehensive dashboard with statistics
- ✅ Monitor all comments and user activity

## 🎨 UI Features

- **Responsive Design**: Works perfectly on desktop, tablet, and mobile
- **Bootstrap 5**: Modern, clean styling
- **Bootstrap Icons**: Beautiful iconography throughout
- **Image Optimization**: Automatic image resizing and optimization
- **User-Friendly**: Intuitive interface for all skill levels

