<?php

// $Id: report.php 8979 2016-09-20 05:29:42Z smallduh $

/* ���o�]�w�� */
include "config.php";
$sel=(!isset($_POST['sel']))?array(1=>1,2=>1,3=>1,4=>1,5=>1,6=>1,7=>1):$_POST['sel'];

sfs_check();

if(!empty($_REQUEST[this_date])){
	$d=explode("-",$_REQUEST[this_date]);
}else{
	$d=explode("-",date("Y-m-d"));
}


$sel_year=curr_year();
$sel_seme=curr_seme();

//���o�g��
$weeks_array=get_week_arr($sel_year,$sel_seme,$today);
$start_day=curr_year_seme_day($sel_year,$sel_seme);

//���o���Ǵ��̦h�`��
	$sql="select Max(sections) as maxsections from score_setup where year='$sel_year' and semester='$sel_seme'";
	$rs=$CONN->Execute($sql) or trigger_error("SQL�y�k���~�G$sql", E_USER_ERROR);
	if ($rs) {
		while (!$rs->EOF) {
			$all_sections_max=$rs->fields['maxsections'];
			$rs->MoveNext();
		}
	}
	
	$ixp=" �Ŀ�@�ֲέp�D��鰲�O�G";
	$section_include=$sel?array_keys($sel):explode(",",$section_include);
	//$section_include=explode(",",$section_include);
	for($i=1;$i<=$all_sections_max;$i++)
	{
	  $checked=in_array($i,$section_include)?'checked':'';
      $ixp.="<input type='checkbox' name='sel[$i]' value='1' $checked onclick='this.form.submit();'>�� $i �`";
	}

	
	$sel_arr="";
   	for($i=1;$i<=$all_sections_max;$i++)
	{
		if($sel[$i]==0)$sel_arr.="'$i',";
	}	
	$sel_arr=substr($sel_arr,0,-1);
	$notsection="";
	if ($sel_arr!="")$notsection="and (a.section not in ($sel_arr))";

	
	
//

if ($_REQUEST[week_num]) {
	$week_num=$_REQUEST[week_num];
	$weeks_array[0]=$week_num;
	if ($start_day[st_start]) {
		$this_date=$weeks_array[$week_num];
		$d=explode("-",$this_date);
	}
}

if (empty($week_num)) $week_num=$weeks_array[0];

$year=(empty($_REQUEST[year]))?$d[0]:$_REQUEST[year];
$month=(empty($_REQUEST[month]))?$d[1]:$_REQUEST[month];
$day=(empty($_REQUEST[day]))?$d[2]:$_REQUEST[day];
$act=$_POST[act];

if ($act=="�X�֦C�L" || $act=="���Z�C�L") {
	$oo_path="oo";
	
	$pm="";
    while (list($key, $value) = each($sel)) 
	{
    if ($value==1)$pm.="+".$key;
    }
	$pm="�ɭ��X{$pm}�`";
	
	include ("trans_main.php");
}
if ($act=="�C�L�q����(�T�w1~7�`)") {
	$oo_path="letter";
	include ("trans_main.php");
}

if ($act=="�C�L�q����(�ۿ�`��)") {
	$oo_path="letter";
	include ("trans_main1.php");
}


$main=&mainForm($sel_year,$sel_seme,$week_num);

//�q�X����
head("���m�ҩ���");

