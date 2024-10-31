-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 30, 2024 at 03:55 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `leave_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `available_leaves`
--

CREATE TABLE `available_leaves` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `casual_leaves` decimal(4,1) DEFAULT 21.0,
  `rest_leaves` decimal(4,1) DEFAULT 24.0,
  `other_leaves` decimal(4,1) NOT NULL,
  `last_reset` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `available_leaves`
--

INSERT INTO `available_leaves` (`id`, `user_id`, `casual_leaves`, `rest_leaves`, `other_leaves`, `last_reset`) VALUES
(21, 46, 21.0, 24.0, 0.0, NULL),
(24, 49, 21.0, 24.0, 0.0, NULL),
(25, 50, 21.0, 24.0, 0.0, NULL),
(26, 51, 21.0, 24.0, 0.0, NULL),
(27, 52, 21.0, 24.0, 0.0, NULL),
(28, 53, 21.0, 24.0, 0.0, NULL),
(29, 54, 21.0, 24.0, 0.0, NULL),
(30, 55, 21.0, 24.0, 0.0, NULL),
(31, 56, 21.0, 24.0, 0.0, NULL),
(32, 57, 21.0, 24.0, 0.0, NULL),
(33, 58, 21.0, 24.0, 0.0, NULL),
(34, 59, 21.0, 24.0, 0.0, NULL),
(35, 60, 21.0, 24.0, 0.0, NULL),
(36, 61, 21.0, 24.0, 0.0, NULL),
(37, 62, 21.0, 24.0, 0.0, NULL),
(38, 63, 21.0, 24.0, 0.0, NULL),
(39, 64, 21.0, 24.0, 0.0, NULL),
(40, 65, 21.0, 24.0, 0.0, NULL),
(41, 66, 21.0, 24.0, 0.0, NULL),
(44, 69, 18.0, 24.0, 0.0, NULL),
(51, 68, 21.0, 22.0, 0.0, NULL),
(52, 73, 21.0, 24.0, 0.0, NULL),
(53, 74, 21.0, 24.0, 0.0, NULL),
(54, 76, 21.0, 24.0, 0.0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`id`, `name`) VALUES
(1, 'IT Department'),
(2, 'Secretary'),
(4, 'Admin');

-- --------------------------------------------------------

--
-- Table structure for table `emergency_leave`
--

CREATE TABLE `emergency_leave` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `emp_on_leave` int(11) NOT NULL,
  `reason` text NOT NULL,
  `commence_leave_date` date NOT NULL,
  `resume_date` date DEFAULT NULL,
  `submission_date` date NOT NULL,
  `status` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leave_applications`
--

