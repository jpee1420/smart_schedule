CREATE DATABASE IF NOT EXISTS `smart_schedule`;
USE `smart_schedule`;

CREATE TABLE IF NOT EXISTS `courses` (
  `id` int(11) AUTO_INCREMENT NOT NULL,
  `course_code` varchar(10) NOT NULL,
  `course_name` varchar(100) NOT NULL,
  `lab` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `professors` (
  `id` int(11) AUTO_INCREMENT NOT NULL,
  `name` varchar(255) NOT NULL,
  `profile_image` varchar(255) DEFAULT 'placeholder.png',
  PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `rooms` (
  `id` int(11) AUTO_INCREMENT NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `schedules` (
  `id` int(11) AUTO_INCREMENT NOT NULL,
  `room_id` int(11) DEFAULT NULL,
  `professor_id` int(11) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `day` enum('MWF','TTH','Sat') DEFAULT 'MWF',
  `notes` text DEFAULT NULL,
  `professor_status` enum('Present','Absent', 'On Leave') DEFAULT 'Present',
  PRIMARY KEY (`id`),
  KEY `course_id` (`course_id`),
  KEY `room_id` (`room_id`),
  KEY `professor_id` (`professor_id`),
  CONSTRAINT `schedules_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`),
  CONSTRAINT `schedules_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`),
  CONSTRAINT `schedules_ibfk_3` FOREIGN KEY (`professor_id`) REFERENCES `professors` (`id`)
);

INSERT INTO `courses` (`course_code`, `course_name`, `lab`) VALUES
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

  INSERT INTO `professors` (`name`, `profile_image`) VALUES
  ('Rina Cabansag', 'placeholder.png'),
  ('Diosdado Caronongan', 'placeholder.png'),
  ('Andrew Caronongan', 'placeholder.png'),
  ('Lorenz Camachoo', 'placeholder.png'),
  ('Eugevar Silang', 'placeholder.png'),
  ('Francis Lanz Cruzada', 'placeholder.png'),
  ('Cee Jay Lomibao', 'placeholder.png'),
  ('Ruzleen Soriano', 'placeholder.png');

  INSERT INTO `rooms` (`name`) VALUES
  ('LR1'),
  ('LR2'),
  ('LR4'),
  ('LR5'),
  ('MM3'),
  ('Grad 1'),
  ('Grad 2');
  