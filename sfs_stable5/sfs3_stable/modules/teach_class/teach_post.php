<?php

// $Id: teach_post.php 7454 2013-08-30 01:30:19Z hami $

// ���J�]�w��
include "teach_config.php";

// �{���ˬd
sfs_check();

// ���ݭn register_globals
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}

//���b¾���A
if ($c_sel != "")
	$sel = $c_sel;
else if ($sel=="")
	$sel = 0 ; //�w�]����b¾���p


switch ($do_key) {
	case $editBtn :
		// ���ˬd�ӱЮv�� teacher_sn �O�_�s�b? �_�h�L�k��s�Юv���������
		$query="select teacher_sn from teacher_post where teacher_sn='$teacher_sn'";
		$rs=&$CONN->Execute($query);
		if (!$rs) {
		  	print $CONN->ErrorMsg();
		} else {
			if ($rs->fields[teacher_sn]) {
				$sql = "update teacher_post set post_kind='$post_kind',post_office='$post_office',post_level='$post_level',official_level='$official_level',post_class='$post_class',post_num='$post_num',bywork_num='$bywork_num',salay='$salay',appoint_date='$appoint_date',arrive_date='$arrive_date',approve_date='$approve_date',approve_number='$approve_number',teach_title_id='$teach_title_id',class_num='$class_num',update_id='$update_id' where teacher_sn='$teacher_sn'";
			} else {
				$sql = "insert into teacher_post(teacher_sn, post_kind, post_office,post_level,official_level,post_class,post_num,bywork_num,salay,appoint_date,arrive_date,approve_date,approve_number,teach_title_id, class_num,update_time,update_id) VALUES ('$teacher_sn', '$post_kind','$post_office','$post_level','$official_level','$post_class','$post_num','$bywork_num','$salay','$appoint_date','$arrive_date','$approve_date','$approve_number','$teach_title_id','$class_num','$update_time','admin')";
			}
			$CONN->Execute($sql) or die($sql);
			$tk=title_kind();
			if (($tk[$teach_title_id]=="�ɮv" || $tk[$teach_title_id]=="�ť��Ѯv")&&$class_num) {
				$res=$CONN->Execute("select name from teacher_base where teacher_sn='$teacher_sn'");
				$CONN->Execute("update school_class set teacher_1='".addslashes($res->fields[name])."' where year='".curr_year()."' and semester='".curr_seme()."' and c_year='".substr($class_num,0,-2)."' and c_sort='".substr($class_num,-2,2)."' and enable='1'") or die("update school_class set teacher_1='".$res->fields[name]."' where year='".curr_year()."' and semester='".curr_seme()."' and c_year='".substr($class_num,0,-2)."' and c_sort='".substr($class_num,-2,2)."' and enable='1'");
			}
		}
		break;
}

//�L�X���Y
head("�Юv��¾���");
//����T
$field_data = get_field_info("teacher_post");
//���s���r��
$linkstr = "teacher_sn=$teacher_sn&sel=$sel";
//�L�X���
print_menu($teach_menu_p,$linkstr);
//�x�s���U�@��
if ($chknext)
	$teacher_sn = $nav_next;	
