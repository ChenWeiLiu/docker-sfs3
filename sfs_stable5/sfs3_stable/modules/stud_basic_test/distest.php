<?php

// $Id: distest.php 5840 2010-02-03 14:34:10Z brucelyc $

// --系統設定檔
include "select_data_config.php";
include "../../include/sfs_case_score.php";

//認證
sfs_check();

$ss_link=array(1=>"chinese",2=>"english",3=>"math",4=>"nature",5=>"social",6=>"health",7=>"art",8=>"complex");

if (empty($_POST[year_seme])) {
	$sel_year = curr_year(); //目前學年
	$sel_seme = curr_seme(); //目前學期
	$year_seme=$sel_year."_".$sel_seme;
} else {
	$ys=explode("_",$_POST[year_seme]);
	$sel_year=$ys[0];
	$sel_seme=$ys[1];
}
$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
$y_arr=array(1=>"一上",2=>"一下",3=>"二上",4=>"二下",5=>"三上");
$s_arr=array(1=>"國",2=>"英",3=>"數",4=>"自",5=>"社",6=>"健",7=>"藝",8=>"綜");
//判斷電話
$phone_col="stud_tel_1";
if($_POST['phone']==2) $phone_col="stud_tel_2";
if($_POST['phone']==3) $phone_col="stud_tel_3";
//判斷住址
$addr_col="stud_addr_1";
if($_POST['address']==2) $addr_col="stud_addr_2";

if ($_POST[year_name]) {
	$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
	$seme_class=$_POST[year_name]."%";
	$query="select a.*,b.stud_name,b.stud_person_id,b.stud_sex,b.stud_birthday,b.$phone_col,b.addr_zip,b.$addr_col from stud_seme a left join stud_base b on a.student_sn=b.student_sn where a.seme_year_seme='$seme_year_seme' and a.seme_class like '$seme_class' and b.stud_study_cond in ('0','15') order by a.seme_class,a.seme_num";
	$res=$CONN->Execute($query);
	while(!$res->EOF) {
		$seme_class=$res->fields[seme_class];
		$sn[]=$res->fields[student_sn];
		$show_sn[$seme_class][$res->fields[seme_num]]=$res->fields[student_sn];
		$stud_data[$res->fields[student_sn]][stud_name]=$res->fields[stud_name];
		$stud_data[$res->fields[student_sn]][stud_id]=$res->fields[stud_id];
		$stud_data[$res->fields[student_sn]][stud_person_id]=$res->fields[stud_person_id];
		$stud_data[$res->fields[student_sn]][stud_sex]=$res->fields[stud_sex];
		$d_arr=explode("-",$res->fields[stud_birthday]);
		$dd=$d_arr[0]-1911;
		$stud_data[$res->fields[student_sn]][stud_birthday]=$dd.sprintf("%02d%02d",$d_arr[1],$d_arr[2]);
		$stud_data[$res->fields[student_sn]][stud_tel]=str_replace("-","",$res->fields[$phone_col]);
		$stud_data[$res->fields[student_sn]][addr_zip]=$res->fields[addr_zip];
		$stud_data[$res->fields[student_sn]][stud_addr_1]=$res->fields[$addr_col];
		$res->MoveNext();
	}
	$query="select stud_study_year from stud_base where student_sn='".pos($sn)."'";
	$res=$CONN->Execute($query);
	$stud_study_year=$res->fields[0];
	if ($IS_JHORES==0)
		$f_year=5;
	else
		$f_year=2;
	for ($i=0;$i<=$f_year;$i++) {
		for ($j=1;$j<=2;$j++) {
			$semes[]=sprintf("%03d",$stud_study_year+$i).$j;
			$rr=&get_all_setup("",$stud_study_year+$i,$j,$i+1+$IS_JHORES);
			$rrr=explode("\n",$rr[rule]);
			foreach($rrr as $k=>$v) $rule_arr[sprintf("%03d",$stud_study_year+$i).$j][$k]=explode("_",$v);
		}
	}
	array_pop($semes);
	array_pop($show_year);
	array_pop($show_seme);
	$fin_score=cal_fin_score($sn,$semes,"",array($sel_year,$sel_seme,$_POST[year_name]));

	for($i=1;$i<=5;$i++) {
		for($j=1;$j<=8;$j++) {
			$temp_arr[$i.$j]=$y_arr[$i].$s_arr[$j];
		}
		$temp2_arr[$i]=$semes[$i-1];
	}

	//echo "<pre>";
	//print_r($rule_arr);
	//echo "</pre>";
}

