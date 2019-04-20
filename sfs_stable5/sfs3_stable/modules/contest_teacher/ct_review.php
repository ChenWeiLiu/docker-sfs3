<?php

// $Id: reward_one.php 6735 2012-04-06 08:14:54Z smallduh $

//取得設定檔
include_once "config.php";


//秀出網頁
head("網路應用競賽 - 作品與成績");

//sfs_check();
?>
<link type="text/css" rel="stylesheet" href="../contest_teacher/include/my.css">
<script type="text/javascript" src="../contest_teacher/include/swfobject.js"></script>
<?php
$tool_bar=&make_menu($school_menu_p);

//列出選單
echo $tool_bar;

//取得目前學年度
$curr_year=curr_year();
$curr_seme=curr_seme();

//目前選定學期
$c_curr_seme=sprintf('%03d%1d',$curr_year,$curr_seme);

//目前日期時間, 用於比對消息有效期限
$Now=date("Y-m-d H:i:s");

//POST 送出後,主程式操作開始 


//界面呈現開始, 全部包在 <form>裡 , act動作 , option1, option2 參數2個
?>
<form method="post" name="myform" action="<?php echo $_SERVER['PHP_SELF'];?>" enctype="multipart/form-data">
 <input type="hidden" name="act" value="<?php echo $_POST['act'];?>">
 <input type="hidden" name="option1" value="<?php echo $_POST['option1'];?>">
 <input type="hidden" name="option2" value="<?php echo $_POST['option2'];?>">
 <input type="hidden" name="RETURN" value="<?php echo $_POST['act'];?>">
