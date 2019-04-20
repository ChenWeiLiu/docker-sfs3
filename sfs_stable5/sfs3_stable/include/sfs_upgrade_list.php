<?php

//$Id: sfs_upgrade_list.php 9217 2018-04-13 03:32:14Z smallduh $

if(!$CONN){
        echo "go away !!";
        exit;
}

// 本檔案為系統重要更新檔案,更新記錄將自動寫入 上傳檔案目錄 $UPLOAD_PATH/upgrade/include 下
// 預設升級程式目錄在 sfs3/include/upgrade_files/

// 更新記錄檔路徑
$upgrade_path = "upgrade/include/";
$upgrade_str = set_upload_path("$upgrade_path");

$temp_str ="2003-06-24 更新學生學期記錄表 stud_seme , 加入 student_sn 欄位,使 stud_base與stud_seme 兩個表同步,方便資料查詢 - by hami \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up20030624.php";
$up_file_name =$upgrade_str."2003-06-24.txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up20030624.php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

//如未調整 pro_check_new 時,調整
$temp_str ="2003-06-27 調整 pro_check_new 屬性 by hami \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up20030627.php";
$up_file_name =$upgrade_str."2003-06-27.txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up20030627.php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

//更新reward,
$temp_str ="2003-11-28 更新 reward 表 by brucelyc \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up20031128.php";
$up_file_name =$upgrade_str."2003-11-28.txt";
if ($CONN->Execute("select * from reward where 1=0")) {
	if (!is_file($up_file_name)){
		require dirname(__FILE__)."/upgrade_files/up20031128.php";
		$fp = fopen ($up_file_name, "w");
		fwrite($fp,$temp_str);
		fclose ($fp);
	}
}

//更新取消 @2006-10-11
$temp_str ="2003-12-05 因 course_table 表會再重建, 故本更新取消 by brucelyc \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up20031205.php";
$up_file_name =$upgrade_str."2003-12-05.txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up20031205.php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

//重建course_table, 並建time_table為新課表設定做預備
$temp_str ="2003-12-06 重建 course_table 表, 並建 time_table by brucelyc \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up20031206.php";
$up_file_name =$upgrade_str."2003-12-06.txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up20031206.php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

//建立本學期name_list
$temp_str ="2003-12-30 建立 name_list 表 by brucelyc \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up20031230.php";
$up_file_name =$upgrade_str."2003-12-30.txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up20031230.php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

//在stud_absent中增加month欄位，以便計算全勤
$temp_str ="2004-01-02 在 stud_absent 表中增加 month 欄位 by brucelyc \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up20040102.php";
$up_file_name =$upgrade_str."2004-01-02.txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up20040102.php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

//在course_table中增加欄位，以便用來配課
$temp_str ="2004-01-30 在 course_table 表中增加sections, test_times, ratio_chg, times_chg, year, semester欄位 by brucelyc \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up20040102.php";
$up_file_name =$upgrade_str."2004-01-30.txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up20040130.php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

//修改日常表現分數欄的屬性
$temp_str ="2004-04-19 修改 seme_score_nor 表中 score1~score7 的屬性 by brucelyc \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up20040419.php";
$up_file_name =$upgrade_str."2004-04-19.txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up20040419.php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

//增加郵遞區號欄
$temp_str ="2004-04-29 在 stud_base 表中增加 addr_zip 欄 by brucelyc \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up20040429.php";
$up_file_name =$upgrade_str."2004-04-29.txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up20040429.php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

//在學生異動表中增加 student_sn 欄
$temp_str ="2004-05-13 在 stud_move 表中增加 student_sn 欄 by brucelyc \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up20040513.php";
$up_file_name =$upgrade_str."2004-05-13.txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up20040513.php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

//修正期中成績表內的class_id錯誤
$temp_str ="2004-07-14 修正期中成績表內的class_id錯誤 by brucelyc \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up20040714.php";
$up_file_name =$upgrade_str."2004-07-14.txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up20040714.php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

