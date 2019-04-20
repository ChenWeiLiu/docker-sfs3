<?php
// $Id: reward_one.php 6735 2012-04-06 08:14:54Z smallduh $
//���o�]�w��
include_once "config.php";

sfs_check();
//��w���ǥ�
$student_sn=$_POST['student_sn'];
//���o�ثe�Ǧ~��
$curr_year=curr_year();
$curr_seme=curr_seme();
$year_seme=sprintf("%03d%1d",$curr_year,$curr_seme);

//ajax ���ͪ�� �s�W
if ($_POST['act']=='insert_form') {
	header('Content-type: text/html;charset=big5');
	$stud=get_stud_base($student_sn);
	$stud_data['stud_name']=$stud['stud_name'];
	$stud_data['student_sn']=$stud['student_sn'];
	$stud_data['score_source']=$stud['exp_group_name'];
	$make_form=make_form($stud_data,"�s�W�@���O��");
	echo $make_form;
	exit();
}

//�R��
if ($_POST['act']=='del_row') {
	//���o�ǥͰ򥻸��
	$stud = get_stud_base($student_sn);
	$sn=$_POST['sn'];
	$sql="select * from score_experiment where sn='$sn'";
	$res=$CONN->Execute($sql) or trigger_error($sql,256);
	$old_file=$res->fields['append_file'];
	if ($old_file!='') {
		unlink($UPLOAD_PATH . 'score_experiment/' . $old_file);
	}
	$sql="delete from score_experiment where sn='$sn'";
	$res=$CONN->Execute($sql) or trigger_error($sql,256);
	echo $sn;
	exit();
}

//ajax ���ͪ�� �s�W
if ($_POST['act']=='edit_row') {
	header('Content-type: text/html;charset=big5');
	$sn=$_POST['sn'];
	$sql="select * from score_experiment where sn='$sn'";
	$res=$CONN->Execute($sql) or trigger_error($sql,256);
	$stud_data=$res->fetchRow();
	$stud=get_stud_base($stud_data['student_sn']);
	$stud_data['stud_name']=$stud['stud_name'];
	$make_form=make_form($stud_data,"�s�פ@���O��");
	echo $make_form;
	exit();
}


//�x�s
if ($_POST['act']=='save') {
	//���o�ǥͰ򥻸��
	$stud=get_stud_base($student_sn);
	//���o��椺�e
	foreach ($_POST as $k=>$v) {
		${$k}=$v;
	}
	//�B�z�ɮפW�� , ���ˬd���S���W���ɮ�(�s�W�@�w�n�W��, �ק藍�ݭn)
		$targetFile="";
		if (isset($_FILES['append_file']['tmp_name'])) {
			$tempFile = $_FILES['append_file']['tmp_name'];
			//$targetPath = $_SERVER['DOCUMENT_ROOT'] . $targetFolder; // �]�w�n�W���ɮת���Ƨ�������|
			$targetPath = $UPLOAD_PATH . 'score_experiment/';
			// Validate the file type
			$fileTypes = array('pdf'); // �i�H�����ɮװ��ɦW
			//���o�ɦW��T
			$fileParts = pathinfo($_FILES['append_file']['name']);
			if (in_array($fileParts['extension'], $fileTypes)) {
				//�ഫ���s�X�L���ɦW
				//$targetFile = rtrim($targetPath,'/') . '/' . $_FILES['append_file']['name'];
				//�ɦW�� $stud_person_id+hash('sha256', $student_sn)+$fileParts['extension'];
				$targetFile = $stud['stud_person_id'] . hash('sha256', $stud['student_sn']).time(). "." . $fileParts['extension'];
				move_uploaded_file($tempFile, $targetPath.$targetFile);
			}
		}
	if ($sn=='') {
		$sql="insert score_experiment (year_seme,student_sn,score,score_level,hard_level,score_memo,append_memo,append_file,score_source,create_time) value ('$year_seme','$student_sn','$score','$score_level','$hard_level','$score_memo','$append_memo','$targetFile','$score_source','$create_time')";
	} else {
		//�Y�������W���ɮ�, �n���R������
		$sql="select append_file from score_experiment where sn='$sn'";
		$res=$CONN->Execute($sql) or trigger_error($sql,256);
		$old_file=$res->fields['append_file'];
		if ($targetFile!='') {
			unlink($UPLOAD_PATH . 'score_experiment/'.$old_file);
		} else {
			$targetFile=$old_file;
		}
		$sql="update score_experiment set score='$score',score_level='$score_level',hard_level='$hard_level',score_memo='$score_memo',append_memo='$append_memo',append_file='$targetFile',score_source='$score_source' where sn='$sn'";
	}

	$CONN->execute($sql) or trigger_error($sql,256);
}

