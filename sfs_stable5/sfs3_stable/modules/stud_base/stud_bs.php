<?php 

// $Id: stud_bs.php 6481 2011-08-17 12:47:59Z infodaes $

// ���J�]�w��
include "config.php";
// �{���ˬd
sfs_check();


// ���ݭn register_globals
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}

if ($_GET['stud_id'] && $_GET['c_curr_seme']) {
	$query="select student_sn from stud_seme where seme_year_seme='".$_GET['c_curr_seme']."' and stud_id='".$_GET['stud_id']."'";
	$res=$CONN->Execute($query);
	if ($res->fields['student_sn']) $student_sn=$res->fields['student_sn'];
}

//����B�z 
switch ($do_key){	
	case $postBtn: //�s�W
		$query="select stud_id from stud_base where student_sn='$student_sn'";
		$res=$CONN->Execute($query);
		$stud_id=$res->fields[stud_id];
		$sql_insert = "insert into stud_brother_sister (stud_id,bs_name,bs_calling,bs_gradu,bs_birthyear,student_sn) values ('$stud_id','$bs_name',$bs_calling,'$bs_gradu','$bs_birthyear','$student_sn')";
		$CONN->Execute($sql_insert) or die ($sql_insert);
		//�O�� log
		sfs_log("stud_brother_sister","insert");
	break;
	case "delete": //�s�W
		$query = "delete from stud_brother_sister where bs_id='$bs_id'";
		$CONN->Execute($query);
		//�O�� log
		sfs_log("stud_brother_sister","delete");

	break;
	case $editBtn: //�ק�
		$query = " select bs_id from stud_brother_sister where student_sn='$student_sn' order by bs_id";
		$result = $CONN->Execute($query);
		while(!$result->EOF) {		
			$bs_id = "bs_id_".$result->fields[0];			
			$bs_name = "bs_name_".$result->fields[0];
			$bs_calling = "bs_calling_".$result->fields[0];
			$bs_gradu = "bs_gradu_".$result->fields[0];
			$bs_birthyear = "bs_birthyear_".$result->fields[0];
			$sql_update = "update stud_brother_sister set bs_name='".$$bs_name."',bs_calling='".$$bs_calling."',bs_gradu='".$$bs_gradu."',bs_birthyear='".$$bs_birthyear."' where bs_id=".$result->fields[0];
			$CONN->Execute ($sql_update) or die ($sql_update);
			$result->MoveNext();
		}
		//�O�� log
		sfs_log("stud_brother_sister","update","$stud_id");

	$ckey = $editModeBtn ;//�]���ק�Ҧ�
	break;	
}
head();
//����T
$field_data = get_field_info("stud_brother_sister");
//���s���r��
$linkstr = "student_sn=$student_sn&c_curr_class=$c_curr_class&c_curr_seme=$c_curr_seme";


//�Ҳտ��
print_menu($menu_p,$linkstr);

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

$c_curr_class_arr = explode("_",$c_curr_class);
$seme_class = intval($c_curr_class_arr[2]).$c_curr_class_arr[3];

	//�x�s���U�@��
if ($chknext)
	$student_sn = $nav_next;	
	$query = "select a.student_sn,a.stud_id,a.stud_name from stud_base a,stud_seme b where a.student_sn=b.student_sn and a.student_sn='$student_sn' and (a.stud_study_cond=0 or a.stud_study_cond=5)  and  b.seme_year_seme='$c_curr_seme' and b.seme_class='$seme_class'";
	$res = $CONN->Execute($query) or die($res->ErrorMsg());
	//���]�w�Χ��ܦb¾���p�ΧR���O���� ��Ĥ@��
	if ($student_sn =="" || $res->RecordCount()==0) {	
		$temp_sql = "select a.student_sn,a.stud_id,a.stud_name from stud_base a,stud_seme b where a.student_sn=b.student_sn  and  (a.stud_study_cond=0 or a.stud_study_cond=5) and  b.seme_year_seme='$c_curr_seme' and b.seme_class='$seme_class' order by b.seme_num ";
		$res = $CONN->Execute($temp_sql) or die($temp_sql);
	}

		$student_sn = $res->fields[0];
		$stud_id = $res->fields[1];
		$stud_name = $res->fields[2];

