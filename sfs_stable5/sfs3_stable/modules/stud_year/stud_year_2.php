<?php

// $Id: stud_year_2.php 9257 2018-05-30 08:38:24Z smallduh $

// ���J�]�w��
include "stud_year_config.php";
// �{���ˬd
sfs_check();

//�Y����ܾǦ~�Ǵ��A�i����Ψ��o�Ǧ~�ξǴ�
if(!empty($_REQUEST[year_seme])){
	$ys=explode("-",$_REQUEST[year_seme]);
	$curr_year=$ys[0];
	$curr_seme=$ys[1];
}else{
	$curr_year=(empty($_REQUEST[curr_year]))?curr_year():$_REQUEST[curr_year]; //�ثe�Ǧ~
	$curr_seme=(empty($_REQUEST[curr_seme]))?curr_seme():$_REQUEST[curr_seme]; //�ثe�Ǵ�
}


// ���ݭn register_globals
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}

//�d�߷��Ǵ��s�Z����
//$curr_year = curr_year();
//$curr_seme = curr_seme();


//����B�z
if($act=="�ߧY����"){
	move2class($old_class_id,$to_class_id,$stud_id);
	header("location: {$_SERVER['PHP_SELF']}?year_seme=$_REQUEST[year_seme]&class_id=$to_class_id");
}else{

	$main=main_form($curr_year,$curr_seme,$class_id);

}


//�L�X���Y
head("�P�~�Ŷ��Z�Žվ�");
?>
<script language="JavaScript">
<!-- Begin
function jumpMenu_seme(){
	location="<?php echo $_SERVER['PHP_SELF']?>?act=<?php echo $act;?>&year_seme=" + document.myform.year_seme.options[document.myform.year_seme.selectedIndex].value + "&class_id=";
}


function jumpMenu(){
	location="<?php echo $_SERVER['PHP_SELF']?>?act=<?php echo $act;?>&year_seme=<?php echo $_REQUEST[year_seme]?>&class_id=" + document.myform.class_id.options[document.myform.class_id.selectedIndex].value;
	//location="<?php echo $_SERVER['PHP_SELF'] ?>?act=<?php echo $act;?>&class_id=" + document.myform.class_id.options[document.myform.class_id.selectedIndex].value;
}

function CheckAll(){
	for (var i=0;i<document.myform2.elements.length;i++){
		var e = document.myform2.elements[i];
		if (e.id == 'stud_arr') e.checked = !e.checked;
	}
}
//  End -->
</script>
<?
echo $main;
foot();


//�D�n����
function main_form($curr_year,$curr_seme,$class_id){

	global $menu_p,$CONN,$s_year;

	if(empty($class_id)){
		if($s_year) {
			if(sizeof($curr_year)<3) $curr_year="0".$curr_year;
			$class_id=$curr_year."_".$curr_seme."_".sprintf("%02d",$s_year)."_01";
		}
		else{
			if(sizeof($curr_year)<3) $curr_year="0".$curr_year;
			$class_id=$curr_year."_".$curr_seme."_01_01";
		}	
	}

	//���o�~�׻P�Ǵ����U�Կ��
	$date_select=class_ok_setup_year($curr_year,$curr_seme,"year_seme","jumpMenu_seme",$_REQUEST[year_seme]);

	//�Z�ſ��
	$get_class_select=get_class_select($curr_year,$curr_seme,"","class_id","jumpMenu",$class_id,"��");

	//�Z�ŦW��
	$array=get_class_stud($class_id);
	$Cyear=$array[c_year]*1;

	//�Ӧ~�ſ��
	$class_select=&get_class_select($curr_year,$curr_seme,$Cyear,"to_class_id","",$class_id,"��");

	//���o�ӯZ�ǥͰ}�C
	$c=class_id_2_old($class_id);
	$stu=get_stud_array($curr_year,$curr_seme,$c[3],$c[4],"sn","name");
	$stu_n=sizeof($stu);
	
	if(!empty($stu) and $stu_n>0){
		$s="";
		while(list($sn,$name)=each($stu)){
			$st=get_stud_base($sn);
			$color=($st[stud_sex]=='1')?"#E3F3FD":"#FFE1E1";

			$s.="
			<tr bgcolor='$color'>
			<td align=center><input type='checkbox' name='stud_id[]' value='$st[stud_id]' id='stud_arr'>".substr($st[curr_class_num],-2)."</td>
			<td align=center>$st[stud_id]</td>
			<td align=center>$name</td>
			</tr>";
		}
	}

	//�u��C
	$tool_bar=make_menu($menu_p);
	$main="
	$tool_bar
	<table cellspacing=0 cellpadding=0><tr><td>
		<table bgcolor='#9EBCDD' cellspacing=1 cellpadding=4>
		<form name ='myform' action='{$_SERVER['PHP_SELF']}' method='post' >
		<tr bgcolor='#FFFFFF'><td colspan='4'>
		$date_select
		$get_class_select

		</form>
		<form name ='myform2' action='{$_SERVER['PHP_SELF']}' method='post' >
		�s�Z�@�~</td>
		<tr class=title_sbody1 ><td align=center><input type='checkbox' name='all_stud' onClick='CheckAll();'>�y��</td><td align=center>�Ǹ�</td><td align=center>�m�W</td></tr>
		</tr>
		$s
		</table>
	</td><td width=9>&nbsp;</td><td valign='top'>
	��Ŀ諸�ǥͳ��ը�G<p>
	$class_select
	<input type='hidden' name='old_class_id' value='$class_id'>
	<input type='hidden' name='year_seme' value='$_REQUEST[year_seme]'>
	<input type=submit name='act' value='�ߧY����'></td></tr>
	</form></table>
	";
	return $main;
}




