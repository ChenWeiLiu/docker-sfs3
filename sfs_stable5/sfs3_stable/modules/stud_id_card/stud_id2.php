<?php
//$Id: stud_id2.php 5310 2009-01-10 07:57:56Z hami $
include "config.php";

//認證
sfs_check();


//主選單設定
$school_menu_p=(empty($school_menu_p))?array():$school_menu_p;

$main=&main_form();


//秀出網頁布景標頭
head("學生證背面文件");

echo $main;

//佈景結尾
foot();

function &main_form(){
	global $school_menu_p;

    $tool_bar=&make_menu($school_menu_p);
    
	//製作按鈕
	$make_button1="<input type='button' value='下載 PDF 檔' onclick=\"window.location.href='stud_id2.pdf'\" class='b1'>";
	
	$make_button2="<input type='button' value='下載 OpenOffice.org Writer 檔' onclick=\"window.location.href='stud_id2.sxw'\" class='b1'>";

	$main="
	$tool_bar
	<table bgcolor='#c0c0c0' cellspacing=1 cellpadding=8 class='small'>
	<tr bgcolor='#FFFFFF'>
	<td valign=top>
	學生證列印僅製作正面部分，此學生證背面文件提供學校列印或製版需求，亦可自行製作。
	</td>
	</tr>
	<tr class='title_mbody'>
	<td valign=top>
	$make_button1
	$make_button2
	</td>
	</tr>
	</table>
	";
	return $main;
}

?>
