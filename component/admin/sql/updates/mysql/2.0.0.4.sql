SET FOREIGN_KEY_CHECKS = 0;

ALTER TABLE `#__reddesign_product_mapping` CHANGE `reddesign_designtype_id` `related_designtype_ids` TEXT NOT NULL;
ALTER TABLE `#__reddesign_product_mapping` ADD `default_designtype_id` INT(11) NOT NULL AFTER `product_id`;

ALTER TABLE `#__reddesign_attribute_mapping` DROP PRIMARY KEY;
ALTER TABLE `#__reddesign_attribute_mapping` CHANGE `reddesign_designtype_id` `designtype_id` INT(11) NOT NULL;
ALTER TABLE `#__reddesign_attribute_mapping` ADD PRIMARY KEY(`designtype_id`, `product_id`, `property_id`);