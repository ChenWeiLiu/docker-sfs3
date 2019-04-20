<?php

// $Id: reward_one.php 6735 2012-04-06 08:14:54Z smallduh $

//取得設定檔
include_once "config.php";

//載入服務學習模組的公用函式
include_once "../stud_service/my_functions.php";

sfs_check();

// 健保卡查核
switch ($ha_checkary){
        case 2:
                ha_check();
                break;
        case 1:
                if (!check_home_ip()){
                        ha_check();
                }
                break;
}


//秀出網頁
head("服務學習- 填寫自我省思");

//模組選單
print_menu($menu_p);

if ($_SESSION['session_who'] != "學生") {
	echo "很抱歉！本功能模組為學生專用！";
	exit();
}

//模組變數 $feedback_deadline 填寫期限


$C[1]="一";
$C[2]="二";
$C[3]="三";
$C[4]="四";
$C[5]="五";
$C[6]="六";
$C[7]="七";
$C[8]="八";
$C[9]="九";

$TODAY=date("Y-m-d");

//目前選定學期
$c_curr_seme=$_POST['c_curr_seme'];

//取得所有學期資料, 每年有兩個學期
$class_seme_p = get_class_seme(); //學年度	

//取得目前學年度
$curr_year=curr_year();

//填寫期限
$feedback_deadline=($feedback_deadline==0)?60:$feedback_deadline;

//儲存資料
if ($_POST['mode']=='save') {
 foreach ($_POST['feedback'] as $sn=>$feedback) {
  $query="update stud_service_detail set feedback='$feedback' where sn='$sn' and student_sn='".$_SESSION['session_tea_sn']."'";
  if (mysql_query($query)) {
   $SAVE_INFO="己於".date("Y-m-d H:i:s")."儲存完畢!";
  }else{
   echo "Error! Query=$query";
   exit();
  } 
 }
}


?>

<form method="post" action="<?php $_SERVER['PHP_SELF'];?>" name="myform">
	<select name="c_curr_seme" onchange="this.form.submit()">
	<option style="color:#FF00FF">請選擇學期</option>
	<?php
	foreach ($class_seme_p as $tid=>$tname) {
	  if (substr($tid,0,3)>$curr_year-3) {
    ?>
      		<option value="<?php echo $tid;?>" <?php if ($c_curr_seme==$tid) echo "selected";?>><?php echo $tname;?></option>
   <?php
      }
    } // end foreach
    ?>
</select> 
</form>
 <?php
 //列出該生該學期所有服務學習資料 
 if ($c_curr_seme!="") {
  $query="select  a.*,b.sn as detail_sn,b.minutes,b.feedback,c.seme_class from stud_service a, stud_service_detail b,stud_seme c where a.sn=b.item_sn and a.year_seme='$c_curr_seme' and b.student_sn='".$_SESSION['session_tea_sn']."' and c.seme_year_seme='$c_curr_seme' and c.student_sn=b.student_sn";
  $res=mysql_query($query);
  if (mysql_num_rows($res)==0) {
   echo "本學期你沒有任何服務記錄!!";
   exit();
  } else {
   ?>
   <table border="0" width="100%">
    <tr>
     <td style="color:#0000FF">本學期你的服務記錄如下，記得要填寫自我省思：(填寫期限<?php echo $feedback_deadline;?>天)<font size="2" color=red><?php echo $SAVE_INFO;?></font></td>
    </tr>
   </table>
   <form method="post" name="myform1" action="<?php echo $_SERVER['PHP_SELF'];?>">
   	<input type="hidden" name="mode" value="">
   	<input type="hidden" name="c_curr_seme" value="<?php echo $c_curr_seme;?>">
   <table border="1" style="border-collapse:collapse" bordercolor="#000000" width="820">
     <tr bgcolor="#FFCCFF">
       <td align="center" width="100">年級及學期</td>
       <td align="center" width="100">年/月/日</td>
       <td align="center" width="240">參加校內外公共服務<br>學習事項及活動項目</td>
       <td align="center" width="50">時數</td>
       <td align="center" width="80">登錄單位</td>
       <td align="center" width="350">自我省思</td>
     </tr>
     <?php
     $M=0;
     while ($row=mysql_fetch_array($res)) {
     	$c=substr($row['seme_class'],0,1);
      ?>
     <tr>
       <td align="center"><?php echo $C[$c]."-".substr($row['year_seme'],-1);?></td>
       <td align="center"><?php echo $row['service_date'];?></td>
       <td>【<?php echo $row['item'];?>】<br><?php echo $row['memo'];?>
       	<?PHP
       	 if ($row['confirm']==0) {
       	 ?>
       	 <br><font color=red size=1>《尚未認證!》</font>
       	 <?php
       	 }
       	?>
       	</td>
       <td align="center"><?php echo round($row['minutes']/60,2);?></td>
       <td align="center"><?php echo getPostRoom($row['department']);?></td>
       <td >
       	<?php
       	 if (deadline_days($row['service_date'])>$feedback_deadline) {
       	   echo $row['feedback'];
       	 } else {
       	?>
       	<textarea name="feedback[<?php echo $row['detail_sn'];?>]" rows="5" cols="45"><?php echo $row['feedback'];?></textarea>
       	<?php
         }
       	?>
       </td>
     </tr>
      <?php
      if ($row['confirm']==1) $M+=$row['minutes'];
     }
     ?>
     
   </table>
   <table border="0" width="100%">
    <tr>
     <td style="color:#800000;font-size:14pt"><b>本學期你的服務時數已累計達 <?php echo round($M/60,2);?> 小時</b></td>
    </tr>
   </table>
   <input type="button" value="儲存資料" onclick="document.myform1.mode.value='save';document.myform1.submit()" style="color:#FF0000">
   </form>
   <?php
  }

 }
 
function deadline_days($day) {
 global $TODAY;
 /***
   $start_time_value=mktime($leaves_starttime_hours,$leaves_starttime_mins,1,
                            $leaves_starttime_month,$leaves_starttime_day,
                            $leaves_starttime_year);
   $end_time_value=mktime($leaves_endtime_hours,$leaves_endtime_mins,1,
                          $leaves_endtime_month,$leaves_endtime_day,
                          $leaves_endtime_year);
  ***/
   $start_time_value=mktime(0,0,1,substr($day,5,2),substr($day,8,2),substr($day,0,4));
   $end_time_value=mktime(0,0,1,substr($TODAY,5,2),substr($TODAY,8,2),substr($TODAY,0,4));
    
   $total_secs=$end_time_value-$start_time_value;
   //$total_mins=$total_secs/60;
   //$total_hours=$total_mins/60;

   $total_days=($total_secs/86400); // 即兩個時間的相差天數
   //$hours=$total_hours%24; // 幾小時
   //$mins=$total_mins%60; // 幾分
  
   return $total_days;
  
}
 
 ?>
 