<?php

include "config.php";

sfs_check();

$curr_class_id=$_POST['curr_class_id'];
$selected_stud=$_POST['selected_stud'];
$data_scope=$_POST['data_scope'];
$checked_sch=$_POST['sch_name']?$_POST['sch_name']:'';

//$data_scope_array=array('1'=>'所有細目','2'=>'學年認證細目');


if($_POST['act']){
	if($selected_stud){ 
		$Barcode_Font=$m_arr['Barcode_Font'];
		$Barcode_Font_size=$m_arr['Barcode_Font_size'];
		
		$curr_class_grade=substr($curr_class_id,0,-2);
		$teacher_array=teacher_array();
		
		//取得認證項目陣列
		foreach($_POST['selected_item'] as $value) $selected_item_list.=$value.',';
		$selected_item_list=substr($selected_item_list,0,-1);
	
		$item_array=array();
		$sql="select * from authentication_item where sn in ($selected_item_list) order by nature,code";
		$res_item=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
		while(!$res_item->EOF){
			$sn=$res_item->fields['sn'];
			$item_array[$sn]['code']=$res_item->fields['code'];
			$item_array[$sn]['title']=$res_item->fields['title'];
			$item_array[$sn]['nature']=$res_item->fields['nature'];
			$item_array[$sn]['room_id']=$res_item->fields['room_id'];
			$res_item->MoveNext();
		}
	
		//取得認證細目陣列
		$sql="select * from authentication_subitem where item_sn in ($selected_item_list) order by item_sn,code";
		$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
		while(!$res->EOF){
			$item_sn=$res->fields['item_sn'];
			$sn=$res->fields['sn'];
			$item_array[$item_sn]['sub_item'][$sn]['code']=$res->fields['code'];
			$item_array[$item_sn]['sub_item'][$sn]['title']=$res->fields['title'];
			$item_array[$item_sn]['sub_item'][$sn]['bonus']=$res->fields['bonus'];
			$item_array[$item_sn]['sub_item'][$sn]['grades']=$res->fields['grades'];
			$res->MoveNext();
		}
		
		foreach($selected_stud as $student_sn)
		{
			//抓取選取學生的認證紀錄
			$sql="select year_seme,sub_item_sn,date,score,teacher_sn from authentication_record WHERE student_sn=$student_sn ORDER BY sub_item_sn";
			$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
			while(!$res->EOF){
				$subitem_sn=$res->fields['sub_item_sn'];
				$record_array[$student_sn][$subitem_sn]['year_seme']=$res->fields['year_seme'];
				$record_array[$student_sn][$subitem_sn]['date']=$res->fields['date'];
				$record_array[$student_sn][$subitem_sn]['note']=$res->fields['note'];
				$record_array[$student_sn][$subitem_sn]['teacher_sn']=$res->fields['teacher_sn'];
				//分數顯示
				$my_score=$res->fields['score'];
				if($my_score) $score_display=$my_score; else $score_display=$m_arr['zero_display'];
				if($my_score>100) $score_display=$m_arr['over_100_display'];
				$record_array[$student_sn][$subitem_sn]['score']=$score_display;
				$res->MoveNext();
			}
			//開始輸出HTML
			//foreach($record_array as $student_sn=>$items)
			//{
				//印學校抬頭
				$title_font_size=$m_arr['title_font_size'];
				$student_data=$checked_sch?"<CENTER><P style='font-size:$title_font_size'>$school_long_name".curr_year().'學年度第'.curr_seme().'學期'."學習認證卡</P></CENTER>":'';
				
				//取得學生基本資料
				$sql="select stud_id,stud_name,curr_class_num from stud_base WHERE student_sn=$student_sn";
				$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
				$stud_id=$res->fields[stud_id];
				$stud_name=$res->fields[stud_name];
				$curr_class_num=$res->fields[curr_class_num];
				
				$class_id=substr($curr_class_num,0,-2);
				$class_no=substr($curr_class_num,-2);
				$class_name=$class_base[$class_id];
				
				$student_data.=$m_arr['header'].'<br>';
				$person_font_size=$m_arr['person_font_size'];
				$student_data.="<P align='center' style='font-size:$person_font_size'>※班級：$class_name 　※座號：$class_no 　※學號：$stud_id 　※姓名：$stud_name</P>";
				$text_font_size=$m_arr['text_font_size'];
				$student_data.="<table border=2 cellpadding=3 cellspacing=0 style='border-collapse:collapse; font-size:$text_font_size' bordercolor='#111111' width='100%'>";
				$student_data.="<tr align='center' bgcolor='#FFCCCC'><td>類別</td><td>認證項目</td><td>認證細目</td><td>適用年級</td><td>積分</td><td>認證日期</td><td>認證簽章<td>掃瞄條碼 / 成績</td></td><td>備註</td></tr>";
				
				$item_data='';
				foreach($item_array as $item_sn=>$item_values) {
					$item_data='<td align=center>'.$item_values[nature].'</td><td>( '.$item_values[code].' )<br>'.$item_values[title].'</td>';
					$record_data_list='';
					$row_span=count($item_values['sub_item']);
					foreach($item_values['sub_item'] as $subitem_sn=>$record_data) {
						$suitable_grades=' ,'.$record_data['grades'].',';
						if(strpos($suitable_grades,$curr_class_grade)){
							if($record_array[$student_sn][$subitem_sn]['year_seme']){
								$teacher_sn=$record_array[$student_sn][$subitem_sn]['teacher_sn'];
								$teacher_name=$teacher_array[$teacher_sn];
								$student_data.="<tr bgcolor='#ffffcc'>$item_data<td>( {$record_data['code']} )<br>{$record_data['title']}</td><td align='center'>{$record_data['grades']}</td><td align='center'>{$record_data['bonus']}</td><td align='center'>{$record_array[$student_sn][$subitem_sn]['year_seme']}<br>{$record_array[$student_sn][$subitem_sn]['date']}</td><td align='center'>{$teacher_name}</td><td align='center'>{$record_array[$student_sn][$subitem_sn]['score']}</td><td align='center'>{$record_array[$student_sn][$subitem_sn]['note']}</td></tr>";
							} else {
								$barcode="<img src='../../include/sfs_barcode.php?code=$student_sn-$subitem_sn&height={$m_arr['Barcode_height']}'>";
								$student_data.="<tr>$item_data<td>( {$record_data['code']} )<br>{$record_data['title']}</td><td align='center'>{$record_data['grades']}</td><td align='center'>{$record_data['bonus']}</td><td></td><td></td><td align='center'>$barcode</td><td></td></tr>";
							}
						}
					}
				}
				$student_data.="</table>";
				//頁腳
				$student_data.=$m_arr['footer'];
				//換頁
				$student_data.="<P style='page-break-after:always'></P>";
				echo $student_data;				
			//}		
		}
	} else echo "<br><font color='red' size=6>您尚未選取任何學生！</font>";
	exit;	
}


