<?php
//$Id: chc_guidance.php 6914 2012-09-24 15:44:04Z infodaes $
//預設的引入檔，不可移除。
include "config.php";

if ($_POST[tea_sn] && $_POST[st_sn]&& $_POST[act]=='write') {
	$day=date("Y-m-d");
	$SQL="insert into stud_guid(st_sn,begin_date,guid_tea_sn) values ('$_POST[st_sn]','$day','$_POST[tea_sn]') ";
	
	$rs = $CONN->Execute($SQL) or die($SQL);
	$URL=$_SERVER[PHP_SELF]."?Seme=".$_POST[Seme]."&Sclass=".$_POST[Sclass];
	header("Location:$URL");
	}

sfs_check();
head("個別輔導紀錄");
print_menu($school_menu_p);

// smarty的一些設定  -----------------------------------

$template_dir = $SFS_PATH."/".get_store_path()."/templates/";
$tpl_file=$template_dir."chc_guidance.htm";
$smarty->left_delimiter="{{";
$smarty->right_delimiter="}}";

/////  以get方式取得 學年學期選項 ------------------
($_GET[Seme]!='') ? $Seme=$_GET[Seme]:$Seme=sprintf("%03d",curr_year()).curr_seme();
/////  以get方式取得 班級選項 ------------------
if($_GET[Sclass]!='') $Sclass=$_GET[Sclass];
/////  若有得到班級選項 則將值帶入選單中 使其 selected ------------------
($Sclass) ? $LINK=link_a($Seme,$Sclass): $LINK=link_a($Seme);
/////  設定一個陣列,將性別轉為圖檔 ------------------
$SEX=array(1=>"<img src=images/boy.gif height=25>",2=>"<img src=images/girl.gif height=25>");
//////  從SFS3內建的函式取學校資料函式---------------------
$sch_data=get_school_base();
$stud_coud=study_cond();


///// 印出年班級下拉選單 ------------------------------------
echo "
<TABLE border=0 width=100% style='font-size:11pt;'  cellspacing=1 cellpadding=0 bgcolor=#9EBCDD>
<TR bgcolor=#9EBCDD><FORM name=p2><TD>　學期:<INPUT TYPE='text' NAME='Seme' value='$Seme' size=6 class=ipmei><INPUT TYPE='submit' value='切換'>　　  $LINK
</TD></TR></FORM></TABLE>";

///// 切開由GET傳入的(學期_年班)變數,以取得年班值 ------------------------------------
	$Class=split("_",$Sclass);

///// 當學期及年班資料都有時,即執行sql以取得該學期所有學生資料-----------------------
if ($Sclass && $Seme){
	$SQL="select a.student_sn,a.stud_id,a.stud_name,a.stud_sex,a.stud_study_cond,b.seme_num from stud_base a,stud_seme b where a.student_sn=b.student_sn and b.seme_year_seme ='$Seme' and b.seme_class='".$Class[1]."' order by b.seme_num ";
//	$SQL1="select a.student_sn,a.stud_id,a.stud_name,a.stud_sex,a.stud_study_cond,b.seme_num, stud_guid.guid_tea_sn  	from stud_base a,stud_seme b  left join stud_guid on a.student_sn=stud_guid.st_sn where a.student_sn=b.student_sn and b.seme_year_seme ='$Seme' and b.seme_class='".$Class[1]."' order by b.seme_num ";
//	$SQL2="select a.student_sn,a.stud_id,a.stud_name,a.stud_sex,a.stud_study_cond,b.seme_num, stud_guid.guid_tea_sn  	from stud_base a,stud_seme b  left join stud_guid on student_sn=st_sn where a.student_sn=b.student_sn and b.seme_year_seme ='$Seme' and b.seme_class='".$Class[1]."' order by b.seme_num ";
	$rs=$CONN->Execute($SQL) or die("無法取得班級學生資料！<br>".$SQL); 
//	if (!$rs) $rs=$CONN->Execute($SQL2)or die("無法查詢，語法:".$SQL2); 
	 
	$All_stu=$rs->GetArray();
	foreach($All_stu as $key=>$value){
		//抓取認輔記錄
		$student_sn=$value['student_sn'];
		$sql="SELECT a.*,b.name FROM stud_guid a LEFT JOIN teacher_base b ON a.guid_tea_sn=b.teacher_sn WHERE a.st_sn=$student_sn order by begin_date";
		$rs=$CONN->Execute($sql) or die("無法取得學生認輔記錄資料！<br>".$sql);
		$guid_record='';
		while(!$rs->EOF){
			$guid_c_id=$rs->fields[guid_c_id];
			$begin_date=$rs->fields[begin_date];
			$end_date=$rs->fields[end_date];
			$guid_c_isover=$rs->fields[guid_c_isover];
			$name=$rs->fields[name];
			$font_color=$guid_c_isover?'#880088':'#ff0000';
			$guid_record.="<li><a href='./guid_prt.php?guid=$guid_c_id&kind=REC' target='rec_$guid_c_id'><font size=1 color='$font_color'>$begin_date~$end_date $name</font></a></li>";			
			$rs->MoveNext();
		}
		$All_stu[$key]['guid_record']=$guid_record;	
	}
}
//echo "<pre>";	
//print_r($All_stu);
//echo "</pre>";	


	$tea_all=get_tea_data();

///  若選擇學生,則進行學生資料擷取  ------------------------------------
if($_GET[st_sn]){
	$stud_data=get_stu_data($_GET[st_sn],$Seme);
	$smarty->assign("stu", $stud_data);
	$smarty->assign("PHP_SELF",$_SERVER[PHP_SELF]);
	}

// smarty 指派變數處理  -----------------------------------
$smarty->assign("tea_all",$tea_all);

$smarty->assign("sel_teach",$tea_all);
$smarty->assign("stud_coud",$stud_coud);
$smarty->assign("data",$All_stu);
$smarty->assign("SEX",$SEX);
$smarty->display($tpl_file);

foot();

?>