$query = "select teacher_sn from teacher_base where teacher_sn='$teacher_sn' and teach_condition ='$sel'";
$res = $CONN->Execute($query) or die($query);
//���]�w�Χ��ܦb¾���p�ΧR���O���� ��Ĥ@��
if ($teacher_sn =="" || $teacher_sn != $res->fields[teacher_sn]) {
	$result= $CONN->Execute("select teacher_base.teacher_sn,teacher_base.teach_condition from teacher_base left join teacher_post on teacher_base.teacher_sn=teacher_post.teacher_sn where  teacher_base.teach_condition ='$sel' limit 0,1");
	$teacher_sn = $result->fields[0];
}
$sql_select = "select teacher_post.*,teacher_base.teacher_sn,teacher_base.name,teacher_base.teach_condition from teacher_base left join teacher_post on teacher_base.teacher_sn = teacher_post.teacher_sn where  teacher_base.teacher_sn='$teacher_sn' and teacher_base.teach_condition ='$sel'";
$recordSet = $CONN->Execute($sql_select);
while (!$recordSet->EOF) {
	$teach_id = $recordSet->fields["teach_id"];
	$teacher_sn = $recordSet->fields["teacher_sn"];
	$name = $recordSet->fields["name"];	
	$post_kind = $recordSet->fields["post_kind"];
	$post_office = $recordSet->fields["post_office"];
	$post_level = $recordSet->fields["post_level"];
	$official_level = $recordSet->fields["official_level"];
	$post_class = $recordSet->fields["post_class"];
	$post_num = $recordSet->fields["post_num"];
	$bywork_num = $recordSet->fields["bywork_num"];
	$salay = $recordSet->fields["salay"];
	$appoint_date = $recordSet->fields["appoint_date"];
	$arrive_date = $recordSet->fields["arrive_date"];
	$approve_date = $recordSet->fields["approve_date"];
	$approve_number = $recordSet->fields["approve_number"];
	$teach_title_id = $recordSet->fields["teach_title_id"];
	$class_num = $recordSet->fields["class_num"];
	$update_time = $recordSet->fields["update_time"];
	$update_id = $recordSet->fields["update_id"];
	$recordSet->MoveNext();
};

?> 
<script language="JavaScript">
function checkok() {
	document.myform.nav_next.value = document.gridform.nav_next.value;		
	return date_check();
}
//-->
</script>
<TABLE BORDER=0 CELLPADDING=10 CELLSPACING=0 CLASS="tableBg" WIDTH="100%" ALIGN="CENTER"> 

<tr>
    <td valign=top>
   <table border="0" width="100%" cellspacing="0" cellpadding="0">
    <tr>
      <td  valign="top" >
    <?php   
	$remove_p = remove(); //��ܿ��   
	$upstr = "���<select name=\"c_sel\" onchange=\"this.form.submit()\">\n";
      	while (list($tid,$tname)=each($remove_p)){
      		if ($sel== $tid)
      			$upstr .= "<option value=\"$tid\" selected>$tname</option>\n";
      		else
      			$upstr .= "<option value=\"$tid\">$tname</option>\n";
      	}
	$upstr .= "</select>";
	$downstr = "<hr size=1>";
	// source in include/sfs_case_PLlib.php  
	$grid1 = new sfs_grid_menu;  //�إ߿��	   
	$grid1->bgcolor = $gridBgcolor;  // �C��   
	$grid1->row = $gridRow_num ;	     //��ܵ���   
	$grid1->key_item = "teacher_sn";  // ������W  	
	$grid1->display_item = array("name");  // �����W 
	$grid1->display_color = array("1"=>"$gridBoy_color","2"=>"$gridGirl_color"); //�k�k�ͧO
	$grid1->color_index_item ="sex" ; //�C��P�_��
	$grid1->class_ccs = " class=leftmenu";  // �C�����		
	//$grid1->sql_str = "select teacher_sn,concat('&nbsp;',name,'&nbsp;') as name,sex from teacher_base where teach_condition='$sel' order by sex,name";   //SQL �R�O 
	$grid1->sql_str = "select a.teacher_sn,concat('&nbsp;' ,d.title_name , ' -- ', a.name,'&nbsp;') as name, a.sex from teacher_base a
	LEFT JOIN teacher_post c ON a.teacher_sn=c.teacher_sn LEFT JOIN teacher_title d ON c.teach_title_id=d.teach_title_id
	where teach_condition='$sel' order by d.rank, sex,name";   //SQL �R�O
	$grid1->do_query(); //����R�O
	$grid1->print_grid($teacher_sn,$upstr,$downstr); // ��ܵe��

?>    
</td></tr></table>
</td>
    
<td width=100% valign=top bgcolor="#CCCCCC">
<form name="myform" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
<table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF"  width="100%" class="main_body" >

<tr>
	<td  colspan=4><b><?php echo "$teacher_sn -- $name" ?></b></td>

</tr>

