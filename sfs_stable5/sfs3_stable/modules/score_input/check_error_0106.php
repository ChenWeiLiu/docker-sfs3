<?php
//$Id: check_error_0106.php 5922 2010-03-26 04:03:15Z hami $

include_once "../../include/config.php";
include_once "../../include/sfs_case_subjectscore.php";
include_once "../../include/sfs_case_studclass.php";

//動態設定php 執行時間，避免 timeout
set_time_limit(300) ;

sfs_check();
$query = "SELECT id_sn FROM pro_check_new WHERE pro_kind_id='1' and id_kind='教師' and id_sn='$_SESSION[session_tea_sn]'";
$res = $CONN->Execute($query);
if ($res->RecordCount()==0){
	head("本程式須升級,請連絡網管人員處理");
		echo "<BR /><BR /><CENTER><H2>成績管理程式須升級,請連絡網管人員處理</H2></CENTER>";
	foot();
	exit;

}

?>
<html>
<meta http-equiv="Content-Type" content="text/html; Charset=Big5">
<head>
<title>成績檢查</title>

</head>
<body>
<?php
if ($_GET[do_key] == "delete"){

	$query = "select min(sss_id) as min_v ,count(*) as cc,seme_year_seme,student_sn,ss_id from stud_seme_score group by seme_year_seme,student_sn,ss_id having cc >1";
	$res = $CONN->Execute($query);
	$del_str = '';
	while(!$res->EOF){
		$min_v = $res->fields[min_v];
		$seme_year_seme = $res->fields[seme_year_seme];
		$student_sn = $res->fields[student_sn];
		$ss_id= $res->fields[ss_id];
		$query = "delete from stud_seme_score where sss_id<>$min_v and seme_year_seme='$seme_year_seme' and student_sn='$student_sn' and ss_id='$ss_id'  ";
//		echo $query;exit;
		if($CONN->Execute($query))
			$del_str .= ",".$res->fields[0];
		$res->MoveNext();

	}
//	echo $del_str;
}
else if ($_GET[do_key]=='query'){
	$temp_arr = explode(",",$_GET[check_str]);
	//刪除
	if ($_GET[do_del_id]<>''){
		$query = "delete from  stud_seme_score where sss_id='$_GET[do_del_id]'";
		$CONN->Execute($query);
	}
	$query = "select * from stud_seme_score where seme_year_seme='$temp_arr[0]' and ss_id=$temp_arr[1] and student_sn=$temp_arr[2] ";
	$res2 = $CONN->Execute($query) or die($query);
	$student_sn = $res2->fields[student_sn];
	$ss_id = $res2->fields[ss_id];
	$seme_year_seme = $res2->fields[seme_year_seme];
	if ($student_sn)
		$stud_name = student_sn_to_stud_name($student_sn);
	else
		$stud_name = "沒有學號";
	if ($ss_id>0)
		$ss_name = ss_id_to_subject_name($ss_id);
	else
		$ss_name = "沒有科目代號";
	$tt_arr_1 = substr($seme_year_seme,0,3);
	$tt_arr_2 = substr($seme_year_seme,-1);
	echo  "$tt_arr_1 學年第 $tt_arr_2 學期 $stud_name $ss_name 相同成績 <hr>";
	echo "<table border=1><tr><td>科目</td><td>成績</td><td>動作</td></tr>";
	while(!$res2->EOF){
		$ss_score = $res2->fields[ss_score];
		$sss_id = $res2->fields[sss_id];
		echo "<tr><td>$ss_name</td><td>$ss_score</td>";
		if ($res2->RecordCount()>1)
			echo "<td><a href=\"$_SERVER[PHP_SELF]?check_str=$_GET[check_str]&do_key=query&do_del_id=$sss_id\" >刪除</a></td>";
		else
			echo "<td>-</td>";
		echo "</tr>";

		$res2->MoveNext();
	}
	echo "</table></body></html>";
	exit;
}

