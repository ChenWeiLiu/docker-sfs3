<?php
//$Id: fix_tool.php 5310 2009-01-10 07:57:56Z hami $

include_once "config.php";

include "../../include/sfs_case_dataarray.php";
include_once "../../include/sfs_case_PLlib.php";


//使用者認證
sfs_check();
##################更新  學籍資料###########################
if($_POST[act]=='write_base'){
	$SQL="update stud_base set stud_id='$_POST[stud_id]', stud_name='$_POST[stud_name]', stud_sex='$_POST[stud_sex]', stud_study_year='$_POST[stud_study_year]' , curr_class_num='$_POST[curr_class_num]' , stud_study_cond='$_POST[stud_study_cond]'  where student_sn='$_POST[student_sn]' ";
	$rs=$CONN->Execute($SQL) or die($SQL);
	$url=$_SERVER[PHP_SELF]."?student_sn=".$_POST[student_sn];
	header("Location:$url");
}
##################更新  學期資料###########################
if($_POST[act]=='write_seme'){
	$SQL="update stud_seme set stud_id='$_POST[stud_id]', seme_year_seme='$_POST[seme_year_seme]', seme_class='$_POST[seme_class]', seme_num='$_POST[seme_num]' where student_sn='$_POST[student_sn]' and seme_year_seme='$_POST[old_seme_year_seme]' and seme_class='$_POST[old_seme_class]' and seme_num='$_POST[old_seme_num]'  and stud_id='$_POST[old_stud_id]'";
	$rs=$CONN->Execute($SQL) or die($SQL);
	$url=$_SERVER[PHP_SELF]."?student_sn=".$_POST[student_sn];
	header("Location:$url");
}
##################刪除資料###########################
if($_POST[act]=='del_base'){
	$SQL="delete from  stud_base   where student_sn='$_POST[student_sn]' ";
	$rs=$CONN->Execute($SQL) or die($SQL);
	$url=$_SERVER[PHP_SELF]."?student_sn=".$_POST[student_sn];
	header("Location:$url");
}
##################刪除學期資料###########################
if($_POST[act]=='del_seme'){
	$SQL="delete from stud_seme where student_sn='$_POST[student_sn]' and stud_id='$_POST[stud_id]'  and seme_year_seme='$_POST[seme_year_seme]' and seme_class='$_POST[seme_class]' and seme_num='$_POST[seme_num]' ";
	$rs=$CONN->Execute($SQL) or die($SQL);
	$url=$_SERVER[PHP_SELF]."?student_sn=".$_POST[student_sn];
	header("Location:$url");
}
##################刪除學期資料###########################
if($_POST[act]=='del_seme_all'){
	$SQL="delete from stud_seme where student_sn='$_POST[student_sn]' ";
	$rs=$CONN->Execute($SQL) or die($SQL);
	$url=$_SERVER[PHP_SELF]."?student_sn=".$_POST[student_sn];
	header("Location:$url");
}

head("問題工具箱");
print_menu($school_menu_p);

if( $_GET[student_sn]!=''){
	$SQL="select * from stud_base where student_sn='$_GET[student_sn]' ";
	$arr_a=get_order2($SQL);
	$SQL="select * from  stud_seme where student_sn='$_GET[student_sn]' order by seme_year_seme ";
	$arr_b=get_order2($SQL);
///////////////// 成績部分顯示//////////////////////////
	$SQL="select subject_id, subject_name from score_subject order by  subject_id ";
	$subj=initArray("id,sname",$SQL);//取中文名稱資料
	$SQL="select ss_id,scope_id ,subject_id from score_ss where  enable='1'  ";
	$ss_3=initArray3("SS,Sa,Sb",$SQL);//取SS_ID資料
	$SQL="select * from  stud_seme_score where student_sn='$_GET[student_sn]' and ss_score !='NULL' order by seme_year_seme ";
	$arr_seme_score=get_order2($SQL);//取該生所有學期成績資料
/////////////////加入中文科目名稱/////////////////
	for($i=0;$i<count($arr_seme_score);$i++){
		$SS=$arr_seme_score[$i][ss_id];
		$arr_seme_score[$i][cname]=$subj[$ss_3[$SS][Sa]].":".$subj[$ss_3[$SS][Sb]];
		}
	}

if($_POST[stud_id]!='') {
	$SQL="select * from stud_base where stud_id='$_POST[stud_id]' ";
	$arr_a=get_order2($SQL);
	$SQL="select * from  stud_seme where stud_id='$_POST[stud_id]'  order by seme_year_seme ";
	$arr_b=get_order2($SQL);
	}



$stud_coud=study_cond();//學籍資料代碼
$now_seme=sprintf("%03d",curr_year()).curr_seme();//目前學期//目前學年
// smarty template 路徑
$template_dir = $SFS_PATH."/".get_store_path()."/templates";
// 使用 smarty tag
$smarty->left_delimiter="{{";
$smarty->right_delimiter="}}";
//學校全銜
$smarty->assign("PHP_SELF",$_SERVER[PHP_SELF]);
$smarty->assign("school_long_name",$school_long_name);
$smarty->assign("now_seme",$now_seme);
$smarty->assign("arr_a",$arr_a);
$smarty->assign("arr_b",$arr_b);
$smarty->assign("arr_seme_score",$arr_seme_score);



$smarty->assign("stud_coud",$stud_coud);
$smarty->assign("template_dir",$template_dir);

$smarty->display("$template_dir/fix_tool.htm");


foot();

##################取資料函式###########################
function get_order2($SQL) {
	global $CONN ;
$rs=$CONN->Execute($SQL) or die($SQL);
$arr = $rs->GetArray();
return $arr ;
}
function initArray3($F1,$SQL){
	global $CONN ;
//	global $db;
// 當尚未到達 記錄集 $rs 的結束位置(EOF：End Of File)時，(即：還有記錄尚未取出時)
	$col=split(",",$F1);
	$rs = $CONN->Execute($SQL) or die($SQL);
	$col[0] = array();
	if (!$rs) {
    Return $CONN->ErrorMsg(); 
	} else {
		while (!$rs->EOF) {
		$col[0][$rs->fields[0]][$col[1]]=$rs->fields[1];
		$col[0][$rs->fields[0]][$col[2]]=$rs->fields[2];
	$rs->MoveNext(); // 移至下一筆記錄
	}
	}
	Return $col[0];
}

##################取得項目資訊函式###########################
function initArray($F1,$SQL){
	global $CONN ;
//	global $db;
// 當尚未到達 記錄集 $rs 的結束位置(EOF：End Of File)時，(即：還有記錄尚未取出時)
	$col=split(",",$F1);
	$rs = $CONN->Execute($SQL) or die($SQL);
	$sch_all = array();
	if (!$rs) {
    Return $CONN->ErrorMsg(); 
	} else {
		while (!$rs->EOF) {
		$sch_all[$rs->fields[0]]=$rs->fields[1]; 
	$rs->MoveNext(); // 移至下一筆記錄
	}
	}
	Return $sch_all;
}

?>
