<?php

// $Id: doc1_list.php 8716 2015-12-31 08:46:04Z qfon $

//載入設定檔
include "docword_config.php";
// session 認證
//session_start();

//管理者檢查
if(checkid($PHP_SELF))
	$ischecked = true;
//-----------------------------------
include "header.php";
prog(1); //上方menu (在 docword_config.php 中設定)

if ($QueryBeginDate == "" )
	$QueryBeginDate = date("Y")."-1-1";
if ($QueryEndDate == "")
	$QueryEndDate = date("Y-m-j");


//計算頁數

$query ="select count(doc1_id) as cc from sch_doc1 where doc1_k_id = 0  ";

//查詢狀態
if ($doc_stat != 0)
	//$query .= " and  doc_stat ='$doc_stat' ";
    $query .= " and  doc_stat =? ";

//開始與結束日期
if ($QueryBeginDate !="")
	$query .= " and doc1_date >= '$QueryBeginDate' and doc1_date <= '$QueryEndDate' ";
//關鍵字
if ($QueryString!="")
	//$query .= " and (doc1_unit like'%$QueryString%' or doc1_main like '%$QueryString%' or do_teacher like '%$QueryString%') ";
	$query .= " and (doc1_unit like ? or doc1_main like ? or do_teacher like ?) ";

//單位
if ($doc1_unit_num1!= 0 )
{
	$doc1_unit_num1=intval($doc1_unit_num1);
	$query .= " and doc1_unit_num1 ='$doc1_unit_num1' ";
}

$QueryString="%$QueryString%";

  ///mysqli	
$mysqliconn = get_mysqli_conn();
$stmt = "";
$stmt = $mysqliconn->prepare($query);
if ($doc_stat != 0)$stmt->bind_param('s',$doc_stat);
if ($QueryString!="")$stmt->bind_param('sss',$QueryString,$QueryString,$QueryString);
$stmt->execute();
$stmt->bind_result($num_record);

$stmt->fetch();
$stmt->close();

///mysqli

//$result = mysql_query($query)or die($query);
//$row = mysql_fetch_row($result);

//總筆數
//$num_record = $row[0];

//計算最後一頁
if ($num_record % $page_count > 0 )
	$last_page = floor($num_record / $page_count)+1;
else
	$last_page = floor($num_record / $page_count);

//預設第一頁
if($curr_page > $last_page || !isset($curr_page) )
	$curr_page =1 ;

$less_record = $num_record -($page_count * ($curr_page+1));

//查詢字串
if ($doc1_unit_num1 !="")
	$Qstr = "doc1_unit_num1=$doc1_unit_num1&doc_stat=$doc_stat&QueryRange=$doc1_k_id&QueryBeginDate=$QueryBeginDate&QueryEndDate=$QueryEndDate&QueryString=$QueryString";

$JumpPage = "跳至第<select name=\"curr_page\" onchange=\"document.wform.submit()\">" ;
for ($i =1 ; $i <=$last_page ;$i++) {
        if ($curr_page == $i)
                $JumpPage .= "<option value=\"$i\" selected>$i</option>";
        else
               $JumpPage .= "<option value=\"$i\">$i</option>";
}

$JumpPage .="</select>頁&nbsp;&nbsp;";
if ($curr_page == 1)
	$navbar  = "[首頁]&nbsp;&nbsp;&nbsp;";

else
	$navbar  = "<a href=\"$PHP_SELF?curr_page=1&next_page=$next_page&$Qstr\">[首頁]</a>&nbsp;&nbsp;&nbsp;";

if ($curr_page > 1)
	$navbar .= "<a href=\"$PHP_SELF?curr_page=".($curr_page-1)."&next_page=$next_page&$Qstr\">[上一頁]</a>&nbsp;&nbsp;";
else
	$navbar .= "[上一頁]&nbsp;&nbsp;";

if ($curr_page >= $last_page)
	$navbar .= "[下一頁]&nbsp;&nbsp;";
else
	$navbar .= "<a href=\"$PHP_SELF?curr_page=".($curr_page+1)."&next_page=$next_page&$Qstr\"> [下一頁]</a>&nbsp;&nbsp;";

if ($curr_page == $last_page)
	$navbar .= "[末頁]&nbsp;&nbsp;";
else
	$navbar .= "<a href=\"$PHP_SELF?curr_page=$last_page&next_page=$next_page&$Qstr\"> [末頁]</a>&nbsp;";


?>

