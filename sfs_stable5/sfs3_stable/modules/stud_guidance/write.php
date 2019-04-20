<?php
//$Id: write.php 5310 2009-01-10 07:57:56Z hami $
include_once "config.php";
sfs_check();

$SQL="select st_sn,guid_tea_sn from stud_guid where guid_tea_sn='$_SESSION[session_tea_sn]' and  guid_c_isover='0'  ";
$rs = $CONN->Execute($SQL) or die($SQL);
$All_ss=$rs->GetArray();
if ($rs->RecordCount()==0) backe("沒有您負責認輔的學生！");

if ($_POST[act]=="write"){
	$flag=check_gui($_POST[guid_c_id],$_SESSION[session_tea_sn]);
	if ($flag!='Yes')  backe("這個個案並非由您認輔！");
	($_POST[guid_c_isover]=='0') ? $end_date='0000-00-00':$end_date=date("Y-m-d");
	if ($_POST[guid_c_isover]=='1' && $_POST[guid_over_reason]=='')  backe("結案原因未填寫！");
	$guid_c_kind=join(',',$_POST[guid_c_kind]);


	$now_t=date("Y-m-d H:i:s");
	$SQL="update stud_guid set
begin_date='$_POST[begin_date]',
guid_c_kind='$guid_c_kind',
guid_c_behave='$_POST[guid_c_behave]',
about_man='$_POST[about_man]',
relation='$_POST[relation]',
ab_man_addr='$_POST[ab_man_addr]',
ab_man_tel='$_POST[ab_man_tel]',
guid_c_isover='$_POST[guid_c_isover]',
update_id='$_SESSION[session_tea_sn]' ,
end_date='$end_date',
update_time='$now_t',

guid_begin_why='$_POST[guid_begin_why]',
guid_c_reason='$_POST[guid_c_reason]',
about_home='$_POST[about_home]',
about_school='$_POST[about_school]',
person_specific='$_POST[person_specific]',
about_oth='$_POST[about_oth]',
st_analysis='$_POST[st_analysis]',
guidance_method='$_POST[guidance_method]',
st_oth_record='$_POST[st_oth_record]',
guid_over_reason='$_POST[guid_over_reason]' 
where guid_c_id='$_POST[guid_c_id]' ";
	$rs = $CONN->Execute($SQL) or die($SQL);
	$URL=$_SERVER[PHP_SELF]."?act=base&guid=".$_POST[guid_c_id];
	header("Location:$URL");
}
if ($_POST[act]=="write_event"){
	$flag=check_gui($_POST[guid_c_id],$_SESSION[session_tea_sn]);
	if ($flag!='Yes')  backe("這個個案並非由您認輔！");
	if ($_POST[guid_kind]=='0' ||$_POST[guid_l_con]=='') backe("請填寫完整！");
	$now_t=date("Y-m-d H:i:s");
	$l_date=$_POST[Date_Year]."-".$_POST[Date_Month]."-".$_POST[Date_Day]." ".$_POST[Time_Hour].":".$_POST[Time_Minute].":00";
	$SQL="insert into stud_guid_event(guid_c_id,guid_l_date,guid_kind,tutor,guid_l_con,update_id) values('$_POST[guid_c_id]','$l_date','$_POST[guid_kind]','$_SESSION[session_tea_sn]','".$_POST[guid_l_con]."','$_SESSION[session_tea_sn]') ";
	$rs = $CONN->Execute($SQL) or die($SQL);
	$URL=$_SERVER[PHP_SELF]."?act=event&guid=".$_POST[guid_c_id];
	header("Location:$URL");
}

if ($_POST[act]=="write_event_updata"){
	$flag=check_gui($_POST[guid_c_id],$_SESSION[session_tea_sn]);
	if ($flag!='Yes')  backe("這個個案並非由您認輔！");
	if ($_POST[guid_kind]=='0' ||$_POST[guid_l_con]=='') backe("請填寫完整！");
	$now_t=date("Y-m-d H:i:s");
	$l_date=$_POST[Date_Year]."-".$_POST[Date_Month]."-".$_POST[Date_Day]." ".$_POST[Time_Hour].":".$_POST[Time_Minute].":00";
	$SQL="update stud_guid_event set guid_l_date='$l_date',guid_kind='$_POST[guid_kind]',guid_l_con='$_POST[guid_l_con]',update_id='$_SESSION[session_tea_sn]' where  guid_l_id='$_POST[guid_l_id]' and guid_c_id ='$_POST[guid_c_id]' ";
	$rs = $CONN->Execute($SQL) or die($SQL);
	$URL=$_SERVER[PHP_SELF]."?act=event&guid=".$_POST[guid_c_id];
	header("Location:$URL");
}

