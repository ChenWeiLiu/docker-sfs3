<?php

// $Id: stat_all.php 9011 2016-11-21 17:48:30Z smallduh $

/* ���o�]�w�� */
include_once "config.php";

sfs_check();
$sel_year=(empty($_REQUEST[sel_year]))?curr_year():$_REQUEST[sel_year];
$sel_seme=(empty($_REQUEST[sel_seme]))?curr_seme():$_REQUEST[sel_seme];

//default start_date and end_date
//���o�}�Ǥ�
$start_day=curr_year_seme_day($sel_year,$sel_seme);
$start_date=($_POST['start_date']=='')?$start_day[st_start]:$_POST['start_date'];
$end_date=($_POST['end_date']=='')?date("Y-m-d"):$_POST['end_date'];


if(!empty($_REQUEST[this_date])){
	$d=explode("-",$_REQUEST[this_date]);
}else{
	$d=explode("-",date("Y-m-d"));
}
$year=(empty($_REQUEST[year]))?$d[0]:$_REQUEST[year];
$month=(empty($_REQUEST[month]))?$d[1]:$_REQUEST[month];
$day=(empty($_REQUEST[day]))?$d[2]:$_REQUEST[day];
	
$act=$_REQUEST[act];



//����ʧ@�P�_
if($act=="�x�s�n�O"){
	add_all($sel_year,$sel_seme,$_POST[class_id],$_POST[date],$_POST[s]);
	header("location: $_SERVER[PHP_SELF]?this_date=$_POST[date]&class_id=$_POST[class_id]");
}elseif($act=="print"){
	$main=statForm($sel_year,$sel_seme,$_GET[class_id],"print");
	echo $main;
	exit;
}elseif($act=="������^"){
	header("location: $_SERVER[PHP_SELF]?this_date=$_POST[date]&class_id=$_POST[class_id]");
}else{
	$main=&mainForm($sel_year,$sel_seme,$_REQUEST[class_id]);
}


//�q�X����
head("���m�Ҳέp");

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
<script type=\"text/javascript\" src=\"./JSCal/src/js/jscal2.js\"></script>
<script type=\"text/javascript\" src=\"./JSCal/src/js/lang/b5.js\"></script>
<link type=\"text/css\" rel=\"stylesheet\" href=\"./JSCal/src/css/jscal2.css\">
";
echo $main;
foot();