//將學生相關資料表加入 student_sn
/*
$temp_str ="2004-07-25 將學生相關資料表加入 student_sn by hami \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up20040725.php";

$up_file_name =$upgrade_str."2004-07-25.txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up20040725.php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fd);
}
*/

//修正學成異動表內的student_sn未寫入正確值
$temp_str ="2004-08-14 修正學成異動表內的student_sn成正確值 by brucelyc \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up20040814.php";
$up_file_name =$upgrade_str."2004-08-14.txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up20040814.php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

//在score_course表中增加c_kind欄位, 記錄該節是0:正常時數, 1:兼課, 2:代課
$temp_str ="2004-09-01 在score_course表中增加c_kind欄位 by brucelyc \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up20040901.php";
$up_file_name =$upgrade_str."2004-09-01.txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up20040901.php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

// 同步學生異動檔 (stud_move) 與學籍記錄檔 (stud_base) 資料
$temp_str ="2004-09-27 步學生異動檔 (stud_move) 與學籍記錄檔 (stud_base) 資料 ".dirname(__FILE__)."/upgrade_files/up20040927.php";
$up_file_name =$upgrade_str."2004-09-27.txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up20040927.php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

//新增開學日及結業日
$temp_str ="2004-10-01 新增開學日及結業日 by brucelyc \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up20041001.php";
$up_file_name =$upgrade_str."2004-10-01.txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up20041001.php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

//修正專科教室新增限制節次欄未建立的錯誤
$temp_str ="2004-12-01 修正專科教室新增限制節次欄未建立的錯誤 by brucelyc \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up20041201.php";
$up_file_name =$upgrade_str."2004-12-01.txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up20041201.php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

//修正系統調整欄位格式
$temp_str ="2005-04-06  修正系統調整欄位格式 by hami \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up20050406.php";
$up_file_name =$upgrade_str."2005-04-06.txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up20050406.php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

//更新取消 @2006-10-11
$temp_str ="2005-09-04 更新移至up20051014 by brucelyc \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up20050904.php";
$up_file_name =$upgrade_str."2005-09-04.txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up20050904.php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

//將出生地加入系統選項清單
$temp_str ="2005-10-06 將出生地加入系統選項清單 by brucelyc \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up20051006.php";
$up_file_name =$upgrade_str."2005-10-06.txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up20051006.php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

//在戶籍資料中加入student_sn
$temp_str ="2005-10-14 在戶籍資料中加入student_sn by brucelyc \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up20051014.php";
$up_file_name =$upgrade_str."2005-10-14.txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up20051014.php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

//在兄弟姐妹資料中加入student_sn
$temp_str ="2005-10-17 在兄弟姐妹資料中加入student_sn by brucelyc \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up20051017.php";
$up_file_name =$upgrade_str."2005-10-17.txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up20051017.php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

//在其他親屬資料中加入student_sn
$temp_str ="2005-10-18 在其他親屬資料中加入student_sn by brucelyc \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up20051018.php";
$up_file_name =$upgrade_str."2005-10-18.txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up20051018.php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

//將teacher_base中的login_pass改為以md5雜湊運算
$temp_str ="2005-12-28 將teacher_base中的login_pass改為以md5雜湊運算 by brucelyc \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up20051228.php";
$up_file_name =$upgrade_str."2005-12-28.txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up20051228.php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

//將stud_seme_score_oth,stud_seme_rew,stud_seme_score_nor三資料表新增點由academic_record移至系統
$temp_str ="2006-09-19 將stud_seme_score_oth,stud_seme_rew,stud_seme_score_nor三資料表新增點由academic_record移至系統 by brucelyc \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up20060919.php";
$up_file_name =$upgrade_str."2006-09-19.txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up20060919.php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

