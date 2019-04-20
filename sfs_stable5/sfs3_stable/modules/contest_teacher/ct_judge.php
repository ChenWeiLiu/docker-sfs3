<?php

// $Id: reward_one.php 6735 2012-04-06 08:14:54Z smallduh $

//取得設定檔
include_once "config.php";

sfs_check();

//秀出網頁
head("網路應用競賽 - 評審工作");

?>
<link type="text/css" rel="stylesheet" href="./include/my.css">
<script type="text/javascript" src="./include/tr_functions.js"></script>
<script type="text/javascript" src="./include/swfobject.js"></script>
<?php
$tool_bar=&make_menu($school_menu_p);

//列出選單
echo $tool_bar;

//取得目前學年度
$curr_year=curr_year();
$curr_seme=curr_seme();

//目前選定學期
$c_curr_seme=sprintf('%03d%1d',$curr_year,$curr_seme);

//目前日期時間
$Now=date("Y-m-d H:i:s");

//POST 送出後,主程式操作開始 
//登入, 確認評分的競賽
if ($_POST['act']=='login') {
 $query="select b.* from contest_judge_user a,contest_setup b where a.tsn=b.tsn and a.teacher_sn='".$_SESSION['session_tea_sn']."' and a.tsn='".$_POST['tsn']."' and b.open_judge=1";
 $result=mysql_query($query);
 $Teacher=get_judge_user($_POST['tsn'],$_SESSION['session_tea_sn']);
 if ($row=mysql_fetch_array($result,1)) {
 	//登入成功
  $_POST['act']="Start";
  $_POST['option1']=$_POST['tsn'];  //競賽的 tsn
  //回寫登入記錄
  $L=$Teacher['logintimes']+1;
  $query="update contest_judge_user set lastlogin='".date("Y-m-d H:i:s")."',logintimes='$L' where tsn='".$_POST['tsn']."' and teacher_sn='".$_SESSION['session_tea_sn']."'"; 
  mysql_query($query);
  } else {
    $INFO="您並未獲邀評分本次的競賽, 請重新選擇!";
    $_POST['act']="";
  } 
  
}// end if act=login

//寫入某生查資料成績
if (@$_POST['act']=='score_search_write') {
  $query="update contest_record1 set chk=0,teacher_sn='".$_SESSION['session_tea_sn']."' where tsn='".$_POST['option1']."' and student_sn='".$_POST['option2']."'";
  mysql_query($query);
  if (count(@$_POST['chk'])>0) {
     foreach ($_POST['chk'] as $ibsn=>$val) {
      $query="update contest_record1 set chk=$val where tsn='".$_POST['option1']."' and student_sn='".$_POST['option2']."' and ibsn='$ibsn'";
      mysql_query($query);
     }// end foreach
	 }
	 $INFO="已於".date("Y-m-d H:i:s")."進行儲存";
	 //畫面切換, 是否自動開啟下一位
	 if ($_POST['goto_next']) {
	 	 if ($_POST['n_student_sn']!="") {
	 	 	$i=$_POST['n_student_sn'];
	 	 	 //下一位學生
			 $sql="select a.*,b.stud_id,b.stud_name,c.seme_class,c.seme_num from contest_user a,stud_base b,stud_seme c,contest_setup d where a.tsn=d.tsn and c.seme_year_seme=d.year_seme and a.tsn='".$_POST['option1']."' and a.ifgroup='' and a.student_sn=b.student_sn and b.student_sn=c.student_sn order by seme_class,seme_num limit $i,1";
   	   $res_next=$CONN->Execute($sql);
   	   if ($res_next->RecordCount()>0) {
   	     $next_stud=$res_next->fields['student_sn'];
	    		$_POST['option2']=$next_stud;
	    		$_POST['act']='score_search';
			 } else {
   	     $next_stud="";
   	     $_POST['act']='score';
   	   }
	   } else { 
	    $_POST['act']='score';
	   }
	 } else {
	  $_POST['act']='score_search';
	 }
}// end if 寫入某生查資料比賽

