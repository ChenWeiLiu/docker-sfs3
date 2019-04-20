<?php
require "config.php";
$class_base = class_base();
if (isset($_POST['id'])) {
	$id = (int)$_POST['id'];
	sfs_check();
	$man = checkid($_SERVER[SCRIPT_FILENAME],1);
	$query = "SELECT id,teacher_sn ,photo_ext FROM artical_detail WHERE id=$id";
	 $res = $CONN->Execute($query);

	if ($_SESSION['session_tea_sn'] == $res->fields['teacher_sn'] or $man) {
		$CONN->Execute("DELETE FROM artical_detail WHERE id=$id");
		$file_name = $uploadPath.$id.'.'.$res->fields['photo_ext'];
		if (is_file($file_name))
		unlink($file_name);
		header("Location: list.php");
	}
}

$id = (int)$_GET['id'];
if (!$_COOKIE['articalCount_'.$id]) {
	setcookie('articalCount_'.$id,1);
	$query = "UPDATE artical_detail SET hits=hits+1 WHERE id=$id";
	$res = $CONN->Execute($query);
}

$query = "SELECT a.* , b.stud_name FROM artical_detail a LEFT JOIN stud_base b ON a.student_sn=b.student_sn WHERE a.id=$id ";
$res = $CONN->Execute($query);
$row = $res->fetchRow();
$className = $class_base[substr($row['class_number'],0 ,3)];


head();
?>
<style>
.img-position-0 {float:left; margin:8px; border:#ccc solid thin;padding:3px}
.img-position-1 {float:right; margin:8px; border:#ccc solid thin ;padding:3px}

#authorDiv {margin: 20px}
#artical {color:#00d;font-size:19px;font-family:"標楷體";line-height:150%}
.leafDiv{background-image: url(images/leaf.gif);height:15px; background-repeat: repeat-x;}
.houseDiv{background-image: url(images/house.gif);height:26px; background-repeat: repeat-x;margin:10px;}
#toolTable td {
	background-image: url(images/tool.gif);
	background-repeat: no-repeat;
	height: 60px;
	width:140px;
	text-align:center;
}

#toolTable td span{
	position: relative;
	left: 0px;
	top: -10px;
}

</style>
<script>
$(function(){
	$("#deleleBtn").click(function(){
		if (confirm('確定刪除這篇文章? ')){
			$("#myform").submit();
		}
	});

	$("#editBtn").click(function(){

		$("#myform").attr('action','sign.php').submit();

	});
});
</script>

<div id="artical">
<h1 style="text-align:center"><?php echo $row['title']?></h1>
<div class="leafDiv"></div>
<div id="authorDiv">
<span style="text:left;font-size:20px;font-weight: bold">【作者:<?php echo $className?>
 <a href="showStudent.php?selStudent=<?php echo $row['student_sn'] ?>"><?php echo $row['stud_name']?></a>】</span>
<span style="float:right; color:blue; font-style: italic">發表時間: <?php echo $row['publish_time']?></span>
</div>
<div style="clear:both"></div>
<?php if (is_file($uploadPath.$row['id'].'.'.$row['photo_ext'])):?>
<div class="img-position-<?php echo $row['image_align']?>">
<img src="<?php echo $UPLOAD_URL.$path_str.$row['id'].'.'.$row['photo_ext'].'?time='.time();?>" title="<?php echo $row['photo_memo']?>" />
<br/><?php echo  $row['photo_memo']?>
 </div>
<?php endif?>
<?php echo nl2br($row['content'])?>
</div>
<div style="clear:both" ></div>
<div class="houseDiv"></div>
<table id="toolTable" style="margin:10px auto; ">
<tr>
<?php if (isset($_SESSION['session_tea_sn'])):?>
<?php $man = checkid($_SERVER[SCRIPT_FILENAME],1); ?>
<?php if ($_SESSION['session_tea_sn'] == $row['teacher_sn'] or $man) :?>
<td><a href="#"><span id="editBtn">編 修</span></a></td>
<td><a href="#"><span id="deleleBtn">刪 除</span></a></td>
<td><a href="list.php"><span  id="backBtn">上一頁</span></a></td>
<?php endif?>
<?php else:?>
<td><a href="javascript:history.back()"><span  id="backBtn">上一頁</span></a></td>
<?php endif?>
</tr>
</table>
<form action="" method="post" id="myform">
<input type="hidden"  name="id"  value="<?php echo $row['id']?>"/>
<input type="hidden" name="act" value="edit" />
</form>
<?php
foot();