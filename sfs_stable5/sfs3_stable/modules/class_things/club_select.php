<?php

// $Id: name_form.php 7706 2013-10-23 08:59:03Z smallduh $

/*引入學務系統設定檔*/
include "config.php";

//載入社團活動模組的公用函式
include_once "../stud_club/my_functions.php";

if($_GET['many_col']) $many_col=$_GET['many_col'];
else $many_col=$_POST['many_col'];
//使用者認證
sfs_check();
if(empty($sel_year))$sel_year = curr_year(); //目前學年
if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期
$teacher_sn=$_SESSION['session_tea_sn'];//取得登入老師的id
//找出任教班級
$class_name=teacher_sn_to_class_name($teacher_sn);

//選定的學生
$STUDENT_SN=$_POST['STUDENT_SN'];
//年級
$CLASS=substr($class_name[0],0,1);


	//秀出網頁
	head("社團選填");

	print_menu($menu_p);

    $seme_year_seme=sprintf("%03d",curr_year()).curr_seme();
		
		$c_curr_seme=$seme_year_seme;

?>    
<form method="post" name="myform" action="<?php echo $_SERVER['PHP_SELF'];?>">
 <input type="hidden" name="mode" value="">
<table border="0">
  <tr>
   <td>
   <!-- 名單區 -->
	 <?php    
    $sql="select student_sn,stud_id,seme_num from stud_seme where seme_class='$class_name[0]' and  seme_year_seme='$seme_year_seme' order by  seme_num";
    $rs=$CONN->Execute($sql);
    $m=0;
    ?>
    <table bgcolor='#000000' border=0 cellspacing=1 cellpadding=2>
    	<tr bgcolor='#FAF799'>
    		<td colspan='3'><?php echo $class_name[1];?></td>
    	</tr>
    <?php
	while(!$rs->EOF){
    		$student_sn[$m]=$rs->fields["student_sn"];
        $stud_id[$m] = $rs->fields["stud_id"];
        $site_num[$m] = $rs->fields["seme_num"];
        $rs_name=$CONN->Execute("select stud_name from stud_base where stud_id='$stud_id[$m]' and stud_study_cond =0 ");
        
        if ($rs_name->fields["stud_name"]) {
           $stud_name[$m] = $rs_name->fields["stud_name"];	
           ?>
           <tr bgcolor='#FFFFFF'>
           	<td><input type="radio" name="STUDENT_SN" value="<?php echo $student_sn[$m];?>"<?php if ($_POST['STUDENT_SN']==$student_sn[$m]) echo " checked";?> onclick='document.myform.submit()'></td>
           	<td><?php echo $site_num[$m];?></td>
           	<td><?php echo $stud_name[$m];?></td>
           </tr>
           <?php
		$m++;
	}
        $rs->MoveNext();
    }
	echo "</table>";
	?>
  
   </td>
   <td valign="top">
	 <!-- 選擇區 -->
	 <?php
	 	//取得學期社團設定
	 	$SETUP=get_club_setup($c_curr_seme);
	 	//可選課時段 時分秒月日年 2012-06-01 12:12:00
		$StartSec=date("U",mktime(substr($SETUP['choice_sttime'],11,2),substr($SETUP['choice_sttime'],14,2),0,substr($SETUP['choice_sttime'],5,2),substr($SETUP['choice_sttime'],8,2),substr($SETUP['choice_sttime'],0,4)));
		$EndSec=date("U",mktime(substr($SETUP['choice_endtime'],11,2),substr($SETUP['choice_endtime'],14,2),0,substr($SETUP['choice_endtime'],5,2),substr($SETUP['choice_endtime'],8,2),substr($SETUP['choice_endtime'],0,4)));
		$nowsec=date("U",mktime(date("H"),date("i"),0,date("n"),date("j"),date("Y")));
		//選課時段是否已過
		if ($StartSec > $nowsec or $EndSec < $nowsec) {
			echo "系統時間".date("Y-m-d H:i:s") ."<br>";
 			echo "抱歉，現在非選課時段，無法進行選課！";
 			exit();
		}
		
		//不允許選修多個社團時
if ($SETUP['multi_join']==0) {
	//檢查學生是否已參加社團
	//$my_club=get_student_join_club($STUDENT_SN,$c_curr_seme);
	if ($my_club=get_student_join_club($STUDENT_SN,$c_curr_seme)) {
		echo "本學期此生已參加下列社團，不需參加選課！<br>";
		foreach ($my_club as $My) {
	 		echo "<font color=red>【".$My['club_name']."】</font><br>";
		}
		exit();
	}
} // end if $SETUP['multi_join']

//check_arrange();


