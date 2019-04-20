<?php

//$Id: allow.php 6731 2012-03-28 01:50:11Z infodaes $
include "config.php";
sfs_check();

//echo "<PRE>";
//print_r($teacher_array[1]);
//echo "</PRE>";

$teacher_sn=$_REQUEST['teacher_sn'];

if($_POST['BtnSubmit']=='印借用單' and $_POST[item_selected]){
	
	$item_selected=$_POST[item_selected];
	$ask_items='';
	foreach($item_selected as $value)
	{
		$ask_items.="$value,";
	}
	$ask_items=SUBSTR($ask_items,0,-1);
	$sql="SELECT a.*,b.item,b.serial FROM equ_request a,equ_equipments b WHERE (a.equ_serial=b.sn) AND (a.sn IN ($ask_items))";
	$res=$CONN->Execute($sql) or user_error("撤除申請紀錄失敗！<br>$sql",256);
	
	$showdata="<font face='標楷體'><CENTER><H2>$school_area".$school_short_name."物品借用單</H2>";
	
	$showdata.=$teacher_sn?"借用者：".$teacher_array[$teacher_sn]['title']."-".$teacher_array[$teacher_sn]['name']:"";
	$showdata.="　　借用日期：".date("Y/m/d D")."　　管理者：".$teacher_array[$session_tea_sn]['title']."-".$teacher_array[$session_tea_sn]['name'];
	$showdata.="<font face='新細明體'><table align=center width=".$m_arr['Table_width']."% border='2' cellpadding='5' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' id='AutoNumber1'>
		<tr bgcolor='$Tr_BGColor'>
		<td align='center'>NO.</td>
		<td align='center'>借用物品</td>".($teacher_sn?"":"<td align='center'>借出簽名</td>")."
		<td align='center'>歸還日期</td>
		<td align='center'>歸還經收</td>
		<td align='center'>作業條碼</td>
		</tr>";	
	while(!$res->EOF)
	{
		$showdata.="<tr><td align='center'>".($res->CurrentRow()+1)."</td>
		<td align='center'>".$res->fields['serial']."<BR>".$res->fields['item']."</td>
		".($teacher_sn?"":"<td></td>")."
		<td align='center'>　年　月　日</td>
		<td></td><td align='center'><font face='".$m_arr['Barcode_Font']."'>*".$res->fields['equ_serial']."*</font></td></tr>";			
		$res->MoveNext();
	}
	$showdata.="</td></tr></table><BR><BR>".$m_arr['Footer'];
	
	//$filename=$teacher_array[$teacher_sn]['title']."-".$teacher_array[$teacher_sn]['name']."物品借用單.CSV";
	//header("Content-disposition: filename=$filename");
	//header("Content-type: application/octetstream ; Charset=Big5");
	//header("Pragma: no-cache");
	//header("Expires: 0");

	$go="<HTML><HEAD><TITLE>列印借用單</TITLE></HEAD>
		<BODY onLoad='printPage()' onclick='window.location.href=\"$_SERVER[PHP_SELF]\"'>

		<SCRIPT LANGUAGE='JavaScript'>
		function printPage() {
		window.print();
		}
		</SCRIPT>
		$showdata;
		</BODY>
		</HTML>";
	echo $go;
	exit;
}

//秀出網頁
if(!$remove_sfs3head) head("借用申請核撥");

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

//橫向選單標籤
$linkstr="teacher_sn=$teacher_sn";
if($_GET['menu']<>'off') echo print_menu($MENU_P,$linkstr);
$memo=$_POST['memo'];


if($_POST['BtnSubmit']=='撥交準備' and $_POST[item_selected]){
	$item_selected=$_POST[item_selected];
	if($item_selected)
	{
		//print_r($item_selected);
		//核可進程紀錄
		$ask_items='';
		foreach($item_selected as $value)
		{
			$ask_items.="$value,";
		}
		
		$ask_items=SUBSTR($ask_items,0,-1);
		$sql="UPDATE equ_request SET status='".$_POST['BtnSubmit']."',memo='$memo',allowed_date=NOW() where sn IN ($ask_items)";
		$res=$CONN->Execute($sql) or user_error("寫入申請紀錄失敗！<br>$sql",256);
	}
}

if($_POST['BtnSubmit']=='無法出借' and $_POST[item_selected]){
	$item_selected=$_POST[item_selected];
	if($item_selected)
	{
		//print_r($item_selected);
		//進行拒絕外借原因用紀錄
		$ask_items='';
		foreach($item_selected as $value)
		{
			$ask_items.="$value,";
		}
		
		$ask_items=SUBSTR($ask_items,0,-1);
		$sql="UPDATE equ_request SET status='".$_POST['BtnSubmit']."',memo='$memo' where sn IN ($ask_items)";
		$res=$CONN->Execute($sql) or user_error("寫入申請紀錄失敗！<br>$sql",256);
	}
}

if($_POST['BtnSubmit']=='撤除提請' and $_POST[item_selected]){
	$item_selected=$_POST[item_selected];
	if($item_selected)
	{
		//print_r($item_selected);
		//進行查詢紀錄轉申請紀錄
		$ask_items='';
		foreach($item_selected as $value)
		{
			$ask_items.="$value,";
		}
		$ask_items=SUBSTR($ask_items,0,-1);
		$sql="DELETE FROM equ_request WHERE sn IN ($ask_items)";
	
		$res=$CONN->Execute($sql) or user_error("撤除申請紀錄失敗！<br>$sql",256);
	}
}

