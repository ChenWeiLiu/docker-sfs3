<?php
// $Id: board_view.php 7280 2013-05-06 02:24:51Z hsiao $
// --系統設定檔
include "board_config.php";

session_start();

$bk_id = $_REQUEST['bk_id'];

$bk_id=($bk_id=='')?$_POST['bk_id']:$bk_id;


//登出
if ($_GET[logout]== "yes"){
	session_start();
	$CONN -> Execute ("update pro_user_state set pu_state=0,pu_time_over=now() where teacher_sn='{$_SESSION['session_tea_sn']}'") or user_error("更新失敗！",256);
	session_destroy();
	$_SESSION[session_log_id]="";
	$_SESSION[session_tea_name]="";
	Header("Location: $_SERVER[PHP_SELF]");
}

if ($_POST['act']=='update_sort') {
	foreach($_POST['B_SORT'] as $k=>$v) {
    $query="update jboard_p set b_sort='$v' where b_id='$k'";
    $res=$CONN->Execute($query) or die("Error! query=".$query);  
  }
  $INFO="已於".date("Y-m-d H:i:s")."進行儲存.";
}



//是否有獨立的界面
if ($is_standalone)
	include "header.php";
else
	head("文章列表");

if ($topage !="")
	$page = $topage;

//取得指定頁碼列表

	///mysqli
$sql_select = "select b_id,bk_id,b_open_date,b_days,b_unit,b_title,b_name,b_sub,b_con,b_hints,b_url,b_post_time,b_own_id,b_is_intranet,b_is_marquee,b_signs,b_is_sign,teacher_sn,b_sort,top_days from jboard_p where bk_id=? order by b_sort,b_open_date desc ,b_post_time desc ";	
$mysqliconn = get_mysqli_conn();
$stmt = "";
$stmt = $mysqliconn->prepare($sql_select);
$stmt->bind_param('s', $bk_id);
$stmt->execute();
$stmt->bind_result($bb_id,$bkk_id,$b_open_date,$b_days,$b_unit,$b_title,$b_name,$b_sub,$b_con,$b_hints,$b_url,$b_post_time,$b_own_id,$b_is_intranet,$b_is_marquee,$b_signs,$b_is_sign,$teacher_sn,$b_sort,$top_days);

while ($stmt->fetch()) {
	if ($b_is_intranet == "1") //內部文件
		$b_i_temp = "<img src=\"images/school.gif\" alt=\"校內文件\" border=0>";
	else
		$b_i_temp ="";

	if ($b_is_sign == "1") //簽收文件
		$b_sign_temp = "<img src=\"images/sign.png\" alt=\"簽收文件\" border=0>";
	else
		$b_sign_temp ="";

	$bgcolor =($i++ % 2)?$table_bg_color:"#".dechex((float) hexdec($table_bg_color)+$offset_color);
	$temp_con .="<tr bgcolor='$bgcolor' onMouseOver=setBG('$record_bg_color',this) onMouseout=setBGOff('$bgcolor',this) onFocus=setBG('$record_bg_color',this) onBlur=setBGOff('$bgcolor',this) style='text-height:$record_height pt;text-align:center;color:$record_text_color;font-size:$font_size pt;'>";
	$temp_con .= sprintf("<td nowrap>%s</td><td nowrap>%s</td><td style='text-align:left;'>%s $b_i_temp $b_sign_temp</td>",$bb_id,$b_open_date,$b_sub);
	if ($enable_title!="0") $temp_con .= sprintf("<td nowrap>%s</td>",$b_title);
	if ($enable_days!="0") $temp_con .= sprintf("<td nowrap style='font-size:10pt'>%s</td>",$days[$b_days]);
	if ($enable_point!="0") $temp_con .= sprintf("<td>%3d</td>",$b_hints);
	$temp_con .= "<td><input type=\"text\" name=\"B_SORT[".$bb_id."]\" value=\"".$b_sort."\" size=\"5\"></td>";
	$temp_con .= "</tr>\n";
	
}

///mysqli

