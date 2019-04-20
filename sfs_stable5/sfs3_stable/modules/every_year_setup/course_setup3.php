<?php

// $Id: course_setup3.php 7705 2013-10-23 08:58:49Z smallduh $

/* 取得基本設定檔 */
require_once "config.php";
require_once "../../include/sfs_oo_zip2.php";
require_once "../../include/sfs_case_PLlib.php";
include ("$SFS_PATH/include/sfs_oo_overlib.php");

$m_arr = get_sfs_module_set();
extract($m_arr, EXTR_OVERWRITE);
$m_arr = &get_sfs_module_set('course_paper');
extract($m_arr, EXTR_OVERWRITE);
if ($midnoon=='') $midnoon=5;

//$CONN->debug = true;
sfs_check();

if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}


//若有選擇學年學期，進行分割取得學年及學期
if(!empty($year_seme)){
	$ys=explode("-",$year_seme);
	$sel_year=$ys[0];
	$sel_seme=$ys[1];
}

if(empty($sel_year))$sel_year = curr_year(); //目前學年
if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期

//錯誤設定
if($error==1){
	$act="error";
	$error_title="無年級和班級設定";
	$error_main="找不到第 ".$sel_year." 學年度，第 ".$sel_seme." 學期的年級、班級設定，故您無法使用此功能。<ol><li>請先到『<a href='".$SFS_PATH_HTML."modules/every_year_setup/class_year_setup.php'>班級設定</a>』設定年級以及班級資料。<li>以後記得每一學期的學期出都要設定一次喔！</ol>";
}

//執行動作判斷
if($act=="清除設定"){
	clear_class_table($sel_year,$sel_seme,$class_id);
	$msg="<font color='red'>所選課表已清除。</font><br><br>";
}

if($act=="快速設定介面"){
	header("location: course_setup3_prolin.php?act=list_class_table&sel_year=$sel_year&sel_seme=$sel_seme&class_id=$class_id");
}elseif ($act=="以教師設定介面"){
	header("location: course_setup3_teacher.php?act=list_class_table&sel_year=$sel_year&sel_seme=$sel_seme&class_id=$class_id");
}elseif($act=="error"){
	$main=&error_tbl($error_title,$error_main);
}elseif($act=="save"){
	save_class_table($sel_year,$sel_seme,$class_id,$ss_id,$teacher_sn,$room,$c_kind);
	$to=($go_on!="view_class")?"list_class_table":$go_on;
	header("location: {$_SERVER['PHP_SELF']}?act=$to&sel_year=$sel_year&sel_seme=$sel_seme&class_id=$class_id&set_teacher_sn=$set_teacher_sn");
}elseif($act=="list_class_table" or $act=="開始設定(舊)"){
	$act="list_class_table";
	$main=&list_class_table($sel_year,$sel_seme,$class_id);
}elseif($act=="delete"){
	$dd = explode("_",$sel);
	$query = "delete from score_course  where  day='$dd[0]' and sector='$dd[1]' and year=$sel_year and semester=$sel_seme and class_id='$class_id'";
	$CONN->Execute($query) or trigger_error("SQL 錯誤!! $query",E_USER_ERROR);
	$act="list_class_table";
	$main=&list_class_table($sel_year,$sel_seme,$class_id);

}elseif($act=="view_class" or $act=="觀看設定"){
	$act="view_class";
	$main=&list_class_table($sel_year,$sel_seme,$class_id,"view");
}elseif($act=="匯入課表" or $_POST['import']==1){
	include "course_setup_import.php";
	exit;
}elseif($act=="確定分派"){
	set_class_2_teacher($class_id,$sel_ss_id,$to_teacher_sn);
	header("location: {$_SERVER['PHP_SELF']}?act=view_class&sel_year=$sel_year&sel_seme=$sel_seme&class_id=$class_id");
}elseif($act=="重新設定"){
	$query = "delete from score_course where year=$sel_year and semester=$sel_seme and class_id='$class_id'";
	$CONN->Execute($query) or trigger_error("SQL 錯誤!! $query",E_USER_ERROR);
	header("location: {$_SERVER['PHP_SELF']}?act=view_class&sel_year=$sel_year&sel_seme=$sel_seme&class_id=$class_id");
}elseif($act=="downlod_ct"){	
	downlod_ct($class_id,$sel_year,$sel_seme);
	header("location: {$_SERVER['PHP_SELF']}?act=view_class&sel_year=$sel_year&sel_seme=$sel_seme&class_id=$class_id");
}else{
	$main=&class_form($sel_year,$sel_seme);
}


