-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Generation Time: Apr 04, 2023 at 12:08 PM
-- Server version: 10.9.4-MariaDB-1:10.9.4+maria~ubu2204
-- PHP Version: 8.0.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `clinicdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `clinic_sections`
--

CREATE TABLE `clinic_sections` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clinic_sections`
--

INSERT INTO `clinic_sections` (`id`, `name`) VALUES
(1, 'Cardiology'),
(2, 'Otorhinolaryngology'),
(3, 'Ophthalmology'),
(4, 'Dermatology'),
(5, 'General Surgery'),
(6, 'Neurology'),
(7, 'Pediatrics'),
(8, 'Psychiatry');

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `section_id` int(11) NOT NULL,
  `email` varchar(150) NOT NULL,
  `date_of_birth` date NOT NULL,
  `phone_number` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`id`, `name`, `section_id`, `email`, `date_of_birth`, `phone_number`) VALUES
(1, 'Mark Johnson', 8, 'markjohnson@gmail.com', '1990-01-11', '06-45678954'),
(2, 'Pip Roozeboom', 1, 'piproozeboom@gmail.com', '1970-07-04', '06-56507183'),
(3, 'Chantall Geel', 4, 'chantallgeel@gmail.com', '1982-09-12', '06-56037028'),
(4, 'Klaus Wagner', 5, 'klauswagner@gmail.com', '1969-06-09', '06-16852938'),
(5, 'Charles Blake', 2, 'charlesblake@gmail.com', '1983-10-24', '06-25586313'),
(6, 'Laura Oliveira', 3, 'lauraoliveira@gmail.com', '1995-08-16', '06-42505263'),
(7, 'Robin van der Plaats', 6, 'robinplaats@gmail.com', '1959-03-19', '06-12181272'),
(8, 'Fiona Loonstra', 7, 'fionaloonstra@gmail.com', '1977-05-27', '06-40099217'),
(9, 'Cristian Barese', 5, 'cristianbarese@gmail.com', '1981-09-07', '06-77297066'),
(10, 'Reitze Polman', 3, 'reitzepolman@gmail.com', '1997-02-23', '06-21346723'),
(11, 'Jacob Greene', 6, 'jacobgreene@gmail.com', '1972-12-19', '06-87445522'),
(12, 'Linnea Karlsen', 7, 'linneakarlsen@gmail.com', '1987-11-05', '06-13566689');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(75) NOT NULL,
  `last_name` varchar(75) NOT NULL,
  `address` varchar(255) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `date_of_birth` date NOT NULL,
  `gender` varchar(20) NOT NULL,
  `email` varchar(75) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_type_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `address`, `phone_number`, `date_of_birth`, `gender`, `email`, `password`, `user_type_id`) VALUES
(1, 'Rares', 'Simion', 'Amsterdam, Humberto Delgadoplein', '0787646478', '2002-08-08', 'Male', 'rares@gmail.com', '$2y$10$NdBG/l8uPraDPUF0uobTq.stBN3j5KG3mGun5/BfqQOs.pbsgUXBq', 1),
(2, 'Jon', 'Jones', 'Haarlem, Wagenweg ', '067635789', '1990-06-23', 'Male', 'bones@gmail.com', '$2y$10$Wot8SXL2kIDfE6UiMaUPk.FwJCg1jJ5KU97BzpoYR8N2g8UdehVF.', 2);

-- --------------------------------------------------------

--
-- Table structure for table `user_types`
--

CREATE TABLE `user_types` (
  `id` int(11) NOT NULL,
  `name` varchar(75) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_types`
--

INSERT INTO `user_types` (`id`, `name`) VALUES
(1, 'Admin'),
(2, 'Regular');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `appointments_ibfk_1` (`user_id`),
  ADD KEY `appointments_ibfk_2` (`doctor_id`);

--
-- Indexes for table `clinic_sections`
--
ALTER TABLE `clinic_sections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `section` (`section_id`) USING BTREE;

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_type` (`user_type_id`);

--
-- Indexes for table `user_types`
--
ALTER TABLE `user_types`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `clinic_sections`
--
ALTER TABLE `clinic_sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_types`
--
ALTER TABLE `user_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `doctors`
--
ALTER TABLE `doctors`
  ADD CONSTRAINT `doctors_ibfk_1` FOREIGN KEY (`section_id`) REFERENCES `clinic_sections` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`user_type_id`) REFERENCES `user_types` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
