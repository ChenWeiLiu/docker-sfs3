<?php
//$Id: 2xml.php 5582 2009-08-11 15:59:55Z brucelyc $
if (!$CONN) exit;
$xml='<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE 學生交換資料 SYSTEM "http://sfshelp.tcc.edu.tw/download/student_3_1.dtd">
<學生交換資料>
';
while(list($seme_class,$d)=each($health_data->stud_data)) {
	$gid=substr($seme_class,0,1);
	$yid=substr($seme_class,-2,2);
	while(list($seme_num,$dd)=each($d)) {
		$sn=$dd[student_sn];
$xml.='	<學生資料>
		<基本資料>
			<學生姓名></學生姓名>
			<學生性別>'.(($health_data->stud_base[$sn][stud_sex]==1)?'男':'女').'</學生性別>
			<學生生日>'.$health_data->stud_base[$sn][stud_birthday].'</學生生日>
			<現在年級>'.$gid.'</現在年級>
			<現在班級>'.$yid.'</現在班級>
			<現在座號>'.$seme_num.'</現在座號>
			<入學年>'.$health_data->stud_base[$sn][stud_study_year].'</入學年>
			<身分證證照>
				<國籍></國籍>
				<證照種類></證照種類>
				<證照號碼>'.$health_data->stud_base[$sn][stud_person_id].'</證照號碼>
				<僑居地></僑居地>
			</身分證證照>
		</基本資料>
		<健康資料>
			<健康基本資料>
				<個人疾病史>
';
		//處理個人疾病史
		if (count($health_data->stud_base[$sn][disease]) > 0) {
			foreach($health_data->stud_base[$sn][disease] as $kk=>$dd) {
$xml.='
					<個人疾病史_資料內容>
						<個人疾病史_類別>'.iconv("BIG5","UTF-8",$dk_arr[$dd]).'</個人疾病史_類別>
						<個人疾病史_名稱></個人疾病史_名稱>
					</個人疾病史_資料內容>
';
			}
		} else {
$xml.='					<個人疾病史_資料內容>
						<個人疾病史_類別>無</個人疾病史_類別>
						<個人疾病史_名稱></個人疾病史_名稱>
					</個人疾病史_資料內容>
';
		}
$xml.='				</個人疾病史>
				<重大傷病及傷殘>
					<領有重大傷病證明卡>'.iconv("BIG5","UTF-8",$sdk_arr[$health_data->stud_base[$sn][serious][0]]).'</領有重大傷病證明卡>
					<領有身心障礙手冊>
						<領有身心障礙手冊_類別>'.iconv("BIG5","UTF-8",$bk_arr[$health_data->stud_base[$sn][bodymind][bm_id]]).'</領有身心障礙手冊_類別>
						<領有身心障礙手冊_等級>'.iconv("BIG5","UTF-8",$bl_arr[$health_data->stud_base[$sn][bodymind][bm_level]]).'</領有身心障礙手冊_等級>
					</領有身心障礙手冊>
				</重大傷病及傷殘>
				<參加保險>
					<參加保險_資料內容>
						<參加保險_類別></參加保險_類別>
					</參加保險_資料內容>
				</參加保險>
				<家族病史>
					<家族病史_資料內容>
						<患有重大遺傳疾病家屬稱謂></患有重大遺傳疾病家屬稱謂>
						<遺傳性疾病名稱></遺傳性疾病名稱>
					</家族病史_資料內容>
				</家族病史>
			</健康基本資料>
			<經常性檢查>
';
		//處理學期檢查記錄
		$sight_arr=array("My"=>"近視","Hy"=>"遠視","Ast"=>"弱視","Amb"=>"散光","other"=>"");
		if (count($health_data->health_data[$sn]) > 0) {
			foreach($health_data->health_data[$sn] as $kk=>$dd) {
$xml.='				<學期檢查記錄>
					<學期檢查_年級>'.(intval(substr($kk,0,3))-$health_data->stud_base[$sn][stud_study_year]+1).'</學期檢查_年級>
					<學期檢查_學期>'.substr($kk,-1,1).'</學期檢查_學期>
					<生長發育>
						<身高>'.$dd[height].'</身高>
						<體重>'.$dd[weight].'</體重>
						<生長發育評值>'.iconv("BIG5","UTF-8",$Bid_arr[$dd[Bid]]).'</生長發育評值>
					</生長發育>	
					<視力>
						<視力檢查結果>
							<視力檢查_部位>右</視力檢查_部位>
							<裸視視力>'.$dd[r][sight_o].'</裸視視力>
							<矯正視力>'.$dd[r][sight_r].'</矯正視力>
							<視力缺點>
';
				reset($sight_arr);
				foreach($sight_arr as $kkk=>$ddd) {
					if ($dd[r][$kkk]) {
$xml.='								<視力缺點_資料內容>
									<視力缺點_類別>'.$ddd.'</視力缺點_類別>
								</視力缺點_資料內容>
';
					}
				}
$xml.='							</視力缺點>
						</視力檢查結果>
						<視力檢查結果>
							<視力檢查_部位>左</視力檢查_部位>
							<裸視視力>'.$dd[l][sight_o].'</裸視視力>
							<矯正視力>'.$dd[l][sight_r].'</矯正視力>
							<視力缺點>
';
				reset($sight_arr);
				foreach($sight_arr as $kkk=>$ddd) {
					if ($dd[l][$kkk]) {
$xml.='								<視力缺點_資料內容>
									<視力缺點_類別>'.$ddd.'</視力缺點_類別>
								</視力缺點_資料內容>
';
					}
				}
$xml.='							</視力缺點>
						</視力檢查結果>
					</視力>
				</學期檢查記錄>
';
			}
		}
		$status_arr=array("1"=>"正常","2"=>"異常");
$xml.='
			</經常性檢查>
			<新生立體感檢查>
				<立體感檢查結果>'.$status_arr[$health_data->stud_base[$sn][ntu]].'</立體感檢查結果>
				<立體感檢查說明></立體感檢查說明>
			</新生立體感檢查>
			<在學期間重大傷病事故>
				<在學期間重大傷病事故_資料內容>
					<在學期間重大傷病事故_日期></在學期間重大傷病事故_日期>
					<在學期間重大傷病事故_描述></在學期間重大傷病事故_描述>
				</在學期間重大傷病事故_資料內容>
			</在學期間重大傷病事故>
			<預防接種>
				<結核菌素測驗></結核菌素測驗>
				<預防接種_資料內容>
					<疫苗種類></疫苗種類>
					<接種劑次></接種劑次>
					<接種日期></接種日期>
				</預防接種_資料內容>
			</預防接種>
			<實驗室檢查>
';
		//處理實驗室檢查資料
		if (count($health_data->health_data[$sn]) > 0) {
			reset($health_data->health_data[$sn]);
			foreach($health_data->health_data[$sn] as $kk=>$dd) {
				if (count($dd[exp]) > 0) {
					foreach($dd[exp] as $kkk=>$ddd) {
						foreach($ddd as $kkkk=>$dddd) {
							foreach($dddd as $kkkkk=>$ddddd) {
								if ($exp_item_arr[$kkkkk] || $kkkkk=='status') {
$xml.='				<實驗室檢查_資料內容>
					<實驗室檢查_年級>'.(intval(substr($kk,0,3))-$health_data->stud_base[$sn][stud_study_year]+1).'</實驗室檢查_年級>
					<實驗室檢查_類別>'.iconv("BIG5","UTF-8",$exp_arr[$kkk]).'</實驗室檢查_類別>
					<實驗室檢查_項目>'.iconv("BIG5","UTF-8",$exp_item_arr[$kkkkk]).'</實驗室檢查_項目>
					<實驗室檢查_次別>'.$kkkk.'</實驗室檢查_次別>
					<實驗室檢查_數值>'.$ddddd.'</實驗室檢查_數值>
					<實驗室檢查_結果>'.iconv("BIG5","UTF-8",$hstatus_arr[$ddddd]).'</實驗室檢查_結果>
				</實驗室檢查_資料內容>
';
								}
							}
						}
					}
				}
			}
		}
$xml.='			</實驗室檢查>
			<全身健檢>
';
		//處理全身健檢資料
		if (count($health_data->health_data[$sn]) > 0) {
			reset($health_data->health_data[$sn]);
			foreach($health_data->health_data[$sn] as $kk=>$dd) {
				if (count($dd[checks]) > 0) {
$xml.='				<全身健檢_資料內容>
					<全身健檢_年級>'.(intval(substr($kk,0,3))-$health_data->stud_base[$sn][stud_study_year]+1).'</全身健檢_年級>
					<全身健檢_日期>'.$dd['checks']['Oph']['date'].'</全身健檢_日期>
					<全身健檢_承辦檢查醫院>'.iconv("BIG5","UTF-8",$dd['checks']['Oph']['hospital']).'</全身健檢_承辦檢查醫院>
					<全身健檢紀錄>
';
					foreach($dd[checks] as $kkk=>$ddd) {
$xml.='						<全身健檢紀錄_資料內容>
							<全身健檢部位>'.iconv("BIG5","UTF-8",$checks_part_arr[$kkk]).'</全身健檢部位>
							<全身健檢狀況>
';
						if ($dd['Dis'.$kkk]==0) {
$xml.='								<全身健檢狀況_資料內容>
									<全身健檢_結果>無異狀</全身健檢_結果>
									<全身健檢_結果附註></全身健檢_結果附註>
								</全身健檢狀況_資料內容>
';
						} else {
							foreach($ddd as $kkkk=>$dddd) {
								if ($kkkk!=0 && $dddd!=0) {
$xml.='								<全身健檢狀況_資料內容>
									<全身健檢_結果>'.iconv("BIG5","UTF-8",$checks_item_arr[$kkk][$kkkk]).'</全身健檢_結果>
									<全身健檢_結果附註>'.iconv("BIG5","UTF-8",$checks_diag_arr[$dddd]).'</全身健檢_結果附註>
								</全身健檢狀況_資料內容>
';
								}
							}
						}
$xml.='							</全身健檢狀況>
							<全身健檢_醫事人員>'.iconv("BIG5","UTF-8",$ddd['doctor']).'</全身健檢_醫事人員>
						</全身健檢紀錄_資料內容>
';
					}
$xml.='						<牙齒健康狀況>
';
					if ($dd[DisTeeth]!=0) {
						foreach($dd as $kkk=>$ddd) {
							if (substr($kkk,0,1)=="T") {
$xml.='							<牙齒健康狀況_資料內容>
								<牙齒位置代碼>'.substr($kkk,1,2).'</牙齒位置代碼>
								<牙齒檢查_結果>'.iconv("BIG5","UTF-8",$tee_chk_arr[$ddd]).'</牙齒檢查_結果>
							</牙齒健康狀況_資料內容>
';
							}
						}
					}
$xml.='						</牙齒健康狀況>
					</全身健檢紀錄>
					<全身健檢_總評建議></全身健檢_總評建議>
				</全身健檢_資料內容>
';
				}
			}
		}
$xml.='			</全身健檢>
			<臨時性檢查>
				<臨時性檢查_資料內容>
					<臨時性檢查_項目></臨時性檢查_項目>
					<臨時性檢查_日期></臨時性檢查_日期>
					<臨時性檢查_結果></臨時性檢查_結果>
					<臨時性檢查_檢查單位></臨時性檢查_檢查單位>
					<臨時性檢查_複查追蹤></臨時性檢查_複查追蹤>
				</臨時性檢查_資料內容>
			</臨時性檢查>
			<健康管理綜合記錄>
				<追蹤矯治>
					<追蹤矯治_資料內容>
						<追蹤矯治_科別項目></追蹤矯治_科別項目>
						<追蹤矯治_情形></追蹤矯治_情形>
					</追蹤矯治_資料內容>
				</追蹤矯治>
				<個案管理摘要>
					<個案管理摘要_資料內容>
						<記錄陳述></記錄陳述>
					</個案管理摘要_資料內容>
				</個案管理摘要>
			</健康管理綜合記錄>
		</健康資料>
	</學生資料>
';
	}
}
$xml.='</學生交換資料>';
?>