if ($_POST['xls']) {
	$sch=get_school_base();
	require_once "../../include/sfs_case_excel.php";
	$x=new sfs_xls();
	$x->setUTF8();
	$x->setBorderStyle(1);
	$x->addSheet("excel");
	$temp3_arr[]=array("selsch","grasch","jregno","stuclass","stuno","stuname","idno","sex","birthday","tel","post","address","sco11","sco12","sco13","sco14","sco15","sco16","sco17","sco18","sco21","sco22","sco23","sco24","sco25","sco26","sco27","sco28","sco31","sco32","sco33","sco34","sco35","sco36","sco37","sco38","sco41","sco42","sco43","sco44","sco45","sco46","sco47","sco48","sco51","sco52","sco53","sco54","sco55","sco56","sco57","sco58","scoavg","nofee");
	$temp3_arr[]=array("招生學校","畢業學校","國中報名序號","班級","學號","姓名","身分證號","性別","生日","電話","郵遞區號","地址","一上國","一上英","一上數","一上自","一上社","一上健","一上藝","一上綜","一下國","一下英","一下數","一下自","一下社","一下健","一下藝","一下綜","二上國","二上英","二上數","二上自","二上社","二上健","二上藝","二上綜","二下國","一下英","二下數","二下自","二下社","二下健","二下藝","二下綜","三上國","三上英","三上數","三上自","三上社","三上健","三上藝","三上綜","總平均","免報名費");
	foreach($show_sn as $seme_class => $d) {
		foreach($d as $site_num => $sn) {
			$str=array();
			$str=array("",$sch[sch_id],"",intval(substr($seme_class,-2,2)),$stud_data[$sn][stud_id],$stud_data[$sn][stud_name],$stud_data[$sn][stud_person_id],$stud_data[$sn][stud_sex],$stud_data[$sn][stud_birthday],$stud_data[$sn][stud_tel],$stud_data[$sn][addr_zip],$stud_data[$sn][stud_addr_1]);
			reset($temp2_arr);
			foreach($temp2_arr as $si) {
				reset($ss_link);
				foreach($ss_link as $sl) {
					$str[]=s2s(array("score"=>$fin_score[$sn][$sl][$si][score],"rule"=>$rule_arr[$si]),$smarty);
				}
			}
			$str[]=s2s(array("score"=>$fin_score[$sn][avg][score],"rule"=>$rule_arr[$seme_year_seme]),$smarty);
			$str[]=" ";
			$temp3_arr[]=$str;
		}
	}
	$x->items=$temp3_arr;
	$x->writeSheet();
	$x->process();
	exit;
}

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE); 
$smarty->assign("module_name","免試入學報表"); 
$smarty->assign("SFS_MENU",$menu_p); 
$smarty->assign("year_seme_menu",year_seme_menu($sel_year,$sel_seme)); 
$smarty->assign("class_year_menu",class_year_menu($sel_year,$sel_seme,$_POST[year_name]));
$smarty->assign("seme_year_seme",$seme_year_seme);
$smarty->assign("semes",$semes);
$smarty->assign("student_sn",$show_sn);
$smarty->assign("stud_data",$stud_data);
$smarty->assign("fin_score",$fin_score);
$smarty->assign("ss_link",$ss_link);
$smarty->assign("rule",$rule_arr);
$smarty->assign("col_arr",$temp_arr);
$smarty->assign("col2_arr",$temp2_arr);
$smarty->assign("menu2","輸出欄位： "."電話：".menu_sel($phone_arr,"phone",$_POST['phone'])."住址：".menu_sel($address_arr,"address",$_POST['address']));
$smarty->register_function('s2s', 's2s');
$smarty->display("stud_basic_test_distest.tpl");

function s2s($params, &$smarty)
{
	$score=$params['score'];
	$rule=$params['rule'];
	if ($score=="") return;
	if (count($rule)>1) {
		while(list($k,$v)=each($rule)){
			if($v[1]==">="){
			if($score >= $v[2])return $v[0];
			}elseif($v[1]==">"){
				if($score > $v[2])return $v[0];
			}elseif($v[1]=="="){
				if($score == $v[2])return $v[0];
			}elseif($v[1]=="<"){
				if($score < $v[2])return $v[0];
			}elseif($v[1]=="<="){
			if($score <= $v[2])return $v[0];
			}
		}
	}
	return;
}
?>