?>
<script language="JavaScript">

function checkok()
{
	var OK=true;	
	document.myform.nav_next.value = document.gridform.nav_next.value;	
	return OK
}

function setfocus(element) {
	element.focus();
 return;
}
//-->
</script>

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

//��ܯZ��
	$tmp=&get_class_select($s_y,$s_s,"","c_curr_class","this.form.submit",$c_curr_class);
	$upstr .= $tmp;

	$grid1 = new ado_grid_menu($_SERVER['SCRIPT_NAME'],$URI,$CONN);  //�إ߿��	   
	$grid1->bgcolor = $gridBgcolor;  // �C��   
	$grid1->row = $gridRow_num ;	     //��ܵ���   
	$grid1->key_item = "student_sn";  // ������W  	
	$grid1->display_item = array("sit_num","stud_name");  // �����W   
	$grid1->display_color = array("1"=>"$gridBoy_color","2"=>"$gridGirl_color"); //�k�k�ͧO
	$grid1->color_index_item ="stud_sex" ; //�C��P�_��
	$grid1->class_ccs = " class=leftmenu";  // �C�����
	$grid1->sql_str = "select a.stud_id,a.student_sn,a.stud_name,a.stud_sex,b.seme_num as sit_num from stud_base a,stud_seme b where a.student_sn=b.student_sn and (a.stud_study_cond=0 or a.stud_study_cond=5) and  b.seme_year_seme='$c_curr_seme' and b.seme_class='$seme_class' order by b.seme_num ";   //SQL �R�O
	//echo $grid1->sql_str;	
	$grid1->do_query(); //����R�O   
	
	$grid1->print_grid($student_sn,$upstr,$downstr); // ��ܵe��   

?>
     </td>
     
    <td width="100%" valign=top bgcolor="#CCCCCC">
<form name ="myform" action="<?php echo $_SERVER['SCRIPT_NAME'] ?>" method="post"  <?php
	//��mnu���Ƭ�0�� �� form �� disabled
	if ($grid1->count_row==0 && !($do_key == $newBtn || $do_key == $postBtn))  
		echo " disabled "; 
	?> > 
<table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF"  width="100%" class=main_body >
<tr>
<td class=title_mbody colspan=5 align=center >
	<?php 
		echo sprintf("%d�Ǧ~��%d�Ǵ� %s--%s (%s)",substr($c_curr_seme,0,-1),substr($c_curr_seme,-1),$class_list_p[$c_curr_seme],$stud_name,$stud_id);
	    	if ($chknext)
    			echo "<input type=checkbox name=chknext value=1 checked >";			
    		else
    			echo "<input type=checkbox name=chknext value=1 >";
    			
    		echo "�۰ʸ��U�@��";
    
    ?>
	</td>	
</tr>
<tr><td colspan=5 align=center>
<?php
	echo ($ckey == "$editModeBtn"  )? "<input type=submit name=\"ckey\" value=\"$browseModeBtn\">": "<input type=submit name=\"ckey\" value=\"$editModeBtn\">";
	echo "&nbsp;&nbsp;<input type=\"submit\" name=\"ckey\" value=\"$newBtn\">";
	if ($ckey==$editModeBtn) 
	echo "&nbsp;&nbsp;<input type=\"submit\" name=\"do_key\" value=\"$editBtn\" onClick=\"return checkok();\" >";
?>	
</td></tr>
<tr><td><?php echo $field_data[bs_name][d_field_cname] ?></td>
	<td><?php echo $field_data[bs_calling][d_field_cname] ?></td>
	<td><?php echo $field_data[bs_gradu][d_field_cname] ?></td>
	<td><?php echo $field_data[bs_birthyear][d_field_cname] ?></td>
	<td>�ʧ@</td>
