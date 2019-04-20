<?php

// $Id: $

/*引入學務系統設定檔*/
include "config.php";
require_once "../../include/sfs_case_excel.php";

//使用者認證
sfs_check();

include_once "../../include/sfs_case_dataarray.php";
$stud_kind = stud_kind();

if($_POST['act'])
{
	$sn=$_POST['target_sn'];
	//抓取欄位資料
	$sql="SELECT * FROM address_book WHERE sn=$sn";
	$rs=$CONN->Execute($sql) or die("無法取得已經開列的樣式資料!<br>$sql");
	$title=$rs->fields['title'];
	$fields=$rs->fields['fields'];
	$header=$rs->fields['header']?"<br>".$rs->fields['header']:'';
	$footer=$rs->fields['footer'];;

	//設定表格抬頭
	$columns_array=explode(',',$fields);
	$fields_list='';
	$fields_list_array=array();
	$table_data="<table STYLE='font-size: x-small' border='1' cellpadding=3 cellspacing=0 style='border-collapse: collapse' bordercolor='#111111' width='100%'><tr bgcolor='#FFCCCC'>";	
	$csv_header="";
	foreach($columns_array as $key=>$value)
	{
		$value=trim($value);
		if($value)
		{
			//如果是 非模組管理員檢查禁列項目 則將 欄位名稱加註*XXXX*
			if (! checkid($_SERVER['SCRIPT_FILENAME'],1))
				if(strpos($forbid,"$value")) $value="*$value*";

			$table_data.="<td align='center'>$value</td>";
			$csv_header.="$value,";
				
			$fields_list_array[$key]=array_search($value,$fields_array);
			if($fields_list_array[$key])
			{
				if($value=='年級') $fields_list.='left(a.curr_class_num,1) AS grade,';
					elseif($value=='班級代號') $fields_list.='left(a.curr_class_num,3) AS class_id,';
					elseif($value=='班級名稱') $fields_list.='left(a.curr_class_num,3) AS class_name,';
					elseif($value=='出生年') $fields_list.='year(a.stud_birthday) AS year,';
					elseif($value=='出生月') $fields_list.='month(a.stud_birthday) AS month,';
					elseif($value=='出生日') $fields_list.='day(a.stud_birthday) AS day,';
					else $fields_list.=$fields_list_array[$key].',';
				$fields_list_array[$key]=substr($fields_list_array[$key],2);
			} else $fields_list_array[$key]='';
		}
	}
	$fields_list=substr($fields_list,0,-1);
	$csv_header=substr($csv_header,0,-1)."\r\n";
	$table_data.="</tr>";

	switch($nature)
	{
		case 'student':
			if($_POST['class_selected'])
			{
				if ($_POST['act']=='excel') {
						$x=new sfs_xls();
	 					$x->setUTF8();
	 					$x->filename=$school_long_name.$title.'.xls';
	 					$Excel_tag=count($_POST['class_selected']);
						$x->setBorderStyle(1);
						$x->addSheet($title);
						$HEADER=explode(",",$csv_header);
						$x->items[0]=$HEADER;   //第1列            
				}
				$last_key=count($_POST['class_selected'])-1;
				$data='';
				foreach($_POST['class_selected'] as $key=>$class_id)
				{
					//抓取指定班級學生資料
					$sql_student="SELECT $fields_list FROM stud_base a LEFT JOIN stud_domicile b ON a.student_sn=b.student_sn WHERE stud_study_cond=0 AND curr_class_num like '$class_id%'ORDER BY curr_class_num";
//echo "$sql_student<br>";
					$rs_student=$CONN->Execute($sql_student) or die("無法取得{$nature_array[$nature]}資料!<br>$sql_student");

					if($_POST['act']=='html'){
						$csv_header='';
						$data="<center><font size=4>$school_long_name<br>$title</font></center><font size=2>$header<p align='right'>◎班級： {$class_name_arr[$class_id]}</p></font>$table_data<tr>";
						while(!$rs_student->EOF) {
							foreach($fields_list_array as $field_name)
							{
								$field_data=$rs_student->fields[$field_name];
								//特殊欄位處理  
								if($field_name=='curr_class_num') $field_data=substr($field_data,-2); else
								if($field_name=='class_name') $field_data=$class_name_arr[$field_data]; else
								if(substr($field_name,-11)=='birth_place') $field_data=$birth_place_array[$field_data]; else
								if(substr($field_name,-5)=='alive') $field_data=$is_live[$field_data]; else
								if($field_name=='stud_sex') $field_data=$sex_arr[$field_data]; else
								if($field_name=='stud_blood_type') $field_data=$blood_arr[$field_data]; else
								if(substr($field_name,-8)=='birthday') $field_data=(date('Y',strtotime($field_data))-1911).date('年m月d日',strtotime($field_data)); else
								if($field_name=='guardian_relation') $field_data=$guardian_relation[$field_data];
								
								if($field_name=='obtain') $field_data=$obtain_arr[$field_data];
								if($field_name=='safeguard') $field_data=$safeguard_arr[$field_data];
								
								//學生身分類別
								if($field_name=='stud_kind') {
									$a=explode(',',$field_data);
									$field_data='';
									foreach($a as $k=>$v) {
										$field_data .= $stud_kind[$v]. ' ';
									}
									$field_data = substr($field_data,0,-1);									
								}
								
								
								//假使是地址 就不設定置中對齊
								if(strpos($field_name,'add')) $align=''; else $align="align='center'";
								$data.="<td $align>$field_data</td>";
							}
							$data.="</tr>";
							$rs_student->MoveNext();
						}
						$data.="</table><font size=1>$footer</font>";
						if($key<$last_key) $data.=$page_break;
						echo $data;
						$data='';
					//Excel輸出
					} elseif ($_POST['act']=='excel'){
						
					  
						$my_class_name=$class_name_arr[$class_id];
						while(!$rs_student->EOF) {
							$data="";
							foreach($fields_list_array as $field_name)
							{
								$field_data=$rs_student->fields[$field_name];
								//特殊欄位處理
								if($field_name=='curr_class_num') $field_data=substr($field_data,-2); else
								if($field_name=='class_name') $field_data=$class_name_arr[$field_data]; else
								if(substr($field_name,-11)=='birth_place') $field_data=$birth_place_array[$field_data]; else
								if(substr($field_name,-5)=='alive') $field_data=$is_live[$field_data]; else
								if($field_name=='stud_sex') $field_data=$sex_arr[$field_data]; else
								if(substr($field_name,-8)=='birthday') $field_data=(date('Y',strtotime($field_data))-1911).date('年m月d日',strtotime($field_data)); else
								if($field_name=='guardian_relation') $field_data=$guardian_relation[$field_data];
								
								if($field_name=='obtain') $field_data=$obtain_arr[$field_data];
								if($field_name=='safeguard') $field_data=$safeguard_arr[$field_data];
								
								//學生身分類別
								if($field_name=='stud_kind') {
									$a=explode(',',$field_data);
									$field_data='';
									foreach($a as $k=>$v) {
										$field_data .= $stud_kind[$v]. ' ';
									}
									$field_data = substr($field_data,0,-1);									
								}
								
								$data.="$field_data,";
							}
							$Excel_row=explode(",",substr($data,0,-1));
							$x->items[]=$Excel_row;  //加入 row
							$rs_student->MoveNext();
						} // end while
   				 
   				
					//CSV輸出	
					} else {
						$my_class_name=$class_name_arr[$class_id];
						while(!$rs_student->EOF) {
							foreach($fields_list_array as $field_name)
							{
								$field_data=$rs_student->fields[$field_name];
								//特殊欄位處理
								if($field_name=='curr_class_num') $field_data=substr($field_data,-2); else
								if($field_name=='class_name') $field_data=$class_name_arr[$field_data]; else
								if(substr($field_name,-11)=='birth_place') $field_data=$birth_place_array[$field_data]; else
								if(substr($field_name,-5)=='alive') $field_data=$is_live[$field_data]; else
								if($field_name=='stud_sex') $field_data=$sex_arr[$field_data]; else
								if(substr($field_name,-8)=='birthday') $field_data=(date('Y',strtotime($field_data))-1911).date('年m月d日',strtotime($field_data)); else
								if($field_name=='guardian_relation') $field_data=$guardian_relation[$field_data];
								
								if($field_name=='obtain') $field_data=$obtain_arr[$field_data];
								if($field_name=='safeguard') $field_data=$safeguard_arr[$field_data];
								
								//學生身分類別
								if($field_name=='stud_kind') {
									$a=explode(',',$field_data);
									$field_data='';
									foreach($a as $k=>$v) {
										$field_data .= $stud_kind[$v]. ' ';
									}
									$field_data = substr($field_data,0,-1);									
								}
								
								$data.="$field_data,";
							}
							$data.="\r\n";
							$rs_student->MoveNext();
						}
					}
				}
				
				if ($_POST['act']=='excel') {
				  $x->writeSheet();
					$x->process();
					exit();
				}
				$filename=$school_long_name.$title.$header.".csv";
				header("Content-disposition: attachment; filename=$filename");
				header("Content-type: text/x-csv");
				//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");

				header("Expires: 0");
				echo $csv_header.$data;
				exit;	
			} else echo "<center><font size=10 color='red'><br><br><br>您未選取任何班級!</font></center>";
			break;
		case 'teacher':
			$sql_teacher="SELECT $fields_list FROM teacher_base a LEFT JOIN teacher_connect b ON a.teacher_sn=b.teacher_sn WHERE teach_condition=0 ORDER BY birthday";
			$rs_teacher=$CONN->Execute($sql_teacher) or die("無法取得{$nature_array[$nature]}資料!<br>$sql_teacher");
			$data="<center><font size=4>$school_long_name<br>$title</font></center><font size=2>$header</font>$table_data<tr>";
			while(!$rs_teacher->EOF) {					
				foreach($fields_list_array as $field_name)
				{
					$field_data=$rs_teacher->fields[$field_name];						
					//特殊欄位處理
					if($field_name=='curr_class_num') $field_data=substr($field_data,-2); 
					if(substr($field_name,-11)=='birth_place') $field_data=$birth_place_array[$field_data];
					
					if($field_name=='sex') $field_data=$sex_arr[$field_data]; //教師性別
					if(substr($field_name,-8)=='birthday') $field_data=(date('Y',strtotime($field_data))-1911).date('年m月d日',strtotime($field_data)); 
				
					//假使是地址 就不設定置中對齊
					if(strpos($field_name,'add')) $align=''; else $align="align='center'";
					$data.="<td $align>$field_data</td>";
				}
				$data.="</tr>";
				$rs_teacher->MoveNext();
			}					
			$data.="</table><font size=1>$footer</font>";
			echo $data;
			break;	
	}
	
} else {
	//秀出網頁
	head("通訊錄輸出");
	print_menu($menu_p);
	
	echo "<script>
		function tagall(status) {
		  var i =0;
		  while (i < document.myform.elements.length)  {
			if (document.myform.elements[i].name=='class_selected[]') {
			  document.myform.elements[i].checked=status;
			}
			i++;
		  }
		}
		</script>";
		
	if($_POST['nature']<>'teacher') $csv_button="";

	//抓取已經開列的樣式資料
	$myself=$_POST['myself']?"and creater='$my_name'":'';
	$saved_format="<table STYLE='font-size: x-small' border='1' cellpadding=5 cellspacing=0 style='border-collapse: collapse' bordercolor='#111111' width='100%'><tr bgcolor='#CCCFFC'><td align='center'>標題</td><td align='center' width='40%'>欄位列表</td><td align='center'>欄數</td><td align='center'>設定者</td><td align='center'>更新日期</td><td align='center'>動作<input type='hidden' name='target_sn' value='{$_POST['target_sn']}'><input type='hidden' name='act' value=''></td></tr>";
	$sql="select * from address_book where room='$my_room' and nature='$nature' $myself order by update_time desc;";
	$rs=$CONN->Execute($sql) or die("無法取得已經開列的樣式資料!<br>$sql");
	while(!$rs->EOF) {
		$target_sn=$rs->fields['sn'];
		if($rs->fields['creater']==$my_name) $myselef_color='#FCFCBF'; else $myselef_color='#FFFFFF';
		$saved_format.="<tr bgcolor='$myselef_color'><td align='center'>{$rs->fields['title']}</td><td>{$rs->fields['fields']}</td><td align='center'>{$rs->fields['columns']}</td><td align='center'>{$rs->fields['creater']}</td><td align='center'>{$rs->fields['update_time']}</td><td align='center'>
						<input type='button' value='網頁輸出' onclick='this.form.target_sn.value=\"$target_sn\"; this.form.act.value=\"html\"; this.form.target=\"_blank\"; this.form.submit();'>
						<input type='button' value='CSV輸出' onclick='this.form.target_sn.value=\"$target_sn\"; this.form.act.value=\"csv\"; this.form.submit();'>
						<input type='button' value='Excel輸出' onclick='this.form.target_sn.value=\"$target_sn\"; this.form.act.value=\"excel\"; this.form.submit();'>
						<input type='button' value='修改' onclick='this.form.target_sn.value=\"$target_sn\"; this.form.act.value=\"modify\"; this.form.action=\"manage.php\"; this.form.target=\"_self\"; this.form.submit();'>
						</td></tr>";
		$rs->MoveNext();
	}
	$myself="<input type='checkbox' name='myself' value='ON'".($_POST['myself']?' checked':'')." onclick='this.form.target_sn.value=\"\"; this.form.act.value=\"\"; this.form.action=\"$_SERVER[SCRIPT_NAME]\"; this.form.target=\"_self\"; this.form.submit();'>只列示我設定的樣式";
	//如果是 非模組管理員顯示禁列的項目
	if (! checkid($_SERVER['SCRIPT_FILENAME'],1)) $myself.=" 　　　<font color='red' size=2>◎您非模組管理員，系統禁止列示的項目: $forbid</font>";
	
	$saved_format.='</table>';
	
	//如果是學生產生班級隨選清單
	if($nature=='student')
	{
		//抓取指定學年班級
		$class_list="<table STYLE='font-size: x-small' border='1' cellpadding=5 cellspacing=0 style='border-collapse: collapse' bordercolor='#111111' width='100%'>
					<tr bgcolor='#FFCCCC'><td align='center'>選定要列印的班級 <input type='checkbox' name='tag' onclick='javascript:tagall(this.checked);'>全選</td></tr><tr><td>";
		$sql="select * from school_class where enable=1 and year='$curr_year' and semester='$curr_seme' order by c_year,c_sort;";
		$rs=$CONN->Execute($sql) or die("無法取得 $curr_year_seme 班級資料!<br>$sql");
		while(!$rs->EOF)
		{
			$class_id=sprintf('%0d%02d',$rs->fields['c_year'],$rs->fields['c_sort']);
			$class_name=$class_name_arr[$class_id];
			$class_list.="<input type='checkbox' name='class_selected[]' value='$class_id'>$class_name ";
			$rs->MoveNext();
		}
		$class_list.="</td></tr></table>";
	}
	echo "<form name='myform' method='post' action='$_SERVER[SCRIPT_NAME]'>$nature_radio<hr>$class_list<br>$myself $saved_format</form>";
	foot();
}

?>
