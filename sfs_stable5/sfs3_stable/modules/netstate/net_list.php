<?php

// $Id: reward_one.php 6735 2012-04-06 08:14:54Z smallduh $

//取得設定檔
include_once "config.php";

sfs_check();


//秀出網頁
head("網路管理 - 資訊設備一覽表");

$tool_bar=&make_menu($school_menu_p);

//列出選單
echo $tool_bar;


 for ($k=0;$k<2;$k++) {
 ?>
 <font color="#800000"><b><?php echo "※".$NET_KIND[$k];?></b></font>
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
   ?>
  <tr>
 	  <td style="font-size:10pt" align="center"><?php echo $i;?></td>
 	  <td style="font-size:10pt"><?php echo $E['net_name'];?></td>
 	  <td style="font-size:10pt"><?php if ($E['net_ip_show']==1) { echo $E['net_ip']; } else { echo '---';} ?></td>
 	  <td style="font-size:10pt"><?php if ($E['net_url_show']==1) { ?> <a href="<?php echo $E['net_url'];?>" target="_blank"><?php echo $E['net_url'];?></a><?php } else { echo "---"; } ?></td>
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

?>

