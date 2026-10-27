-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 22, 2011 at 02:27 AM
-- Server version: 5.5.8
-- PHP Version: 5.3.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `taxtool`
--

-- --------------------------------------------------------

--
-- Table structure for table `sfd_data`
--

CREATE TABLE IF NOT EXISTS `sfd_data` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Data_Type` enum('BUSINESS','REAL','PROFESSIONAL','FISHING') NOT NULL,
  `user_id` int(10) unsigned NOT NULL COMMENT 'references users',
  `tax_year` smallint(4) unsigned NOT NULL,
  `BusinessNumber` char(15) DEFAULT NULL,
  `NAICSCODE` int(6) NOT NULL,
  `BusinessName` char(30) DEFAULT NULL,
  `PartnershipNumber` int(9) DEFAULT NULL,
  `TaxShelterNumber` char(8) DEFAULT NULL,
  `BusinessAddressStreetText` char(30) DEFAULT NULL,
  `AddressPostalCode` char(6) DEFAULT NULL,
  `FiscalPeriodStartDate` date NOT NULL,
  `FiscalPeriodeEndDate` date NOT NULL,
  `BusinessOwnershipPercent` int(4) DEFAULT NULL,
  `FinalBusinessYearIndicator` enum('1','2') DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `sfd_data`
--

REPLACE INTO `sfd_data` (`id`, `Data_Type`, `user_id`, `tax_year`, `BusinessNumber`, `NAICSCODE`, `BusinessName`, `PartnershipNumber`, `TaxShelterNumber`, `BusinessAddressStreetText`, `AddressPostalCode`, `FiscalPeriodStartDate`, `FiscalPeriodeEndDate`, `BusinessOwnershipPercent`, `FinalBusinessYearIndicator`, `date_created`, `date_updated`) VALUES
(1, 'REAL', 2, 2010, '123456789RT1234', 531111, NULL, NULL, NULL, NULL, NULL, '2011-01-01', '2011-12-31', 8450, '1', '2011-10-21 22:00:54', '2011-10-21 22:00:54'),
(2, 'BUSINESS', 2, 2010, '334455667AA8990', 123456, 'HAPPY TAXES LTD.', 123456789, 'AA123456', '12 GREEN ST.', 'K1B2F9', '2011-01-01', '2011-12-31', 8500, '1', '2011-10-21 22:00:54', '2011-10-21 22:00:54'),
(3, 'PROFESSIONAL', 2, 2010, '123456789AA2345', 541110, 'James Publishing', 123456789, 'AB123456', '20 Spicer Street', 'R2Y1C4', '2011-01-01', '2011-12-31', 0, '2', '2011-10-21 22:04:40', '2011-10-21 22:04:40'),
(4, 'FISHING', 2, 2010, '123456789AA2345', 114133, 'Colin''s Fishery', 123456789, 'AA123456', NULL, NULL, '2011-01-01', '2011-12-31', 0, '1', '2011-10-21 22:04:40', '2011-10-21 22:04:40');
