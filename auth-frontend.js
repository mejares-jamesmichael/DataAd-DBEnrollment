/**
 * Authentication and Maintenance Frontend Module
 * Handles login checks, user sessions, database maintenance UI
 */

// Global user object
let currentUser = null;

// ========================================
// AUTHENTICATION FUNCTIONS
// ========================================

/**
 * Check if user is logged in on page load
 */
function checkAuth() {
    fetch('auth.php?action=checkSession')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                currentUser = data.data;
                initializeApp();
            } else {
                // Not logged in, redirect to login page
                window.location.href = 'login.html';
            }
        })
        .catch(err => {
            console.error('Auth check error:', err);
            window.location.href = 'login.html';
        });
}

/**
 * Initialize app after successful authentication
 */
function initializeApp() {
    // Update header with user info
    updateUserInterface();
    
    // Load initial data
    loadDepartments();
    
    // Apply role-based permissions
    applyPermissions();
}

/**
 * Update UI with logged-in user information
 */
function updateUserInterface() {
    // Update header to show logged-in user
    const header = document.querySelector('header p');
    if (header && currentUser) {
        header.innerHTML = `Database: dbenrollment | Logged in as: <strong>${currentUser.full_name}</strong> (${currentUser.role})`;
    }
    
    // Add logout button to header if not exists
    if (!document.getElementById('logoutBtn')) {
        const header = document.querySelector('header');
        const logoutBtn = document.createElement('button');
        logoutBtn.id = 'logoutBtn';
        logoutBtn.textContent = 'Logout';
        logoutBtn.className = 'logout-btn';
        logoutBtn.onclick = logout;
        header.appendChild(logoutBtn);
    }
}

/**
 * Apply role-based permissions
 */
function applyPermissions() {
    if (!currentUser) return;
    
    const role = currentUser.role;
    const nav = document.querySelector('nav');
    
    // Show/hide navigation tabs based on role
    if (role === 'student') {
        // Students only see enrollments
        const buttons = nav.querySelectorAll('button');
        buttons.forEach(btn => {
            if (btn.getAttribute('data-section') !== 'enrollments') {
                btn.style.display = 'none';
            }
        });
        
        // Show only enrollments section
        showSection('enrollments');
    } else if (role === 'instructor') {
        // Instructors see all but can't edit most things
        // Hide maintenance for instructors
        const maintenanceBtn = document.querySelector('[data-section="maintenance"]');
        if (maintenanceBtn) maintenanceBtn.style.display = 'none';
    }
    
    // Hide/disable controls based on permissions
    if (role === 'instructor' || role === 'student') {
        // Disable add/edit/delete buttons
        document.querySelectorAll('.controls button:not(.excel-export)').forEach(btn => {
            if (!btn.onclick || btn.onclick.toString().includes('export') || 
                btn.onclick.toString().includes('print') || 
                btn.onclick.toString().includes('load')) {
                // Keep export, print, and load buttons enabled
            } else if (btn.textContent.includes('Add') || btn.textContent.includes('Trash')) {
                btn.style.display = 'none';
            }
        });
        
        // Hide action column buttons for students
        if (role === 'student') {
            document.querySelectorAll('.edit-btn, .delete-btn').forEach(btn => {
                btn.style.display = 'none';
            });
        }
    }
}

/**
 * Logout function
 */
function logout() {
    if (!confirm('Are you sure you want to logout?')) return;
    
    fetch('auth.php?action=logout', { method: 'POST' })
        .then(res => res.json())
        .then(data => {
            window.location.href = 'login.html';
        })
        .catch(err => {
            console.error('Logout error:', err);
            window.location.href = 'login.html';
        });
}

// ========================================
// DATABASE MAINTENANCE FUNCTIONS
// ========================================

/**
 * Load maintenance section
 */
function loadMaintenance() {
    if (!hasPermission('backup_restore')) {
        showAlert('Access denied. Insufficient permissions.', 'error');
        return;
    }
    
    // Load backup logs
    loadBackupLogs();
    
    // Load database stats
    loadDatabaseStats();
}

/**
 * Open backup modal with filename input
 */
function openBackupModal() {
    if (!hasPermission('backup_restore')) {
        showAlert('Access denied. Insufficient permissions.', 'error');
        return;
    }
    
    // Generate default filename
    const now = new Date();
    const dateStr = `${now.getFullYear()}-${String(now.getMonth()+1).padStart(2,'0')}-${String(now.getDate()).padStart(2,'0')}_${String(now.getHours()).padStart(2,'0')}${String(now.getMinutes()).padStart(2,'0')}${String(now.getSeconds()).padStart(2,'0')}`;
    const defaultFilename = `dbenrollment_backup_${dateStr}.sql`;
    
    document.getElementById('backupFilename').value = defaultFilename;
    document.getElementById('backupModal').style.display = 'block';
}

