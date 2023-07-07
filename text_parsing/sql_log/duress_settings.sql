-- phpMyAdmin SQL Dump
-- version 4.8.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 13, 2020 at 12:26 PM
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
-- Table structure for table `duress_settings`
--

CREATE TABLE `duress_settings` (
  `id` int(11) NOT NULL,
  `file_id` int(11) DEFAULT NULL,
  `security_settings_id` int(11) DEFAULT NULL,
  `section` int(11) DEFAULT NULL,
  `duress_lockout` varchar(255) DEFAULT NULL,
  `dynacore_alarm` varchar(255) DEFAULT NULL,
  `machine_alarm` varchar(255) DEFAULT NULL,
  `autologoff` varchar(255) DEFAULT NULL,
  `duress_lumpsum` int(11) DEFAULT NULL,
  `denom_100_pieces` int(11) DEFAULT NULL,
  `denom_50_pieces` int(11) DEFAULT NULL,
  `denom_20_pieces` int(11) DEFAULT NULL,
  `denom_10_pieces` int(11) DEFAULT NULL,
  `denom_5_pieces` int(11) DEFAULT NULL,
  `denom_1_pieces` int(11) DEFAULT NULL,
  `denom_100_value` double(10,2) DEFAULT NULL,
  `denom_50_value` double(10,2) DEFAULT NULL,
  `denom_20_value` double(10,2) DEFAULT NULL,
  `denom_10_value` double(10,2) DEFAULT NULL,
  `denom_5_value` double(10,2) DEFAULT NULL,
  `denom_1_value` double(10,2) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `duress_settings`
--
ALTER TABLE `duress_settings`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `duress_settings`
--
ALTER TABLE `duress_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
