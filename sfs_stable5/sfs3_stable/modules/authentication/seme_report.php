<?php
// $Id: list.php 5310 2009-01-10 07:57:56Z hami $

include "config.php";
sfs_check();

$item_sn_array=$_POST[item_sn];

if($item_sn_array AND $_POST['act']=='統計列印') {
	//取出項目資料
	$item_count=count($item_sn_array);
	foreach($item_sn_array as $key=>$item_sn){
		//列出項目
		$sql_select="select * from authentication_item where sn=$item_sn";
		$res=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
		$showdata="<br><font size=5>(NO.".($key+1).") #{$res->fields[sn]} {$res->fields[title]}</font>";
		$showdata.="<table border=2 cellpadding=6 cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' width='100%'>
					<tr bgcolor='#CCFF99'>
					<td align='center' >管理處室</td>
					<td align='center'>開列學期</td>
					<td align='center'>類別</td>
					<td align='center'>項目碼</td>
					<td align='center'>認證期間</td>					
					<td align='center'>開列者</td>
					</tr>";
		$showdata.="<tr bgcolor=$item_color><td align='center'>{$room_kind_array[($res->fields[room_id])]}</td>
					<td align='center'>{$res->fields[year_seme]}</td>
					<td align='center'>{$res->fields[nature]}</td>		
					<td align='center'>{$res->fields[code]}</td>
					<td align='center'>{$res->fields[start_date]}~{$res->fields[end_date]}</td>
					<td align='center'>{$teacher_array[($res->fields[creater])]}</td></tr></table>";
					
		//顯示細目與統計資訊
		$showdata.="<br><table border='1' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' width='100%'>
					<tr bgcolor='#AACCFF'>
					<td align='center'>NO</td>
					<td align='center'>細目碼</td>
					<td align='center'>細目名稱</td>	
					<td align='center'>適用年級</td>
					<td align='center'>得點數</td>
					<td align='center'>學期認證情形</td>
					<td align='center'>備註</td>
					</tr>";
		
		$sql="select * from authentication_subitem where item_sn=$item_sn order by code";
		$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
		while(!$res->EOF) {
			$subitem_sn=$res->fields[sn];
			//統計學期認證人數
			$semester_info='';
			$sql_count="select year_seme,count(*) as counter,avg(score) as score from authentication_record where sub_item_sn=$subitem_sn group by year_seme";
			$res_count=$CONN->Execute($sql_count) or user_error("統計失敗！<br>$sql_count",256);
			while(!$res_count->EOF) {

				$semester_info.="<li>[{$res_count->fields[year_seme]}]認證 ".sprintf("%5d",$res_count->fields[counter])."人，平均分數 {$res_count->fields[score]}。</li>";
				$res_count->MoveNext();
			}
			$showdata.="<tr bgcolor='#FFFFFF'><td align='center'>".($res->CurrentRow()+1)."</td>
						<td align='center'>{$res->fields[code]}</td>
						<td>{$res->fields[title]}</td>
						<td align='center'>{$res->fields[grades]}</td>
						<td align='center'>{$res->fields[bonus]}</td>
						<td align='center'>$semester_info</td>
						<td align='center'></td>
						</tr>";
			$res->MoveNext();
		}
		$showdata.="</table>";
				
		//換頁
		if($_POST[new_page]) {
			$key++;
			if($key<$item_count) $showdata.="<P style='page-break-after:always'></P>"; else $showdata.="<br>";
		}
		echo $showdata;	
	}
	exit; 
}

//秀出網頁
head("學期統計");

echo <<<HERE
<script>
function tagall(status) {
  var i =0;

  while (i < document.myform.elements.length)  {
    if (document.myform.elements[i].name=='item_sn[]') {
      document.myform.elements[i].checked=status;
    }
    i++;
  }
}
</script>
HERE;

//橫向選單標籤
echo print_menu($MENU_P);

//取得認證中項目的下拉選單
$main="<form name='myform' method='post' action='$_SERVER[SCRIPT_NAME]' target='_BLANK'>";
$sql_select="select * from authentication_item WHERE CURDATE() BETWEEN start_date AND end_date order by room_id,code";
$res=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);

$col=3; //設定每一列顯示幾人

$main.="<table border='1' cellpadding='3' cellspacing='0' style='border-collapse: collapse;' bordercolor='#111111'>";
while(!$res->EOF) {
	if($res->currentrow() % $col==0) $main.="<tr bgcolor='#FFCCFF'>";
	$main.="<td><input type='checkbox' value='{$res->fields[sn]}' name='item_sn[]'>[{$room_kind_array[($res->fields[room_id])]}]-{$res->fields[nature]}-{$res->fields[code]}-{$res->fields[title]}</td>";
	if($res->currentrow() % $col==($col-1) or $res->EOF) $main.="</tr>";
	$res->MoveNext();
}
$main.="<tr><td colspan=$col align='center'><input type='button' name='all_item' value='全選' onClick='javascript:tagall(1);'><input type='button' name='clear_item'  value='全不選' onClick='javascript:tagall(0);'> 
		　　　<input type='checkbox' name='new_page' checked value=1>自動跳頁 <input type='submit' value='統計列印' name='act'></td></tr></table>";

echo $main."</form>";
foot();
?>