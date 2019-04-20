<?php

include "config.php";

sfs_check();

//秀出網頁
head("學生身分別對應設定");
print_menu($menu_p);

//學期別
$work_year_seme=$_REQUEST['work_year_seme'];
if($work_year_seme=='') $work_year_seme = sprintf("%03d%d",curr_year(),curr_seme());
$curr_year_seme=sprintf("%03d%d",curr_year(),curr_seme());
$academic_year=substr($curr_year_seme,0,-1);
$work_year=substr($work_year_seme,0,-1);
$session_tea_sn=$_SESSION['session_tea_sn'];

//取得學生身份列表
$type_select="SELECT d_id,t_name FROM sfs_text WHERE t_kind='stud_kind' AND d_id>0 order by t_order_id";
$recordSet=$CONN->Execute($type_select) or user_error("讀取失敗！<br>$type_select",256);
while(list($d_id,$t_name)=$recordSet->FetchRow()) {
	$kinddata[$d_id]=$t_name;
}

if($_POST['act']=='儲存並重新設定報名身分'){
	$kind_data_array=$_POST['kind_select'];
	$kind_data=serialize($kind_data_array);
	$disability_data_array=$_POST['disability_select'];
	$disability_data=serialize($disability_data_array);
	$free_data_array=$_POST['free_select'];
	$free_data=serialize($free_data_array);
//echo "<pre>";
//print_r($kind_data_array);	
//print_r($disability_data_array);	
//print_r($free_data_array);	
//echo "</pre>";

	//寫入資料表
	$sql="REPLACE INTO 12basic_kind_ptc SET year_seme='$work_year_seme',kind_data='$kind_data',disability_data='$disability_data',free_data='$free_data'";
	$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);	

	//清除既有設定
	$sql="UPDATE 12basic_ptc SET kind_id=0,disability_id=0,free_id=0,score_disadvantage=0 WHERE academic_year='$work_year' AND editable='1'";
	$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
	
	//抓取學生身分類別資料並決定其身分
	$sql="SELECT a.student_sn,a.editable,b.stud_kind,b.curr_class_num,b.stud_name FROM 12basic_ptc a INNER JOIN stud_base b ON a.student_sn=b.student_sn WHERE a.academic_year='$work_year'";
	$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
	while(!$res->EOF){
		$student_sn=$res->fields['student_sn'];
		$curr_class_num=$res->fields['curr_class_num'];
		$stud_name=$res->fields['stud_name'];
		$editable=$res->fields['editable'];
		
		if($editable){
			$my_kind_arr=explode(',',$res->fields['stud_kind']);
			
			$kind_id=0;
			$kind_rate=0;
			$disability_id=0;
			$free_id=0;
			$free_rate=0;
			$score_disadvantage=0;

	//echo $student_sn.'<br>';
	//echo "<pre>";
	//print_r($stud_free_arr);	
	//echo "</pre>";				
			foreach($my_kind_arr as $key=>$value){
				if($value){
					//特種身分
					$kind_id=$kind_data_array[$value]?$kind_data_array[$value]:$kind_id;
					//echo "$student_sn : $value ===> $kind_id <br>";				
					/*
					//決定以何種身分報名
					$a=$kind_data_array[$value];
					if($kind_rate<$stud_kind_rate[$a]){
						$kind_rate=$stud_kind_rate[$a];
						$kind_id=$a;				
					}
					*/
					//身心障礙
					$disability_id=$disability_data_array[$value]?$disability_data_array[$value]:$disability_id;
					
					//低收失業
					$free_id=$free_data_array[$value]?$free_data_array[$value]:$free_id;
					/*
					$a=$free_data_array[$value];
					//echo $value.'--->'.$a.'--->'.$stud_free_rate[$a].'<br>';					
					if($free_rate<$stud_free_rate[$a]){
						$free_rate=$stud_free_rate[$a];
						$free_id=$a;				
					}
					*/
				}
			}	
	//echo '<br>free final->'.$free_id.'<br><br><br>';					
			//決定低收或中低收入戶積分
			$score_disadvantage=$stud_free_rate[$free_id];
			/*
			//判定族語認證與否
			if($kind_id=='1' or $kind_id=='2'){
				//抓取是否通過族語認證
				$field_name=$kind_field_mirror[$native_language_sort];
				$sql="SELECT $field_name FROM stud_subkind WHERE student_sn=$student_sn and type_id='$native_id'";
				$rs=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
				if($native_language_text==$rs->fields[0]) $kind_id='2'; else $kind_id='1';				
	//echo "$sql<br>$native_language_text -- {$rs->fields[0]}<br>";							
			}
			*/
			//寫入資料表
			$sql="UPDATE 12basic_ptc SET kind_id='$kind_id',disability_id='$disability_id',free_id='$free_id',score_disadvantage='$score_disadvantage',update_sn='$session_tea_sn' WHERE student_sn=$student_sn AND academic_year='$work_year' AND editable='1'";
	//echo $sql.'<br>';	
			$rs=$CONN->Execute($sql) or user_error("更新失敗！<br>$sql",256);
			$check_value=$kind_id+$disability_id+$free_id;
			if($check_value) $msg.="<img src='./images/on.png' border=0 height=12>($curr_class_num)$stud_name  ●報名身分：($kind_id){$stud_kind_arr[$kind_id]} ●身心障礙：($disability_id){$stud_disability_arr[$disability_id]} ●低收失業：($free_id){$stud_free_arr[$free_id]}<br>";
			
		} else $msg.="<img src='./images/sealed.png' border=0 height=12>($curr_class_num)$stud_name 《資料已封存，不進行重新對應》<br>";
		
		$res->MoveNext();
	}
}


