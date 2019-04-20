<?php

include "config.php";
include_once "../../include/sfs_case_dataarray.php";

sfs_check();

if($_POST['act']=='儲存'){
	/*
	echo "<pre>";
	print_r($_POST['campus']);
	echo "</pre>";
	*/
	foreach($_POST['campus'] as $k => $v) {
		$sql="UPDATE school_class SET campus='$v' WHERE class_sn=$k";
		$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
		$note = "已於".date("Y-m-d H:i:s")."儲存設定！";
	}
}

if($_POST['act']=='全清空'){
	$sql="UPDATE school_class SET campus='' WHERE class_id like '{$_POST['work_year_seme']}_%'";
	$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
	$note = "已於".date("Y-m-d H:i:s")."清空原設定！";
}



//秀出網頁
head("班級校區設定");
print_menu($school_menu_p);

//學期別
$work_year_seme = $_REQUEST['work_year_seme'] ? $_REQUEST['work_year_seme'] : sprintf("%03d_%d",curr_year(),curr_seme());


//橫向選單標籤
echo print_menu($menu_p);

//取得年度與學期的下拉選單
$sql="SELECT distinct year,semester FROM school_class ORDER BY year desc,semester desc limit 10";
$res=$CONN->Execute($sql) or user_error("讀取課表設定資料失敗！<br>$sql",256);
$semesters="<select name='work_year_seme' onchange=\"this.form.target=''; this.form.submit();\"><option value=''>-請選擇學期-</option>";
while(!$res->EOF) {
	$year_seme=sprintf("%03d_%d",$res->fields[year],$res->fields[semester]);
	$year_seme_name=$res->fields[year].'學年度第'.$res->fields[semester].'學期';
	$selected=( $work_year_seme == $year_seme )?'selected':''; 
	$semesters.="<option $selected value=$year_seme>$year_seme_name</option>";
	
	$res->MoveNext();
}
$semesters.="</select>";


//產生班級設定列表
$sql="SELECT * FROM school_class WHERE class_id LIKE '{$work_year_seme}_%' ORDER BY class_id";
$res=$CONN->Execute($sql) or user_error("讀取課表設定資料失敗！<br>$sql",256);
while(!$res->EOF) {
	$class_sn=$res->fields['class_sn'];	
	$c_year=$res->fields['c_year'];	
	$c_name=$res->fields['c_name'];
	$campus=$res->fields['campus'];
	
	$classes .= "<li>{$c_year}年{$c_name}班 ：<input type='text' length=10 name='campus[$class_sn]' value='$campus'></li>";
	
	$res->MoveNext();
}

$button = "<input type='submit' value='全清空' name='act' style='border-width:1px; cursor:hand; color:white; background:#5555ff;' onclick=\"return confirm('確定要將已設定的班級所處校區清空？');\">";
$button .= "<input type='submit' value='儲存' name='act' style='border-width:1px; cursor:hand; color:white; background:#ff5555;'>";

$note = $note ? $note : "<font color='brown'> 無分校或分班，請全數留空即可！</font>";
echo "<form name='myform' method='post'>選擇學期： $semesters $button<hr>$note<hr><ol>$classes</ol></form>";


foot();
?>