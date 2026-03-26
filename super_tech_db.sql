-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 26, 2026 at 11:23 AM
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
-- Database: `super_tech_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `dept_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `status` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `student_id`, `teacher_id`, `dept_id`, `date`, `status`) VALUES
(2, 4, 2, 1, '2026-02-02', 'Present'),
(3, 4, 7, 1, '2026-02-14', 'Absent'),
(4, 8, 7, 1, '2026-02-14', 'Present'),
(5, 9, 7, 1, '2026-02-26', 'Present'),
(6, 4, 7, 1, '2026-02-26', 'Present'),
(7, 8, 7, 1, '2026-02-26', 'Present'),
(8, 9, 7, 1, '2026-02-25', 'Present'),
(9, 4, 7, 1, '2026-02-25', 'Present'),
(10, 8, 7, 1, '2026-02-25', 'Present'),
(11, 9, 7, 1, '2026-03-11', 'Present'),
(12, 4, 7, 1, '2026-03-11', 'Present'),
(13, 8, 7, 1, '2026-03-11', 'Present'),
(14, 13, 12, 4, '2026-03-15', 'Present'),
(15, 9, 7, 1, '2026-03-15', 'Absent'),
(16, 4, 7, 1, '2026-03-15', 'Present'),
(17, 8, 7, 1, '2026-03-15', 'Present'),
(18, 9, 7, 1, '2026-03-16', 'Absent'),
(19, 4, 7, 1, '2026-03-16', 'Present'),
(20, 8, 7, 1, '2026-03-16', 'Present');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `fees` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`, `fees`) VALUES
(1, 'Computer Science', 200.00),
(2, 'Mechanical', 200.00),
(3, 'Civil', 200.00),
(4, 'Electrical', 200.00);

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `event_date` date NOT NULL,
  `type` varchar(50) DEFAULT 'General',
  `attachment` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `title`, `description`, `event_date`, `type`, `attachment`) VALUES
(1, 'ANNUAL DAY', '', '2026-03-19', 'General', 'uploads/event_1773555473_event_1768766756_ChatGPT Image Jan 19, 2026, 01_34_55 AM.png');

-- --------------------------------------------------------

--
-- Table structure for table `exams`
--

CREATE TABLE `exams` (
  `id` int(11) NOT NULL,
  `exam_name` varchar(100) NOT NULL,
  `exam_date` date NOT NULL,
  `time_slot` varchar(50) NOT NULL,
  `room_no` varchar(20) NOT NULL,
  `dept_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exams`
--

INSERT INTO `exams` (`id`, `exam_name`, `exam_date`, `time_slot`, `room_no`, `dept_id`) VALUES
(3, 'CAT - I', '2026-03-18', '10:00 AM - 11:00 AM', 'LH 1', 2),
(4, 'CAT 1', '2026-03-17', '10:00 AM - 11:00 AM', 'LH 1', 1),
(5, 'CAT - I', '2026-03-17', '10:00 AM - 11:00 AM', 'LH 1', 4);

-- --------------------------------------------------------

--
-- Table structure for table `exam_results`
--

CREATE TABLE `exam_results` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `dept_id` int(11) NOT NULL,
  `exam_name` varchar(100) NOT NULL,
  `subject_name` varchar(100) NOT NULL,
  `marks_obtained` int(11) NOT NULL,
  `max_marks` int(11) DEFAULT 100,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exam_results`
--

INSERT INTO `exam_results` (`id`, `student_id`, `dept_id`, `exam_name`, `subject_name`, `marks_obtained`, `max_marks`, `created_at`) VALUES
(1, 4, 1, 'CAT 1', 'DSP', 95, 100, '2026-02-14 19:01:39'),
(3, 4, 1, 'CAT 1', 'PYTHON', 85, 100, '2026-02-15 14:18:05'),
(4, 9, 1, 'CAT 1', 'DSP', 85, 100, '2026-02-26 12:13:06');

-- --------------------------------------------------------

--
-- Table structure for table `leave_requests`
--

CREATE TABLE `leave_requests` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `dept_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `reason` text NOT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `request_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leave_requests`
--

INSERT INTO `leave_requests` (`id`, `student_id`, `dept_id`, `start_date`, `end_date`, `reason`, `status`, `request_date`) VALUES
(1, 4, 1, '2026-02-14', '2026-02-14', 'heavy fever', 'Approved', '2026-02-14 15:22:15'),
(2, 4, 1, '2026-02-23', '2026-02-25', 'go for the outing', 'Rejected', '2026-02-14 15:23:52'),
(3, 4, 1, '2026-03-18', '2026-03-20', 'vaction', 'Approved', '2026-03-16 06:22:56');

-- --------------------------------------------------------

--
-- Table structure for table `materials`
--

CREATE TABLE `materials` (
  `id` int(11) NOT NULL,
  `dept_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `upload_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `materials`
--

INSERT INTO `materials` (`id`, `dept_id`, `teacher_id`, `title`, `file_path`, `upload_date`) VALUES
(1, 1, 2, 'dsp', 'uploads/1770038735_DIGITAL SIGNAL PROCESSING_30112018.pdf', '2026-02-02'),
(2, 1, 7, 'DSP', 'uploads/1772107950_1768767967_dsnotes.pdf', '2026-02-26');

-- --------------------------------------------------------

--
-- Table structure for table `timetable`
--

CREATE TABLE `timetable` (
  `id` int(11) NOT NULL,
  `dept_id` int(11) NOT NULL,
  `day` varchar(20) NOT NULL,
  `time_slot` varchar(50) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `teacher_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `timetable`
