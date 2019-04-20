<?php
                                                                                                                             
// $Id: board_search.php 8805 2016-02-02 02:20:59Z qfon $

// --系統設定檔
include "board_config.php"; 
if($is_standalone)
	include	"header.php";
else
	head("校務佈告查詢");

?>
<table border=0 width=100%>
<form method=get name=myform action="<?php echo $PHP_SELF ?>">
<tr><td align=center><B>校務佈告查詢</B> &nbsp;<select name="bk_id" >
	<option value="">全部單位</option>

<?php
	$bk_id = $_REQUEST['bk_id'];
	$query = "select bk_id,board_name from board_kind ";
	$result= $CONN->Execute($query) or  trigger_error('系統錯誤',E_USER_ERROR);
	while( $row = $result->fetchRow()){
		if ($row["bk_id"] == $bk_id  ){
			echo sprintf(" <option value=\"%s\" selected>%s</option>",$row["bk_id"],$row["board_name"]);
			$board_name = $row["board_name"];
		}
		else
			echo sprintf(" <option value=\"%s\">%s</option>",$row["bk_id"],$row["board_name"]);
	}
	echo "</select>";
?>
&nbsp;<input type="text" name="s_str" maxlength=256 value="<?php echo $s_str ?>">
&nbsp;<input type="submit" name="key" value="搜尋">&nbsp;<a href="board_view.php">回佈告欄列表</a></td>
</tr>
</form>
</table>
<?php
$s_str = $_REQUEST['s_str'];
$page = $_GET['page'];
$s_str = strip_tags($s_str);

//mysqli
$mysqliconn = get_mysqli_conn("board_p");

if($s_str) {
	//查詢字串處理 
	
	$sstr = split ('[ +]', $s_str,10);
	reset($sstr);
	while(list($tid,$tname)= each ($sstr)){
		if (chop($tname)) 
		{
			//$tname=addslashes($tname);
			//$tempstr .= " (b_con like '%$tname%' or  b_sub like '%$tname%') and";
			$tempstr .= " (b_con like ? or b_sub like ?) and";
       }
	}
	$tempstr = substr($tempstr,0,-3);


///mysqli	
	
	$sql_select = "select count(b_id) as cc  from board_p  where ";

	if ($bk_id!="") 
		$sql_select .= " bk_id=? and ";
	$sql_select .= "($tempstr) ";
	

$stmt = "";
$stmt = $mysqliconn->prepare($sql_select);
if ($bk_id!="")$stmt->mbind_param('s',$bk_id);

$tname="%$s_str%";	
$tname=addslashes($tname);
$stmt->mbind_param('s',$tname);
$stmt->mbind_param('s',$tname);

$stmt->execute();
$stmt->store_result();
$stmt->bind_result($tol_num);
$stmt->fetch();
$stmt->close();
///mysqli
	
	/*
	$sql_select = "select count(b_id) as cc from board_p  where ";
	if ($bk_id!="") 
		$sql_select .= " bk_id='$bk_id' and ";
	$sql_select .= "($tempstr) ";

	$result = $CONN->Execute($sql_select)or trigger_error('系統錯誤',E_USER_ERROR);
	$row= $result->fetchRow();
	//查詢總數
//	$page_count=5;
	$tol_num = $row[0];
	*/
	
	
	$totalpage= intval($tol_num/$page_count);
	if($tol_num/$page_count <> 0)
		$totalpage++;
	
	//判斷頁數如果不存在或不正常時，指定頁數
	if(!$page || $page < 1)
		$page=1;
	
	if($page > $totalpage)
		$page=$totalpage;

	//查詢單位版區
	$query = "select bk_id,board_name from board_kind order by bk_id";
	$result = $CONN->Execute($query);
	while($row= $result->fetchRow())
		$board_kind_p [$row[0]] = $row[1];	
	
	$start_row = (($page>0?$page-1:0))*$page_count;	
	
	$sql_select = "select b_id,bk_id,b_open_date,  b_unit, b_title, b_name, b_sub, b_con,b_is_intranet  from board_p  where ";
	if ($bk_id!="") 
		$sql_select .= " bk_id=? and ";
	$sql_select .= "($tempstr) order by b_open_date desc limit $start_row,$page_count ";

///mysqli	
$stmt = "";
$stmt = $mysqliconn->prepare($sql_select);
if ($bk_id!="")$stmt->mbind_param('s',$bk_id);
$tname="%$s_str%";	
$tname=addslashes($tname);			
$stmt->mbind_param('s',$tname);
$stmt->mbind_param('s',$tname);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($b_id,$bk_id,$b_open_date,$b_unit,$b_title,$b_name,$b_sub,$b_con,$b_is_intranet);
///mysqli

	/*
	$sql_select = "select b_id,bk_id,b_open_date,  b_unit, b_title, b_name, b_sub, b_con  from board_p  where ";
	if ($bk_id!="") 
		$sql_select .= " bk_id='$bk_id' and ";
	$sql_select .= "($tempstr) order by b_open_date desc limit $start_row,$page_count ";
	$result = $CONN->execute ($sql_select)or  trigger_error('系統錯誤',E_USER_ERROR);
	*/
	
	//最後一筆
	$end_row = $page*$page_count;
	if ($end_row > $tol_num)
		$end_row= $tol_num;
	
    $s_str=stripslashes($s_str);
	echo "<table width=100% border=0 cellpadding=2 cellspacing=0><tr><td bgcolor=green nowrap><font size=-1 color=#ffffff>已搜尋<b>$board_kind_p[$bk_id]</b>關於<b>$s_str</b>。 &nbsp; </font></td><td bgcolor=green align=right nowrap><font size=-1 color=#ffffff> 共約有<b>$tol_num</b>項查詢結果，這是第<b>".($start_row+1)."</b>-<b>$end_row</b>項 。 搜尋共費<b>".get_page_time()."</b>秒。</font></td></tr>";
	$link_str = "bk_id=$bk_id&s_str=".urlencode($s_str)."&b_is_intranet=".$b_is_intranet;
	echo "<tr><td colspan=2 align=right>分頁：".pagesplit($page,$totalpage,5,$link_str);
	echo "</td></tr></table>";
	//while($row = $result->fetchRow()) { 
      while ($stmt->fetch()) {	
	    //$b_sub=stripslashes($b_sub);
        //$b_con=stripslashes($b_con);	  
		echo sprintf("<p><a href=board_show.php?b_id=%d&b_is_intranet=$b_is_intranet>%s</a> -- <font color=green>(%s <i>%s</i>)</font><br>",$b_id,stripslashes(chang_word_color($sstr,$b_sub)),$board_kind_p[$bk_id],$b_open_date); //改變顏色
		echo sprintf("<font size=-1><b>...</b>%s<b>...</b></font></p>",stripslashes(chang_word_color($sstr,$b_con,100))); //改變顏色		
		echo "</p>";
	}	
}

echo"<hr>";

if($is_standalone)
	include "footer.php";
else
	foot();
?>
