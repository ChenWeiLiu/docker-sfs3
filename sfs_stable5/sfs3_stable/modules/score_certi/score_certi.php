<?php

// $Id: score_certi.php 9240 2018-05-04 03:25:04Z igogo $

// 載入設定檔
include "../../include/config.php";
include "../../include/sfs_case_subjectscore.php";
include "../../include/sfs_case_dataarray.php";
include "../../include/sfs_oo_zip2.php";
include "../../include/sfs_case_PLlib.php";    
require_once "./module-cfg.php";

// 認證檢查
sfs_check();

$stud_id=$_POST['stud_id'];
$student_sn=$_POST['student_sn'];
$student_ename=$_POST['student_ename'];
$kind=$_POST['kind'];
$nor_score=$_POST['nor_score'];
$school_move_num=$_POST['school_move_num'];
$c_word=$_POST['c_word'];
$c_num=$_POST['c_num'];
$m_reason=$_POST['m_reason'];
$m_unit=$_POST['m_unit'];
$m_date=$_POST['m_date'];
$m_word=$_POST['m_word'];
$m_num=$_POST['m_num'];
$n_date=$_POST['n_date'];
$have_word=$_POST['have_word'];
$today=explode("-",date("Y-m-d",mktime (date("m"),date("d"),date("Y"))));
$cond=study_cond();

$m_arr = get_sfs_module_set("stud_move");
extract($m_arr, EXTR_OVERWRITE);
$m_arr = get_sfs_module_set("");
extract($m_arr, EXTR_OVERWRITE);

if ($student_sn) {
	$sql="select stud_id,stud_name,stud_study_year,stud_study_cond,stud_name_eng from stud_base where student_sn='$student_sn'";
	$rs=$CONN->Execute($sql);
	$stud_id=$rs->fields['stud_id'];
	$stud_name=$rs->fields['stud_name'];
	if ($stud_name) {
		$stud_study_year=$rs->fields['stud_study_year'];
		$stud_study_cond=$rs->fields['stud_study_cond'];
		$stud_ename=$rs->fields['stud_name_eng'];
	}
}

switch ($kind) {
	case "國中歷年成績證明表(1)":
		$oo_path="1";
		include ("trans_main.php");
		break;
	case "國中歷年成績證明表(2)":
		$oo_path="2";
		include ("trans_main.php");
		break;
	case "國中英文成績證明表":
		$oo_path="3";
		include ("trans_main.php");
		break;
	case "各學期定考成績單";
		include ("stage.php");
		break;
	case "轉學證明書":
		include ("my_fun.php");
		if ($have_word) {
			$d=explode("-",$m_date);
			$m_content="經奉".$m_unit.num2str($d[0])."年".num2str($d[1])."月".num2str($d[2])."日".$m_word."字第".$m_num."號核准";
		} else {
			$d=explode("-",$n_date);
			$m_content="因該生係".num2str($d[0])."年".num2str($d[1])."月".num2str($d[2])."日入學尚未報奉核准";
		}
		$oo_path="move_out";
		include ("move_out.php");
		break;
}
 
//印出檔頭
head("列印成績證明");
print_menu($menu_p);

$check_1="";
$check_2="checked";

if ($student_sn) {
	//取得轉出異動資料紀錄
	$sql="select * from stud_move where (move_kind in (7,8,11,12)) and student_sn='$student_sn' ORDER BY move_id DESC";
	$rs=$CONN->Execute($sql);
	if ($rs->recordcount()>0) {
		$school_move_num=$rs->fields['move_year_seme'].sprintf('%03d',$rs->fields['school_move_num']);
		$reason=$rs->fields['reason'];
		if (!empty($m_num)) {
			$check_1="checked";
			$check_2="";
		}
	}

	//取得新生入學或轉入異動資料紀錄
	$sql="select * from stud_move where (move_kind in (2,13)) and student_sn='$student_sn'";
	$rs=$CONN->Execute($sql);
	if ($rs->recordcount()>0) {
		$m_unit=$rs->fields['move_c_unit'];
		$m_date=DtoCh($rs->fields['move_c_date']);
		$m_word=$rs->fields['move_c_word'];
		$m_num=$rs->fields['move_c_num'];
		$n_date=DtoCh($rs->fields['move_date']);
		if (!empty($m_num)) {
			$check_1="checked";
			$check_2="";
		}
	}
}

