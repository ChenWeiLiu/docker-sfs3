<?php

// $Id:$

//取得設定檔
include_once "config.php";

sfs_check();

//秀出網頁
head("個人服務學習記錄");

$tool_bar=&make_menu($school_menu_p);

//列出選單
echo $tool_bar;

//目前選定學期
$c_curr_seme=$_POST['c_curr_seme'];
//目前選定班級
$c_curr_class=$_POST['c_curr_class'];


//取得所有學期資料, 每年有兩個學期
$class_seme_p = get_class_seme(); //學年度	
$class_seme_p=array_reverse($class_seme_p,1);
//取得目前學年度
$curr_year=curr_year();

//取得登錄者所在部門
$sql_select = "select post_office from teacher_post where teacher_sn='{$_SESSION['session_tea_sn']}'";
$recordSet = $CONN->Execute($sql_select);
$post_office = $recordSet->fields["post_office"];

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
     $tmp=&get_class_select($s_y,$s_s,"","c_curr_class",'this.form.action="";this.form.target="";this.form.submit',$c_curr_class); 
	 
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
   	  <td><?php echo "【".$classid[5]."】學生服務明細總表";?></td>
   	  <td align="right">
   	  	<input type="checkbox" name="init_check" onclick="checkall('STUD');">全選/全不選
   	  	<input type="button" value="列出勾選學生明細" onclick="document.myform.list_class_all.value='<?php echo $class;?>';document.myform.submit()"></td>
     </tr>
   </table>
	<?php
     //列出學生總表
    student_service_select($class,$c_curr_seme);
	
 ?>
 <table border="0" width="800">
 	<tr>
 		<td style="color:#FF0000;font-size:10pt">※請直接點選學生觀看該生本學期的服務明細。<br>※觀看歷年服務明細，請利用「<a href="service_list_all.php">查詢學生總時數</a>」功能。</td>
 		<td align="right"><input type="button" value="友善列印勾選學生明細" onclick="print_kind()"></td>
 	</tr>
 </table>
 <table border="0" width="700">
 	 <tr>
 	   <td>
 <?php
  if ($_POST['student_sn']!='') {
  	list_service($_POST['student_sn'],$c_curr_seme,$classid[5]);
  }
  
 if ($_POST['list_class_all']!="") {
 	//list_class_all($_POST['list_class_all'],$c_curr_seme,$classid[5]);
 	
  //列出勾選 	  
  foreach ($_POST['STUD'] as $student_sn=>$seme_num) {
     		list_service($student_sn,$c_curr_seme,$classid[5]);
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
 	//
 	//flagWindow=window.open('service_print.php?c_curr_seme=<?php echo $c_curr_seme;?>&c_curr_class=<?php echo $c_curr_class;?>&list_class_all=<?php echo $class;?>','service_print','width=640,height=420,resizable=1,toolbar=1,scrollbars=1');
  document.getElementById("myform").target='_blank';
  document.getElementById("myform").action = 'service_print.php';
  document.myform.submit();
 }
 
 function print_here() {
  document.getElementById("myform").target='';
  document.getElementById("myform").action = 'service_list.php';
  document.myform.submit();
}
</Script>
