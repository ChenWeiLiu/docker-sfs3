<?php

// $Id: reward_one.php 6735 2012-04-06 08:14:54Z smallduh $

//取得設定檔
include_once "config.php";

sfs_check();

//秀出網頁
head("網路應用競賽 - 管理查資料比賽題庫");

?>
<script type="text/javascript" src="./include/tr_functions.js"></script>

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

if (!$MANAGER) {
 echo "<font color=red>抱歉! 你沒有管理權限, 系統禁止你繼續操作本功能!!!</font>";
 exit();
}

//POST 送出後,主程式操作開始 
//新增一題
if ($_POST['act']=='inserting') {

$question=$_POST['question'];
$ans=$_POST['ans'];
$ans_url=$_POST['ans_url'];

$ibsn="i".date("y").date("m").date("d").date("H").date("i").date("s");

//測試代碼是否重覆
	do {
	 $a=floor(rand(10,99));
	 $ibsn_test=$ibsn.$a;
	 $query="select count(*) as num from contest_itembank where ibsn='".$ibsn_test."'";
	 $res=mysql_query($query);
	 list($exist)=mysql_fetch_row($res);
	} while ($exist>0);

	$ibsn=$ibsn_test;
	
//存入
	$query="insert into contest_itembank (ibsn,question,ans,ans_url) values ('$ibsn','$question','$ans','$ans_url')";
  if (mysql_query($query)) {
  	//計算要跳要的頁碼
  	list($ALL)=mysql_fetch_row(mysql_query("select count(*) as num from itembank"));
   	$_POST['option2']=ceil($ALL/$PHP_PAGE); //無條件進位
   	$_POST['act']='';
  }else{
   echo "Error! query=".$query;
   exit();
  }
}// end if inserting

//快貼處理資料
if ($_POST['act']=='pasting') {
 	$data_arr=explode("\n",$_POST['items']);
  foreach ($data_arr as $ITEM) {
    $I[0]=$I[1]=$I[2]="";
    $I=explode("\t",$ITEM);
    $question=trim($I[0]);
		$ans=trim($I[1]);
		$ans_url=trim($I[2]);
    
    if ($question!="" and $ans!="") {
		
		//測試代碼是否重覆
		do {
			$ibsn="i".date("y").date("m").date("d").date("H").date("i").date("s");
	 		$a=floor(rand(10,99));
	 		$ibsn_test=$ibsn.$a;
	 		$query="select count(*) as num from contest_itembank where ibsn='".$ibsn_test."'";
	 		$res=mysql_query($query);
	 		list($exist)=mysql_fetch_row($res);
		} while ($exist>0);

		$ibsn=$ibsn_test;
	
		//存入
		$query="insert into contest_itembank (ibsn,question,ans,ans_url) values ('$ibsn','$question','$ans','$ans_url')";
  	mysql_query($query);
    } // end if question!='' and $ans!=''
  } // end foreach

  	//計算要跳要的頁碼
  	list($ALL)=mysql_fetch_row(mysql_query("select count(*) as num from itembank"));
   	$_POST['option2']=ceil($ALL/$PHP_PAGE); //無條件進位
   	$_POST['act']='';
  
} // end if pasting


//修改一題
if ($_POST['act']=='updating') {
  $ibsn=$_POST['option1'];
	$query="update contest_itembank set question='".$_POST['question']."',ans='".$_POST['ans']."',ans_url='".$_POST['ans_url']."' where ibsn='".$ibsn."'";
  if (mysql_query($query)) {
  	//更新題本中的解答
  	$query="update contest_ibgroup set question='".$_POST['question']."',ans='".$_POST['ans']."',ans_url='".$_POST['ans_url']."' where ibsn='".$ibsn."'";
  	mysql_query($query);
   $_POST['act']='';
  }else{
   echo "Error! query=".$query;
   exit();
  }
}// end if updating

//刪除一題
if ($_POST['act']=='delete') {
  $ibsn=$_POST['option1'];
	$query="delete from contest_itembank where ibsn='".$ibsn."'";
  if (mysql_query($query)) {
  	mysql_query("optimize table contest_itembank");
    mysql_query("alter table contest_itembank drop id");
    mysql_query("alter table contest_itembank add id int(5) auto_increment not null primary key first");
    $_POST['act']='';
  }else{
   echo "Error! query=".$query;
   exit();
  }
}// end if delete

//整批刪除
if ($_POST['act']=='delete_tag') {
  foreach ($_POST['tag_it'] as $ibsn) { 
	 $query="delete from contest_itembank where ibsn='".$ibsn."'";
   mysql_query($query);
  }
  	mysql_query("optimize table contest_itembank");
    mysql_query("alter table contest_itembank drop id");
    mysql_query("alter table contest_itembank add id int(5) auto_increment not null primary key first");
    $_POST['act']='';
}// end if delete




