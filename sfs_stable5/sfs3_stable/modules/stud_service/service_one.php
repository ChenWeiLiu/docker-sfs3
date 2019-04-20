<?php

// $Id: reward_one.php 6735 2012-04-06 08:14:54Z smallduh $

//取得設定檔
include_once "config.php";

sfs_check();
?>
<script type="text/javascript" src="./include/functions.js"></script>
<script type="text/javascript" src="./include/JSCal2-1.9/src/js/jscal2.js"></script>
<script type="text/javascript" src="./include/JSCal2-1.9/src/js/lang/b5.js"></script>
<link type="text/css" rel="stylesheet" href="./include/JSCal2-1.9/src/css/jscal2.css">

<?php

//秀出網頁
head("登錄學生服務學習記錄");

$tool_bar=&make_menu($school_menu_p);

//讀取服務類別 $ITEM[0],$ITEM[1].....
$M_SETUP=get_module_setup('stud_service');
$ITEM=explode(",",$M_SETUP['item']);
$CONFIRM=($M_SETUP['confirm']==null)?0:$M_SETUP['confirm'];

//列出選單
echo $tool_bar;

//換頁時保留已 post 的資料
  $sn=$_GET['sn'];
	$year_seme=$_POST['year_seme'];
	$service_date=$_POST['service_date'];
	$department=$_POST['department'];
	$sponsor=$_POST['sponsor'];
	$item=$_POST['item'];
	$memo=$_POST['memo'];
	$update_sn=$_POST['update_sn'];
	
	
//取得資料庫中所有學期資料, 每年有兩個學期
$class_seme_p = get_class_seme(); //學年度	
$class_seme_p=array_reverse($class_seme_p,1);
//取得目前學年度
$curr_year=curr_year();
$curr_seme=curr_seme();

//目前選定學期
$c_curr_seme=($_POST['c_curr_seme']=='')?$curr_year.$curr_seme:$_POST['c_curr_seme'];
//目前選定班級
$c_curr_class=$_POST['c_curr_class'];



//取得目前登錄者所在部門
if ($department=='') {
$sql_select = "select post_office from teacher_post where teacher_sn='{$_SESSION['session_tea_sn']}'";
$recordSet = $CONN->Execute($sql_select);
$department= $recordSet->fields["post_office"];
}

//=[當按下刪除學生]===============================================
 if ($_GET['sn']!="" and $_GET['sn']!="new" and $_POST['check_del']!="" ){
 		delService_stud($_GET['sn'],$_POST['check_del']);
 }

//=[當按下更新學生服務時間時]===============================================
 if ($_POST['check_update']==1) {
 	if ($_GET['sn']!="" and $_GET['sn']!="new") {
 		     check_exist_service($_GET['sn'],1);
 	   //更新學生登錄時間	
	  foreach($_POST['time_stud'] as $student_sn=>$minutes) {
	  	$studmemo=$_POST['studmemo'][$student_sn];
		$STUD=getService_stud_base($sn,$student_sn);
		$query="update stud_service_detail set minutes='$minutes',studmemo='$studmemo' where item_sn='".$_GET['sn']."' and student_sn='$student_sn' ";	
		if (mysql_query($query)) {
		 echo "更新 ".$STUD['seme_class']."班".$STUD['seme_num']."號".$STUD['stud_name']." => (".$minutes."分鐘)_".$STUD['memo'];
		 if ($studmemo!="") echo "<font color=red><i>".$studmemo."</i>";
		 echo "<br>";
		} else{
		  echo "登錄學生時發生錯誤! Query=".$query;
		  exit();
		}
	  } // end forecah
	  ?>
	  <input type="button" value="返回" onclick="window.location='<?php echo $_SERVER['PHP_SELF'];?>?sn=<?php echo $_GET['sn'];?>'">
	  <?php
	  exit();
 	}
 }
 
