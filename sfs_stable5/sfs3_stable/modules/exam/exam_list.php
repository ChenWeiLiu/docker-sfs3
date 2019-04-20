<?php                                                                                                                             
// $Id: exam_list.php 8673 2015-12-25 02:23:33Z qfon $

//載入設定檔
include "exam_config.php";
//session_start();
//session_register("session_e_kind_id"); //目前班級 session
//session_register("session_class_id"); //目前班級 session
//session_register("session_curr_class_num"); //目前班級 session


//判別是否為系統管理者
$man_flag = checkid($_SERVER[SCRIPT_FILENAME],1);

//更改學年
if ($_SESSION[session_class_id] == $_POST[c_class_id] or  $_POST[c_class_id]=='')
	$class_id = $_SESSION[session_class_id];
else {
	$class_id = $_POST[c_class_id];
	$_SESSION[session_class_id] = $_POST[c_class_id];
	$_SESSION[session_e_kind_id] = -1;
}
if ($class_id =='')
	$class_id = sprintf("%03d%d",curr_year(),curr_seme());


//取得年級班級
if (isset($_SESSION[session_curr_class_num]))
	$curr_class = substr($_SESSION[session_curr_class_num],1,2 );

//更改班級
if ($_SESSION[session_e_kind_id] == $_POST[c_e_kind_id] or  $_POST[c_e_kind_id]=='')
	$e_kind_id = $_SESSION[session_e_kind_id];
else {
	$e_kind_id = $_POST[c_e_kind_id];
	$_SESSION[session_e_kind_id] = $_POST[c_e_kind_id];
}

//取得班級名稱陣列
$class_name = class_base($class_id);
	
//目前有作業的班級
$query = "select class_id ,e_kind_id from exam_kind where class_id like '$class_id%' group by class_id order by class_id desc ";
$result = $CONN->Execute($query) or trigger_error("SQL 錯誤",E_USER_ERROR);
$class_select_arr[-1] = "所有班級";
while(!$result->EOF){
	$temp_class = substr($result->fields[class_id],-3);
	$class_select_arr[$result->fields[e_kind_id]] = $class_name[$temp_class];
	$result->MoveNext();
}

$sel = new drop_select();
$sel->s_name="c_e_kind_id";
$sel->id=$e_kind_id;
$sel->has_empty = false;
$sel->is_submit = true;
$sel->arr= $class_select_arr;
$class_select= $sel->get_select();
//取得學期
$query = "select substring(class_id,1,4)as cc from exam_kind group by cc";
$result = $CONN->Execute($query) or trigger_error("SQL 錯誤",E_USER_ERROR);
while(!$result->EOF){
	$temp_name = substr($result->fields[0],0,3)."學年第".substr($result->fields[0],-1)."學期";
	$curr_class_select_arr[$result->fields[0]] = $temp_name;
	$result->MoveNext();
}

$sel->s_name="c_class_id";
$sel->id=$class_id;
$sel->top_option = "";
$sel->has_empty=false;
$sel->is_submit = true;
$sel->arr= $curr_class_select_arr;
$curr_class_select = $sel->get_select();


include "header.php";

echo "<h3><form name=myform action=\"$_SERVER[PHP_SELF]\" method=post>$exam_title &nbsp;$curr_class_select</h3>\n";
echo "<center>"; //班級選項