//�D�n��J�e��
function &mainForm($sel_year,$sel_seme,$class_id=""){
	global $school_menu_p,$year,$month,$day,$SFS_PATH_HTML,$school_menu_p,$start_day,$start_date,$end_date;
	//�����\���
	$tool_bar=&make_menu($school_menu_p);
	

	//���o�ӯZ�ξǥͦW��A�H�ζ�g����
	if(!empty($class_id)){
		$signForm=&statForm($sel_year,$sel_seme,$class_id);
	}
	//�~�ŻP�Z�ſ��
	$class_select=&classSelect($sel_year,$sel_seme,"","class_id",$class_id,false);
	
	if(!empty($class_id)){
		$cal = new MyCalendar;
		$cal->linkStr="&class_id=$class_id";
		$cal->setStartDay(1);
		$cal->getDateLink();
		$mc=$cal->getMonthView($month,$year,$day);
		$the_cal="
		<table cellspacing='1' cellpadding='2' bgcolor='#E2ECFC' class='small'>
		<tr bgcolor='#FEFBDA'>
		<td align='center'>		
		<a href='$_SERVER[PHP_SELF]?act=$_REQUEST[act]&this_day=$today&class_id=$class_id' class='box'><img src='".$SFS_PATH_HTML."images/today.png' alt='�^�줵��' width='16' height='16' hspace='2' border='0' align='absmiddle'>�^�줵��</a>
		</td></tr>
		<tr bgcolor='#FFFFFF'><td>$mc</td></tr>
		</table>
		";
	}


	$main="
	$tool_bar
	<table cellspacing='1' cellpadding='3' bgcolor='#C6D7F2'>
	<form action='$_SERVER[PHP_SELF]' method='post'>
	<tr bgcolor='#FFFFFF'><td>
	<font color='blue'>$sel_year</font>�Ǧ~�ײ�<font color='blue'>$sel_seme</font>�Ǵ�
	��
	<input type=\"text\" name=\"start_date\" id=\"start_date\" size=\"10\" value=\"".$start_date."\">
 		<script type=\"text/javascript\">
		new Calendar({
  		    inputField: \"start_date\",
   		    dateFormat: \"%Y-%m-%d\",
    	    trigger: \"start_date\",
 	        min:\"".$start_day[st_start]."\",
    		max: \"".date("Y-m-d")."\",
    	    bottomBar: false,
    	    weekNumbers: false,
    	    showTime: false,
    	    onSelect: function() {this.hide();}
		    });
		</script>
	��
	<input type=\"text\" name=\"end_date\" id=\"end_date\" size=\"10\" value=\"".$end_date."\">
			<script type = \"text/javascript\" >
			new Calendar({
				inputField:\"end_date\",
				dateFormat: \"%Y-%m-%d\",
				trigger: \"end_date\",
				min: \"".$start_day[st_start]."\",
				max: \"".date("Y-m-d")."\",
				bottomBar: false,
				weekNumbers: false,
				showTime: false,
				onSelect: function() {this.hide();}
				});
			</script>
	��

	$class_select

	���m�Ҳέp�έp���G
	<input type='submit' value='�C�X'>
	</td></tr>
	</form>
	</table>
	<a href='$_SERVER[PHP_SELF]?act=print&class_id=$class_id&end_date=$end_date&start_date=$start_date' target='_blank'>�C�L</a>
	<table cellspacing='1' cellpadding='3'>
	<tr>
	<td valign='top'>$signForm</td>
	<td valign='top'>$the_cal</td>
	</tr>
	</table>
	";
	return $main;
}



