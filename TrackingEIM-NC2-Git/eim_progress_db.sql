-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 20, 2026 at 06:08 AM
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
-- Database: `eim_progress_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `assessments`
--

CREATE TABLE `assessments` (
  `id` int(10) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `competency_id` int(10) UNSIGNED NOT NULL,
  `assessment_type` varchar(50) NOT NULL,
  `score` int(11) DEFAULT NULL,
  `result` enum('pass','fail','pending') NOT NULL DEFAULT 'pending',
  `assessed_by` int(10) UNSIGNED DEFAULT NULL,
  `assessed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `assessments`
--

INSERT INTO `assessments` (`id`, `student_id`, `competency_id`, `assessment_type`, `score`, `result`, `assessed_by`, `assessed_at`, `remarks`) VALUES
(1, 3, 3, 'Project', 75, 'pass', 1, '2026-02-20 04:59:18', 'Goods'),
(2, 2, 2, 'Practical', 89, 'pass', 1, '2026-02-20 05:07:03', 'Goods');

-- --------------------------------------------------------

--
-- Table structure for table `batches`
--

CREATE TABLE `batches` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `year` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `batches`
--

INSERT INTO `batches` (`id`, `name`, `year`, `created_at`, `updated_at`) VALUES
(2, '2024-B', 2024, '2026-02-20 04:39:30', NULL),
(3, '2025-A', 2025, '2026-02-20 04:39:30', NULL),
(5, '2024-B', 2024, '2026-02-20 04:39:48', NULL),
(6, '2025-A', 2025, '2026-02-20 04:39:48', NULL),
(7, '2024-A', 2024, '2026-02-20 04:40:06', NULL),
(8, '2024-B', 2024, '2026-02-20 04:40:06', NULL),
(9, '2025-A', 2025, '2026-02-20 04:40:06', NULL),
(10, '2024-A', 2024, '2026-02-20 04:40:25', NULL),
(11, '2024-B', 2024, '2026-02-20 04:40:25', NULL),
(12, '2025-A', 2025, '2026-02-20 04:40:25', NULL),
(13, '2024-A', 2024, '2026-02-20 04:41:04', NULL),
(14, '2024-B', 2024, '2026-02-20 04:41:04', NULL),
(15, '2025-A', 2025, '2026-02-20 04:41:04', NULL),
(16, '2024-A', 2024, '2026-02-20 04:41:29', NULL),
(17, '2024-B', 2024, '2026-02-20 04:41:29', NULL),
(18, '2025-A', 2025, '2026-02-20 04:41:29', NULL),
(19, 'Sacurom', NULL, '2026-02-20 04:58:09', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `competencies`
--

CREATE TABLE `competencies` (
  `id` int(10) UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `competencies`
--

INSERT INTO `competencies` (`id`, `code`, `title`, `description`, `created_at`, `updated_at`) VALUES
(1, 'EIM-001', 'Install Electrical Wiring', 'Install electrical wiring systems in residential and commercial buildings following safety standards and electrical codes.', '2026-02-20 04:39:30', NULL),
(2, 'EIM-002', 'Perform Roughing-in Activities', 'Perform roughing-in activities including conduit installation, box mounting, and wire pulling.', '2026-02-20 04:39:30', NULL),
(3, 'EIM-003', 'Install Lighting System', 'Install various lighting systems including switches, outlets, fixtures, and control systems.', '2026-02-20 04:39:30', NULL),
(4, 'EIM-004', 'Maintain Electrical System', 'Perform preventive maintenance and troubleshooting on electrical systems and equipment.', '2026-02-20 04:39:30', NULL),
(5, 'EIM-005', 'Troubleshoot Electrical Circuits', 'Diagnose and repair faults in electrical circuits using proper testing equipment.', '2026-02-20 04:39:30', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `batch_id` int(10) UNSIGNED DEFAULT NULL,
  `enrollment_date` date DEFAULT NULL,
  `status` enum('active','inactive','graduated') NOT NULL DEFAULT 'active',
  `tesda_qualification` varchar(100) DEFAULT 'EIM NC II',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `user_id`, `name`, `email`, `batch_id`, `enrollment_date`, `status`, `tesda_qualification`, `created_at`, `updated_at`) VALUES
(1, 5, 'Pedro Reyes', 'pedro.reyes@example.com', 1, '2025-09-02', 'active', 'EIM NC II', '2026-02-20 04:41:04', '2026-02-20 04:41:04'),
(2, 6, 'Ana Garcia', 'ana.garcia@example.com', 2, '2025-08-08', 'active', 'EIM NC II', '2026-02-20 04:41:04', '2026-02-20 04:41:04'),
(3, 7, 'Carlos Mendoza', 'carlos.mendoza@example.com', 2, '2025-11-09', 'active', 'EIM NC II', '2026-02-20 04:41:04', '2026-02-20 04:41:04'),
(4, 8, 'Sacurom', 'elena.torres@example.com', 2, '2025-12-31', 'active', 'EIM NC II', '2026-02-20 04:41:04', '2026-02-20 04:57:53'),
(5, 9, 'Miguel Bautista', 'miguel.bautista@example.com', 3, '2025-05-09', 'active', 'EIM NC II', '2026-02-20 04:41:04', '2026-02-20 04:41:04'),
(6, 10, 'Sofia Ramos', 'sofia.ramos@example.com', 3, '2025-10-11', 'active', 'EIM NC II', '2026-02-20 04:41:04', '2026-02-20 04:41:04');

-- --------------------------------------------------------

--
-- Table structure for table `student_competencies`
--

CREATE TABLE `student_competencies` (
  `student_id` int(10) UNSIGNED NOT NULL,
  `competency_id` int(10) UNSIGNED NOT NULL,
  `status` enum('not_started','in_progress','completed') NOT NULL DEFAULT 'not_started',
  `practical_score` int(11) DEFAULT NULL,
  `assessment_date` date DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `student_competencies`
--

INSERT INTO `student_competencies` (`student_id`, `competency_id`, `status`, `practical_score`, `assessment_date`, `remarks`, `updated_at`) VALUES
(1, 1, 'not_started', NULL, NULL, NULL, NULL),
(1, 2, 'not_started', NULL, NULL, NULL, NULL),
(1, 3, 'not_started', NULL, NULL, NULL, NULL),
(1, 4, 'not_started', NULL, NULL, NULL, NULL),
(1, 5, 'not_started', NULL, NULL, NULL, NULL),
(2, 1, 'not_started', NULL, NULL, NULL, NULL),
(2, 2, 'completed', 89, '2026-02-20', 'Goods', '2026-02-20 05:07:03'),
(2, 3, 'not_started', NULL, NULL, NULL, NULL),
(2, 4, 'not_started', NULL, NULL, NULL, NULL),
(2, 5, 'not_started', NULL, NULL, NULL, NULL),
(3, 1, 'not_started', NULL, NULL, NULL, NULL),
(3, 2, 'not_started', NULL, NULL, NULL, NULL),
(3, 3, 'completed', 75, '2026-02-20', 'Goods', '2026-02-20 04:59:18'),
(3, 4, 'not_started', NULL, NULL, NULL, NULL),
(3, 5, 'not_started', NULL, NULL, NULL, NULL),
(4, 1, 'not_started', NULL, NULL, NULL, NULL),
(4, 2, 'not_started', NULL, NULL, NULL, NULL),
(4, 3, 'not_started', NULL, NULL, NULL, NULL),
(4, 4, 'not_started', NULL, NULL, NULL, NULL),
(4, 5, 'not_started', NULL, NULL, NULL, NULL),
(5, 1, 'not_started', NULL, NULL, NULL, NULL),
(5, 2, 'not_started', NULL, NULL, NULL, NULL),
(5, 3, 'not_started', NULL, NULL, NULL, NULL),
(5, 4, 'not_started', NULL, NULL, NULL, NULL),
(5, 5, 'not_started', NULL, NULL, NULL, NULL),
(6, 1, 'not_started', NULL, NULL, NULL, NULL),
(6, 2, 'not_started', NULL, NULL, NULL, NULL),
(6, 3, 'not_started', NULL, NULL, NULL, NULL),
(6, 4, 'not_started', NULL, NULL, NULL, NULL),
(6, 5, 'not_started', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'student',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`, `updated_at`) VALUES
(1, 'Administrator', 'admin@example.com', '$2y$10$qcHutJ4oRyXlCVkujDYTIOEgaUIzyFcx5sFwFHYEI.VjSO/8T8lI.', 'admin', '2026-02-20 04:25:50', NULL),
(2, 'Juan Dela Cruz', 'juan.cruz@example.com', '$2y$10$cBmR3/wbN3idPkcwHqn.quGdJ8MpTqoybnJliIohQrwdlYJV2NGoO', 'student', '2026-02-20 04:39:30', NULL),
(4, 'Maria Santos', 'maria.santos@example.com', '$2y$10$RVvVQ5LCpaFzx/bA/rlQauTStb54IkOlEnqFvwHsPSI84FIV0a30e', 'student', '2026-02-20 04:40:25', NULL),
(5, 'Pedro Reyes', 'pedro.reyes@example.com', '$2y$10$nAUqQiV.akYFNnM4U9oY8u.tgXERgP3XOBfz4GaYOrswtKNF6OEga', 'student', '2026-02-20 04:41:04', NULL),
(6, 'Ana Garcia', 'ana.garcia@example.com', '$2y$10$.wLE338lV3mfOmdxq7Nf6./V7P43q/kh9yjqIad8AVSrJdXC51U42', 'student', '2026-02-20 04:41:04', NULL),
(7, 'Carlos Mendoza', 'carlos.mendoza@example.com', '$2y$10$4CRsEJZVsGx2n89PthE9HezFtJ868voQqHyPdnSDCVR6svWKXhpti', 'student', '2026-02-20 04:41:04', NULL),
(8, 'Elena Torres', 'elena.torres@example.com', '$2y$10$Q0t3nbuGTKuevktUCPLaJey35/qWvdfVVMqQYRbwvfXhg9g3LOd4C', 'student', '2026-02-20 04:41:04', NULL),
(9, 'Miguel Bautista', 'miguel.bautista@example.com', '$2y$10$6BuQK2eJ4Y05dryURm6xduE9sOAs0dXFdRYRAehdlr2c2Q5mz1ddS', 'student', '2026-02-20 04:41:04', NULL),
(10, 'Sofia Ramos', 'sofia.ramos@example.com', '$2y$10$AunutVOJ8IAK.P8kujI9Geyi/lO66kRiQLZyuy62R6O4L.6EA.186', 'student', '2026-02-20 04:41:04', NULL),
(11, 'Sacurom', 'sacurom@example.com', '$2y$10$aQ18eyj9YPuai0ff353syuqsPKP.s.TomDQYoekr7xBoBJMIl.A3y', 'instructor', '2026-02-20 04:58:38', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assessments`
--
ALTER TABLE `assessments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_assessments_student` (`student_id`),
  ADD KEY `idx_assessments_competency` (`competency_id`),
  ADD KEY `fk_assessments_assessor` (`assessed_by`);

--
-- Indexes for table `batches`
--
ALTER TABLE `batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `competencies`
--
ALTER TABLE `competencies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_competencies_code` (`code`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_students_batch_id` (`batch_id`),
  ADD KEY `idx_students_user_id` (`user_id`);

--
-- Indexes for table `student_competencies`
--
ALTER TABLE `student_competencies`
  ADD PRIMARY KEY (`student_id`,`competency_id`),
  ADD KEY `fk_sc_competency` (`competency_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assessments`
--
ALTER TABLE `assessments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `batches`
--
ALTER TABLE `batches`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `competencies`
--
ALTER TABLE `competencies`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `assessments`
--
ALTER TABLE `assessments`
  ADD CONSTRAINT `fk_assessments_assessor` FOREIGN KEY (`assessed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_assessments_competency` FOREIGN KEY (`competency_id`) REFERENCES `competencies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_assessments_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `fk_students_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `student_competencies`
--
ALTER TABLE `student_competencies`
  ADD CONSTRAINT `fk_sc_competency` FOREIGN KEY (`competency_id`) REFERENCES `competencies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_sc_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
