<?php
                                                                                                                             
// $Id: search.php 8649 2015-12-18 03:50:01Z qfon $

// --系統設定檔
include "config.php"; 
// 不需要 register_globals
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}

?>
<table border=0 width=100%>
<form method=get name=myform action="<?php echo $PHP_SELF ?>">
<tr><td align=center><B>榮譽榜查詢</B>請輸入學生姓名：
&nbsp;<input type="text" name="s_str" maxlength=16 value="<?php echo $s_str ?>">　
<input type="submit" name="key" value="搜尋">　　　　<a href="list.php">回目錄</a></td>
</tr>
</form>
</table>
<?php
if($s_str) {
///mysqli	
$mysqliconn = get_mysqli_conn();
$stmt = "";

$s_str = "%$s_str%";
if ($s_str <> "") {
    $stmt = $mysqliconn->prepare("select stud_id,stud_name,curr_class_num,stud_study_cond  from stud_base where stud_name like ? ");
    $stmt->bind_param('s', $s_str);
} 

$stmt->execute();

$stmt->bind_result($stud_id,$stud_name,$curr_class_num,$cond);

///mysqli

//$sql_select = " select stud_id,stud_name,curr_class_num,stud_study_cond  from stud_base where stud_name like '%$s_str%'  ";
//$result = mysql_query ($sql_select,$conID)or die ($sql_select);
echo "<table align=center width='90%' border='1' cellspacing='0' cellpadding='4' bgcolor='#CCFFFF' bordercolor='#33CCFF'>
 <tr bgcolor='#66CCFF'> 
    <td >學生姓名</td>
    <td >年班座號</td> 
  </tr>";

while ($stmt->fetch()) {
	$note=$cond_arr[$cond];	
	$curr=curr_class_num2_data($curr_class_num);
 	$curr_class_num=$curr[class_id]."-".$curr[num];
      echo "<tr><td><a href='show.php?stud_id=$stud_id'>$stud_name</a></td><td>$curr_class_num $note</td</tr>" ;
 

}
/*	
while ($row = mysql_fetch_array($result)){
	$stud_id = $row["stud_id"];
	$stud_name = $row["stud_name"];
	$cond = $row["stud_study_cond"];
	$note=$cond_arr[$cond];	
	$curr=curr_class_num2_data($row["curr_class_num"]);
 	$curr_class_num=$curr[class_id]."-".$curr[num];
      echo "<tr><td><a href='show.php?stud_id=$stud_id'>$stud_name</a></td><td>$curr_class_num $note</td</tr>" ;
   
  }
  
  */
echo "</table>";
	
}
?>