/**
 * Close backup modal
 */
function closeBackupModal() {
    document.getElementById('backupModal').style.display = 'none';
}

/**
 * Perform database backup
 */
function performBackup(event) {
    event.preventDefault();
    
    const filename = document.getElementById('backupFilename').value.trim();
    
    if (!filename) {
        showAlert('Please enter a filename', 'error');
        return;
    }
    
    // Show loading
    const backupBtn = document.getElementById('backupBtn');
    backupBtn.disabled = true;
    backupBtn.textContent = 'Creating backup...';
    
    // Create form data
    const formData = new FormData();
    formData.append('action', 'backup');
    formData.append('filename', filename);
    
    fetch('maintenance.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Backup failed');
        }
        return response.blob();
    })
    .then(blob => {
        // Download file
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
        
        showAlert('Backup created successfully!', 'success');
        closeBackupModal();
        
        // Reload backup logs
        loadBackupLogs();
    })
    .catch(err => {
        console.error('Backup error:', err);
        showAlert('Backup failed: ' + err.message, 'error');
    })
    .finally(() => {
        backupBtn.disabled = false;
        backupBtn.textContent = 'Download Backup';
    });
}

/**
 * Open restore modal
 */
function openRestoreModal() {
    if (!hasPermission('backup_restore')) {
        showAlert('Access denied. Insufficient permissions.', 'error');
        return;
    }
    
    document.getElementById('restoreModal').style.display = 'block';
}

/**
 * Close restore modal
 */
function closeRestoreModal() {
    document.getElementById('restoreModal').style.display = 'none';
    document.getElementById('restoreForm').reset();
}

/**
 * Perform database restore
 */
function performRestore(event) {
    event.preventDefault();
    
    const fileInput = document.getElementById('restoreFile');
    const file = fileInput.files[0];
    
    if (!file) {
        showAlert('Please select a backup file', 'error');
        return;
    }
    
    if (!file.name.endsWith('.sql')) {
        showAlert('Please select a valid .sql file', 'error');
        return;
    }
    
    if (!confirm('WARNING: This will overwrite all existing data. Are you sure you want to continue?')) {
        return;
    }
    
    // Show loading
    const restoreBtn = document.getElementById('restoreBtn');
    restoreBtn.disabled = true;
    restoreBtn.textContent = 'Restoring...';
    
    // Create form data
    const formData = new FormData();
    formData.append('action', 'restore');
    formData.append('backup_file', file);
    
    fetch('maintenance.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            closeRestoreModal();
            
            // Reload current section
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            showAlert(data.message, 'error');
        }
    })
    .catch(err => {
        console.error('Restore error:', err);
        showAlert('Restore failed: ' + err.message, 'error');
    })
    .finally(() => {
        restoreBtn.disabled = false;
        restoreBtn.textContent = 'Restore Database';
    });
}

/**
 * Optimize database tables
 */
function optimizeTables() {
    if (!confirm('This will optimize all database tables. Continue?')) {
        return;
    }
    
    fetch('maintenance.php?action=optimizeTables')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showAlert(data.message, 'success');
            } else {
                showAlert(data.message, 'error');
            }
        })
        .catch(err => {
            console.error('Optimize error:', err);
            showAlert('Optimization failed', 'error');
        });
}

/**
 * Archive old enrollments
 */
function archiveOldEnrollments() {
    if (!confirm('This will archive enrollments older than 2 years. Continue?')) {
        return;
    }
    
    fetch('maintenance.php?action=archiveOldEnrollments')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showAlert(`${data.message} (${data.data.count} records archived)`, 'success');
                loadEnrollments(); // Reload enrollments
            } else {
                showAlert(data.message, 'error');
            }
        })
        .catch(err => {
            console.error('Archive error:', err);
            showAlert('Archival failed', 'error');
        });
}

/**
 * Load backup logs
 */
function loadBackupLogs() {
    fetch('maintenance.php?action=getBackupLogs')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                displayBackupLogs(data.data);
            } else {
                console.error('Failed to load backup logs');
            }
        })
        .catch(err => {
            console.error('Error loading backup logs:', err);
        });
}

/**
 * Display backup logs in table
 */
function displayBackupLogs(logs) {
    const tbody = document.getElementById('backupLogsBody');
    
    if (!logs || logs.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5">No backup history</td></tr>';
        return;
    }
    
    tbody.innerHTML = logs.map(log => `
        <tr>
            <td>${log.backup_id}</td>
            <td>${log.filename}</td>
            <td>${log.file_size_mb || 'N/A'} MB</td>
            <td>${log.backup_date}</td>
            <td>${log.full_name || 'Unknown'}</td>
        </tr>
    `).join('');
}

