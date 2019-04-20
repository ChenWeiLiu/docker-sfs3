<?php
// $Id: module-upgrade.php 7728 2013-10-28 09:02:05Z smallduh $

if(!$CONN){
        echo "go away !!";
        exit;
}

//
// 檢查更新否
// 更新記錄檔路徑
$upgrade_path = "upgrade/".get_store_path($path);
$upgrade_str = set_upload_path("$upgrade_path");
$up_file_name =$upgrade_str."2004-02-26.txt";
//echo get_store_path();
if (!is_file($up_file_name)){
	$query =" ALTER TABLE `book` DROP PRIMARY KEY , ADD PRIMARY KEY ( `book_id` ,`bookch1_id`)";
        if ($CONN->Execute($query)) {
                $temp_query = "修改 book 唯一欄位為book_id,bookch1_id -- by jrh (2004-02-26)\n$query";
                $fp = fopen ($up_file_name, "w");
                fwrite($fp,$temp_query);
                fclose ($fd);
        }
}

$up_file_name =$upgrade_str."2005-12-02.txt";
if (!is_file($up_file_name)){
	$qy[0] ="ALTER TABLE `book` CHANGE `book_name` `book_name` varchar(100) DEFAULT NULL;";
	$file_str[0] = "修改 book_name 的長度為100個字元";
	$qy[1] ="ALTER TABLE `book` CHANGE `book_author` `book_author` varchar(50) DEFAULT NULL;";
	$file_str[1] = "修改 book_author 的長度為50個字元";
	$qy[2] ="ALTER TABLE `book` CHANGE `book_maker` `book_maker` varchar(50) DEFAULT NULL;";
	$file_str[2] = "修改 book_maker 的長度為50個字元";
	$qy[3] ="ALTER TABLE `book` CHANGE `book_myear` `book_myear` varchar(10) DEFAULT NULL;";
	$file_str[3] = "修改 book_myear 的長度為10個字元";
	$qy[4] ="ALTER TABLE `book` CHANGE `book_isbn` `book_isbn` varchar(13) DEFAULT NULL;";
	$file_str[4] = "修改 book_isbn 的長度為13個字元";
	reset($qy);
	while(list($k,$v)=each($qy)) {
		$temp_str.=$file_str[$k]." -- by brucelyc (2005-12-02)\n$v \n";
		if ($CONN->Execute($v))
			$temp_str.="更新成功 \n";
		else
			$temp_str.="更新失敗 \n";
	}
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

$up_file_name =$upgrade_str."2005-12-12.txt";
if (!is_file($up_file_name)){
	$qy[0] ="ALTER TABLE `book` CHANGE `bookch1_id` `bookch1_id` varchar(3) DEFAULT NULL;";
	$file_str[0] = "修改 bookch1_id 的長度為3個字元";
	$qy[1] ="ALTER TABLE `book` ADD `ISBN` varchar(17) DEFAULT NULL;";
	$file_str[1] = "增加 ISBN 長度為17個字元";
	$qy[2] ="ALTER TABLE `book` ADD `book_sprice` varchar(10) DEFAULT NULL;";
	$file_str[2] = "增加 book_sprice 長度為10個字元";
	$qy[3] ="ALTER TABLE `book` DROP `book_no`;";
	$file_str[3] = "刪除 book_no 欄位";
	$qy[4] ="ALTER TABLE `book` CHANGE `book_dollar` `book_dollar` varchar(8) DEFAULT NULL;";
	$file_str[4] = "修改 book_dollar 的長度為8個字元";
	$qy[5] ="ALTER TABLE `book` CHANGE `book_price` `book_price` int(11) DEFAULT NULL;";
	$file_str[5] = "修改 book_price 的長度為11個位數";
	reset($qy);
	while(list($k,$v)=each($qy)) {
		$temp_str.=$file_str[$k]." -- by brucelyc (2005-12-12)\n$v \n";
		if ($CONN->Execute($v))
			$temp_str.="更新成功 \n";
		else
			$temp_str.="更新失敗 \n";
	}
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}

$up_file_name =$upgrade_str."2005-12-15.txt";
if (!is_file($up_file_name)){
	$qy[0] ="ALTER TABLE `book` ADD `create_time` datetime not NULL DEFAULT '0000-00-00 00:00:00';";
	$file_str[0] = "增加 create_time ";
	$qy[1] ="ALTER TABLE `book` ADD `update_time` datetime not NULL DEFAULT '0000-00-00 00:00:00';";
	$file_str[1] = "增加 update_time ";
	reset($qy);
	while(list($k,$v)=each($qy)) {
		$temp_str.=$file_str[$k]." -- by brucelyc (2005-12-15)\n$v \n";
		if ($CONN->Execute($v))
			$temp_str.="更新成功 \n";
		else
			$temp_str.="更新失敗 \n";
	}
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
}
?>