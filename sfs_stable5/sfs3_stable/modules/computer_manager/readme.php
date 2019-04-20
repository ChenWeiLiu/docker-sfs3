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
    <td>接下來，進行網路架構規劃說明。</td>
  </tr>
  <tr>
   <td>1.本校的防火牆，於 2016.10.12安裝完畢，為局裡最新配發的 FG-200D ，理論上若您的設備也是這一台，應該是可以用。<br>
  <br>
  <img src="./images/fg.png" border="0">
   </td>
  </tr>

  <tr>
   <td>
    2.上圖中, 本校的架構是電腦教室皆為固定IP，皆設為 192.168.2.x，且使用防火牆的 port2，到達防火牆後才會 NAT 轉成真實IP對外連線。<br>
    <br>
   </td>
  </tr>
    <tr>
        <td>
            3.<span style="color:#FF0000">本模組採用的策略，是封鎖 IPv4 上網功能，請記得要把學生電腦網卡的 IPv6 功能取消。</span><br>
            <br>
            <img src="./images/ipv6_disable.png" border="0">
        </td>
    </tr>
  <tr>
    <td>
        ============================================================================<br>
        <br><font color=blue>本模組管制電腦教室電腦能否上網原理：</font><br><br>
        1.在防火牆中定義兩個位址群組，假設一個名為「access群組」，一個名為「deny群組」。<br>
        2.在防火牆定設定兩條策略：(必須由您手動在防火牆上新增)<br>
        第一條策略，允許連線網站，來源網址為所有電腦教室IP，目的網址套用「access群組」<br>
        第二條策略，禁止連上網路，來源網址套用「deny群組」；<br>
        3.如果您想讓某個 IP 不能上網，只要把這個 IP 加入到「deny群組」內即可。這個功能由此模組來進行操作。<br>
        4.如果您想讓電腦教室可登入哪些網站，只要把允許的網址加入到「access群組」內即可。這個功能也能由此模組來進行操作。<br>
        ==============================================================================
    <br><br>
        <p style="color:#FF0000">因此，如果確定要使用本模組，建議流程：</p>
        1.先到模組變數進行設定，有些變數建議採預設值，將來比較看得懂。<br>
        2.執行 防火牆登入測試。<br>
            此時系統會協助建立三個位址群組定義： <br>
        (1)電腦教室所有電腦ip群組(用於定義所有電腦) <br>
        (2)deny群組 (用於定義哪些電腦不能上網) <br>
        (3)access群組 (用於定義例外網路ip) <br>
        3.在防火牆設定兩條策略<br>
        第一條策略是允許例外連入的ip，也就是即使被封鎖連外，這些ip仍然可以去<br>
        <img src="./images/policy1.png" border="0"><br><br>
        第二條策略是禁止 deny群組連入，也就是只要被加入這群組的ip, 都不能上網<br>
        <img src="./images/policy2.png" border="0"><br><br>
        設定完畢, 以我校的例子, 會像是這樣的畫面<br>
        <img src="./images/policy3.png" border="0"><br><br>

        4.設定電腦教室座位 (這裡會花比較久的時間)<br>
        5.可以開始授權給電腦老師使用了, 在授權的部份, 資訊組長要給管理權 (可執行防火牆登入測試及設定例外IP), 電腦老師只要一般權限.

    </td>
  </tr>
</table>