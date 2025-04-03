// Generic edit function
function editRecord(modalId, data) {
    const modal = document.querySelector(modalId);
    if (!modal) return;

    // Clear any existing hidden id field
    const existingHiddenField = modal.querySelector('input[name="id"]');
    if (existingHiddenField) existingHiddenField.remove();

    // Populate form fields
    Object.keys(data).forEach(key => {
        const input = modal.querySelector(`[name="${key}"]`);
        if (input) input.value = data[key];
    });

    // Add hidden id field
    const form = modal.querySelector('form');
    const hiddenField = document.createElement('input');
    hiddenField.type = 'hidden';
    hiddenField.name = 'id';
    hiddenField.value = data.id;
    form.appendChild(hiddenField);

    // Show modal
    const modalInstance = new bootstrap.Modal(modal);
    modalInstance.show();
}

// Generic delete function
function deleteRecord(url, id) {
    if (confirm('Are you sure you want to delete this record?')) {
        window.location.href = `${url}?action=delete&id=${id}`;
    }
}

// Specific functions for each type
function editRoom(id, name) {
    editRecord('#addRoomModal', {
        id: id,
        name: name
    });
}

function editProfessor(id, name) {
    editRecord('#addProfessorModal', {
        id: id,
        name: name
    });
}

function editSchedule(id, roomId, courseId, professorId, day, startTime, endTime) {
    editRecord('#addScheduleModal', {
        id: id,
        course_id: courseId,
        room_id: roomId,
        professor_id: professorId,
        start_time: startTime,
        end_time: endTime,
        day: day
    });
}

function editCourse(id, code, name) {
    editRecord('#addCourseModal', {
        id: id,
        course_code: code,
        course_name: name
    });
}

// Delete functions
function deleteRoom(id) {
    deleteRecord('process_room.php', id);
}

function deleteProfessor(id) {
    deleteRecord('process_professor.php', id);
}

function deleteSchedule(id) {
    if (confirm('Are you sure you want to delete this schedule?')) {
        window.location.href = `process_schedule.php?action=delete&id=${id}`;
    }
}

function deleteCourse(id) {
    deleteRecord('process_course.php', id);
}

function updateProfessorStatus(professorId, status) {
    fetch('process_professor.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=updateStatus&id=${professorId}&status=${status}`
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Update all badges with this professor ID
            const scheduleCards = document.querySelectorAll('.schedule-card');
            scheduleCards.forEach(card => {
                if (card.querySelector(`[data-professor-id="${professorId}"]`)) {
                    const badge = card.querySelector('.badge');
                    let badgeClass = 'success';
                    let textClass = 'text-white';
                    if (status === 'Absent') {
                        badgeClass = 'danger';
                        textClass = 'text-white';
                    } else if (status === 'On Leave') {
                        badgeClass = 'warning';
                        textClass = 'text-dark';
                    } else if (status === 'On Meeting') {
                        badgeClass = 'info';
                        textClass = 'text-white';
                    }
                    badge.className = `badge bg-${badgeClass} ${textClass}`;
                    badge.textContent = status;
                }
            });
            
            // Update the select element styling
            const selectElement = document.querySelector(`.status-select[data-professor-id="${professorId}"]`);
            if (selectElement && typeof updateStatusSelectStyle === 'function') {
                updateStatusSelectStyle(selectElement);
            }
        } else {
            alert(data.message || 'Failed to update status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating status');
    });
}

function validateScheduleForm(form) {
    const formData = new FormData(form);
    formData.append('action', 'validate'); // Add action parameter
    
    return fetch('process_schedule.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Server returned ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        if (!data.success) {
            alert(data.message || 'Schedule validation failed');
            return false;
        }
        return true;
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while validating the schedule');
        return false;
    });
}

// Keep tab state after page reload
document.addEventListener('DOMContentLoaded', function() {
    const hash = window.location.hash;
    if (hash) {
        const tab = new bootstrap.Tab(document.querySelector(`a[data-bs-target="${hash}"]`));
        tab.show();
    }
});

// Modal reset handlers
document.addEventListener('DOMContentLoaded', function() {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.addEventListener('hidden.bs.modal', function() {
            const form = this.querySelector('form');
            if (form) {
                form.reset();
                const hiddenId = form.querySelector('input[name="id"]');
                if (hiddenId) hiddenId.remove();
            }
        });
    });
});

