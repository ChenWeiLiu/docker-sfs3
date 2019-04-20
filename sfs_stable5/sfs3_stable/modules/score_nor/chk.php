<?php

// $Id: chk.php 5310 2009-01-10 07:57:56Z hami $

// 取得設定檔
include "config.php";

sfs_check();


if ($_POST['df_item']=="") $_POST['df_item']=($IS_JHORES)?"default_jh":"default_es";
if ($_POST['year_seme']=="") $_POST['year_seme']=sprintf("%03d",curr_year()).curr_seme();
if($_POST['year_seme']==sprintf("%03d",curr_year()).curr_seme()) $current=sprintf("%03d",curr_year()).curr_seme();

$sel_year=intval(substr($_POST['year_seme'],0,-1));
$sel_seme=intval(substr($_POST['year_seme'],-1,1));

//刪除所有已存在資料
if ($_POST['del_record'])
	$res=$CONN->Execute("delete from stud_seme_score_nor_chk where seme_year_seme='".$_POST['year_seme']."'");

//複製至本學期
if ($_POST['copy_to_cur'])
{
	//檢驗本學期是否已有資料
	$query="select count(*) from score_nor_chk_item where year='".curr_year()."' and seme='".curr_seme()."'";
	$res=$CONN->Execute($query);
	if($res->fields[0]) echo "<script language=\"Javascript\"> alert (\"本學期已經有設定了，系統禁止您進行複製！\")</script>";	
	 else {
		//取得列示學期資料
		$query="select * from score_nor_chk_item where year='$sel_year' and seme='$sel_seme'";	
		$res=$CONN->Execute($query);
		//製作 INSERT SQL
		$copy_data="INSERT INTO score_nor_chk_item(year,seme,main,sub,item) VALUES ";
		while(!$res->EOF) {
			$copy_data.="(".curr_year().",".curr_seme().",".$res->fields['main'].",".$res->fields['sub'].",'".addslashes($res->fields['item'])."'),";
			$res->MoveNext();
		}
		$copy_data=substr($copy_data,0,-1);
		$res=$CONN->Execute($copy_data) or user_error("複製失敗！<br>$copy_data",256);
		$current=sprintf("%03d",curr_year()).curr_seme();
		$_POST['year_seme']=$current;
		
		echo "<script language=\"Javascript\"> alert (\"已經完成複製並轉換顯示至學期 $current 了！ 請檢視!!\")</script>";
	}
}
$smarty->assign("current",$current);

