<?php

// $Id: chart_e.php 7710 2013-10-23 12:40:27Z smallduh $

/* ���o�]�w�� */
include "config.php";

sfs_check();
if ($IS_JHORES=='6') header("location: chart_j.php");

//�V�O�{��
$oth_arr_score = array("���{�u��"=>5,"���{�}�n"=>4,"���{�|�i"=>3,"�ݦA�[�o"=>2,"���ݧ�i"=>1);
$oth_arr_score_2 = array(5=>"���{�u��",4=>"���{�}�n",3=>"���{�|�i",2=>"�ݦA�[�o",1=>"���ݧ�i");


// ���ݭn register_globals
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}

$year_seme=($_POST[year_seme])?$_POST[year_seme]:$_GET[year_seme];
$class_id=($_POST[class_id])?$_POST[class_id]: $_GET[class_id];
$student_sn=($_POST[student_sn])?$_POST[student_sn]:$_GET[student_sn];
$act=($_POST[act])?$_POST[act]:$_GET[act];
$stu_num=($_POST[stu_num])?$_POST[stu_num]:$_GET[stu_num];

//�Y����X�ɮת��A, ��X���T�Ǵ�
if (($class_id)&&($act)) {
	$c=explode("_",$class_id);
	$year_seme=$c[0].$c[1];
}

//���Ǵ�
if (empty($year_seme)) {
	$sel_year = curr_year(); //�ثe�Ǧ~
	$sel_seme = curr_seme(); //�ثe�Ǵ�
	$year_seme=sprintf("%03s%1s",$sel_year,$sel_seme);
} else {
	$sel_year=intval(substr($year_seme,0,-1));
	$sel_seme=substr($year_seme,-1,1);
}

//���Z��
if ($student_sn != "") {
	$sql="select seme_class from stud_seme where student_sn='$student_sn' and seme_year_seme='$year_seme'";
	$rs=$CONN->Execute($sql);
	$stud_num=$rs->fields['seme_class'];
}

if ($class_id=="") {
	// �Q�� $IS_JHORES �� �Ϲj �ꤤ�B��p�B���� ���w�]��
	if ($stud_num) $class_num=$stud_num;
	else {
		$year_name=$IS_JHORES+1;
		$sql="select seme_class,student_sn from stud_seme where seme_year_seme='$year_seme' order by seme_class, seme_num";
		$rs=$CONN->Execute($sql);
		while (!$rs->EOF) {
			$class_num=$rs->fields['seme_class'];
			if ($class_num) {
				$student_sn=$rs->fields['student_sn'];
				if ($student_sn) {
					$res=$CONN->Execute("select * from stud_base where student_sn='$student_sn'");
					if ($res->RecordCount()>0) break;
				}
			}
			$rs->MoveNext();
		}
	}
} else {
	$temp_curr_class_arr = explode("_",$class_id); //091_1_02_03
	$class_num = intval($temp_curr_class_arr[2].$temp_curr_class_arr[3]);
	$sql="select seme_class from stud_seme where seme_year_seme='$year_seme' and seme_class='$class_num' order by seme_class";
	$rs=$CONN->Execute($sql);
	if (!$rs->fields['seme_class']) {
		$sql="select seme_class from stud_seme where seme_year_seme='$year_seme' order by seme_class";
		$rs=$CONN->Execute($sql);
		$class_num=$rs->fields['seme_class'];
	}
	if ($stud_num != $class_num) $student_sn="";
}

//�T�w���ǥͦW��
if(!$class_num) die("<font color='red' size=4><BR>�t�Υ���쥻�Ǵ����Z�žǥͦW��A<BR>��<a href='../stud_year/'>�����s����[�s�Z�@�~]�Ҳ�</a>�i��B�z!</font>");

//���o�Z�ťN��
$class_all=class_num_2_all($class_num);
$class_id=old_class_2_new_id($class_num,$sel_year,$sel_seme);

