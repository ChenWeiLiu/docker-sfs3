<?php

// $Id: stud_list.php 9223 2018-04-23 04:54:59Z smallduh $

// ���J�]�w��
include "stud_reg_config.php";

// �{���ˬd
sfs_check();


// ���ݭn register_globals
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}
if ($stud_person_id) {
	$stud_person_id = strtoupper($stud_person_id) ;
	$edu_key =  hash('sha256', strtoupper($stud_person_id));
}
//$dd=explode("-",$stud_birthday);
//$stud_birthday=sprintf("%04d-%02d-%02d",$dd[0]+1911,$dd[1],$dd[2]);

// �ˬd php.ini �O�_���} file_uploads ?
check_phpini_upload();

//����B�z 
switch ($do_key){
	case $editBtn: //�ק�
	if ($same_key) {
		$addr = $stud_addr_1;
		$ttt = change_addr_str($addr);
		$stud_addr_a = $ttt[0];
		$stud_addr_b = $ttt[1];
		$stud_addr_c = $ttt[2];
		$stud_addr_d = $ttt[3];
		$stud_addr_e = $ttt[4];
		$stud_addr_f = $ttt[5];
		$stud_addr_g = $ttt[6];
		$stud_addr_h = $ttt[7];
		$stud_addr_i = $ttt[8];
		$stud_addr_j = $ttt[9];
		$stud_addr_k = $ttt[10];
		$stud_addr_l = $ttt[11];
	}
		
		$student_sn=trim($student_sn);
		$query="select * from stud_base where student_sn='$student_sn'";
		$res=$CONN->Execute($query);
		$stud_id=trim($res->fields['stud_id']);
		$new_stud_id=trim ($new_stud_id);
		//�P�_�Ǹ��O�_�����
		if($stud_id!==$new_stud_id and $new_stud_id<>''){
			//�p�G���諸�ܡA�ݬݬO�_�s�Ǹ����S���H�ΤF
			$sql_a="select count(*) from stud_base where stud_id='$new_stud_id' ";
			$rs_a=$CONN->Execute($sql_a) or trigger_error($sql_a,256);
			$cou=$rs_a->fields[0];
			if($cou>0){
				//�ӾǸ��w�Q�ϥΤF�A�]�����i��Ǹ����ק�A�é󭶭��W�i��
				$msg="<font color='red'>$new_stud_id �w�g���H�ϥΡA�]�����i��Ǹ����ק�I�I</font><br>";
			}
			elseif($cou==0){
				//$stud_id=$new_stud_id;
				//�N�Ҧ��Pstud_id��������ƪ��i��ק�
				$sql_sfs3="SHOW TABLES FROM $mysql_db";
				$rs_sfs3=$CONN->Execute($sql_sfs3) or trigger_error($sql_sfs3,256);
				if ($rs_sfs3) {
					while( $ar_sfs3 = $rs_sfs3->FetchRow() ) {			
						$sql_fields="show fields from {$ar_sfs3[0]}";
						$rs_fields=$CONN->Execute($sql_fields) or trigger_error($sql_fields,256);
						if($rs_fields){								
							while( $ar_fields = $rs_fields->FetchRow() ) {
								//echo $ar_fields[0];
								if($ar_fields[0]=="stud_id") {
									$u1="update $ar_sfs3[0] set stud_id='$new_stud_id' where stud_id='$stud_id'";
									$CONN->Execute($u1);
								}
								elseif($ar_fields[0]=="student_id"){
									$u2="update $ar_sfs3[0] set student_id='$new_stud_id' where student_id='$stud_id'";
									$CONN->Execute($u2);
								} 
							}
						}
					}
				}
				$stud_id=$new_stud_id;
			}
		}
		//exit;
		$stud_kind_temp =",";
		$sit_num = sprintf("%02d",$sit_num);
		while(list($tid,$tname)=each($stud_kind))
			$stud_kind_temp .= $tname.",";
		$temp_num =sprintf("%s%02d",substr($curr_class_num,0,3),$sit_num);
		$sql_update = "update stud_base set stud_name='$stud_name',stud_name_eng='$stud_name_eng',stud_sex='$stud_sex',
		stud_birthday='$stud_birthday',stud_blood_type='$stud_blood_type',stud_birth_place='$stud_birth_place',
		stud_kind='$stud_kind_temp',stud_country='$stud_country',stud_country_kind='$stud_country_kind',
		stud_person_id='$stud_person_id',stud_country_name='$stud_country_name',addr_move_in='$addr_move_in',
		stud_addr_1='$stud_addr_1',stud_addr_2='$stud_addr_2',stud_tel_1='$stud_tel_1',stud_tel_2='$stud_tel_2',
		stud_tel_3='$stud_tel_3',stud_mail='$stud_mail',stud_addr_a='$stud_addr_a',stud_addr_b='$stud_addr_b',
		stud_addr_c='$stud_addr_c',stud_addr_d='$stud_addr_d',stud_addr_e='$stud_addr_e',stud_addr_f='$stud_addr_f',
		stud_addr_g='$stud_addr_g',stud_addr_h='$stud_addr_h',stud_addr_i='$stud_addr_i',stud_addr_j='$stud_addr_j',
		stud_addr_k='$stud_addr_k',stud_addr_l='$stud_addr_l',stud_addr_m='$stud_addr_m',stud_class_kind='$stud_class_kind',
		stud_spe_kind='$stud_spe_kind',stud_spe_class_kind='$stud_spe_class_kind',stud_spe_class_id='$stud_spe_class_id',
		stud_preschool_status='$stud_preschool_status',stud_preschool_id='$stud_preschool_id',
		stud_preschool_name='$stud_preschool_name',stud_Mschool_status='$stud_Mschool_status',
		stud_mschool_id='$stud_mschool_id',stud_mschool_name='$stud_mschool_name',curr_class_num='$temp_num',
		addr_zip='$addr_zip',enroll_school='$enroll_school' ,edu_key='$edu_key',obtain='$obtain',safeguard='$safeguard',experiment_kind='$experiment_kind',exp_group_name='$exp_group_name' where student_sn='$student_sn'";
		
		$sql_update=str_replace("addr_move_in=''","addr_move_in=NULL",$sql_update);
		
//echo $sql_update; 

		$CONN->Execute($sql_update) or die($sql_update);
		$CONN->Execute("update stud_seme set seme_num='$sit_num' where seme_year_seme ='$c_curr_seme' and  seme_class ='$seme_class' and student_sn='$student_sn'");                
		$upload_str = set_upload_path("$img_path/$stud_study_year");
		//���ɳB�z
		if($_FILES['stud_img']['tmp_name']){
			//�]�w�W���ɮ׸��|	
		 	copy($_FILES['stud_img']['tmp_name'], $upload_str."/".$stud_id);
		 }
		 else if ($del_img) {
		 	if (file_exists($upload_str."/$stud_id"))
				unlink($upload_str."/$stud_id");
		 } 
		//�O�� log
		sfs_log("stud_base","update","$stud_id");
	break;
	
	case new_stud: //�s�W�@��ǥ�
		if(!$year_seme) break;
		$year_seme_arr=explode("_",$year_seme);
		$seme_year_seme=sprintf("%03d%d",$year_seme_arr[0],$year_seme_arr[1]);
		$seme_class=sprintf("%d%02d",$year_seme_arr[2],$year_seme_arr[3]);
		$stud_id_arr=array();
		$seme_num_arr=array();
		$like_seme_class=substr($seme_class,0,-2);
		$sql_seme="select * from stud_seme where seme_year_seme='$seme_year_seme' and seme_class like '$like_seme_class%' ";		
		$i=0;
		$rs_seme=$CONN->Execute($sql_seme) or trigger_error($sql_seme,256);
		while(!$rs_seme->EOF){
			$stud_id_arr[$i]=$rs_seme->fields['stud_id'];			
			$seme_num_arr[$i]=$rs_seme->fields['seme_num'];
			$length=strlen($stud_id_arr[$i]);
			$i++;
			$rs_seme->MoveNext();
		}
		
		//�w�]�J�Ǧ~��
		$new_stud_study_year=$year_seme_arr[0]-$year_seme_arr[2]+1+$IS_JHORES;
		//�w�]�Ǹ�		
		if(count($stud_id_arr)==0) $new_stud_id=$new_stud_study_year."001";
		else $new_stud_id=max($stud_id_arr)+1;
		
		//�w�]�y��
		$new_seme_num=max($seme_num_arr)+1;


		//�w�]�ʧO���k
		$new_stud_sex=2;
		
		//�g����y�򥻸�ƪ�		
		$new_curr_class_num=$seme_class.sprintf("%02d",$new_seme_num);
		$sql_ins="replace into stud_base(stud_id, stud_sex, stud_study_year, curr_class_num, stud_study_cond,obtain,safeguard) values('$new_stud_id','$new_stud_sex', '$new_stud_study_year', '$new_curr_class_num','0','$obtain','$safeguard')";	
		$rs_ins=$CONN->Execute($sql_ins) or trigger_error($sql_ins,256);
		//���X��[�J���y����
		$new_student_sn=mysql_insert_id();
		//echo $sql_ins;
		
		//�g�J�Ǵ���ƪ�
		$seme_class_name=class_id_to_c_name($year_seme);
		$sql_ins2="replace into stud_seme(seme_year_seme,stud_id,seme_class,seme_class_name,seme_num,student_sn) values('$seme_year_seme','$new_stud_id','$seme_class','$seme_class_name','$new_seme_num','$new_student_sn')";
		$rs_ins2=$CONN->Execute($sql_ins2) or trigger_error($sql_ins2,256);
		//echo $sql_ins2;
		
		//�g�J���y��ƪ�
		$update_time=date("YmdHis");
		$update_id=$_SESSION['session_log_id'];
		$sql_ins3="replace into stud_domicile(stud_id,student_sn,update_time,update_id) values('$new_stud_id','$new_student_sn','$update_time','$update_id')";		
		$rs_ins3=$CONN->Execute($sql_ins3) or trigger_error($sql_ins3,256);
		//echo $sql_ins3;		
		
		//��V��s�W���ǥ�
		$student_sn=$new_student_sn;
		$c_curr_class=$year_seme;
		$c_curr_seme=$seme_year_seme;
		//echo $new_stud_id." / ".$new_seme_num;
	
	break;
}


