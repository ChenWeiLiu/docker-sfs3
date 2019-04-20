<?php
//$Id: bad_login.php 5310 2009-01-10 07:57:56Z hami $
include "config.php";

//認證
sfs_check();

$query="SELECT count(*) as counter FROM `login_log_new`";
$res=$CONN->Execute($query);

$detail_list=50;
$pages=ceil($res->fields[counter]/$detail_list);
for($i=1;$i<=$pages;$i++) $pages_array[$i]=$detail_list;
$curr_page=$_POST[curr_page];
$offset=$detail_list*$_POST[curr_page];;

$query="SELECT a.login_time,a.who,b.name,a.ip,a.teacher_sn FROM `login_log_new` a left join teacher_base b on a.teacher_sn=b.teacher_sn order by login_time desc limit $offset,$detail_list";
//echo $query;

$res=$CONN->Execute($query);
$smarty->assign("rowdata",$res->GetRows());

$smarty->assign("detail_list",$detail_list);
$smarty->assign("pages_array",$pages_array);
$smarty->assign("curr_page",$curr_page);
$smarty->assign("curr_no",$offset+1);
$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
$smarty->assign("module_name","登入成功記錄");
$smarty->assign("SFS_MENU",$school_menu_p);
$smarty->display("system_login_rec.tpl");
?>
