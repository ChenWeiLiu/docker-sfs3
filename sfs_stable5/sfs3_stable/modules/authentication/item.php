<?php

include "config.php";
sfs_check();

//秀出網頁
head("學習認證-項目設定");
//學期別
$curr_year_seme=sprintf("%03d%d",curr_year(),curr_seme());
$work_year_seme =$_REQUEST[work_year_seme]?$_REQUEST[work_year_seme]:$curr_year_seme;
$item_sn=$_POST[item_sn];
$select_item=$_POST['select_item'];

//橫向選單標籤
echo print_menu($MENU_P);

if($_POST['act']!='維護'){
	if($work_year_seme==$curr_year_seme)
	{
	//將系統變數選項轉為combobox
	$types="<select name='a_nature'><option>".str_replace(",","</option><option>",$m_arr['types'])."</option></select>";

		//新增項目
		$added_data="<tr></tr><tr bgcolor='#FFCCCC'><td align='center'><img border=0 src='images/add.gif' alt='開列新項目'></td>";
		$added_data.="<td align='center'>$curr_year_seme</td>";
		$added_data.="<td align='center'>$types</td>";
		$added_data.="<td align='center'><input type='text' name='a_code' size=5></td>";
		$added_data.="<td align='center'><input type='text' name='a_title' size=60></td>";
		//$added_data.="<td align='center'><input type='text' name='a_description' size=20></td>";
		$added_data.="<td align='center'><input type='text' name='a_start_date' size=8></td>";
		$added_data.="<td align='center'><input type='text' name='a_end_date' size=8></td>";
		$added_data.="<td align='center' colspan=3><input type='submit' value='新增' name='act'><input type='reset' value='重新設定'></td></tr>";
		//依照格式新增項目
		$added_data.="<tr bgcolor='#CFFCAA'><td align='center'><img border=0 src='images/batchadd.gif' alt='格式新增項目與細目'></td>";
		$added_data.="<td colspan=4><pre>格式：
	[類別]#[代碼]#[認證項目]#[認證起始日期]#[認證終止日期]
	[子代號]#[子項目名稱]#[適用年級]#[積點]
	[子代號]#[子項目名稱]#[適用年級]#[積點]
		:
範例：
	語文#VOC#英文單字1000#2010-12-10#2011-12-10
	L1#數字篇#1,2#1
	L2#交通工具篇#1,2#1
	L3#身體篇#3,4#1
	L4#水果篇#3,4#1
	L5#物品篇#5,6#1
	L6#行為篇#5,6#1
	</PRE></td>";
		$added_data.="<td colspan=5><textarea rows=13 name='formatted' cols=43></textarea><BR><input type='submit' value='依格式新增' name='act'></td></tr><tr></tr>";
	}
}

if($_POST['act']=='新增'){
	$sql_select="INSERT INTO authentication_item(year_seme,code,nature,title,room_id,start_date,end_date,creater) values ('$work_year_seme','$_POST[a_code]','$_POST[a_nature]','$_POST[a_title]','$my_room_id','$_POST[a_start_date]','$_POST[a_end_date]','$my_sn')";
	$res=$CONN->Execute($sql_select) or user_error("新增失敗！<br>$sql_select<br>有可能是代碼重複了!",256);
};

if($_POST['act']=='更新'){
	$sql_select="UPDATE authentication_item SET year_seme='$work_year_seme',code='$_POST[code]',nature='$_POST[nature]',title='$_POST[title]',description='$_POST[description]',room_id='$_POST[room_kind_id]',start_date='$_POST[start_date]',end_date='$_POST[end_date]' WHERE sn=$item_sn;";
	$res=$CONN->Execute($sql_select) or user_error("修改失敗！<br>$sql_select<br>有可能是代碼重複了!",256);
	$_POST[item_sn]=0;
};

