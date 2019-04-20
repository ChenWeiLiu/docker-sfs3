<?php

//$Id: message.php 6207 2010-10-05 04:46:46Z infodaes $
include "config.php";
sfs_check();
//秀出網頁
if(!$remove_sfs3head) head("物品借用管理訊息公告");


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


$manager_sn=$_REQUEST['manager_sn'];
$status=$_POST['status'];


if($_POST['BtnSubmit']=='刪除' and $_POST[item_selected]) {
	$item_selected=$_POST[item_selected];
	$ask_items='';
	foreach($item_selected as $value)
	{
		$ask_items.="$value,";
	}
	$ask_items=SUBSTR($ask_items,0,-1);
	$sql="DELETE FROM equ_board WHERE sn IN ($ask_items)";
	$res=$CONN->Execute($sql) or user_error("刪除公告失敗！<br>$sql",256);
}

if($_POST['BtnSubmit']=='新增') {
	$is_add=1;
	//產生訊息人員
	foreach($teacher_array as $key=>$value){
		if(! $value['condition']) $teacher.="<input type='checkbox' name='item_selected[]' value='$key'>".$value['title']."-".$value['name']."<BR>";
	}
	$add_data.="<table align=center width=".$m_arr['Table_width']."% border='2' cellpadding='5' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' id='AutoNumber1'>
	<tr bgcolor='$Tr_BGColor'>
		<td align='center'><input type='checkbox' name='tag' onclick='javascript:tagall(this.checked);'>對象</td>
		<td align='center'>訊息內容</td>
	</tr>
	<tr>
		<td valign='top'>$teacher</td>
		<td valign='top'>
			主旨：<input type='text' name='title' size=52><BR>
			內容：<textarea rows='15' name='content' cols='50'></textarea><BR>
			起始日期：<input type='text' name='start' size=12 value='".date('Y-m-d',time())."'>
			　　公告日數：<input type='text' name='days' size=2 value='".$m_arr['Over_Days']."'><BR><BR>
			<input type='submit' value='發布訊息公告' name='BtnSubmit' onclick='return confirm(\"真的要發布訊息?\")'>
	</td>
	</tr>";
}

if($_POST['BtnSubmit']=='發布訊息公告' and $_POST[item_selected]){
	$title=$_POST['title'];
	$content=$_POST['content'];
	$start=$_POST['start'];
	$days=$_POST['days'];
	
	if($title){
		//抓取$ask_teachers
		$item_selected=$_POST[item_selected];
		foreach($item_selected as $value)
		{
			$ask_teachers.="[$value]";
		}
		$sql="INSERT INTO equ_board SET announce_date='$start',announce_limit=DATE_ADD('$start',INTERVAL $days DAY),manager_sn=$session_tea_sn,title='$title',detail='$content',receiver_sn='$ask_teachers'";
//echo $sql;
//exit;

		$res=$CONN->Execute($sql) or user_error("發送公告失敗！<br>$sql",256);
		$executed='◎ '.date('Y/m/d h:i:s')." 已發送公告-- $title";
	} else $executed="<font color='red'>◎ ".date('Y/m/d h:i:s')." 未設定公告主旨,無法發送!!</font>";
}




//橫向選單標籤
//$linkstr="manager_sn=$manager_sn";
if($_GET['menu']<>'off') echo print_menu($MENU_P);

	$main="<table><form name='myform' method='post' action='$_SERVER[PHP_SELF]'>";
	
//類型限定
$showdata="顯示限定：";
$status_arr=array(0=>'目前顯示的',1=>'預計的',2=>'全部');
foreach($status_arr as $key=>$value){
	$showdata.="<input type='radio' value='$key' name='status' onclick='this.form.submit()'".($status==$key?' checked':'').">$value ";
}

$showdata.="<table align=center width=".$m_arr['Table_width']."% border='2' cellpadding='5' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' id='AutoNumber1'>
	<tr bgcolor='$Tr_BGColor'>
	<td align='center'><input type='checkbox' name='tag' onclick='javascript:tagall(this.checked);'></td>	
	<td align='center'>主旨</td>
	<td align='center'>內容</td>
	<td align='center'>公告日期</td>
	<td align='center'>公告期限</td>
	<td align='center'>對象</td>
	<td align='center'>已簽收</td>
	<td align='center'>未簽收</td>
	</tr>";

