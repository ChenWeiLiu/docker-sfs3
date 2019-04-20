<?php

if ($_POST[class_id]=='') die();

require_once("./chc_config.php");
sfs_check();

$abs_kind=stud_abs_kind();//請假代碼陣列
($IS_JHORES==6) ? $grad_num=6:$grad_num=12;


$template_dir = $SFS_PATH."/".get_store_path()."/templates/";
$smarty->left_delimiter="{{";
$smarty->right_delimiter="}}";
$smarty->display($template_dir."report_abs_head.htm");


$break_page="<P STYLE='page-break-before: always;'>";//換頁符號
$prn_page = 0;//頁數
$all_class=count($_POST[class_id])-1;

foreach ($_POST[class_id] as $class_id =>$NULLnull) {
	//$class_id='093_2_04_02';
	list($year,$seme,$grade,$cla_no)=explode('_',$class_id);
	
	$title = sprintf("%d學年第%d學期",$year,$seme).num_tw($grade-$IS_JHORES)."年".num_tw($cla_no)."班 歷年缺曠課統計";
	$smarty->assign('break_page',($prn_page > 0 ? $break_page:''));
	
	
	
	
	
	$seme_year_seme=sprintf("%03d%1d",$year,$seme);	
	$seme_class=sprintf("%d%02d",$grade,$cla_no);
	$sql = "select stud_seme.student_sn,seme_num,stud_base.stud_id,stud_base.stud_name from stud_seme left join stud_base using(student_sn) where seme_year_seme='{$seme_year_seme}' and seme_class='{$seme_class}' order by seme_num";
	$rs = $CONN->Execute($sql);
	while ($rs and $ro=$rs->FetchNextObject(false)) {
		$ro->seme_abs=seme_class_id($ro->student_sn);
		$arys[$ro->student_sn] = get_object_vars($ro);
		}
	//$CONN->debug=true;
	foreach ($arys as $student_sn=>$stud) {
		$stud_id = $stud[stud_id];
		foreach($stud[seme_abs] as $grade_seme=>$data) {
			$seme_year_seme = sprintf("%03d%1d",$data[year],$data[seme]);
			$sql = "select * from stud_seme_abs where stud_id='{$stud_id}' and seme_year_seme='{$seme_year_seme}' order by abs_kind ";
			$rs = $CONN->Execute($sql);
			while ($rs and $ro=$rs->FetchNextObject(false)) {
				if ($ro->abs_days >0) {
				$stud[seme_abs][$grade_seme][abs][$ro->abs_kind] += $ro->abs_days;
				$stud[seme_abs][abs_sum][abs][$ro->abs_kind] += $ro->abs_days;
				}
			}
		}
		$arys[$student_sn]=$stud;
	}

$smarty->assign("title",$title);
###----- 指派學生資料進入smarty ------
$smarty->assign("arys",$arys);

###----- 指派學生資料後才抽出一個陣列當標頭------
$one_stu=array_shift($arys);
$one_stu=$one_stu[seme_abs];
$smarty->assign("one_stu",$one_stu);
###----- 指派缺曠課類別-----------------------
$smarty->assign("abs_kind",$abs_kind);
###----- 指派缺曠課類別-----------------------
$smarty->assign("grad_num",grad_num($grad_num));

$smarty->display($template_dir."report_abs.htm");

unset($arys);
unset($one_stu);
$prn_page++;
}

$smarty->display($template_dir."report_abs_end.htm");
//print "<pre>";
//print_r(array_chunk($arys, 1));
//print_r($one_stu);
//print_r($arys);






function grad_num($aa){
	global $IS_JHORES; 
	for ($i=1;$i<=$aa;$i++){
	if ($i%2==1) {	
		$key=ceil($i/2)+$IS_JHORES."_1";
		$grad[$key]=num_tw(ceil($i/2))."上";
	}else{
		$key=ceil($i/2)+$IS_JHORES."_2";
		$grad[$key]=num_tw(ceil($i/2))."下";		
	}}
	return $grad;
}



function seme_class_id($student_sn='') {
	global $CONN, $IS_JHORES;
	$max_grade=6;
	if ($IS_JHORES>0) $max_grade=3;
	$arys = array();
	for($i=1+$IS_JHORES;$i<=$max_grade+$IS_JHORES;$i++) {
		$arys["{$i}_1"]=array();
		$arys["{$i}_2"]=array();
	}
	if (!empty($student_sn)) {
		$sql = "select seme_year_seme,seme_class,seme_class_name,seme_num, stud_id from stud_seme where student_sn='{$student_sn}' order by seme_year_seme, seme_class";
		$rs = $CONN->Execute($sql);
		while ($rs and $ro=$rs->FetchNextObject(false)) {
			$year = substr($ro->seme_year_seme,0,3);
			$seme = substr($ro->seme_year_seme,-1);
			$grade = substr($ro->seme_class,0,-2);
			$cla_no = substr($ro->seme_class, -2);
			$class_id = sprintf("%03d_%d_%02d_%02d",$year,$seme,$grade,$cla_no);
			$sql2 = "select c_name from school_class where class_id='{$class_id}'";
			$rs2 = $CONN->Execute($sql2);
			if ($rs2 and $ro2=$rs2->FetchNextObject(false)) { 
				$class_title = num_tw($grade-$IS_JHORES)."年".$ro2->c_name."班".$ro->seme_num."號";
			}
			$seme_title = sprintf("%d學年第%d學期",$year,$seme);
			$grade_seme = sprintf("%d_%d",$grade,$seme);
			$arys[$grade_seme][year]=$year;
			$arys[$grade_seme][seme]=$seme;
			$arys[$grade_seme][grade]=$grade;
			$arys[$grade_seme][cla_no]=$cla_no;
			$arys[$grade_seme][seme_num]=$ro->seme_num;
			$arys[$grade_seme][class_id]=$class_id;
			$arys[$grade_seme][seme_title]=$seme_title;
			$arys[$grade_seme][class_title]=$class_title;
		}
	}
	//print "<pre>";
	//print_r($arys);
	//-- 以下修正 補空的 class_id 讓成績可以正確產生
	$arys2=array();
	foreach ($arys as $grade_seme=>$detail) {
		if (empty($detail)) {
			$arys2[]=$grade_seme;
		}
	}
	//print_r($arys2);
	$arys2 = array_reverse($arys2);
	//print_r($arys2);
	foreach($arys2 as $grade_seme) {
		$grade=substr($grade_seme,0,1);
		$seme=substr($grade_seme,-1);
		if ($seme==2) {
			$grade++;
			$seme=1;
		}
		elseif ($seme==1) {
			$seme=2;
		}
		$grade_seme_next="{$grade}_{$seme}";
		$class_id = $arys[$grade_seme_next][class_id];
		if (!empty($class_id)) {
			list($year,$seme,$grade,$clano)=explode('_',$class_id);
			if ($seme==2) $seme=1;
			elseif ($seme==1) {
				$grade--;
				$seme=2;
				$year--;
			} 
			$class_id = sprintf("%03d_%d_%02d_%02d",$year,$seme,$grade,$cla_no);
			$seme_title = sprintf("%d學年度第%d學期",$year,$seme);
			$class_title = num_tw($grade-$IS_JHORES)."年級";
			$arys[$grade_seme][class_id]=$class_id;
			$arys[$grade_seme][seme_title]=$seme_title;
			$arys[$grade_seme][class_title]=$class_title;
		}
	}
	//print_r($arys);
	//--- 修正結束
	
	return $arys;
}

?>
