<?php
// $Id: make.php 7710 2013-10-23 12:40:27Z smallduh $

// �t�οﶵ
$performance=array(1=>"��`�欰���{",2=>"���鬡�ʪ��{",3=>"���@�A��",4=>"�ե~�S�����{");
$performance_option=array(1=>"���{�u��",2=>"���{�}�n",3=>"���{�|�i",4=>"�ݦA�[�o",5=>"���ݧ�i");

//�E�~�@�e�������
$ss9[]="�y��-����y��";
$ss9[]="�y��-�m�g�y��";
$ss9[]="�y��-�^�y";
$ss9[]="���d�P��|";
$ss9[]="�ͬ�";
$ss9[]="�ƾ�";
$ss9[]="��X����";
$ss9[]="���|";
$ss9[]="���N�P�H��";
$ss9[]="�۵M�P�ͬ����";

include "config.php";
include "../score_paper/function.php";
include_once "../../include/sfs_case_subjectscore.php";
include_once "../../include/sfs_case_studclass.php";
include_once "../../include/sfs_case_score.php";
include_once "../../include/sfs_case_dataarray.php";
include_once "../../include/sfs_oo_zip2.php";
sfs_check();

/*
//�Y����ܾǦ~�Ǵ��A�i����Ψ��o�Ǧ~�ξǴ�
if(!empty($_REQUEST[year_seme])){
	$ys=explode("-",$_REQUEST[year_seme]);
	$sel_year=$ys[0];
	$sel_seme=$ys[1];
}else{
*/
	$sel_year=(empty($_REQUEST[sel_year]))?curr_year():$_REQUEST[sel_year]; //�ثe�Ǧ~
	$sel_seme=(empty($_REQUEST[sel_seme]))?curr_seme():$_REQUEST[sel_seme]; //�ثe�Ǵ�
	$class_num=get_teach_class();
	$class_all=class_num_2_all($class_num);
	$class_id=old_class_2_new_id($class_num,$sel_year,$sel_seme);
	
	$CHK_KIND=chk_kind();
/*
}
*/
/*
//���o���ЯZ�ťN��
$class_num=get_teach_class();
$class_all=class_num_2_all($class_num);
if(empty($class_num)){
	$act="error";
	$error_title="�L�Z�Žs��";
	$error_main="�䤣��z���Z�Žs���A�G�z�L�k�ϥΦ��\��C<ol>
	<li>�нT�{�z�����ЯZ�šC
	<li>�нT�{�аȳB�w�g�N�z�����и�ƿ�J�t�Τ��C
	</ol>";
}
*/

//�D���]�w
$school_menu_p=(empty($school_menu_p))?array():$school_menu_p;

$act=$_REQUEST[act];

//����ʧ@�P�_

