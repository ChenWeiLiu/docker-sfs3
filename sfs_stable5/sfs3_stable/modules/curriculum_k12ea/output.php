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

//big5��utf8
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

//�p�G�T�w��XXLSX�ɮ�
if ($_POST[act]) {
	$out_arr=array();
	//�]�w�ѷӰ}�C
	$semester_arr=array(1=>'�W�Ǵ�',2=>'�U�Ǵ�');
	$class_year_arr=array(0=>'�����~��',1=>'�@�~��',2=>'�G�~��',3=>'�T�~��',4=>'�|�~��',5=>'���~��',6=>'���~��',7=>'�C�~��',8=>'�K�~��',9=>'�E�~��',10=>'�Q�~��',11=>'�Q�@�~��',12=>'�Q�G�~��');
	$dow_arr=array(1=>'�g�@',2=>'�g�G',3=>'�g�T',4=>'�g�|',5=>'�g��',6=>'�g��',7=>'�g��');
	$sector_arr=array(1=>'�Ĥ@�`',2=>'�ĤG�`',3=>'�ĤT�`',4=>'�ĥ|�`',5=>'�Ĥ��`',6=>'�Ĥ��`',7=>'�ĤC�`',8=>'�ĤK�`',9=>'�ĤE�`',10=>'�ĤQ�`',11=>'�ĤQ�@�`',12=>'�ĤQ�G�`',13=>'�ĤQ�T�`',14=>'�ĤQ�|�`');

	//���ͯZ�ų]�w�C��
	$sql="SELECT class_id,c_kind_k12ea FROM school_class WHERE class_id LIKE '{$_POST['year_seme'][0]}_%' ORDER BY class_id";
	$res=$CONN->Execute($sql) or user_error("Ū���Ҫ�]�w��ƥ��ѡI<br>$sql",256);
	while(!$res->EOF) {
		$class_id=$res->fields['class_id'];
		$k12ea_kind_arr[$class_id]=$res->fields['c_kind_k12ea'];
		$res->MoveNext();
	}
	
	//��иp�ҵ{����ARRAY
	$k12ea_category_array = k12ea_category();
	$k12ea_area_array = k12ea_area();
	$k12ea_subject_array = k12ea_subject();
	$k12ea_language_array = k12ea_language();
	
	$k12ea_class_kind_array = k12ea_class_kind();
	
	//�����ئW��
	$subject_arr=array();
	$sql="SELECT subject_id,subject_name FROM score_subject WHERE enable=1";
	$res=$CONN->Execute($sql) or user_error("Ū���Ҫ�]�w��ƥ��ѡI<br>$sql",256);
	while(!$res->EOF){
		$subject_id=$res->fields['subject_id'];
		$subject_arr[$subject_id]=$res->fields['subject_name'];
		$res->MoveNext();
	}

	//����Ҫ��ƶi��}�C�x�s	
	foreach($_POST[year_seme] as $key=>$year_seme){
		
		$tmp=explode('_',$year_seme);
		$this_year=$tmp[0];
		$this_semester=$tmp[1];
		
			
		//����ҵ{���
		$ss_arr=array();
		$sql_ss="SELECT * FROM score_ss WHERE enable=1 AND year='$this_year' AND semester ORDER BY class_id";
		$res_ss=$CONN->Execute($sql_ss) or user_error("Ū���Ҫ�]�w��ƥ��ѡI<br>$sql_ss",256);
		while(!$res_ss->EOF){
			$ss_id=$res_ss->fields['ss_id'];
			$scope_id=$res_ss->fields['scope_id'];
			$subject_id=$res_ss->fields['subject_id'];
			//�Ǯլ�ئW��
			$ss_arr[$ss_id]['subject']=$subject_arr[$subject_id]?$subject_arr[$subject_id]:$subject_arr[$scope_id];
			//��иp�ҵ{����
			$ss_arr[$ss_id]['k12ea_category']=$res_ss->fields['k12ea_category'];
			$ss_arr[$ss_id]['k12ea_area']=$res_ss->fields['k12ea_area'];
			$ss_arr[$ss_id]['k12ea_subject']=$res_ss->fields['k12ea_subject'];
			$ss_arr[$ss_id]['k12ea_language']=$res_ss->fields['k12ea_language'];		

			$res_ss->MoveNext();
		}
		
		
		
		$out_arr[$year_seme]['year']=$this_year;
		$out_arr[$year_seme]['semester']=$semester_arr[$this_semester];
		
		//����Ҫ���	
		$sql = "SELECT a.*,b.name,b.teach_person_id FROM score_course a LEFT JOIN teacher_base b ON a.teacher_sn=b.teacher_sn WHERE a.year=$this_year AND a.semester=$this_semester ORDER BY ";
		$sql .= $_POST['order'] ? 'teacher_sn,day,sector' : 'class_id,day,sector';
			
		$res=$CONN->Execute($sql) or user_error("Ū���Ҫ�]�w��ƥ��ѡI<br>$sql",256);
		while(!$res->EOF){
			$class_id=$res->fields['class_id'];
			if($k12ea_kind_arr[$class_id]) {  //���]�w�Z���������~��X
				$teacher_sn=$res->fields['teacher_sn'];
				$course_id=$res->fields['course_id'];	

				//�Z������
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
				$out_arr[$year_seme]['curriculums'][$course_id][] = '��'.$class_num[3].'�Z';
				$out_arr[$year_seme]['curriculums'][$course_id][]=$res->fields['name'];
				$out_arr[$year_seme]['curriculums'][$course_id][]=$res->fields['teach_person_id'];

				$ss_id=$res->fields['ss_id'];
				$out_arr[$year_seme]['curriculums'][$course_id][]=$k12ea_category_array[$ss_arr[$ss_id]['k12ea_category']];
				$out_arr[$year_seme]['curriculums'][$course_id][]=$k12ea_area_array[$ss_arr[$ss_id]['k12ea_area']];
				$out_arr[$year_seme]['curriculums'][$course_id][]=$k12ea_subject_array[$ss_arr[$ss_id]['k12ea_subject']];
				$out_arr[$year_seme]['curriculums'][$course_id][]=($k12ea_subject_array[$ss_arr[$ss_id]['k12ea_subject']] == '���g�y��') ? $k12ea_language_array[$ss_arr[$ss_id]['k12ea_language']] : '';
				
				//�t�X�H�O�귽���פJ�P�_�޿�A�խq�ҵ{�P���w�ҵ{�ۦP�ɫh����X�խq�ҵ{�W��
				$out_arr[$year_seme]['curriculums'][$course_id][]= ( $ss_arr[$ss_id]['subject'] == $k12ea_subject_array[$ss_arr[$ss_id]['k12ea_subject']]) ? '' :$ss_arr[$ss_id]['subject'] ;
				$out_arr[$year_seme]['curriculums'][$course_id][]=$ss_arr[$ss_id]['k12ea_frequency'] ? $ss_arr[$ss_id]['k12ea_frequency'] : '�C�g�W��';
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
			//xls��X
			$x=new sfs_xls();
			$x->setUTF8();
			$x->filename="{$SCHOOL_BASE['sch_id']}_{$school_long_name}_��иp�H�O�귽���Ҫ�XLS�ץX�����_{$time}.xls";
			$x->setBorderStyle(1);
			$x->addSheet($school_id);
			//$x->items[0]=array('�g��','�`��','�~��','�Z��','�Юv�m�W','�����Ҧr���Ω~�d�Ҹ�','���O','���','���','�y���O','�խq�ҵ{�W��','�W���W�v','�Z������');
			$x->items[0]=array('�g��','�`��','�~��','�Z��','�Юv�m�W','�����Ҧr���Ω~�d�Ҹ�','���O','���','���','�y���O','�խq�ҵ{�W��','�W���W�v');
	
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
			//�ϥ�XLSXWriter �i��xlsx��X  
			//$data[0] = array('dow','section','grade','class','teacher','PID','category_k12ea','area_k12ea','subject_k12ea','language_k12ea','subject_school','frequency','class_kind');
			$data[0] = array('class_kind','dow','section','grade','class','teacher','PID','category_k12ea','area_k12ea','subject_k12ea','language_k12ea','subject_school','frequency');
			foreach($out_arr as $year_seme) {
				$curriculums = $year_seme['curriculums'];
				foreach($curriculums as $course) {
					//�ରUTF8
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
			$filename = "{$SCHOOL_BASE['sch_id']}_{$school_long_name}_��иp�H�O�귽���Ҫ�XLSX�ץX�����_{$time}.xlsx";
			header('Content-Disposition: attachment;filename='.$filename .' ');
			header('Cache-Control: max-age=0');

			ob_get_clean();
			echo $writer->writeToString();
			ob_end_flush();
			break;
			
			//�ϥ�PHPExcel �i��xlsx��X
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
			$filename = "{$SCHOOL_BASE['sch_id']}_{$school_long_name}_��иp�H�O�귽���Ҫ�ץX�����_{$time}.xlsx";
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


head('�Ҫ�EXCEL�ץX');
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
  
  if(j==0) { alert("�|���������Ǵ��I"); answer=false; }
  
  return answer;
}

</script>
HERE;

//������Ҫ�Ǵ��A���ѿ�椧��
$sql="SELECT distinct year,semester FROM score_course ORDER BY year desc,semester desc";
$res=$CONN->Execute($sql) or user_error("Ū���Ҫ�]�w��ƥ��ѡI<br>$sql",256);

$main.="<form name='myform' method='post'>
		<table border=2 cellpadding=10 cellspacing=0 style='border-collapse: collapse; font-size=12pt;' bordercolor='#ffcfcf' width='50%'>
		<tr align='center' bgcolor='#ffffaa'><td>��ܾǴ�</td><td>��X�ﶵ</td></tr><tr><td>";
while(!$res->EOF) {
	if(curr_year()-$res->fields[year]<5) {
		$year_seme=$res->fields[year].'_'.$res->fields[semester];
		$year_seme_name=$res->fields[year].'�Ǧ~�ײ�'.$res->fields[semester].'�Ǵ�';
		$this_yeae_seme=curr_year().'_'.curr_seme();
		$checked=$this_yeae_seme==$year_seme?'checked':''; 
		$main.="<input type='radio' name='year_seme[]' value='$year_seme' $checked>$year_seme_name<br>";
	}
	$res->MoveNext();
}

$id_mask_list='';
for($i=0;$i<10;$i++){
	$show=$i?$i:'�r��';
	$mask_char=substr($masks,$i,1);
	$checked=($mask_char=='*')?'checked':'';
	$id_mask_list.="<input type='checkbox' name='mask[$i]' value='$show' $checked>$show ";
}
//

$main.="</td><td valign='top'>
<br>���Ҫ��ƱƧǤ覡�G<input type='radio' name='order' value=0 checked>�Z�Ÿ`�� <input type='radio' name='order' value=1>�Юv�`��
</td></tr>
<tr><td colspan=2>
<input type='submit' style='border-width:1px; cursor:hand; color:white; background:#5555ff; font-size:20px; width: 100%; height=100' value='EXCEL 2003 (.XLS)' name='act' onclick='return check_select();'>
<input type='submit' style='border-width:1px; cursor:hand; color:white; background:#558855; font-size:20px; width: 100%; height=100' value='EXCEL 2007 (.XLSX)' name='act' onclick='return check_select();'>
</td></tr></table></form>";

echo $main;

foot();


?>