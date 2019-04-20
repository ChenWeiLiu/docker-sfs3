<?php

// $Id: view_grad_score.php 5310 2009-01-10 07:57:56Z hami $

/*引入學務系統設定檔*/
require "config.php";

if($_GET['grad_year']) $grad_year=$_GET['grad_year'];
else $grad_year=$_POST['grad_year'];
if($_GET['Hgrad_year']) $Hgrad_year=$_GET['Hgrad_year'];
else $Hgrad_year=$_POST['Hgrad_year'];
if($_GET['Hclass_year']) $Hclass_year=$_GET['Hclass_year'];
else $Hclass_year=$_POST['Hclass_year'];
if($_GET['Hclass_sort']) $Hclass_sort=$_GET['Hclass_sort'];
else $Hclass_sort=$_POST['Hclass_sort'];

//使用者認證
sfs_check();
//程式檔頭
head("畢業生作業");

print_menu($menu_p);
//設定主網頁顯示區的背景顏色
echo "
<table border=0 cellspacing=1 cellpadding=2 width=100% bgcolor=#cccccc>
<tr>
<td bgcolor='#FFFFFF'>";

//網頁內容請置於此處
if(empty($sel_year))$sel_year = curr_year(); //目前學年
if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期
$new_sel_year=date("Y")-1911;//目前民國年

$seme_array=get_grad_year();
if(count($seme_array)==0) { echo "</table>";  trigger_error("目前沒有任何畢業成績！<br>".$sql_ss,E_USER_ERROR);}
reset ($seme_array);

if($grad_year){
	$sql_class="select  class_year,class_sort  from grad_stud where stud_grad_year='$grad_year'";
	$rs_class=$CONN->Execute($sql_class);	
	$i=0;
	while(!$rs_class->EOF){		
		$class_year[$i]=$rs_class->fields['class_year'];
		$class_sort[$i]=$rs_class->fields['class_sort'];
		$year_sort[$i]=$class_year[$i]."_".$class_sort[$i];	
		$rs_class->MoveNext();
		$i++;
	}
	$clear_year_sort=deldup($year_sort);
	for($j=0;$j<count($clear_year_sort);$j++){
		$clear_year_sort_A[$j]=explode("_",$clear_year_sort[$j]);
		$B[$j]=$clear_year_sort_A[$j][0]."年".$clear_year_sort_A[$j][1]."班";
		if($Hclass_year==$clear_year_sort_A[$j][0] && $Hclass_sort==$clear_year_sort_A[$j][1]) $this_class[$j]="bgcolor='#EEE726'";
		else $this_class[$j]="bgcolor='#FFFFFF'";
		$class_menu.="<table bgcolor='#FFFFFF'><tr><td $this_class[$j]><a href='{$_SERVER['PHP_SELF']}?grad_year=$grad_year&Hclass_year={$clear_year_sort_A[$j][0]}&Hclass_sort={$clear_year_sort_A[$j][1]}'>$B[$j]</a></td></tr></table>";
	}	
}
	
if($grad_year && $Hclass_year && $Hclass_sort){
	//組成class_id再來顯示出中文的班級名稱
	$class_id=sprintf("%03d_%d_%02d_%02d",$grad_year,2,$Hclass_year,$Hclass_sort);
	//echo $class_id;
	$rs_class_id=$CONN->Execute("select c_year,c_name from school_class where class_id='$class_id'");
	$year_sort_cname=$rs_class_id->fields['c_year']."年".$rs_class_id->fields['c_name']."班";
	$sql_score="select  * from grad_stud where stud_grad_year='$grad_year' and class_year='$Hclass_year' and class_sort='$Hclass_sort'";
	//echo $sql_score."<br>";	
	$rs_score=$CONN->Execute($sql_score);
	$i=0;
	while(!$rs_score->EOF){
		$stud_id[$i]=$rs_score->fields['stud_id'];
		//$stud_nam[$i]=stud_name($stud_id[$i]);
		$class_year[$i]=$rs_score->fields['class_year'];
		$class_sort[$i]=$rs_score->fields['class_sort'];
		$grad_score[$i]=$rs_score->fields['grad_score'];
		//if($grad_score[$i]!="") $grad_score[$i]=number_format($grad_score[$i],2);
		//$bgcolor1=($i%2==0)?"#F4A4FF":"#D08CD9";
		//$one_stud_score.="<tr bgcolor='$bgcolor1'><td>$year_sort_cname</td><td>$stud_nam[$i]</td><td>$grad_score[$i]</td><td>名次</td></tr>";
		$rs_score->MoveNext();
		$i++;
	}
	for($k=0;$k<count($stud_id);$k++){
		$stud_nam[$k]=stud_name($stud_id[$k]);	
		$score_sort[$k]=how_big($grad_score[$k],$grad_score);
		if($grad_score[$k]!="") $grad_score[$k]=number_format($grad_score[$k],2);
		$bgcolor1=($k%2==0)?"#F4A4FF":"#D08CD9";
		$one_stud_score.="<tr bgcolor='$bgcolor1'><td>$year_sort_cname</td><td>$stud_nam[$k]</td><td>$grad_score[$k]</td><td>$score_sort[$k]</td></tr>";
	}
$main_score="<table bgcolor='#D84CEA' border='0' cellspacing='1' cellpadding='2'><tr><td>班級</td><td>姓名</td><td>成績</td><td>名次</td></tr>".$one_stud_score."</table>";
}

$seme_menu.="<table bgcolor='#D84CEA' border='0' cellspacing='1' cellpadding='2'>";
$i=0;
foreach ($seme_array as $value) {	
	$value_name=$value."學年度";
	$bgcolor1=($i%2==0)?"#F4A4FF":"#D08CD9"; 
	$seme_menu.="<tr bgcolor='$bgcolor1' align='right'><td><a href='{$_SERVER['PHP_SELF']}?grad_year=$value'>$value_name</a>";
	if($value==$grad_year) $seme_menu.=$class_menu;
	$seme_menu.="</td></tr>";
	$i++;
}
$seme_menu.="</table>";

echo "<table width='90%' bgcolor='#9F14B1' border='0' cellspacing='1' cellpadding='2'><tr bgcolor='#FACDEF'><td width='80%' valign='top'>$main_score</td><td width='20%' valign='top'>$seme_menu</td></tr></table>";



//結束主網頁顯示區
echo "</td>";
echo "</tr>";
echo "</table>";

//程式檔尾
foot();
?>
