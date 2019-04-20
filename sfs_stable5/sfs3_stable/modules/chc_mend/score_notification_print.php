<?php
//$Id: PHP_tmp.html 5310 2009-01-10 07:57:56Z hami $
include "config.php";
include "../../include/sfs_case_studclass.php";
//認證
sfs_check();
$op = $_REQUEST['op'];
$note = $_REQUEST['note'];
$this_filePath = set_upload_path("/school/chc_mend");
//echo $this_filePath;
//執行動作判斷
switch($op){
	case "print_this_seme_all_school":
		$seme = $_REQUEST['Y'];
		$sql_select = "select a.student_sn from chc_mend a,stud_base b where a.student_sn=b.student_sn and seme='{$seme}' group by student_sn order by b.curr_class_num";
		$recordSet=$CONN->Execute($sql_select) or user_error($sql_select, 256);
		while(!$recordSet->EOF){
			list($student_sn)=$recordSet->FetchRow();
			$mend_score = one_seme_mend_score($student_sn,$seme);
			$print_area .= mend_table($student_sn,$mend_score,$this_filePath)."<p style='page-break-after:always'></p>";
		}
		$print_area = substr($print_area,0,-39);
	break;
	case "print_this_seme_this_grade":
		$seme = $_REQUEST['Y'];
		$students_sn =$_REQUEST['students_sn'];
		foreach($students_sn as $value){
			$mend_score = one_seme_mend_score($value,$seme);
			if(!empty($mend_score)){
				$print_area .= mend_table($value,$mend_score,$this_filePath)."<p style='page-break-after:always'></p>";
			}
		}
		$print_area = substr($print_area,0,-39);
	break;
	/*
	case "print_all_seme_this_class":
		$students_sn =$_REQUEST['students_sn'];
		foreach($students_sn as $value1){
			$semes = get_semes($value1);
			foreach($semes as $value2){
				$mend_score .= one_seme_mend_score($value1,$value2);
			}
			$print_area .= mend_table($value1,$mend_score)."<p style='page-break-after:always'></p>";
			//清空記分
			$mend_score="";
		}
		$print_area = substr($print_area,0,-39);
	break;
	*/
	case "print_all_seme_this_stud":
		$student_sn =$_REQUEST['student_sn'];
		$semes = get_mend_semes($student_sn);
		
		foreach($semes as $value){
			$mend_score .= one_seme_mend_score($student_sn,$value);
		}
		$print_area = mend_table($student_sn,$mend_score,$this_filePath);
	break;
	case "print_this_seme_this_stud":
		$student_sn =$_REQUEST['student_sn'];
		$seme = $_REQUEST['Y'];
		$mend_score = one_seme_mend_score($student_sn,$seme);
		$print_area = mend_table($student_sn,$mend_score,$this_filePath);
	break;
	case "print_this_seme_sel_student":
		$seme = $_REQUEST['Y'];
		$students_sn =$_REQUEST['sel_student_sn'];
		foreach($students_sn as $value){
			$mend_score = one_seme_mend_score($value,$seme);
			if(!empty($mend_score)){
				$print_area .= mend_table($value,$mend_score,$this_filePath)."<p style='page-break-after:always'></p>";
			}
		}
		$print_area = substr($print_area,0,-39);
	break;
}

//單學期補考成績
function one_seme_mend_score($student_sn,$seme){
	global $CONN;
	$cht_scope=array(1=>"語文領域","數學領域","自然與生活科技領域","社會領域","健康與體育領域","藝術與人文領域","綜合領域");
	//取此學期有幾科補考
	$num = get_mend_num($student_sn,$seme);
	//取學期中文名
	$cht_ys = get_cht_ys($seme);
	
	$sql_select = "select scope,score_end,seme from chc_mend where student_sn='{$student_sn}' and seme='{$seme}'";
	$recordSet=$CONN->Execute($sql_select) or user_error($sql_select, 256);
	$i = 1;
	while(!$recordSet->EOF){
		list($scope,$score_end)=$recordSet->FetchRow();
			if($i==1){
				$table .= "<tr bgcolor='#FFFFFF'><td rowspan={$num}>{$cht_ys}</td><td>{$cht_scope[$scope]}</td></tr>";
//				$table .= "<tr bgcolor='#FFFFFF'><td rowspan={$num}>{$cht_ys}</td><td>{$cht_scope[$scope]}</td><td>是</td></tr>";
			}else{
				$table .= "<tr bgcolor='#FFFFFF'><td>{$cht_scope[$scope]}</td></tr>";
//				$table .= "<tr bgcolor='#FFFFFF'><td>{$cht_scope[$scope]}</td><td>是</td></tr>";
			}
			$i++;
	}

	return $table;
}