<tr>

	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[post_kind][d_field_cname] ?></td>
	<td CLASS="gendata">
	<?php
	//¾�O
	$sel1 = new drop_select();
	$sel1->s_name = "post_kind";
	$sel1->id = $post_kind;
	$sel1->has_empty = false;
	$sel1->arr = post_kind();
	$sel1->do_select();
	?>
	</td>	

    <td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[teach_title_id][d_field_cname] ?></td>
	<td CLASS="gendata" colspan="3">
	<?php
	//¾��
	$sel1 = new drop_select();
	$sel1->s_name = "teach_title_id";
	$sel1->id = $teach_title_id;
	$sel1->has_empty = false;
	$sel1->arr = title_kind();
	$sel1->do_select();
    ?>
    </td>
	
</tr>


<tr>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[post_office][d_field_cname] ?></td>
	<td CLASS="gendata">
	<?php
	//�B��
	$sel1 = new drop_select();
	$sel1->s_name = "post_office";
	$sel1->id = $post_office;
	$sel1->has_empty = false;
	$sel1->arr = room_kind();
	$sel1->do_select();
	?>
    
	</td>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[class_num][d_field_cname] ?></td>
	<td CLASS="gendata">
	<?php
    $sel1 = new drop_select(); //���
	$sel1->s_name = "class_num"; //���W��
	$sel1->id = $class_num;
	$sel1->arr = class_base(); //���e�}�C
    $sel1->do_select();
	?>
	</td>
</tr>
</tr>


<tr>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[post_level][d_field_cname] ?></td>
	<td CLASS="gendata" colspan="3">
	
	<?php
	//¾��
	//$sel1 = new drop_select();
	//$sel1->s_name = "post_level";
	//$sel1->id = $post_level;
	//$sel1->has_empty = false;
	//$sel1->arr = official_level();
	//$sel1->do_select();
	?>
	<input type="text" size="20" maxlength="40" name="post_class" value="<?php echo $post_class ?>"></td>
</tr>


<tr>
	<td align="right" CLASS="title_sbody1" nowrap>��¾�N�X</td>	
	<td CLASS="gendata" colspan="3"><?php echo $field_data[post_num][d_field_cname] ?> <input type="text" size="10" maxlength="20" name="post_num" value="<?php echo $post_num ?>">
	<?php echo $field_data[bywork_num][d_field_cname] ?>  <input type="text" size="10" maxlength="20" name="bywork_num" value="<?php echo $bywork_num ?>"></td>

</tr>


<tr>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[salay][d_field_cname] ?></td>
	<td CLASS="gendata" colspan="3" ><input type="text" size="9" maxlength="9" name="salay" value="<?php echo $salay ?>"></td>
</tr>


<tr>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[appoint_date][d_field_cname] ?></td>
	<td CLASS="gendata" colspan="3"><input type="text" size="10" maxlength="10" name="appoint_date" value="<?php echo $appoint_date ?>"></td>
</tr>


<tr>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[arrive_date][d_field_cname] ?></td>
	<td CLASS="gendata" colspan="3"><input type="text" size="10" maxlength="10" name="arrive_date" value="<?php echo $arrive_date ?>"></td>
</tr>


<tr>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[approve_date][d_field_cname] ?></td>
	<td CLASS="gendata" colspan="3"><input type="text" size="10" maxlength="10" name="approve_date" value="<?php echo $approve_date ?>"></td>
</tr>


<tr>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[approve_number][d_field_cname] ?></td>
	<td CLASS="gendata" colspan="3"><input type="text" size="60" maxlength="60" name="approve_number" value="<?php echo $approve_number ?>"></td>
</tr>
<tr>
	
	<td colspan="4" align=center>
	<input type="hidden" name="update_id" value="<?php echo $_SESSION['session_log_id'] ?>">
	<input type="hidden" name="teach_id" value="<?php echo $teach_id ?>">
	<input type="hidden" name="teacher_sn" value="<?php echo $teacher_sn ?>">
	<?php 
		if ($chknext)
    			echo "<input type=\"checkbox\" name=\"chknext\" value=1 checked >";			
    		else
    			echo "<input type=\"checkbox\" name=\"chknext\" value=1 >";
    	
    	?>
    	 �۰ʸ��U�@�� &nbsp;&nbsp<input type=hidden name=nav_next >
	<input type=submit name="do_key" value ="<?php echo $editBtn ?>" onClick="return checkok();">
	</td>
</tr>

</table>
</FORM>
</TD>
</TR>
</TABLE>
<?php 
//�L�X���Y
foot();
?> 