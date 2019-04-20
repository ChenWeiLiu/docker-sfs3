<?php

// $Id: teach_print2.php 7712 2013-10-23 13:31:11Z smallduh $

//載入設定檔
include "teach_report_config.php";
include "../../include/sfs_oo_zip2.php";

// --認證 session 
sfs_check();

// 不需要 register_globals
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}

//更改在職狀態
if ($c_sel != "")
	$sel = $c_sel;
else if ($sel=="")
	$sel = 0 ; //預設選取在職狀況 
	
	
$button["sxw"]="OpenOffice.org Writer 檔";
$button["csv"]="純文字的 csv 檔";
$button["Word"]="MS Office Word 檔";
$button["Excel"]="MS Office Excel 檔";

if(empty($sel_year))$sel_year = curr_year(); //目前學年
if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期


//執行動作判斷
if($_POST['Submit'] == '匯出') {
    if (isset($print_key) and $print_key=="sxw"){
    	dl_sxw($sel_year,$sel_seme,$cols, $sel);
    }elseif(isset($print_key) and $print_key=="csv"){
    	dl_csv($sel_year,$sel_seme,$cols, $sel);
    }elseif(isset($print_key)){
    	print_key($sel_year,$sel_seme,$print_key,$cols, $sel);
    }
}else{
	$main=&main_form($sel_year,$sel_seme, $sel);
}


//秀出網頁
head("教職員通訊錄列印");

echo $main;
foot();

//主要畫面
function &main_form($sel_year,$sel_seme , $sel =0 ){
	global $button;
	//取得教師資料
	$row=&get_teacher_data( $sel);
	
	//資料格式選單
	$import_option="";
	while(list($k,$v)=each($button)){
		$import_option.="<option value='$k'>$v</option>\n";
	}
		
	$import_sel="<select name='print_key' size='1'>$import_option</select>";
	
	$remove_p = remove(); //在職狀況    
	$upstr = "顯示<select name=\"c_sel\" onchange=\"this.form.submit()\">\n"; 
	while (list($tid,$tname)=each($remove_p)){
		if ($sel== $tid)
			$upstr .= "<option value=\"$tid\" selected>$tname</option>\n";
		else
			$upstr .= "<option value=\"$tid\">$tname</option>\n";
	}
	$upstr .= "</select>"; 		
	
	$t_data="";
	for($i=0;$i<sizeof($row);$i++){
		$job = $row[$i]["title_name"];
		if ($row[$i]["class_num"]) {
			//級任 
			$job = class_id2big5($row[$i]["class_num"],$sel_year,$sel_seme);
		}
		
		$teach_person_id = $row[$i]["teach_person_id"];
		$teach_name = $row[$i]["name"];
		$birthday = $row[$i]["birthday"];
		$address = $row[$i]["address"];
		$home_phone = $row[$i]["home_phone"];
		
		//轉換民國日期
		$birthday=( substr($birthday,0,4)>1911)?(substr($birthday,0,4) - 1911). substr($birthday,4):"";
	
		$color= ($i%2 == 1) ? "white" : "#fafafa";
		
		$t_data.= "
		<tr bgcolor='$color' class='small'>
		<td>$job</td>
		<td>$teach_person_id</td>
		<td>$teach_name</td>
		<td>$birthday</td>
		<td>$address</td>
		<td>$home_phone</td></tr>\n";
	}
	
	
	$main="
	<table cellspacing='1' cellpadding='4' align='center' bgcolor='#C0C0C0'>
	<tr bgcolor='#FFFFB9'><td colspan='6' class='small'>
	<form action='{$_SERVER['PHP_SELF']}' method='post'>
	$upstr <font color='#800000'>共有 $i 筆資料</font>
	匯出成： $import_sel
	空白欄數：<input type=text size=3 maxlength=2 name='cols' value='$cols'>
	<input type='submit' name='Submit' value='匯出'></td></tr>
	<tr bgcolor='#D0D8F7' class='small'><td>職稱</td><td>身分証字號</td><td>姓名</td><td>生日</td><td>地址</td><td>電話</td>
	</tr>
	$t_data
	</table>
	</form>";

	return $main;
}

