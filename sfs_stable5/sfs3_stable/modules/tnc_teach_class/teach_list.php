<?php

// $Id: teach_list.php 5310 2009-01-10 07:57:56Z hami $

// 載入設定檔
include "teach_config.php";

// 認證檢查
sfs_check();

require_once "module-upgrade.php";

// 不需要 register_globals
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}


// 檢查 php.ini 是否打開 file_uploads ?
check_phpini_upload();

//更改在職狀態
if ($c_sel != "")
	$sel = $c_sel;
else if ($sel=="")
	$sel = 0 ; //預設選取在職狀況

//按鍵處理 
switch ($do_key){ 
	case $editBtn: //修改
	  // 若 teach_id 變動，才判斷是否有人已使用該 ID
	  	if ($teach_id != $old_teach_id) {
      		$sql_check_id="select teach_id from teacher_base where teach_id='$teach_id'";
        	$check_id=$CONN->Execute($sql_check_id);
        	$tt=$check_id->fields[teach_id];
        	if($tt) trigger_error("該教師代號已經有人使用了！", E_USER_ERROR);
			$head_teach_id=substr($teach_id,0,1);			
			if(ereg ("([0-9]{1})", $head_teach_id, $regs)) trigger_error("該教師代號 $teach_id 不好！第一個字不允許數字", E_USER_ERROR);
	  	}
        
		$sql_update = "update teacher_base set teach_id='$teach_id',teach_person_id='$teach_person_id',name='$name',sex='$sex',birthday='$birthday',birth_place='$birth_place',marriage='$marriage',address='$address',home_phone='$home_phone',cell_phone='$cell_phone',office_home='$office_home',teach_condition='$teach_condition',teach_memo='$teach_memo',teach_edu_kind='$teach_edu_kind',teach_edu_abroad='$teach_edu_abroad',teach_sub_kind='$teach_sub_kind',teach_check_kind='$teach_check_kind',teach_check_word='$teach_check_word',teach_is_cripple='$teach_is_cripple' where teacher_sn='$old_teacher_sn' ";
        $CONN->Execute($sql_update) or die ($sql_update);

        $upload_str = set_upload_path($img_path);

        //圖檔處理
        if($_FILES['teacher_img']['tmp_name']){
            //設定上傳檔案路徑
            copy($_FILES['teacher_img']['tmp_name'], $upload_str."/$old_teacher_sn");
        }
        else if ($del_img) {
            if (file_exists($upload_str."/$old_teacher_sn"))
                unlink($upload_str."/$old_teacher_sn");
        }

        $sel = $teach_condition ;  //設成目前在職狀態
	break;
	
	case $postBtn: //確定新增
        $sql_check_id="select teach_id from teacher_base where teach_id='$teach_id'";
        $check_id=$CONN->Execute($sql_check_id);
        $tt=$check_id->fields[teach_id];
        if($tt) trigger_error("該教師代號已經有人使用了！", E_USER_ERROR);
        else{
            $sql_insert = "insert into teacher_base (teach_id,teach_person_id,name,sex,birthday,birth_place,marriage,address,home_phone,cell_phone,office_home,teach_condition,teach_memo,login_pass,teach_edu_kind,teach_edu_abroad,teach_sub_kind,teach_check_kind,teach_check_word,teach_is_cripple) values ('$teach_id','$teach_person_id','$name','$sex','$birthday','$birth_place','$marriage','$address','$home_phone','$cell_phone','$office_home','$teach_condition','$teach_memo','$DEFAULT_LOG_PASS','$teach_edu_kind','$teach_edu_abroad','$teach_sub_kind','$teach_check_kind','$teach_check_word','$teach_is_cripple')";
            $CONN->Execute($sql_insert) or die ($sql_insert);

            // 根據 $teach_id 來找出新增教師的 teacher_sn
            $sql_query = "select teacher_sn from teacher_base where teach_id='$teach_id'";
            $res=$CONN->Execute($sql_query) or die ($sql_query);
            $teacher_sn=$res->fields[0];

            $teacher_post_insert = "insert into teacher_post(teacher_sn, post_kind, post_office,post_level,official_level,post_class,post_num,bywork_num,salay,appoint_date,arrive_date,approve_date,approve_number,teach_title_id, class_num,update_time,update_id) VALUES ('$teacher_sn', '0','8','0','$official_level','$post_class','$post_num','$bywork_num','0','$appoint_date','$arrive_date','$approve_date','$approve_number','19','0','$update_time','admin')";
            $CONN->Execute($teacher_post_insert) or die ($teacher_post_insert);
            $upload_str = set_upload_path($img_path);
            //圖檔處理
            if($_FILES['teacher_img']['tmp_name']){
                //設定上傳檔案路徑
                copy($_FILES['teacher_img']['tmp_name'], $upload_str."/$teacher_sn");
            }
         }
	break;
	
	case $delBtn: //確定刪除
		
		$sql_pro_check1="select pro_kind_id from pro_check_new where id_sn='$old_teacher_sn' and pro_kind_id=1";
		$rs_pro_check1=$CONN->Execute($sql_pro_check1);
		$your_pro_kind=$rs_pro_check1->fields['pro_kind_id'];
		$howmany_admin=999;
		if($your_pro_kind=="1"){
			$sql_pro_check2="select count(*) from pro_check_new where pro_kind_id='1'";
			$rs_pro_check2=$CONN->Execute($sql_pro_check2);
			$howmany_admin=$rs_pro_check2->fields[0];											
		}										
		//echo $howmany_adm; exit;		
		if($howmany_admin!=1){
			$query = "delete from teacher_base where teacher_sn='$old_teacher_sn'";
			$CONN->Execute($query);
			$query = "delete from teacher_post where teacher_sn='$old_teacher_sn'";
			$CONN->Execute($query);
			$query = "delete from teacher_connect where teacher_sn='$old_teacher_sn'";
			$CONN->Execute($query);
			$query = "delete from pro_check_new where id_sn='$old_teacher_sn'";
			$CONN->Execute($query);
		
			$teacher_sn ="";
		}
	break;


} 
//印出檔頭
head("教師基本資料");
//欄位資訊
$field_data = get_field_info("teacher_base");

