<?php

// $Id:  $

//取得設定檔
include_once "config.php";

sfs_check();

//秀出網頁
head("其他生涯輔導紀錄");

//模組選單
print_menu($menu_p,$linkstr);

$menu=$_POST['menu'];

if($student_sn){
	//抓取學生本學期就讀班級
	$query="select * from stud_seme where student_sn=$student_sn and seme_year_seme='$seme_year_seme'";
	$res=$CONN->Execute($query);
	$seme_class=$res->fields['seme_class'];
	$seme_class_name=$res->fields['seme_class_name'];
	$seme_num=$res->fields['seme_num'];
	$stud_grade=substr($seme_class,0,-2);


	//抓取學生學期就讀班級
	$stud_seme_arr=get_student_seme($student_sn);

	//產生選單
	$memu_select="※我要檢視或設定：";
	$menu_arr=array(1=>'生涯輔導紀錄',2=>'生涯諮詢紀錄',3=>'家長的話');
	foreach($menu_arr as $key=>$title){
		$checked=($menu==$key)?'checked':''; 
		$color=($menu==$key)?'#0000ff':'#000000'; 
		$memu_select.="<input type='radio' name='menu' value='$key' $checked onclick='this.form.submit();'><b><font color='$color'>$title</font></b>";
	}

	//$act=$menu?"<center><input type='submit' value='儲存紀錄' name='go' onclick='return confirm(\"確定要\"+this.value+\"?\")' style='border-width:1px; cursor:hand; color:white; background:#5555ff; font-size:20px; height=42'></center>":"";
	switch($menu){
		case 1:
			if($_POST['go']=='修改'){
				$query="update career_guidance set teacher_name='{$_POST['teacher_name']}',emphasis='{$_POST['emphasis']}',target='{$_POST['target']}',guidance_date='{$_POST['guidance_date']}',update_sn ='{$_SESSION['session_tea_sn']}' where sn={$_POST['edit_sn']}";
				$res=$CONN->Execute($query) or die("SQL錯誤:$query");
				$_POST['edit_sn']=0;
			} elseif($_POST['go']=='刪除'){
				$query="delete from career_guidance where sn={$_POST['edit_sn']}";
				$res=$CONN->Execute($query) or die("SQL錯誤:$query");
				$_POST['edit_sn']=0;
			} elseif($_POST['go']=='新增'){
				$query="insert into career_guidance set student_sn=$student_sn,teacher_name='{$_SESSION{session_tea_name}}',guidance_date=now()";
				$res=$CONN->Execute($query) or die("SQL錯誤:$query");
			}
			$act='';
			$guidance_list="<br><input type='submit' name='go' value='新增'><input type='hidden' name='edit_sn' value=''><input type='hidden' name='add' value=''>
				<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width='100%'>
				<tr bgcolor='#ffcccc' align='center'><td>NO.</td><td>日期</td><td>對象</td><td>輔導重點或建議</td><td>輔導教師</td><td>紀錄日期</td></tr>";
			//抓取既有諮詢紀錄
			$query="select * from career_guidance where student_sn=$student_sn order by guidance_date";
			$res=$CONN->Execute($query) or die("SQL錯誤:$query");
			if($res){
				while(!$res->EOF){
					$ii++;
					$sn=$res->fields['sn'];
					if($_POST['edit_sn']==$sn){
						$guidance_list.="<tr align='center' bgcolor='#ffffcc'>
							<td>$ii<input type='hidden' name='del_sn' value='{$_POST['edit_sn']}'>
							<br><input type='submit' value='修改' name='go' onclick='document.myform.edit_sn.value=\"$sn\";return confirm(\"確定要\"+this.value+\"?\")'>
							<br><input type='submit' value='刪除' name='go' onclick='document.myform.edit_sn.value=\"$sn\"; return confirm(\"確定要\"+this.value+\"?\")'>
							<br><input type='reset' value='取消' onclick='this.form.submit();'>
							</td>
							<td><input type='text' name='guidance_date' value='{$res->fields['guidance_date']}' size=8></td>
							<td><input type='text' name='target' value='{$res->fields['target']}' size=10></td>
							<td><textarea name='emphasis' style='border-width:1px; color:brown; width=100%; height=100%;'>{$res->fields['emphasis']}</textarea></td>
							<td><input type='text' name='teacher_name' value='{$res->fields['teacher_name']}' size=10></td>
							<td>{$res->fields['update_time']}</td>
							</tr>";
					} else {
						$emphasis=str_replace("\r\n",'<br>',$res->fields['emphasis']);
						$java_script="onMouseOver=\"this.style.cursor='hand'; this.style.backgroundColor='#ccccff';\" onMouseOut=\"this.style.backgroundColor='#ffffff';\" ondblclick='document.myform.edit_sn.value=\"$sn\"; document.myform.submit();'";
						$guidance_list.="<tr align='center' $java_script>
						<td>$ii</td>						
						<td>{$res->fields['guidance_date']}</td>
						<td>{$res->fields['target']}</td>
						<td align='left'>$emphasis</td>
						<td>{$res->fields['teacher_name']}</td>	
						<td>{$res->fields['update_time']}</td>
						</tr>";	
					}
					$res->MoveNext();
				}
			} else $guidance_list.="<tr align='center'><td colspan=7 height=24>未發現生涯輔導既有諮詢紀錄！</td></tr>";
			$guidance_list.="</table>";
			
			$showdata="$guidance_list";
			
			break;
		case 2:	
			if($_POST['go']=='修改'){
				$query="update career_consultation set seme_key='{$_POST['seme_key']}',teacher_name='{$_POST['teacher_name']}',emphasis='{$_POST['emphasis']}',memo='{$_POST['memo']}' where sn={$_POST['edit_sn']}";
				$res=$CONN->Execute($query) or die("SQL錯誤:$query");
				$_POST['edit_sn']=0;
			} elseif($_POST['go']=='刪除'){
				$query="delete from career_consultation where sn={$_POST['edit_sn']}";
				$res=$CONN->Execute($query) or die("SQL錯誤:$query");
				$_POST['edit_sn']=0;
			} elseif($_POST['go']=='新增'){
				$query="insert into career_consultation set student_sn=$student_sn,seme_key='$curr_seme_key',consultation_date=now()";
				$res=$CONN->Execute($query) or die("SQL錯誤:$query");
			}	
			
			//$act='';
			//表格欄位抬頭
			$consultation_list="<input type='submit' name='go' value='新增'><input type='hidden' name='edit_sn' value=''><input type='hidden' name='add' value=''>
				<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width=100%>
				<tr align='center' bgcolor='#ffcccc'><td>NO.</td><td>年級</td><td>日期</td><td>您諮詢的師長</td><td>討論重點及意見</td><td>備註</td>";
			
			//抓取既有諮詢紀錄
			$query="select * from career_consultation where student_sn=$student_sn order by seme_key";
			$res=$CONN->Execute($query) or die("SQL錯誤:$query");
			if($res){
				while(!$res->EOF){
					$ii++;
					$sn=$res->fields['sn'];
					if($_POST['edit_sn']==$sn){
						
						$seme_radio='';
						foreach($stud_seme_arr as $seme_key=>$year_seme){
							$checked=($seme_key==$res->fields['seme_key'])?'checked':'';
							$seme_radio.="<input type='radio' name='seme_key' value='$seme_key' $checked>($seme_key) $year_seme <br>";
						}
						
						$consultation_list.="<tr align='center' bgcolor='#ffffcc'>
							<td>$ii<input type='hidden' name='del_sn' value='{$_POST['edit_sn']}'>
							<br><input type='submit' value='修改' name='go' onclick='document.myform.edit_sn.value=\"$sn\";return confirm(\"確定要\"+this.value+\"?\")'>
							<br><input type='submit' value='刪除' name='go' onclick='document.myform.edit_sn.value=\"$sn\"; return confirm(\"確定要\"+this.value+\"?\")'>
							<br><input type='reset' value='取消' onclick='this.form.submit();'>
							</td>
							<td>$seme_radio</td>
							<td><input type='text' name='consultation_date' value='{$res->fields['consultation_date']}' size=8></td>
							<td><input type='text' name='teacher_name' value='{$res->fields['teacher_name']}' size=10></td>
							<td><textarea name='emphasis' style='border-width:1px; color:brown; width=100%; height=100%;'>{$res->fields['emphasis']}</textarea></td>
							<td><textarea name='memo' style='border-width:1px; color:brown; width=100%; height=100%;'>{$res->fields['memo']}</textarea></td>
							</tr>";
					} else {
						$memo=str_replace("\r\n",'<br>',$res->fields['memo']);
						$emphasis=str_replace("\r\n",'<br>',$res->fields['emphasis']);
						$java_script="onMouseOver=\"this.style.cursor='hand'; this.style.backgroundColor='#ccccff';\" onMouseOut=\"this.style.backgroundColor='#ffffff';\" ondblclick='document.myform.edit_sn.value=\"$sn\"; document.myform.submit();'";
						$consultation_list.="<tr align='center' $java_script>
							<td>$ii</td>
							<td>{$res->fields['seme_key']}</td>
							<td>{$res->fields['consultation_date']}</td>
							<td>{$res->fields['teacher_name']}</td>						
							<td align='left'>$emphasis</td>
							<td align='left'>$memo</td>
							</tr>";	
					}
					$res->MoveNext();
				}
			} else $consultation_list.="<tr align='center'><td colspan=7 height=24>未發現生涯輔導既有諮詢紀錄！</td></tr>";
			$consultation_list.="</table>";
			
			$showdata="<br>$consultation_list";
		
			break;
		case 3:	
			$items=array(1=>'我的成長故事',2=>'各項心理測驗',3=>'學習成果及特殊表現',4=>'生涯輔導紀錄',5=>'生涯統整面面觀',6=>'生涯發展規劃書');
			
			if($_POST['go']=='修改'){
				$items_got=serialize($_POST['items']);
				$query="update career_parent set seme_key='{$_POST['seme_key']}',items='$items_got',suggestion='{$_POST['suggestion']}',suggestion_date='{$_POST['suggestion_date']}' where sn={$_POST['edit_sn']}";
				$res=$CONN->Execute($query) or die("SQL錯誤:$query");
				$_POST['edit_sn']=0;
			} elseif($_POST['go']=='刪除'){
				$query="delete from career_parent where sn={$_POST['edit_sn']}";
				$res=$CONN->Execute($query) or die("SQL錯誤:$query");
				$_POST['edit_sn']=0;
			} elseif($_POST['go']=='新增'){
				$query="insert into career_parent set student_sn=$student_sn,seme_key='$curr_seme_key',suggestion_date=now()";
				$res=$CONN->Execute($query) or die("SQL錯誤:$query");
			}
			
			
			//$act='';
			//表格欄位抬頭
			$parent_list="<input type='submit' name='go' value='新增'><input type='hidden' name='edit_sn' value=''><input type='hidden' name='add' value=''>
				<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width=100%>
				<tr align='center' bgcolor='#ffcccc'><td>NO.</td><td>年級-學期</td><td>日期</td><td>參閱資料</td><td>給孩子的鼓勵及建議</td><td>親師溝通導師簽記</td>";
			
			//抓取既有諮詢紀錄
			$query="select * from career_parent where student_sn=$student_sn order by seme_key";
			$res=$CONN->Execute($query) or die("SQL錯誤:$query");
			if($res){
				while(!$res->EOF){
					$ii++;
					//參閱資料還原為陣列
					$items_array=unserialize($res->fields['items']);
					
					$sn=$res->fields['sn'];
					if($_POST['edit_sn']==$sn){					
						$seme_radio='';
						foreach($stud_seme_arr as $seme_key=>$year_seme){
							$checked=($seme_key==$res->fields['seme_key'])?'checked':'';
							$seme_radio.="<input type='radio' name='seme_key' value='$seme_key' $checked>($seme_key) $year_seme <br>";
						}
						$items_checkox="";					
						foreach($items as $key=>$value){
							$color=$items_array[$key]?'#ff0000':'#000000';
							$checked=$items_array[$key]?'checked':'';
							$items_checkox.="<input type='checkbox' name='items[$key]' value='1' $checked><font color='$color'>$value</font><br>";
						}
						
						$parent_list.="<tr align='center' bgcolor='#ffffcc'>
							<td>$ii<input type='hidden' name='del_sn' value='{$_POST['edit_sn']}'>
							<br><input type='submit' value='修改' name='go' onclick='document.myform.edit_sn.value=\"$sn\";return confirm(\"確定要\"+this.value+\"?\")'>
							<br><input type='submit' value='刪除' name='go' onclick='document.myform.edit_sn.value=\"$sn\"; return confirm(\"確定要\"+this.value+\"?\")'>
							<br><input type='reset' value='取消' onclick='this.form.submit();'>
							</td>
							<td>$seme_radio</td>
							<td><input type='text' name='suggestion_date' value='{$res->fields['suggestion_date']}' size=15></td>
							<td align='left'>$items_checkox</td>
							<td><textarea name='suggestion' style='border-width:1px; color:brown; width=100%; height=100%;'>{$res->fields['suggestion']}</textarea></td>
							<td>{$res->fields['tutor_confirm']}<br>{$res->fields['tutor_name']}-{$res->fields['confirm_date']}</td>		
							</tr>";
					} else {				
						$items_list='';
						foreach($items as $key=>$value){
							$color=$items_array[$key]?'#ff0000':'#000000';
							$checked=$items_array[$key]?'●':'○';
							$items_list.="$checked $value<br>";
						}
						$items_list.="";
						
						$suggestion=str_replace("\r\n",'<br>',$res->fields['suggestion']);
						$tutor_confirm=str_replace("\r\n",'<br>',$res->fields['tutor_confirm']);
						$java_script="onMouseOver=\"this.style.cursor='hand'; this.style.backgroundColor='#ccccff';\" onMouseOut=\"this.style.backgroundColor='#ffffff';\" ondblclick='document.myform.edit_sn.value=\"$sn\"; document.myform.submit();'";
						$parent_list.="<tr align='center' $java_script>
							<td>$ii</td>
							<td>{$res->fields['seme_key']}</td>
							<td>{$res->fields['suggestion_date']}</td>
							<td align='left'>$items_list</td>						
							<td align='left'>$suggestion</td>						
							<td align='left'>{$res->fields['tutor_confirm']}<br>{$res->fields['tutor_name']}-{$res->fields['confirm_date']}</td>						
							</tr>";	
					}
					$res->MoveNext();
				}
			} else $parent_list.="<tr align='center'><td colspan=7 height=24>未發現生涯輔導家長的鼓勵及建議紀錄！</td></tr>";
			$parent_list.="</table>";
			
			$showdata="<br>$parent_list";
			
			break;
	}
}
$main="<font size=2><form method='post' action='$_SERVER[SCRIPT_NAME]' name='myform'><table width=100%><tr><td valign='top' width=100>$class_select<br>$student_select</td><td valign='top'>$memu_select $showdata<br>$act</td></tr></table></form></font>";

echo $main;

foot();

?>
