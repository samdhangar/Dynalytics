-- phpMyAdmin SQL Dump
-- version 4.8.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 13, 2020 at 12:27 PM
-- Server version: 10.1.34-MariaDB
-- PHP Version: 7.2.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dynalitics`
--

-- --------------------------------------------------------

--
-- Table structure for table `vault_time`
--

CREATE TABLE `vault_time` (
  `id` int(11) NOT NULL,
  `file_id` int(11) DEFAULT NULL,
  `security_settings_id` int(11) DEFAULT NULL,
  `section` int(11) DEFAULT NULL,
  `monday_start` varchar(255) DEFAULT NULL,
  `monday_end` varchar(255) DEFAULT NULL,
  `tuesday_start` varchar(255) DEFAULT NULL,
  `tuesday_end` varchar(255) DEFAULT NULL,
  `wednesday_start` varchar(255) DEFAULT NULL,
  `wednesday_end` varchar(255) DEFAULT NULL,
  `thursday_start` varchar(255) DEFAULT NULL,
  `thursday_end` varchar(255) DEFAULT NULL,
  `friday_start` varchar(255) DEFAULT NULL,
  `friday_end` varchar(255) DEFAULT NULL,
  `saturday_start` varchar(255) DEFAULT NULL,
  `saturday_end` varchar(255) DEFAULT NULL,
  `sunday_start` varchar(255) DEFAULT NULL,
  `sunday_end` varchar(255) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `vault_time`
--
ALTER TABLE `vault_time`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `vault_time`
--
ALTER TABLE `vault_time`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
