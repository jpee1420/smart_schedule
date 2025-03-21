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

    // Calculate time 5 minutes from now
    $fiveMinutesFromNow = date('H:i:s', strtotime($currentTime . ' + 5 minutes'));

    $sql = "SELECT s.*, p.name as professor_name, p.profile_image, s.professor_status, 
            r.name as room_name, c.course_name as course 
            FROM schedules s 
            JOIN professors p ON s.professor_id = p.id 
            JOIN rooms r ON s.room_id = r.id 
            JOIN courses c ON s.course_id = c.id 
            WHERE s.day = ? 
            AND (
                -- Current classes
                (s.start_time <= ? AND s.end_time >= ?)
                OR
                -- Classes starting within next 5 minutes
                (s.start_time > ? AND s.start_time <= ?)
            )
            ORDER BY s.start_time ASC";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $currentDayFormat, $currentTime, $currentTime, $currentTime, $fiveMinutesFromNow);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Debug information
    error_log("Current and starting soon schedules found: " . $result->num_rows);
    
    if($result->num_rows === 0) {
        // If no current or starting soon schedule, get the next upcoming schedule
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
        $stmt->bind_param("ss", $currentDayFormat, $fiveMinutesFromNow);
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
    <link href="css/all.min.css" rel="stylesheet"> 
    <!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet"> -->
    <link href="styles.css" rel="stylesheet">
    <!-- Add meta refresh every minute -->
    <!-- <meta http-equiv="refresh" content="60"> -->
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
            transition: all 0.3s ease;
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
        
        /* Move carousel items down in fullscreen mode */
        .fullscreen .carousel-item {
            padding-top: 4rem; /* Extra top padding in fullscreen mode */
        }
        
        .current-time {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 10000; /* Increased z-index to be above fullscreen */
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
        /* Add new styles for schedule status */
        .schedule-card.inactive {
            opacity: 0.5;
            pointer-events: none;
        }
        .schedule-status {
            position: absolute;
            bottom: 10px;
            right: 10px;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: bold;
            z-index: 1;
        }
        .card-body {
            position: relative;
            padding-bottom: 40px; /* Make space for the status badge */
        }
        .status-current {
            background-color: #28a745;
            color: white;
        }
        .status-upcoming {
            background-color: #ffc107;
            color: black;
        }
        .status-ended {
            background-color: #dc3545;
            color: white;
        }
        /* Style for professor status badge */
        .professor-status-badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.25rem;
            position: absolute;
            bottom: 10px;
            left: 20px;
            background-color: #198754;  /* Same as Bootstrap's bg-success */
            color: #fff;
        }
        
        /* Dynamic card sizes for different screen resolutions when in fullscreen mode */
        @media screen and (min-height: 800px) {
            body.browser-fullscreen .schedule-card {
                min-height: 320px;
                height: calc(35vh - 40px); /* Responsive height based on viewport */
            }
            
            body.browser-fullscreen .card-body {
                padding: 1.25rem;
            }
        }
        
        @media screen and (min-height: 1000px) {
            body.browser-fullscreen .schedule-card {
                min-height: 380px;
                height: calc(38vh - 40px);
            }
            
            body.browser-fullscreen .card-body {
                padding: 1.5rem;
            }
        }
        
        @media screen and (min-height: 1200px) {
            body.browser-fullscreen .schedule-card {
                min-height: 420px;
                height: calc(40vh - 40px);
            }
            
            body.browser-fullscreen .card-body {
                padding: 1.75rem;
            }
        }
        
        /* Additional styles for proper resizing in fullscreen */
        body.browser-fullscreen .schedule-slide {
            max-width: 95vw;
        }
    </style>
</head>
<body>
    <div class="current-time" id="currentTime">
        <?php echo date('l, h:i:s A'); ?>
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
        <div id="scheduleCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="10000">
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
                        <div class="card schedule-card" 
                             data-start="<?php echo $schedule['start_time']; ?>" 
                             data-end="<?php echo $schedule['end_time']; ?>">
                            <div class="card-body">
                                <div class="schedule-status"></div>
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
                                <span class="professor-status-badge" 
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
            <!-- Carousel controls removed -->
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
    <script>
        // Function to update current time
        function updateTime() {
            const now = new Date();
            const options = { 
                weekday: 'long',
                hour: 'numeric',
                minute: '2-digit',
                second: '2-digit',
                hour12: true 
            };
            document.getElementById('currentTime').textContent = now.toLocaleString('en-US', options);
        }

        // Detect browser fullscreen (F11) mode
        function detectFullscreen() {
            if (window.innerHeight === screen.height) {
                document.body.classList.add('browser-fullscreen');
            } else {
                document.body.classList.remove('browser-fullscreen');
            }
        }

        // Listen for fullscreen change events
        window.addEventListener('resize', detectFullscreen);
        
        // Initial fullscreen check
        detectFullscreen();

        // Function to check schedule status
        function updateScheduleStatuses() {
            const now = new Date();
            const currentTime = now.getHours().toString().padStart(2, '0') + ':' + 
                              now.getMinutes().toString().padStart(2, '0') + ':' + 
                              now.getSeconds().toString().padStart(2, '0');

            document.querySelectorAll('.schedule-card').forEach(card => {
                const startTime = card.dataset.start;
                const endTime = card.dataset.end;
                const statusElement = card.querySelector('.schedule-status');

                // Calculate time 5 minutes before start
                const startDate = new Date();
                const [startHours, startMinutes] = startTime.split(':');
                startDate.setHours(parseInt(startHours), parseInt(startMinutes), 0);
                
                const fiveMinutesBefore = new Date(startDate.getTime() - 5 * 60000);
                const currentDate = new Date();
                currentDate.setSeconds(0); // Ignore seconds for this comparison
                
                // Convert to comparable format for the main time check
                if (currentDate >= fiveMinutesBefore && currentTime < startTime) {
                    // Within 5 minutes before start time
                    statusElement.textContent = 'Starting Soon';
                    statusElement.className = 'schedule-status status-upcoming';
                    card.classList.remove('inactive');
                } else if (currentTime < startTime) {
                    // More than 5 minutes before start
                    statusElement.textContent = 'Upcoming';
                    statusElement.className = 'schedule-status status-upcoming';
                    card.classList.remove('inactive');
                } else if (currentTime >= startTime && currentTime <= endTime) {
                    statusElement.textContent = 'Current';
                    statusElement.className = 'schedule-status status-current';
                    card.classList.remove('inactive');
                } else {
                    statusElement.textContent = 'Ended';
                    statusElement.className = 'schedule-status status-ended';
                    card.classList.add('inactive');
                }
            });

            // Check if all schedules in the current slide are inactive
            const carousel = document.getElementById('scheduleCarousel');
            const activeSlide = carousel.querySelector('.carousel-item.active');
            if (activeSlide) {
                const activeCards = activeSlide.querySelectorAll('.schedule-card:not(.inactive)');
                if (activeCards.length === 0) {
                    // All schedules in current slide are inactive, move to next slide
                    const carouselInstance = bootstrap.Carousel.getInstance(carousel);
                    carouselInstance.next();
                }
            }
        }

        // Update time and schedule statuses every second
        setInterval(() => {
            updateTime();
            updateScheduleStatuses();
        }, 1000);

        // Initial update
        updateTime();
        updateScheduleStatuses();

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
                }, 10000);
            }
        });
    </script>
</body>
</html> 