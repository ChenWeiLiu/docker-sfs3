<?php
// $Id: normal.php 9263 2018-06-28 09:31:06Z chiming $
/*引入學務系統設定檔*/
include "config.php";
include "./module-upgrade.php";
require_once "../../include/sfs_case_score.php";
//引入函數
include "./my_fun.php";

//使用者認證
sfs_check();

//取得模組設定
$m_arr = get_sfs_module_set();
extract($m_arr, EXTR_OVERWRITE);

//變數設定
$edit=$_GET['edit'];
$is_print=$_GET['print'];
$yorn=findyorn();
$teacher_course = $_REQUEST[teacher_course];
$curr_sort = $_REQUEST[curr_sort];
if(empty($sel_year))$sel_year = curr_year(); //目前學年
if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期

//取得排除名單
$student_out=get_manage_out($sel_year,$sel_seme);


//教師代號
$teacher_sn = $_SESSION[session_tea_sn];

//成績表名稱
$nor_score="nor_score_".curr_year()."_".curr_seme();
$score_semester="score_semester_".curr_year()."_".curr_seme();

 //若是該學期的平時成績資料表不存在就依照命名規則自動建立一個 	 
$creat_table_sql="
	CREATE TABLE if not exists $nor_score ( 	 
	sn int(11) NOT NULL auto_increment, 	 
	teach_id varchar(20) NOT NULL default '', 	 
	stud_sn int(10) unsigned NOT NULL default '0', 	 
	class_subj varchar(40) NOT NULL default '', 
	elective_id varchar(10) NOT NULL default '', 	
	stage tinyint(1) unsigned NOT NULL default '0', 	 
	test_name varchar(40) NOT NULL default '', 	 
	test_score float default '-100', 	 
	weighted int(2) NOT NULL default '1', 	 
	enable tinyint(1) unsigned NOT NULL default '1', 	 
	freq int(10) unsigned NOT NULL default '0', 	 
	PRIMARY KEY  (`sn`),
	KEY `teach_id` (`teach_id`,`stud_sn`),
	KEY `elective_id` (`elective_id`)) ENGINE=MyISAM"; 	 
$rs=$CONN->Execute($creat_table_sql);

//取得正確任教課程
$course_arr_all=get_teacher_course(curr_year(),curr_seme(),$teacher_sn,$is_allow);
$course_arr = $course_arr_all['course'];
// 檢查課程權限是否正確
$cc_arr=array_keys($course_arr);
$err=(in_array($teacher_course,$cc_arr) || $teacher_course=="")?0:1;