//寫入上傳作品類競賽成績 (靜畫,動畫,簡報)
if ($_POST['act']=='score_upload') {
	$if_write=0;
		foreach ($_POST['score'] as $k=>$score) {
			if ($score>0) {
				$if_write=1;
		    if (mysql_num_rows(mysql_query("select score from contest_score_record2 where tsn='".$_POST['option1']."' and student_sn='$k' and teacher_sn='".$_SESSION['session_tea_sn']."'"))>0) {
		      $query="update contest_score_record2 set score='$score',prize_memo='".$_POST['prize_memo'][$k]."' where tsn='".$_POST['option1']."' and student_sn='$k' and teacher_sn='".$_SESSION['session_tea_sn']."'";
		    }else{
			   	$query="insert into contest_score_record2 (tsn,student_sn,teacher_sn,score,prize_memo) values ('".$_POST['option1']."','$k','".$_SESSION['session_tea_sn']."','".$score."','".$_POST['prize_memo'][$k]."')";
			  }
			  if (!mysql_query($query)) {
			   echo "Error! query=$query";
			   exit();
			  }
			//依$k 寫入細項成績
			$K='s'.$k;
			 if (count($_POST[$K])>0) {
			  foreach ($_POST[$K] as $sco_sn=>$sco_num) {
			    if (mysql_num_rows(mysql_query("select sco_num from contest_score_user where sco_sn='$sco_sn' and student_sn='$k' and teacher_sn='".$_SESSION['session_tea_sn']."'"))>0) {
			      $query="update contest_score_user set sco_num='$sco_num' where sco_sn='$sco_sn' and student_sn='$k' and teacher_sn='".$_SESSION['session_tea_sn']."'";
			     }else{
			     	$query="insert into contest_score_user (student_sn,teacher_sn,sco_sn,sco_num) values ('$k','".$_SESSION['session_tea_sn']."','$sco_sn','$sco_num')";
			  	}
			  	mysql_query($query);
			  } // end foreach count($_POST[$K])
			 }// end if count($_POST[$k])>0
		  }//end if ($score!="")
		} // end foreach
		
		$INFO=($if_write==1)?"　已於".date("Y-m-d H:i:s")."寫入選手成績!":"";  

  $_POST['act']='score'; //回到評分狀態
  
}

//寫入得獎設定
if ($_POST['act']=='prize_write') {
		//由於陣列 post , 無資料不會發送, 先清除所有記錄, 以免原本有資料，但後清除資料者不會更動
		$query="update contest_user set prize_id=null,prize_text=null where tsn='".$_POST['option1']."'";
		mysql_query($query);
		$if_write=0;
		foreach ($_POST['prize_id'] as $k=>$prize_id) {
			if ($prize_id>0) {
				$if_write=1;
				$prize_text=$_POST['prize_text'][$k];
				$prize_memo=$_POST['prize_memo'][$k];
			  $query="update contest_user set prize_id='$prize_id',prize_text='$prize_text' where tsn='".$_POST['option1']."' and student_sn='".$k."'";
			  //echo $query."<br>";
			  mysql_query($query);
			
		  }
		} // end foreach
		
		$INFO=($if_write==1)?"　已於".date("Y-m-d H:i:s")."寫入得獎記錄!":"";
		$_POST['act']='prize';
} // end if act='prize_write'


//界面呈現開始, 全部包在 <form>裡 , act動作 , option1, option2 參數2個
?>
<form method="post" name="myform" action="<?php echo $_SERVER['PHP_SELF'];?>" enctype="multipart/form-data">
 <input type="hidden" name="act" value="<?php echo $_POST['act'];?>">
 <input type="hidden" name="option1" value="<?php echo $_POST['option1'];?>">
 <input type="hidden" name="option2" value="<?php echo $_POST['option2'];?>">
 <input type="hidden" name="RETURN" value="<?php echo $_POST['act'];?>">

<?php
//無任何參數, 檢查是否有指定評審工作
if ($_POST['act']=='') {

 judge_login();

}

//主畫面
if ($_POST['act']=='Start') {
	$query="select * from contest_setup where tsn='".$_POST['option1']."'";
	$TEST=$CONN->Execute($query)->fetchRow();
?>
  <table border="0" width="100%">
  	<tr>
  		<td style="color:#0000FF">．競賽評分工作</tr>
  </table>
  <?php
  test_main($_POST['option1'],1); //第二個參數設為1, 直接顯示題目, 設為0不顯示題目(用於比賽前公告)
  ?>
    <table border="0" width="100%">
  	<tr>
  		<td>
			<?php
			//如果不是中, 英打, 需要人工評分
			if ($TEST['active']<5 or $TEST['active']>6) {
				?>
					<input type="button" value="進行評分" onclick="document.myform.act.value='score';document.myform.submit();">
				<?php
			}
			?>
  			<input type="button" value="指定得獎" onclick="document.myform.act.value='prize';document.myform.submit();">
  			<input type="button" style="color:#FF00FF" value="登出" onclick="document.myform.act.value='';document.myform.submit();">
  		</td>
  	</tr>
  </table>

<?php
} // end if act='Start'

