// Section Management
function showSection(section) {
    document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('nav button').forEach(b => b.classList.remove('active'));
    document.getElementById(section).classList.add('active');
    event.target.classList.add('active');
    
    switch(section) {
        case 'departments': loadDepartments(); break;
        case 'programs': loadPrograms(); break;
        case 'instructors': loadInstructors(); break;
        case 'students': loadStudents(); break;
        case 'courses': loadCourses(); break;
        case 'terms': loadTerms(); break;
        case 'rooms': loadRooms(); break;
        case 'sections': loadSections(); break;
        case 'enrollments': loadEnrollments(); break;
    }
}

// Alert System
function showAlert(message, type = 'success') {
    const alert = document.getElementById('alert');
    alert.textContent = message;
    alert.className = `alert alert-${type} show`;
    setTimeout(() => alert.classList.remove('show'), 3000);
}

// ===========================================
// DEPARTMENTS MODULE
// ===========================================
function loadDepartments() {
    const search = document.getElementById('searchDept').value;
    fetch(`departments.php?action=read&search=${search}`)
        .then(res => res.json())
        .then(data => {
            const tbody = document.getElementById('deptTableBody');
            if (data.success && data.data.length > 0) {
                tbody.innerHTML = data.data.map(d => `
                    <tr>
                        <td>${d.dept_id}</td>
                        <td>${d.dept_code}</td>
                        <td>${d.dept_name}</td>
                        <td class="no-print">
                            <button onclick="editDept(${d.dept_id})">Edit</button>
                            <button onclick="deleteDept(${d.dept_id})">Delete</button>
                        </td>
                    </tr>
                `).join('');
            } else {
                tbody.innerHTML = '<tr><td colspan="4">No records found</td></tr>';
            }
        })
        .catch(err => showAlert('Error loading departments', 'error'));
}

function openDeptModal() {
    document.getElementById('deptModalTitle').textContent = 'Add Department';
    document.getElementById('deptForm').reset();
    document.getElementById('deptId').value = '';
    document.getElementById('deptModal').classList.add('active');
}

function closeDeptModal() {
    document.getElementById('deptModal').classList.remove('active');
}

function saveDepartment(e) {
    e.preventDefault();
    const formData = new FormData();
    const id = document.getElementById('deptId').value;
    
    formData.append('action', id ? 'update' : 'create');
    if (id) formData.append('dept_id', id);
    formData.append('dept_code', document.getElementById('deptCode').value);
    formData.append('dept_name', document.getElementById('deptName').value);

    fetch('departments.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            showAlert(data.message, data.success ? 'success' : 'error');
            if (data.success) {
                closeDeptModal();
                loadDepartments();
            }
        });
}

function editDept(id) {
    fetch(`departments.php?action=getOne&dept_id=${id}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('deptModalTitle').textContent = 'Edit Department';
                document.getElementById('deptId').value = data.data.dept_id;
                document.getElementById('deptCode').value = data.data.dept_code;
                document.getElementById('deptName').value = data.data.dept_name;
                document.getElementById('deptModal').classList.add('active');
            }
        });
}

function deleteDept(id) {
    if (!confirm('Delete this department?')) return;
    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('dept_id', id);

    fetch('departments.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            showAlert(data.message, data.success ? 'success' : 'error');
            if (data.success) loadDepartments();
        });
}

// ===========================================
// PROGRAMS MODULE
// ===========================================
function loadPrograms() {
    const search = document.getElementById('searchProg')?.value || '';
    fetch(`programs.php?action=read&search=${search}`)
        .then(res => res.json())
        .then(data => {
            const tbody = document.getElementById('progTableBody');
            if (data.success && data.data.length > 0) {
                tbody.innerHTML = data.data.map(p => `
                    <tr>
                        <td>${p.program_id}</td>
                        <td>${p.program_code}</td>
                        <td>${p.program_name}</td>
                        <td>${p.dept_name}</td>
                        <td class="no-print">
                            <button onclick="editProg(${p.program_id})">Edit</button>
                            <button onclick="deleteProg(${p.program_id})">Delete</button>
                        </td>
                    </tr>
                `).join('');
            } else {
                tbody.innerHTML = '<tr><td colspan="5">No records found</td></tr>';
            }
        })
        .catch(err => showAlert('Error loading programs', 'error'));
}

function openProgModal() {
    fetch('departments.php?action=read')
        .then(res => res.json())
        .then(data => {
            const select = document.getElementById('progDept');
            select.innerHTML = '<option value="">Select Department</option>' +
                data.data.map(d => `<option value="${d.dept_id}">${d.dept_name}</option>`).join('');
        });
    
    document.getElementById('progModalTitle').textContent = 'Add Program';
    document.getElementById('progForm').reset();
    document.getElementById('progId').value = '';
    document.getElementById('progModal').classList.add('active');
}

function closeProgModal() {
    document.getElementById('progModal').classList.remove('active');
}

function saveProgram(e) {
    e.preventDefault();
    const formData = new FormData();
    const id = document.getElementById('progId').value;
    
    formData.append('action', id ? 'update' : 'create');
    if (id) formData.append('program_id', id);
    formData.append('program_code', document.getElementById('progCode').value);
    formData.append('program_name', document.getElementById('progName').value);
    formData.append('dept_id', document.getElementById('progDept').value);

    fetch('programs.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            showAlert(data.message, data.success ? 'success' : 'error');
            if (data.success) {
                closeProgModal();
                loadPrograms();
            }
        });
}

function editProg(id) {
    fetch(`programs.php?action=getOne&program_id=${id}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                openProgModal();
                setTimeout(() => {
                    document.getElementById('progModalTitle').textContent = 'Edit Program';
                    document.getElementById('progId').value = data.data.program_id;
                    document.getElementById('progCode').value = data.data.program_code;
                    document.getElementById('progName').value = data.data.program_name;
                    document.getElementById('progDept').value = data.data.dept_id;
                }, 100);
            }
        });
}

