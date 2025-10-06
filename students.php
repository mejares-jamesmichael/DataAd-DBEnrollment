<?php
// students.php - CRUD Operations for Students
require_once 'config.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'create':
        createStudent();
        break;
    case 'read':
        readStudents();
        break;
    case 'update':
        updateStudent();
        break;
    case 'delete':
        deleteStudent();
        break;
    case 'getOne':
        getStudent();
        break;
    default:
        sendResponse(false, 'Invalid action');
}

function createStudent() {
    global $conn;
    
    $student_no = sanitize($_POST['student_no']);
    $last_name = sanitize($_POST['last_name']);
    $first_name = sanitize($_POST['first_name']);
    $email = sanitize($_POST['email']);
    $gender = sanitize($_POST['gender']);
    $birthdate = sanitize($_POST['birthdate']);
    $year_level = intval($_POST['year_level']);
    $program_id = intval($_POST['program_id']);
    
    if (empty($student_no) || empty($last_name) || empty($first_name) || empty($email) || empty($gender) || empty($birthdate) || empty($year_level) || empty($program_id)) {
        sendResponse(false, 'All fields are required');
    }
    
    $sql = "INSERT INTO tblStudents (student_no, last_name, first_name, email, gender, birthdate, year_level, program_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssii", $student_no, $last_name, $first_name, $email, $gender, $birthdate, $year_level, $program_id);
    
    if ($stmt->execute()) {
        sendResponse(true, 'Student created successfully', ['id' => $conn->insert_id]);
    } else {
        sendResponse(false, 'Error: ' . $stmt->error);
    }
}

function readStudents() {
    global $conn;
    
    $search = sanitize($_GET['search'] ?? '');
    
    if (!empty($search)) {
        $sql = "SELECT s.*, p.program_code, p.program_name
                FROM tblStudents s
                LEFT JOIN tblPrograms p ON s.program_id = p.program_id
                WHERE s.student_no LIKE ? OR s.first_name LIKE ? OR s.last_name LIKE ? OR s.email LIKE ? OR p.program_code LIKE ?
                ORDER BY s.student_id";
        $stmt = $conn->prepare($sql);
        $searchTerm = "%$search%";
        $stmt->bind_param("sssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    } else {
        $sql = "SELECT s.*, p.program_code, p.program_name
                FROM tblStudents s
                LEFT JOIN tblPrograms p ON s.program_id = p.program_id
                ORDER BY s.student_id";
        $stmt = $conn->prepare($sql);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $students = [];
    
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
    
    sendResponse(true, 'Students retrieved successfully', $students);
}

function updateStudent() {
    global $conn;
    
    $student_id = intval($_POST['student_id']);
    $student_no = sanitize($_POST['student_no']);
    $last_name = sanitize($_POST['last_name']);
    $first_name = sanitize($_POST['first_name']);
    $email = sanitize($_POST['email']);
    $gender = sanitize($_POST['gender']);
    $birthdate = sanitize($_POST['birthdate']);
    $year_level = intval($_POST['year_level']);
    $program_id = intval($_POST['program_id']);
    
    if (empty($student_no) || empty($last_name) || empty($first_name) || empty($email) || empty($gender) || empty($birthdate) || empty($year_level) || empty($program_id)) {
        sendResponse(false, 'All fields are required');
    }
    
    $sql = "UPDATE tblStudents SET student_no = ?, last_name = ?, first_name = ?, email = ?, gender = ?, birthdate = ?, year_level = ?, program_id = ? WHERE student_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssiii", $student_no, $last_name, $first_name, $email, $gender, $birthdate, $year_level, $program_id, $student_id);
    
    if ($stmt->execute()) {
        sendResponse(true, 'Student updated successfully');
    } else {
        sendResponse(false, 'Error: ' . $stmt->error);
    }
}

function deleteStudent() {
    global $conn;
    
    $student_id = intval($_POST['student_id']);
    
    $sql = "DELETE FROM tblStudents WHERE student_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $student_id);
    
    if ($stmt->execute()) {
        sendResponse(true, 'Student deleted successfully');
    } else {
        sendResponse(false, 'Error: ' . $stmt->error);
    }
}

function getStudent() {
    global $conn;
    
    $student_id = intval($_GET['student_id']);
    
    $sql = "SELECT s.*, p.program_code, p.program_name
            FROM tblStudents s
            LEFT JOIN tblPrograms p ON s.program_id = p.program_id
            WHERE s.student_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        sendResponse(true, 'Student retrieved successfully', $row);
    } else {
        sendResponse(false, 'Student not found');
    }
}
?>