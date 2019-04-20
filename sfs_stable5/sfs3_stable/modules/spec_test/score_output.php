<?php
//$Id: score_output.php 8975 2016-09-14 08:26:39Z smallduh $
include "config.php";
include "../../include/sfs_case_score.php";

//認證
sfs_check();
$output_print=$_POST[output];
$output_csv=$_POST[csv];
$output=($output_print || $output_csv);

//秀出網頁布景標頭
if (!$output) {
	head("特殊測驗");
	print_menu($school_menu_p);
}

//主要內容
if ($_POST[year_seme]) {
	$ys=explode("_",$_POST[year_seme]);
	$sel_year=$ys[0];
	$sel_seme=$ys[1];
} else {
	if(empty($sel_year))$sel_year = curr_year(); //目前學年
	if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期
}

$id=$_REQUEST[id];
$class_id=$_REQUEST[class_id];
$sch_name=$_POST[sch_name];
$n_chk=($sch_name)?"checked":"";
$weight=$_POST[weight];
$w_chk=($weight)?"checked":"";
$sorting=$_POST[sorting];
$s_chk=($sorting)?"checked":"";
$average_one=$_POST[average_one];
$ao_chk=($average_one)?"checked":"";
$average_subject=$_POST[average_subject];
$as_chk=($average_subject)?"checked":"";
$std=$_POST[std];
$d_chk=($std)?"checked":"";
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
	$class_menu=class_menu($sel_year,$sel_seme,$c_year,$class_id);
}
$main="<table border=0 cellspacing=1 cellpadding=2 width=100% bgcolor=#cccccc><tr><td bgcolor='#FFFFFF'>\n";
$year_seme_menu=year_seme_menu($sel_year,$sel_seme);
$test_menu=test_menu($sel_year,$sel_seme,$id);
$main.="<form name=\"f1\" method=\"post\" action=\"$_SERVER[PHP_SELF]\">
	<table>
	<tr>
	<td>$year_seme_menu $test_menu $class_menu</td>
	</tr>
	<tr>
	<td><input type=checkbox name=sch_name $n_chk OnChange='this.form.submit();'>標題加校名
	<input type=checkbox name=weight $w_chk OnChange='this.form.submit();'>加權
	<input type=checkbox name=average_one $ao_chk OnChange='this.form.submit();'>列出個人平均
	<input type=checkbox name=sorting $s_chk OnChange='this.form.submit();'>列出名次
	<input type=checkbox name=average_subject $as_chk OnChange='this.form.submit();'>列出各科平均
	<input type=checkbox name=std $d_chk OnChange='this.form.submit();'>列出各科標準差</td>
	</tr>
	</table>\n";
if (!$output) echo $main;
if ($class_id) {
	$subject=explode("@@",$subject_str);
	$ratio=explode(":",$ratio_str);
	while(list($k,$v)=each($subject)) {
		$col_arr[$k][name]=$v;
		$col_arr[$k][ratio]=$ratio[$k];
	}
	$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
	$class=explode("_",$class_id);
	$seme_class=intval($class[2].$class[3]);
	$query="select a.student_sn,a.seme_num,b.stud_name from stud_seme a,stud_base b where a.seme_year_seme='$seme_year_seme' and a.student_sn=b.student_sn and b.stud_study_cond='0' and seme_class='$seme_class' order by a.seme_num";
	$res=$CONN->Execute($query);
	$all_sn="";
	while (!$res->EOF) {
		$site_num=$res->fields[seme_num];
		$sn=$res->fields[student_sn];
		$row_arr[$site_num][sn]=$sn;
		$row_arr[$site_num][name]=addslashes($res->fields[stud_name]);
		$all_sn.="'".$sn."',";
		$res->MoveNext();
	}
	if ($all_sn) $all_sn=substr($all_sn,0,-1);
	$query="select * from $score_spec where student_sn in ($all_sn) and id='$id'";
	$res=$CONN->Execute($query);
	while (!$res->EOF) {
		$score_str=$res->fields[score_str];
		$sn=$res->fields[student_sn];
		$score=explode("@@",$score_str);
		while(list($k,$v)=each($score)) {
			$score_arr[$sn][$k][0]["特定測驗"]=$v;
		}
		$res->MoveNext();
	}
	$s=get_school_base();
	$c=class_id_2_old($class_id);
	$sc = new score_chart();
	$sc->col_arr=$col_arr;
	$sc->row_arr=$row_arr;
	$sc->score_arr=$score_arr;
	if ($sch_name) $upper_title=$s[sch_cname]." ".$upper_title;
	$sc->upper_title=$upper_title."　《".$c[5]."》";
	$sc->sort=0;
	if ($weight) $sc->ratio_enable=true;
	$sc->kind=array("特定測驗");
	$sc->summary();
	if ($average_one) $sc->average_one();
	if ($sorting) $sc->sorting();
	if ($average_subject) $sc->average_subject();
	if ($std) $sc->std();
	if ($output) { 
		if ($output_print) $sc->output();
		if ($output_csv) $sc->file_out();
	} else {
		echo "<input type=submit name=output value='列印'> <input type=submit name=csv value='匯出CSV檔'>";
		$sc->view();
	}
}
if (!$output) {
	echo "</tr></table><input type=submit name=output value='列印'> <input type=submit name=csv value='匯出CSV檔'></form>";

	//佈景結尾
	foot();
}
?>