//秀出網頁
head("教師配課");

?>


<script language="JavaScript">
<!-- Begin
function jumpMenu(){
	location="<?php echo $_SERVER['PHP_SELF']?>?act=<?php echo $act;?>&sel_year=<?php echo $sel_year;?>&sel_seme=<?php echo $sel_seme;?>&set_teacher_sn=<?php echo $set_teacher_sn ?>&class_id=" + document.myform.class_id.options[document.myform.class_id.selectedIndex].value;
}
function openwindow(url_str){
window.open (url_str,"選擇教師","toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=no,copyhistory=no,width=600,height=420");
}


//  End -->
</script>


<?php
echo $main;
foot();

/*
函式區
*/

//基本設定表單
function &class_form($sel_year,$sel_seme){
	global $school_menu_p,$act,$msg;
	
	//取得年度與學期的下拉選單
	$date_select=&class_ok_setup_year($sel_year,$sel_seme,"year_seme","jumpMenu_seme");
	//年級與班級選單
	$class_select=&get_class_select($sel_year,$sel_seme,"","class_id");
	if(empty($class_select) or empty($date_select))	header("location:{$_SERVER['PHP_SELF']}?error=1");

	//說明
	$help_text="
	請選擇一個學年、學期以做設定。||
	<span class='like_button'>開始設定</span> 會開始進行該學年班級課表設定。||
	<span class='like_button'>觀看設定</span>會列出該學年學期班級課表的設定。
	";
	$help=&help($help_text);

	$tool_bar=&make_menu($school_menu_p);

	$main="
	<script language='JavaScript'>
	function jumpMenu_seme(){
		if(document.myform.year_seme.options[document.myform.year_seme.selectedIndex].value!=''){
			location=\"{$_SERVER['PHP_SELF']}?act=$act&year_seme=\" + document.myform.year_seme.options[document.myform.year_seme.selectedIndex].value;
		}
	}
	function check_ok(){
		if (document.myform.import.value=='' && document.myform.class_id.value=='' && document.myform.clear.value==''){
			alert('未選擇班級');
			return false;
		}
		else 
			return true;
	}
	function clear_data(){
		if (confirm('確定刪除課表?')){
			document.myform.clear.value='1';
			document.myform.submit();
		}
	}
	</script>
	$tool_bar
	<table bgcolor='#9EBCDD' cellspacing=1 cellpadding=4>
	<tr bgcolor='#FFFFFF'><td>
		<table>
		<form action='{$_SERVER['PHP_SELF']}' method='post' name='myform' onsubmit=\"return check_ok()\">
  		<tr><td>請選擇欲設定的學年度：</td><td>$date_select</td></tr>
		<tr><td>請選擇欲設定的班級：</td><td>$class_select</td></tr>
		<tr><td colspan='2'>
		<input type='submit' name='act' value='快速設定介面'>
		<input type='submit' name='act' value='以教師設定介面'>
		<input type='submit' name='act' value='開始設定(舊)' >
		<input type='submit' name='act' value='觀看設定'>
		<input type='submit' name='act' value='匯入課表' OnClick='this.form.import.value=1;this.form.submit();'>
		<input type='submit' name='act' value='清除設定'  onclick='if(confirm(\"真的要清除所選班級已設定的課表?  若未選定班級則會刪除所有班級的課表紀錄!\")){ return clear_data(); } else { alert(\"課表並未清除!\"); }'>
		<input type='hidden' name='clear' value=''>
		<input type='hidden' name='import' value=''>
		</td></tr>
		</form>
		</table>
	</td></tr>
	</table>
	<br>
	$help
	";
	return $main;
}




