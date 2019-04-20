<?php
//$Id: check_stud_seme.php 5310 2009-01-10 07:57:56Z hami $
require_once("./report_config.php");

//認證
sfs_check();

if ($_POST['act']=='修正班名錯誤'){
	$query = "SELECT seme_year_seme,seme_class FROM stud_seme WHERE seme_year_seme='{$_POST['sel_year']}' AND seme_class LIKE '{$_POST['sel_class']}%' GROUP BY seme_year_seme,seme_class";
	$res = $CONN->Execute($query) or die($query);
	//echo $query;exit;
	while($row = $res->FetchRow()){
		$year = intval(substr($row['seme_year_seme'],0,-1));
		$seme = substr($row['seme_year_seme'],-1);
		$class_name = class_name($year,$seme);
		$seme_class = $row['seme_class'];
		$seme_class_name = $class_name[$seme_class];
		
		$query = "UPDATE stud_seme SET seme_class_name='$seme_class_name' WHERE seme_year_seme='{$row['seme_year_seme']}' AND seme_class='$seme_class' AND seme_class_name<>'$seme_class_name'";
		$res2 = $CONN->Execute($query) or die($query);
		$str .= "<BR> 已修正 {$_POST['sel_year']} {$_POST['sel_class']} 年級 班名 ";
		
		//if ($res2->fields['cc'])
		//	echo $seme_class.$seme_class_name."--". $res2->fields['cc']."<BR>";
	}
}


//秀出網頁布景標頭
head("修正班名");


$template_dir = $SFS_PATH."/".get_store_path()."/templates/";
$smarty->left_delimiter="{{";
$smarty->right_delimiter="}}";

$smarty->assign('str',$str);

$query = "SELECT seme_year_seme FROM stud_seme WHERE seme_class<>'0' GROUP BY seme_year_seme";
$res = $CONN->Execute($query) or die($query);
$sel_row_arr = array();
while($row = $res->fetchRow())
	$sel_row_arr[$row['seme_year_seme']] = substr($row['seme_year_seme'],0,-1)."學年 第".substr($row['seme_year_seme'],-1)."學期";
$smarty->assign('sel_year_arr',$sel_row_arr);

if (isset($_POST['sel_year'])){
	$query = "select substring(seme_class,1,1) as yy from stud_seme where seme_year_seme='{$_POST['sel_year']}' GROUP BY yy";
	$res = $CONN->Execute($query) or die($query);
	$arr = array();
	while($row = $res->FetchRow()){
		$arr[$row['yy']] = $row['yy']."年級";
	}
	$smarty->assign("sel_class_arr",$arr);
}

$smarty->display($template_dir."check_stud_seme.htm");

foot();

?>
