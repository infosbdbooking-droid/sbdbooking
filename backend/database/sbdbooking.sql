-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 18, 2025 at 01:13 PM
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
-- Database: `sbdbooking`
--

-- --------------------------------------------------------

--
-- Table structure for table `car`
--

CREATE TABLE `car` (
  `id` int(10) UNSIGNED NOT NULL,
  `car_seats` int(11) NOT NULL,
  `car_photos` varchar(255) DEFAULT NULL,
  `car_ac` varchar(100) DEFAULT NULL,
  `car_type_id` int(10) UNSIGNED NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `car`
--

INSERT INTO `car` (`id`, `car_seats`, `car_photos`, `car_ac`, `car_type_id`, `status`, `created_at`, `updated_at`) VALUES
(4, 13, 'car-684ee2a087136.jpeg', '1', 8, 0, '2025-06-15 09:37:59', '2025-08-19 07:41:25');

-- --------------------------------------------------------

--
-- Table structure for table `car_type`
--

CREATE TABLE `car_type` (
  `id` int(10) UNSIGNED NOT NULL,
  `car_type` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `car_type`
--

INSERT INTO `car_type` (`id`, `car_type`, `status`, `created_at`, `updated_at`) VALUES
(5, 'Amaze', 0, '2025-06-08 09:00:59', '2025-06-15 08:40:27'),
(6, 'Bolero', 1, '2025-06-08 09:01:05', '2025-06-08 09:11:52'),
(7, 'Carens', 1, '2025-06-08 09:01:13', '2025-06-08 09:11:50'),
(8, 'Ertiga', 1, '2025-06-08 09:01:20', '2025-06-08 09:11:48'),
(9, 'Force', 1, '2025-06-08 09:01:27', '2025-06-08 09:11:45'),
(10, 'Maruti', 1, '2025-06-08 09:02:00', '2025-06-08 09:11:43');

-- --------------------------------------------------------

--
-- Table structure for table `charges_type`
--

CREATE TABLE `charges_type` (
  `id` int(10) UNSIGNED NOT NULL,
  `charges_type` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `charges_type`
--

INSERT INTO `charges_type` (`id`, `charges_type`, `status`, `created_at`, `updated_at`) VALUES
(3, 'Night Charges', 1, '2025-06-08 08:25:33', '2025-06-08 08:25:33'),
(4, 'Day Charges', 1, '2025-06-08 08:25:47', '2025-06-08 08:57:02');

-- --------------------------------------------------------

--
-- Table structure for table `login_logs`
--

CREATE TABLE `login_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `logged_in_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_logs`
--

INSERT INTO `login_logs` (`id`, `user_id`, `ip_address`, `logged_in_at`, `created_at`, `updated_at`) VALUES
(1, 1, '127.0.0.1', '2025-06-08 07:19:33', '2025-06-08 07:19:33', '2025-06-08 07:19:33'),
(2, 1, '127.0.0.1', '2025-06-08 07:21:20', '2025-06-08 07:21:20', '2025-06-08 07:21:20'),
(3, 1, '127.0.0.1', '2025-06-08 07:22:33', '2025-06-08 07:22:33', '2025-06-08 07:22:33'),
(4, 1, '127.0.0.1', '2025-06-08 08:32:59', '2025-06-08 08:32:59', '2025-06-08 08:32:59'),
(5, 1, '127.0.0.1', '2025-06-08 08:34:43', '2025-06-08 08:34:43', '2025-06-08 08:34:43'),
(6, 9, '127.0.0.1', '2025-06-08 08:35:33', '2025-06-08 08:35:33', '2025-06-08 08:35:33'),
(7, 1, '127.0.0.1', '2025-06-08 08:40:18', '2025-06-08 08:40:18', '2025-06-08 08:40:18'),
(8, 1, '127.0.0.1', '2025-06-08 08:43:08', '2025-06-08 08:43:08', '2025-06-08 08:43:08'),
(9, 1, '127.0.0.1', '2025-06-08 08:43:58', '2025-06-08 08:43:58', '2025-06-08 08:43:58'),
(10, 1, '127.0.0.1', '2025-06-08 08:51:18', '2025-06-08 08:51:18', '2025-06-08 08:51:18'),
(11, 1, '127.0.0.1', '2025-06-08 08:54:51', '2025-06-08 08:54:51', '2025-06-08 08:54:51'),
(12, 1, '127.0.0.1', '2025-06-08 08:56:46', '2025-06-08 08:56:46', '2025-06-08 08:56:46'),
(13, 1, '127.0.0.1', '2025-06-08 08:58:07', '2025-06-08 08:58:07', '2025-06-08 08:58:07'),
(14, 1, '127.0.0.1', '2025-06-08 09:00:36', '2025-06-08 09:00:36', '2025-06-08 09:00:36'),
(15, 9, '127.0.0.1', '2025-06-08 09:01:47', '2025-06-08 09:01:47', '2025-06-08 09:01:47'),
(16, 1, '127.0.0.1', '2025-06-08 09:30:06', '2025-06-08 09:30:06', '2025-06-08 09:30:06'),
(17, 9, '127.0.0.1', '2025-06-08 09:34:43', '2025-06-08 09:34:43', '2025-06-08 09:34:43'),
(18, 1, '127.0.0.1', '2025-06-08 12:09:38', '2025-06-08 12:09:38', '2025-06-08 12:09:38'),
(19, 1, '127.0.0.1', '2025-06-08 12:21:09', '2025-06-08 12:21:09', '2025-06-08 12:21:09'),
(20, 1, '127.0.0.1', '2025-06-08 14:25:51', '2025-06-08 14:25:51', '2025-06-08 14:25:51'),
(21, 1, '127.0.0.1', '2025-06-08 14:55:51', '2025-06-08 14:55:51', '2025-06-08 14:55:51'),
(22, 1, '127.0.0.1', '2025-06-08 14:56:54', '2025-06-08 14:56:54', '2025-06-08 14:56:54'),
(23, 1, '127.0.0.1', '2025-06-08 15:20:43', '2025-06-08 15:20:43', '2025-06-08 15:20:43'),
(24, 9, '127.0.0.1', '2025-06-08 15:24:18', '2025-06-08 15:24:18', '2025-06-08 15:24:18'),
(25, 9, '127.0.0.1', '2025-06-08 15:26:10', '2025-06-08 15:26:10', '2025-06-08 15:26:10'),
(26, 1, '127.0.0.1', '2025-06-15 13:58:37', '2025-06-15 13:58:37', '2025-06-15 13:58:37'),
(27, 11, '127.0.0.1', '2025-06-15 14:09:03', '2025-06-15 14:09:03', '2025-06-15 14:09:03'),
(28, 1, '127.0.0.1', '2025-06-15 14:10:08', '2025-06-15 14:10:08', '2025-06-15 14:10:08'),
(29, 1, '127.0.0.1', '2025-06-15 14:34:56', '2025-06-15 14:34:56', '2025-06-15 14:34:56'),
(30, 1, '127.0.0.1', '2025-06-16 06:51:12', '2025-06-16 06:51:12', '2025-06-16 06:51:12'),
(31, 1, '127.0.0.1', '2025-08-19 13:07:54', '2025-08-19 13:07:54', '2025-08-19 13:07:54');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_number` varchar(255) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `category` varchar(255) NOT NULL,
  `sub_category` varchar(255) NOT NULL,
  `service_type` varchar(255) NOT NULL,
  `service_frequency` varchar(255) NOT NULL,
  `no_of_services` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `scheduled_day` varchar(255) NOT NULL,
  `price` varchar(191) NOT NULL,
  `tax` varchar(191) DEFAULT NULL,
  `order_total` varchar(191) NOT NULL,
  `discount_amount` varchar(255) DEFAULT NULL,
  `site_request` text DEFAULT NULL,
  `service_center_type` varchar(255) DEFAULT NULL,
  `employee_name` varchar(255) DEFAULT NULL,
  `billing` varchar(255) DEFAULT NULL,
  `business_region` varchar(255) DEFAULT NULL,
  `business_sub_region` varchar(255) DEFAULT NULL,
  `branch_codes` varchar(255) DEFAULT NULL,
  `customer_type` varchar(255) DEFAULT NULL,
  `business_lead` tinyint(1) NOT NULL DEFAULT 0,
  `mobile_number` varchar(50) DEFAULT NULL,
  `customer_legal_name` varchar(255) DEFAULT NULL,
  `customer_trade_name` varchar(255) DEFAULT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `landmark` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `pincode` varchar(20) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `phone_1` varchar(50) DEFAULT NULL,
  `phone_2` varchar(50) DEFAULT NULL,
  `email_1` varchar(255) DEFAULT NULL,
  `email_2` varchar(255) DEFAULT NULL,
  `gstnNum` varchar(50) DEFAULT NULL,
  `others` text DEFAULT NULL,
  `clatlon` varchar(50) DEFAULT NULL,
  `bill_customer_legal_name` varchar(255) DEFAULT NULL,
  `bill_customer_trade_name` varchar(255) DEFAULT NULL,
  `bill_phone` varchar(50) DEFAULT NULL,
  `bill_email` varchar(255) DEFAULT NULL,
  `bill_address` text DEFAULT NULL,
  `bill_city` varchar(255) DEFAULT NULL,
  `bill_pincode` varchar(20) DEFAULT NULL,
  `bill_landmark` varchar(255) DEFAULT NULL,
  `bill_country` varchar(100) DEFAULT NULL,
  `audit_requirement` tinyint(1) NOT NULL DEFAULT 0,
  `desired_date` varchar(50) NOT NULL,
  `desired_time` varchar(50) NOT NULL,
  `order_status` int(11) NOT NULL DEFAULT 1 COMMENT '\r\n1-Scheduled,2-Dispatched,3-OnSite,4-Completed,5-Incomplete\r\n',
  `sez` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `title`, `created_at`, `updated_at`, `deleted_at`) VALUES
(133, 'dashboard', '2025-05-07 00:54:29', '2025-05-07 00:54:29', NULL),
(138, 'manage_orders', '2025-06-01 08:16:39', '2025-06-01 08:16:39', NULL),
(139, 'km_price', '2025-06-01 08:16:52', '2025-06-01 08:16:52', NULL),
(140, 'charges', '2025-06-01 08:17:48', '2025-06-01 08:17:48', NULL),
(141, 'settings', '2025-06-01 08:18:06', '2025-06-01 08:18:06', NULL),
(142, 'access', '2025-06-01 08:18:14', '2025-06-01 08:18:14', NULL),
(143, 'charges_type', '2025-06-08 08:54:53', '2025-06-08 08:54:53', NULL),
(144, 'car_type', '2025-06-08 08:55:03', '2025-06-08 08:55:03', NULL),
(145, 'car', '2025-06-08 09:22:10', '2025-06-08 09:22:10', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `permission_role`
--

CREATE TABLE `permission_role` (
  `role_id` int(10) UNSIGNED NOT NULL,
  `permission_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permission_role`
--

INSERT INTO `permission_role` (`role_id`, `permission_id`) VALUES
(1, 133),
(1, 138),
(13, 133),
(1, 139),
(1, 140),
(1, 141),
(1, 142),
(13, 138),
(13, 139),
(13, 140),
(13, 141),
(1, 143),
(1, 144),
(1, 145),
(14, 145),
(14, 144);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `title`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Super Admin', '2019-04-15 13:43:32', '2025-06-08 03:56:22', NULL),
(13, 'Admin', '2025-06-08 03:31:13', '2025-06-08 04:03:39', NULL),
(14, 'Driver', '2025-06-08 09:46:47', '2025-06-08 09:46:47', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `service_frequency`
--

CREATE TABLE `service_frequency` (
  `id` int(11) NOT NULL,
  `service_type_id` int(11) NOT NULL,
  `service_frequency` varchar(255) NOT NULL,
  `no_of_services` int(11) DEFAULT NULL,
  `scheduled_day` int(11) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `service_type`
--

CREATE TABLE `service_type` (
  `id` int(11) NOT NULL,
  `service_type` varchar(255) NOT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service_type`
--

INSERT INTO `service_type` (`id`, `service_type`, `status`, `created_at`, `updated_at`) VALUES
(15, '10', 1, '2025-06-15 15:49:35', '2025-06-15 15:49:35');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `firebase_key` text DEFAULT NULL,
  `currency` varchar(191) DEFAULT NULL,
  `logo` varchar(191) DEFAULT NULL,
  `copyright` text DEFAULT NULL,
  `address` text DEFAULT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `email` text DEFAULT NULL,
  `facebook` text DEFAULT NULL,
  `twitter` text DEFAULT NULL,
  `instagram` text DEFAULT NULL,
  `linkedin` text DEFAULT NULL,
  `youtube` text DEFAULT NULL,
  `faqs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`faqs`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `firebase_key`, `currency`, `logo`, `copyright`, `address`, `contact`, `email`, `facebook`, `twitter`, `instagram`, `linkedin`, `youtube`, `faqs`, `created_at`, `updated_at`) VALUES
(1, 'test', '$', 'logo-683c574e2619b.jpg', 'Copyright ©️  SBDBOOKING PRIVATE LIMITED. All Rights Reserved.', 'jamuna', '+91-6363865658', 'reach@test.com', 'https://www.test', 'https://www.test', 'https://www.test', 'https://www.test', 'https://www.youtube.com/@hommlie', '[{\"answer\": \"Hommlie offers top-rated pest control services in Bangalore, specializing in cockroach control, termite treatment, rodent removal, and bed bug extermination using eco-friendly and effective solutions.\", \"question\": \"What are the best pest control services in Bangalore?\"}, {\"answer\": \"The best way to eliminate cockroaches permanently is through professional gel treatment and cockroach traps, which target hidden nests and prevent re-infestation.\", \"question\": \"How can I permanently remove cockroaches from my home?\"}, {\"answer\": \"Our bed bug control service uses advanced heat treatment and safe chemical sprays to completely eradicate bed bugs and prevent their return.\", \"question\": \"What is the best treatment for bed bugs?\"}, {\"answer\": \"Termites cause severe structural damage, and our anti-termite treatment in Bangalore provides long-lasting protection with chemical barriers and wood treatment solutions.\", \"question\": \"Why is termite treatment necessary?\"}, {\"answer\": \"For a clean and germ-free home, it is recommended to get a deep home cleaning service every 3-6 months, including kitchen, bathroom, and full-house sanitization.\", \"question\": \"How often should I book a deep home cleaning service?\"}, {\"answer\": \"A mosquito mesh blocks mosquitoes and insects while allowing fresh air in, offering a cost-effective and long-term mosquito control solution for homes.\", \"question\": \"How does a mosquito mesh help in pest control?\"}, {\"answer\": \"Our kitchen cleaning service removes oil stains, bacteria, and dirt, ensuring a spotless kitchen with chimney cleaning, floor scrubbing, and appliance sanitization.\", \"question\": \"What does a kitchen deep cleaning service include?\"}, {\"answer\": \"Rodents spread diseases and damage property; our rodent control service uses trapping, baiting, and exclusion techniques to keep your space rodent-free.\", \"question\": \"How can I control rodents in my home or office?\"}, {\"answer\": \"Prevent mosquitoes by eliminating stagnant water, using mosquito mesh, and booking our fogging and larvicide treatment for long-term protection.\", \"question\": \"How do I prevent mosquitoes at home?\"}, {\"answer\": \"You can call, WhatsApp, or visit our website to schedule your service instantly.\\r\\n\\r\\n📞 Call Now: +91 63638 65658\\r\\n🌐 Book Online: www.hommlie.com\", \"question\": \"How do I book a pest control or home cleaning service with Hommlie?\"}]', NULL, '2025-06-08 00:26:35');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role_id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Admin', 'surajgupta3118@gmail.com', NULL, '$2y$12$JtezO6ClqO1pLMLjOJ5MAu2fepvwHEAOo1izgLoSQx6UN9P7JK/sG', NULL, 1, '2025-05-03 05:17:39', '2025-06-08 01:52:01'),
(9, 14, 'Ukil Gupta', 'test789@gmail.com', NULL, '123456', NULL, 1, '2025-06-01 12:26:40', '2025-06-08 09:51:34'),
(11, 14, 'Driver', 'sujal@gmail.com', NULL, '$2y$12$Mg0HhlN7TFzuNJ7qWQ7dKean0vGCMCtzgfeO13iGrxdcHm3/b4MIS', NULL, 1, '2025-06-15 08:38:11', '2025-06-15 08:38:11');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `car`
--
ALTER TABLE `car`
  ADD PRIMARY KEY (`id`),
  ADD KEY `car_type_id` (`car_type_id`);

--
-- Indexes for table `car_type`
--
ALTER TABLE `car_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `charges_type`
--
ALTER TABLE `charges_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `login_logs`
--
ALTER TABLE `login_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `permission_role`
--
ALTER TABLE `permission_role`
  ADD KEY `permission_role_role_id_foreign` (`role_id`),
  ADD KEY `permission_role_permission_id_foreign` (`permission_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `service_frequency`
--
ALTER TABLE `service_frequency`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_type_id` (`service_type_id`);

--
-- Indexes for table `service_type`
--
ALTER TABLE `service_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `fk_users_role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `car`
--
ALTER TABLE `car`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `car_type`
--
ALTER TABLE `car_type`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `charges_type`
--
ALTER TABLE `charges_type`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `login_logs`
--
ALTER TABLE `login_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=146;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `service_frequency`
--
ALTER TABLE `service_frequency`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `service_type`
--
ALTER TABLE `service_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `car`
--
ALTER TABLE `car`
  ADD CONSTRAINT `car_ibfk_1` FOREIGN KEY (`car_type_id`) REFERENCES `car_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `login_logs`
--
ALTER TABLE `login_logs`
  ADD CONSTRAINT `login_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `permission_role`
--
ALTER TABLE `permission_role`
  ADD CONSTRAINT `permission_role_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`),
  ADD CONSTRAINT `permission_role_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

--
-- Constraints for table `service_frequency`
--
ALTER TABLE `service_frequency`
  ADD CONSTRAINT `service_frequency_ibfk_1` FOREIGN KEY (`service_type_id`) REFERENCES `service_type` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_role_id` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
