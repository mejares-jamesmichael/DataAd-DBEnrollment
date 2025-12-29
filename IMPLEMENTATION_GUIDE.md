# Implementation Guide - User Roles, Permissions & Database Maintenance

## Overview
This document provides instructions for implementing the new features added to the Enrollment Management System.

## New Features Implemented

### 1. User Role & Permission System
- **Database Table:** `tbl_users`
- **Roles:** Admin, Registrar, Instructor, Student
- **Features:**
  - Secure password hashing (bcrypt)
  - Session-based authentication
  - Role-based access control
  - Activity logging

### 2. Database Maintenance Module
- **Backup with Custom Filename:** Users can specify filename with default date format
- **Restore:** Upload and restore SQL backup files
- **Optimize Tables:** Database optimization
- **Archive Old Enrollments:** Move old data to archive table
- **Database Statistics:** View table sizes and record counts

### 3. Course Prerequisite Validation
- **Automatic Validation:** Blocks enrollment if prerequisites not met
- **Prerequisite Checking:** API endpoint to check prerequisite status
- **Clear Error Messages:** Shows which prerequisites are missing

---

## Installation Steps

### Step 1: Run Database Migration

1. Open phpMyAdmin at `http://localhost/phpmyadmin`
2. Select the `dbenrollment` database
3. Click on the "Import" tab
4. Choose the file: `migration_user_roles_and_maintenance.sql`
5. Click "Go" to execute the migration

**What this does:**
- Creates `tbl_users` (user accounts)
- Creates `tbl_backup_logs` (backup history)
- Creates `tbl_archived_enrollments` (old data archive)
- Creates `tbl_activity_logs` (audit trail)
- Creates database views for prerequisite checking
- Inserts default admin account

### Step 2: Verify New Tables

After migration, verify these tables exist:
- ✅ `tbl_users`
- ✅ `tbl_backup_logs`
- ✅ `tbl_archived_enrollments`
- ✅ `tbl_activity_logs`

### Step 3: Test Default Admin Login

**Default Credentials:**
- Username: `admin`
- Password: `admin123`

⚠️ **IMPORTANT:** Change the admin password immediately after first login!

---

## New PHP Files Created

### 1. auth.php
**Purpose:** Handles authentication and authorization

**Available Actions:**
- `login` - User login
- `logout` - User logout
- `checkSession` - Verify active session
- `getCurrentUser` - Get logged-in user info
- `changePassword` - Change user password

**Usage Example:**
```javascript
// Login
fetch('auth.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: 'action=login&username=admin&password=admin123'
})
.then(res => res.json())
.then(data => {
    if (data.success) {
        console.log('Logged in:', data.data);
    }
});
```

### 2. maintenance.php
**Purpose:** Database backup, restore, and maintenance operations

**Available Actions:**
- `backup` - Create database backup with custom filename
- `restore` - Restore from SQL file upload
- `getBackupLogs` - View backup history
- `optimizeTables` - Optimize all tables
- `archiveOldEnrollments` - Archive old data
- `getDatabaseStats` - Get database statistics

**Usage Example:**
```javascript
// Backup with custom filename
fetch('maintenance.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: 'action=backup&filename=my_backup_2025-12-30.sql'
})
.then(response => {
    // File will be downloaded automatically
});
```

### 3. enrollments.php (Updated)
**Purpose:** Enrollment CRUD with prerequisite validation

**New Actions Added:**
- `checkPrerequisites` - Check if student meets prerequisites
- `getPrerequisites` - Get all prerequisites for a course

**Prerequisite Validation:**
- Automatically checks prerequisites when creating enrollment
- Blocks enrollment if prerequisites not satisfied
- Returns clear error message with missing courses

**Usage Example:**
```javascript
// Check prerequisites before enrolling
fetch('enrollments.php?action=checkPrerequisites&student_id=1&section_id=5')
.then(res => res.json())
.then(data => {
    if (data.data.can_enroll) {
        console.log('Student can enroll');
    } else {
        console.log('Missing prerequisites:', data.data.prerequisites);
    }
});
```