//統計本學期記錄筆數
$query="select count(*) from stud_seme_score_nor_chk where seme_year_seme='".$_POST['year_seme']."'";
$res=$CONN->Execute($query);
if ($res->fields[0]>0) {
	$smarty->assign("msg_str","<font color=\"red\">本學期已有".$res->fields[0]."筆記錄存在，想修改或調整項目必須先刪除所有已存在資料。</font>");
} else {
	if ($_POST['default']) {
		while(list($i,$v)=each($item_arr[$_POST['df_item']])) {
			while(list($j,$vv)=each($v)) {
				$CONN->Execute("insert into score_nor_chk_item (year,seme,main,sub,item) values ('$sel_year','$sel_seme','$i','$j','".addslashes($item_arr[$_POST[df_item]][$i][$j])."')");
			}
		}
	}

	//儲存項目
	if ($_POST['act']=="save") {
		$CONN->Execute("update score_nor_chk_item set item='$_POST[item_value]' where year='$sel_year' and seme='$sel_seme' and main='$_POST[main]' and sub='$_POST[sub]'");
	}

	//插入項目
	if ($_POST['act']=="insert") {
		$CONN->Execute("insert into score_nor_chk_item (year,seme,main,sub,item) values ('$sel_year','$sel_seme','$_POST[main]','$_POST[sub]','$_POST[item_value]')");
	}

	//刪除項目
	if ($_POST['act']=="del") {
		if ($_POST['sub']>0) {
			$CONN->Execute("delete from score_nor_chk_item where year='$sel_year' and seme='$sel_seme' and main='$_POST[main]' and sub='$_POST[sub]'");
			$CONN->Execute("update score_nor_chk_item set sub=sub-1 where year='$sel_year' and seme='$sel_seme' and main='$_POST[main]' and sub>'$_POST[sub]' order by main,sub");
		} else {
			$CONN->Execute("delete from score_nor_chk_item where year='$sel_year' and seme='$sel_seme' and main='$_POST[main]'");
			$CONN->Execute("update score_nor_chk_item set main=main-1 where year='$sel_year' and seme='$sel_seme' and main>'$_POST[main]' order by main,sub");
		}
	}

	//項目上移
	if ($_POST['act']=="up") {
		if ($_POST['sub']>0) {
			$CONN->Execute("update score_nor_chk_item set sub='99' where year='$sel_year' and seme='$sel_seme' and main='$_POST[main]' and sub='$_POST[sub]' order by main,sub");
			$CONN->Execute("update score_nor_chk_item set sub=sub+1 where year='$sel_year' and seme='$sel_seme' and main='$_POST[main]' and sub='".($_POST[sub]-1)."' order by main,sub");
			$CONN->Execute("update score_nor_chk_item set sub=".($_POST[sub]-1)." where year='$sel_year' and seme='$sel_seme' and main='$_POST[main]' and sub='99' order by main,sub");
		} else {
			$CONN->Execute("update score_nor_chk_item set main='99' where year='$sel_year' and seme='$sel_seme' and main='$_POST[main]' order by main,sub");
			$CONN->Execute("update score_nor_chk_item set main=main+1 where year='$sel_year' and seme='$sel_seme' and main='".($_POST[main]-1)."' order by main,sub");
			$CONN->Execute("update score_nor_chk_item set main=".($_POST[main]-1)." where year='$sel_year' and seme='$sel_seme' and main='99' order by main,sub");
		}
	}

	//項目下移
	if ($_POST['act']=="down") {
		if ($_POST['sub']>0) {
			$CONN->Execute("update score_nor_chk_item set sub='99' where year='$sel_year' and seme='$sel_seme' and main='$_POST[main]' and sub='$_POST[sub]' order by main,sub");
			$CONN->Execute("update score_nor_chk_item set sub=sub-1 where year='$sel_year' and seme='$sel_seme' and main='$_POST[main]' and sub='".($_POST[sub]+1)."' order by main,sub");
			$CONN->Execute("update score_nor_chk_item set sub=".($_POST[sub]+1)." where year='$sel_year' and seme='$sel_seme' and main='$_POST[main]' and sub='99' order by main,sub");
		} else {
			$CONN->Execute("update score_nor_chk_item set main='99' where year='$sel_year' and seme='$sel_seme' and main='$_POST[main]' order by main,sub");
			$CONN->Execute("update score_nor_chk_item set main=main-1 where year='$sel_year' and seme='$sel_seme' and main='".($_POST[main]+1)."' order by main,sub");
			$CONN->Execute("update score_nor_chk_item set main=".($_POST[main]+1)." where year='$sel_year' and seme='$sel_seme' and main='99' order by main,sub");
		}
	}

	if ($_POST['del_all']) {
		$CONN->Execute("delete from score_nor_chk_item where year='$sel_year' and seme='$sel_seme'");
	}
}

//學年選單
$sel1 = new drop_select();
$sel1->s_name="year_seme";
$sel1->id= $_POST['year_seme'];
$sel1->arr = get_class_seme();
$sel1->has_empty = false;
$sel1->is_submit = true;

$smarty->assign("year_seme_sel",$sel1->get_select());

//預設選目選單
$sel1 = new drop_select();
$sel1->s_name="df_item";
$sel1->id= $_POST['df_item'];
$sel1->arr = $item_sel;
$sel1->has_empty = false;
$sel1->is_submit = true;
$smarty->assign("item_sel",$sel1->get_select());

$query="select main,sub,item from score_nor_chk_item where year='$sel_year' and seme='$sel_seme' order by main,sub";
$res=$CONN->Execute($query);
$smarty->assign("rowdata",$res->GetRows());
$smarty->assign("current_records",$res->recordcount());

$query="select count(item) as num from score_nor_chk_item where year='$sel_year' and seme='$sel_seme' group by main order by main,sub";
$res=$CONN->Execute($query);
$smarty->assign("rownum",$res->GetRows());
$query="select count(item) from score_nor_chk_item where year='$sel_year' and seme='$sel_seme' and sub='0'";
$res=$CONN->Execute($query);
$smarty->assign("maxnum",$res->fields[0]);
$query="select count(item) as num from score_nor_chk_item where year='$sel_year' and seme='$sel_seme' group by main";
$res=$CONN->Execute($query);
$smarty->assign("submax",$res->GetRows());

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
$smarty->assign("module_name","設定學生日常生活檢核表");
$smarty->assign("SFS_MENU",$menu_p);
$smarty->display("score_nor_chk.tpl");
?>
