<?php
// $Id: board_view.php 7280 2013-05-06 02:24:51Z hsiao $
// --系統設定檔
include "board_config.php";

session_start();

sfs_check();

$bk_id = $_REQUEST['bk_id'];

$page = $_REQUEST['topage'];

$module_manager=checkid($_SERVER['SCRIPT_FILENAME'],1);

if ($_POST['act']=="delete_all" and $module_manager){

	foreach ($_POST['check_delete'] as $b_id) {
		$query= "delete from jboard_p where b_id = '$b_id'";
		$CONN->Execute($query);
		$query= "delete from jboard_images where b_id = '$b_id'";
		$CONN->Execute($query);

		//把所有附檔刪除
		$query="select * from jboard_files where b_id='$b_id'";
		$res=$CONN->Execute($query);
		if ($res->RecordCount()>0) {
			while ($row=$res->FetchRow()) {
				$sFile=$row['new_filename'];
				unlink($Download_Path.$sFile);
			}
		}
		$query= "delete from jboard_files where b_id = '$b_id'";
		$CONN->Execute($query);
	}

}



//把過期置頂取消
$CONN->Execute("UPDATE `jboard_p` SET b_sort = '100',top_days='0' WHERE (to_days(b_open_date) + top_days) < to_days(now( )) and top_days>0");

//登出
if ($_GET[logout]== "yes"){
	session_start();
	$CONN -> Execute ("update pro_user_state set pu_state=0,pu_time_over=now() where teacher_sn='{$_SESSION['session_tea_sn']}'") or user_error("更新失敗！",256);
	session_destroy();
	$_SESSION[session_log_id]="";
	$_SESSION[session_tea_name]="";
	Header("Location: $_SERVER[PHP_SELF]");
}

//授權檢驗
$Manager=false;
if ($bk_id!="") {
	$Manager=board_checkid($bk_id);
	$Board_is_sort=board_checksort($bk_id);
	$B_SORT="a.b_sort,";
}	


//是否有獨立的界面
if ($is_standalone)
	include "header.php";
else
	head("文章列表");

if ($topage !="")
	$page = $topage;
	
//取得同步顯示板區的資料
if ($bk_id!='') {
$sql_sync="select bk_id,synchronize_days from jboard_kind where synchronize='$bk_id' and bk_id<>'$bk_id'";
$res=$CONN->Execute($sql_sync) or die ($sql_sync);

if ($res->RecordCount()) {
 while ($row=$res->fetchRow()) {
   $SYNC[]=array('bk_id'=>$row['bk_id'],'days'=>$row['synchronize_days']); //同步板區 id
 }
}
}


//計算本列表所有文章數
$sql_select = "select  a.b_id from jboard_p a,jboard_kind b where ";
if ($bk_id!="")
	$sql_select .= " ( a.bk_id='$bk_id'  ";
else
	$sql_select .= " to_days(a.b_open_date)+ 10 > to_days(curdate()) ";

if (count($SYNC)>0) {
  foreach ($SYNC as $v) {
  	$sql_select .=" or (a.bk_id='".$v['bk_id']."' and to_days(a.b_open_date)+ ".$v['days']." > to_days(curdate()))";
  }
}

$sql_select .= (($bk_id!="")?")":"")." and a.bk_id = b.bk_id order by a.b_open_date desc ,a.b_post_time desc ";

$result = $CONN->Execute($sql_select)or die ($sql_select);
$tol_num= $result->RecordCount();

if ($tol_num % $page_count > 0 )
	$tolpage = intval($tol_num / $page_count)+1;
else
	$tolpage = intval($tol_num / $page_count);

//取得指定頁碼列表
$sql_select = "select  a.* from jboard_p a,jboard_kind b where ";
if ($bk_id != "")
	$sql_select .= "( a.bk_id='$bk_id' ";
else
	$sql_select .= " to_days(a.b_open_date)+ 10 > to_days(curdate())";
	
if (count($SYNC)>0) {
  foreach ($SYNC as $v) {
  	$sql_select .=" or (a.bk_id='".$v['bk_id']."' and to_days(a.b_open_date)+ ".$v['days']." > to_days(curdate()))";
  }
}	

