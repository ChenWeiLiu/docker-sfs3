<?php

include "config.php";

sfs_check();

//學期別
$work_year_seme=$_REQUEST['work_year_seme'];
if($work_year_seme=='') $work_year_seme = sprintf("%03d%d",curr_year(),curr_seme());
$curr_year_seme=sprintf("%03d%d",curr_year(),curr_seme());
$academic_year=substr($curr_year_seme,0,-1);
$work_year=substr($work_year_seme,0,-1);
$session_tea_sn=$_SESSION['session_tea_sn'];


$all=array();
//取得stud_base中班級學生列表並據以與前sql對照後顯示
$stud_select="SELECT a.student_sn,a.seme_class,a.seme_num,b.stud_name,b.stud_sex,b.stud_id FROM stud_seme a INNER JOIN stud_base b ON a.student_sn=b.student_sn
				WHERE a.seme_year_seme='$work_year_seme' AND a.seme_class like '9%' AND b.stud_study_cond IN (0,5,15) ORDER BY a.seme_class,a.seme_num";
$rs=$CONN->Execute($stud_select) or user_error("讀取失敗！<br>$stud_select",256);
while(!$rs->EOF){
	$student_sn=$rs->fields[student_sn];
	$seme_class=$rs->fields[seme_class];
	
	$all[$seme_class][$student_sn]['seme_num']=$rs->fields['seme_num'];
	$all[$seme_class][$student_sn]['stud_name']=$rs->fields['stud_name'];
	$all[$seme_class][$student_sn]['stud_sex']=$rs->fields['stud_sex'];
	$all[$seme_class][$student_sn]['stud_id']=$rs->fields['stud_id'];

	$sn_list.="$student_sn,";
	
	$rs->MoveNext();
}
$sn_list=substr($sn_list,0,-1);

//班級幹部
$sql="select * from career_self_ponder where id='3-2' and student_sn IN ($sn_list)";
$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
while(!$res->EOF)
{
	$student_sn=$res->fields['student_sn'];
	$ponder_array=unserialize($res->fields['content']);
	foreach($ponder_array as $seme_key=>$data){
		if(strpos($leader[$student_sn]['memo'],$data['memo'])===false) $leader[$student_sn]['memo'].=$data['memo'];
		foreach($data as $key=>$value){
			if($key<>'data' and $key<>'memo'){
				foreach($value as $leader_name){
					if($leader_name and array_search($leader_name,$leader_allowed))	$leader[$student_sn][$seme_key].=$leader_name.'<br>';
				}
			}
		}
	}
	$res->MoveNext();
}	

//社團	
$sql="SELECT seme_year_seme,association_name,student_sn FROM association WHERE student_sn IN ($sn_list) AND stud_post='社長'";
$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
while(!$res->EOF)
{
		$student_sn=$res->fields['student_sn'];
		
		//抓取學生學期就讀班級
		$stud_seme_arr=get_student_seme($student_sn);
		
		$seme_year_seme=$res->fields[0];
		$seme_key=array_search($seme_year_seme,$stud_seme_arr);
		$leader[$student_sn][$seme_key].=$res->fields[1].'社長<br>';
		$res->MoveNext();
}


//異動-轉入
$sql="SELECT move_year_seme,student_sn FROM stud_move WHERE move_kind=2 AND student_sn IN ($sn_list)";
$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
while(!$res->EOF)
{
		$move_year_seme=sprintf('%04d',$res->fields[0]);
		$student_sn=$res->fields[1];	

		$move[$student_sn].=$move_year_seme.'轉入<br>';
		$res->MoveNext();
}


//輸出
//取得12basic_ptc紀錄資料
$final_data=get_final_data($work_year);


foreach($all as $class_id=>$students){
	$class_name=substr($class_id,0,1).'年'.substr($class_id,-2).'班';
	$semester_year=$work_year+1;
	echo "<h2>屏東區{$semester_year}學年度高中職免試入學<br>超額比序多元學習表現之「服務表現」證明單</h2>";
	echo "※學校：".$school_long_name.'      　　　　※班級：'.$class_name;
	echo "<table border='2' cellpadding='3' cellspacing='0' style='font-size:11px; border-collapse: collapse' bordercolor='#111111' id='AutoNumber1' width='100%'>
			<tr align='center'><td rowspan=2>座號</td><td rowspan=2>姓名</td><td colspan=5>服務表現項目</td><td rowspan=2>積分</td><td rowspan=2>備註</td></tr>
			<tr align='center'><td>7上</td><td>7下</td><td>8上</td><td>8下</td><td>9上</td></tr>";
	foreach($students as $student_sn=>$data) {	
        $score_service=$final_data[$student_sn]['score_service'];
		echo "<tr align='center'><td>{$data['seme_num']}</td><td>{$data['stud_name']}</td><td>{$leader[$student_sn]['7-1']}</td><td>{$leader[$student_sn]['7-2']}</td><td>{$leader[$student_sn]['8-1']}</td><td>{$leader[$student_sn]['8-2']}</td><td>{$leader[$student_sn]['9-1']}</td><td>$score_service</td><td>{$move[$student_sn]}{$leader[$student_sn]['memo']}</td></tr>";
	}
	echo '</table><table width=80%><tr height=40><td>導師簽名：</td><td>處室核章：</td><td>校長核章：</td><td></td></tr></table> //<td>初審核章：</td>
	<p style="page-break-after: always;"></p>';
}

?>