//開始評分
if ($_POST['act']=='score') {
	?>
	<input type="hidden" name="n_student_sn" value="">
  <table border="0" width="100%">
  	<tr>
  		<td style="color:#0000FF">．競賽評分工作 - 進行評分</td>
	</tr>
  </table>
  <?php
 $TEST=get_test_setup($_POST['option1']);
 title_simple($TEST);
  $query_stud="select a.*,b.stud_id,b.stud_name,c.seme_class,c.seme_num from contest_user a,stud_base b,stud_seme c,contest_setup d where a.tsn=d.tsn and c.seme_year_seme=d.year_seme and a.tsn='".$TEST['tsn']."' and a.ifgroup='' and a.student_sn=b.student_sn and b.student_sn=c.student_sn order by seme_class,seme_num";
  $result_stud=mysql_query($query_stud);
  $all_students=mysql_num_rows($result_stud); //全部的學生
  //查資料比賽
  if ($TEST['active']==1) {
  	?>
   <br>
   <table border="1" width="840" style="border-collapse: collapse" bordercolor="#C0C0C0" cellpadding="5" cellspacing="0">
   	<tr bgcolor="#FFFFCC">
   		<td style="font-size:10pt;color:#800000" width="50" align="center">管理</td>
   		<td style="font-size:10pt;color:#800000" width="40" align="center">序號</td>
   		<td style="font-size:10pt;color:#800000" width="80" align="center">學號</td>
   		<td style="font-size:10pt;color:#800000" width="60" align="center">姓名</td>
   		<td style="font-size:10pt;color:#800000" width="80" align="center">班級座號</td>
   		<td style="font-size:10pt;color:#800000" width="80" align="center">登入次數</td>
   		<td style="font-size:10pt;color:#800000" width="130" align="center">最後登入</td>
   		<td style="font-size:10pt;color:#800000" width="100" align="center">競賽記錄</td>
   		<td style="font-size:10pt;color:#800000" width="150" align="center">評分成績</td>
   		<td style="font-size:8pt;color:#800000" width="60" align="center">評分老師</td>
   	</tr>	

  	<?php
  //查資料比賽
   $i=0;
   while ($Stud=mysql_fetch_array($result_stud,1)) {
 			$i++;	
    	 //學生已查資料筆數
    	 $query="select count(*) as num from contest_record1 where tsn='".$TEST['tsn']."' and student_sn='".$Stud['student_sn']."'";
    	 list($N)=mysql_fetch_row(mysql_query($query));
    	 $RR="已作答 ".$N." 題";
    	 $Fcolor=($N>0)?"#0000FF":"#C0C0C0";
     	 //學生已評分記錄
     	 $chk_right=mysql_num_rows(mysql_query("select * from contest_record1 where tsn='".$TEST['tsn']."' and student_sn='".$Stud['student_sn']."' and chk=1"));
     	 $chk_none=mysql_num_rows(mysql_query("select * from contest_record1 where tsn='".$TEST['tsn']."' and student_sn='".$Stud['student_sn']."' and chk=0"));
     	 $chk_wrong=mysql_num_rows(mysql_query("select * from contest_record1 where tsn='".$TEST['tsn']."' and student_sn='".$Stud['student_sn']."' and chk=-1"));
				
   	   
    	 if ($chk_none==$N) {
    	 	$CC="尚未評分";
    	 	$teacher_sn="";
    	  }else{
    	  $CC="<font color=#FF0000>答對 ".$chk_right." 題，答錯 ".$chk_wrong." 題</font>";
    	  //取得評分老師
    	  $query="select distinct teacher_sn from contest_record1 where  tsn='".$TEST['tsn']."' and student_sn='".$Stud['student_sn']."'";	
    	  list($teacher_sn)=mysql_fetch_row(mysql_query($query));
    	 }
 //       	<tr bgcolor="#FFFFFF" onmouseover="setPointer(this, 'over', '#FFFFFF', '#CCFFCC', '#FFCC99')" onmouseout="setPointer(this, 'out', '#FFFFFF', '#CCFFCC', '#FFCC99')" onmousedown="setPointer(this, 'click', '#FFFFFF', '#CCFFCC', '#FFCC99')" >
    	 
   ?>	
   	<tr class="mytr<?php echo $i%2;?>">
   		<td style="font-size:10pt;color:<?php echo $Fcolor;?>" align="center"><?php if ($N>0) { ?><input type="button" value="評分" style="cursor:hand;color:#FF0000;font-size:10pt" onclick="document.myform.n_student_sn.value='<?php echo $i-1;?>';document.myform.option2.value='<?php echo $Stud['student_sn'];?>';document.myform.act.value='score_search';document.myform.submit();"><?php } ?></td>
   		<td style="font-size:10pt;color:#800000" align="center"><?php echo $i;?></td>	
   		<td style="font-size:10pt;color:<?php echo $Fcolor;?>" align="center"><?php echo $Stud['stud_id'];?></td>
   		<td style="font-size:10pt;color:<?php echo $Fcolor;?>" align="center"><?php echo $Stud['stud_name'];?></td>
   		<td style="font-size:10pt;color:<?php echo $Fcolor;?>" align="center"><?php echo $Stud['seme_class'].sprintf('%02d',$Stud['seme_num']);?></td>
   		<td style="font-size:10pt;color:<?php echo $Fcolor;?>" align="center"><?php echo $Stud['logintimes'];?></td>
   		<td style="font-size:10pt;color:<?php echo $Fcolor;?>" align="center"><?php echo $Stud['lastlogin'];?></td>
   		<td style="font-size:10pt;color:<?php echo $Fcolor;?>" align="center"><?php echo $RR;?></td>
   		<td style="font-size:10pt;color:<?php echo $Fcolor;?>" align="center"><?php echo $CC;?></td>
   		<td style="font-size:10pt;color:<?php echo $Fcolor;?>" align="center"><?php echo get_teacher_name($teacher_sn);?></td>
   	</tr>	
   <?php	
   } // end while
   ?>
   </table>
    <table border="0" width="100%">
  		<tr>
  			<td><input type="button" value="回上一頁" onclick="document.myform.act.value='Start';document.myform.submit();"></td>
  		</tr>
  	</table>

   <?php
  }else{
  //其他繪圖比賽
  //取得每個評分細目欄位代碼
     $SCORE_SET_NUM=0;
     $query="select * from contest_score_setup where tsn='".$_POST['option1']."'";
     $result=mysql_query($query);
     if (mysql_num_rows($result)) {
      while ($row=mysql_fetch_array($result,1)) {
       $SCORE_SET_NUM++;
       $SCORE_FIELD[$SCORE_SET_NUM]=$row['sco_sn'];
      }	
     }
    if ($TEST['active']==2 or $TEST['active']==3) {
    ?>
	<div >
     <input type="checkbox" name="show_small_pic" value="1" onclick="document.myform.act.value='score_upload';document.myform.submit()" <?php if ($_POST['show_small_pic']) echo "checked";?>><font color="#FF0000">一併呈現作品縮圖</font>
     <input type="checkbox" name="hide_no_post" value="1" onclick="document.myform.act.value='score_upload';document.myform.submit()" <?php if ($_POST['hide_no_post']) echo "checked";?>><font color="#FF0000">隱藏未上傳名單</font>
	</div>
	<?php
    } else {
     echo "<br>";
    }
  ?>
   <div style="float:left;width:50%;border:0px solid #F00">

   <table border="1" style="border-collapse: collapse" bordercolor="#C0C0C0" cellpadding="1">
  <?php
   
  //上傳作品類
  $t=0; //人數計數
   while ($Stud=mysql_fetch_array($result_stud,1)) {
    	 //查詢是否上傳
    	 $query="select * from contest_record2 where tsn='".$TEST['tsn']."' and student_sn='".$Stud['student_sn']."'";
    	 $result_file=mysql_query($query);
    	 $N=mysql_num_rows($result_file);
    	 $Fcolor=($N>0)?"#0000FF":"#C0C0C0";
    	 if ($N>0) {
      	 $WORKS=mysql_fetch_array($result_file,1);
    	 }else{
    	 	if ($_POST['hide_no_post']) continue;
    	 	$CC="未上傳";
    	 }
		
   	$t++;
   	//表格標題
   	if ($t%10==1) table_title_1($TEST['tsn']);

    ?>
   	<tr class="mytr<?php echo $t%2;?>">
   		<td style="font-size:10pt;color:#800000" width="50" align="center"><?php echo $t;?></td>
		<?php
		/**
		 2017.02.28 取消作者資訊顯示, 改成右側可直接觀看作品
		<td style="font-size:10pt;color:<?php echo $Fcolor;?>" align="center"><?php echo $Stud['stud_id'];?></td>
		<td style="font-size:10pt;color:<?php echo $Fcolor;?>" align="center"><?php echo $Stud['stud_name'];?></td>
		<td style="font-size:10pt;color:<?php echo $Fcolor;?>" align="center"><?php echo $Stud['seme_class'].sprintf('%02d',$Stud['seme_num']);?></td>
		 */
		?>
   		<td style="font-size:10pt;color:<?php echo $Fcolor;?>" align="center">
   			<?php 
   			if ($N>0) { 
   			 if ($_POST['show_small_pic']) {
				switch ($TEST['active']) {
				  case '2':
				  $a=explode(".",$WORKS['filename']);
				  $filename_s=$a[0]."_s.".$a[1];
							?>
					<img src="<?php echo $UPLOAD_U[2].$filename_s; ?>" border="0"><br>
				  <?php
				  break;
				  case '3':
				   ?>
							<embed src="<?php echo $UPLOAD_U[3].$WORKS['filename'];?>" width=240 height=180 type=application/x-shockwave-flash Wmode="transparent"><br>
				   <?php
				  break;
				  default:
				} // end switch
   			 } // end if show_small_pic
			//有上傳, 若是 scratch 不要直接開
				if ($TEST['active']==7) {
					?>
					<img src="./images/view.png" height="18" style="cursor: pointer" onclick="show_scratch('<?php echo $WORKS['filename'];?>')">
					<?php
				} else {
					?>
					<img src="./images/view.png" height="18" style="cursor: pointer" onclick="show_pic('<?php echo $WORKS['filename'];?>')">
					<a href="<?php echo $UPLOAD_U[$TEST['active']].$WORKS['filename'];?>" target="_blank"><img src="./images/download.png" height="18"></a>
					<?php
				}

			} else {
			?>
   			   未上傳 
   		  <?php
			}
			?>
   		</td>
   			<?php
   			for ($i=1;$i<=$SCORE_SET_NUM;$i++) {
   				$thisScore=0;
   				$query="select sco_num from contest_score_user where sco_sn='".$SCORE_FIELD[$i]."' and student_sn='".$Stud['student_sn']."' and teacher_sn='".$_SESSION['session_tea_sn']."'";
   				if ($row=mysql_fetch_row(mysql_query($query))) list($thisScore)=$row;
   				?>
   				 <td style="font-size:10pt;color:<?php echo $Fcolor;?>" align="center">
   				<?php
   			  //檢查有沒有上傳, 若沒有, 則不顯示表單	
   				 if ($N>0) {
   			?>
   			<input type="text" name="s<?php echo $Stud['student_sn'];?>[<?php echo $SCORE_FIELD[$i];?>]" size="3" value="<?php echo $thisScore;?>"  onBlur="score_sum('<?php echo $Stud['student_sn'];?>');">
    		<?php
    		   }else{
         ?>
   			<input type="hidden" name="giveup[<?php echo $Stud['student_sn'].$i;?>]">-
        <?php 
     		   } // end if ($N>0)
   		   ?>
   		    </td>
   		   <?php
   	   } // end for 列出細項評分
   	   //總分
   	   $query="select score,prize_memo from contest_score_record2 where tsn='".$TEST['tsn']."' and student_sn='".$Stud['student_sn']."' and teacher_sn='".$_SESSION['session_tea_sn']."'";
   	    list($score,$prize_memo)=mysql_fetch_row(mysql_query($query));
				if ($N>0) { 
   		?>
   		<td style="font-size:10pt;color:<?php echo $Fcolor;?>" align="center">
   			<input type="text" name="score[<?php echo $Stud['student_sn'];?>]" value='<?php echo sprintf('%d',$score);?>' size="5" <?php if ($SCORE_SET_NUM>0) { echo "onBlur=\"score_sum('".$Stud['student_sn']."')\""; } ?>>
			</td>
   		<td style="font-size:10pt" align="center"><input type="text" name="prize_memo[<?php echo $Stud['student_sn'];?>]" value="<?php echo $prize_memo;?>" size="20"></td>
   			<?php
   			   } else { 
   			?> 
   			<td style="font-size:10pt;color:<?php echo $Fcolor;?>">-</td>
				<td style="font-size:10pt;color:<?php echo $Fcolor;?>" align="center">棄權<input type="hidden" name="giveup[<?php echo $Stud['student_sn'];?>]">&nbsp</td>			
   			<?php 
   			} // end if $N>0 有上傳作品
   			?>
   	</tr>	
  <?php 
   } // end while  
   ?>
  </table>
   <table border="0" width="100%">
  		<tr>
  			<td>
  			 <input type="button" value="送出評分" onclick="document.myform.act.value='score_upload';document.myform.submit();" style="color:#FF0000">
         <input type="button" value="回上一頁" style="color:#FF00FF" onclick="document.myform.act.value='Start';document.myform.submit();">
  		   <?php echo $INFO;?>  			
  			</td>
  		</tr>
  	</table>
   </div>
	  <!-- 右側第二個 div -->
	  <div style="float:left;width:50%;border:0px solid #000">
		  <div id="flashContent" >

		  </div>
	  </div>
   <?php
  } // end if $TEST['active']==1 else

} // end if act=score

