-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 18, 2025 at 05:37 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dbenrollment`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_course`
--

CREATE TABLE `tbl_course` (
  `course_id` int(11) NOT NULL,
  `course_code` varchar(255) DEFAULT NULL,
  `course_title` varchar(255) DEFAULT NULL,
  `units` int(11) DEFAULT NULL,
  `lecture_hours` int(11) DEFAULT NULL,
  `lab_hours` int(11) DEFAULT NULL,
  `dept_id` int(11) DEFAULT NULL,
  `is_deleted` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_course`
--

INSERT INTO `tbl_course` (`course_id`, `course_code`, `course_title`, `units`, `lecture_hours`, `lab_hours`, `dept_id`, `is_deleted`) VALUES
(1, 'COMP 001', 'Introduction to Computing', 3, 2, 3, 6, 0),
(2, 'COMP 002', 'Computer Programming 1', 3, 2, 3, 6, 0),
(3, 'GEED 004', 'Mathematics in the Modern World/Matematika sa Makabagong Daigdig', 3, 3, 0, 6, 0),
(4, 'GEED 005', 'Purposive Communication/Malayuning Komunikasyon', 3, 3, 0, 6, 0),
(5, 'ITEC 101', 'Keyboarding and Documents Processing with Laboratory', 3, 2, 3, 6, 0),
(6, 'ITEC 102', 'Basic Computer Hardware Servicing', 3, 2, 3, 6, 0),
(7, 'NSTP 001', 'National Service Training Program 1', 3, 3, 0, 6, 0),
(8, 'PATHFIT 1', 'Physical Activity Towards Health and Fitness 1', 2, 2, 0, 6, 0),
(9, 'COMP 003', 'Computer Programming 2', 3, 2, 3, 6, 0),
(10, 'COMP 004', 'Discrete Structures 1', 3, 3, 0, 6, 0),
(11, 'COMP 024', 'Technopreneurship', 3, 3, 0, 6, 0),
(12, 'GEED 001', 'Understanding the Self/Pag-unawa sa Sarili', 3, 3, 0, 6, 0),
(13, 'GEED 007', 'Science, Technology and Society/Agham, Teknolohiya, at Lipunan', 3, 3, 0, 6, 0),
(14, 'ITEC 103', 'Hardware/Software Installation and Maintenance', 3, 2, 3, 6, 0),
(15, 'ITEC 104', 'Basic Electronics', 2, 0, 6, 6, 0),
(16, 'NSTP 002', 'National Service Training Program 2', 3, 3, 0, 6, 0),
(17, 'PATHFIT 2', 'Physical Activity Towards Health and Fitness 2', 2, 2, 0, 6, 0),
(18, 'COMP 006', 'Data Structures and Algorithms', 3, 2, 3, 6, 0),
(19, 'COMP 007', 'Operating Systems', 3, 2, 3, 6, 0),
(20, 'COMP 008', 'Data Communications and Networking', 3, 2, 3, 6, 0),
(21, 'COMP 023', 'Social and Professional Issues in Computing', 3, 3, 0, 6, 0),
(22, 'INTE 201', 'Programming 3 (Structured Programming)', 3, 2, 3, 6, 0),
(23, 'INTE 202', 'Integrative Programming and Technologies 1', 3, 2, 3, 6, 0),
(24, 'PATHFIT 3', 'Physical Activity Towards Health and Fitness 3', 2, 2, 0, 6, 0),
(25, 'COMP 009', 'Object Oriented Programming', 3, 2, 3, 6, 0),
(26, 'COMP 010', 'Information Management', 3, 2, 3, 6, 0),
(27, 'COMP 012', 'Network Administration', 3, 2, 3, 6, 0),
(28, 'COMP 013', 'Human Computer Interaction', 3, 3, 0, 6, 0),
(29, 'COMP 014', 'Quantitative Methods with Modeling and Simulation', 3, 3, 0, 6, 0),
(30, 'COMP 016', 'Web Development', 3, 2, 3, 6, 0),
(31, 'COMP 030', 'Business Intelligence', 3, 2, 3, 6, 0),
(32, 'INTE 403', 'Systems Administration and Maintenance', 3, 2, 3, 6, 0),
(33, 'PATHFIT 4', 'Physical Activity Towards Health and Fitness 4', 2, 2, 0, 6, 0),
(34, 'ITEC 201', 'Practicum 1 (Junior Programmer 1 / Junior Programmer 2 - 300 hours)', 3, 1, 6, 6, 0),
(35, 'COMP 015', 'Fundamentals of Research', 3, 3, 0, 6, 0),
(36, 'COMP 017', 'Multimedia', 3, 2, 3, 6, 0),
(37, 'COMP 018', 'Database Administration', 3, 2, 3, 6, 0),
(38, 'COMP 019', 'Applications Development and Emerging Technologies', 3, 2, 3, 6, 0),
(39, 'COMP 025', 'Project Management', 3, 2, 3, 6, 0),
(40, 'COMP 027', 'Mobile Application Development (SMP PLUS)', 3, 2, 3, 6, 0),
(41, 'INTE 351', 'Systems Analysis and Design', 3, 3, 0, 6, 0),
(42, 'ITEC 301', 'Advance Programming', 3, 2, 3, 6, 0),
(43, 'ITEC 302', 'Capstone', 3, 2, 3, 6, 0),
(44, 'ITEC 303', 'Practicum 2 (Computer Programming Specialist - 300 hours)', 3, 1, 6, 6, 0),
(45, 'ITEC 304', 'Seminar on Issues and Trends in Information Technology', 2, 0, 6, 6, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_course_prerequisite`
--

CREATE TABLE `tbl_course_prerequisite` (
  `course_id` int(11) NOT NULL,
  `prereq_course_id` int(11) NOT NULL,
  `is_deleted` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_course_prerequisite`
--

INSERT INTO `tbl_course_prerequisite` (`course_id`, `prereq_course_id`, `is_deleted`) VALUES
(9, 2, 0),
(10, 3, 0),
(14, 6, 0),
(17, 8, 0),
(19, 1, 0),
(22, 9, 0),
(24, 17, 0),
(25, 9, 0),
(27, 20, 0),
(30, 23, 0),
(33, 24, 0),
(36, 30, 0),
(42, 9, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_department`
--

CREATE TABLE `tbl_department` (
  `dept_id` int(11) NOT NULL,
  `dept_code` varchar(255) DEFAULT NULL,
  `dept_name` varchar(255) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_department`
--

INSERT INTO `tbl_department` (`dept_id`, `dept_code`, `dept_name`, `is_deleted`) VALUES
(1, 'CAF', 'College of Accountancy and Finances', 0),
(2, 'CADBE', 'College of Architecture, Design and the Built Environment', 0),
(3, 'CAL', 'College of Arts and Letters', 0),
(4, 'CBA', 'College of Business Administration', 0),
(5, 'COC', 'College of Communication', 0),
(6, 'CCIS', 'College of Computer and Information Sciences ', 0),
(7, 'COED', 'College of Education', 0),
(8, 'CE', 'College of Engineering', 0),
(9, 'CHK', 'College of Human Kinetics', 0),
(10, 'CL', 'College of Law', 0),
(11, 'CPSPA', 'College of Political Science and Public Administration', 0),
(12, 'CSSD', 'College of Social Sciences and Development', 0),
(13, 'CS', 'College of Science', 0),
(14, 'CTHTM', 'College of Tourism, Hospitality and Transportation Management', 0),
(18, 'dasdadasdada', 'dadadsdad', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_enrollment`
--

CREATE TABLE `tbl_enrollment` (
  `enrollment_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `section_id` int(11) DEFAULT NULL,
  `date_enrolled` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `letter_grade` varchar(255) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_enrollment`
--

INSERT INTO `tbl_enrollment` (`enrollment_id`, `student_id`, `section_id`, `date_enrolled`, `status`, `letter_grade`, `is_deleted`) VALUES
(10, 1, 1, '2025-10-28', 'enrolled', 'P', 0),
(11, 2, 1, '2025-10-28', 'enrolled', 'P', 0),
(12, 3, 1, '2025-10-29', 'enrolled', 'P', 0),
(13, 4, 1, '2025-10-29', 'enrolled', 'P', 0),
(14, 5, 1, '2025-10-29', 'enrolled', 'P', 0),
(15, 6, 1, '2025-10-29', 'enrolled', 'P', 0),
(16, 7, 1, '2025-10-29', 'enrolled', 'P', 0),
(17, 8, 1, '2025-10-29', 'enrolled', 'P', 0),
(18, 9, 1, '2025-10-29', 'enrolled', 'P', 0),
(19, 10, 1, '2025-10-29', 'enrolled', 'P', 0);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_instructor`
--

CREATE TABLE `tbl_instructor` (
  `instructor_id` int(11) NOT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `dept_id` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_instructor`
--

INSERT INTO `tbl_instructor` (`instructor_id`, `last_name`, `first_name`, `email`, `dept_id`, `is_deleted`) VALUES
(1, 'Almirañez', 'Gecilie', 'geciliealmiranez@pup.edu.ph', 6, 0),
(2, 'Modesto', 'Lady', 'ladymodesto@pup.edu.ph', 6, 0),
(3, 'Villarosa', 'Steven', 'svillarosa@pup.edu.ph', 6, 0),
(4, 'San Luis', 'Angelo', 'ajsanluis@pup.edu.ph', 6, 0),
(5, 'Minalabag', 'Jim', 'jminalabag@pup.edu.ph', 3, 0),
(6, 'Santos', 'Aren Dred', 'arendredsantos@pup.edu.ph', 6, 0),
(7, 'Santos', 'John Dustin', 'jdsantos@pup.edu.ph', 6, 0),
(22, 'Barrios', 'Virginia', 'barriosvirginia57300@gmail.com', 2, 1),
(23, 'dada', 'adadad', 'doejaneee@gmail.com', 3, 1),
(24, 'Delima', 'Justine', 'delimajustine24@gmail.com', 11, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_program`
--

CREATE TABLE `tbl_program` (
  `program_id` int(11) NOT NULL,
  `program_code` varchar(255) DEFAULT NULL,
  `program_name` varchar(255) DEFAULT NULL,
  `dept_id` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_program`
--

INSERT INTO `tbl_program` (`program_id`, `program_code`, `program_name`, `dept_id`, `is_deleted`) VALUES
(4, 'DIT', 'Diploma in Information Technology', 1, 0),
(5, 'BSOA', 'Bachelor of Science in Office Administration', 4, 0),
(6, 'BSIT', 'Bachelor of Science in Information Technology', 1, 0),
(7, 'BSECE', 'Bachelor of Science in Electronics Engineering', 3, 0),
(8, 'BSME', 'Bachelor of Science in Mechanical Engineering', 3, 0),
(9, 'BSED-ENG', 'Bachelor of Secondary Education Major in English', 2, 0),
(10, 'BSED-MAT', 'Bachelor of Secondary Education Major in Mathematics', 2, 0),
(11, 'BSPSY', 'Bachelor of Science in Psychology', 1, 0),
(12, 'BSBA-HRM', 'Bachelor of Science in Business Administration Major in Human Resource Management', 1, 0),
(13, 'BSBA-MM', 'Bachelor of Science in Business Administration Major in Marketing Management', 2, 0),
(14, 'DOMT', 'Diploma in Office Management Technology', 4, 0),
(15, 'BSA', 'Bachelor of Science in Accountancy', 4, 0),
(17, 'BSMMA', 'dadadad', 6, 1),
(18, 'adada', 'dasdasda', 3, 1),
(19, 'rwerwrw', 'rwerwrw', 5, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_room`
--

CREATE TABLE `tbl_room` (
  `room_id` int(11) NOT NULL,
  `building` varchar(255) DEFAULT NULL,
  `room_code` varchar(255) DEFAULT NULL,
  `capacity` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_room`
--

INSERT INTO `tbl_room` (`room_id`, `building`, `room_code`, `capacity`, `is_deleted`) VALUES
(1, 'A', 'A202', 50, 0),
(2, 'A', 'A203', 50, 0),
(3, 'A', 'A204', 50, 0),
(4, 'A', 'A205/ABOITIZ', 50, 0),
(5, 'A', 'A208/DOST', 50, 0),
(6, 'A', 'A301', 50, 0),
(7, 'A', 'A302', 50, 0),
(8, 'A', 'A303', 50, 0),
(9, 'A', 'A304', 50, 0),
(10, 'A', 'A308', 50, 0),
(11, 'A', 'A309/BAYER', 50, 0),
(12, 'A', 'A310', 50, 0),
(13, 'A', 'A401', 50, 0),
(14, 'A', 'A402', 50, 0),
(17, 'A', 'A403', 50, 0),
(19, 'A', 'A404', 50, 0),
(20, 'A', 'A405', 50, 0),
(21, 'A', 'Keyboarding Lab', 50, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_section`
--

CREATE TABLE `tbl_section` (
  `section_id` int(11) NOT NULL,
  `section_code` varchar(255) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL,
  `term_id` int(11) DEFAULT NULL,
  `instructor_id` int(11) DEFAULT NULL,
  `day_pattern` varchar(255) DEFAULT NULL,
  `start_time` varchar(255) DEFAULT NULL,
  `end_time` varchar(255) DEFAULT NULL,
  `room_id` int(11) DEFAULT NULL,
  `max_capacity` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_section`
--

INSERT INTO `tbl_section` (`section_id`, `section_code`, `course_id`, `term_id`, `instructor_id`, `day_pattern`, `start_time`, `end_time`, `room_id`, `max_capacity`, `is_deleted`) VALUES
(1, 'DIT-TG', 25, 1, 2, 'TH', '10:30:00', '03:00:00', 13, 14, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_student`
--

CREATE TABLE `tbl_student` (
  `student_id` int(11) NOT NULL,
  `student_no` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `birthdate` varchar(255) DEFAULT NULL,
  `gender` varchar(255) DEFAULT NULL,
  `year_level` int(11) DEFAULT NULL,
  `program_id` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_student`
--

INSERT INTO `tbl_student` (`student_id`, `student_no`, `last_name`, `first_name`, `middle_name`, `email`, `birthdate`, `gender`, `year_level`, `program_id`, `is_deleted`) VALUES
(1, '2023-00424-TG-0', 'Alejandro', 'Aleck Mcklaiyre', 'R', 'aleck.alejandro04@gmail.com', '2004-09-03', 'M', 3, 4, 0),
(2, '2023-00425-TG-0', 'Andaya', 'Gener Jr.', 'A', 'generandaya4@gmail.com', '2004-09-17', 'M', 3, 4, 0),
(3, '2023-00426-TG-0', 'Arroyo', 'John Matthew', 'S', 'johnmatthewarroyo2@gmail.com', '2005-01-01', 'M', 3, 4, 0),
(4, '2023-00427-TG-0', 'Barcelos', 'Kevin Joseph', 'V', 'kevinbarcelos1@gmail.com', '2005-01-01', 'M', 3, 4, 0),
(5, '2023-00429-TG-0', 'Citron', 'Kathleen', 'C', 'kccitron@gmail.com', '2005-06-08', 'F', 3, 4, 0),
(6, '2023-00431-TG-0', 'Consultado', 'Kirby', 'G', 'kirbyconsultado@gmail.com', '2005-09-10', 'M', 3, 4, 0),
(7, '2023-00432-TG-0', 'De Leon', 'Jasmine Robelle', 'C', 'yaskyeria@gmail.com', '2004-08-04', 'F', 3, 4, 0),
(8, '2023-00433-TG-0', 'Delima', 'Justine', 'R', 'delimajustine24@gmail.com', '2005-02-24', 'M', 3, 4, 0),
(9, '2023-00496-TG-0', 'Delumen', 'Ivan', 'V', 'ivandelumen05@gmail.com', '2004-10-28', 'M', 3, 4, 0),
(10, '2023-00434-TG-0', 'Durante', 'Stephanie', 'V', 'durantestephanie07@gmail.com', '2005-02-22', 'F', 3, 4, 0),
(11, '2023-00435-TG-0', 'Esparagoza', 'Mikka Kette', 'P', 'esparagozamikkakette@gmail.com', '2004-11-12', 'F', 3, 4, 0),
(12, '2023-00436-TG-0', 'Florido', 'Maydelyn', 'B', 'maydelynflorido07@gmail.com', '2005-06-07', 'F', 3, 4, 0),
(13, '2023-00437-TG-0', 'Francisco', 'Krislyn Janelle', 'T', 'krislynfrancisco0815@gmail.com', '2005-08-20', 'F', 3, 4, 0),
(14, '2023-00438-TG-0', 'Genandoy', 'Hannah Lorainne', 'M', 'hann000345@gmail.com', '2005-04-03', 'F', 3, 4, 0),
(15, '2023-00439-TG-0', 'Gomez', 'Ashley Hermione', 'C', 'hermionegomez49@gmail.com', '2005-07-10', 'F', 3, 4, 0),
(16, '2023-00498-TG-0', 'Lazaro', 'Franco Alfonso', 'C', 'francoalfonso411@gmail.com', '2005-04-11', 'M', 3, 4, 0),
(17, '2023-00440-TG-0', 'Mamasalanang', 'Gerald', 'K', 'cscb.vpr.gerald@gmail.com', '2005-10-26', 'M', 3, 4, 0),
(18, '2023-00441-TG-0', 'Mejares', 'James Michael', 'C', 'jamesmichaelmejares@gmail.com', '2005-11-07', 'M', 3, 4, 0),
(19, '2023-00443-TG-0', 'Mosquito', 'Michael Angelo', 'P', 'michaelmosquito147@gmail.com', '2005-06-08', 'M', 3, 4, 0),
(20, '2023-00519-TG-0', 'Nolluda', 'John Carlo', 'I', 'johncarlonolluda@gmail.com', '2004-09-18', 'M', 3, 4, 0),
(21, '2023-00444-TG-0', 'Piadozo', 'Edriane', 'O', 'piadozoedriane@gmail.com', '2005-01-01', 'M', 3, 4, 0),
(22, '2023-00445-TG-0', 'Quiambao', 'Ma. Patricia Anne', 'D', 'patriciaquiambao078@gmail.com', '2004-07-13', 'F', 3, 4, 0),
(23, '2023-00446-TG-0', 'Relente', 'Patricia Joy', 'C', 'relente.patriciajoy@gmail.com', '2004-01-03', 'F', 3, 4, 0),
(24, '2023-00447-TG-0', 'Reyes', 'Simone Jake', 'D', 'reyesjake262@gmail.com', '2004-11-17', 'M', 3, 4, 0),
(25, '2023-00448-TG-0', 'Riomalos', 'Zyrrah Feil', 'C', 'zriomalos@gmail.com', '2005-09-24', 'F', 3, 4, 0),
(26, '2023-00449-TG-0', 'Siervo', 'Jallaine Perpetua', 'G', 'jallainesiervo143@gmail.com', '2004-08-23', 'F', 3, 4, 0),
(27, '2023-00450-TG-0', 'Uy', 'Angelica Joy', '', 'angelicajoyuy16@gmail.com', '2004-12-16', 'F', 3, 4, 0),
(28, '2023-00451-TG-0', 'Vesliño', 'Marc', 'D', 'marcveslino000@gmail.com', '2005-05-04', 'M', 3, 4, 0),
(29, '2023-00495-TG-0', 'Victorioso', 'Daniel', 'B', 'danielvictorioso.acd@gmail.com', '2000-11-05', 'M', 3, 4, 0),
(30, '2023-00452-TG-0', 'Villas', 'Clarence', 'F', 'villasclarence56@gmail.com', '2004-10-04', 'M', 3, 4, 0),
(31, '2023-00453-TG-0', 'Ynion', 'Ma. Bea Mae', 'D', 'mabeamaeynion@gmail.com', '2004-07-29', 'F', 3, 4, 0),
(32, '2024-00309-TG-0', 'Acido', 'Roland Renz', 'D', 'acidorenz22@gmail.com', '2004-09-22', 'M', 2, 4, 0),
(33, '2024-00310-TG-0', 'Allego', 'Yuan Paolo', 'A', 'allegoyuanpaolo@gmail.com', '2005-12-30', 'M', 2, 4, 0),
(34, '2024-00515-TG-0', 'Andador', 'Kim Phillip', 'G', 'andadorkimphillipg@gmail.com', '2005-11-16', 'M', 2, 4, 0),
(35, '2024-00524-TG-0', 'Arellano', 'Charlz Keneth', 'L', 'tkminer000@gmail.com', '2003-12-09', 'M', 2, 4, 0),
(36, '2024-00311-TG-0', 'Ariba', 'Mariane Andrea', 'R', 'marianeariba12@gmail.com', '2005-12-10', 'F', 2, 4, 0),
(37, '2024-00480-TG-0', 'Bangaysiso', 'Denze Gervin', 'T', 'denzegervin@gmail.com', '2005-06-12', 'M', 2, 4, 0),
(38, '2024-00312-TG-0', 'Baquiran', 'Prinz Walter', 'R', 'baquiranprinzwalter09@gmail.com', '2006-10-09', 'M', 2, 4, 0),
(39, '2024-00313-TG-0', 'Bawlite', 'Aivan Gabriel', 'C', 'bawliteaivan@gmail.com', '2006-08-08', 'M', 2, 4, 0),
(40, '2024-00314-TG-0', 'Bigtas', 'Jose', NULL, 'jmbigtas0325@gmail.com', '2006-03-25', 'M', 2, 4, 0),
(41, '2024-00315-TG-0', 'Cabasug', 'Francis Dale', 'M', 'franciscabasug26@gmail.com', '2006-01-25', 'M', 2, 4, 0),
(42, '2024-00368-TG-0', 'Cabiades', 'Stephen Cedric', 'O', 'sccabiades@gmail.com', '2006-09-02', 'M', 2, 4, 0),
(43, '2024-00317-TG-0', 'Castillo', 'John Paul', 'E', 'castillojohnpaul001@gmail.com', '2005-09-01', 'M', 2, 4, 0),
(44, '2024-00320-TG-0', 'Castro', 'John Vincent', 'G', 'castrojohn105@gmail.com', '2006-05-17', 'M', 2, 4, 0),
(45, '2024-00358-TG-0', 'Catalan', 'James Rolmer', 'V', 'jamescatalan19@gmail.com', '2006-03-19', 'M', 2, 4, 0),
(46, '2024-00324-TG-0', 'Cruz', 'Arvin James', 'B', 'arvinjamescruz23@gmail.com', '2006-08-23', 'M', 2, 4, 0),
(47, '2024-00325-TG-0', 'Dulaca', 'Amando III', 'R', 'amandodulacaiii0@gmail.com', '2006-07-20', 'M', 2, 4, 0),
(48, '2024-00327-TG-0', 'Espedido', 'Narciso Miguel', NULL, 'migz9.narciso@gmail.com', '2004-04-10', 'M', 2, 4, 0),
(49, '2024-00328-TG-0', 'Floresca', 'Duvan', NULL, 'duvanfloresca@gmail.com', '2004-08-15', 'M', 2, 4, 0),
(50, '2024-00329-TG-0', 'Furaque', 'Patricia Hannah', 'L', 'furaquepatriciahannah@gmail.com', '2004-12-08', 'F', 2, 4, 0),
(51, '2024-00330-TG-0', 'Libay', 'Jed', 'D', 'libayjeddelarema@gmail.com', '2006-02-07', 'M', 2, 4, 0),
(52, '2024-00332-TG-0', 'Limbaña', 'Renz Johanan', 'P', 'renzlimbana@gmail.com', '2005-06-02', 'M', 2, 4, 0),
(53, '2024-00333-TG-0', 'Lipata', 'Hanz Gemuel', 'M', 'lipatagemuelhanzy@gmail.com', '2006-05-09', 'M', 2, 4, 0),
(54, '2024-00334-TG-0', 'Lopez', 'Xander Ney', 'E', 'lopez.xander.ney016@gmail.com', '2005-05-12', 'M', 2, 4, 0),
(55, '2024-00337-TG-0', 'Mabalo', 'Jeremiah', 'E', 'mabalojeremiah@gmail.com', '2006-08-07', 'M', 2, 4, 0),
(56, '2024-00340-TG-0', 'Mandapat', 'Lloyd Frederick Jr.', 'M', 'lloyd.mandapat36@gmail.com', '2006-03-06', 'M', 2, 4, 0),
(57, '2024-00341-TG-0', 'Mariano', 'Iya Leonora', 'S', 'iyaonairam@gmail.com', '2006-01-16', 'F', 2, 4, 0),
(58, '2024-00343-TG-0', 'Mejilla', 'Hezekiah', 'R', 'hrmejilla@gmail.com', '2005-12-30', 'M', 2, 4, 0),
(59, '2024-00346-TG-0', 'Meneses', 'Daniel', 'R', 'danielmeneses434@gmail.com', '2006-01-06', 'M', 2, 4, 0),
(60, '2024-00351-TG-0', 'Nale', 'Luther Ian', 'S', 'lutheriannale@gmail.com', '2005-03-19', 'M', 2, 4, 0),
(61, '2024-00353-TG-0', 'Navarro', 'Leanne Jean', 'C', 'leannejn4@gmail.com', '2006-07-04', 'F', 2, 4, 0),
(62, '2024-00369-TG-0', 'Pascua', 'Vlee Joel', 'R', 'vleepascua04@gmail.com', '2006-04-04', 'M', 2, 4, 0),
(63, '2024-00356-TG-0', 'Ramos', 'John Renz', 'F', 'johnrenzr03@gmail.com', '2003-08-23', 'M', 2, 4, 0),
(64, '2024-00357-TG-0', 'Reniva', 'Rolando Miguel', 'C', 'miggireniva123@gmail.com', '2005-12-16', 'M', 2, 4, 0),
(65, '2024-00398-TG-0', 'Salosagcol', 'Marco Miguel', 'B', 'smarcomiguel222@gmail.com', '2005-08-28', 'M', 2, 4, 0),
(66, '2024-00359-TG-0', 'Salvador', 'Mary Elizabeth', 'S', 'maryelizabeth09584@gmail.com', '2005-07-12', 'F', 2, 4, 0),
(67, '2024-00360-TG-0', 'Samuya', 'Avelino Joseph', 'H', 'avelinosamuya1@gmail.com', '2005-04-24', 'M', 2, 4, 0),
(68, '2024-00361-TG-0', 'Sanchez', 'Gabriel', 'E', 'gabriel.raknchez@gmail.com', '2005-03-01', 'M', 2, 4, 0),
(69, '2024-00362-TG-0', 'Sequite', 'Kurt Laurence', 'S', 'kurtlaurencesequite23@gmail.com', '2006-05-22', 'M', 2, 4, 0),
(70, '2024-00364-TG-0', 'Tilog', 'Zyron Drei', 'R', 'tilogzyrondrei@gmail.com', '2005-11-30', 'M', 2, 4, 0),
(71, '2024-00365-TG-0', 'Tolentino', 'Vincent Johan', 'H', 'vincentjohantolentino@gmail.com', '2005-05-06', 'M', 2, 4, 0),
(72, '2024-00366-TG-0', 'Valila', 'Lhuise Gahbrielle', 'M', 'gahbie.valila@gmail.com', '2006-09-28', 'M', 2, 4, 0),
(73, '2024-00367-TG-0', 'Vasquez', 'Clark Justin', 'B', 'vasquezclarkjustin2006@gmail.com', '2006-08-10', 'M', 2, 4, 0),
(74, '2025-00503-TG-0', 'Salazar', 'Junior', 'C', 'salazarjc030@gmail.com', '2007-05-25', 'M', 1, 4, 0),
(75, '2025-00425-TG-0', 'Bacsal', 'Justin', 'M', 'justinbacsal35@gmail.com', '2007-01-24', 'M', 1, 4, 0),
(76, '2025 -00420-TG-', 'Abanag', 'Ruzzel Andrei', 'V', NULL, NULL, 'M', 1, 4, 0),
(77, '2025-00421-TG-0', 'Adto', 'Daniel', 'P', NULL, NULL, 'M', 1, 4, 0),
(78, '2025-00422-TG-0', 'Aldeza', 'Gabriel Dathan', 'M', 'gab.dathan@gmail.com', '2006-12-09', 'M', 1, 4, 0),
(79, '2025-00423-TG-0', 'Angco', 'Micaella', 'L', 'micaella2023@gmail.com', '2006-11-23', 'F', 1, 4, 0),
(80, '2025-00424-TG-0', 'Aroncillo', 'Andre', 'S', 'andrearoncillo30@gmail.com', '2007-07-30', 'M', 1, 4, 0),
(81, '2025-00426-TG-0', 'Bogñalbal', 'Devan', 'S', 'devybogzzy@gmail.com', '2007-02-13', 'M', 1, 4, 0),
(82, '2025-00427-TG-0', 'Botial', 'Christian Kim', 'T', NULL, NULL, 'M', 1, 4, 0),
(83, '2025-00428-TG-0', 'Caceres', 'Mark Kenneth', 'C', 'caceresmarkkenneth@gmail.com', '2006-03-08', 'M', 1, 4, 0),
(84, '2025-00430-TG-0', 'Cudera', 'Lorenz Samuel', 'Y', 'lrnz.cdra2@gmail.com', '2006-10-23', 'M', 1, 4, 0),
(85, '2025-00249-TG-0', 'Cho', 'Taisang', 'B', 'cts098098@gmail.com', '2006-12-10', 'M', 1, 4, 0),
(86, '2025-00431-TG-0', 'Daza', 'Dilan', 'H', NULL, NULL, 'M', 1, 4, 0),
(87, '2025-00432-TG-0', 'De Guzman', 'Steven Zanter', 'T', 'stevenzantertdeguzman@gmail.com', '2007-01-12', 'M', 1, 4, 0),
(88, '2025-00433-TG-0', 'Delos Santos', 'Kimberly Anne', 'N', 'delossantoskimberly227@gmail.com', '2007-03-11', 'F', 1, 4, 0),
(89, '2025-00435-TG-0', 'Efson', 'Jhon Marco', 'F', NULL, NULL, 'M', 1, 4, 0),
(90, '2025-00436-TG-0', 'Felipe', 'April', 'B', NULL, NULL, 'F', 1, 4, 0),
(91, '2025-00437-TG-0', 'Fuentes', 'Diana', 'C', 'diana.fuentes9700@gmail.com', '2006-01-01', 'F', 1, 4, 0),
(92, '2025-00438-TG-0', 'Gacoscos', 'Angel Ces', 'T', 'angelcesgacoscos@gmail.com', '2004-08-20', 'F', 1, 4, 0),
(93, '2025-00439-TG-0', 'Gatchalian', 'Edward Dave', 'D', NULL, NULL, 'M', 1, 4, 0),
(94, '2025-00440-TG-0', 'Glifonea', 'Alexander', 'K', 'Glifoneaalexander89@gmail.com', '2007-07-20', 'M', 1, 4, 0),
(95, '2025-00441-TG-0', 'Gutierrez', 'Ghail Nashane', 'S', 'ghailnashanegutierrez@gmail.com', '2004-12-19', 'F', 1, 4, 0),
(96, '2025-00442-TG-2', 'Huertas', 'Erica', 'F', NULL, NULL, 'F', 1, 4, 0),
(97, '2025-00443-TG-0', 'Lorenzo', 'Caleb Miguel', 'E', 'calebmiguel51@gmail.com', '2007-01-07', 'M', 1, 4, 0),
(98, '2025-00444-TG-0', 'Magbanua', 'Juliana Theressee', 'A', NULL, NULL, 'F', 1, 4, 0),
(99, '2025-00446-TG-0', 'Mansibang', 'Friyah Caszandra', 'B', NULL, NULL, 'F', 1, 4, 0),
(100, '2025-00447-TG-0', 'Masungsong', 'Lean Chad', 'A', 'leanchad21@gmail.com', '2006-09-29', 'M', 1, 4, 0),
(101, '2025-00448-TG-0', 'Murillo', 'Zius John', 'M', 'ziusmurillo@gmail.com', '2007-06-01', 'M', 1, 4, 0),
(102, '2025-00449-TG-0', 'Naron', 'Arianney', 'B', NULL, NULL, 'F', 1, 4, 0),
(103, '2025-00450-TG-0', 'Paccial', 'Jericjosh', 'C', 'paccialkim19@gmail.com', '2006-11-19', 'M', 1, 4, 0),
(104, '2025-00451-TG-0', 'Pacer', 'Kim Justin', 'C', 'pacerkimjustin9@gmail.com', '2006-09-27', 'M', 1, 4, 0),
(105, '2025-00452-TG-0', 'Palita', 'Ephraim', 'V', NULL, NULL, 'M', 1, 4, 0),
(106, '2025-00453-TG-0', 'Pastrana', 'Noel', 'C', NULL, NULL, 'M', 1, 4, 0),
(107, '2025-00455-TG-0', 'Penid', 'Joshua', 'M', 'penidjoshu4@gmail.com', '2002-09-01', 'M', 1, 4, 0),
(108, '2025-00454-TG-0', 'Pepito', 'Michael Rey', 'A', NULL, NULL, 'M', 1, 4, 0),
(109, '2025-00456-TG-0', 'Portas', 'Jewel Jomar Nash', 'L', NULL, NULL, 'M', 1, 4, 0),
(110, '2025-00458 TG-0', 'Rafael', 'Aaron Lemuel', 'R', 'kashimono443@gmail.com', '2007-01-12', 'M', 1, 4, 0),
(111, '2025-00457-TG-0', 'Ramilo', 'Meijen Florence', 'F', 'meijenramilo@gmail.com', '2007-09-05', 'M', 1, 4, 0),
(112, '2025-00459-TG-0', 'Reli', 'Marco', 'D', NULL, NULL, 'M', 1, 4, 0),
(113, '2025-00460-TG-0', 'Resma', 'Jhon Philip', 'L', 'jhonphilipresma46@gmail.com', '2007-01-21', 'M', 1, 4, 0),
(114, '2025-00461-TG-0', 'Rosales', 'Jermaine Dee', 'B', 'jermainedee042207@gmail.com', '2007-04-22', 'M', 1, 4, 0),
(115, '2025-00462-TG-0', 'Samonte', 'Gian Andrei', 'R', NULL, NULL, 'M', 1, 4, 0),
(116, '2025-00463-TG-0', 'Siladan', 'Jeremiah', 'A', 'jeremiahsiladan32206@gmail.com', '2006-03-22', 'M', 1, 4, 0),
(117, '2025-00464-TG-0', 'Tapic', 'Neo', 'P', 'neotapic21@gmail.com', '2005-12-03', 'M', 1, 4, 0),
(118, '2025-00465- TG-', 'Traqueña', 'Lyka Ericka Bianca', 'M', NULL, NULL, 'F', 1, 4, 0),
(119, '2025-00466-TG-0', 'Varron', 'Avner Roi', 'B', 'avnerroivarron11@gamil.com', '2007-10-11', 'M', 1, 4, 0),
(120, '2025-00467-TG-0', 'Villagarcia', 'Dion Alexander', 'F', 'dionalexandervillagarcia@gmail.com', '2006-03-03', 'M', 1, 4, 0),
(121, '2025-00468-TG-0', 'Yulo', 'Thyonne Pierre', 'B', NULL, NULL, 'M', 1, 4, 0),
(122, '2025-00469-TG-0', 'Zagada', 'John Joshua', 'A', 'null', '0000-00-00', 'M', 1, 4, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_term`
--

CREATE TABLE `tbl_term` (
  `term_id` int(11) NOT NULL,
  `term_code` varchar(255) DEFAULT NULL,
  `start_date` varchar(255) DEFAULT NULL,
  `end_date` varchar(255) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_term`
--

INSERT INTO `tbl_term` (`term_id`, `term_code`, `start_date`, `end_date`, `is_deleted`) VALUES
(1, '1st', '2023-09-26', '2023-12-31', 0),
(2, '2nd', '2023-12-31', '2024-07-12', 0),
(3, 'Sum', '2025-08-01', '2025-09-01', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_course`
--
ALTER TABLE `tbl_course`
  ADD PRIMARY KEY (`course_id`),
  ADD UNIQUE KEY `course_code` (`course_code`),
  ADD KEY `dept_id_fk3` (`dept_id`);

--
-- Indexes for table `tbl_course_prerequisite`
--
ALTER TABLE `tbl_course_prerequisite`
  ADD PRIMARY KEY (`course_id`,`prereq_course_id`),
  ADD KEY `prereq_course_idfk` (`prereq_course_id`);

--
-- Indexes for table `tbl_department`
--
ALTER TABLE `tbl_department`
  ADD PRIMARY KEY (`dept_id`),
  ADD UNIQUE KEY `dept_code` (`dept_code`);

--
-- Indexes for table `tbl_enrollment`
--
ALTER TABLE `tbl_enrollment`
  ADD PRIMARY KEY (`enrollment_id`),
  ADD KEY `student_id_fk` (`student_id`),
  ADD KEY `section_id_fk` (`section_id`);

--
-- Indexes for table `tbl_instructor`
--
ALTER TABLE `tbl_instructor`
  ADD PRIMARY KEY (`instructor_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `dept_id_fk2` (`dept_id`);

--
-- Indexes for table `tbl_program`
--
ALTER TABLE `tbl_program`
  ADD PRIMARY KEY (`program_id`),
  ADD UNIQUE KEY `program_code` (`program_code`),
  ADD KEY `dept_id_fk` (`dept_id`);

--
-- Indexes for table `tbl_room`
--
ALTER TABLE `tbl_room`
  ADD PRIMARY KEY (`room_id`),
  ADD UNIQUE KEY `room_code` (`room_code`);

--
-- Indexes for table `tbl_section`
--
ALTER TABLE `tbl_section`
  ADD PRIMARY KEY (`section_id`),
  ADD KEY `course_id_fk` (`course_id`),
  ADD KEY `term_id_fk` (`term_id`),
  ADD KEY `instructor_id_fk` (`instructor_id`),
  ADD KEY `room_id_fk` (`room_id`);

--
-- Indexes for table `tbl_student`
--
ALTER TABLE `tbl_student`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `student_no` (`student_no`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `program_id_fk` (`program_id`);

--
-- Indexes for table `tbl_term`
--
ALTER TABLE `tbl_term`
  ADD PRIMARY KEY (`term_id`),
  ADD UNIQUE KEY `term_code` (`term_code`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_course`
--
ALTER TABLE `tbl_course`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `tbl_department`
--
ALTER TABLE `tbl_department`
  MODIFY `dept_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `tbl_enrollment`
--
ALTER TABLE `tbl_enrollment`
  MODIFY `enrollment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `tbl_instructor`
--
ALTER TABLE `tbl_instructor`
  MODIFY `instructor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `tbl_program`
--
ALTER TABLE `tbl_program`
  MODIFY `program_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `tbl_room`
--
ALTER TABLE `tbl_room`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `tbl_section`
--
ALTER TABLE `tbl_section`
  MODIFY `section_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tbl_student`
--
ALTER TABLE `tbl_student`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123;

--
-- AUTO_INCREMENT for table `tbl_term`
--
ALTER TABLE `tbl_term`
  MODIFY `term_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_course`
--
ALTER TABLE `tbl_course`
  ADD CONSTRAINT `dept_id_fk3` FOREIGN KEY (`dept_id`) REFERENCES `tbl_department` (`dept_id`) ON UPDATE CASCADE;

--
-- Constraints for table `tbl_course_prerequisite`
--
ALTER TABLE `tbl_course_prerequisite`
  ADD CONSTRAINT `course_id_fk2` FOREIGN KEY (`course_id`) REFERENCES `tbl_course` (`course_id`),
  ADD CONSTRAINT `prereq_course_idfk` FOREIGN KEY (`prereq_course_id`) REFERENCES `tbl_course` (`course_id`);

--
-- Constraints for table `tbl_enrollment`
--
ALTER TABLE `tbl_enrollment`
  ADD CONSTRAINT `section_id_fk` FOREIGN KEY (`section_id`) REFERENCES `tbl_section` (`section_id`),
  ADD CONSTRAINT `student_id_fk` FOREIGN KEY (`student_id`) REFERENCES `tbl_student` (`student_id`);

--
-- Constraints for table `tbl_instructor`
--
ALTER TABLE `tbl_instructor`
  ADD CONSTRAINT `dept_id_fk2` FOREIGN KEY (`dept_id`) REFERENCES `tbl_department` (`dept_id`);

--
-- Constraints for table `tbl_program`
--
ALTER TABLE `tbl_program`
  ADD CONSTRAINT `dept_id_fk` FOREIGN KEY (`dept_id`) REFERENCES `tbl_department` (`dept_id`);

--
-- Constraints for table `tbl_section`
--
ALTER TABLE `tbl_section`
  ADD CONSTRAINT `course_id_fk` FOREIGN KEY (`course_id`) REFERENCES `tbl_course` (`course_id`),
  ADD CONSTRAINT `instructor_id_fk` FOREIGN KEY (`instructor_id`) REFERENCES `tbl_instructor` (`instructor_id`),
  ADD CONSTRAINT `room_id_fk` FOREIGN KEY (`room_id`) REFERENCES `tbl_room` (`room_id`),
  ADD CONSTRAINT `term_id_fk` FOREIGN KEY (`term_id`) REFERENCES `tbl_term` (`term_id`);

--
-- Constraints for table `tbl_student`
--
ALTER TABLE `tbl_student`
  ADD CONSTRAINT `program_id_fk` FOREIGN KEY (`program_id`) REFERENCES `tbl_program` (`program_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
