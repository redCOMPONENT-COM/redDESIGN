SET FOREIGN_KEY_CHECKS = 0;

ALTER TABLE `#__reddesign_backgrounds`
  ADD `useCheckerboard`  TINYINT(1)   NOT NULL DEFAULT '0';

SET FOREIGN_KEY_CHECKS = 1;