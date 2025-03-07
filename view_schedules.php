<?php
session_start();
require_once 'config.php';
require_once 'functions.php';

// Set timezone to Philippines
date_default_timezone_set('Asia/Manila');

// Get current day and time
$currentDay = date('l'); // Returns Sunday, Monday, etc.
$currentTime = date('H:i:s');

// Debug timezone and time
error_log("Timezone: " . date_default_timezone_get());
error_log("Server Time: " . $currentTime);

// Convert day name to MWF or TTH format
function getDayFormat($dayName) {
    switch($dayName) {
        case 'Monday':
        case 'Wednesday':
        case 'Friday':
            return 'MWF';
        case 'Tuesday':
        case 'Thursday':
            return 'TTH';
        case 'Saturday':
            return 'Sat';
        default:
            return '';
    }
}

$currentDayFormat = getDayFormat($currentDay);

// Function to get current and upcoming schedules
function getCurrentSchedules($conn, $currentTime, $currentDayFormat) {
    // If it's Sunday or the day format is empty, return no schedules
    if (empty($currentDayFormat)) {
        return array();
    }

    // Debug information
    error_log("Current Time: " . $currentTime);
    error_log("Current Day Format: " . $currentDayFormat);

    $sql = "SELECT s.*, p.name as professor_name, p.profile_image, s.professor_status, 
            r.name as room_name, c.course_name as course 
            FROM schedules s 
            JOIN professors p ON s.professor_id = p.id 
            JOIN rooms r ON s.room_id = r.id 
            JOIN courses c ON s.course_id = c.id 
            WHERE s.day = ? 
            AND s.start_time <= ? 
            AND s.end_time >= ?
            ORDER BY s.start_time ASC";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $currentDayFormat, $currentTime, $currentTime);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Debug information
    error_log("Current schedules found: " . $result->num_rows);
    
    if($result->num_rows === 0) {
        // If no current schedule, get the next upcoming schedule
        $sql = "SELECT s.*, p.name as professor_name, p.profile_image, s.professor_status, 
                r.name as room_name, c.course_name as course 
                FROM schedules s 
                JOIN professors p ON s.professor_id = p.id 
                JOIN rooms r ON s.room_id = r.id 
                JOIN courses c ON s.course_id = c.id 
                WHERE s.day = ? 
                AND s.start_time > ? 
                ORDER BY s.start_time ASC 
                LIMIT 4";
                
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $currentDayFormat, $currentTime);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Debug information
        error_log("Upcoming schedules found: " . $result->num_rows);
    }
    
    $schedules = $result->fetch_all(MYSQLI_ASSOC);
    
    // Debug each schedule found
    foreach ($schedules as $schedule) {
        error_log("Schedule ID: " . $schedule['id'] . 
                 ", Time: " . $schedule['start_time'] . " - " . $schedule['end_time'] . 
                 ", Day: " . $schedule['day']);
    }
    
    return $schedules;
}

// Get current schedules
$schedules = getCurrentSchedules($conn, $currentTime, $currentDayFormat);