//�L�X���Y
head();

//���Z��
if ($c_curr_class=="")
	// �Q�� $IS_JHORES �� �Ϲj �ꤤ�B��p�B���� ���w�]��
	$c_curr_class = sprintf("%03s_%s_%02s_%02s",curr_year(),curr_seme(),$default_begin_class + round($IS_JHORES/2),1);
else {
	$temp_curr_class_arr = explode("_",$c_curr_class); //091_1_02_03
	$c_curr_class = sprintf("%03s_%s_%02s_%02s",substr($c_curr_seme,0,3),substr($c_curr_seme,-1),$temp_curr_class_arr[2],$temp_curr_class_arr[3]);
}
	
if($c_curr_seme =='')
	$c_curr_seme = sprintf ("%03s%s",curr_year(),curr_seme()); //�{�b�Ǧ~�Ǵ�

//���Ǵ�
if ($c_curr_seme != "")
	$curr_seme = $c_curr_seme;

// �t�X modules/stud_move/stud_move.php ���s���P�_
//if (empty($_GET[c_curr_class])) {
	$c_curr_class_arr = explode("_",$c_curr_class);
	$seme_class = intval($c_curr_class_arr[2]).$c_curr_class_arr[3];

//}
//else
//	$seme_class = $_GET[c_curr_class];

