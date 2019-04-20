<?php

// $Id: stud_list.php 8165 2014-10-08 15:11:08Z infodaes $

// 載入設定檔
include "stud_reg_config.php";

// 認證檢查
sfs_check();

// 不需要 register_globals
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}
if ($stud_person_id) {
	$stud_person_id = strtoupper($stud_person_id) ;
	$edu_key =  hash('sha256', strtoupper($stud_person_id));
}
//印出檔頭
head();
//欄位資訊
$field_data = get_field_info("stud_base");
//選單連結字串
$linkstr = "student_sn=$student_sn&c_curr_class=$c_curr_class&c_curr_seme=$c_curr_seme";
//模組選單
print_menu($menu_p,$linkstr);

//$dd=explode("-",$stud_birthday);
//$stud_birthday=sprintf("%04d-%02d-%02d",$dd[0]+1911,$dd[1],$dd[2]);

//取得任教班級代號
$class_num = get_teach_class();
if ($class_num == '') {
	head("權限錯誤");
	stud_class_err();
	foot();
	exit;
}
$curr_seme = curr_year().curr_seme(); //現在學年學期
	
switch ($do_key){	
	case $editBtn: //修改
	if ($same_key) {
		$addr = $stud_addr_1;
		$ttt = change_addr_str($addr);
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
		while(list($tid,$tname)=each($stud_kind))
			$stud_kind_temp .= $tname.",";
		$temp_num =sprintf("%s%02d",substr($curr_class_num,0,3),$sit_num);
		$sql_update = "update stud_base set stud_name_eng='$stud_name_eng',stud_sex=$stud_sex,stud_birthday='$stud_birthday',
		stud_blood_type='$stud_blood_type',stud_birth_place='$stud_birth_place',stud_kind='$stud_kind_temp',
		stud_country='$stud_country',stud_country_kind='$stud_country_kind',stud_person_id='$stud_person_id',
		stud_country_name='$stud_country_name',addr_move_in='$addr_move_in',stud_addr_1='$stud_addr_1',
		stud_addr_2='$stud_addr_2',stud_tel_1='$stud_tel_1',stud_tel_2='$stud_tel_2',stud_tel_3='$stud_tel_3',
		stud_mail='$stud_mail',stud_addr_a='$stud_addr_a',stud_addr_b='$stud_addr_b',stud_addr_c='$stud_addr_c',
		stud_addr_d='$stud_addr_d',stud_addr_e='$stud_addr_e',stud_addr_f='$stud_addr_f',stud_addr_g='$stud_addr_g',
		stud_addr_h='$stud_addr_h',stud_addr_i='$stud_addr_i',stud_addr_j='$stud_addr_j',stud_addr_k='$stud_addr_k',
		stud_addr_l='$stud_addr_l',stud_addr_m='$stud_addr_m',stud_class_kind='$stud_class_kind',stud_spe_kind='$stud_spe_kind',
		stud_spe_class_kind='$stud_spe_class_kind',stud_spe_class_id='$stud_spe_class_id',
		stud_preschool_status='$stud_preschool_status',stud_preschool_id='$stud_preschool_id',
		stud_preschool_name='$stud_preschool_name',stud_Mschool_status='$stud_Mschool_status',
		stud_mschool_id='$stud_mschool_id',stud_mschool_name='$stud_mschool_name',curr_class_num='$temp_num',
		enroll_school='$enroll_school' ,edu_key='$edu_key',obtain='$obtain',safeguard='$safeguard' where student_sn='$student_sn'";
		$sql_update=str_replace("addr_move_in=''","addr_move_in=NULL",$sql_update);
		$CONN->Execute($sql_update) or die($CONN->ErrorMsg());
		$c_curr_class=substr($curr_class_num,0,3);
		$CONN->Execute("update stud_seme set seme_num='$sit_num' where seme_year_seme ='$c_curr_seme' and  seme_class ='$c_curr_class' and student_sn='$student_sn'");
		$upload_str = set_upload_path("$img_path/$stud_study_year");
		$query="select * from stud_base where student_sn='$student_sn'";
		$res=$CONN->Execute($query);
		$stud_id=$res->fields['stud_id'];
		
		//圖檔處理
		if($_FILES['stud_img']['tmp_name']){
			//設定上傳檔案路徑	
		 	copy($_FILES['stud_img']['tmp_name'], $upload_str."/".$stud_id);
		 }
		 else if ($del_img) {
		 	if (file_exists($upload_str."/$stud_id"))
				unlink($upload_str."/$stud_id");
		 } 		
		//記錄 log
		sfs_log("stud_base","update","$stud_id");
	break;	
}

$c_curr_seme = sprintf ("%03d%d",curr_year(),curr_seme()); //現在學年學期
//更改學期
if ($c_curr_seme != "")
	$curr_seme = $c_curr_seme;

//儲存後到下一筆
if ($chknext) $student_sn = $nav_next;	
$query = "select a.student_sn,a.stud_name from stud_base a,stud_seme b where a.student_sn=b.student_sn and a.student_sn='$student_sn' and a.stud_study_cond=0 and b.seme_year_seme='$c_curr_seme' and b.seme_class='$class_num'";
$res = $CONN->Execute($query) or die($res->ErrorMsg());
//未設定或改變在職狀況或刪除記錄後 到第一筆
if ($student_sn =="" || $res->RecordCount()==0) {	
	$temp_sql = "select a.student_sn,a.stud_name from stud_base a,stud_seme b where a.student_sn=b.student_sn and a.stud_study_cond=0 and b.seme_year_seme='$c_curr_seme' and b.seme_class='$class_num' order by b.seme_num";
	$res = $CONN->Execute($temp_sql) or die($temp_sql);
	$student_sn = $res->fields[0];
}
$stud_name = $res->fields[1];



?> 
<table border="0" width="100%" cellspacing="0" cellpadding="0" CLASS="tableBg" >
<tr>
<td valign=top align="right">
<?php
//建立左邊選單   
	
	$temparr = class_base();   
	$upstr = $temparr[$class_num]; 	
	$grid1 = new ado_grid_menu($_SERVER['SCRIPT_NAME'],$URI,$CONN);  //建立選單	   
	$grid1->bgcolor = $gridBgcolor;  // 顏色   
	$grid1->row = $gridRow_num ;	     //顯示筆數   
	$grid1->key_item = "student_sn";  // 索引欄名  	
	$grid1->display_item = array("sit_num","stud_name");  // 顯示欄名   
	$grid1->display_color = array("1"=>"$gridBoy_color","2"=>"$gridGirl_color"); //男女生別
	$grid1->color_index_item ="stud_sex" ; //顏色判斷值
	$grid1->class_ccs = " class=leftmenu";  // 顏色顯示
	$grid1->sql_str = "select a.student_sn,a.stud_name,a.stud_sex,b.seme_num as sit_num from stud_base a,stud_seme b where a.student_sn=b.student_sn and a.stud_study_cond=0 and b.seme_year_seme='$c_curr_seme' and b.seme_class='$class_num' order by b.seme_num ";   //SQL 命令 
	$grid1->do_query(); //執行命令   
	
	$grid1->print_grid($student_sn,$upstr,$downstr); // 顯示畫面   

	//顯示資料
	$sql_select = "select * from stud_base where student_sn='$student_sn'";
	$recordSet = $CONN->Execute($sql_select);
while (!$recordSet->EOF) {
	$stud_id = $recordSet->fields["stud_id"];
	$stud_name = $recordSet->fields["stud_name"];
	$stud_name_eng = $recordSet->fields["stud_name_eng"];
	$stud_sex = $recordSet->fields["stud_sex"];
	$stud_birthday = $recordSet->fields["stud_birthday"];
	$stud_blood_type = $recordSet->fields["stud_blood_type"];
	$stud_birth_place = $recordSet->fields["stud_birth_place"];
	$stud_kind = $recordSet->fields["stud_kind"];
	$stud_country = $recordSet->fields["stud_country"];
	$stud_country_kind = $recordSet->fields["stud_country_kind"];
	$stud_person_id = $recordSet->fields["stud_person_id"];
	$stud_country_name = $recordSet->fields["stud_country_name"];
	$addr_move_in = $recordSet->fields["addr_move_in"];
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
	$stud_Mschool_status = $recordSet->fields["stud_Mschool_status"];
	$stud_mschool_id = $recordSet->fields["stud_mschool_id"];
	$stud_mschool_name = $recordSet->fields["stud_mschool_name"];
	$stud_study_year = $recordSet->fields["stud_study_year"];
	$curr_class_num = $recordSet->fields["curr_class_num"];
	$stud_study_cond = $recordSet->fields["stud_study_cond"];
	$enroll_school = $recordSet->fields["enroll_school"];
	$obtain = $recordSet->fields["obtain"];
	$safeguard = $recordSet->fields["safeguard"];
	$recordSet->MoveNext();
};

include  "$SFS_PATH/include/sfs_oo_date.php";

// 日期函式
$seldate = new date_class("myform");
$seldate->demo ="none";
//日期檢查javascript 函式
$seldate->date_javascript();
//生日
$stud_birthday_str = $seldate->date_add("stud_birthday",$stud_birthday);

//戶籍遷入日期
$addr_move_in_str = $seldate->date_add("addr_move_in",$addr_move_in,1);

$seldate->do_check();

?>

<script language="JavaScript">
function checkok() {
	document.myform.nav_next.value = document.gridform.nav_next.value;		
	return date_check();
	}

function do_same(){
	document.myform.stud_addr_2.value=document.myform.stud_addr_1.value;
}
//-->
</script>
</td> 
<td  valign="top" width="100%" >   

<form name="myform" action="<?php echo $_SERVER['SCRIPT_NAME'] ?>" method="post" encType="multipart/form-data"
<?php
//當mnu筆數為0時 讓 form 為 disabled
	if ($grid1->count_row==0 && !($key == $newBtn || $key == $postBtn)) 
		echo " disabled ";
	?> >

<table border="1" cellpadding="2" cellspacing="0"  bordercolorlight="#333354" bordercolordark="#FFFFFF" CLASS="tableBg" width="100%">
  <tr>
  <td class=title_mbody colspan=5 align=center  background="images/tablebg.gif" >
	<?php 
		echo sprintf("%d學年第%d學期 %s--%s (%s)",substr($c_curr_seme,0,-1),substr($c_curr_seme,-1),$class_list_p[$c_curr_seme],$stud_name,$stud_id);
		
	?>
	<?php	
    	if ($stud_list_enable=='1') echo "<input type=submit name=do_key value =\"$editBtn\" onClick=\"return checkok();\">&nbsp;&nbsp;";
    		if ($chknext)
    			echo "<input type=checkbox name=chknext value=1 checked >";			
    		else
    			echo "<input type=checkbox name=chknext value=1 >";
    			
    		echo "自動跳下一位";
    ?>
	</td>	
  </tr>
  <tr>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[stud_name][d_field_cname] ?></td>
	<td >中文:<?php echo $stud_name ?><BR>英文:<input type="text" size="20" maxlength="20" name="stud_name_eng" value="<?php echo $stud_name_eng ?>"></td>
	<td align="right" CLASS="title_sbody1" nowrap>座號</td>
    	<td CLASS="gendata" align='center'>
    		<?php
    		//國中，不允許導師改座號
    		if ($IS_JHORES==6) {
    		?>
    		<font size=4><?php echo intval(substr($curr_class_num,-2)) ?></font>
    		<input type="hidden" name="sit_num" size="3" value="<?php echo intval(substr($curr_class_num,-2)) ?>">
    		
    		<?php
    		} else {
					//檢查是否為設定月分
					$Month=date('n');
					$M=explode(',',$update_site_num);
					if (in_array($Month, $M)) {
					?>
    			<input type="text" name="sit_num" size="3" value="<?php echo intval(substr($curr_class_num,-2)) ?>">
					<?php
					} else {
					?>
    			<font size=4><?php echo intval(substr($curr_class_num,-2)) ?></font>
    			<input type="hidden" name="sit_num" size="3" value="<?php echo intval(substr($curr_class_num,-2)) ?>">
    	  	<?php
    		  } // end if in_array
    		}  // end if $IS_JHORES
    		?>
    	<input type=hidden name="curr_class_num" value="<?php echo $curr_class_num ?>">
    	</td>
    	
    <td width="20%" height="83" rowspan="5">
	 <table border=0 cellpadding=0 cellspacing=0 width=100%  >
    	<tr><td height=80% align=center>
    	<input type="hidden" name="stud_study_year" value="<?php echo $stud_study_year ?>"> 
    	<?php 
    	
    	//印出照片
    		$img =$stud_study_year."/".$stud_id;    		
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
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[stud_sex][d_field_cname] ?></td>
	<td CLASS="gendata">
	<?php  
    	//性別 
    	$temp1=""; $temp2=""; 
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
	<td CLASS="gendata"><?php echo $stud_birthday_str ?></td>
  </tr>
  <tr>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[stud_blood_type][d_field_cname] ?></td>
	<td CLASS="gendata">
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
	<td CLASS="gendata">
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
	<td CLASS="gendata"><input type="text" size="20" maxlength="20" name="stud_country" value="<?php echo ($stud_country=="")?$default_country:$stud_country  ?>"></td>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[stud_country_kind][d_field_cname] ?></td>
	<td CLASS="gendata">
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
	<td CLASS="gendata"><input type="text" size="20" maxlength="20" name="stud_person_id" value="<?php echo $stud_person_id ?>"></td>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[stud_country_name][d_field_cname] ?></td>
	<td CLASS="gendata"><input type="text" size="20" maxlength="20" name="stud_country_name" value="<?php echo $stud_country_name ?>"></td>

  </tr>
<tr>
  	<td align="right" CLASS="title_sbody1" nowrap>住址</td>
	<td CLASS="gendata"  colspan="4" >
	<?php echo $field_data[stud_addr_1][d_field_cname] ?>:
	<input type="text" size="40" maxlength="60" name="stud_addr_1" value="<?php echo $stud_addr_1 ?>"><br>
	<?php echo $field_data[stud_addr_2][d_field_cname] ?>:
	<input type="text" size="40" maxlength="60" name="stud_addr_2" value="<?php echo $stud_addr_2 ?>">
	<?php
	 if ($stud_addr_1 == $stud_addr_2)
	 	$disable_str = " disabled ";
	 ?>
	<input type="button" name="same_addr" value="<?php echo $sameBtn ?>" <?php echo $disable_str ?> onclick="do_same()">
</tr>
<tr><td align="right" CLASS="title_sbody1" nowrap>戶籍遷入日期:</td>
<td colspan="4"><?php echo $addr_move_in_str ?></td>
</tr>
<tr>
<td align="right" CLASS="title_sbody1" nowrap>入學時學校：</td><td colspan="4"><input type='text' size=30 maxlength=30 name="enroll_school" value="<?php echo $enroll_school ?>"></td>
</tr>

<tr bgcolor="#ffcccc">
<td align="right" nowrap>學籍取得原因:</td><td>
	<?php
    	$sel1 = new drop_select(); //選單類別
    	$sel1->s_name = "obtain"; //選單名稱
	$sel1->id = intval($obtain);
	$sel1->has_empty = true;
	$sel1->arr = stud_obtain_kind(); //內容陣列
	$sel1->do_select();	
    	?>
</td>
<td align="right" nowrap>個案保護類別:</td><td>
	<?php
    	$sel1 = new drop_select(); //選單類別
    	$sel1->s_name = "safeguard"; //選單名稱
	$sel1->id = intval($safeguard);
	$sel1->has_empty = true;
	$sel1->arr = stud_safeguard_kind(); //內容陣列
	$sel1->do_select();	
    	?>
</td>
<td align='center'>*本列資料非XML交換標準！</td>
</tr>

<tr>
<td CLASS="gendata"  colspan="5" >
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
	
	<td CLASS="gendata"><input type="text" size="5" maxlength="6" name="stud_addr_a" value="<?php echo $stud_addr_a ?>"></td>
	<td CLASS="gendata"><input type="text" size="5" maxlength="12" name="stud_addr_b" value="<?php echo $stud_addr_b ?>"></td>
	<td CLASS="gendata"><input type="text" size="5" maxlength="12" name="stud_addr_c" value="<?php echo $stud_addr_c ?>"></td>	
	<td CLASS="gendata"><input type="text" size="5" maxlength="6" name="stud_addr_d" value="<?php echo $stud_addr_d ?>"></td>
	<td CLASS="gendata"><input type="text" size="5" maxlength="20" name="stud_addr_e" value="<?php echo $stud_addr_e ?>"></td>
	<td CLASS="gendata"><input type="text" size="3" maxlength="6" name="stud_addr_f" value="<?php echo $stud_addr_f ?>"></td>	
	<td CLASS="gendata"><input type="text" size="5" maxlength="8" name="stud_addr_g" value="<?php echo $stud_addr_g ?>"></td>
	<td CLASS="gendata"><input type="text" size="3" maxlength="6" name="stud_addr_h" value="<?php echo $stud_addr_h ?>"></td>
	<td CLASS="gendata"><input type="text" size="5" maxlength="8" name="stud_addr_i" value="<?php echo $stud_addr_i ?>"></td>
	<td CLASS="gendata"><input type="text" size="5" maxlength="8" name="stud_addr_j" value="<?php echo $stud_addr_j ?>"></td>	
	<td CLASS="gendata"><input type="text" size="3" maxlength="6" name="stud_addr_k" value="<?php echo $stud_addr_k ?>"></td>
	<td CLASS="gendata"><input type="text" size="3" maxlength="6" name="stud_addr_l" value="<?php echo $stud_addr_l ?>"></td>
	<td CLASS="gendata"><input type="text" size="5" maxlength="12" name="stud_addr_m" value="<?php echo $stud_addr_m ?>"></td>
	</tr>

	</table>
	
	</td>
</tr>
<tr>
  	
	<td CLASS="gendata"  colspan="5" >
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
	<td CLASS="gendata" colspan="4">
	<?php  
	//學生身分別
		$sel1 = new checkbox_class(); //選單類別		
		$sel1->s_name = "stud_kind"; //選單名稱
		$sel1->id = $stud_kind;
		$sel1->arr = stud_kind(); //內容陣列	
		$sel1->css = "gendata";
		$sel1->is_color =true;
		$sel1->do_select();
	 ?>	
	</td>
  </tr>
  <tr>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[stud_class_kind][d_field_cname] ?></td>
	<td CLASS="gendata">
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
	<td CLASS="gendata" colspan="2">
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
	<td CLASS="gendata">
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
	<td CLASS="gendata" colspan="2">
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
	<td CLASS="gendata" colspan="4">
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
	<td CLASS="gendata" colspan="4">
	入學資格:
	<?php 
	//入學資格
		$sel1 = new drop_select(); //選單類別	
		$sel1->s_name = "stud_Mschool_status"; //選單名稱
		$sel1->id = intval($stud_Mschool_status);
		$sel1->arr = stud_preschool_status(); //內容陣列	
		$sel1->do_select();
	 ?>	
	
	&nbsp;國小學校代號:<input type="text" size="4" maxlength="8" name="stud_mschool_id" value="<?php echo $stud_mschool_id ?>"> &nbsp;
	國小名稱:<input type="text" size="15" maxlength="40" name="stud_mschool_name" value="<?php echo $stud_mschool_name ?>">
	</td>
  </tr>
</table>
<input type="hidden" name="student_sn" value="<?php echo $student_sn ?>">
<input type="hidden" name="c_curr_seme" value="<?php echo $c_curr_seme ?>">
<input type="hidden" name="c_curr_class" value="<?php echo $c_curr_class ?>">
<input type=hidden name=nav_next >
</form>
</table>
</td>
</tr>
</table>
<?php 
//印出尾頭
foot();
?>
