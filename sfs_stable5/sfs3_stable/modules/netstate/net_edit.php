<?php

// $Id: reward_one.php 6735 2012-04-06 08:14:54Z smallduh $

//取得設定檔
include_once "config.php";

sfs_check();


//秀出網頁
head("網路管理 - 設定資訊設備");

$tool_bar=&make_menu($school_menu_p);

//列出選單
echo $tool_bar;

$module_manager=checkid($_SERVER['SCRIPT_FILENAME'],1);
if ($module_manager!=1) {
 echo "抱歉 , 您沒有無管理權限!";
 exit();
}

//新增設備
if ($_POST['act']=='inserting') {
	$net_ip_show=$net_url_show=0;
  foreach($_POST as $k=>$v) {
   ${$k}=$v;
  }
  
  $query="insert into net_base (net_name,net_kind,net_ip,net_ip_show,net_url,net_url_show,net_location,net_memo,net_check) values ('$net_name','$net_kind','$net_ip','$net_ip_show','$net_url','$net_url_show','$net_location','$net_memo','$net_check')";
  mysql_query($query);
  $_POST['act']='';

} // end inserting

//刪除設備
if ($_POST['act']=='del') {
  
  $query="delete from net_base where id='".$_POST['option1']."'";
  mysql_query($query);
  $_POST['act']='';

} // end inserting

//編輯設備
if ($_POST['act']=='update') {
	$net_ip_show=$net_url_show=0;
  foreach($_POST as $k=>$v) {
   ${$k}=$v;
  }
  
  $query="update net_base set net_name='$net_name',net_kind='$net_kind',net_ip='$net_ip',net_ip_show='$net_ip_show',net_url='$net_url',net_url_show='$net_url_show',net_location='$net_location',net_memo='$net_memo',net_check='$net_check' where id='".$_POST['option1']."'";
  mysql_query($query);
  $_POST['act']='';

}


?>
<form method="post" name="myform" action="<?php echo $_SERVER['PHP_SELF'];?>">
	<input type="hidden" name="option1" value="<?php echo $_POST['option1'];?>">
	<input type="hidden" name="option2" value="<?php echo $_POST['option2'];?>">
	<input type="hidden" name="act" value="<?php echo $_POST['act'];?>">

<?php
//新增設備
if ($_POST['act']=='insert') {
 ?>
  ※新登錄一個設備
  
  <?php
   $E['net_url']="http://";
   equipment_form($E);
  ?>
  <input type="button" value="確定新增" onclick="document.myform.act.value='inserting';document.myform.submit()">
 <?php
 
} // end if insert

//編輯設備
if ($_POST['act']=='edit') {
 ?>
  ※編輯設備
  
  <?php
   $E=get_equipment($_POST['option1']);
   equipment_form($E);
  ?>
  <input type="button" value="確定修改" onclick="document.myform.act.value='update';document.myform.submit()">
 <?php
 
} // end if insert


