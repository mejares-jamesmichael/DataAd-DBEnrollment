<?php
// enrollments.php - CRUD Operations for Enrollments with Prerequisite Validation
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
    case 'checkPrerequisites':
        checkPrerequisites();
        break;
    case 'getPrerequisites':
        getPrerequisites();
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
        return;
    }
    
    // **PREREQUISITE VALIDATION**
    // Get course_id from section
    $course_sql = "SELECT course_id FROM tbl_section WHERE section_id = ? AND is_deleted = 0";
    $course_stmt = $conn->prepare($course_sql);
    $course_stmt->bind_param("i", $section_id);
    $course_stmt->execute();
    $course_result = $course_stmt->get_result();
    
    if ($course_result->num_rows === 0) {
        sendResponse(false, 'Section not found');
        return;
    }
    
    $course_row = $course_result->fetch_assoc();
    $course_id = $course_row['course_id'];
    
    // Check if course has prerequisites
    $prereq_check_sql = "SELECT 
                            cp.prereq_course_id,
                            pc.course_code,
                            pc.course_title
                         FROM tbl_course_prerequisite cp
                         INNER JOIN tbl_course pc ON cp.prereq_course_id = pc.course_id
                         WHERE cp.course_id = ? AND cp.is_deleted = 0 AND pc.is_deleted = 0";
    
    $prereq_stmt = $conn->prepare($prereq_check_sql);
    $prereq_stmt->bind_param("i", $course_id);
    $prereq_stmt->execute();
    $prereq_result = $prereq_stmt->get_result();
    
    $missing_prerequisites = [];
    
    while ($prereq = $prereq_result->fetch_assoc()) {
        // Check if student has passed this prerequisite
        $passed_sql = "SELECT e.letter_grade
                       FROM tbl_enrollment e
                       INNER JOIN tbl_section s ON e.section_id = s.section_id
                       WHERE e.student_id = ? 
                           AND s.course_id = ?
                           AND e.is_deleted = 0
                           AND s.is_deleted = 0
                           AND e.letter_grade IN ('P', 'A', 'B', 'C', '1.0', '1.25', '1.5', '1.75', '2.0', '2.25', '2.5', '2.75', '3.0')";
        
        $passed_stmt = $conn->prepare($passed_sql);
        $passed_stmt->bind_param("ii", $student_id, $prereq['prereq_course_id']);
        $passed_stmt->execute();
        $passed_result = $passed_stmt->get_result();
        
        if ($passed_result->num_rows === 0) {
            // Student has not passed this prerequisite
            $missing_prerequisites[] = "{$prereq['course_code']} - {$prereq['course_title']}";
        }
    }
    
    // If there are missing prerequisites, block enrollment
    if (!empty($missing_prerequisites)) {
        $message = "Cannot enroll: Student has not passed the following prerequisite course(s):\n" . 
                   implode("\n", $missing_prerequisites);
        sendResponse(false, $message, ['missing_prerequisites' => $missing_prerequisites]);
        return;
    }
    
    // Check for duplicate enrollment (same student in same section)
    $duplicate_check = "SELECT enrollment_id FROM tbl_enrollment 
                        WHERE student_id = ? AND section_id = ? AND is_deleted = 0";
    $dup_stmt = $conn->prepare($duplicate_check);
    $dup_stmt->bind_param("ii", $student_id, $section_id);
    $dup_stmt->execute();
    $dup_result = $dup_stmt->get_result();
    
    if ($dup_result->num_rows > 0) {
        sendResponse(false, 'Student is already enrolled in this section');
        return;
    }
    
    // All prerequisites satisfied, proceed with enrollment
    $sql = "INSERT INTO tbl_enrollment (student_id, section_id, date_enrolled, status, letter_grade, is_deleted)
            VALUES (?, ?, ?, ?, ?, 0)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisss", $student_id, $section_id, $date_enrolled, $status, $letter_grade);
    
    if ($stmt->execute()) {
        sendResponse(true, 'Enrollment created successfully', ['id' => $conn->insert_id]);
    } else {
        sendResponse(false, 'Error: ' . $stmt->error);
    }
}

/**
 * Check prerequisites for a specific student and section
 */
