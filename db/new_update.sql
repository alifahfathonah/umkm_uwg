-- Update Pelanggan
ALTER TABLE `pelanggan` DROP `keterangan`;
ALTER TABLE `pelanggan` ADD `tipe` TINYINT(1) NULL AFTER `telepon`;
CREATE TABLE `umkm_db`.`tipe_pelanggan` ( `id` INT NOT NULL AUTO_INCREMENT , `nama` VARCHAR(50) NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;
INSERT INTO `tipe_pelanggan` (`id`, `nama`) VALUES (NULL, 'Konsumen'), (NULL, 'Reseller'), (NULL, 'Agen'), (NULL, 'Dropshiper'), (NULL, 'Distributor')