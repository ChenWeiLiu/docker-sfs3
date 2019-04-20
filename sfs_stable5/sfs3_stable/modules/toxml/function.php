<?php
// $Id: function.php 8928 2016-07-20 18:11:45Z smallduh $

function check_IE () {
	return strstr(getenv("HTTP_USER_AGENT"), 'MSIE')?true:false;
}

function xml_header () {
}

// 驗證學籍 XML 檔
// INPUT: XML 上傳暫存檔
// RETURN: 檢驗結果字串
function validate_xml($tmpfile) {
	$phpver=explode(".",phpversion());
	//PHP5使用函式
	if ($phpver[0]==5) {
		$dom = new DOMDocument();
		$dom->load($tmpfile);
		$mesg=($dom->validate())?"驗證正確！可以進行匯入工作":"剖析XML發生錯誤";
		return $mesg;
	} else {
		//檢查是否有dtd
		if(check_dtd($tmpfile)){
			//初始化剖析器，指定編碼方式為UTF-8
			$xml_parser = xml_parser_create("UTF-8");
			//開啟檔案
			if (!($fp = fopen($tmpfile, "r"))) {
				return $mesg.="無法開啟 $tmpfile ！<br>";
			}
			//讀入檔案，並剖析XML
			while ($data = fread($fp, 4096)) {
				//XML錯誤時處理
				if (!xml_parse($xml_parser, $data, feof($fp))) {
					//錯誤時顯示的訊息
					$mesg.=sprintf("XML Error: %s at line %d",
					xml_error_string(xml_get_error_code($xml_parser)),
					xml_get_current_line_number($xml_parser));
				}
				if($mesg) return $mesg;
			}
			//dom驗證
			if (!$dom = xmldocfile($tmpfile)) {
				return $mesg.="剖析XML發生錯誤<br>";
			} else {
				//進行DTD驗證
				$tmpfile=EscapeShellCmd($tmpfile);
				exec("xmllint --valid --noout $tmpfile 2>&1" , $err );
				if(is_array($err)) $err_str=implode("",$err);
				if($err_str) 
					return iconv("UTF-8","Big5",$err_str);
				else 
					return "驗證正確！可以進行匯入工作";
			} 
			//處理完畢並清除記憶體
			xml_parser_free($xml_parser);
		}else{
			return $mesg="文件中未含有student_call-2_0.dtd資料！";
		}
	}
}

// 檢查上傳XML檔中是否有合法的文件型態宣告
// INPUT: XML 上傳暫存檔
// RETURN: 1 有, 0 沒有
function check_dtd($tmpfile) {
  $hfile=fopen($tmpfile, "r") or trigger_error("開啟 $tmpfile 錯誤，請檢查 $tmpfile 是否有讀取權?", E_USER_ERROR);
  while ($data=fgets($hfile, 1024)) {
    $rs=ereg("\<!DOCTYPE .+ SYSTEM \"student_call\-2_0\.dtd\"\>", $data);
    if ($rs) { fclose($hfile); return 1; }
  }
  fclose($hfile);
  return 0;
}

//big5轉 utf8
function big5_to_utf8($str){
    $str = mb_convert_encoding($str, "UTF-8", "BIG5");

    $i=1;

    while ($i != 0){
        $pattern = '/&#\d+\;/';
        preg_match($pattern, $str, $matches);
        $i = sizeof($matches);
        if ($i !=0){
            $unicode_char = mb_convert_encoding($matches[0], 'UTF-8', 'HTML-ENTITIES');
            $str = preg_replace("/$matches[0]/",$unicode_char,$str);
        } //end if
    } //end wile

    return $str;

}
?>
