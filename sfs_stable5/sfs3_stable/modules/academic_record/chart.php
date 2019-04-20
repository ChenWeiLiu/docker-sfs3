<?php 
//$Id: chart.php 5330 2009-01-17 03:22:42Z infodaes $
include "config.php";
include "../score_chart/chc_class2.php";
include "../score_chart/module-cfg.php";


sfs_check();

if (empty($class_num)) header("Location: index.php");

//////  從SFS3內建的函式取學校資料函式---------------------
$sch_data=get_school_base();

// smarty的樣版路徑設定  -----------------------------------
$template_dir = $SFS_PATH."/modules/score_chart/templates/";

//  預設的樣本檔  --(命名：ps國小,國中jh_head表頭.htm)
$tpl_defult_ps=array("head"=>"ps_head.htm","body"=>"ps_body.htm","end"=>"ps_end.htm");
$tpl_defult_jh=array("head"=>"jh_head.htm","body"=>"jh_body.htm","end"=>"jh_end.htm");
$tpl_defult_scope=array("head"=>"scope_head.htm","body"=>"scope_body.htm","end"=>"scope_end.htm");
$tpl_defult_scope_simple_chk=array("head"=>"scope_head.htm","body"=>"schk_body.htm","end"=>"schk_end.htm");
$tpl_defult_scope_complete_chk=array("head"=>"scope_head.htm","body"=>"cchk_body.htm","end"=>"schk_end.htm");
$tpl_defult_scope_none=array("head"=>"scope_head.htm","body"=>"scope_none_body.htm","end"=>"scope_end.htm");
$tpl_defult_scope_simple_chk_none=array("head"=>"scope_head.htm","body"=>"schk_none_body.htm","end"=>"schk_end.htm");
$tpl_defult_scope_complete_chk_none=array("head"=>"scope_head.htm","body"=>"cchk_none_body.htm","end"=>"schk_end.htm");

//  自訂的樣本檔名  -----------------------------------
$tpl_self=array("head"=>"my_head.htm","body"=>"my_body.htm","end"=>"my_end.htm");

//判斷範本
switch($_GET[chart_kind]) {
	case 2:
		$tpl=$tpl_defult_scope;
		break;
	case 3:
		$tpl=$tpl_defult_scope_simple_chk;
		break;
	case 4:
		$tpl=$tpl_defult_scope_complete_chk;
		break;
	case 5:
		$tpl=$tpl_defult_scope_none;
		break;
	case 6:
		$tpl=$tpl_defult_scope_simple_chk_none;
		break;
	case 7:
		$tpl=$tpl_defult_scope_complete_chk_none;
		break;
	default:
		$tpl=($IS_JHORES==0)?$tpl_defult_ps:$tpl_defult_jh;
}

$chk_item=chk_kind();//讀取檢核表表現狀況文字
$smarty->assign("add_memo_file",$template_dir."memo_null.tpl");
$img_title=get_title_pic();//讀取職稱圖章
	
if($_GET){
	$seme_year_seme=sprintf("%03d",curr_year()).curr_seme();//093_1
	$year_seme=curr_year()."_".curr_seme();
	$grade=substr($class_num,0,1);
	
	//指定備註說明檔 在data\school\score_chart下==>年級.txt
	$add_memo_file=$UPLOAD_PATH."school/score_chart/$grade.txt";
	if(! file_exists($add_memo_file)) $add_memo_file='';
	$smarty->assign("add_memo_file",$add_memo_file);
	
	
	$smarty->assign("add_memo_file",$add_memo_file);	

	$days=get_seme_dates($year_seme,$grade);//上課日數
	$smarty->assign("unit",($IS_JHORES)?"節":"天");

	$smarty->assign("school_name",$sch_data[sch_cname]);
	$smarty->assign("img_1",$img_title["校長"]);
	$img_2=($img_title["教務主任"]=="")?$img_title["教導主任"]:$img_title["教務主任"];
	$smarty->assign("img_2",$img_2);
	$img_3=($img_title["學務主任"]=="")?$img_title["訓導主任"]:$img_title["學務主任"];
	$smarty->assign("img_3",$img_3);
	$smarty->assign("sign_1_name",$sign_1_name);
	$smarty->assign("sign_2_name",$sign_2_name);
	$smarty->assign("sign_3_name",$sign_3_name);
	$smarty->assign("sign_1_form",$sign_1_form);
	$smarty->assign("sign_2_form",$sign_2_form);
	$smarty->assign("sign_3_form",$sign_3_form);
	if ($sign_3_title=="") $sign_3_title=0;
	$smarty->assign("sign_3_title",$SIGN_3_TITLE_ARR[$sign_3_title]);
	$smarty->assign("days",$days);

	$class_ary=get_class_info($grade,$year_seme);
	$smarty->display($template_dir.$tpl[head]);
	$i=0;

	$class_id=sprintf("%03d_%d_%02d_%02d",curr_year(),curr_seme(),$grade,substr($class_num,-2,2));
	$chk_tmp=split("_",$class_id);
	$chk_prt=$chk_tmp[0]-($chk_tmp[2]-$IS_JHORES); 
	//echo $chk_prt;
	($chk_prt>=94)?$chk_prt=0:$chk_prt=1;//0不印,1要印

	$class_data = new data_class($class_id);
	$seme_scope=$class_data->seme_scope();
	$seme_nor=$class_data->seme_nor();
	$seme_rew=$class_data->seme_rew();
	$seme_absent=$class_data->seme_absent();
	$smarty->assign("class_info",$class_ary[$class_id]);//班級學年度資料
	$smarty->assign("subject_nor",$class_data->subject_nor);
	$smarty->assign("subject_rew",$class_data->subject_rew);
	$smarty->assign("subject_abs",$class_data->subject_abs);
	$smarty->assign("chk_prt",$chk_prt);//將是否列印等第的參考值入
	$smarty->assign("IS_JHORES",$IS_JHORES);
	$smarty->assign("itemdata",get_chk_item(curr_year(),curr_seme()));//檢核表項目

	foreach ($class_data->stud_base as $student_sn=>$stud) {
		if($i>0){
			$smarty->assign("break_page","<P STYLE='page-break-before: always;'>");
		}else {
			$smarty->assign("break_page","");}
		$smarty->assign("stud",$stud);
		$smarty->assign("seme_scope",$seme_scope[$student_sn]);
		$smarty->assign("seme_nor",$seme_nor[$student_sn]);
		$smarty->assign("seme_rew",$seme_rew[$student_sn]);
		$smarty->assign("seme_absent",$seme_absent[$student_sn]);
		$smarty->assign("chk_value",get_chk_value($student_sn,curr_year(),curr_seme(),$chk_item,"value"));//讀取檢核表內容
		$smarty->display($template_dir.$tpl[body]);
		$i++;
	}
}
?>
