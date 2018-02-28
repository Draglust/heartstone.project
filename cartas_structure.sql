/*
Navicat MySQL Data Transfer

Source Server         : Local
Source Server Version : 50719
Source Host           : localhost:3306
Source Database       : cartas

Target Server Type    : MYSQL
Target Server Version : 50719
File Encoding         : 65001

Date: 2018-02-28 16:54:57
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of migrations
-- ----------------------------
INSERT INTO `migrations` VALUES ('1', '2018_01_20_234741_create_auction_table', '1');
INSERT INTO `migrations` VALUES ('2', '2018_01_22_232941_create_urljson_table', '1');

-- ----------------------------
-- Table structure for objeto
-- ----------------------------
DROP TABLE IF EXISTS `objeto`;
CREATE TABLE `objeto` (
  `Id` bigint(20) NOT NULL,
  `Nombre` varchar(150) COLLATE utf8_bin NOT NULL,
  `Descripcion` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  `Icono` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  UNIQUE KEY `Id` (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- ----------------------------
-- Records of objeto
-- ----------------------------

-- ----------------------------
-- Table structure for subasta
-- ----------------------------
DROP TABLE IF EXISTS `subasta`;
CREATE TABLE `subasta` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `auc` int(11) NOT NULL,
  `item` int(11) NOT NULL,
  `owner` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ownerRealm` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bid` int(11) NOT NULL,
  `buyout` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `timeLeft` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rand` int(11) NOT NULL,
  `seed` int(11) NOT NULL,
  `context` int(11) NOT NULL,
  `petSpeciesId` int(11) NOT NULL,
  `petBreedId` int(11) NOT NULL,
  `petLevel` int(11) NOT NULL,
  `petQualityId` int(11) NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `auctions_id_unique` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of subasta
-- ----------------------------

-- ----------------------------
-- Table structure for urljson
-- ----------------------------
DROP TABLE IF EXISTS `urljson`;
CREATE TABLE `urljson` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `dateNum` int(11) NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `urljson_id_unique` (`id`),
  UNIQUE KEY `urljson_url_unique` (`url`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of urljson
-- ----------------------------

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES ('1', 'Draglust', 'lukemediomuerto@gmail.com', '$2y$10$r8/eMMX7MhuMCQTDwy2tb.tODQEMWqazouiQLno0g.nRqBAvWWVBK', 'vuwG77ZnepoaH41oZU2nk2NwfUbWMG0Pqu1tE2PXhdaZvTCo06q3fKWBQ8E3', '2017-10-17 20:55:42', '2017-10-17 20:55:42');
