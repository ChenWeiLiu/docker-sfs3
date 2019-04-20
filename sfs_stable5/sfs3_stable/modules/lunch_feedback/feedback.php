<?php

// $Id: feedback.php 6715 2012-03-07 03:04:04Z infodaes $
include "config.php";

sfs_check();

$sign_date=$_POST[sign_date];
if(! $sign_date) $sign_date=date("Y-m-d");

$pDesign_pN=$_POST['pDesign_pN'];


//假使按了傳送紐
if($_POST['act']=='傳送'){
	//把原來的清空
	$sql="delete from lunch_feedback where pDate='$sign_date' and class_id='$class_id'";
	$res=$CONN->Execute($sql) or user_error("刪除失敗！<br>$sql",256);
	for($i=0;$i<=count($_POST[quantity]);$i++)
	{
		if($_POST[item][$i])
		$sql_values.="('$class_id','$sign_date','".$_POST[item][$i]."','".$_POST[quantity][$i]."','".$_POST[taste][$i]."','".$_POST[hygiene][$i]."','".$_POST[memo][$i]."',".$session_tea_sn.",'".date("Y-m-d")."'),";
	}
	$sql_values=substr($sql_values,0,-1);
	$sql="INSERT INTO lunch_feedback(class_id,pdate,item,quantity,taste,hygiene,memo,teacher_sn,update_date) VALUES $sql_values";
	$res=$CONN->Execute($sql) or user_error("新增失敗！<br>$sql",256);
	echo "<script language=\"Javascript\"> alert(\"已於[ ".date("Y/m/d H:i:s")." ]填報完畢！午餐中心感謝您的意見回饋\")</script>";
};


//秀出網頁
head("午餐意見調查");

//橫向選單標籤
echo print_menu($MENU_P);

