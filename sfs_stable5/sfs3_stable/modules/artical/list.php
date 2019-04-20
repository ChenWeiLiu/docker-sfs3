<?php
require "config.php";
head('小文章投稿系統');
//print_menu($menu_p);
// 期別
$query = "SELECT * FROM artical WHERE is_publish=1 ORDER BY start_date DESC";
$kindRes = $CONN->Execute($query);

if (isset($_GET['page']))
$current_page = (int)$_GET['page'];
else
$current_page = 0;

$optionQuery = '';
if (isset($_GET['articalId']) and $_GET['articalId'] !='')
$optionQuery .=" AND b.id=".(int)$_GET['articalId'];
if (isset($_GET['sel-year']) and $_GET['sel-year'] !=''){
	$optionQuery .=" AND a.class_number LIKE '".(int)$_GET['sel-year']."%' ";
	$sel_year = (int)$_GET['sel-year'];
}

$query = "SELECT COUNT(*) AS cc FROM artical_detail a LEFT JOIN artical b ON a.artical_id=b.id WHERE
	b.is_publish=1 $optionQuery";
$countRes = $CONN->Execute($query);
$recordCount = $countRes->fields['cc'];


$query = "SELECT a.*, DATE_FORMAT(publish_time, '%Y-%m-%d') AS date , b.title AS artical_title , c.stud_name FROM
	artical_detail a LEFT JOIN artical b ON a.artical_id=b.id LEFT JOIN stud_base c ON a.student_sn=c.student_sn  WHERE
	b.is_publish=1 $optionQuery";


if (isset($_GET['sel-kind']) and $_GET['sel-kind'] == 'mostNew') {
	$query .= " ORDER BY b.start_date DESC, a.publish_time DESC";
	$sel_kind = 'mostNew';
}
elseif (isset($_GET['sel-kind']) and $_GET['sel-kind'] == 'mostHot') {
	$query .= " ORDER BY a.hits DESC";
	$sel_kind = 'mostHot';
}
else {
	$query .= " ORDER BY a.id DESC";
	$sel_lind = '';
}


$query .= " LIMIT ".$current_page * $PARAMSTER['items_per_page'].", {$PARAMSTER['items_per_page']} ";


$res = $CONN->Execute($query) or die($query);
$class_base = class_base();
// 年級
$query = "SELECT substring(class_number,1,1) AS Syear FROM artical_detail ";
if (isset($_GET['articalId']) and $_GET['articalId'] !='')
$query .=" WHERE artical_id=".(int)$_GET['articalId'];
$query .=" GROUP BY Syear ORDER BY Syear  DESC";
$yearRes = $CONN->Execute($query);


?>
<script type="text/javascript" src="<?php echo $SFS_PATH_HTML?>javascripts/jquery/jquery.pagination.js" type="text/javascript"></script>

<style type="text/css">
#listTable {
	width: 100%;
	text-align: center;
	background-image: url(images/background-watermelon.gif);
	border-spacing: 0;
}

#listTable td, #listTable th{
border-bottom: 1px dotted #ccc;
padding:5px;
}
#listTable caption{
	font-size:18px;
	//font-weight: bold;
	color:#00f;
}
#left-area {
	width: 150px
}

#right-area {
	width: 100%
}

#left-area ul {
	padding: 0;
	margin: 20px auto;
	cursor: pointer;
	color:blue;
}

#left-area li {
	list-style-type: none;
	background-image: url(images/tomato.gif);
	background-repeat: no-repeat;
	height: 60px;
	width: 140px;
}

#left-area li span {
	position: relative;
	left: 70px;
	top: 15px;
}
#top-area{width:100%}

#top-area td{
	background-image: url(images/carrot.gif);
	background-repeat: no-repeat;
	height: 60px;
}

#top-area td span {
	position: relative;
	left: 70px;
	top: -5px;
}

