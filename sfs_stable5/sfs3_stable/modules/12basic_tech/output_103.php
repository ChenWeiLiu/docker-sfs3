<?php

include "config.php";
require_once "../../include/sfs_case_excel.php";

sfs_check();

//學期別
$work_year_seme=$_REQUEST['work_year_seme'];
if($work_year_seme=='') $work_year_seme = sprintf("%03d%d",curr_year(),curr_seme());
$curr_year_seme=sprintf("%03d%d",curr_year(),curr_seme());
$academic_year=substr($curr_year_seme,0,-1);
$work_year=substr($work_year_seme,0,-1);
$session_tea_sn=$_SESSION['session_tea_sn'];

if($_POST['act']){
	$_POST['selected_stud']=get_student_list($academic_year);

	//取得12basic_tech紀錄資料
	$final_data=get_final_data($work_year);
	
	//取得學生基本資料
	$student_data=get_student_data($work_year);

	//取得監護人資料
	$domicile_data=get_domicile_data($work_year);
	
	//抓取競賽紀錄
	$competetion_score=count_student_score_competetion($_POST['selected_stud']);
	//echo '<pre>';
	//print_r($competetion_score);
	//echo '</pre>';
	
	$service_score=count_student_score_service($_POST['selected_stud']);
	//echo '<pre>';
	//print_r($service_score);
	//echo '</pre>';
	
	$fault_score=count_student_score_fault($_POST['selected_stud']);
	//echo '<pre>';
	//print_r($fault_score);
	//echo '</pre>';
	
	$fitness_score=count_student_score_fitness($_POST['selected_stud']);
	//echo '<pre>';
	//print_r($fitness_score);
	//echo '</pre>';
	
	//計算多元學習上限
    foreach($_POST['selected_stud'] as $student_sn) {
        $diversification_score[$student_sn]=min($diversification_score_max,$competetion_score[$student_sn]['score']+$service_score[$student_sn]['bonus']+$fault_score[$student_sn]['bonus']+$fitness_score[$student_sn]['bonus']);
		$diversification_score[$student_sn]=sprintf("%2.1f",$diversification_score[$student_sn]);
	}

	
	$particular_score=get_student_particular($work_year);
	//echo '<pre>';
	//print_r($particular_score);
	//echo '</pre>';
	
	$disadvantage_score=get_student_disadvantage($work_year);
	//echo '<pre>';
	//print_r($disadvantage_score);
	//echo '</pre>';
	
	//取得指定學年已經開列的學生多元學習的分數
	$balance_score_t=get_student_balance($work_year);
	//echo '<pre>';
	//print_r($balance_score_t);
	//echo '</pre>';
	
	$balance_area_score=get_student_score_balance($_POST['selected_stud']);
	//echo '<pre>';
	//print_r($balance_area_score);
	//echo '</pre>';

	//取得指定學年已經開列的適性輔導分數
	$personality_score=get_student_personality($work_year);	
	//echo '<pre>';
	//print_r($personality_score);
	//echo '</pre>';
	
	//取得指定學年已經開列的教育會考分數
	$exam_score=get_exam_data($work_year);
	//echo '<pre>';
	//print_r($exam_score);
	//echo '</pre>';
	
	//取得指定學年已經開列的其他項目
	$others_score=get_student_others($work_year);
	//echo '<pre>';
	//print_r($others_score);
	//echo '</pre>';
	
	//取得指定學年已經開列的報名學校
	$student_signup=get_student_signup($work_year);
	//echo '<pre>';
	//print_r($student_signup);
	//echo '</pre>';
	

	//製作抬頭
	switch($_POST['act']){
		case 'EXCEL':
			$x=new sfs_xls();
			$x->setUTF8();
			$x->filename=$SCHOOL_BASE['sch_id'].'_'.$school_long_name.'_五專免試招生系統學生資料檔.xls';
			$x->setBorderStyle(1);
			$x->addSheet($school_id);
			$x->items[0]=array('身分證統一編號','學生姓名','出生年(民國年)','出生月','出生日','年級','班級','座號','報名資格','郵遞區號','地址','市內電話','行動電話','特種生加分類別','減免身分','競賽','擔任幹部','服務時數','服務學習','累計嘉獎','累計小功 ','累計大功 ','累計警告','累計小過','累計大過','日常生活表現評量','肌耐力','柔軟度','瞬發力','心肺耐力','體適能','多元學習表現','技藝教育成績','技藝優良','弱勢身分','弱勢積分','健康與體育','藝術與人文','綜合活動','均衡學習','家長意見','導師意見','輔導小組意見','適性輔導','其它比序項目(全民英檢)','合計積分','報名「北區」五專學校代碼','報名「中區」五專 學校代碼','報名「南區」五專學校代碼','競賽名稱','其他比序項目(多益測驗)','是否報考104年國中教育會考','准考證號碼'); 
			break;
		case 'HTML':
			$main="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' id='AutoNumber1' width='200%'>
				<tr bgcolor='#ffcccc' align='center'><td>身分證統一編號</td><td>學生姓名</td><td>出生年(民國年)</td><td>出生月</td><td>出生日</td><td>年級</td><td>班級</td><td>座號</td><td>報名資格</td><td>郵遞區號</td><td>地址</td><td>市內電話</td><td>行動電話</td><td>特種生加分類別</td><td>減免身分</td><td>競賽</td><td>擔任幹部</td><td>服務時數</td><td>服務學習</td><td>累計嘉獎</td><td>累計小功 </td><td>累計大功 </td><td>累計警告</td><td>累計小過</td><td>累計大過</td><td>日常生活表現評量</td><td>肌耐力</td><td>柔軟度</td><td>瞬發力</td><td>心肺耐力</td><td>體適能</td><td>多元學習表現</td><td>技藝教育成績</td><td>技藝優良</td><td>弱勢身分</td><td>弱勢積分</td><td>健康與體育</td><td>藝術與人文</td><td>綜合活動</td><td>均衡學習</td><td>家長意見</td><td>導師意見</td><td>輔導教師意見</td><td>適性輔導</td><td>其他比序項目(全民英檢)</td><td>合計積分</td><td>報名「北區」五專學校代碼</td><td>報名「中區」五專 學校代碼</td><td>報名「南區」五專學校代碼</td><td>競賽名稱</td><td>其他比序項目(多益測驗)</td><td>是否報考104年國中教育會考</td><td>准考證號碼</td>";
			break;
	}
	
	//取得指定學年已經開列的學生清單
	$sql_select="SELECT a.student_sn,b.stud_id,b.seme_class,b.seme_class_name,b.seme_num FROM 12basic_tech a INNER JOIN stud_seme b ON a.student_sn=b.student_sn WHERE b.seme_year_seme='$work_year_seme' ORDER BY seme_class,seme_num";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	while(!$recordSet->EOF){
		$student_sn=$recordSet->fields['student_sn'];
		$stud_study_cond=$student_data[$student_sn]['stud_study_cond'];
		if($stud_study_cond==0 or $stud_study_cond==15) {
			$no++;
			$stud_id=$recordSet->fields['stud_id'];
			$seme_grade=substr($recordSet->fields['seme_class'],0,1);
			$seme_class=substr($recordSet->fields['seme_class'],-2);
			$seme_class_name=$recordSet->fields['seme_class_name'];
			$seme_num=sprintf('%02d',$recordSet->fields['seme_num']);

			$birth_year=sprintf('%02d',$student_data[$student_sn]['birth_year']);
			$birth_month=sprintf('%02d',$student_data[$student_sn]['birth_month']);
			$birth_day=sprintf('%02d',$student_data[$student_sn]['birth_day']);
			$stud_name=str_replace(' ','',$student_data[$student_sn]['stud_name']);
			$stud_person_id=$student_data[$student_sn]['stud_person_id'];
			//$stud_sex=$student_data[$student_sn]['stud_sex'];
			
			//畢修業
			//$graduate=($graduate_data[$student_sn]==1)?1:0;
			
			//學生聯絡資料處理
			$addr_zip=$student_data[$student_sn]['addr_zip'];
			
			if($data_source) { 
				$guardian_phone=$student_data[$student_sn][$tel_family];
				$guardian_hand_phone=$student_data[$student_sn][$tel_mobile];
				$guardian_address=$student_data[$student_sn][$address_family];		
			} else {	//未設定則依照原先的判斷機制
				$stud_tel_2=$student_data[$student_sn]['stud_tel_2']?$student_data[$student_sn]['stud_tel_2']:$student_data[$student_sn]['stud_tel_1'];
				$stud_addr_2=$student_data[$student_sn]['stud_addr_2']?$student_data[$student_sn]['stud_addr_2']:$student_data[$student_sn]['stud_addr_1'];
				
				$guardian_name=$domicile_data[$student_sn]['guardian_name'];
				$guardian_phone=$domicile_data[$student_sn]['guardian_phone'];
				$guardian_hand_phone=$domicile_data[$student_sn]['guardian_hand_phone']?$domicile_data[$student_sn]['guardian_hand_phone']:$student_data[$student_sn]['stud_tel_3'];

				$guardian_phone=$guardian_phone?$guardian_phone:$stud_tel_2;
				$guardian_address=$domicile_data[$student_sn]['guardian_address']?$domicile_data[$student_sn]['guardian_address']:$stud_addr_2;
			}

			
			//if(!strpos($guardian_hand_phone,'-')) $guardian_hand_phone=$guardian_hand_phone?substr_replace($guardian_hand_phone,'-',4,0):''; 
			
			//依據模組設定進行個資遮罩
			if(!$full_personal_profile){
				$birth_day='00';
				$stud_name=substr($stud_name,0,-2).'○';
				$stud_person_id=substr($stud_person_id,0,-4).'0000';
				$guardian_phone=substr($stud_tel_2,0,-3).'888';
				$guardian_address=substr($guardian_address,0,18).'○○○○○';
				$guardian_name=substr($guardian_name,0,-2).'○';
				$guardian_hand_phone=$guardian_hand_phone?substr($guardian_hand_phone,0,-3).'777':'';
			}
			
			//自動去除 - ( ) 字元
			$search  = array('-', '(', ')',' ');
			$replace = array('', '', '','');
			$guardian_phone=str_replace($search, $replace,$guardian_phone);
			$guardian_hand_phone=str_replace($search,$replace,$guardian_hand_phone);	
			
			
			//計算12basic_tech紀錄資料
			$kind_id=$final_data[$student_sn]['kind_id'];
			$disability_id=$final_data[$student_sn]['disability_id'];	
			$free_id=$final_data[$student_sn]['free_id'];
			
			//分區志願
			$signup_north=($student_signup[$student_sn]['item']['north']=='000')?'':$student_signup[$student_sn]['item']['north'];
			$signup_central=($student_signup[$student_sn]['item']['central']=='000')?'':$student_signup[$student_sn]['item']['central'];
			$signup_south=($student_signup[$student_sn]['item']['south']=='000')?'':$student_signup[$student_sn]['item']['south'];
			
			//其他比序項目
			$others_item=unserialize($others_score[$student_sn]['item']);
			$GEPT=$others_item['GEPT']?$others_item['GEPT']:0;
			$TOEIC=$others_item['TOEIC']?$others_item['TOEIC']:0;
			
			
			//抓取競賽紀錄明細   {$level_array[$data.level]}}-{{$squad_array[$data.squad]}}){{$data.name}}：{{$data.rank}
			$competetion_list='';
			foreach($competetion_score[$student_sn]['detail'] as $key=>$data){
				$competetion_list.=$level_array[$data['level']].'-'.$squad_array[$data['squad']].' '.$data['name'].'：'.$data['rank'].'；';			
			}
			
			//合計分數
			$bonus_total=$diversification_score[$student_sn]+$particular_score[$student_sn]['bonus']+$disadvantage_score[$student_sn]['score']+$balance_score_t[$student_sn]['score']+$personality_score[$student_sn]['bonus'];
			$bonus_total=sprintf("%2.1f",$bonus_total);
			
			//沒資料強制輸出0
			$competetion_score[$student_sn][score]=$competetion_score[$student_sn][score]?sprintf("%1.1f",$competetion_score[$student_sn][score]):'0.0';
			
			$service_score[$student_sn]['leader']=$service_score[$student_sn]['leader']?$service_score[$student_sn]['leader']:0;
			$service_score[$student_sn]['hours']=$service_score[$student_sn]['hours']?$service_score[$student_sn]['hours']:0;
			$service_score[$student_sn]['bonus']=$service_score[$student_sn]['bonus']?$service_score[$student_sn]['bonus']:0;
			
			$fault_score[$student_sn][1]=$fault_score[$student_sn][1]?$fault_score[$student_sn][1]:0;
			$fault_score[$student_sn][3]=$fault_score[$student_sn][3]?$fault_score[$student_sn][3]:0;
			$fault_score[$student_sn][9]=$fault_score[$student_sn][9]?$fault_score[$student_sn][9]:0;
			$fault_score[$student_sn]['a']=$fault_score[$student_sn]['a']?$fault_score[$student_sn]['a']:0;
			$fault_score[$student_sn]['b']=$fault_score[$student_sn]['b']?$fault_score[$student_sn]['b']:0;
			$fault_score[$student_sn]['c']=$fault_score[$student_sn]['c']?$fault_score[$student_sn]['c']:0;
			$fault_score[$student_sn]['bonus']=$fault_score[$student_sn]['bonus']?$fault_score[$student_sn]['bonus']:0;
			
			//是否報名會考
			$acad_exam_reg_num=$exam_score[$student_sn]['acad_exam_reg_num'];
			$acad_exam_ent=($acad_exam_reg_num=='')?0:1;
			
			//輸出資料
			switch($_POST['act']){
				case 'EXCEL':
					$x->items[]=array($stud_person_id,$stud_name,$birth_year,$birth_month,$birth_day,$seme_grade,$seme_class,$seme_num,1,$addr_zip,$guardian_address,$guardian_phone,$guardian_hand_phone,$kind_id,$disability_id,$competetion_score[$student_sn][score],$service_score[$student_sn][leader],$service_score[$student_sn][hours],$service_score[$student_sn][bonus],$fault_score[$student_sn][1],$fault_score[$student_sn][3],$fault_score[$student_sn][9],$fault_score[$student_sn][a],$fault_score[$student_sn][b],$fault_score[$student_sn][c],$fault_score[$student_sn][bonus],$fitness_score[$student_sn][2],$fitness_score[$student_sn][1],$fitness_score[$student_sn][3],$fitness_score[$student_sn][4],$fitness_score[$student_sn][bonus],$diversification_score[$student_sn],$particular_score[$student_sn][score],$particular_score[$student_sn][bonus],$disadvantage_score[$student_sn][disadvantage],$disadvantage_score[$student_sn][score],$balance_area_score[$student_sn][health][avg],$balance_area_score[$student_sn][art][avg],$balance_area_score[$student_sn][complex][avg],$balance_score_t[$student_sn][score],$personality_score[$student_sn][score_adaptive_domicile],$personality_score[$student_sn][score_adaptive_tutor],$personality_score[$student_sn][score_adaptive_guidance],$personality_score[$student_sn][bonus],$GEPT,$bonus_total,$signup_north,$signup_central,$signup_south,$competetion_list,$TOEIC,$acad_exam_ent,sprintf("%09d",intval($acad_exam_reg_num)));
					break;
				case 'HTML':
					$main.="<tr align='center'><td>{$stud_person_id}</td><td>{$stud_name}</td><td>{$birth_year}</td><td>{$birth_month}</td><td>{$birth_day}</td><td>{$seme_grade}</td><td>{$seme_class}</td><td>{$seme_num}</td><td>1</td><td>{$addr_zip}</td><td align='left'>{$guardian_address}</td><td>{$guardian_phone}</td><td>{$guardian_hand_phone}</td><td>{$kind_id}</td><td>{$disability_id}</td><td>{$competetion_score[$student_sn][score]}</td><td>{$service_score[$student_sn][leader]}</td><td>{$service_score[$student_sn][hours]}</td><td>{$service_score[$student_sn][bonus]}</td><td>{$fault_score[$student_sn][1]}</td><td>{$fault_score[$student_sn][3]}</td><td>{$fault_score[$student_sn][9]}</td><td>{$fault_score[$student_sn][a]}</td><td>{$fault_score[$student_sn][b]}</td><td>{$fault_score[$student_sn][c]}</td><td>{$fault_score[$student_sn][bonus]}</td><td>{$fitness_score[$student_sn][2]}</td><td>{$fitness_score[$student_sn][1]}</td><td>{$fitness_score[$student_sn][3]}</td><td>{$fitness_score[$student_sn][4]}</td><td>{$fitness_score[$student_sn][bonus]}</td><td>{$diversification_score[$student_sn]}</td><td>{$particular_score[$student_sn][score]}</td><td>{$particular_score[$student_sn][bonus]}</td><td>{$disadvantage_score[$student_sn][disadvantage]}</td><td>{$disadvantage_score[$student_sn][score]}</td><td>{$balance_area_score[$student_sn][health][avg]}</td><td>{$balance_area_score[$student_sn][art][avg]}</td><td>{$balance_area_score[$student_sn][complex][avg]}</td><td>{$balance_score_t[$student_sn][score]}</td><td>{$personality_score[$student_sn][score_adaptive_domicile]}</td><td>{$personality_score[$student_sn][score_adaptive_tutor]}</td><td>{$personality_score[$student_sn][score_adaptive_guidance]}</td><td>{$personality_score[$student_sn][bonus]}</td><td>$GEPT</td><td>{$bonus_total}</td><td>{$signup_north}</td><td>{$signup_central}</td><td>{$signup_south}</td><td align='left'>{$competetion_list}</td><td>$TOEIC</td><td>$acad_exam_ent</td><td>$acad_exam_reg_num</td></tr>";
					break;
			}
		}
		$recordSet->MoveNext();
	}
	
	
	if(substr($_POST['act'],0,5)=='EXCEL') {
		$x->writeSheet();
		$x->process();
	} else echo $main."</table>";
	exit;
}

