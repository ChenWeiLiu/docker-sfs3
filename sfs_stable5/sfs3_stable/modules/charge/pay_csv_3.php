<?php
// $Id: pay_csv.php 5310 2009-01-10 07:57:56Z hami $

include "config.php";
include "my_fun.php";

sfs_check();

//學期別
$work_year_seme=$_REQUEST[work_year_seme];
if($work_year_seme=='') $work_year_seme = sprintf("%03d%d",curr_year(),curr_seme());
$curr_year_seme=sprintf("%03d%d",curr_year(),curr_seme());

$item_id=$_REQUEST[item_id];
$selected_stud=$_POST[selected_stud];
$dollars=$_POST[dollars];
$grade=substr($class_id,0,1);

// 取出班級名稱陣列
//$class_base= class_base($work_year_seme);

if($_POST['act']=='產生臺中商銀CSV'){
	//取得項目名稱
	$sql="select * from charge_item where item_id=$item_id";
	$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);

	$detail_list=get_item_detail_list($item_id);
	$student_arr=get_item_all_stud_list($item_id);
	
	$detail_column_array=array();
	$detail_column='';
	foreach($detail_lists as $key=>$value){
		$sql_select="select * from charge_detail where item_id=$item_id and detail_type=$key order by detail_sort";
		$res=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
		while(! $res->EOF){
			$detail_column_array[]=$res->fields['detail'];
			$detail_column.=','.$res->fields['detail'];
			 $res->MoveNext();		
		}
		for($i=$res->recordcount();$i<$value;$i++){
				$detail_column_array[]='';
				$detail_column.=','.($i+1);		
		}
	}
/*
echo "<PRE>";
echo $detail_column."<BR><BR>";
print_r($detail_column_array);

echo "</PRE>";
exit;	
*/	

	//開始輸出資料
	foreach($student_arr as $key=>$value){
	
		//echo "<pre>";
		//print_r($detail_list);	
		//echo "</pre>";
	
		$stud_id=$value['stud_id'];
		$stud_name=$value['stud_name'];
		$stud_birth=sprintf ("%03d%02d%02d", $value[birth_year],$value[birth_month],$value[birth_day]);
		$stud_class_no=substr($value['record_id'],-2);
		$stud_class_grade=substr($value['record_id'],4,1);
		$stud_class_serial=substr($value['record_id'],5,2);
		$stud_person_id=$value['stud_person_id'];
		//年級	班級	座號	姓    名	性別	身份別
		$row_data=",,,$stud_class_grade,$stud_class_serial,$stud_class_no,$stud_name,,";
		//foreach($detail_list as $key=>$detail_item){
		foreach($detail_column_array as $key=>$detail_item){
			$dollars=$value['detail']["$detail_item"]['original'];
			$decrease_dollars=$value['detail']["$detail_item"]['decrease_dollars'];
			if($detail_item) $need_to_pay=$dollars-$decrease_dollars; else $need_to_pay='';
			$row_data.=",".$need_to_pay;
		}
		$data.="$row_data\n";
}

	//檔名
	$filename=$work_year_seme."_臺中商銀收費清冊".$res->fields[item].".csv";
	
	################################    輸出 CSV    ##################################
	$Str="科系代號,部別,科系年級班級,年級,班級,座號,姓    名,性別,身份別".$detail_column;

	$Str.="\n".$data;

	header("Content-disposition: attachment; filename=$filename");
	header("Content-type: text/x-csv");
	//header("Pragma: no-cache");
					//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");

	header("Expires: 0");
	echo $Str;
	exit;	
};

//秀出網頁
head("收費管理");

print_menu($menu_p);

//橫向選單標籤
$linkstr="work_year_seme=$work_year_seme&item_id=$item_id";
echo print_menu($MENU_P,$linkstr);


//取得年度與學期的下拉選單
$seme_list=get_class_seme();
$main="<BR>※輸出本CSV，若發現所有收費金額皆為 0，請調整模組變數 is_sort(項目前導排序代號)，將其設為'否'。<BR>※使用前，請在細目設定中將收費細目設定好'收歸帳戶'。";
$main.="<BR>※收歸帳戶選項與各項欄位數請調整模組變數'細目收歸帳戶參照'及'細目收歸帳戶參照列表欄位數'，未見此二變數，請按取回預設值。<BR><BR>";
$main.="<table border='1' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#AAAAAA' width='100%'><form name='myform' method='post' action='$_SERVER[PHP_SELF]'>
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
	//$class_list=get_item_class($item_id,$class_base,$class_id);
	$main.=$class_list."<input type='submit' value='產生臺中商銀CSV' name='act'>";
}
echo $main.$studentdata."</form></table>";
foot();
?>
