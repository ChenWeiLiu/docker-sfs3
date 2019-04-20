<?php

// $Id:  $

//取得設定檔
include_once "config.php";

sfs_check();

//秀出網頁
head("各項心理測驗資料匯入");

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

//產生選單
$memu_select="※要匯入的類別：";
$menu_arr=array(1=>'性向測驗',2=>'興趣測驗',3=>'其他測驗(1)',4=>'其他測驗(2)');
foreach($menu_arr as $key=>$title){
	$checked=($menu==$key)?'checked':''; 
	$color=($menu==$key)?'#0000ff':'#000000'; 
	$memu_select.="<input type='radio' name='menu' value='$key' $checked onclick='this.form.submit();'><b><font color='$color'>$title</font></b>";
}
if(checkid($_SERVER['SCRIPT_FILENAME'],1)) {
	if($menu){	
		//上傳解析			
		$item_default[1]=$guidance_title;
		$item_default[2]=$interest_title;
		$item_default[3]=$other_title;

		$item_radio="※測驗名稱：<input type='text' name='title' value='{$item_default[$menu]}' size=60><input type='submit' name='go' value='匯入'><br><br>※測驗結果(自EXCEL複製貼上)：";
		$import_data="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#119911' width=100%>
			<tr><td>$item_radio<br><textarea name='content' style='border-width:1px; color:brown; background:$bgcolor; font-size:11px; width=100%; height=360;'></textarea></td></tr></table>";
	}
} else $import_data="<center><font size=5 color='#ff0000'><br><br>您不具有系統管理權，無法使用本功能！<br><br></font></center>";

$main="<font size=2><form method='post' action='$_SERVER[SCRIPT_NAME]' name='myform'>$memu_select $import_data</form></font>";

echo $main;

foot();

?>
