<?php
// $Id: manage2.php 8782 2016-01-19 05:12:10Z qfon $


/*引入學務系統設定檔*/
include "config.php";
include_once "../../include/sfs_case_PLlib.php";
include_once "../../include/sfs_case_score.php";
include_once "../../include/sfs_case_subjectscore.php";
include_once "../../include/sfs_case_studclass.php";
include_once "myfun2.php";
//使用者認證
sfs_check();
//取得本學年
$sel_year = curr_year();
//取得本學期
$sel_seme = curr_seme();
//學期資料表名稱
$score_semester="score_semester_".$sel_year."_".$sel_seme;

//取得排除名單
$student_out=get_manage_out($sel_year,$sel_seme);

//列印功能
$is_print=$_GET['is_print'];
//另開視窗
$is_openWin=$_GET['is_openWin'];

if ($_POST[dokey]=='儲存')
	save_semester_score($sel_year,$sel_seme);

else if ($_POST[dokey]=='匯到教務處')
	seme_score_input($sel_year,$sel_seme);

else if ($_POST[file_out]<>'')
	download_score($sel_year,$sel_seme);

else if ($_POST[file_in]<>'')
	import_score($sel_year,$sel_seme);

elseif($_POST['file_date']=="成績檔案匯入")
	save_import_score();

//仍需修改
if($_POST[need_allow]<>'' && $is_allow=='y'){
	if ($_POST[need_allow] =='取消')
		$need_allow=0;
	else
		$need_allow=1;
	$query = "select teacher_sn,class_id,ss_id from score_course where course_id='$_POST[teacher_course]'";
	$update_rs=$CONN->Execute($query);
	$teacher_sn=$update_rs->fields['teacher_sn'];
	$class_id=$update_rs->fields['class_id'];
	$ss_id=$update_rs->fields['ss_id'];
	$query = "UPDATE score_course SET allow='$need_allow' WHERE teacher_sn='$teacher_sn' and class_id='$class_id' and ss_id='$ss_id'";
	$CONN->Execute($query);
	//echo $query;
}

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
		  test_kind varchar(12) NOT NULL default '定期評量',
		  test_sort tinyint(3) unsigned NOT NULL default '0',
		  update_time datetime NOT NULL default '0000-00-00 00:00:00',
		  sendmit enum('0','1') NOT NULL default '1',
 		  teacher_sn smallint(6) NOT NULL default '0',
		  PRIMARY KEY  (student_sn,ss_id,test_kind,test_sort),
		  UNIQUE KEY score_id (score_id)  
                  )";
$CONN->Execute($creat_table_sql);

//科目下拉選單 -------------
$sel= new drop_select();
$sel->s_name = "teacher_course";
$sel->id = $teacher_course;
$sel->is_submit = true;
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
$top_str = "<form action=\"$_SERVER[SCRIPT_NAME]\" name=\"myform\" method=\"post\">$course_sel &nbsp; $select_stage_bar &nbsp;$check_allow </form>";

//檢查是否繳至教務處，($yorn模組變數:是否顯示平時成績)
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