<table border="0"  width="100%" >
  <tr>

       <td align="right" width="100%" colspan=2><form name=wform method="POST" action="<?php echo $PHP_SELF ?>">
	 查詢範圍:<select name="doc1_unit_num1">
	 <option value="0">全部單位</option>
	 <?php
		$doc_unit_p = doc_unit(); //取得處室名稱 ( --> docwprd_config.php)
		while(list($tkey,$tvalue)= each ($doc_unit_p)){
		if ($tkey == $doc1_unit_num1)
			echo  sprintf ("<option value=\"%s\" selected>%s</option>\n",$tkey,$tvalue);
		else
			echo  sprintf ("<option value=\"%s\">%s</option>\n",$tkey,$tvalue);
		}
	 ?>
	 </select>
	 <select size="1" name="doc_stat">
	 <option value="" >全部公文</option>
	<?php
		//公文狀態

		while(list($tkey,$tvalue)= each ($doc_stat_array)){
			if ($tkey == $doc_stat)
				echo  sprintf ("<option value=\"%s\" selected>%s</option>\n",$tkey,$tvalue);
			else
				echo  sprintf ("<option value=\"%s\">%s</option>\n",$tkey,$tvalue);

		}
	?>
	</select>,自<input type="text" name="QueryBeginDate" size="10" value="<?php echo $QueryBeginDate; ?>">至<input type="text" name="QueryEndDate" size="10" value="<?php echo $QueryEndDate ?>">止,關鍵字:<input type="text" name="QueryString" size="10" value="<?php echo $QueryString ?>"><input type="submit" value="查詢" name="B1"></td>
  </tr>
  <tr>
    <td width="35%" align="left" valign="middle">
    <?php
    	if ($ischecked)
    		echo "<a href=\"doc_in.php\" >[新增來文]</a>&nbsp;<a href=\"doc_save.php\">[來文歸檔]</a>&nbsp;<a href=\"doc_print.php\" >[列印簽收單]</a>&nbsp;";
    ?>
    <a href="doc1_list.php" >[重新查詢]</a>
    </td>
    <td  align="right" valign="middle" width="65%">
    <?php echo "$JumpPage $navbar"; //印出跳頁欄 ?>
    </td>
  </tr>
</table>
<table cellSpacing="0" cellPadding="0" width="100%" align="center" bgColor="#cccccc" border="0">
  <tbody>
    <tr>
      <td>
        <table cellSpacing="1" cellPadding="3" width="100%" border="0">
          <tbody>
<!------ 邊框開始 -------->

<table border="0" width="100%" >
  <tr bgcolor= "#c0c0c0">

<?php
if ($next_page == 1) {
	echo "<td align='center' bgColor='#006600' height='30'><a href=\"$PHP_SELF?curr_page=$curr_page&next_page=0&$Qstr\"><img border=0 src=\"images/previous.gif\" alt=\"上一頁\"></a></td>";	
	echo "<td>收文號</td><td align='center'>來文摘要</td><td align='center'>來文單位</td><td align='center'>承辦單位</td><td align='center'>保存年限</td><td align='center'>歸檔日期</td><td align='center'>登錄日期</td>";
}
else {
	echo "<td align='center'>承辦單位</td><td align='center'>狀態</td><td>收文號</td><td align='center'>來文摘要</td><td align='center'>來文單位</td><td align='center'>來文字號</td><td align='center'>收文日期</td>";
	echo"<td align='center' bgColor='#006600' height='30'><a href=\"$PHP_SELF?curr_page=$curr_page&next_page=1&$Qstr\"><img border=0 src=\"images/next.gif\" alt=\"下一頁\"></a></td>";
}
?>

</tr>

<?php
//取得承辦處室
$doc_unit_p = doc_unit();

//$query ="select * from sch_doc1 where doc1_k_id = 0  ";
$query ="select doc1_id,doc1_year_limit,doc1_kind,doc1_date,doc1_date_sign,doc1_unit,doc1_word,doc1_main,doc1_unit_num1,doc1_unit_num2,teach_id,doc1_k_id,doc_stat,doc1_end_date,doc1_infile_date,do_teacher from sch_doc1 where doc1_k_id = 0  ";

//查詢狀態
if ($doc_stat != 0)
	//$query .= " and  doc_stat ='$doc_stat' ";
    $query .= " and  doc_stat =? ";
//開始與結束日期
if ($QueryBeginDate !="")
	$query .= " and doc1_date >= '$QueryBeginDate' and doc1_date <= '$QueryEndDate' ";
//關鍵字
if ($QueryString!="")
	//$query .= " and (doc1_unit like'%$QueryString%' or doc1_main like '%$QueryString%' or do_teacher like '%$QueryString%')";
	$query .= " and (doc1_unit like ? or doc1_main like ? or do_teacher like ?)";