// Debug information in the page
if (empty($schedules)) {
    error_log("No schedules found for " . $currentDayFormat . " at " . $currentTime);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Current Schedules - Schedule Management System</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
    <!-- Add meta refresh every minute -->
    <meta http-equiv="refresh" content="60">
    <style>
        .carousel-item {
            padding: 2rem;
        }
        .schedule-slide {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin: 0 auto;
            max-width: 1200px;
        }
        .schedule-card {
            height: 100%;
        }
        .carousel-control-prev,
        .carousel-control-next {
            width: 5%;
            background-color: rgba(0,0,0,0.2);
        }
        .carousel-indicators {
            bottom: -50px;
        }
        .carousel-indicators button {
            background-color: #6c757d;
        }
        .back-button {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1000;
        }
        .fullscreen-button {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
        .carousel-container {
            padding: 60px 0;
        }
        .fullscreen {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: white;
            z-index: 9999;
            padding: 2rem;
        }
        .current-time {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            font-size: 1.5rem;
            font-weight: bold;
            background-color: rgba(255, 255, 255, 0.9);
            padding: 0.5rem 1rem;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .no-schedule {
            text-align: center;
            padding: 2rem;
            font-size: 1.5rem;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="current-time">
        <?php echo date('l, h:i A'); ?>
    </div>
    
    <a href="index.php" class="btn btn-primary back-button">
        <i class="fas fa-arrow-left"></i> Back to Dashboard
    </a>
    <button class="btn btn-secondary fullscreen-button" onclick="toggleFullscreen()">
        <i class="fas fa-expand"></i> Toggle Fullscreen
    </button>

    <div class="carousel-container">
        <?php if (empty($schedules)): ?>
            <div class="no-schedule">
                <i class="fas fa-calendar-times fa-3x mb-3"></i>
                <p>No schedules for <?php echo $currentDay; ?> at <?php echo date('h:i A'); ?></p>
            </div>
        <?php else: ?>
        <div id="scheduleCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
            <div class="carousel-indicators">
                <?php
                $total_slides = ceil(count($schedules) / 4);
                for ($i = 0; $i < $total_slides; $i++):
                ?>
                <button type="button" data-bs-target="#scheduleCarousel" data-bs-slide-to="<?php echo $i; ?>" 
                        <?php echo $i === 0 ? 'class="active"' : ''; ?>></button>
                <?php endfor; ?>
            </div>

            <div class="carousel-inner">
                <?php
                $chunks = array_chunk($schedules, 4);
                foreach ($chunks as $index => $chunk):
                ?>
                <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                    <div class="schedule-slide">
                        <?php foreach ($chunk as $schedule): ?>
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
                                    <?php 
                                        echo date('h:i A', strtotime($schedule['start_time'])) . ' - ' . 
                                             date('h:i A', strtotime($schedule['end_time'])); 
                                    ?>
                                </p>
                                <p class="card-text">
                                    <i class="fas fa-calendar me-2"></i>
                                    <?php echo $schedule['day']; ?>
                                </p>
                                <p class="card-text">
                                    <i class="fas fa-door-open me-2"></i>
                                    Room <?php echo htmlspecialchars($schedule['room_name']); ?>
                                </p>
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
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <?php if (count($schedules) > 4): ?>
            <button class="carousel-control-prev" type="button" data-bs-target="#scheduleCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#scheduleCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
    <script>
        // Update current time every second
        function updateCurrentTime() {
            const timeDisplay = document.querySelector('.current-time');
            setInterval(() => {
                const now = new Date();
                const options = { weekday: 'long', hour: 'numeric', minute: 'numeric', second: 'numeric', hour12: true };
                timeDisplay.textContent = now.toLocaleString('en-US', options);
            }, 1000);
        }

        // Initialize time display
        updateCurrentTime();

        function toggleFullscreen() {
            const container = document.querySelector('.carousel-container');
            const button = document.querySelector('.fullscreen-button i');
            const fullscreenButton = document.querySelector('.fullscreen-button');

            if (!container.classList.contains('fullscreen')) {
                // Enter fullscreen
                container.classList.add('fullscreen');
                button.classList.remove('fa-expand');
                button.classList.add('fa-compress');
                fullscreenButton.setAttribute('title', 'Exit Fullscreen');
            } else {
                // Exit fullscreen
                container.classList.remove('fullscreen');
                button.classList.remove('fa-compress');
                button.classList.add('fa-expand');
                fullscreenButton.setAttribute('title', 'Enter Fullscreen');
            }
        }

        // Add keyboard shortcut for ESC key to exit fullscreen
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const container = document.querySelector('.carousel-container');
                const button = document.querySelector('.fullscreen-button i');
                const fullscreenButton = document.querySelector('.fullscreen-button');
                
                if (container.classList.contains('fullscreen')) {
                    container.classList.remove('fullscreen');
                    button.classList.remove('fa-compress');
                    button.classList.add('fa-expand');
                    fullscreenButton.setAttribute('title', 'Enter Fullscreen');
                }
            }
        });

        // Restart carousel when it reaches the end
        document.getElementById('scheduleCarousel')?.addEventListener('slid.bs.carousel', function(event) {
            const totalSlides = <?php echo isset($total_slides) ? $total_slides : 0; ?>;
            if (event.to === totalSlides - 1) {
                setTimeout(() => {
                    const carousel = bootstrap.Carousel.getInstance(document.getElementById('scheduleCarousel'));
                    carousel.to(0);
                }, 5000);
            }
        });
    </script>
</body>
</html> 