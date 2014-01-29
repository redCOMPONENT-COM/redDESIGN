SET foreign_key_checks = 0;

ALTER TABLE `#__reddesign_areas` ADD `verticalAlign` VARCHAR(10) NOT NULL DEFAULT 'top' AFTER `textalign`;

SET FOREIGN_KEY_CHECKS = 1;
