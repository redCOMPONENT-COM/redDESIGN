--
-- Table structure for table `#__reddesign_area`
--

CREATE TABLE IF NOT EXISTS `#__reddesign_areas` (
  `reddesign_area_id` INT(11)      NOT NULL AUTO_INCREMENT,
  `area_name`         VARCHAR(255) NOT NULL,
  `offsetLeft`        INT(11)      NOT NULL,
  `offsetTop`         INT(11)      NOT NULL,
  `height`            INT(11)      NOT NULL,
  `width`             INT(11)      NOT NULL,
  `font_size`         TEXT         NOT NULL,
  `font_id`           TEXT         NOT NULL,
  `color_code`        TEXT         NOT NULL,
  `x_pos`             INT(11)      NOT NULL,
  `y_pos`             INT(11)      NOT NULL,
  `textalign`         INT(11)      NOT NULL,
  `image_id`          INT(11)      NOT NULL,
  `apply_cruv`        TINYINT(4)   NOT NULL,
  `txtangle`          VARCHAR(4)   NOT NULL,
  `maxchar`           INT(10)      NOT NULL,
  `defaultFontSize`   INT(11)      NOT NULL,
  `minFontSize`       INT(11)      NOT NULL,
  `maxFontSize`       INT(11)      NOT NULL,
  `maxline`           INT(150)     NOT NULL,
  `fedcolor`          VARCHAR(60)  NOT NULL,
  PRIMARY KEY (`reddesign_area_id`)
)
  ENGINE =MyISAM
  DEFAULT CHARSET =utf8;

--
-- Table structure for table `#__reddesign_designtype`
--

CREATE TABLE IF NOT EXISTS `#__reddesign_designtypes` (
  `reddesign_designtype_id` INT(11)      NOT NULL AUTO_INCREMENT,
  `designtype_name`         VARCHAR(255) NOT NULL,
  `designtemplate`          BIGINT(20)   NOT NULL,
  `reddesign_autotemplate`  TINYINT(4)   NOT NULL DEFAULT '1',
  `enable_autosize`         INT(11)      NOT NULL,
  `enable_slider`           INT(11)      NOT NULL,
  `published`               TINYINT(4)   NOT NULL,
  PRIMARY KEY (`reddesign_designtype_id`)
)
  ENGINE =MyISAM
  DEFAULT CHARSET =utf8;

--
-- Table structure for table `#__reddesign_font`
--

CREATE TABLE IF NOT EXISTS `#__reddesign_fonts` (
  `reddesign_font_id` INT(11)      NOT NULL AUTO_INCREMENT,
  `font_name`         VARCHAR(255) NOT NULL,
  `font_display_name` VARCHAR(255) NOT NULL,
  `order`             INT(11)      NOT NULL,
  `default_width`     DOUBLE(5, 5) NOT NULL,
  `default_height`    DOUBLE(5, 5) NOT NULL,
  `caps_height`       DOUBLE(5, 5) NOT NULL,
  `baseline_height`   DOUBLE(5, 5) NOT NULL,
  `published`         INT(11)      NOT NULL,
  PRIMARY KEY (`reddesign_font_id`)
)
  ENGINE =InnoDB
  DEFAULT CHARSET =utf8;

--
-- Table structure for table `#__reddesign_font_char`
--

CREATE TABLE IF NOT EXISTS `#__reddesign_font_chars` (
  `reddesign_font_char_id` INT(11)     NOT NULL AUTO_INCREMENT,
  `font_id`                INT(11)     NOT NULL,
  `fontchar`               VARCHAR(10) NOT NULL,
  `width`                  DOUBLE      NOT NULL,
  `height`                 DOUBLE      NOT NULL,
  `typography`             INT(11)     NOT NULL DEFAULT '1',
  `typography_height`      DOUBLE      NOT NULL,
  PRIMARY KEY (`reddesign_font_char_id`)
)
  ENGINE =InnoDB
  DEFAULT CHARSET =utf8;

--
-- Table structure for table `#__reddesign_image`
--

CREATE TABLE IF NOT EXISTS `#__reddesign_images` (
  `reddesign_image_id` INT(11)      NOT NULL AUTO_INCREMENT,
  `image_name`         VARCHAR(255) NOT NULL,
  `eps_file`           TEXT         NOT NULL,
  `image_path`         TEXT         NOT NULL,
  `designtype_id`      INT(11)      NOT NULL,
  `ordering`           INT(11)      NOT NULL,
  `is_PDFbgimage`      TINYINT(4)   NOT NULL,
  `published`          TINYINT(4)   NOT NULL,
  PRIMARY KEY (`reddesign_image_id`)
)
  ENGINE =MyISAM
  DEFAULT CHARSET =utf8;

--
-- Table structure for table `#__reddesign_order`
--

CREATE TABLE IF NOT EXISTS `#__reddesign_orders` (
  `reddesign_order_id` BIGINT(20)   NOT NULL AUTO_INCREMENT,
  `order_id`           BIGINT(20)   NOT NULL,
  `product_id`         BIGINT(20)   NOT NULL,
  `order_item_id`      INT(11)      NOT NULL,
  `reddesignfile`      VARCHAR(255) NOT NULL,
  `designhdnargs`      TEXT         NOT NULL,
  `image_id`           INT(11)      NOT NULL,
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