<?php

require "config.php";

sfs_check();



head("親子年齡差距45歲以上列表");
print_menu($menu_p);

$years=45;

$list="<table border=2 cellpadding=3 cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width='100%'>
	<tr align='center' bgcolor='#ccccff'><td>NO.</td><td>班級</td><td>座號</td><td>學生姓名</td><td>狀態</td><td>父親</td><td>父親年紀差距</td><td>母親</td><td>母親年紀差距</td><td>父母年紀差距</td></tr>";

$sql="select a.curr_class_num,a.stud_name,YEAR(a.stud_birthday)-1911 AS stud_birthyear,a.stud_study_cond,b.fath_name,b.fath_birthyear,YEAR(a.stud_birthday)-b.fath_birthyear-1911 AS diff_fath,b.moth_name,b.moth_birthyear,YEAR(a.stud_birthday)-b.moth_birthyear-1911 AS diff_moth ,ABS(b.fath_birthyear-b.moth_birthyear) AS diff FROM stud_base a LEFT JOIN stud_domicile b ON a.student_sn=b.student_sn WHERE a.stud_study_cond IN (0,15) HAVING ( diff_fath>=45 OR diff_moth>=45 ) ORDER BY curr_class_num";
$res=$CONN->Execute($sql) or trigger_error("SQL語法錯誤：$sql", E_USER_ERROR);
while(!$res->EOF){
	++$i;
	$class=substr($res->fields['curr_class_num'],0,3);
	$no=substr($res->fields['curr_class_num'],-2);
	$stud_name="{$res->fields['stud_name']} ({$res->fields['stud_birthyear']})";
	$fath_name="{$res->fields['fath_name']} ({$res->fields['fath_birthyear']})";
	$diff_fath=$res->fields['diff_fath'];
	$moth_name="{$res->fields['moth_name']} ({$res->fields['moth_birthyear']})";
	$diff_moth=$res->fields['diff_moth'];
	$diff=$res->fields['diff'];
	$stud_cond = $res->fields['stud_study_cond'] ? '在家自學' : '在籍';
	
	$bgcolor_fath = $diff_fath>=$years ? "#ccffcc" : "#ffffff";
	$bgcolor_moth = $diff_moth>=$years ? "#ffcccc" : "#ffffff";
	
	$list.= "<tr align='center'><td>$i</td><td>$class</td><td>$no</td><td>$stud_name</td><td>$stud_cond</td><td>$fath_name</td><td bgcolor=\"$bgcolor_fath\">$diff_fath</td><td>$moth_name</td><td bgcolor=\"$bgcolor_moth\">$diff_moth</td><td>$diff</td></tr>";
	$res->MoveNext();
}
$list.= "</table>";
echo $list;
echo "※備註： 1.僅以出生年次進行計算； 2.父母親的出生年註記須為民國年。";

foot();

?>