($_POST['student_sn']=='') ? $student_sn=$_GET['student_sn']: $student_sn=$_POST['student_sn'];

	//�x�s���U�@��
if ($chknext)
	$student_sn = $nav_next;	
	$query = "select a.student_sn from stud_base a,stud_seme b where a.student_sn=b.student_sn and a.student_sn='$student_sn' and (a.stud_study_cond=0 or a.stud_study_cond=5)  and  b.seme_year_seme='$c_curr_seme' and b.seme_class='$seme_class'";	
	$res = $CONN->Execute($query) or die($res->ErrorMsg());
	//���]�w�Χ��ܦb¾���p�ΧR���O���� ��Ĥ@��
	if ($student_sn =="" || $res->RecordCount()==0) {	
		$temp_sql = "select a.student_sn from stud_base a,stud_seme b where a.student_sn=b.student_sn and (a.stud_study_cond=0 or a.stud_study_cond=5) and  b.seme_year_seme='$c_curr_seme' and b.seme_class='$seme_class' order by b.seme_num ";
		$res2 = $CONN->Execute($temp_sql) or die($temp_sql);
		$student_sn = $res2->fields[0];
	}

//����T
$field_data = get_field_info("stud_base");
//���s���r��
$linkstr = "student_sn=$student_sn&c_curr_class=$c_curr_class&c_curr_seme=$c_curr_seme";
//�Ҳտ��
print_menu($menu_p,$linkstr);
?> 
<table border="0" width="100%" cellspacing="0" cellpadding="0" CLASS="tableBg" >
<tr>
<td valign=top align="right">
<?php
//�إߥ�����   
//��ܾǴ�
$class_seme_p = get_class_seme(); //�Ǧ~��	
$upstr = "<select name=\"c_curr_seme\" onchange=\"this.form.submit()\">\n";
while (list($tid,$tname)=each($class_seme_p)){
	if ($curr_seme== $tid)
      		$upstr .= "<option value=\"$tid\" selected>$tname</option>\n";
      	else
      		$upstr .= "<option value=\"$tid\">$tname</option>\n";
}
$upstr .= "</select><br>"; 
	
$s_y = substr($c_curr_seme,0,3);
$s_s = substr($c_curr_seme,-1);
//093_1_03_02
if ($_GET[c_curr_class] <>''){
	$c_curr_class_arr = explode("_",$_GET[c_curr_class]);
	$c_curr_class = sprintf("%03d_%d_%02d_%02d",$s_y,$s_s,$c_curr_class_arr[2],$c_curr_class_arr[3]);
}

