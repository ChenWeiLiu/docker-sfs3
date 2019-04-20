<?php
                                                                                                                             
// $Id: esystem.php 8741 2016-01-07 06:30:22Z qfon $

/***********************
 系統相關設定檢測
 1.作業帳號與學生學籍帳號同步
 2.刪除舊資料 
 
 本程式權限為系統管理者 
 在 系統管理 > 學務程式設定 > 授權管理本程式
 ***********************/

// --系統設定檔
include "exam_config.php";

//判別是否為系統管理者
$man_flag = checkid($_SERVER[SCRIPT_FILENAME],1) ;

if (!$man_flag) {	
	$str = "你未被授權使用本功能，參考系統說明檔" ;
	redir("exam.php",3) ;
	exit;
}

if(!checkid(substr($_SERVER[PHP_SELF],1))){
	$go_back=1; //回到自已的認證畫面  
	include "header.php";
	include "$rlogin";
	include "footer.php"; 
	exit;
}

$syncBtn= "座號同步";
$syncpassBtn= "密碼同步";
include "header.php";
include "menu.php";

echo "<h2>系統管理</h2>";
//目前學年
if ( $curr_year =="")
	$curr_year = sprintf("%03s",curr_year());

//按鍵處理  
switch ($_POST[key]) {
	case  $syncBtn : //座號同步化	
	$query = "select class_id,e_kind_id from exam_kind where class_id like '$curr_year%'  group by class_id order by class_id ";
	$result = $CONN->Execute($query);
	$addnum = 0;
	$chgnum = 0;
	while (!$result->EOF) {
		$class_temp = substr($result->fields[class_id],-3);
		$query2 = "select stud_id,stud_name,curr_class_num,email_pass from stud_base where stud_study_cond=0 and curr_class_num like '$class_temp%' order by curr_class_num ";
		$result2 = $CONN->Execute($query2);
		
		while(!$result2->EOF) {
			$stud_num = intval(substr($result2->fields[curr_class_num],-2));
			$result3 = $CONN->Execute("select stud_id,stud_num from exam_stud_data where stud_id ='".$result2->fields[stud_id]."'");
			if ($result3->RecordCount()>0) {
				if($stud_num != $result3->fields[stud_num]) {
					$query3  = "update  exam_stud_data set stud_num='$stud_num' where stud_id = '".$result2->fields[stud_id]."'";
					$CONN->Execute($query3) or die($query3);					
					$query3  = "update  exam_stud set stud_num='$stud_num' where stud_id = '".$result2->fields[stud_id]."'";
					$CONN->Execute($query3) or die($query3);
					$chgnum++;					
				
				}
			}
			else {
//mysqli
$mysqliconn = get_mysqli_conn();	
$query3  = "insert into exam_stud_data (stud_id,stud_num,stud_pass) values('".$result2->fields[stud_id]."','$stud_num',?)";
$stmt = "";
$stmt = $mysqliconn->prepare($query3);
$stmt->bind_param('s', $default_pass);
$stmt->execute();
$stmt->close();
///mysqli	
				
				/*
				$query3  = "insert into exam_stud_data (stud_id,stud_num,stud_pass) values('".$result2->fields[stud_id]."','$stud_num','$default_pass')";
				$CONN->Execute($query3) or die($query3);
				*/
				
				
				$addnum++;
			}
			$result2->MoveNext();
		}
		$result->MoveNext();
	}
	echo "<h2><font color=red>本次作業共異動了 $chgnum 筆，新增了 $addnum 筆 資料</font></h2><br>";
	break;
	
} //end switch

?>

<table border=1 width=600>
<form name=myform action="<?php echo $_SERVER[PHP_SELF] ?>" method=post>
  <tbody>
    <tr>
      <td bgColor="#80ffff">選項</td>      
      <td  bgColor="#80ffff">動作</td>      
    </tr>
    <tr>
      <td>
      <u>學生作業座號</u> 與 <u>學籍座號同步</u><br>(依更改筆數多寡，可能需20秒鐘以上)
      </td>     
      <td><input name=key type=submit value="<?php echo $syncBtn ?>"></td>
     </tr>
     
     <tr>
      <td colspan=2 bgColor="#80ffff">說明：作業管理學生資料表(exam_stud_data) 與學生學籍資料表(stud_base) 不同，在新生轉入或重新編班後，請執行本項操作，以同步化兩個資料表</td>
    </tr>
</tbody>
</form>
</table>
<?php include "footer.php";
?>