//取得公告紀錄
$sql_select="SELECT *,if(CURDATE() BETWEEN announce_date AND announce_limit,0,if(CURDATE()<announce_date,1,2)) AS status FROM equ_board WHERE manager_sn=$session_tea_sn";

switch ($status) {
case 0:
    $sql_select.=" AND (CURDATE() BETWEEN announce_date AND announce_limit)";
    break;
case 1:
    $sql_select.=" AND CURDATE()<announce_date";
    break;
}
$sql_select.=" ORDER BY announce_date";
//echo $sql_select;

$result=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
if($result->recordcount()){
	while(!$result->EOF)
	{
		switch ($result->fields['status']) {
		case 0:
			$BGColor=$m_arr['Cur_BGColor'];
			break;
		case 1:
			$BGColor=$m_arr['Pre_BGColor'];
			break;
		case 1:
			$BGColor=$m_arr['Aft_BGColor'];
			break;
		}
		//將已簽收人員代號轉為姓名
		$received=str_replace('[','',$result->fields['received_sn']);
		$received_sn=explode("]",$received);
		$received='';
		foreach($received_sn as $value){
			if($value) $received.=$teacher_array[$value]['title']."-".$teacher_array[$value]['name']."<BR>";
		}

		//將應簽收人員代號轉為姓名
		$receiver=str_replace('[','',$result->fields['receiver_sn']);
		$receiver_sn=explode("]",$receiver);
		$receiver='';
		foreach($receiver_sn as $value){
			if($value) $receiver.=$teacher_array[$value]['title']."-".$teacher_array[$value]['name']."<BR>";
		}

		//計算未簽收人員
		$not_received_sn=array_diff($receiver_sn,$received_sn);
		$not_received='';
		$receiver=$receiver?$receiver:'全體人員';
		foreach($not_received_sn as $value){
			if($value) $not_received.=$teacher_array[$value]['title']."-".$teacher_array[$value]['name']."<BR>";
		}
		
		
		$showdata.="<tr bgcolor='$BGColor'>
		<td align='center'><input type='checkbox' name='item_selected[]' value='".$result->fields['sn']."'></td>	
		<td>".$result->fields['title']."</td>
		<td>".str_replace("\r\n","<BR>",$result->fields['detail'])."</td>
		<td align='center'>".$result->fields['announce_date']."</td>
		<td align='center'>".$result->fields['announce_limit']."</td>
		<td align='center' OnMouseOver='this.style.cursor=\"images/link.cur\"'><img src='images/receiver.gif' Onclick='receiver=window.open(\"\",\"收訊者\",\"status=no,toolbar=no,location=no,menubar=no,width=200,height=300\");receiver.document.write(\"$receiver\")'></td>
		<td align='center' OnMouseOver='this.style.cursor=\"images/wii.ani\"'><img src='images/received.gif' Onclick='received=window.open(\"\",\"已簽收人員\",\"status=no,toolbar=no,location=no,menubar=no,width=200,height=300\");received.document.write(\"$received\")'></td>
		<td align='center' OnMouseOver='this.style.cursor=\"images/red.ani\"'><img src='images/not_received.gif' Onclick='not_received=window.open(\"\",\"收訊者\",\"status=no,toolbar=no,location=no,menubar=no,width=200,height=300\"); not_received.document.write(\"$not_received\")'></td>
		</tr>";
		$result->MoveNext();
		
		
		/*
		
		<td align='center'>".$receiver."</td>
		<td align='center'>".$received."</td>
		<td align='center'>".$not_received."</td>
		
		
		*/
		
	}
}
$showdata.="<tr><td align='center' colspan=8><input type='submit' value='新增' name='BtnSubmit'><input type='submit' value='刪除' name='BtnSubmit' onclick='return confirm(\"真的要刪除?\")'></td></tr>";

echo $main.($is_add?$add_data:$showdata)."</form></table><BR>$executed";
if(!$remove_sfs3head) foot();
?>