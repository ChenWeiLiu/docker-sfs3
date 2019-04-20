<?php

//$Id: query.php 6732 2012-03-28 01:54:06Z infodaes $
include "config.php";
sfs_check();
//秀出網頁
head("物品借用紀錄");

$manager_sn=$_REQUEST['manager_sn'];
$status=$_POST['status'];
$nature=$_POST['nature'];
$EditSearch=$_POST['EditSearch'];
if($EditSearch){ $manager_sn=0; $nature=''; }

if($_POST['BtnSubmit']=='提請管理人出借'){
	$item_selected=$_POST[item_selected];
	if($item_selected)
	{
		//print_r($item_selected);
		//進行查詢紀錄轉申請紀錄
		$ask_items='';
		$executed='';
		foreach($item_selected as $value)
		{
			//進行判定是否有人決定時間內已經先預約了
			$item=explode(',',str_replace("^^","",$ask_items));
			$item_serial=$item[0];
			$sql_select="SELECT a.equ_serial,b.item FROM equ_request a,equ_equipments b WHERE equ_serial='$item_serial' AND a.equ_serial=b.sn";

			$res=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
			if($res->recordcount()){
				$executed.="[".$res->fields['equ_serial']."]".$res->fields['item']." 在您送出決定前已經被預約了!!<br>";
			} else {
				$ask_items.="($session_tea_sn,NOW(),$value,'待核'),";
			}
		}
		$ask_items=SUBSTR($ask_items,0,-1).';';
		$ask_items=str_replace("^^","'",$ask_items);
		$sql="INSERT INTO equ_request(teacher_sn,ask_date,equ_serial,manager_sn,status) VALUES $ask_items";
		$res=$CONN->Execute($sql) or user_error("寫入申請紀錄失敗！<br>$sql",256);
	}
}

//橫向選單標籤
$linkstr="manager_sn=$manager_sn";
echo print_menu($MENU_P,$linkstr);


//檢查是否須設定EMAIL才准借用
if($User_Email='Y' and ! $teacher_email) {
	echo "<BR><a href='../teacher_self/teach_connect.php'>您尚未設定好您的電子郵件，<BR>請按此連結於 電子郵件1(公佈) 中設定，方可進行物品借用！</a>";
	foot();
	exit;
	}


//檢查是否有逾期未歸紀錄
if($m_arr['Delay_Refused'])
{
	$sql_select="SELECT CONCAT('[',a.equ_serial,']',b.item,':',a.lend_date,'~',a.refund_limit) as delayitem FROM equ_record a,equ_equipments b WHERE a.teacher_sn=$session_tea_sn AND a.equ_serial=b.serial AND ISNULL(a.refund_date) AND CURDATE()>a.refund_limit";
	$res=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	if($res->recordcount())
	{
		$delay_items=$m_arr['Delay_Refused_announce'].'<ol>';
		while(!$res->EOF) {
			$delay_items.="<li>".$res->fields['delayitem']."</li>";
			$res->MoveNext();
		}
		echo "<BR><font color='red'>$delay_items</font></ol>";
		foot();
		exit;
	}	
}



//取得管理人經管之物品分類
if($manager_sn){
	$nature_select="<select name='nature' onchange='this.form.EditSearch.value=\"\"; this.form.submit()'><option></option>";
	$sql_select="SELECT nature,count(*) as amount FROM equ_equipments WHERE opened='Y' AND manager_sn=$manager_sn GROUP BY nature";
	$res=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	while(!$res->EOF) {
		$nature_select.="<option ".($nature==$res->fields['nature']?"selected":"")." value=".$res->fields['nature'].">".$res->fields['nature']."(".$res->fields['amount'].")</option>";
		$res->MoveNext();
	}
	$nature_select.="</select>";
}

//echo $sql_select;

$main="<table align=center width=".$m_arr['Table_width']."% border='2' cellpadding='5' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' id='AutoNumber1'>";
$main.="<form name='form_query' method='post' action='$_SERVER[PHP_SELF]'>管理者與物品類別限定：<select name='manager_sn' onchange='this.form.EditSearch.value=\"\"; this.form.submit()'><option></option>";

	//取得已借用項目之管理人
	$sql_select="SELECT manager_sn,count(*) as amount FROM equ_equipments WHERE opened='Y' GROUP BY manager_sn";
	$res=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	while(!$res->EOF) {
		$main.="<option ".($manager_sn==$res->fields['manager_sn']?"selected":"")." value=".$res->fields['manager_sn'].">".$teacher_array[$res->fields['manager_sn']]['title']."-".$teacher_array[$res->fields['manager_sn']]['name']."(".$res->fields['amount'].")</option>";
		$res->MoveNext();
	}
	$main.="</select>".($manager_sn?$nature_select:'')."　　 名稱查詢：<input type='text' name='EditSearch' size='10' value='$EditSearch'><input type='submit' value='查詢' name='BtnSubmit'>";
	
if($manager_sn or $EditSearch)
{
	
$showdata.="
	<tr bgcolor='$Tr_BGColor'>
		<td align='center'>管理者</td>
		<td align='center'>物品編號</td>
		<td align='center'>物品名稱</td>
		<td align='center'>財產編號</td>
		<td align='center'>購買日期</td>
		<td align='center'>製造商</td>
		<td align='center'>型號</td>
		<td align='center'>機能</td>
		<td align='center'>借期</td>
		<td align='center'>狀態</td>
	</tr>";	


//取得已預約的紀錄
$Requested_arr=array();
//$sql_select="SELECT * FROM equ_request WHERE ISNULL(memo)";
$sql_select="SELECT * FROM equ_request";  //只要有預約紀錄便不能再預約了
$result=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);	
while(!$result->EOF)
{
	$Requested_arr[$result->fields['equ_serial']]['teacher_sn']=$result->fields['teacher_sn'];
	$Requested_arr[$result->fields['equ_serial']]['ask_date']=$result->fields['ask_date'];
	$Requested_arr[$result->fields['equ_serial']]['status']=$result->fields['status'];
	$result->MoveNext();
}

