<?php

// $Id: stud_list_self.php 7404 2013-08-05 06:37:34Z infodaes $

// 載入設定檔
include "config.php";
include "../../include/sfs_oo_date.php";

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
$field_data = get_field_info("stud_base");

//模組選單
print_menu($menu_p,$linkstr);

//檢查是否開放編輯基本資料
if (!$base_edit){
   echo "目前不開放編輯基本資料！";
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
		$stud_study_year=$res->fields['stud_study_year'];
	}
}

//如果在籍才繼續處理
if ($student_sn) {

// 檢查 php.ini 是否打開 file_uploads ?
check_phpini_upload();

//按鍵處理 
switch ($_POST['do_key']){	
	case $editBtn: //修改
		if ($same_key) {
			$ttt = change_addr_str($_POST['stud_addr_1']);
			$stud_addr_a = $ttt[0];
			$stud_addr_b = $ttt[1];
			$stud_addr_c = $ttt[2];
			$stud_addr_d = $ttt[3];
			$stud_addr_e = $ttt[4];
			$stud_addr_f = $ttt[5];
			$stud_addr_g = $ttt[6];
			$stud_addr_h = $ttt[7];
			$stud_addr_i = $ttt[8];
			$stud_addr_j = $ttt[9];
			$stud_addr_k = $ttt[10];
			$stud_addr_l = $ttt[11];
		}

		$stud_kind_temp =",";
		while(list($tid,$tname)=each($_POST['stud_kind'])) $stud_kind_temp .= $tname.",";
		$sql_update = "update stud_base set stud_name_eng='{$_POST['stud_name_eng']}',stud_sex='{$_POST['stud_sex']}',stud_birthday='{$_POST['stud_birthday']}',stud_blood_type='{$_POST['stud_blood_type']}',stud_birth_place='{$_POST['stud_birth_place']}',stud_kind='$stud_kind_temp',stud_country='{$_POST['stud_country']}',stud_country_kind='{$_POST['stud_country_kind']}',stud_person_id='{$_POST['stud_person_id']}',stud_country_name='{$_POST['stud_country_name']}',stud_addr_1='{$_POST['stud_addr_1']}',stud_addr_2='{$_POST['stud_addr_2']}',stud_tel_1='{$_POST['stud_tel_1']}',stud_tel_2='{$_POST['stud_tel_2']}',stud_tel_3='{$_POST['stud_tel_3']}',stud_mail='{$_POST['stud_mail']}',stud_addr_a='$stud_addr_a',stud_addr_b='stud_addr_b',stud_addr_c='$stud_addr_c',stud_addr_d='$stud_addr_d',stud_addr_e='$stud_addr_e',stud_addr_f='$stud_addr_f',stud_addr_g='$stud_addr_g',stud_addr_h='$stud_addr_h',stud_addr_i='$stud_addr_i',stud_addr_j='$stud_addr_j',stud_addr_k='$stud_addr_k',stud_addr_l='$stud_addr_l',stud_addr_m='$stud_addr_m',stud_class_kind='{$_POST['stud_class_kind']}',stud_spe_kind='{$_POST['stud_spe_kind']}',stud_spe_class_kind='{$_POST['stud_spe_class_kind']}',stud_spe_class_id='{$_POST['stud_spe_class_id']}',stud_preschool_status='{$_POST['stud_preschool_status']}',stud_preschool_id='{$_POST['stud_preschool_id']}',stud_preschool_name='{$_POST['stud_preschool_name']}',stud_mschool_status='{$_POST['stud_mschool_status']}',stud_mschool_id='{$_POST['stud_mschool_id']}',stud_mschool_name='{$_POST['stud_mschool_name']}',addr_zip='{$_POST['addr_zip']}' where student_sn='$student_sn'";
		$CONN->Execute($sql_update) or die($sql_update);

		$upload_str = set_upload_path("$img_path/$stud_study_year");
		
		//圖檔處理
		if($_FILES['stud_img']['tmp_name']){
			//設定上傳檔案路徑	
		 	copy($_FILES['stud_img']['tmp_name'], $upload_str."/".$_SESSION['session_log_id']);
		 }
		 else if ($_POST['del_img']) {
		 	if (file_exists($upload_str."/".$_SESSION['session_log_id']))
				unlink($upload_str."/".$_SESSION['session_log_id']);
		 } 
		//記錄 log
		sfs_log("stud_base","update",$_SESSION['session_log_id']);
	
	break;
}

//顯示資料
$sql_select = "select * from stud_base where student_sn='$student_sn' ";	
$recordSet = $CONN->Execute($sql_select);
while (!$recordSet->EOF) {
	$stud_name = $recordSet->fields["stud_name"];
	$stud_name_eng=$recordSet->fields["stud_name_eng"];
	$stud_sex = $recordSet->fields["stud_sex"];
	$stud_birthday = $recordSet->fields["stud_birthday"];
	$stud_blood_type = $recordSet->fields["stud_blood_type"];
	$stud_birth_place = $recordSet->fields["stud_birth_place"];
	$stud_kind = $recordSet->fields["stud_kind"];
	$stud_country = $recordSet->fields["stud_country"];
	$stud_country_kind = $recordSet->fields["stud_country_kind"];
	$stud_person_id = $recordSet->fields["stud_person_id"];
	$stud_country_name = $recordSet->fields["stud_country_name"];
	$stud_addr_1 = $recordSet->fields["stud_addr_1"];
	$stud_addr_2 = $recordSet->fields["stud_addr_2"];
	$stud_tel_1 = $recordSet->fields["stud_tel_1"];
	$stud_tel_2 = $recordSet->fields["stud_tel_2"];
	$stud_tel_3 = $recordSet->fields["stud_tel_3"];
	$stud_mail = $recordSet->fields["stud_mail"];
	$stud_addr_a = $recordSet->fields["stud_addr_a"];
	$stud_addr_b = $recordSet->fields["stud_addr_b"];
	$stud_addr_c = $recordSet->fields["stud_addr_c"];
	$stud_addr_d = $recordSet->fields["stud_addr_d"];
	$stud_addr_e = $recordSet->fields["stud_addr_e"];
	$stud_addr_f = $recordSet->fields["stud_addr_f"];
	$stud_addr_g = $recordSet->fields["stud_addr_g"];
	$stud_addr_h = $recordSet->fields["stud_addr_h"];
	$stud_addr_i = $recordSet->fields["stud_addr_i"];
	$stud_addr_j = $recordSet->fields["stud_addr_j"];
	$stud_addr_k = $recordSet->fields["stud_addr_k"];
	$stud_addr_l = $recordSet->fields["stud_addr_l"];
	$stud_addr_m = $recordSet->fields["stud_addr_m"];
	$stud_class_kind = $recordSet->fields["stud_class_kind"];
	$stud_spe_kind = $recordSet->fields["stud_spe_kind"];
	$stud_spe_class_kind = $recordSet->fields["stud_spe_class_kind"];
	$stud_spe_class_id = $recordSet->fields["stud_spe_class_id"];
	$stud_preschool_status = $recordSet->fields["stud_preschool_status"];
	$stud_preschool_id = $recordSet->fields["stud_preschool_id"];
	$stud_preschool_name = $recordSet->fields["stud_preschool_name"];
	$stud_mschool_status = $recordSet->fields["stud_mschool_status"];
	$stud_mschool_id = $recordSet->fields["stud_mschool_id"];
	$stud_mschool_name = $recordSet->fields["stud_mschool_name"];
	$stud_study_year = $recordSet->fields["stud_study_year"];
	$curr_class_num = $recordSet->fields["curr_class_num"];
	$stud_study_cond = $recordSet->fields["stud_study_cond"];
	$addr_zip = $recordSet->fields["addr_zip"];
	$recordSet->MoveNext();
};

// 日期函式
$seldate = new date_class("myform");
$seldate->demo ="none";

//日期檢查javascript 函式
$seldate->date_javascript();

//生日
$stud_birthday_str = $seldate->date_add("stud_birthday",$stud_birthday);

$seldate->do_check();
?>

<script language="JavaScript">
<!--
function checkok() {
	return date_check();
}

function do_same(){
	document.myform.stud_addr_2.value=document.myform.stud_addr_1.value;
}
//-->
</script>

<table border="0" width="100%" cellspacing="0" cellpadding="0" CLASS="tableBg" >
<tr>
<td  valign="top" width="100%" >   
<form name="myform" action="<?php echo $_SERVER['SCRIPT_NAME'] ?>" method="post" encType="multipart/form-data">
<table border="1" cellpadding="2" cellspacing="0"  bordercolorlight="#333354" bordercolordark="#FFFFFF" class="main_body" width="100%">
<tr>
	<td class=title_mbody colspan=5 align=center >
		<?php echo $stud_name."  (學號：".$_SESSION['session_log_id'].")";?>
	</td>	
</tr>	
<tr>
<td align="right" CLASS="title_sbody1" nowrap>英文姓名</td>
	<td colspan="4" >
	<input type="text" size="40" maxlength="60" name="stud_name_eng" value="<?php echo $stud_name_eng ?>"><br>
</tr>
<tr>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[stud_sex][d_field_cname] ?></td>
	<td >
	<?php  
    	//性別 
    	$temp1="";
		$temp2=""; 
    	if($stud_sex == 1 ){ 
    		$temp1="checked "; $temp2=""; 
    	} 
    	else if($stud_sex == 2){ 
    		$temp1=""; $temp2="checked "; 
    	}
	?> 
	<input type="radio" name="stud_sex" value="1" <?php echo $temp1 ?>>男 &nbsp;&nbsp;<input type="radio" name="stud_sex" value="2" <?php echo $temp2 ?>>女 
	</td>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[stud_birthday][d_field_cname] ?></td>
	<td ><input type="text" name="stud_birthday" size="8" value="<?php echo $stud_birthday ?>">格式 yyyy-mm-dd</td>
    <td width="20%" height="83" rowspan="4">
    <table border=0 cellpadding=0 cellspacing=0 width=100%  >
    	<tr><td height=80% align=center>
    	<input type="hidden" name="stud_study_year" value="<?php echo $stud_study_year ?>"> 
    	<?php 
    	//印出照片
    		$img =$stud_study_year."/".$_SESSION['session_log_id'];    		
    		if (file_exists($UPLOAD_PATH."$img_path/".$img)) {
    			echo "<img src=\"".$UPLOAD_URL."$img_path/$img\" width=\"$img_width\">";
				echo "<br><font size=2><input type=checkbox name=\"del_img\" value=\"1\"> 刪除圖檔</font>";
			}
    	?>
    	</td></tr>
    	<tr><td height=20% valign=bottom>
    	<font size=2>照片</font><input type="file" size=10 name="stud_img" >
    	</td></tr></table>
    </td>
	
  </tr>
  <tr>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[stud_blood_type][d_field_cname] ?></td>
	<td >
	<?php
		//顯示血型
		$sel1 = new drop_select(); //選單類別
		$sel1->s_name = "stud_blood_type"; //選單名稱
		$sel1->id = intval($stud_blood_type);
		$sel1->arr = blood(); //內容陣列
		$sel1->do_select();
	?>	
	</td>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[stud_birth_place][d_field_cname] ?></td>
	<td >
	<?php
    	//出生地陣列 
    	$sel1 = new drop_select(); //選單類別
    	$sel1->s_name = "stud_birth_place"; //選單名稱
		$sel1->id = intval($stud_birth_place);
		$sel1->arr = birth_state(); //內容陣列
		$sel1->do_select();	
    ?>
	</td>
  </tr>
  <tr>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[stud_country][d_field_cname] ?></td>
	<td ><input type="text" size="20" maxlength="20" name="stud_country" value="<?php echo ($stud_country=="")?$default_country:$stud_country  ?>"></td>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[stud_country_kind][d_field_cname] ?></td>
	<td >
	<?php
    	//證照種類 
    	$sel1 = new drop_select(); //選單類別
    	$sel1->s_name = "stud_country_kind"; //選單名稱
		$sel1->id = intval($stud_country_kind);
		$sel1->has_empty = false;
		$sel1->arr = stud_country_kind(); //內容陣列
		$sel1->do_select();	
    ?>
	</td>
  </tr>
  <tr>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[stud_person_id][d_field_cname] ?></td>
	<td ><input type="text" size="20" maxlength="20" name="stud_person_id" value="<?php echo $stud_person_id ?>"></td>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[stud_country_name][d_field_cname] ?></td>
	<td ><input type="text" size="20" maxlength="20" name="stud_country_name" value="<?php echo $stud_country_name ?>"></td>
  </tr>
<tr>
  	<td align="right" CLASS="title_sbody1" nowrap>住址</td>
	<td   colspan="4" >
	<?php echo $field_data[stud_addr_1][d_field_cname] ?>:
	<input type="text" size="40" maxlength="60" name="stud_addr_1" value="<?php echo $stud_addr_1 ?>"><br>
	<?php echo $field_data[stud_addr_2][d_field_cname] ?>:
	<input type="text" size="40" maxlength="60" name="stud_addr_2" value="<?php echo $stud_addr_2 ?>">
	<input type="text" size="3" maxlength="3" name="addr_zip" value="<?php echo $addr_zip ?>" title="填入郵遞區號">
	<?php
	 if ($stud_addr_1 == $stud_addr_2)
	 	$disable_str = " disabled ";
	 ?>
	<input type="button" name="same_addr" value="<?php echo $sameBtn ?>" <?php echo $disable_str ?> onclick="do_same()">
</tr>
<tr>
<td   colspan="5" >
	<!-- 中輟時戶籍地址 -->
	中輟時戶籍地址 &nbsp;&nbsp;&nbsp;<input type="checkbox" name="same_key" value="1"><b><?php echo $sameBtn ?></b>
	<BR>
	<table>
	<tr>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[stud_addr_a][d_field_cname] ?></td>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[stud_addr_b][d_field_cname] ?></td>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[stud_addr_c][d_field_cname] ?></td>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[stud_addr_d][d_field_cname] ?></td>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[stud_addr_e][d_field_cname] ?></td>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[stud_addr_f][d_field_cname] ?></td>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[stud_addr_g][d_field_cname] ?></td>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[stud_addr_h][d_field_cname] ?></td>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[stud_addr_i][d_field_cname] ?></td>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[stud_addr_j][d_field_cname] ?></td>	
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[stud_addr_k][d_field_cname] ?></td>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[stud_addr_l][d_field_cname] ?></td>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[stud_addr_m][d_field_cname] ?></td>
	</tr>
	<tr>
	<td ><input type="text" size="5" maxlength="6" name="stud_addr_a" value="<?php echo $stud_addr_a ?>"></td>
	<td ><input type="text" size="5" maxlength="12" name="stud_addr_b" value="<?php echo $stud_addr_b ?>"></td>
	<td ><input type="text" size="5" maxlength="12" name="stud_addr_c" value="<?php echo $stud_addr_c ?>"></td>	
	<td ><input type="text" size="5" maxlength="6" name="stud_addr_d" value="<?php echo $stud_addr_d ?>"></td>
	<td ><input type="text" size="5" maxlength="20" name="stud_addr_e" value="<?php echo $stud_addr_e ?>"></td>
	<td ><input type="text" size="3" maxlength="6" name="stud_addr_f" value="<?php echo $stud_addr_f ?>"></td>	
	<td ><input type="text" size="5" maxlength="8" name="stud_addr_g" value="<?php echo $stud_addr_g ?>"></td>
	<td ><input type="text" size="3" maxlength="6" name="stud_addr_h" value="<?php echo $stud_addr_h ?>"></td>
	<td ><input type="text" size="5" maxlength="8" name="stud_addr_i" value="<?php echo $stud_addr_i ?>"></td>
	<td ><input type="text" size="5" maxlength="8" name="stud_addr_j" value="<?php echo $stud_addr_j ?>"></td>	
	<td ><input type="text" size="3" maxlength="6" name="stud_addr_k" value="<?php echo $stud_addr_k ?>"></td>
	<td ><input type="text" size="3" maxlength="6" name="stud_addr_l" value="<?php echo $stud_addr_l ?>"></td>
	<td ><input type="text" size="5" maxlength="12" name="stud_addr_m" value="<?php echo $stud_addr_m ?>"></td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td colspan="5" >
	<?php echo $field_data[stud_tel_1][d_field_cname] ?>:
	<input type="text" size="10" maxlength="20" name="stud_tel_1" value="<?php echo $stud_tel_1 ?>">&nbsp;
	<?php echo $field_data[stud_tel_2][d_field_cname] ?>:
	<input type="text" size="10" maxlength="20" name="stud_tel_2" value="<?php echo $stud_tel_2 ?>">&nbsp;
	<?php echo $field_data[stud_tel_3][d_field_cname] ?>:
	<input type="text" size="10" maxlength="20" name="stud_tel_3" value="<?php echo $stud_tel_3 ?>">&nbsp;
	<br>
	<?php echo $field_data[stud_mail][d_field_cname] ?>:
	<input type="text" size="30" maxlength="50" name="stud_mail" value="<?php echo $stud_mail ?>">
	</td>
</tr>
<tr>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[stud_kind][d_field_cname] ?></td>
	<td  colspan="4">
	<?php  
	//學生身分別
		$sel1 = new checkbox_class(); //選單類別		
		$sel1->s_name = "stud_kind"; //選單名稱
		$sel1->id = $stud_kind;
		$sel1->arr = stud_kind(); //內容陣列	
		$sel1->css = "main_body";
		$sel1->is_color =true;
		$sel1->do_select();
	?>	
	</td>
  </tr>
  <tr>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[stud_class_kind][d_field_cname] ?></td>
	<td >
	<?php  
	//班級性質
		$sel1 = new drop_select(); //選單類別	
		$sel1->s_name = "stud_class_kind"; //選單名稱
		$sel1->id = intval($stud_class_kind);
		$sel1->arr = stud_class_kind(); //內容陣列
		$sel1->has_empty =false;
		$sel1->do_select();	  
	?>		
	</td>
	<td align="right" CLASS="title_sbody1"  nowrap><?php echo $field_data[stud_spe_kind][d_field_cname] ?></td>
	<td  colspan="2">
	<?php 
	//特殊班類別
		$sel1 = new drop_select(); //選單類別		
		$sel1->s_name = "stud_spe_kind"; //選單名稱
		$sel1->id = intval($stud_spe_kind);
		$sel1->arr = stud_spe_kind(); //內容陣列
		$sel1->do_select();	  
	 ?>	
	</td>
  </tr>
  <tr>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[stud_spe_class_kind][d_field_cname] ?></td>
	<td >
	<?php 
	//特殊班班別 
		$sel1 = new drop_select(); //選單類別		
		$sel1->s_name = "stud_spe_class_kind"; //選單名稱
		$sel1->id = intval($stud_spe_class_kind);
		$sel1->arr = stud_spe_class_kind(); //內容陣列
		$sel1->do_select();
	 ?>	
	</td>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[stud_spe_class_id][d_field_cname] ?></td>
	<td  colspan="2">
	<?php 
	//特殊班上課性質 
		$sel1 = new drop_select(); //選單類別		
		$sel1->s_name = "stud_spe_class_id"; //選單名稱
		$sel1->id = intval($stud_spe_class_id);
		$sel1->arr = stud_spe_class_id(); //內容陣列
		$sel1->do_select();
	 ?>	
	</td>
  </tr>
  <tr>
	<td align="right" CLASS="title_sbody1" nowrap>入學前幼稚園</td>
	<td  colspan="4">
	入學資格:
	<?php 
	//入學資格
		$sel1 = new drop_select(); //選單類別	
		$sel1->s_name = "stud_preschool_status"; //選單名稱
		$sel1->id = intval($stud_preschool_status);
		$sel1->arr = stud_preschool_status(); //內容陣列	
		$sel1->do_select();
	 ?>
	&nbsp;幼稚園學校代號:<input type="text" size="4" maxlength="8" name="stud_preschool_id" value="<?php echo $stud_preschool_id ?>"> &nbsp;
	幼稚園名稱:<input type="text" size="15" maxlength="40" name="stud_preschool_name" value="<?php echo $stud_preschool_name ?>">
	</td>
  </tr>
  <tr>
	<td align="right" CLASS="title_sbody1" nowrap>入學前國小</td>
	<td  colspan="4">
	入學資格:
	<?php 
	//入學資格
		$sel1 = new drop_select(); //選單類別	
		$sel1->s_name = "stud_mschool_status"; //選單名稱
		$sel1->id = intval($stud_mschool_status);
		$sel1->arr = stud_preschool_status(); //內容陣列	
		$sel1->do_select();
	 ?>	
	&nbsp;國小學校代號:<input type="text" size="4" maxlength="8" name="stud_mschool_id" value="<?php echo $stud_mschool_id ?>"> &nbsp;
	國小名稱:<input type="text" size="15" maxlength="40" name="stud_mschool_name" value="<?php echo $stud_mschool_name ?>">
	</td>
  </tr>
  <tr>
  	<td class=title_mbody colspan=5 align=center >
  		<?php
    		if($base_edit=='1')	echo "<input type=submit name=do_key value =\"$editBtn\" onClick=\"return checkok();\">&nbsp;&nbsp;"; else echo "<font size=1 color='red'>- 僅可瀏覽，禁止更改 -</font>";
    	?>
  	</td>	
  </tr>
</table>
</form>
</table>
</td>
</tr>
</table>
<?php
} else {
	echo "該生已不在籍！";
}

//印出尾頭
foot();
?>
