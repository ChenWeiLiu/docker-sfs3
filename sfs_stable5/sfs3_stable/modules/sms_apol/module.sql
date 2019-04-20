
CREATE TABLE IF NOT EXISTS `sms_apol_task` (
  `sn` int(11) NOT NULL AUTO_INCREMENT,
  `year_seme` varchar(4) NOT NULL,
  `ask_ip` varchar(40) NOT NULL,
  `ask_time` datetime NOT NULL,
  `teacher_sn` int(11) NOT NULL,
  `MDN` varchar(20) NOT NULL,
  `Subject` varchar(20) DEFAULT NULL,
  `Message` varchar(255) NOT NULL,
  `Code` int(11) DEFAULT NULL,
  `TaskID` varchar(20) NOT NULL,
  `TotalRec` int(11) DEFAULT NULL,
  `RtnDateTime` varchar(14) NOT NULL,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`sn`),
  UNIQUE KEY `TaskID` (`TaskID`),
  KEY `teacher_sn` (`teacher_sn`),
  KEY `year_seme` (`year_seme`),
  KEY `ask_time` (`ask_time`)
);


CREATE TABLE IF NOT EXISTS `sms_apol_record` (
  `sn` int(11) NOT NULL AUTO_INCREMENT,
  `MSISDN` varchar(20) NOT NULL,
  `MSISDN_Name` varchar(36) NOT NULL,
  `TaskID` varchar(20) NOT NULL,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`sn`),
  KEY `TaskID` (`TaskID`),
  KEY `MSISDN` (`MSISDN`)
);

