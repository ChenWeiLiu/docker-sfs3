<?php
include_once('config.php');
include_once('include/PHPTelnet.php');
sfs_check();


//秀出網頁
head("網路管理 - 重要說明");

$tool_bar=&make_menu($school_menu_p);

//列出選單
echo $tool_bar;

$module_manager=checkid($_SERVER['SCRIPT_FILENAME'],1);
if ($module_manager!=1) {
 echo "抱歉 , 您沒有無管理權限!";
 exit();
}
?>
<table border="0">
  <tr>
   <td style="color:#FF0000">首先，必須先聲明，本模組目前以本校的架構測試是正常可執行，但不保證貴校的網路架構可適用。如您無法承擔防火牆設定上的風險，請勿啟用！！！</td>
  </tr>
  <tr>
    <td>接下來，若要完全正常啟用本模組的電腦教室上網管理功能，有幾點必須注意。</td>
  </tr>
  <tr>
   <td>1.您必須先確認貴校的防火牆是最近教育局補助的那台 Fortigate 110C，且採用架構如下圖所示：<br>
  <br>
  <img src="./images/fg.png" border="0">
  <br>  ps.FG-400可能也能使用，但沒實際試過。<br><br>
   </td>
  </tr>

  <tr>
   <td>
    2.電腦教室的個人電腦的IP為固定IP，且此IP會通過防火牆(不論是真實IP或 private ip (192.168.x.x))。以本校為例，本校電腦教室的IP皆設為 192.168.2.x ，到達防火牆後才會 NAT 轉成真實IP對外連線。<br>
    3.防火牆內尚未啟動 IP-MAC-Binding 功能，即 IP 綁 MAC 的功能。（由本模組代為啟動，才能設定成符合模組需求的功能）
    <br>
   </td>
  </tr>
  <tr>
    <td><br><font color=blue>本模組管制電腦教室電腦能否上網，主要是利用 IP-MAC-Binding 原理：</font><br><br>
    	1.利用程式代為啟動 IP-MAC-Binding功能(管制寬鬆)，並將其設定為 WAN1 介面。<br>
    	<br>
    	2.透過表單方式，設定每台電腦的IP是否要寫入防火牆的 IP-MAC binding table中。若要鎖定某電腦不能上網，在寫入IP同時，也任意針對該IP寫入一個錯誤的MAC。<br>
     <br>
      3.由於 IP-MAC-Binding 是採用寬鬆設定，所以防火牆在運作時：<br>
        (1)IP及MAC符合資料庫對應  -->放行通過 <br>
        (2)IP and MAC皆不在資料庫定義之中的    -->放行通過 <br>
        (3)若有IP與資料庫相同但其MAC與資料庫不同 -->禁止通過 (<font color=red>電腦教室的電腦不能上網即是透過此規則管理</font>) <br>
        (4)若MAC與資料庫相同但其IP與資料庫不同　--> 禁止通過	<br>
        
        <br>
        ※注意！如果貴校防火牆有啟用 DHCP 功能，當某電腦已利用 DHCP取得 IP , 它的IP及MAC會被記錄在 DHCP 之address leases，此時若該電腦換其他IP 將會無法上網。
         必須telnet進入防火牆後，下指令 execute dhcp lease-clear ,清除DHCP保留紀錄。
    </td>
  </tr>
  <tr>
    <td style="color:blue"><br>
     設定方式：直接點選模組「防火牆IP-MAC功能檢測」標籤，依系統步驟進行，直到出現　「恭喜您! 您的防火牆已正常啟用 IP 綁 MAC 功能。」文字。
    </td>
  </tr>
  <tr>
   <td>
  模組是以模擬 telnet 的方式登入防火牆，進行相關設定，如果想深入了解，<br>關於 IP-MAC binding 的說明書，請自行 <a style="color:#FF0000" href="./include/IP-MAC-Binding.txt">下載</a> 研究。
   </td>
  </tr>
</table>