//列出某個班級的課表
function &list_class_table($sel_year,$sel_seme,$class_id="",$mode=""){
	global $CONN,$class_year,$conID,$weekN,$school_menu_p,$go_on,$debug,$midnoon,$SFS_PATH_HTML;

	$ol  = new overlib($SFS_PATH_HTML."include");
	$ol->ol_capicon=$SFS_PATH_HTML."images/componi.gif";
	$overss = $ol->over("您可以先篩選教師,便於操作","選擇教師");
	//取得班級資料
	$the_class=get_class_all($class_id);
	
	$class_data=class_id_2_old($class_id);
	$class_teacher=get_class_teacher($class_data[2]);
	$class_man=$class_teacher[name];
	$class_man_sn=$class_teacher[sn];
	
	//把某個課程的教師都指定給某人
	$set_class_teacher=&set_class_teacher($sel_year,$sel_seme,$class_id);
	
	//取得學年
	$semester_name=($sel_seme=='2')?"下":"上";
	$date_text="<font color='#607387'>
	<font color='#000000'>$sel_year</font> 學年
	<font color='#000000'>$semester_name</font>學期
	</font>
	<input type=hidden name=sel_year value='$sel_year'>
	<input type=hidden name=sel_seme value='$sel_seme'>
	";


	//每週的日數
	$dayn=sizeof($weekN)+1;
	
	//找出某班所有課程
	$sql_select = "select course_id,teacher_sn,day,sector,ss_id,room,c_kind from score_course where year=$sel_year AND semester=$sel_seme AND class_id='$class_id' order by day,sector";
	$recordSet=$CONN->Execute($sql_select) or trigger_error("錯誤訊息： $sql_select", E_USER_ERROR);
	while (list($course_id,$teacher_sn,$day,$sector,$ss_id,$room,$c_kind)= $recordSet->FetchRow()) {
		$k=$day."_".$sector;
		$a[$k]=$ss_id;
		$b[$k]=$teacher_sn;
		$r[$k]=$room;
		$ckind[$k]=$c_kind;
	}

	//取得星期那一列
	for ($i=1;$i<=count($weekN); $i++) {
		$main_a.="<td align='center' >星期".$weekN[$i-1]."</td>";
	}
	
	//取得考試所有設定
	$sm=&get_all_setup("",$sel_year,$sel_seme,$the_class[year]);
	$sections=$sm[sections];
	if($sections==0)
		trigger_error("請先設定 $sel_year 學年 $sel_seme 學期 [成績設定]項目,再操作課表設定<br><a href=\"$SFS_PATH_HTML/modules/every_year_setup/score_setup.php\">進入設定</a>",E_USER_ERROR);

	//年級與班級選單
	$class_select=&get_class_select($sel_year,$sel_seme,"","class_id","jumpMenu",$class_id);

	if(!empty($class_id)){
		$subject_name_arr =  &get_subject_name_arr();
		$sql_select="select ss_id,scope_id,subject_id,enable from score_ss where class_id='$class_id' and enable='1' order by sort,sub_sort";
		$res = $CONN->Execute($sql_select) or trigger_error("SQL 錯誤 $sql_select",E_USER_ERROR);
		if ($res->RecordCount() ==0){
			$sql_select="select ss_id,scope_id,subject_id,enable from score_ss where class_year='$the_class[year]' and year='$sel_year' and semester='$sel_seme' and enable='1' and class_id='' order by sort,sub_sort";
			$res = $CONN->Execute($sql_select) or trigger_error("SQL 錯誤 $sql_select",E_USER_ERROR);
		}
			
		while(!$res->EOF){
			$scope_id = $res->fields[scope_id];
			$subject_id = $res->fields[subject_id];
			
			$subject_name= $subject_name_arr[$subject_id][subject_name];
			if (empty($subject_name))
				$subject_name= $subject_name_arr[$scope_id][subject_name];

			if($subject_name_arr[$scope_id][enable])
				$subject_name =  "<font color='red'>$subject_name</font>";

			$select_ss_arr[$res->fields[ss_id]] = $subject_name;
			$res->MoveNext();
		}
		
			
		//取得教師陣列
		if (empty($_COOKIE[cookie_sel_teacher])){
			$tea_temp_arr = my_teacher_array();
		}
		else{

			$tea_temp_str = substr($_COOKIE[cookie_sel_teacher],0,-1);
			$query = "select teacher_sn,name from teacher_base where teacher_sn in($tea_temp_str) order by name";
			$res = $CONN->Execute($query);
			while(!$res->EOF){
				$tea_temp_arr[$res->fields[0]] = $res->fields[1];
				$res->MoveNext();
			}
		}
		$set_teacher_sn = $_GET[set_teacher_sn];
		if (empty($set_teacher_sn))
			$set_teacher_sn = $_POST[set_teacher_sn];
		$sel = new drop_select();
		$sel->id= $set_teacher_sn;
		$sel->s_name = "set_teacher_sn";
		$sel->arr = $tea_temp_arr;
		$sel->top_option = "選擇教師";
		$sel->is_submit = true;
		$set_teacher_sn_select = $sel->get_select();
			
		//新增一個下拉選單實例
		$sub_select = new drop_select();
			
		//所有教師一星期的排課情形陣列(判斷是否有衝堂)
		$course_tea_arr =  get_course_tea_arr($sel_year,$sel_seme);
		
		//班級的排課情形
		$course_class_arr = get_course_class_arr($sel_year,$sel_seme,$class_id);
		$def_color = $color;
		//取得課表
		for ($j=1;$j<=$sections;$j++){

			if ($j==$midnoon){
				$all_class.= "<tr bgcolor='white'><td colspan='$dayn' align='center'>午休</td></tr>\n";
			}


			$all_class.="<tr bgcolor='#E1ECFF'><td align='center'>$j</td>";
			
			//列印出各節			
			for ($i=1;$i<=count($weekN); $i++) {
				$color = $def_color;
				$k2=$i."_".$j;
				
				$del_link = "{$_SERVER['PHP_SELF']}?act=delete&sel_year=$sel_year&sel_seme=$sel_seme&class_id=$class_id&set_teacher_sn=$set_teacher_sn&teacher_sn=".$course_class_arr[$k2][teacher_sn];
				$teacher_sel='';
				$subject_sel='';
				$room_sel='';
				$re_set ='';
				$kind_check='';
				$checked=$course_class_arr[$k2][c_kind]?'checked':'';
				//教師在本班已有排課
				if ($course_class_arr[$k2][teacher_sn] == $set_teacher_sn && !empty($set_teacher_sn)) {
					$subject_sel =  $select_ss_arr[$a[$k2]];
					$tem_tea = $course_class_arr[$k2][teacher_sn];
					IF (empty($tem_tea))
						$tem_tea = $set_teacher_sn;
					$teacher_sel = "<font color='blue' size=2>".$course_class_arr[$k2][name]."</font>";
//					$teacher_sel .= "<input type=\"hidden\" name=\"teacher_sn[$k2]\" value=\"$tem_tea\">\n";
					$room_sel="<font color='#000000' size=2>".$r[$k2]."</font>";
					$re_set = "<a href=\"$del_link&sel=$k2\"><img src=\"images/remove.png\" border=0 alt=\"刪除\"></a>";
					$color=$checked?'#ccffaa': "#ffffff";
				}
				
				//教師在他班已有排課
				else if (!empty($course_tea_arr[$set_teacher_sn][$k2][ss_id])) {
					$temp_class_id = get_class_all($course_tea_arr[$set_teacher_sn][$k2][class_id]);
					$subject_sel = $select_ss_arr[$course_tea_arr[$set_teacher_sn][$k2][ss_id]]."<BR />";
					$subject_sel .= "<font size=2>$temp_class_id[name]<BR /></font>";
					$color=$checked?'#ccffaa': "#fff0a6";
				}
				//其他教師在本班已有排課
				else if(!empty($course_class_arr[$k2][teacher_sn])) {
					$teacher_sel = "<font color='blue' size=2>".$course_class_arr[$k2][name]."</font>";
					 $subject_sel =  $select_ss_arr[$a[$k2]];
					$room_sel="<font color='#000000' size=2>".$r[$k2]."</font>";
					$color=$checked?'#ccffaa': "#F5E5E5";
					$re_set = "<a href=\"$del_link&sel=$k2\"><img src=\"images/remove.png\" border=0 alt=\"刪除\"></a>";
				}
				//未排課
				else{
					//科目的下拉選單
					$sub_select->s_name = "ss_id[$k2]";
					$sub_select->id = $a[$k2];
					$sub_select->arr = $select_ss_arr;
					$subject_sel = $sub_select->get_select(); 
					$teacher_sel = "<font size=1 >上課教室:</font><input type=\"hidden\" name=\"teacher_sn[$k2]\" value=\"".$set_teacher_sn."\">\n";
					$room_sel =&select_room($sel_year,$sel_seme,"room[$k2]",$r[$k2]);
					
					$kind_check="兼課:<input type='radio' name='c_kind[$k2]' value='0' checked>否";
					$kind_check.="<input type='radio' name='c_kind[$k2]' value='1' $checked>是";
					$re_set ="";
				}
				
				//每一格
				$debug_str=($debug)?"<small><font color='#aaaaaa'>-".$a[$k2]."</font></small><br>":"";
				$all_class.="<td $align bgcolor='$color'>
				$subject_sel $re_set<br>$debug_str
				$teacher_sel<br>
				$room_sel 
				<br>$kind_check
				</td>\n";
			

			}

			$all_class.= "</tr>\n" ;
		}

		$submit=($mode=="view")?"
		<input type='hidden' name='act' value='list_class_table'>
		<input type='submit' value='修改課表'>":"
		<input type='hidden' name='act' value='save'>
		<input type='submit' value='儲存課表'>";

		//該班課表
		$main_class_list="
		<form action='{$_SERVER['PHP_SELF']}' method='post'>
		<tr bgcolor='#E1ECFF'><td align='center'>節</td>$main_a</tr>
		$all_class
		<tr bgcolor='#E1ECFF'><td colspan='6' align='center'>
		<input type='hidden' name='sel_year' value='$sel_year'>
		<input type='hidden' name='sel_seme' value='$sel_seme'>
		<input type='hidden' name='class_id' value='$class_id'>
		<input type='hidden' name='set_teacher_sn' value='$set_teacher_sn'>
		$submit
		</td></tr>
		";
	}else{
		$main_class_list="";
	}
	
	$tool_bar=&make_menu($school_menu_p);
	
	$checked=($go_on=="view_class")?"checked":"";
		
	$url_str =$SFS_PATH_HTML.get_store_path()."/sel_class.php";

	$open_window = "<a $overss  onclick=\"openwindow('$url_str')\" alt=\"選擇教師\"><img src='./images/wedit.png' border='0'></a>";
	$main="
	$tool_bar
	<table cellspacing=0 cellpadding=0><tr><td>
		<table border='0' cellspacing='1' cellpadding='4' bgcolor='#9EBCDD'>
		<form action='{$_SERVER['PHP_SELF']}' method='post' name='myform'>
		<input type='hidden' name='go_on' value='$go_on'>
		<input type='hidden' name='act' value='list_class_table'>
		<tr><td colspan='6' nowrap bgcolor='#FFFFFF'>
		
		$date_text ， $class_select &nbsp;&nbsp; 排課: $set_teacher_sn_select $open_window &nbsp;&nbsp;導師： $class_man 
		<a href='{$_SERVER['PHP_SELF']}?act=downlod_ct&class_id=$class_id&sel_year=$sel_year&sel_seme=$sel_seme'>
		<img src='images/dl_ct.png' alt='下載課表' width='84' height='24' hspace='6' vspace='0' border='0' align='middle'>
		</a>
		</tr>
		</form>
		$main_class_list
		</table>
	</td>
	<td valign='top' class='small' align='center'>
	$submit
	<p>
	$set_class_teacher
	</p>
	</td>
	</tr></table></form>
	";
	return  $main;
}




