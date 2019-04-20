<?php
include ('config.php');

sfs_check();
//秀出網頁
head("修正獎懲統計表的 student_sn欄位");

$tool_bar=&make_menu($school_menu_p);

//列出選單
echo $tool_bar;

?>
<form method="post" name="myform" action="<?php echo $_SERVER['PHP_SELF'];?>" enctype="multipart/form-data">
	<input type="hidden" name="mode" value="">
<table border="0" style="border-collapse:collapse" bordercolor="#800000">
 <tr>
 	<td><br><font color=red>◎修正10年學號重覆, 導致獎懲統計記錄錯置的問題◎</font><br>
 		<br><font color=blue>說明：</font><br>
 		當導師檢查學生獎懲統計時，發現學生的獎懲總數錯誤，<br>
 		其發生原因目前未知，以101年入學學生為例：<br>
 		在某些尚未查明的狀況下，系統誤
 		<br> 「在91年入學同學號學生的獎懲記錄中填入 101學年同學號學生的流水號」
 		<br>，導至在查詢101學年該生資料時，其資料統計數錯誤(因為把91年資料也統計進來)。<br>
 		<br><font color=blue>修正原理：</font>
 		<br>把 91 年學生的獎懲統計資料，正確填入屬於 91 年學生的流水號即可。<br>
 	</td>
</tr>
  <tr>
  	<td><br><input type="checkbox" name="confirm_save" value="1">進行修正(確定要修正再打勾，否則僅進行觀察)<br></td>
 </tr>	
 
 <tr>
  <td colspan="2"><br><input type="button" value="開始" onclick="document.myform.mode.value='start';document.myform.submit();"> </td>
 </tr>

</table>
 
</form>

<?php
if ($_POST['mode']=="start") {
$query="SELECT a.seme_year_seme,a.stud_id,a.student_sn from stud_seme_rew a,stud_seme b where a.seme_year_seme=b.seme_year_seme and a.stud_id=b.stud_id and a.student_sn!=b.student_sn";
$result=mysql_query($query);
//取出資料 stud_seme_rew資料, 比對 seme_year_seme, stud_id, student_sn
$i=0;
 while ($row=mysql_fetch_array($result)) {
 	$i++;
 	$seme_year_seme=$row['seme_year_seme'];
 	$stud_id=$row['stud_id'];
 	$old_student_sn=$row['student_sn'];
 	
 	$query="select a.student_sn,b.stud_name from stud_seme a,stud_base b where a.student_sn=b.student_sn and a.seme_year_seme='$seme_year_seme' and a.stud_id='$stud_id'";
  $res=mysql_query($query);
  list($student_sn,$stud_name)=mysql_fetch_row($res);
  echo "(記錄 $i )".$seme_year_seme."學期 , 學生:".$stud_name."($stud_id) 的 student_sn=".$old_student_sn." ==>應修正為".$student_sn;
  
  if ($_POST['confirm_save']==1) {
  	$query="update stud_seme_rew set student_sn='".$student_sn."' where seme_year_seme='$seme_year_seme' and stud_id='$stud_id' and student_sn='$old_student_sn'";
    if (mysql_query($query)) {
     echo "已修正!";
    } else {
     echo "<font color=red>修正失敗!!!</font>";
    }
  }
  
  echo "<br>";
  
 }
  echo "<br><font color='red'>執行完畢!</font>";
  if ($i==0) echo " 沒發現錯誤。";
} // end if post

?>