//���o���Ǵ��W���`���
$query = "select days from seme_course_date where seme_year_seme='$year_seme' and class_year=".substr($class_num,0,1);
$res= $CONN->Execute($query) or die($query);
$TOTAL_DAYS = $res->fields[0];

//���o�Ҹռ˪O�s��
$exam_setup=&get_all_setup("",$sel_year,$sel_seme,$class_all[year]);
$interface_sn=$exam_setup[interface_sn];

//����ʧ@�P�_
if($act=="�s��" || $act=="�x�s�ק�")
	save_value();
elseif($act=="dlar") {
	downlod_ar($student_sn,$class_id,$interface_sn,$stu_num,$sel_year,$sel_seme);
	exit;
} elseif($act=="dlar_all") {
	downlod_ar("",$class_id,$interface_sn,"",$sel_year,$sel_seme,"all");
	exit;
}

if($act=="error")
	$main=&error_tbl($error_title,$error_main);
else
	$main=&main_form($interface_sn,$sel_year,$sel_seme,$class_id,$student_sn);

//�q�X����
head("�s�@���Z��");
print_menu($school_menu_p);

?>

<script language="JavaScript">
<!-- Begin
function jumpMenu(){
	location="<?php echo $_SERVER['SCRIPT_NAME']?>?act=<?php echo $act;?>&student_sn=" + document.col1.student_sn.options[document.col1.student_sn.selectedIndex].value;
}
//  End -->
</script>

<?php

echo $main;
foot();