//儲存課表
function save_class_table($sel_year="",$sel_seme="",$class_id="",$ss_id="",$teacher_sn="",$room="",$c_kind=""){
	global $CONN;
	while(list($k,$v)=each($ss_id)){
		$kk=explode("_",$k);
		$day=$kk[0];
		$sector=$kk[1];

		$teacher=$teacher_sn[$k];
		$subject=$ss_id[$k];
		$r=$room[$k];
		$ckind=$c_kind[$k];
		//先取得看看有無課程
		$c=&get_course("",$day,$sector,$class_id);
		//假如沒有課程資料，資料庫中也無該課程，那麼跳過
		if(empty($subject) and empty($c[course_id]))continue;
		
		if(empty($c[course_id])){
			add_course($sel_year,$sel_seme,$teacher,$class_id,$day,$sector,$subject,$r,$ckind);
		}else{
			update_course($c[course_id],$sel_year,$sel_seme,$teacher,$class_id,$day,$sector,$subject,$r,$ckind);
		}

	}
	return ;
}

//儲存一筆課程資料（一班一天的某一節）
function add_course($sel_year,$sel_seme,$teacher,$class_id,$day,$sector,$subject,$room,$c_kind){
	global $CONN;
	//把class_id換成舊的學年
	$c=class_id_2_old($class_id);

	$sql_insert = "insert into score_course
	 (year,semester,class_id,teacher_sn, class_year,class_name,day,sector,ss_id,room,c_kind) values
	($sel_year,'$sel_seme','$class_id','$teacher','$c[3]','$c[4]','$day','$sector','$subject','$room','$c_kind')";
	if($CONN->Execute($sql_insert))	return true;
	die($sql_insert);
	return false;
}

