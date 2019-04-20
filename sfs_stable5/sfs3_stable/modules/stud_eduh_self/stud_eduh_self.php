<?php

// $Id: stud_eduh_self.php 7725 2013-10-28 07:46:59Z smallduh $

// 引入您自己的 config.php 檔
require "config.php";

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
$field_data = get_field_info("stud_seme_eduh");//輔導紀錄欄位
$field_data_base=get_field_info("stud_base");//基本紀錄欄位

//檢查是否開放
if (!$stud_eduh_editable){
   echo "模組變數尚未開放本功能，請洽詢學校系統管理者！";
   exit;
}


//模組選單
print_menu($menu_p);

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

//如果在籍才繼續處理
if ($student_sn) {

switch($_POST[do_key]) {
	case $editBtn:
	for ($i=1;$i<=11;$i++) {
		$sse_temp =",";	
		$sse_arr = "sse_s".$i;
		if (count($_POST["sse_s".$i])>0) {
			foreach ($_POST["sse_s".$i] as $tid=>$tname) {
			 $sse_temp .= $tname.",";
			}
			//while(list($tid,$tname)=each($_POST["sse_s".$i])) $sse_temp .= $tname.",";
			$$sse_arr = $sse_temp;
		}
	}
	$sql_insert = "replace into stud_seme_eduh (seme_year_seme,stud_id,sse_relation,sse_family_kind,sse_family_air,sse_farther,sse_mother,sse_live_state,sse_rich_state,sse_s1,sse_s2,sse_s3,sse_s4,sse_s5,sse_s6,sse_s7,sse_s8,sse_s9,sse_s10,sse_s11) values ('$seme_year_seme','".$_SESSION['session_log_id']."','$_POST[sse_relation]','$_POST[sse_family_kind]','$_POST[sse_family_air]','$_POST[sse_farther]','$_POST[sse_mother]','$_POST[sse_live_state]','$_POST[sse_rich_state]','$sse_s1','$sse_s2','$sse_s3','$sse_s4','$sse_s5','$sse_s6','$sse_s7','$sse_s8','$sse_s9','$sse_s10','$sse_s11')";
	$CONN->Execute($sql_insert) or die ($sql_insert);
	break;
}

$sql_select = "select * from stud_seme_eduh where stud_id='".$_SESSION['session_log_id']."' and seme_year_seme='$seme_year_seme'";
$recordSet = $CONN->Execute($sql_select) or die ($sql_select);
if (!$recordSet->EOF) {
	$sse_relation = $recordSet->fields["sse_relation"];
	$sse_family_kind = $recordSet->fields["sse_family_kind"];
	$sse_family_air = $recordSet->fields["sse_family_air"];
	$sse_farther = $recordSet->fields["sse_farther"];
	$sse_mother = $recordSet->fields["sse_mother"];
	$sse_live_state = $recordSet->fields["sse_live_state"];
	$sse_rich_state = $recordSet->fields["sse_rich_state"];
	$sse_s1 = $recordSet->fields["sse_s1"];
	$sse_s2 = $recordSet->fields["sse_s2"];
	$sse_s3 = $recordSet->fields["sse_s3"];
	$sse_s4 = $recordSet->fields["sse_s4"];
	$sse_s5 = $recordSet->fields["sse_s5"];
	$sse_s6 = $recordSet->fields["sse_s6"];
	$sse_s7 = $recordSet->fields["sse_s7"];
	$sse_s8 = $recordSet->fields["sse_s8"];
	$sse_s9 = $recordSet->fields["sse_s9"];
	$sse_s10 = $recordSet->fields["sse_s10"];
	$sse_s11 = $recordSet->fields["sse_s11"];
} else {
	unset($sse_relation);
	unset($sse_family_kind);
	unset($sse_family_air);
	unset($sse_farther);
	unset($sse_mother);
	unset($sse_live_state);
	unset($sse_rich_state);
	unset($sse_s1);
	unset($sse_s2);
	unset($sse_s3);
	unset($sse_s4);
	unset($sse_s5);
	unset($sse_s6);
	unset($sse_s7);
	unset($sse_s8);
	unset($sse_s9);
	unset($sse_s10);
	unset($sse_s11);
}
?> 
<table BORDER=0 CELLPADDING=0 CELLSPACING=0 CLASS="tableBg" WIDTH="100%" > 
<tr>
    <td width="100%" valign=top bgcolor="#CCCCCC">
    <form name ="myform" action="<?php echo $_SERVER['SCRIPT_NAME'] ?>" method="post">

<!------------------- 輸入表單開始 ------------------------------>

<table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF"  width="100%" class="main_body">
<tr>
	<td class=title_mbody colspan=5 align=center >
		<?php echo $stud_name."  (學號：".$_SESSION['session_log_id'].")";?>
	</td>	
</tr>	
<tr>
	<td colspan=2>
	<table  cellspacing="5" cellpadding="0" >
	<tr>
	<td align="right" CLASS="title_sbody1"><?php echo $field_data[sse_relation][d_field_cname] ?></td>
	<td CLASS="gendata">
	<?php 
	$sel = new drop_select();
	$sel->s_name = "sse_relation";
	$sel->arr = sfs_text("父母關係");
	$sel->id = $sse_relation;
	$sel->do_select();
	?>
	</td>
	<td align="right" CLASS="title_sbody1"><?php echo $field_data[sse_family_kind][d_field_cname] ?></td>
	<td CLASS="gendata">
	<?php 
	$sel->s_name = "sse_family_kind";
	$sel->arr = sfs_text("家庭類型");
	$sel->id = $sse_family_kind;
	$sel->do_select();
	?>
	</td>
	<td align="right" CLASS="title_sbody1"><?php echo $field_data[sse_family_air][d_field_cname] ?></td>
	<td CLASS="gendata">
	<?php 
	$sel->s_name = "sse_family_air";
	$sel->arr = sfs_text("家庭氣氛");
	$sel->id = $sse_family_air;
	$sel->do_select();
	?>
	</td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td colspan=2>
	<table  cellspacing="5" cellpadding="0" >
	<tr>
	<td align="right" CLASS="title_sbody1"><?php echo $field_data[sse_farther][d_field_cname] ?></td>
	<td CLASS="gendata">
	<?php 
	$sel->s_name = "sse_farther";
	$sel->arr = sfs_text("管教方式");
	$sel->id = $sse_farther;
	$sel->do_select();
	?>
	</td>
	<td align="right" CLASS="title_sbody1"><?php echo $field_data[sse_mother][d_field_cname] ?></td>
	<td CLASS="gendata">
	<?php 
	$sel->s_name = "sse_mother";
	$sel->arr = sfs_text("管教方式");
	$sel->id = $sse_mother;
	$sel->do_select();
	?>
	</td>
	<td align="right" CLASS="title_sbody1"><?php echo $field_data[sse_live_state][d_field_cname] ?></td>
	<td CLASS="gendata">
	<?php 
	$sel->s_name = "sse_live_state";
	$sel->arr = sfs_text("居住情形");
	$sel->id = $sse_live_state;
	$sel->do_select();
	?>
	</td>
	<td align="right" CLASS="title_sbody1"><?php echo $field_data[sse_rich_state][d_field_cname] ?></td>
	<td CLASS="gendata">
	<?php 
	$sel->s_name = "sse_rich_state";
	$sel->arr = sfs_text("經濟狀況");
	$sel->id = $sse_rich_state;
	$sel->do_select();
	?>
	</td>
	</tr>
	</table>
	</td>
</tr>	
<tr>
	<td align="right" CLASS="title_sbody1"><?php echo $field_data[sse_s1][d_field_cname] ?></td>
	<td CLASS="gendata">
	<?php
	$chk = new checkbox_class();
	$chk->css = "gendata";
	$chk->s_name="sse_s1";
	$chk->is_color= true;
	$chk->id = $sse_s1;
	$chk->arr = sfs_text("喜愛困難科目");
	$chk->cols=6;
	$chk->do_select();
	?>
	</td>
</tr>
<tr>
	<td align="right" CLASS="title_sbody1"><?php echo $field_data[sse_s2][d_field_cname] ?></td>
	<td CLASS="gendata">
	<?php
	$chk->id = $sse_s2;
	$chk->s_name="sse_s2";
	$chk->css = "gendata";
	$chk->arr = sfs_text("喜愛困難科目");
	$chk->cols=6;
	$chk->do_select();
	?>
	</td>
</tr>
<tr>
	<td align="right" CLASS="title_sbody1"><?php echo $field_data[sse_s3][d_field_cname] ?></td>
	<td CLASS="gendata">
	<?php
	$chk->id = $sse_s3;
	$chk->s_name="sse_s3";
	$chk->css = "gendata";
	$chk->arr = sfs_text("特殊才能");
	$chk->cols=6;
	$chk->do_select();
	?>
	</td>
</tr>
<tr>
	<td align="right" CLASS="title_sbody1"><?php echo $field_data[sse_s4][d_field_cname] ?></td>
	<td CLASS="gendata">
	<?php
	$chk->id = $sse_s4;
	$chk->s_name="sse_s4";
	$chk->css = "gendata";
	$chk->arr = sfs_text("興趣");
	$chk->cols=6;
	$chk->do_select();
	?>
	</td>
</tr>
<tr>
	<td align="right" CLASS="title_sbody1"><?php echo $field_data[sse_s5][d_field_cname] ?></td>
	<td CLASS="gendata">
	<?php
	$chk->id = $sse_s5;
	$chk->s_name="sse_s5";
	$chk->css = "gendata";
	$chk->arr = sfs_text("生活習慣");
	$chk->cols=6;
	$chk->do_select();
	?>
	</td>
</tr>
<tr>
	<td align="right" CLASS="title_sbody1"><?php echo $field_data[sse_s6][d_field_cname] ?></td>
	<td CLASS="gendata">
	<?php
	$chk->id = $sse_s6;
	$chk->s_name="sse_s6";
	$chk->css = "gendata";
	$chk->arr = sfs_text("人際關係");
	$chk->cols=6;
	$chk->do_select();
	?>
	</td>
</tr>
<tr>
	<td align="right" CLASS="title_sbody1"><?php echo $field_data[sse_s7][d_field_cname] ?></td>
	<td CLASS="gendata">
	<?php
	$chk->id = $sse_s7;
	$chk->s_name="sse_s7";
	$chk->css = "gendata";
	$chk->arr = sfs_text("外向行為");
	$chk->cols=6;
	$chk->do_select();
	?>
	</td>
</tr>
<tr>
	<td align="right" CLASS="title_sbody1"><?php echo $field_data[sse_s8][d_field_cname] ?></td>
	<td CLASS="gendata">
	<?php
	$chk->id = $sse_s8;
	$chk->s_name="sse_s8";
	$chk->css = "gendata";
	$chk->arr = sfs_text("內向行為");
	$chk->cols=6;
	$chk->do_select();
	?>
	</td>
</tr>
<tr>
	<td align="right" CLASS="title_sbody1"><?php echo $field_data[sse_s9][d_field_cname] ?></td>
	<td CLASS="gendata">
	<?php
	$chk->id = $sse_s9;
	$chk->s_name="sse_s9";
	$chk->css = "gendata";
	$chk->arr = sfs_text("學習行為");
	$chk->cols=6;
	$chk->do_select();
	?>
	</td>
</tr>
<tr>
	<td align="right" CLASS="title_sbody1"><?php echo $field_data[sse_s10][d_field_cname] ?></td>
	<td CLASS="gendata">
	<?php
	$chk->id = $sse_s10;
	$chk->s_name="sse_s10";
	$chk->css = "gendata";
	$chk->arr = sfs_text("不良習慣");
	$chk->cols=6;
	$chk->do_select();
	?>
	</td>
</tr>
<tr>
	<td align="right" CLASS="title_sbody1"><?php echo $field_data[sse_s11][d_field_cname] ?></td>
	<td CLASS="gendata">
	<?php
	$chk->id = $sse_s11;
	$chk->s_name="sse_s11";
	$chk->css = "gendata";
	$chk->arr = sfs_text("焦慮行為");
	$chk->cols=6;
	$chk->do_select();
	?>
	</td>
</tr>
<tr>
<td class=title_mbody colspan=5 align=center background="images/tablebg.gif" >
	<?php 
		//檢查是否為可填寫月份
		$eduh_months="[,$eduh_months,]";
		$pos=strpos($eduh_months,$curr_month,1);
		if($pos) echo "&nbsp;&nbsp;<input type=submit name=do_key value =\"$editBtn\" onClick=\"return checkok();\">&nbsp;&nbsp;";
			else echo "◎學校設定可填寫月份：$m_arr[eduh_months] ，目前並未開放修改！";
	?>
</td>	
</tr>
</table>
</form>
<!------------------- 輸入表單結束 ------------------------------>
</td>
</tr>
</table>
<?php
} else {
	echo "該生已不在籍！";
}

//印出表尾
foot();
?>
