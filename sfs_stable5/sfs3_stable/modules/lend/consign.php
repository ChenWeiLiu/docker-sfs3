<?php

//$Id: consign.php 7708 2013-10-23 12:19:00Z smallduh $
include "config.php";
sfs_check();

$item_selected=$_POST[item_selected];
$teacher_sn=$_POST['teacher_sn'];

if($_POST['BtnSubmit']=='物品清冊' and $item_selected){
	
	$ask_items='';
	foreach($item_selected as $value)
	{
		$ask_items.="'$value',";
	}
	$ask_items=SUBSTR($ask_items,0,-1);
	
	$sql="SELECT * FROM equ_equipments WHERE manager_sn=$session_tea_sn AND nature IN ($ask_items) ORDER BY nature,serial";
	$res=$CONN->Execute($sql) or user_error("選取物品紀錄失敗！<br>$sql",256);
	
	$CSV='"物品編號","國際條碼號","物品名稱","財產編號","分類","位置","製造商","型號","機能","外借","借期","購買日期","購買金額","經銷商","保固期限","風險評估","使用年限","報廢日期","報廢原因"'."\r\n";
	while(!$res->EOF) {
		$CSV.='"'.$res->fields['serial'].'",';
		$CSV.='"'.$res->fields['barcode'].'",';
		$CSV.='"'.$res->fields['item'].'",';
		$CSV.='"'.$res->fields['asset_no'].'",';
		$CSV.='"'.$res->fields['nature'].'",';
		$CSV.='"'.$res->fields['position'].'",';
		$CSV.='"'.$res->fields['maker'].'",';
		$CSV.='"'.$res->fields['model'].'",';
		$CSV.='"'.$res->fields['healthy'].'",';
		$CSV.='"'.$res->fields['opened'].'",';
		$CSV.='"'.$res->fields['days_limit'].'",';
		$CSV.='"'.$res->fields['sign_date'].'",';
		$CSV.='"'.$res->fields['cost'].'",';
		$CSV.='"'.$res->fields['saler'].'",';
		$CSV.='"'.$res->fields['warranty'].'",';
		$CSV.='"'.$res->fields['importance'].'",';
		$CSV.='"'.$res->fields['usage_years'].'",';
		$CSV.='"'.$res->fields['crash_date'].'",';
		$CSV.='"'.$res->fields['crashed_reason'].'"';
		
		$CSV.="\r\n";
		
		
		$res->MoveNext();
	}
	$filename=$teacher_array[$session_tea_sn]['title']."-".$teacher_array[$session_tea_sn]['name']."經管物品移交清冊.CSV";
	header("Content-disposition: filename=$filename");
	header("Content-type: application/octetstream ; Charset=Big5");
	//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
	header("Expires: 0");
	echo $CSV;
	exit;


}




//秀出網頁
if(!$remove_sfs3head) head("管理物品移交");

echo <<<HERE
<script>
function tagall(status) {
  var i =0;

  while (i < document.myform.elements.length)  {
    if (document.myform.elements[i].name=='item_selected[]') {
      document.myform.elements[i].checked=status;
    }
    i++;
  }
}
</script>
HERE;


if($_POST['BtnSubmit']=='移交' and $item_selected and $teacher_sn){
	
	$ask_items='';
	foreach($item_selected as $value)
	{
		$ask_items.="'$value',";
	}
	$ask_items=SUBSTR($ask_items,0,-1);
	
	$sql="UPDATE equ_equipments SET manager_sn=$teacher_sn WHERE manager_sn=$session_tea_sn AND nature IN ($ask_items)";
	$res=$CONN->Execute($sql) or user_error("新增物品紀錄失敗！<br>$sql",256);

	$executed='◎ '.date('Y/m/d h:i:s')." 已移交物品類別[ $ask_items ]至指定人員 ".$teacher_array[$teacher_sn]['title']."-".$teacher_array[$teacher_sn]['name'];
}




//橫向選單標籤
//$linkstr="manager_sn=$manager_sn";
if($_GET['menu']<>'off') echo print_menu($MENU_P,$linkstr);

$cols=$m_arr['Cols'];

$main="<table border='2' cellpadding='5' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' id='AutoNumber1'>
	<form name='myform' method='post' action='$_SERVER[PHP_SELF]'>
	<tr><td align='center' colspan=$cols bgcolor='$Tr_BGColor'><input type='checkbox' name='tag' onclick='javascript:tagall(this.checked);'>移交管理的物品類別</td></tr>";

	//取得物品分類
	$sql_select="SELECT nature,count(*) as amount FROM equ_equipments WHERE manager_sn=$session_tea_sn GROUP BY nature";
	$res=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	
	//echo $sql_select;
	
	while(!$res->EOF) {
		$mod=($res->currentrow()) % $cols;
		$nature=$res->fields['nature'];
		$amount=$res->fields['amount'];
		if($mod==0) { $main.="<tr>"; }
		$main.="<td><input type='checkbox' name='item_selected[]' value='$nature'>$nature ($amount)</td>";
		if($mod==($cols-1)) { $main.="</tr>"; }
		$res->MoveNext();
	}
	//產生在職人員清單
	$teacher_select="<select name='teacher_sn'><option></option>";
	foreach($teacher_array as $key=>$value){
		if(!$value['condition']) {
			$teacher_select.="<option value='$key'>".$value['title']."-".$value['name']."</option>";
		}
	}
	$teacher_select.="</select>";
	
	$main.=(!($mod==$cols-1)?"</tr>":"")."
		<tr>
		<td align='center' colspan=$cols bgcolor='$Tr_BGColor'>
		新任管理人：$teacher_select
		<input type='submit' value='物品清冊' name='BtnSubmit'>
		<input type='submit' value='移交' name='BtnSubmit' onclick='return confirm(\"真的要移交?\")'>
		</td>
		</tr>
		</table></form><BR>$executed";

echo $main;

if(!$remove_sfs3head) foot();

?>