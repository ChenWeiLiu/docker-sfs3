<?php
//$Id: disgrad.php 6400 2011-03-25 13:42:09Z brucelyc $
include "config.php";
include "../../include/sfs_case_score.php";

//認證
sfs_check();

//印出檔頭
head();
print_menu($menu_p);

$stud_grad_year=$_POST['stud_grad_year']?$_POST['stud_grad_year']:curr_year();

if($_POST['write']=='寫入修業名單'){
	//寫入畢業
	$query="UPDATE grad_stud SET grad_kind=1 WHERE stud_grad_year=$stud_grad_year";
	$res=$CONN->Execute($query) or die("無法更新，語法: $query");
	//寫入修業
	$query="UPDATE grad_stud SET grad_kind=2 WHERE stud_grad_year=$stud_grad_year AND student_sn IN (".$_POST['digrad_sn'].")";
	$res=$CONN->Execute($query) or die("無法更新，語法: $query");	
	
	echo "<script language='JavaScript'> alert(\"已經寫入完成！\")</script>";
	
	//不用再統計
	$_POST['abs_base_days']=0;
}


//顯示說明
$description="<font size=2 color='blue'>
<ol>
<li>設計依據：教育部101年6月13日臺國(二)字第1010109284號函</li>
<li>畢業條件：
<ul>
<li>學習領域畢業成績有四項學習領域平均達丙等以上。<font size=2 color='red'>(四領域以上成績未達60分者，領取修業證書)</font></li>
<li>獎懲抵銷後，未滿三大過(含折算累計：三次警告折算一次小過，三次小過折算一次大過)。<font size=2 color='red'>(得分為-27分以下者，領取修業證書)</font></li>
<li>學習期間扣除學校核可之公(喪、病)假，上課總出席率至少達三分之二以上。<font size=2 color='red'>(事假、曠課、集會缺席日數統計超過輸入之基準日數，領取修業證書)</font></li>
</ul>
</li>
</ol>
<hr></font>";

//產生學期表單
$grad_year_radio="※畢 業 學 年 度：";
$query="select distinct stud_grad_year from grad_stud order by stud_grad_year desc limit 5";
$res=$CONN->Execute($query);
while(!$res->EOF) {
	$checked=($stud_grad_year==$res->fields['stud_grad_year'])?'checked':'';
	$grad_year_radio.="<input type='radio' name='stud_grad_year' value='{$res->fields[0]}' $checked>{$res->fields[0]} ";
	$res->MoveNext();
}

//學生過濾選項
$stud_filter=$_POST['stud_filter'];
$stud_filter_radio="※學生列示選項：<input type='radio' name='stud_filter' value=1".($stud_filter==1?' checked':'').">所有畢業生 <input type='radio' name='stud_filter' value=0".(!$stud_filter?' checked':'').">未達畢業條件學生";

//領域過濾選項
$area_base_count=$_POST['area_base_count']?$_POST['area_base_count']:4;
$area_base_count_text="※達丙等領域數畢業判定基準：<input type='text' name='area_base_count' size=4 value=$area_base_count>";

//獎懲過濾選項
$reward_base_score=$_POST['reward_base_score']?$_POST['reward_base_score']:-26;
$reward_base_score_text="※獎懲分數畢業判定基準：<input type='text' name='reward_base_score' size=4 value=$reward_base_score>以上";

//事曠集缺席日數
$abs_base_days=$_POST['abs_base_days']?$_POST['abs_base_days']:0;
$abs_base_days_text="※事假、曠課、集會缺席日數修業判定基準：<input type='text' name='abs_base_days' size=4 value=$abs_base_days>";

