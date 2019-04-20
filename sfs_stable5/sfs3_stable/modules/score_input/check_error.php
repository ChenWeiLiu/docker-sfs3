<?php
//$Id: check_error.php 6190 2010-09-23 23:44:21Z hami $

include_once "../../include/config.php";
include_once "../../include/sfs_case_subjectscore.php";
include_once "../../include/sfs_case_studclass.php";

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

	$query = "select min(score_id) as min_v ,count(*) as cc,class_id,student_sn,ss_id,test_kind,test_sort from $_GET[table] group by class_id,student_sn,ss_id,test_kind,test_sort having cc >1";
	$res = $CONN->Execute($query);
	$del_str = '';
	while(!$res->EOF){
		$min_v = $res->fields[min_v];
		$class_id = $res->fields[class_id];
		$student_sn = $res->fields[student_sn];
		$ss_id= $res->fields[ss_id];
		$test_kind=$res->fields[test_kind];
		$test_sort=$res->fields[test_sort];
		$query = "delete from $_GET[table] where score_id<>$min_v and class_id='$class_id' and student_sn='$student_sn' and ss_id='$ss_id' and test_kind='$test_kind' and test_sort='$test_sort' ";
	//	echo $query;exit;
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
		$query = "delete from  $temp_arr[0] where score_id='$_GET[do_del_id]'";
		$CONN->Execute($query);
	}
	$query = "select * from $temp_arr[0] where class_id='$temp_arr[1]' and student_sn=$temp_arr[2] and ss_id=$temp_arr[3] and test_kind='$temp_arr[4]' and test_sort=$temp_arr[5] ";
	$res2 = $CONN->Execute($query);
	$class_id = $res2->fields[class_id];
	$student_sn = $res2->fields[student_sn];
	$ss_id = $res2->fields[ss_id];
	$test_kind = $res2->fields[test_kind];
	$test_sort = $res2->fields[test_sort];
	if ($student_sn)
		$stud_name = student_sn_to_stud_name($student_sn);
	else
		$stud_name = "沒有學號";
	if ($ss_id>0)
		$ss_name = ss_id_to_subject_name($ss_id);
	else
		$ss_name = "沒有科目代號";
	$tt_arr = explode ("_",$class_id);
	echo  $tt_arr[0]."學年第".$tt_arr[1]."學期".$tt_arr[2]."年級".$tt_arr[3]."班 $stud_name $ss_name $test_kind $test_sort 階段 相同成績 <hr>";
	echo "<table border=1><tr><td>成績</td><td>建立時間</td><td>動作</td></tr>";
	while(!$res2->EOF){
		$score = $res2->fields[score];
		$score_id = $res2->fields[score_id];
		$update_time = $res2->fields[update_time];
		echo "<tr><td>$score</td><td>$update_time</td>";
		if ($res2->RecordCount()>1)
			echo "<td><a href=\"$_SERVER[PHP_SELF]?check_str=$_GET[check_str]&do_key=query&do_del_id=$score_id\" >刪除</a></td>";
		else
			echo "<td>-</td>";
		echo "</tr>";

		$res2->MoveNext();
	}
	echo "</table></body></html>";
	exit;
}

$query = "show tables like 'score_semester_%'";
$res = $CONN->Execute($query) or die($query);
$html = "";
$html_detail = "";
$is_ok=true;
$res->MoveFirst();
$table_all_arr = array();
while(!$res->EOF){

	$table=$res->fields[0];
	if ($table=='score_semester_move') continue;
	$table_all_arr[] = $table;
	$query = "select count(*) as cc,class_id,student_sn,ss_id,test_kind,test_sort from $table group by class_id,student_sn,ss_id,test_kind,test_sort having cc >1";
	$res2 = $CONN->Execute($query) or die($query);
	$table_arr = explode("_",$table);
	if ($res2->RecordCount()>0){
		$is_ok =false;
		$html .= "<H3>偵測到<font color=red> $table_arr[2]</font> 學年第 <font color=red>$table_arr[3]</font>學期共有<font color=red>".$res2->RecordCount()."</font>筆記錄重復 , <a href=\"#\" onClick=\"return OpenWindow('do_print=$table','list_detail')\">查看詳細資料</a> | <a href=\"$_SERVER[PHP_SELF]?do_key=delete&table=$table\" onClick=\"return confirm('$doc1_unit_name \\n確定刪除重覆的成績僅保留一筆?')\">全部只保留一筆</a></H3>";
	}
	if($_GET[do_print]==$table){
		while(!$res2->EOF){
			$class_id = $res2->fields[class_id];
			$student_sn = $res2->fields[student_sn];
			$ss_id = $res2->fields[ss_id];
			$test_kind = $res2->fields[test_kind];
			$test_sort = $res2->fields[test_sort];
			if ($student_sn)
				$stud_name = student_sn_to_stud_name($student_sn);
			else
				$stud_name = "沒有學號";
			$cc = $res2->fields[cc];
			if($ss_id>0)
				$ss_name = ss_id_to_subject_name($ss_id);
			else
				$ss_name = "沒有科目代號";
			$tt_arr = explode ("_",$class_id);
			$temp_str = $tt_arr[0]."學年第".$tt_arr[1]."學期".$tt_arr[2]."年級".$tt_arr[3]."班 $stud_name $ss_name $test_kind $test_sort 階段 有 $cc 筆相同成績 ";
			$temp_uri = "$table,$class_id,$student_sn,$ss_id,$test_kind,$test_sort";
			$html_detail .= "$temp_str <a href=\"#\" onClick=\"return OpenWindow('check_str=$temp_uri&do_key=query','detail')\">查看</a> <br />";
			$res2->MoveNext();
		}
	}

	$res->MoveNext();
}

if($is_ok == false){
	echo $html;
	echo $html_detail;
}


//修改 primary key
else{
	for($i=0;$i<count($table_all_arr);$i++){
		$table = $table_all_arr[$i];
		$temp_table = $table."_tt";
		$CONN->Execute("DROP TABLE IF EXISTS $temp_table");
		$query ="CREATE TABLE $temp_table (
		  score_id bigint(10) unsigned NOT NULL auto_increment,
		  class_id varchar(11) NOT NULL default '',
		  student_sn int(10) unsigned NOT NULL default '0',
		  ss_id smallint(5) unsigned NOT NULL default '0',
		  score float unsigned NOT NULL default '0',
		  test_name varchar(20) NOT NULL default '',
		  test_kind varchar(10) NOT NULL default '定期評量',
		  test_sort tinyint(3) unsigned NOT NULL default '0',
		  update_time datetime NOT NULL default '0000-00-00 00:00:00',
		  sendmit enum('0','1') NOT NULL default '1',
		  PRIMARY KEY  (student_sn,ss_id,test_kind,test_sort),
		  UNIQUE KEY score_id (score_id)
		) ";

		$CONN->Execute($query) or die($query);

		if ($CONN->Execute("INSERT INTO `$temp_table` SELECT * FROM `$table`")){
			$CONN->Execute("DROP TABLE $table");
			$CONN->Execute("ALTER TABLE `$temp_table` RENAME `$table`");
			$CONN->Execute("ALTER TABLE `$table` ADD `teacher_sn` SMALLINT NOT NULL");

		}
	}
	$upgrade_path = "upgrade/".get_store_path();
	$upgrade_str = set_upload_path("$upgrade_path");
	$up_file_name =$upgrade_str."score_mester_chane.txt";
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
