<?php
include_once('config.php');
include_once('include/PHPTelnet.php');
sfs_check();


//秀出網頁
head("網路管理 - 電腦教室上網管理");

$tool_bar=&make_menu($school_menu_p);
$check_real=$_POST['check_real'];  //比對實際資料庫
//列出選單
echo $tool_bar;


//讀取防火牆帳密
if ($firewall_ip=="" or $firewall_user=="" or $firewall_pwd=="") {
    echo "您尚未設定防火牆相關資訊, 請由模組變數進行設定!";
    exit();
}

$comproom=$_POST['comproom'];


 //若按了儲存
if ($_POST['act']=="save" and $comproom!="") {

    $COMP_INT=substr($comproom,-1);
    $COMP[1]=100;
    $COMP[2]=200;
    $COMP[3]=300;

    echo "\n<div id=\"show_process\" style=\"display:block\">";

    //步驟
    //1. 先把所有本電腦教室的電腦 iflock 設定寫上,
    //2. 接著, 取得所有 post 過來的 key , 把 iflock 取消
    //3. 讀出所有要 lock 的 ip
    //4. 登入防火牆, 把資料庫中所有 iflock=1 的 ipaddress 寫入

    //1. 先把所有本電腦教室的電腦 iflock 設定寫上
    $query="update comp_roomsite set iflock='1' where net_edit like '".$COMP_INT."%' and site_num>'0' and net_ip!=''";
    $CONN->Execute($query) or die ("Error! SQL=".$query);

    //2. 接著, 取得所有 post 過來的 key , 把 iflock 取消
    foreach ($_POST['net_ip_mode'] as $k => $v) {
        $sql="update comp_roomsite set iflock='0' where net_edit='" . $k . "'";
       // echo $sql."<br>";
        $CONN->Execute($sql) or die ("Error! SQL=".$sql);
    }

    //3. 讀出所有要 lock 的 ip
    $sql="select * from comp_roomsite where iflock='1'";
    $res=$CONN->Execute($sql) or die ("Error! SQL=".$sql);
    $row=$res->GetRows();
    $lock_address="\"sfs3_addrgrp_deny_tag\"";
    foreach ($row as $v) {
        $lock_address.=" \"".$v['net_ip']."\"";
    }

    //4. 登入防火牆, 把資料庫中所有 iflock=1 的 ip address 寫入
    //telnet物件
    $telnet = new PHPTelnet();
    echo "進行防火牆連線<br>";
    ob_flush();
    flush();
    //進行連線
    $result = $telnet->Connect($firewall_ip, $firewall_user, $firewall_pwd);
    //連線成功才能進行
    if ($result == 0) {
        if ($VDOM) {
            echo "貴校防火牆啟用 VDOM ...<br>";
            ob_flush();
            flush();
            $telnet->DoCommand('config vdom', $result);
            $telnet->DoCommand('edit root', $result);
            echo str_replace("\n","<br>",$result);
            echo "<br>";
            ob_flush();
            flush();
        }
        //針對哪個位址群組, 進行 update , 把要上鎖的群組寫入
        //系統設定針對 $addrgrp 位址群組進行上網封鎖 , 因此把 $lock_address 寫位此群組
        echo "<span style='color:#FF0000'><br>正在寫入防火牆記錄, 請稍候....<br>(時間可能有點久, 請耐心等待畫面跳回電腦教室配置圖，設定才算成功!!!)</span></br>";
        ob_flush();
        flush();
        //設定 firewall addrgrp 值
        $telnet->DoCommand('config firewall addrgrp', $result);
        $telnet->DoCommand('edit "'.$addrgrp_deny.'"', $result);
        $telnet->DoCommand('set member '.$lock_address, $result);
        $telnet->DoCommand('next', $result);
        $telnet->DoCommand('end', $result);
        //讀出設定
        if ($check_real) {
            echo "<br>檢測設定值...<br>";
            ob_flush();
            flush();
            $telnet->DoCommand('show firewall addrgrp ' . $addrgrp_deny, $result);
            $RES=explode("\n",$result);
            foreach ($RES as $k => $v) {
                echo $v . "<br>";
                ob_flush();
                flush();
                //取得已登錄位址
                if (substr(trim($v),0,10)=='set member')  {
                    $all_deny_ip=get_all_ip(trim($v));
                    $lock_ips=explode("\n",$all_deny_ip);
                }
            }
        }
        $telnet->Disconnect();
        echo "</div>";
        echo "<Script> $(\"#show_process\").css(\"display\",\"none\");</Script>";
    } else {
        echo "無法登入防火牆！</div>";
        exit();
    } // end if result=0

}  elseif ($comproom!="") {
    if ($check_real) {
        echo "\n<div id=\"show_process\" style=\"display:block\">";
        $telnet = new PHPTelnet();
        echo "進行防火牆連線...<br>";
        ob_flush();
        flush();
        //進行連線
        $result = $telnet->Connect($firewall_ip, $firewall_user, $firewall_pwd);
        //連線成功才能進行
        if ($result == 0) {
            if ($VDOM) {
                echo "貴校防火牆啟用 VDOM ...<br>";
                ob_flush();
                flush();
                $telnet->DoCommand('config vdom', $result);
                $telnet->DoCommand('edit root', $result);
                echo str_replace("\n","<br>",$result);
                echo "<br>";
                ob_flush();
                flush();
            }
            echo "<br><span style='color:#FF0000'><br>讀取防火牆設定值中，並與資料庫記錄比對，請稍候....<br>(電腦數量多時，時間可能有點久，請等待畫面跳出電腦教室配置圖。)</span></br>";
            ob_flush();
            flush();
            $telnet->DoCommand('show firewall addrgrp ' . $addrgrp_deny, $result);
            $RES=explode("\n",$result);
            foreach ($RES as $k => $v) {
                echo $v . "<br>";
                ob_flush();
                flush();
                //取得已登錄位址
                if (substr(trim($v),0,10)=='set member')  {
                    $all_deny_ip=get_all_ip(trim($v));
                    $lock_ips=explode("\n",$all_deny_ip);
                }
            }
            $telnet->Disconnect();
            echo "</div>";
            echo "<Script> $(\"#show_process\").css(\"display\",\"none\");</Script>";
        } else {
            echo "防火牆連線失敗!";
            echo "</div>";
            exit();
        }
    }
} // end if $_POST['act']!='') else


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
    <input type="checkbox" name="check_real" value="1"<?php if ($check_real==1) echo " checked";?> onclick="document.myform.submit()">立即登入防火牆檢測實際情形
  <?php


