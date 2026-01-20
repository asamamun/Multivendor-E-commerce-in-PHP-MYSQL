-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 20, 2026 at 06:56 PM
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
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`id`, `name`, `slug`, `image`, `status`) VALUES
(1, 'Walton', 'walton', 'assets/uploads/brands/1768821304_63aa9b4f9ce44.png', 'active'),
(2, 'Toyotaa', 'toyotaa', 'assets/uploads/brands/1768821339_S0005F3V.jpg', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(12, 'TV', 'tv', 'asdfdsf', 1, 'assets/uploads/categories/1768220857_jBzL8MvRTwY.jpg', 0, 'active'),
(13, 'Health & Beauty', 'health-beauty', 'adf asdfsd af', NULL, 'assets/uploads/categories/1768651783_0OFDTcE64kw.jpg', 0, 'active'),
(14, 'Cosmetics', 'cosmetics', 'dsfa sdaf', 13, 'assets/uploads/categories/1768651849_arabic-perfume-known-attar-essential-260nw-697544122.webp', 0, 'active'),
(15, 'Medicine', 'medicine', 'd fsadf d', 13, 'assets/uploads/categories/1768651885_63aaa72717a45.png', 0, 'active'),
(16, 'Sports', 'Sports', '', NULL, 'assets/uploads/categories/1768651898_51+3ImEa6OL.jpg', 0, 'active'),
(17, 'Flowers', 'Gift', 'All types of flowers related solution', 13, 'assets/uploads/categories/1768652806_download03 - Copy.jpg', 0, 'active'),
(18, 'Flowers bouquet', 'Agro', 'All kind of natural flowers bouquet are available here', 13, 'assets/uploads/categories/1768653025_598756555_1363875332150472_4750196602199956461_n.jpg', 0, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `couriers`
--

CREATE TABLE `couriers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `vehicle_type` enum('bike','car','van','truck') NOT NULL,
  `vehicle_number` varchar(50) DEFAULT NULL,
  `coverage_areas` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`coverage_areas`)),
  `status` enum('active','inactive','busy') DEFAULT 'active',
  `rating` decimal(3,2) DEFAULT 0.00,
  `total_deliveries` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `deliveries`
--

CREATE TABLE `deliveries` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `courier_id` int(11) DEFAULT NULL,
  `pickup_address` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`pickup_address`)),
  `delivery_address` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`delivery_address`)),
  `estimated_delivery` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `actual_pickup` timestamp NULL DEFAULT NULL,
  `actual_delivery` timestamp NULL DEFAULT NULL,
  `delivery_cost` decimal(8,2) NOT NULL,
  `status` enum('assigned','picked_up','in_transit','delivered','failed','returned') DEFAULT 'assigned',
  `tracking_number` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('order','payment','delivery','review','system') NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data`)),
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_number` varchar(50) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `shipping_cost` decimal(10,2) DEFAULT 0.00,
  `tax_amount` decimal(10,2) DEFAULT 0.00,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL,
  `currency` varchar(3) DEFAULT 'BDT',
  `payment_method` enum('bkash','nagad','cod','rocket') NOT NULL,
  `payment_status` enum('pending','paid','failed','refunded') DEFAULT 'pending',
  `order_status` enum('pending','confirmed','processing','shipped','delivered','cancelled','returned') DEFAULT 'pending',
  `shipping_address` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`shipping_address`)),
  `billing_address` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`billing_address`)),
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_number`, `customer_id`, `subtotal`, `shipping_cost`, `tax_amount`, `discount_amount`, `total_amount`, `currency`, `payment_method`, `payment_status`, `order_status`, `shipping_address`, `billing_address`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'ORD-696E2C5D86EEB', 32, 26872201.00, 60.00, 0.00, 0.00, 26872261.00, 'BDT', 'bkash', 'paid', 'processing', '{\"name\":\"mamun mamun\",\"phone\":\"0181234567\",\"address\":\"sdfdsf\",\"city\":\"Dhaka\",\"zip\":\"1216\"}', '{\"name\":\"mamun mamun\",\"phone\":\"0181234567\",\"address\":\"sdfdsf\",\"city\":\"Dhaka\",\"zip\":\"1216\"}', 'bokale asben', '2026-01-19 13:06:37', '2026-01-20 12:38:38'),
(2, 'ORD-696E2CCE0D6FB', 32, 46279201.00, 60.00, 0.00, 0.00, 46279261.00, 'BDT', 'bkash', 'paid', 'processing', '{\"name\":\"mamun mamun\",\"phone\":\"234534545\",\"address\":\"sadfdsf\",\"city\":\"Dhaka\",\"zip\":\"1216\"}', '{\"name\":\"mamun mamun\",\"phone\":\"234534545\",\"address\":\"sadfdsf\",\"city\":\"Dhaka\",\"zip\":\"1216\"}', '', '2026-01-19 13:08:30', '2026-01-19 13:09:50'),
(3, 'ORD-696F4F2B81D8D', 32, 2605850.00, 60.00, 0.00, 0.00, 2605910.00, 'BDT', 'bkash', 'pending', 'pending', '{\"name\":\"mamun mamun\",\"phone\":\"018111234565\",\"address\":\"dsf sdf\",\"city\":\"Dhaka\",\"zip\":\"1216\"}', '{\"name\":\"mamun mamun\",\"phone\":\"018111234565\",\"address\":\"dsf sdf\",\"city\":\"Dhaka\",\"zip\":\"1216\"}', 'asdfd d saf', '2026-01-20 09:47:23', '2026-01-20 09:47:23'),
(4, 'ORD-696F5D87CCC59', 32, 92581.00, 60.00, 0.00, 0.00, 92641.00, 'BDT', 'cod', 'paid', 'delivered', '{\"name\":\"mamun mamun\",\"phone\":\"23432434\",\"address\":\"asdf dsfsadf sdf\",\"city\":\"Dhaka\",\"zip\":\"1216\"}', '{\"name\":\"mamun mamun\",\"phone\":\"23432434\",\"address\":\"asdf dsfsadf sdf\",\"city\":\"Dhaka\",\"zip\":\"1216\"}', 'saef sdaf', '2026-01-20 10:48:39', '2026-01-20 11:10:42'),
(5, 'ORD-696F62E3D217C', 38, 99999999.99, 60.00, 0.00, 0.00, 99999999.99, 'BDT', 'bkash', 'pending', 'pending', '{\"name\":\"ISDB Bishew\",\"phone\":\"01895431599\",\"address\":\"kazipara\",\"city\":\"dhaka\",\"zip\":\"1215\"}', '{\"name\":\"ISDB Bishew\",\"phone\":\"01895431599\",\"address\":\"kazipara\",\"city\":\"dhaka\",\"zip\":\"1215\"}', 'asdf', '2026-01-20 11:11:31', '2026-01-20 11:11:31'),
(6, 'ORD-696F641791FF4', 38, 175000.00, 60.00, 0.00, 0.00, 175060.00, 'BDT', 'bkash', 'paid', 'pending', '{\"name\":\"ISDB Bishew\",\"phone\":\"01895431599\",\"address\":\"kazipara\",\"city\":\"dhaka\",\"zip\":\"1215\"}', '{\"name\":\"ISDB Bishew\",\"phone\":\"01895431599\",\"address\":\"kazipara\",\"city\":\"dhaka\",\"zip\":\"1215\"}', 'skfjeiu', '2026-01-20 11:16:39', '2026-01-20 12:47:30'),
(7, 'ORD-696F64D07B406', 38, 210000.00, 60.00, 0.00, 0.00, 210060.00, 'BDT', 'bkash', 'pending', 'pending', '{\"name\":\"ISDB Bishew\",\"phone\":\"01895431599\",\"address\":\"kazipara\",\"city\":\"dhaka\",\"zip\":\"1215\"}', '{\"name\":\"ISDB Bishew\",\"phone\":\"01895431599\",\"address\":\"kazipara\",\"city\":\"dhaka\",\"zip\":\"1215\"}', 'gfc', '2026-01-20 11:19:44', '2026-01-20 11:19:44'),
(8, 'ORD-696F65E3203CB', 40, 70000.00, 60.00, 0.00, 0.00, 70060.00, 'BDT', 'bkash', 'failed', 'pending', '{\"name\":\"Asad Khan\",\"phone\":\"01895426733\",\"address\":\"kazipara\",\"city\":\"dhaka\",\"zip\":\"1215\"}', '{\"name\":\"Asad Khan\",\"phone\":\"01895426733\",\"address\":\"kazipara\",\"city\":\"dhaka\",\"zip\":\"1215\"}', 'its emergency for me', '2026-01-20 11:24:19', '2026-01-20 12:39:04'),
(9, 'ORD-696F775B243AB', 42, 8391.00, 60.00, 0.00, 0.00, 8451.00, 'BDT', 'cod', 'paid', 'pending', '{\"name\":\"Asad Khan\",\"phone\":\"0124285428\",\"address\":\"Barishal\",\"city\":\"barishal\",\"zip\":\"2250\"}', '{\"name\":\"Asad Khan\",\"phone\":\"0124285428\",\"address\":\"Barishal\",\"city\":\"barishal\",\"zip\":\"2250\"}', '', '2026-01-20 12:38:51', '2026-01-20 12:39:17'),
(10, 'ORD-696F77C7197D4', 30, 428997.00, 60.00, 0.00, 0.00, 429057.00, 'BDT', 'cod', 'failed', 'pending', '{\"name\":\"Mahady Hasan\",\"phone\":\"01775394527\",\"address\":\"Savar\",\"city\":\"Dhaka\",\"zip\":\"\"}', '{\"name\":\"Mahady Hasan\",\"phone\":\"01775394527\",\"address\":\"Savar\",\"city\":\"Dhaka\",\"zip\":\"\"}', '', '2026-01-20 12:40:39', '2026-01-20 12:42:17'),
(11, 'ORD-696F77E62E3FC', 43, 99999999.99, 60.00, 0.00, 0.00, 99999999.99, 'BDT', 'cod', 'failed', 'pending', '{\"name\":\"Shamim  Hassan\",\"phone\":\"01571717682\",\"address\":\"dhaka\",\"city\":\"mirpur\",\"zip\":\"\"}', '{\"name\":\"Shamim  Hassan\",\"phone\":\"01571717682\",\"address\":\"dhaka\",\"city\":\"mirpur\",\"zip\":\"\"}', '', '2026-01-20 12:41:10', '2026-01-20 12:41:36');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_sku` varchar(100) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `vendor_status` enum('pending','confirmed','processing','shipped','delivered','cancelled') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `vendor_id`, `product_name`, `product_sku`, `quantity`, `unit_price`, `total_price`, `vendor_status`) VALUES
(1, 1, 13, 8, 'CFMOTO 300SR', '121515', 1, 458500.00, 458500.00, 'pending'),
(2, 1, 47, 16, 'flower', '147', 1, 10201.00, 10201.00, 'pending'),
(3, 1, 90, 10, 'Toyota Corolla', '1232125', 2, 3800000.00, 7600000.00, 'pending'),
(4, 1, 91, 29, 'flower bouquet', '14785', 1, 3500.00, 3500.00, 'pending'),
(5, 1, 92, 10, 'Toyota Noah', '1221588', 2, 3500000.00, 7000000.00, 'pending'),
(6, 1, 95, 10, 'Honda Accord', '7845961', 2, 5900000.00, 11800000.00, 'pending'),
(7, 2, 13, 8, 'CFMOTO 300SR', '121515', 1, 458500.00, 458500.00, 'pending'),
(8, 2, 47, 16, 'flower', '147', 1, 10201.00, 10201.00, 'pending'),
(9, 2, 90, 10, 'Toyota Corolla', '1232125', 4, 3800000.00, 15200000.00, 'shipped'),
(10, 2, 91, 29, 'flower bouquet', '14785', 3, 3500.00, 10500.00, 'pending'),
(11, 2, 92, 10, 'Toyota Noah', '1221588', 2, 3500000.00, 7000000.00, 'shipped'),
(12, 2, 95, 10, 'Honda Accord', '7845961', 4, 5900000.00, 23600000.00, 'shipped'),
(13, 3, 67, 8, 'Sony XR-85X95L Bravia 85-Inch XR Series 4K Ultra HD Smart Google TV', '64645464', 4, 649900.00, 2599600.00, 'pending'),
(14, 3, 68, 11, 'Digital Scale 10kg', '14', 3, 300.00, 900.00, 'pending'),
(15, 3, 70, 9, 'Dot and key sunscreen', '111222', 3, 1200.00, 3600.00, 'pending'),
(16, 3, 88, 28, 'Vitamin B Complex', '12457541', 5, 350.00, 1750.00, 'pending'),
(17, 4, 1, 1, 'Walton Bike1', 'sadf345dsfg', 1, 90001.00, 90001.00, 'delivered'),
(18, 4, 102, 29, 'Fuler gohona', '3214', 1, 2580.00, 2580.00, 'pending'),
(19, 5, 90, 10, 'Toyota Corolla', '1232125', 500, 3800000.00, 99999999.99, 'pending'),
(20, 6, 88, 28, 'Vitamin B Complex', '12457541', 500, 350.00, 175000.00, 'pending'),
(21, 7, 18, 7, 'হেয়ার গ্রোথ সিরাম', '9512364', 420, 500.00, 210000.00, 'pending'),
(22, 8, 88, 28, 'Vitamin B Complex', '12457541', 200, 350.00, 70000.00, 'pending'),
(23, 9, 54, 7, 'Hair Building Fibers', '7852', 1, 1870.00, 1870.00, 'pending'),
(24, 9, 62, 7, 'Onion Oil', '7896', 1, 1670.00, 1670.00, 'pending'),
(25, 9, 65, 7, 'Biotin Supplements', '25874', 1, 4851.00, 4851.00, 'pending'),
(26, 10, 143, 39, 'Google pixel 10 pro xl', '64646464', 1, 154999.00, 154999.00, 'pending'),
(27, 10, 146, 39, 'Samsung Galaxy S25 Ultra', '541787', 1, 226999.00, 226999.00, 'pending'),
(28, 10, 148, 39, 'Honor X9d', '4654651', 1, 46999.00, 46999.00, 'pending'),
(29, 11, 114, 36, 'car', '4578', 4, 99999999.99, 99999999.99, 'pending'),
(30, 11, 132, 18, 'Kids Car', '4444444', 5, 555555.00, 2777775.00, 'pending'),
(31, 11, 141, 39, 'Sony Xperia 1 VI', '987445', 6, 30000.00, 180000.00, 'confirmed'),
(32, 11, 143, 39, 'Google pixel 10 pro xl', '64646464', 2, 154999.00, 309998.00, 'cancelled'),
(33, 11, 146, 39, 'Samsung Galaxy S25 Ultra', '541787', 4, 226999.00, 907996.00, 'cancelled'),
(34, 11, 148, 39, 'Honor X9d', '4654651', 6, 46999.00, 281994.00, 'shipped'),
(35, 11, 149, 39, 'Huawei Nova 15 ultra', '448464', 4, 80000.00, 320000.00, 'shipped');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `payment_method` enum('bkash','nagad','cod') NOT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `gateway_transaction_id` varchar(100) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(3) DEFAULT 'BDT',
  `status` enum('pending','processing','completed','failed','cancelled','refunded') DEFAULT 'pending',
  `gateway_response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`gateway_response`)),
  `processed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `order_id`, `payment_method`, `transaction_id`, `gateway_transaction_id`, `amount`, `currency`, `status`, `gateway_response`, `processed_at`, `created_at`) VALUES
(1, 1, 'bkash', 'edgfdgfdg5', NULL, 26872261.00, 'BDT', 'pending', NULL, NULL, '2026-01-19 13:06:37'),
(2, 2, 'bkash', '3245435dfsgsdfgfdg', NULL, 46279261.00, 'BDT', 'pending', NULL, NULL, '2026-01-19 13:08:30'),
(3, 3, 'bkash', '3454355656', NULL, 2605910.00, 'BDT', 'pending', NULL, NULL, '2026-01-20 09:47:23'),
(4, 4, 'cod', '', NULL, 92641.00, 'BDT', 'pending', NULL, NULL, '2026-01-20 10:48:39'),
(5, 5, 'bkash', '123698745', NULL, 99999999.99, 'BDT', 'pending', NULL, NULL, '2026-01-20 11:11:31'),
(6, 6, 'bkash', '121346579', NULL, 175060.00, 'BDT', 'pending', NULL, NULL, '2026-01-20 11:16:39'),
(7, 7, 'bkash', '1236547896', NULL, 210060.00, 'BDT', 'pending', NULL, NULL, '2026-01-20 11:19:44'),
(8, 8, 'bkash', '785412369', NULL, 70060.00, 'BDT', 'pending', NULL, NULL, '2026-01-20 11:24:19'),
(9, 9, 'cod', '', NULL, 8451.00, 'BDT', 'pending', NULL, NULL, '2026-01-20 12:38:51'),
(10, 10, 'cod', '', NULL, 429057.00, 'BDT', 'pending', NULL, NULL, '2026-01-20 12:40:39'),
(11, 11, 'cod', '', NULL, 99999999.99, 'BDT', 'pending', NULL, NULL, '2026-01-20 12:41:10');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `short_description` varchar(500) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `compare_price` decimal(10,2) DEFAULT NULL,
  `cost_price` decimal(10,2) DEFAULT NULL,
  `sku` varchar(100) DEFAULT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `min_stock_level` int(11) DEFAULT 5,
  `weight` decimal(8,2) DEFAULT NULL,
  `dimensions` varchar(100) DEFAULT NULL,
  `status` enum('active','inactive','draft') DEFAULT 'draft',
  `featured` tinyint(1) DEFAULT 0,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `rating` decimal(3,2) DEFAULT 0.00,
  `review_count` int(11) DEFAULT 0,
  `view_count` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `vendor_id`, `category_id`, `brand_id`, `name`, `slug`, `description`, `short_description`, `price`, `compare_price`, `cost_price`, `sku`, `stock_quantity`, `min_stock_level`, `weight`, `dimensions`, `status`, `featured`, `meta_title`, `meta_description`, `rating`, `review_count`, `view_count`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 6, NULL, 'Walton Bike1', 'walton-bike', '11 It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here, content here\', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for \'lorem ipsum\' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).', 'qwe', 90001.00, 99000.00, 70001.00, 'sadf345dsfg', 49, 5, 20.00, '5', 'active', 1, '0', '0', 0.00, 0, 0, '2026-01-17 11:40:28', '2026-01-20 12:20:33', NULL),
(13, 8, 6, NULL, 'CFMOTO 300SR', 'cfmoto-300sr', 'The CFMOTO 300SR is one of the fully-faired sportbikes exclusively featured and produced by CFMOTO, a renowned Chinese manufacturer of motorcycles and sport vehicles known as ZHEJIANG CFMOTO POWER CO., LTD. The CFMoto 300NK was introduced to the market with the intention of positioning itself between the 250SR and 450SR models, incorporating appealing features from these two motorcycles in the sport-oriented SR series.', '0', 458500.00, 0.00, 420000.00, '121515', 8, 1, 0.00, '0', 'active', 1, '0', '0', 0.00, 0, 0, '2026-01-17 12:03:37', '2026-01-20 12:35:50', '2026-01-20 12:35:50'),
(18, 7, 13, NULL, 'হেয়ার গ্রোথ সিরাম', '-', 'হেয়ার গ্রোথ সিরাম ও তেল উভয়ই চুলের বৃদ্ধিতে সাহায্য করে, তবে সিরাম হালকা টেক্সচারের এবং মূলত স্টাইলিং বা চিকিৎসার জন্য ব্যবহৃত হয়, যা চুলকে শাইন দেয়, বাড়ানো কমায় ও ঘনত্ব বাড়ায়, অন্যদিকে তেল স্ক্যাল্পে গভীর পুষ্টি জোগায়, যা চুলের গোড়া মজবুত করে; সিরাম সাধারণত পরিষ্কার ও শুকনো চুলে ব্যবহার করা হয়, আর তেল ম্যাসাজ করে লাগানো হয়, যা চুলের স্বাস্থ্য ও হাইড্রেশন উন্নত করে।', '0', 500.00, 800.00, 350.00, '9512364', -170, 5, 0.00, '0', 'active', 1, '0', '0', 0.00, 0, 0, '2026-01-17 12:14:24', '2026-01-20 11:19:44', NULL),
(28, 9, 13, NULL, 'Mamaearth rosemary Hair oil', 'mamaearth-rosemary-hair-oil', 'Mamaearth Rosemary Hair Growth Oil is a herbal hair oil formulated to promote hair growth, reduce hair fall, and nourish the scalp using plant-based ingredients like rosemary and methi (fenugreek). It’s made to be lightweight, non-toxic, and suitable for all hair types.', '0', 900.00, 1100.00, 700.00, '4444', 10, 10, 200.00, '0', 'active', 1, '0', '', 0.00, 0, 0, '2026-01-17 12:17:46', '2026-01-19 11:51:37', NULL),
(29, 8, 2, NULL, 'Sony Xperia 1 VI', 'sony-xperia-1-vi', 'It is a 6.5\" LTPO OLED display and 120Hz refresh rate. The phone is powered by the Qualcomm SM8650-AB Snapdragon 8 Gen 3 chipset with Adreno 750 GPU. The 5000mAh battery supports 30W fast charging. The Sony Xperia 1 VI has a triple rear camera, 48MP main sensor, 12MP ultra wide sensor, and 12MP telephoto sensor. For selfies, there\'s a single 12MP camera. The phone runs on Android 14. The Sony Xperia 1 VI price in Bangladesh is BDT 140,000 (Unofficial). It has 12GB of RAM and 256GB of internal storage. This model is officially unavailable in Bangladesh. It comes in Black, Platinum silver, Khaki green, and Scar red colors.', '0', 140000.00, 0.00, 130000.00, '985456', 20, 5, 0.00, '0', 'active', 1, '0', '', 0.00, 0, 0, '2026-01-17 12:17:56', '2026-01-20 12:35:10', '2026-01-20 12:35:10'),
(31, 11, 1, NULL, 'Tapo C200c Indoor Pan/Tilt Home Security Wi-Fi', 'tapo-c200c-indoor-pan-tilt-home-security-wi-fi', 'Tapo C200c Indoor Pan/Tilt Home Security Wi-Fi Camera, FHD, Surveillance Camera, Night Vision, 360° Viewing Angle, Two Way Audio, Motion Detection and Notifications, Integrated Acoustic and Clear Alarm', '0', 1799.00, 0.00, 850.00, '10', 10, 5, 0.00, '0', 'active', 0, '0', '0', 0.00, 0, 0, '2026-01-17 12:18:32', '2026-01-20 09:39:49', NULL),
(32, 10, 10, NULL, 'MG Cyberster', 'mg-cyberster', 'The MG Cyberster is a new all-electric two-seater roadster that revives MG\'s classic sports car heritage with modern tech, featuring scissor doors, a sporty design, and impressive electric performance, hitting 0-100 km/h in about 3.2 seconds with dual motors and a 77kWh battery offering decent range, all packed with digital cockpit screens and luxury features like Bose audio and Alcantara seats, positioning itself as a stylish and powerful EV statemen.', '0', 5500000.00, 6000000.00, 4500000.00, '2154584757', 5000000, 1, 1885.00, '0', 'active', 0, '0', '0', 0.00, 0, 0, '2026-01-17 12:18:35', '2026-01-20 12:20:33', NULL),
(44, 8, 4, NULL, 'Range Rover Sport HST', 'range-rover-sport-hst', 'Land Rover Range Rover Sport HST is a new by Land Rover, the price of Range Rover Sport HST in Bangladesh is BDT 10,415,121, on this page you can find the best and most updated price of Range Rover Sport HST in Bangladesh with detailed specifications and features.', '0', 10415121.00, 0.00, 0.00, '8465464', 5, 1, 0.00, '0', 'active', 1, '0', '', 0.00, 0, 0, '2026-01-17 12:23:13', '2026-01-20 12:35:01', '2026-01-20 12:35:01'),
(47, 16, 17, NULL, 'flower', 'flower', 'wholesale flower', '0', 10201.00, 11000.00, 2145.00, '147', 48, 5, 5.00, '10', 'active', 1, '0', '0', 0.00, 0, 0, '2026-01-17 12:24:20', '2026-01-20 09:39:49', NULL),
(48, 9, 14, NULL, 'Dot and key cocoa nude lipbalm', 'dot-and-key-cocoa-nude-lipbalm', 'Mamaearth Rosemary Hair Growth Oil is a herbal hair oil formulated to promote hair growth, reduce hair fall, and nourish the scalp using plant-based ingredients like rosemary and methi (fenugreek). It’s made to be lightweight, non-toxic, and suitable for all hair types.', '0', 500.00, 700.00, 400.00, '3333', 10, 10, 30.00, '0', 'active', 0, '0', '', 0.00, 0, 0, '2026-01-17 12:24:36', '2026-01-20 09:39:49', NULL),
(50, 11, 1, NULL, 'AirPods Pro 2nd Generation', 'airpods-pro-2nd-generation', '', '0', 399.00, 0.00, 0.00, '11', 10, 5, 0.00, '0', 'active', 0, '0', '0', 0.00, 0, 0, '2026-01-17 12:25:06', '2026-01-20 09:39:49', NULL),
(51, 10, 3, NULL, 'MG 5 GT', 'mg-5-gt', 'The MG 5 sedan offers varying specs by market, generally featuring a 1.5L engine (around 114-120 hp, 150 Nm torque) with CVT, though a more powerful 1.5L Turbo (173 hp, 250 Nm) exists in some regions like Bangladesh, providing faster acceleration (0-100 km/h in 8.5s). Key features often include modern tech like Apple CarPlay/Android Auto, safety with multiple airbags, and a 5-star C-NCAP rating in some trims, with boot space around 512L and good fuel efficiency', '0', 2800000.00, 3500000.00, 2200000.00, '25455852', 5000000, 1, 1330.00, '0', 'active', 0, '0', '0', 0.00, 0, 0, '2026-01-17 12:25:39', '2026-01-20 12:20:33', NULL),
(52, 7, 13, NULL, 'Minoxidil Solutions', 'minoxidil-solutions', 'মেডিকেটেড শ্যাম্পু হলো এমন বিশেষ ধরনের শ্যাম্পু যা খুশকি, সেবোরিক ডার্মাটাইটিস, সোরিয়াসিস, বা ফাঙ্গাল ইনফেকশনের মতো মাথার ত্বকের বিভিন্ন সমস্যা (যেমন চুলকানি, ফ্লেকিং, অতিরিক্ত তেল) নিরাময় বা নিয়ন্ত্রণে ব্যবহৃত হয়, যেখানে কেটোকোনাজোল, স্যালিসাইলিক অ্যাসিড, কয়লা আলকাতরা, বা সিলিকন সালফাইড-এর মতো সক্রিয় উপাদান থাকে। এটি সাধারণ শ্যাম্পুর চেয়ে ভিন্ন, কারণ এতে সক্রিয় উপাদান থাকে যা খুশকির কারণ ছত্রাক বা ব্যাকটেরিয়ার বিরুদ্ধে কাজ করে এবং অস্বস্তি কমায়।', '0', 1270.00, 1500.00, 780.00, '9654', 120, 5, 0.00, '0', 'active', 1, '0', 'ADADF SADF', 0.00, 0, 0, '2026-01-17 12:25:58', '2026-01-20 09:39:49', NULL),
(54, 7, 13, NULL, 'Hair Building Fibers', 'hair-building-fibers', 'Hair building fibers are cosmetic keratin microfibers that cling to existing hair strands using static electricity to instantly make thinning hair look thicker and fuller, effectively concealing bald spots and sparse areas for both men and women. Applied by shaking or spraying onto dry hair, they bond to create volume, can be used for root touch-ups, and resist sweat and light rain, washing out with shampoo. Popular brands include Toppik, XFusion, and Caboki, available in many colors to match natural hair.', '0', 1870.00, 2200.00, 1580.00, '7852', 119, 5, 0.00, '0', 'active', 0, '0', 'ZXDF SDF', 0.00, 0, 0, '2026-01-17 12:27:51', '2026-01-20 12:38:51', NULL),
(55, 8, 1, NULL, 'Hoco EQ2 True Wireless Bluetooth Earbuds', 'hoco-eq2-true-wireless-bluetooth-earbuds', 'Bluetooth 5.3 for seamless wireless connectivity\r\n• Hoco EQ2 TWS True Wireless in Ear Earbuds\r\n• Model: EQ2 with master-slave switching support\r\n• Long use time: 7 hours, standby time: 180 hours\r\n• Compact size: 58*49*25mm, lightweight: 50g', '0', 1500.00, 0.00, 0.00, '65646446', 50, 5, 50.00, '0', 'active', 1, '0', '', 0.00, 0, 0, '2026-01-17 12:28:02', '2026-01-20 12:34:17', '2026-01-20 12:34:17'),
(57, 11, 1, NULL, 'Remote Control Light', 'remote-control-light', '', '0', 300.00, 0.00, 0.00, '12', 10, 5, 0.00, '0', 'active', 0, '0', '0', 0.00, 0, 0, '2026-01-17 12:28:09', '2026-01-20 09:39:49', NULL),
(60, 22, 2, NULL, 'Nokia', 'nokia', 'user friendly button phone.', '0', 3000.00, 4500.00, 2700.00, '123', 100, 35, 0.00, '0', 'active', 0, '0', 'button phone', 0.00, 0, 0, '2026-01-17 12:29:22', '2026-01-20 09:39:49', NULL),
(61, 9, 13, NULL, 'Pilgrim hair serum', 'pilgrim-hair-serum', 'Pilgrim Hair Growth Serum is designed to promote hair growth, reduce hair fall, and improve overall hair health. It contains Redensyl, which targets hair follicles to induce new hair growth, and Anagain, derived from pea sprouts, which stimulates hair growth and prolongs the life cycle of hair follicles. The serum is suitable for all hair types and is non-greasy, making it easy to use without the need to wash it the day after application. It is also free from harsh chemicals and is vegan and cruelty-free, ensuring safe long-term use.', '0', 1500.00, 1700.00, 1200.00, '999', 8, 8, 50.00, '0', 'active', 0, '0', '', 0.00, 0, 0, '2026-01-17 12:29:29', '2026-01-20 09:39:49', NULL),
(62, 7, 13, NULL, 'Onion Oil', 'onion-oil', 'Onion oil, rich in sulfur and nutrients, is a popular natural remedy primarily used in hair care to promote hair growth, reduce hair fall, strengthen strands, add shine, and fight dandruff by nourishing hair follicles and improving scalp health, often blended with carrier oils like coconut or almond oil and herbs like bhringraj. It\'s available commercially or can be made at home by infusing onion juice/paste with carrier oils, and it\'s also used for skin issues like blemishes due to its antibacterial properties.', '0', 1670.00, 1820.00, 1320.00, '7896', 39, 5, 0.00, '0', 'active', 0, '0', 'DFG SDF', 0.00, 0, 0, '2026-01-17 12:29:44', '2026-01-20 12:38:51', NULL),
(63, 10, 3, NULL, 'Toyota Supra Mk5', 'toyota-supra-mk5', 'The Toyota Supra Mk5 (A90/A91) features BMW-sourced engines, primarily a 3.0L turbocharged inline-six (382 HP/368 lb-ft torque) and an available 2.0L turbo four-cylinder, paired with 8-speed auto or 6-speed manual transmissions, offering rapid 0-60 mph times (around 3.9s for the 3.0L), rear-wheel drive, Adaptive Variable Suspension, and performance-focused tech like Launch Control and active differentials, with recent models adding more power and special editions. \r\nEngine & Performance (3.0L Model)\r\nEngine: 3.0L Turbocharged Inline-6 (B58)\r\nHorsepower: Up to 382 HP (later models/Final Edition)\r\nTorque: Up to 368 lb-ft (or 369 lb-ft)\r\n0-60 mph: Around 3.9 seconds (with 3.0L)\r\nTop Speed: Electronically limited (around 155 mph / 161 mph)\r\nDrivetrain: Rear-Wheel Drive (RWD) \r\nTransmission Options\r\n8-speed ZF Automatic\r\n6-speed Manual Transmission (available in later models/Final Edition) \r\nChassis & Handling\r\nSuspension: Adaptive Variable Suspension (AVS) available\r\nFront: Independent MacPherson Strut\r\nRear: Multi-link\r\nDifferential: Active Differential (on some trims)\r\nBrakes: 4-piston Brembo (on higher trims/Final Edition) \r\nKey Features\r\nDriver Modes: Sport Mode, Launch Control\r\nTechnology: Wireless Apple CarPlay, Navigation, JBL Audio\r\nInterior: Power-adjustable sport seats, Head-Up Display (on some trims) \r\nOther Engine Option (2.0L)\r\nEngine: 2.0L Turbocharged Inline-4 (B48)\r\nPower: Around 255 HP, less weight than the 3.0L.', '0', 11000000.00, 13000000.00, 8800000.00, '23542558', 5000000, 1, 1410.00, '0', 'active', 1, '0', '0', 0.00, 0, 0, '2026-01-17 12:30:38', '2026-01-20 12:20:33', NULL),
(64, 11, 13, NULL, 'Regular mobile ray Bluecut Glass', 'regular-mobile-ray-bluecut-glass', '', '0', 550.00, 0.00, 0.00, '13', 100, 5, 0.00, '0', 'active', 0, '0', '0', 0.00, 0, 0, '2026-01-17 12:31:29', '2026-01-20 09:39:49', NULL),
(65, 7, 13, NULL, 'Biotin Supplements', 'biotin-supplements', 'Biotin supplements (Vitamin B7) support energy metabolism, brain function, and the health of hair, skin, and nails by aiding in the production of keratin, though scientific evidence for benefits in healthy individuals is limited; they\'re used to treat rare deficiencies, improve brittle nails, and are generally safe as excess is excreted, but high doses can interfere with lab tests, so consult a doctor before use.', '0', 4851.00, 6700.00, 4000.00, '25874', 99, 5, 0.00, '0', 'active', 0, '0', 'FB DGF', 0.00, 0, 0, '2026-01-17 12:31:36', '2026-01-20 12:38:51', NULL),
(66, 18, 13, NULL, 'Food', 'food', 'this is very fine', '0', 5000.00, 6500.00, 4500.00, '1', 100, 5, 50.00, '0', 'active', 0, '0', 'bnp,jamat ,shibir', 0.00, 0, 0, '2026-01-17 12:32:50', '2026-01-20 09:39:49', NULL),
(67, 8, 12, NULL, 'Sony XR-85X95L Bravia 85-Inch XR Series 4K Ultra HD Smart Google TV', 'sony-xr-85x95l-bravia-85-inch-xr-series-4k-ultra-hd-smart-google-tv', 'LIFELIKE PICTURE: The intelligent and powerful Cognitive Processor XR delivers a picture with wide dynamic contrast, detailed blacks, natural colors, and high peak brightness, replicating how we see the real world.\r\nMINI LED CONTRAST AND COLOR See ultimate contrast from thousands of Mini LEDs and billions of accurate colors, all precision-controlled by the XR Backlight Master Drive and XR Triluminos Pro.\r\nPremium Smart TV: Get access to all your favorite streaming apps in one place with Google TV, and simply use your voice to search and ask questions with Google Assistant. Supports Apple AirPlay.\r\nMOVIES ON US WITH BRAVIA CORE Enjoy streaming high-bitrate, high-quality 4K UHD movies included with the BRAVIA CORE app. Get 5 credits to redeem on latest-release movies and a 12-month subscription on hundreds of classics.\r\nALL YOUR GAME SETTINGS IN ONE PLACE Game Menu puts all your gaming picture settings and exclusive assist features in a single easy-to-manage interface.\r\nHDMI 2.1 Gaming: Get the advantage in high-performance gaming with HDMI 2.1 features like 4K/120, VRR, and ALLM.\r\nPERFECT FOR PLAYSTATION 5: Take your gaming to the next level with exclusive features like Auto HDR Tone Mapping and Auto Genre Picture Mode for optimized picture quality while gaming and streaming on your PS5 console.', '0', 649900.00, 1030900.00, 0.00, '64645464', 96, 5, 0.00, '0', 'active', 1, '0', '', 0.00, 0, 0, '2026-01-17 12:32:52', '2026-01-20 09:47:23', NULL),
(68, 11, 1, NULL, 'Digital Scale 10kg', 'digital-scale-10kg', '', '0', 300.00, 0.00, 0.00, '14', 97, 5, 0.00, '0', 'active', 0, '0', '0', 0.00, 0, 0, '2026-01-17 12:33:19', '2026-01-20 09:47:23', NULL),
(70, 9, 13, NULL, 'Dot and key sunscreen', 'dot-and-key-sunscreen', 'DOT & KEY Vitamin C + E Super Bright Sunscreen SPF 50 | Water-Light, UVA/UVB & Blue Light Protection | For Even Toned & Glowing Skin | With Liquid Spf 50+++ | No White Cast | For All Skin Types | SPF 50+ water-light sunscreen for glowing, sun-protected skinn|Infused with Triple Vitamin C, Sicilian Blood Orange & UV Filters | Reduces dullness & dark spots caused by excessive sun exposure | Protect skin against damaging UVA, UVB & blue light rays | Water-light & quick-absorbing for dewy finish on all skin types with zero white cast.', '0', 1200.00, 1400.00, 1000.00, '111222', 97, 5, 100.00, '0', 'active', 0, '0', '0', 0.00, 0, 0, '2026-01-17 12:33:59', '2026-01-20 09:47:23', NULL),
(72, 25, 11, NULL, 'Mug', 'mug', 'A mug is a type of cup, a drinking vessel usually intended for hot drinks such as coffee, hot chocolate, or tea. Mugs have handles and usually hold a larger amount of fluid than other types of cups such as teacups or coffee cups. Typically, a mug holds approximately 250–350 ml (8–12 US fl oz) of liquid.', '0', 1500.00, 1220.00, 0.00, '52', 100, 5, 0.00, '0', 'active', 0, '0', '', 0.00, 0, 0, '2026-01-17 12:34:49', '2026-01-20 09:39:49', NULL),
(74, 16, 18, NULL, 'Fulll', 'fulll', 'Natural fresh cut flowers supplier all over Bangladesh.', '0', 4050.00, 5500.00, 3400.00, '5874', 100, 50, 0.00, '0', 'active', 1, '0', 'flewersservicebd,gift,birthdaygift,anniverserygift,valobasa,dhaka\'s florist.', 0.00, 0, 0, '2026-01-17 12:35:29', '2026-01-20 09:39:49', NULL),
(75, 10, 3, NULL, 'Toyota Land Cruiser V8', 'toyota-land-cruiser-v8', 'Toyota Land Cruiser V8 specs vary by year and market, but generally feature powerful 4.5L twin-turbo diesel or 4.6L/5.7L petrol V8 engines, offering robust performance with around 270-300+ HP and significant torque (650 Nm for diesel), paired with 6-speed automatics, full-time 4WD, and luxury features like premium interiors, diff locks, and advanced tech for serious off-roading and comfort. \r\nEngine & Performance (Common Diesel Model - 4.5L D-4D)\r\nEngine: 4.5L (4461cc) Twin-Turbo V8 Diesel (1VD-FTV).\r\nPower: ~272 PS (200 kW / 268 bhp).\r\nTorque: ~650 Nm (479 lb-ft).\r\nTransmission: 6-speed automatic.\r\nDrivetrain: Full-time 4WD with a transfer case. \r\nEngine & Performance (Common Petrol Model - 4.6L)\r\nEngine: 4.6L (4608cc) V8 Petrol (1UR-FE).\r\nPower: ~304 HP.\r\nTorque: ~44.8 Kg-m (440 Nm).\r\nTransmission: 6-speed automatic. \r\nKey Features & Dimensions (Approximate)\r\nChassis: Body-on-frame construction, double-wishbone front, 4-link coil rear suspension.\r\nBrakes: Ventilated disc brakes front and rear.\r\nFuel Capacity: ~93 liters (plus optional sub-tank).\r\nGround Clearance: ~225 mm.\r\nTires: Options like 285/65R17 or 285/60R1', '0', 45000000.00, 50000000.00, 39000000.00, '25425587', 5000000, 1, 2500.00, '0', 'active', 1, '0', '0', 0.00, 0, 0, '2026-01-17 12:35:34', '2026-01-20 12:20:33', NULL),
(77, 25, 11, NULL, 'korai', 'korai', 'A mug is a type of cup, a drinking vessel usually intended for hot drinks such as coffee, hot chocolate, or tea. Mugs have handles and usually hold a larger amount of fluid than other types of cups such as teacups or coffee cups. Typically, a mug holds approximately 250–350 ml (8–12 US fl oz) of liquid.', '0', 2500.00, 200.00, 0.00, '85', 100, 5, 5.00, '0', 'active', 0, '0', '0', 0.00, 0, 0, '2026-01-17 12:36:00', '2026-01-20 09:39:49', NULL),
(79, 18, 1, NULL, 'Mobile', 'mobile', 'dfgdhsfgfsad', '0', 25000.00, 28000.00, 23000.00, '2', 100, 5, 50.00, '0', 'active', 1, '0', 'BNP,JAmat,', 0.00, 0, 0, '2026-01-17 12:37:18', '2026-01-20 09:39:49', NULL),
(80, 22, 12, NULL, 'Sony Bravia W654A 42-inch Full HD wifi Internet', 'sony-bravia-w654a-42-inch-full-hd-wifi-internet', 'Sony Bravia W654A 42-inch Full HD Edge LED  Internet enabled TV. The W65 is packed with technology to make everything you watch clearer, smoother and more full of life. It has some unexpected extras too, enabling you to seamlessly integrate your smartphone or tablet into the entertainment experience. The Sony Entertainment Network and stream content from an ever-expanding catalogue of HD movies, music and TV channels. It offers a web browser too, plus apps like Twitter, Facebook, YouTube and Skype.\r\nFEATURES\r\nScreen Size	42-inch, Aspect Ratio: 16 : 9\r\nResolution	Full HD, X-Reality PRO takes everything closer to Full HD\r\nTechnology	Screen Type: LCD, Backlight Type: LED, Dimming Type: Frame Dimming\r\nRefresh Rate	Motionflow XR 200Hz\r\nContrast	Dynamic Contrast Ratio: Over 1 million; Advanced Contrast Enhancer\r\nBrightness	Picture Quality: Face Area Detection, Video Area Detection, Super resolution for Game Mode. Live Colour Technology, Deep Colour, Intelligent MPEG Noise Reduction, 24p True Cinema\r\nSound	Bass Reflex Box Speaker, 8W+8W Audio power output, S-Force Front Surround 3D, S-Master, Dolby Digital / Dolby Digital Plus / Dolby Pulse\r\nConnectivity	Network Features: Wireless Screen Mirroring, TV Side View, Wireless LAN , Skype Ready, Sony Entertainment Network, Apps, Opera Web browser, DLNA. Input/output: RF in, HDMI In x2, SCART in (without Smartlink), USB Port, Ethernet in, Composite video in, Component video (Y/Pb/Pr) in, PCMCIA in, Analog audio in, Optical digital out, Audio out, Headphone out, Video Out (SCART)\r\nRemote	Yes\r\nDimension	Wall-mount your TV, no extra bracket needed - The TV\'s integrated stand doubles as a wall-mounting bracket, so there\'s no need to go out and buy one. TV without table-top stand 9.9 Kg\r\nOther Features	LightSensor, Viewing Angle (Right/Left): 178 (89/89), Viewing Angle (Up/Down): 178 (89/89), X-Reality PRO Picture processing engine, Power consumption (in operation): 50W in Home Mode and 95W in Shop Mode, Power saving modes, Dynamic backlight control, Backlight off mode, USB Play, USB HDD Recording, Panorama. Optional accessories: VC Camera and Micropohone, MHL Cable\r\nWarranty	5 Years Service Warranty', '0', 39999.00, 40500.00, 35000.00, '458', 100, 5, 0.00, '0', 'active', 1, '0', 'Sony Television, television', 0.00, 0, 0, '2026-01-17 12:37:20', '2026-01-20 09:39:49', NULL),
(81, 9, 13, NULL, 'livon hair serum', 'livon-hair-serum', 'Livon Hair Serum is a leave-in treatment designed to provide instant frizz control, enhanced shine, and improved manageability for all hair types. It features a lightweight, non-sticky formula that helps eliminate frizz and gives hair an ultra-glossy finish. Just a few drops can transform your hair, making it smooth and easy to style', '0', 500.00, 700.00, 450.00, '9765', 100, 5, 50.00, '0', 'active', 0, '0', '', 0.00, 0, 0, '2026-01-17 12:37:37', '2026-01-20 09:39:49', NULL),
(82, 10, 3, NULL, 'Toyota Land Cruiser Prado', 'toyota-land-cruiser-prado', 'The Toyota Land Cruiser Prado (J250 series) offers robust specs, featuring a standard 2.8L turbo-diesel engine (around 201 HP/500 Nm) with mild-hybrid tech, paired with an 8-speed automatic transmission and full-time 4WD, plus available 2.4L turbo-petrol options, seating for 7, a 3,500kg towing capacity, Toyota Safety Sense, and modern tech like wireless Apple CarPlay/Android Auto, built on a strong GA-F platform for serious off-road capability with advanced suspension systems. \r\nEngine & Performance (Typical Diesel)\r\nEngine: 2.8L 4-Cylinder Turbo Diesel (1GD-FTV)\r\nPower: ~150 kW (201 HP) @ 3400 rpm\r\nTorque: ~500 Nm @ 1600-2800 rpm\r\nTransmission: 8-speed Automatic\r\nDrive: Full-time 4WD with dual-range transfer case\r\nTowing Capacity: 3,500 kg (braked) \r\nDimensions & Capability\r\nPlatform: GA-F ladder frame chassis\r\nLength: Around 4920-4995 mm\r\nWidth: ~1980 mm\r\nHeight: ~1870-1950 mm (varies by trim)\r\nWheelbase: 2850 mm\r\nGround Clearance: ~210 mm\r\nApproach Angle: ~32 degrees\r\nFuel Capacity: Up to 110L (main + sub-tank) \r\nInterior & Technology (Varies by Trim) \r\nSeating: 7-seater capacity\r\nInfotainment: 12.3\" touchscreen with wireless Apple CarPlay & Android Auto\r\nSafety: Toyota Safety Sense (Pre-Collision System, Dynamic Radar Cruise Control, Lane Departure Alert, etc.)\r\nConvenience: Fabric or leather seats, heated/ventilated front seats, SmartKey entry, panoramic sunroof (on higher trims', '0', 22500000.00, 25000000.00, 17000000.00, '245875445', 5000000, 2, 2330.00, '0', 'active', 1, '0', '0', 0.00, 0, 0, '2026-01-17 12:39:25', '2026-01-20 12:20:33', NULL),
(83, 18, 13, NULL, 'Hunny', 'huny', 'dfgjhgj', '0', 1100.00, 1200.00, 980.00, '3', 100, 5, 50.00, '0', 'active', 1, '0', '0', 0.00, 0, 0, '2026-01-17 12:42:39', '2026-01-20 09:39:49', NULL),
(84, 18, 16, NULL, 'Cloth', 'cloth', 'dsjfhjdskhfjksdghkjdsgh', '0', 1500.00, 1800.00, 1400.00, '4', 100, 5, 1.00, '0', 'active', 1, '0', 'dhgjkfdhgkjfds', 0.00, 0, 0, '2026-01-17 12:46:05', '2026-01-20 09:39:49', NULL),
(85, 18, 5, NULL, 'sdfgdjs', 'sdfgdjs', 'fdgfdsgdsfgdsf', '0', 450000.00, 4800000.00, 440000.00, '5', 100, 5, 100.00, '0', 'active', 0, '0', '', 0.00, 0, 0, '2026-01-17 12:47:40', '2026-01-20 09:39:49', NULL),
(86, 26, 2, NULL, 'mobile phone', 'mobile-phone', 'this is a nice phone in the world', '0', 500000.00, 500000.00, 14156.00, '41', 100, 5, 50.00, '0', 'active', 1, '0', 'fb phone', 0.00, 0, 0, '2026-01-17 12:51:08', '2026-01-20 09:39:49', NULL),
(87, 22, 16, NULL, 'Lamborghini SC20 Is A 760bhp Sports Car', 'lamborghini-sc20-is-a-760bhp-sports-car', 'For all the fear-mongering about cars becoming soulless transportation boxes, enthusiasts still have a lot to look forward to in 2025. This year is already proving to be one of the biggest in recent memory for debuts of new sports cars and supercars.\r\n\r\nWith the latest technology injecting its way into the realm of performance, you’ll find far more hybrids and electric cars on this list than, say, five years ago. But don’t think that’s a bad thing. Our favorite car of 2024 was a hybrid, and one of the best performance vehicles on the market right now is an all-electric hot hatch. With that in mind, here\'s what you can look forward to this year.', '0', 8000000.00, 8500000.00, 7500000.00, '86786541', 100, 5, 250.00, '0', 'active', 1, '0', 'sports car, car, sports', 0.00, 0, 0, '2026-01-17 12:54:05', '2026-01-20 09:39:49', NULL),
(88, 28, 13, NULL, 'Vitamin B Complex', 'vitamin-b-complex', 'Vitamin B Complex, especially B1 (Thiamine), B6 (Pyridoxine), and B12 (Cobalamin), are crucial water-soluble vitamins that support nerve health, energy metabolism, red blood cell formation, and overall nervous system function, often working together to relieve symptoms like tingling, numbness, and weakness, and found in foods like meat, dairy, and fortified cereals. Deficiencies can lead to neurological issues like neuropathy, making supplementation important for at-risk groups, though high doses should be discussed with a docto', '0', 350.00, 370.00, 120.00, '12457541', -605, 100, 0.00, '0', 'active', 1, '0', 'vitamin', 0.00, 0, 0, '2026-01-17 12:55:49', '2026-01-20 11:24:19', NULL),
(89, 10, 3, NULL, 'Toyota Corolla Cross', 'toyota-corolla-cross', 'The 2025 Toyota Corolla Cross offers updated styling, including a new grille and lights, with available hybrid powertrains featuring strong fuel efficiency (around 4.1 L/100km) and options for All-Wheel Drive (AWD) for better traction, alongside standard features like Toyota Safety Sense 3.0, large digital screens, and modern connectivity, built on the reliable TNGA platform for a comfortable and capable compact SUV experience', '0', 5500000.00, 6300000.00, 4200000.00, '124578', 5000000, 1, 1320.00, '0', 'active', 1, '0', '0', 0.00, 0, 0, '2026-01-18 10:54:03', '2026-01-20 12:20:33', NULL),
(90, 10, 3, NULL, 'Toyota Corolla', 'toyota-corolla', 'The 2025 Toyota Corolla brings updates like the sporty FX trim with black accents and lowered suspension, plus tech upgrades including an available 10.5-inch touchscreen for all trims and standard Toyota Safety Sense 3.0 across the lineup, with options for gas or hybrid powertrains offering excellent fuel economy and features like wireless Apple CarPlay/Android Auto, providing a blend of style, safety, and efficiency for the popular compact car. \r\nKey Features & Updates\r\nFX Package: A new cosmetic and handling package for the sedan with a sport mesh grille, rear spoiler, black roof, 18-inch black wheels, lowering springs, and sport seats.\r\nTechnology: Standard 8-inch touchscreen on base trims, with a new available 10.5-inch touchscreen and wireless Apple CarPlay/Android Auto on higher trims.\r\nSafety: Comes standard with Toyota Safety Sense 3.0 (TSS 3.0), including Pre-Collision System, Lane Departure Alert, and Dynamic Radar Cruise Control.\r\nPowertrain: Available 2.0L 4-cylinder engine (169 hp) or an efficient hybrid option, with a Dynamic-Shift CVT.\r\nTrims: Includes LE, SE, Nightshade Edition, XSE, and Hybrid models, offering different levels of sportiness and luxury. \r\nWhat\'s New for 2025?\r\nIntroduction of the stylish FX trim.\r\nStandard 10.5-inch touchscreen available on more models.\r\nEnhanced Proactive Driving Assist within the standard TSS 3.0. \r\nFuel Economy (Est. MPG)\r\nGas: Around 31 city / 40 highway / 34 combined (varies by trim).\r\nHybrid: Can exceed 50+ MPG combined', '0', 3800000.00, 4500000.00, 3300000.00, '1232125', 5000000, 5, 1300.00, '0', 'active', 1, '0', '0', 0.00, 0, 0, '2026-01-18 10:58:43', '2026-01-20 12:20:33', NULL),
(91, 29, 18, NULL, 'flower bouquet', 'flower-bouquet', 'dsalkjfasieeeeeeeeeejan vfijasdfja vefijequw7hfsadksjidvn aiufoasd', '0', 3500.00, 3850.00, 3200.00, '14785', 96, 5, 0.00, '0', 'active', 0, '0', 'valobasabashi, gift,birthday gift, annyversary gift', 0.00, 0, 0, '2026-01-18 10:59:37', '2026-01-20 09:39:49', NULL),
(92, 10, 3, NULL, 'Toyota Noah', 'toyota-noah', 'The Toyota Noah 2025 is a popular, spacious family minivan (MPV) known for its fuel-efficient hybrid options, comfortable and flexible interior with advanced infotainment, and strong safety features like Toyota Safety Sense, offering a practical choice for families with seating for 7-8 passengers and premium features on higher trims like the S-Z, focusing on comfort, technology, and economy for various markets, especially in Asia. \r\nKey Features & Specs (General/Hybrid Models):\r\nEngine: Often features a hybrid powertrain (e.g., 1.8L engine + electric motor) for great mileage, paired with a CVT transmission.\r\nSeating: Flexible 3-row seating, typically 7 or 8-seater configurations with ample legroom.\r\nInterior: High-quality materials, large touchscreen display with smartphone integration (CarPlay/Android Auto), automatic climate control, USB ports, and smart storage solutions.\r\nSafety: Equipped with Toyota Safety Sense (Pre-Collision System, Lane Keep Assist, Cruise Control) and other safety tech like airbags, ABS, ESC, and collision safety bodies.\r\nConvenience: Power sliding doors, rear hatch with \"karakuri\" mechanism for easy access, smart keys, and available auto cruise control.\r\nDesign: Modern, dignified look with LED lighting, distinct grilles (especially on Voxy twin model), and comfortable ride quality. \r\nModel Variations:\r\nHybrid S-Z: A popular trim offering premium features like leather seats, head-up display, advanced speakers, and larger rims.\r\nToyota Voxy: The \"twin\" model, offering a more aggressive/radical design but sharing much of the same technology and platform. \r\nAvailability:\r\nWidely available in markets like Bangladesh, Japan, Singapore, and Hong Kong, often imported as brand new or reconditioned units. \r\nWhy Choose It?\r\nFuel Efficiency: The hybrid system significantly reduces running costs.\r\nSpaciousness: Excellent for families needing versatile passenger and cargo space.\r\nComfort & Tech: Luxurious feel and modern connectivity for enjoyable journeys.\r\nReliability: A trusted name in minivans, known for hassle-free ownership', '0', 3500000.00, 3800000.00, 3400000.00, '1221588', 5000000, 1, 1730.00, '0', 'active', 1, '0', '0', 0.00, 0, 0, '2026-01-18 12:08:43', '2026-01-20 12:20:33', NULL),
(93, 10, 4, NULL, 'Hyundai Creta', 'hyundai-creta', 'The 2024 Hyundai Creta is a popular compact SUV that received a significant facelift, offering updated styling, enhanced safety features like available Level 2 ADAS, and new engine options. It continues to be available in various trims, including the base Premium and higher-end Executive models. \r\nKey Updates and Features\r\nThe 2024 model year brings several updates to the Hyundai Creta, focusing on a refreshed design and technology upgrades. \r\nExterior Design: The facelift introduces a more angular and robust look, featuring a bold, blacked-out parametric jewel pattern grille, new all-width LED DRLs, and updated quad-beam LED headlights. It also features redesigned rear connected LED taillights and is available with a matte paint finish on certain trims.\r\nInterior Upgrades: The cabin has been redesigned with a modern layout, featuring a new dashboard with dual 10.25-inch screens for the infotainment system and digital instrument cluster on higher trims. Convenience features such as dual-zone automatic climate control, a panoramic sunroof, ventilated front seats, and wireless charging are available.\r\nSafety: Safety has been significantly improved, with six airbags as standard and the availability of the Hyundai SmartSense (ADAS) suite, which includes features like forward collision avoidance, blind-spot collision avoidance, and lane-keeping assist.\r\nEngine Options: The 2024 Creta offers a range of engine choices depending on the market, including a 1.5-liter naturally aspirated petrol engine, a 1.5-liter U2 CRDi diesel engine, and a new, more powerful 1.5-liter turbocharged petrol engine. \r\nSpecifications\r\nThe 2024 Hyundai Creta has the following general specifications, which may vary slightly by market and trim level: \r\nSpecification 	Details\r\nEngine Capacity	1,493cc to 1,500cc (depending on engine type)\r\nMax Power	84 kW (115 PS) to 118 kW (160 PS)\r\nTransmission	6-speed Manual, 6-speed Automatic, or Intelligent Variable Transmission (IVT/CVT)\r\nFuel Type	Petrol/Gasoline, Diesel\r\nSeating Capacity	5 seats (Creta Grand is a 7-seater option in some markets)\r\nGround Clearance	Approx. 200 mm\r\nWheels	16-inch or 17-inch alloy wheels, with 18-inch on specific variants like the Creta Grand', '0', 3000000.00, 3300000.00, 2600000.00, '12125456', 5000000, 3, 0.00, '0', 'active', 0, '0', '0', 0.00, 0, 0, '2026-01-19 11:15:24', '2026-01-20 12:20:33', NULL),
(94, 10, 4, NULL, 'Hyundai Sonata', 'hyundai-sonata', 'The 2024 Hyundai Sonata is a midsize sedan that received a significant refresh with updated styling, a revamped interior featuring new technology, and the addition of an all-wheel drive option. It is available with multiple powertrain options, including a hybrid and a powerful N Line version. \r\nKey Updates for 2024\r\nRedesigned Exterior: The front and rear fascias are thoroughly redesigned with a sportier, more upscale look, featuring a full-width LED daytime running light bar at the front and H-themed taillights at the rear.\r\nUpgraded Interior: The cabin has a new dashboard design with a minimalist aesthetic, including a panoramic curved display that merges a 12.3-inch digital gauge cluster and a 12.3-inch infotainment screen into one unit.\r\nWireless Connectivity: Wireless Apple CarPlay and Android Auto are now standard across all trims.\r\nNew Powertrain Options: All-wheel drive (AWD) is now available as an option on the SEL trim, a first for the Sonata in this generation. The 1.6-liter turbocharged engine option has been discontinued, leaving the 2.5-liter naturally aspirated, 2.0-liter hybrid, and 2.5-liter turbo N Line engines.\r\nColumn Shifter: The gear shifter has been moved to the steering column, freeing up space in the center console for storage and a wireless charging pad on most trims. \r\nPerformance and Specs\r\nThe 2024 Hyundai Sonata offers a range of engines tailored to different driving preferences. \r\nEngine 	Horsepower	Torque	Transmission	Drivetrain\r\n2.5L 4-cylinder	191 hp	181 lb-ft	8-speed automatic	FWD (std), AWD (opt)\r\n2.0L Hybrid	192 hp (combined)	N/A	6-speed automatic	FWD\r\n2.5L Turbo 4-cylinder (N Line)	290 hp	311 lb-ft	8-speed dual-clutch	FWD', '0', 3500000.00, 3700000.00, 2900000.00, '124573', 5000000, 1, 0.00, '0', 'active', 0, '0', '0', 0.00, 0, 0, '2026-01-19 11:20:34', '2026-01-20 12:20:33', NULL),
(95, 10, 4, NULL, 'Honda Accord', 'honda-accord', 'The 2024 Honda Accord is a midsize sedan available with both a standard turbocharged gasoline engine and a more powerful, fuel-efficient hybrid powertrain, with no significant changes from the previous model year. It is praised for its spacious interior, comfortable ride, and solid reliability. \r\nTrims and Powertrains\r\nThe 2024 Honda Accord is offered in six trim levels, which are divided into two distinct powertrain types: \r\nLX and EX These trims feature a 1.5-liter turbocharged four-cylinder engine that delivers 192 horsepower and 192 lb-ft of torque, paired with a continuously variable transmission (CVT).\r\nSport Hybrid, EX-L Hybrid, Sport-L Hybrid, and Touring Hybrid These trims upgrade to a more powerful 2.0-liter four-cylinder hybrid powertrain that produces a total system output of 204 horsepower and 247 lb-ft of torque. \r\nKey Features and Technology\r\nThe 2024 Accord includes a variety of standard and optional features, with higher trims offering more premium content: \r\nInfotainment: The LX and EX trims come with a 7-inch touchscreen, while all hybrid trims feature a larger 12.3-inch color touchscreen with wireless Apple CarPlay and Android Auto compatibility. The Touring Hybrid adds a Google built-in system with Google Maps and Google Assistant functionality.\r\nInterior Comfort: Base models offer cloth seats and single-zone automatic climate control, while higher trims provide features such as leather-trimmed seats, heated front seats, dual-zone climate control, and a one-touch power moonroof. The top-tier Touring Hybrid includes heated and ventilated front seats as well as heated rear seats.\r\nSafety: The full suite of Honda Sensing safety and driver-assistive technologies is standard across all trims, including Adaptive Cruise Control, Collision Mitigation Braking System, and Lane Keeping Assist. \r\nFuel Economy (MPG)\r\nFuel efficiency is a strong point for the Accord, particularly the hybrid models. \r\nTrim 	City MPG	Highway MPG\r\nLX, EX (Gas)	29 mpg	37 mpg\r\nSport Hybrid, Sport-L Hybrid, Touring Hybrid	46 mpg	41 mpg\r\nEX-L Hybrid	51 mpg	44 mpg', '0', 5900000.00, 6500000.00, 4900000.00, '7845961', 5000000, 1, 0.00, '0', 'active', 0, '0', '0', 0.00, 0, 0, '2026-01-19 11:27:15', '2026-01-20 12:20:33', NULL),
(97, 10, 4, NULL, 'Honda Insight', 'honda-insight', 'There is no new 2024 Honda Insight model available, as the vehicle was discontinued after the 2022 model year. Its production ended in June 2022 to make way for the new Honda Civic Hybrid, which serves as its successor and began sales in late 2024 as a 2025 model year. \r\nSuccessor: Honda Civic Hybrid\r\nThe Honda Civic Hybrid effectively replaces the Insight and is a core model in Honda\'s electrification strategy, offered in both sedan and hatchback body styles. \r\nPowertrain: It uses a more powerful 2.0-liter Atkinson-cycle four-cylinder engine paired with two electric motors, delivering a combined output of approximately 200 horsepower, a significant increase over the Insight\'s 151 hp.\r\nFuel Efficiency: The Civic Hybrid is expected to offer even better fuel economy than the last Insight, with estimates in the 50+ MPG range.\r\nAvailability: It was released for the North American market in late 2024 as a 2025 model, so it is the model you would find at dealerships instead of a 2024 Insight. \r\nAvailability of the Discontinued Insight\r\nSince the Honda Insight was discontinued, you cannot purchase a new 2024 model from a dealership.\r\nUsed Market: You can still find used and certified pre-owned (CPO) 2022 and earlier Honda Insight models at dealerships and on the used car market.\r\nInventory: Some dealerships might still advertise \"2024 Honda Insight\" for informational purposes, using the latest available EPA ratings from the 2022 model to highlight its efficiency, but they are selling used inventory, not a newly manufactured 2024 model. \r\nIf you are looking for a new, efficient hybrid sedan from Honda, the Honda Civic Hybrid or the larger Honda Accord Hybrid are the current options available in their 2024 and 2025 model years', '0', 2700000.00, 3300000.00, 2500000.00, '45652588', 5000000, 1, 0.00, '0', 'active', 0, '0', '0', 0.00, 0, 0, '2026-01-19 11:31:38', '2026-01-20 12:20:33', NULL),
(99, 10, 4, NULL, 'Mazda MX-5', 'mazda-mx-5', 'The 2023 Mazda MX-5 Miata is a lightweight, two-seat, rear-wheel-drive sports car celebrated for its agile handling and pure driving experience, powered by a 181-hp 2.0L four-cylinder engine. It is available with a manual-folding soft top or a power-folding Retractable Fastback (RF) hardtop. \r\nPerformance and Driving Experience\r\nThe 2023 MX-5 Miata prioritizes driver engagement and nimble handling over raw power. \r\nEngine: All trims feature a 2.0-liter SKYACTIV-G 4-cylinder engine that produces 181 horsepower at 7,000 rpm and 151 lb-ft of torque at 4,000 rpm.\r\nTransmission: A 6-speed manual transmission with a short-throw shifter is standard, offering an engaging driving feel. A 6-speed Sport automatic transmission with paddle shifters is available as an option on the Grand Touring trim.\r\nHandling: The car\'s low curb weight (around 2,341 to 2,496 lbs) and nearly perfect 50/50 weight distribution contribute to its exceptional agility. It includes Kinematic Posture Control (KPC), a system that applies subtle brake pressure to the inner rear wheel in corners to enhance stability and minimize body roll.\r\nFuel Economy: The manual transmission version has an EPA-estimated fuel economy of 26 city/34 highway MPG, while the automatic version achieves 26 city/35 highway MPG. \r\nTrims and Features\r\nThe 2023 Mazda MX-5 Miata is offered in three primary trim levels: Sport, Club, and Grand Touring. \r\nSport: The base trim comes with 16-inch black alloy wheels, black cloth seats, a 7-inch touchscreen infotainment system with Apple CarPlay and Android Auto, LED headlights, and safety features like blind-spot monitoring and low-speed automatic emergency braking.\r\nClub: This mid-tier, factory-order only trim adds performance upgrades, including a sport-tuned suspension with Bilstein dampers, a limited-slip rear differential (with manual transmission), a front shock tower brace, and 17-inch metallic black alloy wheels. Interior upgrades include heated seats and a 9-speaker Bose premium audio system with headrest speakers.\r\nGrand Touring: The top trim focuses on luxury, adding features such as leather-trimmed seats (with an optional Terracotta Nappa leather), Mazda Navigation, auto on/off headlights, and adaptive front-lighting systems. A power-folding hardtop (RF) is available as an option on this and the Club trim. \r\nPracticality and Reviews\r\nReviewers universally praise the Miata for being incredibly fun to drive, offering an unmatched connection to the road and many \"smiles per gallon\". However, its design sacrifices practicality: \r\nPros: Highly engaging driving dynamics, classic roadster styling, impressive fuel economy for a sports car, and a simple-to-use manual soft top.\r\nCons: Very limited interior storage (no glove box, small trunk), a cramped cabin that can be tight for taller individuals, significant road noise at highway speeds, and an aging infotainment interface.', '0', 3900000.00, 4500000.00, 3500000.00, '7895425', 5000000, 2, 0.00, '0', 'active', 0, '0', '0', 0.00, 0, 0, '2026-01-19 11:36:21', '2026-01-20 12:20:33', NULL),
(100, 10, 4, NULL, 'Mazda 3', 'mazda-3', 'The 2023 Mazda 3 is a compact sedan and hatchback renowned for its near-premium interior, engaging driving dynamics, and excellent safety ratings. It offers a choice of potent non-turbo and turbocharged engines, with available all-wheel drive. \r\nKey Specifications\r\nFeature 	Details\r\nBody Styles	Sedan (4-door) and Hatchback (5-door)\r\nSeating Capacity	5\r\nDrivetrain	Standard Front-Wheel Drive (FWD); available i-Activ All-Wheel Drive (AWD) on select trims and standard on Turbo models\r\nEngines	Skyactiv-G 2.5L 4-cylinder (191 hp); Skyactiv-G 2.5L Turbo 4-cylinder (up to 250 hp with premium fuel)\r\nTransmission	Standard 6-speed automatic; 6-speed manual available only on the FWD Premium hatchback model\r\nSafety Rating	5-star overall safety rating from NHTSA; IIHS Top Safety Pick+ award\r\nCargo Space	Sedan: 13.2 cubic feet; Hatchback: 20.1 cubic feet (with rear seats up)\r\nWhat\'s New for 2023\r\nEngine Updates: The previous 2.0-liter base engine was discontinued for most markets, making the 2.5-liter the new standard engine. The 2.5-liter naturally aspirated engine received a 5 horsepower bump (to 191 hp) and improved fuel economy, thanks to updated cylinder deactivation technology.\r\nSafety Features: All models gained standard rear side airbags.\r\nTrim Adjustments: The lineup was slightly reshuffled, with the Carbon Edition trim now offering an optional AWD system. \r\nExpert Reviews & User Opinions\r\nThe 2023 Mazda 3 is widely praised for offering a premium experience that punches above its class. \r\nPros: Reviewers on Edmunds and Car Connection praise the confident handling and fun-to-drive dynamics. The interior materials and design are frequently described as sophisticated and near-luxury. The turbocharged engine provides brisk acceleration, and the availability of AWD is a key differentiator in the compact segment.\r\nCons: Common critiques include limited rear legroom and cargo space compared to rivals like the Honda Civic and Hyundai Elantra. Some reviewers and users find the non-touchscreen infotainment system cumbersome to operate, particularly with Apple CarPlay and Android Auto interfaces. Rear visibility in the hatchback is also noted as a weakness due to its design', '0', 2750000.00, 3000000.00, 2500000.00, '78954213', 5000000, 1, 0.00, '0', 'active', 1, '0', '0', 0.00, 0, 0, '2026-01-19 11:40:41', '2026-01-20 12:20:33', NULL),
(101, 29, 17, NULL, 'Flowers Jewlary', 'flowers-jewlary', 'primium flower jewlary.', '0', 2200.00, 2700.00, 1950.00, '1247', 0, 5, 0.00, '0', 'active', 0, '0', 'fuler gohona,biyesadhi', 0.00, 0, 0, '2026-01-20 09:53:51', '2026-01-20 10:09:33', NULL),
(102, 29, 17, NULL, 'Fuler gohona', 'fuler-gohona', 'gohona by natural flowers', '0', 2580.00, 3210.00, 2200.00, '3214', -1, 5, 0.00, '0', 'active', 1, '0', '', 0.00, 0, 0, '2026-01-20 09:56:02', '2026-01-20 10:48:39', NULL),
(104, 33, 16, NULL, 'Bat', 'bat', 'England Willow Bat', '0', 100000.00, 102000.00, 0.00, '15', 0, 5, 0.00, '0', 'active', 0, '0', 'bat, best bat in bangladesh, england bat', 0.00, 0, 0, '2026-01-20 10:06:42', '2026-01-20 10:10:27', NULL),
(105, 29, 17, NULL, 'wholesale flowers', 'wholesale-flowers', 'all kind of natural cut flowers aupplier', '0', 1254.00, 1600.00, 1472.00, '2541', 0, 5, 0.00, '0', 'active', 0, '0', 'agargaon flowers', 0.00, 0, 0, '2026-01-20 10:07:11', '2026-01-20 10:13:54', NULL),
(106, 10, 4, NULL, 'Mitsubishi Outlander', 'mitsubishi-outlander', 'The Mitsubishi Outlander is a versatile, mid-size SUV known for offering standard three-row seating and strong value for money compared to many rivals. The latest models are available with a petrol engine or a plug-in hybrid electric vehicle (PHEV) powertrain and come equipped with a suite of advanced safety and technology features. \r\nKey Features and Specifications (2026 Model Year)\r\nSeating Capacity: The Mitsubishi Outlander is one of the few vehicles in its class to offer standard seating for seven passengers (in a \"5+2\" configuration, with the third row best suited for children).\r\nEngine Options:\r\nPetrol: The primary engine is a 2.5-litre four-cylinder engine, which produces 181 horsepower and 181 lb-ft of torque. A new turbocharged 1.5-litre mild-hybrid (MHEV) engine is also being introduced, which aims to improve low-speed responsiveness and efficiency.\r\nPHEV: The Plug-in Hybrid EV model features electric motors on both axles and a 2.4-litre petrol engine, offering an all-electric range suitable for average commutes and powerful acceleration.\r\nDrivetrain: Front-wheel drive (FWD) is standard on base models, while Mitsubishi\'s advanced Super All-Wheel Control (S-AWC) 4WD system is available as an option or standard on higher trims.\r\nTechnology: Available features include a 12.3-inch digital driver display, a 10.8-inch Head-Up Display (HUD), wireless Apple CarPlay and Android Auto, a wireless smartphone charger, and a premium Dynamic Sound Yamaha audio system.\r\nSafety: The Outlander is equipped with comprehensive safety features as part of its Mi-TEc system, including Forward Collision Mitigation (FCM) with pedestrian detection, Blind Spot Warning (BSW), Lane Departure Warning (LDW), and Rear Automatic Emergency Braking (Rear AEB). \r\nTrim Levels\r\nThe 2026 Mitsubishi Outlander is available in several trim levels, allowing buyers to choose their desired balance of features and price. \r\nES: The base model comes well-equipped with 18-inch alloy wheels, 7-passenger seating, and a 12.3-inch Smartphone-link Display Audio system.\r\nSE: Adds features such as 20-inch two-tone alloy wheels, heated front seats, and MI-PILOT Assist, an advanced navigation-linked adaptive cruise control system.\r\nSEL: Further enhances luxury with leather-appointed seats (available with a quilted pattern), a heated steering wheel, and heated rear seats.\r\nTrail Edition: A more rugged option featuring specific visual enhancements and dedicated 18-inch black alloy wheels.\r\nRalliart: A special edition trim with rally-inspired graphics and unique body effects.\r\nPlatinum Edition/Exceed Tourer: The top-of-the-line variant offering premium semi-aniline leather seating with a massage function, a frameless digital rearview mirror, and a full 12-speaker Yamaha sound system.', '0', 4900000.00, 5200000.00, 4400000.00, '4512367', 5000000, 5, 0.00, '0', 'active', 1, '0', '0', 0.00, 0, 0, '2026-01-20 10:07:47', '2026-01-20 12:20:33', NULL);
INSERT INTO `products` (`id`, `vendor_id`, `category_id`, `brand_id`, `name`, `slug`, `description`, `short_description`, `price`, `compare_price`, `cost_price`, `sku`, `stock_quantity`, `min_stock_level`, `weight`, `dimensions`, `status`, `featured`, `meta_title`, `meta_description`, `rating`, `review_count`, `view_count`, `created_at`, `updated_at`, `deleted_at`) VALUES
(108, 33, 16, NULL, 'MS Dhont Bat', 'ms-dhont-bat', 'Bat', '0', 150000.00, 120000.00, 0.00, '8', 0, 5, 0.00, '0', 'active', 0, '0', 'Dhony Bat', 0.00, 0, 0, '2026-01-20 10:08:40', '2026-01-20 10:14:18', NULL),
(110, 33, 16, NULL, 'Helmet', 'helmet', 'Helmet', '0', 5000.00, 0.00, 0.00, '9', 0, 5, 0.00, '0', 'active', 0, '0', '0', 0.00, 0, 0, '2026-01-20 10:10:27', '2026-01-20 12:20:33', NULL),
(111, 29, 18, NULL, 'Money bouquet', 'money-bouquet', 'bouquet made by money.', '0', 10000.00, 12000.00, 9500.00, '1245', 0, 5, 0.00, '0', 'active', 0, '0', 'gift shop in dhaka', 0.00, 0, 0, '2026-01-20 10:11:26', '2026-01-20 10:14:10', NULL),
(112, 10, 4, NULL, 'Mitsubishi Pajero Sport', 'mitsubishi-pajero-sport', 'The Mitsubishi Pajero Sport is a rugged, 7-seater, body-on-frame SUV known for its strong off-road capabilities and value for money, though some reviewers note its interior is starting to feel dated compared to newer rivals. It is built on the same platform as the Mitsubishi Triton ute. \r\nKey Specifications (Model dependent)\r\nSpecifications can vary by market and trim level. The following are typical specifications based on recent models: \r\nEngine: 2.4L MIVEC intercooled turbo diesel, 4-cylinder.\r\nPower & Torque: Approximately 133kW (178 hp) at 3500rpm and 430Nm of torque at 2500rpm.\r\nTransmission: 8-speed automatic transmission with paddle shifters.\r\nDrivetrain: Available in both 2WD and Mitsubishi\'s \"Super Select II\" 4WD system, which can be used on sealed surfaces with an open center differential.\r\nSeating Capacity: 7 seats across three rows (some base variants offer 5 seats).\r\nTowing Capacity: Up to 3.1 tonnes (braked).\r\nGround Clearance: 218 mm.\r\nSuspension: Double wishbone front suspension and a 3-link coil spring rear suspension system for improved on-road comfort over its ute sibling. \r\nReviews and Expert Opinions\r\nOverall, the Pajero Sport is considered a dependable and capable vehicle, particularly for adventurous families or those who require genuine off-road ability. \r\nPros:\r\nValue for money: It often undercuts competitors like the Ford Everest and Toyota Fortuner while offering a strong list of features.\r\nOff-road performance: Its ladder-frame chassis, Super Select II 4WD system (with various off-road modes: Gravel, Mud/Snow, Sand, and Rock), and locking rear differential make it highly capable off-road.\r\nPracticality and Space: Reviewers praise the generous second-row space and the ability to fold down both rear rows for a cavernous, flat load space, which is great for cargo.\r\nWarranty: Mitsubishi\'s extensive 10-year conditional warranty is a significant draw.\r\nCons:\r\nRefinement and Ride: As a ute-derived SUV, it can feel \"truck-like,\" with a firm ride over rough surfaces and noticeable engine noise.\r\nDated Interior/Tech: The infotainment system and some interior elements are showing their age, with low-resolution cameras and a lack of modern features like wireless Android Auto or Apple CarPlay (in some trims).\r\nTowing Capacity: While a respectable 3.1 tonnes, its capacity is lower than some key rivals (e.g., Ford Everest) which can tow 3.5 tonnes. \r\nPurchasing Options\r\nThe Mitsubishi Pajero Sport is available for purchase in various locations, including showrooms in Dhaka. \r\nMitsubishi Motors Bangladesh Dhaka: The official dealer located at 215 Bir Uttam Mir Shawkat Sarak, Dhaka.\r\nJapan Auto Market: A dealer specializing in importing vehicles, including the Pajero Sport, located in Banani, Dhaka.', '0', 8999999.00, 9900000.00, 8000000.00, '741258', 5000000, 5, 0.00, '0', 'active', 1, '0', '0', 0.00, 0, 0, '2026-01-20 10:12:05', '2026-01-20 12:20:33', NULL),
(114, 36, 3, NULL, 'car', 'car', 'nice car bd', 'nice car', 99999999.99, 99999999.99, 99999999.00, '4578', 26, 10, 1100.00, '1000', 'active', 1, '0', '0', 0.00, 0, 0, '2026-01-20 10:14:55', '2026-01-20 12:41:10', NULL),
(118, 34, 17, NULL, 'Bottle gourd (লাউ)', 'bottle-gourd-', 'Bottle Gourd (Lau) is a nutritious and fresh vegetable widely used in traditional dishes. It is rich in water content, low in fat, and ideal for a healthy diet.', '0', 100.00, 0.00, 90.00, '0', 0, 1, 2.00, '0', 'active', 0, '0', '0', 0.00, 0, 0, '2026-01-20 10:50:44', '2026-01-20 12:20:33', NULL),
(121, 34, 17, NULL, 'chili', 'chili', 'prochur jhal', '0', 120.00, 0.00, 100.00, '1325', 0, 5, 0.00, '0', 'active', 0, '0', '0', 0.00, 0, 0, '2026-01-20 10:58:42', '2026-01-20 12:20:33', NULL),
(123, 36, 3, NULL, 'joshua koblin', 'joshua-koblin', 'this is a new car', '0', 99999999.99, 99999999.99, 99999999.99, '44', NULL, 5, 1300.00, '1000', 'active', 1, '0', 'this is the nice car', 0.00, 0, 0, '2026-01-20 11:03:13', '2026-01-20 12:20:33', NULL),
(124, 36, 3, NULL, 'cambell-3zusnjh', 'cambell-3zusnjh', 'color: black', '0', 10000000.00, 12000000.00, 99999999.99, '45', NULL, 5, 1400.00, '0', 'active', 0, '0', '', 0.00, 0, 0, '2026-01-20 11:07:04', '2026-01-20 12:20:33', NULL),
(126, 36, 3, NULL, 'olav-tvedt-6lsbynp', 'olav-tvedt-6lsbynp', 'color: white', '0', 2000000.00, 2300000.00, 2500000.00, '46', 0, 5, 1400.00, '1000', 'active', 0, '0', '0', 0.00, 0, 0, '2026-01-20 11:14:51', '2026-01-20 12:20:33', NULL),
(127, 36, 4, NULL, 'stefan-rodriguez-2', 'stefan-rodriguez-2', 'color: blue', '0', 10000000.00, 1200000.00, 99999999.99, '47', NULL, 5, 1399.00, '1000', 'active', 0, '0', '', 0.00, 0, 0, '2026-01-20 11:19:30', '2026-01-20 12:20:33', NULL),
(129, 34, 17, NULL, 'Tomato', 'tomato', 'Fresh, green leafy vegetable ✅', '0', 100.00, 0.00, 80.00, '1324', 0, 5, 2.00, '0', 'active', 1, '0', '0', 0.00, 0, 0, '2026-01-20 11:20:57', '2026-01-20 12:20:33', NULL),
(130, 36, 4, NULL, 'd-panyukov-dwxlh', 'd-panyukov-dwxlh', 'color:yellow', '0', 20000000.00, 21000000.00, 20000000.00, '49', NULL, 5, 1400.00, '1000', 'active', 0, '0', '', 0.00, 0, 0, '2026-01-20 11:21:56', '2026-01-20 12:20:33', NULL),
(132, 18, 5, NULL, 'Kids Car', 'kids-car', 'This is a famous car', '0', 555555.00, 666666.00, 444444.00, '4444444', 2147483642, 5, 50.00, '0', 'active', 1, 'car', '0', 0.00, 0, 0, '2026-01-20 11:22:55', '2026-01-20 12:41:10', NULL),
(134, 34, 17, NULL, 'Korola', 'korola', 'Fresh, green leafy vegetable ✅', '0', 180.00, 0.00, 160.00, '53241', NULL, 5, 1.00, '0', 'active', 0, '0', '', 0.00, 0, 0, '2026-01-20 11:23:56', '2026-01-20 12:20:33', NULL),
(135, 34, 17, NULL, 'Red chili', 'red-chili', 'Fresh, green leafy vegetable ✅', '0', 500.00, 0.00, 450.00, '890678', NULL, 5, 235.00, '0', 'active', 0, '0', '', 0.00, 0, 0, '2026-01-20 11:25:31', '2026-01-20 12:20:33', NULL),
(136, 18, 5, NULL, 'Adult Car', 'adult-car', 'Rich car', '0', 5000.00, 6000.00, 4500.00, '445666', NULL, 5, 150.00, '0', 'active', 1, '0', 'car', 0.00, 0, 0, '2026-01-20 11:27:49', '2026-01-20 12:20:33', NULL),
(139, 18, 10, NULL, 'Adult Car', 'adult-car_750', 'jsdhfjdska', '0', 99999999.99, 99999999.99, 99999999.99, '54545454', NULL, 5, 50000.00, '0', 'active', 1, '0', 'car', 0.00, 0, 0, '2026-01-20 11:31:25', '2026-01-20 12:20:33', NULL),
(141, 39, 2, NULL, 'Sony Xperia 1 VI', 'sony-xperia-1-vi_893', 'The Sony Xperia 1 VI is a high-end smartphone that combines cutting-edge technology with a user-friendly interface. It features a 6.5-inch LTPO OLED display with a 19.5:9 aspect ratio and FHD+ resolution, providing vibrant colors and deep blacks. The phone is powered by the Snapdragon 8 Gen 3 chipset, ensuring smooth performance and efficient multitasking. With a 5000 mAh battery, it offers up to two days of usage, making it suitable for both casual and intensive use. The camera system includes a 48 MP primary lens, a 12 MP ultrawide lens, and a 12 MP telephoto lens with optical zoom capabilities, allowing for versatile photography. The Xperia 1 VI is available in multiple colors and comes with a dual SIM stand-by feature, catering to various user needs.', '0', 30000.00, 0.00, 0.00, '987445', 2147483641, 10, 1926.00, '162', 'active', 1, '0', '0', 0.00, 0, 0, '2026-01-20 11:34:26', '2026-01-20 12:41:10', NULL),
(142, 34, 17, NULL, 'Red Tomato', 'red-tomato', 'Fresh, green leafy vegetable ✅', '0', 500.00, 0.00, 450.00, '546784', NULL, 5, 13234.00, '0', 'active', 0, '0', '', 0.00, 0, 0, '2026-01-20 11:35:40', '2026-01-20 12:20:33', NULL),
(143, 39, 2, NULL, 'Google pixel 10 pro xl', 'google-pixel-10-pro-xl', 'The Google Pixel 10 Pro XL is a 5G Android smartphone announced in August 2025. It features a 6.8-inch display, powered by the Google Tensor G5 chipset, and is equipped with a 5200 mAh battery. The device offers 1024 GB of storage and 16 GB of RAM, making it one of the most powerful options in the Pixel lineup. Additionally, it boasts a waterproof build and advanced camera capabilities, making it a top choice for users seeking high performance and durability.', '0', 154999.00, 0.00, 0.00, '64646464', 2147483644, 5, 2328.00, '162', 'active', 1, '0', '0', 0.00, 0, 0, '2026-01-20 11:39:56', '2026-01-20 12:41:10', NULL),
(144, 34, 17, NULL, 'tomato', 'tomato_387', 'Fresh, green leafy vegetable ✅', '0', 235.00, 0.00, 220.00, '523', 0, 5, 41.00, '0', 'active', 0, '0', '0', 0.00, 0, 0, '2026-01-20 11:42:12', '2026-01-20 12:20:33', NULL),
(146, 39, 2, NULL, 'Samsung Galaxy S25 Ultra', 'samsung-galaxy-s25-ultra', 'Display: The Galaxy S25 Ultra boasts a 6.9-inch Dynamic AMOLED 2X display with a Quad HD+ resolution and a smooth 120Hz refresh rate, providing an immersive viewing experience for videos and games. \r\n2\r\nProcessor: It is powered by the Snapdragon 8 Elite Mobile Platform, which enhances performance and efficiency, making it ideal for multitasking and gaming. \r\n2\r\nCamera System: The device features a quad-camera setup with a groundbreaking 200MP main camera, allowing for stunning photography with advanced AI enhancements. It also includes capabilities for 4K video recording and various shooting modes, including Expert RAW for professional-grade photography. \r\n2\r\nBattery Life: The Galaxy S25 Ultra is equipped with a 5000 mAh battery, designed to last throughout the day, supporting both wired and wireless charging options. \r\n2\r\nStorage Options: Users can choose from multiple storage configurations, including 256GB, 512GB, or 1TB, ensuring ample space for apps, photos, and videos. \r\n2\r\nDurability: The phone features a titanium frame and is reinforced with Corning Gorilla Armor 2 glass, providing durability and protection against drops and scratches. It also has an IP68 rating for water and dust resistance. \r\n2\r\nSoftware: Running on Android 15 with One UI 7, the Galaxy S25 Ultra offers a user-friendly interface with enhanced features and customization options. \r\n1\r\nAdditional Features: The device includes support for the S Pen, making it versatile for note-taking and creative tasks. It also features advanced AI capabilities for improved user experience and productivity. \r\n2\r\n\r\nThe Samsung Galaxy S25 Ultra is designed for users who demand top-tier performance, cutting-edge technology, and exceptional camera capabilities in a smartphone. It is positioned as a leading choice for those seeking a premium mobile experience.', '0', 226999.00, 0.00, 0.00, '541787', 2147483642, 10, 0.00, '0', 'active', 1, '0', '0', 0.00, 0, 0, '2026-01-20 11:44:30', '2026-01-20 12:41:10', NULL),
(148, 39, 2, NULL, 'Honor X9d', 'honor-x9d', 'Durability: The Honor X9d 5G is marketed as one of the toughest mid-range smartphones, boasting an IP69K rating for water and dust resistance. It can withstand drops from heights of up to 2.5 meters and is designed to handle extreme conditions, including high-pressure water jets. \r\n\r\nBattery Life: It is equipped with a massive 8300mAh silicon-carbon battery, which allows for multi-day use without frequent charging. This makes it ideal for users who require long battery life for daily activities. \r\n2\r\nCamera Capabilities: The device features advanced photography capabilities, including a high-resolution camera system that caters to photography enthusiasts. \r\n\r\nPerformance: Powered by the Snapdragon 6 Gen 4 processor, the Honor X9d 5G offers robust performance for multitasking and gaming, making it a suitable choice for various user needs.', '0', 46999.00, 0.00, 0.00, '4654651', 2147483640, 10, 1936.00, '161', 'active', 0, '0', '0', 0.00, 0, 0, '2026-01-20 11:49:37', '2026-01-20 12:41:10', NULL),
(149, 39, 2, NULL, 'Huawei Nova 15 ultra', 'huawei-nova-15-ultra', 'Display: 6.70-inch touchscreen with a resolution of 1,084 x 2,412 pixels (396 PPI).\r\nCamera: Triple rear camera setup with a 50-megapixel (f/1.9) main camera, a 12-megapixel (f/2.4) ultra-wide camera, and a 1.5-megapixel (f/2.4) depth sensor. It also has a 50-megapixel (f/2.4) front camera for selfies.\r\nStorage: 256GB internal storage (non-expandable).\r\nBattery: 6000mAh non-removable battery.\r\nOperating System: Runs on HarmonyOS 6.0.\r\nDimensions: Measures 161.87 x 75.50 x 7.20 mm and weighs 196 grams.\r\nConnectivity: Includes Wi-Fi, GPS, Bluetooth, NFC, and USB Type-C. It also features various sensors like a fingerprint sensor, ambient light sensor, and gyroscope', '0', 80000.00, 0.00, 0.00, '448464', 2147483643, 10, 0.00, '0', 'active', 0, '0', '0', 0.00, 0, 0, '2026-01-20 12:18:16', '2026-01-20 12:41:10', NULL),
(150, 22, 13, NULL, 'fresh milk', 'fresh-milk', 'Standard cow’s milk tends to be cheaper than alternatives and is a good source of calcium. The downside is the saturated fat it contains, which many of us are eating too much of. While milk isn’t high in fat (even whole milk is four per cent fat, putting it in the ‘medium’ category), if you have it frequently, it adds up.\r\nThe good news is that it’s easy to switch to lower-fat milk. If you’re used to whole milk, switch to semi-skimmed first, which has about half as much fat as whole. The taste and texture are similar. If you are using semi-skimmed, but skimmed is a bridge too far, try one per cent milk. Most people can’t tell the difference from semi-skimmed, but it has half the fat.\r\n\r\nSome people avoid cow’s milk for environmental or ethical reasons. These are personal choices, although it’s worth being aware that non-dairy milks may also have an environmental impact.', '0', 1564.00, 5645.00, 4454.00, '1415348', 0, 5, 0.00, '0', '', 0, '0', '0', 0.00, 0, 0, '2026-01-20 12:56:37', '2026-01-20 12:56:52', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_primary` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `image_path`, `alt_text`, `sort_order`, `is_primary`) VALUES
(1, 1, 'assets/uploads/product/1768650028_9619_0.jpg', '0', 0, 1),
(2, 1, 'assets/uploads/product/1768650029_9440_1.jpg', '0', 1, 0),
(3, 1, 'assets/uploads/product/1768650029_8709_2.jpg', '0', 2, 0),
(4, 1, 'assets/uploads/product/1768650031_9063_3.jpg', '0', 3, 0),
(5, 1, 'assets/uploads/product/1768650031_6390_4.jpg', '0', 4, 0),
(6, 13, 'assets/uploads/product/1768651442_2565_0.jpg', '0', 0, 1),
(7, 13, 'assets/uploads/product/1768651447_3244_0.jpg', '0', 1, 0),
(8, 13, 'assets/uploads/product/1768651460_7921_0.jpg', '0', 2, 0),
(9, 18, 'assets/uploads/product/1768652077_4936_0.jpg', '0', 0, 1),
(10, 28, 'assets/uploads/product/1768652266_8174_0.jpg', '0', 0, 1),
(11, 29, 'assets/uploads/product/1768652276_2218_0.jpg', '0', 0, 1),
(12, 32, 'assets/uploads/product/1768652315_9399_0.jpg', '0', 0, 1),
(13, 32, 'assets/uploads/product/1768652315_3592_1.png', '0', 1, 0),
(14, 31, 'assets/uploads/product/1768652481_9188_0.webp', '0', 0, 1),
(15, 44, 'assets/uploads/product/1768652593_9842_0.jpg', '0', 0, 1),
(16, 47, 'assets/uploads/product/1768652660_7062_0.jpg', '0', 0, 1),
(17, 47, 'assets/uploads/product/1768652660_1001_1.jpg', '0', 1, 0),
(18, 47, 'assets/uploads/product/1768652660_2177_2.jpg', '0', 2, 0),
(19, 47, 'assets/uploads/product/1768652660_5442_3.jpg', '0', 3, 0),
(20, 47, 'assets/uploads/product/1768652660_1650_4.jpg', '0', 4, 0),
(21, 48, 'assets/uploads/product/1768652676_7737_0.jpg', '0', 0, 1),
(22, 51, 'assets/uploads/product/1768652739_6863_0.jpg', '0', 0, 1),
(23, 51, 'assets/uploads/product/1768652739_4853_1.jpg', '0', 1, 0),
(24, 52, 'assets/uploads/product/1768652758_6500_0.jpg', '0', 0, 1),
(25, 54, 'assets/uploads/product/1768652871_4738_0.jpg', '0', 0, 1),
(26, 55, 'assets/uploads/product/1768652882_1142_0.jpg', '0', 0, 1),
(27, 50, 'assets/uploads/product/1768652909_7759_0.webp', '0', 0, 1),
(28, 57, 'assets/uploads/product/1768652940_9686_0.webp', '0', 0, 1),
(29, 60, 'assets/uploads/product/1768652962_9080_0.webp', '0', 0, 1),
(30, 60, 'assets/uploads/product/1768652962_9149_1.jpg', '0', 1, 0),
(31, 60, 'assets/uploads/product/1768652962_2189_2.jpg', '0', 2, 0),
(32, 60, 'assets/uploads/product/1768652962_2873_3.jpg', '0', 3, 0),
(33, 60, 'assets/uploads/product/1768652962_3010_4.jpg', '0', 4, 0),
(34, 60, 'assets/uploads/product/1768652962_5879_5.jpg', '0', 5, 0),
(35, 61, 'assets/uploads/product/1768652969_4136_0.jpg', '0', 0, 1),
(36, 62, 'assets/uploads/product/1768652984_3497_0.jpg', '0', 0, 1),
(37, 63, 'assets/uploads/product/1768653038_5372_0.jpg', '0', 0, 1),
(38, 63, 'assets/uploads/product/1768653038_4451_1.jpg', '0', 1, 0),
(39, 64, 'assets/uploads/product/1768653089_5046_0.webp', '0', 0, 1),
(40, 65, 'assets/uploads/product/1768653096_3674_0.jpg', '0', 0, 1),
(41, 66, 'assets/uploads/product/1768653170_2091_0.webp', '0', 0, 1),
(42, 67, 'assets/uploads/product/1768653172_1292_0.jpg', '0', 0, 1),
(43, 68, 'assets/uploads/product/1768653199_9948_0.webp', '0', 0, 1),
(44, 70, 'assets/uploads/product/1768653254_9373_0.jpg', '0', 0, 1),
(45, 74, 'assets/uploads/product/1768653329_1569_0.png', '0', 0, 1),
(46, 74, 'assets/uploads/product/1768653329_4230_1.png', '0', 1, 0),
(47, 74, 'assets/uploads/product/1768653330_9564_2.png', '0', 2, 0),
(48, 74, 'assets/uploads/product/1768653330_1695_3.png', '0', 3, 0),
(49, 74, 'assets/uploads/product/1768653330_7012_4.png', '0', 4, 0),
(50, 75, 'assets/uploads/product/1768653334_9486_0.jpg', '0', 0, 1),
(51, 79, 'assets/uploads/product/1768653438_4119_0.jpg', '0', 0, 1),
(52, 80, 'assets/uploads/product/1768653440_1272_0.webp', '0', 0, 1),
(53, 80, 'assets/uploads/product/1768653440_7862_1.jpg', '0', 1, 0),
(54, 80, 'assets/uploads/product/1768653440_2829_2.jpg', '0', 2, 0),
(55, 80, 'assets/uploads/product/1768653440_7358_3.jpg', '0', 3, 0),
(56, 81, 'assets/uploads/product/1768653457_5755_0.jpg', '0', 0, 1),
(57, 82, 'assets/uploads/product/1768653565_1714_0.jpg', '0', 0, 1),
(58, 77, 'assets/uploads/product/1768653656_8099_0.png', '0', 0, 1),
(59, 83, 'assets/uploads/product/1768653759_1832_0.jpg', '0', 0, 1),
(60, 84, 'assets/uploads/product/1768653965_5870_0.jpg', '0', 0, 1),
(61, 85, 'assets/uploads/product/1768654060_9646_0.jpg', '0', 0, 1),
(62, 63, 'assets/uploads/product/1768654156_1021_0.jpg', '0', 2, 0),
(63, 86, 'assets/uploads/product/1768654268_8903_0.jpg', '0', 0, 1),
(64, 87, 'assets/uploads/product/1768654445_9951_0.jpg', '0', 0, 1),
(65, 87, 'assets/uploads/product/1768654446_8346_1.jpg', '0', 1, 0),
(66, 87, 'assets/uploads/product/1768654446_9229_2.jpg', '0', 2, 0),
(67, 87, 'assets/uploads/product/1768654446_6130_3.jpg', '0', 3, 0),
(68, 87, 'assets/uploads/product/1768654446_7001_4.jpg', '0', 4, 0),
(69, 88, 'assets/uploads/product/1768654549_5738_0.jpg', '0', 0, 1),
(70, 89, 'assets/uploads/product/1768733643_4270_0.jpg', '0', 0, 1),
(71, 90, 'assets/uploads/product/1768733923_4748_0.jpg', '0', 0, 1),
(72, 91, 'assets/uploads/product/1768733977_6849_0.png', '0', 0, 1),
(73, 91, 'assets/uploads/product/1768733977_9482_1.png', '0', 1, 0),
(74, 91, 'assets/uploads/product/1768733977_6875_2.png', '0', 2, 0),
(75, 91, 'assets/uploads/product/1768733978_3746_3.png', '0', 3, 0),
(76, 91, 'assets/uploads/product/1768733978_9915_4.png', '0', 4, 0),
(77, 92, 'assets/uploads/product/1768738123_6470_0.jpg', '0', 0, 1),
(78, 93, 'assets/uploads/product/1768821324_2764_0.jpg', '0', 0, 1),
(79, 93, 'assets/uploads/product/1768821324_6553_1.jpg', '0', 1, 0),
(80, 94, 'assets/uploads/product/1768821634_7537_0.jpg', '0', 0, 1),
(81, 94, 'assets/uploads/product/1768821634_8694_1.jpg', '0', 1, 0),
(82, 95, 'assets/uploads/product/1768822035_7834_0.jpg', '0', 0, 1),
(83, 97, 'assets/uploads/product/1768822298_9266_0.jpg', '0', 0, 1),
(84, 97, 'assets/uploads/product/1768822298_4987_1.jpg', '0', 1, 0),
(85, 99, 'assets/uploads/product/1768822581_2130_0.jpg', '0', 0, 1),
(86, 99, 'assets/uploads/product/1768822581_1580_1.jpg', '0', 1, 0),
(87, 100, 'assets/uploads/product/1768822841_7361_0.jpg', '0', 0, 1),
(88, 100, 'assets/uploads/product/1768822841_7924_1.jpg', '0', 1, 0),
(89, 101, 'assets/uploads/product/1768902831_6203_0.jpeg', '0', 0, 1),
(90, 101, 'assets/uploads/product/1768902831_6712_1.jpeg', '0', 1, 0),
(91, 101, 'assets/uploads/product/1768902831_4387_2.jpeg', '0', 2, 0),
(92, 101, 'assets/uploads/product/1768902831_9441_3.jpeg', '0', 3, 0),
(93, 101, 'assets/uploads/product/1768902831_8347_4.jpeg', '0', 4, 0),
(94, 102, 'assets/uploads/product/1768902962_3278_0.jpeg', '0', 0, 1),
(95, 102, 'assets/uploads/product/1768902962_8852_1.jpeg', '0', 1, 0),
(96, 102, 'assets/uploads/product/1768902962_2098_2.jpeg', '0', 2, 0),
(97, 102, 'assets/uploads/product/1768902962_2227_3.jpeg', '0', 3, 0),
(100, 104, 'assets/uploads/product/1768903602_8684_0.png', '0', 0, 1),
(101, 105, 'assets/uploads/product/1768903631_5234_0.jpg', '0', 0, 1),
(102, 105, 'assets/uploads/product/1768903631_3648_1.jpg', '0', 1, 0),
(103, 105, 'assets/uploads/product/1768903631_1399_2.jpg', '0', 2, 0),
(104, 105, 'assets/uploads/product/1768903632_9350_3.jpg', '0', 3, 0),
(105, 105, 'assets/uploads/product/1768903632_9074_4.jpg', '0', 4, 0),
(106, 106, 'assets/uploads/product/1768903667_7480_0.jpg', '0', 0, 1),
(107, 108, 'assets/uploads/product/1768903720_6798_0.png', '0', 0, 1),
(108, 110, 'assets/uploads/product/1768903865_9958_0.png', '0', 0, 1),
(109, 111, 'assets/uploads/product/1768903886_6655_0.jpeg', '0', 0, 1),
(110, 111, 'assets/uploads/product/1768903887_1089_1.jpeg', '0', 1, 0),
(111, 111, 'assets/uploads/product/1768903887_3613_2.jpeg', '0', 2, 0),
(112, 111, 'assets/uploads/product/1768903887_3921_3.jpeg', '0', 3, 0),
(113, 111, 'assets/uploads/product/1768903887_8888_4.jpeg', '0', 4, 0),
(114, 112, 'assets/uploads/product/1768903925_7810_0.jpg', '0', 0, 1),
(115, 110, 'assets/uploads/product/1768904075_6894_0.png', '0', 1, 0),
(116, 110, 'assets/uploads/product/1768904091_2333_0.png', '0', 2, 0),
(117, 114, 'assets/uploads/product/1768904095_3467_0.jpg', '0', 0, 1),
(118, 114, 'assets/uploads/product/1768906578_6583_0.jpg', '0', 1, 0),
(119, 123, 'assets/uploads/product/1768906993_5981_0.jpg', '0', 0, 1),
(120, 124, 'assets/uploads/product/1768907224_7633_0.jpg', '0', 0, 1),
(121, 126, 'assets/uploads/product/1768907709_1474_0.jpg', '0', 0, 1),
(122, 121, 'assets/uploads/product/1768907810_8421_0.jpg', '0', 0, 1),
(123, 118, 'assets/uploads/product/1768907840_6345_0.jpg', '0', 0, 1),
(124, 127, 'assets/uploads/product/1768907970_3999_0.jpg', '0', 0, 1),
(125, 129, 'assets/uploads/product/1768908074_9810_0.jpg', '0', 0, 1),
(126, 130, 'assets/uploads/product/1768908116_4610_0.jpg', '0', 0, 1),
(129, 132, 'assets/uploads/product/1768908175_9774_0.jpg', '0', 0, 1),
(130, 134, 'assets/uploads/product/1768908236_5213_0.jpg', '0', 0, 1),
(131, 135, 'assets/uploads/product/1768908331_6793_0.jpg', '0', 0, 1),
(132, 136, 'assets/uploads/product/1768908469_8266_0.jpg', '0', 0, 1),
(133, 139, 'assets/uploads/product/1768908685_1718_0.jpg', '0', 0, 1),
(134, 141, 'assets/uploads/product/1768908881_6009_0.jpg', '0', 0, 1),
(135, 142, 'assets/uploads/product/1768908940_5375_0.jpg', '0', 0, 1),
(136, 143, 'assets/uploads/product/1768909196_2193_0.jpg', '0', 0, 1),
(137, 144, 'assets/uploads/product/1768909332_4047_0.jpg', '0', 0, 1),
(138, 146, 'assets/uploads/product/1768909482_3863_0.jpg', '0', 0, 1),
(139, 148, 'assets/uploads/product/1768909777_3753_0.jpg', '0', 0, 1),
(140, 149, 'assets/uploads/product/1768911496_9472_0.jpg', '0', 0, 1),
(141, 150, 'assets/uploads/product/1768913810_5499_0.jpg', '0', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `order_item_id` int(11) NOT NULL,
  `rating` tinyint(4) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `title` varchar(255) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `verified_purchase` tinyint(1) DEFAULT 1,
  `helpful_count` int(11) DEFAULT 0,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subscribers`
--

CREATE TABLE `subscribers` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `status` enum('subscribed','unsubscribed') DEFAULT 'subscribed',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subscribers`
--

INSERT INTO `subscribers` (`id`, `email`, `status`, `created_at`) VALUES
(1, 'user01@gmail.com', 'subscribed', '2026-01-11 11:34:46'),
(2, 'asadkhan@gmail.com', 'subscribed', '2026-01-11 11:48:48'),
(3, 'admin@gmail.com', 'subscribed', '2026-01-12 09:43:12'),
(4, 'asadkhan1122@gmail.com', 'subscribed', '2026-01-17 11:47:08'),
(5, '253-22-008@diu.edu.bd', 'subscribed', '2026-01-17 12:07:00'),
(6, 'webdevr68@gmail.com', 'subscribed', '2026-01-17 12:10:19'),
(7, 'user.last@gmail.com', 'subscribed', '2026-01-17 12:12:19'),
(8, 'kafi@gmail.com', 'subscribed', '2026-01-17 12:12:25'),
(9, 'rr9jahidulislam11220@gmail.com', 'subscribed', '2026-01-17 12:14:03'),
(10, 'lastvendor@gmail.com', 'subscribed', '2026-01-17 12:15:33'),
(11, 'alauddin@gmail.com', 'subscribed', '2026-01-17 12:16:29'),
(12, 'abc@gmail.com', 'subscribed', '2026-01-17 12:20:39'),
(13, 'mm9330414@gmail.com', 'subscribed', '2026-01-17 12:22:45'),
(14, 'rr@gmail.com', 'subscribed', '2026-01-17 12:29:53'),
(15, 'neurocare@gmail.com', 'subscribed', '2026-01-17 12:51:07'),
(16, 'patwarysohan45@gmail.com', 'subscribed', '2026-01-18 11:05:02'),
(17, 'abu@gmail.com', 'subscribed', '2026-01-20 09:32:15'),
(18, 'kabu@gmail.com', 'subscribed', '2026-01-20 10:19:13'),
(19, 'asadkhan3344@gmail.com', 'subscribed', '2026-01-20 11:17:53');

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES
(1, 'company_name', 'MarketPlace', 'The name of the website/company', '2026-01-20 11:37:08', '2026-01-20 11:37:08'),
(2, 'site_logo', 'assets/uploads/settings/logo.png', 'Path to the main logo image', '2026-01-20 11:37:08', '2026-01-20 11:37:08'),
(3, 'site_favicon', 'assets/uploads/settings/favicon.ico', 'Path to the favicon', '2026-01-20 11:37:08', '2026-01-20 11:37:08'),
(4, 'company_address', '123 E-commerce St, Digital City, Dhaka-1200', 'Physical address of the company', '2026-01-20 11:37:08', '2026-01-20 11:37:08'),
(5, 'company_email', 'support@marketplace.com', 'Contact email address', '2026-01-20 11:37:08', '2026-01-20 11:37:08'),
(6, 'company_phone', '+880 1700 000000', 'Contact phone number', '2026-01-20 11:37:08', '2026-01-20 11:37:08'),
(7, 'vat_rate', '5.00', 'VAT percentage added to orders', '2026-01-20 11:37:08', '2026-01-20 11:37:08'),
(8, 'commission_rate', '10.00', 'Commission percentage taken from vendor sales', '2026-01-20 11:37:08', '2026-01-20 11:37:08'),
(9, 'currency_symbol', '৳', 'Currency symbol to display', '2026-01-20 11:37:08', '2026-01-20 11:37:08'),
(10, 'footer_text', '&copy; 2026 MarketPlace. All rights reserved.', 'Text to display in the footer', '2026-01-20 11:37:08', '2026-01-20 11:37:08'),
(11, 'social_facebook', 'https://facebook.com', 'Facebook page URL', '2026-01-20 11:37:08', '2026-01-20 11:37:08'),
(12, 'social_twitter', 'https://twitter.com', 'Twitter profile URL', '2026-01-20 11:37:08', '2026-01-20 11:37:08'),
(13, 'social_instagram', 'https://instagram.com', 'Instagram profile URL', '2026-01-20 11:37:08', '2026-01-20 11:37:08');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('customer','vendor','admin') DEFAULT 'customer',
  `email_verified` tinyint(1) DEFAULT 0,
  `status` enum('active','inactive','suspended') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `role`, `email_verified`, `status`, `created_at`, `updated_at`) VALUES
