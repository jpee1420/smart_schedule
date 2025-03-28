<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    
    if (isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        $sql = "UPDATE rooms SET name = '$name' WHERE id = $id";
        $message = 'Room updated successfully!';
    } else {

        $sql = "INSERT INTO rooms (name) VALUES ('$name')";
        $message = 'Room added successfully!';
    }
    
    if ($conn->query($sql)) {
        $_SESSION['message'] = $message;
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Error: ' . $conn->error;
        $_SESSION['message_type'] = 'danger';
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'delete') {
    $id = (int)$_GET['id'];
    
    // Check if room is being used in schedules
    $check_sql = "SELECT COUNT(*) as count FROM schedules WHERE room_id = $id";
    $result = $conn->query($check_sql);
    $row = $result->fetch_assoc();
    
    if ($row['count'] > 0) {
        $_SESSION['message'] = 'Cannot delete room: It is being used in schedules';
        $_SESSION['message_type'] = 'danger';
    } else {
        $sql = "DELETE FROM rooms WHERE id = $id";
        if ($conn->query($sql)) {
            $_SESSION['message'] = 'Room deleted successfully!';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Error deleting room: ' . $conn->error;
            $_SESSION['message_type'] = 'danger';
        }
    }
}

header('Location: index.php');
exit();
?>