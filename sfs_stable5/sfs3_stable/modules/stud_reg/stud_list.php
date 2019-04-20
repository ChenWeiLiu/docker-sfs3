<?php

// $Id: stud_list.php 9223 2018-04-23 04:54:59Z smallduh $

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
//$dd=explode("-",$stud_birthday);
//$stud_birthday=sprintf("%04d-%02d-%02d",$dd[0]+1911,$dd[1],$dd[2]);

// 檢查 php.ini 是否打開 file_uploads ?
check_phpini_upload();

//按鍵處理 
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
		
		$student_sn=trim($student_sn);
		$query="select * from stud_base where student_sn='$student_sn'";
		$res=$CONN->Execute($query);
		$stud_id=trim($res->fields['stud_id']);
		$new_stud_id=trim ($new_stud_id);
		//判斷學號是否有更改
		if($stud_id!==$new_stud_id and $new_stud_id<>''){
			//如果有改的話，看看是否新學號有沒有人用了
			$sql_a="select count(*) from stud_base where stud_id='$new_stud_id' ";
			$rs_a=$CONN->Execute($sql_a) or trigger_error($sql_a,256);
			$cou=$rs_a->fields[0];
			if($cou>0){
				//該學號已被使用了，因此不進行學號的修改，並於頁面上告知
				$msg="<font color='red'>$new_stud_id 已經有人使用，因此不進行學號的修改！！</font><br>";
			}
			elseif($cou==0){
				//$stud_id=$new_stud_id;
				//將所有與stud_id相關的資料表進行修改
				$sql_sfs3="SHOW TABLES FROM $mysql_db";
				$rs_sfs3=$CONN->Execute($sql_sfs3) or trigger_error($sql_sfs3,256);
				if ($rs_sfs3) {
					while( $ar_sfs3 = $rs_sfs3->FetchRow() ) {			
						$sql_fields="show fields from {$ar_sfs3[0]}";
						$rs_fields=$CONN->Execute($sql_fields) or trigger_error($sql_fields,256);
						if($rs_fields){								
							while( $ar_fields = $rs_fields->FetchRow() ) {
								//echo $ar_fields[0];
								if($ar_fields[0]=="stud_id") {
									$u1="update $ar_sfs3[0] set stud_id='$new_stud_id' where stud_id='$stud_id'";
									$CONN->Execute($u1);
								}
								elseif($ar_fields[0]=="student_id"){
									$u2="update $ar_sfs3[0] set student_id='$new_stud_id' where student_id='$stud_id'";
									$CONN->Execute($u2);
								} 
							}
						}
					}
				}
				$stud_id=$new_stud_id;
			}
		}
		//exit;
		$stud_kind_temp =",";
		$sit_num = sprintf("%02d",$sit_num);
		while(list($tid,$tname)=each($stud_kind))
			$stud_kind_temp .= $tname.",";
		$temp_num =sprintf("%s%02d",substr($curr_class_num,0,3),$sit_num);
		$sql_update = "update stud_base set stud_name='$stud_name',stud_name_eng='$stud_name_eng',stud_sex='$stud_sex',
		stud_birthday='$stud_birthday',stud_blood_type='$stud_blood_type',stud_birth_place='$stud_birth_place',
		stud_kind='$stud_kind_temp',stud_country='$stud_country',stud_country_kind='$stud_country_kind',
		stud_person_id='$stud_person_id',stud_country_name='$stud_country_name',addr_move_in='$addr_move_in',
		stud_addr_1='$stud_addr_1',stud_addr_2='$stud_addr_2',stud_tel_1='$stud_tel_1',stud_tel_2='$stud_tel_2',
		stud_tel_3='$stud_tel_3',stud_mail='$stud_mail',stud_addr_a='$stud_addr_a',stud_addr_b='$stud_addr_b',
		stud_addr_c='$stud_addr_c',stud_addr_d='$stud_addr_d',stud_addr_e='$stud_addr_e',stud_addr_f='$stud_addr_f',
		stud_addr_g='$stud_addr_g',stud_addr_h='$stud_addr_h',stud_addr_i='$stud_addr_i',stud_addr_j='$stud_addr_j',
		stud_addr_k='$stud_addr_k',stud_addr_l='$stud_addr_l',stud_addr_m='$stud_addr_m',stud_class_kind='$stud_class_kind',
		stud_spe_kind='$stud_spe_kind',stud_spe_class_kind='$stud_spe_class_kind',stud_spe_class_id='$stud_spe_class_id',
		stud_preschool_status='$stud_preschool_status',stud_preschool_id='$stud_preschool_id',
		stud_preschool_name='$stud_preschool_name',stud_Mschool_status='$stud_Mschool_status',
		stud_mschool_id='$stud_mschool_id',stud_mschool_name='$stud_mschool_name',curr_class_num='$temp_num',
		addr_zip='$addr_zip',enroll_school='$enroll_school' ,edu_key='$edu_key',obtain='$obtain',safeguard='$safeguard',experiment_kind='$experiment_kind',exp_group_name='$exp_group_name' where student_sn='$student_sn'";
		
		$sql_update=str_replace("addr_move_in=''","addr_move_in=NULL",$sql_update);
		
