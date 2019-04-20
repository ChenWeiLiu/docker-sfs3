<?php

// $Id: school_list.php 5310 2009-01-10 07:57:56Z hami $

include "config.php";

sfs_check();

if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
}

//上傳檔案
$submit=$_POST[submit];
$file = $_FILES['file']['tmp_name'];
$file_name = $_FILES['file']['name'];
if($submit){
    $temp_path = $UPLOAD_PATH."sign_act/";
	$upload_dir = chdir($temp_path);
	if($upload_dir==false)mkdir($temp_path);
	copy($file,$temp_path.$file_name);// 複製檔案
}
//判斷上傳目錄的檔案是否存在
if(file_exists($UPLOAD_PATH."sign_act/".$SCHOOL_LIST)){
	$file_url= $UPLOAD_URL."./sign_act/";
}else{
	$file_url="./";
}

head("上傳學校表列") ;
print_menu($menu_p);

$main = "<table border=1 cellpadding='7' cellspacing='0' bgcolor='#BED2FF' bordercolordark='white' bordercolorlight='black'>
			<tr><form action='$_SERVER[PHP_SELF]'  enctype='multipart/form-data' method='POST'>
				<td valign='top'>檔案來源：<input type='file' name='file'><p>
				<input type='submit' name='submit' value='確定上傳'>
				</td>
				<td valign='top'>檔案上傳說明：<p>
				<li>修改【 <a href=".$file_url.$SCHOOL_LIST.">範例 </a>】檔案，更改檔名以後上傳(不改也可)。</li>
				<li>上傳完畢以後，請至 「/ 系統管理 / 模組權限管理」取回預設值，<br>&nbsp;&nbsp;&nbsp;&nbsp;修改成您的檔案名稱。</li>
				</td>
			</tr></table>";

echo $main ;

foot();

?>