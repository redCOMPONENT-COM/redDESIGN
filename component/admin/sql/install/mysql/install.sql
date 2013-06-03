--
-- NEW SCHEMA (new FoF redDESIGN)
--



--
-- Table structure for table `#_reddesign_fonts`
--

CREATE TABLE IF NOT EXISTS `#__reddesign_fonts` (
  `reddesign_font_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` VARCHAR (255),
  `slug` VARCHAR (255),
  `enabled` tinyint(3) NOT NULL DEFAULT '1',
  `ordering` int(10) NOT NULL DEFAULT '0',
  `created_by` bigint(20) NOT NULL DEFAULT '0',
  `created_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` bigint(20) NOT NULL DEFAULT '0',
  `modified_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `locked_by` bigint(20) NOT NULL DEFAULT '0',
  `locked_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `fontfile` VARCHAR(255)  NOT NULL,
	`fontthumb` varchar(255) NOT NULL,
   PRIMARY KEY (`reddesign_font_id`)
) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci;


--
-- Table structure for table `#_reddesign_fonts`
--

CREATE TABLE IF NOT EXISTS `#__reddesign_designs` (
	`reddesign_design_id` int(11) NOT NULL AUTO_INCREMENT,
	`title` VARCHAR (255),
	`slug` VARCHAR (255),
	`enabled` tinyint(3) NOT NULL DEFAULT '1',
	`ordering` int(10) NOT NULL DEFAULT '0',
	`created_by` bigint(20) NOT NULL DEFAULT '0',
	`created_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	`modified_by` bigint(20) NOT NULL DEFAULT '0',
	`modified_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	`locked_by` bigint(20) NOT NULL DEFAULT '0',
	`locked_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY (`reddesign_design_id`)
) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci;

--
-- Table structure for table `#_reddesign_fonts`
--

CREATE TABLE IF NOT EXISTS `#__reddesign_designbackgrounds` (
	`reddesign_designbackground_id` int(11) NOT NULL AUTO_INCREMENT,
	`title` VARCHAR (255),
	`slug` VARCHAR (255),
	`enabled` tinyint(3) NOT NULL DEFAULT '1',
	`ordering` int(10) NOT NULL DEFAULT '0',
	`created_by` bigint(20) NOT NULL DEFAULT '0',
	`created_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	`modified_by` bigint(20) NOT NULL DEFAULT '0',
	`modified_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	`locked_by` bigint(20) NOT NULL DEFAULT '0',
	`locked_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	`epsfile` VARCHAR(255)  NOT NULL,
	`jpegpreviewfile` varchar(255) NOT NULL,
	`design` int(11) NOT NULL COMMENT 'foreing key of #__reddesign_designs',
	PRIMARY KEY (`reddesign_designbackground_id`)
) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci;

--
-- Table structure for table `#__reddesign_backgroundfonts`
--
CREATE TABLE IF NOT EXISTS `#__reddesign_backgroundfonts`(
	`reddesign_designbackground_id` int(11) NOT NULL COMMENT 'foreing key of #__reddesign_designbackgrounds',
	`reddesign_font_id` int(11) NOT NULL COMMENT 'foreing key of #__reddesign_fonts',
	PRIMARY KEY (  `reddesign_designbackground_id` ,  `reddesign_font_id` ),
	INDEX (  `reddesign_designbackground_id` ),
	INDEX (  `reddesign_font_id` )
) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci;