(1, 'user user', 'user@gmail.com', '$2y$10$uulJGar.t54C0cj8YmoBCOuLNMQVowPHbbMgZhPPCExSFZX2IHG3W', '32142354', 'vendor', 0, 'active', '2026-01-11 11:31:13', '2026-01-12 10:04:28'),
(2, 'user1 user1', 'user01@gmail.com', '$2y$10$1hQ486GqwzyENnyvaYAn.eet558miUKePU/JQEUn2ptxPAGLMKAzW', '34658976', 'customer', 0, 'active', '2026-01-11 11:34:46', '2026-01-11 11:34:46'),
(3, 'Asad Khan', 'asadkhan@gmail.com', '$2y$10$jrv3sllShTMnFmjvOCxEkOieaJv12fF8DsIWIInqhz.ECbniafPWO', '01772353298', 'customer', 0, 'active', '2026-01-11 11:48:48', '2026-01-11 11:48:48'),
(4, 'md jahidul islam', 'jahid.dc.msc2022@gmail.com', '$2y$10$edclC149cTai3Fs8Ap8WfO4i.snv3jZIXZDnMfaYIcXAh7.BHQxn.', '01882774001', 'customer', 0, 'active', '2026-01-11 12:50:43', '2026-01-11 12:50:43'),
(5, 'admin admin', 'admin@gmail.com', '$2y$10$SlTsljS/IDYz2JiIlxwYuu6WAkpa0ZAnsQ9aTpEfwdC7bF/wMe3Ci', '01911123456', 'admin', 0, 'active', '2026-01-12 09:43:12', '2026-01-12 09:43:34'),
(6, 'Asad Khan', 'asadkhan1122@gmail.com', '$2y$10$xlrcCjC9KfGoCcbKN/HcUukIqVHOgcxSh0h073tMKw6eqyMs7EEMG', '01772353298', 'vendor', 0, 'active', '2026-01-17 11:47:08', '2026-01-17 11:47:08'),
(7, 'abul  hayat', 'abulhayat@gmail.com', '$2y$10$apzPN5XyJZk0KnpvCVPm0u8j14IHTcZZhTGyn03qqWfLl9p4efkHW', '01463105944', 'vendor', 0, 'active', '2026-01-17 11:49:47', '2026-01-17 11:49:47'),
(8, 'Asad khan', 'vendor@gmail.com', '$2y$10$ZqhZjRdgNo3sqXtvi8g5XOMZzH.UZ9yOa1pKgJgY.dWJAD0dKaPdu', '01772353298', 'vendor', 0, 'active', '2026-01-17 11:53:19', '2026-01-17 11:53:19'),
(9, 'jahanara ..', '253-22-008@diu.edu.bd', '$2y$10$gA.LrrVM12iCBNdtiv7epe1bhsdG3mlNbUHE/V1sRz2KjM2JdnOV6', '01537685915', 'vendor', 0, 'active', '2026-01-17 12:07:00', '2026-01-17 12:07:00'),
(10, 'Mahady Hasan', 'webdevr68@gmail.com', '$2y$10$Gdk1yVfV7W9rLXs82E9zC.dV6RHD2ShwVI2sxScNN9F5wTLPo5yza', '01909045166', 'vendor', 0, 'active', '2026-01-17 12:10:19', '2026-01-17 12:10:19'),
(11, 'Shamim  Hassan', 'shamimhassan@gmail.com', '$2y$10$MVLOnBnr.YvO78og1.K3cus00o3WqFpreZBC3feU9UlbsRBRaNlGm', '01571717682', 'vendor', 0, 'active', '2026-01-17 12:10:24', '2026-01-17 12:10:24'),
(12, 'user last', 'user.last@gmail.com', '$2y$10$NFLr9AJWessci3tBNVQT4OJfhtsRtA7RnfYZESSzTwS9N2GHsbJya', '01715258456', 'customer', 0, 'active', '2026-01-17 12:12:19', '2026-01-17 12:12:19'),
(13, 'kafi kafi', 'kafi@gmail.com', '$2y$10$iOyQTcNC39oB/2l4lDZS2OKgCQMeylh0N8v5egxUrRfviKj.WYCgO', '01781058284', 'customer', 0, 'active', '2026-01-17 12:12:25', '2026-01-17 12:12:25'),
(14, 'Md Jahidul Islam', 'rr9jahidulislam11220@gmail.com', '$2y$10$mRa7U3AS725e/Usn5zKwmO/Ambjtp4cz4asImd1WboQMF9R6H1eKy', '01575428014', 'customer', 0, 'active', '2026-01-17 12:14:03', '2026-01-17 12:14:03'),
(15, 'vendor last', 'lastvendor@gmail.com', '$2y$10$qBN46FppggDvyNfRgF.TmuzsdmJlI4HfruTIJ16h/p2GR2tNf4xaS', '01715258455', 'vendor', 0, 'active', '2026-01-17 12:15:33', '2026-01-17 12:15:33'),
(16, 'MD Al Ameen', 'ameen@gmail.com', '$2y$10$YG91lD8rIqt0Mj/a25kaZuVqiviy1vCHy46LdfqJozzE2zlJjh.bu', '010750810198', 'vendor', 0, 'active', '2026-01-17 12:15:43', '2026-01-17 12:15:43'),
(17, 'Ala Uddin', 'alauddin@gmail.com', '$2y$10$.Ab5phjeGyIZt6TRu9yUgeAc46yGt6x3BH0ey10PN.pn7L69sgFm2', '120120', 'customer', 0, 'active', '2026-01-17 12:16:29', '2026-01-17 12:16:29'),
(18, 'Abdulla Al Mosabbir', 'idbabdullaalmosabbir@gmail.com', '$2y$10$MEu7ytOR/F/BfqvKWiePm.hZEo2hJrj7r3JaOT1rLNQczpjOgkRuq', '01746901471', 'vendor', 0, 'active', '2026-01-17 12:17:05', '2026-01-17 12:17:05'),
(19, 'tuhin islam', 'tuhinislam@gmail.com', '$2y$10$czbQm46DKXT/iaM.oatRN.iuJQaVu0p5UYjO28X8c6KjDWWU.QXYm', '01781058284', 'customer', 0, 'active', '2026-01-17 12:17:27', '2026-01-20 12:25:09'),
(20, 'last vendor', 'vendorlast@gmail.com', '$2y$10$mfWG8rklW5BcLa/5nIw85uqUbAarqRbjQwnWkvgC.jw5ysoVzRHJO', '01715258459', 'customer', 0, 'active', '2026-01-17 12:18:32', '2026-01-17 12:18:32'),
(21, 'MD JAHIDUL ISLAM', 'abc@gmail.com', '$2y$10$RHJM4/Tla..TONyY9PZKBuuzbjwsmu8ZIFHUjiGtO0wjYzKEx5lma', '01575428014', 'customer', 0, 'active', '2026-01-17 12:20:39', '2026-01-17 12:20:39'),
(22, 'seller last', 'seller@gmail.com', '$2y$10$NMsZUA/UfBTHzVCPLgzQAOQdxXUeHd5lg3b7wzJL745as5jZ1uaoa', '01715258459', 'vendor', 0, 'active', '2026-01-17 12:20:44', '2026-01-20 12:26:25'),
(23, 'Rayhan Khan', 'alauddin1@gmail.com', '$2y$10$kALpm3/7Cea10OvAC5VzMOb2YB.aszmzc.4LWGeliKBrB/2wngmz.', '123456789', 'customer', 0, 'active', '2026-01-17 12:20:44', '2026-01-17 12:20:44'),
(24, 'MD JAHIDUL ISLAM', 'mm9330414@gmail.com', '$2y$10$RHkxn.VbkCjIVj0PXiFUr.mBVEAI8iNf7FdZpLhepoXj.5cst72sG', '01701304686', 'vendor', 0, 'active', '2026-01-17 12:22:45', '2026-01-17 12:22:45'),
(25, 'Jahidul Islam', 'rr@gmail.com', '$2y$10$Vj8a.c9Ixh0RHx.pYHqtguYqQMqHnx7HpKIZIYX/Eye/Zwwhw2EFO', '01682552884', 'vendor', 0, 'active', '2026-01-17 12:29:53', '2026-01-17 12:29:53'),
(26, 'tuhin islam', 'tuhinislam8@gmail.com', '$2y$10$nRfTlV5aQcH7uRvYr3JoM.C3QN.abvvIUgD4xoW2Xi1wpm8D9hSfG', '01781058284', 'vendor', 0, 'active', '2026-01-17 12:40:25', '2026-01-17 12:40:25'),
(27, 'Tuhin  islam', 'tuhin@gmail.com', '$2y$10$HTbf8HBzLfB/lOI7NhHRMOpqRnvylU8iy24WurRM5Q/tKfk.Wn3Te', '01781058284', 'vendor', 0, 'active', '2026-01-17 12:40:43', '2026-01-20 12:39:37'),
(28, 'Random Alavola', 'neurocare@gmail.com', '$2y$10$mH85tr.QxkOvhMSZXYsGquxYN6djjXWLRVxaXzClqIgyvW/0Lc9iG', '01909045169', 'vendor', 0, 'active', '2026-01-17 12:51:07', '2026-01-17 12:51:07'),
(29, 'MD Al Ameen', 'alameen@gmail.com', '$2y$10$.HumqXzCNngXw99cLcJ23uCVzNfxxo5kgbqDaXGUQgRvpElROu8L.', '017580810198', 'vendor', 0, 'active', '2026-01-18 10:56:02', '2026-01-18 10:56:02'),
(30, 'Mahady Hasan', 'patwarysohan45@gmail.com', '$2y$10$gu1.0VUzJoLyTB3yW4J4DeQSv6Z4ZsTgAdwdScX8ZKHf.dEBavVhq', '01775394527', 'customer', 0, 'active', '2026-01-18 11:05:02', '2026-01-18 11:05:02'),
(31, 'Ajaira Customer', 'ajaira@gmail.com', '$2y$10$e9fhh/VgwISOLfKjOENI1uhBSzsCwKsWl8pc8EM54Rxbqo4ptUCHm', '01685542211', 'customer', 0, 'active', '2026-01-18 11:08:46', '2026-01-18 11:08:46'),
(32, 'mamun mamun', 'mamun@gmail.com', '$2y$10$zuwqSngMOLIEZZuVj8D38OkIphi2lLY6ZfPuhxlq.EHQMLUEDLs4e', '01911123456', 'customer', 0, 'active', '2026-01-19 12:52:27', '2026-01-19 12:52:27'),
(33, 'MD JAHIDUL ISLAM', 'abu@gmail.com', '$2y$10$tDIH0SuR88xXS1Vte7ytlenvH4X57J8AlMcXHXAQlsGtlB84Di7Ha', '01701304686', 'vendor', 0, 'active', '2026-01-20 09:32:15', '2026-01-20 09:32:15'),
(34, 'Abdullah  Al Kafi', 'kafi1@gmail.com', '$2y$10$Xzvq1gWbDHKkIT0MPcSRPuJ1hDMN5dFt0G6YsGKKNUsAwHNvi7oGa', '01781058284', 'vendor', 0, 'active', '2026-01-20 09:58:05', '2026-01-20 09:58:05'),
(35, 'tuhin islam', 'pb@gmail.com', '$2y$10$GO/naf8QtfGyqXV2DZhjyuFP.99l0rjfign6g5U7BJli7t9.4XPdm', '0145256565', 'customer', 0, 'active', '2026-01-20 10:07:48', '2026-01-20 10:07:48'),
(36, 'tuhin islam', 'pope@gmail.com', '$2y$10$VvZmgz4z9DjFaCw1G.h5NuKtDuikl5wZEC.VOoCEonWwKt3xypxPG', '01475525641', 'vendor', 0, 'active', '2026-01-20 10:09:46', '2026-01-20 10:09:46'),
(37, 'jahid islam', 'kabu@gmail.com', '$2y$10$IiFSU3wSvfov36/BrzSYIuS6z/nYhbiMt6zYX3cm2Bx6eUswnsJTy', '01888877245', 'customer', 0, 'active', '2026-01-20 10:19:13', '2026-01-20 10:19:13'),
(38, 'ISDB Bishew', 'isdb@gmail.com', '$2y$10$JjQ8cXMHlfnk.rIUaMxz.ONV7Y0k7yYVXZePyCACuSCBzLhXJSs0C', '01895431599', 'customer', 0, 'active', '2026-01-20 11:09:40', '2026-01-20 11:09:40'),
(39, 'Asad khan', 'asadkhan3344@gmail.com', '$2y$10$ekeiDIr9cHeODQB85edJru1FTXPad7zgvhn/uXS9Pe8NNFA75MDp.', '01772353298', 'vendor', 0, 'active', '2026-01-20 11:17:53', '2026-01-20 11:17:53'),
(40, 'Asad Khan', 'khan@gmail.com', '$2y$10$7DwzB8RvE6A5oK5VJapRAuLCI6HwPKuox4zCmJAKrnkot2vdFbpkq', '01876521466', 'customer', 0, 'active', '2026-01-20 11:22:53', '2026-01-20 11:22:53'),
(41, 'tuhin islam', 'tuhinislam12@gmail.com', '$2y$10$ZLT/FBeRF7X9L.sRiP4PXeAqKkdPJVqbxtTDb92z.7deveDrpV90y', '01781058284', 'customer', 0, 'active', '2026-01-20 11:37:34', '2026-01-20 11:37:34'),
(42, 'Asad Khan', 'asadkhanwithk@gmail.com', '$2y$10$6yTmEcLpLGvAmB5Kx0DHtudsaTy252k9bybH5iK66qasacPT0y9ha', '01235425411', 'customer', 0, 'active', '2026-01-20 12:35:03', '2026-01-20 12:35:03'),
(43, 'Shamim  Hassan', 'shamim@gmail.com', '$2y$10$x2RM8I.qmAVpCfAjNglZyOP/38y8Rf58GT4LqlYTDesj2WWT.R4hS', '01571717682', 'customer', 0, 'active', '2026-01-20 12:37:46', '2026-01-20 12:37:46');

