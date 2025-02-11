<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add or update schedule
    $subject = $conn->real_escape_string($_POST['subject']);
    $professor_id = (int)$_POST['professor_id'];
    $room_id = (int)$_POST['room_id'];
    $start_time = $conn->real_escape_string($_POST['start_time']);
    $end_time = $conn->real_escape_string($_POST['end_time']);
    $day = $conn->real_escape_string($_POST['day']);
    $notes = $conn->real_escape_string($_POST['notes']);

    if (isset($_POST['id'])) {
        // Update existing schedule
        $id = (int)$_POST['id'];
        $sql = "UPDATE schedules SET 
                subject = '$subject',
                professor_id = $professor_id,
                room_id = $room_id,
                start_time = '$start_time',
                end_time = '$end_time',
                day = '$day',
                notes = '$notes'
                WHERE id = $id";
    } else {
        // Add new schedule
        $sql = "INSERT INTO schedules (subject, professor_id, room_id, start_time, end_time, day, notes)
                VALUES ('$subject', $professor_id, $room_id, '$start_time', '$end_time', '$day', '$notes')";
    }

    if ($conn->query($sql)) {
        $_SESSION['message'] = 'Schedule saved successfully!';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Error saving schedule: ' . $conn->error;
        $_SESSION['message_type'] = 'danger';
    }

} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'delete') {
    // Delete schedule
    $id = (int)$_GET['id'];
    $sql = "DELETE FROM schedules WHERE id = $id";

    if ($conn->query($sql)) {
        $_SESSION['message'] = 'Schedule deleted successfully!';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Error deleting schedule: ' . $conn->error;
        $_SESSION['message_type'] = 'danger';
    }
}

header('Location: index.php');
exit();
?>