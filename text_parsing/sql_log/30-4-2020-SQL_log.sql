-- phpMyAdmin SQL Dump
-- version 4.8.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 30, 2020 at 09:16 AM
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
-- Table structure for table `alert_event`
--

CREATE TABLE `alert_event` (
  `id` int(11) NOT NULL,
  `file_id` int(11) NOT NULL,
  `section` int(11) NOT NULL,
  `user_login_id` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `message` varchar(255) NOT NULL,
  `datetime` datetime NOT NULL,
  `created_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `automix_settings`
--

CREATE TABLE `automix_settings` (
  `id` int(11) NOT NULL,
  `file_id` int(11) DEFAULT NULL,
  `section` int(11) DEFAULT NULL,
  `inventory_settings_id` int(11) DEFAULT NULL,
  `denom` int(11) DEFAULT NULL,
  `low` int(11) DEFAULT NULL,
  `mid` int(11) DEFAULT NULL,
  `high` int(11) DEFAULT NULL,
  `weight` int(11) DEFAULT NULL,
  `odds` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `config_mode_access`
--

CREATE TABLE `config_mode_access` (
  `id` int(11) NOT NULL,
  `file_id` int(11) DEFAULT NULL,
  `section` int(11) DEFAULT NULL,
  `enter_datetime` datetime DEFAULT NULL,
  `enter_by_user` varchar(255) DEFAULT NULL,
  `exit_datetime` datetime DEFAULT NULL,
  `exit_by_user` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `config_mode_activity`
--

CREATE TABLE `config_mode_activity` (
  `id` int(11) NOT NULL,
  `file_id` int(11) DEFAULT NULL,
  `config_mode_access_id` int(11) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `action_datetime` datetime DEFAULT NULL,
  `action_affected_id` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `daily_file_processing_detail`
--

CREATE TABLE `daily_file_processing_detail` (
  `id` int(11) NOT NULL,
  `file_processing_detail_id` int(10) NOT NULL,
  `filename` varchar(100) NOT NULL,
  `company_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `station` varchar(255) NOT NULL,
  `file_date` date NOT NULL,
  `row_number` int(11) DEFAULT NULL,
  `processing_counter` int(11) DEFAULT '0',
  `processing_starttime` datetime DEFAULT NULL,
  `processing_endtime` datetime DEFAULT NULL,
  `transaction_number` int(11) DEFAULT NULL,
  `adjustment_number` int(11) DEFAULT NULL,
  `activity_report_number` int(11) DEFAULT NULL,
  `total_amount_tendered` decimal(10,2) DEFAULT NULL,
  `total_deposit` decimal(10,2) DEFAULT NULL,
  `created_date` datetime NOT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `updated_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `dispensable_notes`
--

CREATE TABLE `dispensable_notes` (
  `id` int(11) NOT NULL,
  `file_id` int(11) DEFAULT NULL,
  `section` int(11) DEFAULT NULL,
  `inventory_id` int(11) DEFAULT NULL,
  `denom_100_pieces` int(11) DEFAULT NULL,
  `denom_100_value` double(10,2) DEFAULT NULL,
  `denom_50_pieces` int(11) DEFAULT NULL,
  `denom_50_value` double(10,2) DEFAULT NULL,
  `denom_20_pieces` int(11) DEFAULT NULL,
  `denom_20_value` double(10,2) DEFAULT NULL,
  `denom_10_pieces` int(11) DEFAULT NULL,
  `denom_10_value` double(10,2) DEFAULT NULL,
  `denom_5_pieces` int(11) DEFAULT NULL,
  `denom_5_value` double(10,2) DEFAULT NULL,
  `denom_2_pieces` int(11) DEFAULT NULL,
  `denom_2_value` double(10,2) DEFAULT NULL,
  `denom_1_pieces` int(11) DEFAULT NULL,
  `denom_1_value` double(10,2) DEFAULT NULL,
  `total_dispensable_notes` double(10,2) DEFAULT NULL,
  `case_1` int(11) DEFAULT NULL,
  `case_1_pieces` int(11) DEFAULT NULL,
  `case_1_value` double(10,2) DEFAULT NULL,
  `case_2` int(11) DEFAULT NULL,
  `case_2_pieces` int(11) DEFAULT NULL,
  `case_2_value` double(10,2) DEFAULT NULL,
  `case_3` int(11) DEFAULT NULL,
  `case_3_pieces` int(11) DEFAULT NULL,
  `case_3_value` double(10,2) DEFAULT NULL,
  `case_4` int(11) DEFAULT NULL,
  `case_4_pieces` int(11) DEFAULT NULL,
  `case_4_value` double(10,2) DEFAULT NULL,
  `case_5` int(11) DEFAULT NULL,
  `case_5_pieces` int(11) DEFAULT NULL,
  `case_5_value` double(10,2) DEFAULT NULL,
  `case_6` int(11) DEFAULT NULL,
  `case_6_pieces` int(11) DEFAULT NULL,
  `case_6_value` double(10,2) DEFAULT NULL,
  `case_a_low` int(11) DEFAULT NULL,
  `case_b_low` int(11) DEFAULT NULL,
  `case_c_low` int(11) DEFAULT NULL,
  `case_d_low` int(11) DEFAULT NULL,
  `case_e_low` int(11) DEFAULT NULL,
  `case_f_low` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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

-- --------------------------------------------------------

--
-- Table structure for table `dynacore_functions`
--

CREATE TABLE `dynacore_functions` (
  `id` int(11) NOT NULL,
  `function_name` varchar(255) NOT NULL,
  `created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `dynacore_groups`
--

CREATE TABLE `dynacore_groups` (
  `id` int(11) NOT NULL,
  `file_id` int(11) NOT NULL,
  `section` int(11) NOT NULL,
  `station` int(11) NOT NULL,
  `branch` varchar(255) NOT NULL,
  `region` varchar(255) NOT NULL,
  `report_datetime` datetime NOT NULL,
  `created_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `dynacore_groups_details`
--

CREATE TABLE `dynacore_groups_details` (
  `id` int(11) NOT NULL,
  `file_id` int(11) DEFAULT NULL,
  `section` int(11) DEFAULT NULL,
  `dynacore_group_id` int(11) DEFAULT NULL,
  `group_name` varchar(255) DEFAULT NULL,
  `allowed_function` varchar(255) DEFAULT NULL,
  `tansaction_limit` varchar(255) DEFAULT NULL,
  `daily_limit` varchar(255) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `dynacore_users`
--

CREATE TABLE `dynacore_users` (
  `id` int(11) NOT NULL,
  `file_id` int(11) DEFAULT NULL,
  `section` int(11) DEFAULT NULL,
  `station` int(11) DEFAULT NULL,
  `branch` varchar(255) DEFAULT NULL,
  `region` varchar(255) DEFAULT NULL,
  `report_datetime` datetime DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `group_roles` varchar(255) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `dynassign_session_status`
--

CREATE TABLE `dynassign_session_status` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `file_processing_detail_id` bigint(20) NOT NULL,
  `admin` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `datetime` datetime DEFAULT NULL,
  `messages` text COLLATE utf8_bin,
  `created_date` datetime DEFAULT NULL,
  `created_by` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `updated_by` varchar(100) COLLATE utf8_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `hardware_settings`
--

CREATE TABLE `hardware_settings` (
  `id` int(11) NOT NULL,
  `file_id` varchar(255) DEFAULT NULL,
  `section` varchar(255) DEFAULT NULL,
  `station` varchar(255) DEFAULT NULL,
  `branch` varchar(255) DEFAULT NULL,
  `region` varchar(255) DEFAULT NULL,
  `tcd_tcr_address` varchar(255) DEFAULT NULL,
  `tcd_tcr_port` varchar(255) DEFAULT NULL,
  `tcd_tcr_connection` varchar(255) DEFAULT NULL,
  `coin_dispenser` varchar(255) DEFAULT NULL,
  `bill_discriminator` varchar(255) DEFAULT NULL,
  `coin_sorter` varchar(255) DEFAULT NULL,
  `use_printer` varchar(255) DEFAULT NULL,
  `pre_pass_max_dispense_count` int(11) DEFAULT NULL,
  `transaction_end_line_feeds` int(11) DEFAULT NULL,
  `report_end_line_feeds` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_settings`
--

CREATE TABLE `inventory_settings` (
  `id` int(11) NOT NULL,
  `file_id` int(11) DEFAULT NULL,
  `section` int(11) DEFAULT NULL,
  `station` int(11) DEFAULT NULL,
  `branch` int(11) DEFAULT NULL,
  `region` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_warn_bundle`
--

CREATE TABLE `inventory_warn_bundle` (
  `id` int(11) NOT NULL,
  `file_id` int(11) DEFAULT NULL,
  `section` int(11) DEFAULT NULL,
  `inventory_settings_id` int(11) DEFAULT NULL,
  `denom` int(11) DEFAULT NULL,
  `warn_low` int(11) DEFAULT NULL,
  `warn_high` int(11) DEFAULT NULL,
  `bundle_teller` int(11) DEFAULT NULL,
  `bundle_manager` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `machine_users`
--

CREATE TABLE `machine_users` (
  `id` int(11) NOT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `manager_activity`
--

CREATE TABLE `manager_activity` (
  `id` int(11) NOT NULL,
  `file_id` int(11) DEFAULT NULL,
  `section` int(11) DEFAULT NULL,
  `manager_activity_id` int(11) DEFAULT NULL,
  `activity_name` varchar(255) DEFAULT NULL,
  `activity_date` date DEFAULT NULL,
  `activity_time` time DEFAULT NULL,
  `created_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `id` int(11) NOT NULL,
  `file_id` int(11) NOT NULL,
  `section` varchar(255) NOT NULL,
  `created_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `security_settings`
--

CREATE TABLE `security_settings` (
  `id` int(11) NOT NULL,
  `file_id` int(11) DEFAULT NULL,
  `section` int(11) DEFAULT NULL,
  `station` int(11) DEFAULT NULL,
  `branch` int(11) DEFAULT NULL,
  `region` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user_settings`
--

CREATE TABLE `user_settings` (
  `id` int(11) NOT NULL,
  `file_id` int(11) DEFAULT NULL,
  `section` int(11) DEFAULT NULL,
  `station` varchar(255) DEFAULT NULL,
  `branch` varchar(255) DEFAULT NULL,
  `region` varchar(255) DEFAULT NULL,
  `active_directory` varchar(255) DEFAULT NULL,
  `batch_total` varchar(255) DEFAULT NULL,
  `ibutton_mode` varchar(255) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
-- Indexes for table `alert_event`
--
ALTER TABLE `alert_event`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `automix_settings`
--
ALTER TABLE `automix_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `config_mode_access`
--
ALTER TABLE `config_mode_access`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `config_mode_activity`
--
ALTER TABLE `config_mode_activity`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `daily_file_processing_detail`
--
ALTER TABLE `daily_file_processing_detail`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dispensable_notes`
--
ALTER TABLE `dispensable_notes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `duress_settings`
--
ALTER TABLE `duress_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dynacore_functions`
--
ALTER TABLE `dynacore_functions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dynacore_groups`
--
ALTER TABLE `dynacore_groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dynacore_groups_details`
--
ALTER TABLE `dynacore_groups_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dynacore_users`
--
ALTER TABLE `dynacore_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dynassign_session_status`
--
ALTER TABLE `dynassign_session_status`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hardware_settings`
--
ALTER TABLE `hardware_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory_settings`
--
ALTER TABLE `inventory_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory_warn_bundle`
--
ALTER TABLE `inventory_warn_bundle`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `machine_users`
--
ALTER TABLE `machine_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `manager_activity`
--
ALTER TABLE `manager_activity`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `security_settings`
--
ALTER TABLE `security_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_settings`
--
ALTER TABLE `user_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vault_time`
--
ALTER TABLE `vault_time`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `alert_event`
--
ALTER TABLE `alert_event`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `automix_settings`
--
ALTER TABLE `automix_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `config_mode_access`
--
ALTER TABLE `config_mode_access`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `config_mode_activity`
--
ALTER TABLE `config_mode_activity`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `daily_file_processing_detail`
--
ALTER TABLE `daily_file_processing_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dispensable_notes`
--
ALTER TABLE `dispensable_notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `duress_settings`
--
ALTER TABLE `duress_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dynacore_functions`
--
ALTER TABLE `dynacore_functions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dynacore_groups`
--
ALTER TABLE `dynacore_groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dynacore_groups_details`
--
ALTER TABLE `dynacore_groups_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dynacore_users`
--
ALTER TABLE `dynacore_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dynassign_session_status`
--
ALTER TABLE `dynassign_session_status`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hardware_settings`
--
ALTER TABLE `hardware_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_settings`
--
ALTER TABLE `inventory_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_warn_bundle`
--
ALTER TABLE `inventory_warn_bundle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `machine_users`
--
ALTER TABLE `machine_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `manager_activity`
--
ALTER TABLE `manager_activity`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `security_settings`
--
ALTER TABLE `security_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_settings`
--
ALTER TABLE `user_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vault_time`
--
ALTER TABLE `vault_time`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
