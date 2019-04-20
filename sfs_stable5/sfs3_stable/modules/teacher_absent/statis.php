<?php
//$Id: supply.php 5310 2009-01-10 07:57:56Z hami $
include "config.php";
head("差假統計");
$tool_bar=make_menu($school_menu_p);
echo $tool_bar;

//選擇學期
$year_seme_menu=year_seme_menu($sel_year,$sel_seme);

//選擇是否全學年
$check_arr=array("1"=>"全學年");
$d_check_menu=d_make_menu("選擇範圍",$_POST[d_check] , $check_arr,"d_check",1); 
//選擇月份
$month=month_menu($_POST[month],$month_arr); 

//條件
//$query1=" year='$sel_year' and semester='$sel_seme' ";

if ($_POST[d_check]==1) {
	$query1=" year='$sel_year'";
	$sel="全學年";
}else{
	$query1=" year='$sel_year' and semester='$sel_seme' ";
	$sel="第 ". $sel_seme ." 學期";

}

if ($_POST[month] ) {
$_POST[month]=intval($_POST[month]);
$query1 .=" and month='$_POST[month]'";
}



echo "<table width=100% border=0 cellspacing=1 cellpadding=4 ><form name='menu_form' method='post' action='{$_SERVER['PHP_SELF']}'>
<tr><td> $year_seme_menu $d_check_menu $month</td>
</tr>";
//取得教師陣列
$tea_name_arr=my_teacher_array();

//取得假別陣列 
$abs_kind_arr=tea_abs_kind();


$a=count($abs_kind_arr);

$abs_month= $month_arr[$_POST[month]];

echo "<tr bgcolor=#cccccc><td> $sel_year  學年度 $sel   $abs_month  </td></tr>";

$main="<tr><tr><table border=0 cellspacing=1 cellpadding=4 width=100% bgcolor=#cccccc class='main_body' >
	<tr bgcolor=#E1ECFF align=center><td>姓名</td>";
$i=0;
while (list($key, $val) = each($abs_kind_arr) ){
	$i++;
	$abs[$i]=$key;	
	$main.="<td> $val </td>";
}
$main.="</tr>";
echo $main;

//讀取資料
$sql_select="select * from teacher_absent where check4_sn>0 and teacher_sn=$_SESSION[session_tea_sn] and " .$query1 ;
//$sql_select .=" order by start_date  desc ";
$result = $CONN->Execute ($sql_select) or die($sql_select) ;

while (!$result->EOF) {
		$abs_kind=$result->fields["abs_kind"];
		$s_day[$abs_kind]+=$result->fields["day"];
		$s_hour[$abs_kind]+=$result->fields["hour"];
		$result->MoveNext();
}

	$t_name=$tea_name_arr[$_SESSION[session_tea_sn]] ;
	$main="<tr bgcolor=#ddddff align=center OnMouseOver=sbar(this) OnMouseOut=cbar(this)><td > $t_name </td>";
	for ($i = 1; $i <= $a; $i++) {
		$m_day=$s_day[$abs[$i]]+intval($s_hour[$abs[$i]]/8);
		$m_hour=($s_hour[$abs[$i]] % 8);
		$day_s=($m_day==0)?"":$m_day ."日";
		$hour_s=($m_hour==0)?"":$m_hour ."時";
		
   	 	$main.="<td >$day_s$hour_s</td>";
	
	}
	$main.="</tr>";
	echo $main;


echo "</table></td></tr></form></table>";
foot();
?>

<script language="JavaScript1.2">

<!-- Begin

function sbar(st){st.style.backgroundColor="#F3F3F3";}

function cbar(st){st.style.backgroundColor="";}

//  End -->

</script>

