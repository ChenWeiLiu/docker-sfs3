<?php

// $Id: readme.php 5588 2009-08-16 17:13:02Z infodaes $

// 引入 SFS3 的函式庫
include "../../include/config.php";

// 引入您自己的 config.php 檔
require "config.php";

// 取得動作
$act=$_GET['act'];

// 動作處理
if ($act=='stu_dtd') { 
	header("Content-type: text/html; Charset=utf8");
	header("Location: {$SFS_PATH_HTML}include/xml/student_call-2_0.dtd"); 
	exit;
} elseif ($act=='stu_xml') {
	header("Content-type: text/html; Charset=utf8");
	header("Location: {$SFS_PATH_HTML}include/xml/student_call-2_0.xml"); 
	exit;
}

// 叫用 SFS3 的版頭
head("XML交換作業");

// 認證
//sfs_check();

//
// 您的程式碼由此開始

// 選單
$tool_bar=make_menu($toxml_menu);
echo $tool_bar;

// 秀出說明
echo <<<HERE
		<form method="post" action="reload.php" name="login_form">
		<input type="hidden" name="login_attempt" value="1">
		<input type="hidden" name="CustomerID" value="">
		
		<table cellspacing="2" cellpadding="2" align="right">

		<tr>
			<td	align="right" class="global-login-text" colspan="2" style="font-size:10px">
				<span style="color:red;font-face:verdana"></span>
			</td>
		</tr>
		
		<tr>
			<td rowspan="10" valign="top" align="left" width="80%">
			
			
			
			
			</td>
			<td	align="right" class="global-login-text" style="font-size:10px" nowrap>機關代碼: </td>
			<td width="1%"><input class="global-sub-nav-input" type="text" name="CompanyName"	value="" size="23"></td>
		</tr>
		
		<tr>
			<td	align="right" class="global-login-text" style="font-size:10px">帳　　號: </td>
			<td><input class="global-sub-nav-input" type="text" name="name" id="name" value="" size="23"></td>
		</tr>
		
		<tr>
			<td	align="right" class="global-login-text" style="font-size:10px">密　　碼: </td>
			<td><input class="global-sub-nav-input" type="password" name="password" value=""	size="23"></td>
		</tr>
		
			
		<tr>
			<td colspan="2"	align="right">
				<input class="boldbutton" type="submit" value="Logon" name="Logon">
				<input class="boldbutton" type="reset" value="Reset" name="Reset">
			</td>
		</tr>
		<tr>
			<td align="right" colspan=2 class="global-login-text" style="font-size:10px">
			<br><br><Br><br><br>
			
			</td>
		</tr>
		</table>
		</form>

HERE;


// SFS3 的版尾
foot();

?>
