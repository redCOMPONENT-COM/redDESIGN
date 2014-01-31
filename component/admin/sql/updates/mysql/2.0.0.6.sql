SET FOREIGN_KEY_CHECKS = 0;

ALTER TABLE `#__reddesign_property_background_mapping` ADD FOREIGN KEY (`property_id`) REFERENCES `#__redshop_product_attribute_property` (`property_id`) ON DELETE CASCADE ON UPDATE NO ACTION;
ALTER TABLE `#__reddesign_property_background_mapping` ADD FOREIGN KEY (`background_id`) REFERENCES `#__reddesign_backgrounds` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

SET FOREIGN_KEY_CHECKS = 1;
