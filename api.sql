# Host: localhost  (Version 5.5.5-10.1.19-MariaDB)
# Date: 2019-04-04 17:36:12
# Generator: MySQL-Front 6.1  (Build 1.26)


#
# Structure for table "apps"
#

DROP TABLE IF EXISTS `apps`;
CREATE TABLE `apps` (
  `apps_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `last_modified` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `time_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`apps_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

#
# Data for table "apps"
#

INSERT INTO `apps` VALUES (1,'Android METRO E-MYC','METRO E-MYC AND REDEMPTION POINT','2018-08-25 00:06:18','2018-08-25 00:06:18'),(2,'IOS METRO E-MYC','METRO E-MYC AND REDEMPTION POINT','2018-08-25 00:06:21','2018-08-25 00:06:21'),(3,'WEB METRO Monitoring E-MYC','METRO Monitoring E-MYC Customer Services','2018-08-25 00:07:35','2018-08-25 00:07:35'),(4,'METRO ACCESS','NEW METRO ACCESS',NULL,'2018-08-30 13:26:29'),(5,'PERGI.COM','PARTNER',NULL,'2018-10-17 15:18:45'),(6,'ZENDESK','HELPDESK',NULL,'2018-12-14 10:05:38');

#
# Structure for table "email_verification"
#

DROP TABLE IF EXISTS `email_verification`;
CREATE TABLE `email_verification` (
  `apps_id` int(11) NOT NULL DEFAULT '0',
  `email` varchar(255) NOT NULL DEFAULT '',
  `exp_interval` int(11) NOT NULL DEFAULT '0',
  `generate_time` timestamp NULL DEFAULT NULL,
  `expired_time` timestamp NULL DEFAULT NULL,
  `code` int(11) DEFAULT NULL,
  PRIMARY KEY (`apps_id`,`email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# Data for table "email_verification"
#


#
# Structure for table "logs"
#

DROP TABLE IF EXISTS `logs`;
CREATE TABLE `logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uri` varchar(255) NOT NULL,
  `method` varchar(6) NOT NULL,
  `params` text,
  `api_key` varchar(40) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `time` int(11) NOT NULL,
  `rtime` float DEFAULT NULL,
  `authorized` varchar(1) NOT NULL,
  `response_code` smallint(3) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2205986 DEFAULT CHARSET=utf8;

#
# Data for table "logs"
#

INSERT INTO `logs` VALUES (2205990,'v1/token/refresh','get','a:6:{s:12:\"Content-Type\";s:0:\"\";s:14:\"Content-Length\";s:1:\"0\";s:14:\"X-Original-Url\";s:17:\"/v1/token/refresh\";s:4:\"Host\";s:17:\"192.168.1.26:8088\";s:13:\"Authorization\";s:348:\"Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NTQzNzM5NDksImV4cCI6MTU1NDM3NDg0OSwiZGF0YSI6W3sidXNlcm5hbWUiOiJtZXRyb2FjY2VzcyIsImVtYWlsIjoib21hckBvbWFyLmNvbSIsIm5hbWUiOiJNRVRSTyBBQ0NFU1MiLCJzdGF0dXMiOiIxIiwiYXBwc19uYW1lIjoiTUVUUk8gQUNDRVNTIiwiYXBwc19kZXNjcmlwdGlvbiI6Ik5FVyBNRVRSTyBBQ0NFU1MifV19.uvzziobVeaj27xeFF9R_fJ5lKV6KMA_ea56IJgF4wsU\";s:6:\"Accept\";s:3:\"*/*\";}','','192.168.224.2',1554374152,0.564989,'1',200),(2205991,'v1/token/refresh','get','a:6:{s:12:\"Content-Type\";s:0:\"\";s:14:\"Content-Length\";s:1:\"0\";s:14:\"X-Original-Url\";s:17:\"/v1/token/refresh\";s:4:\"Host\";s:17:\"192.168.1.26:8088\";s:13:\"Authorization\";s:348:\"Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NTQzNzM4NjIsImV4cCI6MTU1NDM3NDc2MiwiZGF0YSI6W3sidXNlcm5hbWUiOiJtZXRyb2FjY2VzcyIsImVtYWlsIjoib21hckBvbWFyLmNvbSIsIm5hbWUiOiJNRVRSTyBBQ0NFU1MiLCJzdGF0dXMiOiIxIiwiYXBwc19uYW1lIjoiTUVUUk8gQUNDRVNTIiwiYXBwc19kZXNjcmlwdGlvbiI6Ik5FVyBNRVRSTyBBQ0NFU1MifV19.fGbz7eSv1eKhMGp0kFF-jzo5RiXIRyWmyyrsljCx7iw\";s:6:\"Accept\";s:3:\"*/*\";}','','192.168.1.28',1554374162,0.0154922,'1',200),(2205992,'v1/token/refresh','get','a:6:{s:12:\"Content-Type\";s:0:\"\";s:14:\"Content-Length\";s:1:\"0\";s:14:\"X-Original-Url\";s:17:\"/v1/token/refresh\";s:4:\"Host\";s:17:\"192.168.1.26:8088\";s:13:\"Authorization\";s:348:\"Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NTQzNzM4NjksImV4cCI6MTU1NDM3NDc2OSwiZGF0YSI6W3sidXNlcm5hbWUiOiJtZXRyb2FjY2VzcyIsImVtYWlsIjoib21hckBvbWFyLmNvbSIsIm5hbWUiOiJNRVRSTyBBQ0NFU1MiLCJzdGF0dXMiOiIxIiwiYXBwc19uYW1lIjoiTUVUUk8gQUNDRVNTIiwiYXBwc19kZXNjcmlwdGlvbiI6Ik5FVyBNRVRSTyBBQ0NFU1MifV19.FJ--w-YH8Tw-lxTuRgsuVKBZzHne1iwvEctDGpVti9s\";s:6:\"Accept\";s:3:\"*/*\";}','','192.168.1.28',1554374169,0.0127449,'1',200),(2205993,'v1/token/refresh','get','a:6:{s:12:\"Content-Type\";s:0:\"\";s:14:\"Content-Length\";s:1:\"0\";s:14:\"X-Original-Url\";s:17:\"/v1/token/refresh\";s:4:\"Host\";s:17:\"192.168.1.26:8088\";s:13:\"Authorization\";s:348:\"Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NTQzNzM4NzAsImV4cCI6MTU1NDM3NDc3MCwiZGF0YSI6W3sidXNlcm5hbWUiOiJtZXRyb2FjY2VzcyIsImVtYWlsIjoib21hckBvbWFyLmNvbSIsIm5hbWUiOiJNRVRSTyBBQ0NFU1MiLCJzdGF0dXMiOiIxIiwiYXBwc19uYW1lIjoiTUVUUk8gQUNDRVNTIiwiYXBwc19kZXNjcmlwdGlvbiI6Ik5FVyBNRVRSTyBBQ0NFU1MifV19.epgFfRU-rUGace64NCWritlWkCPap_PDlxLqAxQAO9E\";s:6:\"Accept\";s:3:\"*/*\";}','','192.168.224.2',1554374170,0.0090301,'1',200);

#
# Structure for table "users"
#

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `users_id` int(11) NOT NULL AUTO_INCREMENT,
  `apps_id` int(11) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `last_modified` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `time_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`users_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

#
# Data for table "users"
#

INSERT INTO `users` VALUES (1,4,'metroaccess','7cbe64177b496ab4d2978883b223525f','omar@omar.com','METRO ACCESS',1,'2018-08-30 13:26:16','2018-08-24 23:59:25'),(2,1,'mobileappmyc','7cbe64177b496ab4d2978883b223525f','hana@hana.com','MOBILE APP',1,'2018-08-30 13:26:10','2018-08-24 23:59:25'),(3,5,'pergi.com','7523d02b6489661f90e5389a91660e0a','erwin.setiawan@pergi.com','PERGI.COM',0,'2019-02-08 10:25:15','2018-10-17 15:19:31'),(4,6,'zendesk','7c083a6415fd5a385ccc46d98725a240','nando@metroindonesia.com','ZENDESK',1,NULL,'2018-12-14 10:06:42');

#
# View "validate_email_verification"
#

DROP VIEW IF EXISTS `validate_email_verification`;
CREATE
  ALGORITHM = UNDEFINED
  VIEW `validate_email_verification`
  AS
  SELECT
    `apps_id`,
    `email`,
    `exp_interval`,
    `generate_time`,
    `expired_time`,
    `code`
  FROM
    `email_verification`
  WHERE
    (`expired_time` >= NOW());

#
# Trigger "update_expired_time"
#

DROP TRIGGER IF EXISTS `update_expired_time`;
CREATE TRIGGER `update_expired_time`
BEFORE INSERT ON `email_verification`
FOR EACH ROW  
BEGIN
  SET NEW.generate_time = CURRENT_TIMESTAMP;
  SET NEW.expired_time = DATE_ADD(NEW.generate_time, INTERVAL NEW.exp_interval MINUTE);
  SET NEW.code = FLOOR( 1000 + ( RAND( ) * 8999 ) );
END;
