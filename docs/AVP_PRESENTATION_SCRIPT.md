# AVP PRESENTATION SCRIPT
## Database Enrollment Management System - SQL Statements Documentation

---

## INTRODUCTION (0:00 - 0:30)

**[SLIDE 1: Title Slide]**

Good day! I'm presenting the **Database Enrollment Management System** - a comprehensive web-based application for managing university data including students, courses, instructors, and enrollments built using **PHP, MySQL, JavaScript, HTML, and CSS**, running on XAMPP with the database **dbenrollment**.

---

## SYSTEM OVERVIEW (0:30 - 0:50)

**[SLIDE 2: System Features]**

The system manages **9 core modules**: Departments, Programs, Instructors, Students, Courses, Terms, Rooms, Sections, and Enrollments. Each module implements full **CRUD operations** with search, sort, soft delete, and Excel export features.

---

## DATABASE ARCHITECTURE (0:50 - 1:10)

**[SLIDE 3: Database Connection & Schema]**

**Database Connection (config.php):**
```php
$conn = new mysqli('localhost', 'root', '', 'dbenrollment');
$conn->set_charset("utf8mb4");
```

The database has **10 tables**: 9 main tables plus tblCoursePrerequisites for many-to-many relationships. All tables use **soft delete** with the `deleted_at` timestamp field. The system uses **MySQLi prepared statements** for SQL injection prevention.

---

## CORE CRUD OPERATIONS (1:10 - 2:30)

**[SLIDE 4: Standard CRUD Pattern - All Modules]**

All 9 modules follow the same secure pattern using **prepared statements**:

**CREATE Example (Departments):**
```sql
INSERT INTO tblDepartments (dept_code, dept_name) VALUES (?, ?)
```

**READ Example with JOIN (Students):**
```sql
SELECT s.*, p.program_code, p.program_name
FROM tblStudents s
LEFT JOIN tblPrograms p ON s.program_id = p.program_id
WHERE s.deleted_at IS NULL
```

**UPDATE Example:**
```sql
UPDATE tblDepartments SET dept_code = ?, dept_name = ? WHERE dept_id = ?
```

**SOFT DELETE Example:**
```sql
UPDATE tblDepartments SET deleted_at = NOW() WHERE dept_id = ?
```

---

## ADVANCED SQL FEATURES (2:30 - 3:30)

**[SLIDE 5: Complex Queries]**

**1. Courses with GROUP_CONCAT (Aggregating Prerequisites):**
```sql
SELECT c.*, d.dept_name,
  GROUP_CONCAT(pc.course_code SEPARATOR ', ') as prerequisites
FROM tblCourses c
LEFT JOIN tblDepartments d ON c.dept_id = d.dept_id
LEFT JOIN tblCoursePrerequisites cp ON c.course_id = cp.course_id
LEFT JOIN tblCourses pc ON cp.prereq_course_id = pc.course_id
WHERE c.deleted_at IS NULL
GROUP BY c.course_id
```

**2. Sections with Multiple JOINs & CONCAT:**
```sql
SELECT s.*, c.course_code, t.term_code,
  CONCAT(i.first_name, ' ', i.last_name) as instructor_name,
  r.room_code, r.building
FROM tblSections s
LEFT JOIN tblCourses c ON s.course_id = c.course_id
LEFT JOIN tblTerms t ON s.term_id = t.term_id
LEFT JOIN tblInstructors i ON s.instructor_id = i.instructor_id
LEFT JOIN tblRooms r ON s.room_id = r.room_id
WHERE s.deleted_at IS NULL
```

This query joins **5 tables** to display complete section information.

**3. Enrollments - Most Complex Query:**
```sql
SELECT e.*, 
  CONCAT(st.first_name, ' ', st.last_name) as student_name,
  st.student_no, c.course_code, sec.section_code, t.term_code
FROM tblEnrollments e
LEFT JOIN tblStudents st ON e.student_id = st.student_id
LEFT JOIN tblSections sec ON e.section_id = sec.section_id
LEFT JOIN tblCourses c ON sec.course_id = c.course_id
LEFT JOIN tblTerms t ON sec.term_id = t.term_id
WHERE e.deleted_at IS NULL
```

---

## SECURITY & FEATURES (3:30 - 4:30)

**[SLIDE 6: Security Implementation]**

**PHP Prepared Statement Pattern:**
```php
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $dept_code, $dept_name);
$stmt->execute();
```

**Search Functionality with LIKE:**
```sql
WHERE (student_no LIKE ? OR first_name LIKE ? OR email LIKE ?)
AND deleted_at IS NULL
```

**Key Features:**
- **57+ SQL Statements** total across all modules
- **Soft Delete** - preserves data with timestamp
- **LEFT JOIN** - combines related data from multiple tables
- **GROUP_CONCAT** - aggregates many-to-many relationships
- **CONCAT** - combines first and last names
- **Dynamic Sorting** - ORDER BY ASC/DESC
- **Foreign Keys** - maintains data integrity with CASCADE

---

## CONCLUSION (4:30 - 5:00)

**[SLIDE 7: System Summary]**

The **Database Enrollment Management System** successfully implements:

✓ **9 Modules** with full CRUD operations
✓ **10 Database Tables** with proper relationships
✓ **Prepared Statements** for SQL injection prevention
✓ **Advanced SQL**: JOINs, GROUP_CONCAT, CONCAT, LIKE
✓ **Soft Delete** for data recovery
✓ **Search & Sort** capabilities
✓ **RESTful API** pattern with JSON responses

**Technologies:**
- PHP + MySQLi
- MySQL Database
- JavaScript + AJAX
- HTML5 + CSS3

Thank you for watching this presentation!

---

## VIDEO PRODUCTION NOTES

**Total Duration:** 5 minutes

**Suggested Sections:**
- Introduction & Overview (0:00 - 1:10)
- Core CRUD Operations (1:10 - 2:30)
- Advanced SQL Features (2:30 - 3:30)
- Security & Features (3:30 - 4:30)
- Conclusion (4:30 - 5:00)

**Recording Tips:**
1. Show code snippets alongside explanations
2. Demonstrate 1-2 live CRUD operations
3. Highlight SQL statements in phpMyAdmin
4. Show the web interface briefly

**Before Recording:**
1. Populate database with sample data
2. Test all features
3. Have code files open in editor
4. Have phpMyAdmin ready

---

## END OF SCRIPT