//學生登入訊息
if ($_SESSION[session_stud_id] !="") {
	//班級座號
	$temp_class = substr($_SESSION[session_curr_class_num],0,3);
	$temp = $class_name[$temp_class];
	echo "歡迎 $temp -- $_SESSION[session_stud_name] 登入 ";
	echo "&nbsp;｜&nbsp; $class_select \n";
	echo "&nbsp;｜&nbsp;<a href=\"stud_chg.php\">個人資料</a>";
	echo "&nbsp;｜&nbsp;<a href=\"checkid.php?logout=1&exename=$_SERVER[PHP_SELF]&e_kind_id=$e_kind_id\">登出系統</a>";
}
//教師管理部份
else if ($_SESSION[session_log_id] != "" ) { 
	echo "歡迎 $_SESSION[session_tea_name] 登入 ";
	echo "&nbsp;｜&nbsp; $class_select \n";	
	echo "&nbsp;｜&nbsp; <a href=\"exam.php\">作業管理</a>";
	if ($e_kind_id >0){
		echo "&nbsp;｜&nbsp; <a href=\"exam_score.php\">學期作業成績</a>";
		echo "&nbsp;｜&nbsp; <a href=\"show_seat.php\">座位表</a>";
	}
	echo "&nbsp;｜&nbsp; <a href=\"checkid.php?logout=1&exename=$_SERVER[PHP_SELF]\">登出系統</a>";
}
//未登入部份
else {
	echo "$class_select \n";
	echo "&nbsp;｜&nbsp; <a href=\"checkid.php?exename=$_SERVER[PHP_SELF]\">學員登入</a>";	
	echo "&nbsp;｜&nbsp; <a href=\"teacher_checkid.php?exename=$_SERVER[PHP_SELF]\">教師登入</a>";
}
echo "</form></center>";

//列出作業
$sql_select  = "select exam.* ,exam_kind.e_upload_ok,exam_kind.class_id  from exam,exam_kind ";
$sql_select .= " where exam.e_kind_id=exam_kind.e_kind_id \n";
$sql_select .= " and exam_kind.e_kind_open=1 and exam.exam_isopen=1 and exam_kind.class_id like '$class_id%' ";
if ($e_kind_id !="-1" and $e_kind_id<>'')
   {
	$e_kind_id=intval($e_kind_id);
    $sql_select .= " and exam.e_kind_id='$e_kind_id' ";
   }
$sql_select .= " order by exam_kind.class_id,exam.exam_id desc ";
$result = $CONN->Execute ($sql_select)or die ($sql_select);
//沒有作業
if($result->EOF) {
	echo "<center><h3>作業尚未展示，請期待!!</h3></center><br>";
}
else {
	echo "<table border=1  width=90% bordercolorlight=#FFFFFF cellspacing=0 bordercolordark=#C0C0C0 bordercolor=#FAD8F8 bgcolor=#FAD8F8>";
	$e_kind_id =0;
	$temp_class = sprintf("%03d%d%d",curr_year(),curr_seme(),substr($_SESSION[session_curr_class_num],0,3));
	while (!$result->EOF) {

		$e_upload_ok = $result->fields["e_upload_ok"];
		$exam_id = $result->fields["exam_id"];	
		$exam_name = $result->fields["exam_name"];
		$exam_memo = $result->fields["exam_memo"];
		$teach_name = $result->fields["teach_name"];
		$teach_id = $result->fields["teach_id"];
		$class_id = $result->fields["class_id"];
		$exam_isupload = $result->fields["exam_isupload"];
		if ($e_kind_id != $result->fields["e_kind_id"]){
			//取得班級名稱			
			$temp_class_name = $class_name[substr($class_id,-3)];
			echo "<tr><td colspan=4 height=34 bgColor=\"#C8CDFB\"><p align=center><b>$temp_class_name 作業列表</b></td></tr>\n";
			$e_kind_id = $result->fields["e_kind_id"];
		}
		
		if ($color_i++ % 2 == 0 )	 
			echo "<tr bgcolor=\"#FDEAFA\">";
		else
			echo "<tr>";
		echo "<td><a href=\"tea_show.php?e_kind_id=$e_kind_id&exam_id=$exam_id\">$exam_name</a></td> <td>$exam_memo</td><td>指導教師 -- $teach_name</td><td>";
	
		//判斷是否開始上傳作業 exam_isupload == 1
		//判斷是否為該班學生或指導教師，再給予上傳權限
		if ($exam_isupload == '1' &&(($e_upload_ok == '1' && $class_id == $temp_class)||($teach_id == $_SESSION[session_log_id])))

			echo " <a href=\"tea_upload.php?exam_id=$exam_id&exam_name=$exam_name\">上傳作業</a></td></tr>\n";
		else
			echo "&nbsp;</td></tr>\n";
		$i++;
		$result->MoveNext();

	}
	echo "</table>\n";
}
include "footer.php";
?>

