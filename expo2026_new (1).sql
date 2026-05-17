-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 17, 2026 at 07:56 PM
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
-- Database: `expo2026_new`
--

-- --------------------------------------------------------

--
-- Table structure for table `certificates`
--

CREATE TABLE `certificates` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `project_id` int(11) DEFAULT NULL,
  `certificate_type` enum('participant','judge','winner','supervisor') DEFAULT NULL,
  `certificate_file` varchar(255) DEFAULT NULL,
  `issue_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `evaluations`
--

CREATE TABLE `evaluations` (
  `id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `judge_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `total_score` decimal(5,2) DEFAULT NULL,
  `feedback` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `judges`
--

CREATE TABLE `judges` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `specialization` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `judging_sessions`
--

CREATE TABLE `judging_sessions` (
  `id` int(11) NOT NULL,
  `session_name` varchar(255) NOT NULL,
  `project_id` int(11) NOT NULL,
  `department` varchar(100) NOT NULL,
  `track` varchar(100) NOT NULL,
  `zoom_link` varchar(500) DEFAULT NULL,
  `session_date` date NOT NULL,
  `session_time` time NOT NULL,
  `duration_minutes` int(11) NOT NULL DEFAULT 30,
  `status` enum('upcoming','ongoing','completed','cancelled') DEFAULT 'upcoming',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `supervisor` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `theme` enum('Health','Economies','Sustainability','Energy','Education','Research') NOT NULL,
  `track` varchar(100) NOT NULL,
  `poster_pdf` varchar(255) DEFAULT NULL,
  `poster_ppt` varchar(255) DEFAULT NULL,
  `status` enum('draft','submitted','under_review','approved','rejected') DEFAULT 'submitted',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `student_id`, `title`, `description`, `supervisor`, `phone`, `theme`, `track`, `poster_pdf`, `poster_ppt`, `status`, `created_at`, `updated_at`) VALUES
(2, 10, 'html', 'css', 'abbas', '0546691465', 'Health', 'Computer Science', 'poster/1779038949_pdf_33a43df310371d79.pdf', 'poster/1779038949_ppt_2ba861c0cfc0733f.pptx', 'submitted', '2026-05-17 17:28:51', '2026-05-17 17:29:09');

-- --------------------------------------------------------

--
-- Table structure for table `project_members`
--

CREATE TABLE `project_members` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `project_members`
--

INSERT INTO `project_members` (`id`, `project_id`, `full_name`, `email`, `user_id`) VALUES
(2, 2, 'Ali', 'abbas.h.8215@gmail.com', 3);

-- --------------------------------------------------------

--
-- Table structure for table `session_judges`
--

CREATE TABLE `session_judges` (
  `id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `judge_id` int(11) NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `attendance_confirmed` tinyint(1) NOT NULL DEFAULT 0,
  `joined_at` datetime DEFAULT NULL,
  `left_at` datetime DEFAULT NULL,
  `feedback_given` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `session_students`
--

CREATE TABLE `session_students` (
  `id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `presentation_order` int(11) NOT NULL DEFAULT 0,
  `attendance_confirmed` tinyint(1) NOT NULL DEFAULT 0,
  `presentation_file` varchar(500) DEFAULT NULL,
  `feedback` text DEFAULT NULL,
  `score` decimal(5,2) DEFAULT NULL,
  `evaluated_by` int(11) DEFAULT NULL,
  `evaluated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `setting_key` varchar(100) NOT NULL,
  `setting_value` varchar(255) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`setting_key`, `setting_value`, `updated_at`) VALUES
