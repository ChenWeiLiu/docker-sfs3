<?php

// $Id: board_show.php 7779 2013-11-20 16:09:00Z smallduh $

// --系統設定檔
include	"board_config.php";
include_once "../../include/sfs_case_dataarray.php";

// 回簽處理
if ($_GET['act']=='Sign'){
	if ($_SESSION['session_tea_sn']<>''){
		$_GET['b_id']=intval($_GET['b_id']);
		$query = "SELECT b_signs FROM jboard_p WHERE b_id='{$_GET['b_id']}'";
		$res = & $CONN->Execute($query);
		$b_signs = $res->fields['b_signs'];

		if (false === CheckIsSigned($b_signs)){
			$time = time();
			$b_signs .= ','.$_SESSION['session_tea_sn']."^".$time;
			$query = "UPDATE jboard_p SET b_signs='$b_signs' WHERE b_id='{$_GET['b_id']}'";

			if ($CONN->Execute($query))
					echo strftime("%Y-%m-%d %H:%M:%S",$time);
		}
	}
exit;
}

// 查詢回簽狀況
if ($_GET['act'] == 'show_sign'){
		if ($_SESSION['session_tea_sn']<>''){
			$_GET['b_id']=intval($_GET['b_id']);
			$query = "SELECT b_signs FROM jboard_p WHERE b_id='{$_GET['b_id']}'";
			$res = & $CONN->Execute($query);
		 $b_signs = $res->fields['b_signs'];
			$sign_arr = CheckIsSigned($b_signs,1);
			$student_sn_key = implode(",",array_keys($sign_arr));
			$post_office_p = room_kind();
			$class_name = class_base();
			header("Content-type: text/html; charset=big5");
			echo "<br> <hr/>";
			echo '<h3>回簽情形</h3>';
			if (!$sign_arr){
				echo '沒有回簽資料!';

				exit;
			}
			echo "<table style='background-color:#ffccff;width:600;text-align:center'>" .
					"<tr><td>處室</td><td>職稱</td><td>姓名</td><td>簽收時間</td></tr>";
			$query="
	SELECT a.teacher_sn,a.teach_id,a.name, b.post_kind, b.post_office, d.title_name ,b.class_num
	FROM teacher_base a , teacher_post b, teacher_title d
	where a.teacher_sn = b.teacher_sn
	and b.teach_title_id = d.teach_title_id AND a.teacher_sn IN ($student_sn_key)
	 ORDER BY b.teach_title_id, b.class_num";
		$recordSet = $CONN->Execute($query) or user_error($query,256);
		$ii = 0;

		while($row = $recordSet->fetchRow()){
			if ($ii++ % 2 == 0)
				echo "<tr style='background-color:#fff'>";
			else
				echo "<tr style='background-color:#fef'>";
			if ($class_num)
				echo "<td>".$class_name[$row['class_num']]."</td>";
			else
				echo "<td>".$post_office_p[$row['post_office']]."</td>";

			echo "<td>".$row['title_name']."</td>";
			echo "<td>".$row['name']."</td>";
			echo "<td>".strftime("%Y-%m-%d %H:%M:%S",$sign_arr[$row['teacher_sn']])."</td>";
			echo "</tr>";
		}
		echo "</table>";

			$str = ob_get_contents();
			ob_end_clean();
			echo $str;//echo iconv('Big5','UTF-8',$str);
		}
		exit;
}

//-----------------------------------
//是否有獨立的界面
if ($is_standalone)
	include "header.php";
else
	head("Joomla!文章編輯");


$b_id= intval($_GET['b_id']);
$query="update jboard_p set b_hints = b_hints+1 where b_id='$b_id' ";
$CONN->Execute($query);
$query = "select  * from jboard_p  where b_id='$b_id' ";
$result = $CONN->Execute($query);
$row= $result->fetchRow();
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
$b_own_id = $row["b_own_id"];
$b_url = $row["b_url"];
$b_post_time = $row["b_post_time"];
$b_is_intranet = $row["b_is_intranet"];
$teacher_sn = $row["teacher_sn"];
$b_is_sign = $row['b_is_sign'];
$b_signs = $row['b_signs'];


