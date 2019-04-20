<?php

// $Id: board.php 7779 2013-11-20 16:09:00Z smallduh $

// --系統設定檔
include	"board_config.php";
// session 認證
session_start();

$teach_id=$_SESSION[session_log_id];

$bk_id = $_REQUEST['bk_id'];

if(!board_checkid($bk_id) and !checkid($_SERVER[SCRIPT_FILENAME],1)){

	$go_back=1; //回到自已的認證畫面
	if ($is_standalone)
		include "header.php";
	else
		head("校務佈告欄");

	include $SFS_PATH."/rlogin.php";
	if ($is_standalone)
		include "footer.php";
	else
		foot();
	exit;
}
//-----------------------------------

//$query = "select  a.post_office ,b.teach_title_id, b.title_name ,c.name from teacher_post a ,teacher_title b ,teacher_base c where a.teacher_sn = c.teacher_sn and  a.teach_title_id =b.teach_title_id  and a.teacher_sn='$_SESSION[session_tea_sn]' ";
$query = "select  a.post_office , b.teach_title_id ,b.title_name ,b.room_id,c.name from teacher_post a ,teacher_title b ,teacher_base c  where a.teacher_sn = c.teacher_sn and  a.teach_title_id =b.teach_title_id  and a.teacher_sn='{$_SESSION['session_tea_sn']}' ";
$result	= $CONN->Execute($query) or die ($query);

$row = $result->fetchRow();
$b_name	= addslashes($row["name"]); //張貼人姓名
//$b_unit	= addslashes($_POST['board_name']); //所在處室
$b_unit=$row['room_id'];		//發文者所在處室

//$b_title = addslashes($row["title_name"]); //職稱  2014.04.23 後以 teach_title_id 取代
$b_title=$row['teach_title_id'];

///mysqli
$query = "select board_name,board_date,board_k_id,board_last_date,board_is_upload,board_is_public,board_admin from jboard_kind ";
$query .= "where bk_id =? ";

$mysqliconn = get_mysqli_conn();
$stmt = "";
$stmt = $mysqliconn->prepare($query);
$stmt->bind_param('s', $bk_id);
$stmt->execute();
$stmt->bind_result($board_name,$board_date,$board_k_id,$board_last_date,$board_is_upload,$board_is_public,$board_admin);
$stmt->fetch();
$stmt->close();
///mysqli

/*
$query = "select * from jboard_kind ";
$query .= "where bk_id ='$bk_id' ";
$result= $CONN->Execute($query) or die ($query);
$row = $result->fetchRow();
	$board_name = $row["board_name"];
	$board_date = $row["board_date"];
	$board_k_id = $row["board_k_id"];
	$board_last_date = $row["board_last_date"];
	$board_is_upload = $row["board_is_upload"];
	$board_is_public = $row["board_is_public"];
	$board_admin = $row["board_admin"];
*/	
	
if ($_POST['key'] == "確定儲存"){
	$b_post_time = mysql_date();
	$b_sort=($_POST['top_days']==0)?"100":"99";
/*		
$sql_insert = "insert into jboard_p(bk_id,b_open_date,b_days,b_unit,b_title,b_name," .
			"b_sub,b_con,b_url,b_own_id,b_post_time,b_is_intranet,teacher_sn,b_is_sign,b_is_marquee,b_sort,top_days) values " .
			"('{$_POST['bk_id']}','{$_POST['b_open_date']}','{$_POST['b_days']}'," .
			"'$b_unit','$b_title','$b_name','{$_POST['b_sub']}','{$_POST['b_con']}'," .
			"'{$_POST['b_url']}','{$_SESSION['session_log_id']}',now()," .
			"'{$_POST['b_is_intranet']}','{$_SESSION['session_tea_sn']}','{$_POST['b_is_sign']}','{$_POST['b_is_marquee']}','$b_sort','{$_POST['top_days']}')";

	$CONN->Execute($sql_insert) or die ($sql_insert);
	//echo $sql_insert;
	$b_id = $CONN->Insert_ID();
*/

//mysqli	
$sql_insert = "insert into jboard_p(bk_id,b_open_date,b_days,b_unit,b_title,b_name," .
			"b_sub,b_con,b_url,b_own_id,b_post_time,b_is_intranet,teacher_sn,b_is_sign,b_is_marquee,b_sort,top_days) values " .
			"(?,?,?," .
			"'$b_unit','$b_title','$b_name',?,?," .
			"?,'{$_SESSION['session_log_id']}',now()," .
			"?,'{$_SESSION['session_tea_sn']}',?,?,'$b_sort',?)";
$stmt = "";
$stmt = $mysqliconn->prepare($sql_insert);
$stmt->bind_param('ssssssssss',check_mysqli_param($_POST['bk_id']),check_mysqli_param($_POST['b_open_date']),check_mysqli_param($_POST['b_days']),check_mysqli_param($_POST['b_sub']),check_mysqli_param($_POST['b_con']),check_mysqli_param($_POST['b_url']),check_mysqli_param($_POST['b_is_intranet']),check_mysqli_param($_POST['b_is_sign']),check_mysqli_param($_POST['b_is_marquee']),check_mysqli_param($_POST['top_days']));
$stmt->execute();

$b_id=mysqli_stmt_insert_id($stmt);
$stmt->close();
///mysqli		


	//處理圖片
  $sPath = $USR_IMG_TMP.'images/';

  //把 $b_con 的 <img src""> 確實有檔案存在的作處理, 圖檔存入資料庫後刪除
  //必須先取得  $b_id, $sPath ,$b_con 全域變數
  $b_con=$_POST['b_con'];
	GetImgFromHTML(); 
	
	//載入檔案處理程式	
  include_once("board_files_upload.php");

	if (!$error_flag)
		Header ("Location: board_view.php?bk_id=$bk_id");
}

//是否有獨立的界面
if ($is_standalone)
	include "header.php";
else
	head("Joomla!新增文章");

$b_open_date = date("Y-m-j");
// <script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></Script>

?>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></Script>

<Script type="text/javascript" src="../../include/ckeditor/ckeditor.js"></Script>

<script language="JavaScript">
	//$(document).ready(function() {
	//	CKEDITOR.replace('b_con',{ filebrowserImageUploadUrl: '<?php echo $USR_IMG_TMP;?>' });
  //});
	
function checkok()
{
	var OK=true
	if(document.eform.b_sub.value == "")
	{
		OK=false;
	}
	if (OK == false)
	{
		alert("標題不可空白")
	}
	return OK
}

//-->
</script>

<script type="text/javascript" src="<?php echo $SFS_PATH_HTML ?>javascripts/forms.js"></script>

<form enctype="multipart/form-data" name=eform method="post" action="<?php echo $PHP_SELF ?>" onSubmit="return checkok()">

<?php
//顯示錯誤訊息
if ($error_flag)
	echo "<h3><font color=red>錯誤 !! 不可上傳 php 程式檔!!</font></h3>";

include_once("board_form.php");
?>


<input type="hidden" name="board_name" value="<?php echo $board_name ?>">
<input type="submit" name="key" value="確定儲存">
<input TYPE="button" VALUE="回上一頁" onclick="window.location='board_view.php'">

</form>


<?php
//若啟用 html 編輯器
if ($enable_is_html<>'') {  
	?>
	<script>
		CKEDITOR.replace('b_con',{ language: 'zh'},{ filebrowserImageUploadUrl: '<?php echo $USR_IMG_TMP;?>' });
		</script>
	<?php
	}

if($is_standalone)
	include	"footer.php";
else
	foot();
?>
