<?php
include_once('config.php');
include_once('include/PHPTelnet.php');
sfs_check();


//秀出網頁
head("網路管理 - 設定例外網路IP");

$tool_bar=&make_menu($school_menu_p);

//列出選單
echo $tool_bar;

$module_manager=checkid($_SERVER['SCRIPT_FILENAME'],1);
if ($module_manager!=1) {
    echo "抱歉 , 您沒有無管理權限!";
    exit();
}


$telnet = new PHPTelnet();

//讀取防火牆帳密
if ($firewall_ip=="" or $firewall_user=="" or $firewall_pwd=="") {
    echo "您尚未設定防火牆相關資訊, 請由模組變數進行設定!";
    exit();
}

//若按了儲存
if ($_POST['act']!="") {

    //登入防火牆
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
        //讀出所有 ip , 並檢查是否已有此 ip 定義
        $all_ip=explode("\n",$_POST['all_ip']);

        $access_address="\"sfs3_addrgrp_access_tag\"";
        foreach ($all_ip as $check_ip) {
            $check_ip=trim($check_ip);
            $check_ip=preg_replace('/\s(?=\s)/', '', $check_ip);
            $check_ip=preg_replace('/[\r\t]/', '', $check_ip);
            $check_ip=nf_to_wf($check_ip,0);
            $save_ip="";
            //若輸入的非空值
            if ($check_ip!='') {
                echo $check_ip."<br>";
                $telnet->DoCommand('show firewall address '.$check_ip, $result);
                // NOTE: $result may contain newlines
                $RES=explode("\n",$result);
                // $RES[0] 會重覆送出的指令
                //有定義
                if (trim($RES[1])=='config firewall address') {
                    echo "本 IP address 已有定義<br>";
                    foreach ($RES as $k => $v) {
                        echo $v . "<br>";
                        ob_flush();
                        flush();
                    }
                    $save_ip=$check_ip;
                } else {
                    echo "尚未定義，進行定義...<br>";
                    $telnet->DoCommand('config firewall address', $result);
                    $telnet->DoCommand('edit '.$check_ip, $result);
                    $telnet->DoCommand('set subnet '.$check_ip." 255.255.255.255", $result);
                    $telnet->DoCommand('next', $result);
                    $telnet->DoCommand('end', $result);
                    $telnet->DoCommand('show firewall address '.$check_ip, $result);
                    $RES=explode("\n",$result);
                    if (trim($RES[1])=='config firewall address') {
                        echo "設定完成<br>";
                        foreach ($RES as $k => $v) {
                            echo $v . "<br>";
                            ob_flush();
                            flush();
                        }
                        $save_ip=$check_ip;
                    } else {
                        echo "設定失敗! <br>";
                    }
                }

                $access_address.=" \"".$save_ip."\"";

            }

        } // end foreach

        echo "將 access 的所有 IP 儲存在 access 群組... <br>";
        echo $access_address."<br>";
        ob_flush();
        flush();
        //設定 firewall addrgrp 值
        $telnet->DoCommand('config firewall addrgrp', $result);
        $telnet->DoCommand('edit "'.$addrgrp_access.'"', $result);
        $telnet->DoCommand('set member '.$access_address, $result);
        $telnet->DoCommand('next', $result);
        $telnet->DoCommand('end', $result);
        //讀出設定
        echo "檢測設定值...<br>";
        $telnet->DoCommand('show firewall addrgrp ' . $addrgrp_access, $result);
        $RES=explode("\n",$result);
        foreach ($RES as $k => $v) {
            echo $v . "<br>";
            ob_flush();
            flush();
            //取得已登錄位址
            if (substr(trim($v),0,10)=='set member')  $all_access_ip=get_all_ip(trim($v));
        }

        $telnet->Disconnect();
        echo "</div>";
        echo "<Script> $(\"#show_process\").css(\"display\",\"none\");</Script>";
    } else {
        echo "無法登入防火牆！</div>";
        exit();
    } // end if result=0

} else {
  //直接登入防火牆,讀取設定
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
            echo str_replace("\n", "<br>", $result);
            echo "<br>";
            ob_flush();
            flush();
        }
        $telnet->DoCommand('show firewall addrgrp ' . $addrgrp_access, $result);
        $RES=explode("\n",$result);
        if (trim($RES[1])=='config firewall addrgrp') {
            foreach ($RES as $k => $v) {
                echo $v . "<br>";
                ob_flush();
                flush();
                //取得已登錄位址
                if (substr(trim($v),0,10)=='set member')  $all_access_ip=get_all_ip(trim($v));
            }

        } else {
            echo "找不到 $addrgrp_access 位址群組定義! <br>";
            echo "請再執行一次「防火牆登入測試」。 <br>";
            exit();
        }
    } else {
        echo "無法登入防火牆！</div>";
        exit();
    }
} // end if $_POST['act']!='')


