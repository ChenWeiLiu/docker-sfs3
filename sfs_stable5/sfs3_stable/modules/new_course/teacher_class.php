<?php
// $Id: teacher_class.php 8102 2014-08-31 15:06:51Z infodaes $

/* ���o�򥻳]�w�� */
include "config.php";

sfs_check();

if($_POST['course_id'] and checkid($_SERVER['SCRIPT_FILENAME'],1)) {
	$sql="UPDATE score_course SET c_kind='".$_POST['course_id_kind']."' WHERE course_id=".$_POST['course_id'];
	$rs=$CONN->Execute($sql) or user_error("���~�T���G",$sql,256);
}


$teacher_sn = $_REQUEST['view_tsn'];
$year_seme = $_REQUEST['year_seme'];
$class_id = $_REQUEST['class_id'];

//�Y����ܾǦ~�Ǵ��A�i����Ψ��o�Ǧ~�ξǴ�
if(!empty($year_seme)){
	$ys=explode("-",$year_seme);
	$sel_year=$ys[0];
	$sel_seme=$ys[1];
}

if(empty($sel_year))$sel_year = curr_year(); //�ثe�Ǧ~
if(empty($sel_seme))$sel_seme = curr_seme(); //�ثe�Ǵ�
//���~�]�w
if($error==1){
	$act="error";
	$error_title="�L�~�ũM�Z�ų]�w";
	$error_main="�䤣�� $sel_year �Ǧ~�ײ� $sel_seme �Ǵ����~�ų]�w�A�G�z�L�k�ϥΦ��\��C<ol><li>�Х���y<a href='".$SFS_PATH_HTML."school_affairs/every_year_setup/class_year_setup.php'>�Z�ų]�w</a>�z�]�w�~�ťH�ίZ�Ÿ�ơC<li>�H��O�o�C�@�Ǵ����Ǵ��X���n�]�w�@����I</ol>";
}

//����ʧ@�P�_
if($act=="error"){
	$main=error_tbl($error_title,$error_main);
}else{
	$main=class_form_search($sel_year,$sel_seme);
}



//�q�X����
head("�Юv�Ҫ��d��");

echo $main;
foot();

/*
�禡��
*/

//�򥻳]�w����
function class_form_search($sel_year,$sel_seme){
	global $school_menu_p,$PHP_SELF,$view_tsn,$teacher_sn,$class_id;
	if(empty($view_tsn))$view_tsn=$teacher_sn;

  //�u�X�{���ƽҪ�
	//$teacher_select=select_teacher("teacher_sn",$view_tsn,'1',$sel_year,$sel_seme,"jumpMenu");
	$teacher_select=select_teacher_in_course("teacher_sn",$view_tsn,'1',$sel_year,$sel_seme,"jumpMenu");


	//���o�~�׻P�Ǵ����U�Կ��
	$date_select=class_ok_setup_year($sel_year,$sel_seme,"year_seme","jumpMenu_seme");

	$tool_bar=make_menu($school_menu_p);
	
	if ($view_tsn)
	   $list_class_table=search_teacher_class_table($sel_year,$sel_seme,$view_tsn);

	$main="
	<script language='JavaScript'>
	function jumpMenu(){
		location=\"$PHP_SELF?act=$act&&year_seme=\" + document.myform.year_seme.options[document.myform.year_seme.selectedIndex].value + \"&view_tsn=\" + document.myform.teacher_sn.options[document.myform.teacher_sn.selectedIndex].value;
	}
	function jumpMenu_seme(){
		if(document.myform.year_seme.options[document.myform.year_seme.selectedIndex].value!=''){
			location=\"$PHP_SELF?act=$act&year_seme=\" + document.myform.year_seme.options[document.myform.year_seme.selectedIndex].value;
		}
	}
	</script>
	$tool_bar
	<form action='$PHP_SELF' method='post' name='myform'>
	<table cellspacing='1' cellpadding='4'  bgcolor=#9EBCDD>
	
	<tr bgcolor='#F7F7F7'>
	<td>$date_select</td>
	<td>�Юv�G $teacher_select	</td>
	</tr>	
	</table>
	$list_class_table
	</form>
	";
	return $main;
}

