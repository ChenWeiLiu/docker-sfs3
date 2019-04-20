<?php

// $Id: read_card.php 7958 2014-03-26 01:36:13Z infodaes $

// --�t�γ]�w��
include "config.php";

//--�{�� session
sfs_check();

//���o�ثe�Ǧ~
$curr_year = curr_year();

//���o�ثe�Ǵ�
$curr_seme = curr_seme();

//�Ǧ~�Ǵ�
$seme_year_seme = sprintf("%03d", curr_year()).curr_seme();

$sel_file=basename($_POST['sel_file']);
$del_file=$_POST['del_file'];
$des_subject=$_POST['des_subject'];
$subject=$_POST['subject'];
$tkind=intval($_POST['tkind']);
$stage=intval($_POST['stage']);
$trans=$_POST['trans'];

//�L�X���Y
head("�פJŪ�d���Z");
print_menu($MENU_P);
$temp_score_arr=array("temp_score1"=>"�s�ʹ����ؤ@","temp_score2"=>"�s�ʹ����ؤG","temp_score3"=>"�s�ʹ����ؤT");

//�R���ɮ�
if ($del_file && $sel_file) {
	unlink($temp_path.$sel_file);
	$sel_file="";
}

//��X�S��������
$query="select * from test_manage where 1=0";
$res=$CONN->Execute($query);
if ($res) {
	$query="select * from test_manage where year='".curr_year()."' and semester='".curr_seme()."' order by id";
	$res=$CONN->Execute($query);
	while (!$res->EOF) {
		$id=$res->fields[year]."_".$res->fields[semester]."_".$res->fields[id];
		$spec_test_arr[$id]=$res->fields[title];
		$res->MoveNext();
	}
}

//�ɮפW��
$file_name=strtoupper($_FILES['upload_file']['name']);
$lastname=substr($file_name,(strpos($file_name,".")+1),3);
$s_str="/";
if (substr(strtoupper($_ENV['OS']),0,3)=="WIN") {
	$ff_arr=explode("\\",$_FILES['upload_file']['tmp_name']);
	$ff_str=$ff_arr[0];
	for($i=1;$i<(count($ff_arr)-1);$i++) $ff_str.="\\".$ff_arr[$i];
	if (strtoupper($ff_str)==strtoupper($tmp_path)) $tmp_path=$ff_str;
	$s_str="\\";
}
if (is_uploaded_file($_FILES['upload_file']['tmp_name']) && !$_FILES['upload_file']['error'] && $_FILES['upload_file']['size'] >0 && $file_name != "" &&  ($lastname == "CSV" || $lastname == "csv")){
	move_uploaded_file($_FILES['upload_file']['tmp_name'],$temp_path.$_FILES['upload_file']['name']);
}
$today=date("Y-m-d G:i:s",mktime (date("G"),date("i"),date("s"),date("m"),date("d"),date("Y")));