function deleteProg(id) {
    if (!confirm('Delete this program?')) return;
    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('program_id', id);

    fetch('programs.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            showAlert(data.message, data.success ? 'success' : 'error');
            if (data.success) loadPrograms();
        });
}

// ===========================================
// INSTRUCTORS MODULE
// ===========================================
function loadInstructors() {
    const search = document.getElementById('searchInst')?.value || '';
    fetch(`instructors.php?action=read&search=${search}`)
        .then(res => res.json())
        .then(data => {
            const tbody = document.getElementById('instTableBody');
            if (data.success && data.data.length > 0) {
                tbody.innerHTML = data.data.map(i => `
                    <tr>
                        <td>${i.instructor_id}</td>
                        <td>${i.first_name} ${i.last_name}</td>
                        <td>${i.email}</td>
                        <td>${i.dept_name}</td>
                        <td class="no-print">
                            <button onclick="editInst(${i.instructor_id})">Edit</button>
                            <button onclick="deleteInst(${i.instructor_id})">Delete</button>
                        </td>
                    </tr>
                `).join('');
            } else {
                tbody.innerHTML = '<tr><td colspan="5">No records found</td></tr>';
            }
        })
        .catch(err => showAlert('Error loading instructors', 'error'));
}

function openInstModal() {
    fetch('departments.php?action=read')
        .then(res => res.json())
        .then(data => {
            const select = document.getElementById('instDept');
            select.innerHTML = '<option value="">Select Department</option>' +
                data.data.map(d => `<option value="${d.dept_id}">${d.dept_name}</option>`).join('');
        });
    
    document.getElementById('instModalTitle').textContent = 'Add Instructor';
    document.getElementById('instForm').reset();
    document.getElementById('instId').value = '';
    document.getElementById('instModal').classList.add('active');
}

function closeInstModal() {
    document.getElementById('instModal').classList.remove('active');
}

function saveInstructor(e) {
    e.preventDefault();
    const formData = new FormData();
    const id = document.getElementById('instId').value;
    
    formData.append('action', id ? 'update' : 'create');
    if (id) formData.append('instructor_id', id);
    formData.append('first_name', document.getElementById('instFirst').value);
    formData.append('last_name', document.getElementById('instLast').value);
    formData.append('email', document.getElementById('instEmail').value);
    formData.append('dept_id', document.getElementById('instDept').value);

    fetch('instructors.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            showAlert(data.message, data.success ? 'success' : 'error');
            if (data.success) {
                closeInstModal();
                loadInstructors();
            }
        });
}

