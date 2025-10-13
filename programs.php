<?php
// programs.php - CRUD Operations for Programs
require_once 'config.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'create':
        createProgram();
        break;
    case 'read':
        readPrograms();
        break;
    case 'update':
        updateProgram();
        break;
    case 'delete':
        deleteProgram();
        break;
    case 'getOne':
        getProgram();
        break;
    case 'restore':
        restoreProgram();
        break;
    case 'readDeleted':
        readDeletedPrograms();
        break;
    default:
        sendResponse(false, 'Invalid action');
}

function createProgram() {
    global $conn;
    
    $program_code = sanitize($_POST['program_code']);
    $program_name = sanitize($_POST['program_name']);
    $dept_id = intval($_POST['dept_id']);
    
    if (empty($program_code) || empty($program_name) || empty($dept_id)) {
        sendResponse(false, 'All fields are required');
    }
    
    $sql = "INSERT INTO tblPrograms (program_code, program_name, dept_id) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $program_code, $program_name, $dept_id);
    
    if ($stmt->execute()) {
        sendResponse(true, 'Program created successfully', ['id' => $conn->insert_id]);
    } else {
        sendResponse(false, 'Error: ' . $stmt->error);
    }
}

function readPrograms() {
    global $conn;
    
    $search = sanitize($_GET['search'] ?? '');
    
    if (!empty($search)) {
        $sql = "SELECT p.*, d.dept_name 
                FROM tblPrograms p
                LEFT JOIN tblDepartments d ON p.dept_id = d.dept_id AND d.deleted_at IS NULL
                WHERE (p.program_code LIKE ? OR p.program_name LIKE ? OR d.dept_name LIKE ?)
                AND p.deleted_at IS NULL
                ORDER BY p.program_id";
        $stmt = $conn->prepare($sql);
        $searchTerm = "%$search%";
        $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
    } else {
        $sql = "SELECT p.*, d.dept_name 
                FROM tblPrograms p
                LEFT JOIN tblDepartments d ON p.dept_id = d.dept_id AND d.deleted_at IS NULL
                WHERE p.deleted_at IS NULL
                ORDER BY p.program_id";
        $stmt = $conn->prepare($sql);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $programs = [];
    
    while ($row = $result->fetch_assoc()) {
        $programs[] = $row;
    }
    
    sendResponse(true, 'Programs retrieved successfully', $programs);
}

function updateProgram() {
    global $conn;
    
    $program_id = intval($_POST['program_id']);
    $program_code = sanitize($_POST['program_code']);
    $program_name = sanitize($_POST['program_name']);
    $dept_id = intval($_POST['dept_id']);
    
    if (empty($program_code) || empty($program_name) || empty($dept_id)) {
        sendResponse(false, 'All fields are required');
    }
    
    $sql = "UPDATE tblPrograms SET program_code = ?, program_name = ?, dept_id = ? WHERE program_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $program_code, $program_name, $dept_id, $program_id);
    
    if ($stmt->execute()) {
        sendResponse(true, 'Program updated successfully');
    } else {
        sendResponse(false, 'Error: ' . $stmt->error);
    }
}

function deleteProgram() {
    global $conn;
    
    $program_id = intval($_POST['program_id']);
    
    if (softDelete('tblPrograms', 'program_id', $program_id)) {
        sendResponse(true, 'Program deleted successfully');
    } else {
        sendResponse(false, 'Error deleting program');
    }
}

function getProgram() {
    global $conn;
    
    $program_id = intval($_GET['program_id']);
    
    $sql = "SELECT p.*, d.dept_name 
            FROM tblPrograms p
            LEFT JOIN tblDepartments d ON p.dept_id = d.dept_id
            WHERE p.program_id = ? AND p.deleted_at IS NULL";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $program_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        sendResponse(true, 'Program retrieved successfully', $row);
    } else {
        sendResponse(false, 'Program not found');
    }
}

function restoreProgram() {
    global $conn;
    
    $program_id = intval($_POST['program_id']);
    
    if (restoreDeleted('tblPrograms', 'program_id', $program_id)) {
        sendResponse(true, 'Program restored successfully');
    } else {
        sendResponse(false, 'Error restoring program');
    }
}

function readDeletedPrograms() {
    global $conn;
    
    $sql = "SELECT p.*, d.dept_name 
            FROM tblPrograms p
            LEFT JOIN tblDepartments d ON p.dept_id = d.dept_id
            WHERE p.deleted_at IS NOT NULL 
            ORDER BY p.deleted_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $programs = [];
    
    while ($row = $result->fetch_assoc()) {
        $programs[] = $row;
    }
    
    sendResponse(true, 'Deleted programs retrieved successfully', $programs);
}
?>