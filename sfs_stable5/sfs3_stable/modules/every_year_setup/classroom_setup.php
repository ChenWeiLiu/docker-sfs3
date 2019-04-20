<?php

// $Id: classroom_setup.php 7705 2013-10-23 08:58:49Z smallduh $

/* 取得基本設定檔 */
require_once "config.php";
require_once "../../include/sfs_oo_zip2.php";
require_once "../../include/sfs_case_PLlib.php";
include ("$SFS_PATH/include/sfs_oo_overlib.php");

$m_arr = get_sfs_module_set();
extract($m_arr, EXTR_OVERWRITE);

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
if($act=="error"){
	$main=&error_tbl($error_title,$error_main);
}elseif($act=="save"&&$room_id){
	save_room_table($sel_year,$sel_seme,$room_id,$set_id);
	$to=($go_on!="view_room")?"list_room_table":$go_on;
	header("location: {$_SERVER['PHP_SELF']}?act=$to&sel_year=$sel_year&sel_seme=$sel_seme&room_id=$room_id");
}elseif(($act=="list_room_table" or $act=="開始設定")&&$room_id){
	$act="list_room_table";
	$main=&list_room_table($sel_year,$sel_seme,$room_id);
}elseif(($act=="view_room" or $act=="觀看設定")&&$room_id){
	$act="view_room";
	$main=&list_room_table($sel_year,$sel_seme,$room_id,"view");
}elseif($act=="刪除教室"&&$room_id){
	$main=del_classroom($room_id);
	header("location: {$_SERVER['PHP_SELF']}");
}elseif($act=="修改教室"&&$room_id){
	$main=edit_classroom($room_id,$room_name,$enable,$notfree_time);
	header("location: {$_SERVER['PHP_SELF']}");
}elseif($act=="增加教室"&&$room_name){
	$main=add_classroom($room_name,$enable,$notfree_time);
	header("location: {$_SERVER['PHP_SELF']}");
}elseif($act=="重新設定"){
	$query = "delete from score_course where year=$sel_year and semester=$sel_seme and class_id='$class_id'";
	$CONN->Execute($query) or trigger_error("SQL 錯誤!! $query",E_USER_ERROR);
	header("location: {$_SERVER['PHP_SELF']}?act=view_class&sel_year=$sel_year&sel_seme=$sel_seme&class_id=$class_id");
}elseif($act=="downlod_ct"){	
	downlod_ct($class_id,$sel_year,$sel_seme);
	header("location: {$_SERVER['PHP_SELF']}?act=view_class&sel_year=$sel_year&sel_seme=$sel_seme&class_id=$class_id");
}else{
	$main=&room_form($sel_year,$sel_seme,$room_id);
}


//秀出網頁
head("專科教室設定");
echo $main;
foot();

/*
函式區
*/