//秀出網頁
head("認證表");

echo <<<HERE
<script>
function check_select(class_id) {
  var i=0; j=0; k=0; answer=true;
  while (i < document.myform.elements.length)  {
    if(document.myform.elements[i].checked) {
		if(document.myform.elements[i].name=='selected_stud[]') j++;
		if(document.myform.elements[i].name=='selected_item[]') k++;
    }
    i++;
  }  
  if(k==0) { alert("尚未選取認證項目！"); answer=false; }
  if(j==0) { alert("尚未選取學生！"); answer=false; }
 
 document.myform.target=class_id;

  return answer;
}
function tagall(status,obj) {
  var i =0;

  while (i < document.myform.elements.length)  {
    if (document.myform.elements[i].name==obj) {
      document.myform.elements[i].checked=status;
    }
    i++;
  }
}
</script>
HERE;


//橫向選單標籤
echo print_menu($MENU_P);


$main="<form name='myform' method='post' action='$_SERVER[SCRIPT_NAME]'>";

//顯示認證項目(自動核取目前認證學期的項目)

$col=5; //設定每一列顯示幾班
$sql_select="select *,if(CURDATE() between start_date and end_date,1,0) as ing from authentication_item order by nature,code";
$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
$item_data="<br>※認證項目(藍色底為目前認證中的項目)：<input type='button' name='all_stud' value='全選' onClick='javascript:tagall(1,\"selected_item[]\");'>
				<input type='button' name='clear_stud'  value='全不選' onClick='javascript:tagall(0,\"selected_item[]\");'>
				<table border=1 cellpadding=5 cellspacing=0 style='border-collapse: collapse;' bordercolor='#111111'>";
