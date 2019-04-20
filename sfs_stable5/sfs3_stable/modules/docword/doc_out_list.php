<?php

// $Id: doc_out_list.php 8716 2015-12-31 08:46:04Z qfon $

//載入設定檔
include "docword_config.php";
// session 認證
//session_start();
//session_register("session_log_id");

//管理者檢查
if(checkid($PHP_SELF))
	$ischecked = true;
//-----------------------------------

include "header.php";
prog(2); //上方menu (在 docword_config.php 中設定)
//prog_doc2(1); //子menu (在 docword_config.php 中設定)

if ($QueryBeginDate == "" )
	$QueryBeginDate = date("Y")."-1-1";
if ($QueryEndDate == "")
	$QueryEndDate = date("Y-m-j");


//計算頁數
$query ="select count(doc1_id) as cc from sch_doc1 where doc1_k_id = 1 ";

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

  ///mysqli	
$QueryString="%$QueryString%";
$mysqliconn = get_mysqli_conn();
$stmt = "";
$stmt = $mysqliconn->prepare($query);
if ($QueryString!="")$stmt->bind_param('sss',$QueryString,$QueryString,$QueryString);
$stmt->execute();
$stmt->bind_result($num_record);
$stmt->fetch();
$stmt->close();
///mysqli

/*
$result = mysql_query($query)or die($query);
$result = mysql_query($query);
$row = mysql_fetch_row($result);
$num_record = $row[0];
*/

//計算最後一頁
if ($num_record % $page_count > 0 )
	$last_page = floor($num_record / $page_count)+1;
else
	$last_page = floor($num_record / $page_count);

$less_record = $num_record -($page_count * ($curr_page+1));

if (!isset($curr_page))
	$curr_page =1 ;

//查詢字串	
$Qstr = "doc1_unit_num1=$doc1_unit_num1&QueryBeginDate=$QueryBeginDate&QueryEndDate=$QueryEndDate&QueryString=$QueryString";

if ($curr_page == 1)
	$navbar  = "[首頁]&nbsp;&nbsp;&nbsp;";

else
	$navbar  = "<a href=\"$PHP_SELF?curr_page=1&$Qstr\">[首頁]</a>&nbsp;&nbsp;&nbsp;";

if ($curr_page > 1)
	$navbar .= "<a href=\"$PHP_SELF?curr_page=".($curr_page-1)."&$Qstr\">[上一頁]</a>&nbsp;&nbsp;";
else
	$navbar .= "[上一頁]&nbsp;&nbsp;";

if ($curr_page >= $last_page)
	$navbar .= "[下一頁]&nbsp;&nbsp;";
else
	$navbar .= "<a href=\"$PHP_SELF?curr_page=".($curr_page+1)."&$Qstr\"> [下一頁]</a>&nbsp;&nbsp;";

if ($curr_page == $last_page)
	$navbar .= "[末頁]&nbsp;&nbsp;";
else
	$navbar .= "<a href=\"$PHP_SELF?curr_page=$last_page&$Qstr\"> [末頁]</a>&nbsp;";


?>

<table border="0" width="100%">
  <tr>
         <td align="right" width="100%" colspan=2><form method="POST" action="<?php echo $PHP_SELF ?>">
         查詢範圍 <select name="doc1_unit_num1">         
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
	 </select>&nbsp;自
	 <input type="text" name="QueryBeginDate" size="10" value="<?php echo $QueryBeginDate; ?>">至<input type="text" name="QueryEndDate" size="10" value="<?php echo $QueryEndDate ?>">止,關鍵字:<input type="text" name="QueryString" size="10"><input type="submit" value="查詢" name="B1"></form></td>
  </tr>
  <tr>  
    <td width="35%" align="left">
	<?php 
    		if ($ischecked)
    			echo "<a href=\"doc_out.php\" >[新增發文]</a>";
    
	?>
	<a href="doc_out_list.php" >[重新查詢]</a></td>
    <td width="65%" align="right">
    <?php echo $navbar; //印出跳頁欄 ?>
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

