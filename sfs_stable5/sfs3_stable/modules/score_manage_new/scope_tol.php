<?php
//$Id: scope_tol.php 6530 2011-09-21 08:55:51Z infodaes $
include "config.php";
include "../../include/sfs_case_score.php";

//認證
sfs_check();

//$ss_link=array("語文-本國語文"=>"chinese","語文-鄉土語文"=>"local","語文-英語"=>"english","數學"=>"math","自然與生活科技"=>"nature","社會"=>"social","健康與體育"=>"health","藝術與人文"=>"art","綜合活動"=>"complex");
//$link_ss=array("chinese"=>"語文-本國語文","local"=>"語文-鄉土語文","english"=>"語文-英語","math"=>"數學","nature"=>"自然與生活科技","social"=>"社會","health"=>"健康與體育","art"=>"藝術與人文","complex"=>"綜合活動");
if($_POST[year_name]>2){
	$ss_link=array("語文-本國語文"=>"chinese","語文-鄉土語文"=>"local","語文-英語"=>"english","數學"=>"math","自然與生活科技"=>"nature","社會"=>"social","健康與體育"=>"health","藝術與人文"=>"art","綜合活動"=>"complex");
	$link_ss=array("chinese"=>"語文-本國語文","local"=>"語文-鄉土語文","english"=>"語文-英語","math"=>"數學","nature"=>"自然與生活科技","social"=>"社會","health"=>"健康與體育","art"=>"藝術與人文","complex"=>"綜合活動");
	$area_rowspan=9;
} else {
	$ss_link=array("語文-本國語文"=>"chinese","語文-鄉土語文"=>"local","語文-英語"=>"english","數學"=>"math","健康與體育"=>"health","生活"=>"life","綜合活動"=>"complex");
	$link_ss=array("chinese"=>"語文-本國語文","local"=>"語文-鄉土語文","english"=>"語文-英語","math"=>"數學","health"=>"健康與體育","life"=>"生活","complex"=>"綜合活動");
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

$percision=$_POST['percision'];

if($_POST[me]) {
	$percision_radio="<font size=2 color='red'> ◎成績顯示的精度：";
	$percision_array=array('1'=>'整數','2'=>'小數1位','3'=>'小數2位');
	foreach($percision_array as $key=>$value){
		if($percision==$key) $checked='checked'; else $checked='';
		$percision_radio.="<input type='radio' value='$key' name='percision' $checked onclick='this.form.submit();'>$value";	
	}
}

if ($_POST[me] && $percision) {
	$percision--;
	$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
	$seme_class=$_POST[year_name].sprintf("%02d",$_POST[me]);
	$query="select a.*,b.stud_name from stud_seme a left join stud_base b on a.student_sn=b.student_sn where a.seme_year_seme='$seme_year_seme' and a.seme_class='$seme_class' and b.stud_study_cond in ('0','15') order by a.seme_num";
	$res=$CONN->Execute($query);
	while(!$res->EOF) {
		$sn[$res->fields[seme_num]]=$res->fields[student_sn];
		$stud_name[$res->fields[seme_num]]=$res->fields[stud_name];
		$stud_id[$res->fields[seme_num]]=$res->fields[stud_id];
		$res->MoveNext();
	}
	$query="select stud_study_year from stud_base where student_sn='".pos($sn)."'";
	$res=$CONN->Execute($query);
	$semes[]=sprintf("%03d",$sel_year).$sel_seme;
	$show_year[]=$sel_year;
	$show_seme[]=$sel_seme;

	$fin_score=cal_fin_score($sn,$semes,"",array($sel_year,$sel_seme,$_POST[year_name]),$percision);
	$fin_nor_score=cal_fin_nor_score($sn,$semes);
	$sm=&get_all_setup("",$sel_year,$sel_seme,$_POST[year_name]);
	$rule=explode("\n",$sm[rule]);
	while(list($s,$v)=each($fin_score)) {
		$fin_score[$s][avg][str]=score2str($fin_score[$s][avg][score],"",$rule);
	}
}

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE); 
$smarty->assign("module_name","學習領域學期成績總表"); 
$smarty->assign("SFS_MENU",$menu_p); 
$smarty->assign("year_seme_menu",year_seme_menu($sel_year,$sel_seme)); 
$smarty->assign("class_year_menu",class_year_menu($sel_year,$sel_seme,$_POST[year_name])); 
$smarty->assign("class_name_menu",class_name_menu($sel_year,$sel_seme,$_POST[year_name],$_POST[me])); 
$smarty->assign("show_year",$show_year);
$smarty->assign("show_seme",$show_seme);
$smarty->assign("semes",$semes);
$smarty->assign("stud_id",$stud_id);
$smarty->assign("student_sn",$sn);
$smarty->assign("stud_name",$stud_name);
$smarty->assign("stud_num",count($stud_id));
$smarty->assign("fin_score",$fin_score);
$smarty->assign("fin_nor_score",$fin_nor_score);
$smarty->assign("ss_link",$ss_link);
$smarty->assign("link_ss",$link_ss);
$smarty->assign("ss_num",count($ss_link));
$smarty->assign("rule",$rule_all);
$smarty->assign("jos",$IS_JHORES);
$smarty->assign("class_base",class_base($seme_year_seme));
$smarty->assign("seme_class",$_POST[year_name].sprintf("%02d",$_POST[me]));

$smarty->assign("percision_radio",$percision_radio);
$smarty->assign("area_rowspan",$area_rowspan);

if ($_POST[friendly_print]) {
	$smarty->display("score_manage_new_scope_tol_print.tpl");
} else {
	$smarty->display("score_manage_new_scope_tol.tpl");
}
?>
