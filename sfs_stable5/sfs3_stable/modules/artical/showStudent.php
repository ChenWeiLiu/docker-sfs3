<?php
require  "config.php";

$studentSn = (int) $_GET['selStudent'];

$query = "SELECT a.id,a.title, a.student_sn, DATE_FORMAT(a.publish_time,'%Y-%m-%d')  AS date,
a.hits, a.class_number ,c.stud_name, b.title AS baseTitle FROM artical_detail a LEFT JOIN artical b
ON a.artical_id=b.id LEFT JOIN stud_base c ON a.student_sn=c.student_sn
WHERE a.student_sn=$studentSn ORDER BY a.publish_time DESC";
$res = $CONN->Execute($query) or die($query);


head();
//print_menu($menu_p);
?>
<style type="text/css">
#listTable{width:100%; text-align:center;background: #ccc ;border-spacing: 1px;margin:5px auto;}
#listTable td {padding:5px;}
#listTable a {color: blue;}
.odd{background: #fff}
.even{background: #eff}
.ui-widget-header{padding:5px;text-align:center}
</style>
<script>
$(function(){
	$("#listTable tbody tr:even").addClass('even');
	$("#listTable tbody tr:odd").addClass('odd');

});
</script>
<div class="ui-widget-header ui-corner-top" style="font-size:20px"><?php echo $res->fields['stud_name']?> 的文章列表</div>
<div class="ui-widget-content ui-corner-bottom" style="border:#ccc solid thin">
<table style="width:100%">
<tr>
<?php
$file_name = $photoUploadPath.$studentSn.'.jpg';
if (is_file($file_name)):
?>
<td style="width:350px;" valign="top">
<img src="<?php echo $UPLOAD_URL.$photo_path_str.$studentSn.'.jpg'?>" style="width:350px;margin:5px; padding:3px; border:dashed #ccc thin;" />
</td>
<?php endif?>
<td valign="top">
<table id="listTable">
<thead class="ui-widget-header">
<tr>
<th>編號</th>
<th>主題</th>
<th>期別</th>
<th>發表時間</th>
<th>點閱數</th>

</tr>
</thead>
<tbody class="ui-widget-content">
<?php foreach($res as $row):?>
<tr>
<td><?php echo $row['id']?></td>
<td><a href="show.php?id=<?php echo $row['id']?>"><?php echo $row['title']?></a></td>
<td><?php echo $row['baseTitle']?></td>
<td><?php echo $row['date']?></td>
<td><?php echo $row['hits']?></td>
</tr>
<?php endforeach?>
</tbody>
</table>
</td>
</tr>
</table>

</div>


<?php foot()?>