//echo "<PRE>";
//print_r($Requested_arr);
//echo "</PRE>";

//取得借用未歸紀錄
$NoReturn_arr=array();
$sql_select="SELECT equ_serial,teacher_sn,lend_date,refund_limit,(TO_DAYS(CURDATE())-TO_DAYS(refund_limit)) as leftdays FROM equ_record WHERE ISNULL(refund_date)";
$result=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);	
while(!$result->EOF)
{
	$NoReturn_arr[$result->fields['equ_serial']]['teacher_sn']=$result->fields['teacher_sn'];
	$NoReturn_arr[$result->fields['equ_serial']]['lend_date']=$result->fields['lend_date'];
	$NoReturn_arr[$result->fields['equ_serial']]['refund_limit']=$result->fields['refund_limit'];
	$NoReturn_arr[$result->fields['equ_serial']]['leftdays']=$result->fields['leftdays'];
	$result->MoveNext();
}

//echo "<PRE>";
//print_r($NoReturn_arr);
//echo "</PRE>";
	
//取得物品紀錄
$sql_select="SELECT * FROM equ_equipments WHERE opened='Y'";
if($EditSearch) $sql_select.=" AND item like '%$EditSearch%'"; else 
	if($manager_sn) $sql_select.=" AND manager_sn=$manager_sn AND nature='$nature'";

//$sql_select.=" ORDER BY nature";
$result=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
if($result->recordcount()){
	while(!$result->EOF)
	{
		$status=0;
		$BGColor=$m_arr['Lendable_BGColor'];
		$Alt_Message='';
		//檢查是否已經預約了　　　　　　$Requested_arr[$result->fields['serial']]['teacher_sn']
		if (array_key_exists($result->fields['sn'],$Requested_arr)) {
    			$status=1;
    			$BGColor=$m_arr['Requested_BGColor'];
    			$Alt_Message=$teacher_array[$Requested_arr[$result->fields['sn']]['teacher_sn']]['name'].' '.$Requested_arr[$result->fields['sn']]['ask_date'].'['.$Requested_arr[$result->fields['sn']]['status'].']';
		}

		
		//檢查是否已經外借了
		if (array_key_exists($result->fields['serial'],$NoReturn_arr)) {
    			if($NoReturn_arr[$result->fields['serial']][leftdays]>0)
    			{
				$status=3;
				$BGColor=$m_arr['OverTime_BGColor'];
			} else {
				$status=2;
    				$BGColor=$m_arr['NotReturned_BGColor'];
			}
			$Alt_Message=$teacher_array[$NoReturn_arr[$result->fields['serial']]['teacher_sn']]['name'].' '.$NoReturn_arr[$result->fields['serial']]['refund_limit'].'('.$NoReturn_arr[$result->fields['serial']]['leftdays'].')';
		}
		
		
		$alt=$teacher_array[$NoReturn_arr[$result->fields['serial']]['teacher_sn']]['title'];
		$alt.='-'.$NoReturn_arr[$result->fields['serial']]['refund_limit'];
		$alt.='-'.$NoReturn_arr[$result->fields['serial']]['leftdays'];		
		if($NoReturn_arr[$result->fields['serial']]['leftdays']>0) $Status_gif='out'; else $Status_gif='in';
		
		$lend_pic="../../data/lend/pics/".$result->fields['barcode'].".jpg";
		$pic_show=$result->fields['barcode']?"onMouseOver=\"this.style.cursor='hand'; this.style.backgroundColor='#fccfaa';\" onMouseOut=\"this.style.backgroundColor='$BGColor';\" Onclick='receiver=window.open(\"$lend_pic\",\"物品圖片\",\"status=no,toolbar=no,location=no,menubar=no,width=$Pic_Width,height=$Pic_Height\");'":"";
		
		$showdata.="<tr bgcolor='$BGColor'><td>".$teacher_array[$result->fields['manager_sn']]['title']."-".$teacher_array[$result->fields['manager_sn']]['name']."</td>
			<td $pic_show>".($status?'':"<input type='checkbox' name='item_selected[]' value='^^".$result->fields['sn']."^^,".$result->fields['manager_sn']."'>").$result->fields['serial']."</td>
			<td $pic_show>".$result->fields['item']."</td>
			<td>".$result->fields['asset_no']."</td>
			<td>".$result->fields['sign_date']."</td>
			<td>".$result->fields['maker']."</td>
			<td>".$result->fields['model']."</td>
			<td align='center'>".$result->fields['healthy']."</td>
			<td align='center'>".$result->fields['days_limit']."天</td>
			<td align='center'><img src='images\\$status.gif' alt='$Alt_Message'></td></tr>";
		$result->MoveNext();
	}
	$showdata.="<tr><td colspan=10 align=center bgcolor='$Tr_BGColor'><input type='submit' value='提請管理人出借' name='BtnSubmit' onclick='return confirm(\"真的要提請出借?\")'></td></tr>";
}
}

$showdata.="</table></form><BR>$executed";
echo $main.$showdata;

foot();

?>