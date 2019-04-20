<?php
// $Id:$

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
	//取得指定學年已經開列的學生清單
	$student_list_array=get_student_list($work_year);

	//取得學生基本資料
	$student_data=get_student_data($work_year);

	//取得監護人資料
	$domicile_data=get_domicile_data($work_year);
	
	//取得畢業資格
	$graduate_data=get_graduate_data($work_year);

//echo '<pre>';	
//print_r($graduate_data);
//echo '<pre>';
	
	//取得12basic_tcntc紀錄資料
	$final_data=get_final_data($work_year);
	
	//製作抬頭
	switch($_POST['act']){
		case 'EXCEL':
			$x=new sfs_xls();
			$x->setUTF8();
			$x->filename=$SCHOOL_BASE['sch_id'].'_'.$school_long_name.'_招生系統學生資料檔.xls';
			$x->setBorderStyle(1);
			//$x->addSheet($school_id);
			//2015.05.08標籤改為 student
			$x->addSheet('student');
			$x->items[0]=array('考區代碼','集報單位代碼','序號','學號','班級','座號','學生姓名','身分證統一編號','性別','出生年(民國年)','出生月','出生日','畢業學校代碼','畢業年(民國年)','畢肄業','學生身分','身心障礙','就學區','低收入戶','中低收入戶','失業勞工子女','資料授權','家長姓名','市內電話','行動電話','郵遞區號','通訊地址','原住民是否含母語認證','非中華民國身分證號','特殊生加分百分比','就近入學','偏遠地區','健康與體育','藝術與人文','綜合活動','記過紀錄','大功支數','小功支數','嘉獎支數','服務學習得分','社團得分');  //'扶助弱勢','均衡學習','德行表現','無記過紀錄','獎勵紀錄'
			break;
		case 'HTML':
			$main="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' id='AutoNumber1' width='200%'>
				<tr bgcolor='#ffcccc' align='center'><td>考區代碼</td><td>集報單位代碼</td><td>序號</td><td>學號</td><td>班級</td><td>座號</td><td>學生姓名</td><td>身分證統一編號</td><td>性別</td><td>出生年(民國年)</td><td>出生月</td><td>出生日</td><td>畢業學校代碼</td><td>畢業年(民國年)</td><td>畢肄業</td><td>學生身分</td><td>身心障礙</td><td>就學區</td><td>低收入戶</td><td>中低收入戶</td><td>失業勞工子女</td><td>資料授權</td><td>家長姓名</td><td>市內電話</td><td>行動電話</td><td>郵遞區號</td><td>通訊地址</td><td>原住民是否含母語認證</td><td>非中華民國身分證號</td><td>特殊生加分百分比</td><td>就近入學</td><td>偏遠地區</td><td>健康與體育</td><td>藝術與人文</td><td>綜合活動</td><td>記過紀錄</td><td>大功支數</td><td>小功支數</td><td>嘉獎支數</td><td>服務學習得分</td><td>社團得分</td>";
			break;
	}

	//取得指定學年已經開列的學生清單
	$sql_select="SELECT a.student_sn,b.stud_id,b.seme_class,b.seme_class_name,b.seme_num FROM 12basic_tcntc a INNER JOIN stud_seme b ON a.student_sn=b.student_sn WHERE b.seme_year_seme='$work_year_seme' ORDER BY seme_class,seme_num";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	while(!$recordSet->EOF){
		$student_sn=$recordSet->fields['student_sn'];
		$stud_study_cond=$student_data[$student_sn]['stud_study_cond'];
		if($stud_study_cond==0 or $stud_study_cond==15) {
			$no++;
			$stud_id=$recordSet->fields['stud_id'];
			$seme_class=substr($recordSet->fields['seme_class'],-2);
			$seme_class_name=$recordSet->fields['seme_class_name'];
			$seme_num=sprintf('%02d',$recordSet->fields['seme_num']);

			$birth_year=sprintf('%02d',$student_data[$student_sn]['birth_year']);
			$birth_month=sprintf('%02d',$student_data[$student_sn]['birth_month']);
			$birth_day=sprintf('%02d',$student_data[$student_sn]['birth_day']);
			$stud_name=str_replace(' ','',$student_data[$student_sn]['stud_name']);
			$stud_person_id=$student_data[$student_sn]['stud_person_id'];
			$stud_sex=$student_data[$student_sn]['stud_sex'];
			//非中華民國身分證號判斷
			$stud_country_kind=($student_data[$student_sn]['stud_country_kind']<>'0')?"V":"";
			
			//畢修業
			$graduate=($graduate_data[$student_sn]==1)?1:0;
			
			//學生聯絡資料處理
			$addr_zip=$student_data[$student_sn]['addr_zip'];
			$guardian_name=$domicile_data[$student_sn]['guardian_name'];
			if($data_source) { 
				$guardian_phone=$student_data[$student_sn][$tel_family];
				$guardian_hand_phone=$student_data[$student_sn][$tel_mobile];
				$guardian_address=$student_data[$student_sn][$address_family];		
			} else {	//未設定則依照原先的判斷機制
				$stud_tel_2=$student_data[$student_sn]['stud_tel_2']?$student_data[$student_sn]['stud_tel_2']:$student_data[$student_sn]['stud_tel_1'];
				$stud_addr_2=$student_data[$student_sn]['stud_addr_2']?$student_data[$student_sn]['stud_addr_2']:$student_data[$student_sn]['stud_addr_1'];
				
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
			
			//計算12basic_tcntc紀錄資料
			$kind_id=$final_data[$student_sn]['kind_id'];
			$disability_id=$final_data[$student_sn]['disability_id'];	
			$free_id=$final_data[$student_sn]['free_id'];
			
			//低收失業		
			switch($free_id){
				case 0: $free_1=0; $free_2=0; $free_3=0; break;
				case 1: $free_1=1; $free_2=0; $free_3=0; break;
				case 2: $free_1=0; $free_2=1; $free_3=0; break;
				case 3: $free_1=0; $free_2=0; $free_3=1; break;		
			}

			$score_disadvantage=$final_data[$student_sn]['score_remote']+$final_data[$student_sn]['score_disadvantage'];
			$score_balance=$final_data[$student_sn]['score_balance_health']+$final_data[$student_sn]['score_balance_art']+$final_data[$student_sn]['score_balance_complex'];
			$score_morality=$final_data[$student_sn]['score_association']+$final_data[$student_sn]['score_service'];
			$score_fault=$final_data[$student_sn]['score_fault'];
			$score_reward=$final_data[$student_sn]['score_reward'];
			
			//配合招生統輸出需要的
			$score_remote=$final_data[$student_sn]['score_remote'];
			$score_balance_health=$final_data[$student_sn]['score_balance_health'];
			$score_balance_art=$final_data[$student_sn]['score_balance_art'];
			$score_balance_complex=$final_data[$student_sn]['score_balance_complex'];
			$score_association=$final_data[$student_sn]['score_association'];
			$score_service=$final_data[$student_sn]['score_service'];
			$language_certified=$final_data[$student_sn]['language_certified'];
			//抓取獎勵支數
			$reward_list=get_student_reward_list($work_year);
			$reward_1=$reward_list[$student_sn][1]?$reward_list[$student_sn][1]:0;
			$reward_3=$reward_list[$student_sn][3]?$reward_list[$student_sn][3]:0;
			$reward_9=$reward_list[$student_sn][9]?$reward_list[$student_sn][9]:0;
	/*		
	echo '<pre>';
	print_r($final_data);
	echo '</pre>';
	exit;
	*/		
			//輸出資料
			$graduate_year=$work_year+1;
			switch($_POST['act']){
				case 'EXCEL':
					$x->items[]=array($area_code,$school_id,$no,$stud_id,$seme_class,$seme_num,$stud_name,$stud_person_id,$stud_sex,$birth_year,$birth_month,$birth_day,$school_id,$graduate_year,$graduate,$kind_id,$disability_id,'',$free_1,$free_2,$free_3,0,$guardian_name,$guardian_phone,$guardian_hand_phone,$addr_zip,$guardian_address,$language_certified,$stud_country_kind,'0',$school_nature,$score_remote,$score_balance_health,$score_balance_art,$score_balance_complex,$score_fault,$reward_9,$reward_3,$reward_1,$score_service,$score_association); //,$score_fault,$score_reward
					break;
				case 'HTML':
					$main.="<tr align='center'><td>$area_code</td><td>$school_id</td><td>$no</td><td>$stud_id</td><td>$seme_class</td><td>$seme_num</td><td>$stud_name</td><td>$stud_person_id</td><td>$stud_sex</td><td>$birth_year</td><td>$birth_month</td><td>$birth_day</td><td>$school_id</td><td>$graduate_year</td><td>$graduate</td><td>$kind_id</td><td>$disability_id</td><td></td><td>$free_1</td><td>$free_2</td><td>$free_3</td><td>0</td><td>$guardian_name</td><td>$guardian_phone</td><td>$guardian_hand_phone</td><td>$addr_zip</td><td align='left'>$guardian_address</td>
						<td>$language_certified</td><td>$stud_country_kind</td><td>0</td><td>$school_nature</td><td>$score_remote</td><td>$score_balance_health</td><td>$score_balance_art</td><td>$score_balance_complex</td><td>$score_fault</td><td>$reward_9</td><td>$reward_3</td><td>$reward_1</td><td>$score_service</td><td>$score_association</td></tr>";  //<td>$score_fault</td><td>$score_reward</td>
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
$sql="SELECT DISTINCT academic_year FROM 12basic_tcntc ORDER BY academic_year";
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
