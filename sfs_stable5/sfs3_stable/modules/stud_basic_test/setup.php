<?php

// $Id: setup.php 7219 2013-03-12 07:02:20Z brucelyc $

include "select_data_config.php";

sfs_check();

//判斷原住民身分別
$type9="";
$query="select * from stud_subkind_ref where type_id='9'";
$res=$CONN->Execute($query);
$smarty->assign("clan",$res->fields['clan_title']); 
$smarty->assign("area",$res->fields['area_title']);
$temp_str=$res->fields['memo_title'];
if ($temp_str=="族語認證") $type9="memo";
$smarty->assign("memo",$temp_str); 
$temp_str=$res->fields['note_title'];
if ($temp_str=="族語認證") $type9="note";
$smarty->assign("note",$res->fields['note_title']); 

//判斷境外科技人才子女
$type71="";
$have71=0;
$query="select * from sfs_text where t_kind='stud_kind' and t_name='境外科技人才子女'";
$res=$CONN->Execute($query);
if ($res->fields['d_id']=="") {
	$query="select * from sfs_text where t_kind='stud_kind' and (d_id/1)>'70' order by d_id";
	$res=$CONN->Execute($query);
	if ($res->fields['d_id']>71 || $res->fields['d_id']=="") $type71=71;
	else {
		$oid=70;
		while(!$res->EOF) {
			if ($res->fields['d_id']!=($oid+1)) break;
			$oid=$res->fields['d_id'];
			$type71=$oid+2;
			$res->MoveNext();
		}
	}
} else {
	$type71=$res->fields['d_id'];
	$have71=1;
}

//判斷境外科技人才子女後新增
if ($have71) {
	$query="select * from stud_subkind_ref where type_id='$type71'";
	$res=$CONN->Execute($query);
	if ($res->RecordCount()>0) {
		$query="select * from stud_subkind_ref where type_id='$type71' and clan_title='來臺就讀狀況'";
		$res=$CONN->Execute($query);
		if ($res->RecordCount()==0) {
			$query="update stud_subkind_ref set clan_title='來臺就讀狀況',clan='未滿一學期\r\n未滿一學年\r\n未滿二學年\r\n未滿三學年' where type_id='$type71'";
			$res=$CONN->Execute($query);
		}
	} else {
		$query="insert into stud_subkind_ref (type_id,clan_title,clan) values ('$type71','來臺就讀狀況','未滿一學期\r\n未滿一學年\r\n未滿二學年\r\n未滿三學年')";
		$res=$CONN->Execute($query);
	}
}

//派外子女新增子項
$query="select * from stud_subkind_ref where type_id='12'";
$res=$CONN->Execute($query);
if ($res->RecordCount()>0) {
	$query="select * from stud_subkind_ref where type_id='12' and clan_title='返國就讀狀況'";
	$res=$CONN->Execute($query);
	if ($res->RecordCount()==0) {
		$query="update stud_subkind_ref set clan_title='返國就讀狀況',clan='未滿一學期\r\n未滿一學年\r\n未滿二學年\r\n未滿三學年' where type_id='12'";
		$res=$CONN->Execute($query);
		echo "qqq";
	}
} else {
	$query="insert into stud_subkind_ref (type_id,clan_title,clan) values ('12','返國就讀狀況','未滿一學期\r\n未滿一學年\r\n未滿二學年\r\n未滿三學年')";
	$res=$CONN->Execute($query);
}

//確定$_POST['spec']值
if ($_POST['spec']!="memo" && $_POST['spec']!="note") $_POST['spec']="";

//新增採計部份學期成績一般生
if ($_POST['add'] && $_POST['stud_id']) {
	$query="select * from stud_base where stud_id='".$_POST['stud_id']."' and stud_study_cond='0'";
	$res=$CONN->Execute($query);
	$student_sn=$res->fields['student_sn'];
	if ($student_sn) {
		$query="select * from stud_seme_dis where seme_year_seme='".sprintf("%03d",curr_year()).curr_seme()."' and sp_kind='0' and student_sn='$student_sn'";
		$res=$CONN->Execute($query);
		$student_sn=$res->fields['student_sn'];
		if ($student_sn) {
			$query="update stud_seme_dis set sp_cal='1' where seme_year_seme='".sprintf("%03d",curr_year()).curr_seme()."' and student_sn='$student_sn'";
			$res=$CONN->Execute($query);
		}
	}
}

//新增採計部份學期成績特殊生
if ($_POST['sp'] && $_POST['stud_id']) {
	$query="select * from stud_base where stud_id='".$_POST['stud_id']."' and stud_study_cond='0'";
	$res=$CONN->Execute($query);
	$student_sn=$res->fields['student_sn'];
	if ($student_sn) {
		$query="select * from stud_seme_dis where seme_year_seme='".sprintf("%03d",curr_year()).curr_seme()."' and sp_kind<>'0' and student_sn='$student_sn'";
		$res=$CONN->Execute($query);
		$student_sn=$res->fields['student_sn'];
		if ($student_sn) {
			$query="update stud_seme_dis set sp_cal='1' where seme_year_seme='".sprintf("%03d",curr_year()).curr_seme()."' and student_sn='$student_sn'";
			$res=$CONN->Execute($query);
		}
	}
}

