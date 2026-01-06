-- Drop all old tables from uz123_kokand database
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `agents`;
DROP TABLE IF EXISTS `calculation`;
DROP TABLE IF EXISTS `cashin_from_agents`;
DROP TABLE IF EXISTS `credit`;
DROP TABLE IF EXISTS `exchange`;
DROP TABLE IF EXISTS `fine`;
DROP TABLE IF EXISTS `incash`;
DROP TABLE IF EXISTS `payment`;
DROP TABLE IF EXISTS `pays`;
DROP TABLE IF EXISTS `pays_byudjet`;
DROP TABLE IF EXISTS `pays_comunal`;
DROP TABLE IF EXISTS `rate`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `migrations`;

SET FOREIGN_KEY_CHECKS = 1;
