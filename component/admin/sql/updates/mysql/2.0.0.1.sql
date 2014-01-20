SET FOREIGN_KEY_CHECKS = 0;

ALTER TABLE `#__reddesign_backgrounds`
  ADD `usecheckerboard`  TINYINT(1)   NOT NULL DEFAULT '1';

SET FOREIGN_KEY_CHECKS = 1;