//�͵��C�L
if ($_GET['act']=='print') {
	if ($_GET['student_sn']!='') {
		//���o���ͪ��Ҧ��O��
		$sql="select * from score_experiment where student_sn='".$_GET['student_sn']."'";
		$res=$CONN->Execute($sql) or trigger_error($sql,256);
		?>
		<table border="0" width="100%">
			<tr>
				<td align="center"><?php echo $print_header;?></td>
			</tr>
		</table>
		<table border="1" style="width:100%;border-collapse: collapse;border-style:solid;border-width: thin;border-color: #000000">
			<tr style="background-color: #d4d4d4">
				<td align="center" style="padding:5px">�Ǧ~</td>
				<td align="center" style="padding:5px">�Ǵ�</td>
				<td align="center" style="padding:5px">�ʤ���</td>
				<td align="center" style="padding:5px">����</td>
				<td align="center" style="padding:5px">�V�O�{��</td>
				<td align="center" style="padding:5px">��r�y�z</td>
				<td align="center" style="padding:5px">�Ƶ��Ϊ��O����</td>
				<td align="center" style="padding:5px">���Z�ӷ�</td>
			</tr>
			<?php
			while ($row=$res->fetchRow()) {
				?>
				<tr id="tr_<?php echo $row['sn'];?>">
					<td align="center"><?php echo substr($row['year_seme'],0,3);?></td>
					<td align="center"><?php echo substr($row['year_seme'],-1);?></td>
					<td align="center"><?php echo $row['score']?></td>
					<td align="center"><?php echo $row['score_level']?></td>
					<td align="center"><?php echo $row['hard_level']?></td>
					<td><?php echo $row['score_memo']?></td>
					<td><?php echo nl2br($row['append_memo'])?></td>
					<td><?php echo $row['score_source']?></td>
				</tr>
				<?php
			}
			?>
		</table>
		<table border="0">
			<tr>
				<td><?php echo $print_footer;?></td>
			</tr>
		</table>
		<?php
	}
	exit();
}


//�q�X����
head("����Ш|���Z�n��");

$tool_bar=make_menu($school_menu_p);

//�C�X���
echo $tool_bar;



//���o�Ҧ�����Ш|�ǥ�
$sql="select * from stud_base where stud_study_cond in (0,15) and experiment_kind>0 order by curr_class_num";
$res=$CONN->Execute($sql) or trigger_error($sql,256);

$stud_option="<option value=''>�п�ܾǥ�</option>";

while ($row=$res->fetchrow()) {
	$stud_option.="<option value='".$row['student_sn']."'".(($row['student_sn']==$student_sn)?" selected":"").">".substr($row['curr_class_num'],0,3)."-".substr($row['curr_class_num'],-2)."-".$row['stud_name'].(($row['exp_group_name']!='')?" (".$row['exp_group_name'].")":"")."</option>";
}

?>
<style>
	.div_blocker {
	width: 800px;
	height: 500px;
	position: absolute;
	z-index: 2;
	top: 50%;
	left: 50%;
	margin: -250px 0 0 -450px;
	overflow:auto;
	border-width:3px;border-style:dashed;border-color: #dedede;padding:5px;
	}

	#mask {
		position: fixed;
		top: 0;
		right:0;
		bottom:0;
		left: 0;
		z-index: 1;
		opacity:0.5;
		-moz-opacity:0.5;
		display: none;
		background-color: #000;
	}