/*
$sql_select = "select  * from jboard_p where bk_id='$bk_id' order by b_sort,b_open_date desc ,b_post_time desc ";
$result = $CONN->Execute($sql_select)or die ($sql_select);

$temp_con="";
while ($row = $result->fetchRow()){
	$bb_id = $row["b_id"];
	$bkk_id = $row["bk_id"];
	$b_open_date = $row["b_open_date"];
	$b_days = $row["b_days"];
	$b_unit = $row["b_unit"];
	$b_title = $row["b_title"];
	$b_name = $row["b_name"];
	$b_sub = $row["b_sub"];
	$b_hints = $row["b_hints"];
	$b_upload = $row["b_upload"];
	$b_own_id = $row["b_own_id"];
	$b_post_time = $row["b_post_time"];
	$b_is_intranet = $row["b_is_intranet"];
	$teacher_sn = $row["teacher_sn"];
	$b_is_sign = $row['b_is_sign'];
	$b_sort=$row['b_sort'];

	if ($b_is_intranet == "1") //內部文件
		$b_i_temp = "<img src=\"images/school.gif\" alt=\"校內文件\" border=0>";
	else
		$b_i_temp ="";

	if ($b_is_sign == "1") //簽收文件
		$b_sign_temp = "<img src=\"images/sign.png\" alt=\"簽收文件\" border=0>";
	else
		$b_sign_temp ="";

	$bgcolor =($i++ % 2)?$table_bg_color:"#".dechex((float) hexdec($table_bg_color)+$offset_color);
	$temp_con .="<tr bgcolor='$bgcolor' onMouseOver=setBG('$record_bg_color',this) onMouseout=setBGOff('$bgcolor',this) onFocus=setBG('$record_bg_color',this) onBlur=setBGOff('$bgcolor',this) style='text-height:$record_height pt;text-align:center;color:$record_text_color;font-size:$font_size pt;'>";
	$temp_con .= sprintf("<td nowrap>%s</td><td nowrap>%s</td><td style='text-align:left;'>%s $b_i_temp $b_sign_temp</td>",$bb_id,$b_open_date,$b_sub);
	if ($enable_title!="0") $temp_con .= sprintf("<td nowrap>%s</td>",$b_title);
	if ($enable_days!="0") $temp_con .= sprintf("<td nowrap style='font-size:10pt'>%s</td>",$days[$b_days]);
	if ($enable_point!="0") $temp_con .= sprintf("<td>%3d</td>",$b_hints);
	$temp_con .= "<td><input type=\"text\" name=\"B_SORT[".$bb_id."]\" value=\"".$b_sort."\" size=\"5\"></td>";
	$temp_con .= "</tr>\n";
}
*/


?>
<center>
<script language="JavaScript">
<!--
function setBG(TheColor,thetable) {
thetable.bgColor=TheColor
}
function setBGOff(TheColor,thetable) {
thetable.bgColor=TheColor
}

//-->
</script>

<form action="<?php echo $_SERVER[PHP_SELF] ?>" method="POST" name="bform">
	<input type="hidden" name="act" value="">
<table width="95%"  >
<tr >
<td nowrap>
<select name="bk_id" onchange="document.bform.submit()">
	
<?php
	$query = "select bk_id,board_name from jboard_kind order by bk_id ";
	$result= $CONN->Execute($query) or die ($query);
	while( $row = $result->fetchRow()){
		//授權檢驗
	  $Manager=board_checkid($row["bk_id"]);
	  if ($Manager) {
		 if ($row["bk_id"] == $bk_id  ){
			echo sprintf(" <option value=\"%s\" selected>%s</option>",$row["bk_id"],$row["board_name"]);
			$board_name = $row["board_name"];
		}
		else
			echo sprintf(" <option value=\"%s\">%s</option>",$row["bk_id"],$row["board_name"]);
	  }
  }
	echo "</select>";
	echo "<font color=red>$INFO</font>";
	
	if ($bk_id!="")
		echo "</td><td align=center width=100%><b>".$board_name."文章列表</b>";
	else
		echo "</td><td align=center width=100% bgcolor=#FFFFFF><b>近期文章列表</b>";


	echo "</td>";
	echo "<td align=right nowrap >";
	if ($bk_id!="") {
		?>
		《<a href="board_view.php?bk_id=<?php echo $bk_id;?>">返回列表</a>》
		<input type="button" value="儲存排序" onclick="document.bform.act.value='update_sort';document.bform.submit()" style="color:#FF0000">
		<?php
	}


	?>
</tr>
</table>
<?php
echo "<font color='$header_text_color' size='8'><table width='$table_width' bgcolor='$table_bg_color'  border='$table_border_width' bordercolor='$table_border_color' cellpadding='2' cellspacing='0' style='border-collapse: collapse'>
	<tr style='color:$header_text_color;background-color:$header_bg_color;text-align:center;line-height:$header_height pt;font-size:$header_text_size pt;'>
		<td width='5%'>b_id</td>
		<td width='10%'>發佈日期</td>
		<td width='60%'>標  題</td>";
if ($enable_title!="0") echo "<td width='10%'>職  稱</td>";
if ($enable_days!="0") echo "<td width='8%'>期限</td>";
if ($enable_point!="0") echo "<td width='7%' style='font-size:10pt'>點閱數</td>";
echo "<td width='5%' style='font-size:9pt'>排序</td>";

echo "</tr>";

 echo $temp_con;
?>
</table></font>

<?php
if ($bk_id=="") {//頁尾說明
	if (!$no_footer) include "board_foot.php";
} else
	echo "<br><a href=\"board_view.php\">回文章近期列表</a>";
?>
</center>
</form>
<?php
//是否有獨立的界面
if ($is_standalone)
	include "footer.php";
else
	foot();
?>
