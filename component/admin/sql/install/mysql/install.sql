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
  `offsetLeft`                INT(11)     NOT NULL,
  `offsetTop`                 INT(11)     NOT NULL,
  `height`                    INT(11)     NOT NULL,
  `width`                     INT(11)     NOT NULL,
  `font_size`                 TEXT        NOT NULL,
  `font_id`                   TEXT        NOT NULL,
  `color_code`                TEXT        NOT NULL,
  `x_pos`                     INT(11)     NOT NULL,
  `y_pos`                     INT(11)     NOT NULL,
  `textalign`                 INT(11)     NOT NULL,
  `reddesign_background_id`   INT(11)     NOT NULL,
  `apply_cruv`                TINYINT(4)  NOT NULL,
  `txtangle`                  VARCHAR(4)  NOT NULL,
  `maxchar`                   INT(10)     NOT NULL,
  `defaultFontSize`           INT(11)     NOT NULL,
  `minFontSize`               INT(11)     NOT NULL,
  `maxFontSize`               INT(11)     NOT NULL,
  `maxline`                   INT(150)    NOT NULL,
  `fedcolor`                  VARCHAR(60) NOT NULL,
  PRIMARY KEY (`reddesign_area_id`)
)
  ENGINE =MyISAM
  DEFAULT CHARSET =utf8;

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
  PRIMARY KEY (`reddesign_designtype_id`)
)
  ENGINE =MyISAM
  DEFAULT CHARSET =utf8;

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
  ENGINE =InnoDB
  DEFAULT CHARSET =utf8;

--
-- Table structure for table `#__reddesign_font_char`
--

CREATE TABLE IF NOT EXISTS `#__reddesign_font_chars` (
  `reddesign_font_char_id` INT(11)      NOT NULL AUTO_INCREMENT,
  `reddesign_font_id`      INT(11)      NOT NULL,
  `font_char`              VARCHAR(10)  NOT NULL,
  `width`                  DOUBLE(5, 5) NOT NULL,
  `height`                 DOUBLE(5, 5) NOT NULL,
  `typography`             INT(11)      NOT NULL DEFAULT '1',
  `typography_height`      DOUBLE(5, 5) NOT NULL,
  PRIMARY KEY (`reddesign_font_char_id`)
)
  ENGINE =InnoDB
  DEFAULT CHARSET =utf8;

--
-- Table structure for table `#__reddesign_backgrounds`
--

CREATE TABLE IF NOT EXISTS `#__reddesign_backgrounds` (
  `reddesign_background_id` INT(11)    NOT NULL AUTO_INCREMENT,
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
  `eps_file`                TEXT NOT NULL,
  `image_path`              TEXT       NOT NULL,
  `reddesign_designtype_id` INT(11)    NOT NULL,
  `isPDFbgimage`            TINYINT(4) NOT NULL,
  PRIMARY KEY (`reddesign_background_id`)
)
  ENGINE =MyISAM
  DEFAULT CHARSET =utf8;

--
-- Table structure for table `#__reddesign_order`
--

CREATE TABLE IF NOT EXISTS `#__reddesign_orders` (
  `reddesign_order_id`        BIGINT(20)   NOT NULL AUTO_INCREMENT,
  `order_id`                  BIGINT(20)   NOT NULL,
  `product_id`                BIGINT(20)   NOT NULL,
  `order_item_id`             INT(11)      NOT NULL,
  `reddesignfile`             VARCHAR(255) NOT NULL,
  `designhdnargs`             TEXT         NOT NULL,
  `reddesign_background_id`   INT(11)      NOT NULL,
  PRIMARY KEY (`reddesign_order_id`)
)
  ENGINE =MyISAM
  DEFAULT CHARSET =utf8;

--
-- Table structure for table `#__reddesign_redshop`
--

CREATE TABLE IF NOT EXISTS `#__reddesign_redshops` (
  `reddesign_redshop_id` BIGINT(20)   NOT NULL AUTO_INCREMENT,
  `product_id`           BIGINT(20)   NOT NULL,
  `designtype_id`        BIGINT(20)   NOT NULL,
  `shoppergroups`        VARCHAR(255) NOT NULL,
  `reddesign_enable`     TINYINT(4)   NOT NULL,
  PRIMARY KEY (`reddesign_redshop_id`)
)
  ENGINE =MyISAM
  DEFAULT CHARSET =utf8;
