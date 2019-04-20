<?php
require "config.php";
require "imging.class.php";
sfs_check();



switch ($_POST['act']) {
	// 編修
	case 'edit':
		$id = (int) $_POST['id'];
		$teacher_sn = (int)$_SESSION['session_tea_sn'];
		$query = "SELECT * FROM artical_detail WHERE id=$id";
		$res = $CONN->Execute($query);

		$editRow = $res->fetchRow();
		$man = checkid($_SERVER[SCRIPT_FILENAME],1);
		if (!($editRow['teacher_sn']==$teacher_sn or $man)) {
			echo '<h1>無權修改</h1>';
			exit;
		}
	break;
	//刪學生照片
	case 'delete-photo' :
		$student_sn = (int) $_POST['studentSn'];
		$file_name = $photoUploadPath.$student_sn.'.jpg';
		if (is_file($file_name)) {
			unlink($file_name);
			echo 1;
		}
		exit;

		break;
	// 上傳學生照片
	case 'uploadStudentPhoto':
	//上傳照片
		$student_sn = (int) $_POST['studentSn'];
		if (is_file($_FILES['student_photo']['tmp_name'])) {
			$ext = end(explode(".",$_FILES['student_photo']['name']));
			if (in_array(strtolower($ext),array('jpg','jpeg'))){
				$file_name = $photoUploadPath.$student_sn.'.jpg';
				copy($_FILES['student_photo']['tmp_name'], $file_name);
				$img = new imaging;
				$img->set_img($file_name);
				$img->set_quality(90);
				// Small thumbnail
				$img->set_size($PARAMSTER['image_width']);
				$img->save_img($file_name);
					echo $UPLOAD_URL.$photo_path_str.$student_sn.'.jpg?'.time();
			}
			exit;
		}
		break;

	//檢查照片檔
	case 'getStudentPhoto':
		$studentSn = (int)$_POST['studentSn'];
		if (is_file($photoUploadPath.$studentSn.'.jpg'))
		echo $UPLOAD_URL.$photo_path_str.$studentSn.'jpg?time='.time();
		else 	echo '';
		exit;
		break;

	// 新增文章
	case 'append' :

		$teacher_sn = (int)$_SESSION['session_tea_sn'];
		$artical_id =  filter_input(INPUT_POST, 'artical_id' ,FILTER_SANITIZE_NUMBER_INT);
		$title = $_POST['title'];//filter_input(INPUT_POST, 'title',FILTER_SANITIZE_STRIPPED);
		$class_number = filter_input(INPUT_POST, 'classNum' ,FILTER_SANITIZE_NUMBER_INT);
		
		//$student_sn = filter_input(INPUT_POST, 'student_sn' ,FILTER_SANITIZE_NUMBER_INT);
		$query = "SELECT student_sn FROM stud_base WHERE curr_class_num='$class_number' AND stud_study_cond=0";
		$res = $CONN->Execute($query) or die('SQL 錯誤');
		$student_sn = $res->fields['student_sn'];
		
		$content = $_POST['content'];//filter_input(INPUT_POST, 'content');
		$id = filter_input(INPUT_POST, 'id');

		$image_align = filter_input(INPUT_POST, 'image_align' ,FILTER_SANITIZE_NUMBER_INT);
		$mode = '';
		if ($id) {
			// 修改
			$query = "UPDATE artical_detail SET title='$title' , content='$content'
			 ,class_number='$class_number' ,image_align='$image_align'
			 ,student_sn=$student_sn WHERE id=$id ";
			$CONN->Execute($query) or die($query);
			$mode='edit';
		}
		else{
			// 新增
			$query = "INSERT INTO artical_detail(artical_id, title, content, student_sn, class_number, teacher_sn, image_align)
			VALUES($artical_id, '$title', '$content', $student_sn, $class_number, $teacher_sn, $image_align)";
			$res = $CONN->Execute($query) or die($query);
			$id = $CONN->Insert_ID();
		}



		// 上傳圖片
		if (is_file($_FILES['photo']['tmp_name'])) {
			$ext = end(explode(".",$_FILES['photo']['name']));
			if (in_array(strtolower($ext),$fileExt)){
				$photo_ext = strtolower($ext);
			 	$file_name = $uploadPath.$id.'.'.$photo_ext;
				copy($_FILES['photo']['tmp_name'], $file_name);
				$img = new imaging;
				$img->set_img($file_name);
				$img->set_quality(90);
				// Small thumbnail
				$img->set_size($PARAMSTER['image_width']);
				$img->save_img($file_name);
				$photo_memo = filter_input(INPUT_POST, 'photo_memo');
				$query = "UPDATE artical_detail SET photo_ext='$photo_ext', photo_memo='$photo_memo' WHERE id=$id";
				$CONN->Execute($query) or die($query);
			}
		}
		if ($mode == 'edit')
		header("Location: show.php?id=$id");
		else
		header("Location: list.php");
		exit;
	break;

	// 查詢學生
	case 'getStudent':

		$classNum = (int)$_POST['classNum'];
		$query = "SELECT stud_name,student_sn FROM stud_base WHERE curr_class_num='$classNum' AND stud_study_cond=0";
		$res = $CONN->Execute($query) or die('SQL 錯誤');

		$result = '';
		if ($res->recordCount() > 0) {
			$class_base = class_base();
			$tempClass = substr($classNum,0,3);
			$tempNumber = substr($classNum,-2);
			$result = $res->fields['student_sn'].'-'. $class_base[$tempClass].' '.$tempNumber.'號 '.$res->fields['stud_name'];
		}

		header('Content-type:text/html; charset=big5');
		echo $result;
		exit;
		break;
}


