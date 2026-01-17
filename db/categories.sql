-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 17, 2026 at 04:40 PM
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
-- Database: `mul_ven_ec_68`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `parent_id`, `image`, `sort_order`, `status`) VALUES
(1, 'Electronics', 'electronics', 'All electronics products', NULL, 'assets/uploads/categories/1768221341_TTHQHxsd8JQ.jpg', 0, 'active'),
(2, 'Phone', 'phone', 'All phone products', 1, 'assets/uploads/categories/1768221420_ked202208100033.1076x.0.jpg', 0, 'active'),
(3, 'Automobiles', 'automobiles', 'All automobiles', NULL, 'assets/uploads/categories/1768215320_2020-mitsubishi-pajero-sport-debuts-india-launch-price-14-1200x900.jpg', 0, 'active'),
(4, 'Car', 'car', 'all cars', 3, 'assets/uploads/categories/1768215388_Hyundai-Tucson-Limited-2021.jpg', 0, 'active'),
(5, 'Sedan', 'sedan', 'all sedan cars', 4, 'assets/uploads/categories/1768215473_tucson-suv-find-a-car-thumbnail-pc.webp', 0, 'active'),
(6, 'Bike', 'bike', 'All bikes', 3, 'assets/uploads/categories/1768216556_bike.jpg', 0, 'active'),
(7, 'Laptop', 'laptop', 'all notebooks and laptops', 1, 'assets/uploads/categories/1768216686_63ae7a4b43c8c.png', 0, 'active'),
(10, 'car', 'car2', 'dsfdsaf', 4, 'assets/uploads/categories/1768217344_0uWbJuWM3Gw.jpg', 0, 'active'),
(11, 'sdf', 'sadf', 'sdaf', 1, 'assets/uploads/categories/1768220758_EJPqDUoJDno.jpg', 0, 'active'),
(12, 'TV', 'tv', 'asdfdsf', 1, 'assets/uploads/categories/1768220857_jBzL8MvRTwY.jpg', 0, 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `parent_id` (`parent_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