//假使未指定處室且要限定公告日期
if(!$bk_id and $m_arr["display_limit"]) $sql_select .= " and to_days(a.b_open_date)<=to_days(curdate())";
	
$sql_select .= (($bk_id!="")?")":"")." and a.bk_id = b.bk_id order by ".$B_SORT."a.b_open_date desc ,a.b_post_time desc ";
$sql_select .= " limit ".($page * $page_count).", $page_count";
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
	
	//讀取本分類設定
  $BOARD_SETUP=get_board_kind_setup($bkk_id);

  $UP=($b_sort<100)?"<font size=1 color=red>[置頂]</font>":"";

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

	//檢查是否為內部文件(建立者、系統管理員仍可看見內部文件)
//	if (($b_is_intranet== 0 ) || ($b_own_id == $_SESSION[session_log_id] )|| (checkid($_SERVER[SCRIPT_FILENAME],1)) || ($b_is_intranet == '1' && check_home_ip())){
	//if (($b_is_intranet== 0 ) || ($teacher_sn == $_SESSION[session_tea_sn] )|| (checkid($_SERVER[SCRIPT_FILENAME],1)) || ($b_is_intranet == '1' && $is_home_ip)){
	if (($b_is_intranet== 0 ) || isset($_SESSION[session_tea_sn] )|| (checkid($_SERVER[SCRIPT_FILENAME],1)) || ($b_is_intranet == '1' && $is_home_ip)){
		$temp_con .=($module_manager and $bk_id!="")?sprintf("<td><input type='checkbox' class='class_check_delete' name='check_delete[]' value='%s'></td><td nowrap>%s</td><td nowrap>%s</td><td style='text-align:left;'>$UP<a href=\"board_show.php?b_id=%d\">%s</a> $b_i_temp $b_sign_temp</td><td nowrap style='font-size:10pt'>%s</td><td nowrap style='font-size:10pt'>%s</td>",$bb_id,$bb_id,$b_open_date,$bb_id,$b_sub,$BOARD_SETUP['board_name'],$ROOM[$b_unit]): sprintf("<td nowrap>%s</td><td nowrap>%s</td><td style='text-align:left;'>$UP<a href=\"board_show.php?b_id=%d\">%s</a> $b_i_temp $b_sign_temp</td><td nowrap style='font-size:10pt'>%s</td><td nowrap style='font-size:10pt'>%s</td>",$bb_id,$b_open_date,$bb_id,$b_sub,$BOARD_SETUP['board_name'],$ROOM[$b_unit]);
	} else {
		$temp_con .=($module_manager and $bk_id!="")?sprintf("<td><input type='checkbox' class='class_check_delete' name='check_delete[]' value='%s'></td><td nowrap>%s</td><td nowrap>%s</td><td style='text-align:left;'>$UP %s $b_i_temp $b_sign_temp</td><td nowrap>%s</td>",$bb_id,$bb_id,$b_open_date,$b_sub,$BOARD_SETUP['board_name'],$ROOM[$b_unit]):sprintf("<td nowrap>%s</td><td nowrap>%s</td><td style='text-align:left;'>$UP %s $b_i_temp $b_sign_temp</td><td nowrap>%s</td>",$bb_id,$b_open_date,$b_sub,$BOARD_SETUP['board_name'],$ROOM[$b_unit]);
	}
	if ($enable_title!="0") $temp_con .= sprintf("<td nowrap style='font-size:10pt'>%s</td>",$TEACHER_TITLE[$b_title]);
	if ($enable_days!="0") $temp_con .= sprintf("<td nowrap style='font-size:10pt'>%s</td>",$days[$b_days]);
	if ($enable_point!="0") $temp_con .= sprintf("<td>%3d</td>",$b_hints);
	$temp_con .= sprintf("<td>%3d</td>",$b_sort);
	$temp_con .= "</tr>\n";
}


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

