<?php 

// $Id: stud_dom1_self.php 7094 2013-01-28 07:28:15Z hsiao $

// 載入設定檔
include "config.php";
// 認證檢查
sfs_check();

// 健保卡查核
switch ($ha_checkary){
        case 2:
                ha_check();
                break;
        case 1:
                if (!check_home_ip()){
                        ha_check();
                }
                break;
}

//印出檔頭
head("學生資料自建");

//欄位資訊
$field_data = get_field_info("stud_domicile");

//模組選單
print_menu($menu_p);

//檢查是否開放編輯戶口資料
if ($m_arr["dom_edit"]!="1"){
   echo "目前不開放編輯戶口資料！";
   exit;
}

//只限當學期
$seme_year_seme=sprintf("%03d",curr_year()).curr_seme();

//取得登入學生的學號和流水號
$query="select * from stud_seme where seme_year_seme='$seme_year_seme' and stud_id='".$_SESSION['session_log_id']."'";
$res=$CONN->Execute($query);
$student_sn=$res->fields['student_sn'];
if ($student_sn) {
	$query="select * from stud_base where student_sn='$student_sn'";
	$res=$CONN->Execute($query);
	if ($res->fields['stud_study_cond']!="0") {
		$student_sn="";
	} else {
		$stud_name=$res->fields['stud_name'];
	}
}

