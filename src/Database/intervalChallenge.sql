/*
 Navicat Premium Data Transfer

 Source Server         : SEIR3
 Source Server Type    : MySQL
 Source Server Version : 50723
 Source Host           : localhost:3306
 Source Schema         : intervalChallenge

 Target Server Type    : MySQL
 Target Server Version : 50723
 File Encoding         : 65001

 Date: 21/04/2019 01:18:40
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for intervals
-- ----------------------------
DROP TABLE IF EXISTS `intervals`;
CREATE TABLE `intervals` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'primary key',
  `date_start` date DEFAULT NULL,
  `date_end` date DEFAULT NULL,
  `price` float DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;
