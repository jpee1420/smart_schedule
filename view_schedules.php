<?php
session_start();
require_once 'config.php';
require_once 'functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Schedules - Schedule Management System</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
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
    </style>
</head>
<body>
    <a href="index.php" class="btn btn-primary back-button">
        <i class="fas fa-arrow-left"></i> Back to Dashboard
    </a>
    <button class="btn btn-secondary fullscreen-button" onclick="toggleFullscreen()">
        <i class="fas fa-expand"></i> Toggle Fullscreen
    </button>

    <div class="carousel-container">
        <div id="scheduleCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
            <div class="carousel-indicators">
                <?php
                $schedules = getSchedules($conn);
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

            <button class="carousel-control-prev" type="button" data-bs-target="#scheduleCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#scheduleCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
    <script>
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
        document.getElementById('scheduleCarousel').addEventListener('slid.bs.carousel', function(event) {
            const totalSlides = <?php echo $total_slides; ?>;
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