-- --------------------------------------------------------

--
-- Table structure for table `vendor_profiles`
--

CREATE TABLE `vendor_profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `store_name` varchar(100) NOT NULL,
  `store_description` text DEFAULT NULL,
  `store_logo` varchar(255) DEFAULT NULL,
  `store_banner` varchar(255) DEFAULT NULL,
  `business_address` text DEFAULT NULL,
  `trade_license` varchar(100) DEFAULT NULL,
  `nid_number` varchar(20) DEFAULT NULL,
  `bank_account_name` varchar(100) DEFAULT NULL,
  `bank_account_number` varchar(50) DEFAULT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `verification_status` enum('pending','verified','rejected') DEFAULT 'pending',
  `commission_rate` decimal(5,2) DEFAULT 10.00,
  `rating` decimal(3,2) DEFAULT 0.00,
  `total_sales` decimal(12,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vendor_profiles`
--

INSERT INTO `vendor_profiles` (`id`, `user_id`, `store_name`, `store_description`, `store_logo`, `store_banner`, `business_address`, `trade_license`, `nid_number`, `bank_account_name`, `bank_account_number`, `bank_name`, `verification_status`, `commission_rate`, `rating`, `total_sales`, `created_at`) VALUES
(1, 1, 'Jhanaka Family Store', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.', '1_logo.jpg', '1_banner.png', 'Shop No# 531, Shyamoli Square Shopping Mall, (Level-5), mohammadpur Dhakaa', '436546546546546', '4567567567657657', 'Md Jhakanaka', '4565676787689879', 'ABC Bank Ltd', 'pending', 10.00, 0.00, 0.00, '2026-01-17 11:20:54'),
(2, 7, 'Tak Somadhan', '৪টি পদ্ধতিতে হেয়ার রিপ্লেসমেন্ট করানো হয়।                                                    \r\n👍উইভিং সিস্টেম (ফিক্সড)\r\n👍 ক্লিপ সিস্টেম (লক)\r\n👍 আঠা বা গ্লু সিস্টেম\r\n👍 টেপ সিস্টেম।    \r\nBogura Hair Center  দিচ্ছে সর্বোচ্চ  সেরা সার্ভিস দানের নিশ্চয়তা, ট্রেনিং প্রাপ্ত অভিজ্ঞ টেকনিক্যাল টিম দ্বারা  সার্বক্ষণিক সেবা প্রদান করা হয়। \r\nতাই আজই চলে আসুন আমাদের অফিসে। 🤝🤝🤝\r\n🚖 পার্ক রোড (সেলিনা সাইকেল স্টোর এর তৃতীয় তলায় )সাতমাথা, বগুড়া। \r\nযোগাযোগঃ\r\n👇👇👇\r\n📞+8801710987791', '7_logo.jpg', '7_banner.jpg', 'Kazipara', '12369', '789632145', 'Tack Somadhan', '8525741963', 'Islami Bank ltd', 'pending', 10.00, 0.00, 0.00, '2026-01-17 11:58:31'),
(3, 18, 'Bogura Bazar Family store', 'there are all item here', '18_logo.Jpg', '18_banner.png', 'Bogura', '1223', '45646546546', 'Abdulla Al Mosabbir', '45646546464', 'Abdulla Al Mosabbir', 'pending', 10.00, 0.00, 0.00, '2026-01-17 12:24:14'),
(4, 27, 'tuhin cosmatics store', 'dgsdgsdh', '27_logo.jpg', '27_banner.jpg', 'dhaka', '', '', '', '', '', 'pending', 10.00, 0.00, 0.00, '2026-01-17 12:42:16'),
(5, 22, 'seller last', 'Hi, there! from seller last shop here you will find everything.', '22_logo.jpg', '22_banner.jpg', '', 'ashjkanf12358', '23465432123', 'last seller', '012358985675', 'LALA Bank LTD', 'pending', 10.00, 0.00, 0.00, '2026-01-17 12:43:57'),
(6, 25, 'jahid crocaris', 'Ramgonj, Luxmipur', '25_logo.jpg', '25_banner.png', 'Zia Shophing Complex', '', '', '', '', '', 'pending', 10.00, 0.00, 0.00, '2026-01-17 12:44:16'),
(7, 10, 'SP Motors', '\"SP Motors\"  offers luxury, sports, and performance cars, offering sales, finance, and trade-ins, with a focus on high-end brands like BMW, Audi, and Mercedes', '10_logo.jpg', '10_banner.jpg', '151/A, Jafrabad Buddhijibi (Baribad), Mohammadpur, Dhaka-1207', '45452542185', '39887544857', 'Patwary', '1020304050', 'ABC Bank', 'pending', 10.00, 0.00, 0.00, '2026-01-17 12:48:04'),
(8, 8, 'tanebaen shop', 'all faiyazlami', '8_logo.png', '8_banner.png', 'Kazipara ,Mirpur, Dhaka', '54654984', '33036856464', 'Asad khan', '125897469745', 'City Bank', 'pending', 10.00, 0.00, 0.00, '2026-01-17 12:48:55'),
(9, 26, 'free sales store', 'free seller store', NULL, NULL, 'annadanagar,pirgacha,rangpur', '254545454547', '744654654', 'tuhinislam', '6134654321654', 'tuhin bank', 'pending', 10.00, 0.00, 0.00, '2026-01-17 12:55:09'),
(10, 6, 'tanebaen shop', '', '6_logo.png', '6_banner.png', 'Kazipara', '54654984', '33036856464', 'Asad khan', '125897469745', 'City Bank', 'pending', 10.00, 0.00, 0.00, '2026-01-18 10:48:07'),
(11, 34, 'Daily Fresh', 'নিত্যপ্রয়োজনীয় খাদ্যপণ্য দ্রুত ও নির্ভরযোগ্যভাবে সরাসরি গ্রাহকের দোরগোড়ায় পৌঁছে দেয়।\r\nDelivers essential food items quickly and reliably directly to customers’ doorsteps.', '34_logo.jpg', '34_banner.jpg', 'Mirpur,Dhaka-1216', '12345678', '1234567890', '12345678', '12345678', 'ABC Bank', 'pending', 10.00, 0.00, 0.00, '2026-01-20 10:09:29'),
(12, 39, 'Vertex Gadgets', 'Welcome to Vertex Gadgets, your curated source for innovative tech tools and smart accessories. We explore and review the latest in productivity gear, creative tech, and connected devices designed to enhance your workflow and digital life.\r\n\r\nVertex Gadgets is dedicated to showcasing essential technology accessories and innovative devices. From high-performance peripherals to smart home essentials, we help you find the perfect tools to build a more efficient and connected ecosystem.', '39_logo.png', '39_banner.png', 'Kazipara, Dhaka', '54654984', '33036856464', 'Asad khan', '125897469745', 'City Bank', 'pending', 10.00, 0.00, 0.00, '2026-01-20 11:29:50');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_cart_item` (`customer_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `couriers`
--
ALTER TABLE `couriers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `deliveries`
--
ALTER TABLE `deliveries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tracking_number` (`tracking_number`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `courier_id` (`courier_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_unread` (`user_id`,`read_at`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `idx_customer_status` (`customer_id`,`order_status`),
  ADD KEY `idx_payment_status` (`payment_status`),
  ADD KEY `idx_order_date` (`created_at`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `vendor_id` (`vendor_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `idx_transaction` (`transaction_id`),
  ADD KEY `idx_gateway_transaction` (`gateway_transaction_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `idx_vendor_status` (`vendor_id`,`status`),
  ADD KEY `idx_category_status` (`category_id`,`status`),
  ADD KEY `idx_featured` (`featured`,`status`),
  ADD KEY `products_ibfk_3` (`brand_id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_review` (`customer_id`,`order_item_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `order_item_id` (`order_item_id`);

--
-- Indexes for table `subscribers`
--
ALTER TABLE `subscribers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `vendor_profiles`
--
ALTER TABLE `vendor_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `couriers`
--
ALTER TABLE `couriers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `deliveries`
--
ALTER TABLE `deliveries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=151;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=142;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subscribers`
--
ALTER TABLE `subscribers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `vendor_profiles`
--
ALTER TABLE `vendor_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `carts_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `deliveries`
--
ALTER TABLE `deliveries`
  ADD CONSTRAINT `deliveries_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `deliveries_ibfk_2` FOREIGN KEY (`courier_id`) REFERENCES `couriers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `order_items_ibfk_3` FOREIGN KEY (`vendor_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`vendor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `products_ibfk_3` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_3` FOREIGN KEY (`order_item_id`) REFERENCES `order_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vendor_profiles`
--
ALTER TABLE `vendor_profiles`
  ADD CONSTRAINT `vendor_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
