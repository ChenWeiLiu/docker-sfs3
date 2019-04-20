<?php

//$Id: decrease.php 6393 2011-03-15 05:37:20Z infodaes $

include "config.php";
include "my_fun.php";
sfs_check();

//秀出網頁
head("收費管理(導師版)");


echo <<<HERE
<script>
function tagall(status) {
  var i =0;

  while (i < document.myform.elements.length)  {
    if (document.myform.elements[i].name=='selected_stud[]') {
      document.myform.elements[i].checked=status;
    }
    i++;
  }
}
</script>
HERE;

//學期別

$work_year_seme=sprintf("%03d%d",curr_year(),curr_seme());
$item_id=$_REQUEST[item_id];
$detail_id=$_REQUEST[detail_id];
$decrease_id=$_REQUEST[decrease_id];

$a_percent=$_POST[a_percent]?$_POST[a_percent]:100;
$a_cause=$_POST[a_cause];
$subkind_id=$_POST[subkind_id];
$b_percent=$_POST[b_percent]?$_POST[b_percent]:100;
$b_cause=$_POST[b_cause];

// 取出班級陣列
$class_base = class_base($work_year_seme);

//橫向選單標籤
$linkstr="item_id=$item_id";

echo print_menu($MENU_P,$linkstr);

if($m_arr[is_decrease] AND $class_id) {

if($_POST['act']=='新增'){
	if($_POST[selected_stud] AND $_POST[a_percent]){
		foreach($_POST[selected_stud] as $key=>$value) {
			$aaa=explode(',',$value);
			$student_sn=$aaa[0];
			$curr_class_num=$aaa[1];
			if($student_sn<>'' AND $curr_class_num<>'')
			{
				$sql_select="REPLACE INTO charge_decrease(detail_id,student_sn,curr_class_num,percent,cause) values ('$detail_id',$student_sn,'$curr_class_num','$_POST[a_percent]','$_POST[a_cause]')";
				$res=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
			} else echo "<script language=\"Javascript\"> alert (\"學生編號：$student_sn 班級座號：$curr_class_num 傳入資訊不足, 無法新增！\")</script>";
		}
	} else echo "<script language=\"Javascript\"> alert (\"您並未 選擇學生 或者 輸入減免數！\")</script>";
};

if($_POST['act']=='身分批次新增'){
	if($subkind_id<>'' AND $b_percent<>'' AND $_POST[b_cause]<>'')
	{
		//原來的ｓｑｌ　會將所有該身分別學生　　不會管他有無需要收費　通通開列減免
		//$sql_select="select curr_class_num,student_sn from stud_base where (stud_kind like '%,$subkind_id,%') and (stud_study_cond=0) order by curr_class_num";
		//新的ｓｑｌ　只開列該身分別學生　有參加此一收費名單
		$sql_select="select a.*,b.curr_class_num from charge_record a,stud_base b where a.item_id=$item_id AND a.student_sn=b.student_sn AND (b.stud_kind like '%,$subkind_id,%') and (b.stud_study_cond=0) and (curr_class_num like '$class_id%') order by curr_class_num";
		//echo $sql_select."<BR>";
		$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
		//echo "<BR>";
		//print_r($recordSet->FetchRow());
		//echo "<BR>";
		if($recordSet->EOF) echo "<script language=\"Javascript\"> alert (\"無符合資格學生可開列!！\")</script>"; else
		{
			$batch_value="";
			while(!$recordSet->EOF)
			{
				//('$detail_id',$student_sn,'$curr_class_num','$_POST[a_percent]','$_POST[a_cause]')"
				$sn=$recordSet->fields[student_sn];
				$class_num=$recordSet->fields[curr_class_num];
				$batch_value.="('$detail_id',$sn,'$class_num','$_POST[b_percent]','$_POST[b_cause]'),";
				$recordSet->MoveNext();
			}
			$batch_value=substr($batch_value,0,-1);
			//echo "===================<BR>$batch_value<BR>===================";
			$sql_select="REPLACE INTO charge_decrease(detail_id,student_sn,curr_class_num,percent,cause) values $batch_value";
			$res=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
		}
	} else echo "<script language=\"Javascript\"> alert (\"資訊不足, 無法身分別批次新增！\")</script>";
};


if($_POST['act']=='修改'){
	$sql_select="update charge_decrease set detail_id=$detail_id,percent='$_POST[percent]',cause='$_POST[cause]' where decrease_id=$decrease_id;";
	$res=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	$decrease_id=0;
};

if($_POST['act']=='刪除'){
	$sql_select="delete from charge_decrease where decrease_id=$decrease_id";
	$res=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
};


if($_POST['act']=='清空本項名單'){
	$sql_select="delete from charge_decrease where detail_id=$detail_id AND curr_class_num like '$class_id%'";
	$res=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	
	echo $sql_select;
};


$main="<table><form name='myform' method='post' action='$_SERVER[SCRIPT_NAME]'>
	<select name='item_id' onchange='this.form.submit()'><option></option>";

//取得年度項目
$sql_select="select * from charge_item where cooperate=1 AND year_seme='$work_year_seme' AND (curdate() between start_date AND end_date) order by end_date desc";
$res=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);

