-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 05, 2024 at 04:15 PM
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
-- Database: `p_002`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointment_details`
--

CREATE TABLE `appointment_details` (
  `appointment_id` varchar(60) NOT NULL,
  `receiver_id` varchar(20) NOT NULL,
  `sender_id` varchar(20) NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` varchar(50) NOT NULL DEFAULT '',
  `event_name` varchar(100) NOT NULL,
  `location` varchar(100) NOT NULL,
  `appointment_status` char(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointment_details`
--

INSERT INTO `appointment_details` (`appointment_id`, `receiver_id`, `sender_id`, `appointment_date`, `appointment_time`, `event_name`, `location`, `appointment_status`) VALUES
('5vCkv43OD5osWdZv7BCPHYRYWIVsjWPwPHoYvdwwjyoowQyDIdgH7doHPxdW', 'Z4whw2o3jDnHkyQA7tIR', 'XoW6v4698ZkoVomfMYhO', '2024-10-05', '11:20', 'general meeting', 'ms teams', 'approved'),
('jYV4hCsLt6LhmLkSgE7hovQfXogSQ9LofvVd8fghhBC46LS0ohqYof5W9W6E', '10BwghsSz2BD8L7Htdfd', 'XoW6v4698ZkoVomfMYhO', '2024-10-05', '11:20', 'general meeting', 'ms teams', 'cancel');

-- --------------------------------------------------------

--
-- Table structure for table `user_account`
--

CREATE TABLE `user_account` (
  `user_id` varchar(20) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) NOT NULL,
  `search_name` varchar(200) NOT NULL,
  `contact_number` varchar(10) NOT NULL,
  `email_address` varchar(100) NOT NULL,
  `user_password` varchar(200) NOT NULL,
  `profile_picture` blob NOT NULL,
  `last_update` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_account`
--

INSERT INTO `user_account` (`user_id`, `first_name`, `middle_name`, `last_name`, `search_name`, `contact_number`, `email_address`, `user_password`, `profile_picture`, `last_update`) VALUES
('10BwghsSz2BD8L7Htdfd', 'Stacy', 'Smith', 'Baluyot', 'STACY BALUYOT', '9647023773', 'stacybaluyot@example.com', 'stacybaluyot', 0x75706c6f6164732f363664313435356230663463372e706e67, NULL),
('aVpQWBEhr40pNnGFPjCK', 'Rome', 'Manlangit', 'Montecillo', 'ROME MONTECILLO', '9848282983', 'romentecillo.mantlangit@example.com', 'romeanlangit', 0x75706c6f6164732f363664313436313839303637622e706e67, NULL),
('iwpmNfR1NzAxZ030or59', 'Mabeth', 'Rosalia', 'Ramos', 'MABETH RAMOS', '9731676318', 'rosaliaramos@example.com', 'rosaliaramos', 0x75706c6f6164732f363664313434653130663964632e706e67, NULL),
('m3MQ8peXehSvz6Ad4nrS', 'Joe', 'Maiden', 'Mateo', 'JOE MATEO', '9738383882', 'joemateo@example.com', 'joemateo', 0x75706c6f6164732f363664313435396133626632612e706e67, NULL),
('XoW6v4698ZkoVomfMYhO', 'Amber', 'Mobito', 'Cruz', 'AMBER CRUZ', '9646367228', 'ambermobito.cruz@example.com', 'ambermobito', 0x75706c6f6164732f363664313435316333353265622e706e67, NULL),
('Z4whw2o3jDnHkyQA7tIR', 'Luicito', 'Santos', 'Materan', 'LUICITO MATERAN', '9377337338', 'luisito_materan.santos@example.com', 'materanluisito', 0x75706c6f6164732f363664313435643865636364382e706e67, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointment_details`
--
ALTER TABLE `appointment_details`
  ADD PRIMARY KEY (`appointment_id`),
  ADD KEY `receiver_id` (`receiver_id`),
  ADD KEY `sender_id` (`sender_id`);

--
-- Indexes for table `user_account`
--
ALTER TABLE `user_account`
  ADD PRIMARY KEY (`user_id`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointment_details`
--
ALTER TABLE `appointment_details`
  ADD CONSTRAINT `appointment_details_ibfk_1` FOREIGN KEY (`receiver_id`) REFERENCES `user_account` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointment_details_ibfk_2` FOREIGN KEY (`sender_id`) REFERENCES `user_account` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
