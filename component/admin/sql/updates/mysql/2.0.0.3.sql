SET FOREIGN_KEY_CHECKS = 0;

ALTER TABLE `#__reddesign_backgrounds`
ADD CONSTRAINT `#__reddesign_backgrounds_fk1` FOREIGN KEY (`designtype_id`) REFERENCES `#__reddesign_designtypes` (`id`)
	ON DELETE CASCADE
  ON UPDATE NO ACTION;

SET FOREIGN_KEY_CHECKS = 1;
