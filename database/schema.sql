-- Database: nsct_website
--
-- Database: `nsct`
--

-- Drop database if exists and create a new one
DROP DATABASE IF EXISTS `nsct`;
CREATE DATABASE IF NOT EXISTS `nsct` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `nsct`;

-- --------------------------------------------------------

--
-- Table structure for table `users` (for Admin Panel)
--
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','staff') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'staff',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=Active, 0=Inactive',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--
INSERT INTO `users` (`full_name`, `username`, `email`, `password`, `role`, `status`) VALUES
('Administrator', 'admin', 'admin@nsct.org', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1);

-- --------------------------------------------------------

--
-- Table structure for table `members` (for Public Portal)
--
CREATE TABLE `members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `father_husband_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dob` date NOT NULL,
  `gender` enum('Male','Female','Other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pan_aadhar` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `state` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `district` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `blood_group` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nominee_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nominee_relation` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nominee_mobile` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `family_members` tinyint(4) DEFAULT NULL,
  `profile_picture` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Path to member photo',
  `status` enum('Pending','Active','Inactive','Suspended') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `registration_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  `reset_token` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mobile` (`mobile`),
  UNIQUE KEY `pan_aadhar` (`pan_aadhar`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_status` (`status`),
  KEY `idx_state_district` (`state`,`district`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Dumping data for table `members` (Sample Data)
--
INSERT INTO `members` (`id`, `name`, `father_husband_name`, `dob`, `gender`, `mobile`, `email`, `pan_aadhar`, `password`, `address`, `state`, `district`, `blood_group`, `nominee_name`, `nominee_relation`, `nominee_mobile`, `family_members`, `profile_picture`, `status`) VALUES
(1, 'Rajesh Kumar', 'Suresh Kumar', '1985-05-15', 'Male', '9876543210', 'rajesh@example.com', 'ABCDE1234F', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '123 Civil Lines, Near Main Market', 'Uttar Pradesh', 'Prayagraj', 'O+', 'Sunita Kumar', 'Wife', '9876543211', 4, 'member1.jpg', 'Active'),
(2, 'Anita Devi', 'Ramesh Singh', '1990-02-20', 'Female', '9876543212', 'anita@example.com', '123456789012', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '456 MG Road, Lanka', 'Uttar Pradesh', 'Varanasi', 'A+', 'Rohan Singh', 'Son', '9876543213', 3, 'member2.jpg', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `contributions`
--
CREATE TABLE `contributions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `contribution_type` enum('Sahyog','VyawasthaShulk','MembershipFee','Other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_date` date NOT NULL,
  `transaction_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_method` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Completed','Pending','Failed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Completed',
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL COMMENT 'Admin user ID who entered it',
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`),
  KEY `contribution_type` (`contribution_type`),
  CONSTRAINT `fk_contributions_member` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_contributions_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contributions` (Sample Data)
--
INSERT INTO `contributions` (`id`, `member_id`, `amount`, `contribution_type`, `payment_date`, `transaction_id`, `payment_method`, `status`, `notes`, `created_by`) VALUES
(1, 1, '50000.00', 'Sahyog', '2024-05-20', 'TXN12345', 'Bank Transfer', 'Completed', 'For urgent medical surgery due to an accident.', 1),
(2, 2, '75000.00', 'Sahyog', '2024-06-10', 'TXN67890', 'Online', 'Pending', 'Support for daughter\'s wedding after the sudden demise of her father.', 1);

-- --------------------------------------------------------

--
-- Table structure for table `pages` (for dynamic content like About Us, Niyamawali)
--
CREATE TABLE `pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=Published, 0=Draft',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `news_announcements`
--
CREATE TABLE `news_announcements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `publish_date` date NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sliders` (for homepage slider)
--
CREATE TABLE `sliders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `button_text` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `button_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=Iutf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--
CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Unread','Read','Archived') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Unread',
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--
CREATE TABLE `settings` (
  `setting_key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--
INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('contact_address', 'Meja, Prayagraj, Uttar Pradesh'),
('contact_email', 'contact@nsct.org'),
('helpline_number', '707-167-7676'),
('registration_number', '4/014/2021'),
('site_logo', 'assets/images/logo.png'),
('site_name', 'नन्दवंशी सेल्फ केयर ट्रस्ट'),
('site_tagline', 'अल्प अंशदान बनेगा वरदान');

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--
CREATE TABLE `activity_logs` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL COMMENT 'Admin user ID from users table',
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `details` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `fk_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `social_links`
--
CREATE TABLE `social_links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `platform` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon_class` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'e.g., fab fa-facebook-f',
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `social_links`
--
INSERT INTO `social_links` (`platform`, `icon_class`, `url`, `display_order`, `is_active`) VALUES
('Facebook', 'fab fa-facebook-f', '#', 1, 1),
('Twitter', 'fab fa-twitter', '#', 2, 1),
('YouTube', 'fab fa-youtube', '#', 3, 1),
('Instagram', 'fab fa-instagram', '#', 4, 1),
('LinkedIn', 'fab fa-linkedin-in', '#', 5, 1);

-- --------------------------------------------------------

--
-- Table structure for table `quick_links`
--
CREATE TABLE `quick_links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quick_links`
--
INSERT INTO `quick_links` (`title`, `url`, `display_order`, `is_active`) VALUES
('About Us', 'about.php', 1, 1),
('Niyamawali', 'niyamawali.php', 2, 1),
('Sahyog List', 'sahyog_list.php', 3, 1),
('Contact Us', 'contact.php', 4, 1),
('FAQ', 'faq.php', 5, 1);