//基本設定表單
function &room_form($sel_year,$sel_seme,$room_id){
	global $CONN,$school_menu_p,$act;
	
	//取得年度與學期的下拉選單
	$date_select=&class_ok_setup_year($sel_year,$sel_seme,"year_seme","jumpMenu_seme");
	//專科教室選單
	$room_select=&select_room($sel_year,$sel_seme,"room_id",$room_id);
	
        $sql_select = "select * from spec_classroom where room_id='$room_id' ";
	$recordSet=$CONN->Execute($sql_select) or trigger_error("錯誤訊息： $sql_select", E_USER_ERROR);
	@list($room_id,$room_name , $enable ,$notfree_time)= $recordSet->FetchRow() ;
	if ($enable) 
	   $chk_str = 	"<input name='enable' type='checkbox' value='1' checked>開放預約 " ; 
	else 
	   $chk_str = 	"<input name='enable' type='checkbox' value='1' >開放預約" ; 
	      
	//說明
	$help_text="
	請選擇一個學年、學期以做設定。||
	<span class='like_button'>開始設定</span>會開始進行該學年班級課表中專科教室的設定。||
	<span class='like_button'>觀看設定</span>會列出該學年學期班級課表中專科教室的設定。||
	<span class='like_button'>新增教室</span>會將您輸入的教室名增加到專科教室列表中。||
	<span class='like_button'>修改教室</span>會將您所選的專科教室做內容修改。||
	不開放節次以逗號做分隔(如11,13 -->表示星期一的1,3節不開放預約)。||
	早修節次代號為0；午休節次代號為100。
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
	</script>
	$tool_bar
	<table bgcolor='#9EBCDD' cellspacing=1 cellpadding=4>
	<tr bgcolor='#FFFFFF'><td>
		<table>
		<form action='{$_SERVER['PHP_SELF']}' method='post' name='myform'>
  		<tr><td>請選擇欲設定的學年度：</td><td>$date_select</td></tr>
		<tr><td>請選擇欲設定的專科教室：</td><td>$room_select</td></tr>
		<tr bgcolor='#DDDDDD' ><td align='right'>新增、刪除或修改--教室名稱：<br>不開放節次：</td><td><input type='text' name='room_name'  value= '$room_name' size='16'>$chk_str<br>
                                 <input type='text' name='notfree_time' size='40' value='$notfree_time' ></td></tr>		
		<tr><td colspan='2'><input type='submit' name='act' value='開始設定'>
		<input type='submit' name='act' value='增加教室'>
		<input type='submit' name='act' value='刪除教室'>
		<input type='submit' name='act' value='修改教室'>
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

//列出某個專科教室的課表
function &list_room_table($sel_year,$sel_seme,$room_id="",$mode=""){
	global $CONN,$class_year,$conID,$weekN,$school_menu_p,$go_on,$SFS_PATH_HTML,$all_ss_arr;

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

	//取得專科教室名
	$room_name=get_classroom_name($room_id);

	//取得所有課程
	$all_ss_arr=get_all_ssname($sel_year,$sel_seme);

	//找出某專科教室的所有課程
	$sql_select = "select class_id,course_id,teacher_sn,day,sector,ss_id,room from score_course where year='$sel_year' and semester='$sel_seme' and room='$room_name' order by day,sector";
	$recordSet=$CONN->Execute($sql_select) or trigger_error("錯誤訊息： $sql_select", E_USER_ERROR);
	while (list($class_id,$course_id,$teacher_sn,$day,$sector,$ss_id)= $recordSet->FetchRow()) {
		$k=$day."_".$sector;
		$a[$k]=$course_id;
		$b[$k]=$teacher_sn;
		$c[$k]=$class_id;
		$s[$k]=$ss_id;
	}

	//取得星期那一列
	for ($i=1;$i<=count($weekN); $i++) {
		$main_a.="<td align='center' >星期".$weekN[$i-1]."</td>";
	}
	
	//取得考試設定中的最高節次
	$query="select max(sections) from score_setup where year='$sel_year' and semester='$sel_seme'";
	$res=$CONN->Execute($query);
	$sections=$res->fields[0];
	if($sections==0) trigger_error("請先設定 $sel_year 學年 $sel_seme 學期 [成績設定]項目,再操作課表設定<br><a href=\"$SFS_PATH_HTML/modules/every_year_setup/score_setup.php\">進入設定</a>",E_USER_ERROR);

	if(!empty($room_id)){

		//取得教師陣列
		$tea_temp_arr = my_teacher_array();

		//取得班級陣列
		$class_name=get_class_name($sel_year,$sel_seme,"");

		//製作取得專科教室選單
		$room_list=&select_room($sel_year,$sel_seme,"room_id",$room_id);
			
		//新增一個下拉選單實例
		$room_select = new drop_select();
			
		$def_color = $color;
		//取得課表
		for ($j=1;$j<=$sections;$j++){

			if ($j==5){
				$all_class.= "<tr bgcolor='white'><td colspan='$dayn' align='center'>午休</td></tr>\n";
			}

			$all_class.="<tr bgcolor='#E1ECFF'><td align='center'>$j</td>";

			//列印出各節			
			for ($i=1;$i<=count($weekN); $i++) {
				$k2=$i."_".$j;

				//科目的下拉選單
				$room_select->s_name="set_id[$k2]";
				$room_select->id=$a[$k2];
				$room_select->arr=get_course_arr($sel_year,$sel_seme,$room_id,$k2);
				$room_sel=$room_select->get_select();
				$color=(empty($a[$k2]))?$def_color:"#F5E5E5";
				$class_sel=(empty($a[$k2]))?"":"<fieldset><legend><font color='#aaaaaa'>目前設定</font></legend><font color='#aaaaaa'>班級:</font><font color='red'>".$class_name[$c[$k2]]."</font>";
				$subject_sel=(empty($a[$k2]))?"":"<font color='#aaaaaa'>科目:</font><font color='green'>".$all_ss_arr[$s[$k2]]."</font>";
				$teacher_sel=(empty($a[$k2]))?"":"<font color='#aaaaaa'>教師:</font><font color='blue'>".$tea_temp_arr[$b[$k2]]."</font></fieldset>";
				
				//每一格
				$debug_str=($debug)?"<small><font color='#aaaaaa'>-".$a[$k2]."</font></small><br>":"";
				$all_class.="<td $align bgcolor='$color'>
				$room_sel<br><small>$class_sel<br>$subject_sel<br>$teacher_sel<br>$debug_str</small>
				</td>\n";
			

			}

			$all_class.= "</tr>\n" ;
		}

		$submit=($mode=="view")?"
		<input type='hidden' name='act' value='list_room_table'>
		<input type='submit' value='修改設定'>":"
		<input type='hidden' name='act' value='save'>
		<input type='submit' value='儲存設定'>";

		//該班課表
		$main_class_list="
		<form action='{$_SERVER['PHP_SELF']}' method='post'>
		<tr bgcolor='#E1ECFF'><td align='center'>節</td>$main_a</tr>
		$all_class
		<tr bgcolor='#E1ECFF'><td colspan='6' align='center'>
		<input type='hidden' name='sel_year' value='$sel_year'>
		<input type='hidden' name='sel_seme' value='$sel_seme'>
		<input type='hidden' name='room_id' value='$room_id'>
		$submit
		</td></tr>
		";
	}else{
		$main_class_list="";
	}
	
	$tool_bar=&make_menu($school_menu_p);
	
	$checked=($go_on=="view_class")?"checked":"";
		
	$url_str =$SFS_PATH_HTML.get_store_path()."/sel_class.php";

	$main="
	$tool_bar
	<table cellspacing=0 cellpadding=0><tr><td>
		<table border='0' cellspacing='1' cellpadding='4' bgcolor='#9EBCDD'>
		<form action='{$_SERVER['PHP_SELF']}' method='post' name='myform'>
		<input type='hidden' name='go_on' value='$go_on'>
		<input type='hidden' name='act' value='list_room_table'>
		<tr><td colspan='6' nowrap bgcolor='#FFFFFF'>
		$date_text ， $room_list &nbsp;&nbsp;
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

//儲存專科教室設定
function save_room_table($sel_year="",$sel_seme="",$room_id="",$set_id=""){
	global $CONN;
	$room_name=get_classroom_name($room_id);
	while(list($k,$v)=each($set_id)){
		$kk=explode("_",$k);
		$day=$kk[0];
		$sector=$kk[1];

		//先取得看看有無課程
		$c=get_course($sel_year,$sel_seme,"",$day,$sector,$room_name);

		//假如沒有課程資料，資料庫中也無該課程，那麼跳過
		if(empty($set_id[$k]) and empty($c[course_id]))continue;
		
		if(empty($c[course_id])){
			add_room($v,$room_name);
		}else{
			update_room($c[course_id],$v,$room_name);
		}
	}
	return ;
}

//儲存一筆教室設定（一班一天的某一節）
function add_room($course_id="",$room_name=""){
	global $CONN;
	$sql_insert = "update score_course set room='$room_name' where course_id='$course_id'";
	if($CONN->Execute($sql_insert))	return true;
	die($sql_insert);
	return false;
}

//更新一筆教室設定（一班一天的某一節）
function update_room($old_id="",$course_id="",$room_name=""){
	global $CONN;
	$sql_delete = "update score_course set room='' where course_id='$old_id'";
	$CONN->Execute($sql_delete) or die($sql_delete);
	$sql_update = "update score_course set room='$room_name' where course_id='$course_id'";
	if($CONN->Execute($sql_update))	return true;
	die($sql_update);
	return false;
}

//取得專科教室名
function get_classroom_name($room_id=""){
	global $CONN;
	$query="select room_name from spec_classroom where enable='1' and room_id='$room_id' order by room_id";
	$res=$CONN->Execute($query);
	$room_name=$res->fields[room_name];
	return $room_name;
}



//取得所有課程設定
function get_all_ssname($sel_year="",$sel_seme=""){
	global $CONN;
	$subject_name_arr=get_subject_name_arr();
	$sql_select="select class_year,ss_id,scope_id,subject_id from score_ss where year='$sel_year' and semester='$sel_seme' and enable='1' order by class_year,sort,sub_sort";
	$res = $CONN->Execute($sql_select) or trigger_error("SQL 錯誤 $sql_select",E_USER_ERROR);
	while(!$res->EOF){
		$scope_id = $res->fields[scope_id];
		$subject_id = $res->fields[subject_id];
			$subject_name= $subject_name_arr[$subject_id][subject_name];
		if (empty($subject_name))
			$subject_name= $subject_name_arr[$scope_id][subject_name];
		$all_ss_arr[$res->fields[ss_id]] = $subject_name;
		$res->MoveNext();
	}
	return $all_ss_arr;
}

//取得某一筆課程資料
function get_course($sel_year,$sel_seme,$course_id="",$day="",$sector="",$room_name=""){
	global $CONN;
	if(!empty($course_id)){
		$where="where course_id = '$course_id'";
	}else{
		$where="where year='$sel_year' and semester='$sel_seme' and room='$room_name' and day='$day' and sector='$sector'";
	}
	$sql_select = "select * from score_course $where";
	$recordSet=$CONN->Execute($sql_select) or die($sql_select);
	$array = $recordSet->FetchRow();
	return $array;
}

//教室的下拉選單
function &select_room($sel_year,$sel_seme,$name="room_id",$now_room){
	global $CONN;
	$data="<option value='0' >--選擇--</option>" ;
	$sql_select = "select room_id,room_name,enable from spec_classroom order by room_name";
	$recordSet=$CONN->Execute($sql_select) or trigger_error("錯誤訊息： $sql_select", E_USER_ERROR);
	while(list($room_id,$room_name,$enable)= $recordSet->FetchRow()) {
		$selected=($now_room==$room_id)?"selected":"";
		$bgcolor=$enable?"#ffcccc":"#aaaaaa";
		$data.="<option value='$room_id' $selected style='background-color: $bgcolor;'>$room_name</option>";
	}
	$main="<select name='$name' size='1' OnChange='this.form.submit();'>$data</select>";
	return $main;
}

//增加一筆專科教室
function add_classroom($room_name , $enable , $notfree_time ){
	global $CONN;
	$query="select * from spec_classroom where room_name='$room_name' and enable='1'";
	$res=$CONN->Execute($query);
	if ($res->RecordCount()==0) {
		$sql_insert = "insert into spec_classroom (room_name , enable , notfree_time) values ('$room_name' ,'$enable' , '$notfree_time')";
		if($CONN->Execute($sql_insert))	return true;
		die($sql_insert);
	}
	return false;
}

//刪除一筆專科教室
function del_classroom($room_id){
	global $CONN;
	$sql_update = "delete from spec_classroom where room_id='$room_id'";
	$CONN->Execute($sql_update);
	return false;
}

//修改一筆專科教室
function edit_classroom($room_id, $room_name , $enable , $notfree_time  ){
	global $CONN;
	$sql_update = "update spec_classroom set room_name ='$room_name' ,enable='$enable' , notfree_time ='$notfree_time'  where room_id='$room_id'";
	$CONN->Execute($sql_update);
	return false;
}

//取得班級課程陣列
function get_course_arr($sel_year,$sel_seme,$room_id,$date){
	global $CONN,$all_ss_arr;
	$d=explode("_",$date);
	$sql_select="select course_id,class_id,teacher_sn,ss_id from score_course where year='$sel_year' and semester='$sel_seme' and day='".$d[0]."' and sector='".$d[1]."' order by class_id";
	$res=$CONN->Execute($sql_select) or trigger_error("SQL 錯誤 $sql_select",E_USER_ERROR);
	while(list($course_id,$class_id,$teacher_sn,$ss_id)=$res->FetchRow()) {
		$c=explode("_",$class_id);
		if ($course_id) $data[$course_id]="(".intval($c[2]).$c[3].")".$all_ss_arr[$ss_id];
	}
	return $data;
}

//下載功課表
function downlod_ct($class_id="",$sel_year="",$sel_seme=""){
	global $CONN,$weekN,$school_kind_name;
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
	
	$sql_select = "select course_id,teacher_sn,day,sector,ss_id,room from score_course where class_id='$class_id' order by day,sector";

	$recordSet=$CONN->Execute($sql_select) or trigger_error("錯誤訊息： $sql_select", E_USER_ERROR);
	while (list($course_id,$teacher_sn,$day,$sector,$ss_id,$room)= $recordSet->FetchRow()) {
		$k=$day."_".$sector;
		$a[$k]=$ss_id;
		$b[$k]=$teacher_sn;
		$r[$k]=$room;
	}
	
	
	//取得考試所有設定
	$sm=&get_all_setup("",$sel_year,$sel_seme,$the_class[year]);
	$sections=$sm[sections];
	if(!empty($class_id)){
		//取得課表
		for ($j=1;$j<=$sections;$j++){
			//若是最後一列要用不同的樣式
			$ooo_style=($j==$sections)?"4":"2";
			
			if ($j==5){
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
	$ttt->addfile('settings.xml');
	$ttt->addfile('styles.xml');
	$ttt->addfile('meta.xml');

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
	$sss = &$ttt->file();

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
function get_class_name($sel_year,$sel_seme,$cyear=""){
	global $CONN,$class_year;
	$and_cyear=(empty($cyear))?"":"and c_year='$cyear'";
	$sql_select = "select class_id,c_year,c_name from school_class where year='$sel_year' and semester = '$sel_seme' and enable='1' $and_cyear order by c_year,c_sort";
	$recordSet=$CONN->Execute($sql_select)  or trigger_error($sql_select, E_USER_ERROR);
	while (list($class_id,$c_year,$c_name)= $recordSet->FetchRow()) {
		$temp_arr[$class_id]=$class_year[$c_year].$c_name."班";
	}
	return $temp_arr;
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
?>
