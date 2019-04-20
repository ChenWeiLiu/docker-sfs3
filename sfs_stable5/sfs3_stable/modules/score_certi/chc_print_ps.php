<?php


/* 取得設定檔 */
include "config.php";

sfs_check();


// smarty的樣版路徑設定  -----------------------------------
$template_dir = $SFS_PATH."/".get_store_path()."/templates";

//  預設的樣本檔  --(命名：prt列印_ps國小_head表頭.htm)
$tpl_defult=array("head"=>"prt_ps_head.htm","body"=>"prt_ps_body.htm","end"=>"prt_ps_end.htm");

//  自訂的樣本檔名  -----------------------------------
$tpl_self=array("head"=>"my_prt_ps_head.htm","body"=>"my_prt_ps_body.htm","end"=>"my_prt_ps_end.htm");

//  如果沒有自訂的樣本,就用預設的  --------------------
(file_exists($template_dir."/".$tpl_self[head])) ? $tpl=$tpl_self:$tpl=$tpl_defult;

$smarty->left_delimiter="{{";
$smarty->right_delimiter="}}";


//////  從SFS3內建的函式取學校資料函式---------------------
$sch_data=get_school_base();
/////    從SFS3內建的函式取得學籍資料代碼  -------------------
$stud_coud=study_cond();

//////  自傳入的學生流水號,從data_stud物件產生學生資料---------------------
$stud_data=new data_stud($_POST[student_sn]);


//$smarty->assign("stud_data",$stud_data);
$smarty->display($template_dir."/".$tpl[head]);

//	echo "<PRE>$_POST[student_sn]";

$sem_nums=1;//計算張數用
$all_page=count($_POST[stu_sn]);
$pr_all_page=ceil($all_page/2);
	$smarty->assign("pr_all_page",$pr_all_page);

foreach($_POST[stu_sn] as $class_id => $null){
	$Class_DETAIL=split("_",$class_id);
    $my_seme_score=$stud_data->seme_score($class_id,$_POST[student_sn]);
    $my_test=seme_score2smarty($my_seme_score,$class_id);
	$Class_DETAIL[0]=Num2CNum($Class_DETAIL[0]+0);//轉化為國字,學年度
	$Class_DETAIL[1]=Num2CNum($Class_DETAIL[1]+0);//轉化為國字,學期
	//   判斷是否國中  ----------
	($IS_JHORES==6) ? $Class_DETAIL[2]=$Class_DETAIL[2]-6:$Class_DETAIL[2]=$Class_DETAIL[2]+0;
	$Class_DETAIL[2]=Num2CNum($Class_DETAIL[2]);//轉化為國字,年級
	$Class_DETAIL[3]=Num2CNum($Class_DETAIL[3]+0);//轉化為國字,班級

// smarty 指派變數處理  -----------------------------------
	$smarty->assign("stud_coud",$stud_coud[$stud_data->study_cond]);
	$smarty->assign("school_name",$sch_data[sch_cname]);
	$smarty->assign("stud_data",$stud_data);
	$smarty->assign("Class_DETAIL",$Class_DETAIL);
	$smarty->assign("data",$my_test);
	$smarty->assign("all_page",$all_page);
	$smarty->assign("page",$sem_nums);
	$smarty->assign("pr_page",ceil($sem_nums/2));

	(($sem_nums % 2)==0 && $all_page!=$sem_nums) ? $smarty->assign("break_page","<P STYLE='page-break-before: always;'>"):$smarty->assign("break_page","");
	$smarty->display($template_dir."/".$tpl[body]);
	$sem_nums++;
//print_r($my_test);
//	unset($my_test);
}
$smarty->display($template_dir."/".$tpl[end]);
?>
