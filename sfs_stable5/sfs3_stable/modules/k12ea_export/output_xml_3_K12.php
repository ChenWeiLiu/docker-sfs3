<?php
// $Id: output_xml.php 8928 2016-07-20 18:11:45Z smallduh $

require "config.php";
require "class.php";
set_time_limit(0);
ini_set('memory_limit', '100M');
sfs_check();

//���o�Z�ż�
$year=curr_year();
$seme=curr_seme();
//�ثe�Ǵ�
$c_curr_seme=sprintf('%03d%1d',$year,$seme);

/*
$sql="select distinct c_year from school_class where year='{$year}' and semester='$seme' order by c_year";
$res=$CONN->Execute($sql) or die($sql);
$row=$res->getRows();
$select_year=array();
foreach ($row as $v) {
	$select_year[$v[c_year]]=$v['c_year']."�~��";
}
*/

$all_reward=$_POST['all_reward'];
$select_year[99]="���վǥ�";

$selected_year=($_POST['output_selected'])?$_POST['output_selected']:99;

$all_reward_checked=$all_reward?"checked":"";
//�ꤤ�[�J�ͲP���ɿ�X�ﶵ
$checked=$IS_JHOES?'checked':'';
$career_checkbox="<input type='checkbox' name='career' value=1 $checked>��X�ꤤ�ͲP���ɤ�U���(�ݦ��w�ˬ����Ҳ�)";
$smarty->assign("career_checkbox",$career_checkbox);
$smarty->assign("select_year",$select_year);
$smarty->assign("selected_year",$selected_year);
$smarty->assign("all_reward_checked",$all_reward_checked);

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
$smarty->assign("module_name","���q3.0 XML�ץX(��e�p)");
$smarty->assign("SFS_MENU",$toxml_menu);
if ($_POST[output_xml]) {
	$smarty->assign("output",1);
} else {
	$smarty->assign("output",0);
}
$smarty->display("toxml_output_school_xml.tpl");


//��buffer �������e�X
ob_flush();
flush();

