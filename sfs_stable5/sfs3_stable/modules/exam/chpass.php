<?php
                                                                                                                             
// $Id: chpass.php 8743 2016-01-08 14:02:58Z qfon $

if (!$isload)
{
include "config.php";
//session_start();
if ($session_stud_id == "")
{
 $exename = $PHP_SELF;                                 
 include "checkid.php";
 exit;
}

include "header.php";
}

//mysqli
$mysqliconn = get_mysqli_conn();	


  if ($key =="修改")
  {
	/*
	$sql_update = "update stud_base set stud_pass='$stud_pass',tea_school='$tea_school' ";
	$sql_update .= " where stud_id='$session_stud_id' ";
	$result = mysql_query ($sql_update)  or die ($sql_update);  
    */
//mysqli
$sql_update = "update stud_base set stud_pass=?,tea_school=? ";
$sql_update .= " where stud_id=? ";
$stmt = "";
$stmt = $mysqliconn->prepare($sql_insert);
$stmt->bind_param('sss', $stud_pass,$tea_school,$session_stud_id);
$stmt->execute();
$stmt->close();
///mysqli	
	
	
        echo "<p><h2>密碼更改成功</h2>";
	echo "<a href=\"exam_list.php\">回作業區</a>\n";
	include "footer.php";
	exit;
  }
 ///mysqli	
$sql_select = "select stud_id,stud_name,stud_pass,tea_school,tea_img from stud_base";
$sql_select .= " where stud_id=? ";

$stmt = "";
$stmt = $mysqliconn->prepare($sql_select);
$stmt->bind_param('s', $session_stud_id);
$stmt->execute();
$stmt->bind_result($stud_id,$stud_name,$stud_pass,$tea_school,$tea_imgx);
while ($stmt->fetch()) {
       if ($tea_imgx=='1') 
	    $tea_img = " checked ";
	   else 
	    $tea_img = " ";
}
///mysqli
  /*
  $sql_select = "select stud_id,stud_name,stud_pass,tea_school,tea_img from stud_base";
  $sql_select .= " where stud_id='$session_stud_id' ";
  $result = mysql_query ($sql_select);  
 
while ($row = mysql_fetch_array($result)) {

	$stud_id = $row["stud_id"];
	$stud_name = $row["stud_name"];
	$stud_pass = $row["stud_pass"];
	$tea_school = $row["tea_school"];
        if ($row["tea_img"]=='1') 
	    $tea_img = " checked ";
	   else 
	    $tea_img = " ";
		

};
*/
?>
<h3>個人資料 </h3>
<form method="post" name="regform"  >
<table>
<tr>
	<td>教師代號<br>
		<?php echo $stud_id ?>
	</td>
</tr>
<tr>
	<td>教師姓名<br>
		<?php echo $stud_name ?>
	</td>
</tr>
<tr>
	<td>學校<br>
		<input type="text" size="20" maxlength="20" name="tea_school" value="<?php echo $tea_school ?>">
	</td>
</tr>


<tr>
	<td>密碼<br>
		<input type="text" size="6" maxlength="6" name="stud_pass" value="<?php echo $stud_pass ?>">
	</td>
</tr>


<tr>
	<td>
	<input type="hidden" name=stud_id value="<?php echo $stud_id; ?>">
	<input type="submit" name=key value="修改">
	&nbsp;&nbsp;<input type="button"  value= "回上頁" onclick="history.back()">
	</td>
</tr>

</table>


<?php include "footer.php"; ?>