//�Юv�����Ҫ�
function search_teacher_class_table($sel_year="",$sel_seme="",$view_tsn=""){
	global $CONN,$PHP_SELF,$class_year,$conID,$weekN,$school_menu_p,$sections;
	
	$main=teacher_all_class($sel_year,$sel_seme,$view_tsn)."<br>";

	//���o�Юv�½Ҫ��Z�Ÿ�ơ]�}�C�^
	$sql_select = "SELECT class_id FROM score_course WHERE year = $sel_year AND semester=$sel_seme AND (teacher_sn ='$view_tsn' OR cooperate_sn ='$view_tsn') group by class_id";
	$recordSet=$CONN->Execute($sql_select) or user_error("���~�T���G",$sql_select,256);
	while(list($clas_id)= $recordSet->FetchRow()){
		$clas_id_array[]=$clas_id;
	}

	for($i=0;$i<sizeof($clas_id_array);$i++){
		$main.=search_class_table($sel_year,$sel_seme,$clas_id_array[$i],$view_tsn)."<br>";
	}
	return $main;
}

//�Юv�������`��
function teacher_all_class($sel_year="",$sel_seme="",$tsn=""){
	global $CONN,$PHP_SELF,$class_year,$conID,$weekN,$school_menu_p,$sections,$midnoon;

	$teacher_name=get_teacher_name($tsn);

	$double_class=array();
	$kk=array();
	
	//�C�g�����
	$dayn=sizeof($weekN)+1;

	//��X�Юv�Ӧ~�שҦ��ҵ{
	$sql_select = "select course_id,class_id,day,sector,ss_id,room,c_kind from score_course where year='$sel_year' and semester='$sel_seme' and (teacher_sn='$tsn' or cooperate_sn='$tsn') order by day,sector";
	$recordSet=$CONN->Execute($sql_select) or user_error("���~�T���G",$sql_select,256);
	while (list($course_id,$class_id,$day,$sector,$ss_id,$room,$c_kind)= $recordSet->FetchRow()) {
		$k=$day."_".$sector;
		$a[$k]=$ss_id;
		$b[$k]=$class_id;
		$room[$k]=$room;
		$course_id_arr[$k]=$course_id;
		//�O���O�_���ݽ�  0:�@��  1:�ݽ�
		$c_kind_arr[$k]=$c_kind;		
		
		//�Y�O����`�Ʀ����ƪ������_��
		if(in_array($k,$kk))$double_class[]=$k;

		//��Ҧ�����`�Ʃ��}�C
		$kk[]=$k;
	}

	//���o�P�����@�C
	for ($i=1;$i<=count($weekN); $i++) {
		$main_a.="<td align='center' >�P��".$weekN[$i-1]."</td>";
	}

	//���o�`�ƪ��̤j��
	$sections=get_most_class($sel_year,$sel_seme);


	//���o�Ҫ�
	for ($j=1;$j<=$sections;$j++){

		if ($j==$midnoon){
			$all_class.= "<tr bgcolor='white'><td colspan='$dayn' align='center'>�ȥ�</td></tr>\n";
		}


		$all_class.="<tr bgcolor='#FBEC8C'><td align='center'>$j</td>";

		//�C�L�X�U�`
		for ($i=1;$i<=count($weekN); $i++) {

			$k2=$i."_".$j;

			//���o�Z�Ÿ��
			$the_class=get_class_all($b[$k2]);
			$class_name=($the_class[name]=="�Z")?"":$the_class[name];

			//���
			$subject_show="<font size=3>".get_ss_name("","","�u",$a[$k2])."</font>";

			//�Z�O
			if ($b[$k2]) 
			   $class_show="<font size=2><a href='index.php?sel_year=$sel_year&sel_seme=$sel_seme&class_id=$b[$k2]'>$class_name</a></font>";
			else 
			   $class_show="" ;   

			//�Y�O�Ӥ���`�Ʀ��b���ư}�C�z�A�q�X���⩳��
			$d_color=(in_array($k2,$double_class))?"red":"white";
			
			
			//�Y�O�ݽ�  �h��ܡ�
			if($c_kind_arr[$k2]){
				$c_kind_message='��';
				$c_kind_value='0';
			} else {
				$c_kind_message='';
				$c_kind_value='1';
			}

			//�C�@��
			$this_course_id=$course_id_arr[$k2];
			//$java_script="onMouseOver=\"this.style.cursor='hand'; this.style.backgroundColor='#aaaaff';\" onMouseOut=\"this.style.backgroundColor='$stud_sex_color';\" ondblclick='document.myform.edit_sn.value=\"$student_sn\"; document.myform.submit();'";
			if(checkid($_SERVER['SCRIPT_FILENAME'],1)) $java_script="onMouseOver=\"this.style.cursor='hand'; this.style.backgroundColor='#aaaaff';\" onMouseOut=\"this.style.backgroundColor='#ffffff';\" ondblclick='document.myform.course_id.value=\"$this_course_id\"; document.myform.course_id_kind.value=\"$c_kind_value\"; document.myform.submit();'";
			$all_class.="<td align='center'  width=110 bgcolor='$d_color' $java_script>
			$class_show<br>
			$c_kind_message$sub$subject_show$c_kind_message<br>
			<!--<input type='text' name='room' value='".$room[$i][$j]."' size='10'>-->
			</td>\n";
		}

		$all_class.= "</tr>\n" ;
	}


	//�ӯZ�Ҫ�
	$main_class_list="
	<tr bgcolor='#FBDD47'><td colspan=6>�y".$teacher_name."�z�½��`���]�Y���X�{���⩳��A���ܸӰ�Ҧ��İ�F�Y��ئW�٦����A���ܸӸ`���ݽҡC�^</td></tr>
	<tr bgcolor='#FBF6C4'><td align='center'>�`</td>$main_a</tr>
	$all_class";

	$main="<input type='hidden' name='course_id' value=''><input type='hidden' name='course_id_kind' value=''>
	<table border='0' cellspacing='1' cellpadding='4' bgcolor='#D06030' width='80%'>
	$main_class_list
	</table></form>
	";
	return  $main;
}


//���o���~�Юv���U�Կ��(�u�X�{���ƽҪ�)
function &select_teacher_in_course($col_name="teacher_sn",$teacher_sn="",$enable='1',$sel_year="",$sel_seme="",$jump_fn="",$day="",$sector=""){
	global $CONN;
	if (!$CONN) user_error("��Ʈw�s�u���s�b�I���ˬd�����]�w�I",256);
	if(empty($sel_year))$sel_year = curr_year(); //�ثe�Ǧ~
	if(empty($sel_seme))$sel_seme = curr_seme(); //�ثe�Ǵ�
	
	$option="<option value='0'></option>";
	$jump=(!empty($jump_fn))?" onChange='$jump_fn()'":"";
	

	//���ƽұЮv sn �аO
	$sql_select2 =" SELECT teacher_sn FROM score_course where teacher_sn<>'0'  and year='$sel_year' and  semester='$sel_seme'  group by teacher_sn " ;
  $recordSet2=$CONN->Execute($sql_select2) or trigger_error($sql_select2, E_USER_ERROR);
  while (list($tsn)= $recordSet2->FetchRow()) {
  	$sn_list[$tsn]=1 ;
  }	
  
	//����X�Ҧ��Юv���}�C�A�P�_�O�_���ƽ�
	$sql_select = "select name,teacher_sn from teacher_base where teach_condition='0' order by name";
	$recordSet=$CONN->Execute($sql_select);
	while (list($name,$tsn)= $recordSet->FetchRow()) {
		if ( $sn_list[$tsn] == 1) { //���ƽҪ�
		   $selected=($tsn==$teacher_sn)?"selected":"";
		   $option.="<option value='$tsn' $selected style='color: $color'>$name</option>\n";
		}   
	}		   

	
	$select_teacher="
	<select name='$col_name' $jump>
	$option
	</select>";
	return $select_teacher;
}

?>
