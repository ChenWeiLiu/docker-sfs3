<?php

// $Id: barcode.php 6368 2011-03-01 01:53:02Z infodaes $



include "config.php";

sfs_check();



//秀出網頁

head("收費管理");

$linkstr="item_id=$item_id";

echo print_menu($MENU_P,$linkstr);

$barcode=str_replace("*","",$_POST[barcode]);
$paid_date=$_POST[paid_date];

if($barcode AND $_POST['act']=='解析處理'){

	$barcode=explode("\r\n",$barcode);

	//print_r($barcode);

	$excuted="<BR>※ 前次解析並登錄之條碼如下～<BR><BR>";

	$counter=0;

	foreach($barcode as $value){

		//print_r($value);

		//echo "<BR>";

		if($value){

			$data_arr=explode("-",$value);

			//echo $data_arr[0]."=".$data_arr[1]."==".$data_arr[2]."<BR>";

			$sql_select="update charge_record set dollars=".$data_arr[2].",paid_date='$paid_date',comment='條碼掃描登錄' where item_id=".$data_arr[0]." AND record_id='".$data_arr[1]."'";

			

			//echo $sql_select."<BR><BR><BR>";

			$counter++;

			$excuted.="　▲ ($counter) $value<BR>";

			$res=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);

		}

	}  

}

//橫向選單標籤



$main="<table><form name='my_form' method='post' action='$_SERVER[PHP_SELF]'><tr><td>▲繳費日期：<input type='text' size=10 value='".date('Y-m-d',time())."' name='paid_date'><BR>▲請掃描收費單條碼：<BR><textarea rows='22' name='barcode' cols=30></textarea>

<BR><input type='submit' value='解析處理' name='act'><input type='reset' value='清空重掃'></td><td valign='top'>$excuted</td></tr></form></table>";

echo $main;

foot();

?>