<?php
if ($_POST['act']=='') {
	
   $query="select * from contest_setup where endtime<='$Now' and open_review='1' order by endtime desc";
   $result=mysql_query($query);
   if (@mysql_num_rows($result)>0) {
   ?>
  請選擇要瀏覽的作品或成績：
  <select size="1" name="tsn" onchange="this.form.submit()">
	<option value="" style="color:#FF00FF">--請選擇競賽項目--</option>
	<?php
	  while ($TEST=mysql_fetch_array($result,1)) {
	  	?>
	  	<option  style="color:#0000FF" value="<?php echo $TEST['tsn'];?>"<?php if (@$_POST['tsn']==$TEST['tsn']) echo " selected";?>><?php echo $TEST['title'];?>　(類別：<?php echo $PHP_CONTEST[$TEST['active']];?>)</option>
	  	<?php
    }
	?>
	</select>

<?php
 } else {
      echo "<font color=#FF0000>◎抱歉，系統內目前未開放公佈任何競賽成績！</font>";
 }
} // end if act==''
?>
</form>
<br>
<?php
if (@$_POST['tsn']!="") {
   $TEST=get_test_setup($_POST['tsn']);
   title_simple($TEST);
    	//查資料比賽, 僅公告成績
    	if ($TEST['active']==1) {
    		$query="select a.*,b.stud_id,b.stud_name,c.seme_class,c.seme_num from contest_user a,stud_base b,stud_seme c where a.student_sn=b.student_sn and a.student_sn=c.student_sn and c.seme_year_seme='".$TEST['year_seme']."' and a.tsn='".$TEST['tsn']."' and a.prize_id>0 order by prize_id";
    		$result_user=mysql_query($query);
    		if (mysql_num_rows($result_user)) {
    			?>
    			<br>
    			<table border="1" width="700" style="border-collapse: collapse" bordercolor="#C0C0C0" cellpadding="3">
    				<tr bgcolor="#FFFFCC">
    					<td width="50" align="center" style="color:#0000FF">學號</td>
    					<td width="80" align="center" style="color:#0000FF">班級</td>
    					<td width="50" align="center" style="color:#0000FF">座號</td>
    					<td width="80" align="center" style="color:#0000FF">姓名</td>
    					<td width="80" align="center" style="color:#0000FF">作答題數</td>
    					<td width="80" align="center" style="color:#0000FF">答對題數</td>
    					<td width="80" align="center" style="color:#0000FF">名次</td>
    					<td align="center" style="color:#0000FF">備註</td>
    				</tr>
    			<?php
    			$i=0;
    		  while ($Stud=mysql_fetch_array($result_user,1)) {
    		  	$i++;
    		  	//檢查是否有組員
			    	$query="select a.*,b.stud_id,b.stud_name,c.seme_class,c.seme_num from contest_user a,stud_base b,stud_seme c where a.student_sn=b.student_sn and a.student_sn=c.student_sn and c.seme_year_seme='".$TEST['year_seme']."' and  a.tsn='".$TEST['tsn']."' and a.ifgroup='".$Stud['student_sn']."' order by seme_class,seme_num";
    				$GROUPS=mysql_query($query);
    				$Group_num=mysql_num_rows($GROUPS);
    		  	 //學生已評分記錄
    	       list($chk)=mysql_fetch_row(mysql_query("select count(*) from contest_record1 where tsn='".$TEST['tsn']."' and student_sn='".$Stud['student_sn']."'")); //總題數
    	       list($NUM)=mysql_fetch_row(mysql_query("select count(*) from contest_record1 where tsn='".$TEST['tsn']."' and student_sn='".$Stud['student_sn']."' and chk=1")); // 答對

 			    //班級轉中文
    			$class_id=sprintf('%03d_%d_%02d_%02d',substr($TEST['year_seme'],0,3),substr($TEST['year_seme'],3,1),substr($Stud['seme_class'],0,1),substr($Stud['seme_class'],1,2));
  	  		$class_data=class_id_2_old($class_id);
					$Stud['seme_class']=$class_data[5];

						
						?>
   					<tr class="mytr<?php echo $i%2;?>">
   					  <td align="center"><?php echo $Stud['stud_id'];?></td>
      				<td align="center"><?php echo $Stud['seme_class'];?></td>
      				<td align="center"><?php echo $Stud['seme_num'];?></td>
    					<td align="center"><?php echo $Stud['stud_name'];?></td>
    					<td align="center" rowspan="<?php echo $Group_num+1;?>"><?php echo $chk;?></td>
    					<td align="center" rowspan="<?php echo $Group_num+1;?>"><?php echo $NUM;?></td>
    					<td align="center" rowspan="<?php echo $Group_num+1;?>"><?php echo $Stud['prize_text'];?></td>
    					<td style="font-size:10pt" rowspan="<?php echo $Group_num+1;?>"><?php echo $Stud['prize_memo'];?></td>
    				</tr>
    		  <?php
    		    if ($Group_num>0) {
    		     while ($row=mysql_fetch_array($GROUPS,1)) {
		 			    //班級轉中文
    					$class_id=sprintf('%03d_%d_%02d_%02d',substr($TEST['year_seme'],0,3),substr($TEST['year_seme'],3,1),substr($row['seme_class'],0,1),substr($row['seme_class'],1,2));
  	  				$class_data=class_id_2_old($class_id);
							$row['seme_class']=$class_data[5];
    		     ?>
   					<tr class="mytr<?php echo $i%2;?>">
   					  <td align="center"><?php echo $row['stud_id'];?></td>
      				<td align="center"><?php echo $row['seme_class'];?></td>
      				<td align="center"><?php echo $row['seme_num'];?></td>
    					<td align="center"><?php echo $row['stud_name'];?></td>
    				</tr>
    		     <?php
    		     } // end while
    		    } //end if Group_num>0
    		  
    		  } // end while
    		  ?>
    		 </table>
    		 <table border="0" width="100%">
    		   <tr>
    		   	 <td style="color:#FF0000">※如需複查成績，請洽競賽辦理單位。</td>
    		   </tr>
    		 </table>
    		  <?php
    		} // if mysql_num_rows
    	
    	//其他，一併公告作品 
    	} else {   //else if active==1
    		$query="select a.*,b.stud_id,b.stud_name,c.seme_class,c.seme_num from contest_user a,stud_base b,stud_seme c where a.student_sn=b.student_sn and a.student_sn=c.student_sn and c.seme_year_seme='".$TEST['year_seme']."' and a.tsn='".$TEST['tsn']."' and a.prize_id>0 order by prize_id";
    		$result_user=mysql_query($query);
    		if (mysql_num_rows($result_user)) {
    			?>
    			<br>
    			<table border="1" width="100%" style="border-collapse: collapse" bordercolor="#C0C0C0" cellpadding="3">
    				<tr bgcolor="#FFFFCC">
    					<td width="50" align="center" style="color:#0000FF">學號</td>
    					<td width="80" align="center" style="color:#0000FF">班級</td>
    					<td width="50" align="center" style="color:#0000FF">座號</td>
    					<td width="80" align="center" style="color:#0000FF">姓名</td>
    					<td width="256" align="center" style="color:#0000FF">競賽成績或作品</td>
    					<td width="80" align="center" style="color:#0000FF">得獎名目</td>
    					<td align="center" style="color:#0000FF">評審評語或其他附註事項</td>
    				</tr>
    			<?php
    			$i=0;
    		  while ($Stud=mysql_fetch_array($result_user,1)) {
    		  	$i++;
    		  	//檢查是否有組員
			    	$query="select a.*,b.stud_id,b.stud_name,c.seme_class,c.seme_num from contest_user a,stud_base b,stud_seme c where a.student_sn=b.student_sn and a.student_sn=c.student_sn and c.seme_year_seme='".$TEST['year_seme']."' and a.tsn='".$TEST['tsn']."' and a.ifgroup='".$Stud['student_sn']."' order by seme_class,seme_num";
    				$GROUPS=mysql_query($query);
    				$Group_num=mysql_num_rows($GROUPS);
   		  	 //學生作品記錄
    	       $query="select * from contest_record2 where tsn='".$TEST['tsn']."' and student_sn='".$Stud['student_sn']."'";
    	       $WORKS=mysql_fetch_array(mysql_query($query),1);
    	       $WORKS['prize_memo']=get_prize_memo($TEST['tsn'],$Stud['student_sn']);
 			    //班級轉中文
	    				$class_id=sprintf('%03d_%d_%02d_%02d',substr($TEST['year_seme'],0,3),substr($TEST['year_seme'],3,1),substr($Stud['seme_class'],0,1),substr($Stud['seme_class'],1,2));
  		  			$class_data=class_id_2_old($class_id);
							$Stud['seme_class']=$class_data[5];
						?>
   					<tr class="mytr<?php echo $i%2;?>">
   					  <td align="center"><?php echo $Stud['stud_id'];?></td>
      				<td align="center"><?php echo $Stud['seme_class'];?></td>
      				<td align="center"><?php echo $Stud['seme_num'];?></td>
    					<td align="center"><?php echo $Stud['stud_name'];?></td>
    					<td align="center" rowspan="<?php echo $Group_num+1;?>">
    					 <?php
						 if ($TEST['active']<5 or $TEST['active']>6) {
							 $a = explode(".", $WORKS['filename']);
							 $filename_s = $a[0] . "_s." . $a[1];
							 switch ($TEST['active']) {
							 case '2':
								 ?>
								 <img src="<?php echo $UPLOAD_U[2] . $filename_s; ?>" border="0"><br>
								 <a href="<?php echo $UPLOAD_U[$TEST['active']].$WORKS['filename'];?>" target='_blank'>觀看原圖</a>
								 <?php
								 break;
							 case '3':
							 ?>
								<embed src="<?php echo $UPLOAD_U[3] . $WORKS['filename']; ?>" width=240 height=180
									   type=application/x-shockwave-flash Wmode="transparent"><br>
									<a href="<?php echo $UPLOAD_U[$TEST['active']].$WORKS['filename'];?>" target='_blank'>觀看原圖</a>
							<?php
							break;
							case '4':
								?>
								<a href="<?php echo $UPLOAD_U[$TEST['active']].$WORKS['filename'];?>" target='_blank'>觀看作品</a>
								<?php
								break;
							case '7':
							?>
									<input type="button" value="觀看作品" onclick="show_scratch('<?php echo $WORKS['filename'];?>')">
								<?php
								break;


							default:
							} // end switch
						 ?>

						<?php
						 } else {
							$REC=get_stud_record_type($TEST,$Stud['student_sn']);

							echo $REC['speed']." 字/分 ( 正確率 ".$REC['correct']." % )";

						}
   						?>
    					

    					
    					</td>
    					<td align="center" rowspan="<?php echo $Group_num+1;?>"><?php echo $Stud['prize_text'];?></td>
    					<td style="font-size:10pt" rowspan="<?php echo $Group_num+1;?>"><?php echo $WORKS['prize_memo'];?></td>
    				</tr>
    				<?php
    		    if ($Group_num>0) {
    		     while ($row=mysql_fetch_array($GROUPS,1)) {
		 			    //班級轉中文
	    				$class_id=sprintf('%03d_%d_%02d_%02d',substr($TEST['year_seme'],0,3),substr($TEST['year_seme'],3,1),substr($row['seme_class'],0,1),substr($row['seme_class'],1,2));
  		  			$class_data=class_id_2_old($class_id);
							$row['seme_class']=$class_data[5];
    		     ?>
   					<tr class="mytr<?php echo $i%2;?>">
   					  <td align="center"><?php echo $row['stud_id'];?></td>
      				<td align="center"><?php echo $row['seme_class'];?></td>
      				<td align="center"><?php echo $row['seme_num'];?></td>
    					<td align="center"><?php echo $row['stud_name'];?></td>
    				</tr>
    		     <?php
    		     } // end while
    		    } //end if Group_num>0

    		  } // end while
    		  ?>
    		 </table>
    		 <table border="0" width="100%">
    		   <tr>
    		   	 <td style="color:#FF0000">※如需複查成績，請洽競賽辦理單位。</td>
    		   </tr>
    		 </table>
    		  <?php
    		} // if mysql_num_rows
    		
    		
    		
    	} // end if active 
 
} // end if $_POST['tsn']!=''
?>
<div id="flashContent" >

</div>
<Script>
	var params = {
		bgcolor: "#FFFFFF",
		allowScriptAccess: "always",
		allowFullScreen: "true",
		wmode: "window",
		menu:"false"

	};
	function show_scratch(filename) {
		var P='<?php echo $UPLOAD_U[7];?>';
		var scratch_file=P+filename;
		var flashvars = {
			project: scratch_file ,
			autostart: "false"
		};
		var attributes = {};

		swfobject.embedSWF("../contest_teacher/include/Scratch.swf", "flashContent", "482", "387", "10.2.0","../contest_teacher/include/expressInstall.swf", flashvars, params, attributes);

	}
</Script>