-- phpMyAdmin SQL Dump
-- version 3.2.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 22, 2010 at 08:17 AM
-- Server version: 5.1.41
-- PHP Version: 5.3.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `gk_trades`
--

-- --------------------------------------------------------

--
-- Table structure for table `trades`
--

DROP TABLE IF EXISTS `trades`;
CREATE TABLE IF NOT EXISTS `trades` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `type` set('t','c','b','o') NOT NULL,
  `date` varchar(10) NOT NULL,
  `price` varchar(50) NOT NULL,
  `make` varchar(20) NOT NULL,
  `model` varchar(20) DEFAULT NULL,
  `tno` varchar(6) DEFAULT NULL,
  `cno` varchar(6) DEFAULT NULL,
  `serialno` varchar(20) DEFAULT NULL,
  `engineno` varchar(20) DEFAULT NULL,
  `enghp` varchar(8) DEFAULT NULL,
  `ptohp` varchar(8) DEFAULT NULL,
  `description` longtext NOT NULL,
  `hours` varchar(8) DEFAULT NULL,
  `hoursdate` varchar(10) DEFAULT NULL,
  `fronttyresize` varchar(20) DEFAULT NULL,
  `fronttyreply` varchar(10) NOT NULL,
  `fronttyrecon` varchar(5) DEFAULT NULL,
  `reartyresize` varchar(20) DEFAULT NULL,
  `reartyreply` varchar(10) NOT NULL,
  `reartyrecon` varchar(5) DEFAULT NULL,
  `comments` longtext,
  `consignname` varchar(255) DEFAULT NULL,
  `consignphone` varchar(20) DEFAULT NULL,
  `consignfax` varchar(20) DEFAULT NULL,
  `consignmobile` varchar(20) DEFAULT NULL,
  `consignabn` varchar(20) DEFAULT NULL,
  `status` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `price` (`price`,`make`,`model`,`tno`,`cno`,`serialno`,`engineno`,`enghp`,`ptohp`,`hours`,`fronttyresize`,`reartyresize`,`consignname`,`consignabn`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `trades`
--

INSERT INTO `trades` (`id`, `type`, `date`, `price`, `make`, `model`, `tno`, `cno`, `serialno`, `engineno`, `enghp`, `ptohp`, `description`, `hours`, `hoursdate`, `fronttyresize`, `fronttyreply`, `fronttyrecon`, `reartyresize`, `reartyreply`, `reartyrecon`, `comments`, `consignname`, `consignphone`, `consignfax`, `consignmobile`, `consignabn`, `status`) VALUES
(1, 'o', '01/02/2010', '$15,000.00 + $1,500.00 GST = $16,500.00', 'JADAN', 'Accumulator & Grab', 'T1071', '', 'Not Visable', '', '', '', 'Jadan Trailing 15 Bale Accumulator Hay Trailer and Jada Grab. Has done very little work. Included is the Hydraulic Hose Kit from Tractor & Baler Drawbar.', '', '', '', '', '', '185/65-14', '', '70', 'Nice, tidy, straight unit.\r\nWorkshop checked and serviced.', 'STEINBECK STUD FARM', '67694251', '', '', '', 'Re-issued'),
(2, 'o', '01/02/2010', '$15,000.00 + $1,500.00 GST = $16,500.00', 'JADAN', 'Accumulator & Grab', 'T1071', '', 'Not Visable', '', '', '', 'Jadan Trailing 15 Bale Accumulator Hay Trailer and Jada Grab. Has done very little work. Included is the Hydraulic Hose Kit from Tractor & Baler Drawbar.', '', '', '', '', '', '185/65-14', '', '70', 'Nice, tidy, straight unit.\r\nWorkshop checked and serviced.', 'cSTEINBECK STUD FARM', '67694251', '', '', '', 'Re-issued'),
(3, 'o', '01/02/2010', '$15,000.00 + $1,500.00 GST = $16,500.00', 'JADAN', 'Accumulator & Grab', 'T1071', '', 'Not Visable', '', '', '', 'Jadan Trailing 15 Bale Accumulator Hay Trailer and Jada Grab. Has done very little work. Included is the Hydraulic Hose Kit from Tractor & Baler Drawbar.', '', '', '', '', '', '185/65', '14', '70', 'Nice, tidy, straight unit.\r\nWorkshop checked and serviced.', 'tSTEINBECK STUD FARM', '67694251', '', '', '', 'Re-issued'),
(4, 't', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
