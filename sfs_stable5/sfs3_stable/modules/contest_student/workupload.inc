<?php
// 讓 ct_painting.php , ct_animation.php , ct_impress.php include的程式碼
//POST 送出後,主程式操作開始 ====================================================================
//登入, 檢查本競賽有無此生名單, 若有, 開始, 若無, 停止
include_once('login.inc');
/***
if ($_POST['act']=='login') {
 $query="select * from contest_user where tsn='".$_POST['tsn']."' and student_sn='".$_SESSION['session_tea_sn']."' and ifgroup=''";
 $result=mysql_query($query);
 if ($STUDENT=mysql_fetch_array($result,1)) {
 	//登入成功
  $_POST['act']="Start";
  $_POST['option1']=$_POST['tsn'];  //競賽的 tsn
  //回寫登入記錄
  $L=$STUDENT['logintimes']+1;
  $query="update contest_user set lastlogin='".date("Y-m-d H:i:s")."',logintimes='$L' where tsn='".$_POST['tsn']."' and student_sn='".$_SESSION['session_tea_sn']."'"; 
  mysql_query($query);
  } else {
    $INFO="你並未參加本次的競賽, 或你並不是團隊中的組長, 請重新選擇!";
    $_POST['act']="";
  } 
  
}// end if act=login
***/

//檔案上傳 table: test_record2 , 儲存後 mode 改為 Start
if (@$_POST['act']=='Upload') {
 //檢驗作答是否逾時
 $TEST=get_test_setup($_POST['option1']);
 $NOWsec=NowAllSec(date("Y-m-d H:i:s"));
 $StartTest=NowAllSec($TEST['sttime']);
 $EndTest=NowAllSec($TEST['endtime']);
 //給30秒緩衝期限, 避免網路塞車
 if ($NOWsec<$EndTest+30) {
	//檢查是否有含檔案, 有再處理
  if ($_FILES['thefile']['name']!="") {
      //檢查資料夾是否有開
      if (!is_dir($UPLOAD_P[$ACTIVE]) && !is_link($UPLOAD_P[$ACTIVE])) {
          if (!is_file($UPLOAD_P[$ACTIVE])) {
              mkdir($UPLOAD_P[$ACTIVE]);
          } else {
              echo "因為有同名檔案...資料夾建立失敗 !";
              exit();
          }
      }


      //檢查是否重復上傳, 若是, 則不再 insert
  	  $query="select * from contest_record2 where tsn='".$_POST['option1']."' and student_sn='".$_SESSION['session_tea_sn']."'";
  	  $result=mysql_query($query);
  	  $D=mysql_num_rows($result);
  	  if ($D>0) {
  	   $F=mysql_fetch_array($result,1);
  	   unlink ($UPLOAD_P[$ACTIVE].$F['filename']);
  	   	if ($TEST['active']==2) {
  	   		$a=explode(".",$F['filename']);
  	   		$filename_s=$a[0]."_s.".$a[1];
  	   		unlink ($UPLOAD_P[$ACTIVE].$filename_s);
  	    } // end if active=2
  	  }
      //檢驗副檔名
      $expand_name=explode(".",$_FILES['thefile']['name']);
      $nn=count($expand_name)-1;
      $ATTR=strtolower($expand_name[$nn]); //轉小寫副檔名
   	  
      //檢測是否允許上傳此類型檔案
      if (check_file_attr($ATTR)) { 

      //新檔名 
      $filename_1=$_POST['option1']."_".$_SESSION['session_tea_sn'].floor(rand(1000,9999)); //後面加四碼亂數, 避免和舊檔重覆, 導致重傳時仍會看到舊圖,cache問題
      $filename=$filename_1.".".$ATTR;
       if (copy($_FILES['thefile']['tmp_name'],$UPLOAD_P[$ACTIVE].$filename)) {
       	 //製造縮圖檔 (靜態繪圖)
       	 if ($TEST['active']==2) {
       	  $filename_s=$filename_1."_s.".$ATTR;
       	  if (!ImageResize($UPLOAD_P[2].$filename, $UPLOAD_P[2].$filename_s, 256, 192, 100)) {
       	   echo "ErroR!";
       	   exit();
       	  }       	  
       	 }
       	
      	  if ($D==0) {
           $query="insert into contest_record2 (tsn,student_sn,filename,anstime) values ('".$_POST['option1']."','".$_SESSION['session_tea_sn']."','".$filename."','".date('Y-m-d H:i:s')."')";
          } else {
           $query="update contest_record2 set filename='".$filename."',anstime='".date('Y-m-d H:i:s')."' where tsn='".$_POST['option1']."' and student_sn='".$_SESSION['session_tea_sn']."'";
          }// end if $D==0
         if (!mysql_query($query)) {
           echo "Error! Query=".$query;
           exit();
         }
        $_POST['act']="Start";
      
       }else{
        echo "檔案上傳失敗, 請聯絡監考老師!";
        exit();
       } // end if copy
      } else {
       $INFO="上傳失敗! 請注意傳檔上傳類型!";
       $_POST['act']='Start';
      }// end if check_file_attr
  }// end if files exist
 } 
} // end if act=upload

