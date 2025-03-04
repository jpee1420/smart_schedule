<?php
require_once 'config.php';
require_once 'functions.php';

// Set headers to prevent caching
header('Cache-Control: no-cache, must-revalidate');
header('Expires: 0');
header('Content-Type: application/json');

try {
    // Check if search term is provided
    if (isset($_GET['term']) && !empty($_GET['term'])) {
        $searchTerm = $_GET['term'];
        $type = isset($_GET['type']) ? $_GET['type'] : 'all';
        
        $results = [];
        
        // Search based on type
        switch ($type) {
            case 'rooms':
                $results = searchRooms($conn, $searchTerm);
                break;
            case 'professors':
                $results = searchProfessors($conn, $searchTerm);
                break;
            case 'courses':
                $results = searchCourses($conn, $searchTerm);
                break;
            case 'schedules':
                $results = searchSchedules($conn, $searchTerm);
                break;
            case 'all':
            default:
                // Search all types
                $results = [
                    'rooms' => searchRooms($conn, $searchTerm),
                    'professors' => searchProfessors($conn, $searchTerm),
                    'courses' => searchCourses($conn, $searchTerm),
                    'schedules' => searchSchedules($conn, $searchTerm)
                ];
                break;
        }
        
        // Return results as JSON
        echo json_encode(['success' => true, 'data' => $results]);
    } else {
        // Return error if no search term provided
        echo json_encode(['success' => false, 'error' => 'No search term provided']);
    }
} catch (Exception $e) {
    // Return error if an exception occurs
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?> 