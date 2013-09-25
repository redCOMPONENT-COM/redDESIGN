SET foreign_key_checks = 0;

--
-- Table structure for table `#__reddesign_area`
--
CREATE TABLE IF NOT EXISTS `#__reddesign_areas` (
  `reddesign_area_id`         INT(11)     NOT NULL AUTO_INCREMENT,
  `title`                     VARCHAR(255),
  `slug`                      VARCHAR(255),
  `enabled`                   TINYINT(3)  NOT NULL DEFAULT '1',
  `ordering`                  INT(10)     NOT NULL DEFAULT '0',
  `created_by`                BIGINT(20)  NOT NULL DEFAULT '0',
  `created_on`                DATETIME    NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by`               BIGINT(20)  NOT NULL DEFAULT '0',
  `modified_on`               DATETIME    NOT NULL DEFAULT '0000-00-00 00:00:00',
  `locked_by`                 BIGINT(20)  NOT NULL DEFAULT '0',
  `locked_on`                 DATETIME    NOT NULL DEFAULT '0000-00-00 00:00:00',
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
  `reddesign_background_id`   INT(11)     NOT NULL,
  `maxchar`                   INT(10)     NOT NULL,
  `defaultFontSize`           INT(11)     NOT NULL,
  `minFontSize`               INT(11)     NOT NULL,
  `maxFontSize`               INT(11)     NOT NULL,
  `maxline`                   INT(150)    NOT NULL,
  `input_field_type`          TINYINT(3)  NOT NULL DEFAULT '0',
  PRIMARY KEY (`reddesign_area_id`),
  FOREIGN KEY (`reddesign_background_id`)
    REFERENCES `#__reddesign_backgrounds` (`reddesign_background_id`)
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
  `reddesign_designtype_id` INT(11)    NOT NULL AUTO_INCREMENT,
  `title`                   VARCHAR(255),
  `slug`                    VARCHAR(255),
  `enabled`                 TINYINT(3) NOT NULL DEFAULT '1',
  `ordering`                INT(10)    NOT NULL DEFAULT '0',
  `created_by`              BIGINT(20) NOT NULL DEFAULT '0',
  `created_on`              DATETIME   NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by`             BIGINT(20) NOT NULL DEFAULT '0',
  `modified_on`             DATETIME   NOT NULL DEFAULT '0000-00-00 00:00:00',
  `locked_by`               BIGINT(20) NOT NULL DEFAULT '0',
  `locked_on`               DATETIME   NOT NULL DEFAULT '0000-00-00 00:00:00',
  `fontsizer`               ENUM('auto', 'slider', 'dropdown'),
  `intro_description`       TEXT,
  `description`             TEXT,
  `sample_image`            VARCHAR(255),
  `sample_thumb`            VARCHAR(255),
  `related_designtypes`     VARCHAR(255),
  PRIMARY KEY (`reddesign_designtype_id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  DEFAULT COLLATE = utf8_general_ci;

--
-- Table structure for table `#__reddesign_font`
--
CREATE TABLE IF NOT EXISTS `#__reddesign_fonts` (
  `reddesign_font_id`       INT(11)      NOT NULL AUTO_INCREMENT,
  `title`                   VARCHAR(255),
  `slug`                    VARCHAR(255),
  `enabled`                 TINYINT(3)   NOT NULL DEFAULT '1',
  `ordering`                INT(10)      NOT NULL DEFAULT '0',
  `created_by`              BIGINT(20)   NOT NULL DEFAULT '0',
  `created_on`              DATETIME     NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by`             BIGINT(20)   NOT NULL DEFAULT '0',
  `modified_on`             DATETIME     NOT NULL DEFAULT '0000-00-00 00:00:00',
  `locked_by`               BIGINT(20)   NOT NULL DEFAULT '0',
  `locked_on`               DATETIME     NOT NULL DEFAULT '0000-00-00 00:00:00',
  `font_file`               VARCHAR(255) NOT NULL,
  `font_thumb`              VARCHAR(255) NOT NULL,
  `default_width`           DOUBLE(5, 5) NOT NULL,
  `default_height`          DOUBLE(5, 5) NOT NULL,
  `default_caps_height`     DOUBLE(5, 5) NOT NULL,
  `default_baseline_height` DOUBLE(5, 5) NOT NULL,
  PRIMARY KEY (`reddesign_font_id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  DEFAULT COLLATE = utf8_general_ci;

--
-- Table structure for table `#__reddesign_font_char`
--
CREATE TABLE IF NOT EXISTS `#__reddesign_chars` (
  `reddesign_char_id` INT(11)      NOT NULL AUTO_INCREMENT,
  `font_char`              VARCHAR(10)  NOT NULL,
  `width`                  DOUBLE(5, 5) NOT NULL,
  `height`                 DOUBLE(5, 5) NOT NULL,
  `typography`             INT(11)      NOT NULL DEFAULT '1',
  `typography_height`      DOUBLE(5, 5) NOT NULL,
  `reddesign_font_id`      INT(11)      NOT NULL,
  PRIMARY KEY (`reddesign_char_id`),
  FOREIGN KEY (`reddesign_font_id` )
    REFERENCES `#__reddesign_fonts` (`reddesign_font_id` )
      ON DELETE CASCADE
      ON UPDATE NO ACTION
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  DEFAULT COLLATE = utf8_general_ci;

--
-- Table structure for table `#__reddesign_backgrounds`
--
CREATE TABLE IF NOT EXISTS `#__reddesign_backgrounds` (
  `reddesign_background_id` INT(11)      NOT NULL AUTO_INCREMENT,
  `title`                   VARCHAR(255),
  `slug`                    VARCHAR(255),
  `enabled`                 TINYINT(3)   NOT NULL DEFAULT '1',
  `ordering`                INT(10)      NOT NULL DEFAULT '0',
  `created_by`              BIGINT(20)   NOT NULL DEFAULT '0',
  `created_on`              DATETIME     NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by`             BIGINT(20)   NOT NULL DEFAULT '0',
  `modified_on`             DATETIME     NOT NULL DEFAULT '0000-00-00 00:00:00',
  `locked_by`               BIGINT(20)   NOT NULL DEFAULT '0',
  `locked_on`               DATETIME     NOT NULL DEFAULT '0000-00-00 00:00:00',
  `eps_file`                VARCHAR(255),
  `image_path`              VARCHAR(255) NOT NULL,
  `thumbnail`               VARCHAR(255),
  `isPDFbgimage`            TINYINT(1)   NOT NULL,
  `isPreviewbgimage`        TINYINT(1)   NOT NULL,
  `reddesign_designtype_id` INT(11)      NOT NULL,
  PRIMARY KEY (`reddesign_background_id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  DEFAULT COLLATE = utf8_general_ci;

--
-- Table structure for table `#__reddesign_product_mapping`
--
CREATE TABLE IF NOT EXISTS `#__reddesign_product_mapping` (
  `reddesign_designtype_id` INT(11) NOT NULL,
  `product_id`              INT(11) NOT NULL,
  PRIMARY KEY (`reddesign_designtype_id`, `product_id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  DEFAULT COLLATE = utf8_general_ci;

SET foreign_key_checks = 1;