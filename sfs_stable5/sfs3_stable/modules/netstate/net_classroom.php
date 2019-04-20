<?php
include_once('config.php');
include_once('include/PHPTelnet.php');
sfs_check();


//秀出網頁
head("網路管理 - 電腦教室上網管理");

$tool_bar=&make_menu($school_menu_p);

//列出選單
echo $tool_bar;

$comproom=$_POST['comproom'];


?>
<form method="post" name="myform" action="<?php echo $_SERVER['PHP_SELF'];?>">
 <input type="hidden" name="act" value="<?php echo $_POST['act'];?>">
 <input type="hidden" name="option1" value="<?php echo $_POST['option1'];?>">
 
 <select name="comproom" size="1" onchange="document.myform.act.value='';document.myform.submit()">
  <option value="">請選擇要管理的電腦教室...</option>
  <option value="comproom1"<?php if ($comproom=='comproom1') echo " selected";?>>第一電腦教室</option>
  <option value="comproom2"<?php if ($comproom=='comproom2') echo " selected";?>>第二電腦教室</option>
  <option value="comproom3"<?php if ($comproom=='comproom3') echo " selected";?>>第三電腦教室</option>
 </select> 
<?php
if ($comproom!="") {

 $COMP_INT=substr($comproom,-1);
 $COMP[1]=100;
 $COMP[2]=200;
 $COMP[3]=300;

//當進行動作時（上鎖或解鎖），再連線防火牆，避免等待時間過久
if ($_POST['act']!="") {
	//讀取防火牆帳密
	$query="select * from net_firewall where id=1";
	$res=mysql_query($query);
	$row=mysql_fetch_array($res,1);
	$firewall_ip=$row['firewall_ip'];
	$firewall_user=$row['firewall_user'];
	$firewall_pwd=$row['firewall_pwd'];
 //物件
 $telnet = new PHPTelnet();

 //進行連線
 $result = $telnet->Connect($firewall_ip,$firewall_user,$firewall_pwd);
 //連線成功才能進行
 if ($result == 0) {  	
 	//讀取防火牆 ipmacbinding 設定值 
 	$telnet->DoCommand('show firewall ipmacbinding setting', $result);
 	// NOTE: $result may contain newlines
 	$RES=explode("\n",$result);
 	foreach ($RES as $k=>$v) { $RES[$k]=strtolower(trim($v)); }  //去除前後空白
 	//若第２行即為 end , 表未啟動
  if ($RES[2]=='end') {
   echo "<br><font color=red>未啟動防火牆 IP-MAC 管制設定!</font>";
    $telnet->Disconnect();
   exit();
  } 	
 	
 	//刪除 ip
  if ($_POST['act']=='del_ip') {
  	foreach ($_POST['net_ip_mode'] as $k=>$v) {
  	 $net_ip_post[$k]=$v;
  	 //echo $k."=>".$net_ip_post[$k]."<br>";
  	}
  	//exit();
  	$CMD='';
    foreach ($_POST['check_edit'] as $k=>$v) {
    	//echo $k."=>".$v."<br>";    	
    	if ($net_ip_post[$k]=='del_ip') {
 			 $CMD.='delete '.$k.chr(13).chr(10);
 			 mysql_query("update net_roomsite set ipmac='0' where net_edit='".$k."'"); //將即時結果寫入 mysql
     	} // end if    	
    } // end foreach
    if ($CMD!='') {
     	$telnet->DoCommand('config firewall ipmacbinding table', $result);
     	$telnet->DoCommand($CMD, $result);
     	$telnet->DoCommand('end', $result);     
  	}
 	}

 	//增加 ip
  if ($_POST['act']=='add_ip') {
  	foreach ($_POST['net_ip_mode'] as $k=>$v) {
  	 $net_ip_post[$k]=$v;
  	 //echo $k."=>".$net_ip_post[$k]."<br>";
  	}
  	//exit();
  	
    foreach ($_POST['check_edit'] as $k=>$v) {
    	//echo $k."=>".$v."<br>";
    	$CMD='';
    	if ($net_ip_post[$k]=='add_ip') {
    		$CMD='config firewall ipmacbinding table'.chr(13).chr(10).'edit '.$k.chr(13).chr(10).'set ip '.$v.chr(13).chr(10).'set mac 11:11:11:11:11:11'.chr(13).chr(10).'set status enable'.chr(13).chr(10).'end';
  			$telnet->DoCommand($CMD, $result);
  			mysql_query("update net_roomsite set ipmac='1' where net_edit='".$k."'"); //將即時結果寫入 mysql
  			/***
  			$telnet->DoCommand('config firewall ipmacbinding table', $result);
  			$telnet->DoCommand('edit '.$k, $result);
  			$telnet->DoCommand('set ip '.$v, $result);
  			$telnet->DoCommand('set mac 11:11:11:11:11:11', $result);
  			$telnet->DoCommand('set status enable', $result);
  			$telnet->DoCommand('end', $result);
     	  ***/
     	} // end if    	
    } // end foreach
  } // end if $_POST['act']=='add_ip'
   $telnet->Disconnect();
  
  } else {
   echo "無法登入防火牆！";
    exit();
  } // end if result=0
 } // end if $_POST['act']!='')  
  
 //讀取現有設定
 $query="select * from net_roomsite where net_edit like '".$COMP_INT."%' and site_num>0 and net_ip!=''";
 $res=mysql_query($query);
 while ($row=mysql_fetch_array($res,1)) {
   	$net_ip[$row['net_edit']]=$row['net_ip'];
    $site_num[$row['net_edit']]=$row['site_num'];
    if ($row['ipmac']==0) {
  	 $net_ip_post[$row['net_edit']]="add_ip"; //用於 input 的 value='add_ip'
  	 $net_ip_color[$row['net_edit']]="#CCFFCC";
    } else {
  	 $net_ip_post[$row['net_edit']]="del_ip"; //用於 input 的 value='add_ip'
  	 $net_ip_color[$row['net_edit']]="#FFCCCC";
    }
 } // end while
  
 if (count($site_num)==0) {
  echo "<br><font color=red>本校並未設置第".$COMP_INT."電腦教室</font>";
  exit();
 }

   /***
  2013.04.18改為由資料庫預先記錄
 //重讀讀取教室現有設定
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
 ***/

 
 /***
  //分析 ipmac table
  for ($i=1;$i<count($ipmac_table)-1;$i++) {
  	//echo $ipmac_table[$i]."<br>";
   if (substr($ipmac_table[$i],0,4)=='edit') {
     $a=explode(" ",$ipmac_table[$i]);
     $b=explode(" ",$ipmac_table[$i+1]);
     //編號$a 是否為設定的ip
     if ($a[1]>100 and $b[2]=$net_ip[$a[1]]) {
      $net_ip_post[$a[1]]="del_ip";
      $net_ip_color[$a[1]]="#FFCCCC";
     }     
   }
  } // end for
  ***/
  
  
 //列出電腦教室配置圖
  ?>
 <table border="0" width="600">
   <tr><td align="center" style="color:#0000FF">第<?php echo $COMP_INT;?>電腦教室配置圖</td></tr> 
 </table>
 <table width="600" style="border-collapse:collapse" bordercolor="#000000" border="0">
  <tr>
    <td>
      <table border="0" width="100%">
        <?php
         for ($i=10;$i>0;$i--) {
         ?>
          <tr>
           <td align="center">
            <?php
             pc_site($COMP[$COMP_INT]+$i);
            ?>
           </td>
          </tr>
         <?php
         }        
        ?>       
      </table>
    </td>	
    <td>
      <table border="0" width="100%">
        <?php
         for ($i=20;$i>10;$i--) {
         ?>
          <tr>
           <td align="center">
            <?php
             pc_site($COMP[$COMP_INT]+$i);
            ?>
           </td>
          </tr>
         <?php
         }        
        ?>       
      </table>
    </td>	
    <td>
      <table border="0" width="100%">
        <?php
         for ($i=30;$i>20;$i--) {
         ?>
          <tr>
           <td align="center">
            <?php
             pc_site($COMP[$COMP_INT]+$i);
            ?>
           </td>
          </tr>
         <?php
         }        
        ?>       
      </table>
    </td>	
    <td>
      <table border="0" width="100%">
        <?php
         for ($i=40;$i>30;$i--) {
         ?>
          <tr>
           <td align="center">
            <?php
             pc_site($COMP[$COMP_INT]+$i);
            ?>
           </td>
          </tr>
         <?php
         }        
        ?>       
      </table>
    </td>	
  </tr>
 </table>
  <br>
 <table width="600" border="0" style="border-collapse:collapse" bordercolor="#000000">
 	<tr>
 		<td align="center">
 			<table width="80" border="1" style="border-collapse:collapse" bordercolor="#000000">
 			 <tr><td align="center">教師位置</td></tr>
 			</table>
 		</td>
 </tr>
 </table>
  
  
  <br>
  <table border="0">
    <tr>
     <td><input type="button" value="送出解鎖" onclick="document.myform.act.value='del_ip';document.myform.submit()"></td>
     <td><input type="button" value="送出鎖定" onclick="document.myform.act.value='add_ip';document.myform.submit()"></td>
     <td>
      <table border="0">
        <tr>
         <td>
         </td>
         <td>
          <table border="1" style="border-collapse:collapse" width="30" cellpadding="0" cellspacing="0" bordercolor="#000000">
           <tr><td bgcolor="#FFCCCC" width="30">&nbsp;</td></tr>
          </table>
         </td>
        <td style="font-size:9pt">鎖定</td>
        <td>
          <table border="1" style="border-collapse:collapse" width="30" cellpadding="0" cellspacing="0" bordercolor="#000000">
           <tr><td bgcolor="#CCFFCC" width="30">&nbsp;</td></tr>
          </table>
         </td>
         <td style="font-size:9pt">開放</td>
         <td style="font-size:9pt">
          <input type="checkbox" name="tag_it" value="1" onclick="check_tag('tag_it','check_edit')">全選
         </td>
        </tr>
      </table>	
      </td> 
    </tr>
  </table>
  <br>
  <font size="2" color=red>※注意! 勾選電腦愈多，所需回應的時間愈久，一次鎖定36部電腦，約需90秒的時間</font>
    
   
  
  <?php

} // end if comproom
?>
</form>

