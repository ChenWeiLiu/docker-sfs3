<?php

include "config.php";
sfs_check();

//處理上傳自訂的格式
if($_POST['do_key']=='上傳') {
	//統一上傳目錄
	$upath=$UPLOAD_PATH."12basic_ylc";
	if (!is_dir($upath))  { mkdir($upath) or die($upath."建立失敗！"); }

	//上傳目的地
	$the_file=$upath.'/aspiration.csv';
	copy($_FILES['aspiration']['tmp_name'],$the_file);
	unlink($_FILES['aspiration']['tmp_name']);
}


//秀出網頁
head("使用說明");

//橫向選單標籤
$linkstr="item_id=$item_id";
echo print_menu($MENU_P,$linkstr);

$aspiration_array=get_csv_reference();
foreach($aspiration_array as $key=>$value) {
	$aspiration_data.="<tr><td align='center'>{$value[0]}</td><td>{$value[1]}</td><td>{$value[2]}</td></tr>";
}

$main="<form name='myform' enctype='multipart/form-data' method='post' action='$_SERVER[PHP_SELF]'>";
$aspiration_upload="◎<a href='aspiration_example.csv'>志願序參照檔上傳</a>：<input type=\"file\" name=\"aspiration\"><input type=\"submit\" name=\"do_key\" value=\"上傳\" onclick=\"if(this.form.aspiration.value) { return confirm(\'上傳後會將原上傳檔案替換，您確定要這樣做嗎?\'); } else return false;\">";
$main.="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' id='AutoNumber1' width='50%'>
		<tr align='center' bgcolor='#ffcccc'><td>代碼</td><td>學校科別</td><td>備註</td></tr>
		$aspiration_data</table>$aspiration_upload</form>";



echo $main;

foot();

?>