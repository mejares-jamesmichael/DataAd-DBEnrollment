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
    case 'getPrerequisites':
        getPrerequisitesForCourse();
        break;
    case 'restore':
        restoreCourse();
        break;
    case 'readDeleted':
        readDeletedCourses();
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
        $course_id = $conn->insert_id;
        if (isset($_POST['prerequisites']) && is_array($_POST['prerequisites'])) {
            $prereq_sql = "INSERT INTO tblCoursePrerequisites (course_id, prereq_course_id) VALUES (?, ?)";
            $prereq_stmt = $conn->prepare($prereq_sql);
            foreach ($_POST['prerequisites'] as $prereq_id) {
                $prereq_stmt->bind_param("ii", $course_id, $prereq_id);
                $prereq_stmt->execute();
            }
        }
        sendResponse(true, 'Course created successfully', ['id' => $course_id]);
    } else {
        sendResponse(false, 'Error: ' . $stmt->error);
    }
}

function readCourses() {
    global $conn;
    
    $search = sanitize($_GET['search'] ?? '');
    $order = strtoupper(sanitize($_GET['order'] ?? 'DESC'));
    $order = ($order === 'ASC') ? 'ASC' : 'DESC';

    $baseSql = "SELECT c.*, d.dept_name, d.dept_code,
                       GROUP_CONCAT(pc.course_code ORDER BY pc.course_code SEPARATOR ', ') as prerequisites
                FROM tblCourses c
                LEFT JOIN tblDepartments d ON c.dept_id = d.dept_id AND d.deleted_at IS NULL
                LEFT JOIN tblCoursePrerequisites cp ON c.course_id = cp.course_id
                LEFT JOIN tblCourses pc ON cp.prereq_course_id = pc.course_id";

    if (!empty($search)) {
        $sql = "$baseSql WHERE (c.course_code LIKE ? OR c.course_title LIKE ? OR d.dept_name LIKE ?)
                AND c.deleted_at IS NULL
                GROUP BY c.course_id
                ORDER BY c.course_id $order";
        $stmt = $conn->prepare($sql);
        $searchTerm = "%$search%";
        $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
    } else {
        $sql = "$baseSql WHERE c.deleted_at IS NULL
                GROUP BY c.course_id
                ORDER BY c.course_id $order";
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
        // First, delete existing prerequisites
        $delete_sql = "DELETE FROM tblCoursePrerequisites WHERE course_id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $course_id);
        $delete_stmt->execute();

        // Then, insert new prerequisites
        if (isset($_POST['prerequisites']) && is_array($_POST['prerequisites'])) {
            $prereq_sql = "INSERT INTO tblCoursePrerequisites (course_id, prereq_course_id) VALUES (?, ?)";
            $prereq_stmt = $conn->prepare($prereq_sql);
            foreach ($_POST['prerequisites'] as $prereq_id) {
                $prereq_stmt->bind_param("ii", $course_id, $prereq_id);
                $prereq_stmt->execute();
            }
        }
        sendResponse(true, 'Course updated successfully');
    } else {
        sendResponse(false, 'Error: ' . $stmt->error);
    }
.
}

function deleteCourse() {
    global $conn;
    
    $course_id = intval($_POST['course_id']);
    
    if (softDelete('tblCourses', 'course_id', $course_id)) {
        sendResponse(true, 'Course deleted successfully');
    } else {
        sendResponse(false, 'Error deleting course');
    }
}

function getCourse() {
    global $conn;
    
    $course_id = intval($_GET['course_id']);
    
    $sql = "SELECT c.*, d.dept_name, d.dept_code
            FROM tblCourses c
            LEFT JOIN tblDepartments d ON c.dept_id = d.dept_id
            WHERE c.course_id = ? AND c.deleted_at IS NULL";
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

function getPrerequisitesForCourse() {
    global $conn;

    $course_id = intval($_GET['course_id']);

    $sql = "SELECT prereq_course_id FROM tblCoursePrerequisites WHERE course_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $prerequisites = [];

    while ($row = $result->fetch_assoc()) {
        $prerequisites[] = $row['prereq_course_id'];
    }

    sendResponse(true, 'Prerequisites retrieved successfully', $prerequisites);
}

function restoreCourse() {
    global $conn;
    
    $course_id = intval($_POST['course_id']);
    
    if (restoreDeleted('tblCourses', 'course_id',_id)) {
        sendResponse(true, 'Course restored successfully');
    } else {
        sendResponse(false, 'Error restoring course');
    }
}

function readDeletedCourses() {
    global $conn;
    
    $sql = "SELECT c.*, d.dept_name, d.dept_code
            FROM tblCourses c
            LEFT JOIN tblDepartments d ON c.dept_id = d.dept_id
            WHERE c.deleted_at IS NOT NULL 
            ORDER BY c.deleted_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $courses = [];
    
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }
    
    sendResponse(true, 'Deleted courses retrieved successfully', $courses);
}
?>