<?php
//$Id: grad_word.php 8399 2015-04-22 07:14:09Z chiming $
include "config.php";
include "../../include/sfs_case_score.php";

//認證
sfs_check();

$year_seme=$_POST[year_seme];
if (empty($year_seme)) {
	$sel_year = curr_year(); //目前學年
	$sel_seme = curr_seme(); //目前學期
	$year_seme=$sel_year."_".$sel_seme;
} else {
	$ys=explode("_",$year_seme);
	$sel_year=$ys[0];
	$sel_seme=$ys[1];
}

$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;

if ($_POST[save]) {
	$sd=$_POST[stud_id];
	$nsc=$_POST[seme_class];
	$gd=$_POST[P_date];
	$gw=$_POST[P_word];
	$gn=$_POST[P_num];
	reset($_POST[sure_grad]);
	while(list($k,$v)=each($_POST[sure_grad])) {
		$query="select * from grad_stud where stud_grad_year='$sel_year' and stud_id='$sd[$k]'";
		$res=$CONN->Execute($query) or die("SQL執行錯誤： $query");
		$gsn=$res->fields[grad_sn];
		$scy=substr($nsc[$k],0,-2);
		$scn=intval(substr($nsc[$k],-2,2));
		$dd=explode("-",$gd[$k]);
		if ($dd[0]<1980) $dd[0]+=1911;
		$gd[$k]=implode("-",$dd);
		if ($gsn!="")
			$query="update grad_stud set class_year='$scy',class_sort='$scn',stud_id='$sd[$k]',grad_kind='$v',grad_date='$gd[$k]',grad_word='$gw[$k]',grad_num='$gn[$k]' where grad_sn='$gsn'";
		else
			$query="insert into grad_stud (stud_grad_year,class_year,class_sort,stud_id,grad_kind,grad_date,grad_word,grad_num) values ('$sel_year','$scy','$scn','$sd[$k]','$v','$gd[$k]','$gw[$k]','$gn[$k]')";
		$CONN->Execute($query) or die("SQL執行錯誤： $query");
	}
}

if ($_POST[year_name]) {
	if ($_POST[show_kind]!="")
		$kind_str="and c.grad_kind='$_POST[show_kind]'";
	else
		$kind_str="";
	$seme_class=array();
	$seme_class_str="and a.seme_class ".(($_POST[me])?"= '".$_POST[year_name].sprintf("%02d",$_POST[me]):"like '".$_POST[year_name]."%")."'";
	$query="select a.student_sn,a.stud_id,b.stud_name,a.seme_class,a.seme_num from stud_seme a,stud_base b left join grad_stud c on b.stud_id=c.stud_id where a.seme_year_seme='$seme_year_seme' and a.student_sn=b.student_sn and b.stud_study_cond in ('0','5','15') $kind_str $seme_class_str order by a.seme_class,a.seme_num";
	$res=$CONN->Execute($query) or die("SQL執行錯誤： $query");
	while(!$res->EOF) {
		$sn=$res->fields[student_sn];
		$student_sn[$res->fields[stud_id]]=$sn;
		$stud_id[$sn]=$res->fields[stud_id];
		$stud_name[$sn]=$res->fields[stud_name];
		$seme_class[$sn]=$res->fields[seme_class];
		$seme_num[$sn]=$res->fields[seme_num];
		$res->MoveNext();
	}
	$all_id="('".implode("','",$stud_id)."')";
	$query="select * from grad_stud where stud_grad_year='$sel_year' and stud_id in $all_id";
	$res=$CONN->Execute($query) or die("SQL執行錯誤： $query");
	while(!$res->EOF) {
		$sn=$student_sn[$res->fields[stud_id]];
		$grad_kind[$sn]=$res->fields[grad_kind];
		$P_date[$sn]=$res->fields[grad_date];
		$P_word[$sn]=$res->fields[grad_word];
		$P_num[$sn]=$res->fields[grad_num];
		$grad_score[$sn]=$res->fields[grad_score];
		$res->MoveNext();
	}
}

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE); 
$smarty->assign("module_name","畢業文字號"); 
$smarty->assign("SFS_MENU",$menu_p); 
$smarty->assign("year_seme_menu",year_seme_menu($sel_year,$sel_seme)); 
$smarty->assign("class_year_menu",class_year_menu($sel_year,$sel_seme,$_POST[year_name])); 
$smarty->assign("class_name_menu",class_name_menu($sel_year,$sel_seme,$_POST[year_name],$_POST[me])); 
$smarty->assign("student_sn",$student_sn); 
$smarty->assign("stud_id",$stud_id); 
$smarty->assign("stud_name",$stud_name); 
$smarty->assign("seme_class",$seme_class); 
$smarty->assign("seme_num",$seme_num); 
$smarty->assign("P_date",$P_date); 
$smarty->assign("P_word",$P_word); 
$smarty->assign("P_num",$P_num); 
$smarty->assign("grad_kind",$grad_kind); 
$smarty->assign("grad_score",$grad_score); 
$smarty->assign("grad_kind_arr",array(""=>"列出所有學生","1"=>"只列畢業學生","2"=>"只列修業學生")); 
$smarty->assign("class_base",class_base($seme_year_seme));
//20150422修正固定4碼問題彰化和東王麒富
$smarty->assign("grade_num_len","%0".$grade_num_len."d");

$smarty->display("stud_grad_grad_word.tpl");
?>