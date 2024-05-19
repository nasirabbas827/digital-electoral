-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 19, 2024 at 08:22 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `chairperson_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(1, 'admin', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `candidates`
--

CREATE TABLE `candidates` (
  `Candidate_ID` int(11) NOT NULL,
  `Profile_Pic` varchar(255) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Department` varchar(100) NOT NULL,
  `Gender` enum('Male','Female') NOT NULL,
  `Position` varchar(100) NOT NULL DEFAULT 'Chairperson',
  `Approval_Status` enum('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `candidates`
--

INSERT INTO `candidates` (`Candidate_ID`, `Profile_Pic`, `Name`, `Department`, `Gender`, `Position`, `Approval_Status`) VALUES
(2, 'candidate_pics/664986d08f5bb.jpg', 'Shahbaz', 'CS', 'Male', 'Chairperson', 'Approved'),
(3, 'candidate_pics/664986d08f5bb.jpg', 'Shahbaz  Malik', 'IT', 'Male', 'Chairperson', 'Approved');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `gender` enum('male','female') NOT NULL,
  `department` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `gender`, `department`) VALUES
(1, 'NASIR ABBAS', 'nasiryt.827@gmail.com', '$2y$10$viGaX05KRk6Lda0JGhZ1Vuvi.frgoXnoeEtkVr/vR5xgbMFiHODiO', 'male', 'IT'),
(2, 'Gift 1', 'nasiryt.8272@gmail.com', '$2y$10$xM.lwR2wtEY/4uoIJxdFtuip57cKj7hK4xE.aIcO0kufRFPfqeWq6', 'male', 'IT');

-- --------------------------------------------------------

--
-- Table structure for table `voter_table`
--

CREATE TABLE `voter_table` (
  `Voter_ID` int(11) NOT NULL,
  `Voter_Password` varchar(255) NOT NULL,
  `Registered_Date` timestamp NOT NULL DEFAULT current_timestamp(),
  `Approval_Status` enum('Approved','Rejected') DEFAULT 'Approved',
  `user_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `voter_table`
--

INSERT INTO `voter_table` (`Voter_ID`, `Voter_Password`, `Registered_Date`, `Approval_Status`, `user_ID`) VALUES
(1, '123', '2024-05-19 04:45:08', 'Approved', 1),
(2, '123', '2024-05-19 05:57:42', 'Approved', 2);

-- --------------------------------------------------------

--
-- Table structure for table `votes`
--

CREATE TABLE `votes` (
  `Vote_ID` int(11) NOT NULL,
  `Voter_ID` varchar(50) NOT NULL,
  `Candidate_ID` int(11) NOT NULL,
  `Vote_Date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `votes`
--

INSERT INTO `votes` (`Vote_ID`, `Voter_ID`, `Candidate_ID`, `Vote_Date`) VALUES
(4, '1', 3, '2024-05-19 05:43:52'),
(7, '2', 3, '2024-05-19 06:05:12');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `candidates`
--
ALTER TABLE `candidates`
  ADD PRIMARY KEY (`Candidate_ID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `voter_table`
--
ALTER TABLE `voter_table`
  ADD PRIMARY KEY (`Voter_ID`),
  ADD KEY `user_ID` (`user_ID`);

--
-- Indexes for table `votes`
--
ALTER TABLE `votes`
  ADD PRIMARY KEY (`Vote_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `candidates`
--
ALTER TABLE `candidates`
  MODIFY `Candidate_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `voter_table`
--
ALTER TABLE `voter_table`
  MODIFY `Voter_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `votes`
--
ALTER TABLE `votes`
  MODIFY `Vote_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `voter_table`
--
ALTER TABLE `voter_table`
  ADD CONSTRAINT `voter_table_ibfk_1` FOREIGN KEY (`user_ID`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
