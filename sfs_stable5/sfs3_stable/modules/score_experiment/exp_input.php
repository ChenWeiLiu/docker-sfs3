<?php
// $Id: reward_one.php 6735 2012-04-06 08:14:54Z smallduh $
//取得設定檔
include_once "config.php";

sfs_check();
//選定的學生
$student_sn=$_POST['student_sn'];
//取得目前學年度
$curr_year=curr_year();
$curr_seme=curr_seme();
$year_seme=sprintf("%03d%1d",$curr_year,$curr_seme);

//ajax 產生表單 新增
if ($_POST['act']=='insert_form') {
	header('Content-type: text/html;charset=big5');
	$stud=get_stud_base($student_sn);
	$stud_data['stud_name']=$stud['stud_name'];
	$stud_data['student_sn']=$stud['student_sn'];
	$stud_data['score_source']=$stud['exp_group_name'];
	$make_form=make_form($stud_data,"新增一筆記錄");
	echo $make_form;
	exit();
}

//刪除
if ($_POST['act']=='del_row') {
	//取得學生基本資料
	$stud = get_stud_base($student_sn);
	$sn=$_POST['sn'];
	$sql="select * from score_experiment where sn='$sn'";
	$res=$CONN->Execute($sql) or trigger_error($sql,256);
	$old_file=$res->fields['append_file'];
	if ($old_file!='') {
		unlink($UPLOAD_PATH . 'score_experiment/' . $old_file);
	}
	$sql="delete from score_experiment where sn='$sn'";
	$res=$CONN->Execute($sql) or trigger_error($sql,256);
	echo $sn;
	exit();
}

//ajax 產生表單 新增
if ($_POST['act']=='edit_row') {
	header('Content-type: text/html;charset=big5');
	$sn=$_POST['sn'];
	$sql="select * from score_experiment where sn='$sn'";
	$res=$CONN->Execute($sql) or trigger_error($sql,256);
	$stud_data=$res->fetchRow();
	$stud=get_stud_base($stud_data['student_sn']);
	$stud_data['stud_name']=$stud['stud_name'];
	$make_form=make_form($stud_data,"編修一筆記錄");
	echo $make_form;
	exit();
}


//儲存
if ($_POST['act']=='save') {
	//取得學生基本資料
	$stud=get_stud_base($student_sn);
	//取得表單內容
	foreach ($_POST as $k=>$v) {
		${$k}=$v;
	}
	//處理檔案上傳 , 先檢查有沒有上傳檔案(新增一定要上傳, 修改不需要)
		$targetFile="";
		if (isset($_FILES['append_file']['tmp_name'])) {
			$tempFile = $_FILES['append_file']['tmp_name'];
			//$targetPath = $_SERVER['DOCUMENT_ROOT'] . $targetFolder; // 設定要上傳檔案的資料夾絕對路徑
			$targetPath = $UPLOAD_PATH . 'score_experiment/';
			// Validate the file type
			$fileTypes = array('pdf'); // 可以限制檔案副檔名
			//取得檔名資訊
			$fileParts = pathinfo($_FILES['append_file']['name']);
			if (in_array($fileParts['extension'], $fileTypes)) {
				//轉換成編碼過的檔名
				//$targetFile = rtrim($targetPath,'/') . '/' . $_FILES['append_file']['name'];
				//檔名用 $stud_person_id+hash('sha256', $student_sn)+$fileParts['extension'];
				$targetFile = $stud['stud_person_id'] . hash('sha256', $stud['student_sn']).time(). "." . $fileParts['extension'];
				move_uploaded_file($tempFile, $targetPath.$targetFile);
			}
		}
	if ($sn=='') {
		$sql="insert score_experiment (year_seme,student_sn,score,score_level,hard_level,score_memo,append_memo,append_file,score_source,create_time) value ('$year_seme','$student_sn','$score','$score_level','$hard_level','$score_memo','$append_memo','$targetFile','$score_source','$create_time')";
	} else {
		//若本次有上傳檔案, 要先刪除舊檔
		$sql="select append_file from score_experiment where sn='$sn'";
		$res=$CONN->Execute($sql) or trigger_error($sql,256);
		$old_file=$res->fields['append_file'];
		if ($targetFile!='') {
			unlink($UPLOAD_PATH . 'score_experiment/'.$old_file);
		} else {
			$targetFile=$old_file;
		}
		$sql="update score_experiment set score='$score',score_level='$score_level',hard_level='$hard_level',score_memo='$score_memo',append_memo='$append_memo',append_file='$targetFile',score_source='$score_source' where sn='$sn'";
	}

	$CONN->execute($sql) or trigger_error($sql,256);
}

