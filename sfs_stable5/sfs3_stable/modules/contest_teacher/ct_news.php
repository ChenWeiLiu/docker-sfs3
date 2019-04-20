<?php

// $Id: reward_one.php 6735 2012-04-06 08:14:54Z smallduh $

//取得設定檔
include_once "config.php";

sfs_check();


//秀出網頁
head("網路應用競賽 - 最新消息");


?>
<script type="text/javascript" src="./include/tr_functions.js"></script>
<script type="text/javascript" src="../../javascripts/JSCal2-1.9/src/js/jscal2.js"></script>
<script type="text/javascript" src="../../javascripts/JSCal2-1.9/src/js/lang/b5.js"></script>
<link type="text/css" rel="stylesheet" href="../../javascripts/JSCal2-1.9/src/css/jscal2.css">

<script src='include/mupload/jquery.MultiFile.js' type="text/javascript" language="javascript"></script>

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
//新增消息
if ($_POST['act']=='inserting') {
 //資料內容
 	$title=$_POST['title'];
	$sttime=$_POST['sday']." ".$_POST['stime_hour'].":".$_POST['stime_min'].":00";
	$endtime=$_POST['eday']." ".$_POST['etime_hour'].":".$_POST['etime_min'].":00";
	$memo=$_POST['memo'];
	$htmlcode=$_POST['htmlcode'];

  $query="insert into contest_news (title,sttime,endtime,memo,updatetime,htmlcode) values ('$title','$sttime','$endtime','$memo','".date('Y-m-d H:i:s')."','$htmlcode')";
  if (!mysql_query($query)) {
   echo "query=".$query;
  }
    
  //取回最後的 auto_increat 的ID值
  list($nsn)=mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));

  //處理檔案上傳
  news_files($nsn);
  
  $_POST['act']='';
  
} // end if inserting

if ($_POST['act']=='updating') {
 //資料內容
  
  $nsn=$_POST['option1'];
 
 	$title=$_POST['title'];
	$sttime=$_POST['sday']." ".$_POST['stime_hour'].":".$_POST['stime_min'].":00";
	$endtime=$_POST['eday']." ".$_POST['etime_hour'].":".$_POST['etime_min'].":00";
	$memo=$_POST['memo'];
	$htmlcode=$_POST['htmlcode'];

  $query="update contest_news set title='$title',sttime='$sttime',endtime='$endtime',memo='$memo',updatetime='".date('Y-m-d H:i:s')."',htmlcode='$htmlcode' where nsn='$nsn'";
  if (!mysql_query($query)) {
   echo "query=".$query;
  }
    
  //處理檔案上傳
  news_files($nsn);
  
  $_POST['act']=$_POST['RETURN'];
  
} // end if updating

//刪除消息
if ($_POST['act']=='del') {
 	
 	$query="delete from contest_news where nsn='".$_POST['option1']."'";
 	mysql_query($query);
 	
 	$query="select * from contest_files where nsn='".$_POST['option1']."'";
 	$res=mysql_query($query);
 	while ($row=mysql_fetch_array($res,1)) {
 	 unlink ($UPLOAD_P[0].$row['filename']);
 	}
 	
 	$query="delete from contest_files where nsn='".$_POST['option1']."'";
 	mysql_query($query);

  //返回先前狀態
 	$_POST['act']=$_POST['RETURN'];
	  
} // end if del

//刪除消息中的檔案
if ($_POST['act']=='del_file') {
 	
 	$query="select * from contest_files where fsn='".$_POST['option2']."'";
 	$res=mysql_query($query);
 	$row=mysql_fetch_array($res,1);
 	unlink ($UPLOAD_P[0].$row['filename']);
 	
 	$query="delete from contest_files where fsn='".$_POST['option2']."'";
 	mysql_query($query);

  //返回先前狀態
 	$_POST['act']='update';
	  
} // end if del_file


//界面呈現開始, 全部包在 <form>裡 , act動作 , option1, option2 參數2個
?>
<form method="post" name="myform" action="<?php echo $_SERVER['PHP_SELF'];?>" enctype="multipart/form-data">
 <input type="hidden" name="act" value="<?php echo $_POST['act'];?>">
 <input type="hidden" name="option1" value="<?php echo $_POST['option1'];?>">
 <input type="hidden" name="option2" value="<?php echo $_POST['option2'];?>">
 <input type="hidden" name="RETURN" value="<?php echo $_POST['act'];?>">
 
<table border="0" width="100%">
 <tr>
  <td>
  	<?php
  	//有管理權者才能看所有公告
  	if ($MANAGER) {
  		?>
  		
  	<input type="button" value="所有消息" onclick="document.myform.act.value='all';document.myform.submit()">
		
<?php
	  } // end if 
if ($MANAGER and ($_POST['act']=='' or $_POST['act']=='all')) {

//檢驗上傳目錄是否存在, 未存在自動建立
if (!file_exists($UPLOAD_NEWS_PATH)) {
	if (!file_exists($UPLOAD_BASE)) {
		mkdir($UPLOAD_BASE,0777);
	}
    mkdir(substr($UPLOAD_NEWS_PATH,0,strlen($UPLOAD_NEWS_PATH)-1),0777);
}

?>

    <input type="button" value="新增消息" onclick="document.myform.act.value='insert';document.myform.submit();">

<?php
} //end if MANAGER
?>
  </td>
 </tr>
