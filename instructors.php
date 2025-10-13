<?php
// instructors.php - CRUD Operations for Instructors
require_once 'config.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'create':
        createInstructor();
        break;
    case 'read':
        readInstructors();
        break;
    case 'update':
        updateInstructor();
        break;
    case 'delete':
        deleteInstructor();
        break;
    case 'getOne':
        getInstructor();
        break;
    case 'restore':
        restoreInstructor();
        break;
    case 'readDeleted':
        readDeletedInstructors();
        break;
    default:
        sendResponse(false, 'Invalid action');
}

function createInstructor() {
    global $conn;
    
    $last_name = sanitize($_POST['last_name']);
    $first_name = sanitize($_POST['first_name']);
    $email = sanitize($_POST['email']);
    $dept_id = intval($_POST['dept_id']);
    
    if (empty($last_name) || empty($first_name) || empty($email) || empty($dept_id)) {
        sendResponse(false, 'All fields are required');
    }
    
    $sql = "INSERT INTO tblInstructors (last_name, first_name, email, dept_id) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $last_name, $first_name, $email, $dept_id);
    
    if ($stmt->execute()) {
        sendResponse(true, 'Instructor created successfully', ['id' => $conn->insert_id]);
    } else {
        sendResponse(false, 'Error: ' . $stmt->error);
    }
}

function readInstructors() {
    global $conn;
    
    $search = sanitize($_GET['search'] ?? '');
    
    if (!empty($search)) {
        $sql = "SELECT i.*, d.dept_name, d.dept_code
                FROM tblInstructors i
                LEFT JOIN tblDepartments d ON i.dept_id = d.dept_id AND d.deleted_at IS NULL
                WHERE (i.last_name LIKE ? OR i.first_name LIKE ? OR i.email LIKE ? OR d.dept_name LIKE ?)
                AND i.deleted_at IS NULL
                ORDER BY i.instructor_id";
        $stmt = $conn->prepare($sql);
        $searchTerm = "%$search%";
        $stmt->bind_param("ssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    } else {
        $sql = "SELECT i.*, d.dept_name, d.dept_code
                FROM tblInstructors i
                LEFT JOIN tblDepartments d ON i.dept_id = d.dept_id AND d.deleted_at IS NULL
                WHERE i.deleted_at IS NULL
                ORDER BY i.instructor_id";
        $stmt = $conn->prepare($sql);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $instructors = [];
    
    while ($row = $result->fetch_assoc()) {
        $instructors[] = $row;
    }
    
    sendResponse(true, 'Instructors retrieved successfully', $instructors);
}

function updateInstructor() {
    global $conn;
    
    $instructor_id = intval($_POST['instructor_id']);
    $last_name = sanitize($_POST['last_name']);
    $first_name = sanitize($_POST['first_name']);
    $email = sanitize($_POST['email']);
    $dept_id = intval($_POST['dept_id']);
    
    if (empty($last_name) || empty($first_name) || empty($email) || empty($dept_id)) {
        sendResponse(false, 'All fields are required');
    }
    
    $sql = "UPDATE tblInstructors SET last_name = ?, first_name = ?, email = ?, dept_id = ? WHERE instructor_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssii", $last_name, $first_name, $email, $dept_id, $instructor_id);
    
    if ($stmt->execute()) {
        sendResponse(true, 'Instructor updated successfully');
    } else {
        sendResponse(false, 'Error: ' . $stmt->error);
    }
}

function deleteInstructor() {
    global $conn;
    
    $instructor_id = intval($_POST['instructor_id']);
    
    if (softDelete('tblInstructors', 'instructor_id', $instructor_id)) {
        sendResponse(true, 'Instructor deleted successfully');
    } else {
        sendResponse(false, 'Error deleting instructor');
    }
}

function getInstructor() {
    global $conn;
    
    $instructor_id = intval($_GET['instructor_id']);
    
    $sql = "SELECT i.*, d.dept_name, d.dept_code
            FROM tblInstructors i
            LEFT JOIN tblDepartments d ON i.dept_id = d.dept_id
            WHERE i.instructor_id = ? AND i.deleted_at IS NULL";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $instructor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        sendResponse(true, 'Instructor retrieved successfully', $row);
    } else {
        sendResponse(false, 'Instructor not found');
    }
}

function restoreInstructor() {
    global $conn;
    
    $instructor_id = intval($_POST['instructor_id']);
    
    if (restoreDeleted('tblInstructors', 'instructor_id', $instructor_id)) {
        sendResponse(true, 'Instructor restored successfully');
    } else {
        sendResponse(false, 'Error restoring instructor');
    }
}

function readDeletedInstructors() {
    global $conn;
    
    $sql = "SELECT i.*, d.dept_name, d.dept_code
            FROM tblInstructors i
            LEFT JOIN tblDepartments d ON i.dept_id = d.dept_id
            WHERE i.deleted_at IS NOT NULL 
            ORDER BY i.deleted_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $instructors = [];
    
    while ($row = $result->fetch_assoc()) {
        $instructors[] = $row;
    }
    
    sendResponse(true, 'Deleted instructors retrieved successfully', $instructors);
}
?>