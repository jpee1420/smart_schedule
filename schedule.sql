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

USE smart_schedule;

-- Insert 7 rooms
INSERT INTO rooms (name) VALUES
('LR1'),
('LR2'),
('LR4'),
('LR4'),
('MM3'),
('Grad 1'),
('Room');

-- Insert 5 professors
INSERT INTO professors (name, profile_image) VALUES
('Diosdado Caronongan', 'prof1.png'),
('Andrew Caronongan', 'prof2.png'),
('Rina Cabansag', 'prof3.png'),
('Eugevar Silang', 'prof4.png'),
('Lorenz Camacho', 'prof5.png');

-- Insert schedules (sample subjects and times)
INSERT INTO schedules (room_id, professor_id, subject, start_time, end_time, date, notes) VALUES
(1, 1, 'Introduction to Programming', '08:00:00', '10:00:00', '2024-11-06', 'Introductory course to programming'),
(2, 2, 'Data Structures', '10:00:00', '12:00:00', '2024-11-06', 'Covering stacks, queues, trees, and graphs'),
(3, 3, 'Web Development', '12:00:00', '14:00:00', '2024-11-06', 'HTML, CSS, JavaScript, PHP basics'),
(4, 4, 'Database Systems', '14:00:00', '16:00:00', '2024-11-06', 'Introduction to relational databases'),
(5, 5, 'Software Engineering', '08:00:00', '10:00:00', '2024-11-06', 'Software development life cycle'),
(6, 1, 'Algorithms', '10:00:00', '12:00:00', '2024-11-06', 'Fundamental algorithms and problem-solving techniques'),
(7, 2, 'Operating Systems', '12:00:00', '14:00:00', '2024-11-06', 'Process management, memory management, file systems');