function editInst(id) {
    fetch(`instructors.php?action=getOne&instructor_id=${id}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                openInstModal();
                setTimeout(() => {
                    document.getElementById('instModalTitle').textContent = 'Edit Instructor';
                    document.getElementById('instId').value = data.data.instructor_id;
                    document.getElementById('instFirst').value = data.data.first_name;
                    document.getElementById('instLast').value = data.data.last_name;
                    document.getElementById('instEmail').value = data.data.email;
                    document.getElementById('instDept').value = data.data.dept_id;
                }, 100);
            }
        });
}

function deleteInst(id) {
    if (!confirm('Delete this instructor?')) return;
    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('instructor_id', id);

    fetch('instructors.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            showAlert(data.message, data.success ? 'success' : 'error');
            if (data.success) loadInstructors();
        });
}

// ===========================================
// STUDENTS MODULE
// ===========================================
function loadStudents() {
    const search = document.getElementById('searchStud')?.value || '';
    fetch(`students.php?action=read&search=${search}`)
        .then(res => res.json())
        .then(data => {
            const tbody = document.getElementById('studTableBody');
            if (data.success && data.data.length > 0) {
                tbody.innerHTML = data.data.map(s => `
                    <tr>
                        <td>${s.student_id}</td>
                        <td>${s.student_no}</td>
                        <td>${s.first_name} ${s.last_name}</td>
                        <td>${s.email}</td>
                        <td>${s.program_code}</td>
                        <td>${s.year_level}</td>
                        <td class="no-print">
                            <button onclick="editStud(${s.student_id})">Edit</button>
                            <button onclick="deleteStud(${s.student_id})">Delete</button>
                        </td>
                    </tr>
                `).join('');
            } else {
                tbody.innerHTML = '<tr><td colspan="7">No records found</td></tr>';
            }
        })
        .catch(err => showAlert('Error loading students', 'error'));
}

function openStudModal() {
    fetch('programs.php?action=read')
        .then(res => res.json())
        .then(data => {
            const select = document.getElementById('studProg');
            select.innerHTML = '<option value="">Select Program</option>' +
                data.data.map(p => `<option value="${p.program_id}">${p.program_name}</option>`).join('');
        });
    
    document.getElementById('studModalTitle').textContent = 'Add Student';
    document.getElementById('studForm').reset();
    document.getElementById('studId').value = '';
    document.getElementById('studModal').classList.add('active');
}

function closeStudModal() {
    document.getElementById('studModal').classList.remove('active');
}

function saveStudent(e) {
    e.preventDefault();
    const formData = new FormData();
    const id = document.getElementById('studId').value;
    
    formData.append('action', id ? 'update' : 'create');
    if (id) formData.append('student_id', id);
    formData.append('student_no', document.getElementById('studNo').value);
    formData.append('first_name', document.getElementById('studFirst').value);
    formData.append('last_name', document.getElementById('studLast').value);
    formData.append('email', document.getElementById('studEmail').value);
    formData.append('gender', document.getElementById('studGender').value);
    formData.append('birthdate', document.getElementById('studBirth').value);
    formData.append('year_level', document.getElementById('studYear').value);
    formData.append('program_id', document.getElementById('studProg').value);

    fetch('students.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            showAlert(data.message, data.success ? 'success' : 'error');
            if (data.success) {
                closeStudModal();
                loadStudents();
            }
        });
}

function editStud(id) {
    fetch(`students.php?action=getOne&student_id=${id}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                openStudModal();
                setTimeout(() => {
                    document.getElementById('studModalTitle').textContent = 'Edit Student';
                    document.getElementById('studId').value = data.data.student_id;
                    document.getElementById('studNo').value = data.data.student_no;
                    document.getElementById('studFirst').value = data.data.first_name;
                    document.getElementById('studLast').value = data.data.last_name;
                    document.getElementById('studEmail').value = data.data.email;
                    document.getElementById('studGender').value = data.data.gender;
                    document.getElementById('studBirth').value = data.data.birthdate;
                    document.getElementById('studYear').value = data.data.year_level;
                    document.getElementById('studProg').value = data.data.program_id;
                }, 100);
            }
        });
}

function deleteStud(id) {
    if (!confirm('Delete this student?')) return;
    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('student_id', id);

    fetch('students.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            showAlert(data.message, data.success ? 'success' : 'error');
            if (data.success) loadStudents();
        });
}

