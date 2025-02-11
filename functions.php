<?php
// Get all rooms from database
function getAllRooms($conn) {
    $sql = "SELECT * FROM rooms ORDER BY id ASC";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Get all professors from database
function getAllProfessors($conn) {
    $sql = "SELECT * FROM professors ORDER BY id ASC";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Get all courses from database
function getAllCourses($conn) {
    $sql = "SELECT * FROM courses ORDER BY id ASC";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Get all schedules with related information
function getSchedules($conn) {
    $sql = "SELECT s.*, 
            p.name AS professor_name, 
            p.profile_image,
            r.name AS room_name
            FROM schedules s
            LEFT JOIN professors p ON s.professor_id = p.id
            LEFT JOIN rooms r ON s.room_id = r.id";
            
    $result = mysqli_query($conn, $sql);
    
    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }
    
    $schedules = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $schedules[] = $row;
    }
    
    return $schedules;
}

function getProfessorStatus($conn, $professorId) {
    $sql = "SELECT professor_status FROM schedules 
            WHERE professor_id = ? 
            ORDER BY id DESC LIMIT 1";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $professorId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        return $row['professor_status'];
    }
    
    return 'Present'; // Default status
}

function updateProfessorStatus($conn, $professorId, $status) {
    $sql = "UPDATE schedules SET professor_status = ? 
            WHERE professor_id = ? AND DATE(created_at) = CURDATE()";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $professorId);
    return $stmt->execute();
}
?>