//=[當按下登錄服務時間時]==================================================
if ($_POST['save']==1) {
	//必須有勾選學生再處理
	if (count($_POST['selected_stud']))  {
		?>
		<table border="0" width="800">
			<tr>
				<td width="450" valign="top">			
		<?php
    //先登錄服務項目 , 新資料則先儲存, 再取得本服務資料的 sn
    if ($sn=="" or $sn=="new") {
    $query="insert into stud_service (year_seme,service_date,department,item,memo,update_sn,input_sn,input_time,confirm,sponsor) values ('$year_seme','$service_date','$department','$item','$memo','$update_sn','$update_sn','".date('Y-m-d H:i:s')."','$CONFIRM','$sponsor')";
     if (mysql_query($query)) {
     		
     	list($item_sn)=mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));
		  $_GET['sn']=$item_sn;
		 //echo "新增 <font color=blue>$service_date 在 【".getPostRoom($department)."】進行《".$item."》服務...</font><br>\n";

	  }else{
	 	
		echo "登錄服務項目發生錯誤! Query=".$query;
		exit();
		
	  }
	  //若為已存在資料, 直接取得 sn
	}else{
		$item_sn=$_GET['sn'];
       check_exist_service($item_sn,0); // 不顯示任何內容
       $S=getService_one($item_sn); //取得資料, 以便檢查是否已認證
  } // end if  $sn=="new"
  if ($S['confirm']==0) {
	   //再登錄學生	
	  foreach($_POST['selected_stud'] as $student_sn=>$name) {
	  	$minutes=$_POST['time_stud'][$student_sn];
		$studmemo=$_POST['studmemo'][$student_sn];
		  if (check_exist_service_stud($item_sn,$student_sn)) {
			 echo "<font color=red>".$student_sn.$name." 登錄資料已存在, 不予重覆登錄! </font><br>";
		  } else {
		$query="insert into stud_service_detail (student_sn,item_sn,minutes,studmemo) values ('$student_sn','$item_sn','$minutes','$studmemo')";	
		if (mysql_query($query)) {
		 //echo $student_sn.$name."(".$minutes."分鐘) _".$memo;
		 //if ($studmemo!="") echo "<font color=red><i>".$studmemo."</i>";
		 //echo "<br>";
		} else{
		  echo "登錄學生時發生錯誤! Query=".$query;
		  exit();
		}
		 } // end if mysql_num_rows 檢驗該生是否已登錄
	  } // end forecah
	 } // end if $S['confirm']	 
	 
	} // end if count($_POST['selected_stud'])
	/***
 ?>
 <font color="#800000">以上學生已完成登錄! <br><input type="button" value="重新登錄新服務" onclick="window.location='<?php echo $_SERVER['PHP_SELF'];?>?sn=new'">或點選右列已登錄的服務進行學生的增減</font>
 
				</td>
  				<td width="350" valign="top">
						※<?php echo c_curr_seme;?>本學期由您認證的服務學習項目
					 <?php
					  //list_pastservice(curr_year().curr_seme());
					  list_pastservice($c_curr_seme);
					 ?>				
					</td>
 			</tr>
 			</table> 
 <?php
 
 exit();
***/
} // end if ($_POST['save']==1)

//=[更新登錄資料]===========================================================
if ($_POST['mode']=='updating_service') {
	$S=getService_one($_SESSION['sn']);
	if ($S['confirm']==0) { //如果已經認證了, 就不能更改
  //此處不更新 input_sn 及 input_time 欄位(原始登錄記錄)
  $query="update stud_service set service_date='$service_date',department='$department',sponsor='$sponsor',item='$item',memo='$memo',update_sn='".$_SESSION['session_tea_sn']."' where sn='".$_SESSION['sn']."'";
  if (!mysql_query($query)) {
   echo "Error! Query=$query";
   exit();
  }
 }
}
//=[刪除登錄資料]===========================================================
if ($_POST['mode']=='delete_service') {
	$S=getService_one($_SESSION['sn']);
	if ($S['confirm']==0) { //如果已經認證了, 就不能刪除
  //先刪除學生
  $query="delete from stud_service_detail where item_sn='".$_SESSION['sn']."'";
  if (!mysql_query($query)) {
   echo "Error! Query=$query";
   exit();
  }
  //再刪除記錄
  $query="delete from stud_service where sn='".$_SESSION['sn']."'";
  if (mysql_query($query)) {
   $_SESSION['sn']='';
   $_GET['sn']='';
  $sn='';
 	$year_seme=$_POST['year_seme'];
	$service_date=date("Y-m-d");
	$department='';
	$item='';
	$memo='';
   } else {
   echo "Error! Query=$query";
   exit();
  }
 }
}
//====================================================================================================================

//若服務資料是由右側選單勾選而來, 預設資料取代 $_POST值
if ($_GET['sn']!="" and $_GET['sn']!="new") {
	
	$S=getService_one($_GET['sn']);
	
	
	$sn=$S['sn'];
	$year_seme=$c_curr_seme=$S['year_seme'];
	$service_date=$S['service_date'];
	$department=$S['department'];
	$sponsor=$S['sponsor'];
	$item=$S['item'];
	$memo=$S['memo'];
	$confirm=$S['confirm']; //是否認證
	$_SESSION['sn']=$_GET['sn'];
}

?>
<table border="1" style="border-collapse:collapse" bordercolor="#CCCCCC" width="830">
	<tr>
		<td>
			<!--- 輸入表單 --->