//在stud_move表中新增轉出入縣市欄位
$temp_str ="2006-10-12 在stud_move表中新增轉出入縣市欄位 by brucelyc \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up20061012.php";
$up_file_name =$upgrade_str."2006-10-12.txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up20061012.php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

//在stud_base表中增加英文姓名、戶籍遷入日期欄位
$temp_str ="2006-10-24 在stud_base表中增加英文姓名、戶籍遷入日期欄位 by brucelyc \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up20061024.php";
$up_file_name =$upgrade_str."2006-10-24.txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up20061024.php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

//在 stud_domicile 更新 student_sn 資料
$temp_str ="2006-10-28 在stud_domicile 中補 student_sn 資料 by hami \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up20061028.php";
$up_file_name =$upgrade_str."2006-10-28.txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up20061028.php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

//加入檢核表資料表
$temp_str ="2006-11-26 加入檢核表資料表 by brucelyc \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up20061126.php";
$up_file_name =$upgrade_str."2006-11-26.txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up20061126.php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

//加入自動修正檢核表資料表
$temp_str ="2007-01-05 加入自動修正檢核表資料表 by brucelyc \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up20070105.php";
$up_file_name =$upgrade_str."2007-01-05.txt";
if (!is_file($up_file_name)){
        require dirname(__FILE__)."/upgrade_files/up20070105.php";
        $fp = fopen ($up_file_name, "w");
        fwrite($fp,$temp_str);
        fclose ($fp);
}

//合併檢核表文字
$temp_str ="2007-01-14 合併檢核表文字 by brucelyc \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up20070114.php";
$up_file_name =$upgrade_str."2007-01-14.txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up20070114.php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

//加入錯誤登入資料表
$dstr="2007-01-15";
$dsstr=str_replace("-","",$dstr);
$temp_str =$dstr." 加入錯誤登入資料表 by brucelyc \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

//加入設定週別資料表
$dstr="2007-01-25";
$dsstr=str_replace("-","",$dstr);
$temp_str =$dstr." 加入設定週別資料表 by brucelyc \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

//在學生學期獎懲紀錄表中加入student_sn欄位
//這是一個特殊的更新, 因為資料量過多, 所以每次只處理10個人, 一直要到等到完全處理完, 網站的速度才會恢復
$dstr="2007-04-03";
$dsstr=str_replace("-","",$dstr);
$temp_str =$dstr." 在學生學期獎懲紀錄表中加入student_sn欄位 by brucelyc \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
	$query="select count(student_sn) from stud_seme_rew where student_sn=0";
	$res=$CONN->Execute($query);
	if ($res->fields[0]==0) {
		$fp = fopen ($up_file_name, "w");
		fwrite($fp,$temp_str);
		fclose ($fp);
	}
}

//更改stud_base表的primary key
$dstr="2007-04-12";
$dsstr=str_replace("-","",$dstr);
$temp_str =$dstr." 更改stud_base表的primary key by brucelyc \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

//建立class_comment_admin表
$dstr="2007-06-27";
$dsstr=str_replace("-","",$dstr);
$temp_str =$dstr." 建立class_comment_admin表 by brucelyc \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

//修正stud_addr_zip表內三筆資料
$dstr="2008-03-22";
$dsstr=str_replace("-","",$dstr);
$temp_str =$dstr." 修正stud_addr_zip表內三筆資料, 分別將北屯, 西屯, 南屯改成北屯區, 西屯區及南屯區 by brucelyc \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
        require dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
        $fp = fopen ($up_file_name, "w");
        fwrite($fp,$temp_str);
        fclose ($fp);
}

//刪除隱私資料檔
$dstr="secure";
$temp_str ="刪除隱私資料檔 by brucelyc \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/".$dstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/".$dstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

//刪除 Module_Path.php 檔
$dstr="secure_path";
$temp_str ="刪除 data 下 Module_Path.php by hami \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/".$dstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/".$dstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}