function showAlert(message, type = 'success') {
    const alertContainer = document.getElementById('alertContainer');
    const alertHTML = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    alertContainer.innerHTML = alertHTML;

    // Auto-dismiss after 3 seconds
    setTimeout(() => {
        const alertElement = document.querySelector('.alert');
        if (alertElement) {
            const bsAlert = new bootstrap.Alert(alertElement);
            bsAlert.close();
        }
    }, 3000);
}

document.addEventListener('DOMContentLoaded', function() {
    // Auto-dismiss alerts after 3 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 3000);
    });

    // Schedule form submission
    const scheduleForm = document.querySelector('#addScheduleModal form');
    if (scheduleForm) {
        scheduleForm.addEventListener('submit', function(e) {
            const modal = bootstrap.Modal.getInstance(document.querySelector('#addScheduleModal'));
            if (modal) {
                modal.hide();
            }
        });
    }
    
    // Filter functionality for schedules
    // initializeScheduleFilters(); // Removed - now handled by filter.js
    
    // Room search functionality
    const roomSearchInput = document.getElementById('roomSearchInput');
    if (roomSearchInput) {
        roomSearchInput.addEventListener('keyup', function() {
            try {
                const searchTerm = this.value.toLowerCase();
                const roomTable = document.querySelector('#rooms table tbody');
                if (!roomTable) {
                    console.error('Room table body not found');
                    return;
                }
                
                const rows = roomTable.querySelectorAll('tr');
                
                rows.forEach(row => {
                    try {
                        const roomNameCell = row.querySelector('td:nth-child(2)');
                        if (!roomNameCell) {
                            console.error('Room name cell not found in row', row);
                            return;
                        }
                        
                        const roomName = roomNameCell.textContent.toLowerCase();
                        if (roomName.includes(searchTerm)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    } catch (rowError) {
                        console.error('Error processing room row:', rowError);
                    }
                });
            } catch (error) {
                console.error('Error in room search:', error);
            }
        });
    } else {
        console.warn('Room search input not found');
    }
    
    // Professor search functionality
    const professorSearchInput = document.getElementById('professorSearchInput');
    if (professorSearchInput) {
        professorSearchInput.addEventListener('keyup', function() {
            try {
                const searchTerm = this.value.toLowerCase();
                const professorTable = document.querySelector('#professors table tbody');
                if (!professorTable) {
                    console.error('Professor table body not found');
                    return;
                }
                
                const rows = professorTable.querySelectorAll('tr');
                
                rows.forEach(row => {
                    try {
                        const professorNameCell = row.querySelector('td:nth-child(3)');
                        if (!professorNameCell) {
                            console.error('Professor name cell not found in row', row);
                            return;
                        }
                        
                        const professorName = professorNameCell.textContent.toLowerCase();
                        if (professorName.includes(searchTerm)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    } catch (rowError) {
                        console.error('Error processing professor row:', rowError);
                    }
                });
            } catch (error) {
                console.error('Error in professor search:', error);
            }
        });
    } else {
        console.warn('Professor search input not found');
    }
    
    // Course search functionality
    const courseSearchInput = document.getElementById('courseSearchInput');
    if (courseSearchInput) {
        courseSearchInput.addEventListener('keyup', function() {
            try {
                const searchTerm = this.value.toLowerCase();
                const courseTable = document.querySelector('#courses table tbody');
                if (!courseTable) {
                    console.error('Course table body not found');
                    return;
                }
                
                const rows = courseTable.querySelectorAll('tr');
                
                rows.forEach(row => {
                    try {
                        const courseCodeCell = row.querySelector('td:nth-child(2)');
                        const courseNameCell = row.querySelector('td:nth-child(3)');
                        
                        if (!courseCodeCell || !courseNameCell) {
                            console.error('Course code or name cell not found in row', row);
                            return;
                        }
                        
                        const courseCode = courseCodeCell.textContent.toLowerCase();
                        const courseName = courseNameCell.textContent.toLowerCase();
                        
                        if (courseCode.includes(searchTerm) || courseName.includes(searchTerm)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    } catch (rowError) {
                        console.error('Error processing course row:', rowError);
                    }
                });
            } catch (error) {
                console.error('Error in course search:', error);
            }
        });
    } else {
        console.warn('Course search input not found');
    }
    
    // Schedule search functionality
    const scheduleSearchInput = document.getElementById('scheduleSearchInput');
    if (scheduleSearchInput) {
        scheduleSearchInput.addEventListener('keyup', function() {
            // This is now handled by the applyFilters function
        });
    } else {
        console.warn('Schedule search input not found');
    }
    
    // Log that search functionality has been initialized
    console.log('Search functionality initialized');
});

// Function to perform AJAX search (for future use)
function performAjaxSearch(type, searchTerm, callback) {
    fetch(`search.php?type=${type}&term=${encodeURIComponent(searchTerm)}`)
        .then(response => response.json())
        .then(data => {
            if (callback && typeof callback === 'function') {
                callback(data);
            }
        })
        .catch(error => {
            console.error('Error performing search:', error);
        });
}

// Debug function to check if search is working
function debugSearch() {
    console.log('Debugging search functionality...');
    
    // Check if search inputs exist
    const inputs = {
        room: document.getElementById('roomSearchInput'),
        professor: document.getElementById('professorSearchInput'),
        course: document.getElementById('courseSearchInput'),
        schedule: document.getElementById('scheduleSearchInput')
    };
    
    console.log('Search inputs found:', {
        room: !!inputs.room,
        professor: !!inputs.professor,
        course: !!inputs.course,
        schedule: !!inputs.schedule
    });
    
    // Check if tables/cards exist
    const elements = {
        roomTable: document.querySelector('#rooms table tbody'),
        professorTable: document.querySelector('#professors table tbody'),
        courseTable: document.querySelector('#courses table tbody'),
        scheduleCards: document.querySelectorAll('#schedules .schedule-card')
    };
    
    console.log('Search target elements found:', {
        roomTable: !!elements.roomTable,
        professorTable: !!elements.professorTable,
        courseTable: !!elements.courseTable,
        scheduleCards: elements.scheduleCards.length
    });
    
    // Trigger search events manually
    if (inputs.room) {
        console.log('Triggering room search...');
        inputs.room.dispatchEvent(new Event('keyup'));
    }
    
    if (inputs.professor) {
        console.log('Triggering professor search...');
        inputs.professor.dispatchEvent(new Event('keyup'));
    }
    
    if (inputs.course) {
        console.log('Triggering course search...');
        inputs.course.dispatchEvent(new Event('keyup'));
    }
    
    if (inputs.schedule) {
        console.log('Triggering schedule search...');
        inputs.schedule.dispatchEvent(new Event('keyup'));
    }
    
    console.log('Debug complete');
}

// Run debug on page load
document.addEventListener('DOMContentLoaded', function() {
    // Wait a bit to make sure everything is loaded
    setTimeout(debugSearch, 1000);
});

// View toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    const viewToggles = document.querySelectorAll('.view-toggle');
    
    viewToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const view = this.getAttribute('data-view');
            const target = this.getAttribute('data-target');
            const container = document.querySelector(`#${target} .card-body`);
            
            // Update toggle button states
            const toggles = document.querySelectorAll(`[data-target="${target}"]`);
            toggles.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            // Update view
            if (container) {
                const listView = container.querySelector('.list-view');
                const gridView = container.querySelector('.grid-view');
                
                if (view === 'list') {
                    listView.style.display = 'block';
                    gridView.style.display = 'none';
                } else {
                    listView.style.display = 'none';
                    gridView.style.display = 'grid';
                }
            }
            
            // Save preference to localStorage
            localStorage.setItem(`${target}_view`, view);
        });
    });
    
    // Restore view preferences
    ['rooms', 'professors', 'courses'].forEach(section => {
        const savedView = localStorage.getItem(`${section}_view`);
        if (savedView) {
            const toggle = document.querySelector(`.view-toggle[data-view="${savedView}"][data-target="${section}"]`);
            if (toggle) {
                toggle.click();
            }
        }
    });
});

