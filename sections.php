<?php
// sections.php - CRUD Operations for Sections
require_once 'config.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'create':
        createSection();
        break;
    case 'read':
        readSections();
        break;
    case 'update':
        updateSection();
        break;
    case 'delete':
        deleteSection();
        break;
    case 'getOne':
        getSection();
        break;
    case 'restore':
        restoreSection();
        break;
    case 'readDeleted':
        readDeletedSections();
        break;
    default:
        sendResponse(false, 'Invalid action');
}

function createSection() {
    global $conn;
    
    $section_code = sanitize($_POST['section_code']);
    $course_id = intval($_POST['course_id']);
    $term_id = intval($_POST['term_id']);
    $instructor_id = intval($_POST['instructor_id']);
    $day_pattern = sanitize($_POST['day_pattern']);
    $start_time = sanitize($_POST['start_time']);
    $end_time = sanitize($_POST['end_time']);
    $room_id = intval($_POST['room_id']);
    $max_capacity = intval($_POST['max_capacity']);
    
    if (empty($section_code) || empty($course_id) || empty($term_id) || empty($instructor_id) || empty($day_pattern) || empty($start_time) || empty($end_time) || empty($room_id) || empty($max_capacity)) {
        sendResponse(false, 'All fields are required');
    }
    
    $sql = "INSERT INTO tblSections (section_code, course_id, term_id, instructor_id, day_pattern, start_time, end_time, room_id, max_capacity) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siiisssii", $section_code, $course_id, $term_id, $instructor_id, $day_pattern, $start_time, $end_time, $room_id, $max_capacity);
    
    if ($stmt->execute()) {
        sendResponse(true, 'Section created successfully', ['id' => $conn->insert_id]);
    } else {
        sendResponse(false, 'Error: ' . $stmt->error);
    }
}

