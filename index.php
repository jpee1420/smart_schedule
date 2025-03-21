<?php
session_start();
require_once 'config.php';
require_once 'functions.php';

// Verify all professor images at page load
$fixed_count = verifyAllProfessorImages($conn);
if ($fixed_count > 0) {
    $_SESSION['message'] = "Fixed {$fixed_count} missing professor image(s).";
    $_SESSION['message_type'] = 'warning';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Management System</title>
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet"> -->
    <link href="css/all.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
</head>
<body>
    <!-- Alert Container -->
    <div id="alertContainer">
        <?php
        // Display alert if message is set
        if (isset($_SESSION['message']) && isset($_SESSION['message_type'])) {
            echo '<div class="alert alert-' . $_SESSION['message_type'] . ' alert-dismissible fade show" role="alert">';
            echo $_SESSION['message'];
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            echo '</div>';
            
            // Clear the message
            unset($_SESSION['message']);
            unset($_SESSION['message_type']);
        }
        ?>
    </div>

    <!-- Scroll buttons container -->
    <div class="scroll-buttons">
        <button class="scroll-btn" id="scrollTopBtn" title="Scroll to Top">
            <i class="fas fa-arrow-up"></i>
        </button>
        <button class="scroll-btn" id="scrollBottomBtn" title="Scroll to Bottom">
            <i class="fas fa-arrow-down"></i>
        </button>
    </div>

    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Left Sidebar -->
            <div class="col-md-2 sidebar p-0">
                <div class="d-flex flex-column">
                    <div class="p-3 text-white">
                        <h4>Admin Panel</h4>
                    </div>
                    <nav class="nav flex-column">
                        <a class="nav-link active" href="#schedules" data-bs-toggle="tab">
                            <i class="fas fa-calendar-alt me-2"></i> Schedules
                        </a>
                        <a class="nav-link" href="#rooms" data-bs-toggle="tab">
                            <i class="fas fa-door-open me-2"></i> Rooms
                        </a>
                        <a class="nav-link" href="#professors" data-bs-toggle="tab">
                            <i class="fas fa-chalkboard-teacher me-2"></i> Professors
                        </a>
                        <a class="nav-link" href="#courses" data-bs-toggle="tab">
                            <i class="fas fa-book me-2"></i> Courses
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-8 p-4">
                <div class="tab-content">
                    <!-- Schedules Tab -->
                    <div class="tab-pane fade show active" id="schedules">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2>Class Schedules</h2>
                            <div>
                                <a href="view_schedules.php" class="btn btn-info me-2">
                                    <i class="fas fa-tv"></i> View Slideshow
                                </a>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addScheduleModal">
                                    <i class="fas fa-plus"></i> Add Schedule
                                </button>
                            </div>
                        </div>
                        <!-- Add search bar for schedules -->
                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" id="scheduleSearchInput" placeholder="Search schedules by course, professor, or room...">
                            </div>
                            <div class="mt-1 small text-muted">Type to search</div>
                        </div>
                        
                        <?php
                        // Load schedules data before using it in filters
                        $schedules = getSchedules($conn);
                        ?>
                        
                        <!-- Collapsible Filter Section -->
                        <div class="mb-3">
                            <button class="btn btn-sm btn-outline-secondary d-flex justify-content-between align-items-center w-auto px-3 py-1" 
                                    type="button" 
                                    data-bs-toggle="collapse" 
                                    data-bs-target="#filterOptions" 
                                    aria-expanded="false" 
                                    aria-controls="filterOptions">
                                <span><i class="fas fa-filter me-1"></i> Filters</span>
                                <i class="fas fa-chevron-down ms-2"></i>
                            </button>
                            
                            <div class="collapse mt-2" id="filterOptions">
                                <div class="card card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <h6 class="filter-heading">Day</h6>
                                            <div class="filter-group" id="dayFilters">
                                                <?php
                                                $days = [];
                                                foreach ($schedules as $schedule) {
                                                    if (!in_array($schedule['day'], $days)) {
                                                        $days[] = $schedule['day'];
                                                    }
                                                }
                                                foreach ($days as $day):
                                                ?>
                                                <div class="form-check">
                                                    <input class="form-check-input filter-checkbox" type="checkbox" value="<?php echo $day; ?>" id="day_<?php echo $day; ?>" data-filter-type="day">
                                                    <label class="form-check-label" for="day_<?php echo $day; ?>">
                                                        <?php echo $day; ?>
                                                    </label>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <h6 class="filter-heading">Professor</h6>
                                            <div class="filter-group" id="professorFilters">
                                                <?php
                                                $professors = [];
                                                foreach ($schedules as $schedule) {
                                                    if (!in_array($schedule['professor_name'], $professors)) {
                                                        $professors[] = $schedule['professor_name'];
                                                    }
                                                }
                                                sort($professors);
                                                foreach ($professors as $professor):
                                                ?>
                                                <div class="form-check">
                                                    <input class="form-check-input filter-checkbox" type="checkbox" value="<?php echo $professor; ?>" id="prof_<?php echo md5($professor); ?>" data-filter-type="professor">
                                                    <label class="form-check-label" for="prof_<?php echo md5($professor); ?>">
                                                        <?php echo $professor; ?>
                                                    </label>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <h6 class="filter-heading">Room</h6>
                                            <div class="filter-group" id="roomFilters">
                                                <?php
                                                $rooms = [];
                                                foreach ($schedules as $schedule) {
                                                    if (!in_array($schedule['room_name'], $rooms)) {
                                                        $rooms[] = $schedule['room_name'];
                                                    }
                                                }
                                                sort($rooms);
                                                foreach ($rooms as $room):
                                                ?>
                                                <div class="form-check">
                                                    <input class="form-check-input filter-checkbox" type="checkbox" value="<?php echo $room; ?>" id="room_<?php echo md5($room); ?>" data-filter-type="room">
                                                    <label class="form-check-label" for="room_<?php echo md5($room); ?>">
                                                        <?php echo $room; ?>
                                                    </label>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-3 d-flex justify-content-between">
                                        <button class="btn btn-sm btn-secondary" id="clearFiltersBtn">Clear All Filters</button>
                                        <!-- <span class="text-muted small pt-2">Select multiple options to apply filters</span> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <?php
                                    foreach ($schedules as $schedule):
                                    ?>
                                    <div class="col-md-4 schedule-card-container">
                                        <div class="card schedule-card">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center mb-3">
                                                    <img src="<?php 
                                                        echo !empty($schedule['profile_image']) && $schedule['profile_image'] !== 'placeholder.png'
                                                            ? 'uploads/' . htmlspecialchars($schedule['profile_image']) 
                                                            : 'uploads/placeholder.png'; 
                                                    ?>" class="professor-image me-3" alt="Professor">
                                                    <div>
                                                        <h5 class="card-title mb-0"><?php echo htmlspecialchars($schedule['course']); ?></h5>
                                                        <small class="text-muted">Prof. <?php echo htmlspecialchars($schedule['professor_name']); ?></small>
                                                    </div>
                                                </div>
                                                <p class="card-text">
                                                    <i class="fas fa-clock me-2"></i>
                                                    <?php echo date('h:i A', strtotime($schedule['start_time'])); ?> - 
                                                    <?php echo date('h:i A', strtotime($schedule['end_time'])); ?>
                                                </p>
                                                <p class="card-text">
                                                    <i class="fas fa-calendar me-2"></i>
                                                    <?php echo $schedule['day']; ?>
                                                </p>
                                                <p class="card-text">
                                                    <i class="fas fa-door-open me-2"></i>
                                                    Room <?php echo htmlspecialchars($schedule['room_name']); ?>
                                                </p>
                                                <div class="d-flex justify-content-between align-items-center mt-auto">
                                                    <?php
                                                    $badgeClass = 'success';
                                                    if ($schedule['professor_status'] === 'Absent') {
                                                        $badgeClass = 'danger';
                                                    } else if ($schedule['professor_status'] === 'On Leave') {
                                                        $badgeClass = 'warning';
                                                    }
                                                    ?>
                                                    <span class="badge bg-<?php echo $badgeClass; ?>" 
                                                          data-professor-id="<?php echo $schedule['professor_id']; ?>">
                                                        <?php echo $schedule['professor_status']; ?>
                                                    </span>
                                                    <div>
                                                        <button class="btn btn-sm btn-primary me-2" onclick="editSchedule(
                                                            <?php echo $schedule['id']; ?>,
                                                            <?php echo $schedule['room_id']; ?>,
                                                            <?php echo $schedule['course_id']; ?>,
                                                            <?php echo $schedule['professor_id']; ?>,
                                                            '<?php echo $schedule['day']; ?>',
                                                            '<?php echo $schedule['start_time']; ?>',
                                                            '<?php echo $schedule['end_time']; ?>'
                                                        )">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-danger" onclick="deleteSchedule(<?php echo $schedule['id']; ?>)">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Rooms Tab -->
                    <div class="tab-pane fade" id="rooms">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="d-flex align-items-center">
                                <h2>Room Management</h2>
                                <button class="view-toggle active" data-view="list" data-target="rooms">
                                    <i class="fas fa-list"></i> List
                                </button>
                                <button class="view-toggle" data-view="grid" data-target="rooms">
                                    <i class="fas fa-th"></i> Grid
                                </button>
                            </div>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoomModal">
                                <i class="fas fa-plus"></i> Add Room
                            </button>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <!-- Add search bar for rooms -->
                                <div class="mb-3">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                                        <input type="text" class="form-control" id="roomSearchInput" placeholder="Search rooms by name...">
                                    </div>
                                    <div class="mt-1 small text-muted">Type to search</div>
                                </div>
                                <!-- List View -->
                                <div class="list-view">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Room Name</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $rooms = getAllRooms($conn);
                                                foreach ($rooms as $room):
                                                ?>
                                                <tr>
                                                    <td><?php echo $room['id']; ?></td>
                                                    <td><?php echo $room['name']; ?></td>
                                                    <td>
                                                        <button class="btn btn-sm btn-primary" onclick="editRoom(<?php echo $room['id']; ?>, '<?php echo $room['name']; ?>')">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-danger" onclick="deleteRoom(<?php echo $room['id']; ?>)">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- Grid View -->
                                <div class="grid-view" style="display: none;">
                                    <?php foreach ($rooms as $room): ?>
                                    <div class="grid-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h5 class="mb-0"><?php echo $room['name']; ?></h5>
                                            <div class="btn-group">
                                                <button class="btn btn-sm btn-primary" onclick="editRoom(<?php echo $room['id']; ?>, '<?php echo $room['name']; ?>')">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger" onclick="deleteRoom(<?php echo $room['id']; ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="text-muted mt-2">
                                            <small>Room ID: <?php echo $room['id']; ?></small>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Professors Tab -->
                    <div class="tab-pane fade" id="professors">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="d-flex align-items-center">
                                <h2>Professor Management</h2>
                                <button class="view-toggle active" data-view="list" data-target="professors">
                                    <i class="fas fa-list"></i> List
                                </button>
                                <button class="view-toggle" data-view="grid" data-target="professors">
                                    <i class="fas fa-th"></i> Grid
                                </button>
                            </div>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProfessorModal">
                                <i class="fas fa-plus"></i> Add Professor
                            </button>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <!-- Add search bar for professors -->
                                <div class="mb-3">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                                        <input type="text" class="form-control" id="professorSearchInput" placeholder="Search professors by name...">
                                    </div>
                                    <div class="mt-1 small text-muted">Type to search</div>
                                </div>
                                <!-- List View -->
                                <div class="list-view">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Image</th>
                                                    <th>Name</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $professors = getAllProfessors($conn);
                                                foreach ($professors as $professor):
                                                ?>
                                                <tr>
                                                    <td><?php echo $professor['id']; ?></td>
                                                    <td>
                                                        <img src="<?php 
                                                            echo !empty($professor['profile_image']) && $professor['profile_image'] !== 'placeholder.png'
                                                                ? 'uploads/' . htmlspecialchars($professor['profile_image']) 
                                                                : 'uploads/placeholder.png'; 
                                                        ?>" class="professor-profile-image" alt="<?php echo htmlspecialchars($professor['name']); ?>">
                                                    </td>
                                                    <td><?php echo $professor['name']; ?></td>
                                                    <td>
                                                        <button class="btn btn-sm btn-primary me-2" onclick="editProfessor(<?php echo $professor['id']; ?>, '<?php echo $professor['name']; ?>', '<?php echo $professor['profile_image']; ?>')">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-danger" onclick="deleteProfessor(<?php echo $professor['id']; ?>)">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- Grid View -->
                                <div class="grid-view" style="display: none;">
                                    <?php foreach ($professors as $professor): ?>
                                    <div class="grid-item">
                                        <div class="text-center mb-3">
                                            <img src="<?php 
                                                echo !empty($professor['profile_image']) && $professor['profile_image'] !== 'placeholder.png'
                                                    ? 'uploads/' . htmlspecialchars($professor['profile_image']) 
                                                    : 'uploads/placeholder.png'; 
                                            ?>" class="professor-profile-image" alt="<?php echo htmlspecialchars($professor['name']); ?>">
                                        </div>
                                        <h5 class="text-center mb-3"><?php echo $professor['name']; ?></h5>
                                        <div class="d-flex justify-content-center">
                                            <button class="btn btn-sm btn-primary me-2" onclick="editProfessor(<?php echo $professor['id']; ?>, '<?php echo $professor['name']; ?>', '<?php echo $professor['profile_image']; ?>')">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteProfessor(<?php echo $professor['id']; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Courses Tab -->
                    <div class="tab-pane fade" id="courses">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="d-flex align-items-center">
                                <h2>Course Management</h2>
                                <button class="view-toggle active" data-view="list" data-target="courses">
                                    <i class="fas fa-list"></i> List
                                </button>
                                <button class="view-toggle" data-view="grid" data-target="courses">
                                    <i class="fas fa-th"></i> Grid
                                </button>
                            </div>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCourseModal">
                                <i class="fas fa-plus"></i> Add Course
                            </button>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <!-- Add search bar for courses -->
                                <div class="mb-3">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                                        <input type="text" class="form-control" id="courseSearchInput" placeholder="Search courses by code or name...">
                                    </div>
                                    <div class="mt-1 small text-muted">Type to search</div>
                                </div>
                                <!-- List View -->
                                <div class="list-view">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Course Code</th>
                                                    <th>Course Name</th>
                                                    <th>Type</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $courses = getAllCourses($conn);
                                                foreach ($courses as $course):
                                                ?>
                                                <tr>
                                                    <td><?php echo $course['id']; ?></td>
                                                    <td><?php echo $course['course_code']; ?></td>
                                                    <td><?php echo $course['course_name']; ?></td>
                                                    <td>
                                                        <span class="badge bg-<?php echo $course['lab'] ? 'info' : 'primary'; ?>">
                                                            <?php echo $course['lab'] ? 'Laboratory' : 'Lecture'; ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-primary me-2" onclick="editCourse(<?php echo $course['id']; ?>, '<?php echo $course['course_code']; ?>', '<?php echo $course['course_name']; ?>')">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-danger" onclick="deleteCourse(<?php echo $course['id']; ?>)">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- Grid View -->
                                <div class="grid-view" style="display: none;">
                                    <?php foreach ($courses as $course): ?>
                                    <div class="grid-item">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h5 class="mb-0"><?php echo $course['course_code']; ?></h5>
                                            <span class="badge bg-<?php echo $course['lab'] ? 'info' : 'primary'; ?>">
                                                <?php echo $course['lab'] ? 'Laboratory' : 'Lecture'; ?>
                                            </span>
                                        </div>
                                        <p class="mb-3"><?php echo $course['course_name']; ?></p>
                                        <div class="d-flex justify-content-end">
                                            <button class="btn btn-sm btn-primary me-2" onclick="editCourse(<?php echo $course['id']; ?>, '<?php echo $course['course_code']; ?>', '<?php echo $course['course_name']; ?>')">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteCourse(<?php echo $course['id']; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar -->
            <div class="col-md-2 sidebar-right p-0">
                <div class="d-flex flex-column">
                    <div class="p-3 text-white">
                        <h4>Professor Status</h4>
                    </div>
                    <div class="p-3">
                        <?php
                        $professors = getAllProfessors($conn);
                        foreach ($professors as $professor):
                        ?>
                        <div class="professor-status-card mb-3">
                            <div class="d-flex align-items-center">
                                <img src="<?php 
                                    echo !empty($professor['profile_image']) && $professor['profile_image'] !== 'placeholder.png'
                                        ? 'uploads/' . htmlspecialchars($professor['profile_image']) 
                                        : 'uploads/placeholder.png'; 
                                ?>" alt="<?php echo htmlspecialchars($professor['name']); ?>" class="professor-image-small me-2">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1"><?php echo $professor['name']; ?></h6>
                                    <select class="form-select form-select-sm status-select" 
                                            onchange="updateProfessorStatus(<?php echo $professor['id']; ?>, this.value)">
                                        <option value="Present" <?php echo getProfessorStatus($conn, $professor['id']) == 'Present' ? 'selected' : ''; ?>>Present</option>
                                        <option value="Absent" <?php echo getProfessorStatus($conn, $professor['id']) == 'Absent' ? 'selected' : ''; ?>>Absent</option>
                                        <option value="On Leave" <?php echo getProfessorStatus($conn, $professor['id']) == 'On Leave' ? 'selected' : ''; ?>>On Leave</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Modals -->

        <?php include 'modals/add_schedule_modal.php'; ?>
        <?php include 'modals/room_professor_modal.php'; ?>
        <?php include 'modals/course_modal.php'; ?>
        <?php include 'modals/confirm_delete_modal.php'; ?>

    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script> -->
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="scripts/script.js"></script>
    <script src="scripts/filter.js"></script>
    <script>
        // Auto-dismiss alerts
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 3000);
            });
            
            // Scroll button functionality
            const scrollTopBtn = document.getElementById('scrollTopBtn');
            const scrollBottomBtn = document.getElementById('scrollBottomBtn');
            
            scrollTopBtn.addEventListener('click', function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
            
            scrollBottomBtn.addEventListener('click', function() {
                window.scrollTo({
                    top: document.body.scrollHeight,
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html>