//列印文件
function print_key($sel_year="",$sel_seme="",$print_key="",$cols="" ,$sel =0){
	global $CONN,$button;
	
	//抓取教師資料
	$row=&get_teacher_data($sel);
	
	//最多欄位數
	if ($cols > 20 )	$cols = 20;
	
	//製作新增的欄位
	for ($j =0 ;$j< $cols ;$j++){
		$add_col.="<td>&nbsp;</td>";
	}
	
	//製作主要資料
	for($i=0;$i<sizeof($row);$i++){
		$job = $row[$i][title_name] ;
		if ($row[$i][class_num]) {
			//級任 
			$job = class_id2big5($row[$i][class_num],$sel_year,$sel_seme);
		}
		
		$teach_person_id = $row[$i]["teach_person_id"];
		$teach_name = $row[$i]["name"];
		$birthday = $row[$i]["birthday"];
		
		//在excel 中不轉換民國日期
       	if (substr($birthday,0,4)>1911) {
       	    if ($print_key=="Word")	$birthday = (substr($birthday,0,4) - 1911). substr($birthday,4) ;
		}else{
       	    $birthday = " " ; 
		}
		
		$word_add_col=($print_key =="Word")?$add_col:"";
		
       	$address = $row[$i]["address"];
       	$home_phone = $row[$i]["home_phone"];
       
       	$main_data.="
		<tr>
		<td>$job</td>
		<td>$teach_person_id</td>
		<td>$teach_name</td>
		<td>$birthday</td>
		<td>$address</td>
		<td>$home_phone</td>
		$word_add_col
		</tr>\n";
	}
	
	//轉出為excel、word	
	if ($print_key=="Excel")
		$filename =  "教職員通訊錄.xls"; 	
	else if ($print_key=="Word")
		$filename =  "教職員通訊錄.doc";
	header("Content-disposition: filename=$filename");
	header("Content-type: application/octetstream");
	//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
	header("Expires: 0");
	
	echo "
	<html>
	<meta http-equiv=\"Content-Type\" content=\"text/html; charset=big5\">
	<body><table border=1>
	<tr><td colspan=".($cols+6)." align=center>
	教職員通訊錄</td></tr>
	<tr>
	<td>職稱</td><td>身分証字號</td><td>姓名</td><td>生日</td><td>地址</td><td>電話</td>$add_col</tr>
	$main_data
	</table></body><html>";
	exit;
}

//下載標準sxw檔
function dl_sxw($sel_year,$sel_seme,$cols, $sel =0){
	global $CONN;
	//取得學校資料
	$s=get_school_base();
	$oo_path = "ooo";
	$filename="teacher_data.sxw";
	
	//抓取教師資料
	$row=&get_teacher_data($sel);
	
	
	//文字格式，P3左右對齊，P4置中，P5置左
	$text_style=array("job"=>"P4","teach_person_id"=>"P5","teach_name"=>"P5","birthday"=>"P5","address"=>"P6","home_phone"=>"P5");
	
	$all_n=sizeof($row);
	//一列的資料
	for($i=0;$i<$all_n;$i++){
		
		$n=(($all_n-$i)==1)?4:2;
		//表格格式，最左用A2，最右用F2，其餘用B2，最後一行2變成4
		$table_style=array("job"=>"Table1.A".$n."","teach_person_id"=>"Table1.B".$n."","teach_name"=>"Table1.B".$n."","birthday"=>"Table1.B".$n."","address"=>"Table1.B".$n."","home_phone"=>"Table1.F".$n."");
	
		//職稱
		$c[job]=trim($row[$i][title_name]);
		if ($row[$i][class_num]) {
			//級任 
			$c[job]=class_id2big5($row[$i][class_num],$sel_year,$sel_seme);
		}
		
		$c[teach_person_id]=trim($row[$i]["teach_person_id"]);
		$c[teach_name]=trim($row[$i]["name"]);
		$birthday=trim($row[$i]["birthday"]);
		$c[birthday]=(substr($birthday,0,4)>1911)?(substr($birthday,0,4) - 1911). substr($birthday,4):$birthday; 
		$c[address]=trim($row[$i]["address"]);
       	$c[home_phone]=trim($row[$i]["home_phone"]);
		
		$cell="";
		reset($c);
		while(list($col_name,$col_value)=each($c)){
			$cell.=cell($table_style[$col_name],"string",$text_style[$col_name],$col_value);
		}
		/*
		for($j=0;$i<$cols;$j++){
			$cell.=cell($table_style[$teach_name],"string",$text_style[$teach_name],"");
		}
		*/
		$row_data.="<table:table-row>$cell</table:table-row>";

	}

	//新增一個 zipfile 實例
	$ttt = new EasyZip;
	$ttt->setPath($oo_path);
	$ttt->addDir("META-INF");
	$ttt->addFile("settings.xml");
	$ttt->addFile("styles.xml");
	$ttt->addFile("meta.xml");

	//讀出 content.xml 
	$data = $ttt->read_file(dirname(__FILE__)."/$oo_path/content.xml");

	//將 content.xml 的 tag 取代
	$temp_arr["city_name"] = $s[sch_sheng];	
	$temp_arr["school_name"] = $s[sch_attr_id].$s[sch_cname];
	$temp_arr["year"] = $sel_year;
	$temp_arr["seme"] = $sel_seme;
	$temp_arr["row_data"] = $row_data;
	
	// change_temp 會將陣列中的 big5 轉為 UTF-8 讓 openoffice 可以讀出
	$replace_data = $ttt->change_temp($temp_arr,$data,0);
	
	// 加入 content.xml 到zip 中
	$ttt->add_file($replace_data,"content.xml");
	
	//產生 zip 檔
	$sss = & $ttt->file();

	//以串流方式送出 ooo.sxw
	header("Content-disposition: attachment; filename=$filename");
	//header("Content-type: application/octetstream");
	header("Content-type: application/vnd.sun.xml.writer");
	//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
	header("Expires: 0");

	echo $sss;
	
	exit;
	return;
}

