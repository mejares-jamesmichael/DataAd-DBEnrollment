# Frontend Integration Guide

This guide explains how to integrate the new frontend features (login, maintenance, prerequisite validation) into your existing index.html.

## Files Created

1. **login.html** - Complete login page (ready to use, no changes needed)
2. **auth-frontend.js** - Authentication and maintenance JavaScript functions
3. **maintenance-styles.css** - Additional CSS styles for new features
4. **html-additions.txt** - HTML snippets to add to index.html

---

## Integration Steps

### Step 1: Add New Script and CSS Files

Open **index.html** and update the `<head>` section to include the new files:

**Find this line:**
```html
<link rel="stylesheet" href="style.css">
```

**Add after it:**
```html
<link rel="stylesheet" href="maintenance-styles.css">
```

**Find this line (near the end of `<body>`):**
```html
<script src="script.js"></script>
```

**Add BEFORE it:**
```html
<script src="auth-frontend.js"></script>
```

**Final order should be:**
```html
<script src="auth-frontend.js"></script>
<script src="script.js"></script>
```

---

### Step 2: Add Maintenance Navigation Button

**Find the navigation section** (around line 24-34):
```html
<nav role="navigation" aria-label="Main navigation">
    <button onclick="showSection('departments')" class="active" data-section="departments">Departments</button>
    ...
    <button onclick="showSection('enrollments')" data-section="enrollments">Enrollments</button>
</nav>
```

**Add this button AFTER the enrollments button:**
```html
<button onclick="showSection('maintenance')" data-section="maintenance">Maintenance</button>
```

---

### Step 3: Add Maintenance Section

**Find the enrollments section** (around line 257-284). After the closing `</section>` tag of enrollments, **add this complete section:**

```html
<!-- Maintenance Section -->
<section id="maintenance" class="section" data-module="maintenance">
    <h2>Database Maintenance</h2>
    
    <!-- Database Statistics -->
    <div class="maintenance-card">
        <h3>Database Statistics</h3>
        <div id="dbStatsContainer">
            <p>Loading statistics...</p>
        </div>
    </div>
    
    <!-- Backup & Restore -->
    <div class="maintenance-card">
        <h3>Backup & Restore</h3>
        <div class="maintenance-actions">
            <button onclick="openBackupModal()" class="btn-primary">
                📥 Backup Database
            </button>
            <button onclick="openRestoreModal()" class="btn-warning">
                📤 Restore Database
            </button>
        </div>
        
        <h4>Backup History</h4>
        <table id="backupLogsTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Filename</th>
                    <th>Size</th>
                    <th>Date</th>
                    <th>Performed By</th>
                </tr>
            </thead>
            <tbody id="backupLogsBody">
                <tr><td colspan="5">Loading...</td></tr>
            </tbody>
        </table>
    </div>
    
    <!-- Database Optimization -->
    <div class="maintenance-card">
        <h3>Database Optimization</h3>
        <div class="maintenance-actions">
            <button onclick="optimizeTables()" class="btn-secondary">
                ⚡ Optimize Tables
            </button>
            <button onclick="archiveOldEnrollments()" class="btn-secondary">
                📦 Archive Old Enrollments
            </button>
        </div>
        <p class="help-text">
            <strong>Optimize Tables:</strong> Improves database performance by reorganizing data storage.<br>
            <strong>Archive Old Enrollments:</strong> Moves enrollments older than 2 years to archive table.
        </p>
    </div>
</section>
```

---

### Step 4: Update Enrollment Modal for Prerequisites

**Find the enrollment modal** (around line 484-510):

```html
<!-- Enrollment Modal -->
<div id="enrollModal" class="modal"...>
    <div class="modal-content">
        ...
        <form id="enrollForm" onsubmit="saveEnrollment(event)">
            <input type="hidden" id="enrollId">
            <label for="enrollStud">Student:</label>
            <select id="enrollStud" required></select>
            <label for="enrollSection">Section:</label>
            <select id="enrollSection" required></select>
            ...
```

**Make these changes:**

1. **Update student select:**
   ```html
   <!-- OLD: -->
   <select id="enrollStud" required></select>
   
   <!-- NEW: -->
   <select id="enrollStud" required onchange="checkEnrollmentPrerequisites()"></select>
   ```

2. **Update section select:**
   ```html
   <!-- OLD: -->
   <select id="enrollSection" required></select>
   
   <!-- NEW: -->
   <select id="enrollSection" required onchange="checkEnrollmentPrerequisites()"></select>
   ```

3. **Add prerequisite display container AFTER the section select:**
   ```html
   <select id="enrollSection" required onchange="checkEnrollmentPrerequisites()"></select>
   <div id="prerequisitesInfo" class="prerequisites-container"></div>
   <label for="enrollDate">Date Enrolled:</label>
   ```

---

### Step 5: Add Backup and Restore Modals

**Find the trash modal** (around line 512-526). **AFTER the trash modal's closing `</div>`, add these two modals:**

