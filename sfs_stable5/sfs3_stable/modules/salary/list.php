<?php

//$Id: $
include "config.php";
sfs_check();
//秀出網頁
head("薪津查詢");

//橫向選單標籤
//$linkstr="item_id=$item_id";
print_menu($menu_p);

$No=$_POST['No'];
//$query = "SELECT  EXTRACT(YEAR FROM AnnounceDate) AS yy FROM  salary  WHERE ID='$pserson_id' GROUP BY  AnnounceDate";
if($person_id) {
	$main="<table>
<form name='form_item' method='post' action='$_SERVER[PHP_SELF]'>項目清單：<select name='No' onchange='this.form.submit()'><option></option>";

	//取得年度項目
	$sql_select="SELECT * FROM salary WHERE ID='$person_id' ORDER BY InType";

	$res=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	while(!$res->EOF) {
		$main.="<option ".($No==$res->fields['No']?"selected":"")." value=".$res->fields['No'].">".$res->fields['InType']."</option>";
		$res->MoveNext();
	}
	$main.="</select>";

	if($No)
	{
		//取得已發布紀錄
		$stud_select="select * from salary WHERE No=$No";
		$recordSet=$CONN->Execute($stud_select) or user_error("讀取失敗！<br>$stud_select",256);

		$showdata.="<table align=center width=".$m_arr['Table_width']."% border='2' cellpadding='5' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' id='AutoNumber1'>

			<tr bgcolor='$Tr_BGColor'>
				<td align='center' colspan=3>".$m_arr['BasisData_caption']."</td>
			</tr>

			<tr><td colspan=3>
			<table width='100%' cellpadding=0 cellspacing=0 class='small'>
				<tr><td width='50%'>";
		foreach($BasisData1_arr as $fieldname){
			if ($fieldname == 'ID' || $filedname=='AccountID1' || $fieldname=='AccountID2')
			$tempData = substr($recordSet->fields[$fieldname],0,3) . str_repeat('*', strlen($recordSet->fields[$fieldname])-6). substr($recordSet->fields[$fieldname],-3);
			else
			$tempData = $recordSet->fields[$fieldname];

			$showdata.=$m_arr[$fieldname]."：<font color='blue'>".$tempData."</font><BR>";
		}
		$showdata.="</td><td width='50%'>";
		foreach($BasisData2_arr as $fieldname){
			if ( $fieldname=='AccountID1' || $fieldname=='AccountID2')
			$tempData = substr($recordSet->fields[$fieldname],0,3) . str_repeat('*', strlen($recordSet->fields[$fieldname])-6). substr($recordSet->fields[$fieldname],-3);
			else
			$tempData = $recordSet->fields[$fieldname];
			$showdata.=$m_arr[$fieldname]."：<font color='#FF6600'>".$tempData."</font><BR>";
		}
		$showdata.="</td></tr>
			</table>";
		$showdata.="</td></tr>
			<tr bgcolor='$Tr_BGColor'>
				<td align='center'>".$m_arr['Mg_caption']."</td>
				<td align='center'>".$m_arr['Mh_caption']."</td>
				<td align='center'>".$m_arr['Mi_caption']."</td>
			</tr>
			<tr><td align='left' valign='top' class='small'>";

		$Mg_Total=0;
		foreach($Mg_arr as $fieldNo){
			$fieldname='Mg'.$fieldNo;
			$showdata.=$m_arr[$fieldname]."：<font color='#FF6600'>".$recordSet->fields[$fieldname]."</font><BR>";
			$Mg_Total+=$recordSet->fields[$fieldname];
		}
		$showdata.="</td><td align='left' valign='top' class='small'>";

		$Mh_Total=0;
		foreach($Mh_arr as $fieldNo){
			$fieldname='Mh'.$fieldNo;
			$showdata.=$m_arr[$fieldname]."：<font color='#FF6600'>".$recordSet->fields[$fieldname]."</font><BR>";
			$Mh_Total+=$recordSet->fields[$fieldname];
		}
		$showdata.="</td><td align='left' valign='top' class='small'>";

		$Mi_Total=0;
		foreach($Mi_arr as $fieldNo){
			$fieldname='Mi'.$fieldNo;
			$showdata.=$m_arr[$fieldname]."：<font color='#FF6600'>".$recordSet->fields[$fieldname]."</font><BR>";
			$Mi_Total+=$recordSet->fields[$fieldname];
		}

		$total=$Mg_Total-$Mh_Total-$Mi_Total;
		$memo=str_replace("+","■",$recordSet->fields[Memo]);
		$showdata.="</td></tr>
			<tr>
			<td align='center'>小計(1)：$Mg_Total</td>
			<td align='center'>小計(2)：$Mh_Total</td>
			<td align='center'>小計(3)：$Mi_Total</td>
			</tr><tr bgcolor='$Tr_BGColor'><td  align='center' colspan=3>入帳金額： (1)-(2)-(3) = $total</td></tr>
			<tr><td align='left' valign='top' colspan=3>備註：$memo</td></tr></table>";
	}
	$showdata.="</form></table>";

	echo $main.$showdata;
} else echo $empty_person_id;
foot();
?>
