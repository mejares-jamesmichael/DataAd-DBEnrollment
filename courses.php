<?php
// courses.php - CRUD Operations for Courses
require_once 'config.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'create':
        createCourse();
        break;
    case 'read':
        readCourses();
        break;
    case 'update':
        updateCourse();
        break;
    case 'delete':
        deleteCourse();
        break;
    case 'getOne':
        getCourse();
        break;
    default:
        sendResponse(false, 'Invalid action');
}

function createCourse() {
    global $conn;
    
    $course_code = sanitize($_POST['course_code']);
    $course_title = sanitize($_POST['course_title']);
    $units = intval($_POST['units']);
    $lecture_hours = floatval($_POST['lecture_hours']);
    $lab_hours = floatval($_POST['lab_hours']);
    $dept_id = intval($_POST['dept_id']);
    
    if (empty($course_code) || empty($course_title) || empty($units) || empty($dept_id)) {
        sendResponse(false, 'Required fields are missing');
    }
    
    $sql = "INSERT INTO tblCourses (course_code, course_title, units, lecture_hours, lab_hours, dept_id) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiddi", $course_code, $course_title, $units, $lecture_hours, $lab_hours, $dept_id);
    
    if ($stmt->execute()) {
        sendResponse(true, 'Course created successfully', ['id' => $conn->insert_id]);
    } else {
        sendResponse(false, 'Error: ' . $stmt->error);
    }
}

function readCourses() {
    global $conn;
    
    $search = sanitize($_GET['search'] ?? '');
    
    if (!empty($search)) {
        $sql = "SELECT c.*, d.dept_name, d.dept_code
                FROM tblCourses c
                LEFT JOIN tblDepartments d ON c.dept_id = d.dept_id
                WHERE c.course_code LIKE ? OR c.course_title LIKE ? OR d.dept_name LIKE ?
                ORDER BY c.course_id";
        $stmt = $conn->prepare($sql);
        $searchTerm = "%$search%";
        $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
    } else {
        $sql = "SELECT c.*, d.dept_name, d.dept_code
                FROM tblCourses c
                LEFT JOIN tblDepartments d ON c.dept_id = d.dept_id
                ORDER BY c.course_id";
        $stmt = $conn->prepare($sql);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $courses = [];
    
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }
    
    sendResponse(true, 'Courses retrieved successfully', $courses);
}

function updateCourse() {
    global $conn;
    
    $course_id = intval($_POST['course_id']);
    $course_code = sanitize($_POST['course_code']);
    $course_title = sanitize($_POST['course_title']);
    $units = intval($_POST['units']);
    $lecture_hours = floatval($_POST['lecture_hours']);
    $lab_hours = floatval($_POST['lab_hours']);
    $dept_id = intval($_POST['dept_id']);
    
    if (empty($course_code) || empty($course_title) || empty($units) || empty($dept_id)) {
        sendResponse(false, 'Required fields are missing');
    }
    
    $sql = "UPDATE tblCourses SET course_code = ?, course_title = ?, units = ?, lecture_hours = ?, lab_hours = ?, dept_id = ? 
            WHERE course_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiddii", $course_code, $course_title, $units, $lecture_hours, $lab_hours, $dept_id, $course_id);
    
    if ($stmt->execute()) {
        sendResponse(true, 'Course updated successfully');
    } else {
        sendResponse(false, 'Error: ' . $stmt->error);
    }
}

function deleteCourse() {
    global $conn;
    
    $course_id = intval($_POST['course_id']);
    
    $sql = "DELETE FROM tblCourses WHERE course_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $course_id);
    
    if ($stmt->execute()) {
        sendResponse(true, 'Course deleted successfully');
    } else {
        sendResponse(false, 'Error: ' . $stmt->error);
    }
}

function getCourse() {
    global $conn;
    
    $course_id = intval($_GET['course_id']);
    
    $sql = "SELECT c.*, d.dept_name, d.dept_code
            FROM tblCourses c
            LEFT JOIN tblDepartments d ON c.dept_id = d.dept_id
            WHERE c.course_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        sendResponse(true, 'Course retrieved successfully', $row);
    } else {
        sendResponse(false, 'Course not found');
    }
}
?>