//界面呈現開始, 全部包在 <form>裡 , act動作 , option1, option2 參數2個 return返回前一個動作=============================
?>
<form method="post" name="myform" action="<?php echo $_SERVER['PHP_SELF'];?>"  enctype="multipart/form-data">
 <input type="hidden" name="act" value="<?php echo $_POST['act'];?>">
 <input type="hidden" name="option1" value="<?php echo $_POST['option1'];?>">
 <input type="hidden" name="option2" value="<?php echo $_POST['option2'];?>">
 <input type="hidden" name="return" value="<?php echo $_POST['act'];?>">

<?php
//未登入
 if ($_POST['act']=='') {
   stud_login($ACTIVE,$INFO);
 }

//登入成功, 開始
if ($_POST['act']=='Start') {
 
 //取得競賽設定 
 $TEST=get_test_setup($_POST['option1']);				//競賽設定

 $STUD=get_student($_SESSION['session_tea_sn']);  //學生資料
 
 $NOWsec=NowAllSec(date("Y-m-d H:i:s"));
 $StartTest=NowAllSec($TEST['sttime']);
 $EndTest=NowAllSec($TEST['endtime']);

 //取出競賽資料, 若還沒開始
 if ($NOWsec<$StartTest) {
  //競賽尚未開始，提示多久即將進行 ********************
  $LeaveTime=$StartTest-$NOWsec;
   ?>
   <table border="0" width="100%">
    <tr>
   	 <td style="color:#0000FF">
   	 	登入學生：<?php echo $STUD['class_name']." ".$STUD['seme_num']."號 ".$STUD['stud_name'];?>，距離比賽開始還有<input type="text" name="time" size="6">，請注意！      	 	
   	 	</td> 
    </tr>
   </table>
   <?php
   test_main($_POST['option1'],0);
   ?>
 <Script language="JavaScript">
	//全域變數
	var ACT="Start";
	//比賽尚未進行，提示到數計時後開始
	var inttestsec=<?php echo $LeaveTime;?>;
 
  checkLeaveTime();

</Script>
   
   <?php
  //比賽進行中
 } elseif ($NOWsec>=$StartTest and $NOWsec<$EndTest) {
 //競賽已開始 *****************************************
 //該生已作答題數
  $LeaveTime=$EndTest-$NOWsec; //剩餘秒數
  
  //檢查是否已上傳
  $query="select * from contest_record2 where tsn='".$_POST['option1']."' and student_sn='".$_SESSION['session_tea_sn']."'";
  $result=mysql_query($query);
  $N=0;
 	 if (mysql_num_rows($result)>0) {
 	 $row=mysql_fetch_array($result,1);
   $N=1;
   //若為靜態,分析縮圖檔案
 	   	if ($TEST['active']==2) {
  	   		$a=explode(".",$row['filename']);
  	   		$filename_s=$a[0]."_s.".$a[1];
	    } // end if active=2
   }

  
  ?>
  <table border="1" width="100%" style="border-collapse: collapse" bordercolor="#C0C0C0" cellpadding="5">
  	<tr>
  		<td width="80" align="right">剩餘時間</td>
  		<td><input type="text" name="time" value="" size="8">(※請隨時注意作答時間，逾時即無法再上傳檔案, 視同棄權。)</td>
  	</tr>
  	<tr>
  		<td width="80" align="right">檔案上傳</td>
  		<td><input type="file" name="thefile" value="" size="80"></td>
  	</tr>
  </table>
  <input type="button" value="上傳檔案" onclick="if (document.myform.thefile.value!='') { document.myform.act.value='Upload';document.myform.submit(); }">
  	<?php
  	//若檢查已經上傳
  	if (@$N>0) {
  	 ?>
	<table border="0" width="100%">
  	 <tr>
  	 	<td style="color:#FF0000">注意！您已經上傳檔案，時間截止前可隨時修改後再上傳。《<a href="<?php echo $UPLOAD_U[$TEST['active']].$row['filename'];?>" target="_blank">觀看</a>》</td>
  	</tr>
  	
  	<tr>
  	 <td>
  	   <?php
  	    switch ($TEST['active']) {
  	    	case '2':
  	    	
  	 			if ($filename_s!="") {
  	   		?>
  	        <img src="<?php echo $UPLOAD_U[2].$filename_s; ?>" border="0">
  	   		<?php
  	 				} // end if $filename_s!=""
  	      break;
  	    	case '3':
  	   		?>
						<embed src="<?php echo $UPLOAD_U[3].$row['filename'];?>" width=400 height=300 type=application/x-shockwave-flash Wmode="transparent">

  	   		<?php
  	      break;
            case '7':
            ?>
                            <script type="text/javascript" src="../contest_teacher/include/swfobject.js"></script>

                            <script >
                                var flashvars = {
                                    project: "<?php echo $UPLOAD_U[7].$row['filename'];?>" ,
                                    autostart: "false"
                                };

                                var params = {
                                    bgcolor: "#FFFFFF",
                                    allowScriptAccess: "always",
                                    allowFullScreen: "true",
                                    wmode: "window",
                                    menu:"false"

                                };
                                var attributes = {};

                                swfobject.embedSWF("../contest_teacher/include/Scratch.swf", "flashContent", "482", "387", "10.2.0","../contest_teacher/include/expressInstall.swf", flashvars, params, attributes);

                            </script>
                            <div id="flashContent" >

                            </div>

            <?php
            break;
            default:
  	   } // end switch
  	   ?>
  	 </td>
  	</tr>
  </table>
  	 <?php
  	} // end if $N>0 已上傳
  	?>
  	<br>
<table border="0" width="100%">
	<tr><td style="color:#FF0000" align="left">※請仔細瀏覽以下本競賽相關細節...</td></tr>
</table>
<?php
test_main($_POST['option1'],1);
?>

<Script language="JavaScript">
  var ACT="End";
	//比賽進行中，提示剩餘時間, 呼叫 functions.php 裡的 java function
	var inttestsec=<?php echo $LeaveTime;?>;
  checkLeaveTime('End');
</Script>

  <?php
 } else { 
  $_POST['act']='End'; //競賽結束
 }// end if $NOW<$StartTest
} // end if start


 //競賽已經結束
 if ($_POST['act']=='End') {
  $TEST=get_test_setup($_POST['option1']);
  $STUD=get_student($_SESSION['session_tea_sn']);
  //檢查是否已上傳
  $query="select * from contest_record2 where tsn='".$_POST['option1']."' and student_sn='".$_SESSION['session_tea_sn']."'";
  $result=mysql_query($query);
  $N=0;
 	 if (mysql_num_rows($result)>0) {
 	 $row=mysql_fetch_array($result,1);
   $N=1;
   }

  ?>
  <table border="0" width="100%">
    <tr>
   	 <td style="color:#0000FF">
   	 	登入學生：<?php echo $STUD['class_name']." ".$STUD['seme_num']."號 ".$STUD['stud_name'];?>，競賽時間已結束.      	 	
      <?php
      if (@$N==1) {
      	?>
      《<a href="<?php echo $UPLOAD_U[$TEST['active']].$row['filename'];?>" target="_blank">觀看自己的作品</a>》
   	 	<?php
   	  } // end if N>0
   	 	?>
   	 	</td> 
    </tr>
   </table>
  <?php
		test_main($_POST['option1'],1);  
 }
 
?>

</form>
