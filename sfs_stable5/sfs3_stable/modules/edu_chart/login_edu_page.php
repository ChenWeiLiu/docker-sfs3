<?php
//$Id: login_edu_page.php 5310 2009-01-10 07:57:56Z hami $
include "config.php";

//認證
sfs_check();

//if ($_POST[chart_no]=="") $_POST[chart_no]=2;
if ($_POST[chart_no]!="") {
	if (file_exists($temp_path."url.txt")) {
		$fp=fopen($temp_path."url.txt","r");
		$i=1;
		while(!feof($fp)) {
			$urls[$i]=fgets($fp,1024);
			$i++;
		}
	}
	$smarty->assign("replace_url",trim($urls[$_POST[chart_no]]));
}

//表別選單
$sel1 = new drop_select();
$sel1->s_name="chart_no";
$sel1->id= $_POST[chart_no];
$sel1->arr = array("2"=>"表二: 學生年齡別","3"=>"表三: 班級數","4"=>"表四: 學生裸視視力","5"=>"表五: 原住民學生統計","6"=>"表六: 僑生統計");
$sel1->has_empty = true;
$sel1->is_submit = true;
$smarty->assign("chart_sel",$sel1->get_select());

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
$smarty->assign("SFS_PATH_HTML",$SFS_PATH_HTML);
$smarty->assign("module_name","『定期公務報表』網路填報作業登入");
$smarty->display("edu_chart_login_edu_page.tpl");
?>