if (!$trans || ($trans && (!$des_subject || !($stage||$spec_test_arr[$des_subject])))) {
	//�ɮ׿��
	$temp4="<select name='sel_file' onChange='this.form.submit();'>
	<option value=''>�п���ɮ�";
	$fp = opendir($temp_path);
	while ( gettype($file=readdir($fp)) != boolean ){
		$temp5=($sel_file==$file)?"selected":"";
		if (is_file("$temp_path/$file")){
			$temp4.="<option value='$file' $temp5>$file";
		}
	}
	closedir($fp);
	$temp4.="</select>";

	if ($sel_file) $del_str="<input type='submit' name='del_file' value='�R���ɮ�'>";
	echo "	<table cellspacing='1' cellpadding='3' class='main_body'>
		<tr bgcolor='#FFFFFF'>
		<form name='form0' enctype='multipart/form-data' action='{$_SERVER['SCRIPT_NAME']}' method='post'>
		<td class='title_sbody1' nowrap>�W���ɮסG<td><input type=file name='upload_file'></td>
		<td class='title_sbody1' nowrap><input type=submit name='doup_key' value='�W��'></td>
		</form>
		</tr>
		<tr bgcolor='#FFFFFF'>
		<form name='form1' action='{$_SERVER['SCRIPT_NAME']}' method='post'>
		<td class='title_sbody1' nowrap>���A�����s�ɮסG<td colspan=2>$temp4 $del_str</td>
		</form>
		</tr>";
	if (!$sel_file) {
		//����
		$help_text="
			�ɮפ��u������椺�e�P�ثe��ܬۦP�����Z�|�Q�פJ�C||
			�z�i�H�U��<a href=read_card_demo.csv>��f�d����</a>�B<a href=read_card_demo_2.csv>�ȩ��d����</a>�B<a href=read_card_demo_3.csv>�W��d����</a>�B<a href=read_card_demo_4.csv>�j���d����</a>�B<a href=read_card_demo_5.csv>�ʮE�d����</a>�C||
			�d���ɤ������Ӧ��Ȫ����O���n�����A��L�����O�d�ťթΦ���Ƴ��L���C||
			<a href=stud_base.php>�U����f�ǥͰ򥻸����</a>�B<a href=stud_base2.php>�U���ʮE�ǥͰ򥻸����</a>";
		$help=help($help_text);
	}
} elseif ($trans && $tkind && $des_subject && ($stage||$spec_test_arr[$des_subject])) {
	$stud=array();
	$sql="select curr_class_num,student_sn from stud_base where stud_study_cond='0' order by curr_class_num";
	$rs=$CONN->Execute($sql);
	while (!$rs->EOF) {
		$stud[$rs->fields[curr_class_num]]=$rs->fields[student_sn];
		$rs->MoveNext();
	}
	if ($spec_test_arr[$des_subject]=="") {
		if ($tkind=="1") {
			$score_semester="nor_score_".$curr_year."_".$curr_seme;
			$sql="select scope_id,subject_id from score_ss where ss_id='$des_subject'";
			$rs=$CONN->Execute($sql);
			$subject_id=$rs->fields[subject_id];
			$scope_id=$rs->fields[scope_id];
			if ($subject_id=="0") $subject_id=$scope_id;
			$sql="select subject_name from score_subject where subject_id='$subject_id'";
			$rs=$CONN->Execute($sql);
			$test_name=$rs->fields[subject_name]."��".date("is");
			$query="select teacher_sn,teach_id from teacher_base where teach_condition='0' order by teacher_sn";
			$res=$CONN->Execute($query);
			while (!$res->EOF) {
				$teach_id_arr[$res->fields[teacher_sn]]=$res->fields[teach_id];
				$res->MoveNext();
			}
		} else {
			$score_semester="score_semester_".$curr_year."_".$curr_seme;
			$sql="select print from score_ss where ss_id='$des_subject'";
			$rs=$CONN->Execute($sql);
			$test_kind=($rs->fields['print'])?"�w�����q":"���Ǵ�";
			$test_name=$test_kind;
		}
	} else {
		$ids=explode("_",$des_subject);
		$query="select * from test_manage where id='".$ids[2]."'";
		$res=$CONN->Execute($query);
		$max_subs=count(explode("@@",$res->fields[subject_str]));
	}
	//���X csv ����
	$file_name=$temp_path.$sel_file;
	$fd=fopen($file_name,"r");
	$stud_study_year=date("Y")-1911;
	while ($tt = sfs_fgetcsv ($fd, 2000, ",")) {
		if (in_array("�y��",$tt)) {
			chk_data($tt);
		}
		//�P�_�O�_���~�����
		if ($vs[0]==99) {
			$tt[99]=$tt[$vs[99]].sprintf("%02d",$tt[$vs[98]]);
		}
		if ($temp_score_arr[$des_subject]=="") {
			echo "<table><tr><td>";
			if ($spec_test_arr[$des_subject]=="") {
				//�P�_�O�_���~�����
				$c_year=intval(substr($tt[$vs[0]],0,1));
				if ($c_year > 0 && $subject==$tt[$vs[4]] && $tt[$vs[7]]!="") {
					if ($c_year<$IS_JHORES) $c_year+=$IS_JHORES;
					$class_id=sprintf("%03d_%d_%02d_%02d",$curr_year,$curr_seme,$c_year,substr($tt[$vs[0]],-2,2));
					$curr_class_num=sprintf("%d%02d%02d",$c_year,substr($tt[$vs[0]],-2,2),$tt[$vs[1]]);
					$student_sn=$stud[$curr_class_num];
					$score=$tt[$vs[7]];
					$class_subj=$class_id."_".$subject_id;
					if ($student_sn!="") {
						if ($tkind=="1") {
							if ($class_subj != $old_subj) {
								$sql="select teacher_sn from score_course where year='$curr_year' and semester='$curr_seme' and class_id='$class_id' and ss_id='$des_subject'";
								$rs=$CONN->Execute($sql);
								$teach_id=$teach_id_arr[$rs->fields[teacher_sn]];
								$sql="select max(freq) from $score_semester where teach_id='$teach_id' and class_subj='$class_subj' and stage='$stage'and enable='1'";
								$rs=$CONN->Execute($sql);
								$freq=$rs->fields[0]+1;
							}
							$old_subj=$class_subj;
							$sql="insert into $score_semester (teach_id,stud_sn,class_subj,stage,test_name,test_score,weighted,enable,freq) values ('$teach_id','$student_sn','$class_subj','$stage','$test_name','$score','1','1','$freq')";
							$rs=$CONN->Execute($sql);
							echo $tt[$vs[0]]."�Z".sprintf("%2d",$tt[$vs[1]])."��".$tt[$vs[2]]."--".$subject."--".$tt[$vs[7]]."��---�פJ����<br>";
						} else {
							$sql="replace into $score_semester (class_id,student_sn,ss_id,score,test_name,test_kind,test_sort,update_time,sendmit) values ('$class_id','$student_sn','$des_subject','$score','$test_name','$test_kind','$stage','$today','1')";
							$rs=$CONN->Execute($sql);
							echo $tt[$vs[0]]."�Z".sprintf("%2d",$tt[$vs[1]])."��".$tt[$vs[2]]."--".$subject."--".$tt[$vs[7]]."��---�פJ����<br>";
						}
					}
				}
			} else {
				if ($tkind) {
					$c_year = intval(substr($tt[$vs[0]],0,1));
					if ($c_year > 0 && $subject==$tt[$vs[4]] && $tt[$vs[7]]!="") {
						$c_year+=$IS_JHORES;
						$score_spec="score_spec_".$curr_year."_".$curr_seme;
						$curr_class_num=sprintf("%d%02d%02d",$c_year,substr($tt[$vs[0]],-2,2),$tt[$vs[1]]);
						$student_sn=$stud[$curr_class_num];
						$query="select * from $score_spec where id='".$ids[2]."' and student_sn='$student_sn'";
						$res=$CONN->Execute($query);
						$scs=explode("@@",$res->fields[score_str]);
						$scs[$tkind-1]=$tt[$vs[7]];
						$scs_str="";
						for ($i=0;$i<=$max_subs-1;$i++) $scs_str.=$scs[$i]."@@";
						$scs_str=substr($scs_str,0,-2);
						if ($res->fields[student_sn]=="") {
							$query="insert into $score_spec (student_sn,id,score_str) values ('$student_sn','".$ids[2]."','$scs_str')";
						} else {
							$query="update $score_spec set score_str='$scs_str' where id='".$ids[2]."' and student_sn='$student_sn'";
						}
						$CONN->Execute($query);
						echo $tt[$vs[0]]."�Z".sprintf("%2d",$tt[$vs[1]])."��".$tt[$vs[2]]."--".$subject."--".$tt[$vs[7]]."��---�פJ����<br>";
					}
				}
			}
		} else {
			$temp_class=$tt[$vs[0]];
			if (substr($temp_class,0,1)<$IS_JHORES) $temp_class+=$IS_JHORES*100;
			$temp_site=intval($tt[$vs[1]]);
			$query="select newstud_sn,stud_name from new_stud where temp_class='$temp_class' and temp_site='$temp_site' and stud_study_year='$stud_study_year'";
			$rs=$CONN->Execute($query) or die($query);
			$newstud_sn=$rs->fields['newstud_sn'];
			$stud_name=addslashes($rs->fields['stud_name']);
			$score=trim($tt[$vs[7]]);
			if ($score=="") {
				$score="-100";
				$score_str="�L���Z";
			} else {
				$score=intval($score);
				$score_str=$score."��";
			}
			if ($newstud_sn) {
				$query="update new_stud set ".$des_subject."='$score' where newstud_sn='$newstud_sn'";
				$CONN->Execute($query) or die($query);
				echo $temp_class."�Z".$temp_site."��".stripslashes($stud_name)."--".$temp_score[$des_subject]."--".$score_str."---�פJ����<br>";
			}
		}
		echo "</td></tr>";
	}
}

