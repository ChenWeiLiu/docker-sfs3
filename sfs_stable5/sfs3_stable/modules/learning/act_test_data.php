<?php

// $Id: act_test_data.php 7708 2013-10-23 12:19:00Z smallduh $

// --系統設定檔
include "config.php";
//限教師進入
if($_SESSION['session_who']=='教師'){

// $unit 唯一傳入的單元代號

$m = substr ($unit, 0, 1); 
$t = substr ($unit, 1, 2); 
$u = trim (substr ($unit, 3, 4)); 

//取得各領域冊別
$sqlstr = "select * from unit_tome where  unit_m='$m' and unit_t='$t' " ;
$result = mysql_query($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256);
$row= mysql_fetch_array($result);
$c_tome = $row["unit_tome"];
$tome_ver = $row["tome_ver"];
//取得單元名稱
$sqlstr = "select * from unit_u where  unit_m='$m'  and unit_t='$t' and u_s='$u' and tome_ver='$tome_ver' ";
$result = mysql_query($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256);
$row= mysql_fetch_array($result);
$c_unit = $row["unit_name"];
$u_id = $row["u_id"];
$msg_err="";
if($u_id==""){   //無此單元
	$s_unit="<font size=7 color=red>無此單元的題庫！</font>";
}
$s_title= $modules[$m] . $c_tome .$c_unit  ; 



//--認證 session
// sfs_check();

// 不需要 register_globals
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}


if ($do_key =="CSV 輸出") {		
	$filename = $s_title.".csv";	
	header("Content-disposition: filename=$filename");
	header("Content-type: application/octetstream; Charset=Big5");
	//header("Pragma: no-cache");
					//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");

	header("Expires: 0");
	
		$ma .= "題目類型(0:選擇 1:複選 2:填充),答案,問題,選項1,選項2,選項3,選項4,選項5,選項6,(第一列留作標題，請不要修改)\n";

$sqlstr = "select * from test_data   where  u_id='$u_id' " ;
$result =$CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ;
while ($row = $result->FetchRow() ) {    	
	$ques = $row["ques"] ;  
	$ch1= $row["ch1"] ;  
	$ch2= $row["ch2"] ;  
	$ch3= $row["ch3"] ;  
	$ch4= $row["ch4"] ;  
	$ch5 = $row["ch5"] ;  
	$ch6 = $row["ch6"] ;  
	$breed = $row["breed"] ; 
	$answer= $row["answer"] ; 
	$arr = array($breed,$answer,$ques,$ch1,$ch2,$ch3,$ch4,$ch5,$ch6); 
	$data[] = implode(",", $arr);
}
$ma .= implode("\n", $data);
echo $ma;
exit;
}

}

echo  "<font size=5 face=標楷體 color=#800000><b>$s_title</b> 題庫匯出</font>";	
?>

<table border="0" width="90%" cellspacing="0" cellpadding="0" >
<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" name="pform">
<tr>
<td>
<a href=test_edit.php?unit=<?=$unit ?>>回上一頁</a>　<input type=submit name="do_key" value="CSV 輸出">
</td>
</tr>
<tr>
<td>
本功能只限匯出題庫文字部份，如需有圖片或語音，請於匯入後，另以修改的方式上傳。
</td>
</tr>
<input type='hidden' name='unit' value=<?=$unit ?>>	
</form>
</table>





