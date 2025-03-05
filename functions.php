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
            r.name AS room_name,
            c.course_name AS course
            FROM schedules s
            LEFT JOIN professors p ON s.professor_id = p.id
            LEFT JOIN rooms r ON s.room_id = r.id
            LEFT JOIN courses c ON s.course_id = c.id";
            
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

function checkScheduleConflict($conn, $professor_id, $room_id, $day, $start_time, $end_time, $schedule_id = null) {
    $conflicts = array();
    
    // Base SQL for checking time overlap
    $sql = "SELECT s.*, 
            p.name as professor_name, 
            r.name as room_name,
            c.course_name,
            TIME_FORMAT(s.start_time, '%h:%i %p') as formatted_start_time,
            TIME_FORMAT(s.end_time, '%h:%i %p') as formatted_end_time
            FROM schedules s
            JOIN professors p ON s.professor_id = p.id
            JOIN rooms r ON s.room_id = r.id
            JOIN courses c ON s.course_id = c.id
            WHERE (
                (s.start_time < ? AND s.end_time > ?)
                OR (? < s.end_time AND ? > s.start_time)
                AND s.day = ?
            )";
    
    if ($schedule_id) {
        $sql .= " AND s.id != ?";
    }

    // Prepare the base statement
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return ["Database error: " . $conn->error];
    }

    // Check professor conflicts
    $professor_sql = $sql . " AND s.professor_id = ?";
    $stmt = $conn->prepare($professor_sql);
    
    if ($schedule_id) {
        $stmt->bind_param("sssssii", $end_time, $start_time, $start_time, $end_time, $day, $schedule_id, $professor_id);
    } else {
        $stmt->bind_param("sssssi", $end_time, $start_time, $start_time, $end_time, $day, $professor_id);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $conflicts[] = "Professor {$row['professor_name']} already has a class ({$row['course_name']}) scheduled on {$day} from {$row['formatted_start_time']} to {$row['formatted_end_time']}";
        }
    }

    // Check room conflicts
    $room_sql = $sql . " AND s.room_id = ?";
    $stmt = $conn->prepare($room_sql);
    
    if ($schedule_id) {
        $stmt->bind_param("sssssii", $end_time, $start_time, $start_time, $end_time, $day, $schedule_id, $room_id);
    } else {
        $stmt->bind_param("sssssi", $end_time, $start_time, $start_time, $end_time, $day, $room_id);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $conflicts[] = "Room {$row['room_name']} is already booked for {$row['course_name']} with Prof. {$row['professor_name']} on {$day} from {$row['formatted_start_time']} to {$row['formatted_end_time']}";
        }
    }

    return $conflicts;
}

// Search rooms by name
function searchRooms($conn, $searchTerm) {
    $searchTerm = "%$searchTerm%";
    $sql = "SELECT * FROM rooms WHERE name LIKE ? ORDER BY id ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Search professors by name
function searchProfessors($conn, $searchTerm) {
    $searchTerm = "%$searchTerm%";
    $sql = "SELECT * FROM professors WHERE name LIKE ? ORDER BY id ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Search courses by code or name
function searchCourses($conn, $searchTerm) {
    $searchTerm = "%$searchTerm%";
    $sql = "SELECT * FROM courses WHERE course_code LIKE ? OR course_name LIKE ? ORDER BY id ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Search schedules by course, professor, or room
function searchSchedules($conn, $searchTerm) {
    $searchTerm = "%$searchTerm%";
    $sql = "SELECT s.*, 
            p.name AS professor_name, 
            p.profile_image,
            r.name AS room_name,
            c.course_name AS course
            FROM schedules s
            LEFT JOIN professors p ON s.professor_id = p.id
            LEFT JOIN rooms r ON s.room_id = r.id
            LEFT JOIN courses c ON s.course_id = c.id
            WHERE p.name LIKE ? OR r.name LIKE ? OR c.course_name LIKE ? OR c.course_code LIKE ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}
?>