$main="
	<table cellspacing=0 cellpadding=0><tr><td>
	<table bgcolor='#9EBCDD' cellspacing=1 cellpadding=4><tr  class='title_sbody1'>
	<form name ='form' action='{$_SERVER['PHP_SELF']}' method='post' >
	<td class='title_sbody2'>學生學號<td colspan='3' align='left'><input type='text' size='10' name='stud_id' value='$stud_id'><input name='SUBMIT' type='submit' value='更換學生'></td></form></tr>";
if ($student_sn=="" && $stud_id) {
	$main.="<form name ='form1' action='{$_SERVER['PHP_SELF']}' method='post'><tr class=\"title_sbody2\"><td align=\"center\">學生列表</td><td style=\"text-align:left;background-color:white;\">";
	$query="select * from stud_base where stud_id='$stud_id' order by stud_study_year";
	$res=$CONN->Execute($query);
	while(!$res->EOF) {
		$main.="<input type=\"radio\" name=\"student_sn\" value=\"".$res->fields['student_sn']."\" OnClick=\"this.form.submit();\"><span style=\"color:red;\">".$res->fields['stud_name']."</span>(".$res->fields['stud_study_year']."年入學)(".$cond[$res->fields['stud_study_cond']].")<br>";
		$res->MoveNext();
	}
	$main.="</td></tr>";
} elseif ($student_sn && $stud_name) {
	$main.="
		<form name ='form1' action='{$_SERVER['PHP_SELF']}' method='post' >
		<tr class='title_sbody1'>
		<td class='title_sbody2'>學生姓名
		<td align='left' colspan='3'>$stud_name
		</tr>
		<tr class='title_sbody1'>
		<td class='title_sbody2'>入 學 年
		<td align='left' colspan='3'>".$stud_study_year."
		</tr>
		<tr  class='title_sbody1'>
		<td class='title_sbody2'>就學狀態
		<td align='left' colspan='3'>".$cond[$stud_study_cond]."
		</tr>
		<tr class='title_sbody1'>
		<td class='title_sbody2'>日常成績
		<td align='left' colspan='3'><input type='radio' name='nor_score' value='1'>有 <input type='radio' name='nor_score' value='0' checked>無
		</tr>
		<tr class='title_sbody1'>
		<td class='title_sbody2' rowspan='3'>下　　載".(($IS_JHORES)?"
		<td align='left' colspan='3'> <input type='submit' name='kind' value='國中歷年成績證明表(1)'> (轉學用，含文字描述)<input type='hidden' name='student_sn' value='$student_sn'><br>
		<input type='submit' name='kind' value='國中歷年成績證明表(2)'> (一般用，不含文字描述)<br>
		<input type='submit' name='kind' value='國中英文成績證明表'><input type='hidden' name='student_sn' value='$student_sn'></td>
		":"
		<td align='left' colspan='3'> <INPUT TYPE=button value='國小版成績證明書' onclick=\" window.open('/sfs3/modules/chc_page/index.php?st_sn=$student_sn');\"> (一般用，含文字描述)
		")."
		</tr>
		<tr class='title_sbody1'>
		<td align='left'> <input type='submit' name='kind' value='各學期定考成績單'></td>
		</tr>
		<tr class='title_sbody1'>
		<td align='left' colspan='3'> <input type='submit' name='kind' value='轉學證明書'>(請先輸入下列內容)
		<br>　　證書字　：<input type='text' name='c_word' value='".($default_cword?$default_cword:$school_sshort_name.'轉證')."'>字
		<br>　　證書號　：第<input type='text' name='c_num' value='$school_move_num' size='18'>號
		<br>　　轉學理由：<input type='text' name='m_reason' value='$reason'>
		<br><input type='radio' name='have_word' $check_1 value='1'>學籍已有核准文號
		<br>　　核准單位：<input type='text' name='m_unit' value='$m_unit'>
		<br>　　核准日期：民國 <input type='text' name='m_date' value='$m_date' size='16'>
		<br>　　核准字　：<input type='text' name='m_word' value='$m_word'>字
		<br>　　核准號　：第<input type='text' name='m_num' value='$m_num' size='18'>號
		<br><input type='radio' name='have_word' $check_2 value='0'>學籍尚無核准文號
		<br>　　入學日期：民國 <input type='text' name='n_date' value='$n_date' size='16'>
		<input type='hidden' name='student_sn' value='$student_sn'>
		</tr>";
}
$main.="</form>";
$main.="</table></td></tr></table>";

echo $main;
foot();
?>
