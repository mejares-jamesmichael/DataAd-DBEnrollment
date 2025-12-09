<?php
// students.php - CRUD Operations for Students with Sorting
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
    case 'restore':
        restoreStudent();
        break;
    case 'readDeleted':
        readDeletedStudents();
        break;
    default:
        sendResponse(false, 'Invalid action');
}

// ---------------- CRUD FUNCTIONS ---------------- //

function createStudent() {
    global $conn;
    
    $student_no = sanitize($_POST['student_no']);
    $last_name = sanitize($_POST['last_name']);
    $first_name = sanitize($_POST['first_name']);
    $middle_name = sanitize($_POST['middle_name'] ?? '');
    $email = sanitize($_POST['email']);
    $gender = sanitize($_POST['gender']);
    $birthdate = sanitize($_POST['birthdate']);
    $year_level = intval($_POST['year_level']);
    $program_id = intval($_POST['program_id']);

    if (empty($student_no) || empty($last_name) || empty($first_name) || empty($email) || empty($gender) || empty($birthdate) || empty($year_level) || empty($program_id)) {
        sendResponse(false, 'All fields are required');
    }

    $sql = "INSERT INTO tbl_student (student_no, last_name, first_name, middle_name, email, gender, birthdate, year_level, program_id, is_deleted) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssii", $student_no, $last_name, $first_name, $middle_name, $email, $gender, $birthdate, $year_level, $program_id);
    
    if ($stmt->execute()) {
        sendResponse(true, 'Student created successfully', ['id' => $conn->insert_id]);
    } else {
        sendResponse(false, 'Error: ' . $stmt->error);
    }
}

function readStudents() {
    global $conn;

    $search = sanitize($_GET['search'] ?? '');

    // Sorting parameters
    $allowedSortColumns = ['student_id', 'student_no', 'last_name', 'first_name', 'email', 'year_level', 'program_code'];
    $sort = $_GET['sort'] ?? 'student_id';
    $order = strtoupper($_GET['order'] ?? 'ASC');

    if (!in_array($sort, $allowedSortColumns)) {
        $sort = 'student_id';
    }
    if (!in_array($order, ['ASC', 'DESC'])) {
        $order = 'ASC';
    }

    if (!empty($search)) {
        $sql = "SELECT s.*, p.program_code, p.program_name
                FROM tbl_student s
                LEFT JOIN tbl_program p ON s.program_id = p.program_id AND p.is_deleted = 0
                WHERE (s.student_no LIKE ? OR s.first_name LIKE ? OR s.last_name LIKE ? OR s.email LIKE ? OR p.program_code LIKE ?)
                AND s.is_deleted = 0
                ORDER BY $sort $order";
        $stmt = $conn->prepare($sql);
        $searchTerm = "%$search%";
        $stmt->bind_param("sssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    } else {
        $sql = "SELECT s.*, p.program_code, p.program_name
                FROM tbl_student s
                LEFT JOIN tbl_program p ON s.program_id = p.program_id AND p.is_deleted = 0
                WHERE s.is_deleted = 0
                ORDER BY $sort $order";
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
    $middle_name = sanitize($_POST['middle_name'] ?? '');
    $email = sanitize($_POST['email']);
    $gender = sanitize($_POST['gender']);
    $birthdate = sanitize($_POST['birthdate']);
    $year_level = intval($_POST['year_level']);
    $program_id = intval($_POST['program_id']);

    if (empty($student_no) || empty($last_name) || empty($first_name) || empty($email) || empty($gender) || empty($birthdate) || empty($year_level) || empty($program_id)) {
        sendResponse(false, 'All fields are required');
    }

    $sql = "UPDATE tbl_student SET student_no = ?, last_name = ?, first_name = ?, middle_name = ?, email = ?, gender = ?, birthdate = ?, year_level = ?, program_id = ? WHERE student_id = ? AND is_deleted = 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssiii", $student_no, $last_name, $first_name, $middle_name, $email, $gender, $birthdate, $year_level, $program_id, $student_id);
    
    if ($stmt->execute()) {
        sendResponse(true, 'Student updated successfully');
    } else {
        sendResponse(false, 'Error: ' . $stmt->error);
    }
}

function deleteStudent() {
    global $conn;
    
    $student_id = intval($_POST['student_id']);
    
    if (softDelete('tbl_student', 'student_id', $student_id)) {
        sendResponse(true, 'Student deleted successfully');
    } else {
        sendResponse(false, 'Error deleting student');
    }
}

function getStudent() {
    global $conn;
    
    $student_id = intval($_GET['student_id']);
    
    $sql = "SELECT s.*, p.program_code, p.program_name
            FROM tbl_student s
            LEFT JOIN tbl_program p ON s.program_id = p.program_id AND p.is_deleted = 0
            WHERE s.student_id = ? AND s.is_deleted = 0";
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

function restoreStudent() {
    global $conn;
    
    $student_id = intval($_POST['student_id']);
    
    if (restoreDeleted('tbl_student', 'student_id', $student_id)) {
        sendResponse(true, 'Student restored successfully');
    } else {
        sendResponse(false, 'Error restoring student');
    }
}

function readDeletedStudents() {
    global $conn;

    // Sorting parameters
    $allowedSortColumns = ['student_id', 'student_no', 'last_name', 'first_name', 'email', 'year_level', 'program_code'];
    $sort = $_GET['sort'] ?? 'student_id';
    $order = strtoupper($_GET['order'] ?? 'DESC');

    if (!in_array($sort, $allowedSortColumns)) {
        $sort = 'student_id';
    }
    if (!in_array($order, ['ASC', 'DESC'])) {
        $order = 'DESC';
    }

    $sql = "SELECT s.*, p.program_code, p.program_name
            FROM tbl_student s
            LEFT JOIN tbl_program p ON s.program_id = p.program_id
            WHERE s.is_deleted = 1
            ORDER BY $sort $order";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $students = [];
    
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
    
    sendResponse(true, 'Deleted students retrieved successfully', $students);
}
?>
