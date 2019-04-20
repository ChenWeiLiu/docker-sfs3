<?php

// $Id: item.php 5310 2009-01-10 07:57:56Z hami $



include "config.php";

sfs_check();



//秀出網頁

head("收費管理");



//學期別

$work_year_seme=$_REQUEST[work_year_seme];

if($work_year_seme=='') $work_year_seme = sprintf("%03d%d",curr_year(),curr_seme());

$curr_year_seme=sprintf("%03d%d",curr_year(),curr_seme());



$item_id=$_REQUEST[item_id];



//橫向選單標籤

$linkstr="work_year_seme=$work_year_seme&item_id=$item_id";

echo print_menu($MENU_P,$linkstr);



if($_POST['act']=='新增'){

	$sql_select="INSERT INTO charge_item(year_seme,item_type,item,start_date,end_date,comment,creater) values ('$work_year_seme','$_POST[a_item_type]','$_POST[a_item]','$_POST[a_start_date]','$_POST[a_end_date]','$_POST[a_comment]','$_SESSION[session_tea_name]')";

	$res=$CONN->Execute($sql_select) or user_error("新增失敗！<br>$sql_select",256);

};



if($_POST['act']=='修改'){

	$sql_select="update charge_item set year_seme='$work_year_seme',item_type='$_POST[item_type]',item='$_POST[item]',start_date='$_POST[start_date]',end_date='$_POST[end_date]',comment='$_POST[comment]',creater='$_SESSION[session_tea_name]',authority='$_POST[authority]',paid_method='$_POST[paid_method]',announce_note='$_POST[announce_note]',announce_note2='$_POST[announce_note2]',cooperate=$_POST[cooperate] where item_id=$item_id;";

	$res=$CONN->Execute($sql_select) or user_error("修改失敗！<br>$sql_select",256);

	$item_id=0;

};



if($_POST['act']=='刪除'){

	//刪除項目

	$sql_select="delete from charge_item where item_id=$item_id";

	$res=$CONN->Execute($sql_select) or user_error("刪除項目失敗！<br>$sql_select",256);

	//刪除細目

	$sql_select="delete from charge_detail where item_id=$item_id";

	$res=$CONN->Execute($sql_select) or user_error("刪除細目失敗！<br>$sql_select",256);

	//刪除紀錄

	$sql_select="delete from charge_record where item_id=$item_id";

	$res=$CONN->Execute($sql_select) or user_error("刪除紀錄失敗！<br>$sql_select",256);

	

};





if($_POST['act']=='複製'){

	//複製項目

	$sql_select="INSERT INTO charge_item(year_seme,item_type,item,comment,creater) values ('$curr_year_seme','$_POST[item_type]','複製-$_POST[item]','$_POST[a_comment]','$_SESSION[session_tea_name]')";

	$res=$CONN->Execute($sql_select) or user_error("複製項目失敗！<br>$sql_select",256);

	$item_id=$CONN->insert_id();

	

	//echo "<PRE>";

	//print_r($CONN->insert_id());

	//echo "</PRE>";

	

	//複製細目

	$sql_select="select * from charge_detail where item_id=$item_id order by detail_sort;";

	$res=$CONN->Execute($sql_select) or user_error("複製細目失敗！<br>$sql_select",256);

	$batch_value="";

	while(!$res->EOF)

	{

		$detail_sort=$res->fields[detail_sort];

		$detail=$res->fields[detail];

		$dollars=$res->fields[dollars];

		$batch_value.="($item_id,'$detail_sort','$detail','$dollars'),";

		$res->MoveNext();

	}

	$batch_value=substr($batch_value,0,-1);

	//echo "===================<BR>$batch_value<BR>===================";

	$sql_select="REPLACE INTO charge_detail(item_id,detail_sort,detail,dollars) values $batch_value";

	$res=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);



	$work_year_seme=$curr_year_seme;



};