//按鍵處理 
switch ($_POST['do_key']){	
	case $editBtn: //修改
	$sql = "select * from stud_domicile where student_sn='$student_sn'";
	$recordSet = $CONN->Execute($sql) or die($sql);
    if  ($recordSet->RecordCount() == 0 ) 
        $sql_update = " INSERT INTO stud_domicile (stud_id , fath_name , fath_birthyear , fath_alive , fath_relation , fath_p_id , fath_education , fath_occupation , fath_unit , fath_work_name , fath_phone , fath_home_phone , fath_hand_phone , fath_email , moth_name , moth_birthyear , moth_alive , moth_relation , moth_p_id , moth_education , moth_occupation , moth_unit , moth_work_name , moth_phone , moth_home_phone , moth_hand_phone , moth_email , guardian_name , guardian_phone , guardian_address , guardian_relation , guardian_p_id , guardian_unit , guardian_work_name , guardian_hand_phone , guardian_email , grandfath_name , grandfath_alive , grandmoth_name , grandmoth_alive , update_time , update_id , student_sn ) 
			VALUES ('".$_SESSION['session_log_id']."', '".$_POST['fath_name']."' , '".$_POST['fath_birthyear']."' , '".$_POST['fath_alive']."' ,
            '".$_POST['fath_relation']."' , '".$_POST['fath_p_id']."' , '".$_POST['fath_education']."' , '".$_POST['fath_occupation']."' ,
            '".$_POST['fath_unit']."' , '".$_POST['fath_work_name']."' , '".$_POST['fath_phone']."' , '".$_POST['fath_home_phone']."' , 
            '".$_POST['fath_hand_phone']."' , '".$_POST['fath_email']."' , '".$_POST['moth_name']."' , '".$_POST['moth_birthyear']."' , 
            '".$_POST['moth_alive']."' , '".$_POST['moth_relation']."' , '".$_POST['moth_p_id']."' , '".$_POST['moth_education']."' ,
            '".$_POST['moth_occupation']."' , '".$_POST['moth_unit']."' , '".$_POST['moth_work_name']."' , '".$_POST['moth_phone']."' , 
            '".$_POST['moth_home_phone']."' , '".$_POST['moth_hand_phone']."' , '".$_POST['moth_email']."' , '".$_POST['guardian_name']."' , 
            '".$_POST['guardian_phone']."' , '".$_POST['guardian_address']."' , '".$_POST['guardian_relation']."' , 
            '".$_POST['guardian_p_id']."' , '".$_POST['guardian_unit']."' , '".$_POST['guardian_work_name']."' , 
            '".$_POST['guardian_hand_phone']."' , '".$_POST['guardian_email']."' , '".$_POST['grandfath_name']."' , 
            '".$_POST['grandfath_alive']."' , '".$_POST['grandmoth_name']."' , '".$_POST['grandmoth_alive']."' , '".$_POST['update_time']."' , 
            '".$_SESSION['session_log_id']."' , '$student_sn') ";
    else     
	   $sql_update = "update stud_domicile set fath_name='".$_POST['fath_name']."',fath_birthyear='".$_POST['fath_birthyear']."',fath_alive='".$_POST['fath_alive']."',fath_relation='".$_POST['fath_relation']."',fath_p_id='".$_POST['fath_p_id']."',fath_education='".$_POST['fath_education']."',fath_occupation='".$_POST['fath_occupation']."',fath_unit='".$_POST['fath_unit']."',fath_work_name='".$_POST['fath_work_name']."',fath_phone='".$_POST['fath_phone']."',fath_home_phone='".$_POST['fath_home_phone']."',fath_hand_phone='".$_POST['fath_hand_phone']."',fath_email='".$_POST['fath_email']."',moth_name='".$_POST['moth_name']."',moth_birthyear='".$_POST['moth_birthyear']."',moth_alive='".$_POST['moth_alive']."',moth_relation='".$_POST['moth_relation']."',moth_p_id='".$_POST['moth_p_id']."',moth_education='".$_POST['moth_education']."',moth_occupation='".$_POST['moth_occupation']."',moth_unit='".$_POST['moth_unit']."',moth_work_name='".$_POST['moth_work_name']."',moth_phone='".$_POST['moth_phone']."',moth_home_phone='".$_POST['moth_home_phone']."',moth_hand_phone='".$_POST['moth_hand_phone']."',moth_email='".$_POST['moth_email']."',guardian_name='".$_POST['guardian_name']."',guardian_phone='".$_POST['guardian_phone']."',guardian_address='".$_POST['guardian_address']."',guardian_relation='".$_POST['guardian_relation']."',guardian_p_id='".$_POST['guardian_p_id']."',guardian_unit='".$_POST['guardian_unit']."',guardian_work_name='".$_POST['guardian_work_name']."',guardian_hand_phone='".$_POST['guardian_hand_phone']."',guardian_email='".$_POST['guardian_email']."',grandfath_name='".$_POST['grandfath_name']."',grandfath_alive='".$_POST['grandfath_alive']."',grandmoth_name='".$_POST['grandmoth_name']."',grandmoth_alive='".$_POST['grandmoth_alive']."',update_time='$update_time',update_id='".$_SESSION['session_log_id']."' where student_sn='$student_sn'";
	  
	$CONN->Execute($sql_update) or die($sql_update);
	//記錄 log
	sfs_log("stud_domicile","update",$_SESSION['session_log_id']);
	break;
}


?> 
<script language="JavaScript">
<!--
function same_father() {
	document.myform.guardian_name.value=document.myform.fath_name.value;
	document.myform.guardian_hand_phone.value=document.myform.fath_hand_phone.value;
	document.myform.guardian_phone.value=document.myform.fath_home_phone.value;
	document.myform.guardian_p_id.value=document.myform.fath_p_id.value;
	document.myform.guardian_unit.value=document.myform.fath_unit.value;
	document.myform.guardian_work_name.value=document.myform.fath_work_name.value;
	document.myform.guardian_email.value=document.myform.fath_email.value;
	document.myform.guardian_relation.value=document.myform.sex_f_hide.value;
	
}
function same_mother() {
	document.myform.guardian_name.value=document.myform.moth_name.value;
	document.myform.guardian_hand_phone.value=document.myform.moth_hand_phone.value;
	document.myform.guardian_phone.value=document.myform.moth_home_phone.value;
	document.myform.guardian_p_id.value=document.myform.moth_p_id.value;
	document.myform.guardian_unit.value=document.myform.moth_unit.value;
	document.myform.guardian_work_name.value=document.myform.moth_work_name.value;
	document.myform.guardian_email.value=document.myform.moth_email.value;
	document.myform.guardian_relation.value=document.myform.sex_m_hide.value ;
}
//-->
</script>

