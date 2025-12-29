<?php
/**
 * Authentication and Authorization Module
 * Handles user login, logout, session management, and role-based access control
 */

require_once 'config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'login':
        login();
        break;
    case 'logout':
        logout();
        break;
    case 'checkSession':
        checkSession();
        break;
    case 'getCurrentUser':
        getCurrentUser();
        break;
    case 'changePassword':
        changePassword();
        break;
    default:
        sendResponse(false, 'Invalid action');
}

/**
 * User login
 */
function login() {
    global $conn;
    
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        sendResponse(false, 'Username and password are required');
        return;
    }
    
    $sql = "SELECT user_id, username, password, role, related_id, email, full_name, is_active 
            FROM tbl_users 
            WHERE username = ? AND is_deleted = 0";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        logActivity(null, 'login_failed', 'user', null, "Failed login attempt for username: $username");
        sendResponse(false, 'Invalid username or password');
        return;
    }
    
    $user = $result->fetch_assoc();
    
    // Check if account is active
    if ($user['is_active'] != 1) {
        sendResponse(false, 'Account is inactive. Please contact administrator.');
        return;
    }
    
    // Verify password
    if (!password_verify($password, $user['password'])) {
        logActivity(null, 'login_failed', 'user', $user['user_id'], "Failed login attempt for user: {$user['username']}");
        sendResponse(false, 'Invalid username or password');
        return;
    }
    
    // Set session variables
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['related_id'] = $user['related_id'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['logged_in'] = true;
    
    // Update last login
    $updateSql = "UPDATE tbl_users SET last_login = NOW() WHERE user_id = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("i", $user['user_id']);
    $updateStmt->execute();
    
    // Log successful login
    logActivity($user['user_id'], 'login', 'user', $user['user_id'], "User logged in successfully");
    
    unset($user['password']);
    sendResponse(true, 'Login successful', $user);
}

/**
 * User logout
 */
function logout() {
    $user_id = $_SESSION['user_id'] ?? null;
    $username = $_SESSION['username'] ?? 'Unknown';
    
    // Log logout activity
    if ($user_id) {
        logActivity($user_id, 'logout', 'user', $user_id, "User logged out");
    }
    
    // Clear session
    session_unset();
    session_destroy();
    
    sendResponse(true, 'Logged out successfully');
}

/**
 * Check if user session is valid
 */
function checkSession() {
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
        sendResponse(true, 'Session is active', [
            'user_id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'role' => $_SESSION['role'],
            'full_name' => $_SESSION['full_name'],
            'email' => $_SESSION['email']
        ]);
    } else {
        sendResponse(false, 'No active session');
    }
}

/**
 * Get current logged-in user info
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        sendResponse(false, 'Not logged in');
        return;
    }
    
    sendResponse(true, 'User retrieved', [
        'user_id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        'role' => $_SESSION['role'],
        'full_name' => $_SESSION['full_name'],
        'email' => $_SESSION['email'],
        'related_id' => $_SESSION['related_id']
    ]);
}

/**
 * Change user password
 */
function changePassword() {
    global $conn;
    
    if (!isLoggedIn()) {
        sendResponse(false, 'Not logged in');
        return;
    }
    
    $user_id = $_SESSION['user_id'];
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        sendResponse(false, 'All fields are required');
        return;
    }
    
    if ($new_password !== $confirm_password) {
        sendResponse(false, 'New passwords do not match');
        return;
    }
    
    if (strlen($new_password) < 6) {
        sendResponse(false, 'New password must be at least 6 characters');
        return;
    }
    
    // Verify current password
    $sql = "SELECT password FROM tbl_users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if (!password_verify($current_password, $user['password'])) {
        sendResponse(false, 'Current password is incorrect');
        return;
    }
    
    // Update password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $updateSql = "UPDATE tbl_users SET password = ? WHERE user_id = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("si", $hashed_password, $user_id);
    
    if ($updateStmt->execute()) {
        logActivity($user_id, 'password_changed', 'user', $user_id, "User changed password");
        sendResponse(true, 'Password changed successfully');
    } else {
        sendResponse(false, 'Failed to change password');
    }
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

/**
 * Check if user has required role
 */
function hasRole($required_roles) {
    if (!isLoggedIn()) {
        return false;
    }
    
    $user_role = $_SESSION['role'];
    
    if (is_array($required_roles)) {
        return in_array($user_role, $required_roles);
    }
    
    return $user_role === $required_roles;
}

/**
 * Require authentication - redirect to login if not logged in
 */
function requireAuth() {
    if (!isLoggedIn()) {
        sendResponse(false, 'Authentication required', ['redirect' => 'login']);
        exit;
    }
}

/**
 * Require specific role(s)
 */
function requireRole($required_roles) {
    requireAuth();
    
    if (!hasRole($required_roles)) {
        sendResponse(false, 'Access denied. Insufficient permissions.');
        exit;
    }
}

/**
 * Get permissions for current user role
 */
function getPermissions() {
    if (!isLoggedIn()) {
        return [];
    }
    
    $role = $_SESSION['role'];
    
    $permissions = [
        'admin' => [
            'view_all' => true,
            'create_all' => true,
            'edit_all' => true,
            'delete_all' => true,
            'manage_users' => true,
            'backup_restore' => true,
            'view_logs' => true
        ],
        'registrar' => [
            'view_all' => true,
            'create_all' => true,
            'edit_all' => true,
            'delete_all' => true,
            'manage_users' => false,
            'backup_restore' => true,
            'view_logs' => false
        ],
        'instructor' => [
            'view_all' => true,
            'create_all' => false,
            'edit_all' => false,
            'delete_all' => false,
            'manage_users' => false,
            'backup_restore' => false,
            'view_logs' => false,
            'view_own_sections' => true,
            'edit_own_sections' => true,
            'manage_grades' => true
        ],
        'student' => [
            'view_all' => false,
            'create_all' => false,
            'edit_all' => false,
            'delete_all' => false,
            'manage_users' => false,
            'backup_restore' => false,
            'view_logs' => false,
            'view_own_enrollment' => true,
            'enroll_courses' => true
        ]
    ];
    
    return $permissions[$role] ?? [];
}

/**
 * Log user activity
 */
function logActivity($user_id, $action, $entity_type, $entity_id, $description) {
    global $conn;
    
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    
    $sql = "INSERT INTO tbl_activity_logs (user_id, action, entity_type, entity_id, description, ip_address) 
            VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ississ", $user_id, $action, $entity_type, $entity_id, $description, $ip_address);
    $stmt->execute();
}
