<?php

include "config.php";

sfs_check();

$curr_class_id=$_POST['curr_class_id'];
$selected_stud=$_POST['selected_stud'];
$data_scope=$_POST['data_scope'];
$checked_sch=$_POST['sch_name']?$_POST['sch_name']:'';

$data_scope_array=array('1'=>'本學期','2'=>'本學年','0'=>'所有學期');


if($_POST['act']){
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
			$sql="select a.*,b.item_sn from authentication_record a LEFT JOIN authentication_subitem b ON a.sub_item_sn=b.sn WHERE student_sn=$student_sn";
			if($data_scope==1) $sql.=" and year_seme='$curr_year_seme'"; else if($data_scope==2) $sql.=" and year_seme like '".substr($curr_year_seme,0,-1)."%'"; 
			$sql.=" ORDER BY item_sn,sub_item_sn";
			$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
			while(!$res->EOF){
				$subitem_sn=$res->fields['sub_item_sn'];
				$item_sn=$subitem_array[$subitem_sn]['item_sn'];
				$record_array[$student_sn][$item_sn][$subitem_sn]['year_seme']=$res->fields['year_seme'];
				$record_array[$student_sn][$item_sn][$subitem_sn]['teacher_sn']=$res->fields['teacher_sn'];
				$record_array[$student_sn][$item_sn][$subitem_sn]['date']=$res->fields['date'];
				//分數顯示
				$my_score=$res->fields['score'];
				if($my_score) $score_display=$my_score; else $score_display=$m_arr['zero_display'];
				if($my_score>100) $score_display=$m_arr['over_100_display'];
				$record_array[$student_sn][$item_sn][$subitem_sn][score]=$score_display;
				
				$record_array[$student_sn][$item_sn][$subitem_sn][note]=$res->fields[note];
				$res->MoveNext();
			}
		}
		
//echo "<pre>";		
//print_r($record_array);
//echo "</pre>";
		
		//開始HTML輸出
		$item_count=count($record_array);
		if($_POST['act']=='HTML輸出'){
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
		} else {
			################################    輸出 CSV    ##################################
			$class_name=$class_base[$curr_class_id];
			$data_scope_title=$data_scope_array[$data_scope];
			$filename =$school_short_name.curr_year().'學年第'.curr_seme().'學期'.$class_name.$data_scope_title.'認證清冊.csv';
			$student_data="班級,座號,學號,姓名,類別,項目代碼,項目名稱,細目代號,細目名稱,學年,學期,認證日期,得分,得點,備註\r\n";
			
			foreach($record_array as $student_sn=>$items){
				//取得學生基本資料
				$sql="select stud_id,stud_name,curr_class_num from stud_base WHERE student_sn=$student_sn";
				$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
				$stud_id=$res->fields[stud_id];
				$stud_name=$res->fields[stud_name];
				$curr_class_num=$res->fields[curr_class_num];
				
				$class_id=substr($curr_class_num,0,-2);
				$class_no=substr($curr_class_num,-2);
				
				$student_header="$class_name,$class_no,$stud_id,$stud_name,";
				$bonus=0;			
				foreach($items as $item_sn=>$sub_items) {
					foreach($sub_items as $subitem_sn=>$record_data) {
						$student_data.=$student_header.$item_array[$item_sn]['nature'].','.$item_array[$item_sn]['code'].','.$item_array[$item_sn]['title'].','.$subitem_array[$subitem_sn]['code'].','.$subitem_array[$subitem_sn]['title'].','.substr($record_data['year_seme'],0,-1).','.substr($record_data['year_seme'],-1).','.$record_data['date'].','.$record_data['score'].','.$subitem_array[$subitem_sn]['bonus'].','.$record_data['note']."\r\n";
					}
				}				
			}			
			header("Content-disposition: attachment; filename=$filename");
			header("Content-type: text/x-csv");
			//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
			
			header("Expires: 0");
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


$main="<form name='myform' method='post' action='$_SERVER[SCRIPT_NAME]'>";
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

$main.="※班級：$class_list <font size=2 color='#FF0000'> 　　※認證項目範圍：<input type='radio' name='data_scope' value=1 ".(($data_scope==1)?'checked':'').">本學期 <input type='radio' name='data_scope' value=2 ".(($data_scope==2)?'checked':'').">本學年 <input type='radio' name='data_scope' value=0 ".($data_scope?'':'checked').">所有學期</font>";
if($show_student)
{
	//取得stud_base中班級學生列表
	$col=7; //設定每一列顯示幾人
	$stud_select="SELECT a.student_sn,a.stud_id,a.seme_num,b.stud_name,b.stud_sex FROM stud_seme a,stud_base b WHERE seme_year_seme='$curr_year_seme' and a.seme_class='$curr_class_id' and a.student_sn=b.student_sn ORDER BY seme_num";
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
	$studentdata.="</td></tr><tr align='center'><td colspan=2>
					<input type='button' name='all_stud' value='全選' onClick='javascript:tagall(1);'>
					<input type='button' name='clear_stud'  value='全不選' onClick='javascript:tagall(0);'></td>
					<td colspan=4><input type='checkbox' name='sch_name' value='checked' $checked_sch>印學校全銜
					 <input type='checkbox' name='show_bouns' value='Y' checked>末尾加印總積分
					<input type='submit' name='act' value='HTML輸出' onClick='this.form.target=\"_BLANK\"';'></td>
					<td colspan=".($col-5)."><input type='submit' name='act' value='CSV輸出' onClick='this.form.target=\"_BLANK\"';'></td>
					</tr></table>";
	
}
echo $main.$studentdata."</form>";
foot();
?>