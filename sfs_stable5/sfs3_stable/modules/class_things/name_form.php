<?php

// $Id: name_form.php 9274 2018-12-03 08:09:28Z chiming $

/*�ޤJ�ǰȨt�γ]�w��*/
include "config.php";

if($_GET['many_col']) $many_col=$_GET['many_col'];
else $many_col=$_POST['many_col'];
//�ϥΪ̻{��
sfs_check();
if(empty($sel_year))$sel_year = curr_year(); //�ثe�Ǧ~
if(empty($sel_seme))$sel_seme = curr_seme(); //�ثe�Ǵ�
$teacher_sn=$_SESSION['session_tea_sn'];//���o�n�J�Ѯv��id
//��X���ЯZ��
$class_name=teacher_sn_to_class_name($teacher_sn);

//�L�C�\�Ҫ�
$urlA="course_setup3.php?act=downlod_ct&class_id=".$class_name[3];
$urlB="course_setup3.php?act=downlod_ct_htm&class_id=".$class_name[3];

if($_POST['Submit1']=="�U���Z�ŦW��") {
  $print_key=$_POST['print_key'];
  if ($print_key == "sxw")   
     echo ooo();
  else 
     print_key($sel_year,$sel_seme,$print_key,$many_col) ;
}    
else{
	//�q�X����
	head("�Z�Ũư�");

	if ($_GET['act']=="") print_menu($menu_p);
	
		
	//�]�w�D������ܰϪ��I���C��
	$menu="
		<table cellspacing=2 cellpadding=2>
			<tr>
				<td>
					<form name='form1' method='post' action='{$_SERVER['PHP_SELF']}'>
					$import_option
					�ť����ơG<input type='text' name='many_col' size='2' maxlength='4' value='0'>
					<input type='submit' name='Submit1' value='�U���Z�ŦW��'>
					</form>
<a href='$urlA' target=_blank>�\�Ҫ�</a>�B<a href='$urlB' target=_blank>�������\�Ҫ�</a>
				</td>
			</tr>
		</table>";
	echo $menu;
    $seme_year_seme=sprintf("%03d",curr_year()).curr_seme();
    $sql="select stud_id,seme_num from stud_seme where seme_class='$class_name[0]' and  seme_year_seme='$seme_year_seme' order by  seme_num";
    $rs=$CONN->Execute($sql);
    $m=0;
    echo "<table bgcolor='#000000' border=0 cellspacing=1 cellpadding=2><tr bgcolor='#FAF799'><td colspan='2'>$class_name[1]</td></tr>";
	while(!$rs->EOF){
        $stud_id[$m] = $rs->fields["stud_id"];
        $site_num[$m] = $rs->fields["seme_num"];
        $rs_name=$CONN->Execute("select stud_name from stud_base where stud_id='$stud_id[$m]' and stud_study_cond =0 ");
        
        if ($rs_name->fields["stud_name"]) {
           $stud_name[$m] = $rs_name->fields["stud_name"];	
           echo "<tr bgcolor='#FFFFFF'><td>$site_num[$m]</td><td>$stud_name[$m]</td></tr>";
		$m++;
	}
        $rs->MoveNext();
    }
	echo "</table>";
	//�����D������ܰ�
	echo "</td>";
	echo "</tr>";
	echo "</table>";
	//�{���ɧ�
	foot();
}

//�C�L���
function print_key($sel_year="",$sel_seme="",$print_key="",$cols=0){
	global $CONN, $class_name;
	
	
	//��X��excel�Bword	
	if ($print_key=="Excel")
		$filename =  "name.xls"; 	
	else if ($print_key=="Word")
		$filename =  "name.doc";
 
	header("Content-disposition: filename=$filename");
	header("Content-type: application/octetstream");
	//header("Pragma: no-cache");
					//�t�X SSL�s�u�ɡAIE 6,7,8�U�������D�A�i��ק� 
				header("Cache-Control: max-age=0");
				header("Pragma: public");

	header("Expires: 0");
 
    $seme_year_seme=sprintf("%03d",curr_year()).curr_seme();
    $sql="select stud_id,seme_num from stud_seme where seme_class='$class_name[0]' and  seme_year_seme='$seme_year_seme' order by  seme_num";
    $rs=$CONN->Execute($sql);	
    $m=0;
    $td_str="" ;
    for($m=0;$m<$cols;$m++)
        $td_str .= "<td></td>" ;
        
        
    echo "<table border=1 cellspacing=1 cellpadding=2 width = '95%'>
    <tr ><td colspan='2'>$class_name[1]</td>$td_str</tr>";
	while(!$rs->EOF){
        $stud_id[$m] = $rs->fields["stud_id"];
        $site_num[$m] = $rs->fields["seme_num"];
        $rs_name=$CONN->Execute("select stud_name from stud_base where stud_id='$stud_id[$m]' and stud_study_cond =0 ");
        
        if ($rs_name->fields["stud_name"]) {
           $stud_name[$m] = $rs_name->fields["stud_name"];	
           echo "<tr ><td>$site_num[$m]</td><td>$stud_name[$m]</td>$td_str</tr>";
		$m++;
	}
        $rs->MoveNext();
    }
	echo "</table>";

	exit;
}