if($_POST['act']=='依格式新增'){

	if($_POST[formatted]){

		//將格式轉為陣列

		$formatted=explode("\r\n",$_POST[formatted]);

		

		//開列項目

		$item_data=explode("_",$formatted[0]);

		$sql_select="INSERT INTO charge_item(year_seme,item_type,item,comment,creater) values ('$curr_year_seme','$item_data[0]','$item_data[1]','$_POST[a_comment]','$_SESSION[session_tea_name]')";

		$res=$CONN->Execute($sql_select) or user_error("開列項目失敗！<br>$sql_select",256);

		$item_id=$CONN->insert_id();

		

		array_shift($formatted);		

		//複製細目

		$batch_value="";

		foreach($formatted as $value)

		{

			if($value)

			{

				$value=explode("_",$value);

				$detail_sort=$value[0];

				$detail=$value[1];

				$dollars=$value[2];

				$batch_value.="($item_id,'$detail_sort','$detail','$dollars'),";

			}

		}

		$batch_value=substr($batch_value,0,-1);

		$sql_select="REPLACE INTO charge_detail(item_id,detail_sort,detail,dollars) values $batch_value";

		$res=$CONN->Execute($sql_select) or user_error("開列細目失敗！<br>$sql_select",256);

		$work_year_seme=$curr_year_seme;

		echo "<script language=\"Javascript\"> alert (\"項目與細目已依格式開列,請繼續設定[收費日期]及[附記說明]！\")</script>";

	}

};











//取得年度與學期的下拉選單

$seme_list=get_class_seme();

$main="<table><form name='form_item' method='post' action='$_SERVER[PHP_SELF]'>

	<select name='work_year_seme' onchange='this.form.submit()'>";

foreach($seme_list as $key=>$value){

	$main.="<option ".($key==$work_year_seme?"selected":"")." value=$key>$value</option>";

}

$main.="</select>　<img border=0 src='images/modify.gif' alt='編修選定項目'><select name='item_id' onchange='this.form.submit()'><option></option>";



//取得年度項目

$sql_select="select * from charge_item where year_seme='$work_year_seme' order by end_date desc";

$res=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);



while(!$res->EOF) {

	$main.="<option ".($item_id==$res->fields[item_id]?"selected":"")." value=".$res->fields[item_id].">".$res->fields[item]."(".$res->fields[start_date]."~".$res->fields[end_date].")</option>";

	$res->MoveNext();

}

$main.="</select></table>";



//顯示指定學期項目詳細資料



$res->MoveFirst();



$showdata="<table border='1' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' width='100%'>

	<tr>

	<td align='center' bgcolor='#CCFF99'>NO.</td>

	<td align='center' bgcolor='#CCFF99'>類別</td>

	<td align='center' bgcolor='#CCFF99'>項目名稱</td>

	<td align='center' bgcolor='#CCFF99' colspan=2>收費日期</td>

	<td align='center' bgcolor='#CCFF99'>管理備註</td>

	<td align='center' bgcolor='#CCFF99'>開列者</td>

	</tr>";