</style>
<form name="myform" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
	<div style="margin-top: 10px">
		��w�ǥ�
		<select size="1" name="student_sn" onchange="this.form.submit()">
			<?php echo $stud_option;?>
		</select>
	</div>
	<?php
	if ($student_sn!='') {
		//���o���ͪ��Ҧ��O��
		$sql="select * from score_experiment where student_sn='$student_sn'";
		$res=$CONN->Execute($sql) or trigger_error($sql,256);

	?>
	<div style="margin-top: 10px">
		<div style="margin-bottom: 10px">
		<span style="float:right;">
			<?php
			if ($res->recordCount()) {
				?>
				<input type="button" class="btn_print" id="<?php echo $student_sn;?>" value="�͵��C�L">
			<?php
			}
			?>
			<input type="button" class="btn_insert" id="<?php echo $student_sn;?>" value="�s�W�@��">

		</span>
		</div>
		<table class="small table-sfs3" style="width:100%">
			<tr style="background-color: #0d3349;color:#FFFFFF">
				<td align="center" style="padding:5px">�Ǧ~</td>
				<td align="center" style="padding:5px">�Ǵ�</td>
				<td align="center" style="padding:5px">�ʤ���</td>
				<td align="center" style="padding:5px">����</td>
				<td align="center" style="padding:5px">�V�O�{��</td>
				<td align="center" style="padding:5px">��r�y�z</td>
				<td align="center" style="padding:5px">�Ƶ��Ϊ��O����</td>
				<td align="center" style="padding:5px">���Z�ӷ�</td>
				<td align="center" style="padding:5px">�����ɮ�</td>
				<td align="center" style="padding:5px">�ާ@</td>
			</tr>
			<?php
			while ($row=$res->fetchRow()) {
				?>
				<tr id="tr_<?php echo $row['sn'];?>">
					<td align="center"><?php echo substr($row['year_seme'],0,3);?></td>
					<td align="center"><?php echo substr($row['year_seme'],-1);?></td>
					<td align="center"><?php echo $row['score']?></td>
					<td align="center"><?php echo $row['score_level']?></td>
					<td align="center"><?php echo $row['hard_level']?></td>
					<td><?php echo $row['score_memo']?></td>
					<td><?php echo nl2br($row['append_memo'])?></td>
					<td><?php echo $row['score_source']?></td>
					<td align="center">
						<?php
						if ($row['append_file']!='') {
							?><input type="button" id="<?php echo $row['append_file']?>" class="view_append_file" value="�s��">
							<?php
						}
						?>
					</td>
					<td align="center">
						<?php
						if ($year_seme==$row['year_seme']) {
							?>
								<img src="./images/pen.png" style="cursor: pointer" id="<?php echo $row['sn']?>" class="edit_row" title="�s��">
								<img src="./images/del.png" style="cursor: pointer" id="<?php echo $row['sn']?>" class="del_row" title="�R��">
							<?php
						}
						?>
					</td>
				</tr>
				<?php
			}
			?>
		</table>
	</div>
	<?php
	}
	?>
</form>
<!--�z���B�n?-->
<div id="mask"></div>

<div id="blocker_div" class="div_blocker" style="display:none;background-color: rgb(250, 248, 225)">
	<div>
		<span style="float:right">
			<button class="btn-closed" style="background-color: #dadada;color:#a0a0a0">closed</button>
		</span>
	</div>
	<div id="blocker_display" style="margin:25px">

	</div>
</div>
<?php
foot();
?>
<Script>
	$(".btn-closed").click(function(){
		$("#blocker_div").css("display","none");
		$("#mask").css("display","none");
	});

	//�s������
	$(".view_append_file").click(function(){
		//�ɦW
		var file_name=$(this).attr("id");
		var file_url="<?php echo $UPLOAD_URL . 'score_experiment/';?>"+file_name;
		var dis="<object data=\""+file_url+"\" type=\"application/pdf\" width=\"100%\" height=\"500px\"><embed src=\""+file_url+"\"><p>�z���s�������䴩�����[�� PDF ��. �ФU����A�ϥΨ�L�{�����}�C <a href=\""+file_url+"\">Download PDF</a>.</p></embed></object>";
		$("#blocker_display").html(dis);
		$("#mask").css("display","block");
		$("#blocker_div").width("90%");
		$("#blocker_div").css("height","600");
		$("#blocker_div").css("margin","-300px 0 0 -45%");
		$("#blocker_div").fadeIn(300);
	});

	//�R���@��
	$(".del_row").click(function() {
		var sn = $(this).attr("id");
		var confirm_delete=confirm("�z�T�w�n�R���o�����?");

		if (confirm_delete) {
			$.ajax({
				type: "post",
				url: 'exp_input.php',
				data: { act:'del_row',sn:sn },
				dataType: "text",
				error: function(xhr) {
					alert('ajax request �o�Ϳ��~!');
				},
				success: function(response) {
					//��ӦC����
					$("#tr_"+response).remove();
					alert("��Ƭy����"+sn+ ", �w�R��!");
				}
			});   // end $.ajax

		} else {
			return false;
		}

	});

	//�s��@��
	$(".edit_row").click(function() {
		var sn = $(this).attr("id");

			$.ajax({
				type: "post",
				url: 'exp_input.php',
				data: { act:'edit_row',sn:sn },
				dataType: "text",
				error: function(xhr) {
					alert('ajax request �o�Ϳ��~!');
				},
				success: function(response) {
					$("#blocker_display").html(response);
					$("#mask").css("display","block");
					$("#blocker_div").width("800px");
					$("#blocker_div").height("500px");
					$("#blocker_div").css("margin","-250px 0 0 -400px");
					$("#blocker_div").fadeIn(300);
				}
			});   // end $.ajax


	});
	//�s�W�@��
	$(".btn_insert").click(function(){

		//var show=$(this);
		var student_sn=$(this).attr("id");

		$.ajax({
			type: "post",
			url: 'exp_input.php',
			data: { act:'insert_form',student_sn:student_sn },
			dataType: "text",
			error: function(xhr) {
				alert('ajax request �o�Ϳ��~!');
			},
			success: function(response) {
				//alert('ajax request ���\!');
				$("#blocker_display").html(response);
				$("#blocker_div").width("800px");
				$("#blocker_div").height("500px");
				$("#blocker_div").css("margin","-250px 0 0 -400px");
				$("#mask").css("display","block");
				$("#blocker_div").fadeIn(300);
			}
		});   // end $.ajax
	});

	//�s�W�@��
	$(".btn_print").click(function() {
		//var show=$(this);
		var student_sn = $(this).attr("id");

		window.open("exp_input.php?act=print&student_sn="+student_sn);

		return false;

	});
	//�x�s�e�ˬd
	function check_before_save() {

		var sn=document.form_block.sn.value;
		var append_file=document.form_block.append_file.value;

		if (sn=='' && append_file=='') {
			alert("�ܤ֭n���Ѫ���!");
			return false;
		}

		return true;

	}

	function unset_ower(thetext) {
		if(thetext.value>100){ thetext.style.background = '#FF0000'; alert("��J���Z����100��");}
		else if(thetext.value<0){ thetext.style.background = '#AA5555'; alert("��J���Z���t��"); }
		else { thetext.style.background = '#FFFFFF'; }
		return true;
	}
</Script>