//更新一筆課程資料（一班一天的某一節）
function update_course($course_id="",$sel_year="",$sel_seme="",$teacher,$class_id="",$day,$sector,$subject,$room,$c_kind){
	global $CONN;
	//把class_id換成舊的??~
	$c=class_id_2_old($class_id);

	if(!empty($course_id)){
		$where="where course_id = '$course_id'";
	}else{
		$where="where class_id = '$class_id'  and  day='$day'  and sector='$sector'";
	}
	$sql_update = "update score_course set year=$sel_year, semester='$sel_seme', class_id='$class_id',teacher_sn='$teacher', class_year='$c[3]',class_name='$c[4]', day='$day', sector='$sector', ss_id='$subject', room='$room',c_kind='$c_kind' $where";
//	echo $sql_update;
	$CONN->Execute($sql_update) or die($sql_update);
	return true;
}



//取得某一筆課程資料
function &get_course($course_id="",$day="",$sector="",$class_id=""){
	global $CONN;
	if(!empty($course_id)){
		$where="where course_id = '$course_id'";
	}else{
		$where="where class_id='$class_id' and day='$day' and sector='$sector'";
	}
	$sql_select = "select * from score_course $where";
	
	$recordSet=$CONN->Execute($sql_select) or die($sql_select);
	$array = $recordSet->FetchRow();
	return $array;
}

