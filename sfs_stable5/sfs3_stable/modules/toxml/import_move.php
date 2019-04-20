<?php
	
	$stud_moved_array=array("轉出"=>"1","轉入"=>"2","中輟復學"=>"3","休學復學"=>"4","畢業"=>"5","休學"=>"6","出國"=>"7","調校"=>"8","升級"=>"9","降級"=>"10","死亡"=>"11","中輟"=>"12","新生入學"=>"13","轉學復學"=>"14","在家自學"=>"15");

	//echo "<BR>".$stud_moved_array["轉出"]."<BR>".$stud_moved_array["轉入"]."<BR>".$stud_moved_array["中輟復學"]."<BR>".$stud_moved_array["休學復學"]."<BR>";

	$seme_year_seme_s=0+$seme_year_seme;
	$SQL='';
	$move_datas=$student->異動資料->異動資料_資料內容;
	foreach($move_datas as $move_data){
		//以下寫在 異動資料表 stud_move
		$sst_city=$move_data->原就讀縣市;
		$sst_school=$move_data->原就讀學校名稱;
		$sst_code=$move_data->原就讀學校代碼;
		$move_date=$move_data->異動日期;
		$move_c_unit=$move_data->異動核准機關名稱;
		
		$reason=$move_data->異動原因;
		$move_kind=$stud_moved_array["$reason"];
		
		$move_c_date=$move_data->核准文號_日期;
		$move_c_word=$move_data->核准文號_字;
		$move_c_num=$move_data->核准文號_號;
		$update_id=$_SESSION['session_log_id'];
		$update_ip=$REMOTE_ADDR;
		
		if($reason<>"" AND $reason<>"null") {
			$SQL.="($student_sn,'$stud_id','$move_kind','$seme_year_seme_s','$move_date','$move_c_unit','$move_c_date','$move_c_word','$move_c_num',now(),'$update_id','$update_ip','$reason','$sst_city','$sst_school','$sst_code'),";
		}
	}
	$SQL=substr($SQL,0,-1);
	$SQL="INSERT INTO stud_move_import(student_sn,stud_id,move_kind,move_year_seme,move_date,move_c_unit,move_c_date,move_c_word,move_c_num,update_time,update_id,update_ip,reason,city,school,school_id) VALUES ".$SQL;
	if(substr($SQL,-7)<>'VALUES ') {
		$SQL=iconv("UTF-8","Big5//IGNORE",$SQL);
		$SQL=str_replace("'null'","''",$SQL);
		//先清空原有紀錄
		$rs = $CONN->Execute("DELETE FROM stud_move_import WHERE student_sn=$student_sn AND move_year_seme='$seme_year_seme_s'") or user_error("刪除 stud_move_import 內的轉學生紀錄發生錯誤 ( student_sn:$student_sn  seme_year_seme=$seme_year_seme_s)! <br><br>",256) ;
		$rs = $CONN->Execute($SQL) or user_error(" 匯入 student_sn=$student_sn  stud_id=$stud_id 異動記錄 ( stud_move_import ) 失敗! <br><br>$SQL",256) ;
		echo iconv("UTF-8","Big5//IGNORE","<BR>#◎ 匯入 student_sn=$student_sn  stud_id=$stud_id 異動記錄 ( stud_move_import ) OK ! ");
		if($ShowSQL) echo '<BR>'.$SQL;
	}	
?>