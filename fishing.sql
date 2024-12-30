-- phpMyAdmin SQL Dump
-- version 5.1.1
-- http://www.phpmyadmin.net
-- 主機: 127.0.0.1
-- 產生時間: 2024-12-29
-- 伺服器版本: 10.1.9-MariaDB
-- PHP 版本: 8.0.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- 資料庫: `fishing_db`
CREATE DATABASE IF NOT EXISTS `fishing_db` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `fishing_db`;

-- --------------------------------------------------------

-- 漁業公司表 (Fishing Company)
CREATE TABLE `Fishing_Company` (
  `Company_ID` INT NOT NULL,
  `Company_Name` VARCHAR(100) NOT NULL,
  `Address` VARCHAR(200),
  `Phone` VARCHAR(20),
  PRIMARY KEY (`Company_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='漁業公司資料';

-- 插入範例數據
INSERT INTO `Fishing_Company` (`Company_ID`, `Company_Name`, `Address`, `Phone`) VALUES
(101, '海洋漁業公司', '台北市信義區松仁路100號', '02-12345678'),
(102, '大海漁船公司', '基隆市中正區海港路50號', '02-87654321'),
(103, '遠洋捕撈企業', '高雄市前鎮區海邊街30號', '07-33557799');

-- --------------------------------------------------------

-- 漁船表 (Fishing Vessel)
CREATE TABLE `Fishing_Vessel` (
  `Vessel_ID` INT NOT NULL,
  `Vessel_Name` VARCHAR(100) NOT NULL,
  `Vessel_Type` VARCHAR(50),
  `Company_ID` INT,
  `GPS_Location` VARCHAR(100),
  PRIMARY KEY (`Vessel_ID`),
  FOREIGN KEY (`Company_ID`) REFERENCES `Fishing_Company`(`Company_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='漁船資料';

-- 插入範例數據
INSERT INTO `Fishing_Vessel` (`Vessel_ID`, `Vessel_Name`, `Vessel_Type`, `Company_ID`, `GPS_Location`) VALUES
(201, '遠洋一號', '拖網漁船', 101, '25.032969, 121.565418'),
(202, '遠洋二號', '圍網漁船', 101, '24.150229, 120.654275'),
(203, '大海之星', '拖網漁船', 102, '22.627278, 120.301435');

-- --------------------------------------------------------

-- 捕撈數據表 (Fishing Data)
CREATE TABLE `Fishing_Data` (
  `Catch_ID` INT NOT NULL,
  `Vessel_ID` INT,
  `Catch_Time` DATETIME,
  `Catch_Location` VARCHAR(100),
  `Fish_Type` VARCHAR(100),
  `Catch_Weight` FLOAT,
  PRIMARY KEY (`Catch_ID`),
  FOREIGN KEY (`Vessel_ID`) REFERENCES `Fishing_Vessel`(`Vessel_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='捕撈數據';

-- 插入範例數據
INSERT INTO `Fishing_Data` (`Catch_ID`, `Vessel_ID`, `Catch_Time`, `Catch_Location`, `Fish_Type`, `Catch_Weight`) VALUES
(301, 201, '2024-12-20 08:00:00', '東海海域', '鮪魚', 500.5),
(302, 202, '2024-12-21 09:30:00', '台灣海峽', '鯖魚', 1200.75),
(303, 203, '2024-12-22 07:45:00', '南海海域', '旗魚', 890.0);

-- --------------------------------------------------------

-- 區塊鏈交易紀錄表 (Blockchain Transaction)
CREATE TABLE `Blockchain_Transaction` (
  `Transaction_ID` INT NOT NULL,
  `Catch_ID` INT,
  `Transaction_Time` DATETIME,
  `Hash_Value` VARCHAR(256),
  PRIMARY KEY (`Transaction_ID`),
  FOREIGN KEY (`Catch_ID`) REFERENCES `Fishing_Data`(`Catch_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='區塊鏈交易紀錄';

-- 插入範例數據
INSERT INTO `Blockchain_Transaction` (`Transaction_ID`, `Catch_ID`, `Transaction_Time`, `Hash_Value`) VALUES
(401, 301, '2024-12-20 09:00:00', 'b94d27b9934d3e08a52e52d7da7dabfa'),
(402, 302, '2024-12-21 10:00:00', '4a44dc15364204a80fe80d79c3205a'),
(403, 303, '2024-12-22 08:15:00', '5f4dcc3b5aa765d61d8327deb882cf99');

-- --------------------------------------------------------

-- 索引
ALTER TABLE `Fishing_Company`
  ADD PRIMARY KEY (`Company_ID`);

ALTER TABLE `Fishing_Vessel`
  ADD PRIMARY KEY (`Vessel_ID`);

ALTER TABLE `Fishing_Data`
  ADD PRIMARY KEY (`Catch_ID`);

ALTER TABLE `Blockchain_Transaction`
  ADD PRIMARY KEY (`Transaction_ID`);
