<?php
//$Id: borrow_data.php 5310 2009-01-10 07:57:56Z hami $
include "config.php";

//ป{รา
book_check();

$query="select a.*,b.book_name,date_format(a.out_date,'%Y-%m-%d')as out_d,date_format(DATE_ADD(a.out_date, INTERVAL 13 DAY),'%Y-%m-%d') as re_d ,to_days(curdate())-to_days(a.out_date)-13 as yet from borrow a left join book b on a.book_id=b.book_id where a.stud_id='$_SESSION[session_log_id]' order by a.out_date";
$res=$CONN->Execute($query);
$smarty->assign("data_arr",$res->GetRows());
$smarty->display("book_query_borrow_data.tpl");
?>
