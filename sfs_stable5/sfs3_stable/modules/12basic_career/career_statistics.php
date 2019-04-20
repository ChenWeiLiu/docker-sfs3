<?php

// $Id:  $

//取得設定檔
include_once "config.php";

sfs_check();

//秀出網頁
head("生涯輔導報表統計");

//模組選單
print_menu($menu_p,$linkstr);

if(checkid($_SERVER['SCRIPT_FILENAME'],1)) {
	$sort_rank=$sort_rank?$sort_rank:10;
	
	//抓取學年學期
	$year_seme=$_POST['year_seme']?$_POST['year_seme']:'';
	$semester_select=get_recent_semester_select('year_seme',$year_seme);
	
	//抓取本學期
	$garde_select=$_POST['year_seme']?get_semester_grade('grade',$curr_year,$curr_seme,$_POST['grade']):'';
	
	if($_POST['grade']){
		
		//產生選單
		$menu=$_POST['menu'];
		$memu_select="<br>《統計項目》<br>";
		$menu_arr=array(1=>'自我認識',2=>'職業與我',3=>'性向測驗',4=>'興趣測驗',5=>'其他測驗',6=>'教育會考',7=>'選擇方向',8=>'升讀志願',9=>'輔導諮詢',10=>'生涯試探');
		foreach($menu_arr as $key=>$title){
			$checked=($menu==$key)?'checked':''; 
			$memu_select.="<input type='radio' name='menu' onclick='this.form.submit()' $checked value='$key'>$title<br>";
		}
		
		//抓取學生列表
		$student_sn_list=get_student_sn_list($year_seme,$_POST['grade']);
		$stud_total=count(explode(',',$student_sn_list));

		switch($menu){
			case 1:
				//抓取個性、各項活動參照表
				$personality_items=SFS_TEXT('個性(人格特質)');
				$activity_items=SFS_TEXT('各項活動');

				//取得我的成長故事既有資料
				$query="select student_sn,personality,interest,specialty from career_mystory where student_sn in ($student_sn_list)";
				$res=$CONN->Execute($query) or die("SQL錯誤:$query");
				$record_count=$res->RecordCount();
				while(!$res->EOF){
					//抓取自我認識各個項目的內容
					$personality_array=unserialize($res->fields['personality']);
					$personality_array=$personality_array[$_POST['grade']];  //只抓目前年級的  進行統計
					foreach($personality_array as $item=>$value) $personality_sum[$item]++;
					
					$interest_array=unserialize($res->fields['interest']);
					$interest_array=$interest_array[$_POST['grade']];  //只抓目前年級的  進行統計
					foreach($interest_array as $item=>$value) $interest_sum[$item]++;
					
					$specialty_array=unserialize($res->fields['specialty']);
					$specialty_array=$specialty_array[$_POST['grade']];  //只抓目前年級的  進行統計
					foreach($specialty_array as $item=>$value) $specialty_sum[$item]++;

					$res->MoveNext();
				}
				
				//排序
				arsort($personality_sum);
				arsort($interest_sum);
				arsort($specialty_sum);
				
				//統計名次列表
				$i=0;
				$personality_rank="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width='100%'>
					<tr bgcolor='#ccccff' align='center'><td>排序</td><td>項　　　目</td><td>統計數</td><td>百分比</td></tr>";
				foreach($personality_sum as $key=>$value){
					$i++;
					$percent=sprintf("%3.2f",$value/$stud_total*100).'%';
					if($i<=$sort_rank) $personality_rank.="<tr align='center'><td>$i</td><td align='left'>($key) {$personality_items[$key]}</td><td>$value</td><td>$percent</td></tr>";					
				}
				$personality_rank.="</table>";
				
				$i=0;
				$interest_rank="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width=100%>
					<tr bgcolor='#ccccff' align='center'><td>排序</td><td>項　　　目</td><td>統計數</td><td>百分比</td></tr>";
				foreach($interest_sum as $key=>$value){
					$i++;
					$percent=sprintf("%3.2f",$value/$stud_total*100).'%';
					if($i<=$sort_rank) $interest_rank.="<tr align='center'><td>$i</td><td align='left'>($key) {$activity_items[$key]}</td><td>$value</td><td>$percent</td></tr>";					
				}
				$interest_rank.="</table>";
				
				$i=0;
				$specialty_rank="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width='100%'>
					<tr bgcolor='#ccccff' align='center'><td>排序</td><td>項　　　目</td><td>統計數</td><td>百分比</td></tr>";
				foreach($specialty_sum as $key=>$value){
					$i++;
					$percent=sprintf("%3.2f",$value/$stud_total*100).'%';
					if($i<=$sort_rank) $specialty_rank.="<tr align='center'><td>$i</td><td align='left'>($key) {$activity_items[$key]}</td><td>$value</td><td>$percent</td></tr>";					
				}
				$specialty_rank.="</table>";
				
				
				
				//列表
				$personality_view="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width='100%'>
					<tr bgcolor='#ccffcc' align='center'><td>Id</td><td>項　　　目</td><td>統計數</td><td>百分比</td></tr>";
				foreach($personality_items as $key=>$value){
					$percent=sprintf("%3.2f",$personality_sum[$key]/$stud_total*100).'%';
					$personality_view.="<tr><td align='center'>$key</td><td>$value</td><td align='center'>{$personality_sum[$key]}</td><td align='center'>$percent</td></tr>";
				}
				$personality_view.="</table>";
				
				$interest_view="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width='100%'>
					<tr bgcolor='#ccffcc' align='center'><td>Id</td><td>項　　　目</td><td>統計數</td><td>百分比</td></tr>";
				foreach($activity_items as $key=>$value){
					$percent=sprintf("%3.2f",$interest_sum[$key]/$stud_total*100).'%';
					$interest_view.="<tr><td align='center'>$key</td><td>$value</td><td align='center'>{$interest_sum[$key]}</td><td align='center'>$percent</td></tr>";
				}
				$interest_view.="</table>";
				
				$specialty_view="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width='100%'>
					<tr bgcolor='#ccffcc' align='center'><td>Id</td><td>項　　　目</td><td>統計數</td><td>百分比</td></tr>";
				foreach($activity_items as $key=>$value){
					$percent=sprintf("%3.2f",$specialty_sum[$key]/$stud_total*100).'%';
					$specialty_view.="<tr><td align='center'>$key</td><td>$value</td><td align='center'>{$specialty_sum[$key]}</td><td align='center'>$percent</td></tr>";
				}
				$specialty_view.="</table>";
				
				$showdata.="※統計的學生數： $record_count / $stud_total<table width=100% style='border-collapse: collapse; font-size=12px;'><tr align='center'><td>《個性(人格特質)》</td><td>《休閒興趣》</td><td>《專長》</td></tr>
				<tr valign='top'><td>$personality_rank<br>$personality_view</td><td>$interest_rank<br>$interest_view</td><td>$specialty_rank<br>$specialty_view</td></tr></table>";

				break;
			case 2:	
				//抓取選擇職業時重視的條件參照表
				$weight_items=SFS_TEXT('選擇職業時重視的條件');
				
				//重視條件
				$query="select student_sn,occupation_suggestion,occupation_myown,occupation_others,occupation_weight from career_mystory where student_sn in ($student_sn_list)";
				$res=$CONN->Execute($query) or die("SQL錯誤:$query");
				$record_count=$res->RecordCount();
				while(!$res->EOF){
					//抓取自我認識各個項目的內容
					$weight_array=unserialize($res->fields['occupation_weight']);
					$weight_array=$weight_array[$_POST['grade']];  //只抓目前年級的進行統計
					foreach($weight_array as $item=>$value) $weight_sum[$item]++;
					$res->MoveNext();
				}
				
				//排序
				arsort($weight_sum);
	
				//統計名次列表
				$i=0;
				$weight_rank="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width=100%>
					<tr bgcolor='#ccccff' align='center'><td>排序</td><td>項　　　目</td><td>統計數</td><td>百分比</td></tr>";
				foreach($weight_sum as $key=>$value){
					$i++;
					$percent=sprintf("%3.2f",$value/$stud_total*100).'%';
					if($i<=$sort_rank) $weight_rank.="<tr align='center'><td>$i</td><td align='left'>($key) {$weight_items[$key]}</td><td>$value</td><td align='center'>$percent</td></tr>";					
				}
				$weight_rank.="</table>";

				$weight_view="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width=100%>
					<tr bgcolor='#ccffcc' align='center'><td>Id</td><td>項　　　目</td><td>統計數</td><td>百分比</td></tr>";
				foreach($weight_items as $key=>$value){
					$percent=sprintf("%3.2f",$weight_sum[$key]/$stud_total*100).'%';
					$weight_view.="<tr><td align='center'>$key</td><td>$value</td><td align='center'>{$weight_sum[$key]}</td><td align='center'>$percent</td></tr>";
				}
				$weight_view.="</table>";
				
				$showdata="※統計的學生數： $record_count / $stud_total<table width=100% style='border-collapse: collapse; font-size=12px;'><tr align='center'><td>《選擇職業時重視的條件》</td></tr><tr valign='top'><td>$weight_rank<br>$weight_view</td></tr></table>";
			
				break;
			case 3;
			case 4;
			case 5:
				//取得性向測驗既有資料
				$target_id=$menu-2;
				$query="select study,job from career_test where id=$target_id and student_sn in ($student_sn_list)";
				$res=$CONN->Execute($query) or die("SQL錯誤:$query");
				$record_count=$res->RecordCount();
				while(!$res->EOF){
					$item=$res->fields['study'];
					if($item) $study_sum[$item]++;

					$item=$res->fields['job'];
					if($item) $job_sum[$item]++;
					$res->MoveNext();
				}
				$showdata="※統計的學生數： $record_count / $stud_total
				<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width=100%>
				<tr bgcolor='#ffcccc' align='center'><td rowspan=2>NO.</td><td colspan=3>升學適合就讀..</td><td colspan=3>就業適合從事..</td></tr>
				<tr bgcolor='#ffcccc' align='center'><td>項目</td><td>統計數</td><td>百分比</td><td>項目</td><td>統計數</td><td>百分比</td></tr>";
				
				arsort($study_sum);
				$i=0;
				foreach($study_sum as $key=>$value) {
					$i++; $study_rank[$i]['key']=$key;
					$study_rank[$i]['value']=$value;
					$study_rank[$i]['percent']=sprintf("%3.2f",$value/$stud_total*100).'%';
				}
				
				arsort($job_sum);
				$i=0;
				foreach($job_sum as $key=>$value) {
					$i++;
					$job_rank[$i]['key']=$key;
					$job_rank[$i]['value']=$value;
					$job_rank[$i]['percent']=sprintf("%3.2f",$value/$stud_total*100).'%';
				}
				
				$max=max(count($study_rank),count($job_rank));
				for($i=1;$i<=$max;$i++) $showdata.="<tr align='center'><td>$i</td><td>{$study_rank[$i]['key']}</td><td>{$study_rank[$i]['value']}</td><td>{$study_rank[$i]['percent']}</td><td>{$job_rank[$i]['key']}</td><td>{$job_rank[$i]['value']}</td><td>{$job_rank[$i]['percent']}</td></tr>";
			
				$showdata.="</table>";
				break;
			case 6:
				//取得教育會考成績資料
				$subject_arr=array('c'=>'國文','e'=>'英語','m'=>'數學','n'=>'自然','s'=>'社會','w'=>'寫作測驗');
				$max_score=-1; $min_score=9999;
				$query="select * from career_exam where student_sn in ($student_sn_list)";
				$res=$CONN->Execute($query) or die("SQL錯誤:$query");
				$record_count=$res->RecordCount();
				if($record_count){
					while(!$res->EOF){
						foreach($subject_arr as $key=>$value){
							$score=$res->fields[$key];
							$exam_sum[$key][$score]++;
							$max_score=max($max_score,$score); $min_score=min($min_score,$score);
						}
						$res->MoveNext();
					}
				
					foreach($subject_arr as $key=>$value) $subject_title.="<td>$value</td>";				
					$showdata="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width='100%'>
					<tr bgcolor='#c4d9ff' align='center'><td>得分</td>$subject_title</tr>";
					for($i=$max;$i>=min;$i--){
						$subject_score='';
						foreach($subject_arr as $key=>$value) $subject_score.="<td>{$exam_sum[$key][$i]}</td>";
						$showdata.="<tr align='center'><td>$i</td>$subject_score</tr>";
					}
					$showdata.="</table>";
				} else $showdata="<center><font size=5 color='#ff0000'><br><br>未發現教育會考成績資料！<br><br></font></center>";
				break;
			case 7:
				//就 學生期望與 家長期望、教師建議 進行同質異質分析
				//抓取生涯選擇方向參照表
				$direction_items=SFS_TEXT('生涯選擇方向');
				//取得既有資料
				$query="select direction from career_view where student_sn in ($student_sn_list)";
				$res=$CONN->Execute($query) or die("SQL錯誤:$query");
				$record_count=$res->RecordCount();
				if($record_count){
					$direction_initial=array(1=>'self',2=>'parent',3=>'teacher');
					$direction_title=array(1=>'自己的想法',2=>'家長的期望',3=>'學校教師的建議');
					while(!$res->EOF){
						$direction_array=unserialize($res->fields['direction']);
						for($i=1;$i<=3;$i++){
							//統計
							foreach($direction_initial as $key=>$value){
								$target_id=$direction_array[$value][$i];
								$direction_sum[$i][$value][$target_id]++;								
							}
							//異同統計
							if($direction_array[$direction_initial[1]][$i]==$direction_array[$direction_initial[2]][$i]) $compare[$i][$direction_initial[2]]['same']++; else $compare[$i][$direction_initial[2]]['diff']++;
							if($direction_array[$direction_initial[1]][$i]==$direction_array[$direction_initial[3]][$i]) $compare[$i][$direction_initial[3]]['same']++; else $compare[$i][$direction_initial[3]]['diff']++;
						}
						$res->MoveNext();
					}
					
					//開始輸出
					for($i=1;$i<=3;$i++){
						$item_title='';
						foreach($direction_title as $key=>$title) $item_title.="<td>$title</td>";
						$showdata.="《 第 $i 選擇 》<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111'>
							<tr bgcolor='#c4d9ff' align='center'><td>選擇方向</td>$item_title</tr>";
						foreach($direction_items as $key=>$item){
							$showdata.="<tr align='right'><td>$item</td>";
							foreach($direction_initial as $id=>$aa) $showdata.="<td align='center'>{$direction_sum[$i][$aa][$key]}</td>";
							$showdata.="</tr>";							
						}
						//異同資訊
						$showdata.="<tr></tr><tr bgcolor='#ffcccc' align='center'><td>學生想法與家長期望比較</td><td colspan=3>同：{$compare[$i]['parent']['same']} 　　 異：{$compare[$i]['parent']['diff']}</td></tr>
								<tr bgcolor='#ccffcc' align='center'><td>學生想法與教師建議比較</td><td colspan=3>同：{$compare[$i]['teacher']['same']} 　　 異：{$compare[$i]['teacher']['diff']}</td></tr>
								</table><br>";
						
					}
				} else $showdata="<center><font size=5 color='#ff0000'><br><br>未發現生涯選擇方向資料！<br><br></font></center>";
				break;
			case 8:
				$ordered=$_POST['order'];
				$rank_radio="※要分析的志願序：";
				for($i=1;$i<=$sort_rank;$i++){
					$checked=$ordered[$i]?'checked':'';
					$color=$ordered[$i]?'#ff0000':'#aaaaaa';
					if($ordered[$i]) $ordered_list.="$i,";
					$rank_radio.="<input type='checkbox' name='order[$i]' value=$i $checked onclick='this.form.submit()'><font color='$color'>$i<font>";
				}
				$ordered_list=substr($ordered_list,0,-1);
				if($ordered_list){
					//就 學生志願序進行學校、學科統計列表
					//取得既有資料
					$query="select school,course from career_course where student_sn in ($student_sn_list) and aspiration_order in ($ordered_list)";
					$res=$CONN->Execute($query) or die("SQL錯誤:$query");
					$record_count=$res->RecordCount();
					if($record_count){
						while(!$res->EOF){
							$school=$res->fields['school'];
							$school_order[$school]++;
							$course=$res->fields['course'];
							$course_order[$course]++;
							$sc=$school.'-'.$course;
							$sc_order[$sc]++;
							
							$res->MoveNext();
						}
						
						//排序
						arsort($school_order);
						arsort($course_order);
						arsort($sc_order);
						
						//開始輸出
						$school_list="《學校志願排序》<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width='100%'>
							<tr bgcolor='#ffcccc' align='center'><td>NO.</td><td>學校名稱</td><td>選填人數</td></tr>";
						foreach($school_order as $key=>$value){
							$ss++;
							$school_list.="<tr><td align='center'>$ss</td><td>$key</td><td align='center'>$value</td></tr>";
						}
						$school_list.="</table>";
						
						$course_list="《學科志願排序》<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width='100%'>
							<tr bgcolor='#fcfccc' align='center'><td>NO.</td><td>學程科別</td><td>選填人數</td></tr>";
						foreach($course_order as $key=>$value){
							$cc++;
							$course_list.="<tr><td align='center'>$cc</td><td>$key</td><td align='center'>$value</td></tr>";
						}
						$course_list.="</table>";
						
						
						$sc_list="《學校-學科志願排序》<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width='100%'>
						<tr bgcolor='#cfcafa' align='center'><td>NO.</td><td>學校-學程科別</td><td>選填人數</td></tr>";
						foreach($sc_order as $key=>$value){
							$sscc++;
							$sc_list.="<tr><td align='center'>$sscc</td><td>$key</td><td align='center'>$value</td></tr>";
						}
						$sc_list.="</table>";


						$showdata="<table width='100%' style='border-collapse: collapse; font-size=12px;'><tr><td valign='top'>$school_list</td><td valign='top'>$course_list</td><td valign='top'>$sc_list</td></tr></table>";
					} else $showdata="<center><font size=5 color='#ff0000'><br><br>未發現學生志願序資料！<br><br></font></center>";
				} 
				$showdata=$rank_radio.$showdata;
				break;
			case 9:
				//就 輔導諮詢建議 統計學生紀錄數
				//先產生學生紀錄數陣列
				$zero=$stud_total;				
				$query="select student_sn,count(*) as counter from career_guidance where student_sn in ($student_sn_list)";
				$res=$CONN->Execute($query) or die("SQL錯誤:$query");
				while(!$res->EOF){
					$counter=$res->fields['counter'];
					$guidance_sum[$counter]++;	
					$zero--;
					$res->MoveNext();
				}
				$guidance_sum[0]=$zero;
				
				$zero=$stud_total;
				$query="select student_sn,count(*) as counter from career_consultation where student_sn in ($student_sn_list)";
				$res=$CONN->Execute($query) or die("SQL錯誤:$query");
				while(!$res->EOF){
					$counter=$res->fields['counter'];
					$consultation_sum[$counter]++;	
					$zero--;					
					$res->MoveNext();
				}
				$consultation_sum[0]=$zero;
			
				$zero=$stud_total;
				$query="select student_sn,count(*) as counter from career_parent where student_sn in ($student_sn_list)";
				$res=$CONN->Execute($query) or die("SQL錯誤:$query");
				while(!$res->EOF){
					$counter=$res->fields['counter'];
					$parent_sum[$counter]++;
					$zero--;
					$res->MoveNext();
				}
				$parent_sum[0]=$zero;
				
				//排序
				arsort($guidance_sum);
				$guidance_list="《生涯輔導紀錄》<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width='100%'>
				<tr bgcolor='#cfcafa' align='center'><td>NO.</td><td>記錄數</td><td>人數</td></tr>";
				foreach($guidance_sum as $key=>$value){
					$gg++;
					$guidance_list.="<tr align='center'><td>$gg</td><td>$key</td><td>$value</td></tr>";
				}
				$guidance_list.="</table>";				
				
				arsort($consultation_sum);
				$consultation_list="《生涯諮詢紀錄》<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width='100%'>
				<tr bgcolor='#facfca' align='center'><td>NO.</td><td>記錄數</td><td>人數</td></tr>";
				foreach($consultation_sum as $key=>$value){
					$cc++;
					$consultation_list.="<tr align='center'><td>$cc</td><td>$key</td><td>$value</td></tr>";
				}
				$consultation_list.="</table>";
				
				arsort($parent_sum);
				$parent_list="《家長的話》<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width='100%'>
				<tr bgcolor='#cffaca' align='center'><td>NO.</td><td>記錄數</td><td>人數</td></tr>";
				foreach($parent_sum as $key=>$value){
					$pp++;
					$parent_list.="<tr align='center'><td>$pp</td><td>$key</td><td>$value</td></tr>";
				}
				$parent_list.="</table>";
				
				//輸出
				$showdata="<table width='100%' style='border-collapse: collapse; font-size=12px;'><tr><td valign='top'>$guidance_list</td><td valign='top'>$consultation_list</td><td valign='top'>$parent_list</td></tr></table>";
				
				break;	
			case 10:
				//抓取個性、各項活動參照表
				$course_array=SFS_TEXT('生涯試探學程及群科');
				$activity_array=SFS_TEXT('生涯試探活動方式');
				
				//檢查是否有空的紀錄該刪除
				$query="select a.student_sn,b.curr_class_num,b.stud_name,b.stud_sex,b.stud_study_cond from career_explore a inner join stud_base b on a.student_sn=b.student_sn where isnull(degree) or isnull(course_id) order by b.curr_class_num";
				$res=$CONN->Execute($query) or die("SQL錯誤:$query");
				$null_count=$res->RecordCount();
				while(!$res->EOF){
					$color=($res->fields['stud_sex']==1)?'#0000ff':'#ff0000';
					$color=($res->fields['stud_study_cond']==0 or $res->fields['stud_study_cond']==15)?$color:'#cccccc';
					$err_stud_list.="<font color='$color'>( {$res->fields['curr_class_num']} ){$res->fields['stud_name']};</font> ";
					$res->MoveNext();
				}
				if($err_stud_list) $err_stud_list="<font color='red'>※系統發現有 $null_count 位學生的生涯試探活動紀錄填寫不完整：$err_stud_list</font>";
				
				//先產生學生紀錄數陣列
				$zero=$stud_total;				
				$query="select student_sn,count(*) as counter from career_explore where student_sn in ($student_sn_list) group by student_sn";
				$res=$CONN->Execute($query) or die("SQL錯誤:$query");
				while(!$res->EOF){
					$counter=$res->fields['counter'];
					$explore_sum[$counter]++;	
					$zero--;
					$res->MoveNext();
				}
				$explore_sum[0]=$zero;				
				//排序
				arsort($explore_sum);
				$explore_list="《個人參與場次數》<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width='100%'>
				<tr bgcolor='#cfcafa' align='center'><td>參與場次數</td><td>人數</td></tr>";
				foreach($explore_sum as $key=>$value){
					$explore_list.="<tr align='center'><td>$key</td><td>$value</td></tr>";
				}
				$explore_list.="</table>";	
				
				//參與後感興趣統計
				$query="select student_sn,degree from career_explore where student_sn in ($student_sn_list) order by degree";
//echo $query.'<br><br>';				
				$res=$CONN->Execute($query) or die("SQL錯誤:$query");
				while(!$res->EOF){
					$degree=$res->fields['degree'];
					$degree_sum[$degree]++;
					$res->MoveNext();
				}
				$explore_list.="<br><table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width='100%'>
					<tr align='center' bgcolor='#ffcccc'><td>《參與後感興趣的程度》</td><td>《人數》</td></tr>";
				foreach($degree_sum as $key=>$value){
					$explore_list.="<tr align='center'><td>$key</td><td>$value</td></tr>";
				}
				$explore_list.="</table>";
				

				$query="select course_id,degree,count(*) as counter from career_explore where student_sn in ($student_sn_list) group by course_id,degree";
				$res=$CONN->Execute($query) or die("SQL錯誤:$query");
				while(!$res->EOF){
					$course_id=$res->fields['course_id'];
					$degree=$res->fields['degree'];
					$course_sum[$course_id][$degree]+=$res->fields['counter'];					
					$res->MoveNext();
				}				
				$course_list="《辦理活動學程及群科數》<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width='100%'>
				<tr bgcolor='#cfcafa' align='center'><td>活動學程及群科</td><td>參與人數</td><td>感興趣的程度</td></tr>";
				foreach($course_sum as $key=>$value){
					$stud_sum=0;
					$degree_list='';
					foreach($value as $degree=>$counter){
						$degree_list.="<b>$degree</b> → $counter 人<br>";
						$stud_sum+=$counter;
					}
					$course_list.="<tr align='center'><td align='left'>($key) {$course_array[$key]}</td><td>$stud_sum</td><td>$degree_list</td></tr>";
				}
				$course_list.="</table>";	


				$query="select activity_id,degree,count(*) as counter from career_explore where student_sn in ($student_sn_list) group by activity_id,degree";
				$res=$CONN->Execute($query) or die("SQL錯誤:$query");
				while(!$res->EOF){
					$activity_id=$res->fields['activity_id'];
					$degree=$res->fields['degree'];
					$activity_sum[$activity_id][$degree]+=$res->fields['counter'];		
					$res->MoveNext();
				}				
				$activity_list="《辦理活動方式數》<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width='100%'>
				<tr bgcolor='#cfcafa' align='center'><td>活動方式</td><td>參加人數</td><td>感興趣的程度</td></tr>";
				foreach($activity_sum as $key=>$value){
					$stud_sum=0;
					$degree_list='';
					foreach($value as $degree=>$counter){
						$degree_list.="<b>$degree</b> → $counter 人<br>";
						$stud_sum+=$counter;
					}
					$activity_list.="<tr align='center'><td align='left'>($key) {$activity_array[$key]}</td><td>$stud_sum</td><td>$degree_list</td></tr>";
				}
				$activity_list.="</table>";

				//學期辦理數
				$query="select seme_key,degree,count(*) as counter from career_explore where student_sn in ($student_sn_list) group by seme_key,degree";
				$res=$CONN->Execute($query) or die("SQL錯誤:$query");
				while(!$res->EOF){
					$seme_key=$res->fields['seme_key'];
					$degree=$res->fields['degree'];
					$seme_sum[$seme_key][$degree]+=$res->fields['counter'];		
					$res->MoveNext();
				}				
				$seme_list="《參加的年級-學期》<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width='100%'>
				<tr bgcolor='#cfcafa' align='center'><td>年級-學期</td><td>參加人數</td><td>感興趣的程度</td></tr>";
				foreach($seme_sum as $key=>$value){
					$stud_sum=0;
					$degree_list='';
					foreach($value as $degree=>$counter){
						$degree_list.="<b>$degree</b> → $counter 人<br>";
						$stud_sum+=$counter;
					}
					$seme_list.="<tr align='center'><td>$key</td><td>$stud_sum</td><td>$degree_list</td></tr>";
				}
				$seme_list.="</table>";	
				
				
				//輸出
				$showdata="<table width='100%' style='border-collapse: collapse; font-size=12px;'>
					<tr><td valign='top'>$explore_list</td><td valign='top'>$seme_list</td></tr>
					<tr><td valign='top'><br>$course_list</td><td valign='top'><br>$activity_list</td></tr>
					</table>";
				
				break;		
		}
	}
	$main="<font size=2><form method='post' action='$_SERVER[SCRIPT_NAME]' name='myform'>
		<table width='100%' cellspacing=6  cellpadding=5 style='border-collapse: collapse; font-size=12px;'>
			<tr>
				<td valign='top' width=100 bgcolor='#fccfcf'>$semester_select<br>$garde_select<br>$memu_select</td>
				<td valign='top'>$showdata<br><br>$err_stud_list</td>
			</tr></table></form></font>";
	echo $main;
	
} else echo "<center><font size=5 color='#ff0000'><br><br>您不具有模組管理權，系統禁止您使用！<br><br></font></center>";

foot();

?>
