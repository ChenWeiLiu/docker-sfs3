<?php
//$Id: supply.php 5310 2009-01-10 07:57:56Z hami $
include "config.php";

if(!$_POST['csv']) {
	head("代課列表");
	$tool_bar=make_menu($school_menu_p);
	echo $tool_bar;
}

$abs_kind_arr=tea_abs_kind();
// 判斷是否為管理權限
$isAdmin = (int)checkid($_SERVER[SCRIPT_FILENAME],1);

//選擇學期
$year_seme_menu=year_seme_menu($sel_year,$sel_seme);
//選擇教師
$leave_teacher_menu=teacher_menu("teacher_sn",$_POST[teacher_sn]); 
//選擇假別
$abs_kind=tea_abs($_POST[abs_kind],$abs_kind_arr); 
//選擇月份
$month=month_menu($_POST[month],$month_arr); 
//選擇教師
$leave_deputy_menu=teacher_menu("deputy_sn",$_POST[deputy_sn]); 
//選擇代課方式
$d_class_dis_menu=d_make_menu("選擇方式",$_POST[class_dis] , $c_course_kind,"class_dis",1);

//選擇是否確定
$d_check4_menu=d_make_menu("是否確定",$_POST[d_check4] , $check_arr,"d_check4",1); 

//條件
$sel_year=intval($sel_year);
$sel_seme=intval($sel_seme);
$query1.=" and year='$sel_year' and semester='$sel_seme' ";

if ($_POST[teacher_sn]) {
$_POST[teacher_sn]=intval($_POST[teacher_sn]);
$query1 .=" and b.teacher_sn='$_POST[teacher_sn]'";
$abs_name=get_teacher_name($_POST[teacher_sn])."請假　";
}
if ($_POST[deputy_sn]) {
$_POST[deputy_sn]=intval($_POST[deputy_sn]);
$query1 .=" and b.deputy_sn='$_POST[deputy_sn]'";
$dep_name=get_teacher_name($_POST[deputy_sn])."代課　" ;
}

if ($_POST[class_dis]) {
$_POST[class_dis]=intval($_POST[class_dis]);
$query1 .=" and b.class_dis='$_POST[class_dis]'";
}

if ($_POST[d_check4]=='1') {
//	$query1 .=" and b.status='1'";
	$query1 .=" and a.check4_sn > '0' ";

}else{
//	$query1 .=" and b.status='0'";
	$query1 .=" and a.check4_sn='0'";

}

if ($_POST[abs_kind]) {
$_POST[abs_kind]=intval($_POST[abs_kind]);
$query1 .=" and abs_kind='$_POST[abs_kind]'";
}
if ($_POST[month] ) {
$_POST[month]=intval($_POST[month]);
$query1 .=" and month='$_POST[month]'";
}


$html_data.="<table width=100% border=0 cellspacing=1 cellpadding=4 ><form name='menu_form' method='post' action='{$_SERVER['PHP_SELF']}'>
<tr><td> $year_seme_menu 請假人:$leave_teacher_menu 代理人:$leave_deputy_menu $abs_kind $month $d_class_dis_menu  $d_check4_menu</td>
</tr>";

$abs_kind=$abs_kind_arr[$_POST[abs_kind]];
$abs_month= $month_arr[$_POST[month]];
$n_class_dis=$course_kind[$_POST[class_dis]];

$filename="{$sel_year}學年度第{$sel_seme}學期 $abs_name $dep_name $abs_kind $abs_month $n_class_dis".'.csv';
if ($isAdmin)
	$html_data.="<tr bgcolor=#cccccc><td> $sel_year  學年度第 $sel_seme 學期  (全校)";
else 
	$html_data.="<tr bgcolor=#cccccc><td> $sel_year  學年度第 $sel_seme 學期  (本處室)";

$html_data.="$abs_name  $dep_name 　$abs_kind $abs_month $n_class_dis <input type='submit' name='csv' value='CSV輸出'</td></tr>";