//表格
function mend_table($student_sn,$mend_score,$filePath){
	global $school_short_name;
	$cht_study_cond=array("在籍","轉出","轉入","中輟復學","休學復學","畢業","休學","出國","調校","升級","降級","死亡","中輟","新生入學","轉學復學","在家自學");
	
	$stud_base = get_stud_base($student_sn,"");
	$cht_class = class_id2big5(substr($stud_base['curr_class_num'],0,3),curr_year(),curr_seme());
	$num = substr($stud_base['curr_class_num'],3,2);
	$cht_class_num = $cht_class.$num."號";
	$today = date("Y-m-d");
	$year = date("Y")-1911;
	$month = date("m");
	$note= ReEdit_note($filePath);
	$table="
	<table cellPadding='0' border=0 cellSpacing='0' width='90%' align=center style='border-collapse:collapse;font-size:12pt;line-height:16pt'>
	<tr><td colspan=8 align=center><H3>{$school_short_name} 學習領域補考家長通知單</H3></td></tr>
	<tr align=left>
    <td><H3>{$cht_class_num} {$stud_base['stud_name']} 的家長您好：</H3></td>	
	</tr>
	<tr>
	<td>{$note}</td>
	</tr>
	</table>
	<div align=center>
	<table  style='text-align: left;border-collapse:collapse' border='1' cellspacing='2' cellpadding='2'>
	<tr bgcolor='#FFFFFF'><td width=200>學期</td><td width=200>需補考領域名稱</td></tr>
	{$mend_score}
	</table>
	</div>
		<div align=right><H3>{$school_short_name} 敬啟　{$today}　　　　</H3></div>
	<table cellPadding='0' border=0 cellSpacing='0' width='90%' align=center >
	<tr><td>-------------------------------------------------------------------------------------------------</td></tr>
	<tr><td colspan=8 align=center><H3>{$school_short_name} 學習領域補考家長通知單回條<BR></td></tr>
	<tr></tr>
	<tr style='font-size:12pt;line-height:20pt'  align=left >
	<td>本人已知悉 {$cht_class_num} {$stud_base['stud_name']} 參加補考配合事項,並督導敝子弟,積極準備補考。</td>
	</tr>
	<tr><td align=left>此致</td></tr>
	<tr><td><H3>{$school_short_name}</td></tr>
	<tr></tr>
	<tr><td align=right><H3>家長簽章___________________ {$year}年{$month}月 </td></tr>
	</table>
	";
	return $table;
}
//查某生在哪幾個學期有補考記錄
function get_semes($student_sn){
	global $CONN;
	$sql_select = "select seme from chc_mend where student_sn='{$student_sn}' group by seme";
	$recordSet=$CONN->Execute($sql_select) or user_error($sql_select, 256);
	$i=0;
	while(!$recordSet->EOF){
		list($semes[$i])=$recordSet->FetchRow();
		$i++;
	}
	return $semes;
}

//查某生在某學期有幾個領域要補考$seme為4碼
function get_mend_num($student_sn,$seme){
	global $CONN;
	$seme_select=(!empty($seme))?"and seme='{$seme}'":"";
	$sql_select = "select count(*) from chc_mend where student_sn='{$student_sn}' {$seme_select}";
	$recordSet=$CONN->Execute($sql_select);
	list($num)=$recordSet->FetchRow();
	return $num;
}

//學期轉換成中文
function get_cht_ys($ys){
	$cht_year = explode("_",$ys);
	$cht_seme = ($cht_year[1]==1)?"上":"下";
	$cht_ys = $cht_year[0]."學年".$cht_seme."學期";
	return $cht_ys;
}

function get_mend_semes($student_sn){
	global $CONN;
	$sql_select = "select seme from chc_mend where student_sn='{$student_sn}' group by seme";
	$recordSet=$CONN->Execute($sql_select) or user_error($sql_select, 256);
	while(!$recordSet->EOF){
		list($seme)=$recordSet->FetchRow();
		$semes[]=$seme;
	}
	return $semes;
}

//讀取TXT檔
function ReEdit_note($filePath) {
	
	 //$fp=fopen("../../data/school/chc_mend/note.txt",'r');
	 $fp=fopen($filePath."/note.txt",'r');
	 while(! feof($fp)) {
	 $oldnote .= fgets($fp)."<br>"; 	 
	 
	 }

	return $oldnote; 
		
	}	
?>

<html>
<title>補考通知單</title>
<body onload='window.print()'>
<?php
if(empty($print_area)) $print_area = "無任何資料";
echo $print_area;
?>
</body>
</html>
