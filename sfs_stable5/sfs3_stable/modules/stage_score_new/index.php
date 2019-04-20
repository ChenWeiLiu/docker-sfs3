<?php

/* 取得設定檔 */
include "config.php";

sfs_check();

$year_seme=$_POST[year_seme]?$_POST[year_seme]:sprintf("%03d%d",curr_year(),curr_seme());
$class_id=$_POST[class_id]?$_POST[class_id]:'';
$stage_arr=$_POST[stage];
$subject_arr=$_POST[subject];
$stages=$_POST[stages];
$test_ratio=$_POST[test_ratio];
$score_mode=$_POST[score_mode];
$stage_radio=$_POST[stage_radio]?$_POST[stage_radio]:1;
$percision=$_POST[percision]?$_POST[percision]:$default_percision;
$note_text=$_POST[note_text]?$_POST[note_text]:$default_note_text;

//進行換行替換
$note_text=str_replace("\n","<br>",$note_text);

$sel_year=substr($year_seme,0,-1);
$sel_seme=substr($year_seme,-1);
$go_caption="HTML輸出列印";
$class_year=substr($class_id,0,-2);
foreach($stage_arr as $key=>$value){ $stage_list.="[$value]"; }

//產生選取科目陣列
foreach($subject_arr as $key=>$value) {		
	$temp=explode('_',$value);
	$ss_id=$temp[0];
	$score_subject_array[$ss_id][rate]=$temp[1];
	$score_subject_array[$ss_id][subject_id]=$temp[2];
	$score_subject_array[$ss_id][subject_name]=stripslashes($temp[3]);
}
//echo "<pre>"; print_r($score_subject_array); echo "</pre>"; 

