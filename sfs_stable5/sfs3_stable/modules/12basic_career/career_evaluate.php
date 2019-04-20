<?php

// $Id:  $

//取得設定檔
include_once "config.php";
include "../../include/sfs_case_score.php";

sfs_check();

//秀出網頁
head("生涯發展規劃書");

//模組選單
print_menu($menu_p,$linkstr);

$menu=$_POST['menu'];

//儲存紀錄處理
if($_POST['go']=='儲存紀錄'){
	switch($menu){
		case 1:
			$factor=serialize($_POST['evaluate']);
			$query="update career_course set factor='$factor' where sn={$_POST['sn']}";
			$res=$CONN->Execute($query) or die("SQL錯誤:$query");	
			break;
		case 3:
			$parent=implode(',',$_POST['parent']);
			$tutor=implode(',',$_POST['tutor']);
			$guidance=implode(',',$_POST['guidance']);
			$query="update career_opinion set parent='$parent',parent_memo='{$_POST['parent_memo']}',tutor='$tutor',tutor_memo='{$_POST['tutor_memo']}',guidance='$guidance',guidance_memo='{$_POST['guidance_memo']}' where sn={$_POST['sn']}";
			$res=$CONN->Execute($query) or die("SQL錯誤:$query");	
			break;
	}
}


