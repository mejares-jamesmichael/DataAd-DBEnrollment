<?php
// config.php - Database Configuration and Connection

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'dbenrollment');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die(json_encode([
        'success' => false,
        'message' => 'Database connection failed: ' . $conn->connect_error
    ]));
}

// Set charset to utf8mb4
$conn->set_charset("utf8mb4");

// Function to sanitize input
function sanitize($data) {
    global $conn;
    return $conn->real_escape_string(trim($data));
}

// Function to send JSON response
function sendResponse($success, $message, $data = null) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

// Soft Delete Helper Functions
function softDelete($table, $idColumn, $id) {
    global $conn;
    $sql = "UPDATE $table SET is_deleted = 1 WHERE $idColumn = ? AND is_deleted = 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

function restoreDeleted($table, $idColumn, $id) {
    global $conn;
    $sql = "UPDATE $table SET is_deleted = 0 WHERE $idColumn = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

function permanentDelete($table, $idColumn, $id) {
    global $conn;
    $sql = "DELETE FROM $table WHERE $idColumn = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

// Enable error reporting for development (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>