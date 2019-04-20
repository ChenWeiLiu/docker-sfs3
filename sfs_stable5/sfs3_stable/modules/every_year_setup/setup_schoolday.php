<?php
// $Id: setup_schoolday.php 6311 2011-02-15 08:14:29Z infodaes $
include "config.php";

sfs_check();

$sel_year = $_POST['sel_year'];
$sel_seme = $_POST['sel_seme'];
if ($sel_year =='') $sel_year = curr_year();
if ($sel_seme =='') $sel_seme = curr_seme();

if ($_POST['act']=="insert") {
	$sql_insert = "replace into school_day (day_kind,day,year,seme) values ('start','".$_POST['data']['start']."','$sel_year','$sel_seme')";
	$CONN->Execute($sql_insert) or user_error("新增失敗！<br>$sql_insert",256);
	
	$sql_insert = "replace into school_day (day_kind,day,year,seme) values ('end','".$_POST['data']['end']."','$sel_year','$sel_seme')";
	$CONN->Execute($sql_insert) or user_error("新增失敗！<br>$sql_insert",256);

	$sql_insert = "replace into school_day (day_kind,day,year,seme) values ('st_start','".$_POST['data']['st_start']."','$sel_year','$sel_seme')";
	$CONN->Execute($sql_insert) or user_error("新增失敗！<br>$sql_insert",256);

	$sql_insert = "replace into school_day (day_kind,day,year,seme) values ('st_end','".$_POST['data']['st_end']."','$sel_year','$sel_seme')";
	$CONN->Execute($sql_insert) or user_error("新增失敗！<br>$sql_insert",256);
	
	//將班級課程設為不可
	$sql_class="UPDATE pro_module SET pm_value='0' WHERE pm_name='every_year_setup' AND pm_item='IS_CLASS_SUBJECT';";
	$CONN->Execute($sql_class) or user_error("更新失敗！<br>$sql_class",256);
	
	// 建立學期成績資料表
	//--------------------
	//學期資料表名稱
	$score_semester="score_semester_".$sel_year."_".$sel_seme;
	$creat_table_sql="CREATE TABLE  if not exists $score_semester (
			  score_id bigint(10) unsigned NOT NULL auto_increment,
			  class_id varchar(11) NOT NULL default '',
			  student_sn int(10) unsigned NOT NULL default '0',
			  ss_id smallint(5) unsigned NOT NULL default '0',
			  score float  NOT NULL default '0',
			  test_name varchar(20) NOT NULL default '',
			  test_kind varchar(10) NOT NULL default '定期評量',
			  test_sort tinyint(3) unsigned NOT NULL default '0',
			  update_time datetime NOT NULL default '0000-00-00 00:00:00',
			  sendmit enum('0','1') NOT NULL default '1',
			  teacher_sn smallint(6) NOT NULL default '0',
			  PRIMARY KEY  (student_sn,ss_id,test_kind,test_sort),
			  UNIQUE KEY score_id (score_id)  
					  )";
	$CONN->Execute($creat_table_sql);	
	
	if (!$_POST['week_setup']) $CONN->Execute("delete from week_setup where year='$sel_year' and semester='$sel_seme'");
}

if ($_POST['act']) {

	//取得資料庫中現存之日期資料
	$db_date=curr_year_seme_day($sel_year,$sel_seme);
	
  //日期尚未設定過(prolin)
	if ($db_date[start] == "") {
		if ($sel_seme == 1 ) {
			//上學期
			$y = $sel_year+1911 ;
			$y2 = $sel_year+1912 ;
			$db_date[start]= "$y-08-01" ;
			$db_date[end]= "$y2-01-31" ;
			$db_date[st_start] = "$y-08-31" ;
			$db_date[st_end] = "$y2-01-20" ;
		}else {
			$y = $sel_year+1912 ;
			$db_date[start]= "$y-02-01" ;
			$db_date[end]= "$y-07-31" ;
			$db_date[st_start] = "$y-02-10" ;
			$db_date[st_end] = "$y-06-30" ;       
    }     	 	
  }	
	$smarty->assign("data",$db_date);

	//取得週次資料
	$d=explode("-",$db_date[st_start]);
	$end_date=$db_date[st_end];
	$smt=mktime(0,0,0,$d[1],$d[2],$d[0]);
	$w_day=date("Y-m-d",$smt);
	$dd=getdate($smt);
	$wmt=$smt-($dd[wday]*86400);
	$i=1;
	do {
		$w_day=date("Y-m-d",$wmt);
		$week_data[$i]=$w_day;
		$wmt+=86400*7;
		$i++;
	}
	while ($w_day < $end_date);
	array_pop($week_data);
	$smarty->assign("week_data",$week_data);

	$res=$CONN->Execute("select count(*) as num from week_setup where year='$sel_year' and semester='$sel_seme'");
	$r_num=$res->fields['num'];
	if ($r_num>0 && $_POST['mode']!="disable") $_POST['week_setup']=1;

	if ($_POST['week_setup']) {
		if ($_POST['act']=="insert") {
			$i=1;
			reset($week_data);
			while(list($k,$v)=each($week_data)) {
				if ($_POST[week_enable][$k]) continue;
				$CONN->Execute("replace into week_setup (year,semester,week_no,start_date) values ('$sel_year','$sel_seme','$i','$v')");
				$i++;
			}
		}
		if ($_POST['mode']!="edit" && $r_num>0) {
			$res=$CONN->Execute("select * from week_setup where year='$sel_year' and semester='$sel_seme' order by week_no");
			while(!$res->EOF) {
				$v=$res->fields['start_date'];
				$k=array_search($v,$week_data);
				if ($k) $_POST['week_enable'][$k]=1;
				$res->MoveNext();
			}
			while(list($k,$v)=each($week_data)) {
				$_POST['week_enable'][$k]=1-$_POST[week_enable][$k];
			}
		}
	}
}

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE); 
$smarty->assign("module_name","學期日期設定"); 
$smarty->assign("SFS_MENU",$school_menu_p); 
$smarty->assign("sel_year",$sel_year);
$smarty->assign("sel_seme",$sel_seme);
$smarty->assign("now",date("Y-m-d"));
$smarty->display('every_year_setup_setup_schoolday.tpl'); 
?>
