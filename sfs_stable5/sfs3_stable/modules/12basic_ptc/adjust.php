<?php

include "config.php";

sfs_check();

//秀出網頁
head("學生通訊資料回校");
print_menu($menu_p);

//學年別
$curr_year_seme=sprintf("%03d%d",curr_year(),curr_seme());
//考區代碼	集報單位代碼	序號	學號	班級	座號	學生姓名	身分證號	性別	出生年	出生月	出生日	畢業學校代碼	畢業年	畢肄業	學生身分	身心障礙	就學區	低收入戶	中低收入戶	失業勞工子女	資料授權	家長姓名	市內電話	行動電話	郵遞區號	通訊地址	寫作資料授權
//134517	103	1	0	1	12	0	0	0	0	陳00	7891234	0912345678	920	屏東縣潮州鎮中山里中山路1號	0

if($_POST['act']=='確定回校' && $_POST['data']){
	$data=explode("\r\n",$_POST['data']);
	foreach($data as $key=>$value){
		$single=explode("\t",$value);
		/*
		Array
		(
			[0] => 考區代碼
			[1] => 集報單位代碼
			[2] => 序號
			[3] => 學號
			[4] => 班級
			[5] => 座號
			[6] => 學生姓名
			[7] => 身分證號
			[8] => 性別
			[9] => 出生年
			[10] => 出生月
			[11] => 出生日
			[12] => 畢業學校代碼
			[13] => 畢業年
			[14] => 畢肄業
			[15] => 學生身分
			[16] => 身心障礙
			[17] => 就學區
			[18] => 低收入戶
			[19] => 中低收入戶
			[20] => 失業勞工子女
			[21] => 資料授權
			[22] => 家長姓名
			[23] => 市內電話
			[24] => 行動電話
			[25] => 郵遞區號
			[26] => 通訊地址
			[27] => 寫作資料授權
		)
		*/
		//抓取student_sn
		$stud_id=$single[3];
		if($single[2] && $stud_id) {
			$seme_class=sprintf("%d%02d",9,$single[4]);
			//$res=$CONN->Execute("SELECT student_sn FROM stud_seme WHERE seme_year_seme='$curr_year_seme' AND stud_id='$stud_id' AND seme_class='$seme_class' AND seme_num='$seme_num'") or user_error("讀取失敗！<br>$sql",256);
			$res=$CONN->Execute("SELECT student_sn FROM stud_base WHERE stud_person_id='{$single[7]}' AND stud_id='$stud_id'") or user_error("讀取失敗！<br>$sql",256);
			$student_sn=$res->fields['student_sn'];
			if($student_sn){
				//更新基本資料
				$birth_year=$single[9]+1911;
				$birthday=sprintf("%d-%02d-%02d",$birth_year,$single[10],$single[11]);
				$CONN->Execute("UPDATE stud_base SET stud_birthday='$birthday',stud_tel_2='{$single[23]}',stud_tel_3='{$single[24]}',addr_zip='{$single[25]}',stud_addr_2='{$single[26]}' WHERE student_sn={$student_sn}") or user_error("讀取失敗！<br>$sql",256);
				//更新監護人姓名
				$CONN->Execute("UPDATE stud_domicile SET guardian_name='{$single[22]}' WHERE student_sn={$student_sn}") or user_error("讀取失敗！<br>$sql",256);
				$counter++;
			} else echo "<br>班級：{$seme_class} 學號：{$stud_id} 姓名：{$single[6]} 因為找不到學生基本資料，無法更新！";
			/*
			echo "<pre>";
			print_r($single);
			echo "</pre>";
			*/
		}
	}
	echo "共更新了 $counter 位學生的資料！<br>>";
};

//橫向選單標籤
$linkstr="work_year_seme=$work_year_seme&stud_class=$stud_class";
echo print_menu($MENU_P,$linkstr);
echo "<form name='myform' method='post' action='$_SERVER[PHP_SELF]'>";
echo "<br>※更新的學年度：".curr_year();
echo "<br>※貼上的資料：教育會考轉出檔所有欄位資料，無須包含第一列(標題列)。";
echo "<br>※更新回寫比對依據：學號、身分證號";
echo "<br>※會更新回寫的欄位：出生年、出生月、出生日、家長姓名、市內電話、行動電話、郵遞區號、通訊地址";
echo "<br>※快貼資料：<br><textarea rows=33 name='data' cols=200></textarea>";
echo "<br><input type='submit' name='act' value='確定回校' onclick='return confirm(\"確定要回寫學生基本資料？\")'>";
echo "</form>";
foot();
?>