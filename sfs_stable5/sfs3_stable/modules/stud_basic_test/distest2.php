<?php

// $Id: distest2.php 7711 2013-10-23 13:07:37Z smallduh $

// --系統設定檔
include "select_data_config.php";
include "../../include/sfs_case_score.php";

//認證
sfs_check();

$ss_link=array(1=>"chinese",2=>"english",3=>"math",4=>"social",5=>"nature",6=>"health",7=>"art",8=>"complex");
if ($IS_JHORES==0)
	$f_year=5;
else
	$f_year=2;

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
$s_arr=array(1=>"國",2=>"英",3=>"數",4=>"社",5=>"自",6=>"健",7=>"藝",8=>"綜");
//判斷家長
$parent_col="guardian_name";
if($_POST['parent']==2) $parent_col="fath_name";
if($_POST['parent']==3) $parent_col="moth_name";
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
		$stud_data[$res->fields[student_sn]][stud_tel]=substr(str_replace("-","",$res->fields[$phone_col]),0,10);
		$stud_data[$res->fields[student_sn]][addr_zip]=(strlen($res->fields[addr_zip])>3)?substr($res->fields[addr_zip],0,3):sprintf("%-3s",$res->fields[addr_zip]);
		$stud_data[$res->fields[student_sn]][stud_addr_1]=$res->fields[$addr_col];
		$query2 = "select $parent_col from stud_domicile where stud_id ='".$res->fields[stud_id]."'";
		$res2=$CONN->Execute($query2);
		$stud_data[$res->fields[student_sn]][parent_name]=$res2->fields[$parent_col];
		$res->MoveNext();
	}
	$query="select stud_study_year from stud_base where student_sn='".pos($sn)."'";
	$res=$CONN->Execute($query);
	$stud_study_year=$res->fields[0];
	for ($i=0;$i<=$f_year;$i++) {
		for ($j=1;$j<=2;$j++) {
			$semes[]=sprintf("%03d",$stud_study_year+$i).$j;
		}
	}
	array_pop($semes);
	$fin_score=cal_fin_score($sn,$semes,"","");
	//echo "<pre>";print_r($fin_score);echo "</pre>";
	for($i=1;$i<=5;$i++) {
		for($j=1;$j<=8;$j++) {
			$temp_arr[$i.$j]=$y_arr[$i].$s_arr[$j];
		}
		$temp2_arr[$i]=$semes[$i-1];
	}
}

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE); 
$smarty->assign("module_name","免試入學報表-99中區、北區五專"); 
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
$smarty->assign("menu2","輸出欄位： "."家長：".menu_sel($parent_arr,"parent",$_POST['parent'])."電話：".menu_sel($phone_arr,"phone",$_POST['phone'])."住址：".menu_sel($address_arr,"address",$_POST['address']));
$smarty->register_function('o2n', 'o2n');
$smarty->register_function('s2n', 's2n');
$smarty->register_function('tavg', 'tavg');
if ($_POST['txt']) {
	$filename = "student.txt";
	header("Content-disposition: attachment; filename=$filename");
	header("Content-type: text/plain");
	//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
	header("Expires: 0");
	$smarty->display("stud_basic_test_distest2_txt.tpl");
} elseif ($_POST['xls']) {
	$c_arr=array(1=>"丁",2=>"丙",3=>"乙",4=>"甲",5=>"優");
	$s=get_school_base();
	require_once "../../include/sfs_case_excel.php";
	$x=new sfs_xls();
	$x->setUTF8();
	$x->setBorderStyle(1);
	$x->addSheet("student");
	$x->setRowText(array("學校代碼","報名序號","學號","班級","座號","學生姓名","身分證號","性別","出生年","出生月","出生日","家長姓名","電話","郵遞區號","地址","國文","英文","數學","社會","自然","健康體育","藝術人文","綜合活動","七大領域平均等第","報名學校代碼","低收入戶","失業給付","特別加分"));
	$n=1;
	foreach($show_sn as $seme_class => $d) {
		foreach($d as $site => $sn) {
			$cno=substr($seme_class,-2,2);
			$dd=$stud_data[$sn][stud_birthday];
			$row_arr=array($s[sch_id],$n,$stud_data[$sn][stud_id],$cno,$site,$stud_data[$sn][stud_name],$stud_data[$sn][stud_person_id],$stud_data[$sn][stud_sex],substr($dd,0,2),substr($dd,2,2),substr($dd,4,2),$stud_data[$sn][parent_name],$stud_data[$sn][stud_tel],$stud_data[$sn][addr_zip],$stud_data[$sn][stud_addr_1]);
			foreach($ss_link as $sl) {
				$params=array();
				$params['score']=$fin_score[$sn][$sl];
				$params['semes']=$semes;
				$row_arr[]=s2n($params);
			}
			$params=array();
			$params['score']=$fin_score[$sn];
			$params['semes']=$semes;
			$params['ss_link']=$ss_link;
			$row_arr[]=$c_arr[tavg($params)];
			for($i=0;$i<4;$i++) $row_arr[]="";
			$data_arr[]=$row_arr;
			$n++;
		}
	}
	$x->items=$data_arr;
	$x->writeSheet();
	$x->process();
	exit;
} elseif ($_POST['chart']) {
	$smarty->assign("sch_arr",get_school_base());
	$smarty->assign("css_link",array("chinese"=>"本國語文","english"=>"英語","math"=>"數學","social"=>"社會","nature"=>"自然與生活科技","health"=>"健康與體育","art"=>"藝術與人文","complex"=>"綜合活動"));
	$smarty->display("stud_basic_test_distest2_tcc.tpl");
} else
	$smarty->display("stud_basic_test_distest2.tpl");

function o2n($params, &$smarty)
{
	$s=floatval($params['score']);
	if($s >= 90) return 5;
	elseif($s >= 80) return 4;
	elseif($s >= 70) return 3;
	elseif($s >= 60) return 2;
	else return 1;
}

function s2n($params, &$smarty)
{
	$score=$params['score'];
	$semes=$params['semes'];
	if (count($score)>1) {
		$total=0;
		foreach($score as $k=>$d) {
			if ($d['score']!="") {
				if($d['score']=="" || !in_array($k,$semes)) continue;
				$s=floatval($d['score']);
				if($s >= 90) $total+=5;
				elseif($s >= 80) $total+=4;
				elseif($s >= 70) $total+=3;
				elseif($s >= 60) $total+=2;
				else $total+=1;
			}
		}
		return sprintf("%02d",$total);
	} else
		return "00";
}

function tavg($params, &$smarty)
{
	$score=$params['score'];
	$ss_link=$params['ss_link'];
	$semes=$params['semes'];
	$mode=$params['mode'];
	if (count($score)>1 && count($semes)>1) {
		$t=0;
		foreach($ss_link as $ss) {
			$s=0;
			foreach($semes as $se) {
				$s+=$score[$ss][$se][score];
			}
			$t+=number_format($s/5,2);
		}
		$t=round($t/8,1);
		if ($mode) return $t;
		if($t >= 90) return 5;
		elseif($t >= 80) return 4;
		elseif($t >= 70) return 3;
		elseif($t >= 60) return 2;
		else return 1;
	} else 
	return 0;
}
?>