while(!$res->EOF) {
	$main.="<option ".($item_id==$res->fields[item_id]?"selected":"")." value=".$res->fields[item_id].">".$res->fields[item]."(".$res->fields[start_date]."~".$res->fields[end_date].")</option>";
	$res->MoveNext();
}
$main.="</select>";

//顯示指定項目詳細資料
$sql_select="select * from charge_detail where item_id='$item_id' order by detail_sort";
$res=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);

$main.="<select name='detail_id' onchange='this.form.submit()'><option></option>";
while(!$res->EOF) {
	//取得各學年應收費金額
	$selected="";
	if($detail_id==$res->fields[detail_id])
	{
		$selected="selected";
		$grade_dollar=explode(',',$res->fields[dollars]);
		//陣列索引偏移值
		if ($IS_JHORES==0) $grade_offset=1; else $grade_offset=7;
		//print_r($grade_dollar);
	}
	$main.="<option $selected value=".$res->fields[detail_id].">".$res->fields[detail]."</option>";
	$res->MoveNext();
}
$main.="</select>";

if($detail_id)
{
//顯示指定項目的減免人員
$sql_select="select a.*,b.stud_name,left(a.curr_class_num,3) as class_id,right(a.curr_class_num,2) as class_no from charge_decrease a,stud_base b where a.detail_id='$detail_id' AND a.student_sn=b.student_sn AND a.curr_class_num like '$class_id%' order by curr_class_num";
$res=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
$listed=array();
$main.="　<img border=0 src='images/modify.gif' alt='編修選定減免學生'><select name='decrease_id' onchange='this.form.submit()'><option></option>";
while(!$res->EOF) {
	$main.="<option ".($decrease_id==$res->fields[decrease_id]?"selected":"")." value=".$res->fields[decrease_id].">[".$res->fields[curr_class_num]."]".$res->fields[stud_name]."->".$res->fields[cause]."</option>";
	$student_sn=$res->fields[student_sn];
	$listed[$student_sn][percent]=$res->fields[percent];
	$listed[$student_sn][cause]=$res->fields[cause];
	$res->MoveNext();
}
$main.="</select></table>";

$showdata="<table border='1' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' width='100%'>
	<tr bgcolor='#CCFF99'>
	<td align='center' size=5>NO.</td>
	<td align='center' size=5>班級</td>
	<td align='center' size=3>座號</td>
	<td align='center' size=20>學生姓名</td>
	<td align='center' size=30>應收款</td>
	<td align='center' size=30>減免數</td>
	<td align='center' size=30>減免額</td>
	<td align='center' size=30>應繳款</td>
	<td align='center' size=30>減免原因</td>
	<td align='center'><input type='submit' name='act' value='清空本項名單' onclick='return confirm(\"確定要\"+this.value+\"?\")'></td>
	</tr>";
	
	//<input type='reset' value='回復'>
$res->MoveFirst();
while(!$res->EOF) {
	$curr_grade=substr($res->fields[class_id],0,1)-$grade_offset;
	$my_decrease=round($grade_dollar[$curr_grade]*$res->fields[percent]/100);
	$my_dollar=$grade_dollar[$curr_grade]-$my_decrease;
	if($decrease_id==$res->fields[decrease_id]){
		$showdata.="<tr bgcolor=#AAFFCC><td align='center'>".($res->CurrentRow()+1)."</td>";
		$showdata.="<td align='center'>".$class_base[$res->fields[class_id]]."</td>";
		$showdata.="<td align='center'>".$res->fields[class_no]."</td>";
		$showdata.="<td align='center'>".$res->fields[stud_name]."</td>";
		$showdata.="<td align='center'>".$grade_dollar[$curr_grade]."</td>";
		$showdata.="<td align='center'><input type='text' name='percent' value='".$res->fields[percent]."' size=3>%</td>";
		$showdata.="<td align='center'>".$my_decrease."</td>";
		$showdata.="<td align='center'>".$my_dollar."</td>";
		$showdata.="<td align='center'><input type='text' name='cause' value='".$res->fields[cause]."' size=20></td>";
		//$showdata.="<td align='center'>".$res->fields[decrease_id]."</td>";
		$showdata.="<td align='center'><input type='submit' value='修改' name='act' onclick='return confirm(\"確定要更改[".$res->fields[stud_name]."]?\")'>　<input type='submit' value='刪除' name='act' onclick='return confirm(\"真的要刪除[".$res->fields[stud_name]."]?\")'></td></tr>";
	} else {	
		$showdata.="<tr bgcolor=#FFFFDD><td align='center'>".($res->CurrentRow()+1)."</td>";
		$showdata.="<td align='center'>".$class_base[$res->fields[class_id]]."</td>";
		$showdata.="<td align='center'>".$res->fields[class_no]."</td>";
		$showdata.="<td align='center'>".$res->fields[stud_name]."</td>";
		$showdata.="<td align='center'>".$grade_dollar[$curr_grade]."</td>";
		$showdata.="<td align='center'>".$res->fields[percent]."%</td>";
		$showdata.="<td align='center'>".$my_decrease."</td>";
		$showdata.="<td align='center'>".$my_dollar."</td>";
		
		$showdata.="<td align='center'>".$res->fields[cause]."</td>";
		$showdata.="<td></td>";

		//功能連結
		//$showdata.="<td align='center'>";
		//$showdata.="<a href='detail.php?item_id=".$res->fields[item_id]."'><img border=0 src='images/modify.gif' alt='設定細目'> </a>";
		//$showdata.="<a href='record.php?item_id=".$res->fields[item_id]."'><img border=0 src='images/sxw.gif' alt='印收費單'> </a>";
		//$showdata.="<a href='statistics.php?item_id=".$res->fields[item_id]."'><img border=0 src='images/sigma.gif' alt='收費統計'> </a>";
		//$showdata.="<a href='item.php?act=delete&item_id=".$res->fields[item_id]."'><img border=0 src='images/delete.gif' alt='刪除' onclick='return confirm(\"真的要刪除 $stud_name ?\")'></a>";
		$showdata.="</td></tr>";
	}
	$res->MoveNext();
}
if($item_id and $detail_id)
{
	//新增減免紀錄
	//取得班級學生列表
	$stud_select="select a.*,mid(a.record_id,5) as curr_class_num,b.stud_name,b.stud_sex from charge_record a,stud_base b where item_id=$item_id AND record_id like '$work_year_seme$class_id%' AND a.student_sn=b.student_sn order by record_id";
    $recordSet=$CONN->Execute($stud_select) or user_error("讀取失敗！<br>$stud_select",256);

	$col=9; //設定每一列顯示幾人
	$studentdata="<table border=1 cellpadding=3 cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width='100%'><tr><td colspan=$col bgcolor='#AAAAFF' align='center'>◎新增減免學生◎</td></tr>";
	while(!$recordSet->EOF)	{
		$student_sn=$recordSet->fields[student_sn];
		$curr_class_num=$recordSet->fields[curr_class_num];
		$stud_name=$recordSet->fields[stud_name];
		$class_no=substr($curr_class_num,-2);
		$stud_sex=$recordSet->fields[stud_sex];

		$pointer=($recordSet->currentrow() % $col)+1;
		if($pointer==1) $studentdata.="<tr>";
		if (array_key_exists($student_sn,$listed)) {
			$studentdata.="<td bgcolor=".($listed[$recordSet->fields[student_sn]-1]?"#CCCCCC":"#FFFFDD")." align='center'>($class_no)$stud_name<br>{$listed[$student_sn][cause]} {$listed[$student_sn][percent]}%</td>";
		} else {
			$studentdata.="<td bgcolor=".($stud_sex==1?"#CCFFCC":"#FFCCCC")." align='center'><input type='checkbox' name='selected_stud[]' value='$student_sn,$curr_class_num'>($class_no)$stud_name</td>";
		}
		if($pointer==$col or $recordSet->EOF) $studentdata.="</tr>";
		$recordSet->MoveNext();
	}
	$studentdata.="<tr><td align='center' colspan=$col>
					<input type='button' name='all_stud' value='全選' onClick='javascript:tagall(1);'>
					<input type='button' name='clear_stud'  value='全不選' onClick='javascript:tagall(0);'>
					　　　　◎減免原因：<input type='text' name='a_cause' value='$a_cause' size=20>　　 ◎減免數：<input type='text' name='a_percent' value='$a_percent' size=3>% 　
					<input type='submit' value='新增' name='act'  onclick='return confirm(\"確定要新增?\")'></td></tr></table>";
	

	/*
	
    if($class_id){
		while(!$recordSet->EOF)
		{
			$studentdata.="<option value='".$recordSet->fields[student_sn]."_".$recordSet->fields[curr_class_num]."'>(".substr($recordSet->fields[curr_class_num],-2).")".$recordSet->fields[stud_name]."</option>";
			$recordSet->MoveNext();
		}
	}
    $studentdata.="</select>";
	
	$showdata.="<tr></tr><tr bgcolor='#FFCCCC'><td align='center'><img border=0 src='images/add.gif' alt='單一學生新增'></td>";
	$showdata.="<td align='center'>$class_list";
	$showdata.="<td colspan=2 align='center'>$studentdata";
	$showdata.="<td align='center'>--</td><td align='center'><input type='text' name='a_percent' value='$a_percent' size=3>%</td>";
	$showdata.="<td align='center'>--</td><td align='center'>--</td><td align='center'><input type='text' name='a_cause' value='$a_cause' size=20></td>";
	$showdata.="<td align='center'><input type='submit' value='單一學生新增' name='act'></td></tr>";
	/*
	//以學生身分別批次新增
	//取得學生身份列表
	$type_select="SELECT d_id,t_name FROM sfs_text WHERE t_kind='stud_kind' AND d_id>0 order by t_order_id";
	$recordSet=$CONN->Execute($type_select) or user_error("讀取失敗！<br>$type_select",256);
	while (list($d_id,$t_name)=$recordSet->FetchRow()) { $typedata.="<option value='$d_id'>$t_name</option>"; }
	$typedata="<select name='subkind_id' onchange='this.form.b_cause.value=this.options[this.selectedIndex].text'><option></option>".$typedata."</select>";
	//echo $typedata;
	
	$showdata.="<tr></tr><tr bgcolor='#CCCCAA'><td align='center'><img border=0 src='images/batchadd.gif' alt='身分別批次新增'></td>";
	$showdata.="<td colspan=3 align='center'>$typedata";
	$showdata.="<td align='center'>--</td><td align='center'><input type='text' name='b_percent' value='$b_percent' size=3>%</td>";
	$showdata.="<td align='center'>--</td><td align='center'>--</td><td align='center'><input type='text' name='b_cause' value='$b_cause' size=20></td>";
	$showdata.="<td align='center'><input type='submit' value='身分批次新增' name='act'></td></tr><tr></tr>";
	*/
}
}
$showdata.="</table><br>$studentdata</form>";

echo $main.$showdata;

} else echo $not_allowed;
foot();
?>