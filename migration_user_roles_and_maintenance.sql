-- Migration SQL for User Roles, Permissions, and Database Maintenance Features
-- Execute this after importing dbenrollment.sql

USE dbenrollment;

-- --------------------------------------------------------
-- Table structure for table `tbl_users`
-- Stores user accounts with role-based access
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `tbl_users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','registrar','instructor','student') NOT NULL DEFAULT 'student',
  `related_id` int(11) DEFAULT NULL COMMENT 'Links to instructor_id or student_id based on role',
  `email` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert default admin user
-- Username: admin, Password: admin123 (hashed with PASSWORD_DEFAULT)
INSERT INTO `tbl_users` (`username`, `password`, `role`, `email`, `full_name`, `is_active`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'admin@pup.edu.ph', 'System Administrator', 1);

-- --------------------------------------------------------
-- Table structure for table `tbl_backup_logs`
-- Tracks database backup history
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `tbl_backup_logs` (
  `backup_id` int(11) NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) NOT NULL,
  `file_size` bigint(20) DEFAULT NULL COMMENT 'Size in bytes',
  `backup_date` datetime NOT NULL,
  `performed_by` int(11) DEFAULT NULL COMMENT 'user_id who performed backup',
  `status` enum('success','failed') NOT NULL DEFAULT 'success',
  `notes` text DEFAULT NULL,
  PRIMARY KEY (`backup_id`),
  KEY `performed_by_fk` (`performed_by`),
  CONSTRAINT `performed_by_fk` FOREIGN KEY (`performed_by`) REFERENCES `tbl_users` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `tbl_archived_enrollments`
-- Stores old enrollment records for data archival
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `tbl_archived_enrollments` (
  `archive_id` int(11) NOT NULL AUTO_INCREMENT,
  `enrollment_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `section_id` int(11) DEFAULT NULL,
  `date_enrolled` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `letter_grade` varchar(255) DEFAULT NULL,
  `archived_date` datetime NOT NULL DEFAULT current_timestamp(),
  `archived_by` int(11) DEFAULT NULL COMMENT 'user_id who archived',
  `original_data` text DEFAULT NULL COMMENT 'JSON of complete enrollment data',
  PRIMARY KEY (`archive_id`),
  KEY `archived_by_fk` (`archived_by`),
  CONSTRAINT `archived_by_fk` FOREIGN KEY (`archived_by`) REFERENCES `tbl_users` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `tbl_activity_logs`
-- Tracks user actions for audit trail
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `tbl_activity_logs` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(50) NOT NULL COMMENT 'login, logout, create, update, delete, backup, restore',
  `entity_type` varchar(50) DEFAULT NULL COMMENT 'student, course, enrollment, etc.',
  `entity_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`log_id`),
  KEY `user_id_log_fk` (`user_id`),
  CONSTRAINT `user_id_log_fk` FOREIGN KEY (`user_id`) REFERENCES `tbl_users` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Add index for prerequisite validation performance
-- --------------------------------------------------------

ALTER TABLE `tbl_course_prerequisite` 
ADD INDEX `course_prereq_idx` (`course_id`, `prereq_course_id`);

-- --------------------------------------------------------
-- Create view for easy prerequisite checking
-- --------------------------------------------------------

CREATE OR REPLACE VIEW `vw_course_prerequisites` AS
SELECT 
    c.course_id,
    c.course_code,
    c.course_title,
    cp.prereq_course_id,
    pc.course_code as prereq_course_code,
    pc.course_title as prereq_course_title
FROM tbl_course c
LEFT JOIN tbl_course_prerequisite cp ON c.course_id = cp.course_id AND cp.is_deleted = 0
LEFT JOIN tbl_course pc ON cp.prereq_course_id = pc.course_id AND pc.is_deleted = 0
WHERE c.is_deleted = 0;

-- --------------------------------------------------------
-- Create view for student prerequisite status
-- --------------------------------------------------------

CREATE OR REPLACE VIEW `vw_student_completed_courses` AS
SELECT 
    e.student_id,
    s.section_id,
    s.course_id,
    c.course_code,
    c.course_title,
    e.letter_grade,
    e.status,
    CASE 
        WHEN e.letter_grade IN ('P', 'A', 'B', 'C', '1.0', '1.25', '1.5', '1.75', '2.0', '2.25', '2.5', '2.75', '3.0') THEN 1
        ELSE 0
    END as is_passed
FROM tbl_enrollment e
INNER JOIN tbl_section s ON e.section_id = s.section_id AND s.is_deleted = 0
INNER JOIN tbl_course c ON s.course_id = c.course_id AND c.is_deleted = 0
WHERE e.is_deleted = 0 
    AND e.status IN ('enrolled', 'completed')
    AND e.letter_grade IS NOT NULL;

COMMIT;

-- --------------------------------------------------------
-- Migration completed
-- --------------------------------------------------------
-- Default Credentials:
-- Username: admin
-- Password: admin123
-- 
-- Note: Change admin password after first login!
-- --------------------------------------------------------