<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>?sn=<?php echo $_GET['sn'];?>" name="myform">
	<input type="hidden" name="target_service" value="">
	<input type="hidden" name="mode" value="">
			<table border="0" width="100%">
				<tr>
					<td>
						<font color="#800000">服務事件發生於：</font>
				<select name="c_curr_seme" onchange="this.form.submit()">
					<?php
						while (list($tid,$tname)=each($class_seme_p)){
							if ($_GET['sn']!="" and $_GET['sn']!="new" and $c_curr_seme!=$tid) continue; //如果是修改模式，僅列出該學期，無法選擇其他學期
	  					if (substr($tid,0,3)>$curr_year-3) {
			    ?>
      				<option value="<?php echo $tid;?>" <?php if ($c_curr_seme==$tid) echo "selected";?>><?php echo $tname;?></option>
   				<?php
      				} // end if
    				} // end while
		    ?>
				</select> 
						
					</td>
					<td width="430" rowspan="3" style="color:#800000" valign="top">
						※<?php echo $c_curr_seme;?>本學期您登錄的服務學習項目
					 <?php
					  //list_pastservice(curr_year().curr_seme());
					  list_pastservice($c_curr_seme);
					 ?>						
					</td>
			  </tr>
				<tr>
					<td width="400" style="color:#0000FF">步驟1: <font style="font-size:9pt">請輸入<input type="button" value="新服務記錄" onclick="window.location='<?php echo $_SERVER['PHP_SELF'];?>?sn=new'">或點選右列已登錄服務增減學生</font></td>

				</tr>
			   <tr>
					<td width="400" valign="top">
					<?php
					  //列出服務項目表單
					 $year_seme=($year_seme=='')?$c_curr_seme:$year_seme;
					service_table($sn,$year_seme,$service_date,$department,$sponsor,$item,$memo);
					?>			
								
					</td>
				</tr>
			</table>
			<?php
			if ($_GET['sn']!="" and $_GET['sn']!="new" and $_POST['update']=='' and $confirm==0) {
			 ?>
			 <input type="button" value="修改登錄內容" onclick="document.myform.mode.value='update_service';document.myform.submit();">
			 <input type="button" value="刪除這筆記錄" onclick="javascript:confirm_delete();">
			 <?php
			}
			//
			if ($_POST['mode']=='update_service') {
			?>
			  <input type="button" value="確認更改" onclick="document.myform.mode.value='updating_service';document.myform.submit()" style='color:#FF0000'>
			  <input type="button" value="返回" onclick="window.location='<?php echo $_SERVER['PHP_SELF'];?>?sn=<?php echo $_GET['sn'];?>'">
			<?php
			 exit();
			}
			
			
			//當按下 [修改下列學生登錄資料]鈕時, 執行本述後停止 ,不列出已登錄學生
			if ($_GET['sn']!="" and $_GET['sn']!="new" and $_POST['update']==1) {
 			?>
 			 		</td>
	     </tr>
        </table>
 			<?php
			 			update_service_stud($_GET['sn']); 
 				exit();
			 }
			?>

			<table border="0" width="100%">
				<?php
				if ($CONFIRM==0 and $S['confirm']==0) {
					?>
				<tr>
					<td style="color:#FF0000;font-size:9pt">※注意! 本資料必須經管理單位認證核可才會列入時數統計。</td>
				</tr>
				<?php
				}
				?>
				<tr>
					<td style="color:#0000FF">步驟2: <font style="font-size:9pt">請選擇要登錄本服務記錄的學生</font></td>
				</tr>
			</table>

	
 <?php
 
  if ($c_curr_seme!="" and $confirm==0) {
  	
    $s_y = substr($c_curr_seme,0,3);
    $s_s = substr($c_curr_seme,-1);
    
    //做出年級與班級選單
     $tmp=&get_class_select($s_y,$s_s,"","c_curr_class","this.form.submit",$c_curr_class); 
	//$year_seme=sprintf('%03d%d',$s_y,$s_s);
	//$class_array=class_base($c_curr_seme);
	//print_r($class_array);
	 
	 echo $tmp;
	 
  }
 
  if ($c_curr_class!="" and $c_curr_seme==substr($c_curr_class,0,3).substr($c_curr_class,4,1)) {
  	$Cyear=substr($c_curr_class,-5,2);
  	$Cnum=substr($c_curr_class,-2,2);
 	?>
 	<input type='button' name='all_stud' value='全選' onClick='javascript:tagall(1);'><input type='button' name='clear_stud'  value='全不選' onClick='javascript:tagall(0);'>
 	<?php
    //列出勾選學生的表格
    student_select($c_curr_class);
	
 ?>
 <input type="hidden" name="year_seme" value="<?php echo $c_curr_seme;?>">
 <input type="hidden" name="update_sn" value="<?php echo $_SESSION['session_tea_sn'];?>">

 <table border="0" width="100%">
 	<tr>
 		<td style="color:#FF0000;font-size:10pt"><input type="button" value="登錄服務時間" style="color:#FF0000" onclick="check_save()">※注意! 僅登錄有勾選的學生。</td>
 	</tr>
 </table>
 <?php
   } // end if c_curr_class
   ?>
  		</td>
	</tr>
</table>
   <?php
  //列出已登錄的學生
 if ($_GET['sn']!="" and $_GET['sn']!="new" and $_GET['update_stud']=="") {
 	echo "<br>";
  			list_service_stud($_GET['sn']);
 }
 
  

?>
</form>			
 