if ($_GET[act]=="del" && $_GET[del]!='' ){
	$flag=check_event($_GET[del],$_SESSION[session_tea_sn]);
	if ($flag!='Yes')  backe("這筆資料並非由您填寫的！");
	$SQL="delete from stud_guid_event where guid_l_id ='$_GET[del]' and tutor ='$_SESSION[session_tea_sn]'";
	$rs = $CONN->Execute($SQL) or die($SQL);
	$URL=$_SERVER[PHP_SELF]."?act=event&guid=".$_GET[guid];
	header("Location:$URL");
}

$template_dir = $SFS_PATH."/".get_store_path()."/templates/";
$tpl_file1=$template_dir."work_a.htm";
$tpl_file2=$template_dir."work_b.htm";
$tpl_file3=$template_dir."work_c.htm";
$smarty->left_delimiter="{{";
$smarty->right_delimiter="}}";

($_GET[Seme]!='') ? $Seme=$_GET[Seme]:$Seme=sprintf("%03d",curr_year()).curr_seme();

$SEX=array(1=>"<img src=images/boy.gif height=25>",2=>"<img src=images/girl.gif height=25>");
$stud_coud=study_cond();
$sch_data=get_school_base();
$all_tea=get_tea_data();//全部教師陣列



head("個別輔導紀錄");
print_menu($school_menu_p);
myheader();
	$sel_tea=get_tea_sel();
	$stud_list=get_stu_list($Seme,$_SESSION[session_tea_sn]);
	$smarty->assign("stud_list", $stud_list);//學生列表陣列
	$smarty->assign("SEX",$SEX);//性別圖示
if ($_GET[guid]!='' && $_GET[act]=='base') {
	$g_stu=get_stu_gui($_GET[guid],$_SESSION[session_tea_sn]);//學生輔導表資料

	$smarty->assign("teach",$all_tea);
	$the_stu=get_stu_data($g_stu[st_sn],$Seme);//取得學生基本資料
	$smarty->assign("PHP_SELF",$_SERVER[PHP_SELF]);

	$birth_state=birth_state();//取得籍貫陣列
	$smarty->assign("question_kind",$question_kind);//輔導問題類型陣列
//	$smarty->assign("c_from",$come_from);//轉介來源陣列
	$smarty->assign("SEX",$SEX);//性別圖示
	$smarty->assign("sel_tea",$sel_tea);//教師下拉選單
	$smarty->assign("c_isover",$guid_over);//教師下拉選單
	$smarty->assign("birth_state",$birth_state);//籍貫陣列
	$smarty->assign("gui_stu",$g_stu);//學生輔導表資料
	$smarty->assign("the_stu",$the_stu);//學生基本資料
	$smarty->assign("tpl_file",$tpl_file2);//學生基本資料
}
if ($_GET[guid]!='' && $_GET[act]=='event') {

	if($_GET[edit]!=''){
		$event_one=get_event_one($_GET[edit],$_SESSION[session_tea_sn]);
		}
	$g_stu=get_stu_gui($_GET[guid],$_SESSION[session_tea_sn]);//學生輔導表資料
	$event_all=get_event_all($_GET[guid],$_SESSION[session_tea_sn]);//學生輔導經過記錄表資料
	$the_stu=get_stu_data($g_stu[st_sn],$Seme);//取得學生基本資料
	$smarty->assign("PHP_SELF",$_SERVER[PHP_SELF]);
//	$birth_state=birth_state();//取得籍貫陣列
//	$smarty->assign("question_kind",$question_kind);//輔導問題類型陣列
//	$smarty->assign("c_from",$come_from);//轉介來源陣列
	$smarty->assign("SEX",$SEX);//性別圖示
	$smarty->assign("talk_gui_stud",$talk_gui_stud);//傳入輔導方式資料陣列
	$smarty->assign("event_all",$event_all);//教師下拉選單
	$smarty->assign("event_one",$event_one);//教師下拉選單
	$smarty->assign("event_time",$event_time);//教師下拉選單
//	$smarty->assign("birth_state",$birth_state);//籍貫陣列
//	$smarty->assign("gui_stu",$g_stu);//學生輔導表資料
	$smarty->assign("the_stu",$the_stu);//學生基本資料
	$smarty->assign("tpl_file",$tpl_file3);//學生基本資料
}

	$smarty->display($tpl_file1);

//print_r($the_stu);


//print_r($stud_list);
foot();
?>
