<?php
//$Id: chc_seme.php 5310 2009-01-10 07:57:56Z hami $

//�w�]���ޤJ�ɡA���i�����C
require_once "./module-cfg.php";
include_once "../../include/config.php";

//Ū�� LDAP �]�w
$query="select * from ldap limit 1";
$res=$CONN->Execute($query); // or die('Error! SQL='.$query);
if (!$res) {
	$LDAP['enable']=$LDAP['enable1']=0;
} else {
	$LDAP=$res->fetchrow();
}

//Ū���ǥͱK�X�޲z�Ҳճ]�w
$stud_pass=&get_module_setup("stud_pass");
//if ($stud_pass['quick_chpass']==0 or $LDAP['enable1']==1) {
if ($stud_pass['quick_chpass']==0) {
	echo "�t�Τ����\�b���ܧ�K�X!";
	exit();
}

//�ثe��w�~�šA100�������w
$c_curr_class=$_POST['c_curr_class'];
//���o�ثe�Ǧ~��
$curr_year=curr_year();
$curr_seme=curr_seme();
$c_curr_seme=$curr_year.$curr_seme;
$s_y = substr($c_curr_seme,0,3);
$s_s = substr($c_curr_seme,-1);
$tmp=&get_class_select($s_y,$s_s,"","c_curr_class","this.form.submit",$c_curr_class);

//���X�~�ŻP�Z�ſ��
if ($c_curr_class!='') {
	$class=sprintf('%d%02d',substr($c_curr_class,6,2),substr($c_curr_class,9,2));
	//���o�ǥͦW��
	$query="select a.stud_name,b.seme_num,b.student_sn from stud_base a,stud_seme b where a.student_sn=b.student_sn and b.seme_class='$class' and b.seme_year_seme='$c_curr_seme' and a.stud_study_cond=0 order by seme_num";
	$res=$CONN->Execute($query) or user_error("Ū�����~!",256);
	$select_option="";
	while ($row=$res->fetchrow()) {
		$name=mb_substr($row['stud_name'],0,1,"BIG-5")."��".mb_substr($row['stud_name'],-2,null,"BIG-5");
		$select_option.="<option value=\"{$row['student_sn']}\">{$row['seme_num']}.{$name}</option>";
	}


}

//�q�X�����������Y
head("�ǥͱK�X�ܧ�");
if (isset($_POST['student_sn']) and isset($_POST['old_log_pass']) and isset($_POST['new_log_pass1']) and isset($_POST['new_log_pass2']) and $_POST['new_log_pass1']=$_POST['new_log_pass2']) {

	$res = change_stu_pass($_POST['student_sn'], $_POST['new_log_pass1'], $_POST['old_log_pass']);

	head ("�ǥͱK�X�ܧ�");
	if ($res>0) {
		echo "<div style='margin:auto;width:100%;text-align: center'><span style='color:#0000FF;font-size:20pt'>�K�X�ܧ󦨥\!!</span></div>";
	} else {
		echo "<div style='margin:auto;width:100%;text-align: center'><span style='color:#FF0000;font-size:20pt'>�K�X�ܧ󥢱�!!</span></div>";
	}

	foot();

	exit();
}


?>
	<form action='<?php echo $_SERVER['PHP_SELF']?>' method='post' name="myform">
		<table style='width:100%;'>
			<tr><td style='text-align:center;padding:15px;'>
					<div  class='ui-widget-header ui-corner-top'  style='width:350px; padding:5px; margin:auto'>
						<span style='text-align:center;'>�ǥͱK�X�ܧ�</span>
					</div>
					<div  class='ui-widget-content ui-corner-bottom'  style='width:350px; padding:5px; margin:auto'>
						<table cellspacing='0' cellpadding='3' align='center'>
							<tr class='small'>
								<td nowrap>��ܯZ��</td><td nowrap>
									<?php echo $tmp;?>
								</td>
							</tr>
							<tr class='small'>
								<td nowrap>��ܾǥ�</td>
								<td nowrap>
									<select size="1" name="student_sn" id="sutdent_sn">
										<option>�п��...</option>
										<?php echo $select_option; ?>
									</select>
								</td>
							</tr>
							<tr class='small'>
								<td nowrap>��J�±K�X</td>
								<td>
									<input type='password' id="old_log_pass" name='old_log_pass' size='20' maxlength='24'>
								</td>
							</tr>
							<tr class='small'>
								<td nowrap>��J�s�K�X�Ĥ@��</td>
								<td>
									<input type='password' id="new_log_pass1" name='new_log_pass1' size='20' maxlength='24'>
								</td>
							</tr>
							<tr class='small'>
								<td nowrap>��J�s�K�X�ĤG��</td>
								<td>
									<input type='password' id="new_log_pass2" name='new_log_pass2' size='20' maxlength='24'>
								</td>
							</tr>
							<tr class='small'>
								<td nowrap></td>
								<td><input type="button" id="confirm_change" value="�T�w�n�ܧ�">
								</td>
							</tr>
						</table>

					</div>
				</td>
			</tr>
			<tr>
				<td align="center">
					<table cellspacing='0' cellpadding='3' style="margin-top:20px">
						<tr>
							<td>1. �K�X�ܤ� 4 �Ӧr��. </td>
						</tr>
						<tr>
							<td>2. �K�X�̦n�V�X�Ʀr�B�^��r���M�Ÿ��A�W�[�Q�O�H�q��������. </td>
						</tr>
						<tr>
							<td>3. ���n�ϥήa�̹q�ܡB�����Ҧr��...���e���Q�q�����Ʀr��K�X. </td>
						</tr>

					</table>
				</td>
			</tr>
		</table>
	</form>


<?php
foot();
?>
<Script>
	$("#confirm_change").click(function(){
		var student_sn=$("#student_sn").val();
		var old_log_pass=$("#old_log_pass").val();
		var new_log_pass1=$("#new_log_pass1").val();
		var new_log_pass2=$("#new_log_pass2").val();

		if (student_sn=='') {
			alert("�A������ܧA���m�W!");
			return false;
		}

		if (old_log_pass=='') {
			alert("�A������J�A�ثe�ϥΪ��K�X!");
			$("#old_log_pass").focus();
			return false;
		}

		if (new_log_pass1=='') {
			alert("�A������J�A�n�ܧ󪺱K�X!");
			$("#new_log_pass1").focus();
			return false;
		}

		if (new_log_pass2=='') {
			alert("�n�ܧ󪺱K�X�������⦸�@! �ӥB�⦸���n�@��!");
			$("#new_log_pass2").focus();
			return false;
		}

		if (new_log_pass1!=new_log_pass2) {
			alert("�A��J�⦸�n�ܧ󪺱K�X���@�P! \n�Э��s��J!");
			$("#new_log_pass1").val("");
			$("#new_log_pass2").val("");
			$("#new_log_pass1").focus();
			return false;
		}

		if (new_log_pass1.length<4) {
			alert("�K�X�ӵu!");
			$("#new_log_pass1").val("");
			$("#new_log_pass2").val("");
			$("#new_log_pass1").focus();
			return false;
		}

		document.myform.submit();

	});
</Script>
