SET FOREIGN_KEY_CHECKS = 0;

ALTER TABLE `#__reddesign_fonts` DROP `default_width`;
ALTER TABLE `#__reddesign_fonts` DROP `default_height`;
ALTER TABLE `#__reddesign_fonts` DROP `default_caps_height`;
ALTER TABLE `#__reddesign_fonts` DROP `default_baseline_height`;

SET FOREIGN_KEY_CHECKS = 1;