// ===========================================
// COURSES MODULE
// ===========================================
function loadCourses() {
    const search = document.getElementById('searchCourse')?.value || '';
    fetch(`courses.php?action=read&search=${search}`)
        .then(res => res.json())
        .then(data => {
            const tbody = document.getElementById('courseTableBody');
            if (data.success && data.data.length > 0) {
                tbody.innerHTML = data.data.map(c => `
                    <tr>
                        <td>${c.course_id}</td>
                        <td>${c.course_code}</td>
                        <td>${c.course_title}</td>
                        <td>${c.units}</td>
                        <td>${c.lecture_hours}</td>
                        <td>${c.lab_hours}</td>
                        <td>${c.dept_name}</td>
                        <td class="no-print">
                            <button onclick="editCourse(${c.course_id})">Edit</button>
                            <button onclick="deleteCourse(${c.course_id})">Delete</button>
                        </td>
                    </tr>
                `).join('');
            } else {
                tbody.innerHTML = '<tr><td colspan="8">No records found</td></tr>';
            }
        })
        .catch(err => showAlert('Error loading courses', 'error'));
}

function openCourseModal() {
    fetch('departments.php?action=read')
        .then(res => res.json())
        .then(data => {
            const select = document.getElementById('courseDept');
            select.innerHTML = '<option value="">Select Department</option>' +
                data.data.map(d => `<option value="${d.dept_id}">${d.dept_name}</option>`).join('');
        });
    
    document.getElementById('courseModalTitle').textContent = 'Add Course';
    document.getElementById('courseForm').reset();
    document.getElementById('courseId').value = '';
    document.getElementById('courseModal').classList.add('active');
}

function closeCourseModal() {
    document.getElementById('courseModal').classList.remove('active');
}

function saveCourse(e) {
    e.preventDefault();
    const formData = new FormData();
    const id = document.getElementById('courseId').value;
    
    formData.append('action', id ? 'update' : 'create');
    if (id) formData.append('course_id', id);
    formData.append('course_code', document.getElementById('courseCode').value);
    formData.append('course_title', document.getElementById('courseTitle').value);
    formData.append('units', document.getElementById('courseUnits').value);
    formData.append('lecture_hours', document.getElementById('courseLecHrs').value);
    formData.append('lab_hours', document.getElementById('courseLabHrs').value);
    formData.append('dept_id', document.getElementById('courseDept').value);

    fetch('courses.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            showAlert(data.message, data.success ? 'success' : 'error');
            if (data.success) {
                closeCourseModal();
                loadCourses();
            }
        });
}

function editCourse(id) {
    fetch(`courses.php?action=getOne&course_id=${id}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                openCourseModal();
                setTimeout(() => {
                    document.getElementById('courseModalTitle').textContent = 'Edit Course';
                    document.getElementById('courseId').value = data.data.course_id;
                    document.getElementById('courseCode').value = data.data.course_code;
                    document.getElementById('courseTitle').value = data.data.course_title;
                    document.getElementById('courseUnits').value = data.data.units;
                    document.getElementById('courseLecHrs').value = data.data.lecture_hours;
                    document.getElementById('courseLabHrs').value = data.data.lab_hours;
                    document.getElementById('courseDept').value = data.data.dept_id;
                }, 100);
            }
        });
}

function deleteCourse(id) {
    if (!confirm('Delete this course?')) return;
    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('course_id', id);

    fetch('courses.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            showAlert(data.message, data.success ? 'success' : 'error');
            if (data.success) loadCourses();
        });
}

// ===========================================
// TERMS MODULE
// ===========================================
function loadTerms() {
    const search = document.getElementById('searchTerm')?.value || '';
    fetch(`terms.php?action=read&search=${search}`)
        .then(res => res.json())
        .then(data => {
            const tbody = document.getElementById('termTableBody');
            if (data.success && data.data.length > 0) {
                tbody.innerHTML = data.data.map(t => `
                    <tr>
                        <td>${t.term_id}</td>
                        <td>${t.term_code}</td>
                        <td>${t.start_date}</td>
                        <td>${t.end_date}</td>
                        <td class="no-print">
                            <button onclick="editTerm(${t.term_id})">Edit</button>
                            <button onclick="deleteTerm(${t.term_id})">Delete</button>
                        </td>
                    </tr>
                `).join('');
            } else {
                tbody.innerHTML = '<tr><td colspan="5">No records found</td></tr>';
            }
        })
        .catch(err => showAlert('Error loading terms', 'error'));
}

function openTermModal() {
    document.getElementById('termModalTitle').textContent = 'Add Term';
    document.getElementById('termForm').reset();
    document.getElementById('termId').value = '';
    document.getElementById('termModal').classList.add('active');
}

function closeTermModal() {
    document.getElementById('termModal').classList.remove('active');
}

function saveTerm(e) {
    e.preventDefault();
    const formData = new FormData();
    const id = document.getElementById('termId').value;
    
    formData.append('action', id ? 'update' : 'create');
    if (id) formData.append('term_id', id);
    formData.append('term_code', document.getElementById('termCode').value);
    formData.append('start_date', document.getElementById('termStart').value);
    formData.append('end_date', document.getElementById('termEnd').value);

    fetch('terms.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            showAlert(data.message, data.success ? 'success' : 'error');
            if (data.success) {
                closeTermModal();
                loadTerms();
            }
        });
}

function editTerm(id) {
    fetch(`terms.php?action=getOne&term_id=${id}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('termModalTitle').textContent = 'Edit Term';
                document.getElementById('termId').value = data.data.term_id;
                document.getElementById('termCode').value = data.data.term_code;
                document.getElementById('termStart').value = data.data.start_date;
                document.getElementById('termEnd').value = data.data.end_date;
                document.getElementById('termModal').classList.add('active');
            }
        });
}

