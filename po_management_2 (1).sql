-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 04, 2025 at 09:15 AM
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
-- Database: `po_management_2`
--

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `invoice_id` bigint(20) UNSIGNED NOT NULL,
  `po_id` bigint(20) UNSIGNED NOT NULL,
  `cantik_invoice_no` varchar(100) NOT NULL,
  `cantik_invoice_date` date NOT NULL,
  `taxable_value` decimal(18,2) NOT NULL,
  `tds` decimal(18,2) GENERATED ALWAYS AS (round(`taxable_value` * 0.02,2)) STORED,
  `receivable` decimal(18,2) GENERATED ALWAYS AS (round(`taxable_value` * 1.18 - `taxable_value` * 0.02,2)) STORED,
  `vendor_invoice_no` varchar(100) DEFAULT NULL,
  `payment_receipt_date` date DEFAULT NULL,
  `payment_advise_no` varchar(100) DEFAULT NULL,
  `vendor_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`invoice_id`, `po_id`, `cantik_invoice_no`, `cantik_invoice_date`, `taxable_value`, `vendor_invoice_no`, `payment_receipt_date`, `payment_advise_no`, `vendor_name`) VALUES
(1, 1, 'CTPL/24-25/1312', '2025-02-05', 35225.00, 'MAH/464/24-25', '2025-06-03', '', 'VRATA TECH SOLUTIONS PRIVATE LIMITED');

-- --------------------------------------------------------

--
-- Table structure for table `outsourcing_details`
--

CREATE TABLE `outsourcing_details` (
  `outsourcing_id` bigint(20) UNSIGNED NOT NULL,
  `po_id` bigint(20) UNSIGNED NOT NULL,
  `cantik_po_no` varchar(100) NOT NULL,
  `cantik_po_date` date DEFAULT NULL,
  `cantik_po_value` decimal(18,2) DEFAULT NULL,
  `vendor_invoice_no` varchar(100) NOT NULL,
  `vendor_invoice_date` date DEFAULT NULL,
  `vendor_invoice_value` decimal(18,2) NOT NULL,
  `tds_ded` decimal(18,2) GENERATED ALWAYS AS (round(`vendor_invoice_value` * 0.02,2)) STORED,
  `net_payable` decimal(18,2) GENERATED ALWAYS AS (round(`vendor_invoice_value` * 1.18 - `vendor_invoice_value` * 0.02,2)) STORED,
  `payment_status` varchar(100) DEFAULT NULL,
  `payment_value` decimal(18,2) DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `pending_payment` decimal(18,2) GENERATED ALWAYS AS (`net_payable` - ifnull(`payment_value`,0)) STORED,
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `outsourcing_details`
--

INSERT INTO `outsourcing_details` (`outsourcing_id`, `po_id`, `cantik_po_no`, `cantik_po_date`, `cantik_po_value`, `vendor_invoice_no`, `vendor_invoice_date`, `vendor_invoice_value`, `payment_status`, `payment_value`, `payment_date`, `remarks`) VALUES
(1, 1, 'CTPL/PO/24-25/396', '2025-01-27', 167143.00, 'MAH/464/24-25', '2025-01-31', 33548.00, '3 jun 2025', 38915.00, '2025-03-12', '');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_orders`
--