//評分某學生的查資料比賽, 競賽: $_POST['option1'] , 學生: $_POST['option2']
if ($_POST['act']=='score_search') {

 $i=$_POST['n_student_sn'];
//呈現競賽資料
 $TEST=get_test_setup($_POST['option1']);
 title_simple($TEST);
 $STUD=get_student($_POST['option2']);  //學生資料

?>
  <input type="hidden" name="n_student_sn" value="">
  <input type="hidden" name="goto_next" value="">
  <table border="0" width="100%">
  	<tr>
  		<td style="colorL#FFCC66">競賽學生：<?php echo $STUD['class_name']." ".$STUD['seme_num']."號 ".$STUD['stud_name'];?> 的作答如下...</td>
  	</tr>
  </table>
  <table border="1" width="100%" style="border-collapse: collapse" bordercolor="#C0C0C0" cellpadding="2">
	   <tr>
      	<td bgcolor="#FFCCCC" align="center" style="font-size:10pt" width="30">題號</td>
        <td bgcolor="#FFCCCC" align="center" style="font-size:10pt">題目</td>
        <td bgcolor="#FFCCCC" align="center" style="font-size:10pt" width="160">參考答案</td>
        <td bgcolor="#FFCCCC" align="center" style="font-size:10pt" width="160">學生作答</td>
        <td bgcolor="#FFCCCC" align="center" style="font-size:8pt" width="50">超連結</td>
        <td bgcolor="#FFCCCC" align="center" style="font-size:8pt" width="80">作答時間</td>
        <td bgcolor="#FFCCCC" align="center" style="font-size:10pt" width="80">答對?</td>
      </tr>
      <?php
      $I=0;
      $query="SELECT a.* , b.question,b.ans,b.ans_url FROM contest_record1 AS a, contest_ibgroup AS b WHERE a.tsn=b.tsn and a.tsn='".$_POST['option1']."' and a.ibsn=b.ibsn and a.student_sn='".$_POST['option2']."' order by b.tsort";
      $result=mysql_query($query);
      while ($ITEM=mysql_fetch_array($result,1)) {
      	$I++;
      	if ($ITEM['ans']=='' or $ITEM['lurl']=='') $ITEM['chk']=-1;
      	?>
      <tr>
      	<td style="font-size:10pt" width="30" align="center" ><?php echo $I;?></td>
        <td style="font-size:10pt"><?php echo $ITEM['question'];?></td>
        <td style="font-size:10pt" width="180"><?php echo $ITEM['ans'];?></td>
        <td style="font-size:10pt" width="180"><?php echo $ITEM['myans'];?></td>
        <td style="font-size:10pt" width="50" align="center" ><?php if ($ITEM['lurl']!="") { ?><a href="<?php echo $ITEM['lurl'];?>" target="_blank">網頁</a><?php } ?></td>
        <td style="font-size:9pt" width="80" align="center" ><?php echo $ITEM['anstime']; ?></td>
        <td style="font-size:10pt" width="80">
        	<input type="radio" name="chk[<?php echo $ITEM['ibsn'];?>]" value="1" <?php if ($ITEM['chk']==1) { echo "checked"; } ?>>對
        	<input type="radio" name="chk[<?php echo $ITEM['ibsn'];?>]" value="-1" <?php if ($ITEM['chk']==-1) { echo " checked"; } ?>>錯
        </td>
      </tr>
      <?php 
      }
      ?>
		</table>
  <table border="0" width="100%">
  		<tr>
  			<td style="color:#FF0000">
  				<input type="button" style="color:#FF0000" value="暫存評分結果" onclick="document.myform.n_student_sn.value='<?php echo $i;?>';document.myform.act.value='score_search_write';document.myform.submit()">
  				<input type="button" style="color:#FF0000" value="儲存評分並跳下一位" onclick="document.myform.n_student_sn.value='<?php echo $i+1;?>';document.myform.goto_next.value='1';document.myform.act.value='score_search_write';document.myform.submit()">
  				<input type="button" style="color:#FF00FF" value="返回競賽學生列表" onclick="document.myform.act.value='score';document.myform.submit();">
  				<?php echo $INFO;?>
  				</td>
  		</tr>
  </table>
  <?php
} // end if act=score_search_write



