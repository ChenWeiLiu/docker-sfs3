<?php

// $Id: validate_xml.php 5921 2010-03-25 14:59:04Z hami $

// 引入 SFS3 的函式庫
include "../../include/config.php";

// 引入您自己的 config.php 檔
require "config.php";

// 認證
sfs_check();

// 檢查 php.ini 是否打開 file_uploads ?
check_phpini_upload();

// 取得動作
$act=$_POST['act'];

// 動作處理
if ($act=='yes_do_it') {
  if ($_FILES['xmlfile']['size'] >0 && $_FILES['xmlfile']['name'] != "") {
	$dom = new domDocument;
	//$dom->validateOnParse = true;
	$dom->load($_FILES['xmlfile']['tmp_name']);
	if ($dom->validate()){
	   $error_message="恭喜你(妳)~~~XML檔案驗證成功!!";

	} else {

	   $error_message="<font color=red>喔喔~~XML檔案驗證失敗!!</font>";
	}
  }
}

// 叫用 SFS3 的版頭
head("XML交換作業");

$tool_bar=make_menu($toxml_menu);
echo $tool_bar;

echo "
<form action =\"{$_SERVER['PHP_SELF']}\" enctype=\"multipart/form-data\" method=post>
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

?>
