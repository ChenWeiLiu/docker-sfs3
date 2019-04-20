<?php
// $Id$
include "config.php";
include "../../include/sfs_case_studclass.php";

sfs_check();

//學期別
$work_year_seme=$_REQUEST['work_year_seme'];
if($work_year_seme=='') $work_year_seme = sprintf("%03d%d",curr_year(),curr_seme());
$curr_year_seme=sprintf("%03d%d",curr_year(),curr_seme());
$academic_year=substr($curr_year_seme,0,-1);
$work_year=substr($work_year_seme,0,-1);
$session_tea_sn=$_SESSION['session_tea_sn'];
$stud_class=$_REQUEST['stud_class'];
$selected_stud=$_POST['selected_stud'];		//取得選擇開列成績證明單學生編號陣列

//輸出成績證明單
if($selected_stud && $_POST['act']=='輸出成績證明單') {
	$data="";
	foreach($selected_stud as $student_key=>$student_sn) {
		$student_data=get_student_data($work_year);		//取得學生基本資料
		$domicile_data=get_domicile_data($work_year);		//取得監護人資料
		$final_data=get_final_data($work_year);		//取得12basic_ylc紀錄資料
		$reward_data=count_student_reward($student_sn);		//取得學生歷年學期獎懲次數
		$absence_data=count_student_seme_abs($student_data[$student_sn]['stud_id']);		//取得學生歷年學期出缺席次數
		$balance=get_student_score_balance($student_sn);		//學生歷年學期均衡學習分數-健體health,藝文art,綜合complex
		$fitness=get_student_score_fitness($student_sn);		//學生歷年學期體適能紀錄
		$competetion=get_student_score_competetion($student_sn);		//學生歷年學期競賽紀錄
		$reward_list=get_student_reward_list($student_sn);		//學生獎懲明細
		//表頭
		if(next($selected_stud)==true) $p_break="page-break-after:always;"; else $p_break="";
		$data.="<div align='center' style='page-break-after:always;'><div align='left' style='width:1000px;line-height:30px;'>";
		$data.="<div align='center' style='padding:20px 0 0px 0; font-size:20pt;'>".(date('Y')-1911)."年雲林縣「十二年國民基本教育免試入學超額比序項目」成績證明單</div>";
		//表一：學生基本資料
		$stud_name=str_replace(' ','',$student_data[$student_sn]['stud_name']);		//姓名
		$stud_school_name=$SCHOOL_BASE['sch_cname_s'];		//畢業學校
		$stud_school_remote=$final_data[$student_sn]['score_remote'];		//偏遠小校
		$stud_person_id=$student_data[$student_sn]['stud_person_id'];		//身分證號
		$stud_seme_num=student_sn_to_site_num($student_sn);             //座號		
		$stud_seme_class="三年".class_id_to_c_name(student_sn_to_class_id($student_sn,$work_year))."班";		//就讀班級
		$stud_school_nature=$final_data[$student_sn]['score_nearby'];		//就近入學
		$stud_birth=sprintf('%02d',$student_data[$student_sn]['birth_year'])."年".sprintf('%02d',$student_data[$student_sn]['birth_month'])."月".sprintf('%02d',$student_data[$student_sn]['birth_day'])."日";		//出生年月日
		$stud_graduation_year=$work_year;		//畢業年
		$stud_sex=($student_data[$student_sn]['stud_sex']==1)?"男":"女";		//性別
		$stud_disadvantage=$final_data[$student_sn]['score_disadvantage'];		//經濟弱勢
		$guardian_name=$domicile_data[$student_sn]['guardian_name'];		//監護人
		if($student_data[$student_sn]['stud_tel_2']!='') $stud_telphone=$student_data[$student_sn]['stud_tel_2']; elseif($student_data[$student_sn]['stud_tel_3']!='') $stud_telphone=$student_data[$student_sn]['stud_tel_3']; else $stud_telphone=$student_data[$student_sn]['stud_tel_1'];		//聯絡電話
		$balance_all_score=$final_data[$student_sn]['score_balance_health']+$final_data[$student_sn]['score_balance_art']+$final_data[$student_sn]['score_balance_complex'];		//均衡學習分數合計
		
		$reward_competetion_fitness_score=$final_data[$student_sn]['score_reward']+$final_data[$student_sn]['score_competetion']+$final_data[$student_sn]['score_fitness'];		//獎勵+競賽+體適能分數
		if ($reward_competetion_fitness_score < $reward_competetion_fitness_score_max) $reward_competetion_fitness_score=$reward_competetion_fitness_score; else $reward_competetion_fitness_score=$reward_competetion_fitness_score_max;		//判斷獎勵+競賽+體適能分數是否超過25分
		
		$stud_score=$final_data[$student_sn]['score_disadvantage']+$final_data[$student_sn]['score_remote']+$final_data[$student_sn]['score_nearby']+$final_data[$student_sn]['score_absence']+$final_data[$student_sn]['score_fault']+$final_data[$student_sn]['score_balance_health']+$final_data[$student_sn]['score_balance_art']+$final_data[$student_sn]['score_balance_complex']+$reward_competetion_fitness_score;
		//$stud_score=$final_data[$student_sn]['score_disadvantage']+$final_data[$student_sn]['score_remote']+$final_data[$student_sn]['score_nearby']+$final_data[$student_sn]['score_reward']+$final_data[$student_sn]['score_absence']+$final_data[$student_sn]['score_fault']+$final_data[$student_sn]['score_balance_health']+$final_data[$student_sn]['score_balance_art']+$final_data[$student_sn]['score_balance_complex']+$final_data[$student_sn]['score_competetion']+$final_data[$student_sn]['score_fitness'];		//可得分數
		$addr_zip=$student_data[$student_sn]['addr_zip']?'('.$student_data[$student_sn]['addr_zip'].')':'';
		$stud_address=$student_data[$student_sn]['stud_addr_2']?$student_data[$student_sn]['stud_addr_2']:$student_data[$student_sn]['stud_addr_1'];		//通訊處
		$data.="<div style='margin:18px 0;'>";
		$data.="<span style='font-size:16pt;'>一、學生基本資料：</span>";
		$data.="<table border='2' cellpadding='3' cellspacing='0' style='width:100%; font-size:15pt; border-collapse:collapse;' bordercolor='#111111'>";
		$data.="<tr align='center'><th>學校</th><th>班級</th><th>座號</th><th>姓名</th><th>出生年月曰</th><th>性別</th><th>身分證號</th><th>監護人</th><th>聯絡電話</th>";
		$data.="<tr align='center'><td>{$stud_school_name}</td><td>{$stud_seme_class}</td><td>{$stud_seme_num}</td><td>{$stud_name}</td><td>{$stud_birth}</td><td>{$stud_sex}</td><td>{$stud_person_id}</td><td>{$guardian_name}</td><td>{$stud_telphone}</td></tr>";
		$data.="</table></div>";

		//表二超額比序項目積分
		$data.="<div style='margin:18px 0;'>";
		$data.="<span style='font-size:16pt;'>二、超額比序項目積分：</span><br><span style='font-size:14pt;'>1.獎勵紀錄、競賽成績、體適能三項最高採計25分。</span>";
		$data.="<span style='font-size:14pt;'></span>";
		$data.="<table border='2' cellpadding='3' cellspacing='0' style='width:100%; font-size:15pt; border-collapse:collapse;' bordercolor='#111111'>";
		$data.="<tr align='center'><th>經濟弱勢</th><th>就近入學</th><th>出缺席紀錄</th><th>無記過紀錄</th><th>均衡學習</th><th>偏遠小校</th><th>獎勵紀錄</th><th>競賽成績</th><th>體適能</th><th>小計</th></tr>";
	$data.="<tr align='center'><td>{$stud_disadvantage}</td><td>{$stud_school_nature}</td><td>{$final_data[$student_sn]['score_absence']}</td><td>{$final_data[$student_sn]['score_fault']}</td><td>{$balance_all_score}</td><td>{$stud_school_remote}</td><td>{$final_data[$student_sn]['score_reward']}</td><td>{$final_data[$student_sn]['score_competetion']}</td><td>{$final_data[$student_sn]['score_fitness']}</td><td>{$stud_score}</td></tr>";
		$data.="</table></div>";

		//表三：品德服務
		$f = explode(",",$reward_semester);
		$grade71=substr($f[0],1,-1);
		$grade72=substr($f[1],1,-1);
		$grade81=substr($f[2],1,-1);
		$grade82=substr($f[3],1,-1);
		$grade91=substr($f[4],1,-1);
		if($absence_data[$grade71]=='') $absence_data[$grade71]=0;
		if($absence_data[$grade72]=='') $absence_data[$grade72]=0;
		if($absence_data[$grade81]=='') $absence_data[$grade81]=0;
		if($absence_data[$grade82]=='') $absence_data[$grade82]=0;
		if($absence_data[$grade91]=='') $absence_data[$grade91]=0;
		$data.="<div style='margin:18px 0;'>";
                 $data.="<span style='font-size:16pt;'>三、品德服務：</span><br><span style='font-size:14pt;'>1.獎勵紀錄：大功\4.5分、小功\1.5分、嘉獎0.5分，最高15分。<br>2.出缺席紀錄：每學期無曠課者得1分。<br>3.無記過紀錄：無警告以上紀錄者(含銷過後)5分、銷過後無累積至小過(含)以上者1分。</span>";
		$data.="<table border='2' cellpadding='3' cellspacing='0' style='width:100%; font-size:14pt; border-collapse:collapse;' bordercolor='#111111'>";
		$data.="<tr align='center'><th rowspan='2'>年級</th><th rowspan='2'>學期</th><th colspan='3'>獎勵紀錄</th><th>出缺席</th><th colspan='3'>懲處紀錄</th></tr>";
		$data.="<tr align='center'><th>大功\</th><th>小功\</th><th>嘉獎</th><th>節數</th><th>警告</th><th>小過</th><th>大過</th></tr>";
                $data.="<tr align='center'><td>7-1</td><td>{$grade71}</td><td>{$reward_data[$grade71][9]}</td><td>{$reward_data[$grade71][3]}</td><td>{$reward_data[$grade71][1]}</td><td>{$absence_data[$grade71]}</td><td>{$reward_data[$grade71][-1]}</td><td>{$reward_data[$grade71][-3]}</td><td>{$reward_data[$grade71][-9]}</td></tr>";
                $data.="<tr align='center'><td>7-2</td><td>{$grade72}</td><td>{$reward_data[$grade72][9]}</td><td>{$reward_data[$grade72][3]}</td><td>{$reward_data[$grade72][1]}</td><td>{$absence_data[$grade72]}</td><td>{$reward_data[$grade72][-1]}</td><td>{$reward_data[$grade72][-3]}</td><td>{$reward_data[$grade72][-9]}</td></tr>";
		$data.="<tr align='center'><td>8-1</td><td>{$grade81}</td><td>{$reward_data[$grade81][9]}</td><td>{$reward_data[$grade81][3]}</td><td>{$reward_data[$grade81][1]}</td><td>{$absence_data[$grade81]}</td><td>{$reward_data[$grade81][-1]}</td><td>{$reward_data[$grade81][-3]}</td><td>{$reward_data[$grade81][-9]}</td></tr>";
		$data.="<tr align='center'><td>8-2</td><td>{$grade82}</td><td>{$reward_data[$grade82][9]}</td><td>{$reward_data[$grade82][3]}</td><td>{$reward_data[$grade82][1]}</td><td>{$absence_data[$grade82]}</td><td>{$reward_data[$grade82][-1]}</td><td>{$reward_data[$grade82][-3]}</td><td>{$reward_data[$grade82][-9]}</td></tr>";
		$data.="<tr align='center'><td>9-1</td><td>{$grade91}</td><td>{$reward_data[$grade91][9]}</td><td>{$reward_data[$grade91][3]}</td><td>{$reward_data[$grade91][1]}</td><td>{$absence_data[$grade91]}</td><td>{$reward_data[$grade91][-1]}</td><td>{$reward_data[$grade91][-3]}</td><td>{$reward_data[$grade91][-9]}</td></tr>";
		$data.="<tr align='center'><th colspan='2'>改過銷過紀錄</th><td bgcolor='#D6D3D6'></td><td bgcolor='#D6D3D6'></td><td bgcolor='#D6D3D6'></td><td bgcolor='#D6D3D6'></td><td>{$reward_data['fault_cancel'][-1]}</td><td>{$reward_data['fault_cancel'][-3]}</td><td>{$reward_data['fault_cancel'][-9]}</td></tr>";
		$data.="<tr align='center'><th colspan='2'>合計</th><td>".($reward_data[$grade71][9]+$reward_data[$grade72][9]+$reward_data[$grade81][9]+$reward_data[$grade82][9]+$reward_data[$grade91][9])."</td><td>".($reward_data[$grade71][3]+$reward_data[$grade72][3]+$reward_data[$grade81][3]+$reward_data[$grade82][3]+$reward_data[$grade91][3])."</td><td>".($reward_data[$grade71][1]+$reward_data[$grade72][1]+$reward_data[$grade81][1]+$reward_data[$grade82][1]+$reward_data[$grade91][1])."</td><td>".($absence_data[$grade71]+$absence_data[$grade72]+$absence_data[$grade81]+$absence_data[$grade82]+$absence_data[$grade91])."</td><td>".($reward_data[$grade71][-1]+$reward_data[$grade72][-1]+$reward_data[$grade81][-1]+$reward_data[$grade82][-1]+$reward_data[$grade91][-1]-$reward_data['fault_cancel'][-1])."</td><td>".($reward_data[$grade71][-3]+$reward_data[$grade72][-3]+$reward_data[$grade81][-3]+$reward_data[$grade82][-3]+$reward_data[$grade91][-3]-$reward_data['fault_cancel'][-3])."</td><td>".($reward_data[$grade71][-9]+$reward_data[$grade72][-9]+$reward_data[$grade81][-9]+$reward_data[$grade82][-9]+$reward_data[$grade91][-9]-$reward_data['fault_cancel'][-9])."</td></tr>";
		$data.="<tr align='center'><th colspan='2'>得分</th><td colspan='3'>{$final_data[$student_sn]['score_reward']}</td><td>{$final_data[$student_sn]['score_absence']}</td><td colspan='3'>{$final_data[$student_sn]['score_fault']}</td></tr>";
		$data.="</table></div>";
		//表四：多元學習
		$data.="<div style='margin:18px 0;'>";
                $data.="<span style='font-size:16pt;'>四、多元學習：</span><br><span style='font-size:14pt;'>1.均衡學習：藝術與人文、健康與體育、綜合活動學期總平均成績及格者，每一領域給3分。<br>2.體適能：任一項成績達門檻標準者得3分，最高採計6分。<br>3.競賽表現：最高採計9分。</span>";
		$data.="<table border='2' cellpadding='3' cellspacing='0' style='width:100%; font-size:14pt; border-collapse:collapse;' bordercolor='#111111'>";
		$data.="<tr align='center'><th rowspan='2'>年級</th><th rowspan='2'>學期</th><th colspan='3'>均衡學習</th><th colspan='5'>體適能</th></tr>";
		$data.="<tr align='center'><th>藝術與人文</th><th>健康與體育</th><th>綜合活動</th><th>坐姿前彎<br>(cm)[%]</th><th>立定跳遠<br>(cm)[%]</th><th>仰臥起坐<br>(次)[%]</th><th>心肺適能<br>(秒)[%]</th><th>獎章</th></tr>";
		$data.="<tr align='center'><td>7-1</td><td>{$grade71}</td><td>{$balance['art'][$grade71]}</td><td>{$balance['health'][$grade71]}</td><td>{$balance['complex'][$grade71]}</td><td>{$fitness[$grade71]['test1']}[{$fitness[$grade71]['prec1']}]</td><td>{$fitness[$grade71]['test3']}[{$fitness[$grade71]['prec3']}]</td><td>{$fitness[$grade71]['test2']}[{$fitness[$grade71]['prec2']}]</td><td>{$fitness[$grade71]['test4']}[{$fitness[$grade71]['prec4']}]</td><td>{$fitness_medal[$fitness[$grade71]['medal']]}</td></tr>";
		$data.="<tr align='center'><td>7-2</td><td>{$grade72}</td><td>{$balance['art'][$grade72]}</td><td>{$balance['health'][$grade72]}</td><td>{$balance['complex'][$grade72]}</td><td>{$fitness[$grade72]['test1']}[{$fitness[$grade72]['prec1']}]</td><td>{$fitness[$grade72]['test3']}[{$fitness[$grade72]['prec3']}]</td><td>{$fitness[$grade72]['test2']}[{$fitness[$grade72]['prec2']}]</td><td>{$fitness[$grade72]['test4']}[{$fitness[$grade72]['prec4']}]</td><td>{$fitness_medal[$fitness[$grade72]['medal']]}</td></tr>";
		$data.="<tr align='center'><td>8-1</td><td>{$grade81}</td><td>{$balance['art'][$grade81]}</td><td>{$balance['health'][$grade81]}</td><td>{$balance['complex'][$grade81]}</td><td>{$fitness[$grade81]['test1']}[{$fitness[$grade81]['prec1']}]</td><td>{$fitness[$grade81]['test3']}[{$fitness[$grade81]['prec3']}]</td><td>{$fitness[$grade81]['test2']}[{$fitness[$grade81]['prec2']}]</td><td>{$fitness[$grade81]['test4']}[{$fitness[$grade81]['prec4']}]</td><td>{$fitness_medal[$fitness[$grade81]['medal']]}</td></tr>";
		$data.="<tr align='center'><td>8-2</td><td>{$grade82}</td><td>{$balance['art'][$grade82]}</td><td>{$balance['health'][$grade82]}</td><td>{$balance['complex'][$grade82]}</td><td>{$fitness[$grade82]['test1']}[{$fitness[$grade82]['prec1']}]</td><td>{$fitness[$grade82]['test3']}[{$fitness[$grade82]['prec3']}]</td><td>{$fitness[$grade82]['test2']}[{$fitness[$grade82]['prec2']}]</td><td>{$fitness[$grade82]['test4']}[{$fitness[$grade82]['prec4']}]</td><td>{$fitness_medal[$fitness[$grade82]['medal']]}</td></tr>";
		$data.="<tr align='center'><td>9-1</td><td>{$grade91}</td><td>{$balance['art'][$grade91]}</td><td>{$balance['health'][$grade91]}</td><td>{$balance['complex'][$grade91]}</td><td>{$fitness[$grade91]['test1']}[{$fitness[$grade91]['prec1']}]</td><td>{$fitness[$grade91]['test3']}[{$fitness[$grade91]['prec3']}]</td><td>{$fitness[$grade91]['test2']}[{$fitness[$grade91]['prec2']}]</td><td>{$fitness[$grade91]['test4']}[{$fitness[$grade91]['prec4']}]</td><td>{$fitness_medal[$fitness[$grade91]['medal']]}</td></tr>";
		$data.="<tr align='center'><th colspan='2'>平均</th><td>{$balance['art']['avg']}</td><td>{$balance['health']['avg']}</td><td>{$balance['complex']['avg']}</td><td bgcolor='#D6D3D6'></td><td bgcolor='#D6D3D6'></td><td bgcolor='#D6D3D6'></td><td bgcolor='#D6D3D6'></td><td>{$fitness_medal[$fitness['avg']['medal']]}</td></tr>";
		$data.="<tr align='center'><th colspan='2'>得分</th><td>{$final_data[$student_sn]['score_balance_art']}</td><td>{$final_data[$student_sn]['score_balance_health']}</td><td>{$final_data[$student_sn]['score_balance_complex']}</td><td bgcolor='#D6D3D6'></td><td bgcolor='#D6D3D6'></td><td bgcolor='#D6D3D6'></td><td bgcolor='#D6D3D6'></td><td>{$final_data[$student_sn]['score_fitness']}</td></tr>";
		$data.="</table><br>";
		$data.="<table border='2' cellpadding='3' cellspacing='0' style='width:100%; font-size:12pt; border-collapse:collapse;' bordercolor='#111111'>";
		$data.="<tr align='center'><th>NO</th><th>範圍</th><th>性質</th><th>學年度</th><th>競賽名稱</th><th>證書日期</th><th>主辦單位</th><th>證書字號</th><th>名次</th><th>得分</th><th>採計</th></tr>";
		for($i=1; $i<=count($competetion); $i++) {
			if($competetion[$i]['squad']==2 && $competetion[$i]['mark']=='V') $team="(".$squad_team[$competetion[$i]['weight']].")"; else $team="";
			$data.="<tr align='center'><td>{$i}</td><td>{$level_array[$competetion[$i]['level']]}</td><td>{$squad_array[$competetion[$i]['squad']]}{$team}</td><td>{$competetion[$i]['year']}</td><td>{$competetion[$i]['name']}</td><td>{$competetion[$i]['certificate_date']}</td><td>{$competetion[$i]['sponsor']}</td><td>{$competetion[$i]['word']}</td><td>{$competetion[$i]['rank']}</td><td>{$competetion[$i]['score']}</td><td>{$competetion[$i]['mark']}</td></tr>";
		}
		$data.="<tr align='center'><th colspan='9'>合計</th><td>{$final_data[$student_sn]['score_competetion']}</td><td bgcolor='#D6D3D6'></td></tr>";
		$data.="</table>";
		$data.="</div>";
		//五：簽名、核章
		$data.="<div style='margin:12px 0 0 0;'><span style='font-size:16pt;'>學生簽名：　　　　　　　　　家長簽名：　　　　　　　　　學校核章：</span></div>";
		$data.="</div></div>";
		//附件、獎懲明細
		$data.="<div align='center' style='{$p_break}'><div style='text-align:left;width:1000px;line-height:30px;'>";
		$data.="<span style='font-size:16pt;'>附件、獎懲明細：</span>";
		$data.="<table border='2' cellpadding='3' cellspacing='0' style='width:100%; font-size:12pt; border-collapse:collapse;' bordercolor='#111111'>";
		$data.="<tr align='center'><th>NO</th><th>年級</th><th>學期別</th><th>獎懲日期</th><th>獎懲類別</th><th>獎懲事由</th><th>獎懲依據</th><th>銷過日期</th><th>採計</th></tr>";
		$n=1;
		for($i=1; $i<=count($reward_list); $i++) {
			if(($reward_list[$i]['reward_kind']<=-1)&&($reward_list[$i]['reward_year_seme']<$fault_start_semester)) continue;
			$data.="<tr align='center'><td>{$n}</td><td>{$reward_list[$i]['reward_grade']}</td><td>{$reward_list[$i]['reward_year_seme']}</td><td>{$reward_list[$i]['reward_date']}</td><td>{$reward_kind[$reward_list[$i]['reward_kind']]}</td><td align='left'>{$reward_list[$i]['reward_reason']}</td><td>{$reward_list[$i]['reward_base']}</td><td>{$reward_list[$i]['reward_cancel_date']}</td><td>{$reward_list[$i]['mark']}</td></tr>";
			$n++;
		}
		$data.="</table>";
		$data.="</div></div>";
	}
	echo $data;
	exit;
}