--

INSERT INTO `timetable` (`id`, `dept_id`, `day`, `time_slot`, `subject`, `teacher_id`) VALUES
(1, 1, 'Monday', '08:00 PM - 09:00 PM', 'DSP', 2),
(2, 1, 'Monday', '10:00 AM - 11:00 AM', 'PYTHON ', 2),
(3, 1, 'Thursday', '12:30 pm - 13 :00', 'python', 2),
(4, 1, 'Tuesday', '10:00 AM - 11:00 AM', 'SL', 11),
(5, 1, 'Wednesday', '10:00 AM - 11:00 AM', 'EPT', 2),
(6, 1, 'Friday', '12:30 PM - 13 :00 PM', 'JAVA', 7),
(7, 1, 'Wednesday', '01:00 PM - 04:00 PM', 'SL', 7),
(8, 2, 'Monday', '10:00 AM - 11:00 AM', 'DESINING', 11),
(9, 4, 'Monday', '10:00 AM - 11:00 AM', 'CIRCUIT', 12);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','teacher','student') NOT NULL,
  `dept_id` int(11) DEFAULT NULL,
  `salary` decimal(10,2) DEFAULT 0.00,
  `fees_due` decimal(10,2) DEFAULT 0.00,
  `phone` varchar(20) DEFAULT NULL,
  `designation` varchar(100) DEFAULT 'Student',
  `profile_pic` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `dept_id`, `salary`, `fees_due`, `phone`, `designation`, `profile_pic`) VALUES
(1, 'Super Admin', 'admin@supertech.com', '1234', 'admin', NULL, 0.00, 0.00, '+91 3154897617', 'admin', NULL),
(2, 'Syed Teacher', 'teacher@gmail.com', '1234', 'teacher', 1, 150.00, 0.00, '+91 6475915872', 'lecturer', NULL),
(4, 'syed', 'syed_student@gmail.com', '1234', 'student', 1, 0.00, 0.00, '+91 5741986324', 'Student', NULL),
(6, 'syed', 'syed_faaiz@gmail.com', '1234', 'admin', NULL, 0.00, 0.00, '+91 5824796423', 'admin', NULL),
(7, 'syed teacher', 'syed_teacher@gmail.com', '1234', 'teacher', 1, 200.00, 0.00, '+91 75395185', 'Senior lecturer ', 'uploads/1771162228_event_1768766756_ChatGPT Image Jan 19, 2026, 01_34_55 AM.png'),
(8, 'syed1', 'syed_student1@gmail.com', '1234', 'student', 1, 0.00, 0.00, '+91 963852741', 'Student', NULL),
(9, 'suganth', 'suganth@gmail.com', '1234', 'student', 1, 0.00, 0.00, '+91 123456', 'Student', NULL),
(10, 'ragesh', 'ragesh@gmail.com', '1234', 'student', 2, 0.00, 3210.00, '+91 2345690', 'Student', NULL),
(11, 'sivaram', 'sivaram@gmail.com', '1234', 'teacher', 2, 0.00, 0.00, '91+ 12345678', 'lecturer', NULL),
(12, 'aaqil', 'aaqil@gmail.com', '1234', 'teacher', 4, 0.00, 0.00, '91+ 12345678', 'HOD', NULL),
(13, 'alwin', 'alwin@gmail.com', '1234', 'student', 4, 0.00, 0.00, '+91 3154897617', 'Student', NULL),
(14, 'siva', 'siva@gmail.com', '1234', 'admin', NULL, 0.00, 0.00, '+91 1234567', 'Student', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dept_id` (`dept_id`);

--
-- Indexes for table `exam_results`
--
ALTER TABLE `exam_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `dept_id` (`dept_id`);

--
-- Indexes for table `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `dept_id` (`dept_id`);

--
-- Indexes for table `materials`
--
ALTER TABLE `materials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dept_id` (`dept_id`);

--
-- Indexes for table `timetable`
--
ALTER TABLE `timetable`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dept_id` (`dept_id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `dept_id` (`dept_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `exams`
--
ALTER TABLE `exams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `exam_results`
--
ALTER TABLE `exam_results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `leave_requests`
--
ALTER TABLE `leave_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `materials`
--
ALTER TABLE `materials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `timetable`
--
ALTER TABLE `timetable`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exams`
--
ALTER TABLE `exams`
  ADD CONSTRAINT `exams_ibfk_1` FOREIGN KEY (`dept_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exam_results`
--
ALTER TABLE `exam_results`
  ADD CONSTRAINT `exam_results_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_results_ibfk_2` FOREIGN KEY (`dept_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD CONSTRAINT `leave_requests_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `leave_requests_ibfk_2` FOREIGN KEY (`dept_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `materials`
--
ALTER TABLE `materials`
  ADD CONSTRAINT `materials_ibfk_1` FOREIGN KEY (`dept_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `timetable`
--
ALTER TABLE `timetable`
  ADD CONSTRAINT `timetable_ibfk_1` FOREIGN KEY (`dept_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `timetable_ibfk_2` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`dept_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
