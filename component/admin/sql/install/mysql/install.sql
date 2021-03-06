SET foreign_key_checks = 0;

--
-- Table structure for table `#__reddesign_areas`
--
CREATE TABLE IF NOT EXISTS `#__reddesign_areas` (
  `id`                        INT(11)     NOT NULL AUTO_INCREMENT,
  `areaType`                  TINYINT(2)  NOT NULL DEFAULT '1' COMMENT '1 = Text, 2 = Clipart',
  `name`                      VARCHAR(255),
  `alias`                     VARCHAR(255),
  `state`                     TINYINT(3)  NOT NULL DEFAULT '1',
  `ordering`                  INT(10)     NOT NULL DEFAULT '0',
  `created_by`                INT(11)     DEFAULT NULL,
  `created_date`              DATETIME    NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by`               INT(11)     DEFAULT NULL,
  `modified_data`             DATETIME    NOT NULL DEFAULT '0000-00-00 00:00:00',
  `checked_out`               INT(11)     DEFAULT NULL,
  `checked_out_time`          DATETIME    NOT NULL DEFAULT '0000-00-00 00:00:00',
  `x1_pos`                    INT(11)     NOT NULL,
  `y1_pos`                    INT(11)     NOT NULL,
  `x2_pos`                    INT(11)     NOT NULL,
  `y2_pos`                    INT(11)     NOT NULL,
  `width`                     INT(11)     NOT NULL,
  `height`                    INT(11)     NOT NULL,
  `font_size`                 TEXT        NOT NULL,
  `font_id`                   TEXT        NOT NULL,
  `color_code`                TEXT        NOT NULL,
  `default_text`              TEXT,
  `textalign`                 INT(11)     NOT NULL,
  `verticalAlign`             VARCHAR(10) NOT NULL DEFAULT 'top',
  `background_id`             INT(11)     NOT NULL,
  `maxchar`                   INT(10)     NOT NULL,
  `defaultFontSize`           INT(11)     NOT NULL,
  `minFontSize`               INT(11)     NOT NULL,
  `maxFontSize`               INT(11)     NOT NULL,
  `maxline`                   INT(150)    NOT NULL,
  `input_field_type`          TINYINT(3)  NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  FOREIGN KEY (`background_id`)
    REFERENCES `#__reddesign_backgrounds` (`id`)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  DEFAULT COLLATE = utf8_general_ci;

--
-- Table structure for table `#__reddesign_designtype`
--
CREATE TABLE IF NOT EXISTS `#__reddesign_designtypes` (
  `id`               INT(11)       NOT NULL AUTO_INCREMENT,
  `name`             VARCHAR(255),
  `alias`            VARCHAR(255),
  `state`            TINYINT(3)    NOT NULL DEFAULT '1',
  `ordering`         INT(10)       NOT NULL DEFAULT '0',
  `created_by`       INT(11)       DEFAULT NULL,
  `created_date`     DATETIME      NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by`      INT(11)       DEFAULT NULL,
  `modified_date`    DATETIME      NOT NULL DEFAULT '0000-00-00 00:00:00',
  `checked_out`      INT(11)       DEFAULT NULL,
  `checked_out_time` DATETIME      NOT NULL DEFAULT '0000-00-00 00:00:00',
  `fontsizer`        ENUM('auto', 'slider', 'dropdown_numbers', 'dropdown_labels'),
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  DEFAULT COLLATE = utf8_general_ci;

--
-- Table structure for table `#__reddesign_fonts`
--
CREATE TABLE IF NOT EXISTS `#__reddesign_fonts` (
  `id`                      INT(11)       NOT NULL AUTO_INCREMENT,
  `name`                    VARCHAR(255),
  `state`                   TINYINT(3)    NOT NULL DEFAULT '1',
  `ordering`                INT(10)       NOT NULL DEFAULT '0',
  `created_by`              INT(11)       DEFAULT NULL,
  `created_date`            DATETIME      NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by`             INT(11)       DEFAULT NULL,
  `modified_date`           DATETIME      NOT NULL DEFAULT '0000-00-00 00:00:00',
  `checked_out`             INT(11)       DEFAULT NULL,
  `checked_out_time`        DATETIME      NOT NULL DEFAULT '0000-00-00 00:00:00',
  `font_file`               VARCHAR(255)  NOT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  DEFAULT COLLATE = utf8_general_ci;

--
-- Table structure for table `#__reddesign_backgrounds`
--
CREATE TABLE IF NOT EXISTS `#__reddesign_backgrounds` (
  `id`               INT(11)      NOT NULL AUTO_INCREMENT,
  `name`             VARCHAR(255),
  `alias`            VARCHAR(255),
  `state`            TINYINT(3)   NOT NULL DEFAULT '1',
  `ordering`         INT(10)      NOT NULL DEFAULT '0',
  `created_by`       INT(11)      DEFAULT NULL,
  `created_date`     DATETIME     NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by`      INT(11)      DEFAULT NULL,
  `modified_date`    DATETIME     NOT NULL DEFAULT '0000-00-00 00:00:00',
  `checked_out`      INT(11)      DEFAULT NULL,
  `checked_out_time` DATETIME     NOT NULL DEFAULT '0000-00-00 00:00:00',
  `svg_file`         VARCHAR(255),
  `image_path`       VARCHAR(255) NOT NULL,
  `isProductionBg`   TINYINT(1)   NOT NULL,
  `isPreviewBg`      TINYINT(1)   NOT NULL,
  `isDefaultPreview` TINYINT(1)   NOT NULL,
  `useCheckerboard`  TINYINT(1)   NOT NULL DEFAULT '0',
  `designtype_id`    INT(11)      NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`designtype_id`)
    REFERENCES `#__reddesign_designtypes` (`id`)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  DEFAULT COLLATE = utf8_general_ci;

--
-- Table structure for table `#__reddesign_product_mapping`
--
CREATE TABLE IF NOT EXISTS `#__reddesign_product_mapping` (
  `product_id`             INT(11) NOT NULL,
  `related_designtype_ids` TEXT    NOT NULL,
  PRIMARY KEY (`product_id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  DEFAULT COLLATE = utf8_general_ci;

--
-- Table structure for table `#__reddesign_attribute_mapping`
--
CREATE TABLE IF NOT EXISTS `#__reddesign_property_background_mapping` (
  `property_id`   INT(11) NOT NULL,
  `background_id` INT(11) NOT NULL,
  PRIMARY KEY (`property_id`),
  FOREIGN KEY (`property_id`) REFERENCES `#__redshop_product_attribute_property` (`property_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  FOREIGN KEY (`background_id`) REFERENCES `#__reddesign_backgrounds` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  DEFAULT COLLATE = utf8_general_ci;

--
-- Table structure for table `#__reddesign_orderitem_mapping`
--
CREATE TABLE IF NOT EXISTS `#__reddesign_orderitem_mapping` (
  `order_item_id` INT(11)      NOT NULL,
  `redDesignData` LONGTEXT     NOT NULL,
  `productionSvg` VARCHAR(255),
  PRIMARY KEY (`order_item_id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  DEFAULT COLLATE = utf8_general_ci;

--
-- Table structure for table `#__reddesign_property_area_xref`
--
CREATE TABLE IF NOT EXISTS `#__reddesign_property_area_xref` (
  `property_id`       INT(11) NOT NULL,
  `reddesign_area_id` INT(11) NOT NULL,
  `display_type`      ENUM('TEXT','FONT','FONTSIZE','COLOR') NOT NULL,
  PRIMARY KEY (`property_id`,`reddesign_area_id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  DEFAULT COLLATE = utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__reddesign_config` (
  `id`     TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `params` TEXT             NOT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  DEFAULT COLLATE = utf8_general_ci;

--
-- Table structure for table `#__reddesign_cliparts`
--
CREATE TABLE IF NOT EXISTS `#__reddesign_cliparts` (
  `id`                      INT(11)       NOT NULL AUTO_INCREMENT,
  `name`                    VARCHAR(255)  NOT NULL DEFAULT '',
  `categoryId`              INT(11)       DEFAULT NULL,
  `clipartFile`             VARCHAR(255)  NOT NULL,
  `state`                   TINYINT(3)    NOT NULL DEFAULT '1',
  `ordering`                INT(10)       NOT NULL DEFAULT '0',
  `created_by`              INT(11)       DEFAULT NULL,
  `created_date`            DATETIME      NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by`             INT(11)       DEFAULT NULL,
  `modified_date`           DATETIME      NOT NULL DEFAULT '0000-00-00 00:00:00',
  `checked_out`             INT(11)       DEFAULT NULL,
  `checked_out_time`        DATETIME      NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  DEFAULT COLLATE = utf8_general_ci;

--
-- Table structure for table `#__reddesign_area_clipart_xref`
--
CREATE TABLE IF NOT EXISTS `#__reddesign_area_clipart_xref` (
  `areaId`       INT(11) NOT NULL,
  `clipartId` INT(11) NOT NULL,
  PRIMARY KEY (`areaId`, `clipartId`),
  FOREIGN KEY (`areaId`) REFERENCES `#__reddesign_areas` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  FOREIGN KEY (`clipartId`) REFERENCES `#__reddesign_cliparts` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  DEFAULT COLLATE = utf8_general_ci;

SET foreign_key_checks = 1;