<?php

if(!$CONN){
        echo "go away !!";
        exit;
}
$query1 = "
		CREATE TABLE IF NOT EXISTS `teacher_appraisal` (
		  `sn` int(11) NOT NULL AUTO_INCREMENT,
		  `title` varchar(60) NOT NULL,
		  `rec_sort` int(11) NOT NULL,
		  `teacher_sn` int(11) NOT NULL,
		  `years_of_service` int(2) NOT NULL,
		  `base_salary` int(3) NOT NULL,
		  `allowance` tinyint(1) NOT NULL,
		  `area_kind` varchar(4) NOT NULL,
		  `area_years_of_service` int(2) NOT NULL,
		  `memo` text NOT NULL,
		  `update_sn` int(11) NOT NULL,
		  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		  PRIMARY KEY (`sn`)
		);";

$query2="CREATE TABLE IF NOT EXISTS `teacher_appraisal_avoid` (
		  `sn` int(11) NOT NULL AUTO_INCREMENT,
		  `teacher_sn` int(11) NOT NULL,
		  `update_sn` int(11) NOT NULL,
		  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		  PRIMARY KEY (`sn`)
		);";

$rs=$CONN->Execute($query1);
$rs=$CONN->Execute($query2);

?>