//進行HTML列印
if($_POST[act]==$go_caption){
	$teacher_name=stripslashes($_POST[teacher_name]);
	//精度值要減1
	$percision--;
	$score_group_array=array();
	$page_break="<P STYLE='page-break-before: always;' />";
	//$group_item_array=array(10=>'100分',9=>'90分以上<br>未滿100分',8=>'80分以上<br>未滿90分',7=>'70分以上<br>未滿80分',6=>'60分以上<br>未滿70分',5=>'50分以上<br>未滿60分',4=>'40分以上<br>未滿50分',3=>'30分以上<br>未滿40分',2=>'20分以上<br>未滿30分',1=>'10分以上<br>未滿20分',0=>'0分以上<br>未滿10分');
	$group_item_array=array(10=>'100分',9=>'90~99分',8=>'80~89分',7=>'70~79分',6=>'60~69分',5=>'50~59分',4=>'40~49分',3=>'30~39分',2=>'20~29分',1=>'10~19分',0=>'0~9分');
	if($stage_radio==1){
		//最大的階段(用以決定平時成績列表階段)
		$max_section=max($stage_arr);
		//取出階段成績比例設定
		if($score_mode=='all') {
			$stage_ratio=explode('-',$test_ratio);
			for($i=1;$i<=$stages;$i++){
				$stage_ratio_arr[$i]['定期評量']=$stage_ratio[0];
				$stage_ratio_arr[$i]['平時成績']=$stage_ratio[1];
				//$stage_ratio_arr[$i]['全學期']=100;
			}
		} else {
			$test_ratio_arr=explode(',',$test_ratio);
			foreach($test_ratio_arr as $key=>$value){
				$stage_ratio=explode('-',$value);
				$stage_ratio_arr[$key+1]['定期評量']=$stage_ratio[0];
				$stage_ratio_arr[$key+1]['平時成績']=$stage_ratio[1];
				//$stage_ratio_arr[$key+1]['全學期']=100;		
			}	
		}
		
		//製作階段成績標題
		$subject_title='';		
		foreach($score_subject_array as $ss_id=>$subject_data){
			$subject_title.="<td colspan=3>$subject_data[subject_name] (*$subject_data[rate])</td>";
			$item_title.="<td>定期</td><td>平時</td><td>平均</td>";
		}
		$subject_title.="<td rowspan=2>總分</td><td rowspan=2>平均</td>";
		
		//找出階段成績(自傳入的ss_id)
		$score_db="score_semester_".intval($sel_year).'_'.$sel_seme;
		$score_class_id=sprintf('%03d_%d_%02d_%02d',$sel_year,$sel_seme,$class_year,substr($class_id,-2));
		foreach($_POST[selected_stud] as $key=>$student_data){
			$temp=explode('_',$student_data);
			$student_sn=$temp[0];
			$student_num=$temp[1];
			//抓取姓名
			$sql="SELECT stud_name FROM stud_base WHERE student_sn=$student_sn";
			$res = $CONN->Execute($sql) or trigger_error($sql,E_USER_ERROR);
			$student_name=$res->fields[0];

			//抓取成績(只論是否已經傳到教務處、不分完整與否，成績由科目選項抓取後再計算)
			$sql="SELECT ss_id,score,test_name,test_sort FROM $score_db WHERE student_sn=$student_sn AND class_id='$score_class_id' AND sendmit=1";
			$res = $CONN->Execute($sql) or trigger_error($sql,E_USER_ERROR);
			while(!$res->EOF){
				$ss_id=$res->fields[ss_id];
				$test_name=$res->fields[test_name];
				$test_sort=$res->fields[test_sort];
				$stage=$res->fields[test_name];
				$student_score_array[$student_sn][$ss_id][$test_sort][$test_name]=$res->fields[score];			
				$res->MoveNext();
			}
			//開始進行階段成績計算與輸出
			$title=$school_long_name.$title_break.intval($sel_year)."學年度第".$sel_seme."學期".$report_title;
			$student_score_array[$student_sn][stage_data]="<table border=0 width=100%><tr align='center'>
			<td align='right'><img src='$logo_link' alt='圖示可至模組變數設定'></td><td><P style='font-size:$title_font_size; color:$title_font_color; font-family:$title_font_name'>$title</P>
			◎班級：{$_POST[selected_class_name]} 　◎座號：{$student_num} 　◎姓名：{$student_name} 　◎導師：{$teacher_name}</td></tr>
			<tr align='left'><td colspan=2>$note_text</td></tr></table>";
			
			//計算階段並列示成績
			$stage_score='';
			foreach($stage_arr as $key=>$stage){
				$ratio_1=$stage_ratio_arr[$stage]['定期評量'];
				$ratio_2=$stage_ratio_arr[$stage]['平時成績'];
				$subject_rate_count=0;
				$subject_score_count=0;
				$stage_score.="<tr align='center'><td colspan=$subject_count><b>第 $stage 階段<br><font size=2>(定期:$ratio_1%,平時:$ratio_2%)</font></td>";
				foreach($score_subject_array as $ss_id=>$subject_data){
					$score_1=$student_score_array[$student_sn][$ss_id][$stage]['定期評量'];
					$score_1=number_format($score_1,$percision);
					$score_1=intval($score_1)?$score_1:'';				
					$score_2=$student_score_array[$student_sn][$ss_id][$stage]['平時成績'];
					$score_2=number_format($score_2,$percision);
					$score_2=intval($score_2)?$score_2:'';
					$score_avg=($score_1*$ratio_1+$score_2*$ratio_2)/($ratio_1+$ratio_2);
					$score_avg=number_format($score_avg,$percision);
					$score_avg=intval($score_avg)?$score_avg:'';
					$stage_score.="<td bgcolor='$stage_bgcolor'>$score_1</td><td bgcolor='$nor_bgcolor'>$score_2</td><td bgcolor='$avg_bgcolor'><b>$score_avg</td>";
					//計算總分
					$subject_rate_count+=$score_subject_array[$ss_id][rate];
					$subject_score_count+=$score_subject_array[$ss_id][rate]*$score_avg;
					
					//科目組距
					if($max_section==$stage){
						$score_avg_2=intval($score_avg/10);
						$score_group_array[$ss_id][$score_avg_2]++;
					}
				}
				
				$subject_score_count=intval($subject_score_count)?$subject_score_count:'';
				$subject_score_avg=number_format($subject_score_count/$subject_rate_count,$percision);
				$subject_score_avg=intval($subject_score_avg)?$subject_score_avg:'';
				$stage_score.="<td><b>$subject_score_count</td><td><b>$subject_score_avg</td></tr>";

				//平均組距
				if($max_section==$stage){
					$score_avg_2=intval($subject_score_avg/10);
					$score_group_array[avg][$score_avg_2]++;
				}
			}
			
			$student_score_array[$student_sn][stage_data].="<table border=$stage_border_width cellpadding=2 cellspacing='0' style='border-collapse: collapse; font-size=$font_size;' bordercolor='$stage_border_color' width=100%>
			<tr align='center'><td rowspan=2>階段別</td>$subject_title</tr><tr align='center' style='font-size:$detail_title_font_size;'>$item_title</tr>
			$stage_score	
			</table>";  //階段成績列表
			
			if($_POST[show_nor_detail]){
		
				//抓取並列示選取最大階段的平時成績 $max_section;
				$nor_score_db="nor_score_".intval($sel_year).'_'.$sel_seme;
				$nor_score_data='';		
				foreach($score_subject_array as $ss_id=>$subject_data){
					$class_subj=$score_class_id.'_'.$subject_data[subject_id];
					$sql="SELECT test_name,weighted,test_score FROM $nor_score_db WHERE stud_sn=$student_sn AND class_subj='$class_subj' AND enable=1 AND stage=$max_section";
					$res = $CONN->Execute($sql) or trigger_error($sql,E_USER_ERROR);
					$nor_columns='';
					$nor_scores='';
					$weighted_count=0;
					$test_score_count=0;
					
					$nor_subject_data="<table border=$nor_border_width cellpadding=6 cellspacing='0' style='border-collapse: collapse; font-size=$font_size;' bordercolor='$nor_border_color' width=100%>";
					$nor_subject_data.="<tr align='center' bgcolor='#ffffcc'><td colspan=2>$subject_data[subject_name]</td></tr>";
					while(!$res->EOF){
						$test_score=$res->fields[test_score];
						if($test_score<>-100){
							$test_name=$res->fields[test_name];
							$weighted=$res->fields[weighted];							
							
							$weighted_count+=$weighted;
							$test_score_count+=$weighted*$test_score;
							
							$nor_subject_data.="<tr><td>$test_name (*$weighted)</td><td align='center'>$test_score</td></tr>";
						}
						$res->MoveNext();
					}
					$test_score_avg=number_format($test_score_count/$weighted_count,$percision);
					$test_score_avg=intval($test_score_avg)?$test_score_avg:'';
					if(!intval($test_score_avg)) $test_score_avg='---';
					$nor_subject_data.="<tr align='center'><td><b>平　均</td><td><b>$test_score_avg</td></tr></table>";
					$nor_score_data.="<td>$nor_subject_data<td>";
				}
				$nor_score_data="<br><center><b>◎ 第 $max_section 階段平時成績記錄 ◎</center><table border=0 width=100%><tr valign='top'>$nor_score_data</tr></table>";
				$student_score_array[$student_sn][nor_score_data]=$nor_score_data;  //平時成績列表
			}
		}

		//寫組距表
		//設定組距陣列
		$score_group_list="<br><center><b>◎ 第 $max_section 階段學習評量班級成績組距表 ◎</center>
							<table border=$stage_border_width cellpadding=2 cellspacing='0' style='border-collapse: collapse; font-size=$font_size;' bordercolor='$stage_border_color' width=100%>
						<tr align='center'><td bgcolor='#ccffcc'>學習領域科目</td>";
		//組距抬頭
		foreach($group_item_array as $key=>$value) $score_group_list.="<td bgcolor='#ccffcc'><font size=2>$value</font></td>";
		$score_group_list.="</tr>";
		foreach($score_subject_array as $ss_id=>$data){
			$score_group_list.="<tr align='center'><td bgcolor='#ffcccc'>".$data[subject_name]."</td>";
			for($i=10;$i>=0;$i--){
				$counter=$score_group_array[$ss_id][$i];
				$score_group_list.="<td>$counter</td>";
			}
			$score_group_list.="</tr>";
		}
		//平均成績組距
		$score_avg_list='';
		for($i=10;$i>=0;$i--){
			$counter=$score_group_array[avg][$i];
			$score_avg_list.="<td><b>$counter</td>";
		}		
		$score_avg_list="<tr align='center' bgcolor='#ccccff'><td>*平均成績*</td>$score_avg_list</tr>";
		$score_group_list.="$score_avg_list</table>";
		
		//開始進行輸出
		foreach($student_score_array as $student_sn=>$data){			
			echo $data['stage_data'];
			if($_POST['show_group_list']) echo $score_group_list;
			echo $data['nor_score_data'];			
			echo $report_footer;
			if($_POST['force_new_page']) echo $page_break; else echo "<br>";				
		}
		
	} else {
		//製作科目標題
		$subject_title='';		
		foreach($score_subject_array as $ss_id=>$subject_data){
			$subject_title.="<td bgcolor='#ffcccc'>$subject_data[subject_name] (*$subject_data[rate])</td>";
		}
	
		//找出階段成績(自傳入的ss_id)
		$score_db="score_semester_".intval($sel_year).'_'.$sel_seme;
		$score_class_id=sprintf('%03d_%d_%02d_%02d',$sel_year,$sel_seme,$class_year,substr($class_id,-2));
		foreach($_POST[selected_stud] as $key=>$student_data){
			$temp=explode('_',$student_data);
			$student_sn=$temp[0];
			$student_num=$temp[1];
			//抓取姓名
			$sql="SELECT stud_name FROM stud_base WHERE student_sn=$student_sn";
			$res = $CONN->Execute($sql) or trigger_error($sql,E_USER_ERROR);
			$student_name=$res->fields[0];

			//抓取成績(只抓取已經傳到教務處全學期的成績)
			$sql="SELECT ss_id,score,test_name,test_sort FROM $score_db WHERE student_sn=$student_sn AND class_id='$score_class_id' AND test_sort='255' AND sendmit=1";
			$res = $CONN->Execute($sql) or trigger_error($sql,E_USER_ERROR);
			while(!$res->EOF){
				$ss_id=$res->fields[ss_id];
				$test_name=$res->fields[test_name];
				$test_sort=$res->fields[test_sort];
				$stage=$res->fields[test_name];
				$student_score_array[$student_sn][$ss_id]=$res->fields[score];			
				$res->MoveNext();
			}
			//開始進行階段成績計算與輸出
			$title=$school_long_name.$title_break.intval($sel_year)."學年度第".$sel_seme."學期".$report_title;
			$student_score_array[$student_sn]['stage_data']="<table border=0 width=100%><tr align='center'>
			<td align='right'><img src='$logo_link' alt='圖示可至模組變數設定'></td><td><P style='font-size:$title_font_size; color:$title_font_color; font-family:$title_font_name'>$title</P>
			◎班級：{$_POST[selected_class_name]} 　 ◎座號：{$student_num} 　◎姓名：$student_name 　◎導師：{$teacher_name}</td></tr>
			<tr align='left'><td colspan=2>$note_text</td></tr></table>";
			
			//計算階段並列示成績
			$stage_score='';
			$stage_score.="<tr align='center' height='40'><td bgcolor='#ccffff'><b>成　績</td>";
			foreach($score_subject_array as $ss_id=>$subject_data){
				$score_1=$student_score_array[$student_sn][$ss_id];
				$score_1=number_format($score_1,$percision);
				$score_1=intval($score_1)?$score_1:'';				
				$stage_score.="<td><b>$score_1</td>";				
				//科目組距
				$score_avg_2=intval($score_1/10);
				$score_group_array[$ss_id][$score_avg_2]++;
			}
			$stage_score.="</tr>";
			
			$student_score_array[$student_sn]['stage_data'].="<table border=$stage_border_width cellpadding=2 cellspacing='0' style='border-collapse: collapse; font-size=$font_size;' bordercolor='$stage_border_color' width=100%>
			<tr align='center'><td bgcolor='#ccffff'><b>不分階段學科</td>$subject_title</tr>$stage_score	
			</table>";  //階段成績列表
			
			if($_POST[show_nor_detail]){
				$nor_score_db="nor_score_".intval($sel_year).'_'.$sel_seme;
				$nor_score_data='';		
				foreach($score_subject_array as $ss_id=>$subject_data){
					$class_subj=$score_class_id.'_'.$subject_data[subject_id];
					$sql="SELECT test_name,weighted,test_score FROM $nor_score_db WHERE stud_sn=$student_sn AND class_subj='$class_subj' AND enable=1 AND stage=255";
					$res = $CONN->Execute($sql) or trigger_error($sql,E_USER_ERROR);
					$nor_columns='';
					$nor_scores='';
					$weighted_count=0;
					$test_score_count=0;
					
					$nor_subject_data="<table border=$nor_border_width cellpadding=6 cellspacing='0' style='border-collapse: collapse; font-size=$font_size;' bordercolor='$nor_border_color' width=100%>";
					$nor_subject_data.="<tr align='center' bgcolor='#ffffcc'><td colspan=2>$subject_data[subject_name]</td></tr>";
					while(!$res->EOF){
						$test_score=$res->fields[test_score];
						if($test_score<>-100){							
							$test_name=$res->fields[test_name];
							$weighted=$res->fields[weighted];
							
							$weighted_count+=$weighted;
							$test_score_count+=$weighted*$test_score;
							
							$nor_subject_data.="<tr><td>$test_name (*$weighted)</td><td align='center'>$test_score</td></tr>";
						}
						$res->MoveNext();
					}
					$test_score_avg=number_format($test_score_count/$weighted_count,$percision);
					$test_score_avg=intval($test_score_avg)?$test_score_avg:'';
					if(!intval($test_score_avg)) $test_score_avg='---';
					$nor_subject_data.="<tr align='center'><td><b>平　均</td><td><b>$test_score_avg</td></tr></table>";
					$nor_score_data.="<td>$nor_subject_data<td>";
				}
				$student_score_array[$student_sn]['nor_score_data']="<br><center><b>◎ 不分階段學科平時成績記錄 ◎</center><table border=0 width=100%><tr valign='top'>$nor_score_data</tr></table>";
			}
		}
		
		//寫組距表
		//設定組距陣列
		$score_group_list="<br><center><b>◎ 不分階段學習評量班級成績組距表 ◎</center>
							<table border=$stage_border_width cellpadding=2 cellspacing='0' style='border-collapse: collapse; font-size=$font_size;' bordercolor='$stage_border_color' width=100%>
						<tr align='center'><td bgcolor='#ccffcc'>學習領域科目</td>";
		//組距抬頭
		foreach($group_item_array as $key=>$value) $score_group_list.="<td bgcolor='#ccffcc'><font size=2>$value</font></td>";
		$score_group_list.="</tr>";
		foreach($score_subject_array as $ss_id=>$data){
			$score_group_list.="<tr align='center'><td bgcolor='#ffcccc'>".$data[subject_name]."</td>";
			for($i=10;$i>=0;$i--){
				$counter=$score_group_array[$ss_id][$i];
				$score_group_list.="<td>$counter</td>";
			}
			$score_group_list.="</tr>";
		}
		$score_group_list.="</table>";
		
		//開始進行輸出
		foreach($student_score_array as $student_sn=>$data){			
			echo $data['stage_data'];
			if($_POST['show_group_list']) echo $score_group_list;
			echo $data['nor_score_data'];			
			echo $report_footer;
			if(isset($_POST['force_new_page'])) echo $page_break; else echo "<br>";				
		}		
	}		
	exit;
}


