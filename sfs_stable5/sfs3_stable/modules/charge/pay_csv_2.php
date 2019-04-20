<?php
// $Id:  $

include "config.php";
include "my_fun.php";

sfs_check();

//學期別
$work_year_seme=$_POST[work_year_seme];
if($work_year_seme=='') $work_year_seme = sprintf("%03d%d",curr_year(),curr_seme());
$curr_year_seme=sprintf("%03d%d",curr_year(),curr_seme());

$item_type=$_POST['item_type'];
$max_sort=$_POST['max_sort'];

$grade=substr($class_id,0,1);

if($_POST['act']=='產生CSV'){
	//取得"指定學期"與"類別"中所有的收費細目
	$detail_list_all=array();
	$student_arr_all=array();
	
	$sql="select distinct item_id from charge_item where year_seme='$work_year_seme' AND item_type='$item_type' ORDER BY item_id";
	$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
	while(!$res->EOF) {
		$item_id_list.=$res->fields['item_id'].',';		
		$res->MoveNext();
	}
	$item_id_list=substr($item_id_list,0,-1);
	
	$detail_list=get_item_detail_list_multi($item_id_list);
	ksort($detail_list);
	$student_arr=get_item_all_stud_list_multi($item_id_list);
	
	ksort($student_arr); //以班級座號排序

	//開始輸出資料
	foreach($student_arr as $key=>$value)
	{
		$study_year=substr($value['stud_id'],0,2);
		$stud_name=$value['stud_name'];
		$stud_class_no=substr($value['record_id'],-2);
		$stud_class_serial=substr($value['record_id'],5,2);
		
		$row_data="$study_year,$stud_name,$stud_class_serial,$stud_class_no";

		for($i=1;$i<=$max_sort;$i++) {
			if (array_key_exists($i,$value['detail'])) {
				$detail_item=$value['detail'][$i][item];
				$dollars=$value['detail'][$i]['original'];
				$decrease_dollars=$value['detail'][$i]['decrease_dollars'];
				$need_to_pay=$dollars-$decrease_dollars;
				$row_data.=",$detail_item,$need_to_pay";
			} else $row_data.=",,";
		}
		$data.="$row_data\n";
	}

//echo "<pre>";
//print_r($student_arr);
//echo "</pre><BR><BR>";
//exit;	
	
	//檔名
	$filename=$work_year_seme."_收費清冊(中國信託版)".$item_type.".csv";
	
	################################    輸出 CSV    ##################################
	$Str="入學學年度,學生姓名,班級,座號";
	//抬頭列加上收費細目
	for($i=1;$i<=$max_sort;$i++) {
			$Str.=",學細項$i,學金額$i";	
	}

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
$main="※說明：
<li>本CSV輸出係針對學校委託中國信託商業銀行辦理學生收費事宜所設計。</li>
<li>該CSV的格式欄位採固定序號模式，並有分類間隔。</li>
<li>格式規範：入學學年度	學生姓名	班級	座號	學細項1	學金額1	學細項2	學金額2.......學細項15	學金額15。</li>
<li>可進行同學期多收費項目的細目統合輸出，唯須將其設為同一個類別，並且 1.排序不可重複 2.排序須為數字。</li>
<li>細目的排序號碼即為輸出時的欄位序碼。</li>
<li>輸出的檔案型態為ＣＳＶ，請於ＥＸＣＥＬ中開啟並另存為ＸＬＳ。</li>
<BR><BR>※輸出：


";
$main.="<table border='1' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#AAAAAA' width='100%'><form name='myform' method='post' action='$_SERVER[PHP_SELF]'>
	　◎收費類別：<select name='work_year_seme' onchange='this.form.submit()'>";
foreach($seme_list as $key=>$value){
	$main.="<option ".($key==$work_year_seme?"selected":"")." value=$key>$value</option>";
}
$main.="</select><select name='item_type' onchange='this.form.submit()'><option></option>";

//取得學期類別
$sql_select="select item_type,count(*) as item_count from charge_item where year_seme='$work_year_seme' group by item_type order by item_type";
$res=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);

while(!$res->EOF) {
	$main.="<option ".($item_type==$res->fields['item_type']?"selected":"")." value='".$res->fields['item_type']."'>".$res->fields['item_type']."(".$res->fields['item_count'].")</option>";
	$res->MoveNext();
}
$main.="</select>";


//顯示處理按鈕
if($item_type)
{
	$error_message='';
	//抓取類別細目
	$detail_array=array();
	$max_sort=0;
	$sql_select="select a.*,b.item,b.item_type from charge_detail a,charge_item b where a.item_id=b.item_id AND b.year_seme='$work_year_seme' AND b.item_type='$item_type' ORDER BY detail_sort";
//echo "$sql_select<BR>";

	$res=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	while(!$res->EOF) {
		$detail_sort=$res->fields['detail_sort'];
		if($detail_sort>$max_sort) $max_sort=$detail_sort;
		$detail_array[$detail_sort]['detail'].=$res->fields['item']."=>".$res->fields['detail'].';';
		$detail_array[$detail_sort]['counter']+=1;
		$res->MoveNext();
	}
	//開始檢查
	$detail_messdage='';
	$error_count=0;
	foreach($detail_array as $key=>$value)
	{
		$detail=$value['detail'];
		$counter=$value['counter'];
		if($value['counter']>1 or $key==0) { $error_count+=1; $show_color='red'; } else  { $show_color='green'; }
		$detail_messdage.="<font color='$show_color'><BR>　　▲ $key($counter)  $detail</font>";
	}
	$max_sort=max($max_sort,15);
	if(!$error_count) $class_list.=" ▲細目欄位數:<input type='text' name='max_sort' value='$max_sort' size=3> <input type='submit' value='產生CSV' name='act'>";
}
echo "$main $class_list <BR> $detail_messdage</form></table>";
foot();
?>
