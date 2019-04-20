<?php
//$Id: certi_htm.php 8291 2015-01-15 14:07:34Z brucelyc $
include "config.php";
include_once "my_fun.php";

//認證
sfs_check();

if (empty($_POST[year_seme])) {
	$sel_year = curr_year(); //目前學年
	$sel_seme = curr_seme(); //目前學期
	$year_seme=$sel_year."_".$sel_seme;
} else {
	$ys=explode("_",$_POST[year_seme]);
	$sel_year=$ys[0];
	$sel_seme=$ys[1];
}

$m_arr = get_sfs_module_set("");
extract($m_arr, EXTR_OVERWRITE);
if($IS_JHORES){
	$ss_link=array("語文-本國語文"=>"chinese","語文-鄉土語文"=>"local","語文-英語"=>"english","數學"=>"math","自然與生活科技"=>"nature","社會"=>"social","健康與體育"=>"health","藝術與人文"=>"art","綜合活動"=>"complex");
	$link_ss=array("chinese"=>"語文-本國語文","local"=>"語文-鄉土語文","english"=>"語文-英語","math"=>"數學","nature"=>"自然與生活科技","social"=>"社會","health"=>"健康與體育","art"=>"藝術與人文","complex"=>"綜合活動");
	$area_span=9;
	} else {
	$ss_link=array("語文-本國語文"=>"chinese","語文-鄉土語文"=>"local","語文-英語"=>"english","數學"=>"math","健康與體育"=>"health","生活"=>"life","自然與生活科技"=>"nature","社會"=>"social","藝術與人文"=>"art","綜合活動"=>"complex");
	$link_ss=array("chinese"=>"語文-本國語文","local"=>"語文-鄉土語文","english"=>"語文-英語","math"=>"數學","health"=>"健康與體育","life"=>"生活","nature"=>"自然與生活科技","social"=>"社會","art"=>"藝術與人文","complex"=>"綜合活動");
	$area_span=10;
}
if ($_POST[form1]) {
	$stud_study_year=$_POST[stud_study_year];
	foreach($_POST[sel_stud] as $k=>$sn) {
		$student_sn[]=$sn;
	}
	
	foreach($_POST[sel_seme] as $k=>$v) {
		$w=explode("_",$v);
		$max_seme=sprintf("%03d",$w[0]).$w[1];
		$semes[]=$max_seme;
		$show_year[]=$w[0];
		$show_seme[]=$w[1];
		$sm=&get_all_setup("",$w[0],$w[1],$w[0]-$stud_study_year+1+$IS_JHORES);
		$rule[$max_seme]=explode("\n",$sm[rule]);
	}
	$fin_score=cal_fin_score($student_sn,$semes,"",array($sel_year,$sel_seme,$_POST[year_name]),$_POST[precision]);
	if ($_POST[include_nor]) $fin_nor_score=cal_fin_nor_score($student_sn,$semes);
	$all_sn="'".implode("','",$student_sn)."'";
	$query = "select student_sn,stud_id,stud_name,stud_name_eng,stud_birthday from stud_base where student_sn in ($all_sn)";
	$res = $CONN->Execute($query) or die ($query);
	while(!$res->EOF) {
		$stud_id[$res->fields[student_sn]]=$res->fields[stud_id];
		
		$sb=explode("-",$res->fields[stud_birthday]);
		if (mb_substr($_POST[form1],0,5,"big5")=='列印成績表') {
			$stud_birthday[$res->fields[student_sn]]=($sb[0]-1911)."年".$sb[1]."月".$sb[2]."日";
			$stud_name[$res->fields[student_sn]]=$res->fields[stud_name];
			if($_POST[form1]=='列印成績表(七領域)') {
				$link_ss=array("language"=>"語文","math"=>"數學","nature"=>"自然與生活科技","social"=>"社會","health"=>"健康與體育","art"=>"藝術與人文","complex"=>"綜合活動");
				$ss_link=array("語文"=>"language","數學"=>"math","自然與生活科技"=>"nature","社會"=>"social","健康與體育"=>"health","藝術與人文"=>"art","綜合活動"=>"complex");
				$area_span=7;
			}
		} else {
			$stud_birthday[$res->fields[student_sn]]=$sb[0].".".$sb[1].".".$sb[2];
			$stud_name[$res->fields[student_sn]]=$res->fields[stud_name_eng]?$res->fields[stud_name_eng]:$res->fields[stud_name];
			
		}
		$res->MoveNext();
	}
	reset($fin_score);
	foreach($fin_score as $sn=>$v){
		reset($link_ss);
		foreach( $link_ss as $sl=>$vv) {
			reset($fin_score[$sn][$sl]);
			foreach ($fin_score[$sn][$sl] as $seme_year_seme => $vvv) {
				$fin_score[$sn][$sl][$seme_year_seme][str]=score2str($fin_score[$sn][$sl][$seme_year_seme][score],"",$rule[$seme_year_seme]);
			}
			$fin_score[$sn][$sl][avg][str]=score2str($fin_score[$sn][$sl][avg][score],"",$rule[$max_seme]);
		}
		reset($semes);
		foreach ($semes as $vvv => $seme_year_seme) {
			$fin_score[$sn][$seme_year_seme][avg][str]=score2str($fin_score[$sn][$seme_year_seme][avg][score],"",$rule[$seme_year_seme]);
		}
		if ($_POST[include_nor]) {
			$fin_score[$sn][language][avg][str]=score2str($fin_score[$sn][language][avg][score],"",$rule[$max_seme]);
			$fin_score[$sn][avg][str]=score2str($fin_score[$sn][avg][score],"",$rule[$max_seme]);
			reset($semes);
			foreach ($semes as $vvv => $seme_year_seme) {
				$fin_nor_score[$sn][$seme_year_seme][str]=score2str($fin_nor_score[$sn][$seme_year_seme][score],"",$rule[$seme_year_seme]);
			}
			$fin_nor_score[$sn][avg][str]=score2str($fin_nor_score[$sn][avg][score],"",$rule[$max_seme]);
		}
	}
} elseif ($_POST[me]) {
	$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
	$query = "select a.student_sn,a.stud_id,a.stud_name,a.curr_class_num,a.stud_study_cond,a.stud_study_year from stud_base a , stud_seme b where a.student_sn=b.student_sn and b.seme_year_seme='$seme_year_seme' and seme_class like '".$_POST[year_name].sprintf("%02d",$_POST[me])."' order by b.seme_num";
	$res = $CONN->Execute($query) or die ($query);
	$y_arr=array();
	while(!$res->EOF) {
		$stud_id[]=$res->fields[stud_id];
		$student_sn[]=$res->fields[student_sn];
		$stud_name[]=$res->fields[stud_name];
		$stud_study_cond[]=$res->fields[stud_study_cond];
		$stud_site[]=substr($res->fields[curr_class_num],-2,2);
		$y_arr[$res->fields[stud_study_year]]++;
		$res->MoveNext();
	}
	$stud_study_year=0;
	foreach ($y_arr as $k=>$v) {
		if ($y_arr[$stud_study_year]<$v) $stud_study_year=$k;
	}
	for ($i=0;$i<=(($IS_JHORES)?2:5);$i++) {
		for ($j=1;$j<=2;$j++) {
			$work_year=$stud_study_year+$i;
			$max_seme=sprintf("%03d",$work_year).$j;
			$semes[]=$max_seme;
			$show_year[]=$work_year;
			$show_seme[]=$j;
		}
	}
}


