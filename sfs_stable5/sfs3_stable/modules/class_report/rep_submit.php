<?php
include_once('config.php');

include_once "../../include/sfs_case_PLlib.php";
include_once "../../include/sfs_case_score.php";
include_once "../modules/score_input/myfun2.php";

sfs_check();

//製作選單 ( $school_menu_p陣列設定於 module-cfg.php )
$tool_bar=&make_menu($school_menu_p);

//讀取目前操作的老師有沒有管理權
$module_manager=checkid($_SERVER['SCRIPT_FILENAME'],1);

//使用者認證
sfs_check();
//取得本學年
$sel_year = curr_year();
//取得本學期
$sel_seme = curr_seme();
//目前學期
$c_curr_seme=sprintf("%03d%d",$sel_year ,$sel_seme);

//學期資料表名稱
$score_semester="score_semester_".$sel_year."_".$sel_seme;
$nor_score="nor_score_".$sel_year."_".$sel_seme;

//POST後的動作****************************************/
if ($_POST['act']=='SUBMIT_IT') {
	//
	$query = "select teacher_sn,class_id,ss_id from score_course where course_id='$_POST[teacher_course]'";
	$update_rs=$CONN->Execute($query);
	$teacher_sn=$update_rs->fields['teacher_sn'];
	$class_id=$update_rs->fields['class_id'];
	$ss_id=$update_rs->fields['ss_id'];
	
	$test_kind="平時成績";
	$now=date("Y-m-d H:i:s");
	
	$test_sort=$_POST['curr_sort'];
	
	
		$REP_SETUP=get_report_setup($_POST['the_report']);
		//取得所有學生
	  $STUD=get_seme_class_students($REP_SETUP['seme_year_seme'],$REP_SETUP['seme_class']);
	  //取得所有成績
	  $SCORES=get_report_score_all($REP_SETUP['sn'],1);	  
		
		foreach ($STUD as $V) {
	  	$score=number_format($SCORES[$V['student_sn']]['avg'],2);
	  	$student_sn=$V['student_sn'];
			$sql = "REPLACE INTO $score_semester(class_id,student_sn,ss_id,score,test_name,test_kind,test_sort,update_time,teacher_sn) values('$class_id','$student_sn','$ss_id','$score','$test_kind','$test_kind','$test_sort','$now','$_SESSION[session_tea_sn]')";
			$res=$CONN->Execute($sql) or die("SQL錯誤, query=".$sql);
		}
		
		$sql="update class_report_setup set locked=1 where sn='".$REP_SETUP['sn']."'";
	  $res=$CONN->Execute($sql) or die("SQL錯誤, query=".$sql);

		$INFO="已於".date("Y-m-d H:i:s")."匯出至「學期成績」中的第".$test_sort."階段平常成績, 請記得透過「成績管理」模組確認!!";
}

if ($_POST['act']=="SUBMIT_NORMAL") {
	 	//課程相關設定
	 	$teacher_course=$_POST['teacher_course'];
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

		//小考成績單的成績
		$REP_SETUP=get_report_setup($_POST['the_report']);
		//取得所有學生
	  $STUD=get_seme_class_students($REP_SETUP['seme_year_seme'],$REP_SETUP['seme_class']);
	  //取得所有成績
	  $SCORES=get_report_score_all($REP_SETUP['sn'],1);	  
		
		//平常成績的設定值
		$teach_id=$_SESSION['session_log_id'];
		$class_subj=(strstr($teacher_course,'g'))? $teacher_course:$class_id."_".$subject_id;

		$test_name=$_SESSION['session_tea_name'].date("is"); 	//以教師中文姓名+分秒
		$curr_sort=$_POST['curr_sort'];

		$query="select max(freq) from $nor_score where class_subj='$class_subj' and stage='$curr_sort' and enable='1'";
		$res=$CONN->Execute($query);
		$next_freq=$res->fields[0]+1;						//第幾次
		$weighted=1;														//加權
		foreach ($STUD as $V) {
			$student_sn=$V['student_sn'];	
	  	$score=number_format($SCORES[$V['student_sn']]['avg'],2);
	  	$score=($score==0)?"-100":$score;
			$sql="replace into $nor_score (teach_id,stud_sn,class_subj,stage,test_name,test_score,weighted,enable,freq) values ('$teach_id','$student_sn','$class_subj','$curr_sort','$test_name','$score','$weighted','1','$next_freq')";
			$CONN->Execute($sql);
	  }
	  
	  $sql="update class_report_setup set locked=1 where sn='".$REP_SETUP['sn']."'";
	  $res=$CONN->Execute($sql) or die("SQL錯誤, query=".$sql);
	  
	  $INFO="已於".date("Y-m-d H:i:s")."新增一次第".$curr_sort."階段的平時成績《名稱:".$test_name."[加權1]》,請務必透過「成績管理」模組調整科目名稱及加權。";
}

