<?php
//$Id:  $
include "config.php";
include "../../include/sfs_case_score.php";
include "../../include/sfs_case_dataarray.php";
//認證
sfs_check();

//$percision=$_POST['percision'];
$year_name=substr($class_id,0,-2);
$study_cond_array=study_cond();

if($year_name>2){
	$ss_link=array("語文-本國語文"=>"chinese","語文-鄉土語文"=>"local","語文-英語"=>"english","數學"=>"math","自然與生活科技"=>"nature","社會"=>"social","健康與體育"=>"health","藝術與人文"=>"art","綜合活動"=>"complex");
	$link_ss=array("chinese"=>"語文-本國語文","local"=>"語文-鄉土語文","english"=>"語文-英語","math"=>"數學","nature"=>"自然與生活科技","social"=>"社會","health"=>"健康與體育","art"=>"藝術與人文","complex"=>"綜合活動");
	//$order_array=array("language"=>"語文領域","chinese"=>"　本國語文","english"=>"　英語","local"=>"　本土語言","health"=>"健康與體育","math"=>"數學","social"=>"社會","nature"=>"自然與生活科技","art"=>"藝術與人文","complex"=>"綜合活動","avg"=>"學期成績");
	$area_rowspan=9;
} else {
	$ss_link=array("語文-本國語文"=>"chinese","語文-鄉土語文"=>"local","語文-英語"=>"english","數學"=>"math","健康與體育"=>"health","生活"=>"life","綜合活動"=>"complex");
	$link_ss=array("chinese"=>"語文-本國語文","local"=>"語文-鄉土語文","english"=>"語文-英語","math"=>"數學","health"=>"健康與體育","life"=>"生活","complex"=>"綜合活動");
	//$order_array=array("language"=>"語文領域","chinese"=>"　本國語文","english"=>"　英語","local"=>"　本土語言","health"=>"健康與體育","math"=>"數學","life"=>"生活","complex"=>"綜合活動","avg"=>"學期成績");
	$area_rowspan=7;
}

if (empty($_POST[year_seme])) {
	$sel_year = curr_year(); //目前學年
	$sel_seme = curr_seme(); //目前學期
	$year_seme=$sel_year."_".$sel_seme;
} else {
	$ys=explode("_",$_POST[year_seme]);
	$sel_year=$ys[0];
	$sel_seme=$ys[1];
}

$order=$_POST[order];

//抓取班級名稱
$class_name_arr = class_base();
$class_name=$class_name_arr[$class_id];

if($year_name) {
	$percision--;
	$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
	$seme_class=$year_name.sprintf("%02d",$_POST[me]);
	//$query="select a.*,b.stud_name from stud_seme a left join stud_base b on a.student_sn=b.student_sn where a.seme_year_seme='$seme_year_seme' and a.seme_class like '$year_name%' and b.stud_study_cond in ('0','15') order by a.seme_class,a.seme_num";
	$query="select a.*,b.stud_name,b.stud_study_cond from stud_seme a left join stud_base b on a.student_sn=b.student_sn where a.seme_year_seme='$seme_year_seme' and b.curr_class_num like '$class_id%' and b.stud_study_cond in ('0','15') order by a.seme_class,a.seme_num";
	$res=$CONN->Execute($query);
	while(!$res->EOF) {
		$student_sn=$res->fields['student_sn'];
		$sn[]=$student_sn;
		$student_data[$student_sn]['seme_num']=sprintf('%02d',$res->fields['seme_num']);
		$student_data[$student_sn]['stud_name']=$res->fields['stud_name'];
		$student_data[$student_sn]['stud_id']=$res->fields['stud_id'];
		$student_data[$student_sn]['class_name']=$class_year[$year_name].$res->fields[seme_class_name].'班';
		$student_data[$student_sn]['stud_study_cond']=$res->fields['stud_study_cond']?'<br><font size=2 color="red">'.$study_cond_array[$res->fields['stud_study_cond']].'</font>':'';

		$res->MoveNext();
	}
	$semes[]=sprintf("%03d",$sel_year).$sel_seme;
	$show_year[]=$sel_year;
	$show_seme[]=$sel_seme;
	//抓取領域成績
	$fin_score=cal_fin_score($sn,$semes,"",array($sel_year,$sel_seme,$year_name),$percision);
	
	//抓取日常生活表現記錄
	foreach($sn as $key=>$value){
		$sql="select * from stud_seme_score_nor where seme_year_seme='$seme_year_seme' and student_sn=$value";
		$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
		while(!$res->EOF) {
			$student_sn=$res->fields['student_sn'];
			$ss_id=$res->fields['ss_id'];
			$student_data[$student_sn]['nor'][$ss_id]=$res->fields['ss_score_memo'];
			$res->MoveNext();
		}
	}
	/* 這是等第判定
	$sm=&get_all_setup("",$sel_year,$sel_seme,$year_name);
	$rule=explode("\n",$sm[rule]);
	while(list($s,$v)=each($fin_score)) {
		$fin_score[$s][avg][str]=score2str($fin_score[$s][avg][score],"",$rule);
	}
	*/
}

//進行排序
$score_rank=array();
foreach($fin_score as $student_sn=>$score) {
	if($order=='') $score_rank[$student_sn]=$student_data[$student_sn]['seme_num'];
		elseif($order=='total') $score_rank[$student_sn]=$score['avg']['score']; else $score_rank[$student_sn]=$score[$order]['avg']['score'];
}
if($order) arsort($score_rank); else asort($score_rank);

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE); 
$smarty->assign("module_name","學習領域學期成績總表"); 
$smarty->assign("SFS_MENU",$menu_p); 
//$smarty->assign("year_seme_menu",year_seme_menu($sel_year,$sel_seme,"this.form.target='';")); 
//$smarty->assign("class_year_menu",class_year_menu($sel_year,$sel_seme,$year_name,"this.form.target='';")); 
$smarty->assign("class_name",$class_name);
$smarty->assign("show_year",$show_year);
$smarty->assign("show_seme",$show_seme);
$smarty->assign("semes",$semes);
$smarty->assign("curr_seme",$semes[0]);
$smarty->assign("fin_score",$fin_score);
$smarty->assign("student_data_nor",$student_data_nor);
$smarty->assign("ss_link",$ss_link);
$smarty->assign("link_ss",$link_ss);
$smarty->assign("rule",$rule_all);
$smarty->assign("year_name",$year_name);
$smarty->assign("percision_radio",$percision_radio);
$smarty->assign("student_data",$student_data);
$smarty->assign("m_arr",$m_arr);
$smarty->assign("school_long_name",$school_long_name);
$smarty->assign("order",$order);
$smarty->assign("score_rank",$score_rank);

if ($_POST['print_all']) {
	$smarty->display("score_report_scope_print.tpl");
} else {
	$smarty->display("score_report_scope.tpl");
}
?>
