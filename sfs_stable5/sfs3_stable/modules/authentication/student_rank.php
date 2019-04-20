<?php

include "config.php";

sfs_check();

$select_group=$_POST['select_group'];
$select_period=$_POST['select_period'];
$rank_item=$_POST['rank_item'];
$rank_limit=$_POST['rank_limit']?$_POST['rank_limit']:5;
$curr_year_seme=sprintf('%03d%d',curr_year(),curr_seme());
$curr_year=sprintf('%03d',curr_year());

if($_POST['act']=='按我列表')
{
	//取得積點對照
	$subitem_array=array();
	$sql="SELECT * FROM authentication_subitem";
	$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
	while(!$res->EOF)
	{
		$subitem_sn=$res->fields[sn];
		$bonus=$res->fields[bonus];
		$subitem_array[$subitem_sn]=$bonus;
		$res->MoveNext();
	}

	//取得選擇時段認證紀錄
	switch($select_period){
		case 0:
			$filter="AND a.year_seme='$curr_year_seme'";
		break;
		case 1:
			$filter="AND a.year_seme like '$curr_year%'"; 
		break;
		case 2:
			$filter=''; 
		break;
	}

	$school_rank=array();
	$grade_rank=array();
	$class_rank=array();

	$sql="SELECT a.*,b.stud_name,b.curr_class_num FROM authentication_record a INNER JOIN stud_base b WHERE a.student_sn=b.student_sn AND b.stud_study_cond=0 $filter ORDER BY curr_class_num";
	$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
	while(!$res->EOF){
		$student_sn=$res->fields[student_sn];
		$subitem_sn=$res->fields[sub_item_sn];
		$class_id=substr($res->fields[curr_class_num],0,-2);
		$class_no=substr($res->fields[curr_class_num],-2);
		$grade=substr($res->fields[curr_class_num],0,-4);	
		$bonus=$rank_item?$subitem_array[$subitem_sn]:1;
		
//echo "subitem_sn bonus= ".$subitem_array[$subitem_sn]." -->	$bonus <br>";	
		
		//學生資料陣列
		$student_data[$student_sn][class_id]=$class_id;
		$student_data[$student_sn][class_no]=$class_no;		
		$student_data[$student_sn][stud_name]=$res->fields[stud_name];
		
		//依照選擇準備列表陣列
		switch($select_group){
		 case 0:
			//班級陣列
			$rank_data[$class_id][$student_sn]+=$bonus;
		  break;
		 case 1:
			 //年級陣列
			$rank_data[$grade][$student_sn]+=$bonus;
		  break;
		 case 2:
			//全校陣列
			$rank_data['全校'][$student_sn]+=$bonus; 
		  break;
		}
		$res->MoveNext();
	}
	//進行排序後輸出
	echo "<center><font size=5>$school_long_name<br>".curr_year()."學年度第".curr_seme()."學期學習認證排行榜</font>
	<table border=2 cellpadding=6 cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' width='100%'>
				<tr align='center' bgcolor='#FFAAAA'><td>名次</td><td>班級</td><td>座號</td><td>姓名</td><td>積分</td><td>　備　註　</td></tr><tr></tr>";
	foreach($rank_data as $key=>$value) {
		arsort($value);
		//echo "<br>------------------------<br>群組：$key <br>------------------------<br>";
		$rank=0;
		$student_count=0;
		$curr_bonus=-1;
		foreach($value as $sn=>$bonus) {
			//開始輸出
			if($curr_bonus==$bonus) {
				$student_count++; 
			} else { 
				$rank+=$student_count+1;
				$student_count=0;
				$curr_bonus=$bonus;
			}
			$class_name=$class_base[$student_data[$sn][class_id]];
			if($rank_limit>=$rank) echo "<tr align='center'><td>$rank</td><td>$class_name</td><td>{$student_data[$sn][class_no]}</td><td>{$student_data[$sn][stud_name]}</td><td>$bonus</td><td></td></tr>";
		}
		echo "<tr></tr>";
	}
echo "</table></center>";	

} else {

	//秀出網頁
	head("學生認證排行");

	//橫向選單標籤
	echo print_menu($MENU_P);

	$main="<form name='myform' method='post' action='$_SERVER[SCRIPT_NAME]' target='_BLANK'><br>
	※群組選擇：<input type='radio' value=0 name='select_group' checked>班級 
					<input type='radio' value=1 name='select_group'>年級
					<input type='radio' value=2 name='select_group'>全校
					<br><br>
	※時段選擇：<input type='radio' value=0 name='select_period' checked>本學期 
					<input type='radio' value=1 name='select_period'>本學年度
					<input type='radio' value=2 name='select_period'>在學期間
					<br><br>
	※排序依據：<input type='radio' value=0 name='rank_item' checked>通過項目數(每項績分為1) 
					<input type='radio' value=1 name='rank_item'>總積分(依照細目設定)
					<br><br>
	※列表人次：<input type='text' name='rank_limit' value=$rank_limit size=3> <input type='submit' name='act' value='按我列表'>"; 

	echo $main.$studentdata."</form>";
	foot();
}
?>