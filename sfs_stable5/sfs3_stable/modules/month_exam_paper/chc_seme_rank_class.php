<?php
// $Id: chc_seme_rank_class.php 5310 2009-01-10 07:57:56Z hami $
// 引入 SFS3 的函式庫
//include "../../include/config.php";

// 引入您自己的 config.php 檔
@ini_set('error_reporting','E_ALL & ~E_NOTICE');
require "config.php";
include "chc_seme_advance_class.php";

// 認證
sfs_check();

	//轉換成全域變數
$act=($_POST['act'])?"{$_POST['act']}":"{$_GET['act']}";
$test_sort=($_POST['test_sort'])?"{$_POST['test_sort']}":"{$_GET['test_sort']}";
$class_num=($_POST['class_num'])?"{$_POST['class_num']}":"{$_GET['class_num']}";

//程式使用的Smarty樣本檔
$template_file = dirname (__file__)."/templates/chc_seme_rank_class.htm";


// 叫用 SFS3 的版頭
head("月考成績進退步查詢");

// 您的程式碼由此開始
print_menu($menu_p);
$curr_year = curr_year();
$curr_seme = curr_seme();

//由teacher_sn找出他是哪一班的導師
$class_num=get_teach_class();
if($class_num){
   $class_id=sprintf("%03d",$curr_year)."_".$curr_seme."_".sprintf("%02d_%02d",substr($class_num,0,-2),substr($class_num,-2,2));
   //debug_msg("第".__LINE__."行 class_id ", $class_id);
   //建立物件
   $obj= new chc_seme_advance_class($CONN,$smarty);
   //初始化
   $obj->init();

   
   $obj->process($class_id);
   //顯示內容
   $obj->display($template_file);

   // SFS3 的版尾
   foot();

}else{
   $main="<table cellspacing=1 cellpadding=6 border=0 bgcolor='#FFF829' width='80%' align='center'><tr><td align='center'><h1><img src='images/caution.png' align='middle' border=0> 警告</h1></font></td></tr><tr><td align='center' bgcolor='#FFFFFF' width='90%'>".$_SESSION['session_tea_name']."您不具導師身份！ 無法進行操作！<br>若有疑問請檢查『教師管理』的任職資料。</td></tr></table>";

	//設定主網頁顯示區的背景顏色
	$back_ground="
		<table cellspacing=1 cellpadding=6 border=0 bgcolor='#B0C0F8' width='100%'>
			<tr bgcolor='#FFFFFF'>
				<td>
					$main
				</td>
			</tr>
		</table>";
	echo $back_ground;
}

function debug_msg($title, $showarry){
	echo "<pre>";
	echo "<br>$title<br>";
	print_r($showarry);
}

?>
