<?php

// $Id:  $

//取得設定檔
include_once "config.php";

sfs_check();

// 健保卡查核
switch ($ha_checkary){
        case 2:
                ha_check();
                break;
        case 1:
                if (!check_home_ip()){
                        ha_check();
                }
                break;
}


//秀出網頁
head("我的成長故事");

//模組選單
print_menu($menu_p);

//檢查是否開放
if (!$mystory){
   echo "模組變數尚未開放本功能，請洽詢學校系統管理者！";
   exit;
}

//取得目前學年度
$curr_year=curr_year();
$curr_seme=curr_seme();

//目前選定學期
$seme_year_seme=sprintf('%03d%1d',$curr_year,$curr_seme);
$student_sn=$_SESSION['session_tea_sn'];
$stud_name=$_SESSION['session_tea_name'];

$menu=$_POST['menu'];

$min=$IS_JHORES?7:4;
$max=$IS_JHORES?9:6;


//抓取學生本學期就讀班級
$query="select * from stud_seme where student_sn=$student_sn and seme_year_seme='$seme_year_seme'";
$res=$CONN->Execute($query);
$seme_class=$res->fields['seme_class'];
$seme_class_name=$res->fields['seme_class_name'];
$seme_num=$res->fields['seme_num'];
$stud_grade=substr($seme_class,0,-2);

//儲存紀錄處理
if($_POST['go']=='儲存紀錄'){
	switch($menu){
		case 1:
			$personality=serialize($_POST['personality']);
			$interest=serialize($_POST['interest']);
			$specialty=serialize($_POST['specialty']);
			//檢查是否已有舊紀錄
			$query="select sn from career_mystory where student_sn=$student_sn";
			$res=$CONN->Execute($query) or die("SQL錯誤:$query");
			$sn=$res->fields[0];
			if($sn) $query="update career_mystory set personality='$personality',interest='$interest',specialty='$specialty' where sn=$sn";
				else $query="insert into career_mystory set student_sn=$student_sn,personality='$personality',interest='$interest',specialty='$specialty'";
			$res=$CONN->Execute($query) or die("SQL錯誤:$query");	
			break;
		case 2:
			//occupation_suggestion occupation_myown occupation_others occupation_weight
			$occupation_suggestion=serialize($_POST['suggestion']);
			$occupation_myown=serialize($_POST['myown']);
			$occupation_others=serialize($_POST['others']);
			$occupation_weight=serialize($_POST['weight']);
			
			//檢查是否已有舊紀錄
			$query="select sn from career_mystory where student_sn=$student_sn";
			$res=$CONN->Execute($query) or die("SQL錯誤:$query");
			$sn=$res->fields[0];
			if($sn) $query="update career_mystory set occupation_suggestion='$occupation_suggestion',occupation_myown='$occupation_myown',occupation_others='$occupation_others',occupation_weight='$occupation_weight' where sn=$sn";
				else $query="insert into career_mystory set student_sn=$student_sn,occupation_suggestion='$occupation_suggestion',occupation_myown='$occupation_myown',occupation_others='$occupation_others',occupation_weight='$occupation_weight'";
			$res=$CONN->Execute($query) or die("SQL錯誤:$query");	
			break;
	}
}