//�[�ݼҪO
function &main_form($interface_sn="",$sel_year="",$sel_seme="",$class_id="",$student_sn=""){
	global $CONN,$input_kind,$school_menu_p,$cq,$comm,$chknext,$nav_next,$edit_mode,$submit;

	$year_seme=sprintf("%03s%1s",$sel_year,$sel_seme);
	$c=explode("_",$class_id);
	$seme_class=$c[2].$c[3];
	if (substr($seme_class,0,1)=="0") $seme_class=substr($seme_class,1,strlen($seme_class)-1);
	
	//�ഫ�Z�ťN�X
	$class=class_id_2_old($class_id);
	
	//���p�S�����w�ǥ͡A���o�Ĥ@��ǥ�
	if(empty($student_sn)) {
		$sql="select student_sn from stud_seme where seme_year_seme='$year_seme' and seme_class='$seme_class' order by seme_num";
		$rs=$CONN->Execute($sql);
		$student_sn=$rs->fields['student_sn'];
	}

	//�Y���O�S��$stud_id�A�h�q�X���~�T��
	if(empty($student_sn))header("location:{$_SERVER['SCRIPT_NAME']}?error=1");
	
	if ($chknext && $nav_next<>'') $student_sn = $nav_next;
	
	//�D�o�ǥ�ID
	$query="select stud_id from stud_base where student_sn='$student_sn'";
	$res=$CONN->Execute($query);
	$stud_id=$res->fields['stud_id'];

	//���o�Ӿǥͤ�`�ͬ����{���q��
	$oth_data=&get_oth_value($stud_id,$sel_year,$sel_seme);
	
	//���o�ǥͤ�`�ͬ����{���Ƥξɮv���y��ĳ
	$nor_data=get_nor_value($student_sn,$sel_year,$sel_seme);

	//���o�ǥͯʮu���p
	$abs_data=get_abs_value($stud_id,$sel_year,$sel_seme);
	
	//�ǥͼ��g���p
	$reward_data = get_reward_value($stud_id,$sel_year,$sel_seme);	

	//���o�ǥͦ��Z��
	$score_data = &get_score_value($stud_id,$student_sn,$class_id,$oth_data);
	
	if(count($oth_data['�ͬ����{���q'])>0){
		$edit_mode="update";
		$submit="�x�s�ק�";
	}else{
		$edit_mode="add";
		$submit="�s��";
	}

	//���o�ԲӸ��
	$html=&html2code2($class,$sel_year,$sel_seme,$oth_data,$nor_data,$abs_data,$reward_data,$score_data,$student_sn);
	
	$gridBgcolor="#DDDDDC";
	//�w�s�@����C��
	$over_color = "#223322";
	//�����k������C��
	$non_color = "blue";

	//�Ǧ~���
	$class_seme_p = get_class_seme(); //�Ǧ~��	
	$upstr = "<select name=\"year_seme\" onchange=\"document.gridform.submit()\">\n";
	while (list($tid,$tname)=each($class_seme_p)){
		if ($year_seme== $tid)
	      		$upstr .= "<option value=\"$tid\" selected>$tname</option>\n";
	      	else
	      		$upstr .= "<option value=\"$tid\">$tname</option>\n";
	}
	$upstr .= "</select><br>"; 
	//�Z�ſ��
	$tmp=&get_class_select($sel_year,$sel_seme,"","class_id","document.gridform.submit",$class_id);
	$upstr .= $tmp;

	$grid1 = new ado_grid_menu($_SERVER['SCRIPT_NAME'],$URI,$CONN);  //�إ߿��	   	
	$grid1->key_item = "student_sn";  // ������W  	
	$grid1->display_item = array("sit_num","stud_name");  // �����W   
	$grid1->bgcolor = $gridBgcolor;
	$grid1->display_color = array("1"=>"blue","2"=>"red");
	$grid1->color_index_item ="stud_sex" ; //�C��P�_��
	$grid1->class_ccs = " class=leftmenu";  // �C�����
	$grid1->sql_str = "select a.student_sn,a.stud_name,a.stud_sex,b.seme_num as sit_num $stud_id_temp from stud_base a,stud_seme b where a.student_sn=b.student_sn and (a.stud_study_cond=0 or a.stud_study_cond=5) and b.seme_year_seme='$year_seme' and b.seme_class='$seme_class' order by b.seme_num";   //SQL �R�O
	$grid1->do_query(); //����R�O 

	$stud_select = $grid1->get_grid_str($student_sn,$upstr,$downstr); // ��ܵe��

	//���o���w�ǥ͸��
	$stu=get_stud_base($student_sn,"");

	//�y��
	$sql="select seme_num from stud_seme where seme_year_seme='$year_seme' and student_sn='$student_sn'";
	$rs=$CONN->Execute($sql);
	$stu_class_num=$rs->fields['seme_num'];
	
	//���o�Ǯո��
	$s=get_school_base();

	if ($use_both) $tool_bar=&make_menu($school_menu_p);
	
	$checked=($chknext)?"checked":"";

	$main="
	<script language=\"JavaScript\">
	var remote=null;
	function OpenWindow(p,x){
	strFeatures =\"top=300,left=20,width=500,height=200,toolbar=0,resizable=yes,scrollbars=yes,status=0\";
	remote = window.open(\"comment.php?cq=\"+p,\"MyNew\", strFeatures);
	if (remote != null) {
	if (remote.opener == null)
	remote.opener = self;
	}
	if (x == 1) { return remote; }
	}
	function OpenWindow2(p,x){
	strFeatures =\"top=0,left=0,width=600,height=400,toolbar=0,resizable=yes,scrollbars=yes,status=0\";
	remote = window.open(\"quick_input_memo.php?cq=\"+p,\"MyNew\", strFeatures);
	if (remote != null) {
	if (remote.opener == null)
	remote.opener = self;
	}
	if (x == 1) { return remote; }
	}
	function checkok() {
	document.col1.nav_next.value = document.gridform.nav_next.value;	
	return true;	
	}
	
	</script>
	$tool_bar
	<table bgcolor='#DFDFDF' cellspacing=1 cellpadding=4>
	<tr class='small'><td valign='top'>$stud_select
	<p><a href='{$_SERVER['SCRIPT_NAME']}?act=dlar&student_sn=$student_sn&stu_num=$stu_class_num&class_id=$class_id'>�U��".$stu[stud_name]."�����Z��</a></p>
	<p><a href='{$_SERVER['SCRIPT_NAME']}?act=dlar_all&class_id=$class_id'>�U�����Z�����Z��</a></p>
	
	<form action='{$_SERVER['SCRIPT_NAME']}' method='post' name='col1'>
	<input type='checkbox' name='chknext' value='1' $checked>�۰ʸ��U�@��
	</td><td bgcolor='#FFFFFF' valign='top'>
	<p align='center'>
	<font size=3>".$s[sch_cname]." ".$sel_year."�Ǧ~�ײ�".$sel_seme."�Ǵ����Z��</p>
	<table align=center cellspacing=4>
	<tr>
	<td>�Z�šG<font color='blue'>$class[5]</font></td><td width=40></td>
	<td>�y���G<font color='green'>$stu_class_num</font></td><td width=40></td>
	<td>�m�W�G<font color='red'>$stu[stud_name]</font></td>
	</tr></table></font>
	$html
	<input type='hidden' name='student_sn' value='$student_sn'>
	<input type='hidden' name='year_seme' value='$year_seme'>
	<input type='hidden' name='class_id' value='$class_id'>
	<input type='hidden' name='nav_next' ><br><div align='center'>
	<input type='submit' name='act' value='$submit' onClick='return checkok();'>
	</div>
	</form>
	</td></tr></table>
	";

	return $main;
}


//�x�s��
function save_value(){
	global $CONN;
	//��`�ͬ����{�s��
	$seme_year_seme = $_POST['year_seme'];
	for ($i=1;$i<=4;$i++){
		$query = "replace into stud_seme_score_oth (seme_year_seme,stud_id,ss_kind,ss_id,ss_val) values('$seme_year_seme','$_POST[stud_id]','�ͬ����{���q','$i','".$_POST["a_$i"]."')";
		$CONN->Execute($query) or trigger_error("sql ���~ $query",E_USER_ERROR);		
	}
	//��اV�O�{�צs��
	//$temp_ss_id_arr = explode(",",$_POST[hidden_ss_id]);
//	while(list($id,$val)=each($temp_ss_id_arr)){
//		if ($val<>''){
//			$query = "replace into stud_seme_score_oth (seme_year_seme,stud_id,ss_kind,ss_id,ss_val)values('$seme_year_seme','$_POST[stud_id]','�V�O�{��','$val','".$_POST["ss_$val"]."')";
//			$CONN->Execute($query) or trigger_error("sql ���~ $query",E_USER_ERROR);
//		}
//	}
	//�ɮv���y�Ϋ�ĳ�ε���
	$query = "replace into stud_seme_score_nor (seme_year_seme,student_sn,ss_id,ss_score,ss_score_memo) values('$seme_year_seme','$_POST[student_sn]',0,'$_POST[nor_score]','$_POST[nor_score_memo]')";
	$CONN->Execute($query) or trigger_error("sql ���~ $query",E_USER_ERROR);
	
	//�ǥͥX�ʮu
	$abs_kind_arr = stud_abs_kind();
	while(list($id,$val)=each($abs_kind_arr)) {
		$query = "replace into stud_seme_abs(seme_year_seme,stud_id,abs_kind,abs_days) values('$seme_year_seme','$_POST[stud_id]','$id','".$_POST["abs_$id"]."')";
		$CONN->Execute($query) or trigger_error("sql ���~ $query",E_USER_ERROR);

	}
	//�ǥͼ��g
	 $rep_kind_arr = stud_rep_kind();
	while(list($id,$val)=each($rep_kind_arr)) {
		$query = "replace into stud_seme_rew(seme_year_seme,stud_id,sr_kind_id,sr_num) values('$seme_year_seme','$_POST[stud_id]','$id','".$_POST["reward_$id"]."')";
		$CONN->Execute($query) or trigger_error("sql ���~ $query",E_USER_ERROR);
	}
	
	//��L�]�w
	$query = "replace into stud_seme_score_oth (seme_year_seme,stud_id,ss_kind,ss_id,ss_val) values('$seme_year_seme','$_POST[stud_id]','��L�]�w',0,'$_POST[oth_rep]')";
	$CONN->Execute($query) or trigger_error("sql ���~ $query",E_USER_ERROR);
	return true;

}

//��s��
function update_value(){
	return;	
}

// ���o���Z��XML
function &get_score_xml_value($stud_id,$student_sn,$class_id,$oth_data) {
	global $CONN,$oth_arr_score,$oth_arr_score_2;
	//�s�W�@�� zipfile ���
	$ttt = new EasyZip;
	
	$class=class_id_2_old($class_id);
	// ���o���~�Ū��ҵ{�}�C
	$ss_name_arr = &get_ss_name_arr($class);
	// ���o�V�O�{�פ�r�ԭz
//	$arr_1 = sfs_text("�V�O�{��");
	// ���o�ҵ{�C�g�ɼ�
	$ss_num_arr = get_ss_num_arr($class_id);
	$ss_sql_select = "select ss_id,rate,link_ss from score_ss where enable='1' and class_id='$class_id' and need_exam='1' order by sort,sub_sort";
	$ss_recordSet=$CONN->Execute($ss_sql_select) or user_error("Ū�����ѡI<br>$ss_sql_select",256);
	if ($ss_recordSet->RecordCount() ==0){
		$ss_sql_select = "select ss_id,rate,link_ss from score_ss where enable='1' and year='$class[0]' and semester='$class[1]' and need_exam='1' and class_id='' order by sort,sub_sort";
		$ss_recordSet=$CONN->Execute($ss_sql_select) or user_error("Ū�����ѡI<br>$ss_sql_select",256);
	}
	$hidden_ss_id='';
	$temp_9_arr = array();
	while ($SS=$ss_recordSet->FetchRow()) {		
		$ss_id=$SS[ss_id];
		$link_ss=$SS[link_ss];
		$rate=$SS[rate];
		$ss_name= $ss_name_arr[$ss_id];
		$temp_9_arr[$link_ss][ss_hours] += $ss_num_arr[$ss_id];
		$temp_9_arr[$link_ss][ss_score] += $ss_score_arr[$ss_id][ss_score]*$rate;
		$temp_9_arr[$link_ss][rate] += $rate;
		$oth_data_rate = 0;
		$temp_9_arr[$link_ss][oth_data] += $oth_arr_score[$oth_data["�V�O�{��"]["$ss_id"]]*$rate;
		if ($ss_score_arr[$ss_id][ss_score_memo]<>'')
			$temp_9_arr[$link_ss][ss_score_memo] .= "$ss_name :".$ss_score_arr[$ss_id][ss_score_memo]."<br/>";
		//if ($temp_sel=='')
		//	$temp_sel = "--";
	}
	while(list($id,$val)=each($temp_9_arr)){			
		$score_temp = $val[ss_score]/$val[rate];
		$score_oth = $oth_arr_score_2[round($val[oth_data]/$val[rate],0)];
		if ($score_temp>0)
			$score_temp_str = round($score_temp,0);
		else
			$score_temp_str ='';
		$score_memo = score2str($score_temp,$class);
		$ss_id=$SS[ss_id];
		$ss_name= $ss_name_arr[$ss_id];
		$res_str.="<table:table-row table:style-name=\"ss_table.1\"><table:table-cell table:style-name=\"ss_table.A2\" table:value-type=\"string\"><text:p text:style-name=\"P5\">$id</text:p></table:table-cell><table:table-cell table:style-name=\"ss_table.A2\" table:value-type=\"string\"><text:p text:style-name=\"P5\">$val[ss_hours]</text:p></table:table-cell><table:table-cell table:style-name=\"ss_table.A2\" table:value-type=\"string\"><text:p text:style-name=\"P5\">".$score_oth."</text:p></table:table-cell><table:table-cell table:style-name=\"ss_table.A2\" table:value-type=\"string\"><text:p text:style-name=\"P5\">".$score_memo."</text:p></table:table-cell><table:table-cell table:style-name=\"ss_table.E2\" table:value-type=\"string\"><text:p text:style-name=\"P11\">".$ttt->xml_reference_change(substr($val[ss_score_memo],0,-5))."</text:p></table:table-cell></table:table-row>";


//		$temp_str .= "<tr bgcolor='white'>
//			<td>$id</td>
//			<td align='center'>$val[ss_hours]�`</td>			
//			<td nowrap align='center'>".$score_oth."</td>
//			<td align='center'>$score_memo $score_temp_str</td>
//			<td>".substr($val[ss_score_memo],0,-5)."</td>
//			</tr>";
		//�Nss_id ��b hidden 	
	}



	// ���o�ǲߦ��N
	$ss_score_arr =get_ss_score_arr($class,$student_sn);

	$ss_sql_select = "select ss_id from score_ss where enable='1' and  year='$class[0]' and semester='$class[1]' and ((class_year='$class[3]' and class_id='') or class_id='$class_id')  and  need_exam='1' and enable=1 order by sort,sub_sort";
	$ss_recordSet=$CONN->Execute($ss_sql_select) or user_error("Ū�����ѡI<br>$ss_sql_select",256);
	$res_str = "";
	while ($SS=$ss_recordSet->FetchRow()) {		
		$ss_id=$SS[ss_id];
		$ss_name= $ss_name_arr[$ss_id];
		$res_str.="<table:table-row table:style-name=\"ss_table.1\"><table:table-cell table:style-name=\"ss_table.A2\" table:value-type=\"string\"><text:p text:style-name=\"P5\">$ss_name</text:p></table:table-cell><table:table-cell table:style-name=\"ss_table.A2\" table:value-type=\"string\"><text:p text:style-name=\"P5\">$ss_num_arr[$ss_id]</text:p></table:table-cell><table:table-cell table:style-name=\"ss_table.A2\" table:value-type=\"string\"><text:p text:style-name=\"P5\">".$oth_data["�V�O�{��"]["$ss_id"]."</text:p></table:table-cell><table:table-cell table:style-name=\"ss_table.A2\" table:value-type=\"string\"><text:p text:style-name=\"P5\">".$ss_score_arr[$ss_id][score_name]."</text:p></table:table-cell><table:table-cell table:style-name=\"ss_table.E2\" table:value-type=\"string\"><text:p text:style-name=\"P11\">".$ttt->xml_reference_change($ss_score_arr[$ss_id][ss_score_memo])."</text:p></table:table-cell></table:table-row>";

	}

	return $res_str;
}

//�U�����Z��
function downlod_ar($student_sn="",$class_id="",$interface_sn="",$stu_num="",$sel_year="",$sel_seme="",$mode=""){
	global $CONN,$UPLOAD_PATH,$UPLOAD_URL,$SFS_PATH_HTML,$line_color,$line_width,$draw_img_width,$draw_img_height,$TOTAL_DAYS,$sign_1_form,$sign_2_form,$sign_1_name,$sign_2_name;
	
	//Openofiice�����|
	$oo_path = "ooo/e";
	
	//�ɦW����
	if($mode=="all"){
		$filename="score_".$class_id.".sxw";
	}else{
		$filename="score_".$class_id."_".$stu_num.".sxw";
	}
	
	//�ഫ�Z�ťN�X
	$class=class_id_2_old($class_id);
	$class_num=$class[2];
	$year_seme=sprintf("%03s%1s",$class[0],$class[1]);

	//���o�Ƶ�����
	$memo_temp_str = &say_rule_2($class);
	
	//���o�Ǯո��
	$s=get_school_base();

	$memo_temp_str = "<text:p text:style-name=\"P11\">���Ǵ��W�Ҥ��: $TOTAL_DAYS �� </text:p>".$memo_temp_str;
	
	//���� tag
	$break ="<text:p text:style-name=\"break_page\"/>";
	if ($draw_img_width=='') $draw_img_width="1.27cm";
	if ($draw_img_height=='') $draw_img_height="1.27cm";
	//�ժ�ñ����
	if ($sign_1_form=="" || $sign_1_form==1) {
		if (is_file($UPLOAD_PATH."school/title_img/title_1")){
			$title_img = "http://".$_SERVER["SERVER_ADDR"]."/".$UPLOAD_URL."school/title_img/title_1";
			$sign_1 ="<draw:image draw:style-name=\"fr1\" draw:name=\"aaaa1\" text:anchor-type=\"paragraph\" svg:x=\"0.73cm\" svg:y=\"0.161cm\" svg:width=\"$draw_img_width\" svg:height=\"$draw_img_height\" draw:z-index=\"0\" xlink:href=\"$title_img\" xlink:type=\"simple\" xlink:show=\"embed\" xlink:actuate=\"onLoad\"/>";
		}
	} elseif ($sign_1_form==2) {
		$sign_1=$sign_1_name;
	}
	//�аȥD��ñ����
	if ($sign_2_form=="" || $sign_2_form==1) {
		if (is_file($UPLOAD_PATH."school/title_img/title_2")){
			$title_img = "http://".$_SERVER["SERVER_ADDR"]."/"."$UPLOAD_URL"."school/title_img/title_2";
			$sign_2 = "<draw:image draw:style-name=\"fr2\" draw:name=\"bbbb1\" text:anchor-type=\"paragraph\" svg:x=\"0.727cm\" svg:y=\"0.344cm\" svg:width=\"$draw_img_width\" svg:height=\"$draw_img_height\" draw:z-index=\"1\" xlink:href=\"$title_img\" xlink:type=\"simple\" xlink:show=\"embed\" xlink:actuate=\"onLoad\"/>";
		}
	} elseif ($sign_2_form==2) {
		$sign_2=$sign_2_name;
	}
	
	$arr_1 = sfs_text("��`�欰���{");
	$arr_2 = sfs_text("���鬡�ʪ��{");
	$arr_3 = sfs_text("���@�A�Ȫ��{");
	$arr_4 = sfs_text("�ե~�S�����{");
	//���O
	$abs_kind_arr = stud_abs_kind();
	//���g
	$rep_kind_arr = stud_rep_kind();

	
	//�s�W�@�� zipfile ���
	$ttt = new EasyZip;
	$ttt->setPath($oo_path);	
	$ttt->addDir("META-INF");
	$ttt->addfile("settings.xml");
	$ttt->addfile("styles.xml");
	$ttt->addfile("meta.xml");

//�Z�Ÿ�ơq�Y�O��H�A�h�q��H��ơr
	$where=($mode=="all")?"where (a.stud_study_cond=0 or a.stud_study_cond=5) and  b.seme_year_seme='$year_seme' and b.seme_class='$class_num' order by b.seme_num ":"where b.student_sn='$student_sn' and b.seme_year_seme='$year_seme'";
	$query = "select a.stud_id,a.stud_name,a.student_sn,b.seme_num from stud_base a RIGHT join stud_seme b on a.student_sn=b.student_sn $where";
	$res = $CONN->Execute($query)or trigger_error($query, E_USER_ERROR);
	while (list($stud_id,$stud_name,$student_sn,$stu_num)=$res->FetchRow()) {
		//Ū�X content.xml 
		$content_body = $ttt->read_file(dirname(__FILE__)."/$oo_path/content_body.xml");

		$sign2_arr["SIGN_1"] = $sign_1;
		$sign2_arr["SIGN_2"] = $sign_2;
		$content_body = $ttt->change_temp($sign2_arr,$content_body,0);
		//$stu_num= intval (substr($stu_num,-2));
		
		//�N content.xml �� tag ���N
		$temp_arr["city_name"] = $s[sch_sheng];	
		$temp_arr["school_name"] = $s[sch_cname];
		$temp_arr["stu_class"] = $class[5];
		$temp_arr["year"] = $sel_year;
		$temp_arr["seme"] = $sel_seme;
		$temp_arr["stu_name"] = $ttt->change_str($stud_name,1,0);
		$temp_arr["stu_num"] = $stu_num;

		//���o�Ӿǥͤ�`�ͬ����{���q��
		$oth_data=&get_oth_value($stud_id,$sel_year,$sel_seme);
		$temp_arr["2"] = $oth_data['�ͬ����{���q'][1];
		$temp_arr["3"] = $oth_data['�ͬ����{���q'][2];
		$temp_arr["4"] = $oth_data['�ͬ����{���q'][3];
		$temp_arr["5"] = $oth_data['�ͬ����{���q'][4];
		
		//���o�ǥͯʮu���p
		$abs_data=get_abs_value($stud_id,$sel_year,$sel_seme);
		reset($abs_kind_arr);
		$i=9;	
		while(list($id,$val)=each($abs_kind_arr)){
			($abs_data[$id]==0)?$temp_i="":$temp_i=$abs_data[$id];
			$temp_arr["$i"] = $temp_i;
			$i++;
		}

		reset($rep_kind_arr);
		//�ǥͼ��g���p
		$reward_data = get_reward_value($stud_id,$sel_year,$sel_seme);
		$i=16;
		while(list($id,$val)=each($reward_data)){
			($reward_data[$id]==0)?$temp_i="":$temp_i=$reward_data[$id];
			$temp_arr["$i"] = $temp_i;
			$i++;
		}


		//���o�ǥͤ�`�ͬ����{���Ƥξɮv���y��ĳ
		$nor_data=get_nor_value($student_sn,$sel_year,$sel_seme);
		$temp_arr["6"] = $ttt->change_str($nor_data[ss_score_memo],1,0);
		$temp_arr["7"] = score2str($nor_data[ss_score],$class);	

		//���o��L�r��
		$temp_arr[22] = $oth_data['��L�]�w'][0];
	
		//���o�ǥͦ��Z��
		$temp_arr_score["ss_table"] = &get_score_xml_value($stud_id,$student_sn,$class_id,$oth_data);
	
		$temp_arr["MEMO"] = $ttt->change_str($memo_temp_str,1,0);
		
		//����
		if($mode=="all")	$content_body .= $break;
		$content_body = $ttt->change_temp($temp_arr_score,$content_body,0);
		// change_temp �|�N�}�C���� big5 �ର UTF-8 �� openoffice �i�HŪ�X
		$replace_data .= $ttt->change_temp($temp_arr,$content_body,0);
	
	}
	//echo $replace_data;
	//exit;
	//Ū�X XML ���Y
	$doc_head = $ttt->read_file(dirname(__FILE__)."/$oo_path/content_head.xml");
	if ($line_width<>'') {
		$sign_arr["0.002cm solid #000000"] = "$line_width solid $line_color";
		//�ﴫ��u�e��
		$doc_head = $ttt->change_sigle_temp($sign_arr,$doc_head);
	}
	//Ū�X XML �ɧ�
	$doc_foot = $ttt->read_file(dirname(__FILE__)."/$oo_path/content_foot.xml");

	$replace_data =$doc_head.$replace_data.$doc_foot;
	
	// �[�J content.xml ��zip ��
	$ttt->add_file($replace_data,"content.xml");
	
	//���� zip ��
	$sss = & $ttt->file();

	//�H��y�覡�e�X sxw
	header("Content-disposition: attachment; filename=$filename");
	header("Content-type: application/vnd.sun.xml.writer");
	//header("Pragma: no-cache");
				//�t�X SSL�s�u�ɡAIE 6,7,8�U�������D�A�i��ק� 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
	header("Expires: 0");

	echo $sss;
	
	exit;
	return;
}
?>