```html
<!-- Backup Modal -->
<div id="backupModal" class="modal" role="dialog" aria-labelledby="backupModalTitle" aria-modal="true">
    <div class="modal-content">
        <span class="close" onclick="closeBackupModal()" aria-label="Close">&times;</span>
        <h2 id="backupModalTitle">Backup Database</h2>
        <form id="backupForm" onsubmit="performBackup(event)">
            <label for="backupFilename">Backup Filename:</label>
            <input type="text" id="backupFilename" required>
            <p class="help-text">
                The backup file will be downloaded to your computer.
                Default filename includes current date and time.
            </p>
            <button type="submit" id="backupBtn">Download Backup</button>
            <button type="button" onclick="closeBackupModal()">Cancel</button>
        </form>
    </div>
</div>

<!-- Restore Modal -->
<div id="restoreModal" class="modal" role="dialog" aria-labelledby="restoreModalTitle" aria-modal="true">
    <div class="modal-content">
        <span class="close" onclick="closeRestoreModal()" aria-label="Close">&times;</span>
        <h2 id="restoreModalTitle">Restore Database</h2>
        <form id="restoreForm" onsubmit="performRestore(event)">
            <label for="restoreFile">Select Backup File (.sql):</label>
            <input type="file" id="restoreFile" accept=".sql" required>
            <div class="warning-box">
                <strong>⚠️ WARNING:</strong> This will overwrite all existing data in the database.
                Make sure you have a recent backup before proceeding.
            </div>
            <button type="submit" id="restoreBtn" class="btn-warning">Restore Database</button>
            <button type="button" onclick="closeRestoreModal()">Cancel</button>
        </form>
    </div>
</div>
```

---

### Step 6: Update script.js to Call Maintenance Load

Open **script.js** and find the `showSection()` function (should be near the top).

**Add this code inside the showSection function:**

```javascript
function showSection(sectionName) {
    // ... existing code ...
    
    // Add this at the end of the function:
    if (sectionName === 'maintenance') {
        loadMaintenance();
    }
}
```

---

## Verification Checklist

After making all changes, verify:

- [ ] login.html exists and is accessible
- [ ] auth-frontend.js is loaded in index.html
- [ ] maintenance-styles.css is loaded in index.html
- [ ] Maintenance navigation button is visible
- [ ] Maintenance section exists with all 3 cards
- [ ] Enrollment modal has onchange handlers on selects
- [ ] Prerequisites display container is in enrollment modal
- [ ] Backup modal exists
- [ ] Restore modal exists
- [ ] showSection() calls loadMaintenance()

---

## Testing the Integration

### 1. Test Login
1. Navigate to `http://localhost/DataAd-DBEnrollment/login.html`
2. Login with: username `admin`, password `admin123`
3. Should redirect to index.html

### 2. Test Authentication Check
1. Try accessing `http://localhost/DataAd-DBEnrollment/index.html` directly
2. If not logged in, should redirect to login.html
3. If logged in, should display main page with logout button

### 3. Test Maintenance Section
1. Click "Maintenance" tab
2. Should see database statistics
3. Click "Backup Database"
4. Modal should open with default filename
5. Click "Download Backup" - file should download
6. Check backup history table updates

### 4. Test Prerequisite Validation
1. Click "Enrollments" tab
2. Click "Add Enrollment"
3. Select a student
4. Select a section for a course WITH prerequisites (e.g., COMP 009)
5. Should see prerequisite check results
6. Try to enroll without satisfied prerequisites - should show error

### 5. Test Role-Based Access
1. Logout
2. Create a student or instructor user
3. Login with that user
4. Verify limited navigation/features based on role

---

## Common Issues & Solutions

### Issue: "Cannot read property 'style' of null"
**Solution:** Make sure all HTML elements with IDs exist before JavaScript tries to access them.

### Issue: Login redirects in a loop
**Solution:** Check that auth-frontend.js is loaded BEFORE script.js.

### Issue: Prerequisite check not showing
**Solution:** Verify onchange handlers are added to both student and section selects.

### Issue: Backup downloads empty file
**Solution:** Check PHP error logs, ensure maintenance.php has write permissions to backups/ directory.

### Issue: Styles look broken
**Solution:** Verify maintenance-styles.css is loaded after style.css in the HTML.

---

## File Structure After Integration

```
DataAd-DBEnrollment/
├── auth.php                              # Backend: Authentication
├── maintenance.php                       # Backend: Database maintenance
├── enrollments.php                       # Backend: Enrollments with prereq validation
├── auth-frontend.js                      # Frontend: Auth & maintenance JS (NEW)
├── login.html                            # Frontend: Login page (NEW)
├── index.html                            # Frontend: Main app (MODIFIED)
├── script.js                             # Frontend: Existing JS (MODIFY SLIGHTLY)
├── style.css                             # Frontend: Existing styles
├── maintenance-styles.css                # Frontend: New styles (NEW)
├── migration_user_roles_and_maintenance.sql  # Database migration
├── backups/                              # Backup files directory (auto-created)
└── [other existing files...]
```

---

## Summary of Changes

**New Files Created:**
- ✅ login.html (complete, ready to use)
- ✅ auth-frontend.js (authentication & maintenance JavaScript)
- ✅ maintenance-styles.css (styles for new features)

**Files to Modify:**
- ⚠️ index.html (add maintenance section, modals, update enrollment modal)
- ⚠️ script.js (add loadMaintenance() call in showSection())

**No Changes Needed:**
- ✅ config.php, courses.php, students.php, etc. (work as-is)
- ✅ style.css (keep existing, just add new CSS file)

---

## Next Steps After Integration

1. **Test all features** using the testing checklist above
2. **Create additional user accounts** for different roles
3. **Perform a test backup** to verify functionality
4. **Change default admin password** immediately!
5. **Review IMPLEMENTATION_GUIDE.md** for complete feature documentation

---

## Need Help?

If you encounter any issues during integration:

1. Check browser console for JavaScript errors (F12)
2. Check PHP error logs for backend errors
3. Verify all file paths are correct
4. Ensure database migration was successful
5. Test with default admin credentials first

---

**Integration Date:** December 30, 2025
**Version:** 2.0 Frontend
**Status:** Ready for Integration
