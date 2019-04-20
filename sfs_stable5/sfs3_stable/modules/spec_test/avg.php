<?php
//$Id: avg.php 7710 2013-10-23 12:40:27Z smallduh $
include "config.php";

//認證
sfs_check();

$csv=$_POST[csv];
$id=$_REQUEST[id];
$kind=$_POST[kind];

//秀出網頁布景標頭
if (!$csv) {
	head("特殊測驗");
	print_menu($school_menu_p);
}

//主要內容
if ($_REQUEST[year_seme]) {
	$ys=explode("_",$_REQUEST[year_seme]);
	$sel_year=$ys[0];
	$sel_seme=$ys[1];
} else {
	if(empty($sel_year))$sel_year = curr_year(); //目前學年
	if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期
}
$score_spec="score_spec_".$sel_year."_".$sel_seme;

if ($id) {
	$query="select * from test_manage where id='$id'";
	$res=$CONN->Execute($query);
	$sel_year=$res->fields[year];
	$sel_seme=$res->fields[semester];
	$c_year=$res->fields[c_year];
	$upper_title=$res->fields[title];
	$subject_str=$res->fields[subject_str];
	$ratio_str=$res->fields[ratio_str];

	$query="select * from school_class where year='$sel_year' and semester='$sel_seme' and c_year='$c_year' order by c_sort";
	$res=$CONN->Execute($query);
	while(!$res->EOF) {
		$seme_class=$res->fields[c_year].sprintf("%02d",$res->fields[c_sort]);
		$class_names[$seme_class]=$class_year[$res->fields[c_year]].$res->fields[c_name]."班";
		$res->MoveNext();
	}
}
$main="<table border=0 cellspacing=1 cellpadding=2 width=100% bgcolor=#cccccc><tr><td bgcolor='#FFFFFF'>\n";
$year_seme_menu=year_seme_menu($sel_year,$sel_seme);
$test_menu=test_menu($sel_year,$sel_seme,$id);
$main.="<form name=\"f1\" method=\"post\" action=\"$_SERVER[PHP_SELF]\">
	<table>
	<tr>
	<td>$year_seme_menu $test_menu $kind_menu $num_input<td><input type=submit name=csv value='匯出CSV'></td>
	</tr>
	</table>\n";
if (!$csv) echo $main;
if ($id) {
	$subject=explode("@@",$subject_str);
	$ratio=explode(":",$ratio_str);
	while(list($k,$v)=each($subject)) {
		$col_arr[$k][name]=$v;
		$col_arr[$k][ratio]=$ratio[$k];
		$subj_str.="<td>".$v;
		$subj_csv.=$v.",";
	}
	$main2="<table bgcolor='#0000ff' border='0' cellpadding='6' cellspacing='1'>
		<tr bgcolor='#FDC3F5'><td>班級".$subj_str."<td>加權總平均</td></tr>";
	$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
	while(list($seme_class,$x)=each($class_names)) {
		$query="select a.student_sn,a.seme_class,a.seme_num,b.stud_id,b.stud_name from stud_seme a,stud_base b where a.seme_year_seme='$seme_year_seme' and a.student_sn=b.student_sn and b.stud_study_cond='0' and a.seme_class like '$seme_class%' order by a.seme_class,a.seme_num";
		$res=$CONN->Execute($query);
		$all_sn="";
		while (!$res->EOF) {
			$sn=$res->fields[student_sn];
			$row_arr[$sn][site_num]=$res->fields[seme_class]."_".$res->fields[seme_num];
			$row_arr[$sn][name]=addslashes($res->fields[stud_name]);
			$row_arr[$sn][id]=$res->fields[stud_id];
			$all_sn.="'".$sn."',";
			$res->MoveNext();
		}
		if ($all_sn) $all_sn=substr($all_sn,0,-1);
		$sum=array();
		$query="select * from $score_spec where student_sn in ($all_sn) and id='$id'";
		$res=$CONN->Execute($query);
		while (!$res->EOF) {
			$score_str=$res->fields[score_str];
			$sn=$res->fields[student_sn];
			$score=explode("@@",$score_str);
			while(list($k,$v)=each($score)) {
				$score_arr[$seme_class][$k][score]+=$v;
				$score_arr[$seme_class][$k][num]++;
			}
			$res->MoveNext();
		}
		$score_td="";
		$score_csv="";
		$i=1;
		while(list($k,$v)=each($score_arr[$seme_class])) {
			$sum[$seme_class][score]+=$score_arr[$seme_class][$k][score]/$score_arr[$seme_class][$k][num]*$col_arr[$k][ratio];
			$sum[$seme_class][ratio]+=$col_arr[$k][ratio];
			$score_td.="<td>".number_format($score_arr[$seme_class][$k][score]/$score_arr[$seme_class][$k][num],2);
			$score_csv.=number_format($score_arr[$seme_class][$k][score]/$score_arr[$seme_class][$k][num],2).",";
		}
		$main2.="<tr bgcolor='#ffffff'><td>".$class_names[$seme_class].$score_td."<td bgcolor='#B4BED3'>".number_format($sum[$seme_class][score]/$sum[$seme_class][ratio],2)."</tr>";
		$main_csv.=$class_names[$seme_class].",".$score_csv.number_format($sum[$seme_class][score]/$sum[$seme_class][ratio],2)."\n\r";
	}
	$main2.="</table>";
}
if ($csv) {
	$filename="avg.CSV";
	header("Content-disposition: filename=$filename");
	header("Content-type: application/octetstream ; Charset=Big5");
	//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
	header("Expires: 0");
	echo "班級,".$subj_csv."加權總平均\n\r";
	echo $main_csv;
} else
	echo $main2."</tr></table></form>";

//佈景結尾
if (!$csv) foot();
?>