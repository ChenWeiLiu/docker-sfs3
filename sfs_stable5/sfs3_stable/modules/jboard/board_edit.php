<?php

// $Id: board_edit.php 7779 2013-11-20 16:09:00Z smallduh $

// --系統設定檔
include "board_config.php";

// session 認證
//session_start();
//session_register("session_log_id");
$bk_id = $_REQUEST['bk_id'];
$b_id = $_REQUEST['b_id'];
if(!board_checkid($bk_id) and !checkid($_SERVER['SCRIPT_FILENAME'],1)){

	$go_back=1; //回到自已的認證畫面
	if ($is_standalone)
		include "header.php";
	else
		head("Joomla!文章編輯");

	include $SFS_PATH."/rlogin.php";
	if ($is_standalone)
		include "footer.php";
	else
		foot();
	exit;
}

//檢查修改權
//$query = "select b_id from board_p where b_id ='$b_id' and b_own_id='$session_log_id'";
$query = "select b_id from jboard_p where b_id ='$b_id'";
$result = $CONN->Execute($query) or die($query);
if ($result->EOF && !checkid($_SERVER['SCRIPT_FILENAME'],1)){
	echo "沒有權限修改本公告";
	exit;
}

// 刪除檔案
if ($_GET['act'] == 'del_file'){
	$fArr = board_getFileArray($_GET['b_id']);
	//$sFile = $USR_DESTINATION.'/'.$_GET['b_id'].'/'.$fArr[$_GET['id']]['new_filename'];
	//if (is_file($sFile)) {
	//	unlink($sFile);
	 $query= "delete from jboard_files where b_id = '$b_id' and new_filename='".$fArr[$_GET['id']]['new_filename']."'";
	 $CONN->Execute($query);
	 
	 $sFile=$Download_Path.$fArr[$_GET['id']]['new_filename'];
	 unlink($sFile);
		header("Content-type: text/html; charset=big5");
		echo $fArr[$_GET['id']]['new_filename'];
	//}
	exit;
}


//-----------------------------------

$query = "select  a.post_office , b.teach_title_id , b.title_name ,b.room_id,c.name from teacher_post a ,teacher_title b ,teacher_base c  where a.teacher_sn = c.teacher_sn and  a.teach_title_id =b.teach_title_id  and a.teacher_sn='{$_SESSION['session_tea_sn']}' ";

$result = $CONN->Execute($query) or die ($query);

$row = $result->fetchRow();

$b_name = $row["name"]; //張貼人姓名
//發文者目前所在處室 , 讀取 teacher_title 裡的room_id , 然後轉換成發文處室名稱
//$b_unit = $_POST['board_name']; //所在處室

$b_unit=$row['room_id'];		//發文者所在處室

//$b_title = addslashes($row["title_name"]); //職稱  2014.04.23 後以 teach_title_id 取代
$b_title=$row['teach_title_id'];

$query = "select * from jboard_kind where bk_id ='$bk_id' ";
$result= $CONN->execute($query) or die ($query);
$row = $result->fetchRow();
$b_is_sign = $row['b_is_sign']; // 是否簽收
$bk_id = $row["bk_id"];
$board_name = $row["board_name"];
$board_date = $row["board_date"];
$board_k_id = $row["board_k_id"];
$board_last_date = $row["board_last_date"];
$board_is_upload = $row["board_is_upload"];
$board_is_public = $row["board_is_public"];