function deleteTerm(id) {
    if (!confirm('Delete this term?')) return;
    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('term_id', id);

    fetch('terms.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            showAlert(data.message, data.success ? 'success' : 'error');
            if (data.success) loadTerms();
        });
}

// ===========================================
// ROOMS MODULE
// ===========================================
function loadRooms() {
    const search = document.getElementById('searchRoom')?.value || '';
    fetch(`rooms.php?action=read&search=${search}`)
        .then(res => res.json())
        .then(data => {
            const tbody = document.getElementById('roomTableBody');
            if (data.success && data.data.length > 0) {
                tbody.innerHTML = data.data.map(r => `
                    <tr>
                        <td>${r.room_id}</td>
                        <td>${r.building}</td>
                        <td>${r.room_code}</td>
                        <td>${r.capacity}</td>
                        <td class="no-print">
                            <button onclick="editRoom(${r.room_id})">Edit</button>
                            <button onclick="deleteRoom(${r.room_id})">Delete</button>
                        </td>
                    </tr>
                `).join('');
            } else {
                tbody.innerHTML = '<tr><td colspan="5">No records found</td></tr>';
            }
        })
        .catch(err => showAlert('Error loading rooms', 'error'));
}

function openRoomModal() {
    document.getElementById('roomModalTitle').textContent = 'Add Room';
    document.getElementById('roomForm').reset();
    document.getElementById('roomId').value = '';
    document.getElementById('roomModal').classList.add('active');
}

function closeRoomModal() {
    document.getElementById('roomModal').classList.remove('active');
}

function saveRoom(e) {
    e.preventDefault();
    const formData = new FormData();
    const id = document.getElementById('roomId').value;
    
    formData.append('action', id ? 'update' : 'create');
    if (id) formData.append('room_id', id);
    formData.append('building', document.getElementById('roomBuilding').value);
    formData.append('room_code', document.getElementById('roomCode').value);
    formData.append('capacity', document.getElementById('roomCapacity').value);

    fetch('rooms.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            showAlert(data.message, data.success ? 'success' : 'error');
            if (data.success) {
                closeRoomModal();
                loadRooms();
            }
        });
}