//echo $sql_update; 

		$CONN->Execute($sql_update) or die($sql_update);
		$CONN->Execute("update stud_seme set seme_num='$sit_num' where seme_year_seme ='$c_curr_seme' and  seme_class ='$seme_class' and student_sn='$student_sn'");                
		$upload_str = set_upload_path("$img_path/$stud_study_year");
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
	
	case new_stud: //新增一位學生
		if(!$year_seme) break;
		$year_seme_arr=explode("_",$year_seme);
		$seme_year_seme=sprintf("%03d%d",$year_seme_arr[0],$year_seme_arr[1]);
		$seme_class=sprintf("%d%02d",$year_seme_arr[2],$year_seme_arr[3]);
		$stud_id_arr=array();
		$seme_num_arr=array();
		$like_seme_class=substr($seme_class,0,-2);
		$sql_seme="select * from stud_seme where seme_year_seme='$seme_year_seme' and seme_class like '$like_seme_class%' ";		
		$i=0;
		$rs_seme=$CONN->Execute($sql_seme) or trigger_error($sql_seme,256);
		while(!$rs_seme->EOF){
			$stud_id_arr[$i]=$rs_seme->fields['stud_id'];			
			$seme_num_arr[$i]=$rs_seme->fields['seme_num'];
			$length=strlen($stud_id_arr[$i]);
			$i++;
			$rs_seme->MoveNext();
		}
		
		//預設入學年度
		$new_stud_study_year=$year_seme_arr[0]-$year_seme_arr[2]+1+$IS_JHORES;
		//預設學號		
		if(count($stud_id_arr)==0) $new_stud_id=$new_stud_study_year."001";
		else $new_stud_id=max($stud_id_arr)+1;
		
		//預設座號
		$new_seme_num=max($seme_num_arr)+1;


		//預設性別為女
		$new_stud_sex=2;
		
		//寫到學籍基本資料表		
		$new_curr_class_num=$seme_class.sprintf("%02d",$new_seme_num);
		$sql_ins="replace into stud_base(stud_id, stud_sex, stud_study_year, curr_class_num, stud_study_cond,obtain,safeguard) values('$new_stud_id','$new_stud_sex', '$new_stud_study_year', '$new_curr_class_num','0','$obtain','$safeguard')";	
		$rs_ins=$CONN->Execute($sql_ins) or trigger_error($sql_ins,256);
		//取出剛加入的流水號
		$new_student_sn=mysql_insert_id();
		//echo $sql_ins;
		
		//寫入學期資料表
		$seme_class_name=class_id_to_c_name($year_seme);
		$sql_ins2="replace into stud_seme(seme_year_seme,stud_id,seme_class,seme_class_name,seme_num,student_sn) values('$seme_year_seme','$new_stud_id','$seme_class','$seme_class_name','$new_seme_num','$new_student_sn')";
		$rs_ins2=$CONN->Execute($sql_ins2) or trigger_error($sql_ins2,256);
		//echo $sql_ins2;
		
		//寫入戶籍資料表
		$update_time=date("YmdHis");
		$update_id=$_SESSION['session_log_id'];
		$sql_ins3="replace into stud_domicile(stud_id,student_sn,update_time,update_id) values('$new_stud_id','$new_student_sn','$update_time','$update_id')";		
		$rs_ins3=$CONN->Execute($sql_ins3) or trigger_error($sql_ins3,256);
		//echo $sql_ins3;		
		
		//轉向到新增的學生
		$student_sn=$new_student_sn;
		$c_curr_class=$year_seme;
		$c_curr_seme=$seme_year_seme;
		//echo $new_stud_id." / ".$new_seme_num;
	
	break;
}