//秀出網頁
head("招生系統資料匯出");
echo print_menu($MENU_P,$linkstr);
//取得有紀錄年度的學期的下拉選單
$sql="SELECT DISTINCT academic_year FROM 12basic_tech ORDER BY academic_year";
$rs=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
$radio_year_seme="";
while(!$rs->EOF)
{
	$academic_year=$rs->fields['academic_year'];
	$checked=($work_year==$academic_year)?'checked':'';
	$radio_year_seme.="<input type='radio' name='edit_remote' value=$academic_year $checked>$academic_year ";
	$rs->MoveNext();
}

$data="※輸出格式：<input type='submit' name='act' value='HTML' onclick=\"document.myform.target='$academic_year'\"> <input type='submit' name='act' value='EXCEL' onclick=\"document.myform.target=''\">";

if($full_sealed_check) {
	//檢查是否有可修改紀錄的參與免試學生
	$editable_sn_array=get_editable_sn($work_year);
	if($editable_sn_array) $data="<font size=5 color='red'><br><br><center>有學生資料尚未封存！<br>模組變數設定您必須先封存所有資料才可以進行輸出。</center></font>";
}
	
echo "<form name='myform' method='post' action='$_SERVER[SCRIPT_NAME]'><br>※要輸出的學年：$radio_year_seme	<br><br>$data</form>";

foot();
?>