if($act=="dlar"){
	downlod_ar($_POST[stud_id],$_POST[class_id],$_POST[sp_sn],$_POST[stu_num],$sel_year,$sel_seme);
	header("location: {$_SERVER['PHP_SELF']}?class_id=$_POST[class_id]&stud_id=$_POST[stud_id]");
}elseif($act=="dlar_all"){
	downlod_ar("",$_POST[class_id],$_POST[sp_sn],"",$sel_year,$sel_seme,"all");
	header("location: {$_SERVER['PHP_SELF']}?class_id=$_POST[class_id]");
}elseif($_REQUEST[error]==1){
	user_error("�ӯZ�ŵL�ǥ͸�ơA�L�k�~��C<ol>$oth_data
	<li>�нT�{�z�����ЯZ�šC
	<li>�нT�{�аȳB�w�g�N�z���ǥ͸�ƿ�J�t�Τ��C
	<li>�פJ�ǥ͸�ơG�y�ǰȨt�έ���>�а�>���U��>�פJ��ơz(<a href='".$SFS_PATH_HTML."school_affairs/student_reg/create_data/mstudent2.php'>".$SFS_PATH_HTML."school_affairs/student_reg/create_data/mstudent2.php</a>)</ol>",256);
}else{
	$main=&main_form($sel_year,$sel_seme,$class_id,$_REQUEST[stud_id]);
}


//�q�X����
head("���Z��s�@");
print_menu($menu_p);
?>


<script language="JavaScript">
<!-- Begin
function jumpMenu_seme(){
	location="<?php echo $_SERVER['PHP_SELF']?>?act=<?php echo $act;?>&year_seme=" + document.myform.year_seme.options[document.myform.year_seme.selectedIndex].value + "&class_id=<?php echo $_REQUEST[class_id]?>";
}

function jumpMenu_seme_1(){
	location="<?php echo $_SERVER['PHP_SELF']?>?act=<?php echo $act;?>&year_seme=<?php echo $_REQUEST[year_seme]?>&class_id=" + document.myform.class_id.options[document.myform.class_id.selectedIndex].value;
}
//  End -->$oth_data
</script>


<?php
echo $main;
foot();

function &main_form($sel_year,$sel_seme,$class_id,$stud_id){
	global $CONN,$sch_montain_p,$sch_mark_p,$sch_class_p,$UPLOAD_URL,$school_menu_p,$performance,$SFS_PATH_HTML,$CHK_KIND;

	//���o�~�׻P�Ǵ����U�Կ��DEMO
	//$date_select=&class_ok_setup_year($sel_year,$sel_seme,"year_seme","jumpMenu_seme");
	//�~�ŻP�Z�ſ��
	//$class_select=&get_class_select($sel_year,$sel_seme,"","class_id","jumpMenu_seme_1",$_REQUEST['class_id']);

	//���o�ǥͿ��
	//if(empty($class_select) or empty($date_select))	header("location:{$_SERVER['PHP_SELF']}?error=1&year_seme=$_REQUEST[year_seme]");


	if(!empty($class_id)){
		//�ഫ�Z�ťN�X
		$class=class_id_2_old($class_id);
		//���p�S�����w�ǥ͡A���o�Ĥ@��ǥ�
		if(empty($stud_id))$stud_id=get_no1($class_id);
		//�Y���O�S�� $stud_id �A�h�q�X���~�T��
		if(empty($stud_id))header("location:{$_SERVER['PHP_SELF']}?error=1");


		$gridBgcolor="#DDDDDC";
		//�w�s�@����C��
		$over_color = "#223322";
		//�����k������C��
		$non_color = "blue";

		$grid1 = new ado_grid_menu($_SERVER['PHP_SELF'],$URI,$CONN);  //�إ߿��
		$grid1->key_item = "stud_id";  // ������W
		$grid1->formname = "myform";
		$grid1->display_item = array("sit_num","stud_name");  // �����W
		$grid1->bgcolor = $gridBgcolor;
		$grid1->display_color = array("1"=>"blue","2"=>"red");
		$grid1->color_index_item ="stud_sex" ; //�C��P�_��
		$grid1->class_ccs = " class=leftmenu";  // �C�����
		$grid1->sql_str = "select stud_id,stud_name,stud_sex,substring(curr_class_num,4,2)as sit_num  from stud_base where curr_class_num like '$class[2]%' and stud_study_cond=0 order by curr_class_num";   //SQL �R�O
		$grid1->do_query(); //����R�O

		$stud_select = $grid1->get_grid_str($stud_id); // ��ܵe��


		if(!empty($stud_id)){

			if ($chknext && $nav_next<>'')	$stud_id = $nav_next;

			//�D�o�ǥ�ID
			$student_sn=stud_id2student_sn($stud_id);

			//���o���w�ǥ͸��
			$stu=get_stud_base("",$stud_id);

			//�y��
			$stu_class_num=curr_class_num2_data($stu['curr_class_num']);

			$score_paper_option=score_paper_option();
			$down_box="<div>
			<form action='{$_SERVER['PHP_SELF']}' method='post'>
			���Z��榡�G<select name='sp_sn'>
			$score_paper_option
			</select>
			<br>
			<input type='radio' name='act' value='dlar' checked>�U��".$stu[stud_name]."�����Z��<br>
			<input type='radio' name='act' value='dlar_all'>�U�����Z�����Z��
			<input type='hidden' name='stud_id' value='$stud_id'>
			<input type='hidden' name='stu_num' value='$stu_class_num[num]'>
			<input type='hidden' name='class_id' value='$class_id'>
			<input type='hidden' name='year_seme' value='$_REQUEST[year_seme]'>
			<br>
			<input type='submit' value='�U��'>
			</form>
			</div>";


			$stud_all="<b>".$stu[stud_name]."�]".$stu_class_num[num]."���^</b>�����Z��Ʀp�U�G<br>
			<table><tr><td valign=top>

			<table width=300 cellspacing='1' cellpadding='3' bgcolor='#C0C0C0' class='small'>";
			//���o�ǥ͸�T
			$studata=get_stud_base_array($class_id,$stud_id);
			$stud_all.=make_list($studata,"�ǥ͸�T","","",false);

			//���o�Ӿǥͤ�`�ͬ����{���q��
			$oth_data=&get_oth_value($stud_id,$sel_year,$sel_seme);
			foreach($performance as $id=>$sk){
				$oth_array[$sk]=$oth_data['�ͬ����{���q'][$id];
			}
			$stud_all.=make_list($oth_array,"�ͬ����{���q","","",false);

			//���o�Ǵ���T
			$days=get_all_days($sel_year,$sel_seme,$class_id);
			$stud_all.=make_list($days,"�Ǵ���T","","",false);

			//���o�ǥ;Ǵ����y�Τ���
			$nor_value=get_nor_value($student_sn,$sel_year,$sel_seme,$class_id);
			$stud_all.=make_list($nor_value,"�Ǵ��`���{","","",false);

			//���o�ǥͤ�`�ͬ����{��r
			$nor_text=get_nor_text($student_sn,$sel_year,$sel_seme);
			$stud_all.=make_list($nor_text,"��`�ͬ����{��r","","",false);
			
			//���o�ǥͤ�`�ͬ��ˮ֤�r
			$chk_text=get_chk_text($student_sn,$sel_year,$sel_seme,$CHK_KIND);
			$stud_all.=make_list($chk_text,"��`�ͬ��ˮ֤�r","","",false);

			//���o�ǥͯʮu���p
			$abs_data=get_abs_value($stud_id,$sel_year,$sel_seme,"����");
			$stud_all.=make_list($abs_data,"�ʮu���p","","",false);

			//���o�ǥͯʮu���p�]���Z���J���^
			$abs_data=get_abs_value($stud_id,$sel_year,$sel_seme,"����_��");
			$stud_all.=make_list($abs_data,"�ʮu���p�]���Z���J���^","","",false);

			//�ǥͼ��g���p
			$reward_data=get_reward_value2($stud_id,$sel_year,$sel_seme);
			$stud_all.=make_list($reward_data,"���g���p","","",false);

			//�ǥͼ��g���p�]���Z���J���^
			$reward_data2=get_reward_value($stud_id,$sel_year,$sel_seme,"����_��");
			$stud_all.=make_list($reward_data2,"���g���p�]���Z���J���^","","",false);

			$stud_all.="</table></td><td valign=top>".$down_box."</td></tr></table><p>";

			//���o�ǥͦ��Z��
			$stud_all.=get_score_value2($sel_year,$sel_seme,$stud_id,$student_sn,$class_id,$oth_data);
			//echo $sel_year."--".$sel_seme."--".$stud_id."--".$student_sn."--".$class_id."--".$oth_data;

		}else{
			$stud_all="�|����ܾǥ�</td></tr><table>";
		}

	}
    $tool_bar=&make_menu($school_menu_p);

    //���o���w�ǥ͸��
	$stu=get_stud_base("",$stud_id);

	$main="
	$tool_bar
	<table bgcolor='#9EBCDD' cellspacing=0 cellpadding=4>
	<tr bgcolor='#FFFFFF'><td bgcolor='#BDD3FF' valign=top>
		<table>
		<form action='{$_SERVER['PHP_SELF']}' method='post' name='myform'>
  		<tr><td valign=top align=center>
  		$stud_select
  		</td></tr>
		</form>
		</table>
	</td><td valign=top>$stud_all</td></tr>
	</table>
	";
	return $main;
}




// ���o���Z��
function &get_score_value2($sel_year,$sel_seme,$stud_id,$student_sn,$class_id,$oth_data) {
	global $CONN,$ss9;

	$class=class_id_2_old($class_id);
	$cyear=$class[3];

	// ���o�V�O�{�פ�r�ԭz
	//	$arr_1 = sfs_text("�V�O�{��");
	// ���o�ҵ{�C�g�ɼ�
	$ss_num_arr = get_ss_num_arr($class_id);
	// ���o�ǲߦ��N
	$ss_score_arr =get_ss_score_arr($class,$student_sn);

	$other_title="<td>�`��</td><td>����</td><td>�[�v</td><td>����</td><td>�V�O�{��</td><td>���y</td>";

	$main.="<p>";

	//�۰ʰ����E�~�@�e��ؼ���
	$ss9_array=get_ss9_array($sel_year,$sel_seme,$stud_id,$student_sn,$class_id);

	$yss9=array();


	//�@�Ӱj�@�Ӭ��
	foreach($ss9 as $link_ss){
		//if($subject['need_exam']!='1')continue;
		$k="�E_".$link_ss;
		$k1=$k."�`��";
		$k2=$k."����";
		$k3=$k."�[�v";
		$k4=$k."����";
		$k5=$k."�V�O�{��";
		$k6=$k."���y";

		$yss9[$k]=$link_ss;
		$other9[$k]=array(str_replace("{break_text}","<br>",$ss9_array[$k1]."�`"),str_replace("{break_text}","<br>",$ss9_array[$k2]),str_replace("{break_text}","<br>",$ss9_array[$k3]),str_replace("{break_text}","<br>",$ss9_array[$k4]),str_replace("{break_text}","<br>",$ss9_array[$k5]),str_replace("{break_text}","<br>",$ss9_array[$k6]));
		//$other9[$k]=array($ss9_array[$k1]."�`",$ss9_array[$k2],$ss9_array[$k3],$ss9_array[$k4],$ss9_array[$k5],$ss9_array[$k6]);
	}

	if(!empty($ss9_array)){
		$main.=make_list($yss9,"�۰ʰ����E�~�@�e���",$other_title,$other9)."<br>";

	}

	//���o��ذ}�C
	$ss_array=ss_array($sel_year,$sel_seme,$cyear,$class_id);
	$yss=array();

	foreach($ss_array as $ss_id=>$subject){
		if($subject[need_exam]!='1')continue;

		$k=$subject['name'];
		$yss[$k]=$subject['name'];
		$other[$k]=array($ss_num_arr[$ss_id]."�`",$ss_score_arr[$ss_id]['ss_score'],$subject['rate'],$ss_score_arr[$ss_id]['score_name'],$oth_data["�V�O�{��"]["$ss_id"],$ss_score_arr[$ss_id]['ss_score_memo']);
	}

	if(!empty($ss_array)){
		$main.=make_list($yss,"$cyear �~�Ŭ��",$other_title,$other)."<br>";
	}



	return $main;
}


//�U�����Z��
function downlod_ar($stud_id="",$class_id="",$sp_sn="",$stu_num="",$sel_year="",$sel_seme="",$mode=""){
	global $CONN,$UPLOAD_PATH,$UPLOAD_URL,$SFS_PATH_HTML,$line_color,$line_width,$draw_img_width,$draw_img_height;

	//�ഫ�Z�ťN�X
	$class=class_id_2_old($class_id);
	$class_num=$class[2];


	//�D�o�ǥ�ID
	$student_sn=stud_id2student_sn($stud_id);
	if($mode=="all"){
		//���o�ӯZ�ǥ�
		$all_stud_array=get_stud_array($sel_year,$sel_seme,$class[3],$class[4],"sn","id");
		make_ooo($sel_year,$sel_seme,$class_id,$sp_sn,$all_stud_array);

	}else{
		make_ooo($sel_year,$sel_seme,$class_id,$sp_sn,array($student_sn=>$stud_id));
	}

	return;

}



function make_ooo($sel_year,$sel_seme,$class_id,$sp_sn,$data_arr){
	global $CONN,$UPLOAD_PATH,$CHK_KIND;

	//Openofiice�����|
	$oo_path=$UPLOAD_PATH."score_paper/".$sp_sn;

	//�ɦW����
	if($mode=="one"){
		$filename="score".$class_id.".sxw";
	}else{
		$filename="score".$stud_id.".sxw";
	}


	//�s�W�@�� zipfile ���
	$ttt = new EasyZip;
	$ttt->setPath($oo_path);

	//�[�J xml �ɮר� zip ���A�@�������ɮ�
	//�Ĥ@�ӰѼƬ���l�r��A�ĤG�ӰѼƬ� zip �ɮת��ؿ��M�W��

	if (is_dir($oo_path)) { 
		if ($dh = opendir($oo_path)) { 
			while (($file = readdir($dh)) !== false) { 
				if($file=="." or $file==".." or $file=="content.xml" or $file=="Configurations2" or $file=="Thumbnails" or strtoupper(substr($file,-4))=='.SXW') {
					continue;
				}elseif(is_dir($oo_path."/".$file)){
					if ($dh2 = opendir($oo_path."/".$file)) { 
						while (($file2 = readdir($dh2)) !== false) { 
							if($file2=="." or $file2==".."){
								continue;
							}else{
								$data = $ttt->read_file($oo_path."/".$file."/".$file2);
								$ttt->add_file($data,$file."/".$file2);
							}
						} 
						closedir($dh2); 
					} 
				}else{
					$data = $ttt->read_file($oo_path."/".$file);
					$ttt->add_file($data,$file);
				}
			} 
			closedir($dh); 
		} 
	} 


	//Ū�X content.xml
	$data = $ttt->read_file($oo_path."/content.xml");
	// �[�J���� tag

	$data = str_replace("<office:automatic-styles>",'<office:automatic-styles><style:style style:name="sfs_break_page" style:family="paragraph" style:parent-style-name="Standard"><style:properties fo:break-before="page"/></style:style>',$data);

	//��� content.xml
	$arr1 = explode("<office:body>",$data);
	//���Y
	$con_head = $arr1[0]."<office:body>";
	$arr2 = explode("</office:body>",$arr1[1]);
	//��Ƥ��e
	$con_body = $arr2[0];
	//�ɧ�
	$con_foot = "</office:body>".$arr2[1];
	$i=0;
	$replace_data ='';
	foreach($data_arr as $student_sn=>$stud_id){
		$i++;
		//�N content.xml �� tag ���N
		$temp = array();
		//���o�Ǯո��
   $temp_arr = get_school_base_array();
		//�Z�ŭӤH���
		$temp[]=get_stud_base_array($class_id,$stud_id);
		//�X�ʮu���
		$temp[]=get_abs_value($stud_id,$sel_year,$sel_seme,"����");
		//�X�ʮu��ơ]���Z���J���^
		$temp[]=get_abs_value($stud_id,$sel_year,$sel_seme,"����_��");
		//���y���
		$temp[]=get_reward_value2($stud_id,$sel_year,$sel_seme);
		//���y��ơ]���Z���J���^
		$temp[]=get_reward_value($stud_id,$sel_year,$sel_seme,"����_��");
		//�`���P����
		$temp[]=get_nor_value($student_sn,$sel_year,$sel_seme,$class_id);
		//�ͬ����{��r
		$temp[]=get_nor_text($student_sn,$sel_year,$sel_seme);
		//�ˮ֪���r
		$temp[]=get_chk_text($student_sn,$sel_year,$sel_seme,$CHK_KIND);
		//�ͬ����{���q
		$temp[]=get_performance_value($stud_id,$sel_year,$sel_seme);
		//���o�Ǵ���T
		$temp[]=get_all_days($sel_year,$sel_seme,$class_id);
		//���Z���
		$temp[]=get_score_array($sel_year,$sel_seme,$stud_id,$student_sn,$class_id);
		//�E�~�@�e���Z���
		$ss9_array=get_ss9_array($sel_year,$sel_seme,$stud_id,$student_sn,$class_id);
		$temp[]=$ss9_array;
		//�E�~�@�e�l�ͦ��Z���
		$temp[]=get_ssm_array($ss9_array,$class_id);

		foreach($temp as $t_arr){
			if (count($t_arr))
				$temp_arr = array_merge($temp_arr,$t_arr);
		}


		// change_temp �|�N�}�C���� big5 �ର UTF-8 �� openoffice �i�HŪ�X
		$replace_data .= $ttt->change_temp($temp_arr,$con_body,0);

		//�����B�z
		if ($i<count($data_arr))
			$replace_data .='<text:p text:style-name="sfs_break_page"/>';

	}

	$replace_data = $ttt->change_temp2(array("break_text"=>"<text:line-break/>"),$replace_data);
	$replace_data = $con_head.$replace_data.$con_foot;

	//��@�Ǧh�l�����ҥH�ťը��N
	$pattern[]="/\{([^\}]*)\}/";
	$replacement[]="";

	$replace_data=preg_replace($pattern, $replacement, $replace_data);


	// �[�J content.xml ��zip ��
	$ttt->add_file($replace_data,"content.xml");

	//���� zip ��
	$sss = & $ttt->file();

	//�H��y�覡�e�X ooo.sxw
	header("Content-disposition: attachment; filename=$filename");
	header("Content-type: application/vnd.sun.xml.writer");
	//header("Pragma: no-cache");
				//�t�X SSL�s�u�ɡAIE 6,7,8�U�������D�A�i��ק� 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
	header("Expires: 0");
	echo $sss;
	exit;
}
?>