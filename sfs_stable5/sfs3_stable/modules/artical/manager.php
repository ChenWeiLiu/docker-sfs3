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

switch ($_REQUEST['act']) {
	case '確定' :
		$teacher_sn = $_SESSION[session_tea_sn];
		$title = filter_input(INPUT_POST, 'title',FILTER_SANITIZE_STRIPPED);
		$start_date = filter_input(INPUT_POST, 'start_date' );
		$end_date = filter_input(INPUT_POST, 'end_date');
		$is_publish = filter_input(INPUT_POST, 'is_publish');
		$id = filter_input(INPUT_POST, 'id');
		if ($id)
		$query = "UPDATE artical SET
		title='$title', start_date='$start_date', end_date='$end_date',
		teacher_sn='$teacher_sn', is_publish='$is_publish'
		WHERE id=$id";
		else
		$query = "INSERT INTO artical(title,start_date, end_date, teacher_sn,is_publish)
			VALUES('$title','$start_date','$end_date','$teacher_sn','$is_publish')";
		$CONN->Execute($query) or die($query);
		break;
	// 刪除
	case 'delete':
		$id = (int) $_POST['id'];
		$query = "DELETE FROM artical WHERE id=$id";
		$CONN->Execute($query) or trigger_Error('SQL 錯誤');
		echo 1;
		exit;
		break;
	case 'get-form' :
		header('Content-type:text/html; charset=big5');
		$id = (int)$_POST['id'];
		if ($id) {
			$query = "SELECT * FROM artical WHERE id=$id";
			$res = $CONN->Execute($query) or trigger_error('SQL 錯誤');

		}
		include('manager_form.php');
		exit;
		break;

}

if (isset($_POST['year']))
$year = $_POST['year'];
else
$year = date('Y');

// 計算學期別
$query = "SELECT DATE_FORMAT(start_date, '%Y') AS year FROM artical GROUP BY year ";
$resYear = $CONN->Execute($query) or trigger_error('SQL 錯誤');

head();
print_menu($menu_p);
?>

<style>
.ui-widget {
	font-size: 14px;
}

#managerDiv {
	border: #ccc solid thin;
	padding: 5px;
}
.pblish-0 {background:#ff0}
.string-border {border:1px #ade dotted; padding:1px;margin:auto 5px}
.ui-corner-all {border:#ccc solid 1px; padding: 1px}
.error {color:red; font-weight: bold;}
</style>
<script type="text/javascript" src="<?php echo $SFS_PATH_HTML?>javascripts/ui/jquery.ui.widget.js"></script>
<script type="text/javascript" src="<?php echo $SFS_PATH_HTML?>javascripts/ui/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="<?php echo $SFS_PATH_HTML?>javascripts/ui/i18n/jquery.ui.datepicker-zh-TW.js"></script>
	<script type="text/javascript" src="<?php echo $SFS_PATH_HTML?>javascripts/external/jquery-migrate-1.4.1.min.js"></script>
	<script type="text/javascript" src="<?php echo $SFS_PATH_HTML?>javascripts/external/jquery.bgiframe.js"></script>
<script type="text/javascript" src="<?php echo $SFS_PATH_HTML?>javascripts/ui/jquery.ui.widget.js"></script>
<script type="text/javascript" src="<?php echo $SFS_PATH_HTML?>javascripts/ui/jquery.ui.mouse.js"></script>
<script type="text/javascript" src="<?php echo $SFS_PATH_HTML?>javascripts/ui/jquery.ui.button.js"></script>
<script type="text/javascript" src="<?php echo $SFS_PATH_HTML?>javascripts/ui/jquery.ui.draggable.js"></script>
<script type="text/javascript" src="<?php echo $SFS_PATH_HTML?>javascripts/ui/jquery.ui.position.js"></script>
<script type="text/javascript" src="<?php echo $SFS_PATH_HTML?>javascripts/ui/jquery.ui.resizable.js"></script>
<script type="text/javascript" src="<?php echo $SFS_PATH_HTML?>javascripts/ui/jquery.ui.dialog.js"></script>
<script type="text/javascript" src="<?php echo $SFS_PATH_HTML?>javascripts/ui/jquery.effects.core.js"></script>

<script src="<?php echo $SFS_PATH_HTML?>javascripts/external/jquery.metadata.js" type="text/javascript"></script>
<script src="<?php echo $SFS_PATH_HTML?>javascripts/jquery/jquery.validate.min.js" type="text/javascript"></script>
<script src="<?php echo $SFS_PATH_HTML?>javascripts/jquery/messages_tw.js" type="text/javascript"></script>


<script>
$(function() {
	$("#managerDiv").dialog({
		 modal: true,
		 width: 400,
		 position: ['center',100],
		 autoOpen: false ,
		 title : '期別編修'
		});

	$("#create-artical").click(function(){
		$.post('manager.php', {act: 'get-form'},function(data){
			$("#managerDiv").html(data);
			$("#managerDiv").dialog('open');
		});

		return false;
	});

	$(".deleteBtn").click(function(){
		if (confirm('確定刪除？')) {
			var id= $(this).attr('id').substr(4);
			$.post('manager.php',{act:'delete' , id:id},function(data){
			if (data ==1)
				$("#del-"+id).parent().parent().remove();
			});
		}
	});

	$(".editBtn").click(function(){
		var id = $(this).attr('id').substr(5);
		$.post('manager.php',{act:'get-form', id:id},function(data){
			$("#managerDiv").html(data);
			$("#managerDiv").dialog('open');
		});
	});

	$("#sel-year").change(function(){
		$("#select-form").submit();
	});

});
</script>
<div style="margin:5px">
<form method="post" action="" id="select-form">
選擇年度 : <select name="year" id="sel-year">
<?php foreach($resYear as $row):?>
<option value="<?php echo $row['year']?>" <?php if ($year==$row['year']):?>selected="selected"<?php endif;?> >
<?php echo $row['year']?> 年度
</option>
<?php endforeach?>
</select>
<button id="create-artical">建立新的期刊</button>
</form>
</div>

<div id="articalList">
<table class="ui-widget ui-widget-content ui-corner-all" style="background:#ffe; width:720px;text-align:center;">
	<thead>
		<tr class="ui-widget-header">
			<th>編號</th>
			<th>期別</th>
			<th>投稿期間</th>
			<th>是否發布</th>
			<th>編修</th>
		</tr>
	</thead>
	<?php
	$query = "SELECT * FROM artical  WHERE  DATE_FORMAT(start_date, '%Y')=$year ORDER BY is_publish ,start_date DESC ";
	$res = $CONN->Execute($query);
	if ($res->RecordCount()>0):
	?>
	<tbody>
	<?php foreach($res as $row):?>
		<tr class="pblish-<?php echo $row['is_publish']?>">
			<td><?php echo $row['id']?></td>
			<td><?php echo $row['title']?></td>
			<td><span class="string-border"><?php echo $row['start_date']?></span>~<span class="string-border"><?php echo $row['end_date']?></span></td>
			<td><?php echo ($row['is_publish'])?'已發布':'未發布'?></td>
			<td>
			   <a href="#" class="editBtn ui-corner-all"     id="edit-<?php echo $row['id']?>">編修</a>
			   <a href="#" class="deleteBtn ui-corner-all"	id="del-<?php echo $row['id']?>">刪除</a>
			  </td>
		</tr>
		<?php endforeach?>
	</tbody>
	<?php endif;?>
</table>



</div>

<div class="ui-widget ui-content ui-corner-all" id="managerDiv">

</div>
<?php foot()?>