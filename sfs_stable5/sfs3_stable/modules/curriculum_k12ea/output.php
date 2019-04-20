<?php
// $Id: output_xml.php 6036 2010-08-26 05:39:46Z infodaes $

require "config.php";
require_once "../../include/sfs_case_excel.php";
require_once "../../Spreadsheet/XLSX/xlsxwriter.class.php";
//require_once "../../Spreadsheet/PHPExcel.php";
//require_once "../../Spreadsheet/PHPExcel/Writer/Excel2007.php";
//require_once "../../Spreadsheet/PHPExcel/IOFactory.php";
require_once "../../include/sfs_case_dataarray.php";

sfs_check();

//big5轉utf8
function big5_to_utf8($str){
    $str = mb_convert_encoding($str, "UTF-8", "BIG5");
    $i=1;
    while ($i != 0){
        $pattern = '/&#\d+\;/';
        preg_match($pattern, $str, $matches);
        $i = sizeof($matches);
        if ($i !=0){
            $unicode_char = mb_convert_encoding($matches[0], 'UTF-8', 'HTML-ENTITIES');
            $str = preg_replace("/$matches[0]/",$unicode_char,$str);
        }
    }
    return $str;
}

//如果確定輸出XLSX檔案
if ($_POST[act]) {
	$out_arr=array();
	//設定參照陣列
	$semester_arr=array(1=>'上學期',2=>'下學期');
	$class_year_arr=array(0=>'不分年級',1=>'一年級',2=>'二年級',3=>'三年級',4=>'四年級',5=>'五年級',6=>'六年級',7=>'七年級',8=>'八年級',9=>'九年級',10=>'十年級',11=>'十一年級',12=>'十二年級');
	$dow_arr=array(1=>'週一',2=>'週二',3=>'週三',4=>'週四',5=>'週五',6=>'週六',7=>'週日');
	$sector_arr=array(1=>'第一節',2=>'第二節',3=>'第三節',4=>'第四節',5=>'第五節',6=>'第六節',7=>'第七節',8=>'第八節',9=>'第九節',10=>'第十節',11=>'第十一節',12=>'第十二節',13=>'第十三節',14=>'第十四節');

	//產生班級設定列表
	$sql="SELECT class_id,c_kind_k12ea FROM school_class WHERE class_id LIKE '{$_POST['year_seme'][0]}_%' ORDER BY class_id";
	$res=$CONN->Execute($sql) or user_error("讀取課表設定資料失敗！<br>$sql",256);
	while(!$res->EOF) {
		$class_id=$res->fields['class_id'];
		$k12ea_kind_arr[$class_id]=$res->fields['c_kind_k12ea'];
		$res->MoveNext();
	}
	
	//國教署課程對應ARRAY
	$k12ea_category_array = k12ea_category();
	$k12ea_area_array = k12ea_area();
	$k12ea_subject_array = k12ea_subject();
	$k12ea_language_array = k12ea_language();
	
	$k12ea_class_kind_array = k12ea_class_kind();
	
	//抓取科目名稱
	$subject_arr=array();
	$sql="SELECT subject_id,subject_name FROM score_subject WHERE enable=1";
	$res=$CONN->Execute($sql) or user_error("讀取課表設定資料失敗！<br>$sql",256);
	while(!$res->EOF){
		$subject_id=$res->fields['subject_id'];
		$subject_arr[$subject_id]=$res->fields['subject_name'];
		$res->MoveNext();
	}

	//抓取課表資料進行陣列儲存	
	foreach($_POST[year_seme] as $key=>$year_seme){
		
		$tmp=explode('_',$year_seme);
		$this_year=$tmp[0];
		$this_semester=$tmp[1];
		
			
		//抓取課程資料
		$ss_arr=array();
		$sql_ss="SELECT * FROM score_ss WHERE enable=1 AND year='$this_year' AND semester ORDER BY class_id";
		$res_ss=$CONN->Execute($sql_ss) or user_error("讀取課表設定資料失敗！<br>$sql_ss",256);
		while(!$res_ss->EOF){
			$ss_id=$res_ss->fields['ss_id'];
			$scope_id=$res_ss->fields['scope_id'];
			$subject_id=$res_ss->fields['subject_id'];
			//學校科目名稱
			$ss_arr[$ss_id]['subject']=$subject_arr[$subject_id]?$subject_arr[$subject_id]:$subject_arr[$scope_id];
			//國教署課程對應
			$ss_arr[$ss_id]['k12ea_category']=$res_ss->fields['k12ea_category'];
			$ss_arr[$ss_id]['k12ea_area']=$res_ss->fields['k12ea_area'];
			$ss_arr[$ss_id]['k12ea_subject']=$res_ss->fields['k12ea_subject'];
			$ss_arr[$ss_id]['k12ea_language']=$res_ss->fields['k12ea_language'];		

			$res_ss->MoveNext();
		}
		
		
		
		$out_arr[$year_seme]['year']=$this_year;
		$out_arr[$year_seme]['semester']=$semester_arr[$this_semester];
		
		//抓取課表資料	
		$sql = "SELECT a.*,b.name,b.teach_person_id FROM score_course a LEFT JOIN teacher_base b ON a.teacher_sn=b.teacher_sn WHERE a.year=$this_year AND a.semester=$this_semester ORDER BY ";
		$sql .= $_POST['order'] ? 'teacher_sn,day,sector' : 'class_id,day,sector';
			
		$res=$CONN->Execute($sql) or user_error("讀取課表設定資料失敗！<br>$sql",256);
		while(!$res->EOF){
			$class_id=$res->fields['class_id'];
			if($k12ea_kind_arr[$class_id]) {  //有設定班級類型的才輸出
				$teacher_sn=$res->fields['teacher_sn'];
				$course_id=$res->fields['course_id'];	

				//班級類型
				if($_POST[act] == 'EXCEL 2007 (.XLSX)') {
					$class_kind = $k12ea_kind_arr[$class_id];	
					$out_arr[$year_seme]['curriculums'][$course_id][]= $k12ea_class_kind_array[$class_kind];
				}
						
				$dow=$res->fields['day'];
				$out_arr[$year_seme]['curriculums'][$course_id][]=$dow_arr[$dow];
				$sector=$res->fields['sector'];
				$out_arr[$year_seme]['curriculums'][$course_id][]=$sector_arr[$sector];
				
				$class_year=$res->fields['class_year'];
				$out_arr[$year_seme]['curriculums'][$course_id][]=$class_year_arr[$class_year];
				
				$class_id=$res->fields['class_id'];
				$class_num = explode("_",$class_id);
				$out_arr[$year_seme]['curriculums'][$course_id][] = '第'.$class_num[3].'班';
				$out_arr[$year_seme]['curriculums'][$course_id][]=$res->fields['name'];
				$out_arr[$year_seme]['curriculums'][$course_id][]=$res->fields['teach_person_id'];

				$ss_id=$res->fields['ss_id'];
				$out_arr[$year_seme]['curriculums'][$course_id][]=$k12ea_category_array[$ss_arr[$ss_id]['k12ea_category']];
				$out_arr[$year_seme]['curriculums'][$course_id][]=$k12ea_area_array[$ss_arr[$ss_id]['k12ea_area']];
				$out_arr[$year_seme]['curriculums'][$course_id][]=$k12ea_subject_array[$ss_arr[$ss_id]['k12ea_subject']];
				$out_arr[$year_seme]['curriculums'][$course_id][]=($k12ea_subject_array[$ss_arr[$ss_id]['k12ea_subject']] == '本土語言') ? $k12ea_language_array[$ss_arr[$ss_id]['k12ea_language']] : '';
				
				//配合人力資源網匯入判斷邏輯，校訂課程與部定課程相同時則不輸出校訂課程名稱
				$out_arr[$year_seme]['curriculums'][$course_id][]= ( $ss_arr[$ss_id]['subject'] == $k12ea_subject_array[$ss_arr[$ss_id]['k12ea_subject']]) ? '' :$ss_arr[$ss_id]['subject'] ;
				$out_arr[$year_seme]['curriculums'][$course_id][]=$ss_arr[$ss_id]['k12ea_frequency'] ? $ss_arr[$ss_id]['k12ea_frequency'] : '每週上課';
			}
			$res->MoveNext();
		}
	}
	/*
	echo "<pre>";
	print_r($out_arr);
	echo "</pre>";
	exit;
	*/
	
	$time = date("Ymd_His");
	switch($_POST[act]) {
		case 'EXCEL 2003 (.XLS)':
			//xls輸出
			$x=new sfs_xls();
			$x->setUTF8();
			$x->filename="{$SCHOOL_BASE['sch_id']}_{$school_long_name}_國教署人力資源網課表XLS匯出資料檔_{$time}.xls";
			$x->setBorderStyle(1);
			$x->addSheet($school_id);
			//$x->items[0]=array('週次','節次','年級','班級','教師姓名','身分證字號或居留證號','類別','領域','科目','語言別','校訂課程名稱','上課頻率','班級類型');
			$x->items[0]=array('週次','節次','年級','班級','教師姓名','身分證字號或居留證號','類別','領域','科目','語言別','校訂課程名稱','上課頻率');
	
			foreach($out_arr as $year_seme) {
				$curriculums = $year_seme['curriculums'];
				foreach($curriculums as $course) {
					$x->items[]=$course;
				}
			}
			$x->writeSheet();
			$x->process();
			break;
		case 'EXCEL 2007 (.XLSX)':
			//使用XLSXWriter 進行xlsx輸出  
			//$data[0] = array('dow','section','grade','class','teacher','PID','category_k12ea','area_k12ea','subject_k12ea','language_k12ea','subject_school','frequency','class_kind');
			$data[0] = array('class_kind','dow','section','grade','class','teacher','PID','category_k12ea','area_k12ea','subject_k12ea','language_k12ea','subject_school','frequency');
			foreach($out_arr as $year_seme) {
				$curriculums = $year_seme['curriculums'];
				foreach($curriculums as $course) {
					//轉為UTF8
					foreach($course as $k=>$v) $course[$k]=big5_to_utf8($v);
					/*
					echo "<pre>";
					print_r($course);
					echo "</pre>";
					exit;
					*/					
					$data[] = $course;
				}
			}
			/*
			header ('Content-Type: text/html; charset=utf8');
			echo "<pre>";
			print_r($data);
			echo "</pre>";
			exit;
			*/			
			 
			$writer = new XLSXWriter();
			$writer->writeSheet($data);
			
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			$filename = "{$SCHOOL_BASE['sch_id']}_{$school_long_name}_國教署人力資源網課表XLSX匯出資料檔_{$time}.xlsx";
			header('Content-Disposition: attachment;filename='.$filename .' ');
			header('Cache-Control: max-age=0');

			ob_get_clean();
			echo $writer->writeToString();
			ob_end_flush();
			break;
			
			//使用PHPExcel 進行xlsx輸出
			/*			
			$objPHPExcel = new PHPExcel();
			//$objPHPExcel->setActiveSheetIndex(0);
				
			$objPHPExcel->getActiveSheet()->setTitle('AAAA')
			 ->setCellValue('A1', 'dow')
			 ->setCellValue('B1', 'section')
			 ->setCellValue('C1', 'grade')
			 ->setCellValue('D1', 'class')
			 ->setCellValue('E1', 'teacher')
			 ->setCellValue('F1', 'PID')
			 ->setCellValue('G1', 'category')
			 ->setCellValue('H1', 'area')
			 ->setCellValue('I1', 'subject')
			 ->setCellValue('J1', 'language')
			 ->setCellValue('K1', 'subject_school')
			 ->setCellValue('L1', 'frequency');

			foreach($out_arr as $year_seme) {
				$rows = $year_seme['curriculums'];
				$row=2;
				foreach($rows as $foo) {
					$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,$foo[0]); 
					$objPHPExcel->getActiveSheet()->setCellValue('B'.$row,$foo[1]);
					$objPHPExcel->getActiveSheet()->setCellValue('C'.$row,$foo[2]);
					$objPHPExcel->getActiveSheet()->setCellValue('D'.$row,$foo[3]);
					$objPHPExcel->getActiveSheet()->setCellValue('E'.$row,$foo[4]);
					$objPHPExcel->getActiveSheet()->setCellValue('F'.$row,$foo[5]);		
					$objPHPExcel->getActiveSheet()->setCellValue('G'.$row,$foo[6]);
					$objPHPExcel->getActiveSheet()->setCellValue('H'.$row,$foo[7]);
					$objPHPExcel->getActiveSheet()->setCellValue('I'.$row,$foo[8]);
					$objPHPExcel->getActiveSheet()->setCellValue('J'.$row,$foo[9]);
					$objPHPExcel->getActiveSheet()->setCellValue('K'.$row,$foo[10]);
					$objPHPExcel->getActiveSheet()->setCellValue('L'.$row,$foo[11]);
					$row++ ;			
				}
			}
			$filename = "{$SCHOOL_BASE['sch_id']}_{$school_long_name}_國教署人力資源網課表匯出資料檔_{$time}.xlsx";
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');	
			header('Content-Disposition: attachment;filename='.$filename .' ');
			header('Cache-Control: max-age=0');
			$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			ob_get_clean();
			$objWriter->save('php://output');
			ob_end_flush();
			*/
	}
	exit;
}