if($abs_base_days){

	//抓取選定學年同步化後的學生sn、基本資料、判斷入學年
	$grad_year_sn=array();
	$student_data_arr=array();
	$stud_id_list='';
	$stud_study_year=99999;
	$query="select a.student_sn,b.curr_class_num,b.stud_name,b.stud_study_year,b.stud_id from grad_stud a inner join stud_base b on a.student_sn=b.student_sn where a.stud_grad_year='$stud_grad_year' order by b.curr_class_num";
	$res=$CONN->Execute($query);
	while(!$res->EOF) {
		$student_sn=$res->fields['student_sn'];
		$stud_id=$res->fields['stud_id'];
		$stud_id_list.="'$stud_id',";
		$grad_year_sn[]=$student_sn;
		$grad_year_id[]=$stud_id;
		$student_data_arr[$student_sn]['curr_class']=substr($res->fields['curr_class_num'],0,3);
		$student_data_arr[$student_sn]['curr_no']=substr($res->fields['curr_class_num'],-2);
		$student_data_arr[$student_sn]['stud_name']=$res->fields['stud_name'];
		$student_data_arr[$student_sn]['stud_id']=$stud_id;
		$stud_study_year=min($stud_study_year,$res->fields['stud_study_year']);
		$res->MoveNext();
	}
	$stud_id_list=substr($stud_id_list,0,-1);

	$year_semester_list="<br>※成績計算學年： [ $stud_study_year ] ~ [ $stud_grad_year ]";
	$semes=array();
	$semes_list='';
	for($i=$stud_study_year;$i<=$stud_grad_year;$i++)
		for($j=1;$j<=2;$j++) { $semes[]=sprintf("%03d%d",$i,$j); $semes_list.="'".sprintf("%03d%d",$i,$j)."',"; }

	$semes_list=substr($semes_list,0,-1);

	/*
	echo "<pre>";
	print_r($fin_score);
	echo "</pre>";
	exit;
	*/


	//判定銷過後獎懲
	$reward=array();
	foreach($grad_year_sn as $student_sn){
		//抓取學生未銷過的獎懲紀錄
		$sql="SELECT reward_year_seme,reward_kind FROM reward WHERE student_sn='$student_sn' AND reward_cancel_date='0000-00-00' ORDER BY student_sn,reward_year_seme";
		$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
		while(!$res->EOF)
		{
			$reward_kind=$res->fields['reward_kind'];
			switch ($reward_kind) {   //功過相抵、轉換為基本分
				case 1:			$reward[$student_sn]++; break;
				case 2:			$reward[$student_sn]+=2; break;
				case 3:			$reward[$student_sn]+=3; break;
				case 4:			$reward[$student_sn]+=6; break;
				case 5:			$reward[$student_sn]+=9; break;
				case 6:			$reward[$student_sn]+=18; break;
				case 7:			$reward[$student_sn][9]+=27; break;
				case -1:		$reward[$student_sn]--;	break;
				case -2:		$reward[$student_sn]-=2; break;
				case -3:		$reward[$student_sn]-=3; break;
				case -4:		$reward[$student_sn]-=6; break;
				case -5:		$reward[$student_sn]-=9; break;
				case -6:		$reward[$student_sn]-=18; break;
				case -7:		$reward[$student_sn]-=27; break;
			}
			$res->MoveNext();
		}
	}

	//判定出席率  "1"=>"事假","2"=>"病假","3"=>"曠課","4"=>"集會","5"=>"公假","6"=>"其他"
	//僅取 "1"=>"事假"、"3"=>"曠課"、"4"=>"集會"
	$abs_data_arr=array();
	$sql="SELECT stud_id,sum(abs_days) FROM stud_seme_abs WHERE seme_year_seme IN ($semes_list) AND abs_kind IN (1,3,4) AND stud_id IN ($stud_id_list) GROUP BY stud_id";
	$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
	while(!$res->EOF)
	{
		$stud_id=$res->fields['stud_id'];
		$abs_data_arr[$stud_id]=$res->fields[1];
		$res->MoveNext();
	}

	/*
	echo "<pre>";
	print_r($abs_data);
	echo "</pre>";
	exit;
	*/

	//抓取成績
	$fin_score=cal_fin_score($grad_year_sn,$semes);

	$show_ss=array("language"=>"語文","math"=>"數學","nature"=>"自然與生活科技","social"=>"社會","health"=>"健康與體育","art"=>"藝術與人文","complex"=>"綜合活動");
	$student_data="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' id='AutoNumber1' width='100%'><tr align='center' bgcolor='#ccffcc'><td>班級</td><td>座號</td><td>學號</td><td>姓名</td>";
	foreach($show_ss as $key=>$value) $student_data.="<td bgcolor='#ccccff'>$value</td>";
	$student_data.="<td>丙等以上</td><td>獎懲</td><td>事曠集缺席節次</td></tr>";

	
	$succ_count=array(7=>0,6=>0,5=>0,4=>0,3=>0,2=>0,1=>0,0=>0);
	foreach($student_data_arr as $student_sn=>$data){
		$succ=$fin_score[$student_sn]['succ'];
		$stud_id=$data['stud_id'];
		$abs_data=$abs_data_arr[$stud_id];
		if($fin_score[$student_sn]['life']['avg']['score']>=60) $succ--;
		$reward_score=$reward[$student_sn]?$reward[$student_sn]:0;
		$this_one="<tr align='center'><td>{$data['curr_class']}</td><td>{$data['curr_no']}</td><td>$stud_id</td><td>{$data['stud_name']}</td>";
		foreach($show_ss as $key=>$value){
			$bgcolor=$fin_score[$student_sn][$key]['avg']['score']<60?"bgcolor='#ffcccc'":"";
			$this_one.="<td $bgcolor>{$fin_score[$student_sn][$key]['avg']['score']}</td>";
		}		
		$bgcolor=($succ>=$area_base_count)?"":"bgcolor='#ffcccc'";
		$bgcolor_reward=$reward[$student_sn]<=$reward_base_score?"":"bgcolor='#ffcccc'";
		$bgcolor_abs=$abs_data_arr[$stud_id]<$abs_base_days?"":"bgcolor='#ffcccc'";
		$this_one.="<td $bgcolor>$succ</td><td $bgcolor_reward>{$reward[$student_sn]}</td><td $bgcolor_abs>$abs_data</td></tr>";

		$succ_count[$succ]++;
		if($stud_filter) $student_data.=$this_one; else if($succ<$area_base_count or $reward_score<$reward_base_score or $abs_data_arr[$stud_id]>=$abs_base_days) { $student_data.=$this_one; $digrad_sn_list.="$student_sn,"; }
	}
	$digrad_sn_list=substr($digrad_sn_list,0,-1);
	$student_data.="</table>";
	
	$area_count="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' id='AutoNumber1' width='100%'><tr bgcolor='#CCFFCC' align='center'><td>丙等以上學習領域達成數</td><td>人數統計</td></tr>";
	foreach($succ_count as $key=>$value) {
		$area_count.="<tr align='center'><td>$key</td><td>$value</td></tr>";	
	}
	$area_count.="</table><br>";
	
	
} else $err_msg="<hr><center><font color='red'>※請不要忘記輸入事假、曠課、集會缺席日數修業判定基準數值囉！</font></center><hr>";
if($digrad_sn_list and !$stud_filter) $enable_disgrad="<input type='hidden' name='digrad_sn' value='$digrad_sn_list'><input type='submit' name='write' value='寫入修業名單' onclick='return confirm(\"確定要寫入？ 寫入前會先將本年度原設定清空！\")'>";
echo "<form name='myform' method='post'>$description $grad_year_radio $year_semester_list<br>$stud_filter_radio<br>$area_base_count_text<br>$reward_base_score_text<br>$abs_base_days_text <input type='submit' name='act' value='統計顯示'>$enable_disgrad $area_count $student_data</form>$err_msg";

foot();
?>