//�p�G�T�w��XXML�ɮ�
if ($_POST[output_xml]) {
	if( ini_get('safe_mode') ){
		echo "Safe Mode ���}�F! �нվ� php.ini �⥦����!<br>";
	}else{
		// it's not
	}
	//���X�Ҧ���w�� sn
	switch ($_POST['output_selected']) {
		case '99':
			$sql="select count(*) from stud_base where stud_study_cond=0 or stud_study_cond=15";
			$res=$CONN->Execute($sql) or die($sql);
			$total=$res->fields[0];
			//�p��n�B�z���Z�ż�
			//$total = count($selected_student);
			$sql="select class_id from school_class where year='{$year}' and semester='$seme' order by class_id";
			$res=$CONN->Execute($sql) or die($sql);
			$class_row=$res->getRows();
			break;

	}


	//?�ܪ�?��??�סA?�� px
	$width = 500;
	//�C???���ާ@�ҥe��?��??��?��
	$pix = $width / $total;
	//�q??�l��?��?�ʤ���
	$progress = 0;
	echo "
         <script language=\"JavaScript\">
         <!--
         function updateProgress(sMsg, iWidth)
         {
          document.getElementById(\"status\").innerHTML = sMsg;
          document.getElementById(\"progress\").style.width = iWidth + \"px\";
          document.getElementById(\"percent\").innerHTML = parseInt(iWidth / ".$width." * 100) + \"%\";
          }
         -->
         </script>
    ";
	?>
	<div style="width:100%" id="process_show">
			<div style="margin:50px auto; padding: 8px; border: 1px solid gray; background: #EAEAEA; width: <?php echo $width+16; ?>px">
				<div style="padding: 0; background-color: white; border: 1px solid navy; width: <?php echo $width; ?>px">
					<div id="progress" style="padding: 0; background-color: #FFCC66; border: 0; width: 0px; text-align: center; height: 20px"></div>
				</div>
				<div id="status"></div>
				<div id="percent" style="position: relative; top: -34px; text-align: center; font-weight: bold; font-size: 8pt">0%</div>
			</div>
	</div>

	<?php
	ob_flush();
	flush();  //�N��� buffer ����X���s����
	//exit();

	$start_time=date("Y-m-d H:i:s");

	$ini_val=ini_get('upload_tmp_dir');
	$tmp_path = $ini_val ? $ini_val : sys_get_temp_dir();
	//�ɦW
	$filename=md5($SCHOOL_BASE['sch_id'].time());
	if (file_exists($tmp_path.'/'.$filename)) unlink($tmp_path.'/'.$filename);
	$i=$j=0;
	$out_xml_file = fopen($tmp_path.'/'.$filename, "a") or die("Unable to open file! �ɮסG".$tmp_path.$filename);
	foreach ($class_row as $class) {

		$class_id=$class['class_id'];
		$seme_class=sprintf('%d%02d',substr($class_id,6,2),substr($class_id,9,2));

		$query="select b.student_sn from stud_base a,stud_seme b where a.student_sn=b.student_sn and b.seme_class='$seme_class' and b.seme_year_seme='$c_curr_seme' and (a.stud_study_cond=0 or a.stud_study_cond=15) order by seme_num";
		$res=$CONN->Execute($query) or die ($query);
		$row_stud=$res->getRows();
		$stud_arr=array();
		foreach ($row_stud as $v) {
			if ($v['student_sn']>0) {
				$stud_arr[$v['student_sn']]=$v['student_sn'];
			}
		}

		$xml_obj=new sfsxmlfile();
		$xml_obj->student_sn=$stud_arr;

		$xml_obj->output();

		//�ǮեN�X $school_edu_id
		$smarty->assign("school_edu_id",$SCHOOL_BASE['sch_id']);
		//���y���
		$smarty->assign("data_arr",$xml_obj->out_arr);
		//�ʧO�}�C
		$smarty->assign("sex_arr",array("1"=>"�k","2"=>"�k"));
		//�����O�}�C (�Ƶ��Ȥ�����)
		$smarty->assign("stud_kind_arr",stud_kind());
		//�ҷ����O�}�C
		$smarty->assign("id_kind_arr",stud_country_kind());
		//�ǥͯZ�ũʽ�}�C
		$smarty->assign("class_kind_arr",stud_class_kind());

		//�ǥͯS��Z���O�}�C
		$smarty->assign("spe_kind_arr",stud_spe_kind());
		//�ǥͯS��Z�W�ҩʽ�}�C
		$smarty->assign("spe_class_id_arr",stud_spe_class_id());
		//�ǥͯS��Z�Z�O�}�C
		$smarty->assign("spe_class_kind_arr",stud_spe_class_kind());
		//�ꤤ�p�P�w SFS 4.0 �����ץ�
		$smarty->assign("jhores",$IS_JHORES);
		//�J�Ǹ��}�C
		$smarty->assign("preschool_status_arr",stud_preschool_status());

		//���׷~�}�C
		$smarty->assign("grad_kind_arr",grad_kind());

		//�s�\�}�C
		$smarty->assign("is_live_arr",is_live());
		//�P�����Y�}�C
		$smarty->assign("f_rela_arr",fath_relation());
		//�P�����Y�}�C
		$smarty->assign("m_rela_arr",moth_relation());
		//�P���@�H���Y�}�C
		$smarty->assign("g_rela_arr",guardian_relation());
		//�Ǿ��}�C
		$smarty->assign("edu_kind_arr",edu_kind());
		//�S�̩j�f�}�C
		$smarty->assign("bs_calling_kind_arr",bs_calling_kind());

		//�ͲP���ɦҼ{�]���}�C
		$factor_items=array('self'=>'�ӤH�]��','env'=>'���Ҧ]��','info'=>'��T�]��');
		foreach($factor_items as $item=>$title){
			$factors[$item]=SFS_TEXT($title);
		}
		$smarty->assign("factors",$factors);

		//����U�Ǵ����X�u���
		$query="select * from seme_course_date order by seme_year_seme,class_year";
		$res=$CONN->Execute($query);
		while(!$res->EOF) {
			$current_seme_year_seme=$res->fields[seme_year_seme];
			$row_data=$res->FetchRow();
			$seme_course_date_arr[$current_seme_year_seme][$row_data['class_year']]=$row_data['days'];
		}
		$smarty->assign("seme_course_date_arr",$seme_course_date_arr);

		//�B�z�i��
		$i+=count($stud_arr);
		$progress = $pix*$i;
		if ($i==$total) $progress=500;
		?>
		<script language="JavaScript">
			updateProgress("�w�B�z <?php echo $i; ?> ��....",<?php echo min($width, intval($progress)); ?>);
		</script>
		<?php

		ob_flush();
		flush(); //�N��� buffer ����X���s����
		ob_clean();


		//�Nsmarty��X����ƥ�cache��
		ob_start();
		$smarty->display("student_3_K12.tpl");
		$xmls=ob_get_contents();
		ob_end_clean();
		//ob_clean();
		//�N�ŭȥHnull���N
		$xmls=str_replace("><",">null<",$xmls);
		$xmls=str_replace("> <",">null<",$xmls);
		$xmls=str_replace("&","��",$xmls);
		$xmls=str_replace("��amp;","&amp;",$xmls);
		$xmls=str_replace("��gt;","&gt;",$xmls);
		$xmls=str_replace("��lt;","&lt;",$xmls);
		$xmls=str_replace("��quot;","&quot;",$xmls);
		$xmls=str_replace("��apos;","&apos;",$xmls);

		/* �̲M�j��e�p�ݨD, ���N�Y�ǸӨt�Τ������r�� */
		//���N
		$xmls=str_replace("<�ҷӺ���>�����Ҧr��</�ҷӺ���>","<�ҷӺ���>������</�ҷӺ���>",$xmls);
		$xmls=str_replace("<�ҷӺ���>null</�ҷӺ���>","<�ҷӺ���>������</�ҷӺ���>",$xmls);
		$xmls=str_replace("<���y>null</���y>","<���y>���إ���</���y>",$xmls);
		$xmls=str_replace("<�Z�ũʽ�>�@��Z</�Z�ũʽ�>","<�Z�ũʽ�>���q�Z</�Z�ũʽ�>",$xmls);
		$xmls=str_replace("<���ޱФ覡>���D</���ޱФ覡>","<���ޱФ覡>���D��</���ޱФ覡>",$xmls);
		$xmls=str_replace("<���ޱФ覡>���D</���ޱФ覡>","<���ޱФ覡>���D��</���ޱФ覡>",$xmls);
		$xmls=str_replace("<���ޱФ覡>�v��</���ޱФ覡>","<���ޱФ覡>�v�¦�</���ޱФ覡>",$xmls);
		$xmls=str_replace("<���ޱФ覡>�v��</���ޱФ覡>","<���ޱФ覡>�v�¦�</���ޱФ覡>",$xmls);
		$xmls=str_replace("<���ޱФ覡>���</���ޱФ覡>","<���ޱФ覡>�����</���ޱФ覡>",$xmls);
		$xmls=str_replace("<���ޱФ覡>���</���ޱФ覡>","<���ޱФ覡>�����</���ޱФ覡>",$xmls);
		$xmls=str_replace(" </�ǥͩm�W>","</�ǥͩm�W>",$xmls);

		//���h�S��Ÿ��y�������~
		$xmls=preg_replace('/[\b]/', ' ', $xmls);   //Backspace��



		//�B�z�ĴX�Z
		$j++;
		//�j��@�ӯZ�A���h�Y�h���X�ְʧ@
		if (count($class_row)>1) {
			if ($j==1) {
				//�Ĥ@�Z�h��
				$s=strpos($xmls,"</���y�洫���>");
				$xmls=substr($xmls,0,$s);
			} elseif ($j==count($class_row)) {
				//�̥��Z�h�Y
				$s=strpos($xmls,"	<�ǥ͸��>");
				$xmls=substr($xmls,$s);
			} else {
				//�����Z
				//�h�Y
				$s=strpos($xmls,"	<�ǥ͸��>");
				$xmls=substr($xmls,$s);
				//�h��
				$s=strpos($xmls,"</���y�洫���>");
				$xmls=substr($xmls,0,$s);
			}

		}

		fwrite($out_xml_file, big5_to_utf8($xmls));
		//fwrite($out_xml_file, $xmls);
	}

	ob_end_clean();

	fclose($out_xml_file);

	$end_time=date("Y-m-d H:i:s");

	echo "<div style='text-align: center'>����ɶ� ".substr($start_time,11)." -> ".substr($end_time,11)."

	 <span id='download_button'><input type='button' value='�U���ɮ�' id='download'></div></span>
	</div>

	<Script>
	   $(\"#download\").click(function(){
	   	d = new Date();
	   	var now_time=d.toLocaleTimeString();
	    $(\"#download_button\").html('( �w�󥻾��ɶ� '+now_time+' �U���A�Ȧs�ɤw�q���A���t�Τ��R��! )')
	     window.location='output_xml_download.php?set=$filename';
	   });
	</Script>

	";




}



?>