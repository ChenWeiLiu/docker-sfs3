<?php
include_once('config.php');
include_once('include/PHPTelnet.php');
sfs_check();


//秀出網頁
head("網路管理 - 防火牆 IP-MAC 功能檢測");

$tool_bar=&make_menu($school_menu_p);

//列出選單
echo $tool_bar;

$module_manager=checkid($_SERVER['SCRIPT_FILENAME'],1);
if ($module_manager!=1) {
 echo "抱歉 , 您沒有無管理權限!";
 exit();
}

//儲存防火牆密碼
if ($_POST['act']=='password') {
  $firewall_ip=$_POST['firewall_ip'];
  $firewall_user=$_POST['firewall_user'];
  $firewall_pwd=$_POST['firewall_pwd'];
  
  $query="replace into net_firewall (id,firewall_ip,firewall_user,firewall_pwd) values ('1','$firewall_ip','$firewall_user','$firewall_pwd')";
  mysql_query($query);
  
}

//讀取防火牆帳密
$query="select * from net_firewall where id=1";
$res=mysql_query($query);
$row=mysql_fetch_array($res,1);
$firewall_ip=$row['firewall_ip'];
$firewall_user=$row['firewall_user'];
$firewall_pwd=$row['firewall_pwd'];

?>
<form method="post" name="myform" action="<?php echo $_SERVER['PHP_SELF'];?>">
 <input type="hidden" name="act" value="<?php echo $_POST['act'];?>">
 <input type="hidden" name="option1" value="<?php echo $_POST['option1'];?>">

