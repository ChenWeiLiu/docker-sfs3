<?php
                                                                                                                             
// $Id: tea_upload.php 8743 2016-01-08 14:02:58Z qfon $

//系統設定檔
include "exam_config.php";
$exam_id=$_POST[exam_id];
if ($exam_id=='')
	$exam_id = $_GET[exam_id];

$e_kind_id = $_POST[e_kind_id];

//mysqli
$mysqliconn = get_mysqli_conn();	



// 檢查檔案 (zip 格式)
function list_file($m_path) {
	
	exec("ls -l ".$m_path , $result, $id);
			$i = 1;
			$temp_ok = "";
			while (isset($result[$i])) {
				$result[$i] = eregi_replace(" +", ",", $result[$i]);
				$line = explode(",", $result[$i]);
				
				if (!ereg("^d", $line[0]))  {					
					$temp_ok .= $line[8]."上傳 完成!<br>\n";
					$f_temp = explode(".", $line[8]);
					//將 .php .php3 檔改為 .phps
					//if ($f_temp[count($f_temp)-1] == 'php' || $f_temp[count($f_temp)-1] == 'php3')
					if (is_php_name($f_temp[count($f_temp)-1]))
						exec ( "mv $m_path/".$line[8]." $m_path/".$f_temp[0].".phps" , $val);
				}			
				else {
					 list_file($m_path."/".$line[8]);	
				}
				$i++;

			}
}

//檢查檔案
//檢查到 return true 
function is_php_name ($chk) {
	global 	$limit_file_name ; //不允許之副檔名 在 exam_config.php 中設定
	$flag = false;
	for($i=0;$i<count($limit_file_name);$i++)
		if ($chk == $limit_file_name[$i])
			$flag = true;
	return $flag;
}

session_start();
if ($_SESSION[session_stud_id] == "" && $_SESSION[session_log_id] == "" ){	
	$exename = $_SERVER[REQUEST_URI];
	include "checkid.php";
	exit;
}
//教師部份
if ($_SESSION[session_log_id] !=""){
	$s_stud_id = "demo_".$_SESSION[session_log_id];
	$s_stud_name = addslashes ($_SESSION[session_tea_name]);
}
else {
	$s_stud_id = $_SESSION[session_stud_id];
	$s_stud_name = addslashes ($_SESSION[session_stud_name]);
}