if ($err==0) {

	//科目下拉選單 -------------
	$sel= new drop_select();
	$sel->s_name = "teacher_course";
	$sel->id = $teacher_course;
	$sel->is_submit = true;
	$sel->arr = $course_arr;
	$sel->top_option = "選擇班級科目";
	$sel->font_style="";
	$sel->font_color = "#F75500";
	$sel->is_bgcolor_list = true;
	$course_sel = $sel->get_select();
	//------------- 科目下拉選單 結束
	$smarty->assign("course_sel",$course_sel);

	if(strstr ($teacher_course, 'g')){

		//分組課程的階段下拉選單 ------------
		$teacher_course_arr=explode("g",$teacher_course);
		$group_id=$teacher_course_arr[0];
		$ss_id=$teacher_course_arr[1];

		//檢查是否是完整的課程，就是要月考的啦！
		$query = "select scope_id,subject_id,print from score_ss where ss_id='$ss_id' ";
		$res = $CONN->Execute($query) or trigger_error($query,E_USER_ERROR);
		$print = $res->fields['print'];

	}else{
		//階段下拉選單 ------------
		$query = "select a.ss_id,a.class_id,b.scope_id,b.subject_id,b.print from score_course a, score_ss b where a.ss_id=b.ss_id and a.course_id='$teacher_course' and b.enable='1'";
		$res = $CONN->Execute($query) or trigger_error($query,E_USER_ERROR);
		$class_id = $res->fields['class_id'];
		$ss_id = $res->fields['ss_id'];
		$print = $res->fields['print'];
	}
	//取得科目代碼
	$subject_id=$res->fields[subject_id];
	if ($subject_id==0) {
		$subject_id=$res->fields[scope_id];
	}

	//取得所有學生資料
	$all_sn="";
	$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
	if(strstr($teacher_course, 'g')){
		$query = "select class_year from score_ss where ss_id='$ss_id'";
		$res = $CONN->Execute($query) or trigger_error($query,E_USER_ERROR);
		$class_year= $res->fields['class_year'];
		$teacher_course_arr=explode("g",$teacher_course);
		$query="select a.*,b.stud_name,b.curr_class_num,b.stud_id,b.stud_study_year from elective_stu a,stud_base b where a.student_sn=b.student_sn and a.group_id='$teacher_course_arr[0]' and b.stud_study_cond in ($in_study) order by b.curr_class_num";
	}else{
		if ($class_id) $class_arr=class_id_2_old($class_id);
		$class_year=$class_arr[3];
		$query="select a.*,b.stud_name,b.curr_class_num,b.stud_id,b.stud_study_year from stud_seme a,stud_base b where a.student_sn=b.student_sn and a.seme_year_seme='$seme_year_seme' and a.seme_class='$class_arr[2]' and b.stud_study_cond in ($in_study) order by a.seme_num";
	}
	$res=$CONN->Execute($query) or trigger_error($query,E_USER_ERROR);
	if ($res)
		while (!$res->EOF) {
			$student_sn=$res->fields['student_sn'];
			$stud_list[$student_sn][site_num]=(strstr($teacher_course,'g'))? substr($res->fields[curr_class_num],-4,2)."_".substr($res->fields[curr_class_num],-2,2):$res->fields[seme_num];
			$stud_list[$student_sn][name]=$res->fields[stud_name];
			
				//排除名單加註*
  	  $stud_list[$student_sn][name].=($student_out[$student_sn])?"<font color=red>*</font>":"";
			
			$stud_list[$student_sn][stud_id]=$res->fields[stud_id];
			$stud_list[$student_sn][class_id]=sprintf("%03d_%d_%02d_%02d",$sel_year,$sel_seme,substr($res->fields[curr_class_num],0,-4),substr($res->fields[curr_class_num],-4,2));
			$stud_study_year=$res->fields[stud_study_year];
			$stud_list[$student_sn][stud_study_year]=$stud_study_year;
			$img=$UPLOAD_PATH."photo/student/".$stud_study_year."/".$res->fields[stud_id];
			if (file_exists($img)) $stud_list[$student_sn][pic]="1";			
			$all_sn.="'".$student_sn."',";
			$res->MoveNext();
		}
	if ($all_sn) $all_sn=substr($all_sn,0,-1);
	$smarty->assign("stud_list",$stud_list);

	// 科目完整時(含階段及學期成績),才出現階段下拉選單
	if ($print=="1") {
		$query = "select performance_test_times,score_mode,test_ratio from score_setup where  class_year='$class_year' and year=$sel_year and semester='$sel_seme' and enable='1'";
		$res = $CONN->Execute($query) or trigger_error($query,E_USER_ERROR);

		//測驗次數
		$performance_test_times = $res->fields[performance_test_times];

		if ($curr_sort <254 && $curr_sort> $performance_test_times)	$curr_sort='';
		//如果未選擇階段時自動取得下個階段
		if ($curr_sort=='' || ($_POST[curr_sort_hidden] <>'' and $curr_sort<>$_POST[curr_sort_hidden]) and $curr_sort<254) {
			//計算目前應在第幾階段 (sendmit = 0 表示已送至教務處成績)
			$query ="select max(test_sort) as mm from $score_semester where student_sn in ($all_sn) and ss_id='$ss_id' and sendmit='0' and test_sort<254";
			$res = $CONN->Execute($query) or trigger_error($query,E_USER_ERROR);
			$mm = $res->fields[0]+1;
			if ($curr_sort =='')	$curr_sort = $mm;
			if ($curr_sort>$performance_test_times)	$curr_sort = $performance_test_times;
		}

		//如果不是每一階段都有平時成績時,出現學期平時成績選項
		if ($yorn=='n') {
			$test_times_arr[254] = "平時成績";
			$curr_sort=254;
		} else {
			//產生下拉選單項目陣列
			for($i=1;$i<= $performance_test_times;$i++)
				$test_times_arr[$i] = "第 $i 階段";
		}

	} else  {
		//全學期只輸入一次成績
		$curr_sort = 255;
		$test_times_arr[255] = "不分階段";
	}
	
	//產生下拉選單
	$sel= new drop_select();
	$sel->s_name = "curr_sort";
	$sel->id = $curr_sort;
	$sel->is_submit = true;
	$sel->arr = $test_times_arr;
	$sel->font_style="";
	$sel->has_empty=false;
	$select_stage_bar = $sel->get_select();	
	//記住上次 curr_sort 值,做判別用
	$select_stage_bar .= "<input type=\"hidden\" name=\"curr_sort_hidden\" value=\"$curr_sort\">";

	//--------------階段下拉選單 結束
	$smarty->assign("curr_sort",$curr_sort);
	$smarty->assign("stage_sel",$select_stage_bar);

	//取得班級及科目名稱
	$full_class_name = $course_arr[$teacher_course];

	//取得中文代碼
	$class_subj=(strstr($teacher_course,'g'))? $teacher_course:$class_id."_".$subject_id;
	$smarty->assign("class_subj",$class_subj);

	//如果是新增一次平時成績
	if ($_POST[add]) {
		if(strstr($teacher_course, 'g'))
			$test_name=ss_id_to_subject_name($ss_id)."分組平".date("is");
		else
			$test_name=ss_id_to_subject_name($ss_id)."平".date("is");
		$query="select max(freq) from $nor_score where class_subj='$class_subj' and stage='$curr_sort' and enable='1'";
		$res=$CONN->Execute($query);
		$next_freq=$res->fields[0]+1;
		reset($stud_list);
		//while(list($student_sn,$v)=each($stud_list)) {
		foreach( $stud_list as $student_sn=>$v) {
			$CONN->Execute("insert into $nor_score (teach_id,stud_sn,class_subj,stage,test_name,test_score,weighted,enable,freq) values ('$_SESSION[session_log_id]','$student_sn','$class_subj','$curr_sort','$test_name','-100','1','1','$next_freq')");
		}
		header("Location: normal.php?teacher_course={$_REQUEST['teacher_course']}&curr_sort=$curr_sort");
		exit;
	}

	//如果是儲存成績
	if ($_POST[save]) {
		//while(list($student_sn,$score)=each($_POST[nor_score])) {
		foreach($_POST['nor_score'] as $student_sn=>$score) {			
			if ($score=="") $score="-100";
			if (substr($student_sn,0,1)=='n'){
				$student_sn = substr($student_sn,1);
				//轉學生
				$query = "insert into $nor_score (teach_id,stud_sn,class_subj,stage,test_name,test_score,weighted,enable,freq) values ('$_SESSION[session_log_id]','$student_sn','$class_subj','$curr_sort','$_POST[test_name]','$score','$_POST[weighted]','1','$_POST[freq]')";
				//echo $query;
			}
			else {
				$query="update $nor_score set test_name='$_POST[test_name]',test_score='$score',weighted='$_POST[weighted]' where teach_id='$_SESSION[session_log_id]' and stud_sn='$student_sn' and stage='$curr_sort' and class_subj='$class_subj' and freq='$_POST[freq]'";
			}
			$res=$CONN->Execute($query);
		}
		if ($_POST['quick']){
			echo "<html><body><script LANGUAGE=\"JavaScript\">javascript:window.opener.location.reload(); window.close();</script></body></html>";
			exit;
		}
	}

	//檢查是否繳至教務處
	if($yorn=='n' && $curr_sort != 255 ){
		if($curr_sort == 254){
			$query = "select count(*) from $score_semester where student_sn in ($all_sn) and ss_id='$ss_id' and test_kind='平時成績' and sendmit='0'";
		}else{
			$query = "select count(*) from $score_semester where student_sn in ($all_sn) and ss_id='$ss_id' and test_sort='$curr_sort' and test_kind='定期評量' and sendmit='0'";
		}
	}else{
		$query = "select count(*) from $score_semester where student_sn in ($all_sn) and ss_id='$ss_id' and test_sort='$curr_sort' and sendmit='0'";
	}
	$res= $CONN->Execute($query);
	$is_send = $res->fields[0];
	$smarty->assign("is_send",$is_send);

	//取得成績
	$data_arr=get_nor_score($sel_year,$sel_seme,$curr_sort,$class_subj,$teacher_sn,2);  //2是計算到小數第二位
	
	//了解已開設的項目數
	$nor_item_already_count=$data_arr[status];
	if($ss_id and !$nor_item_already_count){
		//抓取平時成績預定項目
		$query = "select nor_item_kind from score_ss where ss_id=$ss_id";
		$res = $CONN->Execute($query) or trigger_error($query,E_USER_ERROR);
		$nor_item=$res->fields[0];
		if($nor_item){
			//抓取設定的項目
			$nor_item_array=sfs_text('平時成績選項');
			$nor_item_data=$nor_item_array[$nor_item];
			$nor_item_data_detail=explode(',',$nor_item_data);
			$nor_item_data_detail_count=count($nor_item_data_detail);
			//自動新增至項目表
			foreach($nor_item_data_detail as $nor_item_key=>$nor_item_value){
				//取得項目和加權
				$item_array=explode('*',$nor_item_value);
				$test_name=$item_array[0];
				$weighted=$item_array[1]?$item_array[1]:1;
				$next_freq=$nor_item_key+1;
				foreach($stud_list as $stud_sn=>$v){
					$CONN->Execute("insert into $nor_score(teach_id,stud_sn,class_subj,stage,test_name,test_score,weighted,enable,freq) values ('$_SESSION[session_log_id]','$stud_sn','$class_subj','$curr_sort','$test_name','-100','$weighted','1','$next_freq')");
				}			
			}
			//重新抓取成績
			header("Location: normal.php?teacher_course={$_REQUEST['teacher_course']}&curr_sort=$curr_sort");
		}
	}
//exit;	
	$smarty->assign("data_arr",$data_arr);
	

	//取得教師陣列
	$query="select teach_id,name from teacher_base order by name";
	$res= $CONN->Execute($query);
	while(!$res->EOF) {
		$t_arr[$res->fields[teach_id]]=$res->fields[name];
		$res->MoveNext();
	}
	$smarty->assign("teacher_arr",$t_arr);

	//如果是匯到教務處
	if ($_POST[trans]) {
		$update_time=date("Y-m-d H:i:s");
		if ($yorn=="y" || $curr_sort=="255") {
			$test_kind=($curr_sort=="255")?"全學期":"平時成績";
			$query="select * from $score_semester where student_sn in ($all_sn) and ss_id='$ss_id' and test_sort='$curr_sort' and test_kind='$test_kind'";
		} else {
			//全學期只打一次平時成績
			$test_kind="平時成績";
			$query="select * from $score_semester where student_sn in ($all_sn) and ss_id='$ss_id' and test_kind='$test_kind'";
		}
		$res=$CONN->Execute($query);
		while(!$res->EOF) {
			$data_arr[value][$res->fields[test_sort]][$res->fields[student_sn]]="1";
			$res->MoveNext();
		}

		if ($yorn=="y" || $curr_sort=="255") {
			//while(list($student_sn,$score)=each($data_arr[score][$curr_sort][avg])) {
			foreach($data_arr[score][$curr_sort][avg] as $student_sn=>$score) {
				$cid=$stud_list[$student_sn][class_id];
				if ($data_arr[value][$curr_sort][$student_sn]==1) {
					$query="update $score_semester set score='$score',update_time='$update_time',teacher_sn='$teacher_sn',test_kind='$test_kind' where student_sn='$student_sn' and ss_id='$ss_id' and test_sort='$curr_sort' and  test_kind='$test_kind' ";
					$CONN->Execute($query);
				} else {
					$query="insert into $score_semester (class_id,student_sn,ss_id,score,test_name,test_kind,test_sort,update_time,teacher_sn) values ('$cid','$student_sn','$ss_id','$score','$test_kind','$test_kind','$curr_sort','$update_time','$teacher_sn')";
					$CONN->Execute($query);
				}
			}
		} else {
			//全學期只打一次平時成績
			for ($i=1;$i<=$performance_test_times;$i++) {
				reset($data_arr[score][$curr_sort][avg]);
				//while(list($student_sn,$score)=each($data_arr[score][$curr_sort][avg])) {
				foreach( $data_arr[score][$curr_sort][avg] as  $student_sn=>$score) {
					$cid=$stud_list[$student_sn][class_id];
					if ($data_arr[value][$i][$student_sn]==1) {
						$query="update $score_semester set score='$score',update_time='$update_time',teacher_sn='$teacher_sn' where student_sn='$student_sn' and ss_id='$ss_id' and test_sort='$i' and test_kind='$test_kind' ";
						$CONN->Execute($query);
					} else {
						$query="insert into $score_semester (class_id,student_sn,ss_id,score,test_name,test_kind,test_sort,update_time,teacher_sn) values ('$cid','$student_sn','$ss_id','$score','$test_kind','$test_kind','$i','$update_time','$teacher_sn')";
						$CONN->Execute($query);
					}
				}
			}
		}
		header("Location:manage2.php?is_ok=1&teacher_course=$_POST[teacher_course]&curr_sort=$curr_sort");
	}
	//套入樣版
	$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
	$smarty->assign("module_name","日常成績管理");
	$smarty->assign("SFS_MENU",$menu_p);
	$smarty->assign("SFS_MENU_LINK","teacher_course=".$teacher_course);
	$smarty->assign("is_new_nor",$is_new_nor);
	$smarty->assign("is_mod_nor",$is_mod_nor);
	$smarty->assign("pic_checked",$pic_checked);
	$smarty->assign("pic_width",$pic_width);
	$smarty->assign("UPLOAD_URL",$UPLOAD_URL);

	
				if($pic_checked) {
				//印出照片
				$img=$UPLOAD_PATH."photo/student/".$stud_study_year."/".$stud_id; 
				$img_link=$UPLOAD_URL."photo/student/".$stud_study_year."/".$stud_id;			
				if (file_exists($img)) $stud_list[$student_sn][pic_data]="<td><img src='$img_link' width=$pic_width></td>"; else $stud_list[$student_sn][pic_data]="<td></td>";
			} else $pic_data="";
	
	if ($_REQUEST[quick]) {
		$smarty->assign("sel_year",$sel_year);
		$smarty->assign("sel_seme",$sel_seme);
		$smarty->assign("full_class_name",$full_class_name);
		$smarty->display("score_input_normal_quick.tpl");
	} else
		$smarty->display("score_input_normal.tpl");

} else {
	$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
	$smarty->assign("module_name","日常成績管理");
	$smarty->assign("SFS_MENU",$menu_p);
	$smarty->display("score_input_err.tpl");
}