('final_results_visible', '0', '2026-05-15 18:17:20'),
('poster_upload_open', '1', '2026-05-15 18:17:20'),
('registration_open', '1', '2026-05-15 18:17:20');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `firstName` varchar(100) NOT NULL,
  `lastName` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('student','guest','judge','Admin') NOT NULL DEFAULT 'guest',
  `phone_number` varchar(20) NOT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `verification_token` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstName`, `lastName`, `email`, `password`, `role`, `phone_number`, `profile_photo`, `verification_token`, `is_active`, `created_at`) VALUES
(3, 'Abbas', 'Al Shief', 'abbas.h.8215@gmail.com', '$2y$10$4d5LneA/6niiwL1xtLov8eK69ZCn7649FPLHalwh/jaXIaXTuG.U6', 'Admin', '0561253160', NULL, NULL, 1, '2026-05-15 18:40:41'),
(9, 'Abbas', 'Al Shief', 'abbas.a.8215@gmail.com', '$2y$10$9vnkGT/SI9CuE.ub2bogduqIGQLcFp.Qo3HorOIs4ViTDvQfTXXQ.', 'judge', '', 'uploads/1779038692_شهادة - ورشة الذكاء الاصطناعي .png', NULL, 1, '2026-05-17 17:22:32'),
(10, 'Abbas', 'Al Shief', 'abbasalshief@gmail.com', '$2y$10$.wkS3.d3M25mTlmSl7EGM.Gw1PPCa7P0wZNP7H2n02jhoRw82omXO', 'student', '0561253160', NULL, NULL, 1, '2026-05-17 17:27:13');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `certificates`
--
ALTER TABLE `certificates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_project` (`project_id`);

--
-- Indexes for table `evaluations`
--
ALTER TABLE `evaluations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_session` (`session_id`),
  ADD KEY `idx_judge` (`judge_id`),
  ADD KEY `idx_project` (`project_id`);

--
-- Indexes for table `judges`
--
ALTER TABLE `judges`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_judge_user` (`user_id`);

--
-- Indexes for table `judging_sessions`
--
ALTER TABLE `judging_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_project` (`project_id`),
  ADD KEY `idx_date` (`session_date`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_student_project` (`student_id`),
  ADD KEY `idx_theme` (`theme`),
  ADD KEY `idx_track` (`track`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `project_members`
--
ALTER TABLE `project_members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_project` (`project_id`),
  ADD KEY `idx_user` (`user_id`);

--
-- Indexes for table `session_judges`
--
ALTER TABLE `session_judges`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_session_judge` (`session_id`,`judge_id`),
  ADD KEY `idx_judge` (`judge_id`);

--
-- Indexes for table `session_students`
--
ALTER TABLE `session_students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_session_student` (`session_id`,`student_id`),
  ADD KEY `idx_student` (`student_id`),
  ADD KEY `idx_evaluator` (`evaluated_by`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`setting_key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_email` (`email`),
  ADD KEY `idx_role` (`role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `certificates`
--
ALTER TABLE `certificates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `evaluations`
--
ALTER TABLE `evaluations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `judges`
--
ALTER TABLE `judges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `judging_sessions`
--
ALTER TABLE `judging_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `project_members`
--
ALTER TABLE `project_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `session_judges`
--
ALTER TABLE `session_judges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `session_students`
--
ALTER TABLE `session_students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `certificates`
--
ALTER TABLE `certificates`
  ADD CONSTRAINT `fk_cert_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cert_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `evaluations`
--
ALTER TABLE `evaluations`
  ADD CONSTRAINT `fk_eval_judge` FOREIGN KEY (`judge_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_eval_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_eval_session` FOREIGN KEY (`session_id`) REFERENCES `judging_sessions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `judges`
--
ALTER TABLE `judges`
  ADD CONSTRAINT `fk_judge_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `judging_sessions`
--
ALTER TABLE `judging_sessions`
  ADD CONSTRAINT `fk_session_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `fk_project_student` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `project_members`
--
ALTER TABLE `project_members`
  ADD CONSTRAINT `fk_member_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_member_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `session_judges`
--
ALTER TABLE `session_judges`
  ADD CONSTRAINT `fk_sj_judge` FOREIGN KEY (`judge_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_sj_session` FOREIGN KEY (`session_id`) REFERENCES `judging_sessions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `session_students`
--
ALTER TABLE `session_students`
  ADD CONSTRAINT `fk_ss_evaluator` FOREIGN KEY (`evaluated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_ss_session` FOREIGN KEY (`session_id`) REFERENCES `judging_sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ss_student` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