echo "<style type=\"text/css\">
<!--
.calendarTr {font-size:12px; font-weight: bolder; color: #006600}
.calendarHeader {font-size:12px; font-weight: bolder; color: #cc0000}
.calendarToday {font-size:12px; background-color: #ffcc66}
.calendarTheday {font-size:12px; background-color: #ccffcc}
.calendar {font-size:11px;font-family: Arial}
.dateStyle {font-size:15px;font-family: Arial; color: #cc0066; font-weight: bolder}
-->
</style>
";
echo $main;
foot();

//�D�n��J�e��
function &mainForm($sel_year,$sel_seme,$week_num=""){
	global $school_menu_p,$year,$month,$day,$SFS_PATH_HTML,$CONN,$today,$start_day,$weeks_array,$ixp;
	
	//�����\���
	$tool_bar=&make_menu($school_menu_p);
	
	//�g����
	if ($week_num) $weekForm=wform($sel_year,$sel_seme,$week_num);
	
	//�g���
	$week_select="";
	if (!$start_day[st_start])
		$week_select="�}�Ǥ�S���]�w";
	else {
		reset($weeks_array);
		while(list($k,$v)=each($weeks_array)) {
			if ($k==0) continue;
			$weeks[$k]="��".$k."�g ($v ~ ".date("Y-m-d",(strtotime($v)+86400*6)).")";
		}
		$ds=new drop_select();
		$ds->s_name = "week_num"; //���W��
		$ds->id = $week_num; //����ID
		$ds->arr = $weeks; //���e�}�C
		$ds->has_empty = true; //���C�X�ť�
		$ds->top_option = "�п�ܶg��";
		$ds->bgcolor = "#FFFFFF";
		$ds->font_style = "font-size:12px";
		$ds->is_submit = true; //��ʮɰe�X�d��
		$week_select=$ds->get_select();
	}
		
	//��䪺�s���r��
//	$linkStr=(!empty($week_num))?"&week_num=$week_num":"";
	
	if(!empty($week_num)){
		$cal = new MyCalendar;
		$cal->linkStr=$linkStr;
		$cal->setStartDay(1);
		$cal->getDateLink();
		$mc=$cal->getMonthView($month,$year,$day);
		$the_cal="
		<table cellspacing='1' cellpadding='2' bgcolor='#E2ECFC' class='small'>
		<tr bgcolor='#FEFBDA'>
		<td align='center'>		
		<a href='$_SERVER[SCRIPT_NAME]?act=$_REQUEST[act]&this_day=$today' class='box'><img src='".$SFS_PATH_HTML."images/today.png' alt='�^�줵��' width='16' height='16' hspace='2' border='0' align='absmiddle'>�^�줵��</a>
		</td></tr>
		<tr bgcolor='#FFFFFF'><td>$mc</td></tr>
		</table>
		";
	}

	$main="
	$tool_bar
	<table cellspacing='1' cellpadding='3' bgcolor='#C6D7F2'>
	<tr bgcolor='#FFFFFF'><td>
	<form action='$_SERVER[SCRIPT_NAME]' method='post'>
	<font color='blue'>$sel_year</font>�Ǧ~�ײ�<font color='blue'>$sel_seme</font>�Ǵ� �Դk�g����
	$week_select <br> $ixp <br>
	<input type='hidden' name='act' value='view'>
	<input type='hidden' name='this_date' value='$year-$month-$day'>
	<input type='submit' name='act' value='���Z�C�L'><input type='submit' name='act' value='�X�֦C�L'><input type='submit' name='act' value='�C�L�q����(�T�w1~7�`)'><input type='submit' name='act' value='�C�L�q����(�ۿ�`��)'></td></form></tr>
	</table>
	<table cellspacing='1' cellpadding='3'>
	<tr>
	<td valign='top'>$weekForm</td>
	<td valign='top'>$the_cal</td>
	</tr>
	</table>
	";
	return $main;
}

function wform($sel_year,$sel_seme,$week_num) {
	global $CONN,$weekN,$class_year,$start_day,$weeks_array,$notsection;

	//���o�ӯZ���X�`��
	$sql = "select sections,class_year from score_setup where year = '$sel_year' and semester='$sel_seme'";
	$rs=$CONN->Execute($sql) or trigger_error("SQL�y�k���~�G $sql", E_USER_ERROR);
	while (!$rs->EOF) {
		$i=$rs->fields['class_year'];
		$all_sections[$i] = $rs->fields['sections'];
		$rs->MoveNext();
	}
	$sql="select c_name,c_sort from school_class where year='$sel_year' and semester='$sel_seme' and enable=1 order by c_sort";
	$rs=$CONN->Execute($sql);
	while (!$rs->EOF) {
		$class_cname[$rs->fields['c_sort']]=$rs->fields['c_name'];
		$rs->MoveNext();
	}
	$bgcolor_arr=array("1"=>"#FEFED7","2"=>"#FEFEC4","3"=>"#FEFEB1","4"=>"#FEFE9E","5"=>"#FEFE8B");
	$d=explode("-",$weeks_array[$week_num]);
	$wmt=mktime(0,0,0,$d[1],$d[2],$d[0]);
	$temp="
		<table cellspacing='0' cellpadding='0'0class='small'>
		<tr><td valign='top'>
		<table cellspacing='1' cellpadding='3' bgcolor='#C6D7F2' class='small'>
		<tr bgcolor='#E6F2FF'>
		<td align='center' rowspan='3'>�~��</td>
		<td align='center' rowspan='3'>�Z��</td>
		<td align='center' rowspan='3'>�y��</td>
		<td align='center' rowspan='3'>�m�W</td>
		";
	
	$w_days=0;
	for ($i=1;$i<=6;$i++) { //2013.10.29 $i �� 5�אּ6
		$dd=getdate($wmt+86400*$i);
		$wd[$i]=sprintf("%04d-%02d-%02d",($dd[year]),$dd[mon],$dd[mday]);
		if ($DAY[$wd[$i]]=='1') $w_days++; //2013.10.29 �έp���g�W�Ҥ��
		$dw[$wd[$i]]=$i;
		$temp.="
		<td align='center' colspan='5'>".$dd[mon]."��".$dd[mday]."��</td>
		";
	}
	$temp.="<td align='center' rowspan='2' colspan='5'>���g�X�p</td><td align='center' rowspan='2' colspan='5'>�ܥ��g�֭p</td></tr><tr bgcolor='#E6F2FF'>";
	for ($i=1;$i<=6;$i++) $temp.="<td align='center' colspan='5'>�P��".$weekN[$i-1]."</td>"; //2013.10.29 $i �� 5�אּ6
	$temp.="</tr><tr>";
	for ($i=1;$i<=8;$i++) {  //2013.10.29 $i �� 7�אּ8
		$temp.="
		<td bgcolor='".$bgcolor_arr[1]."'>��<br>��</td>
		<td bgcolor='".$bgcolor_arr[2]."'>�f<br>��</td>
		<td bgcolor='".$bgcolor_arr[3]."'>�m<br>��</td>
		<td bgcolor='".$bgcolor_arr[4]."'>��<br>��<br>�X</td>
		<td bgcolor='".$bgcolor_arr[5]."'>��<br>�L</td>";
	}
	$temp.="</tr>";
	$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
	//2013.10.29  sql $wd[5] �� 5�אּ6
	$sql="select a.*,b.seme_num,c.stud_name from stud_absent a, stud_seme b, stud_base c where a.year='$sel_year' and a.semester='$sel_seme' and a.date >= '$start_day[st_start]' and a.date <= '$wd[6]' and a.stud_id=b.stud_id and b.student_sn=c.student_sn and b.seme_year_seme='$seme_year_seme' $notsection order by a.class_id,b.seme_num,a.date,a.section";
	$rs=$CONN->Execute($sql);
	if ($rs->recordcount() > 0) {
		$m=0;
		while (!$rs->EOF) {
			$ad=$rs->fields['date'];
			$id=$rs->fields['stud_id'];
			if ($stud_id[$m]!=$id) {
				$m++;
				$stud_id[$m]=$id;
				$stud_name[$m]=addslashes($rs->fields['stud_name']);
			} 
			$class_id=explode("_",$rs->fields['class_id']);
			$class[$m][year]=intval($class_id[2]);
			$class[$m][name]=intval($class_id[3]);
			$class[$m][num]=intval($rs->fields['seme_num']);
			switch ($rs->fields['absent_kind']) {
				case '�ư�':
					$abskind=1;
					break;
				case '�f��':
					$abskind=2;
					break;
				case '�m��':
					$abskind=3;
					break;
				default:
					$abskind=5;
					break;
			}
			$section=$rs->fields['section'];
			if ($section=='uf' || $section=='df') {
				//�p�G�O�m�Ҫ��ɭ��X�~�B�z
				if ($abskind==3) {
					if ($wd[1] <= $ad) {
						$enable[$m]=1;
						$absent[$m][$dw[$ad]][4]++;
						$absent[$m][7][4]++; //2013.10.29 [$m][7] �� 6�אּ7
					}
					$absent_total[$id][4]++;
				}
			} elseif ($section=="allday") {
				if ($wd[1] <= $ad) {
					$enable[$m]=1;
					//�p�G�O�m��, �ɭ��X�U�[�@
					if ($abskind==3) {
						$absent[$m][$dw[$ad]][4]+=2;
						$absent[$m][7][4]+=2; //2013.10.29 [$m][7] �� 6�אּ7
					}
					$absent[$m][$dw[$ad]][$abskind]+=$all_sections[$class[$m][year]];
					$absent[$m][7][$abskind]+=$all_sections[$class[$m][year]]; //2013.10.29 [$m][7] �� 6�אּ7
				}
				if ($abskind==3) $absent_total[$id][4]+=2;
				$absent_total[$id][$abskind]+=$all_sections[$class[$m][year]];
			} else {
				if ($wd[1] <= $ad) {
					$enable[$m]=1;
					$absent[$m][$dw[$ad]][$abskind]++;
					$absent[$m][7][$abskind]++; //2013.10.29 [$m][7] �� 6�אּ7
				}
				$absent_total[$id][$abskind]++;
			}
			$rs->MoveNext();
		}
	}
	reset($enable);
	while(list($i,$v)=each($enable)) {
		$temp.="<tr bgcolor='#E6F2FF'><td>".substr($class_year[$class[$i][year]],0,2)."<td>".$class_cname[$class[$i][name]]."<td align='right'>".$class[$i][num]."<td>".stripslashes($stud_name[$i]);
		for ($j=1;$j<=7;$j++) {  //2013.10.29 $j �� 6�אּ7
			for ($m=1;$m<=5;$m++) {	
				$temp.="<td bgcolor='".$bgcolor_arr[$m]."'>".$absent[$i][$j][$m];
			}
		}
		for ($m=1;$m<=5;$m++) {
			$temp.="<td bgcolor='".$bgcolor_arr[$m]."'>".$absent_total[$stud_id[$i]][$m];
		}
		$temp.="</tr>";
		$have_data=1;
	}
	if (!$have_data==1) $temp.="<tr bgcolor='#E6F2FF'><td colspan='39' align='center'>���g�L���</td></tr>";
	$temp.="</table>
		</td><td valign='top'>
		</td></tr>
		</table>
		";
	return $temp;
}
?>
