<?php
require_once 'db.php';

$current_date = date('Y-m-d');
$schedules = $conn->query("SELECT schedules.*, rooms.name AS room_name, professors.name AS professor_name, professors.profile_image
                           FROM schedules
                           JOIN rooms ON schedules.room_id = rooms.id
                           JOIN professors ON schedules.professor_id = professors.id");

$schedules_list = [];
while ($schedule = $schedules->fetch_assoc()) {
    $schedules_list[] = $schedule;
}
$page1_schedules = array_slice($schedules_list, 0, 4); // First 4 schedules
$page2_schedules = array_slice($schedules_list, 4);    // Remaining schedules (up to 3)
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Schedule Display</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .gradient-header {
            background: linear-gradient(to right, #007bff, #6c757d);
            color: white;
            padding: 10px;
            text-align: center;
            position: relative;
        }

        .quadrant-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-template-rows: 1fr 1fr;
            gap: 20px;
            height: calc(100vh - 150px); /* Adjust based on header height */
            padding: 20px;
        }

        .card {
            margin: 0;
            height: 100%;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .room-status {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .room-status.ongoing {
            color: #28a745;
        }

        .room-status.upcoming {
            color: #ffc107;
        }

        .room-status.completed {
            color: #6c757d;
        }

        .professor-img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #007bff;
        }

        .clock {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 24px;
            font-weight: bold;
        }

        .page {
            display: none;
        }

        .page.active {
            display: block;
        }

        .card-header {
            background: #f8f9fa;
            font-size: 1.2rem;
            font-weight: bold;
        }

        .card-body {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .professor-info {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }

        /* Regular page layout for page 2 */
        .regular-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px;
        }

        .page-controls {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }

        .switch-button {
            background: linear-gradient(to right, #007bff, #6c757d);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 16px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .switch-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.2);
        }

        .switch-button:active {
            transform: translateY(0);
        }

        .page-indicator {
            position: fixed;
            bottom: 20px;
            left: 20px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 5px 15px;
            border-radius: 15px;
            font-size: 14px;
        }
    </style>
</head>

<body>

<div class="gradient-header">
    <h1>Smart Schedule Display</h1>
    <span id="currentDate"><?= $current_date ?></span>
    <div id="clock" class="clock"></div>
</div>

<div class="container-fluid">
    <!-- Page 1: Quadrant layout -->
    <div id="page1" class="page active">
        <div class="quadrant-container">
            <?php foreach ($page1_schedules as $schedule):
                $current_time = date('H:i:s');
                $start_time = $schedule['start_time'];
                $end_time = $schedule['end_time'];
                ?>
                <div class="card">
                    <div class="card-header">
                        <?= $schedule['room_name'] ?>
                    </div>
                    <div class="card-body">
                        <div>
                            <h4 class="card-title"><?= $schedule['subject'] ?></h4>
                            <p class="mb-2"><strong>Time:</strong> <?= $schedule['start_time'] ?> - <?= $schedule['end_time'] ?></p>
                            <p class="mb-2"><strong>Day:</strong> <?= $schedule['day'] ?></p>
                        </div>
                        <div class="professor-info">
                            <img src="prof-img/<?= $schedule['profile_image'] ?>" alt="Professor" class="professor-img">
                            <div>
                                <strong>Professor:</strong><br>
                                <?= $schedule['professor_name'] ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>


    <!-- Page 2: Regular grid layout -->
    <div id="page2" class="page">
        <div class="quadrant-container">
            <?php foreach ($page2_schedules as $schedule):
                $current_time = date('H:i:s');
                $start_time = $schedule['start_time'];
                $end_time = $schedule['end_time'];
                ?>
                <div class="card">
                    <div class="card-header">
                        <?= $schedule['room_name'] ?>
                    </div>
                    <div class="card-body">
                        <div>
                            <h4 class="card-title"><?= $schedule['subject'] ?></h4>
                            <p class="mb-2"><strong>Time:</strong> <?= $schedule['start_time'] ?> - <?= $schedule['end_time'] ?></p>
                            <p class="mb-2"><strong>Day:</strong> <?= $schedule['day'] ?></p>
                        </div>
                        <div class="professor-info">
                            <img src="prof-img/<?= $schedule['profile_image'] ?>" alt="Professor" class="professor-img">
                            <div>
                                <strong>Professor:</strong><br>
                                <?= $schedule['professor_name'] ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="page-controls">
    <button id="switchPageBtn" class="switch-button">Next Page</button>
</div>

<div class="page-indicator">
    Page <span id="currentPageIndicator">1</span> of 2
</div>

<script>
// Real-time clock function
function updateClock() {
    const now = new Date();
    const hours = now.getHours().toString().padStart(2, '0');
    const minutes = now.getMinutes().toString().padStart(2, '0');
    const seconds = now.getSeconds().toString().padStart(2, '0');
    const clock = document.getElementById('clock');
    clock.textContent = `${hours}:${minutes}:${seconds}`;
}

// Update the clock every second
setInterval(updateClock, 1000);

// Call the function to set initial time
updateClock();

// Page switching logic
let currentPage = 1;
let autoSwitchInterval;

function updatePageIndicator() {
    document.getElementById('currentPageIndicator').textContent = currentPage;
}

function switchPage() {
    const page1 = document.getElementById('page1');
    const page2 = document.getElementById('page2');
    
    if (currentPage === 1) {
        page1.classList.remove('active');
        page2.classList.add('active');
        currentPage = 2;
    } else {
        page2.classList.remove('active');
        page1.classList.add('active');
        currentPage = 1;
    }
    
    updatePageIndicator();
}

function startAutoSwitch() {
    autoSwitchInterval = setInterval(switchPage, 15000);
}

document.getElementById('switchPageBtn').addEventListener('click', function() {
    // Clear the existing interval
    clearInterval(autoSwitchInterval);
    
    // Perform the page switch
    switchPage();
    
    // Restart the automatic switching
    startAutoSwitch();
});

startAutoSwitch();

// Switch between pages every 15 seconds
</script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
