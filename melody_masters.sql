-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 28, 2026 at 03:56 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `melody_masters`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(50) NOT NULL,
  `parent_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`, `parent_id`) VALUES
(1, 'Guitars', NULL),
(2, 'Pianos', NULL),
(3, 'Drums', NULL),
(4, 'Violins', NULL),
(5, 'Accessories', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`id`, `name`, `email`, `subject`, `message`, `submitted_at`) VALUES
(1, 'sanduni', 'sandu@gmail.com', 'About goods', 'Nice and quality goods', '2026-01-21 13:45:03');

-- --------------------------------------------------------

--
-- Table structure for table `digital_downloads`
--

CREATE TABLE `digital_downloads` (
  `download_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `download_count` int(11) DEFAULT 0,
  `max_limit` int(11) DEFAULT 5,
  `expiry_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `digital_downloads`
--

INSERT INTO `digital_downloads` (`download_id`, `order_id`, `product_id`, `user_id`, `download_count`, `max_limit`, `expiry_date`) VALUES
(2, 18, 7, 12, 0, 5, '2026-01-29 16:26:10'),
(3, 19, 7, 12, 0, 5, '2026-01-29 16:26:14'),
(4, 20, 8, 12, 0, 5, '2026-01-29 16:31:49'),
(5, 21, 9, 12, 1, 5, '2026-01-29 16:40:11'),
(6, 23, 21, 18, 1, 5, '2026-03-07 15:52:26');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_amount` decimal(10,2) NOT NULL,
  `shipping_cost` decimal(10,2) DEFAULT 0.00,
  `order_status` enum('Pending','Paid','Shipped','Delivered') DEFAULT 'Pending',
  `shipping_address` text DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `order_date`, `total_amount`, `shipping_cost`, `order_status`, `shipping_address`, `payment_method`) VALUES
(1, 2, '2026-01-17 16:28:51', 10000.00, 0.00, 'Pending', NULL, NULL),
(2, 1, '2026-01-17 16:35:14', 5000.00, 0.00, 'Pending', NULL, NULL),
(3, 2, '2026-01-17 13:13:26', 5000.00, 0.00, 'Pending', NULL, NULL),
(4, 12, '2026-01-21 13:51:49', 5000.00, 500.00, 'Pending', NULL, NULL),
(5, 12, '2026-01-21 14:09:21', 15000.00, 0.00, 'Pending', NULL, NULL),
(6, 12, '2026-01-21 14:36:16', 15000.00, 0.00, 'Pending', NULL, NULL),
(7, 12, '2026-01-21 14:54:10', 10000.00, 500.00, 'Pending', NULL, NULL),
(8, 12, '2026-01-21 14:55:41', 5000.00, 500.00, 'Pending', NULL, NULL),
(9, 12, '2026-01-21 14:56:55', 5000.00, 500.00, 'Pending', NULL, NULL),
(10, 12, '2026-01-21 14:58:15', 5000.00, 500.00, 'Pending', NULL, NULL),
(11, 12, '2026-01-21 15:00:18', 15000.00, 0.00, 'Shipped', 'malabe', NULL),
(12, 12, '2026-01-21 15:34:52', 5500.00, 500.00, 'Delivered', 'matara, sri lanka', 'Credit/Debit Card'),
(13, 13, '2026-01-21 17:11:49', 15000.00, 0.00, 'Pending', 'malabe', 'Cash on Delivery'),
(14, 12, '2026-01-22 14:26:55', 5500.00, 500.00, 'Delivered', 'malabe, colombo', 'Cash on Delivery'),
(15, 12, '2026-01-22 14:38:49', 8000.00, 500.00, 'Delivered', 'gampaha', 'Cash on Delivery'),
(16, 11, '2026-01-22 14:44:38', 15000.00, 0.00, 'Delivered', 'galle', 'Cash on Delivery'),
(17, 12, '2026-01-22 14:46:22', 5500.00, 500.00, 'Delivered', 'colombo', 'Cash on Delivery'),
(18, 12, '2026-01-22 15:18:48', 1000.00, 500.00, 'Delivered', 'colombo', 'Cash on Delivery'),
(19, 12, '2026-01-22 15:26:00', 1000.00, 500.00, 'Delivered', 'colombo', 'Cash on Delivery'),
(20, 12, '2026-01-22 15:31:17', 1500.00, 500.00, 'Delivered', 'matara', 'Cash on Delivery'),
(21, 12, '2026-01-22 15:39:32', 8500.00, 500.00, 'Shipped', 'malabe', 'Cash on Delivery'),
(22, 18, '2026-02-27 17:09:34', 500000.00, 0.00, 'Delivered', 'no 473/4, malabe', 'Credit/Debit Card'),
(23, 18, '2026-02-28 14:51:26', 11000.00, 500.00, 'Delivered', '74/0, waliwita road, malabe', 'Credit/Debit Card');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `product_id`, `product_name`, `price`, `quantity`) VALUES
(1, 7, NULL, '', 5000.00, 2),
(2, 8, NULL, '', 5000.00, 1),
(3, 9, NULL, 'Guitar', 5000.00, 1),
(4, 10, NULL, 'Guitar', 5000.00, 1),
(5, 11, NULL, 'Guitar', 5000.00, 3),
(6, 12, NULL, 'Guitar', 5000.00, 1),
(7, 13, NULL, 'Guitar', 5000.00, 3),
(8, 14, NULL, 'Guitar', 5000.00, 1),
(9, 15, NULL, 'scsdvd erger', 7500.00, 1),
(10, 16, 5, 'scsdvd erger', 7500.00, 2),
(11, 17, 2, 'Guitar', 5000.00, 1),
(12, 18, 7, 'eg', 500.00, 1),
(13, 19, 7, 'eg', 500.00, 1),
(14, 20, 8, 'bfbdbkgjvrtogjvro njn,ie', 1000.00, 1),
(15, 21, 9, 'sddfwsdfb fvsdf', 8000.00, 1),
(16, 22, 10, 'Stratocaster Player Series', 250000.00, 2),
(17, 23, 21, 'Drum Backing Tracks Pack (MP3)', 3500.00, 3);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `product_name` varchar(100) NOT NULL,
  `brand` varchar(50) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock_quantity` int(11) NOT NULL,
  `product_image` varchar(255) DEFAULT NULL,
  `download_file` varchar(255) DEFAULT NULL,
  `specifications` text DEFAULT NULL,
  `product_type` enum('Physical','Digital') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `category_id`, `product_name`, `brand`, `price`, `stock_quantity`, `product_image`, `download_file`, `specifications`, `product_type`) VALUES