<table border="0" width="100%" cellspacing="0" cellpadding="0" CLASS="tableBg" >
<tr>
<td valign=top align="right">
<?php
	//顯示資料
	$sql_select = "select a.*, b.stud_name, b.stud_sex from stud_base b left join stud_domicile a on a.student_sn=b.student_sn where b.student_sn='$student_sn'";
	$recordSet = $CONN->Execute($sql_select);
while (!$recordSet->EOF) {
	$stud_name = $recordSet->fields["stud_name"];
	$stud_sex = $recordSet->fields["stud_sex"];
	$fath_name = $recordSet->fields["fath_name"];
	$fath_birthyear = $recordSet->fields["fath_birthyear"];
	$fath_alive = $recordSet->fields["fath_alive"];
	$fath_relation = $recordSet->fields["fath_relation"];
	$fath_p_id = $recordSet->fields["fath_p_id"];
	$fath_education = $recordSet->fields["fath_education"];
	$fath_occupation = $recordSet->fields["fath_occupation"];
	$fath_unit = $recordSet->fields["fath_unit"];
	$fath_work_name = $recordSet->fields["fath_work_name"];
	$fath_phone = $recordSet->fields["fath_phone"];
	$fath_home_phone = $recordSet->fields["fath_home_phone"];
	$fath_hand_phone = $recordSet->fields["fath_hand_phone"];
	$fath_email = $recordSet->fields["fath_email"];
	$moth_name = $recordSet->fields["moth_name"];
	$moth_birthyear = $recordSet->fields["moth_birthyear"];
	$moth_alive = $recordSet->fields["moth_alive"];
	$moth_relation = $recordSet->fields["moth_relation"];
	$moth_p_id = $recordSet->fields["moth_p_id"];
	$moth_education = $recordSet->fields["moth_education"];
	$moth_occupation = $recordSet->fields["moth_occupation"];
	$moth_unit = $recordSet->fields["moth_unit"];
	$moth_work_name = $recordSet->fields["moth_work_name"];
	$moth_phone = $recordSet->fields["moth_phone"];
	$moth_home_phone = $recordSet->fields["moth_home_phone"];
	$moth_hand_phone = $recordSet->fields["moth_hand_phone"];
	$moth_email = $recordSet->fields["moth_email"];
	$guardian_name = $recordSet->fields["guardian_name"];
	$guardian_phone = $recordSet->fields["guardian_phone"];
	$guardian_address = $recordSet->fields["guardian_address"];
	$guardian_relation = $recordSet->fields["guardian_relation"];
	$guardian_p_id = $recordSet->fields["guardian_p_id"];
	$guardian_unit = $recordSet->fields["guardian_unit"];
	$guardian_work_name = $recordSet->fields["guardian_work_name"];
	$guardian_hand_phone = $recordSet->fields["guardian_hand_phone"];
	$guardian_email = $recordSet->fields["guardian_email"];
	$grandfath_name = $recordSet->fields["grandfath_name"];
	$grandfath_alive = $recordSet->fields["grandfath_alive"];
	$grandmoth_name = $recordSet->fields["grandmoth_name"];
	$grandmoth_alive = $recordSet->fields["grandmoth_alive"];
	$update_time = $recordSet->fields["update_time"];
	$recordSet->MoveNext();
};
?>
    </td>
    <td width="100%" valign=top bgcolor="#CCCCCC">
    <form name ="myform" action="<?php echo $_SERVER['S'] ?>" method="post"> 
  <table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF"  width="100%" class=main_body >
<tr>
	<td class=title_mbody colspan=5 align=center >
		<?php echo $stud_name."  (學號：".$_SESSION['session_log_id'].")";?>
	</td>	
</tr>	