if ($comproom!="") {

$COMP_INT=substr($comproom,-1);
$COMP[1]=100;
$COMP[2]=200;
$COMP[3]=300;

 //讀取現有設定
 $query="select * from comp_roomsite where net_edit like '".$COMP_INT."%' and site_num>0 and net_ip!=''";
 $res=mysql_query($query);
 while ($row=mysql_fetch_array($res,1)) {
   	$net_ip[$row['net_edit']]=$row['net_ip'];
    $site_num[$row['net_edit']]=$row['site_num'];
     if ($check_real) {
         //依 防火牆實際限制 有被鎖住的, 放在 $lock_ips array 中
         if (in_array($row['net_ip'],$lock_ips)) {
             $iflock[$row['net_edit']]=1;
             $net_ip_color[$row['net_edit']]="#FFCCCC";
         } else {
             $iflock[$row['net_edit']]=0;
             $net_ip_color[$row['net_edit']]="#CCFFCC";
         }

     } else {
         //僅依 MySQL 資料庫登載
         //開放
         if ($row['iflock']==0) {
             $iflock[$row['net_edit']]=0;
             $net_ip_color[$row['net_edit']]="#CCFFCC";
         } else {
             $iflock[$row['net_edit']]=1;
             $net_ip_color[$row['net_edit']]="#FFCCCC";
         }

     }
 } // end while


 if (count($site_num)==0) {
  echo "<br><font color=red>本校並未設置第".$COMP_INT."電腦教室</font>";
  exit();
 }


  ?>
 <table border="0" width="720">
   <tr><td align="center" style="color:#0000FF">第<?php echo $COMP_INT;?>電腦教室配置圖</td></tr> 
 </table>
 <table width="720" style="border-collapse:collapse" bordercolor="#000000" border="0">
  <tr>
    <td width="180">
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
    <td width="180">
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
    <td width="180">
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
    <td width="180">
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
 <table width="720" border="0" style="border-collapse:collapse" bordercolor="#000000">
 	<tr>
 		<td align="center">
 			<table width="80" border="1" style="border-collapse:collapse" bordercolor="#000000">
 			 <tr><td align="center">教師位置</td></tr>
 			</table>
 		</td>
 </tr>
 </table>
  
  
  <br>
  <table width="720" border="0">
   <tr>
     <td width="300">
      <table border="0">
        <tr>
         <td>
          <table border="1" style="border-collapse:collapse" width="30" cellpadding="0" cellspacing="0" bordercolor="#000000">
           <tr><td bgcolor="#FFCCCC" width="30">&nbsp;</td></tr>
          </table>
         </td>
        <td style="font-size:9pt">封鎖對外連線</td>
        <td>
          <table border="1" style="border-collapse:collapse" width="30" cellpadding="0" cellspacing="0" bordercolor="#000000">
           <tr><td bgcolor="#CCFFCC" width="30">&nbsp;</td></tr>
          </table>
         </td>
         <td style="font-size:9pt">開放自由上網</td>
         <td style="font-size:9pt">
         </td>
        </tr>
      </table>	
      </td> 

          <td align="right" width="420">
              <input type="button" style="color:#00AA00" onclick="check_tag('tag_it','net_ip_mode',1)" value="全部開放">
              <input type="button" style="color:#AA0000" onclick="check_tag('tag_it','net_ip_mode',0)" value="全部封鎖">
              <input type="button" value="儲存設定" onclick="document.myform.act.value='save';document.myform.submit()">
          </td>
      </tr>
  </table>
  <br>
  <font size="2" color=red>※注意! 打勾代表允許該電腦上網(校外網路)，記得要再按下 「儲存設定」。</font>
    
   
  
  <?php

} // end if comproom
?>
</form>