//界面呈現開始, 全部包在 <form>裡 , act動作 , option1, option2 參數2個 , return記下返回頁面
?>
<form method="post" name="myform" action="<?php echo $_SERVER['PHP_SELF'];?>" enctype="multipart/form-data">
 <input type="hidden" name="act" value="<?php echo $_POST['act'];?>">
 <input type="hidden" name="option1" value="<?php echo $_POST['option1'];?>">
 <input type="hidden" name="option2" value="<?php echo $_POST['option2'];?>">
 <input type="hidden" name="RETURN" value="<?php echo $_POST['return'];?>">
<?php
//預設為新增表單 + 題庫列表
if ($_POST['act']=='') {
	$IB['question']='';
	$IB['ans']='';
	$PAGE=($_POST['option2']=='')?1:$_POST['option2'];
?>

 <table border="0" width="100%">
  	<tr>
  		<td style="color:#0000FF">※新增題庫試題</td>
  	</tr>
  </table>
		<input type="hidden" name="ibsn" value="<?php echo $ibsn;?>">
  <?php
  @form_additem($ib);
  ?>
  <table border="0" width="100%">
  	<tr>
  		<td>
  		 <input type="button" value="新增一題" onclick="checkdata('inserting');">  		 	
  		 <input type="reset" value="重寫">
  		 <input type="button" value="使用快貼大量新增" onclick="document.myform.act.value='paste';document.myform.submit();">
  		</td>
  	</tr>
  </table>
  <hr>
   <table border="0" width="100%">
  	<tr>
  		<td style="color:#800000">．題庫試題列表 (總題數：<?php echo mysql_num_rows(mysql_query("select id from contest_itembank"));?>題) <input type="button" value="刪除勾選試題" onclick="document.myform.act.value='delete_tag';document.myform.submit();">
  			</td>
  	</tr>
  </table>
  <?php
   listitembank($PAGE);

} // end if insert

//修改試題
if ($_POST['act']=='update') {
	$IB=get_item($_POST['option1']);
	
?>

 <table border="0" width="100%">
  	<tr>
  		<td style="color:#0000FF">※編修題庫試題</td>
  	</tr>
  </table>
		<input type="hidden" name="ibsn" value="<?php echo $IB['ibsn'];?>">
  <?php
  @form_additem($IB);
  ?>
  <table border="0" width="100%">
  	<tr>
  		<td>
  		 <input type="button" value="確定修改" onclick="checkdata('updating');">  		 	
  		 <input type="reset" value="重寫">
  		</td>
  	</tr>
  </table>
<?php
} // end if insert


if ($_POST['act']=='paste') {
?>	
 <table border="0" width="100%">
  	<tr>
  		<td style="color:#0000FF">※編修題庫試題 - 自 Excel 快貼</td>
  	</tr>
 </table>
 <table border="0" width="100%">
   <tr>
    <td>
     <textarea cols="80" rows="10" name="items"></textarea>
    </td>
   </tr>
 </table> 
 <input type="button" value="送出" onclick="document.myform.act.value='pasting';document.myform.submit();">	
 <input type="button" value="回上一頁" style="color:#FF00FF" onclick="document.myform.act.value='';document.myform.submit();">
 <table border="0" width="100%">
  <tr>
    <td>請依下圖所示, 直接開啟 Excel 題庫檔, 然後選擇「題目」、「參考答案」、「參考網址」三個部分(不要包括第一行的標題), 複製後再貼到上面區塊內, 按下「送出」即可.</td>
  </tr>
  <tr>
    <td><img src="./images/item_paste.jpg" border="0"></td>
  </tr>
 </table>
<?php
}


 	?>
 	
</form>
<?php
foot();

?>
<Script Language="JavaScript">
	<?php
	if ($_POST['act']=='' or $_POST['act']=='update') {
		?>
   document.myform.question.focus();
  <?php
  }
  ?>
  function checkdata(VAL) {
    if (document.myform.question.value=='' || document.myform.ans.value=='') {
    	alert("輸入的內容不完整!");
    	return false;
    } else {
    	document.myform.act.value=VAL;
      document.myform.submit();
    }
  } // end function;
  
  function del_itembank(IBSN) {
   Y=confirm('您確定要刪除此題?');
   
   if (Y) {
    document.myform.option1.value=IBSN;
    document.myform.act.value='delete';
    document.myform.submit();
   } else {
     return false;
   } // end if    
  } // end function
  
  function page(PAGE) {
   document.myform.option2.value=PAGE;
   document.myform.submit();
  }
  
  
  //找到特定目標, 全選或全取消
	function tag_all(SOURCE,STR) {
	var j=0;
	while (j < document.myform.elements.length)  {
	 if (document.myform.elements[j].name==SOURCE) {
	  if (document.myform.elements[j].checked) {
	   k=1;
	  } else {
	   k=0;
	  }	
	 }
	 	j++;
	}
	
  var i =0;
  while (i < document.myform.elements.length)  {
    if (document.myform.elements[i].name.substr(0,STR.length)==STR) {
      document.myform.elements[i].checked=k;
    }
    i++;
  }
 } // end function
    	
</Script>
