<?php
// $Id: module-upgrade.php 7198 2013-03-06 07:09:51Z smallduh $

if(!$CONN){
        echo "go away !!";
        exit;
}

// �ˬd��s�_
// ��s�O���ɸ��|
$upgrade_path = "upgrade/".get_store_path($path);
$upgrade_str = set_upload_path("$upgrade_path");

//�ץ� 2018-01-22~2018-01-24 �T�Ѫ����, �ݩ� 106-2
$up_file_name =$upgrade_str."2018-03-03.txt";

if (!is_file($up_file_name)){
	$target_day=array("2018-01-22","2018-01-23","2018-01-24");
	foreach ($target_day as $fix_day) {
		$query = "select * FROM `stud_absent` WHERE date='$fix_day'";
		$res=$CONN->Execute($query);
		while ($row=$res->fetchRow()) {
			$class_id=$row['class_id'];
			$sasn=$row['sasn'];
			$c=explode("_",$class_id);
			$update_class_id=sprintf("%03d_%d_%02d_%02d",$c[0],2,$c[2],$c[3]);
			$update_query="update `stud_absent` set semester='2',class_id='$update_class_id' where sasn='$sasn'";
			$CONN->Execute($update_query);
		}
	}

		$temp_query = "�ץ� 2018-01-22~2018-01-24 �T�Ѫ����, �ݩ� 106�Ǧ~��2�Ǵ� -- by smallduh (2013-03-06)\n$query";
		$fp = fopen ($up_file_name, "w");
		fwrite($fp,$temp_query);
		fclose ($fd);
}

?>