$csv_data="序號,請假人,假別,事由,課務,代課日期,結束日期或節次,科目班級,代理人,數量,單位\r\n";
$html_data.="<tr><td><table border='1' cellPadding='3' cellSpacing='0' class='main_body' width=100%>
	<tr bgcolor=#E1ECFF align=center>
	<td  align='center'width=5%> 序號</td>
	<td  align='center'width=8%> 請假人</td>
	<td  align='center' width=5%>假別</td>
	<td  align='center'width=15%> 事由</td>
	<td  align='center'width=7%> 課務</td>
	<td  align='center'width=13%>代課日期</td>
	<td align='center'width=12%>結束日期或節次</td>
	<td align='center'width=10%>科目班級</td>
	<td align='center'width=15%>代理人</td>
	<td align='center'width=5%>數量</td>
	<td align='center'width=5%>單位</td></tr>";

//讀取資料
if ($isAdmin)
$sql_select = "select * from teacher_absent a , teacher_absent_course b where a.id=b.a_id and travel='0' ";
else { 
	$query = "SELECT * FROM teacher_post WHERE teacher_sn={$_SESSION['session_tea_sn']}";
	$res=$CONN->Execute($query);
	$user_post_office = $res->fields['post_office'];
	
	$sql_select = "select a.* from teacher_absent a , teacher_absent_course b , teacher_post c where
			a.teacher_sn=c.teacher_sn and c.post_office=$user_post_office and 
			a.id=b.a_id and travel='0'   ";
}

$sql_select.=$query1;
$sql_select.=" order by b.start_date desc ,b.deputy_sn,b.end_date";

$result = $CONN->Execute ($sql_select) or die($sql_select) ;
$i=0;
while (!$result->EOF) {
	$a_id = $result->fields["id"];
	$teacher_sn = $result->fields["teacher_sn"];		
	$reason=$result->fields["reason"];
	$c_id = $result->fields["c_id"];
	$d_kind = $result->fields["d_kind"];

	$abs_kind_arr=tea_abs_kind();
	$abs_kind=$abs_kind_arr[$result->fields["abs_kind"]];

	$start_date = $result->fields["start_date"];
	$end_date = $result->fields["end_date"];
	$class_name = $result->fields["class_name"];
	$deputy_sn = $result->fields["deputy_sn"];
	$times = $result->fields["times"];
	$status = $result->fields["status"];

	$class_dis=$result->fields["class_dis"];
	$d_name=get_teacher_name($deputy_sn);
	$t_name=get_teacher_name($teacher_sn);

	$n_class_dis=$course_kind["$class_dis"];
	$c_a_id="<a href=class.php?id=$a_id title=課務處理> $a_id </a>";
	$ti = ($i++%2)+1;
	$check=($status=="0") ?
	"<font size=2 color=red>待確定</font>":"";

	$csv_data.="$a_id,$t_name,$abs_kind,$reason,$n_class_dis,$start_date,$end_date,$class_name,$d_name,$times,$times_kind_arr[$d_kind]\r\n";
	$html_data.="
	<tr bgcolor=#ddddff align=center OnMouseOver=sbar(this) OnMouseOut=cbar(this)>
	<td align='center'>$c_a_id</td>
	<td align='center'><font size=3>$t_name</font></td>
	<td align='center'>$abs_kind</td> 
	<td align='center'>$reason</td>
	<td align='center'>$n_class_dis</td>

	<td align='center'><font size=3>$start_date</font></td>
	<td align='center'><font size=3>$end_date</font></td>		
	<td align='center'>$class_name</td>
	<td align='center'><font size=3>$d_name</font> $check</td>
	<td align='center'><font size=3>$times</font></td>
	<td align='center'>$times_kind_arr[$d_kind]</td></tr>
	";
	$result->MoveNext();
}
$html_data.="</table></td></tr></table>";

if($_POST['csv']) {
	header("Content-disposition: attachment; filename=$filename");
	header("Content-type: text/x-csv");
	//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
	header("Expires: 0");
	echo $csv_data;
	exit;
} else echo $html_data;
foot();
?>
<script language="JavaScript1.2">

<!-- Begin

function sbar(st){st.style.backgroundColor="#F3F3F3";}

function cbar(st){st.style.backgroundColor="";}

//  End -->



</script>