function readSections() {
    global $conn;
    
    $search = sanitize($_GET['search'] ?? '');
    $order = strtoupper(sanitize($_GET['order'] ?? 'DESC'));
    $order = ($order === 'ASC') ? 'ASC' : 'DESC';
    
    if (!empty($search)) {
        $sql = "SELECT s.*, c.course_code, c.course_title, t.term_code, 
                       CONCAT(i.first_name, ' ', i.last_name) as instructor_name, 
                       r.room_code, r.building
                FROM tblSections s
                LEFT JOIN tblCourses c ON s.course_id = c.course_id AND c.deleted_at IS NULL
                LEFT JOIN tblTerms t ON s.term_id = t.term_id AND t.deleted_at IS NULL
                LEFT JOIN tblInstructors i ON s.instructor_id = i.instructor_id AND i.deleted_at IS NULL
                LEFT JOIN tblRooms r ON s.room_id = r.room_id AND r.deleted_at IS NULL
                WHERE (s.section_code LIKE ? OR c.course_code LIKE ? OR c.course_title LIKE ? OR t.term_code LIKE ?)
                AND s.deleted_at IS NULL
                ORDER BY s.section_id $order";
        $stmt = $conn->prepare($sql);
        $searchTerm = "%$search%";
        $stmt->bind_param("ssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    } else {
        $sql = "SELECT s.*, c.course_code, c.course_title, t.term_code, 
                       CONCAT(i.first_name, ' ', i.last_name) as instructor_name, 
                       r.room_code, r.building
                FROM tblSections s
                LEFT JOIN tblCourses c ON s.course_id = c.course_id AND c.deleted_at IS NULL
                LEFT JOIN tblTerms t ON s.term_id = t.term_id AND t.deleted_at IS NULL
                LEFT JOIN tblInstructors i ON s.instructor_id = i.instructor_id AND i.deleted_at IS NULL
                LEFT JOIN tblRooms r ON s.room_id = r.room_id AND r.deleted_at IS NULL
                WHERE s.deleted_at IS NULL
                ORDER BY s.section_id $order";
        $stmt = $conn->prepare($sql);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $sections = [];
    
    while ($row = $result->fetch_assoc()) {
        $sections[] = $row;
    }
    
    sendResponse(true, 'Sections retrieved successfully', $sections);
}

function updateSection() {
    global $conn;
    
    $section_id = intval($_POST['section_id']);
    $section_code = sanitize($_POST['section_code']);
    $course_id = intval($_POST['course_id']);
    $term_id = intval($_POST['term_id']);
    $instructor_id = intval($_POST['instructor_id']);
    $day_pattern = sanitize($_POST['day_pattern']);
    $start_time = sanitize($_POST['start_time']);
    $end_time = sanitize($_POST['end_time']);
    $room_id = intval($_POST['room_id']);
    $max_capacity = intval($_POST['max_capacity']);
    
    if (empty($section_code) || empty($course_id) || empty($term_id) || empty($instructor_id) || empty($day_pattern) || empty($start_time) || empty($end_time) || empty($room_id) || empty($max_capacity)) {
        sendResponse(false, 'All fields are required');
    }
    
    $sql = "UPDATE tblSections SET section_code = ?, course_id = ?, term_id = ?, instructor_id = ?, day_pattern = ?, start_time = ?, end_time = ?, room_id = ?, max_capacity = ? 
            WHERE section_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siiissssii", $section_code, $course_id, $term_id, $instructor_id, $day_pattern, $start_time, $end_time, $room_id, $max_capacity, $section_id);
    
    if ($stmt->execute()) {
        sendResponse(true, 'Section updated successfully');
    } else {
        sendResponse(false, 'Error: ' . $stmt->error);
    }
}

function deleteSection() {
    global $conn;
    
    $section_id = intval($_POST['section_id']);
    
    if (softDelete('tblSections', 'section_id', $section_id)) {
        sendResponse(true, 'Section deleted successfully');
    } else {
        sendResponse(false, 'Error deleting section');
    }
}

function getSection() {
    global $conn;
    
    $section_id = intval($_GET['section_id']);
    
    $sql = "SELECT s.*, c.course_code, c.course_title, t.term_code, 
                   CONCAT(i.first_name, ' ', i.last_name) as instructor_name, 
                   r.room_code, r.building
            FROM tblSections s
            LEFT JOIN tblCourses c ON s.course_id = c.course_id
            LEFT JOIN tblTerms t ON s.term_id = t.term_id
            LEFT JOIN tblInstructors i ON s.instructor_id = i.instructor_id
            LEFT JOIN tblRooms r ON s.room_id = r.room_id
            WHERE s.section_id = ? AND s.deleted_at IS NULL";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $section_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        sendResponse(true, 'Section retrieved successfully', $row);
    } else {
        sendResponse(false, 'Section not found');
    }
}

function restoreSection() {
    global $conn;
    
    $section_id = intval($_POST['section_id']);
    
    if (restoreDeleted('tblSections', 'section_id', $section_id)) {
        sendResponse(true, 'Section restored successfully');
    } else {
        sendResponse(false, 'Error restoring section');
    }
}

function readDeletedSections() {
    global $conn;
    
    $sql = "SELECT s.*, c.course_code, c.course_title, t.term_code, 
                   CONCAT(i.first_name, ' ', i.last_name) as instructor_name, 
                   r.room_code, r.building
            FROM tblSections s
            LEFT JOIN tblCourses c ON s.course_id = c.course_id
            LEFT JOIN tblTerms t ON s.term_id = t.term_id
            LEFT JOIN tblInstructors i ON s.instructor_id = i.instructor_id
            LEFT JOIN tblRooms r ON s.room_id = r.room_id
            WHERE s.deleted_at IS NOT NULL 
            ORDER BY s.deleted_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $sections = [];
    
    while ($row = $result->fetch_assoc()) {
        $sections[] = $row;
    }
    
    sendResponse(true, 'Deleted sections retrieved successfully', $sections);
}
?>