<tr>
	<td class=title_sbody1 nowrap ><?php echo $field_data[fath_name][d_field_cname] ?></td> 
	<td><input type="text" size="20" maxlength="20" name="fath_name" value="<?php echo $fath_name ?>"></td>
	<td class=title_sbody1 nowrap><?php echo $field_data[fath_alive][d_field_cname] ?></td> 
	<td><?php
		//存歿
		$sel1 = new drop_select(); //選單 		
		$sel1->s_name = "fath_alive"; //選單名稱
		$sel1->id = intval($fath_alive);
		$sel1->arr = is_live(); //內容陣列
		$sel1->do_select();		
    	?> </td>
</tr>

<tr>
	<td class=title_sbody1 nowrap><?php echo $field_data[fath_p_id][d_field_cname] ?></td> 
	<td><input type="text" size="20" maxlength="20" name="fath_p_id" value="<?php echo $fath_p_id ?>"></td>
	<td class=title_sbody1 nowrap><?php echo $field_data[fath_birthyear][d_field_cname] ?></td> 
	<td><input type="text" size="10" maxlength="10" name="fath_birthyear" value="<?php echo $fath_birthyear ?>"></td>
</tr>

<tr>
	<td class=title_sbody1 nowrap><?php echo $field_data[fath_relation][d_field_cname] ?></td> 
	<td><?php
		//與父關係
		$sel1 = new drop_select(); //選單
		$sel1->s_name = "fath_relation"; //選單名稱
		$sel1->id = intval($fath_relation);
		$sel1->arr = fath_relation(); //內容陣列		
		$sel1->do_select();		
    	?> </td>
	<td class=title_sbody1 nowrap><?php echo $field_data[fath_education][d_field_cname] ?></td>
	<td><?php
		//學歷
		$sel1 = new drop_select(); //選單 		
		$sel1->s_name = "fath_education"; //選單名稱
		$sel1->id = intval($fath_education);
		$sel1->arr = edu_kind(); //內容陣列		
		$sel1->do_select();		
    	?> </td>
</tr>

<tr>
	<td class=title_sbody1 nowrap><?php echo $field_data[fath_occupation][d_field_cname] ?></td> 
	<td><input type="text" size="20" maxlength="20" name="fath_occupation" value="<?php echo $fath_occupation ?>"></td>
	<td class=title_sbody1 nowrap><?php echo $field_data[fath_unit][d_field_cname] ?></td> 
	<td><input type="text" size="20" maxlength="20" name="fath_unit" value="<?php echo $fath_unit ?>"></td>
</tr>

<tr>
	<td class=title_sbody1 nowrap><?php echo $field_data[fath_work_name][d_field_cname] ?></td> 
	<td><input type="text" size="20" maxlength="20" name="fath_work_name" value="<?php echo $fath_work_name ?>"></td>
	<td class=title_sbody1 nowrap><?php echo $field_data[fath_hand_phone][d_field_cname] ?></td> 
	<td><input type="text" size="20" maxlength="20" name="fath_hand_phone" value="<?php echo $fath_hand_phone ?>"></td>
</tr>

<tr>
	<td class=title_sbody1 nowrap> <?php echo $field_data[fath_home_phone][d_field_cname] ?></td> 
	<td><input type="text" size="20" maxlength="20" name="fath_home_phone" value="<?php echo $fath_home_phone ?>"></td>
	<td class=title_sbody1 nowrap><?php echo $field_data[fath_phone][d_field_cname] ?></td> 
	<td><input type="text" size="20" maxlength="20" name="fath_phone" value="<?php echo $fath_phone ?>"></td>
</tr>

<tr>
	<td class=title_sbody1 nowrap><?php echo $field_data[fath_email][d_field_cname] ?></td>
	<td colspan="3"><input type="text" size="30" maxlength="30" name="fath_email" value="<?php echo $fath_email ?>"></td>
	
</tr>

<tr>
	<td class=title_sbody2 nowrap><?php echo $field_data[moth_name][d_field_cname] ?></td>
	<td><input type="text" size="20" maxlength="20" name="moth_name" value="<?php echo $moth_name ?>"></td>
	<td class=title_sbody2 nowrap><?php echo $field_data[moth_alive][d_field_cname] ?></td> 
	<td><?php
		//存歿
		$sel1 = new drop_select(); //選單		
		$sel1->s_name = "moth_alive"; //選單名稱
		$sel1->id = intval($moth_alive);
		$sel1->arr = is_live(); //內容陣列		
		$sel1->do_select();
    	?>
	</td>