//橫向選單標籤
$linkstr="work_year_seme=$work_year_seme&stud_class=$stud_class";
echo print_menu($MENU_P,$linkstr);

//取得年度與學期的下拉選單
$recent_semester=get_recent_semester_select('work_year_seme',$work_year_seme);

if($work_year==$academic_year) $tool_icon="<input type='submit' name='act' value='儲存並重新設定報名身分' onclick='return confirm(\"確定要\"+this.value+\"?\")'>";

$main="<form name='myform' method='post' action='$_SERVER[PHP_SELF]'><input type='hidden' name='edit_sn' value='$edit_sn'>$recent_semester $tool_icon
		<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' id='AutoNumber1'>";
		//<font size=1 color='red'><li>學生身分對應為 1.原住民 或 2.原住民(語言認證)，程式會依照貴校模組變數的設定 自動抓取身分屬性資料決定是否通過族語認證！</li></font>

//抓取報名身分與低收失業對應表
$sql="SELECT * FROM `12basic_kind_ptc` WHERE year_seme='$work_year_seme'";
$rs=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
$kind_mirror_array=unserialize($rs->fields['kind_data']);
$disability_mirror_array=unserialize($rs->fields['disability_data']);
$free_mirror_array=unserialize($rs->fields['free_data']);

$mirror_data="<tr align='center' bgcolor='#ccccff'><td>SFS3學務系統內的身分類別</td><td>學生身分</td><td>身心障礙</td><td>低收失業</td></tr>";
foreach($kinddata as $key=>$value){
	//產生對應的學生身分select元件
	$kind_select="<select name='kind_select[$key]'>";
	foreach($stud_kind_arr as $kind_key=>$kind_value){
		$selected='';
		$bg_color='';
		if($kind_key==$kind_mirror_array[$key]){
			$selected='selected';
			if($kind_mirror_array[$key]) $bg_color="style='background-color: #ffcccc;'";
		}
		$kind_select.="<option value='$kind_key' $selected $bg_color>($kind_key) $kind_value</option>";
	}
	$kind_select.="</select>";
	
	//產生對應的身心障礙select元件
	$disability_select="<select name='disability_select[$key]'>";
	foreach($stud_disability_arr as $disability_key=>$disability_value){
		$selected='';
		$bg_color='';
		if($disability_key==$disability_mirror_array[$key]){
			$selected='selected';
			if($disability_mirror_array[$key]) $bg_color="style='background-color: #ffcccc;'";
		}
		$disability_select.="<option value='$disability_key' $selected $bg_color>($disability_key) $disability_value</option>";
	}
	$disability_select.="</select>";
	
	//產生對應的低收失業select元件
	$free_select="<select name='free_select[$key]'>";
	foreach($stud_free_arr as $free_key=>$free_value){
		$selected='';
		$bg_color='';
		if($free_key==$free_mirror_array[$key]){
			$selected='selected';
			if($free_mirror_array[$key]) $bg_color="style='background-color: #ffcccc;'";
		}
		$free_select.="<option value='$free_key' $selected $bg_color>($free_key) $free_value</option>";
	}
	$free_select.="</select>";
	
	$mirror_data.="<tr><td bgcolor='#ccffcc'>($key)$value</td><td>$kind_select</td><td>$disability_select</td><td>$free_select</td></tr>";
}


//顯示封存狀態資訊
echo get_sealed_status($work_year).'<br>';

echo $main.$mirror_data."</table></form><br>$msg";
foot();
?>