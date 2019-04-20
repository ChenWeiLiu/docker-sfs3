<?php

//$Id:  $

if(!$CONN){
        echo "go away !!";
        exit;
}

$query = "CREATE  TABLE  IF  NOT  EXISTS stud_absent_move(sn int(11)  NOT  NULL  AUTO_INCREMENT ,
seme_year_seme varchar(6)  NOT  NULL default  '',
 `year` tinyint(4)  NOT  NULL ,
 `month` tinyint(4)  NOT  NULL ,
stud_id varchar(20)  NOT  NULL default  '',
abs_kind tinyint(3) unsigned NOT  NULL default  '0',
abs_days int(10) unsigned default '0' ,
student_sn int(11)  NOT  NULL ,
 PRIMARY  KEY (sn ) ,
 KEY student_sn(student_sn ) );";
 $CONN->Execute($query);
?>
