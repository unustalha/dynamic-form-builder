-- phpMyAdmin SQL Dump
-- version 4.4.3
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 31, 2015 at 04:36 PM
-- Server version: 5.6.24
-- PHP Version: 5.5.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `formbuilder`
--

-- --------------------------------------------------------

--
-- Table structure for table `element_options`
--

CREATE TABLE IF NOT EXISTS `element_options` (
  `TableID` int(11) NOT NULL,
  `ElementID` int(11) DEFAULT NULL,
  `Value` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `forms`
--

CREATE TABLE IF NOT EXISTS `forms` (
  `TableID` int(11) NOT NULL,
  `FormName` varchar(255) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `TableName` varchar(255) DEFAULT NULL,
  `ReturnURL` varchar(255) DEFAULT NULL,
  `ThankYouMessage` varchar(255) DEFAULT 'Thank You',
  `EmailToSend` varchar(255) DEFAULT NULL,
  `SaveInDB` tinyint(1) DEFAULT '1',
  `CreatedDate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `form_elements`
--

CREATE TABLE IF NOT EXISTS `form_elements` (
  `TableID` int(11) NOT NULL,
  `FormID` int(11) DEFAULT NULL,
  `Label` varchar(255) DEFAULT NULL,
  `HTML_Name` varchar(255) DEFAULT NULL,
  `Type` tinyint(1) DEFAULT NULL COMMENT '0',
  `IsRequired` tinyint(1) DEFAULT NULL COMMENT '0',
  `IsNumeric` tinyint(1) DEFAULT NULL COMMENT '0',
  `IsEmail` tinyint(1) DEFAULT NULL COMMENT '0',
  `ShowInListing` tinyint(1) DEFAULT '1',
  `Sequence` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `element_options`
--
ALTER TABLE `element_options`
  ADD PRIMARY KEY (`TableID`),
  ADD KEY `FK_element_options` (`ElementID`);

--
-- Indexes for table `forms`
--
ALTER TABLE `forms`
  ADD PRIMARY KEY (`TableID`),
  ADD KEY `NewIndex1` (`TableName`),
  ADD KEY `NewIndex2` (`CreatedDate`);

--
-- Indexes for table `form_elements`
--
ALTER TABLE `form_elements`
  ADD PRIMARY KEY (`TableID`),
  ADD KEY `NewIndex1` (`FormID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `element_options`
--
ALTER TABLE `element_options`
  MODIFY `TableID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `forms`
--
ALTER TABLE `forms`
  MODIFY `TableID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `form_elements`
--
ALTER TABLE `form_elements`
  MODIFY `TableID` int(11) NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `element_options`
--
ALTER TABLE `element_options`
  ADD CONSTRAINT `FK_element_options` FOREIGN KEY (`ElementID`) REFERENCES `form_elements` (`TableID`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `form_elements`
--
ALTER TABLE `form_elements`
  ADD CONSTRAINT `FK_form_elements` FOREIGN KEY (`FormID`) REFERENCES `forms` (`TableID`) ON DELETE CASCADE ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
