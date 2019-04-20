<?php
// $Id: function.php 5310 2009-01-10 07:57:56Z hami $
include_once "config.php";
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

function check_files_list($Path){
global $UPLOAD_URL;
$d = dir($Path);
$html_str="<table border=1 cellspacing=0 cellpadding=2 bordercolorlight=#333354 bordercolordark=#FFFFFF  width=600><TR bgcolor=#B7EBFF><TD width=10%>項次</TD><TD width=80%>建立日期</TD><TD width=10%>動作</TD></TR>";
$f=0;
while (false !== ($entry = $d->read())) {
  if($entry!= '.' && $entry!= '..'){
    $f++;
    //path for javascript download
    $downloadpath="\'".$UPLOAD_URL."eduxcachange/".$entry."\'";
    $day_time=substr($entry,-18,4)."年".substr($entry,-14,2)."月".substr($entry,-12,2)."日".substr($entry,-10,2)."時".substr($entry,-8,2)."分".substr($entry,-6,2)."秒";
    $html_str.="<TR><TD>$f</TD><TD>$day_time</TD><td><input type=button value=下載 onclick=\"go_download($downloadpath)\"></td></TR>";
  } 
}
if($f==0){
    $html_str.="<TR><TD>0</TD><TD>沒有找到任何檔案</TD><TD></TD></TR></table>"; 
    $html_str.="<br><font size=\'7\' color=\'blue\'>請先點選『產生XML檔』</a></font>";
}else{
   $html_str.="</table>";
$html_str.="<br><font size=\'5\' color=\'red\'>XML檔案不需要下載，下載只是提供檢視資料是否有誤</font>";
$html_str.="<br><font size=\'7\' color=\'blue\'>請直接前往<a href=\'upload_edu_xml.php\'><上傳面頁></a></font>"; 
}  
$d->close();
return $html_str;
}

function exist_file_path($Path){
global $UPLOAD_PATH;
$d = dir($Path);
while (false !== ($entry = $d->read())) {
  if($entry!= '.' && $entry!= '..'){
   $filepath=$UPLOAD_PATH."eduxcachange/".$entry;
  } 
}
$d->close();
return $filepath;
}
?>
