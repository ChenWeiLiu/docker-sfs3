<?php
if (!$isload)
{
include "config.php";
//session_start();	
if ($session_tea_img != "1")
{
 $exename = $PHP_SELF;                                 
 include "checkid.php";
 exit;
}

include "header.php";
}
if($key =='新增')
{
//mysqli
$mysqliconn = get_mysqli_conn();	
$sql_insert = "insert into stud_base (stud_id,stud_name,stud_pass,tea_school,tea_img) values (?,?,?,?,?)";
$stmt = "";
$stmt = $mysqliconn->prepare($sql_insert);
$stmt->bind_param('sssss', $stud_id,$stud_name,$stud_pass,$tea_school,$tea_img);
$stmt->execute();
$stmt->close();
///mysqli	
	
//$sql_insert = "insert into stud_base (stud_id,stud_name,stud_pass,tea_school,tea_img) values ('$stud_id','$stud_name','$stud_pass','$tea_school','$tea_img')";
// Insert: 
  //$result = mysql_query ($sql_insert,$conID) or die($sql_insert);  
 
  //if ($result) 
  if ($mysqliconn->affected_rows==1){
  include "stud_base.php";
  exit;
}

//$sql_update = "update exam_kind set e_kind_id='$e_kind_id',e_kind_name='$e_kind_name',e_kind_memo='$e_kind_memo'";


// Update: 
//$result = mysql_query ($sql_update,$conID);




?>
人員管理

<form method="post" >
<table>


<tr>
	<td>教師代號<br>
		<input type="text" size="6" maxlength="6" name="stud_id" value="<?php echo $stud_id ?>">
	</td>
</tr>



<tr>
	<td>教師姓名<br>
		<input type="text" size="20" maxlength="20" name="stud_name" value="<?php echo $stud_name ?>">
	</td>
</tr>



<tr>
	<td>密碼<br>
		<input type="text" size="6" maxlength="6" name="stud_pass" value="<?php echo $stud_pass ?>">
	</td>
</tr>



<tr>
	<td>學校<br>
		<input type="text" size="20" maxlength="20" name="tea_school" value="<?php echo $tea_school ?>">
	</td>
</tr>

<tr>
	<td>管理者<br>
		是 <input type="checkbox" name="tea_img" value="1" >
	</td>
</tr>

<tr>
	<td>
		<input type="submit" name="key" value="新增">
                &nbsp;&nbsp;<input type="button"  value= "回上頁" onclick="history.back()">		
	</td>
</tr>
</table>
</form>
<?php include "footer.php"; ?>