//讀取本分類設定
$BOARD_SETUP=get_board_kind_setup($bk_id);


//處理圖片的 <img src="">
//$b_con=GetImgFromHTML($b_con);
/*
if (count($pics[2])) {
 foreach ($pics[2] as $pic_key) {
   $K=explode("/",$pic_key);
   $Key_len=strlen($k[count($K)-1]);
   $Key=substr($pic_key,0,strlen($pic_key)-$Key_len);
   $new_pic="img_show.php?name=".$K[count($K)-1];
   $b_con=ereg_replace($pic_key,$new_pic, $b_con); 
 } 
}
*/



//強制須登入
if($login_force and !$_SESSION[session_tea_sn]) {
	redir_str("../../login.php","<br><center>請先登入學務系統，方可檢視公告內容！</center>",5);
	exit;
}

//if ($b_own_id !="$session_log_id" && $b_is_intranet == '1' && !check_home_ip()) {
if (empty($_SESSION[session_tea_sn]) && $b_is_intranet == '1' && !$is_home_ip) {
	redir_str("board_view.php","抱歉!! 本訊息為校內文件，僅供本校參考",5);
	exit;
}

?>

<script type="text/javascript" src="<?php echo $SFS_PATH_HTML ?>javascripts/prototype.js"></script>
<script type="text/javascript">
function showSign() {
	new Ajax.Request('<?php echo $_SERVER['PHP_SELF'] ?>', {
  method: 'get',
  parameters: {act: 'show_sign', b_id: '<?php echo $b_id ?>' },
  onSuccess: function(transport){
  var response = transport.responseText;
     if (response !=''){
          document.getElementById('show_sign').innerHTML=response;
              }
    },
  onFailure: function(){ alert('錯誤!') }
  });
}

function signAct() {
	new Ajax.Request('<?php echo $_SERVER['PHP_SELF'] ?>', {
  method: 'get',
  parameters: {act: 'Sign', b_id: '<?php echo $b_id ?>' },
  onSuccess: function(transport){
  var response = transport.responseText;
     if (response !=''){
          document.getElementById('signBtn').innerHTML='您已於 ' + response + ' 簽收此公告';
          	showSign();
              }
     else{
     	alert('請先登入,再簽收');
     }
    },
  onFailure: function(){ alert('錯誤!') }
  });
}
</script>
<center>
<?php
	if ($b_is_sign AND isset($_SESSION[session_tea_sn]) ){
	echo "<a  href='#' onclick=\"showSign()\" class='b1'>簽收查詢</a> &nbsp;| ";
	}
	if(isset($_SESSION[session_tea_sn]) and (($teacher_sn ==$_SESSION[session_tea_sn]) || checkid($_SERVER[SCRIPT_FILENAME],1) || (board_checkid($bk_id) and $BOARD_SETUP['board_is_coop_edit']))){
	//if(isset($_SESSION[session_tea_sn]) and (board_checkid($bk_id) || checkid($_SERVER[SCRIPT_FILENAME],1) )){
		echo "<font size=3>《<a href=\"board_edit.php?bk_id=$bk_id&b_id=$b_id\">修改內容</a>》｜";
		echo "《<a href=\"board_delete.php?bk_id=$bk_id&b_id=$b_id\">刪除這篇文章</a>》｜";
	}
	echo "回《<a href=\"board_view.php\"><b>近期文章</b>列表</a>》</font>";
	if ($bk_id != "" and (board_checkid($bk_id) || checkid($_SERVER[SCRIPT_FILENAME],1)))
		echo "回《<a href=\"board_view.php?bk_id=$bk_id\"><b>".$BOARD_SETUP['board_name']."</b>列表</a>》</font>";  
