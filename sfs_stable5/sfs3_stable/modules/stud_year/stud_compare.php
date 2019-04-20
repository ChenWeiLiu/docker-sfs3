<?php
include "stud_year_config.php";

sfs_check();

if (isset($_POST['curr_class']))
$curr_class = $_POST['curr_class'];
else
$curr_class = 1;


if (isset($_POST['curr_seme'])) {
	$year = (int)substr($_POST['curr_seme'],0,-1);
	$seme = (int)substr($_POST['curr_seme'],-1);
	$curr_seme = $_POST['curr_seme'];

} else {
	$year = curr_year();
	$seme = curr_seme();
	$curr_seme = sprintf("%03d%d",$year,$seme);
}

if ($seme == 1) {
	$last_year = $year-1;
	$last_seme=2;
	$last_curr_class = $curr_class -1;
}
else {
	$last_year = $year;
	$last_seme = 1;
	$last_curr_class = $curr_class;
}

$last_year_seme = sprintf("%03d%d",$last_year,$last_seme);

$query = "SELECT a.student_sn,substring(a.seme_class,1,1) AS c_year,substring(a.seme_class,2) AS c_class  , a.seme_num, b.stud_name FROM stud_seme a ,stud_base b WHERE a.student_sn=b.student_sn AND a.seme_year_seme ='$curr_seme' AND a.seme_class LIKE '$curr_class%' ORDER BY a.seme_class ";

$res = $CONN->Execute($query) or die($query);
$arr = array();
while($row =$res->fetchRow()) {
	$arr['s'.$row['student_sn']] = $row;
}

$arr2 = array();

$query = "SELECT student_sn,substring(seme_class,1,1) AS c_year ,substring(seme_class,2)  AS c_class,seme_num FROM stud_seme WHERE seme_year_seme='$last_year_seme' AND seme_class LIKE '$last_curr_class%' ORDER BY seme_class,seme_num ";

$res2 = $CONN->Execute($query) or die($query);
//echo $query;
while($row =$res2->fetchRow())
	$arr2['s'.$row['student_sn']] = $row;
//print_r($class_year);

head('新舊學期座號對應');
print_menu($menu_p);
$class_seme_p = get_class_seme(); //學年度
?>

<form id="myform" method="post" action=""><select name="curr_seme"
	onchange="this.form.submit()">
	<?php foreach($class_seme_p as $id=>$val):?>
	<option value="<?=$id?>" <?php if($curr_seme==$id):?> selected
	<?php endif?>><?=$val?></option>
	<?php endforeach;?>
</select> <select name="curr_class" onChange="this.form.submit()">
<?php foreach($class_year as $id=>$val):?>
	<option value="<?=$id?>" <?php if ($id == $curr_class):?> selected
	<?php endif;?>><?=$val?></option>
	<?php endforeach;?>
</select></form>
<table border="1">
	<tr>
		<td colspan="3"><?=$year?>學年第<?=$seme?>學期</td>
		<td colspan="3"><?=$last_year?>學年第<?=$last_seme?>學期</td>
		<td rowspan="2">姓名</td>
	</tr>
	<tr>
		<td>新年級</td>
		<td>新班級</td>
		<td>新座號</td>
		<td>原年級</td>
		<td>原班級</td>
		<td>原座號</td>
	</tr>
	<?php foreach($arr as $sn=>$val):?>
	<tr>
		<td><?=$val['c_year']?></td>
		<td><?=$val['c_class']?></td>
		<td><?=$val['seme_num']?></td>
		<td><?=$arr2[$sn]['c_year']?></td>
		<td><?=$arr2[$sn]['c_class']?></td>
		<td><?=$arr2[$sn]['seme_num']?></td>
		<td><?=$val['stud_name']?></td>
	</tr>
	<?php endforeach;?>
</table>
<?php
	foot();
?>