//秀出網頁
head(" 定期評量成績通知單");
echo <<<HERE
<script>

function tagall(status,s) {
  var i =0;
  while (i < document.myform.elements.length)  {
    if(document.myform.elements[i].name==s) {
      document.myform.elements[i].checked=status;
    }
    i++;
  }
}

function check_select(stage_radio) {
  var i=0; j=0; k=0; l=0; answer=true;
  while (i < document.myform.elements.length)  {
    if(document.myform.elements[i].checked) {
		if(document.myform.elements[i].name=='selected_stud[]') j++;
		if(document.myform.elements[i].name=='stage[]') k++;
		if(document.myform.elements[i].name=='subject[]') l++;
    }
    i++;
  }
  
  if(j==0) { alert("尚未選取學生！"); answer=false; }
  if(stage_radio==1) if(k==0) { alert("尚未選取階段！"); answer=false; }
  if(l==0) { alert("尚未選取科目！"); answer=false; }
  
  return answer;
}

function check_default(default_subject) {
  var i =0; s='';
	  
	while (i < document.myform.elements.length)  {
		if(document.myform.elements[i].name=='subject[]'){
			s=document.myform.elements[i].value;
			var s_arr=s.split('_');
			s=s_arr[3];
			var pos=default_subject.indexOf(s);
			if(pos>0) document.myform.elements[i].checked=true;			
		}
		i++;
	}
}
</script>
HERE;

