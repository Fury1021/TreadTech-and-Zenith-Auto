-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 10, 2024 at 07:41 PM
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
-- Database: `retailerapplication`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `role` enum('admin','customer') NOT NULL DEFAULT 'admin',
  `profilepicture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `firstname`, `lastname`, `email`, `password`, `address`, `contact_number`, `role`, `profilepicture`) VALUES
(11, 'Patrick', 'Balaga', 'patrickbalaga@gmail.com', '$2y$10$uopANJYpEn2N3E7cY91TR.5LczoK8qxDAWO8xUrmGaJ3XYu2CPgq6', 'Baclaran', '09216210618', 'admin', 'patrick.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `tire_id` int(11) NOT NULL,
  `amount` int(11) DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_email`, `total_price`, `created_at`) VALUES
(16, 'patrickbalaga@gmail.com', 111.00, '2024-07-10 11:16:10');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tires`
--

CREATE TABLE `tires` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `item_name` text NOT NULL,
  `type` varchar(255) NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `description` text DEFAULT NULL,
  `brand` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `amount` int(11) DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tires`
--

INSERT INTO `tires` (`id`, `item_name`, `type`, `price`, `description`, `brand`, `image`, `created_at`, `updated_at`, `amount`) VALUES
(29, 'sample', 'PCT', 111.00, 'sampler', 'Michelin', '../retailer/storage/OIP.jpeg', NULL, NULL, 12),
(22, 'sample', 'PCT', 112.00, 'adwdasd', 'Michelin', 'storage/taguro.jpg', NULL, NULL, 11),
(14, 'Sand Tires', 'ORAT', 210.00, 'Designedd for driving on sandy terrain, featuring wide treads to prevent sinking.', 'Tirebuyer', 'storage/sandtires.png', NULL, NULL, 30),
(13, 'Rock Crawling Tires', 'ORAT', 280.00, 'Engineered for extreme off-road conditions and very deep treads.', 'Tirebuyer', 'storage/rockcrawlingtires.png', NULL, NULL, 30),
(12, 'Commercial Van Tires', 'CHDT', 300.00, 'Designed for heavy loads and frequent stops.', 'Deestone', 'storage/commercialvantires.png', NULL, NULL, 30),
(11, 'Light Truck Tires', 'CHDT', 300.00, 'Built for durability and load-carrying capacity', 'GT Radial', 'storage/lighttrucktires.png', NULL, NULL, 30),
(10, 'Eco-Friendly Tires', 'ST', 255.00, 'Made from environmetnally friendly materials and designed for fuel efficiency', 'Maxxis', 'storage/ecofriendlytires.png', NULL, NULL, 30),
(9, 'Run-Flat Tires', 'ST', 140.00, 'Can be driven for a limited distance after a puncture', 'Toyo Tires', 'storage/runflattires.png', NULL, NULL, 30),
(7, 'Ultra-High Performance (UHP) Tires', 'PT', 250.00, 'Provide enhanced grip and handling for sports cars.', 'Yokohama', 'storage/ultrahighperformancetires.png', NULL, NULL, 30),
(5, 'Mud-Terain Tires', 'TSUVT', 210.00, 'Optimized for muddy and off-road conditions with aggressive tread patterns.', 'Goodyear', 'storage/mudterraintires.png', NULL, NULL, 30),
(8, 'Performance Summer Tires', 'PT', 220.00, 'Offer maximum grip and performance in warm weather.', 'Michelin', 'storage/performancesummertires.png', NULL, NULL, 30),
(6, 'Highway Tires', 'TSUVT', 170.00, 'Designed for smooth and quiet rides on highways.', 'Kapsen', 'storage/highwaytires.jpg', NULL, NULL, 30),
(4, 'All-Terain Tires', 'TSUVT', 230.00, 'Suitable for both on-road and off-road driving.', 'Dunlop', 'storage/allterraintires.png', NULL, NULL, 30),
(3, 'Winter Tires', 'PCT', 200.00, 'Made for snowy and icy conditions, with deeper treads and softer rubber.', 'Michelin', 'storage/wintertires.jpg', NULL, NULL, 30),
(1, 'All-Season Tires', 'PCT', 100.00, 'Versatile tires suitable for most weather conditions.', 'Arivo', 'storage/allseasontires.png', NULL, NULL, 12),
(2, 'Summer Tires', 'PCT', 130.00, 'Designed for warm weather, offering superior handling and performance.', 'CST', 'storage/SummerTires.png', NULL, NULL, 26);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_email` (`user_email`),
  ADD KEY `tire_id` (`tire_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_email` (`user_email`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tires`
--
ALTER TABLE `tires`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tires`
--
ALTER TABLE `tires`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
