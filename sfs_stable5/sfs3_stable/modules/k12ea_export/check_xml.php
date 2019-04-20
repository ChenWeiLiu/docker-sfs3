<?php
include "../../include/config.php";
require "config.php";

//中心端支援
$cookie_sch_id=$_COOKIE['cookie_sch_id'];
if($cookie_sch_id==null){
    $cookie_sch_id= get_session_prot();
}

sfs_check();

// 取得動作
$act=$_POST['act'];

// 動作處理
if ($act=='yes_do_it') {
    header("Content-Type:text/html; charset=utf-8");
  if ($_FILES['xmlfile']['size'] >0 && $_FILES['xmlfile']['name'] != "") {
	$contents= file_get_contents($_FILES['xmlfile']['tmp_name']);
        is_valid_xml($contents);
  }
}else{
    // 叫用 SFS3 的版頭
head("XML TAG檢查");

$tool_bar=make_menu($toxml_menu);
echo $tool_bar;
}

function is_valid_xml ( $xmlstr ) {
    libxml_use_internal_errors( true );

    $doc = simplexml_load_string($xmlstr);
    $xml = explode("\n", $xmlstr); 
    if ($doc === false) {
    $errors = libxml_get_errors();
     
    foreach ($errors as $error) {
        echo display_xml_error($error, $xml);
    }
    }else{
        echo "check ok";
    }
    libxml_clear_errors();
}

function display_xml_error($error, $xml)
{
    $return  = $xml[$error->line - 1] . "\n";
    $return .= str_repeat('-', $error->column) . "^\n";

    switch ($error->level) {
        case LIBXML_ERR_WARNING:
            $return .= "Warning $error->code: ";
            break;
         case LIBXML_ERR_ERROR:
            $return .= "Error $error->code: ";
            break;
        case LIBXML_ERR_FATAL:
            $return .= "Fatal Error $error->code: ";
            break;
    }

    $return .= trim($error->message) .
               "\n  Line: $error->line" .
               "\n  Column: $error->column";

    if ($error->file) {
        $return .= "\n  File: $error->file";
    }

    return "$return\n\n--------------------------------------------\n\n";
}

if ($act!='yes_do_it') {
echo "
<form action =\"{$_SERVER['PHP_SELF']}\" enctype=\"multipart/form-data\" method=post target=_blank>
<table border='1' cellpadding='4' cellspacing='0' bgcolor='#0000FF'><tr>
<td nowrap bgcolor='#FFFFFF' class='small'>
<p>請上傳您欲驗證的XML檔。</p>
檔案：<input type=file name=\"xmlfile\" size=60>
<input type=\"submit\" name=\"submit\" value=\"驗證\">
<input type=\"hidden\" name=\"act\" value=\"yes_do_it\">
</td>
</tr></table>
</form><BR>$error_message";

// SFS3 的版尾
foot();
}


?>