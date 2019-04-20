<?php                                                                                                                             
// $Id: exam.php 8742 2016-01-08 13:57:14Z qfon $
// --系統設定檔
include "exam_config.php";
// --認證 session
//session_start();
//session_register("session_e_kind_id"); //目前班級 session


//判別是否為系統管理者
$man_flag = checkid($_SERVER[SCRIPT_FILENAME],1) ;

//更改班級
if ($_SESSION[session_e_kind_id] == $_POST[c_e_kind_id] or  $_POST[c_e_kind_id]=='')
	$e_kind_id = $_SESSION[session_e_kind_id];
else {
	$e_kind_id = $_POST[c_e_kind_id];
	$_SESSION[session_e_kind_id] = $_POST[c_e_kind_id];
}


if(!checkid($_SERVER[PHP_SELF])){
	$go_back=1; //回到自已的認證畫面  
	include "header.php";
	include "$rlogin";
	include "footer.php"; 
	exit;
}

$_GET[exam_id]=intval($_GET[exam_id]);

//更改展示設定
if ($_GET[sel_open] !="") {
	$query= "update exam set ";
	if ($_GET[sel_open]=="yes")
		$query .= "exam_isopen = 1 ";
	else if ($_GET[sel_open]=="no")
		$query .= "exam_isopen = 0 ";
	$query  .= "where exam_id='$_GET[exam_id]' ";
	$CONN->Execute($query);
}

//更改上傳作業設定
if ($_GET[sel_upload] !="") {
	$query= "update exam set ";
	if ($_GET[sel_upload]=="yes")
		$query .= "exam_isupload = 1 ";
	else if ($_GET[sel_upload]=="no")
		$query .= "exam_isupload = 0 ";
	$query  .= "where exam_id='$_GET[exam_id]' ";
	$CONN->Execute($query) or die($query);
}

include "header.php";
//
include "menu.php";
//目前學年
/*
if ( $curr_year =="")
	$curr_year = sprintf("%03s",curr_year());

$curr_year_seme = sprintf("%03s%d",curr_year(),curr_seme());
*/
$class_seme_p = get_class_seme(); //學年度

if($_REQUEST[curr_year_seme]=='')	//預設本學期
	$curr_year_seme = sprintf("%03s%d",curr_year(),curr_seme());
else
	$curr_year_seme = $_REQUEST[curr_year_seme];


//取得目前班級陣列
$class_name = class_base();
  
//目前有作業的班級
$sql_select = "select exam_kind.class_id,exam_kind.e_kind_id  from exam,exam_kind ";
$sql_select .=" where exam.e_kind_id=exam_kind.e_kind_id and exam.teach_id ='$_SESSION[session_log_id]' and exam_kind.class_id like '$curr_year_seme%' group by exam_kind.class_id order by exam_kind.class_id  ";

$result = $CONN->Execute($sql_select) or trigger_error("SQL 錯誤",E_USER_ERROR);

$class_select_arr[-1]="所有班級";
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

?>
<h3>
<?php 
echo "<form name=myform action=\"$_SERVER[PHP_SELF]\" method=post>";
echo "<h3>";//<select name=\"curr_year_seme\" onchange=\"this.form.submit()\">\n";
$sel1 = new drop_select();
$sel1->s_name = "curr_year_seme";
$sel1->id = $curr_year_seme;
$sel1->has_empty = false;
$sel1->is_submit = true;
$sel1->arr = $class_seme_p;
$sel1->do_select();
?>
作業列表，任課教師：<?php echo $_SESSION[session_tea_name] ?></h3>
<?php echo $class_select ?>&nbsp;｜&nbsp;<a href="exam_new.php">新增作業</a></form>
<table border=1 >
  <tbody>
    <tr>
      <td bgColor="#80ffff">班級</td>
      <td bgColor="#80ffff">作業名稱</td>
      <td bgColor="#80ffff">開始展示?</td>      
      <td bgColor="#80ffff">開始上傳作業?</td>
      <td colspan=2 bgColor="#80ffff">編修動作</td>
    </tr>
  <tbody>

<?php

$sql_select = "select exam.*,exam_kind.class_id  from exam,exam_kind ";
$sql_select .=" where exam.e_kind_id=exam_kind.e_kind_id and exam.teach_id ='$_SESSION[session_log_id]' and exam_kind.class_id like '$curr_year_seme%' ";
if ($e_kind_id !="-1")
{
$e_kind_id=intval($e_kind_id);
$sql_select .= " and exam.e_kind_id='$e_kind_id' ";
}
$sql_select .=" order by exam_kind.class_id ";
$result = $CONN->Execute($sql_select)or die ($sql_select);
$i=0;
while (!$result->EOF) {
	$exam_id = $result->fields["exam_id"];
	$exam_name = $result->fields["exam_name"];
	if ($result->fields["exam_isopen"]=='1') 
		$exam_isopen = "<font color=red><b>是</b></font>&nbsp;｜&nbsp;<a href=\"$_SERVER[PHP_SELF]?exam_id=$exam_id&sel_open=no&c_e_kind_id=$e_kind_id&curr_year_seme=$curr_year_seme\">否</a>";
	else 
		$exam_isopen = "<a href=\"$_SERVER[PHP_SELF]?exam_id=$exam_id&&sel_open=yes&c_e_kind_id=$e_kind_id&curr_year_seme=$curr_year_seme\">是</a>&nbsp;｜&nbsp;<font color=red><b>否</b></font>";
	
	if ($result->fields["exam_isupload"]=='1') 
		$exam_isupload = "<font color=red><b>是</b></font>&nbsp;｜&nbsp;<a href=\"$_SERVER[PHP_SELF]?exam_id=$exam_id&sel_upload=no&c_e_kind_id=$e_kind_id&curr_year_seme=$curr_year_seme\">否</a>";
	else 
		$exam_isupload = "<a href=\"$_SERVER[PHP_SELF]?exam_id=$exam_id&sel_upload=yes&c_e_kind_id=$e_kind_id&curr_year_seme=$curr_year_seme\">是</a>&nbsp;｜&nbsp;<font color=red><b>否</b></font>";		
	 
	$class_id = $result->fields["class_id"];
	// $class_id 0-3 ->學年 4->學期 5->年級 6- 班級 	
	$c_temp = intval(substr($class_id,0,3))."學年度";
	if (substr($class_id,3,1) == 1 )
		$c_temp .= "上學期";
	else
		$c_temp .= "下學期";
	
	$temp_class_name = $class_name[substr($class_id,-3)]; //取得班級
	

      if ($i % 2 == 0) 
	$bg = "bgColor=\"#ffffcc\"";
	else
	$bg = "";
      print "<tr $bg >
      <td>$temp_class_name</td>
      <td>$exam_name</td>      
      <td align=center>$exam_isopen</td>      
      <td align=center>$exam_isupload</td>            
      <td align=center><a href=\"exam_edit.php?exam_id=$exam_id&class_id=$class_id\">修改</a></td>      
      <td align=center><a href=\"exam_edit.php?sel=delete&exam_id=$exam_id&exam_name=$exam_name\">刪除</a></td>
     </tr>";
    $i++;
      $result->MoveNext();
}
  
?>
</tbody>
</table>
<hr size=1 width=80%>
<a href="exam_new.php">新增作業</a>
 
<?php include "footer.php"; 