function ooo(){
	global $CONN,$class_name,$many_col;

	$oo_path = "ooo_nameform";

	$filename="nameform".$class_name[0].".sxw";
	$ttt = new EasyZip;
	$ttt->setPath($oo_path);
	$ttt->addDir('META-INF');
	$ttt->addfile("settings.xml");
	$ttt->addfile("styles.xml");
	$ttt->addfile("meta.xml");

	//Ū�X content.xml
	$data = $ttt->read_file(dirname(__FILE__)."/$oo_path/content.xml");

	//�N content.xml �� tag ���N
    //$class_name=teacher_sn_to_class_name($_SESSION['session_tea_sn']);
    $seme_year_seme=sprintf("%03d",curr_year()).curr_seme();
    $sql="select stud_id,seme_num from stud_seme where seme_class='$class_name[0]' and  seme_year_seme='$seme_year_seme' order by seme_num";
    $rs=$CONN->Execute($sql);
    $m=0;
    while(!$rs->EOF){
        $stud_id[$m] = $rs->fields["stud_id"];
        $site_num[$m] = $rs->fields["seme_num"];
        $rs_name=$CONN->Execute("select stud_name from stud_base where stud_id='$stud_id[$m]' and stud_study_cond =0 ");
        if ($rs_name->fields["stud_name"] ) {
          $stud_name[$m] = $rs_name->fields["stud_name"];
          $m++;
        }
        $rs->MoveNext();
    }
	$head.="
		<table:table-header-rows>
		<table:table-row>
		<table:table-cell table:style-name='course_tbl.A1' table:number-columns-spanned='2' table:value-type='string'>
		<text:p text:style-name='P1'>$class_name[1]
		</text:p>
		</table:table-cell>
		<table:covered-table-cell/>";
	for($m=0;$m<$many_col;$m++){
		$head.="
			<table:table-cell table:style-name='course_tbl.L1' table:value-type='string'>
			<text:p text:style-name='P1'/>
			</table:table-cell>";
	}
	$head.="
		</table:table-row>
		</table:table-header-rows>";

    for($i=0;$i<count($stud_id);$i++){
        $cont.="
			<table:table-row>
			<table:table-cell table:style-name='course_tbl.A2' table:value-type='string'>
			<text:p text:style-name='P2'>$site_num[$i]
			</text:p>
			</table:table-cell>
			<table:table-cell table:style-name='course_tbl.A2' table:value-type='string'>
			<text:p text:style-name='P2'>$stud_name[$i]
			</text:p>
			</table:table-cell>";

		for($m=0;$m<$many_col;$m++){
		$cont.="
			<table:table-cell table:style-name='course_tbl.L2' table:value-type='string'>
			<text:p text:style-name='P2'/>
			</table:table-cell>";
		}
		$cont.="
			</table:table-row>";
    }

	$temp_arr["many_col"] = ($many_col<"1")?"0":$many_col-1;
    $temp_arr["head"] = $head;
	$temp_arr["cont"] = $cont;
	// change_temp �|�N�}�C���� big5 �ର UTF-8 �� openoffice �i�HŪ�X
	$replace_data = $ttt->change_temp($temp_arr,$data,0);

//��ie�U�����ɦW���T*************************************	
	//Ū�X XML ���Y
	$doc_head = $ttt->read_file(dirname(__FILE__)."/$oo_path/content_head.xml");
	//Ū�X XML �ɧ�
	$doc_foot = $ttt->read_file(dirname(__FILE__)."/$oo_path/content_foot.xml");
	$replace_data =$doc_head.$replace_data.$doc_foot;	
//************************************************************************
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
	return;
}

?>