//指定得獎
if ($_POST['act']=='prize') {
	?>
		<div>

  <table border="0" width="100%">
  	<tr>
  		<td style="color:#0000FF">．競賽評分工作 - 指定得獎學生</td>
	</tr>
  </table>

  <?php
//呈現競賽資料
 $TEST=get_test_setup($_POST['option1']);
 title_simple($TEST);

    if ($TEST['active']==2 or $TEST['active']==3) {
    ?>
     <input type="checkbox" name="show_small_pic" value="1" onclick="document.myform.act.value='prize_write';document.myform.submit()" <?php if ($_POST['show_small_pic']) echo "checked";?>><font color="#FF0000">一併呈現作品縮圖</font>
     <input type="checkbox" name="hide_no_post" value="1" onclick="document.myform.act.value='score_upload';document.myform.submit()" <?php if ($_POST['hide_no_post']) echo "checked";?>><font color="#FF0000">隱藏未上傳名單</font>
    <?php
    } else {
     echo "<br>";
    }

?>
  <table border="1" style="border-collapse: collapse" bordercolor="#C0C0C0" cellpadding="1">
<?php	
  //取出學生
  $query="select a.*,b.stud_id,b.stud_name,c.seme_class,c.seme_num from contest_user a,stud_base b,stud_seme c,contest_setup d where a.tsn=d.tsn and c.seme_year_seme=d.year_seme and a.tsn='".$TEST['tsn']."' and a.ifgroup='' and a.student_sn=b.student_sn and b.student_sn=c.student_sn order by seme_class,seme_num";
  $result=mysql_query($query);
  $t=0;
   while ($Stud=mysql_fetch_array($result,1)) {
    	//依active 分開處理
    	//查資料類=============================================
    	if ($TEST['active']==1) {
    		
    	 //學生已評分記錄
    	 $REC=get_stud_record1_info($TEST,$Stud['student_sn']);
    	 /***
    	 $query="select count(*) as num from contest_record1 where tsn='".$TEST['tsn']."' and student_sn='".$Stud['student_sn']."'";
    	 list($N)=mysql_fetch_row(mysql_query($query));
    	 $Fcolor=($N>0)?"#0000FF":"#C0C0C0";
     	 //學生已評分記錄
     	 $chk_right=mysql_num_rows(mysql_query("select * from contest_record1 where tsn='".$TEST['tsn']."' and student_sn='".$Stud['student_sn']."' and chk=1"));
     	 $chk_none=mysql_num_rows(mysql_query("select * from contest_record1 where tsn='".$TEST['tsn']."' and student_sn='".$Stud['student_sn']."' and chk=0"));
     	 $chk_wrong=mysql_num_rows(mysql_query("select * from contest_record1 where tsn='".$TEST['tsn']."' and student_sn='".$Stud['student_sn']."' and chk=-1"));
   	 
    	 if ($chk_none==$N) {
    	 	$WORKS['score']="尚未評分";
    	 	$P=0;
    	  }else{
    	  $WORKS['score']="<font color=#FF0000>答對".$chk_right."題，答錯".$chk_wrong."題</font>";
    	  $P=1;	
    	 }
    	 ***/
    	 $Fcolor=($REC[0]>0)?"#0000FF":"#C0C0C0";
    	 $P=$REC[2]; //未評分者不能指定得獎
    	 $WORKS['score']=$REC[3];
       $N=0;   //非作品類, 所以歸零, 避免競賽作品欄出現超連結 連結作品
       $WORKS['prize_memo']=$REC[4];  //最後作答時間
    	//作品類===============================================
    	} else {

			if ($TEST['active']>4 and $TEST['active']<7) {
				//打字比賽記錄
				$REC=get_stud_record_type($TEST,$Stud['student_sn']);
				$P=$N=$REC[0];
				$WORKS['score']=$REC['speed']." 字/分";
				//$WORKS['prize_memo']=get_prize_memo($TEST['tsn'],$Stud['student_sn']);
				//if ($REC[4]=='' and $_POST['hide_no_post']==1) continue;

			} else {
				//學生已評分記錄 return [0]是否作答0,1 [1]作答情況 [2]是否評分0或評分人數 [3]平均成績
				$REC=get_stud_record2_info($TEST,$Stud['student_sn']);
				$P=$N=$REC[0];
				$WORKS['score']=sprintf("%3.2f",$REC[3])." ( ".$REC[2]." 個評分)";
				$WORKS['prize_memo']=get_prize_memo($TEST['tsn'],$Stud['student_sn']);
				if ($REC[4]=='' and $_POST['hide_no_post']==1) continue;
			}
      } // end if $TEST['active'] else
		$t++;

	  if ($TEST['active']<5 or $TEST['active']>6) {
		  //表格標題
		  if ($t%10==1) table_title_2();
	  } else {
	 	  //打字比賽的
		  if ($t%10==1) table_title_type();
	  }

    ?>
   	<tr class="mytr<?php echo $t%2;?>" >
   		<td style="font-size:10pt;color:#800000" width="50" align="center"><?php echo $t;?></td>
   		<td style="font-size:10pt;color:#800000" align="center"><?php echo $Stud['stud_id'];?></td>
   		<td style="font-size:10pt;color:#800000" align="center"><?php echo $Stud['stud_name'];?></td>
   		<td style="font-size:10pt;color:#800000" align="center"><?php echo $Stud['seme_class'].sprintf('%02d',$Stud['seme_num']);?></td>
   		<td style="font-size:10pt;color:#800000" align="center">
   			<?php
   			 if ($_POST['show_small_pic']) {
				switch ($TEST['active']) {
				  case '2':
				  $a=explode(".",$REC[4]);
				  $filename_s=$a[0]."_s.".$a[1];
							?>
					<img src="<?php echo $UPLOAD_U[2].$filename_s; ?>" border="0"><br>
				  <?php
				  break;
				  case '3':
				   ?>
							<embed src="<?php echo $UPLOAD_U[3].$REC[4];?>" width=240 height=180 type=application/x-shockwave-flash Wmode="transparent"><br>
				   <?php
				  break;
				  default:
				} // end switch
   			 } // end if show_small_pic  		
   		
   		//
   			echo $REC[1];
   			?>
   		</td>
   		<td style="font-size:10pt;color:#800000" align="center"><?php echo $WORKS['score'];?></td>
		<?php
		if ($TEST['active']<5 or $TEST['active']>6) {
			?>
				<td style="font-size:10pt;color:#800000"><?php echo $WORKS['prize_memo'];?></td>
			<?php
		}
		?>

   		<td style="font-size:10pt;color:#800000" align="center"><?php if ($REC[2]>0) { ?><input type="text" name="prize_id[<?php echo $Stud['student_sn'];?>]" value="<?php echo $Stud['prize_id'];?>" size="3"><?php } ?></td>
   		<td style="font-size:10pt;color:#800000" align="center"><?php if ($REC[2]>0) { ?><input type="text" name="prize_text[<?php echo $Stud['student_sn'];?>]" value="<?php echo $Stud['prize_text'];?>" size="10"><?php } ?></td>

   	</tr>	
    <?php
    
   } // end while
   ?>
  </table>
   <table border="0" width="100%">
  		<tr>
  			<td>
  			 <input type="button" value="送出得獎設定" onclick="document.myform.act.value='prize_write';document.myform.submit();" style="color:#FF0000">
         <input type="button" value="返回上一頁" onclick="document.myform.act.value='Start';document.myform.submit();"  style="color:#FF00FF">
  			 <?php echo $INFO;?>
  			</td>
  		</tr>
  		<tr>
  		 <td style="color:#0000FF">
  		 ※說明：<br>
  		 　1.得獎序號:用於成績公佈時名次呈現的順序, 保留空白表示該生未得獎, 將不會呈現出來.<br>
  		 　2.得獎名目:實際上呈現的獎項名稱, 如「第一名、第二名、第三名」或「特優、優等、佳作」等.
  		 </td>
  		</tr>
  	</table>
	</div>
	<div id="flashContent" >

	</div>
<?php

} // end if act='prize'
?>
</form>



