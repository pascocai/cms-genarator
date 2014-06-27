-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.1.35-community - MySQL Community Server (GPL)
-- Server OS:                    Win32
-- HeidiSQL version:             7.0.0.4053
-- Date/time:                    2014-06-27 10:35:19
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET FOREIGN_KEY_CHECKS=0 */;

-- Dumping database structure for cmsg
CREATE DATABASE IF NOT EXISTS `cmsg` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_bin */;
USE `cmsg`;

-- Dumping structure for table its.cmsg_field_option
CREATE TABLE IF NOT EXISTS `cmsg_field_option` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `table_name` varchar(50) COLLATE utf8_bin NOT NULL,
  `table_field` varchar(50) COLLATE utf8_bin NOT NULL,
  `option_index` tinyint(3) unsigned DEFAULT NULL,
  `option_value` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=548 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


-- Dumping data for table its.cmsg_field_option: ~20 rows (approximately)
/*!40000 ALTER TABLE `cmsg_field_option` DISABLE KEYS */;
INSERT INTO `cmsg_field_option` (`id`, `table_name`, `table_field`, `option_index`, `option_value`) VALUES
	(1, 'cmsg_user', 'cmsg_user_rights', 0, '1'),
	(2, 'cmsg_user', 'cmsg_user_rights', 1, '2'),
	(3, 'cmsg_user', 'cmsg_user_rights', 2, '3'),
	(4, 'cmsg_user', 'cmsg_user_is_delete', 0, '0'),
	(5, 'cmsg_user', 'cmsg_user_is_delete', 1, '1'),
	(6, 'sub_user', 'sub_user_gender', 0, '請選擇'),
	(7, 'sub_user', 'sub_user_gender', 1, '女'),
	(8, 'sub_user', 'sub_user_gender', 2, '男'),
	(9, 'sub_user', 'sub_user_checkbox', 0, 'aaa'),
	(10, 'sub_user', 'sub_user_checkbox', 1, 'bbb'),
	(11, 'sub_user', 'sub_user_is_delete', 0, '0'),
	(12, 'sub_user', 'sub_user_is_delete', 1, '1'),
	(13, 'user', 'user_gender', 0, '請選擇'),
	(14, 'user', 'user_gender', 1, '女'),
	(15, 'user', 'user_gender', 2, '男'),
	(16, 'user', 'user_checkbox', 0, 'aaa'),
	(17, 'user', 'user_checkbox', 1, 'bbb'),
	(18, 'user', 'user_checkbox', 2, 'ccc'),
	(19, 'user', 'user_is_delete', 0, '0'),
	(20, 'user', 'user_is_delete', 1, '1');
/*!40000 ALTER TABLE `cmsg_field_option` ENABLE KEYS */;

-- Dumping structure for table its.cmsg_field_type
CREATE TABLE IF NOT EXISTS `cmsg_field_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `table_name` varchar(50) COLLATE utf8_bin NOT NULL,
  `table_field` varchar(50) COLLATE utf8_bin NOT NULL,
  `type_value` tinyint(3) unsigned DEFAULT NULL,
  `is_checked` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1746 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- Dumping structure for table its.cmsg_table_fk
CREATE TABLE IF NOT EXISTS `cmsg_table_fk` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `table_name` varchar(50) COLLATE utf8_bin NOT NULL,
  `table_field` varchar(50) COLLATE utf8_bin NOT NULL,
  `fk_table_name` varchar(50) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=125 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- Dumping structure for table its.cmsg_user
CREATE TABLE IF NOT EXISTS `cmsg_user` (
  `cuid` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `loginname` varchar(32) COLLATE utf8_bin DEFAULT NULL,
  `realname` varchar(32) COLLATE utf8_bin DEFAULT NULL,
  `email` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `password` varchar(32) COLLATE utf8_bin DEFAULT NULL,
  `rights` tinyint(3) unsigned DEFAULT NULL,
  `create_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `is_delete` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`cuid`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


-- Dumping data for table its.cmsg_user: ~3 rows (approximately)
/*!40000 ALTER TABLE `cmsg_user` DISABLE KEYS */;
INSERT INTO `cmsg_user` (`cuid`, `loginname`, `realname`, `email`, `password`, `rights`, `create_time`, `update_time`, `is_delete`) VALUES
	(1, 'pasco', 'pascocai', 'pascocai@nmg.com.hk', 'e10adc3949ba59abbe56e057f20f883e', 1, '2012-12-12 15:47:11', '2012-12-12 15:53:30', 0),
	(2, 'aaa', 'aaa', 'aaa', 'e10adc3949ba59abbe56e057f20f883e', 2, '2012-12-12 15:54:24', '2012-12-25 16:48:27', 0),
	(3, 'bbb', 'bbb', 'bbb', 'e10adc3949ba59abbe56e057f20f883e', 3, '2012-12-25 16:39:06', '2012-12-25 16:49:33', 0);
/*!40000 ALTER TABLE `cmsg_user` ENABLE KEYS */;
/*!40014 SET FOREIGN_KEY_CHECKS=1 */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