CREATE TABLE `leave_applications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `leaveDates` decimal(4,1) DEFAULT NULL,
  `leaveReason` enum('Casual','Rest','Other') NOT NULL,
  `firstAppointmentDate` date NOT NULL,
  `commenceLeaveDate` date NOT NULL,
  `resumeDate` date NOT NULL,
  `replacement` int(11) DEFAULT NULL,
  `addressDuringLeave` varchar(255) NOT NULL,
  `actingOfficer` int(11) DEFAULT NULL,
  `submissionDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `fullReason` varchar(255) NOT NULL,
  `status` enum('pending','approved','rejected','') NOT NULL DEFAULT 'pending',
  `emg` tinyint(1) NOT NULL DEFAULT 0,
  `rejectionReason` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leave_history`
--

CREATE TABLE `leave_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `year` int(4) NOT NULL,
  `casual_leaves` decimal(4,1) NOT NULL,
  `rest_leaves` decimal(4,1) NOT NULL,
  `other_leaves` decimal(4,1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `medicals`
--

CREATE TABLE `medicals` (
  `id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `upload_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `request_status`
--

CREATE TABLE `request_status` (
  `id` int(11) NOT NULL,
  `leave_application_id` int(11) NOT NULL,
  `replacement_status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `staff_status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `supervising_officer_status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `head_of_department_status` enum('Pending','Approved','Rejected') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`id`, `name`) VALUES
(1, 'Employee'),
(2, 'Subject Officer'),
(3, 'Staff Officer');

-- --------------------------------------------------------

--
-- Table structure for table `short_leaves`
--

CREATE TABLE `short_leaves` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `short_leaves` int(11) DEFAULT 0,
  `modified_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `short_leaves`
--

INSERT INTO `short_leaves` (`id`, `user_id`, `short_leaves`, `modified_date`) VALUES
(4, 49, 0, '2024-10-25 10:42:03'),
(5, 50, 0, '2024-10-25 10:42:03'),
(6, 51, 0, '2024-10-25 10:42:03'),
(7, 52, 0, '2024-10-25 10:42:03'),
(8, 53, 0, '2024-10-25 10:42:03'),
(9, 54, 0, '2024-10-25 10:42:03'),
(10, 55, 0, '2024-10-25 10:42:03'),
(11, 56, 0, '2024-10-25 10:42:03'),
(12, 57, 0, '2024-10-25 10:42:03'),
(13, 58, 0, '2024-10-25 10:42:03'),
(14, 59, 0, '2024-10-25 10:42:03'),
(15, 60, 0, '2024-10-25 10:42:03'),
(16, 61, 0, '2024-10-25 10:42:03'),
(17, 62, 0, '2024-10-25 10:42:03'),
(18, 63, 0, '2024-10-25 10:42:03'),
(19, 64, 0, '2024-10-25 10:42:03'),
(20, 65, 0, '2024-10-25 10:42:03'),
(21, 66, 0, '2024-10-25 10:42:03'),
(23, 68, 0, '2024-10-25 10:42:03'),
(24, 69, 0, '2024-10-25 10:42:03'),
(28, 73, 0, '2024-10-25 10:42:03'),
(29, 74, 0, '2024-10-25 10:42:03'),
(31, 76, 0, '2024-10-28 04:26:23');

-- --------------------------------------------------------

--
-- Table structure for table `short_leave_history`
--

CREATE TABLE `short_leave_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `short_leaves` int(11) DEFAULT NULL,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `designation` varchar(255) NOT NULL,
  `dept` varchar(255) NOT NULL,
  `nic` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `acting` int(11) DEFAULT NULL,
  `staff` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`staff`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `designation`, `dept`, `nic`, `email`, `password`, `role`, `acting`, `staff`) VALUES
(1, 'Admin', 'Admin', '', 'admin', '', '$2y$10$hLTMPGGUcuJsLs04Me4V1udFM15nXu7dtC2DQpkjKERuuwwuHBuNi', 'Admin', NULL, 'null'),
(46, 'Super Admin', 'Super Admin', '', 'superAdmin', '', '$2y$10$Qah7pyr2paMt1omzIFZX7uT1U0384vY51sskvrA803I3hjZf5V.5C', 'Super Admin', NULL, 'null'),
(49, 'M.P.N.M. Wickramasinghe', 'Secretary', 'Secretary', '196621401682', 'wnishantha66@gmail.com', '$2y$10$GHAYGaut/I0GlkMSTJRDyuFlwh2PhQNGlRyMdqPcos/ufPS9KFN6C', 'Staff Officer', NULL, 'null'),
(50, 'N.A.A.P.S. Nissanka', 'Additional Secretary ( Administration and HR )', 'Admin', '196754800869', 'nissankaapsara@gmail.com', '$2y$10$z553XgjqPTEb3Hlf0NVKi.TSdGcpe.Dm.LhzfkLY6H9bJHhjoGUnC', 'Staff Officer', 51, 'null'),
(51, 'P. Kodithuwakku', 'Senior Assistant Secretary', 'Admin', '836982380V', 'priyankakodithuwakku@gmail.com', '$2y$10$kfTH10Kr9h/U8LCPDWQgXeIwWYucf8ufSs69MJvmCDADI.6e5w/Fu', 'Staff Officer', NULL, '[\"49\",\"50\"]'),
(52, 'A.G.N.R. Anandani', 'Administrative Officer (Acting)', 'Admin', '668080162V', 'nilmini.anandani@gmail.com', '$2y$10$D0FZqf1zH9LA4ygGhLQpLOssaWFcRpifih62s4DMt5.3gHYnCcYhm', 'Staff Officer', NULL, '[\"50\",\"51\"]'),
(53, 'K.A.H.M. Silva', 'Development Officer', 'Admin', '916211163V', 'harshanisilva42@gmail.com', '$2y$10$yOVnfcabH6mdnH0H7PwsUO.jG09QRxw0qRCbJ7IB3ZaVEJTs/kkry', 'Employee', 54, '[\"50\",\"51\",\"52\"]'),
(54, 'B.M.P.S. Bandara', 'Development Officer', 'Admin', '198932400098', 'sanjeewabmp@gmail.com', '$2y$10$6Cw5.V7GEgRVl9tZoS7BfuTDnevvUMe7clrZN.yie2E4nocXfiQnW', 'Employee', 53, '[\"50\",\"51\",\"52\"]'),
(55, 'M.P. Nishanthi', 'Management Service Officer', 'Admin', '718491401V', 'nishanthimanamendra11@gmail.com', '$2y$10$RaBZC9iwaD8PdrmKoiUuEOXzAWKyQczvRCUlzYa7f0hKHIBxfA812', 'Employee', 52, '[\"50\",\"51\",\"52\"]'),
(56, 'W.N. Dhammika', 'Management Service Officer', 'Admin', '716421597V', 'ndweerasiri@gmail.com', '$2y$10$WpzheErmEvOn.F3LGcrBMuxPYQGxQV5GhTlLUitOc/Utb.bBvrNmC', 'Employee', 55, '[\"50\",\"51\",\"52\"]'),
(57, 'D. Nisansala', 'Development Officer', 'Admin', '885520618V', 'asstsecfisheries@yahoo.com', '$2y$10$cu9JWCKKwLnO00s/VcqNDelI6d6DVLn3Cbw696boLPXVdWrwb6zty', 'Employee', 59, '[\"50\",\"51\",\"52\"]'),
(58, 'R.M.S.S. Rajapaksha', 'Development Officer', 'Admin', '197561902744', 'rajapakshashyama28@gmail.com', '$2y$10$hcWs1URx5SZzLNPpoh0g4uo7swg0d5AIDW.lyMgbNWixxOAc/LUcu', 'Employee', 57, '[\"50\",\"51\",\"52\"]'),
(59, 'D.W.N.I. Kumari', 'Development Officer', 'Admin', '198762303800', 'niluwidanage0@gmail.com', '$2y$10$indmypc8.x3kpwVd9KyTDuILvlON/mMamqsKbjVE3BJE2uKn9gDVG', 'Employee', 58, '[\"50\",\"51\",\"52\"]'),
(60, 'K.D. Ayesha', 'Management Service Officer', 'Admin', '19737070883V', 'kachchadraayesha@gmail.com', '$2y$10$Pwmh9yzoNX6H24Nhg7CH3e9BEA/FlLoVRLbaUTWkbyS5Mq.ggL6Ke', 'Employee', 56, '[\"50\",\"51\",\"52\"]'),
(61, 'K.A.A. Priyadarshana', 'Management Service Officer', 'Admin', '942154143V', 'kkp.amal@gmail.com', '$2y$10$5WBA7W4iVwNteZr.B1BgXeekc5UGPet4cgZ3tqwEKuynEXymG.66a', 'Employee', 62, '[\"50\",\"51\",\"52\"]'),
(62, 'W.L.R.S. Kumara', 'Development Officer', 'Admin', '811281620V', 'roshsuneth@gmail.com', '$2y$10$//9vyW4ReM1fC1zBvFVmU.NhX/RnHEoNvZbPjMSgYL7i3Ef51vl0i', 'Employee', 61, '[\"50\",\"51\",\"52\"]'),
(63, 'S.H.C.K. Wijerathna', 'Management Service Officer', 'Admin', '935033691V', 'chathurika9313@gmail.com', '$2y$10$y070E1fChUcAtX4OpBp7IuhNNn6DhBnvs7q8dEqImxpM52d06w9SG', 'Employee', 64, '[\"50\",\"51\",\"52\"]'),
(64, 'H.S.I. Kumari', 'Management Service Officer', 'Admin', '199184803144', 'imangasarani91@gmail.com', '$2y$10$2XC6qZtHgCFuCPCNi.Ah9eSzSpllp7V7kRzfhX8KuhHp0NhK2fMle', 'Subject Officer', 63, '[\"50\",\"51\",\"52\"]'),
(65, 'W.M.S. Harshani', 'Development Officer', 'Admin', '928284387V', 'shashikalaharshani833@gmail.com', '$2y$10$Aux6TNbIFNGtF/Aeu77fs.WgroBXy.FeT3gPkD7oobkrkHLxBLWqS', 'Employee', 56, '[\"50\",\"51\",\"52\"]'),
(66, 'W.R.T. Munasighe', 'Development Officer', 'Admin', '198074600714', 'tharanganim80@gmail.com', '$2y$10$D/Ib7R4QpcEtejEjdXuAJuf1i8gMbx7pDdqYCgtdpifAbOXtGurwO', 'Employee', 63, '[\"50\",\"51\",\"52\"]'),
(68, 'K.T.D.N. Dilshan', 'SE', 'IT Department', '992873078V', 'nadun.dilshan.733@gmail.com', '$2y$10$OYkbdgH8onqLPtdURglCX.y7nNWwgMLl8Vp3xXIZezjL2MU4sJnYK', 'Employee', 69, '[\"73\",\"74\"]'),
(69, 'M.M. Balasooriya', 'SE', 'IT Department', '992061642V', 'malindamihiranga1@gmail.com', '$2y$10$mDZFOWmIRRMGsaLHDzo/Q.l1mqCZW1vVLPXiS1tCRcSWkg/a2JQjW', 'Employee', 68, '[\"73\",\"74\"]'),
(73, 'Staff officer 1', 'IT', 'IT Department', 'staff1', 'cocp.733@gmail.com', '$2y$10$SceczqUDkAke6MLZNdJfeu/XQa/TQAuy0Ss85aAFamZCut8r9cp/G', 'Staff Officer', 74, 'null'),
(74, 'Staff officer 2', 'IT', 'IT Department', 'staff2', 'malindamihiranga1@gmail.com', '$2y$10$HZRKTIonirn0Oo7CqcDAXecXFyOrFxqXnx7jk0K5RIqSCYe5rWfKq', 'Staff Officer', 73, '[\"69\"]'),
(76, 'Subject Officer', 'Subject Officer', 'IT Department', 'subject', 'subject@gmail.com', '$2y$10$qS8jpqzILJQfHr1sMh.DG.pHYTpWnT3JdEUpnUxl6a/wjbCEVoBka', 'Subject Officer', NULL, '[\"73\",\"74\"]');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `available_leaves`
--
ALTER TABLE `available_leaves`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `emergency_leave`
--
ALTER TABLE `emergency_leave`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `emergency_leave_ibfk_2` (`emp_on_leave`);

--
-- Indexes for table `leave_applications`
--
ALTER TABLE `leave_applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `replacement` (`replacement`),
  ADD KEY `actingOfficer` (`actingOfficer`),
  ADD KEY `leave_applications_ibfk_1` (`user_id`);

--
-- Indexes for table `leave_history`
--
ALTER TABLE `leave_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user` (`user_id`);

--
-- Indexes for table `medicals`
--
ALTER TABLE `medicals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `request_status`
--
ALTER TABLE `request_status`
  ADD PRIMARY KEY (`id`),
  ADD KEY `request_status_ibfk_1` (`leave_application_id`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `short_leaves`
--
ALTER TABLE `short_leaves`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `short_leave_history`
--
ALTER TABLE `short_leave_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`nic`),
  ADD KEY `fk_acting_user` (`acting`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `available_leaves`
--
ALTER TABLE `available_leaves`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `emergency_leave`
--
ALTER TABLE `emergency_leave`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `leave_applications`
--
ALTER TABLE `leave_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

--
-- AUTO_INCREMENT for table `leave_history`
--
ALTER TABLE `leave_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `medicals`
--
ALTER TABLE `medicals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `request_status`
--
ALTER TABLE `request_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `short_leaves`
--
ALTER TABLE `short_leaves`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `short_leave_history`
--
ALTER TABLE `short_leave_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `available_leaves`
--
ALTER TABLE `available_leaves`
  ADD CONSTRAINT `available_leaves_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `emergency_leave`
--
ALTER TABLE `emergency_leave`
  ADD CONSTRAINT `emergency_leave_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `emergency_leave_ibfk_2` FOREIGN KEY (`emp_on_leave`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `leave_applications`
--
ALTER TABLE `leave_applications`
  ADD CONSTRAINT `leave_applications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `leave_applications_ibfk_2` FOREIGN KEY (`replacement`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `leave_applications_ibfk_3` FOREIGN KEY (`actingOfficer`) REFERENCES `users` (`id`);

--
-- Constraints for table `leave_history`
--
ALTER TABLE `leave_history`
  ADD CONSTRAINT `fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `request_status`
--
ALTER TABLE `request_status`
  ADD CONSTRAINT `request_status_ibfk_1` FOREIGN KEY (`leave_application_id`) REFERENCES `leave_applications` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `short_leaves`
--
ALTER TABLE `short_leaves`
  ADD CONSTRAINT `short_leaves_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `short_leave_history`
--
ALTER TABLE `short_leave_history`
  ADD CONSTRAINT `short_leave_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_acting_user` FOREIGN KEY (`acting`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