<Script Language="JavaScript">
	//評分細項的統計函數
	function score_sum(STR) {
		//var Num=<?php echo $SCORE_SET_NUM;?>;
		var intSUM=0;
		var i=0;

		var STR1='s'+STR;
		//學生細項統計
		while (i < document.myform.elements.length)  {
			if (document.myform.elements[i].name.substr(0,STR1.length)==STR1) {
				intSUM=intSUM+document.myform.elements[i].value*1;
			}
			i++;
		}

		//總分
		i =0;
		var STR2='score['+STR+']';
		while (i < document.myform.elements.length)  {
			if (document.myform.elements[i].name==STR2) {
				document.myform.elements[i].value=intSUM;
			}
			i++;
		}
	} // end fnction

	//scratch
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

		swfobject.embedSWF("./include/Scratch.swf", "flashContent", "482", "387", "10.2.0","./include/expressInstall.swf", flashvars, params, attributes);

	}

	function show_pic(filename) {
		var P = '<?php echo $UPLOAD_U[2];?>';
		var file = P + filename;
		var img_str = "<img src='" + file + "' border='0' style='height: 100%; width: 100%; object-fit: contain'>";

		 $("#flashContent").html(img_str);
	}
</Script>


<?php
//評分標題
function table_title_1($tsn) {
	?>
   	<tr bgcolor="#FFFFCC">
   		<td style="font-size:10pt;color:#800000" width="40" align="center">序號</td>
		<!--
		2017.02.28 取消作者資訊
   		<td style="font-size:10pt;color:#800000" width="60" align="center">學號</td>
   		<td style="font-size:10pt;color:#800000" width="60" align="center">姓名</td>
   		<td style="font-size:10pt;color:#800000" width="70" align="center">班級座號</td>
   		-->
   		<td style="font-size:10pt;color:#800000" width="70" align="center">競賽作品</td>
     <?php
     $query="select * from contest_score_setup where tsn='".$tsn."'";
     $result=mysql_query($query);
     if (mysql_num_rows($result)) {
      while ($row=mysql_fetch_array($result,1)) {
      ?>
       <td style="font-size:10pt;color:#800000" width="80" align="center"><?php echo $row['sco_text'];?></td>
       <?php
      }	
     }
     
     ?>
   		<td style="font-size:10pt;color:#800000" width="60" align="center">總成績</td>
   		<td style="font-size:10pt;color:#800000" width="150" align="center">評語</td>
   	</tr>	
   	<?php
} // end function table_title_1