if($_POST['act']=='刪除'){
	//刪除項目
	$sql_select="delete from authentication_item where sn=$item_sn";
	$res=$CONN->Execute($sql_select) or user_error("刪除項目失敗！<br>$sql_select",256);
	/*
	//刪除細目
	$sql_select="delete from authentication_subitem where item_sn=$item_sn";
	$res=$CONN->Execute($sql_select) or user_error("刪除細目失敗！<br>$sql_select",256);
	//刪除紀錄
	$sql_select="delete from authentication_item where item_id=$item_sn";
	$res=$CONN->Execute($sql_select) or user_error("刪除紀錄失敗！<br>$sql_select",256);
	*/
};
if($_POST['act']=='複製'){
	//複製項目
	$sql="INSERT INTO authentication_item SET year_seme='$work_year_seme',code='".substr(time(),-5)."',nature='{$_POST[nature]}',title='複製-{$_POST[title]}',description='{$_POST[description]}',room_id='$my_room_id',start_date='{$_POST[start_date]}',end_date='{$_POST[end_date]}',creater=$my_sn;";
	$res=$CONN->Execute($sql) or user_error("複製項目失敗！<br>$sql",256);
	$item_sn=$CONN->insert_id();
	
	//複製細目
	$sql_select="select * from authentication_subitem where item_sn={$_POST[item_sn]} order by code;";
	$res=$CONN->Execute($sql_select) or user_error("讀取複製項目失敗！<br>$sql_select",256);
	
	$batch_value="";
	while(!$res->EOF)
	{
		$code=$res->fields[code];
		$title=$res->fields[title];
		$grades=$res->fields[grades];
		$bonus=$res->fields[bonus];
		$batch_value.="($item_sn,'$code','$title','$grades',$bonus),";
		$res->MoveNext();
	}
	$batch_value=substr($batch_value,0,-1);
	$sql_select="INSERT INTO authentication_subitem(item_sn,code,title,grades,bonus) values $batch_value";
	$res=$CONN->Execute($sql_select) or user_error("複製細目失敗！<br>$sql_select",256);
	echo "<script language=\"Javascript\"> alert (\"項目與細目已複製，請繼續設定正確的項目代碼！\")</script>";
	$_POST[item_sn]=$item_sn;
};


if($_POST['act']=='依格式新增'){
	if($_POST['formatted']){
		//將格式轉為陣列
		$formatted=explode("\r\n",$_POST['formatted']);
		//開列項目
		$item_data=explode("#",$formatted[0]);
		$sql_select="INSERT INTO authentication_item(year_seme,nature,code,title,start_date,end_date,room_id,creater) values ('$work_year_seme','$item_data[0]','$item_data[1]','$item_data[2]','$item_data[3]','$item_data[4]','$my_room_id','$my_sn')";
		$res=$CONN->Execute($sql_select) or user_error("開列項目失敗！<br>$sql_select",256);
		$item_sn=$CONN->insert_id();
		array_shift($formatted);		
		
		//開列細目
		$batch_value="";
		foreach($formatted as $value)
		{
			if($value)
			{
				$value=explode("#",$value);
				$code=$value[0];
				$title=$value[1];
				$grades=$value[2];
				$bonus=$value[3];
				$batch_value.="($item_sn,'$code','$title','$grades',$bonus),";
			}
		}
		$batch_value=substr($batch_value,0,-1);
		$sql_select="INSERT INTO authentication_subitem(item_sn,code,title,grades,bonus) values $batch_value";
		$res=$CONN->Execute($sql_select) or user_error("依格式新增細目失敗<br>$sql_select",256);
		$work_year_seme=$curr_year_seme;
		echo "<script language=\"Javascript\"> alert (\"項目與細目已依格式開列！\")</script>";
		
		$_POST[item_sn]=$item_sn;
		
	}
};

$main="<form name='myform' method='post' action='$_SERVER[SCRIPT_NAME]'><input type='hidden' name='item_sn' value=$item_sn>
	※顯示限定：<input type='radio' value=0 ".($select_item==0?'checked':'')." name='select_item' onclick='this.form.submit()'>全部 
				<input type='radio' value=1 ".($select_item==1?'checked':'')." name='select_item' onclick='this.form.submit()'>認證中 
				<input type='radio' value=2 ".($select_item==2?'checked':'')." name='select_item' onclick='this.form.submit()'>停止認證 
				<input type='radio' value=3 ".($select_item==3?'checked':'')." name='select_item' onclick='this.form.submit()'>待啟用認證";
