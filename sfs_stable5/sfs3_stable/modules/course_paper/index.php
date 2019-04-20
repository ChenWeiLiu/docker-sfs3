<?php
// $Id: index.php 6991 2012-11-01 12:16:47Z infodaes $
/* 取得基本設定檔 */
include "config.php";

sfs_check();
$teacher_sn=$_SESSION['session_tea_sn'];
$choice=$_POST['choice'];

//若有選擇學年學期，進行分割取得學年及學期
$year_seme=$_POST['year_seme'];
if(!empty($year_seme)){
	$ys=explode("-",$year_seme);
	$sel_year=$ys[0];
	$sel_seme=$ys[1];
}
if(empty($sel_year))$sel_year = curr_year(); //目前學年
if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期


//執行匯出的項目
switch ($choice) {
    case 0:
        break;
    case 1:
        Header("Location: list_all_teacher.php?sel_year=$sel_year&sel_seme=$sel_seme");
        break;
    case 2:
        Header("Location: list_all_room.php?sel_year=$sel_year&sel_seme=$sel_seme");
		break;
    case 3:
        Header("Location: list_class_sum.php?sel_year=$sel_year&sel_seme=$sel_seme");
        break;
    case 4:
        Header("Location: list_teach_sum.php?sel_year=$sel_year&sel_seme=$sel_seme");
        break;
    case 5:
        Header("Location: list_class_assign.php?sel_year=$sel_year&sel_seme=$sel_seme");
        break;
    case 6:
        Header("Location: list_chk_class.php?sel_year=$sel_year&sel_seme=$sel_seme");
        break;
	case 7:
        Header("Location: list_teach_sum_csv.php?sel_year=$sel_year&sel_seme=$sel_seme");
        break;
	case 8:
		Header("Location: csv_class_all.php?sel_year=$sel_year&sel_seme=$sel_seme");
		break;
}


//秀出網頁
head("班級課表查詢");
$tool_bar=&make_menu($school_menu_p);
echo $tool_bar ;
//取得年度與學期的下拉選單
$date_select=class_ok_setup_year($sel_year,$sel_seme,"year_seme");

echo "<table cellspacing='1' cellpadding='4'  bgcolor=#9EBCDD><form name='myform' method='post' action='$_SERVER[PHP_SELF]'>
	<tr bgcolor='#F7F7F7'>
	<td>匯出成openoffice 文件檔案</td></tr>
	<tr><td>選擇學期：$date_select</td></tr>
	<tr bgcolor='#F7F7F7'>
	<td>
	<input type='radio' value='1' name='choice'>教師個別課表匯出<br>
	<input type='radio' value='2' name='choice'>專科教室課表匯出<br>
	<input type='radio' value='3' name='choice'>班級總表匯出<br>
	<input type='radio' value='4' name='choice'>教師總表匯出<br>
	<input type='radio' value='5' name='choice'>班級配課表匯出(.CSV)<br>
	<input type='radio' value='7' name='choice'>教師總表匯出(.CSV)<br>
	<input type='radio' value='8' name='choice'>班級功課表套印CSV匯出<br>
	</td>
	</tr>
	<tr>
	<td align='center'>
	<input type='submit' name='go' value='執行匯出'><br>
	</td>
	</tr>		
	</table></form>" ;
	
foot();


?>
