-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 04, 2025 at 07:20 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: 'smart_schedule'
--

-- --------------------------------------------------------

--
-- Table structure for table 'courses'
--

CREATE TABLE courses (
  id int(11) NOT NULL,
  course_code varchar(10) NOT NULL,
  course_name varchar(100) NOT NULL,
  lab tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table 'courses'
--

INSERT INTO courses (id, course_code, course_name, lab) VALUES
('CCS112', 'Logic Design and Digital Computing', 0),
('CCS111', 'Introduction to Computing', 0),
('CCS121L', 'Fundamentals of Problem Solving and Computing', 1),
('CCS122', 'Computer Organization', 0),
('CCS123', 'PC Troubleshooting and Maintenance', 0),
('CCS131', 'Technopreneurship', 0),
('CCS211L', 'Programming 2', 1),
('CCS212', 'Presentation Skills and Technical Writing', 0),
('CCS213L', 'Hardware Technologies', 1),
('CCS214', 'Data Communication and Networking', 0),
('CCS221', 'Web Design and Development', 0),
('CCS222', 'Operating Systems', 0),
('CCS223', 'Information Systems Security', 0),
('CCS224L', 'SQL Scripting', 1),
('CCS225', 'Graphic Design', 0),
('CCS231', 'Human and Computer Interaction', 0),
('CCS311', 'Data Structures and Alghorithm', 0),
('CCS312', 'Discrete Mathematics', 0),
('CCS313L', 'Object-Oriented Programming', 1),
('CCS314', 'Software Engineering', 0),
('CCS315', 'Network Administration', 0),
('CCS321', 'Database Administration', 0),
('CCS322L', 'Application Development and Emerging Technologies', 1),
('CCS323', 'Principles of Accounting and Financial Processes', 0),
('CCS324', 'Multimedia Systems', 0),
('CCS325', 'Project Management', 0),
('CCS326', 'System Management', 0),
('CCS331', 'IT Professionals and Social Issues', 0), 
('CCS411', 'Field Trips and Seminar', 0), 
('CCS413', 'Information Systems Security Administration', 0), 
('CCS414L', 'CAD Application', 1), 
('CCS415', 'Thesis A', 0),
('CCS416', 'Language Theory and Automata', 0),
('CCS417', 'Design and Implementation of Programming Languages', 0),
('CCS422', 'OJT 200', 0),
('CCS423', 'Thesis B', 0),
('GE11', 'Purposive Communication', 0),
('GE12', 'Ethics', 0),
('GE13', 'The Contemporary World', 0),
('GE14', 'Mathemcatics in the Modern World', 0),
('GE15', 'Art Appreciation', 0),
('GE16', 'Reading in Philippine History', 0),
('GE17', 'Science, Technology and Society', 0),
('GE18', 'Understanding the Self', 0),
('GE19', 'Life and Works of Rizal', 0),
('GE ELec 1', 'Living in the IT Era', 0),
('GE ELec 2', 'Reading Visual Art', 0),
('GE ELec 3', 'Happiness', 0),
('ELE003', 'Intelligent Systems', 0),
('ELE004L', 'SAP Administration', 1),
('ELE005L', 'Data Analytics', 1),
('PE11', 'Physical Activities, Training and Fitness 1', 0),
('PE12', 'Physical Activities, Training and Fitness 2', 0),
('PE13', 'Physical Activities, Training and Fitness 3', 0),
('PE14', 'Physical Activities, Training and Fitness 3', 0),
('NSTP11','National Service Training Program 1', 0),
('NSTP12','National Service Training Program 2', 0);




-- --------------------------------------------------------
--
-- Table structure for table 'professors'
--

CREATE TABLE professors (
  id int(11) NOT NULL,
  name varchar(255) NOT NULL,
  profile_image varchar(255) DEFAULT 'placeholder.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table 'professors'
--

INSERT INTO professors (id, name, profile_image) VALUES
(1, 'Rina Cabansag', 'placeholder.png'),
(2, 'Diosdado Caronongan', 'placeholder.png'),
(3, 'Andrew Caronongan', 'placeholder.png'),
(4, 'Lorenz Camachoo', 'placeholder.png'),
(5, 'Eugevar Silang', 'placeholder.png'),
(6, 'Francis Lanz Cruzada', 'placeholder.png'),
(7, 'Cee Jay Lomibao', 'placeholder.png'),
(8, 'Ruzleen Soriano', 'placeholder.png');

-- --------------------------------------------------------

--
-- Table structure for table 'rooms'
--

CREATE TABLE rooms (
  id int(11) NOT NULL,
  name varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table 'rooms'
--

INSERT INTO rooms (id, name) VALUES
(1, 'LR1'),
(2, 'LR2'),
(3, 'LR4'),
(4, 'LR5'),
(5, 'MM3'),
(6, 'Grad 1'),
(7, 'Grad 2');

-- --------------------------------------------------------

--
-- Table structure for table 'schedules'
--

CREATE TABLE schedules (
  id int(11) NOT NULL,
  room_id int(11) DEFAULT NULL,
  professor_id int(11) DEFAULT NULL,
  course_id int(11) DEFAULT NULL,
  start_time time DEFAULT NULL,
  end_time time DEFAULT NULL,
  day enum('MWF','TTH','Sat') DEFAULT 'MWF',
  professor_status enum('Present','On Leave','Absent') DEFAULT 'Present'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table 'courses'
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table 'professors'
--
ALTER TABLE `professors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table 'rooms'
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table 'schedules'
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `professor_id` (`professor_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table 'courses'
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table 'professors'
--
ALTER TABLE `professors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table 'rooms'
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table 'schedules'
--
ALTER TABLE `schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table 'schedules'
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `schedules_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`),
  ADD CONSTRAINT `schedules_ibfk_2` FOREIGN KEY (`professor_id`) REFERENCES `professors` (`id`),
  ADD CONSTRAINT `schedules_ibfk_3` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
