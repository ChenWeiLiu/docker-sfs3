<?php
// $Id: index.php 5310 2009-01-10 07:57:56Z smallduh $
//取得設定檔
include_once "config.php";
//驗證是否登入
sfs_check();
//製作選單 ( $school_menu_p陣列設定於 module-cfg.php )
$tool_bar=&make_menu($school_menu_p);
//讀取目前操作的老師有沒有管理權 , 搭配 module-cfg.php 裡的設定
$module_manager=checkid($_SERVER['SCRIPT_FILENAME'],1);

/**************** 開始秀出網頁 ******************/
//秀出 SFS3 標題
head();
//列出選單
echo $tool_bar;
?>

<br>
以下為以 POST 方法自 SFS3 傳回本學期班級陣列的範例，參數 character=UTF-8 表示把資料轉成 UTF-8 格式再傳回 <br><br>

<p style="font-size:10pt;border-style: solid;border-width: thin;border-color: #000000">
        $ch = curl_init();<br>
        $url="<span style="color:#FF0000"><?= $SFS_PATH_HTML ?>modules/rest/api.php</span>";<br>
        //要 POST 的 各個參數的資訊<br>
        $POST_DATA=array( "<span style="color:#FF0000">search</span>"=>base64_encode("<span style="color:#FF0000">classroom</span>"),"<span style="color:#FF0000">character</span>"=>base64_encode("<span style="color:#FF0000">UTF-8</span>"));<br>
    <br>
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,  '<span style="color:#FF0000">POST</span>');    //可用 GET , PUT , POST , DELETE 等 method<br>
        curl_setopt($ch, CURLOPT_POSTFIELDS,$POST_DATA);     //把傳遞的參數帶入<br>
    <br>
        curl_setopt($ch, CURLOPT_HEADER, 0);<br>
        //放在 header 裡的認證資訊<br>
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('<span style="color:#FF0000">S_ID:帳號</span>','<span style="color:#FF0000">S_PWD:密碼</span>'));<br>
        curl_setopt($ch, CURLOPT_URL, $url);<br>
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);<br>
    <br>
        $receive=curl_exec($ch);<br>
        curl_close($ch);<br>
    <br>
        //轉成陣列<br>
        $SERVICE=json_decode($receive,true); <br>
        $SERVICE=array_base64_decode($SERVICE);<br>
    <br>
        if ($SERVICE['result']) {<br>
        &nbsp;&nbsp;&nbsp;    $data=$SERVICE['data'];    //成功取得的資料<br>
        } else {<br>
        &nbsp;&nbsp;&nbsp;    //發生錯誤!<br>
        &nbsp;&nbsp;&nbsp;        echo  $SERVICE['message']; //列出錯誤訊息<br>
        }<br>
    <br>
    <br>
     <br>
    //把資料做 base64_decode<br>
    function array_base64_decode($data) { <br>
    　foreach($data as $key=>$value){<br>
    　if (is_array($value)){<br>
    　　　$data[$key] = array_base64_decode($value);<br>
    　}else{<br>
    　　　$data[$key]= base64_decode($value);<br>
    　}<br>
    } // end foreach<br>
    <br>
    return $data;<br>
    <br>

</p>