(10, 1, 'Stratocaster Player Series', 'Fender', 255000.00, 5, '1772212082_g.jpg', '', 'Electric guitar, Alder body, Maple neck, 3× Single-coil pickups, 22 frets', 'Physical'),
(11, 3, 'Pearl Export 5-Piece Drum Set', 'Pearl', 185000.00, 10, '1772288896_EXX725SPC778_hqw_3993x2958.webp', '', '5-piece drum kit\r\n22\" Bass Drum\r\nMaple shell construction\r\nIncludes hardware pack\r\nColor: Jet Black', 'Physical'),
(12, 3, 'Yamaha Stage Custom Birch Drum Kit', 'Yamaha ', 240000.00, 3, '1772288989_yamaha-stage-custom-birch-5-piece-shell-kit-deep-blue-sunburst-sbx0f56dbs-new-drum-kit-yamaha-894620.webp', '', '100% Birch Shell\r\n20\" Bass Drum\r\nAdvanced lightweight hardware\r\nStudio & Live performance ready', 'Physical'),
(13, 1, 'Yamaha F280 Acoustic Guitar', 'Yamaha ', 45000.00, 10, '1772289078_VCW6810.jpg', '', 'Spruce Top\r\nRosewood Fingerboard\r\nFull-size Dreadnought\r\nPerfect for beginners', 'Physical'),
(14, 2, 'Yamaha P-45 Digital Piano', 'Yamaha ', 210000.00, 6, '1772289156_yamaha-p45-piano.jpg', '', '88 Weighted Keys\r\nGraded Hammer Standard\r\n10 Voices\r\nUSB to Host\r\nIncludes Sustain Pedal', 'Physical'),
(15, 2, 'Casio CT-X700 Portable Keyboard', 'Casio ', 78000.00, 8, '1772289256_casio_ctx_700_value_bundle_kit_1389987.jpg', '', '61 Keys\r\n600 Tones\r\n195 Rhythms\r\nBuilt-in Speakers\r\nLightweight Design', 'Physical'),
(16, 4, 'Stentor Student II Violin 4/4', 'Stentor', 38000.00, 7, '1772289307_Stentor-Student-II-Violin_21c59c76-e9d7-4d53-97e6-eac40e31379a_1024x1024.webp', '', 'Solid Spruce Top\r\nEbony Fingerboard\r\nIncludes Bow & Case\r\nIdeal for students', 'Physical'),
(17, 4, 'Yamaha V3SKA Violin', 'Yamaha ', 95000.00, 4, '1772289402_violino-v3ska.jpg', '', 'Hand-carved Spruce\r\nPremium Strings\r\nRich Tone Quality\r\nFull Set with Case', 'Physical'),
(18, 5, 'Guitar Capo (Metal Alloy)', 'Generic', 2500.00, 25, '1772289492_s-l1200.jpg', '', 'Lightweight\r\nFits Acoustic & Electric\r\nQuick Release Mechanism', 'Physical'),
(19, 5, 'Drum Sticks (5A Hickory)', 'Vic Firth', 4500.00, 30, '1772289564_RAWHickoryClassic5BWoodTipDrumsticks1_e468bb4d-52f7-473d-a28a-77c4e82cb2a4-Photoroom_1.webp', '', 'Hickory Wood\r\n5A Standard Size\r\nBalanced & Durable', 'Physical'),
(20, 1, 'Beginner Guitar Lessons PDF', 'Melody Masters', 2500.00, 999, '1772289838_il_fullxfull.5977207237_oxvv.webp', '1772289838_file_manual_beginner_guitar.pdf', 'Step-by-step beginner guide\r\nChord charts included\r\nPractice exercises\r\nInstant PDF download', 'Digital'),
(21, 3, 'Drum Backing Tracks Pack (MP3)', 'Melody Masters', 3500.00, 997, '1772290087_a4206693962_16.jpg', '1772290087_file_Lamb_of_God_-_Laid_to_Rest_(mp3.pm).mp3', '20 Professional Backing Tracks\r\nMP3 Format\r\nStudio Quality\r\nInstant Download', 'Digital');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Admin','Staff','Customer') DEFAULT 'Customer',
  `address` text DEFAULT NULL,
  `contact_number` varchar(15) DEFAULT NULL,
  `reset_token_hash` varchar(64) DEFAULT NULL,
  `reset_token_expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `email`, `password`, `role`, `address`, `contact_number`, `reset_token_hash`, `reset_token_expires_at`) VALUES
(1, 'Chathu', 'chatu@gmail.com', '$2y$10$ApoFkOgZSoSgutkpCg2Hc.AcK83BVjL527/jLtXJyB.BUDckYWqnG', 'Customer', NULL, '0768483156', NULL, NULL),
(2, 'Chamo', 'chamo@gmail.com', '$2y$10$fJTsBM9JO.tznoc3Nm6oAeV7aGwMknZ80flvwRxUQXS/nQvMqyVPO', 'Customer', NULL, '0761817518', NULL, NULL),
(11, 'Vihara', 'sandunivihara717@gmail.com', '$2y$10$lj5/HcraEE8HJslDgdLE5OXl4fRNlZdH9DxNcaLO30ItBZ/RJadwq', 'Admin', NULL, '0712436759', NULL, NULL),
(12, 'sanduni', 'sandu@gmail.com', '$2y$10$DVH2QZecEQXW5LjqhlrVLeEEuOqOPwEcAv7P7ZVxcyGk5HGaoJsAC', 'Customer', NULL, '0773512698', NULL, NULL),
(13, 'Sanduni Vihara', 'sandunivihara228@gmail.com', '$2y$10$CCbH3lU/rteWbiP9hvDKWu1HqNWZR5XzVSDJbZ8UJb7/C0ZDjS6jy', 'Customer', NULL, '0714790107', NULL, NULL),
(14, 'Madu', 'madu@gmail.com', '$2y$10$BNalU.k52y0oiX5/48Z/ne.CKXrzOYWiHCbEtrDtHa0d8sP3YhJsu', 'Staff', NULL, NULL, NULL, NULL),
(16, 'Tharu', 'tharu@gmail.com', '$2y$10$0SXEdRQa6j82GpQv1V60ROY7RnioK96axVtrcS8z1bqudDdwgK.Ky', 'Admin', NULL, '0773512698', NULL, NULL),
(17, 'Menu', 'menu@gmail.com', '$2y$10$C5R5FYPKmJ3BATKH4NGRAOKNMB0.x2LRT1YLG.pe4MQv0S./8R8Gq', 'Customer', NULL, '0712436759', NULL, NULL),
(18, 'Shan', 'shan@gmail.com', '$2y$10$0oYzP9DSz14scBL6h6GtCuIAYqOdKyHdb6UsfYquhcM5zYYUQLOG6', 'Staff', NULL, NULL, NULL, NULL),
(19, 'sayuri', 'sayu@gmail.com', '$2y$10$rwC6rL.BL0MEDTC3ptWkv.M2HfUR7gLhhLe8m/YJAh5gFMUcBmBIy', 'Customer', NULL, '0714578963', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `digital_downloads`
--
ALTER TABLE `digital_downloads`
  ADD PRIMARY KEY (`download_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `digital_downloads`
--
ALTER TABLE `digital_downloads`
  MODIFY `download_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`category_id`);

--
-- Constraints for table `digital_downloads`
--
ALTER TABLE `digital_downloads`
  ADD CONSTRAINT `digital_downloads_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
