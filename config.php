<?php
// Database configuration
$host = 'localhost:3307';
$username = 'root';
$password = '';
$database = 'blog';

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset
$conn->set_charset("utf8mb4");

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Helper function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Helper function to check if user is admin
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

// Helper function to redirect
function redirect($url) {
    header("Location: $url");
    exit;
}

// Helper function to sanitize input
function sanitize($data) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($data));
}
?>