$filter='';
$item_color='#FFFFFF';
switch($select_item){
 case 1:
     $filter='WHERE CURDATE() BETWEEN start_date AND end_date'; $item_color='#CCFFFF'; 
  break;
 case 2:
     $filter='WHERE CURDATE()>end_date'; $item_color='#CCCCCC'; 
  break;
 case 3:
     $filter='WHERE CURDATE()<start_date'; $item_color='#FFCCCC'; 
  break;
}
 
		
//列出項目
$sql_select="select * from authentication_item $filter order by code";
$res=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
//顯示指定學期項目詳細資料
$res->MoveFirst();
$showdata="<table border='1' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' width='100%'>
	<tr>
	<td align='center' bgcolor='#CCFF99'>NO.</td>
	<td align='center' bgcolor='#CCFF99'>學期</td>
	<td align='center' bgcolor='#CCFF99'>類別</td>
	<td align='center' bgcolor='#CCFF99'>代碼</td>
	<td align='center' bgcolor='#CCFF99'>認證項目</td>
	<td align='center' bgcolor='#CCFF99' colspan=2>認證期間</td>
	<td align='center' bgcolor='#CCFF99'>管理處室</td>
	<td align='center' bgcolor='#CCFF99'>開列者</td>
	<td align='center' bgcolor='#CCFF99'>--</td>
	</tr>";
	while(!$res->EOF) {
		if($_POST[item_sn]==$res->fields[sn]){
			//產生處室選單
			$room_kind_select="<select name='room_kind_id'>";
			foreach($room_kind_array as $key=>$value) {
				if($key==$res->fields[room_id]) $selected='selected'; else $selected='';
				$room_kind_select.="<option value=$key $selected>$value</option>";
			}
			$room_kind_select.="</select>";
			
			$authority=explode(',',$m_arr['authority']);
			foreach($authority as $value){
				$authority_ref.="<option>$value</option>";
			}
			$authority_ref="<select name='authority_ref' onchange='this.form.authority.value=this.options[this.selectedIndex].text'><option></option>$authority_ref</select>";

			$showdata.="<tr bgcolor='#FFFAAA'><td align='center'>".($res->CurrentRow()+1)."</td>";
			$showdata.="<td align='center'>".$res->fields[year_seme]."</td>";
			$showdata.="<td align='center'><input type='text' name='nature' size=10 value={$res->fields[nature]}></td>";		
			$showdata.="<td align='center'><input type='text' name='code' size=5 value={$res->fields[code]}></td>";
			$showdata.="<td><input type='text' name='title' size=60 value={$res->fields[title]}></td>";
			//$showdata.="<td>".$res->fields[description]."</td>";
			$showdata.="<td align='center'><input type='text' name='start_date' size=10 value='{$res->fields[start_date]}'></td>";
			$showdata.="<td align='center'><input type='text' name='end_date' size=10 value='{$res->fields[end_date]}'></td>";		
			$showdata.="<td align='center'>$room_kind_select</td>";
			//$showdata.="<td align='center'>{$res->fields[creater]}</td>";
			//$showdata.="<td align='center'><input type='text' name='creater' size=10 value='{$res->fields[creater]}'></td>";
			$showdata.="<td align='center' colspan=2>";
			if($my_room_id==$res->fields['room_id']) $showdata.="<input type='submit' value='更新' name='act' onclick='return confirm(\"確定要更新[{$res->fields[code]}-{$res->fields[title]}]?\")'><input type='submit' value='刪除' name='act' onclick='return confirm(\"真的要刪除[{$res->fields[code]}-{$res->fields[title]}]?\")'>";
			$showdata.="<input type='submit' value='複製' name='act' onclick='return confirm(\"確定要複製[".$res->fields[title]."]至本學年?\")'>　</td></tr>";
		} else {	
			$item_sn=$res->fields[sn];
			$showdata.="<tr bgcolor=$item_color><td align='center'>".($res->CurrentRow()+1)."</td>";
			$showdata.="<td align='center'>".$res->fields[year_seme]."</td>";
			$showdata.="<td align='center'>".$res->fields[nature]."</td>";		
			$showdata.="<td align='center'>".$res->fields[code]."</td>";
			$showdata.="<td>".$res->fields[title]."</td>";
			$showdata.="<td align='center'>".$res->fields[start_date]."</td>";
			$showdata.="<td align='center'>".$res->fields[end_date]."</td>";
			
			$showdata.="<td align='center'>".$room_kind_array[($res->fields[room_id])]."</td>";
			$showdata.="<td align='center'>".$teacher_array[($res->fields[creater])]."</td>";
			$showdata.="<td align='center'><input type='button' name='act' value='維護' onclick='this.form.item_sn.value=\"$item_sn\"; this.form.submit();'></td>";
			$showdata.="</tr>";
		}
		$res->MoveNext();
	}

echo $main.$showdata.$added_data."</form></table>";
foot();

?>