// 若是修改狀態, 則使其畫面仍停留在被修改的教師
if ($old_teacher_sn) $teacher_sn=$old_teacher_sn;

//選單連結字串
$linkstr = "teacher_sn=$teacher_sn&sel=$sel";
//印出選單
print_menu($teach_menu_p,$linkstr);

if($do_key == $newBtn || $do_key == $postBtn) {//新增 或確定新增鈕	
	if ($is_IDauto)
		$teach_id = get_sfs_id(); //取得流水號		
}
else {
	//儲存後到下一筆
	if ($chknext)
		$teacher_sn = $nav_next;	
}


if($do_key == $newBtn || $do_key == $postBtn) {//新增 或確定新增鈕
	
	$teach_person_id = '';
	$name = '';
	$sex = '';	
	$birthday = '';
	$birth_place = '';
	$marriage = '';
	$address = '';
	$home_phone = '';
	$cell_phone = '';
	$office_home = '';
	$teach_condition = '';	
	$teach_edu_kind = '';
	$teach_edu_abroad = '';
	$teach_sub_kind = '';
    $teach_check_kind = '';
	$teach_check_word = '';
	$teach_is_cripple = '';	
	
}
else {	
	$query = "select teacher_sn from teacher_base where teacher_sn='$teacher_sn' and teach_condition ='$sel'";
	$res = $CONN->Execute($query) or die($query);
	//未設定或改變在職狀況或刪除記錄後 到第一筆
	if ($teacher_sn =="" || $res->RecordCount()==0) {
		$temp_sql = "select teacher_sn from teacher_base where teach_condition ='$sel' order by teacher_sn  ";
		$res2 = $CONN->Execute($temp_sql) or die($temp_sql);
		$teacher_sn = $res2->fields[0];
	}

	$sql_select = "select teach_id,teach_person_id,name,sex,age,birthday,birth_place,marriage,address,home_phone,cell_phone,office_home,teach_condition,teach_memo,teach_edu_kind,teach_edu_abroad,teach_sub_kind,teach_check_kind,teach_check_word,teach_is_cripple,update_time,update_id from teacher_base where teacher_sn='$teacher_sn' ";
	$recordSet = $CONN->Execute($sql_select)or die($sql_select);
	if(!$recordSet->EOF) {
		$teach_id = $recordSet->fields["teach_id"];
		$teach_person_id = $recordSet->fields["teach_person_id"];
		$name = $recordSet->fields["name"];
		$sex = $recordSet->fields["sex"];
		$age = $recordSet->fields["age"];
		$birthday = $recordSet->fields["birthday"];
		$birth_place = $recordSet->fields["birth_place"];
		$marriage = $recordSet->fields["marriage"];
		$address = $recordSet->fields["address"];
		$home_phone = $recordSet->fields["home_phone"];
		$cell_phone = $recordSet->fields["cell_phone"];
		$office_home = $recordSet->fields["office_home"];
		$teach_condition = $recordSet->fields["teach_condition"];
		$teach_memo = $recordSet->fields["teach_memo"];

		$teach_edu_kind = $recordSet->fields["teach_edu_kind"];
		$teach_edu_abroad = $recordSet->fields["teach_edu_abroad"];
		$teach_sub_kind = $recordSet->fields["teach_sub_kind"];
        $teach_check_kind = $recordSet->fields["teach_check_kind"];
		$teach_check_word = $recordSet->fields["teach_check_word"];
		$teach_is_cripple = $recordSet->fields["teach_is_cripple"];
		$update_time = $recordSet->fields["update_time"];

	}
}
include  "$SFS_PATH/include/sfs_oo_date.php";

