<?php

// $Id: teach_print2.php 5310 2009-01-10 07:57:56Z hami $

//載入設定檔
include "config.php";


// --認證 session 
sfs_check();



if(empty($sel_year))$sel_year = curr_year(); //目前學年
if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期


//更改在職狀態
if ($c_sel != "")
	$sel = $c_sel;
else if ($sel=="")
	$sel = 0 ; //預設選取在職狀況
	


	
//執行動作判斷

$main=&main_form($sel_year,$sel_seme , $sel );



//秀出網頁
head("教職員通訊錄列印");
 $tool_bar=&make_menu($school_menu_p);
 echo $tool_bar;

echo $main;
foot();

//主要畫面
function &main_form($sel_year,$sel_seme , $sel){
	global $button;
	
	//取得教師資料
	$row=&get_teacher_data(   $sel);

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


		$teach_name = $row[$i]["name"];

		$address = $row[$i]["address"];
		$home_phone = $row[$i]["home_phone"];
		$cell_phone = $row[$i]["cell_phone"];
		//轉換民國日期
		$birthday=( substr($birthday,0,4)>1911)?(substr($birthday,0,4) - 1911). substr($birthday,4):"";
	
		$color= ($i%2 == 1) ? "white" : "#fafafa";
		
		$t_data.= "
		<tr bgcolor='$color' class='small'>
		<td>$job</td>
		<td>$teach_name</td>
		<td>$address</td>
		<td>$home_phone</td><td>$cell_phone</td></tr>\n";
	}
	
	
	$main="
	<table cellspacing='1' cellpadding='4' align='center' bgcolor='#C0C0C0'>
	<tr bgcolor='#FFFFB9'><td colspan='6' class='small'>
	<form action='{$_SERVER['PHP_SELF']}' method='post'>
	$upstr  <font color='#800000'>共有 $i 筆資料</font> 
	
	<tr bgcolor='#D0D8F7' class='small'><td>職稱</td><td>姓名</td><td>地址</td><td>電話</td><td>行動電話</td>
	</tr>
	$t_data
	</table>
	</form>";

	return $main;
}



//抓取教師資料，包括〈teach_person_id,name,birthday,address,home_phone,title_name,class_num〉
function &get_teacher_data( $sel = 0 ){
	global $CONN;
	
	//抓取教師資料
	$sql_select = "
	SELECT a.teach_person_id , a.name, a.birthday, a.address, a.home_phone, a.cell_phone , d.title_name ,b.class_num 
	FROM  teacher_base a , teacher_post b, teacher_title d 
	where  a.teacher_sn =b.teacher_sn  
	and b.teach_title_id = d.teach_title_id  
	and a.teach_condition = '$sel'  order by class_num, post_kind , post_office , a.teach_id "  ;              
	
	$recordSet=$CONN->Execute($sql_select) or user_error($sql_select,256);
	while($row=$recordSet->FetchRow()){
		$data[]=$row;
	}
	
	return $data;
}
?>