CREATE TABLE `purchase_orders` (
  `po_id` bigint(20) UNSIGNED NOT NULL,
  `project_description` varchar(255) NOT NULL,
  `cost_center` varchar(100) NOT NULL,
  `sow_number` varchar(100) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `po_number` varchar(50) NOT NULL,
  `po_date` date DEFAULT NULL,
  `po_value` decimal(18,2) DEFAULT NULL,
  `billing_frequency` enum('Monthly','Quarterly','Yearly','Other') DEFAULT 'Monthly',
  `target_gm` decimal(5,2) DEFAULT NULL,
  `po_status` enum('Open','Active','Closed','Cancelled','On Hold') DEFAULT 'Active',
  `remarks` text DEFAULT NULL,
  `vendor_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_orders`
--

INSERT INTO `purchase_orders` (`po_id`, `project_description`, `cost_center`, `sow_number`, `start_date`, `end_date`, `po_number`, `po_date`, `po_value`, `billing_frequency`, `target_gm`, `po_status`, `remarks`, `vendor_name`, `created_at`, `updated_at`) VALUES
(1, 'Raptakos Resource Deployment - Anuj Kushwaha', 'Raptakos PT', 'FC2024-497', '2025-01-16', '2025-03-31', '4500095281', '2025-01-27', 175500.00, 'Monthly', 5.00, 'Closed', 'Jan-39000, Feb & Mar 68250\r\n', 'VRATA TECH SOLUTIONS PRIVATE LIMITED', '2025-09-04 06:39:28', '2025-09-04 06:39:28');

-- --------------------------------------------------------

--
-- Stand-in structure for view `so_form`
-- (See below for the actual view)
--
CREATE TABLE `so_form` (
`cost_center` varchar(100)
,`customer_po_no` varchar(50)
,`customer_po_value` decimal(18,2)
,`billed_till_date` decimal(40,2)
,`remaining_balance_in_po` decimal(41,2)
,`vendor_name` varchar(255)
,`vendor_po_no` varchar(100)
,`vendor_po_value` decimal(18,2)
,`vendor_invoicing_till_date` decimal(40,2)
,`remaining_balance_in_vendor_po` decimal(41,2)
,`sale_margin_till_date` decimal(47,2)
,`target_gm` decimal(5,2)
,`variance_in_gm` decimal(48,2)
);

-- --------------------------------------------------------

--
-- Table structure for table `users_login_signup`
--

CREATE TABLE `users_login_signup` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure for view `so_form`
--
DROP TABLE IF EXISTS `so_form`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `so_form`  AS SELECT `po`.`cost_center` AS `cost_center`, `po`.`po_number` AS `customer_po_no`, `po`.`po_value` AS `customer_po_value`, coalesce(sum(`inv`.`taxable_value`),0) AS `billed_till_date`, `po`.`po_value`- coalesce(sum(`inv`.`taxable_value`),0) AS `remaining_balance_in_po`, `po`.`vendor_name` AS `vendor_name`, max(`outd`.`cantik_po_no`) AS `vendor_po_no`, max(`outd`.`cantik_po_value`) AS `vendor_po_value`, coalesce(sum(`outd`.`vendor_invoice_value`),0) AS `vendor_invoicing_till_date`, max(`outd`.`cantik_po_value`) - coalesce(sum(`outd`.`vendor_invoice_value`),0) AS `remaining_balance_in_vendor_po`, round((coalesce(sum(`inv`.`taxable_value`),0) - coalesce(sum(`outd`.`vendor_invoice_value`),0)) / nullif(coalesce(sum(`inv`.`taxable_value`),0),0) * 100,2) AS `sale_margin_till_date`, `po`.`target_gm` AS `target_gm`, round((coalesce(sum(`inv`.`taxable_value`),0) - coalesce(sum(`outd`.`vendor_invoice_value`),0)) / nullif(coalesce(sum(`inv`.`taxable_value`),0),0) * 100 - `po`.`target_gm`,2) AS `variance_in_gm` FROM ((`purchase_orders` `po` left join `invoices` `inv` on(`inv`.`po_id` = `po`.`po_id`)) left join `outsourcing_details` `outd` on(`outd`.`po_id` = `po`.`po_id`)) GROUP BY `po`.`po_id` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`invoice_id`),
  ADD KEY `fk_invoice_po` (`po_id`);

--
-- Indexes for table `outsourcing_details`
--
ALTER TABLE `outsourcing_details`
  ADD PRIMARY KEY (`outsourcing_id`),
  ADD KEY `fk_outsourcing_po` (`po_id`);

--
-- Indexes for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD PRIMARY KEY (`po_id`),
  ADD UNIQUE KEY `po_number` (`po_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `invoice_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `outsourcing_details`
--
ALTER TABLE `outsourcing_details`
  MODIFY `outsourcing_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `po_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `fk_invoice_po` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`po_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `outsourcing_details`
--
ALTER TABLE `outsourcing_details`
  ADD CONSTRAINT `fk_outsourcing_po` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`po_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