//友善列印
if ($_GET['act']=='print') {
	if ($_GET['student_sn']!='') {
		//取得此生的所有記錄
		$sql="select * from score_experiment where student_sn='".$_GET['student_sn']."'";
		$res=$CONN->Execute($sql) or trigger_error($sql,256);
		?>
		<table border="0" width="100%">
			<tr>
				<td align="center"><?php echo $print_header;?></td>
			</tr>
		</table>
		<table border="1" style="width:100%;border-collapse: collapse;border-style:solid;border-width: thin;border-color: #000000">
			<tr style="background-color: #d4d4d4">
				<td align="center" style="padding:5px">學年</td>
				<td align="center" style="padding:5px">學期</td>
				<td align="center" style="padding:5px">百分數</td>
				<td align="center" style="padding:5px">等第</td>
				<td align="center" style="padding:5px">努力程度</td>
				<td align="center" style="padding:5px">文字描述</td>
				<td align="center" style="padding:5px">備註或附記說明</td>
				<td align="center" style="padding:5px">成績來源</td>
			</tr>
			<?php
			while ($row=$res->fetchRow()) {
				?>
				<tr id="tr_<?php echo $row['sn'];?>">
					<td align="center"><?php echo substr($row['year_seme'],0,3);?></td>
					<td align="center"><?php echo substr($row['year_seme'],-1);?></td>
					<td align="center"><?php echo $row['score']?></td>
					<td align="center"><?php echo $row['score_level']?></td>
					<td align="center"><?php echo $row['hard_level']?></td>
					<td><?php echo $row['score_memo']?></td>
					<td><?php echo nl2br($row['append_memo'])?></td>
					<td><?php echo $row['score_source']?></td>
				</tr>
				<?php
			}
			?>
		</table>
		<table border="0">
			<tr>
				<td><?php echo $print_footer;?></td>
			</tr>
		</table>
		<?php
	}
	exit();
}


//秀出網頁
head("實驗教育成績登錄");

$tool_bar=make_menu($school_menu_p);

//列出選單
echo $tool_bar;



//取得所有實驗教育學生
$sql="select * from stud_base where stud_study_cond in (0,15) and experiment_kind>0 order by curr_class_num";
$res=$CONN->Execute($sql) or trigger_error($sql,256);

$stud_option="<option value=''>請選擇學生</option>";

while ($row=$res->fetchrow()) {
	$stud_option.="<option value='".$row['student_sn']."'".(($row['student_sn']==$student_sn)?" selected":"").">".substr($row['curr_class_num'],0,3)."-".substr($row['curr_class_num'],-2)."-".$row['stud_name'].(($row['exp_group_name']!='')?" (".$row['exp_group_name'].")":"")."</option>";
}

?>
<style>
	.div_blocker {
	width: 800px;
	height: 500px;
	position: absolute;
	z-index: 2;
	top: 50%;
	left: 50%;
	margin: -250px 0 0 -450px;
	overflow:auto;
	border-width:3px;border-style:dashed;border-color: #dedede;padding:5px;
	}

	#mask {
		position: fixed;
		top: 0;
		right:0;
		bottom:0;
		left: 0;
		z-index: 1;
		opacity:0.5;
		-moz-opacity:0.5;
		display: none;
		background-color: #000;
	}


</style>
<form name="myform" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
	<div style="margin-top: 10px">
		選定學生
		<select size="1" name="student_sn" onchange="this.form.submit()">
			<?php echo $stud_option;?>
		</select>
	</div>
	<?php
	if ($student_sn!='') {
		//取得此生的所有記錄
		$sql="select * from score_experiment where student_sn='$student_sn'";
		$res=$CONN->Execute($sql) or trigger_error($sql,256);

	?>
	<div style="margin-top: 10px">
		<div style="margin-bottom: 10px">
		<span style="float:right;">
			<?php
			if ($res->recordCount()) {
				?>
				<input type="button" class="btn_print" id="<?php echo $student_sn;?>" value="友善列印">
			<?php
			}
			?>
			<input type="button" class="btn_insert" id="<?php echo $student_sn;?>" value="新增一筆">

		</span>
		</div>
		<table class="small table-sfs3" style="width:100%">
			<tr style="background-color: #0d3349;color:#FFFFFF">
				<td align="center" style="padding:5px">學年</td>
				<td align="center" style="padding:5px">學期</td>
				<td align="center" style="padding:5px">百分數</td>
				<td align="center" style="padding:5px">等第</td>
				<td align="center" style="padding:5px">努力程度</td>
				<td align="center" style="padding:5px">文字描述</td>
				<td align="center" style="padding:5px">備註或附記說明</td>
				<td align="center" style="padding:5px">成績來源</td>
				<td align="center" style="padding:5px">附件檔案</td>
				<td align="center" style="padding:5px">操作</td>
			</tr>
			<?php
			while ($row=$res->fetchRow()) {
				?>
				<tr id="tr_<?php echo $row['sn'];?>">
					<td align="center"><?php echo substr($row['year_seme'],0,3);?></td>
					<td align="center"><?php echo substr($row['year_seme'],-1);?></td>
					<td align="center"><?php echo $row['score']?></td>
					<td align="center"><?php echo $row['score_level']?></td>
					<td align="center"><?php echo $row['hard_level']?></td>
					<td><?php echo $row['score_memo']?></td>
					<td><?php echo nl2br($row['append_memo'])?></td>
					<td><?php echo $row['score_source']?></td>
					<td align="center">
						<?php
						if ($row['append_file']!='') {
							?><input type="button" id="<?php echo $row['append_file']?>" class="view_append_file" value="瀏覽">
							<?php
						}
						?>
					</td>
					<td align="center">
						<?php
						if ($year_seme==$row['year_seme']) {
							?>
								<img src="./images/pen.png" style="cursor: pointer" id="<?php echo $row['sn']?>" class="edit_row" title="編修">
								<img src="./images/del.png" style="cursor: pointer" id="<?php echo $row['sn']?>" class="del_row" title="刪除">
							<?php
						}
						?>
					</td>
				</tr>
				<?php
			}
			?>
		</table>
	</div>
	<?php
	}
	?>
