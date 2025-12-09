<?php
// terms.php - CRUD Operations for Terms
require_once 'config.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'create':
        createTerm();
        break;
    case 'read':
        readTerms();
        break;
    case 'update':
        updateTerm();
        break;
    case 'delete':
        deleteTerm();
        break;
    case 'getOne':
        getTerm();
        break;
    case 'restore':
        restoreTerm();
        break;
    case 'readDeleted':
        readDeletedTerms();
        break;
    default:
        sendResponse(false, 'Invalid action');
}

function createTerm() {
    global $conn;
    
    $term_code = sanitize($_POST['term_code']);
    $start_date = sanitize($_POST['start_date']);
    $end_date = sanitize($_POST['end_date']);
    
    if (empty($term_code) || empty($start_date) || empty($end_date)) {
        sendResponse(false, 'All fields are required');
    }
    
    $sql = "INSERT INTO tbl_term (term_code, start_date, end_date, is_deleted) VALUES (?, ?, ?, 0)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $term_code, $start_date, $end_date);
    
    if ($stmt->execute()) {
        sendResponse(true, 'Term created successfully', ['id' => $conn->insert_id]);
    } else {
        sendResponse(false, 'Error: ' . $stmt->error);
    }
}

function readTerms() {
    global $conn;
    
    $search = sanitize($_GET['search'] ?? '');
    $order = strtoupper(sanitize($_GET['order'] ?? 'DESC'));
    $order = ($order === 'ASC') ? 'ASC' : 'DESC';
    
    if (!empty($search)) {
        $sql = "SELECT * FROM tbl_term
                WHERE term_code LIKE ? AND is_deleted = 0
                ORDER BY term_id $order";
        $stmt = $conn->prepare($sql);
        $searchTerm = "%$search%";
        $stmt->bind_param("s", $searchTerm);
    } else {
        $sql = "SELECT * FROM tbl_term WHERE is_deleted = 0 ORDER BY term_id $order";
        $stmt = $conn->prepare($sql);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $terms = [];
    
    while ($row = $result->fetch_assoc()) {
        $terms[] = $row;
    }
    
    sendResponse(true, 'Terms retrieved successfully', $terms);
}

function updateTerm() {
    global $conn;
    
    $term_id = intval($_POST['term_id']);
    $term_code = sanitize($_POST['term_code']);
    $start_date = sanitize($_POST['start_date']);
    $end_date = sanitize($_POST['end_date']);
    
    if (empty($term_code) || empty($start_date) || empty($end_date)) {
        sendResponse(false, 'All fields are required');
    }
    
    $sql = "UPDATE tbl_term SET term_code = ?, start_date = ?, end_date = ? WHERE term_id = ? AND is_deleted = 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $term_code, $start_date, $end_date, $term_id);
    
    if ($stmt->execute()) {
        sendResponse(true, 'Term updated successfully');
    } else {
        sendResponse(false, 'Error: ' . $stmt->error);
    }
}

function deleteTerm() {
    global $conn;
    
    $term_id = intval($_POST['term_id']);
    
    if (softDelete('tbl_term', 'term_id', $term_id)) {
        sendResponse(true, 'Term deleted successfully');
    } else {
        sendResponse(false, 'Error deleting term');
    }
}

function getTerm() {
    global $conn;
    
    $term_id = intval($_GET['term_id']);
    
    $sql = "SELECT * FROM tbl_term WHERE term_id = ? AND is_deleted = 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $term_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        sendResponse(true, 'Term retrieved successfully', $row);
    } else {
        sendResponse(false, 'Term not found');
    }
}

function restoreTerm() {
    global $conn;
    
    $term_id = intval($_POST['term_id']);
    
    if (restoreDeleted('tbl_term', 'term_id', $term_id)) {
        sendResponse(true, 'Term restored successfully');
    } else {
        sendResponse(false, 'Error restoring term');
    }
}

function readDeletedTerms() {
    global $conn;
    
    $sql = "SELECT * FROM tbl_term WHERE is_deleted = 1 ORDER BY term_id DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $terms = [];
    
    while ($row = $result->fetch_assoc()) {
        $terms[] = $row;
    }
    
    sendResponse(true, 'Deleted terms retrieved successfully', $terms);
}
?>