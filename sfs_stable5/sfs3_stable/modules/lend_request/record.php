<?php

//$Id: record.php 6732 2012-03-28 01:54:06Z infodaes $
include "config.php";
sfs_check();
//秀出網頁
head("物品借用紀錄");

$manager_sn=$_REQUEST['manager_sn'];
$status=$_POST['status'];


//橫向選單標籤
$linkstr="manager_sn=$manager_sn";
echo print_menu($MENU_P,$linkstr);

	$main="<table>
<form name='form_item' method='post' action='$_SERVER[PHP_SELF]'>借用來源顯示限定：<select name='manager_sn' onchange='this.form.submit()'><option></option>";

	//取得已借用項目之管理人
	$sql_select="SELECT manager_sn,count(*) as amount FROM equ_record WHERE teacher_sn=$session_tea_sn GROUP BY manager_sn";
	$res=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	while(!$res->EOF) {
		$main.="<option ".($manager_sn==$res->fields['manager_sn']?"selected":"")." value=".$res->fields['manager_sn'].">".$teacher_array[$res->fields['manager_sn']]['title']."-".$teacher_array[$res->fields['manager_sn']]['name']."(".$res->fields['amount'].")</option>";
		$res->MoveNext();
	}
	$main.="</select>　　 狀態：";
	
//類型限定
$status_arr=array(0=>'全部',1=>'已歸還',2=>'未歸還',3=>'逾期未歸');
foreach($status_arr as $key=>$value){
	$main.="<input type='radio' value='$key' name='status' onclick='this.form.submit()'".($status==$key?' checked':'').">$value ";
}
	
$showdata.="<table align=center width=".$m_arr['Table_width']."% border='2' cellpadding='5' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' id='AutoNumber1'>
	<tr bgcolor='$Tr_BGColor'>
		<td align='center'>申請日期</td>
		<td align='center'>申請對象</td>
		<td align='center'>物品編號</td>
		<td align='center'>物品名稱</td>
		<td align='center'>核示日期</td>
		<td align='center'>借出日期</td>
		<td align='center'>借用期限</td>
		<td align='center'>歸還日期</td>
		<td align='center'>歸還登錄</td>
		<td align='center'>附記說明</td>
	</tr>";

//取得申請紀錄
$sql_select="SELECT a.*,TO_DAYS(CURDATE())-TO_DAYS(a.refund_limit) as leftdays,b.item,b.barcode FROM equ_record a,equ_equipments b WHERE a.teacher_sn=$session_tea_sn AND a.equ_serial=b.serial";
if($manager_sn) $sql_select.=" AND a.manager_sn=$manager_sn";

switch ($status) {
case 1:
    $sql_select.=" AND NOT ISNULL(a.refund_date)";
    break;
case 2:
    $sql_select.=" AND ISNULL(a.refund_date)";
    break;
case 3:
    $sql_select.=" AND ISNULL(a.refund_date) AND a.refund_limit<CURDATE()";
    break;
}
//$sql_select.=" ORDER BY ask_date";
$result=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
if($result->recordcount()){
	while(!$result->EOF)
	{
		if($result->fields['refund_date']) { $BGColor=$m_arr['Returned_BGColor']; }
		else {
			if($result->fields['leftdays']>0)  { $BGColor=$m_arr['OverTime_BGColor']; }
				else { $BGColor='#FFFFFF'; }
		} 
		$lend_pic="../../data/lend/pics/".$result->fields['barcode'].".jpg";
		$pic_show=$result->fields['barcode']?"onMouseOver=\"this.style.cursor='hand'; this.style.backgroundColor='#fccfaa';\" onMouseOut=\"this.style.backgroundColor='$BGColor';\" Onclick='receiver=window.open(\"$lend_pic\",\"物品圖片\",\"status=no,toolbar=no,location=no,menubar=no,width=$Pic_Width,height=$Pic_Height\");'":"";
		
		$showdata.="<tr bgcolor='$BGColor'><td align='center'>".$result->fields['ask_date']."</td>
			<td>".$teacher_array[$result->fields['manager_sn']]['title']."-".$teacher_array[$result->fields['manager_sn']]['name']."</td>
			<td align='center' $pic_show>".$result->fields['equ_serial']."</td>
			<td $pic_show>".$result->fields['item']."</td>
			<td align='center'>".$result->fields['allowed_date']."</td>
			<td align='center'>".$result->fields['lend_date']."</td>
			<td align='center'>".$result->fields['refund_limit']."</td>
			<td align='center'>".$result->fields['refund_date']."</td>
			<td align='center'>".$teacher_array[$result->fields['receiver']]['name']."</td>
			<td>".$result->fields['memo']."</td></tr>";
		$result->MoveNext();
	}
}
$showdata.="</form></table>";
echo $main.$showdata;
foot();
?>