/**
 * Load database statistics
 */
function loadDatabaseStats() {
    fetch('maintenance.php?action=getDatabaseStats')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                displayDatabaseStats(data.data);
            }
        })
        .catch(err => {
            console.error('Error loading stats:', err);
        });
}

/**
 * Display database statistics
 */
function displayDatabaseStats(stats) {
    const container = document.getElementById('dbStatsContainer');
    
    let html = '<div class="stats-grid">';
    
    // Record counts
    if (stats.record_counts) {
        html += '<div class="stat-card"><h4>Record Counts</h4>';
        for (let [table, count] of Object.entries(stats.record_counts)) {
            const displayName = table.replace('tbl_', '').replace('_', ' ');
            html += `<p>${displayName}: <strong>${count}</strong></p>`;
        }
        html += '</div>';
    }
    
    // Last backup
    if (stats.last_backup) {
        html += '<div class="stat-card"><h4>Last Backup</h4>';
        html += `<p>Date: <strong>${stats.last_backup.backup_date}</strong></p>`;
        html += `<p>File: <strong>${stats.last_backup.filename}</strong></p>`;
        html += '</div>';
    }
    
    html += '</div>';
    container.innerHTML = html;
}

// ========================================
// PREREQUISITE VALIDATION FUNCTIONS
// ========================================

/**
 * Check prerequisites when section is selected
 */
function checkEnrollmentPrerequisites() {
    const studentId = document.getElementById('enrollStud').value;
    const sectionId = document.getElementById('enrollSection').value;
    const prereqContainer = document.getElementById('prerequisitesInfo');
    
    if (!studentId || !sectionId) {
        prereqContainer.innerHTML = '';
        return;
    }
    
    prereqContainer.innerHTML = '<p>Checking prerequisites...</p>';
    
    fetch(`enrollments.php?action=checkPrerequisites&student_id=${studentId}&section_id=${sectionId}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                displayPrerequisites(data.data);
            } else {
                prereqContainer.innerHTML = `<p class="error">${data.message}</p>`;
            }
        })
        .catch(err => {
            console.error('Error checking prerequisites:', err);
            prereqContainer.innerHTML = '<p class="error">Error checking prerequisites</p>';
        });
}

/**
 * Display prerequisite status
 */
function displayPrerequisites(data) {
    const prereqContainer = document.getElementById('prerequisitesInfo');
    
    if (!data.prerequisites || data.prerequisites.length === 0) {
        prereqContainer.innerHTML = '<p class="success">✓ No prerequisites required</p>';
        return;
    }
    
    let html = '<div class="prereq-box">';
    html += `<h4>Prerequisites for ${data.course.course_code}:</h4>`;
    html += '<ul class="prereq-list">';
    
    data.prerequisites.forEach(prereq => {
        const icon = prereq.is_satisfied ? '✓' : '✗';
        const className = prereq.is_satisfied ? 'satisfied' : 'unsatisfied';
        const gradeInfo = prereq.grade ? ` (Grade: ${prereq.grade})` : '';
        
        html += `<li class="${className}">
            <span class="prereq-icon">${icon}</span>
            ${prereq.course_code} - ${prereq.course_title}${gradeInfo}
        </li>`;
    });
    
    html += '</ul>';
    
    if (data.can_enroll) {
        html += '<p class="success"><strong>✓ Student can enroll in this course</strong></p>';
    } else {
        html += '<p class="error"><strong>✗ Student cannot enroll - missing prerequisites</strong></p>';
    }
    
    html += '</div>';
    prereqContainer.innerHTML = html;
}

/**
 * Check if current user has a specific permission
 */
function hasPermission(permission) {
    if (!currentUser) return false;
    
    const role = currentUser.role;
    
    const permissions = {
        'admin': ['view_all', 'create_all', 'edit_all', 'delete_all', 'manage_users', 'backup_restore', 'view_logs'],
        'registrar': ['view_all', 'create_all', 'edit_all', 'delete_all', 'backup_restore'],
        'instructor': ['view_all', 'view_own_sections', 'edit_own_sections', 'manage_grades'],
        'student': ['view_own_enrollment', 'enroll_courses']
    };
    
    return permissions[role] && permissions[role].includes(permission);
}

// ========================================
// INITIALIZATION
// ========================================

// Check authentication when page loads
window.addEventListener('DOMContentLoaded', () => {
    // Only check auth if not on login page
    if (!window.location.href.includes('login.html')) {
        checkAuth();
    }
});