if($_POST['BtnSubmit']=='列冊撥交' and $_POST[item_selected]){
	//
	$curr_year_seme = sprintf("%03d%d",curr_year(),curr_seme());
	$refund_limit=$_POST['refund_limit'];
	$item_selected=$_POST[item_selected];
	$ask_items='';
	foreach($item_selected as $value)
	{
		$ask_items.="$value,";
	}
	$ask_items=SUBSTR($ask_items,0,-1);
	$sql="SELECT a.*,b.item,b.serial,DATE_ADD(curdate(),INTERVAL b.days_limit DAY) AS refund_limit FROM equ_request a,equ_equipments b WHERE (a.equ_serial=b.sn) AND (a.sn IN ($ask_items))";
	$res=$CONN->Execute($sql) or user_error("'列冊撥交失敗！<br>$sql",256);
	
	//準備移植的sql
	$sql="INSERT INTO equ_record(year_seme,teacher_sn,ask_date,allowed_date,lend_date,equ_serial,refund_limit,memo,manager_sn) VALUES ";
	while(!$res->EOF) {
		$sql.="('$curr_year_seme',".$res->fields['teacher_sn'].",'".$res->fields['ask_date']."','".$res->fields['allowed_date']."',NOW(),'".$res->fields['serial']."','".$res->fields['refund_limit']."','".$res->fields['memo']."',$session_tea_sn),";
		$res->MoveNext();
	}
	$sql=substr($sql,0,-1);
	$res=$CONN->Execute($sql) or user_error("寫入申請紀錄失敗！<br>$sql",256);

	//刪除request裡面的紀錄
	$sql="DELETE FROM equ_request WHERE sn IN ($ask_items)";
	$res=$CONN->Execute($sql) or user_error("已經創建借用紀錄,唯刪除申請紀錄失敗！<br>$sql",256);
}


	$main="<table>
<form name='myform' method='post' action='$_SERVER[PHP_SELF]'>提請的借用者顯示限定：<select name='teacher_sn' onchange='this.form.submit()'><option></option>";

	//取得已申請項目之管理人
	$sql_select="SELECT teacher_sn,count(*) as amount FROM equ_request WHERE manager_sn=$session_tea_sn GROUP BY teacher_sn";
	$res=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	while(!$res->EOF) {
		$main.="<option ".($teacher_sn==$res->fields['teacher_sn']?"selected":"")." value=".$res->fields['teacher_sn'].">".$teacher_array[$res->fields['teacher_sn']]['title']."-".$teacher_array[$res->fields['teacher_sn']]['name']."(".$res->fields['amount'].")</option>";
		$res->MoveNext();
	}
	$main.="</select>";
	
$showdata.="<table align=center width=".$m_arr['Table_width']."% border='2' cellpadding='5' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' id='AutoNumber1'>
	<tr bgcolor='$Tr_BGColor'>
		<td align='center'>申請日期</td>
		<td align='center'>申請者</td>
		<td align='center'>物品編號</td>
		<td align='center'><input type='checkbox' name='tag' onclick='javascript:tagall(this.checked);'>物品名稱</td>
		<td align='center'>位置</td>
		<td align='center'>狀態</td>
		<td align='center'>附記說明</td>
		<td align='center'>核示日期</td>
		
	</tr>";	

//取得申請紀錄
$sql_select="SELECT a.*,b.item,b.position,b.serial FROM equ_request a,equ_equipments b WHERE a.equ_serial=b.sn AND a.manager_sn=$session_tea_sn";
if($manager_sn) $sql_select.=" AND a.teacher_sn=$teacher_sn";
$sql_select.=" ORDER BY teacher_sn,ask_date";

$result=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
if($result->recordcount()){
	while(!$result->EOF)
	{
		$showdata.="<tr align='center'><td>".$result->fields['ask_date']."</td>
			<td>".$teacher_array[$result->fields['teacher_sn']]['title']."-".$teacher_array[$result->fields['teacher_sn']]['name']."</td>
			<td>".$result->fields['serial']."</td>
			<td><input type='checkbox' name='item_selected[]' value=".$result->fields['sn'].">".$result->fields['item']."</td>
			<td>".$result->fields['position']."</td>
			<td>".$result->fields['status']."</td>
			<td>".$result->fields['memo']."</td>
			<td>".$result->fields['allowed_date']."</td></tr>";
		$result->MoveNext();
	}
	$showdata.="<tr bgcolor='$Tr_BGColor'><td align=center><input type='submit' value='撤除提請' name='BtnSubmit' onclick='return confirm(\"真的要撤除?\")'".($m_arr['User_Removable']?'':' disabled')."></td>";
	$showdata.="<td colspan=4 align='center'>附記：<input type='text' name='memo' size='10' value='$memo'>
		 <input type='submit' value='撥交準備' name='BtnSubmit' onclick='return confirm(\"真的要核可?\")'> <input type='submit' value='無法出借' name='BtnSubmit' onclick='return confirm(\"確定不予出借?\")'></td>";
	$showdata.="<td align='center'>
		<input type='submit' name='BtnSubmit' value='印借用單' this.form.submit();\"></td>
		<td colspan=2>歸期：<input type='text' size=10 value='' name='refund_limit'><input type='submit' value='列冊撥交' name='BtnSubmit' onclick='return confirm(\"真的要列冊撥交(產生借用紀錄後刪除申請紀錄)?\")'></td>";
	$showdata.="</tr>";
}

$showdata.="</form></table>";
echo $main.$showdata;
if(!$remove_sfs3head) foot();
?>