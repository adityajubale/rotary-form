-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 18, 2026 at 10:03 AM
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
-- Database: `rotary`
--

-- --------------------------------------------------------

--
-- Table structure for table `clubs`
--

CREATE TABLE `clubs` (
  `id` int(11) NOT NULL,
  `club_name` varchar(200) NOT NULL,
  `club_location` varchar(300) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `clubs`
--

INSERT INTO `clubs` (`id`, `club_name`, `club_location`, `is_active`, `created_at`) VALUES
(1, 'Club Azure (Downtown)', 'Downtown Area', 1, '2026-04-18 07:30:19'),
(2, 'Club Neon (Beachside)', 'Beach Road', 1, '2026-04-18 07:30:19'),
(3, 'The Loft Lounge', 'City Center', 1, '2026-04-18 07:30:19'),
(4, 'Sky Garden Rooftop', 'Hilltop Road', 1, '2026-04-18 07:30:19');

-- --------------------------------------------------------

--
-- Table structure for table `cohost_gold_registrations`
--

CREATE TABLE `cohost_gold_registrations` (
  `id` int(11) NOT NULL,
  `registration_id` varchar(50) NOT NULL,
  `uti_number` varchar(100) NOT NULL,
  `screenshot_filename` varchar(500) DEFAULT NULL,
  `full_name` varchar(200) NOT NULL,
  `email` varchar(150) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `club_id` int(11) DEFAULT NULL,
  `club_name` varchar(200) DEFAULT NULL,
  `amount_paid` decimal(10,2) NOT NULL,
  `payment_status` enum('pending','completed','failed') DEFAULT 'completed',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cohost_platinum_registrations`
--

CREATE TABLE `cohost_platinum_registrations` (
  `id` int(11) NOT NULL,
  `registration_id` varchar(50) NOT NULL,
  `uti_number` varchar(100) NOT NULL,
  `screenshot_filename` varchar(500) DEFAULT NULL,
  `full_name` varchar(200) NOT NULL,
  `email` varchar(150) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `club_id` int(11) DEFAULT NULL,
  `club_name` varchar(200) DEFAULT NULL,
  `amount_paid` decimal(10,2) NOT NULL,
  `payment_status` enum('pending','completed','failed') DEFAULT 'completed',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cohost_silver_registrations`
--

CREATE TABLE `cohost_silver_registrations` (
  `id` int(11) NOT NULL,
  `registration_id` varchar(50) NOT NULL,
  `uti_number` varchar(100) NOT NULL,
  `screenshot_filename` varchar(500) DEFAULT NULL,
  `full_name` varchar(200) NOT NULL,
  `email` varchar(150) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `club_id` int(11) DEFAULT NULL,
  `club_name` varchar(200) DEFAULT NULL,
  `amount_paid` decimal(10,2) NOT NULL,
  `payment_status` enum('pending','completed','failed') DEFAULT 'completed',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `couple_registrations`
--

CREATE TABLE `couple_registrations` (
  `id` int(11) NOT NULL,
  `registration_id` varchar(50) NOT NULL,
  `full_name` varchar(200) NOT NULL,
  `uti_number` varchar(100) NOT NULL,
  `screenshot_filename` varchar(500) DEFAULT NULL,
  `designation` varchar(200) NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'MEMBER',
  `spouse_name` varchar(200) NOT NULL,
  `email` varchar(150) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `club_id` int(11) DEFAULT NULL,
  `club_name` varchar(200) DEFAULT NULL,
  `food_preference` enum('Veg','Nonveg') DEFAULT NULL,
  `alcohol` enum('Yes','No') DEFAULT NULL,
  `amount_paid` decimal(10,2) NOT NULL,
  `payment_status` enum('pending','completed','failed') DEFAULT 'completed',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `single_registrations`
--

CREATE TABLE `single_registrations` (
  `id` int(11) NOT NULL,
  `registration_id` varchar(50) NOT NULL,
  `full_name` varchar(200) NOT NULL,
  `uti_number` varchar(100) NOT NULL,
  `screenshot_filename` varchar(500) DEFAULT NULL,
  `designation` varchar(200) NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'MEMBER',
  `email` varchar(150) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `club_id` int(11) DEFAULT NULL,
  `club_name` varchar(200) DEFAULT NULL,
  `food_preference` enum('Veg','Nonveg') DEFAULT NULL,
  `alcohol` enum('Yes','No') DEFAULT NULL,
  `amount_paid` decimal(10,2) NOT NULL,
  `payment_status` enum('pending','completed','failed') DEFAULT 'completed',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `clubs`
--
ALTER TABLE `clubs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cohost_gold_registrations`
--
ALTER TABLE `cohost_gold_registrations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `registration_id` (`registration_id`),
  ADD UNIQUE KEY `uti_number` (`uti_number`),
  ADD KEY `idx_email` (`email`);

--
-- Indexes for table `cohost_platinum_registrations`
--
ALTER TABLE `cohost_platinum_registrations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `registration_id` (`registration_id`),
  ADD UNIQUE KEY `uti_number` (`uti_number`),
  ADD KEY `idx_email` (`email`);

--
-- Indexes for table `cohost_silver_registrations`
--
ALTER TABLE `cohost_silver_registrations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `registration_id` (`registration_id`),
  ADD UNIQUE KEY `uti_number` (`uti_number`),
  ADD KEY `idx_email` (`email`);

--
-- Indexes for table `couple_registrations`
--
ALTER TABLE `couple_registrations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `registration_id` (`registration_id`),
  ADD UNIQUE KEY `uti_number` (`uti_number`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `club_id` (`club_id`);

--
-- Indexes for table `single_registrations`
--
ALTER TABLE `single_registrations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `registration_id` (`registration_id`),
  ADD UNIQUE KEY `uti_number` (`uti_number`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `club_id` (`club_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `clubs`
--
ALTER TABLE `clubs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `cohost_gold_registrations`
--
ALTER TABLE `cohost_gold_registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cohost_platinum_registrations`
--
ALTER TABLE `cohost_platinum_registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cohost_silver_registrations`
--
ALTER TABLE `cohost_silver_registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `couple_registrations`
--
ALTER TABLE `couple_registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `single_registrations`
--
ALTER TABLE `single_registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `couple_registrations`
--
ALTER TABLE `couple_registrations`
  ADD CONSTRAINT `couple_registrations_ibfk_1` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `single_registrations`
--
ALTER TABLE `single_registrations`
  ADD CONSTRAINT `single_registrations_ibfk_1` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