//產生選單
$memu_select="※我是 $stud_name ，本學期就讀班級： $seme_class ，座號： $seme_num 。 ※我要檢視或設定";
$menu_arr=array(1=>'自我認識',2=>'職業與我');
foreach($menu_arr as $key=>$title){
	$checked=($menu==$key)?'checked':''; 
	$color=($menu==$key)?'#0000ff':'#000000'; 
	$memu_select.="<input type='radio' name='menu' value='$key' $checked onclick='this.form.submit();'><b><font color='$color'>$title</font></b>";
}
//檢查是否為可填寫月份
$mystory_months="[,$mystory_months,]";
$pos=strpos($mystory_months,$curr_month,1);
switch($menu){
	case 1:
		//抓取個性、各項活動參照表
		$personality_items=SFS_TEXT('個性(人格特質)');
		$activity_items=SFS_TEXT('各項活動');
	
		//取得我的成長故事既有資料
		$query="select personality,interest,specialty from career_mystory where student_sn=$student_sn";
		$res=$CONN->Execute($query);
		
		//抓取自我認識各個項目的內容
		$personality_array=unserialize($res->fields['personality']);
		$interest_array=unserialize($res->fields['interest']);
		$specialty_array=unserialize($res->fields['specialty']);
		
		$personality_checkox="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' id='AutoNumber1' width='100%'>
		<tr bgcolor='#ccccff' align='center'><td colspan=3>個性(人格特質)</td></tr>
		<tr bgcolor='#ffcccc' align='center'><td>{$class_year[$min]}級</td><td>{$class_year[$min+1]}級</td><td>{$class_year[$min+2]}級</td></tr><tr>";
			
		$interest_checkox="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' id='AutoNumber1' width='100%'>
		<tr bgcolor='#ccccff' align='center'><td colspan=3>休閒興趣</td></tr>
		<tr bgcolor='#ffcccc' align='center'><td>{$class_year[$min]}級</td><td>{$class_year[$min+1]}級</td><td>{$class_year[$min+2]}級</td></tr><tr>";
		
		$specialty_checkox="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' id='AutoNumber1' width='100%'>
		<tr bgcolor='#ccccff' align='center'><td colspan=3>專長</td></tr>
		<tr bgcolor='#ffcccc' align='center'><td>{$class_year[$min]}級</td><td>{$class_year[$min+1]}級</td><td>{$class_year[$min+2]}級</td></tr><tr>";
		
		for($i=$min;$i<=$max;$i++){
			if($pos){
				$disabled=($career_previous or $stud_grade==$i)?'':'disabled';
				$bgcolor=($career_previous or $stud_grade==$i)?'#ffdfdf':'#cfefef';
				
				$personality_checkox.="<td bgcolor='$bgcolor'>";
				foreach($personality_items as $key=>$value){
					$color=$personality_array[$i][$key]?'#ff0000':'#000000';
					$checked=$personality_array[$i][$key]?'checked':'';
					$personality_checkox.="<input type='checkbox' name='personality[$i][$key]' value='1' $disabled $checked><font color='$color'>$value</font><br>";
					if($disabled and $checked) $personality_checkox.="<input type='hidden' name='personality[$i][$key]' value='1'>"; 
				}
				$personality_checkox.="</td>";
			} else {
				$personality_checkox.="<td>";
				foreach($personality_items as $key=>$value){
					$checked=$personality_array[$i][$key]?'■':'□';
					$personality_checkox.="$checked $value<br>";
				}
				$personality_checkox.="</td>";
			}
			
			$interest_checkox.="<td bgcolor='$bgcolor'>";
			foreach($activity_items as $key=>$value){
				if($pos){
					$color=$interest_array[$i][$key]?'#ff0000':'#000000';
					$checked=$interest_array[$i][$key]?'checked':'';
					$interest_checkox.="<input type='checkbox' name='interest[$i][$key]' value='$1' $disabled $checked><font color='$color'>$value</font><br>";
					if($disabled and $checked) $interest_checkox.="<input type='hidden' name='interest[$i][$key]' value='1'>";
				} else {
					$checked=$interest_array[$i][$key]?'■':'□';
					$interest_checkox.="$checked $value<br>";
				}
			}
			$interest_checkox.="</td>";
			
			$specialty_checkox.="<td bgcolor='$bgcolor'>";
			foreach($activity_items as $key=>$value){
				if($pos){
					$color=$specialty_array[$i][$key]?'#ff0000':'#000000';
					$checked=$specialty_array[$i][$key]?'checked':'';
					$specialty_checkox.="<input type='checkbox' name='specialty[$i][$key]' value='1' $disabled $checked><font color='$color'>$value</font><br>";
					if($disabled and $checked) $specialty_checkox.="<input type='hidden' name='specialty[$i][$key]' value='1'>";
				} else {
					$checked=$specialty_array[$i][$key]?'■':'□';
					$specialty_checkox.="$checked $value<br>";
				}
			}
			$specialty_checkox.="</td>";			
		}
		$personality_checkox.='</tr></table>';
		$interest_checkox.='</tr></table>';
		$specialty_checkox.='</tr></table>';		
		
		$showdata="$personality_checkox<br>$interest_checkox<br>$specialty_checkox";
		
		break;
	case 2:	
		//職業與我-問題陣列定義
		$suggestion_question=array(1=>'家人、師長或親友曾經建議我未來可選擇的職業',2=>'給我建議的人',3=>'建議我選擇這項職業的原因');
		$myown_question=array(1=>'我最感興趣的職業',2=>'我對這職業感興趣的原因',3=>'這項職業需具備的學歷、能力、專長或其他條件');
		$others_question=array(1=>'我想要進一步了解哪些職業');
		
		//抓取選擇職業時重視的條件參照表
		$weight_items=SFS_TEXT('選擇職業時重視的條件');
		
		//取得我的成長故事既有資料
		$query="select occupation_suggestion,occupation_myown,occupation_others,occupation_weight from career_mystory where student_sn=$student_sn";
		$res=$CONN->Execute($query);
		
		//抓取自我認識各個項目的內容
		$suggestion_array=unserialize($res->fields['occupation_suggestion']);
		$myown_array=unserialize($res->fields['occupation_myown']);
		$others_array=unserialize($res->fields['occupation_others']);
		$weight_array=unserialize($res->fields['occupation_weight']);
		
		$suggestion_list="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' id='AutoNumber1' width='100%'>
		<tr bgcolor='#ccccff' align='center'><td colspan=4>家人、師長或親友的建議</td></tr>
		<tr bgcolor='#ffcccc' align='center'><td>問　　　　題</td><td>{$class_year[$min]}級</td><td>{$class_year[$min+1]}級</td><td>{$class_year[$min+2]}級</td></tr>";	
		foreach($suggestion_question as $key=>$value){
			$suggestion_list.="<tr><td>$key. $value</td>";
			for($i=$min;$i<=$max;$i++){
				$mydata=$suggestion_array[$i][$key];
				if($pos) {
					if($career_previous or $stud_grade==$i)	$suggestion_list.="<td bgcolor='#ffdfdf'><input type='text' name='suggestion[$i][$key]' value='$mydata'></td>";
						else $suggestion_list.="<td bgcolor='#cfefef'>$mydata<input type='hidden' name='suggestion[$i][$key]' value='$mydata'></td>";
				} else {
					$suggestion_list.="<td bgcolor='#eeeeee'>$mydata</td>";
				}
			}
			$suggestion_list.='</tr>';		
		}
		$suggestion_list.='</table><br>';	
		
		
		$myown_list="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' id='AutoNumber1' width='100%'>
		<tr bgcolor='#ccccff' align='center'><td colspan=4>我最感興趣的職業</td></tr>
		<tr bgcolor='#ffcccc' align='center'><td>問　　　　題</td><td>{$class_year[$min]}級</td><td>{$class_year[$min+1]}級</td><td>{$class_year[$min+2]}級</td></tr>";	
		foreach($myown_question as $key=>$value){
			$myown_list.="<tr><td>$key. $value</td>";
			for($i=$min;$i<=$max;$i++){
				$mydata=$myown_array[$i][$key];
				if($pos) {
					if($career_previous or $stud_grade==$i)	$myown_list.="<td bgcolor='#ffdfdf'><input type='text' name='myown[$i][$key]' value='$mydata'></td>";
					else $myown_list.="<td bgcolor='#cfefef'>$mydata<input type='hidden' name='myown[$i][$key]' value='$mydata'></td>";
				} else {
					$myown_list.="<td bgcolor='#eeeeee'>$mydata</td>";
				}
			}
			$myown_list.='</tr>';		
		}
		$myown_list.='</table><br>';
		
		
		$others_list="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' id='AutoNumber1' width='100%'>
		<tr bgcolor='#ccccff' align='center'><td colspan=4>我想要進一步了解的職業</td></tr>
		<tr bgcolor='#ffcccc' align='center'><td>問　　　　題</td><td>{$class_year[$min]}級</td><td>{$class_year[$min+1]}級</td><td>{$class_year[$min+2]}級</td></tr>";	
		foreach($others_question as $key=>$value){
			$others_list.="<tr><td>$key. $value</td>";
			for($i=$min;$i<=$max;$i++){
				$mydata=$others_array[$i][$key];
				if($pos) {
					if($career_previous or $stud_grade==$i)	$others_list.="<td bgcolor='#ffdfdf'><input type='text' name='others[$i][$key]' value='$mydata'></td>";
					else $others_list.="<td bgcolor='#cfefef'>$mydata<input type='hidden' name='others[$i][$key]' value='$mydata'></td>";
				} else {
					$others_list.="<td bgcolor='#eeeeee'>$mydata</td>";
				}
			}
			$others_list.='</tr>';		
		}
		$others_list.='</table><br>';
		
		//重視條件　（與上面程式結構不同）
		$weight_list="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' id='AutoNumber1' width='100%'>
			<tr bgcolor='#ccccff' align='center'><td colspan=4>選擇職業時，我重視的條件(可複選)</td></tr>
			<tr bgcolor='#ffcccc' align='center'><td width=200>填寫說明</td><td>{$class_year[$min]}級</td><td>{$class_year[$min+1]}級</td><td>{$class_year[$min+2]}級</td></tr>
			<tr>
			<td valign='top' width=300>
			<li>進行生涯規劃時，應澄清與瞭解個人特質，搜集學校與職業資料，同時考量家人意見、社會與環境變遷、各項助力阻力因素等。</li>
			<li>在個人特質的澄清與瞭解方面，除了興趣、能力外，工作價值觀（個人重視的條件）也是重要影響因素。</li>
			<li>（八、九年級填寫）</li>	
			</td>";
		
		for($i=$min;$i<=$max;$i++){
			$disabled=($career_previous or $stud_grade==$i)?'':'disabled';
			$bgcolor=($career_previous or $stud_grade==$i)?'#ffdfdf':'#cfefef';
			$bgcolor=$pos?$bgcolor:'#eeeeee';
			$weight_list.="<td bgcolor='$bgcolor'>";
			foreach($weight_items as $key=>$value){
				if($pos) {
					$color=$weight_array[$i][$key]?'#ff0000':'#000000';
					$checked=$weight_array[$i][$key]?'checked':'';
					$weight_list.="<input type='checkbox' name='weight[$i][$key]' value='1' $disabled $checked><font color='$color'>$value</font><br>";
					if($disabled and $checked) $weight_list.="<input type='hidden' name='weight[$i][$key]' value='1'>";
				} else {
					$checked=$specialty_array[$i][$key]?'■':'□';
					$weight_list.="$checked $value<br>";
				}
			}
		}
		$weight_list.="</td></tr></table>";		
		
		$showdata=$suggestion_list.$myown_list.$others_list.$weight_list;
		
		break;	
}

$act=($menu and $pos)?"<center><input type='submit' value='儲存紀錄' name='go' onclick='return confirm(\"確定要\"+this.value+\"?\")' style='border-width:1px; cursor:hand; color:white; background:#5555ff; font-size:20px; height=42'></center>":"◎學校設定可填寫月份：$m_arr[mystory_months]";

$main="<font size=2><form method='post' action='$_SERVER[SCRIPT_NAME]' name='myform'>$memu_select $showdata<br>$act</form></font>";

echo $main;

foot();

?>