//下載標準csv檔
function dl_csv($sel_year,$sel_seme,$cols, $sel =0){
	global $CONN;
	$filename="teacher_data.csv";
	
	//抓取教師資料
	$row=&get_teacher_data($sel);

	$all_n=sizeof($row);
	//一列的資料
	for($i=0;$i<$all_n;$i++){

		//職稱
		$c[job]="\"".trim($row[$i][title_name])."\"";
		if ($row[$i][class_num]) {
			//級任 
			$c[job]="\"".class_id2big5($row[$i][class_num],$sel_year,$sel_seme)."\"";
		}
		
		$c[teach_person_id]="\"".trim($row[$i]["teach_person_id"])."\"";
		$c[teach_name]="\"".trim($row[$i]["name"])."\"";
		$birthday=trim($row[$i]["birthday"]);
		$c[birthday]=(substr($birthday,0,4)>1911)?"\"".(substr($birthday,0,4) - 1911). substr($birthday,4)."\"":"\"".$birthday."\""; 
		$c[address]="\"".trim($row[$i]["address"])."\"";
       	$c[home_phone]="\"".trim($row[$i]["home_phone"])."\"";
		

		reset($c);
		$row_data[]=implode(",",$c);
	
	}

	$main=implode("\n",$row_data);
	
	//以串流方式送出 ooo.csv
	header("Content-disposition: attachment; filename=$filename");
	header("Content-type: text/x-csv");
	//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
	header("Expires: 0");

	echo $main;
	
	exit;
	return;
}

//製造OOo的文件欄位
function cell($table_style,$value_type,$text_style,$text){
	$cell="<table:table-cell table:style-name=\"$table_style\" table:value-type=\"$value_type\"><text:p text:style-name=\"$text_style\">$text</text:p></table:table-cell>";
	return $cell;
}

//抓取教師資料，包括〈teach_person_id,name,birthday,address,home_phone,title_name,class_num〉
function &get_teacher_data( $sel = 0 ){
	global $CONN;
	
	//抓取教師資料
	$sql_select = "
	SELECT a.teach_person_id , a.name, a.birthday, a.address, a.home_phone, d.title_name ,b.class_num 
	FROM  teacher_base a , teacher_post b, teacher_title d 
	where  a.teacher_sn =b.teacher_sn  
	and b.teach_title_id = d.teach_title_id  
	and a.teach_condition = '$sel'   order by class_num, post_kind , post_office , a.teach_id "  ;              
	
	$recordSet=$CONN->Execute($sql_select) or user_error($sql_select,256);
	while($row=$recordSet->FetchRow()){
		$data[]=$row;
	}
	
	return $data;
}
?>