$query = "select count(*) as cc,seme_year_seme,student_sn,ss_id from stud_seme_score group by seme_year_seme,student_sn,ss_id having cc >1";
$res2 = $CONN->Execute($query) ;//or die($query);
$temp_check='';
$html = "";
$html_detail = "";
$is_ok=true;
if ($res2->RecordCount()>0){
	$is_ok =false;
	$html .= "<H3>偵測到 學期成績共有<font color=red> ".$res2->RecordCount()."</font>筆記錄重復 , <a href=\"#\" onClick=\"return OpenWindow('do_print=1','list_detail')\">查看詳細資料</a> | <a href=\"$_SERVER[PHP_SELF]?do_key=delete\" onClick=\"return confirm('$doc1_unit_name \\n確定刪除重覆的成績僅保留一筆?')\">全部只保留一筆</a></H3>";
}

if($_GET[do_print]==1){
	while(!$res2->EOF){
		$student_sn = $res2->fields[student_sn];
		$ss_id = $res2->fields[ss_id];
		$seme_year_seme= $res2->fields[seme_year_seme];
		if ($student_sn)
			$stud_name = student_sn_to_stud_name($student_sn);
		else
			$stud_name = "沒有學號";
		$cc = $res2->fields[cc];
		if($ss_id>0)
			$ss_name = ss_id_to_subject_name($ss_id);
		else
			$ss_name = "沒有科目代號";
		$tt_arr_1 = substr($seme_year_seme,0,3);
		$tt_arr_2 = substr($seme_year_seme,-1);

		$temp_str = "$tt_arr_1 學年第 $tt_arr_2 學期 $stud_name $ss_name $test_kind $test_sort 階段 有 $cc 筆相同成績 ";
		$temp_uri = "$seme_year_seme,$ss_id,$student_sn";
		$html_detail .= "$temp_str <a href=\"#\" onClick=\"return OpenWindow('check_str=$temp_uri&do_key=query','detail')\">查看</a> <br />";
		$res2->MoveNext();
	}
}


if($is_ok == false){
	echo $html;
	echo $html_detail;
}


//修改 primary key
else{

	$table = "stud_seme_score";
	$temp_table = $table."_tt";
	$CONN->Execute("DROP TABLE IF EXISTS $temp_table");
	$query ="
	CREATE TABLE $temp_table (
	  sss_id bigint(20) unsigned NOT NULL auto_increment,
	  seme_year_seme varchar(6) NOT NULL default '',
	  student_sn int(10) unsigned NOT NULL default '0',
	  ss_id smallint(5) unsigned NOT NULL default '0',
	  ss_score decimal(4,2) default NULL,
 	  ss_score_memo text,
	  PRIMARY KEY  (seme_year_seme,student_sn,ss_id),
	  UNIQUE KEY sss_id(sss_id)
	) ";
	$CONN->Execute($query) or die($query);

	if ($CONN->Execute("INSERT INTO `$temp_table` SELECT * FROM `$table`")){
		$CONN->Execute("DROP TABLE $table");
		$CONN->Execute("ALTER TABLE `$temp_table` RENAME `$table`");
		$CONN->Execute("ALTER TABLE `$table` ADD `teacher_sn` SMALLINT NOT NULL");
	 	$CONN->Execute("ALTER TABLE `$table` ADD `ss_update_time` TIMESTAMP NOT NULL");

	}
	$upgrade_path = "upgrade/".get_store_path();
	$upgrade_str = set_upload_path("$upgrade_path");
	$up_file_name =$upgrade_str."score_mester_change_0106.txt";
	$temp_query = "修改學期資料表結構 -- by hami (2003-11-10)\n
		更新人員：".$_SESSION['session_tea_name'].$_SESSION['session_who'];
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose($fd);

	$message="<table cellspacing=1 cellpadding=6 border=0 bgcolor='#FFFF35' width='80%' align='center'><tr><td align='center' bgcolor='#FFFFFF' width='90%'> 學期成績資料表已更新完成！<br></td></tr></table>";
	head("更新完成!!");
	echo $message;
	foot();
	exit;
}


?>
</body>
</html>
<script language="JavaScript">
	var remote=null;
	function OpenWindow(p,name){
		strFeatures ="top=10,left=20,width=500,height=200,toolbar=0,resizable=yes,scrollbars=yes,status=0";
		remote = window.open("<?php echo $_SERVER[PHP_SELF] ?>?"+p,name, strFeatures);
	if (remote != null) {
		if (remote.opener == null)
			remote.opener = self;
	}
		if (x == 1) { return remote; }
	}

	function checkok() {
	return true;
	}

</script>