if(($teacher_course)&&($curr_sort)){
	if ($is_openWin && $_GET[edit]=='s1')
		$url_str_1 = "a href=\"".$SFS_PATH_HTML.get_store_path()."/quick_input_m.php?edit=s1&class_id=$class_id&teacher_course=$teacher_course&ss_id=$ss_id&curr_sort=$curr_sort&KeepThis=true&TB_iframe=true&height=400&width=700\" class=\"thickbox\" id=\"openWin\"";
	else
		$url_str_1 = "a href=\"".$_SERVER['SCRIPT_NAME']."?edit=s1&teacher_course=$teacher_course&curr_sort=$curr_sort&is_openWin=1\"";
	if ($is_openWin && $_GET[edit]=='s2')
		$url_str_2 = "a href=\"".$SFS_PATH_HTML.get_store_path()."/quick_input_m.php?edit=s2&class_id=$class_id&teacher_course=$teacher_course&ss_id=$ss_id&curr_sort=$curr_sort&KeepThis=true&TB_iframe=true&height=400&width=700\" class=\"thickbox\" id=\"openWin\"";
	else
		$url_str_2 = "a href=\"".$_SERVER['SCRIPT_NAME']."?edit=s2&teacher_course=$teacher_course&curr_sort=$curr_sort&is_openWin=1\"";
	$main="	<small><a href='$_SERVER[SCRIPT_NAME]?teacher_course=$teacher_course&class_id=$class_id&ss_id=$ss_id&curr_sort=$curr_sort&is_print=1' target='new'>友善列印</a></small>
		<table bgcolor=#000000 border=0 cellpadding=2 cellspacing=1>
		<tr bgcolor=#ffffff align=center>
		<td>學號</td>
		<td>座號</td>
		<td>姓名</td>".($pic_checked?'<td>大頭照</td>':'');
	//班級代號
	$curr_class_temp = sprintf("%d%02d",$class_arr[3],$class_arr[4]);
	//學生ID hidden 值
	$temp_hidden = "";
	//平均成績 hidden 值
	$avg_temp_hidden = "";

	//階段成績
	if ($curr_sort<254){
		//把前幾次的平均列出來  by smallduh 2015.01.23 ================================
		if ($curr_sort>1) {
				$pre_text="";
       for ($PRE_SORT=1;$PRE_SORT<$curr_sort;$PRE_SORT++) {
       	$main.="<td>".$test_sort_name[$PRE_SORT]."平均</td>";
			 } // end for
    } // end if		
		//=================================================

		if ($yorn=='n'){
			$main .="<td>定期評量*".$test_ratio[0]."%";
			if ($is_send==0) $main.="<br><$url_str_1 title=\"".$full_class_name.$test_sort_name[$curr_sort]."定期評量\"><img src='./images/wedit.png' border='0'></a><a href=\"{$_SERVER['SCRIPT_NAME']}?edit=s1&teacher_course=$teacher_course&curr_sort=$curr_sort\"><img src='./images/pen.png' border='0'></a><a href=\"{$_SERVER['SCRIPT_NAME']}?del=ds1&edit=s1&teacher_course=$teacher_course&curr_sort=$curr_sort\" onClick=\"return confirm('確定刪除這次成績 ?');\"><img src='./images/del.png' border='0'></a></td>";
		}
		else {
			if($test_ratio[0]!=0) {
				$main.="<td>定期評量*".$test_ratio[0]."%";
				if ($is_send==0) $main.="<br><$url_str_1 title=\"".$full_class_name.$test_sort_name[$curr_sort]."定期評量\"><img src='./images/wedit.png' border='0'></a><a href=\"{$_SERVER['SCRIPT_NAME']}?edit=s1&teacher_course=$teacher_course&curr_sort=$curr_sort\"><img src='./images/pen.png' border='0'></a><a href=\"{$_SERVER['SCRIPT_NAME']}?del=ds1&edit=s1&teacher_course=$teacher_course&curr_sort=$curr_sort\" onClick=\"return confirm('確定刪除這次成績 ?');\"><img src='./images/del.png' border='0'></a></td>";
			}
                        if($test_ratio[1]!=0) {
                        	$main.="<td>平時成績*".$test_ratio[1]."%";
                        	if ($is_send==0) $main.="<br><$url_str_2 title=\"".$full_class_name.$test_sort_name[$curr_sort]."平時成績\"><img src='./images/wedit.png' border='0'></a><a href=\"{$_SERVER['SCRIPT_NAME']}?edit=s2&teacher_course=$teacher_course&curr_sort=$curr_sort\"><img src='./images/pen.png' border='0'></a><a href=\"{$_SERVER['SCRIPT_NAME']}?del=ds2&edit=s2&teacher_course=$teacher_course&curr_sort=$curr_sort\" onClick=\"return confirm('確定刪除這次成績 ?');\"><img src='./images/del.png' border='0'></a></td>";
                        }
		}
		$main .="<td>本次平均</td>";
		
		$main .="<tr>\n";
		
		
		
		//評量成績
		if ($yorn=='n'){
			if(strstr ($teacher_course, 'g')) {
				$teacher_course_arr=explode("g",$teacher_course);
				$group_id=$teacher_course_arr[0];
				$query = "select student_sn,test_kind,score from $score_semester where ss_id='$ss_id' and test_sort='$curr_sort' and test_kind='定期評量' and student_sn in ($all_sn)";
			}
			else $query = "select student_sn,test_kind,score from $score_semester where  ss_id='$ss_id' and test_sort='$curr_sort' and test_kind='定期評量' and student_sn in ($all_sn)";
		} else {
			if(strstr ($teacher_course, 'g')) {
				$teacher_course_arr=explode("g",$teacher_course);
				$group_id=$teacher_course_arr[0];
				$query = "select student_sn,test_kind,score from $score_semester where ss_id='$ss_id' and test_sort='$curr_sort' and student_sn in ($all_sn)";
			}
			else $query = "select student_sn,test_kind,score from $score_semester where ss_id='$ss_id' and test_sort='$curr_sort' and student_sn in ($all_sn)";
		}
		$res = $CONN->Execute($query) or trigger_error($query,E_USER_ERROR);
		$em=0;
		while(!$res->EOF){
			$tt =1;
			if ($res->fields[test_kind] =="定期評量")
				$tt = 0;
			$score_arr[$tt][$res->fields[student_sn]] = $res->fields[score];
			if ($res->fields[score]>-100)$em++;
			$res->MoveNext();
		}
		
		
		
		//載入前幾次評量成績 2015.01.23 by smallduh.=======================================
		$score_arr_pre=array();
		if ($curr_sort>1) {
		 for ($PRE_SORT=1;$PRE_SORT<$curr_sort;$PRE_SORT++) {
		  $query_pre = "select student_sn,test_kind,score from $score_semester where ss_id='$ss_id' and test_sort='$PRE_SORT' and student_sn in ($all_sn)";
			//echo  $query_pre."<br>";
			$res_pre = $CONN->Execute($query_pre) or trigger_error($query_pre,E_USER_ERROR);
			while(!$res_pre->EOF){
				$tt =1;
				if ($res_pre->fields[test_kind] =="定期評量")
				$tt = 0;
				$student_sn=$res_pre->fields[student_sn];
				$score_arr_pre[$PRE_SORT][$tt][$student_sn] = $res_pre->fields[score];
				//echo $res_pre->fields[test_kind]."$student_sn ($PRE_SORT , $tt) =>".$score_arr_pre[$PRE_SORT][$tt][$student_sn]."<br>";
				$res_pre->MoveNext();
		  } //end while
		 } // end for
		} // end if ($curr_sort>1)
		//=================================================================================
	
		//顯示學生成績
		if(strstr ($teacher_course, 'g')){
			//分組課程的階段下拉選單 ------------
			$teacher_course_arr=explode("g",$teacher_course);
			$group_id=$teacher_course_arr[0];
			$ss_id=$teacher_course_arr[1];
			$query="select stud_name,curr_class_num,student_sn,stud_id,stud_study_year from stud_base where student_sn in ($all_sn) order by curr_class_num ";
		}else{
			$query = "select stud_name,curr_class_num,student_sn,stud_id,stud_study_year from stud_base where student_sn in ($all_sn) order by curr_class_num";
		}
		$res = $CONN->Execute($query) or triger_error($query,E_USER_ERROR);
		$i=1;
		while(!$res->EOF){
			if(strstr ($teacher_course, 'g')) $stud_num = intval(substr($res->fields[curr_class_num],-4,-2))."-".intval(substr($res->fields[curr_class_num],-2));
			else $stud_num = intval(substr($res->fields[curr_class_num],-2));
			$student_sn = $res->fields[student_sn];
			$stud_name  = addslashes($res->fields[stud_name]);
			//排除名單加註*
      $stud_name.=($student_out[$student_sn])?"<font color=red>*</font>":"";
			$stud_id  = addslashes($res->fields[stud_id]);
			$stud_study_year=$res->fields[stud_study_year];
			
			if($pic_checked) {
				//印出照片
				$img=$UPLOAD_PATH."photo/student/".$stud_study_year."/".$stud_id; 
				$img_link=$UPLOAD_URL."photo/student/".$stud_study_year."/".$stud_id;			
				if (file_exists($img)) $pic_data="<td><img src='$img_link' width=$pic_width></td>"; else $pic_data="<td></td>";
			} else $pic_data="";
			
			
			if ($_GET[del]=='ds1')
				$score_1=-100;
			else
				$score_1 = $score_arr[0][$student_sn];			
			if ($score_1 == -100 || $score_1=="" )
				$score_1_s='';
			else $score_1_s=$score_1;
			if ($_GET[del]=='ds2')
				$score_2 = -100;
			else
				$score_2 = $score_arr[1][$student_sn];
			if ($score_2 == -100 || $score_2=="")
				$score_2_s='';
			else $score_2_s=$score_2;
			$red_1 = ($score_1>=60)?"#000000":"#ff0000";
			$red_2 = ($score_2>=60)?"#000000":"#ff0000";
			$bred_1 = ($score_1<60 && $score_1<>'')?"#ffaabb":"#FFFFFF";
			$bred_2 = ($score_2<60 && $score_2<>'')?"#ffaabb":"#FFFFFF";
			if ($_GET[edit]=='s1')
				$score1_text = "<td align=center ><input type=\"text\" size=6 name=\"s_$student_sn\" id=\"s_$student_sn\" value=\"$score_1_s\" style='background-color: $bred_1;' onBlur=\"unset_ower(this)\"></td>";
			else
				$score1_text = "<td align=center ><font color=$red_1>$score_1_s</font></td>";
			if ($_GET[edit]=='s2')
				$score2_text = "<td align=center ><input type=\"text\" size=6 name=\"s_$student_sn\" id=\"s_$student_sn\" value=\"$score_2_s\" style='background-color: $bred_2;' onBlur=\"unset_ower(this)\"></td>";
			else
				$score2_text = "<td align=center ><font color=$red_2>$score_2_s</font></td>";

			if ($score_1==-100 || $score_2==-100 || $score_1=="" || $score_2=="") {
				if ($score_1>0)
					$avg_score= $score_1_s;
				else
					$avg_score= $score_2_s;
			} else {
				$ratio_sum = $test_ratio[0]+$test_ratio[1];
				$avg_score = sprintf("%01.2f",($score_1*$test_ratio[0]+$score_2*$test_ratio[1])/$ratio_sum);
			}
			
		
			//列出前幾階段的平均成績 by smallduh. 2015.01.23 =======================
			//echo "<pre>";
			//print_r($score_arr_pre);
			//exit();

			if ($curr_sort>1) {
				$pre_text="";
       for ($PRE_SORT=1;$PRE_SORT<$curr_sort;$PRE_SORT++) {
     				
								$score_1 = $score_arr_pre[$PRE_SORT][0][$student_sn];			
								$score_2 = $score_arr_pre[$PRE_SORT][1][$student_sn];

							if ($score_1==-100 || $score_2==-100 || $score_1=="" || $score_2=="") {
								if ($score_1>0) {
									$avg_pre_score= $score_1;
								}elseif ($score_2>0) {
									$avg_pre_score= $score_2;
								} else {
									$avg_pre_score= "";
							  }
							} else {
								$ratio_sum = $test_ratio[0]+$test_ratio[1];
								$avg_pre_score = sprintf("%01.2f",($score_1*$test_ratio[0]+$score_2*$test_ratio[1])/$ratio_sum);
							}
       	 $red_3 = ($avg_pre_score>=60)?"#000000":"#ff0000";
				$pre_text.="<td><font color=$red_3>$avg_pre_score</font></td>"; 
       	
       } // end for			
		  } //end if
		  //======================================================================
			$red_3 = ($avg_score>=60)?"#000000":"#ff0000";
			$stud_num_arr[$i]=$stud_num;
			$stud_name_arr[$stud_num]=$stud_name;
			$stud_score_s_arr[$stud_num]=$score_1_s;
			$stud_score_n_arr[$stud_num]=$score_2_s;
			$stud_id_arr[$stud_num]=$stud_id;
			if ($yorn == 'n')
				$main .="<tr bgcolor=#FFFFFF align='center'><td>$stud_id</td><td>$stud_num</td><td>".stripslashes($stud_name)."</td>$pic_data $pre_text $score1_text <td><font color=$red_3>$avg_score</font></td></tr>\n";
			else
				$main .="<tr bgcolor=#FFFFFF align='center'><td>$stud_id</td><td>$stud_num</td><td>".stripslashes($stud_name)."</td>$pic_data $pre_text $score1_text $score2_text <td><font color=$red_3>$avg_score</font></td></tr>\n";
			$avg_temp_hidden .= "<input type=\"hidden\" name=\"avg_hidden_$student_sn\" value=\"$avg_score\">";
			$temp_hidden .="$student_sn,";
			$i++;
			$res->MoveNext();
		}
	}

	//學期成績
	elseif($curr_sort == 255){
		$main .="<td>學期成績";
		if ($is_send==0) $main.="<br><$url_str_2 title=\"".$full_class_name."全學期成績\"><img src='./images/wedit.png' border='0'></a><a href=\"{$_SERVER['SCRIPT_NAME']}?edit=s2&teacher_course=$teacher_course&curr_sort=$curr_sort\"><img src='./images/pen.png' border='0'></a><a href=\"{$_SERVER['SCRIPT_NAME']}?del=ds2&edit=s2&teacher_course=$teacher_course&curr_sort=$curr_sort\" onClick=\"return confirm('確定刪除這次成績 ?');\"><img src='./images/del.png' border='0'></a></td>";
		$main.="<td>平均</td></tr>\n";
		if(strstr ($teacher_course, 'g')) {
			$teacher_course_arr=explode("g",$teacher_course);
			$group_id=$teacher_course_arr[0];
			$query = "select student_sn,score from $score_semester where ss_id='$ss_id' and test_sort='255' and test_kind='全學期' and student_sn in ($all_sn)";
		}
		else $query = "select student_sn,score from $score_semester where ss_id='$ss_id' and test_sort='255' and test_kind='全學期' and student_sn in ($all_sn)";
		$res = $CONN->Execute($query) or trigger_error($query,E_USER_ERROR);
		while(!$res->EOF){
			$score_arr[$res->fields[student_sn]] = $res->fields[score];	
                        if($res->fields[score]>-100)$em++;
			$res->MoveNext();
		}
		
		
		if(strstr ($teacher_course, 'g')){
			//分組課程的階段下拉選單 ------------
			$teacher_course_arr=explode("g",$teacher_course);
			$group_id=$teacher_course_arr[0];
			$ss_id=$teacher_course_arr[1];
			$query="select stud_name,curr_class_num,student_sn,stud_id,stud_study_year from stud_base where student_sn in ($all_sn) order by curr_class_num";
		}else{
			//將班級字串轉為陣列
			$class_arr=class_id_2_old($class_id);
			$curr_class_temp = sprintf("%d%02d",$class_arr[3],$class_arr[4]);
			//顯示學生成績
			$query = "select stud_name,curr_class_num,student_sn,stud_id,stud_study_year from stud_base where student_sn in ($all_sn) order by curr_class_num";
		}
		$res = $CONN->Execute($query) or triger_error($query,E_USER_ERROR);
		$i=1;
		while(!$res->EOF){
			if(strstr ($teacher_course, 'g')) $stud_num = intval(substr($res->fields[curr_class_num],-4,-2))."-".intval(substr($res->fields[curr_class_num],-2));
			else $stud_num = intval(substr($res->fields[curr_class_num],-2));
			$stud_name  = addslashes($res->fields[stud_name]);
			$student_sn = $res->fields[student_sn];
			$stud_id  = $res->fields[stud_id];
			$stud_study_year= $res->fields[stud_study_year];
			
			if($pic_checked) {
				//印出照片
				$img=$UPLOAD_PATH."photo/student/".$stud_study_year."/".$stud_id; 
				$img_link=$UPLOAD_URL."photo/student/".$stud_study_year."/".$stud_id;			
				if (file_exists($img)) $pic_data="<td><img src='$img_link' width=$pic_width></td>"; else $pic_data="<td></td>";
			} else $pic_data="";
			
			if ($_GET[del]=='ds2')
				$score_2 = -100;
			else
				$score_2 = $score_arr[$student_sn];
			if ($score_2 == -100)
				$score_2='';
			$red_2 = ($score_2>=60)?"#000000":"#ff0000";
			$bred_2 = ($score_2<60 && $score_2<>'')?"#ffaabb":"#FFFFFF";
			if ($_GET[edit]=='s2')
				$score2_text = "<td align=center ><input type=\"text\" size=6 name=\"s_$student_sn\" id=\"s_$student_sn\" value=\"$score_2\" style='background-color: $bred_2;' onBlur=\"unset_ower(this)\"></td>";
			else
				$score2_text = "<td align=center ><font color=$red_2>$score_2</font></td>";

				$avg_score= $score_2;
			$red_3 = ($avg_score>=60)?"#000000":"#ff0000";
			$stud_num_arr[$i]=$stud_num;
			$stud_name_arr[$stud_num]=$stud_name;
			$stud_score_s_arr[$stud_num]=$score_2;
			$stud_id_arr[$stud_num]=$stud_id;
			
			$main .="<tr bgcolor=#FFFFFF align='center'><td>$stud_id</td><td>$stud_num</td><td>".stripslashes($stud_name)."</td>$pic_data $score2_text <td><font color=$red_3>$avg_score</font></td></tr>\n";
			$avg_temp_hidden .= "<input type=\"hidden\" name=\"avg_hidden_$student_sn\" value=\"$avg_score\">";
			$temp_hidden .="$student_sn,";
			$i++;
			$res->MoveNext();
		}
	}


	
	//平時成績
	elseif($curr_sort == 254) {
		$main .="<td>全學期平時成績";

		if ($is_send==0) $main.="<br><$url_str_2 title=\"".$full_class_name."全學期平時成績\"><img src='./images/wedit.png' border='0'></a><a href=\"{$_SERVER['SCRIPT_NAME']}?edit=s2&teacher_course=$teacher_course&curr_sort=$curr_sort\"><img src='./images/pen.png' border='0'></a><a href=\"{$_SERVER['SCRIPT_NAME']}?del=ds2&edit=s2&teacher_course=$teacher_course&curr_sort=$curr_sort\" onClick=\"return confirm('確定刪除這次成績 ?');\"><img src='./images/del.png' border='0'></a></td>";
		$main.="<td>平均</td> </tr>\n";

		
		$query = "select student_sn,score from $score_semester where  ss_id='$ss_id' and test_sort='1' and student_sn in ($all_sn) and test_kind='平時成績'";
		$res = $CONN->Execute($query) or trigger_error($query,E_USER_ERROR);
		while(!$res->EOF){
			$score_arr[$res->fields[student_sn]] = $res->fields[score];
			$res->MoveNext();
		}
			
		//將班級字串轉為陣列
		$class_arr=class_id_2_old($class_id);
		$curr_class_temp = sprintf("%d%02d",$class_arr[3],$class_arr[4]);
		//顯示學生成績
		$query = "select stud_name,curr_class_num,student_sn,stud_id,stud_study_year from stud_base where student_sn in ($all_sn) order by curr_class_num";
		$res = $CONN->Execute($query) or triger_error($query,E_USER_ERROR);
		$i=1;
		while(!$res->EOF){
			$stud_num = intval(substr($res->fields[curr_class_num],-2));
			$stud_name  = addslashes($res->fields[stud_name]);
			$student_sn = $res->fields[student_sn];
			$stud_id = $res->fields[stud_id];
			$stud_study_year= $res->fields[stud_study_year];
			
			if($pic_checked) {
				//印出照片
				$img=$UPLOAD_PATH."photo/student/".$stud_study_year."/".$stud_id; 
				$img_link=$UPLOAD_URL."photo/student/".$stud_study_year."/".$stud_id;			
				if (file_exists($img)) $pic_data="<td><img src='$img_link' width=$pic_width></td>"; else $pic_data="<td></td>";
			} else $pic_data="";
			
			
			if ($_GET[del]=='ds2')
				$score_2 = -100;
			else
				$score_2 = $score_arr[$student_sn];
			if ($score_2 == -100)
				$score_2='';
			$red_2 = ($score_2>=60)?"#000000":"#ff0000";
			$bred_2 = ($score_2<60 && $score_2<>'')?"#ffaabb":"#FFFFFF";
			if ($_GET[edit]=='s2')
				$score2_text = "<td align=center ><input type=\"text\" size=6 name=\"s_$student_sn\" id=\"s_$student_sn\" value=\"$score_2\" style='background-color: $bred_2;' onBlur=\"unset_ower(this)\"></td>";
			else
				$score2_text = "<td align=center ><font color=$red_2>$score_2</font></td>";
			$avg_score= $score_2;
			$red_3 = ($avg_score>=60)?"#000000":"#ff0000";
			$stud_num_arr[$i]=$stud_num;
			$stud_name_arr[$stud_num]=$stud_name;
			$stud_score_s_arr[$stud_num]=$score_2;
			$stud_id_arr[$stud_num]=$stud_id;
			$main .="<tr bgcolor=#FFFFFF align='center'><td>$stud_id</td><td>$stud_num</td><td>".stripslashes($stud_name)."</td>$pic_data $score2_text <td><font color=$red_3>$avg_score</font></td></tr>\n";
			$avg_temp_hidden .= "<input type=\"hidden\" name=\"avg_hidden_$student_sn\" value=\"$avg_score\">";
			$temp_hidden .="$student_sn,";
			$i++;
			$res->MoveNext();
		}
	}
	$main .="</tr>";
	$main .="</table>";
}



