<?php

//$Id:  $

if(!$CONN){
        echo "go away !!";
        exit;
}

$query = "CREATE TABLE IF NOT EXISTS association(
  sn int(11) NOT NULL auto_increment,
  student_sn int(11) NOT NULL,
  seme_year_seme varchar(4) NOT NULL,
  association_name varchar(40) NOT NULL,
  score float NOT NULL,
  description text NOT NULL,
  PRIMARY KEY  (sn),
  KEY student_sn (student_sn,seme_year_seme));";
 $CONN->Execute($query);
?>
