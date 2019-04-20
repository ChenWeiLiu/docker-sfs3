<?php

if (!$_POST['sid']) {
    exit;
}

session_id($_POST['sid']);
//session_start();


require_once('Crypt/DiffieHellman.php');
require_once('Crypt/CBC.php');
include "security.php";
require ("stud_reg_config.php");

// 認證檢查
sfs_check();

$UP_YEAR = ($IS_JHORES == 0) ? 6 : $UP_YEAR = 9; //判斷國中小
if ($_POST['getkey'] == 'true') {

    $alice = new Crypt_DiffieHellman($_POST['serverp'], $_POST['serverg']);

    $alice_pubKey = $alice->generateKeys()->getPublicKey(Crypt_DiffieHellman::BINARY);

    $_SESSION['alicepk'] = $alice_pubKey;

    $alice_computeKey = $alice->computeSecretKey(base64_decode($_POST['serverpk']), Crypt_DiffieHellman::BINARY)->getSharedSecretKey(Crypt_DiffieHellman::BINARY);

    $_SESSION['alicesk'] = $alice_computeKey;


    echo base64_encode($_SESSION['alicepk']);
} else {

    header('Content-Type: application/json; charset=utf-8');
    $key = $_SESSION['alicesk'];
    $aeskey = hash('md5', base64_encode($_SESSION['alicesk']));

    $class_name = class_base();
    $curr_year = curr_year();
    //$str ="";
    //先抓取stud_base資料表取得在籍生資料
    $sql = "Select stud_name as student_name, stud_sex as student_gender,stud_birthday as birthday,stud_country as nationality,stud_person_id as PID, stud_addr_1 as address, stud_study_year as enroll_year,substring(curr_class_num,1,1) as grade,enroll_school, obtain,safeguard from stud_base where stud_study_cond = 0 or stud_study_cond = 15";
    $rs = $CONN->Execute($sql) or user_error("讀取失敗！<br>$sql", 256);


    if (!$curr_seme) {
        $sel_year = curr_year(); //選擇學年
        $sel_seme = curr_seme(); //選擇學期
        $curr_seme = curr_year() . curr_seme(); //現在學年學期
    } else {
        $sel_year = substr($curr_seme, 0, 3);
        $sel_seme = substr($curr_seme, 3, 1);
        $curr_seme = $sel_year . $sel_seme;
    }

    $aryStudyCond = study_cond();
    $aryStudSafeguardKind = stud_safeguard_kind();
    $aryStudObtainKind = stud_obtain_kind();
    while (!$rs->EOF) {
        $guard = trim(iconv("BIG5", "UTF-8", $aryStudSafeguardKind[$rs->fields['safeguard']]));
        $obtain = trim(iconv("BIG5", "UTF-8", $aryStudObtainKind[$rs->fields['obtain']]));
        $nationality = trim(iconv('BIG5', 'UTF-8//IGNORE', trim($rs->fields['nationality'])));
        $results[] = array(
            'curr_seme' => $curr_seme,
            'enroll_year' => trim($rs->fields['enroll_year']),
            'enroll_school' => iconv('BIG5', 'UTF-8//IGNORE', trim($rs->fields['enroll_school'])),
            'stud_grade' => trim($rs->fields['grade']),
            'nationality' => $nationality ? $nationality : 'NA',
            'stud_person_id' => iconv('BIG5', 'UTF-8//IGNORE', trim($rs->fields['PID'])),
            'student_name' => iconv('BIG5', 'UTF-8//IGNORE', trim($rs->fields['student_name'])),
            'stud_gender' => iconv('BIG5', 'UTF-8//IGNORE', trim($rs->fields['student_gender'] == '1' ? '男' : '女')),
            'stud_birthday' => trim($rs->fields['birthday']),
            'stud_address' => iconv('BIG5', 'UTF-8//IGNORE', trim($rs->fields['address'])),
            'obtain' => $obtain ? $obtain : 'NA',
            'safeguard' => $guard ? $guard : 'NA'
        );
        $rs->MoveNext();
    }

    $json = json_encode($results);

    echo Security::encrypt($json, $aeskey);
    exit;
}
?>
