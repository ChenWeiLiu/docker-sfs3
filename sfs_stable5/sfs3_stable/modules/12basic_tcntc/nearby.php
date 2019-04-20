<?php
// $Id: list.php 5310 2009-01-10 07:57:56Z hami $

include "config.php";

sfs_check();

//秀出網頁
head("就近入學");
print_menu($menu_p);

$linkstr="work_year_seme=$work_year_seme&stud_class=$stud_class";
echo print_menu($MENU_P,$linkstr);

echo "<br><br><FONT SIZE=5 color='red'>◎本校參加中投區免試入學學校的類別：[ ".$school_nature_array[$school_nature]." ]";
echo "<br><br>◎本校學生參加中投區免試入學，可得的級分：$school_nature</FONT>";
echo "<br><br><br><br>◎欲修正學校的類別，請系統管理員至 [模組權限管理] 調整本模組的模組變數 school_nature 即可！";
foot();?>