//教師名字陣列依姓名排列
function my_teacher_array(){
	global $CONN;
	$query= "select a.teacher_sn,a.name from teacher_base a,teacher_post b where a.teach_condition=0 and a.teacher_sn=b.teacher_sn  order by a.name ";
	$res=$CONN->Execute($query);
	$temp_arr = array();
	while(!$res->EOF){
		$temp_arr[$res->fields[0]] = $res->fields[1];
		$res->MoveNext();
	}
	return $temp_arr;
}


//把某個課程的教師都指定給某人
function &set_class_teacher($sel_year="",$sel_seme="",$class_id=""){
	global $CONN,$debug;
	//取得教師陣列
	if (empty($_COOKIE[cookie_sel_teacher])){
		$tea_temp_arr = my_teacher_array();
	}
	else{
		$tea_temp_str = substr($_COOKIE[cookie_sel_teacher],0,-1);
		$query = "select teacher_sn,name from teacher_base where teacher_sn in($tea_temp_str)";
		$res = $CONN->Execute($query);
		while(!$res->EOF){
			$tea_temp_arr[$res->fields[0]] = $res->fields[1];
			$res->MoveNext();
		}
	}
	$set_teacher_sn = $_GET[set_teacher_sn];
	if (empty($set_teacher_sn))
		$set_teacher_sn = $_POST[set_teacher_sn];
	$sel = new drop_select();
	$sel->id= $set_teacher_sn;
	$sel->s_name = "set_teacher_sn";
	$sel->arr = $tea_temp_arr;
	$sel->top_option = "選擇教師";
	$set_teacher_sn_select = $sel->get_select();
	$sel->id= $to_teacher_sn;
	$sel->s_name = "to_teacher_sn";
	$set_teacher_sn_select2 = $sel->get_select();

	//先取得該班已經有的課程
	$sql_select = "select a.ss_id,a.teacher_sn from score_course a left join score_ss b on a.ss_id=b.ss_id where a.day<>'' and a.class_id='$class_id' and a.year=$sel_year and a.semester=$sel_seme group by a.ss_id order by b.sort,b.sub_sort";

	$recordSet=$CONN->Execute($sql_select) or trigger_error("錯誤訊息： $sql_select", E_USER_ERROR);
	while(list($ss_id,$teacher_sn)= $recordSet->FetchRow()){
		$color=(empty($teacher_sn))?"white":"#D7D7D7";
		$font_color=(empty($teacher_sn))?"#303AFD":"#7B93AC";
		$debug_id=($debug)?$ss_id."-":"";
		$ss_sel.="<option value='$ss_id' style='background-color: $color; color: $font_color'>".$debug_id.get_ss_name("","","短",$ss_id)."</option>";
	}
	
	if(!empty($ss_sel)){
		$class_name = class_id_to_full_class_name($class_id);
		$all_ss_sel="<select name='sel_ss_id'>$ss_sel</select>";
		$main="
		<table cellspacing='1' cellpadding='4' align='center' bgcolor='#990000'>
		<form action='{$_SERVER['PHP_SELF']}' method='post'>
		<tr><td align='center'><font color='#FFFFFF'>課程指派</font></td></tr>
		<tr bgcolor='#FFFFFF'>
		<td class='small' align='center'>
		該班課程：<br>
		$all_ss_sel
		<p>
		都指定給：<br>
		$set_teacher_sn_select2
		</p>
		<input type='hidden' name='class_id' value='$class_id'>
		<input type='submit' name='act' value='確定分派'>
		</td></tr>
		</table>
		<br>
		<br>
		<table cellspacing='1' cellpadding='4' align='center' bgcolor=yellow>
		<tr><td align='center'>重新設定</td></tr>
		<tr bgcolor='#FFFFFF'>
		<td class='small' align='center'>
		<input type='submit' name='act' value='重新設定' onClick=\"return confirm('確定重新設定[$class_name] 課表？\\n該班本學期課表設定將刪除!!!');\">
		</td></tr>
		</table>
		</form>

		
		";
	}
	return $main;
}

