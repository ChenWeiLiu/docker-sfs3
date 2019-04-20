<?php

// $Id: place.php 6411 2011-04-20 04:41:16Z infodaes $
include "config.php";

sfs_check();

//秀出網頁

head("場地出租管理");

$rent_place=$_POST[rent_place];
$linkstr="rent_place=$rent_place";

echo print_menu($MENU_P,$linkstr);


if($_POST['act']=='新增'){
	if($_POST[a_rent_place]){
		$sql="INSERT INTO rent_place(rank,rent_place,note,rent_public,rent_private,rent_special,prove_public,prove_private,prove_special,clean_public,clean_private,clean_special) values ('$_POST[a_rank]','$_POST[a_rent_place]','$_POST[a_note]',$_POST[a_rent_public],$_POST[a_rent_private],$_POST[a_rent_special],$_POST[a_prove_public],$_POST[a_prove_private],$_POST[a_prove_special],$_POST[a_clean_public],$_POST[a_clean_private],$_POST[a_clean_special]);";
		$res=$CONN->Execute($sql) or user_error("新增失敗！<br>$sql",256);
	}
};
if($_POST['act']=='修改'){
	$sql="update rent_place set rank='$_POST[rank]',rent_place='$_POST[e_rent_place]',note='$_POST[note]',rent_public=$_POST[rent_public],rent_private=$_POST[rent_private],rent_special=$_POST[rent_special],prove_public=$_POST[prove_public],prove_private=$_POST[prove_private],prove_special=$_POST[prove_special],clean_public=$_POST[clean_public],clean_private=$_POST[clean_private],clean_special=$_POST[clean_special] where rent_place='$_POST[rent_place]';";
	$res=$CONN->Execute($sql) or user_error("修改失敗！<br>$sql",256);
	$rent_place='';
};

if($_POST['act']=='刪除'){
	$sql="delete from rent_place where rent_place=$_POST[rent_place]";
	$res=$CONN->Execute($sql) or user_error("刪除項目失敗！<br>$sql",256);
};

$main="<table><form name='form_place' method='post' action='$_SERVER[PHP_SELF]'>※租借場地：
	<select name='rent_place' onchange='this.form.submit()'><option></option>";

//取得租借場地項目
$sql="select * from rent_place order by rank";
$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
while(!$res->EOF) {
	$main.="<option ".($rent_place==$res->fields[rent_place]?"selected":"")." value=".$res->fields[rent_place].">(".$res->fields[rank].")".$res->fields[rent_place]."</option>";
	$res->MoveNext();
}
$main.="</select></table>";

//顯示指定學期項目詳細資料
$res->MoveFirst();
$showdata="<table border='1' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' width='100%'>

	<tr>
	<td align='center' bgcolor='#CCFF99' rowspan=2>NO.</td>
	<td align='center' bgcolor='#CCFF99' rowspan=2>排序</td>
	<td align='center' bgcolor='#CCFF99' rowspan=2>場地名稱</td>
	<td align='center' bgcolor='#CCFF99' colspan=3>公家機關租金</td>
	<td align='center' bgcolor='#CCFF99' colspan=3>私人機構租金</td>
	<td align='center' bgcolor='#CCFF99' colspan=3>特殊單位租金</td>
	<td align='center' bgcolor='#CCFF99' rowspan=2>附記說明</td>
	<td align='center' bgcolor='#CCFF99' rowspan=2>功能操作</td>
	</tr>
	<tr>
	<td align='center' bgcolor='#CCFF99'>管理維護</td>
	<td align='center' bgcolor='#CCFF99'>水電補貼</td>
	<td align='center' bgcolor='#CCFF99'>保證金</td>
	<td align='center' bgcolor='#CCFF99'>管理維護</td>
	<td align='center' bgcolor='#CCFF99'>水電補貼</td>
	<td align='center' bgcolor='#CCFF99'>保證金</td>
	<td align='center' bgcolor='#CCFF99'>管理維護</td>
	<td align='center' bgcolor='#CCFF99'>水電補貼</td>
	<td align='center' bgcolor='#CCFF99'>保證金</td>
	</tr>";