function checkPrerequisites() {
    global $conn;
    
    $student_id = intval($_GET['student_id'] ?? $_POST['student_id']);
    $section_id = intval($_GET['section_id'] ?? $_POST['section_id']);
    
    if (empty($student_id) || empty($section_id)) {
        sendResponse(false, 'Student ID and Section ID are required');
        return;
    }
    
    // Get course_id from section
    $course_sql = "SELECT s.course_id, c.course_code, c.course_title 
                   FROM tbl_section s
                   INNER JOIN tbl_course c ON s.course_id = c.course_id
                   WHERE s.section_id = ? AND s.is_deleted = 0 AND c.is_deleted = 0";
    $course_stmt = $conn->prepare($course_sql);
    $course_stmt->bind_param("i", $section_id);
    $course_stmt->execute();
    $course_result = $course_stmt->get_result();
    
    if ($course_result->num_rows === 0) {
        sendResponse(false, 'Section not found');
        return;
    }
    
    $course = $course_result->fetch_assoc();
    $course_id = $course['course_id'];
    
    // Get all prerequisites for this course
    $prereq_sql = "SELECT 
                      cp.prereq_course_id,
                      pc.course_code,
                      pc.course_title
                   FROM tbl_course_prerequisite cp
                   INNER JOIN tbl_course pc ON cp.prereq_course_id = pc.course_id
                   WHERE cp.course_id = ? AND cp.is_deleted = 0 AND pc.is_deleted = 0";
    
    $prereq_stmt = $conn->prepare($prereq_sql);
    $prereq_stmt->bind_param("i", $course_id);
    $prereq_stmt->execute();
    $prereq_result = $prereq_stmt->get_result();
    
    $prerequisites = [];
    
    while ($prereq = $prereq_result->fetch_assoc()) {
        // Check if student has passed this prerequisite
        $passed_sql = "SELECT e.letter_grade, e.status
                       FROM tbl_enrollment e
                       INNER JOIN tbl_section s ON e.section_id = s.section_id
                       WHERE e.student_id = ? 
                           AND s.course_id = ?
                           AND e.is_deleted = 0
                           AND s.is_deleted = 0
                       ORDER BY e.enrollment_id DESC
                       LIMIT 1";
        
        $passed_stmt = $conn->prepare($passed_sql);
        $passed_stmt->bind_param("ii", $student_id, $prereq['prereq_course_id']);
        $passed_stmt->execute();
        $passed_result = $passed_stmt->get_result();
        
        $is_satisfied = false;
        $grade = null;
        
        if ($passed_result->num_rows > 0) {
            $enrollment = $passed_result->fetch_assoc();
            $grade = $enrollment['letter_grade'];
            
            // Check if grade is passing
            if (in_array($grade, ['P', 'A', 'B', 'C', '1.0', '1.25', '1.5', '1.75', '2.0', '2.25', '2.5', '2.75', '3.0'])) {
                $is_satisfied = true;
            }
        }
        
        $prerequisites[] = [
            'course_id' => $prereq['prereq_course_id'],
            'course_code' => $prereq['course_code'],
            'course_title' => $prereq['course_title'],
            'is_satisfied' => $is_satisfied,
            'grade' => $grade
        ];
    }
    
    $all_satisfied = empty($prerequisites) || !in_array(false, array_column($prerequisites, 'is_satisfied'));
    
    sendResponse(true, 'Prerequisites checked', [
        'course' => $course,
        'prerequisites' => $prerequisites,
        'all_satisfied' => $all_satisfied,
        'can_enroll' => $all_satisfied
    ]);
}

/**
 * Get prerequisites for a course (via section)
 */
