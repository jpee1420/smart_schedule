<?php
// Include database check
require_once 'db_check.php';  // This already includes config.php and starts the session

// Include models
require_once 'queries.php';

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
            r.name as room_name, c.course_name as course, c.course_code
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
        // Get the smallest upcoming start time
        $sql = "SELECT MIN(start_time) as next_start_time 
                FROM schedules 
                WHERE day = ? 
                AND start_time > ?";
                
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $currentDayFormat, $fiveMinutesFromNow);
        $stmt->execute();
        $minResult = $stmt->get_result();
        $nextStartTime = $minResult->fetch_assoc()['next_start_time'];
        
        // If there's a next start time, get all schedules for that hour
        if ($nextStartTime) {
            error_log("Next upcoming start time: " . $nextStartTime);
            
            // Extract hour from the next start time
            $nextHour = date('H', strtotime($nextStartTime));
            $hourStart = $nextHour . ':00:00';
            $hourEnd = $nextHour . ':59:59';
            
            $sql = "SELECT s.*, p.name as professor_name, p.profile_image, s.professor_status, 
                    r.name as room_name, c.course_name as course, c.course_code
                    FROM schedules s 
                    JOIN professors p ON s.professor_id = p.id 
                    JOIN rooms r ON s.room_id = r.id 
                    JOIN courses c ON s.course_id = c.id 
                    WHERE s.day = ? 
                    AND s.start_time >= ? 
                    AND s.start_time <= ?
                    ORDER BY s.start_time ASC";
                    
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $currentDayFormat, $hourStart, $hourEnd);
            $stmt->execute();
            $result = $stmt->get_result();
            
            error_log("Upcoming schedules for hour " . $nextHour . " found: " . $result->num_rows);
        }
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
    <link href="styles.css" rel="stylesheet">
    <!-- Add meta refresh every minute -->
    <meta http-equiv="refresh" content="120">
    <style>
        /* ===== LAYOUT & STRUCTURE ===== */
        .carousel-container {
            padding-top: 100px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 9999;
            padding: 2rem;
            background-color: #f6f1f1;
        }

        .carousel-item {
            padding: 4.5rem;
            top: 60px;
        }

        .schedule-slide {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
            margin: 0 auto;
            max-width: 1200px;
            max-height: calc(100vh - 250px);
            overflow-y: auto;
            padding: 5px;
            scrollbar-width: thin;
        }
        
        /* ===== UNIVERSITY HEADER ===== */
        .university-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background-color: #870100;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
        }
        
        .university-name {
            font-size: 1.5rem;
            font-weight: bold;
            margin: 0;
            font-family: 'Algerian';
        }
        
        .university-name .university-text {
            font-weight: bold;
            display: inline-block;
            margin-right: 10px;
            background: linear-gradient(to top, #ff8800, #ffff00);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            text-fill-color: transparent;
        }
        
        .university-name .college-text {
            background: linear-gradient(to top, #888888, #ffffff);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            text-fill-color: transparent;
        }
        
        .logo {
            height: 4rem;
            width: auto;
            margin-left: 15px;
            margin-right: 15px;
            object-fit: cover;
            display: block;
            transition: all 0.3s ease;
        }
        
        /* ===== CARD STYLES ===== */
        .schedule-card {
            height: 100%;
            transition: all 0.3s ease;
            margin: 0;
        }
        
        .schedule-card.inactive {
            opacity: 0.5;
        }
        
        .card-body {
            position: relative;
            padding-bottom: 40px;
            display: flex;
            flex-direction: column;
        }
        
        .card-title {
            margin-bottom: 0.25rem;
        }
        
        .card-subtitle {
            min-height: 38px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            margin-bottom: 0.25rem;
        }
        
        .schedule-card .d-flex {
            margin-bottom: 1rem !important;
        }
        
        /* Card header with professor image */
        .card-header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            margin-bottom: 0.1rem;
        }
        
        .card-header-text {
            flex: 1;
            padding-right: 15px;
        }
        
        .professor-image {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #f8f9fa;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        /* Status indicators */
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
        }
        
        /* ===== UI CONTROLS ===== */
        .back-button {
            position: fixed;
            top: 100px !important;
            left: 70px !important;
            z-index: 10000;
            opacity: 1;
            transition: all 0.3s ease;
            font-size: 1.2rem;
            padding: 0.5rem;
            background-color: rgba(108, 117, 125, 0.8);
            color: white;
            border: none;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .back-button:hover {
            background-color: rgba(108, 117, 125, 1);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }
        
        .current-time {
            position: fixed;
            top: 100px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 10000;
            font-size: 1.5rem;
            font-weight: bold;
            background-color: rgba(255, 255, 255, 0.9);
            padding: 0.5rem 1rem;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        /* Carousel navigation */
        .carousel-control-prev,
        .carousel-control-next {
            width: 5%;
            background-color: rgba(0,0,0,0.2);
        }
        
        .carousel-controls {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 10001;
        }
        
        .carousel-controls .btn {
            padding: 8px 15px;
            font-weight: 500;
            border-radius: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.2s ease;
        }
        
        .carousel-controls .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .carousel-controls .btn-prev {
            background-color: #f8f9fa;
            color: #495057;
        }
        
        .carousel-controls .btn-next {
            background-color: #6c757d;
            color: white;
            border-color: #6c757d;
        }
        
        /* Duration control */
        .duration-control {
            position: fixed;
            bottom: 20px;
            left: 20px;
            background-color: rgba(255, 255, 255, 0.9);
            padding: 10px;
            border-radius: 5px;
            z-index: 10000;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            max-width: 300px;
        }
        
        .duration-control.collapsed {
            max-width: 50px;
            overflow: hidden;
            cursor: pointer;
        }
        
        .duration-control.collapsed .duration-content {
            display: none;
        }
        
        .duration-toggle {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            cursor: pointer;
            margin-bottom: 5px;
        }
        
        .duration-content {
            transition: opacity 0.3s ease;
        }
        
        .form-range {
            width: 100%;
            margin: 10px 0;
        }
        
        /* Progress bar */
        .carousel-progress-container {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            height: 8px;
            display: block;
            z-index: 10000;
        }
        
        .progress {
            height: 100%;
            border-radius: 0;
            background-color: rgba(0, 0, 0, 0.1);
        }
        
        #carouselProgress {
            width: 0%;
            height: 100%;
            background-color: rgba(40, 167, 69, 0.9);
            transition: width 0.1s linear;
        }
        
        /* Hidden elements */
        .countdown-timer {
            display: none;
        }
        
        /* ===== NO SCHEDULE MESSAGE ===== */
        .no-schedule {
            text-align: center;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 3rem;
            font-size: 1.5rem;
            color: #6c757d;
            border: 2px solid #dee2e6;
            border-radius: 10px;
            background-color: rgba(255, 255, 255, 0.9);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 500px;
        }
        
        .no-schedule i {
            font-size: 4rem;
            color: #6c757d;
            margin-bottom: 1.5rem;
        }
        
        /* ===== DEVELOPER SIGNATURE ===== */
        .developer-signature {
            position: fixed;
            bottom: 5px;
            padding-right: 1rem;
            color: rgb(51, 51, 51);
            font-size: 0.9rem;
            z-index: 10000;
            text-align: right;
            font-style: italic;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
            pointer-events: none;
        }
        
        .developer-signature span {
            display: block;
            margin: 2px 0;
        }
        
        /* ===== SCROLLBARS ===== */
        .schedule-slide::-webkit-scrollbar {
            width: 8px;
        }
        
        .schedule-slide::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.05);
            border-radius: 4px;
        }
        
        .schedule-slide::-webkit-scrollbar-thumb {
            background-color: rgba(0, 0, 0, 0.2);
            border-radius: 4px;
        }
        
        .schedule-slide::-webkit-scrollbar-thumb:hover {
            background-color: rgba(0, 0, 0, 0.3);
        }
        
        /* ===== FULLSCREEN MODE ===== */
        body.browser-fullscreen .back-button,
        body.browser-fullscreen .duration-control,
        body.browser-fullscreen .carousel-controls {
            opacity: 0;
            pointer-events: none;
            visibility: hidden;
        }
        
        body.browser-fullscreen .schedule-slide {
            max-width: 95vw;
        }

        body.browser-fullscreen .developer-signature {
            background-color: rgb(124, 124, 124);
            width: 100%;
            position: fixed;
            bottom: 10px;
            left: 0;
            /* right: 20px; */
            padding: 10px, 20px;
            text-align: right;
            color: white;
            font-size: 0.8rem;
            font-style: italic;
        }
        
        /* ===== RESPONSIVE STYLES ===== */
        /* For 1280x720 resolution */
        @media screen and (max-width: 1280px), screen and (max-height: 720px) {
            /* Layout adjustments */
            .carousel-item {
                padding: 3.5rem 2rem;
                top: 50px;
            }
            
            .schedule-slide {
                max-width: 1000px;
                gap: 1rem;
                max-height: calc(100vh - 200px);
            }
            
            /* University header */
            .university-header {
                padding: 5px 10px;
            }
            
            .university-name {
                font-size: 1.4rem;
                margin: 0 10px;
            }
            
            .university-text {
                margin-right: 6px;
            }
            
            .logo {
                max-height: 50px;
            }
            
            /* Card styles */
            .schedule-card {
                min-height: 220px;
            }
            
            .card-body {
                padding: 1rem;
            }
            
            .professor-image {
                width: 70px;
                height: 70px;
            }
            
            .card-title {
                font-size: 1.1rem;
            }
            
            .card-subtitle {
                min-height: 34px;
                font-size: 0.9rem;
            }
            
            .card-text {
                position: relative;
                margin-bottom: 0.5rem;
                font-size: 0.7rem;
                bottom: 10px;
            }
            
            .card-subtitle + .text {
                bottom: 10px;
                position: relative;
            }
            
            .schedule-status {
                scale: 0.9;
            }
            
            /* UI controls */
            .current-time {
                top: 85px;
                font-size: 1.2rem;
            }
            
            .back-button {
                top: 80px;
                left: 50px;
                width: 35px;
                height: 35px;
                font-size: 1rem;
            }
            
            .countdown-timer {
                bottom: 15px;
                font-size: 0.9rem;
            }
            
            .carousel-controls {
                bottom: 15px;
            }
            
            .carousel-controls .btn {
                padding: 6px 12px;
                font-size: 0.9rem;
            }
            
            .carousel-progress-container {
                height: 6px;
            }
            
            .duration-control {
                bottom: 15px;
                left: 15px;
                padding: 8px;
            }
            
            .duration-toggle {
                width: 25px;
                height: 25px;
            }
            
            /* Signature */
            .developer-signature {
                font-size: 0.8rem;
                bottom: 10px;
                right: 15px;
            }
            
            .developer-signature span {
                margin: 1px 0;
            }
        }
        
        /* Specific styles for fullscreen in different resolutions */
        @media screen and (min-height: 800px) {
            body.browser-fullscreen .schedule-card {
                min-height: 280px;
                height: calc(32vh - 40px);
            }
            
            body.browser-fullscreen .card-body {
                padding: 1.25rem;
            }
            
            body.browser-fullscreen .schedule-slide {
                max-height: calc(100vh - 200px);
            }
            
            body.browser-fullscreen .professor-image {
                width: 100px;
                height: 100px;
            }
        }
        
        @media screen and (min-height: 1000px) {
            body.browser-fullscreen .schedule-card {
                min-height: 340px;
                height: calc(35vh - 40px);
            }
            
            body.browser-fullscreen .card-body {
                padding: 1.5rem;
            }
            
            body.browser-fullscreen .schedule-slide {
                max-height: calc(100vh - 180px);
            }
        }
        
        @media screen and (min-height: 1200px) {
            body.browser-fullscreen .schedule-card {
                min-height: 380px;
                height: calc(37vh - 40px);
            }
            
            body.browser-fullscreen .card-body {
                padding: 1.75rem;
            }
            
            body.browser-fullscreen .schedule-slide {
                max-height: calc(100vh - 160px);
            }
        }
        
        /* Fullscreen at 720p resolution */
        @media screen and (max-height: 720px) {
            body.browser-fullscreen .university-header {
                padding: 10px 5px;
            }
            
            body.browser-fullscreen .university-name {
                font-size: 1.3rem;
            }
            
            body.browser-fullscreen .logo {
                max-height: 50px;
            }
            
            body.browser-fullscreen .schedule-card {
                min-height: 200px;
                height: calc(50vh - 120px);
            }
            
            body.browser-fullscreen .card-body {
                padding: 0.75rem;
            }
            
            body.browser-fullscreen .professor-image {
                width: 100px;
                height: 100px;
            }
            
            body.browser-fullscreen .card-title {
                font-size: 1.1rem;
            }
            
            body.browser-fullscreen .card-subtitle + .text {
                top: 1px;
                position: relative;
            }
            
            body.browser-fullscreen .card-text {
                top: 10px;
                position: relative;
                margin-bottom: 0.4rem;
                font-size: 0.85rem;
            }
            
            body.browser-fullscreen .card-subtitle {
                font-size: 0.85rem;
                min-height: 30px;
            }
            
            body.browser-fullscreen .schedule-slide {
                max-height: calc(100vh - 160px);
                max-width: 90vw;
            }
            
            body.browser-fullscreen .carousel-item {
                padding: 3rem 1.5rem;
            }
            
            body.browser-fullscreen .professor-status-badge {
                padding: 0.2rem 0.4rem;
                font-size: 0.7rem;
                bottom: 8px;
                left: 15px;
            }
            
            body.browser-fullscreen .current-time {
                top: 75px;
            }
            
            body.browser-fullscreen .developer-signature {
                font-size: 0.75rem;
                bottom: 8px;
                right: 10px;
            }
            
            body.browser-fullscreen .carousel-controls .btn {
                padding: 5px 10px;
                font-size: 0.85rem;
            }
            
            body.browser-fullscreen .carousel-progress-container {
                height: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="university-header">
        <img src="images/ul-logo.png" alt="University Logo" class="university-logo logo">
        <h1 class="university-name">
            <span class="university-text">University of Luzon</span> 
            <span class="college-text">College of Computer Studies</span>
        </h1>
        <img src="images/cs-logo.png" alt="Department Logo" class="department-logo logo">
    </div>

    <div class="current-time" id="currentTime">
        <?php echo date('l, h:i:s A'); ?>
    </div>
    
    <a href="index.php" class="btn btn-secondary back-button" title="Back to Dashboard">
        <i class="fas fa-arrow-left"></i>
    </a>

    <!-- Duration Control -->
    <div id="durationControl" class="duration-control collapsed">
        <div class="duration-toggle">
            <i class="fas fa-clock"></i>
        </div>
        <div class="duration-content">
            <div class="d-flex align-items-center">
                <span class="me-2 small">Speed:</span>
                <input type="range" class="form-range" id="durationSlider" min="3" max="30" step="1" value="10">
                <span class="ms-2 small" id="durationValue">10s</span>
            </div>
        </div>
    </div>

    <!-- Countdown Timer -->
    <div id="countdownTimer" class="countdown-timer">
        Next slide in: <span id="countdown">10</span>s
    </div>

    <!-- Progress Bar -->
    <div class="carousel-progress-container">
        <div class="progress">
            <div id="carouselProgress" class="progress-bar bg-secondary" role="progressbar" style="width: 0%"></div>
        </div>
    </div>

    <div class="carousel-container">
        <?php if (empty($schedules)): ?>
            <div class="no-schedule">
                <i class="fas fa-calendar-times fa-3x mb-3"></i>
                <p>No schedules for <?php echo $currentDay; ?> at <?php echo date('h:i A'); ?></p>
            </div>
        <?php else: ?>
        <div id="scheduleCarousel" class="carousel slide" data-bs-ride="carousel">
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
                                <div class="card-header-content">
                                    <div class="card-header-text">
                                        <h5 class="card-title mb-2"><?php echo htmlspecialchars($schedule['course_code']); ?></h5>
                                        <div class="card-subtitle mb-1"><?php echo htmlspecialchars($schedule['course']); ?></div>
                                        <small class="text">Prof. <?php echo htmlspecialchars($schedule['professor_name']); ?></small>
                                    </div>
                                    <img src="<?php 
                                        echo !empty($schedule['profile_image']) && $schedule['profile_image'] !== 'placeholder.png'
                                            ? 'uploads/' . htmlspecialchars($schedule['profile_image']) 
                                            : 'uploads/placeholder.png'; 
                                    ?>" class="professor-image" alt="Professor">
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
                                $badgeClass = 'bg-success text-white';
                                if ($schedule['professor_status'] === 'Absent') {
                                    $badgeClass = 'bg-danger text-white';
                                } else if ($schedule['professor_status'] === 'On Leave') {
                                    $badgeClass = 'bg-warning text-dark';
                                } else if ($schedule['professor_status'] === 'On Meeting') {
                                    $badgeClass = 'bg-info text-dark';
                                }
                                ?>
                                <span class="professor-status-badge <?php echo $badgeClass; ?>" 
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
            <!-- Add manual navigation buttons -->
            <div class="carousel-controls">
                <button class="btn btn-outline-secondary btn-prev" id="prevSlide">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="btn btn-outline-secondary btn-next" id="nextSlide">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Developer Signature -->
    <div class="developer-signature">
        <span>Developed by</span>
        <span>Juan Paolo Picar & Ieson Louis Muyano</span>
        <span>Q1, 2025</span>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
    <script>
        // Global variables for carousel control
        let carouselDuration = 10; // Default duration in seconds
        let carouselInstance;
        let countdownInterval; // Interval for countdown timer
        let countdownValue = carouselDuration; // Current countdown value
        
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            // Load saved duration from localStorage if available
            if (localStorage.getItem('carouselDuration')) {
                carouselDuration = parseInt(localStorage.getItem('carouselDuration'));
                document.getElementById('durationSlider').value = carouselDuration;
                document.getElementById('durationValue').textContent = carouselDuration + 's';
                countdownValue = carouselDuration;
                document.getElementById('countdown').textContent = countdownValue;
            } else {
                // Set initial countdown display
                document.getElementById('countdown').textContent = carouselDuration;
            }
            
            // Initialize carousel without auto-cycling
            const carousel = document.getElementById('scheduleCarousel');
            if (carousel) {
                // Initialize the carousel properly but without auto-cycling
                carousel.removeAttribute('data-bs-ride');
                carousel.setAttribute('data-bs-interval', 'false'); // Disable automatic cycling
                
                // Create carousel instance with no autoplay
                carouselInstance = new bootstrap.Carousel(carousel, {
                    interval: false,  // Disable auto cycling
                    pause: false,     // Don't pause on hover
                    wrap: true        // Loop through slides
                });
            }
            
            // Also check if browser is in fullscreen mode
            detectFullscreen();
            
            // Setup duration slider
            const durationSlider = document.getElementById('durationSlider');
            durationSlider.addEventListener('input', function() {
                updateCarouselDuration(this.value);
            });
            
            // Start the countdown timer
            startCountdown();

            // Add duration control toggle
            const durationControl = document.getElementById('durationControl');
            const durationToggle = durationControl.querySelector('.duration-toggle');
            
            durationToggle.addEventListener('click', function() {
                durationControl.classList.toggle('collapsed');
            });
            
            // Setup manual navigation buttons
            const prevButton = document.getElementById('prevSlide');
            const nextButton = document.getElementById('nextSlide');
            
            if (prevButton && nextButton) {
                prevButton.addEventListener('click', function() {
                    // Manual navigation implementation
                    const carousel = document.getElementById('scheduleCarousel');
                    const bsCarousel = bootstrap.Carousel.getInstance(carousel);
                    if (bsCarousel) {
                        bsCarousel.prev();
                    }
                    
                    // Reset the countdown timer
                    resetCountdown();
                });
                
                nextButton.addEventListener('click', function() {
                    // Manual navigation implementation
                    const carousel = document.getElementById('scheduleCarousel');
                    const bsCarousel = bootstrap.Carousel.getInstance(carousel);
                    if (bsCarousel) {
                        bsCarousel.next();
                    }
                    
                    // Reset the countdown timer
                    resetCountdown();
                });
            }
        });
        
        // Function to update carousel duration
        function updateCarouselDuration(seconds) {
            carouselDuration = parseInt(seconds);
            document.getElementById('durationValue').textContent = seconds + 's';
            
            // Update countdown - this will control when slides advance
            countdownValue = carouselDuration;
            document.getElementById('countdown').textContent = countdownValue;
            resetCountdown();
            
            // Save to localStorage for persistence
            localStorage.setItem('carouselDuration', carouselDuration);
        }
        
        // Function to start the countdown timer
        function startCountdown() {
            // Clear any existing interval
            if (countdownInterval) {
                clearInterval(countdownInterval);
                countdownInterval = null;
            }
            
            // Reset countdown value
            countdownValue = carouselDuration;
            
            // Reset progress bar to 0% immediately
            const progressBar = document.getElementById('carouselProgress');
            if (progressBar) {
                // Force a repaint by setting to 0 after a very brief timeout
                progressBar.style.transition = 'none';
                progressBar.style.width = '0%';
                
                // Force browser to repaint before starting animation
                setTimeout(function() {
                    progressBar.style.transition = 'width 0.1s linear';
                    
                    // Calculate how often to update the progress bar for smooth animation
                    const updateInterval = 50; // Update every 50ms for smoother animation
                    const steps = (carouselDuration * 1000) / updateInterval;
                    const incrementPerStep = 100 / steps;
                    let currentProgress = 0;
                    
                    // Use a recursive setTimeout approach for precise timing
                    function updateProgress() {
                        currentProgress += incrementPerStep;
                        
                        // Update progress bar
                        if (progressBar) {
                            progressBar.style.width = Math.min(currentProgress, 100) + '%';
                        }
                        
                        // When progress reaches 100%
                        if (currentProgress >= 100) {
                            // Wait just enough time to let the bar fill completely
                            setTimeout(function() {
                                // Get the carousel
                                const carousel = document.getElementById('scheduleCarousel');
                                if (carousel) {
                                    const bsCarousel = bootstrap.Carousel.getInstance(carousel);
                                    if (bsCarousel) {
                                        // Listen for slide transition end
                                        carousel.addEventListener('slid.bs.carousel', function onSlideEnd() {
                                            carousel.removeEventListener('slid.bs.carousel', onSlideEnd);
                                            
                                            // Start next countdown after transition completes
                                            setTimeout(function() {
                                                startCountdown();
                                            }, 100); // Small delay to ensure a clean start
                                        }, { once: true });
                                        
                                        // Start slide change
                                        bsCarousel.next();
                                    }
                                }
                            }, 50);
                            
                            // Clear the interval
                            clearInterval(countdownInterval);
                            countdownInterval = null;
                        } else {
                            // Continue updating
                            countdownInterval = setTimeout(updateProgress, updateInterval);
                        }
                    }
                    
                    // Start progress updates
                    countdownInterval = setTimeout(updateProgress, updateInterval);
                }, 10);
            }
        }
        
        // Function to reset the countdown timer
        function resetCountdown() {
            if (countdownInterval) {
                clearTimeout(countdownInterval);
                countdownInterval = null;
            }
            
            // Start a new countdown
            startCountdown();
        }
        
        // Function to update current time
        function updateTime() {
            const now = new Date();
            const options = { 
                weekday: 'long',
                month: 'long',
                day: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: true
            };
            const dateStr = now.toLocaleString('en-US', options);
            // Format: "Monday, 03/25/2024, 2:30:45 PM"
            const formattedDate = dateStr.replace(/(\d+)\/(\d+)\/(\d+)/, '$1/$2/$3');
            document.getElementById('currentTime').textContent = formattedDate;
        }

        // Detect browser fullscreen (F11) mode
        function detectFullscreen() {
            if (window.innerHeight === screen.height) {
                document.body.classList.add('browser-fullscreen');
                
                // Hide back button and duration control in browser fullscreen
                const backButton = document.querySelector('.back-button');
                const durationControl = document.querySelector('.duration-control');
                
                if (backButton) backButton.style.display = 'none';
                if (durationControl) durationControl.style.display = 'none';
            } else {
                document.body.classList.remove('browser-fullscreen');
                
                // Show back button and duration control when not in browser fullscreen
                const backButton = document.querySelector('.back-button');
                const durationControl = document.querySelector('.duration-control');
                
                if (backButton) backButton.style.display = 'flex';
                if (durationControl) durationControl.style.display = 'block';
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
                    // Don't add inactive class to any schedule status
                } else if (currentTime < startTime) {
                    // More than 5 minutes before start
                    statusElement.textContent = 'Upcoming';
                    statusElement.className = 'schedule-status status-upcoming';
                    // Don't add inactive class to any schedule status
                } else if (currentTime >= startTime && currentTime <= endTime) {
                    statusElement.textContent = 'Current';
                    statusElement.className = 'schedule-status status-current';
                    // Don't add inactive class to any schedule status
                } else {
                    statusElement.textContent = 'Ended';
                    statusElement.className = 'schedule-status status-ended';
                    // Just change visual appearance but don't make inactive
                    card.classList.add('inactive');
                }
            });
        }

        // Update time and schedule statuses every second
        setInterval(() => {
            updateTime();
            updateScheduleStatuses();
        }, 1000);

        // Initial update
        updateTime();
        updateScheduleStatuses();

        // Add keyboard shortcut for ESC key to exit fullscreen
        document.addEventListener('keydown', function(event) {
            // Remove Escape key handling for fullscreen - we want to stay in fullscreen
        });

        // Handle carousel slide events - remove this listener as we're now using one-time listeners
        document.getElementById('scheduleCarousel')?.addEventListener('slid.bs.carousel', function(event) {
            // This is handled by the one-time event listener in startCountdown
        });
    </script>
</body>
</html> 