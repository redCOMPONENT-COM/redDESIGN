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
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;


--
-- Table structure for table `#__reddesign_designs`
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
	`reddesign_background_id` int(11) NOT NULL,
	PRIMARY KEY (`reddesign_design_id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

--
-- Table structure for table `#__reddesign_backgrounds`
--

CREATE TABLE IF NOT EXISTS `#__reddesign_backgrounds` (
	`reddesign_background_id` int(11) NOT NULL AUTO_INCREMENT,
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
	`area_x1` int(11) NOT NULL COMMENT 'x coordinate top left corner',
	`area_y1` int(11) NOT NULL COMMENT 'y coordinate top left corner',
	`area_x2` int(11) NOT NULL COMMENT 'x coordinate bottom right corner',
	`area_y2` int(11) NOT NULL COMMENT 'y coordinate bottom right corner',
	`area_width` int(11) NOT NULL COMMENT 'selection width',
	`area_height` int(11) NOT NULL COMMENT 'selection height',
	`reddesign_design_id` int(11) NOT NULL COMMENT 'foreing key of #__reddesign_designs',
	`fontcolors` VARCHAR (255)  COMMENT 'list of available colors',
	PRIMARY KEY (`reddesign_background_id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

-- -----------------------------------------------------
-- Table `default_schema`.`#__reddesign_backgroundfonts`
-- xref of fonts and backgrounds
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__reddesign_backgrounds_fonts` (
	`reddesign_background_id` INT(11) NOT NULL COMMENT 'foreing key of #__reddesign_backgrounds',
	`reddesign_font_id` INT(11) NOT NULL COMMENT 'foreing key of #__reddesign_fonts',
	PRIMARY KEY (`reddesign_background_id`, `reddesign_font_id`) ,
	FOREIGN KEY (`reddesign_background_id` )
		REFERENCES `#__reddesign_backgrounds` (`reddesign_background_id` )
			ON DELETE CASCADE
			ON UPDATE NO ACTION,
	FOREIGN KEY (`reddesign_font_id` )
		REFERENCES `#__reddesign_fonts` (`reddesign_font_id` )
			ON DELETE CASCADE
			ON UPDATE NO ACTION
) ENGINE = InnoDB DEFAULT COLLATE=utf8_general_ci;
