<?php
//$Id: fix_score.php 9260 2018-06-13 02:31:59Z chiming $
include_once "config.php";

include "../../include/sfs_case_dataarray.php";
include_once "../../include/sfs_case_PLlib.php";


//使用者認證
sfs_check();
##################更新  學籍資料###########################
if($_POST['act']=='do_change1' && $_POST['SCO_TAB']!='' && $_POST['student_sn']!='' && $_POST['year_seme']!=''  ){
	if (count($_POST['score_id']) ==0) die(backinput("未選擇那一筆資料，按下後重選！"));
	$TB=strip_tags($_POST['SCO_TAB']);
	$class_id=strip_tags($_POST['class_id']);
	foreach ($_POST['score_id'] as $key=>$Val){
		$SQL="update $TB set class_id='{$class_id}' where score_id='$key' ";
		$rs=$CONN->Execute($SQL) or die(backinput());
	}
	$url=$_SERVER['SCRIPT_NAME']."?st_sn=".$_POST['student_sn']."&year_seme=".$_POST['year_seme'];
	header("Location:$url");
}
##################更新  學期資料###########################
if($_POST['act']=='do_change2' && $_POST['SCO_TAB']!='' && $_POST['student_sn']!='' && $_POST['year_seme']!=''  ){
	if (count($_POST['score_id']) ==0) die(backinput("未選擇那一筆資料，按下後重選！"));
	if ($_POST['My_class_id'] =='') die(backinput("未輸入學期年班資料，按下後重填！"));
	$TB=strip_tags($_POST['SCO_TAB']);
	$class_id=strip_tags($_POST['My_class_id']);
	foreach ($_POST['score_id'] as $key=>$Val){
		$SQL="update $TB set class_id='{$class_id}' where score_id='{$key}' ";
		$rs=$CONN->Execute($SQL) or die(backinput());
	}
	$url=$_SERVER['SCRIPT_NAME']."?st_sn=".$_POST['student_sn']."&year_seme=".$_POST['year_seme'];
	header("Location:$url");
}
##################刪除資料###########################
if($_POST['act']=='del_data' && $_POST['SCO_TAB']!='' && $_POST['student_sn']!='' && $_POST['year_seme']!=''  ){
	if (count($_POST['score_id']) ==0)  die(backinput("未選擇刪除那一筆資料，按下後重選！"));
	$TB=strip_tags($_POST['SCO_TAB']);
	$SN=(int)$_POST['student_sn'];
	foreach ($_POST['score_id']  as $key=>$Val ){
		$SQL="delete from  $TB where student_sn='{$SN}' and score_id='{$key}' ";
		$rs=$CONN->Execute($SQL) or die(backinput());
	}
	$url=$_SERVER['SCRIPT_NAME']."?st_sn=".$SN."&year_seme=".$_POST['year_seme'];
	header("Location:$url");
}
##################sendmit資料###########################
if($_POST['act']=='do_sendmit' && $_POST['SCO_TAB']!='' && $_POST['student_sn']!='' && $_POST['year_seme']!=''  ){
	if (count($_POST['score_id']) ==0) die(backinput("未選擇那一筆資料，按下後重選！"));
	if ($_POST['sendmit'] =='') die(backinput("請填寫 sendmit 值，按下後重填！"));
	$TB=strip_tags($_POST['SCO_TAB']);
	$SN=(int)$_POST['student_sn'];
	$sendmit=(int)$_POST['sendmit'];
	foreach ($_POST['score_id']  as $key=>$Val ){
		$SQL="update $TB set sendmit='{$sendmit}' where score_id='{$key}' ";
		$rs=$CONN->Execute($SQL) or die(backinput());
	}

	$url=$_SERVER['SCRIPT_NAME']."?st_sn=".$SN."&year_seme=".$_POST['year_seme'];
	header("Location:$url");
}

head("成績修正工具");
print_menu($school_menu_p);


if($_POST['stud_id']!='') {
	$SQL="select * from stud_base where stud_id='{$_POST['stud_id']}' ";
	$arr_a=get_order2($SQL);
	}
if($_GET['st_sn']!='') {
	$SQL="select * from stud_base where student_sn='{$_GET['st_sn']}' ";
	$arr_a=get_order2($SQL);
	$SQL="select * from  stud_seme where student_sn='{$_GET['st_sn']}'  order by seme_year_seme ";
	$arr_b=get_order2($SQL);
	}
if($_GET['st_sn']!='' && $_GET['year_seme']!='') {
	$Score_Table="score_semester_".sprintf("%d",substr($_GET['year_seme'],0,3))."_".substr($_GET['year_seme'],3,1);
	//該學期成績表
	$SQL="select * from  $Score_Table where student_sn='{$_GET['st_sn']}' order by ss_id ,test_sort ";
	$rs=$CONN->Execute($SQL) or die(backinput());
	$arr_sco = $rs->GetArray();
///////////////// 成績部分顯示--中文科目名稱資料 陣列//////////////////////////
	$SQL="select subject_id, subject_name from score_subject order by  subject_id ";
	$subj=initArray("id,sname",$SQL);//取中文名稱資料
	$SQL="select ss_id,scope_id ,subject_id from score_ss where  enable='1'  ";
	$ss_3=initArray3("SS,Sa,Sb",$SQL);//取SS_ID資料
/////////////////加入中文科目名稱/////////////////
	for($i=0;$i<count($arr_sco);$i++){
		$SS=$arr_sco[$i]['ss_id'];
		$arr_sco[$i]['cname']=$subj[$ss_3[$SS]['Sa']].":".$subj[$ss_3[$SS]['Sb']];
		}
///////////////// 找出當學期年班//////////////////////////
	for($i=0;$i<count($arr_b);$i++){
	if($arr_b[$i]['seme_year_seme']===$_GET['year_seme']) {
	$stu_class_id=substr($_GET['year_seme'],0,3)."_".substr($_GET['year_seme'],3,1)."_".sprintf("%02d",substr($arr_b[$i]['seme_class'],0,1))."_".sprintf("%02d",substr($arr_b[$i]['seme_class'],1,2));
	$stu_sn=$arr_b[$i]['student_sn'];
	}
	}

	}


$now_seme=sprintf("%03d",curr_year()).curr_seme();//目前學期//目前學年
// smarty template 路徑
$template_dir = $SFS_PATH."/".get_store_path()."/templates";
// 使用 smarty tag
$smarty->left_delimiter="{{";
$smarty->right_delimiter="}}";
// 替代變數
$smarty->assign("arr_a",$arr_a);//傳入stud_base資料
$smarty->assign("arr_b",$arr_b);//傳入stud_seme資料
$smarty->assign("now_seme",$now_seme);//傳入目前學年學期
$smarty->assign("arr_sco",$arr_sco);//傳入該學期平時成績
if (count($arr_sco)!=0){
	$smarty->assign("SCO_TAB",$Score_Table);//傳入成績表名稱
	$smarty->assign("Seme_class_id",$stu_class_id);//指派class_id
	$smarty->assign("stu_sn",$stu_sn);//指派student_sn
	}
$smarty->assign("template_dir",$template_dir);
$smarty->assign("PHP_SELF",$_SERVER['PHP_SELF']);
$smarty->display("$template_dir/fix_score.htm");
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

function backinput($st="查無該學期平時成績表資料!按下後返回!") {
echo"<BR><BR><CENTER><form>
	<input type='button' name='b1' value='$st' onclick=\"history.back()\" style='font-size:16pt;color:red'>
	</form></CENTER>";
	}


