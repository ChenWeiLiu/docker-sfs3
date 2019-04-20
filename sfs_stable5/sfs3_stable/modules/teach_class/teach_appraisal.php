<?php
// $Id: teach_list.php 7454 2013-08-30 01:30:19Z hami $
// �Үִ���
// ���J�]�w��
include "teach_config.php";

// �{���ˬd
sfs_check();
//����޲z�v
$module_manager=checkid($_SERVER['SCRIPT_FILENAME'],1);
if ($module_manager!=1) {
	echo "��p , �z�S���L�޲z�v��!";
	exit();
}

//AJAX
if ($_POST['act']=='delete_one') {

	header('Content-type: text/html;charset=big5');

	$sn=$_POST['sn'];
	$sql="select a.*,b.name from teacher_appraisal a,teacher_base b where a.sn='{$sn}' and a.teacher_sn=b.teacher_sn";
	$res=$CONN->Execute($sql);

	$sql="delete from teacher_appraisal where sn='{$sn}'";
	if ($CONN->Execute($sql)) {
		$return_data="�u".$res->fields['name']."-".$res->fields['title']."�v�w�R��!";
		//���s��z��ƶ���
		$teacher_sn=$res->fields['teacher_sn'];
		$sql="select * from `teacher_appraisal` where teacher_sn='{$teacher_sn}' order by rec_sort";
		$res=$CONN->Execute($sql);
		$i=0;
		while ($row=$res->fetchRow()) {
			$i++;
			$sn=$row['sn'];
			$CONN->Execute("update `teacher_appraisal` set rec_sort='{$i}' where sn='{$sn}'");
		} // end while


	} else {
		$return_data="�R������! SQL=".$sql;
	}

	echo $return_data;
	exit();
}
//AJAX ���o�~�B�}�C�αM�~�[���}�C
if ($_POST['act']=='get_salary_array') {

	header('Content-type: text/html;charset=big5');

	$salary_kind=$_POST['salary_kind'];

	$return_data['salary_array']=$salary_array[$salary_kind];
	$return_data['spec_array']=$spec_array[$salary_kind];

	if ($slary!="teacher") {
		$return_data['init_spec_allowance']=get_init_spec_allowance($salary_kind,0,"",$spec_array[$salary_kind]);
	} else {
		$return_data['init_spec_allowance']=0;
	}

	$returnJson=json_encode($return_data);

	echo $returnJson;

	exit();

}

//���o�w�]���M�~�[��
if ($_POST['act']=='get_init_spec_allowance') {

	header('Content-type: text/html;charset=big5');

	$teacher_sn=$_POST['teacher_sn'];
	$salary_kind=$_POST['salary_kind'];
	$base_salary=$_POST['base_salary'];

	$return_data['salary_array']=$salary_array[$salary_kind];
	$return_data['spec_array']=$spec_array[$salary_kind];

	//���o¾��
	$sql="select b.title_name,c.teach_edu_kind from teacher_post a,teacher_title b,teacher_base c where a.teacher_sn='$teacher_sn' and a.teach_title_id=b.teach_title_id and a.teacher_sn=c.teacher_sn";
	$res=$CONN->Execute($sql);
	$title_name=$res->fields['title_name'];
	$teach_edu_kind=$res->fields['teach_edu_kind'];

	$init_spec_allowance=get_init_spec_allowance($salary_kind,$base_salary,$title_name,$spec_array[$salary_kind],$teach_edu_kind);

	$returnJson=json_encode($init_spec_allowance);

	echo $returnJson;

	exit();
}



//AJAX
if ($_POST['act']=='remove_teacher_sn') {

	header('Content-type: text/html;charset=big5');

	$teacher_sn=$_POST['teacher_sn'];
	$update_sn=$_SESSION['session_tea_sn'];

	$sql="select teacher_sn from `teacher_appraisal_avoid` where teacher_sn='{$teacher_sn}'";
	$res=$CONN->Execute($sql);
	if ($res->recordCount()==0) {
		$res=$CONN->Execute("select name from teacher_base where teacher_sn='{$teacher_sn}'");
		$name=$res->fields['name'];
		$sql="insert into `teacher_appraisal_avoid` (teacher_sn,update_sn) values ('{$teacher_sn}','{$update_sn}')";
		$res=$CONN->Execute($sql);
		echo $name;
	}

	exit();
}

//AJAX
if ($_POST['act']=='restore_teacher_sn') {

	header('Content-type: text/html;charset=big5');

	$teacher_sn=$_POST['teacher_sn'];
	$update_sn=$_SESSION['session_tea_sn'];

		$res=$CONN->Execute("select name from teacher_base where teacher_sn='{$teacher_sn}'");
		$name=$res->fields['name'];
		$sql="delete from `teacher_appraisal_avoid` where teacher_sn='{$teacher_sn}'";
		$res=$CONN->Execute($sql);
		echo $name;

	exit();
}


