<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_code = $conn->real_escape_string($_POST['course_code']);
    $course_name = $conn->real_escape_string($_POST['course_name']);
    
    // Check if course code ends with 'L' to determine if it's a lab course
    $is_lab = substr($course_code, -1) === 'L' ? 1 : 0;
    
    if (isset($_POST['id'])) {
        // Update existing course
        $id = (int)$_POST['id'];
        $sql = "UPDATE courses SET course_code = '$course_code', course_name = '$course_name', lab = $is_lab WHERE id = $id";
        $message = 'Course updated successfully!';
    } else {
        // Add new course
        $sql = "INSERT INTO courses (course_code, course_name, lab) VALUES ('$course_code', '$course_name', $is_lab)";
        $message = 'Course added successfully!';
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
    $sql = "DELETE FROM courses WHERE id = $id";
    
    if ($conn->query($sql)) {
        $_SESSION['message'] = 'Course deleted successfully!';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Error deleting course: ' . $conn->error;
        $_SESSION['message_type'] = 'danger';
    }
}

header('Location: index.php#courses');
exit();
?>