<?php

include "config.php";
include_once "../../include/sfs_case_dataarray.php";

sfs_check();


if($_POST['act']=='全設為普通班'){
	$sql="UPDATE school_class SET c_kind_k12ea='A' WHERE class_id like '{$_POST['work_year_seme']}_%'";
	$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);	
}


if($_POST['act']=='儲存'){
	/*
	echo "<pre>";
	print_r($_POST['kind_select']);
	echo "</pre>";
	*/
	foreach($_POST['kind_select'] as $k => $v) {
		$sql="UPDATE school_class SET c_kind_k12ea='$v' WHERE class_sn=$k";
		$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
	}
}



//秀出網頁
head("班級類型設定");
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


//取得班級類別列表(設定選項)
$class_kind = k12ea_class_kind();
$class_kind['']='虛擬班 (不輸出課表)';

//產生班級設定列表
$sql="SELECT * FROM school_class WHERE class_id LIKE '{$work_year_seme}_%' ORDER BY class_id";
$res=$CONN->Execute($sql) or user_error("讀取課表設定資料失敗！<br>$sql",256);
while(!$res->EOF) {
	$class_sn=$res->fields['class_sn'];	
	$c_year=$res->fields['c_year'];	
	$c_name=$res->fields['c_name'];
	$kind=$res->fields['c_kind_k12ea'];
	$kind_select="<select name='kind_select[$class_sn]'>";
	foreach($class_kind as $k => $v) {
		$selected = ($k==$kind) ? 'selected' : '' ;
		$bg_color = ($k==$kind) ? "style='background-color: #ffcccc;'" : '' ;
		$kind_select.="<option value='$k' $selected $bg_color>$k : $v</option>";
	}
	$kind_select.="</select>";
	
	$classes .= "<li>{$c_year}年{$c_name}班 ( $kind )：$kind_select</li>";
	
	$res->MoveNext();
}

$button = "<input type='submit' value='全設為普通班' name='act' style='border-width:1px; cursor:hand; color:white; background:#5555ff;' onclick=\"return confirm('確定要將尚未儲存類型的班級設為普通班？');\">";
$button .= "<input type='submit' value='儲存' name='act' style='border-width:1px; cursor:hand; color:white; background:#ff5555;'>";


echo "<form name='myform' method='post'>選擇學期： $semesters $button<hr><ol>$classes</ol></form>";


foot();
?>