//新增不參與排序學生
if ($_POST['del'] && $_POST['stud_id']) {
	$query="select * from stud_base where stud_id='".$_POST['stud_id']."' and stud_study_cond='0'";
	$res=$CONN->Execute($query);
	$student_sn=$res->fields['student_sn'];
	if ($student_sn) {
		$query="select * from stud_seme_dis where seme_year_seme='".sprintf("%03d",curr_year()).curr_seme()."' and student_sn='$student_sn'";
		$res=$CONN->Execute($query);
		$student_sn=$res->fields['student_sn'];
		if ($student_sn) {
			$query="update stud_seme_dis set cal='0' where seme_year_seme='".sprintf("%03d",curr_year()).curr_seme()."' and student_sn='$student_sn'";
			$res=$CONN->Execute($query);
		}
	}
}

//儲存
if ($type9=="" && $_POST['sure9'] && ($_POST['spec']=="memo" || $_POST['spec']=="note")) {
	$query="update stud_subkind_ref set ".$_POST['spec']."_title='族語認證',".$_POST['spec']."='無\r\n有' where type_id=9";
	$res=$CONN->Execute($query);
} elseif ($have71==0) {
	$smarty->assign("type71",$type71);
	$_POST['tech']=intval($_POST['tech']);
	if ($_POST['sure71'] && $_POST['tech']) {
		$query="select * from sfs_text where t_kind='stud_kind' and d_id='".$_POST['tech']."'";
		$res=$CONN->Execute($query);
		if ($res->fields['t_name']=="") {
			$query="select * from sfs_text where t_kind='stud_kind' and t_parent=''";
			$res=$CONN->Execute($query);
			$p_id=$res->fields['t_id'];
			$query="insert into sfs_text (t_order_id,t_kind,g_id,d_id,t_name,t_parent,p_id,p_dot) values ('".$_POST['tech']."','stud_kind','1','".$_POST['tech']."','境外科技人才子女','$p_id,','$p_id','.')";
			$res=$CONN->Execute($query) or die("新增「境外科技人才子女」項目錯誤");
			header("location: setup.php");
		}
	}
}

if ($type9=="")
	$smarty->assign("stage",1);
elseif ($have71==0)
	$smarty->assign("stage",2);
