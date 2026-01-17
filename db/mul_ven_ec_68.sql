-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 17, 2026 at 07:04 PM
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
  `payment_method` enum('bkash','nagad','cod') NOT NULL,
  `payment_status` enum('pending','paid','failed','refunded') DEFAULT 'pending',
  `order_status` enum('pending','confirmed','processing','shipped','delivered','cancelled','returned') DEFAULT 'pending',
  `shipping_address` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`shipping_address`)),
  `billing_address` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`billing_address`)),
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `vendor_id`, `category_id`, `brand_id`, `name`, `slug`, `description`, `short_description`, `price`, `compare_price`, `cost_price`, `sku`, `stock_quantity`, `min_stock_level`, `weight`, `dimensions`, `status`, `featured`, `meta_title`, `meta_description`, `rating`, `review_count`, `view_count`, `created_at`, `updated_at`) VALUES
(1, 1, 6, NULL, 'Walton Bike1', 'walton-bike', '11 It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here, content here\', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for \'lorem ipsum\' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).', 'qwe', 90001.00, 99000.00, 70001.00, '0', 50, 5, 20.00, '5', '', 1, '0', '0', 0.00, 0, 0, '2026-01-17 11:40:28', '2026-01-17 12:05:14'),
(13, 8, 6, NULL, 'CFMOTO 300SR', 'cfmoto-300sr', 'The CFMOTO 300SR is one of the fully-faired sportbikes exclusively featured and produced by CFMOTO, a renowned Chinese manufacturer of motorcycles and sport vehicles known as ZHEJIANG CFMOTO POWER CO., LTD. The CFMoto 300NK was introduced to the market with the intention of positioning itself between the 250SR and 450SR models, incorporating appealing features from these two motorcycles in the sport-oriented SR series.', '0', 458500.00, 0.00, 420000.00, '121515', 10, 1, 0.00, '0', '', 1, '0', '0', 0.00, 0, 0, '2026-01-17 12:03:37', '2026-01-17 12:04:20'),
(18, 7, 13, NULL, 'হেয়ার গ্রোথ সিরাম', '-', 'হেয়ার গ্রোথ সিরাম ও তেল উভয়ই চুলের বৃদ্ধিতে সাহায্য করে, তবে সিরাম হালকা টেক্সচারের এবং মূলত স্টাইলিং বা চিকিৎসার জন্য ব্যবহৃত হয়, যা চুলকে শাইন দেয়, বাড়ানো কমায় ও ঘনত্ব বাড়ায়, অন্যদিকে তেল স্ক্যাল্পে গভীর পুষ্টি জোগায়, যা চুলের গোড়া মজবুত করে; সিরাম সাধারণত পরিষ্কার ও শুকনো চুলে ব্যবহার করা হয়, আর তেল ম্যাসাজ করে লাগানো হয়, যা চুলের স্বাস্থ্য ও হাইড্রেশন উন্নত করে।', '0', 500.00, 800.00, 350.00, '9512364', 250, 5, 0.00, '0', '', 0, '0', '0', 0.00, 0, 0, '2026-01-17 12:14:24', '2026-01-17 12:14:50'),
(28, 9, 13, NULL, 'Mamaearth rosemary Hair oil', 'mamaearth-rosemary-hair-oil', 'Mamaearth Rosemary Hair Growth Oil is a herbal hair oil formulated to promote hair growth, reduce hair fall, and nourish the scalp using plant-based ingredients like rosemary and methi (fenugreek). It’s made to be lightweight, non-toxic, and suitable for all hair types.', '0', 900.00, 1100.00, 700.00, '4444', 10, 10, 200.00, '0', '', 1, '0', '', 0.00, 0, 0, '2026-01-17 12:17:46', '2026-01-17 12:17:46'),
(29, 8, 2, NULL, 'Sony Xperia 1 VI', 'sony-xperia-1-vi', 'It is a 6.5\" LTPO OLED display and 120Hz refresh rate. The phone is powered by the Qualcomm SM8650-AB Snapdragon 8 Gen 3 chipset with Adreno 750 GPU. The 5000mAh battery supports 30W fast charging. The Sony Xperia 1 VI has a triple rear camera, 48MP main sensor, 12MP ultra wide sensor, and 12MP telephoto sensor. For selfies, there\'s a single 12MP camera. The phone runs on Android 14. The Sony Xperia 1 VI price in Bangladesh is BDT 140,000 (Unofficial). It has 12GB of RAM and 256GB of internal storage. This model is officially unavailable in Bangladesh. It comes in Black, Platinum silver, Khaki green, and Scar red colors.', '0', 140000.00, 0.00, 130000.00, '985456', 20, 5, 0.00, '0', '', 1, '0', '', 0.00, 0, 0, '2026-01-17 12:17:56', '2026-01-17 12:17:56'),
(31, 11, 1, NULL, 'Tapo C200c Indoor Pan/Tilt Home Security Wi-Fi', 'tapo-c200c-indoor-pan-tilt-home-security-wi-fi', 'Tapo C200c Indoor Pan/Tilt Home Security Wi-Fi Camera, FHD, Surveillance Camera, Night Vision, 360° Viewing Angle, Two Way Audio, Motion Detection and Notifications, Integrated Acoustic and Clear Alarm', '0', 1799.00, 0.00, 850.00, '10', 10, 5, 0.00, '0', '', 0, '0', '0', 0.00, 0, 0, '2026-01-17 12:18:32', '2026-01-17 12:36:57'),
(32, 10, 10, NULL, 'MG Cyberster', 'mg-cyberster', 'The MG Cyberster is a new all-electric two-seater roadster that revives MG\'s classic sports car heritage with modern tech, featuring scissor doors, a sporty design, and impressive electric performance, hitting 0-100 km/h in about 3.2 seconds with dual motors and a 77kWh battery offering decent range, all packed with digital cockpit screens and luxury features like Bose audio and Alcantara seats, positioning itself as a stylish and powerful EV statemen.', '0', 5500000.00, 6000000.00, 4500000.00, '2154584757', 10, 1, 1885.00, '0', '', 0, '0', 'car,MG Cyberster,', 0.00, 0, 0, '2026-01-17 12:18:35', '2026-01-17 12:18:35'),
(44, 8, 4, NULL, 'Range Rover Sport HST', 'range-rover-sport-hst', 'Land Rover Range Rover Sport HST is a new by Land Rover, the price of Range Rover Sport HST in Bangladesh is BDT 10,415,121, on this page you can find the best and most updated price of Range Rover Sport HST in Bangladesh with detailed specifications and features.', '0', 10415121.00, 0.00, 0.00, '8465464', 5, 1, 0.00, '0', '', 1, '0', '', 0.00, 0, 0, '2026-01-17 12:23:13', '2026-01-17 12:23:13'),
(47, 16, 17, NULL, 'flower', 'flower', 'wholesale flower', '0', 10201.00, 11000.00, 2145.00, '147', 50, 5, 5.00, '10', '', 1, '0', '0', 0.00, 0, 0, '2026-01-17 12:24:20', '2026-01-17 12:27:47'),
(48, 9, 14, NULL, 'Dot and key cocoa nude lipbalm', 'dot-and-key-cocoa-nude-lipbalm', 'Mamaearth Rosemary Hair Growth Oil is a herbal hair oil formulated to promote hair growth, reduce hair fall, and nourish the scalp using plant-based ingredients like rosemary and methi (fenugreek). It’s made to be lightweight, non-toxic, and suitable for all hair types.', '0', 500.00, 700.00, 400.00, '3333', 10, 10, 30.00, '0', '', 0, '0', '', 0.00, 0, 0, '2026-01-17 12:24:36', '2026-01-17 12:24:36'),
(50, 11, 1, NULL, 'AirPods Pro 2nd Generation', 'airpods-pro-2nd-generation', '', '0', 399.00, 0.00, 0.00, '11', 10, 5, 0.00, '0', '', 0, '0', '0', 0.00, 0, 0, '2026-01-17 12:25:06', '2026-01-17 12:36:48'),
(51, 10, 3, NULL, 'MG 5 GT', 'mg-5-gt', 'The MG 5 sedan offers varying specs by market, generally featuring a 1.5L engine (around 114-120 hp, 150 Nm torque) with CVT, though a more powerful 1.5L Turbo (173 hp, 250 Nm) exists in some regions like Bangladesh, providing faster acceleration (0-100 km/h in 8.5s). Key features often include modern tech like Apple CarPlay/Android Auto, safety with multiple airbags, and a 5-star C-NCAP rating in some trims, with boot space around 512L and good fuel efficiency', '0', 2800000.00, 3500000.00, 2200000.00, '25455852', 6, 1, 1330.00, '0', '', 0, '0', '0', 0.00, 0, 0, '2026-01-17 12:25:39', '2026-01-17 12:31:50'),
(52, 7, 13, NULL, 'Minoxidil Solutions', 'minoxidil-solutions', 'মেডিকেটেড শ্যাম্পু হলো এমন বিশেষ ধরনের শ্যাম্পু যা খুশকি, সেবোরিক ডার্মাটাইটিস, সোরিয়াসিস, বা ফাঙ্গাল ইনফেকশনের মতো মাথার ত্বকের বিভিন্ন সমস্যা (যেমন চুলকানি, ফ্লেকিং, অতিরিক্ত তেল) নিরাময় বা নিয়ন্ত্রণে ব্যবহৃত হয়, যেখানে কেটোকোনাজোল, স্যালিসাইলিক অ্যাসিড, কয়লা আলকাতরা, বা সিলিকন সালফাইড-এর মতো সক্রিয় উপাদান থাকে। এটি সাধারণ শ্যাম্পুর চেয়ে ভিন্ন, কারণ এতে সক্রিয় উপাদান থাকে যা খুশকির কারণ ছত্রাক বা ব্যাকটেরিয়ার বিরুদ্ধে কাজ করে এবং অস্বস্তি কমায়।', '0', 1270.00, 1500.00, 780.00, '9654', 120, 5, 0.00, '0', '', 1, '0', 'ADADF SADF', 0.00, 0, 0, '2026-01-17 12:25:58', '2026-01-17 12:25:58'),
(54, 7, 13, NULL, 'Hair Building Fibers', 'hair-building-fibers', 'Hair building fibers are cosmetic keratin microfibers that cling to existing hair strands using static electricity to instantly make thinning hair look thicker and fuller, effectively concealing bald spots and sparse areas for both men and women. Applied by shaking or spraying onto dry hair, they bond to create volume, can be used for root touch-ups, and resist sweat and light rain, washing out with shampoo. Popular brands include Toppik, XFusion, and Caboki, available in many colors to match natural hair.', '0', 1870.00, 2200.00, 1580.00, '7852', 120, 5, 0.00, '0', '', 0, '0', 'ZXDF SDF', 0.00, 0, 0, '2026-01-17 12:27:51', '2026-01-17 12:27:51'),
(55, 8, 1, NULL, 'Hoco EQ2 True Wireless Bluetooth Earbuds', 'hoco-eq2-true-wireless-bluetooth-earbuds', 'Bluetooth 5.3 for seamless wireless connectivity\r\n• Hoco EQ2 TWS True Wireless in Ear Earbuds\r\n• Model: EQ2 with master-slave switching support\r\n• Long use time: 7 hours, standby time: 180 hours\r\n• Compact size: 58*49*25mm, lightweight: 50g', '0', 1500.00, 0.00, 0.00, '65646446', 50, 5, 50.00, '0', '', 1, '0', '', 0.00, 0, 0, '2026-01-17 12:28:02', '2026-01-17 12:28:02'),
(57, 11, 1, NULL, 'Remote Control Light', 'remote-control-light', '', '0', 300.00, 0.00, 0.00, '12', 10, 5, 0.00, '0', '', 0, '0', '0', 0.00, 0, 0, '2026-01-17 12:28:09', '2026-01-17 12:36:37'),
(60, 22, 2, NULL, 'Nokia', 'nokia', 'user friendly button phone.', '0', 3000.00, 4500.00, 2700.00, '123', 100, 35, 0.00, '0', '', 0, '0', 'button phone', 0.00, 0, 0, '2026-01-17 12:29:22', '2026-01-17 12:29:22'),
(61, 9, 13, NULL, 'Pilgrim hair serum', 'pilgrim-hair-serum', 'Pilgrim Hair Growth Serum is designed to promote hair growth, reduce hair fall, and improve overall hair health. It contains Redensyl, which targets hair follicles to induce new hair growth, and Anagain, derived from pea sprouts, which stimulates hair growth and prolongs the life cycle of hair follicles. The serum is suitable for all hair types and is non-greasy, making it easy to use without the need to wash it the day after application. It is also free from harsh chemicals and is vegan and cruelty-free, ensuring safe long-term use.', '0', 1500.00, 1700.00, 1200.00, '999', 8, 8, 50.00, '0', '', 0, '0', '', 0.00, 0, 0, '2026-01-17 12:29:29', '2026-01-17 12:29:29'),
(62, 7, 13, NULL, 'Onion Oil', 'onion-oil', 'Onion oil, rich in sulfur and nutrients, is a popular natural remedy primarily used in hair care to promote hair growth, reduce hair fall, strengthen strands, add shine, and fight dandruff by nourishing hair follicles and improving scalp health, often blended with carrier oils like coconut or almond oil and herbs like bhringraj. It\'s available commercially or can be made at home by infusing onion juice/paste with carrier oils, and it\'s also used for skin issues like blemishes due to its antibacterial properties.', '0', 1670.00, 1820.00, 1320.00, '7896', 40, 5, 0.00, '0', '', 0, '0', 'DFG SDF', 0.00, 0, 0, '2026-01-17 12:29:44', '2026-01-17 12:29:44'),
(63, 10, 3, NULL, 'Toyota Supra Mk5', 'toyota-supra-mk5', 'The Toyota Supra Mk5 (A90/A91) features BMW-sourced engines, primarily a 3.0L turbocharged inline-six (382 HP/368 lb-ft torque) and an available 2.0L turbo four-cylinder, paired with 8-speed auto or 6-speed manual transmissions, offering rapid 0-60 mph times (around 3.9s for the 3.0L), rear-wheel drive, Adaptive Variable Suspension, and performance-focused tech like Launch Control and active differentials, with recent models adding more power and special editions. \r\nEngine & Performance (3.0L Model)\r\nEngine: 3.0L Turbocharged Inline-6 (B58)\r\nHorsepower: Up to 382 HP (later models/Final Edition)\r\nTorque: Up to 368 lb-ft (or 369 lb-ft)\r\n0-60 mph: Around 3.9 seconds (with 3.0L)\r\nTop Speed: Electronically limited (around 155 mph / 161 mph)\r\nDrivetrain: Rear-Wheel Drive (RWD) \r\nTransmission Options\r\n8-speed ZF Automatic\r\n6-speed Manual Transmission (available in later models/Final Edition) \r\nChassis & Handling\r\nSuspension: Adaptive Variable Suspension (AVS) available\r\nFront: Independent MacPherson Strut\r\nRear: Multi-link\r\nDifferential: Active Differential (on some trims)\r\nBrakes: 4-piston Brembo (on higher trims/Final Edition) \r\nKey Features\r\nDriver Modes: Sport Mode, Launch Control\r\nTechnology: Wireless Apple CarPlay, Navigation, JBL Audio\r\nInterior: Power-adjustable sport seats, Head-Up Display (on some trims) \r\nOther Engine Option (2.0L)\r\nEngine: 2.0L Turbocharged Inline-4 (B48)\r\nPower: Around 255 HP, less weight than the 3.0L.', '0', 11000000.00, 13000000.00, 8800000.00, '23542558', 5, 1, 1410.00, '0', '', 1, '0', '0', 0.00, 0, 0, '2026-01-17 12:30:38', '2026-01-17 12:49:16'),
(64, 11, 13, NULL, 'Regular mobile ray Bluecut Glass', 'regular-mobile-ray-bluecut-glass', '', '0', 550.00, 0.00, 0.00, '13', 0, 5, 0.00, '0', '', 0, '0', '0', 0.00, 0, 0, '2026-01-17 12:31:29', '2026-01-17 12:36:22'),
(65, 7, 13, NULL, 'Biotin Supplements', 'biotin-supplements', 'Biotin supplements (Vitamin B7) support energy metabolism, brain function, and the health of hair, skin, and nails by aiding in the production of keratin, though scientific evidence for benefits in healthy individuals is limited; they\'re used to treat rare deficiencies, improve brittle nails, and are generally safe as excess is excreted, but high doses can interfere with lab tests, so consult a doctor before use.', '0', 4851.00, 6700.00, 4000.00, '25874', NULL, 5, 0.00, '0', '', 0, '0', 'FB DGF', 0.00, 0, 0, '2026-01-17 12:31:36', '2026-01-17 12:31:36'),
(66, 18, 13, NULL, 'Food', 'food', 'this is very fine', '0', 5000.00, 6500.00, 4500.00, '1', NULL, 5, 50.00, '0', '', 0, '0', 'bnp,jamat ,shibir', 0.00, 0, 0, '2026-01-17 12:32:50', '2026-01-17 12:32:50'),
(67, 8, 12, NULL, 'Sony XR-85X95L Bravia 85-Inch XR Series 4K Ultra HD Smart Google TV', 'sony-xr-85x95l-bravia-85-inch-xr-series-4k-ultra-hd-smart-google-tv', 'LIFELIKE PICTURE: The intelligent and powerful Cognitive Processor XR delivers a picture with wide dynamic contrast, detailed blacks, natural colors, and high peak brightness, replicating how we see the real world.\r\nMINI LED CONTRAST AND COLOR See ultimate contrast from thousands of Mini LEDs and billions of accurate colors, all precision-controlled by the XR Backlight Master Drive and XR Triluminos Pro.\r\nPremium Smart TV: Get access to all your favorite streaming apps in one place with Google TV, and simply use your voice to search and ask questions with Google Assistant. Supports Apple AirPlay.\r\nMOVIES ON US WITH BRAVIA CORE Enjoy streaming high-bitrate, high-quality 4K UHD movies included with the BRAVIA CORE app. Get 5 credits to redeem on latest-release movies and a 12-month subscription on hundreds of classics.\r\nALL YOUR GAME SETTINGS IN ONE PLACE Game Menu puts all your gaming picture settings and exclusive assist features in a single easy-to-manage interface.\r\nHDMI 2.1 Gaming: Get the advantage in high-performance gaming with HDMI 2.1 features like 4K/120, VRR, and ALLM.\r\nPERFECT FOR PLAYSTATION 5: Take your gaming to the next level with exclusive features like Auto HDR Tone Mapping and Auto Genre Picture Mode for optimized picture quality while gaming and streaming on your PS5 console.', '0', 649900.00, 1030900.00, 0.00, '64645464', NULL, 5, 0.00, '0', '', 1, '0', '', 0.00, 0, 0, '2026-01-17 12:32:52', '2026-01-17 12:32:52'),
(68, 11, 1, NULL, 'Digital Scale 10kg', 'digital-scale-10kg', '', '0', 300.00, 0.00, 0.00, '14', 0, 5, 0.00, '0', '', 0, '0', '0', 0.00, 0, 0, '2026-01-17 12:33:19', '2026-01-17 12:36:11'),
(70, 9, 13, NULL, 'Dot and key sunscreen', 'dot-and-key-sunscreen', 'DOT & KEY Vitamin C + E Super Bright Sunscreen SPF 50 | Water-Light, UVA/UVB & Blue Light Protection | For Even Toned & Glowing Skin | With Liquid Spf 50+++ | No White Cast | For All Skin Types | SPF 50+ water-light sunscreen for glowing, sun-protected skinn|Infused with Triple Vitamin C, Sicilian Blood Orange & UV Filters | Reduces dullness & dark spots caused by excessive sun exposure | Protect skin against damaging UVA, UVB & blue light rays | Water-light & quick-absorbing for dewy finish on all skin types with zero white cast.', '0', 1200.00, 1400.00, 1000.00, '111222', 0, 5, 100.00, '0', '', 0, '0', '0', 0.00, 0, 0, '2026-01-17 12:33:59', '2026-01-17 12:34:27'),
(72, 25, 11, NULL, 'Mug', 'mug', 'A mug is a type of cup, a drinking vessel usually intended for hot drinks such as coffee, hot chocolate, or tea. Mugs have handles and usually hold a larger amount of fluid than other types of cups such as teacups or coffee cups. Typically, a mug holds approximately 250–350 ml (8–12 US fl oz) of liquid.', '0', 1500.00, 1220.00, 0.00, '52', NULL, 5, 0.00, '0', '', 0, '0', '', 0.00, 0, 0, '2026-01-17 12:34:49', '2026-01-17 12:34:49'),
(74, 16, 18, NULL, 'Fulll', 'fulll', 'Natural fresh cut flowers supplier all over Bangladesh.', '0', 4050.00, 5500.00, 3400.00, '5874', NULL, 50, 0.00, '0', '', 1, '0', 'flewersservicebd,gift,birthdaygift,anniverserygift,valobasa,dhaka\'s florist.', 0.00, 0, 0, '2026-01-17 12:35:29', '2026-01-17 12:35:29'),
(75, 10, 3, NULL, 'Toyota Land Cruiser V8', 'toyota-land-cruiser-v8', 'Toyota Land Cruiser V8 specs vary by year and market, but generally feature powerful 4.5L twin-turbo diesel or 4.6L/5.7L petrol V8 engines, offering robust performance with around 270-300+ HP and significant torque (650 Nm for diesel), paired with 6-speed automatics, full-time 4WD, and luxury features like premium interiors, diff locks, and advanced tech for serious off-roading and comfort. \r\nEngine & Performance (Common Diesel Model - 4.5L D-4D)\r\nEngine: 4.5L (4461cc) Twin-Turbo V8 Diesel (1VD-FTV).\r\nPower: ~272 PS (200 kW / 268 bhp).\r\nTorque: ~650 Nm (479 lb-ft).\r\nTransmission: 6-speed automatic.\r\nDrivetrain: Full-time 4WD with a transfer case. \r\nEngine & Performance (Common Petrol Model - 4.6L)\r\nEngine: 4.6L (4608cc) V8 Petrol (1UR-FE).\r\nPower: ~304 HP.\r\nTorque: ~44.8 Kg-m (440 Nm).\r\nTransmission: 6-speed automatic. \r\nKey Features & Dimensions (Approximate)\r\nChassis: Body-on-frame construction, double-wishbone front, 4-link coil rear suspension.\r\nBrakes: Ventilated disc brakes front and rear.\r\nFuel Capacity: ~93 liters (plus optional sub-tank).\r\nGround Clearance: ~225 mm.\r\nTires: Options like 285/65R17 or 285/60R1', '0', 45000000.00, 50000000.00, 39000000.00, '25425587', NULL, 1, 2500.00, '0', '', 1, '0', 'toyota', 0.00, 0, 0, '2026-01-17 12:35:34', '2026-01-17 12:35:34'),
(77, 25, 11, NULL, 'korai', 'korai', 'A mug is a type of cup, a drinking vessel usually intended for hot drinks such as coffee, hot chocolate, or tea. Mugs have handles and usually hold a larger amount of fluid than other types of cups such as teacups or coffee cups. Typically, a mug holds approximately 250–350 ml (8–12 US fl oz) of liquid.', '0', 2500.00, 200.00, 0.00, '85', 0, 5, 5.00, '0', '', 0, '0', '0', 0.00, 0, 0, '2026-01-17 12:36:00', '2026-01-17 12:41:01'),
(79, 18, 1, NULL, 'Mobile', 'mobile', 'dfgdhsfgfsad', '0', 25000.00, 28000.00, 23000.00, '2', NULL, 5, 50.00, '0', '', 1, '0', 'BNP,JAmat,', 0.00, 0, 0, '2026-01-17 12:37:18', '2026-01-17 12:37:18'),
(80, 22, 12, NULL, 'Sony Bravia W654A 42-inch Full HD wifi Internet', 'sony-bravia-w654a-42-inch-full-hd-wifi-internet', 'Sony Bravia W654A 42-inch Full HD Edge LED  Internet enabled TV. The W65 is packed with technology to make everything you watch clearer, smoother and more full of life. It has some unexpected extras too, enabling you to seamlessly integrate your smartphone or tablet into the entertainment experience. The Sony Entertainment Network and stream content from an ever-expanding catalogue of HD movies, music and TV channels. It offers a web browser too, plus apps like Twitter, Facebook, YouTube and Skype.\r\nFEATURES\r\nScreen Size	42-inch, Aspect Ratio: 16 : 9\r\nResolution	Full HD, X-Reality PRO takes everything closer to Full HD\r\nTechnology	Screen Type: LCD, Backlight Type: LED, Dimming Type: Frame Dimming\r\nRefresh Rate	Motionflow XR 200Hz\r\nContrast	Dynamic Contrast Ratio: Over 1 million; Advanced Contrast Enhancer\r\nBrightness	Picture Quality: Face Area Detection, Video Area Detection, Super resolution for Game Mode. Live Colour Technology, Deep Colour, Intelligent MPEG Noise Reduction, 24p True Cinema\r\nSound	Bass Reflex Box Speaker, 8W+8W Audio power output, S-Force Front Surround 3D, S-Master, Dolby Digital / Dolby Digital Plus / Dolby Pulse\r\nConnectivity	Network Features: Wireless Screen Mirroring, TV Side View, Wireless LAN , Skype Ready, Sony Entertainment Network, Apps, Opera Web browser, DLNA. Input/output: RF in, HDMI In x2, SCART in (without Smartlink), USB Port, Ethernet in, Composite video in, Component video (Y/Pb/Pr) in, PCMCIA in, Analog audio in, Optical digital out, Audio out, Headphone out, Video Out (SCART)\r\nRemote	Yes\r\nDimension	Wall-mount your TV, no extra bracket needed - The TV\'s integrated stand doubles as a wall-mounting bracket, so there\'s no need to go out and buy one. TV without table-top stand 9.9 Kg\r\nOther Features	LightSensor, Viewing Angle (Right/Left): 178 (89/89), Viewing Angle (Up/Down): 178 (89/89), X-Reality PRO Picture processing engine, Power consumption (in operation): 50W in Home Mode and 95W in Shop Mode, Power saving modes, Dynamic backlight control, Backlight off mode, USB Play, USB HDD Recording, Panorama. Optional accessories: VC Camera and Micropohone, MHL Cable\r\nWarranty	5 Years Service Warranty', '0', 39999.00, 40500.00, 35000.00, '458', NULL, 5, 0.00, '0', '', 1, '0', 'Sony Television, television', 0.00, 0, 0, '2026-01-17 12:37:20', '2026-01-17 12:37:20'),
(81, 9, 13, NULL, 'livon hair serum', 'livon-hair-serum', 'Livon Hair Serum is a leave-in treatment designed to provide instant frizz control, enhanced shine, and improved manageability for all hair types. It features a lightweight, non-sticky formula that helps eliminate frizz and gives hair an ultra-glossy finish. Just a few drops can transform your hair, making it smooth and easy to style', '0', 500.00, 700.00, 450.00, '9765', NULL, 5, 50.00, '0', '', 0, '0', '', 0.00, 0, 0, '2026-01-17 12:37:37', '2026-01-17 12:37:37'),
(82, 10, 3, NULL, 'Toyota Land Cruiser Prado', 'toyota-land-cruiser-prado', 'The Toyota Land Cruiser Prado (J250 series) offers robust specs, featuring a standard 2.8L turbo-diesel engine (around 201 HP/500 Nm) with mild-hybrid tech, paired with an 8-speed automatic transmission and full-time 4WD, plus available 2.4L turbo-petrol options, seating for 7, a 3,500kg towing capacity, Toyota Safety Sense, and modern tech like wireless Apple CarPlay/Android Auto, built on a strong GA-F platform for serious off-road capability with advanced suspension systems. \r\nEngine & Performance (Typical Diesel)\r\nEngine: 2.8L 4-Cylinder Turbo Diesel (1GD-FTV)\r\nPower: ~150 kW (201 HP) @ 3400 rpm\r\nTorque: ~500 Nm @ 1600-2800 rpm\r\nTransmission: 8-speed Automatic\r\nDrive: Full-time 4WD with dual-range transfer case\r\nTowing Capacity: 3,500 kg (braked) \r\nDimensions & Capability\r\nPlatform: GA-F ladder frame chassis\r\nLength: Around 4920-4995 mm\r\nWidth: ~1980 mm\r\nHeight: ~1870-1950 mm (varies by trim)\r\nWheelbase: 2850 mm\r\nGround Clearance: ~210 mm\r\nApproach Angle: ~32 degrees\r\nFuel Capacity: Up to 110L (main + sub-tank) \r\nInterior & Technology (Varies by Trim) \r\nSeating: 7-seater capacity\r\nInfotainment: 12.3\" touchscreen with wireless Apple CarPlay & Android Auto\r\nSafety: Toyota Safety Sense (Pre-Collision System, Dynamic Radar Cruise Control, Lane Departure Alert, etc.)\r\nConvenience: Fabric or leather seats, heated/ventilated front seats, SmartKey entry, panoramic sunroof (on higher trims', '0', 22500000.00, 25000000.00, 17000000.00, '245875445', NULL, 2, 2330.00, '0', '', 1, '0', 'prado', 0.00, 0, 0, '2026-01-17 12:39:25', '2026-01-17 12:39:25'),
(83, 18, 13, NULL, 'Huny', 'huny', 'dfgjhgj', '0', 1100.00, 1200.00, 980.00, '3', NULL, 5, 50.00, '0', '', 1, '0', 'fghjkfdhgjkfdhg', 0.00, 0, 0, '2026-01-17 12:42:39', '2026-01-17 12:42:39'),
(84, 18, 16, NULL, 'Cloth', 'cloth', 'dsjfhjdskhfjksdghkjdsgh', '0', 1500.00, 1800.00, 1400.00, '4', NULL, 5, 1.00, '0', '', 1, '0', 'dhgjkfdhgkjfds', 0.00, 0, 0, '2026-01-17 12:46:05', '2026-01-17 12:46:05'),
(85, 18, 5, NULL, 'sdfgdjs', 'sdfgdjs', 'fdgfdsgdsfgdsf', '0', 450000.00, 4800000.00, 440000.00, '5', NULL, 5, 100.00, '0', '', 0, '0', '', 0.00, 0, 0, '2026-01-17 12:47:40', '2026-01-17 12:47:40'),
(86, 26, 2, NULL, 'mobile phone', 'mobile-phone', 'this is a nice phone in the world', '0', 500000.00, 500000.00, 14156.00, '41', NULL, 5, 50.00, '0', '', 1, '0', 'fb phone', 0.00, 0, 0, '2026-01-17 12:51:08', '2026-01-17 12:51:08'),
(87, 22, 16, NULL, 'Lamborghini SC20 Is A 760bhp Sports Car', 'lamborghini-sc20-is-a-760bhp-sports-car', 'For all the fear-mongering about cars becoming soulless transportation boxes, enthusiasts still have a lot to look forward to in 2025. This year is already proving to be one of the biggest in recent memory for debuts of new sports cars and supercars.\r\n\r\nWith the latest technology injecting its way into the realm of performance, you’ll find far more hybrids and electric cars on this list than, say, five years ago. But don’t think that’s a bad thing. Our favorite car of 2024 was a hybrid, and one of the best performance vehicles on the market right now is an all-electric hot hatch. With that in mind, here\'s what you can look forward to this year.', '0', 8000000.00, 8500000.00, 7500000.00, '86786541', NULL, 5, 250.00, '0', '', 1, '0', 'sports car, car, sports', 0.00, 0, 0, '2026-01-17 12:54:05', '2026-01-17 12:54:05'),
(88, 28, 13, NULL, 'Vitamin B Complex', 'vitamin-b-complex', 'Vitamin B Complex, especially B1 (Thiamine), B6 (Pyridoxine), and B12 (Cobalamin), are crucial water-soluble vitamins that support nerve health, energy metabolism, red blood cell formation, and overall nervous system function, often working together to relieve symptoms like tingling, numbness, and weakness, and found in foods like meat, dairy, and fortified cereals. Deficiencies can lead to neurological issues like neuropathy, making supplementation important for at-risk groups, though high doses should be discussed with a docto', '0', 350.00, 370.00, 120.00, '12457541', NULL, 100, 0.00, '0', '', 1, '0', 'vitamin', 0.00, 0, 0, '2026-01-17 12:55:49', '2026-01-17 12:55:49');

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
(69, 88, 'assets/uploads/product/1768654549_5738_0.jpg', '0', 0, 1);

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
(15, 'neurocare@gmail.com', 'subscribed', '2026-01-17 12:51:07');

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` enum('string','number','boolean','json') DEFAULT 'string',
  `description` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(19, 'tuhin islam', 'tuhinislam@gmail.com', '$2y$10$mO/5qCxSENuoNTRDxyslMO8HX3yqHJk8RVoABKFnev//I/2BjhfRe', '01781058284', 'customer', 0, 'active', '2026-01-17 12:17:27', '2026-01-17 12:17:27'),
(20, 'last vendor', 'vendorlast@gmail.com', '$2y$10$mfWG8rklW5BcLa/5nIw85uqUbAarqRbjQwnWkvgC.jw5ysoVzRHJO', '01715258459', 'customer', 0, 'active', '2026-01-17 12:18:32', '2026-01-17 12:18:32'),
(21, 'MD JAHIDUL ISLAM', 'abc@gmail.com', '$2y$10$RHJM4/Tla..TONyY9PZKBuuzbjwsmu8ZIFHUjiGtO0wjYzKEx5lma', '01575428014', 'customer', 0, 'active', '2026-01-17 12:20:39', '2026-01-17 12:20:39'),
(22, 'seller last', 'seller@gmail.com', '$2y$10$DPOvkOO7jQOeZItAIdvOUOHQwO.SOz6eKKdnCxy2mVoNZObeQLyMu', '01715258459', 'vendor', 0, 'active', '2026-01-17 12:20:44', '2026-01-17 12:20:44'),
(23, 'Rayhan Khan', 'alauddin1@gmail.com', '$2y$10$kALpm3/7Cea10OvAC5VzMOb2YB.aszmzc.4LWGeliKBrB/2wngmz.', '123456789', 'customer', 0, 'active', '2026-01-17 12:20:44', '2026-01-17 12:20:44'),
(24, 'MD JAHIDUL ISLAM', 'mm9330414@gmail.com', '$2y$10$RHkxn.VbkCjIVj0PXiFUr.mBVEAI8iNf7FdZpLhepoXj.5cst72sG', '01701304686', 'vendor', 0, 'active', '2026-01-17 12:22:45', '2026-01-17 12:22:45'),
(25, 'Jahidul Islam', 'rr@gmail.com', '$2y$10$Vj8a.c9Ixh0RHx.pYHqtguYqQMqHnx7HpKIZIYX/Eye/Zwwhw2EFO', '01682552884', 'vendor', 0, 'active', '2026-01-17 12:29:53', '2026-01-17 12:29:53'),
(26, 'tuhin islam', 'tuhinislam8@gmail.com', '$2y$10$nRfTlV5aQcH7uRvYr3JoM.C3QN.abvvIUgD4xoW2Xi1wpm8D9hSfG', '01781058284', 'vendor', 0, 'active', '2026-01-17 12:40:25', '2026-01-17 12:40:25'),
(27, 'Tuhin  islam', 'tuhin@gmail.com', '$2y$10$Px/4r9g0WQGlyu5LLvhiqu5yzWy.memhBb7qCkzlUODOBIFqvb7qu', '01781058284', 'vendor', 0, 'active', '2026-01-17 12:40:43', '2026-01-17 12:40:43'),
(28, 'Random Alavola', 'neurocare@gmail.com', '$2y$10$mH85tr.QxkOvhMSZXYsGquxYN6djjXWLRVxaXzClqIgyvW/0Lc9iG', '01909045169', 'vendor', 0, 'active', '2026-01-17 12:51:07', '2026-01-17 12:51:07');

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
(1, 1, 'Jhanaka Family Store', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.', '1_logo.jpg', '1_banner.png', 'Shop No# 531, Shyamoli Square Shopping Mall, (Level-5), mohammadpur Dhaka', '436546546546546', '4567567567657657', 'Md Jhakanaka', '4565676787689879', 'ABC Bank Ltd', 'pending', 10.00, 0.00, 0.00, '2026-01-17 11:20:54'),
(2, 7, 'Tak Somadhan', '৪টি পদ্ধতিতে হেয়ার রিপ্লেসমেন্ট করানো হয়।                                                    \r\n👍উইভিং সিস্টেম (ফিক্সড)\r\n👍 ক্লিপ সিস্টেম (লক)\r\n👍 আঠা বা গ্লু সিস্টেম\r\n👍 টেপ সিস্টেম।    \r\nBogura Hair Center  দিচ্ছে সর্বোচ্চ  সেরা সার্ভিস দানের নিশ্চয়তা, ট্রেনিং প্রাপ্ত অভিজ্ঞ টেকনিক্যাল টিম দ্বারা  সার্বক্ষণিক সেবা প্রদান করা হয়। \r\nতাই আজই চলে আসুন আমাদের অফিসে। 🤝🤝🤝\r\n🚖 পার্ক রোড (সেলিনা সাইকেল স্টোর এর তৃতীয় তলায় )সাতমাথা, বগুড়া। \r\nযোগাযোগঃ\r\n👇👇👇\r\n📞+8801710987791', '7_logo.jpg', '7_banner.jpg', 'Kazipara', '12369', '789632145', 'Tack Somadhan', '8525741963', 'Islami Bank ltd', 'pending', 10.00, 0.00, 0.00, '2026-01-17 11:58:31'),
(3, 18, 'Bogura Bazar Family store', 'there are all item here', '18_logo.png', '18_banner.png', 'Bogura', '1223', '45646546546', 'abdulla', '45646546464', 'abdulla', 'pending', 10.00, 0.00, 0.00, '2026-01-17 12:24:14'),
(4, 27, 'tuhin cosmatics store', 'dgsdgsdh', '27_logo.jpg', '27_banner.jpg', 'dhaka', '', '', '', '', '', 'pending', 10.00, 0.00, 0.00, '2026-01-17 12:42:16'),
(5, 22, 'seller last', 'Hi, there! from seller last shop here you will find everything.', '22_logo.jpg', '22_banner.jpg', '', 'ashjkanf12358', '23465432123', 'last seller', '012358985675', 'LALA Bank LTD', 'pending', 10.00, 0.00, 0.00, '2026-01-17 12:43:57'),
(6, 25, 'jahid crocaris', 'Ramgonj, Luxmipur', '25_logo.jpg', '25_banner.png', 'Zia Shophing Complex', '', '', '', '', '', 'pending', 10.00, 0.00, 0.00, '2026-01-17 12:44:16'),
(7, 10, 'SP Motors', '\"SP Motors\"  offers luxury, sports, and performance cars, offering sales, finance, and trade-ins, with a focus on high-end brands like BMW, Audi, and Mercedes', '10_logo.png', '10_banner.jpg', '151/A, Jafrabad Buddhijibi (Baribad), Mohammadpur, Dhaka-1207', '45452542185', '39887544857', 'Patwary', '1020304050', 'ABC Bank', 'pending', 10.00, 0.00, 0.00, '2026-01-17 12:48:04'),
(8, 8, 'tanebaen shop', 'all faiyazlami', '8_logo.png', '8_banner.png', 'Kazipara ,Mirpur, Dhaka', '54654984', '33036856464', 'Asad khan', '125897469745', 'City Bank', 'pending', 10.00, 0.00, 0.00, '2026-01-17 12:48:55'),
(9, 26, 'free sales store', 'free seller store', NULL, NULL, 'annadanagar,pirgacha,rangpur', '254545454547', '744654654', 'tuhinislam', '6134654321654', 'tuhin bank', 'pending', 10.00, 0.00, 0.00, '2026-01-17 12:55:09');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subscribers`
--
ALTER TABLE `subscribers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `vendor_profiles`
--
ALTER TABLE `vendor_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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
