<?php
// $Id: module-upgrade.php 7977 2014-04-10 07:34:47Z infodaes $

if(!$CONN){
        echo "go away !!";
        exit;
}
//啟動 session
session_start();
//
// 檢查更新否
// 更新記錄檔路徑

$upgrade_path = "upgrade/".get_store_path($path);
$upgrade_str = set_upload_path("$upgrade_path");
// 2010 -10-18 刪除 2003 年舊表升級處理
//$up_file_name =$upgrade_str."2003-10-09.txt";
//$up_file_name =$upgrade_str."score_mester_chane.txt";
//$up_file_name =$upgrade_str."2003-11-13.txt";
//$up_file_name =$upgrade_str."score_mester_change_0106.txt";


//補正score_semester_94_2分組學習科目成績學生的class_id欄位是空值的問題
$up_file_name =$upgrade_str."class_id_corrected_0942.txt";
if (!is_file($up_file_name) ){
        if (curr_year()==94 && curr_seme()==2) {
	        $score_semester='score_semester_94_2';
	        $leadstring='094_2';
	        $query = "SELECT score_id,student_sn FROM $score_semester WHERE (class_id IS NULL) OR class_id=''";
	        //echo "$query<BR>";
	        $res = $CONN->Execute($query);
	        if ($res->RecordCount()>0){
	              //echo "系統發現( $score_semester )學期成績資料表\"class_id\"欄位有空值(".$res->RecordCount()."筆)<BR>自動進行修補........<BR>";
	              while ($data=$res->FetchRow()) {
	                    //從 stud-base 抓取目前班級帶入CLASS_ID
	                    $sn=$data['student_sn'];
	                    $score_id=$data['score_id'];
	                    $sql_select="select curr_class_num from stud_base where student_sn=$sn";
	                    $rs_class=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	                    $row = $rs_class->FetchRow() ;
	                    $class_num = $row["curr_class_num"].'';
	                    $class_num=sprintf("%02d_%02d",substr($class_num,0,-4),substr($class_num,-4,2));
	                    $class_id=$leadstring.'_'.$class_num;

	                    $query = "update $score_semester set class_id='$class_id' where score_id=$score_id";
	                    $CONN->Execute($query) or die($query);

	                    //echo "#$score_id OK!!  ";
	                }
		}
		$nums=$res->RecordCount();
	}
        $fp = fopen ($up_file_name, "w");
        $temp_query = "( $score_semester )學期成績資料表\"class_id\"欄位有空值(".$nums."筆)<BR>自動進行修補 -- by infodaes (2006-4-14)";
        fwrite($fp,$temp_query);
        fclose($fp);
}

// 加入教師上傳成績單資料表
$up_file_name =$upgrade_str."score_paper_upload_06_27.txt";
if (!is_file($up_file_name) ){
        $query = "CREATE TABLE IF NOT EXISTS score_paper_upload (
  spu_sn int(5) NOT NULL auto_increment,
  curr_seme varchar(5) NOT NULL default '',
  class_num char(3) NOT NULL default '',
  file_name varchar(255) NOT NULL default '',
  log_id varchar(20) NOT NULL default '',
  time datetime default NULL,
  printed tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (spu_sn),
  UNIQUE KEY class_num (class_num,curr_seme)
) ";

        $res = $CONN->Execute($query);
        $fp = fopen ($up_file_name, "w");
        $temp_query = "加入教師上傳成績單資料表 score_paper_upload	-- by hami (2006-6-27)";
        fwrite($fp,$temp_query);
        fclose($fp);
}



// 平時成績資料表自102_2加入分組班索引欄位elective_id
$up_file_name =$upgrade_str."2014-04-10.txt";
if (!is_file($up_file_name) ){
        $query = "ALTER TABLE nor_score_102_2 ADD elective_id varchar(10) NOT NULL default ''";
        $res = $CONN->Execute($query);
        $fp = fopen ($up_file_name, "w");
        $temp_query = "平時成績資料表自102_2加入分組班索引欄位  elective_id	-- by infodaes (2014-04-10)";
        fwrite($fp,$temp_query);
        fclose($fp);
}



?>
