<?php

// $Id: stud_eduh_list.php 6461 2011-06-13 02:44:10Z infodaes $

// 引入您自己的 config.php 檔
require "config.php";

// 認證檢查
sfs_check();
//目前學年學期
$seme_year_seme = sprintf("%03d%d",curr_year(),curr_seme());

//印出檔頭
head();

//列出選單
$tool_bar=&make_menu($menu_p);
echo $tool_bar;

//檢查管理權限
$module_manager=checkid($_SERVER['SCRIPT_FILENAME'],1);
if ($module_manager!=1) {
 echo "抱歉 , 您沒有管理權限!";
 exit();
}
//submit後的動作 ========================================================================
if ($_POST['mode']=='save') {
	$query="delete from `score_eduh_teacher2` where year_seme='$seme_year_seme' and teacher_sn='".$_POST['teacher_sn']."'";
	mysql_query($query);
  //依序存入 checkbox 的資料
	foreach ($_POST['class_id'] as $class_id) {
    $query="insert into `score_eduh_teacher2` (year_seme,teacher_sn,class_id,update_sn) values ('$seme_year_seme','".$_POST['teacher_sn']."','$class_id','".$_SESSION['session_tea_sn']."')";
    mysql_query($query);	
	}
	$MESSAGE="已於".date("Y-m-d h:i:s")."設定".get_teacher_name($_POST['teacher_sn'])."資料!";
}
//===<<主程式開始>>=======================================================================
//1.先列出老師select列表
//2.列出所有班級供勾選


$query="SELECT a.teacher_sn,a.teach_id,a.name, b.post_kind, b.post_office,d.title_name ,b.class_num FROM teacher_base a , teacher_post b, teacher_title d WHERE a.teacher_sn = b.teacher_sn AND b.teach_title_id = d.teach_title_id  AND a.teach_condition = 0 order by a.name" ;
//echo $query;
$res_teacher=mysql_query($query);
?>
<form method="post" name="form_select" action="<?php echo $_SERVER['PHP_SELF'];?>">
<table border="0">
	<tr>
	<td>請選擇教師：
		<select name="teacher_sn" size="1" onchange="javascript:document.form_select.submit()">
		  <option value="" style="color:#FF00FF">請選擇...</option>
		 <?php
		 while ($row=mysql_fetch_array($res_teacher)) {
		 	?>
		  <option value="<?php echo $row['teacher_sn'];?>"<?php if ($_POST['teacher_sn']==$row['teacher_sn']) echo " selected";?>><?php echo $row['name'];?></option>
		  <?php
		 } // end while		 
		 ?>	
		</select>
		</td>	
	</tr>
</table>

<?php
 if ($_POST['teacher_sn']!="") {
?>
<input type="hidden" name="mode" value="">
<table border="0">
	<tr>
	<td style="color:#FF00FF">請勾選本教師負責的班級</td>	
	</tr>
</table>

<table border="0" >
 <tr valign="top">	
 	<?php
 	//從 school_class 找出班級, 依年級
$query="SELECT DISTINCT c_year FROM `school_class` WHERE year ='".curr_year()."' AND semester ='".curr_seme()."' order by c_year";
$res_year=mysql_query($query);
while ($row_year=mysql_fetch_array($res_year)) {
 //列出每一年級的班級
 ?>
 <td>
 	
 	<table border="1" style="border-collapse:collapse" bordercolor="#800000">
 		<tr bgcolor="#FFCCFF">
 	    <td>選</td>
 	    <td>班級</td>
 		</tr>
 <?php
 $query="select class_id,c_year,c_name,c_kind  from `school_class` where c_year='".$row_year['c_year']."' and  year ='".curr_year()."' AND semester ='".curr_seme()."' order by class_id";
 $res_class=mysql_query($query);
 		while($row_class=mysql_fetch_array($res_class)) {
 			$c_year=$row_class['c_year'];
 			$c_name=$row_class['c_name'];
 	   ?>
 	  <tr>
 	    <td style="font-size:10pt;color:"><input type="checkbox" name="class_id[]" value="<?php echo $row_class['class_id'];?>"<?php if (check_class_id($seme_year_seme,$_POST['teacher_sn'],$row_class['class_id'])) echo " checked";?>></td>
 	    <td><?php echo $school_kind_name[$c_year]."".$c_name."班";?></td>
 		</tr>
 	   
 	   <?php
   	} // end while $row_class
  	?>
  </table>
  </td>
  <?php
  } // end while $row_year
} // end if $_POST['teacher_sn']
?>
</tr>
</table>
</form>	


<table border="0">
	 <tr>
   <td style="color:#FF0000;font-size:10pt">
   	<input type="button" value="儲存設定" onclick="document.form_select.mode.value='save';document.form_select.submit()">
   	<?php echo $MESSAGE;?>
   </td>
  </tr>
</table>

 <?php 
//印出尾頭
foot();
?> 
