<?php
	$seme_datas=$student->學期資料->個別學期資料;
	//print_R($seme_datas);
	//echo iconv("UTF-8","Big5//IGNORE","<BR><BR>===== 學期資料表更新<BR><BR>");
	
//echo "<PRE>";
//print_r($seme_datas);
//echo "</PRE>";
//exit;	

	foreach($seme_datas as $seme_data){
		$seme_year_seme=sprintf("%03d%d",$seme_data->學年別,$seme_data->學期別);
		$current_year=$seme_data->學年別;
		$current_semester=$seme_data->學期別;
		$seme_class_grade=$seme_data->班級座號->年級;
		$seme_class_name=$seme_data->班級座號->班級;
		$seme_num=$seme_data->班級座號->座號;
		$teacher_name=$seme_data->學期成績->導師姓名;


		//以下資料記錄在班級就讀紀錄 stud_seme_import
		$SQL="REPLACE stud_seme_import set seme_year_seme='$seme_year_seme',stud_id='$stud_id',seme_class_grade='$seme_class_grade',seme_class_name='$seme_class_name',teacher_name='$teacher_name'";
		$SQL.=",seme_num=$seme_num,student_sn=$student_sn;";
		$SQL=str_replace("'null'","''",$SQL);
		$SQL=iconv("UTF-8","Big5//IGNORE",$SQL);
		if($SQL) $rs = $CONN->Execute($SQL) or user_error("ERROR WHILE EXCUING SQL! <br><br>$SQL",256) ;
		echo iconv("UTF-8","Big5//IGNORE","<BR>#◎ 匯入[$seme_year_seme]學期班級就讀資料 ( stud_seme_import ) OK ! ");
		if($ShowSQL) echo '<BR>'.$SQL;
	

		//以下資料記錄在領域學期成績資料表 stud_seme_final_score
		
		//系統將檢查並創建新的資料表
		$SQL="CREATE TABLE IF NOT EXISTS `stud_seme_final_score` (
			`student_sn` smallint( 6  ) NOT  NULL default '0',
			`seme_year_seme` varchar( 5 ) NOT NULL default '',
			`area` varchar(40) NOT NULL default  '',
			`score` tinyint(4) NULL ,
			`description` varchar(120)  NULL,
			`comment` varchar(20)  NULL ,
			PRIMARY KEY (`student_sn`,`seme_year_seme`,`area`))";
		$rs = $CONN->Execute($SQL) or user_error("ERROR WHILE CREATEING TABLE.....<br><br>$SQL",256);
		
		$seme_score=$seme_data->學期成績;
		//---------------------------------------------
		$topics["1"]="語文_學習領域";
		$topics["2"]="數學_學習領域";
		$topics["3"]="自然與生活科技_學習領域";
		$topics["4"]="社會_學習領域";
		$topics["5"]="健康與體育_學習領域";
		$topics["6"]="藝術與人文_學習領域";
		$topics["7"]="生活課程_學習領域";
		$topics["8"]="綜合活動_學習領域";
		
		$topics["9"]="本國語文";
		$topics["10"]="本土語文";
		$topics["11"]="英語";
		//---------------------------------------------
		$ss_links["1"]="";
		$ss_links["2"]="數學";
		$ss_links["3"]="自然與生活科技";
		$ss_links["4"]="社會";
		$ss_links["5"]="健康與體育";
		$ss_links["6"]="藝術與人文";
		$ss_links["7"]="生活";
		$ss_links["8"]="綜合活動";
		
		$ss_links["9"]="語文-本國語文";
		$ss_links["10"]="語文-鄉土語文";
		$ss_links["11"]="語文-英語";
		//---------------------------------------------
				/*
         * 把 語文_學習領域文字描述 , 利用 explode 切割分配給本國語文、本土語文、英語  2015.11.09 by smallduh
         */
        $lang_all=explode(';',$seme_score->語文_學習領域文字描述,3);
        
		$SQL="";
		$SQL2="";
		$SQL3="";
		foreach($topics as $key=>$topic){
			$content_score="$topic"."百分制成績";
			$content_description="$topic"."文字描述";
			
			$area_score=$seme_score->$content_score;
			$area_description=$seme_score->$content_description;
			   	/*
         	 * 把 本國語文、本土語文、英語 的文字描述, 用切割後的 語文_學習領域文字 取代 2015.11.09 by smallduh
        	 */
			     switch ($topic) {
                case '本國語文':
                    $area_description=$lang_all[0];
                    break;
                case '本土語文':
                    $area_description=$lang_all[1];
                    break;
                case '英語':
                    $area_description=$lang_all[2];
                    break;
           }
           
			if($topic=="本土語文")
			{
				$area_comment=",comment='$seme_score->本土語言類別'";
			} else {
				$area_comment="";
			}			
			$SQL.="REPLACE stud_seme_final_score SET seme_year_seme='$seme_year_seme',student_sn=$student_sn,area='$topic',score='$area_score',description='$area_description'$area_comment;<BR>";
			
			//取得年級與科目代號
			if($key>1){
				$link_ss=$ss_links[$key];
				$link_ss=iconv("UTF-8","Big5//IGNORE",$link_ss);
				$SQL_SSID="SELECT * FROM score_ss WHERE year='$current_year' AND semester='$current_semester' AND class_year='$seme_class_grade' AND enable=1 AND need_exam=1 AND class_id='' AND link_ss='$link_ss';";
				
				//echo '<BR>'.$SQL_SSID;
				
				$recordSet_SSID=$CONN->Execute($SQL_SSID) or user_error("取得年級與科目代號失敗！<br>$SQL_SSID",256);
				while ($data_SSID=$recordSet_SSID->FetchRow()) {
					$ss_id=$data_SSID['ss_id'];
					//seme_year_seme  student_sn  ss_id  ss_score  ss_score_memo  
					$SQL2.="REPLACE stud_seme_score SET seme_year_seme='$seme_year_seme',student_sn=$student_sn,ss_id=$ss_id,ss_score='$area_score',ss_score_memo='$area_description';<BR>";
					$SQL3.='#'.$link_ss.'<BR>'."REPLACE stud_seme_score SET seme_year_seme='$seme_year_seme',student_sn=$student_sn,ss_id=$ss_id,ss_score='$area_score',ss_score_memo='$area_description';<BR>";
				}
			}
		}
		
		//處理彈性課程
		$other_scores=$seme_score->彈性時數->彈性時數_分項科目;
		foreach($other_scores as $key=>$other_score){
			$SQL.="REPLACE stud_seme_final_score SET seme_year_seme='$seme_year_seme',student_sn=$student_sn,area='$other_score->彈性時數_科目名稱',score='$other_score->彈性時數_科目百分制成績',description='$other_score->彈性時數_科目文字描述',comment='彈性時數';<BR>";
		}
		
		//寫入stud_seme_final_score
		$SQL=iconv("UTF-8","Big5//IGNORE",$SQL);
		$SQL=str_replace("'null'","''",$SQL);	
		$SQL_Arr=explode("<BR>",$SQL);		
		foreach($SQL_Arr as $SQL_S){			
			if($SQL_S<>"") $rs=$CONN->Execute($SQL_S) or user_error("ERROR WHILE EXCUING SQL! <br><br>$SQL_S",256) ;
		}
		
		//寫入真正的stud_seme_score
		$SQL2=iconv("UTF-8","Big5//IGNORE",$SQL2);
		$SQL2=str_replace("'null'","''",$SQL2);
		$SQL2_Arr=explode("<BR>",$SQL2);
		foreach($SQL2_Arr as $SQL2_S){			
			if($SQL2_S<>"") $rs=$CONN->Execute($SQL2_S) or user_error("ERROR WHILE EXCUING SQL! <br><br>$SQL2_S",256) ;
		}
		
		echo iconv("UTF-8","Big5//IGNORE","<BR>#◎ 匯入[$seme_year_seme]領域學期成績 (stud_seme_final_score、stud_seme_score ) OK ! ");
		if($ShowSQL) echo '<BR>'.$SQL;
		if($ShowSQL) echo "<font color='blue'>".iconv("UTF-8","Big5//IGNORE",$SQL3)."</font>";

		
		//以下寫在日常生活表現成績表  stud_seme_score_nor
		$nor_score=$seme_data->日常生活表現;
		$SQL="REPLACE stud_seme_score_nor SET seme_year_seme='$seme_year_seme',student_sn=$student_sn,ss_id=0,ss_score_memo='$nor_score->日常生活表現_文字描述';";
		$SQL=iconv("UTF-8","Big5//IGNORE",$SQL);
		$SQL=str_replace("'null'","''",$SQL);
		if($SQL) $rs = $CONN->Execute($SQL) or user_error("ERROR WHILE EXCUING SQL! <br><br>$SQL",256) ;
		echo iconv("UTF-8","Big5//IGNORE","<BR>#◎ 匯入[$seme_year_seme]日常生活表現 ( stud_seme_score_nor ) OK ! ");
		if($ShowSQL) echo '<BR>'.$SQL;
	
		//以下寫在學期出席紀錄表  stud_seme_abs
		$SQL="REPLACE stud_seme_abs SET seme_year_seme='$seme_year_seme',stud_id='$stud_id',abs_kind=1,abs_days=$nor_score->學期出缺席_事假數;<BR>";
		$SQL.="REPLACE stud_seme_abs SET seme_year_seme='$seme_year_seme',stud_id='$stud_id',abs_kind=2,abs_days=$nor_score->學期出缺席_病假數;<BR>";
		$SQL.="REPLACE stud_seme_abs SET seme_year_seme='$seme_year_seme',stud_id='$stud_id',abs_kind=3,abs_days=$nor_score->學期出缺席_曠課數;<BR>";
		$SQL.="REPLACE stud_seme_abs SET seme_year_seme='$seme_year_seme',stud_id='$stud_id',abs_kind=6,abs_days=$nor_score->學期出缺席_其他假數;<BR>";
		// SFS定義  "4"=>"集會","5"=>"公假"  xml中未定義
		//XML中 學期出缺席_應出席日數 & 學期出缺席_單位 尚未處理
		
		$SQL=iconv("UTF-8","Big5//IGNORE",$SQL);
		$SQL=str_replace("'null'","''",$SQL);
		$SQL_Arr=explode("<BR>",$SQL);		
		foreach($SQL_Arr as $SQL_S){
			if($SQL_S) $rs = $CONN->Execute($SQL_S) or user_error("ERROR WHILE EXCUING SQL! <br><br>$SQL_S",256) ;
		}
		echo iconv("UTF-8","Big5//IGNORE","<BR>#◎ 匯入[$seme_year_seme]學期出席紀錄 ( stud_seme_abs ) OK ! ");
		if($ShowSQL) echo '<BR>'.$SQL;
		
		//以下寫在特殊優良表現資料表 stud_seme_spe
		$spe_scores=$seme_data->特殊優良表現->優良表現事蹟;
		$SQL="";
		foreach($spe_scores as $spe_score){
			if($spe_score->優良表現_事由<>"" AND $spe_score->優良表現_事由<>"null")
				$SQL.="('$seme_year_seme','$stud_id','$spe_score->優良表現_日期','$spe_score->優良表現_事由'),";
		}
		$SQL=substr($SQL,0,-1);		
		$SQL="INSERT INTO stud_seme_spe(seme_year_seme,stud_id,sp_date,sp_memo) VALUES ".$SQL;
		if(substr($SQL,-7)<>'VALUES ') {
			$SQL=iconv("UTF-8","Big5//IGNORE",$SQL);
			$SQL=str_replace("'null'","''",$SQL);
			//先清空原有紀錄
			$rs = $CONN->Execute("DELETE FROM stud_seme_spe WHERE stud_id=$stud_id AND seme_year_seme='$seme_year_seme'") or user_error("ERROR WHILE DELETING THE RECORDS OF TABLE stud_seme_spe ( STUD_ID:$stud_id )! <br><br>",256) ;
			//執行新增
			$rs = $CONN->Execute($SQL) or user_error("ERROR WHILE EXCUING SQL! <br><br>$SQL",256) ;
			echo iconv("UTF-8","Big5//IGNORE","<BR>#◎ 匯入[$seme_year_seme]特殊優良表現資料 ( stud_seme_spe ) OK ! ");
			if($ShowSQL) echo '<BR>'.$SQL;
		}

		//以下寫在 心理測驗 資料表 stud_psy_test

		$test_scores=$seme_data->心理測驗->心理測驗_資料內容;
		$SQL="";
		foreach($test_scores as $test_score){
			$item=$test_score->心理測驗_名稱;
			if($item<>"" AND $item<>"null") {				
				$score=$test_score->心理測驗_原始分數;
				$model=$test_score->心理測驗_常模樣本;
				$standard=$test_score->心理測驗_標準分數;
				$pr=$test_score->心理測驗_百分等級;
				$explanation=$test_score->心理測驗_解釋;
				$SQL.="('$current_year','$current_semester','$student_sn','$item','$score','$model','$standard','$pr','$explanation'),";
			}
		}
		
		$SQL=substr($SQL,0,-1);
		$SQL="INSERT INTO stud_psy_test(year,semester,student_sn,item,score,model,standard,pr,explanation) VALUES ".$SQL;
		if(substr($SQL,-7)<>'VALUES ') {
			$SQL=iconv("UTF-8","Big5//IGNORE",$SQL);
			$SQL=str_replace("'null'","''",$SQL);
			//先清空原有紀錄
			$rs = $CONN->Execute("DELETE FROM stud_psy_test WHERE student_sn=$student_sn AND year='$current_year' AND semester='$current_semester'") or user_error("刪除心理測驗記錄失敗 ( STUDENT_SN=$student_sn )! <br><br>$SQL",256) ;
			//進行新增
			$rs = $CONN->Execute($SQL) or user_error("新增心理測驗記錄失敗! <br><br>$SQL",256) ;
			echo iconv("UTF-8","Big5//IGNORE","<BR>#◎ 匯入[$seme_year_seme]心理測驗資料 ( stud_psy_test ) OK ! ");
			if($ShowSQL) echo '<BR>'.$SQL;
		}
	
		//以下寫在學期輔導基本資料 stud_seme_eduh
		$eduh_record=$seme_data->輔導紀錄;
		$sse_relation=array_search(iconv("UTF-8","Big5//IGNORE",$eduh_record->父母關係),$sse_relation_arr);
		//sse_family_kind ??? 家庭類型?
		$sse_family_air=array_search(iconv("UTF-8","Big5//IGNORE",$eduh_record->家庭氣氛),$sse_family_air_arr);
		$sse_farther=array_search(iconv("UTF-8","Big5//IGNORE",$eduh_record->父管教方式),$sse_teach_arr);
		$sse_mother=array_search(iconv("UTF-8","Big5//IGNORE",$eduh_record->母管教方式),$sse_teach_arr);
		$sse_live_state=array_search(iconv("UTF-8","Big5//IGNORE",$eduh_record->居住情形),$sse_live_state_arr);
		$sse_rich_state=array_search(iconv("UTF-8","Big5//IGNORE",$eduh_record->經濟狀況),$sse_rich_state_arr);
		
		$SQL="'$seme_year_seme','$stud_id','$sse_relation','$sse_family_air','$sse_farther','$sse_mother','$sse_live_state','$sse_rich_state',";

		$topics["1"]="最喜愛學習領域";
		$topics["2"]="最困難學習領域";
		$topics["3"]="特殊才能";
		$topics["4"]="興趣";
		$topics["5"]="生活習慣";
		$topics["6"]="人際關係";
		$topics["7"]="外向行為";
		$topics["8"]="內向行為";
		$topics["9"]="學習行為";
		$topics["10"]="不良習慣";
		$topics["11"]="焦慮行為";

		foreach($topics as $key=>$topic){
			$content="$topic"."_資料內容";
			$items="";
			$contents=$eduh_record->$topic->$content;
			foreach($contents as $item){  //將選項以,串聯
				if($item<>"" AND strtoupper($item)<>"NULL") {
					$item=iconv("UTF-8","Big5//IGNORE",$item);
					$item=array_search($item,${"sse_arr_$key"});
					$items.=",$item";
				}
			}
			//$items=substr($items,0,-1);
			$items.=',';
			$SQL.="'$items',";
		}
		$SQL=substr($SQL,0,-1);
		$SQL=str_replace("'null'","''",$SQL);
		
		$SQL="REPLACE INTO stud_seme_eduh(seme_year_seme,stud_id,sse_relation,sse_family_air,sse_farther,sse_mother,sse_live_state,sse_rich_state,sse_s1,sse_s2,sse_s3,sse_s4,sse_s5,sse_s6,sse_s7,sse_s8,sse_s9,sse_s10,sse_s11) VALUES ($SQL)";
		$SQL=iconv("UTF-8","Big5//IGNORE",$SQL);
		//seme_year_seme  stud_id  sse_relation  sse_family_kind            sse_s1  sse_s2  sse_s3  sse_s4  sse_s5  sse_s6  sse_s7  sse_s8  sse_s9  sse_s10  sse_s11  
		if($SQL) $rs = $CONN->Execute($SQL) or user_error("ERROR WHILE EXCUING SQL! <br><br>$SQL",256) ;
		echo iconv("UTF-8","Big5//IGNORE","<BR>#◎ 匯入[$seme_year_seme]學期輔導基本資料 ( stud_seme_eduh ) OK ! ");
		if($ShowSQL) echo '<BR>'.$SQL;
	
		//以下寫在 輔導訪談紀錄 資料表 stud_seme_talk

		$talks=$seme_data->輔導訪談紀錄->輔導訪談紀錄_資料內容;
		
		$SQL="";
		foreach($talks as $talk){
			$sst_memo=$talk->內容要點;
			if($sst_memo<>"" AND $sst_memo<>"null") {
				$sst_date=$talk->紀錄日期;
				$sst_name=$talk->連絡對象;
				$sst_main=$talk->連絡事項;
			
				$SQL.="('$seme_year_seme','$stud_id','$sst_date','$sst_name','$sst_main','$sst_memo'),";
			}
		}
		$SQL=substr($SQL,0,-1);
		$SQL="REPLACE INTO stud_seme_talk(seme_year_seme,stud_id,sst_date,sst_name,sst_main,sst_memo) VALUES $SQL";
		if(substr($SQL,-7)<>'VALUES ') {
			$SQL=iconv("UTF-8","Big5//IGNORE",$SQL);
			$SQL=str_replace("'null'","''",$SQL);
			//先清空原有紀錄
			$rs = $CONN->Execute("DELETE FROM stud_seme_talk WHERE stud_id=$stud_id AND seme_year_seme='$seme_year_seme'") or user_error("ERROR WHILE DELETING THE RECORDS OF TABLE stud_seme_talk ( STUDENT_SN:$student_sn )! <br><br>",256) ;
			if($SQL) $rs = $CONN->Execute($SQL) or user_error("ERROR WHILE EXCUING SQL! <br><br>$SQL",256) ;
			echo iconv("UTF-8","Big5//IGNORE","<BR>#◎ 匯入[$seme_year_seme]輔導訪談紀錄 ( stud_seme_talk ) OK ! ");
			if($ShowSQL) echo '<BR>'.$SQL;
		}
	}
?>
