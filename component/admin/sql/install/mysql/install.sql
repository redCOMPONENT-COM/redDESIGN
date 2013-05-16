--
-- Table structure for table `#__reddesign_area`
--

CREATE TABLE IF NOT EXISTS `#__reddesign_area` (
  `area_id` int(11) NOT NULL auto_increment,
  `area_name` varchar(255) NOT NULL,
  `offsetLeft` double NOT NULL,
  `offsetTop` double NOT NULL,
  `height` int(11) NOT NULL,
  `width` int(11) NOT NULL,
  `font_size` text NOT NULL,
  `font_name` text NOT NULL,
  `color_code` text NOT NULL,
  `x_pos` int(11) NOT NULL,
  `y_pos` int(11) NOT NULL,
  `textalign` int(11) NOT NULL,
  `image_id` int(11) NOT NULL,
  PRIMARY KEY  (`area_id`)
);

--
-- Table structure for table `#__reddesign_designtype`
--

CREATE TABLE IF NOT EXISTS `#__reddesign_designtype` (
  `designtype_id` int(11) NOT NULL auto_increment,
  `designtype_name` varchar(255) NOT NULL,
  `designtemplate` bigint(20) NOT NULL,
  `reddesign_autotemplate` tinyint(4) NOT NULL default '1',
  `published` tinyint(4) NOT NULL,
  PRIMARY KEY  (`designtype_id`)
);


--
-- Table structure for table `#__reddesign_image`
--

CREATE TABLE IF NOT EXISTS `#__reddesign_image` (
 `image_id` int(11) NOT NULL auto_increment,
  `image_name` varchar(255) NOT NULL,
  `eps_file` text NOT NULL,
  `image_path` text NOT NULL,
  `designtype_id` int(11) NOT NULL,
  `ordering` int(11) NOT NULL,
  `published` tinyint(4) NOT NULL,
  PRIMARY KEY  (`image_id`)
);

--
-- Table structure for table `#__reddesign_redshop`
--

CREATE TABLE IF NOT EXISTS `#__reddesign_redshop` (
 `id` bigint(20) NOT NULL auto_increment,
  `product_id` bigint(20) NOT NULL,
  `designtype_id` bigint(20) NOT NULL,
  `shoppergroups` varchar(255) NOT NULL,
  `reddesign_enable` tinyint(4) NOT NULL,
  PRIMARY KEY  (`id`)
);

--
-- Table structure for table `#__reddesign_order`
--

CREATE TABLE IF NOT EXISTS `#__reddesign_order` (
  `id` bigint(20) NOT NULL auto_increment,
  `order_id` bigint(20) NOT NULL,
  `product_id` bigint(20) NOT NULL,
  `reddesignfile` varchar(255) NOT NULL,
  `designhdnargs` text NOT NULL,
  PRIMARY KEY  (`id`)
);

--
-- Table structure for table `#__reddesign_template`
--

CREATE TABLE IF NOT EXISTS `#__reddesign_template` (
   `template_id` int(11) NOT NULL auto_increment,
  `template_name` varchar(250) NOT NULL,
  `template_section` varchar(250) NOT NULL,
  `template_desc` longtext NOT NULL,
  `published` tinyint(4) NOT NULL,
  PRIMARY KEY  (`template_id`)
);

--
-- Table structure for table `#__reddesign_config`
--

CREATE TABLE IF NOT EXISTS `#__reddesign_config` (
   `id` int(11) NOT NULL auto_increment,
  `show_areaname` tinyint(4) NOT NULL,
  `show_area_border` tinyint(4) NOT NULL,
  `show_areaname_backend` tinyint(4) NOT NULL,
  `show_preview_text` tinyint(4) NOT NULL,
  `preview_text_size` tinyint(4) NOT NULL,
  PRIMARY KEY  (`id`)
);

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
  `default_width` VARCHAR (255),
  `default_height` VARCHAR (255),
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
	`reddesign_design_id` int(11) NOT NULL COMMENT 'foreing key of #__reddesign_designs',
	PRIMARY KEY (`reddesign_designbackground_id`)
) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci;