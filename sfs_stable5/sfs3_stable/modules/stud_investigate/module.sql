
DROP TABLE IF EXISTS `investigate`;
CREATE TABLE IF NOT EXISTS `investigate` (
`sn` int(10) unsigned NOT NULL,
  `room` varchar(20) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `fields` text,
  `selections` text,
  `memo` text,
  `visible` varchar(1) DEFAULT 'Y',
  `start` date DEFAULT NULL,
  `end` date DEFAULT NULL,
  `update_name` varchar(20) DEFAULT NULL,
  `update_datetime` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

ALTER TABLE `investigate`
 ADD PRIMARY KEY (`sn`);

ALTER TABLE `investigate`
MODIFY `sn` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;


DROP TABLE IF EXISTS `investigate_record`;
CREATE TABLE IF NOT EXISTS `investigate_record` (
`sn` int(10) unsigned NOT NULL,
  `investigate_sn` int(11) unsigned NOT NULL,
  `student_sn` int(11) NOT NULL,
  `field` varchar(20) NOT NULL,
  `value` varchar(20) DEFAULT NULL,
  `memo` varchar(20) DEFAULT NULL,
  `update_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);


ALTER TABLE `investigate_record`
 ADD PRIMARY KEY (`sn`), ADD UNIQUE KEY `field` (`field`,`student_sn`,`investigate_sn`), ADD KEY `student_sn` (`student_sn`), ADD KEY `investigate_sn` (`investigate_sn`);

ALTER TABLE `investigate_record`
MODIFY `sn` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;