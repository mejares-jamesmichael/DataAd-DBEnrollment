<?php
// enrollments.php - CRUD Operations for Enrollments
require_once 'config.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'create':
        createEnrollment();
        break;
    case 'read':
        readEnrollments();
        break;
    case 'update':
        updateEnrollment();
        break;
    case 'delete':
        deleteEnrollment();
        break;
    case 'getOne':
        getEnrollment();
        break;
    case 'restore':
        restoreEnrollment();
        break;
    case 'readDeleted':
        readDeletedEnrollments();
        break;
    default:
        sendResponse(false, 'Invalid action');
}

function createEnrollment() {
    global $conn;
    
    $student_id = intval($_POST['student_id']);
    $section_id = intval($_POST['section_id']);
    $date_enrolled = sanitize($_POST['date_enrolled']);
    $status = sanitize($_POST['status']);
    $letter_grade = sanitize($_POST['letter_grade'] ?? '');
    
    if (empty($student_id) || empty($section_id) || empty($date_enrolled) || empty($status)) {
        sendResponse(false, 'Required fields are missing');
    }
    
    $sql = "INSERT INTO tblEnrollments (student_id, section_id, date_enrolled, status, letter_grade) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisss", $student_id, $section_id, $date_enrolled, $status, $letter_grade);
    
    if ($stmt->execute()) {
        sendResponse(true, 'Enrollment created successfully', ['id' => $conn->insert_id]);
    } else {
        sendResponse(false, 'Error: ' . $stmt->error);
    }
}

function readEnrollments() {
    global $conn;
    
    $search = sanitize($_GET['search'] ?? '');
    
    if (!empty($search)) {
        $sql = "SELECT e.*, 
                       CONCAT(st.first_name, ' ', st.last_name) as student_name,
                       st.student_no,
                       c.course_code, c.course_title,
                       sec.section_code,
                       t.term_code
                FROM tblEnrollments e
                LEFT JOIN tblStudents st ON e.student_id = st.student_id AND st.deleted_at IS NULL
                LEFT JOIN tblSections sec ON e.section_id = sec.section_id AND sec.deleted_at IS NULL
                LEFT JOIN tblCourses c ON sec.course_id = c.course_id AND c.deleted_at IS NULL
                LEFT JOIN tblTerms t ON sec.term_id = t.term_id AND t.deleted_at IS NULL
                WHERE (st.student_no LIKE ? OR st.first_name LIKE ? OR st.last_name LIKE ? OR c.course_code LIKE ?)
                AND e.deleted_at IS NULL
                ORDER BY e.enrollment_id DESC";
        $stmt = $conn->prepare($sql);
        $searchTerm = "%$search%";
        $stmt->bind_param("ssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    } else {
        $sql = "SELECT e.*, 
                       CONCAT(st.first_name, ' ', st.last_name) as student_name,
                       st.student_no,
                       c.course_code, c.course_title,
                       sec.section_code,
                       t.term_code
                FROM tblEnrollments e
                LEFT JOIN tblStudents st ON e.student_id = st.student_id AND st.deleted_at IS NULL
                LEFT JOIN tblSections sec ON e.section_id = sec.section_id AND sec.deleted_at IS NULL
                LEFT JOIN tblCourses c ON sec.course_id = c.course_id AND c.deleted_at IS NULL
                LEFT JOIN tblTerms t ON sec.term_id = t.term_id AND t.deleted_at IS NULL
                WHERE e.deleted_at IS NULL
                ORDER BY e.enrollment_id DESC";
        $stmt = $conn->prepare($sql);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $enrollments = [];
    
    while ($row = $result->fetch_assoc()) {
        $enrollments[] = $row;
    }
    
    sendResponse(true, 'Enrollments retrieved successfully', $enrollments);
}

function updateEnrollment() {
    global $conn;
    
    $enrollment_id = intval($_POST['enrollment_id']);
    $student_id = intval($_POST['student_id']);
    $section_id = intval($_POST['section_id']);
    $date_enrolled = sanitize($_POST['date_enrolled']);
    $status = sanitize($_POST['status']);
    $letter_grade = sanitize($_POST['letter_grade'] ?? '');
    
    if (empty($student_id) || empty($section_id) || empty($date_enrolled) || empty($status)) {
        sendResponse(false, 'Required fields are missing');
    }
    
    $sql = "UPDATE tblEnrollments SET student_id = ?, section_id = ?, date_enrolled = ?, status = ?, letter_grade = ? 
            WHERE enrollment_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisssi", $student_id, $section_id, $date_enrolled, $status, $letter_grade, $enrollment_id);
    
    if ($stmt->execute()) {
        sendResponse(true, 'Enrollment updated successfully');
    } else {
        sendResponse(false, 'Error: ' . $stmt->error);
    }
}

function deleteEnrollment() {
    global $conn;
    
    $enrollment_id = intval($_POST['enrollment_id']);
    
    if (softDelete('tblEnrollments', 'enrollment_id', $enrollment_id)) {
        sendResponse(true, 'Enrollment deleted successfully');
    } else {
        sendResponse(false, 'Error deleting enrollment');
    }
}

function getEnrollment() {
    global $conn;
    
    $enrollment_id = intval($_GET['enrollment_id']);
    
    $sql = "SELECT e.*, 
                   CONCAT(st.first_name, ' ', st.last_name) as student_name,
                   st.student_no,
                   c.course_code, c.course_title,
                   sec.section_code,
                   t.term_code
            FROM tblEnrollments e
            LEFT JOIN tblStudents st ON e.student_id = st.student_id
            LEFT JOIN tblSections sec ON e.section_id = sec.section_id
            LEFT JOIN tblCourses c ON sec.course_id = c.course_id
            LEFT JOIN tblTerms t ON sec.term_id = t.term_id
            WHERE e.enrollment_id = ? AND e.deleted_at IS NULL";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $enrollment_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        sendResponse(true, 'Enrollment retrieved successfully', $row);
    } else {
        sendResponse(false, 'Enrollment not found');
    }
}

function restoreEnrollment() {
    global $conn;
    
    $enrollment_id = intval($_POST['enrollment_id']);
    
    if (restoreDeleted('tblEnrollments', 'enrollment_id', $enrollment_id)) {
        sendResponse(true, 'Enrollment restored successfully');
    } else {
        sendResponse(false, 'Error restoring enrollment');
    }
}

function readDeletedEnrollments() {
    global $conn;
    
    $sql = "SELECT e.*, 
                   CONCAT(st.first_name, ' ', st.last_name) as student_name,
                   st.student_no,
                   c.course_code, c.course_title,
                   sec.section_code,
                   t.term_code
            FROM tblEnrollments e
            LEFT JOIN tblStudents st ON e.student_id = st.student_id
            LEFT JOIN tblSections sec ON e.section_id = sec.section_id
            LEFT JOIN tblCourses c ON sec.course_id = c.course_id
            LEFT JOIN tblTerms t ON sec.term_id = t.term_id
            WHERE e.deleted_at IS NOT NULL 
            ORDER BY e.deleted_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $enrollments = [];
    
    while ($row = $result->fetch_assoc()) {
        $enrollments[] = $row;
    }
    
    sendResponse(true, 'Deleted enrollments retrieved successfully', $enrollments);
}
?>