while(!$recordSet->EOF) {
	$ing=$recordSet->fields['ing'];
	$sn=$recordSet->fields['sn'];
	$nature=$recordSet->fields['nature'];
	$title=$recordSet->fields['title'];

	$bgcolor=($ing)?"#ccccff":"#ffffff";
	if($recordSet->currentrow() % $col==0) $item_data.="<tr>";
	$item_data.="<td bgcolor='$bgcolor'><input type='checkbox' name='selected_item[]' value='$sn'".($ing?' checked':'').">($nature)$title</td>";
	if($recordSet->currentrow() % $col==($col-1) or $recordSet->EOF) $item_data.="</tr>";
	$recordSet->MoveNext();
}
$item_data.="</td></tr></table><br>";

$item_data.="※細目列示範圍：<input type='radio' name='data_scope' value=1 ".(($data_scope==1)?'checked':'').">適用年級 <input type='radio' name='data_scope' value=0 ".($data_scope?'':'checked').">全部</font><br><br>";



//顯示班級
$sql_select="select * from school_class where year=".curr_year()." AND semester=".curr_seme()." order by class_id ";
$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);

$class_list="<select name='curr_class_id' onchange='this.form.target=\"_self\"; this.form.submit();'><option></option>";
while(!$recordSet->EOF)
{
	$class_id=sprintf("%d%02d",($recordSet->fields[c_year]),($recordSet->fields[c_sort]));
	$class_name=$class_base[$class_id];
	if($curr_class_id==$class_id){
		$selected='selected';
		$show_student=1;
	} else $selected='';
	$class_list.="<option value='$class_id' $selected>$class_name</option>";
	$recordSet->MoveNext();
}
$class_list.="</select>";

$main.=$item_data."※班級：$class_list <font size=2 color='#FF0000'></font>";
if($show_student)
{
	//取得stud_base中班級學生列表
	$col=7; //設定每一列顯示幾人
	$stud_select="SELECT a.student_sn,a.stud_id,a.seme_num,b.stud_name,b.stud_sex FROM stud_seme a,stud_base b WHERE seme_year_seme='$curr_year_seme' and a.seme_class='$curr_class_id' and a.student_sn=b.student_sn ORDER BY seme_num";
	$recordSet=$CONN->Execute($stud_select) or user_error("讀取失敗！<br>$stud_select",256);
	$studentdata="<table border=1 cellpadding=3 cellspacing=0 style='border-collapse: collapse;' bordercolor='#111111'>";
	while(!$recordSet->EOF) {
		$student_sn=$recordSet->fields[student_sn];
		$stud_id=$recordSet->fields[stud_id];
		$seme_num=$recordSet->fields[seme_num];
		$stud_name=$recordSet->fields[stud_name];
		$stud_sex=$recordSet->fields[stud_sex];
		$bgcolor=($stud_sex==1)?"#DDFFDD":"#FFDDDD";	
		if($recordSet->currentrow() % $col==0) $studentdata.="<tr>";
		$studentdata.="<td bgcolor='$bgcolor'><input type='checkbox' name='selected_stud[]' value='$student_sn' checked>($seme_num)$stud_name</td>";
		if($recordSet->currentrow() % $col==($col-1) or $recordSet->EOF) $studentdata.="</tr>";
		$recordSet->MoveNext();
	}
	$studentdata.="</td></tr><tr align='center'><td colspan=$col><input type='button' name='all_stud' value='全選' onClick='javascript:tagall(1,\"selected_stud[]\");'>
					<input type='button' name='clear_stud'  value='全不選' onClick='javascript:tagall(0,\"selected_stud[]\");'> 
					<input type='checkbox' name='sch_name' value='checked' $checked_sch>印抬頭 
					<input type='submit' style='border-width:1px; cursor:hand; color:white; background:#ff555f; font-size:16pt;' value='HTML輸出' name='act' onclick='return check_select($curr_class_id);'>
					</td>
					</tr></table>";
	
}
echo $main.$studentdata."</form>";
foot();
?>