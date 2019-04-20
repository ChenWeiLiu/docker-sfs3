<?php
	$absent_datas=$student->期中資料->期中缺席;

	//以下紀錄期中缺席日數  總缺席紀錄部分修正XML時   建議應可省略  需要統計數據  自分月資料統計即可
	//假別 "1"=>"事假","2"=>"病假","3"=>"曠課","4"=>"集會","5"=>"公假","6"=>"其他"
	$total_leave=$absent_datas->期中總缺席_事假數;
	$total_ill=$absent_datas->期中總缺席_病假數;
	$total_truancy=$absent_datas->期中總缺席_曠課數;
	$total_other=$absent_datas->期中總缺席_其他假數;
	$absent_unit=$absent_datas->期中總缺席_單位;
	
	//以下紀錄在期中缺席紀錄  stud_absent_move  ;  此資料表在系統 up20080810.php 更新  會被建立
	//日後統計學期缺席時，除日常紀錄表外，還得到此表抓取轉學的期中缺席紀錄
	$curr_year_seme=sprintf('%03d%d',curr_year(),curr_seme());
	//先清除舊資料  以免重覆紀錄
	$SQL="DELETE FROM stud_absent_move WHERE student_sn=$student_sn AND seme_year_seme='$curr_year_seme'";
	$rs=$CONN->Execute($SQL) or user_error("ERROR WHILE EXCUING SQL! <br><br>$SQL",256);
	
	//抓取分月資料
	$absent_months=$absent_datas->期中缺席_資料內容->期中缺席_分月資料;
	
	if(count($absent_months)>0)
	{
		$SQL='';
		foreach($absent_months as $absent_month)
		{
			$sma_year=$absent_month->期中缺席_年;
			$sma_month=$absent_month->期中缺席_月;
			$sma_leave=$absent_month->期中缺席_事假數;
			$sma_ill=$absent_month->期中缺席_病假數;
			$sma_truancy=$absent_month->期中缺席_曠課數;
			$sma_other=$absent_month->期中缺席_其他假數;
			
			$SQL.="('$curr_year_seme','$sma_year','$sma_month','$stud_id',1,$sma_leave,$student_sn),";
			$SQL.="('$curr_year_seme','$sma_year','$sma_month','$stud_id',2,$sma_ill,$student_sn),";
			$SQL.="('$curr_year_seme','$sma_year','$sma_month','$stud_id',3,$sma_truancy,$student_sn),";
			$SQL.="('$curr_year_seme','$sma_year','$sma_month','$stud_id',6,$sma_other,$student_sn),";
		}
		$SQL=substr($SQL,0,-1);
		$SQL="INSERT INTO stud_absent_move(seme_year_seme,year,month,stud_id,abs_kind,abs_days,student_sn) VALUES ".$SQL;
		//都是英數字元 免轉換  $SQL=iconv("UTF-8","Big5//IGNORE",$SQL);
		//$SQL=str_replace("'null'","''",$SQL);
		$rs = $CONN->Execute($SQL) or user_error("ERROR WHILE EXCUING SQL! <br><br>$SQL",256);
		echo iconv("UTF-8","Big5//IGNORE","<BR>#◎ 匯入[$seme_year_seme]期中缺席紀錄 ( stud_absent_move ) OK ! ");
		if($ShowSQL) echo '<BR>'.$SQL;		
	} else echo iconv("UTF-8","Big5//IGNORE","<BR>#◎ 匯入[$seme_year_seme]期中缺席紀錄 ( stud_absent_move ) ~~無期中缺席紀錄! <BR>");

	//===========================================================================================================================================	
	//以下為期中成績
	//$scores=array();

	//先將資料轉成陣列
	$inner_scores=$student->期中資料->期中成績->期中成績_語文->期中成績_語文資料內容;
	foreach($inner_scores as $area_content)
	{
		$section=intval($area_content->期中成績_語文領域段考別);
		$scores[$section]['language']['chinese']=intval($area_content->期中成績_本國語文百分制成績);
		$scores[$section]['language']['local']=intval($area_content->期中成績_鄉土語文百分制成績);
		$scores[$section]['language']['english']=intval($area_content->期中成績_英語百分制成績);
	}
	

	$inner_scores=$student->期中資料->期中成績->期中成績_數學->期中成績_數學資料內容;
	foreach($inner_scores as $area_content)
	{
		$section=intval($area_content->期中成績_數學領域段考別);
		$scores[$section]['math']=intval($area_content->期中成績_數學領域百分制成績);
	}
		
	$inner_scores=$student->期中資料->期中成績->期中成績_自然與生活科技->期中成績_自然與生活科技資料內容;
	foreach($inner_scores as $area_content)
	{
		$section=intval($area_content->期中成績_自然與生活科技領域段考別);
		$scores[$section]['nature']=intval($area_content->期中成績_自然與生活科技領域百分制成績);
	}
	
	$inner_scores=$student->期中資料->期中成績->期中成績_社會->期中成績_社會資料內容;
	foreach($inner_scores as $area_content)
	{
		$section=intval($area_content->期中成績_社會領域段考別);
		$scores[$section]['social']=intval($area_content->期中成績_社會領域百分制成績);
	}
	
	$inner_scores=$student->期中資料->期中成績->期中成績_健康與體育->期中成績_健康與體育資料內容;
	foreach($inner_scores as $area_content)
	{
		$section=intval($area_content->期中成績_健康與體育領域段考別);
		$scores[$section]['health']=intval($area_content->期中成績_健康與體育領域百分制成績);
	}
	
	$inner_scores=$student->期中資料->期中成績->期中成績_藝術與人文->期中成績_藝術與人文資料內容;
	foreach($inner_scores as $area_content)
	{
		$section=intval($area_content->期中成績_藝術與人文領域段考別);
		$scores[$section]['art']=intval($area_content->期中成績_藝術與人文領域百分制成績);
	}
	
	$inner_scores=$student->期中資料->期中成績->期中成績_生活課程->期中成績_生活課程資料內容;
	foreach($inner_scores as $area_content)
	{
		$section=intval($area_content->期中成績_生活課程領域段考別);
		$scores[$section]['life']=intval($area_content->期中成績_生活課程領域百分制成績);
	}
	
	
	$inner_scores=$student->期中資料->期中成績->期中成績_綜合活動->期中成績_綜合活動資料內容;
	foreach($inner_scores as $area_content)
	{
		$section=intval($area_content->期中成績_綜合活動領域段考別);
		$scores[$section]['complex']=intval($area_content->期中成績_綜合活動領域百分制成績);
	}
	
	$inner_scores=$student->期中資料->期中成績->期中成績_彈性分項科目->期中成績_彈性時數;
	$item_sort=0;
	foreach($inner_scores as $area_content)
	{
		$item_sort++;
		$elasticity_scores[$item_sort]['name']=$area_content->期中成績_彈性時數科目名稱.'';
		$elasticity_scores[$item_sort]['score']=intval($area_content->期中成績_彈性時數科目百分制成績);
	}
	/*
		echo "<PRE>";
		print_r($scores);
		print_r($elasticity_scores);
		echo "</PRE>";
	*/	
	//顯示表格
	$showdata="<BR><BR>※轉學之期中成績(本表顯示資料並未記錄於系統中，請自行複製存檔參考！)<table border='2' cellpadding='5' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111'>
	<tr bgcolor='#FFCCCC'>
		<td rowspan='2' align='center'>階段別</td>
		<td colspan='3' align='center'>語文領域</td>
		<td align='center' rowspan='2'>數學</td>
		<td align='center' rowspan='2'>自然與生活科技</td>
		<td align='center' rowspan='2'>社會</td>
		<td align='center' rowspan='2'>健康與體育</td>
		<td align='center' rowspan='2'>藝術與人文</td>
		<td align='center' rowspan='2'>生活課程</td>
		<td align='center' rowspan='2'>綜合活動</td>
	</tr>
	<tr bgcolor='#FFCCCC'>
		<td align='center'>本國語文</td>
		<td align='center'>鄉土語言</td>
		<td align='center'>英語</td>
	</tr>";
	foreach($scores as $section_key=>$section_score)
	{
		$chinese=$scores[$section_key]['language']['chinese'];
		$local=$scores[$section_key]['language']['local'];
		$english=$scores[$section_key]['language']['english'];
		$math=$scores[$section_key]['math'];
		$nature=$scores[$section_key]['nature'];
		$social=$scores[$section_key]['social'];
		$health=$scores[$section_key]['health'];
		$art=$scores[$section_key]['art'];
		$life=$scores[$section_key]['life'];
		$complex=$scores[$section_key]['complex'];
		$showdata.="<tr>
					<td align='center'>$section_key</td>
					<td align='center'>$chinese</td>
					<td align='center'>$local</td>
					<td align='center'>$english</td>
					<td align='center'>$math</td>
					<td align='center'>$nature</td>
					<td align='center'>$social</td>
					<td align='center'>$health</td>
					<td align='center'>$art</td>
					<td align='center'>$life</td>
					<td align='center'>$complex</td>
					";
	}
	//加入社團活動資料
	$elasticity_data='';
	foreach($elasticity_scores as $lasticity)
	{
		$elasticity_name=$lasticity['name'];
		$elasticity_score=$lasticity['score'];
		$elasticity_data.="<li>社團名稱： $elasticity_name  成績： $elasticity_score</li>";
	}
	
	
	$showdata.="<tr><td colspan=4 align='center'>社團活動紀錄</td><td colspan=7>$elasticity_data</td></tr></table>";
	echo iconv("UTF-8","Big5//IGNORE",$showdata);

	//===========================================================================================================================================
	
	//以下為期中獎懲(匯入至暫存資料表 reward_exchange)	
	//先清除舊資料  以免重覆紀錄
	$SQL="DELETE FROM reward_exchange WHERE student_sn=$student_sn AND reward_year_seme='$curr_year_seme'";
	$rs = $CONN->Execute($SQL) or user_error("ERROR WHILE EXCUING SQL! <br><br>$SQL",256);

	//抓取期中獎懲資料
	$inner_rewards=$student->期中資料->期中獎懲->期中獎懲紀錄;
	if(count($inner_rewards)>0)
	{
		$SQL='';	
		foreach($inner_rewards as $key=>$inner_reward){
			$reward_date=$inner_reward->期中獎懲_日期;
			$reward_kind=addslashes(iconv("UTF-8","Big5//IGNORE",$inner_reward->期中獎懲_類別));
			$reward_numbers=iconv("UTF-8","Big5//IGNORE",$inner_reward->期中獎懲_次數);
			$reward_reason=iconv("UTF-8","Big5//IGNORE",$inner_reward->期中獎懲_事由);
			$SQL.="('$student_sn','$curr_year_seme','$reward_date','$reward_kind','$reward_numbers','$reward_reason'),";
		}
		$SQL=substr($SQL,0,-1);
		$SQL="INSERT INTO reward_exchange(student_sn,reward_year_seme,reward_date,reward_kind,reward_numbers,reward_reason) VALUES ".$SQL;
		//$SQL=iconv("UTF-8","Big5//IGNORE",$SQL);
		$SQL=str_replace("'null'","''",$SQL);
		$rs = $CONN->Execute($SQL) or user_error("ERROR WHILE EXCUING SQL! <br><br>$SQL",256);
		echo iconv("UTF-8","Big5//IGNORE","<BR>#◎ 匯入[$seme_year_seme]期中獎懲紀錄 ( stud_absent_move ) OK ! ");
		if($ShowSQL) echo '<BR>'.$SQL;
	} else echo iconv("UTF-8","Big5//IGNORE","<BR>#◎ 匯入[$seme_year_seme]期中獎懲紀錄 ( stud_absent_move ) ~~無期中獎懲紀錄 ! <BR>");

	//以下為>社團活動
	//先清除舊資料  以免重覆紀錄
	$SQL="DELETE FROM association WHERE student_sn=$student_sn AND seme_year_seme='$curr_year_seme'";
	$rs = $CONN->Execute($SQL) or user_error("ERROR WHILE EXCUING SQL! <br><br>$SQL",256);
	
	$inner_associations=$student->期中資料->社團活動->社團活動內容;
	if(count($inner_associations)>0)
	{
		$SQL='';		
		foreach($inner_associations as $key=>$inner_association){
			$association_name=trim($inner_association->社團名稱);
			$association_score = ($inner_association->社團活動成績=='' or $inner_association->社團活動成績=='null')?0:$inner_association->社團活動成績;
			if ($association_name!='null') {
				$SQL.="('$curr_year_seme',$student_sn,'$association_name',$association_score),";
			}
		}
		if ($SQL!='') {
			$SQL=substr($SQL,0,-1);
			$SQL="INSERT INTO association(seme_year_seme,student_sn,association_name,score) VALUES ".$SQL;
			$SQL=iconv("UTF-8","Big5//IGNORE",$SQL);
			$SQL=str_replace("'null'","''",$SQL);
			$rs = $CONN->Execute($SQL) or user_error("ERROR WHILE EXCUING SQL! <br><br>$SQL",256);
			echo iconv("UTF-8","Big5//IGNORE","<BR>#◎ 匯入[$seme_year_seme]期中社團紀錄 ( association ) OK ! ");
			if($ShowSQL) echo '<BR>'.$SQL;

		} else {
			echo iconv("UTF-8","Big5//IGNORE","<BR>#◎ 無 [$seme_year_seme] 期中社團紀錄 OK ! ");
		}
	} else echo iconv("UTF-8","Big5//IGNORE","<BR>#◎ 匯入[$seme_year_seme]期中社團紀錄 ( stud_absent_move ) ~~無期中社團活動紀錄 ! <BR>");

?>
