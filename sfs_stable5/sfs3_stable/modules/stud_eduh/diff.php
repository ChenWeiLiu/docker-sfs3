<?php

require "config.php";

sfs_check();



head("�ˤl�~�֮t�Z45���H�W�C��");
print_menu($menu_p);

$years=45;

$list="<table border=2 cellpadding=3 cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width='100%'>
	<tr align='center' bgcolor='#ccccff'><td>NO.</td><td>�Z��</td><td>�y��</td><td>�ǥͩm�W</td><td>���A</td><td>����</td><td>���˦~���t�Z</td><td>����</td><td>���˦~���t�Z</td><td>�����~���t�Z</td></tr>";

$sql="select a.curr_class_num,a.stud_name,YEAR(a.stud_birthday)-1911 AS stud_birthyear,a.stud_study_cond,b.fath_name,b.fath_birthyear,YEAR(a.stud_birthday)-b.fath_birthyear-1911 AS diff_fath,b.moth_name,b.moth_birthyear,YEAR(a.stud_birthday)-b.moth_birthyear-1911 AS diff_moth ,ABS(b.fath_birthyear-b.moth_birthyear) AS diff FROM stud_base a LEFT JOIN stud_domicile b ON a.student_sn=b.student_sn WHERE a.stud_study_cond IN (0,15) HAVING ( diff_fath>=45 OR diff_moth>=45 ) ORDER BY curr_class_num";
$res=$CONN->Execute($sql) or trigger_error("SQL�y�k���~�G$sql", E_USER_ERROR);
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
	$stud_cond = $res->fields['stud_study_cond'] ? '�b�a�۾�' : '�b�y';
	
	$bgcolor_fath = $diff_fath>=$years ? "#ccffcc" : "#ffffff";
	$bgcolor_moth = $diff_moth>=$years ? "#ffcccc" : "#ffffff";
	
	$list.= "<tr align='center'><td>$i</td><td>$class</td><td>$no</td><td>$stud_name</td><td>$stud_cond</td><td>$fath_name</td><td bgcolor=\"$bgcolor_fath\">$diff_fath</td><td>$moth_name</td><td bgcolor=\"$bgcolor_moth\">$diff_moth</td><td>$diff</td></tr>";
	$res->MoveNext();
}
$list.= "</table>";
echo $list;
echo "���Ƶ��G 1.�ȥH�X�ͦ~���i��p��F 2.�����˪��X�ͦ~���O��������~�C";

foot();

?>
