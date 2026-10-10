-- MySQL Database Schema for South Moon Lipoma Shop
-- (Optional: Use this if you choose MySQL instead of SQLite on cPanel)

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- --------------------------------------------------------
-- Table structure for orders
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_number` varchar(50) DEFAULT NULL,
  `customer_name` varchar(150) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `address` text NOT NULL,
  `package_name` varchar(150) NOT NULL,
  `package_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `quantity` int(11) NOT NULL DEFAULT '1',
  `shipping_area` varchar(100) NOT NULL,
  `shipping_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `note` text DEFAULT NULL,
  `status` enum('pending','confirmed','processing','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending',
  `ip_address` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_number` (`order_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for settings
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `settings` (
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for admins
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Default Settings Data
-- --------------------------------------------------------
INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('site_title', 'সাউথ মুন লাইপোমা রিমুভাল স্প্রে | ১০০% অরিজিনাল'),
('product_name', 'South Moon Lipoma Removal Spray'),
('hotline', '01872307272'),
('whatsapp', '01872307272'),
('package_1_title', 'Buy 2 Get 1 Free (৩টি স্প্রে)'),
('package_1_price', '1580'),
('package_1_regular', '2370'),
('package_1_badge', 'সেরা অফার (Buy 2 Get 1)'),
('package_2_title', '১টি স্প্রে'),
('package_2_price', '790'),
('package_2_regular', '990'),
('package_2_badge', 'স্ট্যান্ডার্ড প্যাক'),
('delivery_dhaka', '70'),
('delivery_outside', '130'),
('meta_pixel_id', ''),
('meta_pixel_active', '0'),
('custom_header_script', ''),
('custom_footer_script', '')
ON DUPLICATE KEY UPDATE `setting_value` = VALUES(`setting_value`);

-- Default Admin: admin / admin123456
INSERT INTO `admins` (`id`, `username`, `password_hash`) VALUES
(1, 'admin', '$2y$10$vU8W0XjYg7t5M8x8V5K3OOnrW0XjYg7t5M8x8V5K3OOnrW0XjYg7t')
ON DUPLICATE KEY UPDATE `username` = VALUES(`username`);
