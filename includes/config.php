<?php
// Configuration file for site

// Database connection settings
$hostname = 'localhost';
$username = 'username';
$password = 'password';
$dbname = 'database';

// Create a database connection
$conn = new mysqli($hostname, $username, $password, $dbname);

// Check for a connection error
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');
header('X-Frame-Options: SAMEORIGIN');

// Sanitize input
function sanitize($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Validate email
function validarEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Get configuration settings for the site
function getConfigSite() {
    global $hostname, $username, $password, $dbname;
    return [
        'hostname' => $hostname,
        'username' => $username,
        'password' => $password,
        'dbname' => $dbname,
    ];
}
?>