<?php

//顯示每個座位的顯況
function pc_site ($edit_num) {
 global $net_ip,$net_ip_color,$net_ip_post,$site_num;
 if ($site_num[$edit_num]>0) {
 ?>
 	<input type="hidden" value="<?php echo $net_ip_post[$edit_num];?>" name="net_ip_mode[<?php echo $edit_num;?>]">
 	<table border="1" style="border-collapse:collapse" bordercolor="#000000" width="90%" height="28">
  	<tr>
   		<td bgcolor="<?php echo $net_ip_color[$edit_num];?>" height="28">
   			<table border="0" width="100%">
   			  <tr>
   			    <td width="50"><input type="checkbox" name="check_edit[<?php echo $edit_num;?>]" value="<?php echo $net_ip[$edit_num];?>"><?php echo $site_num[$edit_num];?></td>
   			    <td width="100" style="font-size:10pt"><?php echo $net_ip[$edit_num];?></td>
   			  </tr>
   			</table>
   		
   		</td>
  	</tr>
 	</table>
 
 <?php
 //沒有啟用的座位, 呈現淺色方塊
 } else {
 	?>
 	<table border="1" style="border-collapse:collapse" bordercolor="#CCCCCC" width="80%" height="28">
  	<tr>
   		<td height="28">&nbsp;&nbsp;</td>
  	</tr>
 	</table>
 <?php
 } // end if site_num>0
}

?>
<Script Language="JavaScript">
function check_tag(SOURCE,STR) {
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