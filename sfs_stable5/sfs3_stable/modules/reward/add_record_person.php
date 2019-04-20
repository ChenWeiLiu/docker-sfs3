<?php
//$Id: add_record_person.php 6920 2012-10-01 08:25:16Z infodaes $
include "config.php";
include "../../include/sfs_case_dataarray.php";

//認證
sfs_check();
//print_r($_POST);
if ($_POST['student_sn']=="" && $_POST['stud_id']) {
	$stud_id=$_POST['stud_id'];
	$query="select * from stud_base where (stud_id='$stud_id' or stud_person_id='$stud_id') order by stud_study_year";
	$res=$CONN->Execute($query);
	$temp_arr=array();
	$i=0;
	while(!$res->EOF) {
		$temp_arr[$i]['student_sn']=$res->fields['student_sn'];
		$temp_arr[$i]['stud_name']=$res->fields['stud_name'];
		$temp_arr[$i]['stud_sex']=$res->fields['stud_sex'];
		$temp_arr[$i]['stud_study_year']=$res->fields['stud_study_year'];
		$temp_arr[$i]['stud_study_cond']=$res->fields['stud_study_cond'];
		$i++;
		$res->MoveNext();
	}
	$smarty->assign("stud_rows",$temp_arr);
	$smarty->assign("stud_nums",count($temp_arr));
} elseif ($_POST['student_sn']) {
	$query="select * from stud_base where student_sn='$_POST[student_sn]'";
	$res=$CONN->Execute($query);
	$smarty->assign("stud_name",$res->fields[stud_name]);
	$smarty->assign("stud_study_cond",$res->fields[stud_study_cond]);
	$stud_id=$res->fields['stud_id'];

	if ($_POST['sure']) {
		while(list($seme_year_seme,$v)=each($_POST[reward_data])) {
			reset($v);
			while(list($reward_kind,$vv)=each($v)) {
				if ($vv!="") {
					$CONN->Execute("replace into stud_seme_rew (seme_year_seme,stud_id,sr_kind_id,sr_num) values ('$seme_year_seme','$stud_id','$reward_kind','$vv')");
					echo "replace into stud_seme_rew (seme_year_seme,stud_id,sr_kind_id,sr_num) values ('$seme_year_seme','$stud_id','$reward_kind','$vv')<br>";
				}
				
			}
		}
	}

	$max_year=($IS_JHORES==0)?6:3;
	$reward_kind_arr=stud_rep_kind();
	$rowdata=array();
	$all_seme=array();
	for($i=0;$i<$max_year;$i++) {
		for($j=1;$j<=2;$j++) {
			reset($reward_kind_arr);
			while(list($k,$v)=each($reward_kind_arr)) {
				$seme=sprintf("%03d",($res->fields[stud_study_year]+$i)).$j;
				$all_seme[]=$seme;
				$rowdata[$seme][$k]="";
			}
		}
	}
	if (count($all_seme)>0) $all_seme_str="'".implode("','",$all_seme)."'";

	$query="select * from stud_seme_rew where stud_id='$stud_id' and seme_year_seme in ($all_seme_str) order by seme_year_seme";
	$res=$CONN->Execute($query);
	while(!$res->EOF) {
		$rowdata[$res->fields[seme_year_seme]][$res->fields[sr_kind_id]]=$res->fields[sr_num];
		$res->MoveNext();
	}
	$smarty->assign("rowdata",$rowdata);
	$smarty->assign("reward_data",$reward_data);
	$smarty->assign("reward_kind",stud_rep_kind());
}


//取得學年學期
$year_seme=$_REQUEST[year_seme];
if ($year_seme) {
	$sel_year=intval(substr($year_seme,0,3));
	$sel_seme=substr($year_seme,3,1);
} else {
	$sel_year=(empty($_REQUEST[sel_year]))?curr_year():$_REQUEST[sel_year];
	$sel_seme=(empty($_REQUEST[sel_seme]))?curr_seme():$_REQUEST[sel_seme];
}
$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;

//學年學期選單
	$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
	$year_seme_p=get_class_seme();
	$year_seme_select = "<select name='year_seme' onchange='this.form.submit()';>\n";
	while (list($k,$v)=each($year_seme_p)){
		if ($seme_year_seme==$k)
	      		$year_seme_select.="<option value='$k' selected>$v</option>\n";
	      	else
	      		$year_seme_select.="<option value='$k'>$v</option>\n";
	}
	$year_seme_select.= "</select>"; 

	
//年級與班級選單
$default_class_id=($IS_JHORES+1).'01';
$class_id=$_POST[class_id]?$_POST[class_id]:$default_class_id;
$_POST['stud_id']=$_POST['stud_id_select']?$_POST['stud_id_select']:$_POST['stud_id'];
$class_select=&classSelect($sel_year,$sel_seme,"","class_id",$class_id,true);
$stud_select=get_stud_select($class_id,$One,"stud_id_select","",1);

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
$smarty->assign("module_name","個人學期獎懲補登");
$smarty->assign("SFS_MENU",$student_menu_p);
$smarty->assign("study_cond",study_cond());
$smarty->assign("year_seme_select",$year_seme_select);
$smarty->assign("stud_select",$stud_select);
$smarty->assign("class_select",$class_select);
$smarty->display("reward_add_record_person.tpl");
?>