function getPrerequisites() {
    global $conn;
    
    $section_id = intval($_GET['section_id'] ?? $_POST['section_id']);
    
    if (empty($section_id)) {
        sendResponse(false, 'Section ID is required');
        return;
    }
    
    // Get course_id from section
    $course_sql = "SELECT s.course_id, c.course_code, c.course_title 
                   FROM tbl_section s
                   INNER JOIN tbl_course c ON s.course_id = c.course_id
                   WHERE s.section_id = ? AND s.is_deleted = 0 AND c.is_deleted = 0";
    $course_stmt = $conn->prepare($course_sql);
    $course_stmt->bind_param("i", $section_id);
    $course_stmt->execute();
    $course_result = $course_stmt->get_result();
    
    if ($course_result->num_rows === 0) {
        sendResponse(false, 'Section not found');
        return;
    }
    
    $course = $course_result->fetch_assoc();
    $course_id = $course['course_id'];
    
    // Get all prerequisites
    $prereq_sql = "SELECT 
                      pc.course_id,
                      pc.course_code,
                      pc.course_title
                   FROM tbl_course_prerequisite cp
                   INNER JOIN tbl_course pc ON cp.prereq_course_id = pc.course_id
                   WHERE cp.course_id = ? AND cp.is_deleted = 0 AND pc.is_deleted = 0";
    
    $prereq_stmt = $conn->prepare($prereq_sql);
    $prereq_stmt->bind_param("i", $course_id);
    $prereq_stmt->execute();
    $prereq_result = $prereq_stmt->get_result();
    
    $prerequisites = [];
    while ($row = $prereq_result->fetch_assoc()) {
        $prerequisites[] = $row;
    }
    
    sendResponse(true, 'Prerequisites retrieved', [
        'course' => $course,
        'prerequisites' => $prerequisites
    ]);
}

function readEnrollments() {
    global $conn;
    
    $search = sanitize($_GET['search'] ?? '');
    $order = strtoupper(sanitize($_GET['order'] ?? 'DESC'));
    $order = ($order === 'ASC') ? 'ASC' : 'DESC';
    
    if (!empty($search)) {
        $sql = "SELECT e.*,
                       CONCAT(st.first_name, ' ', st.last_name) as student_name,
                       st.student_no,
                       c.course_code, c.course_title,
                       sec.section_code,
                       t.term_code
                FROM tbl_enrollment e
                LEFT JOIN tbl_student st ON e.student_id = st.student_id AND st.is_deleted = 0
                LEFT JOIN tbl_section sec ON e.section_id = sec.section_id AND sec.is_deleted = 0
                LEFT JOIN tbl_course c ON sec.course_id = c.course_id AND c.is_deleted = 0
                LEFT JOIN tbl_term t ON sec.term_id = t.term_id AND t.is_deleted = 0
                WHERE (st.student_no LIKE ? OR st.first_name LIKE ? OR st.last_name LIKE ? OR c.course_code LIKE ?)
                AND e.is_deleted = 0
                ORDER BY e.enrollment_id $order";
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
                FROM tbl_enrollment e
                LEFT JOIN tbl_student st ON e.student_id = st.student_id AND st.is_deleted = 0
                LEFT JOIN tbl_section sec ON e.section_id = sec.section_id AND sec.is_deleted = 0
                LEFT JOIN tbl_course c ON sec.course_id = c.course_id AND c.is_deleted = 0
                LEFT JOIN tbl_term t ON sec.term_id = t.term_id AND t.is_deleted = 0
                WHERE e.is_deleted = 0
                ORDER BY e.enrollment_id $order";
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
        return;
    }
    
    $sql = "UPDATE tbl_enrollment SET student_id = ?, section_id = ?, date_enrolled = ?, status = ?, letter_grade = ?
            WHERE enrollment_id = ? AND is_deleted = 0";
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
    
    if (softDelete('tbl_enrollment', 'enrollment_id', $enrollment_id)) {
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
            FROM tbl_enrollment e
            LEFT JOIN tbl_student st ON e.student_id = st.student_id
            LEFT JOIN tbl_section sec ON e.section_id = sec.section_id
            LEFT JOIN tbl_course c ON sec.course_id = c.course_id
            LEFT JOIN tbl_term t ON sec.term_id = t.term_id
            WHERE e.enrollment_id = ? AND e.is_deleted = 0";
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
    
    if (restoreDeleted('tbl_enrollment', 'enrollment_id', $enrollment_id)) {
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
            FROM tbl_enrollment e
            LEFT JOIN tbl_student st ON e.student_id = st.student_id
            LEFT JOIN tbl_section sec ON e.section_id = sec.section_id
            LEFT JOIN tbl_course c ON sec.course_id = c.course_id
            LEFT JOIN tbl_term t ON sec.term_id = t.term_id
            WHERE e.is_deleted = 1
            ORDER BY e.enrollment_id DESC";
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
