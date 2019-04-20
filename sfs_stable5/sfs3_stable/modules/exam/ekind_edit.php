<?php                                                                                                                             
// $Id: ekind_edit.php 8874 2016-04-21 02:09:24Z qfon $
// --系統設定檔
include "exam_config.php";

//判別是否為系統管理者
$man_flag =  checkid($_SERVER[SCRIPT_FILENAME],1);

//判斷是否為管理者 $perr_man 陣列設定在 exam_config.php
if (!$man_flag) {	
	$str = "你未被授權使用本功能，參考系統說明檔" ;
	redir("exam.php",3) ;
	exit;
}
$e_kind_id = $_GET[e_kind_id];
if ($e_kind_id =='')
	$e_kind_id = $_POST[e_kind_id];


if(!checkid(substr($_SERVER[PHP_SELF],1))){
	$go_back=1; //回到自已的認證畫面  
	include "header.php";
	include "$rlogin";
	include "footer.php"; 
	exit;
}



//mysqli
$mysqliconn = get_mysqli_conn();


//刪除處理
if ($_GET[sel] =="delete"){
	include "header.php";
	
	//班級資料
	$e_kind_id=intval($e_kind_id);
	$query = "select class_id from exam_kind where e_kind_id = '$e_kind_id' ";
	$result = $CONN->Execute($query);
	$class_id = $result->fields[0];
	$temp_year = substr($class_id,4,1); //取得年級	
	$temp_class = substr($class_id,5); //取得班級
	//作業名稱
	$query = "select exam_name from exam where e_kind_id = '$e_kind_id' ";
	$result = $CONN->Execute($query);
	$tt ="";
	while(!$result->EOF){
		$tt.= "<li>".$result->fields[0];
		$result->MOveNext();
	}
	
        echo "<form action=\"$_SERVER[PHP_SELF]\" method=\"post\">\n"; 
        echo "確定刪除 <font color=red>$class_year[$temp_year]$class_name[$temp_class]班</font> ？<br>";
        if ($tt !="") 
        	echo "<br>及其所有的作業，如下列<br>".$tt;
        echo "<hr><input type=\"hidden\" name=\"e_kind_id\" value=\"$e_kind_id\">\n";
        echo "<input type=\"submit\" name=\"key\" value=\"確定刪除\" >\n";
        echo "&nbsp;&nbsp;<input type=button  value= \"回上頁\" onclick=\"history.back()\">";

        echo "</form>";
        include "footer.php";
	exit;
}  
if ($_POST[key] =="確定刪除"){
	//刪除上傳目錄資料
	$e_kind_id=intval($e_kind_id);
	$query = "select exam_id from exam where e_kind_id ='$e_kind_id'";
	$result = $CONN->Execute($query)or die($query);
	while (!$result->EOF) {
		$exam_id = $result->fields[0];
		//檔案目錄
		$e_path = $upload_path."/e_".$result->fields[0]; 
		if (is_dir($e_path))
			exec( "rm -rf $e_path", $val );
		//刪除學生上傳記錄
		$sql_update = " delete from exam_stud ";	
		$sql_update .= " where exam_id='$exam_id' ";
		$CONN->Execute($sql_update)  or die ($sql_update);

		$result->MoveNext();
	}
	//刪除 班級資料
    $sql_update = " delete from exam_kind ";
	$sql_update .= " where e_kind_id='$e_kind_id' ";
	$result = $CONN->Execute($sql_update)  or die ($sql_update);  
	//刪除 班級作業資料
	$sql_update = " delete from exam ";
	$sql_update .= " where e_kind_id='$e_kind_id' ";
	$result = $CONN->Execute($sql_update)  or die ($sql_update); 
	
	header ("Location: ekind.php");
}