if ($_POST['key'] == "確定修改"){
	$b_post_time = mysql_date();
	//$b_unit = $board_name;  
  $b_sort=($_POST['top_days']==0)?"100":"99";
	$sql_update = "update jboard_p set bk_id='".$_POST['bk_id']."',b_open_date='{$_POST['b_open_date']}', " .
			"b_days='{$_POST['b_days']}',b_unit='$b_unit', b_sub='{$_POST['b_sub']}'," .
			"b_con='{$_POST['b_con']}', b_url='{$_POST['b_url']}' ,b_post_time='$b_post_time'," .
			"b_is_intranet='{$_POST['b_is_intranet']}',b_is_sign='{$_POST['b_is_sign']}',b_is_marquee='{$_POST['b_is_marquee']}' ,b_sort='$b_sort',top_days='{$_POST['top_days']}'";
	if ($_POST['del_sign']=='1'){
		$sql_update .= ",b_signs='' ";
	}
	$sql_update .= " where b_id='$b_id' " ;
	$CONN->Execute($sql_update) or die ($sql_update);

  //處理圖片必須先取得  $b_id, $sPath ,$b_con 全域變數
  $sPath = $USR_IMG_TMP.'images/';
  $b_con=$_POST['b_con'];
	GetImgFromHTML(); 

	//處理修改後已不使用的圖
	DelImgNotInHTML();
	
	//載入檔案處理程式
  include_once("board_files_upload.php");

	if (!$error_flag)
		Header ("Location: board_show.php?bk_id=$bk_id&b_id=$b_id");
}

$query = "select * from jboard_p where b_id ='$b_id' ";
$result = $CONN->Execute($query);

$row = $result->fetchRow();
$b_id = $row["b_id"];
$bk_id = $row["bk_id"];
$b_open_date = $row["b_open_date"];
$b_days = $row["b_days"];
$b_unit = $row["b_unit"];
$b_title = $row["b_title"];
$b_name = $row["b_name"];
$b_sub = $row["b_sub"];
$b_con = $row["b_con"];
$b_hints = $row["b_hints"];
$b_upload = $row["b_upload"];
$b_url = $row["b_url"];
$b_own_id = $row["b_own_id"];
$b_is_intranet = $row["b_is_intranet"];
$b_is_marquee = $row["b_is_marquee"];
$b_is_sign = $row["b_is_sign"];
$b_signs = $row['b_signs'];
$top_days=$row['top_days'];

//是否有獨立的界面
if ($is_standalone)
	include "header.php";
else
	head("Joomla!文章編輯");
?>
<Script src="../../include/ckeditor/ckeditor.js"></Script>
<script language="JavaScript">
function checkok(){
	var OK=true
	if(document.eform.b_sub.value == ""){
		OK=false;
	}
	if (OK == false){
		alert("標題不可空白")
	}
	return OK
}

//-->
</script>
<script type="text/javascript" src="<?php echo $SFS_PATH_HTML ?>javascripts/forms.js"></script>
<script type="text/javascript" src="<?php echo $SFS_PATH_HTML ?>javascripts/prototype.js"></script>
<script type="text/javascript">
function del_file(f_id,id) {
	new Ajax.Request('<?php echo $_SERVER['PHP_SELF'] ?>', {
  method: 'get',
  parameters: {bk_id: '<?php echo $bk_id ?>', b_id: '<?php echo $b_id ?>', act: 'del_file', id: id},
 onSuccess: function(transport){
  			document.getElementById(f_id).style.visibility="hidden";
      var response = transport.responseText || "no response text";
      alert("成功 刪除附檔! \n\n" + response);
    },
    onFailure: function(){ alert('錯誤!') }
  });
}
</script>

<form enctype="multipart/form-data" name=eform method="post" action="<?php echo $PHP_SELF ?>" onSubmit="return checkok()" >

<?php
//顯示錯誤訊息
if ($error_flag)
	echo "<h3><font color=red>錯誤 !! 不可上傳 php 程式檔!!</font></h3>";

//載入表單
include_once("board_form.php");
?>



<input type="hidden" name="board_name" value="<?php echo $board_name ?>">
<input type="hidden" name="b_old_upload" value="<?php echo $b_upload ?>">
<input type="hidden" name="b_id" value="<?php echo $b_id ?>">

<input type="submit" name="key" value="確定修改">&nbsp;&nbsp;&nbsp;
<INPUT TYPE="button" VALUE="回上一頁" onClick="history.back()"></td>

</form>
</center>
<?php
//若啟用 html 編輯器
if ($enable_is_html<>'') {  echo "<script>CKEDITOR.replace('b_con',{ language: 'zh'});</script>"; }
//是否有獨立的界面
if ($is_standalone)
	include "footer.php";
else
	foot();
?>