else {
	$smarty->assign("stage",3);
	//先確認資料表是否存在
	$query="select * from stud_seme_dis where 1=1";
	$res=$CONN->Execute($query);
	if ($res) {
		//如果按了「儲存採計學期」
		if ($_POST['save']) {
			foreach($_POST['sel'] as $sn=>$v) {
				$query="update stud_seme_dis set enable0='".($_POST['cal'][$sn][0]?1:"")."',enable1='".($_POST['cal'][$sn][1]?1:"")."',enable2='".($_POST['cal'][$sn][2]?1:"")."' where seme_year_seme='".sprintf("%03d",curr_year()).curr_seme()."' and student_sn='$sn'";
				$res=$CONN->Execute($query);
			}
		}
		$query="select a.*,b.stud_name,b.stud_sex from stud_seme_dis a left join stud_base b on a.student_sn=b.student_sn where a.seme_year_seme='".sprintf("%03d",curr_year()).curr_seme()."' and (sp_kind>'0' or sp_cal='1') and a.seme_class like '9%' order by a.sp_kind,a.seme_class,a.seme_num";
		$res=$CONN->Execute($query);
		$rowdata=array();
		$stud_data=array();
		while(!$res->EOF) {
			$sn=$res->fields['student_sn'];
			$sp_kind=$res->fields['sp_kind'];
			$query2="select stud_id from stud_base where student_sn='$sn'";
			$res2=$CONN->Execute($query2);
			$rowdata[$sp_kind][$sn]['stud_id']=$res2->fields['stud_id'];
			$rowdata[$sp_kind][$sn]['seme_class']=$res->fields['seme_class'];
			$rowdata[$sp_kind][$sn]['seme_num']=$res->fields['seme_num'];
			$rowdata[$sp_kind][$sn]['name']=$res->fields['stud_name'];
			$rowdata[$sp_kind][$sn]['sex']=$res->fields['stud_sex'];
			$rowdata[$sp_kind][$sn]['sp_cal']=$res->fields['sp_cal'];
			$stud_data[$sn]['enable0']=$res->fields['enable0'];
			$stud_data[$sn]['enable1']=$res->fields['enable1'];
			$stud_data[$sn]['enable2']=$res->fields['enable2'];
			$stud_data[$sn]['sp_cal']=$res->fields['sp_cal'];
			$stud_data[$sn]['kind']=$res->fields['stud_kind'];
			$stud_data[$sn]['sp_kind']=$sp_kind;
			$stud_data[$sn]['plus']=$plus_arr[$sp_kind];
			$res->MoveNext();
		}
	}
	$smarty->assign("rowdata",$rowdata);
	$smarty->assign("spc_arr",array(0=>"一般生",1=>"原住民",2=>"原住民",3=>"境外科技人才子女",4=>"境外科技人才子女",5=>"境外科技人才子女",6=>"境外科技人才子女",7=>"派外人員子女",8=>"派外人員子女",9=>"派外人員子女",'A'=>"派外人員子女",'B'=>"蒙藏生",'C'=>"身障生"));
	$smarty->assign("spo_arr",array(1=>"無族語認證",2=>"有族語認證",3=>"未滿一學期",4=>"未滿一學年",5=>"未滿二學年",6=>"未滿三學年",7=>"未滿一學期",8=>"未滿一學年",9=>"未滿二學年",'A'=>"未滿三學年",'B'=>"",'C'=>""));
	$smarty->assign("chk_arr",array(0=>"1",3=>"1",4=>"1",5=>"1",6=>"1",7=>"1",8=>"1",9=>"1",'A'=>"1"));
	$smarty->assign("plus_arr",$plus_arr);
	$smarty->assign("stud_data",$stud_data);

	if (count($_POST['sel'])>0 && $_POST['print']) {
		$allprint="";
		foreach($_POST['sel'] as $sn=>$v) $allprint.="'$sn',";
		$allprint=substr($allprint,0,-1);
		$query="select * from stud_seme_dis where seme_year_seme='".sprintf("%03d",curr_year()).curr_seme()."' and student_sn in ($allprint) order by sp_kind,seme_class,seme_num";
		$res=$CONN->Execute($query);
		$show_sn=array();
		while(!$res->EOF) {
			$seme_class=$res->fields[seme_class];
			$show_sn[$seme_class][$res->fields[seme_num]]=$res->fields[student_sn];
			$res->MoveNext();
		}
		$query="select * from stud_base where student_sn in ($allprint)";
		$res=$CONN->Execute($query);
		while(!$res->EOF) {
			$sn=$res->fields['student_sn'];
			$stud_data[$sn][stud_name]=$res->fields['stud_name'];
			$stud_data[$sn][stud_id]=$res->fields['stud_id'];
			$stud_data[$sn][stud_person_id]=$res->fields['stud_person_id'];
			$stud_data[$sn][stud_sex]=$res->fields['stud_sex'];
			$stud_data[$sn][stud_addr_1]=$res->fields['stud_addr_1'];
			$stud_data[$sn][stud_tel_1]=$res->fields['stud_tel_1'];
			$stud_data[$sn][addr_zip]=$res->fields['addr_zip'];
			$res->MoveNext();
		}
		$s_arr=array(1=>"本國語文",2=>"英語",3=>"語文平均",4=>"數學",5=>"社會",6=>"自然與生活科技",7=>"藝術與人文",8=>"健康與體育",9=>"綜合活動",10=>"學期成績平均");
		$query="select * from temp_tcc_score where student_sn in ($allprint)";
		$res=$CONN->Execute($query);
		$rowdata=array();
		while(!$res->EOF) {
			$rowdata[$res->fields['student_sn']][$res->fields['seme']][$res->fields['ss_no']][score]=$res->fields['score'];
			$rowdata[$res->fields['student_sn']][$res->fields['seme']][$res->fields['ss_no']][pr]=$res->fields['pr'];
			$res->MoveNext();
		}

		foreach($rowdata as $sn=>$d) {
			reset($s_arr);
			foreach($s_arr as $ss_no=>$dd) {
				$plus=1+$stud_data[$sn][plus]/100;
				$sc=$rowdata[$sn][3][$ss_no][score]*$plus;
				$rowdata[$sn][3][$ss_no][pscore]=$sc;
				$query="select * from temp_tcc_score where seme='3' and ss_no='$ss_no' and score>='$sc' order by pr desc limit 0,1";
				$res=$CONN->Execute($query);
				$upr=$res->fields['pr'];
				$mypr=(intval($upr)==0)?1:$upr;
				$rowdata[$sn][3][$ss_no][ppr]=$mypr;
			}
			$rowdata[$sn][3][$ss_no][pscore]=$sc;
			for($i=0;$i<3;$i++) $rowdata[$sn][$i][$ss_no][pscore]=$rowdata[$sn][$i][$ss_no][score]*$plus;
		}
		$smarty->assign("student_sn",$show_sn);
		$smarty->assign("rowdata",$rowdata);
		$smarty->assign("stud_data",$stud_data);
		$smarty->assign("sch_arr",get_school_base());
		$smarty->assign("s_arr",$s_arr);
		$smarty->display("stud_basic_test_setup_print.tpl");
		exit;
	}
}
$smarty->assign("SFS_PATH_HTML",$SFS_PATH_HTML);
$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE); 
$smarty->assign("module_name","免試入學特種身分學生設定"); 
$smarty->assign("SFS_MENU",$menu_p); 
$smarty->display("stud_basic_test_setup.tpl");
?>
