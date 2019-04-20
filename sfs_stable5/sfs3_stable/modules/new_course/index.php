<?php
// $Id: index.php 5310 2009-01-10 07:57:56Z hami $

/* 取得基本設定檔 */
include "config.php";

//sfs_check();
$teacher_sn=$_SESSION['session_tea_sn'];
$year_seme = $_REQUEST['year_seme'];
$class_id = $_REQUEST['class_id'];

//若有選擇學年學期，進行分割取得學年及學期
if(!empty($year_seme)){
	$ys=explode("-",$year_seme);
	$sel_year=$ys[0];
	$sel_seme=$ys[1];
}

if(empty($sel_year))$sel_year = curr_year(); //目前學年
if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期

//錯誤設定
if($error==1){
	$act="error";
	$error_title="無年級和班級設定";
	$error_main="找不到 $sel_year 學年度第 $sel_seme 學期的年級設定，故您無法使用此功能。<ol><li>請先到『<a href='".$SFS_PATH_HTML."school_affairs/every_year_setup/class_year_setup.php'>班級設定</a>』設定年級以及班級資料。<li>以後記得每一學期的學期出都要設定一次喔！</ol>";
}

//執行動作判斷
if($act=="error"){
	$main=error_tbl($error_title,$error_main);
}else{
	$main=class_form_search($sel_year,$sel_seme);
}


//秀出網頁
if (!$IS_STANDALONE) head("班級課表查詢");

echo $main;
if (!$IS_STANDALONE) foot();

/*
函式區
*/

//基本設定表單
function class_form_search($sel_year,$sel_seme){
	global $school_menu_p,$PHP_SELF,$view_tsn,$teacher_sn,$class_id,$act,$IS_STANDALONE;
	if(empty($view_tsn))$view_tsn=$teacher_sn;

	//年級與班級選單
	$class_select=get_class_select($sel_year,$sel_seme,"","class_id","jumpMenu",$class_id);

	if(empty($class_select))	header("location:$PHP_SELF?sel_year&sel_seme=$sel_seme&error=1");


	//取得年度與學期的下拉選單
	$date_select=class_ok_setup_year($sel_year,$sel_seme,"year_seme","jumpMenu_seme");

	$tool_bar=(!$IS_STANDALONE)?make_menu($school_menu_p):"";

	$list_class_table=search_class_table($sel_year,$sel_seme,$class_id,"view");

	$main="
	<script language='JavaScript'>
	function jumpMenu(){
		location=\"$PHP_SELF?act=$act&&year_seme=\" + document.myform.year_seme.options[document.myform.year_seme.selectedIndex].value + \"&class_id=\" + document.myform.class_id.options[document.myform.class_id.selectedIndex].value;
	}
	function jumpMenu_seme(){
		if(document.myform.year_seme.options[document.myform.year_seme.selectedIndex].value!=''){
			location=\"$PHP_SELF?act=$act&year_seme=\" + document.myform.year_seme.options[document.myform.year_seme.selectedIndex].value;
		}
	}
	</script>
	$tool_bar
	<table cellspacing='1' cellpadding='4'  bgcolor=#9EBCDD>
	<form action='$PHP_SELF' method='post' name='myform'>
	<tr bgcolor='#F7F7F7'>
	<td>$date_select</td>
	<td>班級： $class_select</td>
	</tr>
	</form>
	</table>
	$list_class_table
	";
	return $main;
}

?>