.artical-title {border:#ccc solid thin;}

.pagination {
            font-size: 80%;
        }

.pagination a {
    text-decoration: none;
	border: solid 1px #AAE;
	color: #15B;
}

.pagination a, .pagination span {
    display: block;
    float: left;
    padding: 0.3em 0.5em;
    margin-right: 5px;
	margin-bottom: 5px;
}

.pagination .current {
    background: #26B;
    color: #fff;
	border: solid 1px #AAE;
}

.pagination .current.prev, .pagination .current.next{
	color:#999;
	border-color:#999;
	background:#fff;
}

</style>
<script>
$(function(){
   $("#Pagination").pagination(
		<?php echo $recordCount?>
		, {
		prev_text : '前頁',
		next_text : '下頁',
		current_page : <?php echo $current_page?>,
		items_per_page: <?php echo $PARAMSTER['items_per_page']?>,
		num_edge_entries: 2,
		num_display_entries: 6,
      callback: pageselectCallback
    });

	function pageselectCallback(page_id, jq){
		$("#page").attr('value',page_id);
		$("#selectArticalForm").submit();
		return false;
    }


	$("#curr_artical").html($("#articalId option:selected").text());
	$("#articalId").change(function(){

//	$("#sel-kind").val('');
	$("#selectArticalForm").submit();
	});

	$("#left-area li").click(function(){
		var selYear = $(this).attr('id').substr(4);
		$("#sel-year").val(selYear);
		$("#selectArticalForm").submit();
	});

	$(".sel-kind").click(function(){
		var id = $(this).attr('id');
		$("#sel-kind").val(id);
		$("#sel-year").val('');
		$("#selectArticalForm").submit();
	});
});

</script>
<table>
	<tr>
		<td id="left-area" valign="top">
		<form action="" method="get" id="selectArticalForm"><select
			name="articalId" id="articalId">
			<option value="">全部期別</option>
			<?php foreach($kindRes as $row):?>
			<option value="<?php echo $row['id']?>"
			<?php if ($_GET['articalId']==$row['id']):?> selected="selected"
			<?php endif?>><?php echo $row['title']?>(<?php echo $row['start_date']?>)</option>
			<?php endforeach;?>
		</select>
		<input type="hidden" name="sel-year" id="sel-year" value="<?php echo $sel_year?>"/>
		<input type="hidden" name="sel-kind" id="sel-kind" value="<?php echo $sel_kind ?>" />
		<input type="hidden" name="page" id="page" />
		</form>
		<ul>
		<?php foreach ($yearRes->getAll() as $row):?>
			<li  id="sel-<?php echo $row['Syear']?>"><span><?php echo $class_year[$row['Syear']]?>級</span></li>
			<?php endforeach;?>
		</ul>
		</td>
		<td id="right-area" valign="top" align="center">

		<table id="top-area" class="ui-corner-all">
		<tr>
		<td><span><a class="sel-kind" href="#" id="mostNew">最新</a></span></td>
		<td><span><a class="sel-kind" href="#" id="mostHot">熱門</a></span></td>
		<td><span><a href="sign.php">投稿</a></span></td>
		<td><span><a href="manager.php">管理</a></span></td>
		</tr>
		</table>
<?php if ($res->recordCount()>0):?>
		<table id="listTable" class="ui-corner-all" style="border:2px solid #ccc;">
		<caption>
		<span><?php echo $PARAMSTER['title'] ?></span>
		<span id="curr_artical"></span>
		<span><?php if (isset($_GET['sel-year']) && $_GET['sel-year'] !='') echo $class_year[$_GET['sel-year']].'級'?></span>
		<span><?php if (isset($_GET['sel-kind']) && $_GET['sel-kind'] =='mostHot'):?>最熱門
		<?php elseif (isset($_GET['sel-kind']) && $_GET['sel-kind'] =='mostNew'):?>最新<?php endif?></span>
		作品列表
		</caption>
			<thead>
			<tr>
			<td colspan="6">
			<div id="Pagination" class="pagination"></div>
			</td>
			</tr>
				<tr>
					<th>編號</th>
					<th>主題</th>
					<th>作者</th>
					<th>期別</th>
					<th>瀏覽日期</th>
					<th>人氣</th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($res as $row):?>
			<?php $classId = substr($row['class_number'],0 ,3)?>
				<tr>
					<td><?php echo $row['id']?></td>
					<td><a href="show.php?id=<?php echo $row['id']?>"><?php echo $row['title']?></a></td>
					<td><a
						href="showStudent.php?selStudent=<?php echo $row['student_sn']?>">
						<?php echo $class_base[$classId]?> <?php echo $row['stud_name']?></a>
					</td>
					<td><?php echo $row['artical_title']?></td>
					<td><?php echo $row['date']?></td>
					<td><?php echo $row['hits']?></td>
				</tr>
				<?php endforeach?>
			</tbody>

		</table>

<?php else:?>

<h2>尚未上傳文章</h2>

<?php endif?>
		</td>
	</tr>
</table>
<?php foot()?>