</tr>

<tr>
	<td class=title_sbody2  nowrap><?php echo $field_data[moth_p_id][d_field_cname] ?></td> 
	<td><input type="text" size="20" maxlength="20" name="moth_p_id" value="<?php echo $moth_p_id ?>"></td>
	<td class=title_sbody2 nowrap><?php echo $field_data[moth_birthyear][d_field_cname] ?></td> 
	<td><input type="text" size="10" maxlength="10" name="moth_birthyear" value="<?php echo $moth_birthyear ?>"></td>
</tr>

<tr>
	<td class=title_sbody2  nowrap><?php echo $field_data[moth_relation][d_field_cname] ?></td> 
	<td><?php
		//與母關係
		$sel1 = new drop_select(); //選單
		$sel1->s_name = "moth_relation"; //選單名稱
		$sel1->id = intval($moth_relation);
		$sel1->arr = moth_relation(); //內容陣列		
		$sel1->do_select();		
    	?> </td>
	<td class=title_sbody2  nowrap><?php echo $field_data[moth_education][d_field_cname] ?></td> 
	<td><?php
		//學歷 
		$sel1 = new drop_select(); //選單
		$sel1->s_name = "moth_education"; //選單名稱
		$sel1->id = intval($moth_education);
		$sel1->arr = edu_kind(); //內容陣列		
		$sel1->do_select();		
    	?> </td>
</tr>



<tr>
	<td class=title_sbody2 nowrap><?php echo $field_data[moth_occupation][d_field_cname] ?></td> 
	<td><input type="text" size="20" maxlength="20" name="moth_occupation" value="<?php echo $moth_occupation ?>"></td>
	<td class=title_sbody2 nowrap><?php echo $field_data[moth_unit][d_field_cname] ?></td> 
	<td><input type="text" size="20" maxlength="20" name="moth_unit" value="<?php echo $moth_unit ?>"></td>
</tr>

<tr>
	<td class=title_sbody2 nowrap><?php echo $field_data[moth_work_name][d_field_cname] ?></td>
	<td><input type="text" size="20" maxlength="20" name="moth_work_name" value="<?php echo $moth_work_name ?>"></td>
	<td class=title_sbody2 nowrap><?php echo $field_data[moth_hand_phone][d_field_cname] ?></td> 
	<td><input type="text" size="20" maxlength="20" name="moth_hand_phone" value="<?php echo $moth_hand_phone ?>"></td>
</tr>

<tr>
	<td class=title_sbody2 nowrap><?php echo $field_data[moth_home_phone][d_field_cname] ?></td> 
	<td><input type="text" size="20" maxlength="20" name="moth_home_phone" value="<?php echo $moth_home_phone ?>"></td>
	<td class=title_sbody2 nowrap><?php echo $field_data[moth_phone][d_field_cname] ?></td> 
	<td><input type="text" size="20" maxlength="20" name="moth_phone" value="<?php echo $moth_phone ?>"></td>
</tr>

<tr>
	<td class=title_sbody2 nowrap><?php echo $field_data[moth_email][d_field_cname] ?></td> 
	<td  colspan="3" ><input type="text" size="30" maxlength="30" name="moth_email" value="<?php echo $moth_email ?>"></td>
</tr>


<tr>
	<td class=title_sbody1 nowrap><?php echo $field_data[guardian_name][d_field_cname] ?></td> 
	<td colspan="3" ><input type="text" size="20" maxlength="20" name="guardian_name" value="<?php echo $guardian_name ?>">
	<input type="button" name="same_key" value="與生父同" onclick="same_father()" >&nbsp;<input type="button" name="same_key" value="與生母同"  onclick="same_mother()" >
	</td>
		
</tr>

