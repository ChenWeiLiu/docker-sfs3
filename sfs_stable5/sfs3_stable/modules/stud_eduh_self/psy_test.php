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
head("各項心理測驗");

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
$seme_year_seme=sprintf('%03d%d',$curr_year,$curr_seme);
$student_sn=$_SESSION['session_tea_sn'];
$stud_name=$_SESSION['session_tea_name'];

$menu=$_POST['menu'];

if($_POST['go']=='儲存紀錄'){
	$highest_str=implode(',',$_POST['highest']);
	$query="update career_test set study='{$_POST['study']}',job='{$_POST['job']}',highest='$highest_str' where sn={$_POST['sn']}";
	$res=$CONN->Execute($query) or die("SQL錯誤:$query");
}


//抓取學生本學期就讀班級
$query="select * from stud_seme where student_sn=$student_sn and seme_year_seme='$seme_year_seme'";
$res=$CONN->Execute($query);
$seme_class=$res->fields['seme_class'];
$seme_class_name=$res->fields['seme_class_name'];
$seme_num=$res->fields['seme_num'];
$stud_grade=substr($seme_class,0,-2);

//產生選單
$memu_select="※我是 $stud_name ，本學期就讀班級： $seme_class ，座號： $seme_num 。<br>※我要檢視";
$menu_arr=array(1=>'性向測驗',2=>'興趣測驗',3=>'其他測驗(1)',4=>'其他測驗(2)');
foreach($menu_arr as $key=>$title){
	$checked=($menu==$key)?'checked':''; 
	$color=($menu==$key)?'#0000ff':'#000000'; 
	$memu_select.="<input type='radio' name='menu' value='$key' $checked onclick='this.form.submit();'><b><font color='$color'>$title</font></b>";
}

if($menu){
	//取得性向測驗既有資料
	$query="select * from career_test where student_sn=$student_sn and id=$menu";
	$res=$CONN->Execute($query);
	if($res->RecordCount()){
		while(!$res->EOF){
			$sn=$res->fields['sn'];
			$content=unserialize($res->fields['content']);

			$title=$content['title'];
			$test_result=$content['data'];
			$study="<input type='text' name='study' value='{$res->fields['study']}'>";
			$job="<input type='text' name='job' value='{$res->fields['job']}'>";
			$highest_arr=explode(',',$res->fields['highest']);
			for($i=0;$i<3;$i++){
				$highest.="<input type='text' name='highest[$i]' size=10 value='{$highest_arr[$i]}'> ";
			}
			$content_list="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' id='AutoNumber1'>
					<tr bgcolor='#ccffcc' align='center'><td colspan=2><b>$title</b></td></tr><tr></tr>
					<tr bgcolor='#ffcccc' align='center'><td>項目</td><td>內容結果</td></tr>";
			if($test_result){
				foreach($test_result as $key=>$value) $content_list.="<tr><td>$key</td><td align='center'>$value</td></tr>";
			} else $content_list.="<tr align='center'><td colspan=2 height=100>沒有發現任何分項紀錄！</td></tr>";
			$content_list.="<tr bgcolor='#fcccfc'><td colspan=2>
			●分數最高的3項分測驗： $highest<br>
			●根據測驗結果，在升學方面，我適合就讀： $study<br>
			●根據測驗結果，在就業方面，我適合從事： $job</td></tr>";
			$content_list.="<tr bgcolor='#cffccc'><td colspan=2 align='center'><input type='hidden' name='sn' value='$sn'>
						<input type='submit' value='儲存紀錄' name='go' onclick='return confirm(\"確定要\"+this.value+\"?\")' style='border-width:1px; cursor:hand; color:white; background:#5555ff; font-size:20px; height=42'>
					</td></tr>";
			$content_list.="</table><br>";
			
			$res->MoveNext();
		}
	} else $content_list="<center><font size=5 color='#ff0000'><br><br>未發現任何{$menu_arr[$menu]}紀錄！<br><br></font></center>";
}

$main="<font size=2><form method='post' action='$_SERVER[SCRIPT_NAME]' name='myform'>$memu_select $content_list</form></font>";

echo $main;

foot();

?>