//印選單
print_menu($menu_p);

//學期選單
$year_seme_array=get_class_seme();
krsort($year_seme_array); //反轉排序

$year_seme_select="<select name='year_seme' onchange=\"this.form.target='$_PHP[SCRIPT_NAME]'; this.form.submit()\">";
foreach($year_seme_array as $key=>$value){
	$selected=($year_seme==$key)?' selected':'';
	$year_seme_select.="<option value='$key'$selected>$value</option>";
}
$year_seme_select.="</select>";

//echo $year_seme_select; exit;

$stage_radio_list="<input type='radio' name='stage_radio' value='1'".($stage_radio==1?' checked':'')." onclick=\"this.form.target='$_PHP[SCRIPT_NAME]'; this.form.submit();\">階段評量
	<input type='radio' name='stage_radio' value='2'".($stage_radio==2?' checked':'')." onclick=\"this.form.target='$_PHP[SCRIPT_NAME]'; this.form.submit();\">不分階段　";	


//班級選單
$class_select="<select name='class_id' onchange=\"this.form.target='$_PHP[SCRIPT_NAME]'; this.form.submit()\"><option></option>";

$sql="SELECT * FROM school_class WHERE enable=1 AND year=$sel_year AND semester=$sel_seme order by c_year,c_sort";
$res = $CONN->Execute($sql) or trigger_error($sql,E_USER_ERROR);
while(!$res->EOF){
	$this_class_id=sprintf("%d%02d",$res->fields[c_year],$res->fields[c_sort]);
	$this_class_name=$school_kind_name[$res->fields[c_year]].$res->fields[c_name]."班";
	$selected=($this_class_id==$class_id)?' selected':'';
	if($selected) $selected_class_name="<input type='hidden' name='selected_class_name' value='$this_class_name'>";
	$class_select.="<option value='$this_class_id'$selected>$this_class_name</option>";	
	$res->MoveNext();
}
$class_select.="</select>$selected_class_name";

