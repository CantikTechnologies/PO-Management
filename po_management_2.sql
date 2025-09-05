-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 05, 2025 at 11:41 AM
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
-- Table structure for table `finance_tasks`
--

CREATE TABLE `finance_tasks` (
  `id` int(11) NOT NULL,
  `action_requested_by` varchar(100) NOT NULL,
  `request_date` date NOT NULL,
  `cost_center` varchar(100) NOT NULL,
  `action_required` text NOT NULL,
  `action_owner` varchar(100) NOT NULL,
  `status_of_action` enum('Pending','In Progress','Completed','On Hold') DEFAULT 'Pending',
  `completion_date` date DEFAULT NULL,
  `remark` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `finance_tasks`
--

INSERT INTO `finance_tasks` (`id`, `action_requested_by`, `request_date`, `cost_center`, `action_required`, `action_owner`, `status_of_action`, `completion_date`, `remark`, `created_at`, `updated_at`) VALUES
(4, 'Naveen', '2025-06-25', 'Raptokos - PT', 'Vratatech - Raptokos PT One month payment to be released immediately', 'Sanjay', 'Pending', NULL, NULL, '2025-09-05 07:00:39', '2025-09-05 07:00:39'),
(5, 'Naveen', '2025-06-25', 'Raptokos - PT', 'Renewal to be followed with Priya', 'Sneha', 'Pending', NULL, NULL, '2025-09-05 07:00:39', '2025-09-05 07:00:39'),
(6, 'Naveen', '2025-06-25', 'BMW-OA', 'Renewal to be followed with Priya', 'Sneha', 'Pending', NULL, NULL, '2025-09-05 07:00:39', '2025-09-05 07:00:39');

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
(1, 1, 'CTPL/24-25/1312', '2025-02-05', 35225.00, 'MAH/464/24-25', '2025-06-03', '', 'VRATA TECH SOLUTIONS PRIVATE LIMITED'),
(2, 1, 'CTPL/24-25/1521', '2025-03-19', 68250.00, 'MAH/558/24-25', '2025-04-16', '', 'VRATA TECH SOLUTIONS PRIVATE LIMITED'),
(3, 1, 'CTPL/25-26/128', '2025-04-28', 68250.00, 'MAH/650/24-25', '2025-05-28', '', 'VRATA TECH SOLUTIONS PRIVATE LIMITED'),
(4, 2, 'CTPL/25-26/306', '2025-06-21', 68250.00, 'M/256/Jun/024', '2025-07-21', '1400005222', 'VRATA TECH SOLUTIONS PRIVATE LIMITED');

--
-- Triggers `invoices`
--
DELIMITER $$
CREATE TRIGGER `trg_update_pending_after_invoice` AFTER INSERT ON `invoices` FOR EACH ROW BEGIN
  UPDATE purchase_orders
  SET pending_amount_in_po = po_value - (
      SELECT IFNULL(SUM(taxable_value),0)
      FROM invoices
      WHERE po_id = NEW.po_id
  )
  WHERE po_id = NEW.po_id;
END
$$
DELIMITER ;

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
  `payment_value` decimal(18,2) GENERATED ALWAYS AS (`net_payable`) STORED,
  `payment_date` date DEFAULT NULL,
  `pending_payment` decimal(18,2) GENERATED ALWAYS AS (`net_payable` - ifnull(`payment_value`,0)) STORED,
  `remarks` text DEFAULT NULL,
  `vendor_invoice_frequency` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `outsourcing_details`
--

INSERT INTO `outsourcing_details` (`outsourcing_id`, `po_id`, `cantik_po_no`, `cantik_po_date`, `cantik_po_value`, `vendor_invoice_no`, `vendor_invoice_date`, `vendor_invoice_value`, `payment_status`, `payment_date`, `remarks`, `vendor_invoice_frequency`) VALUES
(1, 1, 'CTPL/PO/24-25/396', '2025-01-27', 167143.00, 'MAH/464/24-25', '2025-01-31', 33548.00, '3 jun 2025', '2025-03-12', '', NULL),
(2, 1, 'CTPL/PO/24-25/396', '2025-01-27', 167143.00, 'MAH/558/24-25', '2025-02-28', 65000.00, '16 apr 2025', '2025-04-19', '', NULL),
(3, 1, 'CTPL/PO/24-25/396', '2025-01-27', 167143.00, 'MAH/650/24-25', '2025-03-31', 65000.00, NULL, NULL, '', NULL),
(4, 2, 'CTPL/PO/24-25/104', '2025-04-06', 130000.00, 'M/256/Jun/024', '2025-06-13', 65000.00, NULL, '2025-07-21', 'Payment Sent to finance on 01 Aug 25\r\n', NULL);

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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `pending_amount_in_po` decimal(18,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_orders`
--

INSERT INTO `purchase_orders` (`po_id`, `project_description`, `cost_center`, `sow_number`, `start_date`, `end_date`, `po_number`, `po_date`, `po_value`, `billing_frequency`, `target_gm`, `po_status`, `remarks`, `vendor_name`, `created_at`, `updated_at`, `pending_amount_in_po`) VALUES
(1, 'Raptakos Resource Deployment - Anuj Kushwaha', 'Raptakos PT', 'FC2024-497', '2025-01-16', '2025-03-31', '4500095281', '2025-01-27', 175500.00, 'Monthly', 5.00, 'Closed', 'Jan-39000, Feb & Mar 68250\r\n', 'VRATA TECH SOLUTIONS PRIVATE LIMITED', '2025-09-04 06:39:28', '2025-09-04 09:22:08', 3775.00),
(2, 'Raptakos Resource Deployment - Anuj Kushwaha', 'Raptakos PT', 'FC2025-073', '2025-04-01', '2025-05-31', '4500098831', '2025-04-06', 136500.00, 'Monthly', 5.00, 'Closed', '', 'VRATA TECH SOLUTIONS PRIVATE LIMITED', '2025-09-05 04:33:04', '2025-09-05 04:34:41', 68250.00);

-- --------------------------------------------------------

--
-- Stand-in structure for view `so_form`
-- (See below for the actual view)
--
CREATE TABLE `so_form` (
`project_name` varchar(255)
,`cost_center` varchar(100)
,`customer_po_no` varchar(50)
,`customer_po_value` decimal(18,2)
,`billed_till_date` decimal(40,2)
,`remaining_balance_po` decimal(41,2)
,`vendor_name` varchar(255)
,`vendor_po_no` varchar(100)
,`vendor_po_value` decimal(18,2)
,`vendor_invoicing_till_date` decimal(40,2)
,`remaining_vendor_balance` decimal(41,2)
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
-- Stand-in structure for view `v_purchase_orders`
-- (See below for the actual view)
--
CREATE TABLE `v_purchase_orders` (
`po_id` bigint(20) unsigned
,`project_description` varchar(255)
,`cost_center` varchar(100)
,`sow_number` varchar(100)
,`start_date` date
,`end_date` date
,`po_number` varchar(50)
,`po_date` date
,`po_value` decimal(18,2)
,`billing_frequency` enum('Monthly','Quarterly','Yearly','Other')
,`target_gm` decimal(5,2)
,`po_status` enum('Open','Active','Closed','Cancelled','On Hold')
,`remarks` text
,`vendor_name` varchar(255)
,`created_at` timestamp
,`updated_at` timestamp
,`pending_amount_in_po` decimal(41,2)
);

-- --------------------------------------------------------

--
-- Structure for view `so_form`
--
DROP TABLE IF EXISTS `so_form`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `so_form`  AS SELECT `po`.`project_description` AS `project_name`, `po`.`cost_center` AS `cost_center`, `po`.`po_number` AS `customer_po_no`, `po`.`po_value` AS `customer_po_value`, ifnull(`inv_sum`.`total_taxable`,0) AS `billed_till_date`, `po`.`po_value`- ifnull(`inv_sum`.`total_taxable`,0) AS `remaining_balance_po`, `po`.`vendor_name` AS `vendor_name`, max(`outd`.`cantik_po_no`) AS `vendor_po_no`, max(`outd`.`cantik_po_value`) AS `vendor_po_value`, ifnull(`outd_sum`.`total_vendor_invoice`,0) AS `vendor_invoicing_till_date`, max(`outd`.`cantik_po_value`) - ifnull(`outd_sum`.`total_vendor_invoice`,0) AS `remaining_vendor_balance`, round((ifnull(`inv_sum`.`total_taxable`,0) - ifnull(`outd_sum`.`total_vendor_invoice`,0)) / nullif(ifnull(`inv_sum`.`total_taxable`,0),0) * 100,2) AS `sale_margin_till_date`, `po`.`target_gm` AS `target_gm`, round((ifnull(`inv_sum`.`total_taxable`,0) - ifnull(`outd_sum`.`total_vendor_invoice`,0)) / nullif(ifnull(`inv_sum`.`total_taxable`,0),0) * 100 - `po`.`target_gm`,2) AS `variance_in_gm` FROM (((`purchase_orders` `po` left join (select `invoices`.`po_id` AS `po_id`,sum(`invoices`.`taxable_value`) AS `total_taxable` from `invoices` group by `invoices`.`po_id`) `inv_sum` on(`inv_sum`.`po_id` = `po`.`po_id`)) left join `outsourcing_details` `outd` on(`outd`.`po_id` = `po`.`po_id`)) left join (select `outsourcing_details`.`po_id` AS `po_id`,sum(`outsourcing_details`.`vendor_invoice_value`) AS `total_vendor_invoice` from `outsourcing_details` group by `outsourcing_details`.`po_id`) `outd_sum` on(`outd_sum`.`po_id` = `po`.`po_id`)) GROUP BY `po`.`po_id` ;

-- --------------------------------------------------------

--
-- Structure for view `v_purchase_orders`
--
DROP TABLE IF EXISTS `v_purchase_orders`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_purchase_orders`  AS SELECT `po`.`po_id` AS `po_id`, `po`.`project_description` AS `project_description`, `po`.`cost_center` AS `cost_center`, `po`.`sow_number` AS `sow_number`, `po`.`start_date` AS `start_date`, `po`.`end_date` AS `end_date`, `po`.`po_number` AS `po_number`, `po`.`po_date` AS `po_date`, `po`.`po_value` AS `po_value`, `po`.`billing_frequency` AS `billing_frequency`, `po`.`target_gm` AS `target_gm`, `po`.`po_status` AS `po_status`, `po`.`remarks` AS `remarks`, `po`.`vendor_name` AS `vendor_name`, `po`.`created_at` AS `created_at`, `po`.`updated_at` AS `updated_at`, `po`.`po_value`- ifnull(sum(`inv`.`taxable_value`),0) AS `pending_amount_in_po` FROM (`purchase_orders` `po` left join `invoices` `inv` on(`inv`.`po_id` = `po`.`po_id`)) GROUP BY `po`.`po_id` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `finance_tasks`
--
ALTER TABLE `finance_tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_request_date` (`request_date`),
  ADD KEY `idx_cost_center` (`cost_center`),
  ADD KEY `idx_status` (`status_of_action`),
  ADD KEY `idx_action_owner` (`action_owner`),
  ADD KEY `idx_created_at` (`created_at`);

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
-- AUTO_INCREMENT for table `finance_tasks`
--
ALTER TABLE `finance_tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `invoice_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `outsourcing_details`
--
ALTER TABLE `outsourcing_details`
  MODIFY `outsourcing_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `po_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