while(!$res->EOF) {
	
	//echo "====".$_POST[rent_place]."::::::".$res->fields[rent_place];
	
	if($rent_place==$res->fields[rent_place]){
		//編輯
		$showdata.="<tr bgcolor=#AAFFCC><td align='center'>".($res->CurrentRow()+1)."</td>";
		$showdata.="<td><input type='text' name='rank' size=2 value='".$res->fields[rank]."'</td>";
		$showdata.="<td><input type='text' name='e_rent_place' size=10 value='".$res->fields[rent_place]."'</td>";
		$showdata.="<td><input type='text' name='rent_public' size=5 value='".$res->fields[rent_public]."'</td>";
		$showdata.="<td><input type='text' name='clean_public' size=5 value='".$res->fields[clean_public]."'</td>";
		$showdata.="<td><input type='text' name='prove_public' size=5 value='".$res->fields[prove_public]."'</td>";
		$showdata.="<td><input type='text' name='rent_private' size=5 value='".$res->fields[rent_private]."'</td>";
		$showdata.="<td><input type='text' name='clean_private' size=5 value='".$res->fields[clean_private]."'</td>";
		$showdata.="<td><input type='text' name='prove_private' size=5 value='".$res->fields[prove_private]."'</td>";
		$showdata.="<td><input type='text' name='rent_special' size=5 value='".$res->fields[rent_special]."'</td>";
		$showdata.="<td><input type='text' name='clean_special' size=5 value='".$res->fields[clean_special]."'</td>";
		$showdata.="<td><input type='text' name='prove_special' size=5 value='".$res->fields[prove_special]."'</td>";
		$showdata.="<td><input type='text' name='note' size=20 value='".$res->fields[note]."'</td>";
		$showdata.="<td align='center'><input type='submit' value='修改' name='act' onclick='return confirm(\"確定要更改[".$res->fields[rent_place]."]?\")'><BR><input type='submit' value='刪除' name='act' onclick='return confirm(\"真的要刪除[".$res->fields[rent_place]."]?\\n\\nPS.按[確定]會刪除項目細目及收費紀錄,且資料將不可回復\")'></td></tr>";
	} else {	
		$showdata.="<tr bgcolor=#FFFFDD><td align='center'>".($res->CurrentRow()+1)."</td>";
		$showdata.="<td align='center'>".$res->fields[rank]."</td>";
		$showdata.="<td align='center'>".$res->fields[rent_place]."</td>";
		
		$showdata.="<td align='center'>".$res->fields[rent_public]."</td>";
		$showdata.="<td align='center'>".$res->fields[clean_public]."</td>";
		$showdata.="<td align='center'>".$res->fields[prove_public]."</td>";
		
		$showdata.="<td align='center'>".$res->fields[rent_private]."</td>";
		$showdata.="<td align='center'>".$res->fields[clean_private]."</td>";
		$showdata.="<td align='center'>".$res->fields[prove_private]."</td>";
		
		$showdata.="<td align='center'>".$res->fields[rent_special]."</td>";
		$showdata.="<td align='center'>".$res->fields[clean_special]."</td>";
		$showdata.="<td align='center'>".$res->fields[prove_special]."</td>";
		
		$showdata.="<td align='center'>".$res->fields[note]."</td>";
	
		$showdata.="<td></td></tr>";
	}
	$res->MoveNext();
}
	//新增項目
	if(!$rent_place){
	$showdata.="<tr></tr><tr bgcolor='#FFCCCC' height=60><td align='center'><img border=0 src='images/add.gif' alt='開列新項目'></td>";
		$showdata.="<td><input type='text' name='a_rank' size=2 value=''</td>";
		$showdata.="<td><input type='text' name='a_rent_place' size=10 value=''</td>";
		$showdata.="<td><input type='text' name='a_rent_public' size=5 value='0'</td>";
		$showdata.="<td><input type='text' name='a_clean_public' size=5 value='0'</td>";
		$showdata.="<td><input type='text' name='a_prove_public' size=5 value='0'</td>";
		$showdata.="<td><input type='text' name='a_rent_private' size=5 value='0'</td>";
		$showdata.="<td><input type='text' name='a_clean_private' size=5 value='0'</td>";
		$showdata.="<td><input type='text' name='a_prove_private' size=5 value='0'</td>";
		$showdata.="<td><input type='text' name='a_rent_special' size=5 value='0'</td>";
		$showdata.="<td><input type='text' name='a_clean_special' size=5 value='0'</td>";
		$showdata.="<td><input type='text' name='a_prove_special' size=5 value='0'</td>";
		$showdata.="<td><input type='text' name='a_note' size=20 value=''</td>";
	$showdata.="<td align='center'><input type='submit' value='新增' name='act'><BR><input type='reset' value='重設'></td></tr>";
	}
	$showdata.="</form></table>";

echo $main.$showdata;

foot();

?>