head();
print_menu($menu_p);
$date = date("Y-m-d");

// 期別
$query = "SELECT start_date,end_date, title, id  FROM artical WHERE is_publish='1' AND  start_date<='$date' AND end_date>='$date'";
$resYear = $CONN->Execute($query) or trigger_error('SQL 錯誤');

?>
<script type="text/javascript" src="<?php echo $SFS_PATH_HTML?>javascripts/external/jquery.metadata.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo $SFS_PATH_HTML?>javascripts/jquery/jquery.validate.min.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo $SFS_PATH_HTML?>javascripts/jquery/messages_tw.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo $SFS_PATH_HTML?>javascripts/jquery/ajaxupload.js" type="text/javascript"></script>

<style>
.ui-widget {padding:5px;font-size:14px}
#student_name {background:#ff7}
.error {color:red; font-weight: bold;}
#image_align, #student_photo, #student_photo_image {display: none}
#student_photo_image{width:300px; border:#ccc thin dashed; margin:10px; padding:3px}
#uploadStudentBtn {border:#ccc solid thin;padding:2px;cursor: pointer;margin:auto 5px}
#delete-photo {border:thin #ccc solid; padding:3px}
</style>
<script>
$(function(){
	 var newValue = $('#classNum').val();
     var oldValue;

	//刪除照片
	$("#delete-photo ").click(function(){
		if (confirm('確定刪除相片?')){
				$.post('sign.php',
						{
						act:'delete-photo',
						studentSn: $("#student_sn").attr('value')
						},
						function(data){
							if (data==1){
								$("#student_photo_image").attr('src','').hide();
								$("#delete-photo").hide();
							}

					});
			}
	});

 	// 照片上傳
 	new AjaxUpload('uploadStudentBtn', {
 	 	action: 'sign.php',
 	 	name:  'student_photo',
 		autoSubmit: true,
 		onSubmit : function(file , ext){
            // Allow only images. You should add security check on the server-side.
			if (ext && /^(jpg|jpeg)$/.test(ext.toLowerCase())){
				/* Setting data */
				this.setData({
					act : 'uploadStudentPhoto',
					studentSn: $("#student_sn").val()
				});
				$('#uploadMessage').text('已上傳檔案 ' + file);
			} else {
				// extension is not allowed
				$('#uploadMessage').text('錯誤: 不正確圖檔格式');
				// cancel upload
				return false;
			}
		},
		onComplete : function(file,data){
			$('#uploadMessage').text('已上傳 ' + file);
			if (data) {
				$("#student_photo_image").attr('src',data).show();
				$("#delete-photo").show();
			}
			else {
				$("#student_photo_image").attr('src','').hide();
				$("#delete-photo").hide();
			}
		}



 	 });


	$.metadata.setType("attr", "validate");
	$("#signForm").validate();

	$("#submitBtn").click(function(){
		if ($("#signForm").valid())
			$(this).attr('disabled','disabled');
		$("#signForm").submit();
	});

	$("#classNum").focusout(function(){
		var classNum = $(this).attr('value');
		newValue = classNum;
		if (classNum=='' || oldValue == newValue ) return false;

		 oldValue = newValue;

		$.post('sign.php',{act:'getStudent',classNum: classNum},function(data){
			if (data){
				var studentData = data.split("-");
				$("#student_name").html(studentData[1]);
				$("#student_sn").attr('value',studentData[0]);
				$("#student_photo").show();
				$("#delete-photo").show();
				$.post('sign.php',{act:'getStudentPhoto', studentSn:studentData[0]},function(data){
					if (data) {
						$("#student_photo_image").attr('src','<?php echo $UPLOAD_URL.$photo_path_str?>'+studentData[0]+'.jpg').show();
						$("#delete-photo").show();
					}
					else {
						$("#student_photo_image").attr('src','').hide();
						$("#delete-photo").hide();
					}

				});
			}
			else {
				$("#student_name").html('');
				$("#student_photo").hide();
				$("#student_photo_image").hide();
				$("#student_sn").attr('value','');
				$("#classNum").attr('value','');
				alert('找不到學生');
			}
		});
	});


	$("#photo").change(function(){
		var allow = new Array('gif','png','jpg','jpeg');
		var ext = $('#photo').val().split('.').pop().toLowerCase();
		if(jQuery.inArray(ext, allow) == -1) {
		    alert('不正確的圖片格式!');
		    $(this).val('');
		    $("#image_align").hide();
		}
		else
			$("#image_align").show();
	});

	<?php if ($editRow):?>
	$("#classNum").trigger('focusout');
	<?php endif?>
});
</script>