if (($sel_file && !$trans) || ($trans && (!$des_subject || !$stage))) {
	//���X csv ����
	$file_name=$temp_path."/".$sel_file;
	$fd=fopen($file_name,"r");
	$right_data=0;
	while(!$right_data){
		$tt = sfs_fgetcsv ($fd, 2000, ",");
		chk_data($tt);
		//�P�_�O�_���~�����
		if ($vs[0]==99) {
			$c_year=intval($tt[$vs[99]]);
			$tt[99]=$tt[$vs[99]].sprintf("%02d",$tt[$vs[98]]);
		} else {
			$c_year = intval(substr($tt[$vs[0]],0,1));
		}
		if ($c_year > 0) {
			if ($c_year<7) $c_year+=$IS_JHORES;
			$right_data=1;
		}
		$i++;
	}
	$class_id=sprintf("%03d_%d",$curr_year,$curr_seme)."%";
	//��ؿ��
	$sql="select subject_id,ss_id,scope_id,class_id from score_ss where year='$curr_year' and semester='$curr_seme' and class_year='$c_year' and enable='1' and need_exam='1'";
	$rs=$CONN->Execute($sql) or die($sql);
	while(!$rs->EOF){
		$subject_id = $rs->fields["subject_id"];
		$ss_id = $rs->fields["ss_id"];
		$scope_id = $rs->fields["scope_id"];
		if($subject_id=="0") $subject_id = $scope_id;
		$rs_s=$CONN->Execute("select subject_name from score_subject where subject_id='$subject_id'");
		if ($rs->fields['class_id']=="") $k="(���~�Žҵ{)";
		else $k="(".substr($rs->fields['class_id'],-2,2)."�Z�ҵ{)";
		$subject_name[$ss_id] = $rs_s->fields["subject_name"].$k;
		$rs->MoveNext();
	}
	reset($temp_score_arr);
	while(list($k,$v)=each($temp_score_arr)) {
		$subject_name[$k]=$v;
	}
	reset($spec_test_arr);
	while(list($k,$v)=each($spec_test_arr)) {
		$subject_name[$k]=$v;
	}
	$sc = new drop_select();
	$sc->s_name ="des_subject";
	$sc->top_option = "��ܬ��";
	$sc->id = $des_subject;
	$sc->arr = $subject_name;
	$sc->is_submit = true;
	$ss_menu=$sc->get_select();
	//���q���
	$score_semester="score_semester_".$curr_year."_".$curr_seme;
	$post_msg="";
	if ($des_subject) {
		//���o�`���q��
		$sql="select performance_test_times from score_setup where class_year='$c_year' and year='$curr_year' and semester='$curr_seme' and enable='1'";
		$rs=$CONN->Execute($sql);
		$test_times=$rs->fields[performance_test_times];
		//���o�O�_�C�@����ҭn�t�X�@�����ɦ��Z
		$rs_yorn=$CONN->Execute("SELECT pm_value FROM pro_module WHERE pm_name='score_input' AND pm_item='yorn'");
		$yorn=$rs_yorn->fields['pm_value'];
		//���o���Z�]�w�覡
		$sql="select * from score_ss where ss_id='$des_subject'";
		$rs=$CONN->Execute($sql);
		$print=$rs->fields['print'];
		if ($print==0) {
			$kind_arr[0]="���ɦ��Z";
			$stage_arr[255]="���Ǵ�";
			$post_msg="<input type='hidden' name='tkind' value='1'><input type='hidden' name='stage' value='255'>";
		} else {
			$kind_arr[1]="���ɦ��Z";
			$kind_arr[2]="�w�����q";
			if ($yorn=='n' && $tkind=='1') {
				if ($stage>254) $stage=0;
				$stage_arr[254]="���ɦ��Z";
				$post_msg="<input type='hidden' name='stage' value='254'>";
			} else {
				if ($stage>250) $stage=0;
				if ($test_times > 0)
					for ($i=1;$i<=$test_times;$i++) $stage_arr[$i]="��".$i."���q";
				else
					$stage_arr[0]="���]���q";
			}
		}
	}
	if (count($stage_arr)=='1') {
		$st_menu="";
	} else {
		$st = new drop_select();
		$st->s_name ="stage";
		$st->top_option = "��ܶ��q";
		$st->id = $stage;
		$st->arr = $stage_arr;
		$st->is_submit = true;
		$st_menu=$st->get_select();
	}
	//�������O���
	$show_kind_menu=($des_subject && $temp_score_arr[$des_subject]=="");
	if ($spec_test_arr[$des_subject]) {
		$show_kind_menu=1;
		$ids=explode("_",$des_subject);
		$query="select * from test_manage where id='".$ids[2]."'";
		$res=$CONN->Execute($query);
		$kind_arr=explode("@@","@@".$res->fields[subject_str]);
		unset($kind_arr[0]);
		$post_msg="";
		$kind_str="�פJ���";
	} else {
		$kind_str="�פJ���Z���O";
	}
	if ($show_kind_menu) {
		if (count($kind_arr)==1) {
			$sk_menu=$kind_arr[0];
		} else {
			$sk = new drop_select();
			$sk->s_name ="tkind";
			if ($spec_test_arr[$des_subject])
				$sk->top_option = "��ܬ��";
			else
				$sk->top_option = "������O";
			$sk->id = $tkind;
			$sk->arr = $kind_arr;
			$sk->is_submit = true;
			$sk_menu=$sk->get_select();
		}
	}

	$show_stage_menu=($des_subject && $tkind && $st_menu!="");
		echo "	<td class='title_sbody1' nowrap colspan='3'><p align='left'>
		<form name='form2' enctype='multipart/form-data' action='{$_SERVER['SCRIPT_NAME']}' method='post'>
		<table cellspacing='1' cellpadding='3' class='main_body'>
		<tr>
		<td class='title_sbody2'><p align='center'>�Z��</p>
		<td class='title_sbody2'><p align='center'>�y��</p>
		<td class='title_sbody2'><p align='center'>�m�W</p>
		<td class='title_sbody2'><p align='center'>���</p>
		<td class='title_sbody2'><p align='center'>����</p>
		<td class='title_sbody2'><p align='center'>�פJ��ةδ���W��</p>";
	if ($show_kind_menu) echo "<td class='title_sbody2'><p align='center'>$kind_str</p>";
	if ($show_stage_menu) echo "<td class='title_sbody2'><p align='center'>�פJ�q���O</p>";
	echo "	</tr>
		<tr bgcolor='#ffffff'>
		<td>".$tt[$vs[0]]."
		<td>".$tt[$vs[1]]."
		<td>".$tt[$vs[2]]."
		<td>".$tt[$vs[4]]."
		<td>".$tt[$vs[7]]."
		<td>$ss_menu";
	if ($show_kind_menu) echo "<td align='center'>$sk_menu";
	if ($show_stage_menu) echo "<td>$st_menu";
	echo "	</tr>
		</table>
		$post_msg";
	if ($des_subject && ($post_msg!="" || ($tkind!=0 && $stage!=0)) || ($spec_test_arr[$des_subject]&&$tkind!="")) echo "<p align='left'><input type='submit' name='trans' value='�}�l�פJ'></p>";
	echo "	<input type='hidden' name='sel_file' value='$sel_file'>
		<input type='hidden' name='subject' value='".$tt[$vs[4]]."'>
		</form></p>";
}

echo "</table>".$help;
foot();

function chk_data($kk) {
	global $vs;

	reset($kk);
	while (list($k,$v)=each($kk)) {
		$v=trim($v);
		switch ($v) {
			case "�~��":
				$vs[0]=99; //��쬰�~�šB�Z��...�Ҧ�
				$vs[99]=$k;
				break;
			case "�Z��":
				if ($vs[0]!=99)
					$vs[0]=$k;
				else
					$vs[98]=$k;
				break;
			case "�Z�ťN�X":
				$vs[0]=$k;
				break;
			case "�y��":
				$vs[1]=$k;
				break;
			case "�m�@�@�W":
				$vs[2]=$k;
				break;
			case "�m�W":
				$vs[2]=$k;
				break;
			case "�ǥͩm�W":
				$vs[2]=$k;
				break;
			case "��ئW��":
				$vs[4]=$k;
				break;
			case "��o����":
				$vs[7]=$k;
				break;
			case "�`���Z":
				$vs[7]=$k;
				break;
			case "�`��":
				$vs[7]=$k;
				break;
			case "���Z":
				$vs[7]=$k;
				break;
			default:
				break;
		}
	}
}
?>