<?php

// $Id: lunch.php 5514 2009-06-25 07:41:38Z infodaes $

include "config.php";
$direction=$_GET['direction']?$_GET['direction']:'up';
$scrolldelay=$_GET['scrolldelay']?intval($_GET['scrolldelay']):200;
$scrollamount=$_GET['scrollamount']?intval($_GET['scrollamount']):5;
$width=$_GET['width']?intval($_GET['width']):400;
$height=$_GET['height']?intval($_GET['height']):100;
$bgcolor=$_GET['bgcolor']?$_GET['bgcolor']:'#CCFFCC';
$fontcolor=$_GET['fontcolor']?$_GET['fontcolor']:'#0000FF';
$fontsize=$_GET['fontsize']?intval($_GET['fontsize']):3;


//取得認證項目陣列
$item_array=array();
$sql="select * from authentication_item";
$res_item=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
while(!$res_item->EOF){
	$sn=$res_item->fields[sn];
	$item_array[$sn][code]=$res_item->fields[code];
	$item_array[$sn][title]=$res_item->fields[title];
	$item_array[$sn][nature]=$res_item->fields[nature];
	$item_array[$sn][room_id]=$res_item->fields[room_id];
	
	$res_item->MoveNext();
}

//取得限定日期內認證細目
$sql="select distinct sub_item_sn from authentication_record WHERE CURDATE()-date<={$m_arr[new_day_limit]}";
$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
while(!$res->EOF){
	$sub_item_list.=$res->fields[sub_item_sn].',';
	$res->MoveNext();
}
$sub_item_list=substr($sub_item_list,0,-1);

if($sub_item_list) {

//取得認證細目陣列
$subitem_array=array();
$sql="select * from authentication_subitem where sn in ($sub_item_list)";
$res_subitem=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
while(!$res_subitem->EOF){
	$sn=$res_subitem->fields[sn];
	$subitem_array[$sn][code]=$res_subitem->fields[code];
	$subitem_array[$sn][item_sn]=$res_subitem->fields[item_sn];
	$subitem_array[$sn][title]=$res_subitem->fields[title];
	$subitem_array[$sn][grades]=$res_subitem->fields[grades];
	$subitem_array[$sn][bonus]=$res_subitem->fields[bonus];
	$res_subitem->MoveNext();
}


//取得有效認證項目
$sql="select a.*,b.stud_id,b.stud_name,b.curr_class_num from authentication_record a inner join stud_base b on a.student_sn=b.student_sn WHERE CURDATE()-a.date<={$m_arr[new_day_limit]}";
$res_record=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);

while(!$res_record->EOF){
	$sn=$res_record->fields[sn];
	$subitem_sn=$res_record->fields[sub_item_sn];
	$item_sn=$subitem_array[$subitem_sn][item_sn];
	//將資料加到項目陣列中
	$item_array[$item_sn][record][$subitem_sn].="<li>({$res_record->fields[date]}) {$res_record->fields[curr_class_num]}-{$res_record->fields[stud_name]}</li>";
	$res_record->MoveNext();
}

//印出跑馬燈
$main="<font color='$fontcolor' size=$fontsize><marquee direction=$direction scrolldelay=$scrolldelay scrollamount=$scrollamount behavior=scroll bgcolor=$bgcolor height=$height width=$width><center>";
foreach($item_array as $key=>$item){
	if($item[record]) {
		$main.="<br><font size=4>◎{$item[nature]}-{$item[code]}-{$item[title]}◎</font><br>";
		foreach($item[record] as $subitem_sn=>$record){
			$main.="<font size=3>>> {$subitem_array[$subitem_sn][code]} {$subitem_array[$subitem_sn][title]} <<</font>";
			$main.=$record."<BR>";
		}
	}
}
$main.="</center></marquee></font>";
} else $main="無 {$m_arr[new_day_limit]} 日內的最新認證資訊！";
echo $main;
?>
