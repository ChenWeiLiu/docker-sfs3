<?php
//本程式應屏東縣要求，在career_race模組有相同複本
include "config.php";

sfs_check();

//學期別
$work_year_seme=$_REQUEST['work_year_seme'];
if($work_year_seme=='') $work_year_seme = sprintf("%03d%d",curr_year(),curr_seme());
$curr_year_seme=sprintf("%03d%d",curr_year(),curr_seme());
$academic_year=substr($curr_year_seme,0,-1);
$work_year=substr($work_year_seme,0,-1);
$session_tea_sn=$_SESSION['session_tea_sn'];



//顯示對照
$level_arr=array("1"=>"國際性","2"=>"全國性","3"=>"區域性(跨縣市)","4"=>"全縣性","5"=>"縣市區(鄉鎮)","6"=>"校內");

//取得stud_base中班級學生列表並據以與前sql對照後顯示
$stud_select="SELECT student_sn,curr_class_num,stud_name,stud_sex,stud_id FROM stud_base
				WHERE student_sn IN (SELECT student_sn FROM 12basic_ptc WHERE academic_year='$work_year') ORDER BY curr_class_num";
$rs=$CONN->Execute($stud_select) or user_error("讀取失敗！<br>$stud_select",256);
while(!$rs->EOF){
	$student_sn=$rs->fields[student_sn];
	$seme_class=substr($rs->fields['curr_class_num'],0,-2);	
	$seme_num=substr($rs->fields['curr_class_num'],-2);
	$stud_name=$rs->fields['stud_name'];
	$stud_sex=$rs->fields['stud_sex'];
	$stud_id=$rs->fields['stud_id'];
	
	//抓取競賽紀錄輸出
	$sql="select * from career_race where student_sn=$student_sn ORDER BY year,nature,certificate_date";
	$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
	if($res->RecordCount()) {
		echo "<div style='page-break-before:always;'><P>※學校：{$school_long_name}</P><P>※班級：{$seme_class} ※座號：{$seme_num} ※姓名：{$stud_name} ※學號：{$stud_id}</P>";
		echo "<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' id='AutoNumber1' width='100%'>
		<tr align='center' bgcolor='#ffcccc'><td>NO.</td><td>學年度</td><td>競賽類別</td><td>範圍</td><td>競賽名稱</td><td>得獎名次</td><td>證書日期</td><td>主辦單位</td><td>字號</td><td>採計</td></tr>";  //<td>性質</td>
		$no=0;
		while(!$res->EOF)
		{
			$level=$res->fields['level'];
			$level=$level_arr[$level];
			$weight=$res->fields['weight']?'是':'';
			if($level) {				
				$no++;
				$bgcolor=$weight?'':"bgcolor='#cccccc'";
				echo "<tr align='center' $bgcolor><td>$no</td><td>{$res->fields['year']}</td><td>{$res->fields['nature']}</td><td>$level</td><td align='left'>{$res->fields['name']}</td><td>{$res->fields['rank']}</td><td>{$res->fields['certificate_date']}</td><td align='left'>{$res->fields['sponsor']}</td><td align='left'>{$res->fields['word']}</td><td>$weight</td></tr>"; //<td>{$res->fields['squad']}</td>
			}
			$res->MoveNext();
		}
		echo "</table></div>";		
	}
	$rs->MoveNext();
}

?>