-- -----------------------------
-- SentCMS MySQL Data Transfer 
-- 
-- Host     : 127.0.0.1
-- Port     : 3306
-- Database : bs
-- 
-- Part : #1
-- Date : 2017-04-23 13:54:37
-- -----------------------------

SET FOREIGN_KEY_CHECKS = 0;


-- -----------------------------
-- Table structure for `think_admin`
-- -----------------------------
DROP TABLE IF EXISTS `think_admin`;
CREATE TABLE `think_admin` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '管理员ID',
  `username` varchar(20) NOT NULL COMMENT '管理员账号',
  `nickname` varchar(20) DEFAULT NULL COMMENT '管理员昵称',
  `password` varchar(100) NOT NULL COMMENT '管理员密码',
  `avatar` varchar(100) DEFAULT NULL COMMENT '头像图片路径',
  `email` varchar(100) DEFAULT NULL COMMENT '邮箱',
  `mobile` varchar(11) DEFAULT NULL COMMENT '手机号',
  `qq` varchar(100) DEFAULT NULL COMMENT 'QQ号',
  `login_time` datetime NOT NULL COMMENT '登录时间',
  `login_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '登录次数',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `update_time` datetime NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `id` (`id`,`username`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- -----------------------------
-- Records of `think_admin`
-- -----------------------------
INSERT INTO `think_admin` VALUES ('1', 'gh7968522', '123456', '123456', '', '123456@qq.com', '', '', '0000-00-00 00:00:00', '0', '2017-04-21 18:30:02', '2017-04-21 18:30:02');
INSERT INTO `think_admin` VALUES ('2', 'hades', '黄', '123456', '', '123456@qq.com', '', '', '0000-00-00 00:00:00', '0', '2017-04-21 18:31:39', '2017-04-21 18:31:39');
