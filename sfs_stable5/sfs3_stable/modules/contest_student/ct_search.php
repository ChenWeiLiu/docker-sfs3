<?php
//取得設定檔
include_once "config.php";

sfs_check();


//秀出網頁
head("網路應用競賽 - 查資料比賽");

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

//POST 送出後,主程式操作開始 ====================================================================
//登入, 檢查本競賽有無此生名單, 若有, 開始, 若無, 停止
include_once('login.inc');

//填答, 寫入資料 **************************************************
if ($_POST['act']=='myans') {
 
 //檢驗作答是否逾時
 $TEST=get_test_setup($_POST['option1']);
 
 $NOWsec=NowAllSec(date("Y-m-d H:i:s"));
 $StartTest=NowAllSec($TEST['sttime']);
 $EndTest=NowAllSec($TEST['endtime']);
  //檢驗已答題數
  $query="select count(*) as num from contest_record1 where tsn='".$_POST['option1']."' and student_sn='".$_SESSION['session_tea_sn']."'";
  $result=mysql_query($query);
  list($N)=mysql_fetch_row($result);  

 //避免連線逾時問題，1秒緩衝時間內仍儲存 , 
 if ($NOWsec<$EndTest+1 and $N<$TEST['search_ibgroup']) {
  $ibsn=$_POST['ibsn'];
  $myans=$_POST['myans'];
  $lurl=$_POST['lurl'];
  $anstime=date("Y-m-d H:i:s");
  //避免重覆作答=========================================================
  $query="select * from contest_record1 where tsn='".$_POST['option1']."' and student_sn='".$_SESSION['session_tea_sn']."' and ibsn='".$ibsn."'";
  if (mysql_num_rows(mysql_query($query))==0) {
  	$query="insert into contest_record1 (tsn,student_sn,ibsn,myans,lurl,anstime) values ('".$_POST['option1']."','".$_SESSION['session_tea_sn']."','$ibsn','$myans','$lurl','$anstime')";
  		if (mysql_query($query)) {
  			$N++;
  			if ($N>=$TEST['search_ibgroup']) {
  			 $_POST['act']='End'; //作答完畢
  			} else {
   		   $_POST['act']='Start';
   		  }
  		} else {
   		echo "Error! Query=$query <br>";
   		echo "請通知監考老師!";
   		exit();
   		}
  }// end if mysql_num_rows==0  此題尚未作答
  $_POST['act']='Start';
 //時間到了======
 } else {
   $_POST['act']='End';
 }
 // end if  $NOWsec<$EndTest+1 and $N<100 
  	
} // end if $_POST['act']=='myans'


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
   stud_login(1,$INFO);
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
   	 	登入學生：<?php echo $STUD['class_name']." ".$STUD['seme_num']."號 ".$STUD['stud_name'];?>，距離比賽開始還有 <input type="text" name="time" size="9"> ，請注意！      	 	
   	 	</td> 
    </tr>
   </form>
   </table>
   <?php
   test_main($_POST['option1'],0);
   ?>
 <Script language="JavaScript">
 	var ACT='Start';
	//比賽尚未進行，提示到數計時後開始
	var inttestsec=<?php echo $LeaveTime;?>;
	//顯示server時間
 
  checkLeaveTime();

