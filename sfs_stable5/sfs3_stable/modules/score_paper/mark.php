<?php
// $Id: mark.php 8599 2015-11-20 02:27:38Z qfon $

include "config.php";
sfs_check();

//主選單設定
$school_menu_p=(empty($school_menu_p))?array():$school_menu_p;

$act=$_REQUEST[act];

$sel_year=(empty($_REQUEST[sel_year]))?curr_year():$_REQUEST[sel_year]; //目前學年
$sel_seme=(empty($_REQUEST[sel_seme]))?curr_seme():$_REQUEST[sel_seme]; //目前學期

$CHK_KIND=chk_kind();

//執行動作判斷
$main=&get_all_mark($sel_year,$sel_seme);


//秀出網頁
head("成績單標籤一覽");
echo $main;
foot();

//取得所有標籤
function &get_all_mark($sel_year,$sel_seme){
	global $CONN,$sch_montain_p,$sch_mark_p,$sch_class_p,$UPLOAD_URL,$school_menu_p,$performance,$ss9,$CHK_KIND;
	
	$mark_all="標籤範例：<table cellspacing='1' cellpadding='3' bgcolor='#C0C0C0' class='small'>";
	
	//取得學校資料
	$school=get_school_base_array();
	
	
	$mark_all.=make_list($school,"學校","","",false);
	$mark_list=get_mark($school);
	
	
	
	//取得班級及個人資料
	$sql_select="SELECT stud_id,curr_class_num FROM stud_base where stud_study_cond='0' and curr_class_num<>'00000' order by curr_class_num LIMIT 0,1";
	$recordSet=$CONN->Execute($sql_select) or user_error($sql_select,256);	
	list($stud_id,$curr_class_num) = $recordSet->FetchRow();
	
	//求得學生ID	
	$student_sn=stud_id2student_sn($stud_id);
	
	$c=curr_class_num2_data($curr_class_num);
	$class_id=old_class_2_new_id($c[class_id],$sel_year,$sel_seme);
	$class=get_stud_base_array($class_id,$stud_id);
	$mark_all.=make_list($class,"班級及個人","","",false);
	$mark_list.=get_mark($class);
	
	//取得學期資訊
	$days=get_all_days($sel_year,$sel_seme,$class_id);
	$mark_all.=make_list($days,"學期資訊","","",false);	
	$mark_list.=get_mark($days);
	
	//取得該學生日常生活表現評量值
	$oth_data=get_oth_value($stud_id,$sel_year,$sel_seme);
	foreach($performance as $id=>$sk){
		$oth_array[$sk]=$oth_data['生活表現評量'][$id];
	}
	$mark_all.=make_list($oth_array,"生活表現評量","","",false);	
	$mark_list.=get_mark($oth_array);
	
	//取得學生學期評語及分數
	$nor_value=get_nor_value($student_sn,$sel_year,$sel_seme,$class_id);
	$mark_all.=make_list($nor_value,"學期總表現","","",false);
	$mark_list.=get_mark($nor_value);

	//取得學生日常生活表現文字
	$nor_text=get_nor_text($student_sn,$sel_year,$sel_seme);
	$mark_all.=make_list($nor_text,"日常生活表現文字","","",false);
	$mark_list.=get_mark($nor_text);

	//取得學生日常生活檢核文字
	$chk_text=get_chk_text($student_sn,$sel_year,$sel_seme,$CHK_KIND);
	$mark_all.=make_list($chk_text,"日常生活檢核文字","","",false);
	$mark_list.=get_mark($chk_text);
	
	//取得學生缺席情況
	$abs_data=get_abs_value($stud_id,$sel_year,$sel_seme,"標籤");
	$mark_all.=make_list($abs_data,"缺席情況","","",false);
	$mark_list.=get_mark($abs_data);
	
	//取得學生缺席情況（成績單輸入版）
	$abs_data2=get_abs_value($stud_id,$sel_year,$sel_seme,"標籤_成");
	$mark_all.=make_list($abs_data2,"缺席情況（成績單輸入版）","","",false);
	$mark_list.=get_mark($abs_data2);
	
	//學生獎懲情況
	$reward_data = get_reward_value2($stud_id,$sel_year,$sel_seme);	
	$mark_all.=make_list($reward_data,"獎懲情況","","",false);
	$mark_list.=get_mark($reward_data);
	
	//學生獎懲情況（成績單輸入版）
	$reward_data2 = get_reward_value($stud_id,$sel_year,$sel_seme,"標籤_成");	
	$mark_all.=make_list($reward_data2,"獎懲情況（成績單輸入版）","","",false);
	$mark_list.=get_mark($reward_data2);
	
	$mark_all.="</table>";
	
	$mark_ss9_all="<p>";
	//自動偵測九年一貫科目標籤
	$other_title="<td>節數</td><td>分數</td><td>加權</td><td>等第</td><td>努力程度</td><td>評語</td>";
	
	//自動偵測九年一貫科目標籤
	$ss9_array=get_ss9_array($sel_year,$sel_seme,$stud_id,$student_sn,$class_id);
	$yss9=array();
	
	
	//一個迴一個科目
	foreach($ss9 as $link_ss){
		//if($subject['need_exam']!='1')continue;
		$k="九_".$link_ss;
		$k1=$k."節數";
		$k2=$k."分數";
		$k3=$k."加權";
		$k4=$k."等第";
		$k5=$k."努力程度";
		$k6=$k."評語";
			
		$yss9[$k]=$link_ss;
		$other9[$k]=array("{".$k."節數}","{".$k."分數}","{".$k."加權}","{".$k."等第}","{".$k."努力程度}","{".$k."評語}");
	}
	
	if(!empty($ss9_array)){
		$mark_ss9_all.=make_list($yss9,"自動偵測九年一貫科目",$other_title,$other9)."<br>";
		$mark_list.=get_mark($yss9,$other9);
	}

	//衍生標籤
	$otherm_title="<td>節數</td><td>分數</td><td>加權</td><td>等第</td>";
	$ssm=array("語文","學期總平均");
	foreach($ssm as $link_ss){
		$k="九_".$link_ss;
		$yssm[$k]=$link_ss;
		$otherm[$k]=array("{".$k."節數}","{".$k."分數}","{".$k."加權}","{".$k."等第}");
	}

	$mark_ss9_all.=make_list($yssm,"九年一貫衍生",$otherm_title,$otherm)."<br>";
	$mark_list.=get_mark($yssm,$otherm);
	
	$mark_ss_all="";
	$mark_ss_list="";

	//取得班級設定
	$sql_select = "select c_year from school_class where year='$sel_year' and semester='$sel_seme' and enable='1' group by c_year";
	$recordSet=$CONN->Execute($sql_select);
	while(list($cyear) = $recordSet->FetchRow()){
		//取得科目陣列
		$ss_array=ss_array($sel_year,$sel_seme,$cyear);
		$yss=array();
		$other=array();
		//一個迴一個科目
		foreach($ss_array as $ss_id=>$subject){
			if($subject['need_exam']!='1')continue;
			$k=$subject['name'];
			
			$yss[$k]=$subject['name'];
			$other[$k]=array("{".$k."節數}","{".$k."分數}","{".$k."加權}","{".$k."等第}","{".$k."努力程度}","{".$k."評語}");			
		}
		
		
		if(!empty($ss_array)){
			$mark_ss_all.=make_list($yss,"$cyear 年級科目",$other_title,$other)."<br>";
			$mark_list.=get_mark($yss,$other);
			
		}
	}
	
	
	
	//相關功能表
	$tool_bar=&make_menu($school_menu_p);
	$main=$tool_bar."<table class=small width='100%'><tr><td valign=top>".$mark_all."</td><td valign=top>所有標籤（方便您複製使用）<br><textarea cols=70 rows=60 class='small' style='width:100%'>".$mark_list."</textarea></td></table>".$mark_ss9_all.$mark_ss_all;
	return $main;
}


?>