if($student_sn){
	//抓取學生學期就讀班級
	$stud_seme_arr=get_student_seme($student_sn);

	//產生選單
	$memu_select="※我要檢視或設定：";
	$menu_arr=array(1=>'志願自我評核',2=>'生涯目標',3=>'師長綜合意見');
	foreach($menu_arr as $key=>$title){
		$checked=($menu==$key)?'checked':''; 
		$color=($menu==$key)?'#0000ff':'#000000'; 
		$memu_select.="<input type='radio' name='menu' value='$key' $checked onclick='this.form.submit();'><b><font color='$color'>$title</font></b>";
	}
	$act=$menu?"<center><input type='submit' value='儲存紀錄' name='go' onclick='return confirm(\"確定要\"+this.value+\"?\")' style='border-width:1px; cursor:hand; color:white; background:#5555ff; font-size:20px; height=42'></center>":"";
	switch($menu){
		case 1:
			//抓取既有資料
			$query="select sn,aspiration_order,school,course,factor from career_course where student_sn=$student_sn order by aspiration_order";
			$res=$CONN->Execute($query);
			$evaluate_count=$res->RecordCount()+1;
			while(!$res->EOF){
				$ii=$res->fields['aspiration_order'];
				$evaluate[$ii]['sn']=$res->fields['sn'];
				$evaluate[$ii]['school']=$res->fields['school'];
				$evaluate[$ii]['course']=$res->fields['course'];
				$evaluate[$ii]['factor']=unserialize($res->fields['factor']);
				$res->MoveNext();
			}
			//表格欄位抬頭
			$evaluate_list="※將我想升讀的高中或高職、五專學校及科別，評估各項考慮因素與每個選項的符合程度，並填入「0～5」的分數，5分代表非常符合，0分代表非常不符合。<input type='hidden' name='edit_order' value=''>
				<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width=100%>
				<tr align='center' bgcolor='#ffcccc'>
				<td bgcolor='#ddffcf'><p align='right'>★志願學校★</p><p align='left'>★考慮因素★</p></td>";
			foreach($evaluate as $order=>$evaluate_data){
				$java_script="onMouseOver=\"this.style.cursor='hand'; this.style.backgroundColor='#aaaaff';\" onMouseOut=\"this.style.backgroundColor='#ffcccc';\" ondblclick='document.myform.edit_order.value=$order; document.myform.submit();'";
				$evaluate_list.="<td $java_script>$order<br>{$evaluate_data['school']}<br>{$evaluate_data['course']}</td>";
			}
			$evaluate_list.='</tr>';

			//抓取考慮因素項目
			$factor_items=array('self'=>'個人因素','env'=>'環境因素','info'=>'資訊因素');
			foreach($factor_items as $item=>$title){
				$factor=SFS_TEXT($title);
				$evaluate_list.="<tr bgcolor='#ddffdd'><td colspan=$evaluate_count>● $title</td></tr>";
				foreach($factor as $key=>$data){
					$evaluate_list.="<tr><td>　 -$data</td>";
					foreach($evaluate as $order=>$evaluate_data){
						$evaluate[$order]['sum']+=$evaluate_data['factor'][$item][$key];
						if($order==$_POST['edit_order']){
							$edit_radio='';
							for($i=1;$i<=5;$i++){
								$checked=($evaluate_data[factor][$item][$key]==$i)?'checked':'';
								$color=($evaluate_data[factor][$item][$key]==$i)?'#ff0000':'#000000';
								$edit_radio.="<input type='radio' name='evaluate[$item][$key]' value=$i $checked><font color='$color'>$i</font>";	
							}					
							$evaluate_list.="<td bgcolor='#fcffcf' align='center'>$edit_radio<input type='hidden' name='sn' value='{$evaluate_data['sn']}'</td>";
						} else { 
							$java_script="onMouseOver=\"this.style.cursor='hand'; this.style.backgroundColor='#aaaaff';\" onMouseOut=\"this.style.backgroundColor='#ffffff';\" ondblclick='document.myform.edit_order.value=$order; document.myform.submit();'";
							$evaluate_list.="<td align='center' $java_script>{$evaluate_data[factor][$item][$key]}</td>"; 
						}
					}
					$evaluate_list.='</tr>';
				}			
			}	
			//加入總計列
			$evaluate_list.="<tr></tr><tr bgcolor='#ddffdd' align='center'><td>★　　總　　　計　　★</td>";
			foreach($evaluate as $order=>$value){
				$evaluate_list.="<td><b>{$value['sum']}<b></td>"; 
			}		
			$evaluate_list.="</tr>";
			
			$evaluate_list.="</table>";
			$act=$_POST['edit_order']?$act:'';
			$showdata="<br>$evaluate_list";
		
			break;
		case 2:
			$act='';
			$showdata='';
			//取得測驗既有資料
			$psy_result="<br><table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width='100%'>
			<tr align='center'><td colspan=3 bgcolor='#ffffcc'>相關心理測驗結果</td></tr><tr align='center'>";
		//取得測驗既有資料
		$item_arr=array(1=>'性向測驗',2=>'興趣測驗',3=>'其他測驗(1)',4=>'其他測驗(2)');
		foreach($item_arr as $key=>$title){
			$query="select * from career_test where student_sn=$student_sn and id=$key";
			$res=$CONN->Execute($query);
			if($res){
				while(!$res->EOF){
					$sn=$res->fields['sn'];
					$content=unserialize($res->fields['content']);

					$title=$content['title'];
					$test_result=$content['data'];
					$study=$res->fields['study'];
					$job=$res->fields['job'];
					
					$content_list="<td><table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width='100%'>
							<tr bgcolor='#ccffcc' align='center'><td colspan=2><b>$title</b></td></tr><tr></tr>
							<tr bgcolor='#ffcccc' align='center'><td>項目</td><td>內容結果</td></tr>";
					if($test_result){
						foreach($test_result as $key2=>$value) $content_list.="<tr><td>$key2</td><td align='center'>$value</td></tr>";
					} else $content_list.="<tr align='center'><td colspan=2 height=100>沒有發現任何分項紀錄！</td></tr>";
					
					$content_list.="<tr bgcolor='#fcccfc'><td colspan=2>●根據測驗結果，在升學方面，我適合就讀： $study<br>●根據測驗結果，在就業方面，我適合從事： $job</td></tr></table></td>";
					
					$psy_result.=$content_list;
	//echo $content_list;				
					$res->MoveNext();
				}
		} else $content_list="<td><center><font size=2 color='#ff0000'>未發現任何{$item_arr[$key]}紀錄！<br></font></center></td>";	
		}
		$psy_result.="</tr></table><br>";

			$showdata.=$psy_result;
		
			//取得領域學習成績資料
			$fin_score=cal_fin_score(array($student_sn),$stud_seme_arr);
			$link_ss=array("chinese"=>"語文-國文","english"=>"語文-英語","math"=>"數學","social"=>"社會","nature"=>"自然與生活科技","art"=>"藝術與人文","health"=>"健康與體育","complex"=>"綜合活動");
			//表格欄位抬頭
			$study_list="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width=100%>
				<tr align='center'><td colspan=10 bgcolor='#ffffcc'>學習表現</td></tr>
				<tr align='center' bgcolor='#ccffcc'><td>年級</td><td>學期</td>";
			foreach($link_ss as $key=>$value) $study_list.="<td>$value</td>";
			
			//內容
			foreach($stud_seme_arr as $seme_key=>$year_seme){			
				$bgcolor=($curr_seme_key==$seme_key)?'#ffdfdf':'#cfefef';
				$readonly=($curr_seme_key==$seme_key)?'':'readonly';
				$study_list.="<tr align='center'><td>$seme_key</td><td>$year_seme</td>";
				foreach($link_ss as $key=>$value) $study_list.="<td>{$fin_score[$student_sn][$key][$year_seme]['score']}</td>";
			}
			//總成績
			$study_list.="<tr></tr><tr align='center' bgcolor='#ccffcc'><td colspan=2>學期平均成績</td>";
			foreach($link_ss as $key=>$value) $study_list.="<td><b>{$fin_score[$student_sn][$key]['avg']['score']}</b></td>";
			$study_list.="</tr></table>";
			$showdata.=$study_list;
			
			$showdata.="<hr>※生涯目標-想升讀的學校-學程：<font color='#0000ff'>";
			//抓取既有資料
			$query="select aspiration_order,school,course from career_course where student_sn=$student_sn order by aspiration_order";
			$res=$CONN->Execute($query);
			//$evaluate_count=$res->RecordCount()+1;
			while(!$res->EOF){
				$ii++;
				$showdata.="($ii). {$res->fields['school']}-{$res->fields['course']}　 ";
				$res->MoveNext();
			}
			$showdata.="</font><hr>";
			break;
		case 3:
			if($_POST['go']=='按我新增'){
				$query="insert into career_opinion set student_sn=$student_sn";
				$res=$CONN->Execute($query) or die("SQL錯誤:$query");
			}
			
			$showdata='';
			//抓取生涯選擇方向參照表
			$direction_items=SFS_TEXT('生涯選擇方向');
			
			//取得師長綜合意見既有資料
			$query="select * from career_opinion where student_sn=$student_sn";
			$res=$CONN->Execute($query);
			if($res){
				while(!$res->EOF){
					$ii++;
					$sn=$res->fields['sn'];				
					$parent=' ,'.$res->fields['parent'].',';	
					$parent_radio='';
			
					foreach($direction_items as $d_key=>$d_value){
						$comp=','.$d_key.',';
						$checked=strpos($parent,$comp)?'checked':'';
						$color=strpos($parent,$comp)?'#ff0000':'#555555';
						$parent_radio.="<input type='checkbox' name='parent[]' value='$d_key' $checked><font color='$color'>$d_value</font>";					
					}
					$parent_memo="<textarea name='parent_memo'  style='border-width:1px; color:brown; width=100%; height=100%;'>{$res->fields['parent_memo']}</textarea>";
					
					$tutor=' ,'.$res->fields['tutor'].',';
					foreach($direction_items as $d_key=>$d_value){
						$comp=','.$d_key.',';
						$checked=strpos($tutor,$comp)?'checked':'';
						$color=strpos($tutor,$comp)?'#00ff00':'#cccccc';
						$tutor_radio.="<input type='checkbox' name='tutor[]' value='$d_key' $checked><font color='$color'>$d_value</font>";	
					}
					$tutor_memo="<textarea name='tutor_memo'  style='border-width:1px; color:brown; width=100%; height=100%;'>{$res->fields['tutor_memo']}</textarea>";
					
					$guidance=' ,'.$res->fields['guidance'].',';
					foreach($direction_items as $d_key=>$d_value){
						$comp=','.$d_key.',';
						$checked=strpos($guidance,$comp)?'checked':'';
						$color=strpos($guidance,$comp)?'#0000ff':'#cccccc';
						$guidance_radio.="<input type='checkbox' name='guidance[]' value='$d_key' $checked><font color='$color'>$d_value</font>";					
					}
					$guidance_memo="<textarea name='guidance_memo'  style='border-width:1px; color:brown; width=100%; height=100%;'>{$res->fields['guidance_memo']}</textarea>";
					
					$content_list.="<input type='hidden' name='sn' value='$sn'><table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width=100%>
								<tr bgcolor='#ffcccc' align='center'><td>NO.</td><td>建議者</td><td>未來選讀建議</td><td width=30%>說明</td></tr>";
					
					$content_list.="<tr><td rowspan=3 align='center'>$ii</td><td align='center'>家　　長</td><td>我希望孩子選擇：<br>$parent_radio</td><td>$parent_memo</td></tr>
									<tr><td align='center'>導　　師</td><td>建議學生選讀：$tutor_radio</td><td>$tutor_memo</td></tr>
									<tr><td align='center'>輔導教師</td><td>建議學生選讀：$guidance_radio</td><td>$guidance_memo</td></tr>";
					$content_list.="</table><br>";
					
					$res->MoveNext();
				}
			} else { $act=''; $content_list="<br><br><br><center><font size=4 color='#ff0000'>未發現任何{$menu_arr[$menu]}紀錄！ <input type='submit' name='go' value='按我新增'><br></font></center>";	}
			$showdata.=$content_list;

			break;
	}
}

$main="<font size=2><form method='post' action='$_SERVER[SCRIPT_NAME]' name='myform'><table style='border-collapse: collapse; font-size=12px;'><tr><td valign='top'>$class_select<br>$student_select</td><td valign='top'>$memu_select $showdata $act</td></tr></table></form></font>";

echo $main;

foot();

function array_csort() {
	$args = func_get_args(); 
	$marray = array_shift($args); 
	$i=0; 
	$msortline = "return(array_multisort("; 
	foreach ($args as $arg) { 
		if (is_string($arg)) { 
			foreach ($marray as $row) { 
				$sortarr[$i][] = $row[$arg]; 
			} 
		} else { 
			$sortarr[$i] = $arg; 
		} 
		$msortline .= "\$sortarr[".$i."],"; 
		$i++; 
	} 
	$msortline .= "\$marray));"; 
	
	eval($msortline); 
	return $marray; 
} 

?>