</Script>
   
   <?php
  //比賽進行中
 } elseif ($NOWsec>=$StartTest and $NOWsec<$EndTest) {
 //競賽已開始 *****************************************
 //該生已作答題數
  $LeaveTime=$EndTest-$NOWsec; //剩餘秒數
  $query="select count(*) as num from contest_record1 where tsn='".$_POST['option1']."' and student_sn='".$_SESSION['session_tea_sn']."'";
  list($N)=mysql_fetch_row(mysql_query($query));
  //還沒作答完畢
  if ($N<$TEST['search_ibgroup']) {
  $N+=1; //列出下一題
  $query="select * from contest_ibgroup where tsn='".$_POST['option1']."' and tsort='".$N."'";
  $ITEM=mysql_fetch_array(mysql_query($query)); //題目
  ?>
  <input type='hidden' name='ibsn' value='<?php echo $ITEM['ibsn'];?>'>
  <table border="0" width="100%">
  <tr>
  	<td style="color:#800000">目前競賽：<?php echo $TEST['title'];?> (<?php echo $PHP_CONTEST[$TEST['active']];?>，<font color=red>總題數：<?php echo  $TEST['search_ibgroup'];?>題</font>)</td> 
  </tr>

  <tr>
  	<td style="color:#0000FF">登入學生：<?php echo $STUD['class_name']." ".$STUD['seme_num']."號 ".$STUD['stud_name'];?></td> 
  </tr>
  <tr>
  <td>
  <table border="1" width="100%" style="border-collapse: collapse" bordercolor="#C0C0C0" cellpadding="5">
  	<tr>
  		<td width="80" align="right">剩餘時間</td>
  		<td style='color:#FF0000;font-ssize:10pt'><input type="text" name="time" value="" size="10">(※請隨時注意作答時間，本提示可能因個人電腦差異而有誤差，實際時間以伺服器端為主。)</td>
  	</tr>
  	<tr>
  		<td width="80" align="right">題目<?php echo $N;?></td>
  		<td style="color:#0000FF"><?php echo $ITEM['question'];?></td>
  	</tr>
  	<tr>
  		<td width="80" align="right">你的作答</td>
  		<td><input type="text" name="myans" value="" size="80"></td>
  	</tr>
  	<tr>
  		<td width="80" align="right">網址資料</td>
  		<td><input type="text" name="lurl" value="" size="80"></td>
  	</tr>
  	<tr>
  		<td width="80" align="right">&nbsp;</td>
  		<td style="font-size:9pt">※注意, 網址必須符合正確書寫格式（http://xxxx.xxx.xxx/xxx ），若格式不正確導致無法判讀，自行負責。</td>
  	</tr>
  </table>
</td>
</tr>
  	<tr>
  		<td style="color:#FF0000">
  			 <input type="submit" value="送出答案並跳到下一題" onclick="checkmyans();">
  			 <input type="reset" value="清除重寫">
  			 <input type="button" value="放棄這一題" onclick="document.myform.act.value='myans';document.myform.submit()"> 
  		</td>
  	</tr>
</table>

<Script language="JavaScript">

  document.myform.myans.focus();
  var ACT='End';
	//比賽進行中，提示剩餘時間
	var inttestsec=<?php echo $LeaveTime;?>;
  checkLeaveTime();
 
 //送答前檢查
 function checkmyans() {
   if (document.myform.myans.value!='' && document.myform.lurl.value!='') {
    document.myform.act.value='myans';
    document.myform.submit();
   } else {
   	alert('作答不完整!!');
    return false;
   }
 }

</Script>

  <?php
 } else {
 	 $_POST['act']='End';
 } //end if $N<100
  //比賽結束
 } else { 
 	$_POST['act']='End';
 }// end if $NOW<$StartTest  
} // end if act=start

//結束, 列出作答
if ($_POST['act']=='End') {
 //取得競賽設定 
 $TEST=get_test_setup($_POST['option1']);				//競賽設定
 $STUD=get_student($_SESSION['session_tea_sn']);  //學生資料

?>
     <table border="0" width="100%">
     	<tr>
     		<td>作答結束! 以下為您的作答情形。</td>
     	</tr>
      <tr>
  	   <td style="color:#800000">目前競賽：<?php echo $TEST['title'];?>(<?php echo $PHP_CONTEST[$TEST['active']];?>)</td> 
      </tr>
			<tr>
  			<td style="color:#0000FF">登入學生：<?php echo $STUD['class_name']." ".$STUD['seme_num']."號 ".$STUD['stud_name'];?></td> 
  		</tr>
  	</table>	
		<table border="1" width="100%" style="border-collapse: collapse" bordercolor="#C0C0C0" cellpadding="2">
      <tr>
      	<td bgcolor="#FFCCCC" align="center" style="font-size:10pt" width="30">題號</td>
        <td bgcolor="#FFCCCC" align="center" style="font-size:10pt">題目</td>
        <td bgcolor="#FFCCCC" align="center" style="font-size:10pt" width="180">你的作答</td>
        <td bgcolor="#FFCCCC" align="center" style="font-size:10pt" width="70">你的連結</td>
        <td bgcolor="#FFCCCC" align="center" style="font-size:8pt" width="70">送答時間</td>
        <td bgcolor="#FFCCCC" align="center" style="font-size:10pt" width="180">參考答案</td>
      </tr>
      <?php
      $I=0;
      $query="SELECT a.* , b.question,b.ans FROM contest_record1 AS a, contest_ibgroup AS b WHERE a.tsn=b.tsn and a.tsn='".$_POST['option1']."' and a.student_sn='".$_SESSION['session_tea_sn']."' and a.ibsn = b.ibsn order by a.anstime";
      $result=mysql_query($query);
      while ($ITEM=mysql_fetch_array($result)) {
      	$I++;
      	?>
      <tr>
      	<td style="font-size:10pt" width="30" align="center" ><?php echo $I;?></td>
        <td style="font-size:10pt"><?php echo $ITEM['question'];?></td>
        <td style="font-size:10pt" width="180"><?php echo $ITEM['myans'];?></td>
        <td style="font-size:10pt" width="70" align="center"><?php if ($ITEM['lurl']!="") { ?><a href="<?php echo $ITEM['lurl'];?>" target="_blank">瀏覽</a><?php } else { echo "無"; }?></td>
        <td style="font-size:10pt" width="70" align="center" ><?php echo substr($ITEM['anstime'],-8,8);?></td>
        <td style="font-size:10pt" width="180"><?php echo $ITEM['ans'];?></td>
      </tr>
      <?php 
      }
      ?>
		</table>
		※注意! 離開後可能無法再檢視作答.
<?php
} // end if $_POST['act']='End';
?>

</form>