//��ܯZ��
	$tmp=&get_class_select($s_y,$s_s,"","c_curr_class","this.form.submit",$c_curr_class);
	$upstr .= $tmp;
	$grid1 = new ado_grid_menu($_SERVER[SCRIPT_NAME],$URI,$CONN);  //�إ߿��	   
	$grid1->bgcolor = $gridBgcolor;  // �C��   
	$grid1->row = $gridRow_num ;	     //��ܵ���   
	$grid1->key_item = "student_sn";  // ������W  	
	$grid1->display_item = array("sit_num","stud_name");  // �����W   
	$grid1->display_color = array("1"=>"$gridBoy_color","2"=>"$gridGirl_color"); //�k�k�ͧO
	$grid1->color_index_item ="stud_sex" ; //�C��P�_��
	$grid1->class_ccs = " class=leftmenu";  // �C�����
	$grid1->sql_str = "select a.student_sn,a.stud_id,a.stud_name,a.stud_sex,b.seme_num as sit_num from stud_base a,stud_seme b where a.student_sn=b.student_sn  and (a.stud_study_cond=0 or a.stud_study_cond=5) and  b.seme_year_seme='$c_curr_seme' and b.seme_class='$seme_class' order by b.seme_num ";   //SQL �R�O   
//	echo $grid1->sql_str;	
	$grid1->do_query(); //����R�O   
	$grid1->print_grid($student_sn,$upstr,$downstr); // ��ܵe��   
	$c_name=class_id_to_full_class_name2($c_curr_class);
	echo "<table align='center'><tr><td>
	<form action='{$_SERVER['SCRIPT_NAME']}' method='POST' name='new_form'>
	<input type='hidden' name='year_seme' value='$c_curr_class'>
	<input type='hidden' name='do_key' value='new_stud'>";
	
	
	//�O�_��ܷs�W�ǥͫ��s
	$sql_select="select pm_id,pm_item,pm_memo,pm_value from pro_module where pm_name = 'stud_reg' and pm_item='view_add' Limit 1";
	$recordSet=$CONN->Execute($sql_select);
	$view_add=$recordSet->fields['pm_value'];

	
	$ba=explode("_",$c_curr_class); 
	
	
	if ($view_add>0 || $ba[2]=="00" || $ba[2]=="01" || $ba[2]=="07")
	{
		echo "<input type='button' value='�s�W' onclick=\"if(confirm('�z�T�w�n�s�W�@��ǥͩ�H\\n$c_name')) this.form.submit()\">";
	
	}
	
	echo "</form>
	</td></tr></table>";
	//��ܸ��
	//if($_GET['stud_id']) $stud_id=$_GET['stud_id'];
	$sql_select = "select * from stud_base where student_sn='$student_sn' ";	
	$recordSet = $CONN->Execute($sql_select);
while (!$recordSet->EOF) {
	$student_sn = $recordSet->fields["student_sn"];
	$stud_id = $recordSet->fields["stud_id"];
	$stud_name = $recordSet->fields["stud_name"];
	$stud_name_eng = $recordSet->fields["stud_name_eng"];
	$stud_sex = $recordSet->fields["stud_sex"];
	$stud_birthday = $recordSet->fields["stud_birthday"];
	$stud_blood_type = $recordSet->fields["stud_blood_type"];
	$stud_birth_place = $recordSet->fields["stud_birth_place"];
	$stud_kind = $recordSet->fields["stud_kind"];
	$stud_country = $recordSet->fields["stud_country"];
	$stud_country_kind = $recordSet->fields["stud_country_kind"];
	$stud_person_id = $recordSet->fields["stud_person_id"];
	$stud_country_name = $recordSet->fields["stud_country_name"];
	$addr_move_in = $recordSet->fields["addr_move_in"];
	$stud_addr_1 = $recordSet->fields["stud_addr_1"];
	$stud_addr_2 = $recordSet->fields["stud_addr_2"];
	$stud_tel_1 = $recordSet->fields["stud_tel_1"];
	$stud_tel_2 = $recordSet->fields["stud_tel_2"];
	$stud_tel_3 = $recordSet->fields["stud_tel_3"];
	$stud_mail = $recordSet->fields["stud_mail"];
	$stud_addr_a = $recordSet->fields["stud_addr_a"];
	$stud_addr_b = $recordSet->fields["stud_addr_b"];
	$stud_addr_c = $recordSet->fields["stud_addr_c"];
	$stud_addr_d = $recordSet->fields["stud_addr_d"];
	$stud_addr_e = $recordSet->fields["stud_addr_e"];
	$stud_addr_f = $recordSet->fields["stud_addr_f"];
	$stud_addr_g = $recordSet->fields["stud_addr_g"];
	$stud_addr_h = $recordSet->fields["stud_addr_h"];
	$stud_addr_i = $recordSet->fields["stud_addr_i"];
	$stud_addr_j = $recordSet->fields["stud_addr_j"];
	$stud_addr_k = $recordSet->fields["stud_addr_k"];
	$stud_addr_l = $recordSet->fields["stud_addr_l"];
	$stud_addr_m = $recordSet->fields["stud_addr_m"];
	$stud_class_kind = $recordSet->fields["stud_class_kind"];
	$stud_spe_kind = $recordSet->fields["stud_spe_kind"];
	$stud_spe_class_kind = $recordSet->fields["stud_spe_class_kind"];
	$stud_spe_class_id = $recordSet->fields["stud_spe_class_id"];
	$stud_preschool_status = $recordSet->fields["stud_preschool_status"];
	$stud_preschool_id = $recordSet->fields["stud_preschool_id"];
	$stud_preschool_name = $recordSet->fields["stud_preschool_name"];
	$stud_Mschool_status = $recordSet->fields["stud_Mschool_status"];
	$stud_mschool_id = $recordSet->fields["stud_mschool_id"];
	$stud_mschool_name = $recordSet->fields["stud_mschool_name"];
	$stud_study_year = $recordSet->fields["stud_study_year"];
	$curr_class_num = $recordSet->fields["curr_class_num"];
	$stud_study_cond = $recordSet->fields["stud_study_cond"];
	$addr_zip = $recordSet->fields["addr_zip"];
	$enroll_school = $recordSet->fields["enroll_school"];
	$obtain = $recordSet->fields["obtain"];
	$safeguard = $recordSet->fields["safeguard"];

	$experiment_kind=$recordSet->fields["experiment_kind"];
	$exp_group_name=$recordSet->fields["exp_group_name"];

	$recordSet->MoveNext();
};