head('課表EXCEL匯出');
print_menu($menu_p);
echo <<<HERE
<script>
function tagall(status) {
  var i =0;

  while (i < document.myform.elements.length)  {
    if (document.myform.elements[i].name=='year_seme[]') {
      document.myform.elements[i].checked=status;
    }
    i++;
  }
}

function check_select() {
  var i=0; j=0; answer=true;
  while (i < document.myform.elements.length)  {
    if(document.myform.elements[i].name=='year_seme[]') {
		if(document.myform.elements[i].checked) j++;
    }
    i++;
  }
  
  if(j==0) { alert("尚未選取任何學期！"); answer=false; }
  
  return answer;
}

</script>
HERE;

//抓取有課表學期，提供選單之用
$sql="SELECT distinct year,semester FROM score_course ORDER BY year desc,semester desc";
$res=$CONN->Execute($sql) or user_error("讀取課表設定資料失敗！<br>$sql",256);

$main.="<form name='myform' method='post'>
		<table border=2 cellpadding=10 cellspacing=0 style='border-collapse: collapse; font-size=12pt;' bordercolor='#ffcfcf' width='50%'>
		<tr align='center' bgcolor='#ffffaa'><td>選擇學期</td><td>輸出選項</td></tr><tr><td>";
while(!$res->EOF) {
	if(curr_year()-$res->fields[year]<5) {
		$year_seme=$res->fields[year].'_'.$res->fields[semester];
		$year_seme_name=$res->fields[year].'學年度第'.$res->fields[semester].'學期';
		$this_yeae_seme=curr_year().'_'.curr_seme();
		$checked=$this_yeae_seme==$year_seme?'checked':''; 
		$main.="<input type='radio' name='year_seme[]' value='$year_seme' $checked>$year_seme_name<br>";
	}
	$res->MoveNext();
}

$id_mask_list='';
for($i=0;$i<10;$i++){
	$show=$i?$i:'字母';
	$mask_char=substr($masks,$i,1);
	$checked=($mask_char=='*')?'checked':'';
	$id_mask_list.="<input type='checkbox' name='mask[$i]' value='$show' $checked>$show ";
}
//

$main.="</td><td valign='top'>
<br>◎課表資料排序方式：<input type='radio' name='order' value=0 checked>班級節次 <input type='radio' name='order' value=1>教師節次
</td></tr>
<tr><td colspan=2>
<input type='submit' style='border-width:1px; cursor:hand; color:white; background:#5555ff; font-size:20px; width: 100%; height=100' value='EXCEL 2003 (.XLS)' name='act' onclick='return check_select();'>
<input type='submit' style='border-width:1px; cursor:hand; color:white; background:#558855; font-size:20px; width: 100%; height=100' value='EXCEL 2007 (.XLSX)' name='act' onclick='return check_select();'>
</td></tr></table></form>";

echo $main;

foot();


?>