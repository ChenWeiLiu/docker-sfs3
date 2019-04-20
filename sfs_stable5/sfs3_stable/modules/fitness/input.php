<?php
// $Id: input.php 7839 2014-01-07 02:20:29Z infodaes $
// 取得設定檔
include "config.php";

sfs_check();

if($_POST['year_seme']=="") $_POST['year_seme']=sprintf("%03d",curr_year()).curr_seme();
$sel_year=intval(substr($_POST['year_seme'],0,-1));
$sel_seme=intval(substr($_POST['year_seme'],-1,1));
$seme_year_seme = sprintf("%03d%d",$sel_year,$sel_seme);
$curr_year_seme = sprintf("%03d%d",curr_year(),curr_seme());
$class_arr=class_base($seme_year_seme);

//if($seme_year_seme==$curr_year_seme) $import=1;

if($_POST['copy_wh']=="抓取本學期全校學生身高體重資料")
	{
		$query="SELECT student_sn FROM stud_base WHERE stud_study_cond=0;";
		$res=$CONN->Execute($query);
		while(!$res->EOF)
		{
			$student_sn=$res->fields[0];
			$check="SELECT weight,height FROM health_WH WHERE student_sn=$student_sn AND year=$sel_year AND semester=$sel_seme;";
			$rs=$CONN->Execute($check);
			if($rs->RecordCount()){
				$tall=$rs->fields['height'];
				$weight=$rs->fields['weight'];
				if($tall and $weight) {
					//檢查有無紀錄了
					$check_record="select id FROM fitness_data WHERE student_sn=$student_sn AND c_curr_seme='$seme_year_seme'";
					$rs2=$CONN->Execute($check_record);
					if($rs2->RecordCount())
						$go_query="UPDATE fitness_data SET tall='$tall',weigh='$weight' WHERE student_sn=$student_sn AND c_curr_seme='$seme_year_seme';";
						else $go_query="INSERT INTO fitness_data SET tall='$tall',weigh='$weight',student_sn=$student_sn,c_curr_seme='$seme_year_seme';";
						$CONN->Execute($go_query);
				}
			}
			$res->MoveNext();
		}
		
		
		$wh_data=array();
		while(!$res->EOF)
		{
			$student_sn=$res->fields[student_sn];
			$weight=$res->fields['weight'];
			$height=$res->fields['height'];
			$wh_data[$student_sn]['weight']=$weight;
			$wh_data[$student_sn]['height']=$height;
			
			$res->MoveNext();
		}
	}

//儲存紀錄處理
if($_POST['go']=='匯入'){
	if($_POST['content']){
		//$organization=$_POST['organization'];
		$content=explode("\r\n",$_POST['content']);
		foreach($content as $key=>$value){
			$student_data=explode("\t",$value);
			//抓取student_sn
			$stud_id=$student_data[4];
			$seme_class=$student_data[3];
			if($stud_id and $seme_class){
				//找出student_sn
				$query="select student_sn,seme_class from stud_seme where stud_id='$stud_id' and seme_year_seme='$seme_year_seme'";
				$res=$CONN->Execute($query) or die("SQL錯誤:$query");
				if($res->recordcount()){
					$target_sn=$res->fields[0];
					$target_class=$res->fields[1];
					if($target_class==$seme_class){
						//刪除舊紀錄
						$query="delete from fitness_data where student_sn='$target_sn' and c_curr_seme='$seme_year_seme'";
						$CONN->Execute($query) or die("SQL錯誤:$query");
						$dd=explode('-',$student_data[0]);
						if(count($dd)<2) $dd=explode('/',$student_data[0]);
						$test_y=($dd[0]>=1911)?$dd[0]-1911:$dd[0];
						$test_m=$dd[1];
						$batch_values.="('$seme_year_seme','$test_y','$test_m','$target_sn','{$_SESSION[session_tea_sn]}',now(),'{$student_data[10]}','{$student_data[12]}','{$student_data[11]}','{$student_data[13]}','{$student_data[8]}','{$student_data[9]}','{$student_data[14]}'),";
					} else $msg.="班級：$seme_class 座號：$seme_num 學號：$stud_id ，此生的就學班級與 $seme_year_seme 學期的紀錄不符，放棄匯入！<br>";
				} else $msg.="班級：$seme_class 座號：$seme_num 學號：$stud_id ，無此生於 $seme_year_seme 學期的就學紀錄，無法匯入！<br>";
			} else $msg.="班級：$seme_class 座號：$seme_num 學號：$stud_id ，無此生的 學號 或 班級，無法匯入！<br>";
		}
		if($batch_values) {
			$batch_values=substr($batch_values,0,-1);
			$batch_values="INSERT INTO fitness_data(c_curr_seme,test_y,test_m,student_sn,teacher_sn,up_date,test1,test2,test3,test4,tall,weigh,organization) VALUES $batch_values ;";
			$res=$CONN->Execute($batch_values) or die("SQL錯誤:$batch_values");
		}

	}
}