<tr>
	<td class=title_sbody1 nowrap><?php echo $field_data[guardian_p_id][d_field_cname] ?></td> 
	<td><input type="text" size="20" maxlength="20" name="guardian_p_id" value="<?php echo $guardian_p_id ?>"></td>
	<td class=title_sbody1 nowrap><?php echo $field_data[guardian_relation][d_field_cname] ?></td> 
	<td><?php
		//與監護人關係
		$sel1 = new drop_select(); //選單
		$sel1->s_name = "guardian_relation"; //選單名稱
		$sel1->id = intval($guardian_relation);
		$sel1->arr = guardian_relation(); //內容陣列		
		$sel1->do_select();		
    	?>
	
	</td>
</tr>

<tr>
	<td class=title_sbody1 nowrap><?php echo $field_data[guardian_unit][d_field_cname] ?></td> 
	<td><input type="text" size="20" maxlength="30" name="guardian_unit" value="<?php echo $guardian_unit ?>"></td>
	<td class=title_sbody1 nowrap><?php echo $field_data[guardian_work_name][d_field_cname] ?></td> 
	<td><input type="text" size="20" maxlength="20" name="guardian_work_name" value="<?php echo $guardian_work_name ?>"></td>
</tr>

<tr>
	<td class=title_sbody1 nowrap><?php echo $field_data[guardian_phone][d_field_cname] ?></td> 
	<td><input type="text" size="20" maxlength="20" name="guardian_phone" value="<?php echo $guardian_phone ?>"></td>
	<td class=title_sbody1 nowrap><?php echo $field_data[guardian_hand_phone][d_field_cname] ?></td>
	<td><input type="text" size="20" maxlength="20" name="guardian_hand_phone" value="<?php echo $guardian_hand_phone ?>"></td>
</tr>

<tr>
	<td class=title_sbody1 nowrap><?php echo $field_data[guardian_address][d_field_cname] ?></td> 
	<td><input type="text" size="30" maxlength="60" name="guardian_address" value="<?php echo $guardian_address ?>"></td>
	<td class=title_sbody1 nowrap><?php echo $field_data[guardian_email][d_field_cname] ?></td> 
	<td><input type="text" size="20" maxlength="30" name="guardian_email" value="<?php echo $guardian_email ?>"></td>
</tr>


<tr>
	<td class=title_sbody2  nowrap><?php echo $field_data[grandfath_name][d_field_cname] ?></td> 
	<td><input type="text" size="20" maxlength="20" name="grandfath_name" value="<?php echo $grandfath_name ?>"></td>
	<td class=title_sbody2  nowrap><?php echo $field_data[grandfath_alive][d_field_cname] ?></td> 
	<td><?php
		//存歿
		$sel1 = new drop_select(); //選單
		$sel1->s_name = "grandfath_alive"; //選單名稱
		$sel1->id = intval($grandfath_alive);
		$sel1->arr = is_live(); //內容陣列		
		$sel1->do_select();		
    	?></td>
</tr>

<tr>
	<td class=title_sbody2  nowrap><?php echo $field_data[grandmoth_name][d_field_cname] ?></td> 
	<td><input type="text" size="20" maxlength="20" name="grandmoth_name" value="<?php echo $grandmoth_name ?>"></td>
	<td class=title_sbody2  nowrap><?php echo $field_data[grandmoth_alive][d_field_cname] ?></td>
	<td><?php
		//存歿
		$sel1 = new drop_select(); //選單
		$sel1->s_name = "grandmoth_alive"; //選單名稱
		$sel1->id = intval($grandmoth_alive);
		$sel1->arr = is_live(); //內容陣列				
		$sel1->do_select();	
    	?></td>
</tr>
<tr>
<td class=title_mbody colspan=5 align=center >
	<?php 
	    	echo "<input type=submit name=do_key value =\"$editBtn\" onClick=\"return checkok();\">&nbsp;&nbsp;";
    ?>
	</td>	
</tr>

</table>    
</form>
    </td>
  </tr>
</table>
<?php
//印出檔頭
foot();
?>
