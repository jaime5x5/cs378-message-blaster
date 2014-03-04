-- phpMyAdmin SQL Dump
-- version 3.3.7deb7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 03, 2014 at 01:10 AM
-- Server version: 5.1.73
-- PHP Version: 5.3.3-7+squeeze18

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `message_blaster`
--

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE IF NOT EXISTS `companies` (
  `company_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `pwd` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `is_admin` tinyint(1) NOT NULL,
  PRIMARY KEY (`company_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`company_id`, `company_name`, `pwd`, `is_admin`) VALUES
(1, 'brian', 'pass123', 1),
(2, 'stephen', 'pass123', 1),
(3, 'jaime', 'pass', 0);

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE IF NOT EXISTS `customers` (
  `customer_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned NOT NULL,
  `customer_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `customer_email` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `use_email` tinyint(1) NOT NULL,
  `customer_phone` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `use_phone` tinyint(1) NOT NULL,
  PRIMARY KEY (`customer_id`),
  KEY `company_id` (`company_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `company_id`, `customer_name`, `customer_email`, `use_email`, `customer_phone`, `use_phone`) VALUES
(1, 3, 'jaime williams', 'jaime.williams@eagles.ewu.edu', 1, '5092168147', 1),
(2, 3, 'jaime', 'twobits@techie.com', 0, '5092168147', 1),
(3, 3, 'jdw', 'jaime@long-technical.com', 0, '5092168147', 0);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE IF NOT EXISTS `messages` (
  `message_id` int(10) NOT NULL AUTO_INCREMENT,
  `message_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `message_content` varchar(170) COLLATE utf8_unicode_ci NOT NULL,
  `company_id` int(10) unsigned NOT NULL,
  `rx_by` varchar(75) COLLATE utf8_unicode_ci NOT NULL,
  `medium` varchar(75) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`message_id`),
  FULLTEXT KEY `message_content` (`message_content`),
  FULLTEXT KEY `message_content_2` (`message_content`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`message_id`, `message_time`, `message_content`, `company_id`, `rx_by`, `medium`) VALUES
(1, '2014-03-03 00:54:20', 'Getting tired.', 3, 'jaime williams', '5092168147'),
(2, '2014-03-03 00:54:20', 'Getting tired.', 3, 'jaime williams', 'jaime.williams@eagles.ewu.edu'),
(3, '2014-03-03 00:54:20', 'Getting tired.', 3, 'jdw', 'jaime@long-technical.com');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `customers_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`company_id`) ON DELETE CASCADE ON UPDATE CASCADE;
