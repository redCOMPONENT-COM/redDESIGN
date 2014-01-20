SET FOREIGN_KEY_CHECKS = 0;

UPDATE `#__reddesign_backgrounds` SET `useCheckerboard` = 0;

ALTER TABLE `#__reddesign_backgrounds` CHANGE  `useCheckerboard`  `useCheckerboard` TINYINT( 1 ) NOT NULL DEFAULT  '0';

SET FOREIGN_KEY_CHECKS = 1;