
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
    $xcaexchangemsg = $_POST['xcaexchangemsg'] . " -- This is from SFS3 Response!";

    echo Security::encrypt($xcaexchangemsg, $aeskey);
    exit;
}
?>

