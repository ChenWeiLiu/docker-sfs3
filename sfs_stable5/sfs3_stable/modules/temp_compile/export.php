<?php

// $Id:$

/*引入學務系統設定檔*/
require "config.php";

//使用者認證
sfs_check();

require_once "../../include/sfs_case_excel.php";
if ($_GET['mode']==2) $str = 'and sure_study=\'1\'';
$x=new sfs_xls();
$x->setUTF8();
$x->addSheet('Sheet1');
$x->setRowText(array('姓名','身分證號'));
$sql="select * from new_stud where class_year='".($IS_JHORES+1)."' and stud_study_year='".(curr_year()+1)."' $str order by temp_id";
//echo $sql;exit;
$rs=$CONN->Execute($sql) or die($sql);
while(!$rs->EOF){
	$temp_arr[] = array($rs->fields['stud_name'],$rs->fields['stud_person_id']);
        $rs->MoveNext();
}
$x->items=$temp_arr;
$x->writeSheet();
$x->process();
exit;