//單位
if ($doc1_unit_num1 != 0 )
{
	$doc1_unit_num1=intval($doc1_unit_num1);
	$query .= " and doc1_unit_num1 ='$doc1_unit_num1' ";		
}

if ($doc_stat==0 && $QueryBeginDate=='' and $doc1_unit_num1=='')
	$query = " and doc1_date>'$this_year-1-1' and doc1_date <'".($this_year+1)."-1-1' ";

$query .= "order by abs(doc1_id) desc  limit ".(($curr_page-1) * $page_count).", $page_count ";

$stmt = $mysqliconn->prepare($query);
if ($doc_stat != 0)$stmt->bind_param('s',$doc_stat);
if ($QueryString!="")$stmt->bind_param('sss',$QueryString,$QueryString,$QueryString);
$stmt->execute();
$stmt->bind_result($doc1_id,$doc1_year_limit,$doc1_kind,$doc1_date,$doc1_date_sign,$doc1_unit,$doc1_word,$doc1_main,$doc1_unit_num1,$doc1_unit_num2,$teach_id,$doc1_k_id,$doc_stat,$doc1_end_date,$doc1_infile_date,$do_teacher );

//$result = mysql_query($query);
//while ($row = mysql_fetch_array($result)) {
	while ($stmt->fetch()) {

	//$doc1_id = $row["doc1_id"];
	//$doc1_year_limit = $row["doc1_year_limit"];
	//$doc1_kind = $row["doc1_kind"];
	//$doc1_date = $row["doc1_date"];
	//$doc1_date_sign = $row["doc1_date_sign"];
	//$doc1_unit = $row["doc1_unit"];
	
	if ($doc1_infile_date ==0 )
		$doc1_infile_date="&nbsp;"; //尚未歸檔
	else
		$doc1_infile_date = $doc1_infile_date;	
	if ($QueryString !="")
		$doc1_unit = str_replace($QueryString,"<font color=red>$QueryString</font>",$doc1_unit);
	$doc1_word = $doc1_word;
	$doc1_main = $doc1_main;
	if ($QueryString !="")
		$doc1_main = str_replace($QueryString,"<font color=red>$QueryString</font>",$doc1_main);
	//$doc1_unit_num1 = $row["doc1_unit_num1"];
	//$doc1_unit_num2 = $row["doc1_unit_num2"];
	//$teach_id = $row["teach_id"];
	//$doc_stat = $row["doc_stat"];
	$unit_temp = $doc_unit_p[$doc1_unit_num1]; //取得處室名稱

	if ($i++ % 2 == 0)
		echo  "<tr bgcolor=#FFFFCC>";
	else
		echo  "<tr bgcolor=#CCFFCC>";
	if ($next_page == 1) {

		echo "<td>&nbsp;</td><td><a href=\"doc_edit.php?doc1_id=$doc1_id\">$doc1_id</a></td><td>$doc1_main</td><td>$doc1_unit</td><td>$unit_temp</td><td align=right>$doc1_year_limit 年</td><td>$doc1_infile_date</td><td>$doc1_date_sign</td>";
	}
	else {
		echo "<td align=center>$unit_temp</td>";
		if ($doc_stat == 1)
			echo "<td>未歸檔</td>";
		else if ($doc_stat == 2)
			echo "<td><font color=red>已歸檔</font></td>";
		else if ($doc_stat == 9)
			echo "<td>已銷毀</td>";
		echo "<td><a href=\"doc_edit.php?doc1_id=$doc1_id\">$doc1_id</a></td><td>$doc1_main</td><td>$doc1_unit</td><td>$doc1_word</td><td>$doc1_date</td>";
		echo "<td>&nbsp;</td>";
	}

	echo "</tr>";
};
echo "</table>";
?>
<table  bgcolor=#c0c0c0 width=100%><tr>
<td>
<?php
	//筆數計算
	$temp = $page_count * ($curr_page);
	if ( $temp >= $num_record)
		$temp = $num_record;
	echo sprintf("第%3d至第%3d筆/符合條件共%5d筆",$page_count * ($curr_page-1)+1,$temp,$num_record);
?>
</td>
<td>
<?php
	//頁數計算
	echo sprintf("第%d頁/共%d頁<font color=green>(一頁%d筆)",$curr_page,$last_page,$page_count);
?>
</td>
<td align= right >
<?php echo $navbar; //印出跳頁欄 ?>
</td></tr></table>
</form>
<!------ 邊框結束 -------->
</tbody>
        </table>
      </td>
    </tr>
  </tbody>
</table>

<?php
include "footer.php";
?>