// 日期函式
$seldate = new date_class("myform");
//$seldate->demo ="none";
//日期檢查javascript 函式
$seldate->date_javascript();

//生日
$birthday_str = $seldate->date_add("birthday",$birthday);

$seldate->do_check();

?>

<script language="JavaScript">
function checkok() {
	document.myform.nav_next.value = document.gridform.nav_next.value;		
	return date_check();
}
//-->
</script>


<TABLE BORDER=0 CELLPADDING=10 CELLSPACING=0 CLASS="tableBg" WIDTH="100%" ALIGN="CENTER"> 
<TR>
<td valign="top">
<!------------------ 左邊選單開始 --------------------!>
<?php    
	//建立左邊選單 
	$remove_p = remove(); //在職狀況    
	$upstr = "顯示<select name=\"c_sel\" onchange=\"this.form.submit()\">\n"; 
	while (list($tid,$tname)=each($remove_p)){
		if ($sel== $tid)
			$upstr .= "<option value=\"$tid\" selected>$tname</option>\n";
		else
			$upstr .= "<option value=\"$tid\">$tname</option>\n";
	}
	$upstr .= "</select>"; 
	if($sel == 0) //在職時顯示 新增鈕 
		$downstr = "<hr size=1><input type=submit name=\"do_key\" value =\"$newBtn\">"; 

	$grid1 = new ado_grid_menu($_SERVER['PHP_SELF'],$URI,$CONN);  //建立選單
	$grid1->bgcolor = $gridBgcolor;  // 顏色
	$grid1->row = $gridRow_num ;	     //顯示筆數
	$grid1->key_item = "teacher_sn";  // 索引欄名
	$grid1->display_item = array("teacher_sn","name");  // 顯示欄名
	$grid1->display_color = array("1"=>"$gridBoy_color","2"=>"$gridGirl_color"); //男女生別
	$grid1->color_index_item ="sex" ; //顏色判斷值
	$grid1->class_ccs = " class=leftmenu";  // 顏色顯示
	$grid1->sql_str = "select teacher_sn,name,sex from teacher_base where teach_condition='$sel' order by teacher_sn";   //SQL 命令
	$grid1->do_query(); //執行命令
	if ($do_key == $newBtn || $do_key == $postBtn)
		$grid1->disabled=1;
	$grid1->print_grid($teacher_sn,$upstr,$downstr); // 顯示畫面


?>
<!------------------ 左邊選單結束 --------------------!>



</td>
<td  valign="top" width="100%" >

<form name ="myform" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" encType="multipart/form-data" <?php
	//當mnu筆數為0時 讓 form 為 disabled

	if ($grid1->count_row==0 && !($do_key == $newBtn || $do_key == $postBtn))
		echo " disabled ";
	?> >
     	
