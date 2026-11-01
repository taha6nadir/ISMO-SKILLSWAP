-- Index de performance pour une base deja existante.
-- Importez ce fichier une seule fois dans la base `skillswaps`
-- (phpMyAdmin > Importer). Les index sont crees seulement s'ils manquent.

USE `skillswaps`;

SET @sql := (SELECT IF(COUNT(*) = 0,
  'ALTER TABLE `utilisateurs` ADD INDEX `idx_role` (`role`)', 'SELECT 1')
  FROM information_schema.statistics
  WHERE table_schema = DATABASE() AND table_name = 'utilisateurs' AND index_name = 'idx_role');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := (SELECT IF(COUNT(*) = 0,
  'ALTER TABLE `utilisateurs` ADD INDEX `idx_statut` (`statut`)', 'SELECT 1')
  FROM information_schema.statistics
  WHERE table_schema = DATABASE() AND table_name = 'utilisateurs' AND index_name = 'idx_statut');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := (SELECT IF(COUNT(*) = 0,
  'ALTER TABLE `utilisateurs` ADD INDEX `idx_filiere` (`filiere`)', 'SELECT 1')
  FROM information_schema.statistics
  WHERE table_schema = DATABASE() AND table_name = 'utilisateurs' AND index_name = 'idx_filiere');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := (SELECT IF(COUNT(*) = 0,
  'ALTER TABLE `aide` ADD INDEX `idx_status_date` (`status`, `date_pub`)', 'SELECT 1')
  FROM information_schema.statistics
  WHERE table_schema = DATABASE() AND table_name = 'aide' AND index_name = 'idx_status_date');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := (SELECT IF(COUNT(*) = 0,
  'ALTER TABLE `aide` ADD INDEX `idx_signal` (`signal`)', 'SELECT 1')
  FROM information_schema.statistics
  WHERE table_schema = DATABASE() AND table_name = 'aide' AND index_name = 'idx_signal');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