//印出檔頭
head();

//更改班級
if ($c_curr_class=="")
	// 利用 $IS_JHORES 來 區隔 國中、國小、高中 的預設值
	$c_curr_class = sprintf("%03s_%s_%02s_%02s",curr_year(),curr_seme(),$default_begin_class + round($IS_JHORES/2),1);
else {
	$temp_curr_class_arr = explode("_",$c_curr_class); //091_1_02_03
	$c_curr_class = sprintf("%03s_%s_%02s_%02s",substr($c_curr_seme,0,3),substr($c_curr_seme,-1),$temp_curr_class_arr[2],$temp_curr_class_arr[3]);
}
	
if($c_curr_seme =='')
	$c_curr_seme = sprintf ("%03s%s",curr_year(),curr_seme()); //現在學年學期

//更改學期
if ($c_curr_seme != "")
	$curr_seme = $c_curr_seme;

// 配合 modules/stud_move/stud_move.php 的連結判斷
//if (empty($_GET[c_curr_class])) {
	$c_curr_class_arr = explode("_",$c_curr_class);
	$seme_class = intval($c_curr_class_arr[2]).$c_curr_class_arr[3];

//}
//else
//	$seme_class = $_GET[c_curr_class];

($_POST['student_sn']=='') ? $student_sn=$_GET['student_sn']: $student_sn=$_POST['student_sn'];

	//儲存後到下一筆
if ($chknext)
	$student_sn = $nav_next;	
	$query = "select a.student_sn from stud_base a,stud_seme b where a.student_sn=b.student_sn and a.student_sn='$student_sn' and (a.stud_study_cond=0 or a.stud_study_cond=5)  and  b.seme_year_seme='$c_curr_seme' and b.seme_class='$seme_class'";	
	$res = $CONN->Execute($query) or die($res->ErrorMsg());
	//未設定或改變在職狀況或刪除記錄後 到第一筆
	if ($student_sn =="" || $res->RecordCount()==0) {	
		$temp_sql = "select a.student_sn from stud_base a,stud_seme b where a.student_sn=b.student_sn and (a.stud_study_cond=0 or a.stud_study_cond=5) and  b.seme_year_seme='$c_curr_seme' and b.seme_class='$seme_class' order by b.seme_num ";
		$res2 = $CONN->Execute($temp_sql) or die($temp_sql);
		$student_sn = $res2->fields[0];
	}