//執行把某個課程的教師都指定給某人
function set_class_2_teacher($class_id,$sel_ss_id,$to_teacher_sn){
	global $CONN;
	$sql_update = "update score_course set teacher_sn='$to_teacher_sn' where class_id = '$class_id' and ss_id='$sel_ss_id'";
	$CONN->Execute($sql_update) or die($sql_update);
	return true;
}

//教室的下拉選單
function &select_room($sel_year,$sel_seme,$name="room",$now_room){
	global $CONN;
	$sql_select = "select room_name from spec_classroom order by room_name";
	$recordSet=$CONN->Execute($sql_select) or trigger_error("錯誤訊息： $sql_select", E_USER_ERROR);
	while (list($room)= $recordSet->FetchRow()) {
		$selected=($now_room==$room)?"selected":"";
		$data.="<option value='$room' $selected>$room</option>";
	}
	$main="<select name='$name' size='1'><option value='' selected></option>$data</select>";
	
	//$main="<input type='text' name='$name' value='$now_room' size='8'>";
	return $main;
}

//下載功課表
function downlod_ct($class_id="",$sel_year="",$sel_seme=""){
	global $CONN,$weekN,$school_kind_name,$midnoon;
	if(empty($class_id))trigger_error("無班級編號，無法下載。因為沒有接班級編號，故無法取得班級課程資料以便下載。", E_USER_ERROR);

	$oo_path = "ooo";
	
	
	$filename="course_".$class_id.".sxw";
	
	if(empty($class_id)){
		//取得任教班級代號
		$class_num=get_teach_class();
	}
	
	//取得班級資料
	$the_class=get_class_all($class_id);
	
	//每週的日數
	$dayn=sizeof($weekN)+1;
	
	$sql_select = "select course_id,teacher_sn,day,sector,ss_id,room,c_kind from score_course where class_id='$class_id' and year=$sel_year and semester=$sel_seme order by day,sector";

	$recordSet=$CONN->Execute($sql_select) or trigger_error("錯誤訊息： $sql_select", E_USER_ERROR);
	while (list($course_id,$teacher_sn,$day,$sector,$ss_id,$room,$c_kind)= $recordSet->FetchRow()) {
		$k=$day."_".$sector;
		$a[$k]=$ss_id;
		$b[$k]=$teacher_sn;
		$r[$k]=$room;
		$ckind[$k]=$c_kind;
	}
	
	
	//取得考試所有設定
	$sm=&get_all_setup("",$sel_year,$sel_seme,$the_class[year]);
	$sections=$sm[sections];
	if(!empty($class_id)){
		//取得課表
		for ($j=1;$j<=$sections;$j++){
			//若是最後一列要用不同的樣式
			$ooo_style=($j==$sections)?"4":"2";
			
			if ($j==$midnoon){
				//預設的午休OpenOffice.org表格程式碼
				$all_class.= "<table:table-row table:style-name=\"course_tbl.3\"><table:table-cell table:style-name=\"course_tbl.A3\" table:number-columns-spanned=\"6\" table:value-type=\"string\"><text:p text:style-name=\"P12\">午間休息</text:p></table:table-cell><table:covered-table-cell/><table:covered-table-cell/><table:covered-table-cell/><table:covered-table-cell/><table:covered-table-cell/></table:table-row>";
			}
			
			$all_class.="<table:table-row table:style-name=\"course_tbl.1\"><table:table-cell table:style-name=\"course_tbl.A".$ooo_style."\" table:value-type=\"string\"><text:p text:style-name=\"P8\">第 $j 節</text:p></table:table-cell>";
			//列印出各節
			$wn=count($weekN);
			for ($i=1;$i<=$wn;$i++) {
				//若是最後一格要用不同的樣式
				$ooo_style2=($i==$wn)?"F":"B";
			
				$k2=$i."_".$j;
				
				$teacher_search_mode=(!empty($tsn) and $tsn==$b[$k2])?true:false;
				//科目
				$subject_sel=&get_ss_name("","","短",$a[$k2]);
				
				//教師
				$teacher_sel=get_teacher_name($b[$k2]);
				//每一格
				$all_class.="<table:table-cell table:style-name=\"course_tbl.".$ooo_style2.$ooo_style."\" table:value-type=\"string\"><text:p text:style-name=\"P9\">$subject_sel</text:p><text:p text:style-name=\"P10\"><text:span text:style-name=\"teacher_name\">$teacher_sel</text:span></text:p></table:table-cell>";
			}
			$all_class.="</table:table-row>";
		}
		
	}else{
		$all_class="";
	}
	
	//轉換班級代碼
	$class=class_id_2_old($class_id);
	$class_teacher=get_class_teacher($class[2]);
	$class_man=$class_teacher[name];

	//取得學校資料
	$s=get_school_base();

	//新增一個 zipfile 實例
	$ttt = new EasyZip;
	$ttt->setPath($oo_path);
	$ttt->addDir('META-INF');
	$ttt->addfile("settings.xml");
	$ttt->addfile("styles.xml");
	$ttt->addfile("meta.xml");
	//讀出 content.xml 
	$data = $ttt->read_file(dirname(__FILE__)."/$oo_path/content.xml");

	//將 content.xml 的 tag 取代
	$temp_arr["city_name"] = "";//$s[sch_sheng];
	$temp_arr["school_name"] = $s[sch_cname];
	$temp_arr["Cyear"] = $stu[stud_name];
	$temp_arr["stu_class"] = $class[5];
	$temp_arr["teacher_name"] = $class_man;
	$temp_arr["year"] = $sel_year;
	$temp_arr["seme"] = $sel_seme;
	$temp_arr["all_course"] = $all_class;

	// change_temp 會將陣列中的 big5 轉為 UTF-8 讓 openoffice 可以讀出
	$replace_data = $ttt->change_temp($temp_arr,$data,0);
	
	// 加入 content.xml 到zip 中
	$ttt->add_file($replace_data,"content.xml");
	
	//產生 zip 檔
	$sss = & $ttt->file();

	//以串流方式送出 ooo.sxw
	header("Content-disposition: attachment; filename=$filename");
	//header("Content-type: application/octetstream");
	header("Content-type: application/vnd.sun.xml.writer");
	//header("Pragma: no-cache");
					//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");

	header("Expires: 0");

	echo $sss;
	
	exit;
	return;
}