</table>
<?php
//新增消息 ===========================================================================
if ($_POST['act']=='insert') {
 ?>
<div style="width:100%" align="center">
  <table border="0" width="100%">
  	<tr>
  		<td style="color:#0000FF" align="center">新增最新消息</td>
  	</tr>
  </table>
  <?php
  $news['nsn']='';
  $news['title']='';
  $news['memo']='';
  $news['sttime']=date("Y-m-d H:i:00");
  $news['endtime']=date("Y-m-d H:i:00");
  $news['htmlcode']=0;
  $mode="inserting";
  form_news($news); //傳入預設值,列出表單
  ?>
 <table border="0" width="100%">
 	<tr>
  		<td>&nbsp;</td>
  		<td style="font-size:10pt;color:#FF0000">
  		 <input type="button" value="送出資料" onclick="check_form('inserting')">  		 	
  		 <input type="reset" value="清除重寫">
  		 <input type="button" style="color:#FF00FF" value="放棄" onclick="document.myform.act.value='<?php echo $_POST['RETURN'];?>';document.myform.submit();">
  		</td>
  	</tr>
 	  </form>
  </table>

</div>
<?php
} // end if _POST['mode']=='insert'

//編修消息 ===========================================================================
if ($_POST['act']=='update') {
 ?>
<div style="width:100%" align="center">
  <table border="0" width="100%">
  	<tr>
  		<td style="color:#0000FF" align="center">編修消息</td>
  	</tr>
  </table>
  <?php
  $query="select * from contest_news where nsn='".$_POST['option1']."'";
  $res=mysql_query($query);
  $news=mysql_fetch_array($res);
  
  form_news($news); //傳入, 列出表單
  
  ?>
 <table border="0" width="100%">
 	<tr>
  		<td>&nbsp;</td>
  		<td style="font-size:10pt;color:#FF0000">
  		 <input type="button" value="送出資料" onclick="check_form('updating');">  		 	
  		 <input type="reset" value="回覆重寫">
  		 <input type="button" style="color:#FF00FF" value="放棄" onclick="document.myform.act.value='<?php echo $_POST['RETURN'];?>';document.myform.submit();">
  		</td>
  	</tr>
 	  </form>
  </table>

</div>
<?php
} // end if _POST['mode']=='update'



//無任何參數, 列出消息 ===========================================================================
if ($_POST['act']=='' or $_POST['act']=='all') {
?>
<div style="width:100%" align="center">
 <table border="0" width="100%" cellpadding="5">
   	<?php
   	 $query=($_POST['act']=='')?"select * from contest_news where sttime<='$Now' and endtime>'$Now' order by updatetime desc":"select * from contest_news order by updatetime desc";
   	 $result=mysql_query($query);
   	 if (mysql_num_rows($result)) {
   	  while ($NEW=mysql_fetch_array($result)) {
   	   echo "<tr><td>";	
   	 	 shownews($NEW);
   	 	 echo "</td></tr>";
   	  }
     } else {
     ?>
      <tr>
      	<td>◎近日無最新消息</td>
      </tr>
     <?php	
     }// end if 
   	?>
</table>
</div>
<?php
} // end if $_POST['mode']==''
?>

</form>
<?php

foot();

?>

<Script language="JavaScript">
	<?php
  	if ($_POST['act']=='insert' or $_POST['act']=='update') { echo "document.myform.title.focus();"; }
   ?>
 function check_form(ACT) {
   var chk_err=0;
    //日期比較 sday+stime_hour+stime_min
   	var starttime=document.myform.sday.value+" "+document.myform.stime_hour.value+":"+document.myform.stime_min.value+":00";
   	starttime=starttime.replace(/-/g, "/"); 
   	starttime=(Date.parse(starttime)).valueOf() ; // 直接轉換成Date型別所代表的值
   	var endtime=document.myform.eday.value+" "+document.myform.etime_hour.value+":"+document.myform.etime_min.value+":00";
   	endtime=endtime.replace(/-/g, "/"); 
   	endtime=(Date.parse(endtime)).valueOf() ; // 直接轉換成Date型別所代表的值
    if (starttime>=endtime) {
     alert ("結束時間不得早於或等於開始時間!");
     chk_err=1;
     return false;
    }	
  	
    if (document.myform.title.value=='') {
    	alert("請輸入標題!");
    	document.myform.title.focus();
    	chk_err=1;
    	return false;
    }  
    if (document.myform.memo.value=='') {
    	alert("請輸入消息內容!");
    	document.myform.memo.focus();
      chk_err=1;
      return false;
    } // end if
    
     if (chk_err==0) {
     	 document.myform.RETURN.value='<?php echo $_POST['RETURN'];?>';
     	 document.myform.act.value=ACT;
			 document.myform.submit();
     }
   }
</Script> 
