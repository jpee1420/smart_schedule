-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 21, 2025 at 07:29 AM
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
-- Database: `smart_schedule`

CREATE DATABASE `smart_schedule`;
USE smart_schedule;
--

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `course_code` varchar(10) NOT NULL,
  `course_name` varchar(100) NOT NULL,
  `lab` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `course_code`, `course_name`, `lab`) VALUES
(1, 'CCS111', 'Introduction to Computing', 0),
(2, 'CCS112', 'Logic Design and Digital Computing', 0),
(3, 'CCS121L', 'Fundamentals of Problem Solving and Computing', 1),
(4, 'CCS122', 'Computer Organization', 0),
(5, 'CCS123', 'PC Troubleshooting and Maintenance', 0),
(6, 'CCS131', 'Technopreneurship', 0),
(7, 'CCS211L', 'Programming 2', 1),
(8, 'CCS212', 'Presentation Skills and Technical Writing', 0),
(9, 'CCS213L', 'Hardware Technologies', 1),
(10, 'CCS214', 'Data Communication and Networking', 0),
(11, 'CCS221', 'Web Design and Development', 0),
(12, 'CCS222', 'Operating Systems', 0),
(13, 'CCS223', 'Information Systems Security', 0),
(14, 'CCS224L', 'SQL Scripting', 1),
(15, 'CCS225', 'Graphic Design', 0),
(16, 'CCS231', 'Human and Computer Interaction', 0),
(17, 'CCS311', 'Data Structures and Alghorithm', 0),
(18, 'CCS312', 'Discrete Mathematics', 0),
(19, 'CCS313L', 'Object-Oriented Programming', 1),
(20, 'CCS314', 'Software Engineering', 0),
(21, 'CCS315', 'Network Administration', 0),
(22, 'CCS321', 'Database Administration', 0),
(23, 'CCS322L', 'Application Development and Emerging Technologies', 1),
(24, 'CCS323', 'Principles of Accounting and Financial Processes', 0),
(25, 'CCS324', 'Multimedia Systems', 0),
(26, 'CCS325', 'Project Management', 0),
(27, 'CCS326', 'System Management', 0),
(28, 'CCS331', 'IT Professionals and Social Issues', 0),
(29, 'CCS411', 'Field Trips and Seminar', 0),
(30, 'CCS413', 'Information Systems Security Administration', 0),
(31, 'CCS414L', 'CAD Application', 1),
(32, 'CCS415', 'Thesis A', 0),
(33, 'CCS416', 'Language Theory and Automata', 0),
(34, 'CCS417', 'Design and Implementation of Programming Languages', 0),
(35, 'CCS422', 'OJT 200', 0),
(36, 'CCS423', 'Thesis B', 0),
(37, 'GE11', 'Purposive Communication', 0),
(38, 'GE12', 'Ethics', 0),
(39, 'GE13', 'The Contemporary World', 0),
(40, 'GE14', 'Mathemcatics in the Modern World', 0),
(41, 'GE15', 'Art Appreciation', 0),
(42, 'GE16', 'Reading in Philippine History', 0),
(43, 'GE17', 'Science, Technology and Society', 0),
(44, 'GE18', 'Understanding the Self', 0),
(45, 'GE19', 'Life and Works of Rizal', 0),
(46, 'GE ELec 1', 'Living in the IT Era', 0),
(47, 'GE ELec 2', 'Reading Visual Art', 0),
(48, 'GE ELec 3', 'Happiness', 0),
(49, 'ELE003', 'Intelligent Systems', 0),
(50, 'ELE004L', 'SAP Administration', 1),
(51, 'ELE005L', 'Data Analytics', 1),
(52, 'PE11', 'Physical Activities, Training and Fitness 1', 0),
(53, 'PE12', 'Physical Activities, Training and Fitness 2', 0),
(54, 'PE13', 'Physical Activities, Training and Fitness 3', 0),
(55, 'PE14', 'Physical Activities, Training and Fitness 4', 0),
(56, 'NSTP11', 'National Service Training Program 1', 0),
(57, 'NSTP12', 'National Service Training Program 2', 0),
(58, 'ELE002L', 'Data Analytics', 1);

-- --------------------------------------------------------

--
-- Table structure for table `professors`
--

