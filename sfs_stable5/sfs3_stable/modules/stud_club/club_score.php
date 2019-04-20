<?php

// $Id: reward_one.php 6735 2012-04-06 08:14:54Z smallduh $

//取得設定檔
include_once "config.php";

sfs_check();


//秀出網頁
head("社團活動 - 社團成績輸入");

$tool_bar=&make_menu($school_menu_p);

//列出選單
echo $tool_bar;

if ($_SESSION['session_who'] != "教師") {
	echo "很抱歉！本功能模組為教師專用！";
	exit();
}

//取得目前學年度
$curr_year=curr_year();
$curr_seme=curr_seme();

//目前選定學期
$c_curr_seme=sprintf('%03d%1d',$curr_year,$curr_seme);

//取得學期社團設定
$SETUP=get_club_setup($c_curr_seme);

$query="select * from stud_club_base where year_seme='$c_curr_seme' and club_teacher='".$_SESSION['session_tea_sn']."'";
$result=mysql_query($query);
if (mysql_num_rows($result)) {
//主程式開始
//$club_base=mysql_fetch_array($result);
 
if ($_POST['mode']=="save" and $_POST['club_sn']!='') {
	foreach ($_POST['score'] as $student_sn=>$score) {
	  $query="update association set score='$score',description='".$_POST['description'][$student_sn]."',stud_post='".$_POST['stud_post'][$student_sn]."',update_sn='".$_SESSION['session_tea_sn']."' where seme_year_seme='$c_curr_seme' and student_sn='$student_sn' and club_sn='".$_POST['club_sn']."'";
	  if (!mysql_query($query)) {
	   echo "Error! Query=$query";
	   exit();
	  }		
	}
  $INFO="已於".date('Y-m-d H:i:s')."儲存成績資料";	
}

echo "<font color='#800000'>指導老師：".get_teacher_name($_SESSION['session_tea_sn'])."<br>";
echo "社團名稱：";

?>
<form name="myform" method="post" action="<?php  echo $_SERVER['PHP_SELF'];?>">
<select size="1" name="club_sn" onchange="document.myform.submit()">
	<?php
	 $i=0;
	 while ($row=mysql_fetch_array($result)) {
	 	$i++;
	 	if ($i==1 and $_POST['club_sn']=='') $_POST['club_sn']=$row['club_sn'];
	 ?>
	  <option value="<?php echo $row['club_sn'];?>" <?php if ($row['club_sn']==$_POST['club_sn']) echo " selected";?>><?php echo $row['club_name'];?></option>
	 <?php
	 }
	?>
</select>
<?php

 if ($_POST['club_sn']!='') {
//取得學生成績
 $query="select a.*,b.seme_class,b.seme_num,c.stud_name from association a,stud_seme b,stud_base c where a.seme_year_seme='$c_curr_seme' and a.club_sn='".$_POST['club_sn']."' and b.seme_year_seme='$c_curr_seme' and a.student_sn=b.student_sn and a.student_sn=c.student_sn and (c.stud_study_cond=0 or c.stud_study_cond=5) order by seme_class,seme_num";
 $res=mysql_query($query);
?>
	<input type="hidden" name="mode" value="">
<table border="1" style="border-collapse:collapse" bordercolor="#800000">
 <tr bgcolor='#CCFFCC'>
  <td align="center" style="color:#000000;font-size:10pt" width="40">序號</td>
 	<td align="center" style="color:#0000FF" width="70">班級</td>
 	<td align="center" style="color:#0000FF" width="50">座號</td>
 	<td align="center" style="color:#0000FF" width="80">姓名</td>
 	<td align="center" style="color:#0000FF" width="50">成績</td>
 	<td align="center" style="color:#0000FF" width="80">擔任職務</td>
 	<td align="center" style="color:#0000FF" width="280">學習描述</td>
 	<?php
  	if ($SETUP['show_teacher_feedback']) {
  	?>
    <td align="center" style="color:#0000FF" width="150">學生自我省思</td>
  	<?php
  	}
  	?> 	
 </tr>
 <?php
 $i=0;
  while ($row=mysql_fetch_array($res)) {
  	$i++;
  	$CLASS_name=$school_kind_name[substr($row['seme_class'],0,1)];
  	if ($row['score']=="0")  $row['score']='';
  ?>
  <tr>
    <td align="center" style="color:#000000;font-size:10pt" width="50"><?php echo $i;?></td>
  	<td align="center"><?php echo $CLASS_name.sprintf('%d',substr($row['seme_class'],1,2))."班";?></td> 
  	<td align="center"><?php echo $row['seme_num'];?></td> 
  	<td align="center"><?php echo $row['stud_name'];?></td> 
  	<td align="center"><input type="text" name="score[<?php echo $row['student_sn'];?>]" value="<?php echo $row['score'];?>" size="3"></td> 
  	<td align="center"><input type="text" name="stud_post[<?php echo $row['student_sn'];?>]" value="<?php echo $row['stud_post'];?>" size="8"></td> 
  	<td ><input type="text" name="description[<?php echo $row['student_sn'];?>]" value="<?php echo $row['description'];?>" size="35"></td> 
  	<?php
  	if ($SETUP['show_teacher_feedback']) {
  	?>
  	<td style="font-size:8pt" width="150"><?php echo $row['stud_feedback']; ?></td>
  	<?php
  	}
  	?>
 </tr>  
  <?php 
  } // end while
 ?>
</table>
<input type="button" value="儲存" onclick="document.myform.mode.value='save';document.myform.submit()">※註: 學生若未擔任職務，該欄可留空白。
<table width="100%" border="0">
	<tr><td style="color:#FF0000;font-size:10pt"><?php echo $INFO;?></td></tr>
</table>
</form>
<?php
 } // end if $_POST['club_sn']
} else {
 echo "抱歉！您未擔任任何社團指導老師，不需輸入成績。";
}

?>