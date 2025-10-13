<?php
// departments.php - CRUD Operations for Departments
require_once 'config.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'create':
        createDepartment();
        break;
    case 'read':
        readDepartments();
        break;
    case 'update':
        updateDepartment();
        break;
    case 'delete':
        deleteDepartment();
        break;
    case 'getOne':
        getDepartment();
        break;
    case 'restore':
        restoreDepartment();
        break;
    case 'readDeleted':
        readDeletedDepartments();
        break;
    case 'permanentDelete':
        permanentDeleteDepartment();
        break;
    default:
        sendResponse(false, 'Invalid action');
}

function createDepartment() {
    global $conn;
    
    $dept_code = sanitize($_POST['dept_code']);
    $dept_name = sanitize($_POST['dept_name']);
    
    if (empty($dept_code) || empty($dept_name)) {
        sendResponse(false, 'All fields are required');
    }
    
    $sql = "INSERT INTO tblDepartments (dept_code, dept_name) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $dept_code, $dept_name);
    
    if ($stmt->execute()) {
        sendResponse(true, 'Department created successfully', ['id' => $conn->insert_id]);
    } else {
        sendResponse(false, 'Error: ' . $stmt->error);
    }
}

function readDepartments() {
    global $conn;
    
    $search = sanitize($_GET['search'] ?? '');
    
    if (!empty($search)) {
        $sql = "SELECT * FROM tblDepartments 
                WHERE (dept_code LIKE ? OR dept_name LIKE ?) 
                AND deleted_at IS NULL
                ORDER BY dept_id";
        $stmt = $conn->prepare($sql);
        $searchTerm = "%$search%";
        $stmt->bind_param("ss", $searchTerm, $searchTerm);
    } else {
        $sql = "SELECT * FROM tblDepartments WHERE deleted_at IS NULL ORDER BY dept_id";
        $stmt = $conn->prepare($sql);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $departments = [];
    
    while ($row = $result->fetch_assoc()) {
        $departments[] = $row;
    }
    
    sendResponse(true, 'Departments retrieved successfully', $departments);
}

function updateDepartment() {
    global $conn;
    
    $dept_id = intval($_POST['dept_id']);
    $dept_code = sanitize($_POST['dept_code']);
    $dept_name = sanitize($_POST['dept_name']);
    
    if (empty($dept_code) || empty($dept_name)) {
        sendResponse(false, 'All fields are required');
    }
    
    $sql = "UPDATE tblDepartments SET dept_code = ?, dept_name = ? WHERE dept_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $dept_code, $dept_name, $dept_id);
    
    if ($stmt->execute()) {
        sendResponse(true, 'Department updated successfully');
    } else {
        sendResponse(false, 'Error: ' . $stmt->error);
    }
}

function deleteDepartment() {
    global $conn;
    
    $dept_id = intval($_POST['dept_id']);
    
    if (softDelete('tblDepartments', 'dept_id', $dept_id)) {
        sendResponse(true, 'Department deleted successfully');
    } else {
        sendResponse(false, 'Error deleting department');
    }
}

function getDepartment() {
    global $conn;
    
    $dept_id = intval($_GET['dept_id']);
    
    $sql = "SELECT * FROM tblDepartments WHERE dept_id = ? AND deleted_at IS NULL";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $dept_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        sendResponse(true, 'Department retrieved successfully', $row);
    } else {
        sendResponse(false, 'Department not found');
    }
}

function restoreDepartment() {
    global $conn;
    
    $dept_id = intval($_POST['dept_id']);
    
    if (restoreDeleted('tblDepartments', 'dept_id', $dept_id)) {
        sendResponse(true, 'Department restored successfully');
    } else {
        sendResponse(false, 'Error restoring department');
    }
}

function readDeletedDepartments() {
    global $conn;
    
    $sql = "SELECT * FROM tblDepartments WHERE deleted_at IS NOT NULL ORDER BY deleted_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $departments = [];
    
    while ($row = $result->fetch_assoc()) {
        $departments[] = $row;
    }
    
    sendResponse(true, 'Deleted departments retrieved successfully', $departments);
}

function permanentDeleteDepartment() {
    global $conn;
    
    $dept_id = intval($_POST['dept_id']);
    
    if (permanentDelete('tblDepartments', 'dept_id', $dept_id)) {
        sendResponse(true, 'Department permanently deleted');
    } else {
        sendResponse(false, 'Error permanently deleting department');
    }
}
?>