//重設列入統計的成績
if ($_POST['act']=='check_real_sum') {
	$the_report=$_POST['the_report'];
	$sql="update class_report_test set real_sum='0' where report_sn='$the_report'";
	$res=$CONN->Execute($sql) or die("SQL錯誤, query=".$sql);
	
	foreach ($_POST['real_sum'] as $sn=>$v) {
		$sql="update class_report_test set real_sum='1' where sn='$sn'";
		$res=$CONN->Execute($sql) or die("SQL錯誤, query=".$sql);
	}
}
/*****************************************************/
//取得導師或級任班級
$class_num = get_teach_class();

//教師代號
$teacher_sn = $_SESSION[session_tea_sn];

//領域科目名稱
$subject_arr = get_subject_name_arr();

//取得本學期班級陣列
$class_name_arr = class_base();

//下拉選單變數轉換
$teacher_course = $_REQUEST[teacher_course];
/*
if (!empty($teacher_course)) {
	if (!check_course($teacher_sn,$teacher_course)) echo "get_out!";
}
*/
$curr_sort = $_REQUEST[curr_sort];

//階段名稱陣列
$test_sort_name=array("","第一階段","第二階段","第三階段","第四階段","第五階段","第六階段","第七階段","第八階段","第九階段","第十階段",255 => "全學期");

//2003-12-25新增，先取出分組課程的選單
$query="select * from score_ss where year='$sel_year' and semester='$sel_seme' and enable='1'";
$res=$CONN->Execute($query);
while(!$res->EOF) {
	$all_ss_id.="'".$res->fields[ss_id]."',";
	$res->MoveNext();
}
if ($all_ss_id) $all_ss_id=substr($all_ss_id,0,-1);
$sql_sub="select * from elective_tea where teacher_sn='$teacher_sn' and ss_id in ($all_ss_id)";
$rs_sub=$CONN->Execute($sql_sub) or creat_elective();
if($rs_sub){
	$sub=0;
	while(!$rs_sub->EOF){
		//一樣要找出科目名
		$group_id=$rs_sub->fields['group_id'];
		$group_name=$rs_sub->fields['group_name'];
		$ss_id=$rs_sub->fields['ss_id'];
		$cid=$rs_sub->fields['course_id'];

		//這個科目需要考試嗎？
		$query = "select need_exam from score_ss where ss_id='$ss_id' ";
		$res = $CONN->Execute($query) or trigger_error($query,E_USER_ERROR);
		$need_exam = $res->fields['need_exam'];
		if($need_exam){
			$class_subj=ss_id_to_class_subject_name($ss_id);
			$class_subj_group=$class_subj."-".$group_name;
			$gs_id=$group_id."g".$ss_id;
			$e_arr[$cid]=$gs_id;
			$course_arr[$gs_id]=$class_subj_group;
			$sub++;
		}
		$rs_sub->MoveNext();
	}
}

//取得正確任教課程
$course_arr_all=get_teacher_course(curr_year(),curr_seme(),$teacher_sn,$is_allow);

