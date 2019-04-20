<?php

include "config.php";

sfs_check();

$selected_stud=$_POST[selected_stud];
$checked=$_POST[this_semester]?$_POST[this_semester]:'';
$checked_sch=$_POST[sch_name]?$_POST[sch_name]:'';

if($_POST['act']=='統計輸出'){
	if($selected_stud){ 
		//取得認證項目陣列
		$item_array=array();
		$sql="select * from authentication_item";
		$res_item=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
		while(!$res_item->EOF){
			$sn=$res_item->fields[sn];
			$item_array[$sn][code]=$res_item->fields[code];
			$item_array[$sn][title]=$res_item->fields[title];
			$item_array[$sn][nature]=$res_item->fields[nature];
			$item_array[$sn][room_id]=$res_item->fields[room_id];
			$res_item->MoveNext();
		}
		
		//取得認證細目陣列
		$sql="select * from authentication_subitem";
		$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
		while(!$res->EOF){
			$sn=$res->fields[sn];
			$subitem_array[$sn][item_sn]=$res->fields[item_sn];
			$subitem_array[$sn][code]=$res->fields[code];
			$subitem_array[$sn][title]=$res->fields[title];
			$subitem_array[$sn][bonus]=$res->fields[bonus];
			$res->MoveNext();
		}
		
		foreach($selected_stud as $student_sn)
		{
			//抓取選取學生的認證紀錄
			$sql="select a.*,b.item_sn from authentication_record a LEFT JOIN authentication_subitem b ON a.sub_item_sn=b.sn WHERE student_sn=$student_sn ORDER BY item_sn,sub_item_sn";
			if($checked) $sql.=" and year_seme='$curr_year_seme'"; 
			$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
			while(!$res->EOF){
				$subitem_sn=$res->fields[sub_item_sn];
				$item_sn=$subitem_array[$subitem_sn][item_sn];
				$record_array[$student_sn][$item_sn][$subitem_sn][year_seme]=$res->fields[year_seme];
				$record_array[$student_sn][$item_sn][$subitem_sn][teacher_sn]=$res->fields[teacher_sn];
				$record_array[$student_sn][$item_sn][$subitem_sn][date]=$res->fields[date];
				$record_array[$student_sn][$item_sn][$subitem_sn][score]=$res->fields[score]?$res->fields[score]:'';
				$record_array[$student_sn][$item_sn][$subitem_sn][note]=$res->fields[note];
				$res->MoveNext();
			}
		}
		//開始HTML輸出
		$item_count=count($record_array);

		foreach($record_array as $student_sn=>$items){
			//印學校抬頭
			$student_data=$checked_sch?"<CENTER><FONT size=4>$school_long_name 學習認證記錄</FONT></CENTER><BR><BR>":'';
			
			//取得學生基本資料
			$sql="select stud_id,stud_name,curr_class_num from stud_base WHERE student_sn=$student_sn";
			$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
			$stud_id=$res->fields[stud_id];
			$stud_name=$res->fields[stud_name];
			$curr_class_num=$res->fields[curr_class_num];
			
			$class_id=substr($curr_class_num,0,-2);
			$class_no=substr($curr_class_num,-2);
			$class_name=$class_base[$class_id];
			
			$student_data.="※班級：$class_name 　※座號：$class_no 　※學號：$stud_id 　※姓名：$stud_name";
			$bonus=0;			
			foreach($items as $item_sn=>$sub_items) {
				$item_data=$item_array[$item_sn][code].'-'.$item_array[$item_sn][nature].'-'.$item_array[$item_sn][title];
				$student_data.="<table border=2 cellpadding=6 cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' width='100%'><tr align='center'><td colspan=5>$item_data</td></tr>";
				$student_data.="<tr align='center' bgcolor='#FFCCCC'><td>認證細目</td><td>認證學期</td><td>認證日期</td><td>得分</td><td>備註</td></tr>";
				foreach($sub_items as $subitem_sn=>$record_data) {
					$record_data_list='<td>'.$record_data[year_seme].'</td><td>'.$record_data[date].'</td><td>'.$record_data[score].'</td><td>'.$record_data[note].'</td>';
					$subitem_data="<tr align='center'><td>".$subitem_array[$subitem_sn][code].' '.$subitem_array[$subitem_sn][title].' (*'.$subitem_array[$subitem_sn][bonus].')</td>'.$record_data_list.'</tr>';
					$bonus+=$subitem_array[$subitem_sn][bonus];
					$student_data.=$subitem_data;
				}
				$student_data.="</table>";
			}
			if($_POST[show_bouns]) $student_data.="<center><font size=2><br>◎總得點數：$bonus</font></center>";
			//換頁
			$key++;
			if($key<$item_count) $student_data.="<P style='page-break-after:always'></P>"; else $student_data.="<br>";
			echo $student_data;
			
		}
		
		//echo "<pre>";
		//print_r($record_array);
		//echo "<pre>";
	} else echo "您尚未選取任何學生！";
	exit;
};

