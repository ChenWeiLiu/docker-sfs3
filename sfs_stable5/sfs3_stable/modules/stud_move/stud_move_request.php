<?php
// 載入設定檔
include_once "stud_move_config.php";

// 認證檢查
sfs_check();
//print_r($_SESSION);
//$m_arr = get_sfs_module_set();
//extract($m_arr, EXTR_OVERWRITE);

//傳出參數
/*
 * request_edu_id  呼叫端學校 (自己的學校代碼)
 * resource_edu_id  對方 (轉出端的學校)
 * stud_person_id  學生的身分證字號
 *
 * API 至橋接主機檢查學生狀態
 *
 *
 */


$ch = curl_init();

//要 POST 到 bridge 資訊 ,
$POST_DATA['request_edu_id']=base64_encode($_POST['request_edu_id']);
$POST_DATA['resource_edu_id']=base64_encode($_POST['resource_edu_id']);
$POST_DATA['stud_person_id']=base64_encode($_POST['stud_person_id']);
$POST_DATA['act']=base64_encode("bridge_check");

curl_setopt($ch, CURLOPT_CUSTOMREQUEST,  'POST');    //可用 GET , PUT , POST , DELETE 等 method
curl_setopt($ch, CURLOPT_POSTFIELDS,$POST_DATA);

// 這裡略過檢查 SSL 憑證有效性
curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);
curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);

curl_setopt($ch, CURLOPT_HEADER, 0);

curl_setopt($ch, CURLOPT_URL, "https://bridge.tc.edu.tw/bridge.php");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

$receive=curl_exec($ch);
curl_close($ch);

if (!$receive==false) {
    //轉成陣列 , json_decode 務必加上 true 參數
    $SERVICE=json_decode($receive,true);
    $SERVICE=array_base64_decode($SERVICE);
    if ($SERVICE['result']==0) {
        $SERVICE['message']="無法連線橋接主機!\n";
    }
} else {
    $SERVICE['result']=0;
    $SERVICE['message']="connect false!無法連線橋接主機!";
}

header("Content-type: text/html; charset=utf-8");

$returnJson=json_encode($SERVICE);
echo $returnJson;