<table border="0" width="100%">
  <tr bgcolor= "#c0c0c0">

<?php

	echo "<td align='center'>承辦單位</td><td>收文號</td><td align='center'>發文摘要</td><td align='center'>受文者</td><td align='center'>發文字號</td><td align='center'>發文日期</td>";


?>

</tr>

<?php
//取得承辦處室
$doc_unit_p = doc_unit();

//$query = "select doc1_id,doc1_year_limit,doc1_kind,doc1_date,doc1_date_sign,doc1_unit,doc1_word,doc1_main,doc1_unit_num1,doc1_unit_num2,teach_id from sch_doc1  where doc1_k_id = 1 ";
$query = "select doc1_id,doc1_year_limit,doc1_kind,doc1_date,doc1_date_sign,doc1_unit,doc1_word,doc1_main,doc1_unit_num1,doc1_unit_num2,teach_id,doc1_k_id,doc_stat,doc1_end_date,doc1_infile_date,do_teacher from sch_doc1  where doc1_k_id = 1 ";

//開始與結束日期
if ($QueryBeginDate !="")
	$query .= " and doc1_date >= '$QueryBeginDate' and doc1_date <= '$QueryEndDate' ";
//關鍵字
if ($QueryString!="")
	//$query .= " and (doc1_unit like'%$QueryString%' or doc1_main like '%$QueryString%' or do_teacher like '%$QueryString%') ";
	$query .= " and (doc1_unit like ? or doc1_main like ? or do_teacher like ?) ";

//單位
if ($doc1_unit_num1 != 0)
{
	$doc1_unit_num1=intval($doc1_unit_num1);
	$query .= " and doc1_unit_num1 ='$doc1_unit_num1' ";
}

$query .= " order by doc1_id desc  limit ".(($curr_page-1) * $page_count).", $page_count ";

  ///mysqli	
$QueryString="%$QueryString%";
//$mysqliconn = get_mysqli_conn();
$stmt = "";
$stmt = $mysqliconn->prepare($query);
if ($QueryString!="")$stmt->bind_param('sss',$QueryString,$QueryString,$QueryString);
$stmt->execute();
$stmt->bind_result($doc1_id,$doc1_year_limit,$doc1_kind,$doc1_date,$doc1_date_sign,$doc1_unit,$doc1_word,$doc1_main,$doc1_unit_num1,$doc1_unit_num2,$teach_id,$doc1_k_id,$doc_stat,$doc1_end_date,$doc1_infile_date,$do_teacher );
///mysqli


//$result = mysql_query($query);
//while ($row = mysql_fetch_array($result)) {
while ($stmt->fetch()) {
	/*
	$doc1_id = $row["doc1_id"];
	$doc1_year_limit = $row["doc1_year_limit"];
	$doc1_kind = $row["doc1_kind"];
	$doc1_date = $row["doc1_date"];
	$doc1_date_sign = $row["doc1_date_sign"];
	$doc1_unit = $row["doc1_unit"];
	$doc1_word = $row["doc1_word"];
	$doc1_main = $row["doc1_main"];
	$doc1_unit_num1 = $row["doc1_unit_num1"];
	$doc1_unit_num2 = $row["doc1_unit_num2"];
	$teach_id = $row["teach_id"];
	*/
              $unit_temp = $doc_unit_p[$doc1_unit_num1]; //取得處室名稱
	if ($i++ % 2 == 0)
		echo  "<tr bgcolor=#FFFFCC>";
	else
		echo  "<tr bgcolor=#CCFFCC>";

		echo "<td align=center>$unit_temp</td><td><a href=\"doc_out_edit.php?doc1_id=$doc1_id\">$doc1_id</a></td><td>$doc1_main</td><td>$doc1_unit</td><td>$doc1_word</td><td>$doc1_date</td>";



	echo "</tr>";
};
echo "</table>";
?>
<table width=100%><tr>
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
<td align= right>
<?php echo $navbar; //印出跳頁欄 ?>
</td></tr></table>
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