include  "$SFS_PATH/include/sfs_oo_date.php";

// ����禡
$seldate = new date_class("myform");
$seldate->demo ="none";
//����ˬdjavascript �禡
$seldate->date_javascript();
//�ͤ�
$stud_birthday_str = $seldate->date_add("stud_birthday",$stud_birthday);
//���y�E�J���
$addr_move_in_str = $seldate->date_add("addr_move_in",$addr_move_in,1);

$seldate->do_check();
?>

<script language="JavaScript">
function checkok() {
	document.myform.nav_next.value = document.gridform.nav_next.value;		
	return date_check();
	}

function do_same(){
	document.myform.stud_addr_2.value=document.myform.stud_addr_1.value;
}
//-->
</script>
</td> 
<td  valign="top" width="100%" >   

<form name="myform" action="<?php echo $_SERVER[SCRIPT_NAME] ?>" method="post" encType="multipart/form-data"
<?php
//��mnu���Ƭ�0�� �� form �� disabled
	if ($grid1->count_row==0 && !($key == $newBtn || $key == $postBtn)) 
		echo " disabled ";
	?> >

<table border="1" cellpadding="2" cellspacing="0"  bordercolorlight="#333354" bordercolordark="#FFFFFF" class="main_body" width="100%">
  <tr>
  <td class=title_mbody colspan="5" align="center">
	<?php 
		echo $msg;
		echo sprintf("%d�Ǧ~��%d�Ǵ� %s--%s %s",substr($c_curr_seme,0,-1),substr($c_curr_seme,-1),$class_list_p[$c_curr_seme],$stud_name,"<input type='text' size='10' name='new_stud_id' value=$stud_id>");
		
	?>

	<?php
	echo "   (�t�άy����:$student_sn)  ";
	if ($modify_flag) {
    	echo "<input type=submit name=do_key value =\"$editBtn\" onClick=\"return checkok();\">&nbsp;&nbsp;";
    		if ($chknext)
    			echo "<input type=checkbox name=chknext value=1 checked >";			
    		else
    			echo "<input type=checkbox name=chknext value=1 >";
    			
    		echo "�۰ʸ��U�@��";
	}
    
    ?>
	</td>	
  </tr>
  <tr>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[stud_name][d_field_cname] ?></td>
	<td>
		����:<input type="text" size="20" maxlength="20" name="stud_name" value="<?php echo $stud_name; ?>"><br />
		�^��:<input type="text" size="20" maxlength="20" name="stud_name_eng" value="<?php echo $stud_name_eng; ?>">
	</td>
	<td align="right" CLASS="title_sbody1" nowrap>�y��</td>
  <td><input type="text" name="sit_num" size="3" value="<?php echo intval(substr($curr_class_num,-2)) ?>">
    	<input type=hidden name="curr_class_num" value="<?php echo $curr_class_num ?>">
  </td>
  <td width="20%" height="83" rowspan="5">
    <table border=0 cellpadding=0 cellspacing=0 width=100%  >
    	<tr>
    		<td height=80% align=center>
    	<input type="hidden" name="stud_study_year" value="<?php echo $stud_study_year ?>"> 
    	<?php 
    	
    	//�L�X�Ӥ�
    		$img =$stud_study_year."/".$stud_id;    		
    		if (file_exists($UPLOAD_PATH."$img_path/".$img)) {
    			echo "<img src=\"".$UPLOAD_URL."$img_path/$img\" width=\"$img_width\">";
			echo "<br><font size=2><input type=checkbox name=\"del_img\" value=\"1\"> �R������</font>";
		}
    	?>
    	</td>
    	</tr>
    	<tr>
    	<td height=20% valign=bottom>
    	<font size=2>�Ӥ�</font><input type="file" size=10 name="stud_img" >
    	</td>
    	</tr>
    </table>
    </td>
  </tr>
  <tr>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[stud_sex][d_field_cname] ?></td>
	<td >
	<?php  
    	//�ʧO 
    	$temp1=""; $temp2=""; 
    	if($stud_sex == 1 ){ 
    		$temp1="checked "; $temp2=""; 
    	} 
    	else if($stud_sex == 2){ 
    		$temp1=""; $temp2="checked "; 
    	}
	?> 
	<input type="radio" name="stud_sex" value="1" <?php echo $temp1 ?>>�k &nbsp;&nbsp;<input type="radio" name="stud_sex" value="2" <?php echo $temp2 ?>>�k 
	</td>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[stud_birthday][d_field_cname] ?></td>
	<td ><?php echo $stud_birthday_str ?></td>
  </tr>
  <tr>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[stud_blood_type][d_field_cname] ?></td>
	<td >
	<?php
		//��ܦ嫬
		$sel1 = new drop_select(); //������O
		$sel1->s_name = "stud_blood_type"; //���W��
		$sel1->id = intval($stud_blood_type);
		$sel1->arr = blood(); //���e�}�C
		$sel1->do_select();
	  ?>	
	</td>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[stud_birth_place][d_field_cname] ?></td>
	<td >
	<?php
    	//�X�ͦa�}�C 
    	$sel1 = new drop_select(); //������O
    	$sel1->s_name = "stud_birth_place"; //���W��
	$sel1->id = intval($stud_birth_place);
	$sel1->arr = birth_state(); //���e�}�C
	$sel1->do_select();	
    	?>
	</td>

  </tr>
  <tr>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[stud_country][d_field_cname] ?></td>
	<td ><input type="text" size="20" maxlength="20" name="stud_country" value="<?php echo ($stud_country=="")?$default_country:$stud_country  ?>"></td>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[stud_country_kind][d_field_cname] ?></td>
	<td >
	<?php
    	//�ҷӺ��� 
    	$sel1 = new drop_select(); //������O
    	$sel1->s_name = "stud_country_kind"; //���W��
	$sel1->id = intval($stud_country_kind);
	$sel1->has_empty = false;
	$sel1->arr = stud_country_kind(); //���e�}�C
	$sel1->do_select();	
    	?>
	</td>
  </tr>
  <tr>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[stud_person_id][d_field_cname] ?></td>
	<td ><input type="text" size="20" maxlength="20" name="stud_person_id" value="<?php echo $stud_person_id ?>"></td>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[stud_country_name][d_field_cname] ?></td>
	<td ><input type="text" size="20" maxlength="20" name="stud_country_name" value="<?php echo $stud_country_name ?>"></td>

  </tr>
