-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 11, 2024 at 05:46 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `EzTickEntry`
--

-- --------------------------------------------------------

--
-- Table structure for table `Bookings`
--

CREATE TABLE `Bookings` (
  `BookingID` int(11) NOT NULL,
  `UserID` int(11) DEFAULT NULL,
  `EventID` int(11) DEFAULT NULL,
  `BookingTime` datetime NOT NULL,
  `TotalPrice` decimal(10,2) NOT NULL,
  `BookingStatusID` int(11) DEFAULT 1,
  `TierID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Bookings`
--

INSERT INTO `Bookings` (`BookingID`, `UserID`, `EventID`, `BookingTime`, `TotalPrice`, `BookingStatusID`, `TierID`) VALUES
(219, 1, 5, '2024-04-09 12:59:01', 15.00, 4, 4),
(220, 1, 5, '2024-04-09 12:59:27', 15.00, 4, 4),
(222, 1, 5, '2024-04-09 13:22:49', 15.00, 4, 4),
(223, 1, 5, '2024-04-09 15:02:13', 15.00, 4, 4),
(224, 1, 5, '2024-04-09 15:14:16', 15.00, 4, 4),
(225, 1, 5, '2024-04-09 15:25:36', 15.00, 4, 4),
(226, 1, 5, '2024-04-09 15:40:29', 15.00, 4, 4),
(227, 1, 3, '2024-04-09 15:48:48', 25.00, 4, 6),
(228, 1, 3, '2024-04-09 16:04:49', 10.00, 4, 8),
(229, 1, 3, '2024-04-09 16:05:32', 20.00, 4, 5),
(230, 1, 3, '2024-04-09 16:09:51', 25.00, 4, 6),
(231, 1, 3, '2024-04-09 16:12:15', 10.00, 4, 8),
(232, 1, 3, '2024-04-09 17:06:15', 25.00, 4, 6),
(233, 1, 5, '2024-04-10 01:54:17', 15.00, 4, 4),
(234, 1, 8, '2024-04-10 23:27:41', 50.00, 4, 21),
(235, 1, 3, '2024-04-11 00:14:01', 20.00, 4, 5),
(236, 15, 5, '2024-04-11 02:22:31', 15.00, 4, 4),
(237, 15, 5, '2024-04-11 02:22:56', 15.00, 4, 4),
(238, 15, 11, '2024-04-11 02:30:27', 400.00, 4, 22),
(239, 15, 5, '2024-04-11 04:14:46', 15.00, 4, 4),
(240, 15, 5, '2024-04-11 05:23:45', 15.00, 4, 4);

-- --------------------------------------------------------

--
-- Table structure for table `BookingStatus`
--

CREATE TABLE `BookingStatus` (
  `BookingStatusID` int(11) NOT NULL,
  `StatusName` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `BookingStatus`
--

INSERT INTO `BookingStatus` (`BookingStatusID`, `StatusName`) VALUES
(3, 'Cancelled'),
(2, 'Completed'),
(4, 'Confirmed'),
(1, 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `Countries`
--

CREATE TABLE `Countries` (
  `CountryID` int(11) NOT NULL,
  `CountryName` varchar(255) NOT NULL,
  `ISOAlpha2` char(2) DEFAULT NULL,
  `ISOAlpha3` char(3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Countries`
--

INSERT INTO `Countries` (`CountryID`, `CountryName`, `ISOAlpha2`, `ISOAlpha3`) VALUES
(1, 'Algeria', NULL, NULL),
(2, 'Angola', NULL, NULL),
(3, 'Benin', NULL, NULL),
(4, 'Botswana', NULL, NULL),
(5, 'Burkina Faso', NULL, NULL),
(6, 'Burundi', NULL, NULL),
(7, 'Cabo Verde', NULL, NULL),
(8, 'Cameroon', NULL, NULL),
(9, 'Central African Republic', NULL, NULL),
(10, 'Chad', NULL, NULL),
(11, 'China', NULL, NULL),
(12, 'Comoros', NULL, NULL),
(13, 'Congo (Brazzaville)', NULL, NULL),
(14, 'Congo (Kinshasa)', NULL, NULL),
(15, 'Djibouti', NULL, NULL),
(16, 'Egypt', NULL, NULL),
(17, 'Equatorial Guinea', NULL, NULL),
(18, 'Eritrea', NULL, NULL),
(19, 'Eswatini', NULL, NULL),
(20, 'Ethiopia', NULL, NULL),
(21, 'Gabon', NULL, NULL),
(22, 'Gambia', NULL, NULL),
(23, 'Ghana', NULL, NULL),
(24, 'Guinea', NULL, NULL),
(25, 'Guinea-Bissau', NULL, NULL),
(26, 'Ivory Coast', NULL, NULL),
(27, 'Kenya', NULL, NULL),
(28, 'Lesotho', NULL, NULL),
(29, 'Liberia', NULL, NULL),
(30, 'Libya', NULL, NULL),
(31, 'Madagascar', NULL, NULL),
(32, 'Malawi', NULL, NULL),
(33, 'Mali', NULL, NULL),
(34, 'Mauritania', NULL, NULL),
(35, 'Mauritius', NULL, NULL),
(36, 'Morocco', NULL, NULL),
(37, 'Mozambique', NULL, NULL),
(38, 'Namibia', NULL, NULL),
(39, 'Niger', NULL, NULL),
(40, 'Nigeria', NULL, NULL),
(41, 'Rwanda', NULL, NULL),
(42, 'Sao Tome and Principe', NULL, NULL),
(43, 'Senegal', NULL, NULL),
(44, 'Seychelles', NULL, NULL),
(45, 'Sierra Leone', NULL, NULL),
(46, 'Somalia', NULL, NULL),
(47, 'South Africa', NULL, NULL),
(48, 'South Sudan', NULL, NULL),
(49, 'Sudan', NULL, NULL),
(50, 'Tanzania', NULL, NULL),
(51, 'Togo', NULL, NULL),
(52, 'Tunisia', NULL, NULL),
(53, 'Uganda', NULL, NULL),
(54, 'United Kingdom', NULL, NULL),
(55, 'United States', NULL, NULL),
(56, 'Zambia', NULL, NULL),
(57, 'Zimbabwe', NULL, NULL),
(58, 'India', NULL, NULL),
(59, 'Brazil', NULL, NULL),
(60, 'Russia', NULL, NULL),
(61, 'Germany', NULL, NULL),
(62, 'France', NULL, NULL),
(63, 'Japan', NULL, NULL),
(64, 'Canada', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `EventCategories`
--

CREATE TABLE `EventCategories` (
  `CategoryID` int(11) NOT NULL,
  `CategoryName` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `EventCategories`
--

INSERT INTO `EventCategories` (`CategoryID`, `CategoryName`) VALUES
(1, 'Cinema/Theatre'),
(2, 'Live Performances/Festivals'),
(3, 'Private Events');

-- --------------------------------------------------------

--
-- Table structure for table `EventInvitations`
--

CREATE TABLE `EventInvitations` (
  `InvitationID` int(11) NOT NULL,
  `EventID` int(11) DEFAULT NULL,
  `UserID` int(11) DEFAULT NULL,
  `SentDate` datetime NOT NULL,
  `RSVPStatusID` int(11) DEFAULT NULL,
  `RSVPDate` datetime DEFAULT NULL,
  `GuestCount` int(11) DEFAULT NULL,
  `Comments` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Events`
--

CREATE TABLE `Events` (
  `EventID` int(11) NOT NULL,
  `OrganizerID` int(11) DEFAULT NULL,
  `CategoryID` int(11) DEFAULT NULL,
  `Title` varchar(255) NOT NULL,
  `Description` text DEFAULT NULL,
  `StartTime` datetime NOT NULL,
  `EndTime` datetime NOT NULL,
  `VenueID` int(11) DEFAULT NULL,
  `IsPrivate` tinyint(1) DEFAULT 0,
  `EventStatusID` int(11) DEFAULT 1,
  `CreationDate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Events`
--

INSERT INTO `Events` (`EventID`, `OrganizerID`, `CategoryID`, `Title`, `Description`, `StartTime`, `EndTime`, `VenueID`, `IsPrivate`, `EventStatusID`, `CreationDate`) VALUES
(3, 1, 2, 'PARTY ON THE BEACHH', 'SUMMER VIBESSsss', '2024-06-30 16:30:00', '2024-06-30 05:00:00', 7, 0, 1, '2024-04-06 11:28:09'),
(4, 1, 2, 'Gaming Night', 'Gaming with friendsS', '2024-04-14 12:20:00', '2024-04-14 12:20:00', 8, 0, 1, '2024-04-06 12:22:01'),
(5, 2, 2, 'MR EAZI CONCERT', 'MR EAZI IS COMING TO. GHANA THIS DECEMBER, IT\\\'S GOING TO BE CRAZYYYY', '2024-12-21 17:45:00', '2024-12-21 03:45:00', 9, 0, 1, '2024-04-06 13:47:37'),
(7, 1, 2, 'Father&Daughter SHOW', 'Father & Daughters Live Karaoke', '2024-04-26 01:43:00', '2024-04-20 01:43:00', 11, 0, 1, '2024-04-07 01:44:50'),
(8, 1, 2, 'Burna Boy Concert', 'Burna Boy First Concert In Togo', '2024-04-26 01:43:00', '2024-04-20 01:43:00', 12, 0, 1, '2024-04-07 01:48:36'),
(9, 1, 1, 'SpiderMan HomeComing', 'FIRST TIME SCREENING IN DOUALA', '2024-04-20 03:46:00', '2024-04-07 03:46:00', 13, 0, 1, '2024-04-07 03:48:23'),
(10, 1, 3, 'PreWedding Dinner', 'Leslie&Fred Pre Wedding Dinner', '2024-04-28 04:25:00', '2024-04-26 04:25:00', 14, 1, 1, '2024-04-07 04:26:09'),
(11, 15, 1, 'Love at first side', 'they fell in love', '2024-04-12 00:26:00', '2024-04-13 00:26:00', 15, 0, 1, '2024-04-11 00:28:38');

-- --------------------------------------------------------

--
-- Table structure for table `EventStatus`
--

CREATE TABLE `EventStatus` (
  `EventStatusID` int(11) NOT NULL,
  `StatusName` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `EventStatus`
--

INSERT INTO `EventStatus` (`EventStatusID`, `StatusName`) VALUES
(1, 'Active'),
(2, 'Cancelled');

-- --------------------------------------------------------

--
-- Table structure for table `Images`
--

CREATE TABLE `Images` (
  `ImageID` int(11) NOT NULL,
  `EventID` int(11) DEFAULT NULL,
  `ImagePath` varchar(255) NOT NULL,
  `ImageType` varchar(50) NOT NULL,
  `UploadDate` datetime NOT NULL,
  `Description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Images`
--

INSERT INTO `Images` (`ImageID`, `EventID`, `ImagePath`, `ImageType`, `UploadDate`, `Description`) VALUES
(2, 3, '../uploads/Setting-a-budget_1024x1024.jpg.webp', 'webp', '2024-04-06 11:28:09', NULL),
(3, 4, '../uploads/DALL·E 2024-04-05 01.41.58 - Craft an inviting and heartwarming scene inside a rustic café where a group of five diverse adults, including Black men and women, are gathered around.webp', 'webp', '2024-04-06 12:22:01', NULL),
(4, 5, '../uploads/MrEazi.jpeg', 'jpeg', '2024-04-06 13:47:37', NULL),
(6, 7, '../uploads/DALL·E 2024-04-05 01.42.18 - Envision a warm and engaging scene where a Black man and a Black girl are seated at a rustic wooden table in a cozy, dimly-lit room filled with event .webp', 'webp', '2024-04-07 01:44:50', NULL),
(7, 8, '../uploads/BURNA.avif', 'avif', '2024-04-07 01:48:36', NULL),
(8, 9, '../uploads/spiderman.jpeg', 'jpeg', '2024-04-07 03:48:23', NULL),
(9, 10, '../uploads/so-5ed906b866a4bd413a3d6efc-ph0.jpg', 'jpg', '2024-04-07 04:26:09', NULL),
(10, 11, '../uploads/LOVE.jpeg', 'jpeg', '2024-04-11 00:28:38', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `Notifications`
--

CREATE TABLE `Notifications` (
  `NotificationID` int(11) NOT NULL,
  `PaymentID` int(11) DEFAULT NULL,
  `Message` text NOT NULL,
  `IsRead` tinyint(1) DEFAULT 0,
  `CreatedAt` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Payments`
--

CREATE TABLE `Payments` (
  `PaymentID` int(11) NOT NULL,
  `BookingID` int(11) DEFAULT NULL,
  `Amount` decimal(10,2) NOT NULL,
  `PaymentMethod` varchar(50) NOT NULL,
  `PaymentStatusID` int(11) DEFAULT NULL,
  `PaymentDate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Payments`
--

INSERT INTO `Payments` (`PaymentID`, `BookingID`, `Amount`, `PaymentMethod`, `PaymentStatusID`, `PaymentDate`) VALUES
(98, 219, 15.00, 'Online', 2, '2024-04-09 10:59:36'),
(99, 220, 15.00, 'Online', 2, '2024-04-09 10:59:36'),
(100, 222, 15.00, 'Online', 2, '2024-04-09 11:23:05'),
(101, 223, 15.00, 'Online', 2, '2024-04-09 13:02:31'),
(102, 224, 15.00, 'Online', 2, '2024-04-09 13:14:39'),
(103, 225, 15.00, 'Online', 2, '2024-04-09 13:25:53'),
(104, 226, 15.00, 'Online', 2, '2024-04-09 13:40:47'),
(105, 227, 25.00, 'Online', 2, '2024-04-09 13:49:04'),
(106, 228, 10.00, 'Online', 2, '2024-04-09 14:04:53'),
(107, 229, 20.00, 'Online', 2, '2024-04-09 14:05:36'),
(108, 230, 25.00, 'Online', 2, '2024-04-09 14:10:21'),
(109, 231, 10.00, 'Online', 2, '2024-04-09 14:12:18'),
(110, 232, 25.00, 'Online', 2, '2024-04-09 15:06:40'),
(111, 233, 15.00, 'Online', 2, '2024-04-09 23:54:35'),
(112, 234, 50.00, 'Online', 2, '2024-04-10 21:27:45'),
(113, 235, 20.00, 'Online', 2, '2024-04-10 22:14:14'),
(114, 236, 15.00, 'Online', 2, '2024-04-11 00:23:26'),
(115, 237, 15.00, 'Online', 2, '2024-04-11 00:23:26'),
(116, 238, 400.00, 'Online', 2, '2024-04-11 00:30:44'),
(117, 239, 15.00, 'Online', 2, '2024-04-11 02:15:01'),
(118, 240, 15.00, 'Online', 2, '2024-04-11 03:24:04');

-- --------------------------------------------------------

--
-- Table structure for table `PaymentStatus`
--

CREATE TABLE `PaymentStatus` (
  `PaymentStatusID` int(11) NOT NULL,
  `StatusName` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `PaymentStatus`
--

INSERT INTO `PaymentStatus` (`PaymentStatusID`, `StatusName`) VALUES
(2, 'Completed'),
(3, 'Failed'),
(1, 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `Roles`
--

CREATE TABLE `Roles` (
  `RoleID` int(11) NOT NULL,
  `RoleName` varchar(50) NOT NULL,
  `Description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `RSVPStatus`
--

CREATE TABLE `RSVPStatus` (
  `RSVPStatusID` int(11) NOT NULL,
  `StatusName` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `RSVPStatus`
--

INSERT INTO `RSVPStatus` (`RSVPStatusID`, `StatusName`) VALUES
(2, 'Accepted'),
(3, 'Declined'),
(1, 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `Seats`
--

CREATE TABLE `Seats` (
  `SeatID` int(11) NOT NULL,
  `TierID` int(11) DEFAULT NULL,
  `SeatNumber` varchar(10) DEFAULT NULL,
  `Row` varchar(10) DEFAULT NULL,
  `SeatStatusID` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `SeatStatus`
--

CREATE TABLE `SeatStatus` (
  `SeatStatusID` int(11) NOT NULL,
  `StatusName` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `SeatStatus`
--

INSERT INTO `SeatStatus` (`SeatStatusID`, `StatusName`) VALUES
(1, 'Available'),
(2, 'Reserved'),
(3, 'Sold');

-- --------------------------------------------------------

--
-- Table structure for table `Tickets`
--

CREATE TABLE `Tickets` (
  `TicketID` int(11) NOT NULL,
  `BookingID` int(11) DEFAULT NULL,
  `TierID` int(11) DEFAULT NULL,
  `TicketStatusID` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Tickets`
--

INSERT INTO `Tickets` (`TicketID`, `BookingID`, `TierID`, `TicketStatusID`) VALUES
(21, 219, 4, 1),
(22, 220, 4, 1),
(23, 222, 4, 1),
(24, 223, 4, 1),
(25, 224, 4, 1),
(26, 225, 4, 1),
(27, 226, 4, 1),
(28, 227, 6, 1),
(29, 228, 8, 1),
(30, 229, 5, 1),
(31, 230, 6, 1),
(32, 231, 8, 1),
(33, 232, 6, 1),
(34, 233, 4, 1),
(35, 234, 21, 1),
(36, 235, 5, 1),
(37, 236, 4, 1),
(38, 237, 4, 1),
(39, 238, 22, 1),
(40, 239, 4, 1),
(41, 240, 4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `TicketStatus`
--

CREATE TABLE `TicketStatus` (
  `TicketStatusID` int(11) NOT NULL,
  `StatusName` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `TicketStatus`
--

INSERT INTO `TicketStatus` (`TicketStatusID`, `StatusName`) VALUES
(3, 'Cancelled'),
(1, 'Issued'),
(2, 'Used');

-- --------------------------------------------------------

--
-- Table structure for table `TicketTiers`
--

CREATE TABLE `TicketTiers` (
  `TierID` int(11) NOT NULL,
  `EventID` int(11) DEFAULT NULL,
  `Name` varchar(255) NOT NULL,
  `Price` decimal(10,2) NOT NULL,
  `QuantityAvailable` int(11) NOT NULL,
  `IsActive` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `TicketTiers`
--

INSERT INTO `TicketTiers` (`TierID`, `EventID`, `Name`, `Price`, `QuantityAvailable`, `IsActive`) VALUES
(1, 3, 'VIP', 29.99, 55, 0),
(2, 3, 'GA(General Admission)', 9.00, 20, 1),
(3, 5, 'VIP', 50.00, 260, 0),
(4, 5, 'General Admission', 15.00, 350, 1),
(5, 3, 'Middle Admission', 20.00, 100, 0),
(6, 3, 'FAMILY SIZE', 25.00, 50, 1),
(7, 5, 'family section', 25.00, 160, 0),
(8, 3, 'down section', 10.00, 22, 1),
(9, 5, 'VIP Premium', 50.00, 260, 0),
(21, 8, 'vip', 50.00, 100, 1),
(22, 11, 'VIP', 400.00, 50, 1);

-- --------------------------------------------------------

--
-- Table structure for table `Users`
--

CREATE TABLE `Users` (
  `UserID` int(11) NOT NULL,
  `FirstName` varchar(255) DEFAULT NULL,
  `LastName` varchar(255) DEFAULT NULL,
  `Email` varchar(255) NOT NULL,
  `ContactNumber` varchar(50) DEFAULT NULL,
  `PasswordHash` varchar(255) NOT NULL,
  `IsSuperAdmin` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Users`
--

INSERT INTO `Users` (`UserID`, `FirstName`, `LastName`, `Email`, `ContactNumber`, `PasswordHash`, `IsSuperAdmin`) VALUES
(1, 'leslie', 'konlack', 'konlacknaomi@gmail.com', '+233555323913', '$2y$10$eGuCvq6L5xErbeiPOcm4bO8IDX0SHVZc/z5cV5i6CIMMBdBeR86P6', 0),
(2, 'Anne', 'Bukari', 'anne@gmail.com', '+23344599858', '$2y$10$1dCrwg9UuFOetCzUySVDueIzhlBCWzEeeOezNwXkSXOpl/3SNwaUi', 0),
(4, 'Super', 'Admin', 'leslie.konlack@ashesi.edu.gh', '+233555323913', '$2y$10$7k67Kz0zoJHiQwsKirHUGeyte4Ehi1rd./fuCCImrk/Cp.kytNijC', 1),
(5, 'Kyllian', 'Mbappe', 'keke@g', '131331', '$2y$10$RoQnv7uPl3MDlwuAxGHvt.zVCj8yaKMPInidMX/YXekvsbGlwQ902', 0),
(6, '0000', '0000', 'K@G', '00000', '$2y$10$5g8UAueOPSgWsPVlX7G6hOnWJCZtZQdehmgq3NmjnypTtcwiIHI5e', 0),
(7, 'kiki', 'mbappe', 'kiki@gmail.com', '', '$2y$10$PUpwXx1.wDZ8GzWxtyWi8OPGR5ccP2KZJnrF2cIfEOHxE1Ab/uOQ6', 0),
(8, 'hv', '222', '76@k', '', '$2y$10$1UQ9EIFlu04iCVIJHnUWL.EkliRt./biaHWzlyHd3Du/LM9VW7p7y', 0),
(12, 'k', 'l', 'k@gmail.com', '20433', '$2y$10$hsyDu9wOyc2dmTenRsq2euhNzOUbEBKzbD3QETahe8PohTZ6BBPdS', 0),
(13, 'd', 'd', 'd@gmail.com', 'dwwqq', '$2y$10$5UtOYMpAnhQvBeB4Vo4Zluj70AyIWx/syAc/wRX.D11xzH2onIhre', 0),
(14, 'lucie', 'konlack', 'lucie.konlack@ashesi.edu.gh', '432545', '$2y$10$PYyFVrsBGwA0SktzDneMYeFooqu3TkHOw2YuMYhPvGff8DmSoTP3a', 0),
(15, 'yaa', 'asumadu', 'yaa.asumadu@ashesi.edu.gh', '0246592859', '$2y$10$fHcJhgv6l/fqzZ0Y0ExqgeAcn9USFw3gWMpUdxhLLEFllmLejtIQy', 0);

-- --------------------------------------------------------

--
-- Table structure for table `Venues`
--

CREATE TABLE `Venues` (
  `VenueID` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `CountryID` int(11) DEFAULT NULL,
  `Location` text NOT NULL,
  `Capacity` int(11) DEFAULT NULL,
  `ContactInfo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Venues`
--

INSERT INTO `Venues` (`VenueID`, `Name`, `CountryID`, `Location`, `Capacity`, `ContactInfo`) VALUES
(7, 'LABADI BEATCH', 23, 'LABADI BEATCH AVENUE', 1000, '233555323912'),
(8, 'BLISS', 23, 'Bliss Center, Spintex Road', 50, '233555323913'),
(9, 'GHANA BLACK STAR', 23, 'Spintex Road', 2000, '233555323915'),
(10, 'Ashesi University', 23, '1 university Avenue', 50, '233555323913'),
(11, 'Canal Olympia', 8, 'Douala Beseingue', 50, '237697056181'),
(12, 'TogO Stadium', 51, 'Douala Beseingue', 1000, '237697056182'),
(13, 'Canal Olympia', 8, ' Canal Olympia Douala,Besengue', 50, '233555323913'),
(14, 'KING\\\'S HALL', 20, 'Ethiopia King\\\'s Hall', 40, '233555323917'),
(15, 'silver bird', 23, 'Accra Mall', 300, '246592859');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Bookings`
--
ALTER TABLE `Bookings`
  ADD PRIMARY KEY (`BookingID`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `EventID` (`EventID`),
  ADD KEY `BookingStatusID` (`BookingStatusID`),
  ADD KEY `fk_bookings_tiertiers` (`TierID`);

--
-- Indexes for table `BookingStatus`
--
ALTER TABLE `BookingStatus`
  ADD PRIMARY KEY (`BookingStatusID`),
  ADD UNIQUE KEY `StatusName` (`StatusName`);

--
-- Indexes for table `Countries`
--
ALTER TABLE `Countries`
  ADD PRIMARY KEY (`CountryID`),
  ADD UNIQUE KEY `CountryName` (`CountryName`);

--
-- Indexes for table `EventCategories`
--
ALTER TABLE `EventCategories`
  ADD PRIMARY KEY (`CategoryID`),
  ADD UNIQUE KEY `CategoryName` (`CategoryName`);

--
-- Indexes for table `EventInvitations`
--
ALTER TABLE `EventInvitations`
  ADD PRIMARY KEY (`InvitationID`),
  ADD KEY `EventID` (`EventID`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `RSVPStatusID` (`RSVPStatusID`);

--
-- Indexes for table `Events`
--
ALTER TABLE `Events`
  ADD PRIMARY KEY (`EventID`),
  ADD KEY `OrganizerID` (`OrganizerID`),
  ADD KEY `CategoryID` (`CategoryID`),
  ADD KEY `VenueID` (`VenueID`),
  ADD KEY `EventStatusID` (`EventStatusID`);

--
-- Indexes for table `EventStatus`
--
ALTER TABLE `EventStatus`
  ADD PRIMARY KEY (`EventStatusID`),
  ADD UNIQUE KEY `StatusName` (`StatusName`);

--
-- Indexes for table `Images`
--
ALTER TABLE `Images`
  ADD PRIMARY KEY (`ImageID`),
  ADD KEY `fk_event_id` (`EventID`);

--
-- Indexes for table `Notifications`
--
ALTER TABLE `Notifications`
  ADD PRIMARY KEY (`NotificationID`),
  ADD KEY `PaymentID` (`PaymentID`);

--
-- Indexes for table `Payments`
--
ALTER TABLE `Payments`
  ADD PRIMARY KEY (`PaymentID`),
  ADD KEY `BookingID` (`BookingID`),
  ADD KEY `PaymentStatusID` (`PaymentStatusID`);

--
-- Indexes for table `PaymentStatus`
--
ALTER TABLE `PaymentStatus`
  ADD PRIMARY KEY (`PaymentStatusID`),
  ADD UNIQUE KEY `StatusName` (`StatusName`);

--
-- Indexes for table `Roles`
--
ALTER TABLE `Roles`
  ADD PRIMARY KEY (`RoleID`),
  ADD UNIQUE KEY `RoleName` (`RoleName`);

--
-- Indexes for table `RSVPStatus`
--
ALTER TABLE `RSVPStatus`
  ADD PRIMARY KEY (`RSVPStatusID`),
  ADD UNIQUE KEY `StatusName` (`StatusName`);

--
-- Indexes for table `Seats`
--
ALTER TABLE `Seats`
  ADD PRIMARY KEY (`SeatID`),
  ADD KEY `TierID` (`TierID`),
  ADD KEY `SeatStatusID` (`SeatStatusID`);

--
-- Indexes for table `SeatStatus`
--
ALTER TABLE `SeatStatus`
  ADD PRIMARY KEY (`SeatStatusID`),
  ADD UNIQUE KEY `StatusName` (`StatusName`);

--
-- Indexes for table `Tickets`
--
ALTER TABLE `Tickets`
  ADD PRIMARY KEY (`TicketID`),
  ADD KEY `BookingID` (`BookingID`),
  ADD KEY `TierID` (`TierID`),
  ADD KEY `TicketStatusID` (`TicketStatusID`);

--
-- Indexes for table `TicketStatus`
--
ALTER TABLE `TicketStatus`
  ADD PRIMARY KEY (`TicketStatusID`),
  ADD UNIQUE KEY `StatusName` (`StatusName`);

--
-- Indexes for table `TicketTiers`
--
ALTER TABLE `TicketTiers`
  ADD PRIMARY KEY (`TierID`),
  ADD KEY `EventID` (`EventID`);

--
-- Indexes for table `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indexes for table `Venues`
--
ALTER TABLE `Venues`
  ADD PRIMARY KEY (`VenueID`),
  ADD KEY `CountryID` (`CountryID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Bookings`
--
ALTER TABLE `Bookings`
  MODIFY `BookingID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=241;

--
-- AUTO_INCREMENT for table `BookingStatus`
--
ALTER TABLE `BookingStatus`
  MODIFY `BookingStatusID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `Countries`
--
ALTER TABLE `Countries`
  MODIFY `CountryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `EventCategories`
--
ALTER TABLE `EventCategories`
  MODIFY `CategoryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `EventInvitations`
--
ALTER TABLE `EventInvitations`
  MODIFY `InvitationID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Events`
--
ALTER TABLE `Events`
  MODIFY `EventID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `EventStatus`
--
ALTER TABLE `EventStatus`
  MODIFY `EventStatusID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `Images`
--
ALTER TABLE `Images`
  MODIFY `ImageID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `Notifications`
--
ALTER TABLE `Notifications`
  MODIFY `NotificationID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Payments`
--
ALTER TABLE `Payments`
  MODIFY `PaymentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=119;

--
-- AUTO_INCREMENT for table `PaymentStatus`
--
ALTER TABLE `PaymentStatus`
  MODIFY `PaymentStatusID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `Roles`
--
ALTER TABLE `Roles`
  MODIFY `RoleID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `RSVPStatus`
--
ALTER TABLE `RSVPStatus`
  MODIFY `RSVPStatusID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `Seats`
--
ALTER TABLE `Seats`
  MODIFY `SeatID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=676;

--
-- AUTO_INCREMENT for table `SeatStatus`
--
ALTER TABLE `SeatStatus`
  MODIFY `SeatStatusID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `Tickets`
--
ALTER TABLE `Tickets`
  MODIFY `TicketID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `TicketStatus`
--
ALTER TABLE `TicketStatus`
  MODIFY `TicketStatusID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `TicketTiers`
--
ALTER TABLE `TicketTiers`
  MODIFY `TierID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `Users`
--
ALTER TABLE `Users`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `Venues`
--
ALTER TABLE `Venues`
  MODIFY `VenueID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Bookings`
--
ALTER TABLE `Bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `Users` (`UserID`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`EventID`) REFERENCES `Events` (`EventID`),
  ADD CONSTRAINT `bookings_ibfk_3` FOREIGN KEY (`BookingStatusID`) REFERENCES `BookingStatus` (`BookingStatusID`),
  ADD CONSTRAINT `fk_bookings_tiertiers` FOREIGN KEY (`TierID`) REFERENCES `TicketTiers` (`TierID`);

--
-- Constraints for table `EventInvitations`
--
ALTER TABLE `EventInvitations`
  ADD CONSTRAINT `eventinvitations_ibfk_1` FOREIGN KEY (`EventID`) REFERENCES `Events` (`EventID`),
  ADD CONSTRAINT `eventinvitations_ibfk_2` FOREIGN KEY (`UserID`) REFERENCES `Users` (`UserID`),
  ADD CONSTRAINT `eventinvitations_ibfk_3` FOREIGN KEY (`RSVPStatusID`) REFERENCES `RSVPStatus` (`RSVPStatusID`);

--
-- Constraints for table `Events`
--
ALTER TABLE `Events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`OrganizerID`) REFERENCES `Users` (`UserID`),
  ADD CONSTRAINT `events_ibfk_2` FOREIGN KEY (`CategoryID`) REFERENCES `EventCategories` (`CategoryID`),
  ADD CONSTRAINT `events_ibfk_3` FOREIGN KEY (`VenueID`) REFERENCES `Venues` (`VenueID`),
  ADD CONSTRAINT `events_ibfk_4` FOREIGN KEY (`EventStatusID`) REFERENCES `EventStatus` (`EventStatusID`);

--
-- Constraints for table `Images`
--
ALTER TABLE `Images`
  ADD CONSTRAINT `fk_event_id` FOREIGN KEY (`EventID`) REFERENCES `Events` (`EventID`) ON DELETE CASCADE,
  ADD CONSTRAINT `images_ibfk_1` FOREIGN KEY (`EventID`) REFERENCES `Events` (`EventID`);

--
-- Constraints for table `Notifications`
--
ALTER TABLE `Notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`PaymentID`) REFERENCES `Payments` (`PaymentID`);

--
-- Constraints for table `Payments`
--
ALTER TABLE `Payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`BookingID`) REFERENCES `Bookings` (`BookingID`),
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`PaymentStatusID`) REFERENCES `PaymentStatus` (`PaymentStatusID`);

--
-- Constraints for table `Seats`
--
ALTER TABLE `Seats`
  ADD CONSTRAINT `seats_ibfk_1` FOREIGN KEY (`TierID`) REFERENCES `TicketTiers` (`TierID`),
  ADD CONSTRAINT `seats_ibfk_2` FOREIGN KEY (`SeatStatusID`) REFERENCES `SeatStatus` (`SeatStatusID`);

--
-- Constraints for table `Tickets`
--
ALTER TABLE `Tickets`
  ADD CONSTRAINT `tickets_ibfk_3` FOREIGN KEY (`TierID`) REFERENCES `TicketTiers` (`TierID`),
  ADD CONSTRAINT `tickets_ibfk_4` FOREIGN KEY (`TicketStatusID`) REFERENCES `TicketStatus` (`TicketStatusID`);

--
-- Constraints for table `TicketTiers`
--
ALTER TABLE `TicketTiers`
  ADD CONSTRAINT `tickettiers_ibfk_1` FOREIGN KEY (`EventID`) REFERENCES `Events` (`EventID`);

--
-- Constraints for table `Venues`
--
ALTER TABLE `Venues`
  ADD CONSTRAINT `venues_ibfk_1` FOREIGN KEY (`CountryID`) REFERENCES `Countries` (`CountryID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
