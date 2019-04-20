<?php

// $Id: reward_one.php 7062 2013-01-08 15:37:05Z smallduh $

//取得設定檔
include_once "config.php";
include "../../include/sfs_case_dataarray.php";

sfs_check();


//秀出網頁
head("轉學生補登他校資料");

	//相關功能表
$tool_bar=&make_menu($school_menu_p);
echo $tool_bar;

$module_manager=checkid($_SERVER['SCRIPT_FILENAME'],1);
if ($module_manager!=1) {
 echo "抱歉 , 您沒有無管理權限!";
 exit();
}


//目前學期
$c_curr_seme=sprintf("%03d%d",curr_year(),curr_seme());

//取得所有學期
$seme_list=get_class_seme();

//目前選定學期
$work_year_seme=$_POST['work_year_seme'];
if ($work_year_seme=='') $work_year_seme = $c_curr_seme;
$move_year_seme = intval(substr($work_year_seme,0,-1)).substr($work_year_seme,-1,1);


  //已點選的學生 student_sn
  $selected_student=$_POST['selected_student'];


//增加一個社團記錄
if ($_POST['act']=='club_add') {
 $year_seme=sprintf("%03d",substr($_POST['year_seme'],0,strlen($_POST['year_seme'])-1)).substr($_POST['year_seme'],-1);
 $query="insert into association (student_sn,seme_year_seme,association_name,score,stud_post,description,update_sn,update_time) values ('".$selected_student."','$year_seme','".$_POST['association_name']."','".$_POST['score']."','".$_POST['stud_post']."','".$_POST['description']."','".$_SESSION['session_tea_sn']."',NOW())";
 mysql_query($query);
 $_POST['act']='';
}
//刪除一個社團記錄
if ($_POST['act']=='club_delete') {
 $query="delete from association where sn='".$_POST['option1']."'";
 mysql_query($query);
 $_POST['act']='';
 $_POST['option1']='';
}

?>

<form method="post" name="myform" act="<?php echo $_SERVER['php_self'];?>">
	<input type="hidden" name="act" value="<?php echo $_POST['act'];?>">
	<input type="hidden" name="option1" value="<?php echo $_POST['option1'];?>">	
		※選擇轉入的學期：
	<select name="work_year_seme" onchange="document.myform.submit();">
  <?php
		foreach($seme_list as $key=>$value) {
	?>		
	 <option value="<?php echo $key;?>" <?php if ($key==$work_year_seme) echo " selected";?>><?php echo $value;?></option>
	 <?php
	 }
	 ?>
	</select><br>
	
<?php
  //針對學期列出學生
if ($work_year_seme!='') {
  	$check_student=0;
  	//取得該學期轉入學生清單
		$sql="SELECT a.*,b.stud_id,b.stud_name,b.stud_sex,b.stud_study_year FROM stud_move a LEFT JOIN stud_base b ON a.student_sn=b.student_sn WHERE a.move_kind in (2,3,14) AND move_year_seme='$move_year_seme' ORDER BY move_date DESC";
		$recordSet=$CONN->Execute($sql) or user_error("讀取stud_move、stud_base資料失敗！<br>$sql",256);
		$col=3; //設定每一列顯示幾人
		$studentdata="※選擇欲補登的學生：<table>";
		while(!$recordSet->EOF) {
			$currentrow=$recordSet->currentrow()+1;
			if($currentrow % $col==1) $studentdata.="<tr>";
			$student_sn=$recordSet->fields['student_sn'];
			$stud_id=$recordSet->fields['stud_id'];
			$stud_name=$recordSet->fields['stud_name'];
			$stud_move_date=$recordSet->fields['move_date'];
			if($recordSet->fields['stud_sex']=='1') $color='#CCFFCC'; else  $color='#FFCCCC';
			if($student_sn==$selected_student) {
				$color='#FFFFAA';
				$stud_study_year=$recordSet->fields['stud_study_year'];
				$selected_student_id=$stud_id;
			}
	    
	    if ($student_sn==$selected_student) {
			  $student_radio="<input type='radio' value='$student_sn' name='selected_student' checked onclick='document.myform.submit()'>( $student_sn - $stud_id ) $stud_name - $stud_move_date";	
			  $check_student=1;
			} else {
			  $student_radio="<input type='radio' value='$student_sn' name='selected_student' onclick='document.myform.submit()'>( $student_sn - $stud_id ) $stud_name - $stud_move_date";	
			}
			$studentdata.="<td bgcolor='$color' align='center'> $student_radio </td>";

			if( $currentrow % $col==0  or $recordSet->EOF) $studentdata.="</tr>";
			$recordSet->movenext();
	  } // end while
			$studentdata.='</table><hr>';
		
    echo $studentdata;
    
    //若已點選學生, 列出該生的資料及新增表單
    if ($check_student) {
    ?>
		  <font color='#800000'>※補登社團記錄</font>
		  <table border='1' style='border-collapse:collapse' bordercolor='#800000'>
		    <tr bgcolor='#FFCCFF'>
		     <td align='center'>學期</td>
		     <td align='center'>社團名稱</td>
		     <td align='center'>成績(0-100分)</td>
		     <td align='center'>職務</td>
		     <td align='center'>指導老師評語</td>
		     <td align='center'>&nbsp;</td>
			  </tr>
		<?php
			$query="select * from association where student_sn='$selected_student' order by seme_year_seme";
			$res=mysql_query($query);
			while ($row=mysql_fetch_array($res,1)) {
			 ?>
		    <tr>
		     <td align='center'><?php echo $row['seme_year_seme'];?></td>
		     <td align='center'><?php echo $row['association_name'];?></td>
		     <td align='center'><?php echo $row['score'];?></td>
				 <td align='center'><?php echo $row['stud_post'];?></td>
		     <td><?php echo $row['description'];?></td>
		     <td align='center'>
		     	<?php
		     	if ($row['club_sn']>0) {
		     	?>
		     	 <font size="2" color=red><i>校內社團</i></font>
		     	<?php
		     	} else {
		     	?>
		     	<input type="button" value="刪除" onclick="if(confirm('您確定要刪除該生的\社團:「<?php echo $row['association_name'];?>」記錄?')) { document.myform.option1.value='<?php echo $row['sn'];?>';document.myform.act.value='club_delete';document.myform.submit(); } ">
			   <?php
			    }
			   ?>
			   </td>
			  </tr>
				<?php
			}  			
			?>  
		    <tr>
		     <td align='center'><input type=;text' name='year_seme' size='5'></td>
		     <td align='center'><input type='text' name='association_name' size='20'></td>
		     <td align='center'><input type='text' name='score' size='5'></td>
		     <td align='center'><input type='text' name='stud_post' size='8'></td>
		     <td><input type='text' name='description' size='50'></td>
		     <td><input type="button" value="新增社團資料" onclick="check_input()">
			  </tr>
		  </table>
      ※說明:<br>
      1.學期請輸入學年+學期別, 如: 99學年第1學期, 則輸入 991 。<br>
      2.以此模組所補登的資料, 在社團模組內無法查得, 但成績單內可正常輸出.
   <?php
  
	} // end if selected_student
    
    
 } // end if ($work_year_seme!='')
?>
	
	

</form>



<?php
foot();
?>

<script>
 function check_input() {
   if (document.myform.year_seme.value=='') {
   	alert('未輸入學年學期');
    return false;
   }
   if (document.myform.association_name.value=='') {
   	alert('未輸入社團名稱');
    return false;
   }
 
  document.myform.act.value='club_add';
  document.myform.submit();

 }

</script>