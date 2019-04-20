<?php

// $Id:  $

//取得設定檔
include_once "config.php";
include "../../include/sfs_case_score.php";

sfs_check();

//秀出網頁
head("學習成果及特殊表現");

//模組選單
print_menu($menu_p,$linkstr);

$menu=$_POST['menu'];


//儲存紀錄處理
if($_POST['go']=='儲存紀錄'){
	$content=serialize($_POST['ponder']);
	//檢查是否已有舊紀錄
	$query="select sn from career_self_ponder where student_sn=$student_sn and id='$menu'";
	$res=$CONN->Execute($query) or die("SQL錯誤:$query");
	$sn=$res->fields[0];
	if($sn) $query="update career_self_ponder set id='$menu',content='$content' where sn=$sn";
		else $query="insert into career_self_ponder set student_sn=$student_sn,id='$menu',content='$content'";
		$res=$CONN->Execute($query) or die("SQL錯誤:$query");
}

if($student_sn){
	//抓取學生學期就讀班級
	$stud_seme_arr=get_student_seme($student_sn);

	//產生選單
	//$memu_select="※我是 $stud_name ，本學期就讀班級： $curr_seme_class ，座號： $curr_seme_num 。<br>※我要檢視 <select name='menu' onchange='this.form.submit();'>";
	$memu_select="※我要檢視：";
	$menu_arr=array('3-1'=>'我的學習表現','3-2'=>'我的經歷（幹部、社團）','3-3'=>'參與各項競賽成果','3-4'=>'行為表現獎懲紀錄','3-5'=>'服務學習紀錄','3-6'=>'生涯試探活動紀錄');

	foreach($menu_arr as $key=>$title){
		$selected=($menu==$key)?'checked':''; 
		$color=($menu==$key)?'#0000ff':'#000000'; 
		$memu_select.="<input type='radio' name='menu' onclick='this.form.submit();' value='$key' $selected><b><font color='$color'>$title</font></b></option>";
	}


	//取得既有所選項目自我省思資料
	if($menu){
		$act="<input type='submit' value='儲存紀錄' name='go' onclick='return confirm(\"確定要\"+this.value+\"?\")'>";
		
		$query="select * from career_self_ponder where student_sn=$student_sn and id='$menu'";
		$res=$CONN->Execute($query);
		$ponder_array=unserialize($res->fields['content']);
	}


	switch($menu){
		case '3-1':
			//取得領域學習成績資料
			$fin_score=cal_fin_score(array($student_sn),$stud_seme_arr);

			$link_ss=array("chinese"=>"語文-國文","english"=>"語文-英語","math"=>"數學","social"=>"社會","nature"=>"自然與生活科技","art"=>"藝術與人文","health"=>"健康與體育","complex"=>"綜合活動");
			//表格欄位抬頭
			$study_list="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width=100%>
					<tr align='center' bgcolor='#ffcccc'><td>年級</td><td>學期</td>";
			foreach($link_ss as $key=>$value) $study_list.="<td>$value</td>";
			$study_list.="<td>對於我的學習表現，我認為</td></tr>";
			
			//內容
			foreach($stud_seme_arr as $seme_key=>$year_seme){			
				$bgcolor=($career_previous or $curr_seme_key==$seme_key)?'#ffdfdf':'#cfefef';
				$readonly=($career_previous or $curr_seme_key==$seme_key)?'':'readonly';
				$study_list.="<tr align='center'><td>$seme_key</td><td>$year_seme</td>";
				foreach($link_ss as $key=>$value) $study_list.="<td>{$fin_score[$student_sn][$key][$year_seme]['score']}</td>";
				$study_list.="<td><textarea name='ponder[$seme_key]' style='border-width:1px; color:brown; background:$bgcolor; font-size:11px; width=100%; height=100%;' $readonly>{$ponder_array[$seme_key]}</textarea></td></tr>";
			}
			$study_list.="</table>";
			
			
			//取得教育會考成績資料
			$exam_list="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width=100%>
			<tr bgcolor='#c4d9ff' align='center'><td>紀錄時間</td><td>國文</td><td>英語</td><td>數學</td><td>自然</td><td>社會</td><td>寫作測驗</td></tr>";
			$query="select * from career_exam where student_sn=$student_sn order by update_time desc";
			$res=$CONN->Execute($query);
			if($res){
				$exam_list.="<tr align='center'>
					<td>{$res->fields['update_time']}</td>
					<td>{$res->fields['c']}</td>
					<td>{$res->fields['e']}</td>
					<td>{$res->fields['m']}</td>
					<td>{$res->fields['n']}</td>
					<td>{$res->fields['s']}</td>
					<td>{$res->fields['w']}</td>
					</tr>";			
				} else $exam_list.="<tr align='center'><td colspan=7>未發現此生的教育會考成績資料</td></tr>";
			$exam_list.="</table>";
			
			//取得體適能成績資料
			$fitness_list="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width=100%>
				<tr bgcolor='#c4d9ff' align='center'>
				<td>年級</td><td>學期</td>
				<td>身高<br>(cm)</td>
				<td>體重<br>(kg)</td>
				<td>BMI指數<br>(kg/m<sup>2</sup>)</td>
				<td>檢測單位</td>
				<td>測驗年月</td>
				<td>坐姿前彎<br>(cm) [%]</td>
				<td>仰臥起坐<br>(次) [%]</td>
				<td>立定跳遠<br>(cm) [%]</td>
				<td>心肺適能<br>(秒) [%]</td>
				<td>年齡</td>
				<td>獎章</td>
				</tr>";
			$query="select * from fitness_data where student_sn=$student_sn order by c_curr_seme";
			$res=$CONN->Execute($query);
			while(!$res->EOF){
				$c_curr_seme=$res->fields['c_curr_seme'];
				$seme_key=array_search($c_curr_seme,$stud_seme_arr);
				//判定獎章
				$g=0;
				$s=0;
				$c=0;
				$passed=0;
				for($i=1;$i<=4;$i++) {
					$field_name='prec'.$i;
					if($res->fields[$field_name]>=85) $g++;
					if($res->fields[$field_name]>=75) $s++;
					if($res->fields[$field_name]>=50) $c++;
					if($res->fields[$field_name]>=25) $passed++;  //通過門檻標準  程式現設為25%以上
				}				
				$medal='';
				if($g==4) $medal="金"; elseif($s==4) $medal="銀 "; elseif($c==4) $medal="銅";
				
				$fitness_list.="<tr align='center'>
					<td>$seme_key</td><td>$c_curr_seme</td>
					<td>{$res->fields['tall']}</td>
					<td>{$res->fields['weigh']}</td>
					<td>{$res->fields['bmt']}</td>
					<td>{$res->fields['organization']}</td>
					<td>{$res->fields['test_y']}-{$res->fields['test_m']}</td>
					<td>{$res->fields['test1']} [{$res->fields['prec1']}]</td>
					<td>{$res->fields['test2']} [{$res->fields['prec2']}]</td>
					<td>{$res->fields['test3']} [{$res->fields['prec3']}]</td>
					<td>{$res->fields['test4']} [{$res->fields['prec4']}]</td>
					<td>{$res->fields['age']}</td>
					<td>$medal</td>
					</tr>";
				$res->MoveNext();
			}
			$fitness_list.="</table>";			
			
			$showdata="<br><br>1.各領域學習成績 $study_list<br>2.國中教育會考表現 $exam_list<br>3.體適能檢測表現 $fitness_list";		
			
			break;
		case '3-2':
			//表格欄位抬頭
			$assistant_list="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width=100%>
				<tr align='center' bgcolor='#ffcccc'><td>年級</td><td>學期</td><td>擔任幹部</td><td>擔任小老師</td><td>備註</td><td>自我省思</td>";
			//內容
			foreach($stud_seme_arr as $seme_key=>$year_seme){
				$bgcolor=($career_previous or $curr_seme_key==$seme_key)?'#ffdfdf':'#cfefef';
				$readonly=($career_previous or $curr_seme_key==$seme_key)?'':'readonly';
				$assistant_list.="<tr align='center'><td>$seme_key</td><td>$year_seme</td>
				<td>1. <input type='text' name='ponder[$seme_key][1][1]' value='{$ponder_array[$seme_key][1][1]}' style='border-width:1px; color:brown; background:$bgcolor;' $readonly>　2. <input type='text' name='ponder[$seme_key][1][2]' value='{$ponder_array[$seme_key][1][2]}' style='border-width:1px; color:brown; background:$bgcolor;' $readonly></td>
				<td>1. <input type='text' name='ponder[$seme_key][2][1]' value='{$ponder_array[$seme_key][2][1]}' style='border-width:1px; color:brown; background:$bgcolor;' $readonly>　2. <input type='text' name='ponder[$seme_key][2][2]' value='{$ponder_array[$seme_key][2][2]}' style='border-width:1px; color:brown; background:$bgcolor;' $readonly></td>
				<td><input type='text' name='ponder[$seme_key][memo]' value='{$ponder_array[$seme_key][memo]}' style='border-width:1px; color:brown; background:$bgcolor;' $readonly></td>
				<td><textarea name='ponder[$seme_key][data]' style='border-width:1px; color:brown; background:$bgcolor; font-size:11px; width=100%; height=100%;' $readonly>{$ponder_array[$seme_key][data]}</textarea></td></tr>";
			}
			$assistant_list.="</table>";

			
			//社團資料
			//表格欄位抬頭
			$club_list="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width=100%>
			<tr align='center' bgcolor='#ffcccc'><td>年級</td><td>學期</td><td>社團名稱</td><td>成績</td><td>擔任職務</td><td>老師評語</td><td>自我省思</td>";
		
			$query="select * from association where student_sn=$student_sn order by seme_year_seme";
			$res=$CONN->Execute($query);
			if($res){
				while(!$res->EOF){
					$seme_year_seme=$res->fields['seme_year_seme'];
					$seme_key=array_search($seme_year_seme,$stud_seme_arr);
					$club_score=$res->fields['score']?$res->fields['score']:'--';
					$feed_back=str_replace("\r\n",'<br>',$res->fields['stud_feedback']);
					$club_list.="<tr align='center'>
					<td>$seme_key</td><td>$seme_year_seme</td>
					<td>{$res->fields['association_name']}</td>
					<td>{$club_score}</td>
					<td>{$res->fields['stud_post']}</td>
					<td align='left'>{$res->fields['description']}</td>
					<td align='left'>$feed_back</td>
					</tr>";			
					$res->MoveNext();
				}
			} else $club_list.="<tr align='center'><td colspan=6 height=24>未發現社團活動紀錄！</td></tr>";
			$club_list.="</table>";
			
			$showdata="<br><br>1.幹部：填寫曾經擔任的全校性、班級幹部或各領域（科）小老師職務，任期須滿一學期以上(含滿一學期)。	$assistant_list<br>2.社團：參加學校於課程內或課後（含假日及寒暑假）實施之社團，滿一學期/20小時。 $club_list";
			
			break;
		case '3-3':
			if($_POST['go']=='修改'){
				$query="update career_race set level={$_POST['level']},squad={$_POST['squad']},name='{$_POST['name']}',rank='{$_POST['rank']}',certificate_date='{$_POST['certificate_date']}',sponsor='{$_POST['sponsor']}',memo='{$_POST['memo']}' where sn={$_POST['edit_sn']}";
				$res=$CONN->Execute($query) or die("SQL錯誤:$query");
				$_POST['edit_sn']=0;
			} elseif($_POST['go']=='刪除'){
				$query="delete from career_race where sn={$_POST['edit_sn']}";
				$res=$CONN->Execute($query) or die("SQL錯誤:$query");
				$_POST['edit_sn']=0;
			} elseif($_POST['go']=='新增'){
				$query="insert into career_race set student_sn=$student_sn,level=1,squad=1,name='---------',rank='------',certificate_date=now(),sponsor='------'";
				$res=$CONN->Execute($query) or die("SQL錯誤:$query");
			}	
			
			$act="<input type='submit' name='go' value='新增'>";
			//表格欄位抬頭
			$race_list="<input type='hidden' name='edit_sn' value=''><input type='hidden' name='add' value=''>
				<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width=100%>
				<tr align='center' bgcolor='#ffcccc'>
				<td>NO.</td><td colspan=2>範圍性質</td><td>競賽名稱</td><td>得獎名次</td><td>證書日期</td><td>主辦單位</td><td>備註</td>";
		
			//各項競賽成果
			$query="select * from career_race where student_sn=$student_sn order by certificate_date";
			$res=$CONN->Execute($query);
			if($res){
				while(!$res->EOF){
					$ii++;
					$sn=$res->fields['sn'];
					if($_POST['edit_sn']==$sn){
						foreach($level_array as $key=>$value){
							$checked=($key==$res->fields['level'])?'checked':'';
							$level_radio.="<input type='radio' name='level' value='$key' $checked>$value<br>";
						}
						foreach($squad_array as $key=>$value){
							$checked=($key==$res->fields['squad'])?'checked':'';
							$squad_radio.="<input type='radio' name='squad' value='$key' $checked>$value<br>";
						}
						$race_list.="<tr align='center' bgcolor='#ffffcc'>
							<td>$ii<input type='hidden' name='del_sn' value='{$_POST['edit_sn']}'>
								<br><input type='submit' value='修改' name='go' onclick='document.myform.edit_sn.value=\"$sn\";return confirm(\"確定要\"+this.value+\"?\")'>
								<br><input type='submit' value='刪除' name='go' onclick='document.myform.edit_sn.value=\"$sn\"; return confirm(\"確定要\"+this.value+\"?\")'>
								<br><input type='reset' value='取消' onclick='this.form.submit();'>
							</td>
							<td align='left'>$level_radio</td>
							<td align='left'>$squad_radio</td>
							<td><input type='text' name='name' value='{$res->fields['name']}'></td>
							<td><input type='text' name='rank' value='{$res->fields['rank']}' size=3></td>
							<td><input type='text' name='certificate_date' value='{$res->fields['certificate_date']}' size=10></td>
							<td><input type='text' name='sponsor' value='{$res->fields['sponsor']}'></td>
							<td><textarea name='memo'  style='border-width:1px; color:brown; width=100%; height=100%;'>{$res->fields['memo']}</textarea></td>
							</tr>";
					} else  {
						$memo=str_replace("\r\n",'<br>',$res->fields['memo']);
						$java_script="onMouseOver=\"this.style.cursor='hand'; this.style.backgroundColor='#ccccff';\" onMouseOut=\"this.style.backgroundColor='#ffffff';\" ondblclick='document.myform.edit_sn.value=\"$sn\"; document.myform.submit();'";
						$race_list.="<tr align='center' $java_script>
							<td>$ii</td>
							<td>{$level_array[$res->fields['level']]}</td>
							<td>{$squad_array[$res->fields['squad']]}</td>
							<td align='left'>{$res->fields['name']}</td>
							<td>{$res->fields['rank']}</td>
							<td>{$res->fields['certificate_date']}</td>
							<td align='left'>{$res->fields['sponsor']}</td>
							<td align='left'>$memo</td>
							</tr>";	
					}
					$res->MoveNext();
				}
			} else $race_list.="<tr align='center'><td colspan=7 height=24>未發現各項競賽成果紀錄！</td></tr>";
			$race_list.="</table>";
			
			
			$showdata="<br>$race_list";
			
			break;
		case '3-4':
			$reward_list="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size:9pt;' bordercolor='#111111' width=100%>
				<tr align='center' bgcolor='#ccccff'><td>NO.</td><td>學期別</td><td>獎懲日期</td><td>獎懲類別</td><td>獎懲事由</td><td>獎懲依據</td><td>銷過日期</td><td>級分採計</td></tr>";
			
			$reward_arr=array("1"=>"嘉獎一次","2"=>"嘉獎二次","3"=>"小功一次","4"=>"小功二次","5"=>"大功一次","6"=>"大功二次","7"=>"大功三次","-1"=>"警告一次","-2"=>"警告二次","-3"=>"小過一次","-4"=>"小過二次","-5"=>"大過一次","-6"=>"大過二次","-7"=>"大過三次");
			//抓取指定學生的獎懲紀錄
			$seme_reward=array();
			$sql="SELECT * FROM reward WHERE student_sn=$student_sn ORDER BY reward_year_seme,reward_date";
			$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
			if($res->RecordCount())
			while(!$res->EOF)
			{
				$reward_kind=$res->fields['reward_kind'];
				$reward_cancel_date=$res->fields['reward_cancel_date'];
				$reward_bonus=$res->fields['reward_bonus']?"<img src='images/ok.png'>":'';
				$reward_year_seme=substr($res->fields['reward_year_seme'],0,-1).'-'.substr($res->fields['reward_year_seme'],-1);
				$recno++;
				$bgcolor=($reward_kind>0)?'#ccffcc':'#ffcccc';
				if($reward_cancel_date=='0000-00-00') $reward_cancel_date=''; else $bgcolor='#cccccc';
				$reward_list.="<tr bgcolor='$bgcolor' align='center'><td>$recno</td><td>$reward_year_seme</td><td>{$res->fields['reward_date']}</td><td>{$reward_arr[$res->fields['reward_kind']]}</td><td align='left'>{$res->fields['reward_reason']}</td><td align='left'>{$res->fields['reward_base']}</td><td>$reward_cancel_date</td><td>$reward_bonus</td></tr>";
				//學期統計
				$reward_year_seme=$res->fields['reward_year_seme'];
				$seme_key=array_search($reward_year_seme,$stud_seme_arr);
				$reward_kind=$res->fields['reward_kind'];			
				
				switch($reward_kind){
					case 1:	$seme_reward_effective[$seme_key][1]++;	$seme_reward_effective['sum'][1]++;	break;
					case 2:	$seme_reward_effective[$seme_key][1]+=2;	$seme_reward_effective['sum'][1]+=2; break;
					case 3:	$seme_reward_effective[$seme_key][3]++;	$seme_reward_effective['sum'][3]++;	break;
					case 4:	$seme_reward_effective[$seme_key][3]+=2;	$seme_reward_effective['sum'][3]+=2; break;
					case 5:	$seme_reward_effective[$seme_key][9]++;	$seme_reward_effective['sum'][9]++;	break;
					case 6:	$seme_reward_effective[$seme_key][9]+=2;	$seme_reward_effective['sum'][9]+=2; break;
					case 7:	$seme_reward_effective[$seme_key][9]+=3;	$seme_reward_effective['sum'][9]+=3; break;
					case -1: $seme_reward_effective[$seme_key][-1]++;	$seme_reward_effective['sum'][-1]++; break;
					case -2: $seme_reward_effective[$seme_key][-1]+=2;	$seme_reward_effective['sum'][-1]+=2; break;
					case -3: $seme_reward_effective[$seme_key][-3]++;	$seme_reward_effective['sum'][-3]++; break;
					case -4: $seme_reward_effective[$seme_key][-3]+=2;	$seme_reward_effective['sum'][-3]+=2; break;
					case -5: $seme_reward_effective[$seme_key][-9]++;	$seme_reward_effective['sum'][-9]++; break;
					case -6: $seme_reward_effective[$seme_key][-9]+=2;	$seme_reward_effective['sum'][-9]+=2; break;
					case -7: $seme_reward_effective[$seme_key][-9]+=3;	$seme_reward_effective['sum'][-9]+=3; break;
				}
				//銷過統計
				if($reward_cancel_date){
					switch($reward_kind){
						case -1: $seme_reward_canceled[$seme_key][-1]++; $seme_reward_canceled['sum'][-1]++; break;
						case -2: $seme_reward_canceled[$seme_key][-1]+=2; $seme_reward_canceled['sum'][-1]+=2; break;
						case -3: $seme_reward_canceled[$seme_key][-3]++; $seme_reward_canceled['sum'][-3]++; break;
						case -4: $seme_reward_canceled[$seme_key][-3]+=2; $seme_reward_canceled['sum'][-3]+=2; break;
						case -5: $seme_reward_canceled[$seme_key][-9]++; $seme_reward_canceled['sum'][-9]++; break;
						case -6: $seme_reward_canceled[$seme_key][-9]+=2; $seme_reward_canceled['sum'][-9]+=2; break;
						case -7: $seme_reward_canceled[$seme_key][-9]+=3; $seme_reward_canceled['sum'][-9]+=3; break;
					}
				}			
				$res->MoveNext();
			} else $reward_list.="<tr><td colspan=12 align='center'><font size=5 color='#ff0000'>未發現任何 {$menu_arr[$menu]} 明細！</font></td>";
			$reward_list.="</table>";
			
			//學期統計列表
			//表格欄位抬頭
			$seme_list="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width=100%>
			<tr align='center' bgcolor='#ffcccc'><td rowspan=2>年級</td><td rowspan=2>學期</td><td colspan=6 bgcolor='#ccccff'>獎懲紀錄</td><td colspan=3 bgcolor='#cccccc'>改過銷過紀錄</td><td rowspan=2>自我省思</td></tr>
			<tr align='center'  bgcolor='#ccccff'><td>大功</td><td>小功</td><td>嘉獎</td><td>警告</td><td>小過</td><td>大過</td><td bgcolor='#cccccc'>警告</td><td bgcolor='#cccccc'>小過</td><td bgcolor='#cccccc'>大過</td></tr>
			";
			//內容
			foreach($stud_seme_arr as $seme_key=>$year_seme){			
				$bgcolor=($career_previous or $curr_seme_key==$seme_key)?'#ffdfdf':'#cfefef';
				$readonly=($career_previous or $curr_seme_key==$seme_key)?'':'readonly';
				$seme_list.="<tr align='center'><td>$seme_key</td><td>$year_seme</td>
					<td>{$seme_reward_effective[$seme_key][9]}</td><td>{$seme_reward_effective[$seme_key][3]}</td><td>{$seme_reward_effective[$seme_key][1]}</td><td>{$seme_reward_effective[$seme_key][-1]}</td><td>{$seme_reward_effective[$seme_key][-3]}</td><td>{$seme_reward_effective[$seme_key][-9]}</td>
					<td>{$seme_reward_canceled[$seme_key][-1]}</td><td>{$seme_reward_canceled[$seme_key][-3]}</td><td>{$seme_reward_canceled[$seme_key][-9]}</td>";
				$seme_list.="<td><textarea name='ponder[$seme_key]' style='border-width:1px; color:brown; background:$bgcolor; font-size:11px; width=100%; height=100%;' $readonly>{$ponder_array[$seme_key]}</textarea></td></tr>";
			}
			//全年統計
			$seme_list.="<tr align='center' bgcolor='#ccccff'><td colspan=2 bgcolor='#ccffcc'>就學期間統計</td>
				<td>{$seme_reward_effective['sum'][9]}</td><td>{$seme_reward_effective['sum'][3]}</td><td>{$seme_reward_effective['sum'][1]}</td><td>{$seme_reward_effective['sum'][-1]}</td><td>{$seme_reward_effective['sum'][-3]}</td><td>{$seme_reward_effective['sum'][-9]}</td>
				<td bgcolor='#cccccc'>{$seme_reward_canceled['sum'][-1]}</td><td bgcolor='#cccccc'>{$seme_reward_canceled['sum'][-3]}</td><td bgcolor='#cccccc'>{$seme_reward_canceled['sum'][-9]}</td>
				<td bgcolor='#ccffcc'><textarea name='ponder[sum]' style='border-width:1px; color:brown; background:$bgcolor; font-size:11px; width=100%; height=100%;'>{$ponder_array['sum']}</textarea></td></tr>";
			$seme_list.="</table>";

			
			$showdata="<br>※明細：$reward_list <br>※統計：$seme_list";
			
			break;
		case '3-5':
			$act='';
			$room_arr=room_kind();
			$seme_list=array();
			//表格欄位抬頭
			$service_list="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111'>
			<tr align='center' bgcolor='#ffcccc'><td>年級</td><td>學期</td><td>服務日期</td><td colspan=2>參加校內外公共服務學習事項及活動項目</td><td>分鐘數</td><td>主辦單位</td><td>自我省思</td><td>登錄單位</td>";
			$query="select a.*,b.* from stud_service_detail a inner join stud_service b on a.item_sn=b.sn where confirm=1 and student_sn=$student_sn order by year_seme";
			$res=$CONN->Execute($query);
			if($res){
				while(!$res->EOF){
					$year_seme=$res->fields['year_seme'];
					$seme_key=array_search($year_seme,$stud_seme_arr);
					$feed_back=str_replace("\r\n",'<br>',$res->fields['stud_feedback']);
					$service_list.="<tr align='center'>
					<td>$seme_key</td><td>$year_seme</td>
					<td>{$res->fields['service_date']}</td> 
					<td>{$res->fields['item']}</td><td align='left'>{$res->fields['memo']}</td>
					<td>{$res->fields['minutes']}</td>
					<td>{$res->fields['sponsor']}</td>
					<td align='left'>$feed_back</td>
					<td>{$room_arr[$res->fields['department']]}</td>
					</tr>";
					$seme_sum[$seme_key]+=$res->fields['minutes'];
					$res->MoveNext();
				}
			} else $service_list.="<tr align='center'><td colspan=6 height=24>未發現已認證的服務學習紀錄！</td></tr>";
			$service_list.="</table>";
			//統計表
			$seme_list="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111'>
			<tr align='center' bgcolor='#ffcccc'><td>年級</td><td>學期</td><td>分鐘數</td><td>服務時數</td></tr>";
			foreach($stud_seme_arr as $seme_key=>$year_seme){
				$minutes=$seme_sum[$seme_key]; $minutes_sum+=$minutes;
				$hours=round($minutes/60,2); $hours_sum+=$hours;			
				$seme_list.="<tr align='center'><td>$seme_key</td><td>$year_seme</td><td>$minutes</td><td>$hours</td></tr>";
			}
			$seme_list.="<tr align='center' bgcolor='#ffcccc'><td colspan=2>就學期間統計</td><td>$minutes_sum</td><td>$hours_sum</td></tr></table>";
			$showdata="<br><br>※紀錄：$service_list<br>※統計：$seme_list";
			
			break;
		case '3-6':	
			if($_POST['go']=='修改'){
				$query="update career_explore set course_id='{$_POST['course_id']}',seme_key='{$_POST['seme_key']}',activity_id='{$_POST['activity_id']}',degree='{$_POST['degree']}',self_ponder='{$_POST['self_ponder']}' where sn={$_POST['edit_sn']}";
				$res=$CONN->Execute($query) or die("SQL錯誤:$query");
				$_POST['edit_sn']=0;
			} elseif($_POST['go']=='刪除'){
				$query="delete from career_explore where sn={$_POST['edit_sn']}";
				$res=$CONN->Execute($query) or die("SQL錯誤:$query");
				$_POST['edit_sn']=0;
			} elseif($_POST['go']=='新增'){
				$query="insert into career_explore set student_sn=$student_sn";
				$res=$CONN->Execute($query) or die("SQL錯誤:$query");
			}	
			
			$act="<input type='submit' name='go' value='新增'>";
			//表格欄位抬頭
			$explore_list="<input type='hidden' name='edit_sn' value=''><input type='hidden' name='add' value=''>
					<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width=100%>
					<tr align='center' bgcolor='#ffcccc'><td>NO.</td><td>年級</td><td>學期</td><td>試探學程及群科</td><td>活動方式</td><td>參與試探活動後圈出對該群科感興趣的程度</td><td>自我省思</td>";
			//抓取個性、各項活動參照表
			$course_array=SFS_TEXT('生涯試探學程及群科');
			$activity_array=SFS_TEXT('生涯試探活動方式');

			//取得生涯試探活動既有資料
			$query="select * from career_explore where student_sn=$student_sn order by seme_key";
			$res=$CONN->Execute($query);
			if($res){
				while(!$res->EOF){
					$ii++;
					$sn=$res->fields['sn'];
					if($_POST['edit_sn']==$sn){					
						foreach($course_array as $key=>$value){
							$checked=($key==$res->fields['course_id'])?'checked':'';
							$course_radio.="<input type='radio' name='course_id' value='$key' $checked>$value<br>";
						}
						foreach($activity_array as $key=>$value){
							$checked=($key==$res->fields['activity_id'])?'checked':'';
							$activity_radio.="<input type='radio' name='activity_id' value='$key' $checked>$value<br>";
						}
						foreach($stud_seme_arr as $seme_key=>$year_seme){
							$checked=($seme_key==$res->fields['seme_key'])?'checked':'';
							$seme_radio.="<input type='radio' name='seme_key' value='$seme_key' $checked>($seme_key) $year_seme <br>";
						}	
						for($i=1;$i<=5;$i++){
							$checked=($i==$res->fields['degree'])?'checked':'';
							$degree_radio.="<input type='radio' name='degree' value='$i' $checked>$i ";
						}						
						
						$explore_list.="<tr align='center' bgcolor='#ffffcc'>
							<td>$ii<input type='hidden' name='del_sn' value='{$_POST['edit_sn']}'>
							<br><input type='submit' value='修改' name='go' onclick='document.myform.edit_sn.value=\"$sn\";return confirm(\"確定要\"+this.value+\"?\")'>
							<br><input type='submit' value='刪除' name='go' onclick='document.myform.edit_sn.value=\"$sn\"; return confirm(\"確定要\"+this.value+\"?\")'>
							<br><input type='reset' value='取消' onclick='this.form.submit();'>
							</td>
							<td colspan=2>$seme_radio</td>
							<td align='left'>$course_radio</td>
							<td align='left'>$activity_radio</td>
							<td>$degree_radio</td>
							<td><textarea name='self_ponder'  style='border-width:1px; color:brown; width=100%; height=100%;'>{$res->fields['self_ponder']}</textarea></td>
							</tr>";
					} else {
						$self_ponder=str_replace("\r\n",'<br>',$res->fields['self_ponder']);
						$java_script="onMouseOver=\"this.style.cursor='hand'; this.style.backgroundColor='#ccccff';\" onMouseOut=\"this.style.backgroundColor='#ffffff';\" ondblclick='document.myform.edit_sn.value=\"$sn\"; document.myform.submit();'";
						$explore_list.="<tr align='center' $java_script>
							<td>$ii</td>
							<td>{$res->fields['seme_key']}</td>
							<td>{$stud_seme_arr[$res->fields['seme_key']]}</td>
							<td>{$course_array[$res->fields['course_id']]}</td>
							<td>{$activity_array[$res->fields['activity_id']]}</td>
							<td>{$res->fields['degree']}</td>
							<td align='left'>$self_ponder</td>
							</tr>";	
					}
					$res->MoveNext();
				}
			} else $explore_list.="<tr align='center'><td colspan=7 height=24>未發現生涯試探活動紀錄！</td></tr>";
			$explore_list.="</table>";

			$showdata="<br>$explore_list";

			break;		
		default:
			$showdata="<center><font size=5 color='#ff0000'><br><br>請選取要檢視或設定的項目！<br><br></font></center>";
	}
}
$main="<font size=2><form method='post' action='$_SERVER[SCRIPT_NAME]' name='myform'><table style='border-collapse: collapse; font-size=12px;'><tr><td valign='top'>$class_select<br>$student_select</td><td valign='top'>$memu_select $act $showdata</form></td></tr></table></font>";

echo $main;

foot();

?>
