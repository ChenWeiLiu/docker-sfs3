<?php

//$Id:$

if(!$CONN){
        echo "go away !!";
        exit;
}
	//增加學生訪談記錄表student_sn、teacher_sn、interview欄位
	$SQL="ALTER TABLE `stud_seme_talk` ADD `interview` VARCHAR( 20 ) NULL AFTER `teach_id`";
	$rs=$CONN->Execute($SQL);
	
	//取得教師ID與SN、name對照
	$teacher_array=teacher_base();
	
	//將interview 資料轉化補上
	$SQL="SELECT DISTINCT teach_id FROM `stud_seme_talk`";
	$res=$CONN->Execute($SQL);
	while(!$res->EOF) {
		$teach_id=$res->fields[teach_id];
		$interview=$teacher_array[$teach_id];

		$update_sql="UPDATE `stud_seme_talk` SET interview='$interview' WHERE teach_id=$teach_id";
		$CONN->Execute($update_sql);
		$res->MoveNext();
	}
?>