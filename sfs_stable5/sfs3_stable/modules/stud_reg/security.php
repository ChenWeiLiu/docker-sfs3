<?php

class Security {

    public static function encrypt($input, $key) {
        $size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
        $input = iconv('big5', 'utf-8', $input);
        $input = Security::pkcs5_pad($input, $size);
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        mcrypt_generic_init($td, $key, $iv);
        $data = mcrypt_generic($td, $input);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $data = base64_encode($data);
        return $data;
    }

    private static function pkcs5_pad($text, $blocksize) {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }

    private static function pkcs5_unpad($text) {

        $pad = ord($text{strlen($text) - 1});

        if ($pad > strlen($text)) {

            return false;
        }

        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) {

            return false;
        }

        return substr($text, 0, -1 * $pad);
    }

    public static function decrypt($sStr, $sKey) {
        $decrypted = mcrypt_decrypt(
                MCRYPT_RIJNDAEL_128, $sKey, base64_decode($sStr), MCRYPT_MODE_ECB
        );
        $dec_s = strlen($decrypted);
        $padding = ord($decrypted[$dec_s - 1]);
        $decrypted = substr($decrypted, 0, -1 * $padding);
        //echo $decrypted.'<br/>';
        //$ut = trim($decrypted);
        //echo $ut.'<br/>';
        return $decrypted;
        //return Security::pkcs5_unpad($ut);
    }

}

?>
