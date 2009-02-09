INSERT INTO `traq_settings` ( `setting` , `value` )
VALUES (
'dbversion', '1'
);
DROP TABLE IF EXISTS `traq_timeline`;
CREATE TABLE `traq_timeline` (
`id` BIGINT NOT NULL ,
`type` BIGINT NOT NULL ,
`data` LONGTEXT NOT NULL ,
`timestamp` BIGINT NOT NULL ,
`date` DATE NOT NULL ,
`userid` BIGINT NOT NULL ,
`projectid` BIGINT NOT NULL
) ENGINE = innodb;