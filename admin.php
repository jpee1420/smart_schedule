<?php
// admin.php

// Include database connection
require_once 'db.php';

// Check if form is submitted to add a schedule
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_id = $_POST['room_id'];
    $professor_id = $_POST['professor_id'];
    $subject = $_POST['subject'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $date = $_POST['date'];
    $notes = $_POST['notes'];

    $sql = "INSERT INTO schedules (room_id, professor_id, subject, start_time, end_time, date, notes)
            VALUES ('$room_id', '$professor_id', '$subject', '$start_time', '$end_time', '$date', '$notes')";
    $conn->query($sql);
    header('Location: admin.php');
}

// Fetch rooms and professors for the form
$rooms = $conn->query("SELECT * FROM rooms");
$professors = $conn->query("SELECT * FROM professors");

// Fetch all schedules
$schedules = $conn->query("SELECT schedules.*, rooms.name AS room_name, professors.name AS professor_name
                           FROM schedules
                           JOIN rooms ON schedules.room_id = rooms.id
                           JOIN professors ON schedules.professor_id = professors.id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Smart Schedule</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .gradient-header {
            background: linear-gradient(to right, #007bff, #6c757d);
            color: white;
            padding: 15px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="gradient-header">
    <h1>Smart Schedule Admin Panel</h1>
</div>

<div class="container my-5">
    <h3>Add New Schedule</h3>
    <form method="POST" action="admin.php">
        <div class="form-group">
            <label for="subject">Subject</label>
            <input type="text" class="form-control" id="subject" name="subject" required>
        </div>
        <div class="form-group">
            <label for="room_id">Room</label>
            <select class="form-control" id="room_id" name="room_id" required>
                <?php while ($room = $rooms->fetch_assoc()): ?>
                    <option value="<?= $room['id'] ?>"><?= $room['name'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="professor_id">Professor</label>
            <select class="form-control" id="professor_id" name="professor_id" required>
                <?php while ($professor = $professors->fetch_assoc()): ?>
                    <option value="<?= $professor['id'] ?>"><?= $professor['name'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="start_time">Start Time</label>
            <input type="time" class="form-control" id="start_time" name="start_time" required>
        </div>
        <div class="form-group">
            <label for="end_time">End Time</label>
            <input type="time" class="form-control" id="end_time" name="end_time" required>
        </div>
        <div class="form-group">
            <label for="date">Date</label>
            <input type="date" class="form-control" id="date" name="date" required>
        </div>
        <div class="form-group">
            <label for="notes">Notes</label>
            <textarea class="form-control" id="notes" name="notes"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Add Schedule</button>
    </form>

    <hr>

    <h3>Existing Schedules</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Subject</th>
                <th>Room</th>
                <th>Professor</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Date</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($schedule = $schedules->fetch_assoc()): ?>
                <tr>
                    <td><?= $schedule['subject'] ?></td>
                    <td><?= $schedule['room_name'] ?></td>
                    <td><?= $schedule['professor_name'] ?></td>
                    <td><?= $schedule['start_time'] ?></td>
                    <td><?= $schedule['end_time'] ?></td>
                    <td><?= $schedule['date'] ?></td>
                    <td><?= $schedule['notes'] ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>