//增加 stud_absent_move 資料表
$dstr="stud_absent_move";
$temp_str ="增加 stud_absent_move 資料表 (轉學生期中缺席用) by infodaes \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/".$dstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/".$dstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}


//增加 association 資料表(XML交換  期中社團記錄暫存用)
$dstr="association";
$temp_str ="增加 association 資料表 (社團參加紀錄用) by infodaes \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/".$dstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/".$dstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

//增加 reward_exchange 資料表(XML交換   期中獎懲暫存用)
$dstr="reward_exchange";
$temp_str ="增加 reward_exchange 資料表(XML交換 期中獎懲暫存用) by infodaes \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/".$dstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/".$dstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

//修正sfs_text 資料表內   殘障==>身障 (XML3.0定義)
$dstr="sfs_text_correction_1";
$temp_str ="修正sfs_text 資料表內   殘障==>身障 (XML3.0定義) by infodaes \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/".$dstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/".$dstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

//建立login_log_new表
$dstr="2009-09-21";
$dsstr=str_replace("-","",$dstr);
$temp_str =$dstr." 建立login_log_new表 by brucelyc \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

//在login_log_new表新增ip欄位
$dstr="2009-09-22";
$dsstr=str_replace("-","",$dstr);
$temp_str =$dstr." 在login_log_new表新增ip欄位 by brucelyc \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

//重建login_log_new表
$dstr="2009-10-21";
$dsstr=str_replace("-","",$dstr);
$temp_str =$dstr." 重建login_log_new表 by brucelyc \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

//把stud_base表addr_zip欄位長度改為5
$dstr="2009-12-15";
$dsstr=str_replace("-","",$dstr);
$temp_str =$dstr." 把stud_base表addr_zip欄位長度改為5 by brucelyc \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

//更改 系統選項值 唱歌->歌唱
$dstr="2010-08-02";
$dsstr=str_replace("-","",$dstr);
$temp_str =$dstr." 把sfs_text 改 系統選項值 唱歌->歌唱 \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

//把stud_domicile表的主鍵改為student_sn
$dstr="2010-08-15";
$dsstr=str_replace("-","",$dstr);
$temp_str =$dstr." 把stud_domicile表的主鍵改為student_sn \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

//把data目錄中的php檔刪除
$dstr="2010-09-01";
$dsstr=str_replace("-","",$dstr);
$temp_str =$dstr." 把data目錄中的php檔刪除 \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

//增加 stud_move_import、stud_seme_import、stud_seme_final_score資料表
$dstr="2010-09-02";
$dsstr=str_replace("-","",$dstr);
$temp_str =$dstr." 增加 stud_move_import、stud_seme_import、stud_seme_final_score資料表 \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

//檢查學務系統版本
$dstr="2010-09-14";
$dsstr=str_replace("-","",$dstr);
$temp_str =$dstr." 檢查學務系統版本 \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
	//$fp = fopen ($up_file_name, "w");
	//fwrite($fp,$temp_str);
	//fclose ($fp);
}

//增加學生訪談記錄表student_sn、interview欄位  並修正資料
$dstr="2011-05-03";
$dsstr=str_replace("-","",$dstr);
$temp_str =$dstr." 更改teach_id為teacher_sn \n 增加學生訪談記錄表student_sn、interview欄位 \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

//修正縣市合併後郵政區號地址參照表
$dstr="2011-08-11";
$dsstr=str_replace("-","",$dstr);
$temp_str =$dstr." 修正縣市合併後郵政區號地址參照表 \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

//增加平時成績項目參照功能
$dstr="2011-10-06";
$dsstr=str_replace("-","",$dstr);
$temp_str =$dstr." 增加平時成績項目參照功能 \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}


//補正增加平時成績項目參照功能
$dstr="2011-10-11";
$dsstr=str_replace("-","",$dstr);
$temp_str =$dstr." 補正增加平時成績項目參照功能SQL多了`字元以致無法正確更新問題! \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

