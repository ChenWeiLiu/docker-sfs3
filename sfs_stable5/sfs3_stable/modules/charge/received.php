<?php

// $Id: received.php 5310 2009-01-10 07:57:56Z hami $



include "config.php";

include "my_fun.php";



sfs_check();



//秀出網頁

head("收費管理");



print_menu($menu_p);

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

$work_year_seme=$_REQUEST[work_year_seme];
if($work_year_seme=='') $work_year_seme = sprintf("%03d%d",curr_year(),curr_seme());
$curr_year_seme=sprintf("%03d%d",curr_year(),curr_seme());

$item_id=$_REQUEST[item_id];

$class_id=$_POST[class_id];
$selected_stud=$_POST[selected_stud];
$paid_date=$_POST[paid_date];
$dollars=$_POST[dollars];
$grade=substr($class_id,0,1);



// 取出班級名稱陣列

$class_base = class_base($work_year_seme);

//橫向選單標籤

$linkstr="work_year_seme=$work_year_seme&item_id=$item_id";

echo print_menu($MENU_P,$linkstr);

if($selected_stud AND $_POST['act']=='繳款設定'){

	if( $item_id AND $class_id)

	{

		//抓取選擇的班級學生

		$batch_value="";

		foreach($selected_stud as $stud_datas)

		{

			$stud_data=explode(',',$stud_datas);

			$sn=$stud_data[0];

			$record_id=$stud_data[1];

			

			$batch_value.="('$record_id',$sn,$item_id,$dollars,'$paid_date'),";

		}

		$batch_value=substr($batch_value,0,-1);

		//echo "===================<BR>$batch_value<BR>===================";

		

		$sql_select="REPLACE INTO charge_record(record_id,student_sn,item_id,dollars,paid_date) values $batch_value";

		

		$res=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);

	} else echo "<script language=\"Javascript\"> alert (\"資訊不足, 無法身分別批次新增！\")</script>";

};





if($_POST['act']=='清空本班級繳款設定'){

	$sql_select="update charge_record set dollars=0,paid_date=NULL where item_id=$item_id AND record_id like '$work_year_seme$class_id%'";

	$res=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);

};





//取得年度與學期的下拉選單

$seme_list=get_class_seme();

$main="<table border='1' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#AAAAAA' width='100%'><form name='myform' method='post' action='$_SERVER[PHP_SELF]'>

	<select name='work_year_seme' onchange='this.form.submit()'>";

foreach($seme_list as $key=>$value){

	$main.="<option ".($key==$work_year_seme?"selected":"")." value=$key>$value</option>";

}

$main.="</select><select name='item_id' onchange='this.form.submit()'><option></option>";



//取得年度項目

$sql_select="select * from charge_item where year_seme='$work_year_seme' order by end_date desc";

$res=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);



while(!$res->EOF) {

	$main.="<option ".($item_id==$res->fields[item_id]?"selected":"")." value=".$res->fields[item_id].">".$res->fields[item]."(".$res->fields[start_date]."~".$res->fields[end_date].")</option>";

	$res->MoveNext();

}

$main.="</select>";

if($item_id)

{

	//顯示班級

	$class_list=get_item_class($item_id,$class_base,$class_id);
	$main.=$class_list;



	if($class_id)

	{

		//取得前已開列學生資料

		$sql_select="select a.record_id,a.student_sn,a.dollars,b.stud_name,b.stud_sex from charge_record a,stud_base b where a.student_sn=b.student_sn AND item_id=$item_id AND record_id like '$work_year_seme$class_id%' order by record_id";
		$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);

		$col=7; //設定每一列顯示幾人

		$studentdata="";

		while(list($record_id,$student_sn,$dollars,$stud_name,$stud_sex)=$recordSet->FetchRow()) {

			//echo $recordSet->currentrow()."==<BR>";			

			if($recordSet->currentrow() % $col==1) $studentdata.="<tr>";

			if($dollars) {

				$studentdata.="<td bgcolor='#CCCCCC' align='center'>(".substr($record_id,-2).")$stud_name<BR>＄ $dollars</td>";

			} else {

				$studentdata.="<td bgcolor=".($stud_sex==1?"#CCFFCC":"#FFCCCC")."><input type='checkbox' name='selected_stud[]' value='$student_sn,$record_id' id='stud_selected'>(".substr($record_id,-2).")$stud_name</td>";

			}

			if($recordSet->currentrow() % $col==0  or $recordSet->EOF) $studentdata.="</tr>";

		}

		$studentdata.="<tr height='50'><td align='center' colspan=$col><input type='button' name='all_stud' value='全選' onClick='javascript:tagall(1);'><input type='button' name='clear_stud'  value='全不選' onClick='javascript:tagall(0);'>　";

		$studentdata.="　繳費日期：<input type='text' size=10 value='".date('Y-m-d',time())."' name='paid_date'>";
		$studentdata.="　金額：<input type='text' size=6 value='' name='dollars'>";

		$studentdata.="<input type='submit' value='繳款設定' name='act'>　＄：已繳款";

		$studentdata.="　<input type='submit' value='清空本班級繳款設定' name='act' onclick='return confirm(\"確定要\"+this.value+\"?\")'></td></tr>";

	}

}

echo $main.$studentdata."</form></table>";

foot();

?>