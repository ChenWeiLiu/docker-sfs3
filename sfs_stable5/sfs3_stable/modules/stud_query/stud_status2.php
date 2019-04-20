<?php

// $Id: stud_status2.php 8758 2016-01-13 12:49:42Z qfon $

include "stud_query_config.php";
//目前班級
$curr_class = class_base();


if(empty($year))$year=curr_year();
if(empty($semester))$semester=curr_seme();

//目前學年度、學期
$main=list_class_stu($year,$semester);



head("班級學生人數統計名冊");
echo $main  ;
foot();

//列出所有班級的資料
function list_class_stu($year,$semester){
	global $menu_p,$CONN ,$school_kind_name;
	$toolbar=&make_menu($menu_p);
	$year=intval($year);
	$semester=intval($semester);
	$sql_select = "select class_sn,class_id,c_year,c_name,c_kind,c_sort from school_class where enable='1' and year='$year' and semester='$semester' order by c_year,c_sort";
	$recordSet=$CONN->Execute($sql_select) or die($sql_select);

	while ($array = $recordSet->FetchRow()) {
		$c=$array[c_year].sprintf("%02d",$array[c_sort]);
		$sql_select2="select sum(stud_sex=1)as boy ,sum(stud_sex=2) as girl from stud_base where stud_study_cond in (0,15) and substring(curr_class_num,1,3)='$c'";
		$recordSet2=$CONN->Execute($sql_select2) or die($sql_select2);
		$sarray = $recordSet2->FetchRow();
		$Cyear=$array[c_year];
		$cclass[$array[c_sort]]=$array[c_name];

		$stud_all=(($sarray[boy]+$sarray[girl])>0)?$sarray[boy]+$sarray[girl]." 人":"";
		$b=(!empty($sarray[boy]))?"$sarray[boy] 人":"";
		$g=(!empty($sarray[girl]))?"$sarray[girl] 人":"";
                
		if ($stud_all >0) {
			$data.="<tr bgcolor='#FFFFFF'>
			<td align='center'>".$school_kind_name[$Cyear].$array[c_name]."班</td>
			<td align='center'>$b</td>
			<td align='center'>$g</td>
			<td align='center'>$stud_all</td>
			</tr>";

			$class_n++;
		
			$all_b+=$sarray[boy];
			$all_g+=$sarray[girl];
			$all_n=$all_b+$all_g;

			$class_data[$Cyear]++;
			$cmb[$Cyear]+=$sarray[boy];
			$cmg[$Cyear]+=$sarray[girl];
			$cmall[$Cyear]+=$all_n;
		}
	}
	
	if(empty($class_data)) return "目前無任何班級資料。";
	
	while(list($k,$v)=each($class_data)){
		$call=$cmb[$k]+$cmg[$k];
		if ($call>0) {
		$aa.="<tr bgcolor='#FFFFFF'>
		<td align='center'>$school_kind_name[$k]</td>
		<td align='center'>$v</td>
		<td align='center'>$cmb[$k]</td>
		<td align='center'>$cmg[$k]</td>
		<td align='center'>$call</td></tr>";
		}
	}

	$query = "SELECT COUNT(*) AS cc ,stud_study_cond FROM stud_base WHERE stud_study_cond =15 GROUP BY stud_study_cond";
	$res = $CONN->Execute($query) or die($query);
	$tol_cond = '';
	if ($res->fields['cc']>0) {
		$tol_cond = "<br><font size=2>含在家教育人數 : ".$res->fields['cc']. " 人";
		$sex_arr=array("1"=>"男","2"=>"女");
		$query="select * from stud_base where stud_study_cond='15' order by curr_class_num";
		$res=$CONN->Execute($query);
		while(!$res->EOF) {
			$curr_class_num=$res->fields[curr_class_num];
			$tol_cond.="<br>".$school_kind_name[substr($curr_class_num,0,-4)].$cclass[intval(substr($curr_class_num,-4,2))]."班".substr($curr_class_num,-2,2)."號 (".$sex_arr[$res->fields[stud_sex]].") ".$res->fields[stud_name];
			$res->MoveNext();
		}
		$tol_cond.="</font>";
	}
	$main="
	$toolbar
	<table cellspacing=0 cellpadding=0>
	<tr><td valign='top'>
		<table bgcolor='#9EBCDD' cellspacing=1 cellpadding=4>
		<tr bgcolor='#E1ECFF'><td>年級</td><td>男學生</td><td>女學生</td><td>學生合計</td></tr>
		$data
		</table>
	</td><td>&nbsp;</td><td valign='top'>
		<table bgcolor='#9EBCDD' cellspacing=1 cellpadding=4>
		<tr bgcolor='#E1ECFF'><td>年級</td><td>總班級數</td><td>男學生</td><td>女學生</td><td>學生合計</td></tr>
		$aa
		<tr bgcolor='#FFFFFF'>
		<td align='center'>合計</td>
		<td align='center'>$class_n</td>
		<td align='center'>$all_b</td>
		<td align='center'>$all_g</td>
		<td align='center'>$all_n</td></tr>
		</table>
		$tol_cond
	</td></tr>
	</table>
	
	";
	return $main;
}
?>
