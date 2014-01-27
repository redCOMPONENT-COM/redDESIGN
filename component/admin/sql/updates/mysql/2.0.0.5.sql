SET FOREIGN_KEY_CHECKS = 0;

ALTER TABLE `#__reddesign_attribute_mapping` DROP PRIMARY KEY;
ALTER TABLE `#__reddesign_attribute_mapping` CHANGE `reddesign_designtype_id` `designtype_id` INT(11) NOT NULL;
ALTER TABLE `#__reddesign_attribute_mapping` ADD PRIMARY KEY(`designtype_id`, `product_id`, `property_id`);