while(!$res->EOF) {

	if($item_id==$res->fields[item_id]){

		//依據參照

		$authority=explode(',',$m_arr['authority']);

		foreach($authority as $value){

			$authority_ref.="<option>$value</option>";

		}

		$authority_ref="<select name='authority_ref' onchange='this.form.authority.value=this.options[this.selectedIndex].text'><option></option>$authority_ref</select>";

		

		//收款方式

		$paid_method=explode(',',$m_arr['paid_method']);

		foreach($paid_method as $value){

			$paid_method_ref.="<option>$value</option>";

		}

		$paid_method_ref="<select name='paid_method_ref' onchange='this.form.paid_method.value=this.options[this.selectedIndex].text'><option></option>$paid_method_ref</select>";

		

		

		

		$showdata.="<tr bgcolor=#AAFFCC><td align='center'>".($res->CurrentRow()+1)."</td>";

		$showdata.="<td colspan=5>※類　　別：<input type='text' name='item_type' size=10 value=".$res->fields[item_type]."><BR>";

		$showdata.="※項目名稱：<input type='text' name='item' size=40 value=".$res->fields[item]."><BR>";

		$showdata.="※管理備註：<input type='text' name='comment' size=40 value=".$res->fields[comment]."><BR>";

		$showdata.="※收費日期：<input type='text' name='start_date' size=10 value=".$res->fields[start_date].">～<input type='text' name='end_date' size=10 value=".$res->fields[end_date]."><BR>";

		$showdata.="※依　　據：$authority_ref => <input type='text' name='authority' size=30 value=".$res->fields[authority]."><BR>";

		$showdata.="※繳款方式：$paid_method_ref => <input type='text' name='paid_method' size=30 value=".$res->fields[paid_method]."><BR>";

		$showdata.="※單據附記：<input type='text' name='announce_note' size=40 value=".$res->fields[announce_note]."><BR>";

		$showdata.="　　　　　　<input type='text' name='announce_note2' size=40 value=".$res->fields[announce_note2]."><BR>";
		
		$showdata.="※收費管理(導師版)協同作業：<select name='cooperate'><option ".($res->fields[cooperate]?"selected":"")."  value='1'>可</option><option ".(!$res->fields[cooperate]?" selected":"")." value='0'>否</option></select>'</td>";
		
		$showdata.="<td align='center'><input type='submit' value='修改' name='act' onclick='return confirm(\"確定要更改[".$res->fields[item]."]?\")'><BR><BR><input type='submit' value='刪除' name='act' onclick='return confirm(\"真的要刪除[".$res->fields[item]."]?\\n\\nPS.按[確定]會刪除項目細目及收費紀錄,且資料將不可回復\")'><BR><BR><input type='submit' value='複製' name='act' onclick='return confirm(\"確定要複製[".$res->fields[item]."]至本學年?\")'>　</td></tr>";

	} else {	

		$showdata.="<tr bgcolor=#FFFFDD><td align='center'>".($res->CurrentRow()+1)."</td>";

		$showdata.="<td align='center'>".$res->fields[item_type]."</td>";

		$showdata.="<td>".$res->fields[item]."</td>";

		$showdata.="<td align='center'>".$res->fields[start_date]."</td>";

		$showdata.="<td align='center'>".$res->fields[end_date]."</td>";

		$showdata.="<td>".$res->fields[comment]."</td>";

		$showdata.="<td align='center'>".$res->fields[creater]."</td>";

		//$showdata.="<td align='center'>".$res->fields[timestamp]."</td>";

		//功能連結

		//$showdata.="<td align='center'>";

		//$showdata.="<a href='detail.php?item_id=".$res->fields[item_id]."'><img border=0 src='images/modify.gif' alt='設定細目'> </a>";

		//$showdata.="<a href='record.php?item_id=".$res->fields[item_id]."'><img border=0 src='images/sxw.gif' alt='印收費單'> </a>";

		//$showdata.="<a href='statistics.php?item_id=".$res->fields[item_id]."'><img border=0 src='images/sigma.gif' alt='收費統計'> </a>";

		//$showdata.="<a href='item.php?act=delete&item_id=".$res->fields[item_id]."'><img border=0 src='images/delete.gif' alt='刪除' onclick='return confirm(\"真的要刪除?\")'></a>";

		$showdata.="</td></tr>";

	}



	$res->MoveNext();

}



if($work_year_seme==$curr_year_seme)

{

//將系統變數選項轉為combobox

$types="<select name='a_item_type'><option>".str_replace(",","</option><option>",$m_arr['types'])."</option></select>";

	//新增項目

	$showdata.="<tr></tr><tr bgcolor='#FFCCCC' height=60><td align='center'><img border=0 src='images/add.gif' alt='開列新項目'></td>";

	$showdata.="<td align='center'>$types</td>";

	$showdata.="<td align='center'><input type='text' name='a_item' size=30></td>";

	$showdata.="<td align='center'><input type='text' name='a_start_date' size=8></td>";

	$showdata.="<td align='center'><input type='text' name='a_end_date' size=8></td>";

	$showdata.="<td align='center'><input type='text' name='a_comment' size=20></td>";



	$showdata.="<td align='center'><input type='submit' value='新增' name='act'><input type='reset' value='重新設定'></td></tr>";

	

	//依照格式新增項目

	$showdata.="<tr bgcolor='#CFFC88'><td align='center'><img border=0 src='images/batchadd.gif' alt='格式新增項目與細目'></td>";

	$showdata.="<td colspan=2><textarea rows=13 name='formatted' cols=43></textarea></td>";

	$showdata.="<td colspan=3><pre>格式：
[類別]_[項目名稱]
[排序]_[細目名稱]_[應收金額]
[排序]_[細目名稱]_[應收金額]
	:
範例：
註冊費_學期註冊費(各項代收代辦費)
A0_班級費_50,50,50,50,50,50
B0_家長會費_100,100,100,100,100,100
C0_平安保險費_138,138,138,138,138,138
D0_教科書費_752,747,921,992,842,838
E0_午餐燃料費_160,160,160,160,160,160
E1_午餐基本費_300,300,300,300,300,300
F0_電腦設備維護費_0,0,0,230,230,230</PRE></td>";
	$showdata.="<td align='center'><input type='submit' value='依格式新增' name='act'></td></tr><tr></tr>";

}



$showdata.="</form></table>";



echo $main.$showdata;



foot();

?>