//��ǥ̲ͭ���Y�ӯZ��
function move2class($old_class_id,$to_class_id,$stud_id){
	global $CONN;
	if(!empty($_REQUEST[year_seme])){
	$ys=explode("-",$_REQUEST[year_seme]);
	$curr_year=$ys[0];
	$curr_seme=$ys[1];
}else{
	$curr_year=(empty($_REQUEST[curr_year]))?curr_year():$_REQUEST[curr_year]; //�ثe�Ǧ~
	$curr_seme=(empty($_REQUEST[curr_seme]))?curr_seme():$_REQUEST[curr_seme]; //�ثe�Ǵ�
}

	$class_name_arr = class_name($curr_year,$curr_seme);
	
//	echo $old_class_id."--".$to_class_id."<pre>";print_r($stud_id);echo"</pre>";exit;	
	//���p�P�˪��Z�šA�h�h�X
	if($old_class_id==$to_class_id)return;
	for($i=0;$i<sizeof($stud_id);$i++){

		//���s�Z�Ū��̫�@���A�÷s�W�@��
		$last_num=get_class_last_num($to_class_id)+1;
		
        //�������ơA�ɨ�
		if(strlen($last_num)<2)$last_num="0".$last_num;

		//�ഫ�Z�ťN�X
		$class_data=class_id_2_old($to_class_id);

		$curr_class_num=$class_data[2].$last_num;	//�y��(10101)
		$STID=$stud_id[$i];

		//��sstud_base���ǥͩ��ݯZ�Ÿ��

		$sql_update = "update stud_base set  curr_class_num= '$curr_class_num' where stud_id='$STID'";
		$CONN->Execute($sql_update) or die($sql_update);
		//���o�Ǧ~�Ǵ�
		$seme_year_seme = sprintf("%03d%d",$curr_year,$curr_seme);
		$seme_class_name = $class_name_arr[$class_data[2]];
		$query = "update stud_seme set seme_class='$class_data[2]', seme_num='$last_num', seme_class_name='$seme_class_name' where stud_id='$STID' and seme_year_seme='$seme_year_seme' ";
		$CONN->Execute($query)or die($query);

		//���o�ǥͪ� student_sn , �M���Ǵ������w�����Z�]�����ӯZ , �_�h�b�s�Z�|�ݤ���e���w�����Z
		$query = "select student_sn from stud_seme where stud_id='$STID' and seme_year_seme='$seme_year_seme' ";
		$res=$CONN->Execute($query) or die ($query);
		$student_sn=$res->fields['student_sn'];
		//�Ǵ���ƪ��W��
		$score_semester="score_semester_".$curr_year."_".$curr_seme;
		$query="update $score_semester set class_id='$to_class_id' where student_sn='$student_sn' and class_id='$old_class_id'";
		$CONN->Execute($query) or die($query);
	}
}




//��X�Y�Z�Ū��̫�@��
function get_class_last_num($to_class_id){
	global $CONN;
	$num=class_id_2_old($to_class_id);
	$query = "select right(curr_class_num,2) from stud_base where stud_study_cond =0 and curr_class_num like '$num[2]%' ";	
    $recordSet = $CONN->Execute($query) or die ($query);

	$big=0;
	while(list($n)=$recordSet->FetchRow()){
		$n=$n*1;
		if($n>$big)$big=$n;
	}
	return $big;
}


?>