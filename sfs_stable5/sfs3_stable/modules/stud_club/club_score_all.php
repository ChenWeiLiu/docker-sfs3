<?php

// $Id: reward_one.php 6735 2012-04-06 08:14:54Z smallduh $

//取得設定檔
include_once "config.php";

sfs_check();


//秀出網頁
head("社團活動 - 學生歷年記錄");

$tool_bar=&make_menu($school_menu_p);

//列出選單
echo $tool_bar;

$module_manager=checkid($_SERVER['SCRIPT_FILENAME'],1);
if ($module_manager!=1) {
 echo "抱歉 , 您沒有無管理權限!";
 exit();
}


//目前選定學期
$c_curr_seme=$_POST['c_curr_seme'];
//目前選定班級
$c_curr_class=$_POST['c_curr_class'];


//取得所有學期資料, 每年有兩個學期
$class_seme_p = get_class_seme(); //學年度	
$class_seme_p=array_reverse($class_seme_p,1);
//取得目前學年度
$curr_year=curr_year();

?>

<form method="post" action="<?php $_SERVER['PHP_SELF'];?>" name="myform" id="myform" target="">
	<input type="hidden" name="student_sn" value="">
	<input type="hidden" name="list_class_all" value="">
	<select name="c_curr_seme" onchange="this.form.submit()">
	<option style="color:#FF00FF">請選擇學期</option>
	<?php
	while (list($tid,$tname)=each($class_seme_p)){
	  if (substr($tid,0,3)>$curr_year-3) {
    ?>
      		<option value="<?php echo $tid;?>" <?php if ($c_curr_seme==$tid) echo "selected";?>><?php echo $tname;?></option>
   <?php
      }
    } // end while
    ?>
</select> 
 <?php
 
  if ($c_curr_seme!="") {
  	
    $s_y = substr($c_curr_seme,0,3);
    $s_s = substr($c_curr_seme,-1);
    
    //做出年級與班級選單
     $tmp=&get_class_select($s_y,$s_s,"","c_curr_class","this.form.submit",$c_curr_class); 
	 
	 echo $tmp;
	 
  }

  if ($c_curr_class!="") {
   	$Cyear=substr($c_curr_class,-5,2);
  	$Cnum=substr($c_curr_class,-2,2);
	$classid=class_id_2_old($c_curr_class);
	 $class=sprintf('%3d',$Cyear.$Cnum);
	?>
   <table border="0"  width="800">
     <tr>
   	  <td><?php echo "《".$s_y."學年第".$s_s."學期》【".$classid[5]."】學生列表";?></td>
   	  <td align="right">
   	  	<input type="checkbox" name="score_list" value="1" <?php if ($_POST['score_list']) echo " checked";?>>含成績
   	  	<input type="checkbox" name="init_check" onclick="check_copy('init_check','STUD');">全選/全不選<input type="button" value="列出勾選學生明細" onclick="document.myform.list_class_all.value='<?php echo $class;?>';print_here()"></td>
     </tr>
   </table>
	<?php
     //列出學生總表
    student_club_select($class,$c_curr_seme);
	
 ?>
 <table border="0" width="800">
 	<tr>
 		<td style="color:#FF0000;font-size:10pt"><img src="images/filefind.png"> 1.本處將列出學生歷年所有社團記錄。<br><img src="images/filefind.png"> 2.僅需單一學期資料，請利用「日常成績管理/<a href="../score_nor/club_serv.php">列印學期通知單</a>」功能。</td>
 		<td align="right" valign="top"><input type="button" value="友善列印勾選學生明細" onclick="print_kind()"></td>
 	</tr>
 </table>
 <table border="0" width="800">
 	 <tr>
 	   <td>
 <?php
  if ($_POST['student_sn']!='') {
  	list_club_record($_POST['student_sn']);
  }
  
 if ($_POST['list_class_all']!="") {
  	//列出勾選 	  
  	foreach ($_POST['STUD'] as $student_sn=>$seme_num) {
     		list_club_record($student_sn);
  	}
 }
  
 ?>
   </td>
  </tr>
</table>
</form>
 <?php
  } // end if c_curr_class
 
foot();

// =====<< 以下為各種 function >>=====================================================================================


?>
<script language="javaScript">

function check_stud(student_sn) {

  	document.myform.student_sn.value=student_sn;
  	document.myform.submit();

}

function print_kind() {
  document.getElementById("myform").target='_blank';
  document.getElementById("myform").action = 'club_print_all.php';
  document.myform.submit();
  document.getElementById("myform").target='';
  document.getElementById("myform").action = 'club_score_all.php';
}

function print_here() {
  document.getElementById("myform").target='';
  document.getElementById("myform").action = 'club_score_all.php';
  document.myform.submit();
}

</Script>