?>
<form method="post" name="myform" action="<?php echo $_SERVER['PHP_SELF'];?>">
    <input type="hidden" name="act" value="<?php echo $_POST['act'];?>">
    <textarea name="all_ip" style="width:500px;background-color: #CCCCCC" rows="10" ><?php echo $all_access_ip;?></textarea>
    <br>
    <input type="button" value="儲存設定" onclick="document.myform.act.value='save';document.myform.submit()">
    <br>
    <p style="color:#FF0000">
    ※請輸入 IPv4 格式之合理 IP ，每個 IP 為一行， 如 : 163.17.39.33 。 <br>
    ※本模組運作方式為直接連入防火牆進行設定，請小心操作，以免危及校園網路。<br>
    ※有些網頁，同一個頁面可能會連接多個網站，所以除非是很清楚該網站確實只有單一IP站臺(例如：網路應用競賽平台)，否則盡可能不要使用本功能。<br>
    </p>
</form>


<?php
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


function nf_to_wf($strs, $types){  //全形半形轉換
    $nft = array(
        "(", ")", "[", "]", "{", "}", ".",
        ",", ";", ":",
        "-",  "!", "@", "#", "$", "%", "&", "|", "\\",
        "/", "+", "=", "*", "~",
        "`", "'", "\"","?",
        "<", ">",
        "^", "_",
        "0", "1", "2", "3", "4", "5", "6", "7", "8", "9",
        "a", "b", "c", "d", "e", "f", "g", "h", "i", "j",
        "k", "l", "m", "n", "o", "p", "q", "r", "s", "t",
        "u", "v", "w", "x", "y", "z",
        "A", "B", "C", "D", "E", "F", "G", "H", "I", "J",
        "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T",
        "U", "V", "W", "X", "Y", "Z",
        " "
    );
    $wft = array(
        "（", "）", "〔", "〕", "｛", "｝", "﹒",
        "，", "；", "：",
        "－",  "！", "＠", "＃", "＄", "％", "＆", "｜", "﹨",
        "∕", "＋", "＝", "＊", "?",
        "、", "、", "?","？",
        "＜", "＞",
        "︿", "＿",
        "０", "１", "２", "３", "４", "５", "６", "７", "８", "９",
        "ａ", "ｂ", "ｃ", "ｄ", "ｅ", "ｆ", "ｇ", "ｈ", "ｉ", "ｊ",
        "ｋ", "ｌ", "ｍ", "ｎ", "ｏ", "ｐ", "ｑ", "ｒ", "ｓ", "ｔ",
        "ｕ", "ｖ", "ｗ", "ｘ", "ｙ", "ｚ",
        "Ａ", "Ｂ", "Ｃ", "Ｄ", "Ｅ", "Ｆ", "Ｇ", "Ｈ", "Ｉ", "Ｊ",
        "Ｋ", "Ｌ", "Ｍ", "Ｎ", "Ｏ", "Ｐ", "Ｑ", "Ｒ", "Ｓ", "Ｔ",
        "Ｕ", "Ｖ", "Ｗ", "Ｘ", "Ｙ", "Ｚ",
        "　"
    );

    if ($types == '1'){
        // 轉全形
        $strtmp = str_replace($nft, $wft, $strs);
    }else{
        // 轉半形
        $strtmp = str_replace($wft, $nft, $strs);
    }
    return $strtmp;
}
?>
