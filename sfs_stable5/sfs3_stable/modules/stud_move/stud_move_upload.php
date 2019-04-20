
<?php

if (!$_POST['sid']) {
    exit;
}

session_id($_POST['sid']);
session_start();


require_once 'Crypt/DiffieHellman.php';
require_once('Crypt/CBC.php');
include 'security.php';

include "stud_move_config.php";
include "../../include/sfs_case_dataarray.php";
sfs_check();

header('Content-Type: text/html; charset=utf-8');

if ($_POST['getkey'] == 'true') {

    $alice = new Crypt_DiffieHellman($_POST['serverp'], $_POST['serverg']);

    $alice_pubKey = $alice->generateKeys()->getPublicKey(Crypt_DiffieHellman::BINARY);

    $_SESSION['alicepk'] = $alice_pubKey;

    $alice_computeKey = $alice->computeSecretKey(base64_decode($_POST['serverpk']), Crypt_DiffieHellman::BINARY)->getSharedSecretKey(Crypt_DiffieHellman::BINARY);

    $_SESSION['alicesk'] = $alice_computeKey;


    echo base64_encode($_SESSION['alicepk']);
} else {

    /*
      echo base64_encode($_SESSION['alicesk']);
      exit;
     */

    $key = $_SESSION['alicesk'];
//$iv=base64_decode($_POST['iv']);

    $aeskey = hash('md5', base64_encode($_SESSION['alicesk']));

    /*
      echo $aeskey;
      exit;
     */

//include "stud_move_config.php";
//sfs_check();
//header("content-type:text/html; charset=utf-8");
    $curr_seme = $_POST['curr_seme'];

    $sql = "select a.stud_person_id,a.stud_name,a.stud_birthday,substring(a.curr_class_num,1,1) as stud_grade,a.stud_study_cond,a.obtain,a.safeguard,b.move_kind,b.move_c_date,b.school_id,concat(b.city,b.school) as schoolname from stud_base as a, stud_move as b where a.student_sn = b.student_sn and b.move_kind not in (5,13) and b.move_year_seme=" . $curr_seme;

    $rs = $CONN->Execute($sql) or trigger_error("建立上傳異動資料失敗 <br/>" . $sql, E_USER_ERROR);
//print_r($rs);
    $result = array();
    $aryStudyCond = study_cond();
    $aryStudSafeguardKind = stud_safeguard_kind();
    $aryStudObtainKind = stud_obtain_kind();
    while (!$rs->EOF) {
        //echo '身分證字號:'.$rs->fields['stud_person_id'].';教育部代碼:'.$rs->fields['school_id'].';學生姓名:'.iconv("BIG5","UTF-8",$rs->fields['stud_name']).'<br/>';a
        $guard = trim(iconv("BIG5", "UTF-8", $aryStudSafeguardKind[$rs->fields['safeguard']]));
        $obtain = trim(iconv("BIG5", "UTF-8", $aryStudObtainKind[$rs->fields['obtain']]));
        $status = trim(iconv("BIG5", "UTF-8", $aryStudyCond[$rs->fields['stud_study_cond']]));
        $results[] = array(
            'curr_seme' => trim($curr_seme),
            'submit_ip' => trim($_SERVER['REMOTE_ADDR']),
            'upload_id' => iconv("BIG5", "UTF-8", $_SESSION['session_tea_name']),
            'stud_person_id' => trim($rs->fields['stud_person_id']),
            'stud_name' => trim(iconv("BIG5", "UTF-8", $rs->fields['stud_name'])),
            'stud_birthday' =>trim($rs->fields['stud_birthday']),
            'stud_grade'=>trim($rs->fields['stud_grade']),
            'obtain' => $obtain ? $obtain : 'NA',
            'safeguard' => $guard ? $guard : 'NA',
            'move_kind' => trim(iconv("BIG5", "UTF-8", $aryStudyCond[$rs->fields['move_kind']])),
            'status' => $status ? $status : 'NA',
            'move_c_date' => trim($rs->fields['move_c_date']),
            'school_id' => $rs->fields['school_id'] ? trim($rs->fields['school_id']) : 'NA',
            'schoolname' => trim(iconv("BIG5", "UTF-8", $rs->fields['schoolname']))
        );
        $rs->moveNext();
    }
    $json = json_encode($results);
    //echo $json;

    echo Security::encrypt($json, $aeskey);
    exit;
}
?>

