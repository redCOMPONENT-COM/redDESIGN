SET FOREIGN_KEY_CHECKS = 0;

ALTER TABLE `#__reddesign_orderitem_mapping` DROP `productionPdf`;
ALTER TABLE `#__reddesign_orderitem_mapping` DROP `productionEps`;

SET FOREIGN_KEY_CHECKS = 1;