//欄位資訊
$field_data = get_field_info("stud_base");
//選單連結字串
$linkstr = "student_sn=$student_sn&c_curr_class=$c_curr_class&c_curr_seme=$c_curr_seme";
//模組選單
print_menu($menu_p,$linkstr);
?> 
<table border="0" width="100%" cellspacing="0" cellpadding="0" CLASS="tableBg" >
<tr>
<td valign=top align="right">
<?php
//建立左邊選單   
//顯示學期
$class_seme_p = get_class_seme(); //學年度	
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
//093_1_03_02
if ($_GET[c_curr_class] <>''){
	$c_curr_class_arr = explode("_",$_GET[c_curr_class]);
	$c_curr_class = sprintf("%03d_%d_%02d_%02d",$s_y,$s_s,$c_curr_class_arr[2],$c_curr_class_arr[3]);
}

//顯示班級
	$tmp=&get_class_select($s_y,$s_s,"","c_curr_class","this.form.submit",$c_curr_class);
	$upstr .= $tmp;
	$grid1 = new ado_grid_menu($_SERVER[SCRIPT_NAME],$URI,$CONN);  //建立選單	   
	$grid1->bgcolor = $gridBgcolor;  // 顏色   
	$grid1->row = $gridRow_num ;	     //顯示筆數   
	$grid1->key_item = "student_sn";  // 索引欄名  	
	$grid1->display_item = array("sit_num","stud_name");  // 顯示欄名   
	$grid1->display_color = array("1"=>"$gridBoy_color","2"=>"$gridGirl_color"); //男女生別
	$grid1->color_index_item ="stud_sex" ; //顏色判斷值
	$grid1->class_ccs = " class=leftmenu";  // 顏色顯示
	$grid1->sql_str = "select a.student_sn,a.stud_id,a.stud_name,a.stud_sex,b.seme_num as sit_num from stud_base a,stud_seme b where a.student_sn=b.student_sn  and (a.stud_study_cond=0 or a.stud_study_cond=5) and  b.seme_year_seme='$c_curr_seme' and b.seme_class='$seme_class' order by b.seme_num ";   //SQL 命令   
//	echo $grid1->sql_str;	
	$grid1->do_query(); //執行命令   
	$grid1->print_grid($student_sn,$upstr,$downstr); // 顯示畫面   
	$c_name=class_id_to_full_class_name2($c_curr_class);
	echo "<table align='center'><tr><td>
	<form action='{$_SERVER['SCRIPT_NAME']}' method='POST' name='new_form'>
	<input type='hidden' name='year_seme' value='$c_curr_class'>
	<input type='hidden' name='do_key' value='new_stud'>";
	
	
	//是否顯示新增學生按鈕
	$sql_select="select pm_id,pm_item,pm_memo,pm_value from pro_module where pm_name = 'stud_reg' and pm_item='view_add' Limit 1";
	$recordSet=$CONN->Execute($sql_select);
	$view_add=$recordSet->fields['pm_value'];

	
	$ba=explode("_",$c_curr_class); 
	
	
	if ($view_add>0 || $ba[2]=="00" || $ba[2]=="01" || $ba[2]=="07")
	{
		echo "<input type='button' value='新增' onclick=\"if(confirm('您確定要新增一位學生於？\\n$c_name')) this.form.submit()\">";
	
	}
	
	echo "</form>
	</td></tr></table>";
	//顯示資料
	//if($_GET['stud_id']) $stud_id=$_GET['stud_id'];
	$sql_select = "select * from stud_base where student_sn='$student_sn' ";	
	$recordSet = $CONN->Execute($sql_select);