$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE); 
$smarty->assign("module_name","網頁式成績證明"); 
$smarty->assign("SFS_MENU",$menu_p);
$smarty->assign("year_seme_menu",year_seme_menu($sel_year,$sel_seme)); 
$smarty->assign("class_year_menu",class_year_menu($sel_year,$sel_seme,$_POST[year_name])); 
$smarty->assign("class_name_menu",class_name_menu($sel_year,$sel_seme,$_POST[year_name],$_POST[me])); 
$smarty->assign("stud_site",$stud_site);
$smarty->assign("stud_name",$stud_name);
$smarty->assign("stud_id",$stud_id);
$smarty->assign("student_sn",$student_sn);
$smarty->assign("stud_study_cond",$stud_study_cond);
$smarty->assign("stud_study_year",$stud_study_year);
$smarty->assign("show_year",$show_year);
$smarty->assign("show_seme",$show_seme);
$smarty->assign("stud_study_cond",$stud_study_cond);
$smarty->assign("study_cond",study_cond());
$smarty->assign("semes",$semes);
$smarty->assign("area_span",$area_span); 
if ($_POST[form1]) {
	$query =  "select b.title_name from teacher_post a ,teacher_title b ,teacher_base c where a.teacher_sn=c.teacher_sn and a.teach_title_id=b.teach_title_id and a.teacher_sn='$_SESSION[session_tea_sn]'";
	$res = $CONN->Execute($query);
	switch ($_POST[sel_sty]) {
		case 1:
			$print_str='<font style="font-size: 15pt;font-family: 標楷體;"><p align="right" valign="top">　</font><font style="font-size: 12pt;font-family: 標楷體;">製表人： <sup>'.$res->fields[title_name].'</sup>'.$_SESSION['session_tea_name'].'</font></p>';
			break;
		case 2:
			$print_str='<table width="100%"><tr align="left"><td width="33%"><b>製表人</b> '.$_SESSION['session_tea_name'].'<td width="34%"><b>註冊組長</b><td width="33%"><b>教務主任</b></td></tr></table><br>';
			break;
		case 3:
			$print_str='<font style="font-size: 15pt;font-family: 標楷體;"><p align="center">校長</p></font>';
			break;
	}
	
	if($_POST[form1]=='列印英文成績表'){
		//修正領域名稱
		if($IS_JHORES) {
			$ss_link=array("Mandarin"=>"chinese","Dialects"=>"local","English"=>"english","Mathematics"=>"math","Nature and Life Science&Technology"=>"nature","Society"=>"social","Health and Physical Education"=>"health","Arts and Humanities"=>"art","Integrative Activities"=>"complex");
			$link_ss=array("chinese"=>"Mandarin","local"=>"Dialects","english"=>"English","math"=>"Mathematics","nature"=>"Nature and Life Science&Technology","social"=>"Society","health"=>"Health and Physical Education","art"=>"Arts and Humanities","complex"=>"Integrative Activities");
		} else {
			$ss_link=array("Mandarin"=>"chinese","Dialects"=>"local","English"=>"english","Mathematics"=>"math","Health and Physical Education"=>"health","life"=>"life","Nature and Life Science&Technology"=>"nature","Society"=>"social","Arts and Humanities"=>"art","Integrative Activities"=>"complex");
			$link_ss=array("chinese"=>"Mandarin","local"=>"Dialects","english"=>"English","math"=>"Mathematics","health"=>"Health and Physical Education","life"=>"life","nature"=>"Nature and Life Science&Technology","social"=>"Society","art"=>"Arts and Humanities","complex"=>"Integrative Activities");
		}
		//修正簽名列
		switch ($_POST[sel_sty]) {
		case 1:
			$print_str='<font style="font-size: 15pt;font-family: Arial;"><p align="right" valign="top">　</font><font style="font-size: 12pt;font-family: Arial;">Undertaker: <sup>'.$res->fields[title_name].'</sup>'.$_SESSION['session_tea_name'].'</font></p>';
			break;
		case 2:
			$print_str='<table width="100%"><tr align="left"><td width="33%"><b>Undertaker: </b> '.$_SESSION['session_tea_name'].'<td width="34%"><b>Head of Registrar: </b><td width="33%"><b>Director of Academic Affairs: </b></td></tr></table><br>';
			break;
		case 3:
			$print_str='<font style="font-size: 15pt;font-family: Arial;"><p align="center">Principal:</p></font>';
			break;
		}
		//增加等第判定轉換陣列
		$grade_eng=array('優'=>'A','甲'=>'B','乙'=>'C','丙'=>'D','丁'=>'E');
		$smarty->assign("grade_eng",$grade_eng);
		
	}
	
	$smarty->assign("sch",get_school_base());
	$smarty->assign("stud_birthday",$stud_birthday);
	$smarty->assign("link_ss",$link_ss);
	$smarty->assign("ss_link",$ss_link);
	$smarty->assign("ss_num",count($ss_link));
	$smarty->assign("fin_score",$fin_score);
	$smarty->assign("fin_nor_score",$fin_nor_score);
	
	$smarty->assign("year",date("Y")-1911);
	$smarty->assign("month",intval(date("m")));
	$smarty->assign("day",intval(date("d")));

	$smarty->assign("no_seme_avg",$no_seme_avg);
	$smarty->assign("print_str",$print_str);
	$smarty->assign("sel_year",$sel_year); 
	$smarty->assign("default_pword",$default_pword); 
	$start_no=$_POST['start_no'];
	$smarty->assign("start_no",$start_no--);
	
	if(mb_substr($_POST[form1],0,5,"big5")=='列印成績表') $smarty->display("score_certi_certi_htm_print.tpl");
	else $smarty->display("score_certi_certi_htm_print_2.tpl");
} else	$smarty->display("score_certi_certi_htm.tpl");

?>