if ($_POST[key] == "上傳作業") {
	
	//建立目錄
	if (!is_dir($upload_path))
		mkdir($upload_path, 0755); //上傳目錄

	$e_path = $upload_path."/e_".$_POST[exam_id];
	if (!is_dir($e_path))
		mkdir($e_path, 0755);//作業目錄

	//判斷檔名
	$f_name = $_FILES[infile][name];
	$f_size = $_FILES[infile][size];
	$f_temp = explode(".", $f_name);	
	//if ($f_temp[count($f_temp)-1] == 'php' || $f_temp[count($f_temp)-1] == 'php3')
	if (is_php_name($f_temp[count($f_temp)-1]))
		$f_name = $f_temp[0].".phps";
	else if ($f_temp[count($f_temp)-1] == 'zip' )

		$f_name_src = "zip";
	


	//刪除舊檔案  
	$exam_id=intval($exam_id);
	$sql_update = "select * from  exam_stud \n";
	$sql_update .= "where stud_id='$s_stud_id' and exam_id='$exam_id'";
	$result = $CONN->Execute($sql_update)or die($sql_update);	
	$alias = $e_path."/".$s_stud_id."_".$result->fields["f_name"];
	$f_temp = explode(".", $alias);
	if (file_exists($alias))
		unlink($alias);
			
	
	//加入時間變數
	$ff_name = time()."_".$f_name;

	$USR_DESTINATION = $e_path."/".$s_stud_id."_".$ff_name;
	if(file_exists($_FILES[infile][tmp_name])){
		copy($_FILES[infile][tmp_name], $USR_DESTINATION); 
		//更新資料庫		
		//$sql_update = "delete from  exam_stud \n";
		//$sql_update .= "where stud_id='$s_stud_id' and exam_id='$exam_id'";
		//$result = mysql_query ($sql_update);
		
		$query = "select stud_id from exam_stud where stud_id='$s_stud_id' and exam_id='$exam_id'";
		$result= $CONN->Execute($query);
		//學生坐號
		$stud_num = substr($_SESSION[session_curr_class_num] ,-2);
		if ($result->RecordCount()== 0 ) { //新增資料		
			/*
			$sql_insert = "insert into exam_stud (exam_id,stud_id,stud_name,stud_num,memo,f_name,f_size,f_ctime) values ('$exam_id','$s_stud_id','$s_stud_name','$stud_num','$_POST[memo] ','$ff_name ','$f_size','$now')";
			$CONN->Execute($sql_insert)or die ($sql_insert);
			*/
//mysqli
$sql_insert = "insert into exam_stud (exam_id,stud_id,stud_name,stud_num,memo,f_name,f_size,f_ctime) values ('$exam_id','$s_stud_id','$s_stud_name','$stud_num',?,'$ff_name ','$f_size','$now')";
$stmt = "";
$stmt = $mysqliconn->prepare($sql_insert);
$stmt->bind_param('s', $_POST[memo]);
$stmt->execute();
$stmt->close();
///mysqli	
			
			
			
		}
		else {
			/*
			$query = "update exam_stud set memo='$_POST[memo]',f_name='$ff_name',f_size='$f_size',f_ctime='$now'  where stud_id='$s_stud_id' and exam_id='$exam_id'";
			$CONN->Execute($query)or die ($query);
			*/
//mysqli
$query = "update exam_stud set memo=?,f_name='$ff_name',f_size='$f_size',f_ctime='$now'  where stud_id='$s_stud_id' and exam_id='$exam_id'";
$stmt = "";
$stmt = $mysqliconn->prepare($query);
$stmt->bind_param('s', $_POST[memo]);
$stmt->execute();
$stmt->close();
///mysqli	
		
		
		
		}
		$m_path = $e_path."/".$s_stud_id;
		//如為目錄則刪除整個目錄
		if (is_dir($m_path))
			exec( "rm -rf $m_path", $val );
		if ($f_name_src == "zip"){		
			mkdir( $m_path , 0755);  //個人zip解開目錄
			//解開檔案
			exec("unzip  $USR_DESTINATION -d $m_path",$val);

			//列出檔案檢查有無 .php .php3 檔
			list_file($m_path);
			
		}
		if ($temp_ok =="") //直接回作業區
			header ("Location: tea_show.php?e_kind_id=$e_kind_id&exam_id=$exam_id");
		else {
			include "header.php";
			echo $temp_ok;
		}
			
	}
	else {
		include "header.php";
		echo "<h2>檔案上傳失敗!!</h2><br>\n";
		echo "<form><input type=\"button\"  value= \"回上頁\" onclick=\"history.back()\"></form>";
	}
	echo "<hr width=200><a href=\"tea_show.php?e_kind_id=$e_kind_id&exam_id=$exam_id\">回作業列表</a>";
	include "footer.php";
	exit;
}
$exam_id=intval($exam_id);
$sql_select = "select exam_kind.e_upload_ok,exam_kind.class_id,exam.exam_id,exam.teach_id,exam.exam_isupload from exam_kind,exam \n";       
$sql_select .= "where exam_kind.e_kind_id =exam.e_kind_id and  exam.exam_id='$exam_id'";
$result = $CONN->Execute($sql_select)or die($sql_select);
//取出使用學生的學年度(3位元)學期(1位元)年級(1位元)學期(1位元)班別(1~ 位元)
$tempc = sprintf("%03s%d%s",curr_year(),curr_seme(),substr($_SESSION[session_curr_class_num],0,strlen($_SESSION[session_curr_class_num])-2));
//echo "$tempc,$row[class_id]<BR>";
//檢查上傳權限
//判斷是否開始上傳作業 exam_isupload == 1
//判斷是否為該班學生或指導教師，再給予上傳權限
if (($result->fields["class_id"] != $tempc || $result->fields["e_upload_ok"] != "1")&& $result->fields["teach_id"] != $_SESSION[session_log_id] || $result->fields["exam_isupload"] != "1") {
	//echo "dddd:".$row["e_upload_ok"];
	echo "<h2>本項作業目前未授權上傳</h2> ";
	echo "<form><input type=\"button\"  value= \"回上頁\" onclick=\"history.back()\"></form>";
	include "footer.php";
	exit;
}

$sql_select = "select exam_id,stud_id,memo from exam_stud \n";
$sql_select .= "where stud_id='$s_stud_id' and exam_id='$exam_id'";
$result = $CONN->Execute($sql_select) or die($sql_select);
$memo = $result->fields["memo"]; 

include "header.php";
?>

<form  enctype="multipart/form-data" method="post">
<?php 
if ($_SESSION[session_tea_name] !="")
	echo "<h3>指導老師：$_SESSION[session_tea_name] </h3>";
else
	echo "<h3>學員：$_SESSION[session_stud_name] </h3>";
?>
<h3>上傳 <font color=red><?php echo stripslashes ($_GET[exam_name]); ?></font> 作業</h3>
<table>

<tr>
	<td>作業說明<br>
		<textarea name="memo" cols=40 rows=5 wrap=virtual><?php echo $memo ?></textarea>
	</td>
</tr>
<tr>
<td>
	檔案位置<br><input type=file name="infile" size=36 >
</table>
	<input type="hidden"  name="exam_id" value="<?php echo $exam_id ?>">
	<input type="submit"  name="key" value="上傳作業">
	
</form>
<hr width=200><a href="exam_list.php">回作業列表</a>
<?php
	include "footer.php";
?>
