CREATE DATABASE smart_schedule;

USE smart_schedule;

CREATE TABLE rooms (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL
);

CREATE TABLE professors (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    profile_image VARCHAR(255) DEFAULT 'placeholder.png'
);

CREATE TABLE schedules (
    id INT PRIMARY KEY AUTO_INCREMENT,
    room_id INT,
    professor_id INT,
    subject VARCHAR(255) NOT NULL,
    start_time TIME,
    end_time TIME,
    date DATE,
    notes TEXT,
    FOREIGN KEY (room_id) REFERENCES rooms(id),
    FOREIGN KEY (professor_id) REFERENCES professors(id)
);

USE smart_schedule;

INSERT INTO rooms (name) VALUES
('Room 101'),
('Room 102'),
('Room 103'),
('Room 104');

INSERT INTO professors (name, profile_image) VALUES
('Prof. John Doe', 'prof1.png'),
('Prof. Jane Smith', 'prof2.png'),
('Prof. Mark Davis', 'prof3.png'),
('Prof. Emily White', 'prof4.png');

INSERT INTO schedules (room_id, professor_id, subject, start_time, end_time, date, notes) VALUES
(1, 1, 'Mathematics 101', '08:00:00', '10:00:00', '2024-11-06', 'Chapter 1: Introduction to Algebra'),
(2, 2, 'Physics 202', '10:00:00', '12:00:00', '2024-11-06', 'Chapter 5: Laws of Motion'),
(3, 3, 'Chemistry 301', '12:00:00', '14:00:00', '2024-11-06', 'Chapter 3: Organic Chemistry'),
(4, 4, 'Computer Science 101', '14:00:00', '16:00:00', '2024-11-06', 'Chapter 2: Data Structures');