function editRoom(id) {
    fetch(`rooms.php?action=getOne&room_id=${id}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('roomModalTitle').textContent = 'Edit Room';
                document.getElementById('roomId').value = data.data.room_id;
                document.getElementById('roomBuilding').value = data.data.building;
                document.getElementById('roomCode').value = data.data.room_code;
                document.getElementById('roomCapacity').value = data.data.capacity;
                document.getElementById('roomModal').classList.add('active');
            }
        });
}

function deleteRoom(id) {
    if (!confirm('Delete this room?')) return;
    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('room_id', id);

    fetch('rooms.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            showAlert(data.message, data.success ? 'success' : 'error');
            if (data.success) loadRooms();
        });
}

// ===========================================
// SECTIONS MODULE
// ===========================================
function loadSections() {
    const search = document.getElementById('searchSection')?.value || '';
    fetch(`sections.php?action=read&search=${search}`)
        .then(res => res.json())
        .then(data => {
            const tbody = document.getElementById('sectionTableBody');
            if (data.success && data.data.length > 0) {
                tbody.innerHTML = data.data.map(s => `
                    <tr>
                        <td>${s.section_id}</td>
                        <td>${s.section_code}</td>
                        <td>${s.course_code}</td>
                        <td>${s.term_code}</td>
                        <td>${s.instructor_name}</td>
                        <td>${s.day_pattern} ${s.start_time}-${s.end_time}</td>
                        <td>${s.room_code}</td>
                        <td class="no-print">
                            <button onclick="editSection(${s.section_id})">Edit</button>
                            <button onclick="deleteSection(${s.section_id})">Delete</button>
                        </td>
                    </tr>
                `).join('');
            } else {
                tbody.innerHTML = '<tr><td colspan="8">No records found</td></tr>';
            }
        })
        .catch(err => showAlert('Error loading sections', 'error'));
}

function openSectionModal() {
    Promise.all([
        fetch('courses.php?action=read').then(r => r.json()),
        fetch('terms.php?action=read').then(r => r.json()),
        fetch('instructors.php?action=read').then(r => r.json()),
        fetch('rooms.php?action=read').then(r => r.json())
    ]).then(([courses, terms, instructors, rooms]) => {
        document.getElementById('sectionCourse').innerHTML = '<option value="">Select</option>' +
            courses.data.map(c => `<option value="${c.course_id}">${c.course_code} - ${c.course_title}</option>`).join('');
        
        document.getElementById('sectionTerm').innerHTML = '<option value="">Select</option>' +
            terms.data.map(t => `<option value="${t.term_id}">${t.term_code}</option>`).join('');
        
        document.getElementById('sectionInst').innerHTML = '<option value="">Select</option>' +
            instructors.data.map(i => `<option value="${i.instructor_id}">${i.first_name} ${i.last_name}</option>`).join('');
        
        document.getElementById('sectionRoom').innerHTML = '<option value="">Select</option>' +
            rooms.data.map(r => `<option value="${r.room_id}">${r.room_code}</option>`).join('');
    });
    
    document.getElementById('sectionModalTitle').textContent = 'Add Section';
    document.getElementById('sectionForm').reset();
    document.getElementById('sectionId').value = '';
    document.getElementById('sectionModal').classList.add('active');
}

function closeSectionModal() {
    document.getElementById('sectionModal').classList.remove('active');
}

function saveSection(e) {
    e.preventDefault();
    const formData = new FormData();
    const id = document.getElementById('sectionId').value;
    
    formData.append('action', id ? 'update' : 'create');
    if (id) formData.append('section_id', id);
    formData.append('section_code', document.getElementById('sectionCode').value);
    formData.append('course_id', document.getElementById('sectionCourse').value);
    formData.append('term_id', document.getElementById('sectionTerm').value);
    formData.append('instructor_id', document.getElementById('sectionInst').value);
    formData.append('day_pattern', document.getElementById('sectionDays').value);
    formData.append('start_time', document.getElementById('sectionStart').value);
    formData.append('end_time', document.getElementById('sectionEnd').value);
    formData.append('room_id', document.getElementById('sectionRoom').value);
    formData.append('max_capacity', document.getElementById('sectionCap').value);

    fetch('sections.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            showAlert(data.message, data.success ? 'success' : 'error');
            if (data.success) {
                closeSectionModal();
                loadSections();
            }
        });
}

function editSection(id) {
    fetch(`sections.php?action=getOne&section_id=${id}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                openSectionModal();
                setTimeout(() => {
                    document.getElementById('sectionModalTitle').textContent = 'Edit Section';
                    document.getElementById('sectionId').value = data.data.section_id;
                    document.getElementById('sectionCode').value = data.data.section_code;
                    document.getElementById('sectionCourse').value = data.data.course_id;
                    document.getElementById('sectionTerm').value = data.data.term_id;
                    document.getElementById('sectionInst').value = data.data.instructor_id;
                    document.getElementById('sectionDays').value = data.data.day_pattern;
                    document.getElementById('sectionStart').value = data.data.start_time;
                    document.getElementById('sectionEnd').value = data.data.end_time;
                    document.getElementById('sectionRoom').value = data.data.room_id;
                    document.getElementById('sectionCap').value = data.data.max_capacity;
                }, 100);
            }
        });
}

