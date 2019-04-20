<?php
                                                                                                                             
// $Id: ekind.php 8742 2016-01-08 13:57:14Z qfon $

/***********************
 每學期班級資料列表
 
 每學期需增加班級進來，
 讓授課老師可以對班級指定作業。
 
 本程式權限為系統管理者 
 在 系統管理 > 學務程式設定 > 授權管理本程式
 ***********************/

// --系統設定檔
include "exam_config.php";

//判別是否為系統管理者
$man_flag = checkid($_SERVER[SCRIPT_FILENAME],1) ;

if (!$man_flag) {	
	$str = "你未被授權使用本功能，參考系統說明檔" ;
	redir_str("exam.php",$str,3) ;
	exit;
}

if(!checkid(substr($_SERVER[PHP_SELF],1))){
	$go_back=1; //回到自已的認證畫面  
	include "header.php";
	include "$rlogin";
	include "footer.php"; 
	exit;
}

$class_seme_p = get_class_seme(); //學年度

if($_POST[curr_year_seme]=='')	//預設本學期
	$curr_year_seme = sprintf("%03s%d",curr_year(),curr_seme());
else
	$curr_year_seme = $_POST[curr_year_seme];

$e_kind_id = intval($_GET[e_kind_id]);

//全部展示處理
if ($_GET[sel_open] !="") {
	//目前學年學期	
	$query = "update exam_kind ";
	if ($_GET[sel_open] == "allyes") 
		$query .= "set e_kind_open=1 ";
	else if ($_GET[sel_open] == "allno") 
		$query .= "set e_kind_open=0 ";
	$query .= "where class_id like '$curr_year_seme%' ";
	$CONN->Execute($query);
}
//全部上傳處理
if ($_GET[sel_upload] !="") {	
	
	$query = "update exam_kind ";
	if ($_GET[sel_upload] == "allyes") 
		$query .= "set e_upload_ok=1 ";
	else if ($_GET[sel_upload] == "allno") 
		$query .= "set e_upload_ok=0 ";
	$query .= "where class_id like '$curr_year_seme%' ";
	//echo $query;
	$CONN->Execute($query);
}
//更改展示設定
if ($_GET[sel_o] !="") {
	$query= "update exam_kind set ";
	if ($_GET[sel_o] == "yes")
		$query .= "e_kind_open = 1 ";
	else if ($_GET[sel_o]=="no")
		$query .= "e_kind_open = 0 ";
	$query  .= "where e_kind_id='$e_kind_id' ";	
	$CONN->Execute($query);
}

//更改上傳設定
if ($_GET[sel_u] !="") {
	$query= "update exam_kind set ";
	if ($_GET[sel_u] == "yes")
		$query .= "e_upload_ok = 1 ";
	else if ($_GET[sel_u]=="no")
		$query .= "e_upload_ok = 0 ";
	$query  .= "where e_kind_id='$e_kind_id' ";	
	$CONN->Execute($query);
}
include "header.php";
include "menu.php";

//印出學期
echo "<form name=myform action=\"$_SERVER[PHP_SELF]\" method=post>";
echo "<h3>";//<select name=\"curr_year_seme\" onchange=\"this.form.submit()\">\n";
$sel1 = new drop_select();
$sel1->s_name = "curr_year_seme";
$sel1->id = $curr_year_seme;
$sel1->has_empty = false;
$sel1->is_submit = true;
$sel1->arr = $class_seme_p;
$sel1->do_select();

echo "-- 班級管理</form></h3>";
?>
<table border=1 >
  <tbody>
    <tr>
      <td bgColor="#80ffff">班級</td>      
      <td bgColor="#80ffff">說明</td>
      <td bgColor="#80ffff">公開展示<br><a href="<?php echo "$_SERVER[PHP_SELF]?sel_open=allyes" ?>">全部皆是</a><br><a href="<?php echo "$_SERVER[PHP_SELF]?sel_open=allno" ?>">全部皆否</a></td>
      <td bgColor="#80ffff">開放上傳<br><a href="<?php echo "$_SERVER[PHP_SELF]?sel_upload=allyes" ?>">全部皆是</a><br><a href="<?php echo "$_SERVER[PHP_SELF]?sel_upload=allno" ?>">全部皆否</a></td>
      <td colspan=2 bgColor="#80ffff">編修動作</td>
    </tr>
 