//修改處理
if ($_POST[key] =="修改"){
	$e_kind_id=intval($e_kind_id);
	$class_id = $_POST[curr_year].$_POST[curr_class_year].$_POST[curr_class_name];
	/*
	$sql_update = "update exam_kind set e_kind_memo='$_POST[e_kind_memo]',e_kind_open='$_POST[e_kind_open]' ,e_upload_ok='$_POST[e_upload_ok]' ,class_id='$class_id'";
	$sql_update .= " where e_kind_id='$e_kind_id' ";
//	echo $sql_update;exit;
	$result = $CONN->Execute($sql_update)  or die ($sql_update);  
	*/
	
//mysqli
$sql_update = "update exam_kind set e_kind_memo=?,e_kind_open=? ,e_upload_ok=? ,class_id=?";
$sql_update .= " where e_kind_id='$e_kind_id' ";
$stmt = "";
$stmt = $mysqliconn->prepare($sql_update);
$stmt->bind_param('ssss', $_POST[e_kind_memo],$_POST[e_kind_open],$_POST[e_upload_ok],$class_id);
$stmt->execute();
$stmt->close();
///mysqli	
	

	header ("Location: ekind.php");
}

//取得班級版區資料
$e_kind_id=intval($e_kind_id);
$sql_select = "select e_kind_id,e_kind_memo,e_kind_open,e_upload_ok from exam_kind";
$sql_select .= " where e_kind_id='$e_kind_id' ";
$result = $CONN->Execute($sql_select);
if ($result->RecordCount() > 0 ){
	$e_kind_id = $result->fields["e_kind_id"];	
	$e_kind_memo = $result->fields["e_kind_memo"];
	if ($result->fields["e_kind_open"]=="1")
		$e_kind_open =" checked ";
	else
		$e_kind_open ="";
	     
	if ($result->fields["e_upload_ok"]=="1")
		$e_upload_ok =" checked ";
	else
		$e_upload_ok ="";	     
}
   
include "header.php";
include "menu.php";

// $class_id 0-3 ->學年 4->學期 5->年級 6- 班級 	
$curr_year = intval(substr($_GET[class_id],0,3))."學年度";
if (substr($_GET[class_id],3,1) == 1 )
	$temp_seme = "上學期";
else
	$temp_seme = "下學期";
	
$temp_year = substr($_GET[class_id],4,1); //取得年級	
$temp_class = substr($_GET[class_id],5); //取得班級

?>
<h3>修改班級資料(<font color=red><?php echo $curr_year. $temp_seme ?></font>)</h3>
<form action ="<?php echo $_SERVER[PHP_SELF] ?>" method="post" >
<input type= hidden name=curr_class_id value="<?php echo $curr_class_id ?>" >
<table>

<tr>
	<td>班級名稱<br>
	<select	name="curr_class_year">
	<?php
	reset($class_year);
	 while(list($tkey,$tvalue)= each ($class_year)) {
		  if ($tkey == $temp_year)	  
			 echo  sprintf ("<option value=\"%s\" selected>%s</option>\n",$tkey,$tvalue);
		   else
			 echo  sprintf ("<option value=\"%s\">%s</option>\n",$tkey,$tvalue);
	}             	 
	?>


	</select>

	<select	name="curr_class_name">
	<?php
	$class_temp ="";
	reset($class_name);
	 while(list($tkey,$tvalue)= each ($class_name)) {
		if ($tkey == $temp_class)
			$class_temp .=  sprintf ("<option value=\"%s\" selected>%s</option>\n",$tkey,$tvalue);
		else
			$class_temp .=   sprintf ("<option value=\"%s\">%s</option>\n",$tkey,$tvalue);
	}             	 
	echo $class_temp ; 
	?>
 
 	</select>
	</td>
</tr>

<tr>
	<td>是否開放展示作業<br>
		<input type="checkbox" name="e_kind_open" value=1  <?php echo $e_kind_open; ?>>
	</td>
</tr>

<tr>
	<td>是否開放上傳作業<br>
		<input type="checkbox" name="e_upload_ok" value=1  <?php echo $e_upload_ok; ?>>
	</td>
</tr>

<tr>
	<td>說明<br>
		<textarea name="e_kind_memo" cols=40 rows=5 wrap=virtual><?php echo $e_kind_memo ?></textarea>
	</td>
</tr>
<tr>
	<td>
	<input type="hidden" name=e_kind_id value="<?php echo $e_kind_id; ?>">
	<input type="hidden" name=curr_year value="<?php echo substr($_GET[class_id],0,4); ?>">
	<input type="submit" name=key value="修改">
	&nbsp;&nbsp;<input type="button"  value= "回上頁" onclick="history.back()">
	</td>
</tr>

</table>

<?php include "footer.php"; ?>