if ($is_print!=1) {
	head("成績列表");
	//列出橫向的連結選單模組
	$Link = "teacher_course=$teacher_course";
	print_menu($menu_p,$Link);
	if ($err==1) {
		$message="<table cellspacing=1 cellpadding=6 border=0 bgcolor='#FFF829' width='80%'><tr><td align='center'><h1><img src='../../images/warn.png' align='middle' border=0>操作權限不符</h1></font></td></tr><tr><td align='center' bgcolor='#FFFFFF' width='90%'>您並無本課程的操作權限！<br></td></tr><tr><td align=center><br></td></tr></table>";
		echo $message;
	} else {
		echo "<link href=\"../../themes/new/thickbox.css\" media=\"screen\" rel=\"stylesheet\" type=\"text/css\"><script type=\"text/javascript\" src=\"../../javascripts/thickbox.js\"></script><table border=0 cellspacing=1 cellpadding=2 width=100% bgcolor=#cccccc><tr><td bgcolor='#FFFFFF'>";
		echo $top_str;
		echo $temp_script;
		echo "<form name=\"form9\" method=\"post\" action=\"$_SERVER[SCRIPT_NAME]\">";
		echo $main;
		echo "
		<input type=\"hidden\" name=\"class_id\" value=\"$class_id\">
		<input type=\"hidden\" name=\"ss_id\" value=\"$ss_id\">
		<input type=\"hidden\" name=\"test_kind\" value=\"$_GET[edit]\">
		<input type=\"hidden\" name=\"test_sort\" value=\"$curr_sort\">
		<input type=\"hidden\" name=\"curr_sort\" value=\"$curr_sort\">
		<input type=\"hidden\" name=\"teacher_course\" value=\"$teacher_course\">
		<input type=\"hidden\" name=\"student_sn_hidden\" value=\"$temp_hidden\">
		<input type=\"hidden\" name=\"performance_test_times\" value=\"$performance_test_times\">";
		
		echo $avg_temp_hidden;

		if($_GET[edit]<>''){
			if ($is_send==0) echo "<input type=\"submit\" name=\"dokey\" id=\"save\" value=\"儲存\">";
			if ($curr_sort ==255)
				$io_test_name="學期成績";
			elseif($_GET[edit]=="s1")
				$io_test_name="定期評量";
			elseif($_GET[edit]=="s2")
				$io_test_name="平時成績";
			echo "
		        <input type=\"submit\" name=\"file_in\" value=匯入".$io_test_name.">
		        <input type=\"submit\" name=\"file_out\" value=匯出".$io_test_name.">";
		}
		if ($teacher_course!='' && $curr_sort!='' && $em){
			if (!$is_send)
				echo "<input type=\"submit\" name=\"dokey\" value=\"匯到教務處\" onclick=\"return confirmSubmit()\">";
			else
				echo "<br /><br /><font color=red>** 本項成績已匯至教務處,若有錯誤,請連絡教務處處理</font>";
		}
		echo "</td></tr></table>";
		echo "</form>";
//2015.10.24 by smallduh	
//如果有階段成績
if (count($score_arr) and $score_analyse) {
	//成績分析
	$analyse_table=array();
	foreach ($score_arr as $t=>$analyse) {
		$total=0;
		$count_number=0;
		//將陣列由高分排列
		arsort($analyse);
		//暫存用, 拾棄 -100分的
		$temp_arr=array();
	   foreach ($analyse as $v) {	 
	   	if ($v>=0) {
	   		$temp_arr[]=$v;
	   		//echo $v."<br>";
	   	 $count_number++;
	   	 $total+=$v;
	   	 $m=floor($v/10);
	   	 if ($m<6) {
	   	   $analyse_table[$t][5]++;
	   	 } else {
	   	   $analyse_table[$t][$m]++;
	   	 }
	    }
	   } // end foreach
	   $analyse_table[$t]['avg']=round($total/$count_number,2);
	   //標準差
	   $analyse_table[$t]['standv']=standv($temp_arr);
	   //求高低標
	   $corner=round(count($temp_arr)*0.5);
	   
	   
	   //求高標
	   arsort($temp_arr);
	   $total=0;
	   for ($i=0;$i<$corner;$i++) {
	   	$total+=$temp_arr[$i];
	   }
	   $analyse_table[$t]['high_avg']=round($total/$corner,2);
	   //求低標
	   sort($temp_arr);
	   $total=0;
	   for ($i=0;$i<$corner;$i++) {
	   	$total+=$temp_arr[$i];
	   }
	   $analyse_table[$t]['low_avg']=round($total/$corner,2);
	} // end foreach
	?>
※<?= $test_sort_name[$curr_sort]?>成績分析
<table border=0>
	<tr>
		<td valign="top">
			<!--成績分析 -->
			<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111'>
 <tr>
 	<td width='100' align='center'>\</td>
 	<td width='80' align='center'>定期評量</td>
 	<td width='80' align='center'>平時成績</td>
 </tr>	
 <tr>
 	<td align='center'>100分</td>
 	<td align='center'><?= $analyse_table[0][10]?></td>
 	<td align='center'><?= $analyse_table[1][10]?></td>
 </tr>	
 <tr>
 	<td align='center'>90分~99分</td>
 	<td align='center'><?= $analyse_table[0][9]?></td>
 	<td align='center'><?= $analyse_table[1][9]?></td>
 </tr>	
 <tr>
 	<td align='center'>80分~89分</td>
 	<td align='center'><?= $analyse_table[0][8]?></td>
 	<td align='center'><?= $analyse_table[1][8]?></td>
 </tr>	
 <tr>
 	<td align='center'>70分~79分</td>
 	<td align='center'><?= $analyse_table[0][7]?></td>
 	<td align='center'><?= $analyse_table[1][7]?></td>
 </tr>	
  <tr>
 	<td align='center'>60分~69分</td>
 	<td align='center'><?= $analyse_table[0][6]?></td>
 	<td align='center'><?= $analyse_table[1][6]?></td>
 </tr>	
  <tr>
 	<td align='center'>59分以下</td>
 	<td align='center'><?= $analyse_table[0][5]?></td>
 	<td align='center'><?= $analyse_table[1][5]?></td>
 </tr>
 <tr>
 	<td align='center'>平均</td>
 	<td align='center'><?= $analyse_table[0]['avg']?></td>
 	<td align='center'><?= $analyse_table[1]['avg']?></td>
 </tr>		
 <tr>
 	<td align='center'>高標</td>
 	<td align='center'><?= $analyse_table[0]['high_avg']?></td>
 	<td align='center'><?= $analyse_table[1]['high_avg']?></td>
 </tr>
  <tr>
 	<td align='center'>低標</td>
 	<td align='center'><?= $analyse_table[0]['low_avg']?></td>
 	<td align='center'><?= $analyse_table[1]['low_avg']?></td>
 </tr>		
  <tr>
 	<td align='center'>標準差</td>
 	<td align='center'><?= round($analyse_table[0]['standv'],2)?></td>
 	<td align='center'><?= round($analyse_table[1]['standv'],2)?></td>
 </tr>
</table>	
		</td>	
		<td valign="top">
			<!--公式說明 -->
			※公式說明：<br>
			。平均：即算術平均數 <img src="./images/average.png"><br>
			。高標：全班分數前 50% 學生的算術平均數。<br>
			。低標：全班分數後 50% 學生的算術平均數。<br>
			。標準差：<img src="./images/standv.png">
		</td>
	
	</tr>

</table>

<?php
 }	// end if (count($score_arr)) {

}
		
	foot();
	
} else {
	$query="select sch_cname from school_base";
	$res=$CONN->Execute($query);
	$school_name=$res->fields[sch_cname];
	if ($curr_sort<250) {
		$sort_str="第 ".$curr_sort." 階段";
		$sort_kind="（".$class_name_kind_1[$curr_sort]."）";
	} elseif ($curr_sort==254) {
		$sort_str="平時成績";
		$sort_kind="平時成績";
	} elseif ($curr_sort==255) {
		$sort_str="學期成績";
		$sort_kind="學期成績";
		
	}
	$endNumber  = end($stud_num_arr);
	if ($endNumber<=40) $endNumber = 40;
	echo "	<html><head><meta content='text/html; charset=big5' http-equiv='Content-Type'><title>成績單</title></head>
		<style>  
		    p {line-height:12pt}
		</style>
		<SCRIPT LANGUAGE=\"JavaScript\">
		<!--
		function pp() {   
			if (window.confirm('開始列印？')){
			self.print();}
		}
		//-->
		</SCRIPT>			
		<body onload=\"pp();return true;\">
		<table width='640' style='border-collapse: collapse;' cellpadding='0' cellspacing='0' border='0'>
		<tbody>
		<tr>
		<td style='padding: 0cm 1.0pt;' valign='middle' width='640'>
		<table width='640' height='600' style='border-collapse: collapse; text-align: center;' cellpadding='0' cellspacing='0' border='0'>
		<tbody>
		<tr style='height: 30pt;'>
		<td colspan='5'><font size='5' face='標楷體'>$school_name</font><br><font size='1'><br></font><font face='Dotum'>$sel_year 學年度第 $sel_seme 學期$sort_str</font>
		<td width='40' rowspan='".($endNumber+4)."'>
		<td colspan='5'><font size='5' face='標楷體'>$school_name</font><br><font size='1'><br></font><font face='Dotum'>$sel_year 學年度第 $sel_seme 學期$sort_str</font></tr>
		<tr style='height: 30pt;'>
		<td align='center' height='40' style='border-style: solid; border-color: windowtext; border-width: 1.5pt 1.5pt 0.75pt 1.5pt; padding: 0cm 1.4pt;' width='300' colspan='5'><font size='2'>".$course_arr[$teacher_course]."科成績登記表</font></td>
		<td align='center' height='40' style='border-style: solid; border-color: windowtext; border-width: 1.5pt 1.5pt 0.75pt 1.5pt; padding: 0cm 1.4pt;' width='300' colspan='5'><font size='2'>".$course_arr[$teacher_course]."科成績登記表</font></td>
		</tr>
		<tr style='height: 20pt;'>
		<td align='center' height='20' style='border-left:1.5pt solid windowtext; border-right:0.75pt solid windowtext; border-top:0.75pt solid windowtext; border-bottom:0.75pt solid windowtext; padding-left:1.4pt; padding-right:1.4pt; padding-top:0cm; padding-bottom:0cm' width='150' colspan='2' rowspan='2'><p align='right'><font face='Dotum' size='2'>次別</font></p><p><font face='Dotum' size='2'>分數</font></p><p align='left'><font face='Dotum' size='2'>姓名</font></p></td>
		<td align='center' height='20' style='border-top: 0.75pt solid windowtext; border-right: 1.5pt solid windowtext; border-bottom: 0.75pt solid windowtext; text-align: center; padding-left:1.4pt; padding-right:1.4pt; padding-top:0cm; padding-bottom:0cm' width='150' colspan='3'><font size='2'>$sort_kind</td>
		<td align='center' height='20' style='border-left:1.5pt solid windowtext; border-right:0.75pt solid windowtext; border-top:0.75pt solid windowtext; border-bottom:0.75pt solid windowtext; padding-left:1.4pt; padding-right:1.4pt; padding-top:0cm; padding-bottom:0cm' width='150' colspan='2' rowspan='2'><p align='right'><font face='Dotum' size='2'>次別</font></p><p><font face='Dotum' size='2'>分數</font></p><p align='left'><font face='Dotum' size='2'>姓名</font></p></td>
		<td align='center' height='20' style='border-top: 0.75pt solid windowtext; border-right: 1.5pt solid windowtext; border-bottom: 0.75pt solid windowtext; text-align: center; padding-left:1.4pt; padding-right:1.4pt; padding-top:0cm; padding-bottom:0cm' width='150' colspan='3'><font size='2'>$sort_kind</font></td>
		</tr>
		<tr style='height: 20pt;'>
		<td align='center' height='19' style='border-top: 0.75pt solid windowtext; border-right: 0.75pt solid windowtext; border-bottom: 0.75pt solid windowtext; text-align: center; padding-left:1.4pt; padding-right:1.4pt; padding-top:0cm; padding-bottom:0cm' width='50'><font size='2'>定期</font><p><font size='2'>考查</font></td>
		<td align='center' height='19' style='border-width: 1.5pt 1px 0.75pt medium; border-top: 0.75pt solid windowtext; border-right: 1px solid windowtext; border-bottom: 0.75pt solid windowtext; padding: 0cm 1.4pt;' width='50'><font size='2'>日常</font><p><font size='2'>考查</font></td>
		<td align='center' height='19' style='border-style: solid solid solid none; border-width: 1.5pt 1.5pt 0.75pt medium; border-top: 0.75pt solid windowtext; border-right: 1.5pt solid windowtext; border-bottom: 0.75pt solid windowtext; padding: 0cm 1.4pt;' width='50'><font size='2'>備</font><p><font size='2'>註</font></td>
		<td align='center' height='19' style='border-top: 0.75pt solid windowtext; border-right: 0.75pt solid windowtext; border-bottom: 0.75pt solid windowtext; text-align: center; padding-left:1.4pt; padding-right:1.4pt; padding-top:0cm; padding-bottom:0cm' width='50'><font size='2'>定期</font><p><font size='2'>考查</font></td>
		<td align='center' height='19' style='border-width: 1.5pt 1px 0.75pt medium; border-top: 0.75pt solid windowtext; border-right: 1px solid windowtext; border-bottom: 0.75pt solid windowtext; padding: 0cm 1.4pt;' width='50'><font size='2'>日常</font><p><font size='2'>考查</font></td>
		<td align='center' height='19' style='border-style: solid solid solid none; border-width: 1.5pt 1.5pt 0.75pt medium; border-top: 0.75pt solid windowtext; border-right: 1.5pt solid windowtext; border-bottom: 0.75pt solid windowtext; padding: 0cm 1.4pt;' width='50'><font size='2'>備</font><p><font size='2'>註</font></td>
		</tr>
		";
 
		for ($i=1;$i<=$endNumber;$i++) {
			$bm=($i % 5 ==0)?"1.5pt":"0.75pt";
			$j=(strstr($teacher_course,'g'))?$stud_num_arr[$i]:$i;
			echo "	<tr>
				<td align='right' height='15' style='border-left:1.5pt solid windowtext; border-right:0.75pt solid windowtext; border-bottom:$bm solid windowtext; border-top-color:windowtext; padding-left:1.4pt; padding-right:1.4pt; padding-top:0cm; padding-bottom:0cm' width='30' valign='middle'><font face='Dotum' size='2'>$j</font></td>
				<td align='center' height='15' style='border-right: 0.75pt solid windowtext; border-bottom: $bm solid windowtext; border-left-style:none; border-left-width:medium; border-top-color:windowtext; padding-left:1.4pt; padding-right:1.4pt; padding-top:0cm; padding-bottom:0cm' width='44' nowrap='nowrap' valign='middle'><font face='新細明體' size='2'>".stripslashes($stud_name_arr[$j])."</font></td>
				<td align='right' height='15' style='border-style: none solid solid none; border-width: medium 0.75pt 0.75pt medium; border-right: 0.75pt solid windowtext; border-bottom: $bm solid windowtext; padding: 0cm 1.4pt;' width='33' valign='middle'><font size='2' face='Dotum'>".$stud_score_s_arr[$j]."　</font></td>
				<td align='right' height='15' style='border-style: none solid solid none; border-width: medium 0.75pt 0.75pt medium; border-right: 0.75pt solid windowtext; border-bottom: $bm solid windowtext; padding: 0cm 1.4pt;' width='33' valign='middle'><font size='2' face='Dotum'>".$stud_score_n_arr[$j]."　</font></td>
				<td align='right' height='15' style='border-style: none solid solid none; border-width: medium 1.5pt 0.75pt medium; border-right: 1.5pt solid windowtext; border-bottom: $bm solid windowtext; padding: 0cm 1.4pt;' width='14' valign='middle'></td>
				
				<td align='right' height='15' style='border-left:1.5pt solid windowtext; border-right:0.75pt solid windowtext; border-bottom:$bm solid windowtext; border-top-color:windowtext; padding-left:1.4pt; padding-right:1.4pt; padding-top:0cm; padding-bottom:0cm' width='30'><font face='Dotum' size='2'>$j</font></td>
				<td align='center' height='15' style='border-right: 0.75pt solid windowtext; border-bottom: $bm solid windowtext; border-left-style:none; border-left-width:medium; border-top-color:windowtext; padding-left:1.4pt; padding-right:1.4pt; padding-top:0cm; padding-bottom:0cm' width='44' nowrap='nowrap'><font face='新細明體' size='2'>".stripslashes($stud_name_arr[$j])."</font></td>
				<td align='right' height='15' style='border-style: none solid solid none; border-width: medium 0.75pt 0.75pt medium; border-right: 0.75pt solid windowtext; border-bottom: $bm solid windowtext; padding: 0cm 1.4pt;' width='33'><font size='2' face='Dotum'>".$stud_score_s_arr[$j]."　</font></td>
				<td align='right' height='15' style='border-style: none solid solid none; border-width: medium 0.75pt 0.75pt medium; border-right: 0.75pt solid windowtext; border-bottom: $bm solid windowtext; padding: 0cm 1.4pt;' width='33'><font size='2' face='Dotum'>".$stud_score_n_arr[$j]."　</font></td>
				<td align='right' height='15' style='border-style: none solid solid none; border-width: medium 1.5pt 0.75pt medium; border-right: 1.5pt solid windowtext; border-bottom: $bm solid windowtext; padding: 0cm 1.4pt;' width='14'></td>
				</tr>";
		}
		echo "	<tr>
			<td align='left' colspan='6'><br><font size='2'>　　任課教師簽名<u>&nbsp;　　　　　　　　　　　&nbsp;</u><br>　　　　　　　　　　　(教師存查)<br>　　　　※請保留至學期末以備隨時調閱</font></td>
			<td align='left' colspan='6'><br><font size='2'>　　任課教師簽名<u>&nbsp;　　　　　　　　　　　&nbsp;</u><br>　　　　　　　　　　　(教務處存查)<br>　　　　※請於段考後一週內擲回教務處</font></td>
			</tr>
			</tbody></table></td></tr></tbody></table></body></html>";
}
//傳入陣列的標準差公式
function standv($m=array())
{
	$num=count($m);
	$total=0;
	foreach ($m as $v) {	
		$total+=$v;
  	//$numx+=$v*$v;
	}
 	$avg=$total/$num;
 	
 	$total=0;
	foreach ($m as $v) {	
		$total+=($v-$avg)*($v-$avg);
  	//$numx+=$v*$v;
	}
 	return sqrt($total/($num-1));
 	/*
 	//所有平方和
 	$numx=0;
 	$total=0;
	foreach ($m as $v) {	
		$total+=$v;
  	$numx+=$v*$v;
	}
	
   $numall=$total*$total;
//所有平方和: $numx 
//所有和的平方 : $numall
//echo "所有和的平方: $numall <br>";
 $S2=(($num*$numx)-$numall)/($num*$num);
 return sqrt($S2);
 */
}//end function
?> 
<script language="JavaScript1.2">
<!-- Begin
<?php
//另開視窗
if ($is_openWin) echo '
$(function() {
	$("#openWin").trigger("click");
});
';
//是否由平時成績匯入
if ($_GET[is_ok]==1) echo "alert ('平時成績匯入成功 !! ');\n";
?>

function confirmSubmit(){
	return confirm('確定要送到教務處？一旦送出之後您將無法在更改，如需更改請洽教務處');	
}

function closeThickbox(){
	tb_remove();
	$("#save").trigger('click');
}

function unset_ower(thetext) {
	if(thetext.value>100){ thetext.style.background = '#FF0000'; alert("輸入成績高於100分");}
	else if(thetext.value<0){ thetext.style.background = '#AA5555'; alert("輸入成績為負數"); }
	else { thetext.style.background = '#FFFFFF'; }
	return true;
}
//  End -->
</script>
