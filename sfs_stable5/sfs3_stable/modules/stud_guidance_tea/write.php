<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="big5">
</head>
<body>
<?php
//$Id: write.php 9026 2017-01-05 07:23:54Z igogo $
include_once "config.php";
sfs_check();

$SQL="select st_sn,guid_tea_sn from stud_guid where guid_tea_sn='$_SESSION[session_tea_sn]' and  guid_c_isover ='0' ";
$rs = $CONN->Execute($SQL) or die($SQL);
$All_ss=$rs->GetArray();
if ($rs->RecordCount()==0) backe("�S���z�t�d�{�����ǥ͡I");

if ($_POST[act]=="write"){
	$flag=check_gui($_POST[guid_c_id],$_SESSION[session_tea_sn]);
	if ($flag!='Yes')  backe("�o�ӭӮרëD�ѱz�{���I");
	($_POST[guid_c_isover]=='0') ? $end_date='0000-00-00':$end_date=date("Y-m-d");
	if ($_POST[guid_c_isover]=='1' && $_POST[guid_over_reason]=='')  backe("���׭�]����g�I");
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
	if ($flag!='Yes')  backe("�o�ӭӮרëD�ѱz�{���I");
	if ($_POST[guid_kind]=='0' ||$_POST[guid_l_con]=='') backe("�ж�g����I");
	$now_t=date("Y-m-d H:i:s");
	$l_date=$_POST[Date_Year]."-".$_POST[Date_Month]."-".$_POST[Date_Day]." ".$_POST[Time_Hour].":".$_POST[Time_Minute].":00";
	$SQL="insert into stud_guid_event(guid_c_id,guid_l_date,guid_kind,tutor,guid_l_con,update_id) values('$_POST[guid_c_id]','$l_date','$_POST[guid_kind]','$_SESSION[session_tea_sn]','".$_POST[guid_l_con]."','$_SESSION[session_tea_sn]') ";
	$rs = $CONN->Execute($SQL) or die($SQL);
	$URL=$_SERVER[PHP_SELF]."?act=event&guid=".$_POST[guid_c_id];
	header("Location:$URL");
}

if ($_POST[act]=="write_event_updata"){
	$flag=check_gui($_POST[guid_c_id],$_SESSION[session_tea_sn]);
	if ($flag!='Yes')  backe("�o�ӭӮרëD�ѱz�{���I");
	if ($_POST[guid_kind]=='0' ||$_POST[guid_l_con]=='') backe("�ж�g����I");
	$now_t=date("Y-m-d H:i:s");
	$l_date=$_POST[Date_Year]."-".$_POST[Date_Month]."-".$_POST[Date_Day]." ".$_POST[Time_Hour].":".$_POST[Time_Minute].":00";
	$SQL="update stud_guid_event set guid_l_date='$l_date',guid_kind='$_POST[guid_kind]',guid_l_con='$_POST[guid_l_con]',update_id='$_SESSION[session_tea_sn]' where  guid_l_id='$_POST[guid_l_id]' and guid_c_id ='$_POST[guid_c_id]' ";
	$rs = $CONN->Execute($SQL) or die($SQL);
	$URL=$_SERVER[PHP_SELF]."?act=event&guid=".$_POST[guid_c_id];
	header("Location:$URL");
}

if ($_GET[act]=="del" && $_GET[del]!='' ){
	$flag=check_event($_GET[del],$_SESSION[session_tea_sn]);
	if ($flag!='Yes')  backe("�o����ƨëD�ѱz��g���I");
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
$all_tea=get_tea_data();//�����Юv�}�C



head("�ӧO���ɬ����U");
print_menu($school_menu_p);
myheader();
	$sel_tea=get_tea_sel();
	$stud_list=get_stu_list($Seme,$_SESSION[session_tea_sn]);
	$smarty->assign("stud_list", $stud_list);//�ǥͦC���}�C
	$smarty->assign("SEX",$SEX);//�ʧO�ϥ�
if ($_GET[guid]!='' && $_GET[act]=='base') {
	$g_stu=get_stu_gui($_GET[guid],$_SESSION[session_tea_sn]);//�ǥͻ��ɪ����

	$smarty->assign("teach",$all_tea);
	$the_stu=get_stu_data($g_stu[st_sn],$Seme);//���o�ǥͰ򥻸��
	$smarty->assign("PHP_SELF",$_SERVER[PHP_SELF]);

	$birth_state=birth_state();//���o�y�e�}�C
	$smarty->assign("question_kind",$question_kind);//���ɰ��D�����}�C
//	$smarty->assign("c_from",$come_from);//�श�ӷ��}�C
	$smarty->assign("SEX",$SEX);//�ʧO�ϥ�
	$smarty->assign("sel_tea",$sel_tea);//�Юv�U�Կ��
	$smarty->assign("c_isover",$guid_over);//�Юv�U�Կ��
	$smarty->assign("birth_state",$birth_state);//�y�e�}�C
	$smarty->assign("gui_stu",$g_stu);//�ǥͻ��ɪ����
	$smarty->assign("the_stu",$the_stu);//�ǥͰ򥻸��
	$smarty->assign("tpl_file",$tpl_file2);//�ǥͰ򥻸��
}
if ($_GET[guid]!='' && $_GET[act]=='event') {

	if($_GET[edit]!=''){
		$event_one=get_event_one($_GET[edit],$_SESSION[session_tea_sn]);
		}
	$g_stu=get_stu_gui($_GET[guid],$_SESSION[session_tea_sn]);//�ǥͻ��ɪ����
	$event_all=get_event_all($_GET[guid],$_SESSION[session_tea_sn]);//�ǥͻ��ɸg�L�O�������
	$the_stu=get_stu_data($g_stu[st_sn],$Seme);//���o�ǥͰ򥻸��
	$smarty->assign("PHP_SELF",$_SERVER[PHP_SELF]);
//	$birth_state=birth_state();//���o�y�e�}�C
//	$smarty->assign("question_kind",$question_kind);//���ɰ��D�����}�C
//	$smarty->assign("c_from",$come_from);//�श�ӷ��}�C
	$smarty->assign("SEX",$SEX);//�ʧO�ϥ�
	
	//$smarty->assign("talk_gui_stud",$talk_gui_stud);//�ǤJ���ɤ覡��ư}�C
	//��覡�L�k���T��ܤ���  �אּ���nSELECT�A�ǤJ
	$temp_select="<select name='guid_kind'>";
	foreach($talk_gui_stud as $key=>$value){
		$selected=($key==$event_one['guid_kind'])?'selected':'';
		$temp_select.="<option value='$key' $selected>$value</option>";	
	}
	$temp_select.="</select>";
	$smarty->assign("temp_select",$temp_select);	

	$smarty->assign("event_all",$event_all);//�Юv�U�Կ��
	$smarty->assign("event_one",$event_one);//�Юv�U�Կ��
	$smarty->assign("event_time",$event_time);//�Юv�U�Կ��
//	$smarty->assign("birth_state",$birth_state);//�y�e�}�C
//	$smarty->assign("gui_stu",$g_stu);//�ǥͻ��ɪ����
	$smarty->assign("the_stu",$the_stu);//�ǥͰ򥻸��
	$smarty->assign("tpl_file",$tpl_file3);//�ǥͰ򥻸��
}

	$smarty->display($tpl_file1);

//print_r($the_stu);


//print_r($stud_list);
foot();
?>
</body>