function table_title_2() {
	global $TEST;
	?>
   	<tr bgcolor="#FFFFCC">
   		<td style="font-size:10pt;color:#800000" width="40" align="center">序號</td>
   		<td style="font-size:10pt;color:#800000" width="60" align="center">學號</td>
   		<td style="font-size:10pt;color:#800000" width="60" align="center">姓名</td>
   		<td style="font-size:10pt;color:#800000" width="80" align="center">班級座號</td>
   		<td style="font-size:10pt;color:#800000" width="100" align="center">競賽作品</td>
   		<td style="font-size:10pt;color:#800000" width="130" align="center">評分成績</td>
		<?php
		if ($TEST['active']==1) {
			?>
			<td style="font-size:8pt;color:#800000" width="200" align="center">最後作答時間</td>
			<?php
		} else {
			?>
			<td style="font-size:10pt;color:#800000" width="200" align="center">評語</td>
			<?php
		}
		?>
   		<td style="font-size:10pt;color:#800000" width="60" align="center">得獎序號</td>
   		<td style="font-size:10pt;color:#800000" width="100" align="center">得獎名目</td>

   	</tr>	
<?php
} // end function table_title_2

function table_title_type() {
	global $TEST;
	?>
	<tr bgcolor="#FFFFCC">
		<td style="font-size:10pt;color:#800000" width="40" align="center">序號</td>
		<td style="font-size:10pt;color:#800000" width="60" align="center">學號</td>
		<td style="font-size:10pt;color:#800000" width="60" align="center">姓名</td>
		<td style="font-size:10pt;color:#800000" width="80" align="center">班級座號</td>
		<td style="font-size:10pt;color:#800000" width="200" align="center">檢測記錄</td>
		<td style="font-size:10pt;color:#800000" width="100" align="center">速度</td>
		<td style="font-size:10pt;color:#800000" width="60" align="center">得獎序號</td>
		<td style="font-size:10pt;color:#800000" width="100" align="center">得獎名目</td>
	</tr>
	<?php
} // end function table_title_2

?>
