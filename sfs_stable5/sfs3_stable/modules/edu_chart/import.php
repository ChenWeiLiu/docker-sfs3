<?php
//$Id: import.php 6149 2010-09-14 03:49:12Z brucelyc $

include "config.php";

//認證
sfs_check();

$sel_year=curr_year();
$sel_seme=curr_seme();
if ($_POST[do_key]) {
	//檔案上傳
	$path_str = "temp/";
	set_upload_path($path_str);
	$temp_path = $UPLOAD_PATH.$path_str;
	$file_name=$temp_path."student.csv";
	if ($_FILES['upload_file']['size']>0){
		copy($_FILES['upload_file']['tmp_name'],$file_name);
	}
	//取出 csv 的值
	$fd=fopen($file_name,"r");
	$i=0;
	while ($tt = sfs_fgetcsv ($fd, 2000, ",")) {
		if ($i==0) $d=chk_data($tt);
		if ($i>1 && $d[0]>=0 && $tt[$d[0]]!="") {
			$id=strtoupper(trim($tt[$d[0]]));
			$fword=substr($id,0,1);
			if (strlen($id)==10 && $fword >= "A" && $fword <= "Z") {
				$query="select * from stud_base where stud_person_id='$id' and stud_study_cond<>'5'";
			} else {
				$query="select * from stud_base where stud_id='$id' and stud_study_cond<>'5'";
			}

			$res=$CONN->Execute($query);
			$student_sn=$res->fields[student_sn];
			$v=intval($tt[$d[1]]);
			if ($v>0) $v*=10;
			$query="replace into health_sight (year,semester,student_sn,side,sight_o) values ('$sel_year','$sel_seme','$student_sn','r','".$v."')";
			$CONN->Execute($query);
			$v=intval($tt[$d[2]]);
			if ($v>0) $v*=10;
			$query="replace into health_sight (year,semester,student_sn,side,sight_o) values ('$sel_year','$sel_seme','$student_sn','l','".$v."')";
			$CONN->Execute($query);
		}
		$i++;
	}
}

//資料選單
$sel1 = new drop_select();
$sel1->s_name="data_id";
$sel1->id= $data_id;
$sel1->arr = array("0"=>"學生CSV檔");
$sel1->has_empty = false;
$sel1->is_submit = true;
$smarty->assign("data_sel",$sel1->get_select());

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
$smarty->assign("SFS_PATH_HTML",$SFS_PATH_HTML);
$smarty->assign("module_name","資料匯入");
$smarty->assign("SFS_MENU",$school_menu_p);
$smarty->display("edu_chart_import.tpl");

function chk_data($kk) {
	reset($kk);
	while (list($k,$v)=each($kk)) {
		switch ($v) {
			case "學號":
				$vs[0]=$k;
				break;
			case "右眼裸視視力":
				$vs[1]=$k;
				break;
			case "左眼裸視視力":
				$vs[2]=$k;
				break;
			default:
				break;
		}
	}
	return $vs;
}
?>
