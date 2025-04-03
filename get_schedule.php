<?php
// Include database check
require_once 'db_check.php';  // This already includes config.php and starts the session

// Include models
require_once 'queries.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    $sql = "SELECT * FROM schedules WHERE id = $id";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $schedule = $result->fetch_assoc();
        // Convert times to HTML time input format
        $schedule['start_time'] = date('H:i', strtotime($schedule['start_time']));
        $schedule['end_time'] = date('H:i', strtotime($schedule['end_time']));
        echo json_encode($schedule);
    } else {
        echo json_encode(['error' => 'Schedule not found']);
    }
} else {
    echo json_encode(['error' => 'No ID provided']);
}
?>