//���o�ӯZ�ξǥͦW��A�H�ζ�g����
function &statForm($sel_year,$sel_seme,$class_id,$mode){
	global $year,$month,$day,$CONN,$start_date,$end_date;
	
	//���o���m�����O
	$absent_kind_array= SFS_TEXT("���m�����O");
	
	//�W�[���|�o�����O
	$abkind_TXT="<td>���|</td>";
	
	//�s�@���D
	foreach($absent_kind_array as $abkind){
		$abkind_TXT.="<td>$abkind</td>";
	}
		
	//�ഫ�Z�ťN��
	$c=class_id_2_old($class_id);
	
	//���o�ǥͰ}�C
	$stud=get_stud_array($c[0],$c[1],$c[3],$c[4],"id","name");

	//�C�X���Y
	$s=get_school_base();
	if ($mode=="print") {
		$start_date=$_GET['start_date'];
		$end_date=$_GET['end_date'];

		$print_str="	<html><head><title>$s[sch_cname]".$sel_year."�Ǧ~�ײ�".$sel_seme."�Ǵ�".$c[3]."�~".$c[4]."�Z���m�Ҳέp��</title></head>\n
			<body>
			<p align='center'><font face='�з���' size='4'>$s[sch_cname]".$sel_year."�Ǧ~�ײ�".$sel_seme."�Ǵ�</font><br><font face='�з���' size='5'>".$c[3]."�~".$c[4]."�Z���m�Ҳέp��</font></p>\n
			<p align='center'><font face='�з���' size='3'>�έp�϶��G$start_date �� $end_date</font></p>\n
			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-collapse: collapse\" bordercolor=\"#111111\" width=\"610\">\n
			<tr><td style=\"border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 0.75pt 1.5pt; padding: 0cm 1.4pt;\" align=\"center\" width=\"50\">�Ǹ�</td>\n
			<td style=\"border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\" width=\"50\">�y��</td>\n
			<td style=\"border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\" width=\"90\">�m�W</td>\n
			<td style=\"border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\" width=\"60\">���|</td>\n
			<td style=\"border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\" width=\"60\">�m��</td>\n
			<td style=\"border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\" width=\"60\">�ư�</td>\n
			<td style=\"border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\" width=\"60\">�f��</td>\n
			<td style=\"border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\" width=\"60\">�ల</td>\n
			<td style=\"border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\" width=\"60\">����</td>\n
			<td style=\"border-style: solid; border-color: windowtext; border-width: 1.5pt 1.5pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\" width=\"60\">���i�ܤO</td></tr>\n";
	}

	//�y��
	$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
	$seme_class=$c[3].sprintf("%02d",$c[4]);
	$sql_num="select stud_id,seme_num from stud_seme where seme_class='$seme_class' and seme_year_seme='$seme_year_seme'";
	$rs_num=$CONN->Execute($sql_num);
	while (!$rs_num->EOF) {
		$stud_id=$rs_num->fields['stud_id'];
		$num[$stud_id]=$rs_num->fields['seme_num'];
		$rs_num->MoveNext();
	}

	//���o�}�Ǥ�
	$start_day=curr_year_seme_day($sel_year,$sel_seme);
	$i=1;
	foreach($stud as $id=>$name){

		//���o�Ӿǥ͸��
		//$aaa=getOneMdata($id,$sel_year,$sel_seme,"$year-$month-$day","����",$start_day[st_start]);
		$aaa=getOneMdata($id,$sel_year,$sel_seme,$end_date,"����",$start_date);

		//�U�د��m�Ҽ�
		if ($mode=="print") {
			$d_b=($i%5==0 || $i==count($stud))?"1.5pt":"0.75pt";
			$sections_data="<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt $d_b 0.75pt; padding: 0cm 1.4pt;\" align=\"center\"><font face=\"Dotum\">$aaa[f]</font></td>\n";
		} else {
			$sections_data="<td>$aaa[f]</td>";
		}
		foreach($absent_kind_array as $abkind){
			if ($mode=="print") {
				$r_b=($abkind=="���i�ܤO")?"1.5pt":"0.75pt";
				$sections_data.="<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt $r_b $d_b 0.75pt; padding: 0cm 1.4pt;\" align=\"center\"><font face=\"Dotum\">$aaa[$abkind]</font></td>\n";
			} else {
				$sections_data.="<td>$aaa[$abkind]</td>";
			}
		}

		if ($mode=="print") {
			$print_str.="<tr><td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt $d_b 1.5pt; padding: 0cm 1.4pt;\" align=\"center\"><font face=\"Dotum\">$id</font></td>\n
					<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt $d_b 0.75pt; padding: 0cm 1.4pt;\" align=\"center\"><font face=\"Dotum\">".$num[$id]."</font></td>\n
					<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt $d_b 0.75pt; padding: 0cm 1.4pt;\">$name</td>\n
					$sections_data
					</tr>\n";
		} else {
			//�C�@�C���
			//��ѨS��
			$data.="
			<tr bgcolor='#FFFFFF' align='center'>
			<td>$id</td>
			<td>".$num[$id]."</td>
			<td>$name</td>		
			$sections_data	
			</tr>";
		}
		$i++;
	}
	
	//����
	$help_text="
	�u���|�v�u�έp�m�Ҫ��ɺX�έ��X�C(�]���u���m�Ҫ��ɺX�έ��X�~���H�����`�ͬ����{����)
	";
	$help=help($help_text);
	$main="
	<table cellspacing='0' cellpadding='0' class='small' >
	<tr><td valign='top'>
		<table cellspacing='1' cellpadding='3' bgcolor='#000000' class='small'>
		<tr bgcolor='#E6F2FF'>
		<td>�Ǹ�</td>
		<td>�y��</td>
		<td>�m�W</td>		
		$abkind_TXT
		</tr>
		<form action='$_SERVER[PHP_SELF]' method='post' name='myform'>
		$data	
		</table>
	</td><td valign='top'>

		</form>
	</td></tr>
	</table>
	$help
	";
	if ($mode=="print") {
		$print_str.="</table>
		<p style='font-size:14pt;font-family:�з���'>�ֳ��B</p>
		";

		return $print_str;
	}	else {
		return $main;
	}

}
?>