<?php

//顯示每個座位的顯況
function pc_site ($edit_num) {
 global $net_ip,$net_ip_color,$lock_ips,$site_num,$iflock;
 if ($site_num[$edit_num]>0) {
 ?>
 	<table border="1" style="border-collapse:collapse" bordercolor="#000000" width="90%" height="28">
  	<tr>
   		<td id="id_<?php echo $edit_num;?>" style="background-color: <?php echo $net_ip_color[$edit_num];?>" height="28">
   			<table border="0" width="100%">
   			  <tr>
   			    <td width="50"><input type="checkbox" name="net_ip_mode[<?php echo $edit_num;?>]" value="<?php echo $net_ip[$edit_num];?>" <?php if ($iflock[$edit_num]==0) echo " checked"; ?>><?php echo $site_num[$edit_num];?></td>
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


function get_all_ip($ip_member) {
    // echo "截取 <br>";
    $all_ip=explode(" ",$ip_member);
    $i=0;
    $save_ip="";
    foreach ($all_ip as $IP) {
        $IP=trim($IP);
        $i++;
        if ($i>3) {
            $save_ip.=substr($IP,1,strlen($IP)-2)."\n";
        }
    }

    return $save_ip;

}  // end function

?>
<Script Language="JavaScript">
function check_tag(SOURCE,STR,MODE) {
	/*
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
	*/
  var i =0;
  while (i < document.myform.elements.length)  {
    if (document.myform.elements[i].name.substr(0,STR.length)==STR) {
      document.myform.elements[i].checked=MODE;
    }
    i++;
  }
 } // end function


</Script>