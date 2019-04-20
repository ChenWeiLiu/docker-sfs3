<?php
//$Id: stud_move_new.php 6029 2010-08-24 16:31:19Z brucelyc $
include "stud_move_config.php";

//認證
sfs_check();

$m_arr = get_sfs_module_set();
extract($m_arr, EXTR_OVERWRITE);

$_POST[year_id]=intval($_POST[year_id]);
if ($_GET[do_key]=="edit")
		$year_id=sprintf("%03d",$_GET[year]);
elseif (empty($_POST[year_id]))
	$year_id=sprintf("%03d",curr_year());
else
	$year_id=sprintf("%03d",$_POST[year_id]);

if ($IS_JHORES)
	$class_year_id=7;
else
	$class_year_id=1;

$year_arr=get_class_year();
$curr_seme=intval($year_id."1");
$stud_study_year=intval($year_id)-intval($class_year_id)+1+$IS_JHORES;

//取出所有轉入生記錄
$query="select * from stud_move where move_kind='2'";
$res=$CONN->Execute($query);		
$all_sn="";
while(!$res->EOF) {
	$all_sn.="'".$res->fields[student_sn]."',";
	$res->MoveNext();
}
if (!empty($all_sn)) $all_sn="and a.student_sn not in (".substr($all_sn,0,-1).")";

//按鍵處理
switch($_REQUEST[do_key]) {
	case $newBtn :
		$update_ip = getip();
		//加入異動記錄
		$CONN->Execute("delete from stud_move where move_kind='13' and move_year_seme='$curr_seme'") or die("error");
		$query="select a.stud_id,a.student_sn from stud_base a right join stud_seme b on a.student_sn=b.student_sn where a.stud_study_year='$stud_study_year' and b.seme_year_seme='".$year_id."1' $all_sn"; 
		$res=$CONN->Execute($query);
		while(!$res->EOF) {
			$sql_insert="insert into stud_move (stud_id,move_kind,move_year_seme,move_date,move_c_unit,move_c_date,move_c_word,move_c_num,update_id,update_ip,update_time,student_sn) values ('".$res->fields[stud_id]."','13','$curr_seme','$_POST[move_date]','$_POST[move_c_unit]','$_POST[move_c_date]','$_POST[move_c_word]','$_POST[move_c_num]','$_SESSION[session_log_id]','$update_ip','".date("Y-m-d G:i:s")."','".$res->fields[student_sn]."')";
			$CONN->Execute($sql_insert) or die($sql_insert);
			$res->MoveNext();
		}
	break;

	case "delete" :
		$query ="delete from stud_move where move_year_seme='$_GET[move_year_seme]' and move_kind='13'";
		$CONN->Execute($query)or die ($query);
	break;

	case "edit" :
		$default_unit=$_GET[unit];
		$default_word=$_GET[word];
		$smarty->assign("default_date",$_GET[date]);
		$smarty->assign("default_c_date",$_GET[c_date]);
		$smarty->assign("default_c_num",$_GET[num]);
	break;
}

//學年選單
$sel1 = new drop_select();
$sel1->s_name="year_id";
$sel1->id= $year_id;
$sel1->arr = $year_arr;
$sel1->has_empty = false;
$sel1->is_submit = true;
$smarty->assign("year_id_sel",$sel1->get_select());

//新生人數統計
$query="select count(a.stud_sex) as tol ,sum(a.stud_sex=1) as boy ,sum(a.stud_sex=2) as girl from stud_base a right join stud_seme b on a.student_sn=b.student_sn where a.stud_study_year='$stud_study_year' and b.seme_year_seme='".$year_id."1' $all_sn";
$res=$CONN->Execute($query);		
$smarty->assign("tol",$res->fields[tol]);
$smarty->assign("boy",$res->fields[boy]);
$smarty->assign("girl",$res->fields[girl]);

//取出所有記錄
$query="select distinct concat(move_c_unit,move_c_word,move_c_num) as dif,count(move_id) as num,left(move_year_seme,length(move_year_seme)-1) as move_year,move_year_seme,move_date,move_c_unit,move_c_date,move_c_word,move_c_num from stud_move where move_kind='13' group by move_year_seme order by move_date desc";
$res=$CONN->Execute($query) or die($query);
$smarty->assign("stud_move",$res->GetRows());

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
$smarty->assign("module_name","新生入學作業");
$smarty->assign("SFS_MENU",$student_menu_p);
$smarty->assign("default_unit",$default_unit);
$smarty->assign("default_word",$default_word);
$smarty->assign("curr_year",curr_year());
$smarty->display("stud_move_stud_move_new.tpl");
?>
