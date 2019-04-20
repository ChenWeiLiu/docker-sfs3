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


//取得目前學年度
$curr_year=curr_year();
$curr_seme=curr_seme();

//目前選定學期
$c_curr_seme=sprintf('%03d%1d',$curr_year,$curr_seme);

//取得學生資料
$STUD=get_student($_SESSION['session_tea_sn'],$c_curr_seme);
$CLASS=substr($STUD['seme_class'],0,1); //年級代碼，用以比對 stud_club_base 的 club_class
$CLASS_name=$school_kind_name[$CLASS]; //中文，如一年，二年...


//取得目前已編班結果
//得到全域變數 $CLASS_choiced, $CLASS_not_choiced, $CLASS_arranged, $CLASS_not_arranged;
$c_curr_class=$CLASS;

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
	//$my_club=get_student_join_club($STUD['student_sn'],$c_curr_seme);
	if ($my_club=get_student_join_club($STUD['student_sn'],$c_curr_seme)) {
		echo "本學期你已參加下列社團，不需參加選課！<br>";
		foreach ($my_club as $My) {
	 		echo "<font color=red>【".$My['club_name']."】</font><br>";
		}
		exit();
	}
} // end if $SETUP['multi_join']

check_arrange();


if ($StartSec<$nowsec and $EndSec>$nowsec) {
	
	//取得可選修的社團
	$query="select * from stud_club_base where year_seme='$c_curr_seme' and club_open='1' and (club_class='$CLASS' or club_class='100')"; 
	$res_club=mysql_query($query);
	$club_num=mysql_num_rows($res_club);
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
	
	//=================================

	if ($_POST['mode']=='insert') {
	  foreach ($_POST['choice'] as $K=>$club_sn) {
	  if ($club_sn!="") {
	  	$CLUB_SET=get_club_base($club_sn);
	  	
	  	$club_student_num=get_club_student_num($c_curr_seme,$row['club_sn']);
			$club_student_number=$club_student_num[0]; //本社團已登錄的人數
	  	
	  	if (($SETUP['choice_over']==0 and get_club_choice_rank($club_sn,1)>=$CLUB_SET['club_student_num']) or ($club_student_number>=$CLUB_SET['club_student_num'])) {
		   $INFO="社團 【".$CLUB_SET['club_name']."】人數已滿！未能儲存!";
		   $query="delete from stud_club_temp where  year_seme='$c_curr_seme' and student_sn='".$STUD['student_sn']."' and choice_rank='$K'";
		   mysql_query($query);
		  } else {
	    $query="select * from stud_club_temp where year_seme='$c_curr_seme' and student_sn='".$STUD['student_sn']."' and choice_rank='$K'";
	    $result=mysql_query($query);
	    if (mysql_num_rows($result)) {
	   			$query="update stud_club_temp set club_sn='$club_sn' where year_seme='$c_curr_seme' and student_sn='".$STUD['student_sn']."' and choice_rank='$K'";
	    	}else{
	   			$query="insert into stud_club_temp (club_sn,year_seme,student_sn,choice_rank) values ('$club_sn','$c_curr_seme','".$STUD['student_sn']."','$K')";
	    } // end if mysql_num_rows
	    
	    if (mysql_query($query)) {
	     $INFO="已於".date("Y-m-d H:i:s")."儲存你的志願!";	    
	    }else{
	     echo "Error! query=$query";
	     exit();
	    }
	   } // end if $SETUP['choice_over']==0 and get_club_choice_rank($club_sn,1)>=$ ....
	  } // end if $club_sn!=""   
	  }	// end foreach
	
	} // end if ($_POST['mode']=='insert')	
	
	//=================================
	
?>	
	<table border="0" width="800">
		 <tr>
		 	<td style="color:#0000FF">※選修社團說明：(學生：<?php echo $STUD['stud_name'];?>)</td>
		</tr>
		<tr>
			<td style="color:#000000">
				1.本學期<font color=red><?php echo $CLASS_name;?>級</font>同學可以選擇的社團共有<?php echo $club_num;?>個，你可以從中選<font color=red>一個</font>社團參加。<br>
				2.選課期限為 <font color=red><?php echo $SETUP['choice_sttime'];?></font> 至 <font color=red><?php echo $SETUP['choice_endtime'];?> </font>止，期限截止前你隨時可修改志願。<br>
				3.選課時你最多可以選擇 <font color=red><?php echo $SETUP['choice_num'];?></font> 個志願，然後選課截止時再依志願順序比序編班。<br>
					(1)先以所有同學的第一志願編班，若某社團的第一志願人數超過限制名額，則依亂數決定可入選的同學，其餘同學彼此再以第二志願編班。<br>
					(2)進行第二志願編班時，只有該社團仍有名額時才會進行第二輪編班，若第二輪仍有落選的同學，則這些同學必須進行第三志願的編班。<br>
					(3)依此類推, 直到所有志願全部編完。<br>
					(4)當所有志願編完，仍然落選的同學，這些同學將由系統依亂數隨機自動編班。<br>
					(5)列出所有社團的志願選擇情況供你參考，慎選你的志願，以免<font color=red><?php echo $SETUP['choice_num'];?></font> 個志願都落選。<br>
					(6)<font color=red>特別提醒：千萬不要所有志願都填同一個社團，否則第一志願沒選到，後面志願將全部落選。</font><br><br>
					
				<font color=blue>※編班流程舉例說明：</font><br>
				<u>杜子</u>喜歡的社團有以下三個，其名額限制分別為：籃球社30人，足球社20人，壘球社30人。<br>
				<u>杜子</u>選了第一志願：籃球社，第二志願足球社，第三志願壘球社。<br><br>
				<font color=blue>※<u>杜子</u>參加編班的流程：</font><br>
					1.系統統計把籃球社當第一志願的總人數有40人，所以依亂數抽籤決定30個入選，其餘10位落選，剛好<u>杜子</u>落選了，<u>杜子</u>只能等第二志願編班。<br>
					2.<u>杜子</u>的第二志願是足球社，當第一志願編完時，足球社尚有5個名額，但把足球社當第二志願的，還有8位在第一志願時沒有選到社團，所以再依亂數抽籤，有3位同學落選，不幸的<u>杜子</u>又落選了，現在<u>杜子</u>只能進行第三志願編班了。<br>
					3.<u>杜子</u>的第三志願是壘球社，壘球社尚有10個名額，選壘球社為第三志願且尚未入選的同學剩5位，這5位全數入選為壘球社。<br>
					4.所以<u>杜子</u>最後是壘球社學員。<br>
			</td>
		</tr>
	</table>
	<table border="1" width="800">
		<tr>
			<!--左欄列出社團目錄被選取情形 -->
			<td valign="top" width="550">
				<font color="#0000FF">※<?php echo $CLASS_name;?>級社團列表與目前選填情形</font>
				<?php
				list_class_club_choice_detail($c_curr_seme,$CLASS,0,1); //列出年級社團選課明細
			  ?>
 				<font color="#0000FF">※跨年級的社團列表與目前選填情形</font>
				<?php
				list_class_club_choice_detail($c_curr_seme,'100',0,1); //列出年級社團選課明細
				?>
			</td>
			<!--右欄列出志願供學生選擇 -->
			<td valign="top" align="left" width="250">
				<font color="#0000FF">※請選擇你的志願：</font><br>
				<form name="myform" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
					<input type="hidden" name="club_menu" value="<?php echo $_POST['club_menu'];?>">
					<input type="hidden" name="mode" value="insert">
				<table border="0" width="100%">
					<?php
					    for ($i=1;$i<=$SETUP['choice_num'];$i++) {
					    	$choice=get_seme_stud_choice_rank($c_curr_seme,$STUD['student_sn'],$i); //傳回　club_sn
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
						<td><input type="submit" value="儲存"></td>
					</tr>
					<tr>
						<td style="color:#FF0000;font-size:9pt"><br><br><?php echo $INFO;?></td>
					</tr>
				</table>
			</form>
			</td>
		</tr>
	</table>
<?php	
} 
?>