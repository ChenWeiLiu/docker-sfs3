<?php

// $Id:  $

//取得設定檔
include_once "config.php";

sfs_check();

//秀出網頁
head("各項心理測驗");

//模組選單
print_menu($menu_p,$linkstr);
$menu=$_POST['menu'];

//抓取學生本學期就讀班級
$query="select * from stud_seme where student_sn=$student_sn and seme_year_seme='$seme_year_seme'";
$res=$CONN->Execute($query);
$seme_class=$res->fields['seme_class'];
$seme_class_name=$res->fields['seme_class_name'];
$seme_num=$res->fields['seme_num'];
$stud_grade=substr($seme_class,0,-2);

//儲存紀錄處理
if($_POST['go']=='匯入'){
	$content=explode("\r\n",$_POST['content']);
	foreach($content as $key=>$value){
		$student_data=explode("\t",$value);
		if($key){
			foreach($student_data as $stud_key=>$stud_value) $realdata['data'][$title[$stud_key]]=$stud_value;
		} else $title=$student_data;
		//抓取student_sn
		$stud_id=$realdata['data']['學號'];
		$realdata['title']=$_POST['title'];
		if($stud_id){
			$query="select student_sn from stud_seme where stud_id='$stud_id' and seme_year_seme='$seme_year_seme'";		
			$res=$CONN->Execute($query) or die("SQL錯誤:$query");
			$target_sn=$res->fields[0];
			if($target_sn){
				$content=serialize($realdata);
				//檢查是否已有舊紀錄
				$query="select sn from career_test where student_sn='$target_sn' and id='$menu'";
				$res=$CONN->Execute($query) or die("SQL錯誤:$query");
				$sn=$res->fields[0];
				if($sn) $query="update career_test set id='$menu',content='$content' where sn=$sn";
				else $query="insert into career_test set student_sn='$target_sn',id='$menu',content='$content'";
				$res=$CONN->Execute($query) or die("SQL錯誤:$query");
			}
		}
	}
}


if($student_sn){

	//產生選單
	$memu_select="※我要檢視：";
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
		if($res){
			while(!$res->EOF){
				$sn=$res->fields['sn'];
				$content=unserialize($res->fields['content']);
				$title=$content['title'];
				$test_result=$content['data'];
				$study=$res->fields['study'];
				$job=$res->fields['job'];
				$highest_arr=explode(',',$res->fields['highest']);
				
				$content_list="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111'>
					<tr bgcolor='#ccffcc' align='center'><td colspan=2><b>$title</b></td></tr><tr></tr>
					<tr bgcolor='#ffcccc' align='center'><td>項目</td><td>內容結果</td></tr>";
				if($test_result){
					foreach($test_result as $key=>$value) $content_list.="<tr><td>$key</td><td align='center'>$value</td></tr>";
				} else $content_list.="<tr align='center'><td colspan=2 height=100>沒有發現任何分項紀錄！</td></tr>";
				$content_list.="<tr bgcolor='#fcfccc'><td colspan=4>分數最高的3項分測驗： (1){$highest_arr[0]} (2){$highest_arr[1]} (3){$highest_arr[2]}</td></tr>";
				$content_list.="<tr bgcolor='#fcccfc'><td colspan=4>根據測驗結果，在升學方面，我適合就讀： $study</td></tr>";
				$content_list.="<tr bgcolor='#cffccc'><td colspan=4>根據測驗結果，在就業方面，我適合從事： $job</td></tr>";
				$content_list.="</table><br>";
				
				$res->MoveNext();
			}
		} else $content_list="<center><font size=5 color='#ff0000'><br><br>未發現任何{$menu_arr[$menu]}紀錄！<br><br></font></center>";
		/*
		if(checkid($_SERVER['SCRIPT_FILENAME'],1)) {
			//上傳解析			
			$item_radio="※測驗名稱：<input type='text' name='title' value='{$item_default[$menu]}' size=60><input type='submit' name='go' value='匯入'><br>※測驗結果(自EXCEL複製貼上)：";
			$item_radio.="";
			$import_data="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#119911'>
				<tr><td>$item_radio<br><textarea name='content' style='border-width:1px; color:brown; background:$bgcolor; font-size:11px; width=100%; height=100;'></textarea></td></tr></table>";
		}
		*/
	}
}

$main="<font size=2><form method='post' action='$_SERVER[SCRIPT_NAME]' name='myform'><table style='border-collapse: collapse; font-size=12px;'><tr><td valign='top'>$class_select<br>$student_select</td><td valign='top'>$memu_select $content_list</td></tr></table>$import_data</form></font>";

echo $main;

foot();

?>
