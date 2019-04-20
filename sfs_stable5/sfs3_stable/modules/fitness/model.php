<?php
// $Id: model.php 8674 2015-12-25 02:45:35Z qfon $
// 取得設定檔
include "config.php";
if ($_POST[model_id]=="") $_POST[model_id]="0";
for($i=1;$i<20;$i++) {
	$p=$i*5;
	//$p=$i;
	if ($p<25) $c="#e6ebff";
	if ($p>20 && $p<50) $c="#f0f4f7";
	if ($p>45 && $p<75) $c="#ebffcc";
	if ($p>70 && $p<85) $c="#faebff";
	if ($p>80) $c="#ffffde";
	$p_arr[$p]=$c;
}
$smarty->assign("p_arr",$p_arr);

$query="select * from fitness_mod where grade='".intval($_POST[model_id])."' order by sex,age";
$res=$CONN->Execute($query);
while(!$res->EOF) {
	$r[$res->fields[sex]][]=$res->FetchRow();
}
$smarty->assign("rowdata",$r);

$model_arr=array("0"=>"身高","1"=>"體重","2"=>"坐姿體前彎","3"=>"仰臥起坐60秒","4"=>"定立跳遠","5"=>"心肺適能");
$model_menu = new drop_select();
$model_menu->s_name ="model_id";
$model_menu->has_empty = false;
$model_menu->id = $_POST[model_id];
$model_menu->arr = $model_arr;
$model_menu->is_submit = true;
$smarty->assign("model_menu",$model_menu->get_select());
$smarty->assign("model_arr",$model_arr);
$smarty->assign("k_arr",array("0"=>"公分","1"=>"公斤","2"=>"公分","3"=>"次","4"=>"公分","5"=>"秒"));

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
$smarty->assign("module_name","體適能常模");
$smarty->assign("SFS_MENU",$menu_p);
$smarty->assign("sel_year",$sel_year);
$smarty->assign("sel_seme",$sel_seme);
$smarty->assign("IS_JHORES",$IS_JHORES);
$smarty->display("fitness_model.tpl");
?>