// Extend search functionality to work with both views
function performSearch(searchInput, targetId) {
    const searchTerm = searchInput.value.toLowerCase();
    const container = document.querySelector(`#${targetId} .card-body`);
    const isGridView = container.querySelector('.grid-view').style.display === 'grid';
    
    if (isGridView) {
        const items = container.querySelectorAll('.grid-item');
        items.forEach(item => {
            const text = item.textContent.toLowerCase();
            item.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    } else {
        const rows = container.querySelectorAll('tbody tr');
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    }
}

// Update existing search event listeners
document.addEventListener('DOMContentLoaded', function() {
    // ... existing DOMContentLoaded code ...
    
    // Room search
    const roomSearchInput = document.getElementById('roomSearchInput');
    if (roomSearchInput) {
        roomSearchInput.addEventListener('keyup', () => performSearch(roomSearchInput, 'rooms'));
    }
    
    // Professor search
    const professorSearchInput = document.getElementById('professorSearchInput');
    if (professorSearchInput) {
        professorSearchInput.addEventListener('keyup', () => performSearch(professorSearchInput, 'professors'));
    }
    
    // Course search
    const courseSearchInput = document.getElementById('courseSearchInput');
    if (courseSearchInput) {
        courseSearchInput.addEventListener('keyup', () => performSearch(courseSearchInput, 'courses'));
    }
});

// Initialize schedule filters - defined outside the DOMContentLoaded to avoid variable conflicts
function initializeScheduleFilters() {
    const filterCheckboxes = document.querySelectorAll('.filter-checkbox');
    const scheduleCards = document.querySelectorAll('.schedule-card-container');
    const clearFiltersBtn = document.getElementById('clearFiltersBtn');
    
    // Store active filters
    let activeFilters = {
        day: [],
        professor: [],
        room: []
    };
    
    // Function to update filters when checkboxes are clicked
    function updateFilters() {
        // Reset active filters
        activeFilters = {
            day: [],
            professor: [],
            room: []
        };
        
        // Collect all checked filters
        filterCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                const filterType = checkbox.getAttribute('data-filter-type');
                const filterValue = checkbox.value;
                activeFilters[filterType].push(filterValue);
            }
        });
        
        // Apply the filters
        applyFilters();
    }
    
    // Apply filters to schedule cards
    function applyFilters() {
        const searchInput = document.getElementById('scheduleSearchInput');
        const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
        
        scheduleCards.forEach(card => {
            let matchesFilters = true;
            
            // Day filter
            if (activeFilters.day.length > 0) {
                const dayElement = card.querySelector('.fa-calendar').parentNode;
                const day = dayElement.textContent.trim();
                if (!activeFilters.day.some(filterDay => day.includes(filterDay))) {
                    matchesFilters = false;
                }
            }
            
            // Professor filter
            if (activeFilters.professor.length > 0 && matchesFilters) {
                const professorElement = card.querySelector('small.text-muted');
                const professor = professorElement.textContent.replace('Prof. ', '').trim();
                if (!activeFilters.professor.includes(professor)) {
                    matchesFilters = false;
                }
            }
            
            // Room filter
            if (activeFilters.room.length > 0 && matchesFilters) {
                const roomElement = card.querySelector('.fa-door-open').parentNode;
                const room = roomElement.textContent.replace('Room ', '').trim();
                if (!activeFilters.room.includes(room)) {
                    matchesFilters = false;
                }
            }
            
            // Search term filter
            if (searchTerm && matchesFilters) {
                const cardText = card.textContent.toLowerCase();
                if (!cardText.includes(searchTerm)) {
                    matchesFilters = false;
                }
            }
            
            // Show/hide the card based on the filter result
            card.style.display = matchesFilters ? '' : 'none';
        });
    }
    
    // Add event listeners to checkboxes
    filterCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateFilters);
    });
    
    // Clear all filters button
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', function() {
            filterCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            
            // Reset active filters
            activeFilters = {
                day: [],
                professor: [],
                room: []
            };
            
            // If search input exists, clear it
            const searchInput = document.getElementById('scheduleSearchInput');
            if (searchInput) {
                searchInput.value = '';
            }
            
            // Show all cards
            scheduleCards.forEach(card => {
                card.style.display = '';
            });
        });
    }
    
    // Connect search input to filter
    const searchInput = document.getElementById('scheduleSearchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', applyFilters);
    }
}