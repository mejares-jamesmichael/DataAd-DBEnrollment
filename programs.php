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
    
    $sql = "INSERT INTO tbl_program (program_code, program_name, dept_id, is_deleted) VALUES (?, ?, ?, 0)";
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
    $order = strtoupper(sanitize($_GET['order'] ?? 'DESC'));
    $order = ($order === 'ASC') ? 'ASC' : 'DESC';
    
    if (!empty($search)) {
        $sql = "SELECT p.*, d.dept_name
                FROM tbl_program p
                LEFT JOIN tbl_department d ON p.dept_id = d.dept_id AND d.is_deleted = 0
                WHERE (p.program_code LIKE ? OR p.program_name LIKE ? OR d.dept_name LIKE ?)
                AND p.is_deleted = 0
                ORDER BY p.program_id $order";
        $stmt = $conn->prepare($sql);
        $searchTerm = "%$search%";
        $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
    } else {
        $sql = "SELECT p.*, d.dept_name
                FROM tbl_program p
                LEFT JOIN tbl_department d ON p.dept_id = d.dept_id AND d.is_deleted = 0
                WHERE p.is_deleted = 0
                ORDER BY p.program_id $order";
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
    
    $sql = "UPDATE tbl_program SET program_code = ?, program_name = ?, dept_id = ? WHERE program_id = ? AND is_deleted = 0";
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
    
    if (softDelete('tbl_program', 'program_id', $program_id)) {
        sendResponse(true, 'Program deleted successfully');
    } else {
        sendResponse(false, 'Error deleting program');
    }
}

function getProgram() {
    global $conn;
    
    $program_id = intval($_GET['program_id']);
    
    $sql = "SELECT p.*, d.dept_name
            FROM tbl_program p
            LEFT JOIN tbl_department d ON p.dept_id = d.dept_id
            WHERE p.program_id = ? AND p.is_deleted = 0";
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
    
    if (restoreDeleted('tbl_program', 'program_id', $program_id)) {
        sendResponse(true, 'Program restored successfully');
    } else {
        sendResponse(false, 'Error restoring program');
    }
}

function readDeletedPrograms() {
    global $conn;
    
    $sql = "SELECT p.*, d.dept_name
            FROM tbl_program p
            LEFT JOIN tbl_department d ON p.dept_id = d.dept_id
            WHERE p.is_deleted = 1
            ORDER BY p.program_id DESC";
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