</form>
<!--透明遮罩?-->
<div id="mask"></div>

<div id="blocker_div" class="div_blocker" style="display:none;background-color: rgb(250, 248, 225)">
	<div>
		<span style="float:right">
			<button class="btn-closed" style="background-color: #dadada;color:#a0a0a0">closed</button>
		</span>
	</div>
	<div id="blocker_display" style="margin:25px">

	</div>
</div>
<?php
foot();
?>
<Script>
	$(".btn-closed").click(function(){
		$("#blocker_div").css("display","none");
		$("#mask").css("display","none");
	});

	//瀏覽附件
	$(".view_append_file").click(function(){
		//檔名
		var file_name=$(this).attr("id");
		var file_url="<?php echo $UPLOAD_URL . 'score_experiment/';?>"+file_name;
		var dis="<object data=\""+file_url+"\" type=\"application/pdf\" width=\"100%\" height=\"500px\"><embed src=\""+file_url+"\"><p>您的瀏覽器不支援直接觀看 PDF 檔. 請下載後再使用其他程式打開。 <a href=\""+file_url+"\">Download PDF</a>.</p></embed></object>";
		$("#blocker_display").html(dis);
		$("#mask").css("display","block");
		$("#blocker_div").width("90%");
		$("#blocker_div").css("height","600");
		$("#blocker_div").css("margin","-300px 0 0 -45%");
		$("#blocker_div").fadeIn(300);
	});

	//刪除一筆
	$(".del_row").click(function() {
		var sn = $(this).attr("id");
		var confirm_delete=confirm("您確定要刪除這筆資料?");

		if (confirm_delete) {
			$.ajax({
				type: "post",
				url: 'exp_input.php',
				data: { act:'del_row',sn:sn },
				dataType: "text",
				error: function(xhr) {
					alert('ajax request 發生錯誤!');
				},
				success: function(response) {
					//把該列移除
					$("#tr_"+response).remove();
					alert("資料流水號"+sn+ ", 已刪除!");
				}
			});   // end $.ajax

		} else {
			return false;
		}

	});

	//編輯一筆
	$(".edit_row").click(function() {
		var sn = $(this).attr("id");

			$.ajax({
				type: "post",
				url: 'exp_input.php',
				data: { act:'edit_row',sn:sn },
				dataType: "text",
				error: function(xhr) {
					alert('ajax request 發生錯誤!');
				},
				success: function(response) {
					$("#blocker_display").html(response);
					$("#mask").css("display","block");
					$("#blocker_div").width("800px");
					$("#blocker_div").height("500px");
					$("#blocker_div").css("margin","-250px 0 0 -400px");
					$("#blocker_div").fadeIn(300);
				}
			});   // end $.ajax


	});
	//新增一筆
	$(".btn_insert").click(function(){

		//var show=$(this);
		var student_sn=$(this).attr("id");

		$.ajax({
			type: "post",
			url: 'exp_input.php',
			data: { act:'insert_form',student_sn:student_sn },
			dataType: "text",
			error: function(xhr) {
				alert('ajax request 發生錯誤!');
			},
			success: function(response) {
				//alert('ajax request 成功!');
				$("#blocker_display").html(response);
				$("#blocker_div").width("800px");
				$("#blocker_div").height("500px");
				$("#blocker_div").css("margin","-250px 0 0 -400px");
				$("#mask").css("display","block");
				$("#blocker_div").fadeIn(300);
			}
		});   // end $.ajax
	});

	//新增一筆
	$(".btn_print").click(function() {
		//var show=$(this);
		var student_sn = $(this).attr("id");

		window.open("exp_input.php?act=print&student_sn="+student_sn);

		return false;

	});
	//儲存前檢查
	function check_before_save() {

		var sn=document.form_block.sn.value;
		var append_file=document.form_block.append_file.value;

		if (sn=='' && append_file=='') {
			alert("至少要提供附檔!");
			return false;
		}

		return true;

	}

	function unset_ower(thetext) {
		if(thetext.value>100){ thetext.style.background = '#FF0000'; alert("輸入成績高於100分");}
		else if(thetext.value<0){ thetext.style.background = '#AA5555'; alert("輸入成績為負數"); }
		else { thetext.style.background = '#FFFFFF'; }
		return true;
	}
</Script>
