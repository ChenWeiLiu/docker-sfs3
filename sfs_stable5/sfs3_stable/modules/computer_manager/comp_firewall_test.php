<?php
include_once('config.php');
include_once('include/PHPTelnet.php');
sfs_check();


//秀出網頁
head("電腦教室管理 - 防火牆檢測");

$tool_bar=&make_menu($school_menu_p);

//列出選單
echo $tool_bar;

$module_manager=checkid($_SERVER['SCRIPT_FILENAME'],1);
if ($module_manager!=1) {
 echo "抱歉 , 您沒有無管理權限!";
 exit();
}


//讀取防火牆帳密
if ($firewall_ip=="" or $firewall_user=="" or $firewall_pwd=="" or $addrgrp_deny_tag=='' or $addrgrp_deny=='' or $addrgrp_access=='' or $addrgrp_comp_all=='') {
    echo "您的防火牆相關資訊未設定完成, 請由模組變數進行設定!";
    exit();
}

  $set_addrgrp_deny=0;
  $set_addrgrp_access=0;

  echo "開始進行防火牆登入測試, 請稍候... (注意！　出現「檢測完成」字樣，才能離開本頁面!)<br>";
 ob_flush();
 flush();
 //物件
 $telnet = new PHPTelnet();

 // if the first argument to Connect is blank,
 // PHPTelnet will connect to the local host via 127.0.0.1
 $result = $telnet->Connect($firewall_ip,$firewall_user,$firewall_pwd);

 if ($result == 0) { 

     echo "防火牆登入成功! <br>";
     ob_flush();
     flush();

     if ($VDOM) {
         echo "貴校防火牆啟用 VDOM ...<br>";

         $telnet->DoCommand('config vdom', $result);
         $telnet->DoCommand('edit root', $result);
         echo str_replace("\n","<br>",$result);
         ob_flush();
         flush();
     }
     echo "<br>-------------------------------------------------------------------------------<br>";
     echo "本模組的運作原理：<br>";
     echo "(1)在防火牆中定義兩個位址群組，假設一個名為「access群組」，一個名為「deny群組」。<br>";
     echo "(2)在防火牆定設定兩條策略：(必須由您手動在防火牆上新增)<br>";
     echo "第一條策略，允許連線網站，來源網址為所有電腦教室IP，目的網址套用「access群組」；<br>";
     echo "第二條策略，禁止連上網路，來源網址套用「deny群組」；<br>";
     echo "(3)如果您想讓某個 IP 不能上網，只要把這個 IP 加入到「deny群組」內即可。這個功能由此模組來進行操作。<br>";
     echo "(4)如果您想讓電腦教室可登入哪些網站，只要把允許的網址加入到「access群組」內即可。這個功能也能由此模組來進行操作。<br>";
     echo "-------------------------------------------------------------------------------<br>";
     echo "現在，程式將協助您在防火牆中定義「deny群組」及「access群組」，<br>";
     echo "您在模組變數中設定的 「deny群組」名稱為 ".$addrgrp_deny."<br>";
     echo "您在模組變數中設定的 「access群組」名稱為 ".$addrgrp_access."<br>";
     ob_flush();
     flush();
     //
     $telnet->DoCommand('show firewall address "sfs3_addrgrp_access_tag"', $result);
     $RES=explode("\n",$result);
     if (trim($RES[1])=='config firewall address') {
         echo "sfs3_addrgrp_access_tag 的 ip address 定義已存在. <br>";
         ob_flush();
         flush();
     } else {
         $telnet->DoCommand('config firewall address', $result);
         $telnet->DoCommand('edit sfs3_addrgrp_access_tag', $result);
         $telnet->DoCommand('set subnet 127.0.0.1 255.255.255.255', $result);
         $telnet->DoCommand('next', $result);
         $telnet->DoCommand('end', $result);
         $telnet->DoCommand('show firewall address "sfs3_addrgrp_access_tag"', $result);
         $RES=explode("\n",$result);
         if (trim($RES[1])=='config firewall address') {
             echo "設定 sfs3_addrgrp_access_tag 的 ip address 定義 完成 (切勿自行手動刪除)<br>";
             foreach ($RES as $k => $v) {
                 echo $v . "<br>";
                 ob_flush();
                 flush();
             }
         } else {
             echo "設定 sfs3_addrgrp_access_tag ip address 定義 失敗! <br>";
             ob_flush();
             flush();
         }
     }

     $telnet->DoCommand('show firewall addrgrp '.$addrgrp_access, $result);
     $RES=explode("\n",$result);
     if (trim($RES[1])=='config firewall addrgrp') {
         echo "access 群組： $addrgrp_access 的定義已存在. <br>";
         $set_addrgrp_access=1;
         ob_flush();
         flush();
     } else {
         echo "定義位址群組 ".$addrgrp_access."...<br>";
         $telnet->DoCommand('config firewall addrgrp', $result);
         $telnet->DoCommand('edit '.$addrgrp_access, $result);
         $telnet->DoCommand('set member "sfs3_addrgrp_access_tag"', $result);
         $telnet->DoCommand('next', $result);
         $telnet->DoCommand('end', $result);
         $telnet->DoCommand('show firewall addrgrp '.$addrgrp_access, $result);
         $RES=explode("\n",$result);
         if (trim($RES[1])=='config firewall addrgrp') {
             echo "設定 access群組 : $addrgrp_access 完成<br>";
             foreach ($RES as $k => $v) {
                 echo $v . "<br>";
                 ob_flush();
                 flush();
             }
             $set_addrgrp_access=1;
         } else {
             echo "設定 access群組 : $addrgrp_access 失敗<br>";
         }

     }
        //檢查是否已待命
             while (trim(end($RES))=="--More--") {
                 $telnet->DoCommand(' ', $result);
                 $RES=explode("\n",$result);
             }

     $telnet->DoCommand('show firewall address "sfs3_addrgrp_deny_tag"', $result);
     $RES=explode("\n",$result);
     if (trim($RES[1])=='config firewall address') {
         echo "sfs3_addrgrp_deny_tag 的 ip address 定義已存在. <br>";
         ob_flush();
         flush();
     } else {
         $telnet->DoCommand('config firewall address', $result);
         $telnet->DoCommand('edit sfs3_addrgrp_deny_tag', $result);
         $telnet->DoCommand('set subnet '.$addrgrp_deny_tag." 255.255.255.255", $result);
         $telnet->DoCommand('next', $result);
         $telnet->DoCommand('end', $result);
         $telnet->DoCommand('show firewall address "sfs3_addrgrp_deny_tag"', $result);
         $RES=explode("\n",$result);
         if (trim($RES[1])=='config firewall address') {
             echo "設定 sfs3_addrgrp_deny_tag 的 ip address 定義 完成 (切勿自行手動刪除)<br>";
             foreach ($RES as $k => $v) {
                 echo $v . "<br>";
                 ob_flush();
                 flush();
             }
         } else {
             echo "設定 sfs3_addrgrp_deny_tag ip address 定義 失敗! <br>";
             ob_flush();
             flush();
         }
     }

     $telnet->DoCommand('show firewall addrgrp '.$addrgrp_deny, $result);
     $RES=explode("\n",$result);
     if (trim($RES[1])=='config firewall addrgrp') {
         echo "deny 群組： $addrgrp_deny 的定義已存在. <br>";
         $set_addrgrp_deny=1;
         ob_flush();
         flush();
     } else {
         echo "定義位址群組 ".$addrgrp_deny."...<br>";
         ob_flush();
         flush();
         $telnet->DoCommand('config firewall addrgrp', $result);
         $telnet->DoCommand('edit '.$addrgrp_deny, $result);
         $telnet->DoCommand('set member "sfs3_addrgrp_deny_tag"', $result);
         $telnet->DoCommand('next', $result);
         $telnet->DoCommand('end', $result);
         $telnet->DoCommand('show firewall addrgrp '.$addrgrp_deny, $result);
         $RES=explode("\n",$result);
         if (trim($RES[1])=='config firewall addrgrp') {
             echo "設定 deny群組 : $addrgrp_deny 完成<br>";
             foreach ($RES as $k => $v) {
                 echo $v . "<br>";
                 ob_flush();
                 flush();
             }
             $set_addrgrp_deny=1;
         } else {
             echo "設定 deny群組 : $addrgrp_deny 失敗<br>";
         }

     }

    //檢查是否已待命  因為群組中的 ip address 數量可能過多，出現 --More-- 要按一下空白
     while (trim(end($RES))=="--More--") {
         $telnet->DoCommand(' ', $result);
         $RES=explode("\n",$result);
     }

     $telnet->DoCommand('show firewall addrgrp '.$addrgrp_comp_all, $result);
     $RES=explode("\n",$result);
     if (trim($RES[1])=='config firewall addrgrp') {
         echo "電腦教室ip群組： $addrgrp_comp_all 的定義已存在. <br>";
         $set_addrgrp_comp_all=1;
         ob_flush();
         flush();
     } else {
         foreach ($RES as $k => $v) {
             echo $v . "<br>";
             ob_flush();
             flush();
         }
         echo "定義電腦教室ip群組 : $addrgrp_comp_all <br>";
         ob_flush();
         flush();
         $telnet->DoCommand('config firewall addrgrp', $result);
         $telnet->DoCommand('edit '.$addrgrp_comp_all, $result);
         $telnet->DoCommand('set member "sfs3_addrgrp_deny_tag"', $result);
         $telnet->DoCommand('next', $result);
         $telnet->DoCommand('end', $result);
         $telnet->DoCommand('show firewall addrgrp '.$addrgrp_comp_all, $result);
         $RES=explode("\n",$result);
         if (trim($RES[1])=='config firewall addrgrp') {
             echo "設定電腦教室ip群組 : $addrgrp_comp_all 完成<br>";
             foreach ($RES as $k => $v) {
                 echo $v . "<br>";
                 ob_flush();
                 flush();
             }
             $set_addrgrp_comp_all=1;
         } else {
             echo "設定電腦教室ip群組 : sfs3_comp_all 失敗<br>";
         }

     }

     //中斷 telnet 連線
     $telnet->Disconnect();
     if ($set_addrgrp_access and $set_addrgrp_deny and $set_addrgrp_comp_all ) {
         echo "系統定義了兩個 IP位址: sfs3_addrgrp_access_tag、sfs3_addrgrp_deny_tag  <br>";
         echo "系統定義了三個位址群組: $addrgrp_access 、 $addrgrp_deny 、 $addrgrp_comp_all <br>";
         echo "以上為本模組在防火牆上運作時必要的定義，請勿自行手動刪除。<br>";
         echo "<br>";
         echo "<p style='color:#FF0000'> 現在, 請在貴校的防火牆適當位置定義以下兩條策略： <br> ";
         echo "第１條：允許 $addrgrp_comp_all 群組連線 $addrgrp_access 群組。<br>";
         echo "第２條：禁止 $addrgrp_deny 對外連線。</p>";
         echo "注意! 第1條必須擺在第2條之前!";
     }

     echo "<br><br><span style='color:#FF0000'>檢測完成</span>";

 } else {
 	?>
  無法登入防火牆！請由模組變數重設防火牆的帳號及密碼。
  <?php
 }

?>