//AJAX
if ($_POST['act']=='detail_teacher_sn') {

	header('Content-type: text/html;charset=big5');

	$teacher_sn=$_POST['teacher_sn'];

	$query = "select * from teacher_appraisal where teacher_sn='{$teacher_sn}' order by rec_sort";
	$res = $CONN->Execute($query) or die("Error! SQL=" . $query);

	$row=$res->getRows();

	$out="<table border=\"1\" style=\"border-collapse:collapse;font-size:10pt\">
			<tr style=\"background-color: #B3CCAB\">
				<td style=\"width:100px\" rowspan=\"2\" align=\"center\">�n����</td>
				<td style=\"width:50px\" rowspan=\"2\"align=\"center\">�~��</td>
				<td style=\"width:70px\" rowspan=\"2\" align=\"center\">�~�B���O</td>
				<td style=\"width:50px\" rowspan=\"2\" align=\"center\">�~�B<br>(���I)</td>
				<td style=\"width:50px\" rowspan=\"2\" align=\"center\">�ǳN��s�O<br>(�M�~�[��)</td>
				<td style=\"width:80px\" rowspan=\"2\" align=\"center\">�D�ޥ[��</td>
				<td style=\"width:50px\" colspan=\"2\"align=\"center\">�a��[��</td>
				<td style=\"width:60px\" rowspan=\"2\" align=\"center\">���O</td>
				<td style=\"width:120px\" rowspan=\"2\" align=\"center\">�s�פ��</td>
				<td style=\"width:50px\" rowspan=\"2\" align=\"center\">�ާ@</td>
			</tr>
			<tr>
						<td style=\"width:60px;font-size:10pt;background-color: #DBCEB9\" align=\"center\">�a�ϯŧO</td>
						<td style=\"width:60px;font-size:10pt;background-color: #DBCEB9\" align=\"center\">�~��[��</td>
			</tr>
	";

	foreach ($row as $R) {
		$out.="<tr id='tr_{$R['sn']}'>
				<td style=\"width:100px\" align=\"center\">{$R['title']}</td>
				<td style=\"width:50px\" align=\"center\">{$R['years_of_service']}</td>
				<td style=\"width:70px\" align=\"center\">{$salary_kind_array[$R['salary_kind']]}</td>
				<td style=\"width:50px\" align=\"center\">{$R['base_salary']}</td>
				<td style=\"width:80px\" align=\"center\">".$R['spec_allowance']."</td>
				<td style=\"width:80px\" align=\"center\">".$R['leader_allowance']."</td>
				<td style=\"width:50px\" align=\"center\">{$R['area_kind']}</td>
				<td style=\"width:60px\" align=\"center\">{$R['area_years_of_service']}</td>
				<td style=\"width:60px\" align=\"center\">{$R['memo']}</td>
				<td style=\"width:120px\" align=\"center\">{$R['update_time']}</td>
				<td align=\"center\"><input type='button' value='�R��' onclick=\"one_delete(this,{$R['sn']})\" style=\"font-size:10pt;background-color: #FFCCCC\"></td>
			</tr>
	";


	}

	$out.="</table> <input type='button' value='����' onclick='hide_detail(\"{$teacher_sn}\")'>";
	echo $out;
	exit();
}