//秀出網頁
head("個人列表");

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



//橫向選單標籤
echo print_menu($MENU_P);

if($my_class_id){   //判定是否為班級導師
	$main="<form name='myform' method='post' action='$_SERVER[SCRIPT_NAME]'><input type='hidden' name='curr_class_id' value='$my_class_id'>";
	$main.="班級：$class_base[$my_class_id] 　　<font size=2 color='#FF0000'><input type='checkbox' name='this_semester' value='checked' $checked>僅提列本學期認證項目 <input type='checkbox' name='sch_name' value='checked' $checked_sch>印學校全銜</font>";
	//if($show_student)
	//{
		//取得stud_base中班級學生列表
		$col=5; //設定每一列顯示幾人
		$stud_select="SELECT a.student_sn,a.stud_id,a.seme_num,b.stud_name,b.stud_sex FROM stud_seme a,stud_base b WHERE seme_year_seme='$curr_year_seme' and a.seme_class='$my_class_id' and a.student_sn=b.student_sn ORDER BY seme_num";
		$recordSet=$CONN->Execute($stud_select) or user_error("讀取失敗！<br>$stud_select",256);
		$studentdata="<table border='1' cellpadding='3' cellspacing='0' style='border-collapse: collapse;' bordercolor='#111111'>";
		while(!$recordSet->EOF) {
			$student_sn=$recordSet->fields[student_sn];
			$stud_id=$recordSet->fields[stud_id];
			$seme_num=$recordSet->fields[seme_num];
			$stud_name=$recordSet->fields[stud_name];
			$stud_sex=$recordSet->fields[stud_sex];
			$bgcolor=($stud_sex==1)?"#DDFFDD":"#FFDDDD";	
			if($recordSet->currentrow() % $col==0) $studentdata.="<tr>";
			$studentdata.="<td bgcolor='$bgcolor'><input type='checkbox' name='selected_stud[]' value='$student_sn'>($seme_num)$stud_name</td>";
			if($recordSet->currentrow() % $col==($col-1) or $recordSet->EOF) $studentdata.="</tr>";
			$recordSet->MoveNext();
		}
		$studentdata.="</td></tr><tr align='right'><td colspan=$col>
						<input type='button' name='all_stud' value='全選' onClick='javascript:tagall(1);'>
						<input type='button' name='clear_stud'  value='全不選' onClick='javascript:tagall(0);'>
						 　　　<input type='checkbox' name='show_bouns' value='Y' checked>末尾加印總積分
						<input type='submit' name='act' value='統計輸出' onClick='this.form.target=\"_BLANK\"';'>
						</td></tr></table>";
		
	//}
	echo $main.$studentdata."</form>";
} else echo "<br><font color='red' size=5>~~~~ 這個功能只供班級導師查詢！ ~~~~</font>";
foot();
?>