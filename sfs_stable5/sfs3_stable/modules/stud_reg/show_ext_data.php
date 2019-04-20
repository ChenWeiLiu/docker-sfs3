<?php 
// 載入設定檔
include "stud_reg_config.php";
// 認證檢查
sfs_check();

// 修改
if (isset($_POST['edit_id'])) {
	$ext_data_name = $_POST['ext_data_name'];
	$doc = $_POST['doc'];
	$id = (int) $_POST['edit_id'];
	$query = "UPDATE stud_ext_data_menu SET 
					ext_data_name='$ext_data_name',
					doc = '$doc'
					WHERE id = $id";
	$CONN->Execute($query);
}
// 新增
if (isset($_POST['act']) and $_POST['act'] == '確定新增') {
	$ext_data_name = $_POST['ext_data_name'];
	$doc = $_POST['doc'];	;
	$query = "INSERT INTO stud_ext_data_menu (ext_data_name,doc) 
	VALUES('$ext_data_name', '$doc')";
	
	$CONN->Execute($query);
}

$ext_data_name = '';
$doc = '';
$edit_id = '';

switch ($_GET['key']) {
	case 'edit':
		
		$id = (int) $_GET['id'];
		$query  = "SELECT * FROM stud_ext_data_menu WHERE id=$id ";
		$result = $CONN->Execute($query);
		
		$ext_data_name = $result->fields['ext_data_name'];
		$doc = $result->fields['doc'];
		$edit_id = $id;
		break;
		
	case 'delete':
		$id = (int) $_GET['id'];
		//刪除主項
		$query = "delete from stud_ext_data_menu where id='$id'";
		$result = $CONN->Execute($query) or die($query);
		//刪除紀錄資料
		$query = "delete from stud_ext_data where mid='$id'";		
		$result = $CONN->Execute($query) or die($query);
		break;
	
	case 'deleteRecord':
		//刪除紀錄資料
		$id = (int) $_GET['id'];
		$query = "delete from stud_ext_data where mid='$id'";
		
		$result = $CONN->Execute($query) or die($query);
		break;
}


?>

<?php
// 檔頭
 head();
 print_menu($menu_p);
?>
<style>
.main-table {border: #000  solid 1px; width:100%}
</style>
<form name="myform" action="<?php echo $_SERVER['PHP_SELF']?>" method="post">

<table class="main-table main_body">
<thead>
<tr><th colspan="2" class="title_mbody">額外資料項目</th>
</tr>
</thead>
<tbody>
<tr><td class="title_sbody1">資料名稱</td>
<td><input type="text" name="ext_data_name"  size="30" value="<?php echo $ext_data_name?>" /></td>
</tr>
<tr><td class="title_sbody1">填寫說明</td>
<td><textarea rows="5" cols="50" name="doc"><?php echo $doc?></textarea></td>
</tr>
<?php if ($edit_id):?>
<tr>
<th colspan="2" class="title_mbody">
<input type="hidden" name="edit_id" value="<?php echo $edit_id?>" />
<input type="submit" value="確定修改" />
</th>
</tr>
<?php else:?>
<tr>
<th colspan="2" class="title_mbody">
<input type="submit"  name="act" value="確定新增" />
</th>
</tr>
<?php endif?>
</tbody>
</table>

</form>
<br/>
<table class="main-table main_body" >
<thead>
<tr>
<td align="center" class="title_top1" colspan="4">舊有項目</td>
</tr>
</thead>
<tbody>
<tr><td>代號</td><td>項目</td><td>填寫說明</td><td>動作</td></tr>
<?php
	$query = "select * from stud_ext_data_menu order by id desc";
	$result = $CONN->Execute($query) or die ($query);
	
?>
<?php foreach($result as $i=>$row):?>
<tr class="<?php if ($i %2 == 0) echo "nom_1"; else echo "nom_2" ?>">
<td><?php echo $row['id']?></td>
<td><?php echo $row['ext_data_name']?></td>
<td><?php echo $row['doc']?></td>
<td>
<a href="<?php echo $_SERVER['PHP_SELF']?>?key=edit&id=<?php echo $row['id']?>">編輯</a> |
<a href="<?php echo $_SERVER['PHP_SELF']?>?key=print&id=<?php echo $row['id']?>#listData">列表查看</a> |  
<a href="<?php echo $_SERVER['PHP_SELF']?>?key=delete&id=<?php echo $row['id']?>" onclick="return confirm('確定剛除')">刪除本項目</a> |
<a href="<?php echo $_SERVER['PHP_SELF']?>?key=deleteRecord&id=<?php echo $row['id']?>" onclick="return confirm('您確定要刪除項目[<?php echo $row['id']?>][<?php echo $row['ext_data_name']?>]的學生相關紀錄嗎?')">刪除紀錄資料</a> 
</td>
</tr>
<?php endforeach;?>
</tbody>
</table>

<?php  if ($_GET['key'] =="print"):?>
<?php $class_name_arr = class_base() ;	//班級陣列 
$id = (int) $_GET['id'];
$query = "SELECT a.*, b.*, c.stud_name, c.curr_class_num FROM stud_ext_data a LEFT JOIN stud_ext_data_menu b ON a.mid=b.id LEFT JOIN stud_base c ON a.stud_id=c.stud_id 
	WHERE b.id=$id AND c.stud_study_cond IN (0,5)  ORDER BY a.stud_id DESC"; 
$result = $CONN->Execute($query) or die($query);
?>
<?php if ($result->RecordCount() > 0):?>
<a name="listData"><h2><?php echo $result->fields['ext_data_name']?></h2></a>
<table border=1 >
<thead>
<tr><th>學號</th><th>班級</th><th>座號</th><th>姓名</th><th>說明</th></tr>
</thead>
<tbody>
<?php foreach($result as $row): ?>
<?php
	 $curr_class_num = $row["curr_class_num"];
	$num = substr($curr_class_num,-2) ;
	$class_name = $class_name_arr[substr($curr_class_num,0,3)] ;
 ?>
	<tr>
		<td><?php echo $row['stud_id']?></td>
		<td><?php echo $class_name?></td>
		<td><?php echo $num?></td>
		<td><?php echo $row['stud_name']?></td>
		<td><?php echo $row['ext_data']?></td>
<?php endforeach;?>
</tbody>
</table>
<?php else:?>
<h2><a name="listData">無資料</a></h2>
<?php endif?>
<?php endif?>

<?php foot()?>