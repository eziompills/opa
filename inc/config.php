<?php
// Database configuration
$host = 'localhost';
$dbname = 'oplani';
$user = 'root';
$pass = '';
$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

session_start();

// Role helpers
function is_logged() { return isset($_SESSION['user']); }
function user() { return is_logged() ? $_SESSION['user'] : null; }
function is_admin() { return user() && user()['role']=='admin'; }
function is_owner() { return user() && user()['role']=='owner'; }
function is_staff() { return user() && user()['role']=='staff'; }

function require_role($roles = []) {
    if (!is_logged() || !in_array(user()['role'], $roles)) {
        header('Location: /login.php');
        exit;
    }
}
?>
