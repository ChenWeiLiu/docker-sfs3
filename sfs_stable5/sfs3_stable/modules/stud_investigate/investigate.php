<?php
// $Id: setkind.php 6947 2012-10-18 06:07:01Z infodaes $

include_once "config.php";
sfs_check();

//秀出網頁
head("學生資料調查-導師填報");

//橫向選單標籤
echo print_menu($MENU_P);


//儲存更新
if($_POST['go']=='儲存') {
	$investigate_sn=$_POST['sn'];
	$datas='';
	$i=0;
	foreach($_POST['data'] as $student_sn => $v) {
		$i++;
		foreach($v as $field => $value) {
			$datas.="($investigate_sn,$student_sn,'$field','$value'),";
		}
	}
	$datas=substr($datas,0,-1);
	
	$sql="REPLACE INTO investigate_record(investigate_sn,student_sn,field,value) VALUES $datas";
	if($CONN->Execute($sql)) echo "<b>已於 ".date('Y-m-d H:i:s')." 新增/更新 了 $i 位學生的資料！</b><br>"; else die("無法執行SQL指令！<br><br>$sql");	
}


//取得任教班級代號
$class_num = get_teach_class();
$SCRIPT_FILENAME = $_SERVER['SCRIPT_FILENAME'];

if(checkid($SCRIPT_FILENAME,1) OR $class_num) {
	//抓取填報項目
	$query="SELECT * FROM investigate WHERE visible='Y' AND (CURDATE() BETWEEN `start` AND `end`) ORDER BY end DESC";
	$res=$CONN->Execute($query);	
	if($res->recordCount()) {
		
		//目標班級
		$stud_class=$_POST['stud_class'];
		$class_id_arr=explode('_',$stud_class);
		$class_id=sprintf('%d%02d',$class_id_arr[2],$class_id_arr[3]);
		if(checkid($SCRIPT_FILENAME,1)) {
			$class_list=get_class_select(curr_year(),curr_seme(),"","stud_class","this.form.submit",$stud_class);		
		} else {
			$class_id=$class_num;
			$class_list="$class_num<input type='hidden' name='stud_class' value='$stud_class'>";
		}
	
		$items="◎填報班級：$class_list  ◎填報項目：<select name='sn' onchange='this.form.submit()'><option value=></option>";
		while(! $res->EOF) {
			//產生列表
			if($res->fields['sn'] == $_POST['sn']) {
				$selected = 'selected';
				$fields=explode("\r\n",$res->fields['fields']);			
				$selections=explode("\r\n",$res->fields['selections']);			
			} else $selected='';
			$selected = ($res->fields['sn'] == $_POST['sn']) ? 'selected' : '';
			$items.="<option value='{$res->fields['sn']}' $selected>{$res->fields['start']}~{$res->fields['end']} {$res->fields['title']}</option>";		
			$res->MoveNext();
		}
		$items.="</select>";
		
		if($_POST['sn']) {
			//抓取任教學生SN
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
			
			//抓取已經填報的資料
			$query="SELECT student_sn,field,value,memo FROM investigate_record WHERE investigate_sn='$_POST[sn]' AND student_sn IN ($sn_list)";
			$res=$CONN->Execute($query);

			while(! $res->EOF) {
				$student_sn = $res->fields['student_sn'];
				$field = $res->fields['field'];
				if (array_key_exists($student_sn, $student_data)) {
					$student_data[$student_sn][$field]['value'] = $res->fields['value'];
					$student_data[$student_sn][$field]['memo'] = $res->fields['memo'];
				}
				$res->MoveNext();
			}
			
			//顯示資料
			$main.="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width=100%>
					<tr bgcolor='#FFCCCC' align='center'><td width='60px'>座號</td><td width='120px'>姓名</td><td width='60px'>性別</td>";
			foreach($fields as $k=>$v) $main.="<td>$v</td>";
			$main.="</tr>";
			foreach( $student_data as $key => $value ) {
				$row_bg=$value['sex']=='男'?'#ccffcc':'#ffcccc';
				$main.="<tr align='center' bgcolor='$row_bg'><td>{$value['no']}</td><td>{$value['name']}</td><td>{$value['sex']}</td>";
				foreach($fields as $k=>$v) {
					//選項
					$options="<select name='data[$key][$v]' style='width:99%;'><option></option>";
					foreach($selections as $i => $s) {
						$selected = ($value[$v][value] == $s) ? 'selected' : '';
						$bgcolor=$selected?"style='background-color: #ffcccc;'":'';
						$options.="<option value='$s' $selected $bgcolor>$s</option>";
					}
					$options.="</select>";
					//$main.="<td><input type='text' name='student_sn[$key]' value='{$value[$v][value]}' style='width:100%;'></td>"; // {$value[$v][memo]}
					$bgcolor=$value[$v][value]?"style='background-color: #9999ff;'":'';
					$main.="<td $bgcolor>$options</td>"; // {$value[$v][memo]}
				}
				$main.="</tr>";
			}
			$main.="</table><p align='center'>
					<input type='submit' name='go' value='儲存' onclick='return confirm(\"真的要儲存?\")'>
					<input type='reset' name='clear' value='清除重設'>
				</p>";
		}
		echo "<form name='investigate' method='post' action='$_SERVER[PHP_SELF]'>$items $main </form>";
	} else echo "<h2><center><BR><BR><font color=#FF0000>目前無可填報項目！</font></center></h2>";
} else {
	echo "<h2><center><BR><BR><font color=#FF0000>您並非導師或模組管理員，無法進行填報！</font></center></h2>";
} 
foot();
?>