//秀出網頁
head("成績證明單");
print_menu($menu_p);
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
$linkstr="work_year_seme=$work_year_seme&stud_class=$stud_class";
echo print_menu($MENU_P,$linkstr);

//取得年度與學期的下拉選單
$recent_semester=get_recent_semester_select('work_year_seme',$work_year_seme);

//顯示班級
$class_list=get_semester_graduate_select('stud_class',$work_year_seme,$graduate_year,$stud_class);

//功能鍵
if($stud_class && $work_year_seme==$curr_year_seme) {
	$tool_icon="<input type='button' name='all_stud' value='全選' onClick='javascript:tagall(1);'><input type='button' name='clear_stud'  value='全不選' onClick='javascript:tagall(0);'>　";
	$tool_icon.="<font size=2>　▼：未參與免試學生　　</font>";
	$tool_icon.="<input type='submit' name='act' value='輸出成績證明單' onclick=\"document.myform.target='ts{$academic_year}'\">";
}

$main="<form name='myform' method='post' action='$_SERVER[SCRIPT_NAME]'>$recent_semester $class_list $tool_icon";

//產出學生名單
$data="";
if($stud_class)
{
	//取得指定學年已經開列的學生清單
	$listed=get_student_list($work_year);
	//取得stud_base中班級學生列表並據以與前sql對照後顯示
	$data.="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' id='AutoNumber1' width='100%'>";
	//$sql="SELECT a.student_sn,b.stud_id,b.stud_name,b.stud_sex,c.seme_num FROM 12basic_ylc AS a ,stud_base AS b, stud_seme AS c WHERE a.student_sn=b.student_sn AND a.student_sn=c.student_sn AND a.academic_year='{$academic_year}' AND c.seme_year_seme='{$work_year_seme}' AND c.seme_class='{$stud_class}' ORDER BY c.seme_num";
	$stud_select="SELECT a.student_sn,a.seme_num,b.stud_name,b.stud_sex,b.stud_id,b.stud_study_year FROM stud_seme a inner join stud_base b on a.student_sn=b.student_sn WHERE a.seme_year_seme='$work_year_seme' AND a.seme_class='$stud_class' AND b.stud_study_cond in (0,5) ORDER BY a.seme_num";
	$recordSet=$CONN->Execute($stud_select) or user_error("讀取失敗！<br>$stud_select",256);
	$col=7; //設定每一列顯示幾人
	while(list($student_sn,$seme_num,$stud_name,$stud_sex,$stud_id,$stud_study_year)=$recordSet->FetchRow()) {
		if($pic_checked) $my_pic=get_pic($stud_study_year,$stud_id);		//大頭照
		if($recordSet->currentrow() % $col==1) $data.="<tr align='center'>";
		$stud_bgcolor=($stud_sex==1)?"#EEFFEE":"#FFEEEE";
		if(array_key_exists($student_sn,$listed)) {
			$checkable="<input type='checkbox' name='selected_stud[]' value='{$student_sn}'>";
		} else {
			$checkable="▼";
			$stud_bgcolor="#FFFFDD";
		}
		$data.="<td bgcolor='{$stud_bgcolor}'>{$my_pic} {$checkable} (".sprintf('%02d',$seme_num)."){$stud_name}</td>";
		if($recordSet->currentrow() % $col==0  or $res->EOF) $data.="</tr>";
	}
	$data.="</table>";
}

$main.=$data."</form>";
echo $main;

foot();
?>