<tr>
  	<td align="right" CLASS="title_sbody1" nowrap>���}</td>
	<td   colspan="4" >
	<?php echo $field_data[stud_addr_1][d_field_cname] ?>:
	<input type="text" size="40" maxlength="60" name="stud_addr_1" value="<?php echo $stud_addr_1 ?>"><br>
	<?php echo $field_data[stud_addr_2][d_field_cname] ?>:
	<input type="text" size="40" maxlength="60" name="stud_addr_2" value="<?php echo $stud_addr_2 ?>">
	<input type="text" size="5" maxlength="5" name="addr_zip" value="<?php echo $addr_zip ?>" title="��J�l���ϸ�">
	<?php
	 if ($stud_addr_1 == $stud_addr_2)
	 	$disable_str = " disabled ";
	 ?>
	<input type="button" name="same_addr" value="<?php echo $sameBtn ?>" <?php echo $disable_str ?> onclick="do_same()">
</tr>
<tr><td align="right" CLASS="title_sbody1" nowrap>���y�E�J���:</td>
<td colspan="4"><?php echo $addr_move_in_str ?></td>
</tr>
<tr>
<td align="right" CLASS="title_sbody1" nowrap>�J�ǮɾǮաG</td><td colspan="4"><input type='text' size=30 maxlength=30 name="enroll_school" value="<?php echo $enroll_school ?>"></td>
</tr>

<tr bgcolor="#ffcccc">
<td align="right" nowrap>���y���o��]:</td><td>
	<?php
    	$sel1 = new drop_select(); //������O
    	$sel1->s_name = "obtain"; //���W��
	$sel1->id = intval($obtain);
	$sel1->has_empty = true;
	$sel1->arr = stud_obtain_kind(); //���e�}�C
	$sel1->do_select();	
    	?>