CREATE TABLE `professors` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `profile_image` varchar(255) DEFAULT 'placeholder.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `professors`
--

INSERT INTO `professors` (`id`, `name`, `profile_image`) VALUES
(1, 'Rina Cabansag', 'placeholder.png'),
(2, 'Diosdado Caronongan', 'placeholder.png'),
(3, 'Andrew Caronongan', 'placeholder.png'),
(4, 'Lorenz Camachoo', 'placeholder.png'),
(5, 'Eugevar Silang', 'placeholder.png'),
(6, 'Francis Lanz Cruzada', 'placeholder.png'),
(7, 'Cee Jay Lomibao', 'placeholder.png'),
(8, 'Ruzleen Soriano', 'placeholder.png'),
(9, 'TBA', 'placeholder.png');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `name`) VALUES
(1, 'LR1'),
(2, 'LR2'),
(3, 'LR4'),
(4, 'LR5'),
(5, 'MM3'),
(6, 'Grad 1'),
(7, 'Grad 2'),
(8, 'TBA'),
(9, 'Gym'),
(10, 'Consultation');

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `id` int(11) NOT NULL,
  `room_id` int(11) DEFAULT NULL,
  `professor_id` int(11) DEFAULT NULL,
  `course_id` int(11) NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `day` enum('MWF','TTH','Sat') DEFAULT 'MWF',
  `professor_status` enum('Present','Absent','On Leave') DEFAULT 'Present'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`id`, `room_id`, `professor_id`, `course_id`, `start_time`, `end_time`, `day`, `professor_status`) VALUES