//增加課程設定score_ss每週節次記錄欄位
$dstr="2011-10-18";
$dsstr=str_replace("-","",$dstr);
$temp_str =$dstr."增加課程設定score_ss每週節次記錄欄位! \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

//增加自然人憑證序號欄位
$dstr="2012-06-01";
$dsstr=str_replace("-","",$dstr);
$temp_str =$dstr."增加自然人憑證序號欄位! \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

//增加模組認證強度欄位
$dstr="2012-06-02";
$dsstr=str_replace("-","",$dstr);
$temp_str =$dstr."增加模組認證強度欄位! \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

//增加個資記錄表
$dstr="2012-07-10";
$dsstr=str_replace("-","",$dstr);
$temp_str =$dstr."增加個資記錄表pipa! \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}
//修正個資記錄表pipa索引
$dstr="2012-07-11";
$dsstr=str_replace("-","",$dstr);
$temp_str =$dstr."修正個資記錄表pipa索引 \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

//增加個資記錄表
$dstr="2012-07-12";
$dsstr=str_replace("-","",$dstr);
$temp_str =$dstr."增加個資記錄表pipa! \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

//修正臺中市406-408 區域名
$dstr="2012-08-25";
$dsstr=str_replace("-","",$dstr);
$temp_str =$dstr."修正臺中市406-408 區域名! \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

//修正臺中市406-408 區域名
$dstr="2012-09-23";
$dsstr=str_replace("-","",$dstr);
$temp_str =$dstr."新增認證模式欄位 \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

//sfs_text增加生涯輔導紀錄101學年版選項
$dstr="2013-01-29";
$dsstr=str_replace("-","",$dstr);
$temp_str =$dstr."新增認證模式欄位 \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

//學生密碼長度改為32個字元
$dstr="2013-02-20";
$dsstr=str_replace("-","",$dstr);
$temp_str =$dstr."新增認證模式欄位 \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

//去除`stud_base`.stud_study_year欄位的UNSIGNED屬性
$dstr="2013-02-25";
$dsstr=str_replace("-","",$dstr);
$temp_str =$dstr."去除`stud_base`.stud_study_year欄位的UNSIGNED屬性 \n 更新檔位置: ".dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

//去除`stud_base`.stud_study_year欄位的UNSIGNED屬性
$dstr="2013-08-22";
$dsstr=str_replace("-","",$dstr);
$temp_str = "teacher_title 加上 rank 排序欄位: ".dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}


// 加入教師學生 sha 256 欄位
$dstr="2013-09-18";
$dsstr=str_replace("-","",$dstr);
$temp_str = "teacher_base stud_base 加上 edu_key 欄位: ".dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

// 加入教師學生 md5 密碼
$dstr="2013-09-20";
$dsstr=str_replace("-","",$dstr);
$temp_str = "重新升級 teacher_base stud_base 加上 ldap_password 欄位: ".dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

// 建立師生帳號欄位 view
$dstr="2013-10-11";
$dsstr=str_replace("-","",$dstr);
$temp_str = "建立師生帳號欄位 view ".dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

// 建立學校上課日欄位
$dstr="2013-10-29";
$dsstr=str_replace("-","",$dstr);
$temp_str = "建立學校上課日欄位 ".dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}


// 檢查輔導訪談記錄表interview欄位是否有資料
$dstr="2014-08-03";
$dsstr=str_replace("-","",$dstr);
$temp_str = "檢查輔導訪談記錄表interview欄位是否有資料 ".dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

// 刪除新生匯入資料暫存檔
$dstr="2014-09-15";
$dsstr=str_replace("-","",$dstr);
$temp_str = "刪除新生匯入資料暫存檔 ".dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
    require dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
    $fp = fopen ($up_file_name, "w");
    fwrite($fp,$temp_str);
    fclose ($fp);
}


// 學生基本資料加入 學籍取得原因、個案保護類別 欄位

$dstr="2014-10-08";
$dsstr=str_replace("-","",$dstr);
$temp_str = "學生基本資料加入 學籍取得原因、個案保護類別 欄位: ".dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