<table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF"  width="100%" class=main_body >
<tr>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[teach_id][d_field_cname] ?></td>
	<td CLASS="gendata" colspan="3">
	<?php
		if($do_key == $newBtn || $do_key == $postBtn) //新增 或確定新增鈕
			echo "<input type=\"text\"  name=\"teach_id\" value=\"$teach_id\">";
		else
			echo "<input type=\"text\"  name=\"teach_id\" value=\"$teach_id\">";
	?>
	</td>

	<td  rowspan="5">
    		<table border=0 cellpadding=0 cellspacing=0 width=100% >
    		<tr>
    			<td height=80% align=center>
 	
 	<?php 
    	
    	//印出照片
    	
    		if (file_exists($UPLOAD_PATH."/$img_path/".$teacher_sn)&& $teacher_sn<>'') {
    			echo "<img src=\"".$UPLOAD_URL."$img_path/$teacher_sn\" width=\"$img_width\">";
    			echo "<br><font size=2><input type=checkbox name=\"del_img\" value=\"1\"> 刪除圖檔</font>";
		}
		else {
			echo "<font size=2>沒有照片</font><br><img src=\"images/pixel_clear.gif\"  >";
			
		}
    	?>
    	
			</td>
		</tr>
		<tr>
			<td height=20% valign=bottom>
    				<input type="file" size=10 name="teacher_img" >
    			</td>
    		</tr>
    		</table>
	</td>
</tr>
<tr>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[name][d_field_cname] ?></td>
	<td CLASS="gendata"><input type="text" size="20" maxlength="20" name="name" value="<?php echo $name ?>"></td>
	<td align="right" CLASS="title_sbody1"><?php echo $field_data[sex][d_field_cname] ?></td>
	<td CLASS="gendata">

	<?php  
    	//性別 
	$temp1=""; $temp2=""; 
	if($sex == 1 ){ 
		$temp1="checked "; $temp2=""; 
	} 
    	else if($sex == 2){ 
		$temp1=""; $temp2="checked "; 
	} 
	?> 
	<input type="radio" name="sex" value="1" <?php echo $temp1 ?>>男 &nbsp;&nbsp;<input type="radio" name="sex" value="2" <?php echo $temp2 ?>>女 
	</td>
</tr>
<tr>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[teach_person_id][d_field_cname] ?></td>
	<td CLASS="gendata"><input type="text" size="10" maxlength="10" name="teach_person_id" value="<?php echo $teach_person_id ?>"></td>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[marriage][d_field_cname] ?></td>
	<td >
	<?php 
    	 //婚姻別 
	$temp1=""; $temp2=""; 
	if($marriage == 1 ){ 
		$temp1="checked "; $temp2=""; 
	} 
	else if($marriage == 2){ 
	$temp1=""; $temp2="checked "; 
	} 
	?> 
	<input type="radio" name="marriage" value="2" <?php echo $temp2 ?>>已婚 <input type="radio" name="marriage" value="1" <?php echo $temp1 ?>>未婚 
	</td>
</tr>
<tr>
	<td align="right" CLASS="title_sbody1"><?php echo $field_data[birthday][d_field_cname] ?></td>
	<td CLASS="gendata"><?php echo $birthday_str ?></td>
	<td align="right" CLASS="title_sbody1"><?php echo $field_data[birth_place][d_field_cname] ?></td>
	<td >
	<?php
    	//出生地陣列 
    	$sel1 = new drop_select(); //選單類別
    	$sel1->s_name = "birth_place"; //選單名稱
	$sel1->id = intval($birth_place);
	$sel1->arr = birth_state(); //內容陣列
	$sel1->do_select();	
	?>
	</td>
</tr>
<tr>
	<td align="right" CLASS="title_sbody1"><?php echo $field_data[teach_is_cripple][d_field_cname] ?></td>
	<td >
	<?php  
    	//殘障手冊判斷 
    	$temp1=""; $temp2=""; 
    	if($teach_is_cripple == 0 ){ 
    		$temp1="checked "; $temp2=""; 
    	} 
    	else {   	 
    		$temp1=""; $temp2="checked "; 
    	} 
	?> 
	<input type="radio" name="teach_is_cripple" value="0" <?php echo $temp1 ?>>否 &nbsp;&nbsp;<input type="radio" name="teach_is_cripple" value="1" <?php echo $temp2 ?>>是     
	</td>
	<td align="right" CLASS="title_sbody1"><?php echo $field_data[teach_condition][d_field_cname] ?></td>
	<td CLASS="gendata">
	<?php
    	//在職狀況
    	$sel1 = new drop_select(); //選單類別
    	$sel1->s_name = "teach_condition"; //選單名稱
	$sel1->id = intval($teach_condition);
	$sel1->arr = remove(); //內容陣列	
	$sel1->do_select();
	?>    
	</td>
</tr>
<tr>
	<td align="right" CLASS="title_sbody1"><?php echo $field_data[address][d_field_cname] ?></td>
	<td CLASS="gendata" colspan="4"><input type="text" size="60" maxlength="60" name="address" value="<?php echo $address ?>"></td>
	