//AJAX
if ($_POST['act']=='add_one_form') {

	header('Content-type: text/html;charset=big5');

	$teacher_sn=$_POST['teacher_sn'];
	$last_year = "select a.*,b.teach_edu_kind,d.title_name from teacher_appraisal a,teacher_base b,teacher_post c,teacher_title d where a.teacher_sn='{$teacher_sn}' and a.teacher_sn=b.teacher_sn and a.teacher_sn=c.teacher_sn and c.teach_title_id=d.teach_title_id order by rec_sort desc limit 1";
	$res_last = $CONN->Execute($last_year) or die("Ū����ƥ���! SQL=" . $sql);
	if ($res_last->recordCount()) {
		$title = $res_last->fields['title'];
		$years_of_service = $res_last->fields['years_of_service'];
		$salary_kind=$res_last->fields['salary_kind'];
		$base_salary = $res_last->fields['base_salary'];
		$leader_allowance = $res_last->fields['leader_allowance'];
		$spec_allowance = $res_last->fields['spec_allowance'];
		$area_kind = $res_last->fields['area_kind'];
		$area_years_of_service = $res_last->fields['area_years_of_service'];
		$memo = $res_last->fields['memo'];
		$teach_edu_kind=$res_last->fields['teach_edu_kind'];
		$title_name=$res_last->fields['title_name'];
	}else {
		$title = $years_of_service = $area_kind=$area_years_of_service="<span style='color:#FF0000'>�L</span>";
		$area_years_of_service=0;
		$salary_kind="teacher";
		$spec_allowance=20130;
		$teach_edu_kind=4;
		$title_name="�Юv";
		$area_kind="�L";
		$base_salary = 90;
		$leader_allowance = 0;
	}
	$teacher_salary_array=$salary_array[$salary_kind];
	$teacher_spec_array=$spec_array[$salary_kind];
	$init_spec_allowance=get_init_spec_allowance($salary_kind,$base_salary,$title_name,$teacher_spec_array,$teach_edu_kind);
	$init_spec_allowance=($init_spec_allowance<$spec_allowance)?$spec_allowance:$init_spec_allowance;
	//�~�B���O
	$option_salary_kind="";
	foreach ($salary_kind_array as $k=>$k_salary) {
		$option_salary_kind.="<option value=\"$k\"".(($salary_kind == $k) ? " selected" : "").">$k_salary</option>";
	}
	//�~�B
	$option_salary="";
	foreach ($teacher_salary_array as $salary) {
		$option_salary.="<option value=\"$salary\"".(($salary == $base_salary) ? " selected" : "").">$salary</option>";
	}
	//�M�~�[��
	foreach ($teacher_spec_array as $spec) {
		$option_spec.="<option value=\"$spec\"".(($spec == $init_spec_allowance) ? " selected" : "").">$spec</option>";
	}

	//�a��[��
	$option_area="";
	foreach ($area_allowance_array as $area_k=>$v) {
		$option_area.="<option value=\"$area_k\"".(($area_kind==$area_k)?"selected":"").">$area_k</option>";
	}
	$out="
		<form method='post' action='".$_SERVER['PHP_SELF']."'>
		 <input type='hidden' name='act' value='add_one'>
		 <input type='hidden' name='teacher_sn' value='{$teacher_sn}'>
		<table border=\"1\" style=\"border-collapse:collapse;font-size:10pt\">
			<tr style=\"background-color: #B3CCAB\">
				<td style=\"width:120px\" rowspan=\"2\" align=\"center\">�n����</td>
				<td style=\"width:50px\" rowspan=\"2\"align=\"center\">�~��</td>
				<td style=\"width:80px\" rowspan=\"2\" align=\"center\">�~�B���O</td>
				<td style=\"width:50px\" rowspan=\"2\" align=\"center\">�~�B<br>(���I)</td>
				<td style=\"width:80px\" rowspan=\"2\" align=\"center\">�ǳN��s�O<br>(�M�~�[��)</td>
				<td style=\"width:80px\" rowspan=\"2\" align=\"center\">�D�ޥ[��</td>
				<td style=\"width:120px\" colspan=\"2\"align=\"center\">�a��[��</td>
				<td style=\"width:120px\" rowspan=\"2\" align=\"center\">���O</td>
			</tr>
			<tr>
						<td style=\"width:60px;font-size:10pt;background-color: #DBCEB9\" align=\"center\">�a�ϯŧO</td>
						<td style=\"width:60px;font-size:10pt;background-color: #DBCEB9\" align=\"center\">�~��[��</td>
			</tr>
			<tr>
						<td><input type='text' name='title' value='{$title}' style='width:120px'></td>
						<td><input type='text' name='years_of_service' value='{$years_of_service}' style='width:50px'></td>
						<td>
							<select size='1' name='salary_kind' id='salary_kind_{$teacher_sn}_one' onchange=\"chang_salary_kind('salary_kind_{$teacher_sn}_one','salary_selection_{$teacher_sn}_one','spec_selection_{$teacher_sn}_one')\">
							{$option_salary_kind}
							</select>
						</td>
						<td>
							<select size='1' name='base_salary' id='salary_selection_{$teacher_sn}_one' onchange=\"chang_base_salary('salary_kind_{$teacher_sn}_one','salary_selection_{$teacher_sn}_one','spec_selection_{$teacher_sn}_one')\">
							{$option_salary}
							</select>
						</td>
						<td>
							<select size='1' name='spec_allowance' id='spec_selection_{$teacher_sn}_one'>
							{$option_spec}
							</select>
						</td>
						<td>
							<input type='text' name='leader_allowance' value='{$leader_allowance}' style='width:50px'>
						</td>
						<td>
							<select size='1' name='area_kind'>{$option_area}</select>
						</td>
						<td>
							<input type='text' name='area_years_of_service' value='{$area_years_of_service}' style='width:50px'>%
						</td>
						<td>
							<input type='text' name='memo' value='{$memo}' style='width:120px'>
						</td>
			</tr>
	";

	$out.="</table>
<input type='submit' value='�x�s�O��'> <span style='font-size:10pt'>
<input type='button' value='�����s��' onclick='hide_detail(\"{$teacher_sn}\")'>
(�`�N! �ק�u�n�����v�W�٫h�t�αN�t�s���@���s�O��)</span></form>";
	echo $out;
	exit();
}


//¾�����O
$POST_KIND = post_kind();

//�Ǿ����O
$TEA_EDU_KIND=tea_edu_kind();

//���
$tool_bar=&make_menu($teach_menu_p);

//���o�Үֱư��W��
$sql="select a.teacher_sn,b.teach_id,b.name,d.title_name from `teacher_appraisal_avoid` a, teacher_base b ,teacher_post c,teacher_title d where a.teacher_sn=b.teacher_sn and b.teacher_sn=c.teacher_sn and c.teach_title_id=d.teach_title_id and b.teach_condition=0";
$res=$CONN->Execute($sql) or die ("�L�kŪ���ư��W����! SQL=".$sql);
$avoid=array();
$i=0;
while ($row=$res->fetchRow()) {
	$teacher_sn=$row['teacher_sn'];
	$avoid['list'][]=$teacher_sn;
	$avoid['detail'][$teacher_sn]['name']=$row['name'];
	$avoid['detail'][$teacher_sn]['title_name']=$row['title_name'];
	$avoid['detail'][$teacher_sn]['teach_id']=$row['teach_id'];
}
$teacher_sn_avoid="('".implode("','",$avoid['list'])."')";

//�u�C�X�����O  / �����ư� ��߱Юv,�եαЮv,�N�z/�N�ұЮv,�ݥ��Юv,ĵ��,�u��
$post_kind_require="('1','2','3','4','5','6','7','12','13')";

//�妸�s�W�~�׸��
if ($_POST['act']=='add_years_data') {
	$update_sn=$_SESSION['session_tea_sn'];
	foreach ($_POST['S'] as $teacher_sn=>$s) {

		//�Үָ�Ʒs�W
		$title=$s['title'];
		$years_of_service=$s['years_of_service'];
		$salary_kind=$s['salary_kind'];
		$base_salary=$s['base_salary'];
		$spec_allowance=$s['spec_allowance'];
		$leader_allowance=$s['leader_allowance'];
		$area_kind=$s['area_kind'];
		$area_years_of_service=$s['area_years_of_service'];
		$memo=$s['memo'];
		//�Ǿ�
		$sql="update teacher_base set teach_edu_kind='{$s['teach_edu_kind']}' where teacher_sn='{$teacher_sn}'";
		$CONN->Execute($sql);

		//����ۦP���w�q�O�_�w�s�b
		$sql="select * from `teacher_appraisal` where teacher_sn='{$teacher_sn}' and title='{$title}'";
		$res=$CONN->Execute($sql);
		if ($res->recordCount()) {
			$sql="update `teacher_appraisal` set years_of_service='{$years_of_service}',salary_kind='{$salary_kind}',base_salary='{$base_salary}',spec_allowance='{$spec_allowance}',leader_allowance='{$leader_allowance}',area_kind='{$area_kind}',area_years_of_service='{$area_years_of_service}',memo='{$memo}',update_sn='{$update_sn}' where teacher_sn='{$teacher_sn}' and title='{$title}'";
			$res=$CONN->Execute($sql) or die("SQL �y�k����! SQL=".$sql);
		} else {
			//���o�Ӯv�w�s�b�O����
			$sql="select count(*) from `teacher_appraisal` where teacher_sn='{$teacher_sn}'";
			$res=$CONN->Execute($sql);
			$rec_sort=$res->fields[0]+1;
			$sql="insert into `teacher_appraisal` (title,rec_sort,teacher_sn,years_of_service,salary_kind,base_salary,spec_allowance,leader_allowance,area_kind,area_years_of_service,memo,update_sn) values ('$title','$rec_sort','$teacher_sn','$years_of_service','$salary_kind','$base_salary','$spec_allowance','$leader_allowance','$area_kind','$area_years_of_service','$memo','$update_sn')";
		}

		$res=$CONN->Execute($sql) or die ("�s�׸�ƥ���! SQL=".$sql);

	}

}

//�妸�s�W�~�׸��
if ($_POST['act']=='add_one') {
	//echo "<pre>";
	//print_r($_POST);
	//exit();
	$update_sn=$_SESSION['session_tea_sn'];
	$teacher_sn=$_POST['teacher_sn'];

		//�Үָ�Ʒs�W
		$title=$_POST['title'];
		$years_of_service=$_POST['years_of_service'];
		$salary_kind=$_POST['salary_kind'];
		$base_salary=$_POST['base_salary'];
		$spec_allowance=$_POST['spec_allowance'];
		$leader_allowance=$_POST['leader_allowance'];
		$area_kind=$_POST['area_kind'];
		$area_years_of_service=$_POST['area_years_of_service'];
		$memo=$_POST['memo'];

		//����ۦP���w�q�O�_�w�s�b
		$sql="select * from `teacher_appraisal` where teacher_sn='{$teacher_sn}' and title='{$title}'";
		$res=$CONN->Execute($sql);
		if ($res->recordCount()) {
			$sql="update `teacher_appraisal` set years_of_service='{$years_of_service}',salary_kind='{$salary_kind}',base_salary='{$base_salary}',spec_allowance='{$spec_allowance}',leader_allowance='{$leader_allowance}',area_kind='{$area_kind}',area_years_of_service='{$area_years_of_service}',memo='{$memo}',update_sn='{$update_sn}' where teacher_sn='{$teacher_sn}' and title='{$title}'";
			$res=$CONN->Execute($sql) or die("SQL �y�k����! SQL=".$sql);
		} else {
			//���o�Ӯv�w�s�b�O����
			$sql="select count(*) from `teacher_appraisal` where teacher_sn='{$teacher_sn}'";
			$res=$CONN->Execute($sql);
			$rec_sort=$res->fields[0]+1;
			$sql="insert into `teacher_appraisal` (title,rec_sort,teacher_sn,years_of_service,salary_kind,base_salary,spec_allowance,leader_allowance,area_kind,area_years_of_service,memo,update_sn) values ('$title','$rec_sort','$teacher_sn','$years_of_service','$salary_kind','$base_salary','$spec_allowance','$leader_allowance','$area_kind','$area_years_of_service','$memo','$update_sn')";
		}

		$res=$CONN->Execute($sql) or die ("�s�׸�ƥ���! SQL=".$sql);

}
//�L�X���Y
head("���ԦҮ�");
//�C�X���
echo $tool_bar;

//�C�X��¾��id
// ====================================================================
if ($_GET['act']=='') {
	?>
	<div style="margin-top: 5px;margin-bottom: 5px">
		<span style="border:2px #ccc solid;margin-top: 5px;margin-bottom: 5px;padding:5px;border-radius:10px;color:#FFFFFF;font-size:12pt;background-color: #117700">�̷s���Z�Үָ��</span>
		<input type="checkbox" id="check_teach_edu_kind" value="1" onclick="list_teach_edu_kind()"><span style="color:#3f91dd">�C�̰ܳ��Ǿ�</span>
		<input type="button" id="edit_year_data" value="�妸�s�צ~�׸��" onclick="window.location='<?php echo $_SERVER['PHP_SELF'];?>?act=Year_Assessment'">

	</div>
	<table border="0" style="border-collapse:collapse;">
		<tr>
			<td>
				<!-- ������ -->
				<table border="1" id="teacher_data"
					   style="border-style:solid;border-width:thin;border-collapse:collapse;border-color:#AAAAAA">
					<thead>
					<tr style="background-color: #8CCCCA;font-size:10pt">
						<td style="width:40px" rowspan="2" align="center">�Ǹ�</td>
						<td style="width:80px" rowspan="2" align="center">¾��</td>
						<td style="width:60px" rowspan="2" align="center">�m�W</td>
						<td style="width:80px;display: none" rowspan="2" align="center" class="teach_edu_kind">�̰��Ǿ�</td>
						<td style="width:100px" rowspan="2" align="center">�n����</td>
						<td style="width:50px" rowspan="2" align="center">�~��</td>
						<td style="width:80px" rowspan="2" align="center">�~�B���O</td>
						<td style="width:50px" rowspan="2" align="center">�~�B<br>(���I)</td>
						<td style="width:80px" rowspan="2" align="center">�ǳN��s�O<br>(�M�~�[��)</td>
						<td style="width:60px" rowspan="2" align="center">�D�ޥ[��</td>
						<td style="width:50px" colspan="2" align="center">�a��[��</td>
						<td style="width:60px" rowspan="2" align="center">���O</td>
						<td style="width:100px" rowspan="2" align="center">�s�פ��</td>
						<td style="width:100px" rowspan="2" align="center">�ާ@</td>
					</tr>
					<tr>
						<td style="width:60px;font-size:10pt;background-color: #a8ccb3" align="center">�a�ϯŧO</td>
						<td style="width:60px;font-size:10pt;background-color: #a8ccb3" align="center" style="font-size:8pt">�~��[��</td>
					</tr>
					</thead>
					<tbody>
						<?php
						$query = "select a.teacher_sn,c.teach_id,c.teach_edu_kind,b.title_name,c.name,a.class_num,a.post_kind from teacher_post a,teacher_title b,teacher_base c where a.teacher_sn not in ".$teacher_sn_avoid." and a.post_kind in " . $post_kind_require . " and a.teach_title_id=b.teach_title_id and a.teacher_sn=c.teacher_sn and c.teach_condition=0 order by a.post_kind,b.room_id,b.rank,a.class_num";
						$res = $CONN->Execute($query) or die("Error! SQL=" . $query);
						$serial_no = 0;
						while ($row = $res->fetchRow()) {
							$serial_no++;
							$teacher_sn=$row['teacher_sn'];
							//�p�G�O�ɮv, �C�X�Z��
							$title_name = ($row['post_kind'] == 6 and $row['class_num'] != "") ? $row['title_name'] . "(" . $row['class_num'] . ")" : $row['title_name'];

							//���o�h�~(�̫�@��)����ơA�w�]�۰ʤɤ@�� ,
							$last_year = "select * from teacher_appraisal where teacher_sn='{$row['teacher_sn']}' order by rec_sort desc limit 1";
							$res_last = $CONN->Execute($last_year) or die("Ū����ƥ���! SQL=" . $sql);
							if ($res_last->recordCount()) {
								$title = $res_last->fields['title'];
								$years_of_service = $res_last->fields['years_of_service'];
								$salary_kind=$res_last->fields['salary_kind'];
								$base_salary = $res_last->fields['base_salary'];
								$update_time = substr($res_last->fields['update_time'], 0, 10);
								$spec_allowance = $res_last->fields['spec_allowance'];
								$leader_allowance = $res_last->fields['leader_allowance'];
								$area_kind=$res_last->fields['area_kind'];
								$area_years_of_service=$res_last->fields['area_years_of_service'];
								$memo=$res_last->fields['memo'];
							} else {
								$title = $years_of_service = $base_salary = $allowance =$area_kind=$area_years_add="";
								$salary_kind="teacher";
								$update_time = "<span style='color:#dd0000'>�L���</span>";
							}
							?>
							<tr>
								<td align="center" style="font-size:10pt"><?php echo $serial_no; ?></td>
								<td align="center" style="font-size:10pt"><?php echo $title_name ?></td>
								<td align="center" style="font-size:10pt"><?php echo $row['name'] ?></td>
								<td align="center" style="font-size:8pt;display:none" class="teach_edu_kind"><?php echo $TEA_EDU_KIND[$row['teach_edu_kind']] ?></td>
								<td align="center" style="font-size:10pt"><?php echo $title; ?></td>
								<td align="center" style="font-size:10pt"><?php echo $years_of_service; ?></td>
								<td align="center" style="font-size:10pt"><?php echo $salary_kind_array[$salary_kind]; ?></td>
								<td align="center" style="font-size:10pt"><?php echo $base_salary; ?></td>
								<td align="center" style="font-size:10pt"><?php echo $spec_allowance; ?></td>
								<td align="center" style="font-size:10pt"><?php echo $leader_allowance; ?></td>
								<td align="center" style="font-size:10pt"><?php echo $area_kind; ?></td>
								<td align="center" style="font-size:10pt"><?php echo $area_years_of_service; ?></td>
								<td align="center" style="font-size:10pt"><?php echo $memo; ?></td>
								<td align="center" style="font-size:10pt"><?php echo $update_time; ?></td>
								<td>
									<input type="button" class="add_one" id="btn_addone_<?php echo $teacher_sn;?>" value="�s�צҮ�">
									<input type="button" class="show_detail" id="btn_detail_<?php echo $teacher_sn;?>" value="�˵��Ҧ��O��">
								</td>
							</tr>
							<tr id="tr_<?php echo $teacher_sn;?>" style="display:none">
								<td colspan="14">
									<div id="detail_<?php echo $teacher_sn;?>" style="padding-left:40px;padding-bottom: 10px">

									</div>
								</td>
							</tr>
							<?php
						}

						?>
					</tbody>
				</table>

			</td>

		</tr>
	</table>
					<?php
					if (count($avoid['list'])>0) {
					?>
							<div style="margin-top: 10px;margin-bottom: 10px">
								<span style="border:2px #ccc solid;margin-top: 5px;margin-bottom: 5px;padding:5px;border-radius:10px;color:#FFFFFF;font-size:8pt;background-color: #e7c0ce">�H�U���ư��W��</span>
							</div>
							<div>
							<table border="1" id="avoid_table" style="border-collapse:collapse">
								<tr style="background-color: #FF986C;">
									<td>¾��</td>
									<td>�m�W</td>
								</tr>
								<?php
								foreach ($avoid['detail'] as $teacher_sn=>$s) {
									?>
									<tr>
										<td><?php echo $s['title_name'];?></td>
										<td><?php echo $s['name'];?><img src="images/del.png" style="cursor: pointer" id="avoid_<?php echo $teacher_sn;?>" class="restore"></td>
									</tr>
									<?php
								}
								?>
							</table>
							</div>

				<?php
				} // end if count($avoid)>0
				?>


	<?php
}

//�s�W�~�׸��
if ($_GET['act']=='Year_Assessment') {
?>
<div style="margin-top: 5px;margin-bottom: 5px">
	<span style="border:2px #ccc solid;margin-top: 5px;margin-bottom: 5px;padding:5px;border-radius:10px;color:#FFFFFF;font-size:12pt;background-color: #117700">�~�׸�ƽs��</span><input type="checkbox" id="check_teach_edu_kind" value="1" onclick="list_teach_edu_kind()"><span style="color:#3f91dd">�C�̰ܳ��Ǿ�</span> <span style="font-size:10pt">(�`�N! ��Ʈw���Y�w�s�b�ۦP�u�n�����v�W�ٸ�ơA�t�αN���мg�覡�x�s���)</span>

</div>
<form name="myform" method="POSt" action="<?php echo $_SERVER['PHP_SELF']?>">
	<input type="hidden" name="act" value="">
	<table border="0" style="border-collapse:collapse;">
		<tr>
			<td>
				<!-- ������ -->
				<table border="1" id="teacher_data"
					   style="border-style:solid;border-width:thin;border-collapse:collapse;border-color:#AAAAAA">
					<thead>
					<tr style="background-color: #8CCCCA;font-size:10pt">
						<td style="width:50px" rowspan="2" align="center">�Ǹ�</td>
						<td style="width:70px" rowspan="2" align="center">¾��</td>
						<td style="width:70px" rowspan="2" align="center">�m�W</td>
						<td style="width:70px;display: none" rowspan="2" align="center" class="teach_edu_kind">�̰��Ǿ�</td>
						<td style="width:150px" rowspan="2" align="center">�n����</td>
						<td style="width:60px" rowspan="2" align="center">�~��</td>
						<td style="width:80px" rowspan="2" align="center">�~�B���O</td>
						<td style="width:90px" rowspan="2" align="center">�~�B<br>(���I)</td>
						<td style="width:70px" rowspan="2" align="center">�ǳN��s�O<br>(�M�~�[��)</td>
						<td style="width:70px" rowspan="2" align="center">�D�ޥ[��</td>
						<td style="width:160px" colspan="2" align="center">�a��[��</td>
						<td style="width:100px" rowspan="2" align="center">���O</td>
						<td style="width:50px" rowspan="2" align="center">�]�w<br>�ư�</td>
					</tr>
					<tr style="background-color: #8CCCCA">
						<td style="width:100px;font-size:10pt;background-color: #a8ccb3" align="center">�a�ϯŧO</td>
						<td style="width:60px;font-size:10pt;background-color: #a8ccb3" align="center">�~��[��%</td>
					</tr>
					</thead>
					<tbody>
						<?php
						$query = "select a.teacher_sn,c.teach_id,c.teach_edu_kind,b.title_name,c.name,a.class_num,a.post_kind from teacher_post a,teacher_title b,teacher_base c where a.teacher_sn not in ".$teacher_sn_avoid." and a.post_kind in " . $post_kind_require . " and a.teach_title_id=b.teach_title_id and a.teacher_sn=c.teacher_sn and c.teach_condition=0 order by a.post_kind,b.room_id,b.rank,a.class_num";
						$res = $CONN->Execute($query) or die("Error! SQL=" . $query);
						$serial_no = 0;
						while ($row = $res->fetchRow()) {
							$serial_no++;
							$teacher_sn=$row['teacher_sn'];
							//�p�G�O�ɮv, �C�X�Z��
							$title_name = ($row['post_kind'] == 6 and $row['class_num'] != "") ? $row['title_name'] . "(" . $row['class_num'] . ")" : trim($row['title_name']);

							//���o�h�~(�̫�@��)����ơA�w�]�۰ʤɤ@�� ,
							$last_year = "select * from teacher_appraisal where teacher_sn='{$row['teacher_sn']}' order by rec_sort desc limit 1";
							$res_last = $CONN->Execute($last_year) or die("Ū����ƥ���! SQL=" . $sql);
							if ($res_last->recordCount()) {
								$title = $res_last->fields['title'];
								$years_of_service = $res_last->fields['years_of_service'];
								$area_years_of_service = $res_last->fields['area_years_of_service'];
								$area_kind = $res_last->fields['area_kind'];
								$salary_kind=($res_last->fields['salary_kind']=='')?"teacher":$res_last->fields['salary_kind'];
								$base_salary = $res_last->fields['base_salary'];
								$spec_allowance = $res_last->fields['spec_allowance'];
								$update_time = substr($res_last->fields['update_time'], 0, 10);
								$leader_allowance = $res_last->fields['leader_allowance'];
								$memo=$res_last->fields['memo'];
							} else {
								$title = $years_of_service = $area_kind=$area_years_of_service="<span style='color:#FF0000'>�L</span>";
								$salary_kind="teacher";
								$spec_allowance=20130;
								$area_years_of_service=0;
								$area_kind="�L";
								$base_salary = "";
								$leader_allowance = 0;
								//$update_time = "<span style='color:#dd0000'>�L���</span>";
							}
							//�w�]����
							if ($salary_kind=="teacher") {
								$M=date("n");  //�S���� 0 ����� , �Ш|�H���j��7/31�~��Ү֤W�@�Ǧ~���Z
								$init_title=($M>7)?(date("Y")-1912)."�Ǧ~���Z�Ү�":(date("Y")-1913)."�Ǧ~���Z�Ү�";
							} else {
								$init_title=(date("Y")-1912)."�~�~�צ��Z";
							}

							$teacher_salary_array=$salary_array[$salary_kind];
							//�ǳN��s�O�ϥΪ��ѷӪ�
							$teacher_spec_array=$spec_array[$salary_kind];

							//�ˬd�O�_���ۦP���n�����A�Y�D�A�h�w�]�ɵ�
							if ($init_title==$title) {
								//�w�]�~��
								$init_years_of_service = $years_of_service;
								//�w�]�~�B
								$init_salary=$base_salary;
								//�w�]�M�~�[��
								$init_spec_allowance=$spec_allowance;
								//�]���{����s, �p�G���e �M�~�[���� 0
								if ($init_spec_allowance==0) {
									$init_spec_allowance=get_init_spec_allowance($salary_kind,$init_salary,$title_name,$teacher_spec_array,$row['teach_edu_kind']);
								}

								//�w�]�a��[��
								$init_area_years_of_service=$area_years_of_service;
								//
								$init_memo=$memo;
							} else {
								//�w�]�~��
								$init_years_of_service = $years_of_service + 1;
								//�~�B���I �p�G�O�Юv, �̾Ǿ����䳻�I, ��L�h�̰}�C��d��
								if ($salary_kind=='teacher') {
									switch ($row['teach_edu_kind']) {
										case '1':
											$top_salary=35;  //680 �դh
											break;
										case '2':
											$top_salary=34;	//650 �Ӥh
											break;
										case '3':
											$top_salary=34;	//650 �|�Q�Ǥ��Z
											break;
										default:
											$top_salary=33;	//625 �j��
									}
									//�w�]�~�B
									$init_salary = ($base_salary)?$base_salary:90;
									//�w�]�ǳN��s�O
										//�᭱���B��X��A�P�_
								} else {
									$top_salary=count($teacher_salary_array)-1;
									$init_salary = ($base_salary)?$base_salary:$teacher_salary_array[0];
									//�w�]�M�~�[��
									/*
									if (substr($salary_kind,0,8)=='official') {
										$d=explode("-",$salary_kind);
										$init_spec_allowance=$teacher_spec_array[$d[1]];
									} else {
										$init_spec_allowance=$teacher_spec_array[1];
									}
									$init_spec_allowance=($init_spec_allowance<$spec_allowance)?$spec_allowance:$init_spec_allowance;
									*/
								}
								//���R�w�]�~�B���S���W�L���I
								$L=0;
								foreach ($teacher_salary_array as $k=>$s) {
									if ($s>$init_salary) {
										$L=$k;
										//���H���~�B�W�L���I, �N�H���I�p
										$L=($L>$top_salary)?$top_salary:$L;
										break;
									}
								}
								if ($L==0) $L=$k;
								$init_salary=$teacher_salary_array[$L];

								//�P�_�Юv���ǳN��s�O
								$init_spec_allowance=get_init_spec_allowance($salary_kind,$init_salary,$title_name,$teacher_spec_array,$row['teach_edu_kind']);
								$init_spec_allowance=($init_spec_allowance<$spec_allowance)?$spec_allowance:$init_spec_allowance;


								//�w�]�a��[��
								$init_area_years_of_service=($area_years_of_service>0)?$area_years_of_service+1:0;
								//�w�]���O
								$init_memo="�|���@";
							}

							$init_area_kind=$area_kind;

							?>
							<tr id="tr_<?php echo $teacher_sn;?>">
								<td align="center" style="font-size:10pt"><?php echo $serial_no; ?></td>
								<td align="center" style="font-size:10pt"><?php echo $title_name ?></td>
								<td align="center" style="font-size:10pt"><?php echo $row['name'] ?></td>
								<td align="center" style="font-size:10pt;display:none" class="teach_edu_kind">
									<select size="1" name="S[<?php echo $teacher_sn; ?>][teach_edu_kind]" style="font-size: 8pt">
										<option value="">&nbsp;</option>
										<?php
										foreach ($TEA_EDU_KIND as $k=>$v) {
											?>
											<option value="<?php echo $k;?>" <?php echo ($k==$row['teach_edu_kind'])?" selected":"";?>><?php echo $v;?></option>
											<?php
										} // end foreach
										?>
									</select>
								</td>

								<td align="center" style="font-size:10pt"><?php echo $title; ?>/<input type="text"
																			 name="S[<?php echo $teacher_sn; ?>][title]"
																			 value="<?php echo $init_title;?>" style="width:100px"></td>
								<td align="center" style="font-size:10pt"><?php echo $years_of_service; ?>/<input type="text"
																			 name="S[<?php echo $teacher_sn; ?>][years_of_service]"
																			 value="<?php echo $init_years_of_service; ?>" style="width:30px">
								</td>
								<td align="center" style="font-size:10pt">
									<select size="1" id="salary_kind_<?php echo $teacher_sn;?>" name="S[<?php echo $teacher_sn; ?>][salary_kind]" onchange="chang_salary_kind('salary_kind_<?php echo $teacher_sn;?>','salary_selection_<?php echo $teacher_sn;?>','spec_allowance_selection_<?php echo $teacher_sn;?>')">
										<?php
										foreach ($salary_kind_array as $k=>$k_salary) {
											?>
											<option value="<?php echo $k; ?>" <?php echo ($k == $salary_kind) ? "selected" : ""; ?>><?php echo $k_salary;?></option>
											<?php
										}
										?>
									</select>
								</td>
								<td align="center" style="font-size:10pt"><?php echo $base_salary; ?>/
									<select size="1" id="salary_selection_<?php echo $teacher_sn;?>" name="S[<?php echo $teacher_sn; ?>][base_salary]" onchange="chang_base_salary('salary_kind_<?php echo $teacher_sn;?>','salary_selection_<?php echo $teacher_sn;?>','spec_allowance_selection_<?php echo $teacher_sn;?>')">
										<?php
										foreach ($teacher_salary_array as $salary) {
											?>
											<option value="<?php echo $salary; ?>" <?php echo ($salary == $init_salary) ? "selected" : ""; ?>><?php echo $salary;?></option>
											<?php
										}
										?>
									</select>
								</td>
								<td align="center" style="font-size:10pt"><?php echo $spec_allowance; ?>/
									<select size="1" id="spec_allowance_selection_<?php echo $teacher_sn;?>" name="S[<?php echo $teacher_sn; ?>][spec_allowance]">
										<?php
										foreach ($teacher_spec_array as $spec) {
											?>
											<option value="<?php echo $spec; ?>" <?php echo ($spec == $init_spec_allowance) ? "selected" : ""; ?>><?php echo $spec;?></option>
											<?php
										}
										?>
									</select>
								</td>
								<td align="center" style="font-size:10pt"><?php echo $leader_allowance; ?>/
									<input type="text" name="S[<?php echo $teacher_sn; ?>][leader_allowance]" value="<?php echo $leader_allowance;?>" style="width:40px">
								</td>
								<td align="center" style="font-size:10pt"><?php echo $area_kind; ?>/
									<select size="1" name="S[<?php echo $teacher_sn; ?>][area_kind]">
										<?php
										foreach ($area_allowance_array as $area_k=>$v) {
											?>
												<option value="<?php echo $area_k;?>" <?php echo ($area_kind==$area_k)?"selected":"";?>><?php echo $area_k;?></option>
											<?php
										}
										?>
									</select>


								</td>
								<td align="center" style="font-size:10pt"><?php echo $area_years_of_service; ?>/<input type="text" name="S[<?php echo $teacher_sn; ?>][area_years_of_service]" value="<?php echo $init_area_years_of_service;?>" style="width:30px"></td>

								<td align="center" style="font-size:10pt"><input type="text" name="S[<?php echo $teacher_sn; ?>][memo]" value="<?php echo $init_memo;?>" style="width:100px"></td>
								<td><input type="button" class="deleteLink" id="<?php echo $teacher_sn;?>" value="�ư�" style="font-size:10pt;background-color: #FFCCCC">
								</td>
							</tr>
							<?php
						}
						?>
					</tbody>
				</table>
			</td>
		</tr>
	</table>
	<input type="button" value="�T�w�x�s" onclick="document.myform.act.value='add_years_data';document.myform.submit()">
</form>
	<?php

}
//�L�X���Y
foot();

//���o�w�]�ǳN��s�O(�M�~�[��)
function get_init_spec_allowance($salary_kind,$init_salary,$title_name,$teacher_spec_array,$edu_kind=3) {

	if ($salary_kind=='teacher') {
		if ($title_name=='�ժ�') {
			$init_spec_allowance=31320;
		} else {
			//�Юv
			foreach ($teacher_spec_array as $k=>$v) {
				if ($init_salary<=$k) {
					$init_spec_allowance=$v;
					break;
				}
				//$init_spec_allowance=31320;
			}
			//�j�ǳ̦h 26290
			if ($edu_kind>3 and $init_spec_allowance>26290) $init_spec_allowance=26290;
		}
	} else {
		//�w�]�M�~�[��
		if (substr($salary_kind,0,8)=='official') {
			$d=explode("-",$salary_kind);
			$init_spec_allowance=$teacher_spec_array[$d[1]];
		} else {
			$init_spec_allowance=$teacher_spec_array[1];
		}
	}

	return $init_spec_allowance;

}

?> 
<Script>
	$('#teacher_data > tbody > tr ').hover(function() {
		$(this).css("background","#E7EDC8");
	}, function() {
		$(this).css("background","");
	});

	$("#teacher_data .deleteLink").on("click",function() {
		var teacher_sn=$(this).attr('id');
		var tr = $(this).closest('tr');
		tr.fadeOut(400, function(){
			//�� teacher_sn �[�J�ư��W��
			$.ajax({
				type: "post",
				url: 'teach_appraisal.php',
				data: { act:'remove_teacher_sn',teacher_sn:teacher_sn },
				dataType: "text",
				error: function(xhr) {
					alert('ajax request �o�Ϳ��~!');
				},
				success: function(response) {
					tr.remove();
					if (response!='') { alert('�u'+response+'�v�w�C�J�ư��W��!'); }
				}
			});   // end $.ajax
		});
		return false;
	});

	$("#avoid_table .restore").on("click",function() {
		var sn=$(this).attr('id');
		var sn_array=sn.split("_");
		var teacher_sn=sn_array[1];

		var tr = $(this).closest('tr');
		tr.fadeOut(400, function(){
			//�� teacher_sn �[�J�ư��W��
			$.ajax({
				type: "post",
				url: 'teach_appraisal.php',
				data: { act:'restore_teacher_sn',teacher_sn:teacher_sn },
				dataType: "text",
				error: function(xhr) {
					alert('ajax request �o�Ϳ��~!');
				},
				success: function(response) {
					tr.remove();
					if (response!='') { alert('�u'+response+'�v�w��_�C�J�Ү֦W��A�z�������s���J����!'); }
				}
			});   // end $.ajax
		});
		return false;
	});

	$("#teacher_data .show_detail").on("click",function() {
		var sn=$(this).attr('id');    //btn_detail_xxx
		var sn_array=sn.split("_");
		var teacher_sn=sn_array[2];
		var show_detail='detail_'+teacher_sn;
		var tr_detail='tr_'+teacher_sn;
		var tr = $(this).closest('tr');
			//�� teacher_sn �[�J�ư��W��
			$.ajax({
				type: "post",
				url: 'teach_appraisal.php',
				data: { act:'detail_teacher_sn',teacher_sn:teacher_sn },
				dataType: "text",
				error: function(xhr) {
					alert('ajax request �o�Ϳ��~!');
				},
				success: function(response) {
					$("#"+show_detail).html(response);
					$("#"+tr_detail).css('display', 'table-row');
				}
			});   // end $.ajax

		return false;
	});

	function hide_detail(teacher_sn) {
		var tr_detail='tr_'+teacher_sn;
		var show_detail='detail_'+teacher_sn;
		$("#"+show_detail).html("");
		$("#"+tr_detail).css('display', 'none');
	}

	$("#teacher_data .add_one").on("click",function() {
		var sn=$(this).attr('id');    //btn_detail_xxx
		var sn_array=sn.split("_");
		var teacher_sn=sn_array[2];
		var show_detail='detail_'+teacher_sn;
		var tr_detail='tr_'+teacher_sn;
		//�� teacher_sn �[�J�ư��W��
		$.ajax({
			type: "post",
			url: 'teach_appraisal.php',
			data: { act:'add_one_form',teacher_sn:teacher_sn },
			dataType: "text",
			error: function(xhr) {
				alert('ajax request �o�Ϳ��~!');
			},
			success: function(response) {
				$("#"+show_detail).html(response);
				$("#"+tr_detail).css('display', 'table-row');
			}
		});   // end $.ajax

		return false;

	});

	//�����~�B���O�� �n�^���~�B�}�C�αM�~�[���}�C
	function chang_salary_kind(salary_kind_id,salary_selection_id,spec_selection_id) {
		var salary_kind_selected=document.getElementById(salary_kind_id).value;
		var myselect_salary=document.getElementById(salary_selection_id);
		var myselect_spec=document.getElementById(spec_selection_id);
		//alert (salary_kind_id + ',' +salary_selection_id);
		//�����Ҧ� salary_option
		var L=myselect_salary.length;
		//�n�Ѥj�Ӥp����
		for (i=L-1;i>-1;i--) {
			myselect_salary.remove(i);
		}
		//�����Ҧ� spec_option
		var L=myselect_spec.length;
		//�n�Ѥj�Ӥp����
		for (i=L-1;i>-1;i--) {
			myselect_spec.remove(i);
		}

		//���o���O�������~�B�ﶵ
		$.ajax({
			type: "post",
			url: 'teach_appraisal.php',
			data: { act:'get_salary_array',salary_kind:salary_kind_selected },
			dataType: "text",
			error: function(xhr) {
				alert('ajax request �o�Ϳ��~!');
			},
			success: function(response) {
				var res_data = JSON.parse(response);  //��ǤJ������ର json �榡�A���R
				$.each( res_data.salary_array, function( k, v) {
					myselect_salary.add(new Option(v,v));
				});
				$.each( res_data.spec_array, function( k, v) {
					myselect_spec.add(new Option(v,v));
				});

				if (res_data.init_spec_allowance>0) {
					for (var i = 0, j = myselect_spec.options.length; i < j; ++i) {
						if (myselect_spec.options[i].value == res_data.init_spec_allowance) {
							myselect_spec.selectedIndex = i;
							break;
						}
					}
				}
			}
		});   // end $.ajax

		return false;

	}

	//�����F���B
	function chang_base_salary(salary_kind_id,salary_selection_id,spec_selection_id) {
		//���O
		var salary_kind_selected=document.getElementById(salary_kind_id).value;

		//alert(salary_kind_selected);
		var w=salary_kind_id.split("_");
		var teacher_sn=w[2];


		//�~�B
		var salary_selected=document.getElementById(salary_selection_id).value;

		//���o�M�~�[�����
		var myselect_spec=document.getElementById(spec_selection_id);

		//���o���O�������~�B�ﶵ
		$.ajax({
			type: "post",
			url: 'teach_appraisal.php',
			data: { act:'get_init_spec_allowance',salary_kind:salary_kind_selected,base_salary:salary_selected,teacher_sn:teacher_sn },
			dataType: "text",
			error: function(xhr) {
				alert('ajax request �o�Ϳ��~!');
			},
			success: function(response) {
				var res_data = JSON.parse(response);  //��ǤJ������ର json �榡�A���R
				//alert(res_data);
				//var L=myselect_spec.op
				for (var i = 0, j = myselect_spec.options.length; i < j; ++i) {
					if (myselect_spec.options[i].value == res_data) {
						myselect_spec.selectedIndex = i;
						break;
					}

				}

			}
		});   // end $.ajax

		return false;

	}



	function one_delete (btn,sn) {

		var confirm_delete=confirm("�z�T�w�n�R��?");

		if (confirm_delete) {
			//alert("�z��F�O"+sn);

			var row = btn.parentNode.parentNode;
			row.parentNode.removeChild(row);

			//�� teacher_sn �[�J�ư��W��
				$.ajax({
					type: "post",
					url: 'teach_appraisal.php',
					data: { act:'delete_one',sn:sn },
					dataType: "text",
					error: function(xhr) {
						alert('ajax request �o�Ϳ��~!');
					},
					success: function(response) {
						if (response!='') {
							alert(response);
						}
					}
				});   // end $.ajax

			return false;
		} else {
			return false;
		}

	}

	function list_teach_edu_kind() {
		if($('#check_teach_edu_kind').prop('checked')) {
			$(".teach_edu_kind").css("display","table-cell");
		} else {
			$(".teach_edu_kind").css("display","none");
		}
	}
</Script>