//假使有傳入班級代號才進行
if($class_id){
	//抓取班級導師 098_2_03_03
	$score_class_id=sprintf('%03d_%d_%02d_%02d',$sel_year,$sel_seme,$class_year,substr($class_id,-2));
	$sql="SELECT teacher_1 FROM school_class WHERE class_id='$score_class_id'";
	$res = $CONN->Execute($sql) or trigger_error($sql,E_USER_ERROR);
	$teacher_name=$res->fields[0];
	
	
	//要不要顯示全選按鈕
	$select_deselect=" 導師：$teacher_name 　<input type='hidden' name='teacher_name' value='$teacher_name'>
		<input type='checkbox' name='stage_tag' checked onclick='javascript:tagall(this.checked,\"selected_stud[]\");'>選取全部/取消選取";

	//取得班級學生列表
	$stud_select="SELECT a.student_sn,a.seme_num,b.stud_name,b.stud_sex,b.stud_study_cond FROM stud_seme a INNER JOIN stud_base b ON a.student_sn=b.student_sn WHERE a.seme_year_seme='$year_seme' AND seme_class='$class_id' order by seme_num";
	$recordSet=$CONN->Execute($stud_select) or user_error("讀取失敗！<br>$stud_select",256);

	$col=$columns; //自模組變數設定每一列顯示幾人
	$studentdata="<table border=1 cellpadding=3 cellspacing='0' style='border-collapse: collapse; font-size=$font_size;' bordercolor='#ccccff'>";
	while(!$recordSet->EOF)	{
		$student_sn=$recordSet->fields[student_sn];
		$stud_name=$recordSet->fields[stud_name];
		$class_no=$recordSet->fields[seme_num];
		$stud_sex=$recordSet->fields[stud_sex];
		$stud_study_cond=$recordSet->fields[stud_study_cond];

		$pointer=($recordSet->currentrow() % $col)+1;
		if($pointer==1) $studentdata.="<tr>";
		$stud_data=$student_sn.'_'.$class_no;
		if($stud_study_cond) $studentdata.="<td bgcolor='#888888' align='center'><input type='checkbox' name='selected_stud[]' value='$stud_data'>($class_no)$stud_name</td>";
			else $studentdata.="<td bgcolor=".($stud_sex==1?"#CCFFCC":"#FFCCCC")." align='center'><input type='checkbox' name='selected_stud[]' value='$stud_data' checked>($class_no)$stud_name</td>";
		if($pointer==$col or $recordSet->EOF) $studentdata.="</tr>";
		$recordSet->MoveNext();
	}
		
	//抓取課程設定階段列表
	if($stage_radio==1){
		$sql="SELECT performance_test_times,test_ratio,score_mode FROM score_setup WHERE enable=1 AND year=$sel_year AND semester=$sel_seme AND class_year=$class_year";
		$res = $CONN->Execute($sql) or trigger_error($sql,E_USER_ERROR);
		$stages=$res->fields[0];
		$test_ratio=$res->fields[1];
		$score_mode=$res->fields[2];
		$stage_check="";
		for($i=1;$i<=$stages;$i++){
			$checked=array_search($i,$stage_arr)?'checked':'';
			$checked=(strpos($stage_list,"[$i]")!==false)?'checked':''; 
			$stage_check.="<input type='checkbox' name='stage[]' value=$i $checked>$i<br>";
		}
		$stage_check.="<input type='hidden' name='stages' value='$stages'><input type='hidden' name='test_ratio' value='$test_ratio'><input type='hidden' name='score_mode' value='$score_mode'>";
		$show_nor_detail="<input type='checkbox' name='show_nor_detail'".($_POST[show_nor_detail]?' checked':'').">列示選取最大階段數的平時成績記錄";
		$is_print='a.print=1';
	} else { $stage_check.="---"; $show_nor_detail="<input type='checkbox' name='show_nor_detail'".($_POST[show_nor_detail]?' checked':'').">列示平時成績記錄"; $is_print='a.print<>1';}
	
	$show_group_list="<input type='checkbox' name='show_group_list'".($_POST[show_group_list]?' checked':'').">列示成績組距表";
	
	//抓取本學期本班課程設定為print的科目(print=1代表完整)
	$sql="SELECT a.ss_id,a.link_ss,a.rate,a.scope_id,a.subject_id,b.subject_name FROM score_ss a LEFT JOIN score_subject b ON a.subject_id=b.subject_id WHERE a.year=$sel_year AND a.semester=$sel_seme and a.class_year=$class_year AND a.enable=1 AND a.need_exam=1 AND $is_print ORDER BY sort,sub_sort";
	$res=$CONN->Execute($sql) or trigger_error("錯誤訊息： $sql", E_USER_ERROR);
	//$subject_array=array();
	$subject_check='';
	while(!$res->EOF) {
		$ss_id=$res->fields['ss_id'];
		$subject_name=$res->fields['subject_name']?$res->fields['subject_name']:$res->fields['link_ss'];
		$rate=$res->fields['rate'];
		$subject_id=$res->fields['subject_id']?$res->fields['subject_id']:$res->fields['scope_id'];
		if(array_key_exists($ss_id,$score_subject_array)) $checked=' checked'; else $checked='';
		$subject_check.="<input type='checkbox' name='subject[]' value='".$ss_id.'_'.$rate.'_'.$subject_id.'_'."$subject_name' $checked>$subject_name (*$rate)<br>";
		$res->MoveNext();
	}
	
	$percision_radio="◎成績精度：";
	$percision_array=array('1'=>'整數','2'=>'小數1位','3'=>'小數2位');
	foreach($percision_array as $key=>$value){
		if($percision==$key) $checked='checked'; else $checked='';
		$percision_radio.="<input type='radio' value='$key' name='percision' $checked>$value";	
	}
		
	$note_table="<table border=$stage_border_width cellpadding=2 cellspacing='0' style='border-collapse: collapse; font-size=$font_size;' bordercolor='#ccccff' width=100%>
		<tr><td><textarea rows='$note_rows' name='note_text' cols=100%>$note_text</textarea></td></tr></table>";	
	$force_new_page="<input type='checkbox' name='force_new_page'".($_POST[force_new_page]?' checked':'').">強制為每個學生分頁";
	$subject_check="$note_table<table border=1 cellpadding=5 cellspacing='0' style='border-collapse: collapse; font-size=$font_size;' bordercolor='#ccccff' width='100%'>
					<tr align='center'>
					<td bgcolor='#ffffcc'><input type='checkbox' name='stage_tag' onclick='javascript:tagall(this.checked,\"stage[]\");'>階段選取</td>
					<td bgcolor='#ffffcc'><input type='checkbox' name='subject_tag' onclick='javascript:tagall(this.checked,\"subject[]\");'>評量科目(加權)</td>					
					<td rowspan=2>$percision_radio<br><br>$force_new_page<br>$show_group_list<br>$show_nor_detail<br><br>
						<input type='submit' style='border-width:1px; cursor:hand; color:white; background:#ff5555; font-size:$title_font_size;' value='$go_caption' name='act' onclick='this.form.target=\"$class_id\"; return check_select($stage_radio);'>
						<br><font size=2 color='red'>*教學者未將階段成績傳送到教務處將不會顯示*<br>*IE可能會有無法正確自動分頁問題，可改用firefox*</font>
					</td></tr>
					<tr valign='top'><td align='center'>$stage_check</td><td><input type='button' style='border-width:2px; cursor:hand; color:black; width:100%; background:#ccccfa; font-size:$font_size;' value='定期考查科目' onclick=\"javascript:check_default('$default_subject');\"><br><br>$subject_check</td></tr></table>";

	$studentdata.="<tr><td align='center' colspan=$col>$subject_check</td></tr></table>";	
}
$main="<form name='myform' method='post'>$year_seme_select $stage_radio_list $class_select $select_deselect $studentdata</form>";
echo $main;


foot();


?>