<?php
 //物件
 $telnet = new PHPTelnet();

 // if the first argument to Connect is blank,
 // PHPTelnet will connect to the local host via 127.0.0.1
 $result = $telnet->Connect($firewall_ip,$firewall_user,$firewall_pwd);

 if ($result == 0) { 
 	
 	
 	//啟用IP-MAC管制
  if ($_POST['act']=='open_ip_mac') {
  	$telnet->DoCommand('config firewall ipmacbinding setting', $result);
  	$telnet->DoCommand('set bindthroughfw enable', $result);
  	$telnet->DoCommand('set undefinedhost allow', $result);
  	$telnet->DoCommand('end', $result);  
  	
  	//WAN1 界面也要啟用 ipmac才行 , 直接自行手動建立
  	
  }
  
  //針對 WAN1 啟用IP-MAC管制
  if ($_POST['act']=='open_wan1_ipmac') {
    $telnet->DoCommand('config system interface', $result);
  	$telnet->DoCommand('edit wan1', $result);
  	$telnet->DoCommand('set ipmac enable', $result);
  	$telnet->DoCommand('end', $result);  
  }
  
  //讀取 firewall 設定值
 	$telnet->DoCommand('show firewall ipmacbinding setting', $result);
 	// NOTE: $result may contain newlines
 	$RES=explode("\n",$result);
 	foreach ($RES as $k=>$v) { $RES[$k]=strtolower(trim($v)); }  //去除前後空白
  //讀取 interface 設定值
 	$telnet->DoCommand('show system interface', $result);
 	// NOTE: $result may contain newlines
 	$line=0;
 	$INTERFACE=array();
 	
 	$tmp_line=explode("\n",$result);
 	 foreach ($tmp_line as $k=>$v) { 
    $line++;
 		$INTERFACE[$line]=strtolower(trim($v)); 
	  	if (substr($INTERFACE[$line],0,9)=='--more-- ') $INTERFACE[$line]=trim(substr($INTERFACE[$line],10));
 	 }  //去除前後空白
  
  //如果一頁無法顯示完, 送出空白換頁
  if ($INTERFACE[$line]=="--more--") {
   do {
    $line--;
    $telnet->DoCommand(' ', $result);
 		$tmp_line=explode("\n",$result);
 	 	foreach ($tmp_line as $k=>$v) { 
    	$line++;
 			$INTERFACE[$line]=strtolower(trim($v)); 
	  	if (substr($INTERFACE[$line],0,9)=='--more-- ') $INTERFACE[$line]=trim(substr($INTERFACE[$line],10));
 	 	}  //去除前後空白
   } while ($INTERFACE[$line]=="--more--");
  } // end if More 	


  //更新 mysql 中的 ipmac table 記錄
  if ($_POST['act']=='update_ipmac') {
  	//先將所有記錄寫入 0 , 表示皆未鎖, 然後比對 ipmacbinding table , 有記錄的再改為1
    mysql_query("update net_roomsite set ipmac='0'");
    //讀取mysql 現有設定
		$query="select * from net_roomsite where net_edit like '".$COMP_INT."%' and site_num>0 and pc_ip!=''";
 		$res=mysql_query($query);
 		while ($row=mysql_fetch_array($res,1)) {
   			$pc_ip[$row['net_edit']]=$row['pc_ip'];
    		$site_num[$row['net_edit']]=$row['site_num'];
 		} // end while  
  
   //讀取ipmac table ==================================================================
 	$telnet->DoCommand('show firewall ipmacbinding table', $result);
 	//echo $result;
 	// NOTE: $result may contain newlines
 	
 	$line=0;
 	$ipmac_table=array();
 	
 	$tmp_line=explode("\n",$result);
 	 foreach ($tmp_line as $k=>$v) { 
    $line++;
 		$ipmac_table[$line]=strtolower(trim($v)); 
   	if (substr($ipmac_table[$line],0,9)=='--more-- ') $ipmac_table[$line]=trim(substr($ipmac_table[$line],10));
 	 }  //去除前後空白
  
  //如果一頁無法顯示完, 送出空白換頁
  if ($ipmac_table[$line]=="--more--") {
   do {
    $line--;
    $telnet->DoCommand(' ', $result);
 		$tmp_line=explode("\n",$result);
 	 	foreach ($tmp_line as $k=>$v) { 
    	$line++;
 			$ipmac_table[$line]=strtolower(trim($v)); 
	  	if (substr($ipmac_table[$line],0,9)=='--more-- ') $ipmac_table[$line]=trim(substr($ipmac_table[$line],10));
 	 	}  //去除前後空白
   } while ($ipmac_table[$line]=="--more--");
  } // end if More
 // ====================================================================================
 
  //分析 ipmac table
  $IPMAC=array(); //記錄那些 IP 已被設定
  for ($i=1;$i<count($ipmac_table)-1;$i++) {
  	//echo $ipmac_table[$i]."<br>";
   if (substr($ipmac_table[$i],0,4)=='edit') {
     $a=explode(" ",$ipmac_table[$i]);
     $b=explode(" ",$ipmac_table[$i+1]);
     //編號$a 是否為設定的ip , 是的話寫入 ipmac 欄位寫入 1
     if ($a[1]>100 and $b[2]=$pc_ip[$a[1]]) {
      mysql_query("update net_roomsite set ipmac='1' where net_edit='".$a[1]."'");
      $IPMAC[$a[1]]=$pc_ip[$a[1]];
     }     
   }
  } // end for  
  
 } // end if update_ipmac
 
 $telnet->Disconnect();

 //檢查 firewall 有無啟用 IP-MAC 設定
 	if ($RES[2]=='end' or $RES[2]=='' or $RES[3]=='') {
 		?>
  	<font color=red>您的防火牆未啟動 IP-MAC binding 功能! 要使用本模組的電腦教室上網管制，必須啟用該功能。</font><br>
   <input type="button" value="啟用 IP-MAC binding 功能" onclick="document.myform.act.value='open_ip_mac';document.myform.submit();">    
   <?php
   exit();
  }

  //檢查有無啟用介面
  $SET_INTERFACE="";
  foreach ($INTERFACE as $k=>$v) {
  	//if (substr($ipmac_table[$i],0,9)=='--more-- ') $ipmac_table[$i]=substr($ipmac_table[$i],10);

   if (substr($v,0,4)=='edit') { 
   	  $a=explode(" ",$v);
   	  $i_face=$a[1];   	
   	}
   if ($v=='set ipmac enable') { $SET_INTERFACE=$i_face; break; }
  }
  
  
  
  echo "<font color=blue>IP-MAC 設定值:<br>";
  for ($i=1;$i<count($RES)-1;$i++) {
   echo $RES[$i]."<br>";
  }
  
  if ($SET_INTERFACE=="") {
   echo "<font color=red>注意! 未針對任何介面啟用 IP-MAC binding 功能! 您必須登入防火牆針對必要界面啟用此功能。<br>";
   echo "一般都是針對連接內網的界面啟用, 以界面 wan1 為例, telnet 登入您的防火牆後, 指令如下:<br>";
   echo "config sys interface <br>";
   echo "edit wan1 <br>";
   echo "set ipmac enable<br>";
   echo "end<br></font>";
   ?>
       <input type="button" value="針對 wan1 界面啟用 IP-MAC 管制" onclick="document.myform.act.value='open_wan1_ipmac';document.myform.submit();"><br>
       ※如果您的防火牆接內網的界面不是在 WAN1 , 請自行參考上述指令進行手動設定, 切勿按下啟用鈕。<br>
   <?php
   
  } else {
   ?>
   IP-MAC binding 啟用界面:<?php echo $SET_INTERFACE;?></font><br><br>
   <font color=red>恭喜您! 您的防火牆已正常啟用 IP 綁 MAC 功能。</font><br>
   <br>
   ※關於電腦教室上網控制說明: <br>
   系統在每次進行防火牆指令後, 會自動將 IP-MAC binding 現況記錄在資料庫中, 以便開啟程式時可快速呈現結果, 不必自防火牆中重新讀取現況.<br>
   若您發覺電腦教室鎖定情況與實際有誤, 請按下按鈕 <input type="button" value="更新資料庫 IP-MAC 現況" onclick="document.myform.act.value='update_ipmac';document.myform.submit()">
 
   <?php
  }
  
 
 } else {
 	?>
  無法登入防火牆！請輸入登入帳號及密碼:
  <table border="0">
   <tr>
    <td>ＩＰ<input type="text" name="firewall_ip" size="20"></td>
   </tr>

   <tr>
    <td>帳號<input type="text" name="firewall_user" size="20"></td>
   </tr>
   <tr>
    <td>密碼<input type="password" name="firewall_pwd" size="20"></td>
   </tr>
   <tr>
   	<td>
     <input type="button" value="儲存並重新檢測" onclick="document.myform.act.value='password';document.myform.submit()">
    </td>
   </tr>
  </table>
  
  <?php
 }

?>
</form>