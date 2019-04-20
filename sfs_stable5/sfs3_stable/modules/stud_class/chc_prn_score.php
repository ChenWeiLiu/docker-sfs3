<?php
//$Id: chc_prn_score.php 6848 2012-08-01 01:55:54Z hsiao $
include "report_config.php";
//取用學籍報表內--彰縣函式
$chc_class_file= $SFS_PATH."/modules/stud_report/chc_func_class.php";
require_once($chc_class_file);
include_once "../../include/sfs_case_dataarray.php";

//列印學籍記錄表
//認證
sfs_check();
// 1.smarty物件
//取用學籍報表內樣本
$template_dir = $SFS_PATH."/modules/stud_report/templates/";
$tol_score_htm=$SFS_PATH."/modules/stud_report/templates/prn_all_score.tpl";
$prn_move_tpl=$SFS_PATH."/modules/stud_report/templates/prn_move.tpl";
// $smarty->config_dir = $template_dir;
$smarty->left_delimiter="{{";
$smarty->right_delimiter="}}";
//	將基本的變數及陣列傳入 smarty


function get_stsn($class_id){
		global $CONN;
	$st_sn=array();
	foreach($class_id as $key=>$data){
	$class_ids=split("_",$key);
	$seme=$class_ids[0].$class_ids[1];
	$the_class=($class_ids[2]+0).$class_ids[3];
	$SQL="select student_sn from stud_seme where  seme_year_seme ='$seme' and seme_class='$the_class' order by seme_num ";
	$rs = $CONN->Execute($SQL);
	$the_sn=$rs->GetArray();
	for ($i=0;$i<$rs->RecordCount();$i++){
		array_push($st_sn,$the_sn[$i][student_sn]);
		}
	}
return $st_sn;
}

//個資記錄
$cid=implode(",",$_POST[class_id]);
$test=pipa_log("印出本班網頁式學籍記錄表\r\n班級代碼：$cid\r\n");

///以班級陣列取出學生
if ($_POST[act]=='OK' && is_array($_POST[class_id]) ){
	$sn_ary=get_stsn($_POST[class_id]);
//			print_r($sn_ary);die();
	}

///以學號取出學生
if (($_POST[act]=='OK' && $_POST[list_stud_id]) || $_GET[list_stud_id]){
($_POST[list_stud_id]!='') ? $list_stud_id=$_POST[list_stud_id]:$list_stud_id=$_GET[list_stud_id];
	if (ereg('-',$list_stud_id)==true){
		$aa=split('-',$list_stud_id);//切開字串
		$SQL="select stud_id,student_sn from stud_base where stud_id between '".$aa[0]."' and '".$aa[1]."' order by stud_id ";
		$rs=$CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
		$All_ss=$rs->GetArray();
		foreach($All_ss as $ss){$sn_ary[]=$ss[student_sn];	}
	}else{
		$SQL="select stud_id,student_sn from stud_base where stud_id ='$list_stud_id' order by stud_id ";
		$rs=$CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
		$All_ss=$rs->GetArray();
		foreach($All_ss as $ss){$sn_ary[]=$ss[student_sn];	}
		}
		//echo "<pre>";
		//print_r($sn_ary);die();
		
	}
	
//print_r($st_sn);



$break_page="<P STYLE='page-break-before: always;'>";
//	$pk_detail	全年級的配課資料
//秀出網頁布景標頭
//主要內容


//$sn_ary = array(1105,1107,1109);
$prn_page = 0;
$smarty->display($template_dir."prn_head.tpl");

$smarty->assign('prn_move_tpl',$prn_move_tpl);
$smarty->assign('guar_kind',guar_kind());

foreach($sn_ary as $student_sn) {
	$student_data=new data_student($student_sn);
	$prn_page++;
	$seme_width=15;
	if ($IS_JHORES==0) $seme_width=7;
	$smarty->assign('break_page',($prn_page>1?$break_page:''));
	$smarty->assign('school_name',$school_long_name);
	$smarty->assign('seme_width',$seme_width);
	$smarty->assign('base',$student_data->base);//基本資料
	$smarty->assign('seme_ary',$student_data->seme_ary);		//每個學期..含成績座號與標題
	$smarty->assign('all_score',$student_data->all_score);		//所有學期成績
	$smarty->assign('move_data',$student_data->move);			//所有異動記錄
//	print_r($student_data->move);
	$smarty->display($tol_score_htm);
	unset($student_data);
}
//$smarty->display($template_dir."prn_foot.tpl");


?>