$allow_arr = $course_arr_all['allow'];
$course_arr = $course_arr_all['course'];

// 檢查課程權限是否正確
$cc_arr=array_keys($course_arr);
$err=(in_array($teacher_course,$cc_arr) || $teacher_course=="")?0:1;

// 建立學期成績資料表
//--------------------
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

// 建立平常成績資料表
//若是該學期的平時成績資料表不存在就依照命名規則自動建立一個 	 
$creat_table_sql="
	CREATE TABLE if not exists $nor_score ( 	 
	sn int(11) NOT NULL auto_increment, 	 
	teach_id varchar(20) NOT NULL default '', 	 
	stud_sn int(10) unsigned NOT NULL default '0', 	 
	class_subj varchar(40) NOT NULL default '', 	 
	stage tinyint(1) unsigned NOT NULL default '0', 	 
	test_name varchar(40) NOT NULL default '', 	 
	test_score float default '-100', 	 
	weighted int(2) NOT NULL default '1', 	 
	enable tinyint(1) unsigned NOT NULL default '1', 	 
	freq int(10) unsigned NOT NULL default '0', 	 
	PRIMARY KEY  (`sn`),
	KEY `teach_id` (`teach_id`,`stud_sn`))"; 	 
$rs=$CONN->Execute($creat_table_sql);

//科目下拉選單 -------------
$sel= new drop_select();
$sel->s_name = "teacher_course";
$sel->id = $teacher_course;
$sel->is_submit = true;
//$sel->other_script = "document.myform.the_report.value=''";
$sel->arr = $course_arr;
$sel->top_option = "選擇班級科目";
$sel->font_style="";
$sel->font_color = "#F71CFF";
$sel->is_bgcolor_list = true;
$course_sel = $sel->get_select();
//------------- 科目下拉選單 結束

if(strstr ($teacher_course, 'g')){

	//分組課程的階段下拉選單 ------------
	$teacher_course_arr=explode("g",$teacher_course);
	$group_id=$teacher_course_arr[0];
	$ss_id=$teacher_course_arr[1];

	//檢查是否是完整的課程，就是要月考的啦！
	$query = "select print from score_ss where ss_id='$ss_id' ";
	$res = $CONN->Execute($query) or trigger_error($query,E_USER_ERROR);
	$print = $res->fields['print'];

}else{
	//階段下拉選單 ------------
	$query = "select a.ss_id,a.class_id,b.print from score_course a, score_ss b where a.ss_id=b.ss_id and a.course_id='$teacher_course' and b.enable='1'";
	$res = $CONN->Execute($query) or trigger_error($query,E_USER_ERROR);
	$class_id = $res->fields['class_id'];
	$ss_id = $res->fields['ss_id'];
	$print = $res->fields['print'];
}

//取得所有學生資料
$all_sn="";
$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
if(strstr ($teacher_course, 'g')){
	$query = "select class_year from score_ss where ss_id='$ss_id'";
	$res = $CONN->Execute($query) or trigger_error($query,E_USER_ERROR);
	$class_year= $res->fields['class_year'];
	$teacher_course_arr=explode("g",$teacher_course);
	$query="select a.* from elective_stu a,stud_base b where a.student_sn=b.student_sn and a.group_id='$teacher_course_arr[0]' and b.stud_study_cond in ($in_study) order by b.curr_class_num";
}else{
	if ($class_id) $class_arr=class_id_2_old($class_id);
	$class_year=$class_arr[3];
	$query="select a.* from stud_seme a,stud_base b where a.student_sn=b.student_sn and a.seme_year_seme='$seme_year_seme' and a.seme_class='$class_arr[2]' and b.stud_study_cond in ($in_study) order by a.seme_num";
}
$res=$CONN->Execute($query) or trigger_error($query,E_USER_ERROR);
if ($res)
	while (!$res->EOF) {
		$student_sn=$res->fields['student_sn'];
		$all_sn.="'".$student_sn."',";
		$res->MoveNext();
	}