(1, 1, 3, 12, '09:00:00', '10:00:00', 'MWF', 'Present'),
(2, 1, 3, 12, '10:00:00', '11:00:00', 'MWF', 'Present'),
(3, 1, 3, 5, '11:00:00', '12:00:00', 'MWF', 'Present'),
(4, 1, 3, 1, '14:00:00', '15:00:00', 'MWF', 'Present'),
(5, 1, 4, 26, '16:00:00', '17:00:00', 'MWF', 'Present'),
(6, 1, 9, 29, '17:00:00', '18:00:00', 'MWF', 'Present'),
(7, 2, 5, 14, '07:00:00', '09:00:00', 'MWF', 'Present'),
(8, 2, 1, 11, '09:00:00', '10:00:00', 'MWF', 'Present'),
(9, 2, 1, 1, '10:00:00', '11:00:00', 'MWF', 'Present'),
(10, 2, 6, 1, '11:00:00', '12:00:00', 'MWF', 'Present'),
(11, 2, 3, 1, '12:00:00', '13:00:00', 'MWF', 'Present'),
(12, 2, 6, 3, '13:00:00', '15:00:00', 'MWF', 'Present'),
(13, 2, 5, 14, '15:00:00', '17:00:00', 'MWF', 'Present'),
(14, 2, 1, 6, '17:00:00', '18:00:00', 'MWF', 'Present'),
(15, 5, 7, 3, '07:00:00', '09:00:00', 'MWF', 'Present'),
(16, 5, 5, 11, '10:00:00', '11:00:00', 'MWF', 'Present'),
(17, 5, 7, 15, '11:00:00', '12:00:00', 'MWF', 'Present'),
(18, 5, 5, 22, '12:00:00', '13:00:00', 'MWF', 'Present'),
(19, 5, 7, 25, '13:00:00', '14:00:00', 'MWF', 'Present'),
(20, 5, 7, 15, '14:00:00', '15:00:00', 'MWF', 'Present'),
(21, 5, 4, 1, '15:00:00', '16:00:00', 'MWF', 'Present'),
(22, 5, 8, 2, '16:00:00', '17:00:00', 'MWF', 'Present'),
(23, 5, 9, 51, '17:00:00', '19:00:00', 'MWF', 'Present'),
(24, 3, 1, 23, '07:00:00', '09:00:00', 'MWF', 'Present'),
(25, 3, 4, 31, '09:00:00', '11:00:00', 'MWF', 'Present'),
(26, 3, 4, 31, '11:00:00', '13:00:00', 'MWF', 'Present'),
(27, 3, 1, 23, '13:00:00', '15:00:00', 'MWF', 'Present'),
(28, 3, 1, 23, '15:00:00', '17:00:00', 'MWF', 'Present'),
(29, 3, 4, 58, '17:00:00', '19:00:00', 'MWF', 'Present'),
(30, 4, 9, 44, '07:00:00', '08:00:00', 'MWF', 'Present'),
(31, 4, 9, 55, '08:00:00', '09:00:00', 'MWF', 'Present'),
(32, 4, 9, 53, '09:00:00', '10:00:00', 'MWF', 'Present'),
(33, 4, 8, 2, '10:00:00', '11:00:00', 'MWF', 'Present'),
(34, 4, 9, 53, '11:00:00', '12:00:00', 'MWF', 'Present'),
(35, 4, 9, 53, '12:00:00', '13:00:00', 'MWF', 'Present'),
(36, 4, 9, 55, '13:00:00', '14:00:00', 'MWF', 'Present'),
(37, 4, 4, 24, '14:00:00', '15:00:00', 'MWF', 'Present'),
(38, 4, 8, 46, '15:00:00', '16:00:00', 'MWF', 'Present'),
(39, 6, 8, 46, '07:00:00', '08:00:00', 'MWF', 'Present'),
(40, 6, 9, 42, '08:00:00', '09:00:00', 'MWF', 'Present'),
(41, 6, 9, 42, '09:00:00', '10:00:00', 'MWF', 'Present'),
(42, 6, 9, 37, '10:00:00', '11:00:00', 'MWF', 'Present'),
(43, 6, 9, 37, '11:00:00', '12:00:00', 'MWF', 'Present'),
(44, 6, 6, 46, '12:00:00', '13:00:00', 'MWF', 'Present'),
(45, 6, 9, 21, '14:00:00', '15:00:00', 'MWF', 'Present'),
(46, 7, 3, 5, '08:00:00', '09:00:00', 'MWF', 'Present'),
(47, 7, 8, 4, '09:00:00', '10:00:00', 'MWF', 'Present'),
(48, 7, 6, 46, '10:00:00', '11:00:00', 'MWF', 'Present'),
(49, 7, 2, 35, '12:00:00', '15:00:00', 'MWF', 'Present'),
(50, 7, 7, 16, '15:00:00', '16:00:00', 'MWF', 'Present'),
(51, 10, 2, 30, '11:00:00', '12:00:00', 'MWF', 'Present'),
(52, 1, 3, 5, '08:30:00', '10:00:00', 'TTH', 'Present'),
(53, 1, 3, 1, '10:00:00', '11:30:00', 'TTH', 'Present'),
(54, 1, 8, 2, '11:30:00', '13:00:00', 'TTH', 'Present'),
(55, 1, 3, 12, '13:00:00', '14:30:00', 'TTH', 'Present'),
(56, 1, 5, 22, '14:30:00', '16:00:00', 'TTH', 'Present'),
(57, 1, 7, 27, '16:00:00', '17:30:00', 'TTH', 'Present'),
(58, 2, 1, 1, '07:00:00', '08:30:00', 'TTH', 'Present'),
(59, 2, 5, 14, '08:30:00', '11:30:00', 'TTH', 'Present'),
(60, 2, 5, 11, '11:30:00', '13:00:00', 'TTH', 'Present'),
(61, 2, 4, 24, '13:00:00', '14:30:00', 'TTH', 'Present'),
(62, 2, 4, 20, '14:30:00', '16:00:00', 'TTH', 'Present'),
(63, 2, 1, 17, '16:00:00', '17:30:00', 'TTH', 'Present'),
(64, 5, 7, 3, '07:00:00', '10:00:00', 'TTH', 'Present'),
(65, 5, 7, 3, '10:00:00', '13:00:00', 'TTH', 'Present'),
(66, 5, 7, 15, '13:00:00', '14:30:00', 'TTH', 'Present'),
(67, 5, 7, 25, '14:30:00', '16:00:00', 'TTH', 'Present'),
(68, 3, 4, 50, '07:00:00', '10:00:00', 'TTH', 'Present'),
(69, 3, 1, 23, '10:00:00', '13:00:00', 'TTH', 'Present'),
(70, 3, 6, 50, '13:00:00', '16:00:00', 'TTH', 'Present'),
(71, 3, 4, 58, '16:00:00', '19:00:00', 'TTH', 'Present'),
(72, 4, 9, 48, '07:00:00', '08:30:00', 'TTH', 'Present'),
(73, 4, 9, 53, '08:30:00', '10:00:00', 'TTH', 'Present'),
(74, 4, 9, 55, '10:00:00', '11:30:00', 'TTH', 'Present'),
(75, 4, 9, 48, '11:30:00', '13:00:00', 'TTH', 'Present'),
(76, 4, 5, 24, '13:00:00', '14:30:00', 'TTH', 'Present'),
(77, 4, 1, 1, '14:30:00', '16:00:00', 'TTH', 'Present'),
(78, 4, 9, 48, '16:00:00', '17:30:00', 'TTH', 'Present'),
(79, 6, 2, 36, '08:30:00', '10:00:00', 'TTH', 'Present'),
(80, 6, 9, 44, '10:00:00', '11:30:00', 'TTH', 'Present'),
(81, 6, 2, 13, '11:30:00', '13:00:00', 'TTH', 'Present'),
(82, 6, 2, 13, '13:00:00', '14:30:00', 'TTH', 'Present'),
(83, 6, 2, 13, '14:30:00', '16:00:00', 'TTH', 'Present'),
(84, 6, 6, 46, '16:00:00', '19:00:00', 'TTH', 'Present'),
(85, 7, 9, 42, '07:00:00', '08:30:00', 'TTH', 'Present'),
(86, 7, 6, 8, '08:30:00', '10:00:00', 'TTH', 'Present'),
(87, 7, 9, 37, '10:00:00', '11:30:00', 'TTH', 'Present'),
(88, 7, 3, 16, '11:30:00', '13:00:00', 'TTH', 'Present'),
(89, 7, 8, 26, '13:00:00', '14:30:00', 'TTH', 'Present'),
(90, 7, 3, 5, '14:30:00', '16:00:00', 'TTH', 'Present'),
(91, 7, 9, 44, '16:00:00', '17:30:00', 'TTH', 'Present'),
(92, 1, 8, 46, '13:00:00', '16:00:00', 'Sat', 'Present'),
(93, 5, 6, 46, '07:00:00', '10:00:00', 'Sat', 'Present'),
(94, 5, 6, 46, '10:00:00', '13:00:00', 'Sat', 'Present'),
(95, 5, 6, 46, '13:00:00', '16:00:00', 'Sat', 'Present'),
(96, 3, 8, 19, '07:00:00', '13:00:00', 'Sat', 'Present'),
(97, 4, 9, 56, '07:00:00', '10:00:00', 'Sat', 'Present'),
(98, 4, 9, 56, '10:00:00', '13:00:00', 'Sat', 'Present'),
(99, 4, 9, 56, '13:00:00', '16:00:00', 'Sat', 'Present');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `professors`
--
ALTER TABLE `professors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `professor_id` (`professor_id`);

--
-- AUTO_INCREMENT for dumped tables
--
SET @max_id = (SELECT COALESCE(MAX(id), 0) FROM courses);
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=@max_id + 1;

SET @max_id = (SELECT COALESCE(MAX(id), 0) FROM professors);
ALTER TABLE `professors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=@max_id + 1;

SET @max_id = (SELECT COALESCE(MAX(id), 0) FROM rooms);
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=@max_id + 1;

SET @max_id = (SELECT COALESCE(MAX(id), 0) FROM schedules);
ALTER TABLE `schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=@max_id + 1;
--
-- -- AUTO_INCREMENT for table `courses`
-- --
-- ALTER TABLE `courses`
--   MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

-- --
-- -- AUTO_INCREMENT for table `professors`
-- --
-- ALTER TABLE `professors`
--   MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

-- --
-- -- AUTO_INCREMENT for table `rooms`
-- --
-- ALTER TABLE `rooms`
--   MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

-- --
-- -- AUTO_INCREMENT for table `schedules`
-- --
-- ALTER TABLE `schedules`
--   MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `course_id` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`),
  ADD CONSTRAINT `professor_id` FOREIGN KEY (`professor_id`) REFERENCES `professors` (`id`),
  ADD CONSTRAINT `room_id` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
