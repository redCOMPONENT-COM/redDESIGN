SET FOREIGN_KEY_CHECKS = 0;

ALTER TABLE `#__reddesign_attribute_mapping` DROP PRIMARY KEY;
ALTER TABLE `#__reddesign_attribute_mapping` DROP `product_id`;
ALTER TABLE `#__reddesign_attribute_mapping` CHANGE `designtype_id` `background_id` INT(11) NOT NULL;
ALTER TABLE `#__reddesign_attribute_mapping` ADD PRIMARY KEY (`property_id`);
RENAME TABLE `#__reddesign_attribute_mapping` TO `#__reddesign_property_background_mapping`;

SET FOREIGN_KEY_CHECKS = 1;