//某年級的班級陣列，若沒指定年級則是全校
function get_Cyear_class_id($sel_year,$sel_seme,$cyear=""){
	global $CONN;
	$and_cyear=(empty($cyear))?"":"and c_year='$cyear'";
	$sql_select = "select class_id from school_class where year='$sel_year' and semester = '$sel_seme' and enable='1' $and_cyear order by c_year,c_sort";
	$recordSet=$CONN->Execute($sql_select)  or trigger_error($sql_select, E_USER_ERROR);
	$class_id=$recordSet->FetchRow();
	return $class_id;
}

//所有教師一星期的排課情形陣列(判斷是否有衝堂)
function get_course_tea_arr($sel_year,$sel_seme) {
	global $CONN;
	$query = "select ss_id ,class_id,day,sector,teacher_sn from score_course where year='$sel_year' and semester='$sel_seme' ";
	$res = $CONN->Execute($query) or trigger_error("SQL 錯誤",E_USER_ERROR);
	while (!$res->EOF) {
		$temp_ds = $res->fields[day]."_".$res->fields[sector];
		$temp_arr[$res->fields[teacher_sn]][$temp_ds][ss_id] = $res->fields[ss_id];
		$temp_arr[$res->fields[teacher_sn]][$temp_ds][class_id] = $res->fields[class_id];
		$res->MoveNext();
	}
	return $temp_arr;
}

//某班的排課情形
function get_course_class_arr($sel_year,$sel_seme,$class_id) {
	global $CONN;
	$query = "SELECT a.teacher_sn,a.ss_id,a.day,a.sector,a.c_kind,b.name FROM score_course a RIGHT JOIN teacher_base b ON a.teacher_sn=b.teacher_sn WHERE a.year='$sel_year' and a.semester='$sel_seme' and a.class_id='$class_id' ";
	$res = $CONN->Execute($query) or trigger_error("SQL 錯誤",E_USER_ERROR);
	while (!$res->EOF) {
		$temp_ds = $res->fields[day]."_".$res->fields[sector];
		$temp_arr[$temp_ds][teacher_sn]=$res->fields[teacher_sn];
		$temp_arr[$temp_ds][name]=$res->fields[name];
		$temp_arr[$temp_ds][ss_id]=$res->fields[ss_id];
		$temp_arr[$temp_ds][c_kind]=$res->fields[c_kind];
		$res->MoveNext();
	}
	return $temp_arr;
}

//清除課表
function clear_class_table($sel_year,$sel_seme,$class_id){
	global $CONN;
	$class_id_str=($class_id=="")?"":" and class_id = '$class_id'";
	$CONN->Execute("delete from score_course where year='$sel_year' and semester='$sel_seme' $class_id_str");
}
?>
