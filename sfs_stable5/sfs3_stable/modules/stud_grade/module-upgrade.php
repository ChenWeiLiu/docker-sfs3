<?php
// $Id: module-upgrade.php 6204 2010-09-30 23:31:15Z infodaes $

if(!$CONN){
	echo "go away !!";
	exit;
}
set_time_limit(0);
// �ˬd��s�_
// ��s�O���ɸ��|
$upgrade_path = "upgrade/".get_store_path();

$upgrade_str = set_upload_path("$upgrade_path");

$up_file_name =$upgrade_str."2011-5-6.txt";
$isOk = false;
if (!is_file($up_file_name) or $_reUpgrade){
	if (isset($_POST['doit'])) {
		$query = "ALTER TABLE `grad_stud` ADD `student_sn` INT NOT NULL , ADD INDEX ( `student_sn` ) ";
		if ($CONN->Execute($query))
		$temp_str = "$query\n ��s���\ ! \n";
		else
		$temp_str = "$query\n ��s���� ! \n";


		$query = "select student_sn,stud_id from stud_base ";
		$res = $CONN->Execute($query);

		$year = 100;  // 100�Ǧ~�e�����
		while(!$res->EOF){
			$query = "update grad_stud  set  student_sn=".$res->fields[0]." where stud_id='".$res->fields[1]."' and stud_grad_year <= ".$year;
			$CONN->Execute($query) or trigger_error("SQL �y�k���~<BR>$query", E_USER_ERROR);
			$res->MoveNext();
		}


		//�ץ��Ǹ�10���Ъ����D, ���� grad_stud �o�� table �̪��Ҧ� student_sn �O�_���T (2017.02.09 by smallduh)
		//�ӥͪ����~�~�����p��J�Ǧ~
		$sql="SELECT a.*,b.stud_study_year FROM `grad_stud` a,`stud_base` b WHERE a.student_sn=b.student_sn and a.stud_grad_year<b.stud_study_year";
		$res=$CONN->Execute($sql) or trigger_error("SQL �y�k���~<BR>$sql", E_USER_ERROR);
		$err_sn=$res->recordCount();
		while ($row=$res->fetchRow()) {
			$grad_sn=$row['grad_sn'];     //���~�͸�Ƭy����
			//���o�� stud_id ���T�� student_sn
			$search="select student_sn from stud_base where stud_study_year<'{$row['stud_grad_year']}' and stud_id='{$row['stud_id']}'";
			list($student_sn)=$CONN->Execute($search)->fetchRow();
			//�g�J
			$CONN->Execute("update grad_stud set student_sn='{$student_sn}' where grad_sn='$grad_sn'");
		}

		$temp_query = "�s�W student_sn ��� -- by hami (2011-05-06)\n\n$temp_str";
		$fp = fopen ($up_file_name, "w");
		fwrite($fp,$temp_query);
		fclose ($fp);
		?>
	<script>
	alert('�w�ɯŧ���! �íץ� <?= $err_sn ?> �Ӧ����D�� student_sn !');
	</script>
		<?php 
	}
	else {
		head();
	?>
	<div style="margin:20px; padding:10px">
	<h2>�t�ηs�W�F���, �ݤ@�Ǯɶ��~�৹���ɯŰʧ@,���ɯŧ�����, �����}����</h2>
		<form method="post" action="" id="upgradForm">
		
		<input type="submit" name="doit" value="�ڤF��, �}�l�ɯ�" />
		</form>	
	</div>
	<?php 
		foot();
		exit;
	}
}

//�ץ��Ǹ�10���Ъ����D, ���� grad_stud �o�� table �̪��Ҧ� student_sn �O�_���T (2017.02.09 by smallduh)
//�ӥͪ����~�~�����p��J�Ǧ~
$up_file_name =$upgrade_str."2017-02-09.txt";
if (!is_file($up_file_name)) {
	$sql="SELECT a.*,b.stud_study_year FROM `grad_stud` a,`stud_base` b WHERE a.student_sn=b.student_sn and a.stud_grad_year<b.stud_study_year";
	$res=$CONN->Execute($sql) or trigger_error("SQL �y�k���~<BR>$sql", E_USER_ERROR);
	$err_sn=$res->recordCount();
	while ($row=$res->fetchRow()) {
		$grad_sn=$row['grad_sn'];     //���~�͸�Ƭy����
		//���o�� stud_id ���T�� student_sn
		$search="select student_sn from stud_base where stud_study_year<'{$row['stud_grad_year']}' and stud_id='{$row['stud_id']}'";
		list($student_sn)=$CONN->Execute($search)->fetchRow();
		//�g�J
		$CONN->Execute("update grad_stud set student_sn='{$student_sn}' where grad_sn='$grad_sn'");
	}
}