SET FOREIGN_KEY_CHECKS = 0;

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

ALTER TABLE `#__reddesign_areas` ADD `areaType`  TINYINT(2)  NOT NULL DEFAULT '1' COMMENT '1 = Text, 2 = Clipart' AFTER `id`;

SET FOREIGN_KEY_CHECKS = 1;