function deleteSection(id) {
    if (!confirm('Delete this section?')) return;
    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('section_id', id);

    fetch('sections.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            showAlert(data.message, data.success ? 'success' : 'error');
            if (data.success) loadSections();
        });
}

// ===========================================
// ENROLLMENTS MODULE
// ===========================================
function loadEnrollments() {
    const search = document.getElementById('searchEnroll')?.value || '';
    fetch(`enrollments.php?action=read&search=${search}`)
        .then(res => res.json())
        .then(data => {
            const tbody = document.getElementById('enrollTableBody');
            if (data.success && data.data.length > 0) {
                tbody.innerHTML = data.data.map(e => `
                    <tr>
                        <td>${e.enrollment_id}</td>
                        <td>${e.student_name} (${e.student_no})</td>
                        <td>${e.course_code}</td>
                        <td>${e.section_code}</td>
                        <td>${e.date_enrolled}</td>
                        <td>${e.status}</td>
                        <td>${e.letter_grade || '-'}</td>
                        <td class="no-print">
                            <button onclick="editEnroll(${e.enrollment_id})">Edit</button>
                            <button onclick="deleteEnroll(${e.enrollment_id})">Delete</button>
                        </td>
                    </tr>
                `).join('');
            } else {
                tbody.innerHTML = '<tr><td colspan="8">No records found</td></tr>';
            }
        })
        .catch(err => showAlert('Error loading enrollments', 'error'));
}

function openEnrollModal() {
    Promise.all([
        fetch('students.php?action=read').then(r => r.json()),
        fetch('sections.php?action=read').then(r => r.json())
    ]).then(([students, sections]) => {
        document.getElementById('enrollStud').innerHTML = '<option value="">Select Student</option>' +
            students.data.map(s => `<option value="${s.student_id}">${s.student_no} - ${s.first_name} ${s.last_name}</option>`).join('');
        
        document.getElementById('enrollSection').innerHTML = '<option value="">Select Section</option>' +
            sections.data.map(sec => `<option value="${sec.section_id}">${sec.course_code} - ${sec.section_code} (${sec.term_code})</option>`).join('');
    });
    
    document.getElementById('enrollModalTitle').textContent = 'Add Enrollment';
    document.getElementById('enrollForm').reset();
    document.getElementById('enrollId').value = '';
    document.getElementById('enrollModal').classList.add('active');
}

function closeEnrollModal() {
    document.getElementById('enrollModal').classList.remove('active');
}

function saveEnrollment(e) {
    e.preventDefault();
    const formData = new FormData();
    const id = document.getElementById('enrollId').value;
    
    formData.append('action', id ? 'update' : 'create');
    if (id) formData.append('enrollment_id', id);
    formData.append('student_id', document.getElementById('enrollStud').value);
    formData.append('section_id', document.getElementById('enrollSection').value);
    formData.append('date_enrolled', document.getElementById('enrollDate').value);
    formData.append('status', document.getElementById('enrollStatus').value);
    formData.append('letter_grade', document.getElementById('enrollGrade').value);

    fetch('enrollments.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            showAlert(data.message, data.success ? 'success' : 'error');
            if (data.success) {
                closeEnrollModal();
                loadEnrollments();
            }
        });
}

