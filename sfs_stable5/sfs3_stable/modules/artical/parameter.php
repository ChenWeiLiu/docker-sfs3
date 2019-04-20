<?php
require "config.php";
sfs_check();
if (!checkid($_SERVER[SCRIPT_FILENAME],1)){
	head();
	print_menu($menu_p);
	echo '<h1>非管理者,不能操作此作業</h1>';
	foot();
	exit;
}
if ($_POST['act'] == '確定') {
	$arr = array();
	$arr['title'] = filter_input(INPUT_POST, 'title');
	$arr['items_per_page'] = filter_input(INPUT_POST, 'items_per_page' ,FILTER_SANITIZE_NUMBER_INT);
	$arr['image_width'] = filter_input(INPUT_POST, 'image_width' ,FILTER_SANITIZE_NUMBER_INT);
	$query = "UPDATE artical_paramter SET paramter='".serialize($arr)."'";
	$CONN->Execute($query);
}


$query = "SELECT * FROM artical_paramter";
$res = $CONN->Execute($query);
$param = $res->fields['paramter'];
if($param=='')
$param = $PARAMSTER;
else
$param = unserialize($param);



head();

print_menu($menu_p);
?>
<style>
.ui-widget-header {font-size:16px;padding:5px;margin-top:5px;}
.ui-widget-content {padding:5px;}
</style>
<div class="ui-widget-header ui-corner-top">系統參數設定</div>
<div class="ui-widget-content ui-corner-bottom">
<form action="" method="post" id="setForm">
<dl>
	<dt>期刊名稱</dt>
	<dd><input type="text" name="title" size="20"
		value="<?php echo $param['title']?>" /></dd>
	<dt>每頁文章筆數</dt>
	<dd><input type="text" name="items_per_page" size="5"
		value="<?php echo $param['items_per_page']?>" /></dd>
	<dt>圖檔寬度</dt>
	<dd><input type="text" name="image_width" size="5"
		value="<?php echo $param['image_width']?>" /></dd>
	<dt></dt>
	<dd><input type="submit" name="act" id="act" value="確定" /></dd>
</dl>
</form>
</div>
<?php
foot();