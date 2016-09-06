-- phpMyAdmin SQL Dump
-- version 3.3.8.1
-- http://www.phpmyadmin.net
--
-- 主机: w.rdc.sae.sina.com.cn:3307
-- 生成日期: 2016 年 09 月 06 日 14:41
-- 服务器版本: 5.6.23
-- PHP 版本: 5.3.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT=0;
START TRANSACTION;


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `app_qbook`
--

-- --------------------------------------------------------

--
-- 表的结构 `QB_Book`
--

CREATE TABLE IF NOT EXISTS `QB_Book` (
  `CorpCode_` varchar(10) NOT NULL COMMENT '企业代码',
  `TBDate_` date NOT NULL,
  `Subject_` varchar(80) NOT NULL,
  `DrCode_` varchar(10) DEFAULT NULL,
  `CrCode_` varchar(10) DEFAULT NULL,
  `Amount_` float(18,4) NOT NULL,
  `Remark_` varchar(255) DEFAULT NULL,
  `UpdateUser_` varchar(30) NOT NULL,
  `UpdateDate_` datetime NOT NULL,
  `AppUser_` varchar(30) NOT NULL,
  `AppDate_` datetime NOT NULL,
  `UpdateKey_` varchar(36) NOT NULL,
  UNIQUE KEY `UpdateKey_` (`UpdateKey_`),
  KEY `CorpCode_` (`CorpCode_`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `QB_Class`
--

CREATE TABLE IF NOT EXISTS `QB_Class` (
  `Code_` varchar(10) NOT NULL COMMENT '科目类别',
  `Name_` varchar(30) NOT NULL COMMENT '类别名称',
  `Remark_` varchar(80) DEFAULT NULL,
  `UpdateUser_` varchar(10) NOT NULL,
  `UpdateDate_` datetime NOT NULL,
  `AppUser_` varchar(30) NOT NULL,
  `AppDate_` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdateKey_` varchar(36) NOT NULL,
  PRIMARY KEY (`Code_`),
  UNIQUE KEY `UpdateKey_` (`UpdateKey_`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `QB_Code`
--

CREATE TABLE IF NOT EXISTS `QB_Code` (
  `CorpCode_` varchar(10) NOT NULL COMMENT '企业代码',
  `ParentCode_` varchar(80) NOT NULL,
  `Class_` varchar(10) DEFAULT NULL,
  `Code_` varchar(10) DEFAULT NULL,
  `Name_` varchar(50) DEFAULT NULL,
  `Remark_` varchar(255) DEFAULT NULL,
  `UpdateUser_` varchar(30) NOT NULL,
  `UpdateDate_` datetime NOT NULL,
  `AppUser_` varchar(30) NOT NULL,
  `AppDate_` datetime NOT NULL,
  `UpdateKey_` varchar(36) NOT NULL,
  UNIQUE KEY `UpdateKey_` (`UpdateKey_`),
  KEY `CorpCode_` (`CorpCode_`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='会计科目代码表';

-- --------------------------------------------------------

--
-- 表的结构 `QB_Person`
--

CREATE TABLE IF NOT EXISTS `QB_Person` (
  `CorpCode_` varchar(10) NOT NULL COMMENT '公司代码',
  `DeptName_` varchar(30) NOT NULL,
  `PersonName_` varchar(30) DEFAULT NULL,
  `Province_` varchar(30) DEFAULT NULL,
  `Phone_` varchar(30) DEFAULT NULL,
  `InDate_` date DEFAULT NULL,
  `OutDate_` date DEFAULT NULL,
  `BankCode_` varchar(30) DEFAULT NULL,
  `Remark_` varchar(255) DEFAULT NULL,
  `UpdateUser_` varchar(30) NOT NULL,
  `UpdateDate_` datetime NOT NULL,
  `AppUser_` varchar(30) NOT NULL,
  `AppDate_` datetime NOT NULL,
  `UpdateKey_` varchar(36) NOT NULL,
  UNIQUE KEY `UpdateKey_` (`UpdateKey_`),
  KEY `CorpCode_` (`CorpCode_`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='员工人事档案表';

-- --------------------------------------------------------

--
-- 表的结构 `QB_Record`
--

CREATE TABLE IF NOT EXISTS `QB_Record` (
  `CorpCode_` varchar(10) NOT NULL COMMENT '企业代码',
  `TBDate_` date NOT NULL,
  `Subject_` varchar(80) DEFAULT NULL,
  `Amount_` float(18,2) DEFAULT NULL,
  `ToBook_` bit(1) DEFAULT NULL,
  `UpdateUser_` varchar(30) NOT NULL,
  `UpdateDate_` datetime NOT NULL,
  `AppUser_` varchar(30) NOT NULL,
  `AppDate_` datetime NOT NULL,
  `UpdateKey_` varchar(36) NOT NULL,
  UNIQUE KEY `UpdateKey_` (`UpdateKey_`),
  KEY `CorpCode_` (`CorpCode_`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `QB_Salary`
--

CREATE TABLE IF NOT EXISTS `QB_Salary` (
  `CorpCode_` varchar(10) NOT NULL COMMENT '公司代码',
  `YearMonth_` int(11) NOT NULL,
  `DeptName_` varchar(30) NOT NULL,
  `PersonName_` varchar(30) DEFAULT NULL,
  `InDate_` date DEFAULT NULL,
  `OutDate_` date DEFAULT NULL,
  `BankCode_` varchar(30) DEFAULT NULL,
  `Amount1_` float(18,2) NOT NULL,
  `Amount2_` float(18,2) NOT NULL,
  `AddDiff_` float(18,2) NOT NULL,
  `Amount3_` float(18,2) NOT NULL,
  `Amount_` float(18,2) DEFAULT NULL,
  `Remark_` varchar(255) DEFAULT NULL,
  `UpdateUser_` varchar(30) NOT NULL,
  `UpdateDate_` datetime NOT NULL,
  `AppUser_` varchar(30) NOT NULL,
  `AppDate_` datetime NOT NULL,
  `UpdateKey_` varchar(36) NOT NULL,
  UNIQUE KEY `UpdateKey_` (`UpdateKey_`),
  KEY `CorpCode_` (`CorpCode_`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='员工薪资总表';

-- --------------------------------------------------------

--
-- 表的结构 `QB_Type`
--

CREATE TABLE IF NOT EXISTS `QB_Type` (
  `CorpCode_` varchar(10) NOT NULL COMMENT '组织代码',
  `Class_` varchar(10) NOT NULL,
  `Title_` varchar(80) NOT NULL,
  `DrCode_` varchar(10) DEFAULT NULL,
  `CrCode_` varchar(10) DEFAULT NULL,
  `Remark_` varchar(255) DEFAULT NULL,
  `UpdateUser_` varchar(30) NOT NULL,
  `UpdateDate_` datetime NOT NULL,
  `AppUser_` varchar(30) NOT NULL,
  `AppDate_` datetime NOT NULL,
  `UpdateKey_` varchar(36) NOT NULL,
  UNIQUE KEY `UpdateKey_` (`UpdateKey_`),
  KEY `CorpCode_` (`CorpCode_`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='常用交易行为类别表';

-- --------------------------------------------------------

--
-- 表的结构 `WF_CusList`
--

CREATE TABLE IF NOT EXISTS `WF_CusList` (
  `Code_` varchar(10) NOT NULL COMMENT '企业代码',
  `ShortName_` varchar(30) NOT NULL,
  `Name_` varchar(80) NOT NULL,
  `RegDate_` date NOT NULL,
  `Remark_` varchar(255) DEFAULT NULL,
  `UpdateUser_` varchar(30) NOT NULL,
  `UpdateDate_` datetime NOT NULL,
  `AppUser_` varchar(30) NOT NULL,
  `AppDate_` datetime NOT NULL,
  `UpdateKey_` varchar(36) NOT NULL,
  UNIQUE KEY `UpdateKey_` (`UpdateKey_`),
  KEY `Code_` (`Code_`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='企业代码表';

-- --------------------------------------------------------

--
-- 表的结构 `WF_Diary`
--

CREATE TABLE IF NOT EXISTS `WF_Diary` (
  `CorpCode_` varchar(10) NOT NULL COMMENT '企业代码',
  `TBDate_` date NOT NULL,
  `Contents_` varchar(255) DEFAULT NULL,
  `UpdateUser_` varchar(30) NOT NULL,
  `UpdateDate_` datetime NOT NULL,
  `AppUser_` varchar(30) NOT NULL,
  `AppDate_` datetime NOT NULL,
  `UpdateKey_` varchar(36) NOT NULL,
  UNIQUE KEY `UpdateKey_` (`UpdateKey_`),
  KEY `CorpCode_` (`CorpCode_`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `WF_Messages`
--

CREATE TABLE IF NOT EXISTS `WF_Messages` (
  `ID_` char(36) NOT NULL,
  `TalkTime_` datetime NOT NULL,
  `TalkUser_` varchar(30) NOT NULL,
  `Type_` tinyint(4) NOT NULL,
  `Subject_` varchar(80) NOT NULL,
  `SendOK_` tinyint(4) NOT NULL,
  `SendTime_` datetime DEFAULT NULL,
  `SendError_` text,
  PRIMARY KEY (`ID_`),
  KEY `TalkUser_` (`TalkUser_`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `WF_UserInfo`
--

CREATE TABLE IF NOT EXISTS `WF_UserInfo` (
  `UserCode_` varchar(30) NOT NULL COMMENT '用户编号,员工编号',
  `CorpCode_` varchar(10) NOT NULL COMMENT '关联企业表的ID,对应所属企业',
  `DeptName_` varchar(30) NOT NULL,
  `UserName_` varchar(30) NOT NULL COMMENT '用户姓名',
  `UserPasswd_` varchar(32) NOT NULL,
  `QQ_` varchar(50) NOT NULL COMMENT '用户QQ',
  `EmailUse_` tinyint(1) NOT NULL DEFAULT '0',
  `Email_` varchar(80) DEFAULT NULL,
  `SMSUse_` tinyint(1) NOT NULL DEFAULT '0',
  `SMSNo_` varchar(20) DEFAULT NULL,
  `Level_` tinyint(1) NOT NULL DEFAULT '2' COMMENT '0=>超级用户, 1=>企业管理员, 2=>普通用户',
  `LoginTime_` datetime DEFAULT NULL,
  `Remark_` varchar(255) NOT NULL COMMENT '用户介绍说明',
  `Enabled_` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否启用该用户,0=>不启用,1=>已启用 未启用之用户，不得登入到系统中',
  `UpdateUser_` varchar(30) NOT NULL COMMENT '最近更新用户',
  `UpdateDate_` datetime NOT NULL COMMENT '最近更新时间',
  `AppUser_` varchar(30) NOT NULL COMMENT '创建人用户ID',
  `AppDate_` datetime NOT NULL COMMENT '创建文档的时间',
  `UpdateKey_` char(36) NOT NULL COMMENT '更新标识 ',
  PRIMARY KEY (`UserCode_`,`CorpCode_`),
  UNIQUE KEY `UpdateKey_` (`UpdateKey_`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户讯息表';
COMMIT;
