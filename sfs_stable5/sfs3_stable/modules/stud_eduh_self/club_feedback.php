<?php

// $Id: reward_one.php 6735 2012-04-06 08:14:54Z smallduh $


if ($_SESSION['session_who'] != "學生") {
	echo "很抱歉！本功能模組為學生專用！";
	exit();
}

//檢查是否開放社團模組
if ($m_arr["club_enable"]!="1"){
   echo "目前不開放社團活動模組！";
   exit;
}


function check_mysqli_param($param){
	if (!isset($param))$param="";
	return $param;
}

//取得目前學年度
$curr_year=curr_year();
$curr_seme=curr_seme();

//目前選定學期
$c_curr_seme=sprintf('%03d%1d',$curr_year,$curr_seme);

//取得學生資料
$STUD=get_student($_SESSION['session_tea_sn'],$c_curr_seme);

//按下儲存時的動作
if ($_POST['mode']=='save' and $_POST['student_sn']==$_SESSION['session_tea_sn']) {
 foreach ($_POST['stud_feedback'] as $club_sn=>$stud_fb) {
     //$query="update association set stud_feedback='$stud_fb' where club_sn='$club_sn' and student_sn='".intval($_POST['student_sn'])."' and seme_year_seme='$c_curr_seme'"; 
     
//mysqli
$mysqliconn = get_mysqli_conn();
$query="update association set stud_feedback=? where club_sn=? and student_sn='".intval($_POST['student_sn'])."' and seme_year_seme='$c_curr_seme'"; 
$stmt="";
$stmt = $mysqliconn->prepare($query);
$stmt->bind_param('ss',check_mysqli_param($stud_fb),check_mysqli_param($club_sn));
$stmt->execute();
$stmt->close();
//mysqli
	 
	 //if (mysql_query($query)) {
	 if ($mysqliconn->affected_rows==1){	 
			 $INFO="資料己於 ".date('Y-m-d H:i:s')."儲存完畢!";
     } else {
       $INFO="Error! query=".$query;
     }
 }
}


//檢查學生是否已參加社團
//$my_club=get_student_join_club($STUD['student_sn'],$c_curr_seme);
if ($my_club=get_student_join_club($STUD['student_sn'],$c_curr_seme)) {
	?>
	<form method="post" name="myform" action="<?php echo $_SERVER['PHP_SELF'];?>">
		<input type="hidden" name="club_menu" value="<?php echo $_POST['club_menu'];?>">
		<input type="hidden" name="mode" value="">
		<input type="hidden" name="student_sn" value="<?php echo $STUD['student_sn'];?>">
	本學期你參加了下列社團，請針對社團活動寫出你的自我省思
	<table border="1" style="border-collapse:collapse" bordercolor="#800000">
	 <tr bgcolor="#FFCCFF">
	   <td width="100" style="font-size:10pt" align="center">學期</td>
	   <td width="100" style="font-size:10pt" align="center">社團名稱</td>
	   <td width="50" style="font-size:10pt" align="center">成績</td>
	   <td width="80" style="font-size:10pt" align="center">擔任職務</td>
	   <td width="200" style="font-size:10pt" align="center">老師評語</td>
	   <td width="300" style="font-size:10pt" align="center">自我省思</td>
	 </tr>
	
	<?php
	foreach ($my_club as $My) {
		 if ($My['seme_year_seme']==$c_curr_seme) {
		 	$My['score']=($My['score']>0)?$My['score']:"-";
	     ?>
	 <tr>
	   <td width="100" style="font-size:10pt" align="center"><?php echo sprintf("%d",substr($My['seme_year_seme'],0,3));?>學年度<br>第<?php echo substr($My['seme_year_seme'],-1);?>學期</td>
	   <td width="100" style="font-size:10pt" align="center"><?php echo $My['club_name'];?></td>
	   <td width="50" style="font-size:10pt" align="center"><?php echo $My['score'];?></td>
	   <td width="80" style="font-size:10pt" align="center"><?php echo $My['stud_post'];?></td>
	   <td width="200" style="font-size:10pt"><?php echo $My['description'];?></td>
	   <td width="300" style="font-size:10pt" align="center"><textarea name="stud_feedback[<?php echo $My['club_sn'];?>]" rows="8" cols="36"><?php echo $My['stud_feedback'];?></textarea></td>
	 </tr>
	     <?php	
		 }
	}
	?>
	</table>
	<input type="button" value="儲存自我省思資料" style="color:#FF0000" onclick="document.myform.mode.value='save';document.myform.submit()">
	<table width="100%" border="0">
	  <tr><td style="color:#FF0000;font-size:10pt"><?php echo $INFO;?></td></tr>
  </table>
	</form>
	<?php
} else {
 echo "本學期你沒有參加任何社團活動喔!";
}
?>
  <table border="0">
   <tr><td style="color:#0000FF">§你的所有社團活動記錄§</td></tr>
  </table>
	<table border="1" style="border-collapse:collapse" bordercolor="#800000">
	 <tr bgcolor="#FFCCFF">
	   <td width="100" style="font-size:10pt" align="center">學期</td>
	   <td width="100" style="font-size:10pt" align="center">社團名稱</td>
	   <td width="50" style="font-size:10pt" align="center">成績</td>
	   <td width="80" style="font-size:10pt" align="center">擔任職務</td>
	   <td width="200" style="font-size:10pt" align="center">老師評語</td>
	   <td width="300" style="font-size:10pt" align="center">自我省思</td>
	 </tr>
	
	<?php
	$my_club=get_student_join_club($STUD['student_sn']);
	foreach ($my_club as $My) {
		 //if ($My['seme_year_seme']==$c_curr_seme) {
		 	$My['score']=($My['score']>0)?$My['score']:"-";
	     ?>
	 <tr>
	   <td width="100" style="font-size:10pt" align="center"><?php echo sprintf("%d",substr($My['seme_year_seme'],0,3));?>學年度<br>第<?php echo substr($My['seme_year_seme'],-1);?>學期</td>
	   <td width="100" style="font-size:10pt" align="center"><?php echo $My['club_name'];?></td>
	   <td width="50" style="font-size:10pt" align="center"><?php echo $My['score'];?></td>
	   <td width="80" style="font-size:10pt" align="center"><?php echo $My['stud_post'];?></td>
	   <td width="200" style="font-size:10pt"><?php echo $My['description'];?></td>
	   <td width="300" style="font-size:10pt"><?php echo $My['stud_feedback'];?></td>
	 </tr>
	     <?php	
		 //}
	}
	?>
	</table>