//管理者
if ($admin==1){
	if ($_POST[me]) {
		$class_num=$_POST[me];
	} else {
		$class_num=($IS_JHORES=="0")?"101":"701";
	}
	$smarty->assign("seme_menu",year_seme_menu($sel_year,$sel_seme));
	$smarty->assign("class_menu",class_name_menu($sel_year,$sel_seme,$class_num));
} else {
	$query="select distinct a.class_id from score_course a,score_ss b where a.ss_id=b.ss_id and a.year='$sel_year' and a.semester='$sel_seme' and a.teacher_sn='".$_SESSION[session_tea_sn]."' and b.link_ss='健康與體育' order by class_id";
	$res=$CONN->Execute($query);
	//領域教師
	if ($res->RecordCount()>0) {
		while(!$res->EOF) {
			$m=$res->FetchRow();
			$n=explode("_",$m[class_id]);
			$nn=intval($n[2].$n[3]);
			$c_arr[$nn]=$class_arr[$nn];
		}
		if ($_POST[me]) {
			$class_num=$_POST[me];
		} else {
			$class_num=$nn;
		}
		$smarty->assign("class_menu",class_menu($sel_year,$sel_seme,$class_num,$c_arr));
	} else {
		//取得任教班級代號
		$class_num=get_teach_class();
		$smarty->assign("class_menu",class_menu($sel_year,$sel_seme,$class_num));
	}
}

if ($class_num) {
	$query="select a.student_sn,a.stud_name,a.stud_id,a.stud_sex,b.seme_num from stud_base a,stud_seme b where a.student_sn=b.student_sn and b.seme_year_seme='$seme_year_seme' and b.seme_class='$class_num' and a.stud_study_cond in ($in_study) order by curr_class_num";
	$res=$CONN->Execute($query);
	$r=$res->GetRows();
	$smarty->assign("rowdata",$r);
	while(list($k,$v)=each($r)) {
		$stud_arr[]=$v[student_sn];
		$query="select count(student_sn) from fitness_data where student_sn='".$v[student_sn]."' and c_curr_seme='$seme_year_seme'";
		$res=$CONN->Execute($query);
		if ($res->fields[0]==0) {
			$CONN->Execute("insert into fitness_data (c_curr_seme,student_sn) values ('$seme_year_seme','".$v[student_sn]."')");
		}
	}

	if($_POST['copy_wh']=="抓取本學期身高體重資料")
	{
		$query="SELECT student_sn,weight,height FROM health_WH WHERE year=$sel_year AND semester=$sel_seme;";
		$res=$CONN->Execute($query);
		$wh_data=array();
		while(!$res->EOF)
		{
			$student_sn=$res->fields[student_sn];
			$weight=$res->fields['weight'];
			$height=$res->fields['height'];
			$wh_data[$student_sn]['weight']=$weight;
			$wh_data[$student_sn]['height']=$height;
			
			$res->MoveNext();
		}
		$query="SELECT student_sn,curr_class_num FROM stud_base WHERE stud_study_cond=0 AND curr_class_num like '$class_num%';";
		$res=$CONN->Execute($query);
		while(!$res->EOF)
		{
			$student_sn=$res->fields[student_sn];
			$tall=$wh_data[$student_sn]['height'];
			$weight=$wh_data[$student_sn]['weight'];			
			if($tall and $weight) {
			$update_query="UPDATE fitness_data SET tall='$tall',weigh='$weight' WHERE student_sn=$student_sn AND c_curr_seme='$seme_year_seme';";
			$res_update=$CONN->Execute($update_query);
			}
					
			$res->MoveNext();
		}
		
	}
	
	$stud_str="'".implode("','",$stud_arr)."'";
	$query="select * from fitness_data where student_sn in ($stud_str) and c_curr_seme='$seme_year_seme'";
	$res=$CONN->Execute($query);
	while(!$res->EOF) {
		$f=array();
		$f=$res->FetchRow();
		$fd[$f[student_sn]]=$f;
	}
	$smarty->assign("fd",$fd);
	$smarty->assign("class_num",$class_num);
} else {
	head("權限錯誤");
	stud_class_err();
	exit;
}

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
$smarty->assign("module_name","體適能紀錄輸入");
$smarty->assign("SFS_MENU",$menu_p);
$smarty->assign("admin",$admin);  
$smarty->assign("msg",$msg);
$smarty->assign("import",$import);
$smarty->assign("sel_year",$sel_year);
$smarty->assign("sel_seme",$sel_seme);
$smarty->display("fitness_input.tpl");
?>
