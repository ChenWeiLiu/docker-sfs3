<?php

// $Id: $

/*引入學務系統設定檔*/
include "config.php";
require_once "../../include/sfs_case_excel.php";

//使用者認證
sfs_check();

if($_POST['act'])
{
	//抓取選擇班級
	if($_POST['class_selected']) {
		$investigate_sn=$_POST['target_sn'];
		if($investigate_sn) {	
			//抓取欄位資料
			$sql="SELECT * FROM investigate WHERE sn=$investigate_sn";
			$rs=$CONN->Execute($sql) or die("無法取得已經開列的項目資料!<br>$sql");
			$title=$rs->fields['title'];
			$fields=explode("\r\n",$rs->fields['fields']);
			$selections=explode("\r\n",$rs->fields['selections']);

			
			foreach($_POST['class_selected'] as $key=>$class_id) {				
				//抓取指定班級學生SN
				$student_data=array();
				$item_data=array();
				$class_id_list.="$class_id ";
				$query="SELECT student_sn,curr_class_num,stud_name,stud_sex FROM stud_base WHERE stud_study_cond=0 AND curr_class_num LIKE '$class_id%' ORDER BY curr_class_num";
				$res=$CONN->Execute($query);
				$sn_list='';
				while(! $res->EOF) {
					$student_sn = $res->fields['student_sn'];
					$student_data[$student_sn]['no']=substr($res->fields['curr_class_num'],-2);
					$student_data[$student_sn]['name']=$res->fields['stud_name'];
					$student_data[$student_sn]['sex']=$res->fields['stud_sex']=='1'?'男':'女';
					$sn_list .= "{$student_sn},";
					$res->MoveNext();
				}
				$sn_list=substr($sn_list,0,-1);
				//echo $sn_list;
				
				switch($_POST['act']) {
					case 'list':  //班級資料列表						
						//抓取已經填報的資料
						$query="SELECT student_sn,field,value,memo FROM investigate_record WHERE investigate_sn='$investigate_sn' AND student_sn IN ($sn_list)";
						$res=$CONN->Execute($query);

						while(! $res->EOF) {
							$student_sn = $res->fields['student_sn'];
							$field = $res->fields['field'];
							//if (array_key_exists($student_sn, $student_data)) {
								$student_data[$student_sn][$field]['value'] = $res->fields['value'];
								$student_data[$student_sn][$field]['memo'] = $res->fields['memo'];
							//}
							$res->MoveNext();
						}
						
						//顯示資料
						$main="◎調查項目：$title ◎班級：$class_id ";
						$main.="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width=100%>
								<tr bgcolor='#CCCCFF' align='center'><td width='60px'>座號</td><td width='120px'>姓名</td><td width='60px'>性別</td>";
						foreach($fields as $k=>$v) $main.="<td>$v</td>";
						$main.="<td width='200px'>備註</td></tr>";
						foreach( $student_data as $key => $value ) {
							$row_bg=$value['sex']=='男'?'#ddffdd':'#ffdddd';
							$main.="<tr align='center' bgcolor='$row_bg'><td>{$value['no']}</td><td>{$value['name']}</td><td>{$value['sex']}</td>";
							foreach($fields as $k=>$v) {
								$main.="<td>{$value[$v][value]}</td>"; // {$value[$v][memo]}
							}
							$main.="<td></td></tr>";
						}
						$main.="</table>";
							
						echo $main.$page_break;
						break;
					case 'item':  //選項人員名單
						//抓取已經填報的資料
						$query="SELECT student_sn,field,value,memo FROM investigate_record WHERE investigate_sn='$investigate_sn' AND student_sn IN ($sn_list)";
						$res=$CONN->Execute($query);

						while(! $res->EOF) {
							$student_sn = $res->fields['student_sn'];
							$field = $res->fields['field'];
							//if (array_key_exists($student_sn, $student_data)) {
								$student_sn = $res->fields['student_sn'];
								$value=$res->fields['value'];
								if($value) {
									//班級
									$item_data[$field][$value] .= "({$student_data[$student_sn]['no']}) {$student_data[$student_sn]['name']}<br>";
									//班級累加
									if(count($_POST['class_selected'])>1) $total_data[$field][$value] .= "($class_id-{$student_data[$student_sn]['no']}) {$student_data[$student_sn]['name']}<br>";
								}
							//}
							$res->MoveNext();
						}

						//顯示班級資料
						$main="◎調查項目：$title ◎班級：$class_id ";
						$main.="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width=100%>
								<tr bgcolor='#CCCCFF' align='center'><td>選項</td>";
						foreach($fields as $k=>$v) $main.="<td>$v</td>";
						$main.="</tr>";
						
						foreach( $selections as $key => $value ) {
							$main.="<tr><td align='center'>$value</td>";
							foreach($fields as $k=>$v) {
								$main.="<td valign='top'>{$item_data[$v][$value]}</td>";
							}
							$main.="</tr>";
						} 
						$main.="</table>";
						echo $main.$page_break;
						break;
				}
			}
			//顯示所有選定班級資料
			if(count($_POST['class_selected'])>1 && $_POST['act']=='item') {
				$main="<center><h2>※ $title ※</h2></center><p align='right'>◎班級：$class_id_list</p>";
				$main.="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width=100%>
						<tr bgcolor='#CCCCFF' align='center' height='30px'><td>選項</td>";
				foreach($fields as $k=>$v) $main.="<td>$v</td>";
				$main.="</tr>";
				
				foreach( $selections as $key => $value ) {
					$main.="<tr><td align='center'>$value</td>";
					foreach($fields as $k=>$v) {
						$main.="<td valign='top'>{$total_data[$v][$value]}</td>";
					}
					$main.="</tr>";
				} 
				$main.="</table>";
				echo $main;
			}
		}
	}  else echo "<h1>未選定班級，無法輸出資料！</h1>";
} else {
	
//秀出網頁
head("調查資料匯出");
print_menu($MENU_P);	
	echo "<script>
		function tagall(status) {
		  var i =0;
		  while (i < document.myform.elements.length)  {
			if (document.myform.elements[i].name=='class_selected[]') {
			  document.myform.elements[i].checked=status;
			}
			i++;
		  }
		}
		</script>";

	//抓取已經開列的樣式資料
	$myself=$_POST['myself']?"and update_name='$my_title$my_name'":'';
	$saved_format="<table STYLE='font-size: x-small' border='1' cellpadding=5 cellspacing=0 style='border-collapse: collapse' bordercolor='#111111' width='100%'>
	<tr bgcolor='#CCCFFC'>
		<td align='center'>項目名稱</td>
		<td align='center'>調查欄位列表</td>
		<td align='center'>備註</td>
		<td align='center'>導師可視</td>
		<td align='center'>填報期間</td>
		<td align='center'>設定者</td>
		<td align='center'>更新日期</td>
		<td align='center'>動作<input type='hidden' name='target_sn' value='{$_POST['target_sn']}'><input type='hidden' name='act' value=''></td>
	</tr>";
	$sql="select * from investigate where room='$my_room' $myself order by update_datetime desc;";
	$rs=$CONN->Execute($sql) or die("無法取得已經開列的樣式資料!<br>$sql");
	while(!$rs->EOF) {
		$target_sn=$rs->fields['sn'];
		if($rs->fields['update_name']==$my_name) $myselef_color='#FCBFFC'; else $myselef_color='#FFFFFF';
		$saved_format.="<tr bgcolor='$myselef_color'>
							<td align='center'>{$rs->fields['title']}</td>
							<td>{$rs->fields['fields']}</td><td align='center'>{$rs->fields['memo']}</td>
							<td align='center'>{$rs->fields['visible']}</td>
							<td align='center'>{$rs->fields['start']} ~ {$rs->fields['end']}</td>
							<td align='center'>{$rs->fields['update_name']}</td>
							<td align='center'>{$rs->fields['update_datetime']}</td>
							<td align='center'>
								<input type='button' value='班級資料列表' onclick='this.form.target_sn.value=\"$target_sn\"; this.form.act.value=\"list\"; this.form.target=\"_blank\"; this.form.submit();'>
								<input type='button' value='名單結果匯整' onclick='this.form.target_sn.value=\"$target_sn\"; this.form.act.value=\"item\"; this.form.submit();'>
							</td>
						</tr>";
		$rs->MoveNext();
	}
	
	$myself="<input type='checkbox' name='myself' value='ON'".($_POST['myself']?' checked':'')." onclick='this.form.target_sn.value=\"\"; this.form.act.value=\"\"; this.form.action=\"$_SERVER[SCRIPT_NAME]\"; this.form.target=\"_self\"; this.form.submit();'>只列示我設定的樣式";
	$saved_format.='</table>';
	
	//抓取指定學年班級
	$class_list="<table STYLE='font-size: x-small' border='1' cellpadding=5 cellspacing=0 style='border-collapse: collapse' bordercolor='#111111' width='100%'>
				<tr bgcolor='#FFCCCC'><td align='center'>選定要輸出資料的班級 <input type='checkbox' name='tag' onclick='javascript:tagall(this.checked);'>全選</td></tr><tr><td>";
				
	$curr_year=curr_year();
	$curr_seme=curr_seme();
	$sql="select * from school_class where enable=1 and year='$curr_year' and semester='$curr_seme' order by c_year,c_sort;";
	$rs=$CONN->Execute($sql) or die("無法取得{$curr_year}學年度第{$curr_seme}學期的班級資料!<br>$sql");
	while(!$rs->EOF)
	{
		$class_id=sprintf('%0d%02d',$rs->fields['c_year'],$rs->fields['c_sort']);
		$class_name=$class_name_arr[$class_id];
		$class_list.="<input type='checkbox' name='class_selected[]' value='$class_id'>$class_name ";
		$rs->MoveNext();
	}
	$class_list.="</td></tr></table>";
		
	echo "<form name='myform' method='post' action='$_SERVER[SCRIPT_NAME]'>$nature_radio<hr>$class_list<br>$myself $saved_format</form>";
	foot();
}

?>
