<?php
//$Id: guid_prt.php 7273 2013-04-23 08:14:38Z infodaes $
include_once "config.php";
sfs_check();
$template_dir = $SFS_PATH."/".get_store_path()."/templates/";
$smarty->left_delimiter="{{";
$smarty->right_delimiter="}}";

###--- 列印輔導基本資料 ----#####
if ($_GET[kind]=='base'){

$tpl_file=$template_dir."ps_guid.htm";//樣本檔
$SQL="select * from stud_guid where  guid_c_id='$_GET[guid]' ";
$rs = $CONN->Execute($SQL) or die($SQL);
if ($rs ) $the_stu = get_object_vars($rs->FetchNextObject(false));
//$tmp_stu=$rs->GetArray();
//$the_stu=$tmp_stu[0];
if ($rs->RecordCount()==0)  backend("沒有資料！");;
//echo $the_stu[st_sn];
($_GET[Seme]!='') ? $Seme=$_GET[Seme]:$Seme=sprintf("%03d",curr_year()).curr_seme();
$the_stu_base=get_stu_data($the_stu[st_sn], $Seme);
$all_tea=get_tea_data();//全部教師陣列
$SEX=array(1=>"男",2=>"女");
$birth_state=birth_state();//取得籍貫陣列
$smarty->assign("stud",$the_stu);
$smarty->assign("base",$the_stu_base);
$smarty->assign("teach",$all_tea);
$smarty->assign("SEX",$SEX);
$smarty->assign("place",$birth_state);//籍貫陣列
$smarty->display($tpl_file);
}

###--- 列印輔導記錄 ----#####
if ($_GET[kind]=='REC'){

	$tpl_file=$template_dir."ps_guid_rec.htm";//樣本檔
	$SQL="select * from stud_guid_event where  guid_c_id='$_GET[guid]' order by guid_l_date";
	$rs = $CONN->Execute($SQL) or die($SQL);
if ($rs->RecordCount()==0) backend("沒有資料！");

	$the_rec=$rs->GetArray();
	$smarty->assign("tkind",$talk_gui_stud);
	
	$smarty->assign("the_rec",$the_rec);
	$smarty->display($tpl_file);
	
	

}
?>