<?php
///mysqli	
$mysqliconn = get_mysqli_conn();
$stmt = "";
$s_str = "$curr_year_seme%";
$stmt = $mysqliconn->prepare("select e_kind_id,e_kind_memo,e_kind_open,e_upload_ok,class_id  from exam_kind where class_id like ? order by class_id");
$stmt->bind_param('s', $s_str);
$stmt->execute();
$stmt->bind_result($e_kind_id,$e_kind_memo,$e_kind_openx,$e_upload_okx,$class_id);
$i=0;
while ($stmt->fetch()) {
	$temp_class_name = get_class_name($class_id); //取得班級	
	if ($e_kind_openx=='1') 
		$e_kind_open = "<font color=red><b>是</b></font>&nbsp;｜&nbsp;<a href=\"$_SERVER[PHP_SELF]?e_kind_id=$e_kind_id&sel_o=no\">否</a>";
	else 
		$e_kind_open = "<a href=\"$_SERVER[PHP_SELF]?e_kind_id=$e_kind_id&sel_o=yes\">是</a>&nbsp;｜&nbsp;<font color=red><b>否</b></font>";
		
	if ($e_upload_okx=='1') 
		$e_upload_ok = "<font color=red><b>是</b></font>&nbsp;｜&nbsp;<a href=\"$_SERVER[PHP_SELF]?e_kind_id=$e_kind_id&sel_u=no\">否</a>";
	else 
		$e_upload_ok = "<a href=\"$_SERVER[PHP_SELF]?e_kind_id=$e_kind_id&sel_u=yes\">是</a>&nbsp;｜&nbsp;<font color=red><b>否</b></font>";

	if ($i % 2 == 0) 
		$bg = "bgColor=\"#ffff80\"";
	else
		$bg = "";
	echo "<tr $bg > <td>$temp_class_name</td> <td>$e_kind_memo</td> <td>$e_kind_open</td> <td>$e_upload_ok</td> <td><a href=\"ekind_edit.php?e_kind_id=$e_kind_id&class_id=$class_id\">修改</a></td> <td><a href=\"ekind_edit.php?sel=delete&e_kind_id=$e_kind_id&class_id=$class_id\">刪除</a></td> </tr>";
	$i++; 

}

///mysqli

/*
$sql_select = "select e_kind_id,e_kind_memo,e_kind_open , e_upload_ok ,class_id  from exam_kind where  class_id like '$curr_year_seme%' order by class_id ";
$result = mysql_query ($sql_select)or die ($sql_select);
$i=0;
while ($row = mysql_fetch_array($result)) {

	$e_kind_id = $row["e_kind_id"];	
	$e_kind_memo = $row["e_kind_memo"];
	$class_id = $row["class_id"];

	$temp_class_name = get_class_name($class_id); //取得班級
	
	
	if ($row["e_kind_open"]=='1') 
		$e_kind_open = "<font color=red><b>是</b></font>&nbsp;｜&nbsp;<a href=\"$_SERVER[PHP_SELF]?e_kind_id=$e_kind_id&sel_o=no\">否</a>";
	else 
		$e_kind_open = "<a href=\"$_SERVER[PHP_SELF]?e_kind_id=$e_kind_id&sel_o=yes\">是</a>&nbsp;｜&nbsp;<font color=red><b>否</b></font>";
		
	if ($row["e_upload_ok"]=='1') 
		$e_upload_ok = "<font color=red><b>是</b></font>&nbsp;｜&nbsp;<a href=\"$_SERVER[PHP_SELF]?e_kind_id=$e_kind_id&sel_u=no\">否</a>";
	else 
		$e_upload_ok = "<a href=\"$_SERVER[PHP_SELF]?e_kind_id=$e_kind_id&sel_u=yes\">是</a>&nbsp;｜&nbsp;<font color=red><b>否</b></font>";

	if ($i % 2 == 0) 
		$bg = "bgColor=\"#ffff80\"";
	else
		$bg = "";
	echo "<tr $bg > <td>$temp_class_name</td> <td>$e_kind_memo</td> <td>$e_kind_open</td> <td>$e_upload_ok</td> <td><a href=\"ekind_edit.php?e_kind_id=$e_kind_id&class_id=$class_id\">修改</a></td> <td><a href=\"ekind_edit.php?sel=delete&e_kind_id=$e_kind_id&class_id=$class_id\">刪除</a></td> </tr>";
	$i++;
}
*/


?>
</tbody>
</table>
<hr size=1 width=80%>
<a href="ekind_new.php">新增班級</a>
 
<?php include "footer.php"; ?>
