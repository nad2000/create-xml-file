-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 22, 2011 at 02:29 AM
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
-- Table structure for table `partner`
--

CREATE TABLE IF NOT EXISTS `partner` (
  `SFD_ID` int(10) NOT NULL COMMENT 'references SFD_DATA::ID',
  `Partner_ID` int(10) NOT NULL AUTO_INCREMENT,
  `GivenName` char(30) DEFAULT NULL,
  `FamilyName` char(30) NOT NULL,
  `PIN` int(8) NOT NULL,
  `OwnershipPercent` int(6) NOT NULL,
  PRIMARY KEY (`Partner_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `partner`
--

REPLACE INTO `partner` (`SFD_ID`, `Partner_ID`, `GivenName`, `FamilyName`, `PIN`, `OwnershipPercent`) VALUES
(1, 1, 'John', 'Doe', 0, 155000),
(1, 2, 'Traver', 'Smith', 1234, 105000),
(2, 3, 'MAC', 'BLACK', 0, 150000),
(2, 4, 'John', 'Spencer', 0, 100000),
(0, 5, NULL, '', 0, 0),
(3, 6, NULL, '', 0, 0);
