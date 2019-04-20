<?php
                                                                                                                             
// $Id: class_code.php 8753 2016-01-13 12:40:19Z qfon $

// --系統設定檔
include "book_config.php";
include_once "../../include/sfs_case_PLlib.php";
// --認證 session 
//session_start();
//session_register("session_log_id"); 
$class_year = year_base();
if(!checkid(substr($_SERVER['PHP_SELF'],1))){
	$go_back=1; //回到自已的認證畫面  
	include "header.php";
	include "$rlogin";
	include "footer.php"; 
	exit;
}
if ($_POST['key'] =="製作圖書證"){
	echo "<html><body><table border=1 cellPadding=5 cellSpacing=10 ><tr>";
//mysqli		
$mysqliconn = get_mysqli_conn();
$query = "select stud_id,stud_name from stud_base  where curr_class_num like ? and stud_study_cond =0 order by curr_class_num";
$stmt = "";
$stmt = $mysqliconn->prepare($query);
$_POST[class_id]="$_POST[class_id]%";
$stmt->bind_param('s',$_POST[class_id]);
$stmt->execute();
$stmt->bind_result($stud_id,$stud_name);

//mysqli	
    /*
	$query = "select stud_id,stud_name from stud_base  where curr_class_num like '$_POST[class_id]%' and stud_study_cond =0 order by curr_class_num";
	$result = mysql_query ($query,$conID) or die ($query);
	*/
	//while ($row= mysql_fetch_array($result)){
	while ($stmt->fetch()) {
		//$core = $row["stud_id"];
		$core = $stud_id;
		//$topname = "$school_sshort_name"."--".$row["stud_name"];
		$topname = "$school_sshort_name"."--".$stud_name;
		echo "<td align=center nowrap ><font size=2>$topname<BR>";
		barcode($core);
		echo "<br>$core</font></td>\n";
//		echo sprintf ("<img src=\"%s?code=%s&text=%s\">",$code_url,$row["stud_id"],"$school_sshort_name"."--".$row["stud_name"] );
		if ($i++ % $barcore_cols == $barcore_cols-1 )
			echo"</tr><tr>";
	}
	echo "</tr></table>";	
	echo "</body></html>";
	exit;
}
include "header.php";
$code_p =$_SERVER['PHP_SELF'];
?>

<center>
<h3>班級圖書證列印</h3><form action="<?php echo $_SERVER['PHP_SELF'] ?>" method= "post">
<?php
	$class_base = class_base();
	$sel = new drop_select();
	$sel->id=$_POST['class_id'];
	$sel->arr =$class_base;
	$sel->s_name = "class_id";
	$sel->top_option="選擇班級";
	$sel->do_select();
?>

<hr><input type=submit name=key value="製作圖書證">

</form>

<?php
foot();
?>
