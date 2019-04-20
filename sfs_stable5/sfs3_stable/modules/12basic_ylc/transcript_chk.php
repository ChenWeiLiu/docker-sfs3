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

//輸出積分審查表
if($selected_stud && $_POST['act']=='輸出積分審查表') {
	$data="";
	foreach($selected_stud as $student_key=>$student_sn) {
		//查詢各項成績證明資料
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
		$data.="<div align='center' style='{$p_break}'><div align='left' style='width:1000px;line-height:30px;letter-spacing:1px;'>";
		$data.="<div align='right' style='font-size:14pt;'>編號：<u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u></div>";
		$data.="<div align='center' style='padding:30px 0 40px 0; font-size:20pt;'>".(date('Y')-1911)."年雲林區高級中等學校免試入學超額比序積分審查表</div>";
		//學生基本資料
		$stud_school_name=$SCHOOL_BASE['sch_cname_s'];		//校名
		$stud_seme_class="三年<u>&nbsp;&nbsp;&nbsp;".class_id_to_c_name(student_sn_to_class_id($student_sn,$work_year))."&nbsp;&nbsp;&nbsp;</u>班";		//班級
		$stud_seme_num=student_sn_to_site_num($student_sn);		//座號
		$stud_name=str_replace(' ','',$student_data[$student_sn]['stud_name']);		//姓名
		$data.="<div align='center' style='font-size:14pt;'>校名：<u>&nbsp;&nbsp;&nbsp;".$stud_school_name."&nbsp;&nbsp;&nbsp;</u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$stud_seme_class."<u>&nbsp;&nbsp;&nbsp;".$stud_seme_num."&nbsp;&nbsp;&nbsp;</u>號&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;學生姓名：<u>&nbsp;&nbsp;&nbsp;".$stud_name."&nbsp;&nbsp;&nbsp;</u></div>";
		//審查項目(表格標題)
		$data.="<table border='2' cellpadding='3' cellspacing='0' style='width:100%; font-size:14pt; line-height:30px; letter-spacing:1px; border-collapse:collapse;' bordercolor='#111111'>";
		$data.="<tr align='center' style='font-size:14pt;'><th width='8%' style='padding:5px 5px;'>審查項目</th><th width='46%' style='padding:5px 5px;'>學校初審</th><th width='46%' style='padding:5px 5px;' colspan='2'>委員會複審意見</th></tr>";
		//偏遠小校
		$stud_school_remote=$final_data[$student_sn]['score_remote'];		//偏遠小校積分
		$stud_move_date_y='';
		$stud_move_date_m='';
		$stud_move_date_d='';
		if($school_remote>0){		//轉學生
			$sql_move="SELECT move_date FROM stud_move WHERE student_sn=$student_sn AND move_kind=2";
			$res_move=$CONN->Execute($sql_move) or user_error("讀取失敗！<br>$sql_move",256);
			if($res_move->recordcount()>0){
				$move_date=explode("-",$res_move->fields['move_date']);
				$stud_move_date_y=$move_date[0]-1911;
				$stud_move_date_m=$move_date[1];
				$stud_move_date_d=$move_date[2];
			}
		}
		$data.="<th width='8%' align='center' style='padding:20px 10px;'>偏遠<br>小校</th>";
		$data.="<td width='46%' style='padding:20px 10px;'>
					<p>".(($school_remote>0)?'■':'□')."符合偏遠小校資格：<u>&nbsp;&nbsp;&nbsp;".(($school_remote>0)?$stud_school_remote:'')."&nbsp;&nbsp;&nbsp;</u>分<br>&nbsp;&nbsp;".(($school_remote==2)?'■':'□')."7班以下<br>&nbsp;&nbsp;".(($school_remote==1)?'■':'□')."8-12班<br>&nbsp;&nbsp;".((($school_remote>0)&&($res_move->recordcount()>0))?'■':'□')."轉學生：<u>&nbsp;&nbsp;".$stud_move_date_y."&nbsp;&nbsp;</u>年<u>&nbsp;&nbsp;".$stud_move_date_m."&nbsp;&nbsp;</u>月<u>&nbsp;&nbsp;".$stud_move_date_d."&nbsp;&nbsp;</u>日轉入<br>&nbsp;&nbsp;".((($school_remote>0)&&($res_move->recordcount()==0))?'■':'□')."非轉學生</p>
					<p>".(($school_remote>0)?'□':'■')."不符合偏遠小校資格</p>
				</td>";
		$data.="<td width='46%' valign='top' style='padding:20px 10px;' colspan='2'>
					<p>□符合：<u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>分</p>
					<p>□不符合：<u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>分</p>
				</td>";
		$data.="</tr>";
		
		//獎勵紀錄
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

		$data.="<tr align='left' ;><th width='8%' align='center' style='padding:20px 10px;'>獎勵<br>紀錄</th>";
	$data.="<th width='46%' >大功<u>&nbsp;&nbsp;&nbsp;&nbsp;".($reward_data[$grade71][9]+$reward_data[$grade72][9]+$reward_data[$grade81][9]+$reward_data[$grade82][9]+$reward_data[$grade91][9])."&nbsp;&nbsp;&nbsp;&nbsp;</u>支、小功<u>&nbsp;&nbsp;&nbsp;&nbsp;".($reward_data[$grade71][3]+$reward_data[$grade72][3]+$reward_data[$grade81][3]+$reward_data[$grade82][3]+$reward_data[$grade91][3])."&nbsp;&nbsp;&nbsp;&nbsp;</u>支、嘉獎<u>&nbsp;&nbsp;&nbsp;&nbsp;".($reward_data[$grade71][1]+$reward_data[$grade72][1]+$reward_data[$grade81][1]+$reward_data[$grade82][1]+$reward_data[$grade91][1])."&nbsp;&nbsp;&nbsp;&nbsp;</u>支<p>得分<u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$final_data[$student_sn]['score_reward']."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>分</th><th width='46%' colspan='2'>大功<u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>支、小功<u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>支、嘉獎<u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>支<p>得分<u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>分</th></tr>";

		//競賽成績
		$data.="<tr align='left'>";
		$data.="<th rowspan='4' width='8%' align='center' style='padding:5px 5px;'>競賽<br>成績</th>";
		$data.="<td width='46%'>
					<p>國際賽<br>&nbsp;&nbsp;□已納入獎勵紀錄<br>&nbsp;&nbsp;□未納入獎勵紀錄<br>&nbsp;&nbsp;□符合採計項目：<u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>項<u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>分</p>
				</td>";
		$data.="<td width='46%' colspan='2'>
					<p>國際賽<br>&nbsp;&nbsp;□重複採計<br>&nbsp;&nbsp;□未重複採計<br>&nbsp;&nbsp;□符合採計項目：<u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>項<u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>分</p>
				</td>";
		$data.="</tr>";
		$data.="<tr>";
		$data.="<td width='46%'>
					<p>全國賽<br>&nbsp;&nbsp;□已納入獎勵紀錄<br>&nbsp;&nbsp;□未納入獎勵紀錄<br>&nbsp;&nbsp;□符合採計項目：<u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>項<u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>分</p>
				</td>
				<td colspan='2' width='46%'>
					<p>全國賽<br>&nbsp;&nbsp;□重複採計<br>&nbsp;&nbsp;□未重複採計<br>&nbsp;&nbsp;□符合採計項目：<u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>項<u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>分</p>
				</td>";
		$data.="</tr>";
		$data.="<tr>";
		$data.="<td width='46%'>
					<p>縣賽<br>&nbsp;&nbsp;□已納入獎勵紀錄<br>&nbsp;&nbsp;□未納入獎勵紀錄<br>&nbsp;&nbsp;□符合採計項目：<u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>項<u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>分</p>
				</td>
				<td colspan='2' width='46%'>
					<p>縣賽<br>&nbsp;&nbsp;□重複採計<br>&nbsp;&nbsp;□未重複採計<br>&nbsp;&nbsp;□符合採計項目：<u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>項<u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>分</p>
				</td>";
		$data.="</tr>";
		$data.="<tr>";
		$data.="<td width='46%'>
					<p style='margin-top:15px;'>競賽成績得分：<u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>分</p>
				</td>
				<td colspan='2' width='46%'>
					<p style='margin-top:15px;'>競賽成績得分：<u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>分</p>
				</td>";
		$data.="</tr>";
		//積分
		$reward_competetion_fitness_score=$final_data[$student_sn]['score_reward']+$final_data[$student_sn]['score_competetion']+$final_data[$student_sn]['score_fitness'];		//獎勵+競賽+體適能分數
		if ($reward_competetion_fitness_score < $reward_competetion_fitness_score_max)
		$reward_competetion_fitness_score=$reward_competetion_fitness_score; 
		else 
		$reward_competetion_fitness_score=$reward_competetion_fitness_score_max;
				//判斷獎勵+競賽+體適能分數是否超過25分
		
		$stud_score=$final_data[$student_sn]['score_disadvantage']+$final_data[$student_sn]['score_remote']+$final_data[$student_sn]['score_nearby']+$final_data[$student_sn]['score_absence']+$final_data[$student_sn]['score_fault']+$final_data[$student_sn]['score_balance_health']+$final_data[$student_sn]['score_balance_art']+$final_data[$student_sn]['score_balance_complex']+$reward_competetion_fitness_score;    		//105年積分算法
		//$stud_score=$final_data[$student_sn]['score_disadvantage']+$final_data[$student_sn]['score_remote']+$final_data[$student_sn]['score_nearby']+$final_data[$student_sn]['score_reward']+$final_data[$student_sn]['score_absence']+$final_data[$student_sn]['score_fault']+$final_data[$student_sn]['score_balance_health']+$final_data[$student_sn]['score_balance_art']+$final_data[$student_sn]['score_balance_complex']+$final_data[$student_sn]['score_competetion']+$final_data[$student_sn]['score_fitness'];		//104年前積分算法
		$data.="<tr align='left'><th width='8%' align='center' style='padding:30px 10px;'>積分</th><td width='46%' style='padding:20px 10px;'>未含會考及志願序積分：<u>&nbsp;&nbsp;&nbsp;".$stud_score."&nbsp;&nbsp;&nbsp;</u>分</td><td width='46%' style='padding:20px 10px;' colspan='2'>未含會考及志願序積分：<u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>分</td></tr>";
		//審查人員核章
		$data.="<tr align='left'>";
		$data.="<th rowspan='5' width='8%' align='center' style='padding:30px 10px;'>審查<br>人員<br>核章</th>
				<td width='46%' align='center'>國中端</td>
				<td width='46%' align='center' colspan='2'>免試委員會審查小組</td>";
		$data.="</tr>";
		$data.="<tr>";
		$data.="<td rowspan='4' align='center' valign='bottom' width='46%'>
				（核章人員各校自訂）
				</td>
				<td width='6%' align='center'>１</td><td width='40%'>&nbsp;</td>";
		$data.="</tr>";
		$data.="<tr>";
		$data.="<td width='6%' align='center'>２</td><td width='40%'>&nbsp;</td>";
		$data.="</tr>";
		$data.="<tr>";
		$data.="<td align='center' colspan='2' width='46%'>複審結果</td>";
		$data.="</tr>";
		$data.="<tr>";
		$data.="<td colspan='2' width='46%' valign='top' style='height:180px;'>
					<p style='padding-left:10px;'>□通過<br>□未通過<br>　原因：<u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u><br>&nbsp;<br>　　　　<u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u></p>
				</td>";
		$data.="</tr>";
		//表尾
		$data.="</table>";
		$data.="</div></div>";
	}
	echo $data;
	exit;
}

//秀出網頁
head("積分審查表");
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
	$tool_icon.="<input type='submit' name='act' value='輸出積分審查表' onclick=\"document.myform.target='ts{$academic_year}'\">";
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
