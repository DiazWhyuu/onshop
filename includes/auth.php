<?php
require_once 'config.php';

/**
 * Cek apakah user sudah login
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Cek apakah user adalah admin
 */
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Redirect user yang belum login
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: ../user/login.php");
        exit();
    }
}

/**
 * Redirect user yang bukan admin
 */
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header("Location: ../index.php");
        exit();
    }
}

/**
 * Register user baru
 */
function registerUser($username, $password, $email, $full_name) {
    global $conn;
    
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (username, password, email, full_name, role) VALUES (?, ?, ?, ?, 'user')");
    $stmt->bind_param("ssss", $username, $hashed_password, $email, $full_name);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Registrasi berhasil! Silakan login.";
        return true;
    }
    return false;
}

/**
 * Update last login time
 */
function updateLastLogin($user_id) {
    global $conn;
    
    $stmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
}
?>