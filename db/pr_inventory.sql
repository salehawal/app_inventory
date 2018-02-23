-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 23, 2018 at 05:38 PM
-- Server version: 10.1.30-MariaDB-0ubuntu0.17.10.1
-- PHP Version: 7.1.11-0ubuntu0.17.10.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pr_inventory`
--

-- --------------------------------------------------------

--
-- Table structure for table `fict_aircondition`
--

CREATE TABLE `fict_aircondition` (
  `fai_code` varchar(11) NOT NULL,
  `fai_desc` varchar(200) NOT NULL,
  `fai_location` varchar(10) NOT NULL,
  `fai_gps_location` varchar(100) NOT NULL,
  `fai_operational` varchar(1) NOT NULL,
  `fai_remarks` varchar(1000) NOT NULL,
  `fai_cre_by` varchar(100) NOT NULL,
  `fai_cre_date` date NOT NULL DEFAULT '0000-00-00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `fict_applicationsc`
--

CREATE TABLE `fict_applicationsc` (
  `fa_code` varchar(11) NOT NULL,
  `fa_app_name` varchar(1000) NOT NULL,
  `fa_app_serial` varchar(100) NOT NULL,
  `fa_system_code` varchar(10) NOT NULL,
  `fa_prod_code` varchar(10) NOT NULL,
  `fa_cre_by` varchar(100) NOT NULL,
  `fa_cre_date` date NOT NULL DEFAULT '0000-00-00',
  `fa_update_by` varchar(100) NOT NULL,
  `fa_update_date` date NOT NULL DEFAULT '0000-00-00',
  `fa_remarks` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `fict_batteries`
--

CREATE TABLE `fict_batteries` (
  `fbt_code` varchar(11) NOT NULL,
  `fbt_id` varchar(10) NOT NULL,
  `fbt_battery` varchar(10) NOT NULL,
  `fbt_operational` varchar(1) NOT NULL,
  `fbt_remarks` varchar(1000) NOT NULL,
  `fbt_camera_unit` varchar(100) NOT NULL,
  `fbt_location` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `fict_configuration`
--

CREATE TABLE `fict_configuration` (
  `fco_code` varchar(11) NOT NULL,
  `fco_desc` varchar(1000) NOT NULL,
  `fco_req_by_empid` varchar(10) NOT NULL,
  `fco_cre_by` varchar(100) NOT NULL,
  `fco_cre_date` date NOT NULL DEFAULT '0000-00-00',
  `fco_update_by` varchar(100) NOT NULL,
  `fco_update_date` date NOT NULL DEFAULT '0000-00-00',
  `fco_remarks` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `fict_contracts`
--

CREATE TABLE `fict_contracts` (
  `fc_code` varchar(11) NOT NULL,
  `fc_desc` varchar(1000) NOT NULL,
  `fc_provider` varchar(1000) NOT NULL,
  `fc_type` varchar(100) NOT NULL,
  `fc_start_date` date NOT NULL DEFAULT '0000-00-00',
  `fc_expire_date` date NOT NULL DEFAULT '0000-00-00',
  `fc_cre_by` varchar(100) NOT NULL,
  `fc_cre_date` date NOT NULL DEFAULT '0000-00-00',
  `fc_update_by` varchar(100) NOT NULL,
  `fc_update_date` date NOT NULL DEFAULT '0000-00-00',
  `fc_remarks` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `fict_contracts_dt`
--

CREATE TABLE `fict_contracts_dt` (
  `fcd_code` varchar(11) NOT NULL,
  `fcd_ln` int(11) DEFAULT NULL,
  `fcd_items` varchar(10) NOT NULL,
  `fcd_location` varchar(10) NOT NULL,
  `fcd_cre_by` varchar(100) NOT NULL,
  `fcd_cre_date` date NOT NULL DEFAULT '0000-00-00',
  `fcd_update_by` varchar(100) NOT NULL,
  `fcd_update_date` date NOT NULL DEFAULT '0000-00-00',
  `fc_remakrs` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `fict_documents`
--

CREATE TABLE `fict_documents` (
  `fd_code` varchar(11) NOT NULL,
  `fd_desc` varchar(1000) NOT NULL,
  `fd_cre_by` varchar(100) NOT NULL,
  `fd_cre_date` date NOT NULL DEFAULT '0000-00-00',
  `fd_update_by` varchar(100) NOT NULL,
  `fd_update_date` date NOT NULL DEFAULT '0000-00-00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `fict_fixed_cameras`
--

CREATE TABLE `fict_fixed_cameras` (
  `ffc_code` varchar(11) NOT NULL,
  `ffc_id` varchar(10) NOT NULL,
  `ffc_battery` varchar(10) NOT NULL,
  `ffc_operational` varchar(1) NOT NULL,
  `ffc_remarks` varchar(1000) NOT NULL,
  `ffc_camera_unit` varchar(100) NOT NULL,
  `ffc_location` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `fict_flash_bulbs`
--

CREATE TABLE `fict_flash_bulbs` (
  `ffb_code` varchar(11) NOT NULL,
  `ffb_desc` varchar(200) NOT NULL,
  `ffb_ffc_code` varchar(10) NOT NULL,
  `ffb_fpc_code` varchar(10) NOT NULL,
  `ffb_location` varchar(10) NOT NULL,
  `ffb_operational` varchar(1) NOT NULL,
  `ffb_remarks` varchar(1000) NOT NULL,
  `ffb_cre_by` varchar(100) NOT NULL,
  `ffb_cre_date` date NOT NULL DEFAULT '0000-00-00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `fict_flash_units`
--

CREATE TABLE `fict_flash_units` (
  `ffu_code` varchar(11) NOT NULL,
  `ffu_desc` varchar(200) NOT NULL,
  `ffu_ffc_code` varchar(10) NOT NULL,
  `ffu_fpc_code` varchar(10) NOT NULL,
  `ffu_location` varchar(10) NOT NULL,
  `ffu_operational` varchar(1) NOT NULL,
  `ffu_remarks` varchar(1000) NOT NULL,
  `ffu_cre_by` varchar(100) NOT NULL,
  `ffu_cre_date` date NOT NULL DEFAULT '0000-00-00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `fict_gaps`
--

CREATE TABLE `fict_gaps` (
  `fga_code` varchar(11) NOT NULL,
  `fga_desc` varchar(1000) NOT NULL,
  `fga_location` varchar(10) NOT NULL,
  `fga_type` varchar(100) NOT NULL,
  `fga_action` varchar(10) NOT NULL,
  `fga_status` varchar(1) NOT NULL,
  `fga_cre_by` varchar(100) NOT NULL,
  `fga_cre_date` date NOT NULL DEFAULT '0000-00-00',
  `fga_update_by` varchar(100) NOT NULL,
  `fga_update_date` date NOT NULL DEFAULT '0000-00-00',
  `fga_remarks` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `fict_hardware`
--

CREATE TABLE `fict_hardware` (
  `fhh_code` varchar(11) NOT NULL,
  `fhh_type` varchar(100) NOT NULL,
  `fhh_make` varchar(100) NOT NULL,
  `fhh_model` varchar(100) NOT NULL,
  `fhh_user` varchar(100) NOT NULL,
  `fhh_ip` varchar(50) NOT NULL,
  `fhh_mac_add` varchar(50) NOT NULL,
  `fhh_product_no` varchar(200) NOT NULL,
  `fhh_serial_no` varchar(200) NOT NULL,
  `fhh_sw_code` varchar(10) NOT NULL,
  `fhh_location` varchar(10) NOT NULL,
  `fhh_cre_by` varchar(100) NOT NULL,
  `fhh_cre_date` date NOT NULL DEFAULT '0000-00-00',
  `fhh_update_by` varchar(100) NOT NULL,
  `fhh_update_date` date NOT NULL DEFAULT '0000-00-00',
  `fhh_remarks` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `fict_images`
--

CREATE TABLE `fict_images` (
  `fim_code` varchar(11) NOT NULL,
  `fim_ref_code` varchar(150) NOT NULL,
  `fim_image` blob
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `fict_location`
--

CREATE TABLE `fict_location` (
  `flo_code` varchar(11) NOT NULL,
  `flo_type` varchar(100) NOT NULL,
  `flo_address` varchar(500) NOT NULL,
  `flo_gps` varchar(100) NOT NULL,
  `flo_desc` varchar(1000) NOT NULL,
  `flo_facility_manager` varchar(10) NOT NULL,
  `flo_ref_branch` varchar(100) NOT NULL,
  `flo_cre_by` varchar(100) NOT NULL,
  `flo_cre_date` date NOT NULL DEFAULT '0000-00-00',
  `flo_remarks` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `fict_office_equipment`
--

CREATE TABLE `fict_office_equipment` (
  `foe_code` varchar(11) NOT NULL,
  `foe_type` varchar(100) NOT NULL,
  `foe_location` varchar(10) NOT NULL,
  `foe_user` varchar(10) NOT NULL,
  `foe_make` varchar(100) NOT NULL,
  `foe_model` varchar(100) NOT NULL,
  `foe_owner` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `fict_options`
--

CREATE TABLE `fict_options` (
  `fo_code` varchar(11) NOT NULL,
  `fo_column` varchar(100) NOT NULL,
  `fo_column_type` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `fict_options_detail`
--

CREATE TABLE `fict_options_detail` (
  `fo_options_detail_id` int(11) NOT NULL,
  `fo_code` varchar(10) NOT NULL,
  `fo_options_name` varchar(100) NOT NULL,
  `fo_options_value` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `fict_portable_cameras`
--

CREATE TABLE `fict_portable_cameras` (
  `fpc_code` int(11) NOT NULL,
  `fpc_id` varchar(10) NOT NULL,
  `fpc_battery` varchar(10) NOT NULL,
  `fpc_operational` varchar(1) NOT NULL,
  `fpc_remarks` varchar(1000) NOT NULL,
  `fpc_camera_unit` varchar(100) NOT NULL,
  `fpc_location` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `fict_pucks`
--

CREATE TABLE `fict_pucks` (
  `fpu_code` varchar(11) NOT NULL,
  `fpu_desc` varchar(200) NOT NULL,
  `fpu_location` varchar(10) NOT NULL,
  `fpu_gps_location` varchar(100) NOT NULL,
  `fpu_operational` varchar(1) NOT NULL,
  `fpu_remarks` varchar(1000) NOT NULL,
  `fpu_cre_by` varchar(100) NOT NULL,
  `fpu_cre_date` date NOT NULL DEFAULT '0000-00-00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `fict_redlight_systems`
--

CREATE TABLE `fict_redlight_systems` (
  `frs_code` varchar(11) NOT NULL,
  `frs_id` varchar(10) NOT NULL,
  `frs_battery` varchar(10) NOT NULL,
  `frs_operational` varchar(1) NOT NULL,
  `frs_remarks` varchar(1000) NOT NULL,
  `frs_camera_unit` varchar(100) NOT NULL,
  `frs_location` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `fict_software`
--

CREATE TABLE `fict_software` (
  `fsw_code` varchar(11) NOT NULL,
  `fsw_sw` varchar(100) NOT NULL,
  `fsw_ver` varchar(100) NOT NULL,
  `fsw_producer` varchar(100) NOT NULL,
  `fsw_system_key` varchar(200) NOT NULL,
  `fsw_covered_license` varchar(10) NOT NULL,
  `fsw_covered_support` varchar(10) NOT NULL,
  `fsw_doc_code` varchar(10) NOT NULL,
  `fsw_login_detail` varchar(200) NOT NULL,
  `fsw_used_by` varchar(10) NOT NULL,
  `fsw_cre_by` varchar(100) NOT NULL,
  `fsw_cre_date` date NOT NULL DEFAULT '0000-00-00',
  `fsw_update_by` varchar(100) NOT NULL,
  `fsw_update_date` date NOT NULL DEFAULT '0000-00-00',
  `fsw_remarks` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `fict_staff`
--

CREATE TABLE `fict_staff` (
  `fst_code` varchar(11) NOT NULL,
  `fst_name` varchar(500) NOT NULL,
  `fst_id` varchar(50) NOT NULL,
  `fst_role` varchar(500) NOT NULL,
  `fst_outcomes` varchar(100) NOT NULL,
  `fst_rep_manager` varchar(100) NOT NULL,
  `fst_employer` varchar(100) NOT NULL,
  `fst_location` varchar(10) NOT NULL,
  `fst_cre_by` varchar(100) NOT NULL,
  `fst_cre_date` date NOT NULL DEFAULT '0000-00-00',
  `fst_remarks` varchar(2000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `fict_staff_dt`
--

CREATE TABLE `fict_staff_dt` (
  `fstd_code` varchar(11) NOT NULL,
  `fstd_fst_code` varchar(10) NOT NULL,
  `fstd_fst_id` varchar(10) NOT NULL,
  `fstd_team_staff_id` varchar(10) NOT NULL,
  `fstd_location` varchar(10) NOT NULL,
  `fstd_cre_by` varchar(100) NOT NULL,
  `fstd_cre_date` date NOT NULL DEFAULT '0000-00-00',
  `fstd_remarks` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `fict_users`
--

CREATE TABLE `fict_users` (
  `fu_code` varchar(11) NOT NULL,
  `fu_username` varchar(150) NOT NULL,
  `fu_role` varchar(150) NOT NULL,
  `fu_password` varchar(200) NOT NULL,
  `fu_rep_manager` varchar(100) NOT NULL,
  `fu_location` varchar(10) NOT NULL,
  `fu_cre_by` varchar(100) NOT NULL,
  `fu_cre_date` date NOT NULL DEFAULT '0000-00-00',
  `fu_remarks` varchar(2000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `fict_vehicles`
--

CREATE TABLE `fict_vehicles` (
  `fv_code` varchar(11) NOT NULL,
  `fv_chassis_no` varchar(50) NOT NULL,
  `fv_type` varchar(100) NOT NULL,
  `fv_location` varchar(10) NOT NULL,
  `fv_wheels` int(11) DEFAULT NULL,
  `fv_engine` varchar(100) NOT NULL,
  `fv_seats` int(11) DEFAULT NULL,
  `fv_battery` varchar(100) NOT NULL,
  `fv_battery_id` varchar(10) NOT NULL,
  `fv_plateno` varchar(50) NOT NULL,
  `fv_assigned_driver` varchar(100) NOT NULL,
  `fv_status` varchar(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `fict_aircondition`
--
ALTER TABLE `fict_aircondition`
  ADD PRIMARY KEY (`fai_code`);

--
-- Indexes for table `fict_applicationsc`
--
ALTER TABLE `fict_applicationsc`
  ADD PRIMARY KEY (`fa_code`);

--
-- Indexes for table `fict_batteries`
--
ALTER TABLE `fict_batteries`
  ADD PRIMARY KEY (`fbt_code`);

--
-- Indexes for table `fict_configuration`
--
ALTER TABLE `fict_configuration`
  ADD PRIMARY KEY (`fco_code`);

--
-- Indexes for table `fict_contracts`
--
ALTER TABLE `fict_contracts`
  ADD PRIMARY KEY (`fc_code`);

--
-- Indexes for table `fict_contracts_dt`
--
ALTER TABLE `fict_contracts_dt`
  ADD PRIMARY KEY (`fcd_code`);

--
-- Indexes for table `fict_documents`
--
ALTER TABLE `fict_documents`
  ADD PRIMARY KEY (`fd_code`);

--
-- Indexes for table `fict_fixed_cameras`
--
ALTER TABLE `fict_fixed_cameras`
  ADD PRIMARY KEY (`ffc_code`);

--
-- Indexes for table `fict_flash_bulbs`
--
ALTER TABLE `fict_flash_bulbs`
  ADD PRIMARY KEY (`ffb_code`);

--
-- Indexes for table `fict_flash_units`
--
ALTER TABLE `fict_flash_units`
  ADD PRIMARY KEY (`ffu_code`);

--
-- Indexes for table `fict_gaps`
--
ALTER TABLE `fict_gaps`
  ADD PRIMARY KEY (`fga_code`);

--
-- Indexes for table `fict_hardware`
--
ALTER TABLE `fict_hardware`
  ADD PRIMARY KEY (`fhh_code`);

--
-- Indexes for table `fict_images`
--
ALTER TABLE `fict_images`
  ADD PRIMARY KEY (`fim_code`);

--
-- Indexes for table `fict_location`
--
ALTER TABLE `fict_location`
  ADD PRIMARY KEY (`flo_code`);

--
-- Indexes for table `fict_office_equipment`
--
ALTER TABLE `fict_office_equipment`
  ADD PRIMARY KEY (`foe_code`);

--
-- Indexes for table `fict_options`
--
ALTER TABLE `fict_options`
  ADD PRIMARY KEY (`fo_code`);

--
-- Indexes for table `fict_options_detail`
--
ALTER TABLE `fict_options_detail`
  ADD PRIMARY KEY (`fo_options_detail_id`);

--
-- Indexes for table `fict_portable_cameras`
--
ALTER TABLE `fict_portable_cameras`
  ADD PRIMARY KEY (`fpc_code`);

--
-- Indexes for table `fict_pucks`
--
ALTER TABLE `fict_pucks`
  ADD PRIMARY KEY (`fpu_code`);

--
-- Indexes for table `fict_redlight_systems`
--
ALTER TABLE `fict_redlight_systems`
  ADD PRIMARY KEY (`frs_code`);

--
-- Indexes for table `fict_software`
--
ALTER TABLE `fict_software`
  ADD PRIMARY KEY (`fsw_code`);

--
-- Indexes for table `fict_staff`
--
ALTER TABLE `fict_staff`
  ADD PRIMARY KEY (`fst_code`);

--
-- Indexes for table `fict_staff_dt`
--
ALTER TABLE `fict_staff_dt`
  ADD PRIMARY KEY (`fstd_code`);

--
-- Indexes for table `fict_users`
--
ALTER TABLE `fict_users`
  ADD PRIMARY KEY (`fu_code`);

--
-- Indexes for table `fict_vehicles`
--
ALTER TABLE `fict_vehicles`
  ADD PRIMARY KEY (`fv_code`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `fict_options_detail`
--
ALTER TABLE `fict_options_detail`
  MODIFY `fo_options_detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=351;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
