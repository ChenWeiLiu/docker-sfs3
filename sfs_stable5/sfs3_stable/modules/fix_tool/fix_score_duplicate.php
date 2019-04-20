<?php

require_once("config.php");
//使用者認證
sfs_check();

head("成績重複性刪除與資料表索引重更新");

$table_list=array(0=>"stud_seme_score",1=>"stud_seme_score_nor");
$limit_record=100;

foreach($table_list as $key=>$table_name)
{
	echo "<li>檢查: $table_name</li>";
	//顯示目前狀況
	$sql="SELECT seme_year_seme,student_sn,ss_id,count(*) AS counter FROM $table_name GROUP BY student_sn,ss_id HAVING counter>1 limit $limit_record";
	$res=$CONN->Execute($sql) or user_error("讀取stud_seme_score學期成績表統計資料失敗！<br>$sql",256);
	$total_record=$res->recordcount();
	if($total_record>1){
		echo "　==>處理的學生科目數： $total_record<br>";
		echo "<table border=1><tr align='center' bgcolor='#FFCCCC'><td>學生流水號</td><td>課程代號</td><td>重複數</td><td>處理狀況</td></tr>";
		while(!$res->EOF) {
			//echo "　　==>處理第".$res->CurrentRow()."筆!<br>";
			$year_seme=$res->fields[seme_year_seme];
			$student_sn=$res->fields[student_sn];
			$ss_id=$res->fields[ss_id];
			$counter=$res->fields[counter];
			echo "<tr><td align='center'>$student_sn</td><td align='center'>$ss_id</td><td align='center'>$counter</td><td>";
			//選取保留資料後刪除
			$kill_sss_id_list='';
			$sql2="SELECT * FROM $table_name WHERE seme_year_seme='$year_seme' AND student_sn=$student_sn AND ss_id=$ss_id ORDER BY ss_update_time";
			$res2=$CONN->Execute($sql2) or user_error("讀取stud_seme_score (student_sn=$student_sn)(ss_id=$ss_id) 學期成績表原始重複紀錄失敗！<br>$sql2",256);
			while(!$res2->EOF) {
				$kill_sss_id_list.=$res2->fields[sss_id].',';
				$reversed_ss_score=$res2->fields[ss_score];
				$teacher_sn=$res2->fields[teacher_sn];
				if($res2->fields[ss_score_memo]<>'') $reversed_ss_score_memo=$res2->fields[ss_score_memo];
				$res2->MoveNext();
			}
			echo "<li>保留成績:$reversed_ss_score 保留描述: $reversed_ss_score_memo</li>";
			//刪除原紀錄
			$kill_sss_id_list=substr($kill_sss_id_list,0,-1);
			$sql_kill="DELETE FROM $table_name WHERE seme_year_seme='$year_seme' AND student_sn=$student_sn AND ss_id=$ss_id";
			$res_kill=$CONN->Execute($sql_kill) or user_error("刪除stud_seme_score原重複紀錄失敗！<br>$sql_kill",256);
			echo "<li>刪除重複紀錄sss_id列表:$kill_sss_id_list</li>";

			//重新寫入紀錄
			$sql_insert="INSERT INTO $table_name SET seme_year_seme='$year_seme',student_sn=$student_sn,ss_id=$ss_id,ss_score=$reversed_ss_score,ss_score_memo='$reversed_ss_score_memo',teacher_sn=$teacher_sn";
			$res_insert=$CONN->Execute($sql_insert) or user_error("新增stud_seme_score (student_sn=$student_sn)(ss_id=$ss_id) 學期成績失敗！<br>$sql_insert",256);
			echo "<li>重新寫入OK!</li>";
			
			$res->MoveNext();
		}
		//檢查是否還有
		$sql="SELECT seme_year_seme,student_sn,ss_id,count(*) AS counter FROM $table_name GROUP BY student_sn,ss_id HAVING counter>1";
		$res=$CONN->Execute($sql) or user_error("讀取stud_seme_score學期成績表統計資料失敗！<br>$sql",256);
		$total_record=$res->recordcount();
		if(!$total_record){
			//修正索引
			$sql_index="ALTER TABLE $table_name DROP PRIMARY KEY ;";
			$res_index=$CONN->Execute($sql_index) or user_error("刪除 $table_name 原索引失敗！<br>$sql_index",256);		
			$sql_index="ALTER TABLE $table_name ADD PRIMARY KEY ( `seme_year_seme` , `student_sn` , `ss_id` ) ";
			$res_index=$CONN->Execute($sql_index) or user_error("重建 $table_name 原索引失敗！<br>$sql_index",256);
			echo "<li>重建正確的主索引 OK!</li></td></tr></table>";
		} else echo "</td></tr></table><FONT size=5 color='#FF0000'>尚有　$total_record 組須處理，請按 F5 重新整理繼續!</FONT>";
	} else echo "<br>　　恭喜你! 本校($school_sshort_name)  $table_name -> $year_seme 學期成績未有資料重複情形<br>";
}
foot();

?>
