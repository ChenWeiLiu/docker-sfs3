<?php

//專門處理unicode to utf8函式
//from http://www.ps3w.net/modules/psbb/?op=openthr&lead=1101

function utf8conv2charset($utf8str, $charset = 'BIG5') {
    mb_regex_encoding($charset); // 宣告 要進行 regex 的多位元編碼轉換格式 為 $charset
    mb_substitute_character('long'); // 宣告 缺碼字改以U+16進位碼為標記取代
    $utf8str = mb_convert_encoding($utf8str, $charset, 'UTF-8');
    $utf8str = preg_replace('/U\+([0-9A-F]{4})/e', '"&#".intval("\\1",16).";"', $utf8str); // 將U+16進位碼標記轉換為UnicodeHTML碼
    return $utf8str;
}

function unicod2utf8byChrCode($chrCode) {
    if (!is_integer($chrCode))
        return $chrCode;
    elseif ($chrCode < 0x80) { // 單一字元 [0xxxxxxx]
        return chr($chrCode);
    } elseif ($chrCode >= 0x80 && $chrCode <= 0x07ff) {        // 雙字元 [110xxxxx][10xxxxxx]
        $bin = sprintf('%011s', decbin($chrCode));
        $chrs = chr(intVal('110' . substr($bin, 0, 5), 2));
        $chrs.= chr(intVal('10' . substr($bin, 5), 2));
    } elseif ($chrCode >= 0x800 && $chrCode <= 0xFFFF) {        // 三字元 [1110xxxx][10xxxxxx][10xxxxxx]
        $bin = sprintf('%016s', decbin($chrCode));
        $chrs = chr(intVal('1110' . substr($bin, 0, 4), 2));
        $chrs.= chr(intVal('10' . substr($bin, 4, 6), 2));
        $chrs.= chr(intVal('10' . substr($bin, 10), 2));
    } elseif ($chrCode >= 0x10000 && $chrCode <= 0x1FFFFF) {     // 四字元 [11110xxx][10xxxxxx][10xxxxxx][10xxxxxx]
        $bin = sprintf('%021s', decbin($chrCode));
        $chrs = chr(intVal('11110' . substr($bin, 0, 3), 2));
        $chrs.= chr(intVal('10' . substr($bin, 3, 6), 2));
        $chrs.= chr(intVal('10' . substr($bin, 9, 6), 2));
        $chrs.= chr(intVal('10' . substr($bin, 15), 2));
    } elseif ($chrCode >= 0x200000 && $chrCode <= 0x3FFFFFF) {    // 五字元 [111110xx][10xxxxxx][10xxxxxx][10xxxxxx][10xxxxxx]
        $bin = sprintf('%026s', decbin($chrCode));
        $chrs = chr(intVal('111110' . substr($bin, 0, 2), 2));
        $chrs.= chr(intVal('10' . substr($bin, 2, 6), 2));
        $chrs.= chr(intVal('10' . substr($bin, 8, 6), 2));
        $chrs.= chr(intVal('10' . substr($bin, 14, 6), 2));
        $chrs.= chr(intVal('10' . substr($bin, 20), 2));
    } elseif ($chrCode >= 0x4000000 && $chrCode <= 0x7FFFFFFF) {    // 六字元 [1111110x][10xxxxxx][10xxxxxx][10xxxxxx][10xxxxxx][10xxxxxx]
        $bin = sprintf('%031s', decbin($chrCode));
        $chrs = chr(intVal('1111110' . substr($bin, 0, 1), 2));
        $chrs.= chr(intVal('10' . substr($bin, 1, 6), 2));
        $chrs.= chr(intVal('10' . substr($bin, 7, 6), 2));
        $chrs.= chr(intVal('10' . substr($bin, 13, 6), 2));
        $chrs.= chr(intVal('10' . substr($bin, 19, 6), 2));
        $chrs.= chr(intVal('10' . substr($bin, 25), 2));
    } else { // 錯誤處理
        return "{[U?$chrCode]}";
    }
    return $chrs;
}

function unicodeHTMLconv2utf8($utf8strWithUnicodeHTMLstr, $suffix_semicolon_included = false) {
    $qms = $suffix_semicolon_included ? '' : '?';
    $pat[] = '/&#([0-9]+);' . $qms . '/e';
    $rep[] = "unicod2utf8byChrCode(\\1)";   // &#(10進制);
    $pat[] = '/&#(x[0-9A-Fa-f]+);/e';
    $rep[] = "unicod2utf8byChrCode(0\\1)";  // &#x(16進制);
    return preg_replace($pat, $rep, $utf8strWithUnicodeHTMLstr);
}

?>
