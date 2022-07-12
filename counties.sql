-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 12, 2022 at 10:05 AM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 7.4.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bukstack`
--

-- --------------------------------------------------------

--
-- Table structure for table `counties`
--

CREATE TABLE `counties` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `url` text COLLATE utf8_unicode_ci NOT NULL,
  `key_sectors` text COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `counties`
--

INSERT INTO `counties` (`id`, `name`, `description`, `url`, `key_sectors`, `created_at`, `updated_at`) VALUES
(48, 'BOMET', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(49, 'BUNGOMA', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(50, 'BUSIA', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(51, 'ELGEYO/MARAKWET', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(52, 'EMBU', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(53, 'GARISSA', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(54, 'HOMA BAY', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(55, 'ISIOLO', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(56, 'KAJIADO', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(57, 'KAKAMEGA', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(58, 'KERICHO', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(59, 'KIAMBU', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(60, 'KILIFI', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(61, 'KIRINYAGA', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(62, 'KISII', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(63, 'KISUMU', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(64, 'KITUI', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(65, 'KWALE', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(66, 'LAIKIPIA', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(67, 'LAMU', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(68, 'MACHAKOS', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(69, 'MAKUENI', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(70, 'MANDERA', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(71, 'MARSABIT', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(72, 'MERU', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(73, 'MIGORI', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(74, 'MOMBASA', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(75, 'MURANGA', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(76, 'NAIROBI', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(77, 'NAKURU', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(78, 'NANDI', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(79, 'NAROK', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(80, 'NYAMIRA', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(81, 'NYANDARUA', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(82, 'NYERI', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(83, 'SAMBURU', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(84, 'SIAYA', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(85, 'TAITA TAVETA', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(86, 'TANA RIVER', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(87, 'THARAKA - NITHI', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(88, 'TRANS NZOIA', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(89, 'TURKANA', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(90, 'UASIN GISHU', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(91, 'VIHIGA', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(92, 'WAJIR', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(93, 'WEST POKOT', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(94, 'BARINGO', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `counties`
--
ALTER TABLE `counties`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `counties`
--
ALTER TABLE `counties`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
