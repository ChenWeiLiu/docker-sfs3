<?php
// $Id: set_id.php 5310 2009-01-10 07:57:56Z hami $

/*引入學務系統設定檔*/
require "config.php";

$class_year_b=$_REQUEST['class_year_b'];
$act=$_REQUEST['act'];
$pre=$_REQUEST['pre'];
$much=$_REQUEST['much'];
$sort_g=$_REQUEST['sort_g'];
$sort_s=$_REQUEST['sort_s'];
//使用者認證
sfs_check();

//程式檔頭
head("新生編班");
print_menu($menu_p,"class_year_b=$class_year_b");

$year=date("Y")-1911;
$year_num=$year;
if ($IS_JHORES!=0) $year_num%=10;

$main.= "
<table border=0 cellspacing=1 cellpadding=2 width=100% bgcolor=#cccccc>
<tr>
<td bgcolor='#FFFFFF'>";
//網頁內容請置於此處
if($act=="send"){
	//先拆解跳過學號
	$skip_id=explode(";",$_POST[skip_id]);
	//開始排學號
	$sql="select * from new_stud where stud_study_year='$year' and sure_study<>'0' order by $sort_s $sort_g";
	$rs=$CONN->Execute($sql) or trigger_error($sql,256);
	$i=1;
	while(!$rs->EOF){
		$stud_id=$pre.sprintf ("%0".$much."d", $i);
		if (in_array($stud_id,$skip_id)) {
			$main.="<font color='red'>".$stud_id."--->跳過</font><br>";
		} else {
			$stud_name=addslashes($rs->fields['stud_name']);
			$newstud_sn=$rs->fields['newstud_sn'];
			$main.=$stud_id."--->".$stud_name."<br>";
			//寫入學號
			$sql_upd="update new_stud set stud_id='$stud_id' where newstud_sn='$newstud_sn' ";
			$CONN->Execute($sql_upd) or trigger_error($sql_upd,256);
			$rs->MoveNext();
		}
		$i++;
	}
}else{
	$main.="
	設定學號原則：<br>
	<form action='{$_SERVER['PHP_SELF']}' method='POST'>
	開頭數字為<input type='text' name='pre' value='$year_num' size='2'>+<input type='text' name='much' size='2' maxlength='2' value='4'>位數字<br><br>
	學號排序依據：<br>
	<table cellspacing=5 cellpadding=0><tr><td valign='top'>
	<table bgcolor='#9EBCDD' cellspacing=1 cellpadding=4>
	<tr bgcolor='#E1ECFF' valign='top'><td>
	<input type='radio' name='sort_g' value='class_sort,class_site' checked>班級座號<br>
	<input type='radio' name='sort_g' value='stud_name'>姓名<br>
	<input type='radio' name='sort_g' value='stud_address'>住址<br>
	<input type='radio' name='sort_g' value='stud_birthday'>生日<br>
	<input type='radio' name='sort_g' value='stud_person_id'>身分證字號<br>
	</td><td>
	<input type='radio' name='sort_s' value='' checked>不管性別<br>
	<input type='radio' name='sort_s' value='stud_sex desc,'>先設定完所有女生再設定男生<br>
	<input type='radio' name='sort_s' value='stud_sex,'>先設定完所有男生再設定女生<br>
	</td></tr></table>
	</td></tr></table><br>
	跳過不排學號：<br>
	<table cellspacing=5 cellpadding=0><tr><td valign='top'>
	<table bgcolor='#9EBCDD' cellspacing=1 cellpadding=4>
	<tr bgcolor='#E1ECFF' valign='top'><td>
	<textarea name='skip_id' cols='30' nowrap></textarea><br>
	</td><td width='300'>
	<ol>
	<li>如果有不雅學號，可以在此設定，排學號時會自動跳過。</li>
	<li>輸入學號時請用小寫分號(;)分隔，而且不留空白，不按換行鍵，一直輸入到完畢為止。</li>
	</ol>
	</td></tr></table>
	</td></tr></table>
	<input type='hidden' name='act' value='send'>
	<input type='hidden' name='class_year_b' value='$class_year_b'>
	<input type='submit' value='確定'>
	</form>";
}
//結束主網頁顯示區
$main.= "</td></tr></table>";

echo stripslashes($main);
//程式檔尾
foot();
?>