</td>
<td align="right" nowrap>�Ӯ׫O�@���O:</td><td>
	<?php
    	$sel1 = new drop_select(); //������O
    	$sel1->s_name = "safeguard"; //���W��
	$sel1->id = intval($safeguard);
	$sel1->has_empty = true;
	$sel1->arr = stud_safeguard_kind(); //���e�}�C
	$sel1->do_select();	
    	?>
</td>
<td align='center' rowspan="2">*���C��ƫDXML�洫�зǡI</td>
</tr>
	<tr bgcolor="#ffcccc">
		<td align="right" nowrap>�ǥ;ǲ߫��A</td><td>
			<?php
			$sel1 = new drop_select(); //������O
			$sel1->s_name = "experiment_kind"; //���W��
			$sel1->id = intval($experiment_kind);
			$sel1->has_empty = true;
			$sel1->arr = stud_experiment_kind(); //���e�}�C
			$sel1->do_select();
			?>
		</td>
		<td align="right" nowrap>����Ш|����/���c�W��</td>
		<td><input type="text" name="exp_group_name" size="20" value="<?php echo $exp_group_name;?>"></td>
	</tr>
<tr>
<td   colspan="5" >
	<!-- �����ɤ��y�a�} -->
	�����ɤ��y�a�} &nbsp;&nbsp;&nbsp;<input type="checkbox" name="same_key" value="1"><b><?php echo $sameBtn ?></b>
	<BR>
	<table>
	<tr>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[stud_addr_a][d_field_cname] ?></td>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[stud_addr_b][d_field_cname] ?></td>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[stud_addr_c][d_field_cname] ?></td>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[stud_addr_d][d_field_cname] ?></td>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[stud_addr_e][d_field_cname] ?></td>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[stud_addr_f][d_field_cname] ?></td>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[stud_addr_g][d_field_cname] ?></td>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[stud_addr_h][d_field_cname] ?></td>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[stud_addr_i][d_field_cname] ?></td>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[stud_addr_j][d_field_cname] ?></td>	
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[stud_addr_k][d_field_cname] ?></td>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[stud_addr_l][d_field_cname] ?></td>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[stud_addr_m][d_field_cname] ?></td>

	</tr>
	<tr>
	
	<td ><input type="text" size="5" maxlength="6" name="stud_addr_a" value="<?php echo $stud_addr_a ?>"></td>
	<td ><input type="text" size="5" maxlength="12" name="stud_addr_b" value="<?php echo $stud_addr_b ?>"></td>
	<td ><input type="text" size="5" maxlength="12" name="stud_addr_c" value="<?php echo $stud_addr_c ?>"></td>	
	<td ><input type="text" size="5" maxlength="6" name="stud_addr_d" value="<?php echo $stud_addr_d ?>"></td>
	<td ><input type="text" size="5" maxlength="20" name="stud_addr_e" value="<?php echo $stud_addr_e ?>"></td>
	<td ><input type="text" size="3" maxlength="6" name="stud_addr_f" value="<?php echo $stud_addr_f ?>"></td>	
	<td ><input type="text" size="5" maxlength="8" name="stud_addr_g" value="<?php echo $stud_addr_g ?>"></td>
	<td ><input type="text" size="3" maxlength="6" name="stud_addr_h" value="<?php echo $stud_addr_h ?>"></td>
	<td ><input type="text" size="5" maxlength="8" name="stud_addr_i" value="<?php echo $stud_addr_i ?>"></td>
	<td ><input type="text" size="5" maxlength="8" name="stud_addr_j" value="<?php echo $stud_addr_j ?>"></td>	
	<td ><input type="text" size="3" maxlength="6" name="stud_addr_k" value="<?php echo $stud_addr_k ?>"></td>
	<td ><input type="text" size="3" maxlength="6" name="stud_addr_l" value="<?php echo $stud_addr_l ?>"></td>
	<td ><input type="text" size="5" maxlength="12" name="stud_addr_m" value="<?php echo $stud_addr_m ?>"></td>
	</tr>

	</table>
	
	</td>
</tr>
<tr>
  	
	<td   colspan="5" >
	<?php echo $field_data[stud_tel_1][d_field_cname] ?>:
	<input type="text" size="10" maxlength="20" name="stud_tel_1" value="<?php echo $stud_tel_1 ?>">&nbsp;
	<?php echo $field_data[stud_tel_2][d_field_cname] ?>:
	<input type="text" size="10" maxlength="20" name="stud_tel_2" value="<?php echo $stud_tel_2 ?>">&nbsp;
	<?php echo $field_data[stud_tel_3][d_field_cname] ?>:
	<input type="text" size="10" maxlength="20" name="stud_tel_3" value="<?php echo $stud_tel_3 ?>">&nbsp;
	<br>
	<?php echo $field_data[stud_mail][d_field_cname] ?>:
	<input type="text" size="30" maxlength="50" name="stud_mail" value="<?php echo $stud_mail ?>">
	</td>