if ($all_sn) $all_sn=substr($all_sn,0,-1);

// 科目完整時(含階段及學期成績),才出現階段下拉選單
if ($print=="1") {
	$query = "select performance_test_times,score_mode,test_ratio from score_setup where  class_year='$class_year' and year=$sel_year and semester='$sel_seme' and enable='1'";
	$res = $CONN->Execute($query) or trigger_error($query,E_USER_ERROR);
	//測驗次數
	$performance_test_times = $res->fields[performance_test_times];
	//成績配分比例相關設定
	$score_mode = $res->fields[score_mode];
	//比率
	$test_ratios = $res->fields[test_ratio];

	if ($curr_sort <254 && $curr_sort> $performance_test_times)	$curr_sort='';
	//如果未選擇階段時自動取得下個階段
	//除平時成績外,階段成績須逐次匯到教務處
	$temp_script = '';
	if ($curr_sort=='' || ($_POST[curr_sort_hidden] <>'' and $curr_sort<>$_POST[curr_sort_hidden]) and $curr_sort<254) {
		//計算目前應在第幾階段 (sendmit = 0 表示已送至教務處成績)
		$query ="select max(test_sort) as mm from $score_semester where student_sn in ($all_sn) and ss_id='$ss_id' and sendmit='0' and test_sort<254";
		$res = $CONN->Execute($query) or trigger_error($query,E_USER_ERROR);
		$mm = $res->fields[0]+1;
		if ($curr_sort =='')	$curr_sort = $mm;
		if ($curr_sort>$performance_test_times)	$curr_sort = $performance_test_times;
			
	}
	//比率換算
	if($score_mode=="all"){
		$test_ratio=explode("-",$test_ratios);
	}
	elseif($score_mode=="severally"){
		$temp_arr=explode(",",$test_ratios);
		$i=$curr_sort-1;
		$test_ratio=explode("-",$temp_arr[$i]);
	}
	else{
		$test_ratio[0]=60;
		$test_ratio[1]=40;
	}


	//產生下拉選單項目陣列
	for($i=1;$i<= $performance_test_times;$i++)
		$test_times_arr[$i] = "第 $i 階段";

	//如果不是每一階段都有平時成績時,出現學期平時成績選項
	if ($yorn=='n')
		$test_times_arr[254] = "平時成績";
	
	//產生下拉選單
	$sel= new drop_select();
	$sel->s_name = "curr_sort";
	$sel->id = $curr_sort;
	$sel->is_submit = true;
	$sel->arr = $test_times_arr;
	$sel->font_style="";
	$sel->top_option = "選擇階段";
	$select_stage_bar = $sel->get_select();	
	//記住上次 curr_sort 值,做判別用
	$select_stage_bar .= "<input type=\"hidden\" name=\"curr_sort_hidden\" value=\"$curr_sort\">";

}
//全學期只輸入一次成績
else
	$curr_sort = 255;

//--------------階段下拉選單 結束
$check_allow = "";

//判別是否顯示允許導師修改功能
if($class_num) $class_num_temp=sprintf("%03d_%d_%02d_%02d",$sel_year,$sel_seme,substr($class_num,0,-2),substr($class_num,-2));
if ($is_allow=='y')
	
	if(($teacher_course) && ($class_id!=$class_num_temp)) {
	        if ($allow_arr[$teacher_course]=='1') {
			$check_allow = "<input type=\"submit\" name=\"need_allow\" value=\"取消\" style=\"border-style:solid; border-width:1px; font-size: 10pt; padding-top: 0; padding-bottom: 0; background-color:#EFDEC8\" >&nbsp<span style=\"font-size: 10pt; background-color:#FFF6C6\">級任導師不可修改本科成績(目前設定：導師不可修改)</span>";
	        }
	        else {
			$check_allow = "<input type=\"submit\" name=\"need_allow\" value=\"開啟\" style=\"border-style:solid; border-width:1px; font-size: 10pt; padding-top: 0; padding-bottom: 0; background-color:#C5CDEF\" >&nbsp<span style=\"font-size: 10pt; background-color:#FFF6C6\">級任導師不可修改本科成績(目前設定：導師可修改)</span>";
		}
	}

