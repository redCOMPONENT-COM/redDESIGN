ALTER TABLE  `#__reddesign_areas` DROP `fedcolor`;
ALTER TABLE  `#__reddesign_areas` DROP `apply_cruv`;
ALTER TABLE  `#__reddesign_areas` DROP `txtangle`;

ALTER TABLE  `#__reddesign_areas` ADD `input_field_type` TINYINT( 3 ) NULL AFTER `maxline`;
ALTER TABLE  `#__reddesign_areas` ADD `default_text` TEXT NULL DEFAULT NULL AFTER  `color_code`
