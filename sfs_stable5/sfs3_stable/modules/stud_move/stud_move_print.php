<?php
//$Id: stud_move_print.php 7969 2014-03-31 02:16:16Z smallduh $
include "stud_move_config.php";
include "../../include/sfs_oo_zip2.php";

//認證
sfs_check();

$m_arr = get_sfs_module_set();
extract($m_arr, EXTR_OVERWRITE);

if (empty($_POST[year_id]))
	$year_id=sprintf("%03d",curr_year());
else
	$year_id=$_POST[year_id];
if (empty($_POST[class_year_id])){
	if ($IS_JHORES)
		$class_year_id = 9;
	else
		$class_year_id = 6;
} else 
	$class_year_id=$_POST[class_year_id];

//若有選擇學年學期，進行分割取得學年及學期
if(!empty($_POST[year_seme])){
	$sel_year=intval(substr($_POST[year_seme],0,-1));
	$sel_seme=substr($_POST[year_seme],-1,1);
	$year_seme=$_POST[year_seme];
} else {
	$sel_year=curr_year(); //目前學年
	$sel_seme=curr_seme(); //目前學期
	$year_seme=sprintf("%03d",$sel_year).$sel_seme;
}
$year_arr=get_class_year();
$curr_seme=intval($year_id."2");
$move_kind_arr=array("0"=>" -- 請選擇 -- ","2"=>"轉入","8"=>"調校","7"=>"出國","11"=>"死亡","12"=>"中輟","3"=>"中輟復學","14"=>"轉學復學","13"=>"新生入學");
$move_kind=$_POST[move_kind];

//按鍵處理
switch($_REQUEST[do_key]) {
	case " 列印封面 ":
	if ($move_kind=="13")
			$oo_path="new_cover";
		else
			$oo_path="move_cover";
		include "stud_move_cover.php";
	break;
	case " 列印封底內頁 ":
		if ($move_kind=="13")
			$oo_path="new_bottom";
		else
			$oo_path="move_bottom";
		include "stud_move_cover.php";
	break;
	case " 列印報表 ":
		$newin=0;
		$all_move_id="";
		if (count($_POST[choice])>0) {
			foreach($_POST[choice] as $k=>$v) {
				if ($move_kind=="13") {
					$vv=explode("_",$v);
					$query="select * from stud_move where move_year_seme='$vv[0]' and move_kind='13'";
					$res=$CONN->Execute($query);
					while (!$res->EOF) {
						$move_id=$res->fields['move_id'];
						$all_move_id.="'".$move_id."',";
						$res->MoveNext();
					}
					$newin=1;
				} else {
					$all_move_id.="'".$k."',";
					$move_id=$k;
				}
			}
			$all_move_id=substr($all_move_id,0,-1);
			if ($move_kind=="3" || $move_kind=="4" || $move_kind=="14") {
				$oo_path="move_out";
				$move_str="復學";
			} elseif ($move_kind=="1" || $move_kind=="7" || $move_kind=="8") {
				$oo_path="move_out";
				$move_str="轉出";
			} elseif ($move_kind=="6" || $move_kind=="11" || $move_kind=="12") {
				$oo_path="move_out";
				$move_str="輟學";
			} elseif ($move_kind=="2"){
                                $oo_path="move_out";
				$move_str="轉入";
                        } elseif ($move_kind=="13") {
				$oo_path="move_in";
				$move_str="";
				$chk_year="and move_year_seme like '".$sel_year."%'";
			}
			$seme_year_seme=sprintf("%04d",$curr_seme);
			include "stud_move_list.php";
		}
	break;
	case " 填入文字號 ":
		if (count($_POST[choice])>0) {
			reset ($_POST[choice]);
			while(list($k,$v)=each($_POST[choice])) {
				$CONN->Execute("update stud_move set move_c_unit='$_POST[move_c_unit]',move_c_date='$_POST[move_c_date]',move_c_word='$_POST[move_c_word]',move_c_num='$_POST[move_c_num]',update_time='".date("Y-m-d G:i:s")."',update_id='$_SESSION[session_log_id]',update_ip='".getip()."' where move_id='$k'");
			}
		}
	break;
}

//異動類別選單
$sel1=new drop_select();
$sel1->s_name="move_kind";
$sel1->id=$move_kind;
$sel1->arr=$move_kind_arr;
$sel1->has_empty=false;
$sel1->is_submit=true;
$smarty->assign("move_kind_sel",$sel1->get_select());

switch ($move_kind) {
	case "5":
	case "13":
		$smarty->assign("form_kind","2");
		$smarty->assign("cseme","");
		$query="select distinct concat(move_c_unit,move_c_word,move_c_num) as dif,count(move_id) as num,left(move_year_seme,length(move_year_seme)-1) as move_year,move_year_seme,move_date,move_c_unit,move_c_date,move_c_word,move_c_num from stud_move where move_kind='$move_kind' group by move_year_seme order by move_date desc";
	break;
	default:
		$smarty->assign("form_kind","1");
		$smarty->assign("cseme","本學期");
		//學期選單
		$sel1=new drop_select();
		$sel1->s_name="year_seme";
		$sel1->id=$year_seme;
		$sel1->arr=get_class_seme();
		$sel1->has_empty=false;
		$sel1->is_submit=true;
		$smarty->assign("year_seme_sel",$sel1->get_select());
		$query="select a.*,b.stud_name,b.stud_birthday from stud_move a ,stud_base b where a.student_sn=b.student_sn and a.move_year_seme='".intval($year_seme)."' and a.move_kind='$move_kind' order by a.move_date desc,a.stud_id desc";
}

//取出所有記錄
$res=$CONN->Execute($query) or die($query);
$smarty->assign("stud_move",$res->GetRows());

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
$smarty->assign("module_name","異動報表作業");
$smarty->assign("SFS_MENU",$student_menu_p);
$smarty->assign("default_unit",$default_unit);
$smarty->assign("default_word",$default_word);
$smarty->display("stud_move_stud_move_print.tpl");
?>
