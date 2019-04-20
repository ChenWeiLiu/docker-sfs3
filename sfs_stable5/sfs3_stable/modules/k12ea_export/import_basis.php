<?php
	//開始XML資料更新
	$SQL="UPDATE stud_base SET ";  //stud_name='$stud_name',stud_sex=$stud_sex,stud_birthday='$stud_birthday'
	$SQL.="stud_country='$stud_country',stud_country_kind='$stud_country_kind'";
	$SQL.=",stud_person_id='$stud_person_id',stud_country_name='$stud_country_name'";
	$SQL.=",stud_addr_1='$stud_addr_1',stud_addr_2='$stud_addr_2'";
	$SQL.=",stud_tel_1='$stud_tel_1',stud_tel_2='$stud_tel_2',stud_tel_3='$stud_te1_3'";
	//註解部分原為中輟時地址
	//$SQL.=",stud_addr_a='$stud_addr_a',stud_addr_b='$stud_addr_b',stud_addr_c='$stud_addr_c',stud_addr_d='$stud_addr_d'";
	//$SQL.=",stud_addr_e='$stud_addr_e',stud_addr_f='$stud_addr_f',stud_addr_g='$stud_addr_g',stud_addr_h='$stud_addr_h'";
	//$SQL.=",stud_addr_i='$stud_addr_i',stud_addr_j='$stud_addr_j',stud_addr_k='$stud_addr_k',stud_addr_l='$stud_addr_l',stud_addr_m='$stud_addr_m'";
	$SQL.=",stud_class_kind='$stud_class_kind',stud_spe_kind='$stud_spe_kind',stud_spe_class_kind='$stud_spe_class_kind',stud_spe_class_id='$stud_spe_class_id'";
	$SQL.=",stud_preschool_status='$stud_preschool_status',stud_preschool_id='$stud_preschool_id',stud_preschool_name='$stud_preschool_name'";
	$SQL.=",stud_mschool_status='$stud_mschool_status',stud_mschool_id='$stud_mschool_id',stud_mschool_name='$stud_mschool_name'";
	$SQL.=" WHERE student_sn=$student_sn;";
	
	//$SQL=str_replace("'null'","NULL",$SQL);
	$SQL=str_replace("'null'","''",$SQL);
	//$SQL=str_replace(",null,",",NULL,",$SQL);
	$SQL=iconv("UTF-8","Big5//IGNORE",$SQL);
	$rs = $CONN->Execute($SQL) or user_error("ERROR WHILE EXCUING SQL! <br><br>$SQL",256) ;
	echo iconv("UTF-8","Big5//IGNORE","<BR>#◎ 匯入學生基本資料 ( stud_base ) OK ! ");
	if($ShowSQL) echo '<BR>'.$SQL;

	
	/* 這部分轉學不處理
	$graduate=$basis_data->畢修業核准文號;
	$WWW=$graduate->畢修業別;
	$XXX=$graduate->畢修業_日期;
	$YYY=$graduate->畢修業_字;
	$ZZZ=$graduate->畢修業_號;  */
	
	$fath_relation_array=fath_relation();
	$moth_relation_array=moth_relation();
	$guardian_relation_array=guardian_relation();
	$guardian_relation_array=guardian_relation();
	$edu_kind_array=edu_kind();
	$grad_kind_array=grad_kind();
	
	//以下部分紀錄在 stud_domicile 資料表
	$father=$basis_data->父親基本資料;
	$fath_name=$father->父親_姓名;
	$fath_birthyear=$father->父親_出生年次;
	$fath_alive=($father->父親_存=="歿")?"2":"1"; 
	$fath_relation=iconv('UTF-8','Big5',$father->與父關係);
		$fath_relation=array_search($fath_relation,$fath_relation_array);	
	$fath_p_id=$father->父親_身分證號;
	$fath_education=iconv('UTF-8','Big5',$father->父親_教育程度);
		$fath_education=array_search($fath_education,$edu_kind_array);
	$fath_grad_kind=iconv('UTF-8','Big5',$father->父親_畢修業別);
		$fath_grad_kind=array_search($fath_grad_kind,$grad_kind_array);
	$fath_occupation=$father->父親_職業;
	$fath_unit=$father->父親_服務單位;
	$fath_work_name=$father->父親_職稱;
	$fath_phone=$father->父親_電話號碼-公;	
	$fath_home_phone=$father->父親_電話號碼-宅;
	$fath_hand_phone=$father->父親_行動電話;
	$fath_email=$father->父親_電子郵件信箱;
	
	$mother=$basis_data->母親基本資料;
	$moth_name=$mother->母親_姓名;
	$moth_birthyear=$mother->母親_出生年次;
	$moth_alive=($mother->母親_存歿=="歿")?"2":"1";
	$moth_relation=iconv('UTF-8','Big5',$mother->與母關係);
		$moth_relation=array_search($moth_relation,$moth_relation_array);
	$moth_p_id=$mother->母親_身分證號;
	$moth_education=iconv('UTF-8','Big5',$mother->母親_教育程度);
		$moth_education=array_search($moth_education,$edu_kind_array);
	$moth_grad_kind=iconv('UTF-8','Big5',$mother->母親_畢修業別);
		$moth_grad_kind=array_search($moth_grad_kind,$grad_kind_array);
	$moth_occupation=$mother->母親_職業;
	$moth_unit=$mother->母親_服務單位;
	$moth_work_name=$mother->母親_職稱;
	$moth_phone=$mother->母親_電話號碼-公;
	$moth_home_phone=$mother->母親_電話號碼-宅;
	$moth_hand_phone=$mother->母親_行動電話;
	$moth_email=$mother->母親_電子郵件信箱;
	
	$grandfather=$basis_data->祖父基本資料;
	$grandfath_name=$father->祖父_姓名;
	$grandfath_alive=($father->祖父_存歿=="歿")?"2":"1";
		$grandmother=$basis_data->祖母基本資料;
	$grandmoth_name=$grandmother->祖母_姓名;
	$grandmoth_alive=($grandmother->祖母_存歿=="歿")?"2":"1";
	
	$guardian=$basis_data->監護人;
	$guardian_name=$basis_data->監護人_姓名;
	$guardian_phone=$guardian->監護人_連絡電話;
	$guardian_address=$guardian->監護人_地址;
	$guardian_relation=iconv('UTF-8','Big5',$guardian->與監護人之關係);
		$guardian_relation=array_search($guardian_relation,$guardian_relation_array);
	$guardian_p_id=$guardian->監護人_身分證號;
	$guardian_unit=$guardian->監護人_服務單位;
	$guardian_work_name=$guardian->監護人_職稱;
	$guardian_hand_phone=$guardian->監護人_行動電話;
	$guardian_email=$guardian->監護人_電子郵件信箱;
	
	
	//產生SQL加入stud_domicile資料表
	$SQL="REPLACE stud_domicile SET stud_id='$stud_id',student_sn=$student_sn";
	$SQL.=",fath_name='$fath_name',fath_birthyear='$fath_birthyear',fath_alive='$fath_alive',fath_relation='$fath_relation'";
	$SQL.=",fath_p_id='$fath_p_id',fath_education='$fath_education',fath_grad_kind='$fath_grad_kind',fath_occupation='$fath_occupation',fath_unit='$fath_unit',fath_work_name='$fath_work_name'";
	$SQL.=",fath_phone='$fath_phone',fath_home_phone='$fath_home_phone',fath_hand_phone='$fath_hand_phone',fath_email='$fath_email'";
	
	$SQL.=",moth_name='$moth_name',moth_birthyear='$moth_birthyear',moth_alive='$moth_alive',moth_relation='$moth_relation'";
	$SQL.=",moth_p_id='$moth_p_id',moth_education='$moth_education',moth_grad_kind='$moth_grad_kind',moth_occupation='$moth_occupation',moth_unit='$moth_unit',moth_work_name='$moth_work_name'";
	$SQL.=",moth_phone='$moth_phone',moth_home_phone='$moth_home_phone',moth_hand_phone='$moth_hand_phone',moth_email='$moth_email'";
	
	$SQL.=",grandfath_name='$grandfath_name',grandfath_alive=$grandfath_alive";
	$SQL.=",grandmoth_name='$grandmoth_name',grandmoth_alive=$grandmoth_alive";
	
	$SQL.=",guardian_name='$guardian_name',guardian_phone='$guardian_phone',guardian_address='$guardian_address',guardian_relation='$guardian_relation',guardian_p_id='$guardian_p_id'";
	$SQL.=",guardian_unit='$guardian_unit',guardian_work_name='$guardian_work_name',guardian_hand_phone='$guardian_hand_phone',guardian_email='$guardian_email'";

	//$SQL.=" WHERE ";
	
	//$SQL=str_replace("'null'","NULL",$SQL);
	$SQL=str_replace("'null'","''",$SQL);
	$SQL=iconv("UTF-8","Big5//IGNORE",$SQL);
	$rs = $CONN->Execute($SQL) or user_error("ERROR WHILE EXCUING SQL! <br><br>$SQL",256) ;
	echo iconv("UTF-8","Big5//IGNORE","<BR>#◎ 匯入學生戶口資料 ( stud_domicile ) OK ! ");
	if($ShowSQL) echo '<BR>'.$SQL;

	
	//以下紀錄在兄弟姊妹資料表stud_brother_sister
	$bs_calling_kind_array=bs_calling_kind();
	
	$bses=$basis_data->兄弟姊妹->兄弟姊妹_資料內容;
	$SQL="";
	foreach($bses as $bs){
		$bs_name=$bs->兄弟姊妹_姓名;
		$bs_calling=iconv("UTF-8","Big5//IGNORE",$bs->兄弟姊妹_稱謂);  //此處得進行代碼轉換
			$bs_calling=array_search($bs_calling,$bs_calling_kind_array);
		//$bs_gradu=$bs->兄弟姊妹_就讀學校; //此處3.0已刪除
		//產生sql檔所需資料
		if($bs_name<>"" AND strtoupper($bs_name)<>"NULL") $SQL.="('$stud_id','$bs_name','$bs_calling','$bs_gradu',$student_sn),";
	}
 
	$SQL=substr($SQL,0,-1);
	if($SQL){
		$SQL="INSERT INTO stud_brother_sister(stud_id,bs_name,bs_calling,bs_gradu,student_sn) VALUES ".$SQL;
		if(substr($SQL,-7)<>'VALUES ') {
			$SQL=iconv("UTF-8","Big5//IGNORE",$SQL);
			//先清空原有紀錄
			$rs = $CONN->Execute("DELETE FROM stud_brother_sister WHERE stud_id=$stud_id AND student_sn=$student_sn") or user_error("ERROR WHILE DELETING THE RECORDS OF TABLE stud_brother_sister ( STUD_ID:$stud_id )! <br><br>",256) ;
			$rs = $CONN->Execute($SQL) or user_error("ERROR WHILE EXCUING SQL! <br><br>$SQL",256) ;
			echo iconv("UTF-8","Big5//IGNORE","<BR>#◎ 匯入學生兄弟姊妹資料 ( stud_brother_sister ) OK ! ");
			if($ShowSQL) echo '<BR>'.$SQL;
		}
	}

	//以下紀錄在其他親屬資料表stud_kinfolk
	$kins=$basis_data->其他親屬->其他親屬_資料內容;
	$SQL="";
	foreach($kins as $kin){
		$kin_name=$kin->其他親屬_姓名;
		$kin_calling=$kin->其他親屬_稱謂;
		$kin_phone=$kin->其他親屬_聯絡電話;
		$kin_hand_phone=$kin->其他親屬_行動電話;
		$kin_email=$kin->其他親屬_電子郵件信箱;
		//產生sql檔所需資料
		if($kin_name<>"" AND strtoupper($kin_name)<>"NULL") $SQL.="('$stud_id','$kin_name','$kin_calling','$kin_phone','$kin_hand_phone','$kin_email',$student_sn),";
	}
	$SQL=substr($SQL,0,-1);
	if($SQL) {
		$SQL="INSERT INTO stud_kinfolk(stud_id,kin_name,kin_calling,kin_phone,kin_hand_phone,kin_email,student_sn) VALUES ".$SQL;
		if(substr($SQL,-7)<>'VALUES ') {
			$SQL=iconv("UTF-8","Big5//IGNORE",$SQL);
			//先清空原有紀錄
			$rs = $CONN->Execute("DELETE FROM stud_kinfolk WHERE stud_id=$stud_id AND student_sn=$student_sn") or user_error("ERROR WHILE DELETING THE RECORDS OF TABLE stud_kinfolk ( STUD_ID:$stud_id )! <br><br>",256) ;
			$rs = $CONN->Execute($SQL) or user_error("ERROR WHILE EXCUING SQL! <br><br>$SQL",256) ;
			echo iconv("UTF-8","Big5//IGNORE","<BR>#◎ 匯入其他親屬資料 ( stud_kinfolk ) OK ! ");
			if($ShowSQL) echo '<BR>'.$SQL;
		}
	}

	//以下紀錄在輔導個案資料表 stud_eduh   3.0標準已經移除
	/*
	$eduhs=$basis_data->輔導個案資料;
	$SQL="";
	foreach($eduhs as $eduh){
		$eh_from=$eduh->個案來源;
		$eh_from_date=$eduh->接案日期;
		$eh_teacher=$eduh->認輔老師;
		$eh_caes=$eduh->問題類別;
		$eh_meth=$eduh->偏差行為;
		$eh_resion_memo=$eduh->適應不良原因;
		$eh_is_over=$eduh->結案否;
		$eh_over_memo=$eduh->結案原因;
		$eh_over_date=$eduh->結案日期;
		//xml是採階層式的與 sfs3 不同  用迴圈紀錄到text欄位  匯出的話要重新解析
		$eduh_records=$eduh->輔導個案紀錄->輔導個案紀錄_資料內容;
		$eh_case_memo="";
		foreach($eduh_records as $eduh_record) {
			$eh_case_date=$eduh_record->輔導日期;
			$eh_case_memo.="#".$eduh_record->輔導日期."\r\n".$eduh_record->輔導內容."\r\n\r\n";
		}
		$eh_case_relation=$eduh->個案關聯編號;
		//產生sql檔所需資料
		if($eh_from<>"" AND strtoupper($eh_from)<>"NULL") $SQL.="('$stud_id','$eh_from','$eh_from_date','$eh_teacher','$eh_caes','$eh_meth','$eh_resion_memo','$eh_is_over','$eh_over_memo','$eh_over_date','$eh_case_date','$eh_case_memo','$eh_case_relation'),";
	}
	
	$SQL=substr($SQL,0,-1);
	$SQL="INSERT INTO stud_eduh(stud_id,eh_from,eh_from_date,eh_teacher,eh_caes,eh_meth,eh_resion_memo,eh_is_over,eh_over_memo,eh_over_date,eh_case_date,eh_case_memo,eh_case_relation) VALUES ".$SQL;
	if(substr($SQL,-7)<>'VALUES ') {
		$SQL=iconv("UTF-8","Big5//IGNORE",$SQL);
		//先清空原有紀錄
		$rs = $CONN->Execute("DELETE FROM stud_eduh WHERE stud_id=$stud_id") or user_error("ERROR WHILE DELETING THE RECORDS OF TABLE stud_eduh ( STUD_ID:$stud_id )! <br><br>",256) ;
		$rs = $CONN->Execute($SQL) or user_error("ERROR WHILE EXCUING SQL! <br><br>$SQL",256) ;
		echo iconv("UTF-8","Big5//IGNORE","<BR>#◎ 匯入輔導個案資料 ( stud_eduh ) OK ! ");
		if($ShowSQL) echo '<BR>'.$SQL;
	}
	*/
?>
