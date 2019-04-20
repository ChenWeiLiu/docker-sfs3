<?php

// $Id: output_xml.php 7270 2013-04-22 02:32:24Z infodaes $

if (!$_POST['sid']) {
    exit;
}

session_id($_POST['sid']);
session_start();

require "config.php";
require "class.php";

require_once 'Crypt/DiffieHellman.php';
require_once('Crypt/CBC.php');
//include 'security.php';
sfs_check();
header("Content-type: text/html; charset=utf-8");
if ($_POST['getkey'] == 'true') {

    $alice = new Crypt_DiffieHellman($_POST['serverp'], $_POST['serverg']);

    $alice_pubKey = $alice->generateKeys()->getPublicKey(Crypt_DiffieHellman::BINARY);

    $_SESSION['alicepk'] = $alice_pubKey;

    $alice_computeKey = $alice->computeSecretKey(base64_decode($_POST['serverpk']), Crypt_DiffieHellman::BINARY)->getSharedSecretKey(Crypt_DiffieHellman::BINARY);

    $_SESSION['alicesk'] = $alice_computeKey;


    echo base64_encode($_SESSION['alicepk']);
} else {

    $key = base64_encode($_SESSION['alicesk']);

    $aeskey = hash('md5', base64_encode($key));

    if ($_POST['encdata']) {
        $ciphertext = $_POST['encdata'];
        $ciphertext_dec = base64_decode($ciphertext);
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        $iv_dec = substr($ciphertext_dec, 0, $iv_size);
        $ciphertext_dec = substr($ciphertext_dec, $iv_size);
        $plaintext_utf8_dec = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $aeskey, $ciphertext_dec, MCRYPT_MODE_CBC, $iv_dec);
        echo base64_decode($plaintext_utf8_dec);
    }
    exit;
}
?>