<form action="<?php echo $_SERVER[PHP_SELF] ?>" method="get" name="bform">
<table width="95%">
<tr>
<td nowrap>
<?php
	if ($bk_id!="" and ($Manager or $module_manager)) {
	 echo "《<a href=\"board.php?bk_id=$bk_id\">發表文章</a>》";
	
	 if ($Board_is_sort or 	$module_manager) { 
	  echo "《<a href=\"board_sort.php?bk_id=$bk_id&act=edit_sort\">調整排序</a>》";
	 }
	}
	
	if ($_SESSION[session_log_id] != "")
		echo "《<a href=\"$_SERVER[PHP_SELF]?logout=yes\">登出系統</a>》";
	if ($bk_id!="")
		echo "</td><td align=center width=100% style='color:#800000'><b>版區文章列表</b>";
	else
		echo "</td><td align=center width=100% bgcolor=#FFFFFF><b>各版區近期所有文章列表</b>";


	echo "</td>";
	echo "<td align=right nowrap >";

	echo "<a href=\"board_search.php?bk_id=$bk_id\">文件搜尋</a>&nbsp第";
	echo " <select name=\"topage\"  onchange=\"document.bform.submit()\">";
	for ($i= 0 ; $i < $tolpage ;$i++)
		if ($page == $i)
			echo sprintf(" <option value=\"%d\" selected>%2d</option>",$i,$i+1);
		else
			echo sprintf(" <option value=\"%d\" >%2d</option>",$i,$i+1);

		echo "</select>頁 /共 $tolpage 頁</td>";

	?>
</tr>
<tr>
<td colspan="4">
<font color="#800000"><b>版區：</b></font><select name="bk_id" onchange="document.bform.submit()">
	<option value="" style="color:#0000FF;background-color:#FFFFCC"><?php echo ($top_item)?$top_item:"近期發佈"; ?></option>
	<option value="">─────</option>

<?php
	$query = "select * from jboard_kind order by bk_order,bk_id ";
	$result= $CONN->Execute($query) or die ($query);
	while( $row = $result->fetchRow()){
		$P=($row['position']>0)?"".str_repeat("|--",$row['position']):"";
		if ($row['board_is_public']=='0' and !board_checkid($row['bk_id']) and !$module_manager) continue;
    /*
		if ($row["bk_id"] == $bk_id  ){
			echo sprintf(" <option style='color:%s' value=\"%s\" selected>%s%s%s</option>",$position_color[$row['position']],$row["bk_id"],$P,$row['bk_order'],$row["board_name"]);
			$board_name = $row["board_name"];
		}
		else
			echo sprintf(" <option style='color:%s' value=\"%s\">%s%s%s</option>",$position_color[$row['position']],$row["bk_id"],$P,$row['bk_order'],$row["board_name"]);
	  }
	  */
	  
	  if ($row["bk_id"] == $bk_id  ){
			echo sprintf(" <option style='color:%s;font-weight:bold;font-size:13pt' value=\"%s\" selected>[%05d] %s%s(%s)</option>",$position_color[$row['position']],$row["bk_id"],$row['bk_order'],$P,$row["board_name"],$row["bk_id"]);
			$board_name = $row["board_name"];
		}
		else
	  	if ($row['position']==0) {
			echo sprintf(" <option style='color:%s;font-weight:bold;font-size:13pt' value=\"%s\">[%05d] %s%s(%s)</option>",$position_color[$row['position']],$row["bk_id"],$row['bk_order'],$P,$row["board_name"],$row["bk_id"]);
	  	} else {
			echo sprintf(" <option style='color:%s' value=\"%s\">[%05d] %s%s(%s)</option>",$position_color[$row['position']],$row["bk_id"],$row['bk_order'],$P,$row["board_name"],$row["bk_id"]);
		  }
	  }

	
	echo "</select>";
	if ($bk_id=='ev6') {
	 echo "【<a href='board_teachers_id.php'>檢視繳交情況</a>】";
	}