/*
$dstr="2015-10-04";
$dsstr=str_replace("-","",$dstr);
$temp_str = "校正畢業生流水號避免學號十年重複問題: ".dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}
*/

$dstr="2015-10-15";
$dsstr=str_replace("-","",$dstr);
$temp_str = "校正畢業生流水號避免學號十年重複問題: ".dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}



$dstr="2015-11-21";
$dsstr=str_replace("-","",$dstr);
$temp_str = "校正校園報名系統有些學生姓名無法顯示的問題: ".dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}


$dstr="2015-12-16";
$dsstr=str_replace("-","",$dstr);
$temp_str = "修改學生英文姓名欄位長度為50個字元 ".dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}


$dstr="2016-01-27";
$dsstr=str_replace("-","",$dstr);
$temp_str = "修改pro_check_new資料表is_admin欄位為enum('0','1','2') ".dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}


$dstr="2016-03-09";
$dsstr=str_replace("-","",$dstr);
$temp_str = "增加cita_kind資料表teach_id欄位 ".dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}


$dstr="2016-04-07";
$dsstr=str_replace("-","",$dstr);
$temp_str = "修改pro_check_new資料表is_admin欄位為enum('0','1','2','3') ".dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

$dstr="2016-05-17";
$dsstr=str_replace("-","",$dstr);
$temp_str = "增加teacher_base資料表last_chpass_time欄位及mem_array欄位 ".dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}


$dstr="2016-10-31";
$dsstr=str_replace("-","",$dstr);
$temp_str = "增加student_view資料表3欄位：birth_year  birth_month  stud_mail ".dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

$dstr="2016-11-01";
$dsstr=str_replace("-","",$dstr);
$temp_str = "增加student_view資料表2欄位：stud_id、stud_mail ".dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}


$dstr="2016-11-28";
$dsstr=str_replace("-","",$dstr);
$temp_str = "修改stud_grad_year欄位型態，避免無法印出三年前學籍表問題".dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}


$dstr="2017-06-06";
$dsstr=str_replace("-","",$dstr);
$temp_str = "增加國前署課表匯入課程對應5欄位".dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}


$dstr="2017-06-07";
$dsstr=str_replace("-","",$dstr);
$temp_str = "更改心理測驗統計解釋欄位型態為text".dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

$dstr="2017-06-11";
$dsstr=str_replace("-","",$dstr);
$temp_str = "修正國教署課程對應本土語言別欄位拼字落字問題".dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}


$dstr="2017-07-06";
$dsstr=str_replace("-","",$dstr);
$temp_str = "增加國教署課表對應班級類型紀錄欄位".dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

$dstr="2017-11-23";
$dsstr=str_replace("-","",$dstr);
$temp_str = "增加教師提敘考核的必要資料表".dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

$dstr="2017-11-29";
$dsstr=str_replace("-","",$dstr);
$temp_str = "增加提敘考核資料表欄位(薪額類別)".dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

$dstr="2017-11-30";
$dsstr=str_replace("-","",$dstr);
$temp_str = "增加提敘考核資料表欄位(專業加給)".dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

$dstr="2017-12-04";
$dsstr=str_replace("-","",$dstr);
$temp_str = "school_class增加班級校區欄位以區別是本校或分校、分班的班級".dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

$dstr="2018-01-09";
$dsstr=str_replace("-","",$dstr);
$temp_str = "刪除 stud_base , stud_seme 裡 stud_id 的 FULLTEXT INDEX 設定";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

//stud_base增加實驗教育分類設定欄位
$dstr="2018-03-27";
$dsstr=str_replace("-","",$dstr);
$temp_str = "增加 stud_base 裡實驗教育類別欄位設定";
$up_file_name =$upgrade_str.$dstr.".txt";
if (!is_file($up_file_name)){
	require dirname(__FILE__)."/upgrade_files/up".$dsstr.".php";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

?>