</tr>

    
  <tr>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[stud_kind][d_field_cname] ?></td>
	<td  colspan="4">
	<?php  
	//�ǥͨ����O
		$sel1 = new checkbox_class(); //������O		
		$sel1->s_name = "stud_kind"; //���W��
		$sel1->id = $stud_kind;
		$sel1->arr = stud_kind(); //���e�}�C	
		$sel1->css = "main_body";
		$sel1->is_color =true;
		$sel1->do_select();
	 ?>	
	
	</td>
  </tr>
  <tr>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[stud_class_kind][d_field_cname] ?></td>
	<td >
	<?php  
	//�Z�ũʽ�
		$sel1 = new drop_select(); //������O	
		$sel1->s_name = "stud_class_kind"; //���W��
		$sel1->id = intval($stud_class_kind);
		$sel1->arr = stud_class_kind(); //���e�}�C
		$sel1->has_empty =false;
		$sel1->do_select();	  
	 ?>		
	</td>
	<td align="right" CLASS="title_sbody1"  nowrap><?php echo $field_data[stud_spe_kind][d_field_cname] ?></td>
	<td  colspan="2">
	<?php 
	//�S���Z���O
		$sel1 = new drop_select(); //������O		
		$sel1->s_name = "stud_spe_kind"; //���W��
		$sel1->id = intval($stud_spe_kind);
		$sel1->arr = stud_spe_kind(); //���e�}�C
		$sel1->do_select();	  
	 ?>	
	</td>
  </tr>
  <tr>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[stud_spe_class_kind][d_field_cname] ?></td>
	<td >
	<?php 
	//�S���Z�Z�O 
		$sel1 = new drop_select(); //������O		
		$sel1->s_name = "stud_spe_class_kind"; //���W��
		$sel1->id = intval($stud_spe_class_kind);
		$sel1->arr = stud_spe_class_kind(); //���e�}�C
		$sel1->do_select();
	 ?>	
	</td>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[stud_spe_class_id][d_field_cname] ?></td>
	<td  colspan="2">
	<?php 
	//�S���Z�W�ҩʽ� 
		$sel1 = new drop_select(); //������O		
		$sel1->s_name = "stud_spe_class_id"; //���W��
		$sel1->id = intval($stud_spe_class_id);
		$sel1->arr = stud_spe_class_id(); //���e�}�C
		$sel1->do_select();
	 ?>	
	</td>
	
  </tr>
  <tr>
	<td align="right" CLASS="title_sbody1" nowrap>�J�ǫe���X��</td>
	<td  colspan="4">
	�J�Ǹ��:
	<?php 
	//�J�Ǹ��
		$sel1 = new drop_select(); //������O	
		$sel1->s_name = "stud_preschool_status"; //���W��
		$sel1->id = intval($stud_preschool_status);
		$sel1->arr = stud_preschool_status(); //���e�}�C	
		$sel1->do_select();
	 ?>
	
	&nbsp;���X��ǮեN��:<input type="text" size="4" maxlength="8" name="stud_preschool_id" value="<?php echo $stud_preschool_id ?>"> &nbsp;
	���X��W��:<input type="text" size="15" maxlength="40" name="stud_preschool_name" value="<?php echo $stud_preschool_name ?>">
	</td>
	
  </tr>
  <tr>
	<td align="right" CLASS="title_sbody1" nowrap>�J�ǫe��p</td>
	<td  colspan="4">
	�J�Ǹ��:
	<?php 
	//�J�Ǹ��
		$sel1 = new drop_select(); //������O	
		$sel1->s_name = "stud_Mschool_status"; //���W��
		$sel1->id = intval($stud_Mschool_status);
		$sel1->arr = stud_preschool_status(); //���e�}�C	
		$sel1->do_select();
	 ?>	
	
	&nbsp;��p�ǮեN��:<input type="text" size="4" maxlength="8" name="stud_mschool_id" value="<?php echo $stud_mschool_id ?>"> &nbsp;
	��p�W��:<input type="text" size="15" maxlength="40" name="stud_mschool_name" value="<?php echo $stud_mschool_name ?>">
	</td>
	
  </tr>
</table>




<input type="hidden" name="student_sn" value="<?php echo $student_sn ?>">
<input type="hidden" name="c_curr_seme" value="<?php echo $c_curr_seme ?>">
<input type="hidden" name="c_curr_class" value="<?php echo $c_curr_class ?>">
<input type="hidden" name="seme_class" value="<?php echo $seme_class ?>">
<input type=hidden name=nav_next >
</form>
</table>
</td>
</tr>
</table>
<?php 
//�L�X���Y
foot();
?>