?>
</td>
</tr>
<tr>
<td colspan="4">
<!--擷取顯示跑馬燈-->
<?php
        $query = "select b_id,b_sub,b_is_intranet,b_title from jboard_p where b_is_marquee = '1' and ((to_days(b_open_date)+b_days > to_days(current_date())) or (to_days(b_open_date)+".$max_marquee_days." > to_days(current_date())))";
        $result = $CONN->Execute($query) or die($query);
        if ($result){
        $html_link = '<p><FONT COLOR="'.$marquee_fontcolor.'">';
        $html_link .= '<MARQUEE WIDTH="100%" height="'.$marquee_height.'" BEHAVIOR="'.$marquee_behavior.'" BGColor="'.$marquee_backcolor.'" direction="'.$marquee_direction.'" scrollamount="'.$marquee_scrollamount.'">';
                $query = "select b_id,b_open_date,b_sub,b_is_intranet,b_title from jboard_p where b_is_marquee = '1' and ((to_days(b_open_date)+b_days > to_days(current_date())) or (to_days(b_open_date)+".$max_marquee_days." > to_days(current_date())))";
                $result = $CONN->Execute($query) or die($query);
                while ($row = $result->fetchRow()){
                        $html_link .= '&nbsp;'.'<a href="board_show.php?b_id='.$row['b_id'].'">'.$row['b_sub'].'&nbsp;(&nbsp;'.$TEACHER_TITLE[$row['b_title']].'&nbsp'.$row['b_open_date'].'&nbsp;)';
                        if ($row['b_is_intranet']=='1'){
                                $html_link .= '<font color="red"><sub>校內文件</sub></font>';
                        }
                        $html_link .= '</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                }
                $html_link .= '</MARQUEE></FONT></p>';
                echo $html_link;
        }
?>
</td>
</tr>
</table>
</form>
<form action="<?php echo $_SERVER[PHP_SELF] ?>" method="POST" name="myform">
	<input type="hidden" name="act" value="">
	<input type="hidden" name="bk_id" value="<?php echo $bk_id;?>">
	<input type="hidden" name="topage" value="<?php echo $page;?>">
<?php
echo "<font color='$header_text_color' size='8'>
<table width='$table_width' bgcolor='$table_bg_color'  border='$table_border_width' bordercolor='$table_border_color' cellpadding='2' cellspacing='0' style='border-collapse: collapse'>
	<tr style='color:$header_text_color;background-color:$header_bg_color;text-align:center;line-height:$header_height pt;font-size:$header_text_size pt;'>
".(($module_manager and $bk_id!="")?"<td><input type='checkbox' id='check_all' name='check_all' value='1'>":"")."
		<td width='5%'>b_id</td>
		<td width='10%'>發佈日期</td>
		<td width='40%'>標  題</td>
		<td width='10%'>文章分類</td>
		<td width='5%'>處室</td>";
if ($enable_title!="0") echo "<td width='10%'>職  稱</td>";
if ($enable_days!="0") echo "<td width='8%'>期限</td>";
if ($enable_point!="0") echo "<td width='7%' style='font-size:10pt'>點閱數</td>";
echo "<td width='5%' style='font-size:9pt'>排序</td>";

echo "</tr>";

 echo $temp_con;
?>
</table></font></form>

<?php
if ($bk_id=="") {//頁尾說明
	if (!$no_footer) include "board_foot.php";
} else
	if ($module_manager) {
		?>
			<table border='0' width="<?php echo $table_width;?>">
				<tr>
					<td align="left"><input type="button" style="color:#FF0000" value="批次刪除" id="commit_delete"></td>
				</tr>
				</table>
		<?php
	}
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
<Script>
	$("#commit_delete").click(function(){
		var confirm_delete=confirm("您確定要刪除所有勾選的公告?");
		if (!confirm_delete) {
			return false;
		} else {
			document.myform.act.value='delete_all';
			document.myform.submit();
		}
	});

	$("#check_all").click(function(){
		if($("#check_all").prop("checked")) {
			$("input[name='check_delete[]']").each(function() {
				$(this).prop("checked", true);
			});
		} else {
			$("input[name='check_delete[]']").each(function() {
				$(this).prop("checked", false);
			});
		}

	});

</Script>
