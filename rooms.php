<?php
// rooms.php - CRUD Operations for Rooms
require_once 'config.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'create':
        createRoom();
        break;
    case 'read':
        readRooms();
        break;
    case 'update':
        updateRoom();
        break;
    case 'delete':
        deleteRoom();
        break;
    case 'getOne':
        getRoom();
        break;
    case 'restore':
        restoreRoom();
        break;
    case 'readDeleted':
        readDeletedRooms();
        break;
    default:
        sendResponse(false, 'Invalid action');
}

function createRoom() {
    global $conn;
    
    $building = sanitize($_POST['building']);
    $room_code = sanitize($_POST['room_code']);
    $capacity = intval($_POST['capacity']);
    
    if (empty($building) || empty($room_code) || empty($capacity)) {
        sendResponse(false, 'All fields are required');
    }
    
    $sql = "INSERT INTO tbl_room (building, room_code, capacity, is_deleted) VALUES (?, ?, ?, 0)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $building, $room_code, $capacity);
    
    if ($stmt->execute()) {
        sendResponse(true, 'Room created successfully', ['id' => $conn->insert_id]);
    } else {
        sendResponse(false, 'Error: ' . $stmt->error);
    }
}

function readRooms() {
    global $conn;
    
    $search = sanitize($_GET['search'] ?? '');
    $order = strtoupper(sanitize($_GET['order'] ?? 'DESC'));
    $order = ($order === 'ASC') ? 'ASC' : 'DESC';
    
    if (!empty($search)) {
        $sql = "SELECT * FROM tbl_room
                WHERE (building LIKE ? OR room_code LIKE ?)
                AND is_deleted = 0
                ORDER BY room_id $order";
        $stmt = $conn->prepare($sql);
        $searchTerm = "%$search%";
        $stmt->bind_param("ss", $searchTerm, $searchTerm);
    } else {
        $sql = "SELECT * FROM tbl_room WHERE is_deleted = 0 ORDER BY room_id $order";
        $stmt = $conn->prepare($sql);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $rooms = [];
    
    while ($row = $result->fetch_assoc()) {
        $rooms[] = $row;
    }
    
    sendResponse(true, 'Rooms retrieved successfully', $rooms);
}

function updateRoom() {
    global $conn;
    
    $room_id = intval($_POST['room_id']);
    $building = sanitize($_POST['building']);
    $room_code = sanitize($_POST['room_code']);
    $capacity = intval($_POST['capacity']);
    
    if (empty($building) || empty($room_code) || empty($capacity)) {
        sendResponse(false, 'All fields are required');
    }
    
    $sql = "UPDATE tbl_room SET building = ?, room_code = ?, capacity = ? WHERE room_id = ? AND is_deleted = 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $building, $room_code, $capacity, $room_id);
    
    if ($stmt->execute()) {
        sendResponse(true, 'Room updated successfully');
    } else {
        sendResponse(false, 'Error: ' . $stmt->error);
    }
}

function deleteRoom() {
    global $conn;
    
    $room_id = intval($_POST['room_id']);
    
    if (softDelete('tbl_room', 'room_id', $room_id)) {
        sendResponse(true, 'Room deleted successfully');
    } else {
        sendResponse(false, 'Error deleting room');
    }
}

function getRoom() {
    global $conn;
    
    $room_id = intval($_GET['room_id']);
    
    $sql = "SELECT * FROM tbl_room WHERE room_id = ? AND is_deleted = 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        sendResponse(true, 'Room retrieved successfully', $row);
    } else {
        sendResponse(false, 'Room not found');
    }
}

function restoreRoom() {
    global $conn;
    
    $room_id = intval($_POST['room_id']);
    
    if (restoreDeleted('tbl_room', 'room_id', $room_id)) {
        sendResponse(true, 'Room restored successfully');
    } else {
        sendResponse(false, 'Error restoring room');
    }
}

function readDeletedRooms() {
    global $conn;
    
    $sql = "SELECT * FROM tbl_room WHERE is_deleted = 1 ORDER BY room_id DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $rooms = [];
    
    while ($row = $result->fetch_assoc()) {
        $rooms[] = $row;
    }
    
    sendResponse(true, 'Deleted rooms retrieved successfully', $rooms);
}
?>