</tr>
<?php
	//�s�W�Ҧ�
	if ($ckey==$newBtn|| $key == $postBtn){
			echo "<tr>";
			echo "<td> <input name=bs_name size=10 ></td><td>";
			//�ٿ�
			$sel1 = new drop_select(); //������O 		
			$sel1->s_name = "bs_calling"; //���W��
			$sel1->id = 0;
			$sel1->arr = bs_calling_kind(); //���e�}�C			
			$sel1->do_select();
			echo "</td>";
			echo "<td><input name=bs_gradu size=10 ></td>";
			echo "<td><input name=bs_birthyear size=4 ></td>";
			echo "<td><input type=submit name=\"do_key\" value=\"$postBtn\"></td>";
			echo "</tr>";
	}
?>
<?php	 

	$sql_select = "select bs_id,student_sn,bs_name,bs_calling,bs_gradu,bs_birthyear from stud_brother_sister where student_sn='$student_sn'";
	$recordSet = $CONN->Execute($sql_select) or die($sql_select);
	while (!$recordSet->EOF) {

		$bs_id = $recordSet->fields["bs_id"];
		$student_sn = $recordSet->fields["student_sn"];
		$bs_name = $recordSet->fields["bs_name"];
		$bs_calling = $recordSet->fields["bs_calling"];
		$bs_gradu = $recordSet->fields["bs_gradu"];
		$bs_birthyear = $recordSet->fields["bs_birthyear"];

		$bs_calling_kind_p = bs_calling_kind(); //�ٿ�	

		$ti = ($i%2)+1;	
		if ($bs_id) {
			echo "<tr class=nom_$ti >";
			if ($ckey==$editModeBtn && $bs_id) { //�ק�Ҧ�
				echo "<td> <input name=bs_name_$bs_id size=10 value=\"".$bs_name."\"></td><td>";
				//�ٿ�
				$sel1 = new drop_select(); //������O
				$sel1->s_name = "bs_calling_$bs_id"; //���W��
				$sel1->id = intval($bs_calling);
				$sel1->arr = bs_calling_kind(); //���e�}�C			
				$sel1->do_select();				
				echo "</td>";
				echo "<td><input name=bs_gradu_$bs_id size=10 value=\"".$bs_gradu."\"></td>";
				echo "<td><input name=bs_birthyear_$bs_id size=4 value=\"".$bs_birthyear."\"></td>";
				echo "<td> <a href=\"{$_SERVER['SCRIPT_NAME']}?do_key=delete&bs_id=$bs_id&ckey=$ckey&$linkstr\" onClick=\"return confirm('�T�w�R�� ".$bs_name." �O��?');\">�R��</a></td>";
			}
			else {
				echo "<td>".$bs_name."</td><td>";
				echo $bs_calling_kind_p[$bs_calling];			
				echo "</td><td>".
				$bs_gradu."</td><td>".
				$bs_birthyear."</td>";
				echo "<td> <a href=\"{$_SERVER['SCRIPT_NAME']}?do_key=delete&bs_id=$bs_id&ckey=$ckey&$linkstr\" onClick=\"return confirm('�T�w�R�� ".$bs_name." �O��?');\">�R��</a></td>";
			}
			echo "</tr>";
		}		
		$recordSet->MoveNext();
	}
	if ($ckey == $editModeBtn && $bs_id) {		
		echo "<tr><td colspan=5 align=center>";
		if ($chknext)
    			echo "<input type=checkbox name=chknext value=1 checked >";			
    		else
    			echo "<input type=checkbox name=chknext value=1 >";
    			
    		echo "�۰ʸ��U�@�� &nbsp;&nbsp;";
		echo "<input type=\"submit\" name=\"do_key\" value=\"$editBtn\" onClick=\"return checkok();\">";
		echo "</td></tr>";		
	}
?>

</table>
    �@</td>
  </tr>
</table>
<input type="hidden" name="student_sn" value="<?php echo $student_sn ?>">
<input type="hidden" name="c_curr_seme" value="<?php echo $c_curr_seme ?>">
<input type="hidden" name="c_curr_class" value="<?php echo $c_curr_class ?>">
<input type=hidden name=nav_next >
</form>
<?php
foot();
?>