</tr>

<tr>
	<td align="right" CLASS="title_sbody1"><?php echo $field_data[home_phone][d_field_cname] ?></td>
	<td CLASS="gendata"><input type="text" size="20" maxlength="20" name="home_phone" value="<?php echo $home_phone ?>"></td>
	<td align="right" CLASS="title_sbody1"><?php echo $field_data[cell_phone][d_field_cname] ?></td>
	<td CLASS="gendata" colspan="2"><input type="text" size="20" maxlength="20" name="cell_phone" value="<?php echo $cell_phone ?>"></td>
    
</tr>
<tr>
<td align="right" CLASS="title_sbody1"><?php echo $field_data[teach_edu_kind][d_field_cname] ?></td>
<td  CLASS="gendata" colspan="4">
    <?php
    	//學歷
    	$sel1 = new drop_select(); //選單類別
    	$sel1->s_name = "teach_edu_kind"; //選單名稱
	$sel1->id = intval($teach_edu_kind);
	$sel1->arr = tea_edu_kind(); //內容陣列
	$sel1->do_select();
	
    	//國內外學歷別 
    	$temp1=""; $temp2=""; 
    	if($teach_edu_abroad == 0 ){ 
    		$temp1="checked "; $temp2=""; 
    	} 
    	else if($teach_edu_abroad == 1){ 
    		$temp1=""; $temp2="checked "; 
    	} 
    	
    ?> 
 
    <input type="radio" name="teach_edu_abroad" value="0" <?php echo $temp1 ?>>國內 &nbsp;<input type="radio" name="teach_edu_abroad" value="1" <?php echo $temp2 ?>>國外     
</td>
</tr>
<tr>
	<td align="right" CLASS="title_sbody1"><?php echo $field_data[teach_sub_kind][d_field_cname] ?></td>
	<td CLASS="gendata"><input type="text" size="20" maxlength="40" name="teach_sub_kind" value="<?php echo $teach_sub_kind ?>"></td>
	<td align="right" CLASS="title_sbody1"><?php echo $field_data[teach_check_kind][d_field_cname] ;?></td>
	<td  CLASS="gendata" colspan="2">
	<?php
    	//檢定資格別
    	$sel1 = new drop_select(); //選單類別
    	$sel1->s_name = "teach_check_kind"; //選單名稱
	    $sel1->id = intval($teach_check_kind);
	    $sel1->arr = tea_check_kind(); //內容陣列
	    $sel1->do_select();
	?>
	</td>    
</tr>
<tr>
	<td align="right" CLASS="title_sbody1"><?php echo $field_data[teach_check_word][d_field_cname] ?></td>
	<td CLASS="gendata" colspan="2"><input type="text" size="30" maxlength="30" name="teach_check_word" value="<?php echo $teach_check_word ?>"></td>	
	<td  colspan="2">上次修改時間： <?php echo $update_time ?></td> 
</tr>
<tr>
	<td  align="center"  colspan="5" >
	<input type="hidden" name="old_teacher_sn" value="<?php echo $teacher_sn ?>">
	<input type="hidden" name="old_teach_id" value="<?php echo $teach_id ?>">
	<input type="hidden" name="update_id" value="<?php echo $_SESSION['session_log_id'] ?>">
	<?php	 
    	if ($do_key == $newBtn || $do_key == $postBtn)
    		echo "<input type=submit name=\"do_key\" value =\"$postBtn\" onClick=\"return checkok();\">"; 
    	else if ($grid1->count_row > 0){ 
    		if ($chknext) 
    			echo "<input type=checkbox name=chknext value=1 checked >";			 
    		else 
    			echo "<input type=checkbox name=chknext value=1 >"; 
    			
    		echo "自動跳下一位 &nbsp;&nbsp;<input type=submit name=\"do_key\" value =\"$editBtn\" onClick=\"return checkok();\">&nbsp;&nbsp;<input type=submit name=\"do_key\" value =\"$delBtn\" onClick=\"return confirm('確定刪除 ".$name." 記錄?');\" >"; 
    	} 
    	else 
    		echo "&nbsp;"; 
    ?> 
    <input type=hidden name=nav_next >
	</td>
</tr>
</table>
</form>
</TD>
</TR>
</TABLE>
<?php 
//印出尾頭
foot();
?> 