---

## User Roles & Permissions

### Admin
**Full System Access**
- ✅ View all data
- ✅ Create/Edit/Delete all records
- ✅ Manage users
- ✅ Database backup/restore
- ✅ View activity logs
- ✅ System maintenance

### Registrar
**Data Management**
- ✅ View all data
- ✅ Create/Edit/Delete all records
- ✅ Database backup/restore
- ❌ Manage users
- ❌ View activity logs

### Instructor
**Limited Access**
- ✅ View all data
- ✅ View own sections
- ✅ Manage grades for own sections
- ❌ Create/Edit/Delete records
- ❌ Database operations

### Student
**Personal Data Only**
- ✅ View own enrollments
- ✅ Enroll in courses (with prerequisite check)
- ❌ View other students
- ❌ Modify any data

---

## Prerequisite Validation Logic

### Passing Grades
The system recognizes these grades as passing:
- Letter grades: `P`, `A`, `B`, `C`
- Numeric grades: `1.0`, `1.25`, `1.5`, `1.75`, `2.0`, `2.25`, `2.5`, `2.75`, `3.0`

### Validation Process
1. Student attempts to enroll in a section
2. System checks course prerequisites from `tbl_course_prerequisite`
3. For each prerequisite, system verifies:
   - Student has enrollment record for that course
   - Letter grade is in passing grades list
   - Enrollment status is "enrolled" or "completed"
4. If ANY prerequisite is not satisfied:
   - Enrollment is **blocked**
   - Error message shows missing courses
5. If ALL prerequisites are satisfied:
   - Enrollment proceeds normally

### Example Scenario

**Course:** COMP 009 (Object Oriented Programming)
**Prerequisites:** COMP 002 (Computer Programming 1)

| Student | Has COMP 002? | Grade | Can Enroll? |
|---------|---------------|-------|-------------|
| Student A | ✅ Yes | P (Passed) | ✅ Yes |
| Student B | ✅ Yes | F (Failed) | ❌ No |
| Student C | ❌ No | - | ❌ No |

---

## Database Backup Feature

### Backup Process

1. **User clicks "Backup Database"**
2. **Modal appears with:**
   - Default filename: `dbenrollment_backup_2025-12-30_143022.sql`
   - Editable text input
   - "Download Backup" button

3. **System generates backup:**
   - Exports all tables with structure and data
   - Creates SQL file
   - Logs backup to `tbl_backup_logs`
   - Downloads file to user's computer

4. **Backup files saved in:** `backups/` directory

### Restore Process

1. **User clicks "Restore Database"**
2. **File upload modal appears**
3. **User selects .sql file**
4. **System validates:**
   - File is .sql format
   - File size under 50MB
5. **Confirmation dialog:**
   - "This will overwrite existing data. Continue?"
6. **System executes restore:**
   - Runs SQL statements
   - Logs activity
   - Shows success message

---

## Security Features

### Password Security
- Passwords hashed with PHP `password_hash()` (bcrypt)
- Never stored in plain text
- Minimum 6 characters required

### Session Management
- PHP sessions track logged-in users
- Session timeout on inactivity
- Secure session variables

### SQL Injection Prevention
- All queries use prepared statements
- Input sanitization with `real_escape_string()`
- Parameter binding for user input

### Access Control
- Every maintenance operation checks user role
- `requireRole()` function blocks unauthorized access
- Activity logging tracks all actions

---

## Activity Logging

All significant actions are logged in `tbl_activity_logs`:

**Logged Actions:**
- User login/logout
- Failed login attempts
- Database backup/restore
- Data creation/update/delete
- Password changes
- Archive operations

**Log Information:**
- User ID
- Action type
- Entity type and ID
- Description
- IP address
- Timestamp

---

## Database Views Created

### vw_course_prerequisites
Shows course-prerequisite relationships
```sql
SELECT * FROM vw_course_prerequisites WHERE course_code = 'COMP 009';
```