while (!$recordSet->EOF) {
	$student_sn = $recordSet->fields["student_sn"];
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
	$addr_zip = $recordSet->fields["addr_zip"];
	$enroll_school = $recordSet->fields["enroll_school"];
	$obtain = $recordSet->fields["obtain"];
	$safeguard = $recordSet->fields["safeguard"];

	$experiment_kind=$recordSet->fields["experiment_kind"];
	$exp_group_name=$recordSet->fields["exp_group_name"];

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

<form name="myform" action="<?php echo $_SERVER[SCRIPT_NAME] ?>" method="post" encType="multipart/form-data"
<?php
//當mnu筆數為0時 讓 form 為 disabled
	if ($grid1->count_row==0 && !($key == $newBtn || $key == $postBtn)) 
		echo " disabled ";
	?> >

<table border="1" cellpadding="2" cellspacing="0"  bordercolorlight="#333354" bordercolordark="#FFFFFF" class="main_body" width="100%">
  <tr>
  <td class=title_mbody colspan="5" align="center">
	<?php 
		echo $msg;
		echo sprintf("%d學年第%d學期 %s--%s %s",substr($c_curr_seme,0,-1),substr($c_curr_seme,-1),$class_list_p[$c_curr_seme],$stud_name,"<input type='text' size='10' name='new_stud_id' value=$stud_id>");
		
	?>

	<?php
	echo "   (系統流水號:$student_sn)  ";
	if ($modify_flag) {
    	echo "<input type=submit name=do_key value =\"$editBtn\" onClick=\"return checkok();\">&nbsp;&nbsp;";
    		if ($chknext)
    			echo "<input type=checkbox name=chknext value=1 checked >";			
    		else
    			echo "<input type=checkbox name=chknext value=1 >";
    			
    		echo "自動跳下一位";
	}
    
    ?>
	</td>	
  </tr>
  <tr>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[stud_name][d_field_cname] ?></td>
	<td>
		中文:<input type="text" size="20" maxlength="20" name="stud_name" value="<?php echo $stud_name; ?>"><br />
		英文:<input type="text" size="20" maxlength="20" name="stud_name_eng" value="<?php echo $stud_name_eng; ?>">
	</td>
	<td align="right" CLASS="title_sbody1" nowrap>座號</td>
  <td><input type="text" name="sit_num" size="3" value="<?php echo intval(substr($curr_class_num,-2)) ?>">
    	<input type=hidden name="curr_class_num" value="<?php echo $curr_class_num ?>">
  </td>
  <td width="20%" height="83" rowspan="5">
    <table border=0 cellpadding=0 cellspacing=0 width=100%  >
    	<tr>
    		<td height=80% align=center>
    	<input type="hidden" name="stud_study_year" value="<?php echo $stud_study_year ?>"> 
    	<?php 
    	
    	//印出照片
    		$img =$stud_study_year."/".$stud_id;    		
    		if (file_exists($UPLOAD_PATH."$img_path/".$img)) {
    			echo "<img src=\"".$UPLOAD_URL."$img_path/$img\" width=\"$img_width\">";
			echo "<br><font size=2><input type=checkbox name=\"del_img\" value=\"1\"> 刪除圖檔</font>";
		}
    	?>
    	</td>
    	</tr>
    	<tr>
    	<td height=20% valign=bottom>
    	<font size=2>照片</font><input type="file" size=10 name="stud_img" >
    	</td>
    	</tr>
    </table>
    </td>
  </tr>
  <tr>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[stud_sex][d_field_cname] ?></td>
	<td >
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
	<td ><?php echo $stud_birthday_str ?></td>
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
	<input type="text" size="5" maxlength="5" name="addr_zip" value="<?php echo $addr_zip ?>" title="填入郵遞區號">
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
<td align='center' rowspan="2">*本列資料非XML交換標準！</td>
</tr>
	<tr bgcolor="#ffcccc">
		<td align="right" nowrap>學生學習型態</td><td>
			<?php
			$sel1 = new drop_select(); //選單類別
			$sel1->s_name = "experiment_kind"; //選單名稱
			$sel1->id = intval($experiment_kind);
			$sel1->has_empty = true;
			$sel1->arr = stud_experiment_kind(); //內容陣列
			$sel1->do_select();
			?>
		</td>
		<td align="right" nowrap>實驗教育團體/機構名稱</td>
		<td><input type="text" name="exp_group_name" size="20" value="<?php echo $exp_group_name;?>"></td>
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
  	
	<td   colspan="5" >
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
<input type="hidden" name="seme_class" value="<?php echo $seme_class ?>">
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
