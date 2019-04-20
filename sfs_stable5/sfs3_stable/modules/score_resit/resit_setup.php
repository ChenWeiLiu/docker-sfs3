<?php	
// $Id: index.php 5310 2009-01-10 07:57:56Z smallduh $
//取得設定檔
include_once "config.php";
//驗證是否登入
sfs_check(); 

//製作選單 ( $school_menu_p陣列設定於 module-cfg.php )
$tool_bar=&make_menu($school_menu_p);
//讀取目前操作的老師有沒有管理權 , 搭配 module-cfg.php 裡的設定
//$module_manager=checkid($_SERVER['SCRIPT_FILENAME'],1);

if ($_POST['act']=='儲存設定') {
 $now_year_seme=$_POST['now_year_seme'];
 $paper_mode=$_POST['paper_mode'];
 $start_time=$_POST['start_time'];
 $end_time=$_POST['end_time'];
 //讀取補考學期別設定
 $sql="select * from resit_seme_setup limit 1";
 $res=$CONN->Execute($sql);
 if ($res->recordcount()==0) {
  $sql="insert into resit_seme_setup (now_year_seme,paper_mode,start_time,end_time) values ('$now_year_seme','$paper_mode','$start_time','$end_time')";
 }else {
 	$sql="update resit_seme_setup  set now_year_seme='$now_year_seme',paper_mode='$paper_mode',start_time='$start_time',end_time='$end_time'";
 }
  $res=$CONN->Execute($sql) or die ('Error! SQL='.$sql);

$INFO="已於".date("Y-m-d H:i:s")."進行儲存!";

}



//取得所有學期資料, 每年有兩個學期
$class_seme_p = get_class_seme(); //學年度	

//讀取補考學期別設定
$sql="select * from resit_seme_setup limit 1";
$res=$CONN->Execute($sql);
if ($res->recordcount()==0) {
 $SETUP['start_time']=date("Y-m-d H:i:s");
 $SETUP['end_time']=date("Y-m-d H:i:s");
 $SETUP['paper_mode']='0';
} else {
 $SETUP=$res->fetchrow();
}

//讀取 POST 值



/**************** 開始秀出網頁 ******************/
//秀出 SFS3 標題
head();
//列出選單
echo $tool_bar;
?>
<form name="myform" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>" onsubmit="return check_setup()">
<table border="1" style="border-collapse:collapse" bordercolor="#111111" bgcolor="#EDEDED">
<tr>
<td>
<table border="0" cellpadding="5">
 <tr>
 	<td align="right">設定啟用補考學期別</td>
 	<td>
		<select size="1" name="now_year_seme">
			<?php
				foreach ($class_seme_p as $k=>$v) {
					if ($curr_year>substr($k,0,3)*1+$CY_step) continue;
			?>
				<option value="<?php echo $k;?>" <?php if ($k==$SETUP['now_year_seme']) echo "selected";?>><?php echo $v;?></option>
			<?php
				}
			?>
		</select>
 	</td>
 </tr>
 <tr>
 		<td align="right" valign="top">領卷模式</td>
 		<td>
 			1.<input type="radio" name="paper_mode" value="0"<?php if ($SETUP['paper_mode']=='0') echo " checked";?>>依試卷個別設定時間領卷<br>
 		  2.<input type="radio" name="paper_mode" value="1"<?php if ($SETUP['paper_mode']=='1') echo " checked";?>>依下列設定時段內開放所有試卷
 		</td>
 </tr>
 <tr>
 		<td align="right">領卷開始時間</td>
 		<td><input type="text" name="start_time" size="20" value="<?php echo $SETUP['start_time'];?>"><font size="2" color="#800000">(格式YYYY-MM-DD HH:MM:SS，領卷模式設為2才生效)</font></td>
 </tr>
 <tr>
 		<td align="right">領卷結束時間</td>
 		<td><input type="text" name="end_time" size="20" value="<?php echo $SETUP['end_time'];?>"><font size="2" color="#800000">(格式YYYY-MM-DD HH:MM:SS，領卷模式設為2才生效)</font></td>
 </tr>
</table>
</td>
</tr>
</table>
<input type="submit" name="act" value="儲存設定">
<br><br>
<font color="red"><?php echo $INFO;?></font>
</form>

<?php

//  --程式檔尾
foot();
?>
<script>
	
 function check_setup() {
 	
   var start_time=document.myform.start_time.value;
	 var end_time=document.myform.end_time.value;
    
    
    //考試時間比較
   	starttime=start_time.replace(/-/g, "/"); 
   	starttime=(Date.parse(starttime)).valueOf() ; // 直接轉換成Date型別所代表的值
   	endtime=end_time.replace(/-/g, "/"); 
   	endtime=(Date.parse(endtime)).valueOf() ; // 直接轉換成Date型別所代表的值
   	
   	//讀取領卷模式
   	for (var i=0; i<myform.paper_mode.length; i++) {
   	if (myform.paper_mode[i].checked)
   		{
      	var paper_mode = myform.paper_mode[i].value;
   		}
  	}
   	   	
    if (starttime>=endtime && paper_mode==1) {
     alert ("考試結束時間不得早於或等於開始時間!");
     return false;
    }	

   return true;
 }

</script>