if ($StartSec<$nowsec and $EndSec>$nowsec) {
	
	//取得可選修的社團
	$query="select * from stud_club_base where year_seme='$c_curr_seme' and club_open='1' and (club_class='$CLASS' or club_class='100')"; 
	$res_club=mysql_query($query);
	$club_num=mysql_num_rows($res_club);
	/*
	//社團可供選擇名額
	$club_for_stud_num=club_for_stud_num($CLASS,$c_curr_seme);

	//取得該年級人數
	$CLASS_num=class_student_num($CLASS,$c_curr_seme);
	// $CLASS_not_arranged 該年級尚選編班人數
	
 //檢查社團開放總錄取數是否足夠該年級學生選取
  if ($CLASS_not_arranged>$club_for_stud_num) {
  	echo "社團可供選修人數不足！<br>";
  	echo "$CLASS_name 級學生共".$CLASS_num."人 , 尚未編班的學生有".$CLASS_not_arranged."人 <br>";
  	echo "但專屬本年級社團剩餘可供選課的名額僅 ".$club_for_stud_num. "人";
  	exit();
  }
	*/
	//=================================

	if ($_POST['mode']=='insert') {
	  foreach ($_POST['choice'] as $K=>$club_sn) {
	  if ($club_sn!="") {
	  	$CLUB_SET=get_club_base($club_sn);
	  	
	  	$club_student_num=get_club_student_num($c_curr_seme,$row['club_sn']);
			$club_student_number=$club_student_num[0]; //本社團已登錄的人數
	  	
	  	if (($SETUP['choice_over']==0 and get_club_choice_rank($club_sn,1)>=$CLUB_SET['club_student_num']) or ($club_student_number>=$CLUB_SET['club_student_num'])) {
		   $INFO="社團 【".$CLUB_SET['club_name']."】人數已滿！未能儲存!";
		   $query="delete from stud_club_temp where  year_seme='$c_curr_seme' and student_sn='".$STUDENT_SN."' and choice_rank='$K'";
		   mysql_query($query);
		  } else {
	    $query="select * from stud_club_temp where year_seme='$c_curr_seme' and student_sn='".$STUDENT_SN."' and choice_rank='$K'";
	    $result=mysql_query($query);
	    if (mysql_num_rows($result)) {
	   			$query="update stud_club_temp set club_sn='$club_sn' where year_seme='$c_curr_seme' and student_sn='".$STUDENT_SN."' and choice_rank='$K'";
	    	}else{
	   			$query="insert into stud_club_temp (club_sn,year_seme,student_sn,choice_rank) values ('$club_sn','$c_curr_seme','".$STUDENT_SN."','$K')";
	    } // end if mysql_num_rows
	    
	    if (mysql_query($query)) {
	     $INFO="已於".date("Y-m-d H:i:s")."儲存志願!";	    
	    }else{
	     echo "Error! query=$query";
	     exit();
	    }
	   } // end if $SETUP['choice_over']==0 and get_club_choice_rank($club_sn,1)>=$ ....
	  } // end if $club_sn!=""   
	  }	// end foreach
	
	} // end if ($_POST['mode']=='insert')	
	
	//=================================
	if ($_POST['STUDENT_SN']=='') {
		echo "請選擇學生:";
	} else {
	?>
 				<table border="0" width="100%">
					<?php
					    for ($i=1;$i<=$SETUP['choice_num'];$i++) {
					    	$choice=get_seme_stud_choice_rank($c_curr_seme,$STUDENT_SN,$i); //傳回　club_sn
					    		//取得可選修的社團
								$query="select * from stud_club_base where year_seme='$c_curr_seme' and club_open='1' and (club_class='$CLASS' or club_class='100') order by club_class,club_name"; 
								$res_club=mysql_query($query);
								?>
					    	 <tr>
					    	 	<td align="left">
										第<?php echo $i;?>志願
										<select size="1" name="choice[<?php echo $i;?>]">
											<option value="" style="color:#FF00FF">請選擇...</option>
											<?php
											while ($row=mysql_fetch_array($res_club)) {
												$club_student_num=get_club_student_num($c_curr_seme,$row['club_sn']);
												$club_student_number=$club_student_num[0]; //本社團已登錄的人數
												if (($SETUP['choice_over']==0 and get_club_choice_rank($row['club_sn'],$i)>=$row['club_student_num']) or ($club_student_number>=$row['club_student_num'])) {
													continue;
												}else{
											 ?>
											 <option value="<?php echo $row['club_sn'];?>"<?php if ($row['club_sn']==$choice) echo " selected";?> style="color:#800000"><?php echo $row['club_name'];?></option>
											 <?php
											 } // end if
											}
											?>			
										</select>
										<br><br>
					    	 	</td>
					    	</tr>
					    	<?php
					    }
					?>
					<tr>
						<td><input type="button" value="儲存" onclick="document.myform.mode.value='insert';document.myform.submit()"></td>
					</tr>
					<tr>
						<td style="color:#FF0000;font-size:9pt"><br><br><?php echo $INFO;?></td>
					</tr>
				</table>
	<?php
	} // end if student_sn
} // end if $StartSec<$nowsec and $EndSec>$nowsec
	?>   
   </td>
  </tr>
</table>  


</form>
  
<?php
	//結束主網頁顯示區
	echo "</td>";
	echo "</tr>";
	echo "</table>";
	//程式檔尾
	foot();

?>