?>
</center>
<table align="center" border="1" cellPadding="3" cellSpacing="0" width="100%">

	<tr bgColor="#f1f5cd">
		<td height="30"><b>標題：<?php echo $b_sub ?></b></td>
	</tr>
	<tr bgColor="#ffffff">
		<td height="30">
	<?php
		if (eregi("<[[:space:]]*([^>]*)[[:space:]]*>",$b_con))
			echo $b_con;
		else
	 		echo nl2br($b_con);
	  ?>
		</td>
	</tr>
<?php
	if($b_url != ""){
		if (eregi("http://",$b_url)) { $b_url ="<a href=\"$b_url\" target=\"window\">$b_url</a> ";	} else if (eregi("https://",$b_url)) { $b_url ="<a href=\"$b_url\" target=\"window\">$b_url</a> ";	}
?>
	<tr bgColor="#ffffff">
		<td height="30"><?php echo "相關網址： $b_url"; ?></td>
	</tr>
<?php
	$bgcolor= "#ffffff";
	} ?>

<?php
	if($fArr = board_getFileArray($b_id)){ ?>
	<tr bgColor="#ffffff">
	<td height="30">
	檔案下載：
	<ul>
	<?php
	foreach ($fArr as $fId => $fName){
		if ($fName['new_filename']!="") {
		//$Org=($fName['org_filename']!="")?"(原檔名：".$fName['org_filename'].")":"";
		echo "<li><a href='#' onclick='download_file(\"".$fName['new_filename']."\")'>".$fName['org_filename']."</a></li>";
	  }
	}
	?>
	</ul>
	 </td>
	</tr>
<?php  } ?>

<?php
	if ($b_is_sign){
		?>
	<tr bgColor="#ffffff">
		<td>
		<?php
			if ($SignTime = CheckIsSigned($b_signs)){
				echo '您已於 '. strftime("%Y-%m-%d %H:%M:%S",$SignTime).' 簽收此公告';
			}
			else{
					echo "<span id='signBtn'><input type='button'  onClick=\"signAct()\" class='b1' value='簽收此公告'></span>";
			}
		}
		echo '</td>	</tr>';
		?>

	<tr bgColor="#ffffff" align=right>
		<td>
			<font size=2><?php echo $TEACHER_TITLE[$b_title]." $b_name 於 $b_post_time 發布"; ?></font>
		</td>
	</tr>
</table>
<center>
<?php
	if ($b_is_sign AND isset($_SESSION[session_tea_sn]) ){
	  echo "<a  href='#' onclick=\"showSign()\" class='b1'>簽收查詢</a> &nbsp;| ";
	}
	if(isset($_SESSION[session_tea_sn]) and (($teacher_sn ==$_SESSION[session_tea_sn]) || checkid($_SERVER[SCRIPT_FILENAME],1) || (board_checkid($bk_id) and $BOARD_SETUP['board_is_coop_edit']))){
		echo "<font size=3>《<a href=\"board_edit.php?bk_id=$bk_id&b_id=$b_id\">修改內容</a>》｜";
		echo "《<a href=\"board_delete.php?bk_id=$bk_id&b_id=$b_id\">刪除這篇文章</a>》｜";
	}
	echo "回《<a href=\"board_view.php\"><b>近期文章</b>列表</a>》</font>";
	if ($bk_id != "" and (board_checkid($bk_id) || checkid($_SERVER[SCRIPT_FILENAME],1)))
		echo "回《<a href=\"board_view.php?bk_id=$bk_id\"><b>".$BOARD_SETUP['board_name']."</b>列表</a>》</font>";
  
?>
<div id="show_sign"></div>
</center>
<?php
//是否有獨立的界面
if ($is_standalone)
	include "footer.php";
else
	foot();
	
?>
<form method="post" name="form1" action="file_download.php">
 <input type="hidden" name="filename" value="">
 <input type="hidden" name="b_id" value="<?php echo $b_id;?>">
</form>

<Script>
function download_file (new_filename){
  //alert(new_filename);
  //exit();	
	document.form1.filename.value=new_filename;
	document.form1.submit();	
}

</Script>