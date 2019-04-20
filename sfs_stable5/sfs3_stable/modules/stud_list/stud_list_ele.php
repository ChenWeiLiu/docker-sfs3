<?php
//$Id: stud_list_ele.php 7764 2013-11-14 08:07:31Z smallduh $
include "config.php";

//認證
sfs_check();

//取得分組班班組
$query="select * from score_ss where year='".curr_year()."' and semester='".curr_seme()."' and enable='1'";
$res=$CONN->Execute($query);
while(!$res->EOF) {
	$all_ss_id.="'".$res->fields[ss_id]."',";
	$res->MoveNext();
}
if ($all_ss_id) $all_ss_id=substr($all_ss_id,0,-1);
$sql_sub="select * from elective_tea where ss_id in ($all_ss_id) order by group_name";
$rs_sub=$CONN->Execute($sql_sub);
while(!$rs_sub->EOF){
	$class_arr[$rs_sub->fields['group_id']]=$rs_sub->fields['group_name'];
	$rs_sub->MoveNext();
}

if ($_POST[print_out] || $_POST[csv_out]) {
	$seme_year_seme=sprintf("%03d",curr_year()).curr_seme();
	$max_num=0;
	reset($_POST[c_id]);
	while(list($k,$v)=each($_POST[c_id])) {
		$query="select a.*,b.stud_name,b.stud_id,b.curr_class_num from elective_stu a,stud_base b where a.student_sn=b.student_sn and a.group_id='$v' and b.stud_study_cond in ('0','15') order by b.curr_class_num";
		$res=$CONN->Execute($query);
		$i=1;
		while(!$res->EOF) {
			$curr_class_num=$res->fields[curr_class_num];
			$data_arr[$k][$i][stud_name]=$res->fields[stud_name];
			$data_arr[$k][$i][stud_id]=$res->fields[stud_id];
			$data_arr[$k][$i][oth]=substr($curr_class_num,-4,2)."-".substr($curr_class_num,-2,2);
			$i++;
			$res->MoveNext();
		}
		if ($max_num<intval($i-1)) $max_num=intval($i-1);
	}
}
$max_num=(ceil($max_num / 5)+1)*5;

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
$smarty->assign("module_name","學生名條");
$smarty->assign("SFS_MENU",$menu_p);
$smarty->assign("class_arr",$class_arr);
if ($_POST[csv_out]) {
	header("Content-disposition: filename=stud_list.csv");
	header("Content-type: application/octetstream ; Charset=Big5");
	//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
	header("Expires: 0");
}
if ($_POST[print_out] || $_POST[csv_out]) {
	$smarty->assign("sel_year",curr_year());
	$smarty->assign("sel_seme",curr_seme());
	$smarty->assign("max_num",$max_num);
	$smarty->assign("data_arr",$data_arr);
	$smarty->assign("oth_str","原班");
	if ($_POST[csv_out])
		$smarty->display("stud_list_stud_list_csv.tpl");
	else
		$smarty->display("stud_list_stud_list_print.tpl");
} else {
	$smarty->assign("sex_sel",0);
	$smarty->display("stud_list_stud_list.tpl");
}
?>
