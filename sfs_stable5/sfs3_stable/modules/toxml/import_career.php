<?php
$sql="select curr_class_num from stud_base where student_sn='$student_sn'";
$res=$CONN->Execute($sql) or die ('Error! Sql='.$sql);
$curr_class_num=$res->fields['curr_class_num'];     //此生目前年級班級座號
$stud_class_year=substr($curr_class_num,0,1);		//此生目前年級

echo iconv("UTF-8","Big5//IGNORE","<BR>#◎目前年級:".$stud_class_year);

//先將資料轉成陣列
	$associations_datas=$student->生涯輔導紀錄->學習成果及特殊表現->我的經歷->社團->社團_資料內容;
//學期社團記錄
foreach($associations_datas as $seme_association)  {
	$ass_class_year=$seme_association->年級;
	//echo "<br>curr_year:".curr_year();
	//echo "<br>YEAR:".$ass_class_year;
	$ass_seme=$seme_association->學期;
	$ass_association_name=$seme_association->社團名稱;
	$ass_association_post=$seme_association->擔任職務;
	$ass_association_feedback=$seme_association->自我省思;

	$seme_year_seme=(curr_year()-($stud_class_year-$ass_class_year)).$ass_seme;

	//先清除舊資料  以免重覆紀錄
	$SQL="DELETE FROM association WHERE student_sn=$student_sn AND seme_year_seme='$seme_year_seme'";
	$rs = $CONN->Execute($SQL) or user_error("ERROR WHILE EXCUING SQL! <br><br>$SQL",256);
	$SQL="INSERT INTO association(seme_year_seme,student_sn,association_name,stud_post,stud_feedback) VALUES ('$seme_year_seme','$student_sn','$ass_association_name','$ass_association_post','$ass_association_feedback')";
	$SQL=iconv("UTF-8","Big5//IGNORE",$SQL);
	$SQL=str_replace("'null'","''",$SQL);
	$rs = $CONN->Execute($SQL) or user_error("ERROR WHILE EXCUING SQL! <br><br>$SQL",256);
	echo iconv("UTF-8","Big5//IGNORE","<BR>#◎ 匯入[$seme_year_seme]社團活動紀錄 ( $ass_association_name ) 至 association 資料表 OK ! ");
	if($ShowSQL) echo '<BR>'.$SQL;

}


//先將資料轉成陣列 , 服務學習
//取得目前登錄者所在部門
	$sql_select = "select post_office from teacher_post where teacher_sn='{$_SESSION['session_tea_sn']}'";
	$recordSet = $CONN->Execute($sql_select);
	$department= $recordSet->fields["post_office"];

	$service_datas=$student->生涯輔導紀錄->學習成果及特殊表現->服務學習紀錄->服務學習紀錄_資料內容;

	foreach($service_datas as $seme_service) {
		$srv_year = $seme_service->年級;
		$srv_seme = $seme_service->學期;
		$srv_date = $seme_service->服務日期;
		$srv_item = addslashes(iconv("UTF-8","Big5//IGNORE",$seme_service->服務項目));
		$srv_memo = addslashes(iconv("UTF-8","Big5//IGNORE",$seme_service->服務內容));
		$srv_min = $seme_service->時數*60;
		$srv_sponsor = addslashes(iconv("UTF-8","Big5//IGNORE",$seme_service->主辦單位));
		$srv_feedback = addslashes(iconv("UTF-8","Big5//IGNORE",$seme_service->自我省思));

		$seme_year_seme=(curr_year()-($stud_class_year-$srv_year)).$srv_seme;

		//檢查資料是否已存在 , 避免重復匯入
		$double=0;
		$sql="select * from stud_service where year_seme='$seme_year_seme' and service_date='$srv_date' and item='$srv_item' and memo='$srv_memo'";
		//$sql=iconv("UTF-8","Big5//IGNORE",$sql);
		$res=$CONN->Execute($sql) or die ("Error! sql=".$sql);
		//如果有這筆記錄, 檢查明細裡有沒有此生, 若也有, 則此筆資料不用新增, 以免重覆
		if ($res->RecordCount()) {
			$item_sn=$res->fields['sn'];
			$SQL="select * from stud_service_detail where student_sn='$student_sn' and item_sn='$item_sn'";
			$res=$CONN->Execute($SQL) or die ("Error! sql=".$SQL);
			if ($res->RecordCount()) {
				$double=1;
			}
		}
		//如果沒有, 進行新增
		if ($double==0) {
			$SQL="insert into stud_service (year_seme,service_date,department,item,memo,input_time,confirm,sponsor) values ('$seme_year_seme','$srv_date','$department','$srv_item','$srv_memo','".date('Y-m-d H:i:s')."','1','$srv_sponsor')";
			//$SQL=iconv("UTF-8","Big5//IGNORE",$SQL);
			$SQL=str_replace("'null'","''",$SQL);
			//if($ShowSQL) echo '<BR>'.$SQL;
			if (mysql_query($SQL)) {
				list($item_sn) = mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));
				$studmemo = iconv("UTF-8","Big5//IGNORE","外校記錄");

				$SQL = "insert into stud_service_detail (student_sn,item_sn,minutes,studmemo) values ('$student_sn','$item_sn','$srv_min','$studmemo')";
				//$SQL=iconv("UTF-8","Big5//IGNORE",$SQL);
				$SQL=str_replace("'null'","''",$SQL);
				if (mysql_query($SQL)) {
					echo iconv("UTF-8","Big5//IGNORE","<BR>#◎ 匯入[$seme_year_seme]服務學習紀錄 ( ").$srv_memo." ) OK ! ";
					//if($ShowSQL) echo '<BR>'.$SQL;
				} else {
					echo iconv("UTF-8","Big5//IGNORE","<BR>#◎ 匯入[$seme_year_seme]服務學習紀錄 ( ").$srv_memo.iconv("UTF-8","Big5//IGNORE"," ) 失敗 ! ");
				}

			}else {
				echo iconv("UTF-8","Big5//IGNORE","<BR>#◎ 匯入[$seme_year_seme]服務學習紀錄 ( ").$srv_memo.iconv("UTF-8","Big5//IGNORE"," ) 失敗 ! ");
			}

		} else {
			echo iconv("UTF-8","Big5//IGNORE","<BR>#◎ [$seme_year_seme]服務學習紀錄 ("). $srv_memo.iconv("UTF-8","Big5//IGNORE"," ) 已經存在, 不予重覆登錄 ! ");
		}// end if else $double
	} // end foreach


?>