<div class="ui-widget ui-widget-header ui-corner-top"><span style="font-size:20px">投稿</span></div>
<div class="ui-widget ui-widget-content ui-corner-bottom">
<div style="float:right">
<img id="student_photo_image"  src="" />
<a href="#" id="delete-photo" style="display: none">刪除照片</a>
</div>
<form action="" method="post" id="signForm" enctype="multipart/form-data">
<dl>
<dt>期別</dt>
<dd><select name="artical_id">
<?php foreach ($resYear as $row):?>
<option value="<?php echo $row['id']?>"><?php echo $row['title']?> [<?php echo $row['start_date'].' 至 '.$row['end_date']?>] </option>
<?php endforeach?>
</select></dd>
<dt>作者</dt>
<dd><input type="text" size="6" name="classNum" id="classNum"  value="<?php echo $editRow['class_number']?>" validate="required:true"  />
請輸入班級座號(例 60101,代表 六年一班1號)</dd>
<dd id="student_photo">
<span id="student_name"></span>
<span id="uploadStudentBtn" class="ui-widget ui-state-default ui-corner-all">上傳學生照片</span>
<span id="uploadMessage" style="background: #ff0"></span>
</dd>

<dt>主題</dt>
<dd><input type="text" name="title" id="title" size="40" validate="required:true"  value="<?php echo $editRow['title']?>"/></dd>
<dt>內容</dt>
<dd><textarea name="content" id="content" rows="5" cols="40" validate="required:true" ><?php echo $editRow['content']?></textarea></dd>
<dt>上傳插圖(只接收 jpg png gif 格式檔案)</dt>
<dd><input type="file" name="photo" size="20" id="photo" />
<span id="image_align">
圖片位置 : <select name="image_align">
<?php foreach ($imageArr as $id=>$val):?>
<option value="<?php echo $id?>"><?php echo $val?></option>
<?php endforeach?>
</select>
圖片說明 : <input type="text" name="photo_memo"  size="16"/>
</span>
</dl>
<input type="hidden" name="student_sn" id="student_sn" />
<input type="hidden" name="act" value="append" />
<?php if ($editRow):?>
<input type="button" id="submitBtn" value="編修稿件" />
<input type="hidden" name="id" value="<?php echo $editRow['id']?>" />
<?php else:?>
<input type="button" id="submitBtn" value="投稿" />
<?php endif?>
</form>
<div style="clear:both"></div>
</div>