// 上方選單
$top_str = "<form action=\"$_SERVER[SCRIPT_NAME]\" name=\"myform\" method=\"post\">$course_sel &nbsp; $select_stage_bar &nbsp;$check_allow ";

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

//取得班級及科目名稱
$full_class_name = $course_arr[$teacher_course];


//秀出 SFS3 標題
head();

//列出選單
echo $tool_bar;

//檢驗身分, 並取出可讀取的成績單
if ($_SESSION['session_who']!='教師') {
		echo "此為教師專用功能!";
		exit();
}

?>

<font color="#0000FF"><b>請選擇要匯入的班級科目及階段:</b></font><br>
<?php
echo $top_str;

if ($_POST['teacher_course']) {
	
//$_POST['curr_sort']會自動選擇最新階段
//取得目前學期的所有可讀取的成績單

	$query = "select teacher_sn,class_id,ss_id from score_course where course_id='$_POST[teacher_course]'";
	$update_rs=$CONN->Execute($query);
	$teacher_sn=$update_rs->fields['teacher_sn'];
	$class_id=$update_rs->fields['class_id'];
	$ss_id=$update_rs->fields['ss_id'];
	$class_num=sprintf("%d%02d",substr($class_id,6,2),substr($class_id,9,2));
	$select_report=get_report("list",$c_curr_seme,$class_num);
?>
<br><font color="#0000FF"><b>請選擇您的小考成績單:</b></font><br>
	<input type="hidden" name="act" value="">
	<select size="1" name="the_report" onchange="document.myform.submit()">
		<option>--請選擇成績單--</option>
		<?php
		foreach ($select_report as $k=>$v) {
		?>
			<option value="<?php echo $v['sn'];?>"<?php if ($_POST['the_report']==$v['sn']) { echo " selected"; $OK=1; } ?>><?php echo "[".$v['seme_class_cname']."]".$v['title'];?></option>
		<?php
		}
		?>
	</select>	
<?php
	if ($_POST['the_report'] and $OK==1) {
			//檢查是否已匯入教務處
			$REP_SETUP=get_report_setup($_POST['the_report']);
			$STUD=get_seme_class_students($REP_SETUP['seme_year_seme'],$REP_SETUP['seme_class']);
			foreach ($STUD as $V) {
				$all_sn.="'".$V['student_sn']."',";
		  }
		  if ($all_sn) $all_sn=substr($all_sn,0,-1);
			$query = "select count(*) from $score_semester where student_sn in ($all_sn) and ss_id='$ss_id' and test_sort='".$_POST['curr_sort']."' and test_kind='定期評量' and sendmit='0'";
			$res= $CONN->Execute($query) or die("SQL錯誤, query=".$query);
			$is_send = $res->fields[0];
			
		if ($is_send==0 and $REP_SETUP['locked']==0) {		
		?>
		<input type="button" value="匯出平均分數至『平常成績』作為一次成績" style="color:#FF00FF" onclick="document.myform.act.value='SUBMIT_NORMAL';document.myform.submit()">
		<input type="button" value="直接匯出平均分數至『學期成績』的平常成績" style="color:#FF00FF" onclick="document.myform.act.value='SUBMIT_IT';document.myform.submit()">
		<?php
		} else {
			echo "<font color=red size=2><i>本階段成績已匯至教務處, 無法將小考成績再匯至學期或平常成績!!</i></font>";
		} 
		
		// end if is_send
		?>
		<table border="0">
			<tr>
				<td style="color:#FF0000"><?php echo $INFO;?></td>
			</tr>
		</table>
		<?php
		//列出成績
		list_class_score($REP_SETUP,0,1,1,1,1,1);
	
	
	} // end if ($_POST['the_report'])
} // end if ($_POST['teacher_course'] and $_POST['curr_sort'])

?>
</form>