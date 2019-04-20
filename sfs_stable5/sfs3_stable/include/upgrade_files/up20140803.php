<?php

//$Id:$

if(!$CONN){
        echo "go away !!";
        exit;
}
	//檢查輔導訪談記錄表interview欄位是否有資料
	$SQL="select count(*) from stud_seme_talk where length(interview)=0";
	$rs=$CONN->Execute($SQL);
	if($rs->fields[0]) {
		//取得教師teach_id與name對照
		$teacher_array=array();
        $sql_select = "select teach_id,name from teacher_base";
        $recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
        while(!$recordSet->EOF) {
			$teach_id=$recordSet->fields['teach_id'];
			$name=$recordSet->fields['name'];
            $teacher_array[$teach_id]=$name;
			$recordSet->MoveNext();
        }
		
		//將interview 資料轉化補上
		$SQL="SELECT DISTINCT teach_id FROM `stud_seme_talk`";
		$res=$CONN->Execute($SQL);
		while(!$res->EOF) {
			$teach_id=$res->fields[teach_id];
			$interview=$teacher_array[$teach_id];

			$update_sql="UPDATE `stud_seme_talk` SET interview='$interview' WHERE length(interview)=0 AND teach_id='$teach_id'";
			$CONN->Execute($update_sql);
			$res->MoveNext();
		}
	}
?>