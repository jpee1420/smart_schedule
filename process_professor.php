<?php
session_start();
require_once 'config.php';
require_once 'functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'updateStatus') {
        $professorId = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $status = isset($_POST['status']) ? $_POST['status'] : '';
        
        // Validate status
        $validStatuses = ['Present', 'Absent', 'On Leave'];
        if (!in_array($status, $validStatuses)) {
            echo json_encode(['success' => false, 'message' => 'Invalid status']);
            exit;
        }

        // Update all schedules for this professor
        $sql = "UPDATE schedules SET professor_status = ? WHERE professor_id = ?";
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("si", $status, $professorId);
            $success = $stmt->execute();
            
            if ($success) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Database update failed']);
            }
            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to prepare statement']);
        }
        exit;
    }

    $name = $conn->real_escape_string($_POST['name']);
    $profile_image = 'placeholder.png'; // Default image
    
    // Handle image upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['profile_image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $new_filename = uniqid() . '.' . $ext;
            $upload_path = 'uploads/' . $new_filename;
            
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_path)) {
                $profile_image = $new_filename;
            }
        }
    }
    
    if (isset($_POST['id'])) {
        // Update existing professor
        $id = (int)$_POST['id'];
        $image_sql = isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0 
            ? ", profile_image = '$profile_image'" 
            : "";
        $sql = "UPDATE professors SET name = '$name' $image_sql WHERE id = $id";
        $message = 'Professor updated successfully!';
    } else {
        // Add new professor
        $sql = "INSERT INTO professors (name, profile_image) VALUES ('$name', '$profile_image')";
        $message = 'Professor added successfully!';
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
    
    // Check if professor is being used in schedules
    $check_sql = "SELECT COUNT(*) as count FROM schedules WHERE professor_id = $id";
    $result = $conn->query($check_sql);
    $row = $result->fetch_assoc();
    
    if ($row['count'] > 0) {
        $_SESSION['message'] = 'Cannot delete professor: They have assigned schedules';
        $_SESSION['message_type'] = 'danger';
    } else {
        // Get professor image
        $img_sql = "SELECT profile_image FROM professors WHERE id = $id";
        $img_result = $conn->query($img_sql);
        $professor = $img_result->fetch_assoc();
        
        $sql = "DELETE FROM professors WHERE id = $id";
        if ($conn->query($sql)) {
            // Delete profile image if not default
            if ($professor['profile_image'] !== 'placeholder.png') {
                @unlink('uploads/' . $professor['profile_image']);
            }
            $_SESSION['message'] = 'Professor deleted successfully!';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Error deleting professor: ' . $conn->error;
            $_SESSION['message_type'] = 'danger';
        }
    }
}

header('Location: index.php');
exit();

$conn->close();
?>