<?php

// $Id: $

include "config.php";

sfs_check();

//選取可用年度
$sql_select="SELECT DISTINCT LEFT(class_id,5) AS semester FROM cita_data ORDER BY semester DESC";
$res=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
while(!$res->EOF) {
	$value=$res->fields[0];
	$this_semester=str_replace("_", "學年度第",$value)."學期";
	$semester_select.="<input type='checkbox' name='semester[]' value='$value'>$this_semester<br>";
	$res->MoveNext();
}


//產生群組radio
$group_array=array(1=>'全校',2=>'年級',3=>'班級');
$_POST[mode]=$_POST[mode]?$_POST[mode]:3;
foreach($group_array as $key=>$value){
	$checked=$_POST[mode]==$key?' checked':'';
	$group_radio.="<input type='radio' name='mode' value=$key $checked>$value<br>";
}

//排序數
$_POST[rank_list]=$_POST[rank_list]?$_POST[rank_list]:$rank_list;


//開始抓取記錄、計算積分
if($_POST[go]=='按此統計列示'){
	if($_POST[semester]){
		$calss_name=class_base();
		$student_data=array();
		$student_bonus=array();
		foreach($_POST[semester] as $key=>$value){
			$semester_list.="$value,";
			//抓取stud_seme學生名單
			$sql_select="SELECT a.stud_id,a.kind,a.data_get,a.bonus,b.kind_set,b.bonus_set FROM cita_data a inner join cita_kind b on a.kind=b.id WHERE order_pos>-1 and a.class_id like '$value%' ORDER BY class_id";
			$res=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
			while(!$res->EOF) {
				$stud_id=$res->fields[stud_id];
				$kind_id=$res->fields[kind];
				$data_get=$res->fields[data_get];
				//抓取此生目前就讀的年級(已經有了就不再查詢)
				if(!$student_data[$stud_id][stud_name]){
					$seme_year_seme=sprintf('%03d%d',curr_year(),curr_seme());
					$sql="SELECT a.seme_class,a.seme_num,a.student_sn,b.stud_name FROM stud_seme a inner join stud_base b on a.student_sn=b.student_sn WHERE a.seme_year_seme='$seme_year_seme' and a.stud_id='$stud_id'";
					$res2=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
					$student_data[$stud_id][student_sn]=$res2->fields[student_sn];
					$student_data[$stud_id][seme_class]=$res2->fields[seme_class];
					$student_data[$stud_id][seme_num]=$res2->fields[seme_num];
					$student_data[$stud_id][stud_name]=$res2->fields[stud_name];
				}
				if($student_data[$stud_id][stud_name])
				//因為要便利排序  所以得進行模式判斷
				switch($_POST[mode]){
					case 1:
						if($bonus_mode) $student_bonus[$stud_id]+=$res->fields[bonus]; else {
							$kind_set_arr=explode(',',$res->fields[kind_set]);
							$bonus_set_arr=explode(',',$res->fields[bonus_set]);
							$kind_key=array_search($data_get,$kind_set_arr);
							$student_bonus[$stud_id]+=$bonus_set_arr[$kind_key];
						}
						break;
					case 2:
						$grade=substr($student_data[$stud_id][seme_class],0,-2);
						if($grade) {
							if($bonus_mode) $student_bonus[$grade][$stud_id]+=$res->fields[bonus]; else {
							$kind_set_arr=explode(',',$res->fields[kind_set]);
							$bonus_set_arr=explode(',',$res->fields[bonus_set]);
							$kind_key=array_search($data_get,$kind_set_arr);
							$student_bonus[$grade][$stud_id]+=$bonus_set_arr[$kind_key];
							}
						}
						break;
					case 3:
						$seme_class=$student_data[$stud_id][seme_class];
						if($seme_class) {
							if($bonus_mode) $student_bonus[$seme_class][$stud_id]+=$res->fields[bonus]; else {
							$kind_set_arr=explode(',',$res->fields[kind_set]);
							$bonus_set_arr=explode(',',$res->fields[bonus_set]);
							$kind_key=array_search($data_get,$kind_set_arr);
							$student_bonus[$seme_class][$stud_id]+=$bonus_set_arr[$kind_key];
							}							
						}						
						break;
				}
				$res->MoveNext();
			}
		}
		$semester_list=substr($semester_list,0,-1);
		$semester_list=str_replace('_','',$semester_list);
		
		//報表標題
		$title="<center><font size=5>{$school_long_name}{$_POST[title]}</font><br><br>◎統計學期： $semester_list</center>";
		
		//統計名次人數
		$rank_count=array();

		//進行排序與列表
		switch($_POST[mode]){
			case 1:
				arsort($student_bonus);
				$rank=0;
				$rank_dup=0;
				$bonus=-1;
				$main="$title<br><table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' width='100%'>
						<tr align='center' bgcolor='#FFCCCC'><td>名次</td><td>班級</td><td>座號</td><td>學號</td><td>姓名</td><td>榮譽積分</td><td> 備　　　　註 </td></tr>";
				foreach($student_bonus as $key=>$value){
					if($value){
						if($bonus==$value) $rank_dup++; else { $rank+=$rank_dup+1; $bonus=$value; $rank_dup=0;}
						if($rank<=$_POST[rank_list]){
							$main.="<tr align='center'><td>$rank</td><td>{$calss_name[$student_data[$key][seme_class]]}</td><td>{$student_data[$key][seme_num]}</td><td>$key</td><td>{$student_data[$key][stud_name]}</td><td>$value</td><td></td></tr>";
							$rank_count[$rank]++;
						}
					}
				}
				$main.="</table>";
				echo $main;
				break;
			case 2:
				ksort($student_bonus);
				echo $title;
				foreach($student_bonus as $grade=>$grade_data){
					arsort($grade_data);
					$rank=0;
					$rank_dup=0;
					$bonus=-1;
					$main="<br>※{$class_name_kind_1[$grade]}年級<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' width='100%'>
							<tr align='center' bgcolor='#FFCCCC'><td>名次</td><td>班級</td><td>座號</td><td>學號</td><td>姓名</td><td>榮譽積分</td><td> 備　　　　註 </td></tr>";
					foreach($grade_data as $key=>$value){
						if($value){
							if($bonus==$value) $rank_dup++; else { $rank+=$rank_dup+1; $bonus=$value; $rank_dup=0;}
							if($rank<=$_POST[rank_list]){
								$main.="<tr align='center'><td>$rank</td><td>{$calss_name[$student_data[$key][seme_class]]}</td><td>{$student_data[$key][seme_num]}</td><td>$key</td><td>{$student_data[$key][stud_name]}</td><td>$value</td><td></td></tr>";
								$rank_count[$rank]++;
							}
						}
					}
					$main.="</table>";
					echo $main;
				}
				break;
			case 3:
				ksort($student_bonus);
				echo $title;
				foreach($student_bonus as $class_id=>$class_data){
					arsort($class_data);
					$rank=0;
					$rank_dup=0;
					$bonus=-1;
					$main="<br>※班級：{$calss_name[$class_id]}<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' width='100%'>
							<tr align='center' bgcolor='#FFCCCC'><td>名次</td><td>班級</td><td>座號</td><td>學號</td><td>姓名</td><td>榮譽積分</td><td> 備　　　　註 </td></tr>";
					foreach($class_data as $key=>$value){
						if($value){
							if($bonus==$value) $rank_dup++; else { $rank+=$rank_dup+1; $bonus=$value; $rank_dup=0;}
							if($rank<=$_POST[rank_list]){
								$main.="<tr align='center'><td>$rank</td><td>$calss_name[$class_id]</td><td>{$student_data[$key][seme_num]}</td><td>$key</td><td>{$student_data[$key][stud_name]}</td><td>$value</td><td></td></tr>";
								$rank_count[$rank]++;
							}
						}
					}
					$main.="</table>";
					echo $main;
				}
				break;
		}
		//列式統計數
		ksort($rank_count);
		foreach($rank_count as $key=>$count){
			$rank_row.="<td>$key</td>";
			$rank_sum.="<td>$count</td>";
			$total+=$count;
		}

		echo "<br><table border=2 cellpadding=7 cellspacing=0 style='border-collapse: collapse' bordercolor='#111111' width='100%'>
			<tr align='center' bgcolor='#CCFFCC'><td>名次</td>$rank_row<td>合計</td></tr>
			<tr align='center'><td bgcolor='#CCFFCC'>人數</td>$rank_sum<td bgcolor='#CCFFCC'>$total</td></tr></table>";
		
		exit;
	}
}

  
head("榮譽積分排行榜") ;
print_menu($menu_p);

echo <<<HERE
<script>
function tagall(status) {
  var i =0;

  while (i < document.myform.elements.length)  {
    if (document.myform.elements[i].name=='semester[]') {
      document.myform.elements[i].checked=status;
    }
    i++;
  }
}
</script>
HERE;


$main="<form name='myform' method='post' action='$_SERVER[SCRIPT_NAME]' target='_BLANK'>
<table border='1' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111'>
<tr bgcolor='#CCFFCC' align='center'><td><input type='checkbox' name='select_all' onClick='javascript:tagall(this.checked);'>學期選擇</td>
<td>群組依據</td>
<td>排行人數</td>
</tr>
<tr align='center' valign='top'><td width=180>$semester_select</td><td>$group_radio</td><td><input type='text' name='rank_list' size=3 value='{$_POST[rank_list]}'></td></tr>
<tr align='center'><td colspan=3>標題：<input type='text' name='title' value='$title_default'><input type='submit' name='go' value='按此統計列示'></td></tr>
</table></form>";

echo $main;

foot();
?>