function editEnroll(id) {
    fetch(`enrollments.php?action=getOne&enrollment_id=${id}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                openEnrollModal();
                setTimeout(() => {
                    document.getElementById('enrollModalTitle').textContent = 'Edit Enrollment';
                    document.getElementById('enrollId').value = data.data.enrollment_id;
                    document.getElementById('enrollStud').value = data.data.student_id;
                    document.getElementById('enrollSection').value = data.data.section_id;
                    document.getElementById('enrollDate').value = data.data.date_enrolled;
                    document.getElementById('enrollStatus').value = data.data.status;
                    document.getElementById('enrollGrade').value = data.data.letter_grade || '';
                }, 100);
            }
        });
}

function deleteEnroll(id) {
    if (!confirm('Delete this enrollment?')) return;
    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('enrollment_id', id);

    fetch('enrollments.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            showAlert(data.message, data.success ? 'success' : 'error');
            if (data.success) loadEnrollments();
        });
}

// ===========================================
// EXCEL EXPORT FUNCTIONS
// ===========================================
function exportToExcel(sectionName, fileName) {
    const tableMap = {
        'departments': 'departmentsTable',
        'programs': 'programsTable',
        'instructors': 'instructorsTable',
        'students': 'studentsTable',
        'courses': 'coursesTable',
        'terms': 'termsTable',
        'rooms': 'roomsTable',
        'sections': 'sectionsTable',
        'enrollments': 'enrollmentsTable'
    };

    const tableId = tableMap[sectionName];
    const table = document.getElementById(tableId);
    
    if (!table) {
        showAlert('Table not found!', 'error');
        return;
    }

    // Create a workbook and worksheet
    const wb = XLSX.utils.book_new();
    
    // Convert table to worksheet, excluding action columns
    const ws = XLSX.utils.table_to_sheet(table, {
        raw: false,
        dateNF: 'yyyy-mm-dd'
    });

    // Remove the Actions column (last column)
    const range = XLSX.utils.decode_range(ws['!ref']);
    const lastCol = XLSX.utils.encode_col(range.e.c);
    
    // Delete the last column (Actions) from each row
    for (let row = range.s.r; row <= range.e.r; row++) {
        const cellAddress = lastCol + (row + 1);
        delete ws[cellAddress];
    }
    
    // Update the range to exclude the last column
    range.e.c = range.e.c - 1;
    ws['!ref'] = XLSX.utils.encode_range(range);

    // Set column widths
    const colWidths = [];
    for (let col = 0; col <= range.e.c; col++) {
        colWidths.push({ width: 15 });
    }
    ws['!cols'] = colWidths;

    // Add worksheet to workbook
    XLSX.utils.book_append_sheet(wb, ws, fileName);

    // Generate filename with current date
    const now = new Date();
    const dateStr = now.toISOString().split('T')[0];
    const fullFileName = `${fileName}_${dateStr}.xlsx`;

    // Write file
    XLSX.writeFile(wb, fullFileName);
    
    showAlert(`Excel file "${fullFileName}" has been downloaded successfully!`, 'success');
}

function exportAllToExcel() {
    const wb = XLSX.utils.book_new();
    
    const sections = [
        { name: 'departments', table: 'departmentsTable', sheet: 'Departments' },
        { name: 'programs', table: 'programsTable', sheet: 'Programs' },
        { name: 'instructors', table: 'instructorsTable', sheet: 'Instructors' },
        { name: 'students', table: 'studentsTable', sheet: 'Students' },
        { name: 'courses', table: 'coursesTable', sheet: 'Courses' },
        { name: 'terms', table: 'termsTable', sheet: 'Terms' },
        { name: 'rooms', table: 'roomsTable', sheet: 'Rooms' },
        { name: 'sections', table: 'sectionsTable', sheet: 'Sections' },
        { name: 'enrollments', table: 'enrollmentsTable', sheet: 'Enrollments' }
    ];

    sections.forEach(section => {
        const table = document.getElementById(section.table);
        if (table && table.rows.length > 1) {
            const ws = XLSX.utils.table_to_sheet(table, { raw: false });
            
            // Remove Actions column
            const range = XLSX.utils.decode_range(ws['!ref']);
            const lastCol = XLSX.utils.encode_col(range.e.c);
            
            for (let row = range.s.r; row <= range.e.r; row++) {
                const cellAddress = lastCol + (row + 1);
                delete ws[cellAddress];
            }
            
            range.e.c = range.e.c - 1;
            ws['!ref'] = XLSX.utils.encode_range(range);
            
            // Set column widths
            const colWidths = [];
            for (let col = 0; col <= range.e.c; col++) {
                colWidths.push({ width: 15 });
            }
            ws['!cols'] = colWidths;
            
            XLSX.utils.book_append_sheet(wb, ws, section.sheet);
        }
    });

    const now = new Date();
    const dateStr = now.toISOString().split('T')[0];
    const fileName = `Enrollment_System_Complete_${dateStr}.xlsx`;
    
    XLSX.writeFile(wb, fileName);
    showAlert(`Complete Excel file "${fileName}" has been downloaded successfully!`, 'success');
}

// ===========================================
// INITIALIZATION
// ===========================================
window.addEventListener('DOMContentLoaded', function() {
    loadDepartments();
});

// Close modals when clicking outside
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.classList.remove('active');
    }
}