### vw_student_completed_courses
Shows courses each student has passed
```sql
SELECT * FROM vw_student_completed_courses WHERE student_id = 1 AND is_passed = 1;
```

---

## Testing Checklist

### Authentication Tests
- [ ] Login with admin credentials
- [ ] Login with wrong password (should fail)
- [ ] Logout
- [ ] Check session persistence
- [ ] Change password

### Backup/Restore Tests
- [ ] Backup with default filename
- [ ] Backup with custom filename
- [ ] View backup logs
- [ ] Restore from backup file
- [ ] Optimize tables

### Prerequisite Validation Tests
- [ ] Enroll student in course WITHOUT prerequisites (should succeed)
- [ ] Enroll student in course WITH satisfied prerequisites (should succeed)
- [ ] Enroll student in course WITH unsatisfied prerequisites (should fail with error)
- [ ] Check prerequisite status endpoint

### Role-Based Access Tests
- [ ] Admin can access maintenance (should work)
- [ ] Registrar can access maintenance (should work)
- [ ] Instructor cannot access maintenance (should fail)
- [ ] Student cannot access maintenance (should fail)

---

## Next Steps (Frontend Integration)

The backend is complete. Next steps require frontend work:

1. **Create Login Page**
   - Login form UI
   - Session check on page load
   - Redirect to login if not authenticated

2. **Update Navigation**
   - Show/hide tabs based on user role
   - Add "Logout" button
   - Display logged-in user name

3. **Add Maintenance Section**
   - Backup modal with filename input
   - Restore modal with file upload
   - Backup logs table
   - Database statistics display

4. **Update Enrollment Form**
   - Show prerequisites when selecting section
   - Display prerequisite status (✓ Satisfied / ✗ Missing)
   - Show validation errors clearly

5. **Add Permission Checks**
   - Disable buttons based on user role
   - Hide features user cannot access

---

## File Structure

```
DataAd-DBEnrollment/
├── auth.php                              # NEW: Authentication system
├── maintenance.php                       # NEW: Database maintenance
├── enrollments.php                       # UPDATED: Added prerequisite validation
├── migration_user_roles_and_maintenance.sql  # NEW: Database migration
├── backups/                              # NEW: Backup files directory (auto-created)
├── config.php                            # Existing
├── [other existing files...]
```

---

## Troubleshooting

### Issue: "Table tbl_users doesn't exist"
**Solution:** Run the migration SQL file in phpMyAdmin

### Issue: "Call to undefined function password_verify()"
**Solution:** Ensure PHP 5.5+ is installed (XAMPP 8.2.12 is fine)

### Issue: "Permission denied" when creating backup
**Solution:** Create `backups/` directory manually with write permissions

### Issue: "Headers already sent" error
**Solution:** Ensure no whitespace before `<?php` in PHP files

### Issue: Backup file not downloading
**Solution:** Check browser download settings, ensure popup blocker is off

---

## Default Admin Account

**DO NOT FORGET TO CHANGE THIS PASSWORD!**

```
Username: admin
Password: admin123
Email: admin@pup.edu.ph
Role: admin
```

To change password:
1. Login as admin
2. Use `changePassword` action in auth.php
3. Or update directly in database with hashed password

---

## Support & Documentation

For questions or issues:
1. Check this guide
2. Review PHP error logs
3. Check browser console for JavaScript errors
4. Review `tbl_activity_logs` for audit trail

---

## Summary

✅ **Completed:**
- User authentication system
- Role-based permissions
- Database backup with custom filenames
- Database restore functionality
- Course prerequisite validation
- Activity logging
- Database maintenance tools

🔄 **Pending (Frontend):**
- Login UI
- Maintenance UI section
- Prerequisite display in enrollment form
- Role-based navigation visibility

---

**Implementation Date:** December 30, 2025
**Version:** 2.0
**Status:** Backend Complete - Frontend Integration Required
