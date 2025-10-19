# Enrollment Management System

A comprehensive local web application for managing university enrollments, courses, students, and more. Built with vanilla PHP, JavaScript, and MySQL, and designed to run on a local XAMPP server.

## Features

*   **Comprehensive Data Management:** Perform CRUD (Create, Read, Update, Delete) operations for all major university entities:
    *   Departments
    *   Programs
    *   Instructors
    *   Students
    *   Courses
    *   Terms
    *   Rooms
    *   Sections
    *   Enrollments
*   **Dynamic UI:** A single-page application experience with sections for each data module.
*   **Search and Sort:** Easily search through records and sort tables in ascending or descending order.
*   **Excel Export:** Export data from any table to an Excel file.
*   **Soft Deletes:** A "trash" feature allows for soft-deleting records, with the ability to restore or permanently delete them later.
*   **Print Functionality:** Print a clean, formatted version of any data table.

## Technology Stack

*   **Frontend:**
    *   HTML
    *   CSS
    *   JavaScript (vanilla)
*   **Backend:**
    *   PHP
*   **Database:**
    *   MySQL (as part of XAMPP)
*   **Server:**
    *   Apache (as part of XAMPP)

## Setup and Installation

To run this project locally, you will need to have XAMPP installed.

1.  **Download and Install XAMPP:**
    *   Download XAMPP from the [official website](https://www.apachefriends.org/index.html).
    *   Follow the installation instructions for your operating system.

2.  **Clone the Repository:**
    *   Clone this repository into the `htdocs` folder of your XAMPP installation.
    *   The path is usually `C:\xampp\htdocs` on Windows or `/Applications/XAMPP/htdocs` on macOS.

3.  **Start Apache and MySQL:**
    *   Open the XAMPP Control Panel.
    *   Start the Apache and MySQL modules.

4.  **Create the Database:**
    *   Open your web browser and navigate to `http://localhost/phpmyadmin`.
    *   Click on the "Databases" tab.
    *   Create a new database named `dbenrollment`.

5.  **Import the Database Schema:**
    *   Select the `dbenrollment` database in phpMyAdmin.
    *   Click on the "SQL" tab.
    *   You will need to create the tables. The schema is detailed in the "Database Schema" section below. You can copy and paste the SQL `CREATE TABLE` statements from there.

6.  **Access the Application:**
    *   Open your web browser and navigate to `http://localhost/<your-repo-name>`.

## Database Schema

Here are the `CREATE TABLE` statements for all the necessary tables in the `dbenrollment` database.

### `tblDepartments`

```sql
CREATE TABLE `tblDepartments` (
  `dept_id` int(11) NOT NULL AUTO_INCREMENT,
  `dept_code` varchar(20) NOT NULL,
  `dept_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`dept_id`),
  UNIQUE KEY `dept_code` (`dept_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### `tblPrograms`

```sql
CREATE TABLE `tblPrograms` (
  `program_id` int(11) NOT NULL AUTO_INCREMENT,
  `program_code` varchar(20) NOT NULL,
  `program_name` varchar(100) NOT NULL,
  `dept_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`program_id`),
  UNIQUE KEY `program_code` (`program_code`),
  KEY `dept_id` (`dept_id`),
  CONSTRAINT `tblprograms_ibfk_1` FOREIGN KEY (`dept_id`) REFERENCES `tbldepartments` (`dept_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### `tblInstructors`

```sql
CREATE TABLE `tblInstructors` (
  `instructor_id` int(11) NOT NULL AUTO_INCREMENT,
  `last_name` varchar(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `dept_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`instructor_id`),
  UNIQUE KEY `email` (`email`),
  KEY `dept_id` (`dept_id`),
  CONSTRAINT `tblinstructors_ibfk_1` FOREIGN KEY (`dept_id`) REFERENCES `tbldepartments` (`dept_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### `tblStudents`

```sql
CREATE TABLE `tblStudents` (
  `student_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_no` varchar(20) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `birthdate` date NOT NULL,
  `year_level` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`student_id`),
  UNIQUE KEY `student_no` (`student_no`),
  UNIQUE KEY `email` (`email`),
  KEY `program_id` (`program_id`),
  CONSTRAINT `tblstudents_ibfk_1` FOREIGN KEY (`program_id`) REFERENCES `tblprograms` (`program_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### `tblCourses`

```sql
CREATE TABLE `tblCourses` (
  `course_id` int(11) NOT NULL AUTO_INCREMENT,
  `course_code` varchar(20) NOT NULL,
  `course_title` varchar(100) NOT NULL,
  `units` int(11) NOT NULL,
  `lecture_hours` decimal(4,1) NOT NULL,
  `lab_hours` decimal(4,1) NOT NULL,
  `dept_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`course_id`),
  UNIQUE KEY `course_code` (`course_code`),
  KEY `dept_id` (`dept_id`),
  CONSTRAINT `tblcourses_ibfk_1` FOREIGN KEY (`dept_id`) REFERENCES `tbldepartments` (`dept_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### `tblTerms`

```sql
CREATE TABLE `tblTerms` (
  `term_id` int(11) NOT NULL AUTO_INCREMENT,
  `term_code` varchar(20) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`term_id`),
  UNIQUE KEY `term_code` (`term_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### `tblRooms`

```sql
CREATE TABLE `tblRooms` (
  `room_id` int(11) NOT NULL AUTO_INCREMENT,
  `building` varchar(50) NOT NULL,
  `room_code` varchar(20) NOT NULL,
  `capacity` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`room_id`),
  UNIQUE KEY `room_code` (`room_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### `tblSections`

```sql
CREATE TABLE `tblSections` (
  `section_id` int(11) NOT NULL AUTO_INCREMENT,
  `section_code` varchar(20) NOT NULL,
  `course_id` int(11) NOT NULL,
  `term_id` int(11) NOT NULL,
  `instructor_id` int(11) NOT NULL,
  `day_pattern` varchar(20) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `room_id` int(11) NOT NULL,
  `max_capacity` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`section_id`),
  UNIQUE KEY `section_code` (`section_code`),
  KEY `course_id` (`course_id`),
  KEY `term_id` (`term_id`),
  KEY `instructor_id` (`instructor_id`),
  KEY `room_id` (`room_id`),
  CONSTRAINT `tblsections_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `tblcourses` (`course_id`) ON DELETE CASCADE,
  CONSTRAINT `tblsections_ibfk_2` FOREIGN KEY (`term_id`) REFERENCES `tblterms` (`term_id`) ON DELETE CASCADE,
  CONSTRAINT `tblsections_ibfk_3` FOREIGN KEY (`instructor_id`) REFERENCES `tblinstructors` (`instructor_id`) ON DELETE CASCADE,
  CONSTRAINT `tblsections_ibfk_4` FOREIGN KEY (`room_id`) REFERENCES `tblrooms` (`room_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### `tblEnrollments`

```sql
CREATE TABLE `tblEnrollments` (
  `enrollment_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `date_enrolled` date NOT NULL,
  `status` enum('Enrolled','Dropped','Completed','Failed') NOT NULL,
  `letter_grade` varchar(2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`enrollment_id`),
  KEY `student_id` (`student_id`),
  KEY `section_id` (`section_id`),
  CONSTRAINT `tblenrollments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `tblstudents` (`student_id`) ON DELETE CASCADE,
  CONSTRAINT `tblenrollments_ibfk_2` FOREIGN KEY (`section_id`) REFERENCES `tblsections` (`section_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```
