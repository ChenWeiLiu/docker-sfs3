<?php

//$Id: up20061024.php 5310 2009-01-10 07:57:56Z hami $

if(!$CONN){
        echo "go away !!";
        exit;
}

//在stud_base表中增加英文姓名、戶籍遷入日期欄位
$query = "ALTER TABLE `stud_base` ADD `stud_name_eng` VARCHAR( 20 ),ADD `addr_move_in` DATE;";
$CONN->Execute($query);
?>