//列出設備
if ($_POST['act']=='') {
?>
<input type="button" value="新增設備" onclick="document.myform.act.value='insert';document.myform.submit()">
<input type="checkbox" value="1" name="check_online" onclick="document.myform.submit()">即時檢測
<br>
<?php
 foreach ($NET_KIND as $k=>$v) {
 ?>
 <font color="#800000"><b><?php echo "※".$v;?></b></font>
 <table border="1" style="border-collapse:collapse" bordercolor="#800000">
 	<tr bgcolor="#FFCCFF">
 	  <td width="30" style="font-size:10pt" align="center">序</td>
 	  <td width="120" style="font-size:10pt" align="center">名稱</td>
 	  <td width="80" style="font-size:10pt" align="center">IP</td>
 	  <td width="180" style="font-size:10pt" align="center">連結網址</td>
 	  <td width="120" style="font-size:10pt" align="center">設備所在地</td>
 	  <td width="60" style="font-size:10pt" align="center">目前狀態</td>
 	  <td width="250" style="font-size:10pt" align="center">註記內容</td>
 	</tr>
 <?
 $query="select * from net_base where net_kind=$k order by net_ip";
 $res=mysql_query($query);
 $i=0;
 while ($E=mysql_fetch_array($res)) {
		
 $i++; 
 if ($_POST['check_online']==1) {
 switch ($E['net_check']) {
 	case '1': //Port 80回應
		if (!$socket = @fsockopen($E['net_ip'], 80, $errno, $errstr, 2)) 	{
  			//離線
  			$STATE="<font color=red>無訊號</font>";
		} else {
  			$STATE="<font color=green>上線</font>";
  			fclose($socket);
		}
 	break;
 	case '2': //使用ping的方式
		exec("ping -c 4 -t 1 " . $E['net_ip'], $output, $result);
		//print_r($output);
		if ($result == 0) {
		//echo "Ping successful!";
     $STATE="<font color=green>上線</font>";
		}else {
     $STATE="<font color=red>無訊號</font>";
		 //echo "Ping unsuccessful!";
	  }
	  break;
	  default:
	    $STATE="未偵測";
	  break;
	 } // end switch
	} else {
	    $STATE="未偵測";
	}
   ?>
  <tr>
 	  <td style="font-size:10pt" align="center"><?php echo $i;?></td>
 	  <td style="font-size:10pt">
 	  	<img src="images/edit.png" style="cursor:hand" title="編輯" onclick="document.myform.act.value='edit';document.myform.option1.value='<?php echo $E['id'];?>';document.myform.submit()">
 	  	<img src="images/del.png" style="cursor:hand"  title="刪除" onclick="if (confirm('您確定要:\n刪除「<?php echo $E['net_name'];?>」記錄？')) { document.myform.act.value='del';document.myform.option1.value='<?php echo $E['id'];?>';document.myform.submit(); } "><?php echo $E['net_name'];?></td>
 	  <td style="font-size:10pt"><?php echo $E['net_ip'];?></td>
 	  <td style="font-size:10pt"><a href="<?php echo $E['net_url'];?>" target="_blank"><?php echo $E['net_url'];?></a></td>
 	  <td style="font-size:10pt" align="center"><?php echo $E['net_location'];?></td>
 	  <td  style="font-size:10pt" align="center"><?php echo $STATE;?></td>
 	  <td style="font-size:10pt"><?php echo $E['net_memo'];?></td>
 	</tr>
   <?php
	} // end while
  ?>
   </table>
   <br>
  <?php 
 } // end foreach
} // end if $_POST['act']==''
?>
</form>

<?php
//取得所有設備
function get_equipment($k) {
	
  $query="select * from net_base where id='$k'";
  $res=mysql_query($query);
  $row=mysql_fetch_array($res,1);
  
  return $row;
} // end function

//表單
function equipment_form($E) {
 global $NET_KIND;
?>
  <table border="0">
   <tr>
     <td>設備名稱</td>
     <td><input type="text" name="net_name" value="<?php echo $E['net_name'];?>"></td>
   </tr>
   <tr>
     <td>設備種類</td>
     <td>
     	<select name="net_kind" size="1">
     		<?php
     		 foreach ($NET_KIND as $k=>$v) {
     		  ?>
     		  <option value="<?php echo $k;?>"<?php if ($k==$E['net_kind']) echo " selected";?>><?php echo $v;?></option>
     		  <?php
     		 }
     		?>
      </select>
     
     </td>
   </tr>
   <tr>
     <td>設備IP</td>
     <td><input type="text" name="net_ip" value="<?php echo $E['net_ip'];?>">&nbsp;<input type="checkbox" name="net_ip_show" value="1"<?php if ($E['net_ip_show']==1) echo "checked";?>>提供瀏覽</td>
   </tr>
   <tr>
     <td>連結網址</td>
     <td><input type="text" name="net_url" value="<?php echo $E['net_url'];?>">&nbsp;<input type="checkbox" name="net_url_show" value="1"<?php if ($E['net_url_show']==1) echo "checked";?>>提供瀏覽</td>
   </tr>
   <tr>
     <td>存放地點</td>
     <td><input type="text" name="net_location" value="<?php echo $E['net_location'];?>"></td>
   </tr>
   <tr>
     <td>附註說明</td>
     <td><textarea cols="50" rows="5" name="net_memo"><?php echo $E['net_memo'];?></textarea></td>
   </tr>
   <tr>
     <td>偵測方式</td>
     <td>
     	 <input type="radio" value="1" name="net_check"<?php if ($E['net_check']==1) echo checked;?>>Port 80 回應
       <input type="radio" value="2" name="net_check"<?php if ($E['net_check']==2) echo checked;?>>Ping 回應
     </td>
   </tr>
  </table>
  
<?php
} // end function

?>