if($class_id)
{
	
	//抓取已填報記錄日期
	$sql="select pDate,record_id from lunch_feedback where class_id='$class_id' and (TO_DAYS(curdate())-TO_DAYS(pDate) between 0 AND $period)";
	$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
	while(!$res->EOF){
		$pDate=$res->fields[pDate];
		$pDate_array[$pDate]=$res->fields[record_id];
		$res->MoveNext();	
	}

	//抓取最近天數
	$period=$m_arr[period];
	$sql="select distinct pDate from lunchtb where TO_DAYS(curdate())-TO_DAYS(pDate) between 0 AND $period ORDER BY pDate DESC";
	$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
	$date_select="<select name='sign_date' onchange='this.form.submit()'><option value='$sign_date'>$sign_date</option>";
	while(!$res->EOF){
		$pDate=$res->fields[pDate];
		$selected=($pDate==$sign_date)?'selected':'';
		if($pDate_array[$pDate]) $bgcolor='#ffaaff'; else $bgcolor='#ccffcc';		
		$date_select.="<option value='$pDate' $selected STYLE='background-color: $bgcolor;'>$pDate</option>";
		$res->MoveNext();	
	}
	$date_select.="</select>";

	//學期別
	$work_year_seme = sprintf("%03d%d",curr_year(),curr_seme());
	// 取出班級名稱陣列
	$class_base=class_base($work_year_seme);
	$tr_bgcolor='#CCFF99';	
	if($is_admin) {
		$class_str="<select name='class_id' onchange='this.form.submit()'>";
		while(list($cid,$v)=each($class_id_arr)) {
			$selected=($_POST[class_id]==$cid)?"selected":"";
			//檢查是否已經有填報
			$sql="select record_id from lunch_feedback where class_id='$cid' and pdate='$sign_date'";
			$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
			if($res->fields[record_id])	$bgcolor='#ffaaff'; else $bgcolor='#ccffcc';
			$class_str.="<option value='$cid' $selected STYLE='background-color: $bgcolor;'>$v</option>";
		}
		$class_str.="</select>";
	} else {
		$class_str=$class_base[$class_id];
	}
	
	if($sign_date)
	{
		$sql="select * from lunchtb where pDate='$sign_date'";
		$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
		$pDesign_radio='';
		$pDesign_count=$res->recordcount();
		while(!$res->EOF){
			$pN=$res->fields[pN];
			$pDesign=$res->fields['pDesign'];
			$checked=($pN==$pDesign_pN)?'checked':'';
			if($pDesign_count==1) { $checked='checked'; $pDesign_pN=$pN; }
			$pDesign_radio.="<input type='radio' name='pDesign_pN' value=$pN $checked onclick='this.form.submit();'>$pDesign ";

			//產生菜單陣列
			//主食
			$pFood=$res->fields['pFood'];
			$feedback_menu[$pN][$pFood]=array();
			//副食
			$pMenu_array=explode("\r\n",$res->fields[pMenu]);
			foreach($pMenu_array as $menu)
				$feedback_menu[$pN][$menu]=array();
			//水果
			$pFruit=$res->fields['pFruit'];
			$feedback_menu[$pN][$pFruit]=array();
			
			$res->MoveNext();
		}		
	}
		
	$main="<table><form name='form_day' method='post' action='$_SERVER[SCRIPT_NAME]'>
		※填報人:$session_tea_name 　※用餐日期:$date_select 　※班級:$class_str 　※供應廠商:$pDesign_radio";
	$showdata="$last_update<table border='1' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' width='100%'>
			<tr bgcolor='$tr_bgcolor'>
			<td align='center' rowspan=2>主副食菜餚名稱</td>
			<td align='center' colspan=3>滿意度</td>
			<td align='center' rowspan=2>文字意見</td>
			</tr>
			<tr bgcolor='$tr_bgcolor'>
			<td align='center'>數量</td>
			<td align='center'>色香味</td>
			<td align='center'>衛生安全</td></tr>";
	
	if($pDesign_pN)
	{
		$feedback_menu=$feedback_menu[$pDesign_pN];
		$sql="select * from lunch_feedback where pDate='$sign_date' and class_id='$class_id'";
		$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
		//抓取前已填報資料
		$tr_bgcolor='#ccffcc';
		$sql="select * from lunch_feedback where pDate='$sign_date' and class_id='$class_id'";
		$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
		while(!$res->EOF)
		{
			if(array_key_exists($res->fields[item],$feedback_menu)) {
				$feedback_menu[$res->fields[item]][quantity]=$res->fields[quantity];
				$feedback_menu[$res->fields[item]][taste]=$res->fields[taste];
				$feedback_menu[$res->fields[item]][hygiene]=$res->fields[hygiene];
				$feedback_menu[$res->fields[item]][memo]=$res->fields[memo];
				$last_update="<font color='red' size=2>　* 填報日期：{$res->fields[update_date]} *</font>";
				$tr_bgcolor='#ffaaff';
			}
			$res->MoveNext();
		}
		

		foreach($feedback_menu as $key=>$menu_item) {

			if($key){
				//產生選單
				$quantity_ref=explode(",",$m_arr[quantity_ref]);
				$quantity_combo="<select name='quantity[]'>";
				foreach($quantity_ref as $value)
				{
					if($feedback_menu[$key][quantity]==$value) $is_selected='selected'; else $is_selected='';
					$quantity_combo.="<option $is_selected>$value</option>";
				}
				$quantity_combo.="</select>";
				
				$taste_ref=explode(",",$m_arr[taste_ref]);
				$taste_combo="<select name='taste[]'>";
				foreach($taste_ref as $value)
				{
					if($feedback_menu[$key][taste]==$value) $is_selected='selected'; else $is_selected='';
					$taste_combo.="<option $is_selected>$value</option>";
				}
				$taste_combo.="</select>";
				
				$hygiene_ref=explode(",",$m_arr[hygiene_ref]);
				$hygiene_combo="<select name='hygiene[]'>";
				foreach($hygiene_ref as $value)
				{
					if($feedback_menu[$key][hygiene]==$value) $is_selected='selected'; else $is_selected='';
					$hygiene_combo.="<option $is_selected>$value</option>";
				}
				$hygiene_combo.="</select>";
				
				$showdata.="<tr bgcolor=#FFFFDD><td align='center'>$key</td><input type='hidden' name='item[]' value='$key'>";
				$showdata.="<td align='center'>".$quantity_combo."</td>";
				$showdata.="<td align='center'>".$taste_combo."</td>";
				$showdata.="<td align='center'>".$hygiene_combo."</td>";
				$showdata.="<td><input type='text' name='memo[]' size='30' value='".$feedback_menu[$key][memo]."'</td>";
				$showdata.="</td></tr>";
			}
		}
	}
	$showdata.="<tr><td align='center' colspan=5><input type='submit' value='傳送' name='act'><input type='reset' value='重新設定'></td></